<?php

if ( ! defined( 'ABSPATH' ) ) exit('You need Jesus!'); // Exit if accessed directly

function church_admin_photo_directory($member_type_id=0,$kids=TRUE)
{
	global $wpdb;


  	$out='';

  	$memb_sql='';
  	$membsql=$sitesql=array();
  	if($member_type_id!=0)
  	{
  		$memb=explode(',',$member_type_id);
      	foreach($memb AS $key=>$value){if(ctype_digit($value))  $membsql[]='a.member_type_id='.$value;}
      	if(!empty($membsql)) {$memb_sql=' ('.implode(' || ',$membsql).')';}
	}
	if(empty($kids)){$kids_sql=' AND a.people_type_id=1 ';}else{$kids_sql="";}
	//build query adding relevant member_types and sites
      $sql='SELECT a.*,a.attachment_id AS image, b.* FROM '.CA_PEO_TBL.' a, '.CA_HOU_TBL.' b WHERE (b.privacy=0 OR b.privacy IS NULL) AND a.household_id=b.household_id ';
	  if(!empty($memb_sql)||!empty($site_sql)) $sql.=' AND ';
	  $sql.=$memb_sql;
		$sql.=$kids_sql;
	  $sql.='   ORDER BY last_name ASC ';

	$results=$wpdb->get_results($sql);
	if(!empty($results))
	{
		//initilaise pdf
		require_once(plugin_dir_path(dirname(__FILE__)).'includes/fpdf.php');
		class PDF extends FPDF
		{
			function Header()
			{
				$this->SetXY(10,10);
				$this->SetFont('Arial','B',18);
				$title=get_option('blogname').' '.__('Address List','church-admin').' '.date(get_option('date_format'));
				$this->Cell(0,8,$title,0,1,'C');
				$this->Ln(5);
			}
		}
		$pdf = new PDF();

		$pdf->SetFont('Arial','',14);
		$pdf->AddPage('P',get_option('church_admin_pdf_size'));
		$y=25;
		foreach($results AS $row)
		{
			//image

			$imagePath=plugin_dir_path( __DIR__).'images/default-avatar.jpg';
			if(!empty($row->image))
			{
				$imagePath=church_admin_scaled_image_path($row->image,'thumbnail') ;
			}

			//output image on left hand side
			if(!empty($imagePath))$pdf->Image($imagePath,10,$y,25);//added test for imagePath to stop errors 2018-04-09 AM
			$x=35;
			$name=array_filter(array($row->first_name,$row->middle_name,$row->prefix,$row->last_name));
			$pdf->SetX(35);
			$pdf->SetFont('Arial','B',14);
			$pdf->Cell(0,8,urldecode(church_admin_encode(implode(' ',$name))),0,2,'L');
			$pdf->SetFont('Arial','',14);
			$pdf->Cell(0,8,urldecode(church_admin_encode($row->address)),0,2,'L');
			$contacts=array_filter(array($row->phone,$row->mobile,$row->email));
			$pdf->Cell(0,8,implode(', ',$contacts),0,2,'L');
			$y+=30;
			if($y>=260){$pdf->AddPage('P',get_option('church_admin_pdf_size'));$y=25;}
			$pdf->SetY($y);
		}
		$pdf->Output();
	}
	else{echo __('No people found','church-admin');}
}




function church_admin_cron_pdf()
{
    //setup pdf
    require_once(plugin_dir_path(dirname(__FILE__)).'includes/fpdf.php');
    $pdf=new FPDF();
    $pdf->AddPage('P','A4');
    $pdf->SetFont('Arial','B',24);
    $text=__('How to set up Bulk Email Queuing','church-admin');
    $pdf->Cell(0,10,$text,0,2,'L');
    if (PHP_OS=='Linux')
    {
    $phppath='/usr/local/bin/php -f ';

    $cronpath=plugin_dir_path(dirname(__FILE__)).'includes/cronemail.php';

	update_option('church_admin_cron_path',$cronpath);
	$command=$phppath.$cronpath;


    $pdf->SetFont('Arial','',8);
    $text="Instructions for Linux servers and cpanel.\r\nLog into Cpanel which should be ".get_bloginfo('url')."/cpanel using your username and password. \r\nOne of the options will be Cron Jobs which is usually in 'Advanced Tools' at the bottom of the screen. Click on 'Standard' Experience level. that will bring up something like this... ";

    $pdf->MultiCell(0, 10, $text,0,'L' );

    $pdf->Image(plugin_dir_path( dirname(__FILE__) ).'images/cron-job1.jpg','10','65','','','jpg','');
    $pdf->SetXY(10,180);
    $text="In the common settings option - select 'Once an Hour'. \r\nIn 'Command to run' put this:\r\n".$command."\r\n and then click Add Cron Job. Job Done. Don't forget to test it by sending an email to yourself at a few minutes before the hour! ";
    $pdf->MultiCell(0, 10, $text,0,'L' );
    }
    else
    {
         $pdf->SetFont('Arial','',10);
        $text=__("Unfortunately setting up queuing for email using cron is nonon Linux servers. Please go back to Communication settings and enable the wp-cron option for scheduling sending of queued emails",'church-admin');
        $pdf->MultiCell(0, 10, $text );
    }
    $pdf->Output();


}



function church_admin_backup_pdf()
{
    //setup pdf
    require_once(plugin_dir_path(dirname(__FILE__)).'includes/fpdf.php');
    $pdf=new FPDF();
    $pdf->AddPage('P','A4');
    $pdf->SetFont('Arial','B',24);
    $text=__('How to set up auto backup','church-admin');
    $pdf->Cell(0,10,$text,0,2,'L');
    if (TRUE)//PHP_OS=='Linux')
    {
    $phppath='/usr/local/bin/php -f ';

    $cronpath=plugin_dir_path(dirname(__FILE__)).'includes/cronbackup.php';

	update_option('church_admin_cron_path',$cronpath);
	$command=$phppath.$cronpath;


    $pdf->SetFont('Arial','',8);
    $text="Instructions for Linux servers and cpanel.\r\nLog into Cpanel which should be ".get_bloginfo('url')."/cpanel using your username and password. \r\nOne of the options will be Cron Jobs which is usually in 'Advanced Tools' at the bottom of the screen. Click on 'Standard' Experience level. that will bring up something like this... ";

    $pdf->MultiCell(0, 10, $text,0,'L' );

    $pdf->Image(plugin_dir_path( dirname(__FILE__) ).'images/cron-job1.jpg','10','65','','','jpg','');
    $pdf->SetXY(10,180);
    $text="In the common settings option - select 'Every Day' or every week. \r\nIn 'Command to run' put this:\r\n".$command."\r\n and then click Add Cron Job. Job Done.\r\n The backups are in the wp-content/uploads/church-admin-cache directory- with random filenames.sgl.gz \r\nPick the right date and restore through phpmyadmin import feature!";
    $pdf->MultiCell(0, 10, $text,0,'L' );
    }
    else
    {
         $pdf->SetFont('Arial','',10);
        $text=__("Unfortunately setting up queuing for backup using cron is not possible with non Linux servers. Please go back to Communication settings and enable the wp-cron option for scheduling sending of queued emails",'church-admin');
        $pdf->MultiCell(0, 10, $text );
    }
    $pdf->Output();


}



