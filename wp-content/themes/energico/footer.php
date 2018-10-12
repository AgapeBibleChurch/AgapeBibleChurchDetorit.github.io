<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after.
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Energico
 */

?>

	</div><!-- #content -->

	<footer id="colophon" <?php energico_footer_class() ?> role="contentinfo">
		<?php get_template_part( 'template-parts/footer/layout', get_theme_mod( 'footer_layout_type' ) ); ?>
	</footer><!-- #colophon -->
</div><!-- #page -->

<?php wp_footer(); ?>
Best Website Templates at <a rel="nofollow" href="https://www.templatemonster.com" target="_blank">TemplateMonster</a>.
</body>
</html>
