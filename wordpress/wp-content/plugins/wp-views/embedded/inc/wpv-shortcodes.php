<?php

require_once( dirname( __FILE__ ) . "/classes/wpv-wp-filter-state.class.php" );
require_once( dirname( __FILE__ ) . "/classes/wpv-render-filters.class.php" );

/**
 * Array of shortcodes that will be offered in the Views dialog popup.
 *
 * Each element must be an array with three elements:
 * 1. shortcode slug
 * 2. shortcode display name
 * 3. callback function
 *
 * @since unknown
 */
global $wpv_shortcodes;

$wpv_shortcodes = array();

$wpv_shortcodes['wpv-post-title'] = array('wpv-post-title', __('Post title', 'wpv-views'), 'wpv_shortcode_wpv_post_title');
$wpv_shortcodes['wpv-post-link'] = array('wpv-post-link', __('Post title with a link', 'wpv-views'), 'wpv_shortcode_wpv_post_link');
$wpv_shortcodes['wpv-post-url'] = array('wpv-post-url', __('Post URL', 'wpv-views'), 'wpv_shortcode_wpv_post_url');
$wpv_shortcodes['wpv-post-body'] = array('wpv-post-body', __('Post body', 'wpv-views'), 'wpv_shortcode_wpv_post_body');
$wpv_shortcodes['wpv-post-excerpt'] = array('wpv-post-excerpt', __('Post excerpt', 'wpv-views'), 'wpv_shortcode_wpv_post_excerpt');
$wpv_shortcodes['wpv-post-date'] = array('wpv-post-date', __('Post date', 'wpv-views'), 'wpv_shortcode_wpv_post_date');
$wpv_shortcodes['wpv-post-author'] = array('wpv-post-author', __('Post author', 'wpv-views'), 'wpv_shortcode_wpv_post_author');
$wpv_shortcodes['wpv-post-featured-image'] = array('wpv-post-featured-image', __('Post featured image', 'wpv-views'), 'wpv_shortcode_wpv_post_featured_image');
$wpv_shortcodes['wpv-post-id'] = array('wpv-post-id', __('Post ID', 'wpv-views'), 'wpv_shortcode_wpv_post_id');
$wpv_shortcodes['wpv-post-slug'] = array('wpv-post-slug', __('Post slug', 'wpv-views'), 'wpv_shortcode_wpv_post_slug');
$wpv_shortcodes['wpv-post-type'] = array('wpv-post-type', __('Post type', 'wpv-views'), 'wpv_shortcode_wpv_post_type');
$wpv_shortcodes['wpv-post-format'] = array('wpv-post-format', __('Post format', 'wpv-views'), 'wpv_shortcode_wpv_post_format');
$wpv_shortcodes['wpv-post-status'] = array('wpv-post-status', __('Post status', 'wpv-views'), 'wpv_shortcode_wpv_post_status');
$wpv_shortcodes['wpv-post-comments-number'] = array('wpv-post-comments-number', __('Post comments number', 'wpv-views'), 'wpv_shortcode_wpv_comments_number');
$wpv_shortcodes['wpv-post-class'] = array('wpv-post-class', __('Post class', 'wpv-views'), 'wpv_shortcode_wpv_post_class');
$wpv_shortcodes['wpv-post-edit-link'] = array('wpv-post-edit-link', __('Post edit link', 'wpv-views'), 'wpv_shortcode_wpv_post_edit_link');



// NOTE:  Put all "post" shortcodes before 'wpv-post-field' so they appear in the right order in various popups.
$wpv_shortcodes['wpv-post-field'] = array('wpv-post-field', __('Post field', 'wpv-views'), 'wpv_shortcode_wpv_post_field');
$wpv_shortcodes['wpv-for-each'] = array('wpv-for-each', __('Post field iterator', 'wpv-views'), 'wpv_for_each_shortcode');


$wpv_shortcodes['wpv-comment-title'] = array('wpv-comment-title', __('Comment title', 'wpv-views'), 'wpv_shortcode_wpv_comment_title');
$wpv_shortcodes['wpv-comment-body'] = array('wpv-comment-body', __('Comment body', 'wpv-views'), 'wpv_shortcode_wpv_comment_body');
$wpv_shortcodes['wpv-comment-author'] = array('wpv-comment-author', __('Comment Author', 'wpv-views'), 'wpv_shortcode_wpv_comment_author');
$wpv_shortcodes['wpv-comment-date'] = array('wpv-comment-date', __('Comment Date', 'wpv-views'), 'wpv_shortcode_wpv_comment_date');

$wpv_shortcodes['wpv-taxonomy-title'] = array('wpv-taxonomy-title', __('Taxonomy title', 'wpv-views'), 'wpv_shortcode_wpv_tax_title');
$wpv_shortcodes['wpv-taxonomy-link'] = array('wpv-taxonomy-link', __('Taxonomy title with a link', 'wpv-views'), 'wpv_shortcode_wpv_tax_title_link');
$wpv_shortcodes['wpv-taxonomy-url'] = array('wpv-taxonomy-url', __('Taxonomy URL', 'wpv-views'), 'wpv_shortcode_wpv_tax_url');
$wpv_shortcodes['wpv-taxonomy-slug'] = array('wpv-taxonomy-slug', __('Taxonomy slug', 'wpv-views'), 'wpv_shortcode_wpv_tax_slug');
$wpv_shortcodes['wpv-taxonomy-id'] = array('wpv-taxonomy-id', __('Taxonomy ID', 'wpv-views'), 'wpv_shortcode_wpv_tax_id');
$wpv_shortcodes['wpv-taxonomy-description'] = array('wpv-taxonomy-description', __('Taxonomy description', 'wpv-views'), 'wpv_shortcode_wpv_tax_description');
$wpv_shortcodes['wpv-taxonomy-post-count'] = array('wpv-taxonomy-post-count', __('Taxonomy post count', 'wpv-views'), 'wpv_shortcode_wpv_tax_items_count');
$wpv_shortcodes['wpv-taxonomy-archive'] = array('wpv-taxonomy-archive', __('Taxonomy page info', 'wpv-views'), 'wpv_shortcode_wpv_taxonomy_archive');


// $wpv_shortcodes['wpv-control'] = array('wpv-control', __('Filter control', 'wpv-views'), 'wpv_shortcode_wpv_control');

$wpv_shortcodes['wpv-bloginfo'] = array('wpv-bloginfo', __('Site information', 'wpv-views'), 'wpv_bloginfo');
$wpv_shortcodes['wpv-search-term'] = array('wpv-search-term', __('Search term', 'wpv-views'), 'wpv_search_term');
$wpv_shortcodes['wpv-archive-title'] = array('wpv-archive-title', __('Archive title', 'wpv-views'), 'wpv_archive_title');
$wpv_shortcodes['wpv-archive-link'] = array('wpv-archive-link', __('Post archive link', 'wpv-views'), 'wpv_archive_link');

//User shortcodes
$wpv_shortcodes['wpv-current-user'] = array('wpv-current-user', __('Current user info', 'wpv-views'), 'wpv_current_user');
$wpv_shortcodes['wpv-user'] = array('wpv-user', __('Show user data', 'wpv-views'), 'wpv_user');
$wpv_shortcodes['wpv-login-form'] = array('wpv-login-form', __('Login form', 'wpv-views'), 'wpv_shortcode_wpv_login_form');

if (defined('WPV_WOOCOMERCE_VIEWS_SHORTCODE')) {
	$wpv_shortcodes['wpv-wooaddcart'] = array('wpv-wooaddcart', __('Add to cart button', 'wpv-views'), 'wpv-wooaddcart');
}
if (defined('WPV_WOOCOMERCEBOX_VIEWS_SHORTCODE')) {
	$wpv_shortcodes['wpv-wooaddcartbox'] = array('wpv-wooaddcartbox', __('Add to cart box', 'wpv-views'), 'wpv-wooaddcartbox');
}

// register the short codes
foreach ($wpv_shortcodes as $shortcode) {
	if (function_exists($shortcode[2])) {
		add_shortcode($shortcode[0], $shortcode[2]);
	}
}

// Init taxonomies shortcode
wpv_post_taxonomies_shortcode();

/**
 * Get the shortcode via name.
 *
 * @since unknown
 */
function wpv_get_shortcode($name) {
	global $wpv_shortcodes;

	foreach ($wpv_shortcodes as $shortcode) {
		if ($shortcode[1] == $name) {
			return $shortcode[0];
		}
	}

	if ($name == 'Taxonomy View') {
		return WPV_TAXONOMY_VIEW;
	}

	if ($name == 'Post View') {
		return WPV_POST_VIEW;
	}

	return null;
}

/**
 * Views-Shortcode: wpv-bloginfo
 *
 * Description: Display bloginfo values.
 *
 * Parameters:
 * 'show' => parameter for show.
 *   "name" displays site title (Ex. "Testpilot")(Default)
 *   "description" displays tagline (Ex. Just another WordPress blog)
 *   "admin_email" displays (Ex. admin@example.com)
 *   "url" displays site url (Ex. http://example/home)
 *   "wpurl" displays home url (Ex. http://example/home/wp)
 *   "stylesheet_directory" displays stylesheet directory (Ex. http://example/home/wp/wp-content/themes/child-theme)
 *   "stylesheet_url" displays stylesheet url (Ex. http://example/home/wp/wp-content/themes/child-theme/style.css)
 *   "template_directory" displays template directory (Ex. http://example/home/wp/wp-content/themes/parent-theme)
 *   "template_url" displays template url (Ex. http://example/home/wp/wp-content/themes/parent-theme)
 *   "atom_url" displays url to feed in atom format (Ex. http://example/home/feed/atom)
 *   "rss2_url" displays url to feed in rss2 format (Ex. http://example/home/feed)
 *   "rss_url" displays url to feed in rss format (Ex. http://example/home/feed/rss)
 *   "pingback_url" displays pingback url (Ex. http://example/home/wp/xmlrpc.php)
 *   "rdf_url" displays rdf url(Ex. http://example/home/feed/rdf)
 *   "comments_atom_url" displays comments atom url (Ex. http://example/home/comments/feed/atom)
 *   "comments_rss2_url" displays comments rss2 url (Ex. http://example/home/comments/feed)
 *   "charset" displays site charset (Ex. UTF-8)
 *   "html_type" displays site html type (Ex. text/html)
 *   "language" displays site language (Ex. en-US)
 *   "text_direction" displays site text direction (Ex. ltr)
 *   "version" displays WordPress version (Ex. 3.1)
 *
 * Example usage:
 * url: [vpw-bloginfo show="url"]
 *
 * Link:
 * List of available parameters <a href="http://codex.wordpress.org/Function_Reference/bloginfo#Parameters">http://codex.wordpress.org/Function_Reference/bloginfo#Parameters</a>
 *
 * Note:
 *
 */

function wpv_bloginfo($attr){
	extract(
		shortcode_atts( array('show' => 'name'), $attr )
	);
	$out = '';
	$available_codes = array(
		'name', 'description', 'admin_email', 'url', 'wpurl', 'stylesheet_directory',
		'stylesheet_url', 'template_directory', 'template_url', 'atom_url', 'rss2_url',
		'rss_url','pingback_url','rdf_url','comments_atom_url','comments_rss2_url',
		'charset','html_type','language','text_direction','version'
	);
	if(in_array($show, $available_codes)){
		$out = get_bloginfo( $show, 'display' );
	}
	apply_filters('wpv_shortcode_debug','wpv-bloginfo', json_encode($attr), '', 'Data received from cache', $out);
	return $out;
}

/**
* wpv_shortcodes_register_wpv_bloginfo_data
*
* Register the wpv-bloginfo shortcode in the GUI API.
*
* @since 1.9
*/

add_filter( 'wpv_filter_wpv_shortcodes_gui_data', 'wpv_shortcodes_register_wpv_bloginfo_data' );

function wpv_shortcodes_register_wpv_bloginfo_data( $views_shortcodes ) {
	$views_shortcodes['wpv-bloginfo'] = array(
		'callback' => 'wpv_shortcodes_get_wpv_bloginfo_data'
	);
	return $views_shortcodes;
}

function wpv_shortcodes_get_wpv_bloginfo_data() {
    $data = array(
        'name' => __( 'Site information', 'wpv-views' ),
        'label' => __( 'Site information', 'wpv-views' ),
        'attributes' => array(
            'display-options' => array(
                'label' => __('Display options', 'wpv-views'),
                'header' => __('Display options', 'wpv-views'),
                'fields' => array(
                    'show' => array(
                        'label' => __( 'Show this information', 'wpv-views'),
                        'type' => 'select',
                        'options' => array(
                            'name' => __( 'Site name', 'wpv-views' ),
							'description' => __( 'Site description', 'wpv-views' ),
							'admin_email' => __( 'Administration email', 'wpv-views' ),
							'url' => __( 'Site address (URL)', 'wpv-views' ),
							'wpurl' => __( 'WordPress address (URL)', 'wpv-views' ),
							'stylesheet_directory' => __( 'Stylesheet directory URL of the active theme', 'wpv-views' ),
                            'stylesheet_url' => __( 'Primary CSS file URL of the active theme', 'wpv-views' ),
							'template_directory' => __( 'URL of the active theme\'s directory', 'wpv-views' ),
							'template_url' => __( 'URL of the active theme\'s directory', 'wpv-views' ),
							'atom_url' => __( 'Atom feed URL', 'wpv-views' ),
							'rss2_url' => __( 'RSS 2.0 feed URL', 'wpv-views' ),
                            'rss_url' => __( 'RSS 0.92 feed URL', 'wpv-views' ),
							'pingback_url' => __( 'Pingback XML-RPC file URL', 'wpv-views' ),
							'rdf_url' => __( 'RDF/RSS 1.0 feed URL', 'wpv-views' ),
							'comments_atom_url' => __( 'Comments Atom feed URL ', 'wpv-views' ),
							'comments_rss2_url' => __( 'Comments RSS 2.0 feed URL', 'wpv-views' ),
                            'charset' => __( 'Encoding for pages and feeds', 'wpv-views' ),
							'html_type' => __( 'Content-Type of WordPress HTML pages', 'wpv-views' ),
							'language' => __( 'Language', 'wpv-views' ),
							'text_direction' => __( 'Text direction', 'wpv-views' ),
							'version' => __( 'WordPress version', 'wpv-views' )
                        ),
                        'default' => 'name',
						'documentation' => '<a href="http://codex.wordpress.org/Function_Reference/bloginfo" target="_blank">' . __( 'WordPress bloginfo function', 'wpv-views' ) . '</a>'
                    ),
                ),
            ),
        ),
    );
    return $data;
}

/**
 * Views-Shortcode: wpv-search-term
 *
 * Description: Display search term value
 *
 * Parameters:
 * 'param' => Default = s
 *
 * Example usage:
 * url: [wpv-search-term param="my-field"]
 *
 */

function wpv_search_term( $attr ) {
	extract(
		shortcode_atts(
			array(
				'param' => 's',
				'separator' => ', '
			),
			$attr
		)
	);
	$out = '';
	if ( isset( $_GET[$param] ) ) {
		$term = $_GET[$param];
		if ( is_array( $term ) ) {
			$out = implode( $separator, $term );
		} else {
			$out = $term;
		}
		$out = urldecode( sanitize_text_field( $out ) );
	}
	return $out;
}

/**
* wpv_shortcodes_register_wpv_search_term_data
*
* Register the wpv-search-term shortcode in the GUI API.
*
* @since 1.9
*/

add_filter( 'wpv_filter_wpv_shortcodes_gui_data', 'wpv_shortcodes_register_wpv_search_term_data' );

function wpv_shortcodes_register_wpv_search_term_data( $views_shortcodes ) {
	$views_shortcodes['wpv-search-term'] = array(
		'callback' => 'wpv_shortcodes_get_wpv_search_term_data'
	);
	return $views_shortcodes;
}

function wpv_shortcodes_get_wpv_search_term_data() {
	$data = array(
        'name' => __( 'Search term', 'wpv-views' ),
        'label' => __( 'Search term', 'wpv-views' ),
        'attributes' => array(
            'display-options' => array(
                'label' => __('Display options', 'wpv-views'),
                'header' => __('Display options', 'wpv-views'),
                'fields' => array(
                    'param' => array(
                        'label' => __( 'URL parameter', 'wpv-views'),
                        'type' => 'text',
						'description' => __( 'Watch this URL parameter. Defaults to "s", which is the natural search parameter.', 'wpv-views' ),
						'default' => 's'
                    ),
					'separator' => array(
                        'label' => __( 'Separator when multiple', 'wpv-views'),
                        'type' => 'text',
						'default' => ', ',
						'description' => __( 'When there are more than one values on that URL parameter, display this separator between them.', 'wpv-views' )
                    ),
                ),
            ),
        ),
    );
	return $data;
}

/**
 * Views-Shortcode: wpv-archive-title
 *
 * Description: Display archive title for current type of archive.
 *
 * Parameters: None
 *
 * Example usage:
 * At title of the archive. [wpv-archive-title]
 *
 * Link:
 *
 * Note: Inspired partly by https://developer.wordpress.org/reference/functions/the_archive_title/
 *
 */
function wpv_archive_title( $attr ) {
    $out = '';

    if ( function_exists( 'get_the_archive_title' ) /* WP 4.1+ */ ) {
        $out = get_the_archive_title();
    } else {
        $out = wpv_get_the_archive_title();
    }

    apply_filters( 'wpv_shortcode_debug', 'wpv-archive-title', json_encode( $attr ), '', '', $out );

    return $out;
}

/**
 * Views-Shortcode: wpv-archive-link
 *
 * Description: Display archive link for selected post type.
 *
 * Parameters:
 * 'name' => post_type_name for show (Default = current post type).
 *
 * Example usage:
 * Archive link for places is on [wpv-archive-link name="places"]
 *
 * Link:
 *
 * Note:
 *
 */
