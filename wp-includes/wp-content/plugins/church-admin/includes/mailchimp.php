<?php

if ( ! defined( 'ABSPATH' ) ) exit('You need Jesus!'); // Exit if accessed directly

/**
 *
 * Mailchimp sync
 *
 * @author  Andy Moyle
 * @param
 * @return   html string
 * @version  0.1
 *
 *
 */
 function church_admin_mailchimp_sync()
 {
	//$debug='true';
	global $wpdb,$people_type;
	if(!empty($debug))$wpdb->show_errors();
	require_once(plugin_dir_path(dirname(__FILE__)).'includes/mailchimp.inc.php');




	echo'<h2>Mailchimp Sync</h2>';

	$settings=get_option('church_admin_mailchimp');


	/*********************
	*
	* Set API key
	*
	**********************/
	if(!empty($_POST['save-api-key']))
	{
		$settings['api_key']=stripslashes($_POST['api_key']);
		update_option('church_admin_mailchimp',$settings);
	}

	echo'<h3 id="step1" class="click">'.__('Step 1 - API key (Click to toggle)','church-admin').'</h3><div class="step1" style="display:none">';
	if(empty($settings['api_key']))echo'<p><a target="_blank" href="https://kb.mailchimp.com/accounts/management/about-api-keys/#Find-or-Generate-Your-API-Key">'.__( 'Click here to get your Mailchimp API key','church-admin').'</a></p>';
	echo'<form action="" method="POST">
			<table>
				<tbody>
					<tr>
						<th scope="row">Mailchimp API key</th>
						<td><input type="text" name="api_key" ';
	if(!empty($settings['api_key']))echo ' value="'.esc_html($settings['api_key']).'"';
	echo '/></td><td><input type="hidden" name="save-api-key" value="true"/><input type="submit" class="button-secondary" value="'.esc_html(__('Save','church-admin')).'"/></td></tr></table></form></div>';

	/*********************
	*
	* Instantiate Mailchimp API
	*
	**********************/

	if(!empty($settings['api_key']))
	{
		$MailChimp = new MailChimp($settings['api_key']);
		$MailChimp->verify_ssl = 'false';
	}

	if(!empty($MailChimp))
	{
		$result = $MailChimp->get("lists");
		/*********************
		*
		* Set listID
		*
		**********************/
		if(!empty($_POST['save-listID']))
		{
			$settings['listID']=stripslashes($_POST['listID']);
			update_option('church_admin_mailchimp',$settings);
		}

		$lists=$result['lists'];
		echo '<h3 id="step2" class="click">'.__('Step 2 - Your Mailchimp lists  (Click to toggle)','church-admin').'</h3><div class="step2" style="display:none"><form action="" method="POST">';
		foreach($lists AS $list)
		{
			echo'<p><input type="radio" name="listID" value="'.esc_html($list['id']).'"';
			if(!empty($settings['listID']) && $settings['listID']==$list['id']) echo ' checked="checked" ';
			echo'/> '.esc_html($list['name']).'</p>';
		}
		echo'<p><input type="hidden" name="save-listID" value="true"/><input type="submit" class="button-secondary" value="'.esc_html(__('Save','church-admin')).'"/></p></form></div>';
		/*********************
		*
		* Sync section
		*
		**********************/
		/*********************
				*
				* Groupings
				*
				**********************/

				$people_types=get_option('church_admin_people_type');
				$gender=get_option('church_admin_gender');
				$small_groups=church_admin_small_groups_array();
				$member_types=church_admin_member_type_array();
				$sites=church_admin_sites_array();
				$ministries=church_admin_ministries_array();
				$hope_teams=church_admin_hope_teams_array();
        $kidswork=church_admin_kidswork_array();
				$totalinterests=count($hope_teams)+count($ministries)+count($sites)+count($member_types)+count($small_groups)+count($gender)+count($people_types)+count($kidswork);

				$groupings=array(

									__('People Type','church-admin')=>$people_types,
									__('Gender','church-admin')=> $gender,
									__('Small group','church-admin')=>$small_groups,
									__('Member Types','church-admin')=>$member_types,
									__('Sites','church-admin')=>$sites,
									__('Ministries','church-admin')=>$ministries,
									__('Hope Teams','church-admin')=>$hope_teams,
                  __('Parents','church-admin')=>$kidswork
								);

				//Only allowed 60 interests so make sure user has less than 60

				$saved_interests=get_option('mailchimp_interests');
				$selected=0;
				if(!empty($saved_interests))foreach($saved_interests AS $saved_interest){$selected+=count($saved_interest);}

		if(empty($_POST['sync']))
		{
			echo'<h3 id="step3" class="click">'.__('Step 3 - What interests to sync  (Click to toggle)','church-admin').'</h3><div class="step3" style="display:none">';
				echo '<p>'.sprintf(__('You have %1$s available interests to sync. (The max is 60)','church-admin'),$totalinterests).'.</p>';
				echo '<p>'.sprintf(__('You have selected %1$s interests so far','church-admin'),'<span class="selected">'.$selected.'</span>');

				// Make sure that there are not too many interests to sync

				if(!empty($_POST['save-interests']))
				{
					$saved_interests=array();

					foreach($groupings AS $title=>$content)
					{
						foreach($content as $key=>$name)
						{
							if(!empty($_POST[sanitize_title($title.'/'.$name)]))
							{
								$saved_interests[$title][]=$name;
							}
						}
					}

					update_option('mailchimp_interests',$saved_interests);
				}

				echo'<button id="select-all">'.__('Select/Deselect All','church-admin').'</button>';
				echo'<form action="" method="POST"><table class="form-table">';
				foreach($groupings AS $title=>$content)
				{
					echo'<tr><th colspacing=2 scope="row">'.esc_html($title).'</th><td>';
					foreach($content as $key=>$name)
					{
						echo'<input type="checkbox" class="interests" value="true" name="'.sanitize_title($title.'/'.$name).'" ';
						if(!empty($saved_interests[$title])&&in_array($name,$saved_interests[$title])) echo' checked="checked" ';
						echo'/>&nbsp; '.esc_html($name).'<br/>';
					}
					echo'</td></tr>';
				}
				echo'<tr><td colspacing=2><input type="hidden" name="save-interests" value="true"/><input type="submit" class="button-secondary" value="'.esc_html(__('Save','church-admin')).'"/></td></tr></table></form>';
			 echo' <script type="text/javascript">jQuery(document).ready(function($) {
			 	$("#select-all").on("click",function(){ var checkBoxes = $(".interests");
        			checkBoxes.prop("checked", !checkBoxes.prop("checked"));
        			updateCounter();});
			 	$(".interests").on("change",function(){updateCounter();console.log("checked");});
			 	function updateCounter() {
    				var len = $(".interests:checked").length;
					if(len>0){$(".selected").text(len);}else{$(".selected").text("0");}
				}
			 });

			 </script></div>';

			//only show sync button if interests have been selected
			if(!empty($saved_interests))
			{
				echo'<h3>'.__('Step 4 - Sync mailchimp','church-admin').'</h3><p><form action="" method="POST"><input type="hidden" name="sync" value="true"/><input class="button-primary" type="submit" value="'.__('Sync','church-admin').'"/></form></p>';
				echo'<p><strong>'.__('Screen will be blank while processing, please be patient','church-admin').'</p>';
			}


		}
		else
		{
		//begin sync
		if(!empty($settings['listID'])&&!empty($MailChimp))
		{
			echo'<h3>'.__('Beginning Mailchimp synchronization','church-admin');

				$groupings=$saved_interests;
				//Get current Groups from mailchimp
				$listID=$settings['listID'];
				$mailchimpGrps=$MailChimp->get("/lists/$listID/interest-categories",array('count'=>60));
				$mailchimpGroupings=$mailchimpGrps['categories'];

				$mailchimpGroups=array();//holder for current mailchimp interest categories
				$mailchimpInterests=array();//2D array for interests for each group
				foreach($mailchimpGroupings AS $MCGkey=>$mailchimpGrp)
				{
					$mailchimpGroups[$mailchimpGrp['title']]=$mailchimpGrp['id'];
				}
				//now $mailchimpGroups is array title=>mailchimp id for group

				//remove Groups not in $groupings array
				foreach($mailchimpGroups AS $title=>$id)
				{

					if(empty($groupings[$title]))
					{
						$req="/lists/$listID/interest-categories/$id";
						//echo $req.'<br/>';
						$MailChimp->delete($req);

						if (!$MailChimp->success()) {echo "Error\r\n".$MailChimp->getLastError();}
						unset($mailchimpGroups[$title]);
					}

				}
				//go through each ministry grouping

				foreach($groupings AS $groupingName=>$interests)
				{

					if(empty($mailchimpGroups[$groupingName]))
					{//create the interest grouping name on mailchimp
						$result=$MailChimp->post("/lists/$listID/interest-categories",array('title'=>$groupingName,'type'=>'hidden'));

						//print_r( $MailChimp->getLastRequest());;
						if (!$MailChimp->success()) {echo "Groupings \r\n".$MailChimp->getLastError().'<br/>';print_r( $MailChimp->getLastRequest());echo'<br>';}
						$id=$result['id'];
						$mailchimpGroups[$groupingName]=$id;
					}
					else
					{//find the id of the interest category
						$id=$mailchimpGroups[$groupingName];

					}
					/******************************
					*
					* Interests within Groupings
					*
					******************************/

					//check what is already stored in interests
					$mailchimpInts=$MailChimp->get("/lists/$listID/interest-categories/$id/interests",array('count'=>60));

					if(!empty($mailchimpInts))
					{
						foreach($mailchimpInts['interests'] AS $mailchimpInt)
						{
							if(!in_array($mailchimpInt['name'],$groupings[$groupingName]))
							{//not in array on church admin so delete
								$interest_id=$mailchimpInt['id'];
								$MailChimp->delete("/lists/$listID/interest-categories/$id/interests/$interest_id");
								if (!$MailChimp->success()) {echo "Line 255 - Error\r\n".$MailChimp->getLastError().'<br/>';print_r( $MailChimp->getLastRequest());echo'<br>';}
							}
							else
							{//build the array
								$mailchimpInterests[$groupingName][$mailchimpInt['name']]=$mailchimpInt['id'];
							}
						}
					}


					foreach($interests AS $key=>$value)
					{

						if(empty($mailchimpInterests[$groupingName][$value]))
						{
							$res=$MailChimp->post("/lists/$listID/interest-categories/$id/interests",array('name'=>$value));

							$mailchimpInterests[$groupingName][$value]=$res['id'];
							if (!$MailChimp->success()) {echo "Interests Error\r\n".$MailChimp->getLastError().'<br/>';}
						}



					}
					echo '<p>'.sprintf(__( '%1$s has been synced','church-admin'),$groupingName).'</p>';
					array_filter($mailchimpInterests[$groupingName]);
				}

				/******************************************************
				*
				* $mailchimpGroupings[$groupingName]=$id
				* $mailchimpInterests[$groupingName][$name]=$interestID
				*
				******************************************************/
        update_option('church_admin_MailChimpGroupings',$mailchimpGroupings);
        update_option('church_admin_MailChimpInterests',$mailchimpInterests);
				/******************************
				*
				* People
				*
				*******************************/
				$sql='SELECT * FROM '.CA_PEO_TBL.' WHERE email!="" AND email_send=1';
				$results=$wpdb->get_results($sql);
				if(!empty($results))
				{

					//build interests array for each person
					foreach($results AS $row)
					{
						$subscriber_hash=md5(strtolower($row->email));
						$interests=array();

            //gender
            if(!empty($mailchimpInterests[__('Gender','church-admin')]))
            {
						        foreach($mailchimpInterests[__('Gender','church-admin')] AS $sex=>$ID)
                    {
                        if($sex==$gender[$row->sex]){$interests[$ID]=TRUE;}else{$interests[$ID]=FALSE;}
						        }
            }
						//people_type
            if(!empty($mailchimpInterests[__('People Type','church-admin')]))
            {
              foreach($mailchimpInterests[__('People Type','church-admin')] AS $people_type=>$ID)
						        {
                      if($people_type==$people_types[$row->people_type_id]){$interests[$ID]=TRUE;}else{$interests[$ID]=FALSE;}
						        }
            }
						//sites
						if(empty($row->site_id))$row->site_id=1;
            if(!empty($mailchimpInterests[__('Sites','church-admin')]))
            {
						        foreach($mailchimpInterests[__('Sites','church-admin')] AS $venue=>$ID)
						        {
                      if($venue==$sites[$row->site_id]){$interests[$ID]=TRUE;}else{$interests[$ID]=FALSE;}
						        }
            }
						//member_type
            if(!empty($mailchimpInterests[__('Sites','church-admin')]))
            {				foreach($mailchimpInterests[__('Member Types','church-admin')] AS $member_type=>$ID)
						        {
							               if($member_type==$member_types[$row->member_type_id]){$interests[$ID]=TRUE;}else{$interests[$ID]=FALSE;}
						        }
            }
						//smallgroups
						$personsGroupsIDs=church_admin_get_people_meta($row->people_id,'smallgroup');

						$personsGroups=array();
						if(is_array($personsGroupsIDs))
						{
							foreach($personsGroupsIDs AS $key=>$group_id)
							{
								$personsGroups[]=$small_groups[$group_id];
							}
						}
            if(!empty($mailchimpInterests[__('Small group','church-admin')]))
            {
						        foreach($mailchimpInterests[__('Small group','church-admin')] AS $group=>$ID)
						        {
							          if(is_array($personsGroups) && in_array($group,$personsGroups)){$interests[$ID]=TRUE;}else{$interests[$ID]=FALSE;}
                    }
						}
						//ministries
						$personsMinsIDs=church_admin_get_people_meta($row->people_id,'ministry');
						$personsMins=array();
						if(is_array($personsMinsIDs))
						{
							foreach($personsMinsIDs AS $key=>$ministry_id)
							{
								$personsMins[]=$ministries[$ministry_id];
							}
						}
            if(!empty($mailchimpInterests[__('Small group','church-admin')]))
            {
						        foreach($mailchimpInterests[__('Small group','church-admin')] AS $ministry=>$ID)
						        {
							          if(is_array($personsMins) && in_array($ministry,$personsMins)){$interests[$ID]=TRUE;}else{$interests[$ID]=FALSE;}
                    }
						}
            //Parents
            $personsKidsGroupsIDs=church_admin_get_kids_groups($row->people_id);
            $personsKidsGroups=array();
            if(is_array($personsKidsGroupsIDs))
            {
              foreach($personsKidsGroupsIDs AS $key=>$group_id)
              {
                $personsKidsGroups[]=$kidswork[$group_id];
              }

            }
            if(!empty($mailchimpInterests[__('Parents','church-admin')]))
            {
						        foreach($mailchimpInterests[__('Parents','church-admin')] AS $group=>$ID)
						        {
							          if(is_array($personsKidsGroups) && in_array($group,$personsKidsGroups)){$interests[$ID]=TRUE;}else{$interests[$ID]=FALSE;}
                    }
						}


						$args=array('email_address'=>$row->email,'interests'=>$interests,'merge_fields'=>array('FNAME'=>esc_html($row->first_name),'LNAME'=>esc_html($row->last_name)));


						//check if email is in list, use status_if_new if not and status if it is
						$status=$MailChimp->get("/lists/$listID}/members/$subscriber_hash");
						if(!empty($status) && $status['status']=="subscribed")
						{
							$args['status']="subscribed";
						}
						else
						{
							$args['status_if_new']='subscribed';
						}

						if(!empty($debug))
						{
							church_admin_debug(print_r($mailchimpInterests,'true'));
							church_admin_debug(print_r($args,'true'));
						}

						$MailChimp->put("/lists/$listID/members/$subscriber_hash",$args);
						if (!$MailChimp->success())
						{

							echo "Subscriber Error\r\n".$MailChimp->getLastError();
							print_r( $MailChimp->getLastRequest());;
							echo'<br/>';

						}

					}
					}

					echo'<p>'.__('Address lists synced with mailchimp','church-admin').'</p>';

					/********************************************************
					*
					*
					*  Add new people who sere subscribed within Mailchimp
					*
					*
					*********************************************************/
					$mailchimpSubscribed=$MailChimp->get("/lists/$listID/members",array('status'=>'subscribed','count'=>3000));
					if(!empty($mailchimpSubscribed))
					{
					echo'<h2>'.__('Possible duplicates','church-admin').'</h2>';
						$count=0;
						foreach($mailchimpSubscribed['members'] AS $mailchimpSubscriber)
						{
							$first_name=$mailchimpSubscriber['merge_fields']['FNAME'];
							$last_name=$mailchimpSubscriber['merge_fields']['LNAME'];
							$email=$mailchimpSubscriber['email_address'];
							$hash=md5($email);
							//check for email in people table
							$sql='SELECT * FROM '.CA_PEO_TBL.' WHERE email="'.$email.'"';
							$people_id=$wpdb->get_row($sql);
							$s=$first_name.' '.$last_name;
							if(empty($people_id))
							{//email not already stored
								$sql='SELECT CONCAT_WS(" ",first_name,last_name) AS name,email,people_id FROM '.CA_PEO_TBL.' WHERE CONCAT_WS(" ",first_name,last_name) LIKE("%'.$s.'%")||CONCAT_WS(" ",first_name,prefix,last_name) LIKE("%'.$s.'%")||first_name LIKE("%'.$s.'%")||last_name LIKE("%'.$s.'%")||nickname LIKE("%'.$s.'%")||email LIKE("%'.$s.'%")||mobile LIKE("%'.$s.'%")';
    							$results=$wpdb->get_results($sql);
								if(!empty($results)&&!empty($first_name)&&!empty($last_name) &&!empty($email))
								{//in address list possible, so allow for merging
									echo '<div id="'.esc_html($hash).'"><p><strong>'.sprintf(__( '%1$s found on Mailchimp and may already be on the address list with a different email address (click contact to merge new email)','church-admin'),esc_html($first_name.' '.$last_name.' '.$email)).'...</strong></p><ul>';
									foreach($results AS $row)
									{
										if(!empty($row->email)&&!empty($row->name))
										{	echo '<li class="ca-merge" data-hash="'.esc_html($hash).'" data-id="'.intval($row->people_id).'" data-email="'.esc_html($email).'">'.esc_html($row->name.' '.$row->email).'</li>';
										}
									}
									echo'</ul>';
									echo'</div>';
								}

									echo'<p class="ca-add" data-id="'.intval($row->people_id).'" data-first-name="'.esc_html($first_name).'" data-last-name="'.esc_html($last_name).'" data-email="'.esc_html($email).'">'.__('Or add as a new contact','church-admin').'</p>';


							}
						}
						if($count==0) echo'<p>'.__('No new Mailchimp subscribers to sync with Church Admin plugin','church-admin').'</p>';

					}
				}//people results


			}//able to connect
		}//end sync

	else
	{
		echo'<p>'.__("You need a valid API key to connect to Mailchimp",'church-admin').'</p>';

	}
	echo'<script type="text/javascript">jQuery(document).ready(function($) {
			 	$(".click").on("click",function(){
					var toggleClass=$(this).attr("id");
					console.log(toggleClass);
					$("."+toggleClass).toggle();
				});
				$(".ca-merge").on("click",function(){
					var hash=$(this).data("hash");
					var email=$(this).data("email");
					var people_id=$(this).data("id");
					$.ajax({
            					url: ajaxurl,
            					type: "post",
            					data:  {"action": "church_admin","method":"mailchimp-merge","email":email,"people_id":people_id,security:"'.wp_create_nonce("mailchimp-merge").'"},
            					error: function() {
                					console.log("theres an error with AJAX");
            					},
            					success: function() {
                					console.log("Saved.");
                					$("#"+hash).hide();
            					}
        			});

				});
				$(".ca-add").on("click",function(){
					var email=$(this).data("email");
					var hash=$(this).data("hash");
					var first_name=$(this).data("first_name");
					var last_name=$(this).data("last_name");
					var people_id=$(this).data("id");
					$.ajax({
            					url: ajaxurl,
            					type: "post",
            					data:  {"action": "church_admin","method":"mailchimp-merge","email":email,"first_name":first_name,"last_name":last_name,security:"'.wp_create_nonce("mailchimp-add").'"},
            					error: function() {
                					console.log("theres an error with AJAX");
            					},
            					success: function() {
                					console.log("Saved.");
                					$("#"+hash).hide();
            					}
        			});

				});

				});
		</script>';
 }

