<?php

/**
* wpv-conditional.php
*
* Manages the wpv-conditional shortcode
*
* @since 1.10
*/

/**
 * AJAX action to fill wpv-conditional shortcode
 *
 * AJAX action to fill wpv-conditional shortcode modal window with all needed
 * content
 *
 * @since 1.9.0
 *
 */
add_action('wp_ajax_wpv_shortcode_gui_dialog_conditional_create', 'wp_ajax_wpv_shortcode_gui_dialog_conditional_create');

/**
 * Content of wpv-conditional modal window.
 *
 * Content of wpv-conditional modal window used when we editing a entryn and
 * setup a wpv-conditional shortcode using Views Shortcode GUI API
 *
 * @since 1.9.0
 *
 * @param string $id Shortcode id
 * @param array $ shortcode data
 * @param array $ css classes
 * @param array $post_type post type
 *
 * @return string Content of modal window for wpv-shortcode.
 */
function wpv_shortcode_wpv_conditional_callback( $id, $data = array(), $classes = array(), $post_type = '' ) {
	global $WP_Views;
    /*if ( empty($post_type) ) {
        return __('Wrong post type', 'wpv-views');
    }*/
	/*
	if ( empty( $post_type ) ) {
		$post_type_slug = '';
	} else {
		if ( isset( $post_type->slug ) ) {
			$post_type_slug = $post_type->slug;
		} elseif ( isset( $post_type->name ) ) {
			$post_type_slug = $post_type->name;
		} else {
			$post_type_slug = '';
		}
	}
	*/
    
    $content = '';
    $fields = array(
        'types' => array(
            'label' => __('Types Fields', 'wpv-views'),
        ),
        'custom-fields' => array(
            'label' => __('Custom Fields', 'wpv-views'),
        ),
        'views-shortcodes' => array(
            'label' => __('Views Shortcodes', 'wpv-views'),
        ),
        'custom-shortcodes' => array(
            'label' => __('Custom Shortcodes', 'wpv-views'),
        ),
        'custom-functions' => array(
            'label' => __('Custom Functions', 'wpv-views'),
        ),
    );
    foreach( array_keys($fields) as $key ) {
        $fields[$key]['fields'] = array();
        $fields[$key]['slug'] = $key;
    }

    /**
     * get types custom fields
     */
	 /*
    $groups = apply_filters('wpcf_get_groups_by_post_type', array(), $post_type);
    if ( !empty($groups) ) {
        foreach( $groups as $group_id => $group_data ) {
            $fields_from_group = apply_filters('wpcf_fields_by_group', array(), $group_id);
            foreach ( $fields_from_group as $field_slug => $field_data ) {
                $fields['types']['fields'][$field_slug] = array(
                    'label' => $field_data['name'],
                    'slug' => sprintf('$(%s)', $field_data['slug']),
                    'type' => $field_data['type'],
                );
            }
        }
    }
	*/

	$post_meta_keys = $WP_Views->get_meta_keys();
	//$post_meta_keys = array_diff( $post_meta_keys, apply_filters( 'wpcf_get_all_fields_slugs', array() ) );
    if ( !empty( $post_meta_keys ) ) {
        foreach( $post_meta_keys as $key ) {
            if ( empty($key) ) {
                continue;
            }
			if ( wpv_is_types_custom_field( $key ) ) {
				$fields['types']['fields'][$key] = array(
					'label' => wpv_types_get_field_name( $key ),
					'slug' => sprintf('$(%s)', $key),
					'type' => 'text',
				);

			} else {
				$fields['custom-fields']['fields'][$key] = array(
					'label' => $key,
					'slug' => sprintf('$(%s)', $key),
					'type' => 'text',
				);
			}
        }
    }

    /**
     * get Views options
     */
    $options = get_option('wpv_options');

    /**
     * Views hidden CF's - already done in get_meta_keys() above!!
     */
	/*
    if ( isset($options['wpv_show_hidden_fields']) && !empty($options['wpv_show_hidden_fields'] ) ) {
        foreach( explode(',', $options['wpv_show_hidden_fields']) as $key) {
            if ( empty($key) ) {
                continue;
            }
            $fields['custom-fields']['fields'][$key] = array(
                'label' => $key,
                'slug' => sprintf('$(%s)', $key),
                'type' => 'text',
            );
        }
    }
	*/

    /**
     * Views Shortcodes
     */

    global $shortcode_tags;
    if ( is_array($shortcode_tags) ) {
        foreach (array_keys($shortcode_tags) as $key) {
            $views_shortcodes_regex = wpv_inner_shortcodes_list_regex();
            $include_expression = "/(". $views_shortcodes_regex .").*?/i";
           
            $check_shortcode = preg_match_all($include_expression, $key, $inner_matches);
            if ( $check_shortcode == 0 ){
                continue;
            }
            /**
             * do not add non-Views shortcodes
             */
            if ( !preg_match('/^wpv/', $key ) ) {
                continue;
            }
            /**
             * add shortode to list
             */
            $fields['views-shortcodes']['fields'][$key] = array(
                'label' => $key,
                'slug' => sprintf('\'[%s]\'', $key),
                'type' => 'text',
            );
        }
        ksort($fields['views-shortcodes']['fields']);
    }

    /**
     * Custom Functions
     */
    if ( isset($options['wpv_custom_conditional_functions']) && !empty($options['wpv_custom_conditional_functions'] ) ) {
        foreach( $options['wpv_custom_conditional_functions'] as $key) {
            if ( empty($key) ) {
                continue;
            }
            $fields['custom-functions']['fields'][$key] = array(
                'label' => $key,
                'slug' => sprintf('%s()', $key),
                'type' => 'text',
            );
        }
    }

    /**
     * Custom Shortcodes
     */
    if ( isset($options['wpv_custom_inner_shortcodes']) && !empty($options['wpv_custom_inner_shortcodes'] ) ) {

        foreach( $options['wpv_custom_inner_shortcodes'] as $key) {
            if ( empty($key) ) {
                continue;
            }
            $fields['custom-shortcodes']['fields'][$key] = array(
                'label' => $key,
                'slug' => sprintf('\'[%s]\'', $key),
                'type' => 'text',
            );
        }
    }

    /**
     * remove empty sections
     */
    foreach( $fields as $key => $field ) {
        if ( empty($field['fields']) ) {
            unset($fields[$key]);
        }
    }

    /**
     * fields json
     */
    $fields = array(
        'labels' => array(
            'select_choose' => esc_html( __('-- Select origin --', 'wpv-views') ),
            'button_delete' => esc_html( __('Delete', 'wpv-views') ),
        ),
        'fields' => $fields,
    );

    foreach ( $fields['fields'] as $key => $data ) {
        if (empty($data) ) {
            unset($fields['fields'][$key]);
        }
    }

    $content .= '<script type="text/javascript">';
    $content .= sprintf('wpv_conditional_data = %s;',json_encode($fields));
    $content .= '</script>';
	$content .= '<span class="js-wpv-shortcode-gui-content"></span>';
    $content .= '<table id="js-wpv-conditionals" class="wpv-conditionals" data-field-name="wpv-conditional"><thead><tr>';
    $content .= sprintf('<th style="width:310px">%s</th>', __('Data origin', 'wpv-views'));
    $content .= sprintf('<th>%s</th>', __('Comparison', 'wpv-views'));
    $content .= sprintf('<th>%s</th>', __('Value', 'wpv-views'));
    $content .= sprintf('<th>%s</th>', __('Relationship', 'wpv-views'));
    $content .= '<th style="width:50px;text-align:right;">&nbsp</th></tr></thead>';
    $content .= sprintf('<tfoot><td colspan="5"><button class="button js-wpv-views-conditional-add-term" >%s</button></td></tr></tfoot>', __('Add another condition', 'wpv-views'));
    $content .= '<tbody class="js-wpv-views-conditional-body"></tbody>';
    $content .= '</table>';
    return $content;
}

