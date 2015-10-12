<?php

/**
 * Hook into the template redirect and see if it's an archive loop.
 *
 * Use the select page (that contains a View) to display the loop items.
 *
 * @since unknown
 */
add_action('template_redirect', 'wpv_archive_redirect');

function wpv_archive_redirect() {

	global $WPV_view_archive_loop, $WPV_settings, $wp_query;
	$wpa_to_apply = 0;
	$wpa_slug = '';

	// See if we have a WPA for the home page
	if ( 
		is_home() 
		&& isset( $WPV_settings['view_home-blog-page'] ) 
		&& $WPV_settings['view_home-blog-page'] > 0 
	) {
		$wpa_to_apply = $WPV_settings['view_home-blog-page'];
		$wpa_slug = 'view_home-blog-page';
	}
	// Check if it's a post type archive and if we have a WPA for it
	if ( is_post_type_archive() ) {
		// From $wp_query->is_post_type_archive() using the same logic based on $wp_query->get('post_type')
		// Before 1.7, we checked against $wp_query->get_queried_object()->public and used $wp_query->get_queried_object()->name
		// But sometimes is_post_type_archive() is TRUE and $wp_query->get_queried_object() is not a post type object, but a post object
		// For example, on some scenarios for WooCommerce shop pages
		// In addition, we do not check now whether the post type is public or not: if it wasn't, there would not be a frontend archive for it
		$post_type = $wp_query->get( 'post_type' );
		if ( is_array( $post_type ) ) {
			$post_type = reset( $post_type );
		}
		if ( 
			isset( $WPV_settings['view_cpt_' . $post_type] ) 
			&& $WPV_settings['view_cpt_' . $post_type] > 0 
		) {
			$wpa_to_apply = $WPV_settings['view_cpt_' . $post_type];
			$wpa_slug = 'view_cpt_' . $post_type;
		}
	}
	// Check taxonomy loops
	if ( is_archive() ) {
		if ( 
			is_tax() 
			|| is_category() 
			|| is_tag() 
		) {
			$term = $wp_query->get_queried_object();
			if ( 
				$term 
				&& isset( $term->taxonomy )
				&& isset( $WPV_settings['view_taxonomy_loop_' . $term->taxonomy] ) 
				&& $WPV_settings['view_taxonomy_loop_' . $term->taxonomy] > 0 
			) {
				$wpa_to_apply = $WPV_settings['view_taxonomy_loop_' . $term->taxonomy];
				$wpa_slug = 'view_taxonomy_loop_' . $term->taxonomy;
			}
		}
	}
	// Check other archives
	if ( 
		is_search() 
		&& isset( $WPV_settings['view_search-page'] ) 
		&& $WPV_settings['view_search-page'] > 0 
	) {
		$wpa_to_apply = $WPV_settings['view_search-page'];
		$wpa_slug = 'view_search-page';
	}
	if ( 
		is_author() 
		&& isset( $WPV_settings['view_author-page'] ) 
		&& $WPV_settings['view_author-page'] > 0 
	) {
		$wpa_to_apply = $WPV_settings['view_author-page'];
		$wpa_slug = 'view_author-page';
	}
	if ( 
		is_year() 
		&& isset( $WPV_settings['view_year-page'] ) 
		&& $WPV_settings['view_year-page'] > 0 
	) {
		$wpa_to_apply = $WPV_settings['view_year-page'];
		$wpa_slug = 'view_year-page';
	}
	if ( 
		is_month() 
		&& isset( $WPV_settings['view_month-page'] ) 
		&& $WPV_settings['view_month-page'] > 0 
	) {
		$wpa_to_apply = $WPV_settings['view_month-page'];
		$wpa_slug = 'view_month-page';
	}
	if ( 
		is_day() 
		&& isset( $WPV_settings['view_day-page'] ) 
		&& $WPV_settings['view_day-page'] > 0 
	) {
		$wpa_to_apply = $WPV_settings['view_day-page'];
		$wpa_slug = 'view_day-page';
	}

	$wpa_to_apply = wpv_force_wordpress_archive( $wpa_to_apply, $wpa_slug );

	// If there is a WPA to apply, apply it
	if ( $wpa_to_apply > 0 ) {
        $wpa_status = get_post_status( $wpa_to_apply );
        // The WPA must be published ( not trashed )
        if ( $wpa_status == 'publish' ) {
            do_action( 'wpv_action_before_initialize_archive_loop', $wpa_to_apply, $wpa_slug );
            $WPV_view_archive_loop->initialize_archive_loop( $wpa_to_apply );
        }
    }

}


