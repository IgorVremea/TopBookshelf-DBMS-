<?php
/**
 * @link       https://onlinewebtutorblog.com
 * @since      3.5
 * @package    Library_Management_System
 * @subpackage Library_Management_System/admin/views/books
 * @copyright  Copyright (c) 2026, Online Web Tutor
 * @license    GPL-2.0+ https://www.gnu.org/licenses/gpl-2.0.html
 * @author     Online Web Tutor
 */
?>
<div class="owt7-lms owt7-lms-books">

    <div class="owt7_library_add_book">

        <div class="page-header">
            <div class="breadcrumb"> <?php _e("Library System", "library-management-system"); ?> >> <span class="active"><?php _e("Add New Book", "library-management-system"); ?></span> </div>
            <div class="page-actions">
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=owt7_library_books' ) ); ?>" class="btn"><span class="dashicons dashicons-arrow-left-alt"></span> <?php _e("Back", "library-management-system"); ?></a>
            </div>
        </div>

        <div class="page-container">

            <div class="page-title-row">
                <div class="page-title">
                    <?php if(isset($params['action'])){ ?> <h2><?php _e(ucfirst($params['action'])." Book", "library-management-system"); ?></h2>
                    <?php }else{ ?> <h2><?php _e("Add Book", "library-management-system"); ?></h2> <?php } ?>
                </div>
            </div>

            <form class="owt7_lms_book_form" id="owt7_lms_book_form" action="javascript:void(0);" method="post" accept-charset="UTF-8">

                <?php wp_nonce_field( 'owt7_library_actions', 'owt7_lms_nonce' ); ?>
                <input type="hidden" name="action_type"
                    value="<?php echo isset($params['action']) && !empty($params['action']) ? $params['action'] : 'add'; ?>">
                <?php 
                if(isset($params['action']) && $params['action'] == 'edit'){ 
                    ?>
                <div class="form-row buttons-group">
                    <input type="hidden" name="edit_id"
                        value="<?php echo isset($params['book']['id']) ? $params['book']['id'] : ''; ?>">
                </div>
                <?php
                } 
                ?>

                <div class="form-row">
                    <!-- Book ID -->
                    <div class="form-group">
                        <label for="owt7_txt_book_id"><?php _e("Book ID", "library-management-system"); ?><?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'book', 'owt7_txt_book_id' ) ) { ?> <span class="required">*</span><?php } ?></label>
                        <input <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> value="<?php echo isset($params['book']['book_id']) ? $params['book']['book_id'] : ''; ?>" <?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'book', 'owt7_txt_book_id' ) ) { ?>required <?php } ?>type="text" id="owt7_txt_book_id" readonly name="owt7_txt_book_id" placeholder="<?php esc_attr_e( 'e.g. BK-2024-001', 'library-management-system' ); ?>">
                    </div>
                    <!-- Category -->
                    <div class="form-group">
                        <label for="owt7_dd_category_id"><?php _e("Category", "library-management-system"); ?><?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'book', 'owt7_dd_category_id' ) ) { ?> <span class="required">*</span><?php } ?></label>
                        <select <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> id="owt7_dd_category_id" name="owt7_dd_category_id" <?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'book', 'owt7_dd_category_id' ) ) { ?>required<?php } ?>>
                            <option value=""><?php _e("-- Select Category --", "library-management-system"); ?></option>
                            <?php 
                                if(!empty($params['categories']) && is_array($params['categories'])){
                                    foreach($params['categories'] as $category){
                                        $selected = "";
                                        if(isset($params['book']['category_id']) && $params['book']['category_id'] == $category->id){
                                            $selected = "selected";
                                        }
                                        ?>
                                    <option <?php echo $selected; ?> value="<?php echo $category->id; ?>">
                                    <?php echo ucfirst($category->name); ?></option>
                                <?php
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <!-- Bookcase -->
                    <div class="form-group">
                        <label for="owt7_dd_bookcase_id"><?php _e("Bookcase", "library-management-system"); ?><?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'book', 'owt7_dd_bookcase_id' ) ) { ?> <span class="required">*</span><?php } ?></label>
                        <select <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> id="owt7_dd_bookcase_id" name="owt7_dd_bookcase_id" <?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'book', 'owt7_dd_bookcase_id' ) ) { ?>required<?php } ?>>
                            <option value=""><?php _e("-- Select Bookcase --", "library-management-system"); ?></option>
                            <?php 
                                if(!empty($params['bookcases']) && is_array($params['bookcases'])){
                                    foreach($params['bookcases'] as $bookcase){
                                        $selected = "";
                                        if(isset($params['book']['bookcase_id']) && $params['book']['bookcase_id'] == $bookcase->id){
                                            $selected = "selected";
                                        }
                                        ?>
                                    <option <?php echo $selected; ?> value="<?php echo $bookcase->id; ?>">
                                    <?php echo ucfirst($bookcase->name); ?></option>
                                <?php
                                    }
                                }
                            ?>
                        </select>
                    </div>
                    <!-- Section -->
                    <div class="form-group">
                        <label for="owt7_dd_section_id"><?php _e("Section", "library-management-system"); ?><?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'book', 'owt7_dd_section_id' ) ) { ?> <span class="required">*</span><?php } ?></label>
                        <select <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> id="owt7_dd_section_id" name="owt7_dd_section_id" <?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'book', 'owt7_dd_section_id' ) ) { ?>required<?php } ?>>
                            <option value=""><?php _e("-- Select Section --", "library-management-system"); ?></option>
                            <?php 
                                if(!empty($params['sections']) && is_array($params['sections'])){
                                    foreach($params['sections'] as $section){
                                        $selected = "";
                                        if(isset($params['book']['bookcase_section_id']) && $params['book']['bookcase_section_id'] == $section->id){
                                            $selected = "selected";
                                        }
                                        ?>
                                    <option <?php echo $selected; ?> value="<?php echo $section->id; ?>">
                                    <?php echo ucfirst($section->name); ?></option>
                                <?php
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <!-- Book Name -->
                    <div class="form-group">
                        <label for="owt7_txt_book_name"><?php _e("Book Name", "library-management-system"); ?><?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'book', 'owt7_txt_book_name' ) ) { ?> <span class="required">*</span><?php } ?></label>
                        <input <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> value="<?php echo isset($params['book']['name']) ? $params['book']['name'] : ''; ?>" <?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'book', 'owt7_txt_book_name' ) ) { ?>required <?php } ?>type="text" id="owt7_txt_book_name" name="owt7_txt_book_name" placeholder="<?php esc_attr_e( 'e.g. Introduction to Programming', 'library-management-system' ); ?>">
                    </div>
                    <!-- Author Name -->
                    <div class="form-group">
                        <label for="owt7_txt_author_name"><?php _e("Author(s) Name", "library-management-system"); ?><?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'book', 'owt7_txt_author_name' ) ) { ?> <span class="required">*</span><?php } ?></label>
                        <input <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> value="<?php echo isset($params['book']['author_name']) ? esc_attr($params['book']['author_name']) : ''; ?>" <?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'book', 'owt7_txt_author_name' ) ) { ?>required <?php } ?>type="text" id="owt7_txt_author_name" name="owt7_txt_author_name" placeholder="<?php esc_attr_e('e.g. John Doe, Jane Smith', 'library-management-system'); ?>">
                        <?php if ( isset($params['action']) && $params['action'] == 'view' && ! empty( $params['book']['author_name'] ) ) : ?>
                        <div class="owt7-lms-view-tags"><?php echo LIBMNS_Admin_FREE::libmns_render_comma_tags( $params['book']['author_name'] ); ?></div>
                        <?php else : ?>
                        <span class="owt7-lms-field-hint"><?php _e("Separate multiple authors with commas; they will appear as tags.", "library-management-system"); ?></span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-row">
                    <!-- Publication Name -->
                    <div class="form-group">
                        <label for="owt7_txt_publication_name"><?php _e("Publication(s) Name", "library-management-system"); ?><?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'book', 'owt7_txt_publication_name' ) ) { ?> <span class="required">*</span><?php } ?></label>
                        <input <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> value="<?php echo isset($params['book']['publication_name']) ? esc_attr($params['book']['publication_name']) : ''; ?>" <?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'book', 'owt7_txt_publication_name' ) ) { ?>required <?php } ?>type="text" id="owt7_txt_publication_name" name="owt7_txt_publication_name" placeholder="<?php esc_attr_e('e.g. Penguin, HarperCollins', 'library-management-system'); ?>">
                        <?php if ( isset($params['action']) && $params['action'] == 'view' && ! empty( $params['book']['publication_name'] ) ) : ?>
                        <div class="owt7-lms-view-tags"><?php echo LIBMNS_Admin_FREE::libmns_render_comma_tags( $params['book']['publication_name'] ); ?></div>
                        <?php else : ?>
                        <span class="owt7-lms-field-hint"><?php _e("Separate multiple publications with commas; they will appear as tags.", "library-management-system"); ?></span>
                        <?php endif; ?>
                    </div>
                    <!-- Publication Year -->
                    <div class="form-group">
                        <label for="owt7_txt_publication_year"><?php _e("Publication Year", "library-management-system"); ?><?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'book', 'owt7_txt_publication_year' ) ) { ?> <span class="required">*</span><?php } ?></label>
                        <input <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> value="<?php echo isset($params['book']['publication_year']) ? $params['book']['publication_year'] : ''; ?>" <?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'book', 'owt7_txt_publication_year' ) ) { ?>required <?php } ?>type="text" id="owt7_txt_publication_year" name="owt7_txt_publication_year" placeholder="<?php esc_attr_e( 'e.g. 2024', 'library-management-system' ); ?>">
                    </div>
                </div>

                <div class="form-row">
                    <!-- Publication Location -->
                    <div class="form-group">
                        <label for="owt7_txt_publication_location"><?php _e("Publication Location", "library-management-system"); ?><?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'book', 'owt7_txt_publication_location' ) ) { ?> <span class="required">*</span><?php } ?></label>
                        <input <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> value="<?php echo isset($params['book']['publication_location']) ? $params['book']['publication_location'] : ''; ?>" <?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'book', 'owt7_txt_publication_location' ) ) { ?>required <?php } ?>type="text" id="owt7_txt_publication_location" name="owt7_txt_publication_location" placeholder="<?php esc_attr_e( 'e.g. New York, NY', 'library-management-system' ); ?>">
                    </div>
                    <div class="form-group">
                        <!-- Cost -->
                        <label for="owt7_txt_cost"><?php _e("Price", "library-management-system"); ?><?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'book', 'owt7_txt_cost' ) ) { ?> <span class="required">*</span><?php } ?></label>
                        <input <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> value="<?php echo isset($params['book']['amount']) ? $params['book']['amount'] : ''; ?>" type="number" min="0" max="100000" id="owt7_txt_cost" name="owt7_txt_cost" <?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'book', 'owt7_txt_cost' ) ) { ?>required <?php } ?>placeholder="<?php esc_attr_e( 'e.g. 29.99', 'library-management-system' ); ?>">
                    </div>
                </div>

                <div class="form-row">
                    <!-- ISBN -->
                    <div class="form-group">
                        <label for="owt7_txt_isbn"><?php _e("ISBN", "library-management-system"); ?><?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'book', 'owt7_txt_isbn' ) ) { ?> <span class="required">*</span><?php } ?></label>
                        <input <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> value="<?php echo isset($params['book']['isbn']) ? $params['book']['isbn'] : ''; ?>" type="text" id="owt7_txt_isbn" name="owt7_txt_isbn" <?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'book', 'owt7_txt_isbn' ) ) { ?>required <?php } ?>placeholder="<?php esc_attr_e( 'e.g. 978-0-123456-78-9', 'library-management-system' ); ?>">
                    </div>
                    <!-- Book URL -->
                    <div class="form-group">
                        <label for="owt7_txt_book_url"><?php _e("Book URL", "library-management-system"); ?><?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'book', 'owt7_txt_book_url' ) ) { ?> <span class="required">*</span><?php } ?></label>
                        <input <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> value="<?php echo isset($params['book']['book_url']) ? $params['book']['book_url'] : ''; ?>" type="text" id="owt7_txt_book_url" name="owt7_txt_book_url" <?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'book', 'owt7_txt_book_url' ) ) { ?>required <?php } ?>placeholder="<?php esc_attr_e( 'https://example.com/book-page', 'library-management-system' ); ?>">
                    </div>
                </div>

                <div class="form-row">
                    <!-- Total Copies -->
                    <div class="form-group">
                        <label for="owt7_txt_quantity"><?php _e("Total Copies", "library-management-system"); ?><?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'book', 'owt7_txt_quantity' ) ) { ?> <span class="required">*</span><?php } ?></label>
                        <input <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> value="<?php echo isset($params['book']['stock_quantity']) ? $params['book']['stock_quantity'] : ''; ?>" type="number" min="1" max="1000000" id="owt7_txt_quantity" name="owt7_txt_quantity" <?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'book', 'owt7_txt_quantity' ) ) { ?>required <?php } ?>placeholder="<?php esc_attr_e( 'e.g. 10', 'library-management-system' ); ?>">
                    </div>
                    <!-- Book Language -->
                    <div class="form-group">
                        <label for="owt7_txt_book_language"><?php _e("Book Language", "library-management-system"); ?><?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'book', 'owt7_txt_book_language' ) ) { ?> <span class="required">*</span><?php } ?></label>
                        <input <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> value="<?php echo isset($params['book']['book_language']) ? $params['book']['book_language'] : ''; ?>" type="text" id="owt7_txt_book_language" name="owt7_txt_book_language" <?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'book', 'owt7_txt_book_language' ) ) { ?>required <?php } ?>placeholder="<?php esc_attr_e( 'e.g. English', 'library-management-system' ); ?>">
                    </div>
                </div>

                <div class="form-row">
                    <!-- Number of Pages -->
                    <div class="form-group">
                        <label for="owt7_txt_total_pages"><?php _e("Number of Page(s)", "library-management-system"); ?><?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'book', 'owt7_txt_total_pages' ) ) { ?> <span class="required">*</span><?php } ?></label>
                        <input <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> value="<?php echo isset($params['book']['book_pages']) ? $params['book']['book_pages'] : ''; ?>" type="text" id="owt7_txt_total_pages" name="owt7_txt_total_pages" <?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'book', 'owt7_txt_total_pages' ) ) { ?>required <?php } ?>placeholder="<?php esc_attr_e( 'e.g. 350', 'library-management-system' ); ?>">
                    </div>
                    <!-- Description -->
                    <div class="form-group">
                        <label for="owt7_txt_description"><?php _e("Description", "library-management-system"); ?><?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'book', 'owt7_txt_description' ) ) { ?> <span class="required">*</span><?php } ?></label>
                        <textarea <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> name="owt7_txt_description" id="owt7_txt_description" cols="50" rows="4" <?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'book', 'owt7_txt_description' ) ) { ?>required <?php } ?>placeholder="<?php esc_attr_e( 'Brief summary of the book...', 'library-management-system' ); ?>"><?php echo isset($params['book']['description']) ? $params['book']['description'] : ''; ?></textarea>
                    </div>
                </div>

                <div class="form-row">
                    <!-- Cover Image -->
                    <div class="form-group">
                        <label for="owt7_cover_image"><?php _e("Cover Image", "library-management-system"); ?><?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'book', 'owt7_cover_image' ) ) { ?> <span class="required">*</span><?php } ?></label>
                        <?php if(isset($params['action']) && $params['action'] == 'view'){ }else{ ?>
                        <button id="owt7_upload_image" type="button" class="btn btn-primary button-large">
                            <?php _e("Upload Cover Image", "library-management-system"); ?>
                        </button>
                        <?php } 
                                if(!empty($params['book']['cover_image'])){
                                    ?> <img src="<?php echo $params['book']['cover_image']; ?>"
                            id="owt7_library_image_preview" /> <?php
                                }else{
                                    ?> <img src="<?php echo LIBMNS_PLUGIN_URL . 'admin/images/default-cover-image.png'; ?>" id="owt7_library_image_preview" /> <?php
                                } 
                        ?>
                        <input type="hidden"
                            value="<?php echo isset($params['book']['cover_image']) && !empty($params['book']['cover_image']) ? $params['book']['cover_image'] : LIBMNS_PLUGIN_URL . 'admin/images/default-cover-image.png'; ?>"
                            name="owt7_cover_image" id="owt7_image_url" />
                    </div>

                    <!-- Status -->
                    <div class="form-group">
                        <label for="owt7_dd_book_status"><?php _e("Status", "library-management-system"); ?><?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'book', 'owt7_dd_book_status' ) ) { ?> <span class="required">*</span><?php } ?></label>
                        <select <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> id="owt7_dd_book_status" name="owt7_dd_book_status" <?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'book', 'owt7_dd_book_status' ) ) { ?>required<?php } ?>>
                            <option value=""><?php _e("-- Select Status --", "library-management-system"); ?></option>
                            <?php 
                            if(!empty($params['statuses']) && is_array($params['statuses'])){
                                foreach($params['statuses'] as $key => $status){
                                    $selected = "";
                                    if(isset($params['book']['status']) && $params['book']['status'] == $key){
                                        $selected = "selected";
                                    }
                                    ?>
                                        <option <?php echo $selected; ?> value="<?php echo $key; ?>"><?php echo ucfirst($status); ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <?php if ( isset( $params['action'] ) && $params['action'] == 'view' && ! empty( $params['book']['id'] ) && LIBMNS_Admin_FREE::libmns_current_user_can( 'owt7_lms_list_books' ) ) { ?>
                <div class="form-row buttons-group">
                    <button type="button" class="btn owt7_lms_show_book_copies_btn" data-book-id="<?php echo esc_attr( $params['book']['id'] ); ?>"><?php _e("View Book Copies", "library-management-system"); ?></button>
                </div>
                <?php } elseif( ( !isset($params['action']) || $params['action'] == 'add' ) && LIBMNS_Admin_FREE::libmns_current_user_can( 'owt7_lms_add_book' ) ){ ?>
                <div class="form-row buttons-group">
                    <button class="btn submit-save-btn" type="submit"><?php _e("Submit & Save", "library-management-system"); ?></button>
                </div>
                <?php } elseif( isset($params['action']) && $params['action'] == 'edit' && LIBMNS_Admin_FREE::libmns_current_user_can( 'owt7_lms_edit_book' ) ){ ?>
                <div class="form-row buttons-group">
                    <button class="btn submit-save-btn" type="submit"><?php _e("Submit & Save", "library-management-system"); ?></button>
                </div>
                <?php } ?>

            </form>

            <?php include_once LIBMNS_PLUGIN_DIR_PATH . 'admin/views/books/templates/owt7_library_book_copies_modal.php'; ?>

        </div>
    </div>
