<?php
/**
 * @link       https://onlinewebtutorblog.com
 * @since      3.5
 * @package    Library_Management_System
 * @subpackage Library_Management_System/public/views
 * @copyright  Copyright (c) 2026, Online Web Tutor
 * @license    GPL-2.0+ https://www.gnu.org/licenses/gpl-2.0.html
 * @author     Online Web Tutor
 */
?>
<div class="owt7-lms">
    <div class="owt7-lms-single-book">
        <a href="<?php echo esc_url(LIBMNS_Public_FREE::owt7_lms_library_base_url()); ?>" class="owt7_lms_back_button">&laquo; <?php esc_html_e('Back', 'library-management-system'); ?></a>
        <div class="book-details-container owt7_lms_single_book">
            <?php if(isset($params['book']->cover_image) && !empty($params['book']->cover_image)){ ?>
            <div class="book-cover">
                <img src="<?php echo $params['book']->cover_image; ?>" alt="<?php echo $params['book']->name; ?>">
            </div>
            <?php }else{ ?>
            <div class="book-cover">
                <img src="<?php echo LIBMNS_PLUGIN_URL . 'public/images/default-cover-image.png'; ?>" alt="<?php echo $params['book']->name; ?>">
            </div>
            <?php } ?>
            <div class="book-info">
                <h2 class="book-title"><?php echo $params['book']->name; ?></h2>
                <p class="book-author"><strong><?php esc_html_e('Author:', 'library-management-system'); ?></strong> <?php echo LIBMNS_Admin_FREE::libmns_render_comma_tags( $params['book']->author_name ) ?: esc_html( __( '—', 'library-management-system' ) ); ?></p>
                <?php if ( ! empty( $params['book']->publication_name ) ) : ?>
                <p class="book-publication"><strong><?php esc_html_e('Publication:', 'library-management-system'); ?></strong> <?php echo LIBMNS_Admin_FREE::libmns_render_comma_tags( $params['book']->publication_name ); ?></p>
                <?php endif; ?>
                <p class="book-status"><strong><?php esc_html_e('Status:', 'library-management-system'); ?></strong> <?php echo ! empty( $params['book']->status ) && (int) $params['book']->stock_quantity > 0 ? esc_html__( 'Available', 'library-management-system' ) : esc_html__( 'Not Available', 'library-management-system' ); ?></p>
                <p class="book-category"><strong><?php esc_html_e('Category:', 'library-management-system'); ?></strong> <?php echo esc_html($params['book']->category_name); ?></p>
                <p class="book-description">
                    <strong><?php esc_html_e('Description:', 'library-management-system'); ?></strong> <?php echo esc_html($params['book']->description); ?>
                </p>
                <div class="book-footer">
                    <?php if($params['book']->status){ 
                        if ( is_user_logged_in() ) { 
                            // LMS Public Page Settings
                            $settings = get_option("owt7_lms_public_settings");
                            $user = wp_get_current_user();
                            $logged_in_wp_roles = isset($user->roles) ? (array) $user->roles : [];
                            
                            $lms_saved_roles = isset($settings['wp_lms_roles']) && is_array($settings['wp_lms_roles']) ? $settings['wp_lms_roles'] : array();
                            $showCheckoutBtn = false;
                            if ( ! empty( $logged_in_wp_roles ) && ! empty( $lms_saved_roles ) ) {
                                foreach ( $logged_in_wp_roles as $u_role ) {
                                    if ( in_array( $u_role, $lms_saved_roles, true ) ) {
                                        $showCheckoutBtn = true;
                                        break;
                                    }
                                }
                            }
                            if ($showCheckoutBtn) {
                                if(in_array($params['book']->id, $params['book_ids'])){
                                    if(in_array($params['checkout_statuses'][$params['book']->id], [LIBMNS_CHECKOUT_APPROVED_BY_ADMIN, LIBMNS_CHECKOUT_SELF_APPROVED, LIBMNS_CHECKOUT_NO_STATUS])){ 
                                        if(isset($params['return_statuses'][$params['book']->id]) && in_array($params['return_statuses'][$params['book']->id], [LIBMNS_DEFAULT_RETURN])){
                                            ?>
                                                <a title="<?php esc_attr_e('Return', 'library-management-system'); ?>" data-id="<?php echo esc_attr(base64_encode($params['book']->id)); ?>" href="javascript:void(0)"
                                                    class="view-book-btn <?php echo 'owt7_lms_return_requested'; ?>">
                                                    <?php esc_html_e('Return Requested', 'library-management-system'); ?>
                                                </a>
                                            <?php
                                        }else{
                                            ?>
                                                <a title="<?php esc_attr_e('Return', 'library-management-system'); ?>" data-id="<?php echo esc_attr(base64_encode($params['book']->id)); ?>" href="javascript:void(0)"
                                                    class="view-book-btn <?php echo 'owt7_lms_user_do_return'; ?>">
                                                    <?php esc_html_e('Return', 'library-management-system'); ?>
                                                </a>
                                            <?php
                                        } 
                                    } elseif($params['checkout_statuses'][$params['book']->id] == LIBMNS_DEFAULT_CHECKOUT){
                                        ?>
                                        <a title="<?php esc_attr_e('Return', 'library-management-system'); ?>" data-id="<?php echo esc_attr(base64_encode($params['book']->id)); ?>" href="javascript:void(0)"
                                            class="view-book-btn <?php echo 'owt7_lms_checkout_requested'; ?>">
                                            <?php esc_html_e('Checkout Requested', 'library-management-system'); ?>
                                        </a>
                                    <?php 
                                    }
                                }else{
                                    ?>
                                        <a title="<?php esc_attr_e('Checkout', 'library-management-system'); ?>" data-id="<?php echo esc_attr(base64_encode($params['book']->id)); ?>" href="javascript:void(0)"
                                            class="view-book-btn <?php echo 'owt7_lms_user_do_checkout'; ?>">
                                            <?php esc_html_e('Checkout', 'library-management-system'); ?>
                                        </a>
                                    <?php   
                                }
                            }
                        } else { 
                            ?>
                            <a title="<?php esc_attr_e('Login', 'library-management-system'); ?>" href="javascript:void(0)"
                                class="view-book-btn <?php echo 'owt7_lms_do_user_login'; ?>">
                                <?php esc_html_e('Login', 'library-management-system'); ?>
                            </a>
                            <?php
                        }
                        ?>
                    <?php } else{  ?>
                        <a title="<?php esc_attr_e('Out of stock', 'library-management-system'); ?>" data-id="<?php echo esc_attr(base64_encode($params['book']->id)); ?>" href="javascript:void(0);"
                            class="view-book-btn <?php echo 'owt7_lms_book_no_stock'; ?>">
                            <?php esc_html_e('Out of stock', 'library-management-system'); ?>
                        </a>
                    <?php } ?>
                </div>
            </div>
            
        </div>
    </div>
</div>