/**
* wp_ajax_wpv_shortcode_gui_dialog_conditional_create
*
* Render dialog_conditional for shortcodes attributes
*
* @since 1.9.0
*/
function wp_ajax_wpv_shortcode_gui_dialog_conditional_create() {
    if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'wpv_editor_callback' ) ) {
        _e('Nounce verify fail, please reload page and try again.', 'wpv-views');
        die();
    }
    /**
     * If post_id was passed, get the current post type object
     */
    $post_id = 0;
    if ( isset( $_GET['post_id'] ) ) {
        $post_id = intval( $_GET['post_id'] );
    }
    $post_type = array();
    if ( $post_id ) {
        $post_type = get_post_type_object( get_post_type( $post_id ) );
    }
    $shortcode = 'wpv-conditional';
    $options = array(
        'post-selection' => true,
        'attributes' => array(
            'expressions' => array(
                'label' => __('Conditions', 'wpv-views'),
                'header' => __('Conditional output', 'wpv-views'),
                'fields' => array(
                    'if' => array(
						'label' => __('Conditions to evaluate', 'wpv-views'),
                        'type' => 'callback',
                        'callback' => 'wpv_shortcode_wpv_conditional_callback',
                    ),
                    'custom-expressions' => array(
						'label' => __('Manual conditions', 'wpv-views'),
                        'type' => 'textarea',
						'description' => __( 'You can manually insert the conditions.', 'wpv-views' ),
                        'default' => '',
                    ),
					'evaluate' => array(
                        'label' => __('Conditions evaluation', 'wpv-views'),
                        'type' => 'radio',
                        'options' => array(
                            'true' => __('The evaluation result should be TRUE', 'wpv-views'),
                            'false' => __('The evaluation result should be FALSE', 'wpv-views'),
                        ),
						'description' => __( 'Whether the condition should be compared to TRUE or to FALSE.', 'wpv-views' ),
                        'default' => 'true',
                    ),
                    'debug' => array(
                        'label' => __('Show debug', 'wpv-views'),
                        'type' => 'radio',
                        'options' => array(
                            'true' => __('Show debug information to administrators', 'wpv-views'),
                            'false' => __('Don\'t show any debug information', 'wpv-views'),
                        ),
						'description' => __( 'Show additional information to administrators about the evaluation process.', 'wpv-views' ),
                        'default' => 'false',
                    ),
                ),
            ),
        ),
    );

    printf(
        '<div class="wpv-dialog js-insert-%s-dialog">',
        esc_attr( $shortcode )
    );
    echo '<input type="hidden" value="' . esc_attr( $shortcode ) . '" class="wpv-views-conditional-shortcode-gui-dialog-name js-wpv-views-conditional-shortcode-gui-dialog-name" />';
    $content = '';
    foreach( $options['attributes'] as $group_id => $group_data ) {
        $content .= sprintf(
            '<div id="%s-%s" style="position:relative">',
            esc_attr( $shortcode ),
            esc_attr( $group_id )
        );
        if ( isset( $group_data['header'] ) ) {
            $content .= sprintf(
                '<h2>%s</h2>',
                esc_html( $group_data['header'] )
            );
        }
		$content .= sprintf(
			'<button class="button js-wpv-shortcode-expression-switcher" style="position:absolute;top:-5px;right:0;">%s</button>',
			esc_html( __('Edit conditions manually', 'wpv-views') )
		);
        /**
         * add fields
         */
        foreach ( $group_data['fields'] as $key => $data ) {
            if ( ! isset( $data['type'] ) ) {
                continue;
            }
            $id = sprintf(
                '%s-%s',
                $shortcode,
                $key
            );
            $content .= sprintf(
                '<div class="wpv-shortcode-gui-attribute-wrapper js-wpv-shortcode-gui-attribute-wrapper js-wpv-shortcode-gui-attribute-wrapper-for-%s" data-type="%s" data-attribute="%s" data-default="%s" %s>',
                esc_attr( $key ),
                esc_attr( $data['type'] ),
                esc_attr( $key ),
                isset( $data['default'] ) ? esc_attr( $data['default'] ) : '',
                'custom-expressions' == $key? 'style="display:none"':''
            );
            $attr_value = isset( $data['default'] ) ? $data['default'] : '';
            $attr_value = isset( $data['default_force'] ) ? $data['default_force'] : $attr_value;

            $classes = array('js-shortcode-gui-field');
            $required = '';
            if (
                isset( $data['required'] )
                && $data['required']
            ) {
                $classes[] = 'js-wpv-shortcode-gui-required';
                $required = ' <span>- ' . esc_html( __( 'required', 'wpv-views' ) ) . '</span>';
            }
            if ( isset( $data['label'] ) ) {
                $content .= sprintf(
                    '<h3>%s%s</h3>',
                    esc_html( $data['label'] ),
                    $required
                );
            }
            /**
             * require
             */
            if ( isset($data['required']) && $data['required']) {
                $classes[] = 'js-required';
            }
            /**
             * Filter of options
             *
             * This filter allow to manipulate of radio/select field options.
             * Filter is 'wpv_filter_wpv_shortcodes_gui_api_{shortode}_options'
             *
             * @param array $options for description see param $options in
             * wpv_filter_wpv_shortcodes_gui_api filter.
             *
             * @param string $type field type
             *
             */
            if ( isset( $data['options'] ) ) {
                $data['options'] = apply_filters( 'wpv_filter_wpv_shortcodes_gui_api_' . $id . '_options', $data['options'], $data['type'] );
            }

            $content .= wpv_shortcode_gui_dialog_render_attribute( $id, $data, $classes, $post_type );

            $desc_and_doc = array();
            if ( isset( $data['description'] ) ) {
                $desc_and_doc[] = esc_html( $data['description'] );
            }
            if ( isset( $data['documentation'] ) ) {
                $desc_and_doc[] = sprintf(
                    __( 'Specific documentation: %s', 'wpv-views' ),
                    $data['documentation']
                );
            }
            if ( ! empty( $desc_and_doc ) ) {
                $content .= '<p class="description">' . implode( '<br />', $desc_and_doc ) . '</p>';
            }
            $content .= '</div>';
        }
        if ( isset( $group_data['content'] ) ) {
            if ( isset( $group_data['content']['hidden'] ) ) {
                $content .= '<span class="wpv-shortcode-gui-content-wrapper js-wpv-shortcode-gui-content-wrapper" style="display:none">';
                $content .= sprintf(
                    '<input id="shortcode-gui-content-%s" type="text" class="large-text js-wpv-shortcode-gui-content" />',
                    esc_attr( $shortcode )
                );
                $content .= '</span>';
            } else {
                $content .= '<div class="wpv-shortcode-gui-content-wrapper js-wpv-shortcode-gui-content-wrapper">';
                $content .= sprintf(
                    '<h3>%s</h3>',
                    esc_html( $group_data['content']['label'] )
                );
                $content .= sprintf(
                    '<input id="shortcode-gui-content-%s" type="text" class="large-text js-wpv-shortcode-gui-content" />',
                    esc_attr( $shortcode )
                );
                $desc_and_doc = array();
                if ( isset( $group_data['content']['description'] ) ) {
                    $desc_and_doc[] = $group_data['content']['description'];
                }
                if ( isset( $group_data['content']['documentation'] ) ) {
                    $desc_and_doc[] = sprintf(
                        __( 'Specific documentation: %s', 'wpv-views' ),
                        $group_data['content']['documentation']
                    );
                }
                if ( ! empty( $desc_and_doc ) ) {
                    $content .= '<p class="description">' . implode( '<br />', $desc_and_doc ) . '</p>';
                }
                $content .= '</div>';
            }
        }
        $content .= '</div>';
    }
    $content .= '</div>';
    echo $content;
    die;
}

