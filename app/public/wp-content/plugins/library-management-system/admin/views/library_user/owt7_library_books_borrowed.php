<?php
/**
 * @link       https://onlinewebtutorblog.com
 * @since      3.5
 * @package    Library_Management_System
 * @subpackage Library_Management_System/admin/views/library_user
 * @copyright  Copyright (c) 2026, Online Web Tutor
 * @license    GPL-2.0+ https://www.gnu.org/licenses/gpl-2.0.html
 * @author     Online Web Tutor
 *
 * Columns match public Library tabs "Books Borrowed": Book name, Category, Author, Publication, Issue date, Expected return, Accession, Action (Return).
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$borrowed_books_list = isset( $params['borrowed_books_list'] ) ? $params['borrowed_books_list'] : array();
$currency_symbol    = get_option( 'owt7_lms_currency', '' );
?>
<div class="owt7-lms owt7-lms-borrowed-books">

	<div class="page-container lms-books-borrowed">
		<h1 class="page-title">
			<?php esc_html_e( 'Books Borrowed', 'library-management-system' ); ?>
		</h1>
		<p class="description">
			<?php esc_html_e( 'View the list of books you have borrowed from the library.', 'library-management-system' ); ?>
		</p>

		<?php if ( ! empty( $borrowed_books_list ) ) : ?>
		<div class="owt7-lms-library-user-table-wrap">
			<table class="owt7-lms-table owt7-lms-library-user-borrowed-table" id="tbl_library_user_borrowed">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Book Name', 'library-management-system' ); ?></th>
						<th><?php esc_html_e( 'Category', 'library-management-system' ); ?></th>
						<th><?php esc_html_e( 'Author', 'library-management-system' ); ?></th>
						<th><?php esc_html_e( 'Publication', 'library-management-system' ); ?></th>
						<th><?php esc_html_e( 'Issue Date', 'library-management-system' ); ?></th>
						<th><?php esc_html_e( 'Expected Return', 'library-management-system' ); ?></th>
						<th><?php esc_html_e( 'Accession No.', 'library-management-system' ); ?></th>
						<th><?php esc_html_e( 'Action', 'library-management-system' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $borrowed_books_list as $row ) :
						$book_id   = isset( $row->id ) ? (int) $row->id : 0;
						$book_code = isset( $row->book_id ) && '' !== $row->book_id ? $row->book_id : $book_id;
						$book_name = isset( $row->name ) ? $row->name : '';
						$category  = isset( $row->category_name ) ? $row->category_name : '—';
						$author    = isset( $row->author_name ) ? trim( $row->author_name ) : '—';
						$pub       = isset( $row->publication_name ) ? trim( $row->publication_name ) : '—';
						$isbn      = isset( $row->isbn ) && '' !== trim( (string) $row->isbn ) ? trim( (string) $row->isbn ) : '—';
						$cost      = isset( $row->amount ) && '' !== trim( (string) $row->amount ) ? trim( (string) $row->amount ) : '—';
						$cost_label = '—' !== $cost ? trim( $currency_symbol . ' ' . $cost ) : '—';
						$issue_raw = isset( $row->issue_date ) ? $row->issue_date : '';
						$issue     = $issue_raw;
						$expected_raw = isset( $row->expected_return_date ) ? $row->expected_return_date : '';
						$expected  = $expected_raw;
						$acc       = isset( $row->accession_number ) ? $row->accession_number : '—';
						$borrow_record_id = isset( $row->borrow_id ) && '' !== $row->borrow_id ? $row->borrow_id : ( isset( $row->borrow_record_id ) ? (string) $row->borrow_record_id : '—' );
						$pending_return_status = isset( $row->pending_return_status ) ? (int) $row->pending_return_status : 0;
						$is_return_pending = defined( 'LIBMNS_DEFAULT_RETURN' ) && (int) LIBMNS_DEFAULT_RETURN === $pending_return_status;
						if ( $issue ) {
							$issue = date_i18n( get_option( 'date_format' ), strtotime( $issue ) );
						}
						if ( $expected ) {
							$expected = date_i18n( get_option( 'date_format' ), strtotime( $expected ) );
						}
					?>
					<tr>
						<td><?php echo esc_html( $book_name ); ?></td>
						<td><?php echo esc_html( $category ); ?></td>
						<td><?php echo esc_html( $author ); ?></td>
						<td><?php echo esc_html( $pub ); ?></td>
						<td><?php echo esc_html( $issue ); ?></td>
						<td><?php echo esc_html( $expected ); ?></td>
						<td><?php echo esc_html( $acc ); ?></td>
						<td>
							<div class="owt7-lms-table-actions">
								<a href="javascript:void(0)" class="button button-small owt7-lms-btn-view-book owt7-lms-portal-btn owt7_lms_user_view_borrowed_details" data-book-name="<?php echo esc_attr( $book_name ); ?>" data-book-code="<?php echo esc_attr( $book_code ); ?>" data-book-category="<?php echo esc_attr( $category ); ?>" data-book-author="<?php echo esc_attr( $author ); ?>" data-book-publication="<?php echo esc_attr( $pub ); ?>" data-book-isbn="<?php echo esc_attr( $isbn ); ?>" data-book-cost="<?php echo esc_attr( $cost_label ); ?>" data-borrow-record-id="<?php echo esc_attr( $borrow_record_id ); ?>" data-issue-date="<?php echo esc_attr( $issue ); ?>" data-expected-return-date="<?php echo esc_attr( $expected ); ?>" data-accession-number="<?php echo esc_attr( $acc ); ?>" title="<?php esc_attr_e( 'View', 'library-management-system' ); ?>">
									<span class="dashicons dashicons-visibility" aria-hidden="true"></span>
									<span><?php esc_html_e( 'View', 'library-management-system' ); ?></span>
								</a>
								<?php if ( $is_return_pending ) : ?>
									<span class="button button-small owt7-lms-btn-return-pending"><?php esc_html_e( 'Return Pending', 'library-management-system' ); ?></span>
								<?php endif; ?>
							</div>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php else : ?>
		<div class="owt7-lms-no-books owt7-lms-empty-state owt7-lms-empty-state-borrowed">
			<div class="owt7-lms-empty-state-icon" aria-hidden="true">
				<span class="dashicons dashicons-book-alt"></span>
			</div>
			<h3 class="owt7-lms-empty-state-title"><?php esc_html_e( 'No borrowed books', 'library-management-system' ); ?></h3>
			<p class="owt7-lms-empty-state-desc"><?php esc_html_e( 'You have no borrowed books at the moment. Visit the Books List to checkout books from the library.', 'library-management-system' ); ?></p>
		</div>
		<?php endif; ?>
	</div>
</div>

<div id="owt7_lms_mdl_user_borrowed_details" class="modal owt7-lms-view-return-modal" style="display: none;">
	<div class="modal-content owt7-lms-view-return-content">
		<button type="button" class="close owt7_lms_user_borrowed_details_close" aria-label="<?php esc_attr_e( 'Close', 'library-management-system' ); ?>">&times;</button>
		<h2 id="owt7_lms_user_borrowed_details_title"><?php esc_html_e( 'Borrowed Book Details', 'library-management-system' ); ?></h2>
		<div class="owt7-lms-view-return-sections">
			<div class="owt7-lms-view-section owt7-lms-view-section-book">
				<h3 class="owt7-lms-view-section-title"><?php esc_html_e( 'Book Information', 'library-management-system' ); ?></h3>
				<div class="owt7-lms-view-detail-body" id="owt7_lms_user_borrowed_book_details"></div>
			</div>
			<div class="owt7-lms-view-section owt7-lms-view-section-remark">
				<h3 class="owt7-lms-view-section-title" id="owt7_lms_user_borrowed_timeline_title"><?php esc_html_e( 'Borrowing Information', 'library-management-system' ); ?></h3>
				<div class="owt7-lms-view-detail-body" id="owt7_lms_user_borrowed_timeline_details"></div>
			</div>
		</div>
	</div>
</div>
