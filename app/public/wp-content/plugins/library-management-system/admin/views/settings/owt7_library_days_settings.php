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

    <div class="owt7_lms_settings">

        <div class="page-header">
            <div class="breadcrumb"> <?php _e('Library System', 'library-management-system'); ?> >> <span class="active"><?php _e('Day(s) Settings', 'library-management-system'); ?></span> </div>
            <div class="page-actions">
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=owt7_library_settings#general' ) ); ?>" class="btn">
                    <span class="dashicons dashicons-arrow-left-alt"></span>
                    <?php _e('Back', 'library-management-system'); ?>
                </a>
            </div>
        </div>

        <div class="page-container">

            <div class="page-title">
                <h2><?php _e('Day(s) List', 'library-management-system'); ?></h2>
            </div>

            <table class="owt7-lms-table">
                <thead>
                    <tr>
                        <th><?php _e('S No', 'library-management-system'); ?></th>
                        <th><?php _e('Day(s)', 'library-management-system'); ?></th>
                        <th><?php _e('Action', 'library-management-system'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        if(!empty($params['days']) && is_array($params['days'])){
                            $sNo = 1;
                            foreach($params['days'] as $day){
                                ?>
                    <tr>
                        <td><?php echo $sNo++; ?></td>
                        <td><?php echo $day->days; ?> <?php _e('days', 'library-management-system'); ?></td>
                        <td>
                            <?php if ( LIBMNS_Admin_FREE::libmns_current_user_can( 'owt7_lms_manage_days' ) ) : ?>
                            <a href="javascript:void(0);" title="<?php _e('Delete', 'library-management-system'); ?>" class="action-btn delete-btn action-btn-delete"
                                data-id="<?php echo base64_encode($day->id) ?>"
                                data-module="<?php echo base64_encode('days'); ?>">
                                <span class="dashicons dashicons-trash"></span>
                            </a>
                            <?php endif; ?>
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


<div class="owt7_lms_modal_section">
    <?php
    ob_start();
    $fileName = "owt7_mdl_days_settings";
    include_once LIBMNS_PLUGIN_DIR_PATH . "admin/views/settings/modals/{$fileName}.php";
    $template = ob_get_contents();
    ob_end_clean();
    echo $template;
    ?>
</div>
