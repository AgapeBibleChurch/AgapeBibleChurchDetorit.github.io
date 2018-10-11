<?php
/**
 * Icon class.
 *
 *
 * @since      1.0.0
 * @package    hayyabuild
 * @subpackage hayyabuild/includes/modules/hb_search
 * @author     ZintaThemes <>
 */
class HayyaModule_hb_search
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
	public $icon 		= 'fa fa-search';

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
	 * @var      string    $admin_css	    Admin style file.
	 */
	public $admin_css 	= array();

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

		$this->name 		= __('Search Box', HAYYAB_BASENAME);
		$this->description 	= __('Sample search form', HAYYAB_BASENAME);

		if ( ! HayyaHelper::__current_user('manage_options') ) {
			
			add_shortcode( 'hb_search', array( $this, 'public_render' ) );
			add_shortcode( 'hb_Search', array( $this, 'public_render' ) );

		} else {

			$this->params = array(
					'theme' => array(
							'type' => 'dropdown',
							'heading' => __('Search Style', HAYYAB_BASENAME),
							'description' => __('Select search box theme.', HAYYAB_BASENAME),
							'value' => array(
									'None' => __('none',HAYYAB_BASENAME),
									'hb_light' => __('Light',HAYYAB_BASENAME),
									'hb_dark' => __('Dark',HAYYAB_BASENAME),
									'hb_transparent' => __('Transparent',HAYYAB_BASENAME),
							)
					),
					'expand' => array(
							'type' => 'checkbox',
							'heading' => __('Expand on Hover', HAYYAB_BASENAME),
							'description' => __('Search will expand when hover over the icon.', HAYYAB_BASENAME),
							'value' => array(
									'hb_expand' => __('Yes',HAYYAB_BASENAME),
							)
					),
					'hb_right' => array(
							'type' => 'checkbox',
							'heading' => __('Float to right'),
							'description' => __('Float to right.'),
							'value' => array(
									'hb_right' => __('Yes',HAYYAB_BASENAME),
							)
					),
			);
		}
	}

	/**
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function admin_render($param = null) {
		$args = array(
				'theme' => array(
						'None' => '',
						'else' => ' '.$param['theme']
				),
				'expand' => ' hb_expand',
				'hb_right' => ' hb_right',
		);

		$classe = ' '.$param['theme'].' '.$param['expand'].' '.$param['hb_right'];

		$html = '<div id="'.$param['id'].'" class="'.$param['class'].$classe.'" style="'.$param['style'].'">
					<div class="hayya_hide_from_backend">
						[hb_search]
					</div>
					<div class="hayya_show_at_backend">
						'.__('Module name', HAYYAB_BASENAME).': '.$this->name.'<br/>
					</div>
				</div>';
		return array('output' => $html, 'args'=> $args );
	}

	/**
	 * Public Output funnction.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function public_render( $atts, $content = null ) {
		$s = '';
		if ( HayyaHelper::_get( 's' ) ) $s = HayyaHelper::_get( 's' ) ;
		$output = '<form role="search" method="get" class="" action="'.get_site_url().'/"><span class="icon"><i class="fa fa-search"></i></span>';
		if ( function_exists('is_plugin_active') && is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			$output .= '<input type="hidden" name="post_type" value="product" />';
		}
		$output .= '<input type="search" value="'.$s.'" name="s" placeholder="'.__('Search').'..." /></form>';
		return $output;
	}

}
