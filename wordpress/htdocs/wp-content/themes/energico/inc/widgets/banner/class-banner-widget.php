<?php
/**
 * Banner widget.
 *
 * @package Energico
 */

if ( ! class_exists( 'Energico_Banner_Widget' ) ) {

	class Energico_Banner_Widget extends Cherry_Abstract_Widget {

		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->widget_cssclass    = 'widget-banner';
			$this->widget_description = esc_html__( 'Display a banner in your sidebar.', 'energico' );
			$this->widget_id          = 'energico_widget_banner';
			$this->widget_name        = esc_html__( 'Banner', 'energico' );
			$this->settings           = array(
				'title'  => array(
					'type'  => 'text',
					'value' => '',
					'label' => esc_html__( 'Title:', 'energico' ),
				),
				'media_id' => array(
					'type'               => 'media',
					'multi_upload'       => false,
					'library_type'       => 'image',
					'upload_button_text' => esc_html__( 'Upload', 'energico' ),
					'value'              => '',
					'label'              => esc_html__( 'Source:', 'energico' ),
				),
				'link' => array(
					'type'        => 'text',
					'placeholder' => esc_html__( 'Type a banner`s link', 'energico' ),
					'value'       => home_url( '/' ),
					'label'       => esc_html__( 'Link:', 'energico' ),
				),
				'target' => array(
					'type'    => 'select',
					'options' => array(
						'_blank' => esc_html__( 'A new window or tab', 'energico' ),
						'_self'  => esc_html__( 'The same frame as it was clicked', 'energico' ),
					),
					'value' => '_blank',
					'label' => esc_html__( 'Opens in:', 'energico' ),
				),
			);

			parent::__construct();
		}

		/**
		 * Widget function.
		 *
		 * @see   WP_Widget
		 * @since 1.0.1
		 * @param array $args
		 * @param array $instance
		 */
		public function widget( $args, $instance ) {

			if ( empty( $instance['media_id'] ) ) {
				return;
			}

			if ( $this->get_cached_widget( $args ) ) {
				return;
			}

			ob_start();

			$this->setup_widget_data( $args, $instance );
			$this->widget_start( $args, $instance );

			$title    = ! empty( $instance['title'] ) ? $instance['title'] : $this->settings['title']['value'];
			$link     = $this->use_wpml_translate( 'link' );

			$media_id = absint( $instance['media_id'] );
			$src      = wp_get_attachment_image_src( $media_id, 'energico-banner' );
			$target   = ! empty( $instance['target'] ) && in_array( $instance['target'], array( '_blank', '_self' ) ) ? $instance['target'] : $this->settings['target']['value'];

			$template = locate_template( 'inc/widgets/banner/views/banner.php', false, false );

			if ( $template ) {
				include $template;
			}

			$this->widget_end( $args );
			$this->reset_widget_data();

			echo $this->cache_widget( $args, ob_get_clean() );
		}
	}
}

add_action( 'widgets_init', 'energico_register_banner_widget' );
function energico_register_banner_widget() {
	register_widget( 'Energico_Banner_Widget' );
}
