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
 * Uses parent class .owt7-lms .owt7-lms-books-catalogue for layout and cards.
 * Data: $params['books'], $params['categories'], $params['authors'], $params['portal'],
 *       $params['per_row'], $params['total_pages'], $params['current_page'],
 *       $params['filter_cat'], $params['filter_author'], $params['filter_search'].
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$books         = isset( $params['books'] ) ? $params['books'] : array();
$categories    = isset( $params['categories'] ) ? $params['categories'] : array();
$authors       = isset( $params['authors'] ) ? $params['authors'] : array();
$portal        = isset( $params['portal'] ) ? $params['portal'] : array();
$per_row       = isset( $params['per_row'] ) ? (int) $params['per_row'] : 4;
$total_pages   = isset( $params['total_pages'] ) ? (int) $params['total_pages'] : 1;
$current_page  = isset( $params['current_page'] ) ? (int) $params['current_page'] : 1;
$filter_cat    = isset( $params['filter_cat'] ) ? (int) $params['filter_cat'] : 0;
$filter_author = isset( $params['filter_author'] ) ? $params['filter_author'] : '';
$filter_search = isset( $params['filter_search'] ) ? $params['filter_search'] : '';
$user_borrowed_book_ids = isset( $params['user_borrowed_book_ids'] ) && is_array( $params['user_borrowed_book_ids'] ) ? $params['user_borrowed_book_ids'] : array();

