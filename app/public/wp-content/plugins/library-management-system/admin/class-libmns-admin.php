<?php
/**
 * @link       https://onlinewebtutorblog.com
 * @since      3.5
 * @package    Library_Management_System
 * @subpackage Library_Management_System/admin
 * @copyright  Copyright (c) 2026, Online Web Tutor
 * @license    GPL-2.0+ https://www.gnu.org/licenses/gpl-2.0.html
 * @author     Online Web Tutor
 */
class LIBMNS_Admin_FREE {

	private $plugin_name;
	private $table_activator;
	private $version;

	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		require_once LIBMNS_PLUGIN_DIR_PATH . 'includes/class-libmns-activator.php';
        $this->table_activator = new LIBMNS_Activator_FREE();
	}

	public function get_table_activator() {
		return $this->table_activator;
	}

	private function libmns_get_allowed_admin_pages() {
		return array(
			'owt7_library_books',
			'library_management_system',
			'owt7_library_users',
			'owt7_library_bookcases',
			'owt7_library_transactions',
			'owt7_library_settings',
			'owt7_library_upgrade_pro',
			'owt7_library_books_catalogue',
			'owt7_library_user_borrowed',
			'owt7_library_user_returned',
		);
	}

	private function libmns_is_library_admin_page() {
		$current_page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
		return in_array( $current_page, $this->libmns_get_allowed_admin_pages(), true );
	}

	private function libmns_is_plugins_page() {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		return $screen && in_array( $screen->id, array( 'plugins', 'plugins-network' ), true );
	}

	public function libmns_enqueue_styles() {
		$is_allowed = $this->libmns_is_library_admin_page() || $this->libmns_is_plugins_page();
		if ( ! $is_allowed && LIBMNS_Roles_Helper_FREE::libmns_user_has_library_user_role() && isset( $_GET['page'] ) ) {
			$page = sanitize_text_field( wp_unslash( $_GET['page'] ) );
			if ( in_array( $page, LIBMNS_Roles_Helper_FREE::libmns_get_library_user_menu_slugs(), true ) ) {
				$is_allowed = true;
			}
		}
		if ( ! $is_allowed ) {
			return;
		}

		wp_enqueue_style( "owt7-lms-table-css", plugin_dir_url( __FILE__ ) . 'css/jquery.dataTables.min.css', array(), $this->version, 'all' );

		wp_enqueue_style( "owt7-lms-table-buttons-css", plugin_dir_url( __FILE__ ) . 'css/buttons.dataTables.min.css', array(), $this->version, 'all' );

		wp_enqueue_style( "owt7-lms-toastr-css", plugin_dir_url( __FILE__ ) . 'css/toastr.min.css', array(), $this->version, 'all' );

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/library-management-system-admin.css', array(), time(), 'all' );

		$primary = get_option( 'owt7_lms_theme_primary', LIBMNS_THEME_PRIMARY_DEFAULT );
		$accent  = get_option( 'owt7_lms_theme_accent', LIBMNS_THEME_ACCENT_DEFAULT );
		if ( $primary && preg_match( '/^#[0-9a-fA-F]{6}$/', $primary ) ) {
			$primary_dark  = $this->libmns_adjust_hex_brightness( $primary, -18 );
			$primary_light = $this->libmns_adjust_hex_brightness( $primary, 15 );
			$vars = "--owt7-primary: {$primary}; --owt7-primary-dark: {$primary_dark}; --owt7-primary-light: {$primary_light};";
			if ( $accent && preg_match( '/^#[0-9a-fA-F]{6}$/', $accent ) ) {
				$vars .= " --owt7-accent: {$accent};";
			}
			$inline = ".owt7-lms { {$vars} }";
			$inline .= " .owt7_lms_modal_section { {$vars} }";
			$inline .= " .owt7-lms-dashboard, .owt7-lms-users, .owt7-lms-bookcases, .owt7-lms-books, .owt7-lms-transactions, .owt7-lms-reports, .owt7-lms-settings, .owt7-lms-about, .owt7-lms-verification, .owt7-lms-library-user-portal { border-left-color: {$primary}; }";
			wp_add_inline_style( $this->plugin_name, $inline );
		} elseif ( $accent && preg_match( '/^#[0-9a-fA-F]{6}$/', $accent ) ) {
			wp_add_inline_style( $this->plugin_name, ".owt7-lms { --owt7-accent: {$accent}; }" );
		}

		$action_view   = get_option( 'owt7_lms_theme_action_view', LIBMNS_THEME_ACTION_VIEW_DEFAULT );
		$action_edit   = get_option( 'owt7_lms_theme_action_edit', LIBMNS_THEME_ACTION_EDIT_DEFAULT );
		$action_copies = get_option( 'owt7_lms_theme_action_book_copies', LIBMNS_THEME_ACTION_BOOK_COPIES_DEFAULT );
		$action_delete = get_option( 'owt7_lms_theme_action_delete', LIBMNS_THEME_ACTION_DELETE_DEFAULT );
		$action_colors = array(
			'view'        => ( preg_match( '/^#[0-9a-fA-F]{6}$/', $action_view ) ? $action_view : LIBMNS_THEME_ACTION_VIEW_DEFAULT ),
			'edit'        => ( preg_match( '/^#[0-9a-fA-F]{6}$/', $action_edit ) ? $action_edit : LIBMNS_THEME_ACTION_EDIT_DEFAULT ),
			'copies'      => ( preg_match( '/^#[0-9a-fA-F]{6}$/', $action_copies ) ? $action_copies : LIBMNS_THEME_ACTION_BOOK_COPIES_DEFAULT ),
			'delete'      => ( preg_match( '/^#[0-9a-fA-F]{6}$/', $action_delete ) ? $action_delete : LIBMNS_THEME_ACTION_DELETE_DEFAULT ),
		);
		$action_vars = '';
		foreach ( $action_colors as $name => $hex ) {
			$dark = $this->libmns_adjust_hex_brightness( $hex, -12 );
			$action_vars .= " --owt7-action-{$name}: {$hex}; --owt7-action-{$name}-dark: {$dark};";
		}
		if ( $action_vars ) {
			$action_inline = ".owt7-lms { {$action_vars} }";
			wp_add_inline_style( $this->plugin_name, $action_inline );
		}
	}

	private function libmns_adjust_hex_brightness( $hex, $percent ) {
		$hex = ltrim( $hex, '#' );
		$r = hexdec( substr( $hex, 0, 2 ) );
		$g = hexdec( substr( $hex, 2, 2 ) );
		$b = hexdec( substr( $hex, 4, 2 ) );
		$mult = 1 + ( $percent / 100 );
		$r = max( 0, min( 255, (int) round( $r * $mult ) ) );
		$g = max( 0, min( 255, (int) round( $g * $mult ) ) );
		$b = max( 0, min( 255, (int) round( $b * $mult ) ) );
		return '#' . sprintf( '%02x%02x%02x', $r, $g, $b );
	}

	public function libmns_get_backup_module_to_table() {
		return LIBMNS_Table_Helper_FREE::get_backup_module_to_table();
	}

	private function libmns_get_distinct_active_branches() {
		global $wpdb;
		$rows = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT id, name FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'branches' ) . " WHERE status = %d ORDER BY name ASC, id ASC",
				1
			)
		);
		$distinct = array();
		foreach ( (array) $rows as $row ) {
			$name = isset( $row->name ) ? trim( (string) $row->name ) : '';
			$key  = strtolower( $name );
			if ( isset( $distinct[ $key ] ) ) {
				continue;
			}
			$row->name = $name;
			$distinct[ $key ] = $row;
		}
		return array_values( $distinct );
	}

	public function libmns_enqueue_scripts() {
		$is_allowed = $this->libmns_is_library_admin_page() || $this->libmns_is_plugins_page();
		if ( ! $is_allowed && LIBMNS_Roles_Helper_FREE::libmns_user_has_library_user_role() && isset( $_GET['page'] ) ) {
			$page = sanitize_text_field( wp_unslash( $_GET['page'] ) );
			if ( in_array( $page, LIBMNS_Roles_Helper_FREE::libmns_get_library_user_menu_slugs(), true ) ) {
				$is_allowed = true;
			}
		}
		if ( ! $is_allowed ) {
			return;
		}

		wp_enqueue_script( "owt7-lms-validate", plugin_dir_url( __FILE__ ) . 'js/jquery.validate.min.js', array( 'jquery' ), $this->version, false );

		wp_enqueue_script( "owt7-lms-toastr", plugin_dir_url( __FILE__ ) . 'js/toastr.min.js', array( 'jquery' ), $this->version, false );

		wp_enqueue_script( "owt7-lms-datatable", plugin_dir_url( __FILE__ ) . 'js/jquery.dataTables.min.js', array( 'jquery' ), $this->version, false );

		wp_enqueue_script( "owt7-lms-datatable-btns", plugin_dir_url( __FILE__ ) . 'js/dataTables.buttons.min.js', array( 'jquery' ), $this->version, false );

		wp_enqueue_script( "owt7-lms-datatable-excel-btn", plugin_dir_url( __FILE__ ) . 'js/jszip.min.js', array( 'jquery' ), $this->version, false );

		wp_enqueue_script( "owt7-lms-datatable-pdf-btn", plugin_dir_url( __FILE__ ) . 'js/pdfmake.min.js', array( 'jquery' ), $this->version, false );

		wp_enqueue_script( "owt7-lms-datatable-vfs-fonts", plugin_dir_url( __FILE__ ) . 'js/vfs_fonts.js', array( 'jquery' ), $this->version, false );

		wp_enqueue_script( "owt7-lms-datatable-btns-plugin", plugin_dir_url( __FILE__ ) . 'js/buttons.html5.min.js', array( 'jquery' ), $this->version, false );

		wp_enqueue_script( "owt7-lms-datatable-copy-btn", plugin_dir_url( __FILE__ ) . 'js/buttons.print.min.js', array( 'jquery' ), $this->version, false );

		wp_enqueue_script( "owt7-lms-sweetalert2", 'https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js', array(), '11', true );

		wp_enqueue_script( "owt7-lms-jspdf", 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js', array(), '2.5.1', true );

		$admin_script_deps = array( 'jquery', 'owt7-lms-sweetalert2', 'owt7-lms-jspdf' );
		
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/library-management-system-admin.js', $admin_script_deps, time(), false );

		wp_localize_script($this->plugin_name, "owt7_library", array(
			"ajaxurl" => admin_url("admin-ajax.php"),
			"active" => 1,
			"plugin_basename" => ( defined( 'LIBMNS_PLUGIN_BASENAME' ) ? LIBMNS_PLUGIN_BASENAME : '' ),
			"ajax_nonce" => wp_create_nonce('owt7_library_actions'),
			"book_prefix" => ( defined( 'LIBMNS_BOOK_PREFIX' ) ? LIBMNS_BOOK_PREFIX : 'LMSBK' ),
			"user_prefix" => ( defined( 'LIBMNS_USER_PREFIX' ) ? LIBMNS_USER_PREFIX : 'LMSUS' ),
			"messages" => array(
				"message_1" => __('Submitted, please wait', 'library-management-system'),
				"message_2" => __('Submit', 'library-management-system'),
				"message_3" => __('Success', 'library-management-system'),
				"message_4" => __('Error', 'library-management-system'),
				"message_5" => __('Upload Image', 'library-management-system'),
				"message_6" => __('Select Section', 'library-management-system'),
				"message_7" => __('Select User', 'library-management-system'),
				"message_8" => __('Select Book', 'library-management-system'),
				"message_9" => __('The Test Data Importer will install the sample dataset. If sample data was installed earlier, only those tracked sample records will be replaced. This action cannot be undone. Do you want to continue?', 'library-management-system'),
				"message_10" => __('Are you sure you want to remove the tracked LMS sample data?', 'library-management-system'),
				"message_11" => __('Are you sure want to pay the fine?', 'library-management-system'),
				"message_12" => __('Are you sure want to delete?', 'library-management-system'),
				"message_13" => __('Are you sure want to return this book?', 'library-management-system'),
				"no_data_export" => __('No data available to export.', 'library-management-system'),
				"showing_branch_users" => __('Showing "%s" borrowers', 'library-management-system')
			)
		));

	}

	public function libmns_register_menus() {
		$parent_slug = 'library_management_system';
		add_menu_page( __( 'Library Management', 'library-management-system' ), __( 'Library Management', 'library-management-system' ), 'view_library_menu', $parent_slug, array( $this, 'libmns_dashboard_page' ), 'dashicons-book-alt', 42 );
		add_submenu_page( $parent_slug, __( 'Dashboard', 'library-management-system' ), __( 'Dashboard', 'library-management-system' ), 'manage_owt7_library_system', $parent_slug, array( $this, 'libmns_dashboard_page' ) );
		add_submenu_page( $parent_slug, __( 'Manage Users', 'library-management-system' ), __( 'Manage Users', 'library-management-system' ), 'manage_owt7_library_system', 'owt7_library_users', array( $this, 'libmns_manage_users_page' ) );
		add_submenu_page( $parent_slug, __( 'Manage Bookcase & Section', 'library-management-system' ), __( 'Manage Bookcase & Section', 'library-management-system' ), 'manage_owt7_library_system', 'owt7_library_bookcases', array( $this, 'libmns_manage_bookcase_page' ) );
		add_submenu_page( $parent_slug, __( 'Manage Books', 'library-management-system' ), __( 'Manage Books', 'library-management-system' ), 'manage_owt7_library_system', 'owt7_library_books', array( $this, 'libmns_manage_books_page' ) );
		add_submenu_page( $parent_slug, __( 'Book Transactions', 'library-management-system' ), __( 'Book Transactions', 'library-management-system' ), 'manage_owt7_library_system', 'owt7_library_transactions', array( $this, 'libmns_transactions_page' ) );
		add_submenu_page( $parent_slug, __( 'All Settings', 'library-management-system' ), __( 'All Settings', 'library-management-system' ), 'manage_owt7_library_system', 'owt7_library_settings', array( $this, 'libmns_settings_page' ) );
		add_submenu_page( $parent_slug, __( 'Upgrade to PRO', 'library-management-system' ), __( 'Upgrade to PRO', 'library-management-system' ), 'manage_owt7_library_system', 'owt7_library_upgrade_pro', array( $this, 'libmns_upgrade_pro_page' ) );
		add_submenu_page( $parent_slug, __( 'Books List', 'library-management-system' ), __( 'Books List', 'library-management-system' ), 'access_owt7_library_user_portal', 'owt7_library_books_catalogue', array( $this, 'libmns_library_books_catalog_page' ) );
		add_submenu_page( $parent_slug, __( 'My Books', 'library-management-system' ), __( 'My Books', 'library-management-system' ), 'access_owt7_library_user_portal', 'owt7_library_user_borrowed', array( $this, 'libmns_library_user_borrowed_page' ) );
		add_submenu_page( $parent_slug, __( 'Returned Books', 'library-management-system' ), __( 'Returned Books', 'library-management-system' ), 'access_owt7_library_user_portal', 'owt7_library_user_returned', array( $this, 'libmns_library_user_returned_page' ) );
	}

	public function libmns_librarian_can_access_page( $page_slug ) {
		if ( current_user_can( 'manage_options' ) ) {
			return;
		}
		$user = wp_get_current_user();
		if ( ! $user || ! is_array( $user->roles ) ) {
			return;
		}
		if ( LIBMNS_Roles_Helper_FREE::libmns_user_has_library_user_role( $user ) ) {
			$allowed = LIBMNS_Roles_Helper_FREE::libmns_get_library_user_menu_slugs();
			if ( in_array( $page_slug, $allowed, true ) ) {
				return;
			}
			wp_die( esc_html__( 'You do not have permission to access this page.', 'library-management-system' ), 403 );
		}
		$restricted_roles = LIBMNS_Roles_Helper_FREE::libmns_get_restricted_lms_roles();
		$has_restricted_role = false;
		foreach ( $restricted_roles as $role_slug ) {
			if ( in_array( $role_slug, $user->roles, true ) ) {
				$has_restricted_role = true;
				break;
			}
		}
		if ( ! $has_restricted_role ) {
			return;
		}
		$required = LIBMNS_Roles_Helper_FREE::libmns_menu_required_caps();
		if ( empty( $required[ $page_slug ] ) ) {
			return;
		}
		foreach ( $required[ $page_slug ] as $cap ) {
			if ( LIBMNS_Roles_Helper_FREE::libmns_current_user_can( $cap ) ) {
				return;
			}
		}
		wp_die( esc_html__( 'You do not have permission to access this page.', 'library-management-system' ), 403 );
	}

	public function libmns_filter_librarian_menu() {
		if ( ! current_user_can( 'view_library_menu' ) ) {
			return;
		}
		$user = wp_get_current_user();
		if ( ! $user || ! is_array( $user->roles ) ) {
			return;
		}

		if ( LIBMNS_Roles_Helper_FREE::libmns_user_has_library_user_role( $user ) ) {
			remove_menu_page( 'edit.php' ); 
			remove_menu_page( 'upload.php' );
			remove_menu_page( 'edit-comments.php' );
			remove_menu_page( 'tools.php' );
			global $submenu;
			$menu_slug = 'library_management_system';
			if ( ! empty( $submenu[ $menu_slug ] ) ) {
				$library_user_slugs = LIBMNS_Roles_Helper_FREE::libmns_get_library_user_menu_slugs();
				$filtered = array();
				foreach ( $submenu[ $menu_slug ] as $item ) {
					if ( in_array( $item[2], $library_user_slugs, true ) ) {
						$filtered[] = $item;
					}
				}
				$submenu[ $menu_slug ] = $filtered;
			}
			return;
		}

		if ( ! current_user_can( 'manage_owt7_library_system' ) ) {
			return;
		}
		$restricted_roles = LIBMNS_Roles_Helper_FREE::libmns_get_restricted_lms_roles();
		$has_restricted_role = false;
		foreach ( $restricted_roles as $role_slug ) {
			if ( in_array( $role_slug, $user->roles, true ) ) {
				$has_restricted_role = true;
				break;
			}
		}
		if ( ! $has_restricted_role ) {
			return;
		}

		remove_menu_page( 'edit.php' );
		remove_menu_page( 'upload.php' );
		remove_menu_page( 'edit-comments.php' );
		remove_menu_page( 'tools.php' );

		global $submenu;
		$menu_slug = 'library_management_system';
		if ( empty( $submenu[ $menu_slug ] ) ) {
			return;
		}
		$required = LIBMNS_Roles_Helper_FREE::libmns_menu_required_caps();
		$library_user_slugs = LIBMNS_Roles_Helper_FREE::libmns_get_library_user_menu_slugs();
		$filtered = array();
		foreach ( $submenu[ $menu_slug ] as $item ) {
			$page_slug = $item[2];
			if ( in_array( $page_slug, $library_user_slugs, true ) ) {
				continue;
			}
			if ( ! isset( $required[ $page_slug ] ) ) {
				$filtered[] = $item;
				continue;
			}
			$has_any = false;
			foreach ( $required[ $page_slug ] as $cap ) {
				if ( LIBMNS_Roles_Helper_FREE::libmns_current_user_can( $cap ) ) {
					$has_any = true;
					break;
				}
			}
			if ( $has_any ) {
				$filtered[] = $item;
			}
		}
		$submenu[ $menu_slug ] = $filtered;
	}

	private function libmns_current_user_sees_restricted_dashboard() {
		if ( current_user_can( 'manage_options' ) ) {
			return false;
		}
		return LIBMNS_Roles_Helper_FREE::libmns_user_has_library_user_role();
	}

	public function libmns_render_welcome_dashboard_widget() {
		$user = wp_get_current_user();
		$name = ! empty( $user->display_name ) ? $user->display_name : __( 'User', 'library-management-system' );
		$is_library_user = LIBMNS_Roles_Helper_FREE::libmns_user_has_library_user_role( $user );
		if ( $is_library_user ) {
			$dashboard_url = admin_url( 'admin.php?page=owt7_library_books_catalogue' );
		} else {
			$dashboard_url = admin_url( 'admin.php?page=library_management_system' );
		}
		?>
		<style>
			.owt7-lms-dashboard-welcome { padding: 4px 0; }
			.owt7-lms-dashboard-welcome .owt7-lms-welcome-lead { font-size: 1.25em; font-weight: 600; color: #1e293b; margin: 0 0 12px 0; }
			.owt7-lms-dashboard-welcome .owt7-lms-welcome-paragraph { font-size: 15px; line-height: 1.6; color: #475569; margin: 0 0 12px 0; }
			.owt7-lms-dashboard-welcome .owt7-lms-welcome-sub { font-size: 13px; color: #64748b; margin: 0 0 16px 0; }
			.owt7-lms-dashboard-welcome .owt7-lms-welcome-cta { margin: 0; }
		</style>
		<div class="owt7-lms-dashboard-welcome">
			<p class="owt7-lms-welcome-lead"><?php echo esc_html( sprintf( __( 'Hello, %s!', 'library-management-system' ), $name ) ); ?></p>
			<?php if ( $is_library_user ) : ?>
				<p class="owt7-lms-welcome-paragraph"><?php esc_html_e( 'Welcome to the Library. Browse the Books List, view your borrowed books, and manage returns from the Library Management menu on the left.', 'library-management-system' ); ?></p>
				<p class="owt7-lms-welcome-sub"><?php esc_html_e( 'Use Books List to search and request checkout, My Books to see current loans and return, and Returned Books for history.', 'library-management-system' ); ?></p>
				<p class="owt7-lms-welcome-cta"><a href="<?php echo esc_url( $dashboard_url ); ?>" class="button button-primary"><?php esc_html_e( 'Go to Books List', 'library-management-system' ); ?></a></p>
			<?php else : ?>
				<p class="owt7-lms-welcome-paragraph"><?php esc_html_e( 'Welcome to your Library Management dashboard. From here you can manage books, branches, users, and circulation. Use the Library System menu on the left to access Books, Users, Bookcases, Transactions, Reports, and Settings.', 'library-management-system' ); ?></p>
				<p class="owt7-lms-welcome-sub"><?php esc_html_e( 'Need help? Check the About page under Library System for documentation and support.', 'library-management-system' ); ?></p>
				<p class="owt7-lms-welcome-cta"><a href="<?php echo esc_url( $dashboard_url ); ?>" class="button button-primary"><?php esc_html_e( 'Open Library Dashboard', 'library-management-system' ); ?></a></p>
			<?php endif; ?>
		</div>
		<?php
	}

	public function libmns_admin_bar_remove_new_for_lms_staff( $wp_admin_bar ) {
		if ( ! $this->libmns_current_user_sees_restricted_dashboard() ) {
			return;
		}
		$wp_admin_bar->remove_node( 'new-content' );
		if ( LIBMNS_Roles_Helper_FREE::libmns_user_has_library_user_role() ) {
			$wp_admin_bar->remove_node( 'comments' );
		}
	}

	public function libmns_plugin_action_links( $links ) {
		$settings_link = '<a href="' . esc_url( admin_url( 'admin.php?page=owt7_library_settings' ) ) . '">' . __( 'Settings', 'library-management-system' ) . '</a>';
		$upgrade_link  = '<a href="' . esc_url( admin_url( 'admin.php?page=owt7_library_upgrade_pro' ) ) . '" style="color: #d63638; font-weight: 600;">' . __( 'Upgrade to PRO', 'library-management-system' ) . '</a>';
		return array_merge( array( $settings_link, $upgrade_link ), $links );
	}
	
	public function libmns_dashboard_page() {
		if ( LIBMNS_Roles_Helper_FREE::libmns_user_has_library_user_role() ) {
			wp_safe_redirect( admin_url( 'admin.php?page=owt7_library_books_catalogue' ) );
			exit;
		}
		$this->libmns_librarian_can_access_page( 'library_management_system' );
		$this->libmns_include_template_file("", "owt7_library_dashboard");
	}

	public function libmns_manage_users_page() {
		$this->libmns_librarian_can_access_page( 'owt7_library_users' );
		global $wpdb;

			$allowed_pages = [
				"user" => [
					"add",
					"list"
				],
				"branch" => [
					"add",
					"list"
				]
			];

			$mod = isset($_REQUEST['mod']) ? strtolower($_REQUEST['mod']) : "";
			$fn = isset($_REQUEST['fn']) ? strtolower($_REQUEST['fn']) : "";

			$id = isset($_REQUEST['id']) ? intval(base64_decode($_REQUEST['id'])) : "";
			$opt = isset($_REQUEST['opt']) ? strtolower($_REQUEST['opt']) : "";

			wp_enqueue_media();

			$statuses = [
				"1" => "active",
				"0" => "inactive"
			];
			
			if(!empty($fn) && in_array($fn, $allowed_pages[$mod])){

				if($mod == "branch"){ 

					$branches = $wpdb->get_results(
						"SELECT branch.*, (SELECT count(*) FROM ".LIBMNS_Table_Helper_FREE::get_table_name( 'users' )." as user WHERE branch.id = user.branch_id LIMIT 1) as total_users from " . LIBMNS_Table_Helper_FREE::get_table_name( 'branches' )." as branch"
					);
				
					$branch = array();

					if(!empty($id)){
						$branch = $wpdb->get_row(
							"SELECT * from " . LIBMNS_Table_Helper_FREE::get_table_name( 'branches' )." WHERE id = {$id}",
							ARRAY_A
						);
					}

					if(!empty($branch)){

						$this->libmns_include_template_file(
							"users", 
							"owt7_library_{$fn}_{$mod}", 
							[
								"branch" => $branch,
								"statuses" => $statuses,
								"action" => $opt
							]
						);
					}else{

						$this->libmns_include_template_file(
							"users", 
							"owt7_library_{$fn}_{$mod}", 
							[
								"branches" => $branches,
								"statuses" => $statuses
							]
						);
					}
				} elseif($mod == "user"){

					$branches = $wpdb->get_results(
						"SELECT * from " . LIBMNS_Table_Helper_FREE::get_table_name( 'branches' ). " WHERE status = 1"
					);
					
					$genders = ["male", "female", "other"];

					$user = array();

					if(!empty($id)){
						$user = $wpdb->get_row(
							"SELECT * from " . LIBMNS_Table_Helper_FREE::get_table_name( 'users' )." WHERE id = {$id}",
							ARRAY_A
						);
						if ( ! empty( $user['wp_user_id'] ) ) {
							$wp_user = get_userdata( (int) $user['wp_user_id'] );
							$user['wp_username'] = ( $wp_user && isset( $wp_user->user_login ) ) ? $wp_user->user_login : '';
						}
					}

					$this->libmns_include_template_file(
						"users", 
						"owt7_library_{$fn}_{$mod}", 
						[
							"branches" => $branches,
							"user" => $user,
							"genders" => $genders,
							"statuses" => $statuses,
							"action" => $opt
						]
					);
				}
			}else{ 

				$users = $wpdb->get_results(
					"SELECT user.*, (SELECT name FROM ".LIBMNS_Table_Helper_FREE::get_table_name( 'branches' )." as branch WHERE branch.id = user.branch_id LIMIT 1) as branch_name from " . LIBMNS_Table_Helper_FREE::get_table_name( 'users' )." as user"
				);

				$branches = $wpdb->get_results(
					"SELECT * from " . LIBMNS_Table_Helper_FREE::get_table_name( 'branches' ). " WHERE status = 1"
				);

				$filter_branch_name = '';
				$branch_id_param = isset( $_REQUEST['branch_id'] ) ? absint( $_REQUEST['branch_id'] ) : 0;
				if ( $branch_id_param > 0 ) {
					$branch_row = $wpdb->get_row( $wpdb->prepare(
						"SELECT name FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'branches' ) . " WHERE id = %d LIMIT 1",
						$branch_id_param
					) );
					if ( ! empty( $branch_row->name ) ) {
						$filter_branch_name = $branch_row->name;
					}
				}
			
				$this->libmns_include_template_file(
					"users", 
					"owt7_library_users", [
						"users" => $users,
						"branches" => $branches,
						"filter_branch_name" => $filter_branch_name,
					]
				);
			}
	}

	public function libmns_manage_bookcase_page() {
		$this->libmns_librarian_can_access_page( 'owt7_library_bookcases' );
		global $wpdb;

			$allowed_pages = [
				"bookcase" => [
					"add",
					"list"
				],
				"section" => [
					"add",
					"list"
				]
			];

			$mod = isset($_REQUEST['mod']) ? strtolower($_REQUEST['mod']) : "";
			$fn = isset($_REQUEST['fn']) ? strtolower($_REQUEST['fn']) : "";

			$id = isset($_REQUEST['id']) ? intval(base64_decode($_REQUEST['id'])) : "";
			$opt = isset($_REQUEST['opt']) ? strtolower($_REQUEST['opt']) : "";

			$statuses = [
				"1" => "active",
				"0" => "inactive"
			];

			if(!empty($fn) && in_array($fn, $allowed_pages[$mod])){

				if($mod == "section"){

					$bookcases = $wpdb->get_results(
						"SELECT * from " . LIBMNS_Table_Helper_FREE::get_table_name( 'bookcases' )." WHERE status = 1"
					);

					$section = array();

					if(!empty($id)){
						$section = $wpdb->get_row(
							"SELECT * from " . LIBMNS_Table_Helper_FREE::get_table_name( 'sections' )." WHERE id = {$id}",
							ARRAY_A
						);
					}

					if(!empty($section)){

						$this->libmns_include_template_file(
							"bookcases", 
							"owt7_library_{$fn}_{$mod}", 
							[
								"section" => $section,
								"bookcases" => $bookcases,
								"statuses" => $statuses,
								"action" => $opt
							]
						);
					}else{

						$filter_bookcase_id = isset( $_REQUEST['bookcase_id'] ) ? absint( $_REQUEST['bookcase_id'] ) : 0;
						$sections_query = "SELECT sec.*, bkcase.name as bookcase_name, (SELECT count(*) FROM ".LIBMNS_Table_Helper_FREE::get_table_name( 'books' )." as book WHERE book.bookcase_section_id = sec.id limit 1) as total_books from " . LIBMNS_Table_Helper_FREE::get_table_name( 'sections' ). " sec INNER JOIN ". LIBMNS_Table_Helper_FREE::get_table_name( 'bookcases' ) . " bkcase ON sec.bookcase_id = bkcase.id";
						if ( $filter_bookcase_id > 0 ) {
							$sections_query .= $wpdb->prepare( " WHERE sec.bookcase_id = %d", $filter_bookcase_id );
						}
						$sections = $wpdb->get_results( $sections_query );

						$this->libmns_include_template_file(
							"bookcases", 
							"owt7_library_{$fn}_{$mod}", 
							[
								"sections" => $sections,
								"bookcases" => $bookcases,
								"statuses" => $statuses,
								"filter_bookcase_id" => $filter_bookcase_id
							]
						);
					}
				}elseif($mod == "bookcase"){

					$bookcase = array();

					if(!empty($id)){
						$bookcase = $wpdb->get_row(
							"SELECT * from " . LIBMNS_Table_Helper_FREE::get_table_name( 'bookcases' )." WHERE id = {$id}",
							ARRAY_A
						);
					}

					$this->libmns_include_template_file(
						"bookcases", 
						"owt7_library_{$fn}_{$mod}", 
						[
								"bookcase" => $bookcase,
								"statuses" => $statuses,
								"action" => $opt
						]
					);
				}
			}else{

				$bookcases = $wpdb->get_results(
					"SELECT bkcase.*, (SELECT count(*) FROM ".LIBMNS_Table_Helper_FREE::get_table_name( 'sections' )." as section WHERE section.bookcase_id = bkcase.id limit 1) as total_sections, (SELECT count(*) FROM ".LIBMNS_Table_Helper_FREE::get_table_name( 'books' )." as book WHERE book.bookcase_id = bkcase.id limit 1) as total_books from " . LIBMNS_Table_Helper_FREE::get_table_name( 'bookcases' )." as bkcase"
				);

				$this->libmns_include_template_file(
					"bookcases", 
					"owt7_library_bookcases", 
					[
						"bookcases" => $bookcases
					]
				);
			}
	}

	public function libmns_manage_books_page(){
		$this->libmns_librarian_can_access_page( 'owt7_library_books' );
		global $wpdb;

			$allowed_pages = [
				"book" => [
					"add",
					"list"
				],
				"category" => [
					"add",
					"list"
				]
			];

			$mod = isset($_REQUEST['mod']) ? strtolower($_REQUEST['mod']) : "";
			$fn = isset($_REQUEST['fn']) ? strtolower($_REQUEST['fn']) : "";
			
			$id = isset($_REQUEST['id']) ? intval(base64_decode($_REQUEST['id'])) : "";
			$opt = isset($_REQUEST['opt']) ? strtolower($_REQUEST['opt']) : "";

			wp_enqueue_media();

			$statuses = [
				"1" => "active",
				"0" => "inactive"
			];

			if(!empty($fn) && in_array($fn, $allowed_pages[$mod])){

				if($mod == "category"){

					$category = array();

					if(!empty($id)){
						$category = $wpdb->get_row(
							"SELECT * from " . LIBMNS_Table_Helper_FREE::get_table_name( 'categories' )." WHERE id = {$id}",
							ARRAY_A
						);
					}

					if(!empty($category)){

						$this->libmns_include_template_file(
							"books", 
							"owt7_library_{$fn}_{$mod}", 
							[
								"category" => $category,
								"statuses" => $statuses,
								"action" => $opt
							]
						);
					}else{

						$categories = $wpdb->get_results(
							"SELECT category.*, (SELECT count(*) FROM ".LIBMNS_Table_Helper_FREE::get_table_name( 'books' )." as book WHERE book.category_id = category.id LIMIT 1) as total_books from " . LIBMNS_Table_Helper_FREE::get_table_name( 'categories' ). " as category"
						);

						$this->libmns_include_template_file(
							"books", 
							"owt7_library_{$fn}_{$mod}", 
							[
								"categories" => $categories,
								"statuses" => $statuses
							]
						);
					}
				} elseif($mod == "book"){

					$book = array();
					$sections = array();

					$categories = $wpdb->get_results(
						"SELECT * from " . LIBMNS_Table_Helper_FREE::get_table_name( 'categories' ). " WHERE status = 1"
					);

					$bookcases = $wpdb->get_results(
						"SELECT * from " . LIBMNS_Table_Helper_FREE::get_table_name( 'bookcases' ). " WHERE status = 1"
					);

					if(!empty($id)){
						$book = $wpdb->get_row(
							"SELECT * from " . LIBMNS_Table_Helper_FREE::get_table_name( 'books' )." WHERE id = {$id}",
							ARRAY_A
						);
						if(!empty($book['bookcase_id'])){

							$sections = $wpdb->get_results(
								"SELECT * from " . LIBMNS_Table_Helper_FREE::get_table_name( 'sections' ). " WHERE bookcase_id = {$book['bookcase_id']} AND status = 1"
							);
						}
					}

					if(!empty($book)){

						$this->libmns_include_template_file(
							"books", 
							"owt7_library_{$fn}_{$mod}", 
							[
								"book" => $book,
								"statuses" => $statuses,
								"sections" => $sections,
								"bookcases" => $bookcases,
								"action" => $opt,
								"categories" => $categories
							]
						);
					}else{

						$this->libmns_include_template_file(
							"books", 
							"owt7_library_{$fn}_{$mod}", 
							[
								"statuses" => $statuses,
								"bookcases" => $bookcases,
								"categories" => $categories
							]
						);
					}
				}
			}else{

				$bkcase_id   = isset( $_REQUEST['bkcase_id'] ) ? absint( $_REQUEST['bkcase_id'] ) : 0;
				$section_id  = isset( $_REQUEST['section_id'] ) ? absint( $_REQUEST['section_id'] ) : 0;
				$category_id = isset( $_REQUEST['category_id'] ) ? absint( $_REQUEST['category_id'] ) : 0;
				$books_base_sql = "SELECT book.id, book.book_id, book.name, book.is_woocom_product, book.stock_quantity, book.status, book.created_at, (SELECT category.name FROM ".LIBMNS_Table_Helper_FREE::get_table_name( 'categories' )." as category WHERE category.id = book.category_id LIMIT 1) as category_name, (SELECT bkcase.name FROM ".LIBMNS_Table_Helper_FREE::get_table_name( 'bookcases' )." as bkcase WHERE bkcase.id = book.bookcase_id LIMIT 1) as bookcase_name, (SELECT section.name FROM ".LIBMNS_Table_Helper_FREE::get_table_name( 'sections' )." as section WHERE section.id = book.bookcase_section_id LIMIT 1) as section_name, (SELECT COUNT(*) FROM ".LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' )." as borrow WHERE borrow.book_id = book.id AND borrow.status = 1) as has_active_borrow from " . LIBMNS_Table_Helper_FREE::get_table_name( 'books' ). " as book";
				if ( $section_id > 0 ) {
					$books_base_sql .= $wpdb->prepare( " WHERE book.bookcase_section_id = %d", $section_id );
				} elseif ( $bkcase_id > 0 ) {
					$books_base_sql .= $wpdb->prepare( " WHERE book.bookcase_id = %d", $bkcase_id );
				} elseif ( $category_id > 0 ) {
					$books_base_sql .= $wpdb->prepare( " WHERE book.category_id = %d", $category_id );
				}
				$books = $wpdb->get_results( $books_base_sql );

				$categories = $wpdb->get_results(
					"SELECT * from " . LIBMNS_Table_Helper_FREE::get_table_name( 'categories' ). " WHERE status = 1"
				);

				$this->libmns_include_template_file(
					"books", 
					"owt7_library_books", [
						"books" => $books,
						"categories" => $categories,
						"filter_bkcase_id" => $bkcase_id,
						"filter_section_id" => $section_id,
						"filter_category_id" => $category_id,
					]
				);
			}
	}

	public function libmns_transactions_page(){
		$this->libmns_librarian_can_access_page( 'owt7_library_transactions' );
		global $wpdb;

			$allowed_pages = [
				"books" => [
					"books",
					"borrow",
					"return-history",
					"return",
					"history"
				]
			];

			$mod = isset($_REQUEST['mod']) ? strtolower($_REQUEST['mod']) : ""; 
			$fn = isset($_REQUEST['fn']) ? strtolower($_REQUEST['fn']) : ""; 

			if(!empty($fn) && in_array($fn, $allowed_pages[$mod])){

				$fn = str_replace("-", "_", $fn);

				if ( $mod === 'books' && $fn === 'borrow' && ! LIBMNS_Roles_Helper_FREE::libmns_current_user_can( 'owt7_lms_borrow_book' ) ) {
					$fn = '';
				}
				if ( $mod === 'books' && $fn === 'return' && ! LIBMNS_Roles_Helper_FREE::libmns_current_user_can( 'owt7_lms_return_book' ) ) {
					$fn = '';
				}
				if ( $mod === 'books' && $fn === 'return_history' && ! LIBMNS_Roles_Helper_FREE::libmns_current_user_can( 'owt7_lms_view_return_list' ) ) {
					$fn = '';
				}
				if ( $mod === 'books' && $fn === 'history' && ! LIBMNS_Roles_Helper_FREE::libmns_current_user_can( 'owt7_lms_view_borrow_list' ) ) {
					$fn = '';
				}

				if($mod == "books" && ! empty( $fn ) ){

					$returns = [];

					$branches = $this->libmns_get_distinct_active_branches();

					$categories = $wpdb->get_results(
						$wpdb->prepare(
							"SELECT id, name from " . LIBMNS_Table_Helper_FREE::get_table_name( 'categories' ) . " WHERE status = %d",
							1
						)
					);

					$days_raw = $wpdb->get_results(
						$wpdb->prepare(
							"SELECT id, days from " . LIBMNS_Table_Helper_FREE::get_table_name( 'issue_days' ) . " WHERE status = %d",
							1
						)
					);
					$by_days = array();
					foreach ( (array) $days_raw as $row ) {
						$num = (int) trim( str_replace( array( 'days', 'Days', ' Days', ' days' ), '', (string) $row->days ) );
						if ( $num > 0 && ! isset( $by_days[ $num ] ) ) {
							$by_days[ $num ] = $row;
						}
					}
					ksort( $by_days, SORT_NUMERIC );
					$days = array_values( $by_days );

					if($fn == "return_history"){

						$returns = $wpdb->get_results(
							"SELECT rt.id, rt.return_id, rt.u_id, rt.wp_user, rt.borrow_id, COALESCE(NULLIF(TRIM(rt.accession_number), ''), (SELECT br.accession_number FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' ) . " br WHERE br.borrow_id = rt.borrow_id LIMIT 1)) as accession_number, rt.return_status, rt.return_condition, rt.return_remark, rt.is_self_return, rt.status, rt.created_at, (SELECT category.name FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'categories' ). " category WHERE category.id = rt.category_id LIMIT 1) as category_name, (SELECT book.name FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'books' ). " book WHERE book.id = rt.book_id LIMIT 1) as book_name, (SELECT book.book_id FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'books' ). " book WHERE book.id = rt.book_id LIMIT 1) as book_book_id, (SELECT branch.name FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'branches' ). " branch WHERE branch.id = rt.branch_id LIMIT 1) as branch_name, (SELECT user.name FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'users' ). " user WHERE user.id = rt.u_id LIMIT 1) as user_name, (SELECT user.email FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'users' ). " user WHERE user.id = rt.u_id LIMIT 1) as user_email, (SELECT user.u_id FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'users' ). " user WHERE user.id = rt.u_id LIMIT 1) as user_u_id, (SELECT borrow.borrows_days FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' ). " borrow WHERE borrow.borrow_id = rt.borrow_id LIMIT 1) as total_days, (SELECT borrow.created_at FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' ). " borrow WHERE borrow.borrow_id = rt.borrow_id LIMIT 1) as issued_on, (SELECT fine.has_paid FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'book_late_fine' ). " fine WHERE fine.return_id = rt.id LIMIT 1) as has_paid, (SELECT fine.fine_amount FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'book_late_fine' ). " fine WHERE fine.return_id = rt.id LIMIT 1) as fine_amount, (SELECT fine.extra_days FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'book_late_fine' ). " fine WHERE fine.return_id = rt.id LIMIT 1) as extra_days FROM ".LIBMNS_Table_Helper_FREE::get_table_name( 'book_return' ). " as rt ORDER by rt.id DESC"
						);
					}

					$borrow_prefix = defined( 'LIBMNS_BOOK_BORROW_PREFIX' ) ? LIBMNS_BOOK_BORROW_PREFIX : 'LMSBB';
					$return_prefix = defined( 'LIBMNS_BOOK_RETURN_PREFIX' ) ? LIBMNS_BOOK_RETURN_PREFIX : 'LMSBR';
					$this->libmns_include_template_file(
						"transactions", 
						"owt7_library_{$mod}_{$fn}",
						[
							"branches"   => $branches,
							"categories" => $categories,
							"returns"    => $returns,
							"days"       => $days,
							"next_borrow_id" => $this->libmns_generate_id_timestamp_suffix( $borrow_prefix ),
							"next_return_id" => $this->libmns_generate_id_timestamp_suffix( $return_prefix ),
						]
					);
				} elseif ( $mod === 'books' && $fn === '' ) {
					$borrows = $wpdb->get_results(
						"SELECT borrow.id, borrow.u_id, borrow.wp_user, borrow.borrow_id, borrow.accession_number, borrow.borrows_days, borrow.return_date, borrow.checkout_status, borrow.is_self_checkout, borrow.status, borrow.created_at, (SELECT category.name FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'categories' ) . " category WHERE category.id = borrow.category_id LIMIT 1) as category_name, (SELECT book.name FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'books' ) . " book WHERE book.id = borrow.book_id LIMIT 1) as book_name, (SELECT book.book_id FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'books' ) . " book WHERE book.id = borrow.book_id LIMIT 1) as book_book_id, (SELECT branch.name FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'branches' ) . " branch WHERE branch.id = borrow.branch_id LIMIT 1) as branch_name, (SELECT user.name FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'users' ) . " user WHERE user.id = borrow.u_id LIMIT 1) as user_name, (SELECT user.email FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'users' ) . " user WHERE user.id = borrow.u_id LIMIT 1) as user_email, (SELECT user.u_id FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'users' ) . " user WHERE user.id = borrow.u_id LIMIT 1) as user_u_id FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' ) . " borrow WHERE borrow.status = 1 ORDER by borrow.id DESC"
					);
					$branches = $this->libmns_get_distinct_active_branches();
					$categories = $wpdb->get_results( $wpdb->prepare( "SELECT id, name from " . LIBMNS_Table_Helper_FREE::get_table_name( 'categories' ) . " WHERE status = %d", 1 ) );
					$this->libmns_include_template_file( "transactions", "owt7_library_books_transactions", array( "borrows" => $borrows, "branches" => $branches, "categories" => $categories ) );
				} else {

					$this->libmns_include_template_file(
						"transactions", 
						"owt7_library_books_{$mod}_{$fn}"
					);
				}
			}else{

				$borrows = $wpdb->get_results(
					"SELECT borrow.id, borrow.u_id, borrow.wp_user, borrow.borrow_id, borrow.accession_number, borrow.borrows_days, borrow.return_date, borrow.checkout_status, borrow.is_self_checkout, borrow.status, borrow.created_at, (SELECT category.name FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'categories' ). " category WHERE category.id = borrow.category_id LIMIT 1) as category_name, (SELECT book.name FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'books' ). " book WHERE book.id = borrow.book_id LIMIT 1) as book_name, (SELECT book.book_id FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'books' ). " book WHERE book.id = borrow.book_id LIMIT 1) as book_book_id, (SELECT branch.name FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'branches' ). " branch WHERE branch.id = borrow.branch_id LIMIT 1) as branch_name, (SELECT user.name FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'users' ). " user WHERE user.id = borrow.u_id LIMIT 1) as user_name, (SELECT user.email FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'users' ). " user WHERE user.id = borrow.u_id LIMIT 1) as user_email, (SELECT user.u_id FROM " .LIBMNS_Table_Helper_FREE::get_table_name( 'users' ). " user WHERE user.id = borrow.u_id LIMIT 1) as user_u_id FROM ".LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' ). " borrow WHERE borrow.status = 1 ORDER by borrow.id DESC"
				);

				$branches = $this->libmns_get_distinct_active_branches();

				$categories = $wpdb->get_results(
					$wpdb->prepare(
						"SELECT id, name from " . LIBMNS_Table_Helper_FREE::get_table_name( 'categories' ) . " WHERE status = %d",
						1
					)
				);

				$this->libmns_include_template_file(
					"transactions", 
					"owt7_library_books_transactions",
					[
						"borrows" => $borrows,
						"branches" => $branches,
						"categories" => $categories
					]
				);
			}
	}

	public function libmns_reports_page() {
		wp_safe_redirect( admin_url( 'admin.php?page=library_management_system' ) );
		exit;
	}

	public function libmns_settings_page(){
		$this->libmns_librarian_can_access_page( 'owt7_library_settings' );
		global $wpdb;
		$mod = isset($_REQUEST['mod']) ? strtolower($_REQUEST['mod']) : ""; 

		if(!empty($mod)){

			if($mod == "days"){
				$days = $wpdb->get_results(
					"SELECT * FROM ".LIBMNS_Table_Helper_FREE::get_table_name( 'issue_days' )
				);
				$this->libmns_include_template_file(
					"settings", 
					"owt7_library_{$mod}_settings",
					[
						"days" => $days
					]
				);
			} elseif ( $mod == "lms_frontend" ) {
				wp_enqueue_media();
				$lms_permissions = LIBMNS_Roles_Helper_FREE::libmns_get_permissions_structure();
				$lms_roles = LIBMNS_Roles_Helper_FREE::libmns_get_lms_roles_with_permissions();
				$allowed_caps_by_role = LIBMNS_Roles_Helper_FREE::libmns_get_allowed_caps_by_role();
				$public_view_settings = get_option( 'owt7_lms_public_view_settings', array() );
				$this->libmns_include_template_file(
					"settings",
					"owt7_library_settings",
					array(
						'lms_permissions'           => $lms_permissions,
						'lms_roles'                 => $lms_roles,
						'allowed_caps_by_role'      => $allowed_caps_by_role,
						'public_view_settings'      => $public_view_settings,
						'open_lms_frontend_modal'   => true,
					)
				);
			} elseif ( $mod == "theme" ) {
				$this->libmns_include_template_file(
					"settings",
					"owt7_library_theme_settings"
				);
			} elseif ( in_array( $mod, array( 'backup' ), true ) ) {
				wp_safe_redirect( admin_url( 'admin.php?page=owt7_library_settings' ) );
				exit;
			} else{
				$this->libmns_include_template_file(
					"settings", 
					"owt7_library_{$mod}_settings"
				);
			}
		} else {

			wp_enqueue_media();
			$lms_permissions = LIBMNS_Roles_Helper_FREE::libmns_get_permissions_structure();
			$lms_roles = LIBMNS_Roles_Helper_FREE::libmns_get_lms_roles_with_permissions();
			$allowed_caps_by_role = LIBMNS_Roles_Helper_FREE::libmns_get_allowed_caps_by_role();
			$public_view_settings = get_option( 'owt7_lms_public_view_settings', array() );
			$open_lms_frontend_modal = ( isset( $_REQUEST['mod'] ) && strtolower( $_REQUEST['mod'] ) === 'lms_frontend' );
			$this->libmns_include_template_file(
				"settings",
				"owt7_library_settings",
				array(
					'lms_permissions'           => $lms_permissions,
					'lms_roles'                 => $lms_roles,
					'allowed_caps_by_role'      => $allowed_caps_by_role,
					'public_view_settings'      => $public_view_settings,
					'open_lms_frontend_modal'   => $open_lms_frontend_modal,
				)
			);
		}
	}

	public function libmns_upgrade_pro_page(){
		$this->libmns_librarian_can_access_page( 'owt7_library_upgrade_pro' );
		$this->libmns_include_template_file("lms", "owt7_library_upgrade_pro");
	}

	public function libmns_library_books_catalog_page() {
		$this->libmns_librarian_can_access_page( 'owt7_library_books_catalogue' );
		global $wpdb;
		$tbl_books     = LIBMNS_Table_Helper_FREE::get_table_name( 'books' );
		$tbl_categories = LIBMNS_Table_Helper_FREE::get_table_name( 'categories' );
		$tbl_bookcases  = LIBMNS_Table_Helper_FREE::get_table_name( 'bookcases' );
		$tbl_sections   = LIBMNS_Table_Helper_FREE::get_table_name( 'sections' );
		$tbl_borrow    = LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' );

		$bid = isset( $_GET['bid'] ) ? absint( $_GET['bid'] ) : 0;
		if ( $bid > 0 ) {
			$single_sql = $wpdb->prepare(
				"SELECT book.id, book.book_id, book.name, book.author_name, book.amount, book.isbn, book.stock_quantity, book.cover_image, book.publication_name, book.publication_year, book.description,
			(SELECT category.name FROM {$tbl_categories} AS category WHERE category.id = book.category_id LIMIT 1) AS category_name,
			(SELECT bkcase.name FROM {$tbl_bookcases} AS bkcase WHERE bkcase.id = book.bookcase_id LIMIT 1) AS bookcase_name,
			(SELECT section.name FROM {$tbl_sections} AS section WHERE section.id = book.bookcase_section_id LIMIT 1) AS section_name,
			(SELECT COUNT(*) FROM {$tbl_borrow} AS borrow WHERE borrow.book_id = book.id AND borrow.status = 1) AS has_active_borrow
			FROM {$tbl_books} AS book WHERE book.id = %d AND book.status = 1",
				$bid
			);
			$book = $wpdb->get_row( $single_sql );
			if ( $book ) {
				$user_borrowed_book_ids = array();
				$checkout_statuses      = array();
				$return_statuses        = array();
				$ids_cat = $this->libmns_get_current_user_library_ids();
				if ( $ids_cat['wp_user_id'] > 0 ) {
					$tbl_return = LIBMNS_Table_Helper_FREE::get_table_name( 'book_return' );
					$library_user_id = $ids_cat['library_user_id'];
					$wp_user_id_cat = $ids_cat['wp_user_id'];
					$borrow_u_cond = $library_user_id !== null
						? $wpdb->prepare( "(u_id = %d OR (wp_user = 1 AND u_id = %d))", $library_user_id, $wp_user_id_cat )
						: $wpdb->prepare( "wp_user = 1 AND u_id = %d", $wp_user_id_cat );
					$return_u_cond = $library_user_id !== null
						? $wpdb->prepare( "(u_id = %d OR (wp_user = 1 AND u_id = %d))", $library_user_id, $wp_user_id_cat )
						: $wpdb->prepare( "wp_user = 1 AND u_id = %d", $wp_user_id_cat );
					$borrowed_rows = $wpdb->get_results(
						"SELECT book_id, checkout_status FROM {$tbl_borrow} WHERE {$borrow_u_cond} AND status = 1 AND (checkout_status IN (1, 2, 5))"
					);
					$return_rows = $wpdb->get_results(
						"SELECT book_id, return_status FROM {$tbl_return} WHERE {$return_u_cond} AND status = 1"
					);
					if ( ! empty( $borrowed_rows ) ) {
						$user_borrowed_book_ids = array_map( 'intval', array_column( $borrowed_rows, 'book_id' ) );
						foreach ( $borrowed_rows as $borrowed_row ) {
							$checkout_statuses[ (int) $borrowed_row->book_id ] = (int) $borrowed_row->checkout_status;
						}
					}
					if ( ! empty( $return_rows ) ) {
						foreach ( $return_rows as $return_row ) {
							$return_statuses[ (int) $return_row->book_id ] = (int) $return_row->return_status;
						}
					}
				}
				$portal = self::libmns_get_library_user_portal_settings();
				$checkout_overview = $this->libmns_get_library_user_checkout_overview( $portal );
				$catalogue_url = admin_url( 'admin.php?page=owt7_library_books_catalogue' );
				$single_params = array(
					'book'                    => $book,
					'portal'                  => $portal,
					'checkout_overview'       => $checkout_overview,
					'user_borrowed_book_ids'  => $user_borrowed_book_ids,
					'checkout_statuses'       => $checkout_statuses,
					'return_statuses'         => $return_statuses,
					'catalogue_url'           => $catalogue_url,
				);
				$this->libmns_include_template_file( 'library_user', 'owt7_library_books_catalogue_single', $single_params );
				return;
			}
			wp_safe_redirect( admin_url( 'admin.php?page=owt7_library_books_catalogue' ) );
			exit;
		}

		$catalogue_params = $this->libmns_get_library_user_catalogue_params( $_GET );
		$this->libmns_include_template_file( 'library_user', 'owt7_library_books_catalogue', $catalogue_params );
	}

	public function libmns_library_user_borrowed_page() {
		$this->libmns_librarian_can_access_page( 'owt7_library_user_borrowed' );
		global $wpdb;
		$ids = self::libmns_get_current_user_library_ids();
		$borrowed_books_list = array();
		if ( $ids['wp_user_id'] > 0 ) {
			$tbl_borrow   = LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' );
			$tbl_books    = LIBMNS_Table_Helper_FREE::get_table_name( 'books' );
			$tbl_category = LIBMNS_Table_Helper_FREE::get_table_name( 'categories' );
			$tbl_return   = LIBMNS_Table_Helper_FREE::get_table_name( 'book_return' );
			$tbl_bookcase  = LIBMNS_Table_Helper_FREE::get_table_name( 'bookcases' );
			$tbl_section  = LIBMNS_Table_Helper_FREE::get_table_name( 'sections' );
			$library_user_id = $ids['library_user_id'];
			$wp_user_id = $ids['wp_user_id'];
			$u_condition = $library_user_id !== null
				? $wpdb->prepare( '(borrow.u_id = %d OR (borrow.wp_user = 1 AND borrow.u_id = %d))', $library_user_id, $wp_user_id )
				: $wpdb->prepare( 'borrow.wp_user = 1 AND borrow.u_id = %d', $wp_user_id );
			$borrowed_books_list = $wpdb->get_results(
				"SELECT borrow.id AS borrow_record_id, borrow.borrow_id, borrow.accession_number, borrow.checkout_status, borrow.is_self_checkout,
				borrow.created_at AS issue_date, borrow.return_date AS expected_return_date,
				(SELECT rt.return_status FROM {$tbl_return} rt WHERE rt.borrow_id = borrow.borrow_id AND rt.u_id = borrow.u_id AND rt.status = 1 ORDER BY rt.id DESC LIMIT 1) AS pending_return_status,
				(CASE WHEN borrow.checkout_status = 1 THEN 'admin' WHEN borrow.checkout_status = 2 OR borrow.is_self_checkout = 1 THEN 'self' ELSE '' END) AS issuer_type,
				book.*,
				(SELECT name FROM {$tbl_category} WHERE id = book.category_id LIMIT 1) AS category_name,
				(SELECT name FROM {$tbl_bookcase} WHERE id = book.bookcase_id LIMIT 1) AS bookcase_name,
				(SELECT name FROM {$tbl_section} WHERE id = book.bookcase_section_id LIMIT 1) AS section_name
				FROM {$tbl_borrow} borrow INNER JOIN {$tbl_books} book ON book.id = borrow.book_id
				WHERE {$u_condition} AND borrow.status = 1 AND (borrow.checkout_status IN (1, 2, 5))
				ORDER BY borrow.created_at DESC"
			);
		}
		$portal = self::libmns_get_library_user_portal_settings();
		$this->libmns_include_template_file( 'library_user', 'owt7_library_books_borrowed', array( 'borrowed_books_list' => $borrowed_books_list, 'portal' => $portal ) );
	}

	public function libmns_library_user_returned_page() {
		$this->libmns_librarian_can_access_page( 'owt7_library_user_returned' );
		global $wpdb;
		$ids = self::libmns_get_current_user_library_ids();
		$returned_books_list = array();
		if ( $ids['wp_user_id'] > 0 && $ids['library_user_id'] > 0 ) {
			$tbl_return   = LIBMNS_Table_Helper_FREE::get_table_name( 'book_return' );
			$tbl_borrow   = LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' );
			$tbl_books    = LIBMNS_Table_Helper_FREE::get_table_name( 'books' );
			$tbl_category = LIBMNS_Table_Helper_FREE::get_table_name( 'categories' );
			$tbl_bookcase  = LIBMNS_Table_Helper_FREE::get_table_name( 'bookcases' );
			$tbl_section  = LIBMNS_Table_Helper_FREE::get_table_name( 'sections' );
			$tbl_late_fine = LIBMNS_Table_Helper_FREE::get_table_name( 'book_late_fine' );
			$tbl_users_lib = LIBMNS_Table_Helper_FREE::get_table_name( 'users' );
			$wp_users     = $wpdb->prefix . 'users';
			$library_user_id = (int) $ids['library_user_id'];

			$returned_by_name_sql = "COALESCE(NULLIF(TRIM((SELECT display_name FROM {$wp_users} WHERE ID = (SELECT wp_user_id FROM {$tbl_users_lib} WHERE id = br.u_id AND status = 1 LIMIT 1) LIMIT 1)), ''), (SELECT name FROM {$tbl_users_lib} WHERE id = br.u_id AND status = 1 LIMIT 1))";
			$returned_books_list = $wpdb->get_results( $wpdb->prepare(
				"SELECT rt.id AS return_record_id, rt.return_id, rt.return_status, br.borrow_id, COALESCE(NULLIF(TRIM(rt.accession_number), ''), br.accession_number) AS accession_number, rt.has_fine_status, rt.created_at AS return_date,
				br.created_at AS issue_date, br.return_date AS expected_return_date,
				{$returned_by_name_sql} AS returned_by_name,
				(CASE WHEN br.checkout_status = 1 THEN 'admin' WHEN br.checkout_status = 2 OR br.is_self_checkout = 1 THEN 'self' ELSE '' END) AS issuer_type,
			(SELECT fine_amount FROM {$tbl_late_fine} WHERE return_id = rt.id LIMIT 1) AS fine_amount,
			(SELECT has_paid FROM {$tbl_late_fine} WHERE return_id = rt.id LIMIT 1) AS fine_has_paid,
				book.*,
				(SELECT name FROM {$tbl_category} WHERE id = book.category_id LIMIT 1) AS category_name,
				(SELECT name FROM {$tbl_bookcase} WHERE id = book.bookcase_id LIMIT 1) AS bookcase_name,
				(SELECT name FROM {$tbl_section} WHERE id = book.bookcase_section_id LIMIT 1) AS section_name
				FROM {$tbl_return} rt
				INNER JOIN {$tbl_borrow} br ON br.borrow_id = rt.borrow_id AND br.u_id = %d
				INNER JOIN {$tbl_books} book ON book.id = rt.book_id
				WHERE (rt.return_status = 5)
				ORDER BY rt.created_at DESC",
				$library_user_id
			) );
		}
		$portal = self::libmns_get_library_user_portal_settings();
		$this->libmns_include_template_file( 'library_user', 'owt7_library_books_returned', array( 'returned_books_list' => $returned_books_list, 'portal' => $portal ) );
	}

	public static function libmns_get_current_user_library_ids() {
		global $wpdb;
		$wp_user_id = get_current_user_id();
		$library_user_id = null;
		if ( $wp_user_id > 0 ) {
			$tbl_users = LIBMNS_Table_Helper_FREE::get_table_name( 'users' );
			$library_user_id = $wpdb->get_var( $wpdb->prepare(
				"SELECT id FROM {$tbl_users} WHERE wp_user_id = %d AND status = 1 LIMIT 1",
				$wp_user_id
			) );
			$library_user_id = $library_user_id !== null ? (int) $library_user_id : null;
		}
		return array( 'library_user_id' => $library_user_id, 'wp_user_id' => (int) $wp_user_id );
	}

	private function libmns_include_template_file($mod, $template, $lib_params = array()){

		ob_start();
		$params = $lib_params;
		if(!empty($mod)){
			include_once LIBMNS_PLUGIN_DIR_PATH . "admin/views/{$mod}/" . $template . ".php";
		}else{
			include_once LIBMNS_PLUGIN_DIR_PATH . 'admin/views/' . $template . ".php";
		}
		$template = ob_get_contents();
		ob_end_clean();

		echo $template;
	}

	public function libmns_generate_id_timestamp_suffix( $prefix ) {
		$ts = sprintf( '%.0f', microtime( true ) * 1000 );
		$suffix = substr( $ts, -7 );
		return $prefix . $suffix;
	}

	public function libmns_json($response = array()) {
		$data = isset($response[2]) ? $response[2] : array();
		$ar = array('sts' => $response[0], 'msg' => $response[1], 'arr' => $data);
        print_r(json_encode($ar));
        die;
    }

	public function libmns_url_exists($url) {
		
		$headers = @get_headers($url);
		
		if($headers && strpos($headers[0], '200') !== false) {
			return true;
		}
		return false;
	}

	public function libmns_get_book_copies_data( $book_id_num ) {
		global $wpdb;
		$this->table_activator->owt7_lms_ensure_book_copies_code_columns();
		$book_id_num = absint( $book_id_num );
		$tbl_books = LIBMNS_Table_Helper_FREE::get_table_name( 'books' );
		$tbl_copies = LIBMNS_Table_Helper_FREE::get_table_name( 'data_books_copies' );
		$book = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT id, book_id, name, author_name, publication_name FROM `{$tbl_books}` WHERE id = %d LIMIT 1",
				$book_id_num
			),
			OBJECT
		);
		if ( ! $book || empty( $book->book_id ) ) {
			return array( 'book_title' => '', 'book_author' => '', 'book_publication' => '', 'copies' => array() );
		}
		$copies = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT id, book_id, accession_number, status, shelf_location, notes, is_barcode_exists, is_qrcode_exists, barcode_path, qrcode_path, created_at FROM `{$tbl_copies}` WHERE book_id = %s ORDER BY accession_number ASC",
				$book->book_id
			),
			ARRAY_A
		);
		$upload_baseurl = wp_upload_dir()['baseurl'];
		foreach ( $copies as $i => $row ) {
			$copies[ $i ]['barcode_url'] = '';
			$copies[ $i ]['qrcode_url']  = '';
			if ( ! empty( $row['barcode_path'] ) ) {
				$copies[ $i ]['barcode_url'] = $upload_baseurl . '/' . ltrim( $row['barcode_path'], '/' );
			}
			if ( ! empty( $row['qrcode_path'] ) ) {
				$copies[ $i ]['qrcode_url'] = $upload_baseurl . '/' . ltrim( $row['qrcode_path'], '/' );
			}
		}
		return array(
			'book_title'        => $book->name ? $book->name : $book->book_id,
			'book_author'       => isset( $book->author_name ) ? $book->author_name : '',
			'book_publication'  => isset( $book->publication_name ) ? $book->publication_name : '',
			'copies'            => is_array( $copies ) ? $copies : array(),
		);
	}

	public function libmns_schedule_checktime($schedules) {
		$schedules['check_lms_status'] = array( 'interval' => 1800, 'display'  => __('Every 30 Minutes', 'library-management-system'));
		return $schedules;
	}

	public function libmns_zip_extension_missing_notice() {
		if ( class_exists( 'ZipArchive' ) ) {
			return;
		}
		echo '<div class="notice notice-warning"><p><strong>' . esc_html__( 'Library Management System:', 'library-management-system' ) . '</strong> '
			. esc_html__( 'Excel export and import require the PHP Zip extension. Enable it in php.ini (extension=zip) and restart the web server.', 'library-management-system' )
			. '</p></div>';
	}

	public function libmns_manage_books_stock($book_id, $action){
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

	public function libmns_allocate_next_available_accession( $book_id ) {
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

	public function libmns_release_accession( $accession_number ) {
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

	public function libmns_allocate_specific_accession( $accession_number ) {
		global $wpdb;
		if ( ! is_string( $accession_number ) || trim( $accession_number ) === '' ) {
			return null;
		}
		$accession_number = trim( $accession_number );
		$tbl_copies = LIBMNS_Table_Helper_FREE::get_table_name( 'data_books_copies' );
		$copy = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT id, accession_number FROM `{$tbl_copies}` WHERE accession_number = %s AND status = %s LIMIT 1",
				$accession_number,
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

	public function libmns_peek_next_available_accession( $book_id ) {
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
				"SELECT accession_number FROM `{$tbl_copies}` WHERE book_id = %s AND status = %s ORDER BY accession_number ASC LIMIT 1",
				$book->book_id,
				'available'
			),
			OBJECT
		);
		return ( $copy && ! empty( $copy->accession_number ) ) ? $copy->accession_number : null;
	}

	public function libmns_generate_user_id_pattern() {
		global $wpdb;
		$prefix = defined( 'LIBMNS_USER_PREFIX' ) ? LIBMNS_USER_PREFIX : 'LMSUS';
		$tbl    = LIBMNS_Table_Helper_FREE::get_table_name( 'users' );
		$max_attempts = 20;
		for ( $attempt = 0; $attempt < $max_attempts; $attempt++ ) {
			$u_id = $this->libmns_generate_id_timestamp_suffix( $prefix );
			$exists = $wpdb->get_var( $wpdb->prepare(
				"SELECT id FROM {$tbl} WHERE LOWER(TRIM(u_id)) = %s LIMIT 1",
				strtolower( trim( $u_id ) )
			) );
			if ( ! $exists ) {
				return $u_id;
			}
		}
		// Fallback: timestamp + random digit
		return $this->libmns_generate_id_timestamp_suffix( $prefix ) . wp_rand( 0, 9 );
	}

	public function libmns_generate_book_id_pattern() {
		global $wpdb;
		$prefix  = defined( 'LIBMNS_BOOK_PREFIX' ) ? LIBMNS_BOOK_PREFIX : 'LMSBK';
		$tbl     = LIBMNS_Table_Helper_FREE::get_table_name( 'books' );
		$max_attempts = 20;
		for ( $attempt = 0; $attempt < $max_attempts; $attempt++ ) {
			$book_id = $this->libmns_generate_id_timestamp_suffix( $prefix );
			$exists = $wpdb->get_var( $wpdb->prepare(
				"SELECT id FROM {$tbl} WHERE LOWER(TRIM(book_id)) = %s LIMIT 1",
				strtolower( trim( $book_id ) )
			) );
			if ( ! $exists ) {
				return $book_id;
			}
		}
		return $this->libmns_generate_id_timestamp_suffix( $prefix ) . wp_rand( 0, 9 );
	}

	public static function libmns_normalize_comma_separated( $input ) {
		if ( ! is_string( $input ) ) {
			return '';
		}
		$parts = array_map( 'trim', explode( ',', $input ) );
		$parts = array_filter( $parts );
		return implode( ', ', $parts );
	}

	public static function libmns_render_comma_tags( $value, $ucwords = true ) {
		if ( ! is_string( $value ) || trim( $value ) === '' ) {
			return '';
		}
		$parts = array_filter( array_map( 'trim', explode( ',', $value ) ) );
		if ( empty( $parts ) ) {
			return '';
		}
		$out = array();
		foreach ( $parts as $part ) {
			$display = $ucwords ? ucwords( $part ) : $part;
			$out[]   = '<span class="owt7-lms-tag">' . esc_html( $display ) . '</span>';
		}
		return implode( ' ', $out );
	}

	public static function libmns_current_user_can( $cap ) {
		return LIBMNS_Roles_Helper_FREE::libmns_current_user_can( $cap );
	}

	public static function libmns_menu_required_caps() {
		return LIBMNS_Roles_Helper_FREE::libmns_menu_required_caps();
	}

	public static function libmns_get_permissions_structure() {
		return LIBMNS_Roles_Helper_FREE::libmns_get_permissions_structure();
	}

	public static function libmns_get_lms_roles_with_permissions() {
		return LIBMNS_Roles_Helper_FREE::libmns_get_lms_roles_with_permissions();
	}

	public static function libmns_get_assignable_lms_roles() {
		return LIBMNS_Roles_Helper_FREE::libmns_get_assignable_lms_roles();
	}

	public static function libmns_get_restricted_lms_roles() {
		return LIBMNS_Roles_Helper_FREE::libmns_get_restricted_lms_roles();
	}

	public static function libmns_get_allowed_caps_by_role() {
		return LIBMNS_Roles_Helper_FREE::libmns_get_allowed_caps_by_role();
	}

	public static function libmns_get_default_caps_for_role( $role_slug ) {
		return LIBMNS_Roles_Helper_FREE::libmns_get_default_caps_for_role( $role_slug );
	}

	public static function libmns_sync_excluded_wp_roles() {
		return array( 'administrator' );
	}

	public static function libmns_get_library_user_portal_settings_defaults() {
		return array(
			'library_user_books_per_row'           => 4,
			'library_user_card_design'             => 'default',
			'library_user_enable_category_filter'  => 1,
			'library_user_enable_author_filter'    => 1,
			'library_user_enable_search'           => 0,
			'library_user_books_per_page'          => 8,
			'library_user_self_checkout'           => 0,
			'library_user_self_return'             => 0,
			'library_user_checkout_more_than_one'  => 0,
			'library_user_checkout_days'           => 30,
			'library_user_self_checkout_roles'     => array( 'owt7_library_user' ),
			'library_user_enable_list_filter_author'    => 0,
			'library_user_enable_list_filter_category'  => 0,
		);
	}

	public static function libmns_get_library_user_portal_settings() {
		$saved = get_option( 'owt7_lms_library_user_portal_settings', array() );
		$defaults = self::libmns_get_library_user_portal_settings_defaults();
		$settings = array_merge( $defaults, is_array( $saved ) ? $saved : array() );
		$settings['library_user_enable_search'] = 0;
		$settings['library_user_self_checkout'] = 0;
		$settings['library_user_self_return'] = 0;
		$settings['library_user_checkout_more_than_one'] = 0;
		$settings['library_user_checkout_days'] = LIBMNS_DEFAULT_BORROW_DAYS;
		$settings['library_user_enable_list_filter_author'] = 0;
		$settings['library_user_enable_list_filter_category'] = 0;
		$settings['library_user_self_checkout_roles'] = array( 'owt7_library_user' );
		return $settings;
	}

	private function libmns_get_library_user_checkout_overview( $portal = array() ) {
		$portal = is_array( $portal ) ? $portal : array();
		$days   = isset( $portal['library_user_checkout_days'] ) ? (int) $portal['library_user_checkout_days'] : LIBMNS_DEFAULT_BORROW_DAYS;
		$days   = max( 1, $days );
		$format = get_option( 'date_format', 'Y-m-d' );
		$request_timestamp  = current_time( 'timestamp' );
		$expected_timestamp = strtotime( '+' . $days . ' days', $request_timestamp );
		return array(
			'days'                 => $days,
			'request_date'         => wp_date( $format, $request_timestamp ),
			'expected_return_date' => wp_date( $format, $expected_timestamp ),
		);
	}

	public function libmns_get_library_user_catalogue_params( $request = array() ) {
		global $wpdb;

		$request        = is_array( $request ) ? $request : array();
		$tbl_books      = LIBMNS_Table_Helper_FREE::get_table_name( 'books' );
		$tbl_categories = LIBMNS_Table_Helper_FREE::get_table_name( 'categories' );
		$tbl_bookcases  = LIBMNS_Table_Helper_FREE::get_table_name( 'bookcases' );
		$tbl_sections   = LIBMNS_Table_Helper_FREE::get_table_name( 'sections' );
		$tbl_borrow     = LIBMNS_Table_Helper_FREE::get_table_name( 'book_borrow' );
		$portal         = self::libmns_get_library_user_portal_settings();
		$per_page       = isset( $portal['library_user_books_per_page'] ) ? max( 1, (int) $portal['library_user_books_per_page'] ) : 8;
		$per_row        = isset( $portal['library_user_books_per_row'] ) ? max( 1, min( 6, (int) $portal['library_user_books_per_row'] ) ) : 4;
		$card_design    = isset( $portal['library_user_card_design'] ) ? $portal['library_user_card_design'] : 'default';

		$current_page  = isset( $request['grid'] ) ? max( 1, absint( $request['grid'] ) ) : 1;
		$filter_cat    = isset( $request['cat'] ) ? absint( $request['cat'] ) : 0;
		$filter_author = isset( $request['author'] ) ? sanitize_text_field( wp_unslash( $request['author'] ) ) : '';
		$filter_search = isset( $request['search'] ) ? sanitize_text_field( wp_unslash( $request['search'] ) ) : '';

		$base_sql = "SELECT book.id, book.book_id, book.name, book.author_name, book.amount, book.isbn, book.stock_quantity, book.cover_image, book.publication_name, book.publication_year, book.description,
			(SELECT category.name FROM {$tbl_categories} AS category WHERE category.id = book.category_id LIMIT 1) AS category_name,
			(SELECT bkcase.name FROM {$tbl_bookcases} AS bkcase WHERE bkcase.id = book.bookcase_id LIMIT 1) AS bookcase_name,
			(SELECT section.name FROM {$tbl_sections} AS section WHERE section.id = book.bookcase_section_id LIMIT 1) AS section_name,
			(SELECT COUNT(*) FROM {$tbl_borrow} AS borrow WHERE borrow.book_id = book.id AND borrow.status = 1) AS has_active_borrow
			FROM {$tbl_books} AS book WHERE book.status = 1";
		$where_clauses = array();
		$prepare_args  = array();

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

		if ( ! empty( $where_clauses ) ) {
			$base_sql .= ' AND ' . implode( ' AND ', $where_clauses );
		}

		$count_sql = "SELECT COUNT(*) FROM {$tbl_books} AS book WHERE book.status = 1";
		if ( ! empty( $where_clauses ) ) {
			$count_sql .= ' AND ' . implode( ' AND ', $where_clauses );
		}
		if ( ! empty( $prepare_args ) ) {
			$count_sql = $wpdb->prepare( $count_sql, $prepare_args );
		}

		$total_books  = (int) $wpdb->get_var( $count_sql );
		$total_pages  = $per_page > 0 ? max( 1, (int) ceil( $total_books / $per_page ) ) : 1;
		$current_page = min( $current_page, $total_pages );
		$offset       = ( $current_page - 1 ) * $per_page;
		$order_sql    = ' ORDER BY book.name ASC';
		$limit_sql    = $wpdb->prepare( ' LIMIT %d OFFSET %d', $per_page, $offset );
		$full_sql     = $base_sql . $order_sql . $limit_sql;

		if ( ! empty( $prepare_args ) ) {
			$full_sql = $wpdb->prepare( $full_sql, $prepare_args );
		}

		$books = $wpdb->get_results( $full_sql );
		$categories = $wpdb->get_results(
			"SELECT c.id, c.name, (SELECT COUNT(*) FROM {$tbl_books} AS b WHERE b.category_id = c.id AND b.status = 1) AS book_count FROM {$tbl_categories} AS c WHERE c.status = 1 ORDER BY c.name ASC"
		);
		$authors = $wpdb->get_results( "SELECT DISTINCT TRIM(author_name) AS author_name FROM {$tbl_books} WHERE status = 1 AND author_name IS NOT NULL AND TRIM(author_name) != '' ORDER BY author_name ASC" );

		$user_borrowed_book_ids = array();
		$checkout_statuses      = array();
		$return_statuses        = array();
		$ids = self::libmns_get_current_user_library_ids();
		if ( $ids['wp_user_id'] > 0 ) {
			$tbl_return = LIBMNS_Table_Helper_FREE::get_table_name( 'book_return' );
			$library_user_id = $ids['library_user_id'];
			$wp_user_id = $ids['wp_user_id'];
			$borrow_u_condition = $library_user_id !== null
				? $wpdb->prepare( "(u_id = %d OR (wp_user = 1 AND u_id = %d))", $library_user_id, $wp_user_id )
				: $wpdb->prepare( "wp_user = 1 AND u_id = %d", $wp_user_id );
			$return_u_condition = $library_user_id !== null
				? $wpdb->prepare( "(u_id = %d OR (wp_user = 1 AND u_id = %d))", $library_user_id, $wp_user_id )
				: $wpdb->prepare( "wp_user = 1 AND u_id = %d", $wp_user_id );
			$borrowed_rows = $wpdb->get_results(
				"SELECT book_id, checkout_status FROM {$tbl_borrow} WHERE {$borrow_u_condition} AND status = 1 AND (checkout_status IN (1, 2, 5))"
			);
			$return_rows = $wpdb->get_results(
				"SELECT book_id, return_status FROM {$tbl_return} WHERE {$return_u_condition} AND status = 1"
			);
			if ( ! empty( $borrowed_rows ) ) {
				$user_borrowed_book_ids = array_map( 'intval', array_column( $borrowed_rows, 'book_id' ) );
				foreach ( $borrowed_rows as $borrowed_row ) {
					$checkout_statuses[ (int) $borrowed_row->book_id ] = (int) $borrowed_row->checkout_status;
				}
			}
			if ( ! empty( $return_rows ) ) {
				foreach ( $return_rows as $return_row ) {
					$return_statuses[ (int) $return_row->book_id ] = (int) $return_row->return_status;
				}
			}
		}

		return array(
			'books'                  => $books,
			'categories'             => $categories,
			'authors'                => $authors,
			'portal'                 => $portal,
			'checkout_overview'      => $this->libmns_get_library_user_checkout_overview( $portal ),
			'per_row'                => $per_row,
			'card_design'            => $card_design,
			'total_books'            => $total_books,
			'total_pages'            => $total_pages,
			'current_page'           => $current_page,
			'filter_cat'             => $filter_cat,
			'filter_author'          => $filter_author,
			'filter_search'          => $filter_search,
			'user_borrowed_book_ids' => $user_borrowed_book_ids,
			'checkout_statuses'      => $checkout_statuses,
			'return_statuses'        => $return_statuses,
		);
	}

	public function libmns_render_library_user_catalogue_results( $catalogue_params ) {
		ob_start();
		$params = is_array( $catalogue_params ) ? $catalogue_params : array();
		include LIBMNS_PLUGIN_DIR_PATH . 'admin/views/library_user/templates/owt7_library_books_catalogue_results.php';
		return ob_get_clean();
	}

	public static function libmns_get_default_required_fields() {
		return array(
			'user'     => array( 'owt7_txt_u_id', 'owt7_dd_branch_id', 'owt7_txt_name', 'owt7_dd_user_status' ),
			'branch'   => array( 'owt7_txt_branch_name', 'owt7_dd_branch_status' ),
			'bookcase' => array( 'owt7_txt_bookcase_name', 'owt7_dd_bookcase_status' ),
			'section'  => array( 'owt7_dd_bookcase_id', 'owt7_txt_section_name', 'owt7_dd_section_status' ),
			'book'     => array( 'owt7_txt_book_id', 'owt7_dd_category_id', 'owt7_dd_bookcase_id', 'owt7_dd_section_id', 'owt7_txt_book_name', 'owt7_txt_quantity', 'owt7_dd_book_status' ),
			'category' => array( 'owt7_txt_category_name', 'owt7_dd_category_status' ),
		);
	}

	public static function libmns_is_field_required( $module, $field_name ) {
		$defaults = self::libmns_get_default_required_fields();
		return ! empty( $defaults[ $module ] ) && in_array( $field_name, $defaults[ $module ], true );
	}

	public static function libmns_get_required_fields_for_module( $module ) {
		$defaults = self::libmns_get_default_required_fields();
		return isset( $defaults[ $module ] ) ? $defaults[ $module ] : array();
	}

	public static function libmns_validate_required_fields( $module ) {
		$required = self::libmns_get_required_fields_for_module( $module );
		if ( empty( $required ) ) {
			return array( true );
		}
		$fields = [];
		$labels = isset( $fields[ $module ] ) ? $fields[ $module ] : array();
		foreach ( $required as $field_name ) {
			$raw = isset( $_REQUEST[ $field_name ] ) ? $_REQUEST[ $field_name ] : '';
			$val = is_string( $raw ) ? trim( $raw ) : $raw;
			if ( $val === '' || $val === null ) {
				$label = isset( $labels[ $field_name ] ) ? $labels[ $field_name ] : $field_name;
				return array( false, sprintf( __( 'Required field "%s" is missing or empty.', 'library-management-system' ), $label ) );
			}
		}
		return array( true );
	}

	public function libmns_get_category_name($category_id){
		global $wpdb;
		$category_data = $wpdb->get_row(
			"SELECT name FROM " . LIBMNS_Table_Helper_FREE::get_table_name( 'categories' ) . " WHERE id = {$category_id}"
		);
		return $category_data->name;
	}

	public function libmns_insert_book_copies( $book_id, $stock_quantity, $bookcase_id, $bookcase_section_id ) {
		global $wpdb;

		$book_id = is_string( $book_id ) ? trim( $book_id ) : '';
		$stock_quantity = max( 0, (int) $stock_quantity );
		if ( $book_id === '' || $stock_quantity === 0 ) {
			return 0;
		}

		$tbl_copies = LIBMNS_Table_Helper_FREE::get_table_name( 'data_books_copies' );
		$tbl_bookcase = LIBMNS_Table_Helper_FREE::get_table_name( 'bookcases' );
		$tbl_sections = LIBMNS_Table_Helper_FREE::get_table_name( 'sections' );

		$bookcase_name = '';
		$section_name  = '';
		if ( $bookcase_id > 0 ) {
			$row = $wpdb->get_row( $wpdb->prepare(
				"SELECT name FROM `{$tbl_bookcase}` WHERE id = %d LIMIT 1",
				$bookcase_id
			), OBJECT );
			if ( $row && isset( $row->name ) ) {
				$bookcase_name = $row->name;
			}
		}
		if ( $bookcase_section_id > 0 ) {
			$row = $wpdb->get_row( $wpdb->prepare(
				"SELECT name FROM `{$tbl_sections}` WHERE id = %d LIMIT 1",
				$bookcase_section_id
			), OBJECT );
			if ( $row && isset( $row->name ) ) {
				$section_name = $row->name;
			}
		}
		$shelf_location = $bookcase_name . ' | ' . $section_name;

		$inserted = 0;
		for ( $i = 1; $i <= $stock_quantity; $i++ ) {
			$accession_number = $book_id . '-' . sprintf( '%05d', $i );
			$result = $wpdb->insert( $tbl_copies, array(
				'book_id'          => $book_id,
				'accession_number' => $accession_number,
				'status'           => 'available',
				'shelf_location'   => $shelf_location,
			), array( '%s', '%s', '%s', '%s' ) );
			if ( $result ) {
				$inserted++;
			}
		}
		return $inserted;
	}
}