function wpv_archive_link($attr){
	extract(
		shortcode_atts( array('name' => ''), $attr )
	);
	$out = '';
	if($name != ''){
		$out  = get_post_type_archive_link($name);
	}
	if($out==''){
		global $post;// @todo check if instaceof Post
		if(isset($post->post_type) and $post->post_type!=''){
			$out = get_post_type_archive_link($post->post_type);
		}
	}
	apply_filters('wpv_shortcode_debug','wpv-archive-link', json_encode($attr), '', '', $out);
	return $out;
}

/**
* wpv_shortcodes_register_wpv_archive_link_data
*
* Register the wpv-archive-link shortcode in the GUI API.
*
* @since 1.9
*/

add_filter( 'wpv_filter_wpv_shortcodes_gui_data', 'wpv_shortcodes_register_wpv_archive_link_data' );

function wpv_shortcodes_register_wpv_archive_link_data( $views_shortcodes ) {
	$views_shortcodes['wpv-archive-link'] = array(
		'callback' => 'wpv_shortcodes_get_wpv_archive_link_data'
	);
	return $views_shortcodes;
}

function wpv_shortcodes_get_wpv_archive_link_data() {
	$options = array(
		'' => __( 'Current post', 'wpv-views' )
	);
	$post_types_with_archive = get_post_types(
		array(
			'public' => true,
			'has_archive' => true
		),
		'objects'
	);
    foreach ( $post_types_with_archive as $post_type_slug => $post_type_data ) {
        $options[$post_type_slug] = $post_type_data->labels->singular_name;
    }
    $data = array(
        'name' => __( 'Link to WordPress archive page', 'wpv-views' ),
        'label' => __( 'Link to WordPress archive page', 'wpv-views' ),
        'attributes' => array(
            'display-options' => array(
                'label' => __('Display options', 'wpv-views'),
                'header' => __('Display options', 'wpv-views'),
                'fields' => array(
                    'name' => array(
                        'label' => __( 'Post type archive', 'wpv-views'),
                        'type' => 'select',
                        'options' => $options,
						'default' => '',
						'description' => __( 'Display the link to the selected post type archive page', 'wpv-views' )
                    ),
                ),
            ),
        ),
    );
    return $data;
}

/**
 * Views-Shortcode: wpv-current-user
 *
 * Description: Display information about current user.
 *
 * Parameters:
 * 'info' => parameter for show.
 *   "display_name" displays user's display name (Default)
 *   "login" displays user's login
 *   "firstname" displays user's first name
 *   "lastname" displays user's last name
 *   "email" displays user's email
 *   "id" displays user's user_id
 *   "logged_in" displays true if user is logged in, false if not
 *   "role" displays user's role
 *
 * Example usage:
 * Current user is [wpv-current-user info="display_name"]
 *
 * Link:
 *
 * Note:
 *
 */

function wpv_current_user($attr){
	global $current_user;
	extract(
		shortcode_atts( array('info' => 'display_name'), $attr )
	);
	$out = '';

	if ( $current_user->ID > 0 ) {
		switch ($info) {
			case 'login':
				$out = $current_user->user_login;
				break;
			case 'firstname':
				$out = $current_user->user_firstname;
				break;
			case 'lastname':
				$out = $current_user->user_lastname;
				break;
			case 'email':
				$out = $current_user->user_email;
				break;
			case 'id':
				$out = $current_user->ID;
				break;
			case 'display_name':
				$out = $current_user->display_name;
				break;
			case 'logged_in':
				$out = 'true';
				break;
		case 'role':
				$out = $current_user->roles[0];
				break;
			default:
				$out = $current_user->display_name;
				break;
		}
	} else {
	switch ($info) {
		case 'logged_in':
		$out = 'false';
		break;
		default:
		$out = '';
		break;
	}
	}
	apply_filters('wpv_shortcode_debug','wpv-current-user', json_encode($attr), '', 'Data received from cache', $out);
	return $out;
}

/**
* wpv_shortcodes_register_wpv_current_user_data
*
* Register the wpv-current-user shortcode in the GUI API.
*
* @since 1.9
*/

add_filter( 'wpv_filter_wpv_shortcodes_gui_data', 'wpv_shortcodes_register_wpv_current_user_data' );

function wpv_shortcodes_register_wpv_current_user_data( $views_shortcodes ) {
	$views_shortcodes['wpv-current-user'] = array(
		'callback' => 'wpv_shortcodes_get_wpv_current_user_data'
	);
	return $views_shortcodes;
}

function wpv_shortcodes_get_wpv_current_user_data() {
    $data = array(
        'name' => __( 'Current user information', 'wpv-views' ),
        'label' => __( 'Current user information', 'wpv-views' ),
        'attributes' => array(
            'display-options' => array(
                'label' => __('Display options', 'wpv-views'),
                'header' => __('Display options', 'wpv-views'),
                'fields' => array(
                    'info' => array(
                        'label' => __( 'Information', 'wpv-views'),
                        'type' => 'radio',
                        'options' => array(
                            'display_name' => __('Display name', 'wpv-views'),
                            'email' => __('Email', 'wpv-views'),
                            'firstname' => __('First name', 'wpv-views'),
							'lastname' => __('Last name', 'wpv-views'),
                            'id' => __('User ID', 'wpv-views'),
                            'logged_in' => __('Logged in', 'wpv-views'),
                            'role' => __('User role', 'wpv-views'),
                        ),
                        'default' => 'display_name',
						'description' => __( 'Display the selected information for the current user', 'wpv-views' ),
						'documentation' => '<a href="http://codex.wordpress.org/Function_Reference/get_userdata" target="_blank">' . __( 'WordPress get_userdata function', 'wpv-views' ) . '</a>'
                    ),
                ),
            ),
        ),
    );
    return $data;
}

/**
 * Views-Shortcode: wpv-login-form
 *
 * Description: Display WordPress login form.
 *
 * Parameters:
 *  "redirect_url" redirects to this URL after successful login. Absolute URL.
 *  "allow_remember" displays the "Remember me" feature (checkbox)
 *  "remember_default" sets "allow_remember" checked status by default
 *
 * Example usage:
 *  [wpv-if evaluate="[wpv-current-user info="logged_in"]" condition="true"]
 *  [/wpv-if]
 *  [wpv-login-form]
 *
 * Link:
 *
 * Note:
 *  Façade for http://codex.wordpress.org/Function_Reference/wp_login_form
 */
function wpv_shortcode_wpv_login_form( $atts ) {
    global $current_user;
    if((int)$current_user->ID > 0) {
        /* Do not display anything if a user is already logged in */
        return '';
    }

    // WordPress gets the current URL this way
    $current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );

    extract( shortcode_atts(
                    array(
        'redirect_url' => $current_url,
        'allow_remember' => false,
        'remember_default' => false,
                    ), $atts )
    );

    $args = array(
        'echo' => false,
        'redirect' => $redirect_url, /* Use absolute URLs */
        'remember' => $allow_remember,
        'value_remember' => $remember_default
    );

    $out = wp_login_form( $args );
    apply_filters( 'wpv_shortcode_debug', 'wpv-login-form', json_encode( $atts ), '', '', $out );
    return $out;
}

/**
* wpv_shortcodes_register_wpv_login_form_data
*
* Register the wpv-login-form shortcode in the GUI API.
*
* @since 1.9
*/

add_filter( 'wpv_filter_wpv_shortcodes_gui_data', 'wpv_shortcodes_register_wpv_login_form_data' );

function wpv_shortcodes_register_wpv_login_form_data( $views_shortcodes ) {
	$views_shortcodes['wpv-login-form'] = array(
		'callback' => 'wpv_shortcodes_get_wpv_login_form_data'
	);
	return $views_shortcodes;
}

function wpv_shortcodes_get_wpv_login_form_data()  {
    $data = array(
        'name' => __( 'Login Form', 'wpv-views' ),
        'label' => __( 'Login Form', 'wpv-views' ),
        'attributes' => array(
            'display-options' => array(
                'label' => __('Display options', 'wpv-views'),
                'header' => __('Display options', 'wpv-views'),
                'fields' => array(
                    'redirect_url' => array(
                        'label' => __( 'Redirect target URL', 'wpv-views'),
                        'type' => 'url',
						'description' => __( 'URL to redirect users after login in. Defaults to the current URL.', 'wpv-views' ),
                    ),
                    'allow_remember' => array(
                        'label' => __( 'Remember me checkbox', 'wpv-views'),
                        'type' => 'radio',
                        'options' => array(
                            'true' => __('Show', 'wpv-views'),
                            'false' => __('Hide', 'wpv-views'),
                        ),
                        'default' => 'false',
						'description' => __( 'Show or hide the checkbox for remembering the user.', 'wpv-views' )
                    ),
                    'remember_default' => array(
                        'label' => __( 'Remember me checkbox default state', 'wpv-views'),
                        'type' => 'radio',
                        'options' => array(
                            'true' => __('Checked', 'wpv-views'),
                            'false' => __('Unchecked', 'wpv-views'),
                        ),
                        'default' => 'false',
						'description' => __( 'Check the checkbox for remembering the user in case it is shown.', 'wpv-views' )
                    ),
                ),
            ),
        ),
    );
    return $data;
}

/**
 * Views-Shortcode: wpv-user
 *
 * Description: Display information for user from the user.
 *
 * Parameters:
 * 'field' => field_key

 *
 * Example usage:
 * Current user is [wpv-user name="custom_name"]
 * specified ID [wpv-user name="custom_name" id="1"]
 *
 * Link:
 *
 * Note:
 *
 */

function wpv_user( $attr ) {

	extract(
		shortcode_atts( 
			array(
			'field' => 'display_name',
			'id' => ''
			), 
			$attr 
		)
	);
	//Get data for specified ID
	if ( 
		isset( $id ) 
		&& ! empty( $id )
	) {
		if ( is_numeric( $id ) ) {
			$data = get_user_by( 'id', $id );
			if ( $data ) {
				$user_id = $id;
				if ( isset( $data->data ) ) {
					$data = $data->data;
					$meta = get_user_meta( $id );
				} else {
					return;
				}
			} else {
				return;
			}
		} else {
			return;
		}
	} else {
		global $WP_Views;
		if ( 
			isset( $WP_Views->users_data['term']->ID ) 
			&& ! empty( $WP_Views->users_data['term']->ID ) 
		) {
			$user_id = $WP_Views->users_data['term']->ID;
			$data = $WP_Views->users_data['term']->data;
			$meta = $WP_Views->users_data['term']->meta;
		} else {
			global $current_user;
			if ( $current_user->ID > 0 ) {
				$user_id = $current_user->ID;
				$data = new WP_User( $user_id );
				if ( isset( $data->data ) ) {
					$data = $data->data;
					$meta = get_user_meta( $user_id );
				} else {
					return;
				}
			} else {
				return;
			}
		}
	}
	$out = '';
	switch ( $field ) {
		case 'display_name':
			$out = $data->$field;
			break;
		case 'user_login':
			$out = $data->$field;
			break;
		case 'first_name':
		case 'user_firstname':
			if ( isset( $meta['first_name']) ){
				$out = $meta['first_name'][0];
			}
			break;
		case 'last_name':
		case 'user_lastname':
			if ( isset( $meta['last_name']) ){
				$out = $meta['last_name'][0];
			}
			break;
		case 'nickname':
			if ( isset( $meta['nickname']) ){
				$out = $meta['nickname'][0];
			}
			break;
		case 'email':
		case 'user_email':
			$field = 'user_email';
			$out = $data->$field;
			break;
		case 'user_url':
			$out = $data->$field;
			break;
		case 'user_registered':
			$out = $data->$field;
			break;
		case 'user_status':
			$out = $data->$field;
			break;
		case 'spam':
			$out = $data->$field;
			break;
		case 'user_id':
		case 'ID':
			$out = $user_id;
			break;
		default:
			if ( isset( $meta[$field] ) ) {
				$out = $meta[$field][0];
			}
			break;
	}
	apply_filters( 'wpv_shortcode_debug','wpv-user', json_encode( $attr ), '', 'Data received from $WP_Views object', $out );
	return $out;
}

/**
* wpv_shortcodes_register_wpv_user_data
*
* Register the wpv-user shortcode in the GUI API.
*
* @since 1.9
*/

add_filter( 'wpv_filter_wpv_shortcodes_gui_data', 'wpv_shortcodes_register_wpv_user_data' );

function wpv_shortcodes_register_wpv_user_data( $views_shortcodes ) {
	$views_shortcodes['wpv-user'] = array(
		'callback' => 'wpv_shortcodes_get_wpv_user_data'
	);
	return $views_shortcodes;
}

function wpv_shortcodes_get_wpv_user_data() {
    $data = array(
        'name'				=> __( 'User data', 'wpv-views' ),
        'label'				=> __( 'User data', 'wpv-views' ),
        'user-selection'	=> true
    );
    return $data;
}

/**
 * Views-Shortcode: wpv-post-id
 *
 * Description: Display the current post's ID
 *
 * Parameters:
 * This takes no parameters.
 *
 * Example usage:
 * ID is [wpv-post-id]
 *
 * Link:
 *
 * Note:
 *
 */

function wpv_shortcode_wpv_post_id($atts){
	$post_id_atts = new WPV_wpcf_switch_post_from_attr_id($atts);

	extract(
		shortcode_atts( array(), $atts )
	);
	$out = '';

	global $post;

	if(!empty($post)){
		$out .= $post->ID;
	}
	apply_filters('wpv_shortcode_debug','wpv-post-id', json_encode($atts), '', 'Data received from cache', $out);
	return $out;
}

/**
* wpv_shortcodes_register_wpv_post_id_data
*
* Register the wpv-post-id shortcode in the GUI API.
*
* @since 1.9
*/


add_filter( 'wpv_filter_wpv_shortcodes_gui_data', 'wpv_shortcodes_register_wpv_post_id_data' );

function wpv_shortcodes_register_wpv_post_id_data( $views_shortcodes ) {
	$views_shortcodes['wpv-post-id'] = array(
		'callback' => 'wpv_shortcodes_get_wpv_post_id_data'
	);
	return $views_shortcodes;
}

function wpv_shortcodes_get_wpv_post_id_data() {
    $data = array(
        'name' => __( 'Post ID', 'wpv-views' ),
        'label' => __( 'Post ID', 'wpv-views' ),
        'post-selection' => true,
    );
    return $data;
}

/**
 * Views-Shortcode: wpv-post-slug
 *
 * Description: Display the current post's slug
 *
 * Parameters:
 * This takes no parameters.
 *
 * Example usage:
 * The slug is [wpv-post-slug]
 *
 * Link:
 *
 * Note:
 *
 */

function wpv_shortcode_wpv_post_slug($atts){
	$post_id_atts = new WPV_wpcf_switch_post_from_attr_id($atts);

	extract(
		shortcode_atts( array(), $atts )
	);
	$out = '';

	global $post;

	if(!empty($post)){
		$out .= $post->post_name;
	}
	apply_filters('wpv_shortcode_debug','wpv-post-slug', json_encode($atts), '', 'Data received from cache', $out);
	return $out;
}

/**
* wpv_shortcodes_register_wpv_post_slug_data
*
* Register the wpv-post-slug shortcode in the GUI API.
*
* @since 1.9
*/

add_filter( 'wpv_filter_wpv_shortcodes_gui_data', 'wpv_shortcodes_register_wpv_post_slug_data' );

function wpv_shortcodes_register_wpv_post_slug_data( $views_shortcodes ) {
	$views_shortcodes['wpv-post-slug'] = array(
		'callback' => 'wpv_shortcodes_get_wpv_post_slug_data'
	);
	return $views_shortcodes;
}

function wpv_shortcodes_get_wpv_post_slug_data() {
    $data = array(
        'name' => __( 'Post slug', 'wpv-views' ),
        'label' => __( 'Post slug', 'wpv-views' ),
        'post-selection' => true,
    );
    return $data;
}

/**
 * Views-Shortcode: wpv-post-title
 *
 * Description: Display the current post's title
 *
 * Parameters:
 * This takes no parameters.
 *
 * Example usage:
 * You are reading [wpv-post-title]
 *
 * Link:
 *
 * Note:
 *
 */

function wpv_shortcode_wpv_post_title($atts){
	$post_id_atts = new WPV_wpcf_switch_post_from_attr_id($atts);

	extract(
		shortcode_atts( array(
			'output' => 'raw'
		), $atts )
	);

	$out = '';

	global $post;

	if(!empty($post)){

		$out .= apply_filters('the_title', $post->post_title);
	}

	// If output="sanitize" then strip tags, escape attributes and replace brackets
	if ( $output == 'sanitize' ) {
		$out = sanitize_text_field( $out );
	//	$out = esc_attr( strip_tags( $out ) );
		$brackets_before = array( '[', ']', '<', '>' );
		$brackets_after = array( '&#91;', '&#93;', '&lt;', '&gt;' );
		$out = str_replace( $brackets_before, $brackets_after, $out );
	}

	apply_filters('wpv_shortcode_debug','wpv-post-title', json_encode($atts), '', 'Data received from cache', $out);
	return $out;
}

/**
* wpv_shortcodes_register_wpv_post_title_data
*
* Register the wpv-post-title shortcode in the GUI API.
*
* @since 1.9
*/

add_filter( 'wpv_filter_wpv_shortcodes_gui_data', 'wpv_shortcodes_register_wpv_post_title_data' );

function wpv_shortcodes_register_wpv_post_title_data( $views_shortcodes ) {
	$views_shortcodes['wpv-post-title'] = array(
		'callback' => 'wpv_shortcodes_get_wpv_post_title_data'
	);
	return $views_shortcodes;
}

