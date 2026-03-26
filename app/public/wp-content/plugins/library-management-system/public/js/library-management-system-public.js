jQuery(function() {

    // Book detail modal: open on View click, load content via AJAX
    var $bookDetailModal = jQuery("#owt7_lms_mdl_book_detail");
    var $bookDetailContent = jQuery("#owt7_lms_book_detail_content");

    jQuery(document).on("click", ".owt7_lms_view_book_modal", function(e) {
        e.preventDefault();
        var bookId = jQuery(this).data("book-id");
        if (!bookId) return;
        $bookDetailContent.html('<div class="owt7-lms-book-detail-loading"><span class="owt7-lms-loader-spinner"></span></div>');
        $bookDetailModal.show().attr("aria-hidden", "false");
        jQuery("body").addClass("owt7-lms-modal-open");
        var postdata = "action=owt7_front_handler&param=owt7_lms_get_book_detail_modal&book_id=" + encodeURIComponent(bookId) + "&owt7_lms_nonce=" + owt7_library.ajax_nonce;
        jQuery.post(owt7_library.ajaxurl, postdata, function(response) {
            var data = typeof response === "object" ? response : (function() { try { return JSON.parse(response); } catch (r) { return {}; } })();
            if (data.sts === 1 && data.arr && data.arr.html) {
                $bookDetailContent.html(data.arr.html);
            } else {
                $bookDetailContent.html("<p class=\"owt7-lms-book-detail-error\">" + (data.msg || "Could not load book details.") + "</p>");
            }
        }).fail(function() {
            $bookDetailContent.html("<p class=\"owt7-lms-book-detail-error\">Request failed. Please try again.</p>");
        });
    });

    function owt7_lms_close_book_detail_modal() {
        $bookDetailModal.hide().attr("aria-hidden", "true");
        jQuery("body").removeClass("owt7-lms-modal-open");
    }

    jQuery(document).on("click", "#owt7_lms_mdl_book_detail .owt7-lms-modal-close, #owt7_lms_mdl_book_detail .owt7-lms-modal-overlay", function() {
        owt7_lms_close_book_detail_modal();
    });

    jQuery(document).on("keydown", function(e) {
        if (e.key === "Escape" && $bookDetailModal.is(":visible")) {
            owt7_lms_close_book_detail_modal();
        }
    });

    function owt7_lms_public_catalogue_ajax(queryString) {
        var $loader = jQuery("#owt7_lms_books_loader");
        $loader.addClass("is-loading").attr("aria-hidden", "false");

        var requestData = queryString + "&action=owt7_front_handler&param=owt7_lms_front_category_filter&owt7_lms_nonce=" + encodeURIComponent(owt7_library.ajax_nonce);
        jQuery.post(owt7_library.ajaxurl, requestData, function(response) {
            var data = typeof response === "object" ? response : (function() { try { return JSON.parse(response); } catch (e) { return {}; } })();
            if (data.sts === 1 && data.arr && data.arr.template) {
                jQuery("#owt7_lms_books").html(data.arr.template);
                if (data.arr.url && window.history && window.history.replaceState) {
                    window.history.replaceState({}, "", data.arr.url);
                }
            } else {
                jQuery("#owt7_lms_books").html("<i>-- No Book Found --</i>");
            }
        }).always(function() {
            $loader.removeClass("is-loading").attr("aria-hidden", "true");
        });
    }

    jQuery(document).on("submit", ".owt7-lms-public-filter-form", function(e) {
        e.preventDefault();
        var queryString = jQuery(this).serialize();
        owt7_lms_public_catalogue_ajax(queryString);
    });

    jQuery(document).on("click", ".owt7-lms-public-filter-form .owt7-lms-filter-reset-btn", function(e) {
        e.preventDefault();
        var $form = jQuery(this).closest(".owt7-lms-public-filter-form");
        $form.find("select").each(function() {
            this.selectedIndex = 0;
        });
        $form.find("input[type='text']").val("");
        owt7_lms_public_catalogue_ajax("");
        if (window.history && window.history.replaceState) {
            window.history.replaceState({}, "", this.href);
        }
    });

    jQuery(document).on("click", "#owt7_lms_books .pagination a", function(e) {
        e.preventDefault();
        var href = jQuery(this).attr("href") || "";
        if (!href) return;
        var url = new URL(href, window.location.origin);
        owt7_lms_public_catalogue_ajax(url.search.replace(/^\?/, ""));
    });

    // User Login
    jQuery(document).on('click', '.owt7_lms_do_user_login', function() {
        var currentPageURL = window.location.href;
        var encodedURL = encodeURIComponent(currentPageURL);
        var postdata = "encodedURL=" + encodedURL + "&action=owt7_front_handler&param=owt7_lms_do_user_login&owt7_lms_nonce=" + owt7_library.ajax_nonce;
        jQuery.post(owt7_library.ajaxurl, postdata, function(response) {
            var data = jQuery.parseJSON(response);
            window.location.href = data.arr.login_url + "?redirect_to=" + encodedURL;
        });
    });

    // User Checkout / Request Checkout
    jQuery(document).on('click', '.owt7_lms_user_do_checkout', function() {
        var bookId = jQuery(this).data("id");
        owt7_lms_confirm("Are you sure want to checkout?").then(function(confirmed) {
            if (confirmed) {
                var postdata = "book_id=" + bookId + "&action=owt7_front_handler&param=owt7_lms_do_user_checkout&owt7_lms_nonce=" + owt7_library.ajax_nonce;
                jQuery.post(owt7_library.ajaxurl, postdata, function(response) {
                    var data = jQuery.parseJSON(response);
                    if (data.sts == 1) {
                        owt7_lms_toastr(data.msg, "success");
                    } else {
                        owt7_lms_toastr(data.msg, "error");
                    }
                });
            }
        });
    });

    // User Return: open Return Status modal, then submit with condition and remark
    jQuery(document).on('click', '.owt7_lms_user_do_return', function(e) {
        e.preventDefault();
        var bookId = jQuery(this).data("id");
        if (!bookId) return;
        jQuery("#owt7_lms_mdl_user_return_status").data("user-return-book-id", bookId).show();
        jQuery("body").addClass("owt7-lms-modal-open");
        owt7_lms_public_user_return_update_fine();
    });

    function owt7_lms_public_user_return_update_fine() {
        var bookId = jQuery("#owt7_lms_mdl_user_return_status").data("user-return-book-id");
        if (!bookId) return;
        var condition = jQuery("#owt7_user_return_condition").val() || "normal_return";
        var postdata = "action=owt7_front_handler&param=owt7_lms_user_return_fine_preview&owt7_lms_nonce=" + owt7_library.ajax_nonce + "&book_id=" + encodeURIComponent(bookId) + "&owt7_return_condition=" + encodeURIComponent(condition);
        jQuery.post(owt7_library.ajaxurl, postdata, function(response) {
            var data = typeof response === "object" ? response : (function() { try { return JSON.parse(response); } catch (e) { return {}; } })();
            var currency = (data.arr && data.arr.currency) ? data.arr.currency : "";
            var total = (data.arr && typeof data.arr.total_fine !== "undefined") ? parseFloat(data.arr.total_fine) : 0;
            jQuery("#owt7_user_return_total_fine_display").text(total + " " + currency);
        }).fail(function() {
            jQuery("#owt7_user_return_total_fine_display").text("—");
        });
    }

    jQuery(document).on("change", "#owt7_user_return_condition", function() {
        owt7_lms_public_user_return_update_fine();
    });

    jQuery(document).on("click", "#owt7_user_return_status_modal_submit", function() {
        var bookId = jQuery("#owt7_lms_mdl_user_return_status").data("user-return-book-id");
        if (!bookId) return;
        var condition = jQuery("#owt7_user_return_condition").val() || "normal_return";
        var remark = jQuery("#owt7_user_return_remark").val() || "";
        var postdata = "action=owt7_front_handler&param=owt7_lms_do_user_return&owt7_lms_nonce=" + owt7_library.ajax_nonce + "&book_id=" + encodeURIComponent(bookId) + "&owt7_return_condition=" + encodeURIComponent(condition) + "&owt7_return_remark=" + encodeURIComponent(remark);
        jQuery.post(owt7_library.ajaxurl, postdata, function(response) {
            var data = typeof response === "object" ? response : (function() { try { return JSON.parse(response); } catch (e) { return {}; } })();
            jQuery("#owt7_lms_mdl_user_return_status").hide();
            jQuery("body").removeClass("owt7-lms-modal-open");
            if (data.sts == 1) {
                owt7_lms_toastr(data.msg, "success");
            } else {
                owt7_lms_toastr(data.msg || "Return failed.", "error");
            }
        }).fail(function() {
            owt7_lms_toastr("Request failed. Please try again.", "error");
        });
    });
});

