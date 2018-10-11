<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * List of funnels
 *
 * @param
 *
 *
 * @author andy_moyle
 *
 */
function church_admin_funnel_list()
{
    global $wpdb,$people_type;
	$member_type=church_admin_member_type_array();
  $ministries=church_admin_ministries_array();
	echo'<p><a class="button-primary" href="'.wp_nonce_url('admin.php?page=church_admin/index.php&amp;action=edit_funnel','edit_funnel').'">'.__('Add a follow up funnel','church-admin').'</a></p>';


    $result=$wpdb->get_results('SELECT * FROM '.CA_FUN_TBL .'  ORDER BY funnel_order');
    if($result)
    {

        echo'<p>'.__('Follow Up funnels can be sorted by drag and drop, for use in other parts of the plugin','church-admin').'</p>';
        echo'<table id="sortable" class="widefat striped"><thead><tr><th>'.__('Edit','church-admin').'</th><th>'.__('Delete','church-admin').'</th><th>'.__('Funnel','church-admin').'</th><th>'.__('Applies to','church-admin').'...</th><th>'.__('Ministry Responsible','church-admin').'</th><th>'.__('Active','church-admin').'</th><th>'.__('Not yet emailed','church-admin').'</th><th>'.__('Completed','church-admin').'</th></tr></thead><tbody class="content ui-sortable">';
        $totalNotEmailed=0;
        foreach($result AS $row)
        {
          $active=$completed=$notEmailed=0;
          $active=$wpdb->get_var('SELECT COUNT(*) FROM '.CA_FP_TBL.' WHERE funnel_id="'.intval($row->funnel_id).'" AND completion_date="0000-00-00"');
          $notEmailed=$wpdb->get_var('SELECT COUNT(*) FROM '.CA_FP_TBL.' WHERE funnel_id="'.intval($row->funnel_id).'" AND email="0000-00-00"');
          $totalNotEmailed+=$notEmailed;
          $complete=$wpdb->get_var('SELECT COUNT(*)  FROM '.CA_FP_TBL.' WHERE funnel_id="'.intval($row->funnel_id).'" AND completion_date!="0000-00-00"');
           $edit='<a href="'.wp_nonce_url('admin.php?page=church_admin/index.php&amp;tab=people&amp;action=edit_funnel&amp;funnel_id='.intval($row->funnel_id),'edit_funnel').'">'.__('Edit','church-admin').'</a>';
				   $delete='<a onclick="return confirm(\''.__('Are you sure?','church-admin').'\');" href="'.wp_nonce_url('admin.php?page=church_admin/index.php&amp;tab=people&amp;action=delete_funnel&amp;funnel_id='.intval($row->funnel_id),'delete_funnel').'">'.__('Delete','church-admin').'</a>';

            echo'<tr class="sortable-row" id="'.intval($row->funnel_id).'"><td>'.$edit.'</td><td>'.$delete.'</td><td>'.esc_html($row->action).'</td>';
				    if(!empty($member_type[$row->member_type_id])){echo '<td>'.esc_html($member_type[$row->member_type_id]).'</td>';}else{echo'<td>&nbsp;</td>';}
				    if(!empty($ministries[$row->department_id])){echo '<td>'.$ministries[$row->department_id].'</td>';}else{echo'<td>&nbsp;</td>';}
            echo'<td>'.intval($active).'</td>';
            echo'<td>'.intval($notEmailed).'</td>';
            echo'<td>'.intval($completed).'</td>';
            echo'</tr>';

        }
		    echo'</tbody><tfoot><tr><th>'.__('Edit','church-admin').'</th><th>'.__('Delete','church-admin').'</th><th>'.__('Funnel','church-admin').'</th><th>'.__('Applies to','church-admin').'...</th><th>'.__('Ministry Responsible','church-admin').'</th><th>'.__('Active','church-admin').'</th><th>'.__('Not yet emailed','church-admin').'</th><th>'.__('Completed','church-admin').'</th></tr></tfoot></table>';
          if($totalNotEmailed)echo'<p><a class="button-secondary"   href="'.wp_nonce_url('admin.php?page=church_admin/index.php&amp;action=church_admin_email_follow_up_activity','email_funnels').'">'.__('Email newly assigned follow-up activity','church-admin').'</a></p>';
        echo '
    <script>

 jQuery(document).ready(function($) {

    var fixHelper = function(e,ui){
            ui.children().each(function() {
                $(this).width($(this).width());
            });
            return ui;
        };
    var sortable = $("#sortable tbody.content").sortable({
    helper: fixHelper,
    stop: function(event, ui) {
        //create an array with the new order


				var Order = "order="+$(this).sortable(\'toArray\').toString();

        console.log(Order);

        $.ajax({
            url: "admin.php?page=church_admin/index.php&action=church_admin_update_order&which=funnel",
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



    </script>
';
  church_admin_my_follow_ups();
    }
}
/**
 * Delete a funnel
 *
 * @param $funnel_id
 *
 *
 * @author andy_moyle
 *
 */
function church_admin_delete_funnel($funnel_id=NULL)
{
	global $wpdb;
	if(!empty($funnel_id)&&ctype_digit($funnel_id))
	{
		$wpdb->query('DELETE FROM '.CA_FUN_TBL.' WHERE funnel_id="'.esc_sql($funnel_id).'"');
		$wpdb->query('DELETE FROM '.CA_FP_TBL.' WHERE funnel_id="'.esc_sql($funnel_id).'"');
		echo'<div class="notice notice-success inline"><p><strong>'.__('Follow Up Funnel Deleted','church-admin').'</strong></p></div>';
		church_admin_funnel_list();
	}
}


/**
 * Edit a funnel
 *
 * @param
 *
 *
 * @author andy_moyle
 *
 */
function church_admin_edit_funnel($funnel_id=NULL,$people_type_id=1)
{
    global $wpdb,$people_type;
    $member_type=church_admin_member_type_array();
    $ministries=church_admin_ministries_array();


    echo'<h2>';
        if($funnel_id){echo __('Edit','church-admin');$data=$wpdb->get_row('SELECT * FROM '.CA_FUN_TBL.' WHERE funnel_id="'.esc_sql($funnel_id).'"');}else{echo __('Add','church-admin');}
        echo' '.__('Follow Up Funnel','church-admin').'</h2>';

        if(isset($_POST['edit_funnel']))
        {//process form
            //deal with new department
            if(!empty($_POST['new_ministry'])&&$_POST['new_ministry']!=__('Or add a new ministry','church-admin'))
            {
                if(!in_array(stripslashes($_POST['new_new_ministry']),$ministries))
                {
                    $wpdb->query('INSERT INTO '.CA_MIN_TBL.' (ministry) VALUES ("'.esc_sql(stripslashes($_POST['new_ministry'])).'")');
                    $ministries[]=stripslashes($_POST['new_new_ministry']);
                }
            }
            if(!$funnel_id)$funnel_id=$wpdb->get_var('SELECT funnel_id FROM '.CA_FUN_TBL.' WHERE action="'.esc_sql(stripslashes($_POST['action'])).'" AND member_type_id="'.esc_sql((int)($_POST['member_type_id'])).'"');
            if($funnel_id)
            {//update
                $success=$wpdb->query('UPDATE '.CA_FUN_TBL.' SET people_type_id="'.esc_sql($people_type_id).'", action="'.esc_sql(stripslashes($_POST['action'])).'",member_type_id="'.esc_sql((int)($_POST['member_type_id'])).'",department_id="'.esc_sql((int)($_POST['ministry_id'])).'" WHERE funnel_id="'.esc_sql($funnel_id).'"');
            }//end update
            else
            {//insert
                $success=$wpdb->query('INSERT INTO '.CA_FUN_TBL.' (action,member_type_id,department_id,people_type_id)VALUES("'.esc_sql(stripslashes($_POST['action'])).'" ,"'.esc_sql((int)($_POST['member_type_id'])).'","'.esc_sql((int)($_POST['ministry_id'])).'","'.esc_sql($people_type_id).'")');
            }//insert
            echo '<div class="notice notice-success inline"><p>'.__('Funnel Updated','church-admin').'</p></div>';
            church_admin_funnel_list($people_type_id);
        }//end process form
        else
        {//form
           echo'<form action="" method="POST">';

           //funnel action
           echo'<table class="form-table"><tbody><tr><th scope="row">'.__('Funnel Action','church-admin').'</th><td><input type="text" name="action" ';
           if(!empty($data->action))echo ' value="'.esc_html($data->action).'" ';
           echo'/></td></tr>';
           //member type
           echo'<tr><th scope="row">'.__('Link to Member Type','church-admin').'</th><td><select name="member_type_id">';
           $first='<option value="">'.__('Please select member type','church-admin').'</option>';
           $option='';
           foreach($member_type AS $id=>$type)
           {
             if($id==$data->member_type_id){$first='<option value="'.intval($id).'" selected="selected">'.esc_html($type).'</option>'; }else{$option.='<option value="'.intval($id).'" >'.esc_html($type).'</option>';}
           }
           echo $first.$option.'</option></select></td></tr>';
           //responsible department
           echo'<tr><th scope="row">'.__('Ministry responsible for action','church-admin').'</th><td><select name="ministry_id">';
           $first=$option='';
           foreach($ministries AS $id=>$type)
           {
             if($id==$data->member_type_id){$first='<option value="'.intval($id).'" selected="selected">'.esc_html($type).'</option>'; }else{$option.='<option value="'.intval($id).'" >'.esc_html($type).'</option>';}
           }
           echo $first.$option.'</option></select></td></tr>';
           echo '<tr><th scope="row">'.__('Or create a new ministry','church-admin').'</th><td><input type="text" name="new_ministry" onfocus="javascript:this.value=\'\';" value="'.__('Or add a new ministry','church-admin').'"/></td></tr>';

           echo'<tr><th scope="row">&nbsp;</th><td><input type="hidden" name="edit_funnel" value="yes"/><input class="button-primary" type="submit" value="'.__('Save Follow Up Funnel','church-admin').' &raquo;" /></td></tr></tbody></table></form>';
        }//form

}


function church_admin_follow_up_completed($id)
{
    global $wpdb;
    $followUpID=$wpdb->get_var('SELECT id FROM  '.CA_FP_TBL.' WHERE md5(CONCAT("follow_up",`id`))="'.esc_sql($id).'"');
    if($followUpID)
    {
      $wpdb->query('UPDATE '.CA_FP_TBL.' SET completion_date="'.date('Y-m-d').'" WHERE id="'.esc_sql($followUpID).'"');
      echo '<div class="notice notice-inline notice-success"><h2>Funnel Completed</h2></div>';
      church_admin_my_follow_ups();
    }else
    {
        wp_exit(__('Follow up task not found','church-admin'));
    }

}

function church_admin_my_follow_ups()
{
    global $wpdb,$current_user;
    $user_id=get_current_user_id();
    $people_id=$wpdb->get_var('SELECT people_id FROM '.CA_PEO_TBL.' WHERE user_id="'.intval($user_id).'"');
    if($people_id)
    {
      echo'<h2>'.__('My follow up tasks','church-admin').'</h2>';
      $tasks=$wpdb->get_results($sql='SELECT * FROM '.CA_FP_TBL.'  LEFT JOIN '.CA_FUN_TBL.' ON '.CA_FP_TBL.'.funnel_id = '.CA_FUN_TBL.'.funnel_id LEFT JOIN '.CA_PEO_TBL.' ON '.CA_FP_TBL.'.people_id = '.CA_PEO_TBL.'.people_id LEFT JOIN '.CA_HOU_TBL.' ON '.CA_PEO_TBL.'.household_id = '.CA_HOU_TBL.'.household_id WHERE '.CA_FP_TBL.'.assign_id="'.intval($people_id).'" AND '.CA_FP_TBL.'.completion_date="0000-00-00"');

      if($tasks)
      {
          echo'<table class="widefat"><thead><tr><th>'.__('Follup Up Task','church-admin').'</th><th>'.__('Who','church-admin').'</th><th>'.__('Completed','church-admin').'</th></tr></thead><tfoot><tr><th>'.__('Follup Up Task','church-admin').'</th><th>'.__('Who','church-admin').'</th><th>'.__('Completed','church-admin').'</th></tr></tfoot><tbody>';
          foreach($tasks AS $task)
          {
            echo '<tr><td>'.esc_html($task->action).'</td><td>'.esc_html($task->first_name.' '.$task->last_name).'</td><td><a href="'.admin_url().'?page=church_admin/index.php&amp;action=follow_up_completed&id='.md5('follow_up'.$task->id).'">'.__("Completed",'church-admin').'</a></td></tr>';
          }
          echo'</tbody></table>';
      }
      else{echo '<p>'.__('No follow up taks for you currently','church-admin').'</p>';}
    }
}
