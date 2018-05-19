<?php
/**
 * Template part for displaying posts.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package Energico
 */
?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php $utility = energico_utility()->utility; ?>

	<header class="entry-header">

		<?php $utility->attributes->get_title( array(
			'class' => 'entry-title',
			'html'  => '<h3 %1$s>%4$s</h3>',
			'echo'  => true,
		) );
		?>

	</header><!-- .entry-header -->

	<figure class="post-thumbnail">
		<?php $size = energico_post_thumbnail_size( array( 'class_prefix' => 'post-thumbnail--' ) ); ?>

		<?php $utility->media->get_image( array(
				'size'        => $size['size'],
				'class'       => 'post-thumbnail__link ' . $size[ 'class' ],
				'html'        => '<img class="post-thumbnail__img wp-post-image" src="%3$s" alt="%4$s" %5$s>',
				'placeholder' => false,
				'echo'        => true,
			) );
		?>

		<?php $cats_visible = energico_is_meta_visible( 'single_post_categories', 'single' ) ? 'true' : 'false'; ?>

		<?php $utility->meta_data->get_terms( array(
			'visible' => $cats_visible,
			'type'    => 'category',
			'icon'    => '',
			'before'  => '<div class="post__cats">',
			'after'   => '</div>',
			'echo'    => true,
		) );
		?>

		<div class="post-thumbnail__format-link">
			<?php do_action( 'cherry_post_format_link', array( 'render' => true ) ); ?>
		</div>
	</figure><!-- .post-thumbnail -->

	<div class="entry-content">
		<?php the_content(); ?>
		<?php wp_link_pages( array(
			'before'      => '<div class="page-links"><span class="page-links__title">' . esc_html__( 'Pages:', 'energico' ) . '</span>',
			'after'       => '</div>',
			'link_before' => '<span class="page-links__item">',
			'link_after'  => '</span>',
			'pagelink'    => '<span class="screen-reader-text">' . esc_html__( 'Page', 'energico' ) . ' </span>%',
			'separator'   => '<span class="screen-reader-text">, </span>',
		) );
		?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">


		<?php if ( 'post' === get_post_type() ) : ?>

			<div class="entry-meta">
				<?php $author_visible = energico_is_meta_visible( 'single_post_author', 'single' ) ? 'true' : 'false'; ?>
				<?php $utility->meta_data->get_author( array(
					'visible' => $author_visible,
					'class'   => 'posted-by__author',
					'prefix'  => esc_html__( 'Posted by ', 'energico' ),
					'html'    => '<span class="posted-by">%1$s<a href="%2$s" %3$s %4$s rel="author">%5$s%6$s</a></span>',
					'echo'    => true,
				) );
				?>
				<span class="post__date">
					<?php $date_visible = energico_is_meta_visible( 'single_post_publish_date', 'single' ) ? 'true' : 'false';

					$utility->meta_data->get_date( array(
						'visible' => $date_visible,
						'class'   => 'post__date-link',
						'echo'    => true,
					) );
					?>
				</span>
				<span class="post__comments">
					<?php $comment_visible = energico_is_meta_visible( 'single_post_comments', 'single' ) ? 'true' : 'false';

					$utility->meta_data->get_comment_count( array(
						'visible' => $comment_visible,
						'class'   => 'post__comments-link',
						'echo'    => true,
						'sufix'   => _n_noop( '%s Comment', '%s Comments', 'energico' ),
					) );
					?>
				</span>
			</div><!-- .entry-meta -->

		<?php endif; ?>

		<?php $tags_visible = energico_is_meta_visible( 'single_post_tags', 'single' ) ? 'true' : 'false'; ?>

		<?php $utility->meta_data->get_terms( array(
			'visible'   => $tags_visible,
			'type'      => 'post_tag',
			'delimiter' => '&nbsp; &nbsp; ',
			'before'    => '<div class="post__tags">',
			'after'     => '</div>',
			'echo'      => true,
		) );
		?>

		<?php energico_share_buttons( 'single' ); ?>
	</footer><!-- .entry-footer -->


</article><!-- #post-## -->