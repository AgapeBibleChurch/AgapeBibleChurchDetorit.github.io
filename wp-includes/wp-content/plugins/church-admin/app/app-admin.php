<?php

if ( ! defined( 'ABSPATH' ) ) exit('You need Jesus!'); // Exit if accessed directly

/**
 *
 * Admin function for app
 *
 * @author  Andy Moyle
 * @param    null
 * @return   html
 * @version  0.1
 *
 */
function church_admin_app()
{

	//initialise
	global $wpdb;
	if(!empty($_POST['app_id']))
	{
			update_option('church_admin_app_id',intval($_POST['app_id']));
	}
	echo'<h1>Church Admin App Admin</h1>';
	$checkedToday=get_option('church_admin_licence_checked');

	if(empty($checkedToday)||$checkedToday!=date('Y-m-d'))
	{
		update_option('church_admin_app_new_licence','no-sub');
		$url = 'https://www.churchadminplugin.com/?church_url='.md5(site_url());
    $result = wp_remote_get( $url );
		if(defined('CA_DEBUG'))church_admin_debug(print_r($result,TRUE));
		update_option('church_admin_app_new_licence',$result['body']);
		update_option('church_admin_licence_checked',date('Y-m-d'));
	}
	$licence=get_option('church_admin_app_new_licence');

	//if(empty($licence)||$licence!=md5('licence'.site_url()))
	if($licence!='subscribed')
	{
		//no licence yet
		echo '<div id="iphone" class="alignleft"><iframe src="'.plugins_url('/app/demo/index.html',dirname(__FILE__) ).'" width=475 height=845 class="demo-app"></iframe></div>';

		delete_option('church_admin_licence_checked');
		echo'<h2>'.__('Church Admin App','church-admin').'</h2>';
		echo'<p>'.__('You may have the best organised church on earth, but all that organisation goes for nothing if you don’t communicate well. In 20 years of church leadership one of the biggest lessons I have learnt is that you cannot over-communicate.','church-admin').'</p>';
	echo '<p>'.__('The Church Admin app puts communicating church life in the hands of your people on their phones. The Android and iOS app allows your church members to access the directory, schedule, prayer requests, bible reading plan, blog, calendar and sermons from their phone or tablet. It’s FREE for your church congregation to use and just a £7.50pm subscription for the church.','church-admin').'</p>';
	echo'<p>'.__('It is available in English, Dutch, French, Norwegian, Spanish and Swedish currently','church-admin').'<p>';
		echo'<form  action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top"><input type="hidden" name="cmd" value="_s-xclick"><input type="hidden" name="hosted_button_id" value="T34GDC3DXFDT6"><input type="hidden" name="os0" style="width:400px" maxlength="500" value="'.site_url().'"><input type="image" src="https://www.paypalobjects.com/en_US/GB/i/btn/btn_subscribeCC_LG.gif" border="0" name="submit" alt="PayPal – The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypalobjects.com/en_GB/i/scr/pixel.gif" width="1" height="1"></form>';


	}
	else
	{
		$app_id=get_option('church_admin_app_id');
		if(empty($app_id))
		{
			echo'<form action="" method="post"><p><label for="app_id">'.__("Please enter app ID", 'church-admin').'</label><input name="app_id" type="number" min="1" step="1"/><input type="submit" class="button-primary" value="'.__('Save','church-admin').'"/></p></form>';
		}

		church_admin_app_menu();
		church_admin_app_content();
		church_admin_app_member_types();
		church_admin_bible_reading_plan();
		church_admin_bible_version();
		church_admin_app_logins();


	}
}

function church_admin_app_menu()
{
		$menu=array('Bible','Calendar','Classes','Checkin','Giving','Groups','Media','News','Prayer','My prayer list','Rotas');
		$displayMenu=array(__('Bible','church-admin'),__('Calendar','church-admin'),__('Checkin','church-admin'),__('Classes','church-admin'),__('Giving','church-admin'),__('Groups','church-admin'),__('Media','church-admin'),__('News','church-admin'),__('Prayer','church-admin'),__('My prayer list','church-admin'),__('Rotas','church-admin'));
		$chosenMenu=get_option('church-admin-app-menu');
		if(empty($chosenMenu))
		{//first run situation
				$chosenMenu=array();
				foreach($menu AS $key=>$item)$chosenMenu[$item]=TRUE;
				sort($chosenMenu);
				update_option('church-admin-app-menu',$chosenMenu);
		}
		//normal function run...
		if(!empty($_POST['save-menu']))
		{
				foreach($menu AS $key=>$item)if(!empty($_POST['item'.$key])){$chosenMenu[$item]=TRUE;}else{$chosenMenu[$item]=FALSE;}
				update_option('church-admin-app-menu',$chosenMenu);

				echo'<div class="notice notice-success"><p><strong>'.__("App menu updated",'church-admin').'</strong></p></div>';
				if(empty($_POST['private-prayer']))
				{
					update_option('church-admin-private-prayer-requests',FALSE);

				}else
				{
					update_option('church-admin-private-prayer-requests',TRUE);

				}
				if(empty($_POST['private-schedule']))
				{
					update_option('church-admin-private-schedule',FALSE);

				}else
				{
					update_option('church-admin-private-schedule',TRUE);

				}
		}


		echo'<h2 class="app-menu-toggle" id="app-menu">'.__('App Menu Items (Click to toggle view)','church-admin').'</h2>';
		echo'<div class="app-menu" style="display:none">';
		echo'<p><strong>'.__("Please don't check the checkin menu item as it is not working on the app yet",'church-admin').'</strong></p>';
			echo'<form  method=POST action=""><table class="form-table">';
			foreach($menu AS $key=>$item)
			{
					echo'<tr><th scope="row">'.esc_html($displayMenu[$key]).'</th><td><input type="checkbox" name="'.esc_html('item'.$key).'" value=1 ';
					if(!empty($chosenMenu[$item]))echo ' checked="checked" ';
					echo '/></td></tr>';
			}
			echo'<tr><th scope="row">'.__('Make prayer requests viewable by logged in users only','church-admin').'</th><td><input type="checkbox" name="private-prayer" value="TRUE" ';
			$private=get_option('church-admin-private-prayer-requests');
			if($private) echo ' checked="checked" ';
			echo '/></td></tr>';
			echo'<tr><th scope="row">'.__('Make schedule viewable by logged in users only','church-admin').'</th><td><input type="checkbox" name="private-schedule" value="TRUE" ';
			$private_schedule=get_option('church-admin-private-schedule');
			if($private_schedule) echo ' checked="checked" ';
			echo '/></td></tr>';
			echo'<tr><td colspacing=2><input type="hidden" name="save-menu" value="yes"/><input type="submit" class="button-primary" value="'.__('Save','church-admin').'"/></td></tr>';

			echo'</table></form>';
			echo'</div>';
				echo'<script type="text/javascript">jQuery(function(){  jQuery(".app-menu-toggle").click(function(){jQuery(".app-menu").toggle();  });});</script>';
}






function church_admin_app_content()
{
	echo'<h2 class="content-toggle">'.__('App Content (Click to toggle view)','church-admin').'</h2>';
	echo'<div class="app-content" style="display:none">';
	if(!empty($_POST['app_content']))
	{
		update_option('church_admin_app_home',stripslashes($_POST['home']));
		update_option('church_admin_app_menu_title',stripslashes($_POST['menu-name']));
		update_option('church_admin_app_giving',stripslashes($_POST['giving']));
		update_option('church_admin_app_groups',stripslashes($_POST['groups']));
		update_option('church_admin_app_style',stripslashes($_POST['style']));
		update_option('church_admin_app_logo',stripslashes($_POST['logo']));
		echo'<div class="notice notice-success inline"><h2>App Content saved</h2></div>';
	}
	$menu_title=get_option('church_admin_app_menu_title');
	$home=get_option('church_admin_app_home');
	$giving=get_option('church_admin_app_giving');
	$groups=get_option('church_admin_app_groups');
	$logo=get_option('church_admin_app_logo');

	if(empty($logo))$logo='https://dummyimage.com/300x220/000/fff&text=Your+Logo+here';
	$style=get_option('church_admin_app_style');
	echo'<table><tr><th scope="row">'.__('Church Logo best as 300px wide','church-admin').'</th><td><input id="houshold-image" type="button" class="household-upload-button button" value="'.__('Upload Image','church-admin').'" />';
	if(!empty($logo))
	{
		echo'<span id="frontend-image" class="remove-image button-secondary" data-attachment_id="1" data-type="household">'.__('Remove image','church-admin').'</span><img class="current-logo" src="'.$logo.'"/>';
	}
	echo'<input type="hidden" name="logo" id="logo" ';
	if(!empty($logo)) echo' value="'.$logo.'"/>';
	echo'<div id="upload-message"></div></td></tr></table>';
	echo'<form action="" method="POST">';
	echo'<p><label>'.__('App Menu Title','church-admin').'</label><input type="text" name="menu-name" placeholder="'.__('App Menu Title','church-admin').'" value="'.esc_html($menu_title).'"/></p>';
	echo'<table class="form-table">';
	echo'<tr style="vertical-align:top"><td><h2>'.__('Home page','church-admin').'</h2>';
	echo'<textarea cols=60 rows=50 name="home">'.$home.'</textarea></td>';
	echo'<td><h2>'.__('Groups page','church-admin').'</h2>';
	echo'<p>'.__('Text for before list of groups','church-admin').'</p>';
	echo'<textarea  cols=60 rows=50 name="groups">'.$groups.'</textarea></td>';

	echo'<td><h2>'.__('Giving page','church-admin').'</h2>';
	echo'<p>'.__('Text for giving page','church-admin').'</p>';
	echo'<textarea  cols=60 rows=50  name="giving">'.$giving.'</textarea></td></tr>';
	echo'<tr><td><h2>'.__('Styling','church-admin').'</h2>';
	echo'<p>'.__('Add your own CSS to the app','church-admin').'</p>';
	echo'<textarea  cols=60 rows=10  name="style">'.$style.'</textarea></td></tr>';
	echo'<tr><td colspacing=3><input type="hidden" name="app_content" value="TRUE"/><input type="submit" class="button-primary" value="Save"/></td></tr></table></form>';
	echo'</div>';
	echo'<script type="text/javascript">jQuery(function(){  jQuery(".content-toggle").click(function(){jQuery(".app-content").toggle();  });});</script>';
	$nonce = wp_create_nonce("church_admin_image_upload");
	echo'<script >jQuery(document).ready(function($){


		//remove image
		$(".remove-image").click(function()
		{
				var type= $(this).data("type");
				var attachment_id=$(this).data("attachment_id");
				var id=$(this).data("id");

				var nonce="'.wp_create_nonce("remove-app-logo").'";
				var data={"action":"church_admin","method":"remove-app-logo","nonce":nonce};
				console.log(data);
				$.ajax({
									url: ajaxurl,
									type: "POST",
									data: data,
									success: function(res) {
										console.log(res);
										$("#upload-message").html("'.__("Image Deleted","church-admin").'<br/>");
										$(".current-logo").attr("srcset","https://dummyimage.com/300x220/000/fff&text=Your+Logo+here");
									},
									error: function(res) {
								$("#upload-message").html("Error deleting<br/>");
									}
							 });
		});

var mediaUploader;

$(".household-upload-button").click(function(e) {
	e.preventDefault();

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

		console.log(attachment.sizes.full.url);
		$("#logo").val("src",attachment.sizes.full.url);
		$(".current-logo").attr("srcset",attachment.sizes.full.url);

		var nonce="'.wp_create_nonce("update-app-logo").'";
		var data={"action":"church_admin","method":"update-app-logo","logo":attachment.sizes.thumbnail.url,"nonce":nonce};
		console.log(data);
		$.ajax({
							url: ajaxurl,
							type: "POST",
							data: data,
							success: function(res) {
								console.log(res);
								$("#upload-message").html("'.__("Image Updated","church-admin").'<br/>");

							},
							error: function(res) {
						$("#upload-message").html("Error deleting<br/>");
							}
					 });
	});
	// Open the uploader dialog
	mediaUploader.open();
});

});</script>';
}