function church_admin_mailchimp_merge()
{
	global $wpdb;
	church_admin_debug("*******************\r\nMailChimp Merge");
	$email=stripslashes($_POST['email']);
	$people_id=intval($_POST['people_id']);
	church_admin_debug($email.' '.$people_id);
	if(!empty($email)&&is_email($email) &&!empty($people_id))
	{
		$sql='UPDATE '.CA_PEO_TBL.' SET email="'.esc_sql($email).'" WHERE people_id="'.intval($people_id).'"';
		church_admin_debug($sql);
		$wpdb->query($sql);
	}
}

function church_admin_mailchimp_add()
{
	global $wpdb;
	church_admin_debug("*******************\r\nMailChimp Add");
	$email=stripslashes($_POST['email']);
	$first_name=stripslashes($_POST['first_name']);
	$last_name=stripslashes($_POST['last_name']);
	$sql='INSERT INTO '.CA_HOU_TBL.' ( address,lat,lng) VALUES("",52.0,0.0)';
	church_admin_debug($sql);
	$wpdb->query($sql);
	$household_id=$wpdb->insert_id;
	$sql='INSERT INTO '.CA_PEO_TBL.' (first_name,last_name,email,people_type_id,member_type_id,household_id)VALUES("'.esc_sql($first_name).'" , "'.esc_sql($last_name).'", "'.esc_sql($email).'"1,1,"'.intval($household_id).'")';
	church_admin_debug($sql);
	$wpdb->query($sql);

}
