<?php
/**
 * Theme hooks.
 *
 * @package Energico
 */

// Menu description.
add_filter( 'walker_nav_menu_start_el', 'energico_nav_menu_description', 10, 4 );

// Sidebars classes.
add_filter( 'energico_widget_area_classes', 'energico_set_sidebar_classes', 10, 2 );

// Add row to footer area classes.
add_filter( 'energico_widget_area_classes', 'energico_add_footer_widgets_wrapper_classes', 10, 2 );

// Set footer columns.
add_filter( 'dynamic_sidebar_params', 'energico_get_footer_widget_layout' );

// Adapt default image post format classes to current theme.
add_filter( 'cherry_post_formats_image_css_model', 'energico_add_image_format_classes', 10, 2 );

// Enqueue sticky menu if required.
add_filter( 'energico_theme_script_depends', 'energico_enqueue_misc' );

// Add has/no thumbnail classes for posts.
add_filter( 'post_class', 'energico_post_thumb_classes' );

// Modify a comment form.
add_filter( 'comment_form_defaults', 'energico_modify_comment_form' );

// Additional body classes.
add_filter( 'body_class', 'energico_extra_body_classes' );

// Render macros in text widgets.
add_filter( 'widget_text', 'energico_render_widget_macros' );

// Adds the meta viewport to the header.
add_action( 'wp_head', 'energico_meta_viewport', 0 );

// Customization for `Tag Cloud` widget.
add_filter( 'widget_tag_cloud_args', 'energico_customize_tag_cloud' );

// Changed excerpt more string.
add_filter( 'excerpt_more', 'energico_excerpt_more' );

// Disable sidebar in 404
add_filter( 'theme_mod_sidebar_position', 'energico_404_sidebar_position' );

// Add custom services listing layout
add_filter( 'cherry_services_listing_templates_list', 'energico_add_custom_services_list' );

// Adds custom macros for cherry services.
add_filter( 'cherry_services_data_callbacks', 'energico_services_data_callbacks' );

// Adds custom macros for cherry projects.
add_filter( 'cherry_services_list_meta_options_args', 'energico_projects_add_features_background' );

// Hide sidebar in single project page
add_filter( 'theme_mod_sidebar_position', 'energico_specific_sidebar_position' );

/**
 * Append description into nav items
 *
 * @param  string $item_output The menu item output.
 * @param  WP_Post $item Menu item object.
 * @param  int $depth Depth of the menu.
 * @param  array $args wp_nav_menu() arguments.
 *
 * @return string
 */
function energico_nav_menu_description( $item_output, $item, $depth, $args ) {

	if ( 'main' !== $args->theme_location || ! $item->description ) {
		return $item_output;
	}

	$descr_enabled = get_theme_mod(
		'header_menu_attributes',
		energico_theme()->customizer->get_default( 'header_menu_attributes' )
	);

	if ( ! $descr_enabled ) {
		return $item_output;
	}

	$current     = $args->link_after . '</a>';
	$description = '<div class="menu-item__desc">' . $item->description . '</div>';
	$item_output = str_replace( $current, $description . $current, $item_output );

	return $item_output;
}

/**
 * Set layout classes for sidebars.
 *
 * @since  1.0.0
 * @uses   energico_get_layout_classes.
 *
 * @param  array $classes Additional classes.
 * @param  string $area_id Sidebar ID.
 *
 * @return array
 */
function energico_set_sidebar_classes( $classes, $area_id ) {

	if ( 'sidebar' !== $area_id ) {
		return $classes;
	}

	return energico_get_layout_classes( 'sidebar', $classes );
}

/**
 * Set layout classes for sidebars.
 *
 * @since  1.0.0
 *
 * @param  array $classes Additional classes.
 * @param  string $area_id Sidebar ID.
 *
 * @return array
 */
function energico_add_footer_widgets_wrapper_classes( $classes, $area_id ) {

	if ( 'footer-area' !== $area_id ) {
		return $classes;
	}

	$classes[] = 'row';

	return $classes;
}


/**
 * Get footer widgets layout class
 *
 * @since  1.0.0
 *
 * @param  string $params Existing widget classes.
 *
 * @return string
 */
function energico_get_footer_widget_layout( $params ) {

	if ( is_admin() ) {
		return $params;
	}

	if ( empty( $params[0]['id'] ) || 'footer-area' !== $params[0]['id'] ) {
		return $params;
	}

	if ( empty( $params[0]['before_widget'] ) ) {
		return $params;
	}

	$columns = get_theme_mod(
		'footer_widget_columns',
		energico_theme()->customizer->get_default( 'footer_widget_columns' )
	);

	$columns = intval( $columns );
	$classes = 'class="col-xs-12 col-sm-%2$s col-md-%1$s %3$s ';

	switch ( $columns ) {
		case 4:
			$md_col = 3;
			$sm_col = 6;
			$extra  = '';
			break;

		case 3:
			$md_col = 4;
			$sm_col = 4;
			$extra  = '';
			break;

		case 2:
			$md_col = 6;
			$sm_col = 6;
			$extra  = '';
			break;

		default:
			$md_col = 12;
			$sm_col = 12;
			$extra  = 'footer-area--centered';
			break;
	}

	$params[0]['before_widget'] = str_replace(
		'class="',
		sprintf( $classes, $md_col, $sm_col, $extra ),
		$params[0]['before_widget']
	);

	return $params;
}

