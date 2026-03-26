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
            <div class="breadcrumb"> <?php _e('Library System', 'library-management-system'); ?> >> <span class="active"><?php _e('Upload CSV Data', 'library-management-system'); ?></span> </div>
            <div class="page-actions">
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=owt7_library_settings#data' ) ); ?>" class="btn">
                    <span class="dashicons dashicons-arrow-left-alt"></span>
                    <?php _e('Back', 'library-management-system'); ?>
                </a>
            </div>
        </div>

        <div class="page-container">

            <div class="page-title">
                <h2><?php _e('Upload CSV Data', 'library-management-system'); ?></h2>
            </div>

            <!-- Upload CSV Form -->
            <form class="owt7_lms_upload_form" id="owt7_lms_upload_form" action="javascript:void(0);" method="post" enctype="multipart/form-data">

                <?php wp_nonce_field( 'owt7_library_actions', 'owt7_lms_nonce' ); ?>

                <div class="form-row">
                    <!-- Module Type -->
                    <div class="form-group">
                        <label for="owt7_dd_upload_csv_type"><?php _e("Upload Data", "library-management-system"); ?> <span class="required">*</span></label>
                        <select required id="owt7_dd_upload_csv_type" name="owt7_dd_upload_csv_type">
                            <option value=""><?php _e("-- Select Module --", "library-management-system"); ?></option>
                            <option value="categories"><?php _e("Categories", "library-management-system"); ?></option>
                            <option value="bookcases"><?php _e("Bookcases", "library-management-system"); ?></option>
                            <option value="sections"><?php _e("Sections", "library-management-system"); ?></option>
                            <option value="books"><?php _e("Books", "library-management-system"); ?></option>
                            <option value="branches"><?php _e("Branches", "library-management-system"); ?></option>
                            <option value="users"><?php _e("Users", "library-management-system"); ?></option>
                        </select>
                    </div>
                    <!-- Upload File (Drag & Drop) -->
                    <div class="form-group owt7_lms_upload_file_wrap">
                        <label><?php _e("Choose CSV or Excel File", "library-management-system"); ?><span class="required">*</span></label>
                        <div class="owt7_lms_csv_dropzone" id="owt7_lms_csv_dropzone">
                            <input type="file" accept=".csv,text/csv,application/csv,.xlsx,.xls,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.ms-excel" id="owt7_upload_csv_file" name="owt7_upload_csv_file" class="owt7_lms_csv_file_input" hidden>
                            <div class="owt7_lms_dropzone_content" id="owt7_lms_dropzone_content">
                                <span class="dashicons dashicons-upload"></span>
                                <p class="owt7_lms_dropzone_text"><?php _e("Drag and drop your CSV or Excel file here", "library-management-system"); ?></p>
                                <p class="owt7_lms_dropzone_subtext"><?php _e("or click to browse", "library-management-system"); ?></p>
                            </div>
                            <div class="owt7_lms_dropzone_file_info" id="owt7_lms_dropzone_file_info" style="display:none;">
                                <span class="dashicons dashicons-media-spreadsheet"></span>
                                <span class="owt7_lms_file_name" id="owt7_lms_file_name"></span>
                                <button type="button" class="owt7_lms_remove_file" id="owt7_lms_remove_file" aria-label="<?php esc_attr_e('Remove file', 'library-management-system'); ?>">&times;</button>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="owt7_lms_upload_note" id="owt7_lms_upload_note">
                    <span class="dashicons dashicons-info"></span>
                    <p><?php echo wp_kses( sprintf( __( 'Upload up to <strong>%d rows</strong> per file (free version limit). Use the same column format as the sample CSV. Excel (.xlsx, .xls) uses the same columns as CSV. To add more, please upgrade.', 'library-management-system' ), defined( 'LIBMNS_FREE_VERSION_LIMIT' ) ? (int) LIBMNS_FREE_VERSION_LIMIT : 30 ), array( 'strong' => array() ) ); ?></p>
                </div>

                <div class="owt7_lms_upload_actions">
                    <button class="btn submit-save-btn" type="submit" data-original-text="<?php echo esc_attr( __( "Submit & Save", "library-management-system" ) ); ?>">
                        <?php _e("Submit & Save", "library-management-system"); ?>
                    </button>
                    <button type="button" class="btn btn-secondary" id="owt7_lms_download_sample_csv_modal" aria-label="<?php esc_attr_e('Download Sample CSV', 'library-management-system'); ?>">
                        <span class="dashicons dashicons-download"></span>
                        <?php _e("Download Sample CSV", "library-management-system"); ?>
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>


<div class="owt7_lms_modal_section">
    <?php
    ob_start();
    $fileName = "owt7_mdl_download_sample_csv";
    include_once LIBMNS_PLUGIN_DIR_PATH . "admin/views/settings/modals/{$fileName}.php";
    $template = ob_get_contents();
    ob_end_clean();
    echo $template;
    ?>
</div>