function wpv_shortcodes_get_wpv_post_title_data() {
    $data = array(
        'name' => __( 'Post title', 'wpv-views' ),
        'label' => __( 'Post title', 'wpv-views' ),
        'post-selection' => true,
        'attributes' => array(
            'display-options' => array(
                'label' => __('Display options', 'wpv-views'),
                'header' => __('Display options', 'wpv-views'),
                'fields' => array(
                    'output' => array(
                        'label' => __( 'Output format', 'wpv-views'),
                        'type' => 'radio',
                        'options' => array(
                            'raw' => __('As stored in the database', 'wpv-views'),
                            'sanitize' => __('Sanitize', 'wpv-views'),
                        ),
                        'default' => 'raw',
						'description' => __('Output the post title as is or sanitize it to use as an HTML attribute.','wpv-views'),
                    ),
                ),
            ),
        ),
    );
    return $data;
}


/**
 * Views-Shortcode: wpv-post-link
 *
 * Description: Display the current post's title as a link to the post
 *
 * Parameters:
 * This takes no parameters.
 *
 * Example usage:
 * Permalink to [wpv-post-link]
 *
 * Link:
 *
 * Note:
 *
 */

function wpv_shortcode_wpv_post_link($atts){
	$post_id_atts = new WPV_wpcf_switch_post_from_attr_id($atts);

	extract(
		shortcode_atts( array(
            'style' => '',
            'class' => ''
            ), $atts )
	);

	$out = '';

	global $post;

	if(!empty($post)){

		$post_id = $post->ID;
		// Adjust for WPML support
		// If WPML is enabled, $post_id should contain the right ID for the current post in the current language
		// However, if using the id attribute, we might need to adjust it to the translated post for the given ID
		$post_id = apply_filters( 'translate_object_id', $post_id, $post->post_type, true, null );

		$post_link = wpv_get_post_permalink( $post_id );

        if ( ! empty( $style ) ) {
            $style = ' style="'. esc_attr( $style ) .'"';
        }
        if ( ! empty( $class ) ) {
            $class = ' class="' . esc_attr( $class ) .'"';
        }

		$out .= '<a href="' . $post_link . '"'. $class . $style .'>';
		$out .= apply_filters('the_title', $post->post_title);
		$out .= '</a>';
		apply_filters('wpv_shortcode_debug','wpv-post-link', json_encode($atts), '', 'Filter the_title applied', $out);

	}


	return $out;
}

/**
* wpv_shortcodes_register_wpv_post_link_data
*
* Register the wpv-post-link shortcode in the GUI API.
*
* @since 1.9
*/

add_filter( 'wpv_filter_wpv_shortcodes_gui_data', 'wpv_shortcodes_register_wpv_post_link_data' );

function wpv_shortcodes_register_wpv_post_link_data( $views_shortcodes ) {
	$views_shortcodes['wpv-post-link'] = array(
		'callback' => 'wpv_shortcodes_get_wpv_post_link_data'
	);
	return $views_shortcodes;
}

function wpv_shortcodes_get_wpv_post_link_data() {
    $data = array(
        'name' => __( 'Post link', 'wpv-views' ),
        'label' => __( 'Post link', 'wpv-views' ),
        'post-selection' => true,
        'attributes' => array(
            'display-options' => array(
                'label' => __('Display options', 'wpv-views'),
                'header' => __('Display options', 'wpv-views'),
                'fields' => array(
					'class' => array(
                        'label' => __( 'Class', 'wpv-views'),
                        'type' => 'text',
                        'description' => __( 'Space-separated list of classnames that will be added to the anchor HTML tag.', 'wpv-views' ),
                        'placeholder' => 'class1 class2',
                    ),
					'style' => array(
                        'label' => __( 'Style', 'wpv-views'),
                        'type' => 'text',
						'description' => __( 'Inline styles that will be added to the anchor HTML tag.', 'wpv-views' ),
                        'placeholder' => 'border: 1px solid red; font-size: 2em;',
                    ),
                ),
            ),
        ),
    );
    return $data;
}


/**
 * Get permalink for given post with respect to it's status.
 *
 * Appends "preview=true" argument to the permalink for drafts and pending posts. In all other aspects it behaves
 * exactly like get_permalink().
 *
 * @since 1.7
 *
 * @see http://codex.wordpress.org/Function_Reference/get_permalink
 * @see https://icanlocalize.basecamphq.com/projects/7393061-toolset/todo_items/190442712/comments#comment_296475746
 *
 * @todo Add support for custom post types.
 *
 * @param int $post_id ID of an existing post.
 *
 * @return The permalink URL or false on failure.
 */
function wpv_get_post_permalink( $post_id ) {

	$post_link = get_permalink( $post_id );
	if( false == $post_link ) {
		return false;
	}

	$post_status = get_post_status( $post_id );

	switch( $post_status ) {

		case 'draft':
		case 'pending':
			// append preview=true argument to permalink
			$post_link = esc_url( add_query_arg( array( 'preview' => 'true' ), $post_link ) );
			break;

		default: // also when get_post_status fails and returns false, which should never happen
			// do nothing
			break;
	}

	return $post_link;
}


/**
 * Views-Shortcode: wpv-post-body
 *
 * Description: Display the content of the current post
 *
 * Parameters:
 * 'view_template' => The name of a Content template to use when displaying the post content.
 * 'suppress_filters' => Returns the post body with just the natural WordPress filters applied
 * 'output' => [ normal | raw | inherit ] The format of the output when view_template="None": with wpautop, without wpautop or inherited from the parent Template when aplicable
 *
 * Example usage:
 * [wpv-post-body view_template="None"]
 *
 * Link:
 *
 * Note:
 *
 */
function wpv_shortcode_wpv_post_body($atts){
	$post_id_atts = new WPV_wpcf_switch_post_from_attr_id($atts);

	extract(
		shortcode_atts( array(
			'view_template' => 'None',
			'output' => 'normal'
		), $atts )
	);
	$old_override = null;
	$out = '';
	global $post;

	if (
		! is_object( $post )
		|| empty( $post )
	) {
		return $out;
	}

	if ( post_password_required( $post ) ) {
		$post_protected_password_form = get_the_password_form( $post );

		/**
		* Filter wpv_filter_post_protected_body
		*
		* @param (string) $post_protected_password_form The default WordPress password form
		* @param (object) $post The post object to which this shortcode is related to
		* @param (array) $atts The array of attributes passed to this shortcode
		*
		* @return (string)
		*
		* @since 1.7.0
		*/

		return apply_filters( 'wpv_filter_post_protected_body', $post_protected_password_form, $post, $atts );
	}

	global $WPV_templates, $WPVDebug;

	static $stop_infinite_loop_keys;

	if ( isset( $atts['suppress_filters'] ) && ( $atts['suppress_filters'] == 'true' ) ) {
		$suppress_filters = true;
	} else {
		$suppress_filters = false;
	}

	$id = '';

	if (isset($atts['view_template'])) {
		if (isset($post->view_template_override) && $post->view_template_override != '') {
			$old_override = $post->view_template_override;
		}
		$post->view_template_override = $atts['view_template'];
		$id = $post->view_template_override;
	}
	if ( strtolower( $id ) == 'none' ) {
		$ct_id = $id;
		$output_mode = $output;
	} else {
		$ct_id = $WPV_templates->get_template_id( $id );
		$output_mode = 'normal';
	}
	$WPVDebug->wpv_debug_start( $ct_id, $atts, 'content-template' );
	$WPVDebug->set_index();
	if ( $WPVDebug->user_can_debug() ) {
		global $WP_Views;
		$current_item_type = 'posts';
		$view_settings = $WP_Views->get_view_settings();
		if ( isset( $view_settings['view-query-mode'] ) && $view_settings['view-query-mode'] == 'normal' &&
			isset( $view_settings['query_type'] ) && isset( $view_settings['query_type'][0] ) && $view_settings['query_type'][0] != 'posts' ) {
			$current_item_type = $view_settings['query_type'][0]; // taxonomy or users
		}
		switch( $current_item_type ) {
			case 'posts':
				$WPVDebug->add_log( 'content-template', $post );
				break;
			case 'taxonomy':
				$WPVDebug->add_log( 'content-template', $WP_Views->taxonomy_data['term'] );
				break;
			case 'users':
				$WPVDebug->add_log( 'content-template', $WP_Views->users_data['term'] );
				break;
		}
	}
	if ( !empty( $post ) && isset( $post->post_type ) && $post->post_type != 'view' && $post->post_type != 'view-template' ) {

		// Set the output mode for this shortcode (based on the "output" attribute if the "view_template" attribute is set to None, the selected Template output mode will override this otherwise)
		// normal (default) - restore wpautop, only needed if has been previously removed
		// raw - remove wpautop and set the $wpautop_was_active to true
		// inherit - when used inside a Content Template, inherit its wpautop setting; when used outside a Template, inherit from the post itself (so add format, just like "normal")
		// NOTE BUG: we need to first remove_wpautop because for some reason not doing so switches the global $post to the top_current_page one
		$wpautop_was_removed = $WPV_templates->is_wpautop_removed();
		$wpautop_was_active = false;
		$WPV_templates->remove_wpautop();

		if ( $wpautop_was_removed ) { // if we had disabled wpautop, we only need to enable it again for mode "normal" in view_template="None" (will be overriden by Template settings if needed)
			if ( $output_mode == 'normal' ) {
				$WPV_templates->restore_wpautop('');
			}
		} else { // if wpautop was not disabled, we need to revert its state, but just for modes "normal" and "inherit"; we will enable it globally again after the main procedure
			$wpautop_was_active = true;
			if ( $output_mode == 'normal' || $output_mode == 'inherit' ) {
				$WPV_templates->restore_wpautop('');
			}
		}

		// Remove the icl language switcher to stop WPML from add the
		// "This post is avaiable in XXXX" twice.
		// Keep this: we need to know that this needs to be restored by the flag $icl_filter_removed
		global $icl_language_switcher;
		$icl_filter_removed = false;
		if ( isset( $icl_language_switcher ) ) {
			$icl_filter_removed = remove_filter( 'the_content', array( $icl_language_switcher, 'post_availability' ), 100 );
		}

		// Check for infinite loops where a View template contains a
		// wpv-post-body shortcode without a View template specified
		// or a View template refers to itself directly or indirectly.
		$key = (string)$post->ID;
		if (isset($post->view_template_override)) {
			$key .= $post->view_template_override;
		}

		if (!isset($stop_infinite_loop_keys[$key])) {
			$stop_infinite_loop_keys[$key] = 1;

		if ( $suppress_filters ) {

			/**
			* wpv_filter_wpv_the_content_suppressed
			*
			* Mimics the the_content filter on wpv-post-body shortcodes with attribute suppress_filters="true"
			* Check WPV_template::init()
			*
			* Since 1.8.0
			*/

			$out .= apply_filters( 'wpv_filter_wpv_the_content_suppressed', $post->post_content );

		} else {
			$filter_state = new WPV_WP_filter_state( 'the_content' );
			$out .= apply_filters('the_content', $post->post_content);
			$filter_state->restore( );
		}

			unset($stop_infinite_loop_keys[$key]);
		} else {
			$out .= $post->post_content;
		}

		if ($icl_filter_removed) {
			// TODO this might not be needed anymore as we are restoring all the filters below
			add_filter('the_content', array($icl_language_switcher, 'post_availability'), 100);
		}

		// Restore the wpautop configuration only if is has been changed
		if ( $wpautop_was_removed ) {
			$WPV_templates->remove_wpautop();
		} else if ( $wpautop_was_active ) {
			$WPV_templates->restore_wpautop('');
		}
	}

	if (isset($post->view_template_override)) {
		if ($old_override) {
			$post->view_template_override = $old_override;
		} else {
			unset($post->view_template_override);
		}
	}

	$WPVDebug->add_log_item( 'output', $out );
	$WPVDebug->wpv_debug_end();

	apply_filters('wpv_shortcode_debug','wpv-post-body', json_encode($atts), '', 'Output shown in the Nested elements section');
	return $out;
}

/**
* wpv_shortcodes_register_wpv_post_body_data
*
* Register the wpv-post-body shortcode in the GUI API.
*
* @since 1.9
*/
add_filter( 'wpv_filter_wpv_shortcodes_gui_data', 'wpv_shortcodes_register_wpv_post_body_data' );

function wpv_shortcodes_register_wpv_post_body_data( $views_shortcodes ) {
	$views_shortcodes['wpv-post-body'] = array(
		'callback' => 'wpv_shortcodes_get_wpv_post_body_data'
	);
	return $views_shortcodes;
}

function wpv_shortcodes_get_wpv_post_body_data() {
	global $wpdb;
	global $wpdb, $sitepress;
	$values_to_prepare = array();
	$wpml_join = $wpml_where = "";
	if (
		isset( $sitepress ) 
		&& function_exists( 'icl_object_id' )
	) {
		$content_templates_translatable = $sitepress->is_translated_post_type( 'view-template' );
		if ( $content_templates_translatable ) {
			$wpml_current_language = $sitepress->get_current_language();
			$wpml_join = " JOIN {$wpdb->prefix}icl_translations t ";
			$wpml_where = " AND p.ID = t.element_id AND t.language_code = %s ";
			$values_to_prepare[] = $wpml_current_language;
		}
	}
	
	$exclude_loop_templates = '';
	$exclude_loop_templates_ids = wpv_get_loop_content_template_ids();
	// Be sure not to include the current CT when editing one
	if ( isset( $_REQUEST['wpv_suggest_wpv_post_body_view_template_exclude'] ) ) {
		$exclude_loop_templates_ids[] = $_REQUEST['wpv_suggest_wpv_post_body_view_template_exclude'];
	}
	if (
		isset( $_GET['page'] )
		&& 'ct-editor' == $_GET['page'] 
		&& isset( $_GET['ct_id'] )
	) {
		$exclude_loop_templates_ids[] = $_GET['ct_id'];
	}
	if ( count( $exclude_loop_templates_ids ) > 0 ) {
		$exclude_loop_templates_ids_sanitized = array_map( 'esc_attr', $exclude_loop_templates_ids );
		$exclude_loop_templates_ids_sanitized = array_map( 'trim', $exclude_loop_templates_ids_sanitized );
		// is_numeric + intval does sanitization
		$exclude_loop_templates_ids_sanitized = array_filter( $exclude_loop_templates_ids_sanitized, 'is_numeric' );
		$exclude_loop_templates_ids_sanitized = array_map( 'intval', $exclude_loop_templates_ids_sanitized );
		if ( count( $exclude_loop_templates_ids_sanitized ) > 0 ) {
			$exclude_loop_templates = " AND p.ID NOT IN ('" . implode( "','" , $exclude_loop_templates_ids_sanitized ) . "') ";
		}
	}
	$values_to_prepare[] = 'view-template';
	$view_tempates_available = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT p.ID, p.post_name, p.post_title 
			FROM {$wpdb->posts} p {$wpml_join} 
			WHERE p.post_status = 'publish' 
			{$wpml_where} 
			AND p.post_type = %s 
			{$exclude_loop_templates}
			ORDER BY p.post_title 
			LIMIT 16",
			$values_to_prepare
		)
	);
	if ( count( $view_tempates_available ) > 15 ) {
		$custom_combo_settings = array(
			'label' => __('Display using a Content Template:', 'wpv-views'),
			'type' => 'suggest',
			'action' => 'wpv_suggest_wpv_post_body_view_template',
			'required' => true,
			'placeholder' => __( 'Start typing', 'wpv-views' ),
		);
	} else {
		$options = array(
			'' => __( 'Select one Content Template', 'wpv-views' )
		);
		foreach ( $view_tempates_available as $row ) {
			$options[esc_js($row->post_title)] = esc_html( $row->post_title );
		}
		$custom_combo_settings = array(
			'label' => __('Display using a Content Template:', 'wpv-views'),
			'type' => 'select',
			'options' => $options,
			'default' => '',
			'required' => true,
		);
	}
    $data = array(
        'name' => __( 'Post body', 'wpv-views' ),
        'label' => __( 'Post body', 'wpv-views' ),
        'post-selection' => true,
        'attributes' => array(
            'display-options' => array(
                'label' => __('Display options', 'wpv-views'),
                'header' => __('Display options', 'wpv-views'),
                'fields' => array(
                    'view_template' => array(
                        'label' => __( 'Content Template to apply', 'wpv-views'),
                        'type' => 'radio',
                        'options' => array(
                            'None' => __('No Content Template (default display for the content)', 'wpv-views'),
                            'custom-combo' => $custom_combo_settings,
                        ),
                        'description' => __( 'Select a Content Template to display its content, referred to the current post.', 'wpv-views' ),
                        'default_force' => 'None',
                    ),
                    'suppress_filters' => array(
                        'label' => __( 'Suppress third-party filters ', 'wpv-views'),
                        'type' => 'radio',
                        'options' => array(
                            'true' => __('Yes', 'wpv-views'),
                            'false' => __('No', 'wpv-views'),
                        ),
                        'default' => 'false',
						'description' => __( 'Avoid applying third-party filter into the output.', 'wpv-views' )
                    ),
					/*
                    'output' => array(
                        'label' => __( 'Output', 'wpv-views'),
                        'type' => 'radio',
                        'options' => array(
                            'normal' => __('normal', 'wpv-views'),
                            'raw' => __('raw', 'wpv-views'),
                            'inherit' => __('inherit', 'wpv-views'),
                        ),
                        'default' => 'normal',
                    ),
					*/
                ),
            ),
        ),
    );
    return $data;
}

/**
 * Views-Shortcode: wpv-post-excerpt
 *
 * Description: Display the excerpt of the current post
 *
 * Parameters:
 * length => the length of the excerpt in chars or words. Default is 252 chars or the excerpt_length defined by the theme. Prior to 1.5.1 this attribute was not applied to manual excerpts.
 * count => [ char | word ] the method used to count the excerpt.  Default is char. Prior to 1.5.1 char was the only option.
 * more => the string to be added to the excerpt if it needs to be trimmed. Default is ' ...' or the excerpt_more defined by the theme. Prior to 1.5.1 the more string was not applied to manual excerpts.
 * format => whether the output should be wp_autop'ed or not
 *
 * Example usage:
 * [wpv-post-excerpt length="150"]
 *
 * Link:
 *
 * Note:
 * If manual excerpt is set then length parameter is ignored - full manual excerpt is displayed
 *
 */

