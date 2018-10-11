<?php
/*

Plugin Name: Church Admin
Plugin URI: http://www.churchadminplugin.com/
Description: A  admin system with address book, small groups, rotas, bulk email  and sms
Version: 1.4590
Author: Andy Moyle
Text Domain: church-admin


Author URI:http://www.themoyles.co.uk
License:
----------------------------------------


Copyright (C) 2010-2016 Andy Moyle



    This program is free software: you can redistribute it and/or modify

    it under the terms of the GNU General Public License as published by

    the Free Software Foundation, either version 3 of the License, or

    (at your option) any later version.



    This program is distributed in the hope that it will be useful,

    but WITHOUT ANY WARRANTY; without even the implied warranty of

    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the

    GNU General Public License for more details.



	http://www.gnu.org/licenses/

----------------------------------------
  ___ _   _             _ _         _                 _         _
 |_ _| |_( )___    __ _| | |   __ _| |__   ___  _   _| |_      | | ___  ___ _   _ ___
  | || __|// __|  / _` | | |  / _` | '_ \ / _ \| | | | __|  _  | |/ _ \/ __| | | / __|
  | || |_  \__ \ | (_| | | | | (_| | |_) | (_) | |_| | |_  | |_| |  __/\__ \ |_| \__ \
 |___|\__| |___/  \__,_|_|_|  \__,_|_.__/ \___/ \__,_|\__|  \___/ \___||___/\__,_|___/


*/
if ( ! defined( 'ABSPATH' ) ) exit('You need Jesus!'); // Exit if accessed directly
define('CA_DEBUG',FALSE);

	$church_admin_version = '1.4590';
	$people_type=get_option('church_admin_people_type');
	$level=get_option('church_admin_levels');
	if(!empty($_POST['save-ca-modules'])){require_once(plugin_dir_path(__FILE__).'includes/settings.php');church_admin_modules();}


/* initialise plugin */

add_action( 'plugins_loaded', 'church_admin_initialise' );
function church_admin_initialise() {
	global $level,$church_admin_version,$wpdb,$current_user,$church_admin_prayer_request_success;

	define('CA_PATH',plugin_dir_path( __FILE__));
	wp_get_current_user();
	church_admin_constants();//setup constants first
	//Version Number
	define('OLD_CHURCH_ADMIN_VERSION',get_option('church_admin_version'));
	if(OLD_CHURCH_ADMIN_VERSION!= $church_admin_version)
	{
		church_admin_backup();
		require_once(plugin_dir_path( __FILE__) .'/includes/install.php');
		church_admin_install();
	}

	$rota_order=ca_rota_order();
	$member_type=church_admin_member_type_array();
	if(!empty($_GET['ca_refresh']))
	{
		delete_option('church-admin-directory-output');
	}
	//handle unsubscribe link from email
	if(!empty($_GET['ca_unsub']))
	{
			$details=$wpdb->get_row('SELECT * FROM '.CA_PEO_TBL.' WHERE md5(people_id)="'.esc_sql($_GET['ca_unsub']).'"');
			$wpdb->query('UPDATE '.CA_PEO_TBL.' SET email_send=0 WHERE md5(people_id)="'.esc_sql($_GET['ca_unsub']).'"');
			require_once(plugin_dir_path(__FILE__).'includes/unsubscribe.php');
			exit();
	}
	//handle re-subscribe
	if(!empty($_GET['ca_sub']))
	{
			$details=$wpdb->get_row('SELECT * FROM '.CA_PEO_TBL.' WHERE md5(people_id)="'.esc_sql($_GET['ca_sub']).'"');
			$wpdb->query('UPDATE '.CA_PEO_TBL.' SET email_send=1 WHERE md5(people_id)="'.esc_sql($_GET['ca_sub']).'"');
			require_once(plugin_dir_path(__FILE__).'includes/resubscribe.php');
			exit();
	}
	//handle gdpr link
	if(!empty($_GET['confirm']))
	{
		$details=explode("/",$_GET['confirm']);
		$household_id=$wpdb->get_var('SELECT household_id FROM '.CA_PEO_TBL.' WHERE last_name LIKE "'.esc_sql($details[0]).'" AND people_id="'.intval($details[1]).'"');
		if($household_id)
		{
			$wpdb->query('UPDATE '.CA_PEO_TBL.' SET email_send=1, sms_send=1, gdpr_reason="'.esc_sql(__('User confirmed on website','church-admin').' '.date(get_option('date_format'))).'" WHERE household_id="'.intval($household_id).'"' );
			$wpdb->query('UPDATE '.CA_HOU_TBL.' SET privacy=0 WHERE household_id="'.intval($household_id).'"' );
		}
		require_once(plugin_dir_path(__FILE__).'includes/confirmed.php');
			exit();
	}
	//temp fix fo bug in app
	if(isset($_GET['action'])&&$_GET['action']=='ca_classes'){require_once(plugin_dir_path(__FILE__).'app/app-admin.php');ca_classes();exit();}

	if(isset($_GET['action'])&&$_GET['action']=='auto_backup'){require_once(plugin_dir_path(__FILE__).'includes/pdf_creator.php');church_admin_backup_pdf();exit();}
	if(isset($_GET['action'])&&$_GET['action']=="delete_backup"){check_admin_referer('delete_backup');church_admin_delete_backup();}
	if(isset($_GET['action'])&&$_GET['action']=="refresh_backup")	{check_admin_referer('refresh_backup');church_admin_backup();}
	//remove cron auto email rotas
	if(isset($_GET['action'])&&$_GET['action']=="delete-cron")
	{
		check_admin_referer('delete-cron');
		require_once(plugin_dir_path(__FILE__).'includes/rota.new.php');
		church_admin_delete_cron($_GET['ts'],$_GET['key']);
		$url=admin_url().'admin.php?page=church_admin%2Findex.php&action=rota&tab=rota';
		wp_redirect( $url );
	}
	if(!empty($_POST['ind_att_csv'])){require_once(plugin_dir_path(__FILE__).'includes/individual_attendance.php');church_admin_output_ind_att_csv();exit();}
	load_plugin_textdomain( 'church-admin', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );


    if(empty($level['Directory']))$level['Directory']='administrator';
    if(empty($level['Small Groups']))$level['Small Groups']='administrator';
    if(empty($level['Rota']))$level['Rota']='administrator';
    if(empty($level['Funnel'])) $level['Funnel']='administrator';
    if(empty($level['Bulk Email']))$level['Bulk Email']='administrator';
    if(empty($level['Sermons']))$level['Sermons']='administrator';
	if(empty($level['Bulk SMS']))$level['Bulk SMS']='administrator';
    if(empty($level['Calendar']))$level['Calendar']='administrator';
    if(empty($level['Attendance']))$level['Attendance']='administrator';
    if(empty($level['Member Type']))$level['Member Type']='administrator';
    if(empty($level['Service']))$level['Service']='administrator';
	if(empty($level['Prayer Chain']))$level['Prayer Chain']='administrator';
	if(empty($level['Sessions']))$level['Sessions']='administrator';
	if(empty($level['App']))$level['App']='administrator';
		if(empty($level['Prayer Requests']))$level['Prayer Requests']='administrator';
    update_option('church_admin_levels',$level);
    if(!empty($_POST['one_site']))$wpdb->query('UPDATE '.CA_PEO_TBL.' SET site_id="'.intval($_POST['site_id']).'"');
    //church admin app initialisation

	if(!empty($_GET['ca-app']))
	{
		require_once(plugin_dir_path(__FILE__).'app/app-admin.php');
		switch($_GET['ca-app'])
		{
			case'latest_media': header("Content-Type: application/json");echo church_admin_json_latest_media();exit();break;

		}
	}

	//copy rota and then redirect
	 if(!empty($_GET['page'])&&($_GET['page']=='church_admin/index.php')&&!empty($_GET['action'])&& $_GET['action']=='copy_rota_data' &&church_admin_level_check('Rota'))
	{
		require_once(plugin_dir_path(__FILE__).'includes/rota.new.php');
		church_admin_copy_rota($_GET['rotaDate1'],$_GET['rotaDate2'], $_GET['service_id'],$_GET['mtg_type']);
		$url=admin_url().'admin.php?page=church_admin%2Findex.php&action=rota&tab=rota&message=copied';
		wp_redirect( $url );
		exit;
	}
		//reset version
	 if(!empty($_GET['page'])&&($_GET['page']=='church_admin/index.php')&&!empty($_GET['action'])&& $_GET['action']=='reset_version')
	{
		check_admin_referer('reset_version');

		delete_option("church_admin_version");
		$url=admin_url().'admin.php?page=church_admin%2Findex.php&message=Church+Admin+Version+Reset';
		wp_redirect( $url );
		exit;
	}
	//reset version
	//upgrade rota for 1.095
	 if(!empty($_GET['page'])&&($_GET['page']=='church_admin/index.php')&&!empty($_GET['action'])&& $_GET['action']=='upgrade_rota')
	{
		check_admin_referer('upgrade_rota');

		delete_option("church_admin_version");
		$wpdb->query('TRUNCATE TABLE '.CA_ROTA_TBL);
		$url=admin_url().'admin.php?page=church_admin%2Findex.php&message=Rota+Table+Reset';
		wp_redirect( $url );
		exit;
	}
		//upgrade rota for 1.095
	 if(!empty($_GET['page'])&&($_GET['page']=='church_admin/index.php')&&!empty($_GET['action'])&& $_GET['action']=='clear_debug')
	{
		check_admin_referer('clear_debug');

		$upload_dir = wp_upload_dir();
		$debug_path=$upload_dir['basedir'].'/church-admin-cache/debug_log.php';
		if(file_exists($debug_path))unlink($debug_path);
		$url=admin_url().'admin.php?page=church_admin%2Findex.php&action=settings&tab=settings&message=Church+Admin+Debug+Log+has+been+deleted.';
		wp_redirect( $url );
		exit;
	}
    //save the church admin note before any display happens

	if(!empty($_POST['save-ca-comment']))
 	{
 		if(defined('CA_DEBUG'))church_admin_debug('******************************'."\r\n Save Comment ".date('Y-m-d H:i:s')."\r\n");
 		$sqlsafe=array();

 		if(!empty($_POST['parent_id']))$parent_id=intval($_POST['parent_id']);
 		if(empty($parent_id))$parent_id=null;
 		foreach($_POST AS $key=>$value)$sqlsafe[$key]=esc_sql(stripslashes($value));
 		if(!empty($_POST['comment_id']))
 		{
 			$sql='UPDATE '.CA_COM_TBL.' SET comment="'.$sqlsafe['comment'].'",comment_type="'.$sqlsafe['comment_type'].'",parent_id="'.$parent_id.'",author_id="'.intval($current_user->ID).'",timestamp="'.date('Y-m-d h:i:s').'" comment_id="'.intval($sqlsafe['comment_id']).'"';
 		}
 		else
 		{

 			$sql='INSERT INTO '.CA_COM_TBL.' (comment,comment_type,parent_id,author_id,timestamp,ID)VALUES("'.$sqlsafe['comment'].'","'.$sqlsafe['comment_type'].'","'.$parent_id.'","'.intval($current_user->ID).'","'.date('Y-m-d h:i:s').'","'.intval($sqlsafe['ID']).'")';
 		}
 		if(defined('CA_DEBUG'))church_admin_debug('******************************'."\r\n $sql \r\n");
 		$wpdb->query($sql);
 		if(empty($sqlsafe['comment_id']))$sqlsafe['comment_id']=$wpdb->insert_id;

 		$comment=$wpdb->get_row('SELECT * FROM '.CA_COM_TBL.' WHERE comment_id="'.intval($sqlsafe['comment_id']).'"');

 	}

}

require_once(plugin_dir_path(__FILE__) .'includes/functions.php');
require_once(plugin_dir_path(__FILE__).'includes/admin.php');
require_once(plugin_dir_path(__FILE__).'app/app-admin.php');
require_once(plugin_dir_path(__FILE__).'includes/custom_fields.php');
add_action( 'delete_user', 'church_admin_delete_user' );//make sure user account disconnected from directory
add_action( 'admin_notices', 'church_admin_app_admin_notice' );

function church_admin_delete_user($user_id)
{
	global $wpdb;
	$wpdb->query('UPDATE '.CA_PEO_TBL.' SET user_id="NULL" WHERE user_id="'.intval($user_id).'"');
}
//add_filter('wp_mail_content_type',create_function('', 'return "text/html"; '));
add_action('activated_plugin','church_admin_save_error');
function church_admin_save_error(){
    update_option('church_admin_plugin_error',  ob_get_contents());
}
add_action('load-church-admin', 'church_admin_add_screen_meta_boxes');

//update_option('church_admin_roles',array(2=>'Elder',1=>'Small group Leader'));
$oldroles=get_option('church_admin_roles');
if(!empty($oldroles))
{
    update_option('church_admin_departments',$oldroles);
    delete_option('church_admin_roles');
}


function church_admin_constants()
{
/**
 *
 * Sets up constants for plugin
 *
 * @author  Andy Moyle
 * @param    null
 * @return
 * @version  0.1
 *
 */
    global $wpdb;

    //define DB
define('CA_ATT_TBL',$wpdb->prefix.'church_admin_attendance');
define('CA_BRP_TBL',$wpdb->prefix.'church_admin_brplan');
define('CA_APP_TBL',$wpdb->prefix.'church_admin_app');
define('CA_CP_TBL',$wpdb->prefix.'church_admin_safeguarding');
define ('CA_BIB_TBL',$wpdb->prefix.'church_admin_bible_books');
define ('CA_CAT_TBL',$wpdb->prefix.'church_admin_calendar_category');
define('CA_CLA_TBL',$wpdb->prefix.'church_admin_classes');
define('CA_COM_TBL',$wpdb->prefix.'church_admin_comments');
define('CA_CUST_TBL',$wpdb->prefix.'church_admin_custom_fields');
define('CA_DATE_TBL',$wpdb->prefix.'church_admin_calendar_date');
define('CA_EVE_TBL',$wpdb->prefix.'church_admin_events');
define('CA_BOO_TBL',$wpdb->prefix.'church_admin_bookings');
define('CA_TIK_TBL',$wpdb->prefix.'church_admin_tickets');
define ('CA_FIL_TBL',$wpdb->prefix.'church_admin_sermon_files');
define ('CA_KJV_TBL',$wpdb->prefix.'church_admin_kjv');
define('CA_EMA_TBL',$wpdb->prefix.'church_admin_email');
define('CA_EBU_TBL',$wpdb->prefix.'church_admin_email_build');

define ('CA_FAC_TBL',$wpdb->prefix.'church_admin_facilities');
define('CA_FUN_TBL',$wpdb->prefix.'church_admin_funnels');
define('CA_FP_TBL',$wpdb->prefix.'church_admin_follow_up');
define('CA_HOU_TBL',$wpdb->prefix.'church_admin_household');
define('CA_HOP_TBL',$wpdb->prefix.'church_admin_hope_team');
define('CA_IND_TBL',$wpdb->prefix.'church_admin_individual_attendance');
define('CA_KID_TBL',$wpdb->prefix.'church_admin_kidswork');
define('CA_MET_TBL',$wpdb->prefix.'church_admin_people_meta');
define('CA_METRICS_TBL',$wpdb->prefix.'church_admin_metrics');
define('CA_METRICS_META_TBL',$wpdb->prefix.'church_admin_metrics_meta');
define('CA_MTY_TBL',$wpdb->prefix.'church_admin_member_types');
define('CA_MIN_TBL',$wpdb->prefix.'church_admin_ministries');
define('CA_PEO_TBL',$wpdb->prefix.'church_admin_people');
define('CA_ROTA_TBL',$wpdb->prefix.'church_admin_new_rota');
define('CA_ROT_TBL',$wpdb->prefix.'church_admin_rotas');
define('CA_RST_TBL',$wpdb->prefix.'church_admin_rota_settings');
define('CA_SMG_TBL',$wpdb->prefix.'church_admin_smallgroup');
define('CA_SER_TBL',$wpdb->prefix.'church_admin_services');
define('CA_SES_TBL',$wpdb->prefix.'church_admin_session');
define('CA_SMET_TBL',$wpdb->prefix.'church_admin_session_meta');
define('CA_SIT_TBL',$wpdb->prefix.'church_admin_sites');
define ('CA_SERM_TBL',$wpdb->prefix.'church_admin_sermon_series');


//define DB
define('OLD_CHURCH_ADMIN_EMAIL_CACHE',WP_PLUGIN_DIR.'/church-admin-cache/');
define('OLD_CHURCH_ADMIN_EMAIL_CACHE_URL',WP_PLUGIN_URL.'/church-admin-cache/');


define('CA_POD_URL',content_url().'/uploads/sermons/');
$upload_dir = wp_upload_dir();
if(!is_dir( $upload_dir['basedir'].'/sermons/'))
    {
        $old = umask(0);
        mkdir( $upload_dir['basedir'].'/sermons/');
        chmod($upload_dir['basedir'].'/sermons/', 0755);
        umask($old);
        $index="<?php\r\n//nothing is good;\r\n?>";
        $fp = fopen($upload_dir['basedir'].'/sermons/'.'index.php', 'w');
        fwrite($fp, $index);
        fclose($fp);
    }
if(!is_dir($upload_dir['basedir'].'/church-admin-cache/'))
{
        $old = umask(0);
		 mkdir($upload_dir['basedir'].'/church-admin-cache/');
        chmod($upload_dir['basedir'].'/church-admin-cache/', 0755);
        umask($old);
        $index="<?php\r\n//nothing is good;\r\n?>";
        $fp = fopen($upload_dir['basedir'].'/church-admin-cache/'.'index.php', 'w');
        fwrite($fp, $index);
        fclose($fp);
}
if(is_dir(OLD_CHURCH_ADMIN_EMAIL_CACHE))
{

    //grab files
    $files=scandir(OLD_CHURCH_ADMIN_EMAIL_CACHE);
    if(!empty($files))
    {
	foreach($files AS $file)
	{
	    if ($file!= "." && $file!= "..")
	    {
	        //work through files, but don't delete as old emails have link to old uris
	        $success=copy(OLD_CHURCH_ADMIN_EMAIL_CACHE.$file,plugin_dir_path( dirname(__FILE__)).'church-admin-cache/'.$file);
	        if($success)
	        {

	        	unlink(OLD_CHURCH_ADMIN_EMAIL_CACHE.$file);
	        }
	    }
	}
	//create htaccess redirect for cached emails

	$htaccess="\r\n RedirectPermanent /wp-content/plugins/church-admin-cache/ /wp-content/uploads/church-admin-cache/\r\n";
	// Let's make sure the file exists and is writable first.
	$htaccess_done=get_option('church_admin_htaccess');
	if (is_writable(ABSPATH.'.htaccess')&&empty($htaccess_done))
	{

	    if (!$handle = fopen(ABSPATH.'.htaccess', 'a')) {echo __('Cannot open file','church-admin').'  ('.ABSPATH.'.htaccess)';}
	    elseif(fwrite($handle, $htaccess) === FALSE) {echo __('Cannot write to file','church-admin').' ('.ABSPATH.'.htaccess)';}
	    else{fclose($handle);}
	    update_option('church_admin_htaccess','1');
	}
    }

}

//this needs to happen very early in page call!
 if(isset($_GET['ca_download'])){church_admin_download($_GET['ca_download']);exit();}
}//end constants


 /**
 *
 * Add new household to admin toolbar
 *
 * @author  Andy Moyle
 * @param    null
 * @return   Array, key is order
 * @version  0.1
 *
 */
