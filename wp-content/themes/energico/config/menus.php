<?php
/**
 * Menus configuration.
 *
 * @package Energico
 */

add_action( 'after_setup_theme', 'energico_register_menus', 5 );
function energico_register_menus() {

	// This theme uses wp_nav_menu() in four locations.
	register_nav_menus( array(
		'top'    => esc_html__( 'Top', 'energico' ),
		'main'   => esc_html__( 'Main', 'energico' ),
		'footer' => esc_html__( 'Footer', 'energico' ),
		'social' => esc_html__( 'Social', 'energico' ),
	) );
}
