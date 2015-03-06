<?php
/**
 * @package Make
 */

if ( ! function_exists( 'ttfmake_page_menu_args' ) ) :
/**
 * Get our wp_nav_menu() fallback, wp_page_menu(), to show a home link.
 *
 * @since  1.0.0.
 *
 * @param  array    $args    Configuration arguments.
 * @return array             Modified page menu args.
 */
function ttfmake_page_menu_args( $args ) {
	$args['show_home'] = true;
	return $args;
}
endif;

add_filter( 'wp_page_menu_args', 'ttfmake_page_menu_args' );

if ( ! function_exists( 'ttfmake_body_classes' ) ) :
/**
 * Adds custom classes to the array of body classes.
 *
 * @since  1.0.0.
 *
 * @param  array    $classes    Classes for the body element.
 * @return array                Modified class list.
 */
function ttfmake_body_classes( $classes ) {
	// Full-width vs Boxed
	$classes[] = get_theme_mod( 'general-layout', ttfmake_get_default( 'general-layout' ) );

	// Header branding position
	if ( 'right' === get_theme_mod( 'header-branding-position', ttfmake_get_default( 'header-branding-position' ) ) ) {
		$classes[] = 'branding-right';
	}

	// Header Bar text position
	if ( 'flipped' === get_theme_mod( 'header-bar-content-layout', ttfmake_get_default( 'header-bar-content-layout' ) ) ) {
		$classes[] = 'header-bar-flipped';
	}

	// Left Sidebar
	if ( true === ttfmake_has_sidebar( 'left' ) ) {
		$classes[] = 'has-left-sidebar';
	}

	// Right Sidebar
	if ( true === ttfmake_has_sidebar( 'right' ) ) {
		$classes[] = 'has-right-sidebar';
	}

	return $classes;
}
endif;

add_filter( 'body_class', 'ttfmake_body_classes' );

if ( ! function_exists( 'ttfmake_wp_title' ) ) :
/**
 * Filters wp_title to print a neat <title> tag based on what is being viewed.
 *
 * @since  1.0.0.
 *
 * @param  string    $title    Default title text for current view.
 * @param  string    $sep      Optional separator.
 *
 * @return string              The filtered title.
 */
function ttfmake_wp_title( $title, $sep ) {
	if ( version_compare( $GLOBALS['wp_version'], '4.1', '>=' ) || is_feed() ) {
		return $title;
	}

	global $page, $paged;

	// Add the blog name
	$title .= get_bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) ) {
		$title .= " $sep $site_description";
	}

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 ) {
		$title .= " $sep " . sprintf( __( 'Page %s', 'make' ), max( $paged, $page ) );
	}

	return $title;
}
endif;

add_filter( 'wp_title', 'ttfmake_wp_title', 10, 2 );

/**
 * Sanitize a string to ensure that it is a float number.
 *
 * @since 1.5.0.
 *
 * @param  string|float    $value    The value to sanitize.
 * @return float                     The sanitized value.
 */
function ttfmake_sanitize_float( $value ) {
	return floatval( $value );
}

if ( ! function_exists( 'ttfmake_sanitize_text' ) ) :
/**
 * Allow only certain tags and attributes in a string.
 *
 * @since  1.0.0.
 *
 * @param  string    $string    The unsanitized string.
 * @return string               The sanitized string.
 */
function ttfmake_sanitize_text( $string ) {
	global $allowedtags;
	$expandedtags = $allowedtags;

	// span
	$expandedtags['span'] = array();

	// Enable id, class, and style attributes for each tag
	foreach ( $expandedtags as $tag => $attributes ) {
		$expandedtags[$tag]['id']    = true;
		$expandedtags[$tag]['class'] = true;
		$expandedtags[$tag]['style'] = true;
	}

	// br (doesn't need attributes)
	$expandedtags['br'] = array();

	/**
	 * Customize the tags and attributes that are allows during text sanitization.
	 *
	 * @since 1.4.3
	 *
	 * @param array     $expandedtags    The list of allowed tags and attributes.
	 * @param string    $string          The text string being sanitized.
	 */
	apply_filters( 'make_sanitize_text_allowed_tags', $expandedtags, $string );

	return wp_kses( $string, $expandedtags );
}
endif;

