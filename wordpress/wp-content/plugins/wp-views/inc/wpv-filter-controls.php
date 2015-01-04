<?php


function wpv_filter_controls_admin($view_settings){ // DEPRECATED
    
    $select = '<select name="" >';
    $select .= '<option value="types-auto">' . __('Types auto style', 'wpv-views') . '&nbsp;</option>';
    $select .= '<option value="radios">' . __('Radio', 'wpv-views') . '&nbsp;</option>';
    $select .= '<option value="checkbox">' . __('Checkbox', 'wpv-views') . '&nbsp;</option>';
    $select .= '<option value="checkboxes">' . __('Checkboxes', 'wpv-views') . '&nbsp;</option>';
    $select .= '<option value="select">' . __('Select', 'wpv-views') . '&nbsp;</option>';
    $select .= '<option value="textfield">' . __('Text field', 'wpv-views') . '&nbsp;</option>';
    $select .= '<option value="datepicker">' . __('Date picker', 'wpv-views') . '&nbsp;</option>';
    $select .= '</select>';

    $select_tax = '<select name="" class="tax-filter">'; // add a classname to taxonomy filter control input type
    $select_tax .= '<option value="checkboxes">' . __('Checkboxes', 'wpv-views') . '&nbsp;</option>';
    $select_tax .= '<option value="select">' . __('Select', 'wpv-views') . '&nbsp;</option>';
    $select_tax .= '</select>';

    $select_search = '<select name="" >';
    $select_search .= '<option value="textfield">' . __('Text field', 'wpv-views') . '&nbsp;</option>';
    $select_search .= '</select>';

    $select_submit = '<select name="" >';
    $select_submit .= '<option value="submit-button">' . __('Submit button', 'wpv-views') . '&nbsp;</option>';
    $select_submit .= '</select>';

    $view_settings = _wpv_initialize_url_param_controls($view_settings);

	wp_nonce_field('wpv_get_types_field_name_nonce', 'wpv_get_types_field_name_nonce');
    
    ?>
    
    <div id="wpv_filter_controls_admin_summary" <?php echo ($view_settings['query_type'][0] == 'posts' && count($view_settings['filter_controls_param'])) > 0 ? '' : 'style="display:none"'; ?>>
        <strong><?php _e('Parametric search controls: ', 'wpv-views') ?></strong>
        <input class="button-secondary" type="button" value="<?php echo __('Edit', 'wpv-views'); ?>" name="<?php echo __('Edit', 'wpv-views'); ?>" onclick="wpv_filter_controls_edit()"/>
    </div>

    <div id="wpv_filter_controls_admin_edit" style="background:<?php echo WPV_EDIT_BACKGROUND;?>;display:none">
        <div style="margin:20px;">
            <br />
            <p>
                <strong><?php _e('Search controls settings', 'wpv-views') ?></strong>
                <?php echo '&nbsp;<a class="wpv-help-link" href="' . WPV_ADD_FILTER_CONTROLS_LINK . '" target="_blank">' . sprintf(__('Learn more about parametric search', 'wpv-views')) . ' &raquo;</a>'; ?>
            </p>
            <table id="view_filter_controls_table" class="widefat fixed">
                <thead>
                    <tr>
                        <th><?php echo __('Enable', 'wpv-views'); ?></th>
                        <th width="170px"><?php echo __('Filter', 'wpv-views'); ?></th>
                        <th width="170px"><?php echo __('Label', 'wpv-views'); ?></th>
                        <th><?php echo __('Input type', 'wpv-views'); ?></th>
                        <th><?php echo __('Input values', 'wpv-views'); ?></th>
                        <th width="16px"></th>
                    </tr>
                </thead>
                
                <tfoot>
                    <tr>
                        <th></th><th></th><th></th><th></th><th></th><th></th>
                    </tr>
                </tfoot>
                
                <tbody>
                    <?php // Add a dummy row that we can copy when we add more controls via javascript. ?>
                    <tr style="display:none">
                        <td>
                            <input type="checkbox" name="_wpv_settings[filter_controls_enable][]" />
                            <input type="hidden" name="_wpv_settings[filter_controls_enable][]" value="0" />
                            <input type="hidden" name="_wpv_settings[filter_controls_param][]" />
                            <input type="hidden" name="_wpv_settings[filter_controls_mode][]" />
                        </td>
                        <td><input type="hidden" name="_wpv_settings[filter_controls_field_name][]" /><span></span></td>
                        <td><input type="text" width="100%" name="_wpv_settings[filter_controls_label][]" /></td>
                        <td><?php echo str_replace('name=""', 'name="_wpv_settings[filter_controls_type][]"', $select); ?></td>
                        <td><input type="hidden" name="_wpv_settings[filter_controls_values][]" /><input type="button" value="<?php _e('Edit', 'wpv-views'); ?>" class="button-secondary"/></td>
                        <td width="16px"><img src="<?php echo WPV_URL; ?>/res/img/move.png" class="move" style="cursor: move;" /></td>
                    </tr>
        
                    <?php
                    
                        for ($i = 0; $i < count($view_settings['filter_controls_param']); $i++) {
                            if ($view_settings['filter_controls_param'][$i] != '') {
                                $show_edit = '';
                                $filter = $view_settings['filter_controls_field_name'][$i] . ' (' . $view_settings['filter_controls_param'][$i] . ')';
                                
                                switch ($view_settings['filter_controls_mode'][$i]) {
                                    case 'cf':
                                        $new_select = str_replace('name=""', 'name="_wpv_settings[filter_controls_type][]"', $select);
                                        switch ($view_settings['filter_controls_type'][$i]) {
                                            case 'types-auto':
                                            case 'textfield':
					    case 'datepicker':
                                                $show_edit = ' style="display:none" ';
                                                break;
                                            
                                            default:
                                                break;
                                        }
                                        break;
                                    
                                    case 'tax':
                                        $new_select = str_replace('name=""', 'name="_wpv_settings[filter_controls_type][]"', $select_tax);
                                        switch ($view_settings['filter_controls_type'][$i]) {
                                            case 'checkboxes':
                                                $show_edit = ' style="display:none" ';
                                                break;
                                            default: // keep the Edit button for select input type
                                                break;
                                        }
                                        break;
                                    
                                    case 'search':
                                        $new_select = str_replace('name=""', 'name="_wpv_settings[filter_controls_type][]"', $select_search);
                                        $show_edit = ' style="display:none" ';
                                        break;

                                    case 'submit':
                                        $new_select = str_replace('name=""', 'name="_wpv_settings[filter_controls_type][]"', $select_submit);
                                        $show_edit = ' style="display:none" ';
                                        $filter = __('Submit button', 'wpv-views');
                                        break;
                                        
                                }
                                $new_select = str_replace('option value="' . $view_settings['filter_controls_type'][$i] . '"',
                                                          'option value="' . $view_settings['filter_controls_type'][$i] . '" selected="selected" ',
                                                          $new_select);
                                ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="_wpv_settings[filter_controls_enable][]" <?php echo $view_settings['filter_controls_enable'][$i] ? 'checked="checked"' : ''; ?> />
                                        <input type="hidden" name="_wpv_settings[filter_controls_enable][]" value="0" />
                                        <input type="hidden" name="_wpv_settings[filter_controls_param][]" value="<?php echo $view_settings['filter_controls_param'][$i]; ?>" />
                                        <input type="hidden" name="_wpv_settings[filter_controls_mode][]" value="<?php echo $view_settings['filter_controls_mode'][$i]; ?>" />
                                    </td>
                                    <td>
                                        <input type="hidden" name="_wpv_settings[filter_controls_field_name][]" value="<?php echo sanitize_text_field($view_settings['filter_controls_field_name'][$i]); ?>" /><span><?php echo sanitize_text_field($filter); ?></span>
                                    </td>
                                    <td><input type="text" width="100%" name="_wpv_settings[filter_controls_label][]" value="<?php echo $view_settings['filter_controls_label'][$i]; ?>" /></td>
                                    <td><?php echo $new_select; ?></td>
                                    <td><input type="hidden" name="_wpv_settings[filter_controls_values][]" value="<?php echo htmlspecialchars($view_settings['filter_controls_values'][$i], ENT_QUOTES); ?>" /><input type="button" value="<?php _e('Edit', 'wpv-views'); ?>" class="button-secondary" <?php echo $show_edit; ?> /></td>
                                    <td width="16px"><img src="<?php echo WPV_URL; ?>/res/img/move.png" class="move" style="cursor: move;" /></td>
                                </tr>
                        <?php }
                        }
                        
                        ?>
                    
                </tbody>
            </table>
            
            <br />
            <input class="button-primary" type="button" value="<?php echo __('OK', 'wpv-views'); ?>" name="<?php echo __('OK', 'wpv-views'); ?>" onclick="wpv_filter_controls_edit_ok()"/>
            <input class="button-secondary" type="button" value="<?php echo __('Cancel', 'wpv-views'); ?>" name="<?php echo __('Cancel', 'wpv-views'); ?>" onclick="wpv_filter_controls_edit_cancel()"/>
            
            <br />
            <br />
        </div>
    </div>
    <?php
    
}

