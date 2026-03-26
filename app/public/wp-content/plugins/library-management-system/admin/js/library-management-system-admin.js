jQuery(function() {

    var owt7_lms_borrow_dropped_file = null;
    var owt7_lms_return_dropped_file = null;

    if (owt7_library.active) {
        var pluginAjaxCount = 0;
        function isPluginAjaxRequest(data) {
            if (data == null) return false;
            if (typeof data === 'string') return data.indexOf('owt_lib_handler') !== -1;
            if (jQuery.isPlainObject(data)) return data.action === 'owt_lib_handler';
            if (typeof FormData !== 'undefined' && data instanceof FormData) {
                return data.get('action') === 'owt_lib_handler';
            }
            return false;
        }
        function shouldUseGlobalPluginLoader(data) {
            if (data == null) return false;
            if (typeof data === 'string') {
                return data.indexOf('param=owt7_lms_filter_library_user_catalogue') === -1;
            }
            if (jQuery.isPlainObject(data)) {
                return data.param !== 'owt7_lms_filter_library_user_catalogue';
            }
            if (typeof FormData !== 'undefined' && data instanceof FormData) {
                var param = data.get('param');
                return param !== 'owt7_lms_filter_library_user_catalogue';
            }
            return true;
        }
        jQuery(document).ajaxSend(function(event, jqXHR, ajaxOptions) {
            if (isPluginAjaxRequest(ajaxOptions.data) && shouldUseGlobalPluginLoader(ajaxOptions.data)) {
                pluginAjaxCount++;
                jQuery('.owt7-lms').addClass('owt7_loader');
                jQuery('body').addClass('owt7_loader');
            }
        });
        jQuery(document).ajaxComplete(function(event, jqXHR, ajaxOptions) {
            if (isPluginAjaxRequest(ajaxOptions.data) && shouldUseGlobalPluginLoader(ajaxOptions.data)) {
                pluginAjaxCount--;
                if (pluginAjaxCount <= 0) {
                    pluginAjaxCount = 0;
                    jQuery('.owt7-lms').removeClass('owt7_loader');
                    jQuery('body').removeClass('owt7_loader');
                }
            }
        });
    }

    var settingsSubmitHandler = function(form) {
        var formEl = jQuery(form);
        var formID = formEl.attr('id');
        var paramID = formID;
        if (paramID === 'owt7_lms_days_form' || paramID === 'owt7_lms_country_form' || paramID === 'owt7_lms_late_fine_form') {
            paramID = 'owt7_lms_data_settings';
        }
        var $btn = formEl.find("button[type='submit']");
        $btn.prop("disabled", true).text('Processing...').css("cursor", "progress");
        var formdata = formEl.serialize();
        var postdata = formdata + "&action=owt_lib_handler&param=" + paramID;
        function resetVerificationButton() {
            $btn.prop("disabled", false).css("cursor", "pointer");
            $btn.text(owt7_library.messages.message_2 || 'Save');
        }
        if (jQuery("#owt7_lms_upload_form").length > 0) {} else {
            jQuery.post(owt7_library.ajaxurl, postdata)
                .done(function(response) {
                    var data = typeof response === "string" ? (function() { try { return JSON.parse(response); } catch (e) { return {}; } })() : (response || {});
                    if (data.sts == 1) {
                        formEl.find("button[type='submit']").text(owt7_library.messages.message_1 + '...').css("cursor", "progress");
                        owt7_lms_toastr(data.msg, "success");
                        if (formEl.closest('#owt7_lms_mdl_late_fine').length) {
                            jQuery("#owt7_lms_mdl_late_fine").hide();
                            jQuery("body").removeClass("owt7-lms-modal-open");
                        }
                        if (formEl.closest('#owt7_lms_mdl_lms_frontend_settings').length) {
                            jQuery("#owt7_lms_mdl_lms_frontend_settings").hide();
                            jQuery("body").removeClass("owt7-lms-modal-open");
                            formEl.find("button[type='submit']").prop("disabled", false).text(owt7_library.messages.message_2 || 'Save settings').css("cursor", "pointer");
                        }
                        if (formID === 'owt7_lms_days_form') {
                            formEl.find("button[type='submit']").prop("disabled", false).text(formEl.find("button[type='submit']").data("original-text") || "Add & save").css("cursor", "pointer");
                            formEl.find("#owt7_lms_days").val("");
                            if (typeof owt7_lms_refresh_days_modal_table === "function") {
                                owt7_lms_refresh_days_modal_table();
                            }
                        }
                    } else {
                        resetVerificationButton();
                        owt7_lms_toastr(data.msg, 'error');
                    }
                })
                .fail(function(jqXHR, textStatus, errorThrown) {
                    resetVerificationButton();
                    var errMsg = 'Request failed. Please try again.';
                    if (jqXHR.responseJSON && jqXHR.responseJSON.msg) {
                        errMsg = jqXHR.responseJSON.msg;
                    } else if (jqXHR.responseText) {
                        try {
                            var body = typeof jqXHR.responseText === 'string' ? JSON.parse(jqXHR.responseText) : {};
                            if (body.msg) errMsg = body.msg;
                        } catch (e) {}
                    }
                    owt7_lms_toastr(errMsg, 'error');
                });
        }
    };
    jQuery(`
		#owt7_lms_branch_form, 
		#owt7_lms_user_form,
        #owt7_lms_category_form,
        #owt7_lms_bookcase_form,
        #owt7_lms_section_form,
        #owt7_lms_category_form,
        #owt7_lms_book_form,
        #owt7_lms_borrow_book,
        #owt7_lms_country_form,
        #owt7_lms_late_fine_form,
        #owt7_lms_days_form,
        #owt7_lms_data_settings,
        #owt7_lms_upload_form,
        #owt7_lms_settings_form
	`).each(function() {
        jQuery(this).validate({ submitHandler: settingsSubmitHandler });
    });
    /* Return book form: no generic submit; uses Return Status modal then AJAX (see owt7_return_open_modal_btn) */
    jQuery("#owt7_lms_return_book").validate({ rules: {}, submitHandler: function() { return false; } });

    /**
     * Library User: Return button — open Return Status modal, then submit via front handler
     */
    function owt7_lms_user_return_modal_el() {
        return jQuery("#owt7_lms_mdl_user_return_status");
    }

    function owt7_lms_user_borrowed_details_modal_el() {
        return jQuery("#owt7_lms_mdl_user_borrowed_details");
    }

    function owt7_lms_user_return_validation_el() {
        return jQuery("#owt7_user_return_validation");
    }

    function owt7_lms_escape_html(value) {
        return jQuery("<div>").text(value || "—").html();
    }

    function owt7_lms_is_overdue_return(expectedReturnRaw) {
        if (!expectedReturnRaw) {
            return false;
        }
        var expected = new Date(expectedReturnRaw + "T00:00:00");
        if (Number.isNaN(expected.getTime())) {
            return false;
        }
        var today = new Date();
        today.setHours(0, 0, 0, 0);
        return today.getTime() > expected.getTime();
    }

    function owt7_lms_show_user_return_validation(message) {
        owt7_lms_user_return_validation_el().text(message).show();
    }

    function owt7_lms_toggle_global_loader(showLoader) {
        jQuery("body")[showLoader ? "addClass" : "removeClass"]("owt7_loader");
    }

    function owt7_lms_hide_user_return_validation() {
        owt7_lms_user_return_validation_el().hide().text("");
    }

    function owt7_lms_validate_user_return_condition() {
        var $modal = owt7_lms_user_return_modal_el();
        var condition = jQuery("#owt7_user_return_condition").val() || "normal_return";
        var expectedReturnRaw = $modal.data("expected-return-raw") || "";
        var expectedReturnLabel = $modal.data("expected-return-label") || expectedReturnRaw || owt7_library.messages.message_10 || "the expected return date";
        if (condition === "normal_return" && owt7_lms_is_overdue_return(expectedReturnRaw)) {
            owt7_lms_show_user_return_validation("This book is past the expected return date (" + expectedReturnLabel + "). Please select Late return instead of Normal return.");
            return false;
        }
        owt7_lms_hide_user_return_validation();
        return true;
    }

    function owt7_lms_close_user_return_status_modal() {
        owt7_lms_user_return_modal_el()
            .hide()
            .attr("aria-hidden", "true")
            .removeData("user-return-book-id")
            .removeData("expected-return-raw")
            .removeData("expected-return-label");
        var currency = owt7_lms_user_return_modal_el().attr("data-currency") || "";
        jQuery("#owt7_user_return_condition").prop("selectedIndex", 0);
        jQuery("#owt7_user_return_remark").val("");
        jQuery("#owt7_user_return_total_fine_display").text(("0 " + currency).trim());
        owt7_lms_hide_user_return_validation();
        owt7_lms_toggle_global_loader(false);
        jQuery("body").removeClass("owt7-lms-modal-open");
    }

    function owt7_lms_close_user_borrowed_details_modal() {
        owt7_lms_user_borrowed_details_modal_el().hide().attr("aria-hidden", "true");
        owt7_lms_toggle_global_loader(false);
        jQuery("body").removeClass("owt7-lms-modal-open");
    }

    jQuery(document).on("click", ".owt7_lms_user_do_return", function(e) {
        e.preventDefault();
        var btn = jQuery(this);
        if (btn.prop("disabled")) return;
        var bookId = btn.data("id");
        if (!bookId) return;
        owt7_lms_toggle_global_loader(true);
        owt7_lms_user_return_modal_el()
            .data("user-return-book-id", bookId)
            .data("expected-return-raw", btn.attr("data-expected-return-raw") || "")
            .data("expected-return-label", btn.attr("data-expected-return-label") || "");
        jQuery("#owt7_user_return_condition").prop("selectedIndex", 0);
        jQuery("#owt7_user_return_remark").val("");
        owt7_lms_hide_user_return_validation();
        window.setTimeout(function() {
            owt7_lms_user_return_modal_el().show().attr("aria-hidden", "false");
            jQuery("body").addClass("owt7-lms-modal-open");
            owt7_lms_user_return_update_fine_display();
        }, 250);
    });

    function owt7_lms_user_return_update_fine_display() {
        var bookId = owt7_lms_user_return_modal_el().data("user-return-book-id");
        if (!bookId) return;
        var condition = jQuery("#owt7_user_return_condition").val() || "normal_return";
        var postdata = "action=owt7_front_handler&param=owt7_lms_user_return_fine_preview&owt7_lms_nonce=" + owt7_library.ajax_nonce + "&book_id=" + encodeURIComponent(bookId) + "&owt7_return_condition=" + encodeURIComponent(condition);
        jQuery.post(owt7_library.ajaxurl, postdata, function(response) {
            var data = typeof response === "object" ? response : (function() { try { return JSON.parse(response); } catch (e) { return {}; } })();
            var currency = (data.arr && data.arr.currency) ? data.arr.currency : "";
            var total = (data.arr && typeof data.arr.total_fine !== "undefined") ? parseFloat(data.arr.total_fine) : 0;
            jQuery("#owt7_user_return_total_fine_display").text(total + " " + currency);
            owt7_lms_toggle_global_loader(false);
        }).fail(function() {
            jQuery("#owt7_user_return_total_fine_display").text("—");
            owt7_lms_toggle_global_loader(false);
        });
    }

    jQuery(document).on("change", "#owt7_user_return_condition", function() {
        owt7_lms_validate_user_return_condition();
        owt7_lms_user_return_update_fine_display();
    });

    jQuery(document).on("click", "#owt7_user_return_status_modal_submit", function() {
        var $btn = jQuery(this);
        var bookId = owt7_lms_user_return_modal_el().data("user-return-book-id");
        if (!bookId) return;
        if (!owt7_lms_validate_user_return_condition()) return;
        var condition = jQuery("#owt7_user_return_condition").val() || "normal_return";
        var remark = jQuery("#owt7_user_return_remark").val() || "";
        var postdata = "action=owt7_front_handler&param=owt7_lms_do_user_return&owt7_lms_nonce=" + owt7_library.ajax_nonce + "&book_id=" + encodeURIComponent(bookId) + "&owt7_return_condition=" + encodeURIComponent(condition) + "&owt7_return_remark=" + encodeURIComponent(remark);
        $btn.prop("disabled", true);
        owt7_lms_toggle_global_loader(true);
        jQuery.post(owt7_library.ajaxurl, postdata, function(response) {
            var data = typeof response === "string" ? (function() { try { return JSON.parse(response); } catch (e) { return {}; } })() : (response || {});
            $btn.prop("disabled", false);
            if (data.sts == 1) {
                owt7_lms_close_user_return_status_modal();
                owt7_lms_toastr(data.msg, "success");
                if (typeof window.location !== "undefined") {
                    window.setTimeout(function() {
                        window.location.reload();
                    }, 600);
                }
            } else {
                owt7_lms_toggle_global_loader(false);
                if (data.msg) {
                    owt7_lms_show_user_return_validation(data.msg);
                }
                owt7_lms_toastr(data.msg || "Return failed.", "error");
            }
        }).fail(function() {
            $btn.prop("disabled", false);
            owt7_lms_toggle_global_loader(false);
            owt7_lms_toastr("Request failed. Please try again.", "error");
        });
    });

    jQuery(document).on("click", "#owt7_lms_mdl_user_return_status .close, .owt7_lms_user_return_status_modal_cancel", function() {
        owt7_lms_close_user_return_status_modal();
    });

    jQuery(document).on("click", ".owt7_lms_user_view_borrowed_details", function(e) {
        e.preventDefault();
        var btn = jQuery(this);
        var $modal = owt7_lms_user_borrowed_details_modal_el();
        if (!$modal.length) return;
        owt7_lms_toggle_global_loader(true);
        var modalTitle = btn.attr("data-modal-title") || "Borrowed Book Details";
        var timelineTitle = btn.attr("data-timeline-title") || "Borrowing Information";
        var bookDetails = '' +
            '<div class="lms-info-block">' +
                '<div class="lms-info-item"><span class="lms-info-label">Book Name</span><span class="lms-info-value">' + owt7_lms_escape_html(btn.attr("data-book-name")) + '</span></div>' +
                '<div class="lms-info-item"><span class="lms-info-label">Book ID</span><span class="lms-info-value">' + owt7_lms_escape_html(btn.attr("data-book-code")) + '</span></div>' +
                '<div class="lms-info-item"><span class="lms-info-label">Category</span><span class="lms-info-value">' + owt7_lms_escape_html(btn.attr("data-book-category")) + '</span></div>' +
                '<div class="lms-info-item"><span class="lms-info-label">Author</span><span class="lms-info-value">' + owt7_lms_escape_html(btn.attr("data-book-author")) + '</span></div>' +
                '<div class="lms-info-item"><span class="lms-info-label">Publication</span><span class="lms-info-value">' + owt7_lms_escape_html(btn.attr("data-book-publication")) + '</span></div>' +
                '<div class="lms-info-item"><span class="lms-info-label">ISBN</span><span class="lms-info-value">' + owt7_lms_escape_html(btn.attr("data-book-isbn")) + '</span></div>' +
                '<div class="lms-info-item"><span class="lms-info-label">Cost</span><span class="lms-info-value">' + owt7_lms_escape_html(btn.attr("data-book-cost")) + '</span></div>' +
                '<div class="lms-info-item"><span class="lms-info-label">Accession No.</span><span class="lms-info-value">' + owt7_lms_escape_html(btn.attr("data-accession-number")) + '</span></div>' +
            '</div>';
        var borrowingDetails = '' +
            '<div class="lms-info-block">' +
                '<div class="lms-info-item"><span class="lms-info-label">Borrow ID</span><span class="lms-info-value">' + owt7_lms_escape_html(btn.attr("data-borrow-record-id")) + '</span></div>' +
                (btn.attr("data-return-record-id") ? '<div class="lms-info-item"><span class="lms-info-label">Return ID</span><span class="lms-info-value">' + owt7_lms_escape_html(btn.attr("data-return-record-id")) + '</span></div>' : '') +
                '<div class="lms-info-item"><span class="lms-info-label">Borrow Date</span><span class="lms-info-value">' + owt7_lms_escape_html(btn.attr("data-issue-date")) + '</span></div>' +
                '<div class="lms-info-item"><span class="lms-info-label">' + owt7_lms_escape_html(btn.attr("data-return-record-id") ? "Return Date" : "Expected Return") + '</span><span class="lms-info-value">' + owt7_lms_escape_html(btn.attr("data-expected-return-date")) + '</span></div>' +
                (btn.attr("data-fine-display") ? '<div class="lms-info-item"><span class="lms-info-label">Fine</span><span class="lms-info-value">' + owt7_lms_escape_html(btn.attr("data-fine-display")) + '</span></div>' : '') +
            '</div>';
        jQuery("#owt7_lms_user_borrowed_details_title").text(modalTitle);
        jQuery("#owt7_lms_user_borrowed_timeline_title").text(timelineTitle);
        jQuery("#owt7_lms_user_borrowed_book_details").html(bookDetails);
        jQuery("#owt7_lms_user_borrowed_timeline_details").html(borrowingDetails);
        // A receipt can be generated for any valid completed return, even when the fine is zero.
        var returnDbId = btn.attr("data-return-db-id") || "";
        var $modalRcptBtn = jQuery("#owt7_lms_user_modal_download_receipt_btn");
        $modalRcptBtn.hide();
        if (returnDbId && returnDbId !== "0") {
            $modalRcptBtn.attr("data-return-db-id", returnDbId).show();
        }
        window.setTimeout(function() {
            $modal.show().attr("aria-hidden", "false");
            jQuery("body").addClass("owt7-lms-modal-open");
            owt7_lms_toggle_global_loader(false);
        }, 250);
    });

    jQuery(document).on("click", "#owt7_lms_mdl_user_borrowed_details .close", function() {
        owt7_lms_close_user_borrowed_details_modal();
    });

    jQuery(document).on("click", "#owt7_lms_mdl_user_return_status, #owt7_lms_mdl_user_borrowed_details", function(e) {
        if (e.target === this) {
            if (this.id === "owt7_lms_mdl_user_return_status") {
                owt7_lms_close_user_return_status_modal();
            } else {
                owt7_lms_close_user_borrowed_details_modal();
            }
        }
    });

    jQuery(document).on("keydown", function(e) {
        if (e.key !== "Escape") return;
        if (owt7_lms_user_return_modal_el().is(":visible")) {
            owt7_lms_close_user_return_status_modal();
        }
        if (owt7_lms_user_borrowed_details_modal_el().is(":visible")) {
            owt7_lms_close_user_borrowed_details_modal();
        }
    });

    function owt7_lms_close_user_checkout_overview_modal() {
        var $modal = jQuery("#owt7_lms_mdl_user_checkout_overview");
        $modal.hide().attr("aria-hidden", "true").removeData("checkoutButton");
        jQuery("#owt7_lms_user_checkout_overview_confirm").prop("disabled", false);
        jQuery("body").removeClass("owt7-lms-modal-open");
    }

    function owt7_lms_open_user_checkout_overview_modal(btn) {
        var modalOpenDelay = 700;
        var $btn = jQuery(btn);
        var $card = $btn.closest(".owt7-lms-book-card");
        var title = $btn.attr("data-book-title") || $btn.data("bookTitle") || $card.attr("data-book-title") || "—";
        var author = $btn.attr("data-book-author") || $btn.data("bookAuthor") || $card.attr("data-book-author") || "—";
        var category = $btn.attr("data-book-category") || $btn.data("bookCategory") || $card.attr("data-book-category") || "—";
        var publication = $btn.attr("data-book-publication") || $btn.data("bookPublication") || $card.attr("data-book-publication") || "—";
        var year = $btn.attr("data-book-year") || $btn.data("bookYear") || $card.attr("data-book-year") || "—";
        var isbn = $btn.attr("data-book-isbn") || $btn.data("bookIsbn") || $card.attr("data-book-isbn") || "—";
        var amount = $btn.attr("data-book-amount") || $btn.data("bookAmount") || $card.attr("data-book-amount") || "—";
        var days = $btn.attr("data-checkout-days") || $btn.data("checkoutDays") || "—";
        var requestDate = $btn.attr("data-request-date") || $btn.data("requestDate") || "—";
        var expectedDate = $btn.attr("data-expected-return-date") || $btn.data("expectedReturnDate") || "—";
        var $modal = jQuery("#owt7_lms_mdl_user_checkout_overview");
        jQuery("body").addClass("owt7_loader");
        if (!$modal.length) {
            jQuery("body").removeClass("owt7_loader");
            return;
        }
        var currencySymbol = $modal.attr("data-currency-symbol") || "";
        var amountText = amount;
        if (amount && amount !== "—" && currencySymbol) {
            amountText = currencySymbol + " " + amount;
        }
        jQuery("#owt7_lms_checkout_overview_book_title").text(title);
        jQuery("#owt7_lms_checkout_overview_book_author").text(author);
        jQuery("#owt7_lms_checkout_overview_book_category").text(category);
        jQuery("#owt7_lms_checkout_overview_book_publication").text(publication);
        jQuery("#owt7_lms_checkout_overview_book_year").text(year);
        jQuery("#owt7_lms_checkout_overview_book_isbn").text(isbn);
        jQuery("#owt7_lms_checkout_overview_book_amount").text(amountText);
        jQuery("#owt7_lms_checkout_overview_days").text(days);
        jQuery("#owt7_lms_checkout_overview_request_date").text(requestDate);
        jQuery("#owt7_lms_checkout_overview_expected_date").text(expectedDate);
        window.setTimeout(function() {
            $modal.data("checkoutButton", $btn).show().attr("aria-hidden", "false");
            jQuery("body").addClass("owt7-lms-modal-open");
            jQuery("body").removeClass("owt7_loader");
        }, modalOpenDelay);
    }

    /**
     * Library User catalogue: Checkout button opens overview modal first.
     */
    jQuery(document).on("click", ".owt7_lms_user_do_checkout", function(e) {
        e.preventDefault();
        var btn = jQuery(this);
        if (btn.prop("disabled")) return;
        owt7_lms_open_user_checkout_overview_modal(btn);
    });

    jQuery(document).on("click", ".owt7-lms-close-user-checkout-overview, .owt7-lms-cancel-user-checkout-overview", function() {
        owt7_lms_close_user_checkout_overview_modal();
    });

    jQuery(document).on("click", "#owt7_lms_mdl_user_checkout_overview", function(e) {
        if (e.target === this) {
            owt7_lms_close_user_checkout_overview_modal();
        }
    });

    jQuery(document).on("click", "#owt7_lms_user_checkout_overview_confirm", function() {
        var $confirmBtn = jQuery(this);
        var $modal = jQuery("#owt7_lms_mdl_user_checkout_overview");
        var btn = $modal.data("checkoutButton");
        var $sourceBtn = btn ? jQuery(btn) : jQuery();
        var bookId = $sourceBtn.attr("data-id") || $sourceBtn.data("id");
        if (!bookId) {
            owt7_lms_close_user_checkout_overview_modal();
            return;
        }
        $confirmBtn.prop("disabled", true);
        $sourceBtn.prop("disabled", true);
        jQuery("body").addClass("owt7_loader");
        var postdata = "book_id=" + encodeURIComponent(bookId) + "&action=owt7_front_handler&param=owt7_lms_do_user_checkout&owt7_lms_nonce=" + owt7_library.ajax_nonce;
        jQuery.post(owt7_library.ajaxurl, postdata, function(response) {
            var data = typeof response === "string" ? (function() { try { return JSON.parse(response); } catch (e) { return {}; } })() : (response || {});
            jQuery("body").removeClass("owt7_loader");
            if (data.sts == 1) {
                owt7_lms_close_user_checkout_overview_modal();
                owt7_lms_toastr(data.msg, "success");
            } else {
                $confirmBtn.prop("disabled", false);
                $sourceBtn.prop("disabled", false);
                owt7_lms_toastr(data.msg || "Checkout failed.", "error");
            }
        }).fail(function() {
            jQuery("body").removeClass("owt7_loader");
            $confirmBtn.prop("disabled", false);
            $sourceBtn.prop("disabled", false);
            owt7_lms_toastr("Request failed. Please try again.", "error");
        });
    });

    /**
     * Library User catalogue: View book detail modal (show body loader while opening)
     * Animated open/close; layout: cover left, details right; Checkout/Return in modal
     */
    function owt7_lms_close_book_detail_modal() {
        var $modal = jQuery("#owt7_lms_mdl_book_detail");
        $modal.removeClass("owt7-lms-modal-visible");
        window.setTimeout(function() {
            $modal.addClass("owt7-lms-modal-closed").attr("aria-hidden", "true");
            jQuery("body").removeClass("owt7-lms-modal-open");
        }, 250);
    }
    jQuery(document).on("click", ".owt7-lms-btn-view-book", function(e) {
        e.preventDefault();
        var modalOpenDelay = 700;
        var btn = jQuery(this);
        var card = btn.closest(".owt7-lms-book-card");
        if (!card.length) return;
        jQuery("body").addClass("owt7_loader");
        var title = card.attr("data-book-title") || "";
        var author = card.attr("data-book-author") || "—";
        var category = card.attr("data-book-category") || "—";
        var publication = card.attr("data-book-publication") || "—";
        var year = card.attr("data-book-year") || "—";
        var description = card.attr("data-book-description") || "—";
        var cover = card.attr("data-book-cover") || "";
        var isbn = card.attr("data-book-isbn") || "—";
        var amount = card.attr("data-book-amount") || "—";
        var borrowed = card.attr("data-book-borrowed") || "0";
        var available = card.attr("data-book-available") || "0";
        var actionState = card.attr("data-book-action-state") || "";
        var idB64 = card.attr("data-book-id-b64") || "";
        var $modal = jQuery("#owt7_lms_mdl_book_detail");
        var $coverImg = jQuery("#owt7_lms_mdl_book_cover");
        var $coverPlace = jQuery("#owt7_lms_mdl_book_cover_placeholder");
        var $actions = jQuery("#owt7_lms_mdl_book_actions");
        jQuery("#owt7_lms_mdl_book_title").text(title);
        jQuery("#owt7_lms_mdl_book_author").text(author);
        jQuery("#owt7_lms_mdl_book_category").text(category);
        jQuery("#owt7_lms_mdl_book_publication").text(publication);
        jQuery("#owt7_lms_mdl_book_year").text(year);
        jQuery("#owt7_lms_mdl_book_isbn").text(isbn);
        jQuery("#owt7_lms_mdl_book_amount").text(amount);
        jQuery("#owt7_lms_mdl_book_description").text(description || "—");
        if (cover) {
            $coverImg.attr("src", cover).attr("alt", title).show();
            $coverPlace.hide();
        } else {
            $coverImg.attr("src", "").hide();
            $coverPlace.show();
        }
        $actions.empty();
        if (actionState === "borrowed" || borrowed === "1") {
            $actions.append('<span class="button button-small owt7-lms-btn-pending" disabled>Borrowed</span>');
        } else if (actionState === "available" || available === "1") {
            $actions.append('<span class="button button-small owt7-lms-btn-pending" disabled>Contact admin to borrow</span>');
        } else {
            $actions.append('<span class="button button-small owt7-lms-btn-unavailable" disabled>Out of stock</span>');
        }
        window.setTimeout(function() {
            $modal.removeClass("owt7-lms-modal-closed").attr("aria-hidden", "false");
            jQuery("body").addClass("owt7-lms-modal-open");
            jQuery("body").removeClass("owt7_loader");
            requestAnimationFrame(function() {
                $modal.addClass("owt7-lms-modal-visible");
            });
        }, modalOpenDelay);
    });
    jQuery(document).on("click", ".owt7-lms-close-book-detail-modal", function() {
        owt7_lms_close_book_detail_modal();
    });
    jQuery(document).on("click", "#owt7_lms_mdl_book_detail.modal", function(e) {
        if (e.target === this) {
            owt7_lms_close_book_detail_modal();
        }
    });
    jQuery(document).on("click", "#owt7_lms_mdl_book_detail .modal-content", function(e) {
        e.stopPropagation();
    });

    function owt7_lms_close_borrow_details_modal() {
        jQuery("#owt7_lms_mdl_borrow_details").hide().attr("aria-hidden", "true");
        jQuery("#owt7_lms_borrow_details_loader").addClass("owt7-lms-borrow-details-loader-hidden").attr("aria-hidden", "true");
        jQuery("#owt7_lms_borrow_details_content").removeClass("owt7-lms-borrow-details-content-loading");
        jQuery("body").removeClass("owt7-lms-modal-open");
    }

    jQuery(document).on("click", ".owt7_lms_view_borrow_details", function(e) {
        e.preventDefault();
        var btn = jQuery(this);
        var $modal = jQuery("#owt7_lms_mdl_borrow_details");
        var $loader = jQuery("#owt7_lms_borrow_details_loader");
        var $content = jQuery("#owt7_lms_borrow_details_content");

        $modal.show().attr("aria-hidden", "false");
        jQuery("body").addClass("owt7-lms-modal-open");
        $content.addClass("owt7-lms-borrow-details-content-loading");
        $loader.removeClass("owt7-lms-borrow-details-loader-hidden").attr("aria-hidden", "false");

        setTimeout(function() {
            var userHtml = '' +
                '<div class="lms-info-block">' +
                    '<div class="lms-info-item"><span class="lms-info-label">Borrow ID</span><span class="lms-info-value">' + owt7_lms_escape_html(btn.attr("data-borrow-id")) + '</span></div>' +
                    '<div class="lms-info-item"><span class="lms-info-label">User ID</span><span class="lms-info-value">' + owt7_lms_escape_html(btn.attr("data-user-id")) + '</span></div>' +
                    '<div class="lms-info-item"><span class="lms-info-label">Name</span><span class="lms-info-value">' + owt7_lms_escape_html(btn.attr("data-user-name")) + '</span></div>' +
                    '<div class="lms-info-item"><span class="lms-info-label">Branch</span><span class="lms-info-value">' + owt7_lms_escape_html(btn.attr("data-branch")) + '</span></div>' +
                '</div>';
            var bookHtml = '' +
                '<div class="lms-info-block">' +
                    '<div class="lms-info-item"><span class="lms-info-label">Book ID</span><span class="lms-info-value">' + owt7_lms_escape_html(btn.attr("data-book-id")) + '</span></div>' +
                    '<div class="lms-info-item"><span class="lms-info-label">Book</span><span class="lms-info-value">' + owt7_lms_escape_html(btn.attr("data-book-name")) + '</span></div>' +
                    '<div class="lms-info-item"><span class="lms-info-label">Category</span><span class="lms-info-value">' + owt7_lms_escape_html(btn.attr("data-category")) + '</span></div>' +
                    '<div class="lms-info-item"><span class="lms-info-label">Accession</span><span class="lms-info-value">' + owt7_lms_escape_html(btn.attr("data-accession")) + '</span></div>' +
                    '<div class="lms-info-item"><span class="lms-info-label">Borrow days</span><span class="lms-info-value">' + owt7_lms_escape_html(btn.attr("data-days")) + '</span></div>' +
                    '<div class="lms-info-item"><span class="lms-info-label">Issued on</span><span class="lms-info-value">' + owt7_lms_escape_html(btn.attr("data-issued-on")) + '</span></div>' +
                    '<div class="lms-info-item"><span class="lms-info-label">Return by</span><span class="lms-info-value">' + owt7_lms_escape_html(btn.attr("data-return-by")) + '</span></div>' +
                '</div>';
            jQuery("#owt7_lms_borrow_details_user").html(userHtml);
            jQuery("#owt7_lms_borrow_details_book").html(bookHtml);
            $loader.addClass("owt7-lms-borrow-details-loader-hidden").attr("aria-hidden", "true");
            $content.removeClass("owt7-lms-borrow-details-content-loading");
        }, 180);
    });

    jQuery(document).on("click", ".owt7_lms_borrow_details_close, #owt7_lms_mdl_borrow_details", function(e) {
        if (e.target !== this && !jQuery(e.target).hasClass("owt7_lms_borrow_details_close")) {
            return;
        }
        owt7_lms_close_borrow_details_modal();
    });

    /**
     * Library User catalogue: AJAX filters and pagination
     */
    function owt7_lms_library_user_catalogue_ajax(queryString, targetUrl) {
        var $loader = jQuery("#owt7_lms_catalogue_loader");
        var $results = jQuery("#owt7_lms_catalogue_results");
        if (!$results.length) return;

        $loader.addClass("is-loading").attr("aria-hidden", "false");
        var requestData = queryString + (queryString ? "&" : "") + "action=owt_lib_handler&param=owt7_lms_filter_library_user_catalogue&owt7_lms_nonce=" + encodeURIComponent(owt7_library.ajax_nonce);

        jQuery.post(owt7_library.ajaxurl, requestData, function(response) {
            var data = typeof response === "object" ? response : (function() { try { return JSON.parse(response); } catch (e) { return {}; } })();
            if (data.sts === 1 && data.arr && data.arr.html) {
                $results.html(data.arr.html);
                if (window.history && window.history.replaceState) {
                    window.history.replaceState({}, "", (targetUrl || data.arr.url || window.location.href));
                }
            } else {
                owt7_lms_toastr((data && data.msg) ? data.msg : "Could not update catalogue.", "error");
            }
        }).fail(function() {
            owt7_lms_toastr("Request failed. Please try again.", "error");
        }).always(function() {
            $loader.removeClass("is-loading").attr("aria-hidden", "true");
        });
    }

    jQuery(document).on("submit", "#owt7_lms_catalogue_filters_form", function(e) {
        e.preventDefault();
        var queryString = jQuery(this).serialize();
        var targetUrl = window.location.pathname + "?" + queryString;
        owt7_lms_library_user_catalogue_ajax(queryString, targetUrl);
    });

    jQuery(document).on("click", "#owt7_lms_catalogue_filters_form .owt7-lms-reset-filters", function(e) {
        e.preventDefault();
        var $form = jQuery("#owt7_lms_catalogue_filters_form");
        $form.find("select").each(function() {
            this.selectedIndex = 0;
        });
        $form.find("input[type='text']").val("");
        owt7_lms_library_user_catalogue_ajax("page=owt7_library_books_catalogue", this.href);
    });

    jQuery(document).on("click", "#owt7_lms_catalogue_results .owt7-lms-catalogue-pagination a", function(e) {
        e.preventDefault();
        var href = jQuery(this).attr("href") || "";
        if (!href) return;
        var url = new URL(href, window.location.origin);
        owt7_lms_library_user_catalogue_ajax(url.search.replace(/^\?/, ""), href);
    });

    /**
     * Delete Function Handler
     */
    jQuery(document).on("click", ".action-btn-delete", function() {
        var btn = jQuery(this);
        var isInsideDaysModal = btn.closest("#owt7_lms_mdl_days").length > 0;
        owt7_lms_confirm(owt7_library.messages.message_12).then(function(confirmed) {
            if (confirmed) {
                var dataId = btn.data("id");
                var dataModule = btn.data("module");
                var postdata = "id=" + dataId + "&module=" + dataModule + "&action=owt_lib_handler&param=owt7_lms_delete_function&owt7_lms_nonce=" + owt7_library.ajax_nonce;
                jQuery.post(owt7_library.ajaxurl, postdata, function(response) {
                    var data = jQuery.parseJSON(response);
                    if (data.sts == 1) {
                        owt7_lms_toastr(data.msg, "success");
                        if (isInsideDaysModal && typeof owt7_lms_refresh_days_modal_table === "function") {
                            owt7_lms_refresh_days_modal_table();
                        }
                    } else {
                        owt7_lms_toastr(data.msg, 'error');
                    }
                });
            }
        });
    });

    /**
     * DataTable export helpers: exclude Action column, professional filename, no-data check
     */
    function owt7_lms_export_columns(dt) {
        var n = dt.columns().count();
        if (n <= 1) return [0];
        var cols = [];
        for (var i = 0; i < n - 1; i++) cols.push(i);
        return cols;
    }
    function owt7_lms_export_filename(tableId) {
        var names = {
            'tbl_branches_list': 'Branches',
            'tbl_users_list': 'Users',
            'tbl_bookcases_list': 'Bookcases',
            'tbl_sections_list': 'Sections',
            'tbl_books_list': 'Books',
            'tbl_books_borrow_history': 'Borrow_History',
            'tbl_books_return_history': 'Return_History',
            'tbl_backups_list': 'Backups',
            'tbl_library_user_borrowed': 'My_Borrowed_Books',
            'tbl_library_user_returned': 'My_Returned_Books'
        };
        var base = (tableId && names[tableId]) ? names[tableId] : 'Export';
        var now = new Date();
        var ts = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0') + '-' + String(now.getDate()).padStart(2, '0') + '_' + String(now.getHours()).padStart(2, '0') + '-' + String(now.getMinutes()).padStart(2, '0') + '-' + String(now.getSeconds()).padStart(2, '0');
        return base + '_' + ts;
    }
    function owt7_lms_export_action(extendKey, defaultAction) {
        return function(e, dt, node, config) {
            var rowCount = dt.rows({ search: 'applied' }).count();
            if (rowCount === 0) {
                owt7_lms_toastr((typeof owt7_library !== 'undefined' && owt7_library.messages && owt7_library.messages.no_data_export) ? owt7_library.messages.no_data_export : 'No data available to export.', 'error');
                return;
            }
            config.exportOptions = config.exportOptions || {};
            config.exportOptions.columns = owt7_lms_export_columns(dt);
            config.filename = owt7_lms_export_filename(dt.table().node().id);
            if (typeof defaultAction === 'function') {
                defaultAction.call(this, e, dt, node, config);
            }
        };
    }
    var owt7_lms_dt_buttons = [
        {
            extend: 'excelHtml5',
            action: function(e, dt, node, config) {
                var def = jQuery.fn.dataTable && jQuery.fn.dataTable.ext && jQuery.fn.dataTable.ext.buttons && jQuery.fn.dataTable.ext.buttons.excelHtml5 ? jQuery.fn.dataTable.ext.buttons.excelHtml5.action : null;
                owt7_lms_export_action('excelHtml5', def).call(this, e, dt, node, config);
            }
        },
        {
            extend: 'csvHtml5',
            action: function(e, dt, node, config) {
                var def = jQuery.fn.dataTable && jQuery.fn.dataTable.ext && jQuery.fn.dataTable.ext.buttons && jQuery.fn.dataTable.ext.buttons.csvHtml5 ? jQuery.fn.dataTable.ext.buttons.csvHtml5.action : null;
                owt7_lms_export_action('csvHtml5', def).call(this, e, dt, node, config);
            }
        },
        {
            extend: 'pdfHtml5',
            action: function(e, dt, node, config) {
                var def = jQuery.fn.dataTable && jQuery.fn.dataTable.ext && jQuery.fn.dataTable.ext.buttons && jQuery.fn.dataTable.ext.buttons.pdfHtml5 ? jQuery.fn.dataTable.ext.buttons.pdfHtml5.action : null;
                owt7_lms_export_action('pdfHtml5', def).call(this, e, dt, node, config);
            }
        }
    ];

    var owt7_lms_dt_options = {
        "columnDefs": [
            { "orderable": false, "targets": 0 }
        ],
        dom: 'Bfrtip',
        buttons: owt7_lms_dt_buttons
    };

    /**
     * DataTable
     */
    jQuery(`
        #tbl_branches_list, 
        #tbl_users_list,
        #tbl_bookcases_list,
        #tbl_sections_list,
        #tbl_branches_list,
        #tbl_books_list,
        #tbl_books_borrow_history,
        #tbl_books_return_history,
        #tbl_backups_list,
        #owt7_lms_tbl_recent_borrows,
        #owt7_lms_tbl_recent_returns,
        #owt7_lms_tbl_books_by_cat,
        #owt7_lms_tbl_low_stock,
        #owt7_lms_tbl_stock_report,
        #owt7_lms_tbl_users_stats,
        #owt7_lms_tbl_fines,
        #tbl_library_user_borrowed,
        #tbl_library_user_returned
    `).DataTable(owt7_lms_dt_options);

    /**
     * Library User borrowed/returned tables: column filters (when Portal Settings enable them)
     */
    jQuery('#tbl_library_user_borrowed, #tbl_library_user_returned').each(function() {
        var $table = jQuery(this);
        var dt = $table.DataTable();
        $table.find('.owt7-lms-dt-col-filter').on('keyup change', function() {
            var colIdx = parseInt(jQuery(this).data('column'), 10);
            if (!isNaN(colIdx)) {
                dt.column(colIdx).search(this.value).draw();
            }
        });
    });

    /**
     * Users list: if URL has branch_id, pre-select branch filter and load filtered users (after DataTable init)
     */
    var $usersFilter = jQuery("#owt7_lms_data_filter[data-module='users']");
    if ($usersFilter.length && typeof URLSearchParams !== 'undefined') {
        var urlParams = new URLSearchParams(window.location.search);
        var branchId = urlParams.get("branch_id");
        if (branchId && branchId !== 'all') {
            var $option = $usersFilter.find("option[value='" + branchId + "']");
            if ($option.length) {
                $usersFilter.val(branchId);
                setTimeout(function() {
                    $usersFilter.trigger("change");
                }, 0);
            }
        }
    }

    /**
     * Upload Profile Image
     */
    jQuery(`
        #owt7_upload_image,
        #owt7_btn_upload_default_cover,
        #owt7_btn_upload_default_profile    
    `).on("click", function() {

        var dataModule = jQuery(this).data("module");

        var image = wp.media({
            title: owt7_library.messages.message_5,
            multiple: false
        }).open().on("select", function() {
            var uploaded_image = image.state().get('selection').first();
            var image_url = uploaded_image.toJSON().url;
            var ext = image_url.split('.').pop().toLowerCase();
            if (jQuery.inArray(ext, ['gif', 'png', 'jpg', 'jpeg']) == -1) {
                // Error
            } else {
                if (jQuery("#owt7_library_image_preview").length > 0) {
                    jQuery("#owt7_library_image_preview").removeClass("hide-input");
                    jQuery("#owt7_library_image_preview").attr('src', image_url);
                    jQuery("#owt7_image_url").val(image_url);
                }

                if (dataModule == "book") {
                    jQuery("#owt7_cover_image_preview").removeClass("hide-input");
                    jQuery("#owt7_cover_image_preview").attr('src', image_url);
                    jQuery("#default_book_cover_image").val(image_url);
                }

                if (dataModule == "user") {
                    jQuery("#owt7_profile_image_preview").removeClass("hide-input");
                    jQuery("#owt7_profile_image_preview").attr('src', image_url);
                    jQuery("#default_user_profile_image").val(image_url);
                }
            }
        });
    });

    /**
     * Add Book: Filter "Sections" by "Bookcase"
     */
    jQuery(document).on("change", "#owt7_dd_bookcase_id", function() {
        var bookcaseId = jQuery(this).val();
        var postdata = "bkcase_id=" + bookcaseId + "&action=owt_lib_handler&param=owt7_lms_filter_section&owt7_lms_nonce=" + owt7_library.ajax_nonce;
        jQuery.post(owt7_library.ajaxurl, postdata, function(response) {
            var data = jQuery.parseJSON(response);
            var sectionHtml = '<option> -- ' + owt7_library.messages.message_6 + ' --</option>';
            if (data.sts == 1) {
                jQuery.each(data.arr.sections, function(index, item) {
                    sectionHtml += `
                        <option value="` + item.id + `">` + item.name + `</option>
                    `;
                });
            }
            jQuery("#owt7_dd_section_id").html(sectionHtml);
        });
    });

    /**
     * Add Book / Return Book: Filter "Users" by "Branch" (only on pages that have user dropdown)
     * Skip on Add User / Edit User page to avoid unnecessary loader and AJAX.
     */
    jQuery(document).on("change", "#owt7_dd_branch_id", function() {
        var $userSelect = jQuery("#owt7_dd_u_id");
        var $borrowUserSelect = jQuery("#owt7_dd_borrow_u_id");
        if ($userSelect.length === 0 && $borrowUserSelect.length === 0) {
            return;
        }
        var branch_id = jQuery(this).val();
        var postdata = "branch_id=" + branch_id + "&action=owt_lib_handler&param=owt7_lms_filter_user&owt7_lms_nonce=" + owt7_library.ajax_nonce;
        jQuery.post(owt7_library.ajaxurl, postdata, function(response) {
            var data = jQuery.parseJSON(response);
            var userHtml = '<option> -- ' + owt7_library.messages.message_7 + ' --</option>';
            if (data.sts == 1) {
                jQuery.each(data.arr.users, function(index, item) {
                    var displayText = item.u_id ? item.name + ' (' + item.u_id + ')' : item.name;
                    userHtml += `
                        <option value="` + item.id + `">` + displayText + `</option>
                    `;
                });
            }
            if ($userSelect.length) {
                jQuery("#owt7_dd_u_id").html(userHtml);
                jQuery("#owt7_chk_books_list").html(`<span class="owt7-no-books-msg">No Book(s) Borrowed.</span>`);
            }
            if ($borrowUserSelect.length > 0) {
                jQuery("#owt7_dd_borrow_u_id").html(userHtml);
                jQuery("#return-section-books").addClass("hide-input");
                jQuery("#owt7_return_books_actions").hide();
            }
        });
    });

    /**
     * Add Book: Filter "Books" by "Category"
     */
    jQuery(document).on("change", "#owt7_dd_category_id", function() {
        var category_id = jQuery(this).val();
        var postdata = "category_id=" + category_id + "&action=owt_lib_handler&param=owt7_lms_filter_book&owt7_lms_nonce=" + owt7_library.ajax_nonce;
        jQuery.post(owt7_library.ajaxurl, postdata, function(response) {
            var data = jQuery.parseJSON(response);
            var bookHtml = '<option> -- ' + owt7_library.messages.message_8 + ' -- </option>';
            if (data.sts == 1) {
                jQuery.each(data.arr.books, function(index, item) {
                    var displayText = item.book_id ? item.name + ' (' + item.book_id + ')' : item.name;
                    bookHtml += `
                            <option value="` + item.id + `">` + displayText + `</option>
                        `;
                });
            }
            jQuery("#owt7_dd_book_id").html(bookHtml);
            jQuery("#owt7_book_copy_info").hide();
        });
    });

    /**
     * Borrow a Book: When a book is selected, show Total Copies Left and Next accession to assign
     */
    jQuery(document).on("change", "#owt7_dd_book_id", function() {
        var book_id = jQuery(this).val();
        var $info = jQuery("#owt7_book_copy_info");
        var $copies = jQuery("#owt7_copies_left_value");
        var $accession = jQuery("#owt7_next_accession_value");
        if (!book_id) {
            $info.hide();
            return;
        }
        var postdata = "book_id=" + book_id + "&action=owt_lib_handler&param=owt7_lms_book_copy_info&owt7_lms_nonce=" + owt7_library.ajax_nonce;
        jQuery.post(owt7_library.ajaxurl, postdata, function(response) {
            try {
                var data = typeof response === "object" ? response : jQuery.parseJSON(response);
                if (data.sts == 1 && data.arr) {
                    $copies.text(data.arr.copies_left != null ? data.arr.copies_left : "0");
                    $accession.text((data.arr.next_accession && data.arr.next_accession !== "") ? data.arr.next_accession : "—");
                    $info.show();
                } else {
                    $info.hide();
                }
            } catch (e) {
                $info.hide();
            }
        });
    });

    /**
     * Check Out Book: How to select book (Barcode Scan / Upload Image / Manual)
     * Category & Book section stays visible; dropdowns are read-only when barcode/upload is selected.
     */
    jQuery(document).on("change", "input[name=owt7_book_select_method]", function() {
        var method = jQuery(this).val();
        var $cat = jQuery("#owt7_dd_category_id");
        var $book = jQuery("#owt7_dd_book_id");
        var $catH = jQuery("#owt7_dd_category_id_h");
        var $bookH = jQuery("#owt7_dd_book_id_h");
        var $sectionBook = jQuery("#owt7_borrow_manual_book_section");

        jQuery("#owt7_borrow_barcode_scan_section, #owt7_borrow_barcode_upload_section").hide();
        if (method === "barcode_scan") {
            jQuery("#owt7_borrow_barcode_scan_section").show();
        } else if (method === "barcode_upload") {
            jQuery("#owt7_borrow_barcode_upload_section").show();
        }

        if (method === "barcode_scan" || method === "barcode_upload") {
            $sectionBook.addClass("owt7-borrow-readonly").show();
            $cat.prop("required", false).prop("disabled", true).removeAttr("name");
            $book.prop("required", false).prop("disabled", true).removeAttr("name");
            $catH.attr("name", "owt7_dd_category_id").val($cat.val() || "");
            $bookH.attr("name", "owt7_dd_book_id").val($book.val() || "");
        } else {
            $sectionBook.removeClass("owt7-borrow-readonly").show();
            $cat.prop("required", true).prop("disabled", false).attr("name", "owt7_dd_category_id");
            $book.prop("required", true).prop("disabled", false).attr("name", "owt7_dd_book_id");
            $catH.removeAttr("name").val("");
            $bookH.removeAttr("name").val("");
        }
        jQuery("#owt7_barcode_scan_result, #owt7_barcode_upload_result").text("");
    });

    /**
     * Check Out Book: Apply barcode lookup result to form (set category, book, accession)
     */
    function owt7_lms_apply_barcode_lookup(decodedText) {
        var $resultScan = jQuery("#owt7_barcode_scan_result");
        var $resultUpload = jQuery("#owt7_barcode_upload_result");
        var postdata = "barcode_data=" + encodeURIComponent(decodedText) + "&action=owt_lib_handler&param=owt7_lms_lookup_book_by_barcode&owt7_lms_nonce=" + owt7_library.ajax_nonce;
        jQuery.post(owt7_library.ajaxurl, postdata, function(response) {
            var data = typeof response === "object" ? response : (function() { try { return jQuery.parseJSON(response); } catch (e) { return {}; } })();
            var msg = data.msg || "";
            if (data.sts === 1 && data.arr) {
                var arr = data.arr;
                jQuery("#owt7_dd_category_id").val(arr.category_id);
                var bookOpt = '<option value=""> -- ' + (owt7_library.messages.message_8 || "Select Book") + ' -- </option><option value="' + arr.id + '" selected>' + (arr.name ? String(arr.name).replace(/</g, "&lt;").replace(/>/g, "&gt;") : "") + (arr.book_id ? " (" + String(arr.book_id).replace(/</g, "&lt;") + ")" : "") + "</option>";
                jQuery("#owt7_dd_book_id").html(bookOpt).val(arr.id);
                jQuery("#owt7_dd_category_id_h").val(arr.category_id || "");
                jQuery("#owt7_dd_book_id_h").val(arr.id || "");
                jQuery("#owt7_accession_number").val(arr.accession_number || "");
                jQuery("#owt7_book_copy_info").show();
                var postdata2 = "book_id=" + arr.id + "&action=owt_lib_handler&param=owt7_lms_book_copy_info&owt7_lms_nonce=" + owt7_library.ajax_nonce;
                jQuery.post(owt7_library.ajaxurl, postdata2, function(res2) {
                    var d2 = typeof res2 === "object" ? res2 : (function() { try { return jQuery.parseJSON(res2); } catch (e) { return {}; } })();
                    if (d2.sts === 1 && d2.arr) {
                        jQuery("#owt7_copies_left_value").text(d2.arr.copies_left != null ? d2.arr.copies_left : "0");
                        jQuery("#owt7_next_accession_value").text((d2.arr.next_accession && d2.arr.next_accession !== "") ? d2.arr.next_accession : "—");
                    }
                });
                var okMsg = "Book: " + (arr.name || "") + (arr.accession_number ? " (Acc: " + arr.accession_number + ")" : "");
                $resultScan.text(okMsg).css("color", "green");
                $resultUpload.text(okMsg).css("color", "green");
                if (typeof owt7_lms_barcodeHtml5QrCode !== "undefined" && owt7_lms_barcodeHtml5QrCode && owt7_lms_barcodeHtml5QrCode.isScanning && owt7_lms_barcodeHtml5QrCode.isScanning()) {
                    owt7_lms_barcodeHtml5QrCode.stop().catch(function() {});
                    jQuery("#owt7_barcode_start_camera_btn").show();
                    jQuery("#owt7_barcode_stop_camera_btn").hide();
                }
            } else {
                $resultScan.text(msg || "Book not found").css("color", "red");
                $resultUpload.text(msg || "Book not found").css("color", "red");
            }
        });
    }

    /**
     * Check Out Book: Start barcode camera
     */
    jQuery(document).on("click", "#owt7_barcode_start_camera_btn", function() {
        if (typeof Html5Qrcode === "undefined") {
            jQuery("#owt7_barcode_scan_result").text("Barcode scanner library not loaded.").css("color", "red");
            return;
        }
        jQuery("#owt7_barcode_reader").empty();
        jQuery("#owt7_barcode_scan_result").text("");
        owt7_lms_barcodeHtml5QrCode = new Html5Qrcode("owt7_barcode_reader");
        Html5Qrcode.getCameras().then(function(devices) {
            if (devices && devices.length) {
                var cameraId = devices[0].id;
                owt7_lms_barcodeHtml5QrCode.start(cameraId, { fps: 10, qrbox: { width: 380, height: 380 } }, function(decodedText) {
                    owt7_lms_apply_barcode_lookup(decodedText);
                }, function() {}).then(function() {
                    jQuery("#owt7_barcode_start_camera_btn").hide();
                    jQuery("#owt7_barcode_stop_camera_btn").show();
                }).catch(function(err) {
                    jQuery("#owt7_barcode_scan_result").text("Camera error: " + (err && err.message ? err.message : "Could not start")).css("color", "red");
                });
            } else {
                jQuery("#owt7_barcode_scan_result").text("No camera found.").css("color", "red");
            }
        }).catch(function(err) {
            jQuery("#owt7_barcode_scan_result").text("Camera error: " + (err && err.message ? err.message : "Could not access")).css("color", "red");
        });
    });

    /**
     * Check Out Book: Stop barcode camera
     */
    jQuery(document).on("click", "#owt7_barcode_stop_camera_btn", function() {
        if (typeof owt7_lms_barcodeHtml5QrCode !== "undefined" && owt7_lms_barcodeHtml5QrCode) {
            owt7_lms_barcodeHtml5QrCode.stop().then(function() {
                jQuery("#owt7_barcode_start_camera_btn").show();
                jQuery("#owt7_barcode_stop_camera_btn").hide();
            }).catch(function() {
                jQuery("#owt7_barcode_start_camera_btn").show();
                jQuery("#owt7_barcode_stop_camera_btn").hide();
            });
        }
    });

    /**
     * Check Out Book: Scan barcode from uploaded image (uses file input or dropped file)
     */
    jQuery(document).on("click", "#owt7_barcode_scan_image_btn", function() {
        var fileInput = jQuery("#owt7_barcode_image_input")[0];
        var file = (fileInput && fileInput.files && fileInput.files.length > 0) ? fileInput.files[0] : owt7_lms_borrow_dropped_file;
        if (!file) {
            jQuery("#owt7_barcode_upload_result").text("Please select an image.").css("color", "red");
            return;
        }
        if (typeof Html5Qrcode === "undefined") {
            jQuery("#owt7_barcode_upload_result").text("Barcode scanner library not loaded.").css("color", "red");
            return;
        }
        jQuery("#owt7_barcode_upload_result").text("Scanning…");
        var reader = new Html5Qrcode("owt7_barcode_reader");
        reader.scanFile(file, true).then(function(decodedText) {
            owt7_lms_apply_barcode_lookup(decodedText);
        }).catch(function() {
            jQuery("#owt7_barcode_upload_result").text("Could not read barcode from image.").css("color", "red");
        });
    });

    /**
     * Check Out Book: Show barcode image preview (replace drop zone)
     */
    function owt7_lms_borrow_show_barcode_preview(file, objectUrl) {
        var $zone = jQuery("#owt7_barcode_drop_zone");
        var $wrap = jQuery("#owt7_barcode_preview_wrap");
        var $img = jQuery("#owt7_barcode_preview_img");
        if (!$wrap.length || !$img.length) return;
        if (objectUrl) {
            $wrap.data("owt7-preview-url", objectUrl);
            $img.attr("src", objectUrl);
        }
        $zone.hide();
        $wrap.show();
    }

    /**
     * Check Out Book: Clear barcode preview and show drop zone again
     */
    function owt7_lms_borrow_clear_barcode_preview() {
        var $zone = jQuery("#owt7_barcode_drop_zone");
        var $wrap = jQuery("#owt7_barcode_preview_wrap");
        var $input = jQuery("#owt7_barcode_image_input");
        var url = $wrap.data("owt7-preview-url");
        if (url) {
            try { URL.revokeObjectURL(url); } catch (e) {}
            $wrap.removeData("owt7-preview-url");
        }
        $wrap.hide();
        $zone.show();
        if ($input.length) $input.val("");
        owt7_lms_borrow_dropped_file = null;
        jQuery("#owt7_barcode_upload_result").text("");
    }

    /**
     * Check Out Book: Drag-and-drop zone and file input change for barcode image
     */
    (function() {
        var $zone = jQuery("#owt7_barcode_drop_zone");
        var $result = jQuery("#owt7_barcode_upload_result");
        if (!$zone.length) return;
        function runBorrowScan(file) {
            if (!file || !file.type || file.type.indexOf("image/") !== 0) {
                $result.text("Please drop an image file.").css("color", "red");
                return;
            }
            if (typeof Html5Qrcode === "undefined") {
                $result.text("Barcode scanner library not loaded.").css("color", "red");
                return;
            }
            $result.text("Scanning…");
            var reader = new Html5Qrcode("owt7_barcode_reader");
            reader.scanFile(file, true).then(function(decodedText) {
                owt7_lms_apply_barcode_lookup(decodedText);
            }).catch(function() {
                $result.text("Could not read barcode from image.").css("color", "red");
            });
        }
        jQuery("#owt7_barcode_image_input").on("change", function() {
            var fileInput = this;
            if (fileInput.files && fileInput.files.length > 0) {
                owt7_lms_borrow_dropped_file = null;
                var url = URL.createObjectURL(fileInput.files[0]);
                owt7_lms_borrow_show_barcode_preview(fileInput.files[0], url);
            }
        });
        jQuery(document).on("click", "#owt7_barcode_remove_btn, #owt7_barcode_reupload_btn", function() {
            owt7_lms_borrow_clear_barcode_preview();
        });
        $zone.on("dragover", function(e) {
            e.preventDefault();
            e.stopPropagation();
            jQuery(this).addClass("owt7-drag-over");
        });
        $zone.on("dragleave", function(e) {
            if (e.relatedTarget && jQuery(this).find(e.relatedTarget).length) return;
            jQuery(this).removeClass("owt7-drag-over");
        });
        $zone.on("drop", function(e) {
            e.preventDefault();
            e.stopPropagation();
            jQuery(this).removeClass("owt7-drag-over");
            var file = e.originalEvent.dataTransfer && e.originalEvent.dataTransfer.files && e.originalEvent.dataTransfer.files[0];
            if (file && file.type && file.type.indexOf("image/") === 0) {
                owt7_lms_borrow_dropped_file = file;
                var url = URL.createObjectURL(file);
                owt7_lms_borrow_show_barcode_preview(file, url);
                runBorrowScan(file);
            }
        });
    })();

    /**
     * Return Books: How to select book (Barcode Scan / Upload Image / Manual)
     */
    jQuery(document).on("change", "input[name=owt7_return_book_select_method]", function() {
        var method = jQuery(this).val();
        jQuery("#owt7_return_barcode_scan_section, #owt7_return_barcode_upload_section").hide();
        if (method === "barcode_scan") {
            jQuery("#owt7_return_barcode_scan_section").show();
        } else if (method === "barcode_upload") {
            jQuery("#owt7_return_barcode_upload_section").show();
        }
        jQuery("#owt7_return_barcode_scan_result, #owt7_return_barcode_upload_result").text("");
    });

    /**
     * Return Books: Apply barcode lookup – set branch, user, load books, check the borrow
     */
    function owt7_lms_apply_return_barcode_lookup(decodedText) {
        var $resultScan = jQuery("#owt7_return_barcode_scan_result");
        var $resultUpload = jQuery("#owt7_return_barcode_upload_result");
        var $section = jQuery("#return-section-books");
        var $container = jQuery("#owt7_chk_books_list");
        var $actions = jQuery("#owt7_return_books_actions");
        var postdata = "barcode_data=" + encodeURIComponent(decodedText) + "&action=owt_lib_handler&param=owt7_lms_lookup_return_by_barcode&owt7_lms_nonce=" + owt7_library.ajax_nonce;
        jQuery.post(owt7_library.ajaxurl, postdata, function(response) {
            var data = typeof response === "object" ? response : (function() { try { return jQuery.parseJSON(response); } catch (e) { return {}; } })();
            if (data.sts !== 1 || !data.arr) {
                var msg = data.msg || "No active borrow found for this barcode.";
                $resultScan.text(msg).css("color", "red");
                $resultUpload.text(msg).css("color", "red");
                return;
            }
            var arr = data.arr;
            var branchId = arr.branch_id;
            var uId = arr.u_id;
            var borrowIdToCheck = arr.id;
            var okMsg = (arr.book_name || "Book") + " – " + (arr.user_name || "User");
            $resultScan.text(okMsg).css("color", "green");
            $resultUpload.text(okMsg).css("color", "green");
            if (typeof owt7_lms_returnBarcodeHtml5QrCode !== "undefined" && owt7_lms_returnBarcodeHtml5QrCode && owt7_lms_returnBarcodeHtml5QrCode.isScanning && owt7_lms_returnBarcodeHtml5QrCode.isScanning()) {
                owt7_lms_returnBarcodeHtml5QrCode.stop().catch(function() {});
                jQuery("#owt7_return_barcode_start_camera_btn").show();
                jQuery("#owt7_return_barcode_stop_camera_btn").hide();
            }
            jQuery("#owt7_dd_branch_id").val(branchId);
            jQuery.post(owt7_library.ajaxurl, "branch_id=" + branchId + "&action=owt_lib_handler&param=owt7_lms_filter_user&owt7_lms_nonce=" + owt7_library.ajax_nonce, function(userResponse) {
                var userData = typeof userResponse === "object" ? userResponse : (function() { try { return jQuery.parseJSON(userResponse); } catch (e) { return {}; } })();
                var userHtml = "<option value=\"\"> -- Select User -- </option>";
                if (userData.sts == 1 && userData.arr && userData.arr.users) {
                    jQuery.each(userData.arr.users, function(i, item) {
                        var displayText = item.u_id ? item.name + " (" + item.u_id + ")" : item.name;
                        userHtml += "<option value=\"" + item.id + "\">" + displayText + "</option>";
                    });
                }
                jQuery("#owt7_dd_borrow_u_id").html(userHtml).val(uId);
                jQuery.post(owt7_library.ajaxurl, "u_id=" + uId + "&action=owt_lib_handler&param=owt7_lms_filter_borrow_book&owt7_lms_nonce=" + owt7_library.ajax_nonce, function(bookResponse) {
                    var bookData = typeof bookResponse === "object" ? bookResponse : (function() { try { return jQuery.parseJSON(bookResponse); } catch (e) { return {}; } })();
                    var bookHtml = "";
                    if (bookData.sts == 1 && bookData.arr.books && bookData.arr.books.length > 0) {
                        jQuery.each(bookData.arr.books, function(index, item) {
                            var title = owt7_lms_toTitleCase(item.book_name);
                            var accRaw = (item.accession_number && String(item.accession_number).trim() !== "") ? String(item.accession_number) : "";
                            var accEsc = accRaw ? accRaw.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;") : "";
                            var accessionHtml = accEsc ? "<span class=\"owt7-book-return-card-accession\"> " + accEsc + "</span>" : "";
                            var checked = (item.id == borrowIdToCheck) ? " checked" : "";
                            bookHtml += "<label class=\"owt7-book-return-card\">" +
                                "<input type=\"checkbox\" name=\"owt7_borrow_books_id[]\" value=\"" + item.id + "\" class=\"owt7-book-return-chk\"" + checked + ">" +
                                "<span class=\"owt7-book-return-card-title\">" + title + "</span>" + accessionHtml +
                                "</label>";
                        });
                        $section.removeClass("hide-input");
                        $actions.show();
                        $container.html(bookHtml);
                    } else {
                        $section.removeClass("hide-input");
                        $actions.hide();
                        $container.html("<span class=\"owt7-no-books-msg\">No Book(s) Borrowed.</span>");
                    }
                });
            });
        });
    }

    /**
     * Return Books: Start barcode camera
     */
    jQuery(document).on("click", "#owt7_return_barcode_start_camera_btn", function() {
        if (typeof Html5Qrcode === "undefined") {
            jQuery("#owt7_return_barcode_scan_result").text("Barcode scanner library not loaded.").css("color", "red");
            return;
        }
        jQuery("#owt7_return_barcode_reader").empty();
        jQuery("#owt7_return_barcode_scan_result").text("");
        owt7_lms_returnBarcodeHtml5QrCode = new Html5Qrcode("owt7_return_barcode_reader");
        Html5Qrcode.getCameras().then(function(devices) {
            if (devices && devices.length) {
                var cameraId = devices[0].id;
                owt7_lms_returnBarcodeHtml5QrCode.start(cameraId, { fps: 10, qrbox: { width: 380, height: 380 } }, function(decodedText) {
                    owt7_lms_apply_return_barcode_lookup(decodedText);
                }, function() {}).then(function() {
                    jQuery("#owt7_return_barcode_start_camera_btn").hide();
                    jQuery("#owt7_return_barcode_stop_camera_btn").show();
                }).catch(function(err) {
                    jQuery("#owt7_return_barcode_scan_result").text("Camera error: " + (err && err.message ? err.message : "Could not start")).css("color", "red");
                });
            } else {
                jQuery("#owt7_return_barcode_scan_result").text("No camera found.").css("color", "red");
            }
        }).catch(function(err) {
            jQuery("#owt7_return_barcode_scan_result").text("Camera error: " + (err && err.message ? err.message : "Could not access")).css("color", "red");
        });
    });

    /**
     * Return Books: Stop barcode camera
     */
    jQuery(document).on("click", "#owt7_return_barcode_stop_camera_btn", function() {
        if (typeof owt7_lms_returnBarcodeHtml5QrCode !== "undefined" && owt7_lms_returnBarcodeHtml5QrCode) {
            owt7_lms_returnBarcodeHtml5QrCode.stop().then(function() {
                jQuery("#owt7_return_barcode_start_camera_btn").show();
                jQuery("#owt7_return_barcode_stop_camera_btn").hide();
            }).catch(function() {
                jQuery("#owt7_return_barcode_start_camera_btn").show();
                jQuery("#owt7_return_barcode_stop_camera_btn").hide();
            });
        }
    });

    /**
     * Return Books: Scan barcode from uploaded image (uses file input or dropped file)
     */
    jQuery(document).on("click", "#owt7_return_barcode_scan_image_btn", function() {
        var fileInput = jQuery("#owt7_return_barcode_image_input")[0];
        var file = (fileInput && fileInput.files && fileInput.files.length > 0) ? fileInput.files[0] : owt7_lms_return_dropped_file;
        if (!file) {
            jQuery("#owt7_return_barcode_upload_result").text("Please select an image.").css("color", "red");
            return;
        }
        if (typeof Html5Qrcode === "undefined") {
            jQuery("#owt7_return_barcode_upload_result").text("Barcode scanner library not loaded.").css("color", "red");
            return;
        }
        jQuery("#owt7_return_barcode_upload_result").text("Scanning…");
        var reader = new Html5Qrcode("owt7_return_barcode_reader");
        reader.scanFile(file, true).then(function(decodedText) {
            owt7_lms_apply_return_barcode_lookup(decodedText);
        }).catch(function() {
            jQuery("#owt7_return_barcode_upload_result").text("Could not read barcode from image.").css("color", "red");
        });
    });

    /**
     * Return Books: Show barcode image preview (replace drop zone)
     */
    function owt7_lms_return_show_barcode_preview(file, objectUrl) {
        var $zone = jQuery("#owt7_return_barcode_drop_zone");
        var $wrap = jQuery("#owt7_return_barcode_preview_wrap");
        var $img = jQuery("#owt7_return_barcode_preview_img");
        if (!$wrap.length || !$img.length) return;
        if (objectUrl) {
            $wrap.data("owt7-preview-url", objectUrl);
            $img.attr("src", objectUrl);
        }
        $zone.hide();
        $wrap.show();
    }

    /**
     * Return Books: Clear barcode preview and show drop zone again
     */
    function owt7_lms_return_clear_barcode_preview() {
        var $zone = jQuery("#owt7_return_barcode_drop_zone");
        var $wrap = jQuery("#owt7_return_barcode_preview_wrap");
        var $input = jQuery("#owt7_return_barcode_image_input");
        var url = $wrap.data("owt7-preview-url");
        if (url) {
            try { URL.revokeObjectURL(url); } catch (e) {}
            $wrap.removeData("owt7-preview-url");
        }
        $wrap.hide();
        $zone.show();
        if ($input.length) $input.val("");
        owt7_lms_return_dropped_file = null;
        jQuery("#owt7_return_barcode_upload_result").text("");
    }

    /**
     * Return Books: Drag-and-drop zone and file input change for barcode image
     */
    (function() {
        var $zone = jQuery("#owt7_return_barcode_drop_zone");
        var $result = jQuery("#owt7_return_barcode_upload_result");
        if (!$zone.length) return;
        function runReturnScan(file) {
            if (!file || !file.type || file.type.indexOf("image/") !== 0) {
                $result.text("Please drop an image file.").css("color", "red");
                return;
            }
            if (typeof Html5Qrcode === "undefined") {
                $result.text("Barcode scanner library not loaded.").css("color", "red");
                return;
            }
            $result.text("Scanning…");
            var reader = new Html5Qrcode("owt7_return_barcode_reader");
            reader.scanFile(file, true).then(function(decodedText) {
                owt7_lms_apply_return_barcode_lookup(decodedText);
            }).catch(function() {
                $result.text("Could not read barcode from image.").css("color", "red");
            });
        }
        jQuery("#owt7_return_barcode_image_input").on("change", function() {
            var fileInput = this;
            if (fileInput.files && fileInput.files.length > 0) {
                owt7_lms_return_dropped_file = null;
                var url = URL.createObjectURL(fileInput.files[0]);
                owt7_lms_return_show_barcode_preview(fileInput.files[0], url);
            }
        });
        jQuery(document).on("click", "#owt7_return_barcode_remove_btn, #owt7_return_barcode_reupload_btn", function() {
            owt7_lms_return_clear_barcode_preview();
        });
        $zone.on("dragover", function(e) {
            e.preventDefault();
            e.stopPropagation();
            jQuery(this).addClass("owt7-drag-over");
        });
        $zone.on("dragleave", function(e) {
            if (e.relatedTarget && jQuery(this).find(e.relatedTarget).length) return;
            jQuery(this).removeClass("owt7-drag-over");
        });
        $zone.on("drop", function(e) {
            e.preventDefault();
            e.stopPropagation();
            jQuery(this).removeClass("owt7-drag-over");
            var file = e.originalEvent.dataTransfer && e.originalEvent.dataTransfer.files && e.originalEvent.dataTransfer.files[0];
            if (file && file.type && file.type.indexOf("image/") === 0) {
                owt7_lms_return_dropped_file = file;
                var url = URL.createObjectURL(file);
                owt7_lms_return_show_barcode_preview(file, url);
                runReturnScan(file);
            }
        });
    })();

    /**
     * List of Borrowed Books By User (Return Book Page) - card layout
     */
    jQuery(document).on("change", "#owt7_dd_borrow_u_id", function() {
        var u_id = jQuery(this).val();
        var $section = jQuery("#return-section-books");
        var $container = jQuery("#owt7_chk_books_list");
        var $actions = jQuery("#owt7_return_books_actions");
        if (!u_id) {
            $section.addClass("hide-input");
            $actions.hide();
            $container.html("<span class=\"owt7-no-books-msg\">Select a user to see borrowed books.</span>");
            return;
        }
        var postdata = "u_id=" + u_id + "&action=owt_lib_handler&param=owt7_lms_filter_borrow_book&owt7_lms_nonce=" + owt7_library.ajax_nonce;
        jQuery.post(owt7_library.ajaxurl, postdata, function(response) {
            var data = jQuery.parseJSON(response);
            var bookHtml = "";
            if (data.sts == 1 && data.arr.books && data.arr.books.length > 0) {
                jQuery.each(data.arr.books, function(index, item) {
                    var title = owt7_lms_toTitleCase(item.book_name);
                    var accRaw = (item.accession_number && String(item.accession_number).trim() !== "") ? String(item.accession_number) : "";
                    var accEsc = accRaw ? accRaw.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;") : "";
                    var accessionHtml = accEsc ? "<span class=\"owt7-book-return-card-accession\"> " + accEsc + "</span>" : "";
                    bookHtml += "<label class=\"owt7-book-return-card\">" +
                        "<input type=\"checkbox\" name=\"owt7_borrow_books_id[]\" value=\"" + item.id + "\" class=\"owt7-book-return-chk\">" +
                        "<span class=\"owt7-book-return-card-title\">" + title + "</span>" + accessionHtml +
                        "</label>";
                });
                $section.removeClass("hide-input");
                $actions.show();
                $container.html(bookHtml);
            } else {
                $section.removeClass("hide-input");
                $actions.hide();
                $container.html("<span class=\"owt7-no-books-msg\">No Book(s) Borrowed.</span>");
            }
        });
    });

    /**
     * Return page: Select all / Deselect all borrowed books
     */
    jQuery(document).on("click", ".owt7-select-all-books", function() {
        jQuery("#owt7_chk_books_list .owt7-book-return-chk").prop("checked", true);
    });
    jQuery(document).on("click", ".owt7-deselect-all-books", function() {
        jQuery("#owt7_chk_books_list .owt7-book-return-chk").prop("checked", false);
    });

    function owt7_lms_return_modal_el() {
        return jQuery("#owt7_lms_mdl_return_status, #owt7_lms_mdl_return_status_txn").first();
    }

    function owt7_lms_return_get_borrow_ids() {
        var formId = owt7_lms_return_modal_el().data("return-form-id") || "owt7_lms_return_book";
        if (formId === "owt7_lms_return_book") {
            return jQuery("#owt7_chk_books_list .owt7-book-return-chk:checked").map(function() { return jQuery(this).val(); }).get();
        }
        var $form = jQuery("#" + formId);
        return $form.find("input[name='owt7_borrow_books_id[]']").map(function() { return jQuery(this).val(); }).get();
    }

    /**
     * Return Status modal: open when Submit & Save is clicked (Return Books page)
     */
    jQuery(document).on("click", "#owt7_return_open_modal_btn", function() {
        var branch = jQuery("#owt7_dd_branch_id").val();
        var user = jQuery("#owt7_dd_borrow_u_id").val();
        var checked = jQuery("#owt7_chk_books_list .owt7-book-return-chk:checked");
        if (!branch || !user) {
            owt7_lms_toastr("Please select Branch and User.", "error");
            return;
        }
        if (!checked.length) {
            owt7_lms_toastr("Please select at least one book to return.", "error");
            return;
        }
        owt7_lms_return_modal_el().data("return-form-id", "owt7_lms_return_book");
        owt7_lms_return_modal_el().show();
        jQuery("body").addClass("owt7-lms-modal-open");
        owt7_lms_return_status_update_fine_display();
    });

    /**
     * Return Status modal: open when Return is clicked in borrow list (Transactions page)
     */
    jQuery(document).on("click", ".owt7_lms_btn_return", function(e) {
        var dataId = jQuery(this).data("id");
        if (dataId == null || dataId === "") return;
        var formId = "owt7_lms_return_book_" + dataId;
        if (jQuery("#" + formId).length === 0) return;
        e.preventDefault();
        owt7_lms_return_modal_el().data("return-form-id", formId);
        owt7_lms_return_modal_el().show();
        jQuery("body").addClass("owt7-lms-modal-open");
        owt7_lms_return_status_update_fine_display();
    });

    /**
     * Return Status modal: update total fine display via AJAX
     */
    function owt7_lms_return_status_update_fine_display() {
        var borrowIds = owt7_lms_return_get_borrow_ids();
        if (!borrowIds.length) return;
        var condition = jQuery("#owt7_return_condition").val() || "normal_return";
        var postdata = "action=owt_lib_handler&param=owt7_lms_return_fine_preview&owt7_lms_nonce=" + owt7_library.ajax_nonce + "&owt7_return_condition=" + encodeURIComponent(condition);
        jQuery.each(borrowIds, function(i, id) { postdata += "&owt7_borrow_books_id[]=" + encodeURIComponent(id); });
        jQuery.post(owt7_library.ajaxurl, postdata, function(response) {
            var data = typeof response === "object" ? response : (function() { try { return JSON.parse(response); } catch (e) { return {}; } })();
            var currency = (data.arr && data.arr.currency) ? data.arr.currency : "";
            var total = (data.arr && typeof data.arr.total_fine !== "undefined") ? parseFloat(data.arr.total_fine) : 0;
            jQuery("#owt7_return_total_fine_display").text(total + " " + currency);
        }).fail(function() {
            jQuery("#owt7_return_total_fine_display").text("—");
        });
    }

    jQuery(document).on("change", "#owt7_return_condition", function() {
        owt7_lms_return_status_update_fine_display();
    });

    jQuery(document).on("click", "#owt7_return_status_modal_submit", function() {
        var $btn = jQuery(this);
        var formId = owt7_lms_return_modal_el().data("return-form-id") || "owt7_lms_return_book";
        var $form = jQuery("#" + formId);
        if (!$form.length) { owt7_lms_toastr("Form not found.", "error"); return; }
        var formdata = $form.serialize();
        var condition = jQuery("#owt7_return_condition").val() || "normal_return";
        var remark = jQuery("#owt7_return_remark").val() || "";
        var postdata = formdata + "&action=owt_lib_handler&param=owt7_lms_return_book&owt7_lms_nonce=" + owt7_library.ajax_nonce + "&owt7_return_condition=" + encodeURIComponent(condition) + "&owt7_return_remark=" + encodeURIComponent(remark);
        $btn.prop("disabled", true);
        jQuery.post(owt7_library.ajaxurl, postdata, function(response) {
            var data = typeof response === "object" ? response : (function() { try { return JSON.parse(response); } catch (e) { return {}; } })();
            $btn.prop("disabled", false);
            owt7_lms_return_modal_el().hide();
            jQuery("body").removeClass("owt7-lms-modal-open");
            if (data.sts == 1) {
                owt7_lms_toastr(data.msg, "success");
                jQuery("#owt7_chk_books_list .owt7-book-return-chk:checked").prop("checked", false);
                owt7_lms_return_status_update_fine_display();
                if (formId.indexOf("owt7_lms_return_book_") === 0) {
                    jQuery("#tbl_books_borrow_history").DataTable().draw(false);
                }
            } else {
                owt7_lms_toastr(data.msg || "Return failed.", "error");
            }
        }).fail(function() {
            $btn.prop("disabled", false);
            owt7_lms_toastr("Request failed. Please try again.", "error");
        });
    });

    jQuery(document).on("click", "#owt7_lms_mdl_return_status .close, #owt7_lms_mdl_return_status_txn .close, .owt7_return_status_modal_cancel", function() {
        owt7_lms_return_modal_el().hide();
        jQuery("body").removeClass("owt7-lms-modal-open");
    });

    jQuery(document).on("change", ".owt7_lms_dd_data_filter", function() {
        var dataTableBodyId = jQuery(this).data("table");
        var dataTableId = jQuery("#" + dataTableBodyId).parent("table").attr("id");
        var dataOption = jQuery(this).data("option");
        var listType = jQuery(this).data("list");
        var optionId = jQuery(this).val();
        jQuery("#" + dataTableId).DataTable().destroy();
        var postdata = "filterby=" + dataOption + "&id=" + optionId + "&list=" + listType + "&action=owt_lib_handler&param=owt7_lms_data_filters&owt7_lms_nonce=" + owt7_library.ajax_nonce;
        jQuery.post(owt7_library.ajaxurl, postdata, function(response) {
            var data = jQuery.parseJSON(response);
            if (data.sts == 1) {
                jQuery("#" + dataTableBodyId).html(data.arr.template);
            } else {
                jQuery("#" + dataTableBodyId).html("");
            }
            jQuery("#" + dataTableId).DataTable(owt7_lms_dt_options);
        });
    });

    var owt7_lms_modal = jQuery('#owt7_lms_mdl_settings');

    jQuery(`
        #owt7_lms_country_modal,
        #owt7_lms_backup_modal,
        #owt7_lms_download_sample_csv_modal
    `).on('click', function() {
        owt7_lms_modal.show();
        jQuery('body').addClass('owt7-lms-modal-open');
    });

    var owt7_lms_days_modal = jQuery('#owt7_lms_mdl_days');
    jQuery('#owt7_lms_days_modal').on('click', function() {
        owt7_lms_days_modal.show();
        jQuery('body').addClass('owt7-lms-modal-open');
        var daysBtn = jQuery('#owt7_lms_days_form').find("button[type='submit']");
        if (daysBtn.length && !daysBtn.data("original-text")) daysBtn.data("original-text", daysBtn.text());
    });
    jQuery(document).on('click', '#owt7_lms_mdl_days .close', function() {
        owt7_lms_days_modal.hide();
        jQuery('body').removeClass('owt7-lms-modal-open');
    });

    var owt7_lms_late_fine_modal = jQuery('#owt7_lms_mdl_late_fine');
    jQuery('#owt7_lms_late_fine_modal, #owt7_lms_fine_modal').on('click', function() {
        owt7_lms_late_fine_modal.show();
        jQuery('body').addClass('owt7-lms-modal-open');
    });
    jQuery(document).on('click', '#owt7_lms_mdl_late_fine .close', function() {
        owt7_lms_late_fine_modal.hide();
        jQuery('body').removeClass('owt7-lms-modal-open');
    });

    function owt7_lms_refresh_days_modal_table() {
        var tbody = jQuery("#owt7_lms_days_modal_tbody");
        if (!tbody.length) return;
        var postdata = "action=owt_lib_handler&param=owt7_lms_get_days_list&owt7_lms_nonce=" + owt7_library.ajax_nonce;
        jQuery.post(owt7_library.ajaxurl, postdata, function(response) {
            var data = typeof response === "string" ? JSON.parse(response) : response;
            if (data.sts !== 1 || !data.strings) return;
            var s = data.strings;
            var canDelete = data.can_delete;
            var days = data.days || [];
            var html = "";
            if (days.length === 0) {
                var colCount = canDelete ? 3 : 2;
                html = "<tr class=\"owt7-lms-days-empty-row\"><td colspan=\"" + colCount + "\">" + (s.no_days_yet || "No days added yet.") + "</td></tr>";
            } else {
                for (var i = 0; i < days.length; i++) {
                    var day = days[i];
                    var idB64 = typeof btoa !== "undefined" ? btoa(String(day.id)) : String(day.id);
                    var modB64 = typeof btoa !== "undefined" ? btoa("days") : "days";
                    html += "<tr><td>" + (i + 1) + "</td><td>" + parseInt(day.days, 10) + " " + (s.days_label || "days") + "</td>";
                    if (canDelete) {
                        html += "<td><a href=\"javascript:void(0);\" title=\"" + (s.delete_title || "Delete") + "\" class=\"action-btn delete-btn action-btn-delete\" data-id=\"" + idB64 + "\" data-module=\"" + modB64 + "\"><span class=\"dashicons dashicons-trash\"></span></a></td>";
                    }
                    html += "</tr>";
                }
            }
            tbody.html(html);
        });
    }

    /** 
     * Modal Close Button Action
     */
    jQuery('.close').on('click', function() {
        owt7_lms_modal.hide();
        jQuery('body').removeClass('owt7-lms-modal-open');
    });

    /* Sync color picker with hex text (Public Design View modals and any .owt7-lms-color-picker / .owt7-lms-color-hex) */
    jQuery(document).on('input change', '.owt7-lms-color-picker', function() {
        var id = jQuery(this).attr('id');
        if (id) {
            jQuery('.owt7-lms-color-hex[data-for="' + id + '"]').val(jQuery(this).val());
        }
    });
    jQuery(document).on('input change', '.owt7-lms-color-hex', function() {
        var val = jQuery(this).val();
        if (/^#[0-9a-fA-F]{6}$/.test(val)) {
            var targetId = jQuery(this).data('for');
            if (targetId) {
                jQuery('#' + targetId).val(val);
            }
        }
    });

    var owt7_lms_public_view_modal = jQuery('#owt7_lms_mdl_public_view');
    jQuery('#owt7_lms_public_view_btn').on('click', function() {
        owt7_lms_public_view_modal.show();
        jQuery('body').addClass('owt7-lms-modal-open');
    });
    jQuery('.owt7_lms_public_view_modal_wrap .owt7-lms-close-public-view-modal').on('click', function() {
        owt7_lms_public_view_modal.hide();
        jQuery('body').removeClass('owt7-lms-modal-open');
    });
    jQuery(document).on('submit', '#owt7_lms_public_view_form', function(e) {
        e.preventDefault();
        var form = jQuery(this);
        var postdata = {
            action: 'owt_lib_handler',
            param: 'owt7_lms_save_public_view_settings',
            owt7_lms_nonce: owt7_library.ajax_nonce,
            cards_per_row: form.find('#owt7_lms_cards_per_row').val(),
            heading_font_size: form.find('#owt7_lms_heading_font_size').val(),
            body_font_size: form.find('#owt7_lms_body_font_size').val(),
            view_btn_placement: form.find('#owt7_lms_view_btn_placement').val(),
            card_bg_color: form.find('#owt7_lms_card_bg_color').val(),
            view_btn_padding: form.find('#owt7_lms_view_btn_padding').val(),
            view_btn_font_size: form.find('#owt7_lms_view_btn_font_size').val(),
            view_btn_color: form.find('#owt7_lms_view_btn_color').val(),
            checkout_btn_color: form.find('#owt7_lms_checkout_btn_color').val()
        };
        form.find('button[type="submit"]').prop('disabled', true).text(owt7_library.messages.message_1 || 'Saving...');
        jQuery.post(owt7_library.ajaxurl, postdata, function(response) {
            var data = typeof response === 'string' ? JSON.parse(response) : response;
            form.find('button[type="submit"]').prop('disabled', false).text('Save Public View Settings');
            if (data.sts === 1) {
                owt7_lms_toastr('Public view settings saved.', 'success');
                owt7_lms_public_view_modal.hide();
                jQuery('body').removeClass('owt7-lms-modal-open');
            } else {
                owt7_lms_toastr('Failed to save.', 'error');
            }
        });
    });

    /**
     * Book Copies modal – open from single book view or from books list actions
     */
    var owt7_lms_book_copies_modal = jQuery('#owt7_lms_mdl_book_copies');
    if (owt7_lms_book_copies_modal.length) {
        var owt7_lms_book_copies_loading = jQuery('#owt7_lms_book_copies_loading');
        var owt7_lms_book_copies_content = jQuery('#owt7_lms_book_copies_content');
        var owt7_lms_book_copies_error = jQuery('#owt7_lms_book_copies_error');
        var owt7_lms_book_copies_tbody = jQuery('#owt7_lms_book_copies_tbody');
        var owt7_lms_book_copies_modal_title = jQuery('#owt7_lms_book_copies_modal_title');
        var owt7_lms_book_copies_empty = jQuery('#owt7_lms_book_copies_empty');
        var owt7_lms_book_copies_table_wrap = owt7_lms_book_copies_content.find('.owt7-lms-book-copies-table-wrap');

        var owt7_lms_last_book_copies_data = { book_title: '', book_author: '', book_publication: '', copies: [] };
        function owt7_lms_render_copies_table(copies, bookTitle, bookAuthor, bookPublication) {
            owt7_lms_last_book_copies_data = {
                book_title: bookTitle || '',
                book_author: bookAuthor || '',
                book_publication: bookPublication || '',
                copies: copies || []
            };
            owt7_lms_book_copies_modal_title.text(bookTitle || '');
            owt7_lms_book_copies_tbody.empty();
            if (!copies || copies.length === 0) {
                owt7_lms_book_copies_empty.show();
                owt7_lms_book_copies_table_wrap.hide();
                return;
            }
            owt7_lms_book_copies_empty.hide();
            owt7_lms_book_copies_table_wrap.show();
            function owt7Esc(s) {
                if (s == null || s === '') return '';
                return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
            }
            function owt7TitleCase(s) {
                if (!s || typeof s !== 'string') return '';
                return s.replace(/\b\w/g, function(c) { return c.toUpperCase(); });
            }
            copies.forEach(function(row, idx) {
                var statusVal = (row.status || '').toLowerCase();
                var isAvailable = statusVal === 'available';
                var statusClass = isAvailable ? 'owt7-lms-copy-status-available' : 'owt7-lms-copy-status-unavailable';
                var statusLabel = row.status ? owt7Esc(owt7TitleCase(row.status)) : '—';
                var statusHtml = '<span class="owt7-lms-copy-status-badge ' + statusClass + '">' + statusLabel + '</span>';
                var shelfRaw = row.shelf_location || '';
                var shelfParts = shelfRaw.split(/\s*\|\s*/).map(function(p) { return owt7Esc(p.trim()); });
                var shelfHtml = shelfParts.length >= 2
                    ? shelfParts[0] + ' <span class="dashicons dashicons-arrow-right-alt owt7-lms-shelf-arrow"></span> ' + shelfParts[1]
                    : (shelfParts[0] || '—');
                owt7_lms_book_copies_tbody.append(
                    '<tr><td>' + (idx + 1) + '</td><td>' + owt7Esc(row.accession_number) + '</td><td>' + statusHtml + '</td><td>' + shelfHtml + '</td></tr>'
                );
            });
        }

        jQuery(document).on('click', '.owt7_lms_show_book_copies_btn', function() {
            var bookId = jQuery(this).data('book-id');
            if (!bookId) return;
            owt7_lms_book_copies_modal.show();
            jQuery('body').addClass('owt7-lms-modal-open');
            owt7_lms_book_copies_content.hide();
            owt7_lms_book_copies_empty.hide();
            owt7_lms_book_copies_table_wrap.show();
            owt7_lms_book_copies_error.hide().text('');
            owt7_lms_book_copies_modal_title.text('');
            owt7_lms_book_copies_loading.show();
            owt7_lms_book_copies_tbody.empty();
            jQuery.post(owt7_library.ajaxurl, {
                action: 'owt_lib_handler',
                param: 'owt7_lms_get_book_copies',
                book_id: bookId,
                owt7_lms_nonce: owt7_library.ajax_nonce
            }, function(response) {
                owt7_lms_book_copies_loading.hide();
                var data = typeof response === 'string' ? JSON.parse(response) : response;
                if (data.sts === 1 && data.arr) {
                    owt7_lms_book_copies_content.attr('data-book-id', bookId);
                    owt7_lms_render_copies_table(data.arr.copies || [], data.arr.book_title || '', data.arr.book_author || '', data.arr.book_publication || '');
                    owt7_lms_book_copies_content.show();
                } else {
                    owt7_lms_book_copies_error.text(data.msg || 'Failed to load book copies.').show();
                }
            }).fail(function() {
                owt7_lms_book_copies_loading.hide();
                owt7_lms_book_copies_error.text('Request failed.').show();
            });
        });
        jQuery(document).on('click', '.owt7-lms-close-book-copies-modal', function() {
            owt7_lms_book_copies_modal.hide();
            jQuery('body').removeClass('owt7-lms-modal-open');
        });
    }

    /**
     * Library User (LMS) Portal Settings modal
     */
    var owt7_lms_library_user_portal_modal = jQuery('#owt7_lms_mdl_library_user_portal_settings');
    if (owt7_lms_library_user_portal_modal.length) {
        jQuery('#owt7_lms_library_user_portal_settings_btn').on('click', function() {
            owt7_lms_library_user_portal_modal.show();
            jQuery('body').addClass('owt7-lms-modal-open');
        });
        owt7_lms_library_user_portal_modal.find('.owt7-lms-close-library-user-portal-modal').on('click', function() {
            owt7_lms_library_user_portal_modal.hide();
            jQuery('body').removeClass('owt7-lms-modal-open');
        });
        jQuery('#owt7_lms_library_user_portal_settings_form').on('submit', function(e) {
            e.preventDefault();
            var $form = jQuery(this);
            var $btn = $form.find('button[type="submit"]');
            $btn.prop('disabled', true).text('Saving…');
            var formdata = $form.serialize() + '&action=owt_lib_handler&owt7_lms_nonce=' + owt7_library.ajax_nonce;
            jQuery.post(owt7_library.ajaxurl, formdata).done(function(response) {
                var data = typeof response === 'string' ? (function() { try { return JSON.parse(response); } catch (err) { return {}; } })() : (response || {});
                if (data.sts === 1) {
                    owt7_lms_toastr(data.msg || 'Saved.', 'success');
                    owt7_lms_library_user_portal_modal.hide();
                    jQuery('body').removeClass('owt7-lms-modal-open');
                } else {
                    owt7_lms_toastr(data.msg || 'Error saving.', 'error');
                }
            }).fail(function() {
                owt7_lms_toastr('Request failed.', 'error');
            }).always(function() {
                $btn.prop('disabled', false).text('Save Library User portal settings');
            });
        });
    }

    /**
     * Basic Settings (LMS Frontend) modal – open, close, save from modal
     */
    var owt7_lms_frontend_modal = jQuery('#owt7_lms_mdl_lms_frontend_settings');
    if (owt7_lms_frontend_modal.length) {
        jQuery('#owt7_lms_lms_frontend_modal_btn').on('click', function() {
            owt7_lms_frontend_modal.show();
            jQuery('body').addClass('owt7-lms-modal-open');
        });
        owt7_lms_frontend_modal.find('.owt7-lms-close-lms-frontend-modal').on('click', function() {
            owt7_lms_frontend_modal.hide();
            jQuery('body').removeClass('owt7-lms-modal-open');
        });
    }
    
    /**
     *  Filters: 
     *  - "Books by Category", 
     *  - "Sections by Bookcase", 
     *  - "Users by Branch"
     */
    jQuery(document).on("change", "#owt7_lms_data_filter", function() {
        var $filter = jQuery(this);
        var module = $filter.data("module");
        var filterBy = $filter.data("filter-by");
        var tableId = "#tbl_" + module + "_list";
        var $table = jQuery(tableId);
        var filterValue = $filter.val();
        var tableBody = jQuery("table#tbl_" + module + "_list tbody");
        var branchName = (module === "users" && filterValue && filterValue !== "all") ? $filter.find("option:selected").text() : "";
        if ($table.length && jQuery.fn.DataTable && jQuery.fn.DataTable.isDataTable($table)) {
            $table.DataTable().destroy();
        }
        var postdata = "module=" + module + "&filterBy=" + filterBy + "&value=" + filterValue + "&action=owt_lib_handler&param=owt7_lms_data_option_filters&owt7_lms_nonce=" + owt7_library.ajax_nonce;
        jQuery.post(owt7_library.ajaxurl, postdata, function(response) {
            var data = jQuery.parseJSON(response);
            if (data.sts == 1) {
                tableBody.html(data.arr.template);
            } else {
                tableBody.html("");
            }
            jQuery(tableId).DataTable(owt7_lms_dt_options);
            if (module === "users") {
                var $msg = jQuery("#owt7_lms_users_filter_msg");
                if ($msg.length) {
                    if (branchName) {
                        $msg.text((owt7_library.messages && owt7_library.messages.showing_branch_users) ? owt7_library.messages.showing_branch_users.replace("%s", branchName) : 'Showing "' + branchName + '" borrowers').show();
                    } else {
                        $msg.text("").hide();
                    }
                }
            }
        });
    });

    /**
     * Run Test Data Importer
     */
    jQuery(document).on("click", "#owt7_lms_run_data_importer, #owt7_lms_refresh_test_data", function() {
        owt7_lms_confirm(owt7_library.messages.message_9).then(function(confirmed) {
            if (confirmed) {
                var postdata = "action=owt_lib_handler&param=owt7_lms_import_test_data&owt7_lms_nonce=" + owt7_library.ajax_nonce;
                jQuery.post(owt7_library.ajaxurl, postdata, function(response) {
                    var data = jQuery.parseJSON(response);
                    if (data.sts == 1) {
                        owt7_lms_toastr(data.msg, "success");
                    } else {
                        owt7_lms_toastr(data.msg, 'error');
                    }
                });
            }
        });
    });

    /**
     * Remove Test Data
     */
    jQuery(document).on("click", "#owt7_lms_remove_test_data", function() {
        owt7_lms_confirm(owt7_library.messages.message_10).then(function(confirmed) {
            if (confirmed) {
                var postdata = "action=owt_lib_handler&param=owt7_lms_remove_test_data&owt7_lms_nonce=" + owt7_library.ajax_nonce;
                jQuery.post(owt7_library.ajaxurl, postdata, function(response) {
                    var data = jQuery.parseJSON(response);
                    if (data.sts == 1) {
                        owt7_lms_toastr(data.msg, "success");
                    } else {
                        owt7_lms_toastr(data.msg, 'error');
                    }
                });
            }
        });
    });

    /**
     * Pay Late Fine
     */
    jQuery(document).on("click", ".owt7_pay_late_fine", function() {
        var return_id = jQuery(this).data("id");
        owt7_lms_confirm(owt7_library.messages.message_11).then(function(confirmed) {
            if (confirmed) {
                var postdata = "return_id=" + return_id + "&action=owt_lib_handler&param=owt7_pay_late_fine&owt7_lms_nonce=" + owt7_library.ajax_nonce;
                jQuery.post(owt7_library.ajaxurl, postdata, function(response) {
                    var data = jQuery.parseJSON(response);
                    if (data.sts == 1) {
                        owt7_lms_toastr(data.msg, "success");
                    } else {
                        owt7_lms_toastr(data.msg, 'error');
                    }
                });
            }
        });
    });

    /** Return History: View – load modal content via AJAX template */
    jQuery(document).on("click", ".owt7_lms_view_return_details", function() {
        var $row = jQuery(this).closest("tr.lms-history-row");
        var returnId = $row.attr("data-return-id");
        if (!returnId) return;
        var $modal = jQuery("#owt7_lms_mdl_view_return_details");
        var $bookEl = jQuery("#owt7_lms_view_book_details");
        var $userEl = jQuery("#owt7_lms_view_user_details");
        var $remarkEl = jQuery("#owt7_lms_view_remark_details");
        var $payBtn = jQuery("#owt7_lms_view_pay_now_btn");
        $bookEl.html("<p class=\"owt7-lms-view-loading\">" + (typeof owt7_library !== "undefined" && owt7_library.messages && owt7_library.messages.loading ? owt7_library.messages.loading : "Loading...") + "</p>");
        $userEl.empty();
        $remarkEl.empty();
        $payBtn.removeAttr("data-id").hide();
        $modal.show();
        jQuery("body").addClass("owt7-lms-modal-open");
        var postdata = "return_id=" + encodeURIComponent(returnId) + "&action=owt_lib_handler&param=owt7_lms_get_return_view_content&owt7_lms_nonce=" + (typeof owt7_library !== "undefined" ? owt7_library.ajax_nonce : "");
        jQuery.post(typeof owt7_library !== "undefined" ? owt7_library.ajaxurl : "", postdata, function(response) {
            var data = typeof response === "string" ? (function() { try { return JSON.parse(response); } catch (e) { return {}; } })() : response;
            if (data.sts === 1 && data.arr) {
                $bookEl.html(data.arr.book_html || "");
                $userEl.html(data.arr.user_html || "");
                $remarkEl.html(data.arr.remark_html || "");
                if (data.arr.has_fine && data.arr.pay_fine_id) {
                    $payBtn.attr("data-id", data.arr.pay_fine_id).show();
                } else {
                    $payBtn.hide();
                }
                // Allow receipt downloads for any valid return record, including zero-fine returns.
                var $rcptBtn = jQuery("#owt7_lms_view_download_receipt_btn");
                $rcptBtn.hide();
                if (data.arr.can_download_receipt) {
                    $rcptBtn.attr("data-return-db-id", returnId).show();
                }
            } else {
                $bookEl.html("<p class=\"owt7-lms-view-error\">" + (data.msg || "Unable to load details.") + "</p>");
            }
        }).fail(function() {
            $bookEl.html("<p class=\"owt7-lms-view-error\">Unable to load details.</p>");
        });
    });
    jQuery(document).on("click", "#owt7_lms_mdl_view_return_details .close", function() {
        jQuery("#owt7_lms_mdl_view_return_details").hide();
        jQuery("body").removeClass("owt7-lms-modal-open");
    });

    /**
     * Last 7 digits of timestamp (ms) for unique ID suffix. Same pattern as server (prefix + 7 digits).
     */
    function owt7_lms_timestampSuffix7() {
        var ts = String(Date.now());
        return ts.slice(-7);
    }

    /**
     * Generate and set Book ID or User ID in the form: prefix + 7-digit timestamp suffix.
     * Used on page load (add forms) and when user clicks "Auto-generate" link.
     * @param {string} module - "book" or "user"
     */
    function owt7_lms_autoGenerateId(module) {
        var prefix = "";
        if (typeof owt7_library !== "undefined") {
            if (module === "book") {
                prefix = owt7_library.book_prefix || "LMSBK";
            } else if (module === "user") {
                prefix = owt7_library.user_prefix || "LMSUS";
            }
        }
        var systemId = prefix + owt7_lms_timestampSuffix7();
        if (module === "book") {
            jQuery("#owt7_txt_book_id").val(systemId);
        } else if (module === "user") {
            jQuery("#owt7_txt_u_id").val(systemId);
        }
    }

    jQuery(document).on("click", "#owt7_btn_ids_auto_generate", function() {
        var module = jQuery(this).data("module");
        if (module === "book" || module === "user") {
            owt7_lms_autoGenerateId(module);
        }
    });

    // Add New Book: auto-generate and show Book ID on page load; user can change via "Auto-generate" link
    if (jQuery("#owt7_lms_book_form").length && jQuery("#owt7_lms_book_form input[name=action_type]").val() === "add") {
        var $bookIdField = jQuery("#owt7_txt_book_id");
        if ($bookIdField.length && !$bookIdField.val().trim()) {
            owt7_lms_autoGenerateId("book");
        }
    }
    // Add New User: auto-generate and show User ID on page load; user can change via "Auto-generate" link
    if (jQuery("#owt7_lms_user_form").length && jQuery("#owt7_lms_user_form input[name=action_type]").val() === "add") {
        var $userIdField = jQuery("#owt7_txt_u_id");
        if ($userIdField.length && !$userIdField.val().trim()) {
            owt7_lms_autoGenerateId("user");
        }
    }

    /**
     * Add User: Toggle WordPress credentials fields when "Create as WordPress user?" is checked
     */
    function owt7_lms_toggle_wp_creds_fields() {
        var cb = jQuery("#owt7_save_as_wp_user");
        var fields = jQuery("#owt7_wp_creds_fields");
        if (cb.length && fields.length) {
            if (cb.is(":checked")) {
                fields.slideDown(200);
                jQuery("#owt7_wp_username, #owt7_wp_password").prop("required", true);
            } else {
                fields.slideUp(200);
                jQuery("#owt7_wp_username, #owt7_wp_password").prop("required", false).val("");
            }
        }
    }
    jQuery(document).on("change", "#owt7_save_as_wp_user", owt7_lms_toggle_wp_creds_fields);
    jQuery(function() { owt7_lms_toggle_wp_creds_fields(); });

    /**
     * Edit User: Toggle "Create as WordPress user?" credentials when linking a non-WP user
     */
    function owt7_lms_toggle_wp_creds_fields_edit() {
        var cb = jQuery("#owt7_save_as_wp_user_edit");
        var fields = jQuery("#owt7_wp_creds_fields_edit");
        if (cb.length && fields.length) {
            if (cb.is(":checked")) {
                fields.slideDown(200);
                jQuery("#owt7_wp_username_edit, #owt7_wp_password_edit").prop("required", true);
            } else {
                fields.slideUp(200);
                jQuery("#owt7_wp_username_edit, #owt7_wp_password_edit").prop("required", false).val("");
            }
        }
    }
    jQuery(document).on("change", "#owt7_save_as_wp_user_edit", owt7_lms_toggle_wp_creds_fields_edit);
    jQuery(function() { owt7_lms_toggle_wp_creds_fields_edit(); });

    /**
     * Edit User: Eye toggles change-password field visibility (show / hide typed password)
     */
    jQuery(document).on("click", "#owt7_wp_password_toggle_btn", function() {
        var $input = jQuery("#owt7_wp_edit_password");
        var $icon = jQuery(this).find(".dashicons");
        if ($input.length) {
            if ($input.attr("type") === "password") {
                $input.attr("type", "text");
                $icon.removeClass("dashicons-visibility").addClass("dashicons-hidden");
            } else {
                $input.attr("type", "password");
                $icon.removeClass("dashicons-hidden").addClass("dashicons-visibility");
            }
        }
    });

    /**
     * Download Sample CSV/Excel
     */
    jQuery(document).on("click", ".owt7_download_format", function() {
        var btn = jQuery(this);
        var tile = btn.closest(".owt7_csv_download_tile");
        var file_type = tile.data("file-type");
        var format = btn.data("format");
        if (!file_type || !format) return;

        function removeLoader() {
            jQuery(".owt7-lms").removeClass("owt7_loader");
        }
        jQuery(".owt7-lms").addClass("owt7_loader");

        if (format === "xlsx") {
            var url = owt7_library.ajaxurl + "?action=owt_lib_handler&param=owt7_lms_download_sample_data&file_type=" + encodeURIComponent(file_type) + "&format=xlsx&owt7_lms_nonce=" + owt7_library.ajax_nonce;
            fetch(url, { credentials: "same-origin" })
                .then(function(response) {
                    var contentType = response.headers.get("content-type") || "";
                    if (contentType.indexOf("application/json") !== -1) {
                        return response.json().then(function(data) {
                            throw data;
                        });
                    }
                    return response.blob();
                })
                .then(function(blob) {
                    var link = document.createElement("a");
                    link.href = window.URL.createObjectURL(blob);
                    link.download = file_type + ".xlsx";
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    window.URL.revokeObjectURL(link.href);
                })
                .then(removeLoader, function(err) {
                    removeLoader();
                    var msg = (err && err.msg) ? err.msg : (owt7_library.messages && owt7_library.messages.download_failed ? owt7_library.messages.download_failed : "Download failed");
                    owt7_lms_toastr(msg, "error");
                });
            return;
        }

        var postdata = "file_type=" + file_type + "&format=csv&action=owt_lib_handler&param=owt7_lms_download_sample_data&owt7_lms_nonce=" + owt7_library.ajax_nonce;
        jQuery.post(owt7_library.ajaxurl, postdata, function(response) {
            var data = jQuery.parseJSON(response);
            if (data.sts == 1 && data.arr && data.arr.content) {
                try {
                    var bin = atob(data.arr.content);
                    var bytes = new Uint8Array(bin.length);
                    for (var i = 0; i < bin.length; i++) bytes[i] = bin.charCodeAt(i);
                    var blob = new Blob([bytes], { type: "text/csv;charset=utf-8" });
                    var link = document.createElement("a");
                    link.href = window.URL.createObjectURL(blob);
                    link.download = data.arr.filename || file_type + ".csv";
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    window.URL.revokeObjectURL(link.href);
                } catch (e) {
                    owt7_lms_toastr("Download failed", "error");
                }
            } else {
                owt7_lms_toastr(data.msg || "Download failed", 'error');
            }
        }).fail(function() {
            owt7_lms_toastr("Download failed", 'error');
        }).always(removeLoader);
    });

    /**
     * Copy to Clipboard
     */
    jQuery(document).on("click", "#owt7_lib_shortcode_copy", function() {
        var valueToCopy = jQuery(this).data("value");
        //console.log(valueToCopy);
        navigator.clipboard.writeText(valueToCopy);
        owt7_lms_toastr("Successfully, Shortcode Copied", "success");
    });

    /**
     * CSV Upload - Expected headers per module type (same format as test-data.json)
     */
    var owt7_lms_csv_expected_headers = {
        categories: ["name"],
        bookcases: ["name"],
        sections: ["bookcase_name", "name"],
        branches: ["name"],
        books: ["name", "category_name", "bookcase_name", "author_name", "publication_name", "publication_year", "publication_location", "amount", "cover_image", "isbn", "book_language", "book_pages", "stock_quantity", "status", "description"],
        users: ["register_from", "name", "email", "gender", "branch_name", "phone_no", "address_info", "status"]
    };

    var OWT7_LMS_CSV_MAX_ROWS = 200;

    /**
     * Parse CSV first line to get headers (normalized: trim, lowercase)
     */
    function owt7_lms_parse_csv_headers(csvText) {
        var firstLine = csvText.split("\n")[0];
        if (!firstLine) return [];
        var raw = firstLine.split(",").map(function(s) { return s.replace(/^["\s]+|["\s]+$/g, "").toLowerCase().trim(); });
        return raw;
    }

    /**
     * Validate file headers match selected module type
     */
    function owt7_lms_validate_csv_format(csvText, csvType) {
        var expected = owt7_lms_csv_expected_headers[csvType];
        if (!expected) return { valid: false, msg: "Invalid module type." };
        var actual = owt7_lms_parse_csv_headers(csvText);
        if (actual.length !== expected.length) {
            return { valid: false, msg: "CSV column count does not match the selected module. Expected " + expected.length + " columns for \"" + csvType + "\". Download a sample CSV for the correct format." };
        }
        for (var i = 0; i < expected.length; i++) {
            if (actual[i] !== expected[i]) {
                return { valid: false, msg: "CSV format does not match the selected module. Expected header \"" + expected[i] + "\" in column " + (i + 1) + ". Download a sample CSV for \"" + csvType + "\" for the correct format." };
            }
        }
        return { valid: true };
    }

    /**
     * Count data rows (excluding header)
     */
    function owt7_lms_count_csv_rows(csvText) {
        var lines = csvText.split("\n").filter(function(line) { return line.trim().length > 0; });
        return Math.max(0, lines.length - 1);
    }

    /**
     * Drag & Drop CSV upload zone
     */
    (function() {
        var dropzone = jQuery("#owt7_lms_csv_dropzone");
        var fileInput = jQuery("#owt7_upload_csv_file");
        var dropzoneContent = jQuery("#owt7_lms_dropzone_content");
        var fileInfo = jQuery("#owt7_lms_dropzone_file_info");
        var fileNameEl = jQuery("#owt7_lms_file_name");
        var removeBtn = jQuery("#owt7_lms_remove_file");

        if (!dropzone.length) return;

        function isCsvOrExcelFile(file) {
            var name = (file.name || "").toLowerCase();
            var type = (file.type || "").toLowerCase();
            return name.endsWith(".csv") || name.endsWith(".xlsx") || name.endsWith(".xls") ||
                type === "text/csv" || type === "application/csv" || type === "text/plain" ||
                type === "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" || type === "application/vnd.ms-excel";
        }

        function clearFile() {
            fileInput.val("");
            fileInfo.hide();
            dropzoneContent.show();
            dropzone.removeClass("owt7_lms_dropzone_error");
        }

        function setFile(file) {
            if (!file) { clearFile(); return; }
            if (!isCsvOrExcelFile(file)) {
                dropzone.addClass("owt7_lms_dropzone_error");
                owt7_lms_toastr("Only CSV or Excel (.csv, .xlsx, .xls) files are allowed.", "error");
                return;
            }
            dropzone.removeClass("owt7_lms_dropzone_error");
            var dt = new DataTransfer();
            dt.items.add(file);
            fileInput[0].files = dt.files;
            fileNameEl.text(file.name);
            dropzoneContent.hide();
            fileInfo.show();
        }

        dropzone.on("click", function(e) {
            if (!jQuery(e.target).closest(".owt7_lms_remove_file").length) {
                fileInput[0].click();
            }
        });

        fileInput.on("change", function() {
            var f = this.files[0];
            if (f) setFile(f); else clearFile();
        });

        removeBtn.on("click", function(e) {
            e.stopPropagation();
            clearFile();
        });

        dropzone.on("dragover dragenter", function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropzone.addClass("owt7_lms_dropzone_over");
        });

        dropzone.on("dragleave dragend drop", function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropzone.removeClass("owt7_lms_dropzone_over");
        });

        dropzone.on("drop", function(e) {
            var files = e.originalEvent.dataTransfer && e.originalEvent.dataTransfer.files;
            if (files && files.length) setFile(files[0]);
        });
    })();

    /**
     * Read CSV or Excel file and submit (CSV: validate client-side; Excel: send file for server-side validation)
     */
    jQuery('#owt7_lms_upload_form').on('submit', function(e) {
        e.preventDefault();

        var $form = jQuery(this);
        var $submitBtn = $form.find('button[type="submit"]');
        function resetSubmitButton() {
            $submitBtn.prop("disabled", false).text($submitBtn.data("original-text") || "Submit & Save").css("cursor", "pointer");
        }
        function showBodyLoader() {
            jQuery('.owt7-lms').addClass('owt7_loader');
        }
        function hideBodyLoader() {
            jQuery('.owt7-lms').removeClass('owt7_loader');
        }

        var fileToUpload = jQuery('#owt7_upload_csv_file')[0].files[0];
        var csvType = jQuery("#owt7_dd_upload_csv_type").val();

        if (!fileToUpload) {
            owt7_lms_toastr("Please choose or drop a CSV or Excel file first.", "error");
            resetSubmitButton();
            return;
        }
        if (!csvType) {
            owt7_lms_toastr("Please select a module (e.g. Categories, Books) before uploading.", "error");
            resetSubmitButton();
            return;
        }

        showBodyLoader();
        $submitBtn.prop("disabled", true).text("Processing...").css("cursor", "progress");

        var fileName = (fileToUpload.name || "").toLowerCase();
        var isExcel = fileName.endsWith(".xlsx") || fileName.endsWith(".xls");

        if (isExcel) {
            var formData = new FormData();
            formData.append("action", "owt_lib_handler");
            formData.append("param", "owt7_lms_upload_form");
            formData.append("owt7_lms_nonce", owt7_library.ajax_nonce);
            formData.append("csvType", csvType);
            formData.append("owt7_upload_csv_file", fileToUpload);

            jQuery.ajax({
                url: owt7_library.ajaxurl,
                type: "post",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    var data = jQuery.parseJSON(response);
                    if (data.sts == 1) {
                        owt7_lms_toastr(data.msg, "success");
                        jQuery("#owt7_lms_remove_file").trigger("click");
                    } else {
                        owt7_lms_toastr(data.msg, "error");
                    }
                    hideBodyLoader();
                    resetSubmitButton();
                },
                error: function() {
                    owt7_lms_toastr("Upload failed. Please try again.", "error");
                    hideBodyLoader();
                    resetSubmitButton();
                }
            });
            return;
        }

        var csvReader = new FileReader();
        csvReader.onload = function(event) {
            var csvData = event.target.result;

            var formatCheck = owt7_lms_validate_csv_format(csvData, csvType);
            if (!formatCheck.valid) {
                owt7_lms_toastr(formatCheck.msg, "error");
                hideBodyLoader();
                resetSubmitButton();
                return;
            }

            var rowCount = owt7_lms_count_csv_rows(csvData);
            if (rowCount === 0) {
                owt7_lms_toastr("The file has no data rows. Add at least one row below the header.", "error");
                hideBodyLoader();
                resetSubmitButton();
                return;
            }

            jQuery.ajax({
                url: owt7_library.ajaxurl,
                type: "post",
                data: {
                    csvData: csvData,
                    csvType: csvType,
                    action: "owt_lib_handler",
                    param: "owt7_lms_upload_form",
                    owt7_lms_nonce: owt7_library.ajax_nonce
                },
                success: function(response) {
                    var data = jQuery.parseJSON(response);
                    if (data.sts == 1) {
                        owt7_lms_toastr(data.msg, "success");
                        jQuery("#owt7_lms_remove_file").trigger("click");
                    } else {
                        owt7_lms_toastr(data.msg, "error");
                    }
                    hideBodyLoader();
                    resetSubmitButton();
                },
                error: function() {
                    owt7_lms_toastr("Upload failed. Please try again.", "error");
                    hideBodyLoader();
                    resetSubmitButton();
                }
            });
        };
        csvReader.readAsText(fileToUpload);
    });

    /** 
     * Restore: LMS db version restore
     */
    jQuery(document).on("click", ".owt7_lms_btn_restore_db", function() {
        var backup_id = jQuery(this).attr("data-id");
        owt7_lms_confirm("Are you sure want to Restore LMS data backup?").then(function(confirmed) {
            if (confirmed) {
                var postdata = "backup_id=" + backup_id + "&action=owt_lib_handler&param=owt7_lms_backup_restore&owt7_lms_nonce=" + owt7_library.ajax_nonce;
                jQuery.post(owt7_library.ajaxurl, postdata, function(response) {
                    var data = jQuery.parseJSON(response);
                    if (data.sts == 1) {
                        owt7_lms_toastr(data.msg, "success");
                    } else {
                        owt7_lms_toastr(data.msg, 'error');
                    }
                });
            }
        });
    });

    /**
     * Deactivate Click of LMS
     */
    var deactivateModal = jQuery("#owt7_lms_deactivate_modal");
    var pluginBasename = (typeof owt7_library !== "undefined" && owt7_library.plugin_basename) ? owt7_library.plugin_basename : "";

    function getLmsDeactivateLink() {
        if (!pluginBasename) {
            return jQuery("#deactivate-library-management-system-basic-pro, #deactivate-library-management-system").first();
        }

        var encodedPluginBasename = encodeURIComponent(pluginBasename);

        return jQuery("a").filter(function() {
            var href = jQuery(this).attr("href") || "";
            if (href.indexOf("action=deactivate") === -1) {
                return false;
            }

            return href.indexOf("plugin=" + pluginBasename) !== -1 || href.indexOf("plugin=" + encodedPluginBasename) !== -1;
        }).first();
    }

    jQuery(document).on("click", "a", function(event) {
        var $deactivateLink = jQuery(this);
        var $targetDeactivateLink = getLmsDeactivateLink();

        if (!$targetDeactivateLink.length || !$deactivateLink.is($targetDeactivateLink)) {
            return;
        }

        var licenseVerified = typeof owt7_library !== 'undefined' && owt7_library.license_verified;
        if (!licenseVerified) {
            return;
        }
        event.preventDefault();
        jQuery("#owt7_lms_deactivate_link").val($deactivateLink.attr("href"));
        deactivateModal.show();
        jQuery('body').addClass('owt7-lms-modal-open');
    });

    /**
     * Modal Close Button Action
     */
    jQuery('#owt7_lms_deactivate_modal .close, #owt7_lms_deactivate_modal .owt7-lms-deactivate-cancel').on('click', function() {
        deactivateModal.hide();
        jQuery('body').removeClass('owt7-lms-modal-open');
    });

    /**
     * Deactivate Data Backup
     */
    jQuery(`
        #owt7_lms_deactivate_data_settings
	`).validate({
        submitHandler: function() {
            formID = "#owt7_lms_deactivate_data_settings";
            var $submitBtn = jQuery(formID).find("button[type='submit']");
            $submitBtn.prop("disabled", true).text('Processing...').css("cursor", "progress");
            jQuery('body').addClass('owt7_loader');
            var formdata = jQuery(formID).serialize();
            var postdata = formdata + "&action=owt_lib_handler&param=owt7_lms_deactivate_data_backup";
            jQuery.post(owt7_library.ajaxurl, postdata)
                .done(function(response) {
                    var data = typeof response === "string" ? (function() { try { return JSON.parse(response); } catch (e) { return {}; } })() : (response || {});
                    if (data.sts == 1) {
                        var fallbackDeactivateLink = getLmsDeactivateLink().attr("href") || "";
                        window.location.href = jQuery('#owt7_lms_deactivate_link').val() || fallbackDeactivateLink;
                    } else {
                        jQuery('body').removeClass('owt7_loader');
                        $submitBtn.prop("disabled", false).text('Deactivate Now').css("cursor", "pointer");
                        owt7_lms_toastr(data.msg, 'error');
                    }
                })
                .fail(function() {
                    jQuery('body').removeClass('owt7_loader');
                    $submitBtn.prop("disabled", false).text('Deactivate Now').css("cursor", "pointer");
                    owt7_lms_toastr('Request failed. Please try again.', 'error');
                });
        }
    });

    /**
     * Bulk Action: Checkboxes
     */
    jQuery('#owt7_lms_master_chk_btn').click(function() {
        jQuery('.owt7_lms_chkbox').prop('checked', this.checked);
    });

    jQuery('.owt7_lms_chkbox').click(function() {
        jQuery('#owt7_lms_master_chk_btn').prop('checked', jQuery('.owt7_lms_chkbox:checked').length === jQuery('.owt7_lms_chkbox').length);
    });

    /**
     * Bulk Action: Apply
     */
    jQuery(document).on("click", "#owt7_lms_btn_bulk_action_apply", function() {
        var selectorId = jQuery(this).data("id");
        var operationType = jQuery("#" + selectorId).val();
        var moduleType = jQuery("#" + selectorId).data("module");
        var checkedDataValues = [];
        jQuery('.owt7_lms_chkbox:checked').each(function() {
            checkedDataValues.push(jQuery(this).data("id"));
        });
        if (checkedDataValues.length > 0) {
            var postdata = "operation=" + operationType + "&module=" + moduleType + "&rows=" + checkedDataValues + "&action=owt_lib_handler&param=owt7_lms_bulk_action&owt7_lms_nonce=" + owt7_library.ajax_nonce;
            jQuery.post(owt7_library.ajaxurl, postdata, function(response) {
                var data = jQuery.parseJSON(response);
                if (data.sts == 1) {
                    owt7_lms_toastr(data.msg, "success");
                } else {
                    owt7_lms_toastr(data.msg, 'error');
                }
            });
        } else {
            owt7_lms_toastr("Please select rows", 'error');
        }
    });

    /**
     * Clone Action
     */
    jQuery(document).on("click", ".owt7_lms_clone_data", function() {
        var dataId = jQuery(this).data("id");
        var moduleType = jQuery(this).data("module");
        owt7_lms_confirm("Are you sure want to clone this row?").then(function(confirmed) {
            if (confirmed) {
                var postdata = "dataId=" + dataId + "&module=" + moduleType + "&action=owt_lib_handler&param=owt7_lms_clone_data&owt7_lms_nonce=" + owt7_library.ajax_nonce;
                jQuery.post(owt7_library.ajaxurl, postdata, function(response) {
                    var data = jQuery.parseJSON(response);
                    if (data.sts == 1) {
                        owt7_lms_toastr(data.msg, "success");
                    } else {
                        owt7_lms_toastr(data.msg, 'error');
                    }
                });
            }
        });
    });

    /**
     * Book Return on Click
     */
    jQuery(document).on("click", ".owt7_lms_btn_approve_reject", function() {
        var dataType = jQuery(this).data("type");
        var dataModule = jQuery(this).data("module");
        var dataId = jQuery(this).data("id");
        owt7_lms_confirm("Are you sure want to " + dataType + "?").then(function(confirmed) {
            if (confirmed) {
                var postdata = "data_id=" + dataId + "&module=" + dataModule + "&type=" + dataType + "&action=owt_lib_handler&param=owt7_lms_checkout_approve_reject&owt7_lms_nonce=" + owt7_library.ajax_nonce;
                jQuery.post(owt7_library.ajaxurl, postdata, function(response) {
                    var data = jQuery.parseJSON(response);
                    if (data.sts == 1) {
                        owt7_lms_toastr(data.msg, "success");
                    } else {
                        owt7_lms_toastr(data.msg, 'error');
                    }
                });
            }
        });
    });

    /**
     * Remove Product from WooCom
     */
    jQuery(document).on("click", ".remove-woocom-product", function() {
        var bookId = jQuery(this).data("id");
        owt7_lms_confirm("Are you sure want to remove this book from store?").then(function(confirmed) {
            if (confirmed) {
                var postdata = "bookId=" + bookId + "&action=owt_lib_handler&param=owt7_lms_remove_woocom_product&owt7_lms_nonce=" + owt7_library.ajax_nonce;
                jQuery.post(owt7_library.ajaxurl, postdata, function(response) {
                    var data = jQuery.parseJSON(response);
                    if (data.sts == 1) {
                        owt7_lms_toastr(data.msg, "success");
                    } else {
                        owt7_lms_toastr(data.msg, 'error');
                    }
                });
            }
        });
    });

    /**
     * Sync WP Users to LMS – open modal, load role-wise users, Check All, Submit
     */
    var owt7_lms_sync_wp_users_modal = jQuery('#owt7_lms_mdl_sync_wp_users');
    var owt7_lms_sync_wp_users_content = jQuery('#owt7_lms_sync_wp_users_content');
    var owt7_lms_sync_wp_users_list = jQuery('#owt7_lms_sync_wp_users_list');
    var owt7_lms_sync_wp_users_empty = jQuery('#owt7_lms_sync_wp_users_empty');
    var owt7_lms_sync_wp_users_check_all = jQuery('#owt7_lms_sync_wp_users_check_all');
    var owt7_lms_sync_wp_users_submit_btn = jQuery('#owt7_lms_sync_wp_users_submit_btn');

    jQuery(document).on("click", ".btn-sync-wp-users", function() {
        if (!owt7_lms_sync_wp_users_modal.length) return;
        owt7_lms_sync_wp_users_modal.show();
        jQuery('body').addClass('owt7-lms-modal-open');
        owt7_lms_sync_wp_users_empty.hide();
        owt7_lms_sync_wp_users_list.empty();
        owt7_lms_sync_wp_users_check_all.prop('checked', false);

        var postdata = "action=owt_lib_handler&param=owt7_lms_get_wp_users_for_sync&owt7_lms_nonce=" + owt7_library.ajax_nonce;
        jQuery.post(owt7_library.ajaxurl, postdata, function(response) {
            var data = typeof response === 'string' ? jQuery.parseJSON(response) : response;
            if (data.sts !== 1 || !data.arr || !data.arr.roles) {
                owt7_lms_sync_wp_users_content.hide();
                owt7_lms_sync_wp_users_empty.show().text(data.msg || 'Unable to load users.');
                return;
            }
            var roles = data.arr.roles;
            var roleKeys = Object.keys(roles);
            if (roleKeys.length === 0) {
                owt7_lms_sync_wp_users_content.hide();
                owt7_lms_sync_wp_users_empty.show();
                return;
            }
            owt7_lms_sync_wp_users_empty.hide();
            owt7_lms_sync_wp_users_content.show();
            roleKeys.forEach(function(roleSlug) {
                var roleData = roles[roleSlug];
                var roleName = roleData.name || roleSlug;
                var users = roleData.users || [];
                var section = jQuery('<div class="owt7-lms-sync-wp-users-role-section"></div>');
                section.append(jQuery('<div class="owt7-lms-sync-wp-users-role-title"></div>').text(roleName));
                var ul = jQuery('<ul class="owt7-lms-sync-wp-users-role-list"></ul>');
                users.forEach(function(u) {
                    var li = jQuery('<li class="owt7-lms-sync-wp-users-user-row"></li>');
                    var label = jQuery('<label></label>');
                    label.append(jQuery('<input type="checkbox" class="owt7-lms-sync-wp-user-chk" name="wp_user_ids[]" value="' + u.id + '" />'));
                    label.append(document.createTextNode(u.display_name + ' (' + (u.email || '') + ')'));
                    li.append(label);
                    ul.append(li);
                });
                section.append(ul);
                owt7_lms_sync_wp_users_list.append(section);
            });

            owt7_lms_sync_wp_users_check_all.off('change').on('change', function() {
                owt7_lms_sync_wp_users_list.find('.owt7-lms-sync-wp-user-chk').prop('checked', jQuery(this).prop('checked'));
            });
        });

        owt7_lms_sync_wp_users_submit_btn.off('click').on('click', function() {
            var ids = [];
            owt7_lms_sync_wp_users_list.find('.owt7-lms-sync-wp-user-chk:checked').each(function() {
                ids.push(jQuery(this).val());
            });
            if (ids.length === 0) {
                owt7_lms_toastr((typeof owt7_library !== 'undefined' && owt7_library.sync_select_one_msg) ? owt7_library.sync_select_one_msg : 'Please select at least one user to sync.', 'error');
                return;
            }
            var postdata = "action=owt_lib_handler&param=owt7_lms_sync_wp_users&owt7_lms_nonce=" + owt7_library.ajax_nonce;
            ids.forEach(function(id) { postdata += "&wp_user_ids[]=" + encodeURIComponent(id); });
            owt7_lms_sync_wp_users_submit_btn.prop('disabled', true);
            jQuery.post(owt7_library.ajaxurl, postdata, function(response) {
                var data = typeof response === 'string' ? jQuery.parseJSON(response) : response;
                owt7_lms_sync_wp_users_submit_btn.prop('disabled', false);
                if (data.sts == 1) {
                    owt7_lms_sync_wp_users_modal.hide();
                    jQuery('body').removeClass('owt7-lms-modal-open');
                    owt7_lms_toastr(data.msg, "success");
                } else {
                    owt7_lms_toastr(data.msg || 'Error syncing users.', 'error');
                }
            });
        });
    });

    jQuery(document).on('click', '.owt7-lms-close-sync-wp-users-modal', function() {
        if (owt7_lms_sync_wp_users_modal.length) {
            owt7_lms_sync_wp_users_modal.hide();
            jQuery('body').removeClass('owt7-lms-modal-open');
        }
    });
});