if ( ! function_exists( 'sanitize_hex_color' ) ) :
/**
 * Sanitizes a hex color.
 *
 * This is a copy of the core function for use when the customizer is not being shown.
 *
 * @since  1.0.0.
 *
 * @param  string         $color    The proposed color.
 * @return string|null              The sanitized color.
 */
function sanitize_hex_color( $color ) {
	if ( '' === $color ) {
		return '';
	}

	// 3 or 6 hex digits, or the empty string.
	if ( preg_match('|^#([A-Fa-f0-9]{3}){1,2}$|', $color ) ) {
		return $color;
	}

	return null;
}
endif;

if ( ! function_exists( 'sanitize_hex_color_no_hash' ) ) :
/**
 * Sanitizes a hex color without a hash. Use sanitize_hex_color() when possible.
 *
 * This is a copy of the core function for use when the customizer is not being shown.
 *
 * @since  1.0.0.
 *
 * @param  string         $color    The proposed color.
 * @return string|null              The sanitized color.
 */
function sanitize_hex_color_no_hash( $color ) {
	$color = ltrim( $color, '#' );

	if ( '' === $color ) {
		return '';
	}

	return sanitize_hex_color( '#' . $color ) ? $color : null;
}
endif;

if ( ! function_exists( 'maybe_hash_hex_color' ) ) :
/**
 * Ensures that any hex color is properly hashed.
 *
 * This is a copy of the core function for use when the customizer is not being shown.
 *
 * @since  1.0.0.
 *
 * @param  string         $color    The proposed color.
 * @return string|null              The sanitized color.
 */
function maybe_hash_hex_color( $color ) {
	if ( $unhashed = sanitize_hex_color_no_hash( $color ) ) {
		return '#' . $unhashed;
	}

	return $color;
}
endif;

if ( ! function_exists( 'ttfmake_excerpt_more' ) ) :
/**
 * Modify the excerpt suffix
 *
 * @since 1.0.0.
 *
 * @param string $more
 *
 * @return string
 */
function ttfmake_excerpt_more( $more ) {
	return ' &hellip;';
}
endif;

add_filter( 'excerpt_more', 'ttfmake_excerpt_more' );

if ( ! function_exists( 'ttfmake_get_view' ) ) :
/**
 * Determine the current view.
 *
 * For use with view-related theme options.
 *
 * @since  1.0.0.
 *
 * @return string    The string representing the current view.
 */
function ttfmake_get_view() {
	// Post types
	$post_types = get_post_types(
		array(
			'public' => true,
			'_builtin' => false
		)
	);
	$post_types[] = 'post';

	// Post parent
	$parent_post_type = '';
	if ( is_attachment() ) {
		$post_parent      = get_post()->post_parent;
		$parent_post_type = get_post_type( $post_parent );
	}

	$view = 'post';

	// Blog
	if ( is_home() ) {
		$view = 'blog';
	}
	// Archives
	else if ( is_archive() ) {
		$view = 'archive';
	}
	// Search results
	else if ( is_search() ) {
		$view = 'search';
	}
	// Posts and public custom post types
	else if ( is_singular( $post_types ) || ( is_attachment() && in_array( $parent_post_type, $post_types ) ) ) {
		$view = 'post';
	}
	// Pages
	else if ( is_page() || ( is_attachment() && 'page' === $parent_post_type ) ) {
		$view = 'page';
	}

	/**
	 * Allow developers to dynamically change the view.
	 *
	 * @since 1.2.3.
	 *
	 * @param string    $view                The view name.
	 * @param string    $parent_post_type    The post type for the parent post of the current post.
	 */
	return apply_filters( 'make_get_view', $view, $parent_post_type );
}
endif;

if ( ! function_exists( 'ttfmake_has_sidebar' ) ) :
/**
 * Determine if the current view should show a sidebar in the given location.
 *
 * @since  1.0.0.
 *
 * @param  string    $location    The location to test for.
 * @return bool                   Whether or not the location has a sidebar.
 */
