<?php get_header( energico_template_base() ); ?>

	<?php do_action( 'energico_render_widget_area', 'full-width-header-area' ); ?>

	<?php energico_site_breadcrumbs(); ?>

	<div <?php echo energico_get_container_classes( array( 'site-content_wrap' ), 'content' ); ?>>

		<?php do_action( 'energico_render_widget_area', 'before-content-area' ); ?>

		<div class="row">

			<div id="primary" <?php energico_primary_content_class(); ?>>

				<?php do_action( 'energico_render_widget_area', 'before-loop-area' ); ?>

				<main id="main" class="site-main" role="main">

					<?php include energico_template_path(); ?>

				</main><!-- #main -->

				<?php do_action( 'energico_render_widget_area', 'after-loop-area' ); ?>

			</div><!-- #primary -->

		</div><!-- .row -->

		<?php do_action( 'energico_render_widget_area', 'after-content-area' ); ?>

	</div><!-- .container -->

	<?php do_action( 'energico_render_widget_area', 'after-content-full-width-area' ); ?>

<?php get_footer( energico_template_base() ); ?>