/**
 *
 * Small Group PDF
 *
 * @author  Andy Moyle
 * @param    $member_type_id,$people_type_id
 * @return
 * @version  0.1
 *
 */
function church_admin_smallgroup_pdf($member_type_id,$people_type_id)
{
    global $wpdb,$people_type;
	$member_type=church_admin_member_type_array();
	require_once(plugin_dir_path(dirname(__FILE__)).'includes/fpdf.php');

	$smallgroups=$groupnames=array();
	$leader=array();
	//get groups
	$results=$wpdb->get_results('SELECT group_name,id FROM '.CA_SMG_TBL);
	if(!empty($results))
	{
		foreach($results AS $row){$smallgroups[$row->id]=array();$groupnames[$row->id]=urldecode(church_admin_encode($row->group_name));}

		//grab people
		//handle people_type_id
		$ptype_sql='';
		if(!empty($people_type_id))
		{
			if(!is_array($people_type_id)){$ptype=explode(',',$people_type_id);}else{$ptype=$people_type_id;}

			foreach($ptype AS $key=>$value){if(ctype_digit($value))  $ptypesql[]='a.people_type_id='.$value;}
			if(!empty($ptypesql)) {$ptype_sql=' AND ('.implode(' OR ',$ptypesql).')';}else{$ptype_sql=' ';}
		}
		//handle member_type_id
		$memb_sql='';
		if($member_type_id!=0)
		{
			if(!is_array($member_type_id)){$memb=explode(',',$member_type_id);}else{$memb=$member_type_id;}
			foreach($memb AS $key=>$value){if(ctype_digit($value))  $membsql[]='a.member_type_id='.$value;}
			if(!empty($membsql)) {$memb_sql=' AND ('.implode(' OR ',$membsql).')';}
		}
		//build query of people
		$sql='SELECT DISTINCT CONCAT_WS(" ",a.first_name,a.prefix, a.last_name) AS name,b.ID,c.group_name FROM '.CA_PEO_TBL.' a, '.CA_MET_TBL.' b, '.CA_SMG_TBL.' c WHERE a.people_id=b.people_id AND b.ID=c.id AND b.meta_type="smallgroup" '.
		$memb_sql.$ptype_sql.'  ORDER BY a.last_name ';

		$peopleresults = $wpdb->get_results($sql);
		$count=$wpdb->num_rows;

		$gp=0;

		foreach ($peopleresults as $people)
		{
			$people->name=stripslashes($people->name);

			if(!empty($people->name))$smallgroups[$people->ID][]=urldecode(church_admin_encode($people->name));

		}
		$noofgroups=$wpdb->get_row('SELECT COUNT(id) AS no FROM '.CA_SMG_TBL);
		$counter=$noofgroups->no;
		$pdf=new FPDF();
		$pageno=0;
		$x=10;
		$y=20;
		$w=1;
		$width=55;
		$pdf->AddPage('L',get_option('church_admin_pdf_size'));
		$pdf->SetFont('Arial','B',16);

		$whichtype=$whichptype=array();
		foreach($memb AS $key=>$value) $whichtype[]=$member_type[$value];//list of member types for title
		$people_type=get_option('church_admin_people_type');

		foreach($ptype AS $key=>$value) $whichptype[]=$people_type[$value];
		$text=implode(", ",$whichtype).' '.__('Small Group List','church-admin').' '.date(get_option('date_format')).'  '.$count.' '.implode(", ",$whichptype);
		$pdf->Cell(0,10,urldecode(church_admin_encode($text)),0,2,'C');
		$pageno+=1;



	foreach($groupnames AS $z=>$groupname)
	{
		$text='';
		if($w==6)
		{
			$pdf->SetFont('Arial','B',16);
			$pdf->AddPage('L',get_option('church_admin_pdf_size'));

			$whichtype=array();
			foreach($memb AS $key=>$value)$whichtype[]=$member_type[$value];
			$text=implode(", ",$whichtype).' '.__('Small Group List','church-admin').' '.date(get_option('date_format')).'  '.$count.' '.implode(", ",$whichptype);
			$pdf->Cell(0,10,urldecode(church_admin_encode($text)),0,2,'C');
			$x=10;
			$y=20;
			$w=1;
		}
		$newx=$x+(($w-1)*$width);
		if($pageno>1) {$newx=$x+(($z-($pageno*5))*$width);}
		$pdf->SetXY($newx,$y);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell($width,8,urldecode(church_admin_encode($groupname)),1,1,'C');
		$pdf->SetFont('Arial','',10);
		$pdf->SetXY($newx,$y+8);
		if(!empty($smallgroups[$z]))
		{
			$pdf->SetFont('Arial','B',10);
			$pdf->SetFont('Arial','',10);
			$text='';
			for($a=0;$a<count($smallgroups[$z]);$a++)
			{
				$b=$a+1;
				if(!empty($smallgroups[$z][$a]))$text.=$b.') '.$smallgroups[$z][$a]."\n";
			}
			$pdf->MultiCell($width,5,urldecode(church_admin_encode($text))."\n",1);

			$pdf->SetX($newx);
		}

		$pdf->Cell($width,0,"",'LB',2,'L');
		$w++;
	}
	$pdf->Output();
}
}
/**
 *
 * Address PDF
 *
 * @author  Andy Moyle
 * @param    $member_type_id
 * @return
 * @version  0.1
 *
 */
