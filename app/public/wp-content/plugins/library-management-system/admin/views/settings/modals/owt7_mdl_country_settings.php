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
?>
<!-- The Modal -->
<div id="owt7_lms_mdl_settings" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2><?php _e('Country & Currency Settings', 'library-management-system'); ?></h2>
        <form class="owt7_lms_form_settings" id="owt7_lms_country_form" method="post" action="javascript:void(0);">
            <?php wp_nonce_field('owt7_library_actions', 'owt7_lms_nonce'); ?>
            <input type="hidden" name="owt7_lms_settings_type" value="country_currency">
            <div class="form-group">
                <label for="owt7_lms_country"><?php _e('Country', 'library-management-system'); ?> <span class="required">*</span></label>
                <input value="<?php echo get_option( 'owt7_lms_country' ) ?>" type="text" id="owt7_lms_country" name="owt7_lms_country" class="form-control"
                placeholder="<?php esc_attr_e( 'e.g. United States', 'library-management-system' ); ?>" required>
            </div>
            <div class="form-group">
                <label for="owt7_lms_currency"><?php _e('Currency', 'library-management-system'); ?> <span class="required">*</span></label>
                <input value="<?php echo get_option( 'owt7_lms_currency' ) ?>" type="text" id="owt7_lms_currency" name="owt7_lms_currency" class="form-control"
                placeholder="<?php esc_attr_e( 'e.g. USD, EUR', 'library-management-system' ); ?>" required>
            </div>

            <button type="submit" class="btn"><?php _e('Submit & Save', 'library-management-system'); ?></button>
        </form>
    </div>
</div>
