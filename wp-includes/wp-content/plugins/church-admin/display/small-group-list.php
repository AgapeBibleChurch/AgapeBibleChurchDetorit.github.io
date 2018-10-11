<?php
function church_admin_small_group_list($map=1,$zoom=13)
{
	global $wpdb,$wp_locale;

	//show small groups

	$out='';

		$row=$wpdb->get_row('SELECT AVG(lat) AS lat,AVG(lng) AS lng FROM '.CA_SIT_TBL);
		if(!empty($row)&& $map==1)
		{

			$out.='<script type="text/javascript">var xml_url="'.site_url().'/?ca_download=small-group-xml&small-group-xml='.wp_create_nonce('small-group-xml').'";';
			if(!empty($row->lat)){$out.=' var lat='.esc_html($row->lat).';';}
			if(!empty($row->lng)){$out.=' var lng='.esc_html($row->lng).';';}
			if(!empty($zoom)){$out.=' var zoom='.intval($zoom).';';}else{$out.=' var zoom=13;';}
			$out.='</script><div id="groups" style="float:left"></div><div id="map" ></div><div class="clear"></div>';
		}
		else
		{//old way for non geolocated
			$leader=array();
			$sql='SELECT * FROM '.CA_SMG_TBL.' WHERE id!="1" ORDER BY group_day,group_time';
			$results = $wpdb->get_results($sql);
			if(!empty($results))foreach ($results as $row) {$out.='<p><strong>'.esc_html($row->group_name).'</strong> '.$wp_locale->get_weekday($row->group_day).' '.mysql2date(get_option('time_format'),$row->group_time).' '.esc_html($row->address).'</p>';}
		}//end old way for non geolocated
	return $out;
}
?>
