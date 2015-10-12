<?php

/**
 * Register the post type of Content Templates.
 *
 * @since unknown
 */
function wpv_register_type_view_template() 
{
  $labels = array(
    'name' => _x('Content Templates', 'post type general name'),
    'singular_name' => _x('Content Template', 'post type singular name'),
    'add_new' => _x('Add New', 'book'),
    'add_new_item' => __('Add New Content Template', 'wpv-views'),
    'edit_item' => __('Edit Content Template', 'wpv-views'),
    'new_item' => __('New Content Template', 'wpv-views'),
    'view_item' => __('View Views-Templates', 'wpv-views'),
    'search_items' => __('Search Content Templates', 'wpv-views'),
    'not_found' =>  __('No Content Templates found', 'wpv-views'),
    'not_found_in_trash' => __('No Content Templates found in Trash', 'wpv-views'), 
    'parent_item_colon' => '',
    'menu_name' => 'Content Templates'

  );
  $args = array(
    'labels' => $labels,
    'public' => false,
    'publicly_queryable' => false,
    'show_ui' => false, 
    'show_in_menu' => false, 
    'query_var' => false,
    'rewrite' => false,
    'can_export' => false,
    'capability_type' => 'post',
    'has_archive' => false, 
    'hierarchical' => false,
    'menu_position' => null,
    'supports' => array('title','editor','author'),
  ); 
  register_post_type('view-template',$args);
}
