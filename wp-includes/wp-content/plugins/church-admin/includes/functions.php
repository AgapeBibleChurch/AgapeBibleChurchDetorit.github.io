<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


/*
 * Matches each symbol of PHP date format standard
 * with jQuery equivalent codeword
 * @author Tristan Jahier
 */
function dateformat_PHP_to_jQueryUI($php_format)
{
    $SYMBOLS_MATCHING = array(
        // Day
        'd' => 'dd',
        'D' => 'D',
        'j' => 'd',
        'l' => 'DD',
        'N' => '',
        'S' => '',
        'w' => '',
        'z' => 'o',
        // Week
        'W' => '',
        // Month
        'F' => 'MM',
        'm' => 'mm',
        'M' => 'M',
        'n' => 'm',
        't' => '',
        // Year
        'L' => '',
        'o' => '',
        'Y' => 'yy',
        'y' => 'y',
        // Time
        'a' => '',
        'A' => '',
        'B' => '',
        'g' => '',
        'G' => '',
        'h' => '',
        'H' => '',
        'i' => '',
        's' => '',
        'u' => ''
    );
    $jqueryui_format = "";
    $escaping = false;
    for($i = 0; $i < strlen($php_format); $i++)
    {
        $char = $php_format[$i];
        if($char === '\\') // PHP date format escaping character
        {
            $i++;
            if($escaping) $jqueryui_format .= $php_format[$i];
            else $jqueryui_format .= '\'' . $php_format[$i];
            $escaping = true;
        }
        else
        {
            if($escaping) { $jqueryui_format .= "'"; $escaping = false; }
            if(isset($SYMBOLS_MATCHING[$char]))
                $jqueryui_format .= $SYMBOLS_MATCHING[$char];
            else
                $jqueryui_format .= $char;
        }
    }
    return $jqueryui_format;
}
function church_admin_date_picker($db_date,$name,$array=FALSE,$start=NULL,$end=NULL,$class=NULL,$id=NULL)
{
	if(empty($start))$start=date('Y');
	if(empty($end))$end=date('Y')+10;
	$out='';
	$date_format=get_option('date_format');
	$jsdate_format=dateformat_PHP_to_jQueryUI($date_format);
	//text field that can be seen
	$out.='<input type="text" name="'.esc_html($name).'x';
	if($array)$out.='[]';
	$out.='" class="'.sanitize_title($class).'x" ';
	if(!empty($db_date)&&$db_date!='0000-00-00') $out.= ' value="'.mysql2date(get_option('date_format'),$db_date).'" ';
	$out.=' id="'.esc_html($id).'x"/>'."\r\n";
	$out.='<span class="dashicons dashicons-calendar-alt"></span>';
	//data that will be processed when form submitted
	$out.='<input id="'.esc_html($id).'" type="hidden" name="'.esc_html($name);
	if($array)$out.='[]';
	$out.='" class="'.esc_html($class).'" ';
	if(!empty($db_date))$out.='value="'. esc_html($db_date).'" ';
	$out.='/>';
	$out.='<script>
		jQuery(document).ready(function($){


         	$("body").on("focus",".'.esc_html($class).'x", function(){
         		var hidden = "#"+this.id.slice(0, -1);//need to be able to detect the hidden id field when cloned
    			$(this).datepicker({altFormat: "yy-mm-dd",altField:hidden, dateFormat : "'.$jsdate_format.'", changeYear: true ,yearRange: "'.esc_html($start).':'.esc_html($end).'"});
			});
		});
		</script>';

	return $out;

}




/**
 * Array of ministries
 *
 * @param
 * deprecated
 *
 * @author andy_moyle
 *
 */
function church_admin_ministries($childID=NULL){
	global $wpdb;
	$ministries=array();
	$sql='SELECT * FROM '.CA_MIN_TBL;
	$where='';
	//if(!empty($childID)) {$where=' WHERE childID ="'.intval($childID).'"';}
	//if($childID=='None')$where=' WHERE childID =0';
	$order=' ORDER BY ministry';
	$results=$wpdb->get_results($sql.$where.$order);
	if(!empty($results))
	{

		foreach($results as $row){$ministries[$row->ID]=$row->ministry;}

	}

	return $ministries;

}
/**
 * sets wp_mail to html type!
 *
 * Author:     Andy Moyle
 * Author URI: http://www.churchadminplugin.com
 *
 *
 */
 function church_admin_email_type($content_type){
return 'text/html';
}


/**
 * This function initialises wp_mail with stored smtp settings
 *
 * Author:     Andy Moyle
 * Author URI: http://www.churchadminplugin.com
 *
 *
 */
add_action( 'phpmailer_init', 'church_admin_smtp_email' );
function church_admin_smtp_email( $phpmailer ) {

	$smtp=get_option('church_admin_smtp_settings');
	if(!empty($smtp['username'])&&!empty($smtp['host'])&&!empty($smtp['port'])&&!empty($smtp['password'])&&!empty($smtp['secure']))
	{
		// Define that we are sending with SMTP
		$phpmailer->isSMTP();

		// The hostname of the mail server
		$phpmailer->Host = $smtp['host'];//"smtp.example.com";

		// Use SMTP authentication (true|false)
		$phpmailer->SMTPAuth = $smtp['auth'];//true;

		// SMTP port number - likely to be 25, 465 or 587
		$phpmailer->Port = $smtp['port'];//"587";

		// Username to use for SMTP authentication
		$phpmailer->Username = $smtp['username'];//yourusername";

		// Password to use for SMTP authentication
		$phpmailer->Password =$smtp['password']; "yourpassword";

		// Encryption system to use - ssl or tls
		$phpmailer->SMTPSecure =$smtp['secure']; //"tls";

		$phpmailer->From = $smtp['from'];//"you@yourdomail.com";
		$phpmailer->FromName = $smtp['from_name'];//"Your Name";
	}
}
//end smtp settings for wp_mail


function church_admin_return_bytes($val) {
    $val = trim($val);

    $last = strtolower($val[strlen($val)-1]);
    switch($last)
    {
        case 'g':
        	$val *= 1024;
        break;
        case 'm':
        	$val *= 1024;
        case 'k':
        	$val *= 1024;
    }
    return $val;
}
function church_admin_max_file_upload_in_bytes() {
    //select maximum upload size
    $max_upload = church_admin_return_bytes(ini_get('upload_max_filesize'));
    //select post limit
    $max_post = church_admin_return_bytes(ini_get('post_max_size'));
    //select memory limit
    $memory_limit = church_admin_return_bytes(ini_get('memory_limit'));
    // return the smallest of them, this defines the real limit
    return min($max_upload, $max_post, $memory_limit)/(1024*1024);
}

function church_admin_get_id_by_shortcode($shortcode) {
	global $wpdb;

	$id = NULL;

	$sql = 'SELECT ID
		FROM ' . $wpdb->posts . '
		WHERE
			post_type = "page"
			AND post_status="publish"
			AND post_content LIKE "%' . $shortcode . '%"';

	$id = $wpdb->get_var($sql);
	return $id;
}
function church_admin_initials($people)
{
	$people=maybe_unserialize($people);
	if(!empty($people))
	{

		foreach($people as $id=>$peep)
		{
			if(ctype_digit($peep)){$person=church_admin_get_person($peep);}else{$person=$peep;}
			$strlen=strlen($person);
			$initials[$id]='';
			for($i=0;$i<=$strlen;$i++)
			{
				$char=substr($person,$i,1);
				if (ctype_upper($char)){$initials[$id].=$char;}
			}
		}

		return implode(', ',$initials);

	}else return '';
}