function church_admin_bible_version()
{
		global $wpdb;
	echo'<h2 class="version-toggle" id="bible-version">'.__('Which Bible version?  (Click to toggle)','church-admin').'</h2>';
	echo'<div class="bible-version" style="display:none">';
	$version=get_option('church_admin_bible_version');
	switch($version)
	{
		case'KJV':
		case "ostervald":
		case "schlachter":
		case "statenvertaling":
		case "swedish":
		case "bibelselskap":
		case "sse":
		case "lithuanian":
			echo' We are offering more versions now, please update';
		break;


	}


	if(!empty($_POST['version']))
	{

		update_option('church_admin_bible_version',stripslashes($_POST['version']));
	}

		$version=get_option('church_admin_bible_version');

		echo'<form action="" method="POST"><select class="search-translation-select translation-select-default form-control" name="version">';
		if(!empty($version)) echo '<option selected="selected" value="'.esc_html($version).'">'.esc_html($version).'</option>';
		?>

		<option class="lang" value="AMU">—Amuzgo de Guerrero (AMU)—</option>
		<option value="AMU">Amuzgo de Guerrero (AMU)</option>
		<option class="spacer" value="AMU">&nbsp;</option>
<option class="lang" value="ERV-AR">—العربية (AR)—</option>
<option value="ERV-AR">Arabic Bible: Easy-to-Read Version (ERV-AR)</option>
<option value="NAV">Ketab El Hayat (NAV)</option>
<option class="spacer" value="NAV">&nbsp;</option>
<option class="lang" value="ERV-AWA">—अवधी (AWA)—</option>
<option value="ERV-AWA">Awadhi Bible: Easy-to-Read Version (ERV-AWA)</option>
<option class="spacer" value="ERV-AWA">&nbsp;</option>
<option class="lang" value="BG1940">—Български (BG)—</option>
<option value="BG1940">1940 Bulgarian Bible (BG1940)</option>
<option value="BULG">Bulgarian Bible (BULG)</option>
<option value="ERV-BG">Bulgarian New Testament: Easy-to-Read Version (ERV-BG)</option>
<option value="CBT">Библия, нов превод от оригиналните езици (с неканоничните книги) (CBT)</option>
<option value="BOB">Библия, синодално издание (BOB)</option>
<option value="BPB">Библия, ревизирано издание (BPB)</option>
<option class="spacer" value="BPB">&nbsp;</option>
<option class="lang" value="CCO">—Chinanteco de Comaltepec (CCO)—</option>
<option value="CCO">Chinanteco de Comaltepec (CCO)</option>
<option class="spacer" value="CCO">&nbsp;</option>
<option class="lang" value="APSD-CEB">—Cebuano (CEB)—</option>
<option value="APSD-CEB">Ang Pulong Sa Dios (APSD-CEB)</option>
<option class="spacer" value="APSD-CEB">&nbsp;</option>
<option class="lang" value="CHR">—ᏣᎳᎩ ᎦᏬᏂᎯᏍ (CHR)—</option>
<option value="CHR">Cherokee New Testament (CHR)</option>
<option class="spacer" value="CHR">&nbsp;</option>
<option class="lang" value="CKW">—Cakchiquel Occidental (CKW)—</option>
<option value="CKW">Cakchiquel Occidental (CKW)</option>
<option class="spacer" value="CKW">&nbsp;</option>
<option class="lang" value="B21">—Čeština (CS)—</option>
<option value="B21">Bible 21 (B21)</option>
<option value="SNC">Slovo na cestu (SNC)</option>
<option class="spacer" value="SNC">&nbsp;</option>
<option class="lang" value="BWM">—Cymraeg (CY)—</option>
<option value="BWM">Beibl William Morgan (BWM)</option>
<option class="spacer" value="BWM">&nbsp;</option>
<option class="lang" value="BPH">—Dansk (DA)—</option>
<option value="BPH">Bibelen på hverdagsdansk (BPH)</option>
<option value="DN1933">Dette er Biblen på dansk (DN1933)</option>
<option class="spacer" value="DN1933">&nbsp;</option>
<option class="lang" value="HOF">—Deutsch (DE)—</option>
<option value="HOF">Hoffnung für Alle (HOF)</option>
<option value="LUTH1545">Luther Bibel 1545 (LUTH1545)</option>
<option value="NGU-DE">Neue Genfer Übersetzung (NGU-DE)</option>
<option value="SCH1951">Schlachter 1951 (SCH1951)</option>
<option value="SCH2000">Schlachter 2000 (SCH2000)</option>
<option class="spacer" value="SCH2000">&nbsp;</option>
<option class="lang" value="KJ21">—English (EN)—</option>
<option value="KJ21">21st Century King James Version (KJ21)</option>
<option value="ASV">American Standard Version (ASV)</option>
<option value="AMP">Amplified Bible (AMP)</option>
<option value="AMPC">Amplified Bible, Classic Edition (AMPC)</option>
<option value="BRG">BRG Bible (BRG)</option>
<option value="CSB">Christian Standard Bible (CSB)</option>
<option value="CEB">Common English Bible (CEB)</option>
<option value="CJB">Complete Jewish Bible (CJB)</option>
<option value="CEV">Contemporary English Version (CEV)</option>
<option value="DARBY">Darby Translation (DARBY)</option>
<option value="DLNT">Disciples’ Literal New Testament (DLNT)</option>
<option value="DRA">Douay-Rheims 1899 American Edition (DRA)</option>
<option value="ERV">Easy-to-Read Version (ERV)</option>
<option value="ESV">English Standard Version (ESV)</option>
<option value="ESVUK">English Standard Version Anglicised (ESVUK)</option>
<option value="EXB">Expanded Bible (EXB)</option>
<option value="GNV">1599 Geneva Bible (GNV)</option>
<option value="GW">GOD’S WORD Translation (GW)</option>
<option value="GNT">Good News Translation (GNT)</option>
<option value="HCSB">Holman Christian Standard Bible (HCSB)</option>
<option value="ICB">International Children’s Bible (ICB)</option>
<option value="ISV">International Standard Version (ISV)</option>
<option value="PHILLIPS">J.B. Phillips New Testament (PHILLIPS)</option>
<option value="JUB">Jubilee Bible 2000 (JUB)</option>
<option value="KJV">King James Version (KJV)</option>
<option value="AKJV">Authorized (King James) Version (AKJV)</option>
<option value="LEB">Lexham English Bible (LEB)</option>
<option value="TLB">Living Bible (TLB)</option>
<option value="MSG">The Message (MSG)</option>
<option value="MEV">Modern English Version (MEV)</option>
<option value="MOUNCE">Mounce Reverse-Interlinear New Testament (MOUNCE)</option>
<option value="NOG">Names of God Bible (NOG)</option>
<option value="NABRE">New American Bible (Revised Edition) (NABRE)</option>
<option value="NASB">New American Standard Bible (NASB)</option>
<option value="NCV">New Century Version (NCV)</option>
<option value="NET">New English Translation (NET Bible)</option>
<option value="NIRV">New International Reader's Version (NIRV)</option>
<option value="NIV">New International Version (NIV)</option>
<option value="NIVUK">New International Version - UK (NIVUK)</option>
<option value="NKJV">New King James Version (NKJV)</option>
<option value="NLV">New Life Version (NLV)</option>
<option value="NLT">New Living Translation (NLT)</option>
<option value="NMB">New Matthew Bible (NMB)</option>
<option value="NRSV">New Revised Standard Version (NRSV)</option>
<option value="NRSVA">New Revised Standard Version, Anglicised (NRSVA)</option>
<option value="NRSVACE">New Revised Standard Version, Anglicised Catholic Edition (NRSVACE)</option>
<option value="NRSVCE">New Revised Standard Version Catholic Edition (NRSVCE)</option>
<option value="NTE">New Testament for Everyone (NTE)</option>
<option value="OJB">Orthodox Jewish Bible (OJB)</option>
<option value="TPT">The Passion Translation (TPT)</option>
<option value="RSV">Revised Standard Version (RSV)</option>
<option value="RSVCE">Revised Standard Version Catholic Edition (RSVCE)</option>
<option value="TLV">Tree of Life Version (TLV)</option>
<option value="VOICE">The Voice (VOICE)</option>
<option value="WEB">World English Bible (WEB)</option>
<option value="WE">Worldwide English (New Testament) (WE)</option>
<option value="WYC">Wycliffe Bible (WYC)</option>
<option value="YLT">Young's Literal Translation (YLT)</option>
<option class="spacer" value="YLT">&nbsp;</option>
<option class="lang" value="LBLA">—Español (ES)—</option>
<option value="LBLA">La Biblia de las Américas (LBLA)</option>
<option value="DHH">Dios Habla Hoy (DHH)</option>
<option value="JBS">Jubilee Bible 2000 (Spanish) (JBS)</option>
<option value="NBD">Nueva Biblia al Día (NBD)</option>
<option value="NBLH">Nueva Biblia Latinoamericana de Hoy (NBLH)</option>
<option value="NTV">Nueva Traducción Viviente (NTV)</option>
<option value="NVI">Nueva Versión Internacional (NVI)</option>
<option value="CST">Nueva Versión Internacional (Castilian) (CST)</option>
<option value="PDT">Palabra de Dios para Todos (PDT)</option>
<option value="BLP">La Palabra (España) (BLP)</option>
<option value="BLPH">La Palabra (Hispanoamérica) (BLPH)</option>
<option value="RVA-2015">Reina Valera Actualizada (RVA-2015)</option>
<option value="RVC">Reina Valera Contemporánea (RVC)</option>
<option value="RVR1960">Reina-Valera 1960 (RVR1960)</option>
<option value="RVR1977">Reina Valera 1977 (RVR1977)</option>
<option value="RVR1995">Reina-Valera 1995 (RVR1995)</option>
<option value="RVA">Reina-Valera Antigua (RVA)</option>
<option value="SRV-BRG">Spanish Blue Red and Gold Letter Edition (SRV-BRG)</option>
<option value="TLA">Traducción en lenguaje actual (TLA)</option>
<option class="spacer" value="TLA">&nbsp;</option>
<option class="lang" value="R1933">—Suomi (FI)—</option>
<option value="R1933">Raamattu 1933/38 (R1933)</option>
<option class="spacer" value="R1933">&nbsp;</option>
<option class="lang" value="BDS">—Français (FR)—</option>
<option value="BDS">La Bible du Semeur (BDS)</option>
<option value="LSG">Louis Segond (LSG)</option>
<option value="NEG1979">Nouvelle Edition de Genève – NEG1979 (NEG1979)</option>
<option value="SG21">Segond 21 (SG21)</option>
<option class="spacer" value="SG21">&nbsp;</option>
<option class="lang" value="TR1550">—Κοινη (GRC)—</option>
<option value="TR1550">1550 Stephanus New Testament (TR1550)</option>
<option value="WHNU">1881 Westcott-Hort New Testament (WHNU)</option>
<option value="TR1894">1894 Scrivener New Testament (TR1894)</option>
<option value="SBLGNT">SBL Greek New Testament (SBLGNT)</option>
<option class="spacer" value="SBLGNT">&nbsp;</option>
<option class="lang" value="HHH">—עברית (HE)—</option>
<option value="HHH">Habrit Hakhadasha/Haderekh (HHH)</option>
<option value="WLC">The Westminster Leningrad Codex (WLC)</option>
<option class="spacer" value="WLC">&nbsp;</option>
<option class="lang" value="ERV-HI">—हिन्दी (HI)—</option>
<option value="ERV-HI">Hindi Bible: Easy-to-Read Version (ERV-HI)</option>
<option class="spacer" value="ERV-HI">&nbsp;</option>
<option class="lang" value="HLGN">—Ilonggo (HIL)—</option>
<option value="HLGN">Ang Pulong Sang Dios (HLGN)</option>
<option class="spacer" value="HLGN">&nbsp;</option>
<option class="lang" value="HNZ-RI">—Hrvatski (HR)—</option>
<option value="HNZ-RI">Hrvatski Novi Zavjet – Rijeka 2001 (HNZ-RI)</option>
<option value="CRO">Knijga O Kristu (CRO)</option>
<option class="spacer" value="CRO">&nbsp;</option>
<option class="lang" value="HCV">—Kreyòl ayisyen (HT)—</option>
<option value="HCV">Haitian Creole Version (HCV)</option>
<option class="spacer" value="HCV">&nbsp;</option>
<option class="lang" value="KAR">—Magyar (HU)—</option>
<option value="KAR">Hungarian Károli (KAR)</option>
<option value="ERV-HU">Hungarian Bible: Easy-to-Read Version (ERV-HU)</option>
<option value="NT-HU">Hungarian New Translation (NT-HU)</option>
<option class="spacer" value="NT-HU">&nbsp;</option>
<option class="lang" value="HWP">—Hawai‘i Pidgin (HWC)—</option>
<option value="HWP">Hawai‘i Pidgin (HWP)</option>
<option class="spacer" value="HWP">&nbsp;</option>
<option class="lang" value="ICELAND">—Íslenska (IS)—</option>
<option value="ICELAND">Icelandic Bible (ICELAND)</option>
<option class="spacer" value="ICELAND">&nbsp;</option>
<option class="lang" value="BDG">—Italiano (IT)—</option>
<option value="BDG">La Bibbia della Gioia (BDG)</option>
<option value="CEI">Conferenza Episcopale Italiana (CEI)</option>
<option value="LND">La Nuova Diodati (LND)</option>
<option value="NR1994">Nuova Riveduta 1994 (NR1994)</option>
<option value="NR2006">Nuova Riveduta 2006 (NR2006)</option>
<option class="spacer" value="NR2006">&nbsp;</option>
<option class="lang" value="JLB">—日本語 (JA)—</option>
<option value="JLB">Japanese Living Bible (JLB)</option>
<option class="spacer" value="JLB">&nbsp;</option>
<option class="lang" value="JAC">—Jacalteco, Oriental (JAC)—</option>
<option value="JAC">Jacalteco, Oriental (JAC)</option>
<option class="spacer" value="JAC">&nbsp;</option>
<option class="lang" value="KEK">—Kekchi (KEK)—</option>
<option value="KEK">Kekchi (KEK)</option>
<option class="spacer" value="KEK">&nbsp;</option>
<option class="lang" value="KLB">—한국어 (KO)—</option>
<option value="KLB">Korean Living Bible (KLB)</option>
<option class="spacer" value="KLB">&nbsp;</option>
<option class="lang" value="VULGATE">—Latina (LA)—</option>
<option value="VULGATE">Biblia Sacra Vulgata (VULGATE)</option>
<option class="spacer" value="VULGATE">&nbsp;</option>
<option class="lang" value="MAORI">—Māori (MI)—</option>
<option value="MAORI">Maori Bible (MAORI)</option>
<option class="spacer" value="MAORI">&nbsp;</option>
<option class="lang" value="MNT">—Македонски (MK)—</option>
<option value="MNT">Macedonian New Testament (MNT)</option>
<option class="spacer" value="MNT">&nbsp;</option>
<option class="lang" value="ERV-MR">—मराठी (MR)—</option>
<option value="ERV-MR">Marathi Bible: Easy-to-Read Version (ERV-MR)</option>
<option class="spacer" value="ERV-MR">&nbsp;</option>
<option class="lang" value="MVC">—Mam, Central (MVC)—</option>
<option value="MVC">Mam, Central (MVC)</option>
<option class="spacer" value="MVC">&nbsp;</option>
<option class="lang" value="MVJ">—Mam, Todos Santos (MVJ)—</option>
<option value="MVJ">Mam de Todos Santos Chuchumatán (MVJ)</option>
<option class="spacer" value="MVJ">&nbsp;</option>
<option class="lang" value="REIMER">—Plautdietsch (NDS)—</option>
<option value="REIMER">Reimer 2001 (REIMER)</option>
<option class="spacer" value="REIMER">&nbsp;</option>
<option class="lang" value="ERV-NE">—नेपाली (NE)—</option>
<option value="ERV-NE">Nepali Bible: Easy-to-Read Version (ERV-NE)</option>
<option class="spacer" value="ERV-NE">&nbsp;</option>
<option class="lang" value="NGU">—Náhuatl de Guerrero (NGU)—</option>
<option value="NGU">Náhuatl de Guerrero (NGU)</option>
<option class="spacer" value="NGU">&nbsp;</option>
<option class="lang" value="HTB">—Nederlands (NL)—</option>
<option value="HTB">Het Boek (HTB)</option>
<option class="spacer" value="HTB">&nbsp;</option>
<option class="lang" value="DNB1930">—Norsk (NO)—</option>
<option value="DNB1930">Det Norsk Bibelselskap 1930 (DNB1930)</option>
<option value="LB">En Levende Bok (LB)</option>
<option class="spacer" value="LB">&nbsp;</option>
<option class="lang" value="ERV-OR">—ଓଡ଼ିଆ (OR)—</option>
<option value="ERV-OR">Oriya Bible: Easy-to-Read Version (ERV-OR)</option>
<option class="spacer" value="ERV-OR">&nbsp;</option>
<option class="lang" value="ERV-PA">—ਪੰਜਾਬੀ (PA)—</option>
<option value="ERV-PA">Punjabi Bible: Easy-to-Read Version (ERV-PA)</option>
<option class="spacer" value="ERV-PA">&nbsp;</option>
<option class="lang" value="NP">—Polski (PL)—</option>
<option value="NP">Nowe Przymierze (NP)</option>
<option value="SZ-PL">Słowo Życia (SZ-PL)</option>
<option value="UBG">Updated Gdańsk Bible (UBG)</option>
<option class="spacer" value="UBG">&nbsp;</option>
<option class="lang" value="NBTN">—Nawat (PPL)—</option>
<option value="NBTN">Ne Bibliaj Tik Nawat (NBTN)</option>
<option class="spacer" value="NBTN">&nbsp;</option>
<option class="lang" value="ARC">—Português (PT)—</option>
<option value="ARC">Almeida Revista e Corrigida 2009 (ARC)</option>
<option value="NTLH">Nova Traduҫão na Linguagem de Hoje 2000 (NTLH)</option>
<option value="NVI-PT">Nova Versão Internacional (NVI-PT)</option>
<option value="OL">O Livro (OL)</option>
<option value="VFL">Portuguese New Testament: Easy-to-Read Version (VFL)</option>
<option class="spacer" value="VFL">&nbsp;</option>
<option class="lang" value="MTDS">—Quichua (QU)—</option>
<option value="MTDS">Mushuj Testamento Diospaj Shimi (MTDS)</option>
<option class="spacer" value="MTDS">&nbsp;</option>
<option class="lang" value="QUT">—Quiché, Centro Occidenta (QUT)—</option>
<option value="QUT">Quiché, Centro Occidental (QUT)</option>
<option class="spacer" value="QUT">&nbsp;</option>
<option class="lang" value="RMNN">—Română (RO)—</option>
<option value="RMNN">Cornilescu 1924 - Revised 2010, 2014 (RMNN)</option>
<option value="NTLR">Nouă Traducere În Limba Română (NTLR)</option>
<option class="spacer" value="NTLR">&nbsp;</option>
<option class="lang" value="NRT">—Русский (RU)—</option>
<option value="NRT">New Russian Translation (NRT)</option>
<option value="CARS">Священное Писание (Восточный Перевод) (CARS)</option>
<option value="CARST">Священное Писание (Восточный перевод), версия для Таджикистана (CARST)</option>
<option value="CARSA">Священное Писание (Восточный перевод), версия с «Аллахом» (CARSA)</option>
<option value="ERV-RU">Russian New Testament: Easy-to-Read Version (ERV-RU)</option>
<option value="RUSV">Russian Synodal Version (RUSV)</option>
<option class="spacer" value="RUSV">&nbsp;</option>
<option class="lang" value="NPK">—Slovenčina (SK)—</option>
<option value="NPK">Nádej pre kazdého (NPK)</option>
<option class="spacer" value="NPK">&nbsp;</option>
<option class="lang" value="SOM">—Somali (SO)—</option>
<option value="SOM">Somali Bible (SOM)</option>
<option class="spacer" value="SOM">&nbsp;</option>
<option class="lang" value="ALB">—Shqip (SQ)—</option>
<option value="ALB">Albanian Bible (ALB)</option>
<option class="spacer" value="ALB">&nbsp;</option>
<option class="lang" value="ERV-SR">—Српски (SR)—</option>
<option value="ERV-SR">Serbian New Testament: Easy-to-Read Version (ERV-SR)</option>
<option class="spacer" value="ERV-SR">&nbsp;</option>
<option class="lang" value="SVL">—Svenska (SV)—</option>
<option value="SVL">Nya Levande Bibeln (SVL)</option>
<option value="SV1917">Svenska 1917 (SV1917)</option>
<option value="SFB">Svenska Folkbibeln (SFB)</option>
<option value="SFB15">Svenska Folkbibeln 2015 (SFB15)</option>
<option class="spacer" value="SFB15">&nbsp;</option>
<option class="lang" value="SNT">—Kiswahili (SW)—</option>
<option value="SNT">Neno: Bibilia Takatifu (SNT)</option>
<option class="spacer" value="SNT">&nbsp;</option>
<option class="lang" value="ERV-TA">—தமிழ் (TA)—</option>
<option value="ERV-TA">Tamil Bible: Easy-to-Read Version (ERV-TA)</option>
<option class="spacer" value="ERV-TA">&nbsp;</option>
<option class="lang" value="TNCV">—ภาษาไทย (TH)—</option>
<option value="TNCV">Thai New Contemporary Bible (TNCV)</option>
<option value="ERV-TH">Thai New Testament: Easy-to-Read Version (ERV-TH)</option>
<option class="spacer" value="ERV-TH">&nbsp;</option>
<option class="lang" value="FSV">—Tagalog (TL)—</option>
<option value="FSV">Ang Bagong Tipan: Filipino Standard Version (FSV)</option>
<option value="ABTAG1978">Ang Biblia (1978) (ABTAG1978)</option>
<option value="ABTAG2001">Ang Biblia, 2001 (ABTAG2001)</option>
<option value="ADB1905">Ang Dating Biblia (1905) (ADB1905)</option>
<option value="SND">Ang Salita ng Diyos (SND)</option>
<option value="MBBTAG">Magandang Balita Biblia (MBBTAG)</option>
<option value="MBBTAG-DC">Magandang Balita Biblia (with Deuterocanon) (MBBTAG-DC)</option>
<option class="spacer" value="MBBTAG-DC">&nbsp;</option>
<option class="lang" value="NA-TWI">—Twi (TWI)—</option>
<option value="NA-TWI">Nkwa Asem (NA-TWI)</option>
<option class="spacer" value="NA-TWI">&nbsp;</option>
<option class="lang" value="UKR">—Українська (UK)—</option>
<option value="UKR">Ukrainian Bible (UKR)</option>
<option value="ERV-UK">Ukrainian New Testament: Easy-to-Read Version (ERV-UK)</option>
<option class="spacer" value="ERV-UK">&nbsp;</option>
<option class="lang" value="ERV-UR">—اردو (UR)—</option>
<option value="ERV-UR">Urdu Bible: Easy-to-Read Version (ERV-UR)</option>
<option class="spacer" value="ERV-UR">&nbsp;</option>
<option class="lang" value="USP">—Uspanteco (USP)—</option>
<option value="USP">Uspanteco (USP)</option>
<option class="spacer" value="USP">&nbsp;</option>
<option class="lang" value="VIET">—Tiêng Viêt (VI)—</option>
<option value="VIET">1934 Vietnamese Bible (VIET)</option>
<option value="BD2011">Bản Dịch 2011 (BD2011)</option>
<option value="NVB">New Vietnamese Bible (NVB)</option>
<option value="BPT">Vietnamese Bible: Easy-to-Read Version (BPT)</option>
<option class="spacer" value="BPT">&nbsp;</option>
<option class="lang" value="CCB">—汉语 (ZH)—</option>
<option value="CCB">Chinese Contemporary Bible (Simplified) (CCB)</option>
<option value="CCBT">Chinese Contemporary Bible (Traditional) (CCBT)</option>
<option value="ERV-ZH">Chinese New Testament: Easy-to-Read Version (ERV-ZH)</option>
<option value="CNVS">Chinese New Version (Simplified) (CNVS)</option>
<option value="CNVT">Chinese New Version (Traditional) (CNVT)</option>
<option value="CSBS">Chinese Standard Bible (Simplified) (CSBS)</option>
<option value="CSBT">Chinese Standard Bible (Traditional) (CSBT)</option>
<option value="CUVS">Chinese Union Version (Simplified) (CUVS)</option>
<option value="CUV">Chinese Union Version (Traditional) (CUV)</option>
<option value="CUVMPS">Chinese Union Version Modern Punctuation (Simplified) (CUVMPS)</option>
<option value="CUVMPT">Chinese Union Version Modern Punctuation (Traditional) (CUVMPT)</option>
</select>
<?php

		echo'<p><input type="submit" value="'.__('Save','church-admin').'" class="button-primary"/></p></form>';

	echo'</div>';
	echo'<script type="text/javascript">jQuery(function(){  jQuery(".version-toggle").click(function(){jQuery(".bible-version").toggle();  });});</script>';

}


