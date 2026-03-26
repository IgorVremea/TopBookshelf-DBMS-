<?php
/**
 * @link       https://onlinewebtutorblog.com
 * @since      3.5
 * @package    Library_Management_System
 * @subpackage Library_Management_System/admin/views/users/templates
 * @copyright  Copyright (c) 2026, Online Web Tutor
 * @license    GPL-2.0+ https://www.gnu.org/licenses/gpl-2.0.html
 * @author     Online Web Tutor
 */
if(!empty($params['users']) && is_array($params['users'])){
    foreach($params['users'] as $user){
        ?>
        <tr class="lms-list-row">
            <td class="lms-cell-id">
                <span class="lms-id-badge">#<?php echo esc_html( $user->u_id ); ?></span>
            </td>
            <td class="lms-cell-user">
                <div class="lms-info-block">
                    <div class="lms-info-item"><span class="lms-info-label"><?php _e('Name', 'library-management-system'); ?></span><span class="lms-info-value"><?php echo esc_html( ucwords( $user->name ) ); ?></span></div>
                    <div class="lms-info-item"><span class="lms-info-label"><?php _e('Email', 'library-management-system'); ?></span><span class="lms-info-value"><?php echo esc_html( !empty($user->email) ? $user->email : __("N/A", "library-management-system") ); ?></span></div>
                    <div class="lms-info-item"><span class="lms-info-label"><?php _e('Branch', 'library-management-system'); ?></span><span class="lms-info-value"><?php echo esc_html( !empty($user->branch_name) ? $user->branch_name : __("N/A", "library-management-system") ); ?></span></div>
                </div>
            </td>
            <td class="lms-cell-status">
                <?php if($user->status){ ?>
                    <span class="lms-status-badge lms-status-success"><?php _e("Active", "library-management-system"); ?></span>
                <?php }else{ ?>
                    <span class="lms-status-badge lms-status-inactive"><?php _e("Inactive", "library-management-system"); ?></span>
                <?php } ?>
            </td>
            <td class="lms-cell-date">
                <?php if ( $user->created_at ) { ?>
                    <span class="lms-info-value lms-date"><?php echo date("Y-m-d", strtotime($user->created_at)); ?></span>
                <?php } else { ?>
                    <span class="lms-fine-na">—</span>
                <?php } ?>
            </td>
            <td class="lms-cell-actions">
                <div class="lms-action-wrap">
                <?php if ( LIBMNS_Admin_FREE::libmns_current_user_can( 'owt7_lms_list_users' ) ) : ?>
                <a href="admin.php?page=owt7_library_users&mod=user&fn=add&opt=view&id=<?php echo base64_encode($user->id); ?>" title="<?php _e("View", "library-management-system"); ?>" class="action-btn view-btn">
                    <span class="dashicons dashicons-visibility"></span>
                </a>
                <?php endif; ?>
                <?php if ( LIBMNS_Admin_FREE::libmns_current_user_can( 'owt7_lms_edit_user' ) ) : ?>
                <a href="admin.php?page=owt7_library_users&mod=user&fn=add&opt=edit&id=<?php echo base64_encode($user->id); ?>" title="<?php _e("Edit", "library-management-system"); ?>" class="action-btn edit-btn">
                    <span class="dashicons dashicons-edit"></span>
                </a>
                <?php endif; ?>
                <?php if ( LIBMNS_Admin_FREE::libmns_current_user_can( 'owt7_lms_delete_user' ) ) : ?>
                <a href="javascript:void(0);" title="<?php _e("Delete", "library-management-system"); ?>" class="action-btn delete-btn action-btn-delete" data-id="<?php echo base64_encode($user->id) ?>" data-module="<?php echo base64_encode('user'); ?>">
                    <span class="dashicons dashicons-trash"></span>
                </a>
                <?php endif; ?>
                </div>
            </td>
        </tr>
        <?php
    }
}
?>
