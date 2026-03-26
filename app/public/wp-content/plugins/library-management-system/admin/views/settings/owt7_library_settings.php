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
<div class="owt7-lms owt7-lms-settings">

    <div class="page-header">
        <div class="breadcrumb">
            <?php _e('Library Management', 'library-management-system'); ?> &raquo;
            <span class="active">
                <?php _e('Settings', 'library-management-system'); ?>
            </span>
        </div>
        <div class="page-actions">
            <a href="<?php echo esc_url( home_url( 'wp-library-books' ) ); ?>" target="_blank" rel="noopener noreferrer" class="button button-primary owt7-lms-nav-to-library-page">
                <span class="dashicons dashicons-external" aria-hidden="true"></span>
                <?php esc_html_e( 'Navigate to Library Page', 'library-management-system' ); ?>
            </a>
            <?php
            if (LIBMNS_Admin_FREE::libmns_current_user_can('owt7_lms_run_test_data_importer')) {
                $alreadyAvailable = get_option('owt7_lms_test_data');
                if (!empty($alreadyAvailable)) {
            ?>
                    <a href="javascript:void(0)" id="owt7_lms_refresh_test_data" class="btn btn-fixed">
                        <span class="dashicons dashicons-update"></span>
                        <?php _e("Reinstall Sample Data", "library-management-system"); ?>
                    </a>
                    <a href="javascript:void(0)" id="owt7_lms_remove_test_data" class="btn btn-danger btn-fixed">
                        <span class="dashicons dashicons-trash"></span>
                        <?php _e("Remove Sample Data", "library-management-system"); ?>
                    </a>
                <?php
                } else {
                ?>
                    <a href="javascript:void(0)" id="owt7_lms_run_data_importer" class="btn btn-fixed">
                        <span class="dashicons dashicons-backup"></span>
                        <?php _e("Install Sample Data", "library-management-system"); ?>
                    </a>
            <?php
                }
            }
            ?>
        </div>
    </div>

    <div class="page-container lms-settings">

        <nav class="owt7-lms-settings-tabs" role="tablist" aria-label="<?php esc_attr_e('Settings categories', 'library-management-system'); ?>">
            <button type="button" class="owt7-lms-settings-tab active" data-category="general" role="tab" aria-selected="true">
                <span class="dashicons dashicons-admin-generic"></span>
                <?php _e('General', 'library-management-system'); ?>
            </button>
            <button type="button" class="owt7-lms-settings-tab" data-category="appearance" role="tab" aria-selected="false">
                <span class="dashicons dashicons-art"></span>
                <?php _e('Appearance & Display', 'library-management-system'); ?>
            </button>
            <button type="button" class="owt7-lms-settings-tab" data-category="data" role="tab" aria-selected="false">
                <span class="dashicons dashicons-database"></span>
                <?php _e('Data & Tools', 'library-management-system'); ?>
            </button>
            <?php if (current_user_can('manage_options')) : ?>
                <button type="button" class="owt7-lms-settings-tab" data-category="permissions" role="tab" aria-selected="false">
                    <span class="dashicons dashicons-groups"></span>
                    <?php _e('Permissions', 'library-management-system'); ?>
                </button>
            <?php endif; ?>
        </nav>

        <div class="lms-dashboard card-container owt7-lms-settings-panels">

            <div class="owt7-lms-settings-panel active" id="panel-general" data-category="general" role="tabpanel">
                <div class="settings-card-row">
                    <a href="javascript:void(0)" id="owt7_lms_country_modal" class="card card--modal-trigger">
                        <span class="dashicons dashicons-admin-site"></span>
                        <h2><?php _e('Country & Currency', 'library-management-system'); ?></h2>
                        <p class="card-desc"><?php _e('Set your library\'s country and default currency for fines, pricing, and display.', 'library-management-system'); ?></p>
                        <span class="card-cta owt7-lms-anc-settings"><?php _e('Open', 'library-management-system'); ?></span>
                    </a>
                    <a href="javascript:void(0)" id="owt7_lms_days_modal" class="card card--modal-trigger">
                        <span class="dashicons dashicons-plus-alt"></span>
                        <h2><?php _e('Loan Periods (Borrow Days)', 'library-management-system'); ?></h2>
                        <p class="card-desc"><?php _e('Define how many days users can borrow items (e.g. 7, 14, or 21 days per loan).', 'library-management-system'); ?></p>
                        <span class="card-cta owt7-lms-anc-settings"><?php _e('Open', 'library-management-system'); ?></span>
                    </a>
                    <a href="javascript:void(0)" id="owt7_lms_late_fine_modal" class="card card--modal-trigger">
                        <span class="dashicons dashicons-tag"></span>
                        <h2><?php _e('Late Return Fines', 'library-management-system'); ?></h2>
                        <p class="card-desc"><?php _e('Configure fines or penalties when items are returned after the due date.', 'library-management-system'); ?></p>
                        <span class="card-cta owt7-lms-anc-settings"><?php _e('Open', 'library-management-system'); ?></span>
                    </a>
                    <a href="javascript:void(0)" id="owt7_lms_lms_frontend_modal_btn" class="card card--modal-trigger">
                        <span class="dashicons dashicons-admin-settings"></span>
                        <h2><?php _e('Basic Settings', 'library-management-system'); ?></h2>
                        <p class="card-desc"><?php _e('Public page books per page and default images for books and members.', 'library-management-system'); ?></p>
                        <span class="card-cta owt7-lms-anc-settings"><?php _e('Open', 'library-management-system'); ?></span>
                    </a>
                </div>
            </div>

            <div class="owt7-lms-settings-panel" id="panel-appearance" data-category="appearance" role="tabpanel" hidden>
                <div class="settings-card-row">
                    <a href="<?php echo esc_url(admin_url('admin.php?page=owt7_library_settings&mod=theme')); ?>" class="card">
                        <span class="dashicons dashicons-art"></span>
                        <h2><?php _e('Admin Theme & Colors', 'library-management-system'); ?></h2>
                        <p class="card-desc"><?php _e('Customize buttons, table headers, and accents in the admin panel.', 'library-management-system'); ?></p>
                        <span class="card-cta owt7-lms-anc-settings"><?php _e('Open', 'library-management-system'); ?></span>
                    </a>
                    <a href="javascript:void(0)" id="owt7_lms_public_view_btn" class="card card--modal-trigger">
                        <span class="dashicons dashicons-visibility"></span>
                        <h2><?php _e('Library Page Layout', 'library-management-system'); ?></h2>
                        <p class="card-desc"><?php _e('Customize layout and sections shown on the main library page (grid, list, columns).', 'library-management-system'); ?></p>
                        <span class="card-cta owt7-lms-anc-settings"><?php _e('Open', 'library-management-system'); ?></span>
                    </a>
                </div>
            </div>

            <div class="owt7-lms-settings-panel" id="panel-data" data-category="data" role="tabpanel" hidden>
                <div class="settings-card-row">
                    <a href="<?php echo esc_url(admin_url('admin.php?page=owt7_library_settings&mod=upload')); ?>" class="card">
                        <span class="dashicons dashicons-upload"></span>
                        <h2><?php _e('Import Data (CSV / Excel)', 'library-management-system'); ?></h2>
                        <p class="card-desc"><?php _e('Import books, users, sections, and other data from CSV or Excel files.', 'library-management-system'); ?></p>
                        <span class="card-cta owt7-lms-anc-settings"><?php _e('Open', 'library-management-system'); ?></span>
                    </a>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=owt7_library_settings&mod=shortcodes' ) ); ?>" class="card">
                        <span class="dashicons dashicons-shortcode"></span>
                        <h2><?php _e( 'Shortcodes', 'library-management-system' ); ?></h2>
                        <p class="card-desc"><?php _e( 'Copy shortcodes to embed the library on any page or post.', 'library-management-system' ); ?></p>
                        <span class="card-cta owt7-lms-anc-settings"><?php _e( 'Open', 'library-management-system' ); ?></span>
                    </a>
                </div>
            </div>

            <?php if (current_user_can('manage_options')) : ?>
                <div class="owt7-lms-settings-panel" id="panel-permissions" data-category="permissions" role="tabpanel" hidden>
                    <div class="settings-card-row">
                        <a href="javascript:void(0)" id="owt7_lms_library_user_portal_settings_btn" class="card card--modal-trigger">
                            <span class="dashicons dashicons-book-alt"></span>
                            <h2><?php _e('Library User (LMS) Portal Settings', 'library-management-system'); ?></h2>
                            <p class="card-desc"><?php _e('Configure Library User portal pages and the shared catalogue filters used on both Books List and the public library page.', 'library-management-system'); ?></p>
                            <span class="card-cta owt7-lms-anc-settings"><?php _e('Open', 'library-management-system'); ?></span>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    (function() {
        var defaultCategory = 'general';
        var tabs = document.querySelectorAll('.owt7-lms-settings-tab');
        var panels = document.querySelectorAll('.owt7-lms-settings-panel');

        function getCategoryFromHash() {
            var hash = (window.location.hash || '').replace(/^#/, '');
            if (!hash) return defaultCategory;
            if (hash === 'library') return 'appearance';
            var valid = ['general', 'appearance', 'data', 'permissions'];
            return valid.indexOf(hash) !== -1 ? hash : defaultCategory;
        }

        function switchTo(category) {
            var hasPanel = false;
            panels.forEach(function(panel) {
                if (panel.getAttribute('data-category') === category) {
                    hasPanel = true;
                }
            });
            if (!hasPanel) {
                category = defaultCategory;
            }
            tabs.forEach(function(tab) {
                var isActive = tab.getAttribute('data-category') === category;
                tab.classList.toggle('active', isActive);
                tab.setAttribute('aria-selected', isActive ? 'true' : 'false');
            });
            panels.forEach(function(panel) {
                var isActive = panel.getAttribute('data-category') === category;
                panel.classList.toggle('active', isActive);
                panel.hidden = !isActive;
            });
            window.location.hash = category;
        }
        tabs.forEach(function(tab) {
            tab.addEventListener('click', function() {
                switchTo(tab.getAttribute('data-category'));
            });
        });
        if (window.location.hash) {
            switchTo(getCategoryFromHash());
        }
        window.addEventListener('hashchange', function() {
            switchTo(getCategoryFromHash());
        });
    })();
</script>

<div class="owt7_lms_modal_section owt7_lms_public_view_modal_wrap">
    <?php
    $public_view_settings = isset($params['public_view_settings']) ? $params['public_view_settings'] : array();
    include_once LIBMNS_PLUGIN_DIR_PATH . 'admin/views/settings/modals/owt7_mdl_public_view_settings.php';
    ?>
</div>
<div class="owt7_lms_modal_section owt7_lms_country_modal_wrap">
    <?php
    include_once LIBMNS_PLUGIN_DIR_PATH . 'admin/views/settings/modals/owt7_mdl_country_settings.php';
    ?>
</div>
<div class="owt7_lms_modal_section owt7_lms_days_modal_wrap">
    <?php
    include_once LIBMNS_PLUGIN_DIR_PATH . 'admin/views/settings/modals/owt7_mdl_days_settings.php';
    ?>
</div>
<div class="owt7_lms_modal_section owt7_lms_late_fine_modal_wrap">
    <?php
    include_once LIBMNS_PLUGIN_DIR_PATH . 'admin/views/settings/modals/owt7_mdl_late_fine_settings.php';
    ?>
</div>
<div class="owt7_lms_modal_section owt7_lms_lms_frontend_modal_wrap">
    <?php include_once LIBMNS_PLUGIN_DIR_PATH . 'admin/views/settings/modals/owt7_mdl_lms_frontend_settings.php'; ?>
</div>
<?php if (current_user_can('manage_options')) : ?>
    <div class="owt7_lms_modal_section owt7_lms_library_user_portal_modal_wrap">
        <?php include_once LIBMNS_PLUGIN_DIR_PATH . 'admin/views/settings/modals/owt7_mdl_library_user_portal_settings.php'; ?>
    </div>
<?php endif; ?>
<?php if ( ! empty( $params['open_lms_frontend_modal'] ) ) : ?>
<script>jQuery(function() { jQuery('#owt7_lms_lms_frontend_modal_btn').trigger('click'); });</script>
<?php endif; ?>