/**
 * Filter image CSS model
 *
 * @param  array $css_model Default CSS model.
 * @param  array $args Post formats module arguments.
 *
 * @return array
 */
function energico_add_image_format_classes( $css_model, $args ) {
	$css_model['link'] .= ' post-thumbnail--fullwidth';

	return $css_model;
}

/**
 * Add jQuery Stickup to theme script dependencies if required.
 *
 * @param  array $depends Default dependencies.
 *
 * @return array
 */
function energico_enqueue_misc( $depends ) {
	$header_menu_sticky = get_theme_mod( 'header_menu_sticky', energico_theme()->customizer->get_default( 'header_menu_sticky' ) );

	if ( $header_menu_sticky && ! wp_is_mobile() ) {
		$depends[] = 'jquery-stickup';
	}

	$totop_visibility = get_theme_mod( 'totop_visibility', energico_theme()->customizer->get_default( 'totop_visibility' ) );

	if ( $totop_visibility ) {
		$depends[] = 'jquery-totop';
	}

	return $depends;
}

/**
 * Add has/no thumbnail classes for posts
 *
 * @param  array $classes Existing classes.
 *
 * @return array
 */
function energico_post_thumb_classes( $classes ) {
	$thumb = 'no-thumb';

	if ( has_post_thumbnail() ) {
		$thumb = 'has-thumb';
	}

	$classes[] = $thumb;

	return $classes;
}

/**
 * Add placeholder attributes for comment form fields.
 *
 * @param  array $args Argumnts for comment form.
 *
 * @return array
 */
function energico_modify_comment_form( $args ) {
	$args = wp_parse_args( $args );

	if ( ! isset( $args['format'] ) ) {
		$args['format'] = current_theme_supports( 'html5', 'comment-form' ) ? 'html5' : 'xhtml';
	}

	$req       = get_option( 'require_name_email' );
	$aria_req  = ( $req ? " aria-required='true'" : '' );
	$html_req  = ( $req ? " required='required'" : '' );
	$html5     = 'html5' === $args['format'];
	$commenter = wp_get_current_commenter();

	$args['label_submit'] = esc_html__( 'Submit Comment', 'energico' );

	$args['fields']['author'] = '<p class="comment-form-author"><input id="author" class="comment-form__field" name="author" type="text" placeholder="' . esc_html__( 'Your name', 'energico' ) . ( $req ? ' *' : '' ) . '" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . $html_req . ' /></p>';

	$args['fields']['email'] = '<p class="comment-form-email"><input id="email" class="comment-form__field" name="email" ' . ( $html5 ? 'type="email"' : 'type="text"' ) . ' placeholder="' . esc_html__( 'Your e-mail', 'energico' ) . ( $req ? ' *' : '' ) . '" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30" aria-describedby="email-notes"' . $aria_req . $html_req . ' /></p>';

	$args['fields']['url'] = '<p class="comment-form-url"><input id="url" class="comment-form__field" name="url" ' . ( $html5 ? 'type="url"' : 'type="text"' ) . ' placeholder="' . esc_html__( 'Your website', 'energico' ) . '" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></p>';

	$args['comment_field'] = '<p class="comment-form-comment"><textarea id="comment" class="comment-form__field" name="comment" placeholder="' . esc_html__( 'Comments *', 'energico' ) . '" cols="45" rows="8" aria-required="true" required="required"></textarea></p>';

	return $args;
}

/**
 * Add extra body classes
 *
 * @param  array $classes Existing classes.
 *
 * @return array
 */
function energico_extra_body_classes( $classes ) {

	// Adds a class of group-blog to blogs with more than 1 published author.
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	if ( ! energico_is_top_panel_visible() ) {
		$classes[] = 'top-panel-invisible';
	}

	// Adds a class based on header layout type.
	$header_layout = get_theme_mod( 'header_layout_type', energico_theme()->customizer->get_default( 'header_layout_type' ) );
	$classes[]     = 'header-layout-' . $header_layout;

	// Adds a options-based classes.
	$header_layout  = get_theme_mod( 'page_layout_type', energico_theme()->customizer->get_default( 'header_container_type' ) );
	$content_layout = get_theme_mod( 'content_container_type', energico_theme()->customizer->get_default( 'content_container_type' ) );
	$footer_layout  = get_theme_mod( 'footer_container_type', energico_theme()->customizer->get_default( 'footer_container_type' ) );
	$blog_layout    = get_theme_mod( 'blog_layout_type', energico_theme()->customizer->get_default( 'blog_layout_type' ) );
	$sb_position    = get_theme_mod( 'sidebar_position', energico_theme()->customizer->get_default( 'sidebar_position' ) );
	$sidebar        = get_theme_mod( 'sidebar_width', energico_theme()->customizer->get_default( 'sidebar_width' ) );

	return array_merge( $classes, array(
		'header-layout-' . $header_layout,
		'content-layout-' . $content_layout,
		'footer-layout-' . $footer_layout,
		'blog-' . $blog_layout,
		'position-' . $sb_position,
		'sidebar-' . str_replace( '/', '-', $sidebar ),
	) );
}

