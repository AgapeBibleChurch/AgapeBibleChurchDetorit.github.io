<?php

/**
 *  Podcast Display
 *
 * @author 	Andy Moyle
 * @return 	$out
 * @version 1.2000
 *
 */
function church_admin_podcast_display($file_id=NULL,$excl)
{
	global $wpdb;
	$exclude=array();
	if(!empty($excl))
	{
		$exclude=explode(",",$excl);
	}
	if(!empty($file_id))
	{
		$out=church_admin_podcast_file($file_id,$exclude);
	}
	else
	{
		//build list of files
		$list=church_admin_podcast_series('','','');
		//build current
		if(!is_user_logged_in()){$where=' WHERE private="0" ';}else{$where='';}
		$details=$wpdb->get_row('SELECT a.*,b.* FROM '.CA_FIL_TBL.' a LEFT JOIN '.CA_SERM_TBL.' b ON a.series_id=b.series_id '.$where.' ORDER BY a.pub_date DESC LIMIT 1');
		$current=church_admin_podcast_file($details->file_id,$exclude);

		$out='<div class="ca-podcast"><div class="ca-podcast-left-column">'.$current.'</div><div class="ca-podcast-list">'.$list.'</div></div>';
	}


	return $out;

}
/**
 *  Podcast Series Detail - some detail for a series
 *
 * @author 	Andy Moyle
 * @return 	$out
 * @version 1.2000
 *
 */
 function church_admin_podcast_series_detail($series_id=NULL)
 {
 		global$wpdb;

 		$out='';

 		if(!empty($series_id))
 		{
 			$detail=$wpdb->get_row('SELECT * FROM '.CA_SERM_TBL.' WHERE series_id="'.intval($series_id).'"');
 			if(!empty($detail))
 			{

 				if(!empty($detail->series_name)){$out.='<h2>'.esc_html($detail->series_name).'</h2>';}
 				if(!empty($detail->series_image)){$out.='<p>'.wp_get_attachment_image( $detail->series_image,'large','',array('class'=>'img-responsive')).'</p>';}

 				if(!empty($detail->series_description)){$out.='<p>'.esc_html($detail->series_description).'</p>';}
 			}else{$out='<p>'.__('No series details yet','church-admin').'</p>';}


 		}else{$out='<p>'.__('No series details yet','church-admin').'</p>';}

 	return $out;

 }
 /**
 *  Podcast Series Detail - some detail for a series
 *
 * @author 	Andy Moyle
 * @return 	$out
 * @version 1.2000
 *
 */
 function church_admin_podcast_latest_sermon($series_id=NULL,$exclude)
 {
 		global$wpdb;
		if(!is_user_logged_in()){$where=' AND private="0" ';}else{$where='';}

 		if(!empty($series_id))
 		{
 			$fileID=$wpdb->get_var('SELECT file_id FROM '.CA_FIL_TBL.' WHERE series_id="'.intval($series_id).'" '.$where.' ORDER BY pub_date LIMIT 1');
 			$out=church_admin_podcast_file($fileID,$exclude);
 		}else{$out=__('No sermons in that series yet','church-admin');}

 	return $out;

 }
/**
 *  Podcast Series Display - show splaylist of all files by filter etc
 *
 * @author 	Andy Moyle
 * @return 	$out
 * @version 1.2000
 *
 */
