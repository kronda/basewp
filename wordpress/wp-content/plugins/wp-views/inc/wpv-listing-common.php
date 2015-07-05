<?php

/**
 * Common code for listing pages.
 *
 * Also dialog box templates for listing pages.
 */

/* ************************************************************************** *\
		Common code for listing pages
\* ************************************************************************** */

/**
 * Modify arguments for WP_Query on listing pages when searching for a string in Views, Content Templates
 * or WordPress Archives.
 *
 * This function will search for given string in titles and descriptions. It returns a modified array of arguments
 * for the "final" query on a listing page with the "post__in" argument containing array View/CT/WPA IDs where matching
 * string was found.
 *
 * Post meta key containing description will be determined from 'post_type' argument in $wpv_args.
 *
 * @param string $s Searched string (will be sanitized).
 * @param array $wpv_args Arguments for the listing WP_Query. They must contain the 'post_type' key with value
 *     either 'view' or 'view-template'.
 *
 * @return array Modified $wpv_args for the listing query.
 *
 * @since 1.7
 */
function wpv_modify_wpquery_for_search( $s, $wpv_args ) {

	$s_param = urldecode( sanitize_text_field( $s ) );
	$results = array();

	$search_args = $wpv_args;
	$search_args['posts_per_page'] = '-1'; // return all posts
	$search_args['fields'] = 'ids'; // return only post IDs

	// First, search in post titles
	$titles_search_args = $search_args;
	$titles_search_args['s'] = $s_param;

	$query_titles = new WP_Query( $titles_search_args );
	$title_results = $query_titles->get_posts();
	if( !is_array( $title_results ) ) {
		$title_results = array();
	}

	// Now search in description.

	// Determine description meta_key based on post type.
	$description_key = '';
	switch( $wpv_args['post_type'] ) {
		// This covers both Views and WPAs.
		case 'view':
			$description_key = '_wpv_description';
			break;
		// Content templates.
		case 'view-template':
			$description_key = '_wpv-content-template-decription';
			break;
	}

	$description_search_args = $search_args;
	$description_search_args['meta_query'] = array(
			array(
				'key' => $description_key,
				'value' => $s_param,
				'compare' => 'LIKE' ) );

	$query_description = new WP_Query( $description_search_args );
	$description_results = $query_description->get_posts();
	if( !is_array( $description_results ) ) {
		$description_results = array();
	}

	// Merge results from both queries.
	$results = array_unique( array_merge( $title_results, $description_results ) );

	// Modify arguments for the final query
	if ( count( $results ) == 0 ) {
		$wpv_args['post__in'] = array( '0' );
	} else {
		$wpv_args['post__in'] = $results;
	}

	return $wpv_args;
}


/**
 * Render controls for bulk actions on listing pages.
 *
 * Renders a select field and an Apply (submit) button in a 'bulkactions' div tag.
 *
 * @since 1.7
 *
 * @param array $actions Array of bulk actions to offer. Keys are action slugs, values are names to be shown to the user.
 * @param string $class Base name for the class attribute of rendered elements. Select field will have class
 *     "{$class}-select" and submit button "{$class}-submit".
 * @param array $submit_button_attributes An key-value array of additional attributes that will be added to the submit button.
 * @param string $position Position of bulk action fields. Usually they are rendered twice on a page, on the top and
 *     after the listing. This value is added as another class (specifically "position-{$position}") to the select and
 *     the submit button. It is used to determine the matching select field after user clicks on a submit button.
 *
 * @return Rendered HTML code.
 */
function wpv_admin_table_bulk_actions( $actions, $class, $submit_button_attributes = array(), $position = 'top' ) {
	$out = '<div class="alignleft actions bulkactions">';

	$out .= sprintf( '<select class="%s">', $class . '-select position-' . $position );
	$out .= sprintf( '<option value="-1" selected="selected">%s</option>', __( 'Bulk Actions', 'wpv-views' ) );

	foreach( $actions as $action => $label ) {
		$out .= sprintf( '<option value="%s">%s</option>', $action, $label );
	}
	$out .= '</select> ';

	$submit_button_attributes_flat = '';
	foreach( $submit_button_attributes as $attribute => $value ) {
		$submit_button_attributes_flat .= sprintf( ' %s="%s" ', $attribute, $value );
	}

	$out .= sprintf(
			'<input type="submit" value="%s" class="%s" data-position="%s" %s />',
			__( 'Apply', 'wpv-views' ),
			'button button-secondary ' . $class . '-submit',
			$position,
			$submit_button_attributes_flat );

	$out .= '</div>';
	return $out;
}


