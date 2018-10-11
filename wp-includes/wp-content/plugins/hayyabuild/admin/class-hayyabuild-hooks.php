<?php
/**
 *
 * HayyaBuild Admin Scripts functionality of the plugin.
 *
 * @since      	1.0.0
 * @package    	hayyabuild
 * @subpackage 	hayyabuild/admin
 * @author     	zintaThemes <>
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }


class HayyaAdminHooks extends HayyaAdmin {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since		3.0.0
	 * @param      	string    $plugin_name       The name of the plugin.
	 * @param      	string    $version    The version of this plugin.
	 */
	public function __construct() {
		HayyaBuild::get_loader()->add_action( 'admin_menu', $this, 'admin_menus' );
		HayyaBuild::get_loader()->add_action( 'admin_init', $this, 'scripts_start');
	} // End __construct()

	/**
	 *
	 * @since		3.0.0
	 */
	public function scripts_start() {
		if (HayyaHelper::__is_hayy_pages()) {
			add_action( 'admin_enqueue_scripts', array($this, 'enqueue_styles') );
			add_action( 'admin_enqueue_scripts', array($this, 'enqueue_scripts') );
		}
		if (HayyaHelper::__is_build_pages()) add_action( 'admin_head', array($this, 'admin_wp_head') );
	} // End scripts_start()

    /**
     *
     * Create admin header script.
     *
     * @access 	public
     * @since 	1.0.0
     */
    public static function admin_wp_head() {
    	if ( method_exists(parent::$modules, 'elements') ) {
			$head = parent::$modules->elements( parent::$type );
    		echo $head;
    	}
    } // End admin_wp_head()

    /**
     * Register the stylesheets for the admin area.
     *
     * This function is provided for demonstration purposes only.
     *
     * An instance of this class should be passed to the run() function
     * defined in Plugin_Name_Loader as all of the hooks are defined
     * in that particular class.
     *
     * The Plugin_Name_Loader will then create the relationship
     * between the defined hooks and the functions defined in this
     * class.
     *
     * @since 	1.0.0
     */
    public static function enqueue_styles() {
    	self::register_style();
    	wp_enqueue_style(HAYYAB_BASENAME);
		if ( is_rtl() ) {
			wp_enqueue_style(HAYYAB_BASENAME . '-rtl');
		}
    } // End enqueue_styles()

    /**
     * Register the JavaScript for the admin area.
     *
     * This function is provided for demonstration purposes only.
     *
     * An instance of this class should be passed to the run() function
     * defined in Plugin_Name_Loader as all of the hooks are defined
     * in that particular class.
     *
     * The Plugin_Name_Loader will then create the relationship
     * between the defined hooks and the functions defined in this
     * class.
     *
     * @access 	public
     * @since 	1.0.0
     */
    public static function enqueue_scripts() {
    	self::register_script();
    	wp_enqueue_script(HAYYAB_BASENAME);
    } // End enqueue_scripts()

    /**
     *
     *
     * @access 	public
     * @since 	3.0.0
     */
    public static function register_style() {
    	$dep = array();
    	wp_register_style( 'fontawesome', HAYYAB_URL . 'admin/assets/libs/font-awesome/css/font-awesome.min.css', array(), HAYYAB_VERSION, 'all' );
		wp_register_style( 'googlefont-Lato', '//fonts.googleapis.com/css?family=Lato:100,200,300,400,500,600,700,800,900', array(), HAYYAB_VERSION, 'all' );
    	if ( HayyaHelper::__is_new_pages() || HayyaHelper::__is_settings_page() || ( parent::$page == 'hayyabuild' && ( HayyaHelper::_get('action') == 'edit' || HayyaHelper::_get('tpl') ) ) ) { // #TODO: try to remove this if statment
			wp_register_style( 'hayya_bootstrap', HAYYAB_URL.'admin/assets/libs/bootstrap/css/bootstrap.min.css', array(), HAYYAB_VERSION, 'all' );
    		wp_register_style( 'minicolors', HAYYAB_URL.'admin/assets/libs/minicolors/jquery.minicolors.css', array('hayya_bootstrap'), HAYYAB_VERSION, 'all' );
    		wp_register_style( 'chosen', HAYYAB_URL.'admin/assets/libs/chosen/chosen.css', array('minicolors'), HAYYAB_VERSION, 'all' );
    		$dep = array('chosen', 'googlefont-Lato', 'fontawesome', 'hayya_bootstrap', 'minicolors');
    		if ( method_exists(parent::$modules, 'elements_list') ) {
    			$elements_list = parent::$modules->elements_list();
    			if ( is_array( $elements_list ) ) {
    				foreach ($elements_list as $path => $elements ) {
    					foreach ($elements as $list ) {
    						if ( is_array($list['admin_css']) && !empty($list['admin_css']) ) {
    							foreach ( $list['admin_css'] as $key => $files ) {
    								$cssFile = $path.$list['base'].'/'.$files;
    								if ( file_exists( $cssFile ) ) {
    									$cssURL = site_url().'/'. str_replace(ABSPATH, '', $cssFile);
    									wp_register_style( 'hayya_'.$key, $cssURL, $dep, HAYYAB_VERSION, 'all' );
    									$deps[] = 'hayya_'.$key;
    									$cssFile = $cssURL = '';
    								}
    							}
    						}
    					}
    				}
    			}
				if ( empty($deps) ) $deps = $dep;
    		} else $deps = array('fontawesome', 'googlefont-Lato', 'chosen', 'hayya_bootstrap');
    	} else $deps = array('fontawesome', 'googlefont-Lato');
		// wp_register_style( 'hayya_public', HAYYAB_URL . 'public/assets/css/hayyabuild.min.css', $deps, HAYYAB_VERSION, 'all' );
		// $deps[] = 'hayya_public';
    	wp_register_style( HAYYAB_BASENAME, HAYYAB_URL . 'admin/assets/css/hayyabuild.min.css', $deps, HAYYAB_VERSION, 'all' );

		if ( is_rtl() ) {
			wp_register_style( HAYYAB_BASENAME . '-rtl', HAYYAB_URL . 'admin/assets/css/rtl.min.css', array( HAYYAB_BASENAME ), HAYYAB_VERSION, 'all' );
		}
    } // End register_style()

    /**
     *
     *
     * @access 	public
     * @since 	3.0.0
     */
    public static function register_script(){
    	$scripts = array();
    	if ( HayyaHelper::__is_settings_page() || HayyaHelper::__is_new_pages() || ( parent::$page == 'hayyabuild' && ( HayyaHelper::_get('action') == 'edit' || HayyaHelper::_get('tpl') ) ) ) {
    		wp_enqueue_media();
    		wp_register_script( 'bootstrap', HAYYAB_URL.'admin/assets/libs/bootstrap/js/bootstrap.min.js', array('jquery', 'jquery-ui-selectable', 'accordion', 'underscore', 'jquery-ui-resizable'), HAYYAB_VERSION, true );
    		wp_register_script( 'minicolors', HAYYAB_URL.'admin/assets/libs/minicolors/jquery.minicolors.min.js', array('bootstrap'), HAYYAB_VERSION, true );
    		wp_register_script( 'chosen', HAYYAB_URL.'admin/assets/libs/chosen/chosen.jquery.min.js', array('minicolors'), HAYYAB_VERSION, true );
    		wp_register_script( 'hayya_param_types', HAYYAB_URL.'admin/assets/js/param_types.min.js', array('chosen'), HAYYAB_VERSION, true );
			wp_register_script( 'sortablejs', HAYYAB_URL.'admin/assets/libs/sortablejs/Sortable.min.js', array('hayya_param_types'), HAYYAB_VERSION, true );
			// wp_register_script( 'hayyasort', HAYYAB_URL.'admin/assets/libs/sortablejs/HayyaSort.min.js', array('sortable'), HAYYAB_VERSION, true );
    		wp_register_script( 'hayya_composer', HAYYAB_URL.'admin/assets/js/composer.min.js', array('sortablejs'), HAYYAB_VERSION, true );
    		wp_register_script( 'ace', HAYYAB_URL.'admin/assets/libs/ace/ace.js', array(), HAYYAB_VERSION, true );
    		$scripts = array('ace', 'hayya_composer');
    	}
    	wp_register_script( 'materialize', HAYYAB_URL.'admin/assets/js/materialize.min.js', $scripts, HAYYAB_VERSION, true );
    	wp_register_script( HAYYAB_BASENAME, HAYYAB_URL.'admin/assets/js/admin_script.min.js', array('materialize'), HAYYAB_VERSION, true );
    }

    /**
     *
     * Create admin menus and pages.
     *
     * @access 	public
     * @since 	1.0.0
     */
    public static function admin_menus() {
    	$parent = new HayyaAdmin();
    	add_menu_page( HAYYAB_NAME, HAYYAB_NAME, 'administrator', HAYYAB_BASENAME, array($parent, 'hayya_admin'), HAYYAB_URL.'admin/assets/images/menu_icon.png' );
    	add_submenu_page( HAYYAB_BASENAME, HAYYAB_NAME, 'List', 'administrator', HAYYAB_BASENAME, array($parent, 'hayya_admin') );
    	add_submenu_page( HAYYAB_BASENAME, HAYYAB_NAME . ' Add Header', 'Add Header', 'administrator', HAYYAB_BASENAME.'_addh', array($parent, 'add_header') );
    	add_submenu_page( HAYYAB_BASENAME, HAYYAB_NAME . ' Add Content', 'Add Content', 'administrator', HAYYAB_BASENAME.'_addc', array($parent, 'add_content') );
    	add_submenu_page( HAYYAB_BASENAME, HAYYAB_NAME . ' Add Footer', 'Add Footer', 'administrator', HAYYAB_BASENAME.'_addf', array($parent, 'add_footer') );
    	add_submenu_page( HAYYAB_BASENAME, HAYYAB_NAME . ' Settings', 'Settings', 'administrator', HAYYAB_BASENAME . '_settings', array($parent, 'hayya_settings') );
    	add_submenu_page( HAYYAB_BASENAME, HAYYAB_NAME . ' Help', 'Help', 'administrator', HAYYAB_BASENAME . '_help', array($parent, 'hayya_help') );
    } // End admin_menus()


} // End Class
