<?php
/**
 * @link       https://onlinewebtutorblog.com
 * @since      3.5
 * @package    Library_Management_System
 * @subpackage Library_Management_System/admin/views/transactions
 * @copyright  Copyright (c) 2026, Online Web Tutor
 * @license    GPL-2.0+ https://www.gnu.org/licenses/gpl-2.0.html
 * @author     Online Web Tutor
 */
?>
<div class="owt7-lms owt7-lms-transactions">

    <div class="lms-borrow-history">

        <div class="page-header">
            <div class="breadcrumb"> <?php _e("Library Management", "library-management-system"); ?> &raquo; <span class="active"><?php _e("Return History", "library-management-system"); ?></span> </div>
            <div class="page-actions">
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=owt7_library_transactions' ) ); ?>" class="btn"><span class="dashicons dashicons-arrow-left-alt"></span> <?php _e("Back", "library-management-system"); ?></a>
            </div>
        </div>

        <div class="page-container">

            <div class="page-title-row">
                <div class="page-title">
                    <h2><?php _e("Return History", "library-management-system"); ?></h2>
                </div>
                <div class="filter-container">
                <select data-list="return_history" data-table="owt7_lms_tbl_return_list" data-option="branch" id="owt7_lms_dd_branch_filter" class="owt7_lms_dd_data_filter">
                    <option value=""><?php _e("-- Select Branch --", "library-management-system"); ?></option>
                    <?php 
                    if(!empty($params['branches']) && is_array($params['branches'])){
                        foreach($params['branches'] as $key => $branch){
                            ?>
                    <option value="<?php echo $branch->id; ?>"><?php echo ucfirst($branch->name); ?></option>
                    <?php
                        }
                    }
                    ?>
                </select>

                <select data-list="return_history" data-table="owt7_lms_tbl_return_list" data-option="category" id="owt7_lms_dd_category_filter" class="owt7_lms_dd_data_filter">
                    <option value=""><?php _e("-- Select Category --", "library-management-system"); ?></option>
                    <?php 
                    if(!empty($params['categories']) && is_array($params['categories'])){
                        foreach($params['categories'] as $key => $category){
                            ?>
                    <option value="<?php echo $category->id; ?>"><?php echo ucfirst($category->name); ?>
                    </option>
                    <?php
                        }
                    }
                    ?>
                </select>
                </div>
            </div>

            <table class="owt7-lms-table" id="tbl_books_return_history">
                <thead>
                    <tr>
                        <th><?php _e("Return To", "library-management-system"); ?></th>
                        <th><?php _e("User Details", "library-management-system"); ?></th>
                        <th><?php _e("Book Details", "library-management-system"); ?></th>
                        <th><?php _e("Return Details [Y-M-D]", "library-management-system"); ?></th>
                        <th><?php _e("Fine Status", "library-management-system"); ?></th>
                        <th><?php _e("Action", "library-management-system"); ?></th>
                    </tr>
                </thead>
                <tbody id="owt7_lms_tbl_return_list">
                    <?php
                        ob_start();
                        include_once LIBMNS_PLUGIN_DIR_PATH . 'admin/views/transactions/templates/owt7_library_return_list.php';
                        $template = ob_get_contents();
                        ob_end_clean();
                        echo $template;
                    ?>
                </tbody>
            </table>

        </div>
    </div>

    <?php /* View Return Details modal */ ?>
    <div id="owt7_lms_mdl_view_return_details" class="modal owt7-lms-view-return-modal" style="display: none;">
        <div class="modal-content owt7-lms-view-return-content">
            <span class="close">&times;</span>
            <h2><?php _e( 'Return Details', 'library-management-system' ); ?></h2>
            <div class="owt7-lms-view-return-sections">
                <div class="owt7-lms-view-return-row owt7-lms-view-return-row-cols">
                    <section class="owt7-lms-view-section owt7-lms-view-section-book">
                        <h3 class="owt7-lms-view-section-title"><?php _e( 'Book Details', 'library-management-system' ); ?></h3>
                        <div class="owt7-lms-view-detail-body" id="owt7_lms_view_book_details"></div>
                    </section>
                    <section class="owt7-lms-view-section owt7-lms-view-section-user">
                        <h3 class="owt7-lms-view-section-title"><?php _e( 'User Details', 'library-management-system' ); ?></h3>
                        <div class="owt7-lms-view-detail-body" id="owt7_lms_view_user_details"></div>
                    </section>
                </div>
                <section class="owt7-lms-view-section owt7-lms-view-section-remark">
                    <h3 class="owt7-lms-view-section-title"><?php _e( 'Remark and fine details', 'library-management-system' ); ?></h3>
                    <div class="owt7-lms-view-detail-body" id="owt7_lms_view_remark_details"></div>
                </section>
            </div>
            <div class="owt7-lms-view-return-actions">
                <button type="button" class="btn submit-save-btn owt7_pay_late_fine owt7_lms_view_pay_now_btn" id="owt7_lms_view_pay_now_btn" style="display: none;"><?php _e( 'Pay now', 'library-management-system' ); ?></button>
                <button type="button" class="btn receipt-btn owt7_lms_download_receipt_btn" id="owt7_lms_view_download_receipt_btn" data-return-db-id="" style="display: none;">
                    <span class="dashicons dashicons-download" aria-hidden="true"></span>
                    <?php _e( 'Download Receipt', 'library-management-system' ); ?>
                </button>
            </div>
        </div>
    </div>

</div>
