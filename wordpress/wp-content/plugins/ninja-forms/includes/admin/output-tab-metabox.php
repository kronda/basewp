<?php
function ninja_forms_output_tab_metabox($form_id = '', $slug, $metabox){
	$plugin_settings = get_option( 'ninja_forms_settings' );
	if($form_id != ''){
		$form_row = ninja_forms_get_form_by_id($form_id);
		$current_settings = $form_row['data'];
	}else{
		$form_id = '';
		$current_settings = get_option("ninja_forms_settings");
	}

	$page = $metabox['page'];
	$tab = $metabox['tab'];

	$title = $metabox['title'];
	if(isset($metabox['settings'])){
		$settings = $metabox['settings'];
	}else{
		$settings = '';
	}

	if(isset($metabox['display_function'])){
		$display_function = $metabox['display_function'];
	}else{
		$display_function = '';
	}

	if($metabox['state'] == 'closed'){
		$state = 'display:none;';
	}else{
		$state = '';
	}

	if( isset( $plugin_settings['metabox_state'][$page][$tab][$slug] ) ){
		$state = $plugin_settings['metabox_state'][$page][$tab][$slug];
	}

	if( isset( $metabox['display_container'] ) ){
		$display_container = $metabox['display_container'];
	}else{
		$display_container = true;
	}

	if( $display_container ){
		?>
		<div id="ninja_forms_metabox_<?php echo $slug;?>" class="postbox ">
			<span class="item-controls">
				<a class="item-edit metabox-item-edit" id="edit_id" title="Edit Menu Item" href="#">Edit Menu Item</a>
			</span>
			<h3 class="hndle"><span><?php _e($title, 'ninja-forms');?></span></h3>
			<div class="inside" style="<?php echo $state;?>">
			<table class="form-table">
				<tbody>
		<?php
	}

	if( is_array( $settings ) AND !empty( $settings ) ){
		foreach( $settings as $s ){

			$value = '';
			if(isset($s['name'])){
				$name = $s['name'];
			}else{
				$name = '';
			}
			$name_array = '';
			if( strpos( $name, '[') !== false ){
				$name_array = str_replace( ']', '', $name );
				$name_array = explode( '[', $name_array );
			}
			if(isset($s['type'])){
				$type = $s['type'];
			}else{
				$type = '';
			}
			if(isset($s['desc'])){
				$desc = $s['desc'];
			}else{
				$desc = '';
			}
			if(isset($s['help_text'])){
				$help_text = $s['help_text'];
			}else{
				$help_text = '';
			}
			if(isset($s['label'])){
				$label = $s['label'];
			}else{
				$label = '';
			}
			if(isset($s['class'])){
				$class = $s['class'];
			}else{
				$class = '';
			}
			if(isset($s['tr_class'])){
				$tr_class = $s['tr_class'];
			}else{
				$tr_class = '';
			}
			if(isset($s['max_file_size'])){
				$max_file_size = $s['max_file_size'];
			}else{
				$max_file_size = '';
			}
			if(isset($s['select_all'])){
				$select_all = $s['select_all'];
			}else{
				$select_all = false;
			}
			if(isset($s['default_value'])){
				$default_value = $s['default_value'];
			}else{
				$default_value = '';
			}
			if( isset( $s['style'] ) ){
				$style = $s['style'];
			}else{
				$style = '';
			}
			if(isset($s['size'])){
				$size = $s['size'];
			}else{
				$size = '';
			}

			if( is_array( $name_array ) ){
				$tmp = '';
				foreach( $name_array as $n ){
					if( $tmp == '' ){
						if( isset( $current_settings[$n] ) ){
							$tmp = $current_settings[$n];
						}
					}else{
						if( isset( $tmp[$n] ) ){
							$tmp = $tmp[$n];
						}
					}
				}
				$value = $tmp;
			}else{
				if(isset($current_settings[$name])){
					if(is_array($current_settings[$name])){
						$value = ninja_forms_stripslashes_deep($current_settings[$name]);
					}else{
						$value = stripslashes($current_settings[$name]);
					}
				}else{
					$value = '';
				}
			}

			if( $value == '' ){
				$value = $default_value;
			}

			switch( $s['type'] ){
				case 'text':
					$value = ninja_forms_esc_html_deep( $value );
					?>
					<tr <?php if( $tr_class != '' ){ ?>class="<?php echo $tr_class;?>"<?php } ?> <?php if( $style != '' ){ ?> style="<?php echo $style;?>"<?php }?>>
						<th>
							<?php echo $label; ?>
						</th>
						<td>
							<input type="text" class="code widefat <?php echo $class;?>" name="<?php echo $name;?>" id="<?php echo $name;?>" value="<?php echo $value;?>" />
							<?php if( $help_text != ''){ ?>
							<a href="#" class="tooltip">
							    <img id="" class='ninja-forms-help-text' src="<?php echo NINJA_FORMS_URL;?>/images/question-ico.gif" title="">
							    <span>
							        <img class="callout" src="<?php echo NINJA_FORMS_URL;?>/images/callout.gif" />
							        <?php echo $help_text;?>
							    </span>
							</a>
							<?php } ?>
						</td>
					</tr>
					<?php
					break;
				case 'select':
					?>
					<tr <?php if( $tr_class != '' ){ ?>class="<?php echo $tr_class;?>"<?php } ?>  <?php if( $style != '' ){ ?> style="<?php echo $style;?>"<?php }?>>
						<th>
							<?php echo $label; ?>
						</th>
						<td>
							<select name="<?php echo $name;?>" id="<?php echo $name;?>" class="<?php echo $class;?>">
								<?php
								if( is_array( $s['options']) AND !empty( $s['options'] ) ){
									foreach( $s['options'] as $option ){
										?>
										<option value="<?php echo $option['value'];?>" <?php selected($value, $option['value']); ?>><?php echo $option['name'];?></option>
										<?php
									}
								} ?>
							</select>
							<?php if( $help_text != ''){ ?>
								<a href="#" class="tooltip">
								    <img id="" class='ninja-forms-help-text' src="<?php echo NINJA_FORMS_URL;?>/images/question-ico.gif" title="">
								    <span>
								        <img class="callout" src="<?php echo NINJA_FORMS_URL;?>/images/callout.gif" />
								        <?php echo $help_text;?>
								    </span>
								</a>
							<?php } ?>
						</td>
					</tr>
					<?php
					break;
				case 'multi_select':
					if( $value == '' ){
						$value = array();
					}
					?>
					<tr <?php if( $tr_class != '' ){ ?>class="<?php echo $tr_class;?>"<?php } ?> <?php if( $style != '' ){ ?> style="<?php echo $style;?>"<?php }?>>
						<th>
							<?php echo $label; ?>
						</th>
						<td>
							<input type="hidden" name="<?php echo $name;?>" value="">
							<select name="<?php echo $name;?>[]" id="<?php echo $name;?>" class="<?php echo $class;?>" multiple="multiple" size="<?php echo $size;?>">
								<?php
								if( is_array( $s['options']) AND !empty( $s['options'] ) ){
									foreach( $s['options'] as $option ){
										?>
										<option value="<?php echo $option['value'];?>" <?php selected( in_array( $option['value'], $value ) ); ?>><?php echo $option['name'];?></option>
										<?php
									}
								} ?>
							</select>
							<?php if( $help_text != ''){ ?>
								<a href="#" class="tooltip">
								    <img id="" class='ninja-forms-help-text' src="<?php echo NINJA_FORMS_URL;?>/images/question-ico.gif" title="">
								    <span>
								        <img class="callout" src="<?php echo NINJA_FORMS_URL;?>/images/callout.gif" />
								        <?php echo $help_text;?>
								    </span>
								</a>
							<?php } ?>
						</td>
					</tr>
					<?php
					break;
				case 'checkbox':
					?>
					<tr <?php if( $tr_class != '' ){ ?>class="<?php echo $tr_class;?>"<?php } ?> <?php if( $style != '' ){ ?> style="<?php echo $style;?>"<?php }?>>
						<th>
							<label for="<?php echo $name;?>"><?php echo $label;?></label>
						</th>
						<td>
							<input type="hidden" name="<?php echo $name;?>" value="0">
							<input type="checkbox" name="<?php echo $name;?>" value="1" <?php checked($value, 1);?> id="<?php echo $name;?>" class="<?php echo $class;?>">
							<?php if( $help_text != ''){ ?>
								<a href="#" class="tooltip">
								    <img id="" class='ninja-forms-help-text' src="<?php echo NINJA_FORMS_URL;?>/images/question-ico.gif" title="">
								    <span>
								        <img class="callout" src="<?php echo NINJA_FORMS_URL;?>/images/callout.gif" />
								        <?php echo $help_text;?>
								    </span>
								</a>
							<?php } ?>
						</td>
					</tr>
					<?php
					break;
				case 'checkbox_list':
					if( $value == '' ){
						$value = array();
					}

					?>
					<input type="hidden" name="<?php echo $name;?>" value="">
					<tr <?php if( $tr_class != '' ){ ?>class="<?php echo $tr_class;?>"<?php } ?> <?php if( $style != '' ){ ?> style="<?php echo $style;?>"<?php }?>>
						<th>
							<?php echo $label;?>
						</th>
						<?php
						if( $select_all ){
							?>
							<td>
								<label>
									<input type="checkbox" name="" value="" id="<?php echo $name;?>_select_all" class="ninja-forms-select-all" title="ninja-forms-<?php echo $name;?>">
								- <?php _e( 'Select All', 'ninja-forms' );?>
								</label>
							</td>
						<?php
						}else{
							if( is_array( $s['options'] ) AND isset( $s['options'][0] ) ){

								$option_name = $s['options'][0]['name'];
								$option_value = $s['options'][0]['value'];

								?>
								<td>
									<label>
										<input type="checkbox" class="ninja-forms-<?php echo $name;?> <?php echo $class;?>" name="<?php echo $name;?>[]" value="<?php echo $option_value;?>" <?php checked( in_array( $option_value, $value ) );?> id="<?php echo $option_name;?>">
										<?php echo $option_name;?>
									</label>
								</td>
								<?php
							}
						}
						?>
					</tr>
					<?php
					if( is_array( $s['options'] ) AND !empty( $s['options'] ) ){
						$x = 0;
						foreach( $s['options'] as $option ){
							if( ( !$select_all AND $x > 0 ) OR $select_all ){
								$option_name = $option['name'];
								$option_value = $option['value'];
								?>
								<tr <?php if( $style != '' ){ ?> style="<?php echo $style;?>"<?php }?>>
									<th>

									</th>
									<td>
										<label>
											<input type="checkbox" class="ninja-forms-<?php echo $name;?> <?php echo $class;?>" name="<?php echo $name;?>[]" value="<?php echo $option_value;?>" <?php checked( in_array( $option_value, $value ) );?> id="<?php echo $option_name;?>">
											<?php echo $option_name;?>
										</label>
									</td>
								</tr>
								<?php
							}
							$x++;
						}
					}
					break;
				case 'radio':
					if( is_array( $s['options'] ) AND !empty( $s['options'] ) ){
						$x = 0; ?>
						<tr <?php if( $tr_class != '' ){ ?>class="<?php echo $tr_class;?>"<?php } ?> <?php if( $style != '' ){ ?> style="<?php echo $style;?>"<?php }?>>
							<th>
								<?php echo $label;?>
							</th>
							<td>
								<?php foreach($s['options'] as $option){ ?>
									<input type="radio" name="<?php echo $name;?>" value="<?php echo $option['value'];?>" id="<?php echo $name."_".$x;?>" <?php checked($value, $option['value']);?> class="<?php echo $class;?>"> <label for="<?php echo $name."_".$x;?>"><?php echo $option['name'];?></label>
										<?php if( $help_text != ''){ ?>
											<a href="#" class="tooltip">
											    <img id="" class='ninja-forms-help-text' src="<?php echo NINJA_FORMS_URL;?>/images/question-ico.gif" title="">
											    <span>
											        <img class="callout" src="<?php echo NINJA_FORMS_URL;?>/images/callout.gif" />
											        <?php echo $help_text;?>
											    </span>
											</a>
										<?php } ?>
									<br />

								<?php
									$x++;
								} ?>
							</td>
						</tr>
					<?php
					}
					break;
				case 'textarea':
					$value = ninja_forms_esc_html_deep( $value );
					?>
					<tr <?php if( $tr_class != '' ){ ?>class="<?php echo $tr_class;?>"<?php } ?> <?php if( $style != '' ){ ?> style="<?php echo $style;?>"<?php }?>>
						<th>
							<?php echo $label; ?>
						</th>
						<td>
							<textarea name="<?php echo $name;?>" id="<?php echo $name;?>" class="<?php echo $class;?>"><?php echo $value;?></textarea>
						</td>
					</tr>
					<?php
					break;
				case 'rte':
					?>
					<tr <?php if( $tr_class != '' ){ ?>class="<?php echo $tr_class;?>"<?php } ?> <?php if( $style != '' ){ ?> style="<?php echo $style;?>"<?php }?>>
						<th>
							<?php echo $label; ?>
						</th>
						<td>
							<?php
							$args = apply_filters( 'ninja_forms_admin_metabox_rte', array() );
							wp_editor( $value, $name, $args );
							?>
						</td>
					</tr>
					<?php
					break;
				case 'file':
					?>
					<tr <?php if( $tr_class != '' ){ ?>class="<?php echo $tr_class;?>"<?php } ?> <?php if( $style != '' ){ ?> style="<?php echo $style;?>"<?php }?>>
						<th>
							<?php echo $label; ?>
						</th>
						<td colspan="2">
							<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $max_file_size;?>" />
							<input type="file" name="<?php echo $name;?>" id="<?php echo $name;?>" class="<?php echo $class;?>">
						</td>
					</tr>
					<?php
					break;
				case 'desc':
					?>
					<tr <?php if( $tr_class != '' ){ ?>class="<?php echo $tr_class;?>"<?php } ?> <?php if( $style != '' ){ ?> style="<?php echo $style;?>"<?php }?>>
						<th>
							<?php echo $label; ?>
						</th>
						<td>
							<?php echo $desc;?>
						</td>
					</tr>
					<?php
					break;
				case 'hidden':
					?>
					<input type="hidden" name="<?php echo $name;?>" id="<?php echo $name;?>" value="<?php echo $value;?>">
					<?php
					break;
				case 'submit':
					?>
					<tr <?php if( $tr_class != '' ){ ?>class="<?php echo $tr_class;?>"<?php } ?> <?php if( $style != '' ){ ?> style="<?php echo $style;?>"<?php }?>>
						<td colspan="2">
							<input type="submit" name="<?php echo $name;?>" class="<?php echo $class; ?>" value="<?php echo $label;?>">
						</td>
					</tr>
					<?php
					break;
				default:
					if( isset( $s['display_function'] ) ){
						$s_display_function = $s['display_function'];
						if( $s_display_function != '' ){
							$arguments['form_id'] = $form_id;
							$arguments['data'] = $current_settings;
							call_user_func_array( $s_display_function, $arguments );
						}
					}
					break;
			}

			if( $desc != '' AND $s['type'] != 'desc' ){
				?>
				<tr class="<?php echo $tr_class;?>" <?php if( $style != '' ){ ?> style="<?php echo $style;?>"<?php }?>>
					<th>

					</th>
					<td class="howto">
						<?php echo $desc;?>
					</td>
				</tr>
				<?php
			}

		}
	}

	if( $display_function != '' ){
		if( $form_id != '' ){
			$arguments['form_id'] = $form_id;
		}
		$arguments['metabox'] = $metabox;
		call_user_func_array( $display_function, $arguments );
	}

	if( $display_container ){
		?>
					</tbody>
				</table>
			</div>
		</div>
		<?php
	}
}