function wpv_preprocess_wpv_conditional_shortcodes($content) {
	global $shortcode_tags, $WPV_Views_Conditional;

	// Back up current registered shortcodes and clear them all out
	$orig_shortcode_tags = $shortcode_tags;
	remove_all_shortcodes();

	add_shortcode( 'wpv-conditional', array( $WPV_Views_Conditional, 'wpv_shortcode_wpv_conditional' ) );

	$expression = '/\\[wpv-conditional((?!\\[wpv-conditional).)*\\[\\/wpv-conditional\\]/isU';
	$counts = preg_match_all($expression, $content, $matches);

	while ($counts) {
		foreach($matches[0] as $match) {

			// this will only processes the [wpv-if] shortcode
			$pattern = get_shortcode_regex();
			$match_corrected = $match;
			if ( 0 !== preg_match( "/$pattern/s", $match, $match_data ) ) {
				// Base64 Encode the inside part of the expression so the WP can't strip out any data it doesn't like.
				// Be sure to prevent base64_encoding more than just the needed: only do it if there are inner shortcodes
				if ( strpos( $match_data[5], '[' ) !== false ) {
					$match_corrected = str_replace( $match_data[5], 'wpv-b64-' . base64_encode( $match_data[5] ), $match_corrected );
				}
				
				$match_attributes = wpv_shortcode_parse_condition_atts( $match_data[3] );
				if ( isset( $match_attributes['if'] ) ) {
					$match_evaluate_corrected = str_replace( '<=', 'lte', $match_attributes['if'] );
					$match_evaluate_corrected = str_replace( '<>', 'ne', $match_evaluate_corrected );
					$match_evaluate_corrected = str_replace( '<', 'lt', $match_evaluate_corrected );
					$match_corrected = str_replace( $match_attributes['if'], $match_evaluate_corrected, $match_corrected );
				}
				
			}
			
			$shortcode = do_shortcode($match_corrected);
			$content = str_replace($match, $shortcode, $content);
			
		}
		
		$counts = preg_match_all($expression, $content, $matches);
	}

	// Put the original shortcodes back
	$shortcode_tags = $orig_shortcode_tags;
	
	return $content;
}