function church_admin_address_pdf($member_type_id=1)
{

//update 2014-03-19 to allow for multiple surnames
//;update 2016-12-13 Left join grabbing household ids
	//initilaise pdf
	require_once(plugin_dir_path(dirname(__FILE__)).'includes/fpdf.php');
	class PDF extends FPDF
	{
		function Header()
		{
			$this->SetXY(10,10);
			$this->SetFont('Arial','B',18);
			$title=get_option('blogname').' '.__('Address List','church-admin').' '.date(get_option('date_format'));
			$this->Cell(0,8,$title,0,1,'C');
			$this->Ln(5);
		}
	}
	$pdf = new PDF();
	$pdf->SetAutoPageBreak(1,10);
	$pdf->AddPage('P',get_option('church_admin_pdf_size'));


  global $wpdb;
//address book cache
$memb_sql='';
  	if(!empty($member_type_id)&&$member_type_id!=0)
  	{
  			if(!is_array($member_type_id)){$memb=explode(',',$member_type_id);}else{$memb=$member_type_id;}
      	foreach($memb AS $key=>$value){if(ctype_digit($value))  $membsql[]='a.member_type_id='.$value;}
      	if(!empty($membsql)) {$memb_sql=' AND ('.implode(' || ',$membsql).')';}
	}
$sql='SELECT a.household_id,b.privacy FROM '.CA_PEO_TBL.' a LEFT JOIN '.CA_HOU_TBL.' b on a.household_id=b.household_id WHERE (b.privacy=0 OR b.privacy iS NULL) '.$memb_sql.'  GROUP BY a.household_id ORDER BY a.last_name ASC ';
  $results=$wpdb->get_results($sql);

  $counter=1;
    $addresses=array();
	foreach($results AS $ordered_row)
	{
		$y=$pdf->GetY();

		$address=$wpdb->get_row('SELECT * FROM '.CA_HOU_TBL.' WHERE household_id="'.esc_sql($ordered_row->household_id).'"');
		$people_results=$wpdb->get_results('SELECT * FROM '.CA_PEO_TBL.' WHERE household_id="'.esc_sql($ordered_row->household_id).'" ORDER BY people_type_id ASC,sex DESC');
		$adults=$children=$emails=$mobiles=$photos=array();
		$last_name='';
		$x=0;
		foreach($people_results AS $people)
		{
			if($people->people_type_id=='1')
			{
				if(!empty($people->prefix)){$prefix=$people->prefix.' ';}else{$prefix='';}
				$last_name=$prefix.$people->last_name;
				$adults[$last_name][]=$people->first_name;
				if(!empty($people->email)&&$people->email!=end($emails)) $emails[$people->first_name]=$people->email;
				if(!empty($people->mobile)&&$people->mobile!=end($mobiles))$mobiles[$people->first_name]=$people->mobile;
				if(!empty($people->attachment_id))$photos[$people->first_name]=$people->attachment_id;
				$x++;
			}
			else
			{
				$children[]=$people->first_name;
				if(!empty($people->attachment_id))$photos[$people->first_name]=$people->attachment_id;
			}

		}
		//create output
		array_filter($adults);$adultline=array();
		foreach($adults as $lastname=>$firstnames){$adultline[]=implode(" & ",$firstnames).' '.$lastname;}
		//address name of adults in household
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(0,5,urldecode(church_admin_encode(implode(" & ",$adultline))),0,1,'L');
		$pdf->SetFont('Arial','',10);
		//children
		if(!empty($children))$pdf->Cell(0,5,urldecode(church_admin_encode(implode(", ",$children))),0,1,'L');
		//address if stored
		if(!empty($address->address)){$pdf->Cell(0,5,urldecode(church_admin_encode($address->address)),0,1,'L');}
		//emails
		if (!empty($emails))
		{
			array_unique($emails);
			if(count($emails)<2 && $x<=1)
			{
				$pdf->Cell(0,5,urldecode(church_admin_encode(end($emails))),0,1,'L',FALSE,'mailto:'.end($emails));
			}
			else
			{//more than one email in household
				$text=array();
				foreach($emails AS $name=>$email)
				{
					$content=$name.': '.$email;
					if($email!=end($emails))
					$width=$pdf->GetStringWidth($content);
					$pdf->Cell(0,5,urldecode(church_admin_encode($content)),0,1,'L',FALSE,'mailto:'.$email);

				}


			}
		}
		if ($address->phone)$pdf->Cell(0,5,urldecode(church_admin_encode($address->phone)),0,1,'L',FALSE,'tel:'.$address->phone);
		if (!empty($mobiles))
		{
			array_unique($mobiles);
			if(count($mobiles)<2 && $x<=1)
			{
				$pdf->Cell(0,5,urldecode(church_admin_encode(end($mobiles))),0,0,'L',FALSE,'tel:'.end($mobiles));
			}
			else
			{//more than one mobile in household
				$text=array();
				foreach($mobiles AS $name=>$mobile)
				{
					$content=$name.': '.$mobile;
					if($mobile!=end($mobiles))$content.=', ';
					$width=$pdf->GetStringWidth($content);
					$pdf->Cell($width,5,urldecode(church_admin_encode($content)),0,0,'L',FALSE,'tel:'.$mobile);
				}

			}
			$pdf->Ln(5);
		}
	$pdf->Ln(5);
    }


$pdf->Output();


}



function church_admin_label_pdf($member_type_id=0)
{
	global $wpdb;

	//Build people sql statement from filters
	$group_by=$other='';
	$member_types=$genders=$people_types=$sites=$smallgroups=$ministries=array();
	$genderSQL=$maritalSQL=$memberSQL=$peopleSQL=$smallgroupsSQL=$ministriesSQL=$filteredby=array();
	require_once('filter.php');
	$sql= church_admin_build_filter_sql($_GET['check']);

	$results = $wpdb->get_results($sql);
	if($results)
	{
    	require_once('PDF_Label.php');
  	  	$pdflabel = new PDF_Label(get_option('church_admin_label'), 'mm', 1, 2);
	    //$pdflabel->Open();
	    $pdflabel->SetFont('Arial','B',10);
  	  	$pdflabel->AddPage();
    	$counter=1;
    	$addresses=array();
    	foreach ($results as $row)
    	{

			$name=implode(" ",array_filter(array($row->first_name.$row->prefix,$row->last_name)));
			$address=urldecode(church_admin_encode($row->address));
			$row->address=str_replace(", ",",",$row->address);
			$add=explode(",",$row->address);
			$add=urldecode(church_admin_encode($name."\n".implode(",\n",$add)));
	    	$pdflabel->Add_Label($add);

    	}

		$pdflabel->Output();

	//end of mailing labels
	}
}


function ca_vcard($id)
{
  global $wpdb;

    $query='SELECT * FROM '.CA_HOU_TBL.' WHERE household_id="'.esc_sql($id).'"';

	$add_row = $wpdb->get_row($query);
    $address=$add_row->address;
    $phone=$add_row->phone;
    $people_results=$wpdb->get_results('SELECT * FROM '.CA_PEO_TBL.' WHERE household_id="'.esc_sql($id).'" ORDER BY people_type_id ASC,sex DESC');
    $adults=$children=$emails=$mobiles=array();
      foreach($people_results AS $people)
	{
	  if($people->people_type_id=='1')
	  {
	    $last_name=$people->last_name;
	    $adults[]=$people->first_name;
	    if($people->email!=end($emails)) $emails[]=$people->email;
	    if($people->mobile!=end($mobiles))$mobiles[]=$people->mobile;

	  }
	  else
	  {
	    $children[]=$people->first_name;
	  }
	  if(!empty($people->attachment_id))
		{
			$photo=wp_get_attachment_image_src( $people->attachment_id, 'ca-people-thumb',0,$attr );

		}
	}
  //prepare vcard
require_once(plugin_dir_path(dirname(__FILE__)).'includes/vcf.php');
$v = new vCard();
if(!empty($add_row->phone))$v->setPhoneNumber($add_row->phone, "PREF;HOME;VOICE");
if(!empty($mobiles))$v->setPhoneNumber("{$mobiles['0']}", "CELL;VOICE");
$v->setName("{$last_name}", implode(" & ",$adults), "", "");

$v->setAddress('',$add_row->address,'','','','','','HOME;POSTAL' );
$v->setEmail("{$emails['0']}");

if(!empty($children)){$v->setNote("Children: ".implode(", ",$children));}
if(!empty($photo))
{

	$t=exif_imagetype($photo['0']);
	switch($t)
		{
			case 1:$type='GIF';break;
			case 2:$type='JPG';break;

		}
	if(!empty($type))$v->setPhoto($type,$photo[0]);
}

$output = $v->getVCard();
$filename=$last_name.'.vcf';


    header("Cache-Control: public");
    header("Content-Description: File Transfer");
    header("Content-Disposition: attachment; filename=$filename");
    header("Content-Type: text/x-vcard");
    header("Content-Transfer-Encoding: binary");

   echo $output;

}

