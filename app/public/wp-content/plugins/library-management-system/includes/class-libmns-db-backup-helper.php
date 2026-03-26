<?php
/**
 * DB backup helper – generates JSON backup (and legacy SQL) using PHP.
 *
 * @link       https://onlinewebtutorblog.com
 * @since      3.5
 * @package    Library_Management_System
 * @subpackage Library_Management_System/includes
 * @copyright  Copyright (c) 2026, Online Web Tutor
 * @license    GPL-2.0+ https://www.gnu.org/licenses/gpl-2.0.html
 * @author     Online Web Tutor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/** Columns to exclude from export (auto-generated on import) */
define( 'LIBMNS_BACKUP_EXCLUDE_COLUMNS', 'id,created_at,updated_at' );

/**
 * Generates a data-only backup JSON file. Excludes id, created_at, updated_at from each row.
 *
 * @param wpdb   $wpdb             WordPress database instance.
 * @param array  $exports          Map of module_key => table_name (e.g. 'users' => 'wp_xxx_users').
 * @param string $bkp_file_path    Full path where the .json file should be written.
 * @param string $plugin_version   Plugin version string (e.g. LIBMNS_VERSION).
 * @param int    $row_limit        Optional max rows to export per module. 0 = unlimited.
 * @return bool True on success, false on failure.
 */
function libmns_generate_json_backup_free( $wpdb, $exports, $bkp_file_path, $plugin_version = '', $row_limit = 0 ) {
	if ( empty( $exports ) || ! is_array( $exports ) ) {
		return false;
	}
	$exclude_cols = array( 'id', 'created_at', 'updated_at' );
	$timestamp    = (int) gmdate( 'YmdHis' );
	if ( $plugin_version === '' && defined( 'LIBMNS_VERSION' ) ) {
		$plugin_version = LIBMNS_VERSION;
	}

	$data = array();
	foreach ( $exports as $module_key => $table_name ) {
		$table_safe = str_replace( '`', '', trim( $table_name ) );
		if ( empty( $table_safe ) ) {
			$data[ $module_key ] = array();
			continue;
		}
		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_safe ) ) !== $table_safe ) {
			$data[ $module_key ] = array();
			continue;
		}
		if ( $row_limit > 0 ) {
			$query = $wpdb->prepare( "SELECT * FROM `{$table_safe}` LIMIT %d", $row_limit );
		} else {
			$query = "SELECT * FROM `{$table_safe}`";
		}
		$rows   = $wpdb->get_results( $query, ARRAY_A );
		$result = array();
		foreach ( $rows as $row ) {
			$clean = array_diff_key( $row, array_flip( $exclude_cols ) );
			$result[] = $clean;
		}
		$data[ $module_key ] = $result;
	}

	$payload = array(
		'plugin'    => 'Library Management System',
		'version'   => $plugin_version,
		'timestamp' => $timestamp,
		'data'      => $data,
	);
	$json = wp_json_encode( $payload, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
	if ( $json === false ) {
		return false;
	}
	return file_put_contents( $bkp_file_path, $json ) !== false;
}

/**
 * Restores database from a JSON backup file. Inserts rows without id/created_at/updated_at (DB auto-generates).
 *
 * @param wpdb   $wpdb             WordPress database instance.
 * @param string $json_file_path   Full path to the .json backup file.
 * @param array  $module_to_table   Map of module_key => table_name (must match backup data keys).
 * @param int    $row_limit         Optional max rows to restore per module. 0 = unlimited.
 * @return bool True on success, false on failure.
 */
function libmns_restore_db_from_json_backup_free( $wpdb, $json_file_path, $module_to_table, $row_limit = 0 ) {
	if ( ! file_exists( $json_file_path ) || ! is_readable( $json_file_path ) ) {
		return false;
	}
	$raw   = file_get_contents( $json_file_path );
	$payload = json_decode( $raw, true );
	if ( ! is_array( $payload ) || ! isset( $payload['data'] ) || ! is_array( $payload['data'] ) ) {
		return false;
	}
	$exclude_cols = array( 'id', 'created_at', 'updated_at' );

	foreach ( $payload['data'] as $module_key => $rows ) {
		if ( ! isset( $module_to_table[ $module_key ] ) || ! is_array( $rows ) ) {
			continue;
		}
		$table_safe = str_replace( '`', '', trim( $module_to_table[ $module_key ] ) );
		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_safe ) ) !== $table_safe ) {
			continue;
		}
		$wpdb->query( "TRUNCATE TABLE `{$table_safe}`" );
		$db_columns = $wpdb->get_col( "SHOW COLUMNS FROM `{$table_safe}`" );
		if ( ! is_array( $db_columns ) ) {
			continue;
		}
		$allowed = array_diff( $db_columns, $exclude_cols );
		$allowed = array_flip( $allowed );

		if ( $row_limit > 0 ) {
			$rows = array_slice( $rows, 0, $row_limit );
		}

		foreach ( $rows as $row ) {
			if ( ! is_array( $row ) ) {
				continue;
			}
			$insert = array_intersect_key( $row, $allowed );
			if ( empty( $insert ) ) {
				continue;
			}
			$wpdb->insert( $table_safe, $insert );
		}
	}
	return true;
}

/**
 * Validates an LMS JSON backup file: plugin name, structure, and module keys must be allowed.
 *
 * @param string $json_content     Raw JSON string.
 * @param array  $module_to_table   Map of module_key => table_name (allowed modules).
 * @param wpdb   $wpdb             WordPress database instance (optional, for column checks).
 * @return array [ true ] on success, or [ false, 'error message' ] on failure.
 */
function libmns_validate_json_backup_file_free( $json_content, $module_to_table, $wpdb = null ) {
	$payload = json_decode( $json_content, true );
	if ( ! is_array( $payload ) ) {
		return array( false, __( 'Invalid JSON in backup file.', 'library-management-system' ) );
	}
	if ( empty( $payload['plugin'] ) || strpos( $payload['plugin'], 'Library Management System' ) === false ) {
		return array( false, __( 'Not a valid LMS backup file.', 'library-management-system' ) );
	}
	if ( ! isset( $payload['data'] ) || ! is_array( $payload['data'] ) ) {
		return array( false, __( 'Backup file has no data section.', 'library-management-system' ) );
	}
	$allowed_keys = array_keys( $module_to_table );
	foreach ( array_keys( $payload['data'] ) as $key ) {
		if ( ! in_array( $key, $allowed_keys, true ) ) {
			return array( false, __( 'Invalid module in backup file.', 'library-management-system' ) );
		}
	}
	return array( true );
}
