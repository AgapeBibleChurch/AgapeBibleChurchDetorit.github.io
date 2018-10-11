<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/*
2011-02-04 added calendar single and series delete; fixed slashes problem
2011-03-14 fixed errors not sowing as red since 0.32.4
2012-07-20 Update Internationalisation
2014-09-22 Simplify db and add image
2014-10-06 Added facilities bookings

*/
function church_admin_facilities($current=NULL,$facilities_id=1)
{
	global $wpdb;
	echo'<h2>'.__('Use this section to organise facilities like rooms, video projectors','church-admin').'</h2>';
	echo' <p><a href="'.wp_nonce_url('admin.php?page=church_admin/index.php&amp;action=edit_facility&tab=facilities','edit_facility').'">'.__('Add Facility','church-admin').'</a></p>';

	$facilities=$wpdb->get_results('SELECT * FROM '.CA_FAC_TBL.' ORDER BY facilities_order');
    if(!empty($facilities))
	{
		echo'<p>'.__('Facilities can be sorted by drag and drop, for use in other parts of the plugin','church-admin').'</p>';
		echo'<table id="sortable" class="widefat striped"><thead><tr><th>'.__('Edit','church-admin').'</th><th>'.__('Delete','church-admin').'</th><th>'.__('Facility','church-admin').'</th><th>'.__('Facility Shortcode','church-admin').'</th></tr></thead><tfoot><tr><th>'.__('Edit','church-admin').'</th><th>'.__('Delete','church-admin').'</th><th>'.__('Facility','church-admin').'</th><th>'.__('Facility Shortcode','church-admin').'</th></tr></tfoot><tbody class="content">';
		foreach($facilities AS $facility)
		{
			$edit='<a href="'.wp_nonce_url('admin.php?page=church_admin/index.php&amp;action=edit_facility&amp;facilities_id='.$facility->facilities_id,'edit_facility').'">'.__('Edit','church-admin').'</a>';

            $delete='<a onclick="return confirm(\''.__('Are you sure?','church-admin').'\');" href="'.wp_nonce_url('admin.php?page=church_admin/index.php&amp;action=delete_facility&facilities_id='.$facility->facilities_id,'delete_facility').'">'.__('Delete','church-admin').'</a>';
			echo'<tr class="sortable-row" id="'.$facility->facilities_id.'"><td>'.$edit.'</td><td>'.$delete.'</td><td>'.esc_html($facility->facility_name).'</td><td>[church_admin type="calendar" facilities_id="'.$facility->facilities_id.'"]</td></tr>';

		}
		echo'</tbody></table>';
		echo ' <script type="text/javascript">jQuery(document).ready(function($) {var fixHelper = function(e,ui){ui.children().each(function() {            $(this).width($(this).width());});  return ui; }; var sortable = $("#sortable tbody.content").sortable({ helper: fixHelper, stop: function(event, ui) {
        //create an array with the new order
        var Order = "order="+$(this).sortable(\'toArray\').toString();

        $.ajax({
            url: "admin.php?page=church_admin/index.php&action=church_admin_update_order&which=facilities",
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
		});
		</script>';
	}
	if(!empty($facilities))
	{
		echo'<form action="admin.php?page=church_admin/index.php&action=facilities&tab=facilities" method="POST"><table><tbody><tr><th scope="row">'.__('Choose facility calendar to view','church-admin').'</th><td><select name="facilities_id">';
		foreach($facilities AS $fac){echo'<option value="'.esc_html($fac->facilities_id).'">'.esc_html($fac->facility_name).'</option>';}
		echo'</select><td><input type="submit" name="'.__('Choose facility','church-admin').'"/></td></tr></tbody></table></form>';
	}
	if(empty($facilities_id))$facilities_id=1;
	church_admin_new_calendar($current,$facilities_id);

}


 /**
 *
 * Calendar Display
 *
 * @author  Andy Moyle
 * @param    $current,$facilities_id
 * @return
 * @version  0.1
 *
 *
 *
 */
 function church_admin_new_calendar( $start_date=NULL,$facilities_id=NULL) {
	global $wpdb, $m, $monthnum, $year, $wp_locale;
	if(defined('CA_DEBUG'))$wpdb->show_errors();
	$initial=true;

	if(!empty($facilities_id))
	{
		$facility=$wpdb->get_var('SELECT facility_name FROM '.CA_FAC_TBL.' WHERE facilities_id="'.esc_sql($facilities_id).'"');
		echo '<h2>'.__('Bookings Calendar for ','church-admin').$facility.'</h2>';
	}else
	{
		echo '<h2>'.__('Calendar','church-admin').'</h2>';
	}
	echo '<p><a class="button-secondary" href="admin.php?page=church_admin/index.php&amp;action=church_admin_category_list">'.__('Category List','church-admin').'</a></p>';

	echo '<p>'.__('Double click on an event to edit it, or a day to add an event','church-admin').'</p>';
	echo '<p><a class="button-primary" href="'.wp_nonce_url('admin.php?page=church_admin/index.php&amp;action=church_admin_new_edit_calendar','edit_event').'">'.__('Add an event','church-admin').'</a></p>';
	//use today if something wrong with given dates
	if(empty($start_date))$start_date=date('Y-m-d');
	if(!empty($_POST['start_date'])&&church_admin_checkdate($_POST['start_date']))$start_date=$_POST['start_date'];
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
	$calendar_output = '<table  class="church_admin_calendar"><thead><tr>';
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
		//$day_name = $initial ? $wp_locale->get_weekday_initial( $wd ) : $wp_locale->get_weekday_abbrev( $wd );
		$wd = esc_attr( $wd );
		$calendar_output .= "\n\t\t<th scope=\"col\" title=\"$wd\">$wd</th>";
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
	<tr class="cal">';

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
			$calendar_output .= "\n\t</tr>\n\t<tr class=\"cal\">\n\t\t";
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
		$day_output='';
		if ( in_array( $day, $daywithevent ) )
		{

			$class[]="ca-event";
			$this_day=esc_sql($thisyear.'-'.$thismonth.'-'.$day);
			if(empty($facilities_id)){$sql='SELECT a.*, b.* FROM '.CA_DATE_TBL.' a,'.CA_CAT_TBL.' b WHERE a.general_calendar=1 AND a.cat_id=b.cat_id  AND a.start_date="'.$this_day.'" ORDER BY a.start_time';}
	else{$sql='SELECT a.*, b.* FROM '.CA_DATE_TBL.' a,'.CA_CAT_TBL.' b WHERE a.facilities_id="'.esc_sql($facilities_id).'" AND a.cat_id=b.cat_id  AND a.start_date="'.$this_day.'" ORDER BY a.start_time';}
			$events=$wpdb->get_results($sql);
			foreach($events AS $event)
        	{
				$border=church_admin_adjust_brightness($event->bgcolor, -50);
				$text=church_admin_adjust_brightness($event->bgcolor, -100);
				if($event->start_time=='00:00:00' && $event->end_time=='23:59:00')
    			{//all day
    				$day_output .=   '<div id="item'.$event->date_id.'"style="background-color:'.$event->bgcolor.';border-left:3px solid '.$border.';padding:5px;color:'.$text.'" >'.__('All Day','church-admin').' '.esc_html($event->title).'... </div></p>';

    			}
    			else
    			{
					$day_output .=  '<div id="item'.$event->date_id.'"style="background-color:'.$event->bgcolor.';border-left:3px solid '.$border.';padding:5px;color:'.$text.'" >'.mysql2date(get_option('time_format'),$event->start_time).' '.esc_html($event->title).'... </div></p>';

				}
            }
		}else{$class[]="ca-no-event";}
		$calendar_output .= '<td';
		if(!empty($class)) $calendar_output.=' class="'.implode(" ",$class).'"';
		$calendar_output.=' id="'.$thisyear.'-'.$thismonth.'-'.$day.'"';
		$calendar_output.='>'.$day.'<br/>';
		$calendar_output.= $day_output.'</td>';

		if ( 6 == calendar_week_mod( date( 'w', mktime(0, 0 , 0, $thismonth, $day, $thisyear ) ) - $week_begins ) ) {
			$newrow = true;
		}
	}

	$pad = 7 - calendar_week_mod( date( 'w', mktime( 0, 0 , 0, $thismonth, $day, $thisyear ) ) - $week_begins );
	if ( $pad != 0 && $pad != 7 ) {
		$calendar_output .= "\n\t\t".'<td class="pad" colspan="'. esc_attr( $pad ) .'">&nbsp;</td>';
	}
	$calendar_output .= "\n\t</tr>\n\t</tbody>\n\t</table>";
	$calendar_output .= '<script type="text/javascript">	jQuery(document).ready(function($) {$(".cal").bind("dblclick", function(event) {window.location.href = "'.admin_url().'?page=church_admin/index.php&action=church_admin_new_edit_calendar&tab=calendar&id="+event.target.id';
if(!empty($facilities_id))$calendar_output.= '+ "&facilities_id='.$facilities_id.'"';
$calendar_output.= '});});</script>';


	echo  $calendar_output ;
}

