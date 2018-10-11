<?php
//use $email=TRUE to stop javascript activating as well eg for SMS and CSV download
function church_admin_directory_filter($JSUse=TRUE,$email=FALSE)
{

	//Make $email TRUE when used for email and SMS
	global $wpdb;
	$church_admin_marital_status=get_option('church_admin_marital_status');
	if($JSUse)echo'<h2>'.__('Filtered Address List','church-admin').'</h2>';

	if($JSUse)echo'<p><strong>'.__('Use the checkboxes to filter the address list you will see','church-admin').'</strong></p>';
	if($JSUse)echo'<p><strong>'.__('People totals in brackets are for that particular item only','church-admin').'</strong></p>';
	if(empty($email)){echo'<form><div id="filters" class="ca-box">';}else{echo'<div id="filters1" class="ca-box">';}
	$class='category';
	//gender
	$genders=get_option('church_admin_gender');
	echo'<div class="filterblock"><label>'.__('Gender','church-admin').'</label>';
	foreach($genders AS $key=>$gender)
	{
		$count=$wpdb->get_var('SELECT COUNT(*) FROM '.CA_PEO_TBL. ' WHERE  sex="'.esc_sql($key).'" ');
		echo'<p><input type="checkbox" name="check[]" class="'.$class.' gender" value="ge/'.sanitize_title($gender).'" />'.esc_html($gender).' ('.intval($count).')</p>';
	}
	echo'</div>';
	//data protection
	echo'<div class="filterblock"><label>'.__('Personal Data','church-admin').'</label>';
	$notCount=$wpdb->get_var('SELECT COUNT(*) FROM '.CA_PEO_TBL. ' WHERE  gdpr_reason IS NULL OR gdpr_reason="" ');
	$count=$wpdb->get_var('SELECT COUNT(*) FROM '.CA_PEO_TBL. ' WHERE  gdpr_reason IS NOT NULL OR gdpr_reason!="" ');
	echo'<p><input type="checkbox" name="check[]" class="'.$class.' gdpr" value="da/1" />'.__('Confirmed','church-admin').' ('.intval($count).')</p>';
	echo'<p><input type="checkbox" name="check[]" class="'.$class.' gdpr" value="da/0" />'.__('Not Confirmed','church-admin').' ('.intval($notCount).')</p>';
	echo'</div>';
	//people types
	$people_types=get_option('church_admin_people_type');
	if(!empty($people_types))
	{
		echo'<div class="filterblock"><label>'.__('People Types','church-admin').'</label>';
		$count=$wpdb->get_var('SELECT COUNT(*) FROM '.CA_PEO_TBL);
		echo'<p><input type="checkbox" name="check[]" class="all '.$class.'" data-id="people" value="all"/><strong>'.__('All','church-admin').' ('.intval($count).')</strong></p>';
		foreach($people_types AS $key=>$people_type)
		{
			$count=$wpdb->get_var('SELECT COUNT(*) FROM '.CA_PEO_TBL. ' WHERE  people_type_id="'.esc_sql($key).'" ');
			echo'<p><input type="checkbox" name="check[]" class="'.$class.' people" value="pe/'.sanitize_title($people_type).'" />'.esc_html($people_type).' ('.intval($count).')</p>';
		}
		echo'</div>';
	}
	//active
	echo'<div class="filterblock"><label>'.__('Active/Deactivated','church-admin').'</label>';
	$count=$wpdb->get_var('SELECT COUNT(*) FROM '.CA_PEO_TBL. ' WHERE  active="1" ');
	echo'<p><input  type="checkbox" name="check[]" class="'.$class.' marital" value="ac/1" />'.__('Active','church-admin').' ('.intval($count).')</p>';
	$count=$wpdb->get_var('SELECT COUNT(*) FROM '.CA_PEO_TBL. ' WHERE  active="0" ');
	echo'<p><input  type="checkbox" name="check[]" class="'.$class.' marital" value="ac/0" />'.__('Deactivated','church-admin').' ('.intval($count).')</p>';
	echo'</div>';
	//marital status
	echo'<div class="filterblock"><label>'.__('Marital Status','church-admin').'</label>';
	foreach($church_admin_marital_status AS $key=>$status)
	{
		$count=$wpdb->get_var('SELECT COUNT(*) FROM '.CA_PEO_TBL. ' WHERE  marital_status="'.esc_sql($status).'" AND people_type_id=1');
		echo'<p><input  type="checkbox" name="check[]" class="'.$class.' marital" value="ma/'.sanitize_title($status).'" />'.esc_html($status).' ('.intval($count).')</p>';
	}
	echo'</div>';
	//Sites
	$results=$wpdb->get_results('SELECT venue,site_id FROM '.CA_SIT_TBL.' ORDER BY venue ASC');
	if(!empty($results))
	{
		echo'<div class="filterblock"><label>'.__('Sites','church-admin').'</label>';
		echo'<p><input type="checkbox" name="check[]" class="all '.$class.'" data-id="sites" value="all"/><strong>'.__('All','church-admin').'</strong></p>';

		foreach($results AS $row)
		{
				$count=$wpdb->get_var('SELECT COUNT(*) FROM '.CA_PEO_TBL. ' WHERE  site_id="'.esc_sql($row->site_id).'" ');
				echo'<p><input type="checkbox" name="check[]" class="'.$class.' sites" value="si/'.sanitize_title($row->venue).'" />'.esc_html($row->venue).' ('.intval($count).')</p>';

		}
				echo'</div>';
	}
	//Member Types
	$results=$wpdb->get_results('SELECT member_type_id,member_type FROM '.CA_MTY_TBL.' ORDER BY member_type_order ASC');
	if(!empty($results))
	{
		echo'<div class="filterblock"><label>'.__('Member Types','church-admin').'</label>';
		echo'<p><input type="checkbox" name="check[]" class="all '.$class.'" data-id="member" value="all"/><strong>'.__('All','church-admin').'</strong></p>';
		foreach($results AS $mt)
		{
			$count=$wpdb->get_var('SELECT COUNT(*) FROM '.CA_PEO_TBL.' WHERE member_type_id="'.intval($mt->member_type_id).'"');
			echo'<p><input  type="checkbox" name="check[]" class="'.$class.' member" value="mt/'.sanitize_title($mt->member_type).'" />'.esc_html($mt->member_type).' ('.intval($count).')</p>';
		}
		echo'</div>';
	}
	//Small Groups
	$results=$wpdb->get_results('SELECT id, group_name FROM '.CA_SMG_TBL.' ORDER BY group_name ASC');
	if(!empty($results))
	{

		echo'<div class="filterblock"><label>'.__('Small Groups','church-admin').'</label>';
		echo'<p><input type="checkbox" name="check[]" class="all '.$class.'" data-id="groups" value="all"/><strong>'.__('All','church-admin').'</strong></p>';
		echo'<p><input type="checkbox"name="check[]" class="'.$class.'" value="gp/no-group"/><strong>'.__('Not in a group (overrides other small group selections)','church-admin').'</strong></p>';
		echo'</p><p>';

			foreach($results AS $row)
			{
				$count=$wpdb->get_var('SELECT COUNT(*) FROM '.CA_MET_TBL.' WHERE ID="'.intval($row->id).'" AND meta_type="smallgroup"');

				echo'<span ><input type="checkbox" name="check[]" class="'.$class.' groups" value="gp/'.sanitize_title($row->group_name).'" />'.esc_html($row->group_name).' ('.intval($count).')</span><br/>';

			}
		}

				echo'</p></div>';


	//Ministries
	$results=$wpdb->get_results('SELECT ministry,ID FROM '.CA_MIN_TBL.' ORDER BY ministry ASC');
	if(!empty($results))
	{
		echo'<div class="filterblock"><label>'.__('Ministries','church-admin').'</label>';
		echo'<p><input type="checkbox" name="check[]" class="all '.$class.'" data-id="ministries" value="all"/><strong>'.__('All','church-admin').'</strong></p>';

		 echo '<p>';
		foreach($results AS $row)
		{
			$count=$wpdb->get_var('SELECT COUNT(*) FROM '.CA_MET_TBL.' WHERE ID="'.intval($row->ID).'" AND meta_type="ministry"');

			echo'<span ><input type="checkbox" name="check[]" class="'.$class.' ministries" value="mi/'.sanitize_title($row->ministry).'" />'.esc_html($row->ministry).' ('.intval($count).')</span><br/>';

		}
				echo'</p></div>';
	}

	//year of birth
	$years=$wpdb->get_results('SELECT YEAR(date_of_birth) AS year FROM '.CA_PEO_TBL.' WHERE date_of_birth!="0000-00-00" GROUP BY YEAR(date_of_birth) ORDER BY YEAR(date_of_birth) ASC');
	if(!empty($years))
	{
			echo'<div class="filterblock"><label>'.__('Year of Birth','church-admin').'</label>';
			echo '<p><select name="check[]" class="'.$class.' parents"><option>'.__('Select year','church-admin').'</option>';
			foreach($years AS $year)
			{
				$count=$wpdb->get_var('SELECT COUNT(*) FROM '.CA_PEO_TBL.' WHERE YEAR(date_of_birth)="'.esc_sql($year->year).'"');

				echo'<option value="ye/'.sanitize_title($year->year).'">'.esc_html($year->year).' ('.intval($count).')</option>';
			}
			echo'</select></p></div>';
	}
	$months=$wpdb->get_results('SELECT MONTH(date_of_birth) AS month FROM '.CA_PEO_TBL.' WHERE date_of_birth!="0000-00-00" GROUP BY MONTH(date_of_birth) ORDER BY MONTH(date_of_birth) ASC');
	if(!empty($months))
	{
			echo'<div class="filterblock"><label>'.__('Month of Birth','church-admin').'</label>';
			echo '<p>';
			foreach($months AS $month)
			{
				$count=$wpdb->get_var('SELECT COUNT(*) FROM '.CA_PEO_TBL.' WHERE MONTH(date_of_birth)="'.esc_sql($month->month).'"');
				echo'<span ><input type="checkbox" name="check[]" class="'.$class.' parents" value="mo/'.sanitize_title($month->month).'" />'.mysql2date('F','2018-'.sprintf('%02d',$month->month).'-01').' ('.intval($count).')</span>';
				echo'<br/>';
			}
			echo'</p></div>';
	}
	//parents
	$results=$wpdb->get_results('SELECT * FROM '.CA_KID_TBL.' ORDER BY youngest');
	if(!empty($results))
	{
		echo'<div class="filterblock"><label>'.__('Parents with children in...','church-admin').'</label>';
		echo'<p><input type="checkbox" name="check[]" class="all '.$class.'" data-id="parents" value="all"/><strong>'.__('All','church-admin').'</strong></p>';
		echo '<p>';
		foreach($results AS $row)
		{

			echo'<span ><input type="checkbox" name="check[]" class="'.$class.' parents" value="pa/'.sanitize_title($row->id).'" />'.esc_html($row->group_name).'</span>';
			echo'<br/>';
		}
		echo'<p></div>';
	}
	//custom FIELDS
	$customFields=get_option('church_admin_custom_fields');

	if(!empty($customFields))
	{

		foreach ($customFields AS $ID=>$field)
		{

			$type=$field['type'];
			switch($type)
			{
				case'boolean';
				    $counttrue=$wpdb->get_var('SELECT COUNT(*) FROM '.CA_CUST_TBL. ' WHERE  custom_id='.intval($ID).' AND data=1 ');
					$countfalse=$wpdb->get_var('SELECT COUNT(*) FROM '.CA_CUST_TBL. ' WHERE  custom_id='.intval($ID).' AND data=0 ');

					echo'<div class="filterblock"><label>'. esc_html($field['name']).'</label>';
					echo'<p><span ><input type="checkbox" name="check[]" class="'.$class.' parents" value="cu/bo~1~'.intval($ID).'" />'.__('Yes','church-admin').' ('.$counttrue.') </span><span ><input type="checkbox" name="check[]" class="'.$class.' parents" value="cu/bo~0~'.intval($ID).'"/>'.__('No','church-admin').' ('.$countfalse.')</span>';

					echo'</div>';
				break;
				case 'date':
					$dates=$wpdb->get_results('SELECT `data` AS customDate FROM '.CA_CUST_TBL.' WHERE data!="0000-00-00" AND custom_id="'.intval($ID).'" GROUP BY `data` ORDER BY `data` ASC');

					if(!empty($dates))
					{
						echo'<div class="filterblock"><label>'.esc_html($field['name']).'</label><p>';

						if($wpdb->num_rows>20)
						{
							echo'<select name="check[]" class="'.$class.' parents"><option>'.__('Please choose a date','church-admin').'</option>';
							foreach($dates as $date)
							{
								if(!empty($date->customDate))
								{
									echo'<option value="cu/da~'.sanitize_title($date->customDate).'" />'.mysql2date(get_option('date_format'),$date->customDate).'</option>';

								}
							}
							echo'</select>';
						}
						else{
							foreach($dates as $date)
							{
								if(!empty($date->customDate))
								{
									echo'<span ><input type="checkbox" name="check[]" class="'.$class.' parents" value="cu/da~'.sanitize_title($date->customDate).'" />'.mysql2date(get_option('date_format'),$date->customDate).'</span>';
									echo'<br/>';
								}
							}
						}
						echo'</p></div>';
					}
				break;
				case'text':
					$sql='SELECT `data` AS textString FROM '.CA_CUST_TBL.' WHERE `data`!="" AND custom_id="'.intval($ID).'" ORDER BY `data` ASC';
					$texts=$wpdb->get_results($sql);

					if(!empty($texts))
					{
						echo'<div class="filterblock"><label>'.esc_html($field['name']).'</label><p>';
						foreach($texts AS $text)
						{
							if(!empty($text->textString))
							{
								$string=substr($text->textString,0,50);
								echo'<span ><input type="checkbox" name="check[]" class="'.$class.' parents" value="cu/tx~'.urlencode($string).'" />'.esc_html($string).'</span>';
								echo'<br/>';
							}
						}
						echo'</p></div>';
					}
				break;
			}

		}

	}
echo'</div>';
if(empty($email))echo'</form>';
	$nonce = wp_create_nonce("church_admin_filter");
	if($JSUse)echo'

	<script >
		jQuery(document).ready(function($) {
			$("#filters .all").on("change", function(){
				var id = this.attr("data-id");

				$("input."+id).prop("checked", !$("."+id).prop("checked"))
			});
		   $("#filters .category").on("change", function(){

      			var category_list = [];
      			$("#filters :input:checked").each(function(){
							var category = $(this).val();
        			category_list.push(category);

        		});
						$("#filters :selected").each(function(){

        			category = $(this).val();
							console.log(category);
        			category_list.push(category);

        		});
      			var data = {
				"action": "church_admin",
				method:"filter",
				"data": category_list,
				"nonce": "'.$nonce.'"
				};
				console.log(category_list);
	$("#filtered-response").html(\'<p style="text-align:center"><img src="'.admin_url().'/images/wpspin_light-2x.gif"/></p>\');
		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post(ajaxurl, data, function(response) {
			$("#filtered-response").html(response);
		});
			});
		});
	</script>
	<div id="filtered-response"></div>
	';

}



function church_admin_filter_process()
{
	//if changes made here also update email.php

	global $wpdb;
	$ptypes=get_option('church_admin_people_type');
	$church_admin_marital_status=get_option('church_admin_marital_status');
	$out='';
	$group_by='';
	$gdpr=$custom=$months=$years=$member_types=$parents=$genders=$people_types=$sites=$smallgroups=$ministries=array();
	$customSQL=$monthSQL=$yearSQL=$marritalSQL=$genderSQL=$memberSQL=$peopleSQL=$smallgroupsSQL=$ministriesSQL=$filteredby=array();
	$gdprSQL='';
	$sql= church_admin_build_filter_sql($_POST['data']);
	if(defined('CA_DEBUG'))church_admin_debug($sql);
	$results=$wpdb->get_results($sql);
	echo'<h2>'.__('Filter results: ','church-admin').'</h2>';
	if(!empty($results))
	{

		echo'<table class="widefat striped">';
		$table_header=array(__('Edit','church-admin'),__('Delete','church-admin'),__('Active?','church-admin'),__('Name','church-admin'),__('People type','church-admin'),__('Home phone','church-admin'),__('Cellphone','church-admin'),__('Email','church-admin'),__('Address','church-admin'),__('User account','church-admin'));
		echo'<thead><tr><th>'.implode('</th><th>',$table_header).'</th></tr></thead><tbody>';
		foreach($results AS $row)
		{
			$class=array();
			if(!empty($row->private))$class[]='ca-private';
			if(empty($row->active))$class[]='ca-deactivated';
			if(!empty($class)){$classes=' class="'.implode(" ",$class).'"';}else$classes='';
			echo '<tr '.$classes.' id="row'.intval($row->people_id).'"><td><a href="'.wp_nonce_url('admin.php?page=church_admin/index.php&amp;action=edit_people&amp;people_id='.$row->people_id.'&amp;household_id='.$row->household_id,'edit_people').'">'.__('Edit','church-admin').'</a></td>';
			echo'<td><a href="'.wp_nonce_url('admin.php?page=church_admin/index.php&amp;action=delete_people&amp;people_id='.$row->people_id.'&amp;household_id='.$row->household_id,'edit_people').'">'.__('Delete','church-admin').'</a></td>';
			if(!empty($row->active)){$activate=__('Active','church-admin');}else{$activate=__('Deactive','church-admin');}
			echo'<td class="activate" id="'.intval($row->people_id).'">'.$activate.'  <span class="dashicons dashicons-info help" id="active-message"></span></td>';
			$name=array_filter(array($row->first_name,$row->middle_name,$row->prefix,$row->last_name));
			echo'<td class="ca-names">'.esc_html(implode(' ',$name)).'</td>';
			echo'<td>'.esc_html($ptypes[$row->people_type_id]).'</td>';
			echo'<td class="ca-phone">'.esc_html($row->phone).'</td>';
			echo'<td class="ca-mobile">'.esc_html($row->mobile).'</td>';
			if(!empty($row->email)){echo'<td class="ca-email"><a href="mailto:'.$row->email.'">'.esc_html($row->email).'</a></td>';}else{echo'<td>&nbsp;</td>';}
			echo'<td class="ca-addresses"><a href="'.wp_nonce_url('admin.php?page=church_admin/index.php&amp;action=edit_household&amp;household_id='.$row->household_id,'edit_household').'">'.esc_html($row->address).'</a></td>';
			if(!empty($row->email))
			{//user account only relevant for people with email
				if(!empty($row->user_id))
				{
					$user_info=get_userdata($row->user_id);
					$user=$user_info->user_login;
				}
				else
				{
					//check if a user exists for this email
					$user_id=email_exists($row->email);
					$unassigned_user=get_userdata($user_id);
					if(!empty($user_id))
					{
						$user='<span class="ca_connect_user" data-people_id="'.intval($row->people_id).'" data-user_id="'.intval($user_id).'">'.__('Connect','church-admin').' '.$unassigned_user->user_login.'</span>';

					}
					else
					{
						$user='<span class="ca_create_user" data-people_id="'.intval($row->people_id).'" >'.__('Create user account','church-admin').'</span>';

					}
				}
			}else{$user='&nbsp;';}
			echo'<td><div class="ca-names userinfo'.intval($row->people_id).'">'.$user.'</div></td>';
			echo'</tr>'."\r\n";

		}
		echo'</tbody><tfoot><tr><th>'.implode('</th><th>',$table_header).'</th></tr></tfoot></table>';

	}else{echo'<p>'.__('Your filters produced no results. Please try again.','church-admin').'</p>';}
	$connect_nonce = wp_create_nonce("connect_user");
	$create_nonce = wp_create_nonce("create_user");
	echo'<script >jQuery(document).ready(function($) {
			$(".ca_connect_user").click(function() {
			var people_id=$(this).attr("data-people_id");
			var data = {
			"action": "church_admin",
			"method": "connect_user",
			"people_id": people_id,
			"user_id": $(this).attr("data-user_id"),
			"nonce": "'.$connect_nonce.'",
			dataType: "json"
			};console.log(data);
			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			jQuery.post(ajaxurl, data, function(response)
			{
				var data=JSON.parse(response);
				console.log("body .userinfo"+data.people_id + " "+data.login)
				$(".userinfo"+data.people_id).replaceWith(data.login);
			});

		});
		$(".ca_create_user").click(function() {
			var people_id=$(this).attr("data-people_id");
			var data = {
			"action": "church_admin",
			"method": "create_user",
			"people_id": $(this).attr("data-people_id"),
			"nonce": "'.$create_nonce.'",
			dataType:"json"
			};
			console.log(data);
			// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
			jQuery.post(ajaxurl, data, function(response)
			{
				var data=JSON.parse(response);
				console.log("body .userinfo"+data.people_id + " "+data.login)
				$(".userinfo"+data.people_id).replaceWith(data.login);
			});
		});
			});</script>';

}


