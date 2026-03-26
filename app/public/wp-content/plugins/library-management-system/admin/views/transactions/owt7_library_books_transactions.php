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

    <div class="lms-borrow-histor">

        <div class="page-header">
            <div class="breadcrumb"> <?php _e("Library Management", "library-management-system"); ?> &raquo; <span class="active"><?php _e("Borrow History", "library-management-system"); ?></span> </div>
            <div class="page-actions">
                <?php if ( LIBMNS_Admin_FREE::libmns_current_user_can( 'owt7_lms_borrow_book' ) ) : ?>
                <a href="admin.php?page=owt7_library_transactions&mod=books&fn=borrow" class="btn"><span class="dashicons dashicons-book"></span> <?php _e("Check Out Book", "library-management-system"); ?></a>
                <?php endif; ?>
                <?php if ( LIBMNS_Admin_FREE::libmns_current_user_can( 'owt7_lms_return_book' ) ) : ?>
                <a href="admin.php?page=owt7_library_transactions&mod=books&fn=return" class="btn"><span class="dashicons dashicons-undo"></span> <?php _e("Return Books", "library-management-system"); ?></a>
                <?php endif; ?>
                <?php if ( LIBMNS_Admin_FREE::libmns_current_user_can( 'owt7_lms_view_return_list' ) ) : ?>
                <a href="admin.php?page=owt7_library_transactions&mod=books&fn=return-history" class="btn"><span class="dashicons dashicons-backup"></span> <?php _e("Return History", "library-management-system"); ?></a>
                <?php endif; ?>
            </div>
        </div>

        <div class="page-container">

            <div class="page-title-row">
                <div class="page-title">
                    <h2><?php _e("Borrow History (Active Loans)", "library-management-system"); ?></h2>
                </div>
                <div class="filter-container">
                    <label for="owt7_lms_filter"><?php _e("Filter by:", "library-management-system"); ?></label>

                <select data-list="borrow_history" data-table="owt7_lms_tbl_borrow_list" data-option="branch"
                    id="owt7_lms_dd_branch_filter" class="owt7_lms_dd_data_filter">
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

                <select data-list="borrow_history" data-table="owt7_lms_tbl_borrow_list" data-option="category"
                    id="owt7_lms_dd_category_filter" class="owt7_lms_dd_data_filter">
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

            <table class="owt7-lms-table" id="tbl_books_borrow_history">
                <thead>
                    <tr>
                        <th><?php _e("Borrow ID", "library-management-system"); ?></th>
                        <th><?php _e("User Details", "library-management-system"); ?></th>
                        <th><?php _e("Book Details", "library-management-system"); ?></th>
                        <th><?php _e("Borrow Details [Y-M-D]", "library-management-system"); ?></th>
                        <th><?php _e("Action", "library-management-system"); ?></th>
                    </tr>
                </thead>
                <tbody id="owt7_lms_tbl_borrow_list">
                    <?php
                        ob_start();
                        include_once LIBMNS_PLUGIN_DIR_PATH . 'admin/views/transactions/templates/owt7_library_borrow_list.php';
                        $template = ob_get_contents();
                        ob_end_clean();
                        echo $template;
                    ?>
                </tbody>
            </table>

        </div>
    </div>

    <?php
    // Return Status modal (for Return button in borrow list – same IDs as Return Books page so shared JS works)
    ?>
    <div id="owt7_lms_mdl_return_status_txn" class="modal owt7-lms-return-status-modal" style="display: none;">
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

    <!-- Quick Return Modal: opened from Return button in borrow history list -->
    <div id="owt7_lms_mdl_quick_return" class="modal owt7-lms-quick-return-modal" style="display: none;" role="dialog" aria-modal="true" aria-labelledby="owt7_lms_qr_title" aria-hidden="true">
        <div class="modal-content owt7-lms-quick-return-content">

            <button type="button" class="close owt7-lms-qr-close" aria-label="<?php esc_attr_e( 'Close', 'library-management-system' ); ?>">&times;</button>
            <h2 class="owt7-lms-qr-title" id="owt7_lms_qr_title">
                <span class="dashicons dashicons-undo owt7-lms-qr-title-icon" aria-hidden="true"></span>
                <?php esc_html_e( 'Return Book', 'library-management-system' ); ?>
            </h2>

            <!-- Loader -->
            <div class="owt7-lms-qr-loader" id="owt7_lms_qr_loader" aria-live="polite">
                <span class="owt7-lms-css-loader owt7-lms-loader-lg" aria-hidden="true"></span>
                <span class="owt7-lms-qr-loader-text"><?php esc_html_e( 'Loading details…', 'library-management-system' ); ?></span>
            </div>

            <!-- Main content (hidden while loading) -->
            <div id="owt7_lms_qr_body" class="owt7-lms-qr-body" style="display:none;">

                <!-- Two-column info grid: Borrower + Book -->
                <div class="owt7-lms-qr-info-grid">
                    <div class="owt7-lms-view-section owt7-lms-qr-section-user">
                        <h3 class="owt7-lms-view-section-title">
                            <span class="dashicons dashicons-admin-users" aria-hidden="true"></span>
                            <?php esc_html_e( 'Borrower Information', 'library-management-system' ); ?>
                        </h3>
                        <div class="owt7-lms-view-detail-body" id="owt7_lms_qr_user_info"></div>
                    </div>
                    <div class="owt7-lms-view-section owt7-lms-qr-section-book">
                        <h3 class="owt7-lms-view-section-title">
                            <span class="dashicons dashicons-book-alt" aria-hidden="true"></span>
                            <?php esc_html_e( 'Book Details', 'library-management-system' ); ?>
                        </h3>
                        <div class="owt7-lms-view-detail-body" id="owt7_lms_qr_book_info"></div>
                    </div>
                </div>

                <!-- Loan details strip -->
                <div class="owt7-lms-view-section owt7-lms-qr-section-loan">
                    <h3 class="owt7-lms-view-section-title">
                        <span class="dashicons dashicons-calendar-alt" aria-hidden="true"></span>
                        <?php esc_html_e( 'Loan Details', 'library-management-system' ); ?>
                    </h3>
                    <div class="owt7-lms-view-detail-body" id="owt7_lms_qr_loan_info"></div>
                </div>

                <div class="owt7-lms-qr-divider" role="separator">
                    <span class="owt7-lms-qr-divider-label">
                        <span class="dashicons dashicons-clipboard" aria-hidden="true"></span>
                        <?php esc_html_e( 'Return Status & Fine', 'library-management-system' ); ?>
                    </span>
                </div>

                <!-- Return Status & Fine section -->
                <div class="owt7-lms-qr-return-section">
                    <p class="return-section-desc"><?php esc_html_e( 'Select the condition of the returned book and add a remark if needed. Total fine will be calculated based on your selection.', 'library-management-system' ); ?></p>

                    <div class="owt7-lms-qr-fields-row">
                        <div class="form-group owt7-lms-qr-field">
                            <label for="owt7_lms_qr_condition"><?php esc_html_e( 'Return status', 'library-management-system' ); ?> <span class="required">*</span></label>
                            <select id="owt7_lms_qr_condition" name="owt7_lms_qr_condition" class="form-control" required>
                                <option value="normal_return"><?php esc_html_e( 'Normal return', 'library-management-system' ); ?></option>
                                <option value="lost_book"><?php esc_html_e( 'Lost book', 'library-management-system' ); ?></option>
                                <option value="late_return"><?php esc_html_e( 'Late return', 'library-management-system' ); ?></option>
                            </select>
                        </div>
                        <div class="form-group owt7-lms-qr-fine-display-wrap">
                            <span class="owt7-lms-field-hint"><?php esc_html_e( 'Total fine:', 'library-management-system' ); ?></span>
                            <strong id="owt7_lms_qr_fine_display" class="owt7_return_total_fine_display">0 <?php echo esc_html( get_option( 'owt7_lms_currency', '' ) ); ?></strong>
                        </div>
                    </div>

                    <!-- Late days notice: visible only when late_return is selected -->
                    <div id="owt7_lms_qr_late_notice" class="owt7-lms-qr-late-notice" style="display:none;" role="status">
                        <span class="owt7-lms-qr-late-icon dashicons dashicons-warning" aria-hidden="true"></span>
                        <span class="owt7-lms-qr-late-text"></span>
                    </div>

                    <div class="form-group">
                        <label for="owt7_lms_qr_remark"><?php esc_html_e( 'Remark', 'library-management-system' ); ?></label>
                        <textarea id="owt7_lms_qr_remark" name="owt7_lms_qr_remark" class="form-control" rows="3" placeholder="<?php esc_attr_e( 'Optional notes about the return…', 'library-management-system' ); ?>"></textarea>
                    </div>
                </div>

                <!-- Hidden form carrying the borrow DB id and return id for submission -->
                <form id="owt7_lms_qr_hidden_form" style="display:none;">
                    <?php wp_nonce_field( 'owt7_library_actions', 'owt7_lms_nonce' ); ?>
                    <input type="hidden" id="owt7_lms_qr_borrow_db_id"  name="owt7_borrow_books_id[]" value="">
                    <input type="hidden" id="owt7_lms_qr_return_id"     name="owt7_return_id"         value="">
                </form>

                <div class="form-row buttons-group owt7-lms-qr-actions">
                    <button type="button" class="btn btn-secondary owt7-lms-qr-close owt7_return_status_modal_cancel">
                        <?php esc_html_e( 'Cancel', 'library-management-system' ); ?>
                    </button>
                    <button type="button" class="btn submit-save-btn" id="owt7_lms_qr_submit">
                        <span class="dashicons dashicons-yes" aria-hidden="true"></span>
                        <?php esc_html_e( 'Submit & Return', 'library-management-system' ); ?>
                    </button>
                </div>

            </div><!-- /#owt7_lms_qr_body -->

            <div id="owt7_lms_qr_error" class="owt7-lms-qr-error" style="display:none;" role="alert"></div>

        </div><!-- /.modal-content -->
    </div><!-- /#owt7_lms_mdl_quick_return -->

    <div id="owt7_lms_mdl_borrow_details" class="modal owt7-lms-view-return-modal" style="display: none;">
        <div class="modal-content owt7-lms-view-return-content">
            <button type="button" class="close owt7_lms_borrow_details_close" aria-label="<?php esc_attr_e( 'Close', 'library-management-system' ); ?>">&times;</button>
            <h2><?php esc_html_e( 'Borrow Details', 'library-management-system' ); ?></h2>
            <div class="owt7-lms-borrow-details-loader" id="owt7_lms_borrow_details_loader" aria-hidden="true">
                <span class="owt7-lms-css-loader owt7-lms-loader-lg" aria-hidden="true"></span>
                <span class="owt7-lms-borrow-details-loader-text"><?php esc_html_e( 'Loading…', 'library-management-system' ); ?></span>
            </div>
            <div class="owt7-lms-view-return-sections" id="owt7_lms_borrow_details_content">
                <div class="owt7-lms-view-section owt7-lms-view-section-book">
                    <h3 class="owt7-lms-view-section-title"><?php esc_html_e( 'Borrower Information', 'library-management-system' ); ?></h3>
                    <div class="owt7-lms-view-detail-body" id="owt7_lms_borrow_details_user"></div>
                </div>
                <div class="owt7-lms-view-section owt7-lms-view-section-remark">
                    <h3 class="owt7-lms-view-section-title"><?php esc_html_e( 'Book & Loan Information', 'library-management-system' ); ?></h3>
                    <div class="owt7-lms-view-detail-body" id="owt7_lms_borrow_details_book"></div>
                </div>
            </div>
        </div>
    </div>

