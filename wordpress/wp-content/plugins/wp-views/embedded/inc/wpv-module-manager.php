<?php

/**
* Module Manager integration
*
* @since 1.2.0
*
* @moved 1.5.0 to its own file
*
* @todo transform this into a proper class
*/

// Add Module Manager constants
define( '_VIEWS_MODULE_MANAGER_KEY_', 'views' );
define( '_VIEW_TEMPLATES_MODULE_MANAGER_KEY_', 'view-templates' );

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
		
		/*Link to read-only versions of elements in installed modules*/
		add_action( 'wpmodules_library_link_components', 'wpv_modules_library_link_components', 10, 2 );
		
	}
	
}

/**
 * wpv_modules_library_link_components
 *
 * Hooks into the Module Manager Library listing and offers links to edit/readonly versions of each Views component
 *
 * @param $current_module
 * @param $modman_modules (array) installed modules as stored in the Options table
 *
 * @since 1.6.2
 */
function wpv_modules_library_link_components( $current_module = array(), $modman_modules = array() ) {
	$this_module_data = array();
	foreach ( $modman_modules as $hackey => $hackhack /* nice :D :D */ ) {
		if ( strtolower( $hackey ) == strtolower( $current_module['name'] ) ) {
			$this_module_data = $hackhack;
		}
	}
	if ( 
		( isset( $this_module_data[_VIEWS_MODULE_MANAGER_KEY_] ) && is_array( $this_module_data[_VIEWS_MODULE_MANAGER_KEY_] ) ) 
		||
		( isset( $this_module_data[_VIEW_TEMPLATES_MODULE_MANAGER_KEY_] ) && is_array( $this_module_data[_VIEW_TEMPLATES_MODULE_MANAGER_KEY_] ) ) 
	) {
		global $wpdb, $WP_Views;
		$embedded = $WP_Views->is_embedded();
	?>
	<div class="module-elements-container">
		<h4><?php _e( 'Views elements in this Module', 'wpv-views' ); ?></h4>
		<ul class="module-elements">
		<?php
		if ( isset( $this_module_data[_VIEWS_MODULE_MANAGER_KEY_] ) && is_array( $this_module_data[_VIEWS_MODULE_MANAGER_KEY_] ) ) {
			$view_titles = array();
			$view_pairs = false;
			foreach ( $this_module_data[_VIEWS_MODULE_MANAGER_KEY_] as $this_view ) {
				$view_titles[] = $this_view['title'];
			}
			if ( count( $view_titles ) > 0 ) {
				$values_to_prepare = array();
				$view_titles_count = count( $view_titles );
				$view_titles_placeholders = array_fill( 0, $view_titles_count, '%s' );
				$view_titles_flat = implode( ",", $view_titles_placeholders );
				foreach ( $view_titles as $view_titles_item ) {
					$values_to_prepare[] = $view_titles_item;
				}
				$values_to_prepare[] = 'view';
				$values_to_prepare[] = $view_titles_count;
				$view_pairs = $wpdb->get_results( 
					$wpdb->prepare( 
						"SELECT ID, post_title FROM {$wpdb->posts} 
						WHERE post_title IN ({$view_titles_flat}) 
						AND post_type = %s
						LIMIT %d",
						$values_to_prepare
					)
				);
			}
			if ( $view_pairs ) {
				$suffix = 'editor';
				if ( $embedded ) {
					$suffix = 'embedded';
				}
				foreach ( $view_pairs as $view_data ) {
					$prefix = 'views';
					if ( $WP_Views->is_archive_view( $view_data->ID ) ) {
						$prefix = 'view-archives';
					}
					echo '<li class="views-element"><a href="' . admin_url() . 'admin.php?page=' . $prefix . '-' . $suffix . '&view_id=' . $view_data->ID . '"><i class="icon-views ont-icon-19 ont-color-orange"></i>' . $view_data->post_title . '</a></li>';
				}
			}
		}
		if ( isset( $this_module_data[_VIEW_TEMPLATES_MODULE_MANAGER_KEY_] ) && is_array( $this_module_data[_VIEW_TEMPLATES_MODULE_MANAGER_KEY_] ) ) {
			$template_titles = array();
			$template_pairs = false;
			foreach ( $this_module_data[_VIEW_TEMPLATES_MODULE_MANAGER_KEY_] as $this_template ) {
				$template_titles[] = $this_template['title'];
			}
			if ( count( $template_titles ) > 0 ) {
				$values_to_prepare = array();
				$template_titles_count = count( $template_titles );
				$template_titles_placeholders = array_fill( 0, $template_titles_count, '%s' );
				$template_titles_flat = implode( ",", $template_titles_placeholders );
				foreach ( $template_titles as $template_titles_item ) {
					$values_to_prepare[] = $template_titles_item;
				}
				$values_to_prepare[] = 'view-template';
				$values_to_prepare[] = $template_titles_count;
				$template_pairs = $wpdb->get_results( 
					$wpdb->prepare( 
						"SELECT ID, post_title FROM {$wpdb->posts} 
						WHERE post_title IN ({$template_titles_flat}) 
						AND post_type = %s
						LIMIT %d",
						$values_to_prepare
					)
				);
			}
			if ( $template_pairs ) {
				foreach ( $template_pairs as $template_data ) {
					if ( $embedded ) {
						echo '<li class="views-element"><a href="' . admin_url() . 'admin.php?page=view-templates-embedded&view_id=' . $template_data->ID . '"><i class="icon-views ont-icon-19 ont-color-orange"></i>' . $template_data->post_title . '</a></li>';
					} else {
                        // We have full Views, so we can safely use the WPV_CT_EDITOR_PAGE_NAME constant.
						printf(
                            '<li class="views-element"><a href="%s"><i class="icon-views ont-icon-19 ont-color-orange"></i>%s</a></li>',
                            add_query_arg(
                                array( 'page' => WPV_CT_EDITOR_PAGE_NAME, 'ct_id' => $template_data->ID, 'action' => 'edit' ),
                                admin_url( 'admin.php' )
                            ),
                            sanitize_text_field( $template_data->post_title )
                        );
					}
				}
			}
		}
		?>
		</ul>
	</div>
	<?php
	}
}

