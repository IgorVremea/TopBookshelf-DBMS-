<?php
/**
 * @link       https://onlinewebtutorblog.com
 * @since      3.5
 * @package    Library_Management_System
 * @subpackage Library_Management_System/admin/views/library_user/templates
 * @copyright  Copyright (c) 2026, Online Web Tutor
 * @license    GPL-2.0+ https://www.gnu.org/licenses/gpl-2.0.html
 * @author     Online Web Tutor
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$checkout_overview = isset( $params['checkout_overview'] ) && is_array( $params['checkout_overview'] ) ? $params['checkout_overview'] : array();
$checkout_days     = isset( $checkout_overview['days'] ) ? (int) $checkout_overview['days'] : LIBMNS_DEFAULT_BORROW_DAYS;
$request_date      = isset( $checkout_overview['request_date'] ) ? (string) $checkout_overview['request_date'] : '';
$expected_date     = isset( $checkout_overview['expected_return_date'] ) ? (string) $checkout_overview['expected_return_date'] : '';
$currency_code     = strtoupper( (string) get_option( 'owt7_lms_currency', '' ) );
$currency_symbols  = array(
	'INR' => '₹',
	'USD' => '$',
	'EUR' => '€',
	'GBP' => '£',
	'AUD' => 'A$',
	'CAD' => 'C$',
	'JPY' => '¥',
	'CNY' => '¥',
	'AED' => 'د.إ',
);
$currency_symbol = isset( $currency_symbols[ $currency_code ] ) ? $currency_symbols[ $currency_code ] : $currency_code;
?>
<div id="owt7_lms_mdl_user_checkout_overview" class="modal owt7-lms-return-status-modal owt7-lms-user-checkout-overview-modal" style="display: none;" aria-hidden="true" data-currency-symbol="<?php echo esc_attr( $currency_symbol ); ?>">
	<div class="modal-content owt7-lms-user-checkout-overview-content">
		<span class="close owt7-lms-close-user-checkout-overview" aria-label="<?php esc_attr_e( 'Close', 'library-management-system' ); ?>">&times;</span>
		<h2><?php esc_html_e( 'Checkout Overview', 'library-management-system' ); ?></h2>
		<p class="return-section-desc"><?php esc_html_e( 'Please review the book and checkout details before confirming your request.', 'library-management-system' ); ?></p>

		<div class="owt7-lms-checkout-overview-section">
			<h3><?php esc_html_e( 'Book details', 'library-management-system' ); ?></h3>
			<dl class="owt7-lms-checkout-overview-list">
				<dt><?php esc_html_e( 'Title', 'library-management-system' ); ?></dt>
				<dd id="owt7_lms_checkout_overview_book_title">-</dd>
				<dt><?php esc_html_e( 'Author', 'library-management-system' ); ?></dt>
				<dd id="owt7_lms_checkout_overview_book_author">-</dd>
				<dt><?php esc_html_e( 'Category', 'library-management-system' ); ?></dt>
				<dd id="owt7_lms_checkout_overview_book_category">-</dd>
				<dt><?php esc_html_e( 'Publication', 'library-management-system' ); ?></dt>
				<dd id="owt7_lms_checkout_overview_book_publication">-</dd>
				<dt><?php esc_html_e( 'Publication year', 'library-management-system' ); ?></dt>
				<dd id="owt7_lms_checkout_overview_book_year">-</dd>
				<dt><?php esc_html_e( 'ISBN', 'library-management-system' ); ?></dt>
				<dd id="owt7_lms_checkout_overview_book_isbn">-</dd>
				<dt><?php esc_html_e( 'Cost', 'library-management-system' ); ?></dt>
				<dd id="owt7_lms_checkout_overview_book_amount">-</dd>
			</dl>
		</div>

		<div class="owt7-lms-checkout-overview-section">
			<h3><?php esc_html_e( 'Checkout details', 'library-management-system' ); ?></h3>
			<dl class="owt7-lms-checkout-overview-list">
				<dt><?php esc_html_e( 'Days', 'library-management-system' ); ?></dt>
				<dd id="owt7_lms_checkout_overview_days"><?php echo esc_html( $checkout_days ); ?></dd>
				<dt><?php esc_html_e( 'Request date', 'library-management-system' ); ?></dt>
				<dd id="owt7_lms_checkout_overview_request_date"><?php echo esc_html( $request_date ); ?></dd>
				<dt><?php esc_html_e( 'Expected return date', 'library-management-system' ); ?></dt>
				<dd id="owt7_lms_checkout_overview_expected_date"><?php echo esc_html( $expected_date ); ?></dd>
			</dl>
		</div>

		<div class="form-row buttons-group owt7-lms-checkout-overview-actions">
			<button type="button" class="btn submit-save-btn" id="owt7_lms_user_checkout_overview_confirm"><?php esc_html_e( 'Confirm Checkout', 'library-management-system' ); ?></button>
		</div>
	</div>
</div>
