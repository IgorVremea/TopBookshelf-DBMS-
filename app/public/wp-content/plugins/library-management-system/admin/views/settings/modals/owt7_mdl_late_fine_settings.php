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
$late_fine_per_day   = get_option( 'owt7_lms_late_fine_currency', 0 );
?>
<!-- The Modal -->
<div id="owt7_lms_mdl_late_fine" class="modal" style="display: none;">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>
            <?php if ( ! empty( $late_fine_per_day ) ) { ?>
                <?php _e( 'Update Late Return Fine', 'library-management-system' ); ?>
            <?php } else { ?>
                <?php _e( 'Set Late Return Fine', 'library-management-system' ); ?>
            <?php } ?>
        </h2>
        <p class="owt7-lms-field-hint"><?php _e( 'Configure the per-day fine used when books are returned late.', 'library-management-system' ); ?></p>
        <form class="owt7_lms_form_settings" id="owt7_lms_late_fine_form" method="post" action="javascript:void(0);">
            <?php wp_nonce_field( 'owt7_library_actions', 'owt7_lms_nonce' ); ?>
            <input type="hidden" name="owt7_lms_settings_type" value="late_fine">
            <div class="form-group">
                <label for="owt7_lms_fine_amount"><?php _e( 'Late return (per day):', 'library-management-system' ); ?> <span class="required">*</span></label>
                <input value="<?php echo esc_attr( $late_fine_per_day ); ?>" type="number" min="0" step="0.01" id="owt7_lms_fine_amount" name="owt7_lms_fine_amount" class="form-control"
                    placeholder="<?php esc_attr_e( 'e.g. 0.50 or 1.00', 'library-management-system' ); ?>">
            </div>
            <button type="submit" class="btn"><?php _e( 'Submit & Save', 'library-management-system' ); ?></button>
        </form>
    </div>
</div>
