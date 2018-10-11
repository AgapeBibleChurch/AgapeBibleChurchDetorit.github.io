<?php

if ( ! defined( 'ABSPATH' ) ) exit('You need Jesus!'); // Exit if accessed directly

function church_admin_frontend_directory($member_type_id=NULL,$map=0,$photo=0,$api_key=NULL,$kids=TRUE,$site_id=0,$updateable=1,$first_initial=0)
{
	$out=get_option('church-admin-directory-output');
	if(empty($out))
	{
  		global $wpdb;
  		$wpdb->show_errors();

  		//set up variables
  		$api_key=get_option('church_admin_google_api_key');
  		$out='';
  		$time_start = microtime(true);
  		$lettersOutput='';
  		$addresEntry='';
 	 	/**************************************************************************
		*
 	 	*	Grab people head of household ordered into a multi-dimensional array
  		*	1st key is First Letter
  		*	2nd key is order key
  		*	3rd array is household data
  		**************************************************************************/
  		$directory=array();
  		/**************************************************************************
		*
		* Build Query to get household_id of relevant people in alphabetic order
		*
		**************************************************************************/
  		$memb_sql='';
  		$membsql=$sitesql=array();
  		if($member_type_id=="#"||empty($member_type_id)){$memb_sql="";}
  		elseif($member_type_id!="")
  		{
  			$memb=explode(',',$member_type_id);
      		foreach($memb AS $key=>$value){if(ctype_digit($value))  $membsql[]='a.member_type_id='.$value;}
      		if(!empty($membsql)) {$memb_sql=' ('.implode(' || ',$membsql).')';}
		}
		$site_sql='';
		if($site_id!=0)
  		{
  			$sites=explode(',',$site_id);
    	  	foreach($sites AS $key=>$value){if(ctype_digit($value))  $sitesql[]='site_id='.$value;}
    	  	if(!empty($sitesql)) {$site_sql=' ('.implode(' || ',$sitesql).')';}
		}

		$sql='SELECT UPPER(LEFT(a.last_name,1)) AS letter,a.*, a.household_id,b.* FROM '.CA_PEO_TBL.' a, '.CA_HOU_TBL.' b WHERE b.privacy=0 AND a.household_id=b.household_id AND head_of_household=1  ';
		if(!empty($memb_sql)||!empty($site_sql)) $sql.=' AND ';
		$sql.=$memb_sql;
		if(!empty($memb_sql)&&!empty($site_sql))$sql.=' AND ';
		$sql.=$site_sql;
		$sql.='   ORDER BY a.last_name ASC';
		church_admin_debug($sql);
  		$results=$wpdb->get_results($sql);
  		foreach($results AS $row)
  		{
  			if(ctype_alpha($row->letter))
  			{
  				$directory[$row->letter][]=church_admin_people_data($row->household_id);
   			}
  		}



  		/**************************************************************************
		*
		* 	Build Output
  		*
  		**************************************************************************/

  		if(!empty($directory))
  		{
  			$i=0;

  			foreach($directory AS $letter=>$data)
  			{
  				$householdIndex=0;
  				if($i==0){$highlighted="church-admin-highlighted";$firstData=$data;}else{$highlighted='';}
  				$lettersOutput.='<div class="church-admin-letter  '.$highlighted.'" id="'.esc_html($letter).'" data-firstid="'.intval($data[0]['household_id']).'"><span class="church-admin-item">'.esc_html($letter).'</span></div><div class="letterNames letter-'.$letter.'" >';

  				foreach($data AS $household)
  				{
  					if($i==0){$style='style="display:block"';}else{$style='style="display:none"';}
  					if($householdIndex==0){$highlightedName="church-admin-highlighted-name";}else{$highlightedName='';}
  					$firstInitial = "";
  					if($household['first_name'] && $first_initial == 1) {
  						$firstInitial = ", ";
  						if(strlen($household['first_name'])>0) {
  							$firstInitial = $firstInitial.substr($household['first_name'],0,1);
  						}
  						else {
  							$firstInitial = $firstInitial.$household['first_name'];
  						}
  					}
  					$lettersOutput.='<div '.$style.' class="church-admin-directory-name ca-names letter-'.$letter.' '.$highlightedName.'" id="index'.$householdIndex.'" data-id="'.intval($household['household_id']).'" style="display:none"><span class="church-admin-name-item">'.$household['last_name'].$firstInitial.'</span></div>';
  					$householdIndex++;
  				}
  				$lettersOutput.='</div>';
  				if($i==0)$directoryEntry=church_admin_formatted_household($data[0],$map,$updateable,$photo);
  				$i++;

 	 		}


	  	}

 	 	$out.="<div class=\"church-admin-new-directory\">\r\n";
 	 	$out.="<div class=\"church-admin-letters\">\r\n";
 	 	$out.=$lettersOutput;
 	 	$out.="</div><!-- .church-admin-letters -->\r\n";
	  	$out.="<div class=\"church-admin-address-entry\">\r\n";
 	 	if(!empty($directoryEntry))$out.=$directoryEntry;
 	 	$out.="</div><!-- .church-admin-address-entry -->\r\n";
  		$out.="</div><!-- .church-admin-new-directory -->\r\n";

		$time_end = microtime(true);
		$time = $time_end - $time_start;
		$date=date(get_option('date_format').' '.get_option('time_format'));
		if(church_admin_level_check('Directory')){$out.= sprintf(__('Directory took %1$s seconds to produce on %2$s','church-admin'),round($time,4),$date).'</span>';}
		else {
			$out.= sprintf(__('Directory last produced on %1$s','church-admin'),$date).'</span>';
		}
		update_option('church-admin-directory-output',$out);
	}
	//jQuery AJAX magic  needs to be fresh every time for nonce
	$output=$out;

	$nonce=wp_create_nonce( "show-person" );
  	$output.='<script>jQuery(document).ready(function($) {$(".church-admin-letter").click(function(){$(".letterNames").hide();var id=$(this).attr("id");$(".letter-"+id).show();var first_id=$(this).data("firstid");var data = {"action":  "church_admin","method": "show-person",security: "'.$nonce.'","map":"'.$map.'","photo":"'.$photo.'","updateable":"'.$updateable.'","id":first_id};$.ajax({url: ajaxurl,type: "post",data:data,success: function( response ){$(".church-admin-address-entry").html(response);},});});$(".church-admin-directory-name").click(function(){$(".church-admin-directory-name").removeClass("church-admin-highlighted-name");var household_id=$(this).data("id");$(this).addClass("church-admin-highlighted-name");var data = {"action":  "church_admin","method": "show-person",security: "'.$nonce.'","map":"'.$map.'","updateable":"'.$updateable.'","photo":"'.$photo.'","id":household_id};$.ajax({url: ajaxurl,type: "post",data:data,success: function( response ) {$(".church-admin-address-entry").html(response);},});});});</script>';
	return $output;
}


