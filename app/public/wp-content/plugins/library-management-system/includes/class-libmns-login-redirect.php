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
class LIBMNS_Login_Redirect_FREE {

	private static $lms_staff_roles = array();
	
	private static function get_lms_dashboard_url() {
		return admin_url( 'admin.php?page=library_management_system' );
	}
	
	private static function user_has_lms_staff_role( $user ) {
		if ( ! $user || empty( $user->roles ) ) {
			return false;
		}
		return (bool) array_intersect( self::$lms_staff_roles, $user->roles );
	}
	
	public function my_plugin_login_redirect( $redirect_to, $requested_redirect_to, $user ) {
	
		if ( is_wp_error( $user ) || ! $user ) {
			return $redirect_to;
		}
	
		// Respect manual redirect
		if ( ! empty( $requested_redirect_to ) ) {
			return $requested_redirect_to;
		}
	
		// Admin → dashboard
		if ( in_array( 'administrator', $user->roles, true ) ) {
			return admin_url();
		}
	
		// Staff → LMS admin
		if ( self::user_has_lms_staff_role( $user ) ) {
			return self::get_lms_dashboard_url();
		}
	
		// Students → front page
		return site_url( '/wp-library-books/' );
	}
	
	/* ---------------- WOOCOMMERCE LOGIN REDIRECT ---------------- */
	
	public function my_plugin_wc_login_redirect( $redirect, $user ) {
	
		if ( ! $user ) return $redirect;
	
		if ( in_array( 'administrator', $user->roles, true ) ) {
			return admin_url();
		}
	
		if ( self::user_has_lms_staff_role( $user ) ) {
			return self::get_lms_dashboard_url();
		}
	
		return site_url( '/wp-library-books/' );
	}
	
	public function my_plugin_block_myaccount_redirect() {
	
		// Stop redirect loops
		if ( is_admin() || wp_doing_ajax() || wp_doing_cron() ) {
			return;
		}
	
		if ( ! function_exists( 'is_account_page' ) ) {
			return;
		}
	
		if ( ! is_user_logged_in() || ! is_account_page() ) {
			return;
		}
	
		$user = wp_get_current_user();
	
		// Admin allowed
		if ( in_array( 'administrator', $user->roles, true ) ) {
			return;
		}
	
		// Prevent infinite loop
		$current_url = home_url( add_query_arg( array(), $_SERVER['REQUEST_URI'] ) );
	
		$lms_url = self::get_lms_dashboard_url();
		$student_url = site_url( '/wp-library-books/' );
	
		if ( strpos( $current_url, $lms_url ) !== false || strpos( $current_url, $student_url ) !== false ) {
			return;
		}
	
		if ( self::user_has_lms_staff_role( $user ) ) {
			wp_safe_redirect( $lms_url );
		} else {
			wp_safe_redirect( $student_url );
		}
		exit;
	}
	}
	