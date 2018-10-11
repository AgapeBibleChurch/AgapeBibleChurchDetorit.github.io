<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
//Address Directory Functions
//2016-09-26 Added Nickname

function church_admin_view_person($people_id=NULL)
{

	global $wpdb;

	$data=$wpdb->get_row('SELECT *,first_name,middle_name,prefix,last_name FROM '.CA_PEO_TBL.' WHERE people_id="'.intval($people_id).'"');
	if(!empty($data))
	{
		if(!empty($data->attachment_id))
		{//photo available

			echo wp_get_attachment_image( $data->attachment_id,'ca-people-thumb',NULL,array('class'=>'alignleft') );

		}//photo available
		$name=$data->first_name.' ';
					$middle_name=get_option('church_admin_use_middle_name');
					if(!empty($middle_name)&&!empty($data->middle_name))$name.=$data->middle_name.' ';
					$nickname=get_option('church_admin_use_nickname');
					if(!empty($nickname)&&!empty($data->nickname))$name.=' ('.$data->nickname.') ';
					$prefix=get_option('church_admin_use_prefix');
					if(!empty($prefix)&&!empty($data->prefix))		$name.=$data->prefix.' ';
					$name.=$data->last_name;
		echo'<h2><a href="'.wp_nonce_url('admin.php?page=church_admin/index.php&amp;action=edit_people&amp;people_id='.intval($people_id),'edit_people').'">'.esc_html($name).'</a></h2><br style="clear:left"/>';
		echo'<h3>'.__('Contact Details','church-admin').'</h3>';
		echo'<table class="form-table">';
		if(!empty($data->mobile))echo'<tr><th scope="row">'.__('Mobile','church-admin').'</th><td><a href="call:'.esc_html($data->mobile).'">'.esc_html($data->mobile).'</a></td></tr>';
		if(!empty($data->email))echo'<tr><th scope="row">'.__('Email','church-admin').'</th><td><a href="call:'.esc_html($data->email).'">'.esc_html($data->email).'</a></td></tr>';
		if(!empty($data->twitter))echo'<tr><th scope="row">Twitter</th><td><a href="https://twitter.com/'.esc_html($data->twitter).'">@'.esc_html($data->twitter).'</a></td></tr>';
		if(!empty($data->facebook))echo'<tr><th scope="row">Facebook</th><td><a href="https://www.facebook.com/'.esc_html($data->facebook).'">'.esc_html($data->facebook).'</a></td></tr>';
		if(!empty($data->instagram))echo'<tr><th scope="row">Instagram</th><td><a href="https://www.instagram.com/'.esc_html($data->instagram).'">'.esc_html($data->instagram).'</a></td></tr>';
		echo'</table>';
		echo'<h3>'.__('Church Metadata','church-admin').'</h3>';
		echo'<table class="form-table">';
		//site
		if(!empty($data->site_id))$site_details=$wpdb->get_var('SELECT venue FROM '.CA_SIT_TBL.' WHERE site_id="'.intval($data->site_id).'"');
		if(!empty($site_details))echo'<tr><th scope="row">'.__('Site attended','church-admin').'</th><td>'.esc_html($site_details).'</td></tr>';
		//small groups
		$groupIDs=church_admin_get_people_meta($people_id,'smallgroup');

		if(!empty($groupIDs))
		{
			foreach($groupIDs AS $groupID)	$group[]=$wpdb->get_var('SELECT group_name FROM '.CA_SMG_TBL.' WHERE id="'.intval($groupID).'"');
			if(!empty($group))echo'<tr><th scope="row">'.__('Small group','church-admin').'</th><td>'.esc_html(implode(", ",$group)).'</td></tr>';
		}
		//ministries
		$mins=array();//temp stor for person'sministries
		$ministries=church_admin_ministries();

		$person_ministries=$wpdb->get_results('SELECT ID FROM '.CA_MET_TBL.' WHERE people_id="'.esc_sql($people_id).'" AND meta_type="ministry"');

		if(!empty($person_ministries))
		{
			foreach($person_ministries AS $person_ministry)$mins[]=$ministries[$person_ministry->ID];
			echo'<tr><th scope="row">'.__('Ministries','church-admin').'</th><td>'.esc_html(implode(", ",$mins)).'</td></tr>';
		}
		//hope team
		$hopeteamjobs=array();
		$hts=$wpdb->get_results('SELECT job,hope_team_id FROM '.CA_HOP_TBL);
		if(!empty($hts))
		{
			foreach($hts AS $ht){$hopeteamjobs[$ht->hope_team_id]=$ht->job;}
		}
		$jobs=$wpdb->get_results('SELECT ID FROM '.CA_MET_TBL.' WHERE people_id="'.esc_sql($people_id).'" AND meta_type="hope_team"');
		$person_jobs=array();
		if(!empty($jobs))
		{
			foreach($jobs AS $job)$personjobs[]=$hopeteamjobs[$job->ID];
			echo'<tr><th scope="row">'.__('Hope Teams','church-admin').'</th><td>'.esc_html(implode(", ",$personjobs)).'</td></tr>';

		}
		echo'</table>';

		$others=$wpdb->get_results('SELECT *,CONCAT_WS(" ",first_name,prefix,last_name) AS name FROM '.CA_PEO_TBL.' WHERE household_id="'.intval($data->household_id).'" AND people_id!="'.intval($people_id).'" ORDER BY people_order ASC');
		if(!empty($others))
		{
			echo'<h3>'.__('Others in household','church-admin').'</h3>';
			foreach($others AS $other)
			{
				echo '<p><a href="'.wp_nonce_url('admin.php?page=church_admin/index.php&amp;action=edit_people&amp;people_id='.intval($other->people_id),'edit_people').'">'.esc_html($other->name).'</a></p>';
			}
		}
		//notes
		require_once(plugin_dir_path(dirname(__FILE__)).'includes/comments.php');
		if(!empty($people_id))church_admin_show_comments('people',	$people_id);

	}



}

function church_admin_address_list($member_type_id=0)
{
    if(!church_admin_level_check('Directory'))wp_die(__('You don\'t have permissions to do that','church-admin'));
    global $wpdb;
    $wpdb->query('UPDATE '.CA_PEO_TBL.' SET head_of_household=0 WHERE head_of_household=NULL');
	$member_type=church_admin_member_type_array();
	$member_type[0]=__('Complete','church-admin');



    //grab address list in order
	$sql='SELECT DISTINCT household_id FROM '.CA_PEO_TBL;
    if(!empty($member_type_id)){$sql.=' WHERE member_type_id="'.esc_sql($member_type_id).'"';}

    $result = $wpdb->get_var($sql);
    $items=$wpdb->num_rows;

    echo'<hr/><table class="form-table"><tbody><tr><th scope="row">'.__('Select different address list to view','church-admin').'</th><td><form name="address" action="admin.php?page=church_admin/index.php&amp;action=church_admin_address_list&tab=people" method="POST"><select name="member_type_id" >';
			    echo '<option value="0">'.__('All Member Type...','church-admin').'</option>';
			    foreach($member_type AS $key=>$value)
			    {
					$count=$wpdb->get_var('SELECT COUNT(people_id) FROM '.CA_PEO_TBL.' WHERE member_type_id="'.esc_sql($key).'"');
					echo '<option value="'.esc_html($key).'" >'.esc_html($value).' ('.$count.' people)</option>';
			    }
			    echo'</select><input type="submit" value="'.__('Go','church-admin').'"/></form></td></tr></tbody></table>';
    // number of total rows in the database
    require_once(plugin_dir_path(dirname(__FILE__)).'includes/pagination.class.php');
    if($items > 0)
    {

	$p = new caPagination;
	$p->items($items);
	$page_limit=get_option('church_admin_pagination_limit');
	if(empty($page_limit)){$page_limit=20;update_option('church_admin_pagination_limit',20);}
	$p->limit($page_limit); // Limit entries per page

	$p->target("admin.php?page=church_admin/index.php&tab=people&action=church_admin_address_list&tab=people&amp;member_type_id=".$member_type_id);
	if(!isset($p->paging))$p->paging=1;
	if(!isset($_GET[$p->paging]))$_GET[$p->paging]=1;
	$p->currentPage((int)$_GET[$p->paging]); // Gets and validates the current page
	$p->calculate(); // Calculates what to show
	$p->parameterName('paging');
	$p->adjacents(1); //No. of page away from the current page
	if(!isset($_GET['paging']))
	{
	    $p->page = 1;
	}
	else
	{
	    $p->page = intval($_GET['paging']);
	}
        //Query for limit paging
	$limit = esc_sql("LIMIT " . ($p->page - 1) * $p->limit  . ", " . $p->limit);


    //prepare WHERE clause using given Member_type_id
	$sort='last_name ASC';
	if(!empty($_GET['sort']))
	{
		switch($_GET['sort'])
		{
			case'date' :$sort='last_updated DESC';break;
			case'last_name':$sort='last_name ASC';break;
			default:$sort='last_name ASC';break;
		}
	}
    $sql='SELECT * FROM '.CA_PEO_TBL ;
    if(!empty($member_type_id))$sql.=' WHERE member_type_id="'.esc_sql($member_type_id).'"';
    $sql.=' GROUP BY household_id ORDER BY '.$sort.' '.$limit;

    $results=$wpdb->get_results($sql);

    if(!empty($results))
    {
		if(empty($member_type[$member_type_id]))$member_type[$member_type_id]=__('Whole','church-admin');
		echo '<h2>'.$member_type[$member_type_id].' '.__('address list','church-admin').'</h2>';
	 	echo'<p><span class="ca-private">'.__('Households not shown publicly','church-admin').' </span></p>';
		// Pagination
    	echo '<div class="tablenav"><div class="tablenav-pages">';
    	echo $p->show();
    	echo '</div></div>';
    	//Pagination
    	//grab address details and associate people and put in table
		echo '<table class="widefat striped"><thead><tr><th>'.__('Delete','church-admin').'</th><th><a href="admin.php?page=church_admin/index.php&action=church_admin_address_list&tab=people&member_type_id='.intval($member_type_id).'&sort=last_name">'.__('Last name','church-admin').'</a></th><th>'.__('First Name(s)','church-admin').'</th><th>'.__('Address','church-admin').'<a></th><th><a href="admin.php?page=church_admin/index.php&action=church_admin_address_list&tab=people&member_type_id='.intval($member_type_id).'&sort=date">'.__('Last Update','church-admin').'</th></tr></thead><tfoot><tr><th>'.__('Delete','church-admin').'</th><th>'.__('Last name','church-admin').'</th><th>'.__('First Name(s)','church-admin').'</th><th>'.__('Address','church-admin').'</th><th>'.__('Last Update','church-admin').'</th></tr></tfoot><tbody>';
		foreach($results AS $row)
		{
	    	$first=1;//in case head of household not set
		$firstPeopleID=0;//in case head of household not set
		$firstLastName='';//in case head of household not set
	    	//grab address
	    	$add_row=$wpdb->get_row('SELECT * FROM '.CA_HOU_TBL.' WHERE household_id="'.esc_sql($row->household_id).'"');

	     	//grab people
	    	$people_results=$wpdb->get_results('SELECT * FROM '.CA_PEO_TBL.' WHERE household_id="'.esc_sql($row->household_id).'" ORDER BY people_order,people_type_id ASC,sex DESC');
	    	$adults=$children=array();
	    	$prefix='';
			$private='';
			$head=0;

			$class=array();
			if(!empty($add_row->privacy))$class[]='ca-private';

	    	foreach($people_results AS $people)
	    	{
				//setting head of household recover variables if needed later...
				if($first==1){$firstPeopleID=$people->people_id;$firstLastName=$people->last_name;}
				$first++;
				if(empty($people->active))$class[]='ca-deactivated';
				if ($people->head_of_household==1)
				{
					$head=1;
					$last_name='';
					if(!empty($people->prefix))$last_name.=$people->prefix.' ';
					$last_name.=$people->last_name;
				}
				if(empty($last_name))$last_name=__('Add Surname','church-admin');
				if(empty($people->first_name))$people->first_name=__('Add Firstname','church-admin');
				if($people->people_type_id=='1'){$adults[]='<a href="'.wp_nonce_url('admin.php?page=church_admin/index.php&tab=people&action=edit_people&amp;household_id='.intval($row->household_id).'&amp;people_id='.intval($people->people_id),'edit_people').'">'.esc_html($people->first_name).'</a>';}else{$children[]='<a href="'.wp_nonce_url('admin.php?page=church_admin/index.php&amp;action=edit_people&tab=people&household_id='.intval($row->household_id).'&amp;people_id='.intval($people->people_id),'edit_people').'">'.esc_html($people->first_name).'</a>' ;}
				if(!empty($people->prefix)){$prefix=$people->prefix.' ';}
	    	}
	    	//check if there were anyone as head of household
	    	if($head==0)
	    	{

	    		//no head of household set so make first named person in household the head
	    		$wpdb->query('UPDATE '.CA_PEO_TBL.' SET head_of_household=1 WHERE people_id="'.intval($firstPeopleID).'"');
	    		$last_name=esc_html($firstLastName);
	    	}
	    	if(!empty($adults)){$adult=implode(" & ",$adults);}else{ $adult=__("Add Name",'church-admin');}
	    	if(!empty($children)){$kids=' ('.implode(", ",$children).')';}else{$kids='';}

	    	$delete='<a onclick="return confirm(\''.__('Are you sure?','church-admin').'\');" href="'.wp_nonce_url('admin.php?page=church_admin/index.php&tab=people&action=delete_household&household_id='.$row->household_id,'delete_household').'">'.__('Delete','church-admin').'</a>';
	    	if(empty($add_row->address))$add_row->address=__('Add Address','church-admin');
	    	if(!empty($class)){$classes=' class="'.implode(" ",$class).'"';}else$classes='';
	   		echo '<tr '.$classes.'><td>'.$delete.'</td><td><a  href="'.wp_nonce_url('admin.php?page=church_admin/index.php&amp;action=display_household&tab=people&household_id='.$row->household_id,'display_household').'">'.esc_html($last_name).'</a></td><td>'.$adult.' '.$kids.'</td><td>';
				//changed to direct edit link 2018-04-09
	   		echo '<a href="'.wp_nonce_url('admin.php?page=church_admin/index.php&amp;action=edit_household&amp;household_id='.$row->household_id,'edit_household').'">';
	   		echo esc_html($add_row->address).'</a></td><td>'.mysql2date('d/M/Y',$add_row->ts).'</td></tr>';
		}


		echo '</tbody></table>';
    	echo '<div class="tablenav"><div class="tablenav-pages">';
    	// Pagination
    	echo '<div class="tablenav"><div class="tablenav-pages">';
    	echo $p->show();
    	echo '</div></div>';
    	//Pagination

    }//end of items>0
    else{echo'<p>'.__('There are no people in that member type category','church-admin').'</p>';}
    }	else{echo'<p>'.__('There are no people in that member type category','church-admin').'</p>';}




}
 /**
 *
 * Edit Household
 *
 * @author  Andy Moyle
 * @param    $household_id
 * @return   html
 * @version  0.1
 *
 */
function church_admin_edit_household($household_id=NULL)
{

    global $wpdb,$church_admin_version;
	$member_type=church_admin_member_type_array();



    $member_type_id=$wpdb->get_var('SELECT member_type_id FROM '.CA_PEO_TBL.' WHERE household_id="'.esc_sql($household_id).'"  ORDER BY people_type_id ASC LIMIT 1');
    if(!empty($household_id)){$data=$wpdb->get_row('SELECT * FROM '.CA_HOU_TBL.' WHERE household_id="'.esc_sql($household_id).'"');}else{$data=new stdClass();}
    if(!empty($_POST['edit_household']))
    {//process form
		$private=NULL;
		if(!empty($_POST['private']))$private=1;
		$form=array();
		foreach ($_POST AS $key=>$value)$sql[$key]=esc_sql(sanitize_text_field(stripslashes($value)));
		if(!$household_id)$household_id=$wpdb->get_var('SELECT household_id FROM '.CA_HOU_TBL.' WHERE address="'.$sql['address'].'" AND lat="'.$sql['lat'].'" AND lng="'.$sql['lng'].'" AND phone="'.$sql['phone'].'"');
		if(!$household_id)
		{//insert
	    	$success=$wpdb->query('INSERT INTO '.CA_HOU_TBL.' (address,lat,lng,phone,privacy,attachment_id) VALUES("'.$sql['address'].'", "'.$sql['lat'].'","'.$sql['lng'].'","'.$sql['phone'].'","'.$private.'","'.$sql['household_attachment_id'].'" )');
	    $household_id=$wpdb->insert_id;
	}//end insert
	else
	{//update
	   $sql='UPDATE '.CA_HOU_TBL.' SET address="'.$sql['address'].'" , lat="'.$sql['lat'].'" , lng="'.$sql['lng'].'" , phone="'.$sql['phone'].'", privacy="'.$private.'" , attachment_id="'.$sql['household_attachment_id'].'" WHERE household_id="'.esc_sql($household_id).'"';
	   //echo $sql;
	   $success=$wpdb->query($sql);
	}//update
	if($success)
	{
	    echo '<div class="notice notice-success inline"><p><strong>'.__('Address saved','church-admin').' <br/><a href="./admin.php?page=church_admin/index.php&tab=people&action=church_admin_address_list&member_type_id='.$member_type_id.'">'.__('Back to Directory','church-admin').'</a></strong></td></tr></div>';
	}
	    echo'<div id="post-body" class="metabox-holder columns-2"><!-- meta box containers here -->';

		echo'<div class="notice notice-success inline"><p><strong>'.__('Household Edited','church-admin').' <br/>';
		if(church_admin_level_check('Directory')) echo'<a href="./admin.php?page=church_admin/index.php&tab=people&action=church_admin_address_list&member_type_id='.$member_type_id.'">'.__('Back to Directory','church-admin').'</a>';
		echo'</strong></td></tr></div>';

		church_admin_display_household($household_id);


    }//end process form
    else
    {//household form
	if(!empty($household_id)){$text='Edit ';}else{$text='Add ';}
	echo '<form action="" method="post">';
	//clean out old style address data
	if(!empty($data->address)&&is_array(maybe_unserialize($data->address)))
	{
		$data->address=implode(", ",array_filter(maybe_unserialize($data->address)));
	}
	echo church_admin_address_form($data,$error=NULL);
	//Phone
    echo '<table class="form-table"><tr><th scope="row">'.__('Phone','church-admin').'</th><td><input type="text" name="phone" ';
	if(!empty($data->phone)) echo ' value="'.esc_html($data->phone).'"';
    if(!empty($errors['phone']))echo' class="red" ';
    echo '/></td></tr>';
    if(empty($data->privacy))$data->privacy=0;
	echo'<tr><th scope="row">'.__('Private (not shown publicly)','church-admin').'</th><td><input type="checkbox" name="private" value="1" '.checked(1,$data->privacy,FALSE).'/></td></tr>';

	echo'<tr><td colspan="2"><input type="hidden" name="edit_household" value="yes"/><input class="button-primary" type="submit" value="'.__('Save Address','church-admin').'&raquo;" /></td></tr></table></form>';
    }//end household form


}

 /**
 *
 * Delete Household
 *
 * @author  Andy Moyle
 * @param    $household_id
 * @return   html
 * @version  0.1
 *
 */
function church_admin_delete_household($household_id=NULL)
{
    if(!church_admin_level_check('Directory'))wp_die(__('You don\'t have permissions to do that','church-admin'));
    //deletes household with specified household_id
    global $wpdb;
	delete_option('church-admin-directory-output');//get rid of cached directory, so it is updated

    //delete people meta data
    $people=$wpdb->get_results('SELECT * FROM '.CA_PEO_TBL.' WHERE household_id="'.esc_sql($household_id).'"');
    foreach($people AS $person){$member_type_id=$person->member_type_id;$wpdb->query('DELETE FROM '.CA_MET_TBL.' WHERE people_id="'.esc_sql($person->people_id).'"');}
    //delete from household and people tables
    $wpdb->query('DELETE FROM '.CA_HOU_TBL.' WHERE household_id="'.esc_sql($household_id).'"');
    $wpdb->query('DELETE FROM '.CA_PEO_TBL.' WHERE household_id="'.esc_sql($household_id).'"');
    echo'<div class="notice notice-success inline"><p><strong>'.__('Household Deleted','church-admin').'</strong></td></tr></div>';

}


 /**
 *
 * Edit a person
 *
 * @author  Andy Moyle
 * @param    $people_id,$household_id
 * @return
 * @version  0.2
 *
 * 0.11 added photo upload 2012-02-24
 * 0.2 added site_id, marital status 2016-05-12
 *
 */