function church_admin_people_data($household_id)
{
	global $wpdb;
	$peopleResult=$first_names=$last_names=$directory_names=$adults=$children=array();
	$directory=$wpdb->get_row('SELECT * FROM '.CA_HOU_TBL.' WHERE household_id="'.intval($household_id).'"','ARRAY_A');
	$sql='SELECT * FROM '.CA_PEO_TBL.' WHERE household_id="'.intval($household_id).'" ORDER BY people_order';
  	$peopleResult=$wpdb->get_results($sql, ARRAY_A);

  	if(!empty($peopleResult))
  	{

  		foreach($peopleResult AS $row)
  		{
  			if($row['head_of_household']==1)$directory['last_name']=$row['last_name'];
  			if($row['people_type_id']==1)
  			{
  				$first_names[]=$row['first_name'];
					$last_names[]=implode(" ",array_filter(array($row['prefix'],$row['last_name'])));
					if(!empty($row['nickname'])){$nickname='('.$row['nickname'].')';}else{$nickname="";}
  				$row['name']=implode(" ",array_filter(array($row['first_name'],$row['middle_name'],$nickname,$row['prefix'],$row['last_name'])));
  				$adults[]=$row;
  			}
  			else
  			{
					if(!empty($row['nickname'])){$nickname='('.$row['nickname'].')';}else{$nickname="";}
  				$row['name']=implode(" ",array_filter(array($row['first_name'],$row['middle_name'],$nickname,$row['prefix'],$row['last_name'])));
  				$children[]=$row;
  			}
  		}

  		if(count($last_names)==1)
  		{
  			$directory['directory_name']=$first_names[0].' '.$last_names[0];
  		}
  		elseif(count($last_names) != count(array_unique($last_names)))
  		{//same last names
  			$directory['directory_name']=implode(" &amp; ",$first_names).' '.end($last_names);
  		}
  		else
  		{//different last names
  			for($x=0;$x<count($last_names);$x++)$directory_names[]=$first_names[$x].' '.$last_names[$x];

  			$directory['directory_name']=implode(" &amp; ",$directory_names);

  		}
  		if(empty($directory['last_name']))$directory['last_name']=$peopleResult[0]['last_name'];//no head of household set
  		$directory['first_name'] = $peopleResult[0]['first_name'];
  		$directory['adults']=$adults;
  		$directory['children']=$children;
  	}

  	return $directory;
}

