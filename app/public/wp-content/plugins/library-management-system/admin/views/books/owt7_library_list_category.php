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

    <div class="owt7_library_list_categories">

        <div class="page-header">
            <div class="breadcrumb"> <?php _e("Library System", "library-management-system"); ?> >> <span class="active"><?php _e("List Category", "library-management-system"); ?></span> </div>
            <div class="page-actions">
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=owt7_library_books' ) ); ?>" class="btn"><span class="dashicons dashicons-arrow-left-alt"></span> <?php _e("Back", "library-management-system"); ?></a>
            </div>
        </div>

        <div class="page-container">

            <div class="page-title-row">
                <div class="page-title">
                    <h2><?php _e("Category List", "library-management-system"); ?></h2>
                </div>
                <div class="filter-container">
                </div>
            </div>

            <table class="owt7-lms-table" id="tbl_branches_list">
                <thead>
                    <tr>
                        <th><?php _e("Name", "library-management-system"); ?></th>
                        <th><?php _e("Total Book(s)", "library-management-system"); ?></th>
                        <th><?php _e("Status", "library-management-system"); ?></th>
                        <th><?php _e("Created at [Y-M-D]", "library-management-system"); ?></th>
                        <th><?php _e("Action", "library-management-system"); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        if(!empty($params['categories']) && is_array($params['categories'])){
                            foreach($params['categories'] as $category){
                                $cat_total = isset( $category->total_books ) ? (int) $category->total_books : 0;
                                $books_list_url = admin_url( 'admin.php?page=owt7_library_books&category_id=' . (int) $category->id );
                                ?>
                                <tr>
                                    <td><?php echo ucwords($category->name); ?></td>
                                    <td><?php if ( $cat_total > 0 ) : ?>
                                        <a href="<?php echo esc_url( $books_list_url ); ?>" class="owt7-lms-total-books-badge"><?php echo esc_html( (string) $cat_total ); ?></a>
                                    <?php else : ?>
                                        <span class="owt7-lms-total-books-badge"><?php echo esc_html( (string) $cat_total ); ?></span>
                                    <?php endif; ?></td>
                                    <td>
                                        <?php if($category->status){ ?>
                                        <a href="javascript:void(0);" class="action-btn view-btn">
                                            <?php _e("Active", "library-management-system"); ?>
                                        </a>
                                        <?php }else{ ?>
                                        <a href="javascript:void(0);" class="action-btn delete-btn">
                                            <?php _e("Inactive", "library-management-system"); ?>
                                        </a>
                                        <?php } ?>
                                    </td>
                                    <td><?php echo $category->created_at ? date("Y-m-d", strtotime($category->created_at)) : ''; ?></td>
                                    <td>
                                        <?php if ( LIBMNS_Admin_FREE::libmns_current_user_can( 'owt7_lms_list_categories' ) ) : ?>
                                        <a href="admin.php?page=owt7_library_books&mod=category&fn=add&opt=view&id=<?php echo base64_encode($category->id); ?>"
                                            title='<?php _e("View", "library-management-system"); ?>' class="action-btn view-btn">
                                            <span class="dashicons dashicons-visibility"></span>
                                        </a>
                                        <?php endif; ?>
                                        <?php if ( LIBMNS_Admin_FREE::libmns_current_user_can( 'owt7_lms_edit_category' ) ) : ?>
                                        <a href="admin.php?page=owt7_library_books&mod=category&fn=add&opt=edit&id=<?php echo base64_encode($category->id); ?>"
                                            title='<?php _e("Edit", "library-management-system"); ?>' class="action-btn edit-btn">
                                            <span class="dashicons dashicons-edit"></span>
                                        </a>
                                        <?php endif; ?>
                                        <?php if ( LIBMNS_Admin_FREE::libmns_current_user_can( 'owt7_lms_delete_category' ) ) : ?>
                                        <a href="javascript:void(0);" title='<?php _e("Delete", "library-management-system"); ?>' class="action-btn delete-btn action-btn-delete"
                                            data-id="<?php echo base64_encode($category->id) ?>"
                                            data-module="<?php echo base64_encode('category'); ?>">
                                            <span class="dashicons dashicons-trash"></span>
                                        </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php
                            }
                        }
                    ?>
                </tbody>
            </table>

        </div>
    </div>