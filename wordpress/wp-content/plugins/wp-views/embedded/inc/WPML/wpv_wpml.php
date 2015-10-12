<?php

/* ************************************************************************* *\
        WPML Translation Management integration
\* ************************************************************************* */


/**
 * Auxiliar function to override the current language
 *
 * @param $lang string the current language
 * @return bool $sitepress->get_default_language()
 *
 * @since unknown
 */
function wpv_wpml_icl_current_language( $lang ) { // TODO check why is this needed: it just returns the default language when looking for the current language...
    global $sitepress;

    return $sitepress->get_default_language();
}


/**
 * Converts links in a string to the corresponding ones in the current language
 *
 * @param $body string to check against
 * @return bool|mixed|string $body
 *
 * @since unknown
 */
function wpml_content_fix_links_to_translated_content($body){
    global $WPV_settings, $wpdb, $sitepress, $sitepress_settings, $wp_taxonomies;

    if ( ! $WPV_settings->wpml_fix_urls ) {
        return $body;
    }

    if (isset($sitepress)) {

        static $content_cache = array();

        $target_lang_code = $sitepress->get_current_language();

        $cache_code = md5($body . $target_lang_code);
        if (isset($content_cache[$cache_code])) {
            $body = $content_cache[$cache_code];
        } else {

			// On the latest fix, those two hooks were  moved to after the _process_generic_text call
			// This needs wild testing on sites with a non-english first language
            add_filter('icl_current_language', 'wpv_wpml_icl_current_language');
            remove_filter('option_rewrite_rules', array($sitepress, 'rewrite_rules_filter'));

            require_once ICL_PLUGIN_PATH . '/inc/absolute-links/absolute-links.class.php';
            $icl_abs_links = new AbsoluteLinks;

            $old_body = $body;
            $alp_broken_links = array();
            $body = $icl_abs_links->_process_generic_text($body, $alp_broken_links);

            // Restore the language as the above call can change the current language.
            $sitepress->switch_lang($target_lang_code);

            if ($body == '') {
                // Handle a problem with abs links occasionally return empty.
                $body = $old_body;
            }

            $new_body = $body;

            $base_url_parts = parse_url(get_option('home'));

            $links = wpml_content_get_link_paths($body);

            $all_links_fixed = 1;

            $pass_on_qvars = array();
            $pass_on_fragments = array();

            foreach($links as $link_idx => $link) {
                $path = $link[2];
                $url_parts = parse_url($path);

                if(isset($url_parts['fragment'])){
                    $pass_on_fragments[$link_idx] = $url_parts['fragment'];
                }

                if((!isset($url_parts['host']) or $base_url_parts['host'] == $url_parts['host']) and
                        (!isset($url_parts['scheme']) or $base_url_parts['scheme'] == $url_parts['scheme']) and
                        isset($url_parts['query'])) {
                    $query_parts = explode('&', $url_parts['query']);

                    foreach($query_parts as $query){
                        // find p=id or cat=id or tag=id queries
                        list($key, $value) = explode('=', $query);
                        $translations = NULL;
                        $is_tax = false;
                        if($key == 'p'){
                            $kind = 'post_' . $wpdb->get_var(
								$wpdb->prepare(
									"SELECT post_type FROM {$wpdb->posts} 
									WHERE ID = %d 
									LIMIT 1",
									$value
								)
							);
                        } else if($key == "page_id"){
                            $kind = 'post_page';
                        } else if($key == 'cat' || $key == 'cat_ID'){
                            $is_tax = true;
                            $kind = 'tax_category';
                            $taxonomy = 'category';
                        } else if($key == 'tag'){
                            $is_tax = true;
                            $taxonomy = 'post_tag';
                            $kind = 'tax_' . $taxonomy;
                            $value = $wpdb->get_var(
								$wpdb->prepare(
									"SELECT term_taxonomy_id FROM {$wpdb->terms} t JOIN {$wpdb->term_taxonomy} x 
									ON t.term_id = x.term_id 
									WHERE x.taxonomy = %s 
									AND t.slug = %s 
									LIMIT 1",
									$taxonomy,
									$value
								)
							);
                        } else {
                            $found = false;
                            foreach($wp_taxonomies as $ktax => $tax){
                                if($tax->query_var && $key == $tax->query_var){
                                    $found = true;
                                    $is_tax = true;
                                    $kind = 'tax_' . $ktax;
                                    $value = $wpdb->get_var(
										$wpdb->prepare(
											"SELECT term_taxonomy_id FROM {$wpdb->terms} t JOIN {$wpdb->term_taxonomy} x 
											ON t.term_id = x.term_id 
											WHERE x.taxonomy = %s 
											AND t.slug = %s 
											LIMIT 1",
											$ktax,
											$value
										)
									);
                                    $taxonomy = $ktax;
                                }
                            }
                            if(!$found){
                                $pass_on_qvars[$link_idx][] = $query;
                                continue;
                            }
                        }

                        $link_id = (int)$value;

                        if (!$link_id) {
                            continue;
                        }

                        $trid = $sitepress->get_element_trid($link_id, $kind);
                        if(!$trid){
                            continue;
                        }
                        if($trid !== NULL){
                            $translations = $sitepress->get_element_translations($trid, $kind);
                        }
                        if(isset($translations[$target_lang_code]) && $translations[$target_lang_code]->element_id != null){

                            // use the new translated id in the link path.

                            $translated_id = $translations[$target_lang_code]->element_id;

                            if($is_tax){ //if it's a tax, get the translated link based on the term slug (to avoid the need to convert from term_taxonomy_id to term_id)
                                $translated_id = $wpdb->get_var(
									$wpdb->prepare(
										"SELECT slug FROM {$wpdb->terms} t JOIN {$wpdb->term_taxonomy} x 
										ON t.term_id = x.term_id 
										WHERE x.term_taxonomy_id = %d 
										LIMIT 1",
										$translated_id
									)
								);
                            }

                            // if absolute links is not on turn into WP permalinks
                            if(empty($GLOBALS['WPML_Sticky_Links'])){
                                ////////
                                if(preg_match('#^post_#', $kind)){
                                    $replace = get_permalink($translated_id);
                                }elseif(preg_match('#^tax_#', $kind)){
                                remove_filter('icl_current_language', 'wpv_wpml_icl_current_language');
                                    if(is_numeric($translated_id)) $translated_id = intval($translated_id);
                                    $replace = get_term_link($translated_id, $taxonomy);
                                    add_filter('icl_current_language', 'wpv_wpml_icl_current_language');
                                }
                                $new_link = str_replace($link[2], $replace, $link[0]);

                                $replace_link_arr[$link_idx] = array('from'=> $link[2], 'to'=>$replace);
                            }else{
                                $replace = $key . '=' . $translated_id;
                                $new_link = str_replace($query, $replace, $link[0]);

                                $replace_link_arr[$link_idx] = array('from'=> $query, 'to'=>$replace);
                            }

                            // replace the link in the body.
                            // $new_body = str_replace($link[0], $new_link, $new_body);
                            $all_links_arr[$link_idx] = array('from'=> $link[0], 'to'=>$new_link);
                            // done in the next loop

                        } else {
                            // translation not found for this.
                            $all_links_fixed = 0;
                        }
                    }
                }

            }

            if(!empty($replace_link_arr))
            foreach($replace_link_arr as $link_idx => $rep){
                $rep_to = $rep['to'];
                $fragment = '';

                // if sticky links is not ON, fix query parameters and fragments
                if(empty($GLOBALS['WPML_Sticky_Links'])){
                    if(!empty($pass_on_fragments[$link_idx])){
                        $fragment = '#' . $pass_on_fragments[$link_idx];
                    }
                    if(!empty($pass_on_qvars[$link_idx])){
                        $url_glue = (strpos($rep['to'], '?') === false) ? '?' : '&';
                        $rep_to = $rep['to'] . $url_glue . join('&', $pass_on_qvars[$link_idx]);
                    }
                }

                $all_links_arr[$link_idx]['to'] = str_replace($rep['to'], $rep_to . $fragment, $all_links_arr[$link_idx]['to']);

            }

            if(!empty($all_links_arr))
            foreach($all_links_arr as $link){
                $new_body = str_replace($link['from'], $link['to'], $new_body);
            }

            $body = $new_body;
            $content_cache[$cache_code] = $body;

            remove_filter('icl_current_language', 'wpv_wpml_icl_current_language');
            add_filter('option_rewrite_rules', array($sitepress, 'rewrite_rules_filter'));

        }
    }

    return $body;
}


