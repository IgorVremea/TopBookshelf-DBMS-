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
 * Columns match public Library tabs "Books Returned": Book name, Category, Author, Publication, Issue date, Return date, Accession, Fine.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$returned_books_list = isset( $params['returned_books_list'] ) ? $params['returned_books_list'] : array();
$currency_symbol    = get_option( 'owt7_lms_currency', '' );
?>
<div class="owt7-lms owt7-lms-returned-books">

	<div class="page-container lms-books-returned">
		<h1 class="page-title">
			<?php esc_html_e( 'Books Returned', 'library-management-system' ); ?>
		</h1>
		<p class="description">
			<?php esc_html_e( 'View the list of books you have returned to the library.', 'library-management-system' ); ?>
		</p>

		<?php if ( ! empty( $returned_books_list ) ) : ?>
		<div class="owt7-lms-library-user-table-wrap">
			<table class="owt7-lms-table owt7-lms-library-user-returned-table" id="tbl_library_user_returned">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Book Name', 'library-management-system' ); ?></th>
						<th><?php esc_html_e( 'Category', 'library-management-system' ); ?></th>
						<th><?php esc_html_e( 'Author', 'library-management-system' ); ?></th>
						<th><?php esc_html_e( 'Publication', 'library-management-system' ); ?></th>
						<th><?php esc_html_e( 'Issue Date', 'library-management-system' ); ?></th>
						<th><?php esc_html_e( 'Return Date', 'library-management-system' ); ?></th>
						<th><?php esc_html_e( 'Accession No.', 'library-management-system' ); ?></th>
						<th><?php esc_html_e( 'Fine', 'library-management-system' ); ?></th>
						<th><?php esc_html_e( 'Action', 'library-management-system' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $returned_books_list as $row ) :
						$book_id   = isset( $row->id ) ? (int) $row->id : 0;
						$book_code = isset( $row->book_id ) && '' !== $row->book_id ? $row->book_id : $book_id;
						$book_name = isset( $row->name ) ? $row->name : '';
						$category  = isset( $row->category_name ) ? $row->category_name : '—';
						$author    = isset( $row->author_name ) ? trim( $row->author_name ) : '—';
						$pub       = isset( $row->publication_name ) ? trim( $row->publication_name ) : '—';
						$isbn      = isset( $row->isbn ) && '' !== trim( (string) $row->isbn ) ? trim( (string) $row->isbn ) : '—';
						$cost      = isset( $row->amount ) && '' !== trim( (string) $row->amount ) ? trim( (string) $row->amount ) : '—';
						$cost_label = '—' !== $cost ? trim( $currency_symbol . ' ' . $cost ) : '—';
						$issue     = isset( $row->issue_date ) ? $row->issue_date : '';
						$ret       = isset( $row->return_date ) ? $row->return_date : '';
						$acc       = isset( $row->accession_number ) ? $row->accession_number : '—';
						$borrow_record_id = isset( $row->borrow_id ) && '' !== $row->borrow_id ? $row->borrow_id : '—';
						$return_record_id = isset( $row->return_id ) && '' !== $row->return_id ? $row->return_id : ( isset( $row->return_record_id ) ? (string) $row->return_record_id : '—' );
					$fine         = isset( $row->fine_amount ) && (int) $row->fine_amount > 0 ? (int) $row->fine_amount : '';
					$fine_display = $fine !== '' ? trim( $currency_symbol . ' ' . (string) $fine ) : '';
					$fine_modal   = $fine_display !== '' ? $fine_display : __( 'No Fine', 'library-management-system' );
					$fine_paid    = $fine !== '' && isset( $row->fine_has_paid ) && (int) $row->fine_has_paid === 2;
						if ( $issue ) {
							$issue = date_i18n( get_option( 'date_format' ), strtotime( $issue ) );
						}
						if ( $ret ) {
							$ret = date_i18n( get_option( 'date_format' ), strtotime( $ret ) );
						}
					?>
					<tr>
						<td><?php echo esc_html( $book_name ); ?></td>
						<td><?php echo esc_html( $category ); ?></td>
						<td><?php echo esc_html( $author ); ?></td>
						<td><?php echo esc_html( $pub ); ?></td>
						<td><?php echo esc_html( $issue ); ?></td>
						<td><?php echo esc_html( $ret ); ?></td>
						<td><?php echo esc_html( $acc ); ?></td>
						<td>
							<?php if ( $fine_display !== '' && ! $fine_paid ) : ?>
								<span class="owt7-lms-fine-badge owt7-lms-fine-badge-amount"><?php echo esc_html( $fine_display ); ?></span>
							<?php elseif ( $fine_paid ) : ?>
								<span class="owt7-lms-fine-badge owt7-lms-fine-badge-paid">
									<span class="dashicons dashicons-yes-alt" aria-hidden="true"></span>
									<?php esc_html_e( 'No fine (Paid)', 'library-management-system' ); ?>
								</span>
								<small class="owt7-lms-fine-paid-sub"><?php echo esc_html( $fine_display ); ?></small>
							<?php else : ?>
								<span class="owt7-lms-fine-badge owt7-lms-fine-badge-none"><?php esc_html_e( 'No Fine', 'library-management-system' ); ?></span>
							<?php endif; ?>
						</td>
						<td>
							<div class="owt7-lms-table-actions">
								<a href="javascript:void(0)" class="button button-small owt7-lms-btn-view-book owt7-lms-portal-btn owt7_lms_user_view_borrowed_details" data-modal-title="<?php echo esc_attr__( 'Returned Book Details', 'library-management-system' ); ?>" data-timeline-title="<?php echo esc_attr__( 'Borrow & Return Information', 'library-management-system' ); ?>" data-book-name="<?php echo esc_attr( $book_name ); ?>" data-book-code="<?php echo esc_attr( $book_code ); ?>" data-book-category="<?php echo esc_attr( $category ); ?>" data-book-author="<?php echo esc_attr( $author ); ?>" data-book-publication="<?php echo esc_attr( $pub ); ?>" data-book-isbn="<?php echo esc_attr( $isbn ); ?>" data-book-cost="<?php echo esc_attr( $cost_label ); ?>" data-borrow-record-id="<?php echo esc_attr( $borrow_record_id ); ?>" data-return-record-id="<?php echo esc_attr( $return_record_id ); ?>" data-return-db-id="<?php echo esc_attr( isset( $row->return_record_id ) ? (int) $row->return_record_id : 0 ); ?>" data-fine-amount="<?php echo esc_attr( $fine !== '' ? $fine : 0 ); ?>" data-issue-date="<?php echo esc_attr( $issue ); ?>" data-expected-return-date="<?php echo esc_attr( $ret ); ?>" data-accession-number="<?php echo esc_attr( $acc ); ?>" data-fine-display="<?php echo esc_attr( $fine_modal ); ?>" title="<?php esc_attr_e( 'View', 'library-management-system' ); ?>">
									<span class="dashicons dashicons-visibility" aria-hidden="true"></span>
									<span><?php esc_html_e( 'View', 'library-management-system' ); ?></span>
								</a>
						<?php if ( isset( $row->return_record_id ) && (int) $row->return_record_id > 0 ) : ?>
						<a href="javascript:void(0);"
							class="button button-small owt7-lms-portal-btn receipt-btn owt7_lms_download_receipt_btn"
							data-return-db-id="<?php echo esc_attr( (int) $row->return_record_id ); ?>"
							title="<?php esc_attr_e( 'Download Receipt', 'library-management-system' ); ?>">
							<span class="dashicons dashicons-download" aria-hidden="true"></span>
							<span><?php esc_html_e( 'Download Receipt', 'library-management-system' ); ?></span>
						</a>
						<?php endif; ?>
							</div>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php else : ?>
		<div class="owt7-lms-no-books owt7-lms-empty-state owt7-lms-empty-state-returned">
			<div class="owt7-lms-empty-state-icon" aria-hidden="true">
				<span class="dashicons dashicons-yes-alt"></span>
			</div>
			<h3 class="owt7-lms-empty-state-title"><?php esc_html_e( 'No return history yet', 'library-management-system' ); ?></h3>
			<p class="owt7-lms-empty-state-desc"><?php esc_html_e( 'You have no returned books in history. Books you return will appear here.', 'library-management-system' ); ?></p>
		</div>
		<?php endif; ?>
	</div>