function church_admin_year_planner_pdf($initial_year)
{
    if(empty($initial_year))$initial_year==date('Y');
    global $wpdb;
	$days=array(0=>__('Sun','church-admin'),1=>__('Mon','church-admin'),2=>__('Tues','church-admin'),3=>__('Weds','church-admin'),4=>__('Thur','church-admin'),5=>__('Fri','church-admin'),6=>__('Sat','church-admin'));
//check cache admin exists
$upload_dir = wp_upload_dir();
$dir=$upload_dir['basedir'].'/church-admin-cache/';


//initialise pdf
require_once(plugin_dir_path(dirname(__FILE__)).'includes/fpdf.php');
$pdf=new FPDF();
$pdf->AddPage('L','A4');

$pageno=0;
$x=10;
$y=5;
//Title
$pdf->SetXY($x,$y);
$pdf->SetFont('Arial','B',18);
$title=get_option('blogname');
$pdf->Cell(0,8,urldecode(church_admin_encode($title)),0,0,'C');
$pdf->SetFont('Arial','B',10);

//Get initial Values
$initial_month='01';
if(empty($initial_year))$initial_year=date('Y');
$month=0;

$row=0;
$current=time();
$this_month = (int)date("m",$current);
$this_year = date( "Y",$current );

for($quarter=0;$quarter<=3;$quarter++)
{
for($column=0;$column<=2;$column++)
{//print one of the three columns of months
    $x=10+($column*80);//position column
    $y=15+(44*$quarter);
    $pdf->SetXY($x,$y);
    $this_month=date('m',strtotime($initial_year.'-'.$initial_month.'-01 + '.$month.' month'));
    $this_year=date('Y',strtotime($initial_year.'-'.$initial_month.'-01 + '.$month.' month'));
    // find out the number of days in the month
    $numdaysinmonth = cal_days_in_month( CAL_GREGORIAN, $this_month, $this_year );
    // create a calendar object
    $jd = cal_to_jd( CAL_GREGORIAN, $this_month,date( 1 ), $this_year );
    // get the start day as an int (0 = Sunday, 1 = Monday, etc)
    $startday = jddayofweek( $jd , 0 );
    // get the month as a name
    $monthname = jdmonthname( $jd, 1 );
    $month++;//increment month for next iteration
    $pdf->SetFont('Arial','B',10);
    $pdf->Cell(70,7,$monthname.' '.$this_year,0,0,'C');
    //position to top left corner of calendar month
    $y+=7;
    $pdf->SetXY($x,$y);
    $pdf->SetFont('Arial','',8);
    //print daylegend
    for($day=0;$day<=6;$day++)$pdf->Cell(10,5,$days[$day],1,0,'C');

    $y+=5;
    $pdf->SetXY($x,$y);
    for($monthrow=0;$monthrow<=5;$monthrow++)
    {//print 6 weeks

        for($day=0;$day<=6;$day++)
        {
            if($monthrow==0 && $day==$startday)$counter=1;//month has started
            if($monthrow==0 && $day<$startday)
            {
                //empty cells before start of month, so fill with grey colour
                $pdf->SetFillColor('192','192','192');
                $pdf->Cell(10,5,'',1,0,'L',TRUE);
            }
            else
            {
                //during month so category background
                $sql='SELECT a.bgcolor FROM '.CA_CAT_TBL.' a, '.CA_DATE_TBL.' b WHERE b.year_planner="1" AND a.cat_id=b.cat_id AND b.start_date="'.$this_year.'-'.$this_month.'-'.sprintf('%02d',$counter).'" LIMIT 1';

				$bgcolor=$wpdb->get_var($sql);
                if(!empty($bgcolor))
                {
                    $colour=html2rgb($bgcolor);
                    $pdf->SetFillColor($colour[0],$colour[1],$colour[2]);
                }
                else
                {
                    $pdf->SetFillColor(255,255,255);
                }

                 if($counter <= $numdaysinmonth)
                {
                    //duringmonth so print a date
                    $pdf->Cell(10,5,$counter,1,0,'L',TRUE);
                    $counter++;
                }
                else
                {
                //end of month, so back to grey background
                $pdf->SetFillColor('192','192','192');
                $pdf->Cell(10,5,'',1,0,'C',TRUE);
                }
            }



        }
        $y+=5;

        $pdf->SetXY($x,$y);
    }

}//end of column
}//end row

//Build key
$x=250;
$y=23;
 $pdf->SetFont('Arial','',8);
$result=$wpdb->get_results("SELECT * FROM ".$wpdb->prefix."church_admin_calendar_category");
foreach ($result AS $row)
{

    $pdf->SetXY($x,$y);
    $colour=html2rgb($row->bgcolor);
    $pdf->SetFillColor($colour[0],$colour[1],$colour[2]);
    $pdf->Cell(15,5,' ',0,0,'L',1);
    $pdf->SetFillColor(255,255,255);
    $pdf->Cell(15,5,urldecode(church_admin_encode($row->category)),0,0,'L');
    $pdf->SetXY($x,$y);
    $pdf->Cell(45,5,'',1);
    $y+=6;
}
$pdf->Output();

}


function html2rgb($color)
{
    if ($color[0] == '#')
        $color = substr($color, 1);

    if (strlen($color) == 6)
        list($r, $g, $b) = array($color[0].$color[1],
                                 $color[2].$color[3],
                                 $color[4].$color[5]);
    elseif (strlen($color) == 3)
        list($r, $g, $b) = array($color[0].$color[0], $color[1].$color[1], $color[2].$color[2]);
    else
        return false;

    $r = hexdec($r); $g = hexdec($g); $b = hexdec($b);

    return array($r, $g, $b);
}





function church_admin_small_group_xml()
{
	global $wpdb, $wp_locale;
	$days=array(0=>__('Sunday','church-admin'),1=>__('Monday','church-admin'),2=>__('Tuesday','church-admin'),3=>__('Wednesday','church-admin'),4=>__('Thursday','church-admin'),5=>__('Friday','church-admin'),6=>__('Saturday','church-admin'));
	$results=$wpdb->get_results('SELECT * FROM '.CA_SMG_TBL.' WHERE lat!="" AND lng!="" ORDER BY group_day, group_time');
	if(!empty($results))
	{
		$color_def = array
	('1'=>"FF0000",'2'=>"00FF00",'3'=>"0000FF",'4'=>"FFF000",'5'=>"00FFFF",'6'=>"FF00FF",'7'=>"CCCCCC",	8  => "FF7F00",	9  => "7F7F7F",	10 => "BFBFBF",	11 => "007F00",
		12 => "7FFF00",	13 => "00007F",	14 => "7F0000",	15 => "7F4000",
		16 => "FF9933",	17 => "007F7F",	18 => "7F007F",	19 => "007F7F",
		20 => "7F00FF",	21 => "3399CC",	22 => "CCFFCC",	23 => "006633",
		24 => "FF0033",	25 => "B21919",	26 => "993300",	27 => "CC9933",
		28 => "999933",	29 => "FFFFBF",	30 => "FFFF7F",31  => "000000"
	);

		header("Content-type: text/xml;charset=utf-8");
		echo '<markers>';
		$x=1;
		foreach($results AS $row)
		{

			// Iterate through the rows, printing XML nodes for each

			// ADD TO XML DOCUMENT NODE
				echo '<marker ';
				echo 'pinColor="'.$color_def[$x].'" ';
				echo 'lat="' . $row->lat . '" ';
				echo 'lng="' . $row->lng . '" ';
				echo 'smallgroup_name="'.htmlentities($row->group_name).'" ';
				echo 'address="'.htmlentities($row->address).'" ';
				if(!empty($row->whenwhere)){echo 'when="'.htmlentities($row->whenwhere).'" ';}
				else {echo 'when="'.htmlentities($days[$row->group_day].' '.mysql2date(get_option('time_format'),$row->group_time)).'" ';}
				echo 'smallgroup_id="'.$row->id.'" ';
				echo '/>';
				$x++;
		}
		// End XML file
		echo '</markers>';

	}
}