function church_admin_new_calendarv1($current=NULL,$facilities_id=NULL)
{

	global $wpdb;

	if(isset($_POST['ca_month']) && isset($_POST['ca_year'])){$current=$date = new DateTime($_POST['ca_year'].' '.$_POST['ca_month'].' 01');}
	else
	{
		$current=new DateTime();
		$current->modify('first day of this month');
	}

	print_r($current);

	/*
	if(isset($_POST['ca_month']) && isset($_POST['ca_year'])){ $current=mktime(12,0,0,$_POST['ca_month'],14,$_POST['ca_year']);}
	if(empty($current)){$current=time();}
	$thismonth = (int)date("m",$current);
	$thisyear = date( "Y",$current );
	$actualyear=date("Y");
	$next = strtotime("+1 month",$current);
	$previous = strtotime("-1 month",$current);
	$now=date("M Y",$current);
	$sqlnow=date("Y-m-d", $current);
    // find out the number of days in the month
    $numdaysinmonth = date('t',$current);//cal_days_in_month( CAL_GREGORIAN, $thismonth, $thisyear );
    // create a calendar object
    $jd = cal_to_jd( CAL_GREGORIAN, $thismonth,date( 1 ), $thisyear );

    // get the start day as an int (0 = Sunday, 1 = Monday, etc)
    $startday = jddayofweek( $jd , 0 );

    // get the month as a name
    $monthname = jdmonthname( $jd, 1 );
	*/
	if(!empty($facilities_id))
	{
		$facility=$wpdb->get_var('SELECT facility_name FROM '.CA_FAC_TBL.' WHERE facilities_id="'.esc_sql($facilities_id).'"');
		echo '<h2>'.__('Bookings Calendar for ','church-admin').$facility.'</h2>';
	}else
	{
		echo '<h2>'.__('Calendar','church-admin').'</h2>';
	}
	echo '<p><a href="admin.php?page=church_admin/index.php&amp;action=church_admin_category_list">'.__('Category List','church-admin').'</a></p>';

	echo '<p>'.__('Double click on an event to edit, or a day to add an event','church-admin').'</p>';
	echo '<p><a href="'.admin_url().'?page=church_admin/index.php&action=church_admin_calendar_list&tab=calendar">'.__('Old Style Calendar List','church-admin').'</a></p>';
	echo '<table class="church_admin_calendar"><tr><td colspan="7" class="calendar-date-switcher"><form method="post" action="'.admin_url().'?page=church_admin/index.php&action=church_admin_new_calendar&tab=calendar">'.__('Month','church-admin').'<select name="ca_month">';
	$first=$option='';
	for($q=0;$q<=12;$q++)
	{
		$mon=date('m',($current+$q*(28*24*60*60)));
		$MON=date('M',($current+$q*(28*24*60*60)));
		if(isset($_POST['ca_month'])&&$_POST['ca_month']==$mon) {$first='<option value="'.esc_html($mon).'" selected="selected">'.esc_html($MON).'</option>';}else{echo  '<option value="'.esc_html($mon).'">'.esc_html($MON).'</option>';}
	}
	echo $first.$option;
	echo '</select>'.__('Year','church-admin').'<select name="ca_year">';
	$first=$option='';
	for ($x=$actualyear;$x<=$actualyear+15;$x++)
	{
		if(isset($_POST['ca_year'])&&$_POST['ca_year']==$x)
		{
			$first='<option value="'.esc_html($x).'" >'.esc_html($x).'</option>';
		}
		else
		{
			$option.='<option value="'.esc_html($x).'" >'.esc_html($x).'</option>';
		}
	}
	echo $first.$option;
	if(!empty($facilities_id)) echo '<input type="hidden" name="facilities_id" value="'.intval($facilities_id).'"/>';
	echo '</select><input  type="submit" value="'.__('Submit','church-admin').'"/></form></td></tr>';
	echo '<tr><td colspan="3" class="calendar-date-switcher">';
	if($now==date('M Y')){echo '&nbsp;';}
	else
	{
		echo '<form action="'.admin_url().'?page=church_admin/index.php&action=church_admin_new_calendar&tab=calendar" name="previous" method="post"><input type="hidden" name="ca_month" value="'.date('m',strtotime("$now -1 month")).'"/><input type="hidden" name="ca_year" value="'.date('Y',strtotime("$now -1 month")).'"/><input type="submit" value="'.__('Previous','church-admin').'" class="calendar-date-switcher"/>';
		if(!empty($facilities_id)) echo '<input type="hidden" name="facilities_id" value="'.intval($facilities_id).'"/>';
		echo '</form>';
	}
	echo '</td><td class="calendar-date-switcher">'.$now.'</td><td class="calendar-date-switcher" colspan="3"><form action="'.admin_url().'?page=church_admin/index.php&action=church_admin_new_calendar&tab=calendar" method="post"><input type="hidden" name="ca_month" value="'.date('m',strtotime($now.' +1 month')).'"/><input type="hidden" name="ca_year" value="'.date('Y',strtotime($now.' +1 month')).'"/>';
	if(!empty($facilities_id)) echo '<input type="hidden" name="facilities_id" value="'.intval($facilities_id).'"/>';
	echo '<input type="submit" class="calendar-date-switcher" value="'.__('Next','church-admin').'"/></form></td></tr>
	<tr><td  ><strong>'.__('Sunday','church-admin').'</strong></td>
    <td ><strong>'.__('Monday','church-admin').'</strong></td>
    <td ><strong>'.__('Tuesday','church-admin').'</strong></td>
    <td ><strong>'.__('Wednesday','church-admin').'</strong></td>
    <td ><strong>'.__('Thursday','church-admin').'</strong></td>
    <td ><strong>'.__('Friday','church-admin').'</strong></td>
    <td ><strong>'.__('Saturday','church-admin').'</strong></td>
    </tr><tr class="cal">';
	// put render empty cells
	$emptycells = 0;
	for( $counter = 0; $counter <  $startday; $counter ++ )
	{
		echo "\t\t<td>-</td>\n";
		$emptycells ++;
	}
	// renders the days
	$rowcounter = $emptycells;
	$numinrow = 7;

	for( $counter = 1; $counter <= $numdaysinmonth; $counter ++ )
	{
		$sqlnow="$thisyear-$thismonth-".sprintf('%02d', $counter);
        $rowcounter ++;
		echo "\t\t".'<td id="'.$sqlnow.'"><strong>'.$counter.'</strong><br/>';
    //put events for day in here

    if(empty($facilities_id)){$sql='SELECT a.*, b.* FROM '.CA_DATE_TBL.' a,'.CA_CAT_TBL.' b WHERE a.general_calendar=1 AND a.cat_id=b.cat_id  AND a.start_date="'.$sqlnow.'" ORDER BY a.start_time';}
	else{$sql='SELECT a.*, b.* FROM '.CA_DATE_TBL.' a,'.CA_CAT_TBL.' b WHERE a.facilities_id="'.esc_sql($facilities_id).'" AND a.cat_id=b.cat_id  AND a.start_date="'.$sqlnow.'" ORDER BY a.start_time';}

    $result=$wpdb->get_results($sql);
    if($wpdb->num_rows=='0')
    {
        echo '&nbsp;<br/>&nbsp;<br/>';
    }
    else
    {
        foreach($result AS $row)
        {
			$border=church_admin_adjust_brightness($row->bgcolor, -50);
			$text=church_admin_adjust_brightness($row->bgcolor, -100);
			if($row->start_time=='00:00:00' && $row->end_time=='23:59:00')
    		{//all day
    			echo  '<div id="item'.$row->date_id.'"style="background-color:'.$row->bgcolor.';border-left:3px solid '.$border.';padding:5px;color:'.$text.'" >'.__('All Day','church-admin').' '.esc_html($row->title).'... </div></p>';

    		}
    		else
    		{
				echo  '<div id="item'.$row->date_id.'"style="background-color:'.$row->bgcolor.';border-left:3px solid '.$border.';padding:5px;color:'.$text.'" >'.mysql2date(get_option('time_format'),$row->start_time).' '.esc_html($row->title).'... </div></p>';

			}
            }
    }
    echo "</td>\n";

        if( $rowcounter % $numinrow == 0 )
        {
            echo "\t</tr>\n";
            if( $counter < $numdaysinmonth )
            {
                echo "\t".'<tr class="cal">'."\n";
            }
            $rowcounter = 0;
        }
}
// clean up
$numcellsleft = $numinrow - $rowcounter;
if( $numcellsleft != $numinrow )
{
    for( $counter = 0; $counter < $numcellsleft; $counter ++ )
    {
        echo  "\t\t<td>-</td>\n";
        $emptycells ++;
    }
}
echo '</tr></table>';

echo '<script type="text/javascript">	jQuery(document).ready(function($) {$(".cal").bind("dblclick", function(event) {window.location.href = "'.admin_url().'?page=church_admin/index.php&action=church_admin_new_edit_calendar&tab=calendar&id="+event.target.id';
if(!empty($facilities_id))echo '+ "&facilities_id='.$facilities_id.'"';
echo '});});</script>';

}




