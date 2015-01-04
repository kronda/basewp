<?php

/**
* Module Manager integration
*
* @since 1.2.0
*
* @moved 1.5.0 to its own file
*/

// Add Module Manager constants
define('_VIEWS_MODULE_MANAGER_KEY_','views');
define('_VIEW_TEMPLATES_MODULE_MANAGER_KEY_','view-templates');

/**
* wpv_module_manager_init
*
* Initialize all the components hooked into Module Manager
*/

add_action( 'init', 'wpv_module_manager_init' );

function wpv_module_manager_init() {
	
	if ( defined( 'MODMAN_PLUGIN_NAME' ) ) {
	
		// Register sections in Module Manager
		add_filter( 'wpmodules_register_sections', 'wpv_register_modules_sections', 20, 1 );
		
		// Views register, export, import and check
		add_filter( 'wpmodules_export_items_'._VIEWS_MODULE_MANAGER_KEY_, 'export_modules_views_items', 10, 2 );
		add_filter( 'wpmodules_import_items_'._VIEWS_MODULE_MANAGER_KEY_, 'import_modules_views_items', 10, 3 );
		add_filter( 'wpmodules_items_check_'._VIEWS_MODULE_MANAGER_KEY_, 'check_modules_views_items', 10, 1 );
		
		// Content Template register, export, import and check
		add_filter( 'wpmodules_export_items_'._VIEW_TEMPLATES_MODULE_MANAGER_KEY_, 'export_modules_view_templates_items', 10, 2 );
		add_filter( 'wpmodules_import_items_'._VIEW_TEMPLATES_MODULE_MANAGER_KEY_, 'import_modules_view_templates_items', 10, 3 );
		add_filter( 'wpmodules_items_check_'._VIEW_TEMPLATES_MODULE_MANAGER_KEY_, 'check_modules_view_templates_items', 10, 1 );
		
		// Hook for adding plugin version
		/*Export*/
		add_filter( 'wpmodules_export_pluginversions_'._VIEWS_MODULE_MANAGER_KEY_, 'wpv_modules_views_pluginversion_used' );
		add_filter( 'wpmodules_export_pluginversions_'._VIEW_TEMPLATES_MODULE_MANAGER_KEY_, 'wpv_modules_views_pluginversion_used' );

		/*Import*/
		add_filter( 'wpmodules_import_pluginversions_'._VIEWS_MODULE_MANAGER_KEY_, 'wpv_modules_views_pluginversion_used' );
		add_filter( 'wpmodules_import_pluginversions_'._VIEW_TEMPLATES_MODULE_MANAGER_KEY_, 'wpv_modules_views_pluginversion_used' );
		
	}
	
}

// Register sections in Module Manager

function wpv_register_modules_sections( $sections ) {
	$sections[_VIEW_TEMPLATES_MODULE_MANAGER_KEY_] = array(
		'title' => __( 'Content Templates','wpv-views' ),
		'icon' => WPV_URL . '/res/img/icon12.png'
	);

	$sections[_VIEWS_MODULE_MANAGER_KEY_] = array(
		'title' => __( 'Views','wpv-views' ),
		'icon' => WPV_URL . '/res/img/icon12.png'
	);
	return $sections;
}

// Views register, export, import and check

function export_modules_views_items( $res, $items ) {
	$newitems = array();
	// items is whole array, not just IDs
	foreach ( $items as $ii=>$item ) {
		$newitems[$ii] = str_replace( _VIEWS_MODULE_MANAGER_KEY_, '', $item['id'] );
	}
	$export_data_pre = wpv_admin_export_selected_data( $newitems, 'view', 'module_manager' );
	$hashes = $export_data_pre['items_hash'];
	foreach ( $items as $jj =>$item ) {
		$id = str_replace( _VIEWS_MODULE_MANAGER_KEY_, '', $item['id'] );
		$items[$jj]['hash'] = $hashes[$id];
	}
	return array(
		'xml' => $export_data_pre['xml'],
		'items' => $items
	);
}

function import_modules_views_items( $result, $xmlstring, $items ) {
	$result = wpv_admin_import_data_from_xmlstring( $xmlstring, $items, 'views' );
	if ( false === $result || is_wp_error( $result ) ) {
		return ( false === $result ) ? __( 'Error during View import','wpv-views' ) : $result->get_error_message( $result->get_error_code() );
	}
	return $result;
}

function check_modules_views_items( $items ) {
	foreach ( $items as $key=>$item ) {
		$view_exists = get_page_by_title( $item['title'], OBJECT, 'view' );
		if ( $view_exists ) {
			$items[$key]['exists'] = true;
			$new_item_export = wpv_admin_export_selected_data( array($view_exists->ID), 'view', 'module_manager' );
			$new_item_hash = $new_item_export['items_hash'][$view_exists->ID];
			if ( $new_item_hash != $items[$key]['hash'] ) {
				$items[$key]['is_different'] = true;
				$items[$key]['new_hash'] = $new_item_hash;
				$items[$key]['old_hash'] = $items[$key]['hash'];
			} else {
				$items[$key]['is_different'] = false;
				$items[$key]['new_hash'] = $new_item_hash;
				$items[$key]['old_hash'] = $items[$key]['hash'];
			}
		} else {
			$items[$key]['exists'] = false;
		}
	}
	return $items;
}

