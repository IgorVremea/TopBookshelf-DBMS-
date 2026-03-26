<?php
/**
 * @link       https://onlinewebtutorblog.com
 * @since      3.5
 * @package    Library_Management_System
 * @subpackage Library_Management_System/admin/views/transactions/templates
 * @copyright  Copyright (c) 2026, Online Web Tutor
 * @license    GPL-2.0+ https://www.gnu.org/licenses/gpl-2.0.html
 * @author     Online Web Tutor
 */
if(!empty($params['returns']) && is_array($params['returns'])){
        $return_condition_labels = array(
            'normal_return'   => __( 'Normal return', 'library-management-system' ),
            'lost_book'       => __( 'Lost book', 'library-management-system' ),
            'late_return'     => __( 'Late return', 'library-management-system' ),
        );
        foreach($params['returns'] as $return){
            $has_fine      = $return->status && isset( $return->has_paid ) && (int) $return->has_paid === 1 && isset( $return->return_status ) && ! in_array( (int) $return->return_status, [ LIBMNS_DEFAULT_RETURN, LIBMNS_RETURN_REJECTED ], true );
            $fine_paid     = isset( $return->has_paid ) && (int) $return->has_paid === 2 && isset( $return->fine_amount ) && floatval( $return->fine_amount ) > 0;
            $can_download_receipt = ! empty( $return->id ) && ( ! isset( $return->return_status ) || ! in_array( (int) $return->return_status, [ LIBMNS_DEFAULT_RETURN, LIBMNS_RETURN_REJECTED ], true ) );
            $return_condition_display = isset( $return->return_condition ) && isset( $return_condition_labels[ $return->return_condition ] ) ? $return_condition_labels[ $return->return_condition ] : ( ! empty( $return->return_condition ) ? $return->return_condition : '—' );
            $returned_date = '';
            if ( ! $return->status && ( ! isset( $return->return_status ) || (int) $return->return_status != LIBMNS_RETURN_REJECTED ) ) {
                $returned_date = ! empty( $return->created_at ) ? date( 'Y-m-d', strtotime( $return->created_at ) ) : '';
            } elseif ( $return->status && ( ! isset( $return->has_paid ) || $return->has_paid == 1 ) && ( ! isset( $return->return_status ) || ! in_array( (int) $return->return_status, [ LIBMNS_DEFAULT_RETURN, LIBMNS_RETURN_REJECTED ], true ) ) ) {
                $returned_date = ! empty( $return->created_at ) ? date( 'Y-m-d', strtotime( $return->created_at ) ) : '';
            } elseif ( $return->status && isset( $return->return_status ) && ! in_array( (int) $return->return_status, [ LIBMNS_DEFAULT_RETURN, LIBMNS_RETURN_REJECTED ], true ) ) {
                $returned_date = ! empty( $return->created_at ) ? date( 'Y-m-d', strtotime( $return->created_at ) ) : '';
            }
            ?>
    <tr class="lms-history-row" data-return-id="<?php echo esc_attr( (int) $return->id ); ?>">
        <td class="lms-cell-return-type">
            <?php if($return->wp_user){ ?>
                <span class="lms-badge lms-badge-wp owt7_lms_wp_user"><?php _e('Self Return', 'library-management-system'); ?></span>
            <?php }else{ ?>
                <span class="lms-badge lms-badge-admin"><?php _e('LMS Admin', 'library-management-system'); ?></span>
            <?php } ?>
        </td>
        <td class="lms-cell-user">
            <div class="lms-info-block">
            <?php
                if($return->wp_user){
                    $user_data = get_userdata( $return->u_id );
                    ?>
                    <div class="lms-info-item"><span class="lms-info-label"><?php _e('User ID', 'library-management-system'); ?></span><span class="lms-info-value"><?php echo esc_html( !empty($return->user_u_id) ? $return->user_u_id : $return->u_id ); ?></span></div>
                    <div class="lms-info-item"><span class="lms-info-label"><?php _e('Name', 'library-management-system'); ?></span><span class="lms-info-value"><?php echo esc_html( $user_data->display_name ); ?></span></div>
                    <div class="lms-info-item"><span class="lms-info-label"><?php _e('Username', 'library-management-system'); ?></span><span class="lms-info-value"><?php echo esc_html( $user_data->user_login ); ?></span></div>

                    <div class="lms-info-item"><span class="lms-info-label"><?php _e('Role', 'library-management-system'); ?></span><span class="lms-info-value"><?php echo esc_html( ucfirst( implode( ', ', $user_data->roles ) ) ); ?></span></div>
                    <?php
                }else{
                    ?>
                    <div class="lms-info-item"><span class="lms-info-label"><?php _e('User ID', 'library-management-system'); ?></span><span class="lms-info-value"><?php echo esc_html( !empty($return->user_u_id) ? $return->user_u_id : $return->u_id ); ?></span></div>
                    <div class="lms-info-item"><span class="lms-info-label"><?php _e('Branch', 'library-management-system'); ?></span><span class="lms-info-value"><?php echo esc_html( $return->branch_name ); ?></span></div>
                    <div class="lms-info-item"><span class="lms-info-label"><?php _e('Name', 'library-management-system'); ?></span><span class="lms-info-value"><?php echo esc_html( $return->user_name ); ?></span></div>
                    <?php
                }
            ?>
            </div>
        </td>
        <td class="lms-cell-book">
            <div class="lms-info-block">
                <div class="lms-info-item"><span class="lms-info-label"><?php _e('Book ID', 'library-management-system'); ?></span><span class="lms-info-value"><?php echo esc_html( !empty($return->book_book_id) ? $return->book_book_id : '' ); ?></span></div>

                <div class="lms-info-item lms-info-item-title"><span class="lms-info-label"><?php _e('Name', 'library-management-system'); ?></span><span class="lms-info-value"><?php echo esc_html( $return->book_name ); ?></span></div>
                <div class="lms-info-item"><span class="lms-info-label"><?php _e('Acc No.', 'library-management-system'); ?></span><span class="lms-info-value"><?php echo esc_html( ! empty( $return->accession_number ) ? $return->accession_number : '—' ); ?></span></div>
            </div>
        </td>
        <td class="lms-cell-dates">
            <div class="lms-info-block">
                <div class="lms-info-item"><span class="lms-info-label"><?php _e('Issued', 'library-management-system'); ?></span><span class="lms-info-value lms-date"><?php echo date( 'Y-m-d', strtotime( $return->issued_on ) ); ?></span></div>
                <?php if ( $returned_date ) : ?>
                <div class="lms-info-item"><span class="lms-info-label book_returned"><?php _e('Returned', 'library-management-system'); ?></span><span class="lms-info-value lms-date"><?php echo esc_html( $returned_date ); ?></span></div>
                <?php elseif ( ! empty( $return->is_self_return ) && isset( $return->return_status ) && (int) $return->return_status === LIBMNS_DEFAULT_RETURN ) : ?>
                <div class="lms-info-item lms-info-status"><span class="lms-info-value"><span class="book_return_pending lms-status-badge"><?php _e('Return Pending', 'library-management-system'); ?> <span class="dashicons dashicons-clock"></span></span></span></div>
                <?php elseif ( ! empty( $return->is_self_return ) && isset( $return->return_status ) && (int) $return->return_status === LIBMNS_RETURN_REJECTED ) : ?>
                <div class="lms-info-item lms-info-status"><span class="lms-info-value"><span class="book_return_rejected lms-status-badge"><?php _e('Return Rejected', 'library-management-system'); ?> <span class="dashicons dashicons-no"></span></span></span></div>
                <?php else : ?>
                <div class="lms-info-item"><span class="lms-info-label"><?php _e('Returned', 'library-management-system'); ?></span><span class="lms-info-value">—</span></div>
                <?php endif; ?>
            </div>
        </td>
        <td class="lms-cell-fine <?php echo $has_fine ? 'lms-fine-pending' : ( $fine_paid ? 'lms-fine-paid' : '' ); ?>">
            <div class="lms-fine-status">
            <?php
            if ( $has_fine ) {
            ?>
                <div class="lms-fine-alert">
                    <div class="lms-fine-detail"><span class="lms-fine-meta"><?php _e("Total Fine", "library-management-system"); ?>:</span> <strong><?php echo esc_html( $return->fine_amount . " " . get_option( 'owt7_lms_currency' ) ); ?></strong></div>
                </div>
            <?php } elseif ( $fine_paid ) { ?>
                <div class="lms-fine-paid-wrap">
                    <span class="lms-fine-ok lms-fine-paid-badge owt7_label_text">
                        <span class="dashicons dashicons-yes-alt" aria-hidden="true"></span>
                        <?php _e( 'No fine (Paid)', 'library-management-system' ); ?>
                    </span>
                    <div class="lms-fine-paid-amount"><?php echo esc_html( $return->fine_amount . ' ' . get_option( 'owt7_lms_currency' ) ); ?></div>
                </div>
            <?php } elseif ( isset( $return->return_status ) && in_array( (int) $return->return_status, [ LIBMNS_DEFAULT_RETURN, LIBMNS_RETURN_REJECTED ], true ) ) { ?>
                <span class="lms-fine-na"><?php _e('--', 'library-management-system'); ?></span>
            <?php } else { ?>
                <span class="lms-fine-ok owt7_label_text"><?php _e('No fine', 'library-management-system'); ?></span>
            <?php } ?>
            </div>
        </td>
        <td class="lms-cell-actions">
            <div class="lms-action-wrap">
            <a href="javascript:void(0);" title="<?php esc_attr_e( 'View details', 'library-management-system' ); ?>"
                class="action-btn view-btn owt7_lms_view_return_details"><?php _e( 'View', 'library-management-system' ); ?></a>
            <?php if ( $can_download_receipt ) : ?>
            <a href="javascript:void(0);"
                class="action-btn receipt-btn owt7_lms_download_receipt_btn"
                data-return-db-id="<?php echo esc_attr( (int) $return->id ); ?>"
                title="<?php esc_attr_e( 'Download Receipt', 'library-management-system' ); ?>">
                <span class="dashicons dashicons-download" aria-hidden="true"></span>
                <?php _e( 'Download Receipt', 'library-management-system' ); ?>
            </a>
            <?php endif; ?>
            </div>
        </td>
    </tr>
    <?php
        }
    } 
?>
