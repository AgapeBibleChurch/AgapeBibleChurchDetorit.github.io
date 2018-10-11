<?php
/**
 *
 * HTML cleaner.
 *
 * @since		 2.1.0
 * @package	  hayyabuild
 * @subpackage  hayyabuild/includes
 * @author		zintaThemes <>
 *
 */
if (! defined( 'ABSPATH' )) {
	exit();
}


class HayyaParser {

	/**
	 * The ID of this plugin.
	 *
	 * @since 1.3.0
	 * @access private
	 * @var string $plugin_name name of this plugin.
	 */
	private $plugin_name = null;

	/**
	 * The version of this plugin.
	 *
	 * @since 1.3.0
	 * @access private
	 * @var string $version The current version of this plugin.
	 */
	private $version = null;

	/**
	 * HTML code.
	 *
	 * @since 1.3.0
	 * @access private
	 * @var string $config The current version of this plugin.
	 */
	public $config = array();

	/**
	 *
	 * Initialize the class and set its properties.
	 *
	 * @since 1.3.0
	 * @param string $plugin_name
	 *		  	The name of this plugin.
	 * @param string $version
	 *		  	The version of this plugin.
	 */
	public function __construct() {
		require HAYYAB_PATH . 'vendor/autoload.php';
	}

	/**
	 *
	 * @param unknown $html
	 * @param unknown $search
	 * @return unknown
	 */
	private function HayyaPhpSimpleDom($html, $search) {
		if ( $dom = $this->HayyaPhpSimpleHtmlDomParser() ) {
			if ( method_exists($dom, 'str_get_html') ) {
				$dom = $dom->str_get_html( $html );
				foreach ( $this->HayyaPhpSimpleFind($dom) as $key => $value ) {
					foreach ( $value as $k => $v ) {
						if ( $key === 'remove') $v->outertext = '';
						if ( $key === 'inner') $v->outertext = $v->innertext;
						if ( $key === 'removeclass') $v->class = $this->HayyaReplaceAttrs('/ hb_ctnr/', $v->class);
					}
				}
				return $dom;
			}
		}
		return false;
	}

	/**
	 *
	 * @param unknown $dom
	 * @param unknown $search
	 * @return unknown
	 */
	private function HayyaPhpSimpleFind($dom) {
		$query = array( 'remove' => '.hayya_show_at_backend', 'inner' => '.hayya_hide_from_backend', 'removeclass' => '.hb_ctnr' );
		foreach ( $query as $key => $value ) $find[$key] = $dom->find( $value );
		return $find;
	}

	/**
	 * use Sunra\PhpSimple\HtmlDomParser;
	 * @param string $value
	 * @return HtmlDomParser
	 */
	private function HayyaPhpSimpleHtmlDomParser($value='') {
		if ( ! class_exists('Sunra\PhpSimple\HtmlDomParser') ) return false;
		return new Sunra\PhpSimple\HtmlDomParser();
	}

	/**
	 *
	 * @param unknown $replace
	 * @param unknown $content
	 * @return unknown
	 */
	private function HayyaReplaceAttrs($replace, $content) {
		return preg_replace_callback(
			$replace,
			function( $matches ) { return ''; },
			$content
		);
	}

