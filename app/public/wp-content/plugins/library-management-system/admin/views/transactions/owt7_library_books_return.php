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

    <div class="lms-return-books">

        <div class="page-header">
            <div class="breadcrumb"> <?php _e("Library Management", "library-management-system"); ?> &raquo; <span class="active"><?php _e("Return Books", "library-management-system"); ?></span> </div>
            <div class="page-actions">
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=owt7_library_transactions' ) ); ?>" class="btn"><span class="dashicons dashicons-arrow-left-alt"></span> <?php _e("Back", "library-management-system"); ?></a>
            </div>
        </div>

        <div class="page-container">

            <div class="page-title-row">
                <div class="page-title">
                    <h2><?php _e("Return Books", "library-management-system"); ?></h2>
                    <p class="page-description"><?php _e("Select branch and user to see their borrowed books, then choose which books to return.", "library-management-system"); ?></p>
                </div>
            </div>

            <form class="owt7_lms_return_book owt7-return-form" id="owt7_lms_return_book" action="javascript:void(0);" method="post">

                <?php wp_nonce_field( 'owt7_library_actions', 'owt7_lms_nonce' ); ?>

                <div class="return-sections">

                    <!-- Row 1: Return details (first) | How to select book (side by side) -->
                    <div class="owt7-return-top-row">
                        <section class="return-section return-section-meta owt7-return-col">
                            <h3 class="return-section-title"><?php _e("Return details", "library-management-system"); ?></h3>
                            <p class="return-section-desc"><?php _e("Today's date is used as the return date.", "library-management-system"); ?></p>
                            <div class="return-meta-row return-meta-row-inline">
                                <span class="return-meta-label"><?php _e("Return ID", "library-management-system"); ?></span>
                                <span class="return-meta-value lms-id-readonly" id="owt7_return_id_display"><?php echo esc_html( ! empty( $params['next_return_id'] ) ? $params['next_return_id'] : '—' ); ?></span>
                                <input type="hidden" name="owt7_return_id" id="owt7_return_id" value="<?php echo esc_attr( ! empty( $params['next_return_id'] ) ? $params['next_return_id'] : '' ); ?>">
                                <span class="return-meta-sep">|</span>
                                <span class="return-meta-label"><?php _e("Return date", "library-management-system"); ?></span>
                                <span class="return-meta-value" id="owt7_return_date_display"><?php echo date('Y-m-d'); ?></span>
                                <input type="hidden" id="owt7_txt_borrow_date" name="owt7_txt_borrow_date" value="<?php echo date('Y-m-d'); ?>">
                            </div>
                            <p class="return-section-desc"><?php _e("The shown Return ID will be used when you submit. For multiple books, the first return uses this ID; additional returns get a new ID each.", "library-management-system"); ?></p>
                        </section>
                    </div>

                    <!-- Section: Branch & User -->
                    <section class="return-section return-section-who">
                        <h3 class="return-section-title"><?php _e("Who is returning?", "library-management-system"); ?></h3>
                        <p class="return-section-desc"><?php _e("Choose the branch and the user to load their borrowed books.", "library-management-system"); ?></p>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="owt7_dd_branch_id"><?php _e("Branch", "library-management-system"); ?> <span class="required">*</span></label>
                                <select required id="owt7_dd_branch_id" name="owt7_dd_branch_id">
                                    <option value=""><?php _e("-- Select Branch --", "library-management-system"); ?></option>
                                    <?php
                                    if ( ! empty( $params['branches'] ) && is_array( $params['branches'] ) ) {
                                        foreach ( $params['branches'] as $key => $branch ) {
                                            ?>
                                            <option value="<?php echo esc_attr( $branch->id ); ?>"><?php echo esc_html( ucfirst( $branch->name ) ); ?></option>
                                            <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="owt7_dd_u_id"><?php _e("User", "library-management-system"); ?> <span class="required">*</span></label>
                                <select required id="owt7_dd_borrow_u_id" name="owt7_dd_borrow_u_id">
                                    <option value=""><?php _e("-- Select User --", "library-management-system"); ?></option>
                                </select>
                            </div>
                        </div>
                    </section>

                    <!-- Section: Books to return -->
                    <section class="return-section return-section-books hide-input" id="return-section-books">
                        <div class="return-section-books-header">
                            <div>
                                <h3 class="return-section-title"><?php _e("Books to return", "library-management-system"); ?> <span class="required">*</span></h3>
                                <p class="return-section-desc"><?php _e("Select one or more borrowed books to mark as returned.", "library-management-system"); ?></p>
                            </div>
                            <div class="return-books-actions" id="owt7_return_books_actions" style="display: none;">
                                <button type="button" class="btn btn-link owt7-select-all-books"><?php _e("Select all", "library-management-system"); ?></button>
                                <span class="return-actions-sep">|</span>
                                <button type="button" class="btn btn-link owt7-deselect-all-books"><?php _e("Deselect all", "library-management-system"); ?></button>
                            </div>
                        </div>
                        <div class="return-books-list" id="owt7_return_books_container">
                            <div class="checkbox-group owt7-books-cards" id="owt7_chk_books_list"></div>
                        </div>
                    </section>

                </div>

                <div class="form-row buttons-group return-submit-row">
                    <button class="btn submit-save-btn" type="button" id="owt7_return_open_modal_btn"><?php _e("Submit & Save", "library-management-system"); ?></button>
                </div>
            </form>

            <!-- Return Status Modal: select condition, remark, see fine, then submit -->
            <div id="owt7_lms_mdl_return_status" class="modal owt7-lms-return-status-modal" style="display: none;">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <h2><?php _e("Return Status & Fine", "library-management-system"); ?></h2>
                    <p class="return-section-desc"><?php _e("Select the condition of the returned book(s) and add a remark if needed. Total fine will be shown based on your selection.", "library-management-system"); ?></p>
                    <div class="form-group">
                        <label for="owt7_return_condition"><?php _e("Return status", "library-management-system"); ?> <span class="required">*</span></label>
                        <select id="owt7_return_condition" name="owt7_return_condition" class="form-control" required>
                            <option value="normal_return"><?php _e("Normal return", "library-management-system"); ?></option>
                            <option value="lost_book"><?php _e("Lost book", "library-management-system"); ?></option>
                            <option value="late_return"><?php _e("Late return", "library-management-system"); ?></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="owt7_return_remark"><?php _e("Remark", "library-management-system"); ?></label>
                        <textarea id="owt7_return_remark" name="owt7_return_remark" class="form-control" rows="3" placeholder="<?php esc_attr_e("Optional notes about the return", "library-management-system"); ?>"></textarea>
                    </div>
                    <div class="form-group owt7-return-total-fine-wrap">
                        <span class="owt7-lms-field-hint"><?php _e("Total late fine:", "library-management-system"); ?></span>
                        <strong id="owt7_return_total_fine_display" class="owt7_return_total_fine_display">0 <?php echo esc_html( get_option( 'owt7_lms_currency', '' ) ); ?></strong>
                    </div>
                    <div class="form-row buttons-group">
                        <button type="button" class="btn submit-save-btn" id="owt7_return_status_modal_submit"><?php _e("Submit & Save", "library-management-system"); ?></button>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