/**
 *
 * Church Admin App Lout person
 *
 * @author  Andy Moyle
 * @param    null
 * @return
 * @version  0.1
 *
 */
 function church_admin_logout_app($user_id)
 {
 	global $wpdb;
 	$wpdb->query('DELETE FROM '.CA_APP_TBL.' WHERE user_id="'.intval($user_id).'"');
 	church_admin_app();
 }
/**
 *
 * Church Admin App Logins
 *
 * @author  Andy Moyle
 * @param    null
 * @return
 * @version  0.1
 *
 */
function church_admin_app_logins()
{
	echo '<h2 class="logged-toggle">'.__('Logged in App Users (Click to toggle)','church-admin').'</h2>';
	echo'<div class="app-logins" style="display:none">';
	global $wpdb;
	$sql='SELECT a.*,CONCAT_WS(" ",b.first_name,b.last_name) AS name FROM '.CA_APP_TBL.' a LEFT JOIN '.CA_PEO_TBL.' b ON a.user_id=b.user_id ORDER BY a.last_login DESC';

	$results=$wpdb->get_results($sql);
	if(!empty($results))
	{
		echo'<table class="widefat striped"><thead><tr><th>'.__('Logout','church-admin').'</th><th>'.__('User','church-admin').'</th><th>'.__('Last login','church-admin').'</th><th>'.__('Last Page Visited','church-admin').'</th></tr></thead><tfoot><tr><th>'.__('Logout','church-admin').'</th><th>'.__('User','church-admin').'</th><th>'.__('Last login','church-admin').'</th><th>'.__('Last Page Visited','church-admin').'</th></tr></tfoot>';
		foreach($results AS $row)
		{
			$logout='<a class="button-secondary" href="'.wp_nonce_url('admin.php?page=church_admin/index.php&amp;tab=App&amp;action=logout_app&amp;user_id='.intval($row->user_id),'logout_app').'">'.__('Logout','church-admin').'</a>';
			echo'<tr><td>'.$logout.'</td><td>'.esc_html($row->name).'</td><td>'.mysql2date(get_option('date_format').' '.get_option('time_format'),$row->last_login).'</td><td>'.esc_html($row->last_page).'</td></tr>';
		}
		echo'</tbody></table>';
	}else{echo'<p>'.__('No-one is logged in','church-admin').'</p>';}
	echo'</div>';
	echo'<script type="text/javascript">jQuery(function(){  jQuery(".logged-toggle").click(function(){jQuery(".app-logins").toggle();  });});</script>';

}

/**
 *
 * Church Admin App Member Types
 *
 * @author  Andy Moyle
 * @param    null
 * @return
 * @version  0.1
 *
 */
 function church_admin_app_member_types()
 {
 		global $wpdb;
 		$member_types=church_admin_member_type_array();
 		echo'<h2 class="member-toggle">'.__('Which Member Types are viewable on the app','church-admin').'</h2>';
 		echo'<div class="member-types" style="display:none">';
 		if(!empty($_POST['save-app-member-types']))
 		{

 			$newmt=array();
 			foreach($_POST['types'] AS $key=>$value)
 			{
 				if(array_key_exists($value,$member_types))$newmt[]=intval($value);
 			}

 			update_option('church_admin_app_member_types',$newmt);
 		}
 		$mt=get_option('church_admin_app_member_types');

 		echo'<form action="" method="POST">';
 		foreach($member_types AS $key=>$value)
 		{
 			echo'<p><input type=checkbox value="'.intval($key).'" name="types[]" ';
 			if(!empty($mt)&&is_array($mt)&& in_array($key,$mt))echo' checked="checked" ';
 			echo'/>'.esc_html($value).'</p>';

 		}
 		echo'<p><input type="hidden" name="save-app-member-types" value="yes"/><input type="submit" class="button-primary" value="'.__('Save','church-admin').'"/></p></form>';
 	echo'</div>';
	echo'<script type="text/javascript">jQuery(function(){  jQuery(".member-toggle").click(function(){jQuery(".member-types").toggle();  });});</script>';

 }

/**
 *
 * Bible Reading Plan
 *
 * @author  Andy Moyle
 * @param    null
 * @return
 * @version  0.1
 *
 */