class WPV_Views_Conditional {

    public function __construct() {
		add_action( 'init', array( $this, 'init' ) );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
    }
	
	public function init() {
		add_shortcode( 'wpv-conditional', array( $this, 'wpv_shortcode_wpv_conditional' ) );
	}
	
	public function admin_init() {
		add_filter( "mce_external_plugins", array( $this, "wpv_add_views_conditional_button_scripts" ) );
		add_filter( "mce_buttons", array( $this, "register_buttons_editor" ) );
		add_action( 'admin_print_footer_scripts', array( $this, 'add_quicktags' ), 99 );
	}
	
	public function wpv_shortcode_wpv_conditional( $attr, $content = '' ) {
		global $post;
		$has_post = true;
		$id = '';
		if ( empty( $post->ID ) ) {
			// Will not execute any condition that involves custom fields
			$has_post = false;
		} else {
            $id = $post->ID;
        }

		if ( 
			empty( $attr['if'] ) 
			|| empty( $content ) 
		) {
			return ''; // ignore
		}
        
		extract(
			shortcode_atts(
				array(
					'evaluate' => 'true',
					'debug' => false,
					'if' => true
				),
				$attr
			)
		);

		
		$out = '';
		$evaluate = ( $evaluate == 'true' || $evaluate === TRUE ) ? true : false;
        $debug = ( $debug == 'true' || $debug === TRUE ) ? true : false;
		
		$attr['if'] = str_replace( " NEQ ", " ne ", $attr['if'] );
		$attr['if'] = str_replace( " neq ", " ne ", $attr['if'] );
		$attr['if'] = str_replace( " EQ ", " = ", $attr['if'] );
		$attr['if'] = str_replace( " eq ", " = ", $attr['if'] );
		$attr['if'] = str_replace( " NE ", " ne ", $attr['if'] );
		$attr['if'] = str_replace( " != ", " ne ", $attr['if'] );
		
		$attr['if'] = str_replace( " LT ", " < ", $attr['if'] );
		$attr['if'] = str_replace( " lt ", " < ", $attr['if'] );
		$attr['if'] = str_replace( " LTE ", " <= ", $attr['if'] );
		$attr['if'] = str_replace( " lte ", " <= ", $attr['if'] );
		$attr['if'] = str_replace( " GT ", " > ", $attr['if'] );
		$attr['if'] = str_replace( " gt ", " > ", $attr['if'] );
		$attr['if'] = str_replace( " GTE ", " >= ", $attr['if'] );
		$attr['if'] = str_replace( " gte ", " >= ", $attr['if'] );
		
		if ( strpos( $content, 'wpv-b64-' ) === 0 ) {
			$content = substr( $content, 7 );
			$content = base64_decode( $content );
		}

		$evaluation_result = $this->parse_conditional( $post, $attr['if'], $debug, $attr, $id, $has_post );

		if ( 
			( 
				$evaluate 
				&& $evaluation_result['passed'] 
			) || (
				! $evaluate  
				&& ! $evaluation_result['passed'] 
			) 
		) {
			$out = $content;
		}

		if ( 
			$debug 
			&& current_user_can( 'manage_options' ) 
		) {
			$out .= '<pre>' . $evaluation_result['debug'] . '</pre>';
		}

		apply_filters('wpv_shortcode_debug','wpv-conditional', json_encode($attr), '', 'Data received from cache', $out);
		return $out;
	}
	
