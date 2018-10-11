<?php
/**
 * Helper class.
 *
 *
 * @since	  1.0.0
 * @package	hayyabuild
 * @subpackage hayyabuild/includes
 * @author	 zintaThemes <>
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class HayyaHelper {

	/**
	 * redirect static varibale
	 *
	 * @since		1.0.0
	 * @access		protected
	 * @var			string	$plugin_name	The string used to uniquely identify this plugin.
	 */
	public static $redirect = array();

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since		1.0.0
	 * @access		protected
	 * @var			string	$plugin_name	The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since		1.0.0
	 * @access		protected
	 * @var			string	$version	The current version of the plugin.
	 */
	protected $version;

	/**
	 *
	 * @since		3.0.0
	 * @access		public
	 * @var 		array		$options
	 */
	public static $options = array();

	/**
	 * construct function
	 *
	 * @access		public
	 * @since		1.0.0
	 */
	public function __construct() {
		return true;
	}

	/**
	 * Admin notices function.
	 *
	 * @since 	1.0.0
	 * @param 	String 		$message 	notice message
	 * @param 	String 		$type 		notice type
	 */
	public static function __notices($message, $type) {
		add_action('admin_notices', function() use ($message, $type) {
			echo '<div class="notice notice-'.$type.' is-dismissible"><p>' . __( $message, HAYYAB_BASENAME ) . '</p></div>';
		});
	} // End __notice()

	/**
	 * add or remove slashes
	 *
	 * @param unknown $content
	 * @param unknown $slashes
	 * @return unknown|boolean
	 */
	public static function __slashes($content = null, $slashes = null ) {
		if ( null !== $content &&  null !== $slashes ) {
			if ( $slashes === 'add' ) return addslashes($content);
			else if ( $slashes === 'strip' ) return stripslashes($content);
		} return false;
	} // End __slashes()

	/**
	 * remove slashes if magic_quotes_gpc() is activated
	 *
	 * @param unknown $content
	 * @return unknown|boolean
	 */
	public static function __strip_magic_quotes($content = null) {
		return stripslashes_deep($content);
		// return get_magic_quotes_gpc() ? self::__slashes($content, 'strip') : $content;
	} // End __slashes()

	/**
	 *  Redirect to edit page after save an new element.
	 *
	 * @access 	public
	 * @since 	3.0.0
	 */
	public static function __redirect($redirect = array()) {
		$redirect = self::$redirect;
		if ( is_array($redirect) && !empty($redirect) ) {
			if (isset($redirect['id']) && !empty($redirect['id'])) wp_redirect(admin_url('/admin.php?page=hayyabuild&id='.$redirect['id'].'&action=edit&update=ok'));
			if (isset($redirect['list']) && $redirect['list'] === 'notfound') wp_redirect(admin_url('/admin.php?page=hayyabuild&notfound=1'));
		}
	} // End __redirect()

	/**
	 *  check is it HayyaBuild pages.
	 *
	 * @access 	public
	 * @since 	3.0.0
	 */
	public static function __is_hayy_pages() {
		return 'hayyabuild' === self::_get( 'page' ) || 'hayyabuild_addh' === self::_get( 'page' ) || 'hayyabuild_addc' === self::_get( 'page' ) || 'hayyabuild_addf' === self::_get( 'page' ) || 'hayyabuild_settings' === self::_get( 'page' ) || 'hayyabuild_help' === self::_get( 'page' );
	} // End __is_hayy_pages()

	/**
	 *  check admin main pages.
	 *
	 * @access 	public
	 * @since 	3.0.0
	 */
	public static function __is_main_pages() {
		return 'hayyabuild' === self::_get( 'page' ) || 'hayyabuild_addh' === self::_get( 'page' ) || 'hayyabuild_addc' === self::_get( 'page' ) || 'hayyabuild_addf' === self::_get( 'page' );
	} // End __is_main_pages()

	/**
	 *  check admin build page.
	 *
	 * @access 	public
	 * @since 	3.0.0
	 */
	public static function __is_build_pages() {
		return  'hayyabuild_addh' === self::_get( 'page' ) || 'hayyabuild_addc' === self::_get( 'page' ) || 'hayyabuild_addf' === self::_get( 'page' ) || ( 'hayyabuild' === self::_get( 'page' ) && 'edit' === self::_get('action') );
	} // End __is_build_pages()

	/**
	 *  check admin add new pages.
	 *
	 * @access 	public
	 * @since 	3.0.0
	 */
	public static function __is_new_pages() {
		return  'hayyabuild_addh' === self::_get( 'page' ) || 'hayyabuild_addc' === self::_get( 'page' ) || 'hayyabuild_addf' === self::_get( 'page' );
	} // End __is_new_pages()

	/**
	 *  check admin add new pages.
	 *
	 * @access 	public
	 * @since 	3.0.0
	 */
	public static function __is_settings_page() {
		return 'hayyabuild_settings' === self::_get( 'page' );
	} // End __is_settings_pages()

	/**
	 *  check admin add new pages.
	 *
	 * @access 	public
	 * @since 	3.0.0
	 */
	public static function __is_help_page() {
		return  'hayyabuild_help' === self::_get( 'page' );
	} // End __is_help_pages()

	/**
	 *
	 * @access		public
	 * @since		1.0.0
	 * @param 		string		$param
	 */
	public static function _get($param) {
		return ( isset( $_GET[$param] ) ) ? $_GET[$param] : false;
	} // End _get()

	/**
	 *
	 * @param 		string 		$param
	 */
	public static function _post($param) {
		return ( isset( $_POST[$param] ) ) ? $_POST[$param] : false;
	} // End _post()


	/**
	 * Get wpdb.
	 *
	 * @since 	1.0.0
	 */
	public static function __hpDB() {
		global $wpdb; return $wpdb;
	} // End __zpdb()

	/**
	 *
	 * @since 	1.0.0
	 */
	public static function __empty( $var = null ) {
		return ( null === $var ) ? '' : $var; // TODO: remove this functions
	} // End __empty()

	/**
	 * Get wpdb.
	 *
	 * @since   1.0.0
	 */
	public static function __debug( $message ) {
		return ( ! empty( $message ) ) ? '<div class="__debug">'.$message.'</div>' : '';
	} // End __debug()

	/**
	 * Get HayyaBuild options.
	 *
	 * @since   3.0.0
	 */
	public static function __options( $atts = null ) {
		if ( empty( self::$options ) ) self::$options = get_option('hayyabuild_settings');
		return self::$options;
	} // End __options()

	/**
	 * Get files content.
	 *
	 * @since   3.2.0
	 */
	public static function __get_content( $file = null ) {
		if ( $file && file_exists( $file ) ) {
			global $wp_filesystem;
			if ( empty( $wp_filesystem ) ) {
				require_once( ABSPATH . '/wp-admin/includes/file.php' );
				WP_Filesystem();
			}
			if ( is_object( $wp_filesystem ) && $content = $wp_filesystem->get_contents( $file ) )
				return $content;
		}
		return false;
	} // End __get_content()

	/**
	 * Check current user
	 *
	 * @since   3.2.0
	 */
	public static function __current_user( $capability = null ) {
		if ( ! function_exists('wp_get_current_user') ) {
			require_once( ABSPATH . 'wp-includes/pluggable.php' );
		}
		if ( function_exists('current_user_can') && function_exists('wp_get_current_user') ) {
			if ( ! $capability ) $capability = 'manage_options';
			if ( is_admin() && current_user_can($capability) ) {
				return true;
			}
		}
		return false;
	} // End __get_content()

	/**
	 *
	 * @since   3.2.0
	 */
	public static function __ajax_nonce($process = null) {
		if ( null === $process ) return check_ajax_referer( $process );
	}

	/**
	 *
	 * @since   3.1.0
	 * @return number
	 */
	public static function __mtime() {
		// $time_start = HayyaHelper::__mtime();
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}

	/**
	 *    @method    check
	 *    @since     1.0.0
	 *    @access    public
	 */
	public static function check() {
		return version_compare( HAYYAB_VERSION , '3', '<' );
	}

}
