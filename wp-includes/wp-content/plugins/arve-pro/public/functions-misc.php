<?php

function arve_use_jsapi( $a ) {

	if (
		( arve_pro_is_mobile() && in_array( $a['provider'], array( 'NOyoutube', 'NOvimeo' ), true ) ) ||
		( ! empty( $a['volume'] ) && in_array( $a['provider'], array( 'NOyoutube' ), true ) )
	) {
		return true;
	}

	return false;
}

function arve_pro_json_api_call( $api_url, $atts ) {

	$wp_remove_get_args = array();

	// wp_remote_get_fails for yahoo.
	if ( 'yahoo' === $atts['provider'] ) {

		// phpcs:disable WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$response = file_get_contents( $api_url ); // TODO Check wp_remote_post.
		// phpcs:enable WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents

		return json_decode( $response );

	} elseif ( 'twitch' === $atts['provider'] ) {

		$wp_remove_get_args['headers'] = array( 'Client-ID' => 'in8d3vsv6bmbmsdrfoch204ict7kos7' );
	}

	$response = wp_remote_get( $api_url, $wp_remove_get_args );

	if ( is_wp_error( $response ) ) {

		return new WP_Error(
			$atts['provider'] . '_thumbnail_retrieval', sprintf(
				// Translators: 1+2 URL.
				__( 'Error retrieving video information from the URL <a href="%1$s">%2$s</a> using <code>wp_remote_get()</code>. Details: %3$s', 'arve-pro' ),
				esc_url( $api_url ),
				esc_html( $api_url ),
				$response->get_error_message()
			)
		);

	} elseif ( 404 === $response['response']['code'] ) {

		if ( empty( $atts['url'] ) ) {

			$error404 = sprintf(
				// Translators: URL.
				__( 'The video is likely no longer available. (The <a href="%s">API endpoint</a> returned a 404 error)', 'arve-pro' ),
				esc_url( $api_url )
			);

		} else {

			$error404 = sprintf(
				// Translators: URL.
				__( '<a href="%1$s">The video</a> is likely no longer available. (The <a href="%2$s">API endpoint</a> returned a 404 error)', 'arve-pro' ),
				esc_url( $atts['url'] ),
				esc_url( $api_url )
			);
		}

		$error404 = apply_filters( 'arve_pro_404_error', $error404 );

		return new WP_Error( $atts['provider'] . '_thumbnail_retrieval', $error404 );

	} elseif ( 403 === $response['response']['code'] ) {

		$error403 = sprintf(
			// Translators: URL.
			__( '<a href="%s">API endpoint</a> returned a 403 error. This can occur when a video has embedding disabled or restricted to certain domains.', 'arve-pro' ),
			esc_url( $api_url )
		);

		return new WP_Error( $atts['provider'] . '_thumbnail_retrieval', $error403 );

	} else {

		return json_decode( wp_remote_retrieve_body( $response ) );
	}
}

function arve_pro_get_json_thumbnail( $api_url, $json_name, $a ) {

	$result = arve_pro_json_api_call( $api_url, $a );

	if ( is_wp_error( $result ) ) {
		return $result->get_error_messages();
	}

	if ( empty( $result->$json_name ) ) {
		return new WP_Error( 'json call', __( 'JSON value does not exist or is empty', 'arve-pro' ) );
	}

	return $result->$json_name;
}

function arve_pro_get_image_size( $img_url ) {
	$response = wp_remote_get( $img_url, array() );
	return getimagesizefromstring( wp_remote_retrieve_body( $response ) );
}

function arve_pro_is_pro_mode( $mode ) {

	$pro_modes = array_flip( arve_pro_get_pro_modes() );

	return ( in_array( $mode, $pro_modes, true ) ) ? true : false;
}

function arve_pro_is_mobile() {

	static $is_mobile = null;

	if ( null === $is_mobile ) {
		$detect    = new ARVE_Mobile_Detect();
		$is_mobile = $detect->isMobile();
	}

	return $is_mobile;
}
