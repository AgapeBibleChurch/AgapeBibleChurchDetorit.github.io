<?php
/**
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Energico
 */

while ( have_posts() ) : the_post();

	get_template_part( 'template-parts/content-single', get_post_format() );

	the_post_navigation(array(
		'next_text' => '<span>Next post</span>',
		'prev_text' => '<span>Previous post</span>',
	));

	energico_post_author_bio();

	energico_related_posts();

	// If comments are open or we have at least one comment, load up the comment template.
	if ( comments_open() || get_comments_number() ) :
		comments_template();
	endif;

endwhile; // End of the loop.
