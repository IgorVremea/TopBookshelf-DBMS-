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

$books                  = isset( $params['books'] ) ? $params['books'] : array();
$total_books            = isset( $params['total_books'] ) ? (int) $params['total_books'] : 0;
$total_pages            = isset( $params['total_pages'] ) ? (int) $params['total_pages'] : 1;
$current_page           = isset( $params['current_page'] ) ? (int) $params['current_page'] : 1;
$filter_cat             = isset( $params['filter_cat'] ) ? (int) $params['filter_cat'] : 0;
$filter_author          = isset( $params['filter_author'] ) ? (string) $params['filter_author'] : '';
$filter_search          = isset( $params['filter_search'] ) ? (string) $params['filter_search'] : '';
$user_borrowed_book_ids = isset( $params['user_borrowed_book_ids'] ) && is_array( $params['user_borrowed_book_ids'] ) ? $params['user_borrowed_book_ids'] : array();
$card_design            = isset( $params['card_design'] ) ? (string) $params['card_design'] : 'default';
$catalogue_url          = admin_url( 'admin.php?page=owt7_library_books_catalogue' );
$showing_count          = is_array( $books ) ? count( $books ) : 0;
$available_count        = 0;
$checkout_overview      = isset( $params['checkout_overview'] ) && is_array( $params['checkout_overview'] ) ? $params['checkout_overview'] : array();
$checkout_days          = isset( $checkout_overview['days'] ) ? (int) $checkout_overview['days'] : LIBMNS_DEFAULT_BORROW_DAYS;
$request_date           = isset( $checkout_overview['request_date'] ) ? (string) $checkout_overview['request_date'] : '';
$expected_date          = isset( $checkout_overview['expected_return_date'] ) ? (string) $checkout_overview['expected_return_date'] : '';
$checkout_statuses      = isset( $params['checkout_statuses'] ) && is_array( $params['checkout_statuses'] ) ? $params['checkout_statuses'] : array();
$return_statuses        = isset( $params['return_statuses'] ) && is_array( $params['return_statuses'] ) ? $params['return_statuses'] : array();

foreach ( (array) $books as $book_row ) {
	$stock        = isset( $book_row->stock_quantity ) ? (int) $book_row->stock_quantity : 0;
	if ( $stock > 0 ) {
		$available_count++;
	}
}
?>
<div class="owt7-lms-catalogue-results-head">
	<div class="owt7-lms-catalogue-result-chip">
		<span class="owt7-lms-catalogue-result-label"><?php esc_html_e( 'Showing', 'library-management-system' ); ?></span>
		<strong><?php echo esc_html( $showing_count ); ?></strong>
	</div>
	<div class="owt7-lms-catalogue-result-chip">
		<span class="owt7-lms-catalogue-result-label"><?php esc_html_e( 'Matched books', 'library-management-system' ); ?></span>
		<strong><?php echo esc_html( $total_books ); ?></strong>
	</div>
	<div class="owt7-lms-catalogue-result-chip">
		<span class="owt7-lms-catalogue-result-label"><?php esc_html_e( 'Available now', 'library-management-system' ); ?></span>
		<strong><?php echo esc_html( $available_count ); ?></strong>
	</div>
</div>