function church_admin_bible_reading_plan()
{
	global $wpdb;
	$current_user = wp_get_current_user();
	if(!church_admin_level_check('Directory'))wp_die(__('You don\'t have permissions to do that','church-admin'));
 if(is_user_logged_in()&& current_user_can('manage_options'))
 {


 	echo'<h2 class="plan-toggle">'.__('Which Bible Reading plan? (Click to toggle)','church-admin').'</h2>';


 	echo'<div class="bible-plans" style="display:none">';
	echo	'<p>'.__('The Bible reading post type for a particular day takes priority over any plan loaded below','church-admin').'</p>';
	if(!empty($_POST['save_csv'])&& check_admin_referer( 'bible_upload', 'nonce' ) )
	{
		$mimes = array('application/vnd.ms-excel','text/plain','text/csv','text/tsv');
		if(!empty($_FILES) && $_FILES['file']['error'] == 0 && in_array($_FILES['file']['type'],$mimes))
		{
			$wpdb->query('TRUNCATE TABLE '.CA_BRP_TBL);
			$plan=stripslashes($_POST['reading_plan_name']);
			update_option('church_admin_brp',$plan);
			$filename = $_FILES['file']['name'];
			$upload_dir = wp_upload_dir();
			$filedest = $upload_dir['path'] . '/' . $filename;
			if(move_uploaded_file($_FILES['file']['tmp_name'], $filedest))echo '<div class="notice notice-success notice-inline">'.__('File Uploaded and saved','church-admin').'</div>';

			ini_set('auto_detect_line_endings',TRUE);
			$file_handle = fopen($filedest, "r");
			$ID=1;
			while (($data = fgetcsv($file_handle, 1000, ",")) !== FALSE)
			{
				$reading=array();
				foreach($data AS $key=>$value)$reading[]=$value;
				$reading=serialize($reading);
				$wpdb->query('INSERT INTO '.CA_BRP_TBL.' (ID,readings)VALUES("'.$ID.'","'.esc_sql($reading).'")');
				$ID++;
			}

		}
	}
	else
	{
		$plan=get_option('church_admin_brp');
		if(!empty($plan)) echo'<h3>'.__('Current Bible Reading plan name','church-admin').':'. esc_html($plan).'</h3>';
		echo'<p>'.__('Import new Bible reading CSV - 365 rows day per row, comma separated passages','church-admin').'</p>';
		echo'<form action="" method="POST" enctype="multipart/form-data">';
		wp_nonce_field('bible_upload','nonce');
		echo'<p><label>'.__('Reading Plan Name','church-admin').'</label><input required="required" name="reading_plan_name" type="text"/></p>';
		echo'<p><label>'.__('CSV File','church-admin').'</label><input type="file" name="file" accept=".csv"/><input type="hidden" name="save_csv" value="yes"/></p>';
		echo'<p><input  class="button-primary" type="submit" Value="'.__('Upload','church-admin').'"/></p></form>';
	}
	echo'</div>';
	echo'<script type="text/javascript">jQuery(function(){  jQuery(".plan-toggle").click(function(){jQuery(".bible-plans").toggle();  });});</script>';
	}
	else{echo '<p>'.__('Only admins can upload bible reading plans','church-admin').'</p>';}
}


function church_admin_app_last_visited($page,$token)
{
	global $wpdb;
	$sql='UPDATE '.CA_APP_TBL.' SET last_page="'.esc_sql($page).'",last_login=NOW() WHERE UUID="'.esc_sql($token).'"';

	$wpdb->query($sql);
}


/**
 *
 * Checks token
 *
 * @author  Andy Moyle
 * @param    null
 * @return
 * @version  0.1
 *
 */
function ca_check_token()
{
		global $wpdb;
		$output=array('error'=>'login required');
		if(empty($_REQUEST['token']))
		{
			$output=array('error'=>'login required');
		}
		else
		{
			$sql='SELECT user_id FROM '.CA_APP_TBL.' WHERE UUID="'.esc_sql(stripslashes($_REQUEST['token'])).'"';
			$result=$wpdb->get_var($sql);
			if(!empty($result))
			{
				$output=array(TRUE);
				$wpdb->query('UPDATE '.CA_APP_TBL.' SET last_login=NOW() WHERE UUID="'.esc_sql(stripslashes($_REQUEST['token'])).'"');
			}
			else
			{
				$output=array('error'=>'login required');
			}
		}
		header('Access-Control-Max-Age: 1728000');
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: *');
		header('Access-Control-Allow-Headers: Content-MD5, X-Alt-Referer');
		header('Access-Control-Allow-Credentials: true');
		echo json_encode($output);
		exit();
}
add_action("wp_ajax_ca_check_token", "ca_check_token");
add_action("wp_ajax_nopriv_ca_check_token", "ca_check_token");
/**
 *
 * Returns media
 *
 * @author  Andy Moyle
 * @param    null
 * @return
 * @version  0.1
 *
 */

function ca_sermons()
{
		global $wpdb;

		if(!empty($_GET['token']))church_admin_app_last_visited(__('Sermons','church-admin'),$_GET['token']);
		$url=content_url().'/uploads/sermons/';
		$output=array();

		$sql='SELECT * FROM '.CA_FIL_TBL.' ORDER BY pub_date DESC LIMIT 5';

		$results=$wpdb->get_results($sql);

		if(!empty($results))
		{
			foreach($results AS $row)
			{


				$output[]=array('title'=>esc_html($row->file_title),'id'=>intval($row->file_id),'description'=>esc_html($row->file_description),'speaker'=>esc_html($row->speaker),'pub_date'=>mysql2date(get_option('date_format'),$row->pub_date),'file_url'=>esc_url($url.$row->file_name));
			}
		}


		header('Access-Control-Max-Age: 1728000');
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: *');
		header('Access-Control-Allow-Headers: Content-MD5, X-Alt-Referer');
		header('Access-Control-Allow-Credentials: true');
		echo json_encode($output);
		exit();
}
add_action("wp_ajax_ca_sermons", "ca_sermons");
add_action("wp_ajax_nopriv_ca_sermons", "ca_sermons");
/**
 *
 * Returns one sermon media
 *
 * @author  Andy Moyle
 * @param    null
 * @return
 * @version  0.1
 *
 */
function ca_sermon()
{
		global $wpdb;

		$url=content_url().'/uploads/sermons/';
		$output=array();

		$sql='SELECT * FROM '.CA_FIL_TBL.' WHERE file_id="'.intval($_REQUEST['ID']).'"';

		$row=$wpdb->get_row($sql);

		if(!empty($row))
		{

				$output=array('title'=>esc_html($row->file_title),'id'=>intval($row->file_id),'description'=>esc_html($row->file_description),'speaker'=>esc_html($row->speaker),'pub_date'=>mysql2date(get_option('date_format'),$row->pub_date),'file_url'=>esc_url($url.$row->file_name));
			if(empty($row->file_name)&&!empty($row->external_file))$output['file_url']=esc_url($row->external_file);
			if(empty($row->file_name)&&empty($row->external_file))$output=array('error'=>'No sermon found');
		}
		else
		{
			$output=array('error'=>'No sermon found');
		}


		header('Access-Control-Max-Age: 1728000');
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: *');
		header('Access-Control-Allow-Headers: Content-MD5, X-Alt-Referer');
		header('Access-Control-Allow-Credentials: true');
		echo json_encode($output);
		exit();
}
add_action("wp_ajax_ca_sermon", "ca_sermon");
add_action("wp_ajax_nopriv_ca_sermon", "ca_sermon");
/**
 *
 * Returns posts
 *
 * @author  Andy Moyle
 * @param    null
 * @return
 * @version  0.1
 *
 */
function ca_prayer_requests()
{
	global $wpdb;
	if(!empty($_GET['token']))church_admin_app_last_visited(__('Prayer Request','church-admin'),$_GET['token']);

	$private=get_option('church-admin-private-prayer-requests');
	if($private)
	{

		if(empty($_GET['token']))
		{//private but no token
			$output=array('error'=>'login required');

		}
		else
		{//private and check token
			$sql='SELECT user_id FROM '.CA_APP_TBL.' WHERE UUID="'.esc_sql(stripslashes($_GET['token'])).'"';
			$result=$wpdb->get_var($sql);
			if(empty($result))
			{//private and no login

				$output=array('error'=>'login required');
			}
			else
			{//private and logged in
				$output=ca_prayer_reqs();
			}
		}
	}
	else
	{
			//not private
			$output=ca_prayer_reqs();
	}

	header('Access-Control-Max-Age: 1728000');
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: *');
	header('Access-Control-Allow-Headers: Content-MD5, X-Alt-Referer');
	header('Access-Control-Allow-Credentials: true');
	echo json_encode($output);

	die();
}

function ca_prayer_reqs()
{

	$posts_array = array();

	$args = array("post_type" => "prayer-requests", "orderby" => "date", "order" => "DESC", "post_status" => "publish", "posts_per_page" => "5");

	$posts = new WP_Query($args);

	if($posts->have_posts()):
		while($posts->have_posts()):
			$posts->the_post();
            $content = get_the_content();
			$content = '<div>'.$content.'</div>';
			$content= do_shortcode($content);
            $post_array = array('title'=>get_the_title(),'content'=>$content,'date'=> get_the_date(),'ID'=>get_the_ID());
            array_push($posts_array, $post_array);

		endwhile;
		else:
        	 return array();

	endif;
	return($posts_array);


}



add_action("wp_ajax_ca_prayer", "ca_prayer_requests");
add_action("wp_ajax_nopriv_ca_prayer", "ca_prayer_requests");
/**
 *
 * Returns posts
 *
 * @author  Andy Moyle
 * @param    null
 * @return
 * @version  0.1
 *
 */
function ca_posts()
{
	header('Access-Control-Max-Age: 1728000');
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: *');
	header('Access-Control-Allow-Headers: Content-MD5, X-Alt-Referer');
	header('Access-Control-Allow-Credentials: true');
	if(!empty($_GET['token']))church_admin_app_last_visited(__('News','church-admin'),$_GET['token']);

	$posts_array = array();

	$args = array("post_type" => "post", "orderby" => "date", "order" => "DESC", "post_status" => "publish", "posts_per_page" => "10");
	if(!empty($_GET['page']))$args['paged']=intval($_GET['page']);
	$posts = new WP_Query($args);

	if($posts->have_posts()):
		while($posts->have_posts()):
			$posts->the_post();
            $post_array = array(get_the_title(), get_the_permalink(), get_the_date(), wp_get_attachment_url(get_post_thumbnail_id()),get_the_ID());
            array_push($posts_array, $post_array);

		endwhile;
		else:
        	echo "{'posts' = []}";
        	die();
	endif;

	echo json_encode($posts_array);

	die();
}



add_action("wp_ajax_ca_posts", "ca_posts");
add_action("wp_ajax_nopriv_ca_posts", "ca_posts");
/**
 *
 * Returns one post
 *
 * @author  Andy Moyle
 * @param    null
 * @return
 * @version  0.1
 *
 */
function ca_post()
{
	header('Access-Control-Max-Age: 1728000');

	header('Access-Control-Allow-Origin: *');

	header('Access-Control-Allow-Methods: *');

	header('Access-Control-Allow-Headers: Content-MD5, X-Alt-Referer');

	header('Access-Control-Allow-Credentials: true');


	$post=get_post($_REQUEST['ID']);
	$user = get_userdata($post->post_author);
	if(!empty($_GET['token']))
	{
			church_admin_app_last_visited($post->post_title,$_GET['token']);
	}
	$data=array('title'=>$post->post_title,'content'=>nl2br(do_shortcode($post->post_content)),'author'=>$user->first_name.' '.$user->last_name,'date'=>mysql2date(get_option('date_format'),$post->post_date));

	echo json_encode($data);

	die();
}
add_action("wp_ajax_ca_post", "ca_post");
add_action("wp_ajax_nopriv_ca_post", "ca_post");
/**
 *
 * Returns rota
 *
 * @author  Andy Moyle
 * @param    null
 * @return
 * @version  0.1
 *
 */
function ca_json_rota()
{
	global $wpdb;
	if(defined('CA_DEBUG'))church_admin_debug(print_r($_GET,TRUE));
	if(!empty($_GET['version']) && version_compare($_GET['version'],2.6,'>=')>=0)
	{


		$private=get_option('church-admin-private-schedule');
		if($private)
		{

			if(empty($_GET['token']))
			{//private but no token
				$output=array('error'=>'login required');

			}
			else
			{//private and check token
				$sql='SELECT user_id FROM '.CA_APP_TBL.' WHERE UUID="'.esc_sql(stripslashes($_GET['token'])).'"';
				$result=$wpdb->get_var($sql);
				if(empty($result))
				{//private and no login

					$output=array('error'=>'login required');
				}
				else
				{//private and logged in
					$output=ca_json_rota_output();
				}
			}
		} //not private and app >2.6
		else $output=ca_json_rota_output();
	}
	else
	{//app is less than 2.6
		$output=ca_json_rota_output();
	}

	header('Access-Control-Max-Age: 1728000');
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: *');
	header('Access-Control-Allow-Headers: Content-MD5, X-Alt-Referer');
	header('Access-Control-Allow-Credentials: true');
	echo json_encode($output);
	die();
}

