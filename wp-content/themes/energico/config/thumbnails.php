<?php
/**
 * Thumbnails configuration.
 *
 * @package Energico
 */

add_action( 'after_setup_theme', 'energico_register_image_sizes', 5 );
function energico_register_image_sizes() {
	set_post_thumbnail_size( 450, 261, true );

	// Registers a new image sizes.
	add_image_size( 'energico-thumb-s', 210, 140, true );
	add_image_size( 'energico-thumb-m', 578, 386, true );
	add_image_size( 'energico-thumb-blog', 690, 380, true );
	add_image_size( 'energico-thumb-projects-list', 689, 609, true );
	add_image_size( 'energico-thumb-l', 865, 386, true );
	add_image_size( 'energico-thumb-xl', 930, 500, true );
	add_image_size( 'energico-service-xl', 1920, 527, true );
	add_image_size( 'energico-thumb-masonry', 600, 999, false );
	add_image_size( 'energico-thumb-fullwidth', 1410, 783, true );
	add_image_size( 'energico-banner', 450, 524, true );
}
