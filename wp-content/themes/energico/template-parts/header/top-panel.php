<?php
/**
 * Template part for top panel in header.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Energico
 */

// Don't show top panel if all elements are disabled.
if ( ! energico_is_top_panel_visible() ) {
	return;
} ?>

<div class="top-panel <?php echo energico_check_gradient('top_panel') ?>">
	<div <?php echo energico_get_container_classes( array( 'top-panel__wrap' ), 'header' ); ?>><?php
		energico_top_message( '<div class="top-panel__message">%s</div>' );
		energico_top_menu();
		?>
		<div class="top-panel__inner_wrap">
			<?php
			energico_social_list( 'header' );
			energico_top_search( '<div class="top-panel__search"><span class="fa  fa-search search_toggle"></span>%s</div>' );
			?></div>
	</div>
</div><!-- .top-panel -->