<div class="owt7-lms-catalogue-list book-list">
	<?php if ( ! empty( $books ) ) : ?>
		<div class="owt7-lms-book-cards">
			<?php foreach ( $books as $book ) : ?>
				<?php
				$book_name     = isset( $book->name ) ? $book->name : '';
				$author_name   = isset( $book->author_name ) ? trim( $book->author_name ) : '';
				$category_name = isset( $book->category_name ) ? $book->category_name : '';
				$isbn          = isset( $book->isbn ) ? $book->isbn : '';
				$amount        = isset( $book->amount ) ? $book->amount : '';
				$book_id       = isset( $book->id ) ? (int) $book->id : 0;
				$stock         = isset( $book->stock_quantity ) ? (int) $book->stock_quantity : 0;
				$available     = ( $stock > 0 );
				$cover         = isset( $book->cover_image ) && $book->cover_image ? esc_url( $book->cover_image ) : '';
				$pub_name      = isset( $book->publication_name ) ? trim( (string) $book->publication_name ) : '';
				$pub_year      = isset( $book->publication_year ) ? trim( (string) $book->publication_year ) : '';
				$description   = isset( $book->description ) ? trim( (string) $book->description ) : '';
				$is_borrowed   = in_array( $book_id, $user_borrowed_book_ids, true );
				$book_id_b64   = base64_encode( (string) $book_id );
				$checkout_status = isset( $checkout_statuses[ $book_id ] ) ? (int) $checkout_statuses[ $book_id ] : 0;
				$return_status   = isset( $return_statuses[ $book_id ] ) ? (int) $return_statuses[ $book_id ] : 0;
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
				?>
				<div class="owt7-lms-book-card owt7-lms-card-<?php echo esc_attr( $card_design ); ?>"
					data-book-title="<?php echo esc_attr( $book_name ); ?>"
					data-book-author="<?php echo esc_attr( $author_name ); ?>"
					data-book-category="<?php echo esc_attr( $category_name ); ?>"
					data-book-publication="<?php echo esc_attr( $pub_name ); ?>"
					data-book-year="<?php echo esc_attr( $pub_year ); ?>"
					data-book-description="<?php echo esc_attr( $description ); ?>"
					data-book-cover="<?php echo esc_attr( $cover ); ?>"
					data-book-isbn="<?php echo esc_attr( $isbn ); ?>"
					data-book-amount="<?php echo esc_attr( $amount ); ?>"
					data-checkout-days="<?php echo esc_attr( $checkout_days ); ?>"
					data-request-date="<?php echo esc_attr( $request_date ); ?>"
					data-expected-return-date="<?php echo esc_attr( $expected_date ); ?>"
					data-book-id-b64="<?php echo esc_attr( $book_id_b64 ); ?>"
					data-book-borrowed="<?php echo $is_borrowed ? '1' : '0'; ?>"
					data-book-available="<?php echo $available ? '1' : '0'; ?>"
					data-book-action-state="<?php echo esc_attr( $action_state ); ?>">
					<div class="owt7-lms-book-card-inner">
						<div class="owt7-lms-book-card-image">
							<?php if ( $cover ) : ?>
								<img src="<?php echo $cover; ?>" alt="<?php echo esc_attr( $book_name ); ?>" loading="lazy" />
							<?php else : ?>
								<div class="owt7-lms-book-card-placeholder">
									<span class="dashicons dashicons-book-alt" aria-hidden="true"></span>
								</div>
							<?php endif; ?>
							<span class="owt7-lms-book-badge <?php echo $available ? 'owt7-lms-badge-available' : 'owt7-lms-badge-unavailable'; ?>">
								<?php echo $available ? esc_html__( 'Available', 'library-management-system' ) : esc_html__( 'Unavailable', 'library-management-system' ); ?>
							</span>
						</div>
						<div class="owt7-lms-book-card-body">
							<div class="owt7-lms-book-card-top">
								<h3 class="owt7-lms-book-title"><?php echo esc_html( $book_name ); ?></h3>
								<?php if ( $category_name ) : ?>
									<span class="owt7-lms-book-category-pill"><?php echo esc_html( $category_name ); ?></span>
								<?php endif; ?>
							</div>
							<?php if ( $author_name ) : ?>
								<p class="owt7-lms-book-meta owt7-lms-book-author"><?php echo esc_html( $author_name ); ?></p>
							<?php endif; ?>
							<?php if ( $pub_name || $pub_year ) : ?>
								<p class="owt7-lms-book-meta owt7-lms-book-publication">
									<?php echo esc_html( trim( $pub_name . ( $pub_year ? ' • ' . $pub_year : '' ) ) ); ?>
								</p>
							<?php endif; ?>
							<?php if ( $isbn || $amount !== '' ) : ?>
								<p class="owt7-lms-book-meta owt7-lms-book-details">
									<?php if ( $isbn ) : ?><span><?php esc_html_e( 'ISBN:', 'library-management-system' ); ?> <?php echo esc_html( $isbn ); ?></span><?php endif; ?>
									<?php if ( $amount !== '' ) : ?><span><?php esc_html_e( 'Cost:', 'library-management-system' ); ?> <?php echo esc_html( $amount ); ?></span><?php endif; ?>
								</p>
							<?php endif; ?>
							<p class="owt7-lms-book-meta owt7-lms-copies-left">
								<?php
								if ( (int) $stock === 1 ) {
									esc_html_e( '1 copy left', 'library-management-system' );
								} else {
									/* translators: %d: number of copies available */
									echo esc_html( sprintf( __( '%d copies left', 'library-management-system' ), (int) $stock ) );
								}
								?>
							</p>
							<div class="owt7-lms-book-card-actions">
								<a href="javascript:void(0)" class="button button-small owt7-lms-btn-view-book owt7-lms-portal-btn" title="<?php esc_attr_e( 'View details', 'library-management-system' ); ?>">
									<span class="dashicons dashicons-visibility" aria-hidden="true"></span>
									<span><?php esc_html_e( 'View', 'library-management-system' ); ?></span>
								</a>
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
			<?php endforeach; ?>
		</div>

		<?php if ( $total_pages > 1 ) : ?>
			<nav class="owt7-lms-catalogue-pagination" aria-label="<?php esc_attr_e( 'Books catalogue pagination', 'library-management-system' ); ?>">
				<ul class="owt7-lms-pagination-list">
					<?php
					$base_query = array( 'page' => 'owt7_library_books_catalogue' );
					if ( $filter_cat > 0 ) {
						$base_query['cat'] = $filter_cat;
					}
					if ( $filter_author !== '' ) {
						$base_query['author'] = $filter_author;
					}
					if ( $filter_search !== '' ) {
						$base_query['search'] = $filter_search;
					}
					$base_url = add_query_arg( $base_query, admin_url( 'admin.php' ) );
					?>
					<?php if ( $current_page > 1 ) : ?>
						<?php $prev_url = $current_page === 2 ? $base_url : add_query_arg( 'grid', $current_page - 1, $base_url ); ?>
						<li><a href="<?php echo esc_url( $prev_url ); ?>" class="owt7-lms-pagination-prev"><?php esc_html_e( '&laquo; Previous', 'library-management-system' ); ?></a></li>
					<?php endif; ?>

				<?php
				$delta         = 2;
				$range_start   = max( 2, $current_page - $delta );
				$range_end     = min( $total_pages - 1, $current_page + $delta );
				$pages_to_show = array_unique( array_merge(
					array( 1 ),
					range( $range_start, $range_end ),
					array( $total_pages )
				) );
				sort( $pages_to_show );
				$prev_p = null;
				foreach ( $pages_to_show as $p ) :
					if ( $prev_p !== null && $p - $prev_p > 1 ) :
				?>
						<li><span class="owt7-lms-pagination-ellipsis" aria-hidden="true">&hellip;</span></li>
				<?php
					endif;
					if ( (int) $p === (int) $current_page ) :
				?>
						<li><span class="owt7-lms-pagination-current" aria-current="page"><?php echo (int) $p; ?></span></li>
				<?php else : ?>
						<?php $page_url = (int) $p === 1 ? $base_url : add_query_arg( 'grid', $p, $base_url ); ?>
						<li><a href="<?php echo esc_url( $page_url ); ?>"><?php echo (int) $p; ?></a></li>
				<?php
					endif;
					$prev_p = $p;
				endforeach;
				?>

					<?php if ( $current_page < $total_pages ) : ?>
						<li><a href="<?php echo esc_url( add_query_arg( 'grid', (int) $current_page + 1, $base_url ) ); ?>" class="owt7-lms-pagination-next"><?php esc_html_e( 'Next &raquo;', 'library-management-system' ); ?></a></li>
					<?php endif; ?>
				</ul>
			</nav>
		<?php endif; ?>
	<?php else : ?>
		<div class="owt7-lms-catalogue-empty">
			<p class="description"><?php esc_html_e( 'No books found. Try adjusting your filters or search.', 'library-management-system' ); ?></p>
		</div>
	<?php endif; ?>
</div>
