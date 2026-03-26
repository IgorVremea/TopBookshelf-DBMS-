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

    <div class="lms-list-branch">

        <div class="page-header">
            <div class="breadcrumb"> <?php _e("Library System", "library-management-system"); ?> >> <span class="active"><?php _e("List Branch", "library-management-system"); ?></span> </div>
            <div class="page-actions">
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=owt7_library_users' ) ); ?>" class="btn"><span class="dashicons dashicons-arrow-left-alt"></span> <?php _e("Back", "library-management-system"); ?></a>
            </div>
        </div>

        <div class="page-container">

            <div class="page-title-row">
                <div class="page-title">
                    <h2><?php _e("Branch List", "library-management-system"); ?></h2>
                </div>
                <div class="filter-container">
                </div>
            </div>

            <table class="owt7-lms-table" id="tbl_branches_list">
                <thead>
                    <tr>
                        <th><?php _e("S No", "library-management-system"); ?></th>
                        <th><?php _e("Name", "library-management-system"); ?></th>
                        <th><?php _e("Total User(s)", "library-management-system"); ?></th>
                        <th><?php _e("Status", "library-management-system"); ?></th>
                        <th><?php _e("Created at [Y-M-D]", "library-management-system"); ?></th>
                        <th><?php _e("Action", "library-management-system"); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        if(!empty($params['branches']) && is_array($params['branches'])){
                            foreach($params['branches'] as $branch){
                                ?>
                    <tr>
                        <td><?php echo $branch->id; ?></td>
                        <td><?php echo ucwords($branch->name); ?></td>
                        <td>
                            <?php
                            if ( ! empty( $branch->total_users ) && (int) $branch->total_users > 0 ) {
                                $users_list_url = add_query_arg( 'branch_id', (int) $branch->id, admin_url( 'admin.php?page=owt7_library_users' ) );
                                echo '<a href="' . esc_url( $users_list_url ) . '" class="owt7-lms-total-sections-badge">' . (int) $branch->total_users . '</a>';
                            } else {
                                echo '<span class="owt7-lms-total-sections-badge">0</span>';
                            }
                            ?>
                        </td>
                        <td>
                            <?php if($branch->status){ ?>
                            <a href="javascript:void(0);" class="action-btn view-btn">
                                <?php _e("Active", "library-management-system"); ?>
                            </a>
                            <?php }else{ ?>
                            <a href="javascript:void(0);" class="action-btn delete-btn">
                                <?php _e("Inactive", "library-management-system"); ?>
                            </a>
                            <?php } ?>
                        </td>
                        <td><?php echo $branch->created_at ? date("Y-m-d", strtotime($branch->created_at)) : ''; ?></td>
                        <td>
                            <?php  
                                if(strtolower($branch->name) == "no branch"){
                                    echo "<i>No action<i>";
                                }else{
                                    if ( LIBMNS_Admin_FREE::libmns_current_user_can( 'owt7_lms_list_branches' ) ) : ?>
                                    <a href="admin.php?page=owt7_library_users&mod=branch&fn=add&opt=view&id=<?php echo base64_encode($branch->id); ?>"
                                        title="<?php _e("View", "library-management-system"); ?>" class="action-btn view-btn">
                                        <span class="dashicons dashicons-visibility"></span>
                                    </a>
                                    <?php endif;
                                    if ( LIBMNS_Admin_FREE::libmns_current_user_can( 'owt7_lms_edit_branch' ) ) : ?>
                                    <a href="admin.php?page=owt7_library_users&mod=branch&fn=add&opt=edit&id=<?php echo base64_encode($branch->id); ?>"
                                        title="<?php _e("Edit", "library-management-system"); ?>" class="action-btn edit-btn">
                                        <span class="dashicons dashicons-edit"></span>
                                    </a>
                                    <?php endif;
                                    if ( LIBMNS_Admin_FREE::libmns_current_user_can( 'owt7_lms_delete_branch' ) ) : ?>
                                    <a href="javascript:void(0);" title="<?php _e("Delete", "library-management-system"); ?>" class="action-btn delete-btn action-btn-delete"
                                        data-id="<?php echo base64_encode($branch->id) ?>"
                                        data-module="<?php echo base64_encode('branch'); ?>">
                                        <span class="dashicons dashicons-trash"></span>
                                    </a>
                                    <?php endif;
                                }
                            ?>
                        </td>
                    </tr>
                    <?php
                            }
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
