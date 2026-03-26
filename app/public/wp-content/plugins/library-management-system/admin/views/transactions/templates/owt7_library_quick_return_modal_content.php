<?php
/**
 * @link       https://onlinewebtutorblog.com
 * @since      3.5
 * @package    Library_Management_System
 * @subpackage Library_Management_System/admin/views/transactions/templates
 * @copyright  Copyright (c) 2026, Online Web Tutor
 * @license    GPL-2.0+ https://www.gnu.org/licenses/gpl-2.0.html
 * @author     Online Web Tutor
 *
 * Template for quick return modal content sections.
 * Requires: $borrow (object), $part (string: 'user'|'book'|'loan')
 */
if ( empty( $borrow ) || empty( $part ) ) {
	return;
}

if ( $part === 'user' ) {
	$user_name  = $borrow->user_name;
	$extra_rows = array();
	if ( ! empty( $borrow->wp_user ) && function_exists( 'get_userdata' ) ) {
		$user_data = get_userdata( $borrow->u_id );
		if ( $user_data ) {
			$user_name    = $user_data->display_name;
			$extra_rows[] = array(
				'label' => __( 'Username', 'library-management-system' ),
				'value' => $user_data->user_login,
			);
			$extra_rows[] = array(
				'label' => __( 'Role', 'library-management-system' ),
				'value' => ucfirst( implode( ', ', $user_data->roles ) ),
			);
		}
	} else {
		$extra_rows[] = array(
			'label' => __( 'Branch', 'library-management-system' ),
			'value' => $borrow->branch_name,
		);
	}
	?>
	<div class="lms-info-block">
		<div class="lms-info-item">
			<span class="lms-info-label"><?php _e( 'User ID', 'library-management-system' ); ?></span>
			<span class="lms-info-value"><?php echo esc_html( ! empty( $borrow->user_u_id ) ? $borrow->user_u_id : $borrow->u_id ); ?></span>
		</div>
		<div class="lms-info-item">
			<span class="lms-info-label"><?php _e( 'Name', 'library-management-system' ); ?></span>
			<span class="lms-info-value"><?php echo esc_html( $user_name ); ?></span>
		</div>
		<div class="lms-info-item">
			<span class="lms-info-label"><?php _e( 'Email', 'library-management-system' ); ?></span>
			<span class="lms-info-value"><?php echo esc_html( $borrow->user_email ); ?></span>
		</div>
		<?php foreach ( $extra_rows as $row ) : ?>
		<div class="lms-info-item">
			<span class="lms-info-label"><?php echo esc_html( $row['label'] ); ?></span>
			<span class="lms-info-value"><?php echo esc_html( $row['value'] ); ?></span>
		</div>
		<?php endforeach; ?>
	</div>
	<?php

} elseif ( $part === 'book' ) {
	?>
	<div class="lms-info-block">
		<div class="lms-info-item">
			<span class="lms-info-label"><?php _e( 'Book ID', 'library-management-system' ); ?></span>
			<span class="lms-info-value"><?php echo esc_html( ! empty( $borrow->book_book_id ) ? $borrow->book_book_id : '—' ); ?></span>
		</div>
		<div class="lms-info-item">
			<span class="lms-info-label"><?php _e( 'Category', 'library-management-system' ); ?></span>
			<span class="lms-info-value"><?php echo esc_html( $borrow->category_name ); ?></span>
		</div>
		<div class="lms-info-item lms-info-item-title">
			<span class="lms-info-label"><?php _e( 'Name', 'library-management-system' ); ?></span>
			<span class="lms-info-value"><?php echo esc_html( $borrow->book_name ); ?></span>
		</div>
		<div class="lms-info-item">
			<span class="lms-info-label"><?php _e( 'Acc No.', 'library-management-system' ); ?></span>
			<span class="lms-info-value"><?php echo esc_html( ! empty( $borrow->accession_number ) ? $borrow->accession_number : '—' ); ?></span>
		</div>
	</div>
	<?php

} elseif ( $part === 'loan' ) {
	?>
	<div class="lms-info-block owt7-lms-qr-loan-grid">
		<div class="lms-info-item">
			<span class="lms-info-label"><?php _e( 'Borrow ID', 'library-management-system' ); ?></span>
			<span class="lms-info-value"><?php echo esc_html( $borrow->borrow_id ); ?></span>
		</div>
		<div class="lms-info-item">
			<span class="lms-info-label"><?php _e( 'Duration', 'library-management-system' ); ?></span>
			<span class="lms-info-value"><?php echo esc_html( $borrow->borrows_days ); ?> <?php _e( 'days', 'library-management-system' ); ?></span>
		</div>
		<div class="lms-info-item">
			<span class="lms-info-label"><?php _e( 'Issued on', 'library-management-system' ); ?></span>
			<span class="lms-info-value lms-date"><?php echo esc_html( date( 'Y-m-d', strtotime( $borrow->created_at ) ) ); ?></span>
		</div>
		<div class="lms-info-item">
			<span class="lms-info-label"><?php _e( 'Return by', 'library-management-system' ); ?></span>
			<span class="lms-info-value lms-date"><?php echo esc_html( $borrow->return_date ); ?></span>
		</div>
	</div>
	<?php
}
