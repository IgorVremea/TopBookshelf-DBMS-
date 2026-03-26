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
if(!empty($params['borrows']) && is_array($params['borrows'])){
    foreach($params['borrows'] as $borrow){
        ?>
        <tr class="lms-history-row">
            <td class="lms-cell-id">
                <span class="lms-id-badge">#<?php echo esc_html( $borrow->borrow_id ); ?></span>
                <?php if($borrow->wp_user){ ?>
                    <span class="lms-badge lms-badge-wp owt7_lms_wp_user"><?php _e('Self Checkout', 'library-management-system'); ?></span>
                <?php } ?>
            </td>
            <td class="lms-cell-user">
                <div class="lms-info-block">
                <?php
                    if($borrow->wp_user){
                        $user_data = get_userdata( $borrow->u_id );
                        ?>
                        <div class="lms-info-item"><span class="lms-info-label"><?php _e('User ID', 'library-management-system'); ?></span><span class="lms-info-value"><?php echo esc_html( !empty($borrow->user_u_id) ? $borrow->user_u_id : $borrow->u_id ); ?></span></div>
                        <div class="lms-info-item"><span class="lms-info-label"><?php _e('Name', 'library-management-system'); ?></span><span class="lms-info-value"><?php echo esc_html( $user_data->display_name ); ?></span></div>
                        <div class="lms-info-item"><span class="lms-info-label"><?php _e('Username', 'library-management-system'); ?></span><span class="lms-info-value"><?php echo esc_html( $user_data->user_login ); ?></span></div>
                        <div class="lms-info-item"><span class="lms-info-label"><?php _e('Email', 'library-management-system'); ?></span><span class="lms-info-value"><?php echo esc_html( $user_data->user_email ); ?></span></div>
                        <div class="lms-info-item"><span class="lms-info-label"><?php _e('Role', 'library-management-system'); ?></span><span class="lms-info-value"><?php echo esc_html( ucfirst( implode( ', ', $user_data->roles ) ) ); ?></span></div>
                        <?php
                    }else{
                        ?>
                        <div class="lms-info-item"><span class="lms-info-label"><?php _e('User ID', 'library-management-system'); ?></span><span class="lms-info-value"><?php echo esc_html( !empty($borrow->user_u_id) ? $borrow->user_u_id : $borrow->u_id ); ?></span></div>
                        <div class="lms-info-item"><span class="lms-info-label"><?php _e('Branch', 'library-management-system'); ?></span><span class="lms-info-value"><?php echo esc_html( $borrow->branch_name ); ?></span></div>
                        <div class="lms-info-item"><span class="lms-info-label"><?php _e('Name', 'library-management-system'); ?></span><span class="lms-info-value"><?php echo esc_html( $borrow->user_name ); ?></span></div>
                        <div class="lms-info-item"><span class="lms-info-label"><?php _e('Email', 'library-management-system'); ?></span><span class="lms-info-value"><?php echo esc_html( $borrow->user_email ); ?></span></div>
                        <?php
                    }
                ?>
                </div>
            </td>
            <td class="lms-cell-book">
                <div class="lms-info-block">
                    <div class="lms-info-item"><span class="lms-info-label"><?php _e('Book ID', 'library-management-system'); ?></span><span class="lms-info-value"><?php echo esc_html( !empty($borrow->book_book_id) ? $borrow->book_book_id : '' ); ?></span></div>
                    <div class="lms-info-item"><span class="lms-info-label"><?php _e('Category', 'library-management-system'); ?></span><span class="lms-info-value"><?php echo esc_html( $borrow->category_name ); ?></span></div>
                    <div class="lms-info-item lms-info-item-title"><span class="lms-info-label"><?php _e('Name', 'library-management-system'); ?></span><span class="lms-info-value"><?php echo esc_html( $borrow->book_name ); ?></span></div>
                    <div class="lms-info-item"><span class="lms-info-label"><?php _e('Acc No.', 'library-management-system'); ?></span><span class="lms-info-value"><?php echo esc_html( ! empty( $borrow->accession_number ) ? $borrow->accession_number : '—' ); ?></span></div>
                </div>
            </td>
            <td class="lms-cell-dates">
                <div class="lms-info-block">
                    <div class="lms-info-item"><span class="lms-info-label"><?php _e('Days', 'library-management-system'); ?></span><span class="lms-info-value"><?php echo esc_html( $borrow->borrows_days ); ?> <?php _e('days', 'library-management-system'); ?></span></div>
                    <div class="lms-info-item"><span class="lms-info-label"><?php _e('Issued on', 'library-management-system'); ?></span><span class="lms-info-value lms-date"><?php echo date("Y-m-d", strtotime($borrow->created_at)); ?></span></div>
                    <div class="lms-info-item"><span class="lms-info-label"><?php _e('Return by', 'library-management-system'); ?></span><span class="lms-info-value lms-date"><?php echo esc_html( $borrow->return_date ); ?></span></div>
                    <div class="lms-info-item lms-info-status"><span class="lms-info-label"><?php _e("Status", "library-management-system"); ?></span><span class="lms-info-value"><?php if(!$borrow->is_self_checkout && $borrow->status){ ?><span class="borrow_status owt7_lms_pending_borrow_status lms-status-badge"><?php _e('Return Pending', 'library-management-system'); ?> <span class="dashicons dashicons-clock"></span></span><?php } elseif($borrow->is_self_checkout && $borrow->checkout_status == LIBMNS_DEFAULT_CHECKOUT && $borrow->status) { ?><span class="borrow_status owt7_lms_approval_pending_status lms-status-badge"><?php _e('Approval Pending', 'library-management-system'); ?> <span class="dashicons dashicons-admin-users"></span></span><?php } elseif(($borrow->is_self_checkout && $borrow->checkout_status == LIBMNS_CHECKOUT_SELF_APPROVED && $borrow->status) || ($borrow->is_self_checkout && $borrow->checkout_status == LIBMNS_CHECKOUT_APPROVED_BY_ADMIN && $borrow->status) ) { ?><span class="borrow_status owt7_lms_pending_borrow_status lms-status-badge"><?php _e('Return Pending', 'library-management-system'); ?> <span class="dashicons dashicons-clock"></span></span><?php } elseif($borrow->is_self_checkout && $borrow->checkout_status == LIBMNS_CHECKOUT_REJECTED && $borrow->status) { ?><span class="borrow_status owt7_lms_pending_borrow_status owt7_lms_rejected_borrow_status lms-status-badge"><?php _e('Rejected', 'library-management-system'); ?> <span class="dashicons dashicons-no"></span></span><?php } else { ?><span class="borrow_status owt7_lms_borrow_status lms-status-badge lms-status-success"><?php _e('Returned', 'library-management-system'); ?><i class="dashicons dashicons-yes"></i></span><?php } ?></span></div>
                </div>
            </td>
            <td class="lms-cell-actions">
                <div class="lms-action-wrap">
                <a href="javascript:void(0);" class="action-btn view-btn owt7_lms_view_borrow_details"
                    data-borrow-id="<?php echo esc_attr( $borrow->borrow_id ); ?>"
                    data-user-id="<?php echo esc_attr( !empty($borrow->user_u_id) ? $borrow->user_u_id : $borrow->u_id ); ?>"
                    data-user-name="<?php echo esc_attr( $borrow->wp_user && isset( $user_data->display_name ) ? $user_data->display_name : $borrow->user_name ); ?>"
                    data-book-id="<?php echo esc_attr( !empty($borrow->book_book_id) ? $borrow->book_book_id : '' ); ?>"
                    data-book-name="<?php echo esc_attr( $borrow->book_name ); ?>"
                    data-category="<?php echo esc_attr( $borrow->category_name ); ?>"
                    data-branch="<?php echo esc_attr( $borrow->branch_name ); ?>"
                    data-accession="<?php echo esc_attr( ! empty( $borrow->accession_number ) ? $borrow->accession_number : '—' ); ?>"
                    data-days="<?php echo esc_attr( $borrow->borrows_days ); ?>"
                    data-issued-on="<?php echo esc_attr( date("Y-m-d", strtotime($borrow->created_at)) ); ?>"
                    data-return-by="<?php echo esc_attr( $borrow->return_date ); ?>">
                    <?php _e('View', 'library-management-system'); ?>
                </a>
                <?php
                $owt7_qr_is_return_pending = (
                    ( ! $borrow->is_self_checkout && $borrow->status ) ||
                    ( $borrow->is_self_checkout && $borrow->status && in_array( (int) $borrow->checkout_status, array( LIBMNS_CHECKOUT_SELF_APPROVED, LIBMNS_CHECKOUT_APPROVED_BY_ADMIN ), true ) )
                );
                if ( $owt7_qr_is_return_pending && LIBMNS_Admin_FREE::libmns_current_user_can( 'owt7_lms_return_book' ) ) :
                ?>
                <a href="javascript:void(0);"
                    class="action-btn return-btn owt7_lms_quick_return_btn"
                    data-borrow-db-id="<?php echo esc_attr( $borrow->id ); ?>"
                    aria-label="<?php esc_attr_e( 'Return this book', 'library-management-system' ); ?>">
                    <span class="dashicons dashicons-undo" aria-hidden="true"></span>
                    <?php _e( 'Return', 'library-management-system' ); ?>
                </a>
                <?php endif; ?>
                </div>
            </td>
        </tr>
        <?php
        }
    } 
?>