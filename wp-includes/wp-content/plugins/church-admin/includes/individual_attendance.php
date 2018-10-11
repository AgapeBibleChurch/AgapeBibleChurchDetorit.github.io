<?php

/**
 *
 * Individual attendance tracking form
 *
 * @author  	Andy Moyle
 * @param    	null
 * @return   	html
 * @version  	1.2450
 * @date 		2017-01-03
 */
function church_admin_individual_attendance()
{
		global $wpdb,$wp_locale;
		$wpdb->show_errors;
		$out='<h1>'.__('Individual Attendance','church-admin').'</h1>';
		$out.='<h3 class="toggle" id="attendance-download">'.__('CSV download (Click to toggle)','church-admin').'</h3>';
		$out.='<div class="attendance-download" ';
		if(empty($_POST['ind_att_csv']))$out.='style="display:none" ';
		$out.='>';
		$out.=church_admin_individual_attendance_csv();
		$out.='</div><script type="text/javascript">jQuery(function($){  $(".toggle").click(function(){var id=$(this).attr("id");jQuery("."+id).toggle();  });});</script>';
		$out.='<h3 class="toggle" id="attendance">'.__('Add Individual Attendance ','church-admin').'</h3>';

		/***************************************************************
		*
		*	Option to choose which type of attendance
		*
		***************************************************************/
		if(empty($_GET['meeting']))
		{

			$services=$wpdb->get_results('SELECT * FROM '.CA_SER_TBL);
			if(!empty($services))
			{
				$option='';
				foreach($services AS $service)
				{
					$option.='<option value="service-'.$service->service_id.'">'.$service->service_name.' on '.$wp_locale->get_weekday($service->service_day).' at '.$service->service_time.'</option>';
				}
			}
			$smallgroups=$wpdb->get_results('SELECT * FROM '.CA_SMG_TBL);
			if(!empty($smallgroups))
			{
				foreach($smallgroups AS $smallgroup)$option.='<option class="smallgroup" value="smallgroup-'.intval($smallgroup->id).'">Small Group - '.esc_html($smallgroup->group_name).'</option>';
			}

			$out.='<form action="'.admin_url().'" method="GET"><table class="form-table">';
			$out.='<input type="hidden" name="page" value="church_admin/index.php"/><input type="hidden" name="action" value="individual_attendance"/><input type="hidden" name="tab" value="services"/>';
			$out.='<tr><th scope="row">'.__('Which Meeting','church-admin').'</th><td><select name="meeting">'.$option.'</select></td></tr>';
			$member_type=church_admin_member_type_array();
			$first=$option='';
			$out.='<tr><th scope="row">'.__('Member type','church-admin').'</th><td>';
			foreach($member_type AS $id=>$type)
			{

				$out.='<input type="checkbox" name="member_type_id[]" value="'.$id.'">'.$type.'<br/>';

			}
			$out.='</td></tr>';
			$out.='<tr><td>&nbsp;</td><td><input type="submit"  class="button-primary" value="'.__('Choose','church-admin').'"/></td></tr>';
			$out.='</table>';
		}
		else
		{

			$meeting=explode("-",$_GET['meeting']);

			if(!empty($_POST['save_ind_att']))
			{

				/***************************************************************
				*
				*	Process
				*
				***************************************************************/
				$adult=$child=0;
				$date=esc_sql(stripslashes($_POST['date']));
				//populate individual attendance table

				//first delete old save if present
				$wpdb->query('DELETE FROM '.CA_IND_TBL.' WHERE `date`="'.$date.'" AND meeting_type="'.esc_sql($meeting[0]).'" AND meeting_id="'.esc_sql($meeting[1]).'"');

					$values=array();

					foreach($_POST['people_id'] AS $key=>$people_id)
					{

						$values[]='("'.$date.'","'.intval($people_id).'","'.esc_sql($meeting[0]).'","'.esc_sql($meeting[1]).'")';
						//find people type so that main attendance can be populated...
						$sql='SELECT people_type_id FROM '.CA_PEO_TBL.' WHERE people_id="'.intval($people_id).'"';
						$person_type=$wpdb->get_var($sql);

						switch($person_type)
						{
							case 1:$adult++;break;
							case 2:$child++;break;
							case 3:$child++;break;
						}
					}
					$adult+=intval($_POST['visitor-adults']);
					$child+=intval($_POST['visitor-children']);
					$sql='INSERT INTO '.CA_IND_TBL.' (`date`,people_id,meeting_type,meeting_id) VALUES '.implode(",",$values);
					$wpdb->query($sql);

					//process main attendance table
					$wpdb->query('DELETE FROM '.CA_ATT_TBL .' WHERE `date`="'.$date.'" and service_id="'.intval($meeting[1]).'" AND mtg_type="'.esc_sql($meeting[0]).'"');
					if(empty($check))
					{
 						$sql='INSERT INTO '.CA_ATT_TBL .' (`date`,adults,children,service_id,mtg_type)VALUES("'.$date.'","'.$adult.'","'.$child.'","'.intval($meeting[1]).'","'.esc_sql($meeting[0]).'")';

						$wpdb->query($sql);
						
					}
					church_admin_refresh_rolling_average();
					$out.='<div class="notice notice-inline notice-success"><h2>'.__('Attendance saved','church-admin').'</h2></div>';
					require_once(CA_PATH.'display/graph.php');
					$meet="S/1";
					switch($meeting[0])
					{
						case'service':$meet='S/'.intval($meeting[1]);break;
						case'smallgroup':$meet='G/'.intval($meeting[1]);break;
						case'class':$meet='C/'.intval($meeting[1]);break;
						default:$meet="S/1";break;
					}
					$out.= church_admin_graph('weekly',$meet,date('Y-m-d',strtotime('-1 year')),date('Y-m-d'),900,500,TRUE);
			}

				/***************************************************************
				*
				*	Form
				*
				***************************************************************/

				//People Query

				switch($meeting[0])
				{
					case 'smallgroup':
						$sql='SELECT a.* FROM '.CA_PEO_TBL.' a, '.CA_MET_TBL.' b WHERE a.people_id=b.people_id AND b.meta_type="smallgroup" AND b.ID="'.intval($meeting[1]).'"';
						$meeting_type=__('Small Group','church-admin');
						$which=$wpdb->get_var('SELECT group_name FROM '.CA_SMG_TBL.' WHERE id="'.intval($meeting[1]).'"');
					break;
					default:
					case 'service':
						$sql='SELECT * FROM '.CA_PEO_TBL.' a, '.CA_SER_TBL.' b WHERE a.site_id=b.site_id AND b.service_id="'.intval($meeting[1]).'"';
						$meeting_type=__('Service','church-admin');
						$which=$wpdb->get_var('SELECT service_name FROM '.CA_SER_TBL.' WHERE service_id="'.intval($meeting[1]).'"');
					break;
				}
				//add in member types
				$membSQL='';
				$membsql=Array();
				if(!empty($_GET['member_type_id']))foreach($_GET['member_type_id'] AS $key=>$value){if(ctype_digit($value))  $membsql[]='a.member_type_id='.$value;}
				if(!empty($membsql)) {$membSQL=' AND ('.implode(' || ',$membsql).')';}
				//get relevant people
				$query=$sql.$membSQL.' ORDER BY last_name, first_name';

				$people=$wpdb->get_results($query);
				$already=array();
				$already_results=$wpdb->get_results('SELECT people_id FROM '.CA_IND_TBL.' WHERE `date`="'.date('Y-m-d').'" AND meeting_type="'.esc_sql($meeting[0]).'" AND meeting_id="'.esc_sql($meeting[1]).'"');
				if(!empty($already_results))foreach($already_results AS $already_row)$already[]=$already_row->people_id;

				if(!empty($people))
				{
					$out.= '<h3>'.$meeting_type.' - '.esc_html($which).'</h3>';
					$out.='<form action="" method="POST">';
					$date=date('Y-m-d');
					$out.='<p><label><strong>'.__('Date','church-admin').':</strong></label>'.church_admin_date_picker($date,'date',FALSE,'2011',date('Y',time()+60*60*24*365*10),'date','date').' '.__('Select date to edit individual attendance','church-admin').'</p>';
					$out.='<table class="wp-list-table striped"><thead><tr><th><input type="checkbox" class="all-people"/> '.__('Attended?','church-admin').'</th><th>'.__('Photo','church-admin').'</th><th>'.__('Name','church-admin').'</th><th>'.__('Address','church-admin').'</th></tr></thead>';

					foreach($people AS $person)
					{
						$out.='<tr><td><input type="checkbox" name="people_id[]" class="people_id" id="person-'.intval($person->people_id).'" value="'.intval($person->people_id).'"';
						if(is_array($already) && in_array($person->people_id,$already))$out.=' checked="checked" ';
						$out.='/></td>';
						if(!empty($person->attachment_id))
						{//photo available
							$out.='<td>'. wp_get_attachment_image( $person->attachment_id,'ca-people-thumb',NULL,array('class'=>'alignleft') ).'</td>';
						}
						else $out.='<td><img src="'.plugins_url('/images/default-avatar.jpg',dirname(__FILE__) ).'" width="75" height="75" class="frontend-image current-photo alignleft" alt="'.__('Photo of Person','church-admin').'"  /></td>';
						$name=array_filter(array($person->first_name,$person->middle_name,$person->prefix,$person->last_name));
						$address='';
						$address=$wpdb->get_var('SELECT address FROM '.CA_HOU_TBL.' WHERE household_id="'.intval($person->household_id).'"');
						$out.='<td><strong>'.esc_html(implode(" ",$name)).'</td><td>'.esc_html($address).'</td></tr>';
					}
					$out.= '<tr><th scope="row">'.__('How many visiting adults?','church-admin').'</th><td><input type="text" name="visitor-adults" placeholder="0"/></td></tr>';
					$out.= '<tr><th scope="row">'.__('How many visiting children?','church-admin').'</th><td><input type="text" name="visitor-children" placeholder="0"/></td></tr>';
					$out.='</table><p><input type="hidden" name="save_ind_att" value="yes"/><input type="submit" class="button-primary" value="'.__('Save','church-admin').'"/></p></form>';
					$nonce = wp_create_nonce("individual_attendance");
					$out.='<script type="text/javascript">jQuery(function($){
						$(".all-people:checkbox").change(function(){console.log("All people clicked");$(".people_id").not(this).prop("checked", this.checked); });
						$(".datex").on("change",function()
							{
								var date =$(".date").val();

								var args = {
									"action": "church_admin",
									"method": "individual_attendance",
									"nonce": "'.$nonce.'",
									"date": date,
									"meeting_type":"'.$meeting[0].'",
									"meeting_id":"'.$meeting[1].'"

								};
								console.log(args);
								// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
								$.getJSON(ajaxurl,args, function(data) {
									console.log(data);
									for(var count = 0; count < data.length; count++)
        					{
        						var item=data[count];
										console.log(item);
										$("#"+item).prop("checked","checked");
									}
								});
							});
						});</script>';
				}
				else
				{
					$out.='<p>'.__('No people are registered for that choice','church-admin').'</p>';
				}
			}




		return $out;
}


