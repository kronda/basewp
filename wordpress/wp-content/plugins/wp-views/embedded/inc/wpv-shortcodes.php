<?php

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

$wpv_shortcodes['wpv-post-id'] = array('wpv-post-id', __('ID', 'wpv-views'), 'wpv_shortcode_wpv_post_id');
$wpv_shortcodes['wpv-post-slug'] = array('wpv-post-slug', __('Slug', 'wpv-views'), 'wpv_shortcode_wpv_post_slug');
$wpv_shortcodes['wpv-post-title'] = array('wpv-post-title', __('Title', 'wpv-views'), 'wpv_shortcode_wpv_post_title');
$wpv_shortcodes['wpv-post-link'] = array('wpv-post-link', __('Title with a link', 'wpv-views'), 'wpv_shortcode_wpv_post_link');
$wpv_shortcodes['wpv-post-body'] = array('wpv-post-body', __('Body', 'wpv-views'), 'wpv_shortcode_wpv_post_body');
$wpv_shortcodes['wpv-post-excerpt'] = array('wpv-post-excerpt', __('Excerpt', 'wpv-views'), 'wpv_shortcode_wpv_post_excerpt');
$wpv_shortcodes['wpv-post-author'] = array('wpv-post-author', __('Author', 'wpv-views'), 'wpv_shortcode_wpv_post_author');
$wpv_shortcodes['wpv-post-date'] = array('wpv-post-date', __('Date', 'wpv-views'), 'wpv_shortcode_wpv_post_date');
$wpv_shortcodes['wpv-post-url'] = array('wpv-post-url', __('URL', 'wpv-views'), 'wpv_shortcode_wpv_post_url');
$wpv_shortcodes['wpv-post-featured-image'] = array('wpv-post-featured-image', __('Featured image', 'wpv-views'), 'wpv_shortcode_wpv_post_featured_image');
$wpv_shortcodes['wpv-post-comments-number'] = array('wpv-post-comments-number', __('Comments number', 'wpv-views'), 'wpv_shortcode_wpv_comments_number');
$wpv_shortcodes['wpv-post-edit-link'] = array('wpv-post-edit-link', __('Edit Link', 'wpv-views'), 'wpv_shortcode_wpv_post_edit_link');
$wpv_shortcodes['wpv-post-type'] = array('wpv-post-type', __('Post type', 'wpv-views'), 'wpv_shortcode_wpv_post_type');
$wpv_shortcodes['wpv-post-format'] = array('wpv-post-format', __('Post format', 'wpv-views'), 'wpv_shortcode_wpv_post_format');
$wpv_shortcodes['wpv-post-status'] = array('wpv-post-status', __('Post status', 'wpv-views'), 'wpv_shortcode_wpv_post_status');
$wpv_shortcodes['wpv-post-class'] = array('wpv-post-class', __('Post class', 'wpv-views'), 'wpv_shortcode_wpv_post_class');

// NOTE:  Put all "post" shortcodes before 'wpv-post-field' so they appear in the right order in various popups.
$wpv_shortcodes['wpv-post-field'] = array('wpv-post-field', __('Field', 'wpv-views'), 'wpv_shortcode_wpv_post_field');


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

$wpv_shortcodes['wpv-bloginfo'] = array('wpv-bloginfo', __('Bloginfo value', 'wpv-views'), 'wpv_bloginfo');
$wpv_shortcodes['wpv-search-term'] = array('wpv-search-term', __('Search term', 'wpv-views'), 'wpv_search_term');
$wpv_shortcodes['wpv-archive-title'] = array('wpv-archive-title', __('Archive title', 'wpv-views'), 'wpv_archive_title');
$wpv_shortcodes['wpv-archive-link'] = array('wpv-archive-link', __('Archive link', 'wpv-views'), 'wpv_archive_link');

