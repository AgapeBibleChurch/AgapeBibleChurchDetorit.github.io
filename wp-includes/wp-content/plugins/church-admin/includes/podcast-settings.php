<?php

function ca_podcast_settings()
{
/**
 *
 * Podcast Settings
 * 
 * @author  Andy Moyle
 * @param    null
 * @return   
 * @version  0.1
 * 
 */ 
    global $wpdb,$ca_podcast_settings;
    $settings=get_option('ca_podcast_settings');
$language_codes = array(
		'en-GB' => 'English UK' ,
                'en_US' => 'English US' ,
		'aa' => 'Afar' , 
		'ab' => 'Abkhazian' , 
		'af' => 'Afrikaans' , 
		'am' => 'Amharic' , 
		'ar' => 'Arabic' , 
		'as' => 'Assamese' , 
		'ay' => 'Aymara' , 
		'az' => 'Azerbaijani' , 
		'ba' => 'Bashkir' , 
		'be' => 'Byelorussian' , 
		'bg' => 'Bulgarian' , 
		'bh' => 'Bihari' , 
		'bi' => 'Bislama' , 
		'bn' => 'Bengali/Bangla' , 
		'bo' => 'Tibetan' , 
		'br' => 'Breton' , 
		'ca' => 'Catalan' , 
		'co' => 'Corsican' , 
		'cs' => 'Czech' , 
		'cy' => 'Welsh' , 
		'da' => 'Danish' , 
		'de' => 'German' , 
		'dz' => 'Bhutani' , 
		'el' => 'Greek' , 
		'eo' => 'Esperanto' , 
		'es' => 'Spanish' , 
		'et' => 'Estonian' , 
		'eu' => 'Basque' , 
		'fa' => 'Persian' , 
		'fi' => 'Finnish' , 
		'fj' => 'Fiji' , 
		'fo' => 'Faeroese' , 
		'fr' => 'French' , 
		'fy' => 'Frisian' , 
		'ga' => 'Irish' , 
		'gd' => 'Scots/Gaelic' , 
		'gl' => 'Galician' , 
		'gn' => 'Guarani' , 
		'gu' => 'Gujarati' , 
		'ha' => 'Hausa' , 
		'hi' => 'Hindi' , 
		'hr' => 'Croatian' , 
		'hu' => 'Hungarian' , 
		'hy' => 'Armenian' , 
		'ia' => 'Interlingua' , 
		'ie' => 'Interlingue' , 
		'ik' => 'Inupiak' , 
		'in' => 'Indonesian' , 
		'is' => 'Icelandic' , 
		'it' => 'Italian' , 
		'iw' => 'Hebrew' , 
		'ja' => 'Japanese' , 
		'ji' => 'Yiddish' , 
		'jw' => 'Javanese' , 
		'ka' => 'Georgian' , 
		'kk' => 'Kazakh' , 
		'kl' => 'Greenlandic' , 
		'km' => 'Cambodian' , 
		'kn' => 'Kannada' , 
		'ko' => 'Korean' , 
		'ks' => 'Kashmiri' , 
		'ku' => 'Kurdish' , 
		'ky' => 'Kirghiz' , 
		'la' => 'Latin' , 
		'ln' => 'Lingala' , 
		'lo' => 'Laothian' , 
		'lt' => 'Lithuanian' , 
		'lv' => 'Latvian/Lettish' , 
		'mg' => 'Malagasy' , 
		'mi' => 'Maori' , 
		'mk' => 'Macedonian' , 
		'ml' => 'Malayalam' , 
		'mn' => 'Mongolian' , 
		'mo' => 'Moldavian' , 
		'mr' => 'Marathi' , 
		'ms' => 'Malay' , 
		'mt' => 'Maltese' , 
		'my' => 'Burmese' , 
		'na' => 'Nauru' , 
		'ne' => 'Nepali' , 
		'nl' => 'Dutch' , 
		'no' => 'Norwegian' , 
		'oc' => 'Occitan' , 
		'om' => '(Afan)/Oromoor/Oriya' , 
		'pa' => 'Punjabi' , 
		'pl' => 'Polish' , 
		'ps' => 'Pashto/Pushto' , 
		'pt' => 'Portuguese' , 
		'qu' => 'Quechua' , 
		'rm' => 'Rhaeto-Romance' , 
		'rn' => 'Kirundi' , 
		'ro' => 'Romanian' , 
		'ru' => 'Russian' , 
		'rw' => 'Kinyarwanda' , 
		'sa' => 'Sanskrit' , 
		'sd' => 'Sindhi' , 
		'sg' => 'Sangro' , 
		'sh' => 'Serbo-Croatian' , 
		'si' => 'Singhalese' , 
		'sk' => 'Slovak' , 
		'sl' => 'Slovenian' , 
		'sm' => 'Samoan' , 
		'sn' => 'Shona' , 
		'so' => 'Somali' , 
		'sq' => 'Albanian' , 
		'sr' => 'Serbian' , 
		'ss' => 'Siswati' , 
		'st' => 'Sesotho' , 
		'su' => 'Sundanese' , 
		'sv' => 'Swedish' , 
		'sw' => 'Swahili' , 
		'ta' => 'Tamil' , 
		'te' => 'Tegulu' , 
		'tg' => 'Tajik' , 
		'th' => 'Thai' , 
		'ti' => 'Tigrinya' , 
		'tk' => 'Turkmen' , 
		'tl' => 'Tagalog' , 
		'tn' => 'Setswana' , 
		'to' => 'Tonga' , 
		'tr' => 'Turkish' , 
		'ts' => 'Tsonga' , 
		'tt' => 'Tatar' , 
		'tw' => 'Twi' , 
		'uk' => 'Ukrainian' , 
		'ur' => 'Urdu' , 
		'uz' => 'Uzbek' , 
		'vi' => 'Vietnamese' , 
		'vo' => 'Volapuk' , 
		'wo' => 'Wolof' , 
		'xh' => 'Xhosa' , 
		'yo' => 'Yoruba' , 
		'zh' => 'Chinese' , 
		'zu' => 'Zulu' , 
		);
    $cats = array( 'Religion &amp; Spirituality -Christianity',
                  'Arts - Design',
            'Arts - Fashion &amp; Beauty',
            'Arts - Food',
            'Arts - Literature',
            'Arts - Performing Arts',
            'Arts - Visual Arts',  
            'Business - Business News',
            'Business - Careers',
            'Business - Investing',
            'Business - Management &amp; Marketing',
            'Business - Shopping',
            'Comedy',
            'Education - Education Technology',
            'Education - Higher Education',
            'Education - K-12',
            'Education - Language Courses',
            'Education - Training',
            'Games &amp; Hobbies - Automotive',
            'Games &amp; Hobbies - Aviation',
            'Games &amp; Hobbies - Hobbies',
            'Games &amp; Hobbies - Other Games',
            'Games &amp; Hobbies - Video Games',
            'Government &amp; Organizations - Local',
            'Government &amp; Organizations - National',
            'Government &amp; Organizations - Non-Profit',
            'Government &amp; Organizations - Regional',
            'Health - Alternative Health',
            'Health - Fitness &amp; Nutrition',
            'Health - Self-Help',
            'Health - Sexuality',
            'Kids &amp; Family',
            'Music',
            'News &amp; Politics',
            'Religion &amp; Spirituality -Buddhism',
            'Religion &amp; Spirituality -Christianity',
            'Religion &amp; Spirituality -Hinduism',
	    'Religion &amp; Spirituality -Islam',
            'Religion &amp; Spirituality -Judaism',
            'Religion &amp; Spirituality -Other',
            'Religion &amp; Spirituality -Spirituality',
            'Science &amp; Medicine - Medicine',
            'Science &amp; Medicine -Natural Sciences',
            'Science &amp; Medicine -Social Sciences',
            'Society &amp; Culture - History',
            'Society &amp; Culture - Personal Journals',
            'Society &amp; Culture - Philosophy',
            'Society &amp; Culture - Places &amp; Travel',
            'Sports &amp; Recreation - Amateur',
            'Sports &amp; Recreation - College &amp; High School',
            'Sports &amp; Recreation - Outdoor',
            'Sports &amp; Recreation - Professional',
            'Technology - Gadgets',
            'Technology - Tech News',
            'Technology - Podcasting',
            'Technology - Software How-To',
            'TV &amp; Film');
            

    if(current_user_can('manage_options'))
    {//current user can
        if(!empty($_POST['save_settings']))
        {//process
            //handle image
			
            if(!empty($_FILES['image']['tmp_name']))
            {
                
				$filetmp = $_FILES['image']['tmp_name'];
				//clean filename and extract extension
				$filename = $_FILES['image']['name'];
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
	    		$image = $upload_dir['path'].'/'  . $filename;
	    
				move_uploaded_file($filetmp, $image);
				$image_url=$upload_dir['url'].'/' .$filename;
            }
            else
            {//no upload, so no change
                $image=$settings['image'];//no change    
            }//no upload, so no change
            //end handle image
            
            
            $xml=array();
            foreach($_POST AS $key=>$value)$xml[$key]=xmlentities(stripslashes($value));
            switch($xml['explicit'])
            {
                case 'clean':$xml['explicit']='clean';break;
                case 'no':$xml['explicit']='no';break;
                case 'yes':$xml['explicit']='yes';break;
                default:$xml['explicit']='no';
            }
            //only allow valid category
            if(in_array($_POST['category'],$cats)){$xml['category']=xmlentities(stripslashes($_POST['category']));}else{$xml['category']='Religion &amp; Spirituality -Christianity';}
            if(!array_key_exists($xml['language'],$language_codes))$xml['language']='en';
            $new_settings=array('itunes_link'=>$xml['itunes_link'],
                'title'=>$xml['title'],  
            'copyright'=>$xml['copyright'],
            'link'=>CA_POD_URL.'podcast.xml',
            'subtitle'=>$xml['subtitle'],
            'author'=>$xml['author'],
            'summary'=>$xml['summary'],
            'description'=>$xml['description'],
            'owner_name'=>$xml['owner_name'],
            'owner_email'=>$xml['owner_email'],
            'image'=>$image_url,
			'image_path'=>$image,
            'category'=>$xml['category'],
            'language'=>$xml['language'],
            'explicit'=>$xml['explicit']
            );
           
            update_option('ca_podcast_settings',$new_settings);
            
            echo'<div class="notice notice-success inline"><p><strong>Podcast Settings Updated<br/><a href="'.CA_POD_URL.'podcast.xml">Check xml feed</a></p></div>';
            require_once(plugin_dir_path(dirname(__FILE__)).'includes/sermon-podcast.php');
            ca_podcast_xml();
            
        }//end process
        else
        {//form
				
            echo'<h2>Podcast Settings for RSS file</h2>';
            echo'<form action="" enctype="multipart/form-data" method="post">';
			
			echo'<table class="form-table"><tr><th scope="row">Itunes Link</th><td><input id="title" type="text" class="regular-text" name="itunes_link" value="'.esc_html($settings['itunes_link']).'"/></td></tr>';
           
            echo'<tr><th scope="row">Podcast title (255 charas)</th><td><input id="title" type="text" class="regular-text" name="title" value="'.esc_html($settings['title']).'"/></td></tr>';
            echo'<tr><th scope="row">Copyright Message: &copy;</th><td><input id="copyright"  class="regular-text" type="text" name="copyright" value="'.esc_html($settings['copyright']).'"/></td></tr>';
            echo'<tr><th scope="row">Subtitle</th><td><textarea id="subtitle" cols=45 rows=4  name="subtitle" >'.esc_html($settings['subtitle']).'</textarea></td></tr>';
            echo'<tr><th scope="row">Author</th><td><input id="author" class="regular-text" type="text" name="author" value="'.esc_html($settings['author']).'"/></td></tr>';
            echo'<tr><th scope="row">Summary</th><td><textarea id="summary" cols=45 rows=4   name="summary">'.esc_html($settings['summary']).'</textarea></td></tr>';
            echo'<tr><th scope="row">Description</th><td><textarea cols=45 rows=4 id="description"  name="description">'.esc_html($settings['title']).'</textarea></td></tr>';
            echo'<tr><th scope="row">Explicit content</th><td><select name="explicit">';
            if(!empty($settings['explicit']))echo'<option value="'.$settings['explicit'].'" selected="selected">'.$settings['explicit'].'</option>';
            echo'<option value="clean">clean</option><option value="no">no</option><option value="yes">yes</option></select></td></tr>';
            
            echo'<tr><th scope="row">Owner Name</th><td><input  class="regular-text" id="owner_name" type="text" name="owner_name" value="'.esc_html($settings['owner_name']).'"/></td></tr>';
            echo'<tr><th scope="row">Owner Email</th><td><input class="regular-text" type="text" name="owner_email" value="'.esc_html($settings['owner_email']).'"/></td></tr>';
            echo'<tr><th scope="row">Language</th><td><select id="language" name="language">';
            $first=$option='';
            foreach($language_codes AS $key=>$value)
            {
                if($key==$settings['language']){$first='<option value="'.intval($key).'" selected="selected" >'.esc_html($value).'</option>';}else{ $option.='<option value="'.intval($key).'">'.esc_html($value).'</option>';}
            }
            echo $first.$option.'</select></td></tr>';
            echo'<tr><th scope="row">Itunes Category</th><td><select id="category" name="category">';
            $first=$option='';
            foreach($cats AS $key=>$value)
            {
                if($value==$settings['category']){$first='<option value="'.intval($value).'" selected="selected" >'.esc_html($value).'</option>';}else{ $option.='<option value="'.intval($value).'">'.esc_html($value).'</option>';}
            }
            echo $first.$option.'</select></td></tr>';
            echo'<tr><th scope="row">Image</th><td><input type="file" name="image"/>';
            if(!empty($settings['image']))echo'<br/><img src="'.esc_url($settings['image']).'">';
            echo'</td></tr>';
            echo '<tr><th scope="row"><input type="hidden" name="save_settings" value="yes"/><input type="submit" class="button-primary" value="Save Podcast XML settings"/></td></tr></table></form>';

            
            
            
        }//form        
        
        
        
    }//end current user can
    
    
}

  function xmlentities( $string ) {
        $not_in_list = "A-Z0-9a-z\s_-";
        return preg_replace_callback( "/[^{$not_in_list}]/" , 'get_xml_entity_at_index_0' , $string );
    }
    function get_xml_entity_at_index_0( $CHAR ) {
        if( !is_string( $CHAR[0] ) || ( strlen( $CHAR[0] ) > 1 ) ) {
            die( "function: 'get_xml_entity_at_index_0' requires data type: 'char' (single character). '{$CHAR[0]}' does not match this type." );
        }
        switch( $CHAR[0] ) {
            case "'":    case '"':    case '&':    case '<':    case '>':
                return htmlspecialchars( $CHAR[0], ENT_QUOTES );    break;
            default:
                return numeric_entity_4_char($CHAR[0]);                break;
        }       
    }
    function numeric_entity_4_char( $char ) {
        return "&#".str_pad(ord($char), 3, '0', STR_PAD_LEFT).";";
    }
    
?>