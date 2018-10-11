<?php
/**
 * Update database class
 *
 * @since      1.0.0
 * @package    hayyabuild
 * @subpackage hayyabuild/includes
 * @author     zintaThemes <>
 */
if (! defined ( 'ABSPATH' )) {exit ();}

class HayyaBuildUpdate {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected static $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @access public
	 * @since 1.0.0
	 * @var unown
	 */
	public function __construct($type) {
		return true;
	}

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
	 * @since 1.0.0
	 * @access private
	 *
	 */
	public static function update() {
		$DBversion 	= get_option('hayyabuild_version');

		if ( ! $DBversion || version_compare( $DBversion, HAYYAB_VERSION, '<' ) ) {

			global $wpdb;

			if ( version_compare( $DBversion, '3.0', '<=' ) ) {
				$table = $wpdb->prefix.HAYYAB_BASENAME.'_map';
				$result = $wpdb->query('ALTER TABLE `'.$table.'` CHANGE `object_id` `object_id` VARCHAR(9) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL;');
			}

			require_once HAYYAB_PATH . 'includes/class-hayyabuild-modules.php';
			$hayya_elements = new HayyaModules( 'showall' );

			if ($elements_list = $hayya_elements->elements_list()) {
				if (is_array($elements_list) && !empty($elements_list)) {
					foreach ( $elements_list as $key => $value ) {
						if (is_array($value) && !empty($value)) {
							foreach ($value as $k => $v) {
								$elements[$v['base']] = 'on';
							}
						}
					}
				}
			}

			$setting = get_option('hayyabuild_settings');

			if ( isset($setting['libraries']) ) {
				$libraries = $setting['libraries'];
			} else {
				$libraries = array( 'bootstrap' => 'on',
					'fontawesome' => 'on',
					'scrollmagic' => 'on',
					'nicescroll' => 'on'
				);
			}
			$csseditor = (isset($setting['csseditor'])) ? $setting['csseditor'] : '';
			$settings 	= array(
					'libraries' => $libraries,
					'elements' => $elements,
					'csseditor' => $csseditor
			);

			update_option( 'hayyabuild_settings', $settings );

			if ( version_compare( $DBversion, '2.1', '<' ) ) {
				require_once HAYYAB_PATH . 'includes/class-hayyabuild-parser.php';
				
				$table = $wpdb->prefix.HAYYAB_BASENAME;
				$result = $wpdb->query("SHOW TABLES LIKE '$table'");

				if ($result) {
					$result = $wpdb->query("SHOW COLUMNS FROM `$table` LIKE 'clean_content'");
					if( !$result ) {

						$sql = "ALTER TABLE `$table` ADD `clean_content` TEXT CHARACTER SET utf8 COLLATE utf8_bin NULL DEFAULT NULL AFTER `content`;";
						$wpdb->query($sql);

						$content = $wpdb->get_results( 'SELECT `id`,`settings`,`pages`,`content` FROM `' . $table . '`' , OBJECT );
						foreach( $content as $value ) {

							$html 		= HayyaParser::cleanAdminHTML( $value->content );
							$main_html 	= addslashes( $html );
							$clean_html = addslashes( HayyaParser::cleanPublicHTML( $html ) );
							$settings 	= (!is_serialized( $value->settings )) ? maybe_serialize( $value->settings ) : $value->settings;
							$pages 		= (!is_serialized( $value->pages )) ? maybe_serialize( $value->pages ) : $value->pages;

							$data = array(
									'settings' 		=> $settings,
									'pages' 		=> $pages,
									'content' 		=> $main_html,
									'clean_content' => $clean_html
							);

							if ( $wpdb->update( $table, $data, array( 'id' => $value->id ) ) ) continue;
							$html = $data = $clean_html = $main_html = '';
						}
					}
				}
			}
			update_option( 'hayyabuild_version', HAYYAB_VERSION );
			return true;
		}
		return false;
	}

	/**
	 * Check for any updates
	 *
	 * @since 2.0.0
	 */
	private static function checkForUpdate() {
		return true;
	}
}