function church_admin_checkdate($date)
{
		$d=explode('-',$date);
		if(is_array($d) && count($d)==3 && checkdate($d[1],$d[2],$d[0])){return TRUE;}else{return FALSE;}
}
function church_admin_level_check($what)
{
    global $current_user;
    wp_get_current_user();

    $user_permissions=maybe_unserialize(get_option('church_admin_user_permissions'));

    $level=get_option('church_admin_levels');

    if(!empty($user_permissions[$what]))
    {//user permissions have been set for $what

		if( in_array($current_user->ID,maybe_unserialize($user_permissions[$what]))){return TRUE;}else{return FALSE;}
	}//end user permissions have been set
    elseif(!empty($level[$what]) && $level[$what]=="administrator"){return current_user_can('manage_options');}
    elseif(!empty($level[$what]) && $level[$what]=="editor"){return current_user_can('delete_others_pages');}
    elseif(!empty($level[$what]) &&$level[$what]=="author"){return current_user_can('publish_posts');}
    elseif(!empty($level[$what]) &&$level[$what]=="contributor"){return current_user_can('edit_posts');}
    elseif(!empty($level[$what]) &&$level[$what]=="subscriber"){return current_user_can('read');}
    else{ return false;}
}

function church_admin_user($ID)
{
		global $wpdb;
		$people_id=$wpdb->get_var('SELECT people_id FROM '.CA_PEO_TBL.' WHERE user_id="'.esc_sql($ID).'"');
		if(!empty($people_id)) {return $people_id;}else{return FALSE;}
}

function church_admin_collapseBoxForUser($userId, $boxId) {
    $optionName = "closedpostboxes_church-admin";
    $close = get_user_option($optionName, $userId);
    $closeIds = explode(',', $close);
    $closeIds[] = $boxId;
    $closeIds = array_unique($clodeIds); // remove duplicate Ids
    $close = implode(',', $closeIds);
    update_user_option($userId, $optionName, $close);
}



function church_admin_autocomplete($name='people',$first_id='friends',$second_id='to',$current_data=array(),$user_id=FALSE)
{
            /**
 *
 * Creates autocomplete field
 *
 * @author  Andy Moyle
 * @param    $name,$first_id,$second_id
 * @return   html string
 * @version  0.1
 *
 *
 */
    $current='';
    if(!empty($current_data))
    {
        $curr_data=maybe_unserialize($current_data);

        if(is_array($curr_data))
		{
			foreach($curr_data AS $key=>$value)
			{

				if(ctype_digit($value))
				{
						if(!$user_id)
						{//people_id
							$peoplename=church_admin_get_person($value);
						}
						else
						{//user_id
							$peoplename=church_admin_get_name_from_user($value);
						}
				}else $peoplename=$value;
				$current.=$peoplename.', ';
			}
		}else$current=$current_data;
    }
    $out= '<input id="'.sanitize_title_with_dashes($first_id).'" class="to" placeholder="'.__('Enter names, separated by commas','church-admin').'" type="text" name="'.esc_html($name).'" value="'.esc_html($current).'"/> ';
    $ajax_nonce = wp_create_nonce( "church-admin-autocomplete" );
    $out.='<script type="text/javascript">

	jQuery(document).ready(function ($){

	$("#'.sanitize_title_with_dashes($first_id).'").autocomplete({
		source: function(req, add){
			req.action="church_admin";
			req.method="autocomplete";
			req.security="'.$ajax_nonce.'";
			console.log(req);
			$.getJSON("'.site_url().'/wp-admin/admin-ajax.php", req,  function(data) {

                    console.log("Response " + data);
                    //create array for response objects
                    var suggestions = [];

                    //process response
                    $.each(data, function(i, val){
                    suggestions.push(val.name);
                });

                //pass array to callback
                add(suggestions);
            });

		},
		select: function (event, ui) {
                var terms = $("#'.sanitize_title_with_dashes($first_id).'").val().split(", ");
		// remove the current input
                terms.pop();
                console.log(terms);
		// add the selected item
                terms.push(ui.item.value);

                // add placeholder to get the comma-and-space at the end
                terms.push("");
                this.value = terms.join(", ");
                $("#'.sanitize_title_with_dashes($first_id).'").val(this.value);
                return false;
            },
		minLength: 3,

	});


});


</script>';
    return $out;
}

             /**
 *
 * Returns person's names from $people_id
 *
 * @author  Andy Moyle
 * @param    $id
 * @return   string
 * @version  0.1
 *
 *
*/
function church_admin_get_person($id)
{

 global $wpdb;
 if(!ctype_digit($id))return $id;
    $row=$wpdb->get_row('SELECT first_name,middle_name,nickname,prefix,last_name FROM '.CA_PEO_TBL.' WHERE people_id="'.esc_sql($id).'"');
    if($row){
    			//build name
				$name=$row->first_name.' ';
					$middle_name=get_option('church_admin_use_middle_name');
					if(!empty($middle_name)&&!empty($row->middle_name))$name.=$row->middle_name.' ';
					$nickname=get_option('church_admin_use_nickname');
					if(!empty($nickname)&&!empty($row->nickname))$name.='('.$row->nickname.') ';
					$prefix=get_option('church_admin_use_prefix');
					if(!empty($prefix)&&!empty($row->prefix))		$name.=$row->prefix.' ';
					$name.=$row->last_name;
    return esc_html($name);
    }else{return FALSE;}
}
function church_admin_get_name_from_user($id)
{
             /**
 *
 * Returns person's names from user_id
 *
 * @author  Andy Moyle
 * @param    $id
 * @return   string
 * @version  0.1
 *
 *
*/
 global $wpdb;
 ;
    $row=$wpdb->get_row('SELECT first_name,middle_name,nickname,prefix,last_name FROM '.CA_PEO_TBL.' WHERE user_id="'.esc_sql($id).'"');

    if($row)
    {
    	//build name
		$name=$row->first_name.' ';
					$middle_name=get_option('church_admin_use_middle_name');
					if(!empty($middle_name)&&!empty($row->middle_name))$name.=$row->middle_name.' ';
					$nickname=get_option('church_admin_use_nickname');
					if(!empty($nickname)&&!empty($row->nickname))$name.='('.$row->nickname.') ';
					$prefix=get_option('church_admin_use_prefix');
					if(!empty($prefix)&&!empty($row->prefix))		$name.=$row->prefix.' ';
					$name.=$row->last_name;
    	return esc_html($name);
    }else{return FALSE;}
}
 /**
 *
 * Returns peoples names from serialized array
 *
 * @author  Andy Moyle
 * @param    $idArray
 * @return   string
 * @version  0.1
 *
 */
function church_admin_get_people($idArray)
{

    global $wpdb;
    $ids=maybe_unserialize($idArray);
    if(!is_array($ids))return $ids;
    if(!empty($ids))
    {
        $names=array();
        foreach($ids AS $key=>$id)
        {
            if(ctype_digit($id))
            {//is int
                $row=$wpdb->get_row('SELECT first_name,middle_name,nickname,prefix,last_name FROM '.CA_PEO_TBL.' WHERE people_id="'.esc_sql($id).'"');
                if(!empty($row))
                {
                	$name=$row->first_name.' ';
					$middle_name=get_option('church_admin_use_middle_name');
					if(!empty($middle_name)&&!empty($row->middle_name))$name.=$row->middle_name.' ';
					$nickname=get_option('church_admin_use_nickname');
					if(!empty($nickname)&&!empty($row->nickname))$name.='('.$row->nickname.') ';
					$prefix=get_option('church_admin_use_prefix');
					if(!empty($prefix)&&!empty($row->prefix))		$name.=$row->prefix.' ';
					$name.=$row->last_name;
                	$names[]=$name;
                }
            }//end is int
            else
            {//is text
                $names[]=$id;
            }//end is text
        }
        return implode(", ", array_filter($names));
    }
    else
    return " ";
}

