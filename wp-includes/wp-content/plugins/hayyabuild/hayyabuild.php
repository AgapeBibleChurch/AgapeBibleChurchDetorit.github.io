<?php
/**
 * Plugin Name: 	HayyaBuild
 * Plugin URI: 		https://hayyabuild.zintathemes.com
 * Author: 			ZintaThemes
 * Author URI: 		www.zintathemes.com
 * Version: 		1.4.0
 * Description: 	HayyaBuild is a powerful and straightforward backend drag-and-drop WordPress plugin that offers responsive headers, pages content and footers builder.
 *
 * Text Domain: 	hayyabuild
 * Domain Path: 	/languages/
 *
 *
 * @link
 * @since			1.0.0
 * @package			HayyaBuild
 * @category 		*
 * @author 			ZintaThemes
 */

// If this file is called directly, abort.
if ( ! defined ( 'ABSPATH' ) ) {
	die( 'This file cannot be accessed directly!' );
}

// Define HayyaBuild constants
defined('HAYYAB_VERSION' ) 	or define( 'HAYYAB_VERSION'	, '1.4.0' );
defined('HAYYAB_BASENAME' ) or define( 'HAYYAB_BASENAME', 'hayyabuild' );
defined('HAYYAB_NAME' ) 	or define( 'HAYYAB_NAME'	, 'HayyaBuild' );
defined('HAYYAB_PATH' ) 	or define( 'HAYYAB_PATH'	, plugin_dir_path(__FILE__) );
defined('HAYYAB_URL' ) 		or define( 'HAYYAB_URL'		, plugin_dir_url (__FILE__) );

final class HayyaBuildStart {

	/**
	 * The version number.
	 * @var     	string
	 * @access  	public
	 * @since   	3.0.0
	 */
	public $version;

	/**
	 * The plugin directory URL.
	 * @var     	string
	 * @access  	public
	 * @since   	3.0.0
	 */
	public $plugin_url;

	/**
	 * The plugin directory path.
	 * @var     	string
	 * @access  	public
	 * @since   	3.0.0
	 */
	public $plugin_path;

	/**
	 * The single instance of HayyaBuild.
	 * @var 		object
	 * @access  	private
	 * @since 		3.0.0
	 */
	private static $_instance = false;

	/**
	 * Constructor function.
	 * @access  	public
	 * @since   	3.0.0
	 */
	public function __construct() {
		require_once HAYYAB_PATH . 'includes/class-hayyabuild.php';
		register_activation_hook( __FILE__, array( 'HayyaBuild', 'hayyabuild_activate' ) );
		register_deactivation_hook( __FILE__, array( 'HayyaBuild', 'hayyabuild_deactivate' ) );
	} // End __construct()

	/**
	 * Begins execution of the plugin.
	 *
	 * @access  	public
	 * @since       3.0.0
	 * @param       $type       string
	 */
	public static function hayya_start( $type = null ) {
		if ( !self::$_instance ) self::$_instance = new self();
		HayyaBuild::run($type);
	} // End hayya_start()
} // End HayyaBuildStart {} Class

/**
 * Begins execution of the plugin.
 *
 * @since       1.0.0
 * @param       $type       string
 */
function hayya_run($type = null) {
	HayyaBuildStart::hayya_start($type);
} // End hayya_run()

// Run HayyaBuild plugin
hayya_run();
