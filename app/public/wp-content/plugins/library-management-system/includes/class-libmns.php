<?php
/**
 * @link       https://onlinewebtutorblog.com
 * @since      3.5
 * @package    Library_Management_System
 * @subpackage Library_Management_System/includes
 * @copyright  Copyright (c) 2026, Online Web Tutor
 * @license    GPL-2.0+ https://www.gnu.org/licenses/gpl-2.0.html
 * @author     Online Web Tutor
 */
class LIBMNS_FREE {

	protected $loader;
	protected $plugin_name;
	protected $version;

	public function __construct() {
		if ( defined( 'LIBMNS_VERSION' ) ) {
			$this->version = LIBMNS_VERSION;
		} else {
			$this->version = '3.0.0';
		}
		$this->plugin_name = defined( 'LIBMNS_PLUGIN_SLUG' ) ? LIBMNS_PLUGIN_SLUG : 'library-management-system';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	private function load_dependencies() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-libmns-loader.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-libmns-i18n.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-libmns-roles.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-libmns-sanitize.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-libmns-admin.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-libmns-ajax-helper.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-libmns-public.php';

		$this->loader = new LIBMNS_Loader_FREE();

	}

	private function set_locale() {

		$plugin_i18n = new LIBMNS_i18n_FREE();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	private function define_admin_hooks() {

		$plugin_admin = new LIBMNS_Admin_FREE( $this->get_plugin_name(), $this->get_version() );
		$ajax_helper  = new LIBMNS_Ajax_Helper_FREE( $plugin_admin );

		$plugin_basename = defined( 'LIBMNS_PLUGIN_BASENAME' ) ? LIBMNS_PLUGIN_BASENAME : plugin_basename( LIBMNS_PLUGIN_FILE );
		$this->loader->add_filter( 'plugin_action_links_' . $plugin_basename, $plugin_admin, 'libmns_plugin_action_links' );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'libmns_enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'libmns_enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'libmns_register_menus' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'libmns_filter_librarian_menu', 999 );
		$this->loader->add_action( 'admin_bar_menu', $plugin_admin, 'libmns_admin_bar_remove_new_for_lms_staff', 999 );
		$this->loader->add_action( 'wp_ajax_owt_lib_handler', $ajax_helper, 'libmns_ajax_handler' );
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'libmns_zip_extension_missing_notice' );

	}

	private function define_public_hooks() {

		$plugin_public = new LIBMNS_Public_FREE( $this->get_plugin_name(), $this->get_version() );

		add_shortcode( 'owt7_library_books', array( $plugin_public, 'owt7_library_all_books_shortcode' ) );
		add_shortcode( 'owt7_user_books_history', array( $plugin_public, 'owt7_library_user_books_history_shortcode' ) );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'wp_ajax_owt7_front_handler', $plugin_public, 'owt7_library_front_request_handler');
		$this->loader->add_action( 'wp_ajax_nopriv_owt7_front_handler', $plugin_public, 'owt7_library_front_request_handler');
		$this->loader->add_filter( 'login_redirect', $plugin_public, "owt7_library_redirect_after_login", 10, 3);
	}

	public function run() {
		add_action( 'plugins_loaded', array( 'LIBMNS_Activator_FREE', 'maybe_upgrade_plugin' ), 5 );
		add_action( 'plugins_loaded', array( 'LIBMNS_Activator_FREE', 'ensure_library_user_role_and_caps' ), 20 );
		$this->loader->run();
	}

	public function get_plugin_name() {
		return $this->plugin_name;
	}

	public function get_loader() {
		return $this->loader;
	}

	public function get_version() {
		return $this->version;
	}
}
