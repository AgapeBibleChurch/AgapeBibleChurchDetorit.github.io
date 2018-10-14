<?php
/**
 * The header for our theme.
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Energico
 */

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php energico_get_page_preloader(); ?>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'energico' ); ?></a>
	<header id="masthead" <?php energico_header_class(); ?> role="banner">
	<!--Header builder BEGIN-->
<?php do_action('stm_hb', array('header' => 'agape-bible-church-header')); ?>
<!--Header builder END-->
		</div><!-- .header-container -->
	</header><!-- #masthead -->

	<div id="content" <?php energico_content_class(); ?>>
