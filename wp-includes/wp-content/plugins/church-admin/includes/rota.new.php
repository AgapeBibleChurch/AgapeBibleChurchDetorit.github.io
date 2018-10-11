<?php

if ( ! defined( 'ABSPATH' ) ) exit('You need Jesus!'); // Exit if accessed directly
/********************************************
*
*
*	Reconfigured for CA_ROTA_TBL:
* 	January 2017
*
*********************************************/


/**
 *
 * displays rota for $service_id
 *
 * @author  Andy Moyle
 * @param    $service_id
 * @return   html string
 * @version  0.1
 *
 *
 */

function church_admin_rota_list($service_id=NULL)
{
	if(!church_admin_level_check('Rota'))wp_die(__('You don\'t have permission to do that','church-admin'));
	//initialise
	global $wpdb,$wp_locale;
	if(empty($service_id))
	{
		//look for first service
		$service_id=$wpdb->get_var('SELECT service_id FROM '.CA_SER_TBL.' ORDER BY service_id ASC LIMIT 1');

		if(empty($service_id))
		{
					echo'<div class="notice notice-warning inline"><a href="'.wp_nonce_url("admin.php?page=church_admin/index.php&tab=rota&amp;action=edit_service",'edit_service').'">'.__('Please set up a service','church-admin').'</a></div>';
		}

	}



	//check for more than one service and show form if there is
	 $services=$wpdb->get_results('SELECT a.*,b.venue FROM '.CA_SER_TBL.' a, '.CA_SIT_TBL.' b WHERE a.site_id=b.site_id');

	$noOfServices=$wpdb->num_rows;

	//always show choose service form if more than one
	if($noOfServices>1)
	{
		echo'<form action="admin.php?page=church_admin/index.php&tab=rota&amp;action=church_admin_rota_list" method="POST">';
		echo'<table class="form-table"><tbody><tr><th scope=row>'.__('Change Service?','church-admin').'</th><td><select name="service_id">';
		echo'<option>'.__('Choose Service','church-admin').'</option>';
		foreach($services AS $service)
		{

			if($service->service_day!=8)
			{
				echo'<option value="'.intval($service->service_id).'">'.sprintf( esc_html__( '%1$s at %2$s on %3$s %4$s', 'church-admin' ), $service->service_name, $service->venue,$wp_locale->get_weekday($service->service_day),$service->service_time).'</option>';
			}
			else
			{
				echo'<option value="'.intval($service->service_id).'">'.sprintf( esc_html__( '%1$s at %2$s', 'church-admin' ), $service->service_name, $service->venue ).'</option>';
			}

		}
		echo'</select> <input type="submit" class="button-primary" name="choose_service" value="'.__('Choose service','church-admin').' &raquo;" /></td></tr></tbody></table></form>';
	}



	//get details of service for title

	$service=$wpdb->get_row('SELECT a.*,b.venue FROM '.CA_SER_TBL.' a, '.CA_SIT_TBL.' b WHERE a.site_id=b.site_id AND a.service_id="'.intval($service_id).'"');

	if(!empty($service)&&$service->service_day>=0&&$service->service_day<=7){echo '<h2>'.sprintf( esc_html__( 'Schedule for %1$s at %2$s on %3$s  %4$s', 'church-admin' ), $service->service_name, $service->venue,$wp_locale->get_weekday($service->service_day),$service->service_time ).'</h2>';}
			else{echo '<h2>'.sprintf( esc_html__( 'Schedule for %1$s at %2$s', 'church-admin' ), $service->service_name, $service->venue).'</h2>';}

	//check rota jobs are set up
	$allRotaJobs=$wpdb->get_results('SELECT * FROM '.CA_RST_TBL.'  ORDER by rota_order');
	if(empty($allRotaJobs))
	{//no rota jobs
		echo'<div class="notice notice-warning inline"><a href="'.wp_nonce_url("admin.php?page=church_admin/index.php&tab=rota&amp;action=church_admin_rota_settings_list",'rota_settings_list').'">'.__('Please set up some schedule jobs first','church-admin').'</a></div>';
	}
	else
	{//rota jobs exist, so safe to proceed


		$rotaJobs=church_admin_required_rota_jobs($service_id);

		//we now have an array $rotaJobs that contains id as key and name of job as value

		//get rota dates
		if(!empty($_POST['rota_date'])){$rota_date=esc_sql(stripslashes($_POST['rota_date']));}else{$rota_date=NULL;}
		if(!empty($rota_date)&&church_admin_checkdate($rota_date))
		{
			$sql='SELECT rota_date FROM '.CA_ROTA_TBL.' WHERE service_id="'.intval($service_id).'" AND mtg_type="service" AND rota_date>="'.$rota_date.'" GROUP BY rota_date';
		}
		else
		{
			$sql='SELECT rota_date FROM '.CA_ROTA_TBL.' WHERE service_id="'.intval($service_id).'" AND mtg_type="service" AND rota_date>=CURDATE() GROUP BY rota_date';
		}
		$rotaDatesResults=$wpdb->get_results($sql);

		//If none add 12 weeks worth
		if(empty($rotaDatesResults))
		{
			echo '<p><a href="'.wp_nonce_url('admin.php?page=church_admin/index.php&tab=rota&action=edit_rota&amp;service_id='.intval($service_id).'&amp;mtg_type=service','edit_rota').'">'.__('Add new schedule date','church-admin').'</a>';

		}
		else
		{


			//feed in message if rota date has been copied and then redirected back here
			if(!empty($_GET['message'])&&$_GET['message']=='copied')echo'<div class="notice notice-success inline">'.__('Schedule date copied','church-admin').'</div>';

			//Build Table Header
			echo '<p><a href="'.wp_nonce_url('admin.php?page=church_admin/index.php&tab=rota&action=edit_rota&amp;service_id='.intval($service_id).'&amp;mtg_type=service','edit_rota').'">'.__('Add new schedule date','church-admin').'</a>';


			//months dropdown
			echo'<form action="" method="POST"><table><tr><th scope="row">'.__('Choose period starting ','church-admin').'</th><td><select name="rota_date">';
			$earliest=$wpdb->get_var('SELECT rota_date FROM '.CA_ROTA_TBL.' WHERE rota_date!="0000-00-00" ORDER BY rota_date LIMIT 1');
			$latest=$wpdb->get_var('SELECT rota_date FROM '.CA_ROTA_TBL.' WHERE rota_date!="0000-00-00" ORDER BY rota_date DESC LIMIT 1');
			echo $earliest.' '.$latest;
			if(!empty($earliest))
			{
				if(!empty($rota_date)){$current=new DateTime($_POST['rota_date']);}
				else
				{
					$current= new DateTime('today');
					$current->modify('first day of this month');
				}
				$start    = new DateTime($latest);
				$start->modify('first day of next month');
				$end      = new DateTime($earliest);
				$end->modify('first day of this month');
				$interval = DateInterval::createFromDateString('-1 month');
				$period   = new DatePeriod($start, $interval, $end);

				echo'<option value="'.$current->format("Y-m-d").'">'.$current->format(get_option('date_format')).'</option>';
				foreach ($period as $dt) {
    				echo'<option value="'.$dt->format("Y-m-d").'">'.$dt->format(get_option('date_format')).'</option>';
				}
				echo'</select><input type="submit" class="button-primary" value="'.__('Go to','church-admin').'"/></table></form>';

			}

			$thead='<tr><th>'.__('Edit','church-admin').'</th><th>'.__('Delete','church-admin').'</th><th width="100">'.__('Date','church-admin').'</th>';
			foreach($rotaJobs AS $id=>$value)$thead.='<th>'.esc_html($value).'</th>';
			$thead.='<th>'.__("Copy",'church-admin').'</th>';
			echo '<table class="widefat striped"><thead>'.$thead.'</thead><tfoot>'.$thead.'</tfoot><tbody>';

			//build row for each date
			$date_options=$rotaDatesResults;
			foreach($rotaDatesResults AS $row)
			{
						$edit_url=wp_nonce_url('admin.php?page=church_admin/index.php&tab=rota&action=edit_rota&rota_date='.esc_html($row->rota_date).'&amp;service_id='.intval($service_id).'&amp;mtg_type=service','edit_rota');
			$delete_url=wp_nonce_url('admin.php?page=church_admin/index.php&tab=rota&action=delete_rota&rota_date='.esc_html($row->rota_date).'&amp;service_id='.intval($service_id).'&amp;mtg_type=service','delete_rota');


				echo'<tr>
					<td><a href="'.$edit_url.'">'.__('Edit','church-admin').'</a></td>
					<td><a href="'.$delete_url.'" onclick="return confirm(\'Are you sure?\')">'.__('Delete','church-admin').'</a></td>
					<td>'.mysql2date(get_option('date_format'),$row->rota_date).'</td>';
				foreach($rotaJobs AS $rota_task_id=>$jobName)
				{
					//note that rota_id for ALL rota jobsrefers to the rota task id!
					$people=church_admin_rota_people_array($row->rota_date,$rota_task_id,$service_id,'service');
					echo'<td class="ca-names">'.esc_html(implode(", ",$people)).'</td>';
				}
				//copy section
				echo'<td><form action="'.admin_url().'admin.php" method="GET">';
				echo'<input type="hidden" name="page" value="church_admin/index.php"/><input type="hidden" name="tab" value="rota"/><input type="hidden" name="action" value="copy_rota_data"/>';
				echo wp_nonce_field('copy_rota','copy_rota');
				echo'<input type="hidden" name="service_id" value="'.intval($service_id).'"/><input type="hidden" name="mtg_type" value="service"/>';
				echo'<input type="hidden" name="rotaDate1" value="'.esc_html($row->rota_date).'"/>';
				echo __('Copy to...','church-admin').church_admin_date_picker($row->rota_date,'rotaDate2',FALSE,NULL,NULL,'new_date'.$row->rota_date,'new_date'.$row->rota_date);
				echo'<input type="submit" value="'.__('Copy schedule','church-admin').'"/></form></td>';
				echo'</tr>';
			}
			echo'</tbody></table>';
		}




	}//end rota jobs exist
}//end function





  /**
 *
 * Delete rota entry
 *
 * @author  Andy Moyle
 * @param    $date,$mtg_type,$service_id
 * @return   BOOL
 * @version  0.1
 *
 */
 function church_admin_delete_rota($rota_date,$mtg_type,$service_id)
 {
 	if(!church_admin_level_check('Rota'))wp_die(__('You don\'t have permission to do that','church-admin'));
 	global $wpdb;
 	$wpdb->query('DELETE FROM '.CA_ROTA_TBL.' WHERE rota_date="'.esc_sql($rota_date).'" AND mtg_type="'.esc_sql($mtg_type).'" AND service_id="'.intval($service_id).'"');
 	echo '<div class="notice notice-success inline">'.__('Schedule Date Deleted','church-admin').'</div>';
	church_admin_rota_main($service_id);
 }