</div>

<script type="text/javascript">
(function($) {
    'use strict';

    var QR = {
        $modal        : null,
        $loader       : null,
        $body         : null,
        $error        : null,
        $userInfo     : null,
        $bookInfo     : null,
        $loanInfo     : null,
        $condition    : null,
        $remark       : null,
        $fineDisplay  : null,
        $dbIdInput    : null,
        $returnIdInput: null,
        $submitBtn    : null,
        $lateNotice   : null,
        issuedOn      : '',
        borrowsDays   : 0,
        ajaxurl       : (typeof owt7_library !== 'undefined' ? owt7_library.ajaxurl : ''),
        nonce         : (typeof owt7_library !== 'undefined' ? owt7_library.ajax_nonce : ''),

        init: function() {
            this.$modal        = $('#owt7_lms_mdl_quick_return');
            this.$loader       = $('#owt7_lms_qr_loader');
            this.$body         = $('#owt7_lms_qr_body');
            this.$error        = $('#owt7_lms_qr_error');
            this.$userInfo     = $('#owt7_lms_qr_user_info');
            this.$bookInfo     = $('#owt7_lms_qr_book_info');
            this.$loanInfo     = $('#owt7_lms_qr_loan_info');
            this.$condition    = $('#owt7_lms_qr_condition');
            this.$remark       = $('#owt7_lms_qr_remark');
            this.$fineDisplay  = $('#owt7_lms_qr_fine_display');
            this.$dbIdInput    = $('#owt7_lms_qr_borrow_db_id');
            this.$returnIdInput= $('#owt7_lms_qr_return_id');
            this.$submitBtn    = $('#owt7_lms_qr_submit');
            this.$lateNotice   = $('#owt7_lms_qr_late_notice');
            this.bindEvents();
        },

        bindEvents: function() {
            var self = this;

            $(document).on('click', '.owt7_lms_quick_return_btn', function(e) {
                e.preventDefault();
                var borrowDbId = $(this).data('borrow-db-id');
                if (!borrowDbId) return;
                self.openModal(borrowDbId);
            });

            $(document).on('change', '#owt7_lms_qr_condition', function() {
                self.updateFinePreview();
                self.updateLateDaysNotice();
            });

            $(document).on('click', '#owt7_lms_qr_submit', function() {
                self.submitReturn();
            });

            $(document).on('click', '.owt7-lms-qr-close, #owt7_lms_mdl_quick_return', function(e) {
                if (e.target !== this &&
                    !$(e.target).hasClass('owt7-lms-qr-close') &&
                    !$(e.target).hasClass('owt7_return_status_modal_cancel')) {
                    return;
                }
                self.closeModal();
            });

            $(document).on('click', '#owt7_lms_mdl_quick_return .modal-content', function(e) {
                e.stopPropagation();
            });

            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' && self.$modal && self.$modal.is(':visible')) {
                    self.closeModal();
                }
            });
        },

        openModal: function(borrowDbId) {
            var self = this;

            this.$loader.show();
            this.$body.hide();
            this.$error.hide().text('');
            this.$condition.val('normal_return');
            this.$remark.val('');
            this.$fineDisplay.text('0 <?php echo esc_js( get_option( 'owt7_lms_currency', '' ) ); ?>');
            this.$dbIdInput.val('');
            this.$returnIdInput.val('');
            this.$submitBtn.prop('disabled', true);
            this.$lateNotice.hide().find('.owt7-lms-qr-late-text').text('');
            this.issuedOn    = '';
            this.borrowsDays = 0;

            this.$modal.show().attr('aria-hidden', 'false');
            $('body').addClass('owt7-lms-modal-open');

            var postdata = 'borrow_db_id=' + encodeURIComponent(borrowDbId) +
                           '&action=owt_lib_handler' +
                           '&param=owt7_lms_get_quick_return_modal_content' +
                           '&owt7_lms_nonce=' + encodeURIComponent(this.nonce);

            $.post(this.ajaxurl, postdata, function(response) {
                var data = (typeof response === 'object') ? response : (function() {
                    try { return JSON.parse(response); } catch(e) { return {}; }
                })();

                self.$loader.hide();

                if (data.sts === 1 && data.arr) {
                    self.$userInfo.html(data.arr.user_html || '');
                    self.$bookInfo.html(data.arr.book_html || '');
                    self.$loanInfo.html(data.arr.loan_html || '');
                    self.$dbIdInput.val(data.arr.borrow_db_id || '');
                    self.$returnIdInput.val(data.arr.next_return_id || '');
                    self.issuedOn    = data.arr.issued_on    || '';
                    self.borrowsDays = parseInt(data.arr.borrows_days) || 0;

                    var currency = data.arr.currency || '';
                    self.$fineDisplay.text('0 ' + currency);
                    self.$fineDisplay.data('currency', currency);

                    self.$body.show();
                    self.$submitBtn.prop('disabled', false);
                    self.updateFinePreview();
                    self.updateLateDaysNotice();
                } else {
                    var errMsg = (data && data.msg) ? data.msg :
                        '<?php echo esc_js( __( 'Unable to load details. Please try again.', 'library-management-system' ) ); ?>';
                    self.$error.text(errMsg).show();
                }
            }).fail(function() {
                self.$loader.hide();
                self.$error.text('<?php echo esc_js( __( 'Request failed. Please try again.', 'library-management-system' ) ); ?>').show();
            });
        },

        closeModal: function() {
            this.$modal.hide().attr('aria-hidden', 'true');
            $('body').removeClass('owt7-lms-modal-open');
        },

        updateFinePreview: function() {
            var self      = this;
            var dbId      = this.$dbIdInput.val();
            var condition = this.$condition.val() || 'normal_return';
            if (!dbId) return;

            var postdata = 'action=owt_lib_handler' +
                           '&param=owt7_lms_return_fine_preview' +
                           '&owt7_lms_nonce=' + encodeURIComponent(this.nonce) +
                           '&owt7_return_condition=' + encodeURIComponent(condition) +
                           '&owt7_borrow_books_id[]=' + encodeURIComponent(dbId);

            $.post(this.ajaxurl, postdata, function(response) {
                var data = (typeof response === 'object') ? response : (function() {
                    try { return JSON.parse(response); } catch(e) { return {}; }
                })();
                var currency = (data.arr && data.arr.currency) ? data.arr.currency :
                               (self.$fineDisplay.data('currency') || '');
                var total    = (data.arr && typeof data.arr.total_fine !== 'undefined') ?
                               parseFloat(data.arr.total_fine) : 0;
                self.$fineDisplay.text(total + ' ' + currency);
            }).fail(function() {
                self.$fineDisplay.text('—');
            });
        },

        updateLateDaysNotice: function() {
            var condition = this.$condition.val();
            if (condition !== 'late_return' || !this.issuedOn || !this.borrowsDays) {
                this.$lateNotice.hide();
                return;
            }

            var issued  = new Date(this.issuedOn);
            var today   = new Date();
            issued.setHours(0, 0, 0, 0);
            today.setHours(0, 0, 0, 0);
            var elapsed   = Math.floor((today - issued) / 86400000);
            var extraDays = elapsed - this.borrowsDays;

            var dueDate = new Date(issued);
            dueDate.setDate(dueDate.getDate() + this.borrowsDays);
            var dueDateStr = dueDate.getFullYear() + '-' +
                String(dueDate.getMonth() + 1).padStart(2, '0') + '-' +
                String(dueDate.getDate()).padStart(2, '0');

            var $notice = this.$lateNotice;
            var $text   = $notice.find('.owt7-lms-qr-late-text');

            var todayStr = today.getFullYear() + '-' +
                String(today.getMonth() + 1).padStart(2, '0') + '-' +
                String(today.getDate()).padStart(2, '0');

            if (extraDays > 0) {
                $notice.removeClass('owt7-lms-qr-late-notice--zero').addClass('owt7-lms-qr-late-notice--overdue');
                $text.text(
                    extraDays + ' <?php echo esc_js( __( 'day(s) overdue', 'library-management-system' ) ); ?>' +
                    ' \u2014 <?php echo esc_js( __( 'due', 'library-management-system' ) ); ?>: ' + dueDateStr +
                    ', <?php echo esc_js( __( 'returned', 'library-management-system' ) ); ?>: ' + todayStr
                );
            } else {
                $notice.removeClass('owt7-lms-qr-late-notice--overdue').addClass('owt7-lms-qr-late-notice--zero');
                $text.text('<?php echo esc_js( __( 'Book is not overdue yet — no late days to charge.', 'library-management-system' ) ); ?>');
            }
            $notice.show();
        },

        submitReturn: function() {
            var self  = this;
            var $btn  = this.$submitBtn;
            var dbId  = this.$dbIdInput.val();
            if (!dbId) {
                if (typeof owt7_lms_toastr === 'function') {
                    owt7_lms_toastr('<?php echo esc_js( __( 'No borrow record selected.', 'library-management-system' ) ); ?>', 'error');
                }
                return;
            }

            var condition = this.$condition.val() || 'normal_return';
            var remark    = this.$remark.val() || '';
            var returnId  = this.$returnIdInput.val() || '';
            var nonce     = $('#owt7_lms_qr_hidden_form input[name="owt7_lms_nonce"]').val() || this.nonce;

            var postdata = 'action=owt_lib_handler' +
                           '&param=owt7_lms_return_book' +
                           '&owt7_lms_nonce=' + encodeURIComponent(nonce) +
                           '&owt7_borrow_books_id[]=' + encodeURIComponent(dbId) +
                           '&owt7_return_id=' + encodeURIComponent(returnId) +
                           '&owt7_return_condition=' + encodeURIComponent(condition) +
                           '&owt7_return_remark=' + encodeURIComponent(remark);

            $btn.prop('disabled', true);

            $.post(this.ajaxurl, postdata, function(response) {
                var data = (typeof response === 'object') ? response : (function() {
                    try { return JSON.parse(response); } catch(e) { return {}; }
                })();
                $btn.prop('disabled', false);

                if (data.sts == 1) {
                    if (typeof owt7_lms_toastr === 'function') {
                        owt7_lms_toastr(data.msg || '<?php echo esc_js( __( 'Book returned successfully.', 'library-management-system' ) ); ?>', 'success');
                    }
                    self.closeModal();
                    if (typeof $.fn.DataTable === 'function' && $.fn.DataTable.isDataTable('#tbl_books_borrow_history')) {
                        $('#tbl_books_borrow_history').DataTable().draw(false);
                    }
                } else {
                    if (typeof owt7_lms_toastr === 'function') {
                        owt7_lms_toastr(data.msg || '<?php echo esc_js( __( 'Return failed. Please try again.', 'library-management-system' ) ); ?>', 'error');
                    }
                }
            }).fail(function() {
                $btn.prop('disabled', false);
                if (typeof owt7_lms_toastr === 'function') {
                    owt7_lms_toastr('<?php echo esc_js( __( 'Request failed. Please try again.', 'library-management-system' ) ); ?>', 'error');
                }
            });
        }
    };

    $(document).ready(function() {
        QR.init();
    });

})(jQuery);
</script>