function church_admin_menu_item ($wp_admin_bar) {

    $args = array (
            'id'        => 'household',
            'title'     => __('Household','church-admin'),
            'href'      => wp_nonce_url('admin.php?page=church_admin/index.php&amp;tab=address&action=church_admin_new_household','new_household'),
            'parent'    => 'new-content'
    );

  if(church_admin_level_check('Directory'))  $wp_admin_bar->add_node( $args );
}

add_action('admin_bar_menu', 'church_admin_menu_item',71);



function ca_rota_order()
{
 /**
 *
 * Retrieves rota items in order
 *
 * @author  Andy Moyle
 * @param    null
 * @return   Array, key is order
 * @version  0.1
 *
 */
    global $wpdb;
    //rota_order
    $results=$wpdb->get_results('SELECT * FROM '.CA_RST_TBL.' ORDER BY rota_order ASC');
    if($results)
    {
        $rota_order=array();
        foreach($results AS $row)
        {
            $rota_order[]=$row->rota_id;
        }
    return $rota_order;
    }

}
/******************************************************************************************************************************
*
* For prayer request, if made private in settings we want to show the login form as the loop starts and then empty the content
*
******************************************************************************************************************************/
add_filter('loop_start','church_admin_prayer_login');
function church_admin_prayer_login()
{
	if(is_post_type_archive('prayer-requests'))
	{
			$private=get_option('church-admin-private-prayer-requests');
			if ( !is_user_logged_in() && $private) {
					echo '<h2>'.__('Please login to view prayer requests','church-admin').'</h2>'.wp_login_form(array('echo'=>FALSE));

			}
	}

}
add_filter( 'the_content', 'church_admin_prayer_requests_login_only' );
function church_admin_prayer_requests_login_only( $content ) {
    global $post;

    if ( $post->post_type == 'prayer-requests' ) {
    		$private=get_option('church-admin-private-prayer-requests');
        if ( !is_user_logged_in() && $private) {
            $content = '<p>'.__('You need to login above, to view prayer requests','church-admin').'</p>';
        }
    }

    return $content;
}
/******************************************************************************************************************************
*
* Show a submit prayer requests form at the top of the archive
*
******************************************************************************************************************************/
add_action('loop_start', 'church_admin_draft_prayer_request');

function church_admin_draft_prayer_request($content)
{
    global $wpdb,$church_admin_prayer_request_success;

		if(is_post_type_archive('prayer-requests'))
    {
			$private=get_option('church-admin-private-prayer-requests');
			//only show form if not private or logged in
			if (!$private ||(is_user_logged_in() && $private))
			{
				$out='';

      	if(empty($_POST['save_prayer_request'])&&empty($_POST['non_spammer'])||!wp_verify_nonce($_POST['non_spammer'],'prayer-request'))
      	{
					$out.='<h3>'.__('Submit a prayer request','church-admin').'</h3>';
					$message=get_option('church_admin_prayer_request_message');
					if(!empty($message))$out.='<p>'. esc_html($message).'</p>';
        	$out.='<form action="" method="POST">';
        	$out.='<table class="form-table"><tbody>';
        	$out.='<tr><th scope="row">'.__('Title','church-admin').'</th><td><input type="text" name="request_title"></td></tr>';
        	$out.='<tr><th scope="row">'.__('Prayer request','church-admin').'</th><td><textarea name="request_content"></textarea></td></tr>';
					$out.='<tr id="spam-proof">&nbsp;</td></tr>';
					$out.='<tr><td cellspacing=2><input type="hidden" value="TRUE" name="save_prayer_request"/><input type="submit" value="'.__('Save','church-admin').'"/></td></tr></table>';

					$out.='</form>';
					$nonce=wp_create_nonce('prayer-request');
					$out.='<script>jQuery(document).ready(function($) {var content="<th scope=\"row\">'.__('Check box if not a spammer','church-admin').'</th><td><input type=\"checkbox\" name=\"non_spammer\" value=\"'.$nonce.'\"/></td></tr>";$("#spam-proof").html(content);});</script>';
				}
				else{$out=$church_admin_prayer_request_success;}
      	echo $out;
			}
		}

}
/****************************************************************************
*
*	From 1.2800 register front end scripts early then enqueue on shortcode process
*
*****************************************************************************/
add_action( 'wp_enqueue_scripts', 'church_admin_register_frontend_scripts' );

