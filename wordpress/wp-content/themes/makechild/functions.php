<?php
/**
 * child functions and definitions
 *
 * @package child
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

function child_nofollow_cat_posts($text) {
global $post;
        if( in_category(1) ) { // SET CATEGORY ID HERE
                $text = stripslashes(wp_rel_nofollow($text));
        }
        return $text;
}
add_filter('the_content', 'child_nofollow_cat_posts');

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) )
  $content_width = 640; /* pixels */

if ( ! function_exists( 'child_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 */
function child_setup() {

  /**
   * Make theme available for translation
   * Translations can be filed in the /languages/ directory
   * If you're building a theme based on _s, use a find and replace
   * to change '_s' to the name of your theme in all the template files
   */
  load_theme_textdomain( 'child', get_template_directory() . '/languages' );

  /**
   * Add default posts and comments RSS feed links to head
   */
  add_theme_support( 'automatic-feed-links' );

  /**
   * Enable support for Post Thumbnails on posts and pages
   *
   * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
   */
  //add_theme_support( 'post-thumbnails' );
  //set_post_thumbnail_size( 247, 270 );  
}
endif; // child_setup
add_action( 'after_setup_theme', 'child_setup' );



/**
 * Enqueue scripts and styles
 */
function child_scripts() {
  wp_enqueue_style( 'child-style', get_stylesheet_uri() . '/stylesheets/style.css', array('ttfmake-main-style') '1.0');
}
add_action( 'wp_enqueue_scripts', 'child_scripts' );

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