function wpv_shortcode_wpv_post_excerpt($atts){
	$post_id_atts = new WPV_wpcf_switch_post_from_attr_id($atts);

	extract(
		shortcode_atts( array(
			'length' => 0,
			'count' => 'char',
			'more' => null,
			'format' => 'autop'
		), $atts )
	);
	$out = $debug = '';

	global $post;

	if ( post_password_required( $post ) ) {

		/**
		* Filter wpv_filter_post_protected_excerpt
		*
		* @param (string) The default WordPress string returned when displaying the excerpt of a password protected post
		* @param (object) $post The post object to which this shortcode is related to
		* @param (array) $atts The array of attributes passed to this shortcode
		*
		* @return (string)
		*
		* @since 1.7.0
		*/

		return apply_filters( 'wpv_filter_post_protected_excerpt', __( 'There is no excerpt because this is a protected post.', 'wpv-views' ), $post, $atts );
	}

	global $WPV_templates;

	if(!empty($post) && $post->post_type != 'view' && $post->post_type != 'view-template'){

		// verify if displaying the real excerpt field or part of the content one
		$display_real_excerpt = false;
		if ( empty($post->post_excerpt) ) {
			$excerpt = $post->post_content;
		} else {
			$excerpt = $post->post_excerpt;
			$display_real_excerpt = true;
		}
		$excerpt = str_replace(']]>', ']]&gt;', $excerpt);

		if ( $length > 0 ) {
			$excerpt_length = $length;
		} else {
			if ( $display_real_excerpt ) {
				$excerpt_length = strlen( $excerpt ); // don't cut manually inserted excerpts if there is no length attribute
			} else {
				$excerpt_length = apply_filters('excerpt_length', 252); // on automatically created excerpts, apply the core excerpt_length filter
			}
		}
		if ( is_null( $more ) ) {
			$excerpt_more = apply_filters('excerpt_more', ' ' . '...'); // when no more attribute is used, apply the core excerpt_more filter; it will only be used if the excerpt needs to be trimmed
		} else {
			$excerpt_more = $more;
		}

		/**
		* Filter wpv_filter_post_excerpt
		*
		* This filter lets you modify the string that will generate the excerpt before it's passed through wpv_do_shortcode() and before the length attribute is applied
		* This way you can parse and delete specific shortcodes from the excerpt, like the [caption] one
		*
		* @param $excerpt the string we will generate the excerpt from (the real $post->excerpt or the $post->content) before stretching and parsing the inner shortcodes
		*
		* @return $excerpt
		*
		* @since 1.5.1
		*/
		$excerpt = apply_filters('wpv_filter_post_excerpt', $excerpt);

		// evaluate shortcodes before truncating tags
		$excerpt = wpv_do_shortcode($excerpt);
		if ( $count == 'word' ) {
			$excerpt = wp_trim_words( $excerpt, $excerpt_length, $excerpt_more );
		} else {
			$excerpt = wp_html_excerpt( $excerpt, $excerpt_length, $excerpt_more );
		}


		$wpautop_was_removed = $WPV_templates->is_wpautop_removed();
		if (
			$wpautop_was_removed
			&& $format == 'autop'
		) {
			$WPV_templates->restore_wpautop('');
		}

		// Remove the Content template excerpt filter. We don't want it applied to this shortcode
		remove_filter('the_excerpt', array($WPV_templates, 'the_excerpt_for_archives'), 1, 1);

		$out .= apply_filters('the_excerpt', $excerpt);

		// restore filter
		add_filter('the_excerpt', array($WPV_templates, 'the_excerpt_for_archives'), 1, 1);

		if ($wpautop_was_removed) {
			$WPV_templates->remove_wpautop();
			$debug = ' Show RAW data.';
		}
	}
	apply_filters('wpv_shortcode_debug','wpv-post-excerpt', json_encode($atts), '', 'Filter the_excerpt applied.' . $debug, $out);
	return $out;
}

/**
* wpv_shortcodes_register_wpv_post_excerpt_data
*
* Register the wpv-post-excerpt shortcode in the GUI API.
*
* @since 1.9
*/

add_filter( 'wpv_filter_wpv_shortcodes_gui_data', 'wpv_shortcodes_register_wpv_post_excerpt_data' );

function wpv_shortcodes_register_wpv_post_excerpt_data( $views_shortcodes ) {
	$views_shortcodes['wpv-post-excerpt'] = array(
		'callback' => 'wpv_shortcodes_get_wpv_post_excerpt_data'
	);
	return $views_shortcodes;
}

function wpv_shortcodes_get_wpv_post_excerpt_data() {
    $data = array(
        'name' => __( 'Post excerpt', 'wpv-views' ),
        'label' => __( 'Post excerpt', 'wpv-views' ),
        'post-selection' => true,
        'attributes' => array(
            'display-options' => array(
                'label' => __('Display options', 'wpv-views'),
                'header' => __('Display options', 'wpv-views'),
                'fields' => array(
                    'length' => array(
                        'label' => __( 'Excerpt length', 'wpv-views'),
                        'type' => 'number',
                        'default' => '',
                        'description' => __('This will shorten the excerpt to a specific length. Leave blank for default.', 'wpv-views'),
                        'placeholder' => __('Enter the excerpt length.', 'wpv-views'),
                    ),
                    'count' => array(
                        'label' => __( 'Count length by', 'wpv-views'),
                        'type' => 'radio',
                        'options' => array(
                            'char' => __('Characters', 'wpv-views'),
                            'word' => __('Words', 'wpv-views'),
                        ),
                        'default' => 'char',
						'description' => __('You can create an excerpt based on the number of words or characters.', 'wpv-views'),
                    ),
                    'more' => array(
                        'label' => __( 'Ellipsis text', 'wpv-views'),
                        'type' => 'text',
						'description' => __('This will be added after the excerpt, as an invitation to keep reading.', 'wpv-views'),
                        'placeholder' => __('Read more...', 'wpv-views'),
                    ),
                ),
            ),
        ),
    );
    return $data;
}


/**
 * Views-Shortcode: wpv-post-author
 *
 * Description: Display the author of the current post
 *
 * Parameters:
 * format => The format of the output.
 *   "name" displays the author's name (Default)
 *   "link" displays the author's name as a link
 *   "url" displays the url for the author
 *   "meta" displays the author meta info
 * meta => The meta field to display when format="meta"
 *
 * Example usage:
 * Posted by [wpv-post-author format="name"]
 *
 * Link:
 * For info about the author meta fields, see <a href="http://codex.wordpress.org/Function_Reference/get_the_author_meta">http://codex.wordpress.org/Function_Reference/get_the_author_meta</a>)
 *
 * Note:
 *
 */

function wpv_shortcode_wpv_post_author($atts) {
	$post_id_atts = new WPV_wpcf_switch_post_from_attr_id($atts);

	extract(
		shortcode_atts(
			array(
				'format' => 'name',
				'meta' => 'nickname'
			),
			$atts
		)
	);

	global $authordata; // TODO check if this global is needed here; when switching posts its useless

	$author_url = esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) );

	switch ( $format ) {
		case 'link':
			$out = '<a href="' . $author_url . '">' . get_the_author() . '</a>';
			break;

		case 'url':
			$out = $author_url;
			break;

		case 'meta':
			$out = get_the_author_meta( $meta );
			break;

		default:
			$out = get_the_author();
			break;

	}
	apply_filters('wpv_shortcode_debug','wpv-post-author', json_encode($atts), '', 'Data received from cache', $out);
	return $out;
}

/**
* wpv_shortcodes_register_wpv_post_author_data
*
* Register the wpv-post-author shortcode in the GUI API.
*
* @since 1.9
*/

add_filter( 'wpv_filter_wpv_shortcodes_gui_data', 'wpv_shortcodes_register_wpv_post_author_data' );

function wpv_shortcodes_register_wpv_post_author_data( $views_shortcodes ) {
	$views_shortcodes['wpv-post-author'] = array(
		'callback' => 'wpv_shortcodes_get_wpv_post_author_data'
	);
	return $views_shortcodes;
}

function wpv_shortcodes_get_wpv_post_author_data() {
    $data = array(
        'name' => __( 'Post author', 'wpv-views' ),
        'label' => __( 'Post author', 'wpv-views' ),
        'post-selection' => true,
        'attributes' => array(
            'display-options' => array(
                'label' => __('Display options', 'wpv-views'),
                'header' => __('Display options', 'wpv-views'),
                'fields' => array(
                    'format' => array(
                        'label' => __( 'Author information', 'wpv-views'),
                        'type' => 'radio',
                        'options' => array(
                            'name' => __('Author name', 'wpv-views'),
                            'link' => __('Author archive link', 'wpv-views'),
                            'url' => __('Author archive URL', 'wpv-views'),
                            'meta' => __('Author metadata', 'wpv-views'),
                        ),
                        'default' => 'name',
						'description' => __( 'Display this information about the current post author.', 'wpv-views' )
                    ),
                    'meta' => array(
                        'label' => __( 'Author metadata', 'wpv-views'),
                        'type' => 'select',
						'default_force' => 'nickname',
                        'options' => array(
							'display_name' => __('Author display name', 'wpv-views'),
							'first_name' => __('Author first name', 'wpv-views'),
                            'last_name' => __('Author last name', 'wpv-views'),
							'nickname' => __('Author nickname', 'wpv-views'),
							'user_nicename' => __('Author nicename', 'wpv-views'),
							'description' => __('Author description', 'wpv-views'),
                            'user_login' => __('Author login', 'wpv-views'),
                            'user_pass' => __('Author password', 'wpv-views'),
							'ID' => __('Author ID', 'wpv-views'),
                            'user_email' => __('Author email', 'wpv-views'),
                            'user_url' => __('Author URL', 'wpv-views'),
                            'user_registered' => __('Author registered date', 'wpv-views'),
                            'user_activation_key' => __('Author activation key', 'wpv-views'),
                            'user_status' => __('Author status', 'wpv-views'),
                            'jabber' => __('Author jabber', 'wpv-views'),
                            'aim' => __('Author aim', 'wpv-views'),
                            'yim' => __('Author yim', 'wpv-views'),
                            'user_level' => __('Author level', 'wpv-views'),
                            'user_firstname' => __('firstname', 'wpv-views'),
                            'user_lastname' => __('lastname', 'wpv-views'),
                            'rich_editing' => __('rich editing', 'wpv-views'),
                            'comment_shortcuts' => __('comment shortcuts', 'wpv-views'),
                            'admin_color' => __('admin_color', 'wpv-views'),
                            'plugins_per_page' => __('plugin per page', 'wpv-views'),
                            'plugins_last_view' => __('plugins last view', 'wpv-views'),

                        ),
						'description' => __( 'Display this metadata if that option was selected on the previous section', 'wpv-views' )
                    ),
                ),
            ),
        ),
    );
    return $data;
}


/**
 * Views-Shortcode: wpv-post-date
 *
 * Description: Display the date of the current post
 *
 * Parameters:
 * format => Format string for the date. Defaults to F jS, Y
 *
 * Example usage:
 * Published on [wpv-post-date format="F jS, Y"]
 *
 * Link:
 * Format parameter is the same as here - <a href="http://codex.wordpress.org/Formatting_Date_and_Time">http://codex.wordpress.org/Formatting_Date_and_Time</a>
 *
 * Note:
 *
 */

function wpv_shortcode_wpv_post_date($atts) {
	$post_id_atts = new WPV_wpcf_switch_post_from_attr_id($atts);

	extract(
		shortcode_atts( array(
			'format' => get_option( 'date_format' )
		), $atts )
	);

	$out = apply_filters('the_time', get_the_time( $format ));
	apply_filters('wpv_shortcode_debug','wpv-post-date', json_encode($atts), '', 'Data received from cache, filter the_time applied', $out);
	return $out;
}

/**
* wpv_shortcodes_register_wpv_post_date_data
*
* Register the wpv-post-date shortcode in the GUI API.
*
* @since 1.9
*/

add_filter( 'wpv_filter_wpv_shortcodes_gui_data', 'wpv_shortcodes_register_wpv_post_date_data' );

function wpv_shortcodes_register_wpv_post_date_data( $views_shortcodes ) {
	$views_shortcodes['wpv-post-date'] = array(
		'callback' => 'wpv_shortcodes_get_wpv_post_date_data'
	);
	return $views_shortcodes;
}

function wpv_shortcodes_get_wpv_post_date_data() {
    $default_format = get_option( 'date_format' );
    $data = array(
        'name' => __( 'Post date', 'wpv-views' ),
        'label' => __( 'Post date', 'wpv-views' ),
        'post-selection' => true,
        'attributes' => array(
            'display-options' => array(
                'label' => __('Display options', 'wpv-views'),
                'header' => __('Display options', 'wpv-views'),
                'fields' => array(
                    'format' => array(
                        'label' => __( 'Date format', 'wpv-views'),
                        'type' => 'radio',
                        'default' => $default_format,
                        'documentation' => '<a href="http://codex.wordpress.org/Formatting_Date_and_Time" target="_blank">' . __( 'WordPress Formatting Date and Time', 'wpv-views' ) . '</a>',
                        'options' => array(
                            $default_format => $default_format . ' - ' . date_i18n( $default_format ),
                            'F j, Y g:i a' => 'F j, Y g:i a - ' . date_i18n( 'F j, Y g:i a' ),
                            'F j, Y' => 'F j, Y - ' . date_i18n( 'F j, Y' ),
                            'd/m/y' => 'd/m/y - ' . date_i18n( 'd/m/y' ),
                            'custom-combo' => array(
                                'label' => __('Custom', 'wpv-views' ),
                                'type' => 'text',
                                'placeholder' => 'l, F j, Y',
                            )
                        ),
                    ),
                ),
            ),
        ),
    );
    return $data;
}


/**
 * Views-Shortcode: wpv-post-url
 *
 * Description: Display the url to the current post
 *
 * Parameters:
 * This takes no parameters.
 *
 * Example usage:
 * [wpv-post-url]
 *
 * Link:
 *
 * Note:
 *
 */

function wpv_shortcode_wpv_post_url($atts) {
	$post_id_atts = new WPV_wpcf_switch_post_from_attr_id($atts);

	extract(
		shortcode_atts( array(), $atts )
	);

	$out = '';

	global $post;

	if( !empty( $post ) ) {

		$post_id = $post->ID;

		// Adjust for WPML support
		// If WPML is enabled, $post_id should contain the right ID for the current post in the current language
		// However, if using the id attribute, we might need to adjust it to the translated post for the given ID
		$post_id = apply_filters( 'translate_object_id', $post_id, $post->post_type, true, null );

		$out = wpv_get_post_permalink( $post_id );

	}

	apply_filters('wpv_shortcode_debug','wpv-post-url', json_encode($atts), '', 'Data received from cache', $out);

	return $out;
}

/**
* wpv_shortcodes_register_wpv_post_url_data
*
* Register the wpv-post-url shortcode in the GUI API.
*
* @since 1.9
*/

add_filter( 'wpv_filter_wpv_shortcodes_gui_data', 'wpv_shortcodes_register_wpv_post_url_data' );

function wpv_shortcodes_register_wpv_post_url_data( $views_shortcodes ) {
	$views_shortcodes['wpv-post-url'] = array(
		'callback' => 'wpv_shortcodes_get_wpv_post_url_data'
	);
	return $views_shortcodes;
}

function wpv_shortcodes_get_wpv_post_url_data() {
    $data = array(
        'name' => __( 'Post URL', 'wpv-views' ),
        'label' => __( 'Post URL', 'wpv-views' ),
        'post-selection' => true,
    );
    return $data;
}

/**
 * Views-Shortcode: wpv-post-type
 *
 * Description: Display the current post type
 *
 * Parameters:
 * 'show' => 'slug', 'single' or 'plural'. Defaults to 'slug'
 *
 * Example usage:
 * [wpv-post-type show="single"]
 *
 * Link:
 *
 * Note:
 *
 */

function wpv_shortcode_wpv_post_type($atts){
	$post_id_atts = new WPV_wpcf_switch_post_from_attr_id($atts);

	extract(
		shortcode_atts( array('show' => 'slug'), $atts )
	);

	$out = '';

	global $post;


	if(!empty($post)){

		$post_object = get_post_type_object($post->post_type);

		if ( !is_null( $post_object ) ) {

		switch ($show) {
			case 'single':
				$out = $post_object->labels->singular_name;
				break;

			case 'plural':
				$out = $post_object->labels->name;
				break;

			case 'slug':
		$rewrite = $post_object->rewrite;
				$out = ( isset( $rewrite ) && isset( $rewrite['slug'] ) ) ? $rewrite['slug'] : $post->post_type;
				break;

			default:
				$out = $post->post_type;
				break;

		}

		}

	}
	apply_filters('wpv_shortcode_debug','wpv-post-type', json_encode($atts), '', 'Data received from cache', $out);
	return $out;
}

/**
* wpv_shortcodes_register_wpv_post_type_data
*
* Register the wpv-post-type shortcode in the GUI API.
*
* @since 1.9
*/

add_filter( 'wpv_filter_wpv_shortcodes_gui_data', 'wpv_shortcodes_register_wpv_post_type_data' );

function wpv_shortcodes_register_wpv_post_type_data( $views_shortcodes ) {
	$views_shortcodes['wpv-post-type'] = array(
		'callback' => 'wpv_shortcodes_get_wpv_post_type_data'
	);
	return $views_shortcodes;
}

function wpv_shortcodes_get_wpv_post_type_data() {
    $data = array(
        'name' => __( 'Post type', 'wpv-views' ),
        'label' => __( 'Post type', 'wpv-views' ),
        'post-selection' => true,
        'attributes' => array(
            'display-options' => array(
                'label' => __('Display options', 'wpv-views'),
                'header' => __('Display options', 'wpv-views'),
                'fields' => array(
                    'show' => array(
                        'label' => __( 'Post type information', 'wpv-views'),
                        'type' => 'radio',
                        'options' => array(
                            'slug' => __('Post type slug', 'wpv-views'),
                            'single' => __('Post type singular name', 'wpv-views'),
                            'plural' => __('Post type plural name', 'wpv-views'),
                        ),
                        'default' => 'slug',
                    ),
                ),
            ),
        ),
    );
    return $data;
}

