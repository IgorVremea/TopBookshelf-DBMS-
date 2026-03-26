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
<div id="owt7_lms_mdl_settings" class="modal owt7_csv_download_modal">
    <div class="modal-content owt7_csv_download_modal_content">
        <span class="close">&times;</span>
        <h2><?php _e('Download Sample CSVs', 'library-management-system'); ?></h2>
        <p class="owt7_csv_download_subtitle"><?php _e('Click on any option to download its sample file (CSV or Excel)', 'library-management-system'); ?></p>
        <div class="owt7_csv_download_tiles">
            <div class="owt7_csv_download_tile" data-file-type="categories">
                <div class="owt7_csv_tile_header">
                    <span class="dashicons dashicons-category"></span>
                    <span class="owt7_csv_tile_label"><?php _e("Categories", "library-management-system"); ?></span>
                </div>
                <div class="owt7_csv_download_formats">
                    <button type="button" class="owt7_download_format" data-format="csv"><?php _e("Categories (CSV)", "library-management-system"); ?></button>
                    <button type="button" class="owt7_download_format" data-format="xlsx"><?php _e("Categories (Excel)", "library-management-system"); ?></button>
                </div>
            </div>
            <div class="owt7_csv_download_tile" data-file-type="bookcases">
                <div class="owt7_csv_tile_header">
                    <span class="dashicons dashicons-building"></span>
                    <span class="owt7_csv_tile_label"><?php _e("Bookcases", "library-management-system"); ?></span>
                </div>
                <div class="owt7_csv_download_formats">
                    <button type="button" class="owt7_download_format" data-format="csv"><?php _e("Bookcases (CSV)", "library-management-system"); ?></button>
                    <button type="button" class="owt7_download_format" data-format="xlsx"><?php _e("Bookcases (Excel)", "library-management-system"); ?></button>
                </div>
            </div>
            <div class="owt7_csv_download_tile" data-file-type="sections">
                <div class="owt7_csv_tile_header">
                    <span class="dashicons dashicons-layout"></span>
                    <span class="owt7_csv_tile_label"><?php _e("Sections", "library-management-system"); ?></span>
                </div>
                <div class="owt7_csv_download_formats">
                    <button type="button" class="owt7_download_format" data-format="csv"><?php _e("Sections (CSV)", "library-management-system"); ?></button>
                    <button type="button" class="owt7_download_format" data-format="xlsx"><?php _e("Sections (Excel)", "library-management-system"); ?></button>
                </div>
            </div>
            <div class="owt7_csv_download_tile" data-file-type="books">
                <div class="owt7_csv_tile_header">
                    <span class="dashicons dashicons-book"></span>
                    <span class="owt7_csv_tile_label"><?php _e("Books", "library-management-system"); ?></span>
                </div>
                <div class="owt7_csv_download_formats">
                    <button type="button" class="owt7_download_format" data-format="csv"><?php _e("Books (CSV)", "library-management-system"); ?></button>
                    <button type="button" class="owt7_download_format" data-format="xlsx"><?php _e("Books (Excel)", "library-management-system"); ?></button>
                </div>
            </div>
            <div class="owt7_csv_download_tile" data-file-type="branches">
                <div class="owt7_csv_tile_header">
                    <span class="dashicons dashicons-store"></span>
                    <span class="owt7_csv_tile_label"><?php _e("Branches", "library-management-system"); ?></span>
                </div>
                <div class="owt7_csv_download_formats">
                    <button type="button" class="owt7_download_format" data-format="csv"><?php _e("Branches (CSV)", "library-management-system"); ?></button>
                    <button type="button" class="owt7_download_format" data-format="xlsx"><?php _e("Branches (Excel)", "library-management-system"); ?></button>
                </div>
            </div>
            <div class="owt7_csv_download_tile" data-file-type="users">
                <div class="owt7_csv_tile_header">
                    <span class="dashicons dashicons-admin-users"></span>
                    <span class="owt7_csv_tile_label"><?php _e("Users", "library-management-system"); ?></span>
                </div>
                <div class="owt7_csv_download_formats">
                    <button type="button" class="owt7_download_format" data-format="csv"><?php _e("Users (CSV)", "library-management-system"); ?></button>
                    <button type="button" class="owt7_download_format" data-format="xlsx"><?php _e("Users (Excel)", "library-management-system"); ?></button>
                </div>
            </div>
        </div>
    </div>
</div>