/**
 * Generate row actions div.
 *
 * Taken from the WP_List_Table WordPress core class.
 *
 * @since 1.7
 *
 * @link https://core.trac.wordpress.org/browser/tags/4.0/src//wp-admin/includes/class-wp-list-table.php#L443
 *
 * @param array $actions List of actions. Action can be an arbitrary HTML code, while key of the element will be used
 * as a class of the wrapping span tag (so it may contain multiple class names separated by space).
 * @param array $custom_attributes List of custom attributes (key-value pairs) to be added to the wrapping span tag.
 * @param bool $always_visible Whether the actions should be always visible.
 *
 * @return string HTML code of the row actions div or empty string if no actions are provided.
 */
function wpv_admin_table_row_actions( $actions, $custom_attributes = array(), $always_visible = false ) {
	$action_count = count( $actions );
	$i = 0;

	if ( !$action_count ) {
		return '';
	}

	$custom_attributes_flat = array();
	foreach( $custom_attributes as $attr => $value ) {
		$custom_attributes_flat[] = sprintf( '%s="%s"', $attr, $value );
	}
	$custom_attributes_string = implode( ' ', $custom_attributes_flat );

	$out = '<div class="' . ( $always_visible ? 'row-actions visible' : 'row-actions' ) . '">';
	foreach ( $actions as $action => $link ) {
		++$i;
		( $i == $action_count ) ? $sep = '' : $sep = ' | ';
		$out .= "<span class='$action' $custom_attributes_string>$link$sep</span>";
	}
	$out .= '</div>';

	return $out;
}


/**
 * Optionaly render a message on a listing page.
 *
 * If a given URL parameter is present that indicates a finished action, show a message. Value of this parameter is
 * supposed to be a number of affected posts.
 *
 * If more than one post was affected, a plural message is shown, otherwise a singular one.
 * Plural message is expected to contain one "%d" placeholder for the number of affected posts.
 *
 * This function also looks for a list of affected IDs (as comma-separated values in an URL parameter) and
 * if $has_undo is true, the filter wpv_maybe_show_listing_message_undo is applied to obtain an Undo link for
 * this action.
 *
 * The message will appear below the h2 tag.
 *
 * @since 1.7
 *
 * @param string $message_name Name of the URL parameter indicating this message should be rendered.
 * @param string $text_singular Message text that will be echoed when one post was affected.
 * @param string $text_plural Message text that will be echoed when more posts were affected.
 * @param bool $has_undo Indicates whether a filter should be applied to obtain an Undo link. Default is false.
 * @param string $affected_id_arg Name of the URL parameter possibly containing IDs of affected posts.
 */
function wpv_maybe_show_listing_message( $message_name, $text_singular, $text_plural, $has_undo = false, $affected_id_arg = 'affected' ) {

	if ( isset( $_GET[ $message_name ] ) ) {

		// Number of affected posts
		$message_value = $_GET[ $message_name ];
		// IDs of affected posts (if set)
		$affected_ids = isset( $_GET[ $affected_id_arg ] ) ? explode( ',', $_GET[ $affected_id_arg ] ) : array( );

		if( $has_undo ) {

			/**
			 * Construct an "Undo" link for a message on listing page.
			 *
			 * Resulting string will be appended after message text.
			 *
			 * @since 1.7
			 *
			 * @param string $undo_html An Undo link to be appended after the message.
			 * @param string $message_name Name of the message as it was passed to wpv_maybe_show_listing_message().
			 * @param array $affected_ids IDs of posts affected by the action.
			 */
			$undo = ' ' . apply_filters( 'wpv_maybe_show_listing_message_undo', '', $message_name, $affected_ids );

		} else {
			$undo = '';
		}

		// Choose the appropriate message text.
		if( $message_value > 1 ) {
			$text = sprintf( $text_plural, $message_value );
		} else {
			$text = $text_singular;
		}

		$text .= $undo;


		?>
		<div id="message" class="updated below-h2">
			<p><?php echo $text ?></p>
		</div>
		<?php
	}
}