/**
 *
 * Individual attendance csv download
 *
 * @author  	Andy Moyle
 * @param    	null
 * @return   	html
 * @version  	1.2450
 * @date 		2017-01-03
 */

function church_admin_individual_attendance_csv()
{
	global $wpdb,$wp_locale;
	$out='';


		$services=$wpdb->get_results('SELECT * FROM '.CA_SER_TBL);
		if(!empty($services))
		{
			$option='';
			foreach($services AS $service)
			{
				$option.='<option value="service-'.$service->service_id.'">'.$service->service_name.' on '.$wp_locale->get_weekday($service->service_day).' at '.$service->service_time.'</option>';
			}
		}
		$smallgroups=$wpdb->get_results('SELECT * FROM '.CA_SMG_TBL);
		if(!empty($smallgroups))
		{
			foreach($smallgroups AS $smallgroup)$option.='<option class="smallgroup" value="smallgroup-'.intval($smallgroup->id).'">Small Group - '.esc_html($smallgroup->group_name).'</option>';
		}

		$out.='<form action="'.admin_url('admin.php?page=church_admin/index.php&action=individual_attendance&tab=services').'" method="POST"><table class="form-table">';
		$out.='<tr><th scope="row">'.__('Which Meeting','church-admin').'</th><td><select name="meeting">'.$option.'</select></td></tr>';
		$member_type=church_admin_member_type_array();
		$first=$option='';
		$out.='<tr><th scope="row">'.__('Member type','church-admin').'</th><td>';
		foreach($member_type AS $id=>$type)
		{
			$out.='<input type="checkbox" name="member_type_id[]" value="'.$id.'">'.$type.'<br/>';
		}
		$out.='</td></tr>';
		$date=date('Y-m-d');
		$out.='<tr><th scope="row">'.__('Start Date','church-admin').':</th><td>'.church_admin_date_picker($date,'start_date',FALSE,'2011',date('Y',time()+60*60*24*365*10),'start_date','start_date').'</td></tr>';
		$out.='<tr><th scope="row">'.__('End Date','church-admin').':</th><td>'.church_admin_date_picker($date,'end_date',FALSE,'2011',date('Y',time()+60*60*24*365*10),'end_date','end_date').'</td></tr>';
		$out.='<tr><td>&nbsp;</td><td><input type="hidden" name="ind_att_csv" value="yes"/><input type="submit" class="button-primary" value="'.__('Choose','church-admin').'"/></td></tr>';
		$out.='</table></form>';


	return $out;
}