	function wpv_add_views_conditional_button_scripts( $plugin_array ) {
		if ( 
			wp_script_is( 'quicktags' ) 
			&& wp_script_is( 'views-shortcodes-gui-script' ) 
		) {
			//enqueue TinyMCE plugin script with its ID.
			$plugin_array["wpv_add_views_conditional_button"] =  WPV_URL_EMBEDDED . '/res/js/views_conditional_button_plugin.js';
		}
		return $plugin_array;
	}


	function register_buttons_editor( $buttons ) {
		if ( 
			wp_script_is( 'quicktags' ) 
			&& wp_script_is( 'views-shortcodes-gui-script' ) 
		) {
			//register buttons with their id.
			array_push( $buttons, "wpv_conditional_output" );
		}
		return $buttons;
	}
	
	public function add_quicktags() {
		if ( 
			wp_script_is( 'quicktags' ) 
			&& wp_script_is( 'views-shortcodes-gui-script' ) 
		) {
			?>
			<script type="text/javascript">
				QTags.addButton( 'wpv_conditional', '<?php echo esc_attr( __( 'conditional output', 'wpv-views' ) ); ?>', wpv_add_conditional_quicktag_function, '', 'c', '<?php echo esc_attr( __( 'Views conditional output', 'wpv-views' ) ); ?>', 121, '', { ariaLabel: '<?php echo esc_attr( __( 'Views conditional output', 'wpv-views' ) ); ?>', ariaLabelClose: '<?php echo esc_attr( __( 'Close Views conditional output', 'wpv-views' ) ); ?>' } );
			</script>
			<?php
		}
	}

