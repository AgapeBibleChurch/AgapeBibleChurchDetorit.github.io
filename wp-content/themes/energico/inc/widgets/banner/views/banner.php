<?php
/**
 * Template part to display Banner widget.
 *
 * @package Energico
 * @subpackage widgets
 */
?>

<a class="widget-banner__link" href="<?php echo energico_render_macros( $link ); ?>"
   target="<?php echo esc_attr( $target ); ?>">
	<img class="widget-banner__img" src="<?php echo esc_url( $src[0] ); ?>" width="<?php echo esc_attr( $src[1] ); ?>"
	     height="<?php echo esc_attr( $src[2] ); ?>" alt="<?php echo esc_attr( $title ); ?>">
</a>