function church_admin_edit_people($people_id=NULL,$household_id=NULL)
{


    global $wpdb,$people_type,$ministries,$current_user;
	$member_type=church_admin_member_type_array();
	$ministries=church_admin_ministries();
	$church_admin_marital_status=get_option('church_admin_marital_status');
	if(empty($church_admin_marital_status))
	{
		$church_admin_marital_status=array(0=>__('N/A','church-admin'),1=>__('Single','church-admin'),2=>__('Co-habiting','church-admin'),3=>__('Married','church-admin'),4=>__('Divorced','church-admin'),5=>__('Widowed','church-admin'));
		update_option('church_admin_marital_status',$church_admin_marital_status);
	}
  wp_get_current_user();


    echo'<h2>'.__('Edit Person','church-admin').'</h2>';
	$hopeteamjobs=array();
		$hts=$wpdb->get_results('SELECT job,hope_team_id FROM '.CA_HOP_TBL);
		if(!empty($hts))
		{

			foreach($hts AS $ht){$hopeteamjobs[$ht->hope_team_id]=$ht->job;}
		}


    if($people_id)$data=$wpdb->get_row('SELECT * FROM '.CA_PEO_TBL.' WHERE people_id="'.esc_sql($people_id).'"');
		if(empty($data)) $data = new stdClass();

    if(!empty($data->household_id))$household_id=$data->household_id;
    if(!empty($_POST['edit_people']))
    {//process


    	if(empty($household_id))
		{
			$wpdb->query('INSERT INTO '.CA_HOU_TBL.' (lat,lng) VALUES("52.000","0.000")');
			$household_id=$wpdb->insert_id;
		}



    	church_admin_save_person(1,$people_id,$household_id);


		//new small group
		if(!empty($_POST['group_name']))
		{
			$check=$wpdb->get_row('SELECT * FROM '.CA_SMG_TBL.' WHERE group_name="'.$sql['group_name'].'" AND whenwhere="'.$sql['when'].'" AND address="'.$sql['where'].'"');

			if(!empty($check))
			{//update
				$ldrs=esc_sql(serialize(array(1=>$people_id)));
				$query='UPDATE '.CA_SMG_TBL.' SET leadership="'.$ldrs.'",group_name="'.$sql['group_name'].'",whenwhere="'.$sql['when'].'" AND address="'.$sql['where'].'" WHERE id="'.esc_sql($check->id).'"';
				$wpdb->query($query);
				$sg_id=$check->id;
			}//end update
			else
			{//insert
				$leaders=esc_sql(maybe_serialize(array(1=>array(1=>$people_id))));
				$query='INSERT INTO  '.CA_SMG_TBL.' (group_name,leadership,whenwhere,address) VALUES("'.$sql['group_name'].'","'.$leaders.'","'.$sql['when'].'","'.$sql['where'].'")';
				$wpdb->query($query);
				$sg_id=$wpdb->insert_id;
			}//insert
			church_admin_update_people_meta($sg_id,$people_id,'smallgroup');
		}



		echo'<div class="notice notice-success inline"><p><strong>'.__('Person Edited','church-admin').' <br/>';
		if(church_admin_level_check('Directory') &&!empty($sql['member_type_id'])) echo'<a href="./admin.php?page=church_admin/index.php&amp;action=church_admin_address_list&tab=people&amp;member_type_id='.$sql['member_type_id'].'">'.__('Back to Directory','church-admin').'</a>';
		echo'</strong></td></tr></div>';
		echo'<form name="ca_search" action="admin.php?page=church_admin/index.php&tab=address" method="POST"><table class="form-table"><tbody><tr><th scope="row">'.__('Search','church-admin').'</th><td><input name="church_admin_search" style="width:100px;" type="text"/><input type="submit" value="'.__('Go','church-admin').'"/></td></tr></table></form>';
		church_admin_display_household($household_id);



    }//end process
    else
    {//form
		if($people_id)$data=$wpdb->get_row('SELECT * FROM '.CA_PEO_TBL.' WHERE people_id="'.esc_sql($people_id).'"');

		echo'<form action="" method="POST" enctype="multipart/form-data">';

		echo church_admin_edit_people_form(1,$data,NULL);
		if(!empty($data->user_id ))
		{
			echo'<table class="form-table"><tr><th scope="row">'.__('Wordpress User','church-admin').'</th><td><input type="hidden" name="ID" value="'.esc_html($data->user_id).'"/>';
			$user_info=get_userdata($data->user_id);
			if(!empty($user_info))
			{
				echo '<span class="username">'.__('Username','church-admin').': '.$user_info->user_login.'<span class="unattach_user"><span class="dashicons dashicons-no"></span></span><br/>'.__('User level','church-admin').': '.$user_info->roles['0'].'</span>';

			$nonce = wp_create_nonce("church_admin_unattach_user");
			echo'<script >jQuery(document).ready(function($) {
			$(".unattach_user").click(function() {
			var data = {
			"action": "church_admin",
			"method": "unattach_user",
			"people_id": '.intval($data->people_id).',
			"user_id": '.intval($data->user_id).',
			"nonce": "'.$nonce.'"
		};

		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post(ajaxurl, data, function(response) {console.log(response);
			$(".username").html("'.__('User disconnected - refresh page to reconnect to a user account','church-admin').'");
		});

			});
			});</script>';

			}
			echo'</td></tr></table>';
		}
		else
		{
			echo church_admin_username_form();

		}



		echo'<table class=form-table"><tr><th scope="row"><input type="hidden" name="edit_people" value="yes"/><input class="button-primary" type="submit" value="'.__('Save Details','church-admin').'&raquo;" /></td></tr></tbody></table></form>';
    }//form
    require_once(plugin_dir_path(dirname(__FILE__)).'includes/comments.php');
	if(!empty($people_id))church_admin_show_comments('people',	$people_id);

}
 /**
 *
 * Table row for username entry
 *
 * @author  Andy Moyle
 * @param
 * @return   html
 * @version  0.1
 *
 */
function church_admin_username_form()
{
	if(!church_admin_level_check('Directory'))wp_die(__('You don\'t have permissions to do that','church-admin'));
	global $wpdb;
			$sql='SELECT user_login,ID FROM '.$wpdb->prefix.'users WHERE `ID` NOT IN (SELECT user_id FROM '.CA_PEO_TBL.' WHERE user_id!=0)';
			$users=$wpdb->get_results($sql);
			$out='';
			if(!empty($users))
			{
					$out.='<tr><th scope="row">'.__('Choose a Wordpress account to associate','church-admin').'</th><td><select name="ID"><option value="">'.__('Select a user...','church-admin').'</option>';
					foreach($users AS $user) $out.='<option value="'.esc_html($user->ID).'">'.esc_html($user->user_login).'</option>';
					$out.='</select></td></tr>';
			}
			$out.='<tr><th scope="row">'.__('Or create a new Wordpress User','church-admin').'</th><td><input id="username" type="text" placeholder="'.__('Username','church-admin').'" name="username" value=""/><span id="user-result"></span></td></tr>'."\r\n";
			$nonce = wp_create_nonce("church_admin_username_check");
			$out.='<script >jQuery(document).ready(function($) {
			$("#username").change(function() {
			var username=$("#username").val();
			var data = {
			"action": "church_admin",
			"method":"username_check",
			"username": username,
			"nonce": "'.$nonce.'"
		};

		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post(ajaxurl, data, function(response) {console.log(response);
			$("#user-result").html(response);
		});

			});
			});</script>';
return $out;
}
 /**
 *
 * Delete People
 *
 * @author  Andy Moyle
 * @param    $people_id,$household_id
 * @return   html
 * @version  0.1
 *
 */
function church_admin_delete_people($people_id=NULL,$household_id)
{
    if(!church_admin_level_check('Directory'))wp_die(__('You don\'t have permissions to do that','church-admin'));
    delete_option('church-admin-directory-output');//get rid of cached directory, so it is updated
    //deletes person with specified people_id
    global $wpdb;
	$user_id=get_current_user_id();
	$message='';
	$data=$wpdb->get_row('SELECT * FROM '.CA_PEO_TBL.' WHERE people_id="'.esc_sql($people_id).'" ');
	if($data->user_id!=$user_id)
	{
		//delete user and log out from app
		wp_delete_user($data->user_id);
		$wpdb->query('DELETE FROM '.CA_APP_TBL.' WHERE user_id="'.intval($data->user_id).'"');
		$message.=__('User account deleted','church-admin').'<br/>';
	}
	if(!empty($data->head_of_household))
	{//need to reassign head of household
		$message.=  esc_html(sprintf(__( '%1$s was head of household','church-admin'),$data->first_name.' '.$data->last_name)).'<br/>';
		//look for another adult
		$next_person=$wpdb->get_row('SELECT * FROM '.CA_PEO_TBL.' WHERE household_id="'.intval($household_id).'" AND people_type_id=1 AND people_id!="'.intval($people_id).'" LIMIT 1');
		if(!empty($next_person))$message.=sprintf( esc_html__( 'Head of household reassigned to %1$s','church-admin'),$next_person->first_name.' '.$next_person->last_name).'<br/>';
		//no adult, find someone!
		if(empty($next_person->people_id))$next_person=$wpdb->get_row('SELECT * from '.CA_PEO_TBL.' WHERE household_id="'.intval($household_id).'"  AND people_id!="'.intval($people_id).'" AND people_type_id=1 LIMIT 1');
		if(!empty($next_person))$message.=esc_html(sprintf(__( 'Head of household reassigned to %1$s','church-admin'),$next_person->first_name.' '.$next_person->last_name)).'<br/>';else{$message='';}
		//set new head of hosuehold
		if(!empty($next_person->people_id))
		{
			$sql='UPDATE '.CA_PEO_TBL.' SET head_of_household=1 WHERE people_id="'.intval($next_person->people_id).'"';
			$wpdb->query($sql);
		}
	}
	//Delete from people table
    $wpdb->query('DELETE FROM '.CA_PEO_TBL.' WHERE people_id="'.esc_sql($people_id).'" ');
    //Delete from custom fields table.
	$wpdb->query('DELETE FROM '.CA_CUST_TBL.' WHERE people_id="'.esc_sql($people_id).'" ');

    $message.=sprintf( esc_html__( '%1$s has been deleted','church-admin'),$data->first_name.' '.$data->last_name).'<br/>';
    $count=$wpdb->get_var('SELECT COUNT(people_id) FROM '.CA_PEO_TBL.' WHERE household_id ="'.esc_sql($household_id).'" ');
    if(empty($count))
    {
    	$wpdb->query('DELETE FROM '.CA_PEO_TBL.' WHERE household_id="'.esc_sql($household_id).'" ');
    	$message=__('Household Deleted','church-admin').'<br/>';
    }
    $wpdb->query('DELETE FROM '.CA_MET_TBL.' WHERE people_id="'.esc_sql($people_id).'"');
    //delete from Mailchimp
    $settings=get_option('church_admin_mailchimp');
    if(!empty($settings))
    {
    /*
    	require_once(plugin_dir_path(dirname(__FILE__)).'includes/mailchimp.inc.php');
    	$MailChimp = new MailChimp($settings['api_key']);
		$MailChimp->verify_ssl = false;
		$list_id=$settings['listID'];
		$subriber_hash=md5(strtolower($data->email));
		$mailchimp->delete("/lists/$list_id/members/$subscriber_hash");
		$message.=__('Deleted from Mailchimp','church-admin');
    */
    }
    echo'<div class="notice notice-success inline"><p><strong>'.$message.'</strong></td></tr></div>';

	if(!empty($count)){church_admin_display_household($household_id);}else{church_admin_people_main();}


}
 /**
 *
 * Address form
 *
 * @author  Andy Moyle
 * @param    $data, $error
 * @return   html
 * @version  0.1
 *
 */
function church_admin_address_form($data,$error)
{
    //echos form contents where $data is object of address data and $error is array of errors if applicable
		$api=get_option('church_admin_google_api_key');
    if(empty($data))$data=(object)'';
    if(!empty($_GET['action'])&&$_GET['action']!="edit_site"){$out='<h3>'.__('Edit Household Details','church-admin').'</h3>';}else{$out='<h3>'.__('Edit Site Details','church-admin').'</h3>';}
    if(!empty($errors))$out.='<p>'.__('There were some errors marked in red','church-admin').'</p>';
		if(!empty($api))
		{
    	if(!empty($data->lat) && !empty($data->lat))
    	{//initial data for position already available
    		$out.='<script > var beginLat ='.esc_html($data->lat).';';
				$out.= 'var beginLng ='.esc_html($data->lng);
    		$out.=';</script>';
    	}else
    	{
         $out.='<script >';
    		$out.='
    				var beginLat = 51.50351129583287;var beginLng = -0.148193359375;if (navigator.geolocation) {var location_timeout = setTimeout("geolocFail()", 10000);navigator.geolocation.getCurrentPosition(function(position) {clearTimeout(location_timeout);beginLat = position.coords.latitude;beginLng = position.coords.longitude;}, function(error) {clearTimeout(location_timeout);});}</script>';

  		}
		}
   	/*************************************
	*
	*	Image
	*
	*************************************/

		$out.='<table class="form-table"><tr><th scope="row">'.__('Household Photo','church-admin').'</th><td>';
		if(!empty($data->attachment_id))
		{
			$out.=wp_get_attachment_image( $data->attachment_id,'ca-people-thumb','', array('class'=>"current-photo frontend-image",'id'=>"frontend-image"));

			$out.='<span id="frontend-image" class="remove-image button-secondary" data-attachment_id="'.intval($data->attachment_id).'" data-type="household" data-id="'.intval($data->household_id).'">'.__('Remove image','church-admin').'</span>';
		}
		else
		{
			$out.= '<img src="'.plugins_url('/images/default-avatar.jpg',dirname(__FILE__) ).'" width="75" height="75" class="frontend-image household-current-photo " alt="'.__('Photo of Person','church-admin').'"  />';
		}

		if(is_admin())
		{//on admin page so use media library
			$out.='<input id="houshold-image" type="button" class="household-upload-button button" value="'.__('Upload Image','church-admin').'" />';

		}else
		{//on front end so use boring update
			$out.='<input type="file" id="file-chooser" class="file-chooser" name="logo"/><input type="button" id="household_image" class="household-frontend-button" value="'.__('Upload Photo','church-admin').'" />';
    	}
	    $out.='<input type="hidden" name="household_attachment_id" class="attachment_id" id="household_attachment_id" ';
    	if(!empty($data->attachment_id))$out.=' value="'.intval($data->attachment_id).'" ';
    	$out.='/><span id="household-upload-message"></span><br style="clear:left"/>';
    	$out.='</td></tr>';


    $out.= '<tr><th scope="row">'.__('Address','church-admin').'</th><td><input style="width:100%" type="text" id="address" name="address" ';
    if(!empty($data->address)) $out.=' value="'.esc_html($data->address).'" ';
    if(!empty($error['address'])) $out.= ' class="red" ';
    $out.= '/></td><tr>';
    if(!empty($api))
		{
			if(!isset($data->lng))$data->lng='51.50351129583287';
    	if(!isset($data->lat))$data->lat='-0.148193359375';
    	$out.= '<tr><th scope="row"><a href="#" id="geocode_address" style="text-decoration:underline!important">'.__('Please click here to update map location, once you have entered an address','church-admin').'...</a></th><td><span id="finalise" ></span></p><input type="hidden" name="lat" id="lat" value="'.$data->lat.'"/><input type="hidden" name="lng" id="lng" value="'.$data->lng.'"/><div id="map" style="width:500px;height:300px;margin-bottom:20px"></div></td></tr></table>';
		}
   		if(is_admin())
   		{
   	$out.='<script >jQuery(document).ready(function($){


		 	//remove image
		 	$(".remove-image").click(function()
		 	{
		 			var type= $(this).data("type");
		 			var attachment_id=$(this).data("attachment_id");
		 			var id=$(this).data("id");

		 			var nonce="'.wp_create_nonce("remove-image").'";
		 			var data={"action":"church_admin","method":"remove-image","type":type,"attachment_id":attachment_id,"id":id,"nonce":nonce};
		 			console.log(data);
		 			$.ajax({
		 								url: ajaxurl,
		 								type: "POST",
		 								data: data,
		 								success: function(res) {
		 									console.log(res);
		 									$("#upload-message").html("'.__("Image Deleted","church-admin").'<br/>");
		 									$("#frontend-image").attr("src","'.plugins_url('/images/default-avatar.jpg',dirname(__FILE__) ).'");
		 									$("#frontend-image").attr("srcset","");
		 									$("#attachment_id").val("");
		 								},
		 								error: function(res) {
		 							$("#upload-message").html("Error deleting<br/>");
		 								}
		 						 });
		 	});

  var mediaUploader;

  $(".household-upload-button").click(function(e) {
    e.preventDefault();
    var id="#household_attachment_id";
    console.log(id);
    // If the uploader object has already been created, reopen the dialog
      if (mediaUploader) {
      mediaUploader.open();
      return;
    }
    // Extend the wp.media object
    mediaUploader = wp.media.frames.file_frame = wp.media({
      title: "Choose Image",
      button: {
      text: "Choose Image"
    }, multiple: false });

    // When a file is selected, grab the URL and set it as the text fields value
    mediaUploader.on("select", function() {
      var attachment = mediaUploader.state().get("selection").first().toJSON();
      console.log(attachment);
      $(id).val(attachment.id);
      console.log(attachment.sizes.thumbnail.url);
      $(".household-current-photo").attr("src",attachment.sizes.thumbnail.url);
      $(".household-current-photo").attr("srcset",null);
    });
    // Open the uploader dialog
    mediaUploader.open();
  });

});</script>';

    }else
    {
   $out.='<script>
	jQuery(document).ready(function($) {

	$( "body" ).on("click",".household-frontend-button", function( event ) {


	$("#household-frontend-image").attr("src","'.admin_url().'/images/wpspin_light-2x.gif");
	$("#household-frontend-image").attr("srcset","");
	var data = new FormData();
	jQuery.each(jQuery("#file-chooser")[0].files, function(i, file) {
    data.append("file-"+i, file);
	});
	$.ajax({
        		url: "'.admin_url().'admin-ajax.php?action=church_admin_image_upload",
        		type: "POST",
        		data: data,
        		processData: false,
        		contentType: false,
        		success: function(res) {
        		var image=JSON.parse(res);
        		console.log(image);

        			$("#household-upload-message").html("'.__("Success uploading","church-admin").'<br/>");
        			$("#household-frontend-image").attr("src",image.src);
        			$("#household-frontend-image").attr("srcset","");
        			$("#household_attachment_id").val(image.attachment_id);
        		},
        		error: function(res) {
					$("#upload-message").html("'.__("Error uploading, please try again","church-admin").'<br/>");
         		}
         });
    });

});
	</script>';

	}

    return $out;

}
 /**
 *
 * Display household
 *
 * @author  Andy Moyle
 * @param    $household_id
 * @return   html
 * @version  0.1
 *
 */