function church_admin_filter_email_count($type)
{
	//if changes made here also update email.php

	global $wpdb;
	$church_admin_marital_status=get_option('church_admin_marital_status');
	$out='';
	$group_by='';
	$member_types=$genders=$people_types=$sites=$smallgroups=$ministries=array();
	$maritalSQL=$genderSQL=$memberSQL=$peopleSQL=$smallgroupsSQL=$ministriesSQL=$filteredby=array();
$sql= church_admin_build_filter_sql($_POST['data']);

	$result=$wpdb->get_results($sql);
	$count=$wpdb->num_rows;
	if(empty($count))$count=0;
	if($type=='email'){return '<strong>'.$count.' email addresses</strong>';}else{return '<strong>'.$count.' mobile numbers</strong>';}
	}





	function church_admin_build_filter_sql($input)
	{
		global $wpdb;
		$church_admin_marital_status=get_option('church_admin_marital_status');
		foreach($input AS $key=>$data)
		{
			//extract posted data
			$temp=explode('/',$data);
			switch($temp[0])
			{
				case 'da':	if(empty($temp[1])){$gdprSQL=' a.gdpr_reason IS NULL OR a.gdpr_reason="" ';}else{$gdprSQL=' a.gdpr_reason IS NOT NULL AND a.gdpr_reason!="" ';}break;
				case 'ac':	$active[]=intval($temp[1]);break;
				case 'cu':	$custom[]=stripslashes($temp[1]);break;
				case 'mo':	$months[]=intval($temp[1]);break;
				case 'ye':	$years[]=intval($temp[1]);break;
				case 'ma': $marital[]=stripslashes($temp[1]);			break;
				case 'ge': 	$genders[]=stripslashes($temp[1]);			break;
				case 'mt': 	$member_types[]=stripslashes($temp[1]);		break;
				case 'pe':	$people_types[]=stripslashes($temp[1]);		break;
				case 'si':	$sites[]=stripslashes($temp[1]);			break;
				case 'gp':	$smallgroups[]=stripslashes($temp[1]);		break;
				case 'mi':	$ministries[]=stripslashes($temp[1]);		break;
				case 'pa':	$parents[]=stripslashes($temp[1]);		break;
			}
		}
		//create clauses for different
		if(!empty($active)&&is_array($active))
		{
			foreach($active AS $key=>$act)$activeSQL[]='a.active="'.$act.'" ';
		}
		if(!empty($custom)&&is_array($custom))
		{

			foreach($custom AS $key=>$cust)
			{
				$customData=explode('~',$cust);

				switch($customData[0])
				{
					case'bo':$customSQL[]='h.`custom_id`="'.intval($customData[2]).'" AND h.`data`="'.intval($customData[1]).'" AND h.people_id=a.people_id';break;
					case'da':$customSQL[]='h.`data`="'.esc_sql($customData[1]).'"AND h.people_id=a.people_id';break;
					case'tx': $customSQL[]=' h.`data` LIKE "%'.esc_sql(urldecode($customData[1])).'%" AND h.people_id=a.people_id';break;
				}

			}
		}
		if(!empty($months)&&is_array($months))
		{
				foreach($months AS $key=>$month)
				{
					$monthSQL[]=' MONTH(a.date_of_birth)="'.intval($month).'"';
				}

		}
		if(!empty($years)&&is_array($years))
		{
				foreach($years AS $key=>$year)
				{
					$yearSQL[]='YEAR(a.date_of_birth)="'.intval($year).'"';
				}

		}

		if(!empty($marital)&&is_array($marital))
		{
			foreach($church_admin_marital_status AS $key=>$status)
			{
				if(in_array(sanitize_title($status),$marital))$maritalSQL[]='a.marital_status="'.$status.'"';
			}
		}
		if(!empty($genders))
		{

			$sex=get_option('church_admin_gender');
			foreach($sex AS $key=>$gender)
			{

				if(in_array(sanitize_title($gender),$genders))
				{
					$genderSQL[]='(a.sex="'.intval($key).'")';
					$filteredby[]=$gender;
				}
			}

		}

		//end gender section
		//member types
		if(!empty($member_types)&&is_array($member_types))
		{

			$allmembers=$wpdb->get_results('SELECT * FROM '.CA_MTY_TBL);

			if(!empty($allmembers))
			{
				foreach($allmembers AS $onetype)
				{

					if(in_array(sanitize_title($onetype->member_type),$member_types))
					{
						$memberSQL[]='(a.member_type_id="'.$onetype->member_type_id.'" AND a.member_type_id=f.member_type_id)';
						$filteredby[]=$onetype->member_type;
					}
				}
			}
		}//end member_types

		//people types
		$ptypes=get_option('church_admin_people_type');
		if(!empty($people_types))
		{

			if(!in_array('all',$people_types))//only do if all not selected
			{
				$ptypes=get_option('church_admin_people_type');

				foreach($ptypes AS $key=>$ptype)
				{
					if(in_array(sanitize_title($ptype),$people_types))
					{
						$peopleSQL[]='(a.people_type_id="'.intval($key).'")';
						$filteredby[]=$ptype;
					}
				}
			}
		}//end people type section

		//sites

		if(!empty($sites)&&is_array($sites))
		{
			if(!in_array('all',$sites))//only do if all not selected
			{
				$campuses=$wpdb->get_results('SELECT * FROM '.CA_SIT_TBL);

				if(!empty($campuses))
				{
					foreach($campuses AS $campus)
					{
						if(in_array(sanitize_title($campus->venue),$sites))
						{
							$sitesSQL[]='(a.site_id="'.intval($campus->site_id).'")';
							$filteredby[]=$campus->venue;
						}
					}
				}
			}
		}//end sites
		//Parents
		//$parents is array of kidswork group_id we are looking for parents of!

		$kidsworkData=church_admin_whosin_kidswork_array();

		$kidsworkSQL=array();
		if(!empty($parents)&&is_array($parents))
		{
				foreach($parents AS $id=>$kidswork_group_id)
				{
							if(!empty($kidsworkData[$kidswork_group_id]['children']))
							{
									foreach($kidsworkData[$kidswork_group_id]['children'] AS $childData)
									{
											foreach($childData['parents'] AS $y=>$people_id)$kidsworkSQL[]='(a.people_id="'.intval($people_id).'")';
									}
							}
				}

		}

		//small groups

		if(!empty($smallgroups)&&is_array($smallgroups))
		{

			if(!in_array('all',$smallgroups))//only do if all not selected
			{
				$sgs=$wpdb->get_results('SELECT * FROM '.CA_SMG_TBL);
				if(!empty($sgs))
				{
					foreach($sgs AS $sg)
					{

						if(in_array(sanitize_title($sg->group_name),$smallgroups))
						{
							$smallgroupsSQL[]='a.people_id=(SELECT c.people_id FROM '.CA_MET_TBL.' c WHERE  c.ID="'.intval($sg->id).'" AND c.meta_type="smallgroup" AND c.people_id=a.people_id)';
							$filteredby[]=$sg->group_name;
						}
					}
				}
			}
			if(in_array('no-group',$smallgroups))
			{
				 $smallgroupsSQL=array('a.people_id NOT IN (SELECT people_id FROM '.CA_MET_TBL.' WHERE meta_type="smallgroup")');
					}
		}//end smallgroups


		//ministries
		if(!empty($ministries)&&is_array($ministries))
		{
			if(!in_array('all',$ministries))//only do if all not selected
			{
				$mins=$wpdb->get_results('SELECT * FROM '.CA_MIN_TBL);

				if(!empty($mins))
				{
					foreach($mins AS $min)
					{
						if(in_array(sanitize_title($min->ministry),$ministries))
						{
							$ministriesSQL[]=' a.people_id=(SELECT people_id FROM '.CA_MET_TBL.' c, '.CA_MIN_TBL.' g WHERE  c.ID="'.intval($min->ID).'" AND c.ID=g.ID AND c.meta_type="ministry" AND c.people_id=a.people_id)';
							$filteredby[]=$min->ministry;
						}
					}
				}
			}
		}//end smallgroups
		$other=$tbls='';
		 $group_by=' GROUP BY a.people_id ';
		 $columns=array('a.people_id','a.user_id','a.household_id','a.first_name','a.middle_name','a.prefix','a.last_name','a.people_type_id','a.email','a.mobile','a.sex','b.phone','b.address','b.privacy','a.active','a.marital_status','a.date_of_birth');
		$tables=array(CA_PEO_TBL.' a',CA_HOU_TBL.' b');
		$table_header=array(__('Edit','church-admin'),__('Delete','church-admin'),__('Activate','church-admin'),__('Name','church-admin'),__('People Type','church-admin'),__('Phone','church-admin'),__('Mobile','church-admin'),__('Email','church-admin'),__('Address','church-admin'),__('Site User','church-admin'));
		if(!empty($activeSQL))		$other.=' AND ('. implode(" OR ",$activeSQL).')';
		if(!empty($gdprSQL))			$other.=' AND '.$gdprSQL;
		if(!empty($maritalSQL))		$other.=' AND ('. implode(" OR ",$maritalSQL).')';
		if(!empty($genderSQL)) 		$other.=' AND ('. implode(" OR ",$genderSQL).')';
		if(!empty($peopleSQL)) 		$other.=' AND ('. implode(" OR ",$peopleSQL).')';
		if(!empty($maritalSQL)) 		$other.=' AND ('. implode(" OR ",$maritalSQL).')';
		if(!empty($kidsworkSQL))	$other.=' AND ('. implode(" OR ",$kidsworkSQL).')';
		if(!empty($yearSQL))		$other.=' AND ('. implode(" OR ",$yearSQL).')';
		if(!empty($monthSQL))		$other.=' AND ('. implode(" OR ",$monthSQL).')';
		if(!empty($sitesSQL)) 		{
										$other.=' AND ('. implode(" OR ",$sitesSQL).') AND a.site_id=d.site_id';
										$tables['d']=CA_SIT_TBL.' d';
										$columns[]='d.venue';
									}
		if(!empty($smallgroupsSQL)) 	{
										$other.=' AND ('. implode(" OR ",$smallgroupsSQL).') AND c.ID=e.id';
										$columns[]='e.group_name';
										$tables['c']=CA_MET_TBL.' c';
										$tables['e']=CA_SMG_TBL.' e';
									}
		if(!empty($memberSQL)) 		{
										$other.=' AND ('. implode(" OR ",$memberSQL).')';
										$columns[]='f.member_type';
										$tables['f']=CA_MTY_TBL.' f';
									}
		if(!empty($ministriesSQL)) 	{
										$other.=' AND ('. implode(" OR ",$ministriesSQL).')';
										$columns[]='g.ministry ';
										$tables['g']=CA_MIN_TBL.' g';
										$tables['c']=CA_MET_TBL.' c';
									}
		if(!empty($customSQL))
		{

										$other.=' AND ('. implode(" OR ", $customSQL).')';
										$columns[]='h.data ';
										$tables['h']=CA_CUST_TBL.' h';
		}
		foreach($tables AS $letter=>$table)$tbls.=', '.$table.' '.$letter;

		$sql='SELECT '.implode(", ",$columns).' FROM '.implode(", ",array_filter($tables)).' WHERE a.household_id=b.household_id '.$other.' '.$group_by.' ORDER BY a.last_name';
		if(defined('CA_DEBUG'))church_admin_debug($sql);
		return $sql;
	}