/**
 *
 * copies data from rota_date to another rota_date
 *	Call early and then redirect to protect url, in case it is done again.
 *
 * @author  Andy Moyle
 * @param    $rotaDate1,$rotaDate2, $service_id,$mtg_type
 * @return   NULL
 * @version  0.1
 *
 */
function church_admin_copy_rota($rotaDate1,$rotaDate2, $service_id,$mtg_type)
{
	if(!church_admin_level_check('Rota'))wp_die(__('You don\'t have permission to do that','church-admin'));
	//$rotaDate1 is destination
	//$rotaDate2 is copy
	global $wpdb;
	$wpdb->query('DELETE FROM '.CA_ROTA_TBL.' WHERE service_id="'.intval($service_id).'" AND mtg_type="service" AND rota_date="'.$rotaDate2.'"');
	$results=$wpdb->get_results('SELECT * FROM '.CA_ROTA_TBL.' WHERE rota_date="'.esc_sql($rotaDate1).'"  AND mtg_type="'.esc_sql($mtg_type).'" AND service_id="'.intval($service_id).'"');
	if(!empty($results))
	{
		foreach($results AS $row)
		{
			church_admin_update_rota_entry($row->rota_task_id,$rotaDate2,$row->people_id,$mtg_type,$service_id);
		}
	}
}
/**
 *
 * Edit Rota Date
 *
 * @author  Andy Moyle
 * @param    $rota_date,$mtg_type,$service_id
 * @return
 * @version  0.1
 *
 */
