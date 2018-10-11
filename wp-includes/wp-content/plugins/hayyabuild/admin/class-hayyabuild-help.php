<?php
/**
 *
 * The admin-list functionality of the plugin.
 *
 * @since      	1.0.0
 * @package    	hayyabuild
 * @subpackage 	hayyabuild/admin
 * @author     	zintaThemes <>
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }


class HayyaHelp extends HayyaAdmin {

    /**
     * Define the view for forntend.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @access		public
     * @since		1.0.0
     * @var			unown
     */
    public function __construct() {
    	return $this->Help();
    }

	/**
	 *
     * @access		public
     * @since		1.0.0
     * @var			unown
	 */
    protected function Help() { ?>
    	<div id="hayyabuild" class="wrap">
    	<!-- <div class="">
    	<?php // wp_editor( 'Hi,its content' , 'desired_id_of_textarea', '' ); ?>
    	    </div> -->
    	    <div class="view_title">
    	        <h1 style="color: #000000;"><?php echo HAYYAB_NAME;?> - <?php echo esc_html_e( 'Version', HAYYAB_BASENAME );?> - <?php echo HAYYAB_VERSION;?></h1>
    	    </div>
    	    <hr>
    	    <ul class="collapsible" data-collapsible="accordion">
    	        <li>
    	            <div class="collapsible-header active"><i class="fa fa-send"></i><?php esc_html_e( 'Contact Us', HAYYAB_BASENAME);?></div>
    	            <div class="collapsible-body valign-wrapper " style="padding-top: 10px;">
    	                <div class="row valign">
    	                    <div class="col s8">
    	                        <section id="top">
    	                            <div>
    	                                <b>First of all, thank you for using HayyaBuild.<br/>
                                        If we can be of further assistance please contact us at </b>
                                        <a target="_blank" href="http://codecanyon.net/item/hayyabuild-responsive-header-and-footer-builder/15315666/comments">CodeCanyon</a><br />
                                        <b>If you like it, Please don't forget to write a good review.</b>
                                        <a target="_blank" class="" href="http://codecanyon.net/downloads" style="color: #FFC800;text-decoration: none;">
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i class="fa fa-star"></i>
                                            <i style="color: blue;">Click here to write your reivew.</i>
                                        </a>.
    	                            </div>
    	                            <div>
    	                                <div style="padding-top:120px;">
    	                                    Need help ? You want to report a bug ?<br />
    	                                    You can find us on:<br>
    	                                </div>
    	                            </div>
    	                        </section>
    	                    </div>
    	                    <div class="col s4 right-align">
    	                        <img src="<?php echo site_url().'/wp-content/plugins/'.HAYYAB_BASENAME.'/admin/assets/images/logo.png?v='.HAYYAB_VERSION; ?>" />
    	                    </div>
    	                </div>
    	                <div align="center" style="padding-bottom: 10px;">
    	                    <a target="_blank" class="waves-effect waves-darck hayya_btn" href="http://codecanyon.net/item/hayyabuild-responsive-header-and-footer-builder/15315666">Plugin Page</a>
    	                    <a target="_blank" class="waves-effect waves-darck hayya_btn" href="http://hayyabuild.zintathemes.com">Plugin Website</a>
    	                </div>
    	            </div>
    	        </li>
    	        <li>
    	            <div class="collapsible-header"><i class="fa fa-exclamation-circle"></i><?php esc_html_e('Help');?></div>
    	            <div class="collapsible-body valign-wrapper" style="padding-top: 10px;">
    	                <div class="row valign hayyabuild_help" style="color: #555555;">
    	                    <div class="col s12">
    	                        <section>
    	                            <h3>Setup Your Template</h3>
    	                            <div>
    	                                You still have one setp to get HayyaBuild worked
    	                                <ul>
    	                                    <li>Make a backup from you tempalate, Copy your template directory to another place.</li>
    	                                    <li>
    	                                        Now open your header.php and footer.php file which is located in the Appearance >> Editor
    	                                    </li>
    	                                    <li>
    	                                        From template files list <i>"on the right"</i> choose Theme Header (header.php)
    	                                    </li>
    	                                    <li>
    	                                        Now replace the header tag <br />
    	                                        <code>&lt;header&gt; .... &lt;/header&gt; <b>OR</b> &lt;div id="header"&gt; .... &lt;/div&gt; <b>or anything else</b></code><br />
    	                                        with this code<br />
    	                                        <code>&lt;?php hayya_run('header');?&gt;</code><br />
    	                                        and click on Update
    	                                    </li>
    	                                    <li>
    	                                        From Editor and template files list <i>"on the right"</i> choose Theme Footer (footer.php)
    	                                    </li>
    	                                    <li>
    	                                        Replace the footer tag<br />
    	                                        <code>&lt;footer&gt; .... &lt;/footer&gt; <b>OR</b> &lt;div id="footer"&gt; .... &lt;/div&gt; <b>or anything else</b></code><br />
    	                                        With this code<br />
    	                                        <code>&lt;?php hayya_run('footer');?&gt;</code><br />
    	                                    </li>
    	                                </ul>
    	                                <blockquote>
    	                                   You can edit header.php and footer.php with any text editor from your disktop.
    	                                </blockquote>
    	                            </div>
    	                        </section>
    	                    </div>
    	                </div>
    	            </div>
    	        </li>
    	    </ul>
    	</div><?php
    }

} // End Class