/**
 * Views-Shortcode: wpv-post-status
 *
 * Description: Display the current post status
 *
 * Parameters:
 *
 * Example usage:
 * This post has a status: [wpv-post-status]
 *
 * Link:
 *
 * Note:
 *
 */

 function wpv_shortcode_wpv_post_status($atts){
	$post_id_atts = new WPV_wpcf_switch_post_from_attr_id($atts);

	$out = '';

	global $post;


	if(!empty($post)){

		$out .= $post->post_status;

	}
	apply_filters('wpv_shortcode_debug','wpv-post-status', json_encode($atts), '', 'Data received from cache', $out);
	return $out;
}

/**
* wpv_shortcodes_register_wpv_post_status_data
*
* Register the wpv-post-status shortcode in the GUI API.
*
* @since 1.9
*/

add_filter( 'wpv_filter_wpv_shortcodes_gui_data', 'wpv_shortcodes_register_wpv_post_status_data' );

function wpv_shortcodes_register_wpv_post_status_data( $views_shortcodes ) {
	$views_shortcodes['wpv-post-status'] = array(
		'callback' => 'wpv_shortcodes_get_wpv_post_status_data'
	);
	return $views_shortcodes;
}

function wpv_shortcodes_get_wpv_post_status_data() {
    $data = array(
        'name' => __( 'Post status', 'wpv-views' ),
        'label' => __( 'Post status', 'wpv-views' ),
        'post-selection' => true,
    );
    return $data;
}

/**
 * Views-Shortcode: wpv-post-class
 *
 * Description: Display a space separated list of the current post classes
 *
 * Parameters:
 * 'add' => a space separated list of classnames to add to the default ones
 *
 * Example usage:
 * {{li class="[wpv-post-class]"}}Content{{/li}}
 *
 * Link:
 *
 * Note:
 *
 */

 function wpv_shortcode_wpv_post_class( $atts ) {
	$post_id_atts = new WPV_wpcf_switch_post_from_attr_id( $atts );

	global $post;

	extract(
		shortcode_atts( array(
			'add'  => ''
		), $atts )
	);

	$post_classes = get_post_class( $add ); // it handles the escaping of the $add classnames
	$out = implode( ' ', $post_classes );

	apply_filters('wpv_shortcode_debug','wpv-post-class', json_encode($atts), '', 'Data received from get_post_class()', $out);
	return $out;
}

/**
* wpv_shortcodes_register_wpv_post_class_data
*
* Register the wpv-post-class shortcode in the GUI API.
*
* @since 1.9
*/

add_filter( 'wpv_filter_wpv_shortcodes_gui_data', 'wpv_shortcodes_register_wpv_post_class_data' );

function wpv_shortcodes_register_wpv_post_class_data( $views_shortcodes ) {
	$views_shortcodes['wpv-post-class'] = array(
		'callback' => 'wpv_shortcodes_get_wpv_post_class_data'
	);
	return $views_shortcodes;
}

function wpv_shortcodes_get_wpv_post_class_data() {
    $data = array(
        'name' => __( 'Post class', 'wpv-views' ),
        'label' => __( 'Post class', 'wpv-views' ),
		'post-selection' => true,
        'attributes' => array(
            'display-options' => array(
                'label' => __('Display options', 'wpv-views'),
                'header' => __('Display options', 'wpv-views'),
                'fields' => array(
                    'add' => array(
                        'label' => __( 'Extra classnames', 'wpv-views'),
                        'type' => 'text',
                        'description' => __('Space-separated list of classnames to be added to the WordPress generated ones.', 'wpv-views'),
                    ),
                ),
            ),
        ),
    );
    return $data;
}

/**
 * Views-Shortcode: wpv-post-format
 *
 * Description: Display the post format (standard|aside|chat|gallery|link|image|quote|status|video|audio|).
 * If post type doesn't support post formats, returns empty string.
 *
 * Parameters:
 * This takes no parameters.
 *
 * Example usage:
 *  [wpv-if evaluate="'[wpv-post-format]' = 'aside'"]
 *      This is aside format
 *  [/wpv-if]
 *
 * Link:
 *
 * Note:
 * This function returns "standard" instead of <tt>false</tt> as <a href="http://codex.wordpress.org/Function_Reference/get_post_format">get_post_format</a> page recommends.
 *
 */
function wpv_shortcode_wpv_post_format( $atts ) {
    $post_id_atts = new WPV_wpcf_switch_post_from_attr_id( $atts );

    extract(
            shortcode_atts( array(), $atts )
    );

    $out = '';
    global $post;
    if ( !empty( $post ) ) {
        $post_format = get_post_format( $post->ID );
        if ( $post_format !== false ) {
            $out = $post_format;
        } else {
            $out = 'standard';
        }
    }

    apply_filters( 'wpv_shortcode_debug', 'wpv-post-format', json_encode( $atts ), '', 'Data received from cache', $out );

    return $out;
}

/**
* wpv_shortcodes_register_wpv_post_format_data
*
* Register the wpv-post-format shortcode in the GUI API.
*
* @since 1.9
*/

add_filter( 'wpv_filter_wpv_shortcodes_gui_data', 'wpv_shortcodes_register_wpv_post_format_data' );

function wpv_shortcodes_register_wpv_post_format_data( $views_shortcodes ) {
	$views_shortcodes['wpv-post-format'] = array(
		'callback' => 'wpv_shortcodes_get_wpv_post_format_data'
	);
	return $views_shortcodes;
}

function wpv_shortcodes_get_wpv_post_format_data() {
    $data = array(
        'name' => __( 'Post format', 'wpv-views' ),
        'label' => __( 'Post format', 'wpv-views' ),
		'post-selection' => true,
    );
    return $data;
}

/**
 * Views-Shortcode: wpv-post-featured-image
 *
 * Description: Display the featured image of the current post
 *
 * Parameters:
 * 'size' => thumbnail|medium|large|full|#custom# - defaults to thumbnail
 * 'output' => img|url|alt|id|author|date|description|title|caption - what to display - if empty, will display the IMG tag for legacy, so defaults to img de facto
 *
 * Legacy prameters:
 * 'raw' => Show url (true) or HTML tag (false) - default to false (HTML tag)
 * 'data' => Show additional image info
 *		  id - attachment ID
 *		  author - attachment author
 *		  date - attachment date
 *		  description - attachment description
 *		  title - attachment title
 *		  caption - attachment title
 *		  original - original size url
 *		  alt - attachment alt
 *
 *
 * Example usage:
 * [wpv-post-featured-image size="medium" raw="false"]
 *
 * Link:
 *
 * Note:
 *
 */

function wpv_shortcode_wpv_post_featured_image( $atts ) {
	global $WPVDebug;
	$post_id_atts = new WPV_wpcf_switch_post_from_attr_id( $atts );
	extract(
		shortcode_atts( array(
			'size'  => 'thumbnail',
			'output' => '',
			'raw'   => 'false',// DEPRECATED
			'data'  => '',// DEPRECATED
			'attr'  => '',
			'class' => ''
		), $atts )
	);
	$out = '';
	$info = array(
		'id' => 'ID',
		'author' => 'post_author',
		'date' => 'post_date',
		'description' => 'post_content',
		'title' => 'post_title',
		'caption' => 'post_excerpt',
		'original' => 'guid'
	);
	// LEGACY - backwards compatibility
	if ( empty( $output ) ) {
		if ( 
			$raw === 'true'  
			|| ! empty( $data ) 
		) {
			if ( empty( $data ) ) {
				$output = 'url';
			} else {
				$output = $data;
			}
		} else {
			$output = 'img';
		}
	}
	// END LEGACY - backwards compatibility
	if ( 'img' == $output ) {
		if ( ! empty( $attr ) ) {
			// first, escape and strip tags
			$attr = esc_attr( strip_tags( $attr ) );
			// now, hack the ampersands on legitimate query-like attributes
			$valid_attr_before = array( '&amp;title', '&#038;title', '&amp;alt', '&#038;alt', '&amp;class', '&#038;class' );
			$valid_attr_after = array( '#wpv-title-hack#', '#wpv-title-hack#', '#wpv-alt-hack#', '#wpv-alt-hack#', '#wpv-class-hack#', '#wpv-class-hack#' );
			$attr = str_replace( $valid_attr_before, $valid_attr_after, $attr );
			// adjust the brackets
			$brackets_before = array( '[', ']', '&amp;', '&#038;' );
			$brackets_after = array( '&#91;', '&#93;', '&', '&' );
			$attr = str_replace( $brackets_before, $brackets_after, $attr );
			// hack the remaining ampersands, even the ones coming from HTML characters
			$before_sanitize = array( '&' );
			$after_sanitize = array( '#wpv-amperhack#' );
			$attr = str_replace( $before_sanitize, $after_sanitize, $attr );
			// add nack the legitimate ampersands
			$attr = str_replace( '#wpv-title-hack#', '&title', $attr );
			$attr = str_replace( '#wpv-alt-hack#', '&alt', $attr );
			$attr = str_replace( '#wpv-class-hack#', '&class', $attr );
			// parse the attributes
			wp_parse_str( $attr, $attr_array );
			// restore the other ampersands
			$attr_array = str_replace( $after_sanitize, $before_sanitize, $attr_array );
		} else {
			$attr_array = array();
		}
		if ( ! empty( $class ) ) {
			$attr_array['class'] = 'attachment-' . esc_attr( $size ) . '  ' . esc_attr( $class );
		}
		$out = get_the_post_thumbnail( null, $size, $attr_array );
		$out = apply_filters( 'wpv-post-featured-image', $out );
	} else {
		$post_thumbnail_id = get_post_thumbnail_id( get_the_ID() );
		if ( !empty( $post_thumbnail_id ) ) {
			switch ( $output ) {
				case 'id':
				case 'author':
				case 'date':
				case 'description':
				case 'title':
				case 'caption':
				case 'original':
					$new_info = get_post( $post_thumbnail_id );
					$new_value = $info[$output];
					if( isset( $new_info->$new_value ) ) {
						$file_info = $new_info->$new_value;
					}
					break;
				case 'alt':
					$file_info = get_post_meta( $post_thumbnail_id , '_wp_attachment_image_alt', true );
					break;
				case 'url':
				default:
					if ( $size == 'full' ) {
						$new_info = get_post( $post_thumbnail_id );
						if( isset( $new_info->guid ) ) {
							$file_info = $new_info->guid;
						}
					} else {
						$out_array = wp_get_attachment_image_src($post_thumbnail_id, $size );
						$file_info = $out_array[0];
					}
					break;
			}
			$out = apply_filters( 'wpv-post-featured-image', $file_info );
		}
	}

	apply_filters('wpv_shortcode_debug','wpv-post-featured-image', json_encode($atts), '', 'Filter wpv-post-featured-image applied', $out);

	return $out;
}

/**
* wpv_shortcodes_register_wpv_post_featured_image_data
*
* Register the wpv-post-featured-image shortcode in the GUI API.
*
* @since 1.9
*/

add_filter( 'wpv_filter_wpv_shortcodes_gui_data', 'wpv_shortcodes_register_wpv_post_featured_image_data' );

function wpv_shortcodes_register_wpv_post_featured_image_data( $views_shortcodes ) {
	$views_shortcodes['wpv-post-featured-image'] = array(
		'callback' => 'wpv_shortcodes_get_wpv_post_featured_image_data'
	);
	return $views_shortcodes;
}

function wpv_shortcodes_get_wpv_post_featured_image_data() {
	$options = array(
		'full' => __('Original image', 'wpv-view')
	);
	$template = '%s - (%dx%d)';
	$defined_sizes = array(
		'thumbnail' => __('Thumbnail', 'wpv-view'),
		'medium' => __('Medium', 'wpv-view'),
		'large' => __('Large', 'wpv-view')
	);
    foreach ( $defined_sizes as $ds_key => $ds_label ) {
        $options[$ds_key] = sprintf(
            $template,
            $ds_label,
            get_option(sprintf('%s_size_w', $ds_key)),
            get_option(sprintf('%s_size_h', $ds_key))
        );
    }
    global $_wp_additional_image_sizes;
    if ( ! empty( $_wp_additional_image_sizes) ) {
		foreach ( $_wp_additional_image_sizes as $key => $value ) {
			if ( 'post-thumbnail' == $key ) {
				continue;
			}
			$options[$key] = sprintf(
				$template,
				$key,
				$value['width'],
				$value['height']
			);
		}
	}
    $data = array(
        'name' => __( 'Post featured image', 'wpv-views' ),
        'label' => __( 'Post featured image', 'wpv-views' ),
        'post-selection' => true,
        'attributes' => array(
            'display-options' => array(
                'label' => __('Display options', 'wpv-views'),
                'header' => __('Display options', 'wpv-views'),
                'fields' => array(
					'size' => array(
                        'label' => __('Featured image size', 'wpv-views'),
                        'type' => 'select',
                        'options' => $options,
                        'default' => 'thumbnail',
                    ),
					'output' => array(
                        'label' => __('What to display', 'wpv-views'),
                        'type' => 'select',
                        'options' => array(
							'img' => __( 'Image HTML tag', 'wpv-views' ),
							'url' => __('URL of the image', 'wpv-view'),
							'title' => __('Title of the image', 'wpv-view'),
							'caption' => __('Caption of the mage', 'wpv-view'),
							'description' => __('Description of the image', 'wpv-view'),
                            'alt' => __('ALT text for the image', 'wpv-view'),
                            'author' => __('Author of the image', 'wpv-view'),
                            'date' => __('Date of the image', 'wpv-view'),
                            'id' => __('ID of the image', 'wpv-view'),
                        ),
                        'default' => 'img',
                    ),
					'class' => array(
                        'label' => __( 'Class', 'wpv-views'),
                        'type' => 'text',
                        'description' => __( 'Space-separated list of classnames that will be added to the image HTML tag.', 'wpv-views' ),
                        'placeholder' => 'class1 class2',
                    ),
					/*
                    'attr' => array(
                        'type' => 'text',
                        'description' => __('Expects a query-string-like value : attr=”title=a&alt=b&classname=c” will add those attributes to the img HTML tag', 'wpv-views'),
                        'label' => __('Attributes', 'wpv-views'),
                    ),
					*/
                ),
            ),
        ),
    );
    return $data;
}

/** This filter is documented in embedded/inc/wpv-shortcodes-gui.php */
// add_filter('wpv_filter_wpv_shortcodes_gui_api_wpv-post-featured-image-size_options', 'wpv_post_featured_image_size_options', 10, 2);

/**
 * Add post featured-image options to shortcode
 *
 * Add post featured-image shortcode options to shortcode attribute.
 *
 * @since 1.9.0
 *
 * @param array $options 
 *
 */
function wpv_post_featured_image_size_options($options, $type = 'text')
{
    $mask = '%s - (%dx%d)';
    if ( 'radio' == $type ) {
        $mask = '%s <small>(%dx%d)</small>';
    }
    /**
     * first add size to label
     */
    foreach ( array('thumbnail', 'medium', 'large') as $key ) {
        $options[$key] = sprintf(
            $mask,
            $options[$key],
            get_option(sprintf('%s_size_w', $key)),
            get_option(sprintf('%s_size_h', $key))
        );
    }
    global $_wp_additional_image_sizes;
    if ( empty( $_wp_additional_image_sizes) ) {
        return $options;
    }
    foreach ( $_wp_additional_image_sizes as $key => $value ) {
        if ( 'post-thumbnail' == $key ) {
            continue;
        }
        $options[$key] = sprintf(
            $mask,
            preg_replace('/[_-]+/', ' ', $key),
            $value['width'],
            $value['height']
        );
    }
    return $options;
}

/**
* Views-Shortcode: wpv-post-edit-link
*
* Description: Display an edit link for the current post
*
* Parameters:
* label: Optional. What to show in the edit link. ie: 'Edit Video' DEPRECATED
* text: Optional
* style: Optional
* class: Optional
*
* Example usage:
* [wpv-post-edit-link]
*
* Link:
*
* Note:
*
*/
function wpv_shortcode_wpv_post_edit_link( $atts ){
	$post_id_atts = new WPV_wpcf_switch_post_from_attr_id( $atts );

	extract(
		shortcode_atts( 
			array(
				'style' => '',
				'class' => '',
				'text' => ''
            ),
			$atts 
		)
	);

	$out = '';
	global $post;

	if ( 
		! empty( $post ) 
		&& current_user_can( 'edit_posts' ) 
	) {
        if ( ! empty( $style ) ) {
            $style = ' style="'. esc_attr( $style ) .'"';
        }
        if ( ! empty( $class ) ) {
            $class = ' ' . esc_attr( $class );
        }
		$anchor_text = '';
		if ( isset( $atts['label'] ) ) {
			$anchor_text = sprintf( __( 'Edit %s', 'wpv-views' ), $atts['label'] );
		} else {
			if ( empty( $text ) ) {
				$anchor_text = __('Edit This', 'wpv-views');
			} else {
				$anchor_text = $text;
			}
		}
		$out .= '<a href="' . get_edit_post_link( $post->ID ) . '" class="post-edit-link'. $class .'"'. $style .'>';
		$out .= $anchor_text;
		$out .= '</a>';
	}
	apply_filters('wpv_shortcode_debug','wpv-post-edit-link', json_encode($atts), '', 'Data received from cache', $out);
	return $out;
}

/**
* wpv_shortcodes_register_wpv_post_edit_link_data
*
* Register the wpv-post-edit-link shortcode in the GUI API.
*
* @since 1.9
*/

add_filter( 'wpv_filter_wpv_shortcodes_gui_data', 'wpv_shortcodes_register_wpv_post_edit_link_data' );