/**
 * Parse links from a given string
 *
 * @param $body string to be parsed
 * @return array $links array of parsed links
 *
 * @since unknown
 */
function wpml_content_get_link_paths($body) {

    $regexp_links = array(
                        /*"/<a.*?href\s*=\s*([\"\']??)([^\"]*)[\"\']>(.*?)<\/a>/i",*/
                        "/<a[^>]*href\s*=\s*([\"\']??)([^\"^>]+)[\"\']??([^>]*)>/i",
                        );

    $links = array();

    foreach($regexp_links as $regexp) {
        if (preg_match_all($regexp, $body, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
              $links[] = $match;
            }
        }
    }
    return $links;
}


add_action( 'icl_tm_menu_mcsetup', 'wpv_wpml_settings' );

/**
 * Filter hooked into icl_tm_menu_mcsetup. Add View settings to the Translation Management setup screen.
 *
 * @note displays different HTML for different WPML versions
 *
 * @since unknown
 */
function wpv_wpml_settings() {
    global $WPV_settings;

    wp_nonce_field('wpv_wpml_save_settings_nonce', 'wpv_wpml_save_settings_nonce');
    
    if(defined('ICL_SITEPRESS_VERSION')) {

        if ( version_compare( ICL_SITEPRESS_VERSION, '3.0' )  < 0 ) {

            /*
            *    This section should be display conditionally, only for WPML < 3.0
            */
            ?>
            <table class="widefat">
                <thead>
                    <tr>
                        <th><?php _e('Views', 'wpv-views'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="border: none;">
                            <p>
                                <label>
                                    <input id="wpv_wpml_fix_urls" type="checkbox" value="1" <?php checked( $WPV_settings->wpml_fix_urls ); ?> />
                                    <?php _e('Convert URLs to point to translated content in Views and Content Templates', 'wpv-views'); ?>
                                </label>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input class="button-primary" type="button" value="<?php _e('Save', 'wpv-views'); ?>" onclick="wpv_wpml_save_view_settings(); return false;" />
                            <span id="icl_ajx_response_views_wpml" class="icl_ajx_response"><?php _e('Settings Saved', 'wpv-views'); ?></span>
                        </td>
                    </tr>
                </tbody>
            </table>
        <?php
    
        } else {

            /*
            * This section should be display conditionally, only for WPML >= 3.0
            */

            ?>

            <div class="wpml-section">
                <div class="wpml-section-header">
                    <h3>
                        <?php _e('Views', 'wpv-views'); ?></th>
                    </h3>
                </div>
                <div class="wpml-section-content">
                    <p>
                        <label>
                            <input id="wpv_wpml_fix_urls" type="checkbox" value="1" <?php checked( $WPV_settings->wpml_fix_urls ); ?> />
                            <?php _e('Convert URLs to point to translated content in Views and Content Templates', 'wpv-views'); ?>
                        </label>
                    </p>
                    <p class="buttons-wrap">
                        <span id="icl_ajx_response_views_wpml" class="icl_ajx_response"><?php _e('Settings Saved', 'wpv-views'); ?></span>
                        <input class="button-primary" type="button" value="<?php _e('Save', 'wpv-views'); ?>" onclick="wpv_wpml_save_view_settings(); return false;" />
                    </p>
                </div>
            </div>

            <?php

        }
    }
    ?>

	<script type="text/javascript">
        function wpv_wpml_save_view_settings() {

            var data = {
                action : 'wpv_wpml_save_settings',
                wpv_wpml_fix_urls : jQuery('#wpv_wpml_fix_urls:checked').val(),
                wpv_nonce : jQuery('#wpv_wpml_save_settings_nonce').attr('value')

            };

            jQuery.ajaxSetup({async:false});
            jQuery.post(ajaxurl, data, function(response) {
                jQuery('#icl_ajx_response_views_wpml').show();
            });

        }
    </script>

    <?php
}



add_action('wp_ajax_wpv_wpml_save_settings', 'wpv_wpml_save_settings');

function wpv_wpml_save_settings() {
	if (wp_verify_nonce($_POST['wpv_nonce'], 'wpv_wpml_save_settings_nonce')) {
        global $WPV_settings;
        $WPV_settings->wpml_fix_urls = isset( $_POST['wpv_wpml_fix_urls'] ) ? $_POST['wpv_wpml_fix_urls'] : false;
        $WPV_settings->save();
    }

    die();
}


/* ************************************************************************* *\
        WPML String Translation integration
\* ************************************************************************* */


add_filter( 'wpv_custom_inner_shortcodes', 'wpv_wpml_string_in_custom_inner_shortcodes' );

/**
 * Filter hooked into wpv_custom_inner_shortcodes. Add the [wpml-string] shortcode to the allowed inner shortcodes, but only if the [wpml-string] shortcode itself exists
 *
 * @param $custom_inner_shortcodes array() of allowed custom inner shortcodes
 *
 * @return $custom_inner_shortcodes
 *
 * @since 1.4.0
 */
function wpv_wpml_string_in_custom_inner_shortcodes( $custom_inner_shortcodes ) {
	if ( function_exists( 'wpml_string_shortcode' ) ) {
		if ( ! is_array( $custom_inner_shortcodes ) ) {
			$custom_inner_shortcodes = array();
		}
		$custom_inner_shortcodes[] = 'wpml-string';
		$custom_inner_shortcodes = array_unique( $custom_inner_shortcodes );
	}
	return $custom_inner_shortcodes;
}

/**
 * Utility function to translate strings used in wpv-control shortcodes.
 *
 * @param string $content The content of the Filter HTML textarea to parse
 * @param int $view_id The current View ID to build the content from
 *
 * @since 1.3.0
 */
function wpv_add_controls_labels_to_translation( $content, $view_id ) {
	if( function_exists('icl_register_string') ) {
		/*
		** Array of fields to be checked
		*/
		$tobechecked = array(
			'display_values',
			'default_label',
			'title',
			'auto_fill_default',
			'name',
			'reset_label'
		);
		/*
		** If there are commas escaped please replace with '|' (pipe char)
		*/
		$content = str_replace("\\\\\,", '|', $content);
		/*
		** Strip all slashes if any left
		*/
		$content = stripslashes( $content );
		/*
		** Make a context out of View title
		*/
		$context = get_post_field( 'post_name', $view_id );
		/*
		** Empty array to store what's already being parsed (when BETWEEN or NOT BETWEEN we can have 2 recorrences of the same labels)
		*/
		$control = array();

		/*
		** Loop through all our fields
		*/
		foreach( $tobechecked as $string ) {
		
			if ( $string == 'name' ) {
				$button_name = 'submit';
			} else {
				$button_name = 'button';
			}
			/*
			** Make sure we have parameters in the form of param="
			*/
			if( strpos( $content, $string.'="' ) !== false ) {
				/*
				** Subquery 1: ( (url_param\s*?=\"(.*?)\").*?)? make sure if we have 0 or more occurences of 'url_param="' and take the value (.*?) in a subquery
				** array[3]
				** Subquery 3: this is our main without ? operator (".$string."\s*?=\"(.*?)\"), if there is store (.*?) subquery value in array[5]
				*/
				preg_match_all( "/( (url_param\s*?=\"(.*?)\").*?)?(".$string."\s*?=\"(.*?)\")/", $content, $matches );
				if ( $string == 'default_label' ) {
					preg_match_all( "/( (ancestor_type\s*?=\"(.*?)\").*?)?(".$string."\s*?=\"(.*?)\")/", $content, $anc_matches );
				}
				/*
				** If we have a corrsponding match on (".$string."\s*?=\"(.*?)\") this one and first element of result array is not empty loop
				*/
				if( isset( $matches[5] ) && isset( $matches[5][0] ) ) {
					/*
					** Loop through results and store $key for control
					*/
					foreach( $matches[5] as $key=>$translate ) {
						/*
						** If we have values we will store first element of the list here to be translated
						*/
						$translate_first = '';
						/*
						** If we have values keep track if the first display_value should be translated or not
						*/

						$should_do = false;
						
						/**
						** Take the key of the actual record to translate.
						** If there are multiple, do it for every one of them
						**/
						$key_juan = array_keys( $matches[5], $translate );
						
						foreach ( $key_juan as $key_t ) {

							/**
							** Create a name for label to translate
							**/
							
							if ( $string == 'default_label' && isset( $anc_matches[3] ) && isset( $anc_matches[3][$key_t] ) ) {
								$button_name = $anc_matches[3][$key_t];
							}
							
							$name = !empty( $matches[3][$key_t] ) ? $matches[3][$key_t] : $button_name;

							/*
							** Make sure we do not already have a translatable string for this occurence
							*/
							if( !in_array($translate . $name, $control) ) {

								/*
								** If we have values loop through them
								*/
								if( $string == 'display_values' ) {
									$should_do = true;
									/*
									** Keep track of the values already pushed for translation f we have more occurences of same value
									*/

									$trs_values = array();
									/*
									** Loop through values
									*/

									/*
									** Translate only display_values if first value is empty and we didn't push it already
									*/

									$translate_first = explode( ',', $translate );
									foreach( $translate_first as $trs_first ) {
										$trs_first = str_replace('|', ',', $trs_first );
										array_push($trs_values, $trs_first);
									}

								}

								/**
								** If eligible for translation do
								**/

								if( $should_do ) {
									$count_values = 1;
									foreach( $trs_values as $trs )
									{
										icl_register_string( "View ".$context, $name.'_'.$string."_".$count_values, $trs );
										$count_values++;
									}
									$trs_first = '';
								} else {
									icl_register_string( "View ".$context, $name.'_'.$string, $translate );
								}

								array_push($control, $translate . $name);
							}

						}
					}
				}
			}
		}
	}
}

/**
 * wpv_parse_wpml_shortcode
 *
 * Parses wpml-string shortcodes in a given string, handling slashes coming from escaped quotes
 *
 * @param $content the string to parse shortcodes from
 * @return array $output array( N => array( 'context'=> $context, 'content'=> $content, 'name'=> $name ) )
 *
 * @since 1.5.0
 */
function wpv_parse_wpml_shortcode( $content ) {
	$output = array();
	$content = stripslashes( $content );
	preg_match_all( "/\[wpml-string context=\"([^\"]+)\"]([^\[]+)\[\/wpml-string\]/iUs", $content, $out );
	if ( count( $out[0] ) > 0 ) {
		$matches = count( $out[0] );
		for( $i=0; $i < $matches; $i++ ){
			$output[] = array( 'context' => $out[1][$i], 'content' => $out[2][$i], 'name' => 'wpml-shortcode-' . md5( $out[2][$i] ) );
		}
	}
	return $output;
}


/**
 * wpv_register_wpml_strings
 *
 * Registers strings wrapped into wpml-string shortcodes for translation using WPML, handling slashes coming from escaped quotes
 *
 * @param string $content The string to parse shortcodes from.
 *
 * @since 1.5.0
 */
function wpv_register_wpml_strings( $content ) {
	if ( function_exists('icl_register_string') ) {
		$registrars = array();
		$registrars = wpv_parse_wpml_shortcode( $content );
		if ( count( $registrars ) > 0 ) {
			foreach ( $registrars as $string ) {
				icl_register_string( $string['context'], $string['name'], $string['content'] );
			}
		}
	}
}


/**
* wpv_register_wpml_strings_on_activation
*
* Hooks into the String Translation activation, registering all Views wpml-string shortcodes and all translatable strings in wpv-control shortcodes
*
* @since 1.5.0
* @since 1.6.2 change of the hook to init as the user capabilities are not reliable before that (and they are used in get_posts())
*/

add_action( 'init', 'wpv_register_wpml_strings_on_activation', 99 );

function wpv_register_wpml_strings_on_activation() {
	if (
		function_exists( 'icl_register_string' ) &&
		defined( 'WPML_ST_VERSION' ) &&
		!get_option( 'wpv_strings_translation_initialized', false ) &&
		current_user_can( 'manage_options' )
	) {
		global $WP_Views;
		// Register strings from Views
		$views = get_posts('post_type=view&post_status=any&posts_per_page=-1');
		foreach ( $views as $key => $post ) {
			$post = (array) $post;
			// Register strings in the content
			wpv_register_wpml_strings( $post['post_content'] );
			// Register strings in the Filter HTML textarea
			$view_array = $WP_Views->get_view_settings( $post["ID"] );
			if ( isset( $view_array['filter_meta_html'] ) ) {
				wpv_add_controls_labels_to_translation( $view_array['filter_meta_html'], $post["ID"] );
				wpv_register_wpml_strings( $view_array['filter_meta_html'] );
			}
			// Register strings in the Layout HTML textarea
			$view_layout_array = $WP_Views->get_view_layout_settings( $post["ID"] );
			if ( isset( $view_layout_array['layout_meta_html'] ) ) {
				wpv_register_wpml_strings( $view_layout_array['layout_meta_html'] );
			}
		}
		// Register strings from Content Templates
		$view_templates = get_posts('post_type=view-template&post_status=any&posts_per_page=-1');
		foreach ( $view_templates as $key => $post ) {
			$post = (array) $post;
			// Register strings in the content
			wpv_register_wpml_strings( $post['post_content'] );
		}
		// Update the flag in the options so this is only run once
		update_option( 'wpv_strings_translation_initialized', 1 );
	}
}

/**
* wpv_add_string_translation_to_formatting_instructions
* 
* Registers the hooks to add the String Translation information to the formatting instructions under CodeMirror textareas
*
* @since 1.7
*/

add_action( 'init', 'wpv_add_string_translation_to_formatting_instructions' );

function wpv_add_string_translation_to_formatting_instructions() {
	if ( function_exists( 'wpml_string_shortcode' )	) {
		// Register the section
		add_filter( 'wpv_filter_formatting_help_filter', 'wpv_register_wpml_section' );
		add_filter( 'wpv_filter_formatting_help_layout', 'wpv_register_wpml_section' );
		add_filter( 'wpv_filter_formatting_help_inline_content_template', 'wpv_register_wpml_section' );
		add_filter( 'wpv_filter_formatting_help_layouts_content_template_cell', 'wpv_register_wpml_section' );
		add_filter( 'wpv_filter_formatting_help_combined_output', 'wpv_register_wpml_section' );
		add_filter( 'wpv_filter_formatting_help_content_template', 'wpv_register_wpml_section' );
		// Register the section content
		add_filter( 'wpv_filter_formatting_instructions_section', 'wpv_wpml_string_translation_shortcodes_instructions', 10, 2 );
	}
}

/**
* wpv_register_wpml_section
*
* Registers the formatting instructions section for WPML in several textareas
*
* Check if the string_translation section has already been registered. If not, add it to the hooked formatting instructions boxes
*
* @param $sections (array) Registered sections for the formatting instructions
*
* @return $sections (array)
*
* @since 1.7
*/

function wpv_register_wpml_section( $sections ) {
	if ( ! in_array( 'string_translation', $sections ) ) {
		array_splice( $sections, -2, 0, array( 'string_translation' ) );
	}
	return $sections;
}


/**
 * wpv_wpml_string_translation_shortcodes_instructions
 *
 * Registers the content of the WPML section in several formatting instructions boxes
 *
 * @param $return (array|false) What to return, generally an array for the section that you want to give content to
 *     'classname' => (string) A specific classname for this section, useful when some kind of show/hide functionality is needed
 *     'title' => (string) The title of the section
 *     'content' => (string) The main text of the section
 *     'table' => (array) Table of ( Element, Description) arrays to showcase shortcodes, markup or related things
 *         array(
 *             'element' => (string) The element to describe. You can use some classes to add styling like in the CodeMirror instances: .wpv-code-shortcode, .wpv-code-html, .wpv-code-attr or .wpv-code-val
 *             'description' => (string) The element description
 *         )
 *     'content_extra' => (string) Extra text to be displayed after the table
 * @param $section (string) The name of the section
 * @return array $return (array|false)
 *
 * @since 1.7
 */
function wpv_wpml_string_translation_shortcodes_instructions( $return, $section ) {
	if ( $section == 'string_translation' ) {
		$return = array(
			'classname' => 'js-wpv-editor-instructions-for-string-translation',
			'title' => __( 'String translation shortcodes', 'wpv-views' ),
			'content' => '',
			'table' => array(
				array(
					'element' => '<span class="wpv-code-shortcode">[wpml-string</span> <span class="wpv-code-attr">context</span>=<span class="wpv-code-val">"wpv-views"</span><span class="wpv-code-shortcode">]</span>' 
							. __( 'Text content', 'wpv-views' )
							. '<span class="wpv-code-shortcode">[/wpml-string]</span>',
					'description' => __( 'Makes the text content translatable via WPML\'s String Translation.', 'wpv-views' )
				)
			),
			'content_extra' => ''
		);
	}
	return $return;
}

/**
* Register the wpml-string shortcode into the shortcodes GUI API
*
* @since 1.9
*/

add_filter( 'wpv_filter_wpv_shortcodes_gui_data', 'wpv_shortcodes_register_wpml_string_data' );

function wpv_shortcodes_register_wpml_string_data( $views_shortcodes ) {
	$views_shortcodes['wpml-string'] = array(
		'callback' => 'wpv_shortcodes_get_wpml_string_data'
	);
	return $views_shortcodes;
}

function wpv_shortcodes_get_wpml_string_data() {
	if ( ! function_exists('icl_register_string') ) {
		return array();
	}
	global $wpdb;
	$results = $wpdb->get_results( 
		$wpdb->prepare( 
            "SELECT DISTINCT context 
            FROM {$wpdb->prefix}icl_strings
            ORDER BY %s ASC 
			LIMIT %d", 
			'context',
			16
		) 
	);
	if ( count( $results ) > 15 ) {
		$context_attr_settings = array(
			'label' => __( 'WPML Context', 'wpv-views'),
			'type' => 'suggest',
			'action' => 'wpv_suggest_wpml_contexts',
			'default' => '',
			'required' => true,
			'placeholder' => __( 'Start typing', 'wpv-views' ),
		);
	} else {
		$options = array(
			'' => __( 'Select one WPML context', 'wpv-views' )
		);
		foreach ( $results as $row ) {
			$options[$row->context] = $row->context;
		}
		$context_attr_settings = array(
			'label' => __( 'WPML Context', 'wpv-views'),
			'type' => 'select',
			'options' => $options,
			'default' => '',
			'required' => true,
		);
	}
	
    $data = array(
        'name' => __( 'Translatable string', 'wpv-views' ),
        'label' => __( 'Translatable string', 'wpv-views' ),
        'attributes' => array(
            'display-options' => array(
                'label' => __('Display options', 'wpv-views'),
                'header' => __('Display options', 'wpv-views'),
                'fields' => array(
                    'context' => $context_attr_settings,
                ),
				'content' => array(
					'label' => __( 'String to translate', 'wpv-views' )
				)
            ),
        ),
    );
    return $data;
}

/**
* wpv_suggest_wpml_contexts
*
* Suggest for WPML string shortcode context, from a suggest callback
*
* @since 1.4
*/

add_action('wp_ajax_wpv_suggest_wpml_contexts', 'wpv_suggest_wpml_contexts');
add_action('wp_ajax_nopriv_wpv_suggest_wpml_contexts', 'wpv_suggest_wpml_contexts');

function wpv_suggest_wpml_contexts() {
	global $wpdb;
	$context_q = '%' . wpv_esc_like( $_REQUEST['q'] ) . '%';
	$results = $wpdb->get_results( 
		$wpdb->prepare( 
            "SELECT DISTINCT context 
            FROM {$wpdb->prefix}icl_strings
            WHERE context LIKE %s
            ORDER BY context ASC", 
			$context_q 
		) 
	);
	foreach ( $results as $row ) {
		echo $row->context . "\n";
	}
	die();
}

/**
* wpv_force_shortcodes_gui_wpml_string
*
* Enforce the wpml-string shortcode on the Basic section of the Fields and Views dialog
* Even when displaying data for Views listing taxonomy terms or users
*
* @since 1.10
*/

add_filter( 'editor_addon_menus_wpv-views', 'wpv_force_shortcodes_gui_wpml_string', 90 );

function wpv_force_shortcodes_gui_wpml_string( $menu = array() ) {
	global $sitepress;
	$basic = __( 'Basic', 'wpv-views' );
	if (
		isset( $menu[$basic] )
		&& isset( $sitepress )
		&& function_exists( 'wpml_string_shortcode' )
	) {
		$nonce = wp_create_nonce( 'wpv_editor_callback' );
		$wpml_string_title = __( 'Translatable string', 'wpv-views' );
		$menu[$basic][$wpml_string_title] = array( 
			$wpml_string_title,
			'wpml-string',
			$basic,
			"WPViews.shortcodes_gui.wpv_insert_popup('wpml-string', '" . $wpml_string_title . "', {}, '" . $nonce . "', this )"
		);
	}
	return $menu;
}

/**
* wpv_disable_wpml_admin_lang_switcher
*
* Disable the WPML admin bar language switcher on Views, CT and WPA related pages
*
* @since 1.9
*/

add_filter('wpml_show_admin_language_switcher', 'wpv_disable_wpml_admin_lang_switcher');

function wpv_disable_wpml_admin_lang_switcher( $state ) {
	global $pagenow;
	$disable_in_views_pages = array(
		'views', 'views-editor', 'embedded-views', 'views-embedded', 
		'view-templates', 'ct-editor', 'embedded-views-templates', 'view-templates-embedded', 
		'view-archives', 'view-archives-editor', 'embedded-views-archives', 'view-archives-embedded', 
		'views-settings', 'views-import-export', 'views-debug-information', 'views-update-help'
	);
	if ( 
		$pagenow == 'admin.php' 
		&& isset( $_GET['page'] ) 
		&& in_array( $_GET['page'], $disable_in_views_pages )
	) {
		$state = false;
	}
	return $state;
}


/**
 * Singleton encapsulating (new) WPML-related functionality.
 *
 * @since 1.10
 */
class WPV_WPML_Integration_Embedded {

    /**
     * The instance.
     *
     * @var WPV_WPML_Integration_Embedded
     * @since 1.10
     */
    protected static $instance = null;


    /**
     * Get the instance of the singleton (and create it if it doesn't exist yet).
     *
     * @return WPV_WPML_Integration_Embedded
     * @since 1.10
     */
    public static function get_instance() {
        if( null == self::$instance ) {
            self::$instance = new WPV_WPML_Integration_Embedded();
        }
        return self::$instance;
    }


    /**
     * Initialize the singleton.
     *
     * Should be called during init action.
     *
     * @since 1.10
     */
    public static function init() {
        self::get_instance();
    }


    /**
     * @var bool Holds information about the state of WPML TM activation.
     * @since 1.10
     */
    protected $_is_wpml_tm_loaded = false;


    /**
     * Singleton instantiation.
     *
     * Should happen before plugins_loaded action. Register further action hooks.
     *
     * @since 1.10
     */
    protected function __construct() {
        add_action( 'admin_init', array( $this, 'admin_init' ) );

        // this will be run during plugins_loaded
        add_action( 'wpml_tm_loaded', array( $this, 'wpml_tm_loaded' ) );
    }


    /**
     * WPML integration actions on admin_init.
     *
     * @since 1.10
     */
    public function admin_init() {
        $this->hook_filters_for_links();
    }


    /**
     * wpml_tm_loaded action hook.
     *
     * @since 1.10
     */
    public function wpml_tm_loaded() {
        $this->_is_wpml_tm_loaded = true;
    }


    /**
     * Determine whether WPML Translation Management is active and fully loaded.
     *
     * @return bool
     * @since 1.10
     */
    public function is_wpml_tm_loaded() {
        return $this->_is_wpml_tm_loaded;
    }


    /**
     * Hook into WPML filters and modify links to edit or view Content Templates in
     * WPML Translation Management.
     *
     * @since 1.10
     */
    protected function hook_filters_for_links() {
        add_filter( 'wpml_document_edit_item_link', array( $this, 'wpml_get_document_edit_link_ct' ), 10, 5 );
        add_filter( 'wpml_document_view_item_link', array( $this, 'wpml_get_document_view_link_ct' ), 10, 5 );
        add_filter( 'wpml_document_edit_item_url', array( $this, 'wpml_document_edit_item_url_ct' ), 10, 3 );
    }


    /**
     * Modify Edit link on Translation Dashboard of WPML Translation Management
     *
     * For Content Templates in default language, return the link to CT read-only page. For
     * CTs in different languages, don't show any link.
     *
     * @param string $post_edit_link The HTML code of the link.
     * @param string $label Link label to be displayed.
     * @param object $current_document
     * @param string $element_type 'post' for posts.
     * @param string $content_type If $element_type is 'post', this will contain a post type.
     *
     * @return string Link HTML.
     *
     * @since 1.10
     */
    public function wpml_get_document_edit_link_ct( $post_edit_link, $label, $current_document, $element_type, $content_type ) {

        if( 'post' == $element_type && WPV_Content_Template_Embedded::POST_TYPE == $content_type ) {
            $ct_id = $current_document->ID;

            // we know WPML is active, nothing else should call this filter
            global $sitepress;

            if( $sitepress->get_default_language() != $current_document->language_code ) {
                // We don't allow editing CTs in nondefault languages in our editor.
                // todo add link to translation editor instead
                $post_edit_link = '';
            } else {
                $link = apply_filters( 'icl_post_link', array(), WPV_Content_Template_Embedded::POST_TYPE, $ct_id, 'edit' );
                $is_disabled = wpv_getarr( $link, 'is_disabled', false );
                $url = wpv_getarr( $link, 'url' );

                if( $is_disabled ) {
                    $post_edit_link = '';
                } else if( !empty( $url ) ) {
                    $post_edit_link = sprintf( '<a href="%s" target="_blank">%s</a>', $url, $label );
                }
            }
        }
        return $post_edit_link;
    }


    /**
     * Modify View link on Translation Dashboard of WPML Translation Management
     *
     * Content Templates have no clear "View" option, so we're disabling the link for them.
     *
     * @param string $post_view_link Current view link
     * @param string $label Link label to be displayed
     * @param object $current_document
     * @param string $element_type 'post' for posts.
     * @param string $content_type If $element_type is 'post', this will contain a post type.
     *
     * @return string Link HTML
     *
     * @since 1.10
     */
    public function wpml_get_document_view_link_ct( $post_view_link,
        /** @noinspection PhpUnusedParameterInspection */ $label,
        /** @noinspection PhpUnusedParameterInspection */ $current_document,
                                                 $element_type, $content_type ) {
        if( 'post' == $element_type && WPV_Content_Template_Embedded::POST_TYPE == $content_type ) {
            // For a Content Template, there is nothing to view directly
            // todo link to some example content, if any exists
            $post_view_link = '';
        }
        return $post_view_link;
    }


    /**
     * Modify edit URLs for Content Templates on Translation Queue in WPML Translation Management.
     *
     * For CT, return URL to CT edit page.
     *
     * @param string $edit_url Current edit URL
     * @param string $content_type For posts, this will be post_{$post_type}.
     * @param int $element_id Post ID if the element is a post.
     *
     * @return string Edit URL.
     *
     * @since 1.10
     */
    public function wpml_document_edit_item_url_ct( $edit_url, $content_type, $element_id ) {
        if ( 'post_' . WPV_Content_Template_Embedded::POST_TYPE == $content_type ) {
            if ( $element_id ) {
                $link = apply_filters( 'icl_post_link', array(), WPV_Content_Template_Embedded::POST_TYPE, $element_id, 'edit' );
                $url = wpv_getarr( $link, 'url' );
                $is_disabled = wpv_getarr( $link, 'is_disabled', false );
                if( $is_disabled ) {
                    $edit_url = ''; // todo check if this works well
                } else if( !empty( $url ) ) {
                    $edit_url = $url;
                }
            }
        }
        
        return $edit_url;
    }

}