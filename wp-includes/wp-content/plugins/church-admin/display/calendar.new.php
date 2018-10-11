<?php

/**
 *
 * Displays calendar via shortcode
 *
 * @author  Andy Moyle
 * @param    null
 * @return   $out
 * @version  0.1
 *
 */

function church_admin_new_calendar_display($length=null,$day_calendar=TRUE)
{

		if(!empty($_POST['start_date'])&& church_admin_checkdate($_POST['start_date'])){$start_date=$_POST['start_date'];}else{$start_date=date('Y-m-d');}

		$out= '<div class="ca-calendar"><div class="ca-cal">'.ca_get_calendar( $start_date,NULL).'</div>';
		if(strtotime($start_date) == gmdate( 'Y',  time() ).'-'.gmdate( 'm',  time() ).'-'.gmdate( 'j', time() ))$start_date=date('Y-m-d');
		$out.='<div class="ca-date-display">'.church_admin_display_day($start_date).'</div></div>';

		$nonce = wp_create_nonce("church_admin_username_check");
		$out.='<script type="text/javascript">jQuery(document).ready(function($) {$(".ca-day").click(function() {$(".ca-day").removeClass("ca-chosen");$(this).addClass("ca-chosen");var date= this.id;var data = {"date": date,"action": "church_admin","method":"calendar_date_display","nonce": "'.$nonce.'"};	jQuery.post(ajaxurl, data, function(response) {console.log(response);$(".ca-date-display").html(response);});});});</script>';

		return $out;
}


/**
 *
 * Displays day of calendar
 *
 * @author  	Andy Moyle
 * @param    	$start_date
 * @return   	$out
 * @version  	0.1
 *
 */

function church_admin_display_day($start_date=null)
{
		//initialise
		global $wpdb;
		$out='';

		//use today if something wrong with given dates
		if(empty($start_date))$start_date=date('Y-m-d');
		if(!church_admin_checkdate($start_date))$start_date=date('Y-m-d');

		//grab events into $events array
		$sql='SELECT a.*, b.* FROM '.CA_DATE_TBL.' a LEFT JOIN '.CA_CAT_TBL.' b ON b.cat_id = a.cat_id WHERE a.start_date="'.esc_sql($start_date).'" ORDER BY a.start_time ASC';

		$event_results=$wpdb->get_results($sql);
		$day=array();
		if(!empty($event_results))
		{
			foreach($event_results As $event)
			{
				$day[strtotime($start_date.' '.$event->start_time)]=$event;
			}
			ksort($day);

		}


		//build output
		$out.='<h2>'.mysql2date(get_option('date_format'),$start_date).'</h2>';
		//header
		$out.='<div class="ca-day-view">';


		//build day
		if(!empty($day))
		{
			foreach($day AS $item)
			{
				$out.='<div class="ca-day-item ca-item" id="'.intval($item->date_id).'" style="border-right:5px solid '.esc_html($item->bgcolor).';border-left:5px solid '.esc_html($item->bgcolor).'"><div class="ca-day-title">'.esc_html($item->title).'</div><div class="ca-day-time">'.esc_html(strtoupper(
				sprintf(__('%1$s to %2$s','church-admin'),
						mysql2date(get_option('time_format'),$item->start_time),
						mysql2date(get_option('time_format'),$item->end_time)
				))).'</div>';
				if(!empty($item->description))$out.='<div class="ca-day-description">'.esc_html($item->description).'</div>';
				if(!empty($item->location))$out.='<div class="ca-day-location">'.esc_html($item->location).'</div>';
				if(!empty($item->link))$out.='<div class="ca-day-link"><a href="'.esc_url($item->link).'">'.esc_html($item->link_title).'</a></div>';
				$out.='</div><div class="clear"></div></div>';
			}
		}
		else
		{
			$out.='<div class="ca-day-item"><div class="ca-day-title">'.__('No events for today','church-admin').'</div><div class="ca-day-time">&nbsp;</div><div class="ca-day-location">&nbsp;</div></div>';


		}


		$out.='</div>';
		return $out;
}

/**
 * Display calendar with days that have church admin events.
 *
 *
 * @global wpdb      $wpdb
 * @global int       $m
 * @global int       $monthnum
 * @global int       $year
 * @global WP_Locale $wp_locale
 * @global array     $posts
 *
 * @param bool $initial Optional, default is true. Use initial calendar names.
 * @param bool $echo    Optional, default is true. Set to false for return.
 * @return $out
 */



