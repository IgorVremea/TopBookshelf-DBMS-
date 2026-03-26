<?php
/**
 * Single book view for Library User catalogue (URL: admin.php?page=owt7_library_books_catalogue&bid=ID).
 *
 * @link       https://onlinewebtutorblog.com
 * @since      3.5
 * @package    Library_Management_System
 * @subpackage Library_Management_System/admin/views/library_user
 * @copyright  Copyright (c) 2026, Online Web Tutor
 * @license    GPL-2.0+ https://www.gnu.org/licenses/gpl-2.0.html
 * @author     Online Web Tutor
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$book                   = isset( $params['book'] ) ? $params['book'] : null;
$user_borrowed_book_ids = isset( $params['user_borrowed_book_ids'] ) && is_array( $params['user_borrowed_book_ids'] ) ? $params['user_borrowed_book_ids'] : array();
$catalogue_url          = isset( $params['catalogue_url'] ) ? $params['catalogue_url'] : admin_url( 'admin.php?page=owt7_library_books_catalogue' );

if ( ! $book ) {
	return;
}

$book_id       = isset( $book->id ) ? (int) $book->id : 0;
$book_name     = isset( $book->name ) ? $book->name : '';
$author_name   = isset( $book->author_name ) ? trim( $book->author_name ) : '';
$category_name = isset( $book->category_name ) ? $book->category_name : '';
$isbn          = isset( $book->isbn ) ? $book->isbn : '';
$amount        = isset( $book->amount ) ? $book->amount : '';
$stock         = isset( $book->stock_quantity ) ? (int) $book->stock_quantity : 0;
$cover         = isset( $book->cover_image ) && $book->cover_image ? esc_url( $book->cover_image ) : '';
$pub_name      = isset( $book->publication_name ) ? trim( (string) $book->publication_name ) : '';
$pub_year      = isset( $book->publication_year ) ? trim( (string) $book->publication_year ) : '';
$description   = isset( $book->description ) ? trim( (string) $book->description ) : '';
$available     = ( $stock > 0 );
$is_borrowed   = in_array( $book_id, $user_borrowed_book_ids, true );
$book_id_b64   = base64_encode( (string) $book_id );
$checkout_statuses = isset( $params['checkout_statuses'] ) && is_array( $params['checkout_statuses'] ) ? $params['checkout_statuses'] : array();
$return_statuses   = isset( $params['return_statuses'] ) && is_array( $params['return_statuses'] ) ? $params['return_statuses'] : array();
$checkout_status   = isset( $checkout_statuses[ $book_id ] ) ? (int) $checkout_statuses[ $book_id ] : 0;
$return_status     = isset( $return_statuses[ $book_id ] ) ? (int) $return_statuses[ $book_id ] : 0;
$is_checkout_pending = (int) LIBMNS_DEFAULT_CHECKOUT === $checkout_status;
$is_return_pending   = (int) LIBMNS_DEFAULT_RETURN === $return_status;
$is_checked_out      = $is_borrowed || in_array( $checkout_status, array( (int) LIBMNS_CHECKOUT_APPROVED_BY_ADMIN, (int) LIBMNS_CHECKOUT_SELF_APPROVED, (int) LIBMNS_CHECKOUT_NO_STATUS ), true );
$action_state        = 'unavailable';
if ( $is_return_pending ) {
	$action_state = 'return_pending';
} elseif ( $is_checkout_pending ) {
	$action_state = 'checkout_pending';
} elseif ( $is_checked_out ) {
	$action_state = 'borrowed';
} elseif ( $available ) {
	$action_state = 'available';
}
$checkout_overview = isset( $params['checkout_overview'] ) && is_array( $params['checkout_overview'] ) ? $params['checkout_overview'] : array();
$checkout_days = isset( $checkout_overview['days'] ) ? (int) $checkout_overview['days'] : LIBMNS_DEFAULT_BORROW_DAYS;
$request_date  = isset( $checkout_overview['request_date'] ) ? (string) $checkout_overview['request_date'] : '';
$expected_date = isset( $checkout_overview['expected_return_date'] ) ? (string) $checkout_overview['expected_return_date'] : '';
?>
<div class="owt7-lms owt7-lms-books-catalogue owt7-lms-catalogue-single">

	<div class="page-container lms-books-catalogue-single">
		<div class="owt7-lms-page-header owt7-lms-catalogue-single-header">
			<div class="owt7-lms-page-header-left">
				<h1 class="page-title">
					<?php echo esc_html( $book_name ); ?>
				</h1>
				<p class="description">
					<?php esc_html_e( 'Book details from the library catalogue.', 'library-management-system' ); ?>
				</p>
			</div>
			<div class="owt7-lms-page-header-right">
				<a href="<?php echo esc_url( $catalogue_url ); ?>" class="button button-primary owt7-lms-btn-back owt7-lms-portal-btn">
					<span class="dashicons dashicons-arrow-left-alt2" aria-hidden="true"></span>
					<span><?php esc_html_e( 'Back', 'library-management-system' ); ?></span>
				</a>
			</div>
		</div>

		<div class="owt7-lms-catalogue-single-content">
			<div class="owt7-lms-single-book-card">
				<div class="owt7-lms-single-book-inner">
					<div class="owt7-lms-single-book-cover">
						<?php if ( $cover ) : ?>
							<img src="<?php echo $cover; ?>" alt="<?php echo esc_attr( $book_name ); ?>" />
						<?php else : ?>
							<div class="owt7-lms-book-cover-placeholder">
								<span class="dashicons dashicons-book-alt" aria-hidden="true"></span>
							</div>
						<?php endif; ?>
						<?php if ( ! $available ) : ?>
							<span class="owt7-lms-book-badge owt7-lms-badge-unavailable"><?php esc_html_e( 'Unavailable', 'library-management-system' ); ?></span>
						<?php endif; ?>
					</div>
					<div class="owt7-lms-single-book-body">
						<dl class="owt7-lms-single-book-meta">
							<?php if ( $author_name ) : ?>
								<dt><?php esc_html_e( 'Author', 'library-management-system' ); ?></dt>
								<dd><?php echo esc_html( $author_name ); ?></dd>
							<?php endif; ?>
							<?php if ( $category_name ) : ?>
								<dt><?php esc_html_e( 'Category', 'library-management-system' ); ?></dt>
								<dd><?php echo esc_html( $category_name ); ?></dd>
							<?php endif; ?>
							<?php if ( $pub_name ) : ?>
								<dt><?php esc_html_e( 'Publication', 'library-management-system' ); ?></dt>
								<dd><?php echo esc_html( $pub_name ); ?></dd>
							<?php endif; ?>
							<?php if ( $pub_year ) : ?>
								<dt><?php esc_html_e( 'Publication year', 'library-management-system' ); ?></dt>
								<dd><?php echo esc_html( $pub_year ); ?></dd>
							<?php endif; ?>
							<?php if ( $isbn ) : ?>
								<dt><?php esc_html_e( 'ISBN', 'library-management-system' ); ?></dt>
								<dd><?php echo esc_html( $isbn ); ?></dd>
							<?php endif; ?>
							<?php if ( $amount !== '' ) : ?>
								<dt><?php esc_html_e( 'Cost', 'library-management-system' ); ?></dt>
								<dd><?php echo esc_html( $amount ); ?></dd>
							<?php endif; ?>
							<dt><?php esc_html_e( 'Copies left', 'library-management-system' ); ?></dt>
							<dd>
								<?php
								if ( (int) $stock === 1 ) {
									esc_html_e( '1 copy', 'library-management-system' );
								} else {
									/* translators: %d: number of copies available */
									echo esc_html( sprintf( __( '%d copies', 'library-management-system' ), (int) $stock ) );
								}
								?>
							</dd>
						</dl>
						<?php if ( $description !== '' ) : ?>
							<div class="owt7-lms-single-book-description">
								<h3 class="owt7-lms-single-book-description-title"><?php esc_html_e( 'Description', 'library-management-system' ); ?></h3>
								<div class="owt7-lms-single-book-description-text"><?php echo nl2br( esc_html( $description ) ); ?></div>
							</div>
						<?php endif; ?>
						<div class="owt7-lms-single-book-actions">
							<?php if ( 'borrowed' === $action_state ) : ?>
								<span class="button button-small owt7-lms-btn-pending" disabled><?php esc_html_e( 'Borrowed', 'library-management-system' ); ?></span>
							<?php elseif ( 'available' === $action_state ) : ?>
								<span class="button button-small owt7-lms-btn-pending" disabled><?php esc_html_e( 'Contact admin to borrow', 'library-management-system' ); ?></span>
							<?php else : ?>
								<span class="button button-small owt7-lms-btn-unavailable" disabled><?php esc_html_e( 'Out of stock', 'library-management-system' ); ?></span>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
