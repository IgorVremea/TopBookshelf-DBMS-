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

    <div class="owt7_library_list_sections">

        <div class="page-header">
            <div class="breadcrumb">
                <?php _e("Library System", "library-management-system"); ?> >>
                <span class="active"><?php _e("List Section", "library-management-system"); ?></span>
            </div>
            <div class="page-actions">
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=owt7_library_bookcases' ) ); ?>" class="btn"><span class="dashicons dashicons-arrow-left-alt"></span> <?php _e("Back", "library-management-system"); ?></a>
            </div>
        </div>

        <div class="page-container">

            <div class="page-title-row">
                <div class="page-title">
                    <h2><?php _e("List Section", "library-management-system"); ?></h2>
                </div>
                <div class="filter-container">

                <div id="owt7_library_data_filter_options">
                    <label for="owt7_lms_data_filter"><?php _e("Filter by:", "library-management-system"); ?></label>
                    <select data-module="sections" data-filter-by="bookcase" id="owt7_lms_data_filter" class="owt7_lms_data_filter">
                        <option value=""><?php _e("-- Select Bookcase --", "library-management-system"); ?></option>
                        <option value="all"><?php _e("All", "library-management-system"); ?></option>
                        <?php 
                        $filter_bookcase_id = isset( $params['filter_bookcase_id'] ) ? (int) $params['filter_bookcase_id'] : 0;
                        if(!empty($params['bookcases']) && is_array($params['bookcases'])){
                            foreach($params['bookcases'] as $bookcase){
                                ?>
                                <option value="<?php echo $bookcase->id; ?>"<?php echo ( $filter_bookcase_id > 0 && (int) $bookcase->id === $filter_bookcase_id ) ? ' selected' : ''; ?>><?php echo ucfirst($bookcase->name); ?></option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                    <?php if ( $filter_bookcase_id > 0 ) : ?>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=owt7_library_bookcases&mod=section&fn=list' ) ); ?>" class="btn owt7-lms-clear-filter"><?php _e("Clear filter", "library-management-system"); ?></a>
                    <?php endif; ?>
                </div>

                </div>
            </div>

            <table class="owt7-lms-table" id="tbl_sections_list">
                <thead>
                    <tr>
                        <th><?php _e("Bookcase", "library-management-system"); ?></th>
                        <th><?php _e("Name", "library-management-system"); ?></th>
                        <th><?php _e("Total Books", "library-management-system"); ?></th>
                        <th><?php _e("Status", "library-management-system"); ?></th>
                        <th><?php _e("Created at [Y-M-D]", "library-management-system"); ?></th>
                        <th><?php _e("Action", "library-management-system"); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        ob_start();
                        include_once LIBMNS_PLUGIN_DIR_PATH . 'admin/views/bookcases/templates/owt7_library_sections_list.php';
                        $template = ob_get_contents();
                        ob_end_clean();
                        echo $template;
                    ?>
                </tbody>
            </table>

        </div>
    </div>

</div>
