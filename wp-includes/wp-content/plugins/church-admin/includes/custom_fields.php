<?php

/**
 *
 * Custom fields
 *
 * @param
 *
 *
 * @author andy_moyle
 *
 */
function church_admin_list_custom_fields()
{

	$custom_fields=get_option('church_admin_custom_fields');

	$out='<p><a href="'.wp_nonce_url('admin.php?page=church_admin/index.php&amp;action=edit_custom_field&amp;tab=people','edit_custom_field').'">'.__('Add a custom field','church-admin').'</a></p>';

	if(!empty($custom_fields))
	{

		$thead='<tr><th>'.__('Edit','church-admin').'</th><th>'.__('Delete','church-admin').'</th><th>'.__('Custom field name','church-admin').'</th><th>'.__('Custom field type','church-admin').'</th></tr>';
		$out.='<table class="widefat striped"><thead>'.$thead.'</thead><tbody>';
		foreach($custom_fields AS $ID=>$field)
		{

			$edit='<a href="'.wp_nonce_url('admin.php?page=church_admin/index.php&amp;action=edit_custom_field&amp;tab=people&amp;id='.$ID,'edit_custom_field').'">'.__('Edit','church-admin').'</a>';
			$delete='<a href="'.wp_nonce_url('admin.php?page=church_admin/index.php&amp;action=delete_custom_field&amp;tab=people&amp;id='.$ID,'delete_custom_field').'">'.__('Delete','church-admin').'</a>';
			$out.='<tr><td>'.$edit.'</td><td>'.$delete.'</td><td>'.esc_html($field['name']).'</td><td>'.esc_html($field['type']).'</td></tr>';
		}
		$out.='</tbody><tfoot>'.$thead.'</tfoot></table>';
	}
	return $out;
}


