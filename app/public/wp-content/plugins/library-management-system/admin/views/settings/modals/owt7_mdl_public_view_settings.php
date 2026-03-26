<?php
/**
 * Modal: Manage Public Design View (Library) – cards per row, fonts, button placement, card bg, button padding.
 *
 * @link       https://onlinewebtutorblog.com
 * @since      3.5
 * @package    Library_Management_System
 * @subpackage Library_Management_System/admin/views/settings/modals
 * @copyright  Copyright (c) 2026, Online Web Tutor
 * @license    GPL-2.0+ https://www.gnu.org/licenses/gpl-2.0.html
 * @author     Online Web Tutor
 */

$public_view_settings = isset( $public_view_settings ) ? $public_view_settings : array();
$cards_per_row        = isset( $public_view_settings['cards_per_row'] ) ? $public_view_settings['cards_per_row'] : '3';
$heading_font_size    = isset( $public_view_settings['heading_font_size'] ) ? $public_view_settings['heading_font_size'] : '18px';
$body_font_size       = isset( $public_view_settings['body_font_size'] ) ? $public_view_settings['body_font_size'] : '14px';
$view_btn_placement   = isset( $public_view_settings['view_btn_placement'] ) ? $public_view_settings['view_btn_placement'] : 'right';
$card_bg_color       = isset( $public_view_settings['card_bg_color'] ) ? $public_view_settings['card_bg_color'] : '#ffffff';
$view_btn_padding    = isset( $public_view_settings['view_btn_padding'] ) ? $public_view_settings['view_btn_padding'] : '4px 9px';
$view_btn_font_size  = isset( $public_view_settings['view_btn_font_size'] ) ? $public_view_settings['view_btn_font_size'] : '12px';
$view_btn_color      = isset( $public_view_settings['view_btn_color'] ) ? $public_view_settings['view_btn_color'] : '#1d2065';
$checkout_btn_color  = isset( $public_view_settings['checkout_btn_color'] ) ? $public_view_settings['checkout_btn_color'] : '#0d9488';
if ( ! preg_match( '/^#[0-9a-fA-F]{6}$/', $card_bg_color ) ) { $card_bg_color = '#ffffff'; }
if ( ! preg_match( '/^#[0-9a-fA-F]{6}$/', $view_btn_color ) ) { $view_btn_color = '#1d2065'; }
if ( ! preg_match( '/^#[0-9a-fA-F]{6}$/', $checkout_btn_color ) ) { $checkout_btn_color = '#0d9488'; }
?>
<div id="owt7_lms_mdl_public_view" class="modal" style="display: none;">
    <div class="modal-content owt7-lms-public-view-modal">
        <span class="close owt7-lms-close-public-view-modal">&times;</span>
        <h2><?php _e( 'Manage Public Design View (Library)', 'library-management-system' ); ?></h2>
        <p class="description"><?php _e( 'These settings control the book list appearance on the public Library page (e.g. wp-library-books/).', 'library-management-system' ); ?></p>
        <form id="owt7_lms_public_view_form" method="post" action="javascript:void(0);">
            <?php wp_nonce_field( 'owt7_library_actions', 'owt7_lms_nonce' ); ?>
            <input type="hidden" name="param" value="owt7_lms_save_public_view_settings">
            <div class="owt7-lms-public-view-two-col">
                <div class="form-group owt7-lms-form-row">
                    <label for="owt7_lms_cards_per_row"><?php _e( 'Number of cards per row', 'library-management-system' ); ?></label>
                    <select name="cards_per_row" id="owt7_lms_cards_per_row" class="form-control">
                        <?php for ( $i = 1; $i <= 6; $i++ ) : ?>
                            <option value="<?php echo esc_attr( (string) $i ); ?>" <?php selected( $cards_per_row, (string) $i ); ?>><?php echo (int) $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="form-group owt7-lms-form-row">
                    <label for="owt7_lms_view_btn_placement"><?php _e( 'View button placement in card', 'library-management-system' ); ?></label>
                    <select name="view_btn_placement" id="owt7_lms_view_btn_placement" class="form-control">
                        <option value="left" <?php selected( $view_btn_placement, 'left' ); ?>><?php _e( 'Left', 'library-management-system' ); ?></option>
                        <option value="center" <?php selected( $view_btn_placement, 'center' ); ?>><?php _e( 'Center', 'library-management-system' ); ?></option>
                        <option value="right" <?php selected( $view_btn_placement, 'right' ); ?>><?php _e( 'Right', 'library-management-system' ); ?></option>
                    </select>
                </div>
            </div>
            <div class="owt7-lms-public-view-two-col">
                <div class="form-group owt7-lms-form-row">
                    <label for="owt7_lms_heading_font_size"><?php _e( 'Heading font size (e.g. 18px, 1.25rem)', 'library-management-system' ); ?></label>
                    <input type="text" name="heading_font_size" id="owt7_lms_heading_font_size" class="form-control" value="<?php echo esc_attr( $heading_font_size ); ?>" placeholder="18px">
                </div>
                <div class="form-group owt7-lms-form-row">
                    <label for="owt7_lms_body_font_size"><?php _e( 'Other text font size (e.g. 14px)', 'library-management-system' ); ?></label>
                    <input type="text" name="body_font_size" id="owt7_lms_body_font_size" class="form-control" value="<?php echo esc_attr( $body_font_size ); ?>" placeholder="14px">
                </div>
            </div>
            <div class="owt7-lms-public-view-two-col">
                <div class="form-group owt7-lms-form-row">
                    <label for="owt7_lms_card_bg_color"><?php _e( 'Card background color', 'library-management-system' ); ?></label>
                    <div class="owt7-lms-color-row">
                        <input type="color" name="card_bg_color" id="owt7_lms_card_bg_color" class="owt7-lms-color-picker" value="<?php echo esc_attr( $card_bg_color ); ?>">
                        <input type="text" class="form-control owt7-lms-color-hex" maxlength="7" placeholder="#ffffff" data-for="owt7_lms_card_bg_color" value="<?php echo esc_attr( $card_bg_color ); ?>">
                    </div>
                </div>
                <div class="form-group owt7-lms-form-row">
                    <label for="owt7_lms_view_btn_padding"><?php _e( 'View button padding (e.g. 8px 12px or 8 12)', 'library-management-system' ); ?></label>
                    <input type="text" name="view_btn_padding" id="owt7_lms_view_btn_padding" class="form-control" value="<?php echo esc_attr( $view_btn_padding ); ?>" placeholder="4px 9px">
                </div>
            </div>
            <div class="owt7-lms-public-view-two-col">
                <div class="form-group owt7-lms-form-row">
                    <label for="owt7_lms_view_btn_font_size"><?php _e( 'Font size of button (e.g. 12px, 1rem)', 'library-management-system' ); ?></label>
                    <input type="text" name="view_btn_font_size" id="owt7_lms_view_btn_font_size" class="form-control" value="<?php echo esc_attr( $view_btn_font_size ); ?>" placeholder="12px">
                </div>
                <div class="form-group owt7-lms-form-row">
                    <label for="owt7_lms_view_btn_color"><?php _e( 'View button color', 'library-management-system' ); ?></label>
                    <div class="owt7-lms-color-row">
                        <input type="color" name="view_btn_color" id="owt7_lms_view_btn_color" class="owt7-lms-color-picker" value="<?php echo esc_attr( $view_btn_color ); ?>">
                        <input type="text" class="form-control owt7-lms-color-hex" maxlength="7" placeholder="#1d2065" data-for="owt7_lms_view_btn_color" value="<?php echo esc_attr( $view_btn_color ); ?>">
                    </div>
                </div>
            </div>
            <div class="owt7-lms-public-view-two-col owt7-lms-public-view-two-col--single">
                <div class="form-group owt7-lms-form-row">
                    <label for="owt7_lms_checkout_btn_color"><?php _e( 'Checkout button color', 'library-management-system' ); ?></label>
                    <div class="owt7-lms-color-row">
                        <input type="color" name="checkout_btn_color" id="owt7_lms_checkout_btn_color" class="owt7-lms-color-picker" value="<?php echo esc_attr( $checkout_btn_color ); ?>">
                        <input type="text" class="form-control owt7-lms-color-hex" maxlength="7" placeholder="#0d9488" data-for="owt7_lms_checkout_btn_color" value="<?php echo esc_attr( $checkout_btn_color ); ?>">
                    </div>
                </div>
            </div>
            <div class="form-row buttons-group" style="margin-top: 1em;">
                <button type="submit" class="btn submit-save-btn"><?php _e( 'Save Public View Settings', 'library-management-system' ); ?></button>
            </div>
        </form>
    </div>
</div>