/**
 * Replace macroses in text widget.
 *
 * @param  string $text Default text.
 *
 * @return string
 */
function energico_render_widget_macros( $text ) {
	$uploads = wp_upload_dir();

	$data = array(
		'/%%uploads_url%%/' => $uploads['baseurl'],
		'/%%home_url%%/'    => home_url(),
		'/%%theme_url%%/'   => get_stylesheet_directory_uri(),
	);

	return preg_replace( array_keys( $data ), array_values( $data ), $text );
}

/**
 * Adds the meta viewport to the header.
 *
 * @since  1.0.1
 * @return string `<meta>` tag for viewport.
 */
function energico_meta_viewport() {
	echo '<meta name="viewport" content="width=device-width, initial-scale=1" />' . "\n";
}

/**
 * Customization for `Tag Cloud` widget.
 *
 * @since  1.0.1
 *
 * @param  array $args Widget arguments.
 *
 * @return array
 */
function energico_customize_tag_cloud( $args ) {
	$args['smallest'] = 14;
	$args['largest']  = 14;
	$args['unit']     = 'px';

	return $args;
}

/**
 * Replaces `[...]` (appended to automatically generated excerpts) with `...`.
 *
 * @since  1.0.1
 *
 * @param  string $more The string shown within the more link.
 *
 * @return string
 */
function energico_excerpt_more( $more ) {

	if ( is_admin() ) {
		return $more;
	}

	return ' &hellip;';
}

/**
 * Disable sidebar in 404
 *
 * @param $value
 *
 * @return string
 */
function energico_404_sidebar_position( $value ) {

	if ( is_404() ) {
		return 'fullwidth';
	}

	return $value;
}

/**
 * Add custom services listing layout
 *
 * @param $args
 *
 * @return mixed
 */
function energico_add_custom_services_list( $args ) {
	$args['custom-listing'] = 'custom-listing.tmpl';

	return $args;
}

/**
 * Get new services macros
 *
 * @param $array
 *
 * @return mixed
 */
function energico_services_data_callbacks( $array ) {
	$array['imgurl']            = 'energico_get_service_image_url';
	$array['advanced_features'] = 'energico_get_service_features_background_image_url';

	return $array;
}

/**
 * Get service image url
 *
 * @return string
 */
function energico_get_service_image_url() {
	global $post;
	$imgurl = sprintf(
		'%s',
		get_the_post_thumbnail_url( $post->ID, 'energico-thumb-fullwidth' )
	);

	return $imgurl;
}

/**
 * Get service features background image url
 *
 * @return string
 */
function energico_get_service_features_background_image_url() {
	global $post;

	$features = get_post_meta( $post->ID, 'cherry-services-features', true );
	$result   = '';

	$features_title_format = apply_filters(
		'cherry_services_features_title_format',
		'<div class="container">
			<h3 class="service-features_title">%s</h3>
		</div>'
	);

	$features_title = get_post_meta( $post->ID, 'cherry-services-features-title', true );
	if ( $features_title ) {
		$result .= sprintf( $features_title_format, $features_title );
	}

	$feature_format = apply_filters(
		'cherry_services_feature_format',
		'<div class="service-features_row" style="background-image: url(%3$s)">
			<div class="container">
					<span class="service-features_label">%1$s</span><span class="service-features_value">%2$s</span>
			</div>
		</div>'
	);

	foreach ( $features as $feature ) {
		$imgurl = wp_get_attachment_image_url( $feature['image'], 'energico-service-xl' );
		$result .= sprintf( $feature_format, esc_html( $feature['label'] ), esc_html( $feature['value'] ), $imgurl );
	}

	return sprintf( '<div class="service-features">%s</div>', $result );
}

/**
 * Adds custom macros for cherry projects.
 *
 * @param $args
 *
 * @return mixed
 */
function energico_projects_add_features_background( $args ) {
	$args['fields']['cherry-services-features']['fields']['image'] = array(
		'name'               => 'image',
		'id'                 => 'image',
		'type'               => 'media',
		'element'            => 'control',
		'parent'             => 'styling',
		'multi_upload'       => false,
		'library_type'       => 'image',
		'upload_button_text' => esc_html__( 'Add Image', 'energico' ),
		'label'              => esc_html__( 'Feature background image', 'energico' ),
		'sanitize_callback'  => 'esc_attr',
	);

	return $args;
}

/**
 * Hide sidebar in single project page
 *
 * @param $value
 *
 * @return string
 */
function energico_specific_sidebar_position($value){
	if ( is_singular( 'projects' )) {
		return 'fullwidth';
	}

	return $value;
}