function church_admin_display_household($household_id)
{
    if(!church_admin_level_check('Directory'))wp_die(__('You don\'t have permissions to do that','church-admin'));
    global $wpdb,$people_type;
	$member_type=church_admin_member_type_array();
    $out='';
    $ministries=church_admin_ministries();
    $add_row=$wpdb->get_row('SELECT * FROM '.CA_HOU_TBL.' WHERE household_id="'.esc_sql($household_id).'"');
    if(empty($add_row))$add_row=new stdClass();
    if($add_row)
    {//address stored
		if(!empty($addrow->privacy)) echo'<p><span class="ca-private">'.__('This household is private and not shown publicly','church-admin').'</span></p>';


		echo'<table class="form-table"><tbody><tr><th scope="row">'.__('Select different address list to view','church-admin').'</th><td><form name="address" action="admin.php?page=church_admin/index.php&amp;action=church_admin_address_list&tab=people" method="POST"><select name="member_type_id" >';
		echo '<option value="0">'.__('All Member Type...','church-admin').'</option>';
		foreach($member_type AS $key=>$value)
		{
			$count=$wpdb->get_var('SELECT COUNT(people_id) FROM '.CA_PEO_TBL.' WHERE member_type_id="'.esc_sql($key).'"');
			echo '<option value="'.esc_html($key).'" >'.esc_html($value).' ('.$count.' people)</option>';
		}
		echo'</select><input type="submit" value="'.__('Go','church-admin').'"/></form></td></tr></tbody></table>';
		echo'<h2>'.__('Household Details','church-admin').'</h2>';

		//grab people
		$people=$wpdb->get_results('SELECT * FROM '.CA_PEO_TBL.' WHERE household_id="'.esc_sql($household_id).'" ORDER BY people_order ASC,people_type_id ASC,date_of_birth ASC,sex DESC');
		if($people)
		{//are people
	    	echo'<p><a class="button-primary" href="'.wp_nonce_url('admin.php?page=church_admin/index.php&amp;action=edit_people&amp;household_id='.$household_id,'edit_people').'">'.__('Add someone','church-admin').'</a></td></tr>';
			echo '<p>'.__('You can drag and drop to sort people display order (First person is head of household)','church-admin').'</td></tr>';
			if(church_admin_level_check('Directory'))
			{
				echo'<table id="sortable" class="widefat striped"><thead><tr><th>'.__('Edit','church-admin').'</th><th>'.__('Delete','church-admin').'</th><th>'.__('Picture','church-admin').'</th><th>'.__('Name','church-admin').'</th><th>'.__('Sex','church-admin').'</th><th>'.__('Date of Birth','church-admin').'</th><th>'.__('Person type','church-admin').'</th><th>'.__('Member Level','church-admin').'</th><th>'.__('Site','church-admin').'</th><th>'.__('Ministries','church-admin').'</th><th>'.__('Privacy Settings','church-admin').'</th><th>'.__('Email','church-admin').'</th><th>'.__('Mobile','church-admin').'</th><th>'.__('Move to different household','church-admin').'</th><th>'.__('WP user','church-admin').'</th></tr></thead><tfoot><tr><th>'.__('Edit','church-admin').'</th><th>'.__('Delete','church-admin').'</th><th>'.__('Picture','church-admin').'</th><th>'.__('Name','church-admin').'</th><th>'.__('Sex','church-admin').'</th><th>'.__('Date of Birth','church-admin').'</th><th>'.__('Person type','church-admin').'</th><th>'.__('Member Level','church-admin').'</th><th>'.__('Site','church-admin').'</th><th>'.__('Ministries','church-admin').'</th><th>'.__('Privacy Settings','church-admin').'</th><th>'.__('Email','church-admin').'</th><th>'.__('Mobile','church-admin').'</th><th>'.__('Move to different household','church-admin').'</th><th>'.__('WP user','church-admin').'</th></tr></tfoot><tbody  class="content">';
			}
			else
			{
				echo'<table id="sortable" class="widefat striped"><thead><tr><th>'.__('Edit','church-admin').'</th><th>'.__('Delete','church-admin').'</th><th>'.__('Picture','church-admin').'</th><th>'.__('Name','church-admin').'</th><th>'.__('Sex','church-admin').'</th><th>'.__('Date of Birth','church-admin').'</th><th>'.__('Person type','church-admin').'</th><th>'.__('Member Level','church-admin').'</th><th>'.__('Site','church-admin').'</th><th>'.__('Ministries','church-admin').'</th><th>'.__('Privacy Settings','church-admin').'</th><th>'.__('Email','church-admin').'</th><th>'.__('Mobile','church-admin').'</th></tr></thead><tfoot><tr><th>'.__('Edit','church-admin').'</th><th>'.__('Delete','church-admin').'</th><th>'.__('Picture','church-admin').'</th><th>'.__('Name','church-admin').'</th><th>'.__('Sex','church-admin').'</th><th>'.__('Person type','church-admin').'</th><th>'.__('Member Level','church-admin').'</th><th>'.__('Site','church-admin').'</th><th>'.__('Ministries','church-admin').'</th><th>'.__('Privacy Settings','church-admin').'</th><th>'.__('Email','church-admin').'</th><th>'.__('Mobile','church-admin').'</th></tr></tfoot><tbody  class="content">';

			}
	    	foreach ($people AS $person)
	    	{
				$gender=get_option('church_admin_gender');

				$sex=$gender[$person->sex];

				$site=$wpdb->get_var('SELECT venue FROM '.CA_SIT_TBL.' WHERE site_id="'.esc_sql($person->site_id).'"');
				//ministries
				$ministries=church_admin_ministries_array();
				$personMinistries=church_admin_get_people_meta($person->people_id,"ministry");


				$ministry=array();
				if(!empty($personMinistries)&&is_array($personMinistries)){foreach($personMinistries AS $key=>$min_id){$ministry[]=$ministries[$min_id];}}
				asort($ministry);
			//privacy
			$privacy='';
			$privacy=__('Email Send','church-admin').' - ';
			if($person->email_send){$privacy.=__('yes','church-admin');}else{$privacy.=__('no','church-admin');}
			$privacy.='<br/>'.__('SMS Send','church-admin').' - ';
			if($person->sms_send){$privacy.=__('yes','church-admin');}else{$privacy.=__('no','church-admin');}
			$privacy.='<br/>'.__('Mail Send','church-admin').' - ';
			if($person->mail_send){$privacy.=__('yes','church-admin');}else{$privacy.=__('no','church-admin');}

			$privacy.='<br/>'.__('Show in directory','church-admin').' - ';
			if(!$add_row->privacy){$privacy.=__('yes','church-admin');}else{$privacy.=__('no','church-admin');}
			if(!empty($person->gdpr_reason)){$privacy.='<br/>'.$person->gdpr_reason;}else{$privacy.='<br/><strong>'.__('Data protection reason required','church-admin').'</strong>';}
			if(!empty($person->email))
			{//user account only relevant for people with email
				if(!empty($person->user_id))
				{
					$user_info=get_userdata($person->user_id);
					if(!empty($user_info))$user=$user_info->user_login;
				}
				else
				{
					//check if a user exists for this email
					$user_id=email_exists($person->email);
					$unassigned_user=get_userdata($user_id);
					if(!empty($user_id))
					{
						$user='<span class="ca_connect_user" data-people_id="'.intval($person->people_id).'" data-user_id="'.intval($user_id).'">'.__('Connect','church-admin').' '.$unassigned_user->user_login.'</span>';

					}
					else
					{
						$user='<span class="ca_create_user" data-people_id="'.intval($person->people_id).'" >'.__('Create user account','church-admin').'</span>';

					}
				}
			}else{$user='&nbsp;';}

			if(!empty($person->attachment_id))
			{//photo available
		    	$photo= wp_get_attachment_image( $person->attachment_id,'ca-people-thumb' );

			}//photo available
			else
			{
		    	$photo= '<img src="'.plugins_url('images/default-avatar.jpg',dirname(__FILE__) ) .'" width="75" height="75"/>';
			}
			if(!empty($person->prefix)){$prefix=$person->prefix.' ';}else{$prefix='';}
			$useNickname=get_option('church_admin_use_nickname');
	    	if($useNickname&& !empty($person->nickname)){$nickname=' ('.$person->nickname.') ';}else{$nickname='';}
			echo'<tr class="sortable-row" id="'.esc_html($person->people_id).'"><td><a href="'.wp_nonce_url('admin.php?page=church_admin/index.php&amp;action=edit_people&amp;people_id='.$person->people_id.'&amp;household_id='.$household_id,'edit_people').'">'.__('Edit','church-admin').'</a></td>';
			echo'<td><a onclick="return confirm(\'Are you sure you want to delete '.esc_html($person->first_name).' '.esc_html($prefix).esc_html($person->last_name).'?\');" href="'.wp_nonce_url('admin.php?page=church_admin/index.php&amp;action=delete_people&amp;household_id='.$household_id.'&amp;people_id='.$person->people_id.'&amp;household_id='.$household_id,'delete_people').'">'.__('Delete','church-admin').'</a></td>';
			echo '<td>'.$photo.'</td><td><a href="'.wp_nonce_url('admin.php?page=church_admin/index.php&amp;action=view_person&amp;people_id='.$person->people_id,'view_person').'">'.esc_html($person->first_name).' '.esc_html($nickname).esc_html($prefix).esc_html($person->last_name).'</a></td>';
			echo '<td>'.$sex.'</td><td>';

			if($person->date_of_birth!="0000-00-00"){echo mysql2date(get_option('date_format'),$person->date_of_birth);}else{echo'&nbsp;';}
			echo'</td><td>'.$people_type[$person->people_type_id].'</td>';
			echo '<td>'.esc_html($member_type[$person->member_type_id]).'</td>';
			echo '<td>'.esc_html($site).'</td><td>'.implode(',<br/>',$ministry).'</td><td>'.$privacy.'</td><td>';
			if(is_email($person->email)){echo '<a href="'.esc_url('mailto:'.$person->email).'">'.esc_html($person->email).'</a>';}else{echo esc_html($person->email);}
			echo '</td><td>'.esc_html($person->mobile).'</td>';
			if(church_admin_level_check('Directory'))
			{//only Directory level users gets these columns!
				echo '<td><a href="'.wp_nonce_url('admin.php?page=church_admin/index.php&amp;action=church_admin_move_person&amp;people_id='.$person->people_id,'move_person').'">Move</a></td>';
				if(!empty($user)){echo'<td><div class="userinfo'.intval($person->people_id).'">'.$user.'</div></td>';}else{echo'<td>&nbsp;</td>';}

			}
			echo'</tr>';
	    	}
	    	echo'</tbody></table>';
			$connect_nonce = wp_create_nonce("connect_user");
			$create_nonce = wp_create_nonce("create_user");
		   echo '
    <script >

 jQuery(document).ready(function($) {

    var fixHelper = function(e,ui){
            ui.children().each(function() {
                $(this).width($(this).width());
            });
            return ui;
        };
    var sortable = $("#sortable tbody.content").sortable({
    helper: fixHelper,
    stop: function(event, ui) {
        //create an array with the new order


				var Order = "order="+$(this).sortable(\'toArray\').toString();



        $.ajax({
            url: "admin.php?page=church_admin/index.php&action=church_admin_update_order&which=people",
            type: "post",
            data:  Order,
            error: function() {
                console.log("theres an error with AJAX");
            },
            success: function() {
                console.log("Saved.");
            }
        });}
});
$("#sortable tbody.content").disableSelection();


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
   });
    </script>
';


		//address section
		echo'<h3><a href="'.wp_nonce_url('admin.php?page=church_admin/index.php&amp;action=edit_household&amp;household_id='.$household_id,'edit_household').'">'.__('Edit address, landline and/or photo','church-admin').'</a></h3>';
		if(!empty($add_row->address))
		{
			//old style <v0.554
			if(is_array(maybe_unserialize($add_row->address))) $address=implode(', ',array_filter(maybe_unserialize($add_row->address)));
			//>v0.553
			else{$address=$add_row->address;}
		}else{$address='Add Address';}
	 	echo'<script > var beginLat =';
    	if(!empty($data->lat)) {echo $data->lat.';';}else {echo '51.50351129583287;';}
			echo 'var beginLng =';
    	if(!empty($data->lng)) {echo $data->lng;}else {echo'-0.148193359375';}
    	echo';</script>';
		if(empty($add_row->lng)){$add_row->lng='-0.148193359375';}
		if(empty($add_row->lat)){$add_row->lat='51.50351129583287';}
		$key=get_option('church_admin_google_api_key');
		$staticMapUrl='http://maps.google.com/maps/api/staticmap?key='.$key.'&amp;center='.$add_row->lat.','.$add_row->lng.'&zoom=15&markers='.$add_row->lat.','.$add_row->lng.'&size=500x300';

		$status=church_admin_api_checker($staticMapUrl);
		if(empty($status)){$map=__('Google Maps API not working','church-admin');}
		elseif($status==403){$map=__('One of the parameters of your Google MAP API request is wrong','church-admin');}
		elseif($status==400){$map=__('Your Google Map API key is missing, wrong, or not enabled for Static maps','church-admin');}
		$map='<img src="'.$staticMapUrl.'" alt="'.$address.'" width=500 height=300/>';
		echo'<table class="form-table">';
		if(!empty($add_row->attachment_id))
		{
			echo'<tr><th scope="row">'.__('Family Photo','church-admin').'</th><td>';
			echo wp_get_attachment_image( $add_row->attachment_id,'medium' ).'</td></tr>';
		}
		if(!empty($add_row->phone))echo'<tr><th scope="row">'.__('Homephone','church-admin').' </th><td>'.esc_html($add_row->phone).'</td></tr>';
		echo '<tr><th scope="row">'.__('Address','church-admin').'</th><td> '.esc_html($address).'<br/>'.$map.'</td></tr></table>';

		require_once(plugin_dir_path(dirname(__FILE__)).'includes/comments.php');
		church_admin_show_comments('household',	$household_id);


	}//end are people
		else
		{//no people
	    	echo'<p>'.__('There are no people stored in that household yet','church-admin').'</td></tr>';
	    	echo'<p><a class="button-primary" href="'.wp_nonce_url('admin.php?page=church_admin/index.php&amp;action=church_admin_new_household','edit_people').'">'.__('Add new household','church-admin').'</a></td></tr>';
		}//no people
		//end grab people


    }//end address stored
    else
    {
		echo'<div class="notice notice-success inline"><p><strong>'.__('No Household found','church-admin').'</strong></td></tr></div>';
	}
}

function church_admin_migrate_users()
{
    global $wpdb;
    $results=$wpdb->get_results('SELECT ID FROM '.$wpdb->users);
    if($results)
    {
	foreach($results AS $row)
	{
	    $check=$wpdb->get_row('SELECT * FROM '.CA_PEO_TBL.' WHERE user_id="'.esc_sql($row->ID).'"');
	    if(!$check)
	    {
		$user_info=get_userdata($row->ID);
		$address='';
		$wpdb->query('INSERT INTO '.CA_HOU_TBL.'(member_type_id,address)VALUES("1","'.$address.'")');
		$household_id=$wpdb->insert_id;
		$wpdb->query('INSERT INTO '.CA_PEO_TBL.' (first_name,last_name,email,household_id,user_id,member_type_id,people_type_id,smallgroup_id,sex) VALUES("'.$user_info->first_name.'","'.$user_info->last_name.'","'.$user_info->user_email.'","'.$household_id.'","'.$row->ID.'","1","1","0","1")');
	    }
	}

	echo'<div class="notice notice-success inline"><p><strong>'.__('Wordpress Users migrated','church-admin').'</strong></td></tr></div>';
    }

    church_admin_address_list();
}
 /**
 *
 * Move person
 *
 * @author  Andy Moyle
 * @param    $people_id
 * @return   html
 * @version  0.1
 *
 */
function church_admin_move_person($people_id)
{
    global $wpdb;
        $data=$wpdb->get_row('SELECT * FROM '.CA_PEO_TBL.' WHERE people_id="'.esc_sql($people_id).'"');
    $message='';
    if(!empty($data))
    {

		if(!empty($_POST['move_person']))
		{
			//handle if person being moved is head of household
			if(!empty($data->head_of_household))
			{//need to reassign head of household
				$message.= sprintf( esc_html__( '%1$s was head of household','church-admin'),$data->first_name.' '.$data->last_name).'<br/>';
				//look for another adult
				$next_person=$wpdb->get_row('SELECT * FROM '.CA_PEO_TBL.' WHERE household_id="'.intval($data->household_id).'" AND people_type_id=1 AND people_id!="'.intval($people_id).'" LIMIT 1');
				if(!empty($next_person))$message.=sprintf( esc_html__( 'Head of household reassigned to %1$s','church-admin'),$next_person->first_name.' '.$next_person->last_name).'<br/>';
				//no adult, find someone!
				if(empty($next_person->people_id))$next_person=$wpdb->get_row('SELECT * from '.CA_PEO_TBL.' WHERE household_id="'.intval($data->household_id).'"  AND people_id!="'.intval($people_id).'" AND people_type_id=1 LIMIT 1');
				if(!empty($next_person))$message.=sprintf( esc_html__( 'Head of household reassigned to %1$s','church-admin'),$next_person->first_name.' '.$next_person->last_name).'<br/>';else{$message='';}
				//set new head of hosuehold
				if(!empty($next_person->people_id))
				{
					$sql='UPDATE '.CA_PEO_TBL.' SET head_of_household=1 WHERE people_id="'.intval($next_person->people_id).'"';
					$wpdb->query($sql);
				}
				//stop them being head of household!
				$wpdb->query('UPDATE '.CA_PEO_TBL.' SET head_of_household=0 WHERE people_id="'.esc_sql($people_id).'"');
			}

	    	if(!empty($_POST['create']))
			{
				$sql='INSERT INTO '.CA_HOU_TBL.' ( address,lat,lng,phone ) SELECT address,lat,lng,phone FROM '.CA_HOU_TBL.' WHERE household_id="'.esc_sql($data->household_id).'";';

				$wpdb->query($sql);
				$household_id=$wpdb->insert_id;
				$wpdb->query('UPDATE '.CA_PEO_TBL.' SET household_id="'.esc_sql($household_id).'" WHERE people_id="'.esc_sql($people_id).'"');
				$message.=sprintf( esc_html__( '%1$s has been moved to a new household with teh same address','church-admin'),$data->first_name.' '.$data->last_name);
				echo'<div class="notice notice-success inline"><p><strong>'.$message.'</strong></td></tr></div>';

			}
			else
			{
				//remove household entry if only one person was in it.
				$no=$wpdb->get_var('SELECT COUNT(people_id) FROM '.CA_PEO_TBL.' WHERE household_id="'.esc_sql($data->household_id).'"');
				if($no==1)$wpdb->query('DELETE FROM '.CA_HOU_TBL.' WHERE household_id="'.esc_sql($data->household_id).'"');
				//move the person to the new household
				$wpdb->query('UPDATE '.CA_PEO_TBL.' SET household_id="'.esc_sql($_POST['household_id']).'" WHERE people_id="'.esc_sql($people_id).'"');
				$message.=sprintf( esc_html__( '%1$s has been moved','church-admin'),$data->first_name.' '.$data->last_name);
				echo'<div class="notice notice-success inline"><p><strong>'.$message.'</strong></td></tr></div>';
				$household_id=(int)$_POST['household_id'];
			}
	    	church_admin_display_household($household_id);

		}
		else
		{
	   		echo'<div class="wrap"><h2>Move '.esc_html($data->first_name).' '.esc_html($data->last_name).'</h2>';

	    	$results=$wpdb->get_results('SELECT a.last_name,a.first_name, a.household_id,b.member_type FROM '.CA_PEO_TBL.' a, '.CA_MTY_TBL.' b WHERE b.member_type_id=a.member_type_id GROUP BY a.household_id,a.last_name ORDER BY a.last_name');
	    	if(!empty($results))
	    	{
				echo'<form action="" method="post">';
				echo'<tr><th scope="row">'.__('Create a new household with same address','church-admin').'</th><td><input type="checkbox" name="create" value="yes"/></td></tr>';
				echo'<tr><th scope="row">'.__('Move to household','church-admin').'</th><td><select name="household_id"><option value="">'.__('Select a new household...','church-admin').'</option>';
				foreach($results AS $row)
				{
		    		echo'<option value="'.esc_html($row->household_id).'">'.esc_html($row->last_name).', '.esc_html($row->first_name).' '.'('.$row->member_type.')</option>';
				}
				echo'</select></td></tr>';
				echo'<p><input type="hidden" name="move_person" value="yes"/><input type="submit" class="button-primary" value="'.__('Move person','church-admin').'"/></td></tr>';
				echo'</form></div>';
	    	}
		}
    }else{echo'<div class="notice notice-warning inline"><h2>'.__("Oh No! Couldn't find the person you want to move",'church-admin').'</h2></div>';}
}
 /**
 *
 * Create user for all people with email address
 *
 * @author  Andy Moyle
 * @param    $people_id
 * @return   html
 * @version  0.1
 *
 */
 function church_admin_users()
 {
 		global $wpdb;
 		echo'<h2>'.__('Create user accounts for every one with an email address','church-admin').'</h2>';
 		if(!empty($_POST['create_users']))
 		{
 			foreach($_POST['member_type_id'] AS $key=>$member_type_id)
 			{
 				$sql='SELECT CONCAT(first_name,last_name) AS username,people_id,household_id FROM '.CA_PEO_TBL.' WHERE member_type_id="'.intval($member_type_id).'"  AND user_id=0 AND email!=""';
				$results=$wpdb->get_results($sql);
				if(!empty($results))
				{
					foreach($results AS $row)echo church_admin_create_user($row->people_id,$row->household_id,$row->username);
				}

			}
			echo'<div class="notice notice-sucess inline"><h2>'.__('Users created','church-admin').'</h2</div>';
 		}
 		else
 		{
 			echo'<form action="" method="POST">';

 			$member_type=church_admin_member_type_array();
 			foreach($member_type AS $key=>$value)
			{
				echo'<p><input type="checkbox" name="member_type_id[]" value="'.esc_html($key).'" />'.esc_html($value).'</p>';

			}
			echo'<p><input type="hidden" name="create_users" value="yes"/><input type="submit" class="button-primary" value="'.__('Create users','church-admin').'"/></p></form>';
 		}

 }
function church_admin_confirmed_users()
{
	global $wpdb;
	$sql='SELECT CONCAT(first_name,last_name) AS username,people_id,household_id FROM '.CA_PEO_TBL.' WHERE (gdpr_reason IS NULL OR gdpr_reason="")  AND user_id=0 AND email!=""';
	$results=$wpdb->get_results($sql);
	if(!empty($results))
	{
		foreach($results AS $row)echo church_admin_create_user($row->people_id,$row->household_id,$row->username);
		echo'<div class="notice notice-sucess inline"><h2>'.__('Users created','church-admin').'</h2</div>';
	}
	else{echo'<div class="notice notice-sucess inline"><h2>'.__('No users created','church-admin').'</h2</div>';}

}
 /**
 *
 * Create user
 *
 * @author  Andy Moyle
 * @param    $people_id,$household_id,$username
 * @return   html
 * @version  0.1
 *
 */
function church_admin_create_user($people_id,$household_id,$username=NULL)
{
    global $wpdb;
		$wpdb->show_errors;
    $out='';
    if(!$people_id)
    {
			$out.="<p>'.__('Nobody was specified to create a wordpress account','church-admin').'</td></tr>";
    }
    else
    {//people_id

	$user=$wpdb->get_row('SELECT * FROM '.CA_PEO_TBL.' WHERE people_id="'.esc_sql($people_id).'"');
	if(empty($user))
	{
	    $out.='<div class="notice notice-success inline">'.__("That people record doesn't exist",'church-admin').'</td></tr></div>';
	}
	else
	{//user exits in plugin db
	    $user_id=email_exists($user->email);
	    if(!empty($user_id) && $user->user_id==$user_id)
	    {//wp user exists and is in plugin db
			$out.='<div class="notice notice-success inline">'.__('User already created','church-admin').'</td></tr></div>';

	    }
	    elseif(!empty($user_id) && $user->user_id!=$user_id)
	    {//wp user exists, update plugin
				$wpdb->query('UPDATE '.CA_PEO_TBL.' SET user_id="'.esc_sql($user_id).'" WHERE people_id="'.esc_sql($people_id).'"');
				$out.='<div class="notice notice-success inline">'.__('User updated','church-admin').'</td></tr></div>';

	    }
	    else
	    {//wp user needs creating!
				//create unique username
				if(empty($username))$username=strtolower(str_replace(' ','',$user->first_name).str_replace(' ','',$user->middle_name).str_replace(' ','',$user->last_name));
				$x='';
				while(username_exists( $username.$x ))
				{
		    	$x+=1;
				}
				$random_password = wp_generate_password( $length=12, $include_standard_special_chars=false );
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
				$out.='<div class="notice notice-success inline">'.__('User created with username','church-admin').' <strong>'.esc_html($username.$x).'</strong>,'.__('password','church-admin').': <strong>'.$random_password.'</strong> '.__('and this message was queued to them','church-admin').'<br/>'.esc_html($message);
					$headers=array();
					$headers[] = 'From: Web team at '.site_url() .'<'.get_option('admin_email').'>';
					$headers[] = 'Cc: Web team at '.site_url() .'<'.get_option('admin_email').'>';
					add_filter('wp_mail_content_type','church_admin_email_type');
					$subject=get_option('church_admin_user_created_email_subject');

					if(empty($subject))$subject='Login for '.site_url();
					if(wp_mail($user->email,$subject,$message,$headers))
					{
		    		$out.='<strong>'.__('User creation email sent successfully','church-admin').'</strong></div>';
					}
					else
					{
		    	$out.='<strong>'.__('User creation email NOT sent successfully','church-admin').'</strong></div>';
				}
				remove_filter('wp_mail_content_type','church_admin_email_type');
				$wpdb->query('UPDATE '.CA_PEO_TBL.' SET user_id="'.esc_sql($user_id).'" WHERE people_id="'.esc_sql($people_id).'"');

	    }//wp user needs creating!



	}//user exits in plugin db


    }//people_id

    return $out;
}//function church_admin_create_user





function church_admin_get_capabilities($id)
{
    if(empty($id))return FALSE;
    $user_info=get_userdata($id);
    if(empty($user_info))return FALSE;
    $cap=$user_info->roles;

	if (in_array('subscriber',$cap))return 'Subscriber';
	if (in_array('author',$cap))return 'Author';
	if (in_array('editor',$cap))return  'Editor';
	if (in_array('administrator',$cap)) return 'Administrator';
	return FALSE;
}
 /**
 *
 * Search
 *
 * @author  Andy Moyle
 * @param
 * @return   html
 * @version  0.1
 *
 */
function church_admin_search($search)
{
    global $wpdb,$rota_order;
    $wpdb->show_errors();
    echo '<p><a class="button-primary" href="'.wp_nonce_url('admin.php?page=church_admin/index.php&amp;tab=address&action=church_admin_new_household','new_household').'">'.__('Add a Household','church-admin').'</a> </p>';
	echo'<form name="ca_search" action="admin.php?page=church_admin/index.php&tab=address" method="POST"><table class="form-table"><tbody><tr><th scope="row">'.__('Search','church-admin').'</th><td><input name="church_admin_search" style="width:200px;" type="text"/><input type="submit" value="'.__('Go','church-admin').'"/></td></tr></table></form>';
    $s=esc_sql(stripslashes($search));
    //try searching first name, last name, email, mobile separately
	$sql='SELECT DISTINCT household_id FROM '.CA_PEO_TBL.' WHERE CONCAT_WS(" ",first_name,last_name) LIKE("%'.$s.'%")||CONCAT_WS(" ",first_name,prefix,last_name) LIKE("%'.$s.'%")||first_name LIKE("%'.$s.'%")||last_name LIKE("%'.$s.'%")||nickname LIKE("%'.$s.'%")||email LIKE("%'.$s.'%")||mobile LIKE("%'.$s.'%")';
    $results=$wpdb->get_results($sql);
    if(!$results)
    {//try address
		$sql='SELECT DISTINCT household_id FROM '.CA_HOU_TBL.' WHERE address LIKE("%'.$s.'%")||phone LIKE("%'.$s.'%")';
		$results=$wpdb->get_results($sql);
    }

    if($results)
    {

	    echo '<h2>'.__('Address List Results','church-admin').' for "'.esc_html($search).'"</h2><table class="widefat striped"><thead><tr><th>'.__('Delete','church-admin').'</th><th>'.__('Last name','church-admin').'</th><th>'.__('First Name(s)','church-admin').'</th><th>'.__('Address','church-admin').'</th><th>'.__('Last Update','church-admin').'</th></tr></thead><tfoot><tr><th>'.__('Delete','church-admin').'</th><th>'.__('Last name','church-admin').'</th><th>'.__('First Name(s)','church-admin').'</th><th>'.__('Address','church-admin').'</th><th>'.__('Last Update','church-admin').'</th></tr></tfoot><tbody>';
		foreach($results AS $row)
		{

	    //grab address
	    $add_row=$wpdb->get_row('SELECT * FROM '.CA_HOU_TBL.' WHERE household_id="'.esc_sql($row->household_id).'"');
	     //grab people
	    $people_results=$wpdb->get_results('SELECT first_name,middle_name,nickname,last_name,people_type_id,people_id FROM '.CA_PEO_TBL.' WHERE household_id="'.esc_sql($row->household_id).'" ORDER BY people_type_id ASC,sex DESC');
	    $adults=$children=$people_ids=array();
	    foreach($people_results AS $people)
	    {
	    	$people_ids[]=$people->people_id;
	    	$useNickname=get_option('church_admin_use_nickname');
	    	if($useNickname&&!empty($people->nickname)){$nickname='('.$people->nickname.')';}else{$nickname="";}
	    	$name=array_filter(array($people->first_name,$people->middle_name,$nickname));
		if($people->people_type_id=='1')
		{
			$last_name='';
			if(!empty($people->prefix))$last_name.=$people->prefix.' ';
			$last_name.=$people->last_name;
			$adults[]='<a href="'.wp_nonce_url('admin.php?page=church_admin/index.php&amp;action=edit_people&amp;household_id='.$row->household_id.'&amp;people_id='.$people->people_id,'edit_people').'">'.esc_html(implode(' ',$name)).'</a>';
		}
		else
		{
			$children[]='<a href="'.wp_nonce_url('admin.php?page=church_admin/index.php&amp;action=edit_people&amp;household_id='.$row->household_id.'&amp;people_id='.$people->people_id,'edit_people').'">'.esc_html(implode(' ',$name)).'</a>' ;}

	    }
	    $adult=implode(" & ",$adults);
	    if(!empty($children)){$kids=' ('.implode(", ",$children).')';}else{$kids='';}
	    $add='';
		if(!empty($add_row->address)){$add=esc_html($add_row->address);}else{$add='&nbsp;';}
	    if(!empty($add_row->ts)){$ts=$add_row->ts;}else{$ts=date('Y-m-d');}
	    if(!empty($add)){$address='<a href="'.wp_nonce_url('admin.php?page=church_admin/index.php&amp;action=edit_household&amp;household_id='.$row->household_id,'edit_household').'">'.esc_html($add).'</a>';}else{$address='<a href="'.wp_nonce_url('admin.php?page=church_admin/index.php&amp;action=edit_household&amp;household_id='.$row->household_id,'edit_household').'">Add Address</a>';}

	    $delete='<a onclick="return confirm(\''.__('Are you sure?','church-admin').'\');" href="'.wp_nonce_url('admin.php?page=church_admin/index.php&amp;action=delete_household&amp;household_id='.$row->household_id,'delete_household').'">'.__('Delete Household','church-admin').'</a>';
	    echo '<tr><td>'.$delete.'</td><td class="ca-names"><a href="'.wp_nonce_url('admin.php?page=church_admin/index.php&amp;action=display_household&amp;household_id='.$row->household_id,'display_household').'">'.$last_name.'</a></td><td class="ca-names">'.$adult.' '.$kids.'</td><td class="ca-addresses">'.$address.'</td><td>'.mysql2date('d/M/Y',$ts).'</td></tr>';


		}
		echo '</tbody></table>';



    }
	else{echo'<p>"'.esc_html($search).'" '.__('not found in directories','church-admin').'.</p>';}


	$people_id=church_admin_get_one_id($search);
	$serial='s:'.strlen($people_id).':"'.$people_id.'";';
	$serviceResults=$wpdb->get_results('SELECT * FROM '.CA_SER_TBL);
	if(!empty($serviceResults))
	{
		$services=array();
		foreach($serviceResults AS $serviceRow)$services[$serviceRow->service_id]=$serviceRow->service_name;
	}
	/**********************************
	*
	* Search rota
	*
	***********************************/

	foreach($people_ids AS $key=>$people_id)
	{
		$sql='SELECT a.service_name,a.service_time, b.rota_task,c.rota_date,a.service_id,c.people_id FROM '.CA_SER_TBL.' a, '.CA_RST_TBL.' b, '.CA_ROTA_TBL.' c WHERE a.service_id=c.service_id AND c.mtg_type="service" AND c.rota_task_id=b.rota_id  AND c.people_id="'.intval($people_id).'" AND c.rota_date>=CURDATE() ORDER BY c.rota_date ASC';

		$dateResults=$wpdb->get_results($sql);
		if(!empty($dateResults))
		{
			echo'<h2>'.__('Schedule results for ','church-admin').esc_html($search).'</h2>';
			echo'<table class="widefat striped">';
			$thead='<tr><th>'.__('Date','church-admin').'</th><th>'.__('Service','church-admin').'</th><th>'.__('Name','church-admin').'</th><th>'.__('Job','church-admin').'</th></tr>';
			echo'<thead>'.$thead.'</thead><tbody>';
			foreach($dateResults AS  $dateRow)
			{
					$edit_url=wp_nonce_url('admin.php?page=church_admin/index.php&tab=rota&action=edit_rota&rota_date='.esc_html($dateRow->rota_date).'&amp;service_id='.intval($dateRow->service_id).'&amp;mtg_type=service','edit_rota');
				echo'<tr><td><a href="'.$edit_url.'">'.mysql2date(get_option('date_format'),$dateRow->rota_date).'</a></td><td>'.esc_html($dateRow->service_name.' '.$dateRow->service_time).'</td><td class="ca-names">'.esc_html(church_admin_get_person($dateRow->people_id)).'</td><td>'.esc_html($dateRow->rota_task).'</td></tr>';

			}

				echo'</tbody><tfoot>'.$thead.'</tfoot></table>';
		}
	}

	//search podcast

	$results=$wpdb->get_results('SELECT * FROM '.CA_FIL_TBL.' WHERE file_title LIKE "%'.$s.'%" OR file_description LIKE "%'.$s.'%" OR speaker LIKE "%'.esc_sql($serial).'%" OR speaker LIKE "%'.$s.'%" ORDER BY pub_date DESC');
	if(!empty($results))
	{

		$upload_dir = wp_upload_dir();
		$path=$upload_dir['basedir'].'/sermons/';
		$url=content_url().'/uploads/sermons/';
		echo '<h2>'.__('Sermon Podcast Results for ','church-admin').'"'.esc_html($search).'"</h2>';
		$table='<table class="widefat striped"><thead><tr><th>'.__('Edit','church-admin').'</th><th>'.__('Delete','church-admin').'</th><th>'.__('Publ. Date','church-admin').'</th><th>'.__('Title','church-admin').'</th><th>'.__('Speakers','church-admin').'</th><th>'.__('Mp3 File','church-admin').'</th></th><th>'.__('File Okay?','church-admin').'</th><th>'.__('Length','church-admin').'</th><th>'.__('Media','church-admin').'</th><th>'.__('Transcript','church-admin').'</th><th>'.__('Event','church-admin').'</th><th>'.__('Shortcode','church-admin').'</th></tr></thead>'."\r\n".'<tfoot><tr><th>'.__('Edit','church-admin').'</th><th>'.__('Delete','church-admin').'</th><th>'.__('Publ. Date','church-admin').'</th><th>'.__('Title','church-admin').'</th><th>'.__('Speakers','church-admin').'</th><th>'.__('Mp3 File','church-admin').'</th></th><th>'.__('File Okay?','church-admin').'</th><th>'.__('Length','church-admin').'</th><th>'.__('Media','church-admin').'</th><th>'.__('Transcript','church-admin').'</th><th>'.__('Event','church-admin').'</th><th>'.__('Shortcode','church-admin').'</th></tr></tfoot>'."\r\n".'<tbody>';
        foreach($results AS $row)
        {
            if(file_exists(plugin_dir_path( $path.$row->file_name))){$okay='<img src="'.plugins_url('images/green.png',dirname(__FILE__) ) .'" width="32" height="32"/>';}else{$okay='<img src="'.plugins_url('images/red.png',dirname(__FILE__) ) .'" width="32" height="32"/>';}
            $edit='<a href="'.wp_nonce_url('admin.php?page=church_admin/index.php&amp;action=edit_file&amp;id='.$row->file_id,'edit_podcast_file').'">Edit</a>';
            $delete='<a onclick="return confirm(\''.__('Are you sure?','church-admin').'\');" href="'.wp_nonce_url('admin.php?page=church_admin/index.php&amp;action=delete_file&amp;id='.$row->file_id,'delete_podcast_file').'">'.__('Delete','church-admin').'</a>';
            $series_name=$wpdb->get_var('SELECT series_name FROM '.CA_SERM_TBL.' WHERE series_id="'.esc_sql($row->series_id).'"');
            if(!empty($row->file_name)&&file_exists($path.$row->file_name)){$file='<a href="'.$url.esc_url($row->file_name).'">'.esc_html($row->file_name).'</a>';$okay='<img src="'.plugins_url('images/green.png',dirname(__FILE__) ) .'"/>';}
			elseif(!empty($row->external_file)){$file='<a href="'.esc_url($row->external_file).'">'.esc_html($row->external_file).'</a>';$okay='<img src="'.plugins_url('images/green.png',dirname(__FILE__) ) .'"/>';}
			else{$file='&nbsp;';$okay='<img src="'.plugins_url('images/red.png',dirname(__FILE__) ).'"/>';}
            $table.='<tr><td>'.$edit.'</td><td>'.$delete.'</td><td>'.date(get_option('date_format'),strtotime($row->pub_date)).'</td><td>'.esc_html($row->file_title).'</td><td class="ca-names">'.esc_html(church_admin_get_people($row->speaker)).'</td><td>'.$file.'</td><td>'.$okay.'</td><td>'.esc_html($row->length).'</td><td>'.$row->video_url.'</td>';
            if(file_exists($path.$row->transcript)){$table.='<td><a href="'.esc_url($url.$row->transcript).'">'.esc_html($row->transcript).'</a></td>';}else{$table.='<td>&nbsp;</td>';}
            $table.='<td>'.esc_html($series_name).'</td><td>[church_admin type="podcast" file_id="'.intval($row->file_id).'"]</td></tr>';
        }

        $table.='</tbody></table>';
        echo $table;
	}
	//search calendar

}
 /**
 *
 * Replicate ministries in to roles.
 * The roles must already have been created in wordpress
 *
 * @author  Andy Moyle
 * @param    None
 * @return   N/A
 * @version  0.2
 *
 */

function church_admin_replicate_roles()
{
	if(!church_admin_level_check('Directory'))wp_die(__('You don\'t have permissions to do that','church-admin'));
	global $wpdb;
	$wpdb->show_errors;


	//Get an array of all the defined roles.
	$wp_roles = new WP_Roles();
	$names =$wp_roles->get_names();

	echo'<p>'.__('Starting to replicate roles','church-admin').'</p>';

	//Find all users in church admin that have a wordpress user ID.
	$sql='SELECT first_name,last_name,people_id,user_id FROM '.CA_PEO_TBL.' WHERE user_id>=1';

	if(defined('CA_DEBUG'))church_admin_debug('running sql '.$sql);

	$result=$wpdb->get_results($sql);
	if(defined('CA_DEBUG'))church_admin_debug('Results '.print_r($result,TRUE));
	if(!empty($result))
	{
		//We found some users with wordpress user ID's - iterate through them.
		foreach($result AS $users)
		{
			if(defined('CA_DEBUG'))church_admin_debug('Person Found with wordpress ID '.$users->user_id);

			//Now find the ministry ID's this user has.
			$sql='SELECT ID FROM '.CA_MET_TBL.' WHERE meta_type="ministry" AND people_id='.$users->people_id;
			if(defined('CA_DEBUG'))church_admin_debug('running sql '.$sql);
			$metaresult=$wpdb->get_results($sql);
			if(!empty($metaresult))
			{

				//User is in some ministries - iterate through them to get the names.
				foreach($metaresult AS $role)
				{
					if(defined('CA_DEBUG'))church_admin_debug('Role ID Found '.$role->ID);
					//For each ID we find get the actual name.
					$sql='SELECT ministry FROM '.CA_MIN_TBL.' WHERE ID='.$role->ID;
					if(defined('CA_DEBUG'))church_admin_debug('running sql '.$sql);
					$rolename=$wpdb->get_var($sql);
					if(defined('CA_DEBUG'))church_admin_debug('Role name is '.$rolename.' Adding role');

					//Iterate through the available roles to get the internal role name
					//as this is whats needed for the add role.
					$internalrolename='';
					$user=get_userdata($users->user_id);
					foreach($names as $key=>$ID)
					{
						if(defined('CA_DEBUG'))church_admin_debug('Role name is '.$key.' ID is ',$ID);

						if (strtolower($ID)==strtolower($rolename))
						{
							$internalrolename=$key;
							//Check if the user already has the role, user->roles is an array of all the roles the user has
							if (!in_array( $internalrolename, $user->roles )	)
							{
								//User does not have the role, so add it.
								echo'<br> Adding role '.$ID.' ('.$internalrolename.') to '.$users->first_name.' '.$users->last_name;
								$user->add_role($internalrolename);
							}
							else
								echo'<br>'.$users->first_name.' '.$users->last_name.' already has role '.$ID.' ('.$internalrolename.').';
							break;
						}
					}
					//We have iterated through all wordpress's known roles and have not found anything that matches.
					if(empty($internalrolename) )
					{
						echo'<br>Unable to add Role <b>('.$rolename.')</b> to user '.$users->first_name.' '.$users->last_name.'. The role was not found in wordpress - please add this manually if required.' ;
					}
				}
			}
		}
	}
}
 /**
 *
 * Import CSV
 *
 * @author  Andy Moyle
 * @param
 * @return   html
 * @version  0.1
 *
 */
function church_admin_import_csv()
{
		if(!church_admin_level_check('Directory'))wp_die(__('You don\'t have permissions to do that','church-admin'));
		global $wpdb;
		$wpdb->show_errors;
		$people_types=get_option('church_admin_people_type');
		$gender=get_option('church_admin_gender');
		$debug=TRUE;
		$church_admin_marital_status=get_option('church_admin_marital_status');
		if(empty($church_admin_marital_status))$church_admin_marital_status=array(0=>__('N/A','church-admin'),1=>__('Single','church-admin'),2=>__('Co-habiting','church-admin'),3=>__('Married','church-admin'),4=>__('Divorced','church-admin'),5=>__('Widowed','church-admin'));
	if(!empty($_POST['process'])&& check_admin_referer('csv_upload','nonce'))
	{
		echo'<p>'.__('Processing','church-admin').'</p>';
		if(!empty($_POST['overwrite']))
		{
			$wpdb->query('TRUNCATE TABLE '.CA_PEO_TBL);
			$wpdb->query('TRUNCATE TABLE '.CA_HOU_TBL);
			$wpdb->query('TRUNCATE TABLE '.CA_MET_TBL);
			$wpdb->query('TRUNCATE TABLE '.CA_CUST_TBL);
			delete_option('church_admin_custom_fields');
			update_option('church_admin_gender',array(1=>__('Male','church-admin'),0=>__('Female','church-admin')));
			echo'<p>'.__('Tables truncated','church-admin').'</p>';
		}

		foreach($_POST AS $key=>$value)
		{
			if(substr($key,0,6)=='column')
			{
				$column=substr($key,6);
				switch($value)
				{
					case'first_name':$first_name=$column;break;
					case'middle_name':$middle_name=$column;break;
					case'nickname':$nickname=$column;break;
					case'prefix':$prefix=$column;break;
					case'last_name':$last_name=$column;break;
					case'sex':$sex=$column;break;
					case'marital_status':$marital_status=$column;break;
					case'date_of_birth':$date_of_birth=$column;break;
					case'email':$email=$column;break;
					case'mobile':$mobile=$column;break;
					case'phone':$phone=$column;break;
					case'address':$address=$column;break;
					case'street_address':$street_address=$column;break;
					case'city':$city=$column;break;
					case'state':$state=$column;break;
					case'zip_code':$zipcode=$column;break;
					case'small_group':$small_group=$column;break;
					case'member_type':$member_type=$column;break;
					case'people_type':$people_type=$column;break;
					case'people_order':$people_order=$column;break;
					case'ministries':$ministries=$column;break;
					case 'custom1':$custom1=$column;break;
					case 'custom2':$custom2=$column;break;
					case 'custom3':$custom3=$column;break;
					case 'custom4':$custom4=$column;break;
					case 'custom5':$custom5=$column;break;
				}

			}

		}
		ini_set('auto_detect_line_endings',TRUE);
		if (($handle = fopen($_POST['path'], "r")) !== FALSE)
		{
			echo'<p>'.__('Begin file Processing','church-admin').'</p>';
			$header=fgetcsv($handle, '', ",");
			//handle custom headers
			$customFields=array();
			if(!empty($custom1)){$custom1Header=$header[$custom1];$customFields[1]=array('name'=>$custom1Header,'type'=>"text");}
			if(!empty($custom2)){$custom2Header=$header[$custom2];$customFields[2]=array('name'=>$custom2Header,'type'=>"text");}
			if(!empty($custom3)){$custom3Header=$header[$custom3];$customFields[3]=array('name'=>$custom3Header,'type'=>"text");}
			if(!empty($custom4)){$custom4Header=$header[$custom4];$customFields[4]=array('name'=>$custom4Header,'type'=>"text");}
			if(!empty($custom5)){$custom5Header=$header[$custom5];$customFields[5]=array('name'=>$custom5Header,'type'=>"text");}
			if(!empty($customFields))			update_option('church_admin_custom_fields',$customFields);
			echo'<p>'.__('Got CSV header','church-admin').'</p>';
			while (($data = fgetcsv($handle, 0, ",")) !== FALSE)
			{

				$head_of_household=1;//reset to 1 each time. Set to 0 if address already stored, which implies head already stored.
				//household
				$household_id=NULL;
				$add='';
				if(!empty($address)&&!empty($data[$address]))
				{
					$ad=array(sanitize_text_field($data[$address]));
					if(!empty($city)&&!empty($data[$city]))$ad[]=sanitize_text_field($data[$city]);
					if(!empty($state)&&!empty($data[$state]))$ad[]=sanitize_text_field($data[$state]);
					if(!empty($zipcode)&&!empty($data[$zipcode]))$ad[]=sanitize_text_field($data[$zipcode]);
					$add=implode(',',$ad);

				}

				if(!empty($phone)&&!empty($data[$phone])){$ph=sanitize_text_field($data[$phone]);}else{$ph=NULL;}
				//if the address is empty then don;t try to match with existing household
				if(!empty($address)&&!empty($data[$address]))
				{
					$sql='SELECT household_id FROM '.CA_HOU_TBL.' WHERE address="'.esc_sql($add).'" AND phone="'.esc_sql($ph).'"';
					$household_id=$wpdb->get_var($sql);
				}
				if(empty($household_id))
				{//insert
					$sql='INSERT INTO '.CA_HOU_TBL.' (address,phone)VALUES("'.esc_sql($add).'","'.esc_sql($ph).'")';

					$wpdb->query($sql);
					$household_id=$wpdb->insert_id;
				}else
				{
					$head_of_household=0;//person stored for that household already
				}
				//member type
				if(!empty($member_type))
				{
					$mt=sanitize_text_field($data[$member_type]);
					$member_type_id=$wpdb->get_var('SELECT member_type_id FROM '.CA_MTY_TBL.' WHERE member_type="'.esc_sql($mt).'"');
					if(empty($member_type_id))
					{
						$wpdb->query('INSERT INTO '.CA_MTY_TBL.' (member_type)VALUES("'.esc_sql($mt).'")');
						$member_type_id=$wpdb->insert_id;
					}
				}else
				{
					$member_type_id=1;
					$check=$wpdb->get_var('SELECT member_type_id FROM '.CA_MTY_TBL.' WHERE member_type_id=1' );
					if(!$check)
					{
						$wpdb->query('INSERT INTO '.CA_MTY_TBL.' (member_type)VALUES("'.__('Member','church-admin').'")');
						$member_type_id=$wpdb->insert_id;
					}
				}
				//people
				//gender

				if(defined('CA_DEBUG'))church_admin_debug('Gender array:'.print_r($gender,TRUE));
				if(defined('CA_DEBUG'))church_admin_debug('"'.$data[$sex].'"');
				if(isset($sex)&&!empty($data[$sex]))
				{
					$malefemale=array_search(trim($data[$sex]),$gender);
					if(!isset($malefemale))
					{
						$gender[]=sanitize_text_field($data[$sex]);
						update_option('church_admin_gender',$gender);
					}
					$malefemale=(int)array_search($data[$sex],$gender);
				}else $malefemale=1;

				if(isset($date_of_birth) && !empty($data[$date_of_birth]))
				{

					if(church_admin_checkdate($data[$date_of_birth])){$dob=$data[$date_of_birth];}
					else{$dob=date('Y-m-d',strtotime($data[$date_of_birth]));}

					if(empty($dob)) $dob='0000-00-00';
				}else{$dob='0000-00-00';}




				if (empty($marital_status)){$data['marital_status']=__('N/A','church-admin');}
				elseif(!in_array($data[$marital_status],$church_admin_marital_status)){$data['marital_status']=__('N/A','church-admin');}else{$data['marital_status']=$data[$marital_status];}
				if(empty($data['marital_status'])){$data['marital_status']=__('N/A','church-admin');}
				if(!isset($first_name)||empty($data[$first_name])){$data['first_name']=NULL;}else{$data['first_name']=$data[$first_name];}
				if(!isset($middle_name)||empty($data[$middle_name])){$data['middle_name']=NULL;}else{$data['middle_name']=$data[$middle_name];}
				if(!isset($nickname)||empty($data[$nickname])){$data['nickname']=NULL;}else{$data['nickname']=$data[$nickname];}
				if(!isset($prefix)||empty($data[$prefix])){$data['prefix']=NULL;}else{$data['prefix']=$data[$prefix];}
				if(!isset($last_name)||empty($data[$last_name])){$data['last_name']=NULL;}else{$data['last_name']=$data[$last_name];}
				if(!isset($mobile)||empty($data[$mobile])){$data['mobile']=NULL;}else{$data['mobile']=$data[$mobile];}
				if(!isset($email)||empty($data[$email])){$data['email']=NULL;}else{$data['email']=$data[$email];}
				if(!isset($custom1)||empty($data[$custom1])){$data['custom1']=NULL;}else{$data['custom1']=$data[$custom1];}
				if(!isset($custom2)||empty($data[$custom2])){$data['custom2']=NULL;}else{$data['custom2']=$data[$custom2];}
				if(!isset($custom3)||empty($data[$custom3])){$data['custom3']=NULL;}else{$data['custom3']=$data[$custom3];}
				if(!isset($custom4)||empty($data[$custom4])){$data['custom4']=NULL;}else{$data['custom4']=$data[$custom4];}
				if(!isset($custom5)||empty($data[$custom5])){$data['custom5']=NULL;}else{$data['custom5']=$data[$custom5];}
				if(!isset($people_order)||empty($data['people_order']))$data['people_order']=0;
				if(!isset($people_type)||empty($data[$people_type])){$data['people_type_id']=1;}
				else
				{
					foreach($people_types AS $id=>$type) if(strtolower($type)==strtolower($data[$people_type])){$data['people_type_id']=intval($id);}
				}


				$sql='INSERT INTO '.CA_PEO_TBL.' (first_name,middle_name,nickname,prefix,last_name,email,mobile,sex,date_of_birth,member_type_id,household_id,people_type_id,facebook,twitter,instagram,head_of_household,marital_status,people_order)VALUES("'.esc_sql(sanitize_text_field($data['first_name'])).'","'.esc_sql(sanitize_text_field($data['middle_name'])).'","'.esc_sql(sanitize_text_field($data['nickname'])).'","'.esc_sql(sanitize_text_field($data['prefix'])).'","'.esc_sql(sanitize_text_field($data['last_name'])).'","'.esc_sql(sanitize_text_field($data['email'])).'","'.esc_sql(sanitize_text_field($data['mobile'])).'","'.$malefemale.'","'.$dob.'","'.esc_sql($member_type_id).'","'.esc_sql($household_id).'","'.intval($data['people_type_id']).'","","","","'.$head_of_household.'","'.esc_sql($data['marital_status']).'","'.intval($data['people_order']).'")';
				if(defined('CA_DEBUG'))church_admin_debug($sql);
				$wpdb->query($sql);
				$people_id=$wpdb->insert_id;

				//Ministries

				if(isset($ministries) && !empty($data[$ministries]))
				{
					$temp = $data[$ministries];
					//echo '<br> ministries= '.$temp;
					$ministryarray=explode(":",$temp);
					foreach($ministryarray AS $key)
					{
						//echo '<br> ministry= '.$key;
						$sql='SELECT ID FROM '.CA_MIN_TBL.' WHERE ministry="'.$key.'"';
						//echo '<br> sql= '.$sql;
						$id=$wpdb->get_var($sql);
						if(!empty($id))
						{
							//echo '<br> ministry id for '.$key.' is '.$id;
							$sql='INSERT INTO '.CA_MET_TBL.' (people_id,ID,meta_type)VALUES("'.esc_sql($people_id).'","'.esc_sql($id).'","ministry")';
							$wpdb->query($sql);
						}

					}
				}
				if(isset($data['custom1']))
				{

					$wpdb->query('INSERT INTO '.CA_CUST_TBL.' (people_id,custom_id,data) VALUES("'.intval($people_id).'","1","'.esc_sql($data['custom1']).'")');
				}
				if(isset($data['custom2']))
				{

					$wpdb->query('INSERT INTO '.CA_CUST_TBL.' (people_id,custom_id,data) VALUES("'.intval($people_id).'","2","'.esc_sql($data['custom2']).'")');
				}
				if(isset($data['custom3']))
				{

					$wpdb->query('INSERT INTO '.CA_CUST_TBL.' (people_id,custom_id,data) VALUES("'.intval($people_id).'","3","'.esc_sql($data['custom3']).'")');
				}
				if(isset($data['custom4']))
				{


					$wpdb->query('INSERT INTO '.CA_CUST_TBL.' (people_id,custom_id,data) VALUES("'.intval($people_id).'","4","'.esc_sql($data['custom4']).'")');
				}
				if(isset($data['custom5']))
				{

					$wpdb->query('INSERT INTO '.CA_CUST_TBL.' (people_id,custom_id,data) VALUES("'.intval($people_id).'","5","'.esc_sql($data['custom5']).'")');
				}
				//look for custom_ in the array, these are user defined custom fields.
				if(defined('CA_DEBUG'))church_admin_debug('Data array:'.print_r($data,TRUE));
				//echo '<br> about to loop '.count($_POST);

				foreach($_POST as $field => $value)
				{
					//echo '<br> field= '.$field;
					$key=$value;
					$pos=strpos($key,'custom_');
					//echo '<br> pos= '.$pos;
					//echo '<br> key= '.$key;
					if ($pos!==false)
					{
					    //echo '<br> Found custom_ at '.$pos;

						$column=substr($field,6);
						//found a custom defined field, extract the custom id
						$cust_id=substr($key,strlen('custom_')+$pos);
						//echo '<br> cust id is'.$cust_id;
						//echo '<br> Data for row '.$column. 'is '.$data[$column];
						$sql='INSERT INTO '.CA_CUST_TBL.' (people_id,custom_id,data) VALUES("'.intval($people_id).'","'.intval($cust_id) .'","'.esc_sql($data[$column]).'")';
  					    $wpdb->query($sql);
					}
				}

				echo '<p>'.__('Added','church-admin').' '.sanitize_text_field($data[$first_name]).' '.sanitize_text_field($data[$last_name]).'</p>';


			}
			echo'<p>'.__('Finished file Processing','church-admin').'</p>';
		}
		fclose($handle);


	}
	elseif(!empty($_POST['save_csv']))
	{
		if(!empty($_FILES) && $_FILES['file']['error'] == 0)
		{
			$custom_fields=get_option('church_admin_custom_fields');
			$filename = $_FILES['file']['name'];
			$upload_dir = wp_upload_dir();
			$filedest = $upload_dir['path'] . '/' . $filename;
			if(move_uploaded_file($_FILES['file']['tmp_name'], $filedest))echo '<p>'.__('File Uploaded and saved','church-admin').'</p>';

			ini_set('auto_detect_line_endings',TRUE);
			$file_handle = fopen($filedest, "r");
			$header=fgetcsv($file_handle, '', ",");



			echo'<form  action="" method="post"><table >';
			echo'<input type="hidden" name="path" value="'.$filedest.'"/><input type="hidden" name="process" value="yes"/>';
			if(!empty($_POST['overwrite']))echo'<input type="hidden" name="overwrite" value="yes"/>';
			echo'<tr><th scope="row">'.__('Your Header','church-admin').'</th><th scope="row">'.__('Maps to','church-admin').'</th></tr>';
			foreach($header AS $key=>$value)
			{
				echo'<tr><th scope="row">'.esc_html($value).'</th><td>';
				echo'<select name="column'.$key.'">';
				echo'<option name="unused">'.__('Unused','church-admin').'</option>';
				echo'<option value="first_name">'.__('First Name','church-admin').'</option>';
				echo'<option value="middle_name">'.__('Middle Name','church-admin').'</option>';
				echo'<option value="nickname">'.__('Nickname','church-admin').'</option>';
				echo'<option value="prefix">'.__('Prefix','church-admin').'</option>';
				echo'<option value="last_name">'.__('Last Name','church-admin').'</option>';
				echo'<option value="sex">'.__('Gender','church-admin').'</option>';
				echo'<option value="marital_status">'.__('Marital Status','church-admin').'</option>';
				echo'<option value="date_of_birth">'.__('Date of Birth','church-admin').'</option>';
				echo'<option value="email">'.__('Email Address','church-admin').'</option>';
				echo'<option value="mobile">'.__('Mobile','church-admin').'</option>';
				echo'<option value="phone">'.__('Home phone','church-admin').'</option>';
				echo'<option value="address">'.__('Address','church-admin').'</option>';
				echo'<option value="city">'.__('City','church-admin').'</option>';
				echo'<option value="state">'.__('State','church-admin').'</option>';
				echo'<option value="zip_code">'.__('Zip Code','church-admin').'</option>';
				echo'<option value="small_group">'.__('Small Group','church-admin').'</option>';
				echo'<option value="member_type">'.__('Member Type','church-admin').'</option>';
				echo'<option value="ministries">'.__('Ministries','church-admin').'</option>';
				echo'<option value="people_type">'.__('People Type','church-admin').'</option>';
				echo'<option value="custom1">'.__('Custom field 1','church-admin').'</option>';
				echo'<option value="custom2">'.__('Custom field 2','church-admin').'</option>';
				echo'<option value="custom3">'.__('Custom field 3','church-admin').'</option>';
				echo'<option value="custom4">'.__('Custom field 4','church-admin').'</option>';
				echo'<option value="custom5">'.__('Custom field 5','church-admin').'</option>';
				foreach($custom_fields AS $ID=>$field)
				{
					echo'<option value=custom_'.$ID.'>'.__($field['name'].' (custom field)','church-admin').'</option>';

				}
				echo'</select>';
				echo'</td></tr>';
			}
			wp_nonce_field('csv_upload','nonce');
			echo'<tr><td colspan="2"><input type="submit" class="button" value="'.__('Save','church-admin').'"/></td></tr></table></form>';
		}
	}
	else
	{
		echo'<h2>'.__('Import csv - please save spreadsheet as a CSV file before uploading!','church-admin').'</h2>';
		echo'<p>'.__('Create a csv spreadsheet with each row as one person and a column header row. You can create a CSV file in your favourite Office software program and save as "Comma Separated Values". Please enclose column items in double quotes, especially if they have a comma! The columns can be and or all of first name, middle name, nickname, prefix, last name, gender, marital status, date of birth, email, cellphone, home phone, address ( as one column or split as address, city, state, postal code), small group name, member type and people type. You can also have up to 5 custom fields','church-admin').'</p>';
		echo'<p>'.__('One column can be ministries, please use : to separate the ministries. e.g.Pastor:Elder:Worship Leader','church-admin').'</p>';
		echo'<p>'.__('Date of birth works most reliably as yyyy-mm-dd e.g. 1970-03-08 for 8th March 1970','church-admin').'</p>';
		echo'<p>'.__('For people types, these values are recognised...','church-admin').implode(", ",$people_types).'</p>';
		echo'<p><a target="_blank" href="https://www.churchadminplugin.com/tutorials/import-address-list-csv/">'.__('Helpful tutorial','church-admin').'</a>';

		echo'<form action="" method="POST" enctype="multipart/form-data">';
		echo'<tr><th scope="row">'.__('CSV File with 1st row as headers','church-admin').'</th><td><input type="file" name="file"/><input type="hidden" name="save_csv" value="yes"/></p>';
		echo'<tr><th scope="row">'.__('Overwrite current address details?','church-admin').'</th><td><input type="checkbox" name="overwrite" value="yes"/></p>';
		echo'<p><input  class="button-primary" type="submit" Value="'.__('Upload','church-admin').'"/></p></form>';

	}
}
/**
 * add new household.
 *
 * @param
 * @param html display new household
 *
 * @author andy_moyle
 *
 */
function church_admin_new_household()
{
	$debug=TRUE;
//2016-04-14 Allow duplicate entries
//v1.05 add middle name

	global $wpdb,$people_type;
	$church_admin_marital_status=get_option('church_admin_marital_status');
	if(empty($church_admin_marital_status))
	{
		$church_admin_marital_status=array(0=>__('N/A','church-admin'),1=>__('Single','church-admin'),2=>__('Co-habiting','church-admin'),3=>__('Married','church-admin'),4=>__('Divorced','church-admin'),5=>__('Widowed','church-admin'));
	update_option('church_admin_marital_status',$church_admin_marital_status);
	}
	$member_type=church_admin_member_type_array();
	$people_type=get_option('church_admin_people_type');
	if(!empty($_POST['new_household']) && check_admin_referer('new-household','nonce'))
	{//process
			if(defined('CA_DEBUG'))church_admin_debug("POST \r\n".print_r($_POST,TRUE));
			$return=church_admin_save_household(FALSE,1,NULL,NULL);//$return is array('household_id','output')
        	if(!empty($return['output']))
        	{
        		echo $return['output'];
        		echo '<div class="notice notice-success"><p>'.__('Household Added','church-admin').'</p></div>';
        	}
			if(!empty($return['household_id']))church_admin_display_household($return['household_id']);

    }//end process
	else
	{
		echo '<div class="church_admin">';
		echo '<h2>'.__('Add new household','church-admin').'</h2>';
		echo'<form action="" method="post"><input type="hidden" name="save" value="yes"/>';
       	echo church_admin_edit_people_form(1,NULL,NULL);
       echo '<p id="jquerybuttons"><input class="button-primary" type="button" id="btnAdd" value="'.__('Add another person','church-admin').'" /> <input type="button"   class="button-secondary"  id="btnDel" value="'.__('Remove person','church-admin').'" /></p>';

         echo'<script >
         		jQuery("body").on("focus",".date_of_birth", function(){
    			jQuery(this).datepicker({dateFormat : "yy-mm-dd",altField:"#"+this(id), changeYear: true ,yearRange: "1910:'.date('Y').'"});
			});</script>';


        echo '<tr><th scope="row">'.__('Phone','church-admin').'</th><td><input name="phone" type="text"/></p>';
        echo church_admin_address_form(NULL,NULL);

		echo'<tr><th scope="row">'.__('Private (not shown publicly)','church-admin').'</th><td><input type="checkbox" name="private" value="1" /></p>';
        wp_nonce_field('new-household','nonce');
        echo'<input type="hidden" name="new_entry" value="yes"/>';
        echo  '<p><input type="hidden" name="new_household" value="TRUE"/><input  class="button-primary" type="submit" value="'.__('Save','church-admin').'"/></form>';
        echo'</div>';
    }//form


}


function church_admin_save_household($create_user=FALSE,$member_type_id=1,$exclude=NULL,$household_id)
{
	global $wpdb;
	$out='';
	delete_option('church-admin-directory-output');//get rid of cached directory, so it is updated
	$debug=FALSE;

			$form=$sql=array();
			foreach ($_POST AS $key=>$value)$form[$key]=stripslashes_deep($value);
			if(defined('CA_DEBUG'))church_admin_debug("*************".date('Y-m-d h:i:s')."\r\n"."Save Household\r\n".print_r($form,TRUE));
			if(empty($form['address']))$household_id=NULL;
			if(empty($form['phone']))$form['phone']=NULL;
			if(empty($household_id))$household_id=$wpdb->get_var('SELECT household_id FROM '.CA_HOU_TBL.' WHERE address="'.esc_sql(sanitize_text_field($form['address'])).'" AND lat="'.esc_sql(sanitize_text_field($form['lat'])).'" AND lng="'.esc_sql(sanitize_text_field($form['lng'])).'" AND phone="'.esc_sql(sanitize_text_field($form['phone'])).'"');



			if(empty($household_id)||!empty($_POST['new_entry']))
			{//insert
				$sql='INSERT INTO '.CA_HOU_TBL.' (address,lat,lng,phone) VALUES("'.esc_sql(sanitize_text_field($form['address'])).'", "'.esc_sql(sanitize_text_field($form['lat'])).'","'.esc_sql(sanitize_text_field($form['lng'])).'","'.esc_sql(sanitize_text_field($form['phone'])).'" )';
				if(defined('CA_DEBUG'))church_admin_debug("Inserted Household : $sql\r\n");

	    		$success=$wpdb->query($sql);
	    		$household_id=$wpdb->insert_id;
	    		if(defined('CA_DEBUG'))church_admin_debug("Inserted Household_id : $household_id \r\n");
			}//end insert
			else
			{//update
				$sql='UPDATE '.CA_HOU_TBL.' SET address="'.esc_sql(sanitize_text_field($form['address'])).'" , lat="'.esc_sql(sanitize_text_field($form['lat'])).'" , lng="'.esc_sql(sanitize_text_field($form['lng'])).'" , phone="'.esc_sql(sanitize_text_field($form['phone'])).'" WHERE household_id="'.esc_sql($household_id).'"';
				if(defined('CA_DEBUG'))church_admin_debug("Updated Household : $sql\r\n");
	   			$success=$wpdb->query($sql);


			}//update

			$sql=array();
			if(defined('CA_DEBUG'))church_admin_debug("household_id is :".$household_id);
      for($x=1;$x<=intval($_POST['fields']);$x++)
      {
				church_admin_save_person($x,NULL,$household_id,$exclude);

    	}//add or update people



        if(defined('CA_DEBUG'))church_admin_debug("Output :\r\n $out\r\n");

        return array('household_id'=>$household_id,'output'=> $out);
}


/**
 * Edit people form
 *
 * @param $x,$data
 *
 *
 * @author andy_moyle
 *
 */
function church_admin_edit_people_form($x=1,$data=null,$exclude=array())
{
	if(empty($exclude))$exclude=array();
	//initialise variables and arrays
	global $wpdb,$people_type,$current_user;

	$required=array();

	$church_admin_marital_status= get_option('church_admin_marital_status');

	$member_type=church_admin_member_type_array();
	$people_type=get_option('church_admin_people_type');

	//start $out
	$out='<input type="hidden" name="fields" id="fields" value="1"/>';
	//start cloned area
	$out.='<div class="clonedInput" id="input'.$x.'">';
	$out.='<div id="poststuff" class="postbox">';

    $out.='<h2 class="hndle" id="'.$x.'">'.__('Person','church-admin').' #<span class="person">'.$x.'</span> ';
		if(!empty($data->first_name)&&!empty($data->last_name))$out.= esc_html($data->first_name.' '.$data->last_name);
		$out.=' ('.__('Click to toggle','church-admin').')</h2>';
    $out.='<div class="inside" id="person'.$x.'"';
		if($x>1)$out.='style="display:none" ';
		$out.='>';
    $out.='<p>'.__('* required','church-admin').'</p>';
    $out.='<input type=hidden value="0" name="people_id[]" id="people_id[]"/>';
    $out.='<table class="form-table">';

     //first name
    $out.='<tr><th scope="row">'.__('First Name','church-admin');
   	$out.=' *';
    $out.='</th><td><input type="text" ';
    $out.='required="required" ';
    $out.='class="first_name" id="first_name'.intval($x).'" name="first_name1"';
    if(!empty($data->first_name)) $out.=' value="'.esc_html($data->first_name).'" ';
    $out.='/></td></tr>';

    //middle name
    $middle_name=get_option('church_admin_use_middle_name');
	if($middle_name ||!in_array('middle-name',$exclude))
	{
		$out.='<tr><th scope="row">'.__('Middle Name','church-admin');
		 if(in_array('middle_name',$required))$out.=' *';
		 $out.='</th><td><input type="text" ';
		  if(in_array('middle_name',$required))$out.='required="required" ';
		 $out.='id="middle_name'.intval($x).'" class="middle_name" name="middle_name'.intval($x).'" ';
		if(!empty($data->middle_name)) $out.=' value="'.esc_html($data->middle_name).'" ';
    	$out.='/></td></tr>';
    }

    //nickname

	$nickname=get_option('church_admin_use_nickname');
	if(in_array('nickname',$exclude))$nickname=FALSE;
	if(!empty($nickname))
	{
		$out.='<tr><th scope="row">'.__('Nickame','church-admin');
		 if(in_array('middle_name',$required))$out.=' *';
		$out.='</th><td><input type="text" class="nickname" ';
		 if(in_array('nickname',$required))$out.='required="required" ';
		$out.='id="nickname'.intval($x).'" name="nickname'.intval($x).'" ';
	 	if(!empty($data->nickname)) $out.=' value="'.esc_html($data->nickname).'" ';
    	$out.='/></td></tr>';
    }

    //prefix

	$use_prefix=get_option('church_admin_use_prefix');
	if(in_array('prefix',$exclude))$use_prefix=FALSE;
	if(!empty($use_prefix))
	{
		$out.='<tr><th scope="row">'.__('Prefix e.g. "van der"','church-admin');
		 if(in_array('prefix',$required))$out.=' *';
		$out.='</th><td><input type="text" class="prefix" ';
		if(in_array('nickname',$required))$out.='required="required" ';
		$out.='id="prefix'.intval($x).'" name="prefix'.intval($x).'" ';
		if(!empty($data->prefix)) $out.=' value="'.esc_html($data->prefix).'" ';
    	$out.='/></td></tr>';
	}

	//last name
	$out.='<tr><th scope="row">'.__('Last Name','church-admin');
	$out.=' *';
	$out.='</th><td><input type="text" required="required" class="last_name"';
	if(in_array('last_name',$required))$out.=' required="required" ';
	$out.='id="last_name'.intval($x).'" name="last_name'.intval($x).'" ';
	if(!empty($data->last_name)) $out.=' value="'.esc_html($data->last_name).'" ';
    $out.='/><input type="hidden" name="people_order" value+"'.intval($x).'"/></td></tr>';

	//date of birth
	if(!in_array('date-of-birth',$exclude))
	{
		if(!empty($data->date_of_birth)){$dob=$data->date_of_birth;}else{$dob=NULL;}
		$out.= '<tr><th scope="row">'.__('Date of birth','church-admin');
		$out.='</th><td>'. church_admin_date_picker($dob,'date_of_birth'.intval($x),FALSE,1910,date('Y'),'date_of_birth','date_of_birth'.intval($x)). '</td></tr>';
	}

	//marital status
	if(!in_array('marital-status',$exclude))
	{
		$church_admin_marital_status=get_option('church_admin_marital_status');
		$out.='<tr><th scope="row">'.__('Marital Status','church-admin').'</th><td><select name="marital_status'.intval($x).'" id="marital_status'.intval($x).'" class="marital_status">';
    	$first=$option='';
    	foreach($church_admin_marital_status AS $id=>$type)
    	{

    		if(!empty($data->marital_status)&& $data->marital_status==$type)
    		{
    			$first='<option value="'.$id.'" selected="selected">'.$type.'</option>'."\r\n";
    		}else $option.='<option value="'.$id.'">'.$type.'</option>'."\r\n";
    	}
    	$out.=$first.$option.'</select></td></tr>'."\r\n";
	}

	//person type
	$out.='<tr><th scope="row">'.__('Person type','church-admin').'</th><td><select name="people_type_id'.intval($x).'" id="people_type_id'.intval($x).'" class="people_type_id">';
    $first=$option='';
    foreach($people_type AS $id=>$type)
    {

    	if(!empty($data->people_type_id)&& $id==$data->people_type_id)
    	{
    		$first='<option value="'.$id.'" selected="selected">'.$type.'</option>'."\r\n";
    	}else $option.='<option value="'.$id.'">'.$type.'</option>'."\r\n";


    }
    $out.=$first.$option.'</select></td></tr>'."\r\n";
	/*************************************
	*
	*	Image
	*
	*************************************/
	if(!in_array('image',$exclude))
	{
		$out.='<tr><th scope="row">'.__('Photo','church-admin').'</th><td>';
		if(!empty($data->attachment_id))
		{
			$out.=wp_get_attachment_image( $data->attachment_id,'ca-people-thumb','', array('class'=>"current-photo frontend-image",'id'=>"frontend-image".$x));
				$out.='<span id="'.$x.'" class="remove-image button-secondary" data-attachment_id="'.intval($data->attachment_id).'" data-type="people" data-id="'.intval($data->people_id).'">'.__('Remove image','church-admin').'</span>';
		}
		else
		{
			$out.= '<img src="'.plugins_url('/images/default-avatar.jpg',dirname(__FILE__) ).'" width="75" height="75" class="frontend-image current-photo alignleft" alt="'.__('Photo of Person','church-admin').'" id="frontend-image'.$x.'" />';
		}

		if(is_admin())
		{//on admin page so use media library
			$out.='<input id="'.$x.'" type="button" class="upload-button button" value="'.__('Upload new image','church-admin').'" />';
		}else
		{//on front end so use boring update
			$out.='<input type="file" id="file-chooser'.$x.'" class="file-chooser" name="logo'.$x.'"/><input type="button" id="'.$x.'" class="frontend-button" value="'.__('Upload Photo','church-admin').'" />';
    	}
	    $out.='<input type="hidden" name="attachment_id'.intval($x).'" class="attachment_id" id="attachment_id'.intval($x).'" ';
    	if(!empty($data->attachment_id))$out.=' value="'.intval($data->attachment_id).'" ';
    	$out.='/><span id="upload-message'.$x.'"></span><br style="clear:left"/>';
    	$out.='</td></tr>';
   	}


   	//small groups
	if(!in_array('small-groups',$exclude))
	{
		$out.='<tr><th scope="row">'.__('Small Group','church-admin').'</th><td><span style="display:inline-block">';
		$smallgroups=$wpdb->get_results('SELECT * FROM '.CA_SMG_TBL);
		if(!empty($smallgroups))
		{
			if(!empty($data->people_id))$dataSmallGroups=church_admin_get_people_meta($data->people_id,'smallgroup');
			foreach($smallgroups AS $smallgroup)
			{
				$out.='<input type="checkbox" class="smallgroup_id"  name="smallgroup_id'.intval($x).'[]" value="'.intval($smallgroup->id).'" ';
				if(!empty($dataSmallGroups) && in_array($smallgroup->id,$dataSmallGroups)) $out.=' checked="checked" ';
				$out.='/> '.esc_html($smallgroup->group_name).'<br/>'."\r\n";
			}
		}
		$out.='<input id="smallgroup'.intval($x).'" class="smallgroup" type="text" name="smallgroup'.intval($x).'" placeholder="'.__('Add New Small Group','church-admin').'"/><br/>';
		$out.= '</span></td></tr>';
	}
	if(!in_array('classes',$exclude))
	{
		//classes
		$classes=$wpdb->get_results('SELECT * FROM '.CA_CLA_TBL);
		if(!empty($classes))
		{
			$out.='<tr><th scope="row">'.__('Classes','church-admin').'</th><td><span style="display:inline-block">';
			if(!empty($data->people_id))$dataClasses=church_admin_get_people_meta($data->people_id,'classes');

			foreach($classes AS $class)
			{
				$out.='<input type="checkbox" class="class_id"  name="class_id'.intval($x).'[]" value="'.intval($class->class_id).'" ';
				if(!empty($dataClasses) && in_array($class->class_id,$dataClasses)) $out.=' checked="checked" ';
				$out.='/> '.esc_html(sprintf(__('%1$s starting %2$s','church-admin'),$class->name,mysql2date(get_option('date_format'),$class->next_start_date))).'<br/>'."\r\n";
			}
			$out.='</td></tr>';
		}
	}
	//socials
	$socials=get_option('church-admin-socials');
	if(in_array('socials',$exclude))$socials=FALSE;

	if(!empty($socials))
	{
		$out.='<tr><th scope="row">'.__('Facebook username','church-admin').'</th><td><input type="text" class="facebook" name="facebook'.$x.'" ';
		if(!empty($data->facebook)) $out.='value="'.esc_html($data->facebook).'" ';
		$out.='/></td></tr>';
		$out.='<tr><th scope="row">'.__('Instagram username','church-admin').'</th><td><input class="instagram" type="text" name="instagram'.$x.'" ';
		if(!empty($data->instagram)) $out.='value="'.esc_html($data->instagram).'" ';
		$out.='/></td></tr>';
		$out.='<tr><th scope="row">'.__('Twitter username','church-admin').'</th><td><input class="twitter" type="text" name="twitter'.$x.'" ';
		if(!empty($data->twitter)) $out.='value="'.esc_html($data->twitter).'" ';
		$out.='/></td></tr>';
	}


	/*************************************
	*
	*	Member levels for authorised users
	*
	*************************************/
	$directory_permission=church_admin_level_check('Directory');

	if($directory_permission)
	{
		$first=$option='';
		$out.='<tr><th scope="row">'.__('Member type','church-admin').'</th><td><select name="member_type_id'.intval($x).'" id="member_type'.intval($x).'" class="member_type_id">';
        foreach($member_type AS $id=>$type)
        {
        	if(!empty($data->member_type_id) && $data->member_type_id==$id)
        	{	$first.= '<option value="'.$id.'" selected="selected" >'.$type.'</option>';
        	}
        	else
        	{
        		$option.='<option value="'.$id.'">'.$type.'</option>';
        	}
        }
        $out.=$first.$option.'</select></td></tr>';
		//member_type_id

		//if(!empty($data->member_data))$prev_member_types=maybe_unserialize($data->member_data);
		$prev_member_types=array();
		if(!empty($data->people_id))
		{
			$prev_member_types_res=$wpdb->get_results('SELECT ID,meta_date FROM '.CA_MET_TBL.' WHERE meta_type="member_date" AND people_id="'.intval($data->people_id).'"');
			if(!empty($prev_member_types_res))
			{

				foreach($prev_member_types_res AS $prevMTrow)
				{
					$prev_member_types[$prevMTrow->ID]=$prevMTrow->meta_date;
				}
			}
		}
		if(!in_array('member-dates',$exclude))
		{
	    	$out.='<tr><th scope="row">'.__('Dates of Member Levels','church-admin').'</th><td><span style="display:inline-block">	';
	    	foreach($member_type AS $key=>$value)
	    	{


	    		if(empty($prev_member_types[$key]))$prev_member_types[$key]=NULL;
	    		if(empty($value))$value='';

				$out.='<span style="float:left;width:150px">'.$value.'</span>'. 			church_admin_date_picker($prev_member_types[$key],'mt-'.intval($key).'-'.$x,FALSE,1910,date('Y'),'mt-'.intval($key),'mt-'.intval($key).'-'.$x).'<br/>';

			}
			$out.='</span></td></tr>'."\r\n";
		}

	}
	/*************************************
	*
	*	Ministries, for authorised users
	*
	*************************************/
	//These next two lines  needed for prayer and bible readings
	if(!empty($data->people_id))$personsMinistries=church_admin_get_people_meta($data->people_id,'ministry');
	$ministries=church_admin_ministries_array();
	if($directory_permission&&!in_array('ministries',$exclude))
	{
		//ministries if allowed

		$out.='<tr><th scope="row">'.__('Ministries','church-admin').'</th><td><span style="display:inline-block">';
		foreach($ministries AS $ministry_id=>$ministry)
		{
			$out.='<input type="checkbox" name="ministry_id'.intval($x).'[]" value="'.intval($ministry_id).'" ';
			if(!empty($personsMinistries) && in_array($ministry_id,$personsMinistries)) $out.=' checked="checked" ';
			$out.='/>&nbsp;'.esc_html($ministry).'<br/>';

		}
		$out.='</span></td></tr>';
	}
	//site
	$sites=$wpdb->get_results('SELECT venue,site_id FROM '.CA_SIT_TBL.' ORDER BY venue ASC');
	if($wpdb->num_rows>1)
	{
		$out.='<tr><th scope="row">'.__('Site','church-admin').'</th><td><select name="site_id'.intval($x).'" id="site_id'.intval($x).'" class="site_id">';
		$first=$option='';
		foreach($sites AS $site)
		{
			if(!empty($data->site_id)&& $data->site_id==$site->site_id)
			{
				$first.='<option value="'.intval($site->site_id).'" selected="selected">'.esc_html($site->venue).'<option>';
			}
			else
			{
				$option.='<option value="'.intval($site->site_id).'">'.esc_html($site->venue).'<option>';
			}
		}
		 $out.=$first.$option.'</select></td></tr>';
    }
    else
    {
    	$out.='<input type="hidden" class="site_id" name="site_id'.intval($x).'" value="'.intval($sites[0]->site_id).'"/>';
    }
    //mobile
    if(!in_array('mobile',$exclude))
	{
    	$out.='<tr><th scope="row">'.__('Mobile','church-admin');
    	if(in_array('mobile',$required))$out.=' *';
    	$out.='</th><td><input type="text" class="mobile" ';
    	if(in_array('mobile',$required))$out.=' required="required"';
    	$out.='id="mobile'.intval($x).'" name="mobile'.intval($x).'" ';
    	if(!empty($data->mobile))$out.=' value="'.esc_html($data->mobile).'" ';
    	$out.='/></td></tr>';
    }
	//email
	$out.='<tr><th scope="row">'.__('Email','church-admin');
	$out.='</th><td><input type="text" class="email" ';
	$out.='id="email'.intval($x).'" name="email'.intval($x).'"';
    if(!empty($data->email))$out.=' value="'.esc_html($data->email).'" ';
    $out.='/></td></tr>';

    if(!in_array('gender',$exclude))
	{
    	$gender=get_option('church_admin_gender');
		$out.='<tr><th scope="row">'.__('Gender','church-admin').'</th><td><select name="sex'.intval($x).'" class="sex" id="sex'.intval($x).'">';
		$first=$option='';

		foreach($gender AS $key=>$value)
		{
			if(isset($data->sex)&&$data->sex == $key)
				{
					$first= '<option value="'.esc_html($key).'" selected="selected">'.esc_html($value).'</option>';
				}
				else
				{
					$option.= '<option value="'.esc_html($key).'" >'.esc_html($value).'</option>';
				}

		}
		$out.=$first.$option.'</select></td></tr>'."\r\n";
	}

	/*****************************************************
	*
	* Custom Fields
	*
	*****************************************************/
	$custom_fields=get_option('church_admin_custom_fields');

	if(!empty($custom_fields)&&!in_array('custom',$exclude))
	{
		foreach($custom_fields AS $id=>$field)
		{
			$dataField='';
			if(!empty($data->people_id))$dataField=$wpdb->get_var('SELECT data FROM '.CA_CUST_TBL .' WHERE people_id="'.intval($data->people_id).'" AND custom_id="'.intval($id).'"');
			$out.='<tr><th scope="row">'.esc_html($field['name']).'</th><td>';
			switch($field['type'])
			{
				case 'boolean':
					$out.='<input type="radio" class="custom-'.$id.'" id="custom-'.$id.'-'.intval($x).'" value="1" name="custom-'.$id.'-'.intval($x).'" ';
					if (isset($dataField)&&$dataField==1)
						$out.= 'checked="checked" ';
					$out.='>'.__('Yes','church-admin').'<br/> <input type="radio" class="custom-'.$id.'" id="custom-'.$id.'-'.intval($x).'" value="0" name="custom-'.$id.'-'.intval($x).'" ';
					if (isset($dataField)&& $dataField==0)
						$out.= 'checked="checked" ';
					$out.='>'.__('No','church-admin');
					break;
				case'text':
					$out.='<input type="text" class="custom-'.$id.'" id="custom-'.$id.'-'.intval($x).'" name="custom-'.$id.'-'.intval($x).'" ';
					if(!empty($dataField)||isset($field['default']))$out.=' value="'.esc_html($dataField).'"';
					$out.='/>';
				break;
				case'date':
					$out.= church_admin_date_picker($dataField,'custom-'.intval($id).'-'.$x,FALSE,1910,date('Y'),'custom-'.intval($id),'custom-'.intval($id).'-'.$x);

				break;
			}
			$out.='</td></tr>';

		}

	}
	/*****************************************************
	*
	* user_id
	*
	*****************************************************/
	if(!empty($data->user_id))$out.='<input type="hidden" name="user_id" value="'.intval($data->user_id).'"/>';
	/*****************************************************
	*
	* Privacy and comms permissions
	*
	*****************************************************/
	$prayer_id=$wpdb->get_var('SELECT ID FROM '.CA_MIN_TBL.' WHERE ministry="'.esc_sql(__('Prayer requests send','church-admin')).'"');
	if($prayer_id)
	{	$out.='<tr><th scope="row">'.__('Receive Prayer requests by email','church-admin').'</th><td><input type="checkbox" value="'.intval($prayer_id).'" id="prayer_chain'.$x.'" class="prayer_chain"   name="prayer_chain'.$x.'" ';
		if(!empty($personsMinistries)&&!empty($prayer_id) && in_array($prayer_id,$personsMinistries)) $out.=' checked="checked" ';
		$out.=' /></td></tr>';
	}
	$bible_id=$wpdb->get_var('SELECT ID FROM '.CA_MIN_TBL.' WHERE ministry="'.esc_sql(__('Bible Readings send','church-admin')).'"');
	if($bible_id)
	{
		$out.='<tr><th scope="row">'.__('Receive new Bible Reading notes by email','church-admin').'</th><td><input type="checkbox" value="'.intval($bible_id).'" id="bible_readings'.$x.'" class="bible_readings"   name="bible_readings'.$x.'" ';
		if(!empty($personsMinistries)&&!empty($bible_id) && in_array($prayer_id,$personsMinistries)) $out.=' checked="checked" ';
		$out.=' /></td></tr>';
	}
	$blogs_id=$wpdb->get_var('SELECT ID FROM '.CA_MIN_TBL.' WHERE ministry="'.esc_sql(__('News send','church-admin')).'"');
	if($blogs_id)
	{
		$out.='<tr><th scope="row">'.__('Receive new blog posts by email','church-admin').'</th><td><input type="checkbox" value="'.intval($blogs_id).'" id="blogs'.$x.'" class="blogs"   name="blogs'.$x.'" ';
		if(!empty($personsMinistries)&&!empty($bible_id) && in_array($blogs_id,$personsMinistries)) $out.=' checked="checked" ';
		$out.=' /></td></tr>';
	}
	$out.='<tr><th scope="row">'.__('Can we send you SMS','church-admin').'</th><td><input type="checkbox" name="sms_send'.$x.'" value="TRUE" class="sms_send" id="sms_send'.$x.'" ';
	if(!empty($data->sms_send)||empty($data)) $out.=' checked="checked" ';
	$out.=' /></td></tr>';
	$out.='<tr><th scope="row">'.__('Can we send you email','church-admin').'</th><td><input type="checkbox" name="email_send'.$x.'" value="TRUE" class="email_send" id="email_send'.$x.'" ';
	if(!empty($data->email_send)||empty($data)) $out.=' checked="checked" ';
	$out.=' /></td></tr>';
	$out.='<tr><th scope="row">'.__('Can we send you mail','church-admin').'</th><td><input type="checkbox" name="mail_send'.$x.'" value="TRUE" class="mail_send" id="mail_send'.$x.'" ';
	if(!empty($data->mail_send)||empty($data)) $out.=' checked="checked" ';
	$out.=' /></td></tr>';
	$out.='<tr><th scope="row">'.__('Do not show me on the password protected address list','church-admin').'</th><td><input type="checkbox" name="private'.$x.'" value="TRUE" class="private" id="private'.$x.'" ';
	if(!empty($data->household_id))
	{
		$sql='SELECT privacy FROM '.CA_HOU_TBL.' WHERE household_id="'.intval($data->household_id).'"';

		$privacy=$wpdb->get_var($sql);
	}
	if(!empty($privacy)) $out.=' checked="checked" ';
	$out.=' /></td></tr>';
	$gdpr=get_option('church_admin_gdpr');

	if($directory_permission)
	{
		$first=$option='';
		$out.='<tr><th scope="row">'.__('How data permission is given','church-admin').'</th><td><input name="gdpr'.$x.'"type="text" ';
		if(!empty($data->gdpr_reason))$out.=' value="'.esc_html($data->gdpr_reason).'" ';
		$out.='</td></tr>';
	}
	else
	{
		$out.='<input type="hidden" name="gdpr" ';
		if(!empty($data->gdpr)){$out.='value="'.esc_html($data->gdpr).'"';}else{$out.'value="'.esc_html(__('User registered on the website','church-admin')).'"';}
		$out.='/>';
	}
    $out.='</table></div></div><!-- .form-group --></div>';
if($x==1)
{
    if(is_admin())
    {
    	$out.='<script >jQuery(document).ready(function($){
			$( "body" ).on("click",".hndle",function(){
console.log("person clicked")
					var id=$(this).attr("id");
					console.log("Person\'s id "+id);
					$("#person"+id).toggle();
				});
				//remove image
				$(".remove-image").click(function()
				{
						var type= $(this).data("type");
						var attachment_id=$(this).data("attachment_id");
						var id=$(this).data("id");
						var imageid=$(this).attr("id");
						var nonce="'.wp_create_nonce("remove-image").'";
						var data={"action":"church_admin","method":"remove-image","type":type,"attachment_id":attachment_id,"id":id,"nonce":nonce};
						console.log(data);
						$.ajax({
											url: ajaxurl,
											type: "POST",
											data: data,
											success: function(res) {
												console.log(res);
												$("#upload-message"+imageid).html("'.__("Image Deleted","church-admin").'<br/>");
												$("#frontend-image"+imageid).attr("src","'.plugins_url('/images/default-avatar.jpg',dirname(__FILE__) ).'");
												$("#frontend-image"+imageid).attr("srcset","");
												$("#attachment_id"+imageid).val("");
											},
											error: function(res) {
										$("#upload-message").html("Error deleting<br/>");
											}
									 });
				});


  var mediaUploader;

  $(".upload-button").click(function(e) {
    e.preventDefault();
    var id="#attachment_id"+$(this).attr("id");
    console.log(id);
    // If the uploader object has already been created, reopen the dialog
      if (mediaUploader) {
      mediaUploader.open();
      return;
    }
    // Extend the wp.media object
    mediaUploader = wp.media.frames.file_frame = wp.media({
      title: "Choose Image",
      button: {
      text: "Choose Image"
    }, multiple: false });

    // When a file is selected, grab the URL and set it as the text fields value
    mediaUploader.on("select", function() {
      var attachment = mediaUploader.state().get("selection").first().toJSON();
      console.log(attachment);
      $(id).val(attachment.id);
      console.log(attachment.sizes.thumbnail.url);
      $(".current-photo").attr("src",attachment.sizes.thumbnail.url);
      $(".current-photo").attr("srcset",null);
    });
    // Open the uploader dialog
    mediaUploader.open();
  });

});</script>';

    }else
    {
   $out.='<script>
	jQuery(document).ready(function($) {
		$( "body" ).on("click",".hndle",function(){
			console.log("person clicked")
				var id=$(this).attr("id");
				console.log("Person\'s id "+id);
				$("#person"+id).toggle();
			});
	$( "body" ).on("click",".frontend-button", function( event ) {

	var id=$(this).attr("id");
	$("#frontend-image"+id).attr("src","'.admin_url().'/images/wpspin_light-2x.gif");
	$("#frontend-image"+id).attr("srcset","");
	var data = new FormData();
	jQuery.each(jQuery("#file-chooser"+id)[0].files, function(i, file) {
    data.append("file-"+i, file);
	});
	$.ajax({
        		url: "'.admin_url().'admin-ajax.php?action=church_admin_image_upload",
        		type: "POST",
        		data: data,
        		processData: false,
        		contentType: false,
        		success: function(res) {
        		var image=JSON.parse(res);
        		console.log(image);

        			$("#upload-message"+id).html("'.__("Success uploading","church-admin").'<br/>");
        			$("#frontend-image"+id).attr("src",image.src);
        			$("#frontend-image"+id).attr("srcset","");
        			$("#attachment_id"+id).val(image.attachment_id);
        		},
        		error: function(res) {
					$("#upload-message").html("'.__("Error uploading, please try again","church-admin").'<br/>");
         		}
         });
    });

});
	</script>';
}
	}
    return $out;
}