function church_admin_podcast_series($sort_by='date',$asc='DESC',$filter=NULL,$ID=0,$start=0)
{
	global $wpdb;
	$out='';
	$start=0;
	$where='ORDER BY pub_date DESC';
	if(!is_user_logged_in()){$private=' AND a.private="0" ';}else{$private=' 1=1 ';}
	//handle filter
	if(!empty($filter)&&!empty($ID))
	{
		switch($filter)
		{
			case 'series_id': $where='WHERE  a.series_id = "'.intval($ID).'" '.$private.' ORDER BY b.last_sermon '.$asc;break;
			case 'speaker_id': $where='WHERE  a.speaker LIKE "%'.esc_sql($speaker_name).'%" '.$private.' ORDER BY pub_date '.$asc;break;
			default: $where='WHERE  '.$private.' ORDER BY pub_date '.$asc;break;
		}
	}else{$where='WHERE '.$private.' ORDER BY pub_date DESC';}
	//handle "pagination"
	$limit = " LIMIT " . ($start) * 10  . ", 10";
	$max=$wpdb->get_var('SELECT COUNT(a.file_id) FROM '.CA_FIL_TBL .' a LEFT JOIN '.CA_SERM_TBL.' b ON a.series_id=b.series_id '. $where);
	$pages=round($max/10);
	$pagination='';
	$prev=$start-1;
	$next=$start+1;
	if($prev>0)$pagination.='<span class="more-sermons prev-sermons" data-page="'.$prev.'">'.__('Previous','church-admin').'</span>';
	//$pagination.='<span class="current" data-page="'.$start.'">'.$start.'</span>';
	if($next<$pages)$pagination.='<span class="more-sermons next-sermons" data-page="'.$next.'">'.__('Next','church-admin').'</span>';

	//gather data for files
	$sql='SELECT a.*, b.* FROM '.CA_FIL_TBL .' a LEFT JOIN '.CA_SERM_TBL.' b ON a.series_id=b.series_id '. $where.$limit;
church_admin_debug($sql);
	$results=$wpdb->get_results($sql);
	$fileData=array();
	if(!empty($results))
	{
		foreach($results AS $row)
		{
			$fileData[]=array(
							'title'			=>	esc_html($row->file_title),
							'pub_date'		=>	mysql2date(get_option('date_format'),$row->pub_date),
							'file_id'		=>	intval($row->file_id),
							'series_name'	=>	esc_html($row->series_name),
							'speaker'		=>	'<span class="ca-names">'.esc_html($row->speaker).'</span>'
							);
		}
	}

	//gather data for series
	$seriesData=array();

	$seriesResults=$wpdb->get_results('SELECT * FROM '.CA_SERM_TBL.' ORDER BY last_sermon DESC');

	if(!empty($seriesResults))
	{
		foreach($seriesResults AS $seriesRow)
		{
			$seriesData[]=array(
							'series_name'	=>	esc_html($seriesRow->series_name),
							'series_id'		=>	intval($seriesRow->series_id),
							);
		}
	}
	//build output
	$out.='<h6>'.__('Click an item to access','church-admin').'</h6>';
			$ca_podcast_settings=get_option('ca_podcast_settings');

	if(!empty($ca_podcast_settings['link']))$out.='<p><a title="Download on Itunes" href="'.$ca_podcast_settings['itunes_link'].'">
<img  alt="badge_itunes-lrg" src="'.plugins_url('/images/badge_itunes-lrg.png',dirname(__FILE__) ).'" width="110" height="40" /></a></p>';
	//tabs

	$out.='<ul class="ca-podcast-tabs"><li class="ca-podcast-tab-active sermons-tab">'.__('Sermons','church-admin').'</li><li class="ca-podcast-tab series-tab">'.__('Series','church-admin').'</li></ul>';

	$out.='<div class="ca-podcast-tab-content">';


	//media files

	$out.='<div class="ca-media-file-list">';

	if(!empty($fileData))
	{

		foreach($fileData AS $key=>$media)
		{

			$out.='<div class="ca-media-list-item" id="'.$media['file_id'].'" data-date="'.mysql2date('Y-m-d',$row->pub_date).'" title="'.__('Click to show sermon','church-admin').'">';
			$out.='<span class="dashicons dashicons-controls-play ca-play"></span><h3>'.$media['title'].' </h3><span  class="ca-names">'.$media['speaker'].'</span><br/>'.$media['pub_date'];
			$out.='</div>';

		}

 	}
	else
	{
		$out.=__('No media yet','church-admin');
	}
		$out.=$pagination;
	$out.='</div>';
	//series
	$out.='<div class="ca-media-series-list">';

	if(!empty($fileData))
	{
		foreach($seriesData AS $key=>$series)
		{
			$out.='<div class="ca-series-list-item" id="'.$series['series_id'].'">';
			$out.='<h4>'.$series['series_name'].' ('.__('Click to toggle','church-admin').')</h4>';
			$out.='</div><div class="ca-series-sermons-list" id="sermons'.$series['series_id'].'"><p>'.__('Sermons in series','church-admin').'</p>';
			$sermons=$wpdb->get_results($sql='SELECT a.*, b.* FROM '.CA_FIL_TBL .' a LEFT JOIN '.CA_SERM_TBL.' b ON a.series_id=b.series_id  WHERE a.series_id="'.intval($series['series_id']).'" ORDER BY pub_date DESC');
			if(!empty($sermons))
			{
				foreach($sermons AS $sermon)
				{
					$out.='<div class="ca-media-list-item" id="'.$sermon->file_id.'" title="'.__('Click to access sermon','church-admin').'">';
					$out.='<span class="dashicons dashicons-controls-play ca-play"></span><h4>'.$sermon->file_title.' </h4><span class="ca-names">'.$sermon->speaker.'</span><br/>'.$sermon->pub_date;
					$out.='</div>';
				}
			}
			$out.='</div>';
		}
	}
	$out.='</div>';
	$out.='</div>';//tab content

	return $out;
}
function church_admin_podcast_more_files($page=0)
{
	global $wpdb;
	if(!is_user_logged_in()){$private=' WHERE a.private="0" ';}else{$private='';}
	$out='';
	$max=$wpdb->get_var('SELECT COUNT(a.file_id) FROM '.CA_FIL_TBL .' a LEFT JOIN '.CA_SERM_TBL.' b ON  a.series_id=b.series_id '.$private);
	$pages=round($max/10);

	$pagination='';
	$prev=$page-1;
	$next=$page+1;

	$limit = " LIMIT " . ($page) * 10  . ", 10";
	$sql='SELECT a.*, b.* FROM '.CA_FIL_TBL .' a,'.CA_SERM_TBL.' b WHERE a.series_id=b.series_id '.$private.' ORDER BY a.pub_date DESC '.$limit;

	$results=$wpdb->get_results($sql);
	$fileData=array();
	if(!empty($results))
	{
		foreach($results AS $sermon)
		{
			$out.='<div class="ca-media-list-item" id="'.$sermon->file_id.'" title="'.__('Click to access sermon','church-admin').'">';
					$out.='<span class="dashicons dashicons-controls-play ca-play"></span><h3>'.$sermon->file_title.' </h3><span class="ca-names">'.$sermon->speaker.'</span><br/>'.mysql2date(get_option('date_format'),$sermon->pub_date);
					$out.='</div>';

		}
	}else{$out.=__('No more sermons','church-admin');}
	if($prev>0)$out.='<span class="more-sermons prev-sermons" data-page="'.$prev.'">'.__('Previous','church-admin').'</span>';

	if($next<$pages)$out.='<span class="more-sermons next-sermons" data-page="'.$next.'">'.__('Next','church-admin').'</span>';

	return $out;
}
/**
 *  Podcast File Display
 *
 * @author  Andy Moyle
 * @param    $file_id
 * @return
 * @version  0.1
 *
 */
