<?php
/**
 * @link       https://onlinewebtutorblog.com
 * @since      3.5
 * @package    Library_Management_System
 * @subpackage Library_Management_System/admin/views/bookcases
 * @copyright  Copyright (c) 2026, Online Web Tutor
 * @license    GPL-2.0+ https://www.gnu.org/licenses/gpl-2.0.html
 * @author     Online Web Tutor
 */
?>
<div class="owt7-lms owt7-lms-bookcases">

    <div class="owt7_library_add_section">

        <div class="page-header">
            <div class="breadcrumb"> <?php _e("Library System", "library-management-system"); ?> >> <span class="active"><?php _e("Add New Section", "library-management-system"); ?></span> </div>
            <div class="page-actions">
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=owt7_library_bookcases' ) ); ?>" class="btn"><span class="dashicons dashicons-arrow-left-alt"></span> <?php _e("Back", "library-management-system"); ?></a>
            </div>
        </div>

        <div class="page-container">

            <div class="page-title-row">
                <div class="page-title">
                    <?php if(isset($params['action'])){ ?> <h2><?php echo _e(ucfirst($params['action']), "library-management-system"); ?> <?php _e("Section", "library-management-system"); ?></h2>
                    <?php }else{ ?> <h2><?php _e("Add Section", "library-management-system"); ?></h2> <?php } ?>
                </div>
            </div>

            <div class="page-container-inner">

                <form class="owt7_lms_section_form" id="owt7_lms_section_form" action="javascript:void(0);"
                    method="post" accept-charset="UTF-8">

                    <?php wp_nonce_field( 'owt7_library_actions', 'owt7_lms_nonce' ); ?>
                    <input type="hidden" name="action_type"
                        value="<?php echo isset($params['action']) && !empty($params['action']) ? $params['action'] : 'add'; ?>">
                    <?php 
                    if(isset($params['action']) && $params['action'] == 'edit'){ 
                    ?>
                    <div class="form-row buttons-group">
                        <input type="hidden" name="edit_id"
                            value="<?php echo isset($params['section']['id']) ? $params['section']['id'] : ''; ?>">
                    </div>
                    <?php
                    } 
                    ?>

                    <div class="form-row">
                        <!-- Bookcase -->
                        <div class="form-group">
                            <label for="owt7_dd_bookcase_name"><?php _e("Bookcase", "library-management-system"); ?><?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'section', 'owt7_dd_bookcase_id' ) ) { ?> <span class="required">*</span><?php } ?></label>
                            <select
                                <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?>
                                name="owt7_dd_bookcase_id" <?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'section', 'owt7_dd_bookcase_id' ) ) { ?>required<?php } ?>>
                                <option value=""><?php _e("-- Select Bookcase --", "library-management-system"); ?></option>
                                <?php 
                                if(!empty($params['bookcases']) && is_array($params['bookcases'])){
                                    foreach($params['bookcases'] as $bookcase){
                                        $selected = "";
                                        if(isset($params['section']['bookcase_id']) && $params['section']['bookcase_id'] == $bookcase->id){
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
                    </div>

                    <div class="form-row">
                        <!-- Section name -->
                        <div class="form-group">
                            <label for="owt7_txt_section_name"><?php _e("Name", "library-management-system"); ?><?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'section', 'owt7_txt_section_name' ) ) { ?> <span class="required">*</span><?php } ?></label>
                            <input type="text"
                                <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?>
                                value="<?php echo isset($params['section']['name']) ? $params['section']['name'] : ''; ?>"
                                <?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'section', 'owt7_txt_section_name' ) ) { ?>required <?php } ?>id="owt7_txt_section_name" name="owt7_txt_section_name" placeholder="<?php esc_attr_e( 'e.g. Row A, Shelf 1', 'library-management-system' ); ?>">
                        </div>
                        <!-- Status -->
                        <div class="form-group">
                            <label for="owt7_dd_section_status"><?php _e("Status", "library-management-system"); ?><?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'section', 'owt7_dd_section_status' ) ) { ?> <span class="required">*</span><?php } ?></label>
                            <select
                                <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?>
                                id="owt7_dd_section_status" name="owt7_dd_section_status" <?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'section', 'owt7_dd_section_status' ) ) { ?>required<?php } ?>>
                                <option value=""><?php _e("-- Select Status --", "library-management-system"); ?></option>
                                <?php 
                                if(!empty($params['statuses']) && is_array($params['statuses'])){
                                    foreach($params['statuses'] as $key => $status){
                                        $selected = "";
                                        if(isset($params['section']['status']) && $params['section']['status'] == $key){
                                            $selected = "selected";
                                        }
                                        ?>
                                <option <?php echo $selected; ?> value="<?php echo $key; ?>">
                                    <?php echo ucfirst($status); ?></option>
                                <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>

                    </div>

                    <?php if(isset($params['action']) && $params['action'] == 'view'){ }elseif( ( !isset($params['action']) || $params['action'] == 'add' ) && LIBMNS_Admin_FREE::libmns_current_user_can( 'owt7_lms_add_section' ) ){ ?>
                    <div class="form-row buttons-group">
                        <button class="btn submit-save-btn" type="submit"><?php _e("Submit & Save", "library-management-system"); ?></button>
                    </div>
                    <?php } elseif( isset($params['action']) && $params['action'] == 'edit' && LIBMNS_Admin_FREE::libmns_current_user_can( 'owt7_lms_edit_section' ) ){ ?>
                    <div class="form-row buttons-group">
                        <button class="btn submit-save-btn" type="submit"><?php _e("Submit & Save", "library-management-system"); ?></button>
                    </div>
                    <?php } ?>

                </form>

            </div>

        </div>
    </div>

</div>