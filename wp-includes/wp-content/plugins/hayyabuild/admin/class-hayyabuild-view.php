<?php
/**
 *
 * The admin-view functionality of the plugin.
 *
 * @since      	1.0.0
 * @package    	hayyabuild
 * @subpackage 	hayyabuild/admin
 * @author     	zintaThemes <>
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }


class HayyaView {

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
    public function __construct() {}


    /**
     *
     * @param unknown $message
     */
    public static function help_tip($message = NULL) {
    	if ( !empty($message) ) {
    		echo '<span class="help-tip" style="margin-top: 15px;"><i class="hb_helper">'.__($message, HAYYAB_BASENAME).'</i></span>';
    	} else return false;
    }

    /**
     *
     * Show empty list message
     *
     * @param unknown $showButtns
     * @param unknown $mainList
     */
    public static function empty_list( $showButtns = NULL, $mainList = NULL ) { ?>
    	<div class="hayya_card-panel">
			<span>
				<?php
				esc_html_e('This list is empty!',HAYYAB_BASENAME );
				if ( $mainList ) {
					echo '<br/>';
					esc_html_e('Your Headers and Footers will appear here',HAYYAB_BASENAME );
				}
				if ( $showButtns) :?>
					<br/>
					<a href="admin.php?page=hayyabuild_addh" class="waves-effect waves-light hayya_btn"><i class="fa fa-plus"></i> <?php esc_html_e('New Header',HAYYAB_BASENAME );?></a>
					<a href="admin.php?page=hayyabuild_addc" class="waves-effect waves-light hayya_btn"><i class="fa fa-plus"></i> <?php esc_html_e('New Content',HAYYAB_BASENAME );?></a>
					<a href="admin.php?page=hayyabuild_addf" class="waves-effect waves-light hayya_btn"><i class="fa fa-plus"></i> <?php esc_html_e('New Footer',HAYYAB_BASENAME );?></a>
					<a href="admin.php?page=hayyabuild&amp;section=templates" class="waves-effect waves-light hayya_btn"><i class="fa fa-th-large"></i>  <?php esc_html_e('Templates',HAYYAB_BASENAME );?></a>
				<?php endif;?>
			</span>
		</div> <?php
    }

    /**
     *
     * Show NavBar
     *
     */
    public static function nav_bar ( $main = NULL ) {
        $tpl = HAYYAB_PATH . 'includes' . DIRECTORY_SEPARATOR . 'class-hayyabuild-templates.php';
        $direction = 'left';
        if ( is_rtl() ) {
            $direction = 'right';
        }
        ?>

	    <div class="main_conf" style="padding-bottom: 20px;">
	        <img src="<?php echo esc_url( HAYYAB_URL.'admin/assets/images/main_logo.png?v='.HAYYAB_VERSION );?>" style="width: 250px;height: auto;" />
        	<?php if ($main) : ?>
            <a class="right top dropdown-button cyan-text hayya_btn-flat" href="#" data-activates="dropdown1" style="padding-bottom: 2px;border: nonea">
            	<?php esc_html_e('Add New', HAYYAB_BASENAME); ?> <i class="fa fa-plus right"></i>
            </a>
            <?php else : ?>
            <a href="admin.php?page=hayyabuild" class="right top dropdown-button cyan-text text-darken-2 hayya_btn-flat"  style="padding-bottom: 2px;border: none;">
                <i class="fa fa-angle-<?php echo esc_attr( $direction );?> <?php echo esc_attr( $direction );?>"></i> <?php esc_html_e('Back', HAYYAB_BASENAME); ?>
            </a>
            <?php endif; ?>
	   </div>
	    <?php if ( $main ) :?>
	    <ul id="dropdown1" class="dropdown-content" style="width: 250px;">
	        <li class="active">
	            <a href="admin.php?page=hayyabuild_addh">
	                <?php esc_html_e('New Header', HAYYAB_BASENAME); ?>
	            </a>
	        </li>
	        <li class="active">
	            <a href="admin.php?page=hayyabuild_addc">
	                <?php esc_html_e('New Content', HAYYAB_BASENAME); ?>
	            </a>
	        </li>
	        <li class="active">
	            <a href="admin.php?page=hayyabuild_addf">
	                <?php esc_html_e('New Footer', HAYYAB_BASENAME); ?>
	            </a>
	        </li>
	        <li class="active">
            <?php
            if ( file_exists( $tpl ) && is_dir( HAYYAB_PATH . 'includes' . DIRECTORY_SEPARATOR . 'data' ) ) :?>
                <a href="admin.php?page=hayyabuild&amp;section=templates">
                    <?php esc_html_e('New from Template', HAYYAB_BASENAME); ?>
                </a>
            <?php else :?>
                <a target="_blank" href="http://codecanyon.net/item/hayyabuild-responsive-header-and-footer-builder/15315666/">
                    <?php esc_html_e('New from Template', HAYYAB_BASENAME); ?> <span class="badge pro">PRO</span>
                </a>
            <?php endif;?>
	        </li>
	    </ul>
	    <?php endif;
    }

    /**
     *
     * Show emprt form
     *
     */
    public static function import_form () { ?>
        <ul class="collapsible" data-collapsible="accordion">
		    <li>
				<div class="collapsible-header">
					<i class="fa fa-mail-forward"></i>
					<?php esc_html_e( 'Import', HAYYAB_BASENAME);?>
				</div>
				<div class="collapsible-body" style="padding: 5px 15px;">
                <?php
                $file = HAYYAB_PATH . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'class-hayyabuild-import.php';
                if ( file_exists( $file ) && ! HayyaHelper::check() ) {
                    require_once $file;
                    hayyaBuildEmport::import_form();
                } else if ( method_exists( 'HayyaView', 'hayya_lite' ) ) {
                    HayyaView::hayya_lite('pro-only');
                }
                ?>
                </div>
            </li>
        </ul>
        <?php
	}

    /**
     * @param unknown
     */
    public static function add_modal($elements_group = NULL) {
    	if ($elements_group && is_array($elements_group)) :
    	?>
    	<script type="text/html" id="hb_elements-modal">
    		<div id="hb_modal" class="modal fade hb_modal" role="dialog">
    		    <div class="modal-dialog modal-lg">
    				<div class="modal-content">
	    				<div class="modal-header">
    						<a href="#" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-angle-up"></i></a>
    						<h4 class="modal-title"><?php esc_html_e( 'Modules list', HAYYAB_BASENAME ); ?></h4>
							<ul class="nav nav-tabs">
								<li class="active"><a href="#hb_elements-tab-all" data-toggle="tab"><?php esc_html_e( 'All', HAYYAB_BASENAME );?></a></li>
								<?php foreach ( $elements_group as $key => $value ) : ?>
								<li><a href="#hb_elements-tab-<?php echo $key;?>" data-toggle="tab"><?php echo esc_html( $key );?></a></li>
								<?php endforeach;?>
							</ul>
	    				</div>

    					<div class="modal-body" style="overflow-y: scroll;">
    						<div id="hb_elements-tabs">
    							<div class="tab-content">
	    							<div id="hb_elements-tab-all" class="tab-pane clearfix active">
    									<?php foreach ( $elements_group as $key => $value ) : ?>
    									<?php foreach ( $value as $element ) : ?>
    									<div class="well text-center text-overflow" data-hb_element="<?php echo esc_attr( $element['base'] );?>">
											<div class="well-content">
    											<i class="<?php echo esc_attr( $element['icon'] );?>"></i>
    											<div class="name"><?php echo esc_html( $element['name'] );?></div>
    											<div class="text-muted small"><?php echo esc_html( $element['description'] );?></div>
											</div>
    									</div>
	    								<?php endforeach;?>
    									<?php endforeach;?>
    									<!-- <div style="clear: both;"></div>-->
                                        <?php
                                        if ( method_exists( 'HayyaView', 'hayya_lite' ) && HayyaHelper::check() ) {
                                            HayyaView::hayya_lite('modules');
                                        }
                                        ?>
    									<?php $key = $value = $element = null;?>
    								</div>
    								<?php foreach ( $elements_group as $key => $value ) : ?>
   	 								<div id="hb_elements-tab-<?php echo esc_html( $key );?>" class="tab-pane clearfix">
   	 									<?php foreach ( $value as $element ) : ?>
   	 									<div class="well text-center text-overflow" data-hb_element="<?php echo esc_attr( $element['base'] );?>">
											<div class="well-content">
    											<i class="<?php echo esc_attr( $element['icon'] );?>"></i>
    											<div class="name"><?php echo esc_html( $element['name'] );?></div>
    											<div class="text-muted small"><?php echo esc_html( $element['description'] );?></div>
											</div>
    									</div>
    									<?php endforeach;?>
    								</div>
    								<?php endforeach;?>
    							</div>
    						</div>
    					</div>
    				</div>
	    	    </div>
    		</div>
	    </script>
    	<?php
    	endif;
    }

    /**
     * @param unknown
     */
    public static function editor_modal() {
    	?>
    	<script type="text/html" id="hb_edit_modal">
    		<div id="hb_modal" class="modal fade hb_modal" role="dialog">
    			<div class="modal-dialog modal-lg">
	    			<div class="modal-content">
    					<div class="modal-header">
    						<a href="#" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-angle-up"></i></a>
	    					<h4 class="modal-title"><%= hb.elementName %></h4>
							<ul class="nav nav-tabs">
							    <%= hb.tab_menu %>
							</ul>
    					</div>
	    				<div class="modal-body" style="overflow-y: scroll;">
    						<div id="hb_elements-tabs">
    							<div class="tab-content" style="padding: 10px;">
									<div id="hb_editor_tabs"></div>
								</div>
							</div>
    					</div>
	    				<div class="modal-footer">
    						<button type="button" class="hayya_btn" data-dismiss="modal">Close</button>
    						<button type="button" class="save hayya_btn">Save changes</button>
	    				</div>
    				</div>
	    		</div>
    		</div>
	    </script>
    	<?php
    }


    /**
     * editor modal for builder page
     *
     * @param unknown
     */
    public static function classes_modal() {
    	?>
    	<script type="text/html" id="hb_classes">
			<div id="classesslist" class="classeslist">
				<fieldset>
    				<legend><i class="fa fa-search-plus"></i> <?php esc_html_e( 'visibility Options', HAYYAB_BASENAME ); ?></legend>
    				<div class="row">
    					<div class="col s6">
							<input type="checkbox" id="visible-xs-block" class="hayyaClassesList" value="visible-xs-block" name="visible-xs-block" <%= classes.Value['visible-xs-block'] %>/>
    						<label for="visible-xs-block"><?php esc_html_e( 'Visible on extra small devices, phones < 768px', HAYYAB_BASENAME ); ?></label>
    					</div>
    					<div class="col s6">
							<input type="checkbox" id="visible-md-block" class="hayyaClassesList" value="visible-md-block" name="visible-md-block" <%= classes.Value['visible-md-block'] %>/>
    						<label for="visible-md-block"><?php esc_html_e( 'Visible on medium devices, desktops ≥ 992px', HAYYAB_BASENAME ); ?></label>
    					</div>
    				</div>
    				<div class="row">
    					<div class="col s6">
							<input type="checkbox" id="visible-sm-block" class="hayyaClassesList" value="visible-sm-block" name="visible-sm-block" <%= classes.Value['visible-sm-block'] %>/>
    						<label for="visible-sm-block"><?php esc_html_e( 'Visible on small devices, tablets ≥ 768px ', HAYYAB_BASENAME ); ?></label>
    					</div>
    					<div class="col s6">
							<input type="checkbox" id="visible-lg-block" class="hayyaClassesList" value="visible-lg-block" name="visible-lg-block" <%= classes.Value['visible-lg-block'] %>/>
    						<label for="visible-lg-block"><?php esc_html_e( 'Visible on large devices, desktops ≥ 1200px', HAYYAB_BASENAME ); ?></label>
    					</div>
    				</div>
    				<div class="row">
    					<div class="col s6">
							<input type="checkbox" id="hidden-xs" class="hayyaClassesList" value="hidden-xs" name="hidden-xs" <%= classes.Value['hidden-xs'] %>/>
    						<label for="hidden-xs"><?php esc_html_e( 'Hidden on extra small devices, phones < 768px', HAYYAB_BASENAME ); ?></label>
    					</div>
    					<div class="col s6">
							<input type="checkbox" id="hidden-md" class="hayyaClassesList" value="hidden-md" name="hidden-md" <%= classes.Value['hidden-md'] %>/>
    						<label for="hidden-md"><?php esc_html_e( 'Hidden on medium devices, desktops ≥ 992px', HAYYAB_BASENAME ); ?></label>
    					</div>
    				</div>
    				<div class="row">
    					<div class="col s6">
							<input type="checkbox" id="hidden-sm" class="hayyaClassesList" value="hidden-sm" name="hidden-sm" <%= classes.Value['hidden-sm'] %>/>
    						<label for="hidden-sm"><?php esc_html_e( 'Hidden on small devices, tablets ≥ 768px', HAYYAB_BASENAME ); ?></label>
    					</div>
    					<div class="col s6">
							<input type="checkbox" id="hidden-lg" class="hayyaClassesList" value="hidden-lg" name="hidden-lg" <%= classes.Value['hidden-lg'] %>/>
    						<label for="hidden-lg"><?php esc_html_e( 'Hidden on large devices, desktops ≥ 1200px', HAYYAB_BASENAME ); ?></label>
    					</div>
    				</div>
	    		</fieldset>
    			<fieldset>
	    			<legend><i class="fa fa-angle-double-down"></i> <?php esc_html_e( 'Scroll Effects', HAYYAB_BASENAME ); ?></legend>
		   			<diV class="row" style="padding:0;margin:0;">
		   				<div class="col s3" style="padding-top:5px;">
				    		<?php esc_html_e( 'Select Effects Duration', HAYYAB_BASENAME ); ?>
			    		</div>
						<div class="col s4 range-field-container">
							<span class="range-field">
						   		<input class="hayyaClassesList" type="range" id="hb_duration" min="0" max="10" name="hb_duration" value="<%= classes.Value['hb_duration'] %>"/>
							</span>
						</div>
                        <div class="col s4">
                            <input type="checkbox" id="hb_unreverse_scroll" data-scrollefect="1" class="hayyaClassesList" value="hb_unreverse_scroll" name="hb_unreverse_scroll" <%= classes.Value['hb_unreverse_scroll'] %>/>
    						<label for="hb_unreverse_scroll"><?php esc_html_e( 'Unreverse Effects', HAYYAB_BASENAME ); ?></label>
						</div>
					</div>
                    <hr/>
                    <div class="row" style="padding:0;margin:0;">
                        <div class="col s3" style="padding-top:10px;">
                            <?php esc_html_e( 'Select Effects Easing', HAYYAB_BASENAME ); ?>
                        </div>
                        <div class="col s5">
                            <select id="hb_scroll_easing" name="hb_scroll_easing" class="hayyaClassesList select_modal select_material" data-value="<%= classes.Value['hb_scroll_easing'] %>">
                                <option value=""><?php esc_html_e( 'Select Easing', HAYYAB_BASENAME ); ?></option>
                                <option value="easeIn">EaseIn</option>
                                <option value="easeInOut">EaseInOut</option>
                                <option value="easeOut">EaseOut</option>
                            </select>
                        </div>
                        <div class="col s4">
                            <select id="hb_scroll_ease_effect" name="hb_scroll_ease_effect" class="hayyaClassesList select_modal select_material" data-value="<%= classes.Value['hb_scroll_ease_effect'] %>">
                                <option value=""><?php esc_html_e( 'Select Mode', HAYYAB_BASENAME ); ?></option>
                                <option value="Power0">Power 0</option>
                                <option value="Power1">Power 1</option>
                                <option value="Power2">Power 2</option>
                                <option value="Power3">Power 3</option>
                                <option value="Power4">Power 4</option>
                                <option value="Back">Back</option>
                                <option value="Elastic">Elastic</option>
                                <option value="Bounce">Bounce</option>
                                <option value="SlowMo">SlowMo</option>
                                <option value="Stepped">Stepped</option>
                                <option value="Circ">Circ</option>
                                <option value="Expo">Expo</option>
                                <option value="Sine">Sine</option>
                            </select>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col s4" style="padding:10px;">
                            <?php esc_html_e( 'Select Effects', HAYYAB_BASENAME ); ?>
                        </div>
                    </div>
	    			<div class="row">
    					<div class="col s6">
							<input type="checkbox" id="hb_scale_out" data-scrollefect="1" class="hayyaClassesList" value="hb_scale_out" name="hb_scale_out" <%= classes.Value['hb_scale_out'] %>/>
    						<label for="hb_scale_out"><?php esc_html_e( 'Scale Out', HAYYAB_BASENAME ); ?></label>
    					</div>
    					<div class="col s6">
							<input type="checkbox" id="hb_scale_in" data-scrollefect="1" class="hayyaClassesList" value="hb_scale_in" name="hb_scale_in" <%= classes.Value['hb_scale_in'] %>/>
    						<label for="hb_scale_in"><?php esc_html_e( 'Scale In', HAYYAB_BASENAME ); ?></label>
    					</div>
    				</div>
	    			<div class="row">
    					<div class="col s6">
							<input type="checkbox" id="hb_slide_left" data-scrollefect="1" class="hayyaClassesList" value="hb_slide_left" name="hb_slide_left" <%= classes.Value['hb_slide_left'] %>/>
    						<label for="hb_slide_left"><?php esc_html_e( 'Slide Left', HAYYAB_BASENAME ); ?></label>
    					</div>
    					<div class="col s6">
							<input type="checkbox" id="hb_slide_right" data-scrollefect="1" class="hayyaClassesList" value="hb_slide_right" name="hb_slide_right" <%= classes.Value['hb_slide_right'] %>/>
    						<label for="hb_slide_right"><?php esc_html_e( 'Slide Right', HAYYAB_BASENAME ); ?></label>
    					</div>
    				</div>
	    			<div class="row">
    					<div class="col s6">
							<input type="checkbox" id="hb_slide_up" data-scrollefect="1" class="hayyaClassesList" value="hb_slide_up" name="hb_slide_up" <%= classes.Value['hb_slide_up'] %>/>
    						<label for="hb_slide_up"><?php esc_html_e( 'Slide UP', HAYYAB_BASENAME ); ?></label>
    					</div>
    					<div class="col s6">
							<input type="checkbox" id="hb_slide_down" data-scrollefect="1" class="hayyaClassesList" value="hb_slide_down" name="hb_slide_down" <%= classes.Value['hb_slide_down'] %>/>
    						<label for="hb_slide_down"><?php esc_html_e( 'Slide DOWN', HAYYAB_BASENAME ); ?></label>
    					</div>
    				</div>
	    			<div class="row">
    					<div class="col s6">
							<input type="checkbox" id="hb_rotation_left" data-scrollefect="1" class="hayyaClassesList" value="hb_rotation_left" name="hb_rotation_left" <%= classes.Value['hb_rotation_left'] %>/>
    						<label for="hb_rotation_left"><?php esc_html_e( 'Left Rotation', HAYYAB_BASENAME ); ?></label>
    					</div>
    					<div class="col s6">
							<input type="checkbox" id="hb_rotation_right" data-scrollefect="1" class="hayyaClassesList" value="hb_rotation_right" name="hb_rotation_right" <%= classes.Value['hb_rotation_right'] %>/>
    						<label for="hb_rotation_right"><?php esc_html_e( 'Right Rotation', HAYYAB_BASENAME ); ?></label>
    					</div>
    				</div>
	    			<div class="row">
    					<div class="col s6">
							<input type="checkbox" id="hb_fade_in" data-scrollefect="1" class="hayyaClassesList" value="hb_fade_in" name="hb_fade_in" <%= classes.Value['hb_fade_in'] %>/>
    						<label for="hb_fade_in"><?php esc_html_e( 'Fade IN', HAYYAB_BASENAME ); ?></label>
    					</div>
    					<div class="col s6">
							<input type="checkbox" id="hb_fade_out" data-scrollefect="1" class="hayyaClassesList" value="hb_fade_out" name="hb_fade_out" <%= classes.Value['hb_fade_out'] %>/>
    						<label for="hb_fade_out"><?php esc_html_e( 'Fade OUT', HAYYAB_BASENAME ); ?></label>
    					</div>
    				</div>
	    			<div class="row">
    					<div class="col s6">
							<input type="checkbox" id="hb_parallax_up" data-scrollefect="1" class="hayyaClassesList" value="hb_parallax_up" name="hb_parallax_up" <%= classes.Value['hb_parallax_up'] %>/>
    						<label for="hb_parallax_up"><?php esc_html_e( 'Parallax Background (UP)', HAYYAB_BASENAME ); ?></label>
    					</div>
    					<div class="col s6">
							<input type="checkbox" id="hb_parallax_down" data-scrollefect="1" class="hayyaClassesList" value="hb_parallax_down" name="hb_parallax_down" <%= classes.Value['hb_parallax_down'] %>/>
    						<label for="hb_parallax_down"><?php esc_html_e( 'Parallax Background (DOWN)', HAYYAB_BASENAME ); ?></label>
    					</div>
    				</div>
	    		</fieldset>
    			<fieldset>
    				<legend><i class="fa fa-gears"></i> <?php esc_html_e( 'Other Options', HAYYAB_BASENAME ); ?></legend>
    				<div class="row">
    					<div class="col s6">
							<input type="checkbox" id="bg-primary" class="hayyaClassesList" value="bg-primary" name="bg-primary" <%= classes.Value['bg-primary'] %>/>
    						<label for="bg-primary"><?php esc_html_e( 'Background primary style', HAYYAB_BASENAME ); ?></label>
    					</div>
    					<div class="col s6">
							<input type="checkbox" id="text-primary" class="hayyaClassesList" value="text-primary" name="text-primary" <%= classes.Value['text-primary'] %>/>
    						<label for="text-primary"><?php esc_html_e( 'Text primary style', HAYYAB_BASENAME ); ?></label>
    					</div>
    				</div>
    				<div class="row">
    					<div class="col s6">
							<input type="checkbox" id="bg-success" class="hayyaClassesList" value="bg-success" name="bg-success" <%= classes.Value['bg-success'] %>/>
    						<label for="bg-success"><?php esc_html_e( 'Background success style', HAYYAB_BASENAME ); ?></label>
    					</div>
    					<div class="col s6">
							<input type="checkbox" id="text-success" class="hayyaClassesList" value="text-success" name="text-success" <%= classes.Value['text-success'] %>/>
    						<label for="text-success"><?php esc_html_e( 'Text success style', HAYYAB_BASENAME ); ?></label>
    					</div>
    				</div>
    				<div class="row">
    					<div class="col s6">
							<input type="checkbox" id="bg-default" class="hayyaClassesList" value="bg-default" name="bg-default" <%= classes.Value['bg-default'] %>/>
    						<label for="bg-default"><?php esc_html_e( 'Background default style', HAYYAB_BASENAME ); ?></label>
    					</div>
    					<div class="col s6">
							<input type="checkbox" id="text-default" class="hayyaClassesList" value="text-default" name="text-default" <%= classes.Value['text-default'] %>/>
    						<label for="text-default"><?php esc_html_e( 'Text default style', HAYYAB_BASENAME ); ?></label>
    					</div>
    				</div>
    				<div class="row">
    					<div class="col s6">
							<input type="checkbox" id="text-muted" class="hayyaClassesList" value="text-muted" name="text-muted" <%= classes.Value['text-muted'] %>/>
    						<label for="text-muted"><?php esc_html_e( 'Text muted style', HAYYAB_BASENAME ); ?></label>
    					</div>
    					<div class="col s6">
							<input type="checkbox" id="small" class="hayyaClassesList" value="small" name="small" <%= classes.Value['small'] %>/>
    						<label for="small"><?php esc_html_e( 'Text small style', HAYYAB_BASENAME ); ?></label>
    					</div>
    				</div>
    				<div class="row">
    					<div class="col s6">
							<input type="checkbox" id="text-left" class="hayyaClassesList" value="text-left" name="text-left" <%= classes.Value['text-left'] %>/>
    						<label for="text-left"><?php esc_html_e( 'Text align left', HAYYAB_BASENAME ); ?></label>
    					</div>
    					<div class="col s6">
							<input type="checkbox" id="text-right" class="hayyaClassesList" value="text-right" name="text-right" <%= classes.Value['text-right'] %>/>
    						<label for="text-right"><?php esc_html_e( 'Text align right', HAYYAB_BASENAME ); ?></label>
    					</div>
    				</div>
    				<div class="row">
    					<div class="col s6">
							<input type="checkbox" id="text-center" class="hayyaClassesList" value="text-center" name="text-center" <%= classes.Value['text-center'] %>/>
    						<label for="text-center"><?php esc_html_e( 'Text align center', HAYYAB_BASENAME ); ?></label>
    					</div>
    					<div class="col s6">
							<input type="checkbox" id="text-justify" class="hayyaClassesList" value="text-justify" name="text-justify" <%= classes.Value['text-justify'] %>/>
    						<label for="text-justify"><?php esc_html_e( 'Text align justify', HAYYAB_BASENAME ); ?></label>
    					</div>
    				</div>
    				<div class="row">
    					<div class="col s6">
							<input type="checkbox" id="pull-left" class="hayyaClassesList" value="pull-left" name="pull-left" <%= classes.Value['pull-left'] %>/>
    						<label for="pull-left"><?php esc_html_e( 'Pull left', HAYYAB_BASENAME ); ?></label>
    					</div>
    					<div class="col s6">
							<input type="checkbox" id="pull-right" class="hayyaClassesList" value="pull-right" name="pull-right" <%= classes.Value['pull-right'] %>/>
    						<label for="pull-right"><?php esc_html_e( 'Pull right', HAYYAB_BASENAME ); ?></label>
    					</div>
    				</div>
	    		</fieldset>
	    		<fieldset>
	    			<legend><i class="fa  fa-plus"></i> <?php esc_html_e( 'Extra classes', HAYYAB_BASENAME ); ?></legend>
	    			<input type="text" class="hayyaClassesList" name="classes" value="<%= classes.classes %>">
	    		</fieldset>
			</div>
    	</script>
    	<?php
    }

    /**
     *    @method    hayya_lite
     *    @param     string    $show    [description]
     *    @param     string    $options    [description]
     *    @return    string    [description]
     *
     *    @since     1.0.0
     *    @access    public
     */
    public static function hayya_lite( $show = '', $options = '' ) {
        $plugin_link = esc_url( 'http://codecanyon.net/item/hayyabuild-responsive-header-and-footer-builder/15315666?ref=zintathemes' );
        if ( empty( $show ) ) {
            ?>
            <ul class="collapsible" data-collapsible="accordion">
    	        <li>
    	            <div class="active collapsible-header <?php echo $options;?>">
    	            	<i class="fa fa-check"></i> <?php _e( 'Unlock All Features', HAYYAB_BASENAME);?>
    	            </div>
    	            <div class="collapsible-body valign-wrapper" style="padding: 10px;">
                            <b>First of all, thank you for using HayyaBuild Lite.<br/>
                            <b>If you like it, Please don't forget to write a good review.</b>
                            <a class="yellow-text text-accent-2" target="_blank" href="http://wordpress.org/plugins/hayyabuild/" style="text-decoration: none;">
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                            </a>
    	                <div class="row valign">
    	                    <div class="col s8">
    	                        <section id="top">
    	                            <div>
	                                    <h3>HayyaBuild PRO features list</h3>
	                                    <ul class="pro-features-list">
                                            <li><b>Extra Modules</b> - More than 40 built in modules. this modules comes with HayyaBuild Pro without the needs for another WordPress plugins or WordPress themes.</li>
                                            <li><b>25 Pre-Made Templates</b> - Save your time by starting from more than 25 pre-made templates.</li>
                                            <li><b>Import HayyaBuild Content</b> - With this feature you can import HayyaBuild content from any wordpress. Use this feature to transfer HayyaBuild content from one wordpress site to the other.</li>
                                            <li><b>And More...</li>
                                        </ul>
    	                            </div>
    	                        </section>
    	                    </div>
    	                    <div class="col s4 right-align">
    	                        <img src="<?php echo site_url().'/wp-content/plugins/'.HAYYAB_BASENAME.'/admin/assets/images/logo.png?v='.HAYYAB_VERSION; ?>" />
    	                    </div>
    	                </div>
    	                <div class="center-align" style="padding-bottom: 10px;">
                                <span class="red-text" style="font-weight: 500;font-size: 15px">
        	                		what you get for <del style="color: #D70000">34$</del> 29$ ?<br/> <br/>
                                	ALL HAYYABUILD FEATURES | UNLIMITED UPDATES | ONE TIME PURCHASE | 6 MONTHS SUPPORT
                                </span>
                                <hr/>
    	                    <a target="_blank" class="waves-effect waves-darck hayya_btn" href="<?php echo $plugin_link; ?>">
    	                    	<?php _e('GET IT NOW', HAYYAB_BASENAME); ?> <i class="fa fa-cart-plus"></i>
    	                    </a>
    	                </div>
    	            </div>
    	        </li>
    	    </ul>
            <?php
        } else if ( $show === 'pro-only' ) {
            ?>
            <div class="yellow lighten-5" style="padding: 20px;border: 1px solid yellow;">
                <div class="center-align">
                	<p class="center-align" style="padding: 5px;">
                    	<?php _e('This feature is available only in HayyaBuild Pro.<br/>You can unlock all features by getting HayyaBuild Pro version.', HAYYAB_BASENAME); ?>

                	</p>
                    <a target="_blank" class="waves-effect waves-darck" href="<?php echo $plugin_link; ?>">
                    	<?php _e('GET IT NOW', HAYYAB_BASENAME); ?> <i class="fa fa-cart-plus"></i>
                    </a>
                </div>
            </div>
            <?php
        } else if ( $show === 'pro' ) {
            ?>
            <div class="yellow lighten-5" style="padding: 7px;border: 1px solid yellow;">
            	<small><?php _e('This feature is available only in HayyaBuild Pro.', HAYYAB_BASENAME); ?></small>
            </div>
            <?php
        } else if ( $show === 'unlock' ) {
            ?>
            <div class="yellow lighten-5" style="padding: 7px;border: 1px solid yellow;margin-top: 10px;">
            	<?php _e('You can support it by unlocking all features.<br/> to unlock all features.', HAYYAB_BASENAME); ?>
                <a target="_blank" href="<?php echo $plugin_link; ?>">
                    <?php _e('click here', HAYYAB_BASENAME); ?>
                </a>
            </div>
            <?php
        } else if ( $show === 'modules' ) {
            ?>
            <div class="well text-center text-overflow">
                <a target="_blank" href="<?php echo $plugin_link; ?>">
                    <div class="well-content red-text">
                        <i class="fa fa-cart-plus"></i>
                        <div class="name"><?php _e('Get More Modules', HAYYAB_BASENAME); ?></div>
                        <div class="text-muted small"><?php _e('Click here to get more modules', HAYYAB_BASENAME); ?></div>
                    </div>
                </a>
            </div>
            <?php
        }
    }

} // End Class