//User shortcodes
$wpv_shortcodes['wpv-current-user'] = array('wpv-current-user', __('Current user info', 'wpv-views'), 'wpv_current_user');
$wpv_shortcodes['wpv-user'] = array('wpv-user', __('Show user data', 'wpv-views'), 'wpv_user');
$wpv_shortcodes['wpv-login-form'] = array('wpv-login-form', __('Login Form', 'wpv-views'), 'wpv_shortcode_wpv_login_form');

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
		global $post;
		if(isset($post->post_type) and $post->post_type!=''){
			$out  = get_post_type_archive_link($post->post_type);
		}
	}
	apply_filters('wpv_shortcode_debug','wpv-archive-link', json_encode($attr), '', '', $out);
	return $out;
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

	if($current_user->ID>0){
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

function wpv_user($attr){

	extract(
		shortcode_atts( array(
		'field' => 'display_name',
		'id' => ''
		), $attr )
	);
	//Get data for specified ID
	if ( isset( $id ) && !empty( $id ) ) {
		$data = new WP_User( $id );
		$user_id = $id;
		if ( isset( $data->data ) ){
			$data = $data->data;
			$meta = get_user_meta( $id );
		} else {
			return;
		}
	} else {
		global $WP_Views;
		if ( isset( $WP_Views->users_data['term']->ID ) && !empty( $WP_Views->users_data['term']->ID ) ) {
			$user_id = $WP_Views->users_data['term']->ID;
		} else {
			return;
		}
		$data = $WP_Views->users_data['term']->data;
		$meta = $WP_Views->users_data['term']->meta;
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
			} else { // if seeking for a non-existing field, return an empty $out
			//	$default_field = 'display_name';
			//	$out = $data->$default_field;
			}
			break;
	}
	apply_filters('wpv_shortcode_debug','wpv-user', json_encode($attr), '', 'Data received from $WP_Views object', $out);
	return $out;
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
		shortcode_atts( array(), $atts )
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

		$out .= '<a href="' . $post_link . '">';
		$out .= apply_filters('the_title', $post->post_title);
		$out .= '</a>';
		apply_filters('wpv_shortcode_debug','wpv-post-link', json_encode($atts), '', 'Filter the_title applied', $out);

	}


	return $out;
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
			$post_link = add_query_arg( array( 'preview' => 'true' ), $post_link );
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
			$out .= apply_filters('the_content', $post->post_content);
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


	/*	if($display_real_excerpt) {
			$excerpt_length = strlen($excerpt); // don't cut manually inserted excerpts
			$excerpt_more = '';
		}
		*/
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
		shortcode_atts( array('format' => 'name', 'meta' => 'nickname'), $atts )
	);

	global $authordata; // TODO check if this global is needed here; when switching posts its useless

	$author_url = esc_url( get_author_posts_url( get_the_author_meta( 'ID' )));

	switch ($format) {
		case 'link':
			$out = '<a href="' . $author_url . '">' . get_the_author() . '</a>';
			break;

		case 'url':
			$out = $author_url;
			break;

		case 'meta':
			$out = get_the_author_meta($meta);
			break;

		default:
			$out = get_the_author();
			break;

	}
	apply_filters('wpv_shortcode_debug','wpv-post-author', json_encode($atts), '', 'Data received from cache', $out);
	return $out;
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
 * Views-Shortcode: wpv-post-featured-image
 *
 * Description: Display the featured image of the current post
 *
 * Parameters:
 * 'size' => Image size - thumbnail, medium, large or full - defaults to thumbnail
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