/**
 * wpv_force_wordpress_archive
 *
 * Applies the wpv_filter_force_wordpress_archive filter to the WPA ID to be displayed
 *
 * @param $wpa_to_apply (integer) the ID of the WPA we want to overwrite
 * @param $wpa_slug (string) [view_cpt_{post_slug}|view_taxonomy_loop_{taxonomy_slug}|view_home-blog-page|view_search-page
 *	 |view_author-page|view_year-page|view_month-page|view_day-page] the kind of WPA being processed
 *
 * @return (int) the ID of the WPA to apply_filters
 *
 * @since 1.6.0
 */
function wpv_force_wordpress_archive( $wpa_to_apply, $wpa_slug ) {

	/**
	 * Filter wpv_filter_force_wordpress_archive
	 *
	 * @param $wpa_to_apply (integer) the ID of the WPA we want to overwrite
	 * @param $wpa_slug (string) [view_cpt_{post_slug}|view_taxonomy_loop_{taxonomy_slug}|view_home-blog-page
	 *	 |view_search-page|view_author-page|view_year-page|view_month-page|view_day-page] the kind of WPA being processed.
	 *
	 * @return (int) the ID of the WPA to apply
	 *
	 * @since 1.6.0
	 */
	$wpa_to_apply = apply_filters( 'wpv_filter_force_wordpress_archive', $wpa_to_apply, $wpa_slug );
	return $wpa_to_apply;
}


/**
 * Generate the natural WordPress pagination link for previous page.
 *
 * Content of the shortcode will be used as a link label. It can contain other shortcodes.
 *
 * @since 1.7
 */ 
add_shortcode( 'wpv-archive-pager-prev-page', 'wpv_archive_pager_prev_page_shortcode' );

function wpv_archive_pager_prev_page_shortcode( $atts, $value ) {
	return get_next_posts_link( wpv_do_shortcode( $value ) );
}


/**
 * Generate the natural WordPress pagination link for next page.
 *
 * Content of the shortcode will be used as a link label. It can contain other shortcodes.
 *
 * @since 1.7
 */ 
add_shortcode( 'wpv-archive-pager-next-page', 'wpv_archive_pager_next_page_shortcode' );

function wpv_archive_pager_next_page_shortcode( $atts, $value ) {
	return get_previous_posts_link( wpv_do_shortcode( $value ) );
}


/**
 * @todo comment properly
 * @todo declare fields that are being declared dynamically in the code
 *
 * @since unknown
 */
class WP_Views_archive_loops {

	function __construct(){
		add_action( 'init', array( $this, 'init' ) );

		$this->header_started = false;
		$this->in_head = false;

		$this->in_the_loop = false;
		$this->loop_found = false;

		$this->loop_has_no_posts = false;
	}


	function __destruct(){

	}


	function init(){
		/*
		DEPRECATED, need some work to delete
		_get_post_type_loops
		*/

		/* 
		* ---------------------------------
		* Compatibility
		* ---------------------------------
		*/
		
		/*
		* WooCommerce
		*
		* Search results on product archive pages with just one result redirect to the product page
		* But if there are no results, the way we fake one dummy post breaks it all
		*
		* @since unknown
		*/
		
		add_action( 'wpv_action_before_initialize_archive_loop', array( $this, 'wpv_wpa_fix_woocommerce_archives' ), 10, 2 );

	}

	/**
	* initialize_archive_loop
	*
	* This will redirect to display the given post_id
	* The post will be displayed using the theme template selected for it
	* When a View is rendered it will use the posts from the current query
	*
	* @param (int) $post_id The ID of the WPA to initialize
	*
	* @since unknown
	*/
	
