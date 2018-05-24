<?php
/**
 * The template for displaying the default footer layout.
 *
 * @package Energico
 */
?>

<?php
if ( get_theme_mod( 'footer_widget_area_visibility', energico_theme()->customizer->get_default( 'footer_widget_area_visibility' ) )  ) {
	?>
	<div class="footer-area-wrap <?php echo energico_check_gradient( 'footer' ) ?> ">
		<div class="container">
			<?php do_action( 'energico_render_widget_area', 'footer-area' ); ?>
		</div>
	</div>
	<?php
} ?>

<div class="footer-container">
	<div <?php echo energico_get_container_classes( array( 'site-info' ), 'footer' ); ?>>
		<div class="site-info__flex">
			<?php energico_footer_logo();?>
			<div class="site-info__mid-box"><?php
				energico_footer_copyright();
			?></div>
		</div>
	</div><!-- .site-info -->
</div><!-- .container -->