function ca_json_rota_output()
{
	global $wpdb;
	if(!empty($_GET['token']))church_admin_app_last_visited(__('Rota','church-admin'),$_GET['token']);
	$output=$rota=array();
	$check=$wpdb->get_var('SELECT count(*) FROM '.CA_ROTA_TBL);
	if(empty($check))
	{
		return array('error'=>"No one is doing anything yet");
	}
	//put chosen rota_id as first in json for dropdown
	if(!empty($_REQUEST['rota_id']))
	{
		$sql='SELECT a.rota_date, a.rota_id,b.service_name,b.service_time,c.venue FROM '.CA_ROTA_TBL.' a LEFT JOIN '.CA_SER_TBL.' b ON a.service_id=b.service_id  LEFT JOIN '.CA_SIT_TBL.' c ON b.site_id=c.site_id WHERE a.rota_id="'.intval($_REQUEST['rota_id']).'"';
		$row=$wpdb->get_row($sql);
		if(!empty($row))$rota['services'][]=array('rota_id'=>intval($row->rota_id),'detail'=>mysql2date("j M",$row->rota_date).' '.mysql2date(get_option('time_format'),$row->service_time).' '.esc_html($row->service_name));
	}
	//grab next 12 meetings

	$sql='SELECT a.rota_date, a.rota_id,b.service_name,b.service_time,c.venue FROM '.CA_ROTA_TBL.' a LEFT JOIN '.CA_SER_TBL.' b ON a.service_id=b.service_id  LEFT JOIN '.CA_SIT_TBL.' c ON b.site_id=c.site_id WHERE a.rota_date >= CURDATE( ) GROUP BY a.service_id, a.rota_date ORDER BY rota_date ASC LIMIT 36';
	$results=$wpdb->get_results($sql);
	foreach($results AS $row)
	{
		$rota['services'][]=array('rota_id'=>intval($row->rota_id),'detail'=>mysql2date("j M",$row->rota_date).' '.mysql2date(get_option('time_format'),$row->service_time).' '.esc_html($row->service_name));
	}

	//rota details for requested service
	if(!empty($_REQUEST['rota_id']))
	{
		$rota_id=intval($_REQUEST['rota_id']);
		$sql='SELECT a.*,b.service_name,a.rota_date FROM '.CA_ROTA_TBL.'  a,'.CA_SER_TBL.' b WHERE a.rota_id="'.$rota_id.'" AND a.service_id =b.service_id';
	}
	else
	{

		$sql='SELECT a.*,b.service_name,a.rota_date FROM '.CA_ROTA_TBL.'  a  LEFT JOIN '.CA_SER_TBL.' b ON a.service_id =b.service_id WHERE a.rota_date>=CURDATE() ORDER BY rota_date ASC LIMIT 1';
	}

	$selectedService=$wpdb->get_row($sql);

	//workout which rota jobs are required
	$rota_tasks=$wpdb->get_results('SELECT * FROM '.CA_RST_TBL.' ORDER BY rota_order');
	$requiredRotaJobs=$rotaDates=array();
	foreach($rota_tasks AS $rota_task)
	{
		$allServiceID=maybe_unserialize($rota_task->service_id);
		if(is_array($allServiceID)&&in_array($selectedService->service_id,$allServiceID))$requiredRotaJobs[$rota_task->rota_id]=$rota_task->rota_task;
	}
	$sql='SELECT * FROM '.CA_ROTA_TBL.' WHERE service_id="'.intval($selectedService->service_id).'" AND mtg_type="service" AND rota_date>='.$selectedService->rota_date;
	$rotaDatesResults=$wpdb->get_results($sql);

	foreach($requiredRotaJobs AS $rota_task_id=>$value)
	{
		$people=esc_html(church_admin_rota_people($selectedService->rota_date,$rota_task_id,$selectedService->service_id,'service'));
		if(!empty($people))$rota['tasks'][]=array('job'=>esc_html($value),'people'=>$people);

	}




	return $rota;

}



add_action("wp_ajax_ca_rota", "ca_json_rota");
add_action("wp_ajax_nopriv_ca_rota", "ca_json_rota");
/**
 *
 * Returns calendar
 *
 * @author  Andy Moyle
 * @param    null
 * @return
 * @version  0.1
 *
 */
function ca_json_cal()
{
	global $wpdb;
	if(!empty($_GET['token']))church_admin_app_last_visited(__('Calendar','church-admin'),$_GET['token']);
	$output=$op=array();
	//dates
	$date=$_REQUEST['date'];
	if(!church_admin_checkdate($date)){$date=NULL;}
	$output['dates']=ca_createweeklist($date);


	//information for dates
	$now='CURDATE()';
	if(church_admin_checkdate($date))$now='"'.$date.'"';
	$sql='SELECT event_id, title,description,start_date,start_time,end_time,location FROM '.CA_DATE_TBL.' WHERE general_calendar=1 AND start_date BETWEEN '.$now.' AND DATE_ADD('.$now.', INTERVAL 7 DAY) ORDER By start_date ASC';

	$results=$wpdb->get_results($sql);
	if(!empty($results))
	{
		foreach($results AS $row)
		{
			$output['cal'][]=array(
							'title'=>$row->title,
							'description'=>$row->description,
							'location'=>esc_html($row->location),
							'start_date'=>mysql2date(get_option('date_format'),$row->start_date),
							'iso_date'=>esc_html($row->start_date),
							'iso_start_time'=>esc_html($row->start_time),
							'iso_end_time'=>esc_html($row->end_time),
							'start_time'=>mysql2date(get_option('time_format'),$row->start_time),
							'end_time'=>mysql2date(get_option('time_format'),$row->end_time),
							'event_id'=>intval($row->event_id)
							);

		}

	}else{$output['error']="There are no events this week in the calendar.";}
	header('Access-Control-Max-Age: 1728000');
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: *');
	header('Access-Control-Allow-Headers: Content-MD5, X-Alt-Referer');
	header('Access-Control-Allow-Credentials: true');
	echo json_encode($output);
	die();
}



add_action("wp_ajax_ca_cal", "ca_json_cal");
add_action("wp_ajax_nopriv_ca_cal", "ca_json_cal");

/**
 *
 * Returns week of list
 *
 * @author  Andy Moyle
 * @param    null
 * @return
 * @version  0.1
 *
 */
function ca_createweeklist($date) {
	$dates=array();
	if(!empty($date)&&church_admin_checkdate($date))$dates[]=array('mysql'=>$date,'friendly'=>date('D jS M', strtotime($date)));
	// assuming your week starts  sunday

	// set start date
	// function will return the monday of the week this date is in
	// eg the monday of the week containing 1/1/2005
	// was 31/12/2004

	$startdate = ca_sundayofweek(date("j"), date("n"), date("Y"));

	// set end date
	// the values below use the current date

	$enddate = ca_sundayofweek(date('j',strtotime('+12 weeks')),date('n',strtotime('+12 weeks')),date('Y',strtotime('+12 weeks')));

	// $currentdate loops through each inclusive monday in the date range

	$currentdate = $startdate;

	do {

		$dates[]=array('mysql'=>date("Y-m-d", $currentdate),'friendly'=>date('D jS M', $currentdate));

		$currentdate = strtotime("12pm next Sunday", $currentdate);

	} while ($currentdate <= $enddate);
	return $dates;

}

function ca_sundayofweek($day, $month, $year) {

	// setting the time to noon avoids any daylight savings time issues

	$returndate = mktime(12, 0, 0, $month, $day, $year);

	// if the date isnt a sunday adjust it to the previous sunday

	if (date("w", $returndate) != 0) {

		$returndate = strtotime("12pm last sunday", $returndate);

	}

	return $returndate;

}
/**
 *
 * Login
 *
 * @author  Andy Moyle
 * @param    null
 * @return
 * @version  0.1
 *
 */
function ca_login()
{
	global $wpdb;

	$creds = array();
	$creds['user_login'] = $_GET["username"];
	$creds['user_password'] = $_GET["password"];
	$user = wp_signon( $creds, false );

	if (empty($user->ID))
	{

		$op=array('error'=>'login required');

	}else
	{
		$sql='SELECT app_id FROM '.CA_APP_TBL.' WHERE UUID="'.esc_sql(stripslashes($_GET['UUID'])).'"';

		$check=$wpdb->get_var($sql);
		if($check)
		{
			//update
			$wpdb->query('UPDATE '.CA_APP_TBL.' SET last_login="'.date('Y-m-d h:i:s').'" WHERE UUID="'.esc_sql(stripslashes($_GET['UUID'])).'"');

		}
		else
		{
			//store hashed UUID to use as token along with people_id, user_id
			$sql='INSERT INTO '.CA_APP_TBL.' (UUID,user_id,last_login)VALUES("'.esc_sql(stripslashes($_GET['UUID'])).'","'.$user->ID.'","'.date('Y-m-d h:i:s').'")';

			$wpdb->query($sql);
		}
		$op=array('login'=>true);
	}
	header('Access-Control-Max-Age: 1728000');
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: *');
	header('Access-Control-Allow-Headers: Content-MD5, X-Alt-Referer');
	header('Access-Control-Allow-Credentials: true');
	echo json_encode($op);
	die();
}

add_action("wp_ajax_ca_login", "ca_login");
add_action("wp_ajax_nopriv_ca_login", "ca_login");


function ca_search()
{
	global $wpdb;
	if(!empty($_GET['token']))church_admin_app_last_visited(__('Address List','church-admin'),$_GET['token']);
	$output=array();
	//check token first
	if(empty($_GET['token']))
	{
		$output=array('error'=>'login required');

	}
	else
	{
		$sql='SELECT user_id FROM '.CA_APP_TBL.' WHERE UUID="'.esc_sql(stripslashes($_GET['token'])).'"';
		$result=$wpdb->get_var($sql);
		if(empty($result))
		{

			$output=array('error'=>'login required');
		}
		else
		{
			$s=esc_sql(stripslashes($_GET['search']));
			$mt=get_option('church_admin_app_member_types');
			if(empty($mt))$mt=array(1);
			foreach($mt AS $key=>$type){$mtsql[]='a.member_type_id='.intval($type);}
			//adjust member_type_id section
			$sql='SELECT a.*,b.address,b.phone FROM '.CA_PEO_TBL.' a LEFT JOIN '.CA_HOU_TBL.' b ON b.household_id=a.household_id WHERE a.household_id=b.household_id AND ('.implode('||',$mtsql).')AND  (CONCAT_WS(" ",a.first_name,a.last_name) LIKE("%'.$s.'%")||CONCAT_WS(" ",a.first_name,a.middle_name,a.last_name) LIKE("%'.$s.'%")||a.nickname LIKE("%'.$s.'%")||a.first_name LIKE("%'.$s.'%")||a.middle_name LIKE("%'.$s.'%")||a.last_name LIKE("%'.$s.'%")||a.email LIKE("%'.$s.'%")||a.mobile LIKE("%'.$s.'%")||b.address LIKE("%'.$s.'%")||b.phone LIKE("%'.$s.'%")) AND (b.privacy=0 OR b.privacy IS NULL) ORDER BY a.last_name,a.people_order,a.first_name';

    		$results=$wpdb->get_results($sql);

			if(!empty($results))
			{
				foreach($results AS $row)
				{
					if(empty($row->phone))$row->phone='';
					if(empty($row->mobile))$row->mobile='';
					if(!empty($row->address)){$address=explode(", ", $row->address);}else{$address=array(0=>NULL,1=>NULL,2=>NULL,3=>NULL);}
$output[]=array('id'=>intval($row->people_id),'first_name'=>esc_html($row->first_name),'last_name'=>esc_html($row->last_name),'name'=>esc_html($row->first_name).' '.esc_html($row->last_name),'email'=>esc_html($row->email),'mobile'=>esc_html($row->mobile),'phone'=>esc_html($row->phone),'address'=>esc_html($row->address),'streetAddress'=>$address[0],'locality'=>$address[1],'region'=>$address[2],'postalCode'=>$address[3]);
				}
			}
			else{$output=array('error'=>'No results');}
		}

	}
	header('Access-Control-Max-Age: 1728000');
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: *');
	header('Access-Control-Allow-Headers: Content-MD5, X-Alt-Referer');
	header('Access-Control-Allow-Credentials: true');
	echo json_encode($output);
		die();
}
add_action("wp_ajax_ca_search", "ca_search");
add_action("wp_ajax_nopriv_ca_search", "ca_search");


function ca_groups()
{
	global $wpdb,$wp_locale;
	if(!empty($_GET['token']))church_admin_app_last_visited(__('Groups','church-admin'),$_GET['token']);
	$sql='SELECT * FROM '.CA_SMG_TBL.' WHERE id!=1';
	$results = $wpdb->get_results($sql);
	if(!empty($results))
	{
		foreach ($results as $row)
		{$output[]=array('name'=>esc_html($row->group_name),'whenwhere'=>esc_html($wp_locale->get_weekday($row->group_day).' '.mysql2date(get_option('time_format'),$row->group_time)),'address'=>esc_html($row->address),'lat'=>$row->lat,'lng'=>$row->lng);}

	}else
	{
		$output=array('error'=>__('No small groups yet','church-admin'));

	}
	header('Access-Control-Max-Age: 1728000');
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: *');
	header('Access-Control-Allow-Headers: Content-MD5, X-Alt-Referer');
	header('Access-Control-Allow-Credentials: true');
	echo json_encode($output);
	die();
}
add_action("wp_ajax_ca_groups", "ca_groups");
add_action("wp_ajax_nopriv_ca_groups", "ca_groups");

function ca_forgotten_password()
{
		$login = trim($_GET['user_login']);
		$user_data = get_user_by('login', $login);
		if(empty($user_data)){$output=array('error'=>'<p>User details not found, please try again</p>');}
		else
		{
			// Redefining user_login ensures we return the right case in the email.
			$user_login = $user_data->user_login;
			$user_email = $user_data->user_email;
			$key = get_password_reset_key( $user_data );
			$message = 'Someone has requested a password reset for the following account at '. "\r\n\r\n";
			$message .= network_home_url( '/' ) . "\r\n\r\n";
			$message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
			$message .= 'If this was a mistake, just ignore this email and nothing will happen.' . "\r\n\r\n";
			$message .= 'To reset your password, visit the following address:' . "\r\n\r\n";
			$message .= '<' . site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login') . '>'."\r\n";
			/*
			 * The blogname option is escaped with esc_html on the way into the database
			 * in sanitize_option we want to reverse this for the plain text arena of emails.
			 */
			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
			$title = sprintf( __('[%s] Password Reset'), $blogname );
			$message = apply_filters( 'retrieve_password_message', $message, $key, $user_login, $user_data );

			if ( $message && wp_mail( $user_email, wp_specialchars_decode( $title ), $message ) ){	$output=array('message'=>'<p>Password email has been sent to your registered email address</p>');}
			else{$output=array('error'=>'<p>Password reset email failed to send. Please try again.</p>');}
		}
		header('Access-Control-Max-Age: 1728000');
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: *');
		header('Access-Control-Allow-Headers: Content-MD5, X-Alt-Referer');
		header('Access-Control-Allow-Credentials: true');
		echo json_encode($output);
		die();
}
add_action("wp_ajax_ca_forgotten_password", "ca_forgotten_password");
add_action("wp_ajax_nopriv_ca_forgotten_password", "ca_forgotten_password");