// Content Templates register, export, import and check

function export_modules_view_templates_items( $res, $items ) {
	$newitems = array();
	// items is whole array, not just IDs
	foreach ( $items as $ii=>$item ) {
		$newitems[$ii]=str_replace( _VIEW_TEMPLATES_MODULE_MANAGER_KEY_, '', $item['id'] );
	}
	$export_data_pre = wpv_admin_export_selected_data( $newitems, 'view-template', 'module_manager' );
	$hashes = $export_data_pre['items_hash'];
	foreach ( $items as $jj =>$item ) {
		$id = str_replace( _VIEW_TEMPLATES_MODULE_MANAGER_KEY_, '', $item['id'] );
		$items[$jj]['hash'] = $hashes[$id];
	}
	return array(
		'xml' => $export_data_pre['xml'],
		'items' => $items
	);
}

function import_modules_view_templates_items( $result, $xmlstring, $items ) {
	$result = wpv_admin_import_data_from_xmlstring( $xmlstring, $items, 'view-templates' );
	if ( false === $result || is_wp_error( $result ) ) {
		return ( false === $result ) ? __( 'Error during Content Template import', 'wpv-views' ) : $result->get_error_message( $result->get_error_code() );
	}
	return $result;
}

function check_modules_view_templates_items( $items ) {
	foreach ( $items as $key => $item ) {
		$view_template_exists = get_page_by_title( $item['title'], OBJECT, 'view-template' );
		if ( $view_template_exists ) {
			$items[$key]['exists'] = true;
			$new_item_export = wpv_admin_export_selected_data( array($view_template_exists->ID), 'view-template', 'module_manager' );
			$new_item_hash = $new_item_export['items_hash'][$view_template_exists->ID];
			if ( $new_item_hash != $items[$key]['hash'] ) {
				$items[$key]['is_different'] = true;
				$items[$key]['new_hash'] = $new_item_hash;
				$items[$key]['old_hash'] = $items[$key]['hash'];
			} else {
				$items[$key]['is_different'] = false;
			}
		} else {
			$items[$key]['exists'] = false;
		}
	}
	return $items;
}

// Return the WPV_VERSION

function wpv_modules_views_pluginversion_used() {
	if (defined( 'WPV_VERSION' )) {
		return WPV_VERSION;
	}
}

