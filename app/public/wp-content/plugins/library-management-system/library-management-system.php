<?php
/**
 * @link       https://onlinewebtutorblog.com
 * @since      3.5
 * @package    Library_Management_System
 * @subpackage Library_Management_System
 * @copyright  Copyright (c) 2026, Online Web Tutor
 * @license    GPL-2.0+ https://www.gnu.org/licenses/gpl-2.0.html
 * @author     Online Web Tutor
 * @wordpress-plugin
 * Plugin Name:       Library Management System
 * Plugin URI:        https://onlinewebtutorblog.com/library-management-system-wordpress-plugin/
 * Description:       Transform your WordPress site into a smart and efficient digital library with an easy-to-use system for managing books, borrowers, and transactions, while also giving you flexible settings and smooth import tools to keep your library organized and running effortlessly for free.
 * Version:           3.5.5
 * Author:            Online Web Tutor
 * Author URI:        https://onlinewebtutorblog.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       library-management-system
 * Domain Path:       /languages
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

require_once plugin_dir_path( __FILE__ ) . 'includes/libmns_constants.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/class-libmns-table-helper.php';

$libmns_composer_autoload = plugin_dir_path( __FILE__ ) . 'vendor/autoload.php';
if ( file_exists( $libmns_composer_autoload ) ) {
	require_once $libmns_composer_autoload;
}

function libmns_free_run_install( $set_redirect = false ) {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-libmns-activator.php';
	$table_activator = new LIBMNS_Activator_FREE();
	$table_activator->activate();
	update_option( 'owt7_library_plugin_slug', LIBMNS_PLUGIN_SLUG );
	if ( $set_redirect ) {
		set_transient( 'libmns_free_activation_redirect', 1, MINUTE_IN_SECONDS );
	}
}

function owt7_activate_library_management_system() {
	libmns_free_run_install( true );
}

function owt7_deactivate_library_management_system() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-libmns-activator.php';
	$table_activator = new LIBMNS_Activator_FREE();
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-libmns-deactivator.php';
	$table_deactivator = new LIBMNS_Deactivator_FREE( $table_activator );
	$table_deactivator->deactivate();
}

register_activation_hook( __FILE__, 'owt7_activate_library_management_system' );
register_deactivation_hook( __FILE__, 'owt7_deactivate_library_management_system' );

function libmns_free_has_missing_tables() {
	global $wpdb;
	foreach ( LIBMNS_Table_Helper_FREE::get_all_table_names() as $table_name ) {
		if ( empty( $table_name ) ) { continue; }
		if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) ) !== $table_name ) {
			return true;
		}
	}
	return false;
}

function libmns_free_maybe_run_upgrade_install() {
	static $has_run = false;
	if ( $has_run || ! function_exists( 'get_option' ) ) {
		return;
	}
	$has_run        = true;
	$stored_version = (string) get_option( 'owt7_library_version', '' );
	$needs_upgrade  = '' === $stored_version || version_compare( $stored_version, LIBMNS_VERSION, '<' );
	if ( ! $needs_upgrade && ! libmns_free_has_missing_tables() ) {
		return;
	}
	libmns_free_run_install();
}

add_action( 'plugins_loaded', 'libmns_free_maybe_run_upgrade_install', 5 );

function libmns_free_redirect_after_activation() {
	if ( ! is_admin() || wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
		return;
	}
	if ( ( defined( 'WP_CLI' ) && WP_CLI ) || is_network_admin() || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	if ( ! get_transient( 'libmns_free_activation_redirect' ) ) {
		return;
	}
	delete_transient( 'libmns_free_activation_redirect' );
	if ( isset( $_GET['activate-multi'] ) ) {
		return;
	}
	wp_safe_redirect( admin_url( 'admin.php?page=library_management_system' ) );
	exit;
}

add_action( 'admin_init', 'libmns_free_redirect_after_activation' );

require plugin_dir_path( __FILE__ ) . 'includes/class-libmns.php';

// Plugin Bootstrap
function run_library_management_system() {
	$plugin = new LIBMNS_FREE();
	$plugin->run();
}

run_library_management_system();
