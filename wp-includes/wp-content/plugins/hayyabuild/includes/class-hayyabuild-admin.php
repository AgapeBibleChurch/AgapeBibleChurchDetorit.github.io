<?php
/**
 *
 * The admin-specific functionality of the plugin.
 *
 * @since      	1.0.0
 * @package    	hayyabuild
 * @subpackage 	hayyabuild/includes
 * @author     	zintaThemes <>
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }


class HayyaAdmin extends HayyaBuild {

   	/**
   	  * The ID of this plugin.
   	  *
   	  * @since 	    1.0.0
   	  * @access 	private
   	  * @var        string 		$plugin_name 	name of this plugin.
   	  */
	private $plugin_name 	= null;

   	/**
   	  * The version of this plugin.
   	  *
   	  * @since 	    1.0.0
   	  * @access 	private
   	  * @var        string 		$version 		The current version of this plugin.
   	  */
   	private $version 		= null;

   	/**
   	  * Element ID.
   	  *
   	  * @since 	    1.0.0
   	  * @access 	private
   	  * @var        Intger 		$id		 		Element ID.
   	  */
   	private $id 			= null;

   	/**
   	  * The elements list.
   	  *
   	  * @since 	    1.0.0
   	  * @access 	private
   	  * @var        string 		$version 		The current version of this plugin.
   	  */
   	protected static $modules = array ();

   	/**
   	  *
   	  * @since   	1.0.0
   	  * @access  	protected
   	  * @var     	string		$type		Elements type.
   	  */
   	protected static $type = null;

   	/**
   	 *
   	 */
   	protected static $page = null;

    /**
      * Initialize the class and set its properties.
      *
      * @since 	    1.0.0
      * @param 	    string 		$plugin_name 	The name of this plugin.
      * @param 	    string 		$version 		The version of this plugin.
      */
    public function __construct() {
		if ( HayyaHelper::_get( 'id' ) ) $this->id = HayyaHelper::_get( 'id' );
		$this->get_current_page();
		$this->notices();
		require_once HAYYAB_PATH . 'admin/class-hayyabuild-view.php';
		if ( HayyaHelper::__is_main_pages() ) {
			if ( HayyaHelper::__is_build_pages() ) {
				$this->build();
			}
			$this->submit();
		}
	}

   	/**
   	 *
   	 * @package		HayyaBuild
   	 * @access		private
   	 * @since		3.0.0
   	 */
   	private function build() {
   		if ( HayyaHelper::__is_new_pages() ) {
   			if (self::$page == 'hayyabuild_addh') $type = 'header';
   			elseif (self::$page == 'hayyabuild_addc') $type = 'content';
   			elseif (self::$page == 'hayyabuild_addf') $type = 'footer';

   		} elseif ( HayyaHelper::_get( 'action' ) == 'edit' ) {
	   		$this->is_page_exists();
   			if ( HayyaHelper::_get( 'id' ) ) $this->id = HayyaHelper::_get( 'id' );
   			$results = $this->hb_getdata( $this->id );
   			if ( ! empty( $results->type ) && ( $results->type == 'header' || $results->type == 'content' || $results->type == 'footer' ) ) $type = $results->type;
   		}
   		self::$modules = new HayyaModules( $type );
   	}

   	/**
   	 *
   	 * @package		HayyaBuild
   	 * @access		private
   	 * @since		3.0.0
   	 */
   	private function get_current_page() {
   		return self::$page = HayyaHelper::_get('page');
   	}

   	/**
   	 *
   	 * @package		HayyaBuild
   	 * @access		public
   	 * @since		3.0.0
   	 */
   	public static function define_hooks() {
   		require_once HAYYAB_PATH. 'admin/class-hayyabuild-hooks.php';
   		new HayyaAdminHooks();
   	} // End defineHooks()

   	/**
   	 * Add header function
   	 *
   	 * @access      public
   	 * @since       1.0.0
   	 */
   	public function add_header($value='') {
   		$this->hayya_admin();
   	}

   	/**
   	 * Add footer function
   	 *
   	 * @access      public
   	 * @since       1.0.0
   	 */
   	public function add_footer($value='') {
   		$this->hayya_admin();
   	}

   	/**
   	 * Add Content function
   	 *
   	 * @access      public
   	 * @since       1.0.0
   	 */
   	public function add_content($value='') {
   		$this->hayya_admin();
   	}

   	/**
   	 *
   	 * load admin setting page
   	 *
   	 * @access 	    private
   	 * @since       1.0.0
   	 */
   	public function hayya_settings() {
   		require_once HAYYAB_PATH . '/admin/class-hayyabuild-settings.php';
   		new HayyaSettings();
   	}

   	/**
   	 *
   	 * Load admin help page
   	 *
   	 * @access 	    private
   	 * @since       1.0.0
   	 */
   	public function hayya_help() {
   		require_once HAYYAB_PATH . '/admin/class-hayyabuild-help.php';
   		new HayyaHelp();
   	}

   	/**
   	 *
   	 * @package		HayyaBuild
   	 * @access		private
   	 * @since		3.0.0
   	 */
   	private function is_page_exists() {
   		if (HayyaHelper::_get( 'action' ) == 'edit' && $this->id ) {
   			if ( ! $this->hb_getdata($this->id) ) {
   				HayyaHelper::$redirect['list'] = 'notfound';
   				add_action( 'admin_init', array('HayyaHelper', '__redirect'));
   			}
   		}
   	}

   	/**
   	 *
   	 * @package		HayyaBuild
   	 * @access		private
   	 * @since		3.0.0
   	 */
   	private function notices() {
   		if (HayyaHelper::_get('notfound'))
   			HayyaHelper::__notices( __('ERROR: This header, content or footer is not found', HAYYAB_BASENAME), 'error' );
   		else if (HayyaHelper::_get( 'updated' ) == '1')
   			HayyaHelper::__notices( __('SUCCESS: Database has been updated', HAYYAB_BASENAME), 'success' );
   	}

   	/**
   	 *
   	 * @package		HayyaBuild
   	 * @access		private
   	 * @since		3.0.0
   	 */
   	private function submit() {
   		if ( HayyaHelper::__is_build_pages() ) {
	   		if ( HayyaHelper::_post( 'submit' ) === 'submit' ) {
				add_action( 'admin_init', array($this, 'hb_save') );
			}
   		} else {
			$tpl_file = HAYYAB_PATH . 'includes' . DIRECTORY_SEPARATOR . 'class-hayyabuild-templates.php';
	   		if ( HayyaHelper::_post( 'tpl' ) && file_exists( $tpl_file )) {
	   			require_once $tpl_file;
	   			$tpl = new HayyaTemplates();
	   			add_action( 'admin_init', array( $tpl, 'template_save' ) );
	   		}

			$file = HAYYAB_PATH . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'class-hayyabuild-import.php';
			if ( file_exists( $file ) && ( HayyaHelper::_post( 'import_btn' ) || HayyaHelper::_get( 'export' ) === '1' ) ) {
				require_once $file;
				if ( HayyaHelper::_post( 'import_btn' ) && class_exists( 'hayyaBuildEmport' )) {
					hayyaBuildEmport::import();
				}
				else if ( HayyaHelper::_get( 'export' ) === '1' ) $this->export();
			}

	   		elseif ( HayyaHelper::_get( 'action' ) === 'deactivate' && $this->id ) $this->hb_deactivate();
	   		elseif ( HayyaHelper::_get( 'action' ) === 'delete' && $this->id ) $this->hb_delete();
	   		elseif ( HayyaHelper::_get( 'action' ) === 'publishe' && $this->id ) $this->hb_publishe();
   		}
   		return true;
   	}

    /**
      *
      * load admin views page
      *
      * @access 	public
      * @since 	    1.0.0
      */
	public function hayya_admin() {
   	    $type = '';
   		if ( HayyaHelper::__is_build_pages() ) {
   			$results = null;
   			if ( HayyaHelper::_get( 'action' ) == 'edit' ) {
				$results = $this->hb_getdata( $this->id );
			}
			if ( method_exists( self::$modules, 'elements_list' ) ) {
				$elements_list = self::$modules->elements_list();
			}

   			$result = array();
   			if ( isset($elements_list) && is_array($elements_list) ) {
   				foreach ( $elements_list as $path => $elements ) {
   					foreach ($elements as $value) $elements_group[$value['categories']][] = $value;
   				}
   			}

   			require_once HAYYAB_PATH . '/admin/class-hayyabuild-main.php';
   			new HayyaBuilder( $results, $elements_group );

   		} elseif (self::$page == 'hayyabuild') {

			require_once HAYYAB_PATH . '/admin/class-hayyabuild-list.php';
			if ( HayyaHelper::_get( 'section' ) == 'templates' ) {
				new HayyaList( 'templates' );
			} else {
       			$data = $this->hb_getdata();
       			new HayyaList( $data );
			}
   		}
   	}

   	/**
   	  *
   	  * @param 	unknown 	$param
   	  */
   	protected function hb_getdata( $id = NULL ) {
   	    global $wpdb;
   		if ( $id ) {

            $data = $wpdb->get_row( $wpdb->prepare(
                'SELECT `id`,`name`,`settings`,`pages`,`content`,`type`,`status`,`added_date`,`modified_date` FROM `'.$wpdb->prefix.HAYYAB_BASENAME.'` WHERE `id`="%d"',
                $id
            ) );

   			if ( $data ) return $data;
   			else return false;
   		} else {
   			$where = ' WHERE `status` = "published"';
   			if ( HayyaHelper::_get( 'list' ) == 'draft' ) $where = ' WHERE `status` = "draft"';
   			elseif ( HayyaHelper::_get( 'list' ) == 'deactivated' ) $where = ' WHERE `status` = "deactivated"';
   			elseif ( HayyaHelper::_get( 'list' ) == 'templates' ) $where = ' WHERE `status` = "template"';

			$data = $wpdb->get_results( 'SELECT `id`,`name`,`pages`,`settings`,`type`,`status`,`added_date`,`modified_date` FROM `'.$wpdb->prefix.HAYYAB_BASENAME.'`'.$where.' ORDER BY `id` DESC');

   			if ( $data ) return $data;
   			else return false;
   		}
   	}

   	/**
   	  * Save data to database
   	  *
   	  *
   	  * @param 	unknown 	$param
   	  */
   	public function hb_save() {
		$nonce = HayyaHelper::_post('_hbnonce');
		$hb_content = HayyaHelper::_post('hb_content');

		if ( ! empty($hb_content) && ! empty($nonce) && wp_verify_nonce($nonce, 'builder_form_nonce') ) {

	        global $wpdb;
	   		require_once HAYYAB_PATH . 'includes/class-hayyabuild-parser.php';
	   		$HayyaParser = new HayyaParser;

	        $data = $settings = array();
	        $settings= stripslashes_deep( HayyaHelper::_post('settings') );
	        $content = stripslashes_deep( $hb_content );
	        $content = $HayyaParser->cleanAdminHTML( $content );
	        $settings['csscode']= HayyaHelper::__slashes( $settings['csscode'], 'add' );

			$data = array();
	   		$data['name']			= sanitize_text_field( HayyaHelper::_post('name') );
	   		$data['content'] 		= HayyaHelper::__slashes( $content, 'add' );
	        $data['clean_content'] 	= $content;
	   		$data['settings'] 		= maybe_serialize( $settings );
	   		$data['pages'] 			= maybe_serialize( HayyaHelper::_post('pages') );
	   		$data['modified_date'] 	= date( "Y-m-d H:i:s" );

	   		if ( ! empty( $this->id ) ) {
	   			$status = HayyaHelper::_post( 'status' );
	   			if ( $status == 'draft' && HayyaHelper::_post( 'publish' ) ) $status = 'published';
	   			$data['status'] = $status;
	   		} else {
	   			if (self::$page == 'hayyabuild_addh') $type = 'header';
	   			elseif (self::$page == 'hayyabuild_addc') $type = 'content';
	   			else $type = 'footer';

	   			$status = ( HayyaHelper::_post( 'publish' ) ) ? 'published' : 'draft';
	   			$data['type'] = $type;
	   			$data['status'] = $status;
	   			$data['added_date'] = date( "Y-m-d H:i:s" );
	   		}

	   		if ( has_filter('before_hayyabuild_save') ) {
		   		$args = array(
	                'settings' => $data['settings'],
	                'content' => $data['clean_content'],
	                'pages' => $data['pages']
	            );

				$hayyabuild_save = apply_filters( 'before_hayyabuild_save', $args );
				if (!empty($hayyabuild_save)) {
			   		$data['settings']        = $hayyabuild_save['settings'];
			   		$data['clean_content']   = $hayyabuild_save['content'];
			   		$data['pages']           = $hayyabuild_save['pages'];
				}
	   		}

	   		$data['clean_content'] 	= HayyaHelper::__slashes( $HayyaParser->cleanPublicHTML( $data['clean_content'] ), 'add' );

	   		if ( ! empty( $this->id ) ) {
	   			if ( $wpdb->update( $wpdb->prefix . HAYYAB_BASENAME, $data, array( 'id' =>$this->id ) ) ) {
	   				$pages = HayyaHelper::_post('pages');
	   				$type  = HayyaHelper::_post( 'type' ) ;
	   				if ( $this->hb_map( $pages, $type ) ) {
	   					HayyaHelper::__notices( __('SUCCESS: Database has been updated', HAYYAB_BASENAME), 'success' );
	   				}
	   			} else HayyaHelper::__notices( __('ERROR01: Someting happen, Can’t update database', HAYYAB_BASENAME), 'error' );
	   		} else {
	   			if ( $wpdb->insert( $wpdb->prefix . HAYYAB_BASENAME, $data, '' ) ) {
	   				$this->id = $wpdb->insert_id;
	   				if ( $status === 'published' ) {
	   					$pages = HayyaHelper::_post('pages');
		   				$this->hb_map($pages, $type);
	   				}
	   				HayyaHelper::$redirect['id'] = $this->id;
	   				add_action( 'admin_init', array('HayyaHelper', '__redirect'), 11);
	   			} else HayyaHelper::__notices( __('ERROR02: Someting happen, Can’t update database', HAYYAB_BASENAME), 'error' );
	   		}
		}
   	}

    /**
      *
      * @param 	unknown
      */
    private function hb_publishe() {
		$nonce = HayyaHelper::_get('_hbnonce');

		if (
			! empty($nonce) &&
			( wp_verify_nonce($nonce, 'publishe_deactivated_url') || wp_verify_nonce($nonce, 'publish_url') )
		) {
	   	    global $wpdb;

	   		$results 	= $this->hb_getdata( $this->id );
			$pages_list = preg_replace_callback( '/s:([0-9]+):\"(.*?)\";/', function ($matches) {
				return "s:".strlen($matches[2]).':"'.$matches[2].'";';
			}, $results->pages );
			$pages_list =  maybe_unserialize( $pages_list );
	   		$type = $results->type;
	   		$data = array( 'status' => 'published' );
	   		if ( $wpdb->update( $wpdb->prefix . HAYYAB_BASENAME, $data, array( 'id' => $this->id ) ) ) {
	   			if ( $this->hb_map( $pages_list, $type ) ) {
	   			    HayyaHelper::__notices( __('SUCCESS: Database has been updated', HAYYAB_BASENAME), 'success' );
	   			}
	   		} else {
	   			HayyaHelper::__notices( __('ERROR03: Someting happen, Can’t update database', HAYYAB_BASENAME), 'error' );
	   		}
		}
   	}

   	/**
   	 *
   	 * @param unknown $pages
   	 * @param unknown $type
   	 * @return boolean
   	 */
   	private function hb_map( $pages, $type ) {
   	    global $wpdb;
   	    $return = true;
   		if ( $this->id ) $id = $this->id;
   	    else $id = $wpdb->insert_id;
   		$wpdb->delete( $wpdb->prefix.HAYYAB_BASENAME.'_map', array('hb_id' => $id ) );
   		if ( is_array( $pages ) ) {
   			foreach ( $pages as $key => $page ) {
   			    $data = array( 'object_id' => $page, 'hb_id' => $id, 'hb_type' => $type );
   				if ( $wpdb->insert( $wpdb->prefix.HAYYAB_BASENAME.'_map', $data , array('%s', '%d', '%s') ) ) continue;
   				else $return = false;
   			}
   		}
   		return $return;
   	}

   	/**
   	  *
   	  * @param 	unknown
   	  */
   	private function hb_deactivate() {
		$nonce = HayyaHelper::_get('_hbnonce');
		if ( ! empty($nonce) && ( wp_verify_nonce($nonce, 'deactivate_url') || wp_verify_nonce($nonce, 'deactivate_draft_url') ) ) {
			global $wpdb;
			$data = array( 'status' => 'deactivated' );
			if ( $wpdb->update( $wpdb->prefix . HAYYAB_BASENAME, $data, array( 'ID' => $this->id ) ) ) {
				$wpdb->delete( $wpdb->prefix . HAYYAB_BASENAME . '_map', array('hb_id' => $this->id ) );
				HayyaHelper::__notices( __('SUCCESS: Item has been successfully deactivated', HAYYAB_BASENAME), 'success' );
			} else HayyaHelper::__notices( __('ERROR04: Someting happen, Can’t deactivated this item.', HAYYAB_BASENAME), 'error' );
		}
   	}

   	/**
   	 *
   	 * @param 	unknown
   	 */
   	private function hb_delete() {
		$nonce = HayyaHelper::_get('_hbnonce');
		if ( ! empty($nonce) && ( wp_verify_nonce($nonce, 'delete_url') || wp_verify_nonce($nonce, 'delete_template_url') ) ) {
	   	    global $wpdb;
	   		if ($wpdb->delete( $wpdb->prefix . HAYYAB_BASENAME, array('id' => $this->id ) ) ) {
	   			$wpdb->delete( $wpdb->prefix . HAYYAB_BASENAME . '_map', array('hb_id' => $this->id ) );
	   			HayyaHelper::__notices( __('SUCCESS: Item has been successfully deleted', HAYYAB_BASENAME), 'success' );
	   		} else {
	   			HayyaHelper::__notices( __('ERROR05: Someting happen, Can’t delete this item.', HAYYAB_BASENAME), 'error' );
	   		}
		}
   	}

   	/**
   	 *
   	 * @param unknown $param
   	 */
   	public function export() {
		$nonce = HayyaHelper::_get('_hbnonce');
		if ( ! empty($nonce) && wp_verify_nonce($nonce, 'export_url') ) {
		    $data = $this->hb_getdata( $this->id );
		    unset($data->id, $data->added_date, $data->modified_date, $data->status, $data->clean_content);
		    $data->status = 'draft';
		    $name = str_replace(" ", "_", $data->name);
		    $site_url = get_site_url();
	        // $data->content =  HayyaHelper::__slashes( $data->content, 'strip' );
		    $json_name = HAYYAB_NAME."-".$name."-".date("m-d-Y"); // Namming the filename will be generated.
	        foreach ($data as $key => $value) {
	            if ( $key === 'settings') {
	                $settings = maybe_unserialize($value);
	                foreach ($settings as $k => $v) {
	                    if (!is_array($v)) {
	                        $settings[$k] = str_replace($site_url, '<--site_url-->', $v);
	                    }
	                }
	                $data->settings = maybe_serialize($settings);
	            } else if ( $key === 'content') {
	                $data->content = HayyaHelper::__slashes($value, 'strip');
	                $data->content = str_replace($site_url, '<--site_url-->', $data->content);
	            }
	        }
		    $json_file = json_encode($data); // Encode data into json data
		    header("Content-Type: text/json; charset=utf8" . get_option( 'blog_charset'));
		    header("Content-Disposition: attachment; filename=$json_name.json");
		    echo $json_file;
		    exit();
		}
    }
} // end of class HayyaAdmin