function wpv_shortcode_wpv_post_featured_image($atts) {
	global $WPVDebug;
	$post_id_atts = new WPV_wpcf_switch_post_from_attr_id($atts);

	extract(
		shortcode_atts( array(
			'size'  => 'thumbnail',
			'raw'   => 'false',
			'attr'  => '',
			'data'  => ''
		), $atts )
	);

	$out = '';
	$info = array('id'=>'ID','author'=>'post_author','date'=>'post_date','description'=>'post_content','title'=>'post_title'
	,'caption'=>'post_excerpt','original'=>'guid');
	if( $raw === 'true'  || !empty($data) ) {
		if ( empty($data) ){
			$data = 'url';
		}
		$file_info = '';
		$post_thumbnail_id = get_post_thumbnail_id( get_the_ID() );
		if ( !empty( $post_thumbnail_id ) ) {
			switch ( $data ) {
				case 'url':
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
				case 'id':
				case 'author':
				case 'date':
				case 'description':
				case 'title':
				case 'caption':
				case 'original':
					$new_info = get_post( $post_thumbnail_id );
					$new_value = $info[$data];
					if( isset( $new_info->$new_value ) ) {
						$file_info = $new_info->$new_value;
					}
					break;
				case 'alt':
					$file_info = get_post_meta($post_thumbnail_id , '_wp_attachment_image_alt', true);
					break;
			}
			$out = apply_filters('wpv-post-featured-image', $file_info);
		}
	} else {
		// Sanitize and escape elements in the query-like string $attr with a HACK
		if ( !empty( $attr ) ) {
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
		$out = get_the_post_thumbnail( null, $size, $attr_array );
		$out = apply_filters('wpv-post-featured-image', $out);
	}

	apply_filters('wpv_shortcode_debug','wpv-post-featured-image', json_encode($atts), '', 'Filter wpv-post-featured-image applied', $out);

	return $out;
}

/**
* Views-Shortcode: wpv-post-edit-link
*
* Description: Display an edit link for the current post
*
* Parameters:
* label: Optional. What to show in the edit link. ie: 'Edit Video'
*
* Example usage:
* [wpv-post-edit-link]
*
* Link:
*
* Note:
*
*/
function wpv_shortcode_wpv_post_edit_link($atts){
	$post_id_atts = new WPV_wpcf_switch_post_from_attr_id($atts);

	extract(
		shortcode_atts( array(), $atts )
	);

	$out = '';
	global $post;

	if(!empty($post) && current_user_can('edit_posts')){
		$out .= '<a href="' . get_edit_post_link( $post->ID ) . '" class="post-edit-link">';
		$out .= (isset($atts['label']))? __('Edit ', 'wpv-views') .$atts['label'] : __('Edit This', 'wpv-views');
		$out .= '</a>';
	}
	apply_filters('wpv_shortcode_debug','wpv-post-edit-link', json_encode($atts), '', 'Data received from cache', $out);
	return $out;
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
		shortcode_atts( array('index' => '',
							  'name' => '',
							  'separator' => ', '), $atts )
	);

	$out = '';
	$filters = '';
	global $post;

	if(!empty($post)){
		$meta = get_post_meta($post->ID, $name);

		$meta = apply_filters('wpv-post-field-meta-' . $name, $meta);
		$filters .= 'Filter wpv-post-field-meta-' . $name .' applied. ';
		if ($meta) {

			// Allow wpv-for-each shortcode to set the index
			$index = apply_filters('wpv-for-each-index', $index);
			$filters .= 'Filter wpv-for-each-index applied, ';
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

$wpv_comments_defaults = array('none' => __('No Comments', 'wpv-views'),
							  'one' => __('1 Comment', 'wpv-views'),
							  'more' => __('% Comments', 'wpv-views'));


function wpv_shortcode_wpv_comments_number($atts) {
	$post_id_atts = new WPV_wpcf_switch_post_from_attr_id($atts);
	global $WPVDebug;

	global $wpv_comments_defaults, $post;

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
		shortcode_atts( $wpv_comments_defaults, $atts )
		);

	ob_start();

	wp_count_comments($post->ID);

	comments_number($none, $one, $more);

	$out = ob_get_clean();
	apply_filters('wpv_shortcode_debug','wpv-post-comments-number', json_encode($atts), $WPVDebug->get_mysql_last(), 'Data received from cache', $out);
	return $out;
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



//@todo - add function for the other shortcodes


/*

  Add the short codes to javascript so they can be added to the
  post visual editor toolbar.

  $types contains the type of items to add to the toolbar
  'post' add all wpv-post shortcodes
  'view' add available views

*/

function add_short_codes_to_js($types, $editor, $call_back = null){

	global $wpv_shortcodes, $wpdb, $WP_Views, $sitepress;

	$cf_keys = $WP_Views->get_meta_keys();

	// Find the field sub menus so we can group sub strings.
	$sub_fields = array();
	$last_field = '';
	foreach ($cf_keys as $field) {
		$parts = explode('_', str_replace('-', '_', $field));
		$start = $parts[0];
		if ($start == '') {
			// starts with an underscore.
			if (isset($parts[1])) {
				$start = $parts[1];
			}
		}

		if ($start == $last_field) {
			// found a duplicate

			if ($parts[0] == '') {
				$start = '_' . $start;
			}
			if (!in_array($start, $sub_fields)) {
				$sub_fields[] = $start;
			}
		} else {
			$last_field = $start;
		}

	}

	$index = 0;
	$nonce = wp_create_nonce('wpv_editor_callback');
	foreach($wpv_shortcodes as $shortcode) {

		if (in_array('post', $types) && strpos($shortcode[0], 'wpv-post-') === 0 && function_exists($shortcode[2])) {

			if ($shortcode[0] == 'wpv-post-field') {

				// we need to output the custom fields to a sub menu.

				foreach ($cf_keys as $cf_key) {

					if ($WP_Views->can_include_type($cf_key)) {
						// add to the javascript array (text, function, sub-menu)
						$function_name = 'wpv_field_' . $index;
						$menu = $shortcode[1];
						$parts = explode('_', str_replace('-', '_', $cf_key));
						$start = $parts[0];
						if ($start == '') {
							// starts with an underscore.
							if (isset($parts[1])) {
								$start = '_' . $parts[1];
							}
						}
						if (in_array($start, $sub_fields)) {
							$menu .= '-!-' . $start;
						}

						if ($call_back) {
							call_user_func($call_back, $index, $cf_key, $function_name, $menu, $shortcode[0]);
						} else {
							$name = ' name="' . $cf_key . '"';
							$editor->add_insert_shortcode_menu($cf_key, $shortcode[0] . $name, $menu);
							}
						$index += 1;
					}
				}

			} else {
				if ($call_back) {
					call_user_func($call_back, $index, $shortcode[1], $shortcode[1], "", $shortcode[0]);
				} else {

					if ($shortcode[0] == 'wpv-post-body') {
						$editor->add_insert_shortcode_menu($shortcode[1], $shortcode[0] . ' view_template="None"', __('Basic', 'wpv-views'));
					} elseif ($shortcode[0] == 'wpv-post-comments-number') {
						global $wpv_comments_defaults;
						$editor->add_insert_shortcode_menu($shortcode[1], $shortcode[0] . ' none="' . $wpv_comments_defaults['none'] . '" one="' . $wpv_comments_defaults['one'] . '" more="' . $wpv_comments_defaults['more'] . '"', __('Basic', 'wpv-views'));
					} else {
						// JS callback
						if (isset($shortcode[3])) {
							$editor->add_insert_shortcode_menu($shortcode[1], $shortcode[0], __('Basic', 'wpv-views'), $shortcode[3]);
						} else {
							$editor->add_insert_shortcode_menu($shortcode[1], $shortcode[0], __('Basic', 'wpv-views'));
						}
					}
				}
				$index += 1;
			}

		}

		if($call_back == 'add-basics') {
			if (isset($shortcode[3])) {
					  $editor->add_insert_shortcode_menu($shortcode[1], $shortcode[0], __('Basic', 'wpv-views'), $shortcode[3]);
				 } else {
					 switch ($shortcode[0]) {
						 case 'wpv-archive-link':
							$editor->add_insert_shortcode_menu($shortcode[1], $shortcode[0].' name=""', __('Basic', 'wpv-views'));
							 break;
						 case 'wpv-bloginfo':
							$editor->add_insert_shortcode_menu($shortcode[1], $shortcode[0].' show="name"', __('Basic', 'wpv-views'));
							 break;
						 case 'wpv-current-user':
							$editor->add_insert_shortcode_menu($shortcode[1], $shortcode[0].' info="login"', __('Basic', 'wpv-views'));
							 break;
						 case 'wpv-user':
						 case 'wpv-login-form':
						 case 'wpv-taxonomy-archive':
						 case 'wpv-comment-title':
						 case 'wpv-comment-body':
						 case 'wpv-comment-author':
						 case 'wpv-comment-date':
						 case 'wpv-taxonomy-title':
						 case 'wpv-taxonomy-link':
						 case 'wpv-taxonomy-url':
						 case 'wpv-taxonomy-slug':
						 case 'wpv-taxonomy-id':
						 case 'wpv-taxonomy-description':
						 case 'wpv-taxonomy-post-count':
							break;
						 default:
							 $editor->add_insert_shortcode_menu($shortcode[1], $shortcode[0], __('Basic', 'wpv-views'));
							 break;
					 }
				 }
		}


		if (in_array('taxonomy', $types) && strpos($shortcode[0], 'wpv-taxonomy-') === 0 && function_exists($shortcode[2])) {
			if ($call_back) {
				call_user_func($call_back, $index, $shortcode[1], $shortcode[1], "", $shortcode[0]);
			}
			$index += 1;
		}

	}

	// Content templates.
	if (in_array('body-view-templates-posts', $types)) {
		// we need to add the available views.
		$view_template_available = $wpdb->get_results(
			"SELECT ID, post_name, post_title FROM {$wpdb->posts} 
			WHERE post_type = 'view-template' 
			AND post_status in ('publish')"
		);
		foreach($view_template_available as $view_template) {
			$editor->add_insert_shortcode_menu($view_template->post_title, 'wpv-post-body view_template="' . $view_template->post_name . '"', __('Content template', 'wpv-views'));
			$index += 1;
		}
	}

	// first we output the Content templates.
	if (in_array('body-view-templates', $types)) {
		global $pagenow, $typenow;
		if ( "view-template" != $typenow || !in_array( $pagenow, array( 'post-new.php' ) ) ) {
			$values_to_prepare = array();
			$values_to_prepare[] = 'view-template';
			$exclude = '';
			if ( in_array( $pagenow, array( 'post.php' ) ) && isset( $_GET["post"] ) ) {
				$this_template = (int) $_GET["post"];
				$exclude = " AND ID != %d ";
				$values_to_prepare[] = $this_template;
			}
			$view_template_available = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT ID, post_name, post_title FROM {$wpdb->posts} 
					WHERE post_type = %s 
					AND post_status in ('publish') 
					{$exclude}",
					$values_to_prepare
				)
			);
			foreach($view_template_available as $view_template) {
			if ($call_back) {
				call_user_func($call_back, $index, $view_template->post_name, '', __('Content template', 'wpv-views'), 'wpv-post-body view_template="' . $view_template->post_name . '"');
			} else {
				$editor->add_insert_shortcode_menu($view_template->post_title, 'wpv-post-body view_template="' . $view_template->post_name . '"', __('Content template', 'wpv-views'));
			}

			$index += 1;
			}
		}
	}

	// If WPML is enabled and String translation is active, add a Translatable string shortcode to the Basic submenu

	if ( isset( $sitepress ) && function_exists( 'wpml_string_shortcode' ) ) {
	$editor->add_insert_shortcode_menu(__('Translatable string', 'wpv-views'),
													'wpml-string',
													__('Basic', 'wpv-views'),
													'WPViews.shortcodes_gui.wpv_insert_translatable_string_popup(\'' . $nonce . '\')'
	);

				$index += 1;
	}

	$editor->add_insert_shortcode_menu(
		__('Search term', 'wpv-views'),
		'wpv-search-term',
		__('Basic', 'wpv-views'),
		'WPViews.shortcodes_gui.wpv_insert_search_term_popup(\'' . $nonce . '\')'
	);
	$index += 1;

	if (
		in_array( 'view', $types )
		|| in_array( 'archives', $types )
		|| in_array( 'user-view', $types )
		|| in_array( 'taxonomy-view', $types )
		|| in_array( 'post-view', $types )
	) {
		$view_available = $wpdb->get_results( 
			"SELECT ID, post_title FROM {$wpdb->posts} 
			WHERE post_type='view' 
			AND post_status in ('publish')" 
		);
		foreach ( $view_available as $view ) {
			if ( $WP_Views->is_archive_view( $view->ID ) ) {
				// Archive Views - add only if in_array( 'archives', $types )
				if ( in_array( 'archives', $types ) ) {
                    $editor->add_insert_shortcode_menu( $view->post_title, 'wpv-view name="' . $view->post_title . '"', __( 'Archive', 'wpv-views' ) );
                    $index += 1;
				}
			} else {
				if ( in_array( 'view', $types ) ) {
					// Add all Views, but add differently for parametric search ones
					if ( $WP_Views->does_view_have_form_controls( $view->ID ) ) {
						global $pagenow;
						$current_id = 0;
						if ( in_array( $pagenow, array( 'post.php' ) ) && isset( $_GET["post"] ) ) {
							$current_id = (int) $_GET["post"];
						} else if ( in_array( $pagenow, array( 'post-new.php' ) ) ) {
							global $post;
							$current_id = $post->ID;
						}
						$editor->add_insert_shortcode_menu($view->post_title,
														'wpv-form-view name="' . $view->post_title . '"',
														__( 'View', 'wpv-views' ),
														'WPViews.shortcodes_gui.wpv_insert_view_form_popup(\'' . $view->ID . '\', \'' . $nonce . '\', \'' . $current_id . '\')');
					} else {
						$editor->add_insert_shortcode_menu( $view->post_title, 'wpv-view name="' . $view->post_title . '"', __( 'View', 'wpv-views' ) );
					}
					$index += 1;
				}
				$view_settings = get_post_meta( $view->ID, '_wpv_settings', true );
				$view_type = 'posts';
				if ( isset( $view_settings['query_type'][0] ) ) {
					$view_type = $view_settings['query_type'][0];
				}
				if ( in_array( 'user-view', $types ) ) {
					// Add Views listing users
					if ( $view_type == 'users' ) {
						$editor->add_insert_shortcode_menu( $view->post_title, 'wpv-view name="' . $view->post_title . '"', __( 'User View', 'wpv-views' ) );
						if ( $call_back ) {
							call_user_func( $call_back, $index, $view->post_title, '', __( 'User View', 'wpv-views' ), 'wpv-view name="' . $view->post_title . '"' );
						}
						$index += 1;
					}
				}
				if ( in_array( 'taxonomy-view', $types ) ) {
					// Add Views listing taxonomies
					if ( $view_type == 'taxonomy' ) {
						$editor->add_insert_shortcode_menu( $view->post_title, 'wpv-view name="' . $view->post_title . '"', __( 'Taxonomy View', 'wpv-views' ) );
						if ( $call_back ) {
							call_user_func( $call_back, $index, $view->post_title, '', __( 'Taxonomy View', 'wpv-views' ), 'wpv-view name="' . $view->post_title . '"' );
						}
						$index += 1;
					}
				}
				if ( in_array( 'post-view', $types ) ) {
					// Add Views listing posts
					if ( $view_type == 'posts' ) {
						$editor->add_insert_shortcode_menu( $view->post_title, 'wpv-view name="' . $view->post_title . '"', __( 'Post View', 'wpv-views' ) );
						if ( $call_back ) {
							call_user_func( $call_back, $index, $view->post_title, '', __( 'Post View', 'wpv-views' ), 'wpv-view name="' . $view->post_title . '"' );
						}
						$index += 1;
					}
				}
			}
		}
        
        // wpv-login-form
        $editor->add_insert_shortcode_menu( $wpv_shortcodes['wpv-login-form'][1], $wpv_shortcodes['wpv-login-form'][0], __( 'Basic', 'wpv-views' ) );
        $index += 1;
    }
    
    // FIXME: What is the best menu group for this shortcode? Is it limited to some contexts?
    // Archive Title
    $editor->add_insert_shortcode_menu( $wpv_shortcodes['wpv-archive-title'][1], $wpv_shortcodes['wpv-archive-title'][0], __( 'Basic', 'wpv-views' ) );
    $index += 1;
    
	if (in_array('wpml', $types)) {
		global $sitepress;

		if (isset($sitepress)) {

			$editor->add_insert_shortcode_menu('WPML lang switcher', 'wpml-lang-switcher', 'WPML');
			$index += 1;

			global $icl_language_switcher;
			if (isset($icl_language_switcher)) {

				$editor->add_insert_shortcode_menu('WPML lang footer', 'wpml-lang-footer', 'WPML');
				$index += 1;

			}

			global $iclCMSNavigation;
			if (isset($iclCMSNavigation)) {

				//$editor->add_insert_shortcode_menu('WPML breadcrumbs', 'wpml-breadcrumbs', 'WPML');
				//$index += 1;

				$editor->add_insert_shortcode_menu('WPML sidebar', 'wpml-sidebar', 'WPML');
				$index += 1;

			}
		}

		if (defined('WPSEO_VERSION')) {

			$editor->add_insert_shortcode_menu('Yoast SEO breadcrumbs', 'yoast-breadcrumbs', 'Yoast SEO');
			$index += 1;
		}

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
 * 'format' => 'link', 'text' or 'url'. Defaults to 'link'
 * 'show' => 'name', 'description', 'slug' or 'count'. Defaults to 'name'
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
		shortcode_atts( array('format' => 'link',
							  'show' => 'name',
							  'order' => 'asc'),
					   $atts )
	);

	global $wplogger;

	$out = '';
	if (empty($atts['type'])) {
		return $out;
	}
	$types = explode(',', @strval($atts['type']));
	if (empty($types)) {
		return $out;
	}

	global $post;
	$separator = !empty($atts['separator']) ? @strval($atts['separator']) : ', ';
	$out_terms = array();
	foreach ($types as $taxonomy_slug) {
		$terms = get_the_terms($post->ID, $taxonomy_slug);
		if ( $terms && !is_wp_error( $terms )) {
			foreach ($terms as $term) {
				$text = $term->name;
				switch ($show) {
					case 'description':
						if ($term->description != '') {
							$text = $term->description;
						}
						break;

					case 'count':
						$text = $term->count;
						break;

					case 'slug':
						$text = $term->slug;
						break;
				}

				$term_link = get_term_link($term, $taxonomy_slug);
				if (is_wp_error($term_link)) {
					$wplogger->log('Term invalid - term_slug = ' . $term->slug . ' - taxonomy_slug = ' . $taxonomy_slug, WPLOG_DEBUG);
				}

				if ($format == 'text') {
					$out_terms[$term->name] = $text;
				} else if ($format == 'url') {
					$out_terms[$term->name] = $term_link;
				} else {
					$out_terms[$term->name] = '<a href="' . $term_link . '">' . $text . '</a>';
				}
			}
		}
	}
	if (!empty($out_terms)) {
		if ($order == 'asc') {
			ksort($out_terms);
		} elseif ($order == 'desc') {
			ksort($out_terms);
			$out_terms = array_reverse($out_terms);
		}
		$out = implode($separator, $out_terms);
	}
	apply_filters('wpv_shortcode_debug','wpv-post-taxonomy', json_encode($atts), '', 'Data received from cache.', $out);
	return $out;
}

function wpv_post_taxonomies_editor_addon_menus_wpv_views_filter($items) {
	$taxonomies = get_taxonomies('', 'objects');
	$exclude_tax_slugs = array();
	$exclude_tax_slugs = apply_filters( 'wpv_admin_exclude_tax_slugs', $exclude_tax_slugs );
	$add = array();
	foreach ($taxonomies as $taxonomy_slug => $taxonomy) {
		if ( in_array($taxonomy_slug, $exclude_tax_slugs ) ) {
			continue;
		}
		if ( !$taxonomy->show_ui ) {
			continue; // Only show taxonomies with show_ui set to TRUE
		}
		$add[__('Taxonomy', 'wpv-views')][$taxonomy->label] = array($taxonomy->label, 'wpv-post-taxonomy type="' . $taxonomy_slug . '" separator=", " format="link" show="name" order="asc"', __('Category', 'wpv-views'), '');
	}

	$part_one = array_slice($items, 0, 1);
	$part_two = array_slice($items, 1);
	$items = $part_one + $add + $part_two;
	return $items;
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
		shortcode_atts( array(), $atts )
	);

	global $WP_Views;
	$view_settings = $WP_Views->get_view_settings();

	if ($view_settings['query_type'][0] == 'posts') {
		if ($view_settings && isset($view_settings['post_search_value']) && isset($view_settings['search_mode']) && $view_settings['search_mode'] == 'specific') {
			$value = 'value="' . $view_settings['post_search_value'] . '"';
		} else {
			$value = '';
		}
		if (isset($_GET['wpv_post_search'])) {
			$value = 'value="' . stripslashes( urldecode( sanitize_text_field( $_GET['wpv_post_search'] ) ) ) . '"';
		}

		return '<input type="text" name="wpv_post_search" ' . $value . ' class="js-wpv-filter-trigger-delayed" />';
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

		return '<input type="text" name="wpv_taxonomy_search" ' . $value . '/>';
	}
}


/** Global for storing the current wpv-for-each index. */
$wpv_for_each_index = array();


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
add_shortcode( 'wpv-for-each', 'wpv_for_each_shortcode' );
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

	if ( $field == '' ) {
		return wpv_do_shortcode( $value );
	}

	$out = '';

	global $post, $wpv_for_each_index;

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

		// iterate through the items and set the for-each index
		$wpv_for_each_index[] = 0;
		for ( $i = $start; $i < $end; $i++ ) {

			// set the for-each index and output
			$wpv_for_each_index[count( $wpv_for_each_index ) - 1] = $i;

			$out .= wpv_do_shortcode( $value );
		}

		array_pop( $wpv_for_each_index );

	}
	apply_filters( 'wpv_shortcode_debug', 'wpv-for-each', json_encode( $atts ), '', 'Data received from cache.', $out );
	return $out;

}

// set the for-each index
add_filter('wpv-for-each-index', 'wpv_for_each_index');
function wpv_for_each_index($index) {
	global $wpv_for_each_index;

	if (count($wpv_for_each_index) > 0) {
		return end($wpv_for_each_index);
	}

	return $index;
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