function ttfmake_has_sidebar( $location ) {
	global $wp_registered_sidebars;

	// Validate the sidebar location
	if ( ! in_array( 'sidebar-' . $location, array_keys( $wp_registered_sidebars ) ) ) {
		return false;
	}

	// Get the view
	$view = ttfmake_get_view();

	// Get the relevant option
	$show_sidebar = (bool) get_theme_mod( 'layout-' . $view . '-sidebar-' . $location, ttfmake_get_default( 'layout-' . $view . '-sidebar-' . $location ) );

	// Builder template doesn't support sidebars
	if ( 'page' === $view && 'template-builder.php' === get_page_template_slug() ) {
		$show_sidebar = false;
	}

	/**
	 * Allow developers to dynamically changed the result of the "has sidebar" check.
	 *
	 * @since 1.2.3.
	 *
	 * @param bool      $show_sidebar    Whether or not to show the sidebar.
	 * @param string    $location        The location of the sidebar being evaluated.
	 * @param string    $view            The view name.
	 */

	return apply_filters( 'make_has_sidebar', $show_sidebar, $location, $view );
}
endif;

if ( ! function_exists( 'ttfmake_sidebar_description' ) ) :
/**
 * Output a sidebar description that reflects its current status.
 *
 * @since  1.0.0.
 *
 * @param  string    $sidebar_id    The sidebar to look up the description for.
 * @return string                   The description.
 */
function ttfmake_sidebar_description( $sidebar_id ) {
	$description = '';

	// Footer sidebars
	if ( false !== strpos( $sidebar_id, 'footer-' ) ) {
		$column = (int) str_replace( 'footer-', '', $sidebar_id );
		$column_count = (int) get_theme_mod( 'footer-widget-areas', ttfmake_get_default( 'footer-widget-areas' ) );

		if ( $column > $column_count ) {
			$description = __( 'This widget area is currently disabled. Enable it in the "Footer" panel of the Customizer.', 'make' );
		}
	}
	// Other sidebars
	else if ( false !== strpos( $sidebar_id, 'sidebar-' ) ) {
		$location = str_replace( 'sidebar-', '', $sidebar_id );

		$enabled_views = ttfmake_sidebar_list_enabled( $location );

		// Not enabled anywhere
		if ( empty( $enabled_views ) ) {
			$description = __( 'This widget area is currently disabled. Enable it in the "Content & Layout" panel of the Customizer.', 'make' );
		}
		// List enabled views
		else {
			$description = sprintf(
				__( 'This widget area is currently enabled for the following views: %s. Change this in the "Content & Layout" panel of the Customizer.', 'make' ),
				esc_html( implode( _x( ', ', 'list item separator', 'make' ), $enabled_views ) )
			);
		}
	}

	return esc_html( $description );
}
endif;

if ( ! function_exists( 'ttfmake_sidebar_list_enabled' ) ) :
/**
 * Compile a list of views where a particular sidebar is enabled.
 *
 * @since  1.0.0.
 *
 * @param  string    $location    The sidebar to look up.
 * @return array                  The sidebar's current locations.
 */
function ttfmake_sidebar_list_enabled( $location ) {
	$enabled_views = array();

	$views = array(
		'blog'    => __( 'Blog (Post Page)', 'make' ),
		'archive' => __( 'Archives', 'make' ),
		'search'  => __( 'Search Results', 'make' ),
		'post'    => __( 'Posts', 'make' ),
		'page'    => __( 'Pages', 'make' ),
	);

	foreach ( $views as $view => $label ) {
		$option = (bool) get_theme_mod( 'layout-' . $view . '-sidebar-' . $location, ttfmake_get_default( 'layout-' . $view . '-sidebar-' . $location ) );
		if ( true === $option ) {
			$enabled_views[] = $label;
		}
	}

	/**
	 * Filter the list of sidebars that are available for a specific location.
	 *
	 * @since 1.2.3.
	 *
	 * @param array    $enabled_views    The list of views enabled for the sidebar.
	 * @param string   $location         The location of the sidebar being evaulated.
	 */
	return apply_filters( 'make_sidebar_list_enabled', $enabled_views, $location );
}
endif;

if ( ! function_exists( 'ttfmake_get_social_links' ) ) :
/**
 * Get the social links from options.
 *
 * @since  1.0.0.
 *
 * @return array    Keys are service names and the values are links.
 */
