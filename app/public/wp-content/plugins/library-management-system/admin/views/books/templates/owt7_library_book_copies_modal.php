<?php
/**
 * Read-only modal to list all copies for a book.
 *
 * @link       https://onlinewebtutorblog.com
 * @since      3.5
 * @package    Library_Management_System
 * @subpackage Library_Management_System/admin/views/books/templates
 * @copyright  Copyright (c) 2026, Online Web Tutor
 * @license    GPL-2.0+ https://www.gnu.org/licenses/gpl-2.0.html
 * @author     Online Web Tutor
 */
?>
<div class="owt7_lms_modal_section owt7_lms_book_copies_modal_wrap">
<div id="owt7_lms_mdl_book_copies" class="modal" style="display: none;">
    <div class="modal-content owt7-lms-book-copies-modal">
        <span class="close owt7-lms-close-book-copies-modal">&times;</span>
        <h2 class="owt7-lms-book-copies-modal-heading"><?php _e( 'Book Copies', 'library-management-system' ); ?></h2>
        <p id="owt7_lms_book_copies_modal_title" class="owt7-lms-book-copies-modal-desc"></p>
        <div id="owt7_lms_book_copies_loading" class="owt7-lms-book-copies-loading" style="display: none;">
            <div class="owt7-lms-css-loader"></div>
            <span class="owt7-lms-book-copies-loading-text"><?php _e( 'Loading copies...', 'library-management-system' ); ?></span>
        </div>
        <div id="owt7_lms_book_copies_content" class="owt7-lms-book-copies-content" style="display: none;" data-book-id="">
            <div class="owt7-lms-book-copies-table-wrap">
                <table class="owt7-lms-table owt7-lms-book-copies-table">
                    <thead>
                        <tr>
                            <th><?php _e( '#', 'library-management-system' ); ?></th>
                            <th><?php _e( 'Accession Number', 'library-management-system' ); ?></th>
                            <th><?php _e( 'Status', 'library-management-system' ); ?></th>
                            <th><?php _e( 'Shelf Location', 'library-management-system' ); ?></th>
                        </tr>
                    </thead>
                    <tbody id="owt7_lms_book_copies_tbody">
                    </tbody>
                </table>
            </div>
            <p id="owt7_lms_book_copies_empty" class="owt7-lms-book-copies-empty description" style="display: none;"><?php _e( 'No copies found for this book.', 'library-management-system' ); ?></p>
        </div>
        <div id="owt7_lms_book_copies_error" class="owt7-lms-book-copies-error notice notice-error" style="display: none;"></div>
    </div>
</div>
</div>
