<?php
/**
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

class LIBMNS_Table_Helper_FREE {

	private static $module_to_suffix = array(
		'branches'         => 'owt7_libmns_branch',
		'users'            => 'owt7_libmns_users',
		'bookcases'        => 'owt7_libmns_bookcase',
		'sections'         => 'owt7_libmns_bookcase_sections',
		'categories'      => 'owt7_libmns_category',
		'books'            => 'owt7_libmns_books',
		'issue_days'       => 'owt7_libmns_days_settings',
		'book_borrow'      => 'owt7_libmns_book_borrow',
		'book_return'       => 'owt7_libmns_return_book',
		'book_late_fine'    => 'owt7_libmns_late_fine',
		'data_backups'     => 'owt7_libmns_sql_backups',
		'data_books_copies' => 'owt7_libmns_books_copies',
		'custom_labels'    => 'owt7_libmns_custom_labels',
	);

	public static function get_table_name( $module ) {
		global $wpdb;
		$module = is_string( $module ) ? trim( $module ) : '';
		if ( isset( self::$module_to_suffix[ $module ] ) ) {
			return $wpdb->prefix . self::$module_to_suffix[ $module ];
		}
		return '';
	}

	public static function get_module_to_table() {
		$out = array();
		foreach ( array_keys( self::$module_to_suffix ) as $module ) {
			$out[ $module ] = self::get_table_name( $module );
		}
		return $out;
	}

	public static function get_backup_module_to_table() {
		$all = self::get_module_to_table();
		$exclude = array( 'data_backups', 'data_books_copies' );
		return array_diff_key( $all, array_flip( $exclude ) );
	}

	public static function get_all_table_names() {
		return array_values( self::get_module_to_table() );
	}
}