/**
* This function produces a xml of people in various categories
*
* @author     	andymoyle
* @param		$member_type_id comma separated,$small_group BOOL
* @return		pdf
*
*/
function church_admin_address_xml($member_type_id=1,$show_small_group=1)
{
    global $wpdb;

	$markers='<markers>';
    $color_def = array(	'1'=>"FF0000",'2'=>"00FF00",'3'=>"0000FF",'4'=>"FFF000",'5'=>"00FFFF",'6'=>"FF00FF",'7'=>"CCCCCC",'8'  => "FF7F00",
	9  => "7F7F7F",	10 => "BFBFBF",	11 => "007F00",
		12 => "7FFF00",	13 => "00007F",	14 => "7F0000",	15 => "7F4000",
		16 => "999933",	17 => "007F7F",	18 => "7F007F",	19 => "007F7F",
		20 => "7F00FF",	21 => "3399CC",	22 => "CCFFCC",	23 => "006633",
		24 => "000033",	25 => "B21919",	26 => "993300",	27 => "CC9933",
		28 => "FF9933",	29 => "FFFFBF",	30 => "FFFF7F",31  => "000000"
	);
	//grab relevant households
	$memb_sql='';
  	if($member_type_id!=0)
  	{
  		$memb=explode(',',$member_type_id);
      	foreach($memb AS $key=>$value){if(ctype_digit($value))  $membsql[]='member_type_id='.$value;}
      	if(!empty($membsql)) {$memb_sql=' WHERE ('.implode(' || ',$membsql).')';}
	}
	$sql='SELECT household_id FROM '.CA_PEO_TBL.$memb_sql.'  GROUP BY household_id ORDER BY last_name ASC ';
	$results=$wpdb->get_results($sql);
    if(!empty($results))
	{
		foreach($results AS $row)
		{
			$address=$wpdb->get_row('SELECT * FROM '.CA_HOU_TBL.' WHERE household_id="'.esc_sql($row->household_id).'"');
			$sql='SELECT a.* FROM '.CA_PEO_TBL.' a  WHERE a.household_id="'.esc_sql($row->household_id).'" ORDER BY a.people_order, a.people_type_id ASC,sex DESC';
			$people_results=$wpdb->get_results($sql);

			$adults=$children=$emails=$mobiles=$photos=array();
			$last_name='';
			$x=0;
			$markers.= '<marker ';
			foreach($people_results AS $people)
			{

				if($people->people_type_id=='1')
				{
					if(!empty($people->prefix)){$prefix=$people->prefix.' ';}else{$prefix='';}
					$last_name=$prefix.$people->last_name;
					$adults[$last_name][]=$people->first_name;

					$smallgroup_id=church_admin_get_people_meta($people->people_id,'smallgroup');
					if(!empty($smallgroup_id[0]))$smallgroup=$wpdb->get_row('SELECT * FROM '.CA_SMG_TBL.' WHERE id="'.$smallgroup_id[0].'"');
							//small group data for marker

							if(!empty($smallgroup)&&!empty($show_small_group))
							{
								if(empty($smallgroup->group_name))$smallgroup->group_name=' ';
								if(empty($smallgroup->address))$smallgroup->address=' ';
								if(empty($smallgroup->whenwhere))$smallgroup->whenwhere=' ';
								$sg=array();
								$sg[]=  'pinColor="'.$color_def[$smallgroup->id].'" ';
								$sg[]= 'smallgroup_id="'.$smallgroup->id.'" ';
								$sg[]= 'smallgroup_name="'.htmlentities($smallgroup->group_name).'" ';
								$sg[]=  'small_group_address="'.htmlentities($smallgroup->address).'" ';
								$sg[]=  'when="'.htmlentities($smallgroup->whenwhere).'" ';
							}
							else
							{$sg=array();
								$sg[]= 'pinColor="FF0000" ';
							}
					$x++;
				}
				else
				{
					if(!empty($people->prefix)){$prefix=$people->prefix.' ';}else{$prefix='';}
					$last_name=$prefix.$people->last_name;
					$children[$last_name][]=$people->first_name;

				}

			}
			$markers.=implode(" ",$sg);
			//address data for marker
			$markers.= 'lat="' . $address->lat . '" ';
			$markers.= 'lng="' . $address->lng . '" ';
			$markers.= 'address="'. $address->address.'" ';

			//people data
			array_filter($adults);
			$adultline=array();
			//the join statement makes sure the array is imploded like this ",,,&"
			//http://stackoverflow.com/questions/8586141/implode-array-with-and-add-and-before-last-item
			foreach($adults as $lastname=>$firstnames){$adultline[]=join(' &amp; ', array_filter(array_merge(array(join(', ', array_slice($firstnames, 0, -1))), array_slice($firstnames, -1)))).' '.$lastname;}
			$markers.='adults_names="'.implode(" &amp; ",$adultline). '" ';
			array_filter($children);
			$childrenline=array();
			foreach($children as $lastname=>$firstnames){$childrenline[]=join(' &amp; ', array_filter(array_merge(array(join(', ', array_slice($firstnames, 0, -1))), array_slice($firstnames, -1)))).' '.$lastname;}
			$markers.='childrens_names="'.implode(" &amp; ",$childrenline). '" ';
			$markers.= '/>';
		}
		$markers.='</markers>';
		header("Content-type: text/xml;charset=utf-8");
		echo $markers;
	}

    exit();
}