function church_admin_get_people_id($name)
{
        /**
 *
 * Returns serialized array of people_id if $name is in DB
 *
 * @author  Andy Moyle
 * @param    $name
 * @return   serialized array
 * @version  0.1
 *
 */
    global $wpdb;
    $names=explode(',',$name);

    $people_ids=array();
    if(!empty($names))
    {
        foreach($names AS $key=>$value)
        {
			$value=trim($value);
            if(!empty($value))
            {//only look if a name stored!
                $sql='SELECT people_id FROM '.CA_PEO_TBL.' WHERE CONCAT_WS(" ",first_name,last_name) LIKE "'.esc_sql($value).'" OR CONCAT_WS(" ",first_name,prefix,last_name) LIKE "'.esc_sql($value).'" OR CONCAT_WS(" ",first_name,middle_name,prefix,last_name) LIKE "'.esc_sql($value).'" OR CONCAT_WS(" ",first_name,middle_name,last_name) LIKE "'.esc_sql($value).'" OR CONCAT_WS(" ",first_name,prefix,last_name) LIKE "'.esc_sql($value).'" OR  nickname LIKE "'.esc_sql($value).'" LIMIT 1';

                $result=$wpdb->get_var($sql);
                if($result){$people_ids[]=$result;}else{$people_ids[]=$value;}
            }
        }
    }
    return maybe_serialize(array_filter($people_ids));
}
function church_admin_get_user_id($name)
{
        /**
 *
 * Returns serialized array of user_id if $name is in DB
 *
 * @author  Andy Moyle
 * @param    $name
 * @return   serialized array
 * @version  0.1
 *
 */
    global $wpdb;
    $names=explode(',',$name);

    $user_ids=array();
    if(!empty($names))
    {
        foreach($names AS $key=>$value)
        {
			$value=trim($value);
            if(!empty($value))
            {//only look if a name stored!
                $sql='SELECT people_id FROM '.CA_PEO_TBL.' WHERE CONCAT_WS(" ",first_name,prefix,last_name) LIKE "'.esc_sql($value).'"OR CONCAT_WS(" ",first_name,middle_name,prefix,last_name) LIKE "'.esc_sql($value).'" OR CONCAT_WS(" ",first_name,middle_name,last_name) LIKE "'.esc_sql($value).'" OR CONCAT_WS(" ",first_name,prefix,last_name) LIKE "'.esc_sql($value).'" OR  nickname LIKE "'.esc_sql($value).'" LIMIT 1';
                $result=$wpdb->get_var($sql);
                if($result){$user_ids[]=$result;}else
				{
					echo '<p>'.esc_html($value).' is not stored by Church Admin as Wordpress User. ';
					$people_id=$wpdb->get_var('SELECT people_id FROM '.CA_PEO_TBL.' WHERE CONCAT_WS(" ",first_name,last_name) REGEXP "^'.esc_sql($value).'" LIMIT 1');
					if(!empty($people_id))echo'Please <a href="'.wp_nonce_url('admin.php?page=church_admin/index.php&amp;action=church_admin_edit_people&amp;people_id='.$people_id,'edit_people').'">edit</a> entry to connect/create site user account.';
					echo'</p>';
				}
            }
        }
    }
    if(!empty($user_ids)){ return maybe_serialize(array_filter($user_ids));}else{return NULL;}
}
function church_admin_get_one_id($name)
{
	global $wpdb;
	$sql='SELECT people_id FROM '.CA_PEO_TBL.' WHERE first_name LIKE "'.esc_sql($name).'" OR last_name LIKE "'.esc_sql($name).'" OR CONCAT_WS(" ",first_name,last_name) LIKE "'.esc_sql($name).'" OR CONCAT_WS(" ",first_name,prefix,last_name) LIKE "'.esc_sql($name).'" OR CONCAT_WS(" ",first_name,middle_name,prefix,last_name) LIKE "'.esc_sql($name).'" OR CONCAT_WS(" ",first_name,middle_name,last_name) LIKE "'.esc_sql($name).'" OR CONCAT_WS(" ",first_name,prefix,last_name) LIKE "'.esc_sql($name).'" OR  nickname LIKE "'.esc_sql($name).'" LIMIT 1';
    $result=$wpdb->get_var($sql);
	if(!empty($result)){return $result;}else{return $name;}
}


function church_admin_update_order($which='member_type')
{
    global $wpdb;
    if(isset($_POST['order']))
    {
        switch($which)
        {
			      case'facilities':$tb=CA_FAC_TBL;$field='facilities_order';$id='facility_id';break;
            case'member_type':$tb=CA_MTY_TBL;$field='member_type_order';$id='member_type_id';break;
            case'rota_settings':$tb=CA_RST_TBL;$field='rota_order';$id='rota_id';break;
            case'small_groups':$tb=CA_SMG_TBL;$field='smallgroup_order';$id='id';break;
			      case'people':$tb=CA_PEO_TBL;$field='people_order';$id='people_id';break;
            case'funnel':$tb=CA_FUN_TBL;$field='funnel_order';$id='funnel_id';break;
        }
        $order=explode(",",$_POST['order']);
        foreach($order AS $order=>$row_id)
        {
            $member_type_order++;
            $head=='';
            if($which=='people')
            {
            	if($order==0){$head=', head_of_household=1';}else{$head=', head_of_household=0';}
            }
            $sql='UPDATE '.$tb.' SET '.$field.'="'.esc_sql($order).'" '.$head.' WHERE '.$id.'="'.esc_sql($row_id).'"';
            church_admin_debug($sql);
            $wpdb->query($sql);
        }
    }
}
function church_admin_member_type_array()
{
    global $wpdb;
    $member_type=array();
    $results=$wpdb->get_results('SELECT * FROM '.CA_MTY_TBL.' ORDER BY member_type_order ASC');
    foreach($results AS $row)
    {
        $member_type[$row->member_type_id]=$row->member_type;
    }
    return($member_type);
}
function church_admin_kidswork_array()
{
    global $wpdb;
    $kidswork=array();
    $results=$wpdb->get_results('SELECT * FROM '.CA_KID_TBL.' ORDER BY youngest ASC');
    foreach($results AS $row)
    {
        $kidswork[$row->id]=$row->group_name;
    }
    return($kidswork);
}


/**
* This function deletes a person from a hope team
*
* @author     	andymoyle
* @param		$people_id,$meta_type
* @return		array
*
*/
function church_admin_delete_from_hope_team($people_id)
{
  global $wpdb;
  ;
  $wpdb->query('DELETE FROM '.CA_MET_TBL.' WHERE people_id="'.esc_sql($people_id).'" AND meta_type="hope_team" ');

}


function church_admin_get_hierarchy($ID)
{
		$rand=rand();
  		church_admin_leadership_hierarchy($ID,$rand);
    	$hierarchy=get_option('church_admin_leadership_hierarchy'.$rand);
    	delete_option('church_admin_leadership_hierarchy'.$rand);
    	return $hierarchy;
}