function wpv_shortcodes_register_wpv_post_edit_link_data( $views_shortcodes ) {
	$views_shortcodes['wpv-post-edit-link'] = array(
		'callback' => 'wpv_shortcodes_get_wpv_post_edit_link_data'
	);
	return $views_shortcodes;
}

function wpv_shortcodes_get_wpv_post_edit_link_data() {
    $data = array(
        'name' => __( 'Post edit link', 'wpv-views' ),
        'label' => __( 'Post edit link', 'wpv-views' ),
        'post-selection' => true,
        'attributes' => array(
            'display-options' => array(
                'label' => __('Display options', 'wpv-views'),
                'header' => __('Display options', 'wpv-views'),
                'fields' => array(
                    'text' => array(
                        'label' => __( 'Edit link text', 'wpv-views'),
                        'type' => 'text',
                        'description' => __('Set the text for the link. Defaults to "Edit This".', 'wpv-views'),
                    ),
					'class' => array(
                        'label' => __( 'Class', 'wpv-views'),
                        'type' => 'text',
                        'description' => __( 'Space-separated list of classnames that will be added to the anchor HTML tag.', 'wpv-views' ),
                        'placeholder' => 'class1 class2',
                    ),
					'style' => array(
                        'label' => __( 'Style', 'wpv-views'),
                        'type' => 'text',
						'description' => __( 'Inline styles that will be added to the anchor HTML tag.', 'wpv-views' ),
                        'placeholder' => 'border: 1px solid red; font-size: 2em;',
                    ),
                ),
            ),
        ),
    );
    return $data;
}


/**
 * Views-Shortcode: wpv-post-field
 *
 * Description: Display a custom field of the current post. This displays
 * the raw data from the field. Use the Types plugin the and the [types] shortcode
 * to display formatted fields.
 *
 * Parameters:
 * 'name' => The name of the custom field to display
 * 'index' => The array index to use if the meta key has multiple values. If index is not set then all values will be output
 * 'separator' => The separator between multiple values. Defaults to ', '
 *
 * Example usage:
 * [wpv-post-field name="customfield"]
 *
 * Link:
 *
 * Note:
 *
 */

function wpv_shortcode_wpv_post_field($atts) {
	$post_id_atts = new WPV_wpcf_switch_post_from_attr_id($atts);

	extract(
		shortcode_atts(
			array(
				'index' => '',
				'name' => '',
				'separator' => ', '
			),
			$atts
		)
	);

	$out = '';
	$filters = '';
	global $post;

	if(!empty($post)){
		$meta = get_post_meta($post->ID, $name);

		$meta = apply_filters('wpv-post-field-meta-' . $name, $meta);
		$filters .= 'Filter wpv-post-field-meta-' . $name .' applied. ';
		if ($meta) {

			if ($index !== '') {
				$index = intval($index);
				$filters .= 'displaying index ' . $index . '. ';
				$out .= $meta[$index];
			} else {
				$filters .= 'no index set. ';
				foreach($meta as $item) {
					if ($out != '') {
						$out .= $separator;
					}
					$out .= $item;
				}

			}
		}
	}

	$out = apply_filters('wpv-post-field-' . $name, $out, $meta);
	$filters .= 'Filter wpv-post-field-' . $name . ' applied. ';
	apply_filters('wpv_shortcode_debug','wpv-post-field', json_encode($atts), '', 'Data received from cache. '. $filters, $out);
	return $out;
}

/**
* wpv_shortcodes_register_wpv_post_field_data
*
* Register the wpv-post-field shortcode in the GUI API.
*
* @since 1.9
*/

add_filter( 'wpv_filter_wpv_shortcodes_gui_data', 'wpv_shortcodes_register_wpv_post_field_data' );

function wpv_shortcodes_register_wpv_post_field_data( $views_shortcodes ) {
	$views_shortcodes['wpv-post-field'] = array(
		'callback' => 'wpv_shortcodes_get_wpv_post_field_data'
	);
	return $views_shortcodes;
}

function wpv_shortcodes_get_wpv_post_field_data() {
    $data = array(
        'name' => __( 'Post field', 'wpv-views' ),
        'label' => __( 'Post field', 'wpv-views' ),
        'post-selection' => true,
        'attributes' => array(
            'display-options' => array(
                'label' => __('Display options', 'wpv-views'),
                'header' => __('Display options', 'wpv-views'),
                'fields' => array(
                    'name' => array(
                        'label' => __('Custom field', 'wpv-views'),
                        'type' => 'suggest',
						'action' => 'wpv_suggest_wpv_post_field_name',
                        'description' => __('The name of the custom field to display', 'wpv-views'),
                        'required' => true,
                    ),
                    'index' => array(
                        'label' => __( 'Index', 'wpv-views'),
                        'type' => 'number',
                        'description' => __('The index to use if the custom field has multiple values. If an index is not set then all values will be output.', 'wpv-views'),
                    ),
                    'separator' => array(
                        'type' => 'text',
                        'label' => __('Separator', 'wpv-views'),
                        'description' => __('The separator between multiple values.','wpv-views'),
                        'default' => ', ',
                    ),
                ),
            ),
        ),
    );
    return $data;
}

/**
 * Views-Shortcode: wpv-post-comments-number
 *
 * Description: Displays the number of comments for the current post
 *
 * Parameters:
 * 'none' => Text if there are no comments. Default - "No Comments"
 * 'one'  => Text if there is only one comment. Default - "1 Comment"
 * 'more' => Text if there is more than one comment. Default "% Comments"
 *
 * Example usage:
 * This post has [wpv-post-comments-number none="No Comments" one="1 Comment" more="% Comments"]
 *
 * Link:
 *
 * Note:
 *
 */

function wpv_shortcode_wpv_comments_number($atts) {
	$post_id_atts = new WPV_wpcf_switch_post_from_attr_id($atts);
	global $WPVDebug, $post;

	if ( function_exists('icl_t') )
	{
		if( isset($atts['none']) )
		{
			icl_register_string('plugin Views', 'No comments-'.md5($atts['none']), $atts['none'] );
			$atts['none'] = icl_t('plugin Views', 'No comments-'.md5($atts['none']), $atts['none'] );
		}
		if( isset($atts['one']) )
		{
			icl_register_string('plugin Views', 'One comment-'.md5($atts['one']), $atts['one'] );
			$atts['one'] = icl_t('plugin Views', 'One comment-'.md5($atts['one']), $atts['one'] );
		}
		if( isset($atts['more']) )
		{
			icl_register_string('plugin Views', 'More comments-'.md5($atts['more']), $atts['more']);
			$atts['more'] = icl_t('plugin Views', 'More comments-'.md5($atts['more']), $atts['more'] );
		}
	}

	extract(
		shortcode_atts(
			array(
				'none' => __('No Comments', 'wpv-views'),
				'one' => __('1 Comment', 'wpv-views'),
				'more' => __('% Comments', 'wpv-views')
			),
			$atts
		)
	);

	ob_start();

	wp_count_comments($post->ID);

	comments_number($none, $one, $more);

	$out = ob_get_clean();
	apply_filters('wpv_shortcode_debug','wpv-post-comments-number', json_encode($atts), $WPVDebug->get_mysql_last(), 'Data received from cache', $out);
	return $out;
}

/**
* wpv_shortcodes_register_wpv_post_comments_number_data
*
* Register the wpv-post-comments-number shortcode in the GUI API.
*
* @since 1.9
*/

add_filter( 'wpv_filter_wpv_shortcodes_gui_data', 'wpv_shortcodes_register_wpv_post_comments_number_data' );

function wpv_shortcodes_register_wpv_post_comments_number_data( $views_shortcodes ) {
	$views_shortcodes['wpv-post-comments-number'] = array(
		'callback' => 'wpv_shortcodes_get_wpv_post_comments_number_data'
	);
	return $views_shortcodes;
}

function wpv_shortcodes_get_wpv_post_comments_number_data() {
    $data = array(
        'name' => __( 'Post comments number', 'wpv-views' ),
        'label' => __( 'Post comments number', 'wpv-views' ),
        'post-selection' => true,
        'attributes' => array(
            'display-options' => array(
                'label' => __('Display options', 'wpv-views'),
                'header' => __('Display options', 'wpv-views'),
                'fields' => array(
                    'none' => array(
                        'label' => __( 'Text to display when there are no comments', 'wpv-views'),
                        'type' => 'text',
                        'default' => __('No Comments', 'wpv-view'),
                    ),
                    'one' => array(
                        'label' => __( 'Text to display when there is one comment', 'wpv-views'),
                        'type' => 'text',
                        'default' => __('1 Comment', 'wpv-view'),
                    ),
                    'more' => array(
                        'label' => __( 'Text to display when there is more than one comment', 'wpv-views'),
                        'type' => 'text',
                        'default' => __('% Comments', 'wpv-view'),
						'description' => __( '%s - the number of comments', 'wpv-views' )
                    ),
                ),
            ),
        ),
    );
    return $data;
}

function wpv_shortcode_wpv_comment_title($atts){
	$post_id_atts = new WPV_wpcf_switch_post_from_attr_id($atts);


}

function wpv_shortcode_wpv_comment_body($atts){
	$post_id_atts = new WPV_wpcf_switch_post_from_attr_id($atts);


}

function wpv_shortcode_wpv_comment_author($atts){
	$post_id_atts = new WPV_wpcf_switch_post_from_attr_id($atts);


}

function wpv_shortcode_wpv_comment_date($atts){
	$post_id_atts = new WPV_wpcf_switch_post_from_attr_id($atts);


}

/**
 * Views-Shortcode: wpv-taxonomy-title
 *
 * Description: Display the taxonomy title as a plain text
 *
 * Parameters:
 * This takes no parameters.
 *
 * Example usage:
 * [wpv-taxonomy-title]
 *
 * Link:
 *
 * Note:
 *
 */

function wpv_shortcode_wpv_tax_title($atts){

	global $WP_Views;
	$out = '';
	$term = $WP_Views->get_current_taxonomy_term();

	if ($term) {
	   $out = $term->name;
	}
	apply_filters('wpv_shortcode_debug','wpv-taxonomy-title', json_encode($atts), '', 'Data received from $WP_Views object.', $out);
	return $out;
}

/**
 * Views-Shortcode: wpv-taxonomy-link
 *
 * Description: Display the taxonomy title within a link
 *
 * Parameters:
 * This takes no parameters.
 *
 * Example usage:
 * [wpv-taxonomy-link]
 *
 * Link:
 *
 * Note:
 *
 */


function wpv_shortcode_wpv_tax_title_link($atts){

	global $WP_Views;
	$out = '';
	$term = $WP_Views->get_current_taxonomy_term();

	if ($term) {
		$out = '<a href="' . get_term_link($term) . '">' . $term->name . '</a>';
	}
	apply_filters('wpv_shortcode_debug','wpv-taxonomy-link', json_encode($atts), '', 'Data received from $WP_Views object.', $out);
	return $out;
}


/**
 * Views-Shortcode: wpv-taxonomy-slug
 *
 * Description: Display the taxonomy slug
 *
 * Parameters:
 * This takes no parameters.
 *
 * Example usage:
 * [wpv-taxonomy-slug]
 *
 * Link:
 *
 * Note:
 *
 */
function wpv_shortcode_wpv_tax_slug($atts){

	global $WP_Views;
	$out = '';
	$term = $WP_Views->get_current_taxonomy_term();

	if ($term) {
		$out = $term->slug;
	}

	apply_filters('wpv_shortcode_debug','wpv-taxonomy-slug', json_encode($atts), '', 'Data received from $WP_Views object.', $out);
	return $out;

}

/**
 * Views-Shortcode: wpv-taxonomy-id
 *
 * Description: Display the taxonomy term ID
 *
 * Parameters:
 * This takes no parameters.
 *
 * Example usage:
 * [wpv-taxonomy-id]
 *
 * Link:
 *
 * Note:
 *
 */
function wpv_shortcode_wpv_tax_id($atts){

	global $WP_Views;
	$out = '';
	$term = $WP_Views->get_current_taxonomy_term();

	if ( $term ) {
		$out = $term->term_id;
	}

	apply_filters('wpv_shortcode_debug','wpv-taxonomy-id', json_encode($atts), '', 'Data received from $WP_Views object.', $out);
	return $out;

}

/**
 * Views-Shortcode: wpv-taxonomy-url
 *
 * Description: Display the taxonomy URL as a plain text (not embedded in a HTML link)
 *
 * Parameters:
 * This takes no parameters.
 *
 * Example usage:
 * [wpv-taxonomy-url]
 *
 * Link:
 *
 * Note:
 *
 */

function wpv_shortcode_wpv_tax_url($atts){

	global $WP_Views;
	$out= '';
	$term = $WP_Views->get_current_taxonomy_term();

	if ($term) {
		$out = get_term_link($term);
	}
	apply_filters('wpv_shortcode_debug','wpv-taxonomy-url', json_encode($atts), '', 'Data received from $WP_Views object.', $out);
	return $out;
}


/**
 * Views-Shortcode: wpv-taxonomy-description
 *
 * Description: Display the taxonomy description text
 *
 * Parameters:
 * This takes no parameters.
 *
 * Example usage:
 * [wpv-taxonomy-description]
 *
 * Link:
 *
 * Note:
 *
 */

function wpv_shortcode_wpv_tax_description($atts){

	global $WP_Views;
	$out = '';
	$term = $WP_Views->get_current_taxonomy_term();

	if ($term) {
		$out = $term->description;
	}
	apply_filters('wpv_shortcode_debug','wpv-taxonomy-description', json_encode($atts), '', 'Data received from $WP_Views object.', $out);
	return $out;
}


/**
 * Views-Shortcode: wpv-taxonomy-post-count
 *
 * Description: Display the number of posts in a taxonomy
 *
 * Parameters:
 * This takes no parameters.
 *
 * Example usage:
 * [wpv-taxonomy-post-count]
 *
 * Link:
 *
 * Note:
 *
 */

function wpv_shortcode_wpv_tax_items_count($atts){
	global $WP_Views;
	$out = '';
	$term = $WP_Views->get_current_taxonomy_term();

	if ($term) {
		$out = $term->count;
	}
	apply_filters('wpv_shortcode_debug','wpv-taxonomy-post-count', json_encode($atts), '', 'Data received from $WP_Views object.', $out);
	return $out;
}

/**
 * Views-Shortcode: wpv-taxonomy-archive
 *
 * Description: Display info for current taxonomy page.
 *
 * Parameters:
 * 'info' =>
 *		  'name' - taxonomy term name (default)
 *		  'slug' - taxonomy term slug
 *		  'description' - taxonomy term description
 *		  'id' - taxonomy term ID
 *		  'taxonomy' - taxonomy
 *		  'parent' - taxonomy term parent
 *		  'count' - total posts with this taxonomy term
 *
 * Example usage:
 * Archive for [wpv-taxonomy-archive info="name"]
 *
 * Link:
 *
 * Note:
 *
 */

function wpv_shortcode_wpv_taxonomy_archive($atts){
	global $WP_Views,$cat, $term;

	$queried_object = get_queried_object();
	if ( !isset($queried_object->term_taxonomy_id) ){
		return;
	}
	$info = '';
	if ( isset($atts['info']) ){
		$info = $atts['info'];
	}
	$out = '';
	if ( empty($info) || $info === 'name' ){
		$out = $queried_object->name;
	}
	if ( $info === 'slug' ){
		$out = $queried_object->slug;
	}
	if ( $info === 'description' ){
		$out = $queried_object->description;
	}
	if ( $info === 'id' ){
		$out = $queried_object->term_taxonomy_id;
	}
	if ( $info === 'taxonomy' ){
		$out = $queried_object->taxonomy;
	}
	if ( $info === 'parent' ){
		$out = $queried_object->parent;
	}
	if ( $info === 'count' ){
		$out = $queried_object->count;
	}
	apply_filters('wpv_shortcode_debug','wpv-taxonomy-archive', json_encode($atts), '', 'Data received from cache.', $out);
	return $out;
}

/**
*
* Add the short codes to javascript so they can be added to the post visual editor toolbar.
*
* $types contains the type of items to add to the toolbar
*
* 	'post' means all wpv-post shortcodes but wpv-post-field in the Basic section.
*	'post-fields-grouped' means non-Types custom fields in the Post field section.
*	'post-extended' means wpv-post-field and wpv-for-each shortcodes in the Basic section.
* 
* 	@important To be used only on native post edit screens:
*		'types-post' means Types custom fields and usermeta fields in their own groups.
*		'types-post-usermeta' means Types usermeta in their own groups.
* 	@important Note that for generic Types items, you can use the wpcf_filter_force_include_types_fields_on_views_dialog filter.
*
* 	'user' means all wpv-user shortcodes with a UserID selector
* 
* 	'body-view-templates' means a CT section listing all available CT.
*
* 	'view' means all available Views. DEPRECATED, use the other ones.
* 	'user-view' means Views listing users.
* 	'taxonomy-view' means Views listing terms.
* 	'post-view' means Views listing posts.
*
* 	'archives' means all WPAs - where is this being used? Nowhere!!
*
* 	'wpml' means some WPML-related shortcodes.
*/

