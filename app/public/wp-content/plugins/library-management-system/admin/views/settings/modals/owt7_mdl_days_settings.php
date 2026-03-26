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
global $wpdb;
$days_table = LIBMNS_Table_Helper_FREE::get_table_name( 'issue_days' );
$days_list = array(
	(object) array(
		'id'   => 1,
		'days' => LIBMNS_DEFAULT_BORROW_DAYS,
	),
);
?>
<!-- The Modal -->
<div id="owt7_lms_mdl_days" class="modal" style="display: none;">
    <div class="modal-content owt7-lms-days-modal-content">
        <span class="close">&times;</span>
        <h2 class="owt7-lms-days-modal-heading"><?php _e('Borrow days', 'library-management-system'); ?></h2>

        <div class="owt7-lms-days-table-card">
            <h3 class="owt7-lms-days-table-card-title"><?php _e('Existing borrow days', 'library-management-system'); ?></h3>
            <p class="description"><?php esc_html_e( 'The free version uses a fixed 30 day borrow period.', 'library-management-system' ); ?></p>
            <div class="owt7-lms-days-table-scroll">
                <table class="owt7-lms-table owt7-lms-days-modal-table">
                    <thead>
                        <tr>
                            <th><?php _e('S No', 'library-management-system'); ?></th>
                            <th><?php _e('Day(s)', 'library-management-system'); ?></th>
                        </tr>
                    </thead>
                    <tbody id="owt7_lms_days_modal_tbody">
                        <?php
                        if ( ! empty( $days_list ) && is_array( $days_list ) ) {
                            $sNo = 1;
                            foreach ( $days_list as $day ) {
                                ?>
                        <tr>
                            <td><?php echo (int) $sNo++; ?></td>
                            <td><?php echo (int) $day->days; ?> <?php _e('days', 'library-management-system'); ?></td>
                        </tr>
                        <?php
                            }
                        } else {
                            ?>
                        <tr class="owt7-lms-days-empty-row">
                            <td colspan="2"><?php _e( 'No days added yet.', 'library-management-system' ); ?></td>
                        </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