/**
 * Display pagination in admin listing pages.
 *
 * @param string $context the admin page where it will be rendered: 'views', 'view-templates' or 'view-archives'.
 * @param int $wpv_found_items
 * @param int $wpv_items_per_page
 * @param array $mod_url
*/
function wpv_admin_listing_pagination( $context = 'views', $wpv_found_items, $wpv_items_per_page = WPV_ITEMS_PER_PAGE, $mod_url = array() ) {
	$page = ( isset( $_GET["paged"] ) ) ? (int) $_GET["paged"] : 1;
	$pages_count = ceil( (int) $wpv_found_items / (int) $wpv_items_per_page );

	if ( $pages_count > 1 ) {

		$items_start = ( ( ( $page - 1 ) * (int) $wpv_items_per_page ) + 1 );
		$items_end = ( ( ( $page - 1 ) * (int) $wpv_items_per_page ) + (int) $wpv_items_per_page );

		if ( $page == $pages_count ) {
			$items_end = $wpv_found_items;
		}

		$mod_url_defaults = array(
				'orderby' => '',
				'order' => '',
				'search' => '',
				'items_per_page' => '',
				'status' => '',
				's' => '' );
		$mod_url = wp_parse_args( $mod_url, $mod_url_defaults );

		?>
		<div class="wpv-listing-pagination tablenav">
			<div class="tablenav-pages">
				<span class="displaying-num">
					<?php _e( 'Displaying ', 'wpv-views' ); echo $items_start; ?> - <?php echo $items_end; _e(' of ', 'wpv-views'); echo $wpv_found_items; ?>
				</span>

				<?php

					if ( $page > 1 ) {
						printf(
								'<a href="%s" class="wpv-filter-navigation-link">&laquo; %s</a>',
								wpv_maybe_add_query_arg(
										array(
												'page' => $context,
												'orderby' => $mod_url['orderby'],
												'order' => $mod_url['order'],
												'search' => $mod_url['search'],
												'items_per_page' => $mod_url['items_per_page'],
												'status' => $mod_url['status'],
												'paged' => $page - 1,
												's' => $mod_url['s'] ),
										admin_url( 'admin.php' ) ),
								__( 'Previous page','wpv-views' ) );
					}

					for ( $i = 1; $i <= $pages_count; $i++ ) {
						$active = 'wpv-filter-navigation-link-inactive';
						if ( $page == $i ) {
							$active = 'js-active active current';
						}

						// If this is a last page, we'll add an argument indicating that.
						$is_last_page = ( $i == $pages_count );

						printf(
								'<a href="%s" class="%s">%s</a>',
								wpv_maybe_add_query_arg(
										array(
												'page' => $context,
												'orderby' => $mod_url['orderby'],
												'order' => $mod_url['order'],
												'search' => $mod_url['search'],
												'items_per_page' => $mod_url['items_per_page'],
												'status' => $mod_url['status'],
												'paged' => $i,
												'last_page' => $is_last_page ? '1' : '',
												's' => $mod_url['s'] ),
										admin_url( 'admin.php' ) ),
								$active,
								$i );
					}

					if ( $page < $pages_count ) {

						$is_last_page = ( ( $page + 1 )  == $pages_count );

						printf(
								'<a href="%s" class="wpv-filter-navigation-link">%s &raquo;</a>',
								wpv_maybe_add_query_arg(
										array(
												'page' => $context,
												'orderby' => $mod_url['orderby'],
												'order' => $mod_url['order'],
												'search' => $mod_url['search'],
												'items_per_page' => $mod_url['items_per_page'],
												'status' => $mod_url['status'],
												'paged' => $page + 1,
												'last_page' => $is_last_page ? '1' : '',
												's' => $mod_url['s'] ),
										admin_url( 'admin.php' ) ),
								__( 'Next page','wpv-views' ) );
					}

				?>

				<?php _e( 'Items per page', 'wpv-views' ); ?>
				<select class="js-items-per-page">
					<option value="10" <?php selected( $wpv_items_per_page == '10' ); ?> >10</option>
					<option value="20" <?php selected( $wpv_items_per_page == '20' ); ?> >20</option>
					<option value="50" <?php selected( $wpv_items_per_page == '50' ); ?> >50</option>
				</select>
				<a href="#" class="js-wpv-display-all-items"><?php _e( 'Display all items', 'wpv-views' ); ?></a>

			</div><!-- .tablenav-pages -->
		</div><!-- .wpv-listing-pagination -->
	<?php } else if ( ( WPV_ITEMS_PER_PAGE != $wpv_items_per_page ) && ( $wpv_found_items > WPV_ITEMS_PER_PAGE ) ) { ?>
		<div class="wpv-listing-pagination tablenav">
			<div class="tablenav-pages">
				<a href="#" class="js-wpv-display-default-items"><?php _e('Display 20 items per page', 'wpv-views'); ?></a>
			</div><!-- .tablenav-pages -->
		</div><!-- .wpv-listing-pagination -->
	<?php }
}