function church_admin_register_frontend_scripts() {
	wp_register_script('church-admin-calendar-script',plugins_url( '/', __FILE__ ) . 'includes/calendar.js',array( 'jquery' ),FALSE, TRUE);
	wp_register_script( 'jquery-ui-datepicker','','',NULL );
	wp_enqueue_style( 'jquery.ui.theme', plugins_url('css/jquery-ui-1.8.21.custom.css',__FILE__ ) ,'',NULL);
	$ajax_nonce = wp_create_nonce("church_admin_mp3_play");

	wp_register_script('ca_podcast_audio',plugins_url('church-admin/includes/audio.min.js',dirname(__FILE__) ) , array( 'jquery' ) ,FALSE, TRUE);
	wp_localize_script( 'ca_podcast_audio', 'ChurchAdminAjax1', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	wp_register_script('ca_podcast_audio_use',plugins_url('church-admin/includes/audio.use.js',dirname(__FILE__) ), array( 'jquery' ) ,FALSE, TRUE);
	wp_localize_script( 'ca_podcast_audio_use', 'ChurchAdminAjax', array('security'=>$ajax_nonce, 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	wp_register_script( 'jquery-ui-datepicker','','',NULL );
	wp_enqueue_style( 'jquery.ui.theme', plugins_url('css/jquery-ui-1.8.21.custom.css',__FILE__ ) ,'',NULL);
	wp_register_script('church_admin_form_clone',plugins_url('church-admin/includes/jquery-formfields.js',dirname(__FILE__) ), array( 'jquery' ) ,FALSE, TRUE);
	//fix issue caused by some "premium" themes, which call google maps w/o key on every admin page. D'uh!
 	wp_dequeue_script('avia-google-maps-api');
	//now enqueue google map api with the key
	$src = 'https://maps.googleapis.com/maps/api/js';
	$key='?key='.get_option('church_admin_google_api_key');
	wp_register_script( 'church_admin_google_maps_api',$src.$key, array() ,FALSE, TRUE);
	wp_register_script('church_admin_map', plugins_url('church-admin/includes/google_maps.js',dirname(__FILE__) ), array( 'jquery' ) ,FALSE, TRUE);
	wp_register_script('church_admin_map_script', plugins_url('church-admin/includes/maps.js',dirname(__FILE__) ), array( 'jquery' ) ,FALSE, TRUE);
	wp_register_script('church_admin_frontend_sg_map_script', plugins_url('church-admin/includes/smallgroup_maps.js',dirname(__FILE__) ), array( 'jquery' ) ,FALSE, TRUE);
	wp_register_script('church_admin_sg_map', plugins_url('church-admin/includes/admin_sg_maps.js',dirname(__FILE__) ), array( 'jquery' ) ,FALSE, TRUE);
//google graph needs to be called early and in header, didn't like being registered and then enqueued later
	wp_enqueue_script('church_admin_google_graph_api','https://www.google.com/jsapi', array( 'jquery' ) ,FALSE, FALSE);
}

add_action('wp_head','church_admin_ajaxurl');
function church_admin_ajaxurl()
{
	$ajax_nonce = wp_create_nonce("church_admin_mp3_play");
	?>
	<script type="text/javascript">
		var ajaxurl = '<?php echo admin_url('admin-ajax.php'); ?>';
		var security= '<?php echo $ajax_nonce; ?>';
	</script>
	<?php
}
add_action('wp_enqueue_scripts', 'church_admin_init');
add_action('admin_enqueue_scripts', 'church_admin_init',9999);//adding withlow priority to be last to call google maps api
/**
 *
 * Initialises js scripts and css
 *
 * @author  Andy Moyle
 * @param    null
 * @return
 * @version  0.1
 *
 */
function church_admin_init()
{

    //This function add scripts as needed
    	wp_enqueue_style( 'dashicons' );
		wp_enqueue_script('common','','',NULL);
		wp_enqueue_script('wp-lists','','',NULL);
		wp_enqueue_script('postbox','','',NULL);
		wp_enqueue_style('font-awesome','https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
    ca_thumbnails();

	if(!empty($_POST['church_admin_search']))church_admin_editable_script();



	if(isset($_GET['action']))
	{
		switch($_GET['action'])
		{
			case'edit_safeguarding':case 'rota':case'church_admin_rota_list':church_admin_date_picker_script();break;
			case 'edit_event':church_admin_date_picker_script();break;
			case 'bulk_geocode':
				check_admin_referer('bulk_geocode');
				church_admin_google_map_api();
				wp_enqueue_script('ca_batch_geocode', plugins_url('church-admin/includes/batch_geocode.js',dirname(__FILE__) ), array( 'jquery' ) ,FALSE, TRUE);
			break;
			case 'services':case'attendance':church_admin_date_picker_script();church_admin_frontend_graph_script();break;
			case'church_admin_cron_email':
				if(defined('CA_DEBUG'))church_admin_debug('Cron fired:'.date('Y-m-d h:i:s')."/r/n");
				church_admin_bulk_email();exit();
			break;
			case 'remove-queue':check_admin_referer('remove-queue');church_admin_remove_queue();break;
			case'church_admin_send_email':church_admin_email_script();church_admin_autocomplete_script();church_admin_date_picker_script();break;
			case'edit_resend':church_admin_email_script();church_admin_autocomplete_script();church_admin_date_picker_script();break;
			case'resend_new':church_admin_email_script();church_admin_autocomplete_script();break;
			case'resend_email':church_admin_email_script();church_admin_autocomplete_script();break;
			case'church_admin_send_sms':church_admin_email_script();church_admin_autocomplete_script();break;
			case'delete_small_group':church_admin_sg_map_script();church_admin_autocomplete_script();break;
			case'church_admin_search';church_admin_editable_script();break;
			//calendar

			case'church_admin_add_category':church_admin_farbtastic_script();break;
			case'church_admin_edit_category':church_admin_farbtastic_script();break;

			case 'small_groups':church_admin_sortable_script();church_admin_form_script();church_admin_sg_map_script();break;
			case 'edit_service':church_admin_form_script();break;
			case 'edit_site':church_admin_form_script();church_admin_map_script();church_admin_media_uploader_enqueue();break;
			case 'edit_small_group':church_admin_form_script();church_admin_sg_map_script();church_admin_map_script();church_admin_autocomplete_script();break;
			case'classes':church_admin_date_picker_script();church_admin_frontend_graph_script();break;
			case'view_class':church_admin_date_picker_script();church_admin_autocomplete_script();church_admin_frontend_graph_script();break;
			case'church_admin_add_calendar':church_admin_date_picker_script();break;
			case'church_admin_series_event_edit':church_admin_date_picker_script();break;
			case'church_admin_single_event_edit':church_admin_date_picker_script();break;
			case'edit_attendance':church_admin_date_picker_script();break;
			case'church_admin_new_edit_calendar':church_admin_date_picker_script();break;
			case'edit_kidswork':church_admin_date_picker_script();break;
			case'individual_attendance':church_admin_date_picker_script();break;
			case'edit_class':church_admin_date_picker_script();church_admin_autocomplete_script();break;

			case'edit_hope_team':church_admin_date_picker_script();church_admin_autocomplete_script();break;
			case'permissions':church_admin_date_picker_script();church_admin_autocomplete_script();break;
			case'edit_file':church_admin_date_picker_script();church_admin_autocomplete_script();break;
			case'file_add':church_admin_date_picker_script();church_admin_autocomplete_script();break;
			case'church_admin_member_type':church_admin_sortable_script();break;
			//rota
			case'rota';church_admin_editable_script();break;
			case'edit_rota';church_admin_editable_script();church_admin_autocomplete_script();church_admin_date_picker_script();break;
			case'list';church_admin_editable_script();break;
			case'church_admin_rota_settings_list':church_admin_sortable_script();break;
			case'church_admin_edit_rota_settings':church_admin_sortable_script();break;
			//directory
			case'new_household':
			case'church_admin_new_household':church_admin_form_script();church_admin_map_script();church_admin_date_picker_script();church_admin_media_uploader_enqueue();church_admin_media_uploader_enqueue();church_admin_date_picker_script();break;
			case'edit_household':
			case'view_household':
				church_admin_form_script();church_admin_map_script();church_admin_form_script();church_admin_date_picker_script();church_admin_media_uploader_enqueue();
			break;
			case 'edit_people':church_admin_form_script();church_admin_date_picker_script();church_admin_media_uploader_enqueue();
			break;
			case'app':case'edit_sermon_series':church_admin_media_uploader_enqueue();
			break;
			case'church_admin_permissions':church_admin_date_picker_script();church_admin_autocomplete_script();break;
			case'view_ministry':church_admin_autocomplete_script();break;
			case'church_admin_update_order': church_admin_update_order($_GET['which']);exit();break;
			case'get_people':church_admin_ajax_people();break;
			case'people':case'edit_funnel':case'delete_funnel':church_admin_sortable_script();break;

		}
	}


}



function church_admin_media_uploader_enqueue() {
   if(is_admin()) wp_enqueue_media();//enqueue media uploader if on admin page

  }

/**
 *
 * Enqueues jquery
 *
 * @author  Andy Moyle
 * @param    null
 * @return
 * @version  0.1
 *
 */
 function church_admin_calendar_script()
 {
 	wp_enqueue_script(
			'church-admin-calendar-script',
			plugins_url( '/', __FILE__ ) . 'includes/calendar.js',
			array( 'jquery' ),
			FALSE, TRUE
		);
}




 /**
 *
 * Registers google map api with low priority, so it happens last on enqueuing!
 *
 * @author  Andy Moyle
 * @param    null
 * @return
 * @version  0.1
 *
 */
 function church_admin_google_map_api()
 {

 	//fix issue caused by some "premium" themes, which call google maps w/o key on every admin page. D'uh!
 	wp_dequeue_script('avia-google-maps-api');

     //now enqueue google map api with the key
     $src = 'https://maps.googleapis.com/maps/api/js';
     $key='?key='.get_option('church_admin_google_api_key');
     wp_enqueue_script( 'Google Map Script',$src.$key, array() ,FALSE, TRUE);


 }

 /**
 *
 * Initialises js scripts for Google graph api
 *
 * @author  Andy Moyle
 * @param    null
 * @return
 * @version  0.1
 *
 */
function church_admin_frontend_graph_script()
{

	wp_enqueue_script('google-graph-api','https://www.google.com/jsapi', array( 'jquery' ) ,FALSE, FALSE);

}
function church_admin_podcast_script()
{
	$ajax_nonce = wp_create_nonce("church_admin_mp3_play");
	wp_enqueue_script('jquery');
	wp_enqueue_script('ca_podcast_audio',plugins_url('church-admin/includes/audio.min.js',dirname(__FILE__) ) , array( 'jquery' ) ,FALSE, TRUE);
	wp_localize_script( 'ca_podcast_audio', 'ChurchAdminAjax1', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
	wp_enqueue_script('ca_podcast_audio_use',plugins_url('church-admin/includes/audio.use.js',dirname(__FILE__) ), array( 'jquery' ) ,FALSE, TRUE);
	wp_localize_script( 'ca_podcast_audio_use', 'ChurchAdminAjax', array('security'=>$ajax_nonce, 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
}
function church_admin_sortable_script()
{
	wp_enqueue_script( 'jquery-ui-sortable' ,'','',NULL);
	wp_enqueue_script('touch-punch',plugins_url('church-admin/includes/jQuery.touchpunch.js',dirname(__FILE__) ), array( 'jquery' ) ,FALSE, TRUE);
}
function church_admin_form_script()
{
	wp_enqueue_script('form-clone',plugins_url('church-admin/includes/jquery-formfields.js',dirname(__FILE__) ), array( 'jquery' ) ,FALSE, TRUE);
}
function church_admin_sg_map_script()
{

	church_admin_google_map_api();
	wp_enqueue_script('ca_google_map_script', plugins_url('church-admin/includes/admin_sg_maps.js',dirname(__FILE__) ), array( 'jquery' ) ,FALSE, TRUE);
}
function church_admin_frontend_sg_map_script()
{

	church_admin_google_map_api();
	wp_enqueue_script('ca_google_map_script', plugins_url('church-admin/includes/smallgroup_maps.js',dirname(__FILE__) ), array( 'jquery' ) ,FALSE, TRUE);
}
function church_admin_map_script()
{
		church_admin_google_map_api();
    wp_enqueue_script('js_map', plugins_url('church-admin/includes/maps.js',dirname(__FILE__) ), array( 'jquery' ) ,FALSE, TRUE);
}
function church_frontend_map_script()
{
	church_admin_google_map_api();
	wp_enqueue_script('js_map', plugins_url('church-admin/includes/google_maps.js',dirname(__FILE__) ), array( 'jquery' ) ,FALSE, TRUE);
}
function church_admin_autocomplete_script()
{
	wp_enqueue_script('jquery-ui-autocomplete','','',NULL);
}
function church_admin_date_picker_script()
{
	wp_enqueue_script( 'jquery-ui-datepicker','','',NULL );
	wp_enqueue_style( 'jquery.ui.theme', plugins_url('css/jquery-ui-1.8.21.custom.css',__FILE__ ) ,'',NULL);
}
function church_admin_farbtastic_script()
{
	wp_enqueue_script( 'farbtastic' ,'','',NULL);
    wp_enqueue_style('farbtastic','','',NULL);
}
function church_admin_email_script()
{
	wp_enqueue_script('jquery','','',NULL);
    wp_register_script('ca_email',  plugins_url('church-admin/includes/email.js',dirname(__FILE__) ), array( 'jquery' ) ,FALSE, TRUE);
	wp_enqueue_script('ca_email','','',NULL);
}
function church_admin_editable_script()
{
    wp_register_script('ca_editable',  plugins_url('church-admin/includes/jquery.jeditable.mini.js',dirname(__FILE__) ), array('jquery'), NULL,TRUE);
    wp_enqueue_script('ca_editable');
}




/* Thumbnails */
function ca_thumbnails()
{
        /**
 *
 * Add thumbnails for plugin use
 *
 * @author  Andy Moyle
 * @param    null
 * @return
 * @version  0.1
 *
 */
    add_theme_support( 'post-thumbnails' );
    if ( function_exists( 'add_image_size' ) )
    {
        add_image_size('ca-people-thumb',75,75);
	add_image_size( 'ca-email-thumb', 300, 200 ); //300 pixels wide (and unlimited height)
	add_image_size('ca-120-thumb',120,90);
	add_image_size('ca-240-thumb',240,180);
    }

}
/* Thumbnails */
add_action( 'admin_enqueue_scripts','church_admin_public_css');
add_action('wp_enqueue_scripts','church_admin_public_css');
function church_admin_public_css(){wp_enqueue_style('Church-Admin',plugins_url('church-admin/includes/style.new.css',dirname(__FILE__) ),'',NULL);}
add_action('wp_head', 'church_admin_public_header');
function church_admin_public_header()
{
    global $church_admin_version;

    echo'<!-- church_admin v'.$church_admin_version.'-->
    <style>table.church_admin_calendar{width:';
    if(get_option('church_admin_calendar_width')){echo get_option('church_admin_calendar_width').'px}</style>';}else {echo'700px}</style>';}

}

//Build Admin Menus
add_action('admin_menu', 'church_admin_menus');
/**
 *
 * Admin menu
 *
 * @author  Andy Moyle
 * @param    null
 * @return
 * @version  0.1
 *
 */
function church_admin_menus()

{

    global $level;
    add_menu_page('church_admin:Administration', __('Church Admin','church-admin'),  'publish_posts', 'church_admin/index.php', 'church_admin_main');
}

// Admin Bar Customisation
/**
 *
 * Admin Bar Menu
 *
 * @author  Andy Moyle
 * @param    null
 * @return
 * @version  0.1
 *
 */
function church_admin_admin_bar_render() {

 	global $wp_admin_bar;
 	// Add a new top level menu link
 	// Here we add a customer support URL link
	if(current_user_can('publish_posts'))
	{
			$wp_admin_bar->add_menu( array('parent' => false, 'id' => 'church_admin', 'title' => __('Church Admin','church-admin'), 'href' => admin_url().'admin.php?page=church_admin/index.php' ));
			if(church_admin_level_check('Directory'))$wp_admin_bar->add_menu(array ('parent' => 'church_admin','id'=> 'household1','title'=> __('New Household','church-admin'),'href'=>wp_nonce_url('admin.php?page=church_admin/index.php&amp;tab=address&action=church_admin_new_household','new_household')) );
			if(current_user_can('manage_options'))$wp_admin_bar->add_menu(array('parent' => 'church_admin','id' => 'church_admin_settings', 'title' => __('Settings','church-admin'), 'href' => admin_url().'admin.php?page=church_admin/index.php&action=church_admin_settings' ));
			$wp_admin_bar->add_menu(array('parent' => 'church_admin','id' => 'plugin_support', 'title' => __('Plugin Support','church-admin'), 'href' => 'http://www.churchadminplugin.com/support/' ));
		}
}

// Finally we add our hook function
add_action( 'wp_before_admin_bar_render', 'church_admin_admin_bar_render' );




//main admin page function


function church_admin_main()
{
    global $wpdb,$church_admin_version;
	echo'<div class="wrap"><!--church_admin_main-->';
	//menu at top of all admin pages
	require_once(plugin_dir_path(__FILE__).'includes/admin.php');
	church_admin_front_admin();

	//allow people to edit their own entry

	$self_edit=FALSE;
	$user_id=get_current_user_id();
	if(!empty($_GET['household_id']))$check=$wpdb->get_var('SELECT user_id FROM '.CA_PEO_TBL.' WHERE user_id="'.esc_sql($user_id).'" AND household_id="'.esc_sql($_GET['household_id']).'"');
	if(!empty($check) && $check==$user_id)$self_edit=TRUE;
	$user_id=!empty($_GET['user_id'])?$_GET['user_id']:NULL;
	$id=isset($_GET['id'])?$_GET['id']:0;
	$mtg_type=!empty($_GET['mtg_type'])?$_GET['mtg_type']:'service';
	$rota_date=!empty($_GET['rota_date'])?$_GET['rota_date']:NULL;
	$rota_id=!empty($_GET['rota_id'])?$_GET['rota_id']:NULL;
	$copy_id=!empty($_GET['copy_id'])?$_GET['copy_id']:NULL;
    $date_id=!empty($_GET['date_id'])?$_GET['date_id']:NULL;
    $event_id=!empty($_GET['event_id'])?$_GET['event_id']:NULL;
	$email_id=!empty($_GET['email_id'])?$_GET['email_id']:NULL;
    $people_id=!empty($_GET['people_id'])?$_GET['people_id']:NULL;
    $household_id=!empty($_GET['household_id'])?$_GET['household_id']:NULL;
    $service_id=!empty($_REQUEST['service_id'])?$_REQUEST['service_id']:NULL;
    $site_id=!empty($_REQUEST['site_id'])?$_REQUEST['site_id']:NULL;
    $attendance_id=!empty($_GET['attendance_id'])?$_GET['attendance_id']:NULL;
    $ID=!empty($_GET['ID'])?$_GET['ID']:NULL;
    $funnel_id=!empty($_GET['funnel_id'])?$_GET['funnel_id']:NULL;
    $people_type_id=isset($_GET['people_type_id'])?$_GET['people_type_id']:NULL;
    $member_type_id=isset($_REQUEST['member_type_id'])?$_REQUEST['member_type_id']:NULL;
	$facilities_id=isset($_REQUEST['facilities_id'])?$_REQUEST['facilities_id']:NULL;
    $edit_type=!empty($_REQUEST['edit_type'])?$_REQUEST['edit_type']:'single';
    $file=!empty($_GET['file'])?$_GET['file']:NULL;
	$smallgroup_id=!empty($_GET['smallgroup_id'])?$_GET['smallgroup_id']:NULL;
    if(!empty($_REQUEST['church_admin_search'])){if(church_admin_level_check('Directory')){require_once(plugin_dir_path(__FILE__).'includes/directory.php');church_admin_search($_REQUEST['church_admin_search']);}}
	elseif(isset($_GET['action']))
    {
	switch($_GET['action'])
	{
		case 'reset_readings':$wpdb->query('UPDATE '.CA_BRP_TBL.' SET passages=""');echo'Done ;-)';break;
		case 'test_email':require_once(plugin_dir_path(__FILE__).'includes/email.php');church_admin_test_email($_GET['email']);break;
		/*************************************
		*
		*		Events
		*
		**************************************/
		case 'edit_event':check_admin_referer('edit_event');require_once(plugin_dir_path(__FILE__).'includes/events.php'); church_admin_edit_event($event_id);break;
		case 'events':require_once(plugin_dir_path(__FILE__).'includes/events.php'); church_admin_events();break;
/*************************************
*
*		CUSTOM FIELDS
*
**************************************/

		case 'edit_custom_field':check_admin_referer('edit_custom_field'); echo church_admin_edit_custom_field($id);break;
		case 'delete_custom_field':check_admin_referer('delete_custom_field'); echo church_admin_delete_custom_field($id);break;
		//main menu sections
		case 'gdpr-email':check_admin_referer('gdpr-email'); if(church_admin_level_check('Directory')){require_once(plugin_dir_path(__FILE__).'includes/directory.php');church_admin_gdpr_email();}break;
		case 'gdpr-email-test':check_admin_referer('gdpr-email'); if(church_admin_level_check('Directory')){require_once(plugin_dir_path(__FILE__).'includes/directory.php');church_admin_gdpr_email_test();}break;
		case 'sessions': require_once(plugin_dir_path(__FILE__).'includes/admin.php');church_admin_sessions_menu();break;
		case'shortcodes':church_admin_shortcodes_list();break;
		case'classes':if(church_admin_level_check('Directory')){require_once(plugin_dir_path(__FILE__).'includes/classes.php');church_admin_classes();}else{echo'<div class="error"><p>You don\'t have permissions</p></div>';}break;
		case'small_groups':if(church_admin_level_check('Small Groups')){ echo church_admin_smallgroups_main();}else{echo'<div class="error"><p>'.__("You don't have permissions",'church-admin').'</p></div>';}break;
		case'services':if(church_admin_level_check('Small Groups')){church_admin_services_main();}else{echo'<div class="error"><p>'.__("You don't have permissions",'church-admin').'</p></div>';}break;
		case'attendance':if(church_admin_level_check('Small Groups')){church_admin_attendance_main();}else{echo'<div class="error"><p>'.__("You don't have permissions",'church-admin').'</p></div>';}break;
		case'ministries':if(church_admin_level_check('Directory')){church_admin_ministries_menu();break;}else{echo'<div class="error"><p>'.__("You don't have permissions",'church-admin').'</p></div>';}break;
		case'people':if(church_admin_level_check('Directory')){church_admin_people_main();}else{echo'<div class="error"><p>'.__("You don't have permissions",'church-admin').'</p></div>';}break;
		case'children':if(church_admin_level_check('Directory')){church_admin_children();}else{echo'<div class="error"><p>'.__("You don't have permissions",'church-admin').'</p></div>';}break;
		case'communication':if(church_admin_level_check('Prayer Chain')){church_admin_communication();}else{echo'<div class="error"><p>'.__("You don't have permissions",'church-admin').'</p></div>';}break;
		case'rota':if(church_admin_level_check('Rota')){require_once(plugin_dir_path(__FILE__).'includes/admin.php');church_admin_rota_main();}else{echo'<div class="error"><p>'.__("You don't have permissions",'church-admin').'</p></div>';}break;
		case'tracking':if(church_admin_level_check('Attendance')){church_admin_tracking();}else{echo'<div class="error"><p>'.__("You don't have permissions",'church-admin').'</p></div>';}break;
		case 'podcast':if(church_admin_level_check('Sermons')){church_admin_podcast();}else{echo'<div class="error"><p>'.__("You don't have permissions",'church-admin').'</p></div>';}break;
		case 'settings':if(current_user_can('manage_options')){church_admin_settings_menu();}else{echo'<div class="error"><p>'.__("You don't have permissions",'church-admin').'</p></div>';}break;
		case 'calendar':if(church_admin_level_check('Calendar')){require_once(plugin_dir_path(__FILE__).'includes/calendar.php');church_admin_new_calendar(time(),$facilities_id);}else{echo'<div class="error"><p>You don\'t have permissions</p></div>';}break;
		case 'facilities':if(church_admin_level_check('Calendar')){require_once(plugin_dir_path(__FILE__).'includes/calendar.php');church_admin_facilities(time(),$facilities_id);}else{echo'<div class="error"><p>You don\'t have permissions</p></div>';}break;
		//csv import
		case'csv-import':if(church_admin_level_check('Directory')){check_admin_referer('csv_import');require_once(plugin_dir_path(__FILE__).'includes/directory.php');church_admin_import_csv();}break;
		case'replicate_roles':if(church_admin_level_check('Directory')){check_admin_referer('replicate_roles');require_once(plugin_dir_path(__FILE__).'includes/directory.php');church_admin_replicate_roles();}break;

		case 'edit_marital_status': if(church_admin_level_check('Directory'))
									{require_once(plugin_dir_path(__FILE__).'includes/settings.php');church_admin_edit_marital_status($ID);}
		break;
		case 'delete_marital_status': if(church_admin_level_check('Directory'))
									{require_once(plugin_dir_path(__FILE__).'includes/settings.php');church_admin_delete_marital_status($ID);}
		break;
		//classes
		case 'class':require_once(plugin_dir_path(__FILE__).'includes/classes.php');church_admin_classes();break;
		case 'edit_class':require_once(plugin_dir_path(__FILE__).'includes/classes.php');church_admin_edit_class($id);break;
		case 'delete_class':require_once(plugin_dir_path(__FILE__).'includes/classes.php');church_admin_delete_class($id);break;
		case 'view_class':require_once(plugin_dir_path(__FILE__).'includes/classes.php');church_admin_view_class($id);break;
/*************************************
*
*		APP
*
**************************************/
		case 'logout_app':require_once(plugin_dir_path(__FILE__).'app/app-admin.php');church_admin_logout_app($user_id);break;
		case 'app_page':require_once(plugin_dir_path(__FILE__).'app/app-admin.php');church_admin_app_post();break;
		case 'app': require_once(plugin_dir_path(__FILE__).'app/app-admin.php');church_admin_app();break;
/*************************************
*
*		CHECK-IN
*
**************************************/
		case 'QRCode':require_once(plugin_dir_path(__FILE__).'includes/checkin.php');church_admin_create_QR($people_id);break;
		case 'checkin-labels':require_once(plugin_dir_path(__FILE__).'includes/checkin.php');church_admin_checkin_labels();break;
/*************************************
*
*		KIDS WORK
*
**************************************/
		case 'edit_kidswork':require_once(plugin_dir_path(__FILE__).'includes/kidswork.php');church_admin_edit_kidswork($id);break;
		case 'delete_kidswork':require_once(plugin_dir_path(__FILE__).'includes/kidswork.php');church_admin_delete_kidswork($id);break;
		case 'kidswork':require_once(plugin_dir_path(__FILE__).'includes/kidswork.php');church_admin_kidswork();break;
		case 'edit_safeguarding':require_once(plugin_dir_path(__FILE__).'includes/kidswork.php');church_admin_edit_safeguarding($people_id);break;
		//prayer chain

		case'prayer_chain_message':if(church_admin_level_check('Prayer Chain')){require_once(plugin_dir_path(__FILE__).'includes/prayer_chain.php');church_admin_prayer_chain();}else{echo __("You don't have permission to send a prayer chain message",'church-admin'); }break;
/*************************************
*
*		HOPETEAM
*
**************************************/
		case'hope_team_jobs':check_admin_referer('hope_team_jobs');require_once(plugin_dir_path(__FILE__).'includes/hope-team.php');church_admin_hope_team_jobs($id);break;
		case'edit_hope_team_job':check_admin_referer('hope_team_jobs');require_once(plugin_dir_path(__FILE__).'includes/hope-team.php');church_admin_edit_hope_team_job($id);break;
		case'delete_hope_team_job':check_admin_referer('delete_hope_team_jobs');require_once(plugin_dir_path(__FILE__).'includes/hope-team.php');church_admin_delete_hope_team_job($id);break;
		case'edit_hope_team':check_admin_referer('edit_hope_team');require_once(plugin_dir_path(__FILE__).'includes/hope-team.php');church_admin_edit_hope_team($id);break;
/*************************************
*
*		ERRORS
*
**************************************/
		case 'church_admin_activation_log_clear':check_admin_referer('clear_error');church_admin_activation_log_clear();break;



/*************************************
*
*		MEDIA
*
**************************************/

	case'list_speakers':
		if(church_admin_level_check('Sermons'))
		{
			require_once(plugin_dir_path(__FILE__).'includes/sermon-podcast.php');
			ca_podcast_list_speakers();
		}
	break;
    case'edit_speaker':
    	    if(church_admin_level_check('Sermons'))
    	    {
    	       require_once(plugin_dir_path(__FILE__).'includes/sermon-podcast.php');
    	       ca_podcast_edit_speaker($id);
    	    }
    break;
            case'delete_speaker':if(church_admin_level_check('Sermons')){require_once(plugin_dir_path(__FILE__).'includes/sermon-podcast.php');ca_podcast_delete_speaker($id);}break;
            case'list_sermon_series':if(church_admin_level_check('Sermons')){require_once(plugin_dir_path(__FILE__).'includes/sermon-podcast.php');ca_podcast_list_series();}break;
            case'edit_sermon_series':if(church_admin_level_check('Sermons')){check_admin_referer('edit_sermon_series');require_once(plugin_dir_path(__FILE__).'includes/sermon-podcast.php');ca_podcast_edit_series($id);}break;
            case'delete_sermon_series':if(church_admin_level_check('Sermons')){check_admin_referer('delete_sermon_series');require_once(plugin_dir_path(__FILE__).'includes/sermon-podcast.php');ca_podcast_delete_series($id);}break;
            case'list_files':if(church_admin_level_check('Sermons')){require_once(plugin_dir_path(__FILE__).'includes/sermon-podcast.php');ca_podcast_list_files();}break;
            case'edit_file':if(church_admin_level_check('Sermons')){check_admin_referer('edit_podcast_file');require_once(plugin_dir_path(__FILE__).'includes/sermon-podcast.php');ca_podcast_edit_file($id);}break;
            case'delete_file':if(church_admin_level_check('Sermons')){check_admin_referer('delete_podcast_file');require_once(plugin_dir_path(__FILE__).'includes/sermon-podcast.php');ca_podcast_delete_file($id);}break;
            case'file_delete':if(church_admin_level_check('Sermons')){check_admin_referer('file_delete');require_once(plugin_dir_path(__FILE__).'includes/sermon-podcast.php');ca_podcast_file_delete($file);}break;
            case'file_add':if(church_admin_level_check('Sermons')){check_admin_referer('file_add');require_once(plugin_dir_path(__FILE__).'includes/sermon-podcast.php');ca_podcast_file_add($file);}break;
            case'check_files':if(church_admin_level_check('Sermons')){require_once(plugin_dir_path(__FILE__).'includes/sermon-podcast.php');ca_podcast_check_files();}break;
            case'podcast':if(church_admin_level_check('Sermons')){require_once(plugin_dir_path(__FILE__).'includes/sermon-podcast.php');if(ca_podcast_xml()){echo'<p>Podcast <a href="'.CA_POD_URL.'podcast.xml">feed</a> updated</p>';}}break;
            case'podcast_settings':if(church_admin_level_check('Sermons')){check_admin_referer('podcast_settings');require_once(plugin_dir_path(__FILE__).'includes/podcast-settings.php');ca_podcast_settings();}break;
/*************************************
*
*		COMMUNICATIONS
*
**************************************/
		case'mailchimp_sync':if(church_admin_level_check('Directory')){require_once(plugin_dir_path(__FILE__).'includes/mailchimp.php');church_admin_mailchimp_sync();}break;
	    case 'church_admin_send_sms':if(church_admin_level_check('Bulk SMS')){require_once(plugin_dir_path(__FILE__ ).'includes/sms.php');church_admin_send_sms();}break;
	    case 'email_list':if(church_admin_level_check('Bulk Email')){require_once(plugin_dir_path(__FILE__).'includes/email.php');church_admin_email_list();}break;
		case 'delete_email':if(church_admin_level_check('Bulk Email')){require_once(plugin_dir_path(__FILE__).'includes/email.php');church_admin_delete_email($email_id);}break;
		case 'resend_email':if(church_admin_level_check('Bulk Email')){require_once(plugin_dir_path(__FILE__).'includes/email.php');church_admin_resend($email_id);}break;
		case 'resend_new':if(church_admin_level_check('Bulk Email')){require_once(plugin_dir_path(__FILE__).'includes/email.php');church_admin_resend_new($email_id);}break;
	    case 'church_admin_send_email':if(church_admin_level_check('Bulk Email')){require_once(plugin_dir_path(__FILE__).'includes/email.php');church_admin_send_email(NULL);}break;
	    case 'edit_resend':if(church_admin_level_check('Bulk Email')){require_once(plugin_dir_path(__FILE__).'includes/email.php');church_admin_send_email($email_id);}break;

	    case'church_admin_people_activity':if(church_admin_level_check('Directory')){require_once(plugin_dir_path(__FILE__).'includes/people_activity.php'); echo church_admin_recent_people_activity();}break;
/*************************************
*
*		ATTENDANCE
*
**************************************/
		case 'individual_attendance':require_once(plugin_dir_path(__FILE__).'includes/individual_attendance.php'); echo church_admin_individual_attendance();break;
	    case 'church_admin_attendance_metrics':require_once(plugin_dir_path(__FILE__).'includes/attendance.php');church_admin_attendance_metrics($service_id);break;

	    case 'church_admin_attendance_list':require_once(plugin_dir_path(__FILE__).'includes/attendance.php');church_admin_attendance_list($service_id);break;
	    case 'edit_attendance':check_admin_referer('edit_attendance');require_once(plugin_dir_path(__FILE__).'includes/attendance.php');church_admin_edit_attendance($attendance_id);break;
	    case 'delete_attendance':check_admin_referer('delete_attendance');require_once(plugin_dir_path(__FILE__).'includes/attendance.php');church_admin_delete_attendance($attendance_id);break;

/*************************************
*
*		MINISTRIES
*
**************************************/
	    case 'edit_ministry':check_admin_referer('edit_ministry');require_once(plugin_dir_path(__FILE__).'includes/departments.php');church_admin_edit_ministry($id);break;
	    case 'delete_ministry':check_admin_referer('delete_ministry');require_once(plugin_dir_path(__FILE__).'includes/departments.php');church_admin_delete_ministry($id);break;
	    case 'ministry_list':check_admin_referer('ministry_list');require_once(plugin_dir_path(__FILE__).'includes/departments.php');church_admin_ministries_list();break;
       case 'view_ministry':check_admin_referer('view_ministry');require_once(plugin_dir_path(__FILE__).'includes/departments.php');church_admin_view_ministry($id);break;
/*************************************
*
*		FOLLOW UP
*
**************************************/
	    case 'church_admin_funnel_list':require_once(plugin_dir_path(__FILE__).'includes/funnel.php');church_admin_funnel_list();break;
	    case 'edit_funnel':check_admin_referer('edit_funnel');require_once(plugin_dir_path(__FILE__).'includes/funnel.php');church_admin_edit_funnel($funnel_id,$people_type_id);break;
		case 'delete_funnel':check_admin_referer('delete_funnel');require_once(plugin_dir_path(__FILE__).'includes/funnel.php');church_admin_delete_funnel($funnel_id);break;
	    case 'church_admin_assign_funnel':require_once(plugin_dir_path(__FILE__).'includes/people_activity.php');church_admin_assign_funnel();break;
	    case 'church_admin_email_follow_up_activity':check_admin_referer('email_funnels');require_once(plugin_dir_path(__FILE__).'includes/people_activity.php');church_admin_email_follow_up_activity();break;
			case 'follow_up_completed':require_once(plugin_dir_path(__FILE__).'includes/funnel.php');require_once(plugin_dir_path(__FILE__).'includes/people_activity.php');church_admin_follow_up_completed($id);break;
/*************************************
*
*		MEMBER TYPE
*
**************************************/
	         case 'church_admin_member_type':require_once(plugin_dir_path(__FILE__).'includes/member_type.php');church_admin_member_type();break;
	    case 'church_admin_edit_member_type':check_admin_referer('edit_member_type');require_once(plugin_dir_path(__FILE__).'includes/member_type.php');church_admin_edit_member_type($member_type_id);break;
	    case 'church_admin_delete_member_type':check_admin_referer('delete_member_type');require_once(plugin_dir_path(__FILE__).'includes/member_type.php');church_admin_delete_member_type($member_type_id);break;

/*************************************
*
*		FACILITIES
*
**************************************/
	    case 'church_admin_facilities':require_once(plugin_dir_path(__FILE__).'includes/facilities.php');church_admin_facilities();break;
	    case 'edit_facility':check_admin_referer('edit_facility');require_once(plugin_dir_path(__FILE__).'includes/facilities.php');church_admin_edit_facility($facilities_id);break;
	    case 'delete_facility':check_admin_referer('delete_facility');require_once(plugin_dir_path(__FILE__).'includes/facilities.php');church_admin_delete_facility($facilities_id);break;

/*************************************
*
*		CALENDAR
*
**************************************/
	    case 'church_admin_new_calendar':if(church_admin_level_check('Calendar')){require_once(plugin_dir_path(__FILE__).'includes/calendar.php');church_admin_new_calendar(time(),$facilities_id);}break;
		case 'church_admin_new_edit_calendar':if(church_admin_level_check('Calendar'))
		{
			require_once(plugin_dir_path(__FILE__).'includes/calendar.php');

			if(substr($id,0,4)=='item'){church_admin_event_edit(substr($id,4),NULL,$edit_type,NULL,$facilities_id);}
			else{church_admin_event_edit(NULL,NULL,NULL,$id,$facilities_id);}
		}
		break;
		case 'church_admin_calendar_list':if(church_admin_level_check('Calendar')){require_once(plugin_dir_path(__FILE__).'includes/calendar.php');church_admin_calendar();}break;

	    case 'church_admin_add_category':check_admin_referer('add_category');if(church_admin_level_check('Calendar')){require_once(plugin_dir_path(__FILE__).'includes/calendar.php');church_admin_edit_category(NULL,NULL);}break;

		case 'church_admin_edit_category':check_admin_referer('edit_category');if(church_admin_level_check('Calendar')){require_once(plugin_dir_path(__FILE__).'includes/calendar.php');church_admin_edit_category($id,NULL);}break;

		case 'church_admin_delete_category':check_admin_referer('delete_category');if(church_admin_level_check('Calendar')){require_once(plugin_dir_path(__FILE__).'includes/calendar.php');church_admin_delete_category($id);}break;

		case 'church_admin_single_event_delete':check_admin_referer('single_event_delete');if(church_admin_level_check('Calendar')){require_once(plugin_dir_path(__FILE__).'includes/calendar.php');church_admin_single_event_delete($date_id,$event_id); }break;

		case 'church_admin_series_event_delete':check_admin_referer('series_event_delete');if(church_admin_level_check('Calendar')){require_once(plugin_dir_path(__FILE__).'includes/calendar.php');church_admin_series_event_delete($event_id);}break;

		case 'church_admin_category_list':if(church_admin_level_check('Calendar'));{require_once(plugin_dir_path(__FILE__).'includes/calendar.php');church_admin_category_list();}break;

		case 'church_admin_series_event_edit':check_admin_referer('series_event_edit');if(church_admin_level_check('Calendar')){require_once(plugin_dir_path(__FILE__).'includes/calendar.php');church_admin_event_edit($date_id,$event_id,'series',NULL,NULL);}break;

		case 'church_admin_single_event_edit':check_admin_referer('single_event_edit');if(church_admin_level_check('Calendar')){require_once(plugin_dir_path(__FILE__).'includes/calendar.php');church_admin_event_edit($date_id,$event_id,'single',NULL,NULL);}break;

		case 'church_admin_add_calendar':if(church_admin_level_check('Calendar')){require_once(plugin_dir_path(__FILE__).'includes/calendar.php');church_admin_event_edit(NULL,NULL,NULL,NULL,NULL);}break;

/*************************************
*
*		DIRECTORY
*
**************************************/
	case'bulk_geocode':
			check_admin_referer('bulk_geocode');
			if(church_admin_level_check('Directory')){require_once(plugin_dir_path(__FILE__).'includes/directory.php');church_admin_bulk_geocode();}
	break;
	    case 'gdpr_bulk_confirm':if(church_admin_level_check('Directory')){require_once(plugin_dir_path(__FILE__).'includes/directory.php');gdpr_confirm_everyone();}break;
	    case 'view_person':if(church_admin_level_check('Directory')){require_once(plugin_dir_path(__FILE__).'includes/directory.php');church_admin_view_person($people_id);}break;
	    case 'church_admin_move_person':if(church_admin_level_check('Directory')){require_once(plugin_dir_path(__FILE__).'includes/directory.php');church_admin_move_person($people_id);}break;
	    case 'church_admin_address_list': if(church_admin_level_check('Directory')){require_once(plugin_dir_path(__FILE__).'includes/directory.php');church_admin_address_list($member_type_id);}else{echo"<p>You don't have permission to do that";}break;
	    case 'create_users':if(church_admin_level_check('Directory')){require_once(plugin_dir_path(__FILE__).'includes/directory.php');church_admin_users();}else{echo'<p>'.__('You do not have permission to do that','church-admin').'</p>';}break;
			case 'create_confirmed_users':if(church_admin_level_check('Directory')){require_once(plugin_dir_path(__FILE__).'includes/directory.php');church_admin_confirmed_users();}else{echo'<p>'.__('You do not have permission to do that','church-admin').'</p>';}break;
	    case 'church_admin_create_user':check_admin_referer('create_user');if(church_admin_level_check('Directory')){require_once(plugin_dir_path(__FILE__).'includes/directory.php');church_admin_create_user($people_id,$household_id);}break;
	    case 'church_admin_migrate_users':check_admin_referer('migrate_users');if(church_admin_level_check('Directory')){require_once(plugin_dir_path(__FILE__).'includes/directory.php');church_admin_migrate_users();}break;
	    case 'display_household':
	    if(church_admin_level_check('Directory')||$self_edit){require_once(plugin_dir_path(__FILE__).'includes/directory.php');church_admin_display_household($household_id);}else{echo'<p>'.__('You do not have permission to do that','church-admin').'</p>';}break;
		case 'church_admin_new_household':if(church_admin_level_check('Directory')||$self_edit){require_once(plugin_dir_path(__FILE__).'includes/directory.php');church_admin_new_household();}else{echo'<p>'.__('You do not have permission to do that','church-admin').'</p>';}break;
	    case 'edit_household':if(church_admin_level_check('Directory')||$self_edit){require_once(plugin_dir_path(__FILE__).'includes/directory.php');church_admin_edit_household($household_id);}else{echo'<p>'.__('You do not have permission to do that','church-admin').'</p>';}break;
	    case 'delete_household':check_admin_referer('delete_household');if(church_admin_level_check('Directory')){require_once(plugin_dir_path(__FILE__).'includes/directory.php');church_admin_delete_household($household_id);}break;
	    case 'edit_people':
	    	if(church_admin_level_check('Directory')||$self_edit){require_once(plugin_dir_path(__FILE__).'includes/directory.php');church_admin_edit_people($people_id,$household_id);}else{echo'<p>'.__('You do not have permission to do that','church-admin').'</p>';}
	    break;
	    case 'delete_people':
	    if(church_admin_level_check('Directory')||$self_edit){require_once(plugin_dir_path(__FILE__).'includes/directory.php');church_admin_delete_people($people_id,$household_id);}else{echo'<p>'.__('You do not have permission to do that','church-admin').'</p>';}break;
	    case 'church_admin_search':if(wp_verify_nonce('ca_search_nonce','ca_search_nonce')){require_once(plugin_dir_path(__FILE__).'includes/directory.php');church_admin_search($_POST['ca_search']);}break;
		case'church_admin_recent_visitors': require_once(plugin_dir_path(__FILE__).'includes/recent.php');echo church_admin_recent_visitors($member_type_id);break;


/*************************************
*
*		ROTA
*
**************************************/


	    case 'church_admin_rota_list':if(church_admin_level_check('Rota')){require_once(plugin_dir_path(__FILE__).'includes/rota.new.php');church_admin_rota_list($service_id);}break;
	    case 'rota_list':if(church_admin_level_check('Rota')){require_once(plugin_dir_path(__FILE__).'includes/rota.new.php');church_admin_rota_list($service_id);}break;
	    case 'edit_rota': 	check_admin_referer('edit_rota');
	    		if(church_admin_level_check('Rota'))
	    		{
	    			require_once(plugin_dir_path(__FILE__).'includes/rota.new.php');
	    			church_admin_edit_rota($rota_date,$mtg_type,$service_id);
	    		}
	    break;
	    case 'delete_rota': check_admin_referer('delete_rota');
	    		if(church_admin_level_check('Rota'))
	    		{
	    			require_once(plugin_dir_path(__FILE__).'includes/rota.new.php');
	    			church_admin_delete_rota($rota_date,$mtg_type,$_GET['service_id']);
	    		}
	    break;
	    case 'email_rota':if(church_admin_level_check('Rota')){require_once(plugin_dir_path(__FILE__).'includes/rota.new.php');church_admin_email_rota($service_id,$rota_date);}break;
	    case 'auto_email_test':church_admin_auto_email_rota($service_id);break;
/*************************************
*
*		ROTA SETTINGS
*
**************************************/
	    case 'church_admin_rota_settings_list':if(church_admin_level_check('Rota')){require_once(plugin_dir_path(__FILE__).'includes/rota_settings.php');church_admin_rota_settings_list();}break;
	    case 'church_admin_edit_rota_settings':check_admin_referer('edit_rota_settings');if(church_admin_level_check('Rota')){require_once(plugin_dir_path(__FILE__).'includes/rota_settings.php');church_admin_edit_rota_settings($id);}break;
	    case 'church_admin_delete_rota_settings':check_admin_referer('delete_rota_settings');if(church_admin_level_check('Rota')){require_once(plugin_dir_path(__FILE__).'includes/rota_settings.php');church_admin_delete_rota_settings($id);}break;
	    case 'test-cron-rota':church_admin_auto_email_rota();break;
	    case 'sms-rota':if(church_admin_level_check('Rota')){require_once(plugin_dir_path(__FILE__).'includes/rota.new.php');church_admin_sms_rota($service_id);}break;
/*************************************
*
*		VISITOR - deprecated
*
**************************************/
	    case 'church_admin_add_visitor':check_admin_referer('add_visitor');if(church_admin_level_check('Visitor')){require_once(plugin_dir_path(__FILE__).'includes/visitor.php'); church_admin_add_visitor();} break;
	    case 'church_admin_edit_visitor':check_admin_referer('edit_visitor');if(church_admin_level_check('Visitor')){church_admin_edit_visitor($id);}break;
	    case 'church_admin_delete_visitor':check_admin_referer('delete_visitor');if(church_admin_level_check('Visitor')){church_admin_delete_visitor($id);} break;
	    case 'church_admin_move_visitor':check_admin_referer('move_visitor');if(church_admin_level_check('Visitor')){church_admin_move_visitor($id);}break;
/*************************************
*
*		SMALL GROUPS
*
**************************************/
		case'remove_from_smallgroup':
			check_admin_referer('remove');
			require_once(plugin_dir_path(__FILE__).'includes/small_groups.php');
			church_admin_remove_from_smallgroup($people_id,$smallgroup_id);
		break;
		case'whosin':check_admin_referer('whosin');if(church_admin_level_check('Small Groups')){require_once(plugin_dir_path(__FILE__).'includes/small_groups.php'); echo church_admin_whosin($id);}break;
	    case  'edit_small_group':check_admin_referer('edit_small_group');if(church_admin_level_check('Small Groups')){require_once(plugin_dir_path(__FILE__).'includes/small_groups.php'); echo church_admin_edit_small_group($id);}break;
	    case  'delete_small_group':check_admin_referer('delete_small_group');if(church_admin_level_check('Small Groups')){require_once(plugin_dir_path(__FILE__).'includes/small_groups.php'); echo church_admin_delete_small_group($id);}break;
	    case 'church_admin_small_groups':if(church_admin_level_check('Small Groups')){require_once(plugin_dir_path(__FILE__).'includes/small_groups.php'); echo church_admin_small_groups();}break;
/*************************************
*
*		SERVICES
*
**************************************/
	    case  'edit_service':check_admin_referer('edit_service');if(church_admin_level_check('Service')){require_once(plugin_dir_path(__FILE__).'includes/services.php');  church_admin_edit_service($id);}break;
	    case  'delete_service':check_admin_referer('delete_service');if(church_admin_level_check('Service')){require_once(plugin_dir_path(__FILE__).'includes/services.php'); church_admin_delete_service($id);}break;
	    case 'service_list':if(church_admin_level_check('Service')){require_once(plugin_dir_path(__FILE__).'includes/services.php'); church_admin_service_list();}break;
	    case 'delete_site':if(church_admin_level_check('Service')){require_once(plugin_dir_path(__FILE__).'includes/sites.php'); church_admin_delete_site($site_id);}break;
	    case 'edit_site':if(church_admin_level_check('Service')){require_once(plugin_dir_path(__FILE__).'includes/sites.php'); church_admin_edit_site($site_id);}break;
/*************************************
*
*		SETTINGS
*
**************************************/

		case'permissions':require_once(plugin_dir_path(__FILE__).'includes/permissions.php');church_admin_permissions();break;
		case'roles':require_once(plugin_dir_path(__FILE__).'includes/settings.php');church_admin_roles();church_admin_settings_menu();break;
	    case 'church_admin_settings':if(current_user_can('manage_options')){require_once(plugin_dir_path(__FILE__).'includes/settings.php');church_admin_general_settings();}break;
	    case'edit_people_type':require_once(plugin_dir_path(__FILE__).'includes/settings.php');echo church_admin_edit_people_type($ID);echo church_admin_people_types_list();break;
	    case'delete_people_type':require_once(plugin_dir_path(__FILE__).'includes/settings.php');echo church_admin_delete_people_type($ID);echo church_admin_people_types_list();break;
/*************************************
*
*		DEFAULT
*
**************************************/
	   default:if(church_admin_level_check('Directory')){church_admin_people_main();}else{echo'<p>'.__("You don't have permissions for this page",'church-admin').'</p>';}break;

	}

    }else if(church_admin_level_check('Directory')){church_admin_people_main();}else{echo'<p>'.__("You don't have permissions for this page",'church-admin').'</p>';}

   echo'<script>// shorthand no-conflict safe document-ready function
  jQuery(function($) {

    $( document ).on( "click", ".notice-church-admin .notice-dismiss", function () {

        var type = $( this ).closest( ".notice-church-admin" ).data( "notice" );

        $.ajax( ajaxurl,
          {
            type: "POST",
            data: {
              action: "dismissed_notice_handler",
              type: type,
            }
          } );
      } );
  });</script>';
   echo'</div><!-- .wrap -->';
}

function church_admin_shortcode($atts, $content = null)
{
	//sort out true false issue where it gets evaluated as a string
   	foreach($atts AS $key=>$value)
   	{
   		if($value==='FALSE'||$value==='false')$atts[$key]=0;
   		if($value==='TRUE'||$value==='true')$atts[$key]=1;
   	}

   	extract(shortcode_atts(array('pdf'=>1,'zoom'=>13,'class_id'=>NULL,'day_calendar'=>TRUE,'style'=>'new','kids'=>TRUE,'height'=>500,'width'=>900,"pdf_font_resize"=>TRUE,"updateable"=>1,"restricted"=>0,"loggedin"=>1,"type" => 'address-list','people_types'=>'all','site_id'=>0,'days'=>30,'year'=>date('Y'),'service_id'=>NULL,'photo'=>0,'category'=>NULL,'weeks'=>4,'ministry_id'=>NULL,'member_type_id'=>NULL,'kids'=>1,'map'=>0,'series_id'=>NULL,'speaker_id'=>NULL,'file_id'=>NULL,'api_key'=>NULL,'facilities_id'=>NULL,'exclude'=>NULL,'today'=>FALSE,'first_initial'=>0), $atts));
    church_admin_posts_logout();
    $out='';

    global $wpdb;

    global $wp_query;

    	$upload_dir = wp_upload_dir();
		$path=$upload_dir['basedir'].'/church-admin-cache/';
    	//look to see if church directory is o/p on a password protected page
    	$pageinfo=get_page($wp_query->post->ID);
    	//grab page info
    	//check to see if on a password protected page
    	if(($pageinfo->post_password!='')&&isset( $_COOKIE['wp-postpass_' . COOKIEHASH] ))
    	{
			$text = __('Log out of password protected posts','church-admin');
		//text for link
		$link = site_url().'?church_admin_logout=posts_logout';
		$out.= '<p><a href="' . wp_nonce_url($link, 'posts logout') .'">' . $text . '</a></p>';
		//output logoutlink
    	}

    	//grab content
    	switch($type)
    	{

			case 'sessions': require_once(plugin_dir_path(__FILE__).'includes/sessions.php');
				$out.=church_admin_sessions(NULL,NULL);
			break;
			case 'recent':
			if(empty($loggedin)||is_user_logged_in())
			{
				require_once(plugin_dir_path(__FILE__).'includes/recent.php');$out.=church_admin_recent_visitors($member_type_id=1);
			}
			else //login required
			{
				$out.='<div class="login"><h2>'.__('Please login','church-admin').'</h2>'.wp_login_form(array('echo'=>FALSE)).'</div>'.'<p><a href="'.wp_lostpassword_url(get_permalink() ).'" title="Lost Password">'.__('Help! I don\'t know my password','church-admin').'</a></p>';
			}
			break;
			case 'podcast':
				wp_enqueue_script('ca_podcast_audio');
				wp_enqueue_script('ca_podcast_audio_use');
				require_once(plugin_dir_path(__FILE__).'display/sermon-podcast.php');
				$out.=do_shortcode(church_admin_podcast_display($file_id,$exclude));
				/*if(!empty($_GET['speaker_name'])){$speaker_name=urldecode($_GET['speaker_name']);}else{$speaker_name=NULL;}
				if(!empty($_GET['series_id'])){$series_id=urldecode($_GET['series_id']);}
	    		$out.=ca_podcast_display($series_id,$file_id,$speaker_name);
				$out = apply_filters ( 'the_content', $out );
				*/
			break;
      case 'calendar':
						wp_enqueue_script('church_admin_calendar');
						if(empty($facilities_id))
						{
							$out.='<table><tr><td>'.__('Year Planner pdfs','church-admin').' </td><td>  <form name="guideform" action="'.$_SERVER['PHP_SELF'].'" method="get"><select name="guidelinks" onchange="window.location=document.guideform.guidelinks.options[document.guideform.guidelinks.selectedIndex].value"> <option selected="selected" value="">-- '.__('Choose a pdf','church-admin').' --</option>';
							for($x=0;$x<5;$x++)
							{
								$y=date('Y')+$x;
								$out.='<option value="'.home_url().'/?ca_download=yearplanner&amp;yearplanner='.wp_create_nonce('yearplanner').'&amp;year='.$y.'">'.$y.__('Year Planner','church-admin').'</option>';
							}
							$out.='</select></form></td></tr></table>';
						}
						if($style=='old')
						{
            		require_once(plugin_dir_path(__FILE__).'display/calendar.php');
            		$out.=church_admin_display_calendar($day_calendar);
            }
            else
            {
            	require_once(plugin_dir_path(__FILE__).'display/calendar.new.php');
            	$out.=church_admin_new_calendar_display('day',$day_calendar);
        		}
      break;
      case 'classes':
						wp_enqueue_script('jquery-ui-datepicker');
						require_once(plugin_dir_path(__FILE__).'display/classes.php');
        		$out.=church_admin_display_classes($today);
      break;
      case 'class':
						wp_enqueue_script('jquery-ui-datepicker');
			  		require_once(plugin_dir_path(__FILE__).'display/classes.php');
        		$out.=church_admin_display_class($class_id);
      break;
      case 'names':
				if(empty($loggedin)||is_user_logged_in())
				{
					require_once(plugin_dir_path(__FILE__).'/display/names.php');$out.=church_admin_names($member_type_id,$people_types);
				}
				else //login required
				{
					$out.='<div class="login"><h2>'.__('Please login','church-admin').'</h2>'.wp_login_form(array('echo'=>FALSE)).'</div>'.'<p><a href="'.wp_lostpassword_url(get_permalink() ).'" title="Lost Password">'.__('Help! I don\'t know my password','church-admin').'</a></p>';
				}
			break;

			case 'calendar-list':
            	require_once(plugin_dir_path(__FILE__).'/display/calendar-list.php');$out.=church_admin_calendar_list($days,$category);
      break;
      case 'directory':
			if(empty($loggedin)||is_user_logged_in())
			{

				if(!empty($pdf))$out.='<p><a href="'.home_url().'/?ca_download=addresslist&amp;addresslist='.wp_create_nonce('address-list','address-list').'&amp;member_type_id='.$member_type_id.'" target="_blank"><i class="fa  fa-3x fa-file-pdf-o"></i> '.__('PDF version','church-admin').'</a></p>';
	   			if(!empty($photo)&&!empty($pdf)) {
$out.='<p><a  target="_blank" href="'.home_url().'/?ca_download=addresslist-family-photos&amp;kids='.$kids.'&amp;addresslist='.wp_create_nonce('address-list','address-list' ).'&amp;member_type_id='.$member_type_id.'"><i class="fa  fa-3x fa-file-pdf-o"></i>'.__('PDF version with Family Photos','church-admin').'</a></p>';
	   			}


       		 	require_once(plugin_dir_path(__FILE__).'display/directory.php');
            	$out.=church_admin_frontend_people($member_type_id,$map,$photo,$api_key,$kids,$site_id);
				}
				else //login required
				{
					$out.='<div class="login"><h2>'.__('Please login','church-admin').'</h2>'.wp_login_form(array('echo'=>FALSE)).'</div>'.'<p><a href="'.wp_lostpassword_url(get_permalink() ).'" title="Lost Password">'.__('Help! I don\'t know my password','church-admin').'</a></p>';
				}
			break;
      case 'address-list':
			if(empty($loggedin)||is_user_logged_in())
			{

					if($style=='old')
				{
					if(!empty($pdf))$out.='Old<p><a  target="_blank" href="'.home_url().'/?ca_download=addresslist&amp;addresslist='.wp_create_nonce('member'.$member_type_id ).'&amp;member_type_id='.$member_type_id.'"><i class="fa fa-3x fa-file-pdf-o"></i> '.__('PDF version','church-admin').'</a></p>';
	   				if(!empty($photo)&&!empty($pdf))$out.='<p><a  target="_blank" href="'.home_url().'/?ca_download=address-list&amp;kids='.$kids.'&amp;addresslist='.wp_create_nonce('address-list','address-list').'&amp;member_type_id='.$member_type_id.'">'.__('PDF version with Photos','church-admin').'</a></p>';
            		require_once(plugin_dir_path(__FILE__).'display/address-list.old.php');
            		$out.=church_admin_frontend_directory($member_type_id,$map,$photo,$api_key,$kids,$site_id,$updateable);
	   			}
	   			else
	   			{
	   				if(!empty($pdf))$out.='<p class="ca-pdf-link"><a  target="_blank"   href="'.home_url().'/?ca_download=addresslist&amp;addresslist='.wp_create_nonce('address-list','address-list' ).'&amp;member_type_id='.$member_type_id.'"><i class="fa fa-3x fa-file-pdf-o"></i> '.__('PDF version','church-admin').'</a></p>';
	   				if(!empty($photo)&&!empty($pdf)){
	   					$out.='<p class="ca-pdf-link"><a  target="_blank" href="'.home_url().'/?ca_download=address-list&amp;kids='.$kids.'&amp;addresslist='.wp_create_nonce('address-list','address-list').'&amp;member_type_id='.$member_type_id.'">'.__('PDF version with People Photos','church-admin').'</a></p>';
	   					$out.='<p class="ca-pdf-link"><a  target="_blank" href="'.home_url().'/?ca_download=addresslist-family-photos&amp;kids='.$kids.'&amp;addresslist='.wp_create_nonce('address-list','address-list').'&amp;member_type_id='.$member_type_id.'">'.__('PDF version with Family Photos','church-admin').'</a></p>';
	   				}
            		require_once(plugin_dir_path(__FILE__).'display/address-list.php');
            		$out.=church_admin_frontend_directory($member_type_id,$map,$photo,$api_key,$kids,$site_id,$updateable,$first_initial);
            		$out.=' <a href="'.get_permalink().'?ca_refresh=TRUE">'.__("Refresh",'church-admin').'</a>';
        		}
				}
				else //login required
				{
					$out.='<div class="login"><h2>'.__('Please login','church-admin').'</h2>'.wp_login_form(array('echo'=>FALSE)).'</div>'.'<p><a href="'.wp_lostpassword_url(get_permalink() ).'" title="Lost Password">'.__('Help! I don\'t know my password','church-admin').'</a></p>';
				}
      break;
      case 'small-groups-list':
							//wp_enqueue_script('church_admin_google_maps_api');
							//wp_enqueue_script('church_admin_frontend_sg_map_script');

            	require_once(plugin_dir_path(__FILE__).'/display/small-group-list.php');
            	$out.= church_admin_small_group_list($map,$zoom);
      break;
			case 'small-groups':
					wp_enqueue_script('church_admin_google_maps_api');
					wp_enqueue_script('church_admin_frontend_sg_map_script');
	        require_once(plugin_dir_path(__FILE__).'/display/small-groups.php');
          $out.=church_admin_frontend_small_groups($member_type_id,$restricted);
      break;
			case 'map':$out.=church_admin_map($atts, $content);break;
			case 'register':$out.=church_admin_register($atts, $content);break;
      case 'ministries':
            	require_once(plugin_dir_path(__FILE__).'/display/ministries.php');
            	$out.=church_admin_frontend_ministries($ministry_id,$member_type_id);
      break;
      case 'my_rota':case 'my-rota':
				if(empty($loggedin)||is_user_logged_in())
				{
            	require_once(plugin_dir_path(__FILE__).'/display/rota.php');
            	$out.=church_admin_my_rota();
				}
				else //login required
				{
					$out.='<div class="login"><h2>'.__('Please login','church-admin').'</h2>'.wp_login_form(array('echo'=>FALSE)).'</div>'.'<p><a href="'.wp_lostpassword_url(get_permalink() ).'" title="Lost Password">'.__('Help! I don\'t know my password','church-admin').'</a></p>';
				}
			break;
			case 'rota':
				if(empty($loggedin)||is_user_logged_in())
				{
            	require_once(plugin_dir_path(__FILE__).'/display/rota.php');
            	if(!empty($_GET['date'])){$date=$_GET['date'];}else{$date=date('Y-m-d');}
            	$out.=church_admin_front_end_rota($service_id,$weeks,$pdf_font_resize,$date);
				}
				else //login required
				{
								$out.='<div class="login"><h2>'.__('Please login','church-admin').'</h2>'.wp_login_form(array('echo'=>FALSE)).'</div>'.'<p><a href="'.wp_lostpassword_url(get_permalink() ).'" title="Lost Password">'.__('Help! I don\'t know my password','church-admin').'</a></p>';
				}
			break;
      case 'rolling-average':
      case 'weekly-attendance':
      case 'monthly-attendance':
      case 'rolling-average-attendance':
			case 'graph':
					wp_enqueue_script('jquery-ui-datepicker');
					wp_enqueue_script('church_admin_google_graph_api');
				if(empty($width))$width=900;
				if(empty($height))$height=500;
				if(!empty($_POST['type']))
				{
					switch($_POST['type'])
					{
						case'weekly':$graphtype='weekly';break;
						case'rolling':$graphtype='rolling';break;
						default:$graphtype='weekly';break;
					}
				}else{$graphtype='weekly';}
				if(!empty($_POST['start'])){$start=$_POST['start'];}else{$start=date('Y-m-d',strtotime('-1 year'));}
				if(!empty($_POST['end'])){$end=$_POST['end'];}else{$end=date('Y-m-d');}
				if(!empty($_POST['service_id'])){$service_id=$_POST['service_id'];}else{$service_id='S/1';}

				require_once(plugin_dir_path(__FILE__).'display/graph.php');
				$out.=church_admin_graph($graphtype,$service_id,$start,$end,$width,$height,FALSE);
			break;
			case 'birthdays':
			if(empty($loggedin)||is_user_logged_in())
			{
				require_once(plugin_dir_path(__FILE__).'includes/birthdays.php');$out.=church_admin_frontend_birthdays($member_type_id, $days);
			}
			else //login required
			{
				$out.='<div class="login"><h2>'.__('Please login','church-admin').'</h2>'.wp_login_form(array('echo'=>FALSE)).'</div>'.'<p><a href="'.wp_lostpassword_url(get_permalink() ).'" title="Lost Password">'.__('Help! I don\'t know my password','church-admin').'</a></p>';
			}

			break;
			case 'restricted':
				//restricts content to certain member_type_ids
				if(!is_user_logged_in())
				{
						 $out.='<div class="login"><h2>'.__('Please login','church-admin').'</h2>'.wp_login_form(array('echo'=>FALSE)).'</div>'.'<p><a href="'.wp_lostpassword_url(get_permalink() ).'" title="Lost Password">'.__('Help! I don\'t know my password','church-admin').'</a></p>';
				}elseif(church_admin_user_member_level($member_type_id)){$out.=$content;}else{$out.=__('You are not permitted to view this content','church-admin');}
			break;
			default:
				if(empty($loggedin)||is_user_logged_in())
				{

						$out.='<p><a href="'.home_url().'/?ca_download=addresslist&amp;addresslist='.wp_create_nonce('member'.$member_type_id ).'&amp;member_type_id='.$member_type_id.'">'.__('PDF version','church-admin').'</a></p>';
        	    require_once(plugin_dir_path(__FILE__).'display/address-list.php');
         	   $out.=church_admin_frontend_directory($member_type_id,$map,$photo,$api_key,$kids,$site_id,$updateable);
					 }
					 else //login required
					 {
						 $out.='<div class="login"><h2>'.__('Please login','church-admin').'</h2>'.wp_login_form(array('echo'=>FALSE)).'</div>'.'<p><a href="'.wp_lostpassword_url(get_permalink() ).'" title="Lost Password">'.__('Help! I don\'t know my password','church-admin').'</a></p>';
					 }
       		break;
    	}

//output content instead of shortcode!
return $out;
}

add_shortcode('church_admin_unsubscribe','church_admin_unsubscribe');
function church_admin_unsubscribe()
{
	$out='<p>'.__('This shortcode is deprecated','church-admin').'</p>';
	return $out;
}
add_shortcode('church_admin_recent','church_admin_recent');
function church_admin_recent($atts, $content = null)
{
    extract(shortcode_atts(array('month'=>1), $atts));
    require_once(plugin_dir_path(__FILE__).'includes/recent.php');church_admin_recent_display($month);
}
add_shortcode("church_admin", "church_admin_shortcode");

add_shortcode("church_admin_map","church_admin_map");
function church_admin_map($atts, $content = null)
{
	global $wpdb;
		$out='';
	extract(shortcode_atts(array('loggedin'=>0), $atts));
	if(empty($loggedin)||is_user_logged_in())
	{
		wp_enqueue_script('church_admin_google_maps_api');
		wp_enqueue_script('church_admin_map');

    extract(shortcode_atts(array('zoom'=>13,'member_type_id'=>1,'small_group'=>1,'unattached'=>0), $atts));
    global $wpdb;

    $service=$wpdb->get_row('SELECT AVG(lat) AS lat,AVG(lng) AS lng FROM '.CA_SIT_TBL);
    $out.='<div class="church-map"><script type="text/javascript">var xml_url="'.site_url().'/?ca_download=address-xml&member_type_id='.esc_html($member_type_id).'&small_group='.esc_html($small_group).'&unattached='.esc_html($unattached).'&address-xml='.wp_create_nonce('address-xml').'";';
    $out.=' var lat='.esc_html($service->lat).';';
    $out.=' var lng='.esc_html($service->lng).';';
		$out.=' var zoom='.esc_html($zoom).';';
    $out.='jQuery(document).ready(function(){
    load(lat,lng,xml_url,zoom);});</script><div id="map"></div>';
    if(empty($small_group)){$out.='<div id="groups" style="display:none"></div>';}else{$out.='<div id="groups" ></div>';}
    $out.='</div>';
	}
	else {
		$out='<h3>'.__('You need to be logged in to view the map','church-admin').'</h3>'.wp_login_form(array('echo'=>false));
	}
    return $out;

}
add_shortcode("church_admin_register","church_admin_register");
function church_admin_register($atts, $content = null)
{
 		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_script('church_admin_form_clone');
		wp_enqueue_script('church_admin_google_map_api');
		//wp_enqueue_script('church_admin_map');
		wp_enqueue_script('church_admin_google_maps_api');
		wp_enqueue_script('church_admin_map_script');
		extract(shortcode_atts(array('email_verify'=>TRUE,'admin_email'=>TRUE,'user'=>FALSE,'member_type_id'=>1,'exclude'=>NULL), $atts));
    require_once(plugin_dir_path(__FILE__).'includes/front_end_register.php');
   	$noshow=array();
   	if(!empty($exclude))
	{
		$noshow=explode(",",$exclude);
	}
    $out=church_admin_front_end_register($user,$member_type_id,$noshow);
    return $out;
}

function church_admin_posts_logout()
{
    if ( isset( $_GET['church_admin_logout'] ) && ( 'posts_logout' == $_GET['church_admin_logout'] ) &&check_admin_referer( 'posts logout' ))
    {
	setcookie( 'wp-postpass_' . COOKIEHASH, ' ', time() - 31536000, COOKIEPATH );
	wp_redirect( wp_get_referer() );
	die();
    }
}


add_action( 'init', 'church_admin_posts_logout' );

//end of logout functions

function church_admin_calendar_widget($args)
{
    global $wpdb;

    extract($args);
    $options=get_option('church_admin_widget');
    $title=$options['title'];

    echo $before_widget;
    if ( $title )echo $before_title . $title . $after_title;

    echo church_admin_calendar_widget_output($options['events'],$options['postit'],$title);
    echo $after_widget;
}
function church_admin_widget_init()
{
    wp_register_sidebar_widget('Church-Admin-Calendar','Church Admin Calendar','church_admin_calendar_widget');
    require_once(plugin_dir_path(__FILE__).'includes/calendar_widget.php');
    wp_register_widget_control('Church-Admin-Calendar','Church Admin Calendar','church_admin_widget_control');
}
add_action('init','church_admin_widget_init');

function church_admin_birthday_widget($args)
{
    global $wpdb;

    extract($args);
	$options=get_option('church_admin_birthday_widget');

    $title=$options['title'];
	if(empty($options['member_type_id']))$options['member_type_id']=1;
	if(empty($options['days']))$options['days']=14;
	$out=church_admin_frontend_birthdays($options['member_type_id'], $options['days']);
   if(!empty($out))
   {
		echo $before_widget;
		if (!empty( $options['title']) )echo $before_title . $options['title'] . $after_title;
		require_once(plugin_dir_path(__FILE__).'includes/birthdays.php');
		echo $out;
		echo $after_widget;
	}
}
function church_admin_birthday_widget_init()
{
    wp_register_sidebar_widget('Church Admin Birthdays','Church Admin Birthdays','church_admin_birthday_widget');
    require_once(plugin_dir_path(__FILE__).'includes/birthdays.php');
    wp_register_widget_control('Church Admin Birthdays','Church Admin Birthdays','church_admin_birthday_widget_control');
}
add_action('init','church_admin_birthday_widget_init');
function church_admin_sermons_widget($args)
{
    global $wpdb;
	church_admin_latest_sermons_scripts();

    extract($args);
    $options=get_option('church_admin_latest_sermons_widget');
    $title=$options['title'];
	$limit=$options['sermons'];
    echo $before_widget;
    if ( $title )echo $before_title . esc_html($title) . $after_title;
	require_once(plugin_dir_path(__FILE__).'includes/sermon-podcast.php');
    echo church_admin_latest_sermons_widget_output($limit,$title);
    echo $after_widget;
}
function church_admin_sermons_widget_init()
{
    wp_register_sidebar_widget('Church-Admin-Latest-Sermons','Church Admin Latest Sermons','church_admin_sermons_widget');
    require_once(plugin_dir_path(__FILE__).'includes/sermon-podcast.php');
    wp_register_widget_control('Church-Admin-Latest-Sermons','Church Admin Latest Sermons','church_admin_latest_sermons_widget_control');


}
function church_admin_latest_sermons_scripts()
{
	$ajax_nonce = wp_create_nonce("church_admin_mp3_play");
	wp_enqueue_script('ca_podcast_audio',plugins_url('church-admin/includes/audio.min.js',dirname(__FILE__)),'',NULL);
	wp_enqueue_script('ca_podcast_audio_use',plugins_url('church-admin/includes/audio.use.js',dirname(__FILE__)),'',NULL);
	wp_localize_script( 'ca_podcast_audio_use', 'ChurchAdminAjax', array('security'=>$ajax_nonce, 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );

}
add_action('init','church_admin_sermons_widget_init');


function church_admin_download($file)
{
	$member_type_id=NULL;
	if(!empty($_GET['member_type_id']))$member_type_id=$_GET['member_type_id'];
	if(!empty($_GET['date'])){$date=$_GET['date'];}else{$date=date('Y-m-d');}
	if(!empty($_GET['pdf_font_resize'])){$resize=$_GET['pdf_font_resize'];}else{$resize=FALSE;}
	if(!empty($_GET['service_id'])){$service_id=intval($_GET['service_id']);}else{$service_id=1;}
	if(!empty($_GET['rota_id'])){$rota_id=$_GET['rota_id'];}else{$rota_id=NULL;}
	if(!empty($_GET['kids'])){$kids=$_GET['kids'];}else{$kids=FALSE;}

    switch($file)
    {
    	case'gdpr-pdf':if(church_admin_level_check('Directory'))require_once(plugin_dir_path(__FILE__).'includes/directory.php');church_admin_gdpr_pdf();break;
    	case'address-list':require_once(plugin_dir_path(__FILE__).'includes/pdf_creator.php');church_admin_photo_directory($member_type_id,$kids);break;
		case'kidswork_pdf':require_once(plugin_dir_path(__FILE__).'includes/pdf_creator.php');church_admin_kidswork_pdf($member_type_id);break;
		//Rotas
        case 'rotacsv':
        	if(wp_verify_nonce($_GET['_wpnonce'],'rotacsv'))
        	{
        		require_once(plugin_dir_path(__FILE__).'includes/rota.new.php');
        		require_once(plugin_dir_path(__FILE__).'includes/csv.php');
        		church_admin_rota_csv($service_id);
        	}else{echo'<p>You can only download if coming from a valid link</p>';}
        break;
		case'rota':
		case'horizontal_rota_pdf':
			require_once(plugin_dir_path(__FILE__).'includes/pdf_creator.php');
			church_admin_new_rota_pdf($service_id,$rota_id);
			break;
		/*case'rota':if(wp_verify_nonce($_GET['_wpnonce'],'rota')){require_once(plugin_dir_path(__FILE__).'includes/pdf_creator.php');church_admin_new_rota_pdf($service_id,$resize,$date);}else{echo'<p>You can only download if coming from a valid link</p>';}break;*/

		case 'hope_team_pdf':require_once(plugin_dir_path(__FILE__).'includes/pdf_creator.php');church_admin_hope_team_pdf();break;

		case'ministries_pdf':
			if(wp_verify_nonce($_GET['_wpnonce'],'ministries_pdf')){
				require_once(plugin_dir_path(__FILE__).'includes/pdf_creator.php');
				church_admin_ministry_pdf();
			}else{
				echo'<p>You can only download if coming from a valid link</p>';
			}
		break;
		case 'people-csv':
				if(wp_verify_nonce($_GET['people-csv'],'people-csv'))
				{
					require_once(plugin_dir_path(__FILE__).'includes/csv.php');
					church_admin_people_csv();
				}
				else
				{
					echo'<p>You can only download if coming from a valid link</p>';
				}
		break;
		case 'small-group-xml':
				if(wp_verify_nonce($_GET['small-group-xml'],'small-group-xml'))
				{

					require_once(plugin_dir_path(__FILE__).'includes/pdf_creator.php');
					church_admin_small_group_xml();
				}else{echo'<p>You can only download if coming from a valid link</p>';}
		break;
		case 'address-xml':

			require_once(plugin_dir_path(__FILE__).'includes/pdf_creator.php');
			church_admin_address_xml($_GET['member_type_id'],$_GET['small_group']);
		break;
        case'cron-instructions':if(wp_verify_nonce($_GET['cron-instructions'],'cron-instructions')){require_once(plugin_dir_path(__FILE__).'includes/pdf_creator.php');church_admin_cron_pdf();}else{echo'<p>You can only download if coming from a valid link</p>';}break;

        case'yearplanner':if(wp_verify_nonce($_GET['yearplanner'],'yearplanner')){require_once(plugin_dir_path(__FILE__).'includes/pdf_creator.php');church_admin_year_planner_pdf($_GET['year']);}else{echo'<p>You can only download if coming from a valid link</p>';}break;
			case'smallgroup':
				if(wp_verify_nonce($_GET['smallgroup'],'smallgroup'))
					{
						require_once(plugin_dir_path(__FILE__).'includes/pdf_creator.php');
						church_admin_smallgroup_pdf($_GET['member_type_id'],$_GET['people_type_id']);
					}
					else{echo'<p>You can only download if coming from a valid link</p>';}
			break;

			case'addresslist':
				if(wp_verify_nonce($_GET['addresslist'],'address-list'))
				{
					require_once(plugin_dir_path(__FILE__).'includes/pdf_creator.php');
					church_admin_address_pdf($_GET['member_type_id']);
				}else{echo'<p>You can only download if coming from a valid link</p>';}
			break;
case'addresslist-family-photos':
	if(wp_verify_nonce($_GET['addresslist'],'address-list'))
	{
		require_once(plugin_dir_path(__FILE__).'includes/pdf_creator.php');
		church_admin_address_pdf_family_photos($_GET['member_type_id']);
	}else{echo'<p>You can only download if coming from a valid link</p>';}
break;

		case'vcf':
			if(wp_verify_nonce($_GET['vcf'],$_GET['id']))
			{
				require_once(plugin_dir_path(__FILE__).'includes/pdf_creator.php');
				ca_vcard($_GET['id']);
			}else{echo'<p>You can only download if coming from a valid link</p>';}
		break;
		case'mailinglabel':if(church_admin_level_check('Directory')){require_once(plugin_dir_path(__FILE__).'includes/pdf_creator.php');church_admin_label_pdf();}break;


    }
}
function church_admin_delete_backup()
{
	$filename=get_option('church_admin_backup_filename');
	$upload_dir = wp_upload_dir();
	$path=$upload_dir['basedir'];
	if($filename&& file_exists($path.'/church-admin-cache/'.$filename))unlink($path.'/church-admin-cache/'.$filename);
	update_option('church_admin_backup_filename',"");
}
function church_admin_backup()
{
    global $church_admin_version,$wpdb;
    $content='';
    if ($wpdb->get_var('SHOW TABLES LIKE "'.CA_ATT_TBL.'"') == CA_ATT_TBL)$content.=church_admin_datadump (CA_ATT_TBL);
    if ($wpdb->get_var('SHOW TABLES LIKE "'.CA_BIB_TBL.'"') == CA_BIB_TBL)$content.=church_admin_datadump (CA_BIB_TBL);
     if ($wpdb->get_var('SHOW TABLES LIKE "'.CA_BRP_TBL.'"') == CA_BRP_TBL)$content.=church_admin_datadump (CA_BRP_TBL);
    if ($wpdb->get_var('SHOW TABLES LIKE "'.CA_CAT_TBL.'"') == CA_CAT_TBL)$content.=church_admin_datadump (CA_CAT_TBL);
     if ($wpdb->get_var('SHOW TABLES LIKE "'.CA_CLA_TBL.'"') == CA_CLA_TBL)$content.=church_admin_datadump (CA_CLA_TBL);
     if ($wpdb->get_var('SHOW TABLES LIKE "'.CA_COM_TBL.'"') == CA_COM_TBL)$content.=church_admin_datadump (CA_COM_TBL);
		 if ($wpdb->get_var('SHOW TABLES LIKE "'.CA_CUST_TBL.'"') == CA_CUST_TBL)$content.=church_admin_datadump (CA_CUST_TBL);
     if ($wpdb->get_var('SHOW TABLES LIKE "'.CA_DATE_TBL.'"') == CA_DATE_TBL)$content.=church_admin_datadump (CA_DATE_TBL);
     if ($wpdb->get_var('SHOW TABLES LIKE "'.CA_EVE_TBL.'"') == CA_EVE_TBL)$content.=church_admin_datadump (CA_EVE_TBL);
     if ($wpdb->get_var('SHOW TABLES LIKE "'.CA_BOO_TBL.'"') == CA_BOO_TBL)$content.=church_admin_datadump (CA_BOO_TBL);
     if ($wpdb->get_var('SHOW TABLES LIKE "'.CA_TIK_TBL.'"') == CA_TIK_TBL)$content.=church_admin_datadump (CA_TIK_TBL);
if ($wpdb->get_var('SHOW TABLES LIKE "'.CA_METRICS_TBL.'"') == CA_METRICS_TBL)$content.=church_admin_datadump (CA_METRICS_TBL);
if ($wpdb->get_var('SHOW TABLES LIKE "'.CA_METRICS_META_TBL.'"') == CA_METRICS_META_TBL)$content.=church_admin_datadump (CA_METRICS_META_TBL);
     if ($wpdb->get_var('SHOW TABLES LIKE "'.CA_EBU_TBL.'"') == CA_EBU_TBL)$content.=church_admin_datadump (CA_EBU_TBL);
	if ($wpdb->get_var('SHOW TABLES LIKE "'.CA_EMA_TBL.'"') == CA_EMA_TBL)$content.=church_admin_datadump (CA_EMA_TBL);
    if ($wpdb->get_var('SHOW TABLES LIKE "'.CA_FIL_TBL.'"') == CA_FIL_TBL)$content.=church_admin_datadump (CA_FIL_TBL);
    if ($wpdb->get_var('SHOW TABLES LIKE "'.CA_FAC_TBL.'"') == CA_FAC_TBL)$content.=church_admin_datadump (CA_FAC_TBL);
	if ($wpdb->get_var('SHOW TABLES LIKE "'.CA_FP_TBL.'"') == CA_FP_TBL)$content.=church_admin_datadump (CA_FP_TBL);
    if ($wpdb->get_var('SHOW TABLES LIKE "'.CA_FUN_TBL.'"') == CA_FUN_TBL)$content.=church_admin_datadump (CA_FUN_TBL);
    if ($wpdb->get_var('SHOW TABLES LIKE "'.CA_HOU_TBL.'"') == CA_HOU_TBL)$content.=church_admin_datadump (CA_HOU_TBL);
    if ($wpdb->get_var('SHOW TABLES LIKE "'.CA_HOP_TBL.'"') == CA_HOP_TBL)$content.=church_admin_datadump (CA_HOP_TBL);
    if ($wpdb->get_var('SHOW TABLES LIKE "'.CA_IND_TBL.'"') == CA_IND_TBL)$content.=church_admin_datadump (CA_IND_TBL);
    if ($wpdb->get_var('SHOW TABLES LIKE "'.CA_KID_TBL.'"') == CA_KID_TBL)$content.=church_admin_datadump (CA_KID_TBL);
    if ($wpdb->get_var('SHOW TABLES LIKE "'.CA_MET_TBL.'"') == CA_MET_TBL)$content.=church_admin_datadump (CA_MET_TBL);
    if ($wpdb->get_var('SHOW TABLES LIKE "'.CA_MTY_TBL.'"') == CA_MTY_TBL)$content.=church_admin_datadump (CA_MTY_TBL);
    if ($wpdb->get_var('SHOW TABLES LIKE "'.CA_PEO_TBL.'"') == CA_PEO_TBL)$content.=church_admin_datadump (CA_PEO_TBL);
    if ($wpdb->get_var('SHOW TABLES LIKE "'.CA_ROT_TBL.'"') == CA_ROT_TBL)$content.=church_admin_datadump (CA_ROT_TBL);
     if ($wpdb->get_var('SHOW TABLES LIKE "'.CA_ROTA_TBL.'"') == CA_ROTA_TBL)$content.=church_admin_datadump (CA_ROTA_TBL);
    if ($wpdb->get_var('SHOW TABLES LIKE "'.CA_RST_TBL.'"') == CA_RST_TBL)$content.=church_admin_datadump (CA_RST_TBL);
    if ($wpdb->get_var('SHOW TABLES LIKE "'.CA_SERM_TBL.'"') == CA_SERM_TBL)$content.=church_admin_datadump (CA_SERM_TBL);
    if ($wpdb->get_var('SHOW TABLES LIKE "'.CA_SER_TBL.'"') == CA_SER_TBL)$content.=church_admin_datadump (CA_SER_TBL);
    if ($wpdb->get_var('SHOW TABLES LIKE "'.CA_SERM_TBL.'"') == CA_SERM_TBL)$content.=church_admin_datadump (CA_SERM_TBL);
    if ($wpdb->get_var('SHOW TABLES LIKE "'.CA_SMG_TBL.'"') == CA_SMG_TBL)$content.=church_admin_datadump (CA_SMG_TBL);
    if ($wpdb->get_var('SHOW TABLES LIKE "'.CA_SIT_TBL.'"') == CA_SIT_TBL)$content.=church_admin_datadump (CA_SIT_TBL);
    if ($wpdb->get_var('SHOW TABLES LIKE "'.CA_SES_TBL.'"') == CA_SES_TBL)$content.=church_admin_datadump (CA_SES_TBL);
    if ($wpdb->get_var('SHOW TABLES LIKE "'.CA_SMET_TBL.'"') == CA_SMET_TBL)$content.=church_admin_datadump (CA_SMET_TBL);
    if ($wpdb->get_var('SHOW TABLES LIKE "'.CA_MIN_TBL.'"') == CA_MIN_TBL)$content.=church_admin_datadump (CA_MIN_TBL);

    if(defined(OLD_CHURCH_ADMIN_VERSION))$content.='UPDATE '.$wpdb->prefix.'options SET option_value="'.OLD_CHURCH_ADMIN_VERSION.'" WHERE option_name="church_admin_version";'."\r\n";
    $sql='SELECT option_name, option_value FROM '.$wpdb->options.' WHERE `option_name` LIKE  "church%"';

    $options=$wpdb->get_results($sql);

    if(!empty($options))
    {
    	foreach($options AS $option)
    	{
    		$content.='DELETE FROM '.$wpdb->prefix.'options WHERE option_name="'.esc_sql($option->option_name).'";'."\r\n";
    		$content.='INSERT INTO  '.$wpdb->prefix.'options (option_name,option_value)VALUES("'.esc_sql($option->option_name).'","'.esc_sql($option->option_value).'");'."\r\n";
    	}
    }
	$length = 10;
	$randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
	$filename=md5($randomString).'.sql.gz';
	update_option('church_admin_backup_filename',$filename);
	$upload_dir = wp_upload_dir();
	$path=$upload_dir['basedir'];
    if(!empty($content))
    {
		$gzdata = gzencode($content);
		$loc=$path.'/church-admin-cache/'.$filename;
		$fp = fopen($loc, 'w');
		fwrite($fp, $gzdata);
		fclose($fp);
	}

}
function church_admin_datadump ($table) {

	global $wpdb;

	$sql="select * from `$table`";
	$tablequery = $wpdb->get_results($sql,ARRAY_N);
	$num_fields=$wpdb->num_rows +1;

	if(!empty($tablequery))
	{

	    $result = "# Dump of $table \r\n";
	    $result .= "# Dump DATE : " . date("d-M-Y") ."\r\n";

	    $increment = $num_fields+1;
	    //build table structure
	    $sql = "SHOW COLUMNS FROM `$table`";
	    $query=$wpdb->get_results($sql);
	    if(!empty($query))
	    {
		$result.="DROP TABLE IF EXISTS `$table`;\r\n CREATE TABLE IF NOT EXISTS `$table` (";
		foreach($query AS $row)
		{
		    $result.="`{$row->Field}` {$row->Type} ";
		    if(isset($row->NULL)){$result.=" NULL ";}else {$result.=" NOT NULL ";}
		    if($row->Key=='PRI'){$key=$row->Field;}
		    if(!empty($row->Default))
		    {
						$result.=" default '".$row->Default."'";}
		    }
		    if(!empty($row->Extra)) $result.=' '.$row->Extra;
		    $result.=',';

	  }
	    $result.="PRIMARY KEY (`{$key}`)) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=".$increment." ;\r\n";
	    $result.="-- \r\n -- Dumping data for table `$table`\r\n--\r\n";
	    //build insert for table
	    $result.="-- \r\n -- Dumping data for table `$table`\r\n--\r\n";

	    foreach($tablequery AS $row)
	    {

		$result .= "INSERT INTO `".$table."` VALUES(";
		for($j=0; $j<count($row); $j++)
		{
		    $row[$j] = addslashes($row[$j]);
		    $row[$j] = str_replace("\n","\\n",$row[$j]);
		    if (isset($row[$j])) $result .= "'{$row[$j]}'" ; else $result .= "''";
		    if ($j<(count($row)-1)) $result .= ",";
		}
		$result .= ");\r\n";
	    }
	    	return $result;
	}
}

 function church_admin_activation_log_clear(){delete_option('church_admin_plugin_error');church_admin_front_admin();}



// Add a new interval of a week
// See http://codex.wordpress.org/Plugin_API/Filter_Reference/cron_schedules
add_filter( 'cron_schedules', 'church_admin_add_weekly_cron_schedule' );
function church_admin_add_weekly_cron_schedule( $schedules ) {
    $schedules['weekly'] = array(
        'interval' => 604800, // 1 week in seconds
        'display'  => __( 'Once Weekly' ),
    );

    return $schedules;
}
if(!empty($_POST['email_rota_day']))
{
	$service_id=intval($_POST['service_id']);

	$en_rota_days=array(1=>'Monday',2=>'Tuesday',3=>'Wednesday',4=>'Thursday',5=>'Friday',6=>'Saturday',7=>'Sunday');
	$email_day=(int)$_POST['email_rota_day'];
	$message=stripslashes($_POST['auto-rota-message']);
	$args=array('service_id'=>intval($service_id),'message'=>$message);
	//update_option('church_admin_auto_rota_email_message',$message);

		update_option('church_admin_email_rota_day',$email_day);
		$first_run = strtotime($en_rota_days[$email_day]);
		wp_schedule_event($first_run, 'weekly','church_admin_cron_email_rota',$args);



}
add_action('church_admin_cron_email_rota','church_admin_auto_email_rota',1,2);
   /**
 *
 * Cron email rota
 *
 * @author  Andy Moyle
 * @param    $service_id
 * @return   string
 * @version  0.1
 *
 */
function church_admin_auto_email_rota($service_id,$message=NULL)
{
    global $wpdb,$wp_locale;
		if(defined('CA_DEBUG'))church_admin_debug("Cron email of rota fired".print_r($message,TRUE));
  	if(empty($service_id))return FALSE;


		//get required task for service_id
		$rota_tasks=$wpdb->get_results('SELECT * FROM '.CA_RST_TBL.' ORDER BY rota_order');
		$requiredRotaJobs=$rotaDates=array();
		foreach($rota_tasks AS $rota_task)
		{
			$allServiceID=maybe_unserialize($rota_task->service_id);
			if(is_array($allServiceID)&&in_array($service_id,$allServiceID))$requiredRotaJobs[$rota_task->rota_id]=$rota_task->rota_task;
		}

		//next service
		$sql='SELECT rota_date FROM '.CA_ROTA_TBL.' WHERE service_id="'.intval($service_id).'" AND mtg_type="service" AND rota_date>=CURDATE() GROUP BY rota_date ORDER by rota_date ASC  LIMIT 1';

		$rota_date=$wpdb->get_var($sql);
		//all jobs from next service
		$sql='SELECT * FROM '.CA_ROTA_TBL.' WHERE service_id="'.intval($service_id).'" AND mtg_type="service" AND rota_date="'.esc_sql($rota_date).'"';
		if(defined('CA_DEBUG'))church_admin_debug($sql);
		$results=$wpdb->get_results($sql);

		$allPeople=array();//array of all people involved in service
		$rotaTable='';
		foreach($results AS $row)
		{
			$people=church_admin_rota_people_array($row->rota_date,$row->rota_task_id,$service_id,'service');
			if(defined('CA_DEBUG'))church_admin_debug("People on rota\r\n".print_r($people,TRUE));
			foreach($people AS $people_id=>$name)if(is_numeric($people_id)&&!in_array($people_id,$allPeople))$allPeople[$people_id]=$name;
			$rotaTable.='<tr><td>'.$requiredRotaJobs[$row->rota_task_id].'</td><td>'.esc_html(implode(", ",$people)).'</td></tr>';

		}
		//Title
		$service=$wpdb->get_row('SELECT * FROM '.CA_SER_TBL.' WHERE service_id="'.intval($service_id).'"');

		$title='<h3>'.__('Rota for','church-admin').' '.esc_html($service->service_name).' '.__('on','church-admin').' '.$wp_locale->get_weekday($service->service_day).' '.__('at','church-admin').' '.esc_html($service->service_time).' '.esc_html($service->venue).'</h3>';

		$out=$title.$message.'<table>'.$rotaTable.'</table>';

		if(defined('CA_DEBUG'))church_admin_debug("Cron email of rota ".date('Y-m-d h:i:s')."\r\n".$out);
		$allPeople=array_filter($allPeople);
		if(defined('CA_DEBUG'))church_admin_debug("All people array \r\n".print_r($allPeople,TRUE));
		foreach($allPeople AS $ID=>$name)
		{
			$email=$wpdb->get_var('SELECT email FROM '.CA_PEO_TBL.' WHERE people_id="'.intval($ID).'"');
			if(!empty($email))
			{

				add_filter('wp_mail_content_type','church_admin_email_type');
				add_filter( 'wp_mail_from_name', 'church_admin_from_name');
				add_filter( 'wp_mail_from', 'church_admin_from_email');
				if(!wp_mail($email,strip_tags($title),$out))
				{
					if(defined('CA_DEBUG'))church_admin_debug("Cron email failure\r\n".$_GLOBALS['phpmailer']->ErrorInfo);
				}
				else
				{
						if(defined('CA_DEBUG'))church_admin_debug('Sent to '.$email);
				}

				remove_filter('wp_mail_content_type','church_admin_email_type');
			}
		}
		exit();
}
function church_admin_from_name( $from ) {if(!empty($_POST['from_name'])){return esc_html(stripslashes($_POST['from_name']));}else return get_option('blogname');}
function church_admin_from_email( $email ) {if(!empty($_POST['from_email'])){return esc_html(stripslashes($_POST['from_email']));}else return get_option('admin_email');}
function church_admin_debug($message)
{
	$upload_dir = wp_upload_dir();
	$debug_path=$upload_dir['basedir'].'/church-admin-cache/';
	if(file_exists($debug_path.'debug.log'))unlink($debug_path.'debug.log');
	if(!file_exists($debug_path.'debug_log.php'))
	{

		$text="<?php exit('God is good and you are not, because you are acting like a hacker.'); \r\n // Nothing is good! ";
		$fp = fopen($debug_path.'debug_log.php', 'w');
		fwrite($fp, $text."\r\n");
	}
	if(empty($fp))$fp = fopen($debug_path.'debug_log.php', 'a');
    fwrite($fp, $message."\r\n");
    fclose($fp);
}

register_deactivation_hook(__FILE__, 'church_admin_deactivation');

function church_admin_deactivation() {
	wp_clear_scheduled_hook('church_admin_bulk_email');
}
add_action('church_admin_bulk_email','church_admin_bulk_email');
function church_admin_bulk_email()
{

	global $wpdb;

	$max_email=get_option('church_admin_bulk_email');

	if(empty($max_email))$max_email=100;
	$sql='SELECT * FROM '.CA_EMA_TBL.' WHERE schedule="0000-00-00" OR schedule <=DATE(NOW()) LIMIT 0,'.$max_email;

	$result=$wpdb->get_results($sql);

	if(!empty($result))
	{
		foreach($result AS $row)
		{
			$headers="From: ".$row->from_name." <".$row->from_email.">\n";
			add_filter('wp_mail_content_type','church_admin_email_type');
			$email=$row->from_email;
			$from=$row->from_name;
			add_filter( 'wp_mail_from_name', 'church_admin_from_name');
			add_filter( 'wp_mail_from', 'church_admin_from_email');
			if(wp_mail($row->recipient,$row->subject,$row->message,$headers,unserialize($row->attachment)))
			{

				$wpdb->query('DELETE FROM '.CA_EMA_TBL.' WHERE email_id="'.esc_sql($row->email_id).'"');
			}else {if(defined('CA_DEBUG'))church_admin_debug( $_GLOBALS['phpmailer']->ErrorInfo);}
			remove_filter('wp_mail_content_type','church_admin_email_type');
		}
	}
}

//add donate link on config page
add_filter( 'plugin_row_meta', 'church_admin_plugin_meta_links', 10, 2 );
function church_admin_plugin_meta_links( $links, $file ) {
	$plugin = plugin_basename(__FILE__);
	// create link
	if ( $file == $plugin ) {
		return array_merge(
			$links,
			array( '<a href="http://www.churchadminplugin.com/support">Support</a>','<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=7WWB7SQCRLUJ4">Donate</a>' )
		);
	}
	return $links;
}



/**
 *
 * Send out Prayer Request Post to the prayer chain
 *
 * @author  Andy Moyle
 * @param    null
 * @return   html
 * @version  0.1
 *
 */


add_action( 'transition_post_status', 'church_admin_prayer_request_email', 10, 3 );

function church_admin_prayer_request_email( $new_status, $old_status, $post ) {
	$debug=FALSE;//stop push notifications while testing
	global $wpdb;
	 $type=get_post_type( $post );
     if($new_status == 'publish' && $old_status != 'publish' && !empty($type) && ($type=='prayer-requests'||$type=='post'||$type=='bible-readings')&&$_SERVER['SERVER_NAME']!="localhost")
     {
        if(!empty($debug))church_admin_debug('Custom post type published -'.$type);
				//app
				$api_key=get_option('church_admin_app_api_key');
				$app_id=get_option('church_admin_app_id');
				switch($type)
				{
					case 'prayer-requests':$title=__('New Prayer Request','church-admin');$contactType='prayer';$ministry=__('Prayer requests send','church-admin');break;
					case 'bible-readings':$title=__('New Bible Reading','church-admin');$contactType='bible';$ministry=__('Bible readings send','church-admin');break;
					case 'post':$title=__('New Blog Post','church-admin');$contactType='news';$ministry=__('News send','church-admin');break;
				}
				if(!empty($api_key)&&empty($debug)&&$_SERVER['SERVER_NAME']!="localhost")
				{// prep the bundle
			 		$url = 'https://fcm.googleapis.com/fcm/send';

   				$headers = array
					(
						'Authorization: key=' . $api_key,
						'Content-Type: application/json'
					);

					$data=array(

							"notification"=>array(
								"title"=>"Church App",
								"body"=>$title." - ".$post->post_title,
								"sound"=>"default",
								"click_action"=>"FCM_PLUGIN_ACTIVITY",
								"icon"=>"fcm_push_icon",
								"content_available"=> true
							),
  					"data"=>array(
  					"title"=>"Church App",
						"body"=>$title." - ".$post->post_title,
						"type"=>$contactType
  				),
  				"to"=>"/topics/church".$app_id,

    			"priority"=>"high",
    			//"restricted_package_name"=>""
				);


			$ch = curl_init ();
    		curl_setopt ( $ch, CURLOPT_URL, $url );
    		curl_setopt ( $ch, CURLOPT_POST, true );
    		curl_setopt ( $ch, CURLOPT_HTTPHEADER, $headers );
    		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
	    	curl_setopt ( $ch, CURLOPT_POSTFIELDS, json_encode($data) );

    		$result = curl_exec ( $ch );
    		//echo $result;

    		curl_close ( $ch );
		}

		//prayer chain emails
		$post_title = get_the_title( $post->ID );
		$post_url = get_permalink( $post->ID );

		$email_title=$title.' - '.$post->post_title;
		$content_post = get_post($post->ID);
		$content = $content_post->post_content;
		$content = apply_filters('the_content', $content);
		$content = str_replace(']]>', ']]&gt;', $content);
		if($type=='bible-readings')
		{
			$version=get_option('church_admin_bible_version');
        	$passage=get_post_meta( $post->ID ,'bible-passage',TRUE);
        	if(!empty($debug))church_admin_debug('Passage:'.$passage);
        	$custom_content ='<div class="ca-bible-date">'.get_the_date().'</div>';
			if(!empty($passage))$custom_content .= '<p class="ca-bible-reading"><a href="https://www.biblegateway.com/passage/?search='.urlencode($passage).'&version='.urlencode($version).'&interface=print" target="_blank" >'.esc_html($passage).'</a></p>';

			$content=$custom_content.$content;
			$email_title=__('New Bible Reading','church-admin').' - '.$passage;
		}

		$current_user=wp_get_current_user();
		$user=$wpdb->get_row('SELECT CONCAT_WS(" ",first_name,prefix,last_name) AS name, email FROM '.CA_PEO_TBL.' WHERE user_id="'.intval($current_user->ID).'"');

		$MailChimpSettings=get_option('church_admin_mailchimp');
		if(empty($MailChimpSettings))
		{

			//prepare send
			//$sql='SELECT  DISTINCT email,CONCAT_WS(" ",first_name,last_name) AS name FROM '.CA_PEO_TBL.' WHERE  prayer_chain=1 AND email!=""';
			//FROM v1.2608 prayer requests and bible readings are ministries and people who get them in church_admin_people_meta
			$ministryID=$wpdb->get_var('SELECT ID FROM '.CA_MIN_TBL.' WHERE ministry="'.esc_sql($ministry).'"');
			$sql='SELECT a.email,CONCAT_WS(" ",a.first_name,a.last_name) AS name FROM '.CA_PEO_TBL.' a, '.CA_MET_TBL.' b WHERE a.people_id=b.people_id AND b.meta_type="ministry" AND b.ID="'.intval($ministryID).'" AND a.email!="" AND email_send!=0 AND gdpr_reason!=""';

			$results=$wpdb->get_results($sql);
			foreach($results AS $row)
			{
				if(get_option('church_admin_cron')!='immediate')
       	 	{
							QueueEmail($row->email, $title,'<h2>'.$email_title.'</h2>'.$content,NULL,$user->name,$user->email,'');
							if(!empty($debug))church_admin_debug("Prayer chain to ".$row->email.' '.date('Y-m-d h:i:s'));
					}
					else
					{
							add_filter('wp_mail_content_type','church_admin_email_type');
							add_filter( 'wp_mail_from_name', 'church_admin_from_name');
							add_filter( 'wp_mail_from', 'church_admin_from_email');
							if(!wp_mail($row->email,$email_title,'<h2>'.$email_title.'</h2>'.$content))
							{
								if(!empty($debug))church_admin_debug("Prayer Chain email failure\r\n");
							}
							else{if(!empty($debug))church_admin_debug("Prayer chain to ".$row->email);}
							remove_filter('wp_mail_content_type','church_admin_email_type');
					}
			}

		}//end use native mail
		else
		{

			$mailChimpInterests=get_option('church_admin_MailChimpInterests');


			require_once(plugin_dir_path(dirname(__FILE__)).'church-admin/includes/mailchimp.inc.php');
			$MailChimp = new MailChimp($MailChimpSettings['api_key']);
			$MailChimp->verify_ssl = 'false';
			$segment_opts =
			array(
						'match' => 'any', // or 'all' or 'none'
						'conditions' => array (
    						array(
        					'condition_type' => 'Interests', // note capital I
        					'field' => 'interests-'.$MailChimpSettings['ministry_id'], // ID of interest category
                                           // This ID is tricky: it is
                                           // the string "interests-" +
                                           // the ID of interest category
                                           // that you get from MailChimp
                                           // API (31f7aec0ec)
        					'op' => 'interestcontains', // or interestcontainsall, interestcontainsnone
        					'value' => array (
            					$mailChimpInterests[__('Ministries','church-admin')][$ministry]
        					)
    					)
  					)
			);
			if(empty($user->email))
			{
				$user= new stdClass();
				$user->email=get_option('admin_email');
				$user->name=get_option('blogname');
			}
			$result = $MailChimp->post("campaigns", Array(
	    'type' => 'regular',
	    'recipients' => array('list_id' =>$MailChimpSettings['listID'],'segment_opts'=>$segment_opts),
	    'settings' => array('subject_line' => $email_title,
						'reply_to' => $user->email,
	           'from_name' => $user->name
					 )
	    ));
			if (!$MailChimp->success()&&defined('CA_DEBUG')) {church_admin_debug( "Post Campaign Error\r\n".$MailChimp->getLastError());}
			$response = $MailChimp->getLastResponse();
			$responseObj = json_decode($response['body']);

			$result = $MailChimp->put('campaigns/' . $responseObj->id . '/content', array('html' =>  $content));
			if (!$MailChimp->success()&&defined('CA_DEBUG')) {church_admin_debug( "Put Campaign Error\r\n".$MailChimp->getLastError());}

			$result = $MailChimp->post('campaigns/' . $responseObj->id . '/actions/send');
			if (!$MailChimp->success()&&defined('CA_DEBUG')) {church_admin_debug( "Send Campaign Error\r\n".$MailChimp->getLastError());}

		}
	} //just published
}

function ca_prayer_create_posttype() {
$labels = array(
		'name'                => _x( 'Prayer Requests', 'Post Type General Name', 'church-admin' ),
		'singular_name'       => _x( 'Prayer Request', 'Post Type Singular Name', 'church-admin' ),
		'menu_name'           => __( 'Prayer Requests', 'church-admin' ),
		'parent_item_colon'   => __( 'Parent Prayer Request', 'church-admin' ),
		'all_items'           => __( 'All Prayer Requests', 'church-admin' ),
		'view_item'           => __( 'View Prayer Request', 'church-admin' ),
		'add_new_item'        => __( 'Add New Prayer Request', 'church-admin' ),
		'add_new'             => __( 'Add New', 'church-admin' ),
		'edit_item'           => __( 'Edit Prayer Request', 'church-admin' ),
		'update_item'         => __( 'Update Prayer Request', 'church-admin' ),
		'search_items'        => __( 'Search Prayer Requests', 'church-admin' ),
		'not_found'           => __( 'Not Found', 'church-admin' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'church-admin' ),
	);

	register_post_type( 'prayer-requests',
	// CPT Options
		array(
			'labels' => $labels,
			'public' => true,
			'exclude_from_search'=>false,
			'has_archive' => true,
			'publicly_queryable'=>true,
			'show_ui'=>true,
			'show_in_menu' => true,

			'supports' => array( 'thumbnail','title','editor' )
		)
	);
}

add_action( 'init', 'ca_prayer_create_posttype' );



/****************************
*
*
* Bible Reading Plan
*
*
*****************************/

function ca_bible_reading_create_posttype() {
$labels = array(
		'name'                => _x( 'Bible Readings', 'Post Type General Name', 'church-admin' ),
		'singular_name'       => _x( 'Bible Reading', 'Post Type Singular Name', 'church-admin' ),
		'menu_name'           => __( 'Bible Readings', 'church-admin' ),
		'parent_item_colon'   => __( 'Parent Bible Reading', 'church-admin' ),
		'all_items'           => __( 'All Bible Readings', 'church-admin' ),
		'view_item'           => __( 'View Bible Reading', 'church-admin' ),
		'add_new_item'        => __( 'Add New Bible Reading', 'church-admin' ),
		'add_new'             => __( 'Add New', 'church-admin' ),
		'edit_item'           => __( 'Edit Bible Reading', 'church-admin' ),
		'update_item'         => __( 'Update Bible Reading', 'church-admin' ),
		'search_items'        => __( 'Search Bible Readings', 'church-admin' ),
		'not_found'           => __( 'Not Found', 'church-admin' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'church-admin' ),

	);

	register_post_type( 'bible-readings',
	// CPT Options
		array(
			'labels' => $labels,
			'public' => true,
			'exclude_from_search'=>false,
			'has_archive' => true,
			'publicly_queryable'=>true,
			'show_ui'=>true,
			'supports' => array( 'thumbnail','title','editor','comments' ),
			'show_in_menu'        => TRUE,
			'show_in_nav_menus'   => TRUE
		)
	);
}

add_action( 'init', 'ca_bible_reading_create_posttype' );

/**
 * Adds a meta box to the post editing screen
 */
function ca_brp_custom_meta() {
    add_meta_box( 'ca_brp_meta', __( 'Scripture', 'church-admin' ), 'ca_brp_meta_callback', 'bible-readings','advanced','high' );
}
add_action( 'add_meta_boxes', 'ca_brp_custom_meta' );
add_action('edit_form_after_title',  'ca_move_metabox_after_title'  );

function ca_move_metabox_after_title () {
    global $post, $wp_meta_boxes;

    do_meta_boxes( get_current_screen(), 'advanced', $post );
    unset( $wp_meta_boxes[get_post_type( $post )]['advanced'] );
}
/**
 * Outputs the content of the meta box
 */
function ca_brp_meta_callback( $post ) {
    wp_nonce_field( basename( __FILE__ ), 'ca_brp__nonce' );
    $stored_meta = get_post_meta( $post->ID ,'bible-passage',TRUE);
    ?>

    <p>
        <label for="meta-text" class="ca_brp_-row-title"><?php _e( 'Bible Passage', 'church-admin' )?></label>
        <input type="text" name="meta-text" class="large-text" id="meta-text" value="<?php if ( isset ( $stored_meta ) ) echo $stored_meta; ?>" />
    </p>

    <?php
}

/**
 * Saves the custom meta input
 */
function ca_brp__meta_save( $post_id ) {

    // Checks save status
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    $is_valid_nonce = ( isset( $_POST[ 'ca_brp__nonce' ] ) && wp_verify_nonce( $_POST[ 'ca_brp__nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';

    // Exits script depending on save status
    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
        return;
    }

    // Checks for input and sanitizes/saves if needed
    if( isset( $_POST[ 'meta-text' ] ) ) {
        update_post_meta( $post_id, 'bible-passage', sanitize_text_field( $_POST[ 'meta-text' ] ) );
    }

}
add_action( 'save_post', 'ca_brp__meta_save' );

function church_admin_no_restricted($search,$wp_query)
{
	global $wpdb;
 if ( empty( $search ) )
  return $search;
 $q = $wp_query->query_vars;
 $n = ! empty( $q['exact'] ) ? '' : '%';
 //$search ='';
 $searchand = ' AND ';
 foreach ( (array) $q['search_terms'] as $term ) {
  $term = esc_sql( $wpdb->esc_like( $term ) );
  $search .= "{$searchand}($wpdb->posts.post_content NOT LIKE '%type=\"restricted\"%')";
  $searchand = ' AND ';
 }
 return $search;
}
add_filter( 'posts_search', 'church_admin_no_restricted', 500, 2 );

function ca_bible_reading_passage( $content ) {

//this function prepends the passage to content for bible readings
	global $post;
    if ( is_single() && 'bible-readings' == get_post_type() ) {
        $version=get_option('church_admin_bible_version');
        $passage=get_post_meta( $post->ID ,'bible-passage',TRUE);
        $dayNo=get_the_date('z')+1;
        $custom_content ='<div class="ca-bible-date">'.get_the_date().' '.__('Day','church-admin').' '.$dayNo.'</div>';
		if(!empty($passage))$custom_content .= '<p class="ca-bible-reading"><a href="https://www.biblegateway.com/passage/?search='.urlencode($passage).'&version='.urlencode($version).'&interface=print" target="_blank" >'.esc_html($passage).'</a></p>';
        $custom_content .= $content;
        return $custom_content;
    } else {
        return $content;
    }
}
add_filter( 'the_content', 'ca_bible_reading_passage' );
/****************************
*
*
* Ajax operations
*
*
*****************************/





add_action('wp_ajax_church_admin_rota_dates','church_admin_ajax_rota_dates');
function church_admin_ajax_rota_dates()
{
	global $wpdb;
	//check_admin_referer('church_admin_rota_dates','nonce');
	$sql='SELECT rota_date FROM '.CA_ROTA_TBL.' WHERE mtg_type="service" AND service_id="'.intval($_REQUEST['service_id']).'" AND rota_date>=CURDATE() GROUP BY rota_date ORDER BY rota_date ASC LIMIT 12';

	$results=$wpdb->get_results($sql);
	if(!empty($results))
	{
		$out='<select name="rota_date">';
		foreach($results AS $row)
		{
			$out.='<option value="'.esc_html($row->rota_date).'">'.mysql2date(get_option('date_format'),$row->rota_date).'</option>';
		}
		$out.='</select>';

	}else{$out=__('No dates yet, create some first!','church-admin');}
		echo $out;
	exit();
}



add_action('wp_ajax_church_admin_calendar_date_display','church_admin_date');
add_action('wp_ajax_nopriv_church_admin_calendar_date_display', '');

/**
 *
 * Ajax image upload
 *
 * @author  Andy Moyle
 * @param    null
 * @return   html
 * @version  0.1
 *
 */
add_action('wp_ajax_church_admin_image_upload','church_admin_image_upload');
add_action('wp_ajax_nopriv_church_admin_image_upload', 'church_admin_image_upload');
function church_admin_image_upload()
{

	// These files need to be included as dependencies when on the front end.
	require_once( ABSPATH . 'wp-admin/includes/image.php' );
	require_once( ABSPATH . 'wp-admin/includes/file.php' );
	require_once( ABSPATH . 'wp-admin/includes/media.php' );
	// Let WordPress handle the upload.
	// Remember, 'my_image_upload' is the name of our file input in our form above.
	$attachment_id = media_handle_upload( 'file-0', 0 );
	if(defined('CA_DEBUG'))church_admin_debug($attachment_id);
	if ( is_wp_error( $attachment_id ) ) {
		// There was an error uploading the image.
	} else {
		// The image was uploaded successfully!
		$image=wp_get_attachment_image_src(  $attachment_id, "thumbnail", false );
		if(defined('CA_DEBUG'))church_admin_debug(print_r($image,TRUE));
		echo json_encode(array('src'=>$image[0],'attachment_id'=>$attachment_id));
		exit();
	}

}

/**
 *
 * Popup of calendar events
 *
 * @author  Andy Moyle
 * @param    null
 * @return   html
 * @version  0.1
 *
 */
add_action('wp_ajax_church_admin_calendar_event_display','church_admin_calendar_event_display');
add_action('wp_ajax_nopriv_church_admin_calendar_event_display', 'church_admin_calendar_event_display');
function church_admin_calendar_event_display()
{
	if(defined('CA_DEBUG'))church_admin_debug('Calendar Event' .date('Y-m-d h:i:s'));
	global $wpdb;
	$date_sql=1;
	$out='';
	$dates=explode(',',$_POST['date']);
    foreach($dates AS $key=>$value){ $datesql[]='a.start_date="'.esc_sql($value).'"';}
    if(!empty($datesql)) {$date_sql=' ('.implode(' || ',$datesql).')';}else{ exit__('No event to show','church-admin');}

	$sql='SELECT a.*, b.* FROM '.CA_DATE_TBL.' a LEFT JOIN '.CA_CAT_TBL.' b ON b.cat_id = a.cat_id WHERE '.$date_sql;


	$result=$wpdb->get_results($sql);

	if(!empty($result))
	{
		foreach($result AS $row)
		{
			$out.='<div class="ca-event ">';
			$out.='<span class="ca-close">x</span>';
			$out.='<h2 style="color:'.esc_html($row->bgcolor).'">'.esc_html($row->title).'</h2>';
			$out.='<p>'.mysql2date(get_option('date_format'),$row->start_date).' '.mysql2date(get_option('time_format'),$row->start_time).' -  '.mysql2date(get_option('time_format'),$row->end_time).'</p>';
			if(!empty($row->description))$out.='<p>'.esc_html($row->description).'</p>';
			if(!empty($row->page_id))$out.='<p><a href="'.get_permalink($row->page_id).'">'.__('More information','church-admin').'</p>';
			if(!empty($row->booking_id))$out.='<p><a class="button-primary" href="'.get_permalink($row->booking_id).'">'.__('Book Now','church-admin').'</p>';
			$out.='</div>';
		}
	}
	else
	{
		$out= __('No event to show','church-admin');
	}
	echo json_encode(array('id'=>esc_html($_POST['date']),'output'=>$out));
	exit();
}












add_action( 'wp_ajax_dismissed_notice_handler', 'church_admin_ajax_notice_handler' );
add_action( 'wp_ajax_church_admin_people_activate', 'church_admin_people_activate_callback' );
//new ajax

add_action('wp_ajax_church_admin','church_admin_ajax_handler');
add_action('wp_ajax_nopriv_church_admin', 'church_admin_ajax_handler');

function church_admin_ajax_handler()
{
	global $wpdb;
		switch ($_REQUEST['method'])
		{
			case 'remove-image':
				check_ajax_referer( 'remove-image', 'nonce' );

				switch($_POST['type'])
				{
					case'people':$wpdb->query('UPDATE '.CA_PEO_TBL.' SET attachment_id=NULL WHERE people_id="'.intval($_POST['id']).'"');break;
					case'household':$wpdb->query('UPDATE '.CA_HOU_TBL.' SET attachment_id=NULL WHERE household_id="'.intval($_POST['id']).'"');break;
				}
				echo TRUE;
				exit();
			break;
			case 'show-person':
				check_ajax_referer( 'show-person', 'security' );
				require_once(plugin_dir_path( __FILE__) .'/display/address-list.php');
				$data= church_admin_people_data(intval($_POST['id']));
				if(defined('CA_DEBUG'))church_admin_debug(print_r($data,TRUE));
				echo church_admin_formatted_household($data,$_POST['map'],$_POST['updateable'],$_POST['photo']);
				exit();
			break;
			//podcast
			case "podcast-file"://checked
				require_once(plugin_dir_path( __FILE__) .'/display/sermon-podcast.php');
				if(defined('CA_DEBUG'))church_admin_debug('podcast file');
				echo church_admin_podcast_file(intval($_POST['id']),FALSE);
				exit();
			break;
			case 'series-detail'://checked
				require_once(plugin_dir_path( __FILE__) .'/display/sermon-podcast.php');

				echo church_admin_podcast_series_detail(intval($_REQUEST['id']));
				exit();
			break;
			case 'latest-series-sermon':
			require_once(plugin_dir_path( __FILE__) .'/display/sermon-podcast.php');

				echo church_admin_podcast_latest_sermon(intval($_REQUEST['id']));
				exit();
			break;
			case 'more-sermons'://checked
				require_once(plugin_dir_path( __FILE__) .'/display/sermon-podcast.php');
				echo church_admin_podcast_more_files($_REQUEST['page']);
				exit();
			break;
			case 'people_activate'://checked
				church_admin_people_activate_callback();
			break;
			case 'unattach_user'://checked
				check_ajax_referer( 'church_admin_unattach_user', 'nonce' );
				church_admin_unattach_user();
			break;
			case 'autocomplete'://checked
				check_ajax_referer( 'church-admin-autocomplete', 'security' );
				church_admin_ajax_people();
			break;
			case 'mp3_plays'://checked
				if(defined('CA_DEBUG'))church_admin_debug(plugin_dir_path( __FILE__) .'/display/sermon-podcast.php');
				require_once(plugin_dir_path( __FILE__) .'/display/sermon-podcast.php');
				church_admin_mp3_plays();
			break;
			case 'username_check'://checked
				church_admin_username_check();
			break;
			case 'filter'://checked
				require_once(plugin_dir_path( __FILE__) .'/includes/filter.php');

				church_admin_filter_callback();
			break;
			case 'filter_email'://checked
				require_once(plugin_dir_path( __FILE__) .'/includes/filter.php');
				church_admin_filter_email_callback();
			break;
			case 'people_activate'://checked
				church_admin_people_activate_callback();
			break;
			case'note_delete':
				church_admin_note_delete_callback();
			break;
			case 'calendar_date_display':
				church_admin_date();
			break;
			case'mailchimp-merge':
				require_once(plugin_dir_path( __FILE__) .'/includes/mailchimp.php');
				church_admin_mailchimp_merge();
			break;
			case'mailchimp-add':
				require_once(plugin_dir_path( __FILE__) .'/includes/mailchimp.php');
				church_admin_mailchimp_add();
			break;
			case'connect_user':
				check_ajax_referer('connect_user','nonce',TRUE);
				if(church_admin_level_check('Directory'))
				{
				if(defined('CA_DEBUG'))church_admin_debug(print_r($_POST,TRUE));
				if(!empty($_POST['user_id'])&&ctype_digit($_POST['user_id']))$ID=church_admin_user_id_exists($_POST['user_id']);
				if(!empty($_POST['people_id'])&&ctype_digit($_POST['people_id'])&& !empty($ID))
				{
					$sql='UPDATE '.CA_PEO_TBL.' SET user_id="'.intval($_POST['user_id']).'" WHERE people_id="'.intval($_POST['people_id']).'"';
					if(defined('CA_DEBUG'))church_admin_debug($sql);
					$wpdb->query($sql);
					$user=get_userdata($_POST['user_id']);
					$response= json_encode(array('login'=>$user->user_login,'people_id'=>intval($_POST['people_id'])));
					if(defined('CA_DEBUG'))church_admin_debug($response);
					echo $response;
				}
				}
				exit();
			break;
			case'create_user':
				check_ajax_referer('create_user','nonce',TRUE);
				if(church_admin_level_check('Directory'))
				{
				if(defined('CA_DEBUG'))church_admin_debug(print_r($_POST,TRUE));

				if(!empty($_POST['people_id'])&&ctype_digit($_POST['people_id']))
				{
					$person=$wpdb->get_row('SELECT * FROM '.CA_PEO_TBL.' WHERE people_id="'.intval($_POST['people_id']).'"');
					if(empty($person->email))exit('No email address');
					$username=trim(strtolower($wpdb->get_var('SELECT CONCAT(first_name,last_name) FROM '.CA_PEO_TBL.' WHERE people_id="'.intval($_POST['people_id']).'"')));
					if(empty($username))exit('No names to form username');
					$x='';
					while(username_exists( $username.$x )){$x+=1;}
					$random_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
					$user_id = wp_create_user( $username.$x, $random_password, $person->email );
					$user_id = wp_create_user( $username.$x, $random_password, $user->email );
					$message=get_option('church_admin_user_created_email');

					if(empty($message))
					{
						$message='<p>'.__('The web team at','church-admin'). '<a href="[SITE_URL]">[SITE_URL]</a> '.__('have just created a user login for you.','church-admin').'</p><p>'.__('Your username is','church-admin').' <strong>[USERNAME]</strong></p><p>'.__('Your password is','church-admin').' <strong>[PASSWORD]</strong></p><p>'.__('We also have an app you can download for [ANDROID] and [IOS]','church-admin').' </p>';
						update_option('church_admin_user_created_email',$message);
					}
					$message=str_replace('[SITE_URL]',site_url(),$message);
					$message=str_replace('[USERNAME]',esc_html($username.$x),$message);
					$message=str_replace('[PASSWORD]',$random_password,$message);
					$page_id=church_admin_register_page_id();
					if(!empty($page_id))$message=str_replace('[EDIT_PAGE]',get_permalink($page_id),$message);
					$message=str_replace('[ANDROID]','<a href="http://www.tinyurl.com/androidChurchApp">Android</a>',$message);
					$message=str_replace('[IOS]','<a href="http://www.tinyurl.com/iOSChurchApp">iOS</a>',$message);
					$app=get_option('church_admin_app_id');
					if(!empty($app))$message.='<p>We also have an app you can download for <a href="http://www.tinyurl.com/androidChurchApp">Android</a> and <a href="http://www.tinyurl.com/iOSChurchApp">iOS</a>. You can use your username and password for the directory on it!</p>';
					$headers=array();
					$headers[] = 'From: Web team at '.site_url() .'<'.get_option('admin_email').'>';
					$headers[] = 'Cc: Web team at '.site_url() .'<'.get_option('admin_email').'>';
					add_filter('wp_mail_content_type','church_admin_email_type');
					$subject=get_option('church_admin_user_created_email_subject');

					if(empty($subject))$subject='Login for '.site_url();
					if(wp_mail($person->email,$subject,$message,$headers))
					{
		    			$out='('.__('Email sent','church-admin').')';
					}

					remove_filter('wp_mail_content_type','church_admin_email_type');
					$wpdb->query('UPDATE '.CA_PEO_TBL.' SET user_id="'.esc_sql($user_id).'" WHERE people_id="'.esc_sql($people_id).'"');
					$response= json_encode(array('login'=>$username.$x.' '.$out,'people_id'=>intval($_POST['people_id'])));
					if(defined('CA_DEBUG'))church_admin_debug($response);
					echo $response;

					}
				}
				exit();
			break;
			case 'individual_attendance':
					if(defined('CA_DEBUG'))church_admin_debug('Individual attendance');
					check_ajax_referer('individual_attendance','nonce',TRUE);

					$sql='SELECT * FROM '.CA_IND_TBL.' WHERE meeting_type="'.esc_sql($_GET['meeting_type']).'" AND meeting_id="'.intval($_GET['meeting_id']).'" AND `date`="'.esc_sql($_GET['date']).'"';
					if(defined('CA_DEBUG'))church_admin_debug($sql);
					$results=$wpdb->get_results($sql);
					if(defined('CA_DEBUG'))church_admin_debug(print_r($results,TRUE));
					$out=array();
					if(!empty($results))
					{
						foreach($results AS $row)
						{
							$out[]='person-'.$row->people_id;

						}
						if(defined('CA_DEBUG'))church_admin_debug(print_r($out,TRUE));
						echo json_encode($out);
					}
					exit();
			break;
			case 'image_upload':
			check_ajax_referer('church_admin_image_upload','nonce',TRUE);
				// These files need to be included as dependencies when on the front end.
				require_once( ABSPATH . 'wp-admin/includes/image.php' );
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
				require_once( ABSPATH . 'wp-admin/includes/media.php' );
				// Let WordPress handle the upload.
				// Remember, 'my_image_upload' is the name of our file input in our form above.
				$attachment_id = media_handle_upload( 'file-0', 0 );
				if(defined('CA_DEBUG'))church_admin_debug($attachment_id);
				if ( is_wp_error( $attachment_id ) ) {
						// There was an error uploading the image.
				} else {
				// The image was uploaded successfully!
				$image=wp_get_attachment_image_src(  $attachment_id, "medium", false );
				if(defined('CA_DEBUG'))church_admin_debug(print_r($image,TRUE));
				echo json_encode(array('src'=>$image[0],'attachment_id'=>$attachment_id));
				exit();
			}
			break;
			case 'remove-app-logo':
				check_ajax_referer('remove-app-logo','nonce',TRUE);
				delete_option('church_admin_app_logo');
				echo TRUE;
				exit();
			break;
			case 'update-app-logo':
				check_ajax_referer('update-app-logo','nonce',TRUE);
				update_option('church_admin_app_logo',stripslashes($_POST['logo']));
				echo TRUE;
				exit();
			break;
		}



}

add_action('init','church_admin_receive_prayer');

function church_admin_receive_prayer()
{
	//handle front end prayer request which needs to happen later than plugins_loaded action
	global $church_admin_prayer_request_success;
	if(!empty($_POST['save_prayer_request'])&&!empty($_POST['non_spammer'])&&wp_verify_nonce($_POST['non_spammer'],'prayer-request'))
	{

		$args=array(
								'post_content'=>sanitize_textarea_field($_POST['request_content']),
								'post_title'=>wp_strip_all_tags($_POST['request_title']),
								'post_status'=>'draft',
								'post_type'=>'prayer-requests'
							);
		if(church_admin_level_check('Prayer Requests')){$args['post_status']='publish';}


		$postid = wp_insert_post($args);

		if($postid)
		{

				//the post is valid
				$church_admin_prayer_request_success='<div class="notice notice-success">';
				if($args['post_status']=='publish'){$church_admin_prayer_request_success.=__('Your prayer-request has been published','church-admin');}
				else
				{
					$church_admin_prayer_request_success.=__('Your prayer-request has been put in the moderation queue','church-admin');
					$message='<p>'.__('New prayer request draft for moderation','church-admin').'</p>';
					wp_mail(get_option('admin_email'),__('New prayer request draft for moderation','church-admin'),$message);

				}
				$church_admin_prayer_request_success.='</div>';
		}
	}
}


//submit prayer request widget
// Register and load the widget
function church_admin_load_prayer_widget() {
    register_widget( 'ca_prayer_widget' );
}
add_action( 'widgets_init', 'church_admin_load_prayer_widget' );

// Creating the widget
class ca_prayer_widget extends WP_Widget {

function __construct() {
parent::__construct(

// Base ID of your widget
'ca_prayer_widget',

// Widget name will appear in UI
__('Submit Prayer Request Widget', 'church-admin'),

// Widget description
array( 'description' => __( 'Prayer Request widget', 'church-admin' ), )
);
}

// Creating widget front-end

public function widget( $args, $instance ) {
	if(empty($ins))
$title =__('Submit prayer Request','church-admin');

// before and after widget arguments are defined by themes
echo $args['before_widget'];
if ( ! empty( $title ) )
echo $args['before_title'] . $title . $args['after_title'];

// This is where you run the code and display the output

if(!empty($_POST['non_spammer']))
{
	echo'<p>'.__('Prayer request saved for moderation','church-admin').'</p>';
}
else {
	$message=get_option('church_admin_prayer_request_message');
	if(!empty($message))echo'<p>'. esc_html($message).'</p>';
	echo'<form action="" method="POST">';
	echo'<table class="form-table"><tbody>';
	echo'<tr><th scope="row">'.__('Title','church-admin').'</th><td><input type="text" name="request_title"></td></tr>';
	echo'<tr><th scope="row">'.__('Prayer request','church-admin').'</th><td><textarea name="request_content"></textarea></td></tr>';
	echo'<tr class="widget-spam-proof">&nbsp;</td></tr>';
	echo'<tr><td cellspacing=2><input type="hidden" value="TRUE" name="save_prayer_request"/><input type="submit" value="'.__('Save','church-admin').'"/></td></tr></table>';

	echo'</form>';
	$nonce=wp_create_nonce('prayer-request');
	echo'<script>jQuery(document).ready(function($) {var content="<th scope=\"row\">'.__('Check box if not a spammer','church-admin').'</th><td><input type=\"checkbox\" name=\"non_spammer\" value=\"'.$nonce.'\"/></td></tr>";$(".widget-spam-proof").html(content);});</script>';
}
echo $args['after_widget'];
}

} // Class wpb_widget ends here


/******************************************************************************************************
*
* Use prayer request recent posts in recent posts widget when on prayer request/bible readings Archive
*
*****************************************************************************************************/

add_filter( 'widget_posts_args', 'church_admin_recent_posts_args');
add_filter('widget_comments_args', 'church_admin_recent_posts_args');
/**
 * Add CPTs to recent posts widget
 *
 * @param array $args default widget args.
 * @return array $args filtered args.
 */
function church_admin_recent_posts_args($args) {
   if(is_post_type_archive('prayer-requests')) $args['post_type'] = array('prayer-requests');
	 elseif(is_post_type_archive('bile-readings')) $args['post_type'] = array('bible-readings');
	 else {
	 $args['post_type'] = array('post');
	 }
    return $args;
}
