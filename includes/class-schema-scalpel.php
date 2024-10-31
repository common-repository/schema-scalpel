<?php

namespace SchemaScalpel;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

/**
 * Plugin init class.
 *
 * @link       https://schemascalpel.com
 *
 * @package    Schema_Scalpel
 * @subpackage Schema_Scalpel/includes
 * @author     Kevin Gillispie
 */
class Schema_Scalpel {


	/**
	 * Variable initialized as Schema_Scalpel_Loader class.
	 *
	 * @access   protected
	 * @var      Schema_Scalpel_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * Variable initialized with plugin name.
	 *
	 * @access   protected
	 * @var      string    $schema_scalpel    The string used to uniquely identify this plugin.
	 */
	protected $schema_scalpel;

	/**
	 * Variable initialized with plugin version.
	 *
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;


	public function __construct() {
		$this->version        = SCHEMA_SCALPEL_VERSION;
		$this->schema_scalpel = 'schema-scalpel';

		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();
		$this->register();
	}

	/**
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once SCHEMA_SCALPEL_DIRECTORY . '/includes/class-schema-scalpel-loader.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once SCHEMA_SCALPEL_DIRECTORY . '/admin/class-schema-scalpel-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once SCHEMA_SCALPEL_DIRECTORY . '/public/class-schema-scalpel-public.php';

		$this->loader = new Schema_Scalpel_Loader();
	}

	/**
	 * Prepare any styles and scripts.
	 *
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Schema_Scalpel_Admin( $this->get_schema_scalpel(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
	}

	/**
	 * Prepare any public scripts.
	 *
	 * @access   private
	 */
	private function define_public_hooks() {
		$plugin_public = new Schema_Scalpel_Public( $this->get_schema_scalpel(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_inline_scripts' );
	}

	/**
	 * Run the plugin loader.
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * Initialize class variable with plugin name.
	 *
	 * @return    string    The name of the plugin.
	 */
	public function get_schema_scalpel() {
		return $this->schema_scalpel;
	}

	/**
	 * Initializes class variable with loader class.
	 *
	 * @return    Schema_Scalpel_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Initialize class variable with plugin version number.
	 *
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Register any admin page styles/scripts.
	 */
	private function register() {
		add_action( 'admin_menu', array( $this, 'add_admin_pages' ) );
		add_action(
			'admin_head',
			function () {
				echo <<<STYLE
            <style>img[src*="menu_icon.svg"]{padding: 0 !important;}</style>
            STYLE;
			}
		);
	}

	/**
	 * Load plugin's admin page.
	 */
	public function admin_index_page() {
		require_once SCHEMA_SCALPEL_DIRECTORY . '/admin/partials/schema-scalpel-admin-main.php';
	}

	/**
	 * Load plugin's settings page.
	 */
	public function user_settings_page() {
		require_once SCHEMA_SCALPEL_DIRECTORY . '/admin/partials/schema-scalpel-user-settings.php';
	}

	/**
	 * Load plugin's export page.
	 */
	public function user_export_page() {
		require_once SCHEMA_SCALPEL_DIRECTORY . '/admin/partials/schema-scalpel-user-export.php';
	}

	/**
	 * Load plugin's admin menu.
	 */
	public function add_admin_pages() {
		add_action(
			'admin_head',
			function () {
				echo '<style class="scsc-admin">.toplevel_page_scsc img {margin-top:6px}</style>';
			}
		);

		add_menu_page(
			'Schema Scalpel Plugin',
			'Schema Scalpel',
			'manage_options',
			'scsc',
			array( $this, 'admin_index_page' ),
			plugin_dir_url( SCHEMA_SCALPEL_PLUGIN ) . 'admin/images/menu_icon.svg',
			100
		);

		add_submenu_page(
			__( 'scsc' ),
			__( 'Add New / Edit' ),
			__( 'Add New / Edit' ),
			'manage_options',
			SCHEMA_SCALPEL_TEXT_DOMAIN,
			array( $this, 'admin_index_page' ),
		);

		add_submenu_page(
			__( 'scsc' ),
			'Schema Scalpel | Settings',
			'Settings',
			'manage_options',
			SCHEMA_SCALPEL_SLUG . 'settings',
			array( $this, 'user_settings_page' ),
			1
		);

		add_submenu_page(
			'scsc',
			'Schema Scalpel | Export',
			'Export',
			'manage_options',
			SCHEMA_SCALPEL_SLUG . 'export',
			array( $this, 'user_export_page' ),
			2
		);
	}
}