function ttfmake_get_social_links() {
	// Define default services; note that these are intentionally non-translatable
	$default_services = array(
		'facebook-official' => array(
			'title' => 'Facebook',
			'class' => 'fa-facebook-official',
		),
		'twitter' => array(
			'title' => 'Twitter',
			'class' => 'fa-twitter',
		),
		'google-plus-square' => array(
			'title' => 'Google+',
			'class' => 'fa-google-plus-square',
		),
		'linkedin' => array(
			'title' => 'LinkedIn',
			'class' => 'fa-linkedin',
		),
		'instagram' => array(
			'title' => 'Instagram',
			'class' => 'fa-instagram',
		),
		'flickr' => array(
			'title' => 'Flickr',
			'class' => 'fa-flickr',
		),
		'youtube' => array(
			'title' => 'YouTube',
			'class' => 'fa-youtube',
		),
		'vimeo-square' => array(
			'title' => 'Vimeo',
			'class' => 'fa-vimeo-square',
		),
		'pinterest' => array(
			'title' => 'Pinterest',
			'class' => 'fa-pinterest',
		),
		'email' => array(
			'title' => __( 'Email', 'make' ),
			'class' => 'fa-envelope',
		),
		'rss' => array(
			'title' => __( 'RSS', 'make' ),
			'class' => 'fa-rss',
		),
	);

	// Set up the collector array
	$services_with_links = array();

	// Get the links for these services
	foreach ( $default_services as $service => $details ) {
		$url = get_theme_mod( 'social-' . $service, ttfmake_get_default( 'social-' . $service ) );
		if ( '' !== $url ) {
			$services_with_links[ $service ] = array(
				'title' => $details['title'],
				'url'   => esc_url( $url ),
				'class' => $details['class'],
			);
		}
	}

	// Special handling for RSS
	$hide_rss = (int) get_theme_mod( 'social-hide-rss', ttfmake_get_default( 'social-hide-rss' ) );
	if ( 0 === $hide_rss ) {
		$custom_rss = get_theme_mod( 'social-custom-rss', ttfmake_get_default( 'social-custom-rss' ) );
		if ( ! empty( $custom_rss ) ) {
			$services_with_links['rss']['url'] = esc_url( $custom_rss );
		} else {
			$services_with_links['rss']['url'] = get_feed_link();
		}
	} else {
		unset( $services_with_links['rss'] );
	}

	// Properly set the email
	if ( isset( $services_with_links['email']['url'] ) ) {
		$services_with_links['email']['url'] = esc_url( 'mailto:' . $services_with_links['email']['url'] );
	}

	/**
	 * Filter the social links added to the site.
	 *
	 * @since 1.2.3.
	 *
	 * @param array    $services_with_links    The social services and links.
	 */
	return apply_filters( 'make_social_links', $services_with_links );
}
endif;

if ( ! function_exists( 'ttfmake_pre_wp_nav_menu_social' ) ) :
/**
 * Alternative output for wp_nav_menu for the 'social' menu location.
 *
 * @since  1.0.0.
 *
 * @param  string    $output    Output for the menu.
 * @param  object    $args      wp_nav_menu arguments.
 * @return string               Modified menu.
 */