	function initialize_archive_loop( $post_id ) {
		global $wp_query;
		if ( ! have_posts() ) {
			// We need to handle empty loops and force the loop processing
			// Create a dummy WP_Post and set the post count to 1
			// That will fire the loop_start and loop_end hooks
			$wp_query->post_count = 1;
			$dummy_post_obj = (object) array(
				'ID' => $post_id,
				'post_author' => '1',
				'post_name' => '',
				'post_type' => '',
				'post_title' => '',
				'post_date' => '0000-00-00 00:00:00',
				'post_date_gmt' => '0000-00-00 00:00:00',
				'post_content' => '',
				'post_excerpt' => '',
				'post_status' => 'publish',
				'comment_status' => 'closed',
				'ping_status' => 'closed',
				'post_password' => '',
				'post_parent' => 0,
				'post_modified' => '0000-00-00 00:00:00',
				'post_modified_gmt' => '0000-00-00 00:00:00',
				'comment_count' => '0',
				'menu_order' => '0'
			);
			$dummy_post = new WP_Post( $dummy_post_obj );
			$wp_query->posts = array( $dummy_post );
			$this->loop_has_no_posts = true;
		}
		if ( have_posts() ) {
			$output_post = get_post( $post_id );
            if ( $output_post ) {
				// Save the original query.
				$this->query = ( $wp_query instanceof WP_Query ) ? clone $wp_query : null;
				$this->view_id = $post_id;
				add_action( 'loop_start', array( $this, 'loop_start' ), 1, 1 );
				add_action( 'loop_end', array( $this, 'loop_end' ), 999, 1 );
				add_action( 'get_header', array( $this, 'get_header' ) );
				// Stop the view being displayed in the head.
				// JetPack can cause this.
				add_action( 'wp_head', array( $this, 'html_head_start' ), -100 ); // try to load first
				add_action( 'wp_head', array( $this, 'html_head_end' ), 999 ); // try to load last
			}
		}
	}


	function get_archive_loop_query() {
		if ($this->in_the_loop) {
			return $this->query;
		} else {
			return null;
		}
	}


	function get_header($name) {
		$this->header_started = true;
	}


	function html_head_start() {
		$this->in_head = true;
	}


	function html_head_end() {
		$this->in_head = false;
	}


	function loop_start($query) {
		if (!$this->in_head && $this->header_started && ($query->query_vars_hash == $this->query->query_vars_hash || $query->request == $this->query->request)) {
			ob_start();
			$this->post_count = $query->post_count;
			$query->post_count = 1;
			$this->loop_found = true;
		}
	}


	function loop_end($query) {
		if ($this->loop_found) {
			ob_end_clean();

			if ($this->loop_has_no_posts) {
				// Reset everything if the loop has no posts.
				// Then the View will render with no posts.

				global $post, $wp_query;

				$this->post_count = 0;
				$this->query->post_count = 0;
				$wp_query->post_count = 0;

				$wp_query->posts = array();
				$this->query->posts = array();

				$post = null;
			}

			$query->post_count = $this->post_count;

			$this->in_the_loop = true;
			echo render_view(array('id' => $this->view_id));
			$this->in_the_loop = false;

			$this->loop_found = false;
		}

	}

    // TODO please try to use $this->get_archive_loops() instead.
	function _get_post_type_loops() {
		$loops = array('home-blog-page' => __('Home/Blog', 'wpv-views'),
					   'search-page' => __('Search results', 'wpv-views'),
					   'author-page' => __('Author archives', 'wpv-views'),
					   'year-page' => __('Year archives', 'wpv-views'),
					   'month-page' => __('Month archives', 'wpv-views'),
					   'day-page' => __('Day archives', 'wpv-views'));

		// Only offer loops for post types that already have an archive
		$post_types = get_post_types(array('public'=>true, 'has_archive' => true), 'objects');
		foreach($post_types as $post_type) {
			if (!in_array($post_type->name, array('post', 'page', 'attachment'))) {
				$type = 'cpt_' . $post_type->name;
				$name = $post_type->labels->name;
				$loops[$type] = $name;
			}
		}

		return $loops;
	}