/**
 * Prepare data for querying Views or WordPress Archives.
 *
 * Because Views and WPAs have the same post type and the information about this "query mode" is stored in a serialized
 * array in postmeta, we have to allways get all views (here views = posts of type "view"), parse it's settings from
 * postmeta (which is more complicated than it seems, see $WP_Views->get_view_settings()) and decide whether to
 * include it in possible results of the final query (that handles things like sorting, ordering, pagination).
 *
 * From all the possible results, we also need to count how many of them are published and trashed, because those numbers
 * also show up on listing pages.
 *
 * We can do all this with one query that will get all IDs, post status and the postmeta with serialized settings. Then,
 * based on post status and query mode, this function will produce an array of IDs of possible results.
 *
 * @param string|array $view_query_mode Query mode (kind of View object). It can be one string value or multiple values
 *     in an array. Usual values are 'normal' (for a View) or 'archive' (for a WPA), however there is also a deprecated
 *     value 'layouts-loop' for WPAs. @todo update this
 * @param string $listed_post_status Post status that is going to be queried: 'publish' or 'trash'.
 * @param array $additional_fields Optional. Additional fields to be queried from the database. Keys must be valid
 *     column names and values are their aliases. Makes sense only with $return_rows = true.
 * @param bool $return_rows Optional. If set to true, returned array will also contain the 'rows' element.
 * @param array $additional_where Optional. An array of additional conditions for the WHERE clause.
 *
 * @return array {
 *     @type int published_count Count of published posts of given query mode.
 *     @type int trashed_count Count of trashed posts of given query mode.
 *     @type int total_count Count of published+trashed posts of given query mode.
 *     @type array post__in An array of post IDs that have the right query mode and post status.
 *     @type array rows If $return_rows is true, this will contain the database results for views accepted in post__in.
 * }
 *
 * @since 1.7
 */
