<?php
/**
 * @link       https://onlinewebtutorblog.com
 * @since      3.5
 * @package    Library_Management_System
 * @subpackage Library_Management_System/public
 * @copyright  Copyright (c) 2026, Online Web Tutor
 * @license    GPL-2.0+ https://www.gnu.org/licenses/gpl-2.0.html
 * @author     Online Web Tutor
 */

class LIBMNS_Public_FREE {

	private $plugin_name;

	private $version;

	private $table_activator;

	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		require_once LIBMNS_PLUGIN_DIR_PATH . 'includes/class-libmns-activator.php';
        $this->table_activator = new LIBMNS_Activator_FREE();
	}


	/**
	 * Get library base URL.
	 *
	 * @return string
	 */
	public static function owt7_lms_library_base_url() {
		return home_url( 'wp-library-books' );
	}

	/**
	 * Get URL for a single book detail page (query string: ?bookId=).
	 *
	 * @param int $book_id Book ID (numeric).
	 * @return string
	 */
	public static function owt7_lms_book_detail_url( $book_id ) {
		return add_query_arg( 'bookId', absint( $book_id ), home_url( 'wp-library-books' ) );
	}

	/**
	 * Get URL for a paginated library page (query string: ?pageNo=).
	 * Uses pageNo to avoid conflict with WordPress reserved "page" query var.
	 *
	 * @param int   $page_no    Page number.
	 * @param array $query_args Extra query arguments to preserve current filters.
	 * @return string
	 */
	public static function owt7_lms_library_page_url( $page_no, $query_args = array() ) {
		$page_no = max( 1, absint( $page_no ) );
		$query_args = is_array( $query_args ) ? $query_args : array();
		$query_args['pageNo'] = $page_no;
		return add_query_arg( $query_args, home_url( 'wp-library-books' ) );
	}

	private function is_library_public_page() {
		$shortcodes = array( 'owt7_library_books', 'owt7_user_books_history' );
		$post_id   = get_queried_object_id();
		if ( ! $post_id ) {
			return false;
		}
		$post = get_post( $post_id );
		if ( ! $post || empty( $post->post_content ) ) {
			return false;
		}
		foreach ( $shortcodes as $tag ) {
			if ( has_shortcode( $post->post_content, $tag ) ) {
				return true;
			}
		}
		return false;
	}

	public function enqueue_styles() {
		if ( ! $this->is_library_public_page() ) {
			return;
		}
		wp_enqueue_style( 'dashicons' );
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/library-management-system-public.css', array( 'dashicons' ), $this->version, 'all' );
		wp_enqueue_style( "owt7-lms-toastr-css", plugin_dir_url( __FILE__ ) . 'css/toastr.min.css', array(), $this->version, 'all' );
		$inline_css = $this->get_public_view_inline_css();
		if ( ! empty( $inline_css ) ) {
			wp_add_inline_style( $this->plugin_name, $inline_css );
		}
	}

	/**
	 * Build inline CSS for the public library book list.
	 * Library settings apply under .owt7-lms-public-library (wp-library-books/).
	 *
	 * @return string CSS string or empty if no overrides.
	 */
	private function get_public_view_inline_css() {
		$library_css = $this->build_public_view_css_for_settings(
			get_option( 'owt7_lms_public_view_settings', array() ),
			'.owt7-lms-public-library'
		);
		$out = array();
		if ( $library_css !== '' ) {
			$out[] = '/* Public View (Library) */ ' . $library_css;
		}
		return implode( ' ', $out );
	}

	/**
	 * Build CSS rules for one public view settings array, scoped by a parent selector.
	 *
	 * @param array  $s      Settings (cards_per_row, heading_font_size, body_font_size, view_btn_placement, card_bg_color, view_btn_padding, view_btn_font_size).
	 * @param string $prefix Parent selector (e.g. .owt7-lms-public-library or .owt7-lms-public-books-store).
	 * @return string CSS rules or empty string.
	 */
	private function build_public_view_css_for_settings( $s, $prefix ) {
		if ( empty( $s ) ) {
			return '';
		}
		$n    = isset( $s['cards_per_row'] ) ? absint( $s['cards_per_row'] ) : 3;
		$n    = $n >= 1 && $n <= 6 ? $n : 3;
		$gap  = 20;
		$width = $n > 1 ? 'calc((100% - ' . ( $gap * ( $n - 1 ) ) . 'px) / ' . $n . ')' : '100%';
		$rules = array();
		$sel = $prefix . ' .book-list-container';
		$card_decl = 'width: ' . $width . '; flex: 0 0 ' . $width . '; box-sizing: border-box;';
		if ( ! empty( $s['card_bg_color'] ) ) {
			$card_bg = sanitize_hex_color( $s['card_bg_color'] );
			if ( $card_bg ) {
				$card_decl .= ' background-color: ' . $card_bg . ';';
			}
		}
		$rules[] = $sel . ' .book-card { ' . $card_decl . ' }';
		if ( ! empty( $s['heading_font_size'] ) ) {
			$fs = preg_replace( '/[^0-9a-z.%\s\-]/i', '', $s['heading_font_size'] );
			$rules[] = $prefix . ' .book-list-heading, ' . $sel . ' .book-details h3.book-name { font-size: ' . $fs . '; }';
		}
		if ( ! empty( $s['body_font_size'] ) ) {
			$fs = preg_replace( '/[^0-9a-z.%\s\-]/i', '', $s['body_font_size'] );
			$rules[] = $sel . ' .book-details p { font-size: ' . $fs . '; }';
		}
		if ( ! empty( $s['view_btn_placement'] ) && in_array( $s['view_btn_placement'], array( 'left', 'center', 'right' ), true ) ) {
			$rules[] = $sel . ' .book-footer { text-align: ' . $s['view_btn_placement'] . '; }';
		}
		if ( ! empty( $s['view_btn_padding'] ) ) {
			$pad = trim( $s['view_btn_padding'] );
			if ( preg_match( '/^[\d\s]+$/', $pad ) ) {
				$parts = array_map( 'trim', explode( ' ', $pad ) );
				$pad = implode( 'px ', array_map( 'absint', $parts ) ) . 'px';
			}
			$pad = preg_replace( '/[^0-9a-z.%\s\-]/i', '', $pad );
			if ( $pad !== '' ) {
				$rules[] = $sel . ' .view-book-btn { padding: ' . $pad . '; }';
			}
		}
		if ( ! empty( $s['view_btn_font_size'] ) ) {
			$fs = preg_replace( '/[^0-9a-z.%\s\-]/i', '', $s['view_btn_font_size'] );
			if ( $fs !== '' ) {
				$rules[] = $sel . ' .view-book-btn { font-size: ' . $fs . '; }';
			}
		}
		if ( ! empty( $s['view_btn_color'] ) && preg_match( '/^#[0-9a-fA-F]{6}$/', $s['view_btn_color'] ) ) {
			$rules[] = $sel . ' .view-book-btn:not(.owt7-lms-checkout-portal-link) { background-color: ' . $s['view_btn_color'] . '; }';
			$rules[] = $sel . ' .view-book-btn:not(.owt7-lms-checkout-portal-link):hover { background-color: ' . $this->libmns_adjust_brightness( $s['view_btn_color'], -20 ) . '; }';
		}
		if ( ! empty( $s['checkout_btn_color'] ) && preg_match( '/^#[0-9a-fA-F]{6}$/', $s['checkout_btn_color'] ) ) {
			$rules[] = $sel . ' .view-book-btn.owt7-lms-checkout-portal-link { background-color: ' . $s['checkout_btn_color'] . '; }';
			$rules[] = $sel . ' .view-book-btn.owt7-lms-checkout-portal-link:hover { background-color: ' . $this->libmns_adjust_brightness( $s['checkout_btn_color'], -20 ) . '; }';
		}
		if ( empty( $rules ) ) {
			return '';
		}
		return implode( ' ', $rules );
	}

	/**
	 * Adjust hex color brightness (positive = lighter, negative = darker).
	 *
	 * @param string $hex   Hex color e.g. #1d2065.
	 * @param int    $steps Steps to add to each RGB component.
	 * @return string Hex color.
	 */
	private function libmns_adjust_brightness( $hex, $steps ) {
		$hex = ltrim( $hex, '#' );
		if ( strlen( $hex ) !== 6 ) {
			return $hex;
		}
		$r = max( 0, min( 255, hexdec( substr( $hex, 0, 2 ) ) + $steps ) );
		$g = max( 0, min( 255, hexdec( substr( $hex, 2, 2 ) ) + $steps ) );
		$b = max( 0, min( 255, hexdec( substr( $hex, 4, 2 ) ) + $steps ) );
		return '#' . sprintf( '%02x%02x%02x', $r, $g, $b );
	}

	public function enqueue_scripts() {
		if ( ! $this->is_library_public_page() ) {
			return;
		}
		wp_enqueue_script( "owt7-lms-sweetalert2", 'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js', array(), '11', true );
		wp_enqueue_script( "owt7-lms-jspdf", 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js', array(), '2.5.1', true );
		wp_enqueue_script( "owt7-lms-jspdf-autotable", 'https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.8.2/jspdf.plugin.autotable.min.js', array( 'owt7-lms-jspdf' ), '3.8.2', true );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/library-management-system-public.js', array( 'jquery', 'owt7-lms-sweetalert2', 'owt7-lms-jspdf-autotable' ), $this->version, false );
		wp_enqueue_script( "owt7-lms-toastr", plugin_dir_url( __FILE__ ) . 'js/toastr.min.js', array( 'jquery' ), $this->version, false );
		wp_localize_script( $this->plugin_name, "owt7_library", array(
			"ajaxurl"        => admin_url( "admin-ajax.php" ),
			"ajax_nonce"     => wp_create_nonce( 'owt7_library_actions' ),
			"page_no"        => 1,
			"pdf_unsupported" => __( 'PDF export is not available.', 'library-management-system' ),
			"pdf_headings"   => array(
				"borrowed" => __( 'My Borrowed Books', 'library-management-system' ),
				"returned" => __( 'Books Returned', 'library-management-system' ),
			),
		) );
	}


	// All books Shortcode Handler
	public function owt7_library_all_books_shortcode($template){

		global $wpdb;

		// Single book is shown in modal via AJAX; no separate page for bookId.

		// Get WP User Borrowed Books
		$u_id = get_current_user_id();
		$book_ids = [];
		$return_book_ids = [];
		$checkout_statuses = [];
		$return_statuses = [];
		
		$books_borrowed = $wpdb->get_results(
			"SELECT book_id, checkout_status FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' ) . " WHERE u_id = {$u_id} AND wp_user = 1 AND status = 1"
		);

		$books_returned = $wpdb->get_results(
			"SELECT book_id, return_status FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'book_return' ) . " WHERE u_id = {$u_id} AND wp_user = 1 AND status = 1"
		);

		if(!empty($books_borrowed)){
			$book_ids = array_column($books_borrowed, "book_id");
			foreach($books_borrowed as $book){
				$checkout_statuses[$book->book_id] = $book->checkout_status;
			}
		}

		if(!empty($books_returned)){
			$return_book_ids = array_column($books_returned, "book_id");
			foreach($books_returned as $book){
				$return_statuses[$book->book_id] = $book->return_status;
			}
		}

		// LMS Public Page Settings
			$settings = get_option( "owt7_lms_public_settings", array() );
			$settings = is_array( $settings ) ? $settings : array();
			$portal   = LIBMNS_Admin_FREE::libmns_get_library_user_portal_settings();

			$books_per_page = isset( $settings['show_books_per_page'] ) && ! empty( $settings['show_books_per_page'] ) ? absint( $settings['show_books_per_page'] ) : LIBMNS_DEFAULT_SHOW_BOOKS;

			// Pagination: wp-library-books?pageNo= or legacy ?p_no=
			$current_page = isset( $_GET['pageNo'] ) ? max( 1, absint( $_GET['pageNo'] ) ) : ( isset( $_GET['p_no'] ) ? max( 1, (int) $_GET['p_no'] ) : LIBMNS_DEFAULT_PAGE_NUMBER );
			$offset       = ( $current_page - 1 ) * $books_per_page;

			$show_category_filter = ! empty( $portal['library_user_enable_category_filter'] );
			$show_author_filter   = ! empty( $portal['library_user_enable_author_filter'] );
			$show_search_filter   = false;

			$filter_cat    = $show_category_filter && isset( $_GET['cat'] ) ? absint( $_GET['cat'] ) : 0;
			$filter_author = $show_author_filter && isset( $_GET['author'] ) ? sanitize_text_field( wp_unslash( $_GET['author'] ) ) : '';
			$filter_search = $show_search_filter && isset( $_GET['search'] ) ? sanitize_text_field( wp_unslash( $_GET['search'] ) ) : '';

			// All categories/authors used by the shared catalogue filters.
			$categories = $wpdb->get_results(
				"SELECT category.*, (SELECT count(*) FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'books' ) . " as book WHERE book.category_id = category.id AND book.status = 1) as total_books from " . LIBMNS_Table_Helper_FREE::get_table_name( 'categories' ) . " as category WHERE status = 1"
			);
			$authors = $wpdb->get_col(
				"SELECT DISTINCT TRIM(author_name) AS author_name FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'books' ) . " WHERE status = 1 AND author_name IS NOT NULL AND TRIM(author_name) <> '' ORDER BY author_name ASC"
			);

			// When logged in with a role allowed for frontend (wp_lms_roles), show tabs. Subscriber is not allowed by default; use Library User (owt7_library_user) for admin portal instead.
			$show_user_tabs     = false;
			$books_borrowed_ids = array();
			if ( is_user_logged_in() ) {
				$user = wp_get_current_user();
				$logged_in_wp_roles = isset( $user->roles ) ? (array) $user->roles : array();
				$lms_saved_roles = isset( $settings['wp_lms_roles'] ) && is_array( $settings['wp_lms_roles'] ) ? $settings['wp_lms_roles'] : array();
				foreach ( $logged_in_wp_roles as $u_role ) {
					if ( in_array( $u_role, $lms_saved_roles, true ) ) {
						$show_user_tabs = true;
						break;
					}
				}
				if ( $show_user_tabs ) {
					$u_id = get_current_user_id();
					$books_borrowed = $wpdb->get_results(
						"SELECT book_id FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' ) . " WHERE u_id = {$u_id} AND wp_user = 1 AND status = 1 AND is_self_checkout = 1 AND (checkout_status = 1 OR checkout_status = 2)"
					);
					if ( ! empty( $books_borrowed ) ) {
						$books_borrowed_ids = array_column( $books_borrowed, 'book_id' );
					}
				}
			}

			$library_where_clauses = array( 'book.status = 1' );
			$prepare_args          = array();

			if ( $show_user_tabs && ! empty( $books_borrowed_ids ) ) {
				$exclude_placeholder    = implode( ',', array_map( 'absint', $books_borrowed_ids ) );
				$library_where_clauses[] = "book.id NOT IN ({$exclude_placeholder})";
			}
			if ( $filter_cat > 0 ) {
				$library_where_clauses[] = 'book.category_id = %d';
				$prepare_args[]          = $filter_cat;
			}
			if ( $filter_author !== '' ) {
				$library_where_clauses[] = 'book.author_name LIKE %s';
				$prepare_args[]          = '%' . $wpdb->esc_like( $filter_author ) . '%';
			}
			if ( $filter_search !== '' ) {
				$like = '%' . $wpdb->esc_like( $filter_search ) . '%';
				$library_where_clauses[] = '(book.isbn LIKE %s OR book.name LIKE %s OR book.amount LIKE %s OR book.author_name LIKE %s)';
				$prepare_args[] = $like;
				$prepare_args[] = $like;
				$prepare_args[] = $like;
				$prepare_args[] = $like;
			}

			$library_where = implode( ' AND ', $library_where_clauses );
			$tbl_books_lib = LIBMNS_Table_Helper_FREE::get_table_name( 'books' );
			$books_base_sql = "SELECT book.*, (SELECT category.name FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'categories' ) . " as category WHERE category.id = book.category_id LIMIT 1) as category_name, (SELECT bkcase.name FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'bookcases' ) . " as bkcase WHERE bkcase.id = book.bookcase_id LIMIT 1) as bookcase_name, (SELECT section.name FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'sections' ) . " as section WHERE section.id = book.bookcase_section_id LIMIT 1) as section_name FROM {$tbl_books_lib} as book WHERE {$library_where}";
			$count_sql = "SELECT COUNT(*) FROM {$tbl_books_lib} as book WHERE {$library_where}";

			if ( ! empty( $prepare_args ) ) {
				$all_books = (int) $wpdb->get_var( $wpdb->prepare( $count_sql, $prepare_args ) );
				$books     = $wpdb->get_results( $wpdb->prepare( $books_base_sql . " LIMIT %d OFFSET %d", array_merge( $prepare_args, array( $books_per_page, $offset ) ) ) );
			} else {
				$all_books = (int) $wpdb->get_var( $count_sql );
				$books     = $wpdb->get_results( $books_base_sql . " LIMIT {$books_per_page} OFFSET {$offset}" );
			}
			$total_pages = $books_per_page > 0 ? (int) ceil( $all_books / $books_per_page ) : 0;

			$extra_params = array(
				"books" => $books,
				"categories" => $categories,
				"authors" => is_array( $authors ) ? $authors : array(),
				"total_pages" => $total_pages,
				"current_page" => $current_page,
				"book_ids" => $book_ids,
				"checkout_statuses" => $checkout_statuses,
				"return_book_ids" => $return_book_ids,
				"return_statuses" => $return_statuses,
				"filter_cat" => $filter_cat,
				"filter_author" => $filter_author,
				"filter_search" => $filter_search,
				"show_category_filter" => $show_category_filter,
				"show_author_filter" => $show_author_filter,
				"show_search_filter" => $show_search_filter,
			);

			$borrowed_books_list = array();
			$returned_books_list = array();
			$total_borrowed_pages = 0;
			$total_returned_pages = 0;
			$current_page_borrowed = $current_page;
			$current_page_returned = $current_page;

			if ( $show_user_tabs ) {
				$u_id = get_current_user_id();
				$tbl_borrow = LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' );
				$tbl_books = LIBMNS_Table_Helper_FREE::get_table_name( 'books' );
				$tbl_category = LIBMNS_Table_Helper_FREE::get_table_name( 'categories' );
				$tbl_bookcase = LIBMNS_Table_Helper_FREE::get_table_name( 'bookcases' );
				$tbl_section = LIBMNS_Table_Helper_FREE::get_table_name( 'sections' );
				$tbl_return = LIBMNS_Table_Helper_FREE::get_table_name( 'book_return' );
				$wp_users = $wpdb->prefix . 'users';

				// My Borrowed Books: from borrow table with issue date, expected return date, issuer
				$all_borrowed_count = (int) $wpdb->get_var(
					"SELECT COUNT(*) FROM {$tbl_borrow} borrow INNER JOIN {$tbl_books} book ON book.id = borrow.book_id WHERE borrow.u_id = {$u_id} AND borrow.wp_user = 1 AND borrow.status = 1 AND (borrow.checkout_status = 1 OR borrow.checkout_status = 2)"
				);
				$total_borrowed_pages = ceil( $all_borrowed_count / $books_per_page );
				$page_borrowed = 1;
				$page_returned = 1;
				$offset_borrowed = ( $page_borrowed - 1 ) * $books_per_page;
				$offset_returned = ( $page_returned - 1 ) * $books_per_page;
				$borrowed_books_list = $wpdb->get_results(
					"SELECT borrow.id AS borrow_record_id, borrow.accession_number, borrow.checkout_status, borrow.is_self_checkout,
					borrow.created_at AS issue_date, borrow.return_date AS expected_return_date,
					(CASE WHEN borrow.checkout_status = 1 THEN 'admin' WHEN borrow.checkout_status = 2 OR borrow.is_self_checkout = 1 THEN 'self' ELSE '' END) AS issuer_type,
					book.*,
					(SELECT name FROM {$tbl_category} WHERE id = book.category_id LIMIT 1) AS category_name,
					(SELECT name FROM {$tbl_bookcase} WHERE id = book.bookcase_id LIMIT 1) AS bookcase_name,
					(SELECT name FROM {$tbl_section} WHERE id = book.bookcase_section_id LIMIT 1) AS section_name
					FROM {$tbl_borrow} borrow INNER JOIN {$tbl_books} book ON book.id = borrow.book_id
					WHERE borrow.u_id = {$u_id} AND borrow.wp_user = 1 AND borrow.status = 1 AND (borrow.checkout_status = 1 OR borrow.checkout_status = 2)
					ORDER BY borrow.created_at DESC LIMIT {$books_per_page} OFFSET {$offset_borrowed}"
				);

				// Books Returned: from return table with issue date, return date, issuer
				$all_returned_count = (int) $wpdb->get_var(
					"SELECT COUNT(*) FROM {$tbl_return} rt WHERE rt.u_id = {$u_id} AND rt.wp_user = 1 AND (rt.return_status = 1 OR rt.return_status = 2)"
				);
				$total_returned_pages = ceil( $all_returned_count / $books_per_page );
				$tbl_late_fine = LIBMNS_Table_Helper_FREE::get_table_name( 'book_late_fine' );
				$returned_books_list = $wpdb->get_results(
					"SELECT rt.id AS return_record_id, COALESCE(NULLIF(TRIM(rt.accession_number), ''), br.accession_number) AS accession_number, rt.has_fine_status, rt.created_at AS return_date,
					br.created_at AS issue_date, br.return_date AS expected_return_date,
					(SELECT display_name FROM {$wp_users} WHERE ID = rt.u_id LIMIT 1) AS returned_by_name,
					(CASE WHEN br.checkout_status = 1 THEN 'admin' WHEN br.checkout_status = 2 OR br.is_self_checkout = 1 THEN 'self' ELSE '' END) AS issuer_type,
					(SELECT fine_amount FROM {$tbl_late_fine} WHERE return_id = rt.id AND status = 1 LIMIT 1) AS fine_amount,
					(SELECT has_paid FROM {$tbl_late_fine} WHERE return_id = rt.id AND status = 1 LIMIT 1) AS fine_has_paid,
					book.*,
					(SELECT name FROM {$tbl_category} WHERE id = book.category_id LIMIT 1) AS category_name,
					(SELECT name FROM {$tbl_bookcase} WHERE id = book.bookcase_id LIMIT 1) AS bookcase_name,
					(SELECT name FROM {$tbl_section} WHERE id = book.bookcase_section_id LIMIT 1) AS section_name
					FROM {$tbl_return} rt
					INNER JOIN {$tbl_borrow} br ON br.borrow_id = rt.borrow_id AND br.u_id = rt.u_id
					INNER JOIN {$tbl_books} book ON book.id = rt.book_id
					WHERE rt.u_id = {$u_id} AND rt.wp_user = 1 AND (rt.return_status = 1 OR rt.return_status = 2)
					ORDER BY rt.created_at DESC LIMIT {$books_per_page} OFFSET {$offset_returned}"
				);

				$extra_params['show_user_tabs'] = true;
				$extra_params['borrowed_books_list'] = $borrowed_books_list;
				$extra_params['returned_books_list'] = $returned_books_list;
				$extra_params['total_borrowed_pages'] = $total_borrowed_pages;
				$extra_params['total_returned_pages'] = $total_returned_pages;
				$extra_params['current_page_borrowed'] = $page_borrowed;
				$extra_params['current_page_returned'] = $page_returned;
			} else {
				$extra_params['show_user_tabs'] = false;
			}

			return $this->owt7_library_include_template_file( "owt7_library_books", $extra_params );
	}

	// User Books History Tabs - Shortcode Handler
	public function owt7_library_user_books_history_shortcode(){

		global $wpdb;

		if ( is_user_logged_in() ) { 

			$user = wp_get_current_user();
			$logged_in_wp_roles = isset($user->roles) ? (array) $user->roles : [];
			// LMS Public Page Settings
			$settings = get_option("owt7_lms_public_settings");
			$lms_saved_roles = isset($settings['wp_lms_roles']) && is_array($settings['wp_lms_roles']) ? $settings['wp_lms_roles'] : [];
			$pageAccess = false;
			foreach ( $logged_in_wp_roles as $u_role ) {
				if ( in_array( $u_role, $lms_saved_roles, true ) ) {
					$pageAccess = true;
					break;
				}
			}

			if ($pageAccess) {

				$u_id = get_current_user_id();

				// LMS Public Page Settings
				$settings = get_option("owt7_lms_public_settings");

				$books_per_page = isset($settings['show_books_per_page']) && !empty($settings['show_books_per_page']) ? $settings['show_books_per_page'] : LIBMNS_DEFAULT_SHOW_BOOKS; // Per page Data
				
				$current_page = isset($_GET['p_no']) ? (int)$_GET['p_no'] : LIBMNS_DEFAULT_PAGE_NUMBER;
				$offset = ($current_page - 1) * $books_per_page;

				$borrowed_books_list = [];
				$total_borrowed_pages = 0;
				$returned_books_list = [];
				$total_returned_pages = 0;

				// Books Borrow History
				$books_borrowed = $wpdb->get_results(
					"SELECT book_id FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' ) . " WHERE u_id = {$u_id} AND wp_user = 1 AND status = 1 AND is_self_checkout = 1 AND (checkout_status = 1 OR checkout_status = 2)"
				);

				// Books Borrowed by User
				if(!empty($books_borrowed)){
					
					$books_borrowed_ids = array_column($books_borrowed, "book_id");
					$books_borrowed_ids_placeholder = implode(',', array_fill(0, count($books_borrowed_ids), '%d'));
					
					// Get Total Pages with the condition
					$all_borrowed_books = $wpdb->get_var(
						$wpdb->prepare(
							"SELECT COUNT(*) FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'books' ) . " 
							WHERE status = 1 AND id IN ($books_borrowed_ids_placeholder)",
							$books_borrowed_ids
						)
					);

					// Books Per Page Query with the condition
					$borrowed_books_list = $wpdb->get_results(
						$wpdb->prepare(
							"SELECT book.*, 
								(SELECT category.name FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'categories' ) . " as category WHERE category.id = book.category_id LIMIT 1) as category_name, 
								(SELECT bkcase.name FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'bookcases' ) . " as bkcase WHERE bkcase.id = book.bookcase_id LIMIT 1) as bookcase_name, 
								(SELECT section.name FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'sections' ) . " as section WHERE section.id = book.bookcase_section_id LIMIT 1) as section_name 
							FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'books' ) . " as book 
							WHERE book.id IN ($books_borrowed_ids_placeholder)
							LIMIT %d OFFSET %d",
							array_merge($books_borrowed_ids, [$books_per_page, $offset])
						)
					);

					// Calculate total pages
					$total_borrowed_pages = ceil($all_borrowed_books / $books_per_page);
				}

				// Books Return History
				$books_returned = $wpdb->get_results(
					"SELECT book_id FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'book_return' ) . " WHERE u_id = {$u_id} AND wp_user = 1 AND is_self_return = 1 AND (return_status = 1 OR return_status = 2)"
				);

				// Books Returned by User
				if(!empty($books_returned)){
					$books_returned_ids = array_column($books_returned, "book_id");
					$books_returned_ids_placeholder = implode(',', array_fill(0, count($books_returned_ids), '%d'));
					
					// Get Total Pages with the condition
					$all_returned_books = $wpdb->get_var(
						$wpdb->prepare(
							"SELECT COUNT(*) FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'books' ) . " 
							WHERE status = 1 AND id IN ($books_returned_ids_placeholder)",
							$books_returned_ids
						)
					);

					// Books Per Page Query with the condition
					$returned_books_list = $wpdb->get_results(
						$wpdb->prepare(
							"SELECT book.*, 
								(SELECT category.name FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'categories' ) . " as category WHERE category.id = book.category_id LIMIT 1) as category_name, 
								(SELECT bkcase.name FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'bookcases' ) . " as bkcase WHERE bkcase.id = book.bookcase_id LIMIT 1) as bookcase_name, 
								(SELECT section.name FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'sections' ) . " as section WHERE section.id = book.bookcase_section_id LIMIT 1) as section_name 
							FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'books' ) . " as book 
							WHERE book.id IN ($books_returned_ids_placeholder)
							LIMIT %d OFFSET %d",
							array_merge($books_returned_ids, [$books_per_page, $offset])
						)
					);

					// Calculate total pages
					$total_returned_pages = ceil($all_returned_books / $books_per_page);
				}

				return $this->owt7_library_include_template_file(
					"owt7_library_user_books_history", 
					compact(
						"borrowed_books_list", 
						"returned_books_list", 
						"total_borrowed_pages", 
						"total_returned_pages",
						"current_page"
					)
				);
			}else{
				
				return $this->owt7_library_include_template_file("errors/owt7_library_403_page");
			}
		}else{

			return $this->owt7_library_include_template_file("errors/owt7_library_403_page");
		}
	}
	// Helper function
	public function owt7_library_include_template_file($template, $lib_params = array()){

		ob_start();
		$params = $lib_params;
		include_once LIBMNS_PLUGIN_DIR_PATH . 'public/views/' . $template . ".php";
		$template = ob_get_contents();
		ob_end_clean();

		return $template;
	}

	// Send Response in JSON
	private function json($response = array())
    {
		$data = isset($response[2]) ? $response[2] : array();
		$ar = array('sts' => $response[0], 'msg' => $response[1], 'arr' => $data);
        print_r(json_encode($ar));
        die;
    }

	// Redirect user after login
	public function owt7_library_redirect_after_login($redirect_to, $request, $user){
		// Check if a redirect URL is stored in session
        if (isset($_SESSION['lms_redirect_url'])) { 
            // Get redirect URL from session and unset session variable
            $redirect_url = $_SESSION['lms_redirect_url'];
            unset($_SESSION['lms_redirect_url']);
            return $redirect_url;
        }
        return $redirect_to;
	}

	// Manage Stock
	private function owt7_library_manage_books_stock($book_id, $action){
		global $wpdb;
		$book_data = $wpdb->get_row(
			"SELECT * FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'books' ) . " WHERE id = {$book_id}"
		);
		if(!empty($book_data)){
			$stock_quantity = $book_data->stock_quantity;
			if($action == "plus"){
				$stock_quantity = $stock_quantity + 1;
				$wpdb->update(LIBMNS_Table_Helper_FREE::get_table_name( 'books' ), [
					"stock_quantity" => $stock_quantity
				], [
					"id" => $book_id
				]);
				return true;
			} elseif($action == "minus"){
				if($stock_quantity > 0){
					$stock_quantity = $stock_quantity - 1;
					$wpdb->update(LIBMNS_Table_Helper_FREE::get_table_name( 'books' ), [
						"stock_quantity" => $stock_quantity
					], [
						"id" => $book_id
					]);
					return true;
				}else{
					return false;
				}
			}
		}else{
			return false;
		}
	}

	/**
	 * Allocate next available accession for a book and mark copy as borrowed.
	 *
	 * @param int $book_id Books table id (numeric).
	 * @return string|null Accession number or null.
	 */
	private function owt7_library_allocate_next_available_accession( $book_id ) {
		global $wpdb;
		$book_id = absint( $book_id );
		if ( ! $book_id ) {
			return null;
		}
		$book = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT book_id FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'books' ) . " WHERE id = %d LIMIT 1",
				$book_id
			),
			OBJECT
		);
		if ( ! $book || empty( $book->book_id ) ) {
			return null;
		}
		$tbl_copies = LIBMNS_Table_Helper_FREE::get_table_name( 'data_books_copies' );
		$copy = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT id, accession_number FROM `{$tbl_copies}` WHERE book_id = %s AND status = %s ORDER BY accession_number ASC LIMIT 1",
				$book->book_id,
				'available'
			),
			OBJECT
		);
		if ( ! $copy || empty( $copy->accession_number ) ) {
			return null;
		}
		$updated = $wpdb->update(
			$tbl_copies,
			array( 'status' => 'borrowed' ),
			array( 'id' => $copy->id ),
			array( '%s' ),
			array( '%d' )
		);
		return ( $updated !== false ) ? $copy->accession_number : null;
	}

	/**
	 * Release an accession (set copy status to available).
	 *
	 * @param string $accession_number Accession number to release.
	 * @return bool True if updated.
	 */
	private function owt7_library_release_accession( $accession_number ) {
		global $wpdb;
		if ( ! is_string( $accession_number ) || trim( $accession_number ) === '' ) {
			return false;
		}
		$tbl_copies = LIBMNS_Table_Helper_FREE::get_table_name( 'data_books_copies' );
		$updated = $wpdb->update(
			$tbl_copies,
			array( 'status' => 'available' ),
			array( 'accession_number' => trim( $accession_number ) ),
			array( '%s' ),
			array( '%s' )
		);
		return $updated !== false && $updated >= 0;
	}

	/**
	 * Last 7 digits of timestamp (milliseconds) for unique ID suffix.
	 *
	 * @param string $prefix Module prefix (e.g. LIBMNS_BOOK_BORROW_PREFIX).
	 * @return string Prefix + 7-digit suffix.
	 */
	private function owt7_lms_generate_id_timestamp_suffix( $prefix ) {
		$ts = sprintf( '%.0f', microtime( true ) * 1000 );
		$suffix = substr( $ts, -7 );
		return $prefix . $suffix;
	}

	// Generate Unique Identifier (legacy).
	private function owt7_library_generate_unique_identifier($prefix = 'LMS', $length = 6) {
		if ($length <= 0) {
			return $prefix;
		}
	
		$characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
	
		return $prefix . $randomString;
	}

	/**
	 * Compute fine amount for one borrow based on return condition (used by user return flow).
	 *
	 * @param object $borrow_row Row from book_borrow.
	 * @param object|null $book_row Row with id, amount or null.
	 * @param string $return_condition One of normal_return, lost_book, late_return.
	 * @return array [ fine_amount, extra_days, fine_type ]
	 */
	private function owt7_library_return_fine_for_borrow( $borrow_row, $book_row, $return_condition ) {
		global $wpdb;
		$tbl_books = LIBMNS_Table_Helper_FREE::get_table_name( 'books' );
		if ( $book_row === null && ! empty( $borrow_row->book_id ) ) {
			$book_row = $wpdb->get_row( $wpdb->prepare( "SELECT id, amount FROM {$tbl_books} WHERE id = %d LIMIT 1", $borrow_row->book_id ) );
		}
		switch ( $return_condition ) {
			case LIBMNS_RETURN_CONDITION_NORMAL:
				return array( 0, 0, 'late_return' );
			case LIBMNS_RETURN_CONDITION_LOST:
				$amount = isset( $book_row->amount ) && is_numeric( $book_row->amount ) ? floatval( $book_row->amount ) : 0;
				return array( $amount, 0, 'lost_book' );
			case LIBMNS_RETURN_CONDITION_LATE:
				$borrow_days = (int) $borrow_row->borrows_days;
				$return_date = date( 'Y-m-d' );
				$borrow_date = date( 'Y-m-d', strtotime( $borrow_row->created_at ) );
				$date_diff = (new DateTime( $borrow_date ))->diff( new DateTime( $return_date ) )->days;
				$extra_days = 0;
				$fine = 0.0;
				if ( $date_diff > $borrow_days ) {
					$extra_days = $date_diff - $borrow_days;
					$per_day = (float) get_option( 'owt7_lms_late_fine_currency', 0 );
					$fine = $extra_days * $per_day;
				}
				return array( $fine, $extra_days, 'late_return' );
			default:
				return array( 0, 0, 'late_return' );
		}
	}

	private function owt7_library_get_expected_return_date_for_borrow( $borrow_row ) {
		if ( ! empty( $borrow_row->return_date ) ) {
			$timestamp = strtotime( $borrow_row->return_date );
			if ( $timestamp ) {
				return date( 'Y-m-d', $timestamp );
			}
		}

		if ( ! empty( $borrow_row->created_at ) && isset( $borrow_row->borrows_days ) ) {
			$borrow_timestamp = strtotime( $borrow_row->created_at );
			if ( $borrow_timestamp ) {
				$days = max( 0, absint( $borrow_row->borrows_days ) );
				return date( 'Y-m-d', strtotime( "+{$days} days", $borrow_timestamp ) );
			}
		}

		return '';
	}

	private function owt7_library_validate_user_return_condition( $borrow_row, $return_condition ) {
		$expected_return_date = $this->owt7_library_get_expected_return_date_for_borrow( $borrow_row );
		if ( '' === $expected_return_date ) {
			return array( true, '' );
		}

		$today = current_time( 'Y-m-d' );
		if ( LIBMNS_RETURN_CONDITION_NORMAL === $return_condition && strtotime( $today ) > strtotime( $expected_return_date ) ) {
			return array( false, __( 'Selected return date is beyond the expected return date. Please choose Late return instead of Normal return.', 'library-management-system' ) );
		}

		return array( true, '' );
	}

	// Ajax Handler
	public function owt7_library_front_request_handler(){

		//ini_set("display_errors", 1);

		global $wpdb;

        $param = isset( $_REQUEST['param'] ) ? trim( $_REQUEST['param'] ) : "";

		if ( isset( $_REQUEST['owt7_lms_nonce'] ) && wp_verify_nonce( $_REQUEST['owt7_lms_nonce'], 'owt7_library_actions' ) ) {

			if ( !empty( $param ) ) {

				if ( $param == "owt7_lms_user_return_fine_preview" ) {
					$u_id = get_current_user_id();
					$book_id = isset( $_REQUEST['book_id'] ) ? sanitize_text_field( wp_unslash( (string) $_REQUEST['book_id'] ) ) : '';
					$book_id = $book_id !== '' ? (string) base64_decode( $book_id, true ) : '';
					$book_id = $book_id !== '' ? sanitize_text_field( $book_id ) : '';
					$return_condition = isset( $_REQUEST['owt7_return_condition'] ) ? sanitize_text_field( wp_unslash( (string) $_REQUEST['owt7_return_condition'] ) ) : LIBMNS_RETURN_CONDITION_NORMAL;
					$valid = array( LIBMNS_RETURN_CONDITION_NORMAL, LIBMNS_RETURN_CONDITION_LOST, LIBMNS_RETURN_CONDITION_LATE );
					if ( $u_id <= 0 || $book_id === '' || ! in_array( $return_condition, $valid, true ) ) {
						wp_send_json( $this->json( array( 0, __( 'Invalid request.', 'library-management-system' ), array() ) ) );
						return;
					}
					$tbl_borrow = LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' );
					$tbl_books  = LIBMNS_Table_Helper_FREE::get_table_name( 'books' );
					$borrow = $wpdb->get_row( $wpdb->prepare(
						"SELECT b.*, (SELECT amount FROM {$tbl_books} WHERE id = b.book_id LIMIT 1) AS book_amount FROM {$tbl_borrow} b WHERE b.book_id = %s AND b.status = 1 AND b.u_id = %d AND b.wp_user = 1 LIMIT 1",
						$book_id, $u_id
					) );
					if ( ! $borrow ) {
						wp_send_json( $this->json( array( 0, __( 'No active borrow found.', 'library-management-system' ), array() ) ) );
						return;
					}
					$book_row = (object) array( 'id' => $borrow->book_id, 'amount' => isset( $borrow->book_amount ) ? $borrow->book_amount : 0 );
					list( $total_fine, , ) = $this->owt7_library_return_fine_for_borrow( $borrow, $book_row, $return_condition );
					$currency = get_option( 'owt7_lms_currency', '' );
					wp_send_json( $this->json( array( 1, '', array( 'total_fine' => $total_fine, 'currency' => $currency ) ) ) );
					return;
				}

				if ( $param === 'owt7_lms_get_book_detail_modal' ) {
					$book_id = isset( $_REQUEST['book_id'] ) ? absint( $_REQUEST['book_id'] ) : 0;
					if ( $book_id <= 0 ) {
						wp_send_json( $this->json( array( 0, __( 'Invalid book.', 'library-management-system' ), array() ) ) );
						return;
					}
					$tbl_books = LIBMNS_Table_Helper_FREE::get_table_name( 'books' );
					$tbl_cat   = LIBMNS_Table_Helper_FREE::get_table_name( 'categories' );
					$tbl_bkcase = LIBMNS_Table_Helper_FREE::get_table_name( 'bookcases' );
					$tbl_section = LIBMNS_Table_Helper_FREE::get_table_name( 'sections' );
					$book = $wpdb->get_row(
						$wpdb->prepare(
							"SELECT book.*, (SELECT name FROM {$tbl_cat} WHERE id = book.category_id LIMIT 1) AS category_name, (SELECT name FROM {$tbl_bkcase} WHERE id = book.bookcase_id LIMIT 1) AS bookcase_name, (SELECT name FROM {$tbl_section} WHERE id = book.bookcase_section_id LIMIT 1) AS section_name FROM {$tbl_books} AS book WHERE book.id = %d LIMIT 1",
							$book_id
						)
					);
					if ( empty( $book ) ) {
						wp_send_json( $this->json( array( 0, __( 'Book not found.', 'library-management-system' ), array() ) ) );
						return;
					}
					$book_ids = array();
					$checkout_statuses = array();
					$return_book_ids = array();
					$return_statuses = array();
					$u_id = get_current_user_id();
					if ( $u_id > 0 ) {
						$books_borrowed = $wpdb->get_results(
							"SELECT book_id, checkout_status FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' ) . " WHERE u_id = {$u_id} AND wp_user = 1 AND status = 1"
						);
						$books_returned = $wpdb->get_results(
							"SELECT book_id, return_status FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'book_return' ) . " WHERE u_id = {$u_id} AND wp_user = 1 AND status = 1"
						);
						if ( ! empty( $books_borrowed ) ) {
							$book_ids = array_column( $books_borrowed, 'book_id' );
							foreach ( $books_borrowed as $b ) {
								$checkout_statuses[ $b->book_id ] = $b->checkout_status;
							}
						}
						if ( ! empty( $books_returned ) ) {
							foreach ( $books_returned as $b ) {
								$return_statuses[ $b->book_id ] = $b->return_status;
							}
						}
					}
					$status_label = ( ! empty( $book->status ) && (int) $book->stock_quantity > 0 )
						? __( 'Available', 'library-management-system' )
						: __( 'Not Available', 'library-management-system' );
					$total_copies_available = (int) $book->stock_quantity;
					$params = array(
						'book'                   => $book,
						'book_ids'               => $book_ids,
						'checkout_statuses'      => $checkout_statuses,
						'return_statuses'        => $return_statuses,
						'status_label'           => $status_label,
						'total_copies_available'  => $total_copies_available,
					);
					$html = $this->owt7_library_include_template_file( 'templates/owt7_library_single_book_modal_content', $params );
					$this->json( array( 1, '', array( 'html' => $html ) ) );
					return;
				}

				if ( $param == "owt7_lms_front_category_filter" ) {

					$settings        = get_option( "owt7_lms_public_settings", array() );
					$settings        = is_array( $settings ) ? $settings : array();
					$portal          = LIBMNS_Admin_FREE::libmns_get_library_user_portal_settings();
					$books_per_page  = isset( $settings['show_books_per_page'] ) && ! empty( $settings['show_books_per_page'] ) ? absint( $settings['show_books_per_page'] ) : LIBMNS_DEFAULT_SHOW_BOOKS;
					$current_page    = isset( $_REQUEST['pageNo'] ) ? max( 1, absint( $_REQUEST['pageNo'] ) ) : ( isset( $_REQUEST['p_no'] ) ? max( 1, absint( $_REQUEST['p_no'] ) ) : LIBMNS_DEFAULT_PAGE_NUMBER );
					$offset          = ( $current_page - 1 ) * $books_per_page;
					$show_cat_filter = ! empty( $portal['library_user_enable_category_filter'] );
					$show_author_filter = ! empty( $portal['library_user_enable_author_filter'] );
					$show_search_filter = false;
					$filter_cat      = $show_cat_filter && isset( $_REQUEST['cat'] ) ? absint( $_REQUEST['cat'] ) : 0;
					$filter_author   = $show_author_filter && isset( $_REQUEST['author'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['author'] ) ) : '';
					$filter_search   = $show_search_filter && isset( $_REQUEST['search'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['search'] ) ) : '';

					$categories = $wpdb->get_results(
						"SELECT category.*, (SELECT count(*) FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'books' ) . " as book WHERE book.category_id = category.id AND book.status = 1) as total_books from " . LIBMNS_Table_Helper_FREE::get_table_name( 'categories' ) . " as category WHERE status = 1"
					);

					$where_clauses = array( 'book.status = 1' );
					$prepare_args  = array();

					$exclude_borrowed_ids = array();
					if ( is_user_logged_in() ) {
						$user = wp_get_current_user();
						$logged_in_wp_roles = isset( $user->roles ) ? (array) $user->roles : array();
						$lms_saved_roles = isset( $settings['wp_lms_roles'] ) && is_array( $settings['wp_lms_roles'] ) ? $settings['wp_lms_roles'] : array();
						$user_has_tabs = false;
						foreach ( $logged_in_wp_roles as $u_role ) {
							if ( in_array( $u_role, $lms_saved_roles, true ) ) {
								$user_has_tabs = true;
								break;
							}
						}
						if ( $user_has_tabs ) {
							$u_id = get_current_user_id();
							$borrowed = $wpdb->get_results(
								"SELECT book_id FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' ) . " WHERE u_id = {$u_id} AND wp_user = 1 AND status = 1 AND is_self_checkout = 1 AND (checkout_status = 1 OR checkout_status = 2)"
							);
							if ( ! empty( $borrowed ) ) {
								$exclude_borrowed_ids = array_column( $borrowed, 'book_id' );
							}
						}
					}
					if ( ! empty( $exclude_borrowed_ids ) ) {
						$exclude_placeholder = implode( ',', array_map( 'absint', $exclude_borrowed_ids ) );
						$where_clauses[] = "book.id NOT IN ({$exclude_placeholder})";
					}
					if ( $filter_cat > 0 ) {
						$where_clauses[] = 'book.category_id = %d';
						$prepare_args[]  = $filter_cat;
					}
					if ( $filter_author !== '' ) {
						$where_clauses[] = 'book.author_name LIKE %s';
						$prepare_args[]  = '%' . $wpdb->esc_like( $filter_author ) . '%';
					}
					if ( $filter_search !== '' ) {
						$like = '%' . $wpdb->esc_like( $filter_search ) . '%';
						$where_clauses[] = '(book.isbn LIKE %s OR book.name LIKE %s OR book.amount LIKE %s OR book.author_name LIKE %s)';
						$prepare_args[]  = $like;
						$prepare_args[]  = $like;
						$prepare_args[]  = $like;
						$prepare_args[]  = $like;
					}

					$where_sql  = implode( ' AND ', $where_clauses );
					$books_base = "SELECT book.*, (SELECT category.name FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'categories' ) . " as category WHERE category.id = book.category_id LIMIT 1) as category_name, (SELECT bkcase.name FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'bookcases' ) . " as bkcase WHERE bkcase.id = book.bookcase_id LIMIT 1) as bookcase_name, (SELECT section.name FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'sections' ) . " as section WHERE section.id = book.bookcase_section_id LIMIT 1) as section_name from " . LIBMNS_Table_Helper_FREE::get_table_name( 'books' ) . " as book WHERE " . $where_sql;
					$count_sql  = "SELECT COUNT(*) from " . LIBMNS_Table_Helper_FREE::get_table_name( 'books' ) . " as book WHERE " . $where_sql;

					if ( ! empty( $prepare_args ) ) {
						$total_books = (int) $wpdb->get_var( $wpdb->prepare( $count_sql, $prepare_args ) );
						$books       = $wpdb->get_results( $wpdb->prepare( $books_base . " LIMIT %d OFFSET %d", array_merge( $prepare_args, array( $books_per_page, $offset ) ) ) );
					} else {
						$total_books = (int) $wpdb->get_var( $count_sql );
						$books       = $wpdb->get_results( $books_base . " LIMIT {$books_per_page} OFFSET {$offset}" );
					}

					$total_pages = $books_per_page > 0 ? max( 1, (int) ceil( $total_books / $books_per_page ) ) : 1;

					$book_ids          = array();
					$checkout_statuses = array();
					$return_book_ids   = array();
					$return_statuses   = array();
					$u_id              = get_current_user_id();
					if ( $u_id > 0 ) {
						$books_borrowed = $wpdb->get_results(
							"SELECT book_id, checkout_status FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' ) . " WHERE u_id = {$u_id} AND wp_user = 1 AND status = 1"
						);
						$books_returned = $wpdb->get_results(
							"SELECT book_id, return_status FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'book_return' ) . " WHERE u_id = {$u_id} AND wp_user = 1 AND status = 1"
						);
						if ( ! empty( $books_borrowed ) ) {
							$book_ids = array_column( $books_borrowed, 'book_id' );
							foreach ( $books_borrowed as $borrowed_book ) {
								$checkout_statuses[ $borrowed_book->book_id ] = $borrowed_book->checkout_status;
							}
						}
						if ( ! empty( $books_returned ) ) {
							$return_book_ids = array_column( $books_returned, 'book_id' );
							foreach ( $books_returned as $returned_book ) {
								$return_statuses[ $returned_book->book_id ] = $returned_book->return_status;
							}
						}
					}

					ob_start();
					$params['books']             = $books;
					$params['categories']        = $categories;
					$params['total_pages']       = $total_pages;
					$params['current_page']      = $current_page;
					$params['book_ids']          = $book_ids;
					$params['checkout_statuses'] = $checkout_statuses;
					$params['return_book_ids']   = $return_book_ids;
					$params['return_statuses']   = $return_statuses;
					$params['filter_cat']        = $filter_cat;
					$params['filter_author']     = $filter_author;
					$params['filter_search']     = $filter_search;
					$template_file = "owt7_library_all_books";
					include_once LIBMNS_PLUGIN_DIR_PATH . "public/views/templates/{$template_file}.php";
					$template = ob_get_contents();
					ob_end_clean();

					$query_args = array();
					if ( $filter_cat > 0 ) {
						$query_args['cat'] = $filter_cat;
					}
					if ( $filter_author !== '' ) {
						$query_args['author'] = $filter_author;
					}
					if ( $filter_search !== '' ) {
						$query_args['search'] = $filter_search;
					}
					if ( $current_page > 1 ) {
						$query_args['pageNo'] = $current_page;
					}

					$response = [
						1,
						! empty( $books ) ? __( "Books found", "library-management-system" ) : __( "No books found", "library-management-system" ),
						[
							"template" => $template,
							"total_pages" => $total_pages,
							"url" => add_query_arg( $query_args, self::owt7_lms_library_base_url() ),
						]
					];
				} elseif ( $param == "owt7_lms_do_user_login" ) {

					$encodedURL = isset($_REQUEST['encodedURL']) ? $_REQUEST['encodedURL'] : "";
					$_SESSION['owt7_lms_redirect_url'] = $encodedURL;
					
					$response = [
						0, 
						"WP Login",
						[
							"login_url" => wp_login_url()
						]
					];
				} elseif ( $param == "owt7_lms_do_user_checkout" ) {
					
					$book_id = isset( $_REQUEST['book_id'] ) ? sanitize_text_field( wp_unslash( trim( (string) $_REQUEST['book_id'] ) ) ) : "" ;
					$u_id = get_current_user_id();
					$is_library_user = LIBMNS_Roles_Helper_FREE::libmns_user_has_library_user_role();
					
					// LMS Public Page Settings (Library User portal uses its own settings when user has library_user role)
					$settings = get_option("owt7_lms_public_settings");
					if ( $is_library_user ) {
						$portal = LIBMNS_Admin_FREE::libmns_get_library_user_portal_settings();
						$days_count = isset( $portal['library_user_checkout_days'] ) && (int) $portal['library_user_checkout_days'] > 0 ? (int) $portal['library_user_checkout_days'] : LIBMNS_DEFAULT_BORROW_DAYS;
						$enable_more_books_library_user = false;
					} else {
						$days_count = isset($settings['self_borrow_books_days']) && !empty($settings['self_borrow_books_days']) ? $settings['self_borrow_books_days'] : LIBMNS_DEFAULT_BORROW_DAYS;
						$enable_more_books_library_user = null;
					}
					if ( ! isset( $days_count ) ) {
						$days_count = isset($settings['self_borrow_books_days']) && !empty($settings['self_borrow_books_days']) ? $settings['self_borrow_books_days'] : LIBMNS_DEFAULT_BORROW_DAYS;
					}
					
					$branch_id = LIBMNS_DEFAULT_BRANCH_ID; // Default LMS Branch
					
					$book_id = base64_decode($book_id);
					
					// Book Data
					$book_data = $wpdb->get_row(
						"SELECT name, category_id from " . LIBMNS_Table_Helper_FREE::get_table_name( 'books' ). " WHERE id = {$book_id}"
					);

					if ( $is_library_user ) {
						$response = [ 0, __( 'Library Users cannot self-checkout books in the free version. Please contact the administrator.', 'library-management-system' ) ];
					} elseif(!empty($book_data)){

						$tbl_fine = LIBMNS_Table_Helper_FREE::get_table_name( 'book_late_fine' );
						$has_unpaid_fine = (int) $wpdb->get_var( $wpdb->prepare(
							"SELECT COUNT(*) FROM {$tbl_fine} WHERE u_id = %d AND status = 1 AND has_paid = %s",
							$u_id,
							'1'
						) );
						if ( $has_unpaid_fine > 0 ) {
							$response = [ 0, __( 'You cannot borrow a new book until all fines are cleared.', 'library-management-system' ) ];
						} else {
						$has_book_borrowed = $wpdb->get_row(
							"SELECT * FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' ) . " WHERE u_id = {$u_id} AND book_id = {$book_id} AND status = 1 AND wp_user = 1"
						);

						if(!empty($has_book_borrowed)){
							$response = [
								0, 
								__("Failed, You already have this book.", "library-management-system")
							];
						}else{

							$enable_public_checkout = isset($settings['enable_public_checkout']) && !empty($settings['enable_public_checkout']);
							// Library User (admin portal: Books Catalogue, etc.) can always submit checkout requests; they go to admin approval when self-checkout is off. Frontend-only users still require enable_public_checkout.
							$allow_checkout = $enable_public_checkout;
							$enable_more_books = ( $enable_more_books_library_user !== null ) ? $enable_more_books_library_user : ( isset($settings['enable_more_books_checkout']) && !empty($settings['enable_more_books_checkout']) );

							if ( ! $allow_checkout ) {
								$response = [
									0,
									__("Self-checkout is not enabled.", "library-management-system")
								];
							} else {
								// If multiple books not allowed, user may only have 0 current borrows to checkout another
								$current_borrow_count = (int) $wpdb->get_var(
									"SELECT COUNT(*) FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' ) . " WHERE u_id = {$u_id} AND status = 1 AND wp_user = 1"
								);
								if ( $current_borrow_count >= 1 && ! $enable_more_books ) {
									$response = [
										0,
										__("You can borrow only one book at a time. Return your current book to checkout another.", "library-management-system")
									];
								} else {
								// Library User portal: self-checkout enabled means no admin approval
								if ( LIBMNS_Roles_Helper_FREE::libmns_user_has_library_user_role() ) {
									$admin_approval = true;
								} else {
									$admin_approval = isset($settings['enable_admin_approves_self_checkout']) && !empty($settings['enable_admin_approves_self_checkout']) ? $settings['enable_admin_approves_self_checkout'] : false;
								}

								if ( isset( $response ) && $response[0] === 0 ) {
									// Role check failed, already set
								} elseif ( $admin_approval ) {
									// Admin approval needed: no allocation or stock change until approved
									$borrow_prefix = defined( 'LIBMNS_BOOK_BORROW_PREFIX' ) ? LIBMNS_BOOK_BORROW_PREFIX : 'LMSBB';
									$borrow_id = $this->owt7_lms_generate_id_timestamp_suffix( $borrow_prefix );
									$currentDate = new DateTime();
									$currentDate->modify("+" . $days_count . " days");
									$newDate = $currentDate->format('Y-m-d');

									$wpdb->insert(LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' ), [
										"borrow_id" => $borrow_id,
										"category_id" => $book_data->category_id,
										"wp_user" => 1,
										"book_id" => $book_id,
										"branch_id" => $branch_id,
										"u_id" => $u_id,
										"is_self_checkout" => 1,
										"checkout_status" => LIBMNS_DEFAULT_CHECKOUT,
										"borrows_days" => $days_count,
										"return_date" => $newDate,
										"status" => 1
									]);
echo $wpdb->last_error;
									if ( $wpdb->insert_id > 0 ) {
										$response = [
											1,
											__("Successfully, Borrow Request Submitted to Admin for Approval.", "library-management-system")
										];
									} else {
										$response = [
											0,
											__("Failed to borrow book", "library-management-system")
										];
									}
								} else {
									$accession_number = $this->owt7_library_allocate_next_available_accession( $book_id );
									if ( $accession_number === null ) {
										$response = [
											0,
											__("Failed, Book is Out of Stock (no available copy).", "library-management-system")
										];
									} elseif ( $this->owt7_library_manage_books_stock( $book_id, "minus" ) ) {
										$borrow_prefix = defined( 'LIBMNS_BOOK_BORROW_PREFIX' ) ? LIBMNS_BOOK_BORROW_PREFIX : 'LMSBB';
										$borrow_id = $this->owt7_lms_generate_id_timestamp_suffix( $borrow_prefix );
										$currentDate = new DateTime();
										$currentDate->modify("+" . $days_count . " days");
										$newDate = $currentDate->format('Y-m-d');

										$wpdb->insert(LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' ), [
											"borrow_id" => $borrow_id,
											"category_id" => $book_data->category_id,
											"wp_user" => 1,
											"book_id" => $book_id,
											"accession_number" => $accession_number,
											"branch_id" => $branch_id,
											"u_id" => $u_id,
											"is_self_checkout" => 1,
											"checkout_status" => LIBMNS_CHECKOUT_SELF_APPROVED,
											"borrows_days" => $days_count,
											"return_date" => $newDate,
											"status" => 1
										]);

										if ( $wpdb->insert_id > 0 ) {
											$response = [
												1,
												__("Successfully, Book borrowed", "library-management-system")
											];
										} else {
											$this->owt7_library_manage_books_stock( $book_id, "plus" );
											$this->owt7_library_release_accession( $accession_number );
											$response = [
												0,
												__("Failed to borrow book", "library-management-system")
											];
										}
									} else {
										$this->owt7_library_release_accession( $accession_number );
										$response = [
											0,
											__("Failed, Book is Out of Stock.", "library-management-system")
										];
									}
								}
								}
							}
						}
						}
					}else{
						$response = [
							0, 
							__("No book found", "library-management-system")
						];
					}
				} elseif ( $param == "owt7_lms_do_user_return" ) {

					$book_id = isset( $_REQUEST['book_id'] ) ? sanitize_text_field( wp_unslash( trim( (string) $_REQUEST['book_id'] ) ) ) : "";
					$return_condition = isset( $_REQUEST['owt7_return_condition'] ) ? sanitize_text_field( wp_unslash( (string) $_REQUEST['owt7_return_condition'] ) ) : LIBMNS_RETURN_CONDITION_NORMAL;
					$return_remark = isset( $_REQUEST['owt7_return_remark'] ) ? sanitize_textarea_field( wp_unslash( $_REQUEST['owt7_return_remark'] ) ) : '';
					$wp_user_id = get_current_user_id();
					$is_library_user = LIBMNS_Roles_Helper_FREE::libmns_user_has_library_user_role();

					$book_id = base64_decode( $book_id, true );
					$book_id = is_numeric( $book_id ) ? absint( $book_id ) : 0;
					$valid_conditions = array( LIBMNS_RETURN_CONDITION_NORMAL, LIBMNS_RETURN_CONDITION_LOST, LIBMNS_RETURN_CONDITION_LATE );
					if ( ! in_array( $return_condition, $valid_conditions, true ) ) {
						$return_condition = LIBMNS_RETURN_CONDITION_NORMAL;
					}

					if ( $book_id <= 0 ) {
						$response = [ 0, __( "Invalid book.", "library-management-system" ) ];
					} elseif ( $wp_user_id <= 0 ) {
						$response = [ 0, __( "Please log in to return a book.", "library-management-system" ) ];
					} elseif ( $is_library_user ) {
						$response = [ 0, __( 'Library Users cannot self-return books in the free version. Please contact the administrator.', 'library-management-system' ) ];
					} else {
						$settings = get_option( "owt7_lms_public_settings" );
						$admin_approval = isset( $settings['enable_admin_approves_self_return'] ) && ! empty( $settings['enable_admin_approves_self_return'] );

						if ( isset( $response ) && $response[0] === 0 ) {
							// role check failed
						} else {
							// book_borrow.u_id is library user id; resolve current WP user to library user id
							$tbl_users = LIBMNS_Table_Helper_FREE::get_table_name( 'users' );
							$library_user_id = $wpdb->get_var( $wpdb->prepare(
								"SELECT id FROM {$tbl_users} WHERE wp_user_id = %d AND status = 1 LIMIT 1",
								$wp_user_id
							) );
							if ( empty( $library_user_id ) ) {
								$response = [ 0, __( "Your account is not linked to a library user. Please contact the administrator.", "library-management-system" ) ];
							} else {
							$library_user_id = (int) $library_user_id;
							$book_borrow_details = $wpdb->get_row( $wpdb->prepare(
								"SELECT * FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' ) . " WHERE book_id = %d AND status = 1 AND u_id = %d AND wp_user = 1",
								$book_id, $library_user_id
							) );

							if ( empty( $book_borrow_details ) ) {
								$response = [ 0, __( "You do not have this book borrowed.", "library-management-system" ) ];
							} else {
								list( $is_valid_return_condition, $return_validation_message ) = $this->owt7_library_validate_user_return_condition( $book_borrow_details, $return_condition );
								if ( ! $is_valid_return_condition ) {
									$response = [ 0, $return_validation_message ];
								} else {
									$has_requested = $wpdb->get_row( $wpdb->prepare(
										"SELECT * FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'book_return' ) . " WHERE borrow_id = %s AND status = 1",
										$book_borrow_details->borrow_id
									) );

									if ( ! empty( $has_requested ) ) {
										$response = $admin_approval ? [ 0, __( "Already Return Request Submitted", "library-management-system" ) ] : [ 0, __( "Book already Returned", "library-management-system" ) ];
									} else {
										$tbl_books = LIBMNS_Table_Helper_FREE::get_table_name( 'books' );
										$book_row = $wpdb->get_row( $wpdb->prepare( "SELECT id, amount FROM {$tbl_books} WHERE id = %d LIMIT 1", $book_borrow_details->book_id ) );
										list( $total_late_fine, $extra_days, $fine_type ) = 
										
										$this->owt7_library_return_fine_for_borrow( $book_borrow_details, $book_row, $return_condition );
										$has_fine = $total_late_fine > 0;
										
										$release_stock = ( $return_condition !== LIBMNS_RETURN_CONDITION_LOST ) && ! $admin_approval;

										if ( $release_stock && ! $this->owt7_library_manage_books_stock( $book_borrow_details->book_id, "plus" ) ) {
											$response = [ 0, __( "Could not update stock.", "library-management-system" ) ];
										} else {
											$return_accession = isset( $book_borrow_details->accession_number ) ? trim( (string) $book_borrow_details->accession_number ) : '';
											if ( $release_stock && $return_accession !== '' ) {
												$this->owt7_library_release_accession( $return_accession );
											}

											$return_prefix = defined( 'LIBMNS_BOOK_RETURN_PREFIX' ) ? LIBMNS_BOOK_RETURN_PREFIX : 'LMSBR';
											$return_code = $this->owt7_lms_generate_id_timestamp_suffix( $return_prefix );

											$insert_return = array(
												'borrow_id'        => $book_borrow_details->borrow_id,
												'return_id'        => $return_code,
												'category_id'      => $book_borrow_details->category_id,
												'book_id'          => $book_borrow_details->book_id,
												'accession_number' => $return_accession !== '' ? $return_accession : null,
												'branch_id'        => $book_borrow_details->branch_id,
												'wp_user'          => $book_borrow_details->wp_user,
												'u_id'             => $book_borrow_details->u_id,
												'has_fine_status'  => $has_fine ? 1 : 0,
												'return_condition' => $return_condition,
												'return_remark'    => $return_remark !== '' ? $return_remark : null,
												'is_self_return'   => 1,
												'return_status'    => $admin_approval ? LIBMNS_DEFAULT_RETURN : LIBMNS_RETURN_SELF_APPROVED,
												'status'           => 1,
											);
											$wpdb->insert( LIBMNS_Table_Helper_FREE::get_table_name( 'book_return' ), $insert_return );
											$return_table_id = $wpdb->insert_id;

											if ( $has_fine && $return_table_id > 0 ) {
												$wpdb->insert( LIBMNS_Table_Helper_FREE::get_table_name( 'book_late_fine' ), array(
													'return_id'   => $return_table_id,
													'book_id'     => $book_borrow_details->book_id,
													'wp_user'     => 1,
													'u_id'        => $book_borrow_details->u_id,
													'extra_days'  => $extra_days,
													'fine_amount' => $total_late_fine,
													'fine_type'   => $fine_type,
													'status'      => 1,
													'has_paid'    => 1,
												) );
											}

											if ( ! $admin_approval ) {
												$wpdb->update( LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' ), array( 'status' => 0 ), array( 'id' => $book_borrow_details->id ) );
											}

											$response = $admin_approval
												? [ 1, __( "Book Return Requested to LMS Admin.", "library-management-system" ) ]
												: [ 1, __( "Successfully, You have returned book.", "library-management-system" ) ];
										}
									}
								}
							}
							}
						}
					}
				} else {
					$response = [
						0, 
						__("Invalid action", "library-management-system")
					];
				}
			}
		}else{
			$response = [
				0, 
				__("LMS actions blocked due to security reasons", "library-management-system")
			];
		}

		wp_send_json($this->json($response));

		wp_die();
	}
}