	/**
	 * Get information about currently existing archive loops.
	 *
	 * @param string $loop_type Optional. Desired type of loops. 'native'|'post_type'|'taxonomy'|'all'. Default is 'all'.
	 * @param bool $include_wpa Optional. Determines whether the information about WPA assigned to this loop should be
	 *     retrieved (the $wpa element). Default is false.
     * @param bool $include_ct Optional. Determines whether the information about CT assigned to given post type archive
     *     or taxonomy archive should be retrieved (the $ct element). Default is false.
     * @param bool $noexclude Optional. If true, no loops of given type will be excluded. Default is false.
	 *
	 * @return array An array of information about native archive loops and loops for custom post types and taxonomies.
	 *     Each element is an array representing one loop:
	 *     array(
	 *         @type string $slug Unique slug (within loop type) as used in other parts of Views.
	 *         @type string $display_name Display name for the loop.
	 *         @type string $post_type_name For 'post_type' loop type, this will contain "raw" post type slug.
	 *         @type string $loop_type 'native'|'post_type'|'taxonomy'
	 *         @type int $wpa If $include_wpa is true, this contains an ID of WPA assigned to this loop, or zero if
	 *             no WPA is assigned.
     *         @type int $ct If $include_ct is true, this contains an ID of CT assigned to this custom post type
     *             archive or taxonomy archive, or zero if no CT is assigned. This element isn't present for native loops.
     *         @type int $single_ct If $include_ct is true, this contains an ID of CT assigned to single posts of
     *             this custom post type, or zero if no CT is assigned. This element is present only for post types.
	 *     )
	 *
	 * @since 1.7
	 *
     * @todo consider implementing caching mechanism
	 */  
	function get_archive_loops( $loop_type = 'all', $include_wpa = false, $include_ct = false, $noexclude = false ) {

		global $WPV_settings;
		
		switch( $loop_type ) {
		
			case 'native':
				$loops = array(
						array(
								'slug' => 'home-blog-page',
								'option' => 'view_home-blog-page',
								'loop_type' => 'native',
								'display_name' => __( 'Home/Blog', 'wpv-views' ) ),
						array(
								'slug' => 'search-page',
								'option' => 'view_search-page',
								'loop_type' => 'native',
								'display_name' => __( 'Search results', 'wpv-views' ) ),
						array(
								'slug' => 'author-page',
								'option' => 'view_author-page',
								'loop_type' => 'native',
								'display_name' => __( 'Author archives', 'wpv-views' ) ),
						array(
								'slug' => 'year-page',
								'option' => 'view_year-page',
								'loop_type' => 'native',
								'display_name' => __( 'Year archives', 'wpv-views' ) ),
						array(
								'slug' => 'month-page',
								'option' => 'view_month-page',
								'loop_type' => 'native',
								'display_name'  => __( 'Month archives', 'wpv-views' ) ),
						array(
								'slug' => 'day-page',
								'option' => 'view_day-page',
								'loop_type' => 'native',
								'display_name' => __( 'Day archives', 'wpv-views' ) ) );

				if( $include_wpa ) {
					$loop_count = count( $loops );
					for( $i = 0; $i < $loop_count; ++$i ) {
						$option = $loops[ $i ]['option'];
						$loops[ $i ]['wpa'] = isset( $WPV_settings[ $option ] ) ? $WPV_settings[ $option ] : 0;
					}
				}
				return $loops;

			case 'post_type':
			
				$pt_loops = array();
				// Only offer loops for post types that already have an archive, unless $noexclude is given
                $pt_query_args = array( 'public' => true );
                if( !$noexclude ) {
                    $pt_query_args['has_archive'] = true;
                }
				$post_types = get_post_types( $pt_query_args, 'objects' );

				foreach ( $post_types as $post_type ) {
					if ( $noexclude || !in_array( $post_type->name, array( 'post', 'page', 'attachment' ) ) ) {
					
						$loop = array(
								'slug' => 'cpt_' . $post_type->name,
								'post_type_name' => $post_type->name,
								'option' => 'view_cpt_' . $post_type->name,
								'display_name' => $post_type->labels->name,
                                'singular_name' => $post_type->labels->singular_name,
								'loop_type' => 'post_type' );

						if( $include_wpa ) {
							$loop['wpa'] = isset( $WPV_settings[ $loop['option'] ] ) ? $WPV_settings[ $loop['option'] ] : 0;
						}

                        if( $include_ct ) {
                            $loop['ct'] = wpv_getarr( $WPV_settings, "views_template_archive_for_{$post_type->name}", 0 );
                            $loop['single_ct'] = wpv_getarr( $WPV_settings, "views_template_for_{$post_type->name}", 0 );
                        }

						$pt_loops[] = $loop;
					}
				}

				return $pt_loops;

			case 'taxonomy':

				$tx_loops = array();
				$taxonomies = get_taxonomies( '', 'objects' );
				$exclude_tax_slugs = array();
                if( !$noexclude ) {
                    $exclude_tax_slugs = apply_filters('wpv_admin_exclude_tax_slugs', $exclude_tax_slugs);
                }
				foreach ( $taxonomies as $taxonomy_slug => $taxonomy ) {
					if ( in_array( $taxonomy_slug, $exclude_tax_slugs ) ) {
						continue;
					}
					// Only show taxonomies with show_ui set to TRUE
					if ( !$taxonomy->show_ui ) {
						continue;
					}
					
					$loop = array(
							'slug' => $taxonomy->name,
							'option' => 'view_taxonomy_loop_' . $taxonomy->name,
							'display_name' => $taxonomy->labels->singular_name,
							'loop_type' => 'taxonomy' );
							
					if( $include_wpa ) {
						$loop['wpa'] = isset( $WPV_settings[ $loop['option'] ] ) ? $WPV_settings[ $loop['option'] ] : 0;
					}

                    if( $include_ct ) {
                        $loop['ct'] = wpv_getarr( $WPV_settings, "views_template_loop_{$taxonomy->name}", 0 );
                    }
					
					$tx_loops[] = $loop;
				}
					
				return $tx_loops;

			case 'all':
			default:
				return array_merge(
						$this->get_archive_loops( 'native', $include_wpa ),
						$this->get_archive_loops( 'post_type', $include_wpa ),
						$this->get_archive_loops( 'taxonomy', $include_wpa ) );
		}
	}
	