/**
* This function produces a pdf of people in each ministry
*
* @author     	andymoyle
* @param		none
* @return		pdf
*
*/
function church_admin_ministry_pdf()
{
	global $wpdb;
	$ministries=$ministry_names=array();
	$results=$wpdb->get_results('SELECT ministry,ID FROM '.CA_MIN_TBL.' ORDER BY ministry ASC');
	foreach($results AS $row)$ministry_names[intval($row->ID)]=$row->ministry;

	foreach($ministry_names AS $key=>$ministry_name)
	{
			$sql='SELECT CONCAT_WS(" ",a.first_name,a.prefix,a.last_name) AS name FROM '.CA_PEO_TBL.' a, '.CA_MET_TBL.' b WHERE b.meta_type="ministry" AND a.people_id=b.people_id AND b.ID="'.esc_sql($key).'" ORDER BY a.last_name';
			$ministries[$ministry_name]=array();
			$people=$wpdb->get_results($sql);
			if(!empty($people))
			{
				foreach($people AS $person) {$ministries[$ministry_name][]=$person->name;}
			}

	}

	require_once(plugin_dir_path(dirname(__FILE__)).'includes/fpdf.php');
	$pdf=new FPDF();
	$pdf->AddPage('L',get_option('church_admin_pdf_size'));

	$pdf->SetFont('Arial','B',12);
	$pdf->Cell(0,10,__('Ministries','church-admin'),0,0,'C');
	$pdf->SetFont('Arial','',10);
	$i=1;
	$x=15;
	$y=25;
	ksort($ministries);
	foreach($ministries AS $min_name=>$people)
	{
		if(empty($people))$people=array(0=>__('No-one yet','church-admin'));
		if($i>6)
		{
			$pdf->AddPage('L',get_option('church_admin_pdf_size'));$x=15;$x=25;$i=1;

			$pdf->SetFont('Arial','B',12);
			$pdf->Cell(0,6,__('Ministries','church-admin'),0,0,'C');

		}
		$pdf->SetXY($x,25);
		//ministry name
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(40,6,urldecode(church_admin_encode($min_name)),1,0,'C');
		$pdf->SetXY($x,31);
		//ministry people
		$pdf->SetFont('Arial','',10);
		$pdf->MultiCell(40,6,urldecode(church_admin_encode(implode("\n",$people))),1,'L');

		$i++;
		$x+=40;
		$y=30;
		$pdf->SetXY($x,$y);
	}
	$pdf->Output();
}




/**
* This function produces a pdf of people in each hope team
*
* @author     	andymoyle
* @param		none
* @return		pdf
*
*/
function church_admin_hope_team_pdf()
{
	global $wpdb;
	$hope_teams=$wpdb->get_results('SELECT * FROM '.CA_HOP_TBL);
	$hope_team_jobs=array();

	foreach($hope_teams AS $hope_team)
	{
			$sql='SELECT CONCAT_WS(" ",a.first_name,a.prefix,a.last_name) AS name , mobile, email FROM '.CA_PEO_TBL.' a, '.CA_MET_TBL.' b WHERE b.meta_type="hope_team" AND a.people_id=b.people_id AND b.ID="'.esc_sql($hope_team->hope_team_id).'" ORDER BY a.last_name';

			$people=$wpdb->get_results($sql);
			if(!empty($people))
			{
				foreach($people AS $person) {$hope_team_jobs[$hope_team->job][]=$person->name.' '.$person->mobile.' '.$person->email;}
			}

	}

	require_once(plugin_dir_path(dirname(__FILE__)).'includes/fpdf.php');
	$pdf=new FPDF();
	$pdf->AddPage('P',get_option('church_admin_pdf_size'));

	$pdf->SetFont('Arial','B',12);
	$pdf->Cell(0,10,__('Hope Team','church-admin'),0,1,'C');
	$pdf->SetFont('Arial','',10);
	$i=1;

	ksort($hope_team_jobs);
	foreach($hope_team_jobs AS $min_name=>$people)
	{

		//ministry name
		$pdf->SetFont('Arial','B',8);
		$pdf->Cell(0,6,urldecode(church_admin_encode($min_name)),1,1,'C');

		//ministry people
		$pdf->SetFont('Arial','',10);
		$pdf->MultiCell(0,6,urldecode(church_admin_encode(implode("\n",$people))),1,'L');
		$pdf->Ln(5);



	}
	$pdf->Output();
}

/**
 *
 * Kids work pdf
 *
 * @author  Andy Moyle
 * @param   Array $member_type_id
 * @return  pdf
 * @version  0.2
 *
 * 2017-01-10 - corrected sql to make override work properly
 */
function church_admin_kidswork_pdf($member_type_id)
{
	global $wpdb;

	$kidsworkGroups=$wpdb->get_results('SELECT * FROM '.CA_KID_TBL.' ORDER BY youngest DESC');
	$memb_sql='';
  	if($member_type_id!=0)
  	{
  		if(!is_array($member_type_id)){$memb=explode(',',$member_type_id);}else{$memb=$member_type_id;}
      	foreach($memb AS $key=>$value){if(ctype_digit($value))  $membsql[]='member_type_id='.$value;}
      	if(!empty($membsql)) {$memb_sql=' ('.implode(' OR ',$membsql).')';}
	}

	$member_type=church_admin_member_type_array();
	require_once(plugin_dir_path(dirname(__FILE__)).'includes/fpdf.php');
	//cache small group pdf

	$kidsworkgroups=$groupnames=array();
	$count=0;
	$leader=array();

	$count=$noofgroups=0;
	//get groups

	if(!empty($kidsworkGroups))
	{
		foreach($kidsworkGroups AS $row)
		{
			$noofgroups++;
			$groupname[$row->id]=iconv('UTF-8', 'ISO-8859-1',$row->group_name);//title first
			//corrected sql 2017-01-10 to make sure override works properly!
			$sql='SELECT CONCAT_WS(" ",first_name,last_name) AS name,kidswork_override FROM '.CA_PEO_TBL.' WHERE '.$memb_sql.' AND (kidswork_override="'.esc_sql($row->id).'" OR ((date_of_birth<"'.$row->youngest.'" AND date_of_birth>"'.$row->oldest.'") AND kidswork_override=0 )) ORDER BY last_name ';

			$peopleresults = $wpdb->get_results($sql);
			if(!empty($peopleresults))
			{
				$colCount=1;
				foreach($peopleresults AS $people)
				{
					$kidsworkgroups[$row->id][]=$colCount.') '.$people->name;
					$colCount++;//column count
					$count++;//total count for title area
				}
			}
		}
	}



	$counter=$noofgroups;

	$pdf=new FPDF();
	$pageno=0;
	$x=10;
	$y=20;
	$w=1;
	$width=55;
	$pdf->AddPage('L',get_option('church_admin_pdf_size'));
	$pdf->SetFont('Arial','B',16);

	$whichtype=array();

	$text=implode(", ",$whichtype).' '.__('Kidswork Group List','church-admin').' '.date(get_option('date_format')).'  '.$count.' '.__('people','church-admin');
	$pdf->Cell(0,10,urldecode(church_admin_encode($text)),0,2,'C');
	$pageno+=1;



	foreach($groupname AS $id=>$groupname)
	{
		$text='';
		if($w==6)
		{
			$pdf->SetFont('Arial','B',16);
			$pdf->AddPage('L',get_option('church_admin_pdf_size'));

			$whichtype=array();
			foreach($memb AS $key=>$value)$whichtype[]=$member_type[$value];
			$text=implode(", ",$whichtype).' '.__('Kidswork Group List','church-admin').' '.date(get_option('date_format')).'  '.$count.' '.__('people','church-admin');
			$pdf->Cell(0,10,urldecode(church_admin_encode($text)),0,2,'C');
			$x=10;
			$y=20;
			$w=1;
		}
		$newx=$x+(($w-1)*$width);
		if($pageno>1) {$newx=$x+(($z-($pageno*5))*$width);}
		$pdf->SetXY($newx,$y);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell($width,8,urldecode(church_admin_encode($groupname)),1,1,'C');
		$pdf->SetFont('Arial','',10);
		$pdf->SetXY($newx,$y+8);


			$pdf->SetFont('Arial','',10);
			$text='';
			if(!empty($kidsworkgroups[$id]))$text=implode("\n",$kidsworkgroups[$id]);
			$pdf->MultiCell($width,5,urldecode(church_admin_encode($text))."\n",'LRB');

			$pdf->SetX($newx);


		$pdf->Cell($width,0,"",'LB',2,'L');
		$w++;
	}
	$pdf->Output();
}

