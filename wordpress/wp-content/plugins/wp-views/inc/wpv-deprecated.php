<?php

/**
* wpv-deprecated.php
*
* Holds some functions that might be deprecated but it is worth checking using Code Coverage
*
* @since 1.6.2
*/

/*************
* Layout functions
**************/

// DEPRECATED old Views Layout Meta HTML layout
function wpv_layout_meta_html_admin($post, $view_layout_settings) { // DEPRECATED
    global $WP_Views;
    $view_settings = $WP_Views->get_view_settings($post->ID);
    $defaults = array('layout_meta_html' => '',
                      'generated_layout_meta_html' => '');
    $view_layout_settings = wp_parse_args($view_layout_settings, $defaults);
    ?>
        <div id="wpv_layout_meta_html_admin">
	    <input type="hidden" name="_wpv_settings[layout_meta_html_state][html]" id="wpv_layout_meta_html_state" value="<?php echo isset($view_settings['layout_meta_html_state']['html']) ? $view_settings['layout_meta_html_state']['html'] : 'off'; ?>" />
            <div id="wpv_layout_meta_html_admin_show">
                <p><i><?php echo __('The layout-style and fields that you selected generate meta HTML. This meta HTML includes shortcodes and HTML, which you can edit, to fully customize the appearance of this View\'s content output section.', 'wpv-views'); ?></i></p>
                <input type="button" class="button-secondary" onclick="wpv_view_layout_meta_html()" value="<?php _e('View/Edit Meta HTML', 'wpv-views'); ?>" />
            </div>
            <div id="wpv_layout_meta_html_admin_edit" style="display:none">
                <div style="margin:10px 10px 10px 10px;">
                    <p><?php _e('<strong>Meta HTML</strong> - This is used to layout the posts found. It gets generated from the View Layout settings and can be modified to suit.', 'wpv-views'); ?></p>
                    <div id="wpv_layout_meta_html_content_error" class="wpv_form_errors" style="display:none;">
                        <p><?php _e("Changes can't be applied. It appears that you made manual modifications to the Meta HTML.", 'wpv-views'); ?></p>
                        <a style="cursor:pointer;margin-bottom:10px;" onclick="wpv_layout_meta_html_generate_new()"><strong><?php echo __('Generate the new layout content', 'wpv-views'); ?></strong></a> <?php _e('(your edits will be displayed and you can apply them again)', 'wpv-views'); ?>
                    </div>
           
                    <?php
                        $show = $view_settings['query_type'][0] == 'posts' ? 'style="display:inline"' : 'style="display:none"';
                    ?>
                    <div id="wpv-layout-v-icon-posts" <?php echo $show;?>>
                    <?php echo $WP_Views->editor_addon->add_form_button('', 'wpv_layout_meta_html_content', true, true); ?>
                    </div>
                    
                    <?php
                        $show = $view_settings['query_type'][0] == 'taxonomy' ? 'style="display:inline"' : 'style="display:none"';
                    ?>
                    <div id="wpv-layout-v-icon-taxonomy" <?php echo $show;?>>
                    <?php
                        // add a "V" icon for taxonomy
                        remove_filter('editor_addon_menus_wpv-views', 'wpv_post_taxonomies_editor_addon_menus_wpv_views_filter', 11);
                        add_filter('editor_addon_menus_wpv-views', 'wpv_layout_taxonomy_V');

                        echo $WP_Views->editor_addon->add_form_button('', 'wpv_layout_meta_html_content');
                        
                        remove_filter('editor_addon_menus_wpv-views', 'wpv_layout_taxonomy_V');
                        add_filter('editor_addon_menus_wpv-views', 'wpv_post_taxonomies_editor_addon_menus_wpv_views_filter', 11);
                    ?>
                    </div>
                    
                    <!--<div style="display:inline">-->
                    <span style="position: relative; top:-5px;">
                        <?php echo apply_filters('wpv_meta_html_add_form_button', '', '#wpv_layout_meta_html_content'); ?>
                    </span>
                    <!--</div>-->
                    
                    <textarea name="_wpv_layout_settings[layout_meta_html]" id="wpv_layout_meta_html_content" cols="40" rows="16" style="width:100%;margin-top:10px"><?php echo $view_layout_settings['layout_meta_html']; ?></textarea>
                    <div id="wpv_layout_meta_html_content_old_div" style="display:none">
                        <div class="wpv_form_notice"><?php _e('<strong>Your edits are shown below:</strong>', 'wpv-views'); ?> <a style="cursor:pointer;margin-bottom:10px;" onclick="wpv_layout_meta_html_old_dismiss()"><strong><?php echo __('dismiss', 'wpv-views'); ?></strong></a></div>
                        <textarea id="wpv_layout_meta_html_content_old" cols="40" rows="16" style="width:100%;margin-top:10px"></textarea>
                    </div>
                    <textarea name="_wpv_layout_settings[generated_layout_meta_html]" id="wpv_generated_layout_meta_html_content" cols="40" rows="16" style="display:none"><?php echo $view_layout_settings['generated_layout_meta_html']; ?></textarea>
                    <div id="wpv_layout_meta_html_notice" class="wpv_form_notice" style="display:none;"><?php _e('* These updates will take effect when you save the view.', 'wpv-views'); ?></div>
                    <p><a style="cursor:pointer;margin-bottom:10px;" onclick="wpv_view_layout_meta_html_close()"><strong><?php _e('Close', 'wpv-views'); ?></strong></a></p>
                </div>
            </div>
            <div id="wpv_layout_meta_html_extra_css" style="margin-top:15px;">
		<input type="hidden" name="_wpv_settings[layout_meta_html_state][css]" id="wpv_layout_meta_html_extra_css_state" value="<?php echo isset($view_settings['layout_meta_html_state']['css']) ? $view_settings['layout_meta_html_state']['css'] : 'off'; ?>" />
		<input type="button" class="button-secondary wpv_layout_meta_html_extra_css_edit" onclick="wpv_view_layout_meta_html_extra(this)" value="<?php _e('Edit CSS', 'wpv-views'); ?>" />
		<div id ="wpv_layout_meta_html_extra_css_edit">
		    <p><?php _e('<strong>CSS</strong> - This is used to add custom CSS to a View layout.', 'wpv-views'); ?></p>
		    <textarea name="_wpv_settings[layout_meta_html_css]" id="wpv_layout_meta_html_css" cols="97" rows="10"><?php echo isset($view_settings['layout_meta_html_css']) ? $view_settings['layout_meta_html_css'] : ''; ?></textarea>
		    <div id="wpv_layout_meta_html_extra_css_notice" class="wpv_form_notice" style="display:none;"><?php _e('* These updates will take effect when you save the view.', 'wpv-views'); ?></div>
		    <p><a style="cursor:pointer;margin-bottom:10px;" id="wpv_layout_meta_html_extra_css_close" onclick="wpv_view_layout_meta_html_extra_css_close()"><strong><?php _e('Close CSS editor', 'wpv-views'); ?></strong></a></p>
		</div>
	    </div>
	    <div id="wpv_layout_meta_html_extra_js" style="margin-top:15px;">
		  <input type="hidden" name="_wpv_settings[layout_meta_html_state][js]" id="wpv_layout_meta_html_extra_js_state" value="<?php echo isset($view_settings['layout_meta_html_state']['js']) ? $view_settings['layout_meta_html_state']['js'] : 'off'; ?>" />
		  <input type="button" class="button-secondary wpv_layout_meta_html_extra_js_edit" onclick="wpv_view_layout_meta_html_extra(this)" value="<?php _e('Edit JS', 'wpv-views'); ?>" />
		  <div id="wpv_layout_meta_html_extra_js_edit">
		    <p><?php _e('<strong>JS</strong> - This is used to add custom javascript to a View layout.', 'wpv-views'); ?></p>
		    <textarea name="_wpv_settings[layout_meta_html_js]" id="wpv_layout_meta_html_js" cols="97" rows="10"><?php echo isset($view_settings['layout_meta_html_js']) ? $view_settings['layout_meta_html_js'] : ''; ?></textarea>
                    <div id="wpv_layout_meta_html_extra_js_notice" class="wpv_form_notice" style="display:none;"><?php _e('* These updates will take effect when you save the view.', 'wpv-views'); ?></div>
                    <p><a style="cursor:pointer;margin-bottom:10px;" id="wpv_layout_meta_html_extra_js_close" onclick="wpv_view_layout_meta_html_extra_js_close()"><strong><?php _e('Close JS editor', 'wpv-views'); ?></strong></a></p>
		</div>
	    </div>
	    <div id="wpv_layout_meta_html_extra_img" style="margin-top:15px;">
            <?php global $post; ?>
		<input type="hidden" name="_wpv_settings[layout_meta_html_state][img]" id="wpv_layout_meta_html_extra_img_state" value="<?php echo isset($view_settings['layout_meta_html_state']['img']) ? $view_settings['layout_meta_html_state']['img'] : 'off'; ?>" />
		<input type="button" class="button-secondary wpv_layout_meta_html_extra_img_edit" onclick="wpv_view_layout_meta_html_extra(this)" value="<?php _e('Manage Media', 'wpv-views'); ?>" />
		<div id ="wpv_layout_meta_html_extra_img_edit">
		    <p><?php _e('<strong>Media</strong> - This is used to add images to a View output.', 'wpv-views'); ?></p>
		    <input type="button" class="button-secondary wpv_layout_meta_html_extra_img_upload" onclick="tb_show('<?php _e('Upload images'); ?>', 'media-upload.php?post_id=<?php echo $post->ID; ?>&type=image&wpv-media-insert=1&TB_iframe=true');return false;" value="<?php _e('Add Media', 'wpv-views'); ?>" />
		    <?php 
			$args = array(
				'post_type' => 'attachment',
				'numberposts' => null,
				'post_status' => null,
				'post_parent' => $post->ID
			); 
			$attachments = get_posts($args);
			if ($attachments) { ?>
			<div class="media-list">
				<p>Copy the links of the media items and paste into the meta HTML and CSS editors. You can use full URLs. When exporting and importing this View to another site, all URLs will be adjusted.</p>
				<table class="wpv_table_attachments widefat">
				<thead>
				<tr>
				<th><?php _e('Thumbnail', 'wpv-views'); ?></th>
				<th><?php _e('URL', 'wpv-views'); ?></th>
				</tr>
				</thead>
				<?php
					foreach ($attachments as $attachment) {
						$type = get_post_mime_type($attachment->ID);
						$icon = wp_mime_type_icon($type);
						if ( $type == 'image/gif' || $type == 'image/jpeg' || $type == 'image/png' ) {
							$thumb = '<img src="' .  $attachment->guid . '" alt="' . $attachment->post_title . '" width="60" height="60" />';
						} else {
							$thumb = '<img src="' . $icon . '" />';
						}
						?>
						<tr>
						<td><?php echo $thumb; ?></td>
						<td><a href="<?php echo $attachment->guid;?>"><?php echo $attachment->guid;?></a></td>
						</tr>
					<?php } ?>
				</table>
				<p><input type="button" class="button-secondary wpv_layout_meta_html_extra_img_edit_existing" onclick="tb_show('<?php _e('Edit media items'); ?>', 'media-upload.php?post_id=<?php echo $post->ID; ?>&type=image&tab=gallery&wpv-media-edit=1&TB_iframe=true');return false;" value="<?php _e('Edit media items', 'wpv-views'); ?>" /></p>
			</div>
			<?php } else { ?>
				<div class="media-list" style="display:none;">
				<p>Copy the links of the media items and paste into the meta HTML and CSS editors. You can use full URLs. When exporting and importing this View to another site, all URLs will be adjusted.</p>
				<table class="wpv_table_attachments widefat"></table>
				<p><input type="button" class="button-secondary wpv_layout_meta_html_extra_img_edit_existing" onclick="tb_show('<?php _e('Edit media items'); ?>', 'media-upload.php?post_id=<?php echo $post->ID; ?>&type=image&tab=gallery&wpv-media-edit=1&TB_iframe=true');return false;" value="<?php _e('Edit media items', 'wpv-views'); ?>" /></p>
				</div>			
			<?php } ?>
                    <div id="wpv_layout_meta_html_extra_img_notice" class="wpv_form_notice" style="display:none;"><?php _e('* These updates will take effect when you save the view.', 'wpv-views'); ?></div>
                    <p><a style="cursor:pointer;margin-bottom:10px;" id="wpv_layout_meta_html_extra_img_close" onclick="wpv_view_layout_meta_html_extra_img_close()"><strong><?php _e('Close Media manager', 'wpv-views'); ?></strong></a></p>
		</div>
            </div>
        </div>
    <?php
}

