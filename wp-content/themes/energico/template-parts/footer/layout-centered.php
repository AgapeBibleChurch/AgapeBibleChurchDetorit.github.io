<?php
/**
 * The template for displaying the default footer layout.
 *
 * @package Energico
 */
?>

<?php
if (  get_theme_mod( 'footer_widget_area_visibility', energico_theme()->customizer->get_default( 'footer_widget_area_visibility' ) )  ) {
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
		<?php
		energico_footer_logo();
		energico_social_list( 'footer' );
		energico_footer_copyright();
		energico_footer_menu();
		?>
	</div><!-- .site-info -->
</div><!-- .container -->
