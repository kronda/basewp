<?php

function wpv_admin_menu_import_export() {

    ?>
    <div class="wrap">

        <div id="icon-views" class="icon32"><br /></div>
        <h2><?php _e('Views Import / Export', 'wpv-views') ?></h2>

		<div class="wpv-setting-container">

			<div class="wpv-settings-header">
				<h3><?php _e('Export Views, WordPress Archives and Content Templates', 'wpv-views'); ?></h3>
			</div>

			<div class="wpv-setting">
		        <form name="View_export" action="<?php echo admin_url('edit.php'); ?>" method="post">

		            <h4><?php _e('Download all Views, WordPress Archives and Content Templates', 'wpv-views'); ?></h4>
		            <p><?php _e('When importing to theme:', 'wpv-views'); ?></p>
		            <ul>
						<li>
							<input id="radio-1" type="radio" value="ask" name="import-mode" checked="checked" />
							<label for="radio-1"><?php _e('ask user for approval', 'wpv-views'); ?></label>
						</li>
						<li>
							<input id="radio-2" type="radio" value="auto" name="import-mode" />
							<label for="radio-2"><?php _e('import automatically', 'wpv-views'); ?></label>
						</li>
					</ul>
		            <h4><?php _e('Affiliate details for theme designers:', 'wpv-views'); ?></h4>
		            <ul>
						<li>
							<label for="aid"><?php _e('Affiliate ID:', 'wpv-views'); ?></label><br>
							<input type="text" name="aid" id="aid" />
						</li>
						<li>
							<label for="akey"><?php _e('Affiliate Key:', 'wpv-views'); ?></label><br>
							<input type="text" name="akey" id="akey" />
						</li>
		            </ul>
		            <p>
			            <?php _e('You only need to enter affiliate settings if you are a theme designer and want to receive affiliate commission.', 'wpv-views'); ?>
			            <?php echo sprintf(__('Log into <a href="%s">your account</a> and go to <a href="%s">affiliate settings</a> for details.', 'wpv-views'),
			                'http://wp-types.com',
			                'http://wp-types.com/shop/account/?acct=affiliate');
			            ?>
		            </p>

		            <p class="update-button-wrap">
		            	<input id="wpv-export" class="button-primary" type="submit" value="<?php _e('Export', 'wpv-views'); ?>" name="export" />
		            </p>

		            <?php wp_nonce_field('wpv-export-nonce', 'wpv-export-nonce'); ?>

		        </form>
			</div>

		</div> <!-- .wpv-setting-container -->

        <?php wpv_admin_import_form( '' ); ?>

    </div> <!-- .wrap -->

    <?php
}

/**
 * Exports data to XML.
 * Note: whatever chage done here must be done too in wpv_admin_export_selected_data()
 */