/**
 * save a person using POST with $x
 *
 * @param $_POST,$x,$people_id,$household_id
 *
 *
 * @author andy_moyle
 *
 */
function church_admin_save_person($x=1,$people_id=NULL,$household_id,$exclude=array())
{

	global $wpdb;
	delete_option('church-admin-directory-output');//get rid of cached directory
	$wpdb->show_errors();
	$member_type=church_admin_member_type_array();
	$people_type=get_option('church_admin_people_type');
	$church_admin_marital_status= get_option('church_admin_marital_status');
	$out='';
	//sanitise form input

    	$form=array();
    	foreach($_POST AS $key=>$value) $form[$key]=stripslashes_deep($value);

    	//prepare and check odd data
		if(empty($_POST['data_of_birth'.$x])&&!empty($_POST['date_of_birth'.$x.'x']))$form['date_of_birth'.$x]=date('Y-m-d',strtotime($_POST['date_of_birth'.$x.'x']));
		if(!empty($_POST['date_of_birth'.$x])&& church_admin_checkdate($_POST['date_of_birth'.$x])){$form['date_of_birth1']=$_POST['date_of_birth'.$x];}else{$form['date_of_birth'.$x]='0000-00-00';}
    	if(!empty($_POST['attachment_id'.$x])){$form['attachment_id'.$x]=intval($_POST['attachment_id'.$x]);}else{if(!empty($data->attachment_id))$form['attachment_id'.$x]=intval($data->attachment_id);}



    	//build data array for query
    	$data=array();
    	$data['user_id']			=	!empty($form['user_id'])?intval($form['user_id']):NULL;
    	if(empty($exclude))$exclude=array();

			$data['attachment_id']=!empty($form['attachment_id'.$x])?intval($form['attachment_id'.$x]):0;
    	$data['people_type_id']		=	!empty($form['people_type_id'.$x])?intval($form['people_type_id'.$x]):1;
    	$data['household_id']		=	!empty($household_id)?intval($household_id):NULL;
    	$data['member_type_id']		=	!empty($form['member_type_id'.$x])?intval($form['member_type_id'.$x]):1;
    	$data['first_name']			=	!empty($form['first_name'.$x])?$form['first_name'.$x]:"";
    	if(!in_array('nickname',$exclude))$data['nickname']			=	!empty($form['nickname'.$x])?$form['nickname'.$x]:"";
    	if(!in_array('middlename',$exclude))$data['middle_name']		=	!empty($form['middle_name'.$x])?$form['middle_name'.$x]:"";
    	if(!in_array('prefix',$exclude))$data['prefix']				=	!empty($form['prefix'.$x])?$form['prefix'.$x]:"";
    	$data['last_name']			=	!empty($form['last_name'.$x])?$form['last_name'.$x]:"";
    	$data['email']				=	!empty($form['email'.$x])?$form['email'.$x]:"";
    	$data['mobile']				=	!empty($form['mobile'.$x])?$form['mobile'.$x]:"";
    	if(!in_array('socials',$exclude))$data['facebook']			=	!empty($form['facebook'.$x])?$form['facebook'.$x]:"";
    	if(!in_array('socials',$exclude))$data['twitter']			=	!empty($form['twitter'.$x])?$form['twitter'.$x]:"";
    	if(!in_array('socials',$exclude))$data['instagram']			=	!empty($form['instagram1'])?$form['instagram'.$x]:"";
    	if(!in_array('date-of-birth',$exclude))$data['date_of_birth']		=	!empty($form['date_of_birth'.$x])?$form['date_of_birth'.$x]:"0000-00-00";
    	if(!in_array('marital-status',$exclude))$data['marital_status']		=	isset($form['marital_status'.$x])?$church_admin_marital_status[$form['marital_status'.$x]]:__('N/A','church-admin');
    	if(!in_array('image',$exclude))$data['attachment_id']		=	!empty($form['attachment_id'.$x])?$form['attachment_id'.$x]:0;
    	//must use isset as female is  0 and !empty(0) returns false!
    	$data['sex']				=	isset($form['sex'.$x])?intval($form['sex'.$x]):1;
    	//$data['prayer_chain']		=	isset($form['prayer_chain'.$x])?1:0;//deprecated 1.2608, now part of ministries
    	$data['site_id']			=	isset($form['site_id'.$x])?intval($form['site_id'.$x]):1;
    	$data['email_send']			=	!empty($form['email_send'.$x])?1:0;
    	$data['sms_send']			=	!empty($form['sms_send'.$x])?1:0;
    	$data['mail_send']			=	!empty($form['mail_send'.$x])?1:0;
    	$data['gdpr_reason']		=	!empty($form['gdpr'.$x])?$form['gdpr'.$x]:"";
     	$data['kidswork_override']	=	!empty($form['kidswork_override'.$x])?$form['kidswork_override'.$x]:"0";
			$data['people_order']=intval($x);
			if($x==1){$data['head_of_household']=1;}else{$data['head_of_household']=0;}
			//front_end_register doesn't pass people_id so let's check they are not in first...
			if(empty($people_id))
    	{
					$people_id=$wpdb->get_var('SELECT people_id FROM '.CA_PEO_TBL.' WHERE household_id="'.intval($household_id).'" AND first_name="'.esc_sql($data['first_name']).'" AND last_name="'.esc_sql($data['last_name']).'"');
			}
			if(!empty($people_id))
			{
				$SET=array();

				foreach($data AS $key=>$value) {$SET[]=' '.$key.'="'.esc_sql($value).'"';}
				$sql='UPDATE '.CA_PEO_TBL.' SET '.implode(",",$SET).' WHERE people_id="'.intval($people_id).'"' ;
    	}
			else
			{
				$columns=$rows=array();
				foreach($data AS $key=>$value){$columns[]=$key;$rows[]=esc_sql($value);}
				$sql='INSERT INTO '.CA_PEO_TBL.' ('.implode(", ",$columns).') VALUES ("'.implode('", "',$rows).'")';

			}

		if(defined('CA_DEBUG'))church_admin_debug("Person update/insert \r\n".$sql);
		$wpdb->query($sql);


    	if(!empty($form['private'.$x]))
    	{
    		//privacy
    		$sql='UPDATE '.CA_HOU_TBL.' SET privacy=1 WHERE household_id="'.intval($data['household_id']).'"';

    		$wpdb->query($sql);
    	}
    	//small groups
		if(empty($people_id)){$people_id=$wpdb->insert_id;church_admin_debug("People_id:".$people_id);}
		//create user if necessary
    	if(!empty($_POST['username']))church_admin_create_user($people_id,$household_id,stripslashes($_POST['username']));

			/*************************************************************
			*
			*    Member level dates
			*
			***************************************************************/
			church_admin_delete_people_meta(NULL,$people_id,'member_date');

			if(is_array($exclude)&&!in_array('member-dates',$exclude))
			{
				//handle member type dates


				foreach($member_type AS $id=>$type)
				{

					if(!empty($_POST['mt-'.$id.'-'.$x])&& church_admin_checkdate($_POST['mt-'.$id.'-'.$x]))
					{
						church_admin_update_people_meta($id,$people_id,'member_date',$_POST['mt-'.$id.'-'.$x]);

					}
				}
			}
			/*************************************************************
			*
			*    Small Group
			*
			***************************************************************/
		church_admin_delete_people_meta(NULL,$people_id,'smallgroup');
		//handle new smallgroup
		if(!empty($_POST['smallgroup'.$x]))
		{

				$check=$wpdb->get_var('SELECT id FROM '.CA_SMG_TBL.' WHERE group_name="'.esc_sql(stripslashes($_POST['smallgroup'.$x])).'"');
				if(!empty($check)){church_admin_update_people_meta($check,$people_id,'smallgroup');}
				else
				{
					$wpdb->query('INSERT INTO '.CA_SMG_TBL.' (group_name) VALUES("'.esc_sql(stripslashes($_POST['smallgroup'.$x])).'")');
					$id=$wpdb->insert_id;
					church_admin_update_people_meta($id,$people_id,'smallgroup');
				}

		}

		if(!empty($_POST['smallgroup_id'.$x]))foreach($_POST['smallgroup_id'.$x] AS $key=>$id)
		{

				church_admin_update_people_meta($id,$people_id,'smallgroup');
		}
		//classes
		church_admin_delete_people_meta(NULL,$people_id,'classes');
		//handle new smallgroup
		if(!empty($_POST['class_id'.$x]))
		{

				foreach($_POST['class_id'.$x] AS $key=>$class_id)church_admin_update_people_meta($class_id,$people_id,'classes');

		}

		if(!empty($_POST['smallgroup_id'.$x]))foreach($_POST['smallgroup_id'.$x] AS $key=>$id)
		{

				church_admin_update_people_meta($id,$people_id,'smallgroup');
		}

		//ministries

		church_admin_delete_people_meta(NULL,$people_id,'ministry');

		if(!empty($_POST['prayer_chain'.$x])){church_admin_update_people_meta($_POST['prayer_chain'.$x],$people_id,"ministry");}
		if(!empty($_POST['bible_readings'.$x])){church_admin_update_people_meta($_POST['bible_readings'.$x],$people_id,"ministry");}
		if(!empty($_POST['blogs'.$x])){church_admin_update_people_meta($_POST['blogs'.$x],$people_id,"ministry");}
		if(!empty($_POST['ministry_id'.$x]))foreach($_POST['ministry_id'.$x] AS $key=>$id)
		{

				church_admin_update_people_meta($id,$people_id,"ministry");
		}



		//classes
		if(!empty($_POST['class_id'.$x]))foreach($_POST['class_id'.$x] AS $key=>$id) 		church_admin_update_people_meta($id,$people_id,'class');
		/********************************************************
		*
		*   USER id
		*
		*********************************************************/


		//user account
		//check is user has user_id
		$user_id=$wpdb->get_var('SELECT user_id FROM '.CA_PEO_TBL.' WHERE people_id="'.intval($people_id).'"');
		if(!empty($_POST['username'.$x])&&empty($user_id))
		{
			$out.= church_admin_create_user($people_id,$household_id,$_POST['username'.$x]);
		}
		if(!empty($_POST['ID']))
		{
			$wpdb->query('UPDATE '.CA_PEO_TBL.' SET user_id="'.intval($_POST['ID']).'" WHERE people_id="'.intval($people_id).'"');
		}
	/*****************************************************
	*
	* Custom Fields
	*
	*****************************************************/
	$custom_fields=get_option('church_admin_custom_fields');

	if(!empty($custom_fields))
	{
		$wpdb->query('DELETE FROM '.CA_CUST_TBL.' WHERE people_id="'.intval($people_id).'"');
		foreach($custom_fields AS $id=>$field)
		{
			if(isset($_POST['custom-'.$id.'-'.$x]))
			{
				$sql='INSERT INTO  '.CA_CUST_TBL.' (data,people_id,custom_id) VALUES ("'.stripslashes($_POST['custom-'.$id.'-'.$x]).'","'.intval($people_id).'","'.intval($id).'")';
				if(defined('CA_DEBUG'))church_admin_debug($sql);
				$wpdb->query($sql);
			}
		}

	}


	return array('output'=>$out,'people_id'=>$people_id,'household_id'=>$household_id);
}