function ttfmake_pre_wp_nav_menu_social( $output, $args ) {
	if ( ! $args->theme_location || 'social' !== $args->theme_location ) {
		return $output;
	}

	// Get the menu object
	$locations = get_nav_menu_locations();
	$menu      = wp_get_nav_menu_object( $locations[ $args->theme_location ] );

	if ( ! $menu || is_wp_error( $menu ) ) {
		return $output;
	}

	$output = '';

	// Get the menu items
	$menu_items = wp_get_nav_menu_items( $menu->term_id, array( 'update_post_term_cache' => false ) );

	// Set up the $menu_item variables
	_wp_menu_item_classes_by_context( $menu_items );

	// Sort the menu items
	$sorted_menu_items = array();
	foreach ( (array) $menu_items as $menu_item ) {
		$sorted_menu_items[ $menu_item->menu_order ] = $menu_item;
	}

	unset( $menu_items, $menu_item );

	/**
	 * Filter the supported social icons.
	 *
	 * This array uses the url pattern for the key and the CSS class (as dictated by Font Awesome) as the array value.
	 * The URL pattern is used to match the URL used by a menu item.
	 *
	 * @since 1.2.3.
	 *
	 * @param array    $icons    The array of supported social icons.
	 */
	$supported_icons = apply_filters( 'make_supported_social_icons', array(
		'angel.co'           => 'fa-angellist',
		'app.net'            => 'fa-adn',
		'behance.net'        => 'fa-behance',
		'bitbucket.org'      => 'fa-bitbucket',
		'codepen.io'         => 'fa-codepen',
		'delicious.com'      => 'fa-delicious',
		'deviantart.com'     => 'fa-deviantart',
		'digg.com'           => 'fa-digg',
		'dribbble.com'       => 'fa-dribbble',
		'facebook.com'       => 'fa-facebook-official',
		'flickr.com'         => 'fa-flickr',
		'foursquare.com'     => 'fa-foursquare',
		'github.com'         => 'fa-github',
		'gittip.com'         => 'fa-gittip',
		'plus.google.com'    => 'fa-google-plus-square',
		'instagram.com'      => 'fa-instagram',
		'jsfiddle.net'       => 'fa-jsfiddle',
		'last.fm'            => 'fa-lastfm',
		'leanpub.com'        => 'fa-leanpub',
		'linkedin.com'       => 'fa-linkedin',
		'medium.com'         => 'fa-medium',
		'pinterest.com'      => 'fa-pinterest',
		'qzone.qq.com'       => 'fa-qq',
		'reddit.com'         => 'fa-reddit',
		'renren.com'         => 'fa-renren',
		'slideshare.net'     => 'fa-slideshare',
		'soundcloud.com'     => 'fa-soundcloud',
		'spotify.com'        => 'fa-spotify',
		'stackexchange.com'  => 'fa-stack-exchange',
		'stackoverflow.com'  => 'fa-stack-overflow',
		'steamcommunity.com' => 'fa-steam',
		'stumbleupon.com'    => 'fa-stumbleupon',
		't.qq.com'           => 'fa-tencent-weibo',
		'trello.com'         => 'fa-trello',
		'tumblr.com'         => 'fa-tumblr',
		'twitch.tv'          => 'fa-twitch',
		'twitter.com'        => 'fa-twitter',
		'vimeo.com'          => 'fa-vimeo-square',
		'vine.co'            => 'fa-vine',
		'vk.com'             => 'fa-vk',
		'weibo.com'          => 'fa-weibo',
		'weixin.qq.com'      => 'fa-weixin',
		'wordpress.com'      => 'fa-wordpress',
		'xing.com'           => 'fa-xing',
		'yahoo.com'          => 'fa-yahoo',
		'yelp.com'           => 'fa-yelp',
		'youtube.com'        => 'fa-youtube',
	) );

	// Process each menu item
	foreach ( $sorted_menu_items as $item ) {
		// Item classes
		$classes = ( isset( $item->classes ) && ! empty( $item->classes ) ) ? implode( ' ', (array) $item->classes ) : '';

		// Item target
		$target  = ( isset( $item->target ) && $item->target ) ? ' target="_blank"' : '';

		$item_output = '';

		// Look for matching icons
		foreach ( $supported_icons as $pattern => $class ) {
			if ( false !== strpos( $item->url, $pattern ) ) {
				$item_output .= '<li class="' . esc_attr( str_replace( 'fa-', '', $class ) ) . ' ' . esc_attr( $classes ) . '">';
				$item_output .= '<a href="' . esc_url( $item->url ) . '"' . $target . '>';
				$item_output .= '<i class="fa fa-fw ' . esc_attr( $class ) . '">';
				$item_output .= '<span>' . esc_html( $item->title ) . '</span>';
				$item_output .= '</i></a></li>';
				break;
			}
		}

		// No matching icons
		if ( '' === $item_output ) {
			$item_output .= '<li class="external-link-square ' . esc_attr( $classes ) . '">';
			$item_output .= '<a href="' . esc_url( $item->url ) . '"' . $target . '>';
			$item_output .= '<i class="fa fa-fw fa-external-link-square">';
			$item_output .= '<span>' . esc_html( $item->title ) . '</span>';
			$item_output .= '</i></a></li>';
		}

		// Add item to list
		$output .= $item_output;
		unset( $item_output );
	}

	// Email & RSS
	$customizer_links = ttfmake_get_social_links();
	if ( isset( $customizer_links['email'] ) ) {
		$output .= '<li class="email menu-item">';
		$output .= '<a href="' . esc_url( $customizer_links['email']['url'] ) . '">';
		$output .= '<i class="fa fa-fw fa-envelope">';
		$output .= '<span>' . esc_html( $customizer_links['email']['title'] ) . '</span>';
		$output .= '</i></a></li>';
	}
	if ( isset( $customizer_links['rss'] ) ) {
		$output .= '<li class="rss menu-item">';
		$output .= '<a href="' . esc_url( $customizer_links['rss']['url'] ) . '">';
		$output .= '<i class="fa fa-fw fa-rss">';
		$output .= '<span>' . esc_html( $customizer_links['rss']['title'] ) . '</span>';
		$output .= '</i></a></li>';
	}

	// If there are menu items, add a wrapper
	if ( '' !== $output ) {
		$output = '<ul class="' . esc_attr( $args->menu_class ) . '">' . $output . '</ul>';
	}

	return $output;
}
endif;

