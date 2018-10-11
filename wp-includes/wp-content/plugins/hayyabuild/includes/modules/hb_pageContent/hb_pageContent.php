<?php
/**
 * Icon class.
 *
 *
 * @since      3.0.0
 * @package    hayyabuild
 * @subpackage hayyabuild/includes/modules/hb_pageContent
 * @author     ZintaThemes <>
 */
class HayyaModule_hb_pageContent
{
	/**
	 * Type of element "header, content, footer, all".
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $type		The current version of this plugin.
	 */
	public $type 	= 'content';

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
	public $icon 		= 'fa fa-newspaper-o';

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
	public $show_settings_on_create = false;

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

		$this->name 		= __('Page Content', HAYYAB_BASENAME);
		$this->description 	= __('Insert Page Content', HAYYAB_BASENAME);

		$this->params = array(

				'width' => array(
						'type' => 'dropdown',
						'heading' => __('Content Width', HAYYAB_BASENAME),
						'description' => __('Make this ceontent as a contanier or a container-fluid', HAYYAB_BASENAME),
						'value' => array(
								null => __('Default', HAYYAB_BASENAME ),
								'container' => __('Container', HAYYAB_BASENAME ),
								'container-fluid' => __('Container Fluid', HAYYAB_BASENAME ),
						)
				),
		);

	}

	/**
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function admin_render($param = null) {
		return '<div id="'.$param['id'].'" class="'.$param['class'].' '.$param['width'].'" style="'.$param['style'].'">
					<div class="hayya_hide_from_backend">
						[hayya_content]
					</div>
					<div class="hayya_show_at_backend">
						'.__('Module name', HAYYAB_BASENAME).': '.$this->name.'<br/>
					</div>
				</div>';
	}

	/**
	 * Public Output funnction.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function public_render( $output ) { return $output; }

}
