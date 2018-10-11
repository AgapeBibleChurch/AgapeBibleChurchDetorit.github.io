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


class HayyaBuilder extends HayyaAdmin {

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
    public function __construct($results = null, $elements_group = null) {
    	if ( $elements_group ) return $this->Builder($results, $elements_group);
    }


    /**
     *
     * @access		private
     * @since		1.0.0
     * @var			unown
     */
    private static function hb_background( $settings ) {
    	$background = 'url(\'../wp-content/plugins/'.HAYYAB_BASENAME.'/admin/assets/images/empty_bg.png\')';
    	if ( is_array($settings) &&  isset($settings['background_type']) ) {
    		if ( $settings['background_type'] == 'background_image' && ! empty( $settings['background_image'] )) $background = 'url(\'' . $settings['background_image'] . '\')';
    		elseif ($settings['background_type'] == 'background_video') $background = '#EEEEEE';
    		elseif ($settings['background_type'] == 'background_color') $background = $settings['background_color'];
    	}
    	return $background;
    }

    /**
     *
     * @param unknown $results
     */
    protected static function Builder($results = null, $elements_group = null) {
    	if ( HayyaHelper::_get( 'action' ) == 'edit' ) {
    		if ( $results  ) {
    			if ( $results->type == 'header' || $results->type == 'footer' || $results->type == 'content' ) {
    				$type = $results->type;
    			}
    			$settings = preg_replace_callback(
    					'/s:([0-9]+):\"(.*?)\";/',
    					function ($matches) { return "s:".strlen($matches[2]).':"'.$matches[2].'";';     },
    					$results->settings
    					);
    			$settings = maybe_unserialize( $settings );
    			$settings['csscode'] = (isset($settings['csscode'])) ? stripslashes( $settings['csscode'] ) : '';
    			$pages_list = preg_replace_callback(
    					'/s:([0-9]+):\"(.*?)\";/',
    					function ($matches) { return "s:".strlen($matches[2]).':"'.$matches[2].'";';     },
    					$results->pages
    					);
    			$pages_list =  maybe_unserialize( $pages_list );
    		}
    	} else {
    		if ( HayyaHelper::_get( 'page' ) == 'hayyabuild_addh' ) $type = 'header';
    		elseif ( HayyaHelper::_get( 'page' ) == 'hayyabuild_addc' ) $type = 'content';
    		elseif ( HayyaHelper::_get( 'page' ) == 'hayyabuild_addf' ) $type = 'footer';
    		$settings = array( 'elements_list' => '', 'fixed_video' => '', 'background_type' => '', 'background_image' => '', 'background_repeat' => '', 'background_size' => '', 'background_effect' => '', 'background_video' => '', 'background_color' => '', 'scroll_effect' => '', 'smooth_scroll' => '', 'smooth_scroll_speed' => '', 'text_color' => '', 'height' => '', 'height_m_unit' => '', 'border_color' => '', 'margin_top' => '', 'margin_bottom' => '', 'margin_left' => '', 'margin_right' => '', 'border_top_width' => '', 'border_bottom_width' => '', 'border_left_width' => '', 'border_right_width' => '', 'padding_top' => '', 'margin_right' => '', 'padding_bottom' => '', 'padding_left' => '', 'padding_right' => '');
    		$results = '';
    	}
    	$settings ['background'] = self::hb_background( $settings );
    	$content = ( isset( $results->content ) ) ? stripslashes( $results->content ) : '';
    	?>

		<div id="hayyabuild" class="wrap">

			<form method="post" action="" id="builder_form" class="form-inline" role="form" id="hayyabuild">

                <?php
                wp_nonce_field( 'builder_form_nonce', '_hbnonce' );
                ?>

				<div class="hb-main_settings">

			        <?php HayyaView::nav_bar($main = false); ?>

                    <hr/>

					<div class="main_conf">
						<div class="row">
							<div class="col m6 s12">
				        		<input name="name" id="name" size="30" value="<?php echo (isset($results->name) ) ? sanitize_text_field($results->name) : '' ;?>" type="text" class="validate" style="height: 35px;margin-top: 0;">
				        		<label for="name">
                                    <?php
                                    echo ucfirst($type) . ' ';
                                    esc_html_e( 'Name', HAYYAB_BASENAME );
                                    ?>
                                </label>
							</div>
						</div>
						<div class="row">
				            <div class="col m6 s12">
				            	<select id="pages" name="pages[]" data-placeholder="Select Pages" class="chosen-select" multiple>
				                	<?php
				                    $pages = get_pages();
				                    $selected = '';
				                    if ( isset($pages_list) && is_array($pages_list) ) $selected = ( $pages_list && in_array( 'all', $pages_list ) ) ? ' selected' : '';
				                    echo '<option value="all"'.$selected.'>'.__( 'All pages', HAYYAB_BASENAME ).'</option>';?>
				                    <optgroup label="<?php esc_attr_e( 'Pages list', HAYYAB_BASENAME )?>">
				                    <?php
				                    foreach ( $pages as $page ) {
				                    	$selected = '';
				                        if ( isset($pages_list) && is_array($pages_list) ) {
				                        	$selected = ( $page->ID && in_array( $page->ID, $pages_list ) ) ? ' selected' : '';
				                        }
				                        echo '<option value="' .$page->ID. '"'.$selected.'>'.$page->post_title .'</option>';
				                    }
				                    ?>
				                    </optgroup>
                                    <optgroup label="<?php esc_attr_e( 'Other Pages', HAYYAB_BASENAME )?>">
    				                    <?php
    				                    $selected = '';
    				                    if ( isset($pages_list) && is_array($pages_list) ) $selected = ( $pages_list && in_array( '404page', $pages_list ) ) ? ' selected' : '';
                                        echo '<option value="404page"'.$selected.'>'.__( '404 Error Page', HAYYAB_BASENAME ).'</option>';?>
    				                    ?>
                                    </optgroup>
				                </select>
				                <label for="pages"><?php esc_html_e( 'Pages List', HAYYAB_BASENAME ); ?></label>
				            </div>
				        </div>
					</div>

					<div class="composer-container-content">
						<textarea rows="10" cols="70" name="hb_content" id="hb_content"><?php echo $content;?></textarea>
					</div>
					<div class="elements_list" style="display: none;visibility: hidden;">
                        <hr/>
						<div class="" style="float: left;height: 20px;">
							<input id="elements_list_input" type="hidden" name="settings[elements_list]" size="100" value="<?php echo (isset($settings['elements_list'])) ? sanitize_text_field($settings['elements_list']) : '';?>">
							<span>
								<?php esc_html_e('Modules list', HAYYAB_BASENAME);?>
							</span>
						</div>
						<ul id="elements_list"></ul>
					</div>

				</div>

				<hr/>

				<ul class="collapsible" data-collapsible="accordion">
					<li>
						<div class="active collapsible-header">
						    <i class="fa fa-gears"></i><?php esc_html_e('Settings', HAYYAB_BASENAME );?>
						</div>
						<div class="collapsible-body" style="padding-top: 10px;">
							<div class="row settings">
								<div class="col s6">
					    			<div class="row">
										<div class="col s4 input-field">
											<?php esc_html_e( 'Background type', HAYYAB_BASENAME );?>
										</div>
										<div class="col s8">
											<select name="settings[background_type]" class="select_material" id="background_type" style="width: 100%;">
												<?php
												$background_types = array( 'background_transparent' => __( 'Transparent', HAYYAB_BASENAME ), 'background_image' => __( 'Image', HAYYAB_BASENAME ), 'background_video' => __( 'Video', HAYYAB_BASENAME ), 'background_color' => __( 'Color', HAYYAB_BASENAME ) );
												foreach ($background_types as $key => $value) {
													$selected = ( $key == HayyaHelper::__empty($settings['background_type']) ) ? 'selected' : '';
													echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
												}
												?>
											</select>
					    				</div>
					    			</div>
									<div class="background_div" id="background_image">
										<div class="row">
											<div class="col s4 input-field">
												<?php esc_html_e( 'Background image', HAYYAB_BASENAME );?>
											</div>
											<div class="col s8">
												<nobr>
													<input id="background_image_input" type="text" name="settings[background_image]" placeholder="<?php esc_attr_e( 'Image URL', HAYYAB_BASENAME );?>" value="<?php echo ( isset( $settings['background_image']) ) ? esc_url($settings['background_image']) : '';?>" style="width: 63%;"/>
							    					<a id="background_image_button" class="waves-effect waves-light hayya_btn " style="width: 35%;" href="#">
														<i class="fa fa-camera"></i> <?php esc_html_e( 'Select', HAYYAB_BASENAME );?>
												  	</a>
												</nobr>
						    				</div>
										</div>
					    				<div class="row">
						    				<div class="col s4 input-field">
												<?php esc_html_e( 'Background repeat', HAYYAB_BASENAME );?>
											</div>
											<div class="col s8">
												<select name="settings[background_repeat]" class="select_material" style="width: 100%;">
													<?php
													$background_repeat = array('repeat' => __( 'Repeat', HAYYAB_BASENAME ), 'repeat-x' => __( 'Repeat X', HAYYAB_BASENAME ), 'repeat-y' => __( 'Repeat Y', HAYYAB_BASENAME ), 'no-repeat' => __( 'No repeat', HAYYAB_BASENAME ) );

													foreach ($background_repeat as $key => $value) {
														$selected = ( $key == HayyaHelper::__empty($settings['background_repeat']) ) ? 'selected': '';
														echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
													}
													?>
												</select>
						    				</div>
					    				</div>
					    				<div class="row">
						    				<div class="col s4 input-field">
												<?php esc_html_e( 'Background size', HAYYAB_BASENAME );?>
											</div>
											<div class="col s8">
												<select name="settings[background_size]" class="select_material" style="width: 100%;">
													<?php
													$background_size = array('auto' => __( 'Auto', HAYYAB_BASENAME ), 'length' => __( 'Length', HAYYAB_BASENAME ), 'cover' => __( 'Cover', HAYYAB_BASENAME ), 'contain' => __( 'Contain', HAYYAB_BASENAME ), 'initial' => __( 'Initial', HAYYAB_BASENAME ) );

													foreach ($background_size as $key => $value) {
														$selected = ( $key == $settings['background_size'] ) ? 'selected': '';
														echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
													}
													?>
												</select>
						    				</div>
					    				</div>
							    		<div class="row">
											<div class="col s4 input-field">
												<?php esc_html_e( 'Background Effects', HAYYAB_BASENAME ); ?>
											</div>
											<div class="col s8">
												<select name="settings[background_effect][]" class="select_material" multiple>
												    <option value="" disabled="disabled">Empty</option>
													<?php
													$background_effect = array(
													     'bgfixed' => __( 'Fixed', HAYYAB_BASENAME ),
													     'bgparallax' => __( 'Parallax', HAYYAB_BASENAME ),
													     // 'bgzoom' => __( 'Zoom Effect', HAYYAB_BASENAME )
		                                            );
													foreach ($background_effect as $key => $value) {
													    $selected = '';
													    if ( isset($settings['background_effect']) && is_array($settings['background_effect']) ) {
		    												$selected = ( in_array( $key, $settings['background_effect'] ) ) ? ' selected' : '';
													    }
														echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
													}
													?>
												</select>
											</div>
										</div>
									</div>

									<div class="background_div" id="background_video">
										<div class="row">
											<div class="col s4 input-field">
												<?php esc_html_e( 'Background Video', HAYYAB_BASENAME );?>
											</div>
											<div class="col s8">
						    					<nobr>
													<input id="background_video_input" type="text" name="settings[background_video]" placeholder="<?php esc_attr_e( 'Video" URL', HAYYAB_BASENAME );?>" value="<?php echo (isset($settings['background_video'])) ? esc_url($settings['background_video']) : '';?>" style="width: 63%;"/>
							    					<a id="background_video_button" class="waves-effect waves-light hayya_btn" href="#"  style="width: 35%;">
														<i class="fa fa-video-camera"></i> <?php esc_html_e( 'Select', HAYYAB_BASENAME );?>
												  	</a>
												</nobr>
						    				</div>
										</div>
										<div class="row">
											<div class="col s4 input-field">
												<?php esc_html_e( 'Fixed Video', HAYYAB_BASENAME ); ?>
											</div>
											<div class="col s8">
												<?php $checked = ( !empty($settings['fixed_video']) ) ? ' checked' : '';?>
												<input id="fixed_video" name="settings[fixed_video]" type="checkbox" <?php echo $checked;?>>
												<label for="fixed_video"></label>
											</div>
										</div>
									</div>

					    			<div class="row background_div" id="background_color">
					    				<div class="col s4 input-field">
											<?php esc_html_e( 'Background color', HAYYAB_BASENAME );?>
										</div>
										<div class="col s8">
											<input name="settings[background_color]" class="minicolors" id="color-piker" type="text" value="<?php echo (isset($settings['background_color'])) ?  sanitize_text_field($settings['background_color']) : '';?>"/>
					    				</div>
									</div>

                                    <?php if ( $type !== 'content' ) :?>
									<div class="row">
										<div class="col s4 input-field">
											<?php esc_html_e( 'Scroll Effects', HAYYAB_BASENAME ); ?>
										</div>
										<div class="col s8">
											<select name="settings[scroll_effect][]" class="select_material" multiple>
		                                        <option value="" disabled="disabled">Empty</option>
												<?php
		                                        $scroll_effect = array(
		                                            'fixed' => __( 'Sticky', HAYYAB_BASENAME ),
		                                            'parallax' => __( 'Parallax Effect', HAYYAB_BASENAME ),
		                                            'opacity' => __( 'Opacity Effect', HAYYAB_BASENAME ),
		                                            'scaleIn' => __( 'Scale In', HAYYAB_BASENAME ),
		                                            'scaleOut' => __( 'Scale Out', HAYYAB_BASENAME )
		                                        );
												foreach ($scroll_effect as $key => $value) {
		                                            $selected = '';
		                                            if ( isset($settings['scroll_effect']) && is_array($settings['scroll_effect']) ) {
		                                                $selected = ( in_array( $key, $settings['scroll_effect'] ) ) ? ' selected' : '';
		                                            }
													echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
												}
												?>
											</select>
										</div>
									</div>
                                    <?php endif; ?>

									<?php if ( $type == 'header' ) :?>
									<div class="row">
										<div class="col s4 input-field">
											<?php esc_html_e( 'Smooth scroll', HAYYAB_BASENAME ); ?>
										</div>
										<div class="col s8">
											<?php $checked = ( isset($settings['smooth_scroll']) && $settings['smooth_scroll'] == 'on' ) ? ' checked' : '';?>
											<div class="switch">
		                                        <label>
		                                          Off <input id="smooth_scroll" type="checkbox" name="settings[smooth_scroll]" <?php echo $checked;?>/><span class="lever"></span> On
		                                        </label>
		                                    </div>
										</div>
									</div>
									<div class="row">
										<div class="col s4 input-field">
											<?php esc_html_e( 'Scroll speed', HAYYAB_BASENAME ); ?>
										</div>
										<div class="col s8">
											<span class="range-field">
										    	<input class="range-field" type="range" id="smooth_scroll_speed" min="10" max="1000" name="settings[smooth_scroll_speed]" value="<?php echo ( isset($settings['smooth_scroll_speed'])) ?  filter_var($settings['smooth_scroll_speed'], FILTER_SANITIZE_NUMBER_INT) : '100';?>"/>
										    </span>
										</div>
									</div>
									<?php endif;?>

								</div>

								<div class="col s6">
									<div class="row">
					    				<div class="col s4 input-field">
											<?php esc_html_e( 'Text color', HAYYAB_BASENAME );?>
										</div>
										<div class="col s8">
											<input name="settings[text_color]" class=" minicolors" type="text" value="<?php echo (isset($settings['text_color'])) ? $settings['text_color'] : '';?>"/>
					    				</div>
									</div>
					    			<div class="row">
					    				<div class="col s4 input-field">
					    					<?php
                                            echo ucfirst($type) . ' ';
                                            esc_html_e( 'Height', HAYYAB_BASENAME );
                                            ?>
					    				</div>
										<div class="col s8">
											<div class="row" style="padding: 0px;margin: 0px;">
												<div class="col s8" style="padding: 0px;margin: 0px;">
													<input id="height" name="settings[height]" type="text" value="<?php echo (isset($settings['height'])) ? filter_var($settings['height'], FILTER_SANITIZE_NUMBER_INT) : '';?>"/>
												</div>
												<div class="col s4" style="padding: 0px;margin: 0px;">
													<select name="settings[height_m_unit]" class="select_material">
														<?php
														$height_fit = array( 'px' => 'px', 'percent' => '%', 'VH' => 'vh' );
														foreach ($height_fit as $key => $value) {
															$selected = ( $key == $settings['height_m_unit'] ) ? 'selected': '';
															echo '<option value="' . esc_attr( $key ) . '" ' . esc_attr( $selected ) . '>' . esc_html( $value ) . '</option>';
														}
														?>
													</select>
												</div>
											</div>
					    				</div>
									</div>
					    			<div class="row">
					    				<div class="col s4 input-field">
											<?php esc_html_e( 'Border Color', HAYYAB_BASENAME );?>
										</div>
										<div class="col s8">
											<input name="settings[border_color]" type="text" class="minicolors" value="<?php echo ( isset($settings['border_color']) ) ?  sanitize_text_field( $settings['border_color'] ) : '';?>"/>
					    				</div>
									</div>
									<div class="row">
										<div class="layout col s12">
										<div class="margin">
							    				<label><?php esc_html_e('Margin', HAYYAB_BASENAME);?></label>
							    				<input name="settings[margin_top]" placeholder="-" value="<?php echo (isset($settings['margin_top'])) ? filter_var($settings['margin_top'], FILTER_SANITIZE_NUMBER_INT) : '';?>" type="text" class="margin_top">
							    				<input name="settings[margin_bottom]" placeholder="-" value="<?php echo (isset($settings['margin_bottom'])) ? filter_var($settings['margin_bottom'], FILTER_SANITIZE_NUMBER_INT) : '';?>" type="text" class="margin_bottom">
							    				<input name="settings[margin_left]" placeholder="-" value="<?php echo (isset($settings['margin_left'])) ? filter_var($settings['margin_left'], FILTER_SANITIZE_NUMBER_INT) : '';?>" type="text" class="margin_left">
							    				<input name="settings[margin_right]" placeholder="-" value="<?php echo (isset($settings['margin_right'])) ? filter_var($settings['margin_right'], FILTER_SANITIZE_NUMBER_INT) : '';?>" type="text" class="margin_right">
							    				<div class="border">
							    					<label><?php esc_html_e('Border', HAYYAB_BASENAME);?></label>
							    					<input name="settings[border_top_width]" placeholder="-" value="<?php echo (isset($settings['border_top_width'])) ? filter_var($settings['border_top_width'], FILTER_SANITIZE_NUMBER_INT) : '';?>" type="text" class="border_top_width">
							    					<input name="settings[border_bottom_width]" placeholder="-" value="<?php echo (isset($settings['border_bottom_width'])) ? filter_var($settings['border_bottom_width'], FILTER_SANITIZE_NUMBER_INT) : '';?>" type="text" class="border_bottom_width">
							    					<input name="settings[border_left_width]" placeholder="-" value="<?php echo (isset($settings['border_left_width'])) ? filter_var($settings['border_left_width'], FILTER_SANITIZE_NUMBER_INT) : '';?>" type="text" class="border_left_width">
							    					<input name="settings[border_right_width]" placeholder="-" value="<?php echo (isset($settings['border_right_width'])) ? filter_var($settings['border_right_width'], FILTER_SANITIZE_NUMBER_INT) : '';?>" type="text" class="border_right_width">
							    					<div class="padding">
							    						<label><?php esc_html_e('Padding', HAYYAB_BASENAME);?></label>
							    						<input name="settings[padding_top]" placeholder="-" value="<?php echo (isset($settings['padding_top'])) ? filter_var($settings['padding_top'], FILTER_SANITIZE_NUMBER_INT) : '';?>" type="text" class="padding_top">
							    						<input name="settings[padding_bottom]" placeholder="-" value="<?php echo (isset($settings['padding_bottom'])) ? filter_var($settings['padding_bottom'], FILTER_SANITIZE_NUMBER_INT) : '';?>" type="text" class="padding_bottom">
							    						<input name="settings[padding_left]" placeholder="-" value="<?php echo (isset($settings['padding_left'])) ? filter_var($settings['padding_left'], FILTER_SANITIZE_NUMBER_INT) : '';?>" type="text" class="padding_left">
							    						<input name="settings[padding_right]" placeholder="-" value="<?php echo (isset($settings['padding_right'])) ? filter_var($settings['padding_right'], FILTER_SANITIZE_NUMBER_INT) : '';?>" type="text" class="padding_right">
							    						<div class="content">
								    						<!-- <span class="help-tip">
																<i><?php esc_html_e( 'Select header Background.', HAYYAB_BASENAME );?></i>
															</span> -->
								    					</div>
								    				</div>
								    			</div>
								    		</div>
					    				</div>
					    			</div>
								</div>
							</div>
						</div>
					</li>
		            <li>
		                <div class="collapsible-header">
		                    <i class="fa fa-file-text"></i><?php esc_html_e( 'CSS Editor', HAYYAB_BASENAME);?>
		                </div>
		                <div class="collapsible-body valign-wrapper" style="padding-top: 10px;">
		                    <div class="row">
		                        <div class="col s12">
		                            <blockquote style="font-size: 12px;">
		                                <?php esc_html_e( 'This CSS code will only appear on pages with this '.$type.'.', HAYYAB_BASENAME);?>
		                            </blockquote>
		                        </div>
		                    </div>
		                    <hr/>
		                    <div class="row">
		                        <div class="col s12">
		                        	<input type="hidden" name="csscodeval" id="csscodeval" value="<?php echo (isset($settings['csscode'])) ?  esc_textarea($settings['csscode']) : '';?>">
		                            <textarea rows="" id="csscode" name="settings[csscode]" cols="" style="width: 100%;"><?php echo (isset($settings['csscode'])) ? esc_textarea($settings['csscode']) : '';?></textarea>
		                            <div id="csscodediv"></div>
		                        </div>
		                    </div>
		                </div>
		            </li>
				</ul>

				<input type="hidden" id="background" value="<?php echo  $settings['background'];?>">

				<?php

				if ( !isset($results->status) || ( $results->status != 'published' && $results->status != 'deactivate' ) ) $results_status = 'draft';
				else $results_status = $results->status;
				?>

				<input type="hidden" id="status" value="<?php echo  $results_status;?>" name="status">
				<input type="hidden" id="type" value="<?php echo  $type;?>" name="type">
				<input type="hidden" id="submit" value="submit" name="submit">

				<div style="margin: 20px 0px" class="hb_buttons">
					<div class="row">
						<div class="col s10">
							<?php
							if ( HayyaHelper::_get ( 'action' ) == 'edit' ) :
							?>
								<button class="waves-effect waves-darck hayya_btn save_shanges" type="submit" name="save" id="save" value="save">
									<?php  esc_html_e('Update', HAYYAB_BASENAME  ); ?>
							  	</button>
								<?php
								if ( $results->status == 'draft' ) :
								?>
									<button class="waves-effect waves-darck hayya_btn save_shanges" type="submit" name="publish" id="publish" value="publish">
										<?php esc_html_e('Publish', HAYYAB_BASENAME  ); ?>
								  	</button>
								<?php
								endif;
								?>
							<?php
							elseif ( HayyaHelper::_get ( 'page' ) == 'hayyabuild_addh' || HayyaHelper::_get ( 'page' ) == 'hayyabuild_addc' || HayyaHelper::_get ( 'page' ) == 'hayyabuild_addf' ) :
							?>
								<button class="waves-effect waves-darck hayya_btn save_shanges" type="submit" name="publish" id="publish" value="publish">
									<?php  esc_html_e('Publish', HAYYAB_BASENAME  ); ?>
							  	</button>
								<button class="waves-effect waves-darck hayya_btn save_shanges" type="submit" name="draft" id="draft" value="draft">
									<?php esc_html_e('Save as Draft', HAYYAB_BASENAME  ); ?>
							  	</button>
							<?php
							endif;
							?>
						</div>
		    		</div>
				</div>

			</form>


		    <div id="hb_modal-container"></div>

		    <!-- BEGIN: add modal. -->
		    <?php HayyaView::add_modal($elements_group); ?>
		    <!-- END: add modal. -->

		    <!-- START: editor modal. -->
		    <?php HayyaView::editor_modal(); ?>
		    <!-- END: editor modal. -->

		    <!-- BEGIN: classes modal. -->
		    <?php HayyaView::classes_modal(); ?>
		    <!-- END: classes modal. -->

		</div><?php
    }

} // End Class