/**
 * Initialize any url param controls that haven't been set already
 * This is for Views that were created before we had front end filters.
 *
 */

function _wpv_initialize_url_param_controls($view_settings) {
    
    if (function_exists('wpcf_admin_fields_get_fields')) {
        $fields = wpcf_admin_fields_get_fields();
    } else {
        $fields = array();
    }
    
    if (!isset($view_settings['filter_controls_param'])) {
        $view_settings['filter_controls_field_name'] = array();
        $view_settings['filter_controls_param'] = array();
        $view_settings['filter_controls_enable'] = array();
        $view_settings['filter_controls_label'] = array();
        $view_settings['filter_controls_values'] = array();
        $view_settings['filter_controls_type'] = array();
        $view_settings['filter_controls_mode'] = array();
    }

    $url_params = wpv_custom_fields_get_url_params($view_settings);
    $url_params = array_merge($url_params, wpv_taxonomy_get_url_params($view_settings));
    $search_param = wpv_search_get_url_params($view_settings);
    $url_params = array_merge($url_params, $search_param);
    
    foreach($url_params as $url_param) {
        // see if it's already set
        $exists = false;
        foreach($view_settings['filter_controls_param'] as $param) {
            if ($param == $url_param['param']) {
                $exists = true;
                break;
            }
        }
        
        if (!$exists) {
            // Doesn't exist so we add the control.
            
            $view_settings['filter_controls_field_name'][] = $url_param['name'];
            $view_settings['filter_controls_param'][] = $url_param['param'];
            $view_settings['filter_controls_enable'][] = 0;
            
            $label = $url_param['param'];
            $type = 'text';
            switch ($url_param ['mode']) {
                case 'cf':
                    foreach ($fields as $field) {
                        if ($url_param['name'] == wpcf_types_get_meta_prefix($field) . $field['slug']) {
                            $label = $field['name'];
                            $type = 'types-auto';
                            break;
                        }
                    }
                    break;
                
                case 'tax':
                    $label = $url_param['cat']->labels->name;
                    break;
                
                case 'search':
                    $label = $url_param['name'];
                    break;
            }
            
            $view_settings['filter_controls_label'][] = $label;
            
            $view_settings['filter_controls_values'][] = '';
            $view_settings['filter_controls_type'][] = $type;
            
            $view_settings['filter_controls_mode'][] = $url_param['mode'];
        }
    }
    
    return $view_settings;
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