<?php
/**
 * HTML code output class.
 *
 *
 * @since      1.0.0
 * @package    hayyabuild
 * @subpackage hayyabuild/includes/modules/hb_html
 * @author     ZintaThemes <>
 */
class HayyaModule_hb_html
{
	/**
	 * Type of element "header, content, footer, all".
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $type		The current version of this plugin.
	 */
	public $type 	= 'all';

	/**
	 * The name of element.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $name		The current version of this plugin.
	 */
	public $name 		= '';

	/**
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $icon	    The current version of this plugin.
	 */
	public $icon 		= 'fa fa-file-code-o';

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $description The current version of this plugin.
	 */
	public $description = '';

	/**
	 * Show settings dialog after click in create.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $show_settings_on_create    The current version of this plugin.
	 */
	public $show_settings_on_create = true;

	/**
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $is_container    The current version of this plugin.
	 */
	public $is_container = false;
	
	/**
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $params		    The current version of this plugin.
	 */
	public $params 	= array();

	/**
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $css_files	    The current version of this plugin.
	 */
	public $css_files 	= array();

	/**
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $js_files	    The current version of this plugin.
	 */
	public $js_files 	= array();

	/**
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $activated    Active this element.
	 */
	public $activated = true;

	/**
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $categories    The current version of this plugin.
	 */
	public $categories = 'Contents';

	/**
	 * Construct function.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function __construct() {

		$this->name 		= __('HTML Box', HAYYAB_BASENAME);
		$this->description 	= __('HTML code on header or footer', HAYYAB_BASENAME);

		$this->params = array(
				'hb_html' => array(
						'type' => 'html',
						'heading' => __('Raw html', HAYYAB_BASENAME),
						'description' => __('Enter your HTML content', HAYYAB_BASENAME),
						'value' => __('<p>I am raw html block.<br/>Click edit button to change this html</p>', HAYYAB_BASENAME)
				)
		);
	}

	/**
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function admin_render($param = null) {
		return '<div id="'.$param['id'].'" class="'.$param['class'].'" style="'.$param['style'].'">'.$param['hb_html'].'</div>';
	}

	/**
	 * Public Output funnction.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function public_render( $output ) { return false; }

}
