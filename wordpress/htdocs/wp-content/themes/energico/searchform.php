<?php
/**
 * The template for displaying search form.
 *
 * @package Energico
 */
?>
<form role="search" method="get" class="search-form" action="<?php echo home_url( '/' ); ?>">
	<label>
		<span class="screen-reader-text"><?php echo _x( 'Search for:', 'label', 'energico' ) ?></span>
		<input type="search" class="search-form__field"
			placeholder="<?php echo esc_attr_x( 'Enter keyword', 'placeholder', 'energico' ) ?>"
			value="<?php echo get_search_query() ?>" name="s"
			title="<?php echo esc_attr_x( 'Search for:', 'label', 'energico' ) ?>" />
	</label>
	<button type="submit" class="search-form__submit "><i class="fa fa-search "></i></button>
</form>