function church_admin_podcast_file($fileID,$exclude)
{
	global $wpdb,$wp_embed;
	if(empty($exclude))$exclude=array();
	$out='';
	$upload_dir = wp_upload_dir();
	$path=$upload_dir['basedir'].'/sermons/';
	$url=content_url().'/uploads/sermons/';
	if(!is_user_logged_in()){$private=' AND a.private="0" ';}else{$private='';}
	if(empty($fileID)){return("<p>There is no file to display</p>");}
	else
	{
		$sql='SELECT a.*,b.* FROM '.CA_FIL_TBL.' a LEFT JOIN  '.CA_SERM_TBL.' b ON a.series_id=b.series_id WHERE a.file_id="'.esc_sql($fileID).'"'.$private;
    	$data=$wpdb->get_row($sql);
    	if(empty($data)){return("<p>There is no file to display</p>");}
		else
		{
				//series detail
				$out.='<div class="ca-series-current">'.church_admin_podcast_series_detail($data->series_id).'</div>';
				$out.='<div class="ca-podcast-current">';
				//now playing tab

				if(!empty($data->file_title))$out.='<h2>'.esc_html($data->file_title).'</h2>';
				if(!empty($data->video_url))$out.=$wp_embed->run_shortcode("\r\n[embed]".esc_url($data->video_url)."[/embed]\r\n<br/>");
				if(!empty($data->file_name) && file_exists($path.$data->file_name))
				{
					if(!empty($data->file_name)){$out.='<p><audio class="sermonmp3" id="'.esc_html($data->file_id).'" src="'.esc_url($url.$data->file_name).'" preload="none" controls></audio></p>';}
					$download='<a href="'.esc_url($url.$data->file_name).'" title="'.esc_html($data->file_title).'">'.esc_html($data->file_title).'</a>';
				}
				elseif(!empty($data->external_file))
				{
					$out.='<p><audio class="sermonmp3" id="'.esc_html($data->file_id).'" src="'.esc_url($data->external_file).'" preload="none" controls></audio></p>';

					$download='<a href="'.esc_url($data->external_file).'" title="'.esc_html($data->file_title).'">'.esc_html($data->file_title).'</a>';
				}
				$out.='<ul class=" ca-podcast-tabs"><li class="ca-tabs ca-podcast-tab-active" id="ca-podcast-file-content">'.__('Now playing','church-admin').'</li><li class="ca-tabs ca-podcast-tab" id="ca-podcast-notes-content">'.__('Sermon Notes','church-admin').'</li></ul>';
				$out.='<div class="ca-podcast-playing-content">';
				//no of plays
				$plays=church_admin_plays($data->file_id);

				$out.='<div class="ca-podcast-file-content ca-tab-content">';
				if(!empty($data->file_description)&&!in_array('description',$exclude))$out.='<p>'.$data->file_description.'</p>';
				$out.='<table>';
				if(!empty($data->speaker))$out.='<tr><td>'.__('Speaker','church-admin').':&nbsp;</td><td class="ca-names">'.esc_html($data->speaker).'</td></tr>';
				if(!empty($data->series_name))$out.='<tr><td>'.__('Series','church-admin').':&nbsp;</td><td>'.esc_html($data->series_name).'</td></tr>';
				if(!empty($data->pub_date)&&!in_array('date',$exclude))$out.='<tr><td>'.__('Date','church-admin').':&nbsp;</td><td>'.mysql2date(get_option('date_format'),$data->pub_date).'</td></tr>';
				if(!empty($download)&&!in_array('download',$exclude))$out.='<tr><td>'.__('Download','church-admin').':&nbsp;</td><td>'.$download.'</td></tr>';
				if(!empty($plays)&&$plays>0&&!in_array('plays',$exclude))$out.='<tr><td>'.__('Plays','church-admin').':&nbsp;</td><td class="plays">'.$plays.'</td></tr>';
				if(!empty($data->bible_texts))
				{
					$pass=array();
					$version=get_option('church_admin_bible_version');
					$passages=explode(",",$data->bible_texts);
					if(!empty($passages)&&is_array($passages))
					{
						foreach($passages AS $passage)$pass[]='<a href="https://www.biblegateway.com/passage/?search='.urlencode($passage).'&version='.$version.'&interface=print" target="_blank">'.esc_html($passage).'</a>';

					$out.='<tr><td>'.__('Scriptures','church-admin').':&nbsp;</td><td>'.implode(", ",$pass).'</td></tr>';
					}
				}
				$out.='</table>';

				$out.='</div><!--end of file content-->';

				$out.='<div class="ca-podcast-notes-content ca-tab-content">';
				if(!empty($data->transcript)){$out.=$data->transcript;}else{$out.=__('No Notes saved for this sermon','church-admin');}
				$out.='</div><!--end of notes content-->';
				$out.='</div><!--end of playing content-->';
				$out.='</div>';
		}
	}

	$out=do_shortcode($out);
	return $out;
}
