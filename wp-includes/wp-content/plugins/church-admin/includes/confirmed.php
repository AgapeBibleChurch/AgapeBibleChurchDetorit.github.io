<!doctype html>
<html>
<head><title><?php echo __('General Data Protection Regulations Confirmation','church-admin');?></title>
<style>
  body { text-align: center; padding: 150px; }
  h1 { font-size: 50px; }
  body { font: 20px Helvetica, sans-serif; color: #333; }
  article { display: block; text-align: left; width: 650px; margin: 0 auto; }
  a { color: #dc8100; text-decoration: none; }
  a:hover { color: #333; text-decoration: none; }
</style>
</head>
<body>
<article>
	<h1><?php bloginfo('name');?></h1>
	<p><?php echo __('Thanks for confiming that you are happy to receive email, sms or mail from us and be on our address list.','church-admin');?></p>
    <div>
        <p><?php echo'<a href="'.site_url().'">'.__('Back to main site','church-admin');?></p>
    </div>
</article>
</body>
</html>