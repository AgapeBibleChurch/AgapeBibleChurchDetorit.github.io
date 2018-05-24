<?php
/**
 * The template for displaying related posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Energico
 * @subpackage single-post
 */
?>
<div class="related-post page-content<?php echo esc_html( $grid_class ); ?>">
	<figure class="post-thumbnail">
		<?php echo $image; ?>
	</figure>
	<header class="entry-header">
		<?php echo $title; ?>
		<?php echo $category; ?>
	</header>
	<div class="entry-content">
		<?php echo $excerpt; ?>
	</div>
	<footer class="entry-footer">
		<div class="entry-meta">
			<?php echo $author; ?>
			<?php echo $date; ?>
			<?php echo $comment_count; ?>
		</div>
		<?php echo $tag; ?>
	</footer>


</div>
