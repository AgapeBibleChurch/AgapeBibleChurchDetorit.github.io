<?php
/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    HayyaBuild
 * @subpackage hayyabuild/includes
 * @author     zintaThemes @
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}


class HayyaBActivator {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since       1.0.0
     * @access      protected
     * @var         Plugin_Name_Loader    $loader    Maintains and registers all hooks for the plugin.
     */
    public $loader;

    /**
     * Define the core functionality of the activation.
     *
     * @access      public
     * @since       1.0.0
     * @var         unown
     */
	public function __construct() {}

	/**
	 * HayyaBuild activation function
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		global $wpdb;
		require_once ABSPATH . 'wp-admin' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'upgrade.php';
        require_once HAYYAB_PATH . 'includes' . DIRECTORY_SEPARATOR . 'class-hayyabuild-modules.php';

        $table            = $wpdb->prefix.HAYYAB_BASENAME;
        $tableMap         = $wpdb->prefix.HAYYAB_BASENAME. '_map';
		$charset_collate  = $wpdb->get_charset_collate();

        if( $wpdb->get_var("SHOW TABLES LIKE '$table'") != $table ) {
    		$sql = 'CREATE TABLE `' . $table . '` ( `id` int(9) NOT NULL AUTO_INCREMENT, `name` varchar(256) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL, `author` int(11) NOT NULL, `settings` text COLLATE utf8_bin, `pages` varchar(256) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL, `content` text COLLATE utf8_bin, `clean_content` text COLLATE utf8_bin, `type` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,`status` varchar(11) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT "draft", `added_date` datetime NOT NULL, `modified_date` datetime NOT NULL, PRIMARY KEY (`id`) ) '.$charset_collate.';';
    		dbDelta ( $sql );
        }

        if($wpdb->get_var("SHOW TABLES LIKE '$tableMap'") != $tableMap) {
    		$sql = 'CREATE TABLE `' . $tableMap . '` ( `id` int(9) NOT NULL AUTO_INCREMENT, `object_id` varchar(9) COLLATE utf8_bin NOT NULL, `hb_id` int(20) NOT NULL, `hb_type` varchar(10) COLLATE utf8_bin NOT NULL, PRIMARY KEY (`id`) ) '.$charset_collate.';';
    		dbDelta ( $sql );
		}

		$hayya_elements = new HayyaModules( 'showall' );

        $elements = array();

		if ( $elements_list  =  $hayya_elements->elements_list() ) {
            if ( is_array($elements_list) ) {
                foreach ( $elements_list as $key => $value ) {
                    if ( is_array($value) && !empty($value) ) {
                        foreach ( $value as $k => $v ) {
                            if (isset($v['base']) && !empty($v['base']) ) $elements[$v['base']] = 'on';
                        }
                    }
                }
            }
        }

        $libraries = array(
                'bootstrap' => 'on',
                'fontawesome' => 'on',
                'scrollmagic' => 'on',
                'nicescroll'    => 'on'
        );

        $settings 	= array(
        		'libraries' => $libraries,
        		'elements' => $elements,
        		'csseditor' => ''
        );

        add_option( 'hayyabuild_settings', $settings );
        add_option( 'hayyabuild_version', HAYYAB_VERSION );
	}
}
