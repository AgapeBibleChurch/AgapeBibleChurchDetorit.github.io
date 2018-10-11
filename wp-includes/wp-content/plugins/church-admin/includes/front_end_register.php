<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function church_admin_front_end_register($create_user=FALSE,$member_type_id=1,$exclude=array())
{
/**
 *
 * Front End Registration
 *
 * @author  Andy Moyle
 * @param    $email_verify,$admin_email
 * @return
 * @version  0.3
 *
 * 0.2 fixed address save
 * 0.3 added recaptcha service
 *
 */
 	$debug=TRUE;
    global $wpdb,$people_type;
    $message='';
    $user = wp_get_current_user();
    require_once(plugin_dir_path(dirname(__FILE__)).'includes/directory.php');
    if(!empty($debug))church_admin_debug(print_r($user,TRUE));
    if(!empty($debug))church_admin_debug(print_r($_GET,TRUE));
    $out='';
    $edit='Registering';


    if(is_user_logged_in())
    {
      //Check logged in user has the rights to edit household
    	$household_id=$wpdb->get_var('SELECT household_id FROM '.CA_PEO_TBL.' WHERE user_id="'.esc_sql($user->ID).'"');

    	if(!empty($_GET['household_id']))
      {
          if(church_admin_level_check('Directory') || ($household_id==$_GET['household_id']))
          {
              $household_id=intval($_GET['household_id']);
              //check household exists
              $check=$wpdb->get_var('SELECT household_id FROM '.CA_PEO_TBL.' WHERE household_id="'.intval($household_id).'"');
              if(empty($check)){$household_id=NULL;$edit='Registering';}
              else $edit='Editing';
          }
          else {
            // user cannot edit this household
            $out.='<div class="notice notice-warning" style="color:red">'.__('You are not allowed to edit the household, but you can edit your own!','church-admin').'</div>';

          }

      }
      if(!empty($household_id))$data=$wpdb->get_row('SELECT * FROM '.CA_HOU_TBL.' WHERE household_id="'.intval($household_id).'"');
    }
    else
    {
    	$out.='<h2 class="ca-login">'.__('Login to edit your entry (Click to toggle)','church-admin').'</h2><div id="ca-login" style="display:none">';
    	$out.=wp_login_form(array('echo'=>FALSE)).'</div>';
    	$out.='<script type="text/javascript">jQuery(function(){  jQuery(".ca-login").click(function(){jQuery("#ca-login").toggle();  });});</script>';
    	$out.='<h2>'.__('Or register...','church-admin').'</h2>';
    	$household_id=NULL;
      $edit='Registering';

    }

    if(!ctype_digit($member_type_id))$member_type_id=1;
/********************************************************
*
*   Process form
*
*********************************************************/
    if(!empty($_POST['save'])  && wp_verify_nonce($_POST['church_admin_register'], 'church_admin_register')   )//add verify nonce
    {//process
    	if(!empty($debug))church_admin_debug(print_r($_POST,TRUE));
   		if(empty($_POST['ItsAllAboutJesus'])){$out.='<h2>'.__('Sorry you look like a spammer, please go back and tick the box to prove you are not!','church-admin').'</p>';}
		  else
		  {
      		require_once(plugin_dir_path(dirname(__FILE__)).'includes/directory.php');
      		$return=church_admin_save_household($create_user=FALSE,1,$exclude,$household_id);

          if($edit=='Registering')
          {
              $message.='<p>'.sprintf(__('A new household has registered. Please $%1s check them $%2s out.','church-admin'),'<a href="'.site_url().'/wp-admin/admin.php?page=church_admin/index.php&action=display_household&household_id='.intval($return['household_id']).'&tab=people">','</a>').'</p>';
          }
          else {
            $message.='<p>'.sprintf(__('A  household has been edited. Please $%1s check them $%2s out.','church-admin'),'<a href="'.site_url().'/wp-admin/admin.php?page=church_admin/index.php&action=display_household&household_id='.intval($return['household_id']).'&tab=people">','</a>').'</p>';
          }
      		if(!empty($admin_email))
        	{

            	add_filter('wp_mail_content_type','church_admin_email_type');
            	wp_mail(get_option('admin_email'),__('New household registration','church-admin'),$message);
              remove_filter('wp_mail_content_type','church_admin_email_type');
        	}
        	if($edit=='register'){$out.='<p>'.__('Thank you for registering on the site','church-admin').'</p>';}
          else {
            $out.='<p>'.__('Thank you for editing your household','church-admin').'</p>';
          }

			/**********************
			*
			*  email verify
			*
			*
			***********************/
			$check_gdpr=$wpdb->get_var('SELECT gdpr_reason FROM '.CA_PEO_TBL.' WHERE household_id="'.intval($return['household_id']).'" ORDER BY gdpr_reason LIMIT 1');
			if(empty($check_gdpr))
			{
	       		$wpdb->query('UPDATE '.CA_PEO_TBL.' SET gdpr_reason = NULL WHERE household_id="'.intval($return['household_id']).'"');
        		$row=$wpdb->get_row(' SELECT CONCAT_WS(" ", a.first_name, a.last_name) AS name, a.last_name,a.people_id, a.email ,b.* FROM '.CA_PEO_TBL.' a, '.CA_HOU_TBL.' b  WHERE a.household_id=b.household_id AND  email!=""  AND a.household_id="'.intval($return['household_id']).'" LIMIT 1');

        		$message.=sprintf(__('Thank you for registering on the church website. There are some new data protection regulations coming in to protect how your personal data is used. We store your name, address and phone details so we can keep the church organised and would like to be able to continue to communicate by email, sms and mail with you. Your contact details are available on the website (%1$s) within a password protected area. Please check with other members of your household who are over 16 and click this %2$s if you are happy. If you are not happy or would like to discuss further then do get in touch with the church office.','church-admin'),site_url(),' <a href="'.site_url().'?confirm='.esc_html($row->last_name).'/'.intval($row->people_id).'">link</a>');
			         add_filter('wp_mail_content_type','church_admin_email_type');
						add_filter( 'wp_mail_from_name', 'church_admin_from_name');
						add_filter( 'wp_mail_from', 'church_admin_from_email');


						if(wp_mail($row->email,__('Please confirm you are happy to receive communications','church-admin'),$message)){$out.='<p>'.esc_html($row->email).' sent immediately</p>';}
						else {church_admin_debug('GDPR confirmation email:'. $GLOBALS['phpmailer']->ErrorInfo);}

					remove_filter('wp_mail_content_type','church_admin_email_type');
          $out.='<p>'.__('Please click on the link in the email we have sent you to confirm your email address','church-admin').'</p>';
			//end email verify
        	}



        	if(!empty($return['output']))$out.=$return['output'];
        }
    }//end process
    else
    {//form
    	//keep church_admin class for form cloning of media upload to work!
    	$out.='<div class="church_admin">';
     	$out.='<form action="" method="post"><input type="hidden" name="save" value="yes"/>';
        $out.=wp_nonce_field('church_admin_register','church_admin_register',TRUE,FALSE);


    	if(!empty($household_id)){
    		//get_people if $houshold_id
    		$people=$wpdb->get_results('SELECT * FROM '.CA_PEO_TBL.' WHERE household_id="'.esc_sql($household_id).'"');
    	}
    	$x=1;
    	if(!empty($household_id)&&!empty($people))
    	{//editing an entry
    		$x=1;
    		foreach($people AS $person)
    		{

    			$out.=church_admin_edit_people_form($x,$person,$exclude);
    			$x++;
    		}

    	}//editing an entry
    	else
    	{
    		$out.='<input type="hidden" name="new_entry" value="yes"/>';
    		$out.=church_admin_edit_people_form(1,NULL,$exclude);
    	}



       	 $out.='<p id="jquerybuttons"><input type="button" id="btnAdd" value="'.__('Add another person','church-admin').'" /><input type="button" id="btnDel" value="'.__('Remove person','church-admin').'" /></p>';;
        /*
        	if(!empty($household_id)){$data=$wpdb->get_row('SELECT * FROM '.CA_HOU_TBL.' WHERE household_id="'.esc_sql($household_id).'"');}else{$data=NULL;}
        	$out.='<p><label>'.__('Phone','church-admin').'</label><input name="phone" type="text" ';
        	if(!empty($person->mobile))$out.='value="'.esc_html($data->phone).'"';
        	$out.='/></p>';
        */


        if(empty($data))$data=(object)'';
        $out.= church_admin_address_form($data,NULL);

        $out.='<p><span class="ItsAllAboutJesus"></span></p>';

		$out.='<div class="clear"></div>';
        $out.= '<p>';
        if(empty($household_id)){$out.='<input type="submit" value="'.__('Register','church-admin').'"/>';}
        else{$out.='<input type="submit" value="'.__('Save','church-admin').'"/>';}
        $out.='</form></p></div>';
        $out.= '<script>jQuery(document).ready(function($) {
    var content=\'<p>'.__('Check box if you are not a spammer','church-admin').'<input type="checkbox" name="ItsAllAboutJesus" value="yes"/></p>\'
    $(".ItsAllAboutJesus").html(content);
});</script>';

    }//form

    return $out;
}

?>
