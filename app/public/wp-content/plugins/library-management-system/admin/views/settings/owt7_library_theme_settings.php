<?php
/**
 * Theme Color Settings - Admin panel theme (buttons, table headers, accents, action buttons).
 *
 * @link       https://onlinewebtutorblog.com
 * @since      3.5
 * @package    Library_Management_System
 * @subpackage Library_Management_System/admin/views/settings
 * @copyright  Copyright (c) 2026, Online Web Tutor
 * @license    GPL-2.0+ https://www.gnu.org/licenses/gpl-2.0.html
 * @author     Online Web Tutor
 */

$primary = get_option( 'owt7_lms_theme_primary', LIBMNS_THEME_PRIMARY_DEFAULT );
$accent  = get_option( 'owt7_lms_theme_accent', LIBMNS_THEME_ACCENT_DEFAULT );
if ( ! preg_match( '/^#[0-9a-fA-F]{6}$/', $primary ) ) {
	$primary = LIBMNS_THEME_PRIMARY_DEFAULT;
}
if ( ! preg_match( '/^#[0-9a-fA-F]{6}$/', $accent ) ) {
	$accent = LIBMNS_THEME_ACCENT_DEFAULT;
}

$action_clone  = get_option( 'owt7_lms_theme_action_clone', LIBMNS_THEME_ACTION_CLONE_DEFAULT );
$action_view   = get_option( 'owt7_lms_theme_action_view', LIBMNS_THEME_ACTION_VIEW_DEFAULT );
$action_edit   = get_option( 'owt7_lms_theme_action_edit', LIBMNS_THEME_ACTION_EDIT_DEFAULT );
$action_copies = get_option( 'owt7_lms_theme_action_book_copies', LIBMNS_THEME_ACTION_BOOK_COPIES_DEFAULT );
$action_delete = get_option( 'owt7_lms_theme_action_delete', LIBMNS_THEME_ACTION_DELETE_DEFAULT );
if ( ! preg_match( '/^#[0-9a-fA-F]{6}$/', $action_clone ) ) { $action_clone = LIBMNS_THEME_ACTION_CLONE_DEFAULT; }
if ( ! preg_match( '/^#[0-9a-fA-F]{6}$/', $action_view ) ) { $action_view = LIBMNS_THEME_ACTION_VIEW_DEFAULT; }
if ( ! preg_match( '/^#[0-9a-fA-F]{6}$/', $action_edit ) ) { $action_edit = LIBMNS_THEME_ACTION_EDIT_DEFAULT; }
if ( ! preg_match( '/^#[0-9a-fA-F]{6}$/', $action_copies ) ) { $action_copies = LIBMNS_THEME_ACTION_BOOK_COPIES_DEFAULT; }
if ( ! preg_match( '/^#[0-9a-fA-F]{6}$/', $action_delete ) ) { $action_delete = LIBMNS_THEME_ACTION_DELETE_DEFAULT; }
?>
<div class="owt7-lms owt7-lms-settings">

    <div class="owt7_lms_settings owt7-lms-theme-settings">

        <div class="page-header">
            <div class="breadcrumb"><?php _e( 'Library System', 'library-management-system' ); ?> &gt;&gt; <span class="active"><?php _e( 'Theme Color', 'library-management-system' ); ?></span></div>
            <div class="page-actions">
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=owt7_library_settings#appearance' ) ); ?>" class="btn">
                    <span class="dashicons dashicons-arrow-left-alt"></span>
                    <?php _e( 'Back', 'library-management-system' ); ?>
                </a>
            </div>
        </div>

        <div class="page-container">

            <div class="page-title">
                <h2><?php _e( 'Theme Color', 'library-management-system' ); ?></h2>
            </div>

            <p class="owt7-lms-theme-desc"><?php _e( 'Choose colors for buttons, table headers, links, and accents across the Library Management admin pages.', 'library-management-system' ); ?></p>

            <form id="owt7_lms_data_settings" class="owt7-lms-theme-form" method="post" action="javascript:void(0);">
                <?php wp_nonce_field( 'owt7_library_actions', 'owt7_lms_nonce' ); ?>
                <input type="hidden" name="owt7_lms_settings_type" value="theme">

                <div class="owt7-lms-theme-fields">
                    <div class="form-group owt7-lms-theme-field">
                        <label for="owt7_lms_theme_primary"><?php _e( 'Primary color', 'library-management-system' ); ?></label>
                        <div class="owt7-lms-color-row">
                            <input type="color" id="owt7_lms_theme_primary" name="owt7_lms_theme_primary" value="<?php echo esc_attr( $primary ); ?>" class="owt7-lms-color-picker">
                            <input type="text" value="<?php echo esc_attr( $primary ); ?>" class="form-control owt7-lms-color-hex" maxlength="7" placeholder="<?php echo esc_attr( LIBMNS_THEME_PRIMARY_DEFAULT ); ?>" data-for="owt7_lms_theme_primary">
                        </div>
                        <span class="description"><?php _e( 'Used for buttons, table headers, borders, and main accents.', 'library-management-system' ); ?></span>
                    </div>

                    <div class="form-group owt7-lms-theme-field">
                        <label for="owt7_lms_theme_accent"><?php _e( 'Accent color', 'library-management-system' ); ?></label>
                        <div class="owt7-lms-color-row">
                            <input type="color" id="owt7_lms_theme_accent" name="owt7_lms_theme_accent" value="<?php echo esc_attr( $accent ); ?>" class="owt7-lms-color-picker">
                            <input type="text" value="<?php echo esc_attr( $accent ); ?>" class="form-control owt7-lms-color-hex" maxlength="7" placeholder="<?php echo esc_attr( LIBMNS_THEME_ACCENT_DEFAULT ); ?>" data-for="owt7_lms_theme_accent">
                        </div>
                        <span class="description"><?php _e( 'Used for highlights, badges, and secondary accents.', 'library-management-system' ); ?></span>
                    </div>
                </div>

                <div class="owt7-lms-theme-section owt7-lms-action-buttons-section">
                    <h3 class="owt7-lms-theme-section-title"><?php _e( 'Action buttons', 'library-management-system' ); ?></h3>
                    <p class="description"><?php _e( 'Colors for list action buttons (View, Edit, Delete). Each operation can have its own color.', 'library-management-system' ); ?></p>
                    <div class="owt7-lms-theme-fields owt7-lms-action-buttons-fields">
                        <div class="form-group owt7-lms-theme-field">
                            <label for="owt7_lms_theme_action_view"><?php _e( 'View', 'library-management-system' ); ?></label>
                            <div class="owt7-lms-color-row">
                                <input type="color" id="owt7_lms_theme_action_view" name="owt7_lms_theme_action_view" value="<?php echo esc_attr( $action_view ); ?>" class="owt7-lms-color-picker">
                                <input type="text" value="<?php echo esc_attr( $action_view ); ?>" class="form-control owt7-lms-color-hex" maxlength="7" placeholder="<?php echo esc_attr( LIBMNS_THEME_ACTION_VIEW_DEFAULT ); ?>" data-for="owt7_lms_theme_action_view">
                            </div>
                        </div>
                        <div class="form-group owt7-lms-theme-field">
                            <label for="owt7_lms_theme_action_edit"><?php _e( 'Edit', 'library-management-system' ); ?></label>
                            <div class="owt7-lms-color-row">
                                <input type="color" id="owt7_lms_theme_action_edit" name="owt7_lms_theme_action_edit" value="<?php echo esc_attr( $action_edit ); ?>" class="owt7-lms-color-picker">
                                <input type="text" value="<?php echo esc_attr( $action_edit ); ?>" class="form-control owt7-lms-color-hex" maxlength="7" placeholder="<?php echo esc_attr( LIBMNS_THEME_ACTION_EDIT_DEFAULT ); ?>" data-for="owt7_lms_theme_action_edit">
                            </div>
                        </div>
                        <div class="form-group owt7-lms-theme-field">
                            <label for="owt7_lms_theme_action_delete"><?php _e( 'Delete', 'library-management-system' ); ?></label>
                            <div class="owt7-lms-color-row">
                                <input type="color" id="owt7_lms_theme_action_delete" name="owt7_lms_theme_action_delete" value="<?php echo esc_attr( $action_delete ); ?>" class="owt7-lms-color-picker">
                                <input type="text" value="<?php echo esc_attr( $action_delete ); ?>" class="form-control owt7-lms-color-hex" maxlength="7" placeholder="<?php echo esc_attr( LIBMNS_THEME_ACTION_DELETE_DEFAULT ); ?>" data-for="owt7_lms_theme_action_delete">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="owt7-lms-theme-actions">
                    <button type="submit" class="btn"><?php _e( 'Save theme', 'library-management-system' ); ?></button>
                    <a href="admin.php?page=owt7_library_settings&mod=theme" class="btn btn-outline owt7-lms-theme-reset"><?php _e( 'Reset to default', 'library-management-system' ); ?></a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
