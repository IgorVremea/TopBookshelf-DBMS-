<?php
/**
 * @link       https://onlinewebtutorblog.com
 * @since      3.5
 * @package    Library_Management_System
 * @subpackage Library_Management_System/admin/views
 * @copyright  Copyright (c) 2026, Online Web Tutor
 * @license    GPL-2.0+ https://www.gnu.org/licenses/gpl-2.0.html
 * @author     Online Web Tutor
 */
?>
<div class="owt7-lms owt7-lms-dashboard">

    <div class="jumbotron">
        <div class="owt7-lms-dashboard-hero-brand">
            <div class="owt7-lms-dashboard-hero-image">
                <img src="<?php echo esc_url( LIBMNS_PLUGIN_URL . 'admin/images/lms.png' ); ?>" alt="<?php esc_attr_e( 'Library Management System', 'library-management-system' ); ?>">
            </div>
            <div class="owt7-lms-dashboard-hero-copy">
                <span class="owt7-lms-dashboard-eyebrow"><?php esc_html_e( 'Free Dashboard', 'library-management-system' ); ?></span>
                <h1><?php _e("Library Management System", "library-management-system"); ?><sup>v<?php echo esc_html( LIBMNS_VERSION ); ?></sup></h1>
                <p><?php esc_html_e( 'Manage books, borrowers, circulation, import tools, and core plugin settings from one streamlined workspace built for the free edition.', 'library-management-system' ); ?></p>
                <div class="owt7-lms-dashboard-hero-tags">
                    <span><?php esc_html_e( 'Import / Export Excel', 'library-management-system' ); ?></span>
                    <span><?php esc_html_e( 'Checkout & Returns', 'library-management-system' ); ?></span>
                    <span><?php esc_html_e( 'Book Catalog', 'library-management-system' ); ?></span>
                    <span><?php esc_html_e( 'Borrowers & Branches', 'library-management-system' ); ?></span>
                    <span><?php esc_html_e( 'Reports & Transactions', 'library-management-system' ); ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="lms-dashboard lms-dashboard-cards card-container">
        <div class="settings-card-row">
            <?php if ( LIBMNS_Admin_FREE::libmns_current_user_can( 'owt7_lms_list_books' ) ) : ?>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=owt7_library_books' ) ); ?>" class="card">
                <span class="dashicons dashicons-book"></span>
                <h2><?php _e("Book Catalog", "library-management-system"); ?></h2>
                <span class="card-cta"><?php _e("Manage Books", "library-management-system"); ?></span>
            </a>
            <?php endif; ?>
            <?php if ( LIBMNS_Admin_FREE::libmns_current_user_can( 'owt7_lms_list_users' ) ) : ?>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=owt7_library_users' ) ); ?>" class="card">
                <span class="dashicons dashicons-admin-users"></span>
                <h2><?php _e("Borrowers", "library-management-system"); ?></h2>
                <span class="card-cta"><?php _e("Manage Borrowers", "library-management-system"); ?></span>
            </a>
            <?php endif; ?>
            <?php if ( LIBMNS_Admin_FREE::libmns_current_user_can( 'owt7_lms_list_bookcases' ) ) : ?>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=owt7_library_bookcases' ) ); ?>" class="card">
                <span class="dashicons dashicons-archive"></span>
                <h2><?php _e("Bookcases & Sections", "library-management-system"); ?></h2>
                <span class="card-cta"><?php _e("Manage Bookcases", "library-management-system"); ?></span>
            </a>
            <?php endif; ?>
        </div>
        <div class="settings-card-row">
            <?php if ( LIBMNS_Admin_FREE::libmns_current_user_can( 'owt7_lms_view_borrow_list' ) ) : ?>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=owt7_library_transactions' ) ); ?>" class="card">
                <span class="dashicons dashicons-chart-bar"></span>
                <h2><?php _e("Transactions", "library-management-system"); ?></h2>
                <span class="card-cta"><?php _e("Open Transactions", "library-management-system"); ?></span>
            </a>
            <?php endif; ?>
            <?php if ( LIBMNS_Admin_FREE::libmns_current_user_can( 'owt7_lms_view_settings' ) ) : ?>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=owt7_library_settings' ) ); ?>" class="card">
                <span class="dashicons dashicons-admin-tools"></span>
                <h2><?php _e("Settings", "library-management-system"); ?></h2>
                <span class="card-cta"><?php _e("Open Settings", "library-management-system"); ?></span>
            </a>
            <?php endif; ?>
            <?php if ( LIBMNS_Admin_FREE::libmns_current_user_can( 'owt7_lms_manage_upload' ) ) : ?>
            <a href="<?php echo esc_url( admin_url( 'admin.php?page=owt7_library_settings&mod=upload' ) ); ?>" class="card">
                <span class="dashicons dashicons-upload"></span>
                <h2><?php _e("Import CSV / Excel", "library-management-system"); ?></h2>
                <span class="card-cta"><?php _e("Open Import", "library-management-system"); ?></span>
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>