add_filter( 'pre_wp_nav_menu', 'ttfmake_pre_wp_nav_menu_social', 10, 2 );

if ( ! function_exists( 'ttfmake_get_section_data' ) ) :
/**
 * Retrieve all of the data for the sections.
 *
 * @since  1.2.0.
 *
 * @param  string    $post_id    The post to retrieve the data from.
 * @return array                 The combined data.
 */
function ttfmake_get_section_data( $post_id ) {
	$ordered_data = array();
	$ids          = get_post_meta( $post_id, '_ttfmake-section-ids', true );
	$ids          = ( ! empty( $ids ) && is_array( $ids ) ) ? array_map( 'strval', $ids ) : $ids;
	$post_meta    = get_post_meta( $post_id );

	// Temp array of hashed keys
	$temp_data = array();

	// Any meta containing the old keys should be deleted
	if ( is_array( $post_meta ) ) {
		foreach ( $post_meta as $key => $value ) {
			// Only consider builder values
			if ( 0 === strpos( $key, '_ttfmake:' ) ) {
				// Get the individual pieces
				$temp_data[ str_replace( '_ttfmake:', '', $key ) ] = $value[0];
			}
		}
	}

	// Create multidimensional array from postmeta
	$data = ttfmake_create_array_from_meta_keys( $temp_data );

	// Reorder the data in the order specified by the section IDs
	if ( is_array( $ids ) ) {
		foreach ( $ids as $id ) {
			if ( isset( $data[ $id ] ) ) {
				$ordered_data[ $id ] = $data[ $id ];
			}
		}
	}

	/**
	 * Filter the section data for a post.
	 *
	 * @since 1.2.3.
	 *
	 * @param array    $ordered_data    The array of section data.
	 * @param int      $post_id         The post ID for the retrieved data.
	 */
	return apply_filters( 'make_get_section_data', $ordered_data, $post_id );
}
endif;

if ( ! function_exists( 'ttfmake_create_array_from_meta_keys' ) ) :
/**
 * Convert an array with array keys that map to a multidimensional array to the array.
 *
 * @since  1.2.0.
 *
 * @param  array    $arr    The array to convert.
 * @return array            The converted array.
 */
function ttfmake_create_array_from_meta_keys( $arr ) {
	// The new multidimensional array we will return
	$result = array();

	// Process each item of the input array
	foreach ( $arr as $key => $value ) {
		// Store a reference to the root of the array
		$current = & $result;

		// Split up the current item's key into its pieces
		$pieces = explode( ':', $key );

		/**
		 * For all but the last piece of the key, create a new sub-array (if necessary), and update the $current
		 * variable to a reference of that sub-array.
		 */
		for ( $i = 0; $i < count( $pieces ) - 1; $i++ ) {
			$step = $pieces[ $i ];
			if ( ! isset( $current[ $step ] ) ) {
				$current[ $step ] = array();
			}
			$current = & $current[ $step ];
		}

		// Add the current value into the final nested sub-array
		$current[ $pieces[ $i ] ] = $value;
	}

	// Return the result array
	return $result;
}
endif;

