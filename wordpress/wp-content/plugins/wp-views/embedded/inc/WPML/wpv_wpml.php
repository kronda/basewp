<?php

/**
* WPML Translation Management integration
*/

/**
* wpv_wpml_icl_current_language
*
* Auxiliar function to override the current language
*
* @param $lang string the current language
*
* @return $sitepress->get_default_language()
*
* @since unknown
*/

function wpv_wpml_icl_current_language($lang) { // TODO check why is this needed: it just returns the default language when looking for the current language...
    global $sitepress;

    return $sitepress->get_default_language();
}

/**
* wpml_content_fix_links_to_translated_content
*
* Converts links in a string to the corresponding ones in the current language
*
* @param $body string to check against
*
* @return $body
*
* @since unknown
*/

function wpml_content_fix_links_to_translated_content($body){
    global $wpdb, $sitepress, $sitepress_settings, $wp_taxonomies;

    global $WP_Views;
    $settings = $WP_Views->get_options();
    if (isset($settings['wpml_fix_urls'])) {
        $wpml_fix_urls = $settings['wpml_fix_urls'];
    } else {
        $wpml_fix_urls = true;
    }

    if (!$wpml_fix_urls) {
        return $body;
    }


    if (isset($sitepress)) {

        static $content_cache = array();

        $target_lang_code = $sitepress->get_current_language();

        $cache_code = md5($body . $target_lang_code);
        if (isset($content_cache[$cache_code])) {
            $body = $content_cache[$cache_code];
        } else {

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
                            $kind = 'post_' . $wpdb->get_var("SELECT post_type FROM {$wpdb->posts} WHERE ID='{$value}'");
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
                            $value = $wpdb->get_var("SELECT term_taxonomy_id FROM {$wpdb->terms} t
                                JOIN {$wpdb->term_taxonomy} x ON t.term_id = x.term_id WHERE x.taxonomy='{$taxonomy}' AND t.slug='{$value}'");
                        } else {
                            $found = false;
                            foreach($wp_taxonomies as $ktax => $tax){
                                if($tax->query_var && $key == $tax->query_var){
                                    $found = true;
                                    $is_tax = true;
                                    $kind = 'tax_' . $ktax;
                                    $value = $wpdb->get_var("
                                        SELECT term_taxonomy_id FROM {$wpdb->terms} t
                                            JOIN {$wpdb->term_taxonomy} x ON t.term_id = x.term_id WHERE x.taxonomy='{$ktax}' AND t.slug='{$value}'");
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
                                $translated_id = $wpdb->get_var("SELECT slug FROM {$wpdb->terms} t JOIN {$wpdb->term_taxonomy} x ON t.term_id=x.term_id WHERE x.term_taxonomy_id=$translated_id");
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
* wpml_content_get_link_paths
*
* Parse links from a given string
*
* @param $body string to be parsed
*
* @return $links array() of parsed links
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

/**
* wpv_wpml_settings
*
* Filter hooked into icl_tm_menu_mcsetup. Add View settings to the Translation Management setup screen
*
* @note displays different HTML for different WPML versions
*
* @since unknown
*/

add_action('icl_tm_menu_mcsetup', 'wpv_wpml_settings');
function wpv_wpml_settings() {
    global $WP_Views;
    $settings = $WP_Views->get_options();
    if (isset($settings['wpml_fix_urls'])) {
        $wpml_fix_urls = $settings['wpml_fix_urls'];
    } else {
        $wpml_fix_urls = true;
    }

    wp_nonce_field('wpv_wpml_save_settings_nonce', 'wpv_wpml_save_settings_nonce');
    
    if(defined('ICL_SITEPRESS_VERSION')) {

	if ( version_compare( ICL_SITEPRESS_VERSION, '3.0' )  < 0 ) {
    
    ?>


    <?php
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
                            <input id="wpv_wpml_fix_urls" type="checkbox" value="1" <?php if($wpml_fix_urls): ?>checked<?php endif; ?> />
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
                    <input id="wpv_wpml_fix_urls" type="checkbox" value="1" <?php if($wpml_fix_urls): ?>checked<?php endif; ?> />
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
        global $WP_Views;
        $settings = $WP_Views->get_options();

        if (isset($_POST['wpv_wpml_fix_urls'])) {
            $settings['wpml_fix_urls'] = $_POST['wpv_wpml_fix_urls'];
        } else {
            $settings['wpml_fix_urls'] = false;
        }

        $WP_Views->save_options($settings);

    }

    die();
}

/**
* WPML String Translation integration
*/

/**
* wpv_wpml_string_in_custom_inner_shortcodes
*
* Filter hooked into wpv_custom_inner_shortcodes. Add the [wpml-string] shortcode to the allowed inner shortcodes, but only if the [wpml-string] shortcode itself exists
*
* @param $custom_inner_shortcodes array() of allowed custom inner shortcodes
*
* @return $custom_inner_shortcodes
*
* @since 1.4.0
*/

add_filter('wpv_custom_inner_shortcodes', 'wpv_wpml_string_in_custom_inner_shortcodes');

function wpv_wpml_string_in_custom_inner_shortcodes($custom_inner_shortcodes) {
	if ( function_exists( 'wpml_string_shortcode' ) ) {
		if ( !is_array( $custom_inner_shortcodes ) ) $custom_inner_shortcodes = array();
		$custom_inner_shortcodes[] = 'wpml-string';
		$custom_inner_shortcodes = array_unique( $custom_inner_shortcodes );
	}
	return $custom_inner_shortcodes;
}

/**
* wpv_add_controls_labels_to_translation
*
* Utility function to translate strings used in wpv-control shortcodes
*
* @param $content the content of the Filter HTML textarea to parse
* @param $view_id the current View ID to build the content from
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
*
* @return $output array( N => array( 'context'=> $context, 'content'=> $content, 'name'=> $name ) )
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
* @param $content the string to parse shortcodes from
*
* @since 1.5.0
*/

function wpv_register_wpml_strings( $content ) {
	if( function_exists('icl_register_string') ) {
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
*/

add_action('plugins_loaded', 'wpv_register_wpml_strings_on_activation', 99);

function wpv_register_wpml_strings_on_activation() {
	if ( function_exists( 'icl_register_string' ) && defined( 'WPML_ST_VERSION' ) && !get_option( 'wpv_strings_translation_initialized', false ) ) {
		// Register strings from Views
		$views = get_posts('post_type=view&post_status=any&posts_per_page=-1');
		foreach ( $views as $key => $post ) {
			$post = (array) $post;
			// Register strings in the content
			wpv_register_wpml_strings( $post['post_content'] );
			// Register strings in the Filter HTML textarea
			$view_array = get_post_meta( $post["ID"], '_wpv_settings', true);
			if ( isset( $view_array['filter_meta_html'] ) ) {
				wpv_add_controls_labels_to_translation( $view_array['filter_meta_html'], $post["ID"] );
				wpv_register_wpml_strings( $view_array['filter_meta_html'] );
			}
			// Register strings in the Layout HTML textarea
			$view_layout_array = get_post_meta($post["ID"], '_wpv_layout_settings', true);
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