    public static function parse_conditional( $post, $condition, $debug = false, $attr, $id, $has_post ){
        
        $logging_string = "####################\nwpv-conditional attributes\n####################\n"
		. print_r( $attr, true )
		. "\n####################\nDebug information\n####################"
		. "\n--------------------\nOriginal expression: "
		. $condition
		. "\n--------------------";
        
        if (!defined('WPTOOLSET_COMMON_PATH')) {
            define('WPTOOLSET_COMMON_PATH', WPV_PATH_EMBEDDED . '/common');
        }
        require_once WPV_PATH_EMBEDDED . '/common/toolset-forms/classes/class.types.php';
        require_once WPV_PATH_EMBEDDED . '/common/toolset-forms/classes/class.cred.php';
        $data = WPToolset_Types::getCustomConditional($condition, '', WPToolset_Types::getConditionalValues($id));

        $evaluate = $data['custom'];
        $values = $data['values'];

        if (strpos($evaluate, "REGEX") === false) {
            $evaluate = trim(stripslashes($evaluate));
            // Check dates
            $evaluate = wpv_filter_parse_date($evaluate);
            $evaluate = self::handle_user_function($evaluate);
        }


        $fields = self::extractFields($evaluate);

        $evaluate = apply_filters( 'wpv-extra-condition-filters', $evaluate);
        $temp = self::extractVariables( $evaluate, $attr, $has_post, $id );
        $evaluate = $temp[0];
        $logging_string .= $temp[1];
        $passed = self::evaluateCustom($evaluate);
        if ( $passed ){
            return array ( 'debug' => $logging_string, 'passed'=>$passed );
        }


        $evaluate = self::_update_values_in_expression($evaluate, $fields, $values);


        $logging_string .= "\n--------------------\nConverted expression: "
		. $evaluate
		. "\n--------------------";
        $passed = self::evaluateCustom($evaluate);

        return array ( 'debug' => $logging_string, 'passed'=>$passed );

    }