if ( ! function_exists( 'ttfmake_post_type_supports_builder' ) ) :
/**
 * Check if a post type supports the Make builder.
 *
 * @since  1.2.0.
 *
 * @param  string    $post_type    The post type to test.
 * @return bool                    True if the post type supports the builder; false if it does not.
 */
function ttfmake_post_type_supports_builder( $post_type ) {
	return post_type_supports( $post_type, 'make-builder' );
}
endif;

if ( ! function_exists( 'ttfmake_is_builder_page' ) ) :
/**
 * Determine if the post uses the builder or not.
 *
 * @since  1.2.0.
 *
 * @param  int     $post_id    The post to inspect.
 * @return bool                True if builder is used for post; false if it is not.
 */
function ttfmake_is_builder_page( $post_id = 0 ) {
	if ( empty( $post_id ) ) {
		$post_id = get_the_ID();
	}

	// Pages will use the template-builder.php template to denote that it is a builder page
	$has_builder_template = ( 'template-builder.php' === get_page_template_slug( $post_id ) );

	// Other post types will use meta data to support builder pages
	$has_builder_meta = ( 1 === (int) get_post_meta( $post_id, '_ttfmake-use-builder', true ) );

	$is_builder_page = $has_builder_template || $has_builder_meta;

	/**
	 * Allow a developer to dynamically change whether the post uses the builder or not.
	 *
	 * @since 1.2.3
	 *
	 * @param bool    $is_builder_page    Whether or not the post uses the builder.
	 * @param int     $post_id            The ID of post being evaluated.
	 */
	return apply_filters( 'make_is_builder_page', $is_builder_page, $post_id );
}
endif;

if ( ! function_exists( 'ttfmake_builder_css' ) ) :
/**
 * Trigger an action hook for each section on a Builder page for the purpose
 * of adding section-specific CSS rules to the document head.
 *
 * @since 1.4.5
 *
 * @return void
 */
function ttfmake_builder_css() {
	if ( ttfmake_is_builder_page() ) {
		$sections = ttfmake_get_section_data( get_the_ID() );

		if ( ! empty( $sections ) ) {
			foreach ( $sections as $id => $data ) {
				if ( isset( $data['section-type'] ) ) {
					/**
					 * Allow section-specific CSS rules to be added to the document head of a Builder page.
					 *
					 * @since 1.4.5
					 *
					 * @param array    $data    The Builder section's data.
					 * @param int      $id      The ID of the Builder section.
					 */
					do_action( 'make_builder_' . $data['section-type'] . '_css', $data, $id );
				}
			}
		}
	}
}
endif;

add_action( 'make_css', 'ttfmake_builder_css' );

if ( ! function_exists( 'ttfmake_builder_banner_css' ) ) :
/**
 * Add frontend CSS rules for Banner sections based on certain section options.
 *
 * @since 1.4.5
 *
 * @param array    $data    The banner's section data.
 * @param int      $id      The banner's section ID.
 *
 * @return void
 */
function ttfmake_builder_banner_css( $data, $id ) {
	$responsive = ( isset( $data['responsive'] ) ) ? $data['responsive'] : 'balanced';
	$slider_height = absint( $data['height'] );
	if ( 0 === $slider_height ) {
		$slider_height = 600;
	}
	$slider_ratio = ( $slider_height / 960 ) * 100;

	if ( 'aspect' === $responsive ) {
		ttfmake_get_css()->add( array(
			'selectors'    => array( '#builder-section-' . esc_attr( $id ) . ' .builder-banner-slide' ),
			'declarations' => array(
				'padding-bottom' => $slider_ratio . '%'
			),
		) );
	} else {
		ttfmake_get_css()->add( array(
			'selectors'    => array( '#builder-section-' . esc_attr( $id ) . ' .builder-banner-slide' ),
			'declarations' => array(
				'padding-bottom' => $slider_height . 'px'
			),
		) );
		ttfmake_get_css()->add( array(
			'selectors'    => array( '#builder-section-' . esc_attr( $id ) . ' .builder-banner-slide' ),
			'declarations' => array(
				'padding-bottom' => $slider_ratio . '%'
			),
			'media'        => 'screen and (min-width: 600px) and (max-width: 960px)'
		) );
	}
}
endif;

add_action( 'make_builder_banner_css', 'ttfmake_builder_banner_css', 10, 2 );