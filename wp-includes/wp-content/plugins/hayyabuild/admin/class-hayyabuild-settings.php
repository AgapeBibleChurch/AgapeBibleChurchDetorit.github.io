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


class HayyaSettings extends HayyaAdmin {

	/**
	 *
	 * @var array
	 */
	private static $elements_list = array();

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
    	add_action('admin_init', array('HayyaHelper', '__notices'));

    	$hayya_elements = new HayyaModules( 'showall' );
    	self::$elements_list =  $hayya_elements->elements_list();
    	if ( HayyaHelper::_post('options') ) $this->save_settings();
    	$this->Settings();
    	// HayyaBuild::get_loader()->add_action('admin_init', $this, 'Settings');
    }

    /**
     *
     */
    private function save_settings() {
		$nonce = HayyaHelper::_post('_hbnonce');
		if ( ! empty($nonce) && wp_verify_nonce($nonce, 'settings_form_nonce') ) {
	    	$post_libraries	= HayyaHelper::_post('libraries') ;
	    	$post_elements	= HayyaHelper::_post('elements') ;
	    	$post_csseditor		= HayyaHelper::_post('csseditor');
	    	foreach (self::$elements_list as $path => $elements_list) {
		    	foreach ( $elements_list as $module ) {
		    		$moduleBase = $module['base'];
		    		if ( $post_elements && !array_key_exists($moduleBase, $post_elements) ) $post_elements[$moduleBase] = 'off';
		    	}
	    	}
	    	$settings = array(
	    			'libraries' => $post_libraries,
	    			'elements' 	=> $post_elements,
	    			'csseditor'	=> $post_csseditor
	    	);
	    	if ( update_option('hayyabuild_settings', $settings) )
	    		HayyaHelper::__notices( __('The database has been successfully updated', HAYYAB_BASENAME), 'success' );
	    	else
	    		HayyaHelper::__notices( __('EROR08: Someting happen, Can’t update database.', HAYYAB_BASENAME), 'error' );
		}
    }

    /**
     *
     * @param unknown $list
     */
    public static function Settings($list = null) {
    	if ( function_exists('get_option') ) {
    		$setting = get_option('hayyabuild_settings');
    		if ( isset( $setting ) && is_array( $setting )) {
    			foreach( $setting as $key => $value ) $$key = $value;
    		}
    	}

    	$csseditor = stripslashes($csseditor);
    	?>
    	<div id="hayyabuild" class="wrap">
			<form method="post" action="" class="form-inline" role="form" id="settings_form">
    			<div class="hb-main_settings">
					<?php HayyaView::nav_bar($main = false); ?>
				    <hr>

			        <?php wp_nonce_field( 'settings_form_nonce', '_hbnonce' ); ?>

			        <ul class="collapsible" data-collapsible="accordion">
			            <li>
			                <div class="active collapsible-header">
			                    <i class="fa fa-adjust"></i><?php esc_html_e( 'Active/Deactivate Modules', HAYYAB_BASENAME);?>
			                </div>
			                <div class="collapsible-body valign-wrapper" style="padding-top: 10px;">
			                    <div class="row">
			                        <div class="col s12">
			                            <blockquote style="font-size: 12px;">
											<strong>
												<?php esc_html_e( 'Please Don’t deactivate anything that you don’t know.', HAYYAB_BASENAME);?>
											</strong>
											<br/>
			                                <?php esc_html_e( 'You can disable one of these modules if you have a problem in builder page.', HAYYAB_BASENAME);?>
			                            </blockquote>
			                        </div>
			                    </div>
			                    <hr/>
			                    <div class="row">
		                    		<div class="col s3" style="border: 1px solid #fff;"></div>
			                    	<div class="col s6">
		                    			<div class="row" style="border-bottom: 2px solid #efefef;padding-bottom: 10px;">
					                        <div class="col s6 input-field" style="text-align: right; border-right: 1px solid #00808E;padding: 5px;">
					                            <?php esc_html_e( 'All Modules', HAYYAB_BASENAME ); ?>
					                        </div>
					                        <div class="col s6" style="text-align: left;">
					                            <div class="switch">
					                                <label>
					                                  <?php esc_html_e( 'OFF', HAYYAB_BASENAME );?>
													  <input type="checkbox" id="settings_all_modules" name="all_modules"/><span class="lever"></span>
													  <?php esc_html_e( 'ON', HAYYAB_BASENAME );?>
					                                </label>
					                            </div>
					                        </div>
		                    			</div>
			                    	</div>
		                    		<div class="col s3"></div>
			                        <div style="clear: both;"></div>
				                    <?php foreach ( self::$elements_list as $path => $element ) : ?>
				                    	<?php foreach ( $element as $key => $val ) : ?>
				                        <div class="col s3" style="color:#00808E;text-align: right;height: 40px !important;border-right: 1px solid #00808E;margin-bottom: 5px;">
				                            <?php echo esc_html( $val['name'] ); ?>
				                            <i class="<?php echo esc_attr( $val['icon'] )?>" style="font-size: 25px;display: inline-block;width: 40px;text-align: center;background: #2CD0E1;border-radius: 2px;color: #00808E;margin-left: 5px;margin-top: 2px;"></i>
				                        </div>
				                        <div class="col s3" style="margin-top: 5px;height: 40px !important;">
				                            <?php $checked = ( isset($val['base']) && isset($elements[$val['base']]) && $elements[$val['base']] == 'on' ) ? ' checked' : '';?>
				                            <div class="switch" style="margin-top: 5px;">
				                                <label>
				                                  <?php esc_html_e( 'OFF', HAYYAB_BASENAME );?> <input class="setting_modules_list" type="checkbox" name="elements[<?php echo esc_attr( $val['base'] );?>]" <?php echo esc_attr( $checked ); ?>/><span class="lever"></span> <?php esc_html_e( 'ON', HAYYAB_BASENAME );?>
				                                </label>
				                            </div>
				                        </div>
				                        <?php endforeach;?>
				                    <?php endforeach; ?>
			                    </div>
			                </div>
			            </li>
			            <!-- </ul>

			            <ul class="collapsible" data-collapsible="accordion"> -->
			            <li>
			                <div class="collapsible-header">
			                    <i class="fa fa-file-text"></i><?php esc_html_e( 'CSS Editor', HAYYAB_BASENAME);?>
			                </div>
			                <div class="collapsible-body valign-wrapper" style="padding-top: 10px;">
			                    <div class="row">
			                        <div class="col s12">
			                            <blockquote style="font-size: 12px;">
			                                <?php esc_html_e( 'This CSS code will appear in all pages.', HAYYAB_BASENAME);?>
			                            </blockquote>
			                        </div>
			                    </div>
			                    <hr/>
			                    <div class="row">
			                        <div class="col s12">
			                        	<input type="hidden" name="csseditorval" id="csscodeval" value="<?php echo esc_textarea($csseditor);?>">
			                            <textarea rows="" id="csscode" name="csseditor" cols="" style="width: 100%;"><?php echo esc_textarea($csseditor);?></textarea>
			                            <div id="csscodediv"></div>
			                        </div>
			                    </div>
			                </div>
			            </li>
			        </ul>


			        <input type="hidden" id="options" value="options" name="options">
				</div>

				<div style="margin: 20px 0px" class="hb_buttons">
					<div class="row">
						<div class="col s10">
							<button class="waves-effect waves-darck hayya_btn" type="submit" name="save" value="save">
								<i class="fa fa-save"></i>
								<?php esc_html_e('Save', HAYYAB_BASENAME  ); ?>
							</button>
						</div>
					</div>
				</div>
			</form>
		</div>
    	<?php
    }

} // End Class