/**
 *
 * Horizontal PDF using new rota table and sized to fit
 *
 * @author  Andy Moyle
 * @param    $lengths, $fontSize
 * @return   array(orientation,font_size,widths)
 * @version  0.1
 *
 */
function church_admin_new_rota_pdf($service_id,$jobs)
{
	global $wpdb;
	$debug=TRUE;
	//get required rota tasks
	if(!empty($debug))church_admin_debug(print_r($jobs,TRUE));

	if(empty($service_id)) exit(__('No service specified','church-admin'));

	$colWidths=array('date'=>0);//array to find max length rota column
	$requiredRotaJobs=$rotaDates=array();
	$rota_tasks=$wpdb->get_results('SELECT * FROM '.CA_RST_TBL.' ORDER BY rota_order');
	if(empty($jobs))
	{
		foreach($rota_tasks AS $rota_task)
		{
			if(in_array($service_id,maybe_unserialize($rota_task->service_id)))
			{
				$requiredRotaJobs[$rota_task->rota_id]=$rota_task->rota_task;
				$colWidths[$rota_task->rota_id]=strlen($rota_task->rota_task);
			}
		}
	}
	else
	{
		//check that the $jobs array contains the right jobs!

		foreach($rota_tasks AS $rota_task)
		{
			if(in_array($rota_task->rota_id,$jobs)&&in_array($service_id,maybe_unserialize($rota_task->service_id)))
			{
				$requiredRotaJobs[$rota_task->rota_id]=$rota_task->rota_task;
				$colWidths[$rota_task->rota_id]=strlen($rota_task->rota_task);
			}
		}
	}
	church_admin_debug(print_r($requiredRotaJobs,TRUE));
	//get next twelve weeks of rota_jobs foreach rota task
	//first grab next twelve weeks of services
	$rotaDatesResults=$wpdb->get_results('SELECT * FROM '.CA_ROTA_TBL.' WHERE service_id="'.intval($service_id).'" AND mtg_type="service" AND rota_date>=CURDATE() GROUP BY rota_date LIMIT 24');
	//grab people for each job and each date and populate $rota array
	$rota=array();

	foreach($rotaDatesResults AS $rotaDateRow)
	{
		//longest date length
		$dateLength=strlen(mysql2date(get_option('date_format'),$rotaDateRow->rota_date));
		if($dateLength>$colWidths['date'])$colWidths['date']=$dateLength;
		//work through each row's column to find longest value
		foreach($requiredRotaJobs AS $rota_task_id=>$value)
		{
			if(!empty($_GET['initials']) &&in_array($rota_task_id,$_GET['initials']))
			{
				//use initials
				$rota[$rotaDateRow->rota_date][$rota_task_id]=urldecode(church_admin_encode(church_admin_rota_people_initials($rotaDateRow->rota_date,$rota_task_id,$service_id,'service')));
			}
			else
			{
							$rota[$rotaDateRow->rota_date][$rota_task_id]=urldecode(church_admin_encode(church_admin_rota_people($rotaDateRow->rota_date,$rota_task_id,$service_id,'service')));
			}
			$thisColWidth=strlen($rota[$rotaDateRow->rota_date][$rota_task_id]);
			if(empty($colWidths[$rota_task_id])||$thisColWidth>$colWidths[$rota_task_id]) $colWidths[$rota_task_id]=$thisColWidth;
		}
	}



	//create pdf
	require_once(plugin_dir_path(dirname(__FILE__)).'includes/fpdf.php');

	$fontSize=12;
	$pdf_settings=church_admin_pdf_settings($colWidths,$fontSize);
	while(!$pdf_settings&&$fontSize>=8){$fontSize--;$pdf_settings=church_admin_pdf_settings($colWidths,$fontSize);}
	if(!empty($debug))church_admin_debug(print_r($pdf_settings,TRUE));
	if(empty($pdf_settings))
	{

		$pdf=new FPDF('P','mm',get_option('church_admin_pdf_size'));
		$pdf->AddPage('P',get_option('church_admin_pdf_size'));
		$pdf->SetFont('Arial','',16);
		$service=$wpdb->get_var('SELECT service_name FROM '.CA_SER_TBL.' WHERE service_id="'.intval($service_id).'"');
		$pdf->SetFont('Arial','',16);
		$text=__('Schedule','church-admin').' '.$service;
		$pdf->Cell(0,10,$text,0,2,'C');
		$pdf->Cell(200,7,__('You have more data than can fit on a page','church-admin'),0,0,'C');
		$pdf->Output();
		exit();

	}

		$pdf=new FPDF($pdf_settings['orientation'],'mm',get_option('church_admin_pdf_size'));
		//initialise pdf
		$pdf->AddPage($pdf_settings['orientation'],get_option('church_admin_pdf_size'));



		//Title
		$service=$wpdb->get_var('SELECT service_name FROM '.CA_SER_TBL.' WHERE service_id="'.intval($service_id).'"');
		$pdf->SetFont('Arial','',16);
		$text=__('Schedule','church-admin').' '.urldecode(church_admin_encode($service));
		$pdf->Cell(0,10,urldecode(church_admin_encode($text)),0,2,'C');

		//Begin table
		$pdf->SetFont('Arial','B',$pdf_settings['font_size']);
		//table header
		$pdf->Cell($pdf_settings['widths']['date'],7,__('Date','church-admin'),1,0,'C');

		foreach($requiredRotaJobs AS $rota_task_id=>$rota_task)
		{
			$pdf->Cell($pdf_settings['widths'][$rota_task_id],7,urldecode(church_admin_encode($rota_task)),1,0,'C');

		}
		$pdf->Ln();
		//table data
		$pdf->SetFont('Arial','',$pdf_settings['font_size']);
		foreach($rota AS $date=>$data)
		{
			//1st column is date
			$pdf->Cell($pdf_settings['widths']['date'],7,mysql2date(get_option('date_format'),$date),1,0,'C');
			//rest of columns for that row
			foreach($data AS $key=>$value)
			{
				$pdf->Cell($pdf_settings['widths'][$key],7,urldecode(church_admin_encode($value)),1,0,'C');
			}
			$pdf->Ln();
		}
		$pdf->Output();

}

