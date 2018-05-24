<footer class="comment-meta">
	<div class="comment-author vcard">
		<?php echo energico_comment_author_avatar(); ?>
	</div>
	<div class="comment-metadata">
		<?php printf( '<span class="posted-by">' . esc_html__( 'Posted by', 'energico' ) . '</span>' . esc_html__( ' %s', 'energico' ), energico_get_comment_author_link() ); ?>
		<?php echo energico_get_comment_date( array( 'format' => 'M d, Y' ) ); ?>
	</div>
</footer>
<div class="comment-content">
	<?php echo energico_get_comment_text(); ?>
</div>
<div class="reply">
	<?php echo energico_get_comment_reply_link( array( 'reply_text' => '<i class="material-icons">reply</i>' ) ); ?>
</div>