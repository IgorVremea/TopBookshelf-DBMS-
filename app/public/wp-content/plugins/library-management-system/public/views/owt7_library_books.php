<?php
/**
 * @link       https://onlinewebtutorblog.com
 * @since      3.5
 * @package    Library_Management_System
 * @subpackage Library_Management_System/public/views
 * @copyright  Copyright (c) 2026, Online Web Tutor
 * @license    GPL-2.0+ https://www.gnu.org/licenses/gpl-2.0.html
 * @author     Online Web Tutor
 */
$list_settings = get_option( 'owt7_lms_public_settings', array() );
$enable_list_filter_book_name = ! empty( $list_settings['enable_list_filter_book_name'] );
$enable_list_filter_author      = ! empty( $list_settings['enable_list_filter_author'] );
$enable_list_filter_category   = ! empty( $list_settings['enable_list_filter_category'] );
$show_list_filters = $enable_list_filter_book_name || $enable_list_filter_author || $enable_list_filter_category;
?>
<div class="owt7-lms owt7-lms-public-library">
    <div class="owt7_lms_front_books">
        <?php
        $categories           = isset( $params['categories'] ) && is_array( $params['categories'] ) ? $params['categories'] : array();
        $authors              = isset( $params['authors'] ) && is_array( $params['authors'] ) ? $params['authors'] : array();
        $show_category_filter = ! empty( $params['show_category_filter'] );
        $show_author_filter   = ! empty( $params['show_author_filter'] );
        $show_search_filter   = ! empty( $params['show_search_filter'] );
        $filter_cat           = isset( $params['filter_cat'] ) ? (int) $params['filter_cat'] : 0;
        $filter_author        = isset( $params['filter_author'] ) ? (string) $params['filter_author'] : '';
        $filter_search        = isset( $params['filter_search'] ) ? (string) $params['filter_search'] : '';
        ?>
        <div class="filter-bar">
            <h2 class="book-list-heading"><?php _e('Library Catalog', 'library-management-system') ?></h2>
            <?php if ( $show_category_filter || $show_author_filter || $show_search_filter ) : ?>
            <form method="get" action="<?php echo esc_url( LIBMNS_Public_FREE::owt7_lms_library_base_url() ); ?>" class="filter-dropdowns owt7-lms-public-filter-form">
                <?php if ( $show_category_filter ) : ?>
                <div class="filter-dropdown">
                    <label for="owt7_lms_category_filter"><?php esc_html_e( 'Category', 'library-management-system' ); ?></label>
                    <select id="owt7_lms_category_filter" name="cat">
                        <option value="0"><?php esc_html_e( 'All categories', 'library-management-system' ); ?></option>
                        <?php foreach ( $categories as $category ) : ?>
                            <option value="<?php echo esc_attr( $category->id ); ?>" <?php selected( $filter_cat, (int) $category->id ); ?>>
                                <?php echo esc_html( ucwords( $category->name ) . ' (' . (int) $category->total_books . ')' ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
                <?php if ( $show_author_filter ) : ?>
                <div class="filter-dropdown">
                    <label for="owt7_lms_author_filter"><?php esc_html_e( 'Author', 'library-management-system' ); ?></label>
                    <select id="owt7_lms_author_filter" name="author">
                        <option value=""><?php esc_html_e( 'All authors', 'library-management-system' ); ?></option>
                        <?php foreach ( $authors as $author_name ) : ?>
                            <option value="<?php echo esc_attr( $author_name ); ?>" <?php selected( $filter_author, $author_name ); ?>>
                                <?php echo esc_html( $author_name ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php endif; ?>
                <?php if ( $show_search_filter ) : ?>
                <div class="filter-dropdown filter-dropdown--search">
                    <label for="owt7_lms_search_filter"><?php esc_html_e( 'Free text filter', 'library-management-system' ); ?></label>
                    <input type="text" id="owt7_lms_search_filter" name="search" value="<?php echo esc_attr( $filter_search ); ?>" placeholder="<?php esc_attr_e( 'ISBN, title, author, cost…', 'library-management-system' ); ?>" />
                </div>
                <?php endif; ?>
                <div class="filter-apply-wrap">
                    <button type="submit" class="owt7-lms-filter-apply-btn"><?php _e('Apply', 'library-management-system') ?></button>
                    <a href="<?php echo esc_url( LIBMNS_Public_FREE::owt7_lms_library_base_url() ); ?>" class="owt7-lms-filter-reset-btn"><?php esc_html_e( 'Reset', 'library-management-system' ); ?></a>
                </div>
            </form>
            <?php endif; ?>
        </div>
        <div class="owt7_lms_books_wrap">
            <div class="owt7-lms-books-loader" id="owt7_lms_books_loader" aria-hidden="true">
                <span class="owt7-lms-loader-spinner"></span>
            </div>
            <div id="owt7_lms_books">
                <?php
                ob_start();
                // Template Variables
                $template_file = "owt7_library_all_books";
                include_once LIBMNS_PLUGIN_DIR_PATH . "public/views/templates/{$template_file}.php";
                $template = ob_get_contents();
                ob_end_clean();
                echo $template;
                ?>
            </div>
        </div>
    </div>
</div>

<?php
// Book detail modal (content loaded via AJAX)
?>
<div id="owt7_lms_mdl_book_detail" class="owt7-lms-modal owt7-lms-book-detail-modal" style="display: none;" aria-hidden="true">
	<div class="owt7-lms-modal-overlay"></div>
	<div class="owt7-lms-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="owt7_lms_book_detail_title">
		<div class="owt7-lms-modal-content">
			<div class="owt7-lms-modal-header">
				<h2 id="owt7_lms_book_detail_title" class="owt7-lms-modal-title"><?php esc_html_e( 'Book Details', 'library-management-system' ); ?></h2>
				<button type="button" class="owt7-lms-modal-close" aria-label="<?php esc_attr_e( 'Close', 'library-management-system' ); ?>">&times;</button>
			</div>
			<div class="owt7-lms-modal-body">
				<div id="owt7_lms_book_detail_content" class="owt7-lms-book-detail-content">
					<div class="owt7-lms-book-detail-loading"><span class="owt7-lms-loader-spinner"></span></div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
// Return Status modal for library user (public shortcode)
?>
<div id="owt7_lms_mdl_user_return_status" class="modal owt7-lms-return-status-modal" style="display: none;">
	<div class="modal-content">
		<span class="close">&times;</span>
		<h2><?php esc_html_e( 'Return Status & Fine', 'library-management-system' ); ?></h2>
		<p class="return-section-desc"><?php esc_html_e( 'Select the condition of the returned book and add a remark if needed. Total fine will be shown based on your selection.', 'library-management-system' ); ?></p>
		<div class="form-group">
			<label for="owt7_user_return_condition"><?php esc_html_e( 'Return status', 'library-management-system' ); ?> <span class="required">*</span></label>
			<select id="owt7_user_return_condition" name="owt7_return_condition" class="form-control" required>
				<option value="normal_return"><?php esc_html_e( 'Normal return', 'library-management-system' ); ?></option>
				<option value="lost_book"><?php esc_html_e( 'Lost book', 'library-management-system' ); ?></option>
				<option value="late_return"><?php esc_html_e( 'Late return', 'library-management-system' ); ?></option>
			</select>
		</div>
		<div class="form-group">
			<label for="owt7_user_return_remark"><?php esc_html_e( 'Remark', 'library-management-system' ); ?></label>
			<textarea id="owt7_user_return_remark" name="owt7_return_remark" class="form-control" rows="3" placeholder="<?php esc_attr_e( 'Optional notes about the return', 'library-management-system' ); ?>"></textarea>
		</div>
		<div class="form-group owt7-return-total-fine-wrap">
			<span class="owt7-lms-field-hint"><?php esc_html_e( 'Total late fine:', 'library-management-system' ); ?></span>
			<strong id="owt7_user_return_total_fine_display" class="owt7_return_total_fine_display">0 <?php echo esc_html( get_option( 'owt7_lms_currency', '' ) ); ?></strong>
		</div>
		<div class="form-row buttons-group">
			<button type="button" class="btn submit-save-btn" id="owt7_user_return_status_modal_submit"><?php esc_html_e( 'Submit', 'library-management-system' ); ?></button>
		</div>
	</div>
</div>