function add_short_codes_to_js( $types, $editor ) {

	global $wpv_shortcodes, $wpdb, $WP_Views, $sitepress;
	$views_shortcodes_with_api_obj = apply_filters('wpv_filter_wpv_shortcodes_gui_data', array());
	$views_shortcodes_with_api = array_keys( $views_shortcodes_with_api_obj );

	$index = 0;
	$nonce = wp_create_nonce('wpv_editor_callback');
	foreach( $wpv_shortcodes as $shortcode ) {

		if ( in_array( $shortcode[0], $views_shortcodes_with_api ) ) {
			$shortcode[3] = "WPViews.shortcodes_gui.wpv_insert_popup('" . $shortcode[0] . "', '" . $shortcode[1] . "', {}, '" . $nonce . "', this )";
		}

		if (
			in_array( 'post', $types ) // Add the wpv-post shortcodes plus non-Types custom fields
			&& strpos( $shortcode[0], 'wpv-post-' ) === 0
			&& $shortcode[0] != 'wpv-post-field'
			&& function_exists( $shortcode[2] )
		) {
			// All wpv-post-*** shortcodes but wpv-post-field
			if ( isset( $shortcode[3] ) ) {
				$editor->add_insert_shortcode_menu($shortcode[1], $shortcode[0], __('Basic', 'wpv-views'), $shortcode[3]);
			} else {
				$editor->add_insert_shortcode_menu($shortcode[1], $shortcode[0], __('Basic', 'wpv-views'));
			}
			$index += 1;
		}
	}
		
	if ( in_array( 'post-fields-placeholder', $types ) ) {
		$menu = __( 'Post field', 'wpv-views' );
		$editor->add_insert_shortcode_menu(
			'<i class="icon-plus-sign"></i> ' . __( 'Load non-Types custom fields', 'wpv-views' ),
			'',
			$menu,
			"WPViews.shortcodes_gui.load_post_field_section_on_demand( event, this )"
		);
		$index += 1;
	}
	
	if ( in_array( 'post-fields-grouped', $types ) ) {
		$cf_keys = $WP_Views->get_meta_keys();
		foreach ( $cf_keys as $cf_key ) {
			if ( ! wpv_is_types_custom_field( $cf_key ) ) {
				// add to the javascript array (text, function, sub-menu)
				$function_name = 'wpv_field_' . $index;
				$menu = __( 'Post field', 'wpv-views' );
				$editor->add_insert_shortcode_menu(
					$cf_key,
					'wpv-post-field name="' . $cf_key . '"',
					$menu
				);
				$index += 1;
			}
		}
	}

	if ( in_array( 'post-extended', $types ) ) {
		// Add the wpv-post-field just in case
		$editor->add_insert_shortcode_menu(
			__('Post field', 'wpv-views'),
			'wpv-post-field',
			__('Basic', 'wpv-views'),
			"WPViews.shortcodes_gui.wpv_insert_popup('wpv-post-field', '" . __( 'Post field', 'wpv-views' ) . "', {}, '" . $nonce . "', this )"
		);
		$index += 1;
		// Add the wpv-for-each iterator
		$editor->add_insert_shortcode_menu(
			__('Post field iterator', 'wpv-views'),
			'wpv-for-each',
			__('Basic', 'wpv-views'),
			"WPViews.shortcodes_gui.wpv_insert_popup('wpv-for-each', '" . __( 'Post field iterator', 'wpv-views' ) . "', {}, '" . $nonce . "', this )"
		);
		$index += 1;
	}
	
	/*
	* Note that the following two actions only have callbacks from Types on post.php and post-new.php
	* On Views and WPA edit screens, postmeta and usermeta items are added automatically by Types without needing to call them
	*/
	if ( in_array( 'types-post', $types ) ) {
		do_action( 'wpv_action_wpv_add_types_postmeta_to_editor', $editor );
	}
	if ( in_array( 'types-post-usermeta', $types ) ) {
		do_action( 'wpv_action_wpv_add_types_post_usermeta_to_editor', $editor );
	}
	
	if ( in_array( 'user', $types ) ) {
		$user_shortcodes = array(
			'ID'			=> array(
				'label'	=> __('User ID', 'wpv-views'),
				'code'	=> 'wpv-user field="ID"'
			),
			'user_email'		=> array(
				'label'	=> __('User Email', 'wpv-views'),
				'code'	=> 'wpv-user field="user_email"'
			),
			'user_login'		=> array(
				'label'	=> __('User Login', 'wpv-views'),
				'code'	=> 'wpv-user field="user_login"'
			),
			'user_firstname'	=> array(
				'label'	=> __('First Name', 'wpv-views'),
				'code'	=> 'wpv-user field="user_firstname"'
			),
			'user_lastname'		=> array(
				'label'	=> __('Last Name', 'wpv-views'),
				'code'	=> 'wpv-user field="user_lastname"'
			),
			'nickname'			=> array(
				'label'	=> __('Nickname', 'wpv-views'),
				'code'	=> 'wpv-user field="nickname"'
			),
			'display_name'		=> array(
				'label'	=> __('Display Name', 'wpv-views'),
				'code'	=> 'wpv-user field="display_name"'
			),
			'description'		=> array(
				'label'	=> __('Description', 'wpv-views'),
				'code'	=> 'wpv-user field="description"'
			),
			'yim'				=> array(
				'label'	=> __('Yahoo IM', 'wpv-views'),
				'code'	=> 'wpv-user field="yim"'
			),
			'jabber'			=> array(
				'label'	=> __('Jabber', 'wpv-views'),
				'code'	=> 'wpv-user field="jabber"'
			),
			'aim'				=> array(
				'label'	=> __('AIM', 'wpv-views'),
				'code'	=> 'wpv-user field="aim"'
			),
			'user_url'			=> array(
				'label'	=> __('User URL', 'wpv-views'),
				'code'	=> 'wpv-user field="user_url"'
			),
			'user_registered'	=> array(
				'label'	=> __('Registration Date', 'wpv-views'),
				'code'	=> 'wpv-user field="user_registered"'
			),
			'user_status'		=> array(
				'label'	=> __('User Status', 'wpv-views'),
				'code'	=> 'wpv-user field="user_status"'
			),
			'spam'				=> array(
				'label'	=> __('User Spam Status', 'wpv-views'),
				'code'	=> 'wpv-user field="spam"'
			),
		);
		foreach ( $user_shortcodes as $u_shortcode_slug => $u_shortcode_data ) {
			$editor->add_insert_shortcode_menu(
				$u_shortcode_data['label'],
				$u_shortcode_data['code'],
				__( 'User basic data', 'wpv-views' ),
				"WPViews.shortcodes_gui.wpv_insert_popup('wpv-user', '" . esc_js( $u_shortcode_data['label'] ) . "', {field:{value:'" . $u_shortcode_slug . "',label:'". esc_js( $u_shortcode_data['label'] ) . "'}}, '" . $nonce . "', this )"
			);
			$index += 1;
		}
	}

	// Content Templates
	if ( in_array( 'body-view-templates', $types ) ) {
		$ct_objects = get_transient( 'wpv_transient_ct_published' );
		if ( $ct_objects === false ) {
			global $pagenow;
			$values_to_prepare = array();
			$values_to_prepare[] = 'view-template';
			$values_to_exclude = array();
			$values_to_exclude_string = '';
			if (
				in_array( $pagenow, array( 'post.php' ) )
				&& isset( $_GET["post"] )
				&& is_numeric( $_GET["post"] )
			) {
				$values_to_exclude[] = (int) $_GET["post"];
			}
			if ( 
				isset( $_GET["page"] ) 
				&& 'ct-editor' == $_GET["page"]
				&& isset( $_GET["ct_id"] )
				&& is_numeric( $_GET["ct_id"] )
			) {
				$values_to_exclude[] = (int) $_GET["ct_id"];
			}
			$exclude_loop_templates_ids = wpv_get_loop_content_template_ids();
			if ( count( $exclude_loop_templates_ids ) > 0 ) {
				$exclude_loop_templates_ids_sanitized = array_map( 'esc_attr', $exclude_loop_templates_ids );
				$exclude_loop_templates_ids_sanitized = array_map( 'trim', $exclude_loop_templates_ids_sanitized );
				// is_numeric + intval does sanitization
				$exclude_loop_templates_ids_sanitized = array_filter( $exclude_loop_templates_ids_sanitized, 'is_numeric' );
				$exclude_loop_templates_ids_sanitized = array_map( 'intval', $exclude_loop_templates_ids_sanitized );
				if ( count( $exclude_loop_templates_ids_sanitized ) > 0 ) {
					$values_to_exclude = array_merge( $values_to_exclude, $exclude_loop_templates_ids_sanitized );
				}
			}
			if ( count( $values_to_exclude ) > 0 ) {
				$values_to_exclude_string = " AND ID NOT IN ('" . implode( "','" , $values_to_exclude ) . "') ";
			}
			$view_template_available = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT ID, post_title FROM {$wpdb->posts}
					WHERE post_type = %s
					AND post_status in ('publish')
					{$values_to_exclude_string}",
					$values_to_prepare
				)
			);
			set_transient( 'wpv_transient_ct_published', $view_template_available, WEEK_IN_SECONDS );
		} else {
			$view_template_available = $ct_objects;
		}
		if ( is_array( $view_template_available ) ) {
			foreach ( $view_template_available as $view_template ) {
				$editor->add_insert_shortcode_menu( esc_html( $view_template->post_title ), 'wpv-post-body view_template="' . esc_html( $view_template->post_title ) . '"', __('Content Template', 'wpv-views'));
				$index += 1;
			}
		}
	}

	if (
		in_array( 'view', $types )
		|| in_array( 'archives', $types )
		|| in_array( 'user-view', $types )
		|| in_array( 'taxonomy-view', $types )
		|| in_array( 'post-view', $types )
	) {
		$views_objects = get_transient( 'wpv_transient_view_published' );
		$views_objects_transient_to_update = array(
			'archive' => array(),
			'posts' => array(),
			'taxonomy' => array(),
			'users' => array()
		);
		global $pagenow;
		$current_id = 0;
		if ( in_array( $pagenow, array( 'post.php' ) ) && isset( $_GET["post"] ) ) {
			$current_id = (int) $_GET["post"];
		} else if ( in_array( $pagenow, array( 'post-new.php' ) ) ) {
			global $post;
			$current_id = $post->ID;
		}
		if ( $views_objects === false ) {
			$view_available = $wpdb->get_results(
				"SELECT ID, post_title FROM {$wpdb->posts}
				WHERE post_type='view'
				AND post_status in ('publish')"
			);
			foreach ( $view_available as $view ) {
				if ( $WP_Views->is_archive_view( $view->ID ) ) {
					// Archive Views - add only if in_array( 'archives', $types )
					if ( in_array( 'archives', $types ) ) {
						$editor->add_insert_shortcode_menu( esc_html( $view->post_title ), 'wpv-view name="' . esc_html( $view->post_title ) . '"', __( 'Archive', 'wpv-views' ) );
						$index += 1;
					}
					$views_objects_transient_to_update['archive'][] = $view;
				} else {
					$view_settings = get_post_meta( $view->ID, '_wpv_settings', true );
					$view_type = 'posts';
					if ( isset( $view_settings['query_type'][0] ) ) {
						$view_type = $view_settings['query_type'][0];
					}
					if ( 
						in_array( 'user-view', $types ) 
						&& $view_type == 'users'
					) {
						// Add Views listing users
						$editor->add_insert_shortcode_menu(
							esc_html( $view->post_title ),
							'wpv-view name="' . esc_html( $view->post_title ) . '"',
							__( 'User View', 'wpv-views' ),
							'WPViews.shortcodes_gui.wpv_insert_view_shortcode_dialog(\'' . $view->ID . '\', \'' . esc_js( $view->post_title ) . '\', \'' . $current_id . '\', \'' . $nonce . '\')'
						);
						$index += 1;
					}
					if ( 
						in_array( 'taxonomy-view', $types ) 
						&& $view_type == 'taxonomy'
					) {
						// Add Views listing taxonomies
						$editor->add_insert_shortcode_menu(
							esc_html( $view->post_title ),
							'wpv-view name="' . esc_html( $view->post_title ) . '"',
							__( 'Taxonomy View', 'wpv-views' ),
							'WPViews.shortcodes_gui.wpv_insert_view_shortcode_dialog(\'' . $view->ID . '\', \'' . esc_js( $view->post_title ) . '\', \'' . $current_id . '\', \'' . $nonce . '\')'
						);
						$index += 1;
					}
					if ( 
						in_array( 'post-view', $types ) 
						&& $view_type == 'posts'
					) {
						// Add Views listing posts
						$editor->add_insert_shortcode_menu(
							esc_html( $view->post_title ),
							'wpv-view name="' . esc_html( $view->post_title ) . '"',
							__( 'Post View', 'wpv-views' ),
							'WPViews.shortcodes_gui.wpv_insert_view_shortcode_dialog(\'' . $view->ID . '\', \'' . esc_js( $view->post_title ) . '\', \'' . $current_id . '\', \'' . $nonce . '\')'
						);
					}
					$views_objects_transient_to_update[$view_type][] = $view;
				}
			}
			set_transient( 'wpv_transient_view_published', $views_objects_transient_to_update, WEEK_IN_SECONDS );
		} else {
			if ( 
				in_array( 'archives', $types ) 
				&& isset( $views_objects['archive'] )
				&& is_array( $views_objects['archive'] )
			) {
				foreach ( $views_objects['archive'] as $view ) {
					$editor->add_insert_shortcode_menu( esc_html( $view->post_title ), 'wpv-view name="' . esc_html( $view->post_title ) . '"', __( 'Archive', 'wpv-views' ) );
					$index += 1;
				}
			}
			if ( 
				in_array( 'post-view', $types ) 
				&& isset( $views_objects['posts'] )
				&& is_array( $views_objects['posts'] )
			) {
				foreach ( $views_objects['posts'] as $view ) {
					$editor->add_insert_shortcode_menu(
						esc_html( $view->post_title ),
						'wpv-view name="' . esc_html( $view->post_title ) . '"',
						__( 'Post View', 'wpv-views' ),
						'WPViews.shortcodes_gui.wpv_insert_view_shortcode_dialog(\'' . $view->ID . '\', \'' . esc_js( $view->post_title ) . '\', \'' . $current_id . '\', \'' . $nonce . '\')'
					);

				}
			}
			if ( 
				in_array( 'taxonomy-view', $types ) 
				&& isset( $views_objects['taxonomy'] )
				&& is_array( $views_objects['taxonomy'] )
			) {
				foreach ( $views_objects['taxonomy'] as $view ) {
					$editor->add_insert_shortcode_menu(
						esc_html( $view->post_title ),
						'wpv-view name="' . esc_html( $view->post_title ) . '"',
						__( 'Taxonomy View', 'wpv-views' ),
						'WPViews.shortcodes_gui.wpv_insert_view_shortcode_dialog(\'' . $view->ID . '\', \'' . esc_js( $view->post_title ) . '\', \'' . $current_id . '\', \'' . $nonce . '\')'
					);
					$index += 1;
				}
			}
			if ( 
				in_array( 'user-view', $types ) 
				&& isset( $views_objects['users'] )
				&& is_array( $views_objects['users'] )
			) {
				foreach ( $views_objects['users'] as $view ) {
					$editor->add_insert_shortcode_menu(
						esc_html( $view->post_title ),
						'wpv-view name="' . esc_html( $view->post_title ) . '"',
						__( 'User View', 'wpv-views' ),
						'WPViews.shortcodes_gui.wpv_insert_view_shortcode_dialog(\'' . $view->ID . '\', \'' . esc_js( $view->post_title ) . '\', \'' . $current_id . '\', \'' . $nonce . '\')'
					);
					$index += 1;
				}
			}
		}
    }

	// @todo move this to the WPML file with dependency-free hooks
	if ( in_array( 'wpml', $types ) ) {
		global $sitepress;
		if ( isset( $sitepress ) ) {
			$editor->add_insert_shortcode_menu(
				__( 'WPML lang switcher', 'wpv-views' ),
				'wpml-lang-switcher',
				'WPML'
			);
			$index += 1;
			global $icl_language_switcher;
			if ( isset( $icl_language_switcher ) ) {
				$editor->add_insert_shortcode_menu(
					__( 'WPML lang footer', 'wpv-views' ),
					'wpml-lang-footer',
					'WPML'
				);
				$index += 1;
			}
			global $iclCMSNavigation;
			if ( isset( $iclCMSNavigation ) ) {
				//$editor->add_insert_shortcode_menu('WPML breadcrumbs', 'wpml-breadcrumbs', 'WPML');
				//$index += 1;
				$editor->add_insert_shortcode_menu(
					__( 'WPML sidebar', 'wpv-views' ),
					'wpml-sidebar',
					'WPML'
				);
				$index += 1;
			}
		}
	}

	if ( defined( 'WPSEO_VERSION' ) ) {
		$editor->add_insert_shortcode_menu(
			__( 'Yoast SEO breadcrumbs', 'wpv-views' ),
			'yoast-breadcrumbs',
			'Yoast SEO'
		);
		$index += 1;
	}
	
	if ( in_array( 'loop-wizard-for-posts', $types ) ) {
		do_action( 'wpv_action_wpv_add_field_on_loop_wizard_for_posts', $editor, $nonce );
	}
	
	if ( in_array( 'loop-wizard-for-taxonomy', $types ) ) {
		do_action( 'wpv_action_wpv_add_field_on_loop_wizard_for_taxonomy', $editor, $nonce );
	}
	
	if ( in_array( 'loop-wizard-for-users', $types ) ) {
		do_action( 'wpv_action_wpv_add_field_on_loop_wizard_for_users', $editor, $nonce );
	}

    return $index;
}

function wpv_post_taxonomies_shortcode() {
	add_shortcode('wpv-post-taxonomy', 'wpv_post_taxonomies_shortcode_render');
	add_filter('editor_addon_menus_wpv-views', 'wpv_post_taxonomies_editor_addon_menus_wpv_views_filter', 11);
}

/**
 * Views-Shortcode: wpv-post-taxonomy
 *
 * Description: Display the taxonomy for the current post.
 *
 * Parameters:
 * 'type' => The name of the taxonomy to be displayed
 * 'separator' => Separator to use when there are multiple taxonomy terms for the post. The default is a comma.
 * 'format' => 'link'|'url'|'name'|'description'|'slug'|'count'. Defaults to 'link'
 *     DEPRECATED 'text' defaults to show="name"
 * 'show' => 'name'|'description'|'slug'|'count'. Defaults to 'name'
 *     USED ONLY when format="link" to set the anchor
 *     DEPRECATED when used in combination with format="text"
 * 'order' => 'asc', 'desc'. Defaults to 'asc'
 *
 * Example usage:
 * Filed under [wpv-post-taxonomy type="category" separator=", " format="link" show="name" order="asc"]
 *
 * Link:
 *
 * Note:
 *
 */