function church_admin_address_pdf_family_photos($member_type_id=1) {
	//initilaise pdf
	require_once(plugin_dir_path(dirname(__FILE__)).'includes/fpdf.php');
	class PDF extends FPDF
	{
		function Header()
		{
			$this->SetXY(10,10);
			$this->SetFont('Arial','B',18);
			$title=get_option('blogname').' '.__('Family Listing','church-admin').' '.date(get_option('date_format'));
			$this->Cell(0,8,$title,0,1,'C');
			$this->Ln(5);
		}
		function Footer() {
			$footerYLocation = $this->GetPageHeight() -5;
			$this->SetXY(10,$footerYLocation);
			$this->SetFont('Arial','',10);
			$footer=__('Page: ','church-admin').$this->PageNo();
			$this->Cell(0,5,$footer,0,1,'C');
		}
	}
	$pdf = new PDF();
	$pdf->SetAutoPageBreak(1,10);
	$pdf->AddPage('P',get_option('church_admin_pdf_size'));


  	global $wpdb;
	//address book cache
	$memb_sql='';
  	if($member_type_id!=0)
  	{
  		$memb=explode(',',$member_type_id);
      	foreach($memb AS $key=>$value){if(ctype_digit($value))  $membsql[]='a.member_type_id='.$value;}
      	if(!empty($membsql)) {$memb_sql=' AND ('.implode(' || ',$membsql).')';}
	}
	$sql='SELECT a.household_id,b.privacy,b.attachment_id AS household_image FROM '.CA_PEO_TBL.' a LEFT JOIN '.CA_HOU_TBL.' b on a.household_id=b.household_id WHERE (b.privacy=0 OR b.privacy iS NULL) '.$memb_sql.'  GROUP BY a.household_id ORDER BY a.last_name ASC ';
  	$results=$wpdb->get_results($sql);

  	$counter=1;
    $addresses=array();
    $y=25;
	foreach($results AS $ordered_row)
	{
		$outputlines = 0;
		$address=$wpdb->get_row('SELECT * FROM '.CA_HOU_TBL.' WHERE household_id="'.esc_sql($ordered_row->household_id).'"');
		$people_results=$wpdb->get_results('SELECT * FROM '.CA_PEO_TBL.' WHERE household_id="'.esc_sql($ordered_row->household_id).'" ORDER BY people_type_id ASC,sex DESC');
		$adults=$children=$emails=$mobiles=$photos=array();
		$last_name='';
		$x=0;
		$imagePath=plugin_dir_path(dirname(__FILE__)).'images/default-avatar.jpg';
		if(!empty($ordered_row->household_image))
		{
			$imagePath=church_admin_scaled_image_path($ordered_row->household_image,'thumbnail') ;
			church_admin_debug(print_r($imagePath,TRUE));
		}

		//output image on left hand side
		if(!empty($imagePath))$pdf->Image($imagePath,10,$y,25);//added test for imagePath to stop error 2018-04-09
		foreach($people_results AS $people)
		{
			if($people->people_type_id=='1')
			{
				if(!empty($people->prefix)){
					$prefix=$people->prefix.' ';
				}else{
					$prefix='';
				}
				$last_name=$prefix.$people->last_name;
				$adults[$last_name][]=$people->first_name;
				if(!empty($people->email)&&$people->email!=end($emails)) $emails[$people->first_name]=$people->email;
				if(!empty($people->mobile)&&$people->mobile!=end($mobiles))$mobiles[$people->first_name]=$people->mobile;
				if(!empty($people->attachment_id))$photos[$people->first_name]=$people->attachment_id;
				$x++;
			}
			else
			{
				$children[]=$people->first_name;
				if(!empty($people->attachment_id))$photos[$people->first_name]=$people->attachment_id;
			}

		}
		//create output
		array_filter($adults);$adultline=array();
		foreach($adults as $lastname=>$firstnames){$adultline[]=implode(" & ",$firstnames).' '.$lastname;}
		//address name of adults in household
		$pdf->SetX(35);
		$pdf->SetFont('Arial','B',10);
		$pdf->Cell(0,5,urldecode(church_admin_encode(implode(" & ",$adultline))),0,1,'L');
		$pdf->SetFont('Arial','',10);
		$outputlines += 1;
		//children
		if(!empty($children)){
			$pdf->SetX(35);
			$pdf->Cell(0,5,urldecode(church_admin_encode(implode(", ",$children))),0,1,'L');
			$outputlines += 1;
		}
		//address if stored
		if(!empty($address->address)){
			$pdf->SetX(35);
			$pdf->Cell(0,5,urldecode(church_admin_encode($address->address)),0,1,'L');
			$outputlines += 1;
		}
		//emails
		if (!empty($emails))
		{
			array_unique($emails);
			if(count($emails)<2 && $x<=1)
			{
				$pdf->SetX(35);
				$pdf->Cell(0,5,urldecode(church_admin_encode(end($emails))),0,1,'L',FALSE,'mailto:'.end($emails));
				$outputlines += 1;
			}
			else
			{//more than one email in household
				$text=array();
				foreach($emails AS $name=>$email)
				{
					$content=$name.': '.$email;
					if($email!=end($emails))
					$width=$pdf->GetStringWidth($content);
					$pdf->SetX(35);
					$pdf->Cell(0,5,urldecode(church_admin_encode($content)),0,1,'L',FALSE,'mailto:'.$email);
					$outputlines += 1;
				}


			}
		}
		if ($address->phone) {
			$pdf->SetX(35);
			$pdf->Cell(0,5,urldecode(church_admin_encode($address->phone)),0,1,'L',FALSE,'tel:'.$address->phone);
			$outputlines += 1;
		}
		if (!empty($mobiles)) {
			array_unique($mobiles);
			$pdf->SetX(35);
			if(count($mobiles)<2 && $x<=1) {
				$pdf->Cell(0,5,urldecode(church_admin_encode(end($mobiles))),0,0,'L',FALSE,'tel:'.end($mobiles));
				$outputlines += 1;
			}
			else {//more than one mobile in household
				$text=array();
				foreach($mobiles AS $name=>$mobile) {
					$content=$name.': '.$mobile;
					if($mobile!=end($mobiles))$content.=', ';
					$width=$pdf->GetStringWidth($content);
					$pdf->Cell($width,5,urldecode(church_admin_encode($content)),0,0,'L',FALSE,'tel:'.$mobile);
					//$outputlines += 1;
				}

			}
			$pdf->Ln(5);
			$outputlines += 1;
		}
		//We allow five lines of output for each family to fit within the picture area
		//More than 5 lines will require some line feeds to push the rest of the listing
		//down beyond the picture and provide more room for the next family.
		$addedlinefeedcount = $outputlines - 5;
		if($addedlinefeedcount < 0) {
			$addedlinefeedcount = 5 - $outputlines;
		}
		$pdf->Ln(5 * $addedlinefeedcount);
		$y = $y + ($addedlinefeedcount * 5) + 30;
		if($y>=250){
			$pdf->AddPage('P',get_option('church_admin_pdf_size'));
			$y=25;
		}
		$pdf->SetY($y);
    }


	$pdf->Output();


}


?>