function church_admin_category_list()
{
    global $wpdb;
    //build category tableheader
        $thead='<tr><th>'.__('Edit','church-admin').'</th><th>'.__('Delete','church-admin').'</th><th width="100">'.__('Category','church-admin').'</th><th>'.__('Shortcode','church-admin').'</th></tr>';
    $table= '<table class="widefat striped" ><thead>'.$thead.'</thead><tfoot>'.$thead.'</tfoot><tbody>';
        //grab categories
    $results=$wpdb->get_results('SELECT * FROM '.CA_CAT_TBL);
    foreach($results AS $row)
    {
        $edit_url='<a href="'.wp_nonce_url('admin.php?page=church_admin/index.php&amp;action=church_admin_edit_category&tab=calendar&amp;id='.$row->cat_id,'edit_category').'">'.__('Edit','church-admin').'</a>';
        $delete_url='<a onclick="return confirm(\''.__('Are you sure?','church-admin').'\');" href="'.wp_nonce_url('admin.php?page=church_admin/index.php&amp;action=church_admin_delete_category&tab=calendar&amp;id='.$row->cat_id,'delete_category').'">'.__('Delete','church-admin').'</a>';
        $shortcode='[church_admin type=calendar-list category='.$row->cat_id.' weeks=4]';
        $table.='<tr><td>'.$edit_url.'</td><td>'.$delete_url.'</td><td style="background:'.$row->bgcolor.'">'.esc_html($row->category).'</td><td>'.$shortcode.'</td></tr>';
    }
    $table.='</tbody></table>';
    echo '<h2>'.__('Calendar Categories','church-admin').'</h2><p><a href="'.wp_nonce_url('admin.php?page=church_admin/index.php&amp;action=church_admin_edit_category&tab=calendar','edit_category').'">'.__('Add a category','church-admin').'</a></p>'.$table;
}



