<?php
/**
 * @link       https://onlinewebtutorblog.com
 * @since      3.5
 * @package    Library_Management_System
 * @subpackage Library_Management_System/public/views/templates
 * @copyright  Copyright (c) 2026, Online Web Tutor
 * @license    GPL-2.0+ https://www.gnu.org/licenses/gpl-2.0.html
 * @author     Online Web Tutor
 */
if ( empty( $params['book'] ) ) {
	return;
}
$book = $params['book'];
$book_ids = isset( $params['book_ids'] ) ? $params['book_ids'] : array();
$checkout_statuses = isset( $params['checkout_statuses'] ) ? $params['checkout_statuses'] : array();
$return_statuses = isset( $params['return_statuses'] ) ? $params['return_statuses'] : array();
$status_label = isset( $params['status_label'] ) ? $params['status_label'] : ( ( ! empty( $book->status ) && (int) $book->stock_quantity > 0 ) ? __( 'Available', 'library-management-system' ) : __( 'Not Available', 'library-management-system' ) );
$total_copies = isset( $params['total_copies_available'] ) ? (int) $params['total_copies_available'] : (int) $book->stock_quantity;
?>
<div class="owt7-lms-book-detail-modal-inner">
	<div class="owt7-lms-book-detail-modal-body">
		<?php if ( ! empty( $book->cover_image ) ) : ?>
			<div class="owt7-lms-book-detail-cover">
				<img src="<?php echo esc_url( $book->cover_image ); ?>" alt="<?php echo esc_attr( $book->name ); ?>">
			</div>
		<?php else : ?>
			<div class="owt7-lms-book-detail-cover">
				<img src="<?php echo esc_url( LIBMNS_PLUGIN_URL . 'public/images/default-cover-image.png' ); ?>" alt="<?php echo esc_attr( $book->name ); ?>">
			</div>
		<?php endif; ?>
		<div class="owt7-lms-book-detail-info">
			<h3 class="owt7-lms-book-detail-title"><?php echo esc_html( $book->name ); ?></h3>
			<dl class="owt7-lms-book-detail-meta">
				<dt><?php esc_html_e( 'Author:', 'library-management-system' ); ?></dt>
				<dd><?php echo LIBMNS_Admin_FREE::libmns_render_comma_tags( $book->author_name ) ?: esc_html( __( '—', 'library-management-system' ) ); ?></dd>

				<?php if ( ! empty( $book->publication_name ) ) : ?>
					<dt><?php esc_html_e( 'Publication:', 'library-management-system' ); ?></dt>
					<dd><?php echo LIBMNS_Admin_FREE::libmns_render_comma_tags( $book->publication_name ); ?></dd>
				<?php endif; ?>

				<dt><?php esc_html_e( 'Category:', 'library-management-system' ); ?></dt>
				<dd><?php echo esc_html( $book->category_name ?: '—' ); ?></dd>

				<?php if ( ! empty( $book->bookcase_name ) ) : ?>
					<dt><?php esc_html_e( 'Bookcase:', 'library-management-system' ); ?></dt>
					<dd><?php echo esc_html( $book->bookcase_name ); ?></dd>
				<?php endif; ?>

				<?php if ( ! empty( $book->section_name ) ) : ?>
					<dt><?php esc_html_e( 'Section:', 'library-management-system' ); ?></dt>
					<dd><?php echo esc_html( $book->section_name ); ?></dd>
				<?php endif; ?>

				<dt><?php esc_html_e( 'Status:', 'library-management-system' ); ?></dt>
				<dd><span class="owt7-lms-status-label"><?php echo esc_html( $status_label ); ?></span></dd>

				<dt><?php esc_html_e( 'Total copies available:', 'library-management-system' ); ?></dt>
				<dd><?php echo (int) $total_copies; ?></dd>
			</dl>
			<?php if ( ! empty( $book->description ) ) : ?>
				<div class="owt7-lms-book-detail-desc">
					<strong><?php esc_html_e( 'Description:', 'library-management-system' ); ?></strong>
					<p><?php echo esc_html( $book->description ); ?></p>
				</div>
			<?php endif; ?>
			<div class="owt7-lms-book-detail-actions">
				<?php
				if ( ! empty( $book->status ) && (int) $book->stock_quantity > 0 ) {
					if ( is_user_logged_in() ) {
						$settings = get_option( 'owt7_lms_public_settings', array() );
						$user = wp_get_current_user();
						$logged_in_wp_roles = isset( $user->roles ) ? (array) $user->roles : array();
						$lms_saved_roles = isset( $settings['wp_lms_roles'] ) && is_array( $settings['wp_lms_roles'] ) ? $settings['wp_lms_roles'] : array();
						$show_checkout_btn = false;
						foreach ( $logged_in_wp_roles as $u_role ) {
							if ( in_array( $u_role, $lms_saved_roles, true ) ) {
								$show_checkout_btn = true;
								break;
							}
						}
						if ( $show_checkout_btn ) {
							if ( in_array( $book->id, $book_ids ) ) {
								if ( in_array( (int) $checkout_statuses[ $book->id ], array( LIBMNS_CHECKOUT_APPROVED_BY_ADMIN, LIBMNS_CHECKOUT_SELF_APPROVED, LIBMNS_CHECKOUT_NO_STATUS ), true ) ) {
									if ( isset( $return_statuses[ $book->id ] ) && (int) $return_statuses[ $book->id ] === LIBMNS_DEFAULT_RETURN ) {
										?><span class="view-book-btn owt7_lms_return_requested"><?php esc_html_e( 'Return Requested', 'library-management-system' ); ?></span><?php
									} else {
										?><a href="javascript:void(0)" class="view-book-btn owt7_lms_user_do_return" data-id="<?php echo esc_attr( base64_encode( (string) $book->id ) ); ?>"><?php esc_html_e( 'Return', 'library-management-system' ); ?></a><?php
									}
								} elseif ( (int) $checkout_statuses[ $book->id ] === LIBMNS_DEFAULT_CHECKOUT ) {
									?><span class="view-book-btn owt7_lms_checkout_requested"><?php esc_html_e( 'Checkout Requested', 'library-management-system' ); ?></span><?php
								}
							} else {
								?><a href="javascript:void(0)" class="view-book-btn owt7_lms_user_do_checkout" data-id="<?php echo esc_attr( base64_encode( (string) $book->id ) ); ?>"><?php esc_html_e( 'Checkout', 'library-management-system' ); ?></a><?php
							}
						}
					} else {
						?><a href="javascript:void(0)" class="view-book-btn owt7_lms_do_user_login"><?php esc_html_e( 'Login', 'library-management-system' ); ?></a><?php
					}
				} else {
					?><span class="view-book-btn owt7_lms_book_no_stock"><?php esc_html_e( 'Out of stock', 'library-management-system' ); ?></span><?php
				}
				?>
			</div>
		</div>
	</div>
</div>
