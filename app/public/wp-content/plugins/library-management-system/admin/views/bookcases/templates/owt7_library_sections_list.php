<?php
/**
 * @link       https://onlinewebtutorblog.com
 * @since      3.5
 * @package    Library_Management_System
 * @subpackage Library_Management_System/admin/views/bookcases/templates
 * @copyright  Copyright (c) 2026, Online Web Tutor
 * @license    GPL-2.0+ https://www.gnu.org/licenses/gpl-2.0.html
 * @author     Online Web Tutor
 */

if(!empty($params['sections']) && is_array($params['sections'])){
    
    foreach($params['sections'] as $section){
        $sec_books_total = isset( $section->total_books ) ? (int) $section->total_books : 0;
        ?>
        <tr>
            <td><?php echo ucwords($section->bookcase_name); ?></td>
            <td><?php echo ucwords($section->name); ?></td>
            <td><?php if ( $sec_books_total > 0 ) : ?>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=owt7_library_books&section_id=' . $section->id ) ); ?>" class="owt7-lms-total-books-badge"><?php echo esc_html( (string) $sec_books_total ); ?></a>
            <?php else : ?>
                <span class="owt7-lms-total-books-badge"><?php echo esc_html( (string) $sec_books_total ); ?></span>
            <?php endif; ?></td>
            <td>
                <?php if($section->status){ ?>
                <a href="javascript:void(0);" class="action-btn view-btn">
                    <?php _e("Active", "library-management-system"); ?>
                </a>
                <?php }else{ ?>
                <a href="javascript:void(0);" class="action-btn delete-btn">
                    <?php _e("Inactive", "library-management-system"); ?>
                </a>
                <?php } ?>
            </td>
            <td><?php echo $section->created_at ? date("Y-m-d", strtotime($section->created_at)) : ''; ?></td>
            <td>
                <?php if ( LIBMNS_Admin_FREE::libmns_current_user_can( 'owt7_lms_list_sections' ) ) : ?>
                <a href="admin.php?page=owt7_library_bookcases&mod=section&fn=add&opt=view&id=<?php echo base64_encode($section->id); ?>"
                    title="<?php _e('View', 'library-management-system'); ?>" class="action-btn view-btn">
                    <span class="dashicons dashicons-visibility"></span>
                </a>
                <?php endif; ?>
                <?php if ( LIBMNS_Admin_FREE::libmns_current_user_can( 'owt7_lms_edit_section' ) ) : ?>
                <a href="admin.php?page=owt7_library_bookcases&mod=section&fn=add&opt=edit&id=<?php echo base64_encode($section->id); ?>"
                    title="<?php _e('Edit', 'library-management-system'); ?>" class="action-btn edit-btn">
                    <span class="dashicons dashicons-edit"></span>
                </a>
                <?php endif; ?>
                <?php if ( LIBMNS_Admin_FREE::libmns_current_user_can( 'owt7_lms_delete_section' ) ) : ?>
                <a href="javascript:void(0);" title="<?php _e('Delete', 'library-management-system'); ?>" class="action-btn delete-btn action-btn-delete"
                    data-id="<?php echo base64_encode($section->id) ?>"
                    data-module="<?php echo base64_encode('section'); ?>">
                    <span class="dashicons dashicons-trash"></span>
                </a>
                <?php endif; ?>
            </td>
        </tr>
    <?php
    }
}
?>
