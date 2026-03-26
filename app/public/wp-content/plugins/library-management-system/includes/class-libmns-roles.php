<?php
/**
 * LMS Roles and Permissions helper.
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

class LIBMNS_Roles_Helper_FREE {

	public static function libmns_current_user_can( $cap ) {
		if ( current_user_can( 'manage_options' ) ) {
			return true;
		}
		if ( 'access_owt7_library_user_portal' === $cap ) {
			return current_user_can( 'access_owt7_library_user_portal' );
		}
		return current_user_can( 'manage_owt7_library_system' );
	}

	public static function libmns_menu_required_caps() {
		return array(
			'library_management_system'   => array( 'owt7_lms_view_dashboard' ),
			'owt7_library_users'          => array( 'owt7_lms_add_branch', 'owt7_lms_edit_branch', 'owt7_lms_delete_branch', 'owt7_lms_list_branches', 'owt7_lms_add_user', 'owt7_lms_edit_user', 'owt7_lms_delete_user', 'owt7_lms_list_users' ),
			'owt7_library_bookcases'      => array( 'owt7_lms_add_bookcase', 'owt7_lms_edit_bookcase', 'owt7_lms_delete_bookcase', 'owt7_lms_list_bookcases', 'owt7_lms_add_section', 'owt7_lms_edit_section', 'owt7_lms_delete_section', 'owt7_lms_list_sections' ),
			'owt7_library_books'          => array( 'owt7_lms_add_category', 'owt7_lms_edit_category', 'owt7_lms_delete_category', 'owt7_lms_list_categories', 'owt7_lms_add_book', 'owt7_lms_edit_book', 'owt7_lms_delete_book', 'owt7_lms_list_books' ),
			'owt7_library_transactions'   => array( 'owt7_lms_borrow_book', 'owt7_lms_return_book', 'owt7_lms_view_borrow_list', 'owt7_lms_view_return_list' ),
			'owt7_library_settings'       => array( 'owt7_lms_view_settings', 'owt7_lms_manage_days', 'owt7_lms_manage_late_fine', 'owt7_lms_manage_country', 'owt7_lms_manage_upload', 'owt7_lms_manage_backup', 'owt7_lms_run_test_data_importer' ),
			'owt7_library_about'          => array( 'owt7_lms_view_about' ),
			'owt7_library_books_catalogue' => array( 'access_owt7_library_user_portal' ),
			'owt7_library_user_borrowed' => array( 'access_owt7_library_user_portal' ),
			'owt7_library_user_returned' => array( 'access_owt7_library_user_portal' ),
		);
	}

	public static function libmns_get_library_user_menu_slugs() {
		return array( 'owt7_library_books_catalogue', 'owt7_library_user_borrowed', 'owt7_library_user_returned' );
	}

	public static function libmns_get_permissions_structure() {
		return array(
			__( 'Dashboard', 'library-management-system' ) => array(
				'owt7_lms_view_dashboard' => __( 'View Dashboard', 'library-management-system' ),
				'owt7_lms_list_books'     => __( 'Manage Books (Dashboard Card)', 'library-management-system' ),
				'owt7_lms_list_users'     => __( 'Manage Users (Dashboard Card)', 'library-management-system' ),
				'owt7_lms_list_bookcases' => __( 'Manage Bookcase (Dashboard Card)', 'library-management-system' ),
				'owt7_lms_view_reports'   => __( 'Transaction Reports (Dashboard Card)', 'library-management-system' ),
				'owt7_lms_view_settings'  => __( 'Settings (Dashboard Card)', 'library-management-system' ),
				'owt7_lms_manage_backup'  => __( 'Plugin Backup (Dashboard Card)', 'library-management-system' ),
			),
			__( 'Users', 'library-management-system' ) => array(
				'owt7_lms_add_branch'     => __( 'Add Branch (Button)', 'library-management-system' ),
				'owt7_lms_edit_branch'    => __( 'Edit Branch (Table Action)', 'library-management-system' ),
				'owt7_lms_delete_branch'  => __( 'Delete Branch (Table Action)', 'library-management-system' ),
				'owt7_lms_list_branches'  => __( 'List Branches', 'library-management-system' ),
				'owt7_lms_add_user'       => __( 'Add User (Button)', 'library-management-system' ),
				'owt7_lms_edit_user'      => __( 'Edit User (Table Action)', 'library-management-system' ),
				'owt7_lms_delete_user'    => __( 'Delete User (Table Action)', 'library-management-system' ),
				'owt7_lms_list_users'     => __( 'List Users', 'library-management-system' ),
			),
			__( 'Bookcase & Section', 'library-management-system' ) => array(
				'owt7_lms_add_bookcase'    => __( 'Add Bookcase (Button)', 'library-management-system' ),
				'owt7_lms_edit_bookcase'   => __( 'Edit Bookcase (Table Action)', 'library-management-system' ),
				'owt7_lms_delete_bookcase' => __( 'Delete Bookcase (Table Action)', 'library-management-system' ),
				'owt7_lms_list_bookcases'  => __( 'List Bookcases', 'library-management-system' ),
				'owt7_lms_add_section'     => __( 'Add Section (Button)', 'library-management-system' ),
				'owt7_lms_edit_section'    => __( 'Edit Section (Table Action)', 'library-management-system' ),
				'owt7_lms_delete_section'  => __( 'Delete Section (Table Action)', 'library-management-system' ),
				'owt7_lms_list_sections'   => __( 'List Sections', 'library-management-system' ),
			),
			__( 'Books', 'library-management-system' ) => array(
				'owt7_lms_add_category'    => __( 'Add Category (Button)', 'library-management-system' ),
				'owt7_lms_edit_category'   => __( 'Edit Category (Table Action)', 'library-management-system' ),
				'owt7_lms_delete_category' => __( 'Delete Category (Table Action)', 'library-management-system' ),
				'owt7_lms_list_categories' => __( 'List Category (Button)', 'library-management-system' ),
				'owt7_lms_add_book'        => __( 'Add Book (Button)', 'library-management-system' ),
				'owt7_lms_edit_book'       => __( 'Edit Book (Table Action)', 'library-management-system' ),
				'owt7_lms_delete_book'     => __( 'Delete Book (Table Action)', 'library-management-system' ),
				'owt7_lms_list_books'      => __( 'List Books', 'library-management-system' ),
			),
			__( 'Book Transactions', 'library-management-system' ) => array(
				'owt7_lms_borrow_book'       => __( 'Borrow Book (Button)', 'library-management-system' ),
				'owt7_lms_return_book'      => __( 'Return Book (Button)', 'library-management-system' ),
				'owt7_lms_view_borrow_list' => __( 'View Borrow List', 'library-management-system' ),
				'owt7_lms_view_return_list' => __( 'View Return List', 'library-management-system' ),
			),
			__( 'Reports', 'library-management-system' ) => array(
				'owt7_lms_view_reports' => __( 'View Reports', 'library-management-system' ),
			),
			__( 'Settings', 'library-management-system' ) => array(
				'owt7_lms_view_settings'      => __( 'View Settings', 'library-management-system' ),
				'owt7_lms_manage_days'         => __( 'Manage Days', 'library-management-system' ),
				'owt7_lms_manage_late_fine'    => __( 'Manage Late Fine', 'library-management-system' ),
				'owt7_lms_manage_country'      => __( 'Manage Country & Currency', 'library-management-system' ),
				'owt7_lms_manage_upload'       => __( 'Manage Upload CSV', 'library-management-system' ),
				'owt7_lms_manage_backup'       => __( 'Manage Backup', 'library-management-system' ),
				'owt7_lms_run_test_data_importer' => __( 'Run Test Data Importer (Button)', 'library-management-system' ),
			),
			__( 'About LMS', 'library-management-system' ) => array(
				'owt7_lms_view_about' => __( 'View About', 'library-management-system' ),
			),
		);
	}

	public static function libmns_get_lms_roles_with_permissions() {
		return array();
	}

	public static function libmns_get_assignable_lms_roles() {
		return array(
			'owt7_library_user'       => __( 'Library User (LMS)', 'library-management-system' ),
		);
	}

	public static function libmns_get_restricted_lms_roles() {
		return array();
	}

	public static function libmns_get_allowed_caps_by_role() {
		return array(
			'owt7_library_user'      => array(), // Portal-only; no granular caps.
			'subscriber'             => array(),
		);
	}

	public static function libmns_get_default_caps_for_role( $role_slug ) {
		if ( $role_slug === 'owt7_library_user' || $role_slug === 'subscriber' ) {
			return array();
		}
		return array();
	}

	public static function libmns_user_has_library_user_role( $user = null ) {
		if ( $user === null ) {
			$user = wp_get_current_user();
		}
		return $user && ! empty( $user->roles ) && in_array( 'owt7_library_user', (array) $user->roles, true );
	}
}
