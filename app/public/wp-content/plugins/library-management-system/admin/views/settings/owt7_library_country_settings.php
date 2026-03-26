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
            <div class="breadcrumb"> <?php _e('Library System', 'library-management-system'); ?> >> <span class="active"><?php _e('Country & Currency', 'library-management-system'); ?></span> </div>
            <div class="page-actions">
                <?php
                if(get_option( 'owt7_lms_country' )){
                    ?>
                    <a href="javascript:void(0)" id="owt7_lms_country_modal" class="btn"><span class="dashicons dashicons-admin-site"></span> <?php _e('Update Data', 'library-management-system'); ?></a>
                    <?php
                }else{
                    ?>
                    <a href="javascript:void(0)" id="owt7_lms_country_modal" class="btn"><span class="dashicons dashicons-admin-site"></span> <?php _e('Add Data', 'library-management-system'); ?></a>
                    <?php
                }
                ?>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=owt7_library_settings#general' ) ); ?>" class="btn">
                    <span class="dashicons dashicons-arrow-left-alt"></span> 
                    <?php _e('Back', 'library-management-system'); ?>
                </a>
            </div>
        </div>

        <div class="page-container">

            <div class="page-title">
                <h2><?php _e('Country & Currency', 'library-management-system'); ?></h2>
            </div>

            <table class="owt7-lms-table">
                <thead>
                    <tr>
                        <th><?php _e('Country', 'library-management-system'); ?></th>
                        <th><?php _e('Currency', 'library-management-system'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <td>
                        <span class="owt7_lms_settings_values">
                            <?php 
                                $country = get_option( 'owt7_lms_country' );
                                echo !empty($country) ? $country : "--"; 
                            ?>
                        </span>
                    </td>
                    <td>
                        <span class="owt7_lms_settings_values">
                            <?php 
                                $currency = get_option( 'owt7_lms_currency' );
                                echo !empty($currency) ? $currency : "--"; 
                            ?>
                        </span>
                    </td>
                </tbody>
            </table>
        </div>
    </div>

</div>


<div class="owt7_lms_modal_section">
    <?php
    ob_start();
    $fileName = "owt7_mdl_country_settings";
    include_once LIBMNS_PLUGIN_DIR_PATH . "admin/views/settings/modals/{$fileName}.php";
    $template = ob_get_contents();
    ob_end_clean();
    echo $template;
    ?>
</div>
