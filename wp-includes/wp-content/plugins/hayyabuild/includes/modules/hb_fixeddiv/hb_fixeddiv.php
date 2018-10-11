<?php
/**
 * Icon class.
 *
 *
 * @since      1.0.0
 * @package    hayyabuild
 * @subpackage hayyabuild/includes/modules/hb_fixeddiv
 * @author     ZintaThemes <>
 */
class HayyaModule_hb_fixeddiv
{

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
	public $icon 		= 'fa fa-external-link-square';

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $description The current version of this plugin.
	 */
	public $description = '';

	/**
	* Type of element "header, content, footer, all".
	*
	* @since    1.0.0
	* @access   public
	* @var      string    $type		The current version of this plugin.
	*/
	public $type 	= 'all';

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
	public $is_container = true;

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
	public $categories = 'Containers';

	/**
	 * Construct function.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function __construct() {

		$this->name 		= __( 'Fixed Box', HAYYAB_BASENAME );
		$this->description 	= __( 'Add fixed content box', HAYYAB_BASENAME );

       $this->params = array(
                'hb_align' => array (
                    'type' => 'dropdown',
                    'heading' => __ ( 'Text Align', HAYYAB_BASENAME ),
            		'value' => array(
        				'left' => __('Left', HAYYAB_BASENAME),
        				'center' => __('Center', HAYYAB_BASENAME),
        				'right' => __('Right', HAYYAB_BASENAME),
            		),
                ),
                'only_fixed' => array (
                    'type' => 'checkbox',
                    'heading' => __ ( 'Only Show Fixed', HAYYAB_BASENAME ),
					'description' => __('Remove this box from page and only show it as sticky bar.', HAYYAB_BASENAME),
					'value' => array(
						'only-fixed' => __('Yes',HAYYAB_BASENAME),
					)
                ),
                'after_header' => array (
                    'type' => 'checkbox',
                    'heading' => __ ( 'After Header Ends', HAYYAB_BASENAME ),
					'description' => __('Create a sticky box that becomes fixed to the top after header ends.', HAYYAB_BASENAME),
					'value' => array(
						'after-header-end' => __('Yes',HAYYAB_BASENAME),
					)
                ),
                'show_up' => array (
                    'type' => 'checkbox',
                    'heading' => __ ( 'Show it when Scroll up', HAYYAB_BASENAME ),
					'description' => __('hide this box as the user scrolls down, and show it again when the user scrolls up.', HAYYAB_BASENAME),
					'value' => array(
						'show-on-scrollup' => __('Yes',HAYYAB_BASENAME),
					)
                ),
        );

	}

	/**
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function admin_render( $param = null ) {
		$args = array(
			'hb_align' => ' text-align:' . $param['hb_align'] . ';',
			'only_fixed' => ' only-fixed',
			'after_header' => ' after-header-end',
			'show_up' => ' show-on-scrollup',
		);
		$class 	= $param['class'] . $param['only_fixed'] . $param['after_header'] . $param['show_up'];
		$html 	= '<div id="' . $param['id'] . '" class="' . $class . '" style="'. $param['hb_align'] . $param['style'] . '"></div>';
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
