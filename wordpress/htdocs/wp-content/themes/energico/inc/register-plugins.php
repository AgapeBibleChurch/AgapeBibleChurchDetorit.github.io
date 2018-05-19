<?php
/**
 * Register required plugins for TGM Plugin Activator
 *
 * @package Energico
 */
add_action( 'tgmpa_register', 'energico_register_required_plugins' );
/**
 * Register the required plugins for this theme.
 *
 * In this example, we register five plugins:
 * - one included with the TGMPA library
 * - two from an external source, one from an arbitrary source, one from a GitHub repository
 * - two from the .org repo, where one demonstrates the use of the `is_callable` argument
 *
 * The variable passed to tgmpa_register_plugins() should be an array of plugin
 * arrays.
 *
 * This function is hooked into tgmpa_init, which is fired within the
 * TGM_Plugin_Activation class constructor.
 */
function energico_register_required_plugins() {
	/*
	 * Array of plugin arrays. Required keys are name and slug.
	 * If the source is NOT from the .org repo, then source is also required.
	 */
	$plugins = array(
		array(
			'name'     => esc_html__( 'Contact Form 7', 'energico' ),
			'slug'     => 'contact-form-7',
		),
		array(
			'name'     => esc_html__( 'Cherry Sidebars', 'energico' ),
			'slug'     => 'cherry-sidebars',
		),
		array(
			'name'     => 'Cherry Services List',
			'slug'     => 'cherry-services-list',
		),
		array(
			'name'     => 'Cherry Projects',
			'slug'     => 'cherry-projects',
		),
		array(
			'name'     => 'Cherry Testimonials ',
			'slug'     => 'cherry-testi',
		),
		array(
			'name'     => 'Cherry Search ',
			'slug'     => 'cherry-search',
		),
		array(
			'name'     => 'Cherry Ld Mods Switcher',
			'slug'     => 'cherry-ld-mods-switcher',
			'source'   => ENERGICO_THEME_DIR . '/assets/plugins/cherry-ld-mods-switcher.zip',
		),
		array(
			'name'     => 'Tm Builder Integrator',
			'slug'     => 'tm-builder-integrator',
			'source'   => ENERGICO_THEME_DIR . '/assets/plugins/tm-builder-integrator.zip',
		),
		array(
			'name'     => 'Cherry Data Importer',
			'slug'     => 'cherry-data-importer',
			'source'   => ENERGICO_THEME_DIR . '/assets/plugins/cherry-data-importer.zip',
		),
		array(
			'name'     => 'Power Builder',
			'slug'     => 'tm-content-builder',
			'source'   => ENERGICO_THEME_DIR . '/assets/plugins/tm-content-builder.zip',
		),
	);

	/*
	 * Array of configuration settings. Amend each line as needed.
	 *
	 * TGMPA will start providing localized text strings soon. If you already have translations of our standard
	 * strings available, please help us make TGMPA even better by giving us access to these translations or by
	 * sending in a pull-request with .po file(s) with the translations.
	 *
	 * Only uncomment the strings in the config array if you want to customize the strings.
	 */
	$config = array(
		'id'           => 'energico',
		// Unique ID for hashing notices for multiple instances of TGMPA.
		'default_path' => '',
		// Default absolute path to bundled plugins.
		'menu'         => 'tgmpa-install-plugins',
		// Menu slug.
		'has_notices'  => true,
		// Show admin notices or not.
		'dismissable'  => true,
		// If false, a user cannot dismiss the nag message.
		'dismiss_msg'  => '',
		// If 'dismissable' is false, this message will be output at top of nag.
		'is_automatic' => false,
		// Automatically activate plugins after installation or not.
		'message'      => '',
		// Message to output right before the plugins table.


	);

	tgmpa( $plugins, $config );
}