	function _view_edit_options($view_id, $options) { // MAYBE DEPRECATED
		static $js_added = false;

		$title = '';
		if (isset($_GET['view_archive'])) {
			$options['view_' . $_GET['view_archive']] = $view_id;
			$loops = $this->_get_post_type_loops();
			$title = sprintf('%s-archive', $loops[$_GET['view_archive']]);
		}

		if (isset($_GET['view_archive_taxonomy'])) {
			$options['view_taxonomy_loop_' . $_GET['view_archive_taxonomy']] = $view_id;
			$taxonomies = get_taxonomies('', 'objects');
			$title = sprintf('%s-taxonomy-archive', $taxonomies[$_GET['view_archive_taxonomy']]->labels->name);
		}

		if ($title != '' && !$js_added) {
			// add some js to set the post title.

			?>
			<script type="text/javascript">
				jQuery(document).ready(function($){
					jQuery('#title').val('<?php echo esc_js($title); ?>');
				});
			</script>
			<?php
			$js_added = true;
		}

		return $options;
	}

	function _create_view_archive_popup( $view_id = 0 ) {
		global $WPV_settings;
		$loops = $this->_get_post_type_loops();
		$this->_view_edit_options( $view_id, $WPV_settings ); // TODO check if we just need the $options above
		$asterisk = ' <span style="color:red">*</span>';
		$asterisk_explanation = __( '<span style="color:red">*</span> A different WordPress Archive is already assigned to this item', 'wpv-views' );
		?>
		<div class="wpv-dialog wpv-shortcode-gui-content-wrapper wpv-dialog-change js-wpv-dialog-change js-wpv-dialog-wpa-manager">
			<form id="wpv-create-archive-view-form">
				<?php wp_nonce_field('wpv_view_edit_nonce', 'wpv_view_edit_nonce'); ?>
				<input type="hidden" value="<?php echo $view_id; ?>" name="wpv-archive-view-id" />
				<?php if ( $view_id == 0 ): ?>
					<strong><label for="wpv-new-archive-name"><?php _e('Name this WordPress Archive','wpv-views'); ?></label></strong>
					<input type="text" value="" class="js-wpv-new-archive-name wpv-new-archive-name" placeholder="<?php _e('WordPress Archive name','wpv-views') ?>" name="wpv-new-archive-name">
					<h3><?php _e('What loop will this Archive be used for?','wpv-views') ?></h3>
				<?php endif; ?>
				<?php
				$show_asterisk_explanation = false;
				$loops = array(
					'home-blog-page' => __('Home/Blog', 'wpv-views'),
					'search-page' => __('Search results', 'wpv-views'),
					'author-page' => __('Author archives', 'wpv-views'),
					'year-page' => __('Year archives', 'wpv-views'),
					'month-page' => __('Month archives', 'wpv-views'),
					'day-page' => __('Day archives', 'wpv-views')
				);
				?>

				<h4><?php _e('Standard Archives', 'wpv-views'); ?></h4>
				<ul>
					<?php foreach($loops as $loop => $loop_name): ?>
						<?php
						$show_asterisk = false;
						if ( isset( $WPV_settings['view_' . $loop] ) && $WPV_settings['view_' . $loop] != $view_id && $WPV_settings['view_' . $loop] != 0 ) {
							$show_asterisk = true;
							$show_asterisk_explanation = true;
						}
						?>
						<li>
							<input
								type="checkbox"
								<?php checked( $view_id > 0 && isset( $WPV_settings['view_' . $loop] ) && $WPV_settings['view_' . $loop] == $view_id ); ?>
								id="wpv-view-loop-<?php echo $loop; ?>"
								name="wpv-view-loop-<?php echo $loop; ?>"
								class="js-wpv-create-wpa-usage-checkbox"
								data-loop-name="<?php echo $loop_name; ?>"
								/>
							<label for="wpv-view-loop-<?php echo $loop; ?>"><?php echo $loop_name; echo $show_asterisk ? $asterisk : '';  ?></label>
						</li>
					<?php endforeach; ?>
				</ul>
				<?php if ( $show_asterisk_explanation ) { ?>
				<span class="wpv-asterisk-explanation">
					<?php echo $asterisk_explanation; ?>
				</span>
				<?php } ?>
				
				<?php
				$pt_loops = array();
				$show_asterisk_explanation = false;
				// Only offer loops for post types that already have an archive
				$post_types = get_post_types( array( 'public' => true, 'has_archive' => true), 'objects' );
				foreach ( $post_types as $post_type ) {
					if ( ! in_array( $post_type->name, array( 'post', 'page', 'attachment' ) ) ) {
						$type = 'cpt_' . $post_type->name;
						$name = $post_type->labels->name;
						$pt_loops[$type] = $name;
					}
				}
				if ( ! empty( $pt_loops ) ) { ?>
					<h4><?php _e( 'Custom Post Archives', 'wpv-views' ); ?></h4>
					<ul>
						<?php foreach ( $pt_loops as $loop => $loop_name ): ?>
							<?php
							$show_asterisk = false;
							$checked = ( $view_id > 0 && isset($WPV_settings['view_' . $loop] ) && $WPV_settings['view_' . $loop] == $view_id ) ? ' checked="checked"' : '';
							if ( isset( $WPV_settings['view_' . $loop] ) && $WPV_settings['view_' . $loop] != $view_id && $WPV_settings['view_' . $loop] != 0 ) {
								$show_asterisk = true;
								$show_asterisk_explanation = true;
							}
							?>
							<li>
								<input
                                    type="checkbox"
                                    <?php echo $checked; ?>
                                    id="wpv-view-loop-<?php echo $loop; ?>"
                                    name="wpv-view-loop-<?php echo $loop; ?>"
                                    class="js-wpv-create-wpa-usage-checkbox"
                                    data-loop-name="<?php echo $loop_name; ?>"
                                    />
								<label for="wpv-view-loop-<?php echo $loop; ?>"><?php echo $loop_name; echo $show_asterisk ? $asterisk : ''; ?></label>
							</li>
						<?php endforeach; ?>
					</ul>
					<?php if ( $show_asterisk_explanation ) { ?>
					<span class="wpv-asterisk-explanation">
						<?php echo $asterisk_explanation; ?>
					</span>
					<?php } ?>
				<?php } ?>

				<?php
				$show_asterisk_explanation = false;
				$taxonomies = get_taxonomies('', 'objects');
				$exclude_tax_slugs = array();
				$exclude_tax_slugs = apply_filters( 'wpv_admin_exclude_tax_slugs', $exclude_tax_slugs );
				foreach ($taxonomies as $category_slug => $category) {
					if ( 
						in_array($category_slug, $exclude_tax_slugs ) 
						|| ! $category->show_ui
					) {
						unset($taxonomies[$category_slug]);
						continue;
					}
				}
				if ( ! empty( $taxonomies ) ) { ?>
					<h4><?php _e('Taxonomy Archives', 'wpv-views'); ?></h4>
					<ul>
						<?php foreach ( $taxonomies as $category_slug => $category ): ?>
							<?php
								$name = $category->name;
								$show_asterisk = false;
								$checked = ( $view_id > 0 && isset( $WPV_settings['view_taxonomy_loop_' . $name ] ) && $WPV_settings['view_taxonomy_loop_' . $name ] == $view_id ) ? ' checked="checked"' : '';
								if ( isset( $WPV_settings['view_taxonomy_loop_' . $name ] ) && $WPV_settings['view_taxonomy_loop_' . $name ] != $view_id && $WPV_settings['view_taxonomy_loop_' . $name ] != 0 ) {
									$show_asterisk = true;
									$show_asterisk_explanation = true;
								}
							?>
							<li>
								<input
                                    type="checkbox"
                                    <?php echo $checked; ?>
                                    id="wpv-view-taxonomy-loop-<?php echo $name; ?>"
                                    name="wpv-view-taxonomy-loop-<?php echo $name; ?>"
                                    class="js-wpv-create-wpa-usage-checkbox"
                                    data-loop-name="<?php echo $category->labels->name; ?>"
                                    />
								<label for="wpv-view-taxonomy-loop-<?php echo $name; ?>"><?php echo $category->labels->name; echo $show_asterisk ? $asterisk : ''; ?></label>
							</li>
						<?php endforeach; ?>
					</ul>
					<?php if ( $show_asterisk_explanation ) { ?>
					<span class="wpv-asterisk-explanation">
						<?php echo $asterisk_explanation; ?>
					</span>
					<?php } ?>
				<?php } ?>
				<div class="js-wpv-error-container"></div>
			</form>
		</div>
		<?php
	}