function church_admin_edit_rota($rota_date=NULL,$mtg_type='service',$service_id=1)
{
	if(!church_admin_level_check('Rota'))wp_die(__('You don\'t have permission to do that','church-admin'));
	global $wpdb;

	if(empty($rota_date)&&!empty($_POST['rota_date'])){$rota_date=$_POST['rota_date'];}
	$requiredRotaJobs=church_admin_required_rota_jobs($service_id);
	//grab service details
	$service=$wpdb->get_var('SELECT service_name FROM '.CA_SER_TBL.' WHERE service_id="'.intval($service_id).'"');
	//grab rota jobs for thsi service id
	$rota_tasks=$wpdb->get_results('SELECT * FROM '.CA_RST_TBL.'  ORDER BY rota_order');
	$requiredRotaJobs=$requiredMinistries=array();
	foreach($rota_tasks AS $rota_task)
	{
		$allServiceID=maybe_unserialize($rota_task->service_id);
		if(is_array($allServiceID)&&in_array($service_id,$allServiceID))$requiredRotaJobs[$rota_task->rota_id]=$rota_task->rota_task;
		$requiredMinistries[$rota_task->rota_id]=maybe_unserialize($rota_task->ministries);
	}



	if(!empty($_POST['save_rota'])&&!empty($rota_date)&&check_admin_referer('edit_rota','nonce'))
	{
		//clear out current entries for that date,service_id and mtg_type;
		$wpdb->query('DELETE FROM '.CA_ROTA_TBL.' WHERE service_id="'.intval($service_id).'" AND mtg_type="service" AND rota_date="'.$rota_date.'"');

		foreach($requiredRotaJobs AS $job_id=>$job_name)
		{
			//deal with checkbox generated entries
			if(!empty($_POST['j'.$job_id]))
			{

				foreach($_POST['j'.$job_id] AS $key=>$people_id)
				{


					church_admin_update_rota_entry($job_id,$rota_date,$people_id,'service',$service_id);
				}
			}
			//deal with autocomplete
			if(!empty($_POST[$job_id]))
			{
				$people=unserialize(church_admin_get_people_id(stripslashes($_POST[$job_id])));
				foreach($people AS $key=>$people_id)
				{
					church_admin_update_rota_entry($job_id,$rota_date,$people_id,'service',$service_id);
				}
			}
		}

		echo '<div class="notice notice-success inline">'.__('Schedule Updated','church-admin').'</div>';
		church_admin_rota_main($service_id);

	}
	else
	{//form
		echo'<h2>'.__('Edit Schedule for','church-admin').' ';
		if(!empty($rota_date))echo mysql2date(get_option('date_format'),$rota_date).' ';
		echo esc_html($service).'</h2>';

		echo'<form action="" method="POST">';
		echo'<table class="form-table">';
		if(empty($rota_date))
		{
			echo '<tr><th scope="row">'.__('Date','church-admin').'</th><td>'.church_admin_date_picker(NULL,'rota_date',FALSE,NULL,NULL,'rota_date','rota_date').'</td></tr>';

		}
		foreach($requiredRotaJobs AS $job_id=>$job_name)
		{
			echo'<tr><th scope="row">'.esc_html($job_name).'</th><td>';

			//checkbox first
			$currentPeople=church_admin_rota_people_array($rota_date,$job_id,$service_id,'service');

			$allMinistryPeople=array();
			if(!empty($requiredMinistries[$job_id]))
			{

				foreach($requiredMinistries[$job_id]AS $key=>$ministry_id)
				{

					$allMinistryPeople=$allMinistryPeople+church_admin_ministry_people_array($ministry_id);
				}

				asort($allMinistryPeople);
				foreach($allMinistryPeople AS $people_id=>$name)
				{
					echo'<input  type="checkbox" name="j'.intval($job_id).'[]" value="'.intval($people_id).'"';
					if(!empty($currentPeople[$people_id])) {echo ' checked="checked "';unset($currentPeople[$people_id]);}
					echo'/> <span class="ca-names">'.esc_html($name).'</span><br/>';
				}
			}
			//autocomplete text field populated with rest of names!
			if(!empty($currentPeople)){$current=implode(", ",$currentPeople);}else{$current='';}

			echo church_admin_autocomplete(intval($job_id),'friends'.intval($job_id),'to'.intval($job_id),$current,FALSE);
			echo'</tr>';
		}
		echo'<tr><td cellspacing=2><input type="hidden" name="save_rota" value="yes"/>'.wp_nonce_field('edit_rota','nonce').'<input type="submit" class="button-primary" value="'.__('Save','church-admin').'"/></td></tr>';
		echo'</table></form>';

	}//form


}


