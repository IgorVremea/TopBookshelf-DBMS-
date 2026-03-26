<?php
/**
 * AJAX request helper for Library Management System.
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

/**
 * Class LIBMNS_Ajax_Helper_FREE
 */
class LIBMNS_Ajax_Helper_FREE {

	/**
	 * @var LIBMNS_Admin_FREE
	 */
	private $admin;

	/**
	 * @param LIBMNS_Admin_FREE $admin Admin class instance (for callbacks and table_activator).
	 */
	public function __construct( LIBMNS_Admin_FREE $admin ) {
		$this->admin = $admin;
	}

	/**
	 * Main AJAX handler: verifies nonce/license, dispatches by param to handle_* methods.
	 */
	public function libmns_ajax_handler() {
		global $wpdb;
		$param = isset( $_REQUEST['param'] ) ? sanitize_text_field( wp_unslash( trim( (string) $_REQUEST['param'] ) ) ) : '';
		if ( ! isset( $_REQUEST['owt7_lms_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( (string) ( $_REQUEST['owt7_lms_nonce'] ?? '' ) ) ), 'owt7_library_actions' ) ) {
			$response = array( 0, __( 'LMS actions blocked due to security reasons', 'library-management-system' ) );
		} else {
			if ( ! empty( $param ) ) {
				$response = $this->dispatch_param( $param );
			} else {
				$response = array( 0, __( 'Invalid LMS operation', 'library-management-system' ) );
			}
		}
		wp_send_json( $this->admin->libmns_json( $response ) );
		wp_die();
	}

	/**
	 * Dispatch param to the corresponding handle_* method.
	 *
	 * @param string $param Action param from request.
	 * @return array Response tuple [ status, message, optional_data ].
	 */
	private function dispatch_param( $param ) {
		$map = array(
			'owt7_lms_branch_form'                    => 'handle_branch_form',
			'owt7_lms_user_form'                      => 'handle_user_form',
			'owt7_lms_delete_function'                => 'handle_delete_function',
			'owt7_lms_bookcase_form'                  => 'handle_bookcase_form',
			'owt7_lms_section_form'                   => 'handle_section_form',
			'owt7_lms_category_form'                  => 'handle_category_form',
			'owt7_lms_book_form'                      => 'handle_book_form',
			'owt7_lms_filter_section'                 => 'handle_filter_section',
			'owt7_lms_filter_user'                    => 'handle_filter_user',
			'owt7_lms_filter_book'                    => 'handle_filter_book',
			'owt7_lms_borrow_book'                    => 'handle_borrow_book',
			'owt7_lms_filter_borrow_book'             => 'handle_filter_borrow_book',
			'owt7_lms_book_copy_info'                 => 'handle_book_copy_info',
			'owt7_lms_return_book'                    => 'handle_return_book',
			'owt7_lms_return_fine_preview'             => 'handle_return_fine_preview',
			'owt7_lms_data_filters'                   => 'handle_data_filters',
			'owt7_lms_get_days_list'                  => 'handle_get_days_list',
			'owt7_lms_data_settings'                  => 'handle_data_settings',
			'owt7_lms_data_option_filters'            => 'handle_data_option_filters',
			'owt7_lms_import_test_data'               => 'handle_import_test_data',
			'owt7_lms_remove_test_data'               => 'handle_remove_test_data',
			'owt7_pay_late_fine'                       => 'handle_pay_late_fine',
			'owt7_lms_get_return_view_content'         => 'handle_get_return_view_content',
			'owt7_lms_get_quick_return_modal_content'  => 'handle_get_quick_return_modal_content',
			'owt7_lms_download_sample_data'            => 'handle_download_sample_data',
			'owt7_lms_upload_form'                    => 'handle_upload_form',
			'owt7_lms_settings_form'                  => 'handle_settings_form',
			'owt7_lms_checkout_approve_reject'         => 'handle_checkout_approve_reject',
			'owt7_lms_save_public_view_settings'      => 'handle_save_public_view_settings',
			'owt7_lms_get_db_tables_health'           => 'handle_get_db_tables_health',
			'owt7_lms_save_library_user_portal_settings' => 'handle_save_library_user_portal_settings',
			'owt7_lms_filter_library_user_catalogue'  => 'handle_filter_library_user_catalogue',
			'owt7_lms_get_book_copies'                => 'handle_get_book_copies',
			'owt7_lms_get_receipt_data'               => 'handle_get_receipt_data',
			'owt7_lms_save_receipt'                   => 'handle_save_receipt',
		);
		if ( isset( $map[ $param ] ) && method_exists( $this, $map[ $param ] ) ) {
			return $this->{$map[ $param ]}();
		}
		return array( 0, __( 'Invalid LMS Operation', 'library-management-system' ) );
	}

	private function libmns_normalize_test_data_int_ids( $ids ) {
		$normalized = array();
		if ( ! is_array( $ids ) ) {
			return $normalized;
		}
		foreach ( $ids as $id ) {
			$id = absint( $id );
			if ( $id > 0 ) {
				$normalized[ $id ] = $id;
			}
		}
		return array_values( $normalized );
	}

	private function libmns_normalize_test_data_strings( $values ) {
		$normalized = array();
		if ( ! is_array( $values ) ) {
			return $normalized;
		}
		foreach ( $values as $value ) {
			$value = is_string( $value ) ? trim( $value ) : '';
			if ( $value !== '' ) {
				$normalized[ $value ] = $value;
			}
		}
		return array_values( $normalized );
	}

	private function libmns_get_test_data_map() {
		$map = get_option( 'owt7_lms_test_data_map', array() );
		if ( ! is_array( $map ) ) {
			$map = array();
		}
		return array(
			'category_ids' => $this->libmns_normalize_test_data_int_ids( isset( $map['category_ids'] ) ? $map['category_ids'] : array() ),
			'bookcase_ids' => $this->libmns_normalize_test_data_int_ids( isset( $map['bookcase_ids'] ) ? $map['bookcase_ids'] : array() ),
			'section_ids'  => $this->libmns_normalize_test_data_int_ids( isset( $map['section_ids'] ) ? $map['section_ids'] : array() ),
			'branch_ids'   => $this->libmns_normalize_test_data_int_ids( isset( $map['branch_ids'] ) ? $map['branch_ids'] : array() ),
			'book_ids'     => $this->libmns_normalize_test_data_int_ids( isset( $map['book_ids'] ) ? $map['book_ids'] : array() ),
			'book_codes'   => $this->libmns_normalize_test_data_strings( isset( $map['book_codes'] ) ? $map['book_codes'] : array() ),
			'user_ids'     => $this->libmns_normalize_test_data_int_ids( isset( $map['user_ids'] ) ? $map['user_ids'] : array() ),
		);
	}

	private function libmns_has_test_data_map( $map = null ) {
		if ( ! is_array( $map ) ) {
			$map = $this->libmns_get_test_data_map();
		}
		foreach ( $map as $values ) {
			if ( ! empty( $values ) ) {
				return true;
			}
		}
		return false;
	}

	private function libmns_save_test_data_map( $map ) {
		$normalized = array(
			'category_ids' => $this->libmns_normalize_test_data_int_ids( isset( $map['category_ids'] ) ? $map['category_ids'] : array() ),
			'bookcase_ids' => $this->libmns_normalize_test_data_int_ids( isset( $map['bookcase_ids'] ) ? $map['bookcase_ids'] : array() ),
			'section_ids'  => $this->libmns_normalize_test_data_int_ids( isset( $map['section_ids'] ) ? $map['section_ids'] : array() ),
			'branch_ids'   => $this->libmns_normalize_test_data_int_ids( isset( $map['branch_ids'] ) ? $map['branch_ids'] : array() ),
			'book_ids'     => $this->libmns_normalize_test_data_int_ids( isset( $map['book_ids'] ) ? $map['book_ids'] : array() ),
			'book_codes'   => $this->libmns_normalize_test_data_strings( isset( $map['book_codes'] ) ? $map['book_codes'] : array() ),
			'user_ids'     => $this->libmns_normalize_test_data_int_ids( isset( $map['user_ids'] ) ? $map['user_ids'] : array() ),
		);
		if ( $this->libmns_has_test_data_map( $normalized ) ) {
			update_option( 'owt7_lms_test_data_map', $normalized );
			update_option( 'owt7_lms_test_data', 1 );
			return;
		}
		$this->libmns_clear_test_data_tracking();
	}

	private function libmns_clear_test_data_tracking() {
		delete_option( 'owt7_lms_test_data' );
		delete_option( 'owt7_lms_test_data_map' );
	}

	private function libmns_delete_rows_by_ids( $table, $ids ) {
		global $wpdb;
		$ids = $this->libmns_normalize_test_data_int_ids( $ids );
		if ( empty( $ids ) ) {
			return 0;
		}
		$placeholders = implode( ', ', array_fill( 0, count( $ids ), '%d' ) );
		$sql          = $wpdb->prepare( "DELETE FROM `{$table}` WHERE id IN ({$placeholders})", $ids );
		$deleted      = $wpdb->query( $sql );
		return ( false === $deleted ) ? 0 : (int) $deleted;
	}

	private function libmns_delete_rows_by_column_values( $table, $column, $values ) {
		global $wpdb;
		$values = $this->libmns_normalize_test_data_strings( $values );
		$column = preg_replace( '/[^a-zA-Z0-9_]/', '', (string) $column );
		if ( empty( $values ) || '' === $column ) {
			return 0;
		}
		$placeholders = implode( ', ', array_fill( 0, count( $values ), '%s' ) );
		$sql          = $wpdb->prepare( "DELETE FROM `{$table}` WHERE `{$column}` IN ({$placeholders})", $values );
		$deleted      = $wpdb->query( $sql );
		return ( false === $deleted ) ? 0 : (int) $deleted;
	}

	private function libmns_get_equivalent_branch_ids( $branch_id ) {
		global $wpdb;
		$branch_id = absint( $branch_id );
		if ( $branch_id <= 0 ) {
			return array();
		}
		$tbl_branches = LIBMNS_Table_Helper_FREE::get_table_name( 'branches' );
		$branch_name  = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT name FROM `{$tbl_branches}` WHERE id = %d LIMIT 1",
				$branch_id
			)
		);
		if ( null === $branch_name ) {
			return array( $branch_id );
		}
		$normalized_name = trim( (string) $branch_name );
		$ids = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT id FROM `{$tbl_branches}` WHERE status = %d AND LOWER(TRIM(name)) = LOWER(TRIM(%s)) ORDER BY id ASC",
				1,
				$normalized_name
			)
		);
		$ids = array_map( 'absint', (array) $ids );
		$ids = array_values( array_filter( array_unique( $ids ) ) );
		return ! empty( $ids ) ? $ids : array( $branch_id );
	}

	private function libmns_get_canonical_branch_id( $branch_id ) {
		$ids = $this->libmns_get_equivalent_branch_ids( $branch_id );
		return ! empty( $ids ) ? (int) min( $ids ) : absint( $branch_id );
	}

	private function libmns_remove_tracked_test_data( $allow_legacy_fallback = false ) {
		global $wpdb;
		$map = $this->libmns_get_test_data_map();
		if ( $this->libmns_has_test_data_map( $map ) ) {
			if ( ! empty( $map['book_ids'] ) ) {
				$placeholders = implode( ', ', array_fill( 0, count( $map['book_ids'] ), '%d' ) );
				$sql          = $wpdb->prepare(
					"DELETE FROM `" . LIBMNS_Table_Helper_FREE::get_table_name( 'book_late_fine' ) . "` WHERE book_id IN ({$placeholders})",
					$map['book_ids']
				);
				$wpdb->query( $sql );
				$sql = $wpdb->prepare(
					"DELETE FROM `" . LIBMNS_Table_Helper_FREE::get_table_name( 'book_return' ) . "` WHERE book_id IN ({$placeholders})",
					$map['book_ids']
				);
				$wpdb->query( $sql );
				$sql = $wpdb->prepare(
					"DELETE FROM `" . LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' ) . "` WHERE book_id IN ({$placeholders})",
					$map['book_ids']
				);
				$wpdb->query( $sql );
			}
			if ( ! empty( $map['user_ids'] ) ) {
				$placeholders = implode( ', ', array_fill( 0, count( $map['user_ids'] ), '%d' ) );
				$sql          = $wpdb->prepare(
					"DELETE FROM `" . LIBMNS_Table_Helper_FREE::get_table_name( 'book_late_fine' ) . "` WHERE u_id IN ({$placeholders})",
					$map['user_ids']
				);
				$wpdb->query( $sql );
				$sql = $wpdb->prepare(
					"DELETE FROM `" . LIBMNS_Table_Helper_FREE::get_table_name( 'book_return' ) . "` WHERE u_id IN ({$placeholders})",
					$map['user_ids']
				);
				$wpdb->query( $sql );
				$sql = $wpdb->prepare(
					"DELETE FROM `" . LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' ) . "` WHERE u_id IN ({$placeholders})",
					$map['user_ids']
				);
				$wpdb->query( $sql );
			}
			$this->libmns_delete_rows_by_column_values( LIBMNS_Table_Helper_FREE::get_table_name( 'data_books_copies' ), 'book_id', $map['book_codes'] );
			$this->libmns_delete_rows_by_ids( LIBMNS_Table_Helper_FREE::get_table_name( 'books' ), $map['book_ids'] );
			$this->libmns_delete_rows_by_ids( LIBMNS_Table_Helper_FREE::get_table_name( 'users' ), $map['user_ids'] );
			$this->libmns_delete_rows_by_ids( LIBMNS_Table_Helper_FREE::get_table_name( 'sections' ), $map['section_ids'] );
			$this->libmns_delete_rows_by_ids( LIBMNS_Table_Helper_FREE::get_table_name( 'bookcases' ), $map['bookcase_ids'] );
			$this->libmns_delete_rows_by_ids( LIBMNS_Table_Helper_FREE::get_table_name( 'branches' ), $map['branch_ids'] );
			$this->libmns_delete_rows_by_ids( LIBMNS_Table_Helper_FREE::get_table_name( 'categories' ), $map['category_ids'] );
			$this->libmns_clear_test_data_tracking();
			return true;
		}
		if ( $allow_legacy_fallback && get_option( 'owt7_lms_test_data' ) ) {
			$truncate_table_names = array(
				LIBMNS_Table_Helper_FREE::get_table_name( 'categories' ),
				LIBMNS_Table_Helper_FREE::get_table_name( 'books' ),
				LIBMNS_Table_Helper_FREE::get_table_name( 'bookcases' ),
				LIBMNS_Table_Helper_FREE::get_table_name( 'sections' ),
				LIBMNS_Table_Helper_FREE::get_table_name( 'branches' ),
				LIBMNS_Table_Helper_FREE::get_table_name( 'users' ),
				LIBMNS_Table_Helper_FREE::get_table_name( 'data_books_copies' )
			);
			foreach ( $truncate_table_names as $table ) {
				$wpdb->query( "TRUNCATE TABLE `{$table}`" );
			}
			$this->libmns_clear_test_data_tracking();
			return true;
		}
		$this->libmns_clear_test_data_tracking();
		return false;
	}

	private function handle_branch_form() {
		global $wpdb;
		$response = array( 0, __( 'Invalid Operation', 'library-management-system' ) );
		$action_type = isset( $_REQUEST['action_type'] ) ? sanitize_text_field( trim( $_REQUEST['action_type'] ) ) : '';
		$cap = ( $action_type === 'edit' ) ? 'owt7_lms_edit_branch' : 'owt7_lms_add_branch';
		if ( ! LIBMNS_Roles_Helper_FREE::libmns_current_user_can( $cap ) ) {
			return array( 0, __( 'You do not have permission to perform this action.', 'library-management-system' ) );
		}
		$branch  = isset( $_REQUEST['owt7_txt_branch_name'] ) ? LIBMNS_Sanitize_FREE::multilingual_text( $_REQUEST['owt7_txt_branch_name'] ) : '';
		$status  = isset( $_REQUEST['owt7_dd_branch_status'] ) ? absint( $_REQUEST['owt7_dd_branch_status'] ) : 1;
		$edit_id = isset( $_REQUEST['edit_id'] ) ? sanitize_text_field( wp_unslash( trim( (string) $_REQUEST['edit_id'] ) ) ) : '';
		if ( empty( $action_type ) ) {
			return array( 0, __( 'Invalid Operation', 'library-management-system' ) );
		}
		if ( $action_type === 'add' ) {
			$valid = LIBMNS_Admin_FREE::libmns_validate_required_fields( 'branch' );
			if ( ! $valid[0] ) {
				return array( 0, $valid[1] );
			}
			$free_limit = defined( 'LIBMNS_FREE_VERSION_LIMIT' ) ? (int) LIBMNS_FREE_VERSION_LIMIT : 30;
			$branch_count = (int) $wpdb->get_var( 'SELECT COUNT(*) FROM ' . LIBMNS_Table_Helper_FREE::get_table_name( 'branches' ) );
			if ( $branch_count >= $free_limit ) {
				return array( 0, sprintf( __( 'Free version limit reached (maximum %d items). Please upgrade to add more branches.', 'library-management-system' ), $free_limit ) );
			}
			if ( empty( $branch ) ) {
				return array( 0, __( 'Branch value required', 'library-management-system' ) );
			}
			$is_branch_exists = $wpdb->get_row( $wpdb->prepare( 'SELECT * from ' . LIBMNS_Table_Helper_FREE::get_table_name( 'branches' ) . ' WHERE LOWER(TRIM(name)) = %s', strtolower( trim( $branch ) ) ) );
			if ( ! empty( $is_branch_exists ) ) {
				return array( 0, __( 'Branch name already taken', 'library-management-system' ) );
			}
			$wpdb->insert( LIBMNS_Table_Helper_FREE::get_table_name( 'branches' ), array( 'name' => $branch, 'status' => $status ) );
			if ( $wpdb->insert_id > 0 ) {
				return array( 1, __( 'Successfully, Branch added to LMS', 'library-management-system' ) );
			}
			return array( 0, __( 'Failed to add Branch', 'library-management-system' ) );
		}
		if ( $action_type === 'edit' ) {
			$valid_edit = LIBMNS_Admin_FREE::libmns_validate_required_fields( 'branch' );
			if ( ! $valid_edit[0] ) {
				return array( 0, $valid_edit[1] );
			}
			if ( empty( $branch ) ) {
				return array( 0, __( 'Branch value required', 'library-management-system' ) );
			}
			$branch_have_same_id = $wpdb->get_row( $wpdb->prepare( 'SELECT * from ' . LIBMNS_Table_Helper_FREE::get_table_name( 'branches' ) . ' WHERE LOWER(TRIM(name)) = %s AND id <> %s', strtolower( trim( $branch ) ), $edit_id ) );
			if ( ! empty( $branch_have_same_id ) ) {
				return array( 0, __( 'Branch name already taken', 'library-management-system' ) );
			}
			$is_branch_exists = $wpdb->get_row( $wpdb->prepare( 'SELECT * from ' . LIBMNS_Table_Helper_FREE::get_table_name( 'branches' ) . ' WHERE id = %s', $edit_id ) );
			if ( empty( $is_branch_exists ) ) {
				return array( 0, __( 'Branch not found', 'library-management-system' ) );
			}
			$wpdb->update( LIBMNS_Table_Helper_FREE::get_table_name( 'branches' ), array( 'name' => $branch, 'status' => $status ), array( 'id' => $edit_id ) );
			return array( 1, __( 'Successfully, Branch data updated', 'library-management-system' ) );
		}
		return $response;
	}

	private function handle_user_form() {
		global $wpdb;
		$response = array( 0, __( 'Invalid LMS operation', 'library-management-system' ) );
		$action_type = isset( $_REQUEST['action_type'] ) ? sanitize_text_field( trim( $_REQUEST['action_type'] ) ) : "" ;
		$cap = ( $action_type === 'edit' ) ? 'owt7_lms_edit_user' : 'owt7_lms_add_user';
		if ( ! LIBMNS_Roles_Helper_FREE::libmns_current_user_can( $cap ) ) {
			$response = [ 0, __( 'You do not have permission to perform this action.', 'library-management-system' ) ];
		} else {
			$userId = isset( $_REQUEST['owt7_txt_u_id'] ) ? LIBMNS_Sanitize_FREE::multilingual_text( $_REQUEST['owt7_txt_u_id'] ) : "" ;
			$branchId = isset( $_REQUEST['owt7_dd_branch_id'] ) ? absint( $_REQUEST['owt7_dd_branch_id'] ) : 1 ;
			$name = isset( $_REQUEST['owt7_txt_name'] ) ? LIBMNS_Sanitize_FREE::multilingual_text( $_REQUEST['owt7_txt_name'] ) : "" ;
			$email = isset( $_REQUEST['owt7_txt_email'] ) ? sanitize_email( wp_unslash( $_REQUEST['owt7_txt_email'] ) ) : "" ;
			$phone = isset( $_REQUEST['owt7_txt_phone'] ) ? LIBMNS_Sanitize_FREE::multilingual_text( $_REQUEST['owt7_txt_phone'] ) : "" ;
			$gender = isset( $_REQUEST['owt7_dd_gender'] ) ? sanitize_text_field( trim( $_REQUEST['owt7_dd_gender'] ) ) : "" ;
			$address = isset( $_REQUEST['owt7_txt_address'] ) ? LIBMNS_Sanitize_FREE::multilingual_textarea( $_REQUEST['owt7_txt_address'] ) : "" ;
			$profileImage = isset( $_REQUEST['owt7_profile_image'] ) ? LIBMNS_Sanitize_FREE::multilingual_text( $_REQUEST['owt7_profile_image'] ) : "" ;
			$status = isset( $_REQUEST['owt7_dd_user_status'] ) ? absint( $_REQUEST['owt7_dd_user_status'] ) : 1 ;
			$edit_id = isset( $_REQUEST['edit_id'] ) ? sanitize_text_field( trim( $_REQUEST['edit_id'] ) ) : "" ;

			if(!empty($action_type)){ // [add, edit, view]

				if($action_type == "add"){
					$valid = LIBMNS_Admin_FREE::libmns_validate_required_fields( 'user' );
					if ( ! $valid[0] ) {
						$response = array( 0, $valid[1] );
					} else {
						$free_limit = defined( 'LIBMNS_FREE_VERSION_LIMIT' ) ? (int) LIBMNS_FREE_VERSION_LIMIT : 30;
						$user_count = (int) $wpdb->get_var( 'SELECT COUNT(*) FROM ' . LIBMNS_Table_Helper_FREE::get_table_name( 'users' ) );
						if ( $user_count >= $free_limit ) {
							$response = array( 0, sprintf( __( 'Free version limit reached (maximum %d items). Please upgrade to add more users/borrowers.', 'library-management-system' ), $free_limit ) );
						} elseif(!empty($userId) || !empty($branchId) || !empty($name)){

						$is_user_exists = $wpdb->get_row(
							$wpdb->prepare(
								"SELECT * from " . LIBMNS_Table_Helper_FREE::get_table_name( 'users' ) . " WHERE LOWER(TRIM(u_id)) = %s",
								strtolower(trim($userId))
							)
						);

						if ( !empty( $is_user_exists ) ) {

							$response = [
								0, 
								__("User already exists", "library-management-system")
							];
						} else {
							$save_as_wp_user = ! empty( $_REQUEST['owt7_save_as_wp_user'] );
							$wp_user_id_value = 0;
							$wp_user_flag = 0;
							$add_user_error = false;

							if ( $save_as_wp_user ) {
								$wp_username = isset( $_REQUEST['owt7_wp_username'] ) ? sanitize_user( trim( wp_unslash( (string) $_REQUEST['owt7_wp_username'] ) ), true ) : '';
								$wp_password = isset( $_REQUEST['owt7_wp_password'] ) ? wp_unslash( (string) $_REQUEST['owt7_wp_password'] ) : '';
								$wp_email = ! empty( $email ) ? $email : '';
								if ( empty( $wp_username ) ) {
									$response = [ 0, __( 'WordPress username is required when creating as WordPress user.', 'library-management-system' ) ];
									$add_user_error = true;
								} elseif ( strlen( $wp_password ) < 6 ) {
									$response = [ 0, __( 'WordPress password must be at least 6 characters.', 'library-management-system' ) ];
									$add_user_error = true;
								} elseif ( username_exists( $wp_username ) ) {
									$response = [ 0, __( 'This WordPress username is already in use.', 'library-management-system' ) ];
									$add_user_error = true;
								} else {
									$new_wp_user_id = wp_create_user( $wp_username, $wp_password, $wp_email );
									if ( is_wp_error( $new_wp_user_id ) ) {
										$response = [ 0, $new_wp_user_id->get_error_message() ];
										$add_user_error = true;
									} else {
										$wp_user_id_value = (int) $new_wp_user_id;
										$wp_user_flag = 1;
										if ( ! empty( $name ) ) {
											$name_parts = explode( ' ', trim( $name ), 2 );
											update_user_meta( $new_wp_user_id, 'first_name', $name_parts[0] );
											if ( ! empty( $name_parts[1] ) ) {
												update_user_meta( $new_wp_user_id, 'last_name', $name_parts[1] );
											}
										}
										$lms_role = isset( $_REQUEST['owt7_lms_wp_user_role'] ) ? sanitize_text_field( trim( $_REQUEST['owt7_lms_wp_user_role'] ) ) : '';
										$allowed_lms_roles = array_keys( LIBMNS_Roles_Helper_FREE::libmns_get_assignable_lms_roles() );
										if ( in_array( $lms_role, $allowed_lms_roles, true ) ) {
											$wp_user_obj = get_userdata( $new_wp_user_id );
											if ( $wp_user_obj ) {
												$wp_user_obj->set_role( $lms_role );
											}
										} else {
											$wp_user_obj = get_userdata( $new_wp_user_id );
											if ( $wp_user_obj ) {
												$wp_user_obj->set_role( 'owt7_library_user' );
											}
										}
									}
								}
							}

							if ( ! $add_user_error ) {
								$insert_data = array(
									"register_from" => "admin",
									"wp_user" => $wp_user_flag,
									"wp_user_id" => $wp_user_id_value,
									"u_id" => $userId,
									"name" => $name,
									"email" => $email,
									"gender" => $gender,
									"branch_id" => $branchId,
									"phone_no" => $phone,
									"profile_image" => $profileImage,
									"address_info" => $address,
									"status" => $status
								);
								$wpdb->insert( LIBMNS_Table_Helper_FREE::get_table_name( 'users' ), $insert_data );

								if ( $wpdb->insert_id > 0 ) {
									$response = [
										1,
										__( "Successfully, User added to LMS", "library-management-system" )
									];
								} else {
									$response = [
										0,
										__( "Failed to add User", "library-management-system" )
									];
								}
							}
							}
							} else {
								$response = [
									0,
									__( 'Required fields are missing', 'library-management-system' )
								];
							}
					}
				} elseif($action_type == "edit"){

					if(!empty($userId) || !empty($branchId) || !empty($name)){

						$user_have_same_id = $wpdb->get_row(
							$wpdb->prepare(
								"SELECT * from " . LIBMNS_Table_Helper_FREE::get_table_name( 'users' ) . " WHERE LOWER(TRIM(u_id)) = %s AND id <> %s",
								strtolower(trim($userId)), $edit_id
							)
						);

						if ( !empty( $user_have_same_id ) ) {

							$response = [
								0, 
								__("User ID already taken", "library-management-system")
							];
						} else{

							$is_user_exists = $wpdb->get_row(
								$wpdb->prepare(
									"SELECT * from " . LIBMNS_Table_Helper_FREE::get_table_name( 'users' ) . " WHERE id = %s",
									$edit_id
								)
							);

							if(!empty($is_user_exists)){

								$update_data = array(
									"u_id" => $userId,
									"name" => $name,
									"email" => $email,
									"gender" => $gender,
									"branch_id" => $branchId,
									"phone_no" => $phone,
									"profile_image" => $profileImage,
									"address_info" => $address,
									"status" => $status
								);

								$edit_user_error = false;
								$wp_user_id_edit = isset( $is_user_exists->wp_user_id ) ? absint( $is_user_exists->wp_user_id ) : 0;
								$edit_link_wp_user = ! empty( $_REQUEST['owt7_save_as_wp_user'] );

								if ( $wp_user_id_edit > 0 ) {
									$wp_user_update = array( 'ID' => $wp_user_id_edit );
									if ( ! empty( $email ) ) {
										$existing_email_user_id = email_exists( $email );
										if ( $existing_email_user_id && (int) $existing_email_user_id !== $wp_user_id_edit ) {
											$response = [ 0, __( 'This email address is already used by another WordPress user.', 'library-management-system' ) ];
											$edit_user_error = true;
										} else {
											$wp_user_update['user_email'] = $email;
										}
									}

									if ( ! $edit_user_error && ! empty( $name ) ) {
										$wp_user_update['display_name'] = $name;
										$name_parts = explode( ' ', trim( $name ), 2 );
										$wp_user_update['first_name'] = $name_parts[0];
										if ( ! empty( $name_parts[1] ) ) {
											$wp_user_update['last_name'] = $name_parts[1];
										}
									}

									if ( ! $edit_user_error && count( $wp_user_update ) > 1 ) {
										$wp_user_update_result = wp_update_user( $wp_user_update );
										if ( is_wp_error( $wp_user_update_result ) ) {
											$response = [ 0, $wp_user_update_result->get_error_message() ];
											$edit_user_error = true;
										}
									}

									$new_password = isset( $_REQUEST['owt7_wp_new_password'] ) ? wp_unslash( (string) $_REQUEST['owt7_wp_new_password'] ) : '';
									if ( ! $edit_user_error && is_string( $new_password ) && $new_password !== '' && strlen( $new_password ) < 6 ) {
										$response = [ 0, __( 'WordPress password must be at least 6 characters.', 'library-management-system' ) ];
										$edit_user_error = true;
									} elseif ( ! $edit_user_error && is_string( $new_password ) && strlen( $new_password ) >= 6 ) {
										wp_set_password( $new_password, $wp_user_id_edit );
									}
								} elseif ( $edit_link_wp_user ) {
									$wp_username = isset( $_REQUEST['owt7_wp_username'] ) ? sanitize_user( trim( wp_unslash( (string) $_REQUEST['owt7_wp_username'] ) ), true ) : '';
									$wp_password = isset( $_REQUEST['owt7_wp_password'] ) ? wp_unslash( (string) $_REQUEST['owt7_wp_password'] ) : '';
									$wp_email = ! empty( $email ) ? $email : '';
									if ( empty( $wp_username ) ) {
										$response = [ 0, __( 'WordPress username is required when creating as WordPress user.', 'library-management-system' ) ];
										$edit_user_error = true;
									} elseif ( strlen( $wp_password ) < 6 ) {
										$response = [ 0, __( 'WordPress password must be at least 6 characters.', 'library-management-system' ) ];
										$edit_user_error = true;
									} elseif ( username_exists( $wp_username ) ) {
										$response = [ 0, __( 'This WordPress username is already in use.', 'library-management-system' ) ];
										$edit_user_error = true;
									} elseif ( ! empty( $wp_email ) && email_exists( $wp_email ) ) {
										$response = [ 0, __( 'This email address is already used by another WordPress user.', 'library-management-system' ) ];
										$edit_user_error = true;
									} else {
										$new_wp_user_id = wp_create_user( $wp_username, $wp_password, $wp_email );
										if ( is_wp_error( $new_wp_user_id ) ) {
											$response = [ 0, $new_wp_user_id->get_error_message() ];
											$edit_user_error = true;
										} else {
											$new_wp_user_id = (int) $new_wp_user_id;
											if ( ! empty( $name ) ) {
												$name_parts = explode( ' ', trim( $name ), 2 );
												update_user_meta( $new_wp_user_id, 'first_name', $name_parts[0] );
												if ( ! empty( $name_parts[1] ) ) {
													update_user_meta( $new_wp_user_id, 'last_name', $name_parts[1] );
												}
											}
											$lms_role = isset( $_REQUEST['owt7_lms_wp_user_role'] ) ? sanitize_text_field( trim( $_REQUEST['owt7_lms_wp_user_role'] ) ) : '';
											$allowed_lms_roles = array_keys( LIBMNS_Roles_Helper_FREE::libmns_get_assignable_lms_roles() );
											if ( in_array( $lms_role, $allowed_lms_roles, true ) ) {
												$wp_user_obj = get_userdata( $new_wp_user_id );
												if ( $wp_user_obj ) {
													$wp_user_obj->set_role( $lms_role );
												}
											} else {
												$wp_user_obj = get_userdata( $new_wp_user_id );
												if ( $wp_user_obj ) {
													$wp_user_obj->set_role( 'subscriber' );
												}
											}
											$update_data['wp_user']    = 1;
											$update_data['wp_user_id'] = $new_wp_user_id;
										}
									}
								}

								if ( ! $edit_user_error ) {
									$wpdb->update( LIBMNS_Table_Helper_FREE::get_table_name( 'users' ), $update_data, [ "id" => $edit_id ] );
									$response = [
										1,
										__( "Successfully, User data updated", "library-management-system" )
									];
								}
							}else{

								$response = [
									0, 
									__("User not found", "library-management-system")
								];
							}
						}
					}else{

						$response = [
							0, 
							__("Required fields are missing", "library-management-system")
						];
					}
				}
			}else{

				$response = [
					0, 
					__("Invalid Operation", "library-management-system")
				];
			}
		}
		return $response;
	}

	private function handle_delete_function() {
		global $wpdb;
		$response = array( 0, __( 'Invalid LMS operation', 'library-management-system' ) );
		$raw_id = isset( $_REQUEST['id'] ) ? sanitize_text_field( wp_unslash( (string) $_REQUEST['id'] ) ) : '';
		$deleteId = $raw_id !== '' ? sanitize_text_field( (string) base64_decode( $raw_id, true ) ) : '';
		$raw_module = isset( $_REQUEST['module'] ) ? sanitize_text_field( wp_unslash( (string) $_REQUEST['module'] ) ) : '';
		$deleteModule = $raw_module !== '' ? sanitize_text_field( (string) base64_decode( $raw_module, true ) ) : ''; 
					$delete_caps = [ 'user' => 'owt7_lms_delete_user', 'branch' => 'owt7_lms_delete_branch', 'bookcase' => 'owt7_lms_delete_bookcase', 'section' => 'owt7_lms_delete_section', 'category' => 'owt7_lms_delete_category', 'book' => 'owt7_lms_delete_book', 'book_borrow' => 'owt7_lms_return_book', 'book_return' => 'owt7_lms_return_book', 'days' => 'owt7_lms_manage_days', 'backup' => 'owt7_lms_manage_backup' ];
					$req_cap = isset( $delete_caps[ $deleteModule ] ) ? $delete_caps[ $deleteModule ] : null;
					if ( $req_cap !== null && ! LIBMNS_Roles_Helper_FREE::libmns_current_user_can( $req_cap ) ) {
						$response = [ 0, __( 'You do not have permission to perform this action.', 'library-management-system' ) ];
					} else {
					// [user, branch, bookcase, section, category, book, borrow, return]
					// Table name
					$tableName = "";
					$deleteStatus = false;
					$associatedModules = [
						"branch" => "user",
						"bookcase" => "section",
						"category" => "book"
					];
					
					if($deleteModule == "user"){
						$tableName = LIBMNS_Table_Helper_FREE::get_table_name( 'users' );
					} else					if($deleteModule == "branch"){
						$tableName = LIBMNS_Table_Helper_FREE::get_table_name( 'branches' );
						// Check to delete
						$has_data = $wpdb->get_results( $wpdb->prepare( "SELECT count(*) as total_rows FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'users' ) . " WHERE branch_id = %s", $deleteId ) );
						if(!empty($has_data)){
							$deleteStatus = true;
						}
					} elseif($deleteModule == "bookcase"){
						$tableName = LIBMNS_Table_Helper_FREE::get_table_name( 'bookcases' );
						// Check to delete
						$has_data = $wpdb->get_results( $wpdb->prepare( "SELECT count(*) as total_rows FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'sections' ) . " WHERE bookcase_id = %s", $deleteId ) );
						if(!empty($has_data)){
							$deleteStatus = true;
						}
					} elseif($deleteModule == "section"){
						$tableName = LIBMNS_Table_Helper_FREE::get_table_name( 'sections' );
					} elseif($deleteModule == "category"){
						$tableName = LIBMNS_Table_Helper_FREE::get_table_name( 'categories' );
						// Check to delete
						$has_data = $wpdb->get_results( $wpdb->prepare( "SELECT count(*) as total_rows FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'books' ) . " WHERE category_id = %s", $deleteId ) );
						if(!empty($has_data)){
							$deleteStatus = true;
						}
					} elseif($deleteModule == "book"){
						$tableName = LIBMNS_Table_Helper_FREE::get_table_name( 'books' );
						// Block delete if book is currently checked out.
						$book_row = $wpdb->get_row( $wpdb->prepare( "SELECT id FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'books' ) . " WHERE id = %s", $deleteId ) );
						if ( ! empty( $book_row ) ) {
							$active_borrow_count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' ) . " WHERE book_id = %s AND status = 1", $deleteId ) );
							if ( $active_borrow_count > 0 ) {
								$deleteStatus = true;
								$book_delete_block_msg = __( 'Cannot delete: Book is currently checked out to a user.', 'library-management-system' );
							}
						}
					} elseif($deleteModule == "book_borrow"){
						$tableName = LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' );
						$deleteModule = str_replace("_", " ", $deleteModule);
					} elseif($deleteModule == "book_return"){
						$tableName = LIBMNS_Table_Helper_FREE::get_table_name( 'book_return' );
						$deleteModule = str_replace("_", " ", $deleteModule);
					} elseif($deleteModule == "days"){
						$tableName = LIBMNS_Table_Helper_FREE::get_table_name( 'issue_days' );
					} elseif($deleteModule == "backup"){
						$tableName = LIBMNS_Table_Helper_FREE::get_table_name( 'data_backups' );
					}

					if(!empty($tableName)){

						if(!$deleteStatus){

							$is_data_exists = $wpdb->get_row(
								$wpdb->prepare(
									"SELECT * from " . $tableName . " WHERE id = %s",
									$deleteId
								)
							);
		
							if(!empty($is_data_exists)){

								// Delete Backup From Path
								if($deleteModule == "backup"){
									$upload_dir = wp_upload_dir();
									$backup_folder = $upload_dir['basedir'] . '/library-management-system_uploads/db-backup';
									$backup_path = $backup_folder . '/' . $is_data_exists->file_name;
									if (file_exists($backup_path)) {
										unlink($backup_path);
									}
								}

								$wpdb->delete($tableName, [
									"id" => $is_data_exists->id
								]);
		
								$response = [
									1, 
									__("Successfully, " . ucfirst($deleteModule) . " deleted", "library-management-system")
								];
							}else{
		
								$response = [
									0, 
									__(ucfirst($deleteModule) . " not found", "library-management-system")
								];
							}
						}else{
							$msg = isset( $book_delete_block_msg ) ? $book_delete_block_msg : __("Failed to delete. There are number of rows of " . ucfirst($associatedModules[$deleteModule]) . " associated with this " . ucfirst($deleteModule) . ". Please delete them first.", "library-management-system");
							$response = [
								0, 
								$msg
							];
						}
					}else{

						$response = [
							0, 
							__("Data Module not found", "library-management-system")
						];
					}
					}
		return $response;
	}


	private function handle_bookcase_form() {
		global $wpdb;
		$response = array( 0, __( 'Invalid LMS operation', 'library-management-system' ) );
		$action_type = isset( $_REQUEST['action_type'] ) ? sanitize_text_field( trim( $_REQUEST['action_type'] ) ) : "" ;
					$cap = ( $action_type === 'edit' ) ? 'owt7_lms_edit_bookcase' : 'owt7_lms_add_bookcase';
					if ( ! LIBMNS_Roles_Helper_FREE::libmns_current_user_can( $cap ) ) {
						$response = [ 0, __( 'You do not have permission to perform this action.', 'library-management-system' ) ];
					} else {
					// Action Type
					// Form Data (multilingual-safe for names)
					$bookcase = isset( $_REQUEST['owt7_txt_bookcase_name'] ) ? LIBMNS_Sanitize_FREE::multilingual_text( $_REQUEST['owt7_txt_bookcase_name'] ) : "";
					$status = isset( $_REQUEST['owt7_dd_bookcase_status'] ) ? absint( $_REQUEST['owt7_dd_bookcase_status'] ) : 1;
					$edit_id = isset( $_REQUEST['edit_id'] ) ? sanitize_text_field( trim( $_REQUEST['edit_id'] ) ) : "" ;
	
					if(!empty($action_type)){ // [add, edit]

						if($action_type == "add"){
							$valid = LIBMNS_Admin_FREE::libmns_validate_required_fields( 'bookcase' );
							if ( ! $valid[0] ) {
								$response = array( 0, $valid[1] );
							} else {
								$free_limit = defined( 'LIBMNS_FREE_VERSION_LIMIT' ) ? (int) LIBMNS_FREE_VERSION_LIMIT : 30;
								$bookcase_count = (int) $wpdb->get_var( 'SELECT COUNT(*) FROM ' . LIBMNS_Table_Helper_FREE::get_table_name( 'bookcases' ) );
								if ( $bookcase_count >= $free_limit ) {
									$response = array( 0, sprintf( __( 'Free version limit reached (maximum %d items). Please upgrade to add more bookcases.', 'library-management-system' ), $free_limit ) );
								} elseif ( !empty( $bookcase ) ) {

								$is_bookcase_exists = $wpdb->get_row(
									$wpdb->prepare(
										"SELECT * from " . LIBMNS_Table_Helper_FREE::get_table_name( 'bookcases' ) . " WHERE LOWER(TRIM(name)) = %s",
										strtolower(trim($bookcase))
									)
								);
			
								if ( !empty( $is_bookcase_exists ) ) {
			
									$response = [
										0, 
										__("Bookcase name already taken", "library-management-system")
									];
								} else {
			
									$wpdb->insert(LIBMNS_Table_Helper_FREE::get_table_name( 'bookcases' ), array(
										"name" => $bookcase,
										"status" => $status
									));
			
									if ( $wpdb->insert_id > 0 ) {
			
										$response = [
											1, 
											__("Successfully, Bookcase added to LMS", "library-management-system")
										];
									} else {
										$response = [
											0, 
											__("Failed to add Bookcase", "library-management-system")
										];
									}
								}
							} else {
									$response = [
										0, 
										__( 'Bookcase value required', 'library-management-system' )
									];
								}
							}
						} elseif ($action_type == "edit"){
							$valid_edit = LIBMNS_Admin_FREE::libmns_validate_required_fields( 'bookcase' );
							if ( ! $valid_edit[0] ) {
								$response = array( 0, $valid_edit[1] );
							} elseif ( !empty( $bookcase ) ) {
	
								$bookcase_have_same_id = $wpdb->get_row(
									$wpdb->prepare(
										"SELECT * from " . LIBMNS_Table_Helper_FREE::get_table_name( 'bookcases' ) . " WHERE LOWER(TRIM(name)) = %s AND id <> %s",
										strtolower(trim($bookcase)), $edit_id
									)
								);
			
								if ( !empty( $bookcase_have_same_id ) ) {
			
									$response = [
										0, 
										__("Bookcase name already taken", "library-management-system")
									];
								}else{

									$is_bookcase_exists = $wpdb->get_row(
										$wpdb->prepare(
											"SELECT * from " . LIBMNS_Table_Helper_FREE::get_table_name( 'bookcases' ) . " WHERE id = %s",
											$edit_id
										)
									);

									if(!empty($is_bookcase_exists)){

										$wpdb->update(LIBMNS_Table_Helper_FREE::get_table_name( 'bookcases' ), array(
											"name" => $bookcase,
											"status" => $status
										), [
											"id" => $edit_id
										]);
				
										$response = [
											1, 
											__("Successfully, Bookcase data updated", "library-management-system")
										];
									}else{

										$response = [
											0, 
											__("Bookcase not found", "library-management-system")
										];
									}
								}
							} else {
								$response = [
									0, 
									__("Bookcase value required", "library-management-system")
								];
							}
						}

					}else{

						$response = [
							0, 
							__("Invalid Operation", "library-management-system")
						];
					}
					}
		return $response;
	}


	private function handle_section_form() {
		global $wpdb;
		$response = array( 0, __( 'Invalid LMS operation', 'library-management-system' ) );
		$action_type = isset( $_REQUEST['action_type'] ) ? sanitize_text_field( trim( $_REQUEST['action_type'] ) ) : "" ;
					$cap = ( $action_type === 'edit' ) ? 'owt7_lms_edit_section' : 'owt7_lms_add_section';
					if ( ! LIBMNS_Roles_Helper_FREE::libmns_current_user_can( $cap ) ) {
						$response = [ 0, __( 'You do not have permission to perform this action.', 'library-management-system' ) ];
					} else {
	
					// Action Type
					$action_type = isset( $_REQUEST['action_type'] ) ? sanitize_text_field( trim( $_REQUEST['action_type'] ) ) : "" ;
					// Form Data (multilingual-safe for names)
					$bookcase_id = isset( $_REQUEST['owt7_dd_bookcase_id'] ) ? sanitize_text_field( wp_unslash( (string) $_REQUEST['owt7_dd_bookcase_id'] ) ) : "";
					$section = isset( $_REQUEST['owt7_txt_section_name'] ) ? LIBMNS_Sanitize_FREE::multilingual_text( $_REQUEST['owt7_txt_section_name'] ) : "";
					$status = isset( $_REQUEST['owt7_dd_section_status'] ) ? absint( $_REQUEST['owt7_dd_section_status'] ) : 1;
					$edit_id = isset( $_REQUEST['edit_id'] ) ? sanitize_text_field( wp_unslash( trim( (string) $_REQUEST['edit_id'] ) ) ) : "" ;
	
					if(!empty($action_type)){ // [add, edit]

						if($action_type == "add"){
							$valid = LIBMNS_Admin_FREE::libmns_validate_required_fields( 'section' );
							if ( ! $valid[0] ) {
								$response = array( 0, $valid[1] );
							} else {
								$free_limit = defined( 'LIBMNS_FREE_VERSION_LIMIT' ) ? (int) LIBMNS_FREE_VERSION_LIMIT : 30;
								$section_count = (int) $wpdb->get_var( 'SELECT COUNT(*) FROM ' . LIBMNS_Table_Helper_FREE::get_table_name( 'sections' ) );
								if ( $section_count >= $free_limit ) {
									$response = array( 0, sprintf( __( 'Free version limit reached (maximum %d items). Please upgrade to add more sections.', 'library-management-system' ), $free_limit ) );
								} elseif ( !empty( $section ) ) {

								$is_section_exists = $wpdb->get_row(
									$wpdb->prepare(
										"SELECT * from " . LIBMNS_Table_Helper_FREE::get_table_name( 'sections' ) . " WHERE LOWER(TRIM(name)) = %s AND bookcase_id = %s",
										strtolower(trim($section)), $bookcase_id
									)
								);
			
								if ( !empty( $is_section_exists ) ) {
			
									$response = [
										0, 
										__("Section name already taken", "library-management-system")
									];
								} else {
			
									$wpdb->insert(LIBMNS_Table_Helper_FREE::get_table_name( 'sections' ), array(
										"name" => $section,
										"bookcase_id" => $bookcase_id,
										"status" => $status
									));
			
									if ( $wpdb->insert_id > 0 ) {
			
										$response = [
											1, 
											__("Successfully, Section added to LMS", "library-management-system")
										];
									} else {
										$response = [
											0, 
											__("Failed to add Section", "library-management-system")
										];
									}
								}
							} else {
									$response = [
										0, 
										__( 'Section value required', 'library-management-system' )
									];
								}
							}
						} elseif ($action_type == "edit"){
							$valid_edit = LIBMNS_Admin_FREE::libmns_validate_required_fields( 'section' );
							if ( ! $valid_edit[0] ) {
								$response = array( 0, $valid_edit[1] );
							} elseif ( !empty( $section ) ) {
	
								$section_have_same_id = $wpdb->get_row(
									$wpdb->prepare(
										"SELECT * from " . LIBMNS_Table_Helper_FREE::get_table_name( 'sections' ) . " WHERE LOWER(TRIM(name)) = %s AND bookcase_id = %s AND id <> %s",
										strtolower(trim($section)), $bookcase_id, $edit_id
									)
								);
			
								if ( !empty( $section_have_same_id ) ) {
			
									$response = [
										0, 
										__("Section name already taken", "library-management-system")
									];
								}else{

									$is_section_exists = $wpdb->get_row(
										$wpdb->prepare(
											"SELECT * from " . LIBMNS_Table_Helper_FREE::get_table_name( 'sections' ) . " WHERE id = %s",
											$edit_id
										)
									);

									if(!empty($is_section_exists)){

										$wpdb->update(LIBMNS_Table_Helper_FREE::get_table_name( 'sections' ), array(
											"name" => $section,
											"bookcase_id" => $bookcase_id,
											"status" => $status
										), [
											"id" => $edit_id
										]);
				
										$response = [
											1, 
											__("Successfully, Section data updated", "library-management-system")
										];
									}else{

										$response = [
											0, 
											__("Section not found", "library-management-system")
										];
									}
								}
							} else {
								$response = [
									0, 
									__("Section value required", "library-management-system")
								];
							}
						}

					}else{

						$response = [
							0, 
							__("Invalid Operation", "library-management-system")
						];
					}
					}
		return $response;
	}


	private function handle_category_form() {
		global $wpdb;
		$response = array( 0, __( 'Invalid LMS operation', 'library-management-system' ) );
		$action_type = isset( $_REQUEST['action_type'] ) ? sanitize_text_field( trim( $_REQUEST['action_type'] ) ) : "" ;
					$cap = ( $action_type === 'edit' ) ? 'owt7_lms_edit_category' : 'owt7_lms_add_category';
					if ( ! LIBMNS_Roles_Helper_FREE::libmns_current_user_can( $cap ) ) {
						$response = [ 0, __( 'You do not have permission to perform this action.', 'library-management-system' ) ];
					} else {
					// Action Type
					// Form Data (multilingual-safe for names)
					$category = isset( $_REQUEST['owt7_txt_category_name'] ) ? LIBMNS_Sanitize_FREE::multilingual_text( $_REQUEST['owt7_txt_category_name'] ) : "";
					$status = isset( $_REQUEST['owt7_dd_category_status'] ) ? absint( $_REQUEST['owt7_dd_category_status'] ) : 1;
					$edit_id = isset( $_REQUEST['edit_id'] ) ? sanitize_text_field( trim( $_REQUEST['edit_id'] ) ) : "" ;
	
					if(!empty($action_type)){ // [add, edit]

						if($action_type == "add"){
							$valid = LIBMNS_Admin_FREE::libmns_validate_required_fields( 'category' );
							if ( ! $valid[0] ) {
								$response = array( 0, $valid[1] );
							} else {
								$free_limit = defined( 'LIBMNS_FREE_VERSION_LIMIT' ) ? (int) LIBMNS_FREE_VERSION_LIMIT : 30;
								$category_count = (int) $wpdb->get_var( 'SELECT COUNT(*) FROM ' . LIBMNS_Table_Helper_FREE::get_table_name( 'categories' ) );
								if ( $category_count >= $free_limit ) {
									$response = array( 0, sprintf( __( 'Free version limit reached (maximum %d items). Please upgrade to add more categories.', 'library-management-system' ), $free_limit ) );
								} elseif ( !empty( $category ) ) {

								$is_category_exists = $wpdb->get_row(
									$wpdb->prepare(
										"SELECT * from " . LIBMNS_Table_Helper_FREE::get_table_name( 'categories' ) . " WHERE LOWER(TRIM(name)) = %s",
										strtolower(trim($category))
									)
								);
			
								if ( !empty( $is_category_exists ) ) {
			
									$response = [
										0, 
										__("Category name already taken", "library-management-system")
									];
								} else {
			
									$wpdb->insert(LIBMNS_Table_Helper_FREE::get_table_name( 'categories' ), array(
										"name" => $category,
										"status" => $status
									));
			
									if ( $wpdb->insert_id > 0 ) {
			
										$response = [
											1, 
											__("Successfully, Category added to LMS", "library-management-system")
										];
									} else {
										$response = [
											0, 
											__("Failed to add Category", "library-management-system")
										];
									}
								}
							} else {
									$response = [
										0, 
										__( 'Category value required', 'library-management-system' )
									];
								}
							}
						} elseif ($action_type == "edit"){
							$valid_edit = LIBMNS_Admin_FREE::libmns_validate_required_fields( 'category' );
							if ( ! $valid_edit[0] ) {
								$response = array( 0, $valid_edit[1] );
							} elseif ( !empty( $category ) ) {
	
								$category_have_same_id = $wpdb->get_row(
									$wpdb->prepare(
										"SELECT * from " . LIBMNS_Table_Helper_FREE::get_table_name( 'categories' ) . " WHERE LOWER(TRIM(name)) = %s AND id <> %s",
										strtolower(trim($category)), $edit_id
									)
								);
			
								if ( !empty( $category_have_same_id ) ) {
			
									$response = [
										0, 
										__("Category name already taken", "library-management-system")
									];
								}else{

									$is_category_exists = $wpdb->get_row(
										$wpdb->prepare(
											"SELECT * from " . LIBMNS_Table_Helper_FREE::get_table_name( 'categories' ) . " WHERE id = %s",
											$edit_id
										)
									);

									if(!empty($is_category_exists)){

										$wpdb->update(LIBMNS_Table_Helper_FREE::get_table_name( 'categories' ), array(
											"name" => $category,
											"status" => $status
										), [
											"id" => $edit_id
										]);
				
										$response = [
											1, 
											__("Successfully, Category data updated", "library-management-system")
										];
									}else{

										$response = [
											0, 
											__("Category not found", "library-management-system")
										];
									}
								}
							} else {
								$response = [
									0, 
									__("Category value required", "library-management-system")
								];
							}
						}

					}else{

						$response = [
							0, 
							__("Invalid Operation", "library-management-system")
						];
					}
					}
		return $response;
	}


	private function handle_book_form() {
		global $wpdb;
		$response = array( 0, __( 'Invalid LMS operation', 'library-management-system' ) );
		$action_type = isset( $_REQUEST['action_type'] ) ? sanitize_text_field( trim( $_REQUEST['action_type'] ) ) : "" ;
					$cap = ( $action_type === 'edit' ) ? 'owt7_lms_edit_book' : 'owt7_lms_add_book';
					if ( ! LIBMNS_Roles_Helper_FREE::libmns_current_user_can( $cap ) ) {
						$response = [ 0, __( 'You do not have permission to perform this action.', 'library-management-system' ) ];
					} else {
	
					// Action Type
					$action_type = isset( $_REQUEST['action_type'] ) ? sanitize_text_field( trim( $_REQUEST['action_type'] ) ) : "" ;
					// Form Data (multilingual-safe for all text fields)
					$book_id = isset( $_REQUEST['owt7_txt_book_id'] ) ? LIBMNS_Sanitize_FREE::multilingual_text( $_REQUEST['owt7_txt_book_id'] ) : "";
					$category_id = isset( $_REQUEST['owt7_dd_category_id'] ) ? sanitize_text_field( wp_unslash( (string) $_REQUEST['owt7_dd_category_id'] ) ) : "";
					$bookcase_id = isset( $_REQUEST['owt7_dd_bookcase_id'] ) ? sanitize_text_field( wp_unslash( (string) $_REQUEST['owt7_dd_bookcase_id'] ) ) : "";
					$section_id = isset( $_REQUEST['owt7_dd_section_id'] ) ? sanitize_text_field( wp_unslash( (string) $_REQUEST['owt7_dd_section_id'] ) ) : "";
					$status = isset( $_REQUEST['owt7_dd_book_status'] ) ? absint( $_REQUEST['owt7_dd_book_status'] ) : 1;
					$book_name = isset( $_REQUEST['owt7_txt_book_name'] ) ? LIBMNS_Sanitize_FREE::multilingual_text( $_REQUEST['owt7_txt_book_name'] ) : "";
					// Multiple authors/publications: comma-separated, stored as normalized "Item1, Item2, Item3"
					$author_name_raw = isset( $_REQUEST['owt7_txt_author_name'] ) ? LIBMNS_Sanitize_FREE::multilingual_text( $_REQUEST['owt7_txt_author_name'] ) : '';
					$author_name = LIBMNS_Admin_FREE::libmns_normalize_comma_separated( $author_name_raw );
					$publication_name_raw = isset( $_REQUEST['owt7_txt_publication_name'] ) ? LIBMNS_Sanitize_FREE::multilingual_text( $_REQUEST['owt7_txt_publication_name'] ) : '';
					$publication_name = LIBMNS_Admin_FREE::libmns_normalize_comma_separated( $publication_name_raw );
					$publication_year = isset( $_REQUEST['owt7_txt_publication_year'] ) ? LIBMNS_Sanitize_FREE::multilingual_text( $_REQUEST['owt7_txt_publication_year'] ) : "" ;
					$publication_location = isset( $_REQUEST['owt7_txt_publication_location'] ) ? LIBMNS_Sanitize_FREE::multilingual_text( $_REQUEST['owt7_txt_publication_location'] ) : "" ;
					$cost = isset( $_REQUEST['owt7_txt_cost'] ) ? LIBMNS_Sanitize_FREE::multilingual_text( $_REQUEST['owt7_txt_cost'] ) : "" ;
					$isbn = isset( $_REQUEST['owt7_txt_isbn'] ) ? LIBMNS_Sanitize_FREE::multilingual_text( $_REQUEST['owt7_txt_isbn'] ) : "" ;
					$book_url = isset( $_REQUEST['owt7_txt_book_url'] ) ? LIBMNS_Sanitize_FREE::multilingual_text( $_REQUEST['owt7_txt_book_url'] ) : "" ;
					$quantity = isset( $_REQUEST['owt7_txt_quantity'] ) ? sanitize_text_field( trim( $_REQUEST['owt7_txt_quantity'] ) ) : "" ;
					$book_language = isset( $_REQUEST['owt7_txt_book_language'] ) ? LIBMNS_Sanitize_FREE::multilingual_text( $_REQUEST['owt7_txt_book_language'] ) : "" ;
					$total_pages = isset( $_REQUEST['owt7_txt_total_pages'] ) ? sanitize_text_field( trim( $_REQUEST['owt7_txt_total_pages'] ) ) : "" ;
					$description = isset( $_REQUEST['owt7_txt_description'] ) ? LIBMNS_Sanitize_FREE::multilingual_textarea( $_REQUEST['owt7_txt_description'] ) : "" ;
					$cover_image = isset( $_REQUEST['owt7_cover_image'] ) ? LIBMNS_Sanitize_FREE::multilingual_text( $_REQUEST['owt7_cover_image'] ) : "" ;
					$edit_id = isset( $_REQUEST['edit_id'] ) ? sanitize_text_field( wp_unslash( trim( (string) $_REQUEST['edit_id'] ) ) ) : "";

					$is_book_woocom = 0;
					$is_woocom_stock = 0;
					$woocom_regular_price = 0;
					$woocom_sale_price = 0;
					$woocom_book_preview_pdf_link = "";
					$woocom_book_pdf_link = "";
	
					if(!empty($action_type)){ // [add, edit]

						if($action_type == "add"){
							$valid = LIBMNS_Admin_FREE::libmns_validate_required_fields( 'book' );
							if ( ! $valid[0] ) {
								$response = array( 0, $valid[1] );
							} else {
								$free_limit = defined( 'LIBMNS_FREE_VERSION_LIMIT' ) ? (int) LIBMNS_FREE_VERSION_LIMIT : 30;
								$book_count = (int) $wpdb->get_var( 'SELECT COUNT(*) FROM ' . LIBMNS_Table_Helper_FREE::get_table_name( 'books' ) );
								if ( $book_count >= $free_limit ) {
									$response = array( 0, sprintf( __( 'Free version limit reached (maximum %d items). Please upgrade to add more books.', 'library-management-system' ), $free_limit ) );
								} elseif ( !empty( $book_id ) || !empty( $category_id ) || !empty( $bookcase_id ) || !empty( $section_id ) || !empty( $book_name ) ) {

								$is_book_exists = $wpdb->get_row(
									$wpdb->prepare(
										"SELECT * from " . LIBMNS_Table_Helper_FREE::get_table_name( 'books' ) . " WHERE LOWER(TRIM(name)) = %s AND book_id = %s AND category_id = %d",
										strtolower(trim($book_name)), $book_id, $category_id
									)
								);
			
								if ( !empty( $is_book_exists ) ) {
			
									$response = [
										0, 
										__("Book name already taken", "library-management-system")
									];
								} else {
			
									$wpdb->insert(LIBMNS_Table_Helper_FREE::get_table_name( 'books' ), array(
										"book_id" => $book_id,
										"bookcase_id" => $bookcase_id,
										"bookcase_section_id" => $section_id,
										"category_id" => $category_id,
										"author_name" => $author_name,
										"name" => $book_name,
										"publication_name" => $publication_name,
										"publication_year" => $publication_year,
										"publication_location" => $publication_location,
										"amount" => $cost,
										"is_woocom_product" => $is_book_woocom,
										"is_woocom_stock" => $is_woocom_stock,
										"woocom_regular_price" => $woocom_regular_price,
										"woocom_sale_price" => $woocom_sale_price,
										"woocom_book_preview_pdf_link" => $woocom_book_preview_pdf_link,
										"woocom_book_pdf_link" => $woocom_book_pdf_link,
										"cover_image" => $cover_image,
										"isbn" => $isbn,
										"book_url" => $book_url,
										"stock_quantity" => $quantity,
										"book_language" => $book_language,
										"book_pages" => $total_pages,
										"description" => $description,
										"status" => $status
									));
			
									if ( $wpdb->insert_id > 0 ) {

										// Insert book copies (owt7_library_tbl_data_books_copies) for stock quantity
										$quantity_int = (int) $quantity;
										if ( $quantity_int > 0 ) {
											$this->admin->libmns_insert_book_copies( $book_id, $quantity_int, $bookcase_id, $section_id );
										}

										$response = [
											1, 
											__("Successfully, Book added to LMS", "library-management-system")
										];
									} else {
										$response = [
											0, 
											__("Failed to add Book", "library-management-system")
										];
									}
								}
							} else {
									$response = [
										0, 
										__( 'Please fill required values', 'library-management-system' )
									];
								}
							}
						} elseif ($action_type == "edit"){
							$valid_edit = LIBMNS_Admin_FREE::libmns_validate_required_fields( 'book' );
							if ( ! $valid_edit[0] ) {
								$response = array( 0, $valid_edit[1] );
							} elseif ( !empty( $book_id ) || !empty( $category_id ) || !empty( $bookcase_id ) || !empty( $section_id ) || !empty( $book_name ) ) {
	
								$book_have_same_data = $wpdb->get_row(
									$wpdb->prepare(
										"SELECT * from " . LIBMNS_Table_Helper_FREE::get_table_name( 'books' ) . " WHERE LOWER(TRIM(name)) = %s AND book_id = %s AND category_id = %s AND id <> %s",
										strtolower(trim($book_name)), $book_id, $category_id, $edit_id
									)
								);
			
								if ( !empty( $book_have_same_data ) ) {
			
									$response = [
										0, 
										__("Book name already taken", "library-management-system")
									];
								}else{

									$is_book_exists = $wpdb->get_row(
										$wpdb->prepare(
											"SELECT * from " . LIBMNS_Table_Helper_FREE::get_table_name( 'books' ) . " WHERE id = %s",
											$edit_id
										)
									);

									if(!empty($is_book_exists)){

										$wpdb->update(LIBMNS_Table_Helper_FREE::get_table_name( 'books' ), array(
											"book_id" => $book_id,
											"bookcase_id" => $bookcase_id,
											"bookcase_section_id" => $section_id,
											"category_id" => $category_id,
											"author_name" => $author_name,
											"name" => $book_name,
											"publication_name" => $publication_name,
											"publication_year" => $publication_year,
											"publication_location" => $publication_location,
											"amount" => $cost,
											"is_woocom_product" => $is_book_woocom,
											"is_woocom_stock" => $is_woocom_stock,
											"woocom_regular_price" => $woocom_regular_price,
											"woocom_sale_price" => $woocom_sale_price,
											"woocom_book_preview_pdf_link" => $woocom_book_preview_pdf_link,
											"woocom_book_pdf_link" => $woocom_book_pdf_link,
											"cover_image" => $cover_image,
											"isbn" => $isbn,
											"book_url" => $book_url,
											"stock_quantity" => $quantity,
											"book_language" => $book_language,
											"book_pages" => $total_pages,
											"description" => $description,
											"status" => $status
										), [
											"id" => $edit_id
										]);

										$response = [
											1, 
											__("Successfully, Book data updated", "library-management-system")
										];
									}else{

										$response = [
											0, 
											__("Book not found", "library-management-system")
										];
									}
								}
							} else {
								$response = [
									0, 
									__("Please fill required values", "library-management-system")
								];
							}
						}

					}else{

						$response = [
							0, 
							__("Invalid Operation", "library-management-system")
						];
					}
					}
		return $response;
	}

	private function handle_filter_section() {
		global $wpdb;
		$bookcase_id = isset( $_REQUEST['bkcase_id'] ) ? sanitize_text_field( wp_unslash( (string) $_REQUEST['bkcase_id'] ) ) : '';
		$sections = $bookcase_id !== '' ? $wpdb->get_results(
			$wpdb->prepare(
				'SELECT id, name from ' . LIBMNS_Table_Helper_FREE::get_table_name( 'sections' ) . ' WHERE bookcase_id = %s AND status = %d',
				$bookcase_id,
				1
			)
		) : array();
		if ( ! empty( $sections ) ) {
			return array( 1, 'Sections', array( 'sections' => $sections ) );
		}
		return array( 0, __( 'No Section Found', 'library-management-system' ) );
	}

	private function handle_filter_user() {
		global $wpdb;
		$branch_id = isset( $_REQUEST['branch_id'] ) ? sanitize_text_field( wp_unslash( (string) $_REQUEST['branch_id'] ) ) : '';
		$branch_ids = $this->libmns_get_equivalent_branch_ids( $branch_id );
		if ( empty( $branch_ids ) ) {
			return array( 0, __( 'No User Found', 'library-management-system' ) );
		}
		$placeholders = implode( ', ', array_fill( 0, count( $branch_ids ), '%d' ) );
		$params       = $branch_ids;
		$params[]     = 1;
		$users = $wpdb->get_results(
			$wpdb->prepare(
				'SELECT id, name, u_id from ' . LIBMNS_Table_Helper_FREE::get_table_name( 'users' ) . " WHERE branch_id IN ({$placeholders}) AND status = %d",
				$params
			)
		);
		if ( ! empty( $users ) ) {
			return array( 1, 'Users', array( 'users' => $users ) );
		}
		return array( 0, __( 'No User Found', 'library-management-system' ) );
	}

	private function handle_filter_book() {
		global $wpdb;
		$category_id = isset( $_REQUEST['category_id'] ) ? sanitize_text_field( wp_unslash( (string) $_REQUEST['category_id'] ) ) : '';
		$books = $category_id !== '' ? $wpdb->get_results(
			$wpdb->prepare(
				'SELECT id, name, book_id from ' . LIBMNS_Table_Helper_FREE::get_table_name( 'books' ) . ' WHERE category_id = %s AND status = %d',
				$category_id,
				1
			)
		) : array();
		if ( ! empty( $books ) ) {
			return array( 1, 'Books', array( 'books' => $books ) );
		}
		return array( 0, __( 'No book found', 'library-management-system' ) );
	}

	private function handle_borrow_book() {

		global $wpdb;
		$response = array( 0, __( 'Invalid LMS operation', 'library-management-system' ) );
		if ( ! LIBMNS_Roles_Helper_FREE::libmns_current_user_can( 'owt7_lms_borrow_book' ) ) {
			$response = array( 0, __( 'You do not have permission to borrow books.', 'library-management-system' ) );
		} else {
			$branch_id = isset( $_REQUEST['owt7_dd_branch_id'] ) ? sanitize_text_field( wp_unslash( (string) $_REQUEST['owt7_dd_branch_id'] ) ) : "";
			$u_id = isset( $_REQUEST['owt7_dd_u_id'] ) ? sanitize_text_field( wp_unslash( (string) $_REQUEST['owt7_dd_u_id'] ) ) : "";
			$category_id = isset( $_REQUEST['owt7_dd_category_id'] ) ? sanitize_text_field( wp_unslash( (string) $_REQUEST['owt7_dd_category_id'] ) ) : "";
			$book_id = isset( $_REQUEST['owt7_dd_book_id'] ) ? sanitize_text_field( wp_unslash( (string) $_REQUEST['owt7_dd_book_id'] ) ) : "";
			$days_count = isset( $_REQUEST['owt7_dd_days'] ) ? absint( $_REQUEST['owt7_dd_days'] ) : "";

			if ( ! empty( $branch_id ) && ! empty( $u_id ) && ! empty( $category_id ) && ! empty( $book_id ) && ! empty( $days_count ) ) {

				$tbl_fine = LIBMNS_Table_Helper_FREE::get_table_name( 'book_late_fine' );
				$has_unpaid_fine = (int) $wpdb->get_var( $wpdb->prepare(
					"SELECT COUNT(*) FROM {$tbl_fine} WHERE u_id = %s AND status = 1 AND has_paid = %s",
					$u_id,
					'1'
				) );
				if ( $has_unpaid_fine > 0 ) {
					$response = [ 0, __( 'User cannot borrow a new book until all fines are cleared.', 'library-management-system' ) ];
				} else {
				$has_book_borrowed = $wpdb->get_row(
					$wpdb->prepare(
						"SELECT * FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' ) . " WHERE u_id = %s AND book_id = %s AND status = 1",
						$u_id,
						$book_id
					)
				);
				$active_borrow_count = (int) $wpdb->get_var(
					$wpdb->prepare(
						"SELECT COUNT(*) FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' ) . " WHERE u_id = %s AND status = 1",
						$u_id
					)
				);

				if(!empty($has_book_borrowed)){
					$response = [
						0, 
						__("Failed, This Book already borrowed by User.", "library-management-system")
					];
				}elseif ( $active_borrow_count >= 1 ) {
					$response = [
						0,
						__( 'Free version allows only one borrowed book per user at a time.', 'library-management-system' )
					];
				}else{
					$requested_accession = isset( $_REQUEST['owt7_accession_number'] ) ? sanitize_text_field( trim( (string) $_REQUEST['owt7_accession_number'] ) ) : '';
					if ( $requested_accession !== '' ) {
						$accession_number = $this->admin->libmns_allocate_specific_accession( $requested_accession );
					} else {
						$accession_number = $this->admin->libmns_allocate_next_available_accession( $book_id );
					}
					if ( $accession_number === null ) {
						$response = [
							0,
							__("Failed, Book is Out of Stock (no available copy).", "library-management-system")
						];
					} elseif ( $this->admin->libmns_manage_books_stock( $book_id, "minus" ) ) {

						// Use Borrow ID from form if provided (same as shown in UI); otherwise generate
						$borrow_id = isset( $_REQUEST['owt7_borrow_id'] ) ? sanitize_text_field( trim( (string) $_REQUEST['owt7_borrow_id'] ) ) : '';
						if ( $borrow_id === '' ) {
							$borrow_prefix = defined( 'LIBMNS_BOOK_BORROW_PREFIX' ) ? LIBMNS_BOOK_BORROW_PREFIX : 'LMSBB';
							$borrow_id = $this->admin->libmns_generate_id_timestamp_suffix( $borrow_prefix );
						}
						$currentDate = new DateTime();
						$currentDate->modify("+" . $days_count . " days");
						$newDate = $currentDate->format('Y-m-d');

						$tbl_users = LIBMNS_Table_Helper_FREE::get_table_name( 'users' );
						$library_user = $wpdb->get_row( $wpdb->prepare(
							"SELECT wp_user, wp_user_id FROM {$tbl_users} WHERE id = %s LIMIT 1",
							$u_id
						) );
						$borrow_wp_user = 0;
						$checkout_status = defined( 'LIBMNS_CHECKOUT_APPROVED_BY_ADMIN' ) ? LIBMNS_CHECKOUT_APPROVED_BY_ADMIN : 1;

						$wpdb->insert(LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' ), [
							"borrow_id"         => $borrow_id,
							"category_id"       => $category_id,
							"book_id"           => $book_id,
							"accession_number"  => $accession_number,
							"branch_id"         => $branch_id,
							"u_id"              => $u_id,
							"wp_user"           => $borrow_wp_user,
							"borrows_days"      => $days_count,
							"return_date"       => $newDate,
							"checkout_status"   => $checkout_status,
							"status"            => 1
						]);

						if ( $wpdb->insert_id > 0 ) {
							$response = [
								1,
								__("Successfully, Book borrowed", "library-management-system")
							];
						} else {
							$this->admin->libmns_manage_books_stock( $book_id, "plus" );
							$this->admin->libmns_release_accession( $accession_number );
							$response = [
								0,
								__("Failed to borrow book", "library-management-system")
							];
						}
					} else {
						$this->admin->libmns_release_accession( $accession_number );
						$response = [
							0,
							__("Failed, Book is Out of Stock.", "library-management-system")
						];
					}
				}
				}
			}else{
				$response = [
					0, 
					__("All fields are required", "library-management-system")
				];
			}
		}
		return $response;
	}


	private function handle_filter_borrow_book() {
		global $wpdb;
		$response = array( 0, __( 'Invalid LMS operation', 'library-management-system' ) );
		$u_id = isset( $_REQUEST['u_id'] ) ? absint( $_REQUEST['u_id'] ) : "" ;

					$borrowed_books = $wpdb->get_results(
						"SELECT borrow.id, borrow.accession_number, (SELECT book.name FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'books' ) . " as book WHERE book.id = borrow.book_id LIMIT 1) as book_name FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' ) . " as borrow WHERE borrow.status = 1 AND borrow.u_id = " . $u_id
					);

					if(!empty($borrowed_books)){
						$response = [
							1, 
							"Books",
							[
								"books" => $borrowed_books
							]
						];
					}else{
						$response = [
							0, 
							__("No book found", "library-management-system")
						];
					}
		return $response;
	}

	private function handle_book_copy_info() {
		global $wpdb;
		$response = array( 0, __( 'Invalid LMS operation', 'library-management-system' ) );
		$book_id = isset( $_REQUEST['book_id'] ) ? sanitize_text_field( wp_unslash( (string) $_REQUEST['book_id'] ) ) : '';
					if ( $book_id === '' ) {
						$response = [ 0, __( 'Invalid book.', 'library-management-system' ), [ 'copies_left' => 0, 'next_accession' => '' ] ];
					} else {
						$book = $wpdb->get_row(
							$wpdb->prepare(
								"SELECT stock_quantity FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'books' ) . " WHERE id = %s LIMIT 1",
								$book_id
							),
							OBJECT
						);
						$copies_left = ( $book && isset( $book->stock_quantity ) ) ? max( 0, (int) $book->stock_quantity ) : 0;
						$next_accession = $this->admin->libmns_peek_next_available_accession( $book_id );
						$response = [
							1,
							'',
							[
								'copies_left'   => $copies_left,
								'next_accession' => $next_accession ? $next_accession : '',
							]
						];
					}
		return $response;
	}


	/**
	 * Look up active borrow by scanned barcode (accession number). For Return Books screen – returns branch_id, u_id, borrow_id, book_name, user_name.
	 *
	 * @return array [ sts, msg, arr ]
	 */
	private function handle_lookup_return_by_barcode() {
		global $wpdb;
		$barcode_data = isset( $_REQUEST['barcode_data'] ) ? sanitize_text_field( trim( (string) $_REQUEST['barcode_data'] ) ) : '';
		if ( $barcode_data === '' ) {
			return array( 0, __( 'No barcode data.', 'library-management-system' ), array() );
		}
		$tbl_borrow = LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' );
		$tbl_books  = LIBMNS_Table_Helper_FREE::get_table_name( 'books' );
		$tbl_users  = LIBMNS_Table_Helper_FREE::get_table_name( 'users' );
		$row = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT b.id, b.borrow_id, b.branch_id, b.u_id, b.book_id, b.accession_number,
				 (SELECT name FROM `{$tbl_books}` WHERE id = b.book_id LIMIT 1) AS book_name,
				 (SELECT name FROM `{$tbl_users}` WHERE id = b.u_id LIMIT 1) AS user_name
				 FROM `{$tbl_borrow}` b WHERE b.status = 1 AND (b.accession_number = %s OR TRIM(COALESCE(b.accession_number,'')) = %s) LIMIT 1",
				$barcode_data,
				$barcode_data
			),
			OBJECT
		);
		if ( ! $row || empty( $row->u_id ) ) {
			return array( 0, __( 'No active borrow found for this barcode.', 'library-management-system' ), array() );
		}
		return array( 1, '', array(
			'id'          => (int) $row->id,
			'branch_id'   => $this->libmns_get_canonical_branch_id( (int) $row->branch_id ),
			'u_id'        => (int) $row->u_id,
			'borrow_id'   => $row->borrow_id,
			'book_name'   => $row->book_name,
			'user_name'   => $row->user_name,
			'accession_number' => isset( $row->accession_number ) ? $row->accession_number : '',
		) );
	}

	/**
	 * Compute fine amount for one borrow based on return condition.
	 *
	 * @param object $borrow_row Row from book_borrow.
	 * @param object|null $book_row Row from books (id, amount) or null to fetch.
	 * @param string $return_condition One of normal_return, lost_book, late_return.
	 * @return array [ fine_amount, extra_days, fine_type ]
	 */
	private function libmns_return_fine_for_borrow( $borrow_row, $book_row, $return_condition ) {
		global $wpdb;
		$tbl_books = LIBMNS_Table_Helper_FREE::get_table_name( 'books' );
		if ( $book_row === null && ! empty( $borrow_row->book_id ) ) {
			$book_row = $wpdb->get_row( $wpdb->prepare( "SELECT id, amount FROM {$tbl_books} WHERE id = %d LIMIT 1", $borrow_row->book_id ) );
		}
		$fine = 0.0;
		$extra_days = 0;
		$fine_type = 'late_return';

		switch ( $return_condition ) {
			case LIBMNS_RETURN_CONDITION_NORMAL:
				return array( 0, 0, 'late_return' );
			case LIBMNS_RETURN_CONDITION_LOST:
				$amount = isset( $book_row->amount ) && is_numeric( $book_row->amount ) ? floatval( $book_row->amount ) : 0;
				return array( $amount, 0, 'lost_book' );
			case LIBMNS_RETURN_CONDITION_LATE:
				$borrow_days = (int) $borrow_row->borrows_days;
				$return_date = date( 'Y-m-d' );
				$borrow_date = date( 'Y-m-d', strtotime( $borrow_row->created_at ) );
				$date_diff = (new DateTime( $borrow_date ))->diff( new DateTime( $return_date ) )->days;
				if ( $date_diff > $borrow_days ) {
					$extra_days = $date_diff - $borrow_days;
					$per_day = (float) get_option( 'owt7_lms_late_fine_currency', 0 );
					$fine = $extra_days * $per_day;
				}
				return array( $fine, $extra_days, 'late_return' );
			default:
				return array( 0, 0, 'late_return' );
		}
	}

	private function handle_return_fine_preview() {
		global $wpdb;
		$response = array( 0, __( 'Invalid request', 'library-management-system' ), array() );
		if ( ! LIBMNS_Roles_Helper_FREE::libmns_current_user_can( 'owt7_lms_return_book' ) ) {
			return array( 0, __( 'You do not have permission.', 'library-management-system' ), array() );
		}
		$borrow_ids = isset( $_REQUEST['owt7_borrow_books_id'] ) && is_array( $_REQUEST['owt7_borrow_books_id'] ) ? array_map( 'absint', $_REQUEST['owt7_borrow_books_id'] ) : array();
		$return_condition = isset( $_REQUEST['owt7_return_condition'] ) ? sanitize_text_field( $_REQUEST['owt7_return_condition'] ) : '';
		$valid_conditions = array( LIBMNS_RETURN_CONDITION_NORMAL, LIBMNS_RETURN_CONDITION_LOST, LIBMNS_RETURN_CONDITION_LATE );
		if ( empty( $borrow_ids ) || ! in_array( $return_condition, $valid_conditions, true ) ) {
			return array( 0, __( 'Invalid parameters.', 'library-management-system' ), array() );
		}
		$tbl_borrow = LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' );
		$tbl_books  = LIBMNS_Table_Helper_FREE::get_table_name( 'books' );
		$ids_placeholders = implode( ',', array_fill( 0, count( $borrow_ids ), '%d' ) );
		$borrows = $wpdb->get_results( $wpdb->prepare(
			"SELECT b.*, (SELECT amount FROM {$tbl_books} WHERE id = b.book_id LIMIT 1) AS book_amount FROM {$tbl_borrow} b WHERE b.id IN ($ids_placeholders) AND b.status = 1",
			$borrow_ids
		) );
		$total_fine = 0.0;
		foreach ( $borrows as $b ) {
			$book_row = (object) array( 'id' => $b->book_id, 'amount' => isset( $b->book_amount ) ? $b->book_amount : 0 );
			list( $fine, , ) = $this->libmns_return_fine_for_borrow( $b, $book_row, $return_condition );
			$total_fine += $fine;
		}
		$currency = get_option( 'owt7_lms_currency', '' );
		return array( 1, '', array( 'total_fine' => $total_fine, 'currency' => $currency ) );
	}

	private function handle_return_book() {
		global $wpdb;
		$response = array( 0, __( 'Invalid LMS operation', 'library-management-system' ) );
		if ( ! LIBMNS_Roles_Helper_FREE::libmns_current_user_can( 'owt7_lms_return_book' ) ) {
			$response = [ 0, __( 'You do not have permission to perform this action.', 'library-management-system' ) ];
		} else {
			$borrow_ids_raw = isset( $_REQUEST['owt7_borrow_books_id'] ) ? $_REQUEST['owt7_borrow_books_id'] : "";
			$borrow_ids = is_array( $borrow_ids_raw ) ? array_map( 'sanitize_text_field', array_values( $borrow_ids_raw ) ) : ( is_string( $borrow_ids_raw ) ? array_filter( array_map( 'sanitize_text_field', explode( ',', $borrow_ids_raw ) ) ) : array() );
			$return_condition = isset( $_REQUEST['owt7_return_condition'] ) ? sanitize_text_field( wp_unslash( (string) $_REQUEST['owt7_return_condition'] ) ) : LIBMNS_RETURN_CONDITION_NORMAL;
			$return_remark = isset( $_REQUEST['owt7_return_remark'] ) ? sanitize_textarea_field( $_REQUEST['owt7_return_remark'] ) : '';
			$valid_conditions = array( LIBMNS_RETURN_CONDITION_NORMAL, LIBMNS_RETURN_CONDITION_LOST, LIBMNS_RETURN_CONDITION_LATE );
			if ( ! in_array( $return_condition, $valid_conditions, true ) ) {
				$return_condition = LIBMNS_RETURN_CONDITION_NORMAL;
			}

			if ( ! empty( $borrow_ids ) && is_array( $borrow_ids ) ) {

				$first_return_code = isset( $_REQUEST['owt7_return_id'] ) ? sanitize_text_field( trim( (string) $_REQUEST['owt7_return_id'] ) ) : '';
				$return_prefix = defined( 'LIBMNS_BOOK_RETURN_PREFIX' ) ? LIBMNS_BOOK_RETURN_PREFIX : 'LMSBR';
				$return_index = 0;
				$tbl_books = LIBMNS_Table_Helper_FREE::get_table_name( 'books' );

				foreach ( $borrow_ids as $borrow_id ) {
					$borrow_id = sanitize_text_field( (string) $borrow_id );
					if ( $borrow_id === '' ) {
						continue;
					}
					$book_borrow_details = $wpdb->get_row(
						$wpdb->prepare(
							"SELECT * FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' ) . " WHERE id = %s AND status = 1",
							$borrow_id
						)
					);

					if ( ! empty( $book_borrow_details ) ) {
						$book_row = $wpdb->get_row( $wpdb->prepare( "SELECT id, amount FROM {$tbl_books} WHERE id = %s LIMIT 1", $book_borrow_details->book_id ) );
						list( $total_late_fine, $extra_days, $fine_type ) = $this->libmns_return_fine_for_borrow( $book_borrow_details, $book_row, $return_condition );
						$has_fine = $total_late_fine > 0;

						// Lost book: do not increase stock (book is lost)
						$release_stock = $return_condition !== LIBMNS_RETURN_CONDITION_LOST;
						if ( $release_stock && ! $this->admin->libmns_manage_books_stock( $book_borrow_details->book_id, "plus" ) ) {
							continue;
						}

						$return_accession = isset( $book_borrow_details->accession_number ) ? trim( (string) $book_borrow_details->accession_number ) : '';
						if ( $release_stock && $return_accession !== '' ) {
							$this->admin->libmns_release_accession( $return_accession );
						}

						if ( $return_index === 0 && $first_return_code !== '' ) {
							$return_code = $first_return_code;
						} else {
							$return_code = $this->admin->libmns_generate_id_timestamp_suffix( $return_prefix );
						}
						$return_index++;

						$wpdb->insert( LIBMNS_Table_Helper_FREE::get_table_name( 'book_return' ), array(
							'borrow_id'         => $book_borrow_details->borrow_id,
							'return_id'         => $return_code,
							'category_id'       => $book_borrow_details->category_id,
							'book_id'           => $book_borrow_details->book_id,
							'accession_number'  => $return_accession !== '' ? $return_accession : null,
							'branch_id'         => $book_borrow_details->branch_id,
							'wp_user'           => 0,
							'u_id'              => $book_borrow_details->u_id,
							'has_fine_status'   => $has_fine ? 1 : 0,
							'return_condition'  => $return_condition,
							'return_remark'     => $return_remark !== '' ? $return_remark : null,
							'status'           => 1,
						) );
						$return_table_id = $wpdb->insert_id;

						if ( $has_fine && $return_table_id > 0 ) {
							$wpdb->insert( LIBMNS_Table_Helper_FREE::get_table_name( 'book_late_fine' ), array(
								'return_id'   => $return_table_id,
								'book_id'     => $book_borrow_details->book_id,
								'wp_user'     => $book_borrow_details->wp_user,
								'u_id'        => $book_borrow_details->u_id,
								'extra_days'  => $extra_days,
								'fine_amount' => $total_late_fine,
								'fine_type'   => $fine_type,
								'status'      => 1,
								'has_paid'    => 1, // 1 - Not paid, 2 - Paid
							) );
						}

						$wpdb->update( LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' ), array( 'status' => 0 ), array( 'id' => $borrow_id ) );
					}
				}

				$response = array( 1, __( 'Successfully, Book(s) Returned', 'library-management-system' ) );
			} else {
				$response = array( 0, __( 'Please Select Book(s) to be Return', 'library-management-system' ) );
			}
		}
		return $response;
	}


	private function handle_data_filters() {
		global $wpdb;
		$response = array( 0, __( 'Invalid LMS operation', 'library-management-system' ) );
		$filterby = isset( $_REQUEST['filterby'] ) ? sanitize_text_field( wp_unslash( trim( (string) $_REQUEST['filterby'] ) ) ) : "" ;
					$list = isset( $_REQUEST['list'] ) ? sanitize_text_field( wp_unslash( trim( (string) $_REQUEST['list'] ) ) ) : "" ;
					$filterbyId = isset( $_REQUEST['id'] ) ? sanitize_text_field( wp_unslash( (string) $_REQUEST['id'] ) ) : '';

					if(!empty($filterby) && $filterbyId !== ''){

						$borrows = array();
						$returns = array();

						if($filterby == "category"){

							if(!empty($list) && $list == "borrow_history"){

								$borrows = $wpdb->get_results(
									$wpdb->prepare(
										"SELECT borrow.id, borrow.u_id, borrow.wp_user, borrow.borrow_id, borrow.accession_number, borrow.borrows_days, borrow.return_date, borrow.status, borrow.created_at, (SELECT category.name FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'categories' ). " category WHERE category.id = borrow.category_id LIMIT 1) as category_name, (SELECT book.name FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'books' ). " book WHERE book.id = borrow.book_id LIMIT 1) as book_name, (SELECT book.book_id FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'books' ). " book WHERE book.id = borrow.book_id LIMIT 1) as book_book_id, (SELECT branch.name FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'branches' ). " branch WHERE branch.id = borrow.branch_id LIMIT 1) as branch_name, (SELECT user.name FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'users' ). " user WHERE user.id = borrow.u_id LIMIT 1) as user_name, (SELECT user.u_id FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'users' ). " user WHERE user.id = borrow.u_id LIMIT 1) as user_u_id FROM ".LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' ). " borrow WHERE borrow.category_id = %s ORDER by borrow.id DESC",
										$filterbyId
									)
								);
							} elseif(!empty($list) && $list == "return_history"){

								$returns = $wpdb->get_results(
									$wpdb->prepare(
										"SELECT rt.id, rt.return_id, rt.borrow_id, rt.return_condition, rt.return_remark, COALESCE(NULLIF(TRIM(rt.accession_number), ''), (SELECT br.accession_number FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' ) . " br WHERE br.borrow_id = rt.borrow_id LIMIT 1)) as accession_number, rt.u_id, rt.wp_user, rt.return_status, rt.is_self_return, rt.status, rt.created_at, (SELECT category.name FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'categories' ). " category WHERE category.id = rt.category_id LIMIT 1) as category_name, (SELECT book.name FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'books' ). " book WHERE book.id = rt.book_id LIMIT 1) as book_name, (SELECT book.book_id FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'books' ). " book WHERE book.id = rt.book_id LIMIT 1) as book_book_id, (SELECT branch.name FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'branches' ). " branch WHERE branch.id = rt.branch_id LIMIT 1) as branch_name, (SELECT user.name FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'users' ). " user WHERE user.id = rt.u_id LIMIT 1) as user_name, (SELECT user.email FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'users' ). " user WHERE user.id = rt.u_id LIMIT 1) as user_email, (SELECT user.u_id FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'users' ). " user WHERE user.id = rt.u_id LIMIT 1) as user_u_id, (SELECT borrow.borrows_days FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' ). " borrow WHERE borrow.borrow_id = rt.borrow_id LIMIT 1) as total_days, (SELECT borrow.created_at FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' ). " borrow WHERE borrow.borrow_id = rt.borrow_id LIMIT 1) as issued_on, (SELECT fine.has_paid FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'book_late_fine' ). " fine WHERE fine.return_id = rt.id LIMIT 1) as has_paid, (SELECT fine.fine_amount FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'book_late_fine' ). " fine WHERE fine.return_id = rt.id LIMIT 1) as fine_amount, (SELECT fine.extra_days FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'book_late_fine' ). " fine WHERE fine.return_id = rt.id LIMIT 1) as extra_days FROM ".LIBMNS_Table_Helper_FREE::get_table_name( 'book_return' ). " as rt WHERE rt.category_id = %s ORDER by rt.id DESC",
										$filterbyId
									)
								);
							}
						} elseif($filterby == "branch"){

							if(!empty($list) && $list == "borrow_history"){

								$borrows = $wpdb->get_results(
									$wpdb->prepare(
										"SELECT borrow.id, borrow.u_id, borrow.wp_user, borrow.borrow_id, borrow.accession_number, borrow.borrows_days, borrow.return_date, borrow.status, borrow.created_at, (SELECT category.name FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'categories' ). " category WHERE category.id = borrow.category_id LIMIT 1) as category_name, (SELECT book.name FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'books' ). " book WHERE book.id = borrow.book_id LIMIT 1) as book_name, (SELECT book.book_id FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'books' ). " book WHERE book.id = borrow.book_id LIMIT 1) as book_book_id, (SELECT branch.name FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'branches' ). " branch WHERE branch.id = borrow.branch_id LIMIT 1) as branch_name, (SELECT user.name FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'users' ). " user WHERE user.id = borrow.u_id LIMIT 1) as user_name, (SELECT user.u_id FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'users' ). " user WHERE user.id = borrow.u_id LIMIT 1) as user_u_id FROM ".LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' ). " borrow WHERE borrow.branch_id = %s ORDER by borrow.id DESC",
										$filterbyId
									)
								);
							} elseif(!empty($list) && $list == "return_history"){

								$returns = $wpdb->get_results(
									$wpdb->prepare(
										"SELECT rt.id, rt.return_id, rt.borrow_id, rt.return_condition, rt.return_remark, COALESCE(NULLIF(TRIM(rt.accession_number), ''), (SELECT br.accession_number FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' ) . " br WHERE br.borrow_id = rt.borrow_id LIMIT 1)) as accession_number, rt.u_id, rt.wp_user, rt.return_status, rt.is_self_return, rt.status, rt.created_at, (SELECT category.name FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'categories' ). " category WHERE category.id = rt.category_id LIMIT 1) as category_name, (SELECT book.name FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'books' ). " book WHERE book.id = rt.book_id LIMIT 1) as book_name, (SELECT book.book_id FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'books' ). " book WHERE book.id = rt.book_id LIMIT 1) as book_book_id, (SELECT branch.name FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'branches' ). " branch WHERE branch.id = rt.branch_id LIMIT 1) as branch_name, (SELECT user.name FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'users' ). " user WHERE user.id = rt.u_id LIMIT 1) as user_name, (SELECT user.email FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'users' ). " user WHERE user.id = rt.u_id LIMIT 1) as user_email, (SELECT user.u_id FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'users' ). " user WHERE user.id = rt.u_id LIMIT 1) as user_u_id, (SELECT borrow.borrows_days FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' ). " borrow WHERE borrow.borrow_id = rt.borrow_id LIMIT 1) as total_days, (SELECT borrow.created_at FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' ). " borrow WHERE borrow.borrow_id = rt.borrow_id LIMIT 1) as issued_on, (SELECT fine.has_paid FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'book_late_fine' ). " fine WHERE fine.return_id = rt.id LIMIT 1) as has_paid, (SELECT fine.fine_amount FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'book_late_fine' ). " fine WHERE fine.return_id = rt.id LIMIT 1) as fine_amount, (SELECT fine.extra_days FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'book_late_fine' ). " fine WHERE fine.return_id = rt.id LIMIT 1) as extra_days FROM ".LIBMNS_Table_Helper_FREE::get_table_name( 'book_return' ). " as rt WHERE rt.branch_id = %s ORDER by rt.id DESC",
										$filterbyId
									)
								);
							}
						}

						if(!empty($list) && $list == "borrow_history"){

							if(!empty($borrows)){
								ob_start();
								// Template Variables
								$params['borrows'] = $borrows;
								include_once LIBMNS_PLUGIN_DIR_PATH . 'admin/views/transactions/templates/owt7_library_borrow_list.php';
								$template = ob_get_contents();
								ob_end_clean();
								// Output
								$response = [
									1, 
									"Book(s) Borrow List",
									[
										"template" => $template
									]
								];
							}else{
	
								$response = [
									0, 
									__("No data found", "library-management-system")
								];
							}
						} elseif(!empty($list) && $list == "return_history"){

							if(!empty($returns)){
								ob_start();
								// Template Variables
								$params['returns'] = $returns;
								include_once LIBMNS_PLUGIN_DIR_PATH . 'admin/views/transactions/templates/owt7_library_return_list.php';
								$template = ob_get_contents();
								ob_end_clean();
								// Output
								$response = [
									1, 
									"Book(s) Return List",
									[
										"template" => $template
									]
								];
							}else{
	
								$response = [
									0, 
									__("No data found", "library-management-system")
								];
							}
						}
					}else{

						$response = [
							0, 
							__("Invalid LMS operation", "library-management-system")
						];
					}
		return $response;
	}


	private function handle_get_days_list() {
		$response = array( 0, __( 'Invalid LMS operation', 'library-management-system' ) );
		$this->admin->libmns_librarian_can_access_page( 'owt7_library_settings' );
		wp_send_json( array(
			'sts'        => 1,
			'days'       => array(
				(object) array(
					'id'   => 1,
					'days' => LIBMNS_DEFAULT_BORROW_DAYS,
				),
			),
			'can_delete' => false,
			'strings'    => array(
				's_no'       => __( 'S No', 'library-management-system' ),
				'days_col'   => __( 'Day(s)', 'library-management-system' ),
				'days_label' => __( 'days', 'library-management-system' ),
				'action_col' => __( 'Action', 'library-management-system' ),
				'delete_title' => __( 'Delete', 'library-management-system' ),
				'no_days_yet'  => __( 'No days added yet.', 'library-management-system' ),
			),
		) );
		return;
		return $response;
	}


	private function handle_data_settings() {
		global $wpdb;
		$response = array( 0, __( 'Invalid LMS operation', 'library-management-system' ) );
		$type = isset( $_REQUEST['owt7_lms_settings_type'] ) ? sanitize_text_field( trim( $_REQUEST['owt7_lms_settings_type'] ) ) : "" ;
					
					if(!empty($type) && $type == "late_fine"){
						$amount = isset( $_REQUEST['owt7_lms_fine_amount'] ) ? floatval( $_REQUEST['owt7_lms_fine_amount'] ) : 0;
						update_option( 'owt7_lms_late_fine_currency', max( 0, $amount ) );
						delete_option( 'owt7_lms_fine_damaged_book' );
						delete_option( 'owt7_lms_fine_missing_pages' );
						$response = [
							1,
							__( 'Successfully, LMS Settings updated', 'library-management-system' )
						];
					} elseif(!empty($type) && $type == "country_currency"){
						
						$country = isset( $_REQUEST['owt7_lms_country'] ) ? sanitize_text_field( trim( $_REQUEST['owt7_lms_country'] ) ) : "" ;
						$currency = isset( $_REQUEST['owt7_lms_currency'] ) ? sanitize_text_field( trim( $_REQUEST['owt7_lms_currency'] ) ) : "" ;
						update_option( 'owt7_lms_country', $country );
						update_option( 'owt7_lms_currency', $currency );

						$response = [
							1, 
							__("Successfully, LMS Settings updated", "library-management-system")
						];
					} elseif(!empty($type) && $type == "days"){
						$response = [
							1,
							__( 'Borrow days are fixed to 30 in the free version.', 'library-management-system' )
						];
					} elseif(!empty($type) && $type == "backup"){
						require_once LIBMNS_PLUGIN_DIR_PATH . 'includes/class-libmns-db-backup-helper.php';
						$version_no = date( 'YmdHis' );
						// Backup File Path (uploads/library-management-system_uploads/db-backup)
						$upload_dir = wp_upload_dir();
						$backup_folder = $upload_dir['basedir'] . '/library-management-system_uploads/db-backup';
						$fileVersionName = "lms-backup-" . $version_no . ".json";
						$bkp_file_path = $backup_folder . "/" . $fileVersionName;
						$bkp_file_url = $upload_dir['baseurl'] . '/library-management-system_uploads/db-backup/' . $fileVersionName;
						$module_to_table = $this->admin->libmns_get_backup_module_to_table();
						if (!is_dir($backup_folder)) {
							mkdir($backup_folder, 0777, true);
						}
						$plugin_version = defined( 'LIBMNS_VERSION' ) ? LIBMNS_VERSION : '';
						$ok = libmns_generate_json_backup_free( $wpdb, $module_to_table, $bkp_file_path, $plugin_version );

						if ( ! $ok ) {
							$response = [ 0, "Failed to Generate Backup" ];
						} else {
							// Get the file size in bytes
							$fileSizeBytes = file_exists( $bkp_file_path ) ? filesize( $bkp_file_path ) : 0;
							$fileSizeKB = $fileSizeBytes / 1024;
							// Remove "latest" flag
							$wpdb->query(
								$wpdb->prepare(
									"UPDATE " . LIBMNS_Table_Helper_FREE::get_table_name( 'data_backups' ) . " SET file_flag = %s WHERE file_flag <> %s",
									"", ""
								)
							);
							$wpdb->insert( LIBMNS_Table_Helper_FREE::get_table_name( 'data_backups' ), array(
								"file_name"   => $fileVersionName,
								"file_size"   => round( $fileSizeKB, 2 ) . " KB",
								"file_flag"   => "latest",
								"file_path"   => $bkp_file_url,
								"backup_type" => "lms"
							) );
							$response = $wpdb->insert_id > 0
								? [ 1, "Successfully, LMS Data Backup Generated" ]
								: [ 0, "Failed to Generate Backup" ];
						}
					} elseif ( ! empty( $type ) && $type === 'theme' ) {
						$primary = isset( $_REQUEST['owt7_lms_theme_primary'] ) ? sanitize_hex_color( $_REQUEST['owt7_lms_theme_primary'] ) : '';
						$accent  = isset( $_REQUEST['owt7_lms_theme_accent'] ) ? sanitize_hex_color( $_REQUEST['owt7_lms_theme_accent'] ) : '';
						if ( $primary ) {
							update_option( 'owt7_lms_theme_primary', $primary );
						}
						if ( $accent ) {
							update_option( 'owt7_lms_theme_accent', $accent );
						}
						$action_keys = array( 'owt7_lms_theme_action_clone', 'owt7_lms_theme_action_view', 'owt7_lms_theme_action_edit', 'owt7_lms_theme_action_book_copies', 'owt7_lms_theme_action_delete' );
						foreach ( $action_keys as $key ) {
							$val = isset( $_REQUEST[ $key ] ) ? sanitize_hex_color( $_REQUEST[ $key ] ) : '';
							if ( $val ) {
								update_option( $key, $val );
							}
						}
						$response = [
							1,
							__( 'Theme colors saved. Refresh the page to see changes.', 'library-management-system' )
						];
					}
		return $response;
	}

	private function handle_data_option_filters() {
		global $wpdb;
		$response = array( 0, __( 'Invalid LMS operation', 'library-management-system' ) );
		$filterValue = isset( $_REQUEST['value'] ) ? sanitize_text_field( $_REQUEST['value'] ) : "all" ;
					$filterBy = isset( $_REQUEST['filterBy'] ) ? sanitize_text_field( trim( $_REQUEST['filterBy'] ) ) : "" ;
					$module = isset( $_REQUEST['module'] ) ? sanitize_text_field( trim( $_REQUEST['module'] ) ) : "" ;

					if(!empty($module)){

						// Books
						if($module == "books" && $filterBy == "category"){

							if($filterValue == "all"){

								// All Books
								$books = $wpdb->get_results(
									"SELECT book.id, book.book_id, book.name, book.is_woocom_product, book.stock_quantity, book.status, book.created_at, (SELECT category.name FROM ".LIBMNS_Table_Helper_FREE::get_table_name( 'categories' )." as category WHERE category.id = book.category_id LIMIT 1) as category_name, (SELECT bkcase.name FROM ".LIBMNS_Table_Helper_FREE::get_table_name( 'bookcases' )." as bkcase WHERE bkcase.id = book.bookcase_id LIMIT 1) as bookcase_name, (SELECT section.name FROM ".LIBMNS_Table_Helper_FREE::get_table_name( 'sections' )." as section WHERE section.id = book.bookcase_section_id LIMIT 1) as section_name, (SELECT COUNT(*) FROM ".LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' )." as borrow WHERE borrow.book_id = book.id AND borrow.status = 1) as has_active_borrow from " . LIBMNS_Table_Helper_FREE::get_table_name( 'books' ). " as book"
								);
							} elseif ( $filterValue > 0 ){

								// Filtered Books
								$books = $wpdb->get_results(
									"SELECT book.id, book.book_id, book.name, book.is_woocom_product, book.stock_quantity, book.status, book.created_at, (SELECT category.name FROM ".LIBMNS_Table_Helper_FREE::get_table_name( 'categories' )." as category WHERE category.id = book.category_id LIMIT 1) as category_name, (SELECT bkcase.name FROM ".LIBMNS_Table_Helper_FREE::get_table_name( 'bookcases' )." as bkcase WHERE bkcase.id = book.bookcase_id LIMIT 1) as bookcase_name, (SELECT section.name FROM ".LIBMNS_Table_Helper_FREE::get_table_name( 'sections' )." as section WHERE section.id = book.bookcase_section_id LIMIT 1) as section_name, (SELECT COUNT(*) FROM ".LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' )." as borrow WHERE borrow.book_id = book.id AND borrow.status = 1) as has_active_borrow from " . LIBMNS_Table_Helper_FREE::get_table_name( 'books' ). " as book WHERE book.category_id = {$filterValue}"
								);
							}

							$moduleFolder = "books";
						} elseif($module == "sections" && $filterBy == "bookcase"){ // Bookcases

							if($filterValue == "all"){

								// All Sections
								$sections = $wpdb->get_results(
									"SELECT sec.*, bkcase.name as bookcase_name, (SELECT count(*) FROM ".LIBMNS_Table_Helper_FREE::get_table_name( 'books' )." as book WHERE book.bookcase_section_id = sec.id limit 1) as total_books from " . LIBMNS_Table_Helper_FREE::get_table_name( 'sections' ). " sec INNER JOIN ". LIBMNS_Table_Helper_FREE::get_table_name( 'bookcases' ) . " bkcase ON sec.bookcase_id = bkcase.id"
								);
							} elseif ( $filterValue > 0 ){

								// Filtered Sections
								$sections = $wpdb->get_results(
									"SELECT sec.*, bkcase.name as bookcase_name, (SELECT count(*) FROM ".LIBMNS_Table_Helper_FREE::get_table_name( 'books' )." as book WHERE book.bookcase_section_id = sec.id limit 1) as total_books from " . LIBMNS_Table_Helper_FREE::get_table_name( 'sections' ). " sec INNER JOIN ". LIBMNS_Table_Helper_FREE::get_table_name( 'bookcases' ) . " bkcase ON sec.bookcase_id = bkcase.id WHERE sec.bookcase_id = {$filterValue}"
								);
							}

							$moduleFolder = "bookcases";
						} elseif($module == "users" && $filterBy == "branch"){ // Users

							if($filterValue == "all"){

								// All Users
								$users = $wpdb->get_results(
									"SELECT user.*, (SELECT name FROM ".LIBMNS_Table_Helper_FREE::get_table_name( 'branches' )." as branch WHERE branch.id = user.branch_id LIMIT 1) as branch_name from " . LIBMNS_Table_Helper_FREE::get_table_name( 'users' )." as user"
								);
							} elseif ( $filterValue > 0 ){

								// Filtered Users
								$users = $wpdb->get_results(
									"SELECT user.*, (SELECT name FROM ".LIBMNS_Table_Helper_FREE::get_table_name( 'branches' )." as branch WHERE branch.id = user.branch_id LIMIT 1) as branch_name from " . LIBMNS_Table_Helper_FREE::get_table_name( 'users' )." as user WHERE user.branch_id = {$filterValue}"
								);
							}

							$moduleFolder = "users";
						} 

						$params[$module] = ${$module};

						ob_start();
						include_once LIBMNS_PLUGIN_DIR_PATH . "admin/views/{$moduleFolder}/templates/owt7_library_{$module}_list.php";
						$template = ob_get_contents();
						ob_end_clean();

						$response = [
							1,
							ucfirst($module),
							[
								"template" => $template
							]
						];
					}else{

						$response = [
							0, 
							__("Invalid LMS Module", "library-management-system")
						];
					}
		return $response;
	}


	private function handle_import_test_data() {
		global $wpdb;
		$response = array( 0, __( 'Invalid LMS operation', 'library-management-system' ) );
		if ( ! LIBMNS_Roles_Helper_FREE::libmns_current_user_can( 'owt7_lms_run_test_data_importer' ) ) {
						$response = array( 0, __( 'You do not have permission to run the test data importer.', 'library-management-system' ) );
					} else {
					if ( get_option( 'owt7_lms_test_data' ) || $this->libmns_has_test_data_map() ) {
						$this->libmns_remove_tracked_test_data( true );
					}

					$json_path = LIBMNS_PLUGIN_DIR_PATH . 'admin/sample-data/test-data.json';
					$import_status = false;
					$tracked_map = array(
						'category_ids' => array(),
						'bookcase_ids' => array(),
						'section_ids'  => array(),
						'branch_ids'   => array(),
						'book_ids'     => array(),
						'book_codes'   => array(),
						'user_ids'     => array(),
					);

					if ( ! file_exists( $json_path ) ) {
						$response = [ 0, __( 'Test data file not found: test-data.json', 'library-management-system' ) ];
					} else {
						$raw = file_get_contents( $json_path );
						$data = is_string( $raw ) ? json_decode( $raw, true ) : null;
						if ( ! is_array( $data ) ) {
							$response = [ 0, __( 'Invalid test data JSON.', 'library-management-system' ) ];
						} else {
							$tbl_category = LIBMNS_Table_Helper_FREE::get_table_name( 'categories' );
							$tbl_bookcase = LIBMNS_Table_Helper_FREE::get_table_name( 'bookcases' );
							$tbl_sections = LIBMNS_Table_Helper_FREE::get_table_name( 'sections' );
							$tbl_branch   = LIBMNS_Table_Helper_FREE::get_table_name( 'branches' );
							$tbl_books   = LIBMNS_Table_Helper_FREE::get_table_name( 'books' );
							$tbl_users   = LIBMNS_Table_Helper_FREE::get_table_name( 'users' );

							$category_by_name = array();
							$bookcase_by_name = array();
							$section_by_key   = array();
							$branch_by_name   = array();

							// 1. Categories (name -> id)
							if ( ! empty( $data['categories'] ) ) {
								foreach ( $data['categories'] as $row ) {
									$name = isset( $row['name'] ) ? LIBMNS_Sanitize_FREE::multilingual_text( $row['name'] ) : '';
									if ( $name !== '' ) {
										$wpdb->insert( $tbl_category, array( 'name' => $name, 'status' => '1' ), array( '%s', '%s' ) );
										if ( $wpdb->insert_id > 0 ) {
											$category_by_name[ $name ] = $wpdb->insert_id;
											$tracked_map['category_ids'][] = $wpdb->insert_id;
										}
									}
								}
							}

							// 2. Bookcases (name -> id)
							if ( ! empty( $data['bookcases'] ) ) {
								foreach ( $data['bookcases'] as $row ) {
									$name = isset( $row['name'] ) ? LIBMNS_Sanitize_FREE::multilingual_text( $row['name'] ) : '';
									if ( $name !== '' ) {
										$wpdb->insert( $tbl_bookcase, array( 'name' => $name, 'status' => '1' ), array( '%s', '%s' ) );
										if ( $wpdb->insert_id > 0 ) {
											$bookcase_by_name[ $name ] = $wpdb->insert_id;
											$tracked_map['bookcase_ids'][] = $wpdb->insert_id;
										}
									}
								}
							}

							// 3. Sections (bookcase_name + name -> id)
							if ( ! empty( $data['sections'] ) ) {
								foreach ( $data['sections'] as $row ) {
									$bname = isset( $row['bookcase_name'] ) ? LIBMNS_Sanitize_FREE::multilingual_text( $row['bookcase_name'] ) : '';
									$name  = isset( $row['name'] ) ? LIBMNS_Sanitize_FREE::multilingual_text( $row['name'] ) : '';
									if ( $bname !== '' && $name !== '' && isset( $bookcase_by_name[ $bname ] ) ) {
										$bookcase_id = $bookcase_by_name[ $bname ];
										$wpdb->insert( $tbl_sections, array( 'bookcase_id' => $bookcase_id, 'name' => $name, 'status' => '1' ), array( '%d', '%s', '%s' ) );
										if ( $wpdb->insert_id > 0 ) {
											$section_by_key[ $bname . '|' . $name ] = $wpdb->insert_id;
											$tracked_map['section_ids'][] = $wpdb->insert_id;
										}
									}
								}
							}

							// 4. Branches (name -> id)
							if ( ! empty( $data['branches'] ) ) {
								foreach ( $data['branches'] as $row ) {
									$name = isset( $row['name'] ) ? LIBMNS_Sanitize_FREE::multilingual_text( $row['name'] ) : '';
									if ( $name !== '' ) {
										$wpdb->insert( $tbl_branch, array( 'name' => $name, 'status' => '1' ), array( '%s', '%s' ) );
										if ( $wpdb->insert_id > 0 ) {
											$branch_by_name[ $name ] = $wpdb->insert_id;
											$tracked_map['branch_ids'][] = $wpdb->insert_id;
										}
									}
								}
							}

							// 5. Books: resolve category_name, bookcase_name (and optional section_name) -> IDs; generate book_id; copies from stock_quantity
							if ( ! empty( $data['books'] ) ) {
								foreach ( $data['books'] as $row ) {
									$category_name = isset( $row['category_name'] ) ? LIBMNS_Sanitize_FREE::multilingual_text( $row['category_name'] ) : '';
									$bookcase_name = isset( $row['bookcase_name'] ) ? LIBMNS_Sanitize_FREE::multilingual_text( $row['bookcase_name'] ) : '';
									$section_name  = isset( $row['section_name'] ) ? LIBMNS_Sanitize_FREE::multilingual_text( $row['section_name'] ) : '';
									$category_id   = isset( $category_by_name[ $category_name ] ) ? (int) $category_by_name[ $category_name ] : 0;
									$bookcase_id   = isset( $bookcase_by_name[ $bookcase_name ] ) ? (int) $bookcase_by_name[ $bookcase_name ] : 0;
									$section_id    = 0;
									if ( $section_name !== '' && $bookcase_name !== '' ) {
										$key = $bookcase_name . '|' . $section_name;
										$section_id = isset( $section_by_key[ $key ] ) ? (int) $section_by_key[ $key ] : 0;
									}
									$stock_quantity = isset( $row['stock_quantity'] ) ? max( 0, (int) $row['stock_quantity'] ) : 1;
									$book_id = $this->admin->libmns_generate_book_id_pattern();
									$book_row = array(
										'book_id'              => $book_id,
										'bookcase_id'          => $bookcase_id,
										'bookcase_section_id'  => $section_id,
										'category_id'          => $category_id,
										'name'                 => isset( $row['name'] ) ? LIBMNS_Sanitize_FREE::multilingual_text( $row['name'] ) : '',
										'author_name'          => isset( $row['author_name'] ) ? LIBMNS_Sanitize_FREE::multilingual_text( $row['author_name'] ) : '',
										'publication_name'     => isset( $row['publication_name'] ) ? LIBMNS_Sanitize_FREE::multilingual_text( $row['publication_name'] ) : '',
										'publication_year'     => isset( $row['publication_year'] ) ? LIBMNS_Sanitize_FREE::multilingual_text( $row['publication_year'] ) : '',
										'publication_location' => isset( $row['publication_location'] ) ? LIBMNS_Sanitize_FREE::multilingual_text( $row['publication_location'] ) : '',
										'amount'               => isset( $row['amount'] ) ? LIBMNS_Sanitize_FREE::multilingual_text( $row['amount'] ) : '',
										'cover_image'          => isset( $row['cover_image'] ) ? esc_url_raw( $row['cover_image'] ) : '',
										'isbn'                 => isset( $row['isbn'] ) ? LIBMNS_Sanitize_FREE::multilingual_text( $row['isbn'] ) : '',
										'book_url'             => isset( $row['book_url'] ) ? esc_url_raw( $row['book_url'] ) : '',
										'stock_quantity'       => $stock_quantity,
										'book_language'        => isset( $row['book_language'] ) ? LIBMNS_Sanitize_FREE::multilingual_text( $row['book_language'] ) : '',
										'book_pages'           => isset( $row['book_pages'] ) ? absint( $row['book_pages'] ) : 0,
										'description'          => isset( $row['description'] ) ? LIBMNS_Sanitize_FREE::multilingual_text( $row['description'] ) : '',
										'status'               => isset( $row['status'] ) ? max( 0, (int) $row['status'] ) : 1,
									);
									$wpdb->insert( $tbl_books, $book_row );
									if ( $wpdb->insert_id > 0 ) {
										$tracked_map['book_ids'][]   = $wpdb->insert_id;
										$tracked_map['book_codes'][] = $book_id;
									}
									if ( $wpdb->insert_id > 0 && $stock_quantity > 0 ) {
										$this->admin->libmns_insert_book_copies( $book_id, $stock_quantity, $bookcase_id, $section_id );
									}
								}
							}

							// 6. Users: resolve branch_name -> branch_id; generate u_id
							if ( ! empty( $data['users'] ) ) {
								foreach ( $data['users'] as $row ) {
									$branch_name = isset( $row['branch_name'] ) ? LIBMNS_Sanitize_FREE::multilingual_text( $row['branch_name'] ) : '';
									$branch_id   = isset( $branch_by_name[ $branch_name ] ) ? (int) $branch_by_name[ $branch_name ] : 0;
									$user_row = array(
										'register_from' => isset( $row['register_from'] ) && in_array( $row['register_from'], array( 'web', 'admin' ), true ) ? $row['register_from'] : 'admin',
										'wp_user'       => 0,
										'wp_user_id'    => 0,
										'u_id'         => $this->admin->libmns_generate_user_id_pattern(),
										'name'         => isset( $row['name'] ) ? LIBMNS_Sanitize_FREE::multilingual_text( $row['name'] ) : '',
										'email'        => isset( $row['email'] ) ? sanitize_email( $row['email'] ) : '',
										'gender'       => isset( $row['gender'] ) && in_array( $row['gender'], array( 'male', 'female', 'other' ), true ) ? $row['gender'] : null,
										'branch_id'    => $branch_id,
										'phone_no'     => isset( $row['phone_no'] ) ? LIBMNS_Sanitize_FREE::multilingual_text( $row['phone_no'] ) : '',
										'profile_image'=> isset( $row['profile_image'] ) ? LIBMNS_Sanitize_FREE::multilingual_text( $row['profile_image'] ) : '',
										'address_info' => isset( $row['address_info'] ) ? LIBMNS_Sanitize_FREE::multilingual_text( $row['address_info'] ) : '',
										'status'       => isset( $row['status'] ) ? max( 0, (int) $row['status'] ) : 1,
									);
									$wpdb->insert( $tbl_users, $user_row );
									if ( $wpdb->insert_id > 0 ) {
										$tracked_map['user_ids'][] = $wpdb->insert_id;
									}
								}
							}
							$import_status = true;
						}
					}

					if($import_status){
						$this->libmns_save_test_data_map( $tracked_map );
						
						$response = [
							1, 
							__("Successfully, Test Data Imported to LMS", "library-management-system")
						];
					}else{
						$response = [
							0, 
							__("Failed to Import Test data", "library-management-system")
						];
					}
					}
		return $response;
	}


	private function handle_remove_test_data() {
		$response = array( 0, __( 'Invalid LMS operation', 'library-management-system' ) );
		if ( ! LIBMNS_Roles_Helper_FREE::libmns_current_user_can( 'owt7_lms_run_test_data_importer' ) ) {
						$response = array( 0, __( 'You do not have permission to remove test data.', 'library-management-system' ) );
					} else {
					$this->libmns_remove_tracked_test_data( true );
						
					$response = [
						1, 
						__("Successfully, Test Data Removed", "library-management-system")
					];
					}
		return $response;
	}


	/**
	 * Return view modal: fetch one return by id and return rendered HTML for book, user, remark sections.
	 */
	private function handle_get_return_view_content() {
		global $wpdb;
		$return_id = isset( $_REQUEST['return_id'] ) ? sanitize_text_field( wp_unslash( (string) $_REQUEST['return_id'] ) ) : '';
		if ( $return_id === '' ) {
			return array( 0, __( 'Invalid return', 'library-management-system' ) );
		}
		$tbl_return  = LIBMNS_Table_Helper_FREE::get_table_name( 'book_return' );
		$tbl_borrow   = LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' );
		$tbl_cat     = LIBMNS_Table_Helper_FREE::get_table_name( 'categories' );
		$tbl_books   = LIBMNS_Table_Helper_FREE::get_table_name( 'books' );
		$tbl_branches = LIBMNS_Table_Helper_FREE::get_table_name( 'branches' );
		$tbl_users   = LIBMNS_Table_Helper_FREE::get_table_name( 'users' );
		$tbl_fine    = LIBMNS_Table_Helper_FREE::get_table_name( 'book_late_fine' );
		$sql = "SELECT rt.id, rt.return_id, rt.u_id, rt.wp_user, rt.borrow_id, COALESCE(NULLIF(TRIM(rt.accession_number), ''), (SELECT br.accession_number FROM {$tbl_borrow} br WHERE br.borrow_id = rt.borrow_id LIMIT 1)) as accession_number, rt.return_status, rt.return_condition, rt.return_remark, rt.is_self_return, rt.status, rt.created_at, (SELECT category.name FROM {$tbl_cat} category WHERE category.id = rt.category_id LIMIT 1) as category_name, (SELECT book.name FROM {$tbl_books} book WHERE book.id = rt.book_id LIMIT 1) as book_name, (SELECT book.book_id FROM {$tbl_books} book WHERE book.id = rt.book_id LIMIT 1) as book_book_id, (SELECT branch.name FROM {$tbl_branches} branch WHERE branch.id = rt.branch_id LIMIT 1) as branch_name, (SELECT user.name FROM {$tbl_users} user WHERE user.id = rt.u_id LIMIT 1) as user_name, (SELECT user.email FROM {$tbl_users} user WHERE user.id = rt.u_id LIMIT 1) as user_email, (SELECT user.u_id FROM {$tbl_users} user WHERE user.id = rt.u_id LIMIT 1) as user_u_id, (SELECT borrow.borrows_days FROM {$tbl_borrow} borrow WHERE borrow.borrow_id = rt.borrow_id LIMIT 1) as total_days, (SELECT borrow.created_at FROM {$tbl_borrow} borrow WHERE borrow.borrow_id = rt.borrow_id LIMIT 1) as issued_on, (SELECT fine.has_paid FROM {$tbl_fine} fine WHERE fine.return_id = rt.id LIMIT 1) as has_paid, (SELECT fine.fine_amount FROM {$tbl_fine} fine WHERE fine.return_id = rt.id LIMIT 1) as fine_amount, (SELECT fine.extra_days FROM {$tbl_fine} fine WHERE fine.return_id = rt.id LIMIT 1) as extra_days FROM {$tbl_return} as rt WHERE rt.id = %s LIMIT 1";
		$return = $wpdb->get_row( $wpdb->prepare( $sql, $return_id ) );
		if ( empty( $return ) ) {
			return array( 0, __( 'Return not found', 'library-management-system' ) );
		}
		$currency = get_option( 'owt7_lms_currency', '' );
		$part = 'book';
		ob_start();
		include LIBMNS_PLUGIN_DIR_PATH . 'admin/views/transactions/templates/owt7_library_return_view_modal_content.php';
		$book_html = ob_get_clean();
		$part = 'user';
		ob_start();
		include LIBMNS_PLUGIN_DIR_PATH . 'admin/views/transactions/templates/owt7_library_return_view_modal_content.php';
		$user_html = ob_get_clean();
		$part = 'remark';
		ob_start();
		include LIBMNS_PLUGIN_DIR_PATH . 'admin/views/transactions/templates/owt7_library_return_view_modal_content.php';
		$remark_html = ob_get_clean();
		$has_fine = $return->status && (int) $return->has_paid === 1 && ! in_array( (int) $return->return_status, array( LIBMNS_DEFAULT_RETURN, LIBMNS_RETURN_REJECTED ), true );
		$pay_fine_id = $has_fine ? base64_encode( (string) $return->id ) : '';
		$can_download_receipt = ! empty( $return->id ) && ! in_array( (int) $return->return_status, array( LIBMNS_DEFAULT_RETURN, LIBMNS_RETURN_REJECTED ), true );
		return array(
			1,
			'',
			array(
				'book_html'   => $book_html,
				'user_html'   => $user_html,
				'remark_html' => $remark_html,
				'has_fine'    => $has_fine ? 1 : 0,
				'can_download_receipt' => $can_download_receipt ? 1 : 0,
				'pay_fine_id' => $pay_fine_id,
			),
		);
	}

	/**
	 * Quick Return modal: fetch one active borrow by DB id and return rendered HTML
	 * for user, book, and loan sections used in the inline quick-return modal.
	 */
	private function handle_get_quick_return_modal_content() {
		global $wpdb;

		if ( ! LIBMNS_Roles_Helper_FREE::libmns_current_user_can( 'owt7_lms_return_book' ) ) {
			return array( 0, __( 'You do not have permission.', 'library-management-system' ) );
		}

		$borrow_db_id = isset( $_REQUEST['borrow_db_id'] ) ? absint( $_REQUEST['borrow_db_id'] ) : 0;
		if ( $borrow_db_id < 1 ) {
			return array( 0, __( 'Invalid request.', 'library-management-system' ) );
		}

		$tbl_borrow   = LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' );
		$tbl_books    = LIBMNS_Table_Helper_FREE::get_table_name( 'books' );
		$tbl_cat      = LIBMNS_Table_Helper_FREE::get_table_name( 'categories' );
		$tbl_branches = LIBMNS_Table_Helper_FREE::get_table_name( 'branches' );
		$tbl_users    = LIBMNS_Table_Helper_FREE::get_table_name( 'users' );

		$borrow = $wpdb->get_row( $wpdb->prepare(
			"SELECT b.id, b.u_id, b.wp_user, b.borrow_id, b.accession_number, b.borrows_days, b.return_date, b.status, b.created_at,
			 (SELECT cat.name  FROM {$tbl_cat}      cat  WHERE cat.id  = b.category_id LIMIT 1) AS category_name,
			 (SELECT bk.name   FROM {$tbl_books}    bk   WHERE bk.id   = b.book_id     LIMIT 1) AS book_name,
			 (SELECT bk.book_id FROM {$tbl_books}   bk   WHERE bk.id   = b.book_id     LIMIT 1) AS book_book_id,
			 (SELECT br.name   FROM {$tbl_branches} br   WHERE br.id   = b.branch_id   LIMIT 1) AS branch_name,
			 (SELECT u.name    FROM {$tbl_users}    u    WHERE u.id    = b.u_id         LIMIT 1) AS user_name,
			 (SELECT u.email   FROM {$tbl_users}    u    WHERE u.id    = b.u_id         LIMIT 1) AS user_email,
			 (SELECT u.u_id    FROM {$tbl_users}    u    WHERE u.id    = b.u_id         LIMIT 1) AS user_u_id
			 FROM {$tbl_borrow} b
			 WHERE b.id = %d AND b.status = 1
			 LIMIT 1",
			$borrow_db_id
		) );

		if ( empty( $borrow ) ) {
			return array( 0, __( 'Borrow record not found or already returned.', 'library-management-system' ) );
		}

		$currency      = get_option( 'owt7_lms_currency', '' );
		$return_prefix = defined( 'LIBMNS_BOOK_RETURN_PREFIX' ) ? LIBMNS_BOOK_RETURN_PREFIX : 'LMSBR';
		$next_return_id = $this->admin->libmns_generate_id_timestamp_suffix( $return_prefix );

		$part = 'user';
		ob_start();
		include LIBMNS_PLUGIN_DIR_PATH . 'admin/views/transactions/templates/owt7_library_quick_return_modal_content.php';
		$user_html = ob_get_clean();

		$part = 'book';
		ob_start();
		include LIBMNS_PLUGIN_DIR_PATH . 'admin/views/transactions/templates/owt7_library_quick_return_modal_content.php';
		$book_html = ob_get_clean();

		$part = 'loan';
		ob_start();
		include LIBMNS_PLUGIN_DIR_PATH . 'admin/views/transactions/templates/owt7_library_quick_return_modal_content.php';
		$loan_html = ob_get_clean();

		return array(
			1,
			'',
			array(
				'user_html'      => $user_html,
				'book_html'      => $book_html,
				'loan_html'      => $loan_html,
				'borrow_db_id'   => (int) $borrow->id,
				'next_return_id' => $next_return_id,
				'currency'       => $currency,
				'issued_on'      => date( 'Y-m-d', strtotime( $borrow->created_at ) ),
				'borrows_days'   => (int) $borrow->borrows_days,
			),
		);
	}

	private function handle_pay_late_fine() {
		global $wpdb;
		$response = array( 0, __( 'Invalid LMS operation', 'library-management-system' ) );
		$raw_return = isset( $_REQUEST['return_id'] ) ? sanitize_text_field( wp_unslash( (string) $_REQUEST['return_id'] ) ) : '';
					$return_id = $raw_return !== '' ? sanitize_text_field( (string) base64_decode( $raw_return, true ) ) : '';

					$book_fine_details = $return_id !== '' ? $wpdb->get_row(
						$wpdb->prepare(
							"SELECT * FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'book_late_fine' ) . " WHERE return_id = %s AND has_paid = 1",
							$return_id
						)
					) : null;

					if(!empty($book_fine_details)){
						$wpdb->update(LIBMNS_Table_Helper_FREE::get_table_name( 'book_late_fine' ), [
							"has_paid" => 2,
							"status" => 0
						], [
							"return_id" => $return_id
						]);
						$wpdb->update(LIBMNS_Table_Helper_FREE::get_table_name( 'book_return' ), [
							"has_fine_status" => 0
						], [
							"id" => $return_id
						]);
						$response = [
							1, 
							__("Successfully, Late Fine Paid.", "library-management-system")
						];
					}else{
						$response = [
							0, 
							__("Fine already paid", "library-management-system")
						];
					}
		return $response;
	}


	private function handle_download_sample_data() {
		$response = array( 0, __( 'Invalid LMS operation', 'library-management-system' ) );
		$type   = isset( $_REQUEST['file_type'] ) ? sanitize_text_field( $_REQUEST['file_type'] ) : '';
		$format = isset( $_REQUEST['format'] ) ? sanitize_text_field( $_REQUEST['format'] ) : 'csv';
		$free_limit = defined( 'LIBMNS_FREE_VERSION_LIMIT' ) ? (int) LIBMNS_FREE_VERSION_LIMIT : 30;

		$allowed_types = array( 'categories', 'bookcases', 'sections', 'books', 'branches', 'users' );

		if ( ! in_array( $type, $allowed_types, true ) ) {
			return array( 0, __( 'Invalid Module Type', 'library-management-system' ) );
		}

		$rows = $this->libmns_get_sample_data_rows_from_json( $type );
		if ( empty( $rows ) ) {
			return array( 0, __( 'Sample data not found. Check test-data.json.', 'library-management-system' ) );
		}
		if ( count( $rows ) > ( $free_limit + 1 ) ) {
			$header = array_shift( $rows );
			$rows   = array_merge( array( $header ), array_slice( $rows, 0, $free_limit ) );
		}

		if ( $format === 'xlsx' ) {
			if ( ! class_exists( 'ZipArchive' ) ) {
				return array( 0, __( 'Excel export requires the PHP Zip extension. Enable it in php.ini (extension=zip) and restart the web server.', 'library-management-system' ) );
			}
			if ( ! class_exists( 'PhpOffice\PhpSpreadsheet\Spreadsheet' ) || ! class_exists( 'PhpOffice\PhpSpreadsheet\IOFactory' ) ) {
				return array( 0, __( 'Excel support is not available. Run "composer install" in the plugin directory.', 'library-management-system' ) );
			}
			try {
				$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
				$sheet       = $spreadsheet->getActiveSheet();
				$rowIndex    = 1;
				foreach ( $rows as $row ) {
					$colIndex = 1;
					foreach ( $row as $cellValue ) {
						$sheet->setCellValueByColumnAndRow( $colIndex, $rowIndex, (string) $cellValue );
						$colIndex++;
					}
					$rowIndex++;
				}
				$writer   = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter( $spreadsheet, 'Xlsx' );
				$filename = $type . '.xlsx';
				header( 'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' );
				header( 'Content-Disposition: attachment; filename="' . esc_attr( $filename ) . '"' );
				$writer->save( 'php://output' );
				exit;
			} catch ( \Exception $e ) {
				return array( 0, __( 'Could not generate Excel file.', 'library-management-system' ) );
			}
		}

		// CSV: build CSV string from rows (with UTF-8 BOM for Excel) and return as base64
		$csv_string = "\xEF\xBB\xBF" . $this->libmns_rows_to_csv_string( $rows );
		$response   = array(
			1,
			__( 'File link', 'library-management-system' ),
			array(
				'content'  => base64_encode( $csv_string ),
				'filename' => $type . '.csv',
			),
		);
		return $response;
	}

	/**
	 * Load test-data.json and return rows (header + data) for the given type.
	 * Column order matches expected CSV/import format.
	 *
	 * @param string $type One of: categories, bookcases, sections, branches, books, users.
	 * @return array Empty array on failure, else array of rows (each row is array of string values).
	 */
	private function libmns_get_sample_data_rows_from_json( $type ) {
		$json_path = LIBMNS_PLUGIN_DIR_PATH . 'admin/sample-data/test-data.json';
		if ( ! is_readable( $json_path ) ) {
			return array();
		}
		$raw  = file_get_contents( $json_path );
		$data = is_string( $raw ) ? json_decode( $raw, true ) : null;
		if ( ! is_array( $data ) || ! isset( $data[ $type ] ) || ! is_array( $data[ $type ] ) ) {
			return array();
		}
		$items = $data[ $type ];
		$order = $this->libmns_sample_data_column_order( $type );
		if ( empty( $order ) ) {
			return array();
		}
		$rows = array( $order );
		foreach ( $items as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}
			$row = array();
			foreach ( $order as $key ) {
				$val = isset( $item[ $key ] ) ? $item[ $key ] : '';
				$row[] = is_scalar( $val ) ? (string) $val : '';
			}
			$rows[] = $row;
		}
		return $rows;
	}

	/**
	 * Expected column order per sample data type (same as test-data.json and CSV import).
	 *
	 * @param string $type One of: categories, bookcases, sections, branches, books, users.
	 * @return array List of column keys.
	 */
	private function libmns_sample_data_column_order( $type ) {
		$orders = array(
			'categories' => array( 'name' ),
			'bookcases'  => array( 'name' ),
			'sections'   => array( 'bookcase_name', 'name' ),
			'branches'   => array( 'name' ),
			'books'      => array( 'name', 'category_name', 'bookcase_name', 'author_name', 'publication_name', 'publication_year', 'publication_location', 'amount', 'cover_image', 'isbn', 'book_language', 'book_pages', 'stock_quantity', 'status', 'description' ),
			'users'      => array( 'register_from', 'name', 'email', 'gender', 'branch_name', 'phone_no', 'address_info', 'status' ),
		);
		return isset( $orders[ $type ] ) ? $orders[ $type ] : array();
	}

	/**
	 * Convert array of rows to CSV string (RFC 4180-style: quote fields containing comma/newline).
	 *
	 * @param array $rows First row is header, rest are data.
	 * @return string CSV content.
	 */
	private function libmns_rows_to_csv_string( $rows ) {
		$out = '';
		foreach ( $rows as $row ) {
			$line = array();
			foreach ( $row as $cell ) {
				$s = (string) $cell;
				if ( strpos( $s, ',' ) !== false || strpos( $s, '"' ) !== false || strpos( $s, "\n" ) !== false || strpos( $s, "\r" ) !== false ) {
					$s = '"' . str_replace( '"', '""', $s ) . '"';
				}
				$line[] = $s;
			}
			$out .= implode( ',', $line ) . "\n";
		}
		return $out;
	}


	/**
	 * Parse a CSV file into array of rows (handles quoted fields, strips UTF-8 BOM from first cell).
	 *
	 * @param string $file_path Path to CSV file.
	 * @return array List of rows, each row is array of string values.
	 */
	private function libmns_parse_csv_file_to_rows( $file_path ) {
		$rows = array();
		$fp   = fopen( $file_path, 'rb' );
		if ( ! $fp ) {
			return $rows;
		}
		$bom = "\xEF\xBB\xBF";
		while ( ( $row = fgetcsv( $fp, 0, ',' ) ) !== false ) {
			if ( ! empty( $rows ) === false && isset( $row[0] ) && is_string( $row[0] ) && substr( $row[0], 0, 3 ) === $bom ) {
				$row[0] = substr( $row[0], 3 );
			}
			$rows[] = array_map( function ( $cell ) {
				return (string) $cell;
			}, $row );
		}
		fclose( $fp );
		return $rows;
	}

	/**
	 * Parse uploaded Excel file (.xlsx, .xls) into array of rows (first row = header).
	 * Column format must match the same headers as CSV import.
	 *
	 * @param string $file_path Temporary upload file path.
	 * @return array|WP_Error Array of rows (each row is array of cell values) or WP_Error on failure.
	 */
	private function libmns_parse_excel_upload( $file_path ) {
		if ( ! class_exists( 'ZipArchive' ) ) {
			return new \WP_Error( 'missing_zip', __( 'Excel import requires the PHP Zip extension. Enable it in php.ini (extension=zip) and restart the web server.', 'library-management-system' ) );
		}
		if ( ! class_exists( 'PhpOffice\PhpSpreadsheet\IOFactory' ) ) {
			return new \WP_Error( 'missing_lib', __( 'Excel support is not available. Run "composer install" in the plugin directory.', 'library-management-system' ) );
		}
		if ( ! is_readable( $file_path ) ) {
			return new \WP_Error( 'read_error', __( 'Could not read the uploaded file.', 'library-management-system' ) );
		}
		try {
			$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load( $file_path );
			$sheet       = $spreadsheet->getActiveSheet();
			$rows        = $sheet->toArray( null, true, true, true );
			$rows        = array_values( $rows );
			// Normalize: convert to list of arrays with string values (same shape as CSV rows).
			$out = array();
			foreach ( $rows as $row ) {
				$out[] = array_map( function ( $cell ) {
					if ( $cell === null || $cell === '' ) {
						return '';
					}
					return (string) $cell;
				}, array_values( $row ) );
			}
			return $out;
		} catch ( \Exception $e ) {
			return new \WP_Error( 'parse_error', __( 'Invalid or corrupted Excel file.', 'library-management-system' ) );
		}
	}

	private function libmns_find_or_create_import_name_record( $table_name, $raw_name ) {
		global $wpdb;

		$name = trim( (string) $raw_name );
		if ( $name === '' ) {
			return 0;
		}

		$table_name_escaped = str_replace( '`', '``', $table_name );
		$normalized_name    = strtolower( $name );
		$existing_id        = (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT id FROM `{$table_name_escaped}` WHERE LOWER(TRIM(name)) = %s LIMIT 1",
				$normalized_name
			)
		);

		if ( $existing_id > 0 ) {
			return $existing_id;
		}

		$wpdb->insert(
			$table_name,
			array(
				'name' => $name,
			),
			array( '%s' )
		);

		return $wpdb->insert_id > 0 ? (int) $wpdb->insert_id : 0;
	}

	private function handle_upload_form() {
		global $wpdb;
		$response = array( 0, __( 'Invalid LMS operation', 'library-management-system' ) );
		$csvType  = isset( $_REQUEST['csvType'] ) ? sanitize_text_field( wp_unslash( (string) $_REQUEST['csvType'] ) ) : '';
		$csvData  = isset( $_REQUEST['csvData'] ) ? ( is_array( $_REQUEST['csvData'] ) ? array_map( function ( $row ) {
			return is_array( $row ) ? array_map( 'sanitize_text_field', $row ) : sanitize_text_field( (string) $row );
		}, $_REQUEST['csvData'] ) : wp_unslash( (string) $_REQUEST['csvData'] ) ) : '';

		$owt7_lms_csv_max_rows = defined( 'LIBMNS_FREE_VERSION_LIMIT' ) ? (int) LIBMNS_FREE_VERSION_LIMIT : 30;

		$owt7_lms_csv_expected_headers = array(
			'categories' => array( 'name' ),
			'bookcases'  => array( 'name' ),
			'sections'   => array( 'bookcase_name', 'name' ),
			'branches'   => array( 'name' ),
			'books'      => array( 'name', 'category_name', 'bookcase_name', 'author_name', 'publication_name', 'publication_year', 'publication_location', 'amount', 'cover_image', 'isbn', 'book_language', 'book_pages', 'stock_quantity', 'status', 'description' ),
			'users'      => array( 'register_from', 'name', 'email', 'gender', 'branch_name', 'phone_no', 'address_info', 'status' ),
		);

		$csvDataArray = array();
		$is_excel     = false;

		if ( ! empty( $_FILES['owt7_upload_csv_file']['tmp_name'] ) && is_uploaded_file( $_FILES['owt7_upload_csv_file']['tmp_name'] ) ) {
			$name = isset( $_FILES['owt7_upload_csv_file']['name'] ) ? strtolower( $_FILES['owt7_upload_csv_file']['name'] ) : '';
			if ( preg_match( '/\.(xlsx|xls)$/', $name ) ) {
				$parsed = $this->libmns_parse_excel_upload( $_FILES['owt7_upload_csv_file']['tmp_name'] );
				if ( is_wp_error( $parsed ) ) {
					return array( 0, $parsed->get_error_message() );
				}
				$csvDataArray = $parsed;
				$is_excel     = true;
			}
		}

		if ( ! $is_excel ) {
			if ( empty( $csvData ) ) {
				$response = array( 0, __( 'No CSV or Excel data received. Please upload a valid CSV or Excel file.', 'library-management-system' ) );
				return $response;
			}
			// Parse CSV data
			$csvRows      = str_getcsv( $csvData, "\n" );
			$csvDataArray = array_map( 'str_getcsv', $csvRows );
		}

		if ( $csvType === 'categories' ) {
			$tableName = LIBMNS_Table_Helper_FREE::get_table_name( 'categories' );
		} elseif ( $csvType === 'bookcases' ) {
			$tableName = LIBMNS_Table_Helper_FREE::get_table_name( 'bookcases' );
		} elseif ( $csvType === 'sections' ) {
			$tableName = LIBMNS_Table_Helper_FREE::get_table_name( 'sections' );
		} elseif ( $csvType === 'branches' ) {
			$tableName = LIBMNS_Table_Helper_FREE::get_table_name( 'branches' );
		} elseif ( $csvType === 'books' ) {
			$tableName = LIBMNS_Table_Helper_FREE::get_table_name( 'books' );
		} elseif ( $csvType === 'users' ) {
			$tableName = LIBMNS_Table_Helper_FREE::get_table_name( 'users' );
		} else {
			$tableName = '';
		}

		if ( empty( $tableName ) ) {
			$response = array( 0, __( 'Invalid module type selected.', 'library-management-system' ) );
			return $response;
		}
		if ( empty( $csvDataArray ) ) {
			$response = array( 0, __( 'File is empty or invalid.', 'library-management-system' ) );
			return $response;
		}

		$headerRow = array_shift( $csvDataArray );
		$bom       = "\xEF\xBB\xBF";
		$headerRowNormalized = array_map( function ( $col ) use ( $bom ) {
			$s = (string) $col;
			if ( substr( $s, 0, 3 ) === $bom ) {
				$s = substr( $s, 3 );
			}
			return strtolower( trim( trim( $s, '"' ) ) );
		}, $headerRow );

		$expectedHeaders = isset( $owt7_lms_csv_expected_headers[ $csvType ] ) ? $owt7_lms_csv_expected_headers[ $csvType ] : array();
		$expectedCount   = count( $expectedHeaders );
		$actualCount     = count( $headerRowNormalized );
		$headersMatch    = ( $actualCount === $expectedCount );
		if ( $headersMatch ) {
			foreach ( $expectedHeaders as $i => $expected ) {
				if ( ! isset( $headerRowNormalized[ $i ] ) || $headerRowNormalized[ $i ] !== $expected ) {
					$headersMatch = false;
					break;
				}
			}
		}

		if ( ! $headersMatch ) {
			if ( $actualCount !== $expectedCount ) {
				$response = array(
					0,
					sprintf(
						__( 'Invalid column count for "%s". Expected exactly %d columns (%s) but the file has %d columns. Please use the same columns as the sample file.', 'library-management-system' ),
						$csvType,
						$expectedCount,
						implode( ', ', $expectedHeaders ),
						$actualCount
					)
				);
			} else {
				$response = array(
					0,
					sprintf(
						__( 'Invalid column headers for "%s". Expected columns (in order): %s. Please use the same column headers as the sample CSV/Excel.', 'library-management-system' ),
						$csvType,
						implode( ', ', $expectedHeaders )
					)
				);
			}
			return $response;
		}

		// Require every data row to have exactly the expected number of columns (no more, no less).
		foreach ( $csvDataArray as $rowIndex => $row ) {
			$rowCols = count( array_values( $row ) );
			if ( $rowCols !== $expectedCount ) {
				$response = array(
					0,
					sprintf(
						__( 'Row %d has %d columns but "%s" requires exactly %d columns (%s). Each row must have the same number of columns as the header.', 'library-management-system' ),
						$rowIndex + 2,
						$rowCols,
						$csvType,
						$expectedCount,
						implode( ', ', $expectedHeaders )
					)
				);
				return $response;
			}
		}

		$csvDataArray = array_map( function ( $row ) use ( $expectedCount ) {
			$row = array_values( $row );
			return array_map( function ( $cell ) {
				if ( $cell === null ) {
					return '';
				}
				return (string) $cell;
			}, $row );
		}, $csvDataArray );

		$original_row_count = count( $csvDataArray );
		$current_count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM `{$tableName}`" );
		if ( $current_count >= $owt7_lms_csv_max_rows ) {
			$response = array(
				0,
				sprintf(
					__( 'Free version limit reached (maximum %d items per module). You already have %d items. Please upgrade to add more.', 'library-management-system' ),
					$owt7_lms_csv_max_rows,
					$current_count
				)
			);
			return $response;
		}
		$available_slots = max( 0, $owt7_lms_csv_max_rows - $current_count );
		if ( count( $csvDataArray ) > $available_slots ) {
			$csvDataArray = array_slice( $csvDataArray, 0, $available_slots );
		}
		if ( empty( $csvDataArray ) ) {
			$response = array( 0, __( 'File has no data rows. Add at least one row below the header.', 'library-management-system' ) );
			return $response;
		}

		$tbl_bookcases = LIBMNS_Table_Helper_FREE::get_table_name( 'bookcases' );
		$tbl_categories = LIBMNS_Table_Helper_FREE::get_table_name( 'categories' );
		$tbl_branches   = LIBMNS_Table_Helper_FREE::get_table_name( 'branches' );
		$tbl_sections   = LIBMNS_Table_Helper_FREE::get_table_name( 'sections' );

		$imported_count = count( $csvDataArray );
		foreach ( $csvDataArray as $index => $data ) {
			switch ( $csvType ) {
				case 'categories':
					$wpdb->insert( $tableName, array( 'name' => isset( $data[0] ) ? $data[0] : '' ) );
					break;
				case 'bookcases':
					$wpdb->insert( $tableName, array( 'name' => isset( $data[0] ) ? $data[0] : '' ) );
					break;
				case 'sections':
					$bookcase_name = isset( $data[0] ) ? trim( (string) $data[0] ) : '';
					$section_name  = isset( $data[1] ) ? trim( (string) $data[1] ) : '';
					$bookcase_id   = 0;
					if ( $bookcase_name !== '' ) {
						$bookcase_id = (int) $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$tbl_bookcases} WHERE name = %s LIMIT 1", $bookcase_name ) );
					}
					if ( $bookcase_id > 0 && $section_name !== '' ) {
						$wpdb->insert( $tableName, array( 'bookcase_id' => $bookcase_id, 'name' => $section_name ) );
					}
					break;
				case 'branches':
					$wpdb->insert( $tableName, array( 'name' => isset( $data[0] ) ? $data[0] : '' ) );
					break;
				case 'books':
					$category_name = isset( $data[1] ) ? trim( (string) $data[1] ) : '';
					$bookcase_name = isset( $data[2] ) ? trim( (string) $data[2] ) : '';
					$category_id   = 0;
					$bookcase_id   = 0;
					if ( $category_name !== '' ) {
						$category_id = $this->libmns_find_or_create_import_name_record( $tbl_categories, $category_name );
					}
					if ( $bookcase_name !== '' ) {
						$bookcase_id = $this->libmns_find_or_create_import_name_record( $tbl_bookcases, $bookcase_name );
					}
					$section_id = 0;
					if ( $bookcase_id > 0 ) {
						$section_id = (int) $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$tbl_sections} WHERE bookcase_id = %d ORDER BY id ASC LIMIT 1", $bookcase_id ) );
					}
					$stock_quantity = isset( $data[12] ) ? max( 0, (int) $data[12] ) : 1;
					$book_id        = $this->admin->libmns_generate_book_id_pattern();
					$wpdb->insert( $tableName, array(
						'book_id'              => $book_id,
						'bookcase_id'          => $bookcase_id,
						'bookcase_section_id'  => $section_id,
						'category_id'          => $category_id,
						'name'                 => isset( $data[0] ) ? $data[0] : '',
						'author_name'          => LIBMNS_Admin_FREE::libmns_normalize_comma_separated( isset( $data[3] ) ? $data[3] : '' ),
						'publication_name'     => LIBMNS_Admin_FREE::libmns_normalize_comma_separated( isset( $data[4] ) ? $data[4] : '' ),
						'publication_year'     => isset( $data[5] ) ? $data[5] : '',
						'publication_location' => isset( $data[6] ) ? $data[6] : '',
						'amount'               => isset( $data[7] ) ? $data[7] : '',
						'cover_image'          => isset( $data[8] ) ? esc_url_raw( $data[8] ) : '',
						'isbn'                 => isset( $data[9] ) ? $data[9] : '',
						'book_url'             => '',
						'stock_quantity'       => $stock_quantity,
						'book_language'        => isset( $data[10] ) ? $data[10] : '',
						'book_pages'           => isset( $data[11] ) ? absint( $data[11] ) : 0,
						'description'          => isset( $data[14] ) ? $data[14] : '',
						'status'               => isset( $data[13] ) ? max( 0, (int) $data[13] ) : 1,
					) );
					if ( $wpdb->insert_id > 0 && $stock_quantity > 0 ) {
						$this->admin->libmns_insert_book_copies( $book_id, $stock_quantity, $bookcase_id, $section_id );
					}
					break;
				case 'users':
					$branch_name = isset( $data[4] ) ? trim( (string) $data[4] ) : '';
					$branch_id   = 0;
					if ( $branch_name !== '' ) {
						$branch_id = (int) $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$tbl_branches} WHERE name = %s LIMIT 1", $branch_name ) );
					}
					$u_id = $this->admin->libmns_generate_user_id_pattern();
					$wpdb->insert( $tableName, array(
						'register_from' => isset( $data[0] ) && in_array( $data[0], array( 'web', 'admin' ), true ) ? $data[0] : 'admin',
						'u_id'          => $u_id,
						'name'          => isset( $data[1] ) ? $data[1] : '',
						'email'         => isset( $data[2] ) ? sanitize_email( $data[2] ) : '',
						'gender'        => isset( $data[3] ) && in_array( $data[3], array( 'male', 'female', 'other' ), true ) ? $data[3] : null,
						'branch_id'     => $branch_id,
						'phone_no'      => isset( $data[5] ) ? $data[5] : '',
						'profile_image' => '',
						'address_info'  => isset( $data[6] ) ? $data[6] : '',
						'status'        => isset( $data[7] ) ? max( 0, (int) $data[7] ) : 1,
					) );
					break;
				default:
					break;
			}
		}

		$skipped_count = max( 0, $original_row_count - $imported_count );
		$response = array(
			1,
			$skipped_count > 0
				? sprintf(
					__( 'CSV/Excel data imported successfully. Imported %1$d row(s); skipped %2$d row(s) due to the free version limit of %3$d items per module.', 'library-management-system' ),
					$imported_count,
					$skipped_count,
					$owt7_lms_csv_max_rows
				)
				: __( 'CSV/Excel data imported successfully.', 'library-management-system' )
		);
		return $response;
	}

	private function handle_settings_form() {
		global $wpdb;
		$response = array( 0, __( 'Invalid LMS operation', 'library-management-system' ) );
		unset($_REQUEST['woocommerce-login-nonce']);
					unset($_REQUEST['woocommerce-reset-password-nonce']);
					unset($_REQUEST['_wpnonce']);
					unset($_REQUEST['owt7_lms_nonce']);
					unset($_REQUEST['_wp_http_referer']);
					unset($_REQUEST['action']);
					unset($_REQUEST['param']);

					// Merge with existing so Basic Settings form does not wipe unrelated frontend keys.
					$existing = get_option( 'owt7_lms_public_settings', array() );
					$existing = is_array( $existing ) ? $existing : array();
					$request_sanitized = array();
					foreach ( $_REQUEST as $key => $val ) {
						if ( is_array( $val ) ) {
							$request_sanitized[ sanitize_key( $key ) ] = array_map( 'sanitize_text_field', $val );
						} elseif ( is_scalar( $val ) ) {
							$request_sanitized[ sanitize_key( $key ) ] = sanitize_text_field( wp_unslash( (string) $val ) );
						}
					}
					$merged = array_merge( $existing, $request_sanitized );
					unset( $merged['enable_woocommerce_checkout'] );
					unset( $merged['wp_lms_roles'] );
					update_option("owt7_lms_public_settings", $merged);

					$response = [
						1, 
						__("LMS Settings Updated Successfully", "library-management-system")
					];
		return $response;
	}


	private function handle_checkout_approve_reject() {
		global $wpdb;
		$response = array( 0, __( 'Invalid LMS operation', 'library-management-system' ) );
		if ( ! LIBMNS_Roles_Helper_FREE::libmns_current_user_can( 'owt7_lms_return_book' ) ) {
						$response = [ 0, __( 'You do not have permission to perform this action.', 'library-management-system' ) ];
					} else {

					$data_id = isset( $_REQUEST['data_id'] ) ? sanitize_text_field( wp_unslash( (string) $_REQUEST['data_id'] ) ) : "";
					$type = isset( $_REQUEST['type'] ) ? sanitize_text_field( wp_unslash( (string) $_REQUEST['type'] ) ) : "";
					$module = isset( $_REQUEST['module'] ) ? sanitize_text_field( wp_unslash( (string) $_REQUEST['module'] ) ) : "";

					if($type == "approve"){

						if($module == "borrow"){

							$has_book_borrowed = $data_id !== '' ? $wpdb->get_row(
								$wpdb->prepare(
									"SELECT * FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' ) . " WHERE id = %s",
									$data_id
								)
							) : null;

							if(!empty($has_book_borrowed)){

								$accession_number = $this->admin->libmns_allocate_next_available_accession( $has_book_borrowed->book_id );
								if ( $accession_number === null ) {
									$response = [
										0,
										__("Failed, No available copy to allocate (out of stock).", "library-management-system")
									];
								} elseif ( $this->admin->libmns_manage_books_stock( $has_book_borrowed->book_id, "minus" ) ) {

									$wpdb->update(LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' ), [
										"checkout_status" => LIBMNS_CHECKOUT_APPROVED_BY_ADMIN,
										"accession_number" => $accession_number,
									], [
										"id" => $data_id
									]);

									$response = [
										1,
										__("Successfully, Book Borrow Request Approved", "library-management-system")
									];
								} else {
									$this->admin->libmns_release_accession( $accession_number );
									$response = [
										0,
										__("Failed, Book is Out of Stock.", "library-management-system")
									];
								}
							}else{

								$response = [
									0, 
									__("Invalid borrow request", "library-management-system")
								];
							}
						} elseif ($module == "return"){

							$has_book_returned = $data_id !== '' ? $wpdb->get_row(
								$wpdb->prepare(
									"SELECT * FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'book_return' ) . " WHERE id = %s",
									$data_id
								)
							) : null;

							if(!empty($has_book_returned)){

								$borrow_row = $wpdb->get_row(
									$wpdb->prepare(
										"SELECT id, accession_number FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' ) . " WHERE borrow_id = %s AND status = 1 LIMIT 1",
										$has_book_returned->borrow_id
									)
								);
								$return_accession = ( $borrow_row && isset( $borrow_row->accession_number ) && trim( (string) $borrow_row->accession_number ) !== '' )
									? trim( (string) $borrow_row->accession_number )
									: ( isset( $has_book_returned->accession_number ) && trim( (string) $has_book_returned->accession_number ) !== '' ? trim( (string) $has_book_returned->accession_number ) : '' );

								if($this->admin->libmns_manage_books_stock($has_book_returned->book_id, "plus")){

									if ( $return_accession !== '' ) {
										$this->admin->libmns_release_accession( $return_accession );
									}

									// Update Borrow Data (by borrow_id, not return id)
									if ( $borrow_row && ! empty( $borrow_row->id ) ) {
										$wpdb->update(LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' ), [
											"status" => 0
										], [
											"id" => $borrow_row->id
										]);
									}

									$borrow_id = $has_book_returned->borrow_id;

									// Update Return Status
									if($has_book_returned->has_fine_status){

										$wpdb->update(LIBMNS_Table_Helper_FREE::get_table_name( 'book_return' ), [
											"return_status" => LIBMNS_RETURN_APPROVED_BY_ADMIN,
											"status" => 1
										], [
											"borrow_id" => $borrow_id
										]);
									}else{

										$wpdb->update(LIBMNS_Table_Helper_FREE::get_table_name( 'book_return' ), [
											"return_status" => LIBMNS_RETURN_APPROVED_BY_ADMIN,
											"status" => 0
										], [
											"borrow_id" => $borrow_id
										]);
									}
		
									$response = [
										1, 
										__("Successfully, Book return Request Approved", "library-management-system")
									];
								}
							}else{

								$response = [
									0, 
									__("Invalid return request", "library-management-system")
								];
							}
						}
					} elseif($type == "reject"){

						if($module == "borrow"){

							$has_book_borrowed = $data_id !== '' ? $wpdb->get_row(
								$wpdb->prepare(
									"SELECT * FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' ) . " WHERE id = %s",
									$data_id
								)
							) : null;

							if(!empty($has_book_borrowed)){

								$wpdb->update(LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' ), [
									"checkout_status" => 4, // Admin Rejected
									"status" => 0
								], [
									"id" => $data_id
								]);
		
								$response = [
									1, 
									__("Book Borrow Requested Rejected.", "library-management-system")
								];
							}
						} elseif ($module == "return"){

							$has_book_returned = $data_id !== '' ? $wpdb->get_row(
								$wpdb->prepare(
									"SELECT * FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'book_return' ) . " WHERE id = %s",
									$data_id
								)
							) : null;

							if(!empty($has_book_returned)){

								$wpdb->update(LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' ), [
									"status" => 1
								], [
									"borrow_id" => $has_book_returned->borrow_id
								]);

								$wpdb->update(LIBMNS_Table_Helper_FREE::get_table_name( 'book_return' ), [
									"return_status" => 4, // Admin Rejected
									"status" => 0
								], [
									"id" => $data_id
								]);
		
								$response = [
									1, 
									__("Book Return Requested Rejected.", "library-management-system")
								];
							}
						}
					} else{

						$response = [
							0, 
							__("Invalid LMS Operation", "library-management-system")
						];
					}
					}
		return $response;
	}

	private function handle_save_public_view_settings() {
		global $wpdb;
		$response = array( 0, __( 'Invalid LMS operation', 'library-management-system' ) );
		$cards_per_row     = isset( $_REQUEST['cards_per_row'] ) ? sanitize_text_field( $_REQUEST['cards_per_row'] ) : '3';
					$heading_font_size = isset( $_REQUEST['heading_font_size'] ) ? sanitize_text_field( $_REQUEST['heading_font_size'] ) : '18px';
					$body_font_size    = isset( $_REQUEST['body_font_size'] ) ? sanitize_text_field( $_REQUEST['body_font_size'] ) : '14px';
					$view_btn_placement = isset( $_REQUEST['view_btn_placement'] ) ? sanitize_text_field( $_REQUEST['view_btn_placement'] ) : 'right';
					$card_bg_color     = isset( $_REQUEST['card_bg_color'] ) ? sanitize_hex_color( wp_unslash( (string) $_REQUEST['card_bg_color'] ) ) : '#ffffff';
					if ( $card_bg_color === '' ) {
						$card_bg_color = '#ffffff';
					}
					$view_btn_padding  = isset( $_REQUEST['view_btn_padding'] ) ? sanitize_text_field( wp_unslash( (string) $_REQUEST['view_btn_padding'] ) ) : '4px 9px';
					$view_btn_font_size = isset( $_REQUEST['view_btn_font_size'] ) ? sanitize_text_field( wp_unslash( (string) $_REQUEST['view_btn_font_size'] ) ) : '12px';
					$view_btn_color     = isset( $_REQUEST['view_btn_color'] ) ? sanitize_hex_color( wp_unslash( (string) $_REQUEST['view_btn_color'] ) ) : '#1d2065';
					if ( $view_btn_color === '' ) {
						$view_btn_color = '#1d2065';
					}
					$checkout_btn_color = isset( $_REQUEST['checkout_btn_color'] ) ? sanitize_hex_color( wp_unslash( (string) $_REQUEST['checkout_btn_color'] ) ) : '#0d9488';
					if ( $checkout_btn_color === '' ) {
						$checkout_btn_color = '#0d9488';
					}
					$allowed_placements = array( 'left', 'right', 'center' );
					if ( ! in_array( $view_btn_placement, $allowed_placements, true ) ) {
						$view_btn_placement = 'right';
					}
					$n = absint( $cards_per_row );
					if ( $n < 1 || $n > 6 ) {
						$n = 3;
					}
					$cards_per_row = (string) $n;
					$settings = array(
						'cards_per_row'       => $cards_per_row,
						'heading_font_size'   => $heading_font_size,
						'body_font_size'      => $body_font_size,
						'view_btn_placement'   => $view_btn_placement,
						'card_bg_color'      => $card_bg_color,
						'view_btn_padding'    => $view_btn_padding,
						'view_btn_font_size'  => $view_btn_font_size,
						'view_btn_color'      => $view_btn_color,
						'checkout_btn_color'  => $checkout_btn_color,
					);
					update_option( 'owt7_lms_public_view_settings', $settings );
					$response = array( 1, __( 'Public view settings saved successfully.', 'library-management-system' ) );
		return $response;
	}

	private function handle_save_library_user_portal_settings() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return array( 0, __( 'You do not have permission to save these settings.', 'library-management-system' ) );
		}
		$defaults = LIBMNS_Admin_FREE::libmns_get_library_user_portal_settings_defaults();
		$allowed_keys = array(
			'library_user_books_per_row',
			'library_user_card_design',
			'library_user_enable_category_filter',
			'library_user_enable_author_filter',
			'library_user_books_per_page',
		);
		$checkbox_keys = array(
			'library_user_enable_category_filter',
			'library_user_enable_author_filter',
		);
		$to_save = array();
		foreach ( $allowed_keys as $key ) {
			if ( in_array( $key, $checkbox_keys, true ) ) {
				$val = isset( $_REQUEST[ $key ] ) ? $_REQUEST[ $key ] : 0;
				$to_save[ $key ] = ( $val === '1' || $val === 1 ) ? 1 : 0;
				continue;
			}
			if ( isset( $_REQUEST[ $key ] ) ) {
				$val = $_REQUEST[ $key ];
				if ( in_array( $key, array( 'library_user_books_per_row', 'library_user_books_per_page', 'library_user_checkout_days' ), true ) ) {
					$to_save[ $key ] = max( 1, absint( $val ) );
				} elseif ( $key === 'library_user_card_design' ) {
					$to_save[ $key ] = sanitize_text_field( $val );
				}
			}
		}
		$merged = array_merge( $defaults, $to_save );
		$merged['library_user_enable_search'] = 0;
		$merged['library_user_self_checkout'] = 0;
		$merged['library_user_self_return'] = 0;
		$merged['library_user_checkout_more_than_one'] = 0;
		$merged['library_user_checkout_days'] = LIBMNS_DEFAULT_BORROW_DAYS;
		$merged['library_user_enable_list_filter_author'] = 0;
		$merged['library_user_enable_list_filter_category'] = 0;
		$merged['library_user_self_checkout_roles'] = array( 'owt7_library_user' );
		update_option( 'owt7_lms_library_user_portal_settings', $merged );

		// Keep the public library page aligned with the shared catalogue filter controls.
		$public_settings = get_option( 'owt7_lms_public_settings', array() );
		$public_settings = is_array( $public_settings ) ? $public_settings : array();
		$public_settings['enable_category_filter'] = ! empty( $merged['library_user_enable_category_filter'] ) ? 1 : 0;
		$public_settings['enable_author_filter']   = ! empty( $merged['library_user_enable_author_filter'] ) ? 1 : 0;
		$public_settings['enable_search_filter']   = 0;
		update_option( 'owt7_lms_public_settings', $public_settings );

		return array( 1, __( 'Library User portal settings saved.', 'library-management-system' ) );
	}

	private function handle_filter_library_user_catalogue() {
		if ( ! current_user_can( 'access_owt7_library_user_portal' ) ) {
			return array( 0, __( 'You do not have permission to access the catalogue.', 'library-management-system' ) );
		}

		$catalogue_params = $this->admin->libmns_get_library_user_catalogue_params( $_REQUEST );
		$html             = $this->admin->libmns_render_library_user_catalogue_results( $catalogue_params );
		$query_args       = array( 'page' => 'owt7_library_books_catalogue' );

		if ( ! empty( $catalogue_params['filter_cat'] ) ) {
			$query_args['cat'] = (int) $catalogue_params['filter_cat'];
		}
		if ( ! empty( $catalogue_params['filter_author'] ) ) {
			$query_args['author'] = $catalogue_params['filter_author'];
		}
		if ( ! empty( $catalogue_params['filter_search'] ) ) {
			$query_args['search'] = $catalogue_params['filter_search'];
		}
		if ( ! empty( $catalogue_params['current_page'] ) && (int) $catalogue_params['current_page'] > 1 ) {
			$query_args['grid'] = (int) $catalogue_params['current_page'];
		}

		return array(
			1,
			__( 'Catalogue updated.', 'library-management-system' ),
			array(
				'html' => $html,
				'url'  => add_query_arg( $query_args, admin_url( 'admin.php' ) ),
			)
		);
	}

	private function handle_get_book_copies() {
		if ( ! LIBMNS_Roles_Helper_FREE::libmns_current_user_can( 'owt7_lms_list_books' ) ) {
			return array( 0, __( 'You do not have permission to view book copies.', 'library-management-system' ) );
		}
		$book_id = isset( $_REQUEST['book_id'] ) ? absint( $_REQUEST['book_id'] ) : 0;
		if ( ! $book_id ) {
			return array( 0, __( 'Invalid book.', 'library-management-system' ) );
		}
		$data = $this->admin->libmns_get_book_copies_data( $book_id );
		return array( 1, __( 'OK', 'library-management-system' ), $data );
	}

	private function handle_get_receipt_data() {
		global $wpdb;

		$return_id = isset( $_REQUEST['return_id'] ) ? absint( $_REQUEST['return_id'] ) : 0;
		if ( $return_id < 1 ) {
			return array( 0, __( 'Invalid return ID', 'library-management-system' ) );
		}

		$tbl_return   = LIBMNS_Table_Helper_FREE::get_table_name( 'book_return' );
		$tbl_borrow   = LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' );
		$tbl_cat      = LIBMNS_Table_Helper_FREE::get_table_name( 'categories' );
		$tbl_books    = LIBMNS_Table_Helper_FREE::get_table_name( 'books' );
		$tbl_branches = LIBMNS_Table_Helper_FREE::get_table_name( 'branches' );
		$tbl_users    = LIBMNS_Table_Helper_FREE::get_table_name( 'users' );
		$tbl_fine     = LIBMNS_Table_Helper_FREE::get_table_name( 'book_late_fine' );

		$sql = "SELECT rt.id, rt.return_id, rt.borrow_id, rt.u_id, rt.wp_user, rt.return_status,
			COALESCE(NULLIF(TRIM(rt.accession_number), ''), (SELECT br.accession_number FROM {$tbl_borrow} br WHERE br.borrow_id = rt.borrow_id LIMIT 1)) AS accession_number,
			rt.return_condition, rt.return_remark, rt.is_self_return, rt.status, rt.created_at,
			(SELECT cat.name   FROM {$tbl_cat}      cat  WHERE cat.id  = rt.category_id LIMIT 1) AS category_name,
			(SELECT bk.name    FROM {$tbl_books}    bk   WHERE bk.id   = rt.book_id     LIMIT 1) AS book_name,
			(SELECT bk.book_id FROM {$tbl_books}    bk   WHERE bk.id   = rt.book_id     LIMIT 1) AS book_book_id,
			(SELECT bk.author_name       FROM {$tbl_books} bk WHERE bk.id = rt.book_id LIMIT 1) AS book_author,
			(SELECT bk.publication_name  FROM {$tbl_books} bk WHERE bk.id = rt.book_id LIMIT 1) AS book_publisher,
			(SELECT bk.publication_year  FROM {$tbl_books} bk WHERE bk.id = rt.book_id LIMIT 1) AS book_pub_year,
			(SELECT bk.isbn              FROM {$tbl_books} bk WHERE bk.id = rt.book_id LIMIT 1) AS book_isbn,
			(SELECT br.name    FROM {$tbl_branches} br   WHERE br.id   = rt.branch_id  LIMIT 1) AS branch_name,
			(SELECT u.name     FROM {$tbl_users}    u    WHERE u.id    = rt.u_id        LIMIT 1) AS user_name,
			(SELECT u.email    FROM {$tbl_users}    u    WHERE u.id    = rt.u_id        LIMIT 1) AS user_email,
			(SELECT u.phone_no FROM {$tbl_users}    u    WHERE u.id    = rt.u_id        LIMIT 1) AS user_phone,
			(SELECT u.u_id     FROM {$tbl_users}    u    WHERE u.id    = rt.u_id        LIMIT 1) AS user_u_id,
			(SELECT bw.borrows_days FROM {$tbl_borrow} bw WHERE bw.borrow_id = rt.borrow_id LIMIT 1) AS total_days,
			(SELECT bw.created_at   FROM {$tbl_borrow} bw WHERE bw.borrow_id = rt.borrow_id LIMIT 1) AS issued_on,
			(SELECT fine.has_paid    FROM {$tbl_fine} fine WHERE fine.return_id = rt.id LIMIT 1) AS has_paid,
			(SELECT fine.fine_amount FROM {$tbl_fine} fine WHERE fine.return_id = rt.id LIMIT 1) AS fine_amount,
			(SELECT fine.extra_days  FROM {$tbl_fine} fine WHERE fine.return_id = rt.id LIMIT 1) AS extra_days,
			(SELECT fine.fine_type   FROM {$tbl_fine} fine WHERE fine.return_id = rt.id LIMIT 1) AS fine_type
			FROM {$tbl_return} rt
			WHERE rt.id = %d LIMIT 1";

		$record = $wpdb->get_row( $wpdb->prepare( $sql, $return_id ) );
		if ( empty( $record ) ) {
			return array( 0, __( 'Return record not found', 'library-management-system' ) );
		}

		// Borrower display name — WP users use WordPress display_name
		$borrower_name  = $record->user_name;
		$borrower_email = $record->user_email;
		$borrower_phone = $record->user_phone;
		if ( ! empty( $record->wp_user ) && function_exists( 'get_userdata' ) ) {
			$wp_user_data = get_userdata( $record->u_id );
			if ( $wp_user_data ) {
				$borrower_name  = $wp_user_data->display_name;
				$borrower_email = $wp_user_data->user_email;
			}
		}

		// Issuer = current logged-in user (the admin/librarian generating the receipt)
		$current_user = wp_get_current_user();
		$issuer_name  = $current_user->display_name;
		$issuer_lms_id = '';
		$issuer_lms_row = $wpdb->get_row( $wpdb->prepare(
			"SELECT u_id FROM {$tbl_users} WHERE wp_user_id = %d AND status = 1 LIMIT 1",
			(int) $current_user->ID
		) );
		if ( $issuer_lms_row ) {
			$issuer_lms_id = $issuer_lms_row->u_id;
		}
		if ( $issuer_lms_id === '' ) {
			$issuer_lms_id = '#WP' . $current_user->ID;
		}

		$currency = get_option( 'owt7_lms_currency', '' );
		$site_name = get_bloginfo( 'name' );

		// Check if receipt file already exists on disk
		$upload_dir       = wp_upload_dir();
		$receipts_dir     = $upload_dir['basedir'] . '/library-management-system_uploads/receipts';
		$receipt_filename = 'receipt_' . sanitize_file_name( $record->return_id ) . '.pdf';
		$receipt_path     = $receipts_dir . '/' . $receipt_filename;
		$receipt_url      = '';
		if ( file_exists( $receipt_path ) ) {
			$receipt_url = $upload_dir['baseurl'] . '/library-management-system_uploads/receipts/' . $receipt_filename;
		}

		$return_condition_labels = array(
			'normal_return' => __( 'Normal Return', 'library-management-system' ),
			'lost_book'     => __( 'Lost Book', 'library-management-system' ),
			'late_return'   => __( 'Late Return', 'library-management-system' ),
		);
		$fine_paid_labels = array(
			1 => __( 'Not Paid', 'library-management-system' ),
			2 => __( 'Paid', 'library-management-system' ),
		);
		$fine_amount = is_numeric( $record->fine_amount ) ? (float) $record->fine_amount : 0;
		$extra_days = is_numeric( $record->extra_days ) ? (int) $record->extra_days : 0;
		$fine_type = $fine_amount > 0 && ! empty( $record->fine_type )
			? ucfirst( str_replace( '_', ' ', $record->fine_type ) )
			: __( 'No fine', 'library-management-system' );
		$fine_status = $fine_amount > 0
			? ( isset( $fine_paid_labels[ (int) $record->has_paid ] ) ? $fine_paid_labels[ (int) $record->has_paid ] : '—' )
			: __( 'No fine', 'library-management-system' );

		return array( 1, '', array(
			'return_id'        => $record->return_id,
			'borrow_id'        => $record->borrow_id,
			'book_id'          => $record->book_book_id,
			'book_name'        => $record->book_name,
			'book_category'    => $record->category_name,
			'book_accession'   => ! empty( $record->accession_number ) ? $record->accession_number : '—',
			'book_author'      => ! empty( $record->book_author )    ? $record->book_author    : '—',
			'book_publisher'   => ! empty( $record->book_publisher ) ? $record->book_publisher : '—',
			'book_pub_year'    => ! empty( $record->book_pub_year )  ? $record->book_pub_year  : '—',
			'book_isbn'        => ! empty( $record->book_isbn )      ? $record->book_isbn      : '—',
			'borrower_user_id' => ! empty( $record->user_u_id ) ? $record->user_u_id : $record->u_id,
			'borrower_name'    => $borrower_name,
			'borrower_email'   => $borrower_email,
			'borrower_phone'   => $borrower_phone ? $borrower_phone : '—',
			'borrower_branch'  => $record->branch_name,
			'issuer_name'      => $issuer_name,
			'issuer_lms_id'    => $issuer_lms_id,
			'issued_on'        => ! empty( $record->issued_on )   ? date( 'Y-m-d', strtotime( $record->issued_on ) )   : '—',
			'return_date'      => ! empty( $record->created_at )  ? date( 'Y-m-d', strtotime( $record->created_at ) )  : '—',
			'total_days'       => $record->total_days ? $record->total_days : '—',
			'extra_days'       => $extra_days,
			'fine_amount'      => $fine_amount,
			'fine_type'        => $fine_type,
			'return_condition' => isset( $return_condition_labels[ $record->return_condition ] ) ? $return_condition_labels[ $record->return_condition ] : ( $record->return_condition ? $record->return_condition : '—' ),
			'fine_status'      => $fine_status,
			'currency'         => $currency,
			'site_name'        => $site_name,
			'receipt_url'      => $receipt_url,
			'generated_on'     => date( 'Y-m-d H:i:s' ),
		) );
	}

	private function handle_save_receipt() {
		$return_id = isset( $_REQUEST['return_id'] ) ? sanitize_text_field( wp_unslash( (string) $_REQUEST['return_id'] ) ) : '';
		$pdf_data  = isset( $_REQUEST['pdf_data'] ) ? wp_unslash( (string) $_REQUEST['pdf_data'] ) : '';

		if ( $return_id === '' || $pdf_data === '' ) {
			return array( 0, __( 'Invalid receipt data', 'library-management-system' ) );
		}

		// Strip data-URI prefix if the client sent it (safety net)
		$pdf_data = preg_replace( '/^data:[^;]+;base64,/', '', trim( $pdf_data ) );

		// PHP's URL form decoder turns '+' into space; restore them before decoding.
		$pdf_data = str_replace( ' ', '+', $pdf_data );

		// Strip any stray whitespace / newlines that may have crept in
		$pdf_data = preg_replace( '/\s+/', '', $pdf_data );

		$pdf_binary = base64_decode( $pdf_data );
		if ( $pdf_binary === false || strlen( $pdf_binary ) < 4 ) {
			return array( 0, __( 'Invalid PDF data received', 'library-management-system' ) );
		}

		$upload_dir       = wp_upload_dir();
		$receipts_dir     = $upload_dir['basedir'] . '/library-management-system_uploads/receipts';
		if ( ! file_exists( $receipts_dir ) ) {
			wp_mkdir_p( $receipts_dir );
		}

		$receipt_filename = 'receipt_' . sanitize_file_name( $return_id ) . '.pdf';
		$receipt_path     = $receipts_dir . '/' . $receipt_filename;

		$result = file_put_contents( $receipt_path, $pdf_binary );
		if ( $result === false ) {
			return array( 0, __( 'Failed to save receipt file', 'library-management-system' ) );
		}

		$receipt_url = $upload_dir['baseurl'] . '/library-management-system_uploads/receipts/' . $receipt_filename;
		return array( 1, __( 'Receipt saved successfully', 'library-management-system' ), array(
			'receipt_url' => $receipt_url,
			'filename'    => $receipt_filename,
		) );
	}
}