function wpv_prepare_view_listing_query( $view_query_mode, $listed_post_status,
		$additional_fields = array(), $return_rows = false, $additional_where = array() ) {
	global $wpdb, $WP_Views;

	// Build a string for SELECT from default and additional fields.
	$select = array(
			'ID AS id',
			'posts.post_status AS post_status',
			'postmeta.meta_value AS view_settings' );
	foreach( $additional_fields as $field => $alias ) {
		$select[] = "$field AS $alias";
	}
	$select_string = implode( ', ', $select );

	// Build a string for WHERE from default and additional conditions.
	$where = array(
			"posts.post_type = 'view'",
			"post_status IN ( 'publish', 'trash' )" );
	$where = array_merge( $where, $additional_where );
	$where_string = implode( ' AND ', $where );

	/* Queries rows with post id, status, value of _wpv_settings meta (or null if it doesn't exist, notice the LEFT JOIN)
	 * and additional fields. */
	$query = "SELECT {$select_string}
			FROM {$wpdb->posts} AS posts
				LEFT JOIN {$wpdb->postmeta} AS postmeta
				ON ( posts.ID = postmeta.post_id AND postmeta.meta_key = '_wpv_settings' )
			WHERE ( {$where_string} )";
	$views = $wpdb->get_results( $query );

	$published_count = 0;
	$trashed_count = 0;
	$post_in = array();

	// This will hold rows from the database if $return_rows is true.
	$rows = array();

	if( !is_array( $view_query_mode ) ) {
		$view_query_mode = array( $view_query_mode );
	}

	/* For each result we need to determine if it's a View or a WPA. If it's what we want, decide by
	 * it's post_status which counter to increment and whether to include into post__in (that means possible result
	 * in the final listing query). */
	foreach( $views as $view ) {

		// Prepare the value of _wpv_settings postmeta in the same way get_post_meta( ..., ..., true ) would.
		// If we don't get a value that makes sense, we just fall back to what would get_view_settings() do.
		$meta_value = ( null == $view->view_settings ) ? null: maybe_unserialize( $view->view_settings );

		// Get View settings without touching database again
		$view_settings = $WP_Views->get_view_settings( $view->id, $meta_value );

		// It is the right kind of View?
		if ( in_array( $view_settings['view-query-mode'], $view_query_mode ) ) {

			// Update counters
			if( 'publish' == $view->post_status ) {
				++$published_count;
			} else {
				// Now post_status can be only 'trash' because of the condition in mysql query
				++$trashed_count;
			}

			if( $listed_post_status == $view->post_status ) {
				// This is a possible result of the final listing query
				$post_in[] = $view->id;
				if( $return_rows ) {
					$rows[] = $view;
				}
			}
		}
	}

	// If there are no results, we don't want any post to match anything in post__in.
	if( count( $post_in ) == 0 ) {
		$post_in[] = 0;
	}

	$ret = array(
			'published_count' => $published_count,
			'trashed_count' => $trashed_count,
			'total_count' => $published_count + $trashed_count,
			'post__in' => $post_in );
	if( $return_rows ) {
		$ret['rows'] = $rows;
	}

	return $ret;
}


/**
 * Generates an Undo link for the 'trashed' message on Views / WordPress Archives listing.
 *
 * @since 1.7
 * 
 * @see wpv_maybe_show_listing_message_undo filter.
 */ 
function wpv_admin_view_listing_message_undo( $undo_link, $message_name, $affected_ids ) {
	if( ( 'trashed' == $message_name ) && !empty( $affected_ids ) ) {
		$undo_link = sprintf( '<a href="%s"	class="js-wpv-untrash" data-ids="%s" data-nonce="%s">%s</a>',
				add_query_arg( array( 'page' => 'views', 'untrashed' => count( $affected_ids ) ), admin_url( 'admin.php' ) ),
				urlencode( implode( ',', $affected_ids ) ),
				wp_create_nonce( 'wpv_view_listing_actions_nonce' ),
				__( 'Undo', 'wpv-views' ) );
	}
	return $undo_link;
}



/* ************************************************************************** *\
		Dialog templates
\* ************************************************************************** */


/**
 * @todo comment
 */ 
function wpv_render_ct_listing_dialog_templates_arrangeby_usage() {
	?>
	<div class="popup-window-container"> <!-- placeholder for static colorbox popups -->

		<!-- popup: unlink Template -->
		<div class="wpv-dialog js-single-unlink-template-dialog">
			<div class="wpv-dialog-header">
				<h2><?php echo sprintf( __('Clear single %s','wpv-views'), '<strong class="js-single-unlink-label"></strong>'); ?></h2>
			</div>
			<div class="wpv-dialog-content">
				<p><?php echo sprintf( __('There is no general Content Template asigned to single %s, but %s individual %s have a Content Template asigned to them.','wpv-views'), '<strong class="js-single-unlink-label"></strong>', '<strong class="js-single-unlink-number"></strong>', '<strong class="js-single-unlink-label"></strong>'); ?></p>
				<p><?php echo __('Would you like to clear them?','wpv-views'); ?></p>
			</div>
			<div class="wpv-dialog-footer">
				<button class="button js-dialog-close"><?php _e('Cancel','wpv-views') ?></button>
				<button class="button button-primary js-single-unlink-template-ok" data-slug="" data-nonce="<?php echo wp_create_nonce( 'wpv_single_unlink_template_nonce' ); ?>"><?php _e('Clear','wpv-views') ?></button>
			</div>
		</div>

	</div>
	<?php
}


/**
 * @todo comment
 */ 