function ca_get_calendar( $start_date=NULL) {
	global $wpdb, $m, $monthnum, $year, $wp_locale;
	$initial=true;

	//use today if something wrong with given dates
	if(empty($start_date))$start_date=date('Y-m-d');
	if(!church_admin_checkdate($start_date))$start_date=date('Y-m-d');
	$prev=explode('-',date('Y-m-d',strtotime($start_date.' -1 month')));
	$next=explode('-',date('Y-m-d',strtotime($start_date.' +1 month')));


	// week_begins = 0 stands for Sunday
	$week_begins = (int) get_option( 'start_of_week' );
	$ts = strtotime( $start_date );
	$thisyear = gmdate( 'Y', $ts );
	$thismonth = gmdate( 'm', $ts );
	$thisday = gmdate( 'd', $ts );
	$unixmonth = mktime( 0, 0 , 0, $thismonth, 1, $thisyear );
	$last_day = date( 't', $unixmonth );



	/* translators: Calendar caption: 1: month name, 2: 4-digit year */
	$calendar_caption = _x('%1$s %2$s', 'calendar caption');
	$calendar_output = '<table class="ca-day-calendar "><thead><tr>';
	$calendar_output .= "\n\t\t".'<th colspan="2" id="prev">'.ca_date_link($prev[0],$prev[1],'01',__('<','church-admin'),NULL).'</th>';
	$calendar_output .= "\n\t\t".'<th colspan="3">' . sprintf(
		$calendar_caption,
		$wp_locale->get_month( $thismonth ),
		date( 'Y', $unixmonth )
	) . '</th>';
	$calendar_output .= "\n\t\t".'<th colspan="2" id="next">'.ca_date_link($next[0],$next[1],'01',__('>','church-admin'),NULL).'</th>';
	$calendar_output .= '</tr></thead><tbody>';

	$myweek = array();

	for ( $wdcount = 0; $wdcount <= 6; $wdcount++ ) {
		$myweek[] = $wp_locale->get_weekday( ( $wdcount + $week_begins ) % 7 );
	}

	foreach ( $myweek as $wd ) {
		$day_name = $initial ? $wp_locale->get_weekday_initial( $wd ) : $wp_locale->get_weekday_abbrev( $wd );
		$wd = esc_attr( $wd );
		$calendar_output .= "\n\t\t<th scope=\"col\" title=\"$wd\">$day_name</th>";
	}

	$calendar_output .= '
	</tr>
	</thead>

	<tfoot>
	<tr>';





	$calendar_output .= '
	</tr>
	</tfoot>

	<tbody>
	<tr>';

	$daywithevent = array();

	// Get days with events
	$dayswithevents = $wpdb->get_results("SELECT DISTINCT DAYOFMONTH(start_date)
		FROM ".CA_DATE_TBL." WHERE start_date >= '{$thisyear}-{$thismonth}-01 00:00:00'
		AND start_date <= '{$thisyear}-{$thismonth}-{$last_day} 23:59:59'", ARRAY_N);
	if ( $dayswithevents ) {
		foreach ( (array) $dayswithevents as $daywith ) {
			$daywithevent[] = $daywith[0];
		}
	}

	// See how much we should pad in the beginning
	$pad = calendar_week_mod( date( 'w', $unixmonth ) - $week_begins );
	if ( 0 != $pad ) {
		$calendar_output .= "\n\t\t".'<td colspan="'. esc_attr( $pad ) .'" class="pad">&nbsp;</td>';
	}

	$newrow = false;
	$daysinmonth = (int) date( 't', $unixmonth );

	for ( $day = 1; $day <= $daysinmonth; ++$day ) {
		if ( isset($newrow) && $newrow ) {
			$calendar_output .= "\n\t</tr>\n\t<tr>\n\t\t";
		}
		$newrow = false;
		$class=array('ca-day');
		if ( $day == gmdate( 'j', $ts ) &&
			$thismonth == gmdate( 'm', $ts ) &&
			$thisyear == gmdate( 'Y', $ts ) ) {
			$class[]= "ca-chosen";

		} elseif(
		 	$day == gmdate( 'j', time() ) &&
			$thismonth == gmdate( 'm',  time() ) &&
			$thisyear == gmdate( 'Y',  time() ) ){
			$class[]="ca-today";

		}

		if ( in_array( $day, $daywithevent ) ) {$class[]="ca-event";}else{$class[]="ca-no-event";}
		$calendar_output .= '<td';
		if(!empty($class)) $calendar_output.=' class="'.implode(" ",$class).'"';
		$calendar_output.=' id="'.$thisyear.'-'.$thismonth.'-'.$day.'"';
		$calendar_output.='>'.$day. '</td>';

		if ( 6 == calendar_week_mod( date( 'w', mktime(0, 0 , 0, $thismonth, $day, $thisyear ) ) - $week_begins ) ) {
			$newrow = true;
		}
	}

	$pad = 7 - calendar_week_mod( date( 'w', mktime( 0, 0 , 0, $thismonth, $day, $thisyear ) ) - $week_begins );
	if ( $pad != 0 && $pad != 7 ) {
		$calendar_output .= "\n\t\t".'<td class="pad" colspan="'. esc_attr( $pad ) .'">&nbsp;</td>';
	}
	$calendar_output .= "\n\t</tr>\n\t</tbody>\n\t</table>";


	return  $calendar_output ;
}
