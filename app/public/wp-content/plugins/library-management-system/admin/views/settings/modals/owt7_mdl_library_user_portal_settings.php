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
$portal = LIBMNS_Admin_FREE::libmns_get_library_user_portal_settings();
?>
<div id="owt7_lms_mdl_library_user_portal_settings" class="modal" style="display: none;">
    <div class="modal-content owt7-lms-required-fields-modal owt7-lms-library-user-portal-settings-modal">
        <span class="close owt7-lms-close-library-user-portal-modal">&times;</span>
        <h2><?php esc_html_e( 'Library User (LMS) Portal Settings', 'library-management-system' ); ?></h2>
        <p class="description"><?php esc_html_e( 'Use this panel to control the Library User admin portal and the shared catalogue filters shown on the public library page.', 'library-management-system' ); ?></p>

        <div class="owt7-lms-portal-settings-pages">
            <div class="owt7-lms-portal-settings-page">
                <span class="dashicons dashicons-book-alt" aria-hidden="true"></span>
                <div>
                    <strong><?php esc_html_e( 'Library User Books List', 'library-management-system' ); ?></strong>
                    <p><?php esc_html_e( 'Admin page: admin.php?page=owt7_library_books_catalogue', 'library-management-system' ); ?></p>
                </div>
            </div>
            <div class="owt7-lms-portal-settings-page">
                <span class="dashicons dashicons-admin-site-alt3" aria-hidden="true"></span>
                <div>
                    <strong><?php esc_html_e( 'Public Library Page', 'library-management-system' ); ?></strong>
                    <p><?php esc_html_e( 'Frontend page: wp-library-books', 'library-management-system' ); ?></p>
                </div>
            </div>
        </div>

        <form id="owt7_lms_library_user_portal_settings_form" method="post" action="javascript:void(0);">
            <?php wp_nonce_field( 'owt7_library_actions', 'owt7_lms_nonce' ); ?>
            <input type="hidden" name="param" value="owt7_lms_save_library_user_portal_settings" />

            <div class="owt7-lms-portal-settings-cards">
                <section class="owt7-lms-portal-settings-card">
                    <div class="owt7-lms-portal-settings-card-head">
                        <h3><?php esc_html_e( 'Catalogue Filters', 'library-management-system' ); ?></h3>
                        <p><?php esc_html_e( 'These three filter settings are shared in one place and apply to both the Library User Books List and the public library page.', 'library-management-system' ); ?></p>
                    </div>
                    <div class="owt7-lms-portal-toggle-grid">
                        <label class="owt7-lms-portal-toggle-card">
                            <span class="owt7-lms-portal-toggle-top">
                                <input type="checkbox" name="library_user_enable_category_filter" value="1" <?php checked( ! empty( $portal['library_user_enable_category_filter'] ) ); ?> />
                                <span class="owt7-lms-portal-toggle-title"><?php esc_html_e( 'Category filter', 'library-management-system' ); ?></span>
                            </span>
                            <span class="owt7-lms-field-hint"><?php esc_html_e( 'Show a category dropdown on both catalogue pages.', 'library-management-system' ); ?></span>
                        </label>
                        <label class="owt7-lms-portal-toggle-card">
                            <span class="owt7-lms-portal-toggle-top">
                                <input type="checkbox" name="library_user_enable_author_filter" value="1" <?php checked( ! empty( $portal['library_user_enable_author_filter'] ) ); ?> />
                                <span class="owt7-lms-portal-toggle-title"><?php esc_html_e( 'Author filter', 'library-management-system' ); ?></span>
                            </span>
                            <span class="owt7-lms-field-hint"><?php esc_html_e( 'Show an author dropdown on both catalogue pages.', 'library-management-system' ); ?></span>
                        </label>
                    </div>
                </section>

                <section class="owt7-lms-portal-settings-card">
                    <div class="owt7-lms-portal-settings-card-head">
                        <h3><?php esc_html_e( 'Books List Layout', 'library-management-system' ); ?></h3>
                        <p><?php esc_html_e( 'These options control only the Library User Books List inside the admin portal.', 'library-management-system' ); ?></p>
                    </div>
                    <div class="owt7-lms-portal-form-row">
                        <div class="form-group owt7-lms-form-row">
                            <label for="library_user_books_per_row"><?php esc_html_e( 'Books per row', 'library-management-system' ); ?></label>
                            <span class="owt7-lms-field-hint"><?php esc_html_e( 'Number of book cards per row on the admin Books List. Default: 4.', 'library-management-system' ); ?></span>
                            <input type="number" name="library_user_books_per_row" id="library_user_books_per_row" class="form-control" min="1" max="6" value="<?php echo esc_attr( isset( $portal['library_user_books_per_row'] ) ? $portal['library_user_books_per_row'] : 4 ); ?>" />
                        </div>
                        <div class="form-group owt7-lms-form-row">
                            <label for="library_user_books_per_page"><?php esc_html_e( 'Books per page', 'library-management-system' ); ?></label>
                            <span class="owt7-lms-field-hint"><?php esc_html_e( 'Pagination size for the admin Books List. Default: 8.', 'library-management-system' ); ?></span>
                            <input type="number" name="library_user_books_per_page" id="library_user_books_per_page" class="form-control" min="3" max="50" value="<?php echo esc_attr( isset( $portal['library_user_books_per_page'] ) ? $portal['library_user_books_per_page'] : 8 ); ?>" />
                        </div>
                    </div>
                    <div class="form-group owt7-lms-form-row">
                        <label for="library_user_card_design"><?php esc_html_e( 'Book card design', 'library-management-system' ); ?></label>
                        <span class="owt7-lms-field-hint"><?php esc_html_e( 'Choose the visual style used in the Library User Books List cards.', 'library-management-system' ); ?></span>
                        <select name="library_user_card_design" id="library_user_card_design" class="form-control">
                            <option value="default" <?php selected( isset( $portal['library_user_card_design'] ) ? $portal['library_user_card_design'] : 'default', 'default' ); ?>><?php esc_html_e( 'Default', 'library-management-system' ); ?></option>
                            <option value="compact" <?php selected( isset( $portal['library_user_card_design'] ) ? $portal['library_user_card_design'] : '', 'compact' ); ?>><?php esc_html_e( 'Compact', 'library-management-system' ); ?></option>
                        </select>
                    </div>
                </section>

            </div>

            <div class="form-row buttons-group" style="margin-top: 1em;">
                <button type="submit" class="btn submit-save-btn"><?php esc_html_e( 'Save Library User portal settings', 'library-management-system' ); ?></button>
            </div>
        </form>
    </div>
</div>
