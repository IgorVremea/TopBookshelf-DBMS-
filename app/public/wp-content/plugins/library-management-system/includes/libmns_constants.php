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

if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( ! function_exists( 'libmns_decode_obfuscated_int' ) ) {

	function libmns_decode_obfuscated_int( $payload ) {
		$decoded = base64_decode( strrev( (string) $payload ), true );
		return false !== $decoded ? (int) $decoded : 0;
	}
}

define( 'LIBMNS_VERSION', '3.5.5' );
define( 'LIBMNS_PREFIX', 'owt7' );
define( 'LIBMNS_TEXT_DOMAIN', 'library-management-system' );
define( 'LIBMNS_PLUGIN_SLUG', 'library-management-system' );
define( 'LIBMNS_PLUGIN_DIR_PATH', dirname( dirname( __FILE__ ) ) . '/' );
define( 'LIBMNS_PLUGIN_FILE', dirname( dirname( __FILE__ ) ) . '/' . basename( dirname( dirname( __FILE__ ) ) ) . '.php' );
define( 'LIBMNS_PLUGIN_BASENAME', plugin_basename( LIBMNS_PLUGIN_FILE ) );
define( 'LIBMNS_PLUGIN_URL', plugin_dir_url( LIBMNS_PLUGIN_FILE ) );

define( 'LIBMNS_FREE_VERSION_LIMIT', libmns_decode_obfuscated_int( '=AzM' ) );

// Basic
define( 'LIBMNS_DEFAULT_SHOW_BOOKS', 6 );
define( 'LIBMNS_DEFAULT_PAGE_NUMBER', 1 );
define( 'LIBMNS_DEFAULT_BORROW_DAYS', 30 );
define( 'LIBMNS_DEFAULT_BRANCH_ID', 1 );

define( 'LIBMNS_THEME_PRIMARY_DEFAULT', '#1d2065' );
define( 'LIBMNS_THEME_ACCENT_DEFAULT', '#f59e0b' );
define( 'LIBMNS_THEME_ACTION_CLONE_DEFAULT', '#6366f1' );
define( 'LIBMNS_THEME_ACTION_VIEW_DEFAULT', '#2563eb' );
define( 'LIBMNS_THEME_ACTION_EDIT_DEFAULT', '#059669' );
define( 'LIBMNS_THEME_ACTION_BOOK_COPIES_DEFAULT', '#0d9488' );
define( 'LIBMNS_THEME_ACTION_DELETE_DEFAULT', '#ef4444' );
define( 'LIBMNS_THEME_ACTION_CHECKOUT_DEFAULT', '#059669' );
define( 'LIBMNS_THEME_ACTION_VIEW_BOOK_DEFAULT', '#2563eb' );
define( 'LIBMNS_THEME_ACTION_RETURN_DEFAULT', '#0d9488' );

// Checkout
define( 'LIBMNS_CHECKOUT_APPROVED_BY_ADMIN', 1 );
define( 'LIBMNS_CHECKOUT_SELF_APPROVED', 2 );
define( 'LIBMNS_DEFAULT_CHECKOUT', 3 );
define( 'LIBMNS_CHECKOUT_REJECTED', 4 );
define( 'LIBMNS_CHECKOUT_NO_STATUS', 5 );

// Return
define( 'LIBMNS_RETURN_APPROVED_BY_ADMIN', 1 );
define( 'LIBMNS_RETURN_SELF_APPROVED', 2 );
define( 'LIBMNS_DEFAULT_RETURN', 3 );
define( 'LIBMNS_RETURN_REJECTED', 4 );
define( 'LIBMNS_RETURN_NO_STATUS', 5 );

// Return condition (physical/condition status of returned book)
define( 'LIBMNS_RETURN_CONDITION_NORMAL', 'normal_return' );
define( 'LIBMNS_RETURN_CONDITION_DAMAGED', 'damaged_book' );
define( 'LIBMNS_RETURN_CONDITION_LOST', 'lost_book' );
define( 'LIBMNS_RETURN_CONDITION_MISSING_PAGES', 'missing_pages' );
define( 'LIBMNS_RETURN_CONDITION_LATE', 'late_return' );

// Prefix
define( 'LIBMNS_BOOK_PREFIX', 'LMSBK' );
define( 'LIBMNS_USER_PREFIX', 'LMSUS' );
define( 'LIBMNS_BRANCH_PREFIX', 'LMSBR' );
define( 'LIBMNS_BOOKCASE_PREFIX', 'LMSBC' );
define( 'LIBMNS_SECTION_PREFIX', 'LMSSE' );
define( 'LIBMNS_CATEGORY_PREFIX', 'LMSCA' );
define( 'LIBMNS_BOOK_BORROW_PREFIX', 'LMSBB' );
define( 'LIBMNS_BOOK_RETURN_PREFIX', 'LMSBR' );
define( 'LIBMNS_BOOK_LATE_FINE_PREFIX', 'LMSLF' );
define( 'LIBMNS_DATA_BACKUPS_PREFIX', 'LMSDB' );