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
  add_theme_support( 'post-thumbnails' );
  //set_post_thumbnail_size( 247, 270 );

  /**
   * This theme uses wp_nav_menu() in one location.
   */
  register_nav_menus( array(
    'primary' => __( 'Primary Menu', 'child' ),
  ) );

  /**
   * Enable support for Post Formats
   */
  add_theme_support( 'post-formats', array( 'aside', 'image', 'video', 'quote', 'link' ) );

  /**
   * Setup the WordPress core custom background feature.
   */
  add_theme_support( 'custom-background', apply_filters( 'child_custom_background_args', array(
    'default-color' => 'ffffff',
    'default-image' => '',
  ) ) );
  // Switches default core markup for search form, comment form, and comments
  // to output valid HTML5.
  add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list' ) );
}
endif; // child_setup
add_action( 'after_setup_theme', 'child_setup' );

  /**
   * Add Google fonts to the header
   */
  function child_google_fonts() {
    echo '<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=PT+Sans:400,700|Roboto:400|Roboto+Condensed:400,300,700" media="screen">';
  }
  add_action( 'wp_head', 'child_google_fonts', 5);

/**
 * Register widgetized area and update sidebar with default widgets
 */
function child_widgets_init() {
  register_sidebar( array(
    'name'          => __( 'Sidebar', 'child' ),
    'id'            => 'sidebar-1',
    'before_widget' => '<aside id="%1$s" class="widget %2$s">',
    'after_widget'  => '</aside>',
    'before_title'  => '<h1 class="widget-title">',
    'after_title'   => '</h1>',
  ) );
}
add_action( 'widgets_init', 'child_widgets_init' );

/**
 * Enqueue scripts and styles
 */
function child_scripts() {
  wp_enqueue_style( 'child-style', get_stylesheet_uri() );
  wp_enqueue_script( 'child_modernizr', get_template_directory_uri() . '/js/modernizr.custom.js', array(), '1.0', false );
  wp_enqueue_script( 'child-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20120206', true );
  wp_enqueue_script( 'child_selectivizr', get_template_directory_uri() . '/js/selectivizr-min.js', array('jquery'), '1.0.2', false );
  wp_enqueue_script( 'child-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true );
  wp_enqueue_script( 'child', get_template_directory_uri() . '/js/child.js', array(), '1.2', 'true');
  wp_enqueue_script( 'child-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20120206', true );
  wp_enqueue_script( 'child-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20130115', true );

  if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
    wp_enqueue_script( 'comment-reply' );
  }

  if ( is_singular() && wp_attachment_is_image() ) {
    wp_enqueue_script( 'child-keyboard-image-navigation', get_template_directory_uri() . '/js/keyboard-image-navigation.js', array( 'jquery' ), '20120202' );
  }
}
add_action( 'wp_enqueue_scripts', 'child_scripts' );