</div>

<div id="owt7_lms_mdl_user_borrowed_details" class="modal owt7-lms-view-return-modal" style="display: none;">
	<div class="modal-content owt7-lms-view-return-content">
		<button type="button" class="close owt7_lms_user_borrowed_details_close" aria-label="<?php esc_attr_e( 'Close', 'library-management-system' ); ?>">&times;</button>
		<h2 id="owt7_lms_user_borrowed_details_title"><?php esc_html_e( 'Returned Book Details', 'library-management-system' ); ?></h2>
		<div class="owt7-lms-view-return-sections">
			<div class="owt7-lms-view-section owt7-lms-view-section-book">
				<h3 class="owt7-lms-view-section-title"><?php esc_html_e( 'Book Information', 'library-management-system' ); ?></h3>
				<div class="owt7-lms-view-detail-body" id="owt7_lms_user_borrowed_book_details"></div>
			</div>
			<div class="owt7-lms-view-section owt7-lms-view-section-remark">
				<h3 class="owt7-lms-view-section-title" id="owt7_lms_user_borrowed_timeline_title"><?php esc_html_e( 'Borrow & Return Information', 'library-management-system' ); ?></h3>
				<div class="owt7-lms-view-detail-body" id="owt7_lms_user_borrowed_timeline_details"></div>
			</div>
		</div>
		<div class="owt7-lms-view-return-actions" id="owt7_lms_user_borrowed_modal_actions">
			<button type="button" class="btn receipt-btn owt7_lms_download_receipt_btn" id="owt7_lms_user_modal_download_receipt_btn" data-return-db-id="" style="display: none;">
				<span class="dashicons dashicons-download" aria-hidden="true"></span>
				<?php esc_html_e( 'Download Receipt', 'library-management-system' ); ?>
			</button>
		</div>
	</div>
</div>
