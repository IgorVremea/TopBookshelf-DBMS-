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

    <div class="lms-borrow-books">

        <div class="page-header">
            <div class="breadcrumb"> <?php _e("Library Management", "library-management-system"); ?> &raquo; <span class="active"><?php _e("Check Out Book", "library-management-system"); ?></span> </div>
            <div class="page-actions">
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=owt7_library_transactions' ) ); ?>" class="btn"><span class="dashicons dashicons-arrow-left-alt"></span> <?php _e("Back", "library-management-system"); ?></a>
            </div>
        </div>

        <div class="page-container">

            <div class="page-title-row">
                <div class="page-title">
                    <h2><?php _e("Check Out Book", "library-management-system"); ?></h2>
                </div>
            </div>

            <form class="owt7_lms_borrow_book owt7-borrow-form" id="owt7_lms_borrow_book" action="javascript:void(0);" method="post">

                <?php wp_nonce_field( 'owt7_library_actions', 'owt7_lms_nonce' ); ?>

                <div class="owt7-borrow-meta-row form-row">
                    <div class="form-group">
                        <label for="owt7_borrow_id_display"><?php _e("Borrow ID", "library-management-system"); ?></label>
                        <input type="text" id="owt7_borrow_id_display" name="owt7_borrow_id" class="lms-id-readonly" value="<?php echo esc_attr( ! empty( $params['next_borrow_id'] ) ? $params['next_borrow_id'] : '' ); ?>" readonly>
                        <p class="description"><?php _e("This ID will be saved when you submit.", "library-management-system"); ?></p>
                    </div>
                    <div class="form-group">
                        <label for="phone"><?php _e("Borrow Date", "library-management-system"); ?></label>
                        <input type="text" id="owt7_txt_borrow_date" name="owt7_txt_borrow_date"
                            value="<?php echo date('Y-m-d'); ?>" readonly placeholder="<?php esc_attr_e( 'YYYY-MM-DD', 'library-management-system' ); ?>">
                    </div>
                </div>

                <div class="owt7-borrow-grid-row owt7-borrow-row-1">
                    <div class="owt7-borrow-section owt7-borrow-section-book">
                        <h3 class="owt7-borrow-section-title"><?php _e("Category & Book", "library-management-system"); ?></h3>
                        <div id="owt7_borrow_manual_book_section" class="owt7-borrow-manual-section">
                            <input type="hidden" id="owt7_dd_category_id_h" value="" />
                            <input type="hidden" id="owt7_dd_book_id_h" value="" />
                            <div class="form-row owt7-borrow-fields-row">
                                <div class="form-group">
                                    <label for="owt7_dd_category_id"><?php _e("Category", "library-management-system"); ?> <span class="required">*</span></label>
                                    <select id="owt7_dd_category_id" name="owt7_dd_category_id">
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
                                <div class="form-group">
                                    <label for="owt7_dd_book_id"><?php _e("Book", "library-management-system"); ?> <span class="required">*</span></label>
                                    <select id="owt7_dd_book_id" name="owt7_dd_book_id">
                                        <option value=""><?php _e("-- Select Book --", "library-management-system"); ?></option>
                                    </select>
                                    <div id="owt7_book_copy_info" class="owt7-book-copy-info" style="display: none; margin-top: 8px; padding: 10px; background: #f0f6fc; border-left: 3px solid #2271b1; border-radius: 2px;">
                                        <div class="owt7-copies-left"><strong><?php _e("Total Copies Left", "library-management-system"); ?>:</strong> <span id="owt7_copies_left_value">0</span></div>
                                        <div class="owt7-next-accession" style="margin-top: 4px;"><strong><?php _e("Next accession to assign", "library-management-system"); ?>:</strong> <span id="owt7_next_accession_value">—</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="owt7-borrow-section owt7-borrow-section-user">
                        <h3 class="owt7-borrow-section-title"><?php _e("User", "library-management-system"); ?></h3>
                        <div class="form-row owt7-borrow-fields-row">
                            <div class="form-group">
                                <label for="owt7_dd_branch_id"><?php _e("Branch", "library-management-system"); ?> <span class="required">*</span></label>
                                <select required id="owt7_dd_branch_id" name="owt7_dd_branch_id">
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
                            </div>
                            <div class="form-group">
                                <label for="owt7_dd_u_id"><?php _e("User", "library-management-system"); ?> <span class="required">*</span></label>
                                <select required id="owt7_dd_u_id" name="owt7_dd_u_id">
                                    <option value=""><?php _e("-- Select User --", "library-management-system"); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="owt7-borrow-grid-row owt7-borrow-row-2">
                    <div class="owt7-borrow-section owt7-borrow-section-days">
                        <h3 class="owt7-borrow-section-title"><?php _e("Days", "library-management-system"); ?></h3>
                        <div class="form-row owt7-borrow-fields-row">
                            <div class="form-group">
                                <label for="owt7_dd_days"><?php _e("Days", "library-management-system"); ?> <span class="required">*</span></label>
                                <select required id="owt7_dd_days" name="owt7_dd_days">
                                    <option value=""><?php _e("-- Select Days --", "library-management-system"); ?></option>
                                    <?php 
                                    if(!empty($params['days']) && is_array($params['days'])){
                                        foreach($params['days'] as $key => $day){
                                            $day_value = str_replace(array("days", "Days", " Days", " days"), "", $day->days);
                                            ?>
                                    <option value="<?php echo $day_value; ?>"><?php echo $day_value; ?> <?php _e("Days", "library-management-system"); ?>
                                    </option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-row buttons-group owt7-borrow-submit-row">
                    <button class="btn submit-save-btn" type="submit"><?php _e("Submit & Save", "library-management-system"); ?></button>
                </div>
            </form>

        </div>
    </div>

</div>
