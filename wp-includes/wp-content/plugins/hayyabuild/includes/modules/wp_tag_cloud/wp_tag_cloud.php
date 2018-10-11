<?php
/**
 * Icon class.
 *
 *
 * @since      1.0.0
 * @package    hayyabuild
 * @subpackage hayyabuild/includes/modules/wp_tag_cloud
 * @author     ZintaThemes <>
 */
class HayyaModule_wp_tag_cloud
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
	public $icon 		= 'fa fa-wordpress';

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
	public $activated = false;

	/**
	 *
	 * @since    1.0.0
	 * @access   public
	 * @var      string    $categories    The current version of this plugin.
	 */
	public $categories = 'Widgets';

	/**
	 * Construct function.
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function __construct() {

		if( class_exists('WP_Widget_Tag_Cloud') ) {

			$this->name 		= __('Tag Cloud', HAYYAB_BASENAME);
			$this->description 	= __('Wordpress tag cloud widget', HAYYAB_BASENAME);
			$this->activated 	= true;

			if ( ! HayyaHelper::__current_user('manage_options') ) add_shortcode( 'hb_Widget_Tag_Cloud', array( $this, 'public_render' ) );

			$this->params = array(
					'taxonomy' => array(
							'type' => 'textfield',
							'heading' => __('Cloud Taxonomy', HAYYAB_BASENAME),
							'description' => __('The taxonomy the cloud will draw tags from. default: post_tag.', HAYYAB_BASENAME),
					),
					'title' => array(
							'type' => 'textfield',
							'heading' => __('Widget Title', HAYYAB_BASENAME),
							'description' => __('Use a Plain Text.', HAYYAB_BASENAME),
					),
					'before_title' => array(
							'type' => 'html',
                            'height' => '2',
							'heading' => __('Before Starting Title', HAYYAB_BASENAME),
							'description' => __('You can use HTML code.', HAYYAB_BASENAME),
					),
					'after_title' => array(
							'type' => 'html',
                            'height' => '2',
							'heading' => __('After Ending Title', HAYYAB_BASENAME),
							'description' => __('You can use HTML code.', HAYYAB_BASENAME),
					),
					'before_widget' => array(
							'type' => 'html',
                            'height' => '2',
							'heading' => __('Before Starting Widget', HAYYAB_BASENAME),
							'description' => __('You can use HTML code.', HAYYAB_BASENAME),
					),
					'after_widget' => array(
							'type' => 'html',
                            'height' => '2',
							'heading' => __('After Ending Widget', HAYYAB_BASENAME),
							'description' => __('You can use HTML code.', HAYYAB_BASENAME),
					),
					'tmp_title' => array('type' => 'hiddenfield', 'value' => 'activated'),
			);

// 			$this->css_files = array('WP_Widget_Tag_Cloud' => 'css/style.css');
		}
	}

	/**
	 *
	 * @since    1.0.0
	 * @access   public
	 */
	public function admin_render($param = null) {
		$args = array(
				'tmp_title' => $param['title'],
				'title' => ' title="'.$param['title'].'"',
				'taxonomy' => ' taxonomy="'.$param['taxonomy'].'"',
				'before_title' => ' before_title="'.$param['before_title'].'"',
				'after_title' => ' after_title="'.$param['after_title'].'"',
				'before_widget' => ' before_widget="'.$param['before_widget'].'"',
				'after_widget' => ' after_widget="'.$param['after_widget'].'"',

		);

		$cloud = $param['title'].$param['taxonomy'].$param['before_title'].$param['after_title'].$param['before_widget'].$param['after_widget'];

		$html = '<div id="'.$param['id'].'" class="hb_widget '.$param['class'].'" style="'.$param['style'].'">
					<div class="hayya_hide_from_backend">
						[hb_Widget_Tag_Cloud '.$cloud.']
					</div>
					<div class="hayya_show_at_backend">
						'.__('Module name', HAYYAB_BASENAME).': '.$this->name.'<br/>
						'.__('Title', HAYYAB_BASENAME).': '.$param['tmp_title'].'<br/>
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

		$before_title = $after_title = $before_widget = $after_widget = $instance = '';

        if ( $atts ) {
    		foreach ( $atts as $key => $value ) {
    			if ( !empty($value) ) {
    			    if ( $key ==  'title' ) $instance .= 'title='.$value;
    				else if ( $key ==  'taxonomy' ) $instance .= '&taxonomy='.$value;
    				else if ( $key ==  'before_title' ) $before_title = $value;
    				else if ( $key ==  'after_title' ) $after_title = $value;
    				else if ( $key ==  'before_widget' ) $before_widget = $value;
    				else if ( $key ==  'after_widget' ) $after_widget = $value;
    			}
    		}
		}

		$args = array(
				'before_title' => $before_title,
				'after_title'  => $after_title,
				'before_widget'  => $before_widget,
				'after_widget'   => $after_widget,
		);

		ob_start();
		the_widget( 'WP_Widget_Tag_Cloud', $instance, $args  ) ;
		$output = ob_get_clean();
		ob_end_flush();
		return $output;
	}

}
