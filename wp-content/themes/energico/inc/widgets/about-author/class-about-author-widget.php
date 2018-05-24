<?php
/**
 * Widget about author.
 *
 * @package Energico
 */
if ( ! class_exists( 'Energico_About_Author_Widget' ) ) {

	class Energico_About_Author_Widget extends Cherry_Abstract_Widget {

		/**
		 * Constructor
		 */
		public function __construct() {

			$this->widget_cssclass    = 'energico widget-about-author';
			$this->widget_description = esc_html__( 'Display an information about selected user.', 'energico' );
			$this->widget_id          = 'energico_widget_about_author';
			$this->widget_name        = esc_html__( 'About Author', 'energico' );
			$this->settings           = array(
				'title'                   => array(
					'type'  => 'text',
					'value' => esc_html__( 'About Author', 'energico' ),
					'label' => esc_html__( 'Title', 'energico' ),
				),
				'user_id'                 => array(
					'type'             => 'select',
					'size'             => 1,
					'value'            => '',
					'options_callback' => array( $this, 'get_users_list' ),
					'options'          => false,
					'label'            => esc_html__( 'Select user to show', 'energico' ),
				),
				'avatar_size'             => array(
					'type'       => 'slider',
					'max_value'  => 512,
					'min_value'  => 0,
					'value'      => 250,
					'step_value' => 1,
					'label'      => esc_html__( 'Author avatar size (set 0 to hide avatar, applied only for Gravatar)', 'energico' ),
				),
				'avatar_img'              => array(
					'type'               => 'media',
					'value'              => '',
					'multi_upload'       => false,
					'library_type'       => 'image',
					'upload_button_text' => esc_html__( 'Select image', 'energico' ),
					'label'              => esc_html__( 'Custom avatar image (override default user avatar)', 'energico' ),
				),
				'link'                    => array(
					'type'  => 'text',
					'value' => '',
					'label' => esc_html__( 'Link (leave empty to hide)', 'energico' ),
				),
				'link_text'               => array(
					'type'  => 'text',
					'value' => esc_html__( 'Read More', 'energico' ),
					'label' => esc_html__( 'Link label', 'energico' ),
				),
				'enable_background'       => array(
					'type'    => 'checkbox',
					'value'   => array(
						'enable_background' => 'false',
					),
					'options' => array(
						'enable_background' => array(
							'label' => esc_html__( 'Enable Custom Background', 'energico' ),
							'slave' => 'background_image'
						),
					),
				),
				'background_image'        => array(
					'type'               => 'media',
					'label'              => esc_html__( 'Background Image', 'energico' ),
					'upload_button_text' => esc_html__( 'Choose Image', 'energico' ),
					'multi_upload'       => false,
					'master'             => 'background_image',
				),
				'invert_text_colorscheme' => array(
					'type'    => 'checkbox',
					'value'   => array(
						'invert_text_colorscheme' => 'true',
					),
					'master'  => 'background_image',
					'options' => array(
						'invert_text_colorscheme' => esc_html__( 'Use "Invert scheme" for text color', 'energico' ),
					),
				),
				'background_position'     => array(
					'type'    => 'select',
					'label'   => esc_html__( 'Background Position', 'energico' ),
					'value'   => 'center',
					'options' => array(
						'top-left'      => esc_html__( 'Top Left', 'energico' ),
						'top-center'    => esc_html__( 'Top Center', 'energico' ),
						'top-right'     => esc_html__( 'Top Right', 'energico' ),
						'center-left'   => esc_html__( 'Middle Left', 'energico' ),
						'center'        => esc_html__( 'Middle Center', 'energico' ),
						'center-right'  => esc_html__( 'Middle Right', 'energico' ),
						'bottom-left'   => esc_html__( 'Bottom Left', 'energico' ),
						'bottom-center' => esc_html__( 'Bottom Center', 'energico' ),
						'bottom-right'  => esc_html__( 'Bottom Right', 'energico' ),
					),
					'master'  => 'background_image',
				),
				'background_repeat'       => array(
					'type'    => 'select',
					'label'   => esc_html__( 'Background Position', 'energico' ),
					'value'   => 'no-repeat',
					'options' => array(
						'repeat'    => esc_html__( 'Repeat', 'energico' ),
						'repeat-x'  => esc_html__( 'Repeat X', 'energico' ),
						'repeat-y'  => esc_html__( 'Repeat Y', 'energico' ),
						'no-repeat' => esc_html__( 'No repeat', 'energico' ),
					),
					'master'  => 'background_image',
				),
				'background_size'         => array(
					'type'    => 'select',
					'label'   => esc_html__( 'Background Size', 'energico' ),
					'value'   => 'inherit',
					'options' => array(
						'cover'   => esc_html__( 'Cover', 'energico' ),
						'contain' => esc_html__( 'Contain', 'energico' ),
						'auto'    => esc_html__( 'Auto', 'energico' ),
					),
					'master'  => 'background_image',
				),
				'background_color'        => array(
					'type'   => 'colorpicker',
					'label'  => esc_html__( 'Background Color', 'energico' ),
					'master' => 'background_image',
				),
			);

			remove_filter( 'pre_user_description', 'wp_filter_kses' );
			add_filter( 'pre_user_description', 'wp_filter_post_kses' );

			parent::__construct();
		}

		/**
		 * Get blog user list array
		 *
		 * @return array
		 */
		public function get_users_list() {

			$users = get_users();

			$result = array( '0' => esc_html__( 'Select a user', 'energico' ) );

			if ( empty( $users ) ) {
				return array();
			}

			foreach ( $users as $user ) {
				$result[ $user->data->ID ] = $user->data->user_nicename;
			}

			return $result;
		}

		/**
		 * Get author name
		 *
		 * @return string
		 */
		public function get_author_name() {
			$user = get_userdata( intval( $this->instance['user_id'] ) );

			if ( ! $user ) {
				return;
			}

			return sprintf( '<h5 class="about-author_name">%s</h5>', $user->display_name );
		}

		/**
		 * Get author name
		 *
		 * @return string
		 */
		public function get_author_avatar() {
			$format = '<div class="about-author_avatar">%s</div>';
			$size   = intval( $this->instance['avatar_size'] );

			if ( ! empty( $this->instance['avatar_img'] ) ) {
				$avatar_src = wp_get_attachment_image_src( intval( $this->instance['avatar_img'] ), 'energico-author-avatar' );
				$avatar     = sprintf( '<img class="about-author_img" src="%s" width="%d" height="%d" alt="avatar">', esc_url( $avatar_src[0] ), $size, $size );

				return sprintf( $format, $avatar );
			}

			if ( empty( $this->instance['avatar_size'] ) || ( '0' === $this->instance['avatar_size'] ) ) {
				return;
			}

			$user_id = intval( $this->instance['user_id'] );
			$user    = get_userdata( $user_id );

			if ( ! $user ) {
				return;
			}

			$avatar = get_avatar( $user_id, $size, '', $user->display_name, array( 'class' => 'about-author_img' ) );

			return sprintf( $format, $avatar );
		}

		/**
		 * Get current author description
		 *
		 * @return string
		 */
		public function get_author_description() {
			$user = get_userdata( intval( $this->instance['user_id'] ) );

			if ( ! $user ) {
				return;
			}

			return sprintf(
				'<div class="about-author_description">%s</div>',
				stripslashes( wp_filter_post_kses( $user->description ) )
			);
		}

		/**
		 * Get author button
		 *
		 * @return string
		 */
		public function get_author_button() {

			if ( empty( $this->instance['link'] ) ) {
				return;
			}

			$btn_class = 'btn btn-primary';

			$link_text = $this->use_wpml_translate( 'link_text' );
			$link      = $this->use_wpml_translate( 'link' );

			return sprintf(
				'<div class="about-author_btn_box"><a href="%2$s" class="about-author_btn %3$s">%1$s</a></div>',
				wp_kses( $link_text, wp_kses_allowed_html( 'post' ) ),
				esc_url( $link ),
				$btn_class
			);

		}

		/**
		 * Get widget title.
		 *
		 * @return string
		 */
		public function get_widget_title() {

			return sprintf(
				'<div class="about-author_title"><span>%s</span></div>',
				wp_kses( $this->instance['title'], wp_kses_allowed_html( 'post' ) )
			);
		}

		/**
		 * widget function.
		 *
		 * @see WP_Widget
		 *
		 * @param array $args
		 * @param array $instance
		 */
		public function widget( $args, $instance ) {

			if ( empty( $instance['user_id'] ) || '0' == $instance['user_id'] ) {
				return;
			}

			if ( $this->get_cached_widget( $args ) ) {
				return;
			}

			ob_start();

			$this->setup_widget_data( $args, $instance );
			$this->widget_start( $args, $instance );

			$template = locate_template( 'inc/widgets/about-author/view/about-author.php', false, false );

			include $template;

			$background_enabled = ( ! empty( $instance['enable_background'] ) ) ? $instance['enable_background'] : false;

			if ( is_array( $background_enabled ) && 'true' === $background_enabled['enable_background'] ) {

				$background_styles_template = locate_template( 'inc/widgets/about-author/view/background-styles-view.php', false, false );

				if ( $background_styles_template ) {
					include $background_styles_template;
				}
			}

			$this->widget_end( $args );
			$this->reset_widget_data();
			wp_reset_postdata();

			echo $this->cache_widget( $args, ob_get_clean() );
		}
	}
}

add_action( 'widgets_init', 'energico_register_about_author_widgets' );
function energico_register_about_author_widgets() {
	register_widget( 'Energico_About_Author_Widget' );
}