function wpv_render_ct_listing_dialog_templates_arrangeby_name() {
	?>
	<div class="popup-window-container">

		<div class="wpv-dialog js-bulk-remove-content-templates-dialog">
			<div class="wpv-dialog-header">
				<h2>
					<span class="js-plural"><?php _e( 'Delete Content Templates', 'wpv-views' ); ?></span>
					<span class="js-singular"><?php _e( 'Delete Content Template', 'wpv-views' ); ?></span>
				</h2>
			</div>
			<div class="wpv-dialog-content">
				<p class="js-ct-single-postcount-message-usage">
					<span class="js-plural">
						<?php
							printf(
									__( 'There are %s single posts that are currently using some of these templates.','wpv-views'),
									'<span class="js-ct-single-postcount"></span>' );
						?>
					</span>
					<span class="js-singular">
						<?php
							printf(
									__( 'There are %s single posts that are currently using this template.','wpv-views'),
									'<span class="js-ct-single-postcount"></span>' );
						?>
					</span>
				</p>
				<p class="js-ct-single-postcount-message-ays-nonzero">
					<span class="js-plural">
						<?php _e( 'Are you sure you want to permanently delete them?', 'wpv-views' ); ?>
					</span>
					<span class="js-singular">
						<?php _e( 'Are you sure you want to permanently delete it?', 'wpv-views' ); ?>
					</span>
				</p>
				<p class="js-ct-single-postcount-message-ays-zero" style="display: none;">
					<span class="js-plural">
						<?php _e( 'Are you sure you want to permanently delete selected content templates?', 'wpv-views' ); ?>
					</span>
					<span class="js-singular">
						<?php _e( 'Are you sure you want to permanently delete selected content template?', 'wpv-views' ); ?>
					</span>
				</p>
			</div>
			<div class="wpv-dialog-footer">
				<button class="button js-dialog-close"><?php _e( 'Cancel','wpv-views' ); ?></button>
				<button class="button button-primary js-bulk-remove-templates-permanent"><?php _e( 'Delete', 'wpv-views' ); ?></button>
			</div>
		</div>

		<?php
			wpv_render_duplicate_dialog( 'ct' );
		?>

	</div>
	<?php
}


/**
 * @todo comment
 */ 
function wpv_render_view_listing_dialog_templates() {
	?>
	<div class="popup-window-container"> <!-- placeholder for static colorbox popups -->

		<!-- popup: create View -->
		<div class="wpv-dialog create-view-form-dialog js-create-view-form-dialog">
			<?php
				wp_nonce_field('wp_nonce_create_view', 'wp_nonce_create_view');
				printf(
						'<input class="js-view-new-redirect" name="view_creation_redirect" type="hidden" value="%s" />',
						// Careful, it is expected that this value really ends with "view_id=". View ID gets appended to it in JS.
						admin_url( 'admin.php?page=views-editor&amp;view_id=') );
			?>
			<div class="wpv-dialog-header">
				<h2><?php _e('Add a new View','wpv-views') ?></h2>
				<i class="icon-remove js-dialog-close"></i>
			</div>
			<div class="wpv-dialog-content no-scrollbar">
				<p>
					<?php _e('A View loads content from the database and displays with your HTML.', 'wpv-views'); ?>
				</p>
				<p>
					<strong><?php _e(' What kind of display do you want to create?','wpv-views'); ?></strong>
				</p>
				<ul>
					<li>
						<p>
							<input type="radio" name="view_purpose" class="js-view-purpose" id="view_purpose_all" value="all" />
							<label for="view_purpose_all"><?php _e('Display all results','wpv-views'); ?></label>
							<span class="helper-text"><?php _e('The View will output all the results returned from the query section.', 'wpv-views'); ?></span>
						</p>
					</li>
					<li>
						<p>
							<input type="radio" name="view_purpose" class="js-view-purpose" id="view_purpose_pagination" value="pagination" />
							<label for="view_purpose_pagination"><?php _e('Display the results with pagination','wpv-views'); ?></label>
							<span class="helper-text"><?php _e('The View will display the query results in pages.', 'wpv-views'); ?></span>
						</p>
					</li>
					<li>
						<p>
							<input type="radio" name="view_purpose" class="js-view-purpose" id="view_purpose_slider" value="slider" />
							<label for="view_purpose_slider"><?php _e('Display the results as a slider','wpv-views'); ?></label>
							<span class="helper-text"><?php _e('The View will display the query results as slides.', 'wpv-views'); ?></span>
						</p>
					</li>
					<li>
						<p>
							<input type="radio" name="view_purpose" class="js-view-purpose" id="view_purpose_parametric" value="parametric" />
							<label for="view_purpose_parametric"><?php _e('Display the results as a parametric search','wpv-views'); ?></label>
							<span class="helper-text"><?php _e('Visitors will be able to search through your content using different search criteria.', 'wpv-views'); ?></span>
						</p>
					</li>
					<li>
						<p>
							<input type="radio" name="view_purpose" class="js-view-purpose" id="view_purpose_full" value="full" />
							<label for="view_purpose_full"><?php _e('Full custom display mode','wpv-views'); ?></label>
							<span class="helper-text"><?php _e('See all the View controls open and set up things manually..', 'wpv-views'); ?></span>
						</p>
					</li>
				</ul>

				<p>
					<strong><label for="view_new_name"><?php _e('Name this View','wpv-views'); ?></label></strong>
				</p>
				<p>
					<input type="text" name="view_new_name" id="view_new_name" class="js-new-post_title"
							placeholder="<?php echo htmlentities( __('Enter title here', 'wpv-views'), ENT_QUOTES ); ?>"
							data-highlight="<?php echo htmlentities( __('Now give this View a name', 'wpv-views'), ENT_QUOTES ); ?>" />
				</p>
				<div class="js-wpv-error-container"></div>
			</div>

			<div class="wpv-dialog-footer">
				<?php wp_nonce_field('wp_nonce_create_view', 'wp_nonce_create_view'); ?>
				<button class="button js-dialog-close"><?php _e('Cancel','wpv-views') ?></button>
				<button class="button button-primary js-create-new-view"><?php _e('Create View','wpv-views') ?></button>
			</div>
		</div> <!-- .create-view-form-dialog -->

		<?php
			wpv_render_duplicate_dialog( 'view' );
		?>

	</div> <!-- .popup-window-container" -->
	<?php
}