function church_admin_output_ind_att_csv()
{
	global $wpdb;
	$debug=TRUE;
	if(!empty($debug))church_admin_debug('Processing csv');
		$meeting=explode("-",$_POST['meeting']);
		$out='';
		$sql='SELECT `date` FROM '.CA_IND_TBL.' WHERE `date`>="'.esc_sql($_POST['start_date']).'" AND meeting_type="'.esc_sql($meeting[0]).'" ORDER BY `date` ASC LIMIT 1';
		if(!empty($debug))church_admin_debug('Start date query'.$sql);
		$startdate=$wpdb->get_var($sql);
		if(!empty($debug))church_admin_debug('Start date '.$startdate);
		$sql='SELECT `date` FROM '.CA_IND_TBL.' WHERE `date`<="'.esc_sql($_POST['end_date']).'" AND meeting_type="'.esc_sql($meeting[0]).'" ORDER BY `date` DESC LIMIT 1';
		$enddate=$wpdb->get_var($sql);
		if(!empty($debug))church_admin_debug('End date sql '.$sql);
		$sql='SELECT `date` FROM '.CA_IND_TBL.' WHERE meeting_type="'.esc_sql($meeting[0]).'" AND `date`>="'.esc_sql($startdate).'" AND `date`<="'.esc_sql($enddate).'" GROUP BY `date`';
		$dates=$wpdb->get_results($sql);
		if(!empty($debug))church_admin_debug('Dates sql '.$sql);
		if(!empty($debug))church_admin_debug(print_r($dates,TRUE));

		if(empty($startdate)||empty($enddate)||empty($dates)){$out.='<p>No dates found</p>';}
		else
		{

			switch($meeting[0])
			{
				case 'smallgroup':
					$sql='SELECT a.* FROM '.CA_PEO_TBL.' a '.CA_MET_TBL.' b WHERE a.people_id=b.people_id AND b.meta_type="smallgroup" AND b.ID="'.intval($meeting[1]).'"';
					$meeting_type=__('Small Group','church-admin');
					$which=$wpdb->get_var('SELECT group_name FROM '.CA_SMG_TBL.' WHERE id="'.intval($meeting[1]).'"');
				break;
				default:
				case 'service':
					$sql='SELECT * FROM '.CA_PEO_TBL.' a, '.CA_SER_TBL.' b WHERE a.site_id=b.site_id AND b.service_id="'.intval($meeting[1]).'"';
					$meeting_type=__('Service','church-admin');
					$which=$wpdb->get_var('SELECT service_name FROM '.CA_SER_TBL.' WHERE service_id="'.intval($meeting[1]).'"');
				break;
			}
			//add in member types
			$membSQL=$membsql='';
			if(!empty($_GET['member_type_id']))foreach($_GET['member_type_id'] AS $key=>$value){if(ctype_digit($value))  $membsql[]='a.member_type_id='.$value;}
			if(!empty($membsql)) {$membSQL=' AND ('.implode(' || ',$membsql).')';}
			//get relevant people
			$query=$sql.$membSQL.' ORDER BY last_name, first_name';
			$people=$wpdb->get_results($query);
			if(!empty($people))
			{
				$csvheader=array('"Name"','"Address"','"Cell"','"Phone"');
				foreach($dates AS $date)$csvheader[]='"'.mysql2date(get_option('date_format'),$date->date).'"';
				$csv=implode(',',$csvheader)."\r\n";;
				foreach($people AS $person)
				{
					$name=array_filter(array($person->first_name,$person->middle_name,$person->prefix,$person->last_name));
					$household=$wpdb->get_row('SELECT address,phone FROM '.CA_HOU_TBL.' WHERE household_id="'.intval($person->household_id).'"');
					$csvline=array('"'.implode(" ",$name).'"');
					if(!empty($household->address)){$csvline[]='"'.esc_html($household->address).'"';}else{$csvline[]='" "';}
					if(!empty($person->mobile)){$csvline[]='"'.esc_html($person->mobile).'"';}else{$csvline[]='" "';}
					if(!empty($household->phone)){$csvline[]='"'.esc_html($household->phone).'"';}else{$csvline[]='" "';}
					foreach($dates AS $date)
					{
						$attendance=$wpdb->get_var('SELECT attendance_id FROM '.CA_IND_TBL.' WHERE meeting_type="'.esc_sql($meeting[0]).'" AND meeting_id="'.esc_sql($meeting[1]).'" AND `date`="'.esc_sql($date->date).'" AND people_id="'.intval($person->people_id).'"');
						if(!empty($attendance)){$csvline[]='"x"';}else{$csvline[]='" "';}
					}
					$csv.=implode(',',$csvline)."\r\n";

				}
				if(!empty($debug))church_admin_debug($csv);
				header('Content-Description: File Transfer');
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename="attendance-'.$meeting[0].'.csv"');
				header('Content-Transfer-Encoding: binary');
				header('Expires: 0');
				header('Cache-Control: must-revalidate');
				header('Pragma: public');
				header('Content-Disposition: attachment; filename="attendance-'.$meeting[0].'.csv"');
				echo $csv;
				exit();
			}
}
echo $out;
}
?>