$catalogue_url   = admin_url( 'admin.php?page=owt7_library_books_catalogue' );
$library_public_url = home_url( 'wp-library-books' );
$enable_cat_filter   = ! empty( $portal['library_user_enable_category_filter'] );
$enable_author_filter = ! empty( $portal['library_user_enable_author_filter'] );
$enable_search       = ! empty( $portal['library_user_enable_search'] );
$card_design         = isset( $portal['library_user_card_design'] ) ? $portal['library_user_card_design'] : 'default';
$total_books         = isset( $params['total_books'] ) ? (int) $params['total_books'] : 0;
$checkout_overview   = isset( $params['checkout_overview'] ) && is_array( $params['checkout_overview'] ) ? $params['checkout_overview'] : array();
?>
<div class="owt7-lms owt7-lms-books-catalogue" style="--owt7-cards-per-row: <?php echo esc_attr( $per_row ); ?>;" data-card-design="<?php echo esc_attr( $card_design ); ?>">

	<div class="page-container lms-books-catalogue">
		<div class="owt7-lms-catalogue-hero">
			<div class="owt7-lms-catalogue-hero-copy">
				<h1 class="page-title">
					<?php esc_html_e( 'Books Catalogue', 'library-management-system' ); ?>
				</h1>
			</div>
			<a href="<?php echo esc_url( $library_public_url ); ?>" target="_blank" rel="noopener noreferrer" class="button button-primary owt7-lms-nav-to-library-page">
				<span class="dashicons dashicons-external" aria-hidden="true"></span>
				<?php esc_html_e( 'Navigate to Library Page', 'library-management-system' ); ?>
			</a>
		</div>

		<?php if ( $enable_cat_filter || $enable_author_filter || $enable_search ) : ?>
		<div class="owt7-lms-catalogue-filters">
			<div class="owt7-lms-catalogue-filters-head">
				<div>
					<h2><?php esc_html_e( 'Find books quickly', 'library-management-system' ); ?></h2>
					<p><?php esc_html_e( 'Apply any combination of category, author, and free text filters. Results update instantly without leaving the page.', 'library-management-system' ); ?></p>
				</div>
			</div>
			<form method="get" action="<?php echo esc_url( admin_url( 'admin.php' ) ); ?>" class="owt7-lms-filters-form" id="owt7_lms_catalogue_filters_form">
				<input type="hidden" name="page" value="owt7_library_books_catalogue" />
				<div class="owt7-lms-filters-row">
					<?php if ( $enable_cat_filter ) : ?>
					<div class="owt7-lms-filter-group">
						<label for="owt7-filter-cat"><?php esc_html_e( 'Category', 'library-management-system' ); ?></label>
						<select name="cat" id="owt7-filter-cat" class="owt7-lms-filter-select">
							<option value="0"><?php esc_html_e( 'All categories', 'library-management-system' ); ?></option>
							<?php foreach ( $categories as $cat ) : 
								$count = isset( $cat->book_count ) ? (int) $cat->book_count : 0;
								$label = $cat->name . ' (' . $count . ')';
							?>
								<option value="<?php echo esc_attr( $cat->id ); ?>" <?php selected( $filter_cat, (int) $cat->id ); ?>><?php echo esc_html( $label ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<?php endif; ?>

					<?php if ( $enable_author_filter ) : ?>
					<div class="owt7-lms-filter-group">
						<label for="owt7-filter-author"><?php esc_html_e( 'Author', 'library-management-system' ); ?></label>
						<select name="author" id="owt7-filter-author" class="owt7-lms-filter-select">
							<option value=""><?php esc_html_e( 'All authors', 'library-management-system' ); ?></option>
							<?php foreach ( $authors as $auth ) : 
								$an = isset( $auth->author_name ) ? trim( $auth->author_name ) : '';
								if ( $an === '' ) continue;
							?>
								<option value="<?php echo esc_attr( $an ); ?>" <?php selected( $filter_author, $an ); ?>><?php echo esc_html( $an ); ?></option>
							<?php endforeach; ?>
						</select>
					</div>
					<?php endif; ?>

					<?php if ( $enable_search ) : ?>
					<div class="owt7-lms-filter-group owt7-lms-filter-search">
						<label for="owt7-filter-search"><?php esc_html_e( 'Search', 'library-management-system' ); ?></label>
						<input type="text" name="search" id="owt7-filter-search" class="owt7-lms-filter-input" value="<?php echo esc_attr( $filter_search ); ?>" placeholder="<?php esc_attr_e( 'ISBN, title, author, cost…', 'library-management-system' ); ?>" />
					</div>
					<?php endif; ?>

					<div class="owt7-lms-filter-actions">
						<button type="submit" class="button button-primary owt7-lms-apply-filters">
							<?php esc_html_e( 'Apply', 'library-management-system' ); ?>
						</button>
						<a href="<?php echo esc_url( $catalogue_url ); ?>" class="button owt7-lms-reset-filters"><?php esc_html_e( 'Reset', 'library-management-system' ); ?></a>
					</div>
				</div>
			</form>
		</div>
		<?php endif; ?>

		<div class="owt7-lms-catalogue-results-wrap">
			<div class="owt7-lms-catalogue-loader" id="owt7_lms_catalogue_loader" aria-hidden="true">
				<span class="owt7-lms-css-loader owt7-lms-loader-lg"></span>
			</div>
			<div id="owt7_lms_catalogue_results">
				<?php include LIBMNS_PLUGIN_DIR_PATH . 'admin/views/library_user/templates/owt7_library_books_catalogue_results.php'; ?>
			</div>
		</div>
	</div>

	<!-- Book detail modal (Library User portal) -->
	<div id="owt7_lms_mdl_book_detail" class="modal owt7-lms-modal-book-detail owt7-lms-modal-closed" aria-hidden="true">
		<div class="modal-content owt7-lms-modal-book-detail-content">
			<span class="close owt7-lms-close-book-detail-modal" aria-label="<?php esc_attr_e( 'Close', 'library-management-system' ); ?>">&times;</span>
			<div class="owt7-lms-book-detail-modal-inner">
				<div class="owt7-lms-book-detail-cover-wrap">
					<img id="owt7_lms_mdl_book_cover" src="" alt="" class="owt7-lms-book-detail-cover" style="display: none;" />
					<div id="owt7_lms_mdl_book_cover_placeholder" class="owt7-lms-book-detail-cover-placeholder">
						<span class="dashicons dashicons-book-alt" aria-hidden="true"></span>
					</div>
				</div>
				<div class="owt7-lms-book-detail-body">
					<h2 id="owt7_lms_mdl_book_title" class="owt7-lms-book-detail-title"></h2>
					<dl class="owt7-lms-book-detail-meta">
						<dt><?php esc_html_e( 'Author', 'library-management-system' ); ?></dt>
						<dd id="owt7_lms_mdl_book_author">—</dd>
						<dt><?php esc_html_e( 'Category', 'library-management-system' ); ?></dt>
						<dd id="owt7_lms_mdl_book_category">—</dd>
						<dt><?php esc_html_e( 'Publication', 'library-management-system' ); ?></dt>
						<dd id="owt7_lms_mdl_book_publication">—</dd>
						<dt><?php esc_html_e( 'Publication year', 'library-management-system' ); ?></dt>
						<dd id="owt7_lms_mdl_book_year">—</dd>
						<dt><?php esc_html_e( 'ISBN', 'library-management-system' ); ?></dt>
						<dd id="owt7_lms_mdl_book_isbn">—</dd>
						<dt><?php esc_html_e( 'Cost', 'library-management-system' ); ?></dt>
						<dd id="owt7_lms_mdl_book_amount">—</dd>
					</dl>
					<div class="owt7-lms-book-detail-description-wrap">
						<h3 class="owt7-lms-book-detail-description-title"><?php esc_html_e( 'Description', 'library-management-system' ); ?></h3>
						<div id="owt7_lms_mdl_book_description" class="owt7-lms-book-detail-description">—</div>
					</div>
					<div id="owt7_lms_mdl_book_actions" class="owt7-lms-book-detail-actions"></div>
				</div>
			</div>
		</div>
	</div>

	<?php include LIBMNS_PLUGIN_DIR_PATH . 'admin/views/library_user/templates/owt7_library_checkout_overview_modal.php'; ?>
</div>