function church_admin_leadership_hierarchy($ID,$rand)
{
	global $wpdb;
	$hierarchy=get_option('church_admin_leadership_hierarchy'.$rand);
	if(empty($hierarchy)||(is_array($hierarchy)&&!in_array($ID,$hierarchy))){$hierarchy=array(1=>$ID);update_option('church_admin_leadership_hierarchy'.$rand,$hierarchy);}
	$sql='SELECT parentID FROM '.CA_MIN_TBL.' WHERE ID="'.esc_sql($ID).'"';

	$nextlevel=$wpdb->get_var($sql);
	if(empty($nextlevel))
	{

	 	return $hierarchy;
	}
	else
	{
		$hierarchy[]=(int)$nextlevel;

		update_option('church_admin_leadership_hierarchy'.$rand,$hierarchy);
		church_admin_leadership_hierarchy($nextlevel,$rand);
	}
}
/**
* This function updates a people meta
*
* @author     	andymoyle
* @param		$people_id,$meta_type,$ID
* @return		array
*
*/
function church_admin_update_people_meta($ID,$people_id,$meta_type='ministry',$meta_date=NULL)
{
  global $wpdb;
  if(empty($meta_date))$meta_date=date('Y-m-d');
 	$wpdb->show_errors();
  	$id=$wpdb->get_var('SELECT meta_id FROM '.CA_MET_TBL.' WHERE people_id="'.esc_sql($people_id).'" AND meta_type="'.esc_sql($meta_type).'" AND ID="'.esc_sql($ID).'" AND meta_date="'.esc_sql($meta_date).'"');
  	if(empty($id))
  	{
  		$sql='INSERT INTO '.CA_MET_TBL.' (people_id,ID,meta_type,meta_date) VALUES ("'.intval($people_id).'", "'.intval($ID).'", "'.esc_sql($meta_type).'", "'.esc_sql($meta_date).'" );';

  		$wpdb->query($sql);

  	}

}



/**
* This function produces an array of meta_id for people_id
*
* @author     	andymoyle
* @param		$people_id,$meta_type
* @return		FALSE or array
*
*/
function church_admin_get_people_meta($people_id,$meta_type='smallgroup'){
  global $wpdb;
  $out=array();

  $results=$wpdb->get_results('SELECT ID FROM '.CA_MET_TBL.' WHERE people_id="'.esc_sql($people_id).'" AND meta_type="'.esc_sql($meta_type).'"');
  if(empty($results)){return FALSE;}
  else
  {
  	foreach($results AS $row)$out[]=$row->ID;
  	return $out;
  }
}

function church_admin_people_meta($ID=NULL,$people_id=NULL,$meta_type=NULL)
{
	global $wpdb;
	$sql='SELECT a.*,b.* FROM '.CA_MET_TBL.' a ,'.CA_PEO_TBL.' b WHERE a.people_id=b.people_id AND ';
	$where=array();
	if(!empty($ID)) $where[]= 'a.ID="'.intval($ID).'" ';
	if(!empty($people_id))$where[]=' a.people_id="'.intval($people_id).'"';
	if(!empty($meta_type))$where[]=' a.meta_type="'.esc_sql($meta_type).'"';
	$query=$sql.implode(' AND ',$where);

	$results=$wpdb->get_results($query);
	return $results;
}

/**
* This function deletes a meta data for a given people_id or meta ID
*
* @author     	andymoyle
* @param		$people_id,$meta_type
* @return
*
*/
function church_admin_delete_people_meta($ID=NULL,$people_id,$meta_type=NULL)
{
	global $wpdb;
	if($ID){
		$wpdb->query('DELETE FROM '.CA_MET_TBL.' WHERE people_id="'.esc_sql($people_id).'" AND meta_type="'.esc_sql($meta_type).'" AND ID="'.esc_sql($ID).'"');
	}else{
		$wpdb->query('DELETE FROM '.CA_MET_TBL.' WHERE people_id="'.esc_sql($people_id).'" AND meta_type="'.esc_sql($meta_type).'"');
	}
}

function strip_only($str, $tags)
{
    //this functions strips some tages, but not all
    if(!is_array($tags)) {
        $tags = (strpos($str, '>') !== false ? explode('>', str_replace('<', '', $tags)) : array($tags));
        if(end($tags) == '') array_pop($tags);
    }
    foreach($tags as $tag) $str = preg_replace('#</?'.$tag.'[^>]*>#is', '', $str);
    return $str;
}

function checkDateFormat($date)
{
  //match the format of the date
  if (preg_match ("/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/", $date, $parts))
  {
    //check weather the date is valid of not
        if(checkdate($parts[2],$parts[3],$parts[1]))
          return true;
        else
         return false;
  }
  else
    return false;
}


function QueueEmail($to,$subject,$message,$copy=NULL,$from_name=NULL,$from_email=NULL,$attachment=NULL,$schedule=NULL)
{
    global $wpdb;

    $sqlsafe=array();
    $sqlsafe['to']=esc_sql($to);
    $sqlsafe['from_name']=esc_sql($from_name);
    $sqlsafe['from_email']=esc_sql($from_email);
    $sqlsafe['subject']=esc_sql($subject);
    $sqlsafe['message']=esc_sql($message);
    $sqlsafe['attachment']=esc_sql(maybe_serialize($attachment));
	$sqlsafe['schedule']=esc_sql($schedule);
    $sqlsafe['copy']=esc_sql(maybe_unserialize($copy));
    $sql='INSERT INTO '.CA_EMA_TBL.' (recipient,from_name,from_email,copy,subject,message,attachment,schedule)VALUES("'.$sqlsafe['to'].'","'.$sqlsafe['from_name'].'","'.$sqlsafe['from_email'].'","'.$sqlsafe['copy'].'","'.$sqlsafe['subject'].'","'.$sqlsafe['message'].'","'.$sqlsafe['attachment'].'","'.$sqlsafe['schedule'].'")';

	$result=$wpdb->query($sql);

    if($result) {return $wpdb->insert_id;}else{return FALSE;}
}

if(!function_exists('set_html_content_type')){function set_html_content_type() {return 'text/html';}}

function church_admin_plays($file_id)
{
	global $wpdb;
	$plays=$wpdb->get_var('SELECT plays FROM '.CA_FIL_TBL.' WHERE file_id="'.esc_sql($file_id).'"');
	return $plays;
}

function church_admin_dateCheck($date, $yearepsilon=5000)
{ // inputs format is "yyyy-mm-dd" ONLY !
if (count($datebits=explode('-',$date))!=3) return false;
$year = intval($datebits[0]);
$month = intval($datebits[1]);
$day = intval($datebits[2]);
if ((abs($year-date('Y'))>$yearepsilon) || // year outside given range
($month<1) || ($month>12) || ($day<1) ||
(($month==2) && ($day>28+(!($year%4))-(!($year%100))+(!($year%400)))) ||
($day>30+(($month>7)^($month&1)))) return false; // date out of range
if( checkdate($month,$day,$year)) {return ($year.'-'.sprintf("%02d", $month).'-'.sprintf("%02d", $day));}else{return FALSE;}
}

