<?php
/**
 * Text class.
 *
 *
 * @since      1.0.0
 * @package    hayyabuild
 * @subpackage hayyabuild/includes/modules/hb_bs_button
 * @author     ZintaThemes <>
 */
class HayyaModule_bs_button
{
	/**
	 * Type of element "header, content, footer, all".
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    		$type		The current version of this plugin.
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
	public $icon 		= 'fa fa-hand-o-up';

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
	 * @var      string    $has_content	    The current version of this plugin.
	 */
	public $has_content = false;

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
	public $categories = 'Bootstrap';

	/**
	 * Construct function.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function __construct() {

		$this->name 		= __('Button', HAYYAB_BASENAME);
		$this->description 	= __('add bootstrap button to content', HAYYAB_BASENAME);

		$this->params = array(
				'text' => array(
						'type' => 'textfield',
						'heading' => __('Text', HAYYAB_BASENAME),
						'value' => __('Buttom text.', HAYYAB_BASENAME)
				),
				'btnstyle' => array(
						'type' => 'dropdown',
						'heading' => __('Button Style', HAYYAB_BASENAME),
						'description' => __('Select bootstrap buttom style.', HAYYAB_BASENAME),
						'value' => array(
								'default' 	=> __('Default', HAYYAB_BASENAME),
								'primary' 	=> __('Primary', HAYYAB_BASENAME),
								'success' 	=> __('Success', HAYYAB_BASENAME),
								'info' 		=> __('Info', HAYYAB_BASENAME),
								'warning' 	=> __('Warning', HAYYAB_BASENAME),
								'danger' 	=> __('Danger', HAYYAB_BASENAME),
								'linlk' 	=> __('Link', HAYYAB_BASENAME),
						)
				),
				'btnsize' => array(
						'type' => 'dropdown',
						'heading' => __('Button size'),
						'value' => array(
								'default' => __('Default', HAYYAB_BASENAME),
								'lg' => __('Large', HAYYAB_BASENAME),
								'md' => __('Medium', HAYYAB_BASENAME),
								'sm' => __('Small', HAYYAB_BASENAME),
								'xs' => __('XSmall', HAYYAB_BASENAME),
						)
				),
				'link' => array(
						'type' => 'link',
						'heading' => __('Link', HAYYAB_BASENAME),
						'description' => __('Button link (url).', HAYYAB_BASENAME)
				),
				'btnicon' => array(
						'type' => 'icon',
						'heading' => __('Buttom icon', HAYYAB_BASENAME)
				),
				'iconright' => array(
						'type' => 'checkbox',
						'heading' => __('Put icon at right', HAYYAB_BASENAME),
						'description' => __('Put icon at the right of the text.'),
						'value' => array(
								'yes' => __('Yes', HAYYAB_BASENAME)
						)
				)
		);
	}

	/**
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function admin_render($param = null) {
		$args = array(
				'btnicon' 	=> '<i class="'.$param['btnicon'].'"></i>',
				'btnsize'	=> ' btn-'.$param['btnsize'],
				'btnstyle'	=> ' btn-'.$param['btnstyle'],
				'iconright'	=> array(
						 null 		=> $param['btnicon'].' '.$param['text'],
						'yes' 		=> $param['text'].' '.$param['btnicon']
				),
		);
		$html = '<div id="'.$param['id'].'" class="'.$param['class'].'"><a href="'.$param['link'].'" class="bs-btn' . $param['btnsize'] . $param['btnstyle'] . '"  style="'.$param['style'].'">'.$param['iconright'].'</a></div>';
		return array('output' => $html, 'args'=> $args );
	}

	/**
	 * Public Output funnction.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function public_render( $output ) { return $output; }

}
