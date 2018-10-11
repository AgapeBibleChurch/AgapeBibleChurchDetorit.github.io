<?php
/**
 * The core plugin class.
*
* This is used to define internationalization, admin-specific hooks, and
* public-facing site hooks.
*
* Also maintains the unique identifier of this plugin as well as the current
* version of the plugin.
*
* @since      1.0.0
* @package    hayyabuild
* @subpackage hayyabuild/includes
* @author     zintaThemes <>
*/

if ( ! defined( 'ABSPATH' ) ) { exit; }


class HayyaBuild
{

	/**
	 * The single instance of HayyaBuild.
	 * @var 	object
	 * @access  private
	 * @since 	3.0.0
	 */
	private static $_instance = false;

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since		1.0.0
	 * @access		protected
	 * @var			Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected static $loader;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @access		public
	 * @since		1.0.0
	 * @var			unown
	 */
	public function __construct( $type = null ) {
		if (empty($type)) {
			add_action('plugins_loaded', array($this, 'update'));
			self::load_dependencies();
			if ( HayyaHelper::__current_user('manage_options') && is_admin() ) $this->define_admin_hooks();
			else $this->define_public_hooks();
		}
		return true;
	} // End __construct()

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - HayyaBLoader. Orchestrates the hooks of the plugin.
	 * - HayyaBi18n. Defines internationalization functionality.
	 * - HayyaAdmin. Defines all hooks for the admin area.
	 * - HayyaPublic. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since		1.0.0
	 * @access		private
	 *
	 */
	private static function load_dependencies() {
		require_once HAYYAB_PATH. 'includes/class-hayyabuild-loader.php';
		if ( self::$loader = new HayyaBLoader() ) return true;
		else return false;
	} // End load_dependencies()

	/**
	 * Load HayyaBuild updater class
	 * @since		3.0.0
	 * @return 		unknown
	 */
	public static function update() {
		require_once HAYYAB_PATH . 'includes/class-hayyabuild-update.php';
		return HayyaBuildUpdate::update();
	} // End update()

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Plugin_Name_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since		1.0.0
	 * @access		private
	 */
	private function set_locale() {
		require_once HAYYAB_PATH. 'includes/class-hayyabuild-i18n.php';
		$plugin_i18n = new HayyaBi18n();
		$plugin_i18n->set_domain( HAYYAB_BASENAME );
		return self::$loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	} // End set_locale()

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since		1.0.0
	 * @access		private
	 */
	private function define_admin_hooks() {
		$this->set_locale();
		return HayyaAdmin::define_hooks();
	} // End define_admin_hooks()

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since		1.0.0
	 * @access		private
	 */
	private function define_public_hooks() {
		return HayyaPublic::define_hooks();
		// HayyaBuild::get_loader()->add_filter('setup_theme', 'HayyaPublic', 'define_hooks');
	} // End define_public_hooks()

	/**
	 * Return a public putput
	 * of the plugin.
	 *
	 * @since		1.0.0
	 * @access		public
	 */
	public static function public_output( $type = null ) {
		if ( class_exists('HayyaBShortcode') && class_exists('HayyaPublic') ) new HayyaBShortcode();
		$plugin_public 	= new HayyaPublic();
		if ($type) {
			$plugin_public->hayya_output();
			// add_action( 'wp_head', array($plugin_public, 'hayya_output') );
			return apply_filters( 'hayya_output', $type);
		}
	} // End public_output()

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since		1.0.0
	 */
	public static function run($type = null) {
		if ( !self::$_instance ) self::$_instance = new self();
		if ( ! $type ) self::$loader->run();
		if ( ! is_admin() ) return self::public_output($type);
	} // End run()

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Plugin_Name_Loader    Orchestrates the hooks of the plugin.
	 */
	public static function get_loader() {
		return self::$loader;
	} // End get_loader()

	/**
	 * The code that runs during plugin activation.
	 * This action is documented in includes/class-hayyabuild-activator.php
	 *
	 * @since     1.0.0
	 */
	public static function hayyabuild_activate() {
		require_once HAYYAB_PATH . 'includes/class-hayyabuild-activator.php';
		return HayyaBActivator::activate();
	} // End hayyabuild_activate()

	/**
	 * The code that runs during plugin deactivation.
	 * This action is documented in includes/class-hayyabuild-deactivator.php
	 *
	 * @since     1.0.0
	 */
	public static function hayyabuild_deactivate() {
		require_once HAYYAB_PATH . 'includes/class-hayyabuild-deactivator.php';
		return HayyaBDeactivator::deactivate();
	} // End hayyabuild_deactivate()

} // End HayyaBuild {} class