/**************************************************************************************************************************************************
*
*
*  Check if logged in user can do what is wanted
* param ID - ID of person about to be edited/deleted or ID of ministry
* admins can do anything
*
*
*
*
***************************************************************************************************************************************************/
function church_admin_user_can($ID,$meta_type='smallgroup')
{
	$can=FALSE;
	global $current_user;
	wp_get_current_user();
	$user_people_id=$wpdb->get_var('SELECT people_id FROM '.CA_PEO_TBL.' WHERE user_id="'.intval($current_user->ID).'"');

	//administrator
	if(current_user_can('manage_options')) return TRUE;

	//if current user is the passed ID
	if($user_people_id==$ID)return TRUE;

	if($meta_type=='smallgroup')
	{
		//check if $ID is in a group led or overseen
		$sgID=$wpdb->get_var('SELECT ID FROM '.CA_MET_TBL.' WHERE people_id="'.intval($ID).'" AND meta_key="smallgroup"');
		if(!empty($sgID))
		{
			$leaders=maybe_unserialize($wpdb->get_var('SELECT leadership FROM '.CA_SMG_TBL.' WHERE id="'.intval($sgID).'"'));
			if(is_array($leaders))
			{
				foreach($leaders AS $leaderlevel)
				{
					if(in_array($user_people_id,$leaderlevel)) return TRUE;
				}
			}
		}
	}
	else
	{//ministry
	//see if ministry has a parent
		$parentID=$wpdb->get_var('SELECT parentID FROM '.CA_MIN_TBL.' WHERE ID="'.intval($ID).'"');
		if(empty($parentID)) return FALSE;
		if(parent($ID)){return TRUE;}
		function parent($ID)
		{
			$check=$wpdb->get_var('SELECT meta_id FROM '.CA_MET_TBL.' WHERE ID="'.intval($ID).'" AND people_id="'.intval($user_people_id).'" AND meta_type="ministry"');
			if(!empty($check)) return TRUE;
			$next_level=$wpdb->get_var('SELECT parentID FROM '.CA_MIN_TBL.' WHERE ID="'.intval($parentID).'"');
			if(!empty($next_level))
			{
				if(parent($next_level)){ return TRUE;}else return FALSE;
			}
			else return FALSE;
		}
		//see if user is in that parent ministry

	}
	return FALSE;
}


function church_admin_adjust_brightness($hex, $steps)
{
    // Steps should be between -255 and 255. Negative = darker, positive = lighter
    $steps = max(-255, min(255, $steps));

    // Normalize into a six character long hex string
    $hex = str_replace('#', '', $hex);
    if (strlen($hex) == 3) {
        $hex = str_repeat(substr($hex,0,1), 2).str_repeat(substr($hex,1,1), 2).str_repeat(substr($hex,2,1), 2);
    }

    // Split into three parts: R, G and B
    $color_parts = str_split($hex, 2);
    $return = '#';

    foreach ($color_parts as $color) {
        $color   = hexdec($color); // Convert to decimal
        $color   = max(0,min(255,$color + $steps)); // Adjust color
        $return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT); // Make two char hex code
    }

    return $return;
}
 /**
 *
 * Replace rota entry
 *
 * @author  Andy Moyle
 * @param    $people_id,$date,$mtg_type,$service_id,$rota_task_id
 * @return   BOOL
 * @version  0.1
 *
 */
 function church_admin_update_rota_entry($rota_task_id,$date,$people_id,$mtg_type,$service_id)
 {
 	global $wpdb;
 	$table=CA_ROTA_TBL;
 	$data=array(
 			'rota_task_id'=>$rota_task_id,
 			'people_id'=>$people_id,
 			'mtg_type'=>$mtg_type,
 			'service_id'=>$service_id,
 			'rota_date'=>$date
 	);

 	$format=array(
 			'%d',
 			'%s',
 			'%s',
 			'%d',
 			'%s'
 	);

 	if(empty($rota_id))
 	{
 		$wpdb->insert($table,$data,$format);
 	}
 	else
 	{
 		$where=array('rota_id'=>$rota_id);
 		$wpdb->update( $table, $data, $where, $format  );

	}


 }
  /**
 *
 * Grab array of people_ids for particular ministry_id
 *
 * @author  Andy Moyle
 * @param    $ministry_id
 * @return   array($people_id=>$name)
 * @version  0.1
 *
 */
 function church_admin_ministry_people_array($ministry_id)
 {
 	global $wpdb;
 	$out=array();
 	$results=$wpdb->get_results('SELECT CONCAT_WS(" ",a.first_name,a.last_name) AS name, b.people_id AS people_id FROM '.CA_PEO_TBL.' a, '.CA_MET_TBL.' b WHERE a.people_id=b.people_id AND b.meta_type="ministry" AND b.ID="'.intval($ministry_id).'" ');
 	if(!empty($results))
 	{
 		foreach($results AS $row)$out[$row->people_id]=$row->name;
 	}

 	return $out;
 }
  /**
 *
 * Grab array of people_ids for particular rota_task_id and event
 *
 * @author  Andy Moyle
 * @param    $rota_date,$rota_taks_id,$service_id,$mtg_type
 * @return   array($people_id=>$name)
 * @version  0.1
 *
 */
 function church_admin_rota_people_array($rota_date,$rota_task_id,$service_id,$mtg_type)
 {
 	global $wpdb;
 	$out=array();
 	if(!empty($rota_date))$results=$wpdb->get_results('SELECT * FROM '.CA_ROTA_TBL.' WHERE rota_task_id="'.intval($rota_task_id).'" AND mtg_type="'.esc_sql($mtg_type).'" AND service_id="'.intval($service_id).'" AND rota_date="'.esc_sql($rota_date).'"');
 	if(!empty($results))
 	{
 		foreach($results AS $row)$out[$row->people_id]=church_admin_get_person($row->people_id);
 	}
 	return $out;
 }
   /**
 *
 * Grab comma separated list of people for particular rota_taks_id and event
 *
 * @author  Andy Moyle
 * @param    $rota_date,$rota_taks_id,$service_id,$mtg_type
 * @return   string
 * @version  0.1
 *
 */
 function church_admin_rota_people($rota_date,$rota_task_id,$service_id,$mtg_type)
 {
 	return implode(", ",church_admin_rota_people_array($rota_date,$rota_task_id,$service_id,$mtg_type));
 }
    /**
 *
 * Grab comma separated list of people for particular rota_taks_id and event
 *
 * @author  Andy Moyle
 * @param    $rota_date,$rota_taks_id,$service_id,$mtg_type
 * @return   string
 * @version  0.1
 *
 */
 function church_admin_rota_people_initials($rota_date,$rota_task_id,$service_id,$mtg_type)
 {
 	$people=church_admin_rota_people_array($rota_date,$rota_task_id,$service_id,$mtg_type);
 	$initials=array();
 	foreach($people AS $key=>$person)
 	{
 		$words = explode(" ", $person);
		$acronym = "";
		foreach ($words as $w) {
  			$acronym .= $w[0];
		}
		$initials[]=$acronym;
 	}
 	return implode(", ",$initials);
 }


/**
 *
 * Works out font size and orientation for data
 *
 * @author  Andy Moyle
 * @param    $lengths, $fontSize
 * @return   array(orientation,font_size,widths)
 * @version  0.1
 *
 */
function church_admin_pdf_settings($lengths,$fontSize=10)
{
	//M is max width letter and at 1pt Arial will take up 0.35mm approx, will allow 3mm either side
	$colWidth=array();
	foreach($lengths AS $key=>$length)$colWidth[$key]=($length*$fontSize*0.2)+6;
	$pdfSettings=array('font_size'=>$fontSize,'widths'=>$colWidth);
	//find total width and check it is less than width of page
	$tableWidth=array_sum($colWidth);
	church_admin_debug("Table Width: $tableWidth");
	$pdfSize=get_option('church_admin_pdf_size');

	switch($pdfSize)
	{
		case 'A4':
					if(($tableWidth)<190)$pdfSettings['orientation']='P';
					elseif($tableWidth<277)$pdfSettings['orientation']='L';
					else{return FALSE;}
		break;
		case 'Letter':
					if(($tableWidth)<195)$pdfSettings['orientation']='P';
					elseif($tableWidth<259)$pdfSettings['orientation']='L';
					else{return FALSE;}
		break;
		case 'Legal':
					if(($tableWidth)<200)$pdfSettings['orientation']='P';
					elseif($tableWidth<346)$pdfSettings['orientation']='L';
					else{return FALSE;}
		break;
	}

	return $pdfSettings;

}

     function church_admin_api_checker($url) {
        $curl = curl_init($url);

        //don't fetch the actual page, you only want to check the connection is ok
        curl_setopt($curl, CURLOPT_NOBODY, true);

        //do request
        $result = curl_exec($curl);

        $ret = false;

        //if request did not fail
        if ($result !== false) {
            //if request was ok, check response code
            $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
       		$ret=$statusCode;
        }
        curl_close($curl);

       return $statusCode;
    }
