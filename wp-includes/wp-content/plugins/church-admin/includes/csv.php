<?php

if ( ! defined( 'ABSPATH' ) ) exit('You need Jesus!'); // Exit if accessed directly
/**
 *
 * outputs address list csv according to filters
 *
 * @author  Andy Moyle
 * @param
 * @return   application/octet-stream
 * @version  1.03
 *
 * rewritten 7th July 2016 to use filters from filter.php
 * refactored 11th April 2016 to remove multi-service bug
 *
 */
function church_admin_people_csv()
{
	global $wpdb;
	$group_by='';
	$gdpr=$custom=$months=$years=$member_types=$parents=$genders=$people_types=$sites=$smallgroups=$ministries=array();
	$customSQL=$monthSQL=$yearSQL=$marritalSQL=$genderSQL=$memberSQL=$peopleSQL=$smallgroupsSQL=$ministriesSQL=$filteredby=array();
	$gdprSQL='';

	require_once('filter.php');
	$sql= church_admin_build_filter_sql($_GET['check']);

	$gender=get_option('church_admin_gender');
	$results=$wpdb->get_results($sql);

	if(!empty($results))
	{
		$table_header=array(__('First name','church-admin'),__('Last name','church-admin'),__('Date of birth','church-admin'),__('People type','church-admin'),__('Marital status','church-admin'),__('Phone','church-admin'),__('Cellphone','church-admin'),__('Email','church-admin'),__('Address','church-admin'),__('Venue','church-admin'),__('Gender','church-admin'));
		$csv='"'.iconv('UTF-8', 'ISO-8859-1',implode('","',$table_header)).'"'."\r\n";
		foreach($results AS $row)
		{
			if(!empty($row->first_name)){$csv.='"'.iconv('UTF-8', 'ISO-8859-1',$row->first_name).'",';}else $csv.='"",';
			if(!empty($row->last_name)){$csv.='"'.iconv('UTF-8', 'ISO-8859-1',$row->last_name).'",';}else $csv.='"",';
			if(!empty($row->date_of_birth)){$csv.='"'.iconv('UTF-8', 'ISO-8859-1',$row->date_of_birth).'",';}else $csv.='"",';
			if(!empty($ptypes[$row->people_type_id])){$csv.='"'.iconv('UTF-8', 'ISO-8859-1',$ptypes[$row->people_type_id]).'",';}else $csv.='"",';
			if(!empty($row->marital_status)){$csv.='"'.iconv('UTF-8', 'ISO-8859-1',$row->marital_status).'",';}else $csv.='"'.__('N/A','church-admin').'",';
			if(!empty($row->phone)){$csv.='"'.iconv('UTF-8', 'ISO-8859-1',$row->phone).'",';}else $csv.='"",';
			if(!empty($row->mobile)){$csv.='"'.iconv('UTF-8', 'ISO-8859-1',$row->mobile).'",';}else $csv.='"",';
			if(!empty($row->email)){$csv.='"'.$row->email.'",';}else $csv.='"",';
			if(!empty($row->address)){$csv.='"'.iconv('UTF-8', 'ISO-8859-1',$row->address).'",';}else $csv.='"",';
			if(!empty($row->venue)){$csv.='"'.iconv('UTF-8', 'ISO-8859-1',$row->venue).'",';}else $csv.='"",';
			//if(!empty($row->group_name)){$csv.='"'.iconv('UTF-8', 'ISO-8859-1',$row->group_name).'",';}else $csv.='"",';
			if(!empty($row->sex)){$csv.='"'.iconv('UTF-8', 'ISO-8859-1',$gender[$row->sex]).'",';}else $csv.='"",';
			$csv.="\r\n";
		}

	header('Content-Description: File Transfer');
	header('Content-Type: application/octet-stream');
	header('Content-Disposition: attachment; filename="filtered-address-list.csv"');
	header('Content-Transfer-Encoding: binary');
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	header("Content-Disposition: attachment; filename=\"filtered-address-list.csv\"");
	echo $csv;
	}
	exit();



}

/**
 *
 * Rota CSV
 *
 * @author  Andy Moyle
 * @param
 * @return   application/octet-stream
 * @version  1.2500
 *
 * 2018-01-13 Fixed
 *
 */
function church_admin_rota_csv($service_id)
{
	global $wpdb;
	$debug=FALSE;
	//get service name
	$service=$wpdb->get_var('SELECT service_name FROM '.CA_SER_TBL.' WHERE service_id="'.intval($service_id).'"');
	//get required rota tasks
	$requiredRotaJobs=church_admin_required_rota_jobs($service_id);
	if(!empty($debug))church_admin_debug(print_r($requiredRotaJobs,TRUE));
	//get next twelve weeks of rota_jobs foreach rota task
	//first grab next twelve weeks of services
	$rotaDatesResults=$wpdb->get_results('SELECT * FROM '.CA_ROTA_TBL.' WHERE service_id="'.intval($service_id).'" AND mtg_type="service" AND rota_date>=CURDATE() GROUP BY rota_date');
	//grab people for each job and each date and populate $rota array
	$rota=array();

	foreach($rotaDatesResults AS $rotaDateRow)
	{

		foreach($requiredRotaJobs AS $rota_task_id=>$value)
		{
			$rota[$rotaDateRow->rota_date][$rota_task_id]=urldecode(church_admin_encode(church_admin_rota_people($rotaDateRow->rota_date,$rota_task_id,$service_id,'service')));

		}
	}
	if(!empty($debug))church_admin_debug(print_r($rota,TRUE));

	//create csv
	$csv='';

	$csvRow=array();
	//table header
	$csvRow[]=__('Date','church-admin');
	foreach($requiredRotaJobs AS $rota_task_id=>$rota_task)
	{
		$csvRow[]='"'.urldecode(church_admin_encode($rota_task)).'"';
	}
	$csv.=implode(",",$csvRow)."\r\n";
	//table data

	foreach($rota AS $date=>$data)
	{
		$csvRow=array(0=>$date);

		//rest of columns for that row
		foreach($data AS$key=>$value)
		{
			$csvRow[]='"'.urldecode(church_admin_encode($value)).'"';
		}
		$csv.=implode(",",$csvRow)."\r\n";
	}
	$filename="Rota-for-service-".esc_html($service).".csv";
	header("Cache-Control: public");
	header("Content-Description: File Transfer");
	header("Content-Disposition: attachment; filename=$filename");
	header("Content-Type: text/csv");
	header("Content-Transfer-Encoding: binary");
	echo $csv;
}
