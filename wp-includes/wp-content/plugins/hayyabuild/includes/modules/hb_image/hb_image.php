<?php
/**
 * Text class.
 *
 *
 * @since      1.0.0
 * @package    hayyabuild
 * @subpackage hayyabuild/includes/modules/hb_image
 * @author     ZintaThemes <>
 */
class HayyaModule_hb_image
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
	public $icon 		= 'fa fa-image';

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
	public $categories = 'Contents';

	/**
	 * Construct function.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function __construct() {

		$this->name 		= __('Image', HAYYAB_BASENAME);
		$this->description 	= __('Insert Image file', HAYYAB_BASENAME);

		$this->params = array(
				'image' => array(
						'type' => 'image',
						'heading' => __('Select Image', HAYYAB_BASENAME)
				),
				'alt' => array(
                        'type' => 'textfield',
                        'heading' => __('ALT text', HAYYAB_BASENAME)
                ),
                'link' => array(
                        'type' => 'link',
                        'heading' => __('Link', HAYYAB_BASENAME),
                        'description' => __('Image link (URL).', HAYYAB_BASENAME)
                ),
				'width' => array(
                        'type' => 'textfield',
                        'heading' => __('Width', HAYYAB_BASENAME),
                        'description' => __('Image width. keep it empty for auto width.<br/>Examples: 100px, 80%, 20em', HAYYAB_BASENAME)
                ),
				'height' => array(
                        'type' => 'textfield',
                        'heading' => __('Height', HAYYAB_BASENAME),
                        'description' => __('Image height. keep it empty for auto height.<br/>Examples: 100px, 80%, 20em', HAYYAB_BASENAME)
                ),
				'image_classes' => array(
                        'type' => 'textfield',
                        'heading' => __('Classes', HAYYAB_BASENAME),
                        'description' => __('Add CSS classes to image', HAYYAB_BASENAME)
                ),
		);
	}

	/**
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function admin_render($param = null) {
		$args = array(
			'width' => 'width: '.$param['width'].';',
			'height' => 'height: '.$param['height'].';',
			'image' => array(
					'empty' => __('Please Select Image file', HAYYAB_BASENAME),
					'else' => '<img class="' . $param['image_classes'] . '" id="'.$param['id'].'" src="'.$param['image'].'" alt="'.$param['alt'].'" style="'.$param['style'].$param['width'].$param['height'].'" />',
			),
			'link' => array(
					null => $param['image'],
					'else' => '<a href="'.$param['link'].'">'.$param['image'].'</a>',
			),
		);

		$html = '<span class="'.$param['class'].'">'.$param['link'].'</span>';
		return array('output' => $html, 'args'=> $args );
	}

	/**
	 * Public Output funnction.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function public_render( $output ) { return false; }

}