	/**
	 * include HTMLPurifier library
	 */
	private function HayyaHTMLPurifier() {
		// require_once HAYYAB_PATH.'includes/libs/htmlpurifier/library/HTMLPurifier.auto.php';
		require_once HAYYAB_PATH.'includes/libs/htmlpurifier-callto.php';
		require_once HAYYAB_PATH.'includes/libs/htmlpurifier-URI.php';

		$this->config = HTMLPurifier_Config::createDefault();
		// $this->config->set('URI.Host', get_site_url());
		$this->config->set( 'Attr.EnableID', true );

		$this->config->set( 'URI.Host', $_SERVER ['HTTP_HOST'] );
		$this->config->set( 'URI.SafeIframeRegexp', '%^(https?:)?//(www\.youtube(?:-nocookie)?\.com/embed/|player\.vimeo\.com/video/|facebook\.com|www\.facebook\.com)%' ); // allow YouTube and Vimeo
		$this->config->set( 'URI.AllowedSchemes', $this->AllowedSchemes() );

		$this->config->set( 'HTML.SafeObject', true );
		$this->config->set( 'HTML.SafeEmbed', true );
		$this->config->set( 'HTML.SafeIframe', true );
		$this->config->set( 'HTML.TargetBlank', true );
		$css_definition = $this->config->getDefinition( 'CSS' );

		$border_radius =
		$info['border-top-left-radius'] =
		$info['border-top-right-radius'] =
		$info['border-bottom-left-radius'] =
		$info['border-bottom-right-radius'] = new HTMLPurifier_AttrDef_CSS_Composite(
			array(
				new HTMLPurifier_AttrDef_CSS_Length('0'),
				new HTMLPurifier_AttrDef_CSS_Percentage(true)
			)
		);

		$info['border-radius'] = new HTMLPurifier_AttrDef_CSS_Multiple($border_radius);

		// $info ['border-radius'] = new HTMLPurifier_AttrDef_CSS_Length();
		// $info ['background-size'] = new HTMLPurifier_AttrDef_Enum();
		// $info ['background'] = new HTMLPurifier_AttrDef_Enum();

		$info ['border-color'] = new HTMLPurifier_AttrDef_CSS_Color();
		$info ['opacity'] = new HTMLPurifier_AttrDef_CSS_AlphaValue();
		$allow_important = $this->config->get( 'CSS.AllowImportant' );
		foreach ( $info as $k => $v ) {
			$css_definition->info[$k] = new HTMLPurifier_AttrDef_CSS_ImportantDecorator( $v, $allow_important );
		}
		$def = $this->config->getHTMLDefinition( true );
		foreach ( $this->allowedAttrs() as $attrs ) {
			$def->info_global_attr[$attrs] = new HTMLPurifier_AttrDef_Text();
		}
	}

	/**
	 * @TODO: check me
	 * @param unknown $html
	 */
	public function cleanPublicHTML($content = '') {
		$this->HayyaHTMLPurifier();
		$def = $this->config->getHTMLDefinition( true );
		$purifier = new HTMLPurifier( $this->config );
		$content = $purifier->purify($content);
		if ( $this->HayyaPhpSimpleDom( $content, '.hb_conditionalBox' ) ) {
			$content = $this->HayyaPhpSimpleDom( $content, '.hb_conditionalBox' );
		}
		return $content;
	}

	/**
	 *
	 * @param unknown $html
	 */
	public function cleanAdminHTML($content = '') {
		$this->HayyaHTMLPurifier();
		$def = $this->config->getHTMLDefinition( true );
		$def->info_global_attr['data-hb_element'] = new HTMLPurifier_AttrDef_Text();
		$def->info_global_attr['data-hb-id'] = new HTMLPurifier_AttrDef_Text();
		$def->info_global_attr['data-hb-style'] = new HTMLPurifier_AttrDef_Text();
		$def->info_global_attr['data-hb-hayya_class'] = new HTMLPurifier_AttrDef_Text();
		$def->info_global_attr['data-hb-device'] = new HTMLPurifier_AttrDef_Text();
		$elements = new HayyaModules( 'showall' );
		$elements_list = $elements->elements_list();
		foreach ( $elements_list as $path => $element ) {
			foreach ( $element as $key => $val ) {
				foreach ( $val as $PKey => $PVal ) {
					if ($PKey == 'params') {
						foreach ( $PVal as $k => $v ) $def->info_global_attr ['data-hb-' . $k] = new HTMLPurifier_AttrDef_Text();
					}
				}
			}
		}
		$purifier = new HTMLPurifier( $this->config );
		return $purifier->purify( $content );
	}

	/**
	 *
	 * @return boolean[]
	 */
	private function AllowedSchemes() {
		return array( 'http' => true, 'https' => true, 'mailto' => true, 'ftp' => true, 'nntp' => true, 'news' => true, 'data' => true, 'tel' => true, 'callto' => true );
	}

	/**
	 * allowed Attrs list
	 *
	 * @return string[]
	 */
	private function allowedAttrs() {
		return array( 'data-color', 'data-icon', 'data-activates', 'role', 'aria-valuenow', 'aria-valuemin', 'aria-valuemax', 'width', 'height', 'src', 'frameborder', 'allowfullscreen', 'data-chrome', 'data-tweet-limit', 'data-size', 'data-url', 'data-text', 'data-show-count', 'data-tweet-limit', 'data-chrome', 'data-width', 'data-height' );
	}

} // end of class HayyaParser