/**
 *
 * Page id of church_admin_register shortcode containing page
 *
 * @author  Andy Moyle
 * @param    $lengths, $fontSize
 * @return   array(orientation,font_size,widths)
 * @version  0.1
 *
 */
function church_admin_register_page_id()
{
	global $wpdb;
	$page_id=$wpdb->get_var('SELECT ID FROM '.$wpdb->posts.' WHERE post_content LIKE "%[church_admin_register]%" AND post_status="publish" LIMIT 1');
	if(!empty($page_id)){return intval($page_id);}else{return FALSE;}
}
 /**
 *
 * Page id of church_admin_unsubscribe shortcode containing page
 *
 * @author  Andy Moyle
 * @param    $lengths, $fontSize
 * @return   array(orientation,font_size,widths)
 * @version  0.1
 *
 */
function church_admin_unsubscribe_page_id()
{
	global $wpdb;
	$page_id=$wpdb->get_var('SELECT ID FROM '.$wpdb->posts.' WHERE post_content LIKE "%[church_admin_unsubscribe]%" AND post_status="publish" LIMIT 1');
	if(!empty($page_id)){return intval($page_id);}else{return FALSE;}
}

 /**
 *
 * Check whether person with peple_id is active or not
 *
 * @author  Andy Moyle
 * @param    $people_id
 * @return   BOOL
 * @version  0.1
 *
 */
function church_admin_deactivated_check($people_id)
{
	global $wpdb;
	$check=$wpdb->get_var('SELECT active FROM '.CA_PEO_TBL.' WHERE people_id="'.intval($people_id).'"');
	if($check)
	{
		return TRUE;
	}
	else
	{
		return FALSE;
	}
}
 /**
 *
 * Output JQuery to handle clicking on Activate/Deactivate
 *
 * @author  Andy Moyle
 * @param
 * @return   $out
 * @version  0.1
 *
 */
function church_admin_activate_script()
{	//jQuery for processing activate/deactivate peopl
		$nonce = wp_create_nonce("church_admin_people_activate");
		$out='

	<script type="text/javascript">
		jQuery(document).ready(function($) {
			$("body").on("click",".activate", function(){
				var id = this.id;

      			var data = {
				"action": "church_admin",
				"method":"people_activate",
				"people_id": id,
				"nonce": "'.$nonce.'"
				};

		// since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
		jQuery.post(ajaxurl, data, function(response) {

			if(response==1){
					$("#row"+id).removeClass("ca-deactivated");
					$("#"+id).html("Active ");
				}else{
					$("#row"+id).addClass("ca-deactivated");
					$("#"+id).html("Deactive ");
				}
		});
			});
		});
	</script>

	';
	return $out;
}

function church_admin_helper()
{


$out='
	<script>
		jQuery(document).ready(function($) {
			$("body").on("click"," .help", function(){
			var id=this.id;
			var message;
				switch(id)
				{
					case "active-message":
						alert("'.__('Click to change active status of person in directory','church-admin').'");
					break;


				}

			});
			});
	</script>';
return $out;


}

class ChurchAdminDateTime extends DateTime {

    public function returnAdd(DateInterval $interval)
    {
        $dt = clone $this;
        $dt->add($interval);
        return $dt;
    }

    public function returnSub(DateInterval $interval)
    {
        $dt = clone $this;
        $dt->sub($interval);
        return $dt;
    }

}


function church_admin_app_admin_notice() {
    global $wpdb;


        $sql='SELECT COUNT(*) FROM '.CA_PEO_TBL.' WHERE gdpr_reason IS NULL';

        $noncompliant=$wpdb->get_var($sql);

        if ( ! get_option('dismissed-church-admin-gdpr', FALSE ) )
        {
            // Added the class "notice-my-class" so jQuery pick it up and pass via AJAX,
            // and added "data-notice" attribute in order to track multiple / different notices
            // multiple dismissible notice states ?>
            <div class="updated notice notice-success notice-church-admin is-dismissible" data-notice="church-admin-gdpr" >
                <p><?php echo __("The church admin plugin is compliant with the UK &amp; EU General Data Protection regulations which came in to force in 25th May 2018. They are a common sense regulation of data privacy that requires permission to use/display people's personal data and so the principles are valid worldwide.",'church-admin');?> </p>
            <?php

            if($noncompliant>0)
            {
            	echo'<strong style="color:red;"> You have '.$noncompliant.' non compliant people records in your directory, where no confirmation of permission is stored.<br/> From 25th May email and sms sending will stop being sent to them. Please begin to update the records.</strong>';
				echo'<p><a href="'.wp_nonce_url('admin.php?page=church_admin/index.php&amp;action=gdpr-email&amp;tab=people','gdpr-email').'">'.__("Send GDPR email to everyone who isn't confirmed already",'church-admin').'</a></p>';
				echo'<p><a href="'.site_url('?ca_download=gdpr-pdf').'">'.__("Print GDPR forms for everone who isn't confirmed already",'church-admin').'</a></p>';
            }
            ?>
            </div>
            <script>
            // shorthand no-conflict safe document-ready function
  jQuery(function($) {
    // Hook into the "notice-my-class" class we added to the notice, so
    // Only listen to YOUR notices being dismissed
    $( document ).on( 'click', '.notice-church-admin ', function () {
        // Read the "data-notice" information to track which notice
        // is being dismissed and send it via AJAX
        var type = $( this ).closest( '.notice-church-admin' ).data( 'notice' );
        console.log(type);
        // Make an AJAX call
        // Since WP 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
        $.ajax( ajaxurl,
          {
            type: 'POST',
            data: {
              action: 'dismissed_notice_handler',

              type: type,
            }
          } );
      } );
  });
            </script>
        <?php
		}




}
/*********************
*
*
*	AJAX operations
*
***********************/
function church_admin_date()
{
	require_once(plugin_dir_path( __FILE__) .'../display/calendar.new.php');
	echo church_admin_display_day($_POST['date']);
	exit();
}


function church_admin_note_delete_callback() {

	check_admin_referer('church_admin_delete_note','nonce');
	global $wpdb;
	$sql='DELETE FROM '.CA_COM_TBL.'  WHERE comment_id="'.intval($_POST['note_id']).'"';
	$wpdb->query($sql);
	$sql='DELETE FROM '.CA_COM_TBL.'  WHERE parent_id="'.intval($_POST['note_id']).'"';
	$wpdb->query($sql);
	echo TRUE;
	exit();
}


function church_admin_ajax_notice_handler() {
    // Pick up the notice "type" - passed via jQuery (the "data-notice" attribute on the notice)

    // Store it in the options table
    switch($_POST['type'])
    {
    	case "church-admin-bible-version":update_option('dismissed-church-admin-bible-version',TRUE);break;
    	case "church-admin-app":update_option( 'dismissed-church-admin-app', TRUE );break;
    	case "church-admin-gdpr":update_option( 'dismissed-church-admin-gdpr', TRUE );break;
    }
}

function church_admin_unattach_user()
{

	global $wpdb;
	church_admin_debug(intval($_POST['people_id']));
	$wpdb->query('UPDATE '.CA_PEO_TBL.' SET user_id=NULL WHERE people_id="'.intval($_POST['people_id']).'"');
}
/**
 *
 * Ajax - returns json array with people's names
 * Used by fautoe
 * @author  Andy Moyle
 * @param    null
 * @return   json array
 * @version  0.1
 *
 */
