<?php
/**
 * @link       https://onlinewebtutorblog.com
 * @since      3.5
 * @package    Library_Management_System
 * @subpackage Library_Management_System/public/views/templates
 * @copyright  Copyright (c) 2026, Online Web Tutor
 * @license    GPL-2.0+ https://www.gnu.org/licenses/gpl-2.0.html
 * @author     Online Web Tutor
 */
?>
<div class="book-list-container" id="owt7_lib_book_container">
    <?php
    if(is_array($params['books']) && count($params['books']) > 0){
        foreach($params['books'] as $book){
            $book_name     = isset( $book->name ) ? (string) $book->name : '';
            $category_name = isset( $book->category_name ) ? (string) $book->category_name : '';
    ?>
        <div class="book-card">
            <div class="book-cover">
                <?php if(!empty($book->cover_image)){ ?>
                    <img src="<?php echo $book->cover_image; ?>" alt="<?php echo esc_attr( ucwords( $book_name ) ); ?>">
                <?php }else{ ?>
                    <img src="<?php echo LIBMNS_PLUGIN_URL . 'public/images/default-cover-image.png'; ?>" alt="<?php echo esc_attr( ucwords( $book_name ) ); ?>">
                <?php } ?> 
            </div>
            <div class="book-details">
                <h3 class="book-name"><strong><?php echo esc_html( ucwords( $book_name ) ); ?></strong></h3>
                <p class="book-category"><strong><?php esc_html_e('Category:', 'library-management-system'); ?></strong> <?php echo '' !== $category_name ? esc_html( ucwords( $category_name ) ) : '—'; ?></p>
                <p class="book-author"><strong><?php esc_html_e('Author:', 'library-management-system'); ?></strong> <?php echo LIBMNS_Admin_FREE::libmns_render_comma_tags( $book->author_name ) ?: '—'; ?></p>
                <p class="book-quantity"><strong><?php esc_html_e('Publication Name:', 'library-management-system'); ?></strong>
                    <?php echo LIBMNS_Admin_FREE::libmns_render_comma_tags( $book->publication_name ) ?: '—'; ?></p>
                <p class="book-status">
                    <strong><?php esc_html_e('Status:', 'library-management-system'); ?></strong>
                    <?php if($book->status && $book->stock_quantity > 0){ ?>
                    <a href="javascript:void(0)" class="owt7_lms_front_btns owt7_lms_book_available"><?php esc_html_e('Available', 'library-management-system'); ?></a>
                    <?php } else{ ?>
                    <a href="javascript:void(0)" class="owt7_lms_front_btns owt7_lms_book_not_available"><?php esc_html_e('Not Available', 'library-management-system'); ?></a>
                    <?php } ?>
                </p>
            </div>
            <div class="book-footer">
                <?php if ( is_user_logged_in() && current_user_can( 'access_owt7_library_user_portal' ) ) : ?>
                <a href="<?php echo esc_url( add_query_arg( array( 'page' => 'owt7_library_books_catalogue', 'bid' => $book->id ), admin_url( 'admin.php' ) ) ); ?>" class="view-book-btn owt7-lms-view-portal-link" title="<?php esc_attr_e( 'View', 'library-management-system' ); ?>">
                    <span class="dashicons dashicons-visibility" aria-hidden="true"></span>
                    <span class="owt7-lms-btn-text"><?php esc_html_e( 'View', 'library-management-system' ); ?></span>
                </a>
                <?php else : ?>
                <a title="<?php esc_attr_e( 'View', 'library-management-system' ); ?>" href="javascript:void(0)" class="view-book-btn owt7_lms_view_book_modal" data-book-id="<?php echo esc_attr( $book->id ); ?>">
                    <span class="dashicons dashicons-visibility" aria-hidden="true"></span>
                    <span class="owt7-lms-btn-text"><?php esc_html_e( 'View', 'library-management-system' ); ?></span>
                </a>
                <?php endif; ?>
            </div>
        </div>
        <?php
        }
    }
    ?>
</div>
<div class="pagination">
    <?php
    if ($params['total_pages'] > 1) {
        $current_page = $params['current_page'];
        $total_pages  = $params['total_pages'];
        $query_args   = array();

        if ( ! empty( $params['filter_cat'] ) ) {
            $query_args['cat'] = (int) $params['filter_cat'];
        }
        if ( ! empty( $params['filter_author'] ) ) {
            $query_args['author'] = $params['filter_author'];
        }
        if ( ! empty( $params['filter_search'] ) ) {
            $query_args['search'] = $params['filter_search'];
        }

        // Number of pages to show before/after current
        $range = 2;

        // First page link
        if ($current_page > 1) {
            echo '<a href="' . esc_url(LIBMNS_Public_FREE::owt7_lms_library_page_url(1, $query_args)) . '">First</a>';
            echo '<a href="' . esc_url(LIBMNS_Public_FREE::owt7_lms_library_page_url($current_page - 1, $query_args)) . '">&laquo; Prev</a>';
        }

        // Page numbers
        for ($i = 1; $i <= $total_pages; $i++) {
            if ($i == 1 || $i == $total_pages || ($i >= $current_page - $range && $i <= $current_page + $range)) {
                if ($i == $current_page) {
                    echo '<span class="current-page">' . $i . '</span>';
                } else {
                    echo '<a href="' . esc_url(LIBMNS_Public_FREE::owt7_lms_library_page_url($i, $query_args)) . '">' . $i . '</a>';
                }
            } elseif ($i == 2 && $current_page - $range > 3) {
                echo '<span class="dots">...</span>';
            } elseif ($i == $total_pages - 1 && $current_page + $range < $total_pages - 2) {
                echo '<span class="dots">...</span>';
            }
        }

        // Next & Last page link
        if ($current_page < $total_pages) {
            echo '<a href="' . esc_url(LIBMNS_Public_FREE::owt7_lms_library_page_url($current_page + 1, $query_args)) . '">' . esc_html__('Next', 'library-management-system') . ' &raquo;</a>';
            echo '<a href="' . esc_url(LIBMNS_Public_FREE::owt7_lms_library_page_url($total_pages, $query_args)) . '">' . esc_html__('Last', 'library-management-system') . '</a>';
        }
    }
    ?>
</div>