function ca_my_group()
{

	global $wpdb;

	$output=array();
	//check token first
	if(empty($_GET['token']))
	{
		$output=array('error'=>'login required');
		if(defined('CA_DEBUG'))church_admin_debug('No token');

	}
	else
	{

		$sql='SELECT user_id FROM '.CA_APP_TBL.' WHERE UUID="'.esc_sql(stripslashes($_GET['token'])).'"';

		$userID=$wpdb->get_var($sql);
		if(empty($userID))
		{

			$output=array('error'=>'login required');
		}
		else
		{
			if(empty($_GET['version']))
			{//app version <=2.5

				//get group ID
				$groupID=$wpdb->get_var('SELECT a.ID FROM '.CA_MET_TBL.' a, '.CA_PEO_TBL.' b WHERE a.meta_type="smallgroup" AND b.user_ID="'.intval($userID).'" and a.people_id=b.people_id');

				if(!empty($groupID)&&groupID!=1)
				{
						$output=ca_get_group($groupID);
				}
				else{$output=array('error'=>'No results');}
			}//end of old version
			else
			{
				/********************************************
				*
				*	From app v2.6, look for multiple groups
				*
				*********************************************/
				$groupIDs=$wpdb->get_results('SELECT a.ID FROM '.CA_MET_TBL.' a, '.CA_PEO_TBL.' b WHERE a.meta_type="smallgroup" AND b.user_ID="'.intval($userID).'" and a.people_id=b.people_id AND a.ID!=1');
				if(defined('CA_DEBUG'))church_admin_debug('Group IDS'.print_r($groupIDs,TRUE));
				if(!empty($groupIDs))
				{
					$output=array();
					 foreach($groupIDs AS $groupID)
					 $output[]=ca_get_group($groupID->ID);
				}
				else
				{//no groups found for user
					$output=array('error'=>'No results');
				}
				if(defined('CA_DEBUG'))church_admin_debug('Output'.print_r($output,TRUE));
			}//endapp version>=2.6

		}
	}
	header('Access-Control-Max-Age: 1728000');
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: *');
	header('Access-Control-Allow-Headers: Content-MD5, X-Alt-Referer');
	header('Access-Control-Allow-Credentials: true');
	echo json_encode($output);
		die();
}
add_action("wp_ajax_ca_my_group", "ca_my_group");
add_action("wp_ajax_nopriv_ca_my_group", "ca_my_group");
function ca_get_group($group_id)
{
	global $wpdb;
	//person is in a group
	//get group name
	$groupDetails=$wpdb->get_row('SELECT * FROM '.CA_SMG_TBL.' WHERE id="'.intval($group_id).'"');
	$output=array();
	$output['group_name']=esc_html($groupDetails->group_name);
	$output['when_where']=esc_html($groupDetails->whenwhere.' '.$groupDetails->address);
	$output['group_id']=$groupID->ID;
	//get group members
	$mt=get_option('church_admin_app_member_types');
	if(empty($mt))$mt=array(1);
	foreach($mt AS $key=>$type){$mtsql[]='a.member_type_id='.intval($type);}
	$sql='SELECT a.*,b.address,b.phone FROM '.CA_PEO_TBL.' a, '.CA_HOU_TBL.' b, '.CA_MET_TBL.' c WHERE ('.implode('||',$mtsql).') AND a.household_id=b.household_id AND a.people_id=c.people_id AND c.meta_type="smallgroup" AND c.ID="'.intval($group_id).'"  ORDER BY a.last_name,a.people_order,a.first_name';

	$results=$wpdb->get_results($sql);

	if(!empty($results))
	{
		foreach($results AS $row)
		{
			if(empty($row->phone))$row->phone='';
			if(empty($row->mobile))$row->mobile='';
			$address=implode(', ',$row->address);
			$output['people'][]=array('id'=>intval($row->people_id),'first_name'=>esc_html($row->first_name),'last_name'=>esc_html($row->last_name),'name'=>esc_html($row->first_name).' '.esc_html($row->last_name),'email'=>esc_html($row->email),'mobile'=>esc_html($row->mobile),'phone'=>esc_html($row->phone),'address'=>esc_html($row->address),'streetAddress'=>$address[0],'locality'=>$address[1],'region'=>$address[2],'postalCode'=>$address[3]);
		}
	}
	return $output;
}

function ca_which_group()
{
	global $wpdb;
	if(empty($_GET['token']))
	{
		$output=array('error'=>'login required');

	}
	else
	{

		$sql='SELECT user_id FROM '.CA_APP_TBL.' WHERE UUID="'.esc_sql(stripslashes($_GET['token'])).'"';

		$userID=$wpdb->get_var($sql);
		if(empty($userID))
		{

			$output=array('error'=>'login required');
		}
		else
		{
			$peopleID=$wpdb->get_var('SELECT a.people_id FROM '.CA_PEO_TBL.' a,'.CA_APP_TBL.' b WHERE a.user_id=b.user_id AND b.UUID="'.esc_sql(stripslashes($_GET['token'])).'"');
			$groupID=$wpdb->get_var('SELECT a.ID FROM '.CA_MET_TBL.' a, '.CA_PEO_TBL.' b WHERE a.meta_type="smallgroup" AND b.people_ID="'.intval($peopleID).'" and a.people_id=b.people_id');
			$groupName=$wpdb->get_var('SELECT group_name FROM '.CA_SMG_TBL.' WHERE id="'.intval($groupID).'"');
			$output=array('groupID'=>$groupID,'peopleID'=>$peopleID,'groupName'=>$groupName);
		}
	}
	header('Access-Control-Max-Age: 1728000');
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: *');
	header('Access-Control-Allow-Headers: Content-MD5, X-Alt-Referer');
	header('Access-Control-Allow-Credentials: true');
	echo json_encode($output);
		die();
}
add_action("wp_ajax_ca_which_group", "ca_which_group");
add_action("wp_ajax_nopriv_ca_which_group", "ca_which_group");


function ca_bible_readings()
{
global $wpdb;
if(defined('CA_DEBUG'))church_admin_debug(print_r($_GET,TRUE));
	if(!empty($_GET['token']))church_admin_app_last_visited(__('Bible Reading','church-admin'),$_GET['token']);
	//bible readings ID starts at 1 date('z') returns 0 for Jan 1

	$version=get_option('church_admin_bible_version');
	if(!empty($_GET['version']))$version=$_GET['version'];
	$ID=date('z',strtotime('Today'))+1;
	//v1.1.0 of the app sends $_GET['date'] to get date, still need to add 1 though!
	//if(!empty($_GET['date'])) $ID=date('z' , strtotime($_GET['date']) )+1;
	//android sends the date in a way strtotime cannot formatting
	if(!empty($_GET['date']))
	{
		$d=\DateTime::createFromFormat('D M d Y H:i:s e+',$_GET['date']);
		$date=$d->format('Y-m-d');
		if(defined('CA_DEBUG'))church_admin_debug($date);
		$ID=$d->format('z')+1;
	}
	else{$date=date('Y-m-d');}
	$out=array();
	//check to see if there is a post in bible-readings for the date

	$sql='SELECT * FROM '.$wpdb->posts.' WHERE post_type="bible-readings" AND DATE_FORMAT(post_date, "%Y-%m-%d")="'.$date.'" AND (post_status="publish" OR post_status="future")';

	$bible_readings=$wpdb->get_results($sql);

	if(!empty($bible_readings))
	{//use the Bible Reading post type
		foreach($bible_readings AS $bible_reading)
		{
			$output='<h2>'.esc_html($bible_reading->post_title).'</h2>';
			$passage=get_post_meta( $bible_reading->ID ,'bible-passage',TRUE);
			$output.='<p class="ca-bible-reading"><a href="https://www.biblegateway.com/passage/?search='.urlencode($passage).'&version='.urlencode($version).'&interface=print"  >'.esc_html($passage).'</a></p>';
			$output.='<p>'.nl2br($bible_reading->post_content,TRUE).'</p>';
			$output.='<p>'.get_the_author_meta( $bible_reading->post_author).'</p>';
			$out[]=$output;
			//if($bible_reading->post_status=="future")$wpdb->query('UPDATE '.$wpdb->posts.' SET post_status="publish" WHERE ID="'.intval($bible_reading->ID).'"');
		}

	}
	else
	{//use the old style bible reading plan
		$sql='SELECT * FROM '.CA_BRP_TBL.' WHERE ID="'.$ID.'"';
		$data=$wpdb->get_row($sql);
		$version=$_GET['version'];
		if(empty($version))$version=get_option('church_admin_bible_version');
		if(empty($version))$version="ESV";
		$readings=maybe_unserialize($data->readings);
		if(!empty($readings))
		{
			foreach($readings AS $key=>$value)
			{
				$out[]='<p class="ca-bible-reading"><a href="https://www.biblegateway.com/passage/?search='.urlencode($value).'&version='.urlencode($version).'&interface=print" >'.esc_html($value).'</a></p>';
			}
		}else $out=array('error'=>'No passages');
	}

	$output=$out;
	header('Access-Control-Max-Age: 1728000');
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: *');
	header('Access-Control-Allow-Headers: Content-MD5, X-Alt-Referer');
	header('Access-Control-Allow-Credentials: true');
	echo json_encode($output);
	die();

}
add_action("wp_ajax_ca_bible_readings", "ca_bible_readings");
add_action("wp_ajax_nopriv_ca_bible_readings", "ca_bible_readings");


function ca_app_my_rota()
{


	global $wpdb;


	if(empty($_GET['token']))
	{
		$output=array('error'=>'login required');

	}
	else
	{
		$people=$wpdb->get_row('SELECT CONCAT_WS(" ",a.first_name,a.last_name) AS name, a.people_id FROM '.CA_PEO_TBL.' a, '.CA_APP_TBL.' b WHERE a.user_id=b.user_id AND b.UUID="'.esc_sql(stripslashes($_GET['token'])).'"');

		if(empty($people->people_id))
		{
			$output=array('error'=>"Your user identity is not connected to a church user profile.");

		}
		else
		{

			$sql='SELECT a.service_name,a.service_time, b.rota_task,c.rota_date,a.service_id FROM '.CA_SER_TBL.' a, '.CA_RST_TBL.' b, '.CA_ROTA_TBL.' c WHERE a.service_id=c.service_id AND c.mtg_type="service" AND c.rota_task_id=b.rota_id  AND c.people_id="'.intval($people->people_id).'" AND c.rota_date>=CURDATE() ORDER BY c.rota_date ASC';

			$results=$wpdb->get_results($sql);
			if(!empty($results))
			{
				$task=$output=array();
				foreach($results AS  $row)
				{

					$service=esc_html($row->service_name.' '.$row->service_time);
					$date=mysql2date(get_option('date_format'),$row->rota_date);
					$task[$row->rota_date][]=array('date'=>$date,'job'=>esc_html($row->rota_task).' - '.esc_html($row->service_name.' '.$row->service_time));
				}
				foreach($task AS $date=>$values)$output[]=$values;
			}
			else $output=array('error'=>'no-rota-jobs');

		}
	}



	header('Access-Control-Max-Age: 1728000');
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: *');
	header('Access-Control-Allow-Headers: Content-MD5, X-Alt-Referer');
	header('Access-Control-Allow-Credentials: true');
	echo json_encode($output);
	die();

}
add_action("wp_ajax_ca_my_rota", "ca_app_my_rota");
add_action("wp_ajax_nopriv_ca_my_rota", "ca_app_my_rota");