class View_layout_field { // NOT SURE IF DEPRECATED
    protected $type;
    protected $prefix;
    protected $suffix;
    protected $edittext;
    
    function __construct($type, $prefix = "", $suffix = "", $row_title = "", $edittext = "", $types_field_name = "", $types_field_data = ""){
        
        $this->type = $type;
        $this->prefix = $prefix;
        $this->suffix = $suffix;
        $this->row_title = $row_title;
        $this->edittext = $edittext;
        $this->types_field_name = $types_field_name;
        $this->types_field_data = $types_field_data;
    }
    
    function render_to_table($index) {
        global $wpv_shortcodes, $WPV_templates, $WP_Views;
        
        $view_template = null;
        $view = null;
        $view_type = null;
        
        if (strpos($this->type, 'wpv-post-field - ') === 0) {
            $name = substr($this->type, strlen('wpv-post-field - '));
            $title = $name;
        } elseif ($this->type == 'types-field') {
            $name = $this->type;
            $title = 'Types - ' . $this->types_field_name;
        } elseif (strpos($this->type, 'types-field - ') === 0) {
            $name = substr($this->type, strlen('types-field - '));
            $title = $name;
        } elseif(strpos($this->type, 'wpvtax') === 0) {
        	// $name = substr($this->type, strlen('wpvtax-'));
        	$name = 'Taxonomy - '. $this->type;
            $title = $name;
        } elseif (strpos($this->type, 'wpv-post-body ') === 0) {
            $name = $wpv_shortcodes['wpv-post-body'][1];
            $parts = explode(' ', $this->type);
            if (isset($parts[1])) {
                $view_template = $parts[1];
            }
            $title = $name;
        } elseif (strpos($this->type, WPV_TAXONOMY_VIEW . ' ') === 0) {
            $name = 'Taxonomy View';
            $parts = explode(' ', $this->type);
            if (isset($parts[1])) {
                $view = $parts[1];
                $view_type = 'taxonomy';
            }
            $title = $name;
        } elseif (strpos($this->type, WPV_POST_VIEW . ' ') === 0) {
            $name = 'Post View';
            $parts = explode(' ', $this->type);
            if (isset($parts[1])) {
                $view = $parts[1];
                $view_type = 'post';
            }
            $title = $name;
        } else {
            $name = $wpv_shortcodes[$this->type][1];
            $title = $name;
        }
        
        ?>
        <td width="120px"><input class="wpv_field_prefix" id="wpv_field_prefix_<?php echo $index; ?>" type="text" value="<?php echo htmlspecialchars($this->prefix); ?>" name="_wpv_layout_settings[fields][prefix_<?php echo $index; ?>]" /></td>
        <td width="120px">
            <span id="wpv_field_name_<?php echo $index; ?>"><?php echo $title; ?></span>
            <input id="wpv_field_name_hidden_<?php echo $index; ?>" type="hidden" value="<?php echo $name; ?>" name="_wpv_layout_settings[fields][name_<?php echo $index; ?>]" />
            <input id="wpv_types_field_name_hidden_<?php echo $index; ?>" type="hidden" value="<?php echo $this->types_field_name; ?>" name="_wpv_layout_settings[fields][types_field_name_<?php echo $index; ?>]" />
            <input id="wpv_types_field_data_hidden_<?php echo $index; ?>" type="hidden" value="<?php echo esc_js($this->types_field_data); ?>" name="_wpv_layout_settings[fields][types_field_data_<?php echo $index; ?>]" />
        </td>
        <?php
        $row_title = $this->row_title;
        ?>
        <td class="row-title hidden" width="120px"><input type="text" id="wpv_field_row_title_<?php echo $index; ?>" value="<?php echo $row_title; ?>" name="_wpv_layout_settings[fields][row_title_<?php echo $index; ?>]" /></td>
        <td width="120px"><input class="wpv_field_suffix"  id="wpv_field_suffix_<?php echo $index; ?>" type="text" value="<?php echo htmlspecialchars($this->suffix); ?>" name="_wpv_layout_settings[fields][suffix_<?php echo $index; ?>]" /></td>
        <?php
    }
    
