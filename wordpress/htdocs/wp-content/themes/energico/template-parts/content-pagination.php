<?php
/**
 * Template part for posts pagination.
 *
 * @package Energico
 */
the_posts_pagination(
	array(
		'prev_text' => '<span>' . esc_html__('Previous', 'energico') . '</span>',
		'next_text' => '<span>' . esc_html__('Next', 'energico') . '</span>'
	)
);
