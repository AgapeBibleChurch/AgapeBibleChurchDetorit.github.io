<?php

function church_admin_calendar_list($days=28,$category=NULL)
{
	global $wpdb,$wp_locale;
	$out='';
	
	//process categories
	$catsql=array();
  	if(empty($category)){$cat_sql="";}
  	else
  	{
  		
  		$cats=explode(',',$category);
      	foreach($cats AS $key=>$value){if(ctype_digit($value))  $catsql[]='a.cat_id='.intval($value);}
      	if(!empty($catsql)) {$cat_sql=' AND ('.implode(' || ',$catsql).')';}
	}
	
	//work out dates
	if(!empty($_POST['date'])&&church_admin_checkdate($_POST['date'])){$date=new ChurchAdminDateTime($_POST['date']);}else{$date=new ChurchAdminDateTime();}
	
	$sqlstart=$date->format('Y-m-d');
	$nowDisplayDate=$date->format(get_option('date_format'));
	$end=$date->returnAdd(new DateInterval('P'.$days.'D'));
	$nextDisplay=$sqlend=$end->format('Y-m-d');
	$next=$sqlend;
	$prevDate=$date->returnSub(new DateInterval('P'.$days.'D'));
	$prevDisplay=$prevDate->format('Y-m-d');
	
	$sql='SELECT a.*, b.* FROM '.CA_DATE_TBL.' a,'.CA_CAT_TBL.' b WHERE a.cat_id=b.cat_id  AND a.start_date BETWEEN CAST("'.$sqlstart.'" AS DATE) AND CAST("'.$sqlend.'" AS DATE) '.$cat_sql.' ORDER BY a.start_date,a.start_time';
	$results=$wpdb->get_results($sql);
	
	$data=array();
	if(!empty($results))
	{
		//build $data for outputing
		foreach($results AS $row)
		{
			if($row->start_time=='00:00:00' && $row->end_time=='23:59:00')
    		{//all day
				$data[]=array(
								'date'=>mysql2date(get_option('date_format'),$row->start_date),
								'time'=>__('All Day','church-admin'),
								'title'=>esc_html(stripslashes($row->title)),
								'description'=>esc_html(stripslashes($row->description))
							);
			}
			{//timed
				$data[]=array(
								'date'=>mysql2date(get_option('date_format'),$row->start_date),
								'time'=>mysql2date(get_option('time_format'),$row->start_time)." - ".mysql2date(get_option('time_format'),$row->end_time),
								'title'=>esc_html(stripslashes($row->title)),
								'description'=>esc_html(stripslashes($row->description))
							);
			}
		}
	}//got results
	
	//Chooser
	$out.='<table class="ca-calendar-list-chooser"><tr><td class="ca-calendar-list-prev">';
	$out.='<form action="'.get_permalink().'"  method="post"><input type="hidden" name="date" value="'.$prevDisplay.'"/><input class="calendar-date-switcher" type="submit" value="'.__('Previous','church-admin').'" /></form></td>';
	$out.='<td class="ca-calendar-list-chooser">'.sprintf(__( 'Events for the next %1$s days from %2$s','church-admin'),intval($days),$nowDisplayDate).'</td>';
	$out.='<td class="ca-calendar-list-next"><form action="'.get_permalink().'"  method="post"><input type="hidden" name="date" value="'.$nextDisplay.'"/><input class="calendar-date-switcher" type="submit" value="'.__('Next','church-admin').'" /></form></td>';
	$out.='</tr></table>';
	
	//build output
	if(empty($data))
	{	
		$out.='<p>'.__('No events to display','church-admin').'</p>';
	}
	else
	{
		$out.='<table class="ca-calendar-list"><thead><tr><th class="ca-list-date">'.__('Date','church-admin').'</th><th class="ca-list-time">'.__('Time','church-admin').'</th><th class="ca-list-event">'.__('Event','church-admin').'</th></tr></thead></tbody>';
		foreach($data AS $key=>$event)
		{
			$out.=	'<tr>
						<td class="ca-list-date">'.$event['date'].'</td>
						<td class="ca-list-time">'.$event['time'].'</td>
						<td class="ca-list-event"><strong>'.$event['title'].'</strong><br/>'.$event['description'].'</td>
					</tr>';
		}
		$out.='</tbody></table>';
	}
	
	
	
	return $out;
}
	
?>