/**
 * @todo comment
 */ 
function wpv_render_duplicate_dialog( $type ) {

	switch( $type ) {
		case 'ct':
			$type_label = __( 'Content Template', 'wpv-views' );
			$dialog_selector = 'js-wpv-duplicate-ct-dialog';
			$button_selector = 'js-wpv-duplicate-ct';
			$nonce = '';
			break;
		case 'view':
			$type_label = __( 'View', 'wpv-views' );
			$nonce = wp_create_nonce( 'wpv_duplicate_view_nonce' );
			$dialog_selector = 'js-duplicate-view-dialog';
			$button_selector = 'js-duplicate-view';
			break;
		case 'wpa':
			return;
			$type_label = __( 'WordPress Archive', 'wpv-views' );
			break;
		default:
			return;
	}

	$already_exists_message = htmlentities(
			sprintf(
					__( 'A %s with that name already exists. Please use another name.', 'wpv-views'),
					$type_label ),
			ENT_QUOTES );
	
	?>
	<div class="wpv-dialog <?php echo $dialog_selector; ?>">
		<div class="wpv-dialog-header">
			<h2><?php printf( __( 'Duplicate %s', 'wpv-views' ), '<span class="js-duplicate-origin-title"></span>' ); ?></h2>
		</div>
		<div class="wpv-dialog-content">
			<p>
				<label for="duplicated_name">
					<?php
						printf( __( 'Name for the new %s', 'wpv-views' ), $type_label );
					?>
				</label>
			</p>
			<p>
				<input type="text" value="" class="widefat js-wpv-duplicated-title"
						placeholder="<?php _e('Enter name here','wpv-views') ?>" name="duplicated_name">
			</p>
			<div class="js-wpv-duplicate-error-container"></div>
		</div>
		<div class="wpv-dialog-footer">
			<button class="button js-dialog-close">
				<?php _e( 'Cancel', 'wpv-views' ); ?>
			</button>
			<button class="button button-secondary <?php echo $button_selector; ?>" disabled="disabled"
					data-nonce="<?php echo $nonce; ?>"
					data-error="<?php echo $already_exists_message; ?>" >
				<?php _e( 'Duplicate', 'wpv-views' ); ?>
			</button>
		</div>
	</div>
	<?php
}

?>