function wpv_post_taxonomies_shortcode_render($atts) {
	$post_id_atts = new WPV_wpcf_switch_post_from_attr_id($atts);

	extract(
		shortcode_atts(
			array(
				'separator' => ', ',
				'format' => 'link',
				'show' => 'name',
				'order' => 'asc'
			),
			$atts
		)
	);

	$out = '';
	if ( empty( $atts['type'] ) ) {
		return $out;
	}
	$types = explode( ',', $atts['type'] );
	if ( empty( $types ) ) {
		return $out;
	} else {
		$types = array_map( 'trim', $types );
		$types = array_map( 'sanitize_text_field', $types );
	}
	global $post;
	$out_terms = array();
	foreach ( $types as $taxonomy_slug ) {
		$terms = get_the_terms( $post->ID, $taxonomy_slug );
		if ( 
			$terms 
			&& ! is_wp_error( $terms )
		) {
			foreach ( $terms as $term ) {
				switch ( $format ) {
					case 'text':// DEPRECATED at 1.9, keep for backwards compatibility
						$text = $term->name;
						switch ( $show ) {
							case 'description':
								$text = $term->description;
								break;
							case 'count':
								$text = $term->count;
								break;
							case 'slug':
								$text = $term->slug;
								break;
						}
						$out_terms[$term->name] = $text;
						break;
					case 'name':
						$out_terms[$term->name] = $term->name;
						break;
					case 'description':
						$out_terms[$term->name] = $term->description;
						break;
					case 'count':
						$out_terms[$term->name] = $term->count;
						break;
					case 'slug':
						$out_terms[$term->name] = $term->slug;;
						break;
					case 'url':
						$term_link = get_term_link( $term, $taxonomy_slug );
						$out_terms[$term->name] = $term_link;
						break;
					default:
						$term_link = get_term_link( $term, $taxonomy_slug );
						$text = $term->name;
						switch ( $show ) {
							case 'description':
								$text = $term->description;
								break;
							case 'count':
								$text = $term->count;
								break;
							case 'slug':
								$text = $term->slug;
								break;
						}
						$out_terms[$term->name] = '<a href="' . $term_link . '">' . $text . '</a>';
						break;
				}
			}
		}
	}
	if ( ! empty( $out_terms ) ) {
		if ( $order == 'asc' ) {
			ksort( $out_terms );
		} elseif ( $order == 'desc' ) {
			ksort( $out_terms );
			$out_terms = array_reverse( $out_terms );
		}
		$out = implode( $separator, $out_terms );
	}
	apply_filters('wpv_shortcode_debug','wpv-post-taxonomy', json_encode($atts), '', 'Data received from cache.', $out);
	return $out;
}

function wpv_post_taxonomies_editor_addon_menus_wpv_views_filter($items) {
	$taxonomies = get_taxonomies('', 'objects');
	$exclude_tax_slugs = array();
	$exclude_tax_slugs = apply_filters( 'wpv_admin_exclude_tax_slugs', $exclude_tax_slugs );
	$add = array();
	$nonce = wp_create_nonce('wpv_editor_callback');
	foreach ($taxonomies as $taxonomy_slug => $taxonomy) {
		if ( in_array($taxonomy_slug, $exclude_tax_slugs ) ) {
			continue;
		}
		if ( !$taxonomy->show_ui ) {
			continue; // Only show taxonomies with show_ui set to TRUE
		}
		$add[__('Taxonomy', 'wpv-views')][$taxonomy->label] = array(
			$taxonomy->label,
			'wpv-post-taxonomy type="' . $taxonomy_slug . '" separator=", " format="link" show="name" order="asc"',
			__('Category', 'wpv-views'),
			"WPViews.shortcodes_gui.wpv_insert_popup('wpv-post-taxonomy', '" . $taxonomy->label . "', {type:{value:'" . $taxonomy_slug . "',label:'". esc_js( $taxonomy->label ) . "'}}, '" . $nonce . "', this )"
		);
	}

	$part_one = array_slice($items, 0, 1);
	$part_two = array_slice($items, 1);
	$items = $part_one + $add + $part_two;
	return $items;
}

/**
* wpv_shortcodes_register_wpv_post_taxonomy_data
*
* Register the wpv-post-taxonomy shortcode in the GUI API.
*
* @since 1.9
*/

add_filter( 'wpv_filter_wpv_shortcodes_gui_data', 'wpv_shortcodes_register_wpv_post_taxonomy_data' );

function wpv_shortcodes_register_wpv_post_taxonomy_data( $views_shortcodes ) {
	$views_shortcodes['wpv-post-taxonomy'] = array(
		'callback' => 'wpv_shortcodes_get_wpv_post_taxonomy_data'
	);
	return $views_shortcodes;
}

function wpv_shortcodes_get_wpv_post_taxonomy_data() {
    $data = array(
        'name' => __( 'Post taxonomy', 'wpv-views' ),
        'label' => __( 'Post taxonomy', 'wpv-views' ),
        'post-selection' => true,
        'attributes' => array(
            'display-options' => array(
                'label' => __('Display options', 'wpv-views'),
                'header' => __('Display options', 'wpv-views'),
                'fields' => array(
                    'format' => array(
                        'label' => __( 'Display format', 'wpv-views'),
                        'type' => 'radio',
                        'options' => array(//'link'|'url'|'name'|'description'|'slug'|'count'
                            'link' => __('Link to term archive page', 'wpv-views'),
                            'url' => __('URL of term archive page', 'wpv-views'),
							'name' => __( 'Term name', 'wpv-views' ),
							'description' => __( 'Term description', 'wpv-views' ),
							'slug' => __( 'Term slug', 'wpv-views' ),
							'count' => __( 'Term post count', 'wpv-views' ),
                            //'text' => __('Term related text', 'wpv-views'),
                        ),
                        'default' => 'link',
                    ),
					'show' => array(
                        'label' => __( 'Anchor text when linking to the term archive page ', 'wpv-views'),
                        'type' => 'select',
                        'options' => array(
                            'name' => __('Term name', 'wpv-views'),
                            'description' => __('Term description', 'wpv-views'),
                            'slug' => __('Term slug', 'wpv-views'),
                            'count' => __('Number of terms', 'wpv-views' ),
                        ),
                        'default' => 'name',
                    ),
                    'separator' => array(
                        'label' => __( 'Separator between terms', 'wpv-views'),
                        'type' => 'text',
                        'default' => ', ',
                    ),
                    'order' => array(
                        'label' => __( 'Order ', 'wpv-views'),
                        'type' => 'radio',
                        'options' => array(
                            'asc' => __('Ascending', 'wpv-views'),
                            'desc' => __('Descending', 'wpv-views'),
                        ),
                        'default' => 'asc',
                    ),
                ),
            ),
        ),
    );

    return $data;
}

function wpv_do_shortcode($content) {

  $content = apply_filters('wpv-pre-do-shortcode', $content);

  // HACK HACK HACK
  // fix up a problem where shortcodes are not handled
  // correctly by WP when there a next to each other

  $content = str_replace('][', ']###SPACE###[', $content);
  $content = str_replace(']###SPACE###[/', '][/', $content);
  $content = do_shortcode($content);
  $content = str_replace('###SPACE###', '', $content);

  return $content;
}

add_shortcode('wpv-filter-order', 'wpv_filter_shortcode_order');
function wpv_filter_shortcode_order($atts){
	extract(
		shortcode_atts( array(), $atts )
	);

	global $WP_Views;
	$view_settings = $WP_Views->get_view_settings();

	$view_settings = wpv_filter_get_order_arg($view_settings, $view_settings);
	$order_selected = $view_settings['order'];

	$orders = array('DESC', 'ASC');
	return wpv_filter_show_user_interface('wpv_order', $orders, $order_selected, $atts['style']);
}

add_shortcode('wpv-filter-types-select', 'wpv_filter_shortcode_types');
function wpv_filter_shortcode_types($atts){
	extract(
		shortcode_atts( array(), $atts )
	);

	global $WP_Views;
	$view_settings = $WP_Views->get_view_settings();

	$view_settings = wpv_filter_get_post_types_arg($view_settings, $view_settings);
	$post_types_selected = $view_settings['post_type'];

	$post_types = get_post_types(array('public'=>true));
	return wpv_filter_show_user_interface('wpv_post_type', $post_types, $post_types_selected, $atts['style']);
}

/**
 * Add a shortcode for the search input from the user
 *
 */

add_shortcode('wpv-filter-search-box', 'wpv_filter_search_box');
function wpv_filter_search_box($atts){
	extract(
		shortcode_atts( array(
            'style' => '',
            'class' => ''
            ), $atts )
	);

	global $WP_Views;
	$view_settings = $WP_Views->get_view_settings();

    if ( ! empty( $style ) ) {
        $style = ' style="'. esc_attr( $style ) .'"';
    }

	if ($view_settings['query_type'][0] == 'posts') {
		if ($view_settings && isset($view_settings['post_search_value']) && isset($view_settings['search_mode']) && $view_settings['search_mode'] == 'specific') {
			$value = 'value="' . $view_settings['post_search_value'] . '"';
		} else {
			$value = '';
		}
		if (isset($_GET['wpv_post_search'])) {
			$value = 'value="' . stripslashes( urldecode( sanitize_text_field( $_GET['wpv_post_search'] ) ) ) . '"';
		}
        if ( ! empty( $class ) ) {
            $class = ' ' . esc_attr( $class ) . '"';
        }
		return '<input type="text" name="wpv_post_search" ' . $value . ' class="js-wpv-filter-trigger-delayed'.  $class .'"'. $style .' />';
	}

	if ($view_settings['query_type'][0] == 'taxonomy') {
		if ($view_settings && isset($view_settings['taxonomy_search_value'])  && isset($view_settings['taxonomy_search_mode']) && $view_settings['taxonomy_search_mode'] == 'specific') {
			$value = 'value="' . $view_settings['taxonomy_search_value'] . '"';
		} else {
			$value = '';
		}
		if (isset($_GET['wpv_taxonomy_search'])) {
			$value = 'value="' . stripslashes( urldecode( sanitize_text_field( $_GET['wpv_taxonomy_search'] ) ) )  . '"';
		}
        if ( ! empty( $class ) ) {
            $class = ' class="' . esc_attr( $class ) . '"';
        }
		return '<input type="text" name="wpv_taxonomy_search" ' . $value . ''.  $class . $style .'/>';
	}
}


/**
 * Views-Shortcode: wpv-for-each
 *
 * Description: Iterate through multple items in a post meta field and output the enclosed text for each item
 *
 * Parameters:
 * 'field' => The name of post meta field.
 *
 * Example usage:
 * Output the field values as an ordered list
 * <ol>[wpv-for-each field="my-field"]<li>[wpv-post-field name="my-field"]</li>[/wpv-for-each]<ol>
 *
 * Link:
 *
 * Note:
 * <a href="#wpv-if">wpv-if</a> shortcode won't work inside a wpv-for-each shortcode
 */

function wpv_for_each_shortcode( $atts, $value ) {
	extract(
		shortcode_atts(
			array(
				'field' => '',
				'start' => 1,
				'end' => null
			),
			$atts
		)
	);
	
	if ( strpos( $value, 'wpv-b64-' ) === 0) {
		$value = substr( $value, 7 );
		$value = base64_decode( $value );
	}

	if ( $field == '' ) {
		return wpv_do_shortcode( $value );
	}

	$out = '';

	global $post;

	if ( !empty( $post ) ) {
		$meta = get_post_meta( $post->ID, $field );

		if ( !$meta ) {
			// return $value; // old behaviour
			// This happens when there is no meta with that key asociated with that post, so return nothing
			// From 1.4
			return '';
		}

		// When the metavalue for this key is empty, $meta is an array with just an empty first element
		// In that case, return nothing either
		// From 1.4
		if ( is_array( $meta ) && ( count( $meta ) == 1 ) && ( empty( $meta[0] ) ) ) {
			return '';
		}

		$start = (int) $start;
		$start = $start - 1;
		if ( is_null( $end ) ) {
			$end = count( $meta );
		}
		$end = (int) $end;
		if ( $start < 0 ) {
			$start = 0;
		}
		if ( $end > count( $meta ) ) {
			$end = count( $meta );
		}
		
		$inner_loopers = "/\\[(wpv-post-field|types).*?\\]/i";
		$counts = preg_match_all($inner_loopers, $value, $matches);
		$value_arr = array();
		for ( $i = $start; $i < $end; $i++ ) {
			// Set indexes in the wpv-post-field shortcode
			if ( $counts > 0 ) {
				$new_value = $value;
				foreach( $matches[0] as $index => $match ) {
					// execute shortcode content and replace
					$shortcode = $matches[ 1 ][ $index ];
					$apply_index = wpv_should_apply_index_for_each_field( $shortcode, $match, $field );
					
					if ( $apply_index) {
						$resolved_match = str_replace( '[' . $shortcode . ' ', '[' . $shortcode . ' index="' . $i . '" ', $match );
						$new_value = str_replace( $match, $resolved_match, $new_value );
					}
				}
				$value_arr[] = $new_value;

			} else {
				$value_arr[] = $value;
			}
		}
		$out .= implode( '', $value_arr );

	}
	apply_filters( 'wpv_shortcode_debug', 'wpv-for-each', json_encode( $atts ), '', 'Data received from cache.', $out );
	return $out;

}

function wpv_should_apply_index_for_each_field( $shortcode_type, $shortcode, $field ) {
	$apply_index = false;
	
	if ( strpos( $shortcode, " index=" ) === false ) {
		$apply_index = true;
	}
	
	return $apply_index;
	
}

/**
* wpv_shortcodes_register_wpv_for_each_data
*
* Register the wpv-for-each shortcode in the GUI API.
*
* @since 1.9
*/

add_filter( 'wpv_filter_wpv_shortcodes_gui_data', 'wpv_shortcodes_register_wpv_for_each_data' );

function wpv_shortcodes_register_wpv_for_each_data( $views_shortcodes ) {
	$views_shortcodes['wpv-for-each'] = array(
		'callback' => 'wpv_shortcodes_get_wpv_for_each_data'
	);
	return $views_shortcodes;
}

function wpv_shortcodes_get_wpv_for_each_data() {
    $data = array(
        'name' => __( 'Post field iterator', 'wpv-views' ),
        'label' => __( 'Post field iterator', 'wpv-views' ),
        //'post-selection' => true,
        'attributes' => array(
            'display-options' => array(
                'label' => __('Display options', 'wpv-views'),
                'header' => __('Display options', 'wpv-views'),
                'fields' => array(
                    'field' => array(
                        'label' => __( 'Custom field', 'wpv-views'),
                        'type' => 'suggest',
						'action' => 'wpv_suggest_wpv_post_field_name',
                        'description' => __('The name of the custom field to display', 'wpv-views'),
                        'required' => true,
                    ),
					'start' => array(
						'label' => __( 'Index to start', 'wpv-views'),
						'type' => 'number',
						'default' => '1',
						'description' => __('Start the iteration on this index. Defaults to 1.', 'wpv-views'),
					),
					'end' => array(
						'label' => __( 'Index to end', 'wpv-views'),
						'type' => 'number',
						'default' => '',
						'description' => __('End the iteration on this index. No value means all the way until the last index.', 'wpv-views'),
					),
                ),
				'content' => array(
					'hidden' => true,
					'label' => __( 'Content of each iteration', 'wpv-views' ),
					'description' => __( 'This will be displayed on each iteration. The usual content is <code>[wpv-post-field name="field-name"]</code> where field-name is the custom field selected above.', 'wpv-views' )
				)
            ),
        ),
    );
    return $data;
}


// WPML shortcodes to add to Views.

add_shortcode('wpml-lang-switcher', 'wpv_wpml_lang_switcher');
function wpv_wpml_lang_switcher($atts, $value){
	ob_start();

	do_action('icl_language_selector');

	$result = ob_get_clean();

	return $result;
}

add_shortcode('wpml-lang-footer', 'wpv_wpml_lang_footer');
function wpv_wpml_lang_footer($atts, $value){

	global $icl_language_switcher;

	if (isset($icl_language_switcher)) {
		ob_start();
		$icl_language_switcher->language_selector_footer_style();
		$icl_language_switcher->language_selector_footer();

		$result = ob_get_clean();
		return $result;
	}

	return '';

}

/*
add_shortcode('wpml-breadcrumbs', 'wpv_wpml_breadcrumbs');
function wpv_wpml_breadcrumbs($atts, $value){
	ob_start();

	global $iclCMSNavigation;
	if (isset($iclCMSNavigation)) {
		$iclCMSNavigation->cms_navigation_breadcrumb('');
	}

	$result = ob_get_clean();

	return $result;
}
*/

add_shortcode('wpml-sidebar', 'wpv_wpml_sidebar');
function wpv_wpml_sidebar($atts, $value){
	ob_start();

	do_action('icl_navigation_sidebar', '');

	$result = ob_get_clean();

	return $result;
}

add_shortcode('yoast-breadcrumbs', 'wpv_yoast_breadcrumbs');
function wpv_yoast_breadcrumbs($atts, $value){

	if ( function_exists('yoast_breadcrumb') ) {
		return yoast_breadcrumb("","",false);
	}

	return '';
}


/** Output value of current View's attribute.
 *
 * @param array $atts {
 *	 Shortcode attributes.
 *
 *	 @string $name Name of the attribute of current View.
 * }
 *
 * @return Attribute value or an empty string if no such attribute is set.
 *
 * @since 1.7
 */
add_shortcode( 'wpv-attribute', 'wpv_attribute' );
function wpv_attribute( $atts, $value ) {
	global $WP_Views;
	extract( shortcode_atts(
			array( 'name' => '' ),
			$atts ) );

	$view_atts = $WP_Views->get_view_shortcodes_attributes();

	if( '' == $name || !array_key_exists( $name, $view_atts ) ) {
		return '';
	}

	return $view_atts[ $name ];
}
