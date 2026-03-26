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

if ( ! function_exists( 'libmns_generate_json_backup_free' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'class-libmns-db-backup-helper.php';
}

class LIBMNS_Deactivator_FREE {

	private $table_activator;

    public function __construct($activator)
    {
        $this->table_activator = $activator;
    }
	
	public function deactivate() {

		global $wpdb;

		if ( defined( 'LIBMNS_LICENSE_VALIDATE_FILE' ) && file_exists( LIBMNS_LICENSE_VALIDATE_FILE ) ) {
			@unlink( LIBMNS_LICENSE_VALIDATE_FILE );
		}
		if ( defined( 'LIBMNS_LICENSE_UPLOADS_DIR' ) && is_dir( LIBMNS_LICENSE_UPLOADS_DIR ) ) {
			$files = @scandir( LIBMNS_LICENSE_UPLOADS_DIR );
			if ( is_array( $files ) && count( $files ) <= 2 ) {
				@rmdir( LIBMNS_LICENSE_UPLOADS_DIR );
			}
		}
		delete_option( 'owt7_library_verified' );
		delete_option( 'owt7_library_validate_key' );
		delete_option( 'owt7_lms_books_store' );
		delete_option( 'owt7_lms_public_view_store_settings' );
		delete_option( 'owt7_lms_sync_wp_roles' );
		delete_option( 'owt7_lms_required_fields_settings' );
		delete_option( 'owt7_lms_library_user_portal_settings' );
		delete_option( 'owt7_lms_fine_damaged_book' );
		delete_option( 'owt7_lms_fine_missing_pages' );

        // Remove Options
        delete_option( 'owt7_library_version');
        delete_option( 'libmns_system' );
		delete_option( 'libmns_db_tables' );
        delete_option( 'libmns_late_fine_currency' );
        delete_option( 'libmns_country' );
        delete_option( 'libmns_currency' ); 
        delete_option( 'libmns_test_data' );
        delete_option( 'libmns_test_data_map' );
        delete_option( 'libmns_public_settings' );
        delete_option( 'libmns_books_store' );
		delete_option( 'libmns_theme_primary' );
		delete_option( 'libmns_theme_accent' );
		delete_option( 'libmns_theme_action_clone' );
		delete_option( 'libmns_theme_action_view' );
		delete_option( 'libmns_theme_action_edit' );
		delete_option( 'libmns_theme_action_book_copies' );
		delete_option( 'libmns_theme_action_delete' );
		delete_option( 'libmns_theme_action_checkout' );
		delete_option( 'libmns_theme_action_view_book' );
		delete_option( 'libmns_theme_action_return' );
		remove_role( 'owt7_librarian' );
		remove_role( 'owt7_circulation_staff' );
		remove_role( 'owt7_library_user' );
		$roles = array( 'administrator' );
		foreach ( $roles as $role_name ) {
			$role = get_role( $role_name );
			if ( $role ) {
				$role->remove_cap( 'manage_owt7_library_system' );
				$role->remove_cap( 'view_library_menu' );
			}
		}

        $module_to_table = LIBMNS_Table_Helper_FREE::get_backup_module_to_table();

        $version_no = date( 'YmdHis' );
        $upload_dir = wp_upload_dir();
        $backup_folder = $upload_dir['basedir'] . '/library-management-system_uploads/db-backup';
        $bkp_file_path = $backup_folder . '/lms-backup-' . $version_no . '.json';
        $plugin_version = defined( 'LIBMNS_VERSION' ) ? LIBMNS_VERSION : '';

        if (!is_dir($backup_folder)) {
            mkdir($backup_folder, 0777, true);
        }

        libmns_generate_json_backup_free( $wpdb, $module_to_table, $bkp_file_path, $plugin_version );

        // Flush rewrite rules so library friendly URLs are removed
        flush_rewrite_rules();

        // Delete plugin-created pages
		$page_slugs = ['wp-library-books'];

        foreach ($page_slugs as $slug) {
            $page_data = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT ID FROM " . $wpdb->prefix . "posts WHERE post_name = %s AND post_type = 'page' LIMIT 1",
                    $slug
                )
            );
            if (!empty($page_data)) {
                wp_delete_post($page_data->ID, true);
            }
        }
	}
}