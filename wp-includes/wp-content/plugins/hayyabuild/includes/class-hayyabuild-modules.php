<?php
/**
 * The elements class.
 *
 * This is used to define modules for admin builder page and public
 *
 * @since		1.0.0
 * @package	 hayyabuild
 * @subpackage hayyabuild/includes
 * @author	  zintaThemes <>
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }


class HayyaModules {

		/**
		 *
		 * @since		3.0.0
		 * @access  	private
		 * @var	  	string		$showModulesList		Elements type.
		 */
		private static $showModulesList;

		/**
		 *
		 * @since		3.0.0
		 * @access  	private
		 * @var	  	array		$elements	Elements type.
		 */
		private static $newModules = array();

		/**
		 * @package		HayyaBuild
		 * @access		private
		 * @since		3.0.0
		 * @var unknown
		 */
		private static $HAYYAPATH = null;

		/**
		 *
		 * Define all elements.
		 *
		 * @package		HayyaBuild
		 * @access		public
		 * @since		1.0.0
		 * @return		elements()
		 */
		public function __construct( $type = null ) {
			self::$showModulesList 	= $type;
			self::$HAYYAPATH 		= HAYYAB_PATH . 'includes/modules/';
		}

		/**
		 *
		 * Get elements list from elememnt directory
		 *
		 * @package		HayyaBuild
		 * @access		public
		 * @since		1.0.0
		 * @var			string		$list
		 */
		public function elements_list() {
			$elements = self::hayya_modules();
			foreach ( $elements as $path => $value ) {
				$elements_list = array();
				foreach ( $value as $entry ) {
					 $e = array();
					if ( is_dir( $path . $entry ) && file_exists( $path . $entry . '/' .  $entry . '.php') ) {
						if ( $e = $this->modules_info($path, $entry) ) {
								$elements_list[] = $e;
						  }
					}
				}
				if ( ! empty($elements_list) ) $modules[$path] = $elements_list;
			}
			if (isset($modules)) return $modules;
			else return false;
		}

		/**
		 *
		 * @since		1.0.0
		 * @param 		unknown 		$output
		 * @return 		mixed
		 */
		private static function hayya_modules() {
			self::hayya_get_modules( self::$HAYYAPATH, self::hayya_modulesList() );
			$setting = self::elements_settings_list();
			if ( self::$showModulesList === 'showall' || !$setting ) {
				$elements = self::$newModules;
		  } else {
				if ( is_array($setting) && is_array(self::$newModules) ) {
					$setting = array_diff($setting, array( 0 => 'on'));
					foreach (self::$newModules as $path => $list ) {
						foreach ( $setting as $element => $status ) {
							if(($key = array_search($element, $list)) !== false) {
								unset($list[$key]);
							}
						}
						$elements[$path] = $list;
						unset($list);
					}
				}
			}
			return self::HayyaBuildCheckList($elements);
		}

		/**
		 * @package		HayyaBuild
		 * @since		3.0.0
		 * @access		private
		 * @return 		array()
		 */
		private static function elements_settings_list () {
			if ( function_exists('get_option') ) {
				$setting = get_option('hayyabuild_settings');
				if ( isset( $setting['elements'] ) && is_array( $setting['elements']) && count($setting['elements']) ) return $setting['elements'];
			} else return false;
		}

		/**
		 *
		 * @package		HayyaBuild
		 * @since		3.0.0
		 * @access		private
		 * @return array()
		 */
		private static function hayya_modulesList() {
			return array( 'hb_row','bs_alert', 'bs_button', 'hb_div', 'hb_menu', 'hb_pageContent','hb_text','bs_embed','hb_testimonial','bs_labels','bs_panel','bs_progress','bs_well','hb_breadcrumb','hb_ContactForm7','hb_facebookTimeline','hb_fbLikeButton','hb_fixeddiv','hb_gobottom','hb_googleMap','hb_gotop','hb_headingtext','hb_html','hb_icon','hb_card','hb_image','hb_LayerSlider','hb_revslider','hb_search','hb_separator','hb_smenu','hb_social','hb_twitterButton','hb_twitterTimeline','wp_archives','wp_calendar','wp_categories','wp_recent_comments','wp_recent_posts','wp_tag_cloud','hb_conditionalBox');
            // 'bs_jumbotron', 'bs_navbar',
		}

		/**
		 *
		 * @since		1.0.0
		 * @param 		unknown 		$output
		 * @return 		mixed
		 */
		private static function hayyaBuildFixOutput($output) {
			return preg_replace( array( '/\>[^\S ]+/s', '/[^\S ]+\</s', '/(\s)+/s', ), array( '>', '<', '\\1' ), $output );
		}

		/**
		 *
		 * @since		 3.0.0
		 * @param		 unknown $list
		 * @return		array
		 */
		private static function HayyaBuildCheckList( $list ) {
			if ( isset($list[self::$HAYYAPATH]) ) $new = array( self::$HAYYAPATH => $list[self::$HAYYAPATH] );
			else $new = array();
			foreach ( $list as $key => $value ) {
				if ( $key == self::$HAYYAPATH ) continue;
	 		$array = self::HayyaBuildCheckRepeated($value, $new);
	 		if ( !empty($array) ) $new[$key] = $array;
			}
			return $new;
		}

		/**
		 *
		 * @since		 3.0.0
		 * @param		 unknown $old
		 * @param		 unknown $new
		 * @return		array[]
		 */
		private static function HayyaBuildCheckRepeated ( $old, $new ) {
			$array = array();
			if ( is_array($old)) {
	 		foreach ( $old as $e ) {
	 			if ( is_array($new) ) foreach ( $new as $k => $v ) $found = ( in_array($e, $v) ) ? true : false;
		 		if ( isset($found) && !$found ) $array[] = $e;
	 		}
			}
			return $array;
		}

		/**
		 *
		 * @since		 3.0.0
		 * @param		 unknown $path
		 * @param		 unknown $list
		 */
		public static function get_modules_public( $list = array() ) {
			if ( is_array($list) && !empty($list) ) {
				new self('showall');
				$elements = self::hayya_modules();
				foreach ($elements as $key => $value) {
					$modulesIntersect = array_intersect($list, $value);
					if (!empty($modulesIntersect)) $modulesList[$key] = $modulesIntersect;
				}
				return $modulesList;
			} return false;
		}

		/**
		 *
		 * @since		3.0.0
		 * @param 		unknown $path
		 * @param 		unknown $list
		 */
		public static function hayya_get_modules( $path = null, $list = null ) {
			if ( ! empty( $path ) && is_dir( $path ) && is_array( $list ) ) {
				if ( ! has_filter( 'hayyabuild_modules' ) ) self::HayyaBuildModules();
				if ( has_filter( 'hayyabuild_modules' ) ) {
					 self::$newModules = apply_filters( 'hayyabuild_modules', $path, $list );
	 			return true;
				}
			} else return false;
		}

		/**
		 * @since		3.0.0
		 * @return		none
		 */
		private static function HayyaBuildModules() {
			add_filter( 'hayyabuild_modules', array('HayyaModules', 'hayyabuild_modules'), 10, 2 );
			return true;
		}

		/**
		 *
		 * @since		3.0.0
		 * @param		 unknown 	$elements
		 * @param		 unknown 	$path
		 * @param		 unknown 	$list
		 * @return		unknown
		 */
		public static function hayyabuild_modules( $path = '', $list = array() ) {
			if ( !empty(self::$newModules) ) {
				return array_merge( self::$newModules, array($path => $list) );
		  } else {
				return array($path => $list);
		  }
		}

		/**
		 *
		 * @package		HayyaBuild
		 * @since		3.0.0
		 * @access		private
		 * @param 		unknown 	$entry
		 * @param 		unknown 	$element
		 * @return 		array()
		 */
		private function modules_info ( $path, $entry ) {
			require_once $path . $entry . '/'. $entry . '.php';
			$class_name = 'HayyaModule_' . $entry;
			if ( class_exists( $class_name ) ) {
				$element = new $class_name();
				if ( self::$showModulesList === 'showall' || ( $element->name && $element->categories && $element->activated  && ( $element->type === self::$showModulesList || $element->type === 'all' ) ) ) {
					$is_container = ( isset($element->is_container) && $element->is_container ) ? true : false;
					$has_content = ( isset($element->has_content) && $element->has_content ) ? true : false;
					if ( empty($element->admin_css) ) $element->admin_css = '';
					 if ( class_exists('HayyaHelper') && HayyaHelper::__is_build_pages() ) {
						  return array( 'base' => $entry, 'name' => $element->name, 'type' => $element->type, 'categories' => $element->categories, 'icon' => $element->icon, 'description' => $element->description, 'show_settings_on_create' => $element->show_settings_on_create, 'is_container' => $is_container, 'has_content' => $has_content, 'params' => $element->params, 'admin_css' => $element->admin_css, 'render' => self::modules_JS_render($element,$entry,$is_container,$has_content) );
					 } else {
						  return array( 'base' => $entry, 'name' => $element->name, 'type' => $element->type, 'categories' => $element->categories, 'icon' => $element->icon, 'description' => $element->description, 'show_settings_on_create' => $element->show_settings_on_create, 'is_container' => $is_container, 'has_content' => $has_content, 'params' => $element->params, 'admin_css' => $element->admin_css );
					 }
				} else {
					return false;
				}
			}
		}

		/**
		 *
		 * @package		HayyaBuild
		 * @access		public
		 * @since		1.0.0
		 * @var			string			$elements
		 */
		public function elements() {
			$elements_list = $this->elements_list();
			$element = '';
			if ( $elements_list && is_array($elements_list) ) {
	 		foreach ( $elements_list as $path => $element_list ) {
	 			foreach ($element_list as $elements ) {
		 			if ( $elements['render'] ) {
		 				$base = $name = $icon = $description = $show_settings_on_create = $params = $prms_list = $render = '';
		 				extract( $elements );
		 				foreach ( $params as $keys => $param ) {
		 					if ( $param && is_array($param) ) {
		 						$prms_list .= '{ param_name: "'.$keys.'",' . $this->hayyBuildElementParamsValue( $param ) . '},';
		 					}
		 				}
		 				$prmss = "params : [ $prms_list ],";
		 				$has_content = ( isset($has_content) && $has_content ) ? 'true' : 'false';
		 				$is_container = ( $is_container ) ? 'true' : 'false';
		 				$show_settings_on_create = ( $show_settings_on_create ) ? 'true' : 'false';
		 				$element .= '{base: "'.$base.'", name: "'.$name.'", icon: "'.$icon.'", description: "'.$description.'", show_settings_on_create: '.$show_settings_on_create.', is_container: '.$is_container.', has_content: '.$has_content.',' . $prmss . ' render : '.$render.' },';
		 			}
	 			}
	 		}
	 		return $this->hayyaBuildJS($element);
			} else return false;
		}

		/**
		 *
		 * @package		HayyaBuild
		 * @access		public
		 * @param 		unknown 		$param
		 * @return 		string
		 */
		private function hayyBuildElementParamsValue( $param = null ) {
			$prms = '';
			if ( is_array($param) && !empty($param)) {
				foreach ( $param as $key => $value ) {
					if ( is_array($value) ) {
						if ( !empty($value) ) {
							$tmp = '';
							foreach ($value as $k => $v ) {
								if (!is_array($v) && !is_object($v)) $tmp .= '"'.$k.'": "'.$v.'",';
							}
							$prms .= $key.': {'.$tmp.'},';
						}
					} else $prms .= $key.': "'.$value.'",'; // @TODO: remove this line
				}
				return $prms;
			}
		}

		/**
		 *
		 * @package		HayyaBuild
		 * @since		3.0.0
		 * @access		private
		 * @param unknown $element
		 * @return string
		 */
		private static function modules_JS_render( $element, $base = null, $is_container = null, $has_content = null ) {
			$prefix 	= 'HAYYA';
			$js 		= '';
			$params 	= array();
			$params 	= $element->params;
			$classes 	= ($is_container && !$has_content) ? 'hb_element '.$base.' hb_ctnr ' : 'hb_element '.$base.' ';

			$params['class'] = 'hayya_class';
			$params['style'] = 'style';
			$params['id'] = 'id';

			foreach ( $element->params as $k => $v ) {
				$params[$k] = $k;
			}
			foreach ( $params as $k => $v ) {
				$js .= 'var '.$prefix.$k.' = this.attrs[\''.$v.'\'];';
			}

			$params['class'] = $classes.'\'+'.$prefix.'class+\'';
			$params['style'] = '\'+'.$prefix.'style+\'';
			$params['id'] = '\'+'.$prefix.'id+\'';

			foreach ( $element->params as $k => $v ) {
				$params[$k] = '\'+'.$prefix.$k.'+\'';
			}
			// $params['id'] = $base.'-'.mt_rand('000000','999999');
			$JSRender = $element->admin_render( $params );
			if ( !empty( $JSRender ) && is_array( $JSRender ) ) {
				if ( isset( $JSRender['args'] ) && is_array( $JSRender['args'] ) ) {
					foreach ( $JSRender['args'] as $key => $value ) {
						if ( ! is_array( $value ) ) {
							$js .= $prefix.$key.' = ( '. $prefix.$key.' == \'\' ) ? \'\' : \''.$value.'\';';
						} else {
							$i = 0;
							foreach ( $value as $k => $v ) {
								if ( ! empty( $k ) && $k === 'empty' ) {
									$k = '';
								}
								$con = '( ' . $prefix . $key . ' === \'' . $k . '\')';
								if ( $i === 0 ) {
									$if = 'if '. $con;
								} elseif ( $v === end( $value ) && $k === 'else' ) {
									$if = 'else ';
								} else {
									$if = 'else if ' . $con;
								}
								if ( is_array( $v ) && ! empty( $v )) {
									$c = '';
									foreach ( $v as $ke => $va ) {
										// $c .=  "\n\t".$prefix.$ke.'= \''.$va.'\'';
										// $c .=  $prefix.$ke.' = ( '. $prefix.$ke.' === \'\' ) ? \'\' : \''.$va.'\';';
										if ( empty( $k ) || 'else' === $k ) {
											$c .= $prefix.$ke.' = \''.$va.'\';';
										} else {
											$c .= $prefix.$ke.' = ( '. $prefix.$ke.' === \'\' ) ? \'\' : \''.$va.'\';';
										}
									}
									$js .= $if.'{'.$c.'} ';
								} else {
									$js .= $if.'{'.$prefix.$key.' = \''.$v.'\';}';
								}
								$i++;
							}

						}
						$params[$key] = '\'+'.$prefix.$key.'+\'';
					}
					$JSRender = $element->admin_render($params);
				}
				$JSRender = $JSRender['output'];
				return 'function($, p, fp) {'.$js.'this.dom_element = $(\''.self::hayyaBuildFixOutput($JSRender).'\');this.dom_content_element = this.dom_element;this.baseclass.prototype.render.apply(this, arguments);}';
			} else if ($JSRender) {
				return 'function($, p, fp) {'.$js.'this.dom_element = $(\''.self::hayyaBuildFixOutput($JSRender).'\');this.dom_content_element = this.dom_element;this.baseclass.prototype.render.apply(this, arguments);}';
			}
		}

		/**
		 * add JS code
		 *
		 * @since		3.0.0
		 * @param 		unknown $element
		 * @return 		string
		 */
		private function hayyaBuildJS( $element ) {
			return "\n<script type=\"text/javascript\">\n(function($) {\n\t var target_options = {'_self': '".__('Same window')."','_blank': '".__('New window')."',};\n\t function t(text) {\n\t\t if ('hayyabuild_t' in window) return window.hayyabuild_t(text);\n\t\t else return text;\n\t};\n\tvar hayyabuild_elements = [ \n\t\t" . $element . "\n\t ]; \n \twindow.hayyabuild_elements = hayyabuild_elements; \n })(window.jQuery); \n</script>\n";
		}

} // End HayyaModules Class
