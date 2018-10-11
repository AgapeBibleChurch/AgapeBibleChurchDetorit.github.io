<?php
/**
 * Weclome Page Class
 *
 * @since       1.3.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

function wpinked_so_admin_page() {
	add_menu_page(
		'Widgets for SiteOrigin',
		__( 'WPinked Widgets', 'wpinked-widgets' ),
		'manage_options',
		'wpinked-widgets',
		'wpinked_so_admin_page_content',
		plugin_dir_url(__FILE__) . 'img/menu-icon.png',
		99
	);

	add_submenu_page(
		'wpinked-widgets',
		'Welcome to Widgets for SiteOrigin',
		__( 'Get Addons', 'wpinked-widgets' ),
		'manage_options',
		'wpinked-widgets',
		'wpinked_so_admin_page_content'
	);

	// add_submenu_page(
	// 	'wpinked-widgets',
	// 	'Craft',
	// 	__( 'Craft Theme', 'wpinked-widgets' ),
	// 	'manage_options',
	// 	'wpinked-craft-theme',
	// 	'wpinked_so_admin_craft'
	// );
}
add_action( 'admin_menu', 'wpinked_so_admin_page' );

// function wpinked_so_admin_craft_external_link() {
//         $menu_redirect = isset( $_GET['page'] ) ? $_GET['page'] : false;
//         if ( $menu_redirect == 'wpinked-craft-theme' ) {
//             header( 'Location: https://wpinked.com/downloads/craft-theme/ ‎' );
//             exit();
//         }
// }
// add_action( 'admin_init', 'wpinked_so_admin_craft_external_link', 1 );

function wpinked_so_admin_page_content() {
?>
	<div class="iw-admin-page">

		<div class="iw-page-header">
			<p class="iw-admin-thank" style="margin-bottom: 75px;">Thank you for installing Widgets for SiteOrigin!</p>

			<h1 class="iw-admin-welcome">Welcome to Widgets for SiteOrigin <?php echo INKED_SO_VER; ?></h1>
			<h2 class="iw-admin-tagline">Modern Widgets for Beautiful Websites</h2>
			<p class="iw-admin-links">
				Show you appreciation with a <a href="https://wordpress.org/support/view/plugin-reviews/widgets-for-siteorigin#postform" class="thankyou" target="_blank" title="Ok, you deserved it">5-star rating</a> |
				<a href="https://wpinked.com/forums/" target="_blank">Support Forum</a> |
				Refer : <a href="https://wpinked.com/widgets-for-siteorigin-docs/" target="_blank">Documentation</a> &bull; <a href="http://widgets.wpinked.com/" target="_blank">Demo</a>
			</p>

		</div>

		<div class="iw-page-content">

			<div class="iw-admin-box pro">
				<h2 class="premium-plugin-features">Widgets for SiteOrigin Pro Features</h2>
				<div class="feature">
					<h3 class="feature-title">Blog Enhanced</h3>
					<p class="feature-content">Extending on the blog widget, this widget comes with a set of predesigned article templates. It also comes equipped with AJAX powered navigation.</p>
					<p class="feature-links"><a href="http://widgets.wpinked.com/blog-enhanced-widget/" target="_blank">Demo</a></p>
				</div>
				<div class="feature new">
					<h3 class="feature-title">Portfolio Enhanced</h3>
					<p class="feature-content">Extending on the portfolio widget, this widget comes more project templates. It also has an option to use a vertical layout for the filters.</p>
					<p class="feature-links"><a href="http://913.68b.myftpupload.com/portfolio-enhanced-widget-pro/" target="_blank">Demo</a></p>
				</div>
				<div class="feature new">
					<h3 class="feature-title">WooCommerce Products</h3>
					<p class="feature-content">This nifty plugin allows for listing of WooCommerce products in a neat grid</p>
					<p class="feature-links"><a href="http://913.68b.myftpupload.com/woocommerce-products-widget-pro/" target="_blank">Demo</a></p>
				</div>
				<div class="row-sep"></div>
				<div class="feature">
					<h3 class="feature-title">Blog Slider</h3>
					<p class="feature-content">This widget lets you easily add eye catching post sliders anywhere on your website. It is typically used at the top of pages and goes great in conjunction with the Blog Widget.</p>
					<p class="feature-links"><a href="http://widgets.wpinked.com/blog-slider-widget/" target="_blank">Demo</a></p>
				</div>
				<div class="feature">
					<h3 class="feature-title">Charts</h3>
					<p class="feature-content">This widget allows you to visualise your data in a number of different ways. Choose from 6 chart types, each of them animated, fully customisable and engaging.</p>
					<p class="feature-links"><a href="http://widgets.wpinked.com/chart-widget/" target="_blank">Demo</a></p>
				</div>
				<div class="feature">
					<h3 class="feature-title">Person Slider</h3>
					<p class="feature-content">This widget is perfectly suited to showcase your personnel on About Me or Team Member pages, where you would like to highlight their bio. It brings together text, imagery, social media links and a smooth slider in a cohesive manner.</p>
					<p class="feature-links"><a href="http://widgets.wpinked.com/person-slider-widget/" target="_blank">Demo</a></p>
				</div>
				<div class="row-sep"></div>
				<div class="feature">
					<h3 class="feature-title">Testimonial Slider</h3>
					<p class="feature-content">This widget gives you is a great way to encourage trust from your visitors by displaying quotes from your customers. It fits snuggly in your Sales, Pricing or Home pages.</p>
					<p class="feature-links"><a href="http://widgets.wpinked.com/testimonial-slider-widget/" target="_blank">Demo</a></p>
				</div>
				<div class="feature">
					<h3 class="feature-title">Flip Box</h3>
					<p class="feature-content">The Flip Box widget is a simple and elegant combination of text and media. This widget is a fantastic way to highlight small bits of information, such as skills, features or services you offer.</p>
					<p class="feature-links"><a href="http://widgets.wpinked.com/flip-box-widget/" target="_blank">Demo</a></p>
				</div>
				<div class="feature">
					<h3 class="feature-title">Gallery</h3>
					<p class="feature-content">Present your image collections in a visually rich masonry layout. It is complemented well with the lightbox feature.</p>
					<p class="feature-links"><a href="http://widgets.wpinked.com/gallery-widget/" target="_blank">Demo</a></p>
				</div>
				<div class="row-sep"></div>
				<div class="feature">
					<h3 class="feature-title">Slider Pro</h3>
					<p class="feature-content">This widget gives you is a great way to encourage trust from your visitors by displaying quotes from your customers. It fits snuggly in your Sales, Pricing or Home pages.</p>
					<p class="feature-links"><a href="http://widgets.wpinked.com/slider-pro-widget/" target="_blank">Demo</a></p>
				</div>
				<div class="feature">
					<h3 class="feature-title">Animations</h3>
					<p class="feature-content">This feature gives you an easy way to animate widgets and rows. Choose from 18 different animation effects.</p>
					<p class="feature-links"><a href="http://widgets.wpinked.com/the-animation-feature/" target="_blank">Demo</a</p>
				</div>
				<div class="feature">
					<h3 class="feature-title">Page Builder</h3>
					<p class="feature-content">This feature enables the use of the page builder in tabs, accordions and filter accordions.</p>
				</div>
				<div class="row-sep"></div>
				<div class="feature">
					<h3 class="feature-title">Custom Fonts</h3>
					<p class="feature-content">This feature lets you apply custom fonts to important elements in your widgets. Choose from hundreds of fonts.</p>
				</div>
				<div class="feature">
					<h3 class="feature-title">Image Lightbox</h3>
					<p class="feature-content">Let users have a closer look at photos and images with the image and gallery widgets.</p>
				</div>
				<div class="row-sep"></div>
				<div class="buy-pro">
					<a href="https://wpinked.com/downloads/widgets-for-siteorigin-pro/" target="_blank">
						<img src="<?php echo plugin_dir_url(__FILE__); ?>img/get-pro-now.jpg">
					</a>
				</div>
			</div>

		</div>

	</div>

<?php
}

function wpinked_so_admin_craft_content() { ?>

	<div class="iw-admin-page craft-teaser">

		<div class="iw-page-header">
			<h1 class="iw-admin-welcome">Explore Craft Theme</h1>
			<h2 class="iw-admin-tagline">Creative Lightweight Multipurpose WordPress Theme</h2>
			<p class="iw-admin-links">
				 |
				<a href="https://wpinked.com/forums/" target="_blank">Support Forum</a> |
				Refer : <a href="https://wpinked.com/craft-theme-docs/" target="_blank">Documentation</a> &bull; <a href="http://widgets.wpinked.com/" target="_blank">Demo</a>
			</p>
		</div>

		<div class="iw-page-content">

		</div>

	</div>

<?php
}
