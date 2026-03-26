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

    <div class="owt7_library_list_bookcases">

        <div class="page-header">
            <div class="breadcrumb"> <?php _e("Library Management", "library-management-system"); ?> &raquo; <span class="active"><?php _e("Bookcases", "library-management-system"); ?></span> </div>
            <div class="page-actions">
                <?php if ( LIBMNS_Admin_FREE::libmns_current_user_can( 'owt7_lms_add_bookcase' ) ) : ?>
                <a href="admin.php?page=owt7_library_bookcases&mod=bookcase&fn=add" class="btn"><span class="dashicons dashicons-plus-alt"></span> <?php _e("Add Bookcase", "library-management-system"); ?></a>
                <?php endif; ?>
                <a href="admin.php?page=owt7_library_bookcases&mod=section&fn=list" class="btn"><span class="dashicons dashicons-list-view"></span> <?php _e("Sections", "library-management-system"); ?></a>
                <?php if ( LIBMNS_Admin_FREE::libmns_current_user_can( 'owt7_lms_add_section' ) ) : ?>
                <a href="admin.php?page=owt7_library_bookcases&mod=section&fn=add" class="btn"><span class="dashicons dashicons-plus-alt"></span> <?php _e("Add Section", "library-management-system"); ?></a>
                <?php endif; ?>
            </div>
        </div>

        <div class="page-container">

            <div class="page-title-row">
                <div class="page-title">
                    <h2><?php _e("Bookcases & Sections", "library-management-system"); ?></h2>
                </div>
                <div class="filter-container">
                </div>
            </div>

            <table class="owt7-lms-table" id="tbl_bookcases_list">
                <thead>
                    <tr>
                        <th><?php _e("Name", "library-management-system"); ?></th>
                        <th><?php _e("Total Section(s)", "library-management-system"); ?></th>
                        <th><?php _e("Total Books", "library-management-system"); ?></th>
                        <th><?php _e("Status", "library-management-system"); ?></th>
                        <th><?php _e("Created at [Y-M-D]", "library-management-system"); ?></th>
                        <th><?php _e("Action", "library-management-system"); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        if(!empty($params['bookcases']) && is_array($params['bookcases'])){
                            foreach($params['bookcases'] as $bookcase){
                                $bc_sections_total = isset( $bookcase->total_sections ) ? (int) $bookcase->total_sections : 0;
                                $bc_books_total = isset( $bookcase->total_books ) ? (int) $bookcase->total_books : 0;
                                ?>
                                <tr>
                                    <td><?php echo ucwords($bookcase->name); ?></td>
                                    <td><?php if ( $bc_sections_total > 0 ) : ?>
                                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=owt7_library_bookcases&mod=section&fn=list&bookcase_id=' . $bookcase->id ) ); ?>" class="owt7-lms-total-sections-badge"><?php echo esc_html( (string) $bc_sections_total ); ?></a>
                                    <?php else : ?>
                                        <span class="owt7-lms-total-sections-badge"><?php echo esc_html( (string) $bc_sections_total ); ?></span>
                                    <?php endif; ?></td>
                                    <td><?php if ( $bc_books_total > 0 ) : ?>
                                        <a href="<?php echo esc_url( admin_url( 'admin.php?page=owt7_library_books&bkcase_id=' . $bookcase->id ) ); ?>" class="owt7-lms-total-books-badge"><?php echo esc_html( (string) $bc_books_total ); ?></a>
                                    <?php else : ?>
                                        <span class="owt7-lms-total-books-badge"><?php echo esc_html( (string) $bc_books_total ); ?></span>
                                    <?php endif; ?></td>
                                    <td>
                                        <?php if($bookcase->status){ ?>
                                        <a href="javascript:void(0);" class="action-btn view-btn">
                                            <?php _e("Active", "library-management-system"); ?>
                                        </a>
                                        <?php }else{ ?>
                                        <a href="javascript:void(0);" class="action-btn delete-btn">
                                            <?php _e("Inactive", "library-management-system"); ?>
                                        </a>
                                        <?php } ?>
                                    </td>
                                    <td><?php echo $bookcase->created_at ? date("Y-m-d", strtotime($bookcase->created_at)) : ''; ?></td>
                                    <td>
                                        <?php if ( LIBMNS_Admin_FREE::libmns_current_user_can( 'owt7_lms_list_bookcases' ) ) : ?>
                                        <a href="admin.php?page=owt7_library_bookcases&mod=bookcase&fn=add&opt=view&id=<?php echo base64_encode($bookcase->id); ?>"
                                            title='<?php _e("View", "library-management-system"); ?>' class="action-btn view-btn">
                                            <span class="dashicons dashicons-visibility"></span>
                                        </a>
                                        <?php endif; ?>
                                        <?php if ( LIBMNS_Admin_FREE::libmns_current_user_can( 'owt7_lms_edit_bookcase' ) ) : ?>
                                        <a href="admin.php?page=owt7_library_bookcases&mod=bookcase&fn=add&opt=edit&id=<?php echo base64_encode($bookcase->id); ?>"
                                            title='<?php _e("Edit", "library-management-system"); ?>' class="action-btn edit-btn">
                                            <span class="dashicons dashicons-edit"></span>
                                        </a>
                                        <?php endif; ?>
                                        <?php if ( LIBMNS_Admin_FREE::libmns_current_user_can( 'owt7_lms_delete_bookcase' ) ) : ?>
                                        <a href="javascript:void(0);" title='<?php _e("Delete", "library-management-system"); ?>' class="action-btn delete-btn action-btn-delete"
                                            data-id="<?php echo base64_encode($bookcase->id) ?>"
                                            data-module="<?php echo base64_encode('bookcase'); ?>">
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

</div>