function church_admin_gdpr_email()
{
	if(!church_admin_level_check('Directory'))wp_die(__('You don\'t have permissions to do that','church-admin'));
	global $wpdb;

	//grab ID of church_admin_register shortcode
	$registerID=$wpdb->get_var('SELECT ID FROM '.$wpdb->posts.' WHERE post_content LIKE "%[church_admin_register]%" AND post_status="publish"');
	if(!empty($registerID))update_option('church_admin_register',$registerID);

	echo'<h2>'.__('General Data Protection Requirement Email Sending','church-admin').'</h2>';
	$result=$wpdb->get_results(' SELECT CONCAT_WS(" ", a.first_name, a.last_name) AS name, a.last_name,a.people_id, a.email ,b.* FROM '.CA_PEO_TBL.' a, '.CA_HOU_TBL.' b  WHERE a.household_id=b.household_id AND  email!=""  AND (gdpr_reason IS NULL OR gdpr_reason="")  GROUP BY email ');
	if(!empty($result))
	{

		foreach($result AS $row)
		{

			church_admin_gdpr_email_send($row);
		}
	}
	else
	{

		echo'<p>'.__('Everyone has responded! So you are GDPR compliant where communications permissions are concerned','church-admin').'</p>';
	}
}
function church_admin_gdpr_email_test()
{
	if(!church_admin_level_check('Directory'))wp_die(__('You don\'t have permissions to do that','church-admin'));
	global $wpdb;
	$user = wp_get_current_user();
	$row=$wpdb->get_row(' SELECT CONCAT_WS(" ", a.first_name, a.last_name) AS name, a.last_name,a.people_id, a.email ,b.* FROM '.CA_PEO_TBL.' a, '.CA_HOU_TBL.' b  WHERE a.household_id=b.household_id AND  email!=""  AND  user_id="'.intval($user->ID).'"');
	if(empty($row)){echo'<div class="notice notice-warning notice-inline">'.__('Your login is not attached to anyone in the directory','church-admin').'</div>';}
	else
	{
		echo'<h2>'.__('GDPR test email send','church-admin').'</h2>';
		church_admin_gdpr_email_send($row);
	}
}
function church_admin_gdpr_email_send($row)
{
	//if(!church_admin_level_check('Directory'))wp_die(__('You don\'t have permissions to do that','church-admin'));
	global $wpdb;
	//grab ID of church_admin_register shortcode
	$registerID=$wpdb->get_var('SELECT ID FROM '.$wpdb->posts.' WHERE post_content LIKE "%[church_admin_register]%" AND post_status="publish"');
	if(!empty($registerID))update_option('church_admin_register',$registerID);
	$message=$row->name.'<br/>';

	$message.=get_option('church_admin_gdpr_email');
	$message=str_replace('[CONFIRM_LINK]', home_url().'?confirm='.esc_html($row->last_name).'/'.intval($row->people_id),$message);
	$message=str_replace('[SITE_URL]',home_url(),$message);
	$message=str_replace('[CHURCH_NAME]',get_bloginfo('name'),$message);
	$message=str_replace('[CONFIRM_URL]',' <a href="'.home_url().'?confirm='.esc_html($row->last_name).'/'.intval($row->people_id).'">'.__('Click to confirm').'</a>',$message);
	$edit_link=add_query_arg( 'household_id', intval($row->household_id), get_permalink($registerID) );
	$message=str_replace('[EDIT_URL]','<a href="'.$edit_link.'">'.$edit_link.'</a>',$message);


			$people=$wpdb->get_results('SELECT * FROM '.CA_PEO_TBL.' WHERE household_id="'.intval($row->household_id).'" ORDER BY people_order ASC');
			if(!empty($people))
			{
				$details='<p><strong>'.__('Household details','church-admin').'</strong></p>';
				$details.='<table><thead><tr><th>'.__('Name','church-admin').'</th><th>'.__('Cell phone','church-admin').'</th><th>'.__('Email','church-admin').'</th><th>'.__('Date of Birth','church-admin').'</th></tr></thead><tbody>';
				foreach($people AS $person)
				{
					$name=array_filter(array($person->first_name,$person->middle_name,$person->last_name));
					$mobile=!empty($person->mobile)?esc_html($person->mobile):"";
					$email=!empty($person->email)?esc_html($person->email):"";

					if(!empty($person->date_of_birth)&&$person->date_of_birth!="0000-00-00"){$dob=mysql2date(get_option('date_format'),$person->date_of_birth);}else{$dob="";}

					$details.='<tr><td>'.esc_html(implode(" ",$name)).'</td><td>'.$mobile.'</td><td>'.$email.'</td><td>'.$dob.'</td></tr>';
				}
				$details.='</tbody></table>';
			}
			if(!empty($row->address))$details.='<p><strong>'.__('Address','church-admin').':</strong> '.esc_html($row->address).'</p>';
			if(!empty($row->phone))$details.='<p><strong>'.__('Phone','church-admin').':</strong> '.esc_html($row->phone).'</p>';
			if(!empty($details))$message=str_replace('[HOUSEHOLD_DETAILS]',$details,$message);

			$message.='<p><a href="'.site_url().'?confirm='.esc_html($row->last_name).'/'.intval($row->people_id).'" style="display: inline-block;padding: 6px 12px;margin-bottom: 0;font-size: 14px;font-weight: 400;line-height: 1.42857143;text-align: center;white-space: nowrap;vertical-align: middle;-ms-touch-action: manipulation;touch-action: manipulation;cursor: pointer;-webkit-user-select: none;-moz-user-select: none;-ms-user-select: none; user-select: none;background-image: none;border: 1px solid transparent;border-radius: 4px;color: #fff;background-color: #5bc0de;border-color: #46b8da;">'.__('Click here to confirm','church-admin').'</a></p>';
				if(get_option('church_admin_cron')!='immediate')
                {
					$emails[]=$row->email;
					if(QueueEmail($row->email,__('Please confirm you are happy to receive communications','church-admin'),$message)) echo'<p>'.esc_html($row->email).' queued</p>';
				}
				else
				{

						add_filter('wp_mail_content_type','church_admin_email_type');
						add_filter( 'wp_mail_from_name', 'church_admin_from_name');
						add_filter( 'wp_mail_from', 'church_admin_from_email');


						if(wp_mail($row->email,__('Please confirm you are happy to receive communications','church-admin'),$message)){echo'<p>'.sprintf(__('Confirmation email sent immediately to %1s','church-admin'), esc_html($row->email)).'</p>';}
						else {echo $GLOBALS['phpmailer']->ErrorInfo;}

					remove_filter('wp_mail_content_type','church_admin_email_type');
				}

}

