<?php
/**
 * @link       https://onlinewebtutorblog.com
 * @since      3.5
 * @package    Library_Management_System
 * @subpackage Library_Management_System/admin/views/users
 * @copyright  Copyright (c) 2026, Online Web Tutor
 * @license    GPL-2.0+ https://www.gnu.org/licenses/gpl-2.0.html
 * @author     Online Web Tutor
 */
?>
<div class="owt7-lms owt7-lms-users">

    <div class="lms-list-user">

        <div class="page-header">
            <div class="breadcrumb"> <?php _e("Library Management", "library-management-system"); ?> &raquo; <span
                    class="active"><?php _e("Borrowers", "library-management-system"); ?></span> </div>
            <div class="page-actions">
                <?php if ( LIBMNS_Admin_FREE::libmns_current_user_can( 'owt7_lms_add_branch' ) ) : ?>
                <a href="admin.php?page=owt7_library_users&mod=branch&fn=add"
                    class="btn"><span class="dashicons dashicons-location"></span> <?php _e("Add Branch", "library-management-system"); ?></a>
                <?php endif; ?>
                <a href="admin.php?page=owt7_library_users&mod=branch&fn=list"
                    class="btn"><span class="dashicons dashicons-list-view"></span> <?php _e("Branches", "library-management-system"); ?></a>
                <?php if ( LIBMNS_Admin_FREE::libmns_current_user_can( 'owt7_lms_add_user' ) ) : ?>
                <a href="admin.php?page=owt7_library_users&mod=user&fn=add"
                    class="btn"><span class="dashicons dashicons-admin-users"></span> <?php _e("Add Borrower", "library-management-system"); ?></a>
                <?php endif; ?>
            </div>
        </div>

        <div class="page-container">

            <div class="page-title-row">
                <div class="page-title">
                    <h2><?php _e("Borrowers", "library-management-system"); ?></h2>
                    <p class="owt7-lms-users-filter-msg" id="owt7_lms_users_filter_msg"<?php echo empty( $params['filter_branch_name'] ) ? ' style="display: none;"' : ''; ?>><?php echo ! empty( $params['filter_branch_name'] ) ? esc_html( sprintf( __( 'Showing "%s" borrowers', 'library-management-system' ), $params['filter_branch_name'] ) ) : ''; ?></p>
                </div>
                <div class="filter-container">

                <div id="owt7_library_data_filter_options">

                    <label for="owt7_lms_branch_filter"><?php _e("Filter by:", "library-management-system"); ?></label>
                    <select data-module="users" data-filter-by="branch" id="owt7_lms_data_filter"
                        class="owt7_lms_data_filter">
                        <option value=""><?php _e("-- Select Branch --", "library-management-system"); ?></option>
                        <option value="all"><?php _e("All", "library-management-system"); ?></option>
                        <?php 
                        if(!empty($params['branches']) && is_array($params['branches'])){
                            foreach($params['branches'] as $branch){
                                ?>
                            <option value="<?php echo $branch->id; ?>"><?php echo ucfirst($branch->name); ?></option>
                            <?php
                            }
                        }
                        ?>
                    </select>
                </div>

                </div>
            </div>

            <table class="owt7-lms-table" id="tbl_users_list">
                <thead>
                    <tr>
                        <th><?php _e("User ID", "library-management-system"); ?></th>
                        <th><?php _e("User Details", "library-management-system"); ?></th>
                        <th><?php _e("Status", "library-management-system"); ?></th>
                        <th><?php _e("Created at [Y-M-D]", "library-management-system"); ?></th>
                        <th><?php _e("Action", "library-management-system"); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        ob_start();
                        include_once LIBMNS_PLUGIN_DIR_PATH . 'admin/views/users/templates/owt7_library_users_list.php';
                        $template = ob_get_contents();
                        ob_end_clean();
                        echo $template;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>