function ca_home()
{
	$menu_title=$menu_title=get_option('church_admin_app_menu_title');
	$home=get_option('church_admin_app_home');
	$giving=get_option('church_admin_app_giving');
	$groups=get_option('church_admin_app_groups');
	$logo=get_option('church_admin_app_logo');
	$style=get_option('church_admin_app_style');
	$church_id=get_option('church_admin_app_id');
	$menu=get_option('church-admin-app-menu');
	$menuOutput='<li id="home-tab-button" class="tab-button" data-tab="#home"><i class="fa fa-home fa-2x" aria-hidden="true"></i> <span class="languagespecificHTML" data-text="home">Home</span></li>';
	$menuOutput.='<li id="account-tab-button" class="tab-button" data-tab="#account"  data-tap-toggle="false"><i class="fa fa-user fa-2x" aria-hidden="true"></i> <span class="languagespecificHTML" data-text="account">Account</span></li>';
	$menuOutput.='<li id="address-tab-button" class="tab-button" data-tab="#address" data-tap-toggle="false"><i class="fa fa-phone fa-2x" aria-hidden="true"></i> <span class="languagespecificHTML" data-text="address">Address</span></li>';
	if(!empty($menu['Bible']))$menuOutput.='<li id="bible-tab-button" class="tab-button" data-tab="#bible"  data-tap-toggle="false"><i class="fa fa-book fa-2x" aria-hidden="true"></i> <span class="languagespecificHTML" data-text="bible">Bible</span><span id="bible-badge"></span></li>';
	if(!empty($menu['Calendar']))$menuOutput.='<li id="calendar-tab-button" class="tab-button" data-tab="#calendar"  data-tap-toggle="false"><i class="fa fa-calendar fa-2x" aria-hidden="true"></i> <span class="languagespecificHTML" data-text="calendar">Calendar</span></li>';
	if(!empty($menu['Checkin']))$menuOutput.='<li id="classes-tab-button" class="tab-button" data-tab="#checkin"  data-tap-toggle="false"><i class="fa  fa-icon-check fa-2x" aria-hidden="true"></i> <span class="languagespecificHTML" data-text="checkin">Checkin</span></li>';
	if(!empty($menu['Classes']))$menuOutput.='<li id="classes-tab-button" class="tab-button" data-tab="#classes"  data-tap-toggle="false"><i class="fa fa-lightbulb fa-2x" aria-hidden="true"></i> <span class="languagespecificHTML" data-text="classes">Classes</span></li>';
	if(!empty($menu['Giving']))$menuOutput.='<li id="giving-tab-button" class="tab-button" data-tab="#giving" data-tap-toggle="false"><i class="fa fa-credit-card fa-2x" aria-hidden="true"></i> <span class="languagespecificHTML" data-text="giving">Giving</span></li>';
	if(!empty($menu['Groups']))$menuOutput.='<li id="group-tab-button" class="tab-button" data-tab="#smallgroup" data-tap-toggle="false"><i class="fa fa-user fa-2x" aria-hidden="true"></i> <span class="languagespecificHTML" data-text="groups">Groups</span></li>';
	if(!empty($menu['Media']))$menuOutput.='<li id="media-tab-button" class="tab-button" data-tab="#media" data-tap-toggle="false"><i class="fa fa-headphones fa-2x" aria-hidden="true"></i> <span class="languagespecificHTML" data-text="media">Media</span></li>';
	if(!empty($menu['News']))$menuOutput.='<li id="news-tab-button" class="tab-button" data-tab="#news"  data-tap-toggle="false"><i class="fa fa-newspaper-o fa-2x" aria-hidden="true"></i> <span class="languagespecificHTML" data-text="news">News</span> <span id="news-badge"></span></li>';
	if(!empty($menu['Prayer']))$menuOutput.='<li id="prayer-tab-button" class="tab-button" data-tab="#prayer"  data-tap-toggle="false"><i class="fa fa-child  fa-2x" aria-hidden="true"></i> <span class="languagespecificHTML" data-text="prayer">Prayer</span> <span id="prayer-badge"></span></li>';
	if(!empty($menu['My prayer list']))$menuOutput.='<li id="my-prayer-tab-button" class="tab-button" data-tab="#myprayer"  data-tap-toggle="false"><i class="fa fa-child  fa-2x" aria-hidden="true"></i> <span class="languagespecificHTML" data-text="my-prayer-list">My Prayer List</span> <span id="prayer-badge"></span></li>';
	if(!empty($menu['Rotas']))$menuOutput.='<li id="rota-tab-button" class="tab-button" data-tab="#rota" data-tap-toggle="false"><i class="fa fa-file-text-o fa-2x" aria-hidden="true"></i> <span class="languagespecificHTML" data-text="rotas">Rotas</span></li>';
	$menuOutput.='<li id="settings-tab-button" class="tab-button" data-tab="#settings" data-tap-toggle="false"><i class="fa fa-cog fa-2x" aria-hidden="true"></i> <span class="languagespecificHTML" data-text="settings">Settings</span></li>';
	$menuOutput.='<li id="logout-tab-button" class="tab-button" data-tab="#logout"  data-tap-toggle="false"><i class="fa fa-sign-out fa-2x" aria-hidden="true"></i></i> <span class="languagespecificHTML" data-text="logout">Logout</span></li>';

	$output=array('menu_title'=>$menu_title,'home'=>$home,'giving'=>$giving,'groups'=>$groups,'logo'=>$logo,'church_id'=>$church_id,'menu'=>$menuOutput,'style'=>$style);
	header('Access-Control-Max-Age: 1728000');
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: *');
	header('Access-Control-Allow-Headers: Content-MD5, X-Alt-Referer');
	header('Access-Control-Allow-Credentials: true');
	echo json_encode($output);
	die();

}
add_action("wp_ajax_ca_home", "ca_home");
add_action("wp_ajax_nopriv_ca_home", "ca_home");




function ca_account()
{
	global $wpdb;
	if(!empty($_GET['token']))church_admin_app_last_visited(__('Account','church-admin'),$_GET['token']);
	$output=array();
	//check token first
	if(empty($_GET['token']))
	{
		$output=array('error'=>'login required');

	}
	else
	{
		$people=$wpdb->get_row('SELECT CONCAT_WS(" ",a.first_name,a.last_name) AS name, a.people_id,a.household_id FROM '.CA_PEO_TBL.' a, '.CA_APP_TBL.' b WHERE a.user_id=b.user_id AND b.UUID="'.esc_sql(stripslashes($_GET['token'])).'"');

		if(empty($people->people_id))
		{
			$output=array('error'=>"Your user identity is not connected to a church user profile.");

		}
		else
		{
			$peeps=array();
			$peeps[]=array('name'=>esc_html($people->name),'people_id'=>intval($people->people_id));
			$others=$wpdb->get_results('SELECT CONCAT_WS(" ",first_name,last_name) AS name,people_id FROM '.CA_PEO_TBL.' WHERE household_id="'.intval($people->household_id).'" AND people_id!="'.intval($people->people_id).'" ORDER BY people_order ASC');
			if(!empty($others))
			{
				foreach($others AS $other)$peeps[]=array('name'=>esc_html($other->name),'people_id'=>intval($other->people_id));
			}

			$address=$wpdb->get_row('SELECT phone, address,lat,lng FROM '.CA_HOU_TBL.' WHERE household_id="'.intval($people->household_id).'"');
			if(!empty($address))
			{
				$add=array('address'=>esc_html($address->address),'lat'=>$address->lat,'lng'=>$address->lng,'phone'=>$address->phone,'household_id'=>$people->household_id);
			}else{$add=array();}

			$output=array('people'=>$peeps,'address'=>$add);
		}
	}

	header('Access-Control-Max-Age: 1728000');
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: *');
	header('Access-Control-Allow-Headers: Content-MD5, X-Alt-Referer');
	header('Access-Control-Allow-Credentials: true');
	echo json_encode($output);
	die();
}

add_action("wp_ajax_ca_account", "ca_account");
add_action("wp_ajax_nopriv_ca_account", "ca_account");



function ca_people_edit()
{

	global $wpdb;
	if(empty($_GET['token']))
	{
		$output=array('error'=>'login required');

	}
	else
	{
		if($_GET['people_id']==0){$output=array('first_name'=>'','last_name'=>'','mobile'=>'','email'=>'');}
		else $output=$wpdb->get_row('SELECT * FROM '.CA_PEO_TBL.' WHERE people_id="'.intval($_GET['people_id']).'"', ARRAY_A);
	}
	header('Access-Control-Max-Age: 1728000');
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: *');
	header('Access-Control-Allow-Headers: Content-MD5, X-Alt-Referer');
	header('Access-Control-Allow-Credentials: true');
	echo json_encode($output);
	die();
}
add_action("wp_ajax_ca_people_edit", "ca_people_edit");
add_action("wp_ajax_nopriv_ca_people_edit", "ca_people_edit");

function ca_address_edit()
{

	global $wpdb;
	if(empty($_GET['token']))
	{
		$output=array('error'=>'login required');

	}
	else
	{
		$household_id=$wpdb->get_var('SELECT a.household_id FROM '.CA_PEO_TBL.' a, '.CA_APP_TBL.' b WHERE a.user_id=b.user_id and b.UUID="'.esc_sql(stripslashes($_GET['token'])).'"');
		if($household_id==intval($_GET['household_id']))
		{
			$output=$wpdb->get_row('SELECT address,phone FROM '.CA_HOU_TBL.' WHERE household_id="'.intval($_GET['household_id']).'"', ARRAY_A);
		}
		else
		{
			$output=array('error'=>'login required');
		}
	}
	header('Access-Control-Max-Age: 1728000');
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: *');
	header('Access-Control-Allow-Headers: Content-MD5, X-Alt-Referer');
	header('Access-Control-Allow-Credentials: true');
	echo json_encode($output);
	die();
}
add_action("wp_ajax_ca_address_edit", "ca_address_edit");
add_action("wp_ajax_nopriv_ca_address_edit", "ca_address_edit");

function ca_save_address_edit()
{
	global $wpdb;
	if(empty($_GET['token']))
	{
		$output=array('error'=>'login required');

	}
	else
	{
		$household_id=$wpdb->get_var('SELECT a.household_id FROM '.CA_PEO_TBL.' a, '.CA_APP_TBL.' b WHERE a.user_id=b.user_id and b.UUID="'.esc_sql(stripslashes($_GET['token'])).'"');

		if($household_id==intval($_GET['household_id']))
		{
			$data=array('address'=>stripslashes($_GET['address']),'phone'=>stripslashes($_GET['phone']));
			$wpdb->update(CA_HOU_TBL,$data,array('household_id'=>intval($_GET['household_id'])));
			$output=array('error'=>'success');
		}
		else{
			$output=array('error'=>'login required');

		}


	}
	header('Access-Control-Max-Age: 1728000');
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: *');
	header('Access-Control-Allow-Headers: Content-MD5, X-Alt-Referer');
	header('Access-Control-Allow-Credentials: true');
	echo json_encode($output);
	die();
}
add_action("wp_ajax_ca_save_address_edit", "ca_save_address_edit");
add_action("wp_ajax_nopriv_ca_save_address_edit", "ca_save_address_edit");

function ca_save_people_edit()
{
	global $wpdb;
	if(empty($_GET['token']))
	{
		$output=array('error'=>'login required');

	}
	else
	{
		$household_id=$wpdb->get_var('SELECT a.household_id FROM '.CA_PEO_TBL.' a, '.CA_APP_TBL.' b WHERE a.user_id=b.user_id and b.UUID="'.esc_sql(stripslashes($_GET['token'])).'"');

		$data=array('first_name'=>stripslashes($_GET['first_name']),'last_name'=>stripslashes($_GET['last_name']),'mobile'=>stripslashes($_GET['mobile']),'email'=>stripslashes($_GET['email']));

		if($household_id && $_GET['people_id']==0)
		{//new person
			$data['household_id']=intval($household_id);
			$wpdb->insert(CA_PEO_TBL,$data);

		}
		elseif($household_id)
		{
			$check=$wpdb->get_var('SELECT people_id FROM '.CA_PEO_TBL.' WHERE people_id="'.intval($_GET['people_id']).'" AND household_id="'.intval($household_id).'"');
			if($check)
			{

				$wpdb->update(CA_PEO_TBL,$data,array('people_id'=>intval($_GET['people_id'])));

			}
		}
		$output=array('error'=>'success');
	}
	header('Access-Control-Max-Age: 1728000');
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: *');
	header('Access-Control-Allow-Headers: Content-MD5, X-Alt-Referer');
	header('Access-Control-Allow-Credentials: true');
	echo json_encode($output);
	die();
}
add_action("wp_ajax_ca_save_people_edit", "ca_save_people_edit");
add_action("wp_ajax_nopriv_ca_save_people_edit", "ca_save_people_edit");

function ca_delete_people()
{
	global $wpdb;
	if(empty($_GET['token']))
	{
		$output=array('error'=>'login required');

	}
	else
	{
		$household_id=$wpdb->get_var('SELECT a.household_id FROM '.CA_PEO_TBL.' a, '.CA_APP_TBL.' b WHERE a.user_id=b.user_id and b.UUID="'.esc_sql(stripslashes($_GET['token'])).'"');

		$check=$wpdb->get_var('SELECT people_id FROM '.CA_PEO_TBL.' WHERE people_id="'.intval($_GET['people_id']).'" AND household_id="'.intval($household_id).'"');
		if($check)
		{
			$wpdb->query('DELETE FROM '.CA_PEO_TBL.' WHERE people_id="'.intval($_GET['people_id']).'"');
			$output=array('error'=>'success');
		}else{$output=array('error'=>'no one found to delete');}
	}
	header('Access-Control-Max-Age: 1728000');
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: *');
	header('Access-Control-Allow-Headers: Content-MD5, X-Alt-Referer');
	header('Access-Control-Allow-Credentials: true');
	echo json_encode($output);
	die();
}
add_action("wp_ajax_ca_delete_people", "ca_delete_people");
add_action("wp_ajax_nopriv_ca_delete_people", "ca_delete_people");

function ca_send_prayer_request()
{
		global $wpdb;
		if(!empty($_GET['token']))
		{
			$sql='SELECT user_id FROM '.CA_APP_TBL.' WHERE UUID="'.esc_sql(stripslashes($_GET['token'])).'"';
			$user_id=$wpdb->get_var($sql);
		}


		$title=stripslashes($_GET['prayer_title']);
		$content=stripslashes($_GET['content']);

		$args=array('post_content'=>sanitize_textarea_field($content),'post_title'=>wp_strip_all_tags($title),'post_status'=>'draft','post_type'=>'prayer-requests');
		if(user_can( $user_id, 'manage_options' ))$args['post_status']='publish';
		if(!empty($user_id))$args['post-author']=$user_id;
		$post_id = wp_insert_post($args);

		if(!is_wp_error($post_id)){
  			//the post is valid

  			if(!user_can( $user_id, 'manage_options' ))wp_mail(get_option('admin_email'),__('Prayer Request Draft','church-admin'),__('A draft prayer request has been posted. Please moderate','church-admin'));
		}else{
  			//there was an error in the post insertion,

		}
		header('Access-Control-Max-Age: 1728000');
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: *');
		header('Access-Control-Allow-Headers: Content-MD5, X-Alt-Referer');
		header('Access-Control-Allow-Credentials: true');
		echo json_encode(array('done'));
		die();
}
add_action("wp_ajax_ca_send_prayer_request", "ca_send_prayer_request");
add_action("wp_ajax_nopriv_ca_send_prayer_request", "ca_send_prayer_request");

