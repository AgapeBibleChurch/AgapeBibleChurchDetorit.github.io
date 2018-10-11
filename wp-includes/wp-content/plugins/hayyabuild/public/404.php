<?php
/**
 * The template for displaying 404 pages (not found).
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package hayyabuild
 */

get_header();

echo apply_filters( 'the_content', 'hayya_404_content' );

get_footer();