function church_admin_delete_category($id)
{
    global $wpdb;

    //count how many events have that category
    $count=$wpdb->get_var('SELECT COUNT(*) FROM '.CA_DATE_TBL.' WHERE cat_id="'.esc_sql($id).'"');
    $wpdb->query('DELETE FROM '.CA_CAT_TBL.' WHERE cat_id="'.esc_sql($id).'"');
    //adjust events with deleted cat_id to 0
    $wpdb->query('UPDATE '.CA_DATE_TBL.' SET cat_id="1" WHERE cat_id="'.esc_sql($id).'"');
    echo '<div id="message" class="notice notice-success inline">';
        echo '<p><strong>'.__('Category Deleted','church-admin').'.<br/>';
        if($count==1) printf(__('Please note that %1$s event used that category and will need editing','church-admin'),$count).'.';
        if($count>1) printf(__('Please note that %1$s events used that category and will need editing','church-admin'),$count).'.';
        echo'</strong></p>';
        echo '</div>';
        church_admin_category_list();


}
function church_admin_edit_category($id)
{
    global $wpdb;
    if(!empty($_POST))
    {
        if(!empty($id))
        {
        	$wpdb->query('UPDATE '.CA_CAT_TBL.' SET category="'.esc_sql(stripslashes($_POST['category'])).'",bgcolor="'.esc_sql($_POST['color']).'" WHERE cat_id="'.esc_sql($id).'"');
        }
        else
        {
        	$wpdb->query('INSERT INTO '.CA_CAT_TBL.' (category,bgcolor) VALUES("'.esc_sql(stripslashes($_POST['category'])).'","'.esc_sql($_POST['color']).'")');        }
        echo '<div id="message" class="notice notice-success inline">';
        if($id){echo '<p><strong>'.__('Category Edited','church-admin').'</strong></p>';}else{echo '<p><strong>'.__('Category Added','church-admin').'</strong></p>';}
        echo '</div>';
        church_admin_category_list();
    }
    else
    {
    if(empty($id)){$which=__("Add category",'church-admin');}else{$which=__('Edit','church-admin');}
    echo'<h2>'.$which.'</h2><form action="" method="post"><table class="form-table">';
    //grab current data
    $data=$wpdb->get_row('SELECT * FROM '.CA_CAT_TBL.' WHERE cat_id="'.esc_sql($id).'"');
    if(empty($data))$data=new stdClass();

    if(empty($data->bgcolor))$data->bgcolor='#e4afb1';
	echo '<script type="text/javascript" >
  jQuery(document).ready(function($) {

    $(\'#picker\').farbtastic(\'#color\');


  });
 </script>
 <tr><th scope="row" >'.__('Category Name','church-admin').'</th><td><input type="text" name="category" ';
 if(!empty($data->category)) echo 'value="'.esc_html($data->category).'"';
	echo'/></td></tr>';
 echo'<tr><th scope="row">'.__('Background Colour','church-admin').'</th><td><input type="text" ';
  if(!empty($data->bgcolor)) echo' style="background:'.esc_html($data->bgcolor).'" ';
  echo' id="color" name="color" ';
  if(!empty($data->bgcolor))echo' value="'.esc_html($data->bgcolor).'" ';
  echo'/><br/><div id="picker"></div></td></tr>';
    echo'<tr><th scope="row">&nbsp;</th><td><input type="submit" class="button-primary" name="edit_category" value="'.$which.'"/></p></form>';

    echo'</table>';
    }
}


function church_admin_calendar()
{
    global $wpdb;
    echo'<div class="wrap church_admin"><h2>'.__('Calendar','church-admin').'</h2><p><a href="admin.php?page=church_admin/index.php&amp;action=church_admin_category_list">'.__('Category List','church-admin').'</a></p>';
    church_admin_calendar_list();
    echo'</div>';
}


 /**
 *
 * Edit an event
 *
 * @author  Andy Moyle
 * @param    $date_id,$event_id,$edit_type,$date,$facilities_id
 * @return
 * @version  0.1
 *
 *
 *
 */
function church_admin_event_edit($date_id,$event_id,$edit_type,$date,$facilities_id)
{

	global $wpdb;

	$edit=''.__('Add','church-admin').'';
	if(!empty($date_id)){$data=$wpdb->get_row('SELECT a.*,b.* FROM '.CA_DATE_TBL.' a,'.CA_CAT_TBL.' b WHERE a.cat_id=b.cat_id AND a.date_id="'.esc_sql($date_id).'"');$edit=''.__('Edit','church-admin').'';}
	if(empty($event_id)&&!empty($data->event_id)){$event_id=$data->event_id;$edit=''.__('Edit','church-admin').'';}

	if(!empty($_POST['save_date']))
	{//process

		switch($edit_type)
		{
			case'single':if(!empty($date_id)){$wpdb->query('DELETE FROM '.CA_DATE_TBL .' WHERE date_id="'.esc_sql($date_id).'"');}break;
			case'series':if(!empty($event_id)){$wpdb->query('DELETE FROM '.CA_DATE_TBL .' WHERE event_id="'.esc_sql($event_id).'"');}break;

		}

		//get next highest event_id
		$event_id=$wpdb->get_var('SELECT MAX(event_id) FROM '.CA_DATE_TBL)+1;
		$form=array();
		foreach($_POST AS $key=>$value)$form[$key]=sanitize_text_field(stripslashes($value));
		//adjust data
		$form['start_time'].=':00';
		$form['end_time'].=':00';
		if(!empty($form['all_day'])){$form['start_time']='00:00:00';$form['end_time']='23:59:00';}
		if(empty($form['cat_id'])){$form['cat_id']=1;}
		if(empty($form['year_planner'])){$form['year_planner']=0;}else{$form['year_planner']=1;}
		if(empty($form['general_calendar'])){$form['general_calendar']=0;}else{$form['general_calendar']=1;}
		if(empty($form['end_date'])){$form['end_date']=$form['start_date'];}
		//text link overrides dropdown menu
		$form['link']='';
		if(!empty($form['link2'])){$form['link']==$form['link2'];}
		if(!empty($form['link1'])){$form['link']==$form['link1'];}
		//only allow one submit!
		$checksql='SELECT date_id FROM '.CA_DATE_TBL.' WHERE title="'.esc_sql($form['title']).'" AND description="'.esc_sql($form['description']).'" AND location="'.esc_sql($form['location']).'"  AND cat_id="'.esc_sql($form['cat_id']).'" AND start_date="'.esc_sql($form['start_date']).'" AND start_time="'.esc_sql($form['start_time']).'" AND end_time="'.esc_sql($form['end_time']).'" AND facilities_id="'.esc_sql($form['facilities_id']).'" LIMIT 1';

		$check=$wpdb->get_var($checksql);
		if(empty($check)||!empty($date_id))
		{
			//handle upload
			if(empty($data->event_image)){$event_image=NULL;}else{$event_image=$data->event_image;}

			if(!empty($_FILES) && $_FILES['uploadfiles']['error'] == 0)
			{
				$filetmp = $_FILES['uploadfiles']['tmp_name'];
				//clean filename and extract extension
				$filename = $_FILES['uploadfiles']['name'];

				// get file info
				$filetype = wp_check_filetype( basename( $filename ), null );
				$filetitle = preg_replace('/\.[^.]+$/', '', basename( $filename ) );
				$filename = $filetitle . '.' . $filetype['ext'];
				$upload_dir = wp_upload_dir();
				/**
				* Check if the filename already exist in the directory and rename the
				* file if necessary
				*/
				$i = 0;
				while ( file_exists( $upload_dir['path'] .'/' . $filename ) )
				{
					$filename = $filetitle . '_' . $i . '.' . $filetype['ext'];
					$i++;
				}

				$filedest = $upload_dir['path'] . '/' . $filename;

				move_uploaded_file($filetmp, $filedest);
				$attachment = array('post_mime_type' => $filetype['type'],'post_title' => $filetitle,'post_content' => '','post_status' => 'inherit');
				$event_image = wp_insert_attachment( $attachment, $filedest );

				require_once( ABSPATH . "wp-admin" . '/includes/image.php' );
				$attach_data = wp_generate_attachment_metadata( $event_image, $filedest );

				wp_update_attachment_metadata( $event_image,  $attach_data );

			}// end handle upload


			switch($_POST['recurring'])
			{
				case's':
					$sql='INSERT INTO '.CA_DATE_TBL.' (title,description,location,recurring,year_planner, event_image,cat_id,event_id,how_many,start_date,start_time,end_time,facilities_id,link,link_title,general_calendar)VALUES("'.esc_sql($form['title']).'","'.esc_sql($form['description']).'","'.esc_sql($form['location']).'","'.esc_sql($form['recurring']).'","'.esc_sql($form['year_planner']).'","'.$event_image.'","'.esc_sql($form['cat_id']).'","'.$event_id.'","'.esc_sql($form['how_many']).'","'.esc_sql($form['start_date']).'","'.esc_sql($form['start_time']).'","'.esc_sql($form['end_time']).'","'.esc_sql($form['facilities_id']).'","'.esc_sql($form['link']).'","'.esc_sql($form['link_title']).'","'.esc_sql($form['general_calendar']).'")';
				break;
				case'n':
					//handle nth
					$values=array();
					for($x=0;$x<$form['how_many'];$x++)
					{
						$start_date=nthday($form['nth'],$form['day'],date('Y-m-d',strtotime($form['start_date']." +$x month")));
               			$values[]='("'.esc_sql($form['title']).'","'.esc_sql($form['description']).'","'.esc_sql($form['location']).'","'.esc_sql($form['recurring']).'","'.esc_sql($form['year_planner']).'","'.$event_image.'","'.$event_id.'","'.esc_sql($form['how_many']).'","'.esc_sql($form['cat_id']).'","'.$start_date.'","'.$form['start_time'].'","'.$form['end_time'].'","'.esc_sql($form['facilities_id']).'","'.esc_sql($form['link']).'","'.esc_sql($form['link_title']).'","'.esc_sql($form['general_calendar']).'")';
					}
					$sql='INSERT INTO '.CA_DATE_TBL.' (title,description,location,recurring,year_planner, event_image,event_id,how_many,cat_id,start_date,start_time,end_time,facilities_id,link,link_title,general_calendar)VALUES'.implode(",",$values);
				break;
				case '14':
					$values=array();
					for($x=0;$x<$form['how_many'];$x++)
					{
						$start_date=date('Y-m-d',strtotime("{$form['start_date']}+$x fortnight"));
						$values[]='("'.esc_sql($form['title']).'","'.esc_sql($form['description']).'","'.esc_sql($form['location']).'","'.esc_sql($form['recurring']).'","'.esc_sql($form['year_planner']).'","'.$event_image.'","'.$event_id.'","'.esc_sql($form['how_many']).'","'.esc_sql($form['cat_id']).'","'.$start_date.'","'.$form['start_time'].'","'.$form['end_time'].'","'.esc_sql($form['facilities_id']).'","'.esc_sql($form['link']).'","'.esc_sql($form['link_title']).'","'.esc_sql($form['general_calendar']).'")';
					}
					$sql='INSERT INTO '.CA_DATE_TBL.' (title,description,location,recurring,year_planner, event_image,event_id,how_many,cat_id,start_date,start_time,end_time,facilities_id,link,link_title,general_calendar)VALUES'.implode(",",$values);
				break;
				case '7':
					$values=array();
					for($x=0;$x<$form['how_many'];$x++)
					{
						$start_date=date('Y-m-d',strtotime("{$form['start_date']}+$x week"));
						$values[]='("'.esc_sql($form['title']).'","'.esc_sql($form['description']).'","'.esc_sql($form['location']).'","'.esc_sql($form['recurring']).'","'.esc_sql($form['year_planner']).'","'.$event_image.'","'.$event_id.'","'.esc_sql($form['how_many']).'","'.esc_sql($form['cat_id']).'","'.$start_date.'","'.$form['start_time'].'","'.$form['end_time'].'","'.esc_sql($form['facilities_id']).'","'.esc_sql($form['link']).'","'.esc_sql($form['link_title']).'","'.esc_sql($form['general_calendar']).'")';
					}
					$sql='INSERT INTO '.CA_DATE_TBL.' (title,description,location,recurring,year_planner, event_image,event_id,how_many,cat_id,start_date,start_time,end_time,facilities_id,link,link_title,general_calendar)VALUES'.implode(",",$values);
				break;
				case '1':
					$values=array();
					for($x=0;$x<$form['how_many'];$x++)
					{
						$start_date=date('Y-m-d',strtotime("{$form['start_date']}+$x day"));
						$values[]='("'.esc_sql($form['title']).'","'.esc_sql($form['description']).'","'.esc_sql($form['location']).'","'.esc_sql($form['recurring']).'","'.esc_sql($form['year_planner']).'","'.$event_image.'","'.$event_id.'","'.esc_sql($form['how_many']).'","'.esc_sql($form['cat_id']).'","'.$start_date.'","'.$form['start_time'].'","'.$form['end_time'].'","'.esc_sql($form['facilities_id']).'","'.esc_sql($form['link']).'","'.esc_sql($form['link_title']).'","'.esc_sql($form['general_calendar']).'")';
					}
					$sql='INSERT INTO '.CA_DATE_TBL.' (title,description,location,recurring,year_planner, event_image,event_id,how_many,cat_id,start_date,start_time,end_time,facilities_id,link,link_title,general_calendar)VALUES'.implode(",",$values);
				break;
				case 'm':
					$values=array();
					for($x=0;$x<$form['how_many'];$x++)
					{
						$start_date=date('Y-m-d',strtotime("{$form['start_date']}+$x month"));
						$values[]='("'.esc_sql($form['title']).'","'.esc_sql($form['description']).'","'.esc_sql($form['location']).'","'.esc_sql($form['recurring']).'","'.esc_sql($form['year_planner']).'","'.$event_image.'","'.$event_id.'","'.esc_sql($form['how_many']).'","'.esc_sql($form['cat_id']).'","'.$start_date.'","'.$form['start_time'].'","'.$form['end_time'].'","'.esc_sql($form['facilities_id']).'","'.esc_sql($form['link']).'","'.esc_sql($form['link_title']).'","'.esc_sql($form['general_calendar']).'")';
					}
					$sql='INSERT INTO '.CA_DATE_TBL.' (title,description,location,recurring,year_planner, event_image,event_id,how_many,cat_id,start_date,start_time,end_time,facilities_id,link,link_title,general_calendar)VALUES'.implode(",",$values);
				break;
				case 'a':
					$values=array();
					for($x=1;$x<$form['how_many'];$x++)
					{
						$start_date=date('Y-m-d',strtotime("{$form['start_date']}+$x year"));
						$values[]='("'.esc_sql($form['title']).'","'.esc_sql($form['description']).'","'.esc_sql($form['location']).'","'.esc_sql($form['recurring']).'","'.esc_sql($form['year_planner']).'","'.$event_image.'","'.$event_id.'","'.esc_sql($form['how_many']).'","'.esc_sql($form['cat_id']).'","'.$start_date.'","'.$form['start_time'].'","'.$form['end_time'].'","'.esc_sql($form['facilities_id']).'","'.esc_sql($form['link']).'","'.esc_sql($form['link_title']).'","'.esc_sql($form['general_calendar']).'")';
					}
					$sql='INSERT INTO '.CA_DATE_TBL.' (title,description,location,recurring,year_planner, event_image,event_id,how_many,cat_id,start_date,start_time,end_time,facilities_id,link,link_title,general_calendar)VALUES'.implode(",",$values);
				break;

			}

			$wpdb->query($sql);
			echo'<div class="notice notice-success inline"><p><strong>'.__('Date(s) saved','church-admin').'</strong></p></div>';
		}
		else{echo'<div class="notice notice-success inline"><p><strong>'.__('Date(s) already saved','church-admin').'</strong></p></div>';}
		church_admin_new_calendar(strtotime($form['start_date']),$facilities_id);
	}//end process
	else
	{



		if(empty($facilities_id)){echo'<h2>'.$edit.' '.__('Calendar Item','church-admin').'</h2>';}else{echo '<h2>'.$edit.' '.__( 'Facility Booking','church-admin').'</h2>';}
		echo'<form action="" enctype="multipart/form-data" id="calendar" method="post">';
		echo'<table class="form-table">';
		if(empty($error))$error =new stdClass();
		if(empty($data)) $data = new stdClass();
		if(!empty($data->event_id))
		{
			$multi=$wpdb->get_var('SELECT COUNT(*) FROM '.CA_DATE_TBL.' WHERE event_id="'.esc_sql($data->event_id).'"');
			if($multi>1)
			{
				echo'<tr><th scope=row>'.__('Single or Series Edit?','church-admin').'</th><td><input type="radio" name="edit_type" value="single" checked="checked"/> '.__('Single or ','church-admin').'<input type="radio" name="edit_type" value="series"/> '.__('Series','church-admin').'</td></tr>';
				if(!empty($data->event_id))echo '<tr><th scope="row">&nbsp;</th><td><a class="button" href="'.wp_nonce_url('admin.php?page=church_admin/index.php&amp;action=church_admin_series_event_delete&tab=calendar&tab=calendar&amp;event_id='.$data->event_id.'&amp;date_id='.$data->date_id,'series_event_delete').'">'.__('Delete this series event','church-admin').'</a></td></tr>';
			}
		}
		if(!empty($data->date_id))echo '<tr><th scope="row">&nbsp;</th><td><a  class="button" href="'.wp_nonce_url('admin.php?page=church_admin/index.php&amp;action=church_admin_single_event_delete&tab=calendar&amp;event_id='.$data->event_id.'&amp;date_id='.$data->date_id,'single_event_delete').'">'.__('Delete this single event','church-admin').'</a></td></tr>';
		echo church_admin_calendar_form($data,$error,1,$date,$facilities_id);
		echo '<tr><th scope="row">&nbsp;</th><td><input type="submit" class="button-primary" name="edit_event" value="'.__('Save Event','church-admin').'"/></td></tr></table></form>';



		}

}



function church_admin_calendar_form($data,$error,$recurring=1,$date,$facilities_id)
{

    global $wpdb;
	if(empty($data)) $data=new stdClass();


    $out='  <script type="text/javascript" src="'.plugins_url('includes/javascript.js',dirname(__FILE__) ) . '"></script>
<script type="text/javascript">document.write(getCalendarStyles());</script>
<script type="text/javascript">
var cal_begin = new CalendarPopup(\'pop_up_cal\');
function OnChange(dropdown){
if(document.getElementById(\'recurring\').value==\'s\'){
		document.getElementById(\'nth\').style.display = \'none\';
		document.getElementById(\'howmany\').style.display = \'none\';
		}
if(document.getElementById(\'recurring\').value==\'1\'){
		document.getElementById(\'nth\').style.display = \'none\';
		document.getElementById(\'howmany\').style.display = \'table-row\';
		}
if(document.getElementById(\'recurring\').value==\'7\'){
		document.getElementById(\'nth\').style.display = \'none\';
		document.getElementById(\'howmany\').style.display = \'table-row\';;
		}
if(document.getElementById(\'recurring\').value==\'14\'){
		document.getElementById(\'nth\').style.display = \'none\';
		document.getElementById(\'howmany\').style.display = \'table-row\';;
		}
if(document.getElementById(\'recurring\').value==\'n\'){
		document.getElementById(\'nth\').style.display = \'table-row\';
		document.getElementById(\'howmany\').style.display = \'table-row\';
		}
if(document.getElementById(\'recurring\').value==\'m\'){
		document.getElementById(\'nth\').style.display = \'none\';
		document.getElementById(\'howmany\').style.display = \'table-row\';
		}
if(document.getElementById(\'recurring\').value==\'a\'){
		document.getElementById(\'nth\').style.display = \'none\';
		document.getElementById(\'howmany\').style.display = \'table-row\';
		}
}
</script>';
$out.='<tr><th scope="row">'.__('Event Title','church-admin').'</th><td><input type="text" name="title" ';
if(!empty($data->title))$out.=' value="'.stripslashes($data->title).'" ';
if(!empty($error->title))$out.=$error->title;
$out.=' /></td></tr>';
$out.='<tr><th scope=row>'.__('Photo','church-admin').'</th><td><input type="file" id="photo" name="uploadfiles" size="35" class="uploadfiles" /></td></tr>';
if(!empty($data->event_image))
		{//photo available
			$out.= '<tr><th scope="row">'.__('Current Photo','church-admin').'</th><td>';
			$out.= wp_get_attachment_image( $data->event_image,'ca-people-thumb' );
			$out.='</td></tr>';
		}//photo available
		else
		{
			$out.= '<tr><th scope="row">&nbsp;</th><td>';
			$out.= '<img src="'.plugins_url('images/default-avatar.jpg',dirname(__FILE__) ) .'" width="75" height="75"/>';
			$out.= '</td></tr>';
		}
$out.='<tr><th scope="row">'.__('Event Description','church-admin').'</th><td><textarea rows="5" cols="50" name="description" ';
if(!empty($error->description))$out.=$error->description;
$out.='>';
if(!empty($data->description))$out.=stripslashes($data->description);
$out.='</textarea></td></tr>';
$out.='<tr><th scope="row">'.__('Event Location','church-admin').'</th><td><textarea rows="5" cols="50" name="location" ';
if(!empty($error->location))$out.=$error->location;
$out.='>';

if(!empty($data->location))$out.=stripslashes($data->location);
$out.='</textarea></td></tr>';
$out.='<tr><th scope="row">'.__('Facility/Room','church-admin').'</th><td><select name="facilities_id"> ';
if(!empty($facilities_id))
{
	$first=$option='';
	$facility_name=$wpdb->get_var('SELECT facility_name FROM '.CA_FAC_TBL.' WHERE facilities_id="'.esc_sql($facilities_id).'"');
	if(!empty($facilitiy_name))
	{
		$first='<option value="'.$facilities_id.'" selected="selected" >'.$facility_name.'</option>';
	}
	else
	{
		$option='<option value="'.$facilities_id.'" >'.$facility_name.'</option>';
	}
	$out.=$first.$option;
}
else{$out.='<option value="">'.__('N/A','church-admin').'</option>';}
$facs=$wpdb->get_results('SELECT * FROM '.CA_FAC_TBL.' ORDER BY facilities_order');
	if(!empty($facs))
	{
		foreach($facs AS $fac){$out.='<option value="'.$fac->facilities_id.'">'.$fac->facility_name.'</option>';}

	}
$out.='</select></td></tr>';

$out.='<tr><th scope="row"> '.__('Category','church-admin').'</th><td><select name="cat_id" ';
if(!empty($error->category)) $out.=$error->category;
$out.=' >';
$select='';
$first='<option value="">'.__('Please select','church-admin').'...</option>';
$sql="SELECT * FROM ".$wpdb->prefix."church_admin_calendar_category";
$result3=$wpdb->get_results($sql);
foreach($result3 AS $row)
{
    if(!empty($data->cat_id)&&$data->cat_id==$row->cat_id)
    {

        $first='<option value="'.$data->cat_id.'" style="background:'.$data->bgcolor.'" selected="selected">'.$data->category.'</option>';
    }
    else
    {
        $select.='<option value="'.$row->cat_id.'" style="background:'.$row->bgcolor.'">'.$row->category.'</option>';
    }
}

$out.=$first.$select;//have original value first!


$out.='</select></td></tr>';

if(!empty($data->recurring))
{
	$startdate=$wpdb->get_var('SELECT start_date FROM '.CA_DATE_TBL.' WHERE event_id="'.intval($data->event_id).'" ORDER BY start_date ASC LIMIT 1');
	$out.='<tr><th colspan=2>'.esc_html(sprintf(__('First date of series is %1$s, use that for "Start Date" if editing the whole series','church-admin'),$startdate)).'</th></tr>';
}
$out.='
<tr><th scope=row>'.__('Start Date','church-admin').'</th><td><input name="start_date" id="start_date" type="text"';
if(!empty($error->start_date))$out.=$error->start_date;
if(!empty($date))$out.=' value="'.$date.'"';
if(!empty($data->start_date))$out.=' value="'.mysql2date('Y-m-d',$data->start_date).'"';
$out.=' size="25" /></td></tr>';
$out.='<script type="text/javascript">
      jQuery(document).ready(function(){
         jQuery(\'#start_date\').datepicker({
            dateFormat : "'."yy-mm-dd".'", changeYear: true ,yearRange: "2011:'.date('Y',time()+60*60*24*365*10).'"
         });
      });
   </script>';
if($recurring==1){
    $out.='<tr><th scope="row">'.__('Recurring','church-admin').'</th><td><select name="recurring" ';
	if(!empty($error->recurring))$out.=$error->recurring;
	$out.=' id="recurring" onchange="OnChange(\'recurring\')">';
	if(!empty($data->recurring))
	{
		$option=array('s'=>__('Once','church-admin'),'1'=>__('Daily','church-admin'),'7'=>__('Weekly','church-admin'),'n'=>__('nth day eg.1st Friday','church-admin'),'m'=>__('Monthly','church-admin'),'a'=>__('Annually','church-admin'));
		$out.= '<option value="'.$data->recurring.'">'.$option[$data->recurring].'</option>';
	}
	$out.='<option value="s">'.__('Once','church-admin').'</option><option value="1">'.__('Daily','church-admin').'</option><option value="7">'.__('Weekly','church-admin').'</option><option value="14">'.__('Fortnightly','church-admin').'</option><option value="n">'.__('nth day (eg 1st Friday)','church-admin').'</option><option value="m">'.__('Monthly on same date','church-admin').'</option><option value="a">'.__('Annually','church-admin').'</option></select></td></tr><tr id="nth" ';
	if(!empty($data->recurring)&&$data->recurring=='n'){$out.='';}else{$out.='style="display:none"';}
	$out.='><th scope="row">'.__('Recurring on','church-admin').' </th><td><select ';
	if(!empty($error->nth))$out.=$error->nth;$out.=' name="nth">';
	if(!empty($data->nth)) $out.='<option value="'.$data->nth.'">'.$data->nth.'</option>';
	$out.='<option value="1">'.__('1st','church-admin').'</option><option value="2">'.__('2nd','church-admin').'</option><option value="3">'.__('3rd','church-admin').'</option><option value="4">'.__('4th','church-admin').'</option><option value="5">'.__('5th','church-admin').'</option></select>&nbsp;<select name="day"><option value="0">'.__('Sunday','church-admin').'</option><option value="1">'.__('Monday','church-admin').'</option><option value="2">'.__('Tuesday','church-admin').'</option><option value="3">'.__('Wednesday','church-admin').'</option><option value="4">'.__('Thursday','church-admin').'</option><option value="5">'.__('Friday','church-admin').'</option><option value="6">'.__('Saturday','church-admin').'</option></select></td></tr>';

	$out.='<tr id="howmany"';
	if(!empty($data->recurring) && $data->recurring!='s'){$out.='';}else{$out.='style="display:none"';}
	$out.='><th scope="row">'.__('How many times in all?','church-admin').'</th><td><input type="text" ';
	if(!empty($error->how_many)) $out.=$error->how_many;
	$out.=' name="how_many" ';
	if(!empty($data->how_many))$out.=' value="'.$data->how_many.'"';
	$out.='/></td></tr></div>';
}//end recurring
else
{
    $out.='<input type="hidden" name="recurring" value="s"/><input type="hidden" name="how_many" value="1"/>';
}
	if(!empty($data->start_time))$data->start_time=substr($data->start_time,0,5);//remove seconds
	if(!empty($data->end_time))$data->end_time=substr($data->end_time,0,5);//remove seconds
	$out.='<tr><th scope="row">'.__('Start Time of form HH:MM','church-admin').'</th><td><input type="text" name="start_time" ';
if(!empty($error->start_time))$out.=$error->start_time;
if(!empty($data->start_time))$out.=' value="'.$data->start_time.'"';
$out.='/></td></tr>';
$out.='<tr><th scope="row">'.__('End Time of form HH:MM','church-admin').'</th><td><input type="text" name="end_time" ';
if(!empty($error->end_time)) $out.=$error->end_time;
if(!empty($data->end_time))$out.=' value="'.$data->end_time.'" ';
$out.='/></td></tr>';
$out.='<tr><th scope="row">'.__('All day','church-admin').'</th><td><input type="checkbox" name="all_day" ';

if(!empty($data->start_time)&&$data->start_time='00:00' &&!empty($data->end_time)&&$data->end_time=='23:59')$out.=' checked="checked" ';
$out.='/></td></tr>';
/********************************
*
* Add a link
*
*********************************/
$out.='<tr><th>'.__('Add a link to event page/post','church-admin').'</th><td><input type="text" name="link1" id="cal-link" ';
if(!empty($data->link))$out.=' value="'.esc_html($data->link).'" ';
$out.='placeholder="'.__('Add a link','church-admin').'"/><br/>';
$out.='<select name="link2" class="cal-link">';
//add in a dropdown of pages and post_status
$out.='<option value="">'.__('Select a post or page','church-admin').'</option>';
$out.=' <optgroup label="'.__('Posts','church-admin').'">';
$args = array( 'numberposts' => 10);
$postlinks = get_posts($args);
foreach( $postlinks as $postlink ) { setup_postdata($postlink); $out.='<option value="'.get_permalink($postlink->ID).'">'.$postlink->post_title.'</option>';}
$out.='</optgroup>';
$out.=' <optgroup label="'.__('Pages','church-admin').'">';
$args = array( 'post_type'=>'page','numberposts' =>-1,'orderby'=>'title','order'=>'ASC');
$postlinks = get_posts($args);
foreach( $postlinks as $postlink ) { setup_postdata($postlink); $out.='<option value="'.get_permalink($postlink->ID).'">'.$postlink->post_title.'</option>';}
$out.='</optgroup';
$out.='</select>';

$out.='</td><tr>';
$out.='<tr><th scope="row">'.__('Link title','church-admin').'</th><td><input type="text" name="link_title" ';
if(!empty($data->link_title)){$out.=' value="'.esc_html($data->link_title).'" ';}else{$out.=' value="'.__('More information','church-admin').'" ';}
$out.='/></td></tr>';
$out.='<tr><th scope="row">'.__('Appear on Year Planner?','church-admin').'</th><td><input type="checkbox" name="year_planner" value="1"';
if(!empty($data->year_planner)) $out.=' checked="checked"';
$out.='/>';
$out.='<tr><th scope="row">'.__('Appear on General Calendar?','church-admin').'</th><td><input type="checkbox" name="general_calendar" value="1"  checked="checked"/>';
$out.='<input type="hidden" name="save_date" value="yes"/></p>';
/*****************************************
*
*				 Links Javascript
*
******************************************/
$out.='<script type="text/javascript">';
$out.='jQuery(document).ready(function($) {$(".cal-link").on("change",function(){
			var link=$(".cal-link").val();
			console.log(link);
			$("#cal-link").val(link);

});
});</script>';
return $out;
}




function church_admin_single_event_delete($date_id)
{
    global $wpdb;
    $date=$wpdb->get_var('SELECT start_date FROM '.CA_DATE_TBL.' WHERE date_id="'.esc_sql($date_id).'"');
    $wpdb->query('DELETE FROM '.CA_DATE_TBL.' WHERE date_id="'.esc_sql($date_id).'"');
    echo '<div id="message" class="notice notice-success inline">';
    echo '<p><strong>'.__('Calendar Events deleted','church-admin').'.</strong></p>';
    echo '</div>';


    church_admin_new_calendar(strtotime($date));
}

function church_admin_series_event_delete($event_id)
{
    global $wpdb;
    $date=$wpdb->get_var('SELECT MIN(start_date) FROM '.CA_DATE_TBL.' WHERE event_id="'.esc_sql($event_id).'"');
    $wpdb->query('DELETE FROM '.CA_DATE_TBL.' WHERE event_id="'.esc_sql($event_id).'"');
    echo '<div id="message" class="notice notice-success inline">';
    echo '<p><strong>'.__('Calendar Events deleted','church-admin').'.</strong></p>';
    echo '</div>';
    church_admin_new_calendar(strtotime($date));
}


function church_admin_calendar_error_check($data)
{
    global $error,$sqlsafe;
     //check startdate
      $start_date=church_admin_dateCheck($data['start_date']);

      $end_date=church_admin_dateCheck($data['end_date'], $yearepsilon=50);

      if($start_date){$sqlsafe['start_date']=esc_sql($start_date);}else{$error->start_date==1;}

      //check start time
   if (preg_match ("/([0-2]{1}[0-9]{1}):([0-5]{1}[0-9]{1})/", $data['start_time'])){$sqlsafe['start_time']=$data['start_time'];}else{$error['start_time']='1';}
        //check end time
  if (preg_match("/([0-2]{1}[0-9]{1}):([0-5]{1}[0-9]{1})/", $data['end_time'])){$sqlsafe['end_time']=$data['end_time'];}else{$error->end_time='1';}

      //check recurring
      if($data['recurring']=='s'||$data['recurring']=='1'||$data['recurring']=='7'||$data['recurring']=='14'||$data['recurring']=='n'||$data['recurring']=='m'||$data['recurring']=='a'){$sqlsafe['recurring']=$data['recurring'];}else{$error['recurring']=1;}
      //check how many
      if($data['recurring']!='s')
      {
        if(ctype_digit($data['how_many']))
        {
            $sqlsafe['how_many']=$data['how_many'];
        }
        else
        {
            $error->how_many=1;
        }
      }
      //check nth if necessary
      if($data['recurring']=='n')
        {
            if(!empty($data['nth']) && $data['nth']<='4')
            {
                $sqlsafe['nth']=$data['nth'];$sqlsafe['day']=$data['day'];
            }
            else
            {
                $error->nth=$error['day']=1;
            }
        }
       if(!empty($data['title'])){ $sqlsafe['title']= esc_sql($data['title']);}else{$error->title=1;}
       if(!empty($data['description'])){ $sqlsafe['description']= esc_sql(nl2br($data['description']));}else{$error->description=1;}
       $sqlsafe['description']=strip_tags($sqlsafe['description']);
      $sqlsafe['location']=esc_sql($data['location']);
      if(!empty($_POST['category'])&&ctype_digit($data['category'])){$sqlsafe['category']=$data['category'];}else{$error['category']=1;}
      if($data['year_planner']=='1'){$sqlsafe['year_planner']=1;}else{$sqlsafe['year_planner']=0;}

    return $error;
}


function church_admin_calendar_list()
{
    global $wpdb;
    if(empty($_REQUEST['date'])){$entereddate=time();}else{$entereddate=$_REQUEST['date'];}
   echo'<div class="wrap church_admin"><p><a href="admin.php?page=church_admin/index.php&amp;action=church_admin_add_calendar&amp;date='.$entereddate.'">'.__('Add calendar Event','church-admin').'</a></p>';
$events=$wpdb->get_var('SELECT COUNT(*) FROM '.CA_DATE_TBL);
 if(!empty($events))
{
     //which month to view
    $current=(isset($_REQUEST['date'])) ? intval($_REQUEST['date']) : time(); //get user date or use today
    $next = strtotime("+1 month",$current);
    $previous = strtotime("-1 month",$current);
    $now=date("M Y",$current);
    $sqlnow=date("Y-m%", $current);
    $sqlnext=date("Y-m-d",$next);

    echo '<table><tr><td><a href="admin.php?page=church_admin/index.php&tab=calendar&amp;action=church_admin_calendar_list&amp;date='.$previous.'">'.__('Prev','church-admin').'</a> '.$now.' <a href="admin.php?page=church_admin/index.php&tab=calendar&amp;action=church_admin_calendar_list&amp;date='.$next.'">'.__('Next','church-admin').'</a></td><td>';
    echo'<form action="admin.php?page=church_admin/index.php&amp;action=church_admin_calendar_list" method="post"><select name="date">';
	echo '<option value="'.$entereddate.'">'.date('M Y',$entereddate).'</option>';
//generate a form to access calendar
for($x=0;$x<12;$x++)
{
    $date=strtotime("+ $x month",time());
    echo '<option value="'.$date.'">'.date('M Y',$date).'</option>';
}
echo '</select><input type="submit" value="'.__('Go to date','church-admin').'"/></form></td></tr></table>';
    //initialise table
    $table='<table class="widefat striped"><thead><tr><th>'.__('Single Edit','church-admin').'</th><th>'.__('Series Edit','church-admin').'</th><th>'.__('Single Delete','church-admin').'</th><th>'.__('Series Delete','church-admin').'</th><th>'.__('Start date','church-admin').'</th><th>'.__('Start Time','church-admin').'</th><th>'.__('End Time','church-admin').'</th><th>'.__('Event Name','church-admin').'</th><th>'.__('Category','church-admin').'</th><th>'.__('Year Planner','church-admin').'?</th></tr></thead><tfoot><tr><th>'.__('Single Edit','church-admin').'</th><th>'.__('Series Edit','church-admin').'</th><th>'.__('Single Delete','church-admin').'</th><th>'.__('Series Delete','church-admin').'</th><th>'.__('Start date','church-admin').'</th><th>'.__('Start Time','church-admin').'</th><th>'.__('End Time','church-admin').'</th><th>'.__('Event Name','church-admin').'</th><th>'.__('Category','church-admin').'</th><th>'.__('Year Planner','church-admin').'?</th></tr></tfoot><tbody>';


	$sql='SELECT a.*,b.category FROM '.CA_DATE_TBL.' a, '.CA_CAT_TBL.' b WHERE a.cat_id=b.cat_id AND a.start_date LIKE "'.$sqlnow.'" ORDER BY a.start_date';

   $result=$wpdb->get_results($sql);
    foreach($result AS $row)
    {
    //create links
    $single_edit_url='<a href="'.wp_nonce_url('admin.php?page=church_admin/index.php&amp;action=church_admin_single_event_edit&tab=calendar&amp;event_id='.$row->event_id.'&amp;date_id='.$row->date_id.'&amp;'.$entereddate,'single_event_edit').'">'.__('Edit','church-admin').'</a>';
    if($row->recurring=='s'){$series_edit_url='&nbsp;';}else{$series_edit_url='<a href="'.wp_nonce_url('admin.php?page=church_admin/index.php&amp;action=church_admin_series_event_edit&tab=calendar&amp;event_id='.$row->event_id.'&amp;date_id='.$row->date_id.'&amp;date='.$entereddate,'series_event_edit').'">'.__('Edit Series','church-admin').'</a>';}
    $single_delete_url='<a href="'.wp_nonce_url('admin.php?page=church_admin/index.php&amp;action=church_admin_single_event_delete&amp;event_id='.$row->event_id.'&amp;date_id='.$row->date_id.'&amp;date='.$entereddate,'single_event_delete').'">'.__('Delete this one','church-admin').'</a>';

    if($row->recurring=='s'){$series_delete_url='&nbsp;';}else{$series_delete_url='<a href="'.wp_nonce_url('admin.php?page=church_admin/index.php&amp;action=church_admin_series_event_delete&amp;event_id='.$row->event_id.'&amp;date_id='.$row->date_id.'&amp;date='.$entereddate,'series_event_delete').'">'.__('Delete Series','church-admin').'</a>';}

    //sort out category
    if(empty($row->bgcolor))$row->bgcolor='#FFF';
     $table.='<tr><td>'.$single_edit_url.'</td><td>'.$series_edit_url.'</td><td>'.$single_delete_url.'</td><td>'.$series_delete_url.'</td><td>'.mysql2date(get_option('date_format'),$row->start_date).'</td><td>'.esc_html($row->start_time).'</td><td>'.esc_html($row->end_time).'</td><td>'.esc_html($row->title).'</td><td style="background:'.$row->bgcolor.'">'.esc_html($row->category).'</td><td>';
     if($row->year_planner){$table.=__('Yes','church-admin');}else{$table.='&nbsp;';}
     $table.='</td></tr>';
    }
    $table.='</tbody></table>';
    echo $table.'</div>';
}//end of non empty calendar table

}





if(!function_exists('array_to_object')) {
  function array_to_object($array = array()) {
    if (!empty($array)) {
        $data = false;
        foreach ($array as $akey => $aval) {
            $data -> {$akey} = $aval;
        }
        return $data;
    }
    return false;
}

}

function nthday($nth,$day,$date)
{
    global $wp_locale;
    $month=date('M',strtotime($date));
    $year=date('Y',strtotime($date));
    return date('Y-m-d',strtotime("+$nth {$wp_locale->get_weekday($day)} $month $year"));
}
?>