function church_admin_ajax_people()
{

    global $wpdb;
    $names=explode(", ", $_GET['term']);//put passed var into array
    $name=esc_sql(stripslashes(trim(end($names))));//grabs final value for search


   $sql='SELECT CONCAT_WS(" ",first_name,prefix, last_name) AS name FROM '.CA_PEO_TBL.' WHERE CONCAT_WS(" ",first_name,last_name) LIKE "%'.esc_sql($name).'%" OR CONCAT_WS(" ",first_name,prefix,last_name) LIKE "%'.esc_sql($name).'%" OR CONCAT_WS(" ",first_name,middle_name,prefix,last_name) LIKE "%'.esc_sql($name).'%" OR CONCAT_WS(" ",first_name,middle_name,last_name) LIKE "%'.esc_sql($name).'%" OR CONCAT_WS(" ",first_name,prefix,last_name) LIKE "%'.esc_sql($name).'%" OR  nickname LIKE "%'.esc_sql($name).'%" ';

    $result=$wpdb->get_results($sql);

    if($result)
    {
        $people=array();
        foreach($result AS $row)
        {
            $people[]=array('name'=>$row->name);
        }

        //echo JSON to page

    $response =json_encode($people);

    echo $response;
    }
    exit();
}


function church_admin_mp3_plays() {
	$nonce = $_POST['data']['security'];
 	if ( ! wp_verify_nonce( $nonce, 'church_admin_mp3_play' ) )die('busted');

	global $wpdb;
	$file_id = esc_sql($_POST['data']['file_id']);
	$sql='UPDATE '.CA_FIL_TBL.' SET plays = plays+1 WHERE file_id = "'.$file_id.'"';
	$wpdb->query($sql);
	$plays=$wpdb->get_var('SELECT plays FROM '.CA_FIL_TBL.' WHERE file_id = "'.$file_id.'"');

	echo $plays;
	die();
}

function church_admin_username_check()
{
	check_admin_referer('church_admin_username_check','nonce');

	if(username_exists(stripslashes($_POST['user_name']))){echo'<span class="dashicons dashicons-no" style="color:red"></span>';}else{echo'<span style="color:green" class="dashicons dashicons-yes"></span>';}
	exit();
}

function church_admin_filter_callback() {

	//check_admin_referer('church_admin_filter','nonce');

	require_once(plugin_dir_path(__FILE__).'filter.php');
	church_admin_debug("callback");

	church_admin_filter_process();

	exit();
}

function church_admin_filter_email_callback() {

	check_admin_referer('church_admin_filter','nonce');
	require_once(plugin_dir_path(__FILE__).'filter.php');
	echo church_admin_filter_email_count($_POST['type']);
	exit();
}




function church_admin_people_activate_callback() {

	check_admin_referer('church_admin_people_activate','nonce');
	global $wpdb;
	$sql='UPDATE '.CA_PEO_TBL.' SET active = !active WHERE people_id="'.intval($_POST['people_id']).'"';

	$wpdb->query($sql);
	$status=$wpdb->get_var('SELECT active FROM '.CA_PEO_TBL.' WHERE people_id="'.intval($_POST['people_id']).'"');
	echo $status;
	exit();
}

function church_admin_sites_array()
{
	global $wpdb;
	$sites=array();
	$results=$wpdb->get_results('SELECT * FROM '.CA_SIT_TBL);
	if(!empty($results)	)
	{
		foreach($results AS $row)
		{
			$sites[intval($row->site_id)]=esc_html($row->venue);
		}
	}
	return $sites;
}
function church_admin_ministries_array()
{
	global $wpdb;
	$ministries=array();
	$results=$wpdb->get_results('SELECT * FROM '.CA_MIN_TBL);
	if(!empty($results))
	{
		foreach($results AS $row)$ministries[intval($row->ID)]=esc_html($row->ministry);
	}
	return $ministries;
}

function church_admin_hope_teams_array()
{
	global $wpdb;
	$hope_team=array();
	$results=$wpdb->get_results('SELECT * FROM '.CA_HOP_TBL);
	if(!empty($results))
	{
		foreach($results AS $row)$hope_team[intval($row->hope_team_id)]=esc_html($row->job);
	}
	return $hope_team;
}
function church_admin_small_groups_array()
{
	global $wpdb;
	$small_groups=array();
	$results=$wpdb->get_results('SELECT * FROM '.CA_SMG_TBL);
	if(!empty($results))
	{
		foreach($results AS $row)$small_groups[intval($row->id)]=esc_html($row->group_name);
	}
	return $small_groups;
}

function church_admin_encode($word) {

    $word = str_replace("@","%40",$word);
    $word = str_replace("`","%60",$word);
    $word = str_replace("¢","%A2",$word);
    $word = str_replace("£","%A3",$word);
    $word = str_replace("¥","%A5",$word);
    $word = str_replace("|","%A6",$word);
    $word = str_replace("«","%AB",$word);
    $word = str_replace("¬","%AC",$word);
    $word = str_replace("¯","%AD",$word);
    $word = str_replace("º","%B0",$word);
    $word = str_replace("±","%B1",$word);
    $word = str_replace("ª","%B2",$word);
    $word = str_replace("µ","%B5",$word);
    $word = str_replace("»","%BB",$word);
    $word = str_replace("¼","%BC",$word);
    $word = str_replace("½","%BD",$word);
    $word = str_replace("¿","%BF",$word);
    $word = str_replace("À","%C0",$word);
    $word = str_replace("Á","%C1",$word);
    $word = str_replace("Â","%C2",$word);
    $word = str_replace("Ã","%C3",$word);
    $word = str_replace("Ä","%C4",$word);
    $word = str_replace("Å","%C5",$word);
    $word = str_replace("Æ","%C6",$word);
    $word = str_replace("Ç","%C7",$word);
    $word = str_replace("È","%C8",$word);
    $word = str_replace("É","%C9",$word);
    $word = str_replace("Ê","%CA",$word);
    $word = str_replace("Ë","%CB",$word);
    $word = str_replace("Ì","%CC",$word);
    $word = str_replace("Í","%CD",$word);
    $word = str_replace("Î","%CE",$word);
    $word = str_replace("Ï","%CF",$word);
    $word = str_replace("Ð","%D0",$word);
    $word = str_replace("Ñ","%D1",$word);
    $word = str_replace("Ò","%D2",$word);
    $word = str_replace("Ó","%D3",$word);
    $word = str_replace("Ô","%D4",$word);
    $word = str_replace("Õ","%D5",$word);
    $word = str_replace("Ö","%D6",$word);
    $word = str_replace("Ø","%D8",$word);
    $word = str_replace("Ù","%D9",$word);
    $word = str_replace("Ú","%DA",$word);
    $word = str_replace("Û","%DB",$word);
    $word = str_replace("Ü","%DC",$word);
    $word = str_replace("Ý","%DD",$word);
    $word = str_replace("Þ","%DE",$word);
    $word = str_replace("ß","%DF",$word);
    $word = str_replace("à","%E0",$word);
    $word = str_replace("á","%E1",$word);
    $word = str_replace("â","%E2",$word);
    $word = str_replace("ã","%E3",$word);
    $word = str_replace("ä","%E4",$word);
    $word = str_replace("å","%E5",$word);
    $word = str_replace("æ","%E6",$word);
    $word = str_replace("ç","%E7",$word);
    $word = str_replace("è","%E8",$word);
    $word = str_replace("é","%E9",$word);
    $word = str_replace("ê","%EA",$word);
    $word = str_replace("ë","%EB",$word);
    $word = str_replace("ì","%EC",$word);
    $word = str_replace("í","%ED",$word);
    $word = str_replace("î","%EE",$word);
    $word = str_replace("ï","%EF",$word);
    $word = str_replace("ð","%F0",$word);
    $word = str_replace("ñ","%F1",$word);
    $word = str_replace("ò","%F2",$word);
    $word = str_replace("ó","%F3",$word);
    $word = str_replace("ô","%F4",$word);
    $word = str_replace("õ","%F5",$word);
    $word = str_replace("ö","%F6",$word);
    $word = str_replace("÷","%F7",$word);
    $word = str_replace("ø","%F8",$word);
    $word = str_replace("ù","%F9",$word);
    $word = str_replace("ú","%FA",$word);
    $word = str_replace("û","%FB",$word);
    $word = str_replace("ü","%FC",$word);
    $word = str_replace("ý","%FD",$word);
    $word = str_replace("þ","%FE",$word);
    $word = str_replace("ÿ","%FF",$word);
    return $word;
}