	public function check_archive_loops_exists() {
		global $WPV_settings;

		$loops = $this->_get_post_type_loops();

		foreach($loops as $loop => $loop_name) {
			foreach ($WPV_settings as $opt_id=> $opt_name) {

				if ('view_'.$loop == $opt_id && $opt_name !== 0) {

					unset($loops[$loop]);
					break;
				}
			}
		}

		$taxonomies = get_taxonomies('', 'objects');
		$exclude_tax_slugs = array();
		$exclude_tax_slugs = apply_filters( 'wpv_admin_exclude_tax_slugs', $exclude_tax_slugs );

		foreach ($taxonomies as $category_slug => $category) {

			if ( in_array( $category_slug, $exclude_tax_slugs ) ) {
				unset($taxonomies[$category_slug]);
				continue;
			}
			if ( !$category->show_ui ) {
				unset($taxonomies[$category_slug]);
				continue; // Only show taxonomies with show_ui set to TRUE
			}

			foreach ( $WPV_settings as $opt_id => $opt_name ) {

                if ('view_taxonomy_loop_' . $category_slug == $opt_id && $opt_name !== 0) {

					unset($taxonomies[$category_slug]);
					break;
				}
			}
		}


		return !(empty($loops) && empty($taxonomies));
	}


	function update_view_archive_settings( $post_id, $data ) {
		global $WPV_settings;

		$found = false;

		// clear existing ones
		$loops = $this->_get_post_type_loops();
		foreach ($loops as $type => $name) {
			if (isset($WPV_settings['view_' . $type]) && $WPV_settings['view_' . $type] == $post_id) {
				unset($WPV_settings['view_' . $type]);
				$found = true;
			}
		}
		$taxonomies = get_taxonomies('', 'objects');
		foreach ($taxonomies as $category_slug => $category) {
			if (isset($WPV_settings['view_taxonomy_loop_' . $category_slug]) && $WPV_settings['view_taxonomy_loop_' . $category_slug] == $post_id) {
				unset($WPV_settings['view_taxonomy_loop_' . $category_slug]);
				$found = true;
			}
		}

		foreach ( $data as $key => $value ) {
			$key = sanitize_text_field( $key );
			if ( strpos( $key, 'wpv-view-loop-' ) === 0 ) {
				preg_match( '/wpv-view-loop-(.*)/', $key, $out );
				$WPV_settings['view_' . $out[1]] = $post_id;
				$found = true;
			}
			if ( strpos( $key, 'wpv-view-taxonomy-loop-' ) === 0 ) {
				$WPV_settings['view_taxonomy_loop_' . substr( $key, 23 )] = $post_id;
				$found = true;
			}
		}
        
        $WPV_settings->refresh_view_settings_data();
        
		if ($found) {
            $WPV_settings->save();
		}
	}

	function wpv_wpa_fix_woocommerce_archives( $wpa_to_apply, $wpa_slug ) {
		global $post, $wp_query;
		if ( ! have_posts() ) {
			add_filter( 'woocommerce_redirect_single_search_result', '__return_false' );
		}
	}

}


global $WPV_view_archive_loop;
$WPV_view_archive_loop = new WP_Views_archive_loops;

