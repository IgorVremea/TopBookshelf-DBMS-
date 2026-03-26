<?php
/**
 * @link       https://onlinewebtutorblog.com
 * @since      3.5
 * @package    Library_Management_System
 * @subpackage Library_Management_System/admin/views/users
 * @copyright  Copyright (c) 2026, Online Web Tutor
 * @license    GPL-2.0+ https://www.gnu.org/licenses/gpl-2.0.html
 * @author     Online Web Tutor
 */
?>
<div class="owt7-lms owt7-lms-users">

    <div class="lms-add-branch">

        <div class="page-header">
            <div class="breadcrumb"> <?php _e("Library System", "library-management-system"); ?> >> <span class="active"><?php _e("Add New Branch", "library-management-system"); ?></span> </div>
            <div class="page-actions">
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=owt7_library_users' ) ); ?>" class="btn"><span class="dashicons dashicons-arrow-left-alt"></span> <?php _e("Back", "library-management-system"); ?></a>
            </div>
        </div>

        <div class="page-container">

            <div class="page-title-row">
                <div class="page-title">
                    <?php if(isset($params['action'])){ ?> <h2><?php echo ucfirst($params['action']) ?> <?php _e("Branch", "library-management-system"); ?></h2> <?php }else{ ?> <h2><?php _e("Add Branch", "library-management-system"); ?></h2> <?php } ?>
                </div>
            </div>

            <form class="owt7_lms_branch_form" id="owt7_lms_branch_form" action="javascript:void(0);" method="post" accept-charset="UTF-8">

                <?php wp_nonce_field( 'owt7_library_actions', 'owt7_lms_nonce' ); ?>
                <input type="hidden" name="action_type" value="<?php echo isset($params['action']) && !empty($params['action']) ? $params['action'] : 'add'; ?>">
                <?php 
                if(isset($params['action']) && $params['action'] == 'edit'){ 
                    ?>
                    <div class="form-row buttons-group">
                    <input type="hidden" name="edit_id" value="<?php echo isset($params['branch']['id']) ? $params['branch']['id'] : ''; ?>">
                    </div>
                    <?php
                } 
                ?>

                <div class="form-row">
                    <!-- Branch name -->
                    <div class="form-group">
                        <label for="owt7_txt_branch_name"><?php _e("Branch Name", "library-management-system"); ?><?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'branch', 'owt7_txt_branch_name' ) ) { ?> <span class="required">*</span><?php } ?></label>
                        <input value="<?php echo isset($params['branch']['name']) ? $params['branch']['name'] : ''; ?>" <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> type="text" id="owt7_txt_branch_name" <?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'branch', 'owt7_txt_branch_name' ) ) { ?>required <?php } ?>name="owt7_txt_branch_name"
                        placeholder="<?php esc_attr_e( 'e.g. Main Library, Downtown Branch', 'library-management-system' ); ?>">
                    </div>
                    <!-- Status -->
                    <div class="form-group">
                        <label for="owt7_dd_branch_status"><?php _e("Status", "library-management-system"); ?><?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'branch', 'owt7_dd_branch_status' ) ) { ?> <span class="required">*</span><?php } ?></label>
                        <select <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> id="owt7_dd_branch_status" name="owt7_dd_branch_status" <?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'branch', 'owt7_dd_branch_status' ) ) { ?>required<?php } ?>>
                            <option value="">-- <?php _e("Select Status", "library-management-system"); ?> --</option>
                            <?php 
                            if(!empty($params['statuses']) && is_array($params['statuses'])){
                                foreach($params['statuses'] as $key => $status){
                                    $selected = "";
                                    if(isset($params['branch']['status']) && $params['branch']['status'] == $key){
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

                <?php if(isset($params['action']) && $params['action'] == 'view'){ }elseif( ( !isset($params['action']) || $params['action'] == 'add' ) && LIBMNS_Admin_FREE::libmns_current_user_can( 'owt7_lms_add_branch' ) ){ ?>
                    <div class="form-row buttons-group">
                        <button class="btn submit-save-btn" type="submit"><?php _e("Submit & Save", "library-management-system"); ?></button>
                    </div>
                <?php } elseif( isset($params['action']) && $params['action'] == 'edit' && LIBMNS_Admin_FREE::libmns_current_user_can( 'owt7_lms_edit_branch' ) ){ ?>
                    <div class="form-row buttons-group">
                        <button class="btn submit-save-btn" type="submit"><?php _e("Submit & Save", "library-management-system"); ?></button>
                    </div>
                <?php } ?>

            </form>

        </div>
    </div>

</div>