/**
 *
 * Rota CSV
 *
 * @author  Andy Moyle
 * @param    $lengths, $fontSize
 * @return   array(orientation,font_size,widths)
 * @version  0.1
 *
 */


 /**
 *
 * Emails out the rota
 *
 * @author  Andy Moyle
 * @param    $service_id,$date
 * @return   html string
 * @version  0.2
 *
 * Fix for translated installs, don't translate date
 */
function church_admin_email_rota($service_id=1,$date=NULL)
{
	if(!church_admin_level_check('Rota'))wp_die(__('You don\'t have permission to do that','church-admin'));
 	$debug=TRUE;

	global $church_admin_version,$wpdb,$wp_locale;
	//don't translate days as strtotime doesn't work
	$wpdb->show_errors;
	//grab service details
	 $service=$wpdb->get_row('SELECT a.*,b.venue FROM '.CA_SER_TBL.' a, '.CA_SIT_TBL.' b WHERE a.site_id=b.site_id AND a.service_id="'.esc_sql($service_id).'"');
    if(empty($date)){$rota_date=$wpdb->get_var('SELECT rota_date FROM '.CA_ROTA_TBL.' WHERE mtg_type="service" AND service_id="'.intval($service_id).'" AND rota_date>=CURDATE() ORDER BY rota_date ASC LIMIT 1');}else{$rota_date=$date;}


	if(!empty($_POST['rota_email'])&& check_admin_referer('send_rota','nonce'))
	{//process form and send email


		$rotaJobs=church_admin_required_rota_jobs($service_id);

		//$rotaJobs is an array rota_task_id=>rota_task


		//build email

			//build rota with jobs
			$user_message=stripslashes(nl2br($_POST['message']));
			//fix floated images for email
			$user_message=str_replace('class="alignleft ','style="float:left;margin-right:20px;" class="',$user_message);
			$user_message=str_replace('class="alignright ','style="float:right;margin-left:20px;" class="',$user_message);
			//$textversion=strip_tags($user_message).'\r\n for '.$service->service_name.' on '.$days[$service->service_day].' at '.$service->service_time.' '.$service->venue.'\r\n';
			if($service->service_day!=8){$message=$user_message.'<h4>'.esc_html(sprintf(__('Schedule for %1$s at %2$s on %3$s at %4$s', 'church-admin' ), $service->service_name, $service->venue,$wp_locale->get_weekday($service->service_day).' '.mysql2date(get_option('date_format'),$rota_date),$service->service_time )).'</h4>';}
			else{$message=$user_message.'<h4>'. esc_html(sprintf(__( 'Schedule for %1$s at %2$%', 'church-admin' ), $service->service_name, $service->venue)).'</h4>';}

			//$message=$user_message.'<h4>'.__('Rota','church-admin').' for <br> '.$service->service_name.' at '.$service->venue.' on '.$days[$service->service_day].' '.mysql2date(get_option('date_format'),$rota_date).' at '.$service->service_time.' </h4>';
			$message.='<table><thead><tr><th>'.__('Job','church-admin').'</th><th>'.__('Who','church-admin').'</th></tr></thead><tbody>';
			$recipients=array();
			foreach($rotaJobs AS $rota_task_id=>$jobName)
				{
					$people='';

					$people=church_admin_rota_people_array($rota_date,$rota_task_id,$service_id,'service');

					if(!empty($people))
					{
						foreach($people AS $people_id=>$name)
						{
							$email=$wpdb->get_var('SELECT email FROM '.CA_PEO_TBL.' WHERE people_id="'.intval($people_id).'" AND email!="" AND email_send=1 && gdpr_reason!=""');
							if(!empty($email)&&!in_array($email,$recipients))$recipients[$name]=$email;
						}
						$message.='<tr><td>'.esc_html($jobName).'</td><td>'.esc_html(implode(", ",$people)).'</td></tr>';
					}
				}
				$message.='</table>';


			//start emailing the message
			$message.='';
			if(!empty($recipients))
			{

				add_filter( 'wp_mail_from_name','church_admin_from_name' );
				add_filter( 'wp_mail_from', 'church_admin_from_email');
				add_filter('wp_mail_content_type','church_admin_email_type');
				foreach($recipients AS $name=>$email)
				{
					 	$email_content='<p>'.__('Dear','church-admin').' '.$name.',</p>'.$message;
						$whenToSend=get_option('church_admin_cron');
						if($whenToSend=='immediate'||empty($whenToSend))
						{

							add_filter( 'wp_mail_content_type', 'set_html_content_type' );


							if(wp_mail($email,__("This week's service schedule for ",'church-admin').mysql2date(get_option('date_format'),$rota_date),$email_content))
							{
								echo'<p>Email to '.esc_html($email).' sent immediately</p>';

							}
							else
							{//log errors
								global $phpmailer;
								if (isset($phpmailer)) {
									church_admin_debug("**********\r\n rota.new.php line303\r\n ".print_r($phpmailer->ErrorInfo,TRUE)."\r\n");
								}
							}
							remove_filter( 'wp_mail_content_type', 'set_html_content_type' );
						}
						else
						{
							if(QueueEmail($email,__("This week's service schedule",'church-admin'),$email_content,'',get_option('blogname'),get_option('admin_email'),'',''))
							{
								echo'<p>Email to '.esc_html($name).' queued</p>';
															}
						}
					}
				}
	}//end send out email
	else
	{
		//The following line is put in by Jostein 1.03.2017
		echo'<h2>'.esc_html (sprintf( __( 'Email service schedule for %1$s at %2$s on %3$s at %4$s', 'church-admin' ),
						$service->service_name, $service->venue,$wp_locale->get_weekday($service->service_day).' '.mysql2date(get_option('date_format'),$rota_date),$service->service_time )).'</h2><form action="" method="post">';
		echo'<table class="form-table"><tr><th scope="row">'.__('From name','church-admin').'</th><td><input type="text" id="from_name" name="from_name"  ';
		$from_name=get_option('church_admin_from_name');
		if(!empty($from_name)) echo ' value="'.esc_html($from_name).'"';
		echo'/></td></tr>';
		echo'<tr><th scope="row">'.__('From email','church-admin').'</th><td><input type="text" id="from_email" name="from_email"  ';
		$from_email=get_option('church_admin_from_email');
		if(!empty($from_email)) echo ' value="'.esc_html($from_email).'"';
		echo'/></td></tr></table>';
		echo'<p>'.__('The email will contain a salutation and the service schedule. Please add your own message','church-admin').'</p>';
		wp_editor('','message',"", true);
		echo'<p><input type="hidden" name="rota_email" value="yes"/>'.wp_nonce_field('send_rota','nonce').'<input type="submit" class="button-primary" value="'.__('Send to rota participants','church-admin').'"/></p>';
		echo'</form>';
	}

}

 /**
 *
 * SMS next rota out for $service_id
 *
 * @author  Andy Moyle
 * @param    $service_id
 * @return   html
 * @version  0.1
 *
 *
 */

