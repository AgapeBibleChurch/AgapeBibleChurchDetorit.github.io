<?php
/**
 *
 * HayyaBuild Shortcode class.
 *
 * @since       2.2.0
 * @package     hayyabuild
 * @subpackage  hayyabuild/includes
 * @author      zintaThemes <>
 *
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'HayyaBShortcode' ) ) {

	class HayyaBShortcode {

		/**
		 * The ID of this plugin.
		 *
		 * @since   1.3.0
		 * @access  private
		 * @var     string      $plugin_name    name of this plugin.
		 */
		private $plugin_name    = null;

		/**
		 * The version of this plugin.
		 *
		 * @since   1.3.0
		 * @access  private
		 * @var     string      $version        The current version of this plugin.
		 */
		private $version        = null;

		/**
		 *
		 * @since   1.3.0
		 * @access  private
		 * @var     string      $config        The current version of this plugin.
		 */
		private  static $shortCodeList   = array();

		/**
		 * HTML code.
		 *
		 * @since   1.3.0
		 * @access  private
		 * @var     string      $config        The current version of this plugin.
		 */
		private $bloginfo   = null;

		/**
		 *
		 * Initialize the class and set its properties.
		 * @since   1.3.0
		 * @param   string      $plugin_name    The name of this plugin.
		 * @param   string      $version        The version of this plugin.
		 */
		public function __construct() {
			self::$shortCodeList = self::getSortCode();
			foreach (self::$shortCodeList as $shortcode => $callback ) {
				add_shortcode ( $shortcode, array ( $this, $callback) );
			}
		}

		/**
		 * Initialize the class and set its properties.
		 * @since   3.0.0
		 * @return string[]
		 */
		private static function getSortCode() {
			return array(
				'hayyabuild' => 'hayya_build', // home_url();
				'hayya_pagetitle' => 'pageTitle', // home_url();
				'hayya_sitetitle' => 'siteTitle', // wp_title();
				'hayya_blogtitle' => 'blogTitle', // get_bloginfo( 'name' );
				'hayya_blogdesc' => 'blogDesc', // get_bloginfo( 'description' );
				'hayya_adminemail' => 'adminEmail', // get_option('admin_email');
				'hayya_siteurl' => 'siteURL', // site_url();
				'hayya_homeurl' => 'homeURL', // home_url();
				'hayya_content' => 'pageContent', // pageContent();
				'hayya_username' => 'username', // wp_get_current_user();
				'hayya_date' => 'date', // site_url();
			);
		}

		/**
		 *
		 * Return HayyaBuild content
		 */
		public function hayya_build( $atts ) {
			if ( is_array( $atts ) && isset( $atts['id'] ) && !empty( $atts['id'] ) ) {
				if ( method_exists( 'HayyaPublic', 'hayya_shortcode' ) ) {
					return HayyaPublic::hayya_shortcode( $atts['id'] );
				}
			}
		}

		/**
		 * Return the page title
		 */
		public function pageTitle( $atts, $content = null ) {
			return get_the_title();
		}

		/**
		 * Return the site title
		 */
		public function siteTitle( $atts, $content = null ) {
			return wp_title();
		}

		/**
		 * Return current blog title
		 */
		public function blogTitle( $atts, $content = null ) {
			return get_bloginfo( 'name' );
		}

		/**
		 * Return current blog description
		 */
		public function blogDesc( $atts, $content = null ) {
			return get_bloginfo( 'description' );
		}

		/**
		 * Return current blog description
		 */
		public function adminEmail( $atts, $content = null ) {
			return get_bloginfo( 'admin_email' );
		}

		/**
		 * Return site URL
		 */
		public function siteURL( $atts, $content = null ) {
			return site_url();
		}

		/**
		 * Return Page Content
		 *
		 * @param unknown $atts
		 * @param unknown $content
		 */
		public function pageContent ( $atts, $content = null ) {
		    if ( is_404()) return '';
		    if ( !is_home() && !is_tag() && !is_archive() ) {
				global $wp_query;
				$id = $wp_query->get_queried_object_id();
				$post = get_page( $id );
				return $post->post_content;
			} else {
				return get_the_content();
			}
		}

		/**
		 * Return site URL
		 */
		public function homeURL( $atts, $content = null ) {
			return home_url();
		}

		/**
		 * User name
		 * @return unknown
		 */
		public function username() {
			$current_user = wp_get_current_user();
			return $current_user->user_firstname.' '.$current_user->user_lastname;
		}

		/**
		 * Return site URL
		 */
		public function date( $atts, $content = null ) {
			$date = date( "Y" );
			if ( is_array( $atts ) ) {
				foreach ( $atts as $key => $value ) {
					if ( $key == 'format' && !empty($value) ) $date = date( $value );
				}
			}
			return $date;
		}
	} // end of class HayyaParser

}
