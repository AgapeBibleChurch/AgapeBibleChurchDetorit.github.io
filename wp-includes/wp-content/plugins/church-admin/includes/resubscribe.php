<!doctype html>
<html>
<head><title><?php echo __('Resubscribe','church-admin');?></title>
<style>
  body { text-align: center; padding: 150px; }
  h1 { font-size: 50px; }
  body { font: 20px Helvetica, sans-serif; color: #333; }
  article { display: block; text-align: left; width: 650px; margin: 0 auto; }
  a { color: #dc8100; text-decoration: none; }
  a:hover { color: #333; text-decoration: none; }
</style>
</head><body>
<article>
	<h1><?php bloginfo('name');?></h1>
	<?php
	if(!empty($details))
	{
		echo'<p>'.esc_html(sprintf(__('Thank %1$s, You have been re-subscribed from our email list. Welcome back!','church-admin'),$details->first_name)).'</p>'; 
	
	}else echo'<p>'.__('You have been re-subscribed from our email list','church-admin').'</p>';
	
   	echo'<a href="'.site_url().'?ca_unsub='.esc_html($_GET['ca_sub']).'">'.__('Oops, please unsubscribe me','church-admin').'</a></p>';
    ?>
    <div>
        <p><?php echo'<a href="'.site_url().'">'.__('Back to main site','church-admin');?></p>
    </div>
</article></body></html>