function ca_classes()
{
	global $wpdb;

	$output=array();
	$sql='SELECT * FROM '.CA_CLA_TBL.' WHERE next_start_date >= CURDATE() ORDER BY next_start_date,start_time';

	$classes=$wpdb->get_results($sql);

	if(empty($classes)){$output['error']='No classes yet';}
	else
	{
		$students=array();
		foreach($classes AS $class)
		{
			//get date
			$allDates=array();

			$datesResults=$wpdb->get_results('SELECT start_date FROM '.CA_DATE_TBL.' WHERE event_id="'.intval($class->event_id).'" ORDER BY start_date ASC');
			if(!empty($datesResults))
			{
				foreach($datesResults As $datesRow)
				{
					$allDates[]=mysql2date(get_option('date_format'),$datesRow->start_date);
				}
			}

			//add checkin for leaders
			if(!empty($_GET['token']))
			{
				//logged in
				$sql='SELECT a.people_id,a.user_id,a.household_id FROM '.CA_PEO_TBL.' a, '.CA_APP_TBL.' b WHERE a.user_id=b.user_id and b.UUID="'.esc_sql(stripslashes($_GET['token'])).'"';
				if(defined('CA_DEBUG'))church_admin_debug($sql);
				$people=$wpdb->get_row($sql);
				$user_id=$wpdb->get_var('SELECT user_id FROM '.CA_APP_TBL.' WHERE UUID="'.esc_sql(stripslashes($_GET['token'])).'"');

				if(!empty($people)||!empty($user_id))
				{

					//user in directory

					if(in_array($people->people_id,maybe_unserialize($class->leadership))|| user_can( $people->user_id, 'manage_options' )||user_can($user_id,'manage_options'))
					{
						if(defined('CA_DEBUG'))church_admin_debug('Leader/Admin');
						//user is leader so give array of students
							$students=array();
							$people_result=church_admin_people_meta($class->class_id,NULL,'class');
							if(!empty($people_result))
							{//people are booked in for class, so can check them in
								foreach($people_result AS $data)
								{
									$name=implode(" ",array_filter(array($data->first_name,$data->prefix,$data->last_name)));
									$students[]=array('people_id'=>intval($data->people_id),'name'=>esc_html($name));

								}
							}
							$bookin=FALSE;
							$family=FALSE;

					}
					else {
						$sql='SELECT people_id FROM '.CA_MET_TBL.' WHERE people_id="'.intval($people->people_id).'" AND meta_type="class" AND ID="'.intval($class->class_id).'"';

						$check=$wpdb->get_var($sql);
						// opportunity to book in
						if(!$check)$bookin=TRUE;

						$family=array();
						$sql='SELECT CONCAT_WS(" ",first_name,last_name) AS name,people_id FROM '.CA_PEO_TBL.' WHERE household_id="'.intval($people->household_id).'"  ORDER BY people_order ASC';
						if(defined('CA_DEBUG'))church_admin_debug($sql);
						$people=$wpdb->get_results($sql);
						if(!empty($people))
						{
							foreach($people AS $person) $family[]=array('name'=>esc_html($person->name),'people_id'=>intval($person->people_id));
						}
					}
				}
			}

			$output[]=array(	'class_id'       =>	intval($class->class_id),
												'date'			=>  mysql2date(get_option('date_format'),$class->next_start_date),
												'sqldate'		=>  esc_html($class->next_start_date),
												'name'			=>	esc_html($class->name),
												'description'	=>	esc_html($class->description),
												'dates'			=>	mysql2date(get_option('date_format'),$class->next_start_date).' - '.mysql2date(get_option('date_format'),$class->end_date),
												'times'			=>	mysql2date(get_option('time_format'),$class->start_time).' - '.mysql2date(get_option('time_format'),$class->end_time),
												'students'		=> 	$students,
												'bookin' =>$bookin,
												'people'=>$family,
												'all_dates'		=> $allDates
											);

		}
	if(defined('CA_DEBUG'))	church_admin_debug(print_r($output,TRUE));
	}

	header('Access-Control-Max-Age: 1728000');
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: *');
	header('Access-Control-Allow-Headers: Content-MD5, X-Alt-Referer');
	header('Access-Control-Allow-Credentials: true');
	echo json_encode($output);
	die();
}
add_action("wp_ajax_ca_classes", "ca_classes");
add_action("wp_ajax_nopriv_ca_classes", "ca_classes");


function ca_class_checkin()
{
	global $wpdb;

	$class=$wpdb->get_row('SELECT * FROM '.CA_CLA_TBL.' WHERE class_id="'.intval($_GET['class_id']).'"');

	if(empty($_GET['token']))
	{
		$output=array('error'=>'login required');
	}
	else
	{


		$people=$wpdb->get_row('SELECT a.people_id,a.user_id FROM '.CA_PEO_TBL.' a, '.CA_APP_TBL.' b WHERE a.user_id=b.user_id and b.UUID="'.esc_sql(stripslashes($_GET['token'])).'"');
		$user_id=$wpdb->get_var('SELECT user_id FROM '.CA_APP_TBL.' WHERE UUID="'.esc_sql(stripslashes($_GET['token'])).'"');
		if(!empty($people)||!empty($user_id))
		{
			//user in directory or an admin
			if(in_array($people->people_id,maybe_unserialize($class->leadership))|| user_can( $people->user_id, 'manage_options' )||user_can($user_id,'manage_options'))
			{

				$class_id=intval($_GET['class_id']);
				$adults=$child=0;
				$date=new DateTime($_GET['date']);
				if(defined('CA_DEBUG'))church_admin_debug(print_r($date,TRUE));

				foreach($_GET['people_id'] AS $key=>$people_id)
				{
					$check=$wpdb->get_var('SELECT attendance_id FROM '.CA_IND_TBL.' WHERE date="'.esc_sql($date->format('Y-m-d')).'" AND people_id="'.intval($people_id).'" AND meeting_type="class" AND meeting_id="'.intval($_GET['class_id']).'"');
					if(empty($check))
					{
						$sql=	'INSERT INTO '.CA_IND_TBL.' (`date`,people_id,meeting_type,meeting_id) VALUES ("'.esc_sql($date->format('Y-m-d')).'","'.intval($people_id).'","class","'.intval($_GET['class_id']).'")';
						$wpdb->query($sql);
						if(defined('CA_DEBUG'))church_admin_debug($sql);
						//check people type
						$sql='SELECT people_type_id FROM '.CA_PEO_TBL.' WHERE people_id="'.intval($people_id).'"';
						$person_type=$wpdb->get_var($sql);
						switch($person_type)
						{
							case 1:$adult++;break;
							case 2:$child++;break;
							case 3:$child++;break;
						}
					}
				}
				if(!empty($adult)||!empty($child))
				{
					$sql='INSERT INTO '.CA_ATT_TBL .' (`date`,adults,children,service_id,mtg_type) VALUES ("'.esc_sql($_GET['date']).'","'.$adult.'","'.$child.'","'.intval($_GET['class_id']).'","class")';
						$wpdb->query($sql);
						church_admin_refresh_rolling_average();
				}
				if(defined('CA_DEBUG'))church_admin_debug(print_r($date,TRUE));
				$name=$wpdb->get_var('SELECT name FROM '.CA_CLA_TBL .' WHERE class_id="'.intval($_GET['class_id']).'"');
				$output=array('success'=>"true",'class_name'=>esc_html($name),'date'=>mysql2date(get_option('date_format'),$date->format('Y-m-d')));
			}
		}
		else{$output=array('error'=>'login required');}

	}
	header('Access-Control-Max-Age: 1728000');
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: *');
	header('Access-Control-Allow-Headers: Content-MD5, X-Alt-Referer');
	header('Access-Control-Allow-Credentials: true');
	echo json_encode($output);
	die();
}

add_action("wp_ajax_ca_class_checkin", "ca_class_checkin");
add_action("wp_ajax_nopriv_ca_class_checkin", "ca_class_checkin");



/**
 *
 * Returns array of events to checkin to today
 *
 * @author  Andy Moyle
 * @param    null
 * @return
 * @version  0.1
 *
 */
function ca_now()
{
	global $wpdb;
	if(defined('CA_DEBUG'))church_admin_debug('ca_now function');
	if(empty($_GET['token']))
	{
		$output=array('error'=>'login required');
	}
	else
	{
		$people=$wpdb->get_row('SELECT * FROM '.CA_PEO_TBL.' a, '.CA_APP_TBL.' b WHERE a.user_id=b.user_id and b.UUID="'.esc_sql(stripslashes($_GET['token'])).'"');
		$events=array();
		$day=idate('w');
		$household=array();
		$family=$wpdb->get_results('SELECT people_id,CONCAT_WS(" ",first_name,last_name) AS name FROM '.CA_PEO_TBL.' WHERE household_id="'.intval($people->household_id).'"');
		if(!empty($family))
		{
			foreach($family as $person)
			{
				$household[]=array('people_id'=>intval($person->people_id),'name'=>esc_html($person->name));
			}
		}
		//look for service now
		$services=$wpdb->get_results('SELECT * FROM '.CA_SER_TBL.' WHERE service_day="'.esc_sql($day).'"');
		if(!empty($services))
		{
			foreach($services AS $service)
			{
				$events[]=array('type'=>'service','id'=>esc_html($service->service_id),'name'=>esc_html($service->service_name));
			}
		}
		//look for right small group
		$sql='SELECT a.group_name,a.ID FROM '.CA_SMG_TBL.' a, '.CA_MET_TBL .' b WHERE a.ID=b.ID AND b.people_id="'.intval($people->people_id).'" AND b.meta_type="smallgroup" AND a.group_day="'.intval($day).'"';
		if(defined('CA_DEBUG'))church_admin_debug($sql);
		$group=$wpdb->get_row($sql);
		if(!empty($group))$events[]=array('type'=>'smallgroup','id'=>esc_html($group->ID),'name'=>esc_html($group->group_name));
		//look for Classes
		$sql='SELECT a.name,a.class_id FROM '.CA_CLA_TBL.' a, '.CA_MET_TBL .' b WHERE a.class_id=b.ID AND b.people_id="'.intval($people->people_id).'" AND b.meta_type="class" AND a.next_start_date="'.date('Y-m-d').'"';
		if(defined('CA_DEBUG'))church_admin_debug($sql);
		$classes=$wpdb->get_results($sql);
		if(!empty($classes))
		{
			foreach($classes AS $class)
			{
				$events[]=array('type'=>'class','id'=>esc_html($class->class_id),'name'=>esc_html($class->name));
			}
		}

		if(empty($events))$output=array('error'=>__('There is nothing to check in to today','church-admin'));
		else $output=array('events'=>$events,'people'=>$household,'date'=>date('Y-m-d'));

	}
	header('Access-Control-Max-Age: 1728000');
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: *');
	header('Access-Control-Allow-Headers: Content-MD5, X-Alt-Referer');
	header('Access-Control-Allow-Credentials: true');
	echo json_encode($output);
	die();

}
add_action("wp_ajax_ca_now", "ca_now");
add_action("wp_ajax_nopriv_ca_now", "ca_now");


function ca_checkin_sent()
{
		if(defined('CA_DEBUG'))church_admin_debug('ca_checkin_sent function');
		if(defined('CA_DEBUG'))church_admin_debug('$_GET array'."\r\n".print_r($_GET,TRUE));
		global $wpdb;
		if(empty($_GET['token']))
		{

			$output=array('error'=>'login required');

		}
		else
		{
				$id=intval($_GET['class_id']);
				$date=$_GET['date'];

				$what='';
				switch($_GET['what'])
				{
						case 'Service' 	: 	$what='service';		break;
						case 'Group' 		: 	$what='smallgroup';	break;
						case 'Class'		: 	$what='class';			break;
				}
		}
		if(defined('CA_DEBUG'))church_admin_debug("What: $what");
		$people_id=array();
		$people_ids=$_GET['people_id'];
		$loggedin_people_id=$wpdb->get_var('SELECT a.people_id FROM '.CA_PEO_TBL.' a, '.CA_APP_TBL.' b WHERE a.user_id=b.user_id and b.UUID="'.esc_sql(stripslashes($_GET['token'])).'"');
		if(defined('CA_DEBUG'))church_admin_debug("Logged in people id: $loggedin_people_id");
		if(!empty($id) && !empty($what) &&!empty($people_ids) &&!empty($loggedin_people_id)&&in_array($loggedin_people_id,$people_ids))
		{
			if(defined('CA_DEBUG'))church_admin_debug('Checks passed');
			foreach($people_ids AS $key=>$peep)
			{
				//individual attendance
				$people_type_id=$wpdb->get_var('SELECT people_type_id FROM '.CA_PEO_TBL.' WHERE people_id="'.intval($peep).'"');
				if(!empty($people_type_id))
				{

					switch($people_type_id)
					{
						case 1:$which='adults=adults+1';$v='"1","0"';break;
						case 2:$which='children=children+1';$v='"0","1"';break;
						default:$which='adults=adults+1';$v='"1","0"';break;
					}

					$check=$wpdb->get_var('SELECT attendance_id FROM '.CA_IND_TBL.' WHERE people_id="'.intval($peep).'" AND meeting_type="'.esc_sql($what).'" AND meeting_id="'.intval($id).'" AND `date`="'.esc_sql($date).'"');
					if(empty($check))
					{
							$sql='INSERT '.CA_IND_TBL.' (people_id,meeting_type,meeting_id,`date`) VALUES("'.intval($peep).'","'.esc_sql($what).'","'.intval($id).'","'.esc_sql($date).'")';
							if(defined('CA_DEBUG'))church_admin_debug($sql);
							$wpdb->query($sql);
							//main attendance
							$sql='SELECT attendance_id FROM '.CA_ATT_TBL.' WHERE mtg_type="'.esc_sql($what).'" AND service_id="'.intval($id).'" AND `date`="'.esc_sql($date).'"';
							if(defined('CA_DEBUG'))church_admin_debug($sql);
							$check=$wpdb->get_var($sql);
							if(!empty($check))
							{
								$sql='UPDATE '.CA_ATT_TBL.' SET '.$which.' WHERE mtg_type="'.esc_sql($what).'" AND service_id="'.intval($id).'" AND `date`="'.esc_sql($date).'"';
								if(defined('CA_DEBUG'))church_admin_debug($sql);
								$wpdb->query($sql);
							}
							else {
								$sql='INSERT INTO '.CA_ATT_TBL.' (adults,children,mtg_type,service_id,`date`) VALUES ('.$v.',"'.esc_sql($what).'","'.intval($id).'","'.esc_sql($date).'")';
								if(defined('CA_DEBUG'))church_admin_debug($sql);
								$wpdb->query($sql);
							}

					}
				}
			}
			$output=array('success'=>'Success');
		}
		else {
			$output=array('error'=>"Empty");
		}
		if(defined('CA_DEBUG'))church_admin_debug(print_r($output,TRUE));
		header('Access-Control-Max-Age: 1728000');
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: *');
		header('Access-Control-Allow-Headers: Content-MD5, X-Alt-Referer');
		header('Access-Control-Allow-Credentials: true');
		echo json_encode($output);
		die();

}
add_action("wp_ajax_ca_checkin_send", "ca_checkin_sent");
add_action("wp_ajax_nopriv_ca_checkin_send", "ca_checkin_sent");
