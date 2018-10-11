<?php
/**
 * Heading text.
 *
 *
 * @since      1.0.0
 * @package    hayyabuild
 * @subpackage hayyabuild/includes/modules/hb_headingtext
 * @author     ZintaThemes <>
 */
class HayyaModule_hb_headingtext
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
	public $icon 		= 'fa fa-header';
	
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
		$this->name 		= __('Heading Text', HAYYAB_BASENAME);
		$this->description 	= __('Insert Heading Text', HAYYAB_BASENAME);
        
        $this->params = array (
                'text' => array(
                        'type' => 'textfield',
                        'heading' => __('Heading Text', HAYYAB_BASENAME),
                        'description' => __('You can use HTML code.', HAYYAB_BASENAME),
                        'value' => __('Insert Text.', HAYYAB_BASENAME),
                ),
                'tag' => array (
                        'type' => 'dropdown',
                        'heading' => __ ( 'Heading Tag', HAYYAB_BASENAME ),
                        'description' => __ ( 'Select Heading tag.', HAYYAB_BASENAME ),
                		'value' => array(
                				'h1' => 'h1',
                				'h2' => 'h2',
                				'h3' => 'h3',
                				'h4' => 'h4',
                				'h5' => 'h5',
                				'h6' => 'h6',
                		)
                ),
                'link' => array(
                        'type' => 'link',
                        'heading' => __('Link', HAYYAB_BASENAME),
                        'description' => __('Heading text link.', HAYYAB_BASENAME)
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
				'link' => array(
						null => $param['text'],
						'else' => '<a href="'.$param['link'].'" >'.$param['text'].'</a>',
				),
                'tag' => array(
				    null => 'h2',
                    'else' => $param['tag'],
				),
		);
		$html = '<'.$param['tag'].' id="'.$param['id'].'" class="'.$param['class'].'" style="'.$param['style'].'">'.$param['link'].'</'.$param['tag'].'>';
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
