<?php
/**
 * @link       https://onlinewebtutorblog.com
 * @since      3.5
 * @package    Library_Management_System
 * @subpackage Library_Management_System/admin/views/settings/modals
 * @copyright  Copyright (c) 2026, Online Web Tutor
 * @license    GPL-2.0+ https://www.gnu.org/licenses/gpl-2.0.html
 * @author     Online Web Tutor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$settings = get_option( 'owt7_lms_public_settings', array() );
?>
<div id="owt7_lms_mdl_lms_frontend_settings" class="modal" style="display: none;">
    <div class="modal-content owt7-lms-frontend-settings-modal">
        <span class="close owt7-lms-close-lms-frontend-modal">&times;</span>
        <h2 class="owt7-lms-modal-title"><?php esc_html_e( 'Basic Settings', 'library-management-system' ); ?></h2>
        <p class="owt7-lms-modal-description"><?php esc_html_e( 'Public page display options and default images used across the library.', 'library-management-system' ); ?></p>

        <form action="javascript:void(0)" id="owt7_lms_settings_form" class="owt7_lms_settings_form owt7-lms-frontend-form" method="post">
            <?php wp_nonce_field( 'owt7_library_actions', 'owt7_lms_nonce' ); ?>

            <div class="owt7-lms-frontend-fields">
                <div class="owt7-lms-form-row">
                    <label for="show_books_per_page" class="owt7-lms-field-label"><?php esc_html_e( 'Books per page (Public Page)', 'library-management-system' ); ?></label>
                    <div class="owt7-lms-field-input">
                        <input type="number" name="show_books_per_page" id="show_books_per_page" max="21" min="3" value="<?php echo isset( $settings['show_books_per_page'] ) && ! empty( $settings['show_books_per_page'] ) ? esc_attr( $settings['show_books_per_page'] ) : 3; ?>" placeholder="<?php esc_attr_e( 'e.g. 6', 'library-management-system' ); ?>">
                        <span class="owt7-lms-field-hint"><?php esc_html_e( 'How many books to show on each page of the public library. Choose between 3 and 21.', 'library-management-system' ); ?></span>
                    </div>
                </div>
                <div class="owt7-lms-form-row">
                    <label class="owt7-lms-field-label"><?php esc_html_e( 'Default book cover image', 'library-management-system' ); ?></label>
                    <div class="owt7-lms-field-input">
                        <div class="owt7-lms-settings-media-row">
                            <button type="button" data-module="book" name="owt7_btn_upload_default_cover" id="owt7_btn_upload_default_cover" class="btn btn-outline"><?php esc_html_e( 'Choose image', 'library-management-system' ); ?></button>
                            <img src="<?php echo isset( $settings['default_book_cover_image'] ) && ! empty( $settings['default_book_cover_image'] ) ? esc_url( $settings['default_book_cover_image'] ) : LIBMNS_PLUGIN_URL . 'admin/images/default-cover-image.png'; ?>" class="owt7_settings_image" alt="" id="owt7_cover_image_preview">
                        </div>
                        <input type="hidden" value="<?php echo isset( $settings['default_book_cover_image'] ) && ! empty( $settings['default_book_cover_image'] ) ? esc_attr( $settings['default_book_cover_image'] ) : LIBMNS_PLUGIN_URL . 'admin/images/default-cover-image.png'; ?>" name="default_book_cover_image" id="default_book_cover_image">
                        <span class="owt7-lms-field-hint"><?php esc_html_e( 'This image is shown for any book that does not have its own cover image.', 'library-management-system' ); ?></span>
                    </div>
                </div>
                <div class="owt7-lms-form-row">
                    <label class="owt7-lms-field-label"><?php esc_html_e( 'Default member avatar', 'library-management-system' ); ?></label>
                    <div class="owt7-lms-field-input">
                        <div class="owt7-lms-settings-media-row">
                            <button type="button" data-module="user" name="owt7_btn_upload_default_profile" id="owt7_btn_upload_default_profile" class="btn btn-outline"><?php esc_html_e( 'Choose image', 'library-management-system' ); ?></button>
                            <img src="<?php echo isset( $settings['default_user_profile_image'] ) && ! empty( $settings['default_user_profile_image'] ) ? esc_url( $settings['default_user_profile_image'] ) : LIBMNS_PLUGIN_URL . 'admin/images/default-user-image.png'; ?>" alt="" class="owt7_settings_image owt7-lms-profile-avatar" id="owt7_profile_image_preview">
                        </div>
                        <input type="hidden" value="<?php echo isset( $settings['default_user_profile_image'] ) && ! empty( $settings['default_user_profile_image'] ) ? esc_attr( $settings['default_user_profile_image'] ) : LIBMNS_PLUGIN_URL . 'admin/images/default-user-image.png'; ?>" name="default_user_profile_image" id="default_user_profile_image">
                        <span class="owt7-lms-field-hint"><?php esc_html_e( 'Shown for members who have not uploaded a profile picture.', 'library-management-system' ); ?></span>
                    </div>
                </div>
            </div>
            <div class="form-row buttons-group">
                <button class="btn submit-save-btn" type="submit"><?php esc_html_e( 'Save settings', 'library-management-system' ); ?></button>
            </div>
        </form>
    </div>
</div>