function church_admin_formatted_household($data,$map=0,$updateable,$photo=1)
{
	$out='';
	$out.='<h2 class="church-admin-address-title ca-names">'.esc_html($data['directory_name']).'</h2>';
	/**************************************************************************
	*
	* 	Image and Map
  	*
  	**************************************************************************/

	$out.='<div class="church-admin-household-image">';
	if(!empty($data['attachment_id'])&&$photo)
	{
		$out.=wp_get_attachment_image( $data['attachment_id'],'medium','',array('class'=>'') );
		$out.='<br/>';
	}
	$api_key=get_option('church_admin_google_api_key');
	if(!empty($api_key)&&!empty($map)&&!empty($data['lng'])&!empty($data['address']))
	{
		$api='';

			if(!empty($api_key))$api='key='.$api_key;
			$url='https://maps.google.com/maps/api/staticmap?'.$api.'&center='.$data['lat'].','.$data['lng'].$api.'&zoom=15&markers='.$data['lat'].','.$data['lng'].'&size=300x225';

			$map_url=esc_url($url);


			$out.='<a href="'.esc_url('https://maps.google.com/maps?q='.$data['lat'].','.$data['lng'].'&amp;t=m&amp;z=16').'" style="margin-top:5px;"><img src="'.$map_url.'" height="225" width="300" alt="Map"/></a>'."\r\n\t";

	}
	$out.='</div><!--church_admin_address_image-->'."\r\n\t";
	/**************************************************************************
	*
	* 	Name & Address
  	*
  	**************************************************************************/

	$out.='<div class="church-admin-address-details">';
	if(!empty($data['address']))$out.='<div><label>'.__('Address','church-admin').':</label><span class="ca-addresses"> '.esc_html($data['address']).'<span></div>';
	if(!empty($data['phone']))$out.='<div><label>'.__('Phone','church-admin').':</label><span class="ca-mobile">  '.esc_html($data['phone']).'</span></div>';
	$out.='</div>';

		//there are children so give a header for adults
		$out.='<h3>'.__('Adults','church-admin').'</h3>';

	/**************************************************************************
	*
	* 	Adults
  	*
  	**************************************************************************/
	foreach($data['adults'] AS $adult)
	{
		$buildAdult=$adultName='';
		if(!empty($adult['attachment_id'])&& $photo)
		{
			$adultName.=wp_get_attachment_image( $adult['attachment_id'],'thumbnail','',array('class'=>'church-admin-person-image') );
		}
		$buildAdult.='<p class="ca-names"><strong>'.esc_html($adult['name']).'</strong><br/>';
		if(!empty($adult['email']))$buildAdult.='<label>'.__('Email','church-admin').': </label><a class="ca-email" href="'.esc_url('mailto:'.$adult['email']).'">'.esc_html($adult['email']).'</a><br/>';
		if(!empty($adult['mobile']))$buildAdult.='<label>'.__('Cell','church-admin').': </label><a class="ca-mobile" href="call:'.esc_html($adult['mobile']).'">'.esc_html($adult['mobile']).'</a><br/>';
		$social='';
		if(!empty($adult['facebook']))$social.='<a href="https://www.facebook.com/'.esc_html($adult['facebook']).'"><i class="fab fa-facebook-square"></i></a> ';
		if(!empty($adult['twitter']))$social.='<a href="https://www.twitter.com/'.esc_html($adult['twitter']).'"><i class="fab fa-twitter-square"></i></a> ';
		if(!empty($adult['instagram']))$social.='<a href="https://www.instagram.com/'.esc_html($adult['instagram']).'"><i class="fab fa-instagram"></i></a> ';
		if(!empty($social))$buildAdult.=$social.'<br/>';
		if(!empty($buildAdult))$out.=$adultName.$buildAdult.'</p><br style="clear:left"/>';
	}
	/**************************************************************************
	*
	* 	Children
  	*
  	**************************************************************************/

	if(!empty($data['children']))
	{
		$out.='<h3>'.__('Children','church-admin').'</h3>';
		foreach($data['children'] AS $child)
		{
			if(!empty($child['attachment_id']))
			{
				$out.=wp_get_attachment_image( $child['attachment_id'],'thumbnail','',array('class'=>'church-admin-person-image') );
			}
			$out.='<p class="ca-names"><strong>'.esc_html($child['name']).'</strong><br/>';
			if(!empty($child['email']))$out.='<label>'.__('Email','church-admin').': </label><a  class="ca-email" href="'.esc_url('mailto:'.$child['email']).'">'.esc_html($child['email']).'</a><br/>';
			if(!empty($child['mobile']))$out.='<label>'.__('Cell','church-admin').': </label><a  class="ca-mobile" href="call:'.esc_html($child['mobile']).'">'.esc_html($child['mobile']).'</a><br/>';
			$social='';
			if(!empty($child['facebook']))$social.='<a href="https://www.facebook.com/'.esc_html($child['facebook']).'"><i class="fa fa-3x fa-facebook-square"></i></a> ';
			if(!empty($child['twitter']))$social.='<a href="https://www.twitter.com/'.esc_html($child['twitter']).'"><i class="fa fa-3x fa-twitter-square"></i></a> ';
			if(!empty($child['instagram']))$social.='<a href="https://www.instagram.com/'.esc_html($child['instagram']).'"><i class="fa fa-3x fa-instagram"></i></a> ';
			if(!empty($social))$out.=$social.'<br/>';
			$out.='<br style="clear:left;"/></p>';//added clear float 2018-04-09
		}
	}
	/**************************************************************************
	*
	* 	Edit stuff
  	*
  	**************************************************************************/

	if($updateable)
		{

				$page_id=church_admin_register_page_id();
				if(!empty($page_id))
				{
				$out.='<p>&nbsp;<a title="'.__('Edit Entry','church-admin').'" href="'.esc_url( add_query_arg( 'household_id',$data['household_id'] ,get_permalink($page_id) )).'"><i class="fa fa-2x fa-pencil-square" aria-hidden="true"></i>'.__('Edit Entry','church-admin').'</a>';
				}else
				{
					$out.='<p>&nbsp;<a title="'.__('Edit Entry','church-admin').'" href="'.admin_url().'admin.php?page=church_admin/index.php&amp;action=display_household&amp;household_id='.$data['household_id'].'"><i class="fa fa-2x fa-pencil-square" aria-hidden="true"></i>'.__('Edit Entry','church-admin').'</a>';
				}
		}

		$out.='<span> <a title="'.__('Download Vcard','church-admin').'" href="'.home_url().'/?ca_download=vcf&amp;vcf='.wp_create_nonce($data['household_id']).'&amp;id='.$data['household_id'].'"><i class="fa fa-download fa-2x"></i></a></span>  Last Updated: '.mysql2date(get_option('date_format'),$data['ts']).'</p>'."\r\n\t".'</div><!--church_admin_vcard-->'."\r\n";
	return $out;

}