function wpv_admin_export_data($download = true) {
    global $WP_Views;

    require_once WPV_PATH_EMBEDDED . '/common/array2xml.php';
    $xml = new ICL_Array2XML();
    $data = array();

    // SRDJAN - add siteurl, upload url, record taxonomies old IDs
    // https://icanlocalize.basecamphq.com/projects/7393061-wp-views/todo_items/142382866/comments
    // https://icanlocalize.basecamphq.com/projects/7393061-wp-views/todo_items/142389966/comments
    $data['site_url'] = get_site_url();
    if ( is_multisite() ) {
        $data['fileupload_url'] = get_option('fileupload_url');
    } else {
        $wp_upload_dir = wp_upload_dir();
        $data['fileupload_url'] = $wp_upload_dir['baseurl'];
    }

    // Get the views
    $views = get_posts( 'post_type=view&post_status=any&posts_per_page=-1' );
    if ( !empty( $views ) ) {
	global $_wp_additional_image_sizes;
	if ( !isset( $_wp_additional_image_sizes ) || !is_array( $_wp_additional_image_sizes ) ) {
		$_wp_additional_image_sizes = array();
	}
	$attached_images_sizes = array_merge(
		// additional thumbnail sizes
		array_keys( $_wp_additional_image_sizes ),
		// wp default thumbnail sizes
		array( 'thumbnail', 'medium', 'large' )
	);
        $data['views'] = array( '__key' => 'view' );
        foreach ( $views as $key => $post ) {
            $post = (array) $post;
            if ( $post['post_name'] ) {
                $post_data = array();
                $copy_data = array( 'ID', 'post_content', 'post_title', 'post_name', 'post_excerpt', 'post_type', 'post_status' );
                foreach ( $copy_data as $copy ) {
                    if ( isset( $post[$copy] ) ) {
                        $post_data[$copy] = $post[$copy];
                    }
                }
                $data['views']['view-' . $post['ID']] = $post_data;
                $meta = get_post_custom( $post['ID'] );
                if ( !empty( $meta ) ) {
                    $data['view']['view-' . $post['ID']]['meta'] = array();
                    foreach ( $meta as $meta_key => $meta_value ) {
                        if ( $meta_key == '_wpv_settings' ) {
                            $value = maybe_unserialize( $meta_value[0] );
                            // Add any taxonomy terms so we can re-map when we import.
                            if ( !empty( $value['taxonomy_terms'] ) ) {
                    			$taxonomy = $value['taxonomy_type'][0];
                                foreach ( $value['taxonomy_terms'] as $term_id ) {
                                    $term = get_term($term_id, $taxonomy);
                                    if ( isset( $term ) && !is_wp_error( $term ) ) {
                                        $data['terms_map']['term_' . $term->term_id]['old_id'] = $term->term_id;
                                        $data['terms_map']['term_' . $term->term_id]['slug'] = $term->slug;
                                        $data['terms_map']['term_' . $term->term_id]['taxonomy'] = $taxonomy;
                                    }
                                }
                            }
							// Adjust several settings stored as an indexed array so we do not break the XML
							// Maybe this should be moved to a specific routine
							// It should replace convert_ids_to_names_in_settings and convert_ids_to_names_in_filters
                            if ( isset( $value['author_mode'] ) ) {
								$value['author_mode']['type'] = $value['author_mode'][0];
								unset( $value['author_mode'][0] );
							}
							if ( isset( $value['taxonomy_parent_mode'] ) ) {
								$value['taxonomy_parent_mode']['state'] = $value['taxonomy_parent_mode'][0];
								unset( $value['taxonomy_parent_mode'][0] );
							}
							if ( isset( $value['taxonomy_search_mode'] ) ) {
								$value['taxonomy_search_mode']['state'] = $value['taxonomy_search_mode'][0];
								unset( $value['taxonomy_search_mode'][0] );
							}
							if ( isset( $value['search_mode'] ) ) {
								$value['search_mode']['state'] = $value['search_mode'][0];
								unset( $value['search_mode'][0] );
							}
							if ( isset( $value['id_mode'] ) ) {
								$value['id_mode']['state'] = $value['id_mode'][0];
								unset( $value['id_mode'][0] );
							}
							if ( isset( $value['users_mode'] ) ) {
								$value['users_mode']['type'] = $value['users_mode'][0];
								unset( $value['users_mode'][0] );
							}
                            $value = $WP_Views->convert_ids_to_names_in_settings( $value );
                            if ( isset( $value['post_id_ids_list'] ) && !empty( $value['post_id_ids_list'] ) ) {
								$value['post_id_ids_list'] = $WP_Views->convert_ids_to_names_in_filters( $value['post_id_ids_list'] );
                            }
                            $data['views']['view-' . $post['ID']]['meta'][$meta_key] = $value;
                        }
                        if ( $meta_key == '_wpv_layout_settings' ) {
                            $value = maybe_unserialize( $meta_value[0] );
                            $value = $WP_Views->convert_ids_to_names_in_layout_settings( $value );
                            $data['views']['view-' . $post['ID']]['meta'][$meta_key] = $value;
                        }
                        if ( $meta_key == '_wpv_description' ) {
							$value = maybe_unserialize( $meta_value[0] );
							$data['views']['view-' . $post['ID']]['meta'][$meta_key] = $value;
                        }
                    }
                    if ( empty( $data['views']['view-' . $post['ID']]['meta'] ) ) {
                        unset( $data['views']['view-' . $post['ID']]['meta'] );
                    }
                }
                // Juan - add images for exporting
				// https://icanlocalize.basecamphq.com/projects/7393061-wp-views/todo_items/150919286/comments
				$att_args = array( 'post_type' => 'attachment', 'numberposts' => -1, 'post_status' => null, 'post_parent' => $post['ID'] );
				$attachments = get_posts( $att_args );
				if ( $attachments ) {
					$data['views']['view-' . $post['ID']]['attachments'] = array();
					foreach ( $attachments as $attachment ) {
						preg_match( '/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $attachment->guid, $matches );
						if ( isset( $matches ) && is_array( $matches ) && isset( $matches[0] ) ) {
							$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID] = array();
							$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['title'] = $attachment->post_title;
							$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['content'] = $attachment->post_content;
							$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['excerpt'] = $attachment->post_excerpt;
							$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['status'] = $attachment->post_status;
							$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['alt'] = get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true );
							$imdata = base64_encode( file_get_contents( $attachment->guid ) );
							$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['data'] = $imdata;
							$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['filename'] = basename( $matches[0] );
							$this_settings = get_post_meta($post['ID'], '_wpv_settings', true);
							$this_layout_settings = get_post_meta($post['ID'], '_wpv_layout_settings', true);
							// NOTE we adjust the 'spinner_image' for pagination and dps on import
							if ( isset( $this_settings['pagination']['spinner_image_uploaded'] ) && $attachment->guid == $this_settings['pagination']['spinner_image_uploaded'] ) {
								$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['custom_spinner'] = 'this';
							}
							if ( isset( $this_settings['dps']['spinner_image_uploaded'] ) && $attachment->guid == $this_settings['dps']['spinner_image_uploaded'] ) {
								$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['dps_custom_spinner'] = 'this';
							}
							$imthumbs = array();
							foreach ($attached_images_sizes as $ts) {
								$imthumbs[$ts] = wp_get_attachment_image_src( $attachment->ID, $ts );
							}
							foreach ($imthumbs as $thumbsize => $thumbdata) {
								if ( isset( $this_settings['pagination']['spinner_image_uploaded'] ) && $thumbdata == $this_settings['pagination']['spinner_image_uploaded'] ) {
									$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['custom_spinner'] = 'this_' . $thumbsize;
								}
								if ( isset( $this_settings['dps']['spinner_image_uploaded'] ) && $thumbdata == $this_settings['dps']['spinner_image_uploaded'] ) {
									$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['dps_custom_spinner'] = 'this_' . $thumbsize;
								}
							}
							
							$this_settings_metaboxes = array(
								'filter_meta_html',
								'filter_meta_html_css',
								'filter_meta_html_js',
								'layout_meta_html_css',
								'layout_meta_html_js'
							);
							$this_layout_settings_metaboxes = array(
								'layout_meta_html'
							);
							
							foreach ( $this_settings_metaboxes as $metabox_id ) {
								if ( isset( $this_settings[$metabox_id] ) ) {
									$pos = strpos( $this_settings[$metabox_id], $attachment->guid );
									if ( $pos !== false ) {
										$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['on_' . $metabox_id] = $attachment->guid;
									}
									foreach ( $imthumbs as $thumbsize => $thumbdata ) {
										if ( !empty( $thumbdata ) && isset( $thumbdata[0] ) ) {
											$pos = strpos( $this_settings[$metabox_id], $thumbdata[0] );
											if ( $pos !== false ) {
												$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['on_' . $metabox_id . '_sizes'][$thumbsize] = $thumbdata[0];
											}
										}
									}
								}
							}
							
							foreach ( $this_layout_settings_metaboxes as $metabox_id ) {
								if ( isset( $this_layout_settings[$metabox_id] ) ) {
									$pos = strpos( $this_layout_settings[$metabox_id], $attachment->guid );
									if ( $pos !== false ) {
										$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['on_' . $metabox_id] = $attachment->guid;
									}
									foreach ( $imthumbs as $thumbsize => $thumbdata ) {
										if ( !empty( $thumbdata) && isset( $thumbdata[0] ) ) {
											$pos = strpos( $this_layout_settings[$metabox_id], $thumbdata[0] );
											if ( $pos !== false ) {
												$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['on_' . $metabox_id . '_sizes'][$thumbsize] = $thumbdata[0];
											}
										}
									}
								}
							}
							/*
							if ( isset( $this_settings['filter_meta_html'] ) ) {
								$pos = strpos( $this_settings['filter_meta_html'], $attachment->guid );
								if ($pos !== false) {
									$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['on_filter_meta_html'] = $attachment->guid;
								}
								foreach ($imthumbs as $thumbsize => $thumbdata) {
									if (!empty($thumbdata) && isset($thumbdata[0])) {
										$pos = strpos( $this_settings['filter_meta_html'], $thumbdata[0] );
										if ($pos !== false) {
											$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['on_filter_meta_html_sizes'][$thumbsize] = $thumbdata[0];
										}
									}
								}
							}
							if ( isset( $this_settings['filter_meta_html_css'] ) ) {
								$pos = strpos( $this_settings['filter_meta_html_css'], $attachment->guid );
								if ($pos !== false) {
									$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['on_filter_meta_html_css'] = $attachment->guid;
								}
								foreach ($imthumbs as $thumbsize => $thumbdata) {
									if (!empty($thumbdata) && isset($thumbdata[0])) {
										$pos = strpos( $this_settings['filter_meta_html_css'], $thumbdata[0] );
										if ($pos !== false) {
											$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['on_filter_meta_html_css_sizes'][$thumbsize] = $thumbdata[0];
										}
									}
								}
							}
							if ( isset( $this_settings['filter_meta_html_js'] ) ) {
								$pos = strpos( $this_settings['filter_meta_html_js'], $attachment->guid );
								if ($pos !== false) {
									$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['on_filter_meta_html_js'] = $attachment->guid;
								}
								foreach ($imthumbs as $thumbsize => $thumbdata) {
									if (!empty($thumbdata) && isset($thumbdata[0])) {
										$pos = strpos( $this_settings['filter_meta_html_js'], $thumbdata[0] );
										if ($pos !== false) {
											$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['on_filter_meta_html_js_sizes'][$thumbsize] = $thumbdata[0];
										}
									}
								}
							}
							if ( isset( $this_layout_settings['layout_meta_html'] ) ) {
								$pos = strpos( $this_layout_settings['layout_meta_html'], $attachment->guid );
								if ($pos !== false) {
									$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['on_layout_meta_html'] = $attachment->guid;
								}
								foreach ($imthumbs as $thumbsize => $thumbdata) {
									if (!empty($thumbdata) && isset($thumbdata[0])) {
										$pos = strpos( $this_layout_settings['layout_meta_html'], $thumbdata[0] );
										if ($pos !== false) {
											$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['on_layout_meta_html_sizes'][$thumbsize] = $thumbdata[0];
										}
									}
								}
							}
							if ( isset( $this_settings['layout_meta_html_css'] ) ) {
								$pos = strpos( $this_settings['layout_meta_html_css'], $attachment->guid );
								if ($pos !== false) {
									$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['on_layout_meta_html_css'] = $attachment->guid;
								}
								foreach ($imthumbs as $thumbsize => $thumbdata) {
									if (!empty($thumbdata) && isset($thumbdata[0])) {
										$pos = strpos( $this_settings['layout_meta_html_css'], $thumbdata[0] );
										if ($pos !== false) {
											$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['on_layout_meta_html_css_sizes'][$thumbsize] = $thumbdata[0];
										}
									}
								}
							}
							if ( isset( $this_settings['layout_meta_html_js'] ) ) {
								$pos = strpos( $this_settings['layout_meta_html_js'], $attachment->guid );
								if ($pos !== false) {
									$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['on_layout_meta_html_js'] = $attachment->guid;
								}
								foreach ($imthumbs as $thumbsize => $thumbdata) {
									if (!empty($thumbdata) && isset($thumbdata[0])) {
										$pos = strpos( $this_settings['layout_meta_html_js'], $thumbdata[0] );
										if ($pos !== false) {
											$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['on_layout_meta_html_js_sizes'][$thumbsize] = $thumbdata[0];
										}
									}
								}
							}
							*/
							$poscont = strpos( $data['views']['view-' . $post['ID']]['post_content'], $attachment->guid );
							if ($poscont !== false) {
								$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['on_post_content'] = $attachment->guid;
							}
							foreach ($imthumbs as $thumbsize => $thumbdata) {
								if (!empty($thumbdata) && isset($thumbdata[0])) {
									$pos = strpos( $data['views']['view-' . $post['ID']]['post_content'], $thumbdata[0] );
									if ($pos !== false) {
										$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['on_post_content_sizes'][$thumbsize] = $thumbdata[0];
									}
								}
							}
						}
					}
				}
			}
		}
	}

    // Get the Content templates
    $view_templates = get_posts( 'post_type=view-template&post_status=any&posts_per_page=-1' );
    if ( !empty( $view_templates ) ) {
	global $_wp_additional_image_sizes;
	if ( !isset( $_wp_additional_image_sizes ) || !is_array( $_wp_additional_image_sizes ) ) {
		$_wp_additional_image_sizes = array();
	}
	$attached_images_sizes = array_merge(
		// additional thumbnail sizes
		array_keys( $_wp_additional_image_sizes ),
		// wp default thumbnail sizes
		array( 'thumbnail', 'medium', 'large' )
	);
        $data['view-templates'] = array( '__key' => 'view-template' );
        foreach ( $view_templates as $key => $post ) {
            $post = (array) $post;
            if ($post['post_name']) {
                $post_data = array();
                $copy_data = array( 'ID', 'post_content', 'post_title', 'post_name', 'post_excerpt', 'post_type', 'post_status' );
                foreach ( $copy_data as $copy ) {
                    if ( isset( $post[$copy] ) ) {
                        $post_data[$copy] = $post[$copy];
                    }
                }
                $output_mode = get_post_meta( $post['ID'], '_wpv_view_template_mode', true );
				$template_extra_css = get_post_meta( $post['ID'], '_wpv_view_template_extra_css', true );
				$template_extra_js = get_post_meta( $post['ID'], '_wpv_view_template_extra_js', true );
				$template_description = get_post_meta( $post['ID'], '_wpv-content-template-decription', true );
				$post_data['template_mode'] = $output_mode;
				$post_data['template_extra_css'] = $template_extra_css;
				$post_data['template_extra_js'] = $template_extra_js;
				$post_data['template_description'] = $template_description;
                // Juan - add images for exporting
				// https://icanlocalize.basecamphq.com/projects/7393061-wp-views/todo_items/150919286/comments
                $att_args = array( 'post_type' => 'attachment', 'numberposts' => -1, 'post_status' => null, 'post_parent' => $post['ID'] );
				$attachments = get_posts( $att_args );
				if ( $attachments ) {
					$post_data['attachments'] = array();
					foreach ( $attachments as $attachment ) {
						preg_match( '/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $attachment->guid, $matches );
						if ( isset( $matches ) && is_array( $matches ) && isset( $matches[0] ) ) {
							$post_data['attachments']['attach_'.$attachment->ID] = array();
							$post_data['attachments']['attach_'.$attachment->ID]['title'] = $attachment->post_title;
							$post_data['attachments']['attach_'.$attachment->ID]['content'] = $attachment->post_content;
							$post_data['attachments']['attach_'.$attachment->ID]['excerpt'] = $attachment->post_excerpt;
							$post_data['attachments']['attach_'.$attachment->ID]['status'] = $attachment->post_status;
							$post_data['attachments']['attach_'.$attachment->ID]['alt'] = get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true );
							$imdata = base64_encode( file_get_contents( $attachment->guid ) );
							$post_data['attachments']['attach_'.$attachment->ID]['data'] = $imdata;
							$post_data['attachments']['attach_'.$attachment->ID]['filename'] = basename( $matches[0] );
							$imthumbs = array();
							foreach ( $attached_images_sizes as $ts ) {
								$imthumbs[$ts] = wp_get_attachment_image_src( $attachment->ID, $ts );
							}
							if ( isset( $template_extra_css ) ) {
								$pos = strpos( $template_extra_css, $attachment->guid );
								if ( $pos !== false ) {
									$post_data['attachments']['attach_'.$attachment->ID]['on_meta_html_css'] = $attachment->guid;
								}
								foreach ( $imthumbs as $thumbsize => $thumbdata ) {
									if ( !empty( $thumbdata ) && isset( $thumbdata[0] ) ) {
										$pos = strpos( $template_extra_css, $thumbdata[0] );
										if ( $pos !== false ) {
											$post_data['attachments']['attach_'.$attachment->ID]['on_meta_html_css_sizes'][$thumbsize] = $thumbdata[0];
										}
									}
								}
							}
							if ( isset( $template_extra_js ) ) {
								$posjs = strpos( $template_extra_js, $attachment->guid );
								if ( $posjs !== false ) {
									$post_data['attachments']['attach_'.$attachment->ID]['on_meta_html_js'] = $attachment->guid;
								}
								foreach ( $imthumbs as $thumbsize => $thumbdata ) {
									if ( !empty( $thumbdata ) && isset( $thumbdata[0] ) ) {
										$pos = strpos( $template_extra_js, $thumbdata[0] );
										if ( $pos !== false ) {
											$post_data['attachments']['attach_'.$attachment->ID]['on_meta_html_js_sizes'][$thumbsize] = $thumbdata[0];
										}
									}
								}
							}
							$poscont = strpos( $post_data['post_content'], $attachment->guid );
							if ( $poscont !== false ) {
								$post_data['attachments']['attach_'.$attachment->ID]['on_post_content'] = $attachment->guid;
							}
							foreach ( $imthumbs as $thumbsize => $thumbdata ) {
								if ( !empty( $thumbdata ) && isset( $thumbdata[0] ) ) {
									$pos = strpos( $post_data['post_content'], $thumbdata[0] );
									if ( $pos !== false ) {
										$post_data['attachments']['attach_'.$attachment->ID]['on_post_content_sizes'][$thumbsize] = $thumbdata[0];
									}
								}
							}
						}
					}
				}
                $data['view-templates']['view-template-' . $post['ID']] = $post_data;
            }
        }
    }

    // Get settings
    $options = get_option( 'wpv_options' );
    if ( !empty( $options ) ) {
        foreach ( $options as $option_name => $option_value ) {
            if ( strpos($option_name, 'view_' ) === 0 || strpos( $option_name, 'views_template_' ) === 0 ) {
                $post = get_post( $option_value );
                if ( !empty( $post ) ) {
                    $options[$option_name] = $post->post_name;
                }
            }
            // Custom inner shortcodes are stored in an indexed array, we need to make it associative
            if ( $option_name == 'wpv_custom_inner_shortcodes' && is_array( $option_value ) ) {
				$cis_option_value = array();
				foreach ( $option_value as $inner_shortcode ) {
					$cis_option_value[$inner_shortcode] = $inner_shortcode;
				}
				$options[$option_name] = $cis_option_value;
            }
        }
        $data['settings'] = $options;
    }

    // Offer for download
    $data = $xml->array2xml($data, 'views');

    $sitename = sanitize_key( get_bloginfo( 'name' ) );
    if ( !empty( $sitename ) ) {
        $sitename .= '.';
    }
    $filename = $sitename . 'views.' . date( 'Y-m-d' ) . '.xml';
    $code = "<?php\r\n";
    $code .= '$timestamp = ' . time() . ';' . "\r\n";
    $code .= '$auto_import = ';
    $code .=  ( isset( $_POST['import-mode'] ) && $_POST['import-mode'] == 'ask' ) ? 0 : 1;
    $code .= ';' . "\r\n";
    if ( isset($_POST['aid'] ) && $_POST['aid'] != '' && isset( $_POST['akey'] ) && $_POST['akey'] != '' ) {
        $code .= '$affiliate_id="' . $_POST['aid'] . '";' . "\r\n";
        $code .= '$affiliate_key="' . $_POST['akey'] . '";' . "\r\n";
    }
    $code .= "\r\n?>";

    if ( !$download ) {
        return $data;
    }
    if ( class_exists( 'ZipArchive' ) ) {
        $zipname = $sitename . 'views.' . date( 'Y-m-d' ) . '.zip';
        $zip = new ZipArchive();
        $file = tempnam( sys_get_temp_dir(), "zip" );
        $zip->open( $file, ZipArchive::OVERWRITE );
        $res = $zip->addFromString( 'settings.xml', $data );
        $zip->addFromString( 'settings.php', $code );
        $zip->close();
        $data = file_get_contents( $file );
        header( "Content-Description: File Transfer" );
        header( "Content-Disposition: attachment; filename=" . $zipname );
        header( "Content-Type: application/zip" );
        header( "Content-length: " . strlen($data) . "\n\n" );
        header( "Content-Transfer-Encoding: binary" );
        echo $data;
        unlink( $file );
        die();
    } else {
        // download the xml.
        header( "Content-Description: File Transfer" );
        header( "Content-Disposition: attachment; filename=" . $filename );
        header( "Content-Type: application/xml" );
        header( "Content-length: " . strlen( $data ) . "\n\n" );
        echo $data;
        die();
    }
}