function church_admin_gdpr_pdf()
{
	if(!church_admin_level_check('Directory'))wp_die(__('You don\'t have permissions to do that','church-admin'));
	global $wpdb;
	require_once(plugin_dir_path(dirname(__FILE__)).'includes/fpdf.php');
	//grab ID of church_admin_register shortcode
	$registerID=$wpdb->get_var('SELECT ID FROM '.$wpdb->posts.' WHERE post_content LIKE "%[church_admin_register]%" AND post_status="publish"');
	if(!empty($registerID))update_option('church_admin_register',$registerID);

	$sql=' SELECT a.last_name,a.people_id, a.email ,b.* FROM '.CA_PEO_TBL.' a, '.CA_HOU_TBL.' b  WHERE a.household_id=b.household_id AND  email!=""  AND (gdpr_reason IS NULL OR gdpr_reason="")  GROUP BY email ORDER BY a.last_name';

	$result=$wpdb->get_results($sql);
	if(!empty($result))
	{

		$pdf = new FPDF();

		foreach($result AS $row)
		{
			$pdf->AddPage('P',get_option('church_admin_pdf_size'));
			$pdf->SetFont('Arial','B',16);
			$pdf->Cell(0,10,urldecode(church_admin_encode(get_bloginfo('name').' '.__('Data Protection Permission','church-admin').' - '.$row->last_name)),0,2,'L');
			$pdf->Ln(10);
			$pdf->SetFont('Arial','',12);
			$text=sprintf(__('There are some new data protection regulations coming in to protect how your personal data is used. We store your name, address and phone details so we can keep the church organised and would like to be able to continue to communicate by email, sms and mail with you. Your contact details are available on the website (%1$s) within a password protected area. Please check with other members of your household who are over 16, sign this form and return if you are happy for us to continue to hold your personal data and use it to communicate with you. If you are not happy or would like to discuss further then do get in touch with the church office.','church-admin'),site_url());
			$height=$pdf->GetMultiCellHeight(0,7,$text,'LTR','L');
			$pdf->MultiCell(0, 7, urldecode(church_admin_encode($text)),0,'L' );
			$pdf->Ln($height+10);
			//confirm online
			$text = __('Click this link to confirm online','church-admin');
			$link=site_url().'?confirm='.urldecode(church_admin_encode($row->last_name)).'/'.intval($row->people_id);
			$pdf->SetFont('Arial','U',12);
			$pdf->Cell(0,7,$text,0,1,'L',NULL,$link);
			$pdf->Ln(5);

			//person's entry
			$people=$wpdb->get_results('SELECT * FROM '.CA_PEO_TBL.' WHERE household_id="'.intval($row->household_id).'" ORDER BY people_order ASC');
			if(!empty($people))
			{
				$pdf->SetFont('Arial','B',12);
				$pdf->Cell(0,7,__('People in your household','church-admin'),0,1,'L');
				$pdf->Cell(50,7,__("Name",'church-admin'),1,0,'L');
				$pdf->Cell(30,7,__("Mobile",'church-admin'),1,0,'L');
				$pdf->Cell(75,7,__("Email",'church-admin'),1,0,'L');
				$pdf->Cell(30,7,__("Date of Birth",'church-admin'),1,1,'L');
				$pdf->SetFont('Arial','',12);
				$text='';
				foreach($people AS $person)
				{
					$name=array_filter(array($person->first_name,$person->middle_name,$person->last_name));
					$mobile=!empty($person->mobile)?esc_html($person->mobile):"";
					$email=!empty($person->email)?esc_html($person->email):"";

					if(!empty($person->date_of_birth)&&$person->date_of_birth!="0000-00-00")
					{$dob=mysql2date(get_option('date_format'),$person->date_of_birth);}else{$dob="";}


					$pdf->Cell(50,7,urldecode(church_admin_encode(implode(' ',$name))),1,0,'L');
					$pdf->Cell(30,7,$mobile,1,0,'L');
					$pdf->Cell(75,7,$email,1,0,'L');
					$pdf->Cell(30,7,$dob,1,1,'L');
				}

			}
			$pdf->Ln(5);
			$pdf->SetFont('Arial','B',12);
			$pdf->Cell(0,7,urldecode(church_admin_encode(__('Address details','church-admin'))),0,1,'L');
			$pdf->SetFont('Arial','',12);
			if(!empty($row->address))$pdf->Cell(0,7,urldecode(church_admin_encode($row->address)),0,1,'L');
			if(!empty($row->phone))$pdf->Cell(0,7,esc_html($row->phone),0,1,'L');

			//form confirmation...
			$pdf->Ln(10);
			$pdf->SetFont('Arial','B',12);
			$pdf->Cell(0,7,urldecode(church_admin_encode(__('Confirmation of personal data use','church-admin'))),0,1,'L');
			$pdf->SetFont('Arial','',12);
			$pdf->Cell(7,7,'',1);
			$text=urldecode(church_admin_encode(__('Please send email','church-admin')));
			$pdf->Cell(0,7,$text,0,1,'L');
			$pdf->Ln(2);
			$pdf->Cell(7,7,'',1);
			$text=urldecode(church_admin_encode(__('Please send SMS','church-admin')));
			$pdf->Cell(0,7,$text,0,1,'L');
			$pdf->Ln(2);
			$pdf->Cell(7,7,'',1);
			$text=urldecode(church_admin_encode(__('Please send mail','church-admin')));
			$pdf->Cell(0,7,$text,0,1,'L');
			$pdf->Ln(2);
			$pdf->Cell(7,7,'',1);
			$text=urldecode(church_admin_encode(__("Please don't publish my address details on the password protected website",'church-admin')));
			$pdf->Cell(0,7,$text,0,1,'L');
			$pdf->Ln(5);
			$pdf->Cell(75,7,'','B');
			$pdf->Cell(0,7,urldecode(church_admin_encode(__('Signature','church-admin'))),0,1,'L');
			$pdf->Ln(10);
			$pdf->Cell(75,7,'','B');
			$pdf->Cell(0,7,urldecode(church_admin_encode(__('Date','church-admin'))),0,1,'L');


		}
			$pdf->Output();
	}else{echo'no people';}
}

