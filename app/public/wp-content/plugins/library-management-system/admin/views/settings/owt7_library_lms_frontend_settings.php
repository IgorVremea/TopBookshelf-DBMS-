<?php
/**
 * @link       https://onlinewebtutorblog.com
 * @since      3.5
 * @package    Library_Management_System
 * @subpackage Library_Management_System/admin/views/settings
 * @copyright  Copyright (c) 2026, Online Web Tutor
 * @license    GPL-2.0+ https://www.gnu.org/licenses/gpl-2.0.html
 * @author     Online Web Tutor
 */
?>
<div class="owt7-lms owt7-lms-settings owt7-lms-frontend-settings">

    <div class="owt7_lms_settings">

        <div class="page-header">
            <div class="breadcrumb"> <?php _e('Library System', 'library-management-system'); ?> &rarr; <span class="active"><?php _e('Basic Settings', 'library-management-system'); ?></span> </div>
            <div class="page-actions">
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=owt7_library_settings#general' ) ); ?>" class="btn"><span class="dashicons dashicons-arrow-left-alt"></span> <?php _e('Back', 'library-management-system'); ?></a>
            </div>
        </div>

        <div class="page-container">

            <div class="page-title">
                <h2><?php _e('Basic Settings', 'library-management-system'); ?></h2>
                <p class="page-description"><?php _e('Public page display options and default images.', 'library-management-system'); ?></p>
            </div>

            <?php $settings = get_option("owt7_lms_public_settings"); ?>

            <form action="javascript:void(0)" id="owt7_lms_settings_form" class="owt7_lms_settings_form" method="post">

                <?php wp_nonce_field( 'owt7_library_actions', 'owt7_lms_nonce' ); ?>

                <!-- Basic Settings -->
                <fieldset class="owt7_lms_fieldset owt7-lms-frontend-section">
                    <legend><?php _e('Basic Settings', 'library-management-system'); ?></legend>
                    <table class="owt7-lms-table owt7-lms-settings-table">
                        <tbody>
                            <tr>
                                <th scope="row">
                                    <label for="show_books_per_page"><?php _e('Books per page (Public Page)', 'library-management-system'); ?></label>
                                    <span class="owt7-lms-field-hint"><?php _e('How many books to show on each page of the public library. Choose between 3 and 21.', 'library-management-system'); ?></span>
                                </th>
                                <td>
                                    <input type="number" name="show_books_per_page" id="show_books_per_page" max="21" min="3" value="<?php echo isset($settings['show_books_per_page']) && !empty($settings['show_books_per_page']) ? esc_attr($settings['show_books_per_page']) : 3; ?>" placeholder="<?php esc_attr_e( 'e.g. 6', 'library-management-system' ); ?>">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label><?php _e('Default book cover image', 'library-management-system'); ?></label>
                                    <span class="owt7-lms-field-hint"><?php _e('This image is shown for any book that does not have its own cover image.', 'library-management-system'); ?></span>
                                </th>
                                <td>
                                    <div class="owt7-lms-settings-media-row">
                                        <button type="button" data-module="book" name="owt7_btn_upload_default_cover" id="owt7_btn_upload_default_cover" class="btn btn-outline"><?php _e('Choose image', 'library-management-system'); ?></button>
                                        <img src="<?php echo isset($settings['default_book_cover_image']) && !empty($settings['default_book_cover_image']) ? esc_url($settings['default_book_cover_image']) : LIBMNS_PLUGIN_URL . 'admin/images/default-cover-image.png'; ?>" class="owt7_settings_image" alt="" id="owt7_cover_image_preview">
                                    </div>
                                    <input type="hidden" value="<?php echo isset($settings['default_book_cover_image']) && !empty($settings['default_book_cover_image']) ? esc_attr($settings['default_book_cover_image']) : LIBMNS_PLUGIN_URL . 'admin/images/default-cover-image.png'; ?>" name="default_book_cover_image" id="default_book_cover_image">
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">
                                    <label><?php _e('Default member avatar', 'library-management-system'); ?></label>
                                    <span class="owt7-lms-field-hint"><?php _e('Shown for members who have not uploaded a profile picture.', 'library-management-system'); ?></span>
                                </th>
                                <td>
                                    <div class="owt7-lms-settings-media-row">
                                        <button type="button" data-module="user" name="owt7_btn_upload_default_profile" id="owt7_btn_upload_default_profile" class="btn btn-outline"><?php _e('Choose image', 'library-management-system'); ?></button>
                                        <img src="<?php echo isset($settings['default_user_profile_image']) && !empty($settings['default_user_profile_image']) ? esc_url($settings['default_user_profile_image']) : LIBMNS_PLUGIN_URL . 'admin/images/default-user-image.png'; ?>" alt="" class="owt7_settings_image owt7-lms-profile-avatar" id="owt7_profile_image_preview">
                                    </div>
                                    <input type="hidden" value="<?php echo isset($settings['default_user_profile_image']) && !empty($settings['default_user_profile_image']) ? esc_attr($settings['default_user_profile_image']) : LIBMNS_PLUGIN_URL . 'admin/images/default-user-image.png'; ?>" name="default_user_profile_image" id="default_user_profile_image">
                                </td>
                            </tr>
                            <tr class="owt7-lms-settings-submit-row">
                                <th scope="row"></th>
                                <td>
                                    <div class="form-row buttons-group">
                                        <button class="btn submit-save-btn" type="submit"><?php _e('Save settings', 'library-management-system'); ?></button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </fieldset>
            </form>
        </div>
    </div>
</div>