function church_admin_sms_rota($service_id=NULL)
{
  if(!church_admin_level_check('Rota'))wp_die(__('You don\'t have permission to do that','church-admin'));
   	$debug=TRUE;

    global $wpdb,$wp_locale;
    $rota_date=$wpdb->get_var('SELECT rota_date FROM '.CA_ROTA_TBL.' WHERE mtg_type="service" AND service_id="'.intval($service_id).'" AND rota_date>CURDATE() LIMIT 1');
    if(!empty($rota_date))
    {
    	 $service=$wpdb->get_row('SELECT a.*,b.venue FROM '.CA_SER_TBL.' a, '.CA_SIT_TBL.' b WHERE a.site_id=b.site_id AND a.service_id="'.esc_sql($service_id).'"');

    	if($service->service_day!=8){echo '<h2>'.sprintf( esc_html__( 'Schedule for %1$s at %2$s on %3$s at %4$s', 'church-admin' ), $service->service_name, $service->venue,$wp_locale->get_weekday($service->service_day).' '.mysql2date(get_option('date_format'),$rota_date),$service->service_time ).'</h2>';}
			else{echo '<h2>'.sprintf( esc_html__( 'Schedule for %1$s at %2$%', 'church-admin' ), $service->service_name, $service->venue).'</h2>';}

		$username=get_option('church_admin_sms_username');
		$password=get_option('church_admin_sms_password');
		$sender=get_option('church_admin_sms_reply');
		$sms_type=get_option('church_admin_bulksms');
		if(empty($username)||empty($password)||empty($sender)||empty($sms_type))
		{
			$out='<h2>Please setup your Bulksms account settings first</h2>';
			echo $out;
			if(!empty($debug))church_admin_debug("**********\r\n rota.new.php line632\r\n FORM ".$out."\r\n");
		}
		else
		{
			//initialise sms sending
			require_once(plugin_dir_path(__FILE__).'/sms.php');

    		if(!empty($debug))church_admin_debug('SMS Schedule Send: '.date('Y-m-d h:i:s'));
			//get jobs
			$jobs=church_admin_required_rota_jobs($service_id);
			//get people and mobile for each job
			$recipients=array();
			foreach($jobs AS $job_id=>$jobName)
			{
				//array of people
				$people=church_admin_rota_people_array($rota_date,$rota_task_id,$service_id,'service');
				foreach($people AS $people_id=>$name)
				{
					$mobile=$wpdb->get_row('SELECT mobile FROM '.CA_PEO_TBL.' WHERE people_id="'.intval($people_id).'" AND mobile!=""');
					$result=church_admin_sms($mobile,$message.__('you are on','church-admin').' "'.$rotaJobs[$job].'"');
					$result=array('success'=>TRUE);//debug
					if( $result['success'] )
					{
						echo'<p>'.__('SMS sent to','church-admin').' '.esc_html($person->name).'</p>';
					}
				}
			}

		}
	}//rota_date found
}


 /**
 *
 * Required rota jobs for service_id
 *
 * @author  Andy Moyle
 * @param    $service_id
 * @return   array rota_task_id=>$rota_task
 * @version  0.1
 *
 *
 */