    public static function extractVariables( $evaluate, $atts, $has_post, $id ){
        $logging_string = '';
        // Evaluate quoted variables that are to be used as strings
        // '$f1' will replace $f1 with the custom field value

            $strings_count = preg_match_all( '/(\'[\$\w^\']*\')/', $evaluate, $matches );
            if (
                $strings_count
                && $strings_count > 0
            ) {
                for ( $i = 0; $i < $strings_count; $i++ ) {
                    $string = $matches[1][$i];
                    // remove single quotes from string literals to get value only
                    $string = (strpos( $string, '\'' ) === 0) ? substr( $string, 1, strlen( $string ) - 2 ) : $string;
                    if ( strpos( $string, '$' ) === 0 ) {
                        $quoted_variables_logging_extra = '';
                        $variable_name = substr( $string, 1 ); // omit dollar sign
                        if ( isset( $atts[$variable_name] ) ) {
                            $string = get_post_meta( $id, $atts[$variable_name], true );
                            $evaluate = str_replace( $matches[1][$i], "'" . $string . "'", $evaluate );
                        } else {
                            $evaluate = str_replace( $matches[1][$i], "", $evaluate );
                            $quoted_variables_logging_extra = "\n\tERROR: Key " . $matches[1][$i] . " does not point to a valid attribute in the wpv-if shortcode: expect parsing errors";
                        }
                        $logging_string .= "\nAfter replacing " . ( $i + 1 ) . " quoted variables: " . $evaluate . $quoted_variables_logging_extra;
                    }
                }
            }

            // Evaluate non-quoted variables, by de-quoting the quoted ones if needed


        $strings_count = preg_match_all( '/((\$\w+)|(\'[^\']*\'))\s*([\!<>\=|lt|lte|eq|ne|gt|gte]+)\s*((\$\w+)|(\'[^\']*\'))/',
            $evaluate, $matches );

        // get all string comparisons - with variables and/or literals
        if (
            $strings_count
            && $strings_count > 0
        ) {
            for ( $i = 0; $i < $strings_count; $i++ ) {

                // get both sides and sign
                $first_string = $matches[1][$i];
                $second_string = $matches[5][$i];
                $math_sign = $matches[4][$i];

                $general_variables_logging_extra = '';

                // remove single quotes from string literals to get value only
                $first_string = ( strpos( $first_string, '\'' ) === 0 ) ? substr( $first_string, 1, strlen( $first_string ) - 2 ) : $first_string;
                $second_string = ( strpos( $second_string, '\'' ) === 0 ) ? substr( $second_string, 1, strlen( $second_string ) - 2 ) : $second_string;
                $general_variables_logging_extra .= "\n\tComparing " . $first_string . " to " . $second_string;

                // replace variables with text representation
                if (
                    strpos( $first_string, '$' ) === 0
                    && $has_post
                ) {
                    $variable_name = substr( $first_string, 1 ); // omit dollar sign
                    if ( isset( $atts[$variable_name] ) ) {
                        $first_string = get_post_meta( $id, $atts[$variable_name], true );
                    } else {
                        $first_string = '';
                        $general_variables_logging_extra .= "\n\tERROR: Key " . $variable_name . " does not point to a valid attribute in the wpv-if shortcode";
                    }
                }
                if ( strpos( $second_string, '$' ) === 0 && $has_post ) {
                    $variable_name = substr( $second_string, 1 );
                    if ( isset( $atts[$variable_name] ) ) {
                        $second_string = get_post_meta( $id, $atts[$variable_name], true );
                    } else {
                        $second_string = '';
                        $general_variables_logging_extra .= "\n\tERROR: Key " . $variable_name . " does not point to a valid attribute in the wpv-if shortcode";
                    }
                }


                $evaluate = (is_numeric( $first_string )  ? str_replace( $matches[1][$i], $first_string, $evaluate ) : str_replace( $matches[1][$i], "'$first_string'", $evaluate ));
                $evaluate = (is_numeric( $first_string )  ? str_replace( $matches[5][$i], $second_string, $evaluate ) : str_replace( $matches[5][$i], "'$second_string'", $evaluate ));
                $logging_string .= "\nAfter replacing " . ( $i + 1 ) . " general variables and comparing strings: " . $evaluate . $general_variables_logging_extra;
            }
        }
        // Evaluate comparisons when at least one of them is numeric
		$strings_count = preg_match_all( '/(\'[^\']*\')/', $evaluate, $matches );
		if (
			$strings_count
			&& $strings_count > 0
		) {
			for ( $i = 0; $i < $strings_count; $i++ ) {
				$string = $matches[1][$i];
				// remove single quotes from string literals to get value only
				$string = ( strpos( $string, '\'' ) === 0 ) ? substr( $string, 1, strlen( $string ) - 2 ) : $string;
				if ( is_numeric( $string ) ) {
					$evaluate = str_replace( $matches[1][$i], $string, $evaluate );
					$logging_string .= "\nAfter matching " . ( $i + 1 ) . " numeric strings into real numbers: " . $evaluate;
					$logging_string .= "\n\tMatched " . $matches[1][$i] . " to " . $string;
				}
			}
		}

		// Evaluate all remaining variables
		if ( $has_post ) {
			$count = preg_match_all( '/\$(\w+)/', $evaluate, $matches );

			// replace all variables with their values listed as shortcode parameters
			if (
				$count
				&& $count > 0
			) {
				$logging_string .= "\nRemaining variables: " . var_export( $matches[1], true );
				// sort array by length desc, fix str_replace incorrect replacement
				// wpv_sort_matches_by_length belongs to common/functions.php
				$matches[1] = wpv_sort_matches_by_length( $matches[1] );

				foreach ( $matches[1] as $match ) {
					if ( isset( $atts[$match] ) ) {
						$meta = get_post_meta( $id, $atts[$match], true );
						if ( empty( $meta ) ) {
							$meta = "''";
						}
					} else {
						$meta = "0";
					}
					$evaluate = str_replace( '$' . $match, $meta, $evaluate );
					$logging_string .= "\nAfter replacing remaining variables: " . $evaluate;
				}
			}
		}
        return array( $evaluate, $logging_string);
    }

    /**
     * Evaluates conditions using custom conditional statement.
     *
     * @uses wpv_condition()
     *
     * @param type $post
     * @param type $evaluate
     * @return boolean
     */
    public static function evaluateCustom($evaluate) {
        $check = false;
        try {
            $parser = new Toolset_Parser($evaluate);
            $parser->parse();
            $check = $parser->evaluate();
        } catch (Exception $e) {
            $check = false;
        }
        return $check;
    }

