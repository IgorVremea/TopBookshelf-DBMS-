<?php
/**
 * @link       https://onlinewebtutorblog.com
 * @since      3.5
 * @package    Library_Management_System
 * @subpackage Library_Management_System/admin/views/transactions/templates
 * @copyright  Copyright (c) 2026, Online Web Tutor
 * @license    GPL-2.0+ https://www.gnu.org/licenses/gpl-2.0.html
 * @author     Online Web Tutor
 */
if ( empty( $return ) || empty( $part ) ) {
	return;
}
$return_condition_labels = array(
	'normal_return'   => __( 'Normal return', 'library-management-system' ),
	'lost_book'       => __( 'Lost book', 'library-management-system' ),
	'late_return'     => __( 'Late return', 'library-management-system' ),
);
$return_condition_display = isset( $return->return_condition ) && isset( $return_condition_labels[ $return->return_condition ] ) ? $return_condition_labels[ $return->return_condition ] : ( ! empty( $return->return_condition ) ? esc_html( $return->return_condition ) : '—' );
$returned_date = '';
$ret_status = (int) $return->return_status;
if ( ! $return->status && $ret_status !== (int) LIBMNS_RETURN_REJECTED ) {
	$returned_date = date( 'Y-m-d', strtotime( $return->created_at ) );
} elseif ( $return->status && ( ! isset( $return->has_paid ) || (int) $return->has_paid === 1 ) && ! in_array( $ret_status, array( (int) LIBMNS_DEFAULT_RETURN, (int) LIBMNS_RETURN_REJECTED ), true ) ) {
	$returned_date = date( 'Y-m-d', strtotime( $return->created_at ) );
} elseif ( $return->status && ! in_array( $ret_status, array( (int) LIBMNS_DEFAULT_RETURN, (int) LIBMNS_RETURN_REJECTED ), true ) ) {
	$returned_date = date( 'Y-m-d', strtotime( $return->created_at ) );
}
$currency = isset( $currency ) ? $currency : get_option( 'owt7_lms_currency', '' );