function church_admin_required_rota_jobs($service_id)
{
	global $wpdb;
	$rota_tasks=$wpdb->get_results('SELECT * FROM '.CA_RST_TBL.'  ORDER BY rota_order');
	$requiredRotaJobs=array();
	foreach($rota_tasks AS $rota_task)
	{
		$allServiceID=maybe_unserialize($rota_task->service_id);
		if(is_array($allServiceID)&&in_array($service_id,$allServiceID))$requiredRotaJobs[$rota_task->rota_id]=$rota_task->rota_task;
	}
	return $requiredRotaJobs;

}

/**
*
* Check rota wp-cron jobs
*
* @author  Andy Moyle
* @param
* @return
* @version  0.1
*
*
*/
function church_admin_cron_check()
{
 global $wpdb,$wp_locale;

 $cron=get_option('cron');

$tableData='';
 if(!empty($cron))
 {
	 foreach($cron AS $ts=>$details)
	 {
		 if(!empty($details['church_admin_cron_email_rota']))
		 {
			 	$churchAdminCronJobs=$details['church_admin_cron_email_rota'];



				foreach($churchAdminCronJobs AS $key=>$churchAdminCronJob)
				{
					$service_id=$churchAdminCronJob['args']['service_id'];
				 	$serviceRow=$wpdb->get_row('SELECT a.*,b.venue FROM '.CA_SER_TBL.' a, '.CA_SIT_TBL.' b WHERE a.site_id=b.site_id AND a.service_id="'.esc_sql($service_id).'"');
					$service=sprintf( esc_html__( '%1$s at %2$s on %3$s %4$s', 'church-admin' ), $serviceRow->service_name, $serviceRow->venue,$wp_locale->get_weekday($serviceRow->service_day),$serviceRow->service_time);
					$delete='<a href="'.wp_nonce_url('admin.php?page=church_admin/index.php&amp;action=delete-cron&amp;tab=rota&ts='.esc_html($ts).'&key='.esc_html($key),'delete-cron').'">'.__('Delete','church-admin').'</a>';

					$tableData.='<tr><td>'.$delete.'</td><td>'.mysql2date(get_option('date_format').' '.get_option('time_format'),date('Y-m-d h:i:s',$ts)).'</td><td>'.esc_html($service).'</td></tr>';
				}

			}
		}
		if(!empty($tableData))
			{
				echo'<h3>'.__('Current auto schedule email wp-cron jobs','church-admin').'</h3>';
				echo '<table class="widefat"><thead><tr><th>'.__('Delete','church-admin').'</th><th>'.__('Next send','church-admin').'</th><th>'.__('Which Service','church-admin').'</th></tr></thead><tfoot><tr><th>'.__('Delete','church-admin').'</th><th>'.__('Next send','church-admin').'</th><th>'.__('Which Service','church-admin').'</th></tr></tfoot><tbody>';
				echo $tableData;
				echo'</tbody></table>';
			}
 }
}

function church_admin_delete_cron($ts,$key)
{
	if(!church_admin_level_check('Rota'))wp_die(__('You don\'t have permission to do that','church-admin'));
	$cron=get_option('cron');
	unset($cron[$ts]['church_admin_cron_email_rota'][$key]);
	update_option('cron',$cron);
}