/**
 * Edit Custom Field
 *
 * @param
 * @param
 *
 * @author andy_moyle
 *
 */
 function church_admin_edit_custom_field($ID)
 {
	 global $wpdb;
 	$custom_fields=array();
 	$custom_fields=get_option('church_admin_custom_fields');
 	if(!empty($custom_fields)&&!empty($ID))$data=$custom_fields[$ID];

 	$out='';

 	if(!empty($_POST['save_custom_field']))
 	{
 		$data=array('name'=>stripslashes($_POST['custom-field-name']),'type'=>stripslashes($_POST['custom-field-type']));
		switch($_POST['custom-field-type'])
		{
			case'boolean':case'text':$data['default']=stripslashes($_POST['custom-field-default']);break;
		}

 		if(!church_admin_in_array_r($data,$custom_fields))
 		{
 			if(!empty($ID)){$custom_fields[$ID]=$data;}else{$custom_fields[]=$data;end($custom_fields);$ID=key($custom_fields);}
 		}

 		update_option('church_admin_custom_fields',$custom_fields);

		if(!empty($_POST['custom-all']))
		{
			$people=$wpdb->get_results('SELECT people_id FROM '.CA_PEO_TBL);
			if(!empty($people))
			{
					foreach($people AS $peep)
					{
							$check=$wpdb->get_var('SELECT ID FROM '.CA_CUST_TBL.' WHERE people_id="'.intval($peep->people_id).'" AND custom_id="'.intval($ID).'" ');
							$sql='INSERT INTO '.CA_CUST_TBL.' (people_id,custom_id,data) VALUES("'.intval($peep->people_id).'","'.intval($ID).'","'.esc_sql(stripslashes($_POST['custom-field-default'])).'")';

							if(empty($check))$wpdb->query($sql);
					}
			}
		}
 		$out.='<div class="notice notice-success">'.__('Custom field saved','church-admin').'</div>';
 		$out.=church_admin_list_custom_fields();
  	}
 	else
 	{
 			$out='<h2>'.__('Edit custom field','church-admin').'</h2>';
 		$out.='<form action="" method="POST">';
 		$out.='<table class="form-table">';
 		$out.='<tr><th scope="row">'.__('Custom field name','church-admin').'</th><td><input type="text" name="custom-field-name" ';
 		if(!empty($data['name']))$out.=' value="'.esc_html($data['name']).'" ';
 		$out.='/>';
 		if(empty($data['type']))$data['type']='';
 		$out.='<tr><th scope="row">'.__('Custom field type','church-admin').'</th><td><select name="custom-field-type" class="custom-type"><option value="boolean" '.selected('boolean',$data['type'],FALSE).'>'.__('Yes/No','church-admin').'</option><option value="date" '.selected('date',$data['type'],FALSE).'>'.__('Date','church-admin').'</option><option value="text" '.selected('text',$data['type'],FALSE).'>'.__('Text field','church-admin').'</option></select></td></tr>';

		if(!empty($data['type']))
		{
			$out.='<tr><th scope="row">'.__('Default','church-admin').'</th>';
			switch($data['type'])
			{
				case 'text':
					$out.='<td class="text" style="display:none"><input type="text" class="text-default" name="custom-field-default" ';
					if(!empty($data['default']))$out.=' value="'.esc_html($data['default']).'" ';
					$out.='/></td></tr>';

				break;
				case 'boolean':
					$out.='<td><select name="custom-field-default" class="boolean-default"><option value="1" ';
					if(!empty($data['default']))$out.=' selected="selected" ';
					$out.='>'.__('Yes','church-admin').'</option><option value="0" ';
					if(isset($data['default'])&& $data['default']=="0")$out.=' selected="selected" ';
					$out.='>'.__('No','church-admin').'</option></select></td></tr>';

			}
			$out.='<tr><th scope="row">'.__('Apply to everyone','church-admin').'</th>';
			$out.='<td><input type="checkbox" name="custom-all" ';
			if(!empty($data['all'])) $out.=' checked="checked" ';
			$out.='/></td></tr>';
		}
		else {
			$out.='<tr class="boolean" style="display:table-row"><th scope="row">'.__('Default','church-admin').'</th><td><select  class="boolean-default" name="custom-field-default"><option value="1" ';
			if(!empty($data['default']))$out.=' selected="selected" ';
			$out.='>'.__('Yes','church-admin').'</option><option value="0" ';
			if(isset($data['default'])&& $data['default']=="0")$out.=' selected="selected" ';
			$out.='>'.__('No','church-admin').'</option></select></td></tr>';

			$out.='<tr class="text"  style="display:none"><th scope="row">'.__('Default','church-admin').'</th><td><input disabled="disabled" type="text" class="text-default" name="custom-field-default" ';
			if(!empty($data['default']))$out.=' value="'.esc_html($data['default']).'" ';
			$out.='/></td></tr>';
			$out.='<tr class="all"><th scope="row">'.__('Apply to everyone','church-admin').'</th>';
			$out.='<td><input type="checkbox" name="custom-all" ';
			if(!empty($data['all'])) $out.=' checked="checked" ';
			$out.='/></td></tr>';
		}
		$out.='<script>
				jQuery(function($){$(".custom-type").change(
					function(){
						var val=$(this).val();
						switch(val)
						{
							case "boolean":$(".boolean").show();$(".text").hide();$(".text-boolean").prop("disabled", true);$(".text-boolean").prop("disabled", false);break;
							case "text":$(".boolean").hide();$(".text").show();$(".text-boolean").prop("disabled", false);$(".text-boolean").prop("disabled", true);break;
							case "date":$(".boolean").hide();$(".text").hide();$(".all").hide();break;
						}

				});
			});
		</script>';
 		$out.='<tr><td>&nbsp;</td><td><input type="hidden" name="save_custom_field" value="yes"/><input type="submit" class="button-primary" value="'.__('Save','church-admin').'"/></td></tr></table></form>';
 	}
 	return $out;


}
/**
 * Delete Custom Field
 *
 * @param
 * @param
 *
 * @author andy_moyle
 *
 */
 function church_admin_delete_custom_field($ID)
 {
	 global $wpdb;
 	$out='';
 	$custom_fields=array();
 	$custom_fields=get_option('church_admin_custom_fields');
 	if(isset($ID)&&!empty($custom_fields[$ID]))
 	{
 		//$out.='deleting - '.print_r($custom_fields[$ID],TRUE);
 		unset($custom_fields[$ID]);
 		update_option('church_admin_custom_fields',$custom_fields);
 		$wpdb->query('DELETE FROM '.CA_CUST_TBL.' WHERE custom_id="'.intval($ID).'"');
 		$out.='<div class="notice notice-success">'.__('Custom field deleted','church-admin').'</div>';

 	}
 	$out.=church_admin_list_custom_fields();
 	return $out;
 }
