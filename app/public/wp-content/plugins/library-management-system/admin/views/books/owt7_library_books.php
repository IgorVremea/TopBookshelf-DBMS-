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

    <div class="owt7_library_list_books">

        <div class="page-header">
            <div class="breadcrumb"> <?php _e("Library Management", "library-management-system"); ?> &raquo; <span
                    class="active"><?php _e("Books", "library-management-system"); ?></span> </div>
            <div class="page-actions">
                <?php if ( LIBMNS_Admin_FREE::libmns_current_user_can( 'owt7_lms_add_category' ) ) : ?>
                <a href="admin.php?page=owt7_library_books&mod=category&fn=add"
                    class="btn"><span class="dashicons dashicons-plus-alt"></span> <?php _e("Add Category", "library-management-system"); ?></a>
                <?php endif; ?>
                <?php if ( LIBMNS_Admin_FREE::libmns_current_user_can( 'owt7_lms_list_categories' ) ) : ?>
                <a href="admin.php?page=owt7_library_books&mod=category&fn=list"
                    class="btn"><span class="dashicons dashicons-list-view"></span> <?php _e("Categories", "library-management-system"); ?></a>
                <?php endif; ?>
                <?php if ( LIBMNS_Admin_FREE::libmns_current_user_can( 'owt7_lms_add_book' ) ) : ?>
                <a href="admin.php?page=owt7_library_books&mod=book&fn=add"
                    class="btn"><span class="dashicons dashicons-book"></span> <?php _e("Add Book", "library-management-system"); ?></a>
                <?php endif; ?>
            </div>
        </div>

        <div class="page-container">

            <div class="page-title-row">
                <div class="page-title">
                    <h2><?php _e("Book Catalog", "library-management-system"); ?></h2>
                </div>
                <div class="filter-container">

                <div id="owt7_library_data_filter_options">
                    <label for="owt7_lms_category_filter"><?php _e("Filter by:", "library-management-system"); ?></label>
                    <select data-module="books" data-filter-by="category" id="owt7_lms_data_filter" class="owt7_lms_data_filter">
                        <option value="all"><?php _e("-- All --", "library-management-system"); ?></option>
                        <?php 
                        if(!empty($params['categories']) && is_array($params['categories'])){
                            foreach($params['categories'] as $category){
                                ?>
                                <option value="<?php echo $category->id; ?>"><?php echo ucfirst($category->name); ?></option>
                                <?php
                            }
                        }
                        ?>
                    </select>
                </div>

                </div>
            </div>

            <table class="owt7-lms-table" id="tbl_books_list">
                <thead>
                    <tr>
                        <th><?php _e("Book ID", "library-management-system"); ?></th>
                        <th><?php _e("Basic Details", "library-management-system"); ?></th>
                        <th><?php _e("Name", "library-management-system"); ?></th>
                        <th><?php _e("Total Copies", "library-management-system"); ?></th>
                        <th><?php _e("Status", "library-management-system"); ?></th>
                        <th><?php _e("Action", "library-management-system"); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        ob_start();
                        include_once LIBMNS_PLUGIN_DIR_PATH . 'admin/views/books/templates/owt7_library_books_list.php';
                        $template = ob_get_contents();
                        ob_end_clean();
                        echo $template;
                    ?>
                </tbody>
            </table>

            <?php include_once LIBMNS_PLUGIN_DIR_PATH . 'admin/views/books/templates/owt7_library_book_copies_modal.php'; ?>

        </div>
    </div>