jQuery(function($) {
    function syncColor(fromInput, toInput) {
        var val = $(fromInput).val();
        if ($(fromInput).is('input[type=color]')) {
            $(toInput).val(val);
        } else {
            if (/^#[0-9a-fA-F]{6}$/.test(val)) {
                $(toInput).val(val);
            }
        }
    }
    $('.owt7-lms-color-picker').on('input change', function() {
        var id = $(this).attr('id');
        $('.owt7-lms-color-hex[data-for="' + id + '"]').val($(this).val());
    });
    $('.owt7-lms-color-hex').on('input change', function() {
        var val = $(this).val();
        if (/^#[0-9a-fA-F]{6}$/.test(val)) {
            $('#' + $(this).data('for')).val(val);
        }
    });
    $('.owt7-lms-theme-reset').on('click', function(e) {
        e.preventDefault();
        $('#owt7_lms_theme_primary').val('<?php echo esc_js( LIBMNS_THEME_PRIMARY_DEFAULT ); ?>');
        $('.owt7-lms-color-hex[data-for="owt7_lms_theme_primary"]').val('<?php echo esc_js( LIBMNS_THEME_PRIMARY_DEFAULT ); ?>');
        $('#owt7_lms_theme_accent').val('<?php echo esc_js( LIBMNS_THEME_ACCENT_DEFAULT ); ?>');
        $('.owt7-lms-color-hex[data-for="owt7_lms_theme_accent"]').val('<?php echo esc_js( LIBMNS_THEME_ACCENT_DEFAULT ); ?>');
        $('#owt7_lms_theme_action_clone').val('<?php echo esc_js( LIBMNS_THEME_ACTION_CLONE_DEFAULT ); ?>');
        $('.owt7-lms-color-hex[data-for="owt7_lms_theme_action_clone"]').val('<?php echo esc_js( LIBMNS_THEME_ACTION_CLONE_DEFAULT ); ?>');
        $('#owt7_lms_theme_action_view').val('<?php echo esc_js( LIBMNS_THEME_ACTION_VIEW_DEFAULT ); ?>');
        $('.owt7-lms-color-hex[data-for="owt7_lms_theme_action_view"]').val('<?php echo esc_js( LIBMNS_THEME_ACTION_VIEW_DEFAULT ); ?>');
        $('#owt7_lms_theme_action_edit').val('<?php echo esc_js( LIBMNS_THEME_ACTION_EDIT_DEFAULT ); ?>');
        $('.owt7-lms-color-hex[data-for="owt7_lms_theme_action_edit"]').val('<?php echo esc_js( LIBMNS_THEME_ACTION_EDIT_DEFAULT ); ?>');
        $('#owt7_lms_theme_action_book_copies').val('<?php echo esc_js( LIBMNS_THEME_ACTION_BOOK_COPIES_DEFAULT ); ?>');
        $('.owt7-lms-color-hex[data-for="owt7_lms_theme_action_book_copies"]').val('<?php echo esc_js( LIBMNS_THEME_ACTION_BOOK_COPIES_DEFAULT ); ?>');
        $('#owt7_lms_theme_action_delete').val('<?php echo esc_js( LIBMNS_THEME_ACTION_DELETE_DEFAULT ); ?>');
        $('.owt7-lms-color-hex[data-for="owt7_lms_theme_action_delete"]').val('<?php echo esc_js( LIBMNS_THEME_ACTION_DELETE_DEFAULT ); ?>');
        $('#owt7_lms_data_settings').submit();
    });
});
</script>
