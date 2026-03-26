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
    <div class="lms-add-user">
        <div class="page-header">
            <div class="breadcrumb"><?php _e("Library System", "library-management-system"); ?> >> <span class="active"><?php _e("Add New User", "library-management-system"); ?></span></div>
            <div class="page-actions">
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=owt7_library_users' ) ); ?>" class="btn"><span class="dashicons dashicons-arrow-left-alt"></span> <?php _e("Back", "library-management-system"); ?></a>
            </div>
        </div>
        <div class="page-container">
            <div class="page-title-row">
                <div class="page-title">
                    <?php if(isset($params['action'])){ ?> <h2><?php echo ucfirst($params['action']) ?> <?php _e("User", "library-management-system"); ?></h2> <?php }else{ ?> <h2><?php _e("Add User", "library-management-system"); ?></h2> <?php } ?>
                </div>
            </div>
            <form class="owt7_lms_user_form" id="owt7_lms_user_form" action="javascript:void(0)" method="post" accept-charset="UTF-8">
                <?php wp_nonce_field( 'owt7_library_actions', 'owt7_lms_nonce' ); ?>
                <input type="hidden" name="action_type" value="<?php echo isset($params['action']) && !empty($params['action']) ? $params['action'] : 'add'; ?>">
                <?php if(isset($params['action']) && $params['action'] == 'edit'){ ?>
                <div class="form-row buttons-group">
                    <input type="hidden" name="edit_id" value="<?php echo isset($params['user']['id']) ? $params['user']['id'] : ''; ?>">
                </div>
                <?php } ?>                
                <div class="form-row">
                    <!-- User ID -->
                    <div class="form-group">
                        <label for="user-id"><?php _e("User ID", "library-management-system"); ?><?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'user', 'owt7_txt_u_id' ) ) { ?> <span class="required">*</span><?php } ?></label>
                        <input <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> value="<?php echo isset($params['user']['u_id']) ? $params['user']['u_id'] : ''; ?>" <?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'user', 'owt7_txt_u_id' ) ) { ?>required <?php } ?>type="text" id="owt7_txt_u_id" name="owt7_txt_u_id" readonly placeholder="<?php esc_attr_e( 'e.g. LIB-2024-001', 'library-management-system' ); ?>">
                    </div>
                    <!-- Branch -->
                    <div class="form-group">
                        <label for="branch"><?php _e("Select Branch", "library-management-system"); ?><?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'user', 'owt7_dd_branch_id' ) ) { ?> <span class="required">*</span><?php } ?></label>
                        <select <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> <?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'user', 'owt7_dd_branch_id' ) ) { ?>required <?php } ?>id="owt7_dd_branch_id" name="owt7_dd_branch_id">
                            <option value="">-- <?php _e("Select Branch", "library-management-system"); ?> --</option>
                            <?php 
                            if(!empty($params['branches']) && is_array($params['branches'])){
                                foreach($params['branches'] as $branch){
                                    $selected = "";
                                    if(isset($params['user']['branch_id']) && $params['user']['branch_id'] == $branch->id){
                                        $selected = "selected";
                                    }
                                    ?>
                                    <option <?php echo $selected; ?> value="<?php echo ucwords($branch->id); ?>"><?php echo ucwords($branch->name); ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <!-- Name -->
                    <div class="form-group">
                        <label for="owt7_txt_name"><?php _e("Name", "library-management-system"); ?><?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'user', 'owt7_txt_name' ) ) { ?> <span class="required">*</span><?php } ?></label>
                        <input <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> value="<?php echo isset($params['user']['name']) ? $params['user']['name'] : ''; ?>" <?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'user', 'owt7_txt_name' ) ) { ?>required <?php } ?>type="text" id="owt7_txt_name" name="owt7_txt_name" placeholder="<?php esc_attr_e( 'e.g. John Smith', 'library-management-system' ); ?>" />
                    </div>
                    <!-- Email -->
                    <div class="form-group">
                        <label for="owt7_txt_email"><?php _e("Email", "library-management-system"); ?><?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'user', 'owt7_txt_email' ) ) { ?> <span class="required">*</span><?php } ?></label>
                        <input <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> value="<?php echo isset($params['user']['email']) ? $params['user']['email'] : ''; ?>" <?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'user', 'owt7_txt_email' ) ) { ?>required <?php } ?>type="email" id="owt7_txt_email" name="owt7_txt_email" placeholder="<?php esc_attr_e( 'e.g. john@example.com', 'library-management-system' ); ?>">
                    </div>
                </div>
                <div class="form-row">
                    <!-- Phone number -->
                    <div class="form-group">
                        <label for="owt7_txt_phone"><?php _e("Phone Number", "library-management-system"); ?><?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'user', 'owt7_txt_phone' ) ) { ?> <span class="required">*</span><?php } ?></label>
                        <input <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> value="<?php echo isset($params['user']['phone_no']) ? $params['user']['phone_no'] : ''; ?>" <?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'user', 'owt7_txt_phone' ) ) { ?>required <?php } ?>type="text" id="owt7_txt_phone" name="owt7_txt_phone" placeholder="<?php esc_attr_e( 'e.g. +1 234 567 8900', 'library-management-system' ); ?>">
                    </div>
                    <!-- Gender -->
                    <div class="form-group">
                        <label for="owt7_dd_gender"><?php _e("Gender", "library-management-system"); ?><?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'user', 'owt7_dd_gender' ) ) { ?> <span class="required">*</span><?php } ?></label>
                        <select <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> id="owt7_dd_gender" name="owt7_dd_gender" <?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'user', 'owt7_dd_gender' ) ) { ?>required <?php } ?>>
                            <option value="">-- <?php _e("Select Gender", "library-management-system"); ?> --</option>
                            <?php 
                            if(!empty($params['genders']) && is_array($params['genders'])){
                                foreach($params['genders'] as $gender){
                                    $selected = "";
                                    if(isset($params['user']['gender']) && $params['user']['gender'] == $gender){
                                        $selected = "selected";
                                    }
                                    ?>
                                        <option <?php echo $selected; ?> value="<?php echo $gender; ?>"><?php echo ucfirst($gender); ?></option>
                                    <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-row">
                    <!-- Address -->
                    <div class="form-group">
                        <label for="owt7_txt_address"><?php _e("Address", "library-management-system"); ?><?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'user', 'owt7_txt_address' ) ) { ?> <span class="required">*</span><?php } ?></label>
                        <input <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> value="<?php echo isset($params['user']['address_info']) ? $params['user']['address_info'] : ''; ?>" <?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'user', 'owt7_txt_address' ) ) { ?>required <?php } ?>type="text" id="owt7_txt_address" name="owt7_txt_address" placeholder="<?php esc_attr_e( 'e.g. 123 Main St, City, Country', 'library-management-system' ); ?>">
                    </div>
                    <!-- Status -->
                    <div class="form-group">
                        <label for="owt7_dd_user_status"><?php _e("Status", "library-management-system"); ?><?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'user', 'owt7_dd_user_status' ) ) { ?> <span class="required">*</span><?php } ?></label>
                        <select <?php echo isset($params['action']) && $params['action'] == 'view' ? 'disabled' : ''; ?> id="owt7_dd_user_status" <?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'user', 'owt7_dd_user_status' ) ) { ?>required <?php } ?>name="owt7_dd_user_status">
                            <option value="">-- <?php _e("Select Status", "library-management-system"); ?> --</option>
                            <?php 
                            if(!empty($params['statuses']) && is_array($params['statuses'])){
                                foreach($params['statuses'] as $key => $status){
                                    $selected = "";
                                    if(isset($params['user']['status']) && $params['user']['status'] == $key){
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
                <?php
                $is_add_user_page = ( ! isset( $params['action'] ) || $params['action'] === '' || $params['action'] == 'add' );
                $is_edit_user_page = isset( $params['action'] ) && $params['action'] == 'edit';
                $user_has_wp_creds = ! empty( $params['user']['wp_user'] ) && ! empty( $params['user']['wp_user_id'] );
                if ( $is_add_user_page && LIBMNS_Admin_FREE::libmns_current_user_can( 'owt7_lms_add_user' ) ) : ?>
                <div class="form-row owt7-lms-wp-user-section">
                    <div class="form-group owt7-lms-wp-user-section-inner">
                        <div class="owt7-lms-wp-user-checkbox-wrap">
                            <label class="owt7-lms-wp-user-checkbox-label">
                                <input type="checkbox" name="owt7_save_as_wp_user" id="owt7_save_as_wp_user" value="1" <?php checked( isset( $params['user']['wp_user'] ) && $params['user']['wp_user'] ); ?> />
                                <span class="owt7-lms-wp-user-checkbox-text"><?php _e( 'Create as WordPress user?', 'library-management-system' ); ?></span>
                            </label>
                            <p class="description owt7-lms-wp-user-desc"><?php _e( 'If enabled, a WordPress account will be created with the credentials below so this library user can log in to the site.', 'library-management-system' ); ?></p>
                        </div>
                        <div class="owt7-wp-creds-fields" id="owt7_wp_creds_fields" style="display: none;">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="owt7_wp_username"><?php _e( 'WordPress Username', 'library-management-system' ); ?> <span class="required owt7-wp-required">*</span></label>
                                    <input type="text" id="owt7_wp_username" name="owt7_wp_username" value="" placeholder="<?php esc_attr_e( 'Choose a login username', 'library-management-system' ); ?>" autocomplete="off" />
                                </div>
                                <div class="form-group">
                                    <label for="owt7_wp_password"><?php _e( 'WordPress Password', 'library-management-system' ); ?> <span class="required owt7-wp-required">*</span></label>
                                    <input type="password" id="owt7_wp_password" name="owt7_wp_password" value="" placeholder="<?php esc_attr_e( 'e.g. •••••••• (min. 6 characters)', 'library-management-system' ); ?>" autocomplete="new-password" />
                                    <p class="description"><?php _e( 'Minimum 6 characters recommended.', 'library-management-system' ); ?></p>
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="owt7_lms_wp_user_role"><?php _e( 'LMS Role', 'library-management-system' ); ?> <span class="required owt7-wp-required">*</span></label>
                                    <select id="owt7_lms_wp_user_role" name="owt7_lms_wp_user_role" class="form-control">
                                        <?php
                                        $lms_roles = LIBMNS_Admin_FREE::libmns_get_assignable_lms_roles();
                                        foreach ( $lms_roles as $role_slug => $role_label ) {
                                            ?>
                                            <option value="<?php echo esc_attr( $role_slug ); ?>"><?php echo esc_html( $role_label ); ?></option>
                                            <?php
                                        }
                                        ?>
                                    </select>
                                    <p class="description"><?php _e( 'Role cannot be changed after saving. In the free edition, WordPress-linked borrowers use the Library User (LMS) role for portal access.', 'library-management-system' ); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <?php
                $is_view_user_page = isset( $params['action'] ) && $params['action'] == 'view';
                $show_wp_details_section = $is_edit_user_page || $is_view_user_page;
                if ( $show_wp_details_section ) :
                    $edit_wp_user_id = isset( $params['user']['wp_user_id'] ) ? absint( $params['user']['wp_user_id'] ) : 0;
                    $edit_wp_user = $edit_wp_user_id > 0 ? get_userdata( $edit_wp_user_id ) : null;
                    $lms_roles_map = LIBMNS_Admin_FREE::libmns_get_assignable_lms_roles();
                    $edit_lms_role = $edit_wp_user && ! empty( $edit_wp_user->roles ) ? array_intersect( array_keys( $lms_roles_map ), $edit_wp_user->roles ) : array();
                    $edit_lms_role_display = ! empty( $edit_lms_role ) ? $lms_roles_map[ reset( $edit_lms_role ) ] : __( '—', 'library-management-system' );
                ?>
                <div class="form-row owt7-lms-wp-user-section owt7-lms-wp-user-section-edit <?php echo $is_view_user_page ? 'owt7-lms-wp-user-section-view' : ''; ?>">
                    <div class="form-group owt7-lms-wp-user-section-inner">
                        <h3 class="owt7-lms-wp-creds-heading"><?php _e( 'WordPress details', 'library-management-system' ); ?></h3>
                        <?php if ( $user_has_wp_creds ) : ?>
                        <div class="owt7-wp-details-row">
                            <p class="description owt7-lms-wp-detail-item"><strong><?php _e( 'Username:', 'library-management-system' ); ?></strong> <?php echo esc_html( isset( $params['user']['wp_username'] ) ? $params['user']['wp_username'] : '—' ); ?></p>
                            <p class="description owt7-lms-wp-detail-item"><strong><?php _e( 'Role:', 'library-management-system' ); ?></strong> <?php echo esc_html( $edit_lms_role_display ); ?></p>
                        </div>
                        <?php if ( $is_edit_user_page && LIBMNS_Admin_FREE::libmns_current_user_can( 'owt7_lms_edit_user' ) ) : ?>
                        <?php if ( ! empty( $edit_lms_role ) ) : ?>
                        <p class="description owt7-lms-wp-role-display"><span class="owt7-lms-role-not-editable">(<?php _e( 'Role not editable after save', 'library-management-system' ); ?>)</span></p>
                        <?php endif; ?>
                        <p class="description owt7-lms-wp-user-desc"><?php _e( 'Enter a new password below to change it; leave blank to keep current.', 'library-management-system' ); ?></p>
                        <div class="owt7-wp-creds-fields owt7-wp-creds-fields-edit">
                            <div class="form-row">
                                <div class="form-group owt7-wp-password-display-wrap">
                                    <label for="owt7_wp_edit_password"><?php _e( 'Update password', 'library-management-system' ); ?></label>
                                    <div class="owt7-wp-password-masked-wrap">
                                        <input type="password" id="owt7_wp_edit_password" name="owt7_wp_new_password" value="" placeholder="<?php esc_attr_e( 'Leave blank to keep current', 'library-management-system' ); ?>" autocomplete="new-password" />
                                        <button type="button" class="owt7-wp-password-toggle-btn" id="owt7_wp_password_toggle_btn" title="<?php esc_attr_e( 'Show password', 'library-management-system' ); ?>" aria-label="<?php esc_attr_e( 'Toggle password visibility', 'library-management-system' ); ?>"><span class="dashicons dashicons-visibility"></span></button>
                                    </div>
                                    <p class="description"><?php _e( 'Leave blank to keep current password. Enter a new password (min. 6 characters) to update.', 'library-management-system' ); ?></p>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                        <?php else : ?>
                        <div class="owt7-wp-details-row owt7-wp-details-not-linked">
                            <p class="description owt7-lms-wp-detail-item"><?php _e( 'Not a WordPress user.', 'library-management-system' ); ?></p>
                            <?php if ( $is_edit_user_page && LIBMNS_Admin_FREE::libmns_current_user_can( 'owt7_lms_edit_user' ) ) : ?>
                            <p class="description owt7-lms-wp-link-desc"><?php _e( 'You can create a WordPress account for this user below. After saving, they will be able to log in with the credentials you set.', 'library-management-system' ); ?></p>
                            <div class="owt7-lms-wp-user-checkbox-wrap owt7-lms-wp-link-on-edit">
                                <label class="owt7-lms-wp-user-checkbox-label">
                                    <input type="checkbox" name="owt7_save_as_wp_user" id="owt7_save_as_wp_user_edit" value="1" />
                                    <span class="owt7-lms-wp-user-checkbox-text"><?php _e( 'Create as WordPress user?', 'library-management-system' ); ?></span>
                                </label>
                            </div>
                            <div class="owt7-wp-creds-fields owt7-wp-creds-fields-edit-link" id="owt7_wp_creds_fields_edit" style="display: none;">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="owt7_wp_username_edit"><?php _e( 'WordPress Username', 'library-management-system' ); ?> <span class="required owt7-wp-required">*</span></label>
                                        <input type="text" id="owt7_wp_username_edit" name="owt7_wp_username" value="" placeholder="<?php esc_attr_e( 'Choose a login username', 'library-management-system' ); ?>" autocomplete="off" />
                                    </div>
                                    <div class="form-group">
                                        <label for="owt7_wp_password_edit"><?php _e( 'WordPress Password', 'library-management-system' ); ?> <span class="required owt7-wp-required">*</span></label>
                                        <input type="password" id="owt7_wp_password_edit" name="owt7_wp_password" value="" placeholder="<?php esc_attr_e( 'e.g. •••••••• (min. 6 characters)', 'library-management-system' ); ?>" autocomplete="new-password" />
                                        <p class="description"><?php _e( 'Minimum 6 characters recommended.', 'library-management-system' ); ?></p>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="owt7_lms_wp_user_role_edit"><?php _e( 'LMS Role', 'library-management-system' ); ?> <span class="required owt7-wp-required">*</span></label>
                                        <select id="owt7_lms_wp_user_role_edit" name="owt7_lms_wp_user_role" class="form-control">
                                            <?php foreach ( $lms_roles_map as $role_slug => $role_label ) : ?>
                                            <option value="<?php echo esc_attr( $role_slug ); ?>"><?php echo esc_html( $role_label ); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
                <div class="form-row">
                    <!-- Profile Image -->
                    <div class="form-group">
                        <label for="owt7_profile_image"><?php _e("Profile Image", "library-management-system"); ?><?php if ( LIBMNS_Admin_FREE::libmns_is_field_required( 'user', 'owt7_profile_image' ) ) { ?> <span class="required">*</span><?php } ?></label>
                        <?php if(isset($params['action']) && $params['action'] == 'view'){ }else{ ?>
                            <button id="owt7_upload_image" type="button" class="btn btn-primary button-large">
                                <?php _e("Upload Profile Image", "library-management-system"); ?>
                            </button>
                        <?php } ?>
                        <?php if(!empty($params['user']['profile_image'])){ ?>
                            <img src="<?php echo $params['user']['profile_image']; ?>" id="owt7_library_image_preview" class="owt7-lms-profile-avatar" alt="<?php esc_attr_e( 'Profile photo', 'library-management-system' ); ?>"/>
                        <?php }else{ ?>
                            <img src="<?php echo LIBMNS_PLUGIN_URL . 'admin/images/default-user-image.png'; ?>" id="owt7_library_image_preview" class="owt7-lms-profile-avatar" alt="<?php esc_attr_e( 'Profile photo', 'library-management-system' ); ?>"/>
                        <?php } ?>
                        <input type="hidden" value="<?php echo isset($params['user']['profile_image']) ? $params['user']['profile_image'] : ''; ?>" name="owt7_profile_image" id="owt7_image_url" />
                    </div>
                </div>
                <?php if(isset($params['action']) && $params['action'] == 'view'){ }elseif( $is_add_user_page && LIBMNS_Admin_FREE::libmns_current_user_can( 'owt7_lms_add_user' ) ){ ?>
                    <div class="form-row buttons-group">
                        <button class="btn submit-save-btn" type="submit"><?php _e("Submit & Save", "library-management-system"); ?></button>
                    </div>
                <?php } elseif( isset($params['action']) && $params['action'] == 'edit' && LIBMNS_Admin_FREE::libmns_current_user_can( 'owt7_lms_edit_user' ) ){ ?>
                    <div class="form-row buttons-group">
                        <button class="btn submit-save-btn" type="submit"><?php _e("Submit & Save", "library-management-system"); ?></button>
                    </div>
                <?php } ?>
            </form>
        </div>
    </div>
</div>

