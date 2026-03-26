<?php
/**
 * @link       https://onlinewebtutorblog.com
 * @since      3.5
 * @package    Library_Management_System
 * @subpackage Library_Management_System/admin/views/books/templates
 * @copyright  Copyright (c) 2026, Online Web Tutor
 * @license    GPL-2.0+ https://www.gnu.org/licenses/gpl-2.0.html
 * @author     Online Web Tutor
 */

if (!empty($params['books']) && is_array($params['books'])) {
    foreach ($params['books'] as $book) {
        $book_not_deletable = isset( $book->has_active_borrow ) && intval( $book->has_active_borrow ) > 0;
        ?>
        <tr class="lms-list-row<?php echo $book_not_deletable ? ' lms-row-not-deletable' : ''; ?>">
            <td class="lms-cell-id">
                <span class="lms-id-badge">#<?php echo esc_html( $book->book_id ); ?></span>
            </td>
            <td class="lms-cell-details">
                <div class="lms-info-block">
                    <div class="lms-info-item"><span class="lms-info-label"><?php _e("Category", "library-management-system"); ?></span><span class="lms-info-value"><?php echo esc_html( $book->category_name ); ?></span></div>
                    <div class="lms-info-item"><span class="lms-info-label"><?php _e("Bookcase", "library-management-system"); ?></span><span class="lms-info-value"><?php echo esc_html( $book->bookcase_name ); ?></span></div>
                    <div class="lms-info-item"><span class="lms-info-label"><?php _e("Section", "library-management-system"); ?></span><span class="lms-info-value"><?php echo ! empty( $book->section_name ) ? esc_html( $book->section_name ) : '<span class="lms-no-section"><i>' . esc_html__( 'No Section', 'library-management-system' ) . '</i></span>'; ?></span></div>
                </div>
            </td>
            <td class="lms-cell-name">
                <?php if ( LIBMNS_Admin_FREE::libmns_current_user_can( 'owt7_lms_list_books' ) ) : ?>
                <a href="<?php echo esc_url( admin_url( 'admin.php?page=owt7_library_books&mod=book&fn=add&opt=view&id=' . base64_encode( $book->id ) ) ); ?>" class="lms-info-value lms-title lms-title-link"><?php echo esc_html( ucwords( $book->name ) ); ?></a>
                <?php else : ?>
                <span class="lms-info-value lms-title"><?php echo esc_html( ucwords( $book->name ) ); ?></span>
                <?php endif; ?>
            </td>
            <td class="lms-cell-stock">
                <span class="lms-stock-badge"><?php echo intval( $book->stock_quantity ); ?></span>
                <?php if ( LIBMNS_Admin_FREE::libmns_current_user_can( 'owt7_lms_list_books' ) ) : ?>
                <div class="lms-stock-actions">
                    <a href="javascript:void(0);" class="owt7_lms_show_book_copies_btn lms-stock-link" data-book-id="<?php echo esc_attr( $book->id ); ?>">
                        <?php esc_html_e( 'View copies', 'library-management-system' ); ?>
                    </a>
                </div>
                <?php endif; ?>
            </td>
            <td class="lms-cell-status">
                <?php if ($book->status) { ?>
                    <span class="lms-status-badge lms-status-success"><?php _e("Active", "library-management-system"); ?></span>
                <?php } else { ?>
                    <span class="lms-status-badge lms-status-inactive"><?php _e("Inactive", "library-management-system"); ?></span>
                <?php } ?>
            </td>
            <td class="lms-cell-actions">
                <div class="lms-action-wrap">
                <?php if ( LIBMNS_Admin_FREE::libmns_current_user_can( 'owt7_lms_list_books' ) ) : ?>
                <a href="admin.php?page=owt7_library_books&mod=book&fn=add&opt=view&id=<?php echo base64_encode($book->id); ?>"
                   title='<?php _e("View", "library-management-system"); ?>' class="action-btn view-btn">
                    <span class="dashicons dashicons-visibility"></span>
                </a>
                <?php endif; ?>
                <?php if ( LIBMNS_Admin_FREE::libmns_current_user_can( 'owt7_lms_edit_book' ) ) : ?>
                <a href="admin.php?page=owt7_library_books&mod=book&fn=add&opt=edit&id=<?php echo base64_encode($book->id); ?>"
                   title='<?php _e("Edit", "library-management-system"); ?>' class="action-btn edit-btn">
                    <span class="dashicons dashicons-edit"></span>
                </a>
                <?php endif; ?>
                <?php if ( LIBMNS_Admin_FREE::libmns_current_user_can( 'owt7_lms_list_books' ) ) : ?>
                <a href="javascript:void(0);" title='<?php _e("Book Copies", "library-management-system"); ?>'
                   class="action-btn view-btn owt7_lms_show_book_copies_btn" data-book-id="<?php echo esc_attr( $book->id ); ?>">
                    <span class="dashicons dashicons-index-card"></span>
                </a>
                <?php endif; ?>
                <?php if ( LIBMNS_Admin_FREE::libmns_current_user_can( 'owt7_lms_delete_book' ) && ! $book_not_deletable ) : ?>
                <a href="javascript:void(0);" title='<?php _e("Delete", "library-management-system"); ?>'
                   class="action-btn delete-btn action-btn-delete" data-id="<?php echo base64_encode($book->id) ?>"
                   data-module="<?php echo base64_encode('book'); ?>">
                    <span class="dashicons dashicons-trash"></span>
                </a>
                <?php endif; ?>
                </div>
            </td>
        </tr>
        <?php
    }
}
?>