    function render_table_row_attributes($view_settings) {
        
        if (strpos($this->type, 'wpv-taxonomy-') === 0 || strpos($this->type, WPV_TAXONOMY_VIEW) === 0) {
            // taxonomy type.
            $output = 'class="wpv-taxonomy-field"';
            if ($view_settings['query_type'][0] != 'taxonomy') {
                $output .= ' style="display:none"';
            }
        } else {
            // post type
            $output = 'class="wpv-post-type-field"';
            if ($view_settings['query_type'][0] != 'posts') {
                $output .= ' style="display:none"';
            }
        }
        
        return $output;
        
    }
    
    function get_body_template() {
        if (strpos($this->type, 'wpv-post-body ') === 0) {
            $parts = explode(' ', $this->type);
            return $parts[1];
        } else {
            return -1;
        }
    }
    
}

$link_layout_number = 0;

function view_layout_fields_to_classes($fields) {
    $output = array();
    for ($i = 0; $i < sizeof($fields); $i++) {
        if (!isset($fields["name_{$i}"])) {
            break;
        }
        $output[] = new View_layout_field($fields["name_{$i}"],
                                          $fields["prefix_{$i}"],
                                          $fields["suffix_{$i}"],
                                          isset($fields["row_title_{$i}"]) ? $fields["row_title_{$i}"] : '',
        								  isset($fields["edittext_{$i}"]) ? $fields["edittext_{$i}"] : '',
                                          isset($fields["types_field_name_{$i}"]) ? $fields["types_field_name_{$i}"] : '',
                                          isset($fields["types_field_data_{$i}"]) ? $fields["types_field_data_{$i}"] : '');
        
    }
    return $output;
}
function view_layout_fields($post, $view_layout_settings) {
    global $WP_Views;
    $view_settings = $WP_Views->get_view_settings($post->ID);
    if (isset($view_layout_settings['fields'])) {
        $view_layout_settings['fields'] = view_layout_fields_to_classes($view_layout_settings['fields']);
    } else {
        $view_layout_settings['fields'] = array();
    }
    view_layout_javascript();
    global $WPV_templates;
    $template_selected = 0;
    foreach ($view_layout_settings['fields'] as $field) {
        $posible_template = $field->get_body_template();
        if ($posible_template >= 0) {
            $template_selected = $posible_template;
            break;
        }
    }
    ?>
    <div id="view_layout_fields" class="view_layout_fields">
        <p id="view_layout_fields_to_include"><strong><?php echo __('Fields to include:', 'wpv-views'); ?></strong></p>
        <p id="view_layout_add_field_message_1"><?php echo __("Click on <strong>Add field</strong> to insert additional fields. Drag them to reorder, or delete fields that you don't need.", 'wpv-views'); ?></p>
        <p id="view_layout_add_field_message_2" style="display:none"><?php echo __("Click on <strong>Add field</strong> to insert fields to this View.", 'wpv-views'); ?></p>
        
        <table id="view_layout_fields_table" class="widefat fixed">
            <thead>
                <tr>
                    <th width="20px"></th><th width="120px"><?php echo __('Prefix', 'wpv-views'); ?></th><th width="220px"><?php echo __('Field', 'wpv-views'); ?></th><th class="row-title hidden" width="120px"><?php echo __('Row Title', 'wpv-views'); ?></th><th width="120px"><?php echo __('Suffix', 'wpv-views'); ?></th><th width="16px"></th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th></th><th></th><th></th><th class="row-title hidden"></th><th></th><th></th>
                </tr>
            </tfoot>
            
            <tbody>
                <?php
                $count = sizeof($view_layout_settings['fields']);
                foreach($view_layout_settings['fields'] as $index => $field) {
                    ?>
                    <tr id="wpv_field_row_<?php echo $index; ?>" <?php echo $field->render_table_row_attributes($view_settings); ?>>
                    
                        <td width="20px"><img src="<?php echo WPV_URL . '/res/img/delete.png'; ?>" onclick="on_delete_wpv(<?php echo $index; ?>)" style="cursor: pointer" /></td><?php $field->render_to_table($index); ?><td width="16px"><img src="<?php echo WPV_URL; ?>/res/img/move.png" class="move" style="cursor: move;" /></td>
                    
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        
        </table>
        <br />
    </div>
    
    <?php
        $show = $view_settings['query_type'][0] == 'posts';
    ?>
    <input class="button-secondary wpv_add_fields_button" type="button" value="<?php echo __('Add field', 'wpv-views'); ?>" name="wpv-layout-add-field" <?php if($show) {echo '';} else {echo ' style="display:none"';} ?> />
    <div id="add_field_popup" style="display:none; overflow: auto;">

        <?php
        global $link_layout_number;
        $link_layout_number = 0;
        $WP_Views->editor_addon->add_form_button('', 'wpv_layout_meta_html_content', false);
        
        ?>

    </div>  

	<?php // echo $WP_Views->editor_addon->add_form_button('', '#wpv_layout_meta_html_content', false); ?>
    <?php // Add a popup for taxonomy fields ?>
    
    <div id="add_taxonomy_field_popup" style="display:none">

        <table id="wpv_taxonomy_field_popup_table" width="100%">
        <tr>
        <?php
        global $link_layout_number;
        $link_layout_number = 0;
        ?>
        </tr>
        </table>

    </div>  

    <script type="text/javascript">
		jQuery('.wpv_add_fields_button').click(function(){
			setTimeout(searchFocus,300);
		});
		function searchFocus(){
			jQuery('#add_field_popup').find('.search_field').focus();
		}
        var wpv_shortcodes = new Array();
        <?php
            $current_index = 0;
        ?>
        wpv_shortcodes[<?php echo $current_index++; ?>] = new Array('Taxonomy View', '<?php echo WPV_TAXONOMY_VIEW; ?>');
        wpv_shortcodes[<?php echo $current_index++; ?>] = new Array('Post View', '<?php echo WPV_POST_VIEW; ?>');
        <?php 
        if (defined('WPV_WOOCOMERCE_VIEWS_SHORTCODE')) {
        ?>
        wpv_shortcodes[<?php echo $current_index; ?>] = new Array('Add to cart button', '<?php echo WPV_WOOCOMERCE_VIEWS_SHORTCODE; ?>');
        <?php 
        }
        ?>
        <?php 
        if (defined('WPV_WOOCOMERCEBOX_VIEWS_SHORTCODE')) {
        ?>
        wpv_shortcodes[<?php echo $current_index++; ?>] = new Array('Add to cart box', '<?php echo WPV_WOOCOMERCEBOX_VIEWS_SHORTCODE; ?>');
        <?php 
        }
        ?>
        var wpv_view_template_text = "<?php echo esc_js(__('Content template', 'wpv-views')); ?>";
        var wpv_taxonomy_view_text = "<?php echo esc_js(__('Taxonomy View', 'wpv-views')); ?>";
        var wpv_post_view_text = "<?php echo esc_js(__('Post View', 'wpv-views')); ?>";
        var wpv_add_field_text = "<?php echo esc_js(__('Field', 'wpv-views')); ?>";
        var wpv_add_taxonomy_text = "<?php echo esc_js(__('Taxonomy', 'wpv-views')); ?>";
    </script>
    <?php
        $show = $view_settings['query_type'][0] == 'taxonomy';
    ?>
    <input alt="#TB_inline?inlineId=add_taxonomy_field_popup" class="thickbox button-secondary wpv_add_taxonomy_fields_button" type="button" value="<?php echo __('Add field', 'wpv-views'); ?>" name="Add a taxonomy field" <?php if($show) {echo '';} else {echo ' style="display:none"';} ?> />
    
    <?php
        $show = $view_settings['query_type'][0] == 'posts' ? '' : 'style="display:none"';
    ?>
    <span id="wpv-layout-help-posts" <?php echo $show;?>><i><?php echo __('Want to add complex fields?', 'wpv-views') . '&nbsp;' .
                                                                               '<a class="wpv-help-link" href="http://wp-types.com/user-guides/using-a-view-template-in-a-view-layout/" target="_blank">' .
                                                                               __('Learn about using Content Templates to customize fields.', 'wpv-views') .
                                                                               ' &raquo;</a>'; ?></i></span>
    <?php
        $show = $view_settings['query_type'][0] == 'taxonomy' ? '' : 'style="display:none"';
    ?>
    <span id="wpv-layout-help-taxonomy" <?php echo $show;?>><i><?php echo sprintf(__('Want to display posts that belong to this taxonomy? Learn about %sinserting child Views to Taxonomy Views%s.', 'wpv-views'),
                                                                                  '<a href="http://wp-types.com/user-guides/using-a-child-view-in-a-taxonomy-view-layout/" target="_blank">',
                                                                                  ' &raquo;</a>'); ?></i></span>

    <?php
        // Warn if Types is less than 1.0.2
        // We need at least 1.0.2 for the Types popups to work when adding fields.
        if (defined('WPCF_VERSION') && version_compare(WPCF_VERSION, '1.0.2', '<')) {
            echo '<br /><p style="color:red;"><strong>';
            _e('* Views requires Types 1.0.2 or greater for best results when adding fields.', 'wpv-views');
            echo '</strong></p>';
        }
    ?>

    
    <?php
}
function view_layout_javascript() {
    global $pagenow;
    ?>
    <script type="text/javascript">
        var wpv_layout_constants = {
            'WPV_TAXONOMY_VIEW' : '<?php echo WPV_TAXONOMY_VIEW;?>',
            'WPV_POST_VIEW': '<?php echo WPV_POST_VIEW;?>'
        };
        var wpv_url = '<?php echo WPV_URL; ?>';
        var wpv_field_text = '<?php echo esc_js(__('Field', 'wpv-views')); ?> - ';
        var wpv_confirm_layout_change = '<?php echo esc_js(__("Are you sure you want to change the layout?", 'wpv-views')); echo "\\n\\n"; echo esc_js(__("It appears that you made modifications to the layout.", 'wpv-views')); ?>';
        var no_post_results_text = "[wpv-no-posts-found][wpml-string context=\"wpv-views\"]<strong>No posts found</strong>[/wpml-string][/wpv-no-posts-found]";
        var no_taxonomy_results_text = "[wpv-no-taxonomy-found][wpml-string context=\"wpv-views\"]<strong>No taxonomy found</strong>[/wpml-string][/wpv-no-taxonomy-found]";
    </script>
    <?php
}
function view_layout_additional_js($post, $view_layout_settings) {
    $js = isset($view_layout_settings['additional_js']) ? strval($view_layout_settings['additional_js']) : '';
    ?>
    <br /><br />
    <fieldset><legend><?php _e('Additional Javascript files to be loaded with this View (comma separated): ', 'wpv-views'); ?></legend>
    <input type="text" name="_wpv_layout_settings[additional_js]" style="width:100%;" value="<?php echo $js; ?>" />
    </fieldset>
    <?php
}
function save_view_layout_settings($post_id) {
    global $view_fields;
    if(isset($_POST['_wpv_layout_settings'])){
        if (!isset($_POST['_wpv_layout_settings']['fields'])) {
            $_POST['_wpv_layout_settings']['fields'] = array();
        }
        $fields = $_POST['_wpv_layout_settings']['fields'];
        foreach ($fields as $index => $value) {
            if (strpos($index, 'name_') === 0) {
                if (strpos($value, __('Field', 'wpv-views') . ' - ') === 0) {
                    $fields[$index] = 'wpv-post-field' . ' - ' . $value;
                } else if (strpos($value, 'types-field') === 0) {
                    // do nothing.
                } else if (strpos($value, 'Types - ') === 0) {
                    $fields[$index] = 'types-field' . ' - ' . $value;
                } else if (strpos($value, 'Taxonomy - ') === 0) {
                	$fields[$index] = substr($value, 11);
                } else {
                    $fields[$index] = wpv_get_shortcode($value);
                    if ($fields[$index] == 'wpv-post-body') {
                        $row = substr($index, 5);
                        if (isset($_POST['views_template_' . $row]) && $_POST['views_template_' . $row] != 0) {
                            $fields[$index] .= ' ' . $_POST['views_template_' . $row];
                        }
                    }

                    // Check for a taxonomy and post view (A view for laying out the child terms)
                    foreach($view_fields as $view_type => $view_prefix) {
                        if ($fields[$index] == $view_type) {
                            $row = substr($index, 5);
                            if (isset($_POST[$view_prefix . $row]) && $_POST[$view_prefix . $row] != 0) {
                                $fields[$index] .= ' ' . $_POST[$view_prefix . $row];
                            }
                        }
                    }
                }
            } else if (strpos($index, 'suffix_') == 0 || strpos($index, 'prefix_') == 0) {
                $fields[$index] = htmlspecialchars_decode($value);
            }
        }
        $_POST['_wpv_layout_settings']['fields'] = $fields;
        if (!isset($_POST['_wpv_layout_settings']['include_field_names'])) {
            // set it to 0 if it's not in the $_POST data
            $_POST['_wpv_layout_settings']['include_field_names'] = 0;
        }
        update_post_meta($post_id, '_wpv_layout_settings', $_POST['_wpv_layout_settings']);
    }
}

function short_code_taxonomy_menu_callback($index, $cf_key, $function_name, $menu, $shortcode) {
    global $link_layout_number;
    static $taxonomy_view_started = false;
    static $post_view_started = false;
    static $suffix = '';
    if (!$taxonomy_view_started && $menu == esc_js(__('Taxonomy View', 'wpv-views'))) {
        echo '</tr><tr><td></td>';
        echo '</tr><tr><td></td></tr><tr>';
        echo '<td colspan="2"><strong>' . $menu . '</strong> ' . __(' - Use to layout child taxonomy terms', 'wpv-views') . '</td>';
        echo '</tr><tr>';
        $link_layout_number = 0;
        $taxonomy_view_started = true;
        $suffix = ' - ' . __('Taxonomy View', 'wpv-views');
    }
    if (!$post_view_started && $menu == esc_js(__('Post View', 'wpv-views'))) {
        echo '</tr><tr><td></td>';
        echo '</tr><tr><td></td></tr><tr>';
        echo '<td colspan="2"><strong>' . $menu . '</strong> ' . __(' - Use to layout posts for the current taxonomy term', 'wpv-views') . '</td>';
        echo '</tr><tr>';
        $link_layout_number = 0;
        $post_view_started = true;
        $suffix = ' - ' . __('Post View', 'wpv-views');
    }
    if (!($link_layout_number % 2)) {
        if ($link_layout_number != 0) {
            echo '</tr><tr>' ;
        }
        
    }
    echo '<td><a style="cursor: pointer" onclick="on_add_field_wpv(\''. $menu . '\', \'' . esc_js($cf_key) . '\', \'' . base64_encode($cf_key . $suffix) . '\')">';
    echo $cf_key;
    echo '</a></td>';
    $link_layout_number++;
}
function short_code_variable_callback($index, $cf_key, $function_name, $menu, $shortcode) {
    ?>
        wpv_shortcodes[<?php echo $index?>] = new Array('<?php echo esc_js($cf_key);?>', '<?php echo esc_js($shortcode); ?>');
    <?php
}

add_filter('wpv_view_settings_save', 'wpv_filter_controls_save');
function wpv_filter_controls_save($view_settings) { // MAYBE DEPRECATED
    if (isset($view_settings['filter_controls_enable'])) {
        // determine which items are checked.
        $result = array();
        $enabled = $view_settings['filter_controls_enable'];
        $skip_next = false;
        foreach($enabled as $enable) {
            if (!$skip_next) {
                if ($enable != '0') {
                    $result[] = true;
                    $skip_next = true;
                } else {
                    $result[] = false;
                    $skip_next = false;
                }
            } else {
                $skip_next = false;
            }
        }
        $view_settings['filter_controls_enable']= $result;
    }
    return $view_settings;
}

/***********************
* Filters general functions
*******************************/
// I think that DEPRECATED: there is no wpv_view_settings_save action anymore
add_filter('wpv_view_settings_save', 'wpv_post_types_defaults_save', 10, 1);
function wpv_post_types_defaults_save($view_settings) {// TODO this only fires when wpv_view_settings_save which I'm not sure is fired at all
    // we need to set 0 for the checkboxes that aren't checked and are missing for the $_POST.
    $defaults = array('post_type_dont_include_current_page' => 0);
    $view_settings = wpv_parse_args_recursive($view_settings, $defaults);
    return $view_settings;
}

// I think that DEPRECATED: there is no wpv_view_settings_save action anymore TODO
add_filter('wpv_view_settings_save', 'wpv_taxonomy_defaults_save', 10, 1);
function wpv_taxonomy_defaults_save($view_settings) {
    global $taxonomy_checkboxes_defaults;
    // we need to set 0 for the checkboxes that aren't checked and are missing for the $_POST.
    $defaults = array();
    foreach($taxonomy_checkboxes_defaults as $key => $value) {
        $defaults[$key] = 0;
    }
    $view_settings = wpv_parse_args_recursive($view_settings, $defaults);
    return $view_settings;
}

/**********************
* Pagination
************************/

if (isset($_GET['wpv-pagination-spinner-media-insert'])) {// DEPRECATED now we use the Media Manager from WordPress
    // Add JS
    add_action('admin_head', 'wpv_pagination_spinner_media_admin_head');
    // Filter media TABs
    add_filter('media_upload_tabs',
            'wpv_pagination_spinner_media_upload_tabs_filter');
    // Add button
    add_filter('attachment_fields_to_edit',
            'wpv_pagination_spinner_attachment_fields_to_edit_filter', 10, 2);
}

/**
 * get the pagination display returned by ajax.
 */

function wpv_ajax_pagination() { // DEPRECATED
    if (wp_verify_nonce($_POST['wpv_nonce'], 'wpv_pagination_nonce')
            && !empty($_POST['_wpv_settings'])) {
        $settings['posts_per_page'] = $_POST['_wpv_settings']['posts_per_page'];
        if (isset($_POST['_wpv_settings']['include_page_selector_control'])) {
            $settings['include_page_selector_control'] = $_POST['_wpv_settings']['include_page_selector_control'];
        }
        if (isset($_POST['_wpv_settings']['include_prev_next_page_controls'])) {
            $settings['include_prev_next_page_controls'] = $_POST['_wpv_settings']['include_prev_next_page_controls'];
        }
        $settings['pagination'] = $_POST['_wpv_settings']['pagination'];
        $settings['ajax_pagination'] = $_POST['_wpv_settings']['ajax_pagination'];
        $settings['rollover'] = $_POST['_wpv_settings']['rollover'];

        $settings = apply_filters('wpv_view_settings_save', $settings);

        $view_settings = apply_filters('wpv_view_settings', $settings);
        
        //wpv_pagination_admin($view_settings);
    }
    
    die();
    
}

/**
 * Media popup JS.
 */
function wpv_pagination_spinner_media_admin_head() { // DEPRECATED

    ?>
    <script type="text/javascript">
        function wpvPaginationSpinnerMediaTrigger(guid, type) {
            window.parent.jQuery('#wpv-pagination-spinner-image').val(guid);
            window.parent.jQuery('#wpv-pagination-spinner-image-preview').attr('src', guid);
            window.parent.jQuery('#TB_closeWindowButton').trigger('click');
        }
    </script>
    <style type="text/css">
        tr.submit { display: none; }
    </style>
    <?php
}

/**
 * Adds 'Spinner' column to media item table.
 * 
 * @param type $form_fields
 * @param type $post
 * @return type 
 */
function wpv_pagination_spinner_attachment_fields_to_edit_filter($form_fields, $post) {// DEPRECATED
    $type = (strpos($post->post_mime_type, 'image/') !== false) ? 'image' : 'file';
    $form_fields['wpcf_fields_file'] = array(
        'label' => __('Views Pagination', 'wpv-views'),
        'input' => 'html',
        'html' => '<a href="#" title="' . $post->guid
        . '" class="wpv-pagination-spinner-insert-button'
        . ' button-primary" onclick="wpvPaginationSpinnerMediaTrigger(\''
        . $post->guid . '\', \'' . $type . '\')">'
        . __('Use as spinner image', 'wpv-views') . '</a><br /><br />',
    );
    return $form_fields;
}

/**
 * Filters media TABs.
 * 
 * @param type $tabs
 * @return type 
 */
function wpv_pagination_spinner_media_upload_tabs_filter($tabs) { // DEPRECATED
    unset($tabs['type_url']);
    return $tabs;
}

/*
* ---------------------
* TEMPORARY FUNCTIONS
* ---------------------
*/

/**
* _wpv_deprecated_remove_admin_bar_toolset
*
* Workaround to remove the Toolset Admin Bar Menu until we can be sure that the class containing it applies the new toolset_filter_toolset_admin_bar_menu_disable filter
*
* Added in Views 1.10 and common 1.7
*
* @toremove on a couple of join releases
*/

add_action( 'init', '_wpv_deprecated_remove_admin_bar_toolset' );

function _wpv_deprecated_remove_admin_bar_toolset() {
	global $toolset_admin_bar_menu;
	$toolset_options = get_option( 'toolset_options', array() );
	$toolset_admin_bar_menu_remove = ( isset( $toolset_options['show_admin_bar_shortcut'] ) && $toolset_options['show_admin_bar_shortcut'] == 'off' ) ? true : false;
	if ( $toolset_admin_bar_menu_remove ) {
		remove_action( 'admin_bar_menu', array( $toolset_admin_bar_menu, 'admin_bar_menu' ), 99 );
	}
}