/**
* wpv_admin_export_selected_data
*
* Custom Export function for Module Manager - Exports selected items (by ID) and of specified type (eg views, view-templates)
*
* Note: whatever chage done here must be done too in wpv_admin_export_data()
*
* @param $items 'all' returns all items | array() to be used in post__in argument
* @param $type 'view' | 'view-template'
* @param $mode 'xml' returns a string to be converted to XML | 'module_manager' returns an array() compatible with Module Manager export
*
* @return mixed xml-string or array()
+
* @since 1.2.0
*/
function wpv_admin_export_selected_data( $items, $type = 'view', $mode = 'xml' ) {
    global $WP_Views;

    require_once WPV_PATH_EMBEDDED . '/common/array2xml.php';
    $xml = new ICL_Array2XML();
    $data = array();
    $items_hash = array();
    $export = false; // flag

    // SRDJAN - add siteurl, upload url, record taxonomies old IDs
    // https://icanlocalize.basecamphq.com/projects/7393061-wp-views/todo_items/142382866/comments
    // https://icanlocalize.basecamphq.com/projects/7393061-wp-views/todo_items/142389966/comments
//    $data['site_url'] = get_site_url();
	// TODO this might not be needed, it's not used here
    if (is_multisite()) {
        $upload_directory = get_option('fileupload_url');
    } else {
        $wp_upload_dir = wp_upload_dir();
        $upload_directory = $wp_upload_dir['baseurl'];
    }

    // Basic arguments for the query
    $args=array(
        'posts_per_page' => -1,
        'post_status' => 'any'
    );

    // Adjust names
    $view_types=array(
        'view' => array( 'key' => 'views' ),
        'view-template' => array( 'key' => 'view-templates' )
    );

    // Set what to export and the post__in query arg if needed
    if ( is_string( $items ) && 'all' === $items ) {
        $export = true;
    } elseif ( is_array( $items ) && !empty( $items ) ) {
        $args['post__in'] = $items;
        $export = true;
    }

    // Check we are exporting the right type and then set the post_type query arg
    if ( !in_array( $type, array_keys( $view_types ) ) ) {
		$export = false;
	} else {
        $args['post_type'] = $type;
        $vkey = $view_types[$type]['key'];
    }
	
	// Return if we won't export
    if ( !$export ) {
		return '';
	}

	// Start collecting data to export
    switch( $type ) {
		
		// If exporting Views
		case 'view':
			// Get the views
			$views = get_posts($args);
			if ( !empty( $views ) ) {
				// Compose the image size array
				global $_wp_additional_image_sizes;
				if (!isset($_wp_additional_image_sizes) || !is_array($_wp_additional_image_sizes)) {
					$_wp_additional_image_sizes = array();
				}
				$attached_images_sizes=array_merge(
					// additional thumbnail sizes
					array_keys($_wp_additional_image_sizes),
					// wp default thumbnail sizes
					array('thumbnail', 'medium', 'large')
				);
				// Create the data['views'] array
				$data['views'] = array('__key' => 'view');
				// Loop through the returned Views to take their data
				foreach ( $views as $key => $post ) {
					$post = (array) $post;
					// Only export items with post_name
					if ($post['post_name']) {
						$hash_data = array();
						$post_data = array();
						// Basic data
						$copy_data = array('ID', 'post_content', 'post_title', 'post_name', 'post_excerpt', 'post_type', 'post_status');
						foreach ( $copy_data as $copy ) {
							if ( isset( $post[$copy] ) ) {
								$post_data[$copy] = $post[$copy];
							}
						}
						$data['views']['view-' . $post['ID']] = $post_data;
						$hash_basics = array('post_title', 'post_name', 'post_type', 'post_status');
						foreach ($hash_basics as $basics) {
							if ( isset( $data['views']['view-' . $post['ID']][$basics] ) ) $hash_data[$basics] = $data['views']['view-' . $post['ID']][$basics];
						}
						if (isset($data['views']['view-' . $post['ID']]['post_content'])) $hash_data['post_content'] = preg_replace('/\s+/', '', str_replace("\n","",$data['views']['view-' . $post['ID']]['post_content']));
						if (isset($data['views']['view-' . $post['ID']]['post_excerpt'])) $hash_data['post_excerpt'] = preg_replace('/\s+/', '', str_replace("\n","",$data['views']['view-' . $post['ID']]['post_excerpt']));
						// Meta data
						$meta = get_post_custom( $post['ID'] );
						if ( !empty( $meta ) ) {
							$data['view']['view-' . $post['ID']]['meta'] = array();
							foreach ( $meta as $meta_key => $meta_value ) {
								// View settings
								if ( $meta_key == '_wpv_settings' ) {
									$value = maybe_unserialize($meta_value[0]);
									// Add any taxonomy terms so we can re-map when we import.
									if ( !empty( $value['taxonomy_terms'] ) ) {
										$taxonomy = $value['taxonomy_type'][0];
										foreach ( $value['taxonomy_terms'] as $term_id ) {
											$term = get_term( $term_id, $taxonomy );
											if ( isset( $term ) && !is_wp_error( $term ) ) {
												$data['terms_map']['term_' . $term->term_id]['old_id'] = $term->term_id;
												$data['terms_map']['term_' . $term->term_id]['slug'] = $term->slug;
												$data['terms_map']['term_' . $term->term_id]['taxonomy'] = $taxonomy;
											}
										}
									}
									// Adjust some settings with 0 as keys
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
									// Convert other IDs to names in View settings
									$value = $WP_Views->convert_ids_to_names_in_settings( $value );
									// Convert IDs to names in the filter by ID if needed
									if (isset($value['post_id_ids_list']) && !empty($value['post_id_ids_list'])) {
										$value['post_id_ids_list'] = $WP_Views->convert_ids_to_names_in_filters( $value['post_id_ids_list'] );
									}
									// Add the data to export
									$data['views']['view-' . $post['ID']]['meta'][$meta_key] = $value;
									// Add the hash for Module Manager if needed
									if ( 'module_manager' == $mode ) {
										$hash_data['meta'][$meta_key] = $value;
										// Correct possible elements with changing format
										if ( isset( $value['taxonomy_hide_empty'] ) ) $hash_data['meta'][$meta_key]['taxonomy_hide_empty'] = strval( $value['taxonomy_hide_empty'] );
										if ( isset( $value['taxonomy_include_non_empty_decendants'] ) ) $hash_data['meta'][$meta_key]['taxonomy_include_non_empty_decendants'] = strval( $value['taxonomy_include_non_empty_decendants'] );
										if ( isset( $value['taxonomy_pad_counts'] ) ) $hash_data['meta'][$meta_key]['taxonomy_pad_counts'] = strval( $value['taxonomy_pad_counts'] );
										if ( isset( $value['post_type_dont_include_current_page'] ) ) $hash_data['meta'][$meta_key]['post_type_dont_include_current_page'] = strval( $value['post_type_dont_include_current_page'] );
										if ( isset( $value['pagination']['preload_images'] ) ) $hash_data['meta'][$meta_key]['pagination']['preload_images'] = strval( $value['pagination']['preload_images'] );
										if ( isset( $value['pagination']['cache_pages'] ) ) $hash_data['meta'][$meta_key]['pagination']['cache_pages'] = strval( $value['pagination']['cache_pages'] );
										if ( isset( $value['pagination']['preload_pages'] ) ) $hash_data['meta'][$meta_key]['pagination']['preload_pages'] = strval( $value['pagination']['preload_pages'] );
										if ( isset( $value['pagination']['spinner_image'] ) ) $hash_data['meta'][$meta_key]['pagination']['spinner_image'] = basename( $value['pagination']['spinner_image'] );
										if ( isset( $value['rollover']['preload_images'] ) ) $hash_data['meta'][$meta_key]['rollover']['preload_images'] = strval( $value['rollover']['preload_images'] );
										if ( isset( $value['offset'] ) ) $hash_data['meta'][$meta_key]['offset'] = strval( $value['offset'] );
										if ( isset( $value['taxonomy_offset'] ) ) $hash_data['meta'][$meta_key]['taxonomy_offset'] = strval( $value['taxonomy_offset'] );
										if ( isset( $value['filter_meta_html'] ) ) $hash_data['meta'][$meta_key]['filter_meta_html'] = preg_replace( '/\s+/', '', str_replace( "\n","",$value['filter_meta_html'] ) );
										if ( isset( $value['generated_filter_meta_html'] ) ) $hash_data['meta'][$meta_key]['generated_filter_meta_html'] = preg_replace( '/\s+/', '', str_replace( "\n","",$value['generated_filter_meta_html'] ) );
										if ( isset( $value['filter_meta_html_css'] ) ) $hash_data['meta'][$meta_key]['filter_meta_html_css'] = preg_replace( '/\s+/', '', str_replace( "\n","",$value['filter_meta_html_css'] ) );
										if ( isset( $value['filter_meta_html_js'] ) ) $hash_data['meta'][$meta_key]['filter_meta_html_js'] = preg_replace( '/\s+/', '', str_replace( "\n","",$value['filter_meta_html_js'] ) );
										if ( isset( $value['layout_meta_html_css'] ) ) $hash_data['meta'][$meta_key]['layout_meta_html_css'] = preg_replace( '/\s+/', '', str_replace( "\n","",$value['layout_meta_html_css'] ) );
										if ( isset( $value['layout_meta_html_js'] ) ) $hash_data['meta'][$meta_key]['layout_meta_html_js'] = preg_replace( '/\s+/', '', str_replace( "\n","",$value['layout_meta_html_js'] ) );
										if ( isset( $value['author_mode'] ) ) $hash_data['meta'][$meta_key]['author_mode'] = reset( $value['author_mode'] );
										if ( isset( $value['taxonomy_parent_mode'] ) ) $hash_data['meta'][$meta_key]['taxonomy_parent_mode'] = reset( $value['taxonomy_parent_mode'] );
										if ( isset( $value['taxonomy_search_mode'] ) ) $hash_data['meta'][$meta_key]['taxonomy_search_mode'] = reset( $value['taxonomy_search_mode'] );
										if ( isset( $value['search_mode'] ) ) $hash_data['meta'][$meta_key]['search_mode'] = reset( $value['search_mode'] );
										if ( isset( $value['id_mode'] ) ) $hash_data['meta'][$meta_key]['id_mode'] = reset( $value['id_mode'] );
										$cursed_array = array(
											'filter_controls_enable',
											'filter_controls_param',
											'filter_controls_mode',
											'filter_controls_field_name',
											'filter_controls_label',
											'filter_controls_type',
											'filter_controls_values'
										);
										foreach ( $cursed_array as $cursed ) {
											if ( isset( $hash_data['meta'][$meta_key][$cursed] ) ) {
												unset( $hash_data['meta'][$meta_key][$cursed] );
											}
										}
									}
								}
								// View layout settings
								if ( $meta_key == '_wpv_layout_settings' ) {
									$value = maybe_unserialize( $meta_value[0] );
									// Convert IDs to names in View layout settings
									$value = $WP_Views->convert_ids_to_names_in_layout_settings( $value );
									// Add the data to export
									$data['views']['view-' . $post['ID']]['meta'][$meta_key] = $value;
									// Add the hash for Module Manager if needed
									if ( 'module_manager' == $mode ) {
										$hash_data['meta'][$meta_key] = $value;
										if ( isset( $value['layout_meta_html'] ) ) $hash_data['meta'][$meta_key]['layout_meta_html'] = preg_replace( '/\s+/', '', str_replace( "\n", "", $value['layout_meta_html'] ) );
										if ( isset( $value['generated_layout_meta_html'] ) ) $hash_data['meta'][$meta_key]['generated_layout_meta_html'] = preg_replace( '/\s+/', '', str_replace( "\n", "", $value['generated_layout_meta_html'] ) );
									}
								}
								// View description
								if ( $meta_key == '_wpv_description' ) {
									$value = maybe_unserialize( $meta_value[0] );
									$data['views']['view-' . $post['ID']]['meta'][$meta_key] = $value;
								}
							}
							// If there is no settings, layout settings or description meta, unset the key
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
							if ('module_manager' == $mode ) $hash_data['attachments'] = array();
							foreach ( $attachments as $attachment ) {
								// Add the attachment to the exported data
								$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID] = array();
								$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['title'] = $attachment->post_title;
								$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['content'] = $attachment->post_content;
								$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['excerpt'] = $attachment->post_excerpt;
								$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['status'] = $attachment->post_status;
								$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['alt'] = get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true );
								$imdata = base64_encode(file_get_contents($attachment->guid));
								if ('module_manager' == $mode ) $hash_data['attachments'][] = md5($imdata);
								$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['data'] = $imdata;
								preg_match( '/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $attachment->guid, $matches );
								$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['filename'] = basename( $matches[0] );
								$this_settings = get_post_meta($post['ID'], '_wpv_settings', true);
								$this_layout_settings = get_post_meta($post['ID'], '_wpv_layout_settings', true);
								if ( isset( $this_settings['pagination']['spinner_image_uploaded'] ) && $attachment->guid == $this_settings['pagination']['spinner_image_uploaded'] ) {
									$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['custom_spinner'] = 'this';
									if ( 'module_manager' == $mode ) {
										$hash_data['meta']['_wpv_settings']['pagination']['spinner_image_uploaded'] = md5($imdata);
									}
								}
								// Get the src for every attachment size
								$imthumbs = array();
								foreach ($attached_images_sizes as $ts) {
									$imthumbs[$ts] = wp_get_attachment_image_src( $attachment->ID, $ts );
								}
								// Adjust the Filter MetaHTML content
								if ( isset( $this_settings['filter_meta_html'] ) ) {
									$pos = strpos( $this_settings['filter_meta_html'], $attachment->guid );
									if ($pos !== false) {
										$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['on_filter_meta_html'] = $attachment->guid;
										if ( 'module_manager' == $mode ) {
											$hash_data['meta']['_wpv_settings']['filter_meta_html'] = str_replace($attachment->guid, md5($imdata), $hash_data['meta']['_wpv_settings']['filter_meta_html']);
										}
									}
									foreach ($imthumbs as $thumbsize => $thumbdata) {
										if (!empty($thumbdata) && isset($thumbdata[0])) {
											$pos = strpos( $this_settings['filter_meta_html'], $thumbdata[0] );
											if ($pos !== false) {
												$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['on_filter_meta_html_sizes'][$thumbsize] = $thumbdata[0];
												if ( 'module_manager' == $mode ) {
													$hash_data['meta']['_wpv_settings']['filter_meta_html'] = str_replace($thumbdata[0], md5($imdata) . '_' . $thumbsize, $hash_data['meta']['_wpv_settings']['filter_meta_html']);
												}
											}
										}
									}
								}
								// Adjust the Filter MetaHTML CSS content
								if ( isset( $this_settings['filter_meta_html_css'] ) ) {
									$pos = strpos( $this_settings['filter_meta_html_css'], $attachment->guid );
									if ($pos !== false) {
										$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['on_filter_meta_html_css'] = $attachment->guid;
										if ( 'module_manager' == $mode ) {
											$hash_data['meta']['_wpv_settings']['filter_meta_html_css'] = str_replace($attachment->guid, md5($imdata), $hash_data['meta']['_wpv_settings']['filter_meta_html_css']);
										}
									}
									foreach ($imthumbs as $thumbsize => $thumbdata) {
										if (!empty($thumbdata) && isset($thumbdata[0])) {
											$pos = strpos( $this_settings['filter_meta_html_css'], $thumbdata[0] );
											if ($pos !== false) {
												$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['on_filter_meta_html_css_sizes'][$thumbsize] = $thumbdata[0];
												if ( 'module_manager' == $mode ) {
													$hash_data['meta']['_wpv_settings']['filter_meta_html_css'] = str_replace($thumbdata[0], md5($imdata) . '_' . $thumbsize, $hash_data['meta']['_wpv_settings']['filter_meta_html_css']);
												}
											}
										}
									}
								}
								// Adjust the Filter MetaHTML JS content
								if ( isset( $this_settings['filter_meta_html_js'] ) ) {
									$pos = strpos( $this_settings['filter_meta_html_js'], $attachment->guid );
									if ($pos !== false) {
										$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['on_filter_meta_html_js'] = $attachment->guid;
										if ( 'module_manager' == $mode ) {
											$hash_data['meta']['_wpv_settings']['filter_meta_html_js'] = str_replace($attachment->guid, md5($imdata), $hash_data['meta']['_wpv_settings']['filter_meta_html_js']);
										}
									}
									foreach ($imthumbs as $thumbsize => $thumbdata) {
										if (!empty($thumbdata) && isset($thumbdata[0])) {
											$pos = strpos( $this_settings['filter_meta_html_js'], $thumbdata[0] );
											if ($pos !== false) {
												$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['on_filter_meta_html_js_sizes'][$thumbsize] = $thumbdata[0];
												if ( 'module_manager' == $mode ) {
													$hash_data['meta']['_wpv_settings']['filter_meta_html_js'] = str_replace($thumbdata[0], md5($imdata) . '_' . $thumbsize, $hash_data['meta']['_wpv_settings']['filter_meta_html_js']);
												}
											}
										}
									}
								}
								// Adjust the Layout MetaHTML content
								if ( isset( $this_layout_settings['layout_meta_html'] ) ) {
									$pos = strpos( $this_layout_settings['layout_meta_html'], $attachment->guid );
									if ($pos !== false) {
										$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['on_layout_meta_html'] = $attachment->guid;
										if ( 'module_manager' == $mode ) {
											$hash_data['meta']['_wpv_layout_settings']['layout_meta_html'] = str_replace($attachment->guid, md5($imdata), $hash_data['meta']['_wpv_layout_settings']['layout_meta_html']);
										}
									}
									foreach ($imthumbs as $thumbsize => $thumbdata) {
										if (!empty($thumbdata) && isset($thumbdata[0])) {
											$pos = strpos( $this_layout_settings['layout_meta_html'], $thumbdata[0] );
											if ($pos !== false) {
												$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['on_layout_meta_html_sizes'][$thumbsize] = $thumbdata[0];
												if ( 'module_manager' == $mode ) {
													$hash_data['meta']['_wpv_layout_settings']['layout_meta_html'] = str_replace($thumbdata[0], md5($imdata) . '_' . $thumbsize, $hash_data['meta']['_wpv_layout_settings']['layout_meta_html']);
												}
											}
										}
									}
								}
								// Adjust the Layout MetaHTML CSS content
								if ( isset( $this_settings['layout_meta_html_css'] ) ) {
									$pos = strpos( $this_settings['layout_meta_html_css'], $attachment->guid );
									if ($pos !== false) {
										$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['on_layout_meta_html_css'] = $attachment->guid;
										if ( 'module_manager' == $mode ) {
											$hash_data['meta']['_wpv_settings']['layout_meta_html_css'] = str_replace($attachment->guid, md5($imdata), $hash_data['meta']['_wpv_settings']['layout_meta_html_css']);
										}
									}
									foreach ($imthumbs as $thumbsize => $thumbdata) {
										if (!empty($thumbdata) && isset($thumbdata[0])) {
											$pos = strpos( $this_settings['layout_meta_html_css'], $thumbdata[0] );
											if ($pos !== false) {
												$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['on_layout_meta_html_css_sizes'][$thumbsize] = $thumbdata[0];
												if ( 'module_manager' == $mode ) {
													$hash_data['meta']['_wpv_settings']['layout_meta_html_css'] = str_replace($thumbdata[0], md5($imdata) . '_' . $thumbsize, $hash_data['meta']['_wpv_settings']['layout_meta_html_css']);
												}
											}
										}
									}
								}
								// Adjust the Layout MetaHTML JS content
								if ( isset( $this_settings['layout_meta_html_js'] ) ) {
									$pos = strpos( $this_settings['layout_meta_html_js'], $attachment->guid );
									if ($pos !== false) {
										$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['on_layout_meta_html_js'] = $attachment->guid;
										if ( 'module_manager' == $mode ) {
											$hash_data['meta']['_wpv_settings']['layout_meta_html_js'] = str_replace($attachment->guid, md5($imdata), $hash_data['meta']['_wpv_settings']['layout_meta_html_js']);
										}
									}
									foreach ($imthumbs as $thumbsize => $thumbdata) {
										if (!empty($thumbdata) && isset($thumbdata[0])) {
											$pos = strpos( $this_settings['layout_meta_html_js'], $thumbdata[0] );
											if ($pos !== false) {
												$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['on_layout_meta_html_js_sizes'][$thumbsize] = $thumbdata[0];
												if ( 'module_manager' == $mode ) {
													$hash_data['meta']['_wpv_settings']['layout_meta_html_js'] = str_replace($thumbdata[0], md5($imdata) . '_' . $thumbsize, $hash_data['meta']['_wpv_settings']['layout_meta_html_js']);
												}
											}
										}
									}
								}
								// Adjust the full content
								$poscont = strpos( $data['views']['view-' . $post['ID']]['post_content'], $attachment->guid );
								if ($poscont !== false) {
									$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['on_post_content'] = $attachment->guid;
									if ( 'module_manager' == $mode ) {
										$hash_data['post_content'] = str_replace($attachment->guid, md5($imdata), $hash_data['post_content']);
									}
								}
								foreach ($imthumbs as $thumbsize => $thumbdata) {
									if (!empty($thumbdata) && isset($thumbdata[0])) {
										$pos = strpos( $data['views']['view-' . $post['ID']]['post_content'], $thumbdata[0] );
										if ($pos !== false) {
											$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['on_post_content_sizes'][$thumbsize] = $thumbdata[0];
											if ( 'module_manager' == $mode ) {
												$hash_data['post_content'] = str_replace($thumbdata[0], md5($imdata) . '_' . $thumbsize, $hash_data['post_content']);
											}
										}
									}
								}
							}
						}
						if ( 'module_manager' == $mode ) {
							//Emerson: Fix issues in inconsistent hash in MM 1.1
							//Added recursive sorting of keys prior to hashing to provide a consistent hash result during the import.
							//Remove some keys for consistency.
							if ( isset( $hash_data['meta']['_wpv_settings']['pagination']['mode'] ) ) {
								unset( $hash_data['meta']['_wpv_settings']['pagination']['mode'] );
							}
							if ( ( isset( $hash_data['meta']['_wpv_settings']['post_category'] ) ) && ( !( empty( $hash_data['meta']['_wpv_settings']['post_category'] ) ) ) ) {
								foreach ( $hash_data['meta']['_wpv_settings']['post_category'] as $post_category_hashing_key => $post_category_hashing_value ) {
									if ( $post_category_hashing_key != '__key' ) {
										unset( $hash_data['meta']['_wpv_settings']['post_category'][$post_category_hashing_key] );
									}
								}
							}
							$items_hash[$post['ID']] = md5( serialize( wpv_ksort_by_string_views( $hash_data ) ) );
						}
					}
				}
			}
			break;
		// If exporting Content Templates
		case 'view-template':
			// Get the Content templates
			$view_templates = get_posts($args);
			if (!empty($view_templates)) {
				// Compose the image size array
				global $_wp_additional_image_sizes;
				if ( !isset( $_wp_additional_image_sizes) || !is_array( $_wp_additional_image_sizes ) ) {
					$_wp_additional_image_sizes = array();
				}
				$attached_images_sizes = array_merge(
					// additional thumbnail sizes
					array_keys( $_wp_additional_image_sizes ),
					// wp default thumbnail sizes
					array( 'thumbnail', 'medium', 'large' )
				);
				// Create the $data['view-template'] array
				$data['view-templates'] = array( '__key' => 'view-template' );
				// Start collecting data
				foreach ( $view_templates as $key => $post ) {
					$post = (array) $post;
					// Only add Content Templates with a post_name
					if ( $post['post_name'] ) {
						$post_data = array();
						// Basic data
						$copy_data = array( 'ID', 'post_content', 'post_title', 'post_name', 'post_excerpt', 'post_type', 'post_status' );
						foreach ( $copy_data as $copy ) {
							if ( isset( $post[$copy] ) ) {
								$post_data[$copy] = $post[$copy];
							}
						}
						// Content Template meta data
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
								$post_data['attachments']['attach_'.$attachment->ID] = array();
								$post_data['attachments']['attach_'.$attachment->ID]['title'] = $attachment->post_title;
								$post_data['attachments']['attach_'.$attachment->ID]['content'] = $attachment->post_content;
								$post_data['attachments']['attach_'.$attachment->ID]['excerpt'] = $attachment->post_excerpt;
								$post_data['attachments']['attach_'.$attachment->ID]['status'] = $attachment->post_status;
								$post_data['attachments']['attach_'.$attachment->ID]['alt'] = get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true );
								$imdata = base64_encode(file_get_contents($attachment->guid));
								$post_data['attachments']['attach_'.$attachment->ID]['data'] = $imdata;
								preg_match( '/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $attachment->guid, $matches );
								$post_data['attachments']['attach_'.$attachment->ID]['filename'] = basename( $matches[0] );
								$imthumbs = array();
								foreach ($attached_images_sizes as $ts) {
									$imthumbs[$ts] = wp_get_attachment_image_src( $attachment->ID, $ts );
								}
								// Adjust images in CSS
								if ( isset( $template_extra_css ) ) {
									$pos = strpos( $template_extra_css, $attachment->guid );
									if ($pos !== false) {
										$post_data['attachments']['attach_'.$attachment->ID]['on_meta_html_css'] = $attachment->guid;
									}
									foreach ($imthumbs as $thumbsize => $thumbdata) {
										if (!empty($thumbdata) && isset($thumbdata[0])) {
											$pos = strpos( $template_extra_css, $thumbdata[0] );
											if ($pos !== false) {
												$post_data['attachments']['attach_'.$attachment->ID]['on_meta_html_css_sizes'][$thumbsize] = $thumbdata[0];
											}
										}
									}
								}
								// Adjust images in JS
								if ( isset( $template_extra_js ) ) {
									$posjs = strpos( $template_extra_js, $attachment->guid );
									if ($posjs !== false) {
										$post_data['attachments']['attach_'.$attachment->ID]['on_meta_html_js'] = $attachment->guid;
									}
									foreach ($imthumbs as $thumbsize => $thumbdata) {
										if (!empty($thumbdata) && isset($thumbdata[0])) {
											$pos = strpos( $template_extra_js, $thumbdata[0] );
											if ($pos !== false) {
												$post_data['attachments']['attach_'.$attachment->ID]['on_meta_html_js_sizes'][$thumbsize] = $thumbdata[0];
											}
										}
									}
								}
								//Adjust images in content
								$poscont = strpos( $post_data['post_content'], $attachment->guid );
								if ($poscont !== false) {
									$post_data['attachments']['attach_'.$attachment->ID]['on_post_content'] = $attachment->guid;
								}
								foreach ($imthumbs as $thumbsize => $thumbdata) {
									if (!empty($thumbdata) && isset($thumbdata[0])) {
										$pos = strpos( $post_data['post_content'], $thumbdata[0] );
										if ($pos !== false) {
											$post_data['attachments']['attach_'.$attachment->ID]['on_post_content_sizes'][$thumbsize] = $thumbdata[0];
										}
									}
								}
							}
						}
						// Add the data to export
						$data['view-templates']['view-template-' . $post['ID']] = $post_data;
                        if ( 'module_manager' == $mode ) {
							$hash_data = $post_data;
							$hash_data['post_content'] = preg_replace( '/\s+/', '', str_replace( "\n", "", $post_data['post_content'] ) );
							$hash_data['template_extra_css'] = preg_replace( '/\s+/', '', str_replace( "\n", "", $post_data['template_extra_css'] ) );
							$hash_data['template_extra_js'] = preg_replace( '/\s+/', '', str_replace( "\n", "", $post_data['template_extra_js'] ) );
							if ( isset( $post_data['attachments'] ) ) {
								unset( $hash_data['attachments'] );
								$hash_data['attachments'] = array();
								foreach ( $post_data['attachments'] as $key => $attvalues ) {
									$hash_data['attachments'][] = md5($attvalues['data']);
									if ( isset( $attvalues['on_meta_html_css'] ) ) $hash_data['template_extra_css'] = str_replace( $attvalues['on_meta_html_css'], md5($attvalues['data']), $hash_data['template_extra_css'] );
									if ( isset( $attvalues['on_meta_html_css_sizes'] ) && is_array( $attvalues['on_meta_html_css_sizes'] ) ) {
										foreach ( $attvalues['on_meta_html_css_sizes'] as $tsize => $turl ) {
											$hash_data['template_extra_css'] = str_replace( $turl, md5($attvalues['data']) . '_' . $tsize, $hash_data['template_extra_css'] );
										}
									}
									if ( isset( $attvalues['on_meta_html_js'] ) ) $hash_data['template_extra_js'] = str_replace( $attvalues['on_meta_html_js'], $attvalues['data'], $hash_data['template_extra_js'] );
									if ( isset( $attvalues['on_meta_html_js_sizes'] ) && is_array( $attvalues['on_meta_html_js_sizes'] ) ) {
										foreach ( $attvalues['on_meta_html_js_sizes'] as $tsize => $turl ) {
											$hash_data['template_extra_js'] = str_replace( $turl, md5($attvalues['data']) . '_' . $tsize, $hash_data['template_extra_js'] );
										}
									}
									if ( isset( $attvalues['on_post_content'] ) ) {
										$hash_data['post_content'] = str_replace( $attvalues['on_post_content'], $attvalues['data'], $hash_data['post_content'] );
									}
									if ( isset( $attvalues['on_post_content_sizes'] ) && is_array( $attvalues['on_post_content_sizes'] ) ) {
										foreach ( $attvalues['on_post_content_sizes'] as $tsize => $turl ) {
											$hash_data['post_content'] = str_replace( $turl, md5($attvalues['data']) . '_' . $tsize, $hash_data['post_content'] );
										}
									}
								}
							}
							unset( $hash_data['ID'] );
							$items_hash[$post['ID']] = md5( serialize( $hash_data ) );
						}
					}
				}
			}
			break;
	}
	// Compose the XML string
	$xmldata = $xml->array2xml( $data, 'views' );
	if ( 'xml' == $mode ) {
		return $xmldata;
	} elseif ( 'module_manager' == $mode ) {
		$export_data = array(
			'xml' => $xmldata,
			'items_hash' => $items_hash // this is an array with format [itemID] => item_hash
		);
		return $export_data;
	}
}

function wpv_ksort_by_string_views( $data ) {
	if ( is_array( $data ) ) {
		ksort( $data, SORT_STRING );
		foreach ( $data as $k => $v ) {
			$data[$k] = wpv_ksort_by_string_views( $v );
		}
	}
	return $data;
}