<?php
/**
 * @package Make child
 *
 * Add your custom functions here.
 */

/**
 * Clean up the output of the <head> block
 */
function child_clean_header() {
  remove_action( 'wp_head', 'feed_links_extra', 3 ); // Display the links to the extra feeds such as category feeds
  remove_action( 'wp_head', 'feed_links', 2 );       // Display the links to the general feeds: Post and Comment Feed
  remove_action( 'wp_head', 'rsd_link' );            // Display the link to the Really Simple Discovery service endpoint, EditURI link
  remove_action( 'wp_head', 'wlwmanifest_link' );    // Display the link to the Windows Live Writer manifest file.
  remove_action( 'wp_head', 'index_rel_link' );      // index link
  remove_action( 'wp_head', 'wp_generator' );        // Display the XHTML generator that is generated on the wp_head hook, WP version
}
add_action( 'init', 'child_clean_header' );

function child_add_body_class( $classes ) {
  global $post;
  if ( isset( $post ) ) {
      $classes[] = $post->post_type . '-' . $post->post_name;
  }
  if ( !is_front_page() ) {
    $classes[] = 'not-home';
  }
  $classes[] = 'child-custom';
  return $classes;
}

add_filter( 'body_class', 'child_add_body_class' );

/**
 * Enqueue scripts and styles
 */
function child_scripts() {
  wp_enqueue_style( 'child-style', get_stylesheet_uri() . '/stylesheets/style.css', array('ttfmake-main-style'), '1.0');
}
add_action( 'wp_enqueue_scripts', 'child_scripts' );