function church_admin_in_array_r($needle, $haystack, $strict = false) {
   if(!empty($haystack)&&is_array($haystack))
   {
   	foreach ($haystack as $item)
   		{
    	    if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && church_admin_in_array_r($needle, $item, $strict)))
    	    {
        	    return true;
        	}
    	}
	}
    return false;
}

function church_admin_user_id_exists($user){

    global $wpdb;

    $count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $wpdb->users WHERE ID = %d", $user));

    if($count == 1){ return true; }else{ return false; }

}


function ca_date_link($year,$month,$day,$legend=FALSE,$class=NULL)
{
	if(empty($legend))$legend=$day;
	if(!empty($class)){$class=' class="'.$class.'"';}else{$class='';}
	if(is_admin()){$link='admin.php?page=church_admin/index.php&amp;action=calendar&amp;tab=calendar';}else{$link='';}
	$out='<form action="'.$link.'" method="POST">
	<input type="hidden" name="start_date" value="'.esc_html($year.'-'.$month.'-'.sprintf('%02d', $day)).'"/><button '.$class.'>'.esc_html($legend).'</button></form>';
	return $out;

}



function church_admin_scaled_image_path($attachment_id, $size = 'thumbnail') {
    $file = get_attached_file($attachment_id, true);
    if (empty($size) || $size === 'full') {
        // for the original size get_attached_file is fine
        return realpath($file);
    }
    if (! wp_attachment_is_image($attachment_id) ) {
        return false; // the id is not referring to a media
    }
    $info = image_get_intermediate_size($attachment_id, $size);
    if (!is_array($info) || ! isset($info['file'])) {
        return false; // probably a bad size argument
    }

    return realpath(str_replace(wp_basename($file), $info['file'], $file));
}

function church_admin_detect_runtime_issues()
{
    global $wp_version;
    $error=array();
      if  (!in_array  ('curl', get_loaded_extensions()))
      {
        $error['curl']=__('cURL is not enabled on your server, please contact your hosting company to get it enabled','church-admin');
      }
      if (version_compare(phpversion(), '5.3.10', '<'))
      {
        $error['php']=__('Your PHP version is low and therefore is not safe and lacks some features needed by Wordpress and Church Admin','church-admin');
      }
      if ( version_compare( $wp_version, '4.0', '<=' ) )
      {
        $error['wordpress']=__('Your Wordpress version is very out of date. Please update now','church-admin');
      }
      //SPIT OUT ERRORS IF NEEDED
      if(!empty($error))
      {
        echo'<div class="notice notice-warning"><h2>'.__('Issues detected by Church Admin plugin','church-admin').'</h2><p><strong>'.implode("<br/>",$error).'</strong></div>';

      }


}
function church_admin_refresh_rolling_average()
{
	global $wpdb;
	$results=$wpdb->get_results('SELECT * FROM '.CA_ATT_TBL);
	if(!empty($results))
	{
		foreach($results AS $row)
		{
			$avesql='SELECT FORMAT(AVG(adults),0) AS rolling_adults,FORMAT(AVG(children),0) AS rolling_children FROM '.CA_ATT_TBL.' WHERE `mtg_type`="'.esc_sql($row->mtg_type).'" AND `service_id`="'.esc_sql($row->service_id).'" AND `date` >= DATE_SUB("'.esc_sql($row->date).'",INTERVAL 52 WEEK) AND `date`<= "'.esc_sql($row->date).'"';
    		$averow=$wpdb->get_row($avesql);
    		$wpdb->query('UPDATE '.CA_ATT_TBL.' SET rolling_adults= "'.intval($averow->rolling_adults).'",rolling_children= "'.intval($averow->rolling_children).'" WHERE attendance_id="'.intval($row->attendance_id).'"');
		}
	}

}

function church_admin_whosin_kidswork_array()
{
		global $wpdb;
		$kidswork=array();
		//select GROUPS

			$results=$wpdb->get_results('SELECT a.*,a.id AS kidswork_id, b.ministry FROM '.CA_KID_TBL.' a  LEFT JOIN '.CA_MIN_TBL.' b ON a.department_id=b.ID ORDER BY youngest DESC');
			if(!empty($results))
			{
					foreach($results AS $row)
					{
							$kidswork[$row->kidswork_id]=array('name'=>$row->group_name,'youngest'=>$row->youngest,'oldest'=>$row->oldest);
							//get kids in that group
							$sql='SELECT people_id,household_id,kidswork_override FROM '.CA_PEO_TBL.' WHERE  (kidswork_override="'.esc_sql($row->id).'" OR ((date_of_birth<"'.$row->youngest.'" AND date_of_birth>"'.$row->oldest.'") AND kidswork_override=0 ))';

							$kids=$wpdb->get_results($sql);
							if(!empty($kids))
							{
								foreach($kids AS $kid)
								{
										//get parents of that kid
										$parents=array();
										$parents_result=$wpdb->get_results('SELECT people_id FROM '.CA_PEO_TBL.' WHERE household_id="'.intval($kid->household_id).'" AND people_type_id=1');
										if(!empty($parents_result))
										{
											foreach($parents_result AS $parent)
											{
													$parents[]=$parent->people_id;
											}
										}
										$kidswork[$row->kidswork_id]['children'][]=array('people_id'=>intval($kid->people_id),'household_id'=>intval($kid->household_id),'parents'=>$parents);

								}
							}
					}
				}

				return $kidswork;

}
function church_admin_get_kids_groups($people_id)
{
  //this function finds the kids work groups a parent has children in.
    $groups=array();//array of groups people_id is a parent of a child in that group
    $kidsGroups=church_admin_whosin_kidswork_array();
    if(!empty($kidsGroups))
    {
      foreach($kidsGroups AS $group_id=>$kidsGroup)
      {
          foreach($kidsGroup['children'] AS $kids)
          {
            if(in_array($people_id,$kids['parents']))$groups[]=$group_id;
          }
      }
    }
    church_admin_debug("people_id: $people_id is a parent of kids in these groups \r\n".print_r($groups,TRUE));
    return $groups;
}
/**
 *
 * Returns member level of current user
 *
 * @author  Andy Moyle
 * @param    $member_type_id Comma separated ids
 * @return
 * @version  0.1
 *
 */
 function church_admin_user_member_level($member_type_id)
 {
   global $wpdb,$current_user;
   wp_get_current_user();
   $permission=FALSE;
   $member_type_ids=explode(',',$member_type_id);
   if(is_user_logged_in())
   {
     $member_type_id=$wpdb->get_var('SELECT member_type_id FROM '.CA_PEO_TBL.' WHERE user_id="'.esc_sql($current_user->ID).'"');
     if(!empty($member_type_id) && is_array($member_type_ids)&& in_array($member_type_id,$member_type_ids)){$permission=TRUE;}
   }
   return $permission;
 }
