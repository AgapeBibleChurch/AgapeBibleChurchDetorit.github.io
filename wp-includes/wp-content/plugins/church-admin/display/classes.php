<?php

function church_admin_display_classes($today)
{
	global $wpdb,$current_user;
	if(defined('CA_DEBUG'))$wpdb->show_errors();
	$user = wp_get_current_user();

	$out='';
	if(empty($today))
	{
		$sql='SELECT * FROM '.CA_CLA_TBL.' WHERE end_date >= CURDATE() ';
	}
	else
	{
		$sql='SELECT a.* FROM '.CA_CLA_TBL.' a, '.CA_DATE_TBL.' b WHERE a.event_id=b.event_id AND b.start_date=CURDATE() ';
	}
	if(defined('CA_DEBUG'))church_admin_debug($sql);
	$results=$wpdb->get_results($sql);
	if(!empty($results))
	{
		foreach($results AS $row)
		{
			$out.=church_admin_display_class($row->class_id);
		}
		$out.='<script type="text/javascript">jQuery(function($){

		$(".class-toggle").click(function(){
				var id=this.id;
				console.log(id);
				$("."+id).toggle();
			});

		});</script>';
	}//there are classes
	else
	{
		if(empty($today))$out.='<p>'.__('No classes running at the moment','church-admin').'</p>';
		else{$out.='<p>'.__('No classes today','church-admin').'</p>';}
	}

	return $out;

}