/**
 * SweetAlert2 confirmation dialog (replaces native confirm)
 * @param {string} message - Confirmation message text
 * @param {string} [title] - Optional dialog title
 * @returns {Promise<boolean>} - Resolves with true if confirmed, false if cancelled
 */
function owt7_lms_confirm(message, title) {
    return Swal.fire({
        title: title || 'Are you sure?',
        text: message,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes',
        customClass: { container: 'owt7-lms-swal-above-modal' }
    }).then(function(result) {
        return result.isConfirmed === true;
    });
}

/**
 * Activity Notification
 */
function owt7_lms_toastr(message, type) {
    if (type == "success") {
        toastr.success(message, owt7_library.messages.message_3);
        setTimeout(function() {
            location.reload();
        }, 2000);
    } else if (type == "error") {
        toastr.error(message, owt7_library.messages.message_4)
    }
}

/**
 *jQuery function to convert the input text to Title Case
 */
function owt7_lms_toTitleCase(str) {
    return str.replace(/\b\w+/g, function(txt) {
        return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
    });
}

/**
 * Generate numeric ID string: first digit 1-9, remaining digits 0-9.
 * Legacy; prefer owt7_lms_timestampSuffix7 for new Book/User IDs.
 */
function owt7_lms_generateNumericId(length) {
    if (length < 1) return "";
    var result = String(1 + Math.floor(Math.random() * 9));
    for (var i = 1; i < length; i++) {
        result += String(Math.floor(Math.random() * 10));
    }
    return result;
}