    public static function extractFields($evaluate) {
        //###############################################################################################
        //https://icanlocalize.basecamphq.com/projects/7393061-toolset/todo_items/193583580/comments
        //Fix REGEX conditions that contains \ that is stripped out
        if (strpos($evaluate, "REGEX") === false) {
            $evaluate = trim(stripslashes($evaluate));
            // Check dates
            $evaluate = wpv_filter_parse_date($evaluate);
            $evaluate = self::handle_user_function($evaluate);
        }

        // Add quotes = > < >= <= === <> !==
        $strings_count = preg_match_all('/[=|==|===|<=|<==|<===|>=|>==|>===|\!===|\!==|\!=|<>]\s(?!\$)(\w*)[\)|\$|\W]/', $evaluate, $matches);

        if (!empty($matches[1])) {
            foreach ($matches[1] as $temp_match) {
                $temp_replace = is_numeric($temp_match) ? $temp_match : '\'' . $temp_match . '\'';
                $evaluate = str_replace(' ' . $temp_match . ')', ' ' . $temp_replace . ')', $evaluate);
            }
        }
        // if new version $(field-value) use this regex
        if (preg_match('/\$\(([^()]+)\)/', $evaluate)) {
            preg_match_all('/\$\(([^()]+)\)/', $evaluate, $matches);
        }
        // if old version $field-value use this other
        else {
            preg_match_all('/\$([^\s]*)/', $evaluate, $matches);
        }


        $fields = array();
        if (!empty($matches)) {
            foreach ($matches[1] as $field_name) {
                $fields[trim($field_name, '()')] = trim($field_name, '()');
            }
        }

        return $fields;
    }
	
	static function sortByLength($a, $b) {
        return strlen($b) - strlen($a);
    }

    private static function _update_values_in_expression($evaluate, $fields, $values) {

        // use string replace to replace any fields with their values.
        // Sort by length just in case a field name contians a shorter version of another field name.
        // eg.  $my-field and $my-field-2

        $keys = array_keys($fields);
        usort($keys, 'WPV_Views_Conditional::sortByLength');

        foreach ($keys as $key) {
            $is_numeric = false;
            $is_array = false;
            $value = isset($values[$fields[$key]]) ? $values[$fields[$key]] : '';
            if ($value == '') {
                $value = "''";
            }
            if (is_numeric($value)) {
                $value = '\'' . $value . '\'';
                $is_numeric = true;
            }

            if ('array' === gettype($value)) {
                $is_array = true;
                // workaround for datepicker data to cover all cases
                if (array_key_exists('timestamp', $value)) {
                    if (is_numeric($value['timestamp'])) {
                        $value = $value['timestamp'];
                    } else if (is_array($value['timestamp'])) {
                        $value = implode(',', array_values($value['timestamp']));
                    }
                } else if (array_key_exists('datepicker', $value)) {
                    if (is_numeric($value['datepicker'])) {
                        $value = $value['datepicker'];
                    } else if (is_array($value['datepicker'])) {
                        $value = implode(',', array_values($value['datepicker']));
                    }
                } else {
                    $value = implode(',', array_values($value));
                }
            }

            if ( !empty($value) && $value != "''" && !$is_numeric && !$is_array ){
                 $value = '\'' . $value . '\'';
            }

            // First replace the $(field_name) format
            $evaluate = str_replace('$(' . $fields[$key] . ')', $value, $evaluate);
            // next replace the $field_name format
            $evaluate = str_replace('$' . $fields[$key], $value, $evaluate);
        }

        return $evaluate;
    }

    public static function handle_user_function($evaluate)
    {
        $evaluate = stripcslashes($evaluate);
        $occurrences = preg_match_all('/(\\w+)\(([^\)]*)\)/', $evaluate, $matches);

        if ($occurrences > 0) {
            for ($i = 0; $i < $occurrences; $i++) {
                $result = false;
                $function = $matches[1][$i];
                $field = isset($matches[2]) ? rtrim($matches[2][$i], ',') : '';

                if ($function === 'USER') {
                    $result = WPV_Handle_Users_Functions::get_user_field($field);
                }

                if ($result) {
                    $evaluate = str_replace($matches[0][$i], $result, $evaluate);
                }
            }
        }

        return $evaluate;
    }

    private static function getStringFromArray($array)
    {
        if ( is_object( $array ) ) {
            return $array;
        }
        if ( is_array( $array ) ) {
            return self::getStringFromArray(array_shift($array));
        }
        return strval( $array );
    }

    public static function getCustomConditional($custom, $suffix = '', $cond_values = array()) {

    }

}

global $WPV_Views_Conditional;
$WPV_Views_Conditional = new WPV_Views_Conditional();