function gdpr_confirm_everyone()
{
	if(!church_admin_level_check('Directory'))wp_die(__('You don\'t have permissions to do that','church-admin'));
	global $wpdb;
	$wpdb->show_errors();
	$wpdb->query('UPDATE '.CA_PEO_TBL.' SET mail_send=1,email_send=1,sms_send=1,gdpr_reason="'.__('GDPR Confirmed by admin','church-admin').'" WHERE gdpr_reason IS NULL OR gdpr_reason=""');
	echo'<h2>GDPR</h2>';
	echo'<p>'.__('That was very naughty. You have confirmed that your entire directory are happy to have personal data stored and be communicated with','church-admin').'</p>';
}


function church_admin_not_confirmed_gdpr()
{
	global $wpdb;
	$result=$wpdb->get_results(' SELECT CONCAT_WS(" ", a.first_name, a.last_name) AS name, a.last_name,a.people_id, a.email ,b.* FROM '.CA_PEO_TBL.' a, '.CA_HOU_TBL.' b  WHERE a.household_id=b.household_id AND  email!=""  AND (gdpr_reason IS NULL OR gdpr_reason="")  GROUP BY email ');
	if(!empty($result))
	{
			echo'<h3>'.__('These people have not responded to GDPR confirmation','church-admin').'</h3>';
			foreach($result AS $row)
			{
				echo'<p>'.esc_html($row->name).'</p>';
			}
	}
}
function church_admin_bulk_geocode()
{
		global $wpdb;
		$results=$wpdb->get_results('SELECT * FROM '.CA_HOU_TBL.' WHERE address!=", , , ," OR address!=""');
		if(!empty($_POST['batch_geocode']))
		{
			if(!empty($results))
			{
				foreach($results AS $row)
				{
						if(isset($_POST['lat'.intval($row->household_id)])&&isset($_POST['lng'.intval($row->household_id)]))
						{
								$wpdb->query('UPDATE '.CA_HOU_TBL.' SET lat="'.esc_sql($_POST['lat'.intval($row->household_id)]).'", lng="'.esc_sql($_POST['lat'.intval($row->household_id)]).'" WHERE household_id="'.intval($row->household_id).'"');

						}
				}
				echo'<div class="notice notice-success notice-inline"><h2>'.__('Address geocodes updated','church-admin').'</h2></div>';
			}else{echo'<div class="notice notice-success">'.__('No households updated','church-admin').'</div>';}
		}
		else {


			echo'<form action="" method="post">';
			echo'<p><a href="#" class="button-primary" id="geocode_address">'.__('Click to batch geocode household addresses','church-admin').'</a></p>';
			echo '<p><input type="hidden" name="batch_geocode" value="TRUE"/><input type="submit" id="submit_batch_geocode" disabled="disabled" value="'.__('Save batched geocode','church-admin').'"/></p>';
			echo'<div id="map"></div><script>var beginLat = 51.50351129583287;var beginLng = -0.148193359375;</script>';


			if(!empty($results))
			{
				foreach($results AS $row)
				{
					echo '<p >'.esc_html($row->address).'<input type="hidden" id="'.intval($row->household_id).'" class="address" value="'.esc_html($row->address).'"/></p>';
					echo __('Latitude','church-admin').'<input type="text" value="'.esc_html($row->lat).'" name="lat'.intval($row->household_id).'"/> '. __('Longitude','church-admin').'<input type="text" value="'.esc_html($row->lng).'" name="lat'.intval($row->household_id).'"/>';
				}
			}
			echo'</form>';
		}
}