/**
 * SweetAlert2 confirmation dialog
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
        confirmButtonText: 'Yes'
    }).then(function(result) {
        return result.isConfirmed === true;
    });
}

/**
 * Activity Notification
 */
function owt7_lms_toastr(message, type) {
    if (type == "success") {
        toastr.success(message, "Success");
        setTimeout(function() {
            location.reload();
        }, 3000);
    } else if (type == "error") {
        toastr.error(message, "Error")
    }
}

// Tabs
function openTab(evt, tabName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
        tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablink");
    for (i = 0; i < tablinks.length; i++) {
        tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";
}

/**
 * Column index for list filters (Book name, Author, Category)
 */
var owt7_lms_filter_col = { "book-name": 0, "author": 3, "category": 2 };

/**
 * Get text content from a table cell (strip HTML)
 */
function owt7_lms_cell_text(cell) {
    if (!cell) return "";
    var text = (cell.textContent || cell.innerText || "").trim();
    return text;
}

/**
 * Apply search and column filters to a list table
 */
function owt7_lms_apply_list_filters(container) {
    var $wrap = jQuery(container);
    var $table = $wrap.find(".owt7-lms-data-table");
    if (!$table.length) return;
    var searchVal = ($wrap.find(".owt7-lms-list-search").val() || "").toLowerCase();
    var filterVals = {};
    $wrap.find(".owt7-lms-list-filter").each(function() {
        var key = jQuery(this).data("filter");
        if (key) filterVals[key] = (jQuery(this).val() || "").toLowerCase();
    });
    var rows = $table.find("tbody tr");
    rows.each(function() {
        var $row = jQuery(this);
        var cells = $row.find("td");
        var matchSearch = true;
        if (searchVal) {
            matchSearch = false;
            for (var c = 0; c < cells.length; c++) {
                if (owt7_lms_cell_text(cells[c]).toLowerCase().indexOf(searchVal) !== -1) {
                    matchSearch = true;
                    break;
                }
            }
        }
        var matchFilters = true;
        jQuery.each(filterVals, function(key, val) {
            if (!val) return;
            var col = owt7_lms_filter_col[key];
            if (col === undefined) return;
            var cellText = owt7_lms_cell_text(cells[col]).toLowerCase();
            if (cellText.indexOf(val) === -1) matchFilters = false;
        });
        $row.toggle(matchSearch && matchFilters);
    });
}

/**
 * Bind list toolbar: search, filters, export
 */
function owt7_lms_init_list_toolbar() {
    jQuery(".owt7-lms-list-with-toolbar").each(function() {
        var container = this;
        var $container = jQuery(container);
        var $table = $container.find(".owt7-lms-data-table");
        if (!$table.length) return;

        // Search
        $container.find(".owt7-lms-list-search").on("input keyup", function() {
            owt7_lms_apply_list_filters(container);
        });
        // Column filters
        $container.find(".owt7-lms-list-filter").on("input keyup", function() {
            owt7_lms_apply_list_filters(container);
        });

        // Export CSV
        $container.find(".owt7-lms-export-csv").on("click", function() {
            var rows = [];
            $table.find("thead tr").each(function() {
                var row = [];
                jQuery(this).find("th").each(function() {
                    row.push(owt7_lms_cell_text(this));
                });
                rows.push(row);
            });
            $table.find("tbody tr:visible").each(function() {
                var row = [];
                jQuery(this).find("td").each(function() {
                    row.push(owt7_lms_cell_text(this));
                });
                rows.push(row);
            });
            var csv = rows.map(function(row) {
                return row.map(function(cell) {
                    var s = String(cell).replace(/"/g, '""');
                    return "\"" + s + "\"";
                }).join(",");
            }).join("\r\n");
            var blob = new Blob(["\uFEFF" + csv], { type: "text/csv;charset=utf-8;" });
            var url = URL.createObjectURL(blob);
            var a = document.createElement("a");
            a.href = url;
            a.download = "library-list-" + ($container.data("table-context") || "export") + ".csv";
            a.click();
            URL.revokeObjectURL(url);
        });

        // Export PDF (requires jsPDF + jspdf-autotable enqueued)
        $container.find(".owt7-lms-export-pdf").on("click", function() {
            var JsPDFConstructor = window.jsPDF || (window.jspdf && window.jspdf.jsPDF);
            if (typeof JsPDFConstructor === "undefined") {
                if (typeof owt7_library !== "undefined" && owt7_library.pdf_unsupported) {
                    alert(owt7_library.pdf_unsupported);
                } else {
                    alert("PDF export is not available.");
                }
                return;
            }
            var head = [];
            $table.find("thead tr").each(function() {
                var row = [];
                jQuery(this).find("th").each(function() {
                    row.push(owt7_lms_cell_text(this));
                });
                head.push(row);
            });
            var body = [];
            $table.find("tbody tr:visible").each(function() {
                var row = [];
                jQuery(this).find("td").each(function() {
                    row.push(owt7_lms_cell_text(this));
                });
                body.push(row);
            });
            try {
                var ctx = $container.data("table-context") || "export";
                var heading = (typeof owt7_library !== "undefined" && owt7_library.pdf_headings && owt7_library.pdf_headings[ctx])
                    ? owt7_library.pdf_headings[ctx]
                    : (ctx === "borrowed" ? "My Borrowed Books" : (ctx === "returned" ? "Books Returned" : "Library List"));
                var doc = new JsPDFConstructor({ orientation: "landscape", unit: "pt", format: "a4" });
                doc.setFontSize(16);
                doc.text(heading, 20, 22);
                doc.autoTable({
                    head: head,
                    body: body,
                    startY: 32,
                    styles: { fontSize: 8 },
                    margin: { left: 20, right: 20 }
                });
                doc.save("library-list-" + ctx + ".pdf");
            } catch (e) {
                alert("PDF export failed.");
            }
        });
    });
}

jQuery(function() {
    owt7_lms_init_list_toolbar();
});