/**
* wpv_register_modules_sections
*
* Register sections in Module Manager
*
* @since unknown
*
* @todo change to new icons
*/

function wpv_register_modules_sections( $sections ) {
	$sections[_VIEW_TEMPLATES_MODULE_MANAGER_KEY_] = array(
		'title' => __( 'Content Templates','wpv-views' ),
		'icon' => WPV_URL_EMBEDDED . '/res/img/views-icon-color_12X12.png',
        'icon_css' => 'icon-views-logo ont-icon-16 ont-color-orange'
	);

	$sections[_VIEWS_MODULE_MANAGER_KEY_] = array(
		'title' => __( 'Views','wpv-views' ),
		'icon' => WPV_URL_EMBEDDED . '/res/img/views-icon-color_12X12.png',
        'icon_css' => 'icon-views-logo ont-icon-16 ont-color-orange'
	);
	return $sections;
}

/**
* export_modules_views_items
*
* Export selected items - post_type=view
*
* @since unknown
*/

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

/**
* import_modules_views_items
*
* Import selected items - post_type=view
*
* @since unknown
*/

function import_modules_views_items( $result, $xmlstring, $items ) {
	$result = wpv_admin_import_data_from_xmlstring( $xmlstring, $items, 'views' );
	if ( is_wp_error( $result ) ) {
		return $result->get_error_message( $result->get_error_code() );
	}
	return $result;
}

/**
* check_modules_views_items
*
* Check selected items for changes - post_type=view
*
* @since unknown
*/

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

/**
* export_modules_view_templates_items
*
* Export selected items - post_type=view-template
*
* @since unknown
*/

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

/**
* import_modules_view_templates_items
*
* Import selected items - post_type=view-template
*
* @since unknown
*/

function import_modules_view_templates_items( $result, $xmlstring, $items ) {
	$result = wpv_admin_import_data_from_xmlstring( $xmlstring, $items, 'view-templates' );
	if ( is_wp_error( $result ) ) {
		return $result->get_error_message( $result->get_error_code() );
	}
	return $result;
}

/**
* check_modules_view_templates_items
*
* Check selected items for changes - post_type=view-template
*
* @since unknown
*/

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
	if ( defined( 'WPV_VERSION' ) ) {
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
    global $wpdb, $WPV_settings, $_wp_additional_image_sizes;

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
    if ( is_multisite() ) {
        $upload_directory = get_option('fileupload_url');
    } else {
        $wp_upload_dir = wp_upload_dir();
        $upload_directory = $wp_upload_dir['baseurl'];
    }

    // Basic arguments for the query
    $args = array(
        'posts_per_page' => -1,
        'post_status' => 'any'
    );

    // Adjust names
    $view_types = array(
        'view' => array( 'key' => 'views' ),
        'view-template' => array( 'key' => 'view-templates' )
    );

    // Set what to export and the post__in query arg if needed
    if ( 
		is_string( $items ) 
		&& 'all' === $items 
	) {
        $export = true;
    } elseif ( 
		is_array( $items ) 
		&& ! empty( $items ) 
	) {
        $args['post__in'] = $items;
        $export = true;
    }

    // Check we are exporting the right type and then set the post_type query arg
    if ( ! in_array( $type, array_keys( $view_types ) ) ) {
		$export = false;
	} else {
        $args['post_type'] = $type;
        $vkey = $view_types[$type]['key'];
    }
	
	// Return if we won't export
    if ( ! $export ) {
		return '';
	}
	
	/**
	* wpv_filter_view_extra_fields_for_import_export
	*
	* Filter set the postmeta needed for Views export and import, beyond the settings and layout settings
	*
	* @param (array) The postmeta keys
	*
	* @since 1.7
	*/
	
	$extra_metas = apply_filters( 'wpv_filter_view_extra_fields_for_import_export', array() );

	// Start collecting data to export
    switch( $type ) {
		
		// If exporting Views
		case 'view':
			// Get the views
			$views = get_posts( $args );
			if ( ! empty( $views ) ) {
				// Compose the image size array
				if (
					! isset( $_wp_additional_image_sizes ) || 
					! is_array( $_wp_additional_image_sizes )
				) {
					$_wp_additional_image_sizes = array();
				}
				$attached_images_sizes = array_merge(
					// additional thumbnail sizes
					array_keys( $_wp_additional_image_sizes ),
					// wp default thumbnail sizes
					array( 'thumbnail', 'medium', 'large' )
				);
				// Create the data['views'] array
				$data['views'] = array('__key' => 'view');
				// Loop through the returned Views to take their data
				foreach ( $views as $key => $post ) {
					$post = (array) $post;
					// Only export items with post_name
					if ( $post['post_name'] ) {
						$hash_data = array();
						$post_data = array();
						$this_settings = array();
						$this_layout_settings = array();
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
						// Basic data
						$copy_data = array( 'ID', 'post_content', 'post_title', 'post_name', 'post_excerpt', 'post_type', 'post_status' );
						foreach ( $copy_data as $copy ) {
							if ( isset( $post[$copy] ) ) {
								$post_data[$copy] = $post[$copy];
							}
						}
						$data['views']['view-' . $post['ID']] = $post_data;
						$hash_basics = array( 'post_title', 'post_name', 'post_type', 'post_status' );
						foreach ( $hash_basics as $basics ) {
							if ( isset( $data['views']['view-' . $post['ID']][$basics] ) ) $hash_data[$basics] = $data['views']['view-' . $post['ID']][$basics];
						}
						if ( isset( $data['views']['view-' . $post['ID']]['post_content'] ) ) {
							$hash_data['post_content'] = preg_replace('/\s+/', '', str_replace("\n","",$data['views']['view-' . $post['ID']]['post_content']));
						}
						if ( isset( $data['views']['view-' . $post['ID']]['post_excerpt'] ) ) {
							$hash_data['post_excerpt'] = preg_replace('/\s+/', '', str_replace("\n","",$data['views']['view-' . $post['ID']]['post_excerpt']));
						}
						// Meta data
						$meta = get_post_custom( $post['ID'] );
						if ( ! empty( $meta ) ) {
							$data['view']['view-' . $post['ID']]['meta'] = array();
							foreach ( $meta as $meta_key => $meta_value ) {
								// View settings
								if ( $meta_key == '_wpv_settings' ) {
									$value = maybe_unserialize( $meta_value[0] );
									$this_settings = $value;
									// Add any taxonomy terms so we can re-map when we import.
									if ( ! empty( $value['taxonomy_terms'] ) ) {
										$taxonomy = $value['taxonomy_type'][0];
										foreach ( $value['taxonomy_terms'] as $term_id ) {
											if ( ! isset( $data['terms_map']['term_' . $term_id] ) ) {
												$term = get_term( $term_id, $taxonomy );
												if ( isset( $term ) && !is_wp_error( $term ) ) {
													$data['terms_map']['term_' . $term->term_id]['old_id'] = $term->term_id;
													$data['terms_map']['term_' . $term->term_id]['slug'] = $term->slug;
													$data['terms_map']['term_' . $term->term_id]['taxonomy'] = $taxonomy;
												}
											}
										}
									}
									
									/**
									* wpv_filter_adjust_view_settings_for_export
									*
									* Filter to adjust Views settings on export
									*
									* Some View settings are stored as indexed arrays, producing errors on index 0
									* We need to transform those indexed arrays into associative arrays before export, that will be restored on import
									* Also, some settings contain IDs pointing to other Views or Content Templates
									* We need to transform them into names, that will be restored on import
									*
									* @param (array) $value The View settings
									* @param (array) $post The post object as an array
									*
									* @since 1.7
									*/
									
									$value = apply_filters( 'wpv_filter_adjust_view_settings_for_export', $value, $post );
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
								} else if ( $meta_key == '_wpv_layout_settings' ) {
									$value = maybe_unserialize( $meta_value[0] );
									$this_layout_settings = $value;
							
									/**
									* wpv_filter_adjust_view_layout_settings_for_export
									*
									* Filter to adjust Views layouts settings on export
									*
									* @param (array) $value The View layout settings
									* @param (array) $post The View post object as an array
									*
									* @since 1.7
									*/
									
									$value = apply_filters( 'wpv_filter_adjust_view_layout_settings_for_export', $value, $post );
									// Add the data to export
									$data['views']['view-' . $post['ID']]['meta'][$meta_key] = $value;
									// Add the hash for Module Manager if needed
									if ( 'module_manager' == $mode ) {
										$hash_data['meta'][$meta_key] = $value;
										if ( isset( $value['layout_meta_html'] ) ) $hash_data['meta'][$meta_key]['layout_meta_html'] = preg_replace( '/\s+/', '', str_replace( "\n", "", $value['layout_meta_html'] ) );
										if ( isset( $value['generated_layout_meta_html'] ) ) $hash_data['meta'][$meta_key]['generated_layout_meta_html'] = preg_replace( '/\s+/', '', str_replace( "\n", "", $value['generated_layout_meta_html'] ) );
									}
								} else {
									if ( in_array( $meta_key, $extra_metas ) ) {
										$value = maybe_unserialize( $meta_value[0] );
										
										/**
										* wpv_filter_adjust_view_extra_fields_for_export
										*
										* Filter to adjust Views postmeta needed on export
										*
										* @param (array) $value The postmeta value
										* @param (array) $post The View post object as an array
										* @meta_key (string) The postmeta key being adjusted
										*
										* @since 1.7
										*/
										
										$value = apply_filters( 'wpv_filter_adjust_view_extra_fields_for_export', $value, $post, $meta_key );
										if ( ! empty( $value ) ) {
											$data['views']['view-' . $post['ID']]['meta'][$meta_key] = $value;
										}
									}
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
								$image_type = get_post_mime_type($attachment->ID);	
								if ( $image_type && ($image_type == 'image/jpeg' || $image_type == 'image/png' || $image_type == 'image/gif') ){
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
									// @todo apply the same logic as in natural export
									// @todo use $this_settings_metaboxes and $this_layout_settings_metaboxes
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
									if ( $poscont !== false ) {
										$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['on_post_content'] = $attachment->guid;
										if ( 'module_manager' == $mode ) {
											$hash_data['post_content'] = str_replace($attachment->guid, md5($imdata), $hash_data['post_content']);
										}
									}
									foreach ( $imthumbs as $thumbsize => $thumbdata ) {
										if (
											! empty( $thumbdata ) 
											&& isset( $thumbdata[0] )
										) {
											$pos = strpos( $data['views']['view-' . $post['ID']]['post_content'], $thumbdata[0] );
											if ( $pos !== false ) {
												$data['views']['view-' . $post['ID']]['attachments']['attach_'.$attachment->ID]['on_post_content_sizes'][$thumbsize] = $thumbdata[0];
												if ( 'module_manager' == $mode ) {
													$hash_data['post_content'] = str_replace($thumbdata[0], md5($imdata) . '_' . $thumbsize, $hash_data['post_content']);
												}
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
			$view_templates = get_posts( $args );
			if ( ! empty( $view_templates ) ) {
				// Compose the image size array
				if (
					! isset( $_wp_additional_image_sizes ) 
					|| ! is_array( $_wp_additional_image_sizes ) 
				) {
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
						$att_args = array( 
							'post_type' => 'attachment', 
							'numberposts' => -1, 
							'post_status' => null, 
							'post_parent' => $post['ID'] 
						);
						$attachments = get_posts( $att_args );
						if ( $attachments ) {
							$post_data['attachments'] = array();
							foreach ( $attachments as $attachment ) {
								$image_type = get_post_mime_type( $attachment->ID );	
								if ( 
									$image_type 
									&& (
										$image_type == 'image/jpeg' 
										|| $image_type == 'image/png' 
										|| $image_type == 'image/gif'
									) 
								) {
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
									foreach ( $attached_images_sizes as $ts ) {
										$imthumbs[$ts] = wp_get_attachment_image_src( $attachment->ID, $ts );
									}
									// Adjust images in CSS
									if ( isset( $template_extra_css ) ) {
										$pos = strpos( $template_extra_css, $attachment->guid );
										if ( $pos !== false ) {
											$post_data['attachments']['attach_'.$attachment->ID]['on_meta_html_css'] = $attachment->guid;
										}
										foreach ( $imthumbs as $thumbsize => $thumbdata ) {
											if (
												! empty( $thumbdata ) 
												&& isset( $thumbdata[0] )
											) {
												$pos = strpos( $template_extra_css, $thumbdata[0] );
												if ( $pos !== false ) {
													$post_data['attachments']['attach_'.$attachment->ID]['on_meta_html_css_sizes'][$thumbsize] = $thumbdata[0];
												}
											}
										}
									}
									// Adjust images in JS
									if ( isset( $template_extra_js ) ) {
										$posjs = strpos( $template_extra_js, $attachment->guid );
										if ( $posjs !== false ) {
											$post_data['attachments']['attach_'.$attachment->ID]['on_meta_html_js'] = $attachment->guid;
										}
										foreach ( $imthumbs as $thumbsize => $thumbdata ) {
											if (
												! empty( $thumbdata ) 
												&& isset( $thumbdata[0] )
											) {
												$pos = strpos( $template_extra_js, $thumbdata[0] );
												if ( $pos !== false ) {
													$post_data['attachments']['attach_'.$attachment->ID]['on_meta_html_js_sizes'][$thumbsize] = $thumbdata[0];
												}
											}
										}
									}
									//Adjust images in content
									$poscont = strpos( $post_data['post_content'], $attachment->guid );
									if ( $poscont !== false ) {
										$post_data['attachments']['attach_'.$attachment->ID]['on_post_content'] = $attachment->guid;
									}
									foreach ( $imthumbs as $thumbsize => $thumbdata ) {
										if (
											! empty( $thumbdata ) 
											&& isset( $thumbdata[0] )
										) {
											$pos = strpos( $post_data['post_content'], $thumbdata[0] );
											if ( $pos !== false ) {
												$post_data['attachments']['attach_'.$attachment->ID]['on_post_content_sizes'][$thumbsize] = $thumbdata[0];
											}
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
	/** EMERSON: Add content template and WordPress archives assignments to post types in settings, add to export XML */
	/** START */	
	if (
		( 'module_manager' == $mode ) 
		&& ( 'view-template' == $type )
	) {
        //This is a module manager export request for Content Template
		if ( ! $WPV_settings->is_empty() ) {
			$wpv_settings_to_export = array();
			foreach ( $WPV_settings as $option_name => $option_value ) {
				if ( strpos( $option_name, 'views_template_for_' ) === 0 ) {
					$item_name = $wpdb->get_var( 
						$wpdb->prepare( 
							"SELECT post_name FROM {$wpdb->posts} 
							WHERE ID = %s 
							LIMIT 1", 
							$option_value 
						) 
					);
					if ( $item_name ) {
						$wpv_settings_to_export[$option_name] = $item_name;
					}
				}
			}
			$data['settings'] = $wpv_settings_to_export;
		}
	} elseif (
		( 'module_manager' == $mode ) 
		&& ( 'view' == $type )
	) {
		//This is a module manager export request for WordPress archives
		if ( ! $WPV_settings->is_empty() ) {
			$wpv_settings_to_export = array();
			foreach ( $WPV_settings as $option_name => $option_value ) {
				if ( strpos( $option_name, 'view_' ) === 0 ) {
					$item_name = $wpdb->get_var( 
						$wpdb->prepare( 
							"SELECT post_name FROM {$wpdb->posts} 
							WHERE ID = %s 
							LIMIT 1", 
							$option_value 
						) 
					);
					if ( $item_name ) {
						$wpv_settings_to_export[$option_name] = $item_name;
					}
				}
			}
			$data['settings'] = $wpv_settings_to_export;
    	}
    }
    /** END */
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

/*
* wpv_admin_import_data_from_xmlstring
*
* Custom Import function for Module Manager
*
* Imports given xml string, an array of items to import and the type of data to import
*
* @param $xmlstring (string) String-ized version of an import XML file
* @param $items (array) Array of items to import - note that the values are arrays prefixed with the Module Manager register key for the component
* @param $import_type (string) <views|view-templates> Type of element to import
*
* @since unknown
*/
function wpv_admin_import_data_from_xmlstring( $xmlstring, $items = array(), $import_type = null ) {
    global $WPV_Export_Import;
    if ( ! empty( $xmlstring ) ) {
        if ( ! function_exists( 'simplexml_load_string' ) ) {
            return new WP_Error( 'xml_missing', __( 'The Simple XML library is missing.', 'wpv-views' ) );
        }
        $xml = simplexml_load_string( $xmlstring );
        if ( ! $xml ) {
            return new WP_Error( 'not_xml_file', sprintf( __( 'The XML could not be read.', 'wpv-views' ) ) );
        }
        $import_data = wpv_admin_import_export_simplexml2array( $xml );
        if ( isset( $import_type ) ) {
			if ( 'view-templates' == $import_type ) { // Import Content Templates
				$import_items = array();
				foreach ( $items as $item ) {
					$import_items[] = str_replace( _VIEW_TEMPLATES_MODULE_MANAGER_KEY_ , '', $item );
				}
				$args = array(
					'force_import_id' => $import_items,
					'return_to' => 'module_manager'
				);
				$result = $WPV_Export_Import->import_content_templates( $import_data, $args );
				
				/** EMERSON: Import Content Template assignments to post types */
				//Proceed only if settings are set and not empty
				if ( 
					isset( $import_data['settings'] ) 
					&& ! empty( $import_data['settings'] )
				) {
					$error = $WPV_Export_Import->import_settings( $import_data );						
				}				
				
				return $result;
				
			} elseif ( 'views' == $import_type ) { // Import Views
				$import_items = array();
				foreach ( $items as $item ) {
					$import_items[] = str_replace( _VIEWS_MODULE_MANAGER_KEY_, '', $item );
				}
				$args = array(
					'force_import_id' => $import_items,
					'return_to' => 'module_manager'
				);
				$result = $WPV_Export_Import->import_views( $import_data, $args );
				
				/** EMERSON: Import WordPress archive assignments */
				//Proceed only if settings are set and not empty
				if ( 
					isset( $import_data['settings'] ) 
					&& ! empty( $import_data['settings'] )
				) {
					foreach ( $import_data['settings'] as $k => $v ) {
						if ( ! ( strpos( $k, 'view_' ) === 0 ) ) {							
							unset( $import_data['settings'][$k] );
						} 
					}
					$error = $WPV_Export_Import->import_settings( $import_data );
				}

				return $result;
				
			} else { // Defined but not known $import_type
				$results = array(
					'updated' => 0,
					'new' => 0,
					'failed' => 0,
					'errors' => array()
				);
				return $results;
			}
        } else { // Not set $import_type
			$results = array(
				'updated' => 0,
				'new' => 0,
				'failed' => 0,
				'errors' => array()
			);
			return $results;
        }
    } else { // empty xml string
		$results = array(
			'updated' => 0,
			'new' => 0,
			'failed' => 0,
			'errors' => array()
		);
		return $results;
    }
}