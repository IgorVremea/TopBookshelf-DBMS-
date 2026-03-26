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
?>
<div class="owt7-lms">
    <div class="owt7_lms_front_books">
        <div class="filter-bar">
            <h2 class="book-list-heading"><?php _e('Book List', 'library-management-system') ?></h2>
            <div class="tabs">
                <button class="tablink active" onclick="openTab(event, 'BooksBorrowed')"><?php esc_html_e('Books Borrowed', 'library-management-system'); ?></button>
                <button class="tablink" onclick="openTab(event, 'BooksReturned')"><?php esc_html_e('Books Returned', 'library-management-system'); ?></button>
            </div>
        </div>

        <!-- Books Borrowed List -->
        <div id="BooksBorrowed" class="tabcontent active">
            <div class="book-list-container" id="owt7_lib_book_container_borrowed">
                <?php
                if(is_array($params['borrowed_books_list']) && count($params['borrowed_books_list']) > 0){
                    foreach($params['borrowed_books_list'] as $book){
                ?>
                <div class="book-card">
                    <?php if(!empty($book->cover_image)){ ?>
                    <div class="book-cover">
                        <img src="<?php echo $book->cover_image; ?>" alt="<?php echo ucwords($book->name); ?>">
                    </div>
                    <?php }else{ ?>
                    <img src="<?php echo LIBMNS_PLUGIN_URL . 'public/images/default-cover-image.png'; ?>"
                        alt="<?php echo ucwords($book->name); ?>">
                    <?php } ?>
                    <div class="book-details">
                        <h3 class="book-name"><strong><?php echo ucwords($book->name); ?></strong></h3>
                        <p class="book-category"><strong><?php esc_html_e('Category:', 'library-management-system'); ?></strong> <?php echo esc_html(ucwords($book->category_name)); ?></p>
                        <p class="book-author"><strong><?php esc_html_e('Author:', 'library-management-system'); ?></strong> <?php echo LIBMNS_Admin_FREE::libmns_render_comma_tags( $book->author_name ) ?: '—'; ?></p>
                        <p class="book-quantity"><strong><?php esc_html_e('Publication Name:', 'library-management-system'); ?></strong> <?php echo LIBMNS_Admin_FREE::libmns_render_comma_tags( $book->publication_name ) ?: '—'; ?></p>
                        <p class="book-status">
                            <strong><?php esc_html_e('Status:', 'library-management-system'); ?></strong>
                            <?php if($book->status){ ?>
                            <a href="javascript:void(0)" class="owt7_lms_front_btns owt7_lms_book_available"><?php esc_html_e('Available', 'library-management-system'); ?></a>
                            <?php } else{ ?>
                            <a href="javascript:void(0)" class="owt7_lms_front_btns owt7_lms_book_not_available"><?php esc_html_e('Not Available', 'library-management-system'); ?></a>
                            <?php } ?>
                        </p>
                    </div>
                    <div class="book-footer">
                        
                        <a title="<?php esc_attr_e('View', 'library-management-system'); ?>" href="<?php echo esc_url(LIBMNS_Public_FREE::owt7_lms_book_detail_url($book->id)); ?>"
                            class="view-book-btn">
                            <?php esc_html_e('View Book', 'library-management-system'); ?>
                        </a>

                        <a title="<?php esc_attr_e('Return', 'library-management-system'); ?>" data-id="<?php echo esc_attr(base64_encode($book->id)); ?>" href="javascript:void(0)"
                            class="view-book-btn <?php echo 'owt7_lms_user_do_return'; ?>">
                            <?php esc_html_e('Return', 'library-management-system'); ?>
                        </a>
                    </div>
                </div>
                <?php
                    }
                }
                ?>
            </div>
            <div class="pagination">
                <?php
                if ($params['total_borrowed_pages'] > 1) {
                    for ($i = 1; $i <= $params['total_borrowed_pages']; $i++) {
                        if ($i == $params['current_page']) {
                            echo '<span class="current-page">' . $i . '</span>';
                        } else {
                            echo '<a href="' . esc_url(LIBMNS_Public_FREE::owt7_lms_library_page_url($i)) . '">' . $i . '</a>';
                        }
                    }
                }
                ?>
            </div>
        </div>

        <!-- Books Returned List -->
        <div id="BooksReturned" class="tabcontent">
            <div class="book-list-container" id="owt7_lib_book_container_returned">
                <?php
                if(is_array($params['returned_books_list']) && count($params['returned_books_list']) > 0){
                    foreach($params['returned_books_list'] as $book){
                ?>
                <div class="book-card">
                    <?php if(!empty($book->cover_image)){ ?>
                    <div class="book-cover">
                        <img src="<?php echo $book->cover_image; ?>" alt="<?php echo ucwords($book->name); ?>">
                    </div>
                    <?php }else{ ?>
                    <img src="<?php echo LIBMNS_PLUGIN_URL . 'public/images/default-cover-image.png'; ?>"
                        alt="<?php echo ucwords($book->name); ?>">
                    <?php } ?>
                    <div class="book-details">
                        <h3 class="book-name"><strong><?php echo ucwords($book->name); ?></strong></h3>
                        <p class="book-category"><strong><?php esc_html_e('Category:', 'library-management-system'); ?></strong> <?php echo esc_html(ucwords($book->category_name)); ?></p>
                        <p class="book-author"><strong><?php esc_html_e('Author:', 'library-management-system'); ?></strong> <?php echo LIBMNS_Admin_FREE::libmns_render_comma_tags( $book->author_name ) ?: '—'; ?></p>
                        <p class="book-quantity"><strong><?php esc_html_e('Publication Name:', 'library-management-system'); ?></strong> <?php echo LIBMNS_Admin_FREE::libmns_render_comma_tags( $book->publication_name ) ?: '—'; ?></p>
                        <p class="book-status">
                            <strong><?php esc_html_e('Status:', 'library-management-system'); ?></strong>
                            <?php if($book->status){ ?>
                            <a href="javascript:void(0)" class="owt7_lms_front_btns owt7_lms_book_available"><?php esc_html_e('Available', 'library-management-system'); ?></a>
                            <?php } else{ ?>
                            <a href="javascript:void(0)" class="owt7_lms_front_btns owt7_lms_book_not_available"><?php esc_html_e('Not Available', 'library-management-system'); ?></a>
                            <?php } ?>
                        </p>
                    </div>
                    <div class="book-footer">
                        
                        <a title="<?php esc_attr_e('View', 'library-management-system'); ?>" href="<?php echo esc_url(LIBMNS_Public_FREE::owt7_lms_book_detail_url($book->id)); ?>"
                            class="view-book-btn">
                            <?php esc_html_e('View Book', 'library-management-system'); ?>
                        </a>
                        
                        <a title="<?php esc_attr_e('Checkout', 'library-management-system'); ?>" data-id="<?php echo esc_attr(base64_encode($book->id)); ?>" href="javascript:void(0)"
                            class="view-book-btn <?php echo 'owt7_lms_user_do_checkout'; ?>">
                            <?php esc_html_e('Checkout', 'library-management-system'); ?>
                        </a>

                    </div>
                </div>
                <?php
                    }
                }
                ?>
            </div>
            <div class="pagination">
                <?php
                if ($params['total_returned_pages'] > 1) {
                    for ($i = 1; $i <= $params['total_returned_pages']; $i++) {
                        if ($i == $params['current_page']) {
                            echo '<span class="current-page">' . $i . '</span>';
                        } else {
                            echo '<a href="' . esc_url(LIBMNS_Public_FREE::owt7_lms_library_page_url($i)) . '">' . $i . '</a>';
                        }
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>

<?php
// Return Status modal (same structure as owt7_library_books for JS compatibility)
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