if ( $part === 'book' ) {
	?>
	<div class="lms-info-block">
		<div class="lms-info-item"><span class="lms-info-label"><?php _e( 'Book ID', 'library-management-system' ); ?></span><span class="lms-info-value"><?php echo esc_html( ! empty( $return->book_book_id ) ? $return->book_book_id : '' ); ?></span></div>
		<div class="lms-info-item"><span class="lms-info-label"><?php _e( 'Category', 'library-management-system' ); ?></span><span class="lms-info-value"><?php echo esc_html( $return->category_name ); ?></span></div>
		<div class="lms-info-item"><span class="lms-info-label"><?php _e( 'Name', 'library-management-system' ); ?></span><span class="lms-info-value"><?php echo esc_html( $return->book_name ); ?></span></div>
		<div class="lms-info-item"><span class="lms-info-label"><?php _e( 'Acc No.', 'library-management-system' ); ?></span><span class="lms-info-value"><?php echo esc_html( ! empty( $return->accession_number ) ? $return->accession_number : '—' ); ?></span></div>
	</div>
	<?php
} elseif ( $part === 'user' ) {
	$user_name = $return->user_name;
	$user_extra = '';
	if ( ! empty( $return->wp_user ) && function_exists( 'get_userdata' ) ) {
		$user_data = get_userdata( $return->u_id );
		$user_name = $user_data ? $user_data->display_name : $return->user_name;
		$user_extra = $user_data ? ( __( 'Username', 'library-management-system' ) . ': ' . $user_data->user_login . ' | ' . __( 'Role', 'library-management-system' ) . ': ' . ucfirst( implode( ', ', $user_data->roles ) ) ) : '';
	} else {
		$user_extra = __( 'Branch', 'library-management-system' ) . ': ' . $return->branch_name;
	}
	?>
	<div class="lms-info-block">
		<div class="lms-info-item"><span class="lms-info-label"><?php _e( 'User ID', 'library-management-system' ); ?></span><span class="lms-info-value"><?php echo esc_html( ! empty( $return->user_u_id ) ? $return->user_u_id : $return->u_id ); ?></span></div>
		<div class="lms-info-item"><span class="lms-info-label"><?php _e( 'Name', 'library-management-system' ); ?></span><span class="lms-info-value"><?php echo esc_html( $user_name ); ?></span></div>
		<div class="lms-info-item"><span class="lms-info-label"><?php _e( 'Email', 'library-management-system' ); ?></span><span class="lms-info-value"><?php echo esc_html( $return->user_email ); ?></span></div>
		<?php if ( $user_extra ) : ?>
		<div class="lms-info-item"><span class="lms-info-label"></span><span class="lms-info-value"><?php echo esc_html( $user_extra ); ?></span></div>
		<?php endif; ?>
	</div>
	<?php
} elseif ( $part === 'remark' ) {
	$extra_days = isset( $return->extra_days ) ? $return->extra_days : 0;
	$fine_amount = isset( $return->fine_amount ) ? $return->fine_amount : 0;
	?>
	<div class="lms-info-block owt7-lms-view-remark-rows">
		<div class="owt7-lms-view-remark-row">
			<div class="lms-info-item"><span class="lms-info-label"><?php _e( 'Return status', 'library-management-system' ); ?></span><span class="lms-info-value"><?php echo $return_condition_display; ?></span></div>
			<div class="lms-info-item"><span class="lms-info-label"><?php _e( 'Remark', 'library-management-system' ); ?></span><span class="lms-info-value"><?php echo esc_html( ! empty( $return->return_remark ) ? $return->return_remark : '—' ); ?></span></div>
		</div>
		<div class="owt7-lms-view-remark-row">
			<div class="lms-info-item"><span class="lms-info-label"><?php _e( 'Return ID', 'library-management-system' ); ?></span><span class="lms-info-value"><?php echo esc_html( ! empty( $return->return_id ) ? $return->return_id : '—' ); ?></span></div>
			<div class="lms-info-item"><span class="lms-info-label"><?php _e( 'Borrow ID', 'library-management-system' ); ?></span><span class="lms-info-value"><?php echo esc_html( $return->borrow_id ); ?></span></div>
		</div>
		<div class="owt7-lms-view-remark-row">
			<div class="lms-info-item"><span class="lms-info-label"><?php _e( 'Issued', 'library-management-system' ); ?></span><span class="lms-info-value"><?php echo esc_html( date( 'Y-m-d', strtotime( $return->issued_on ) ) ); ?></span></div>
			<div class="lms-info-item"><span class="lms-info-label"><?php _e( 'Returned', 'library-management-system' ); ?></span><span class="lms-info-value"><?php echo esc_html( $returned_date ? $returned_date : '—' ); ?></span></div>
		</div>
		<div class="owt7-lms-view-remark-row">
			<div class="lms-info-item"><span class="lms-info-label"><?php _e( 'Total Days', 'library-management-system' ); ?></span><span class="lms-info-value"><?php echo esc_html( $return->total_days . ' ' . __( 'Days', 'library-management-system' ) ); ?></span></div>
			<div class="lms-info-item"><span class="lms-info-label"><?php _e( 'Total Extra', 'library-management-system' ); ?></span><span class="lms-info-value"><?php echo esc_html( $extra_days . ' ' . __( 'days', 'library-management-system' ) ); ?></span></div>
		</div>
		<div class="owt7-lms-view-remark-row">
			<div class="lms-info-item"><span class="lms-info-label"><?php _e( 'Total Fine', 'library-management-system' ); ?></span><span class="lms-info-value"><?php echo esc_html( $fine_amount . ' ' . $currency ); ?></span></div>
		</div>
	</div>
	<?php
}