function church_admin_display_class($class_id=NULL)
{
	global $wpdb,$current_user;
	$wpdb->show_errors();
	$user = wp_get_current_user();
	$out='';
	$sql='SELECT * FROM '.CA_CLA_TBL.' WHERE class_id="'.intval($class_id).'"';

	$row=$wpdb->get_row($sql);
	if(!empty($row))
	{
			$out.='<h2 class="class-toggle" id="class-'.intval($class_id).'">'.esc_html($row->name).'('.__('Click to toggle','church-admin').')</h2>';
			$out.='<div class="class-'.intval($class_id).'" style="display:none">';
			if(!empty($row->description))
			{
				$out.='<p>'.esc_html($row->description).'</p>';
				if(!empty($row->next_start_date))$out.='<p>'.mysql2date(get_option('date_format'),$row->next_start_date);
				if(!empty($row->end_date))$out.=' - '.mysql2date(get_option('date_format'),$row->end_date);
				$out.='</p>';
			}
			//check if logged in and whether leader or punter
			if(!is_user_logged_in())
			{
				$out.='<div class="login"><h3>'.__('Please login to book in yourself or check in attendees','church-admin').'</h3>'.wp_login_form(array('echo'=>FALSE,'form_id'=> 'login'.$class_id,
	'id_username'    => 'user_login'.$class_id,
	'id_password'    => 'user_pass'.$class_id,
	'id_remember'    => 'rememberme'.$class_id,
	'id_submit'      => 'wp-submit'.$class_id)).'</div>';
			}
			else
			{
				//form post
				if(!empty($_POST['class-check-in'])&&!empty($_POST['class_id']))
				{

					//add people to the class
					if(!empty($_POST['delegate']))
					{
						$people=maybe_unserialize(church_admin_get_people_id(stripslashes($_POST['delegate'])));

						if(!empty($people))foreach($people AS $key=>$people_id)
						{
							church_admin_update_people_meta(intval($_POST['class_id']),$people_id,'class');

						}
					}
					if(!empty($_POST['people_id']))
					{
						$adult=$child=0;
						foreach($_POST['people_id'] AS $key=>$people_id)
						{
							$check=$wpdb->get_var('SELECT attendance_id FROM '.CA_IND_TBL.' WHERE `date`="'.esc_sql($_POST['class_date']).'" AND people_id="'.intval($people_id).'" AND meeting_type="class" AND meeting_id="'.intval($_POST['class_id']).'"' );

							if(empty($check))
							{
								$wpdb->query('INSERT INTO '.CA_IND_TBL.' (`date`,people_id,meeting_type,meeting_id) VALUES ("'.esc_sql($_POST['class_date']).'","'.intval($people_id).'","class","'.intval($_POST['class_id']).'")');
								//check people type
								$sql='SELECT people_type_id FROM '.CA_PEO_TBL.' WHERE people_id="'.intval($people_id).'"';
								$person_type=$wpdb->get_var($sql);
								switch($person_type)
								{
									case 1:$adult++;break;
									case 2:$child++;break;
									case 3:$child++;break;
								}
							}
						}

						if(empty($check))
						{
							$sql='INSERT INTO '.CA_ATT_TBL .' (`date`,adults,children,service_id,mtg_type) VALUES ("'.esc_sql($_POST['class_date']).'","'.$adult.'","'.$child.'","'.intval($_POST['class_id']).'","class")';
							$wpdb->query($sql);
						}
					}
					$out.='<div class="notice notice-sucess inline">'.__('Class Checked in','church-admin').'</div>';
				}
				elseif(!empty($_POST['class-book-in'])&&!empty($_POST['class_id']))
				{
					foreach($_POST['people_id'] AS $key=>$people_id)
					{
						church_admin_update_people_meta(intval($_POST['class_id']),intval($people_id),'class');
					}
					$out.='<div class="notice notice-sucess inline">'.__('Class Booked','church-admin').'</div>';
				}
				else
				{//show data
					$person=$wpdb->get_row('SELECT * FROM '.CA_PEO_TBL.' WHERE user_id="'.intval($user->ID).'"');

					if(!empty($person))
					{
						//logged in user is in the direcory

						$ldrs=maybe_unserialize($row->leadership);


						if(is_array($ldrs)&&in_array($person->people_id,$ldrs))
						{
							//leader of course, so show who is in the class to check them in and then fireld to add more people



							$out.='<h3>'.__('Check in class students','church-admin').'</h3>';
							$out.='<form action="" method="POST"><table class="form-table">';
							$dates=$wpdb->get_results('SELECT start_date FROM '.CA_DATE_TBL.' WHERE event_id="'.intval($row->event_id).'" AND start_date>=CURDATE() ORDER BY start_date');
							if(!empty($dates)){
								$class_dates='<select name="class_date">';
								foreach($dates AS $date){$class_dates.='<option value="'.esc_html($date->start_date).'">'.mysql2date(get_option('date_format'),$date->start_date).'</option>';}
								$class_dates.='</select>';
							}
							else{$class_dates=__('No class dates set, please re-edit the class','church-admin');}

							$out.='<tr><th scope="row">'.__('Class Date','church-admin').'</th><td>'.$class_dates.'</td></tr>';
							$people_result=church_admin_people_meta($row->class_id,NULL,'class');

							if(!empty($people_result))
							{//people are booked in for class, so can check them in
								foreach($people_result AS $data)
								{
									$name=array_filter(array($data->first_name,$data->prefix,$data->last_name));
									$out.='<tr><th scope="row">'.esc_html(implode(" ",$name)).'</th><td><input type="checkbox" value="'.intval($data->people_id).'" ';

									$out.='name="people_id[]"/></td></tr>';

								}
							}
							$out.= '<tr><th scope="row">'.__('Add people on the class','church-admin').'</th><td><input type="text" class="large-text" style="width:100%" name="delegate" placeholder="'.__('Type in names, separated by a comma','church-admin').'"</td></tr>';
							$out.='<tr><td colspan=2><input type="hidden" name="class_id" value="'.intval($row->class_id).'"/><input type="hidden" name="class-check-in" value="yes"/><input type="submit" value="'.__('Check in','church-admin').'"/></td></tr></table></form>';

						}
						else
						{
							//not a leader so show the whole household so they can book in people
							//grab household
							$household=$wpdb->get_results('SELECT CONCAT_WS(" ",first_name,last_name) AS name,people_id FROM '.CA_PEO_TBL.' WHERE household_id="'.intval($person->household_id).'" ORDER BY people_order');
							$out.='<h3>'.__('Book in members of your household','church-admin').'</h3>';
							$out.='<form action="" method="POST"><table class="form-table">';
							foreach($household AS $data)
							{
								$out.='<tr><td style="width:50px;"><input type="checkbox" value="'.intval($data->people_id).'" name="people_id[]" ';
								$check=$wpdb->get_var('SELECT meta_id FROM '.CA_MET_TBL.' WHERE meta_type="class" AND people_id="'.intval($data->people_id).'" AND ID="'.intval($row->class_id).'"');
								if($check) $out.=' checked="checked" ';
								$out.='"/></td><td  class="ca-names">'.esc_html($data->name).'</td></tr>';

							}
							$out.='<tr><td colspan=2><input type="hidden" name="class_id" value="'.intval($row->class_id).'"/><input type="hidden" name="class-book-in" value="yes"/><input type="submit" value="'.__('Book','church-admin').'"/></td></tr></table></form>';
						}


					}

				}//show data

			}//logged in
							$out.='</div>';

	}
	else
	{
		$out.=__('No class for today to display','church-admin');
	}
	return $out;
}
