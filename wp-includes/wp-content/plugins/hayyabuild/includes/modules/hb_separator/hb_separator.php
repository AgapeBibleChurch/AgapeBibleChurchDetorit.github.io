<?php
/**
 * Horizontal separator line class.
 *
 *
 * @since	  	1.0.0
 * @package		hayyabuild
 * @subpackage 	hayyabuild/includes/modules/hb_separator
 * @author	 	ZintaThemes <>
 */
class HayyaModule_hb_separator
{
	/**
	 * Type of element "header, content, footer, all".
	 *
	 * @since 	1.0.0
	 * @access	public
	 * @var	  	string	$type		The current version of this plugin.
	 */
	public $type 	= 'all';

	/**
	 * The name of element.
	 *
	 * @since 	1.0.0
	 * @access 	public
	 * @var	  	string	$name		The current version of this plugin.
	 */
	public $name 		= '';

	/**
	 * @since 	1.0.0
	 * @access 	public
	 * @var	  	string	$icon		The current version of this plugin.
	 */
	public $icon 		= 'fa fa-sort';

	/**
	 * The version of this plugin.
	 *
	 * @since 	1.0.0
	 * @access 	public
	 * @var	  	string	$description The current version of this plugin.
	 */
	public $description = '';

	/**
	 * Show settings dialog after click in create.
	 *
	 * @since 	1.0.0
	 * @access 	public
	 * @var	  	string	$show_settings_on_create	The current version of this plugin.
	 */
	public $show_settings_on_create = true;

	/**
	 * @since 	1.0.0
	 * @access 	public
	 * @var	  	string	$is_container	The current version of this plugin.
	 */
	public $is_container = false;

	/**
	 *
	 * @since 	1.0.0
	 * @access 	public
	 * @var	  	string	$params			The current version of this plugin.
	 */
	public $params 	= array();

	/**
	 *
	 * @since 	1.0.0
	 * @access 	public
	 * @var	  	string	$css_files		The current version of this plugin.
	 */
	public $css_files 	= array();

	/**
	 *
	 * @since 	1.0.0
	 * @access 	public
	 * @var	  	string	$js_files		The current version of this plugin.
	 */
	public $js_files 	= array();

	/**
	 *
	 * @since 	1.0.0
	 * @access 	public
	 * @var	  	string	$activated	Active this element.
	 */
	public $activated = true;

	/**
	 *
	 * @since 	1.0.0
	 * @access 	public
	 * @var	  	string	$categories	The current version of this plugin.
	 */
	public $categories = 'Contents';

	/**
	 * Construct function.
	 *
	 * @since 	1.0.0
	 * @access 	public
	 */
	public function __construct() {
		$this->name 		= __('Separator', HAYYAB_BASENAME);
		$this->description 	= __('Horizontal separator line', HAYYAB_BASENAME);
		$this->params = array (
			'separator_style' => array (
				'type' => 'dropdown',
				'heading' => __('Separator Style', HAYYAB_BASENAME),
				'description' => __('Select Separator style or keep it without styling to use a default style.', HAYYAB_BASENAME ),
				'value' => array(
					'one' 		=> __('One line', HAYYAB_BASENAME),
					'double' 	=> __('Double line', HAYYAB_BASENAME),
					'dotted' 	=> __('Dotted', HAYYAB_BASENAME),
					'dashed' 	=> __('Dashed', HAYYAB_BASENAME),
				),
			) ,
			'text' => array(
				'type' => 'textfield',
				'heading' => __('Separator Text', HAYYAB_BASENAME),
				'description' => __('Use a Plain Text.', HAYYAB_BASENAME),
			),
			'icon' => array(
				'type' => 'icon',
				'heading' => __('Separator Icon', HAYYAB_BASENAME),
				'description' => __('You can use bootstrap icons or fontawesome icons.', HAYYAB_BASENAME),
				// 'value' => 'fa fa-chevron-down',
			),
			'text_alignment' => array (
				'type' => 'dropdown',
				'heading' => __('Text Alignment', HAYYAB_BASENAME),
				'description' => __('Text or icon alignment.', HAYYAB_BASENAME),
				'value' => array(
					'center' => __('Center', HAYYAB_BASENAME),
					'left-text' => __('Left', HAYYAB_BASENAME),
					'right-text' => __('Right', HAYYAB_BASENAME),
				),
			) ,
			'color' => array(
				'type' => 'colorpicker',
				'heading' => __('Separator Color', HAYYAB_BASENAME),
				'description' => __('Select Separator Color.', HAYYAB_BASENAME),
			),
			'mask' => array(
				'type' => 'checkbox',
				'heading' => __('Gradient Separator'),
				'description' => __('Apply gradient color for the separator line.<br/>this option not working correctly on older versions of IE.'),
				'value' => array(
					'separator-mask' => __('Yes',HAYYAB_BASENAME),
				)
			),
			'text_color' => array(
				'type' => 'hiddenfield'
			),
		);
	}

	/**
	 *
	 * @since 	1.0.0
	 * @access 	public
	 */
	public function admin_render( $param = null ) {
		$args = array(
			'color'		=> array(
				'empty' => array(
					'color' => '',
					'text_color' => '',
				),
				'else' => array(
					'text_color' 	=> ' color: ' .  $param['color'] . ';',
					'color' 		=> ' style="border-color: ' .  $param['color'] . ';"',
				),
			),
			'separator_style' => array(
				'double' 	=> ' duble-lines',
				'dotted' 	=> ' dotted',
				'dashed' 	=> ' dashed',
				'else' 		=> '',
			),
			'text_alignment' => array(
				'left-text' => ' left-text',
				'right-text' => ' right-text',
				'else' => '',
			),
			'text' 	=> ' <h4 style="' . $param['text_color'] . $param['style'] . '">' . $param['text']  . '</h4> ',
			'icon' 	=> ' <i class="' . $param['icon'] . '"' . $param['text_color'] . '  style="' . $param['text_color'] . $param['style'] . '"></i> ',
			'mask' 	=> ' separator-mask',
		);

		$class 	= $param['class'] . $param['separator_style'] . $param['text_alignment'] . $param['mask'];

		$html = '<div id="' . $param['id'] . '" class="' . $class . '">
					<div class="left-separator"' .  $param['color'] . '></div>
					' .  $param['text'] . $param['icon'] . '
					<div class="right-separator"' .  $param['color'] . '></div>
				</div>';

		return array('output' => $html, 'args'=> $args );
	}

	/**
	 * Public Output funnction.
	 *
	 * @since 	1.0.0
	 * @access 	public
	 */
	public function public_render( $output ) { return false; }

}