function owt7_lms_generateRandomString(length) {
    var chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    var result = '';
    for (var i = 0; i < length; i++) {
        result += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    return result;
}

/* ==========================================================================
   RECEIPT GENERATION
   ========================================================================== */

/**
 * Draw two-column label/value table rows onto the jsPDF document.
 * Returns the updated Y position after the last row.
 */
/**
 * Draw plain label/value rows – no background fills, thin border only on the
 * outer rectangle of each row so the layout stays clean.
 */
function owt7_lms_draw_receipt_rows(doc, rows, x, y, totalW) {
    var rowH   = 7;
    var labelW = Math.round(totalW * 0.40);

    rows.forEach(function (row) {
        // Bottom border line only (clean separation)
        doc.setDrawColor(210, 210, 210);
        doc.setLineWidth(0.2);
        doc.line(x, y + rowH, x + totalW, y + rowH);

        doc.setFontSize(8.5);
        doc.setFont("helvetica", "bold");
        doc.setTextColor(80, 80, 80);
        doc.text(String(row[0]) + ":", x + 2, y + 5);

        doc.setFont("helvetica", "normal");
        doc.setTextColor(20, 20, 20);
        var val = (row[1] !== null && row[1] !== undefined && String(row[1]) !== "") ? String(row[1]) : "\u2014";
        doc.text(val, x + labelW, y + 5);

        y += rowH;
    });
    return y;
}

/**
 * Build a clean, section-only receipt PDF.
 * Sections: Book Information | Borrower Information | Borrow Details
 * No header/footer bands, no row background colours – only section title bars.
 */
function owt7_lms_build_receipt_pdf(d) {
    if (typeof window.jspdf === "undefined" || typeof window.jspdf.jsPDF === "undefined") {
        return null;
    }
    var doc = new window.jspdf.jsPDF({ orientation: "portrait", unit: "mm", format: "a4" });

    var pageW    = doc.internal.pageSize.getWidth();
    var margin   = 14;
    var contentW = pageW - margin * 2;
    var y        = 14;

    /* -- DOCUMENT TITLE -- */
    doc.setTextColor(20, 20, 20);
    doc.setFontSize(13);
    doc.setFont("helvetica", "bold");
    doc.text("Library Management System (Fine Receipt)", pageW / 2, y, { align: "center" });
    y += 7;

    doc.setFontSize(8.5);
    doc.setFont("helvetica", "normal");
    doc.setTextColor(90, 90, 90);
    doc.text(
        "Please find below the details of the fine raised against the returned book. Kindly retain this receipt for your records.",
        pageW / 2, y, { align: "center", maxWidth: contentW }
    );
    y += 7;

    /* Separator */
    doc.setDrawColor(180, 180, 180);
    doc.setLineWidth(0.4);
    doc.line(margin, y, margin + contentW, y);
    y += 7;

    /* Neutral dark colour used for all section title bars */
    var titleBg  = [50, 50, 50];

    /* -- SECTION HELPER -- */
    function drawSection(title, rows) {
        /* Section title bar */
        doc.setFillColor(titleBg[0], titleBg[1], titleBg[2]);
        doc.rect(margin, y, contentW, 7, "F");
        doc.setTextColor(255, 255, 255);
        doc.setFontSize(9);
        doc.setFont("helvetica", "bold");
        doc.text(title, margin + 3, y + 5);
        y += 9;

        /* Rows */
        y = owt7_lms_draw_receipt_rows(doc, rows, margin, y, contentW);
        y += 7;
    }

    /* -- 1. BOOK INFORMATION -- */
    drawSection("BOOK INFORMATION", [
        ["Book ID",       d.book_id],
        ["Book Name",     d.book_name],
        ["Category",      d.book_category],
        ["Accession No.", d.book_accession],
        ["Author",        d.book_author],
        ["Publisher",     d.book_publisher],
        ["Year",          d.book_pub_year],
        ["ISBN",          d.book_isbn]
    ]);

    /* -- 2. BORROWER INFORMATION -- */
    drawSection("BORROWER INFORMATION", [
        ["LMS User ID", d.borrower_user_id],
        ["Full Name",   d.borrower_name],
        ["Email",       d.borrower_email],
        ["Phone",       d.borrower_phone],
        ["Branch",      d.borrower_branch]
    ]);

    /* -- 3. BORROW DETAILS -- */
    drawSection("BORROW DETAILS", [
        ["Borrow ID",        d.borrow_id],
        ["Return ID",        d.return_id],
        ["Issue Date",       d.issued_on],
        ["Return Date",      d.return_date],
        ["Days Allowed",     d.total_days + " days"],
        ["Extra Days",       d.extra_days + " days"],
        ["Return Condition", d.return_condition],
        ["Fine Type",        d.fine_type],
        ["Total Fine",       d.fine_amount + " " + (d.currency || "")],
        ["Fine Status",      d.fine_status]
    ]);

    return doc.output("datauristring");
}

/**
 * Download Receipt – fetch data, build PDF client-side, trigger direct download.
 * No server save step.
 */
jQuery(document).on("click", ".owt7_lms_download_receipt_btn", function () {
    var $btn       = jQuery(this);
    var returnDbId = $btn.attr("data-return-db-id") || "";

    if (!returnDbId || returnDbId === "0") {
        toastr.error("Invalid return record.", "Error");
        return;
    }

    $btn.addClass("owt7-lms-receipt-loading").prop("disabled", true);
    var origHtml = $btn.html();
    $btn.html('<span class="dashicons dashicons-update owt7-spin" aria-hidden="true"></span> Downloading\u2026');

    jQuery.post(
        (typeof owt7_library !== "undefined" ? owt7_library.ajaxurl : ""),
        {
            action         : "owt_lib_handler",
            param          : "owt7_lms_get_receipt_data",
            return_id      : returnDbId,
            owt7_lms_nonce : (typeof owt7_library !== "undefined" ? owt7_library.ajax_nonce : "")
        },
        function (raw) {
            var data = typeof raw === "string"
                ? (function () { try { return JSON.parse(raw); } catch (e) { return {}; } }())
                : raw;

            $btn.removeClass("owt7-lms-receipt-loading").prop("disabled", false).html(origHtml);

            if (data.sts !== 1 || !data.arr) {
                toastr.error(data.msg || "Failed to load receipt data.", "Error");
                return;
            }

            var pdfDataUri = owt7_lms_build_receipt_pdf(data.arr);
            if (!pdfDataUri) {
                toastr.error("PDF library not available.", "Error");
                return;
            }

            var link = document.createElement("a");
            link.href     = pdfDataUri;
            link.download = "receipt_" + (data.arr.return_id || "lms") + ".pdf";
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    ).fail(function () {
        $btn.removeClass("owt7-lms-receipt-loading").prop("disabled", false).html(origHtml);
        toastr.error("Server error while fetching receipt data.", "Error");
    });
});
