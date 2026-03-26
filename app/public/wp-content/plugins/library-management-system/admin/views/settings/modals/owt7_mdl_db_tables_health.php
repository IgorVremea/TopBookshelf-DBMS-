<?php
/**
 * Modal: DB Tables Health Status
 *
 * @link       https://onlinewebtutorblog.com
 * @since      3.5
 * @package    Library_Management_System
 * @subpackage Library_Management_System/admin/views/settings/modals
 * @copyright  Copyright (c) 2026, Online Web Tutor
 * @license    GPL-2.0+ https://www.gnu.org/licenses/gpl-2.0.html
 * @author     Online Web Tutor
 */
?>
<div id="owt7_lms_mdl_db_tables_health" class="modal" style="display: none;">
    <div class="modal-content owt7-lms-db-health-modal">
        <span class="close owt7-lms-close-db-health-modal">&times;</span>
        <h2><?php _e( 'Plugin DB Tables Health', 'library-management-system' ); ?></h2>
        <p class="owt7-lms-db-health-desc"><?php _e( 'Status of all Library Management System database tables.', 'library-management-system' ); ?></p>
        <div id="owt7_lms_db_health_loading" class="owt7-lms-db-health-loading" style="display: none;">
            <div class="owt7-lms-css-loader"></div>
            <span class="owt7-lms-db-health-loading-text"><?php _e( 'Loading table status…', 'library-management-system' ); ?></span>
        </div>
        <div id="owt7_lms_db_health_content" class="owt7-lms-db-health-content" style="display: none;">
            <div class="owt7-lms-db-health-table-wrap">
                <table class="owt7-lms-table owt7-lms-db-health-table">
                    <thead>
                        <tr>
                            <th><?php _e( 'Table Name', 'library-management-system' ); ?></th>
                            <th><?php _e( 'Columns', 'library-management-system' ); ?></th>
                            <th><?php _e( 'Rows', 'library-management-system' ); ?></th>
                            <th><?php _e( 'Size', 'library-management-system' ); ?></th>
                            <th><?php _e( 'Created', 'library-management-system' ); ?></th>
                            <th><?php _e( 'Updated', 'library-management-system' ); ?></th>
                            <th><?php _e( 'Status', 'library-management-system' ); ?></th>
                        </tr>
                    </thead>
                    <tbody id="owt7_lms_db_health_tbody">
                    </tbody>
                </table>
            </div>
            <p class="owt7-lms-db-health-note description"><?php _e( 'Row counts are approximate for InnoDB tables. Size includes data and indexes.', 'library-management-system' ); ?></p>
        </div>
        <div id="owt7_lms_db_health_error" class="owt7-lms-db-health-error notice notice-error" style="display: none;"></div>
    </div>
</div>
