<?php

/**
* wpv_admin_menu_views_listing_page
*
* Creates the main structure of the Views admin listing page: wrapper and header
*
*/

function wpv_admin_menu_views_listing_page() {
	?>
	<div class="wrap toolset-views">

		<div class="wpv-views-listing-page">
			<?php
				// 'trash' or 'publish'
				$current_post_status = wpv_getget( 'status', 'publish', array( 'trash', 'publish' ) );
				$search_term = urldecode( sanitize_text_field( wpv_getget( 's' ) ) );

				// IDs of possible results and counts per post status
				$views_pre_query_data = wpv_prepare_view_listing_query( 'normal', $current_post_status );

				// Do we have any Views at all?
				$has_views = ( $views_pre_query_data['total_count'] > 0 );

				// general nonce
				// TODO please do NOT use this general nonce
				wp_nonce_field( 'work_views_listing', 'work_views_listing' );
			?>
			<div id="icon-views" class="icon32"></div>
			<h2><!-- classname wpv-page-title removed -->
				<?php
					_e( 'Views', 'wpv-views' );
					
					printf( ' <a href="#" class="add-new-h2 page-title-action js-wpv-views-add-new-top">%s</a>', __( 'Add new View', 'wpv-views' ) );

					// TODO maybe have this nonce as a data attribute for all buttons opening the popup
					wp_nonce_field('wp_nonce_create_view_wrapper', 'wp_nonce_create_view_wrapper');

					if ( !empty( $search_term ) ) {
						if ( 'trash' == $current_post_status ) {
							$search_message = __( 'Search results for "%s" in trashed Views', 'wpv-views' );
						} else {
							$search_message = __( 'Search results for "%s"', 'wpv-views' );
						}
						?>
							<span class="subtitle">
								<?php echo sprintf( $search_message, $search_term ); ?>
							</span>
						<?php
					}
				?>
			</h2>

			<?php
				// Messages: trashed, untrashed, deleted
				add_filter( 'wpv_maybe_show_listing_message_undo', 'wpv_admin_view_listing_message_undo', 10, 3 );

				wpv_maybe_show_listing_message( 'trashed', __( 'View moved to the Trash.', 'wpv-views' ), __( '%d Views moved to the Trash.', 'wpv-views' ), true );
				wpv_maybe_show_listing_message( 'untrashed', __( 'View restored from the Trash.', 'wpv-views' ), __( '%d Views restored from the Trash.', 'wpv-views' ) );
				wpv_maybe_show_listing_message( 'deleted', __( 'View permanently deleted.', 'wpv-views' ), __( '%d Views permanently deleted.', 'wpv-views' ) );
				

				if ( $has_views ) {
				
					// Display the rest of the content if there are Views to list
					wpv_admin_view_listing_table( $views_pre_query_data, $current_post_status );
					
				} else {
					// Show a message in any other case
					?>
						<div class="wpv-view-not-exist js-wpv-view-not-exist">
							<p><?php _e('Views load content from the database and display on the site.'); ?></p>
							<p>
								<a class="button js-wpv-views-add-first" href="#">
									<i class="icon-plus"></i>
									<?php _e('Create your first View','wpv-views');?>
								</a>
							</p>
						</div>
					<?php
				}

			?>

		</div> <!-- .wpv-views-listing-page" -->

	</div> <!-- .toolset-views" -->

	<?php

	wpv_render_view_listing_dialog_templates();
}


/**
 * wpv_admin_view_listing_table
 *
 * Displays the content of the Views admin listing page: status, table and pagination.
 *
 * @param array $views_pre_query_data Array with IDs of possible results and counts per post status.
 *     See wpv_prepare_view_listing_query() for details.
 * @param string $current_post_status Status of posts to display. Can be 'publish' or 'trash'.
 *
 * @since unknown
 */
function wpv_admin_view_listing_table( $views_pre_query_data, $current_post_status ) {
	
	// array of URL modifiers
	$mod_url = array(
		'orderby' => '',
		'order' => '',
		's' => '',
		'items_per_page' => '',
		'paged' => '',
		'status' => $current_post_status
	);

	// array of WP_Query parameters
	$wpv_args = array(
		'post_type' => 'view',
		'post__in' => $views_pre_query_data[ 'post__in' ],
		'posts_per_page' => WPV_ITEMS_PER_PAGE,
		'order' => 'ASC',
		'orderby' => 'title',
		'post_status' => $current_post_status,
	);

	// perform the search in Views titles and decriptions and update post__in argument to $wpv_args.
	if ( isset( $_GET["s"] ) && '' != $_GET["s"] ) {
		$wpv_args = wpv_modify_wpquery_for_search( $_GET["s"], $wpv_args );
		$mod_url['s'] = sanitize_text_field( $_GET["s"] );
	}

	// apply posts_per_page coming from the URL parameters
	if ( isset( $_GET["items_per_page"] ) && '' != $_GET["items_per_page"] ) {
		$wpv_args['posts_per_page'] = (int) $_GET["items_per_page"];
		$mod_url['items_per_page'] = (int) $_GET["items_per_page"];
	}

	// apply orderby coming from the URL parameters
	if ( isset( $_GET["orderby"] ) && '' != $_GET["orderby"] ) {
		$wpv_args['orderby'] = sanitize_text_field( $_GET["orderby"] );
		$mod_url['orderby'] = sanitize_text_field( $_GET["orderby"] );

		// apply order coming from the URL parameters
		if ( isset( $_GET["order"] ) && '' != $_GET["order"] ) {
			$wpv_args['order'] = sanitize_text_field( $_GET["order"] );
			$mod_url['order'] = sanitize_text_field( $_GET["order"] );
		}
	}

	// apply paged coming from the URL parameters
	if ( isset( $_GET["paged"] ) && '' != $_GET["paged"] ) {
		$wpv_args['paged'] = (int) $_GET["paged"];
		$mod_url['paged'] = (int) $_GET["paged"];
	}

	$wpv_query = new WP_Query( $wpv_args );

	// The number of Views being displayed.
	$wpv_count_posts = $wpv_query->post_count; 

	// Total number of Views matching query parameters.
	$wpv_found_posts = $wpv_query->found_posts;
	
	?>
		<ul class="subsubsub"><!-- links to lists Views in different statuses -->
			<?php
				// "publish" status
				$is_current = ( ( 'publish' == $current_post_status ) && !isset( $_GET["s"] ) );
				printf(
						'<li><a href="%s" %s >%s</a> (%s) | </li>',
						esc_url( add_query_arg(
								array( 'page' => 'views', 'status' => 'publish' ),
								admin_url('admin.php') ) ),
						$is_current ? ' class="current" ' : '',
						__( 'Published', 'wpv-views' ),
						$views_pre_query_data['published_count'] );

				// "trash" status
				$is_current = ( ( 'trash' == $current_post_status ) && !isset( $_GET["s"] ) );
				printf(
						'<li><a href="%s" %s >%s</a> (%s)</li>',
						esc_url( add_query_arg(
								array( 'page' => 'views', 'status' => 'trash' ),
								admin_url('admin.php') ) ),
						$is_current ? ' class="current" ' : '',
						__( 'Trash', 'wpv-views' ),
						$views_pre_query_data['trashed_count'] );
			?>
		</ul>
	<?php

	// A nonce for view action - used for individual as well as for bulk actions
	$view_action_nonce = wp_create_nonce( 'wpv_view_listing_actions_nonce' );

	// === Render "tablenav" section (Bulk actions and Search box) ===
	echo '<div class="tablenav top">';

	// If there is one or more Views in this query, show search box
	if ( $wpv_found_posts > 0 ) {
		?>
		<div class="alignright">
			<form id="posts-filter" action="" method="get">
				<p class="search-box">
					<label class="screen-reader-text" for="post-search-input"><?php _e('Search Views','wpv-views'); ?>:</label>
					<?php $search_term = isset( $_GET["s"] ) ? urldecode( sanitize_text_field($_GET["s"]) ) : ''; ?>
					<input type="search" id="post-search-input" name="s" value="<?php echo $search_term; ?>" />
					<input type="submit" name="" id="search-submit" class="button" value="<?php echo htmlentities( __('Search Views','wpv-views'), ENT_QUOTES ); ?>" />
					<input type="hidden" name="paged" value="1" />
				</p>
			</form>
		</div>
		<?php
	}

	// If this page has one or more Views, show Bulk actions controls
	if ( $wpv_count_posts > 0 ) {
		// Prepare ender bulk actions dropdown.
		if( 'publish' == $current_post_status ) {
			$bulk_actions = array( 'trash' => __( 'Move to trash', 'wpv-views' ) );
		} else {
			$bulk_actions = array(
				'restore-from-trash' => __( 'Restore from trash', 'wpv-views' ),
				'delete' => __( 'Delete permanently', 'wpv-views' ) );
		}

		$bulk_actions_args = array( 'data-viewactionnonce' => $view_action_nonce );
		$bulk_actions_class = 'js-wpv-views-listing-bulk-action';

		echo wpv_admin_table_bulk_actions( $bulk_actions, $bulk_actions_class, $bulk_actions_args, 'top' );
	}

	echo '</div>'; // End of tablenav section
	

	// If this page has one or more Views, show the table
	if ( $wpv_count_posts > 0 ) {
		?>
		<table class="wpv-views-listing js-wpv-views-listing widefat">
			<thead>
				<?php
					/* To avoid code duplication, table header is stored in output buffer and echoed twice - within
					 * thead and tfoot tags. */
					ob_start();
				?>
				<tr>
					<th class="wpv-admin-listing-col-bulkactions check-column">
						<input type="checkbox" />
					</th>
					<?php
						$column_active = '';
						$column_sort_to = 'ASC';
						$column_sort_now = 'ASC';
						if ( $wpv_args['orderby'] === 'title' ) {
							$column_active = ' views-list-sort-active';
							$column_sort_to = ( $wpv_args['order'] === 'ASC' ) ? 'DESC' : 'ASC';
							$column_sort_now = $wpv_args['order'];
						}
					?>
					<th class="wpv-admin-listing-col-title">
						<?php
							// "sort by title" link
							printf(
									'<a href="%s" class="%s" data-orderby="title">%s <i class="%s"></i></a>',
									wpv_maybe_add_query_arg(
											array(
													'page' => 'views',
													'orderby' => 'title',
													'order' => $column_sort_to,
													's' => $mod_url['s'],
													'items_per_page' => $mod_url['items_per_page'],
													'paged' => $mod_url['paged'],
													'status' => $mod_url['status'] ),
											admin_url( 'admin.php' ) ),
									'js-views-list-sort views-list-sort ' . $column_active,
									__( 'Title','wpv-views' ),
									( 'DESC' === $column_sort_now ) ? 'icon-sort-by-alphabet-alt' : 'icon-sort-by-alphabet' );
						?>
					</th>

					<th class="wpv-admin-listing-col-summary js-wpv-col-two"><?php _e('Content to load','wpv-views') // TODO review this classname ?></th>
					<th class="wpv-admin-listing-col-scan"><?php _e('Used on','wpv-views') ?></th>

					<?php
						$column_active = '';
						$column_sort_to = 'DESC';
						$column_sort_now = 'DESC';
						if ( $wpv_args['orderby'] === 'date' ) {
							$column_active = ' views-list-sort-active';
							$column_sort_to = ( $wpv_args['order'] === 'ASC' ) ? 'DESC' : 'ASC';
							$column_sort_now = $wpv_args['order'];
						}
					?>
					<th class="wpv-admin-listing-col-date">
						<?php
							// "sort by date" link
							printf(
									'<a href="%s" class="%s" data-orderby="date">%s <i class="%s"></i></a>',
									wpv_maybe_add_query_arg(
											array(
													'page' => 'views',
													'orderby' => 'date',
													'order' => $column_sort_to,
													's' => $mod_url['s'],
													'items_per_page' => $mod_url['items_per_page'],
													'paged' => $mod_url['paged'],
													'status' => $mod_url['status'] ),
											admin_url( 'admin.php' ) ),
									'js-views-list-sort views-list-sort ' . $column_active,
									__( 'Date', 'wpv-views' ),
									( 'DESC' === $column_sort_now ) ? 'icon-sort-by-attributes-alt' : 'icon-sort-by-attributes' );
						?>
					</th>
				</tr>
				<?php
					// Get table header from output buffer and stop buffering
					$table_header = ob_get_contents();
					ob_end_clean();

					echo $table_header;
				?>
			</thead>
			<tfoot>
				<?php
					echo $table_header;
				?>
			</tfoot>

			<tbody class="js-wpv-views-listing-body">
				<?php
					$alternate = '';
					while ( $wpv_query->have_posts() ) :
						$wpv_query->the_post();
						$post_id = get_the_id();
						$post = get_post( $post_id );
						$meta = get_post_meta( $post_id, '_wpv_settings' );
						$view_description = get_post_meta( $post_id, '_wpv_description', true );
						$alternate = ' alternate' == $alternate ? '' : ' alternate';
						?>
						<tr id="wpv_view_list_row_<?php echo $post->ID; ?>" class="js-wpv-view-list-row<?php echo $alternate; ?>">
							<th class="wpv-admin-listing-col-bulkactions check-column">
								<?php
									printf( '<input type="checkbox" value="%s" name="view[]" />', $post->ID );
								?>
							</th>
							<td class="wpv-admin-listing-col-title">
								<span class="row-title">
								<?php
									if ( 'trash' == $current_post_status ) {
										echo esc_html( $post->post_title );
									} else {
										printf( '<a href="%s">%s</a>',
											esc_url( add_query_arg(
													array( 'page' => 'views-editor', 'view_id' => $post->ID ),
													admin_url( 'admin.php' ) ) ),
											esc_html( $post->post_title ) 
										);
									}
								?>
								</span>
								<?php
									if ( isset( $view_description ) && '' != $view_description ) {
										?>
										<p class="desc">
											<?php echo nl2br($view_description); ?>
										</p>
										<?php
									}

									/* Generate and show row actions.
									 * Note that we want to add also 'simple' action names to the action list because
									 * they get echoed as a class of the span tag and get styled from WordPress core css
									 * accordingly (e.g. trash in different colour than the rest) */
									$row_actions = array( );

									if ( 'publish' == $current_post_status ) {
										$row_actions['edit'] = sprintf(
												'<a href="%s">%s</a>',
												esc_url( add_query_arg(
													array( 'page' => 'views-editor', 'view_id' => $post->ID ),
													admin_url( 'admin.php' ) ) ),
												__( 'Edit', 'wpv-views' ) );
										$row_actions['duplicate js-views-actions-duplicate'] = sprintf( '<a href="#">%s</a>', __( 'Duplicate', 'wpv-views' ) );
										$row_actions['trash js-views-actions-trash'] = sprintf( '<a href="#">%s</a>', __( 'Move to trash', 'wpv-views' ) );
									} else if ( 'trash' == $current_post_status ) {
										$row_actions['restore-from-trash js-views-actions-restore-from-trash'] = sprintf( '<a href="#">%s</a>', __( 'Restore from trash', 'wpv-views' ) );
										$row_actions['delete js-views-actions-delete'] = sprintf( '<a href="#">%s</a>', __( 'Delete', 'wpv-views' ) );
									}

									echo wpv_admin_table_row_actions( $row_actions,	array(
											"data-view-id" => $post->ID,
											"data-view-title" => esc_html( $post->post_title ),
											"data-viewactionnonce" => $view_action_nonce ) );
								?>
							</td>
							<td class="wpv-admin-listing-col-summary">
								<?php echo wpv_create_content_summary_for_listing( $post->ID ); ?>
							</td>
							<td class="wpv-admin-listing-col-scan">
								<button class="button js-scan-button" data-view-id="<?php echo $post->ID; ?>">
									<?php _e( 'Scan', 'wp-views' ); ?>
								</button>
								<span class="js-nothing-message hidden"><?php _e( 'Nothing found', 'wpv-views' ); ?></span>
							</td>
							<td class="wpv-admin-listing-col-date">
								<?php echo get_the_time( get_option( 'date_format' ), $post->ID ); ?>
							</td>
						</tr>
					<?php
					endwhile;
				?>
			</tbody>
		</table>

		<div class="tablenav bottom">
			<?php
				echo wpv_admin_table_bulk_actions( $bulk_actions, $bulk_actions_class, $bulk_actions_args, 'bottom' );
			?>
		</div>

		<p class="add-new-view" >
			<a class="button js-wpv-views-add-new" href="#">
				<i class="icon-plus"></i><?php _e('Add new View','wpv-views') ?>
			</a>
		</p>

		<?php
			wpv_admin_listing_pagination( 'views', $wpv_found_posts, $wpv_args["posts_per_page"], $mod_url );
		?>
		<?php

	} else {

		// No Views matches the criteria
		?>
		<div class="wpv-views-listing views-empty-list">
			<?php
				if ( isset( $_GET["status"] ) && $_GET["status"] == 'trash' && isset( $_GET["s"] ) && $_GET["s"] != '' ) {
					printf(
							'<p>%s</p><p><a class="button-secondary" href="%s">%s</a></p>',
							__( 'No Views in trash matched your criteria.', 'wpv-views' ),
							wpv_maybe_add_query_arg(
									array(
											'page' => 'views',
											'orderby' => $mod_url['orderby'],
											'order' => $mod_url['order'],
											'items_per_page' => $mod_url['items_per_page'],
											'paged' => '1',
											'status' => 'trash' ),
									admin_url( 'admin.php' ) ),
							__( 'Return', 'wpv-views' ) );
				} else if ( isset( $_GET["status"] ) && $_GET["status"] == 'trash' ) {
					printf(
							'<p>%s</p><p><a class="button-secondary" href="%s">%s</a></p>',
							__( 'No Views in trash.', 'wpv-views' ),
							wpv_maybe_add_query_arg(
									array(
											'page' => 'views',
											'orderby' => $mod_url['orderby'],
											'order' => $mod_url['order'],
											'items_per_page' => $mod_url['items_per_page'],
											'paged' => '1' ),
									admin_url( 'admin.php' ) ),
							__( 'Return', 'wpv-views' ) );
				} else if ( isset( $_GET["s"] ) && $_GET["s"] != '' ) {
					printf(
							'<p>%s</p><p><a class="button-secondary" href="%s">%s</a></p>',
							__( 'No Views matched your criteria.', 'wpv-views' ),
							wpv_maybe_add_query_arg(
									array(
											'page' => 'views',
											'orderby' => $mod_url['orderby'],
											'order' => $mod_url['order'],
											'items_per_page' => $mod_url['items_per_page'],
											'paged' => '1' ),
									admin_url( 'admin.php' ) ),
							__( 'Return', 'wpv-views' ) );
				} else {
					?>
					<div class="wpv-view-not-exist js-wpv-view-not-exist">
						<p><?php _e('Views load content from the database and display on the site.'); ?></p>
						<p><a class="button js-wpv-views-add-first" href="#"><i class="icon-plus"></i><?php _e('Create your first View','wpv-views');?></a></p>
					</div>
					<?php
				}
			?>
		</div>
		<?php
	}
}


/**
 * DEPRECATED.
 *
 * This is not used anywhere in Views.
 */ 
function wpv_admin_menu_views_listing_row($post_id) {

	ob_start();
	$post = get_post($post_id);
	$meta = get_post_meta($post_id, '_wpv_settings');
	$view_description = get_post_meta($post_id, '_wpv_description', true);
	?>
	<tr id="wpv_view_list_row_<?php echo $post->ID; ?>" class="js-wpv-view-list-row">
		<td class="post-title page-title column-title">
			<span class="row-title">
				<a href="admin.php?page=views-editor&amp;view_id=<?php echo esc_attr( $post->ID ); ?>"><?php echo esc_html( $post->post_title ); ?></a>
			</span>
			<?php if (isset($view_description) && '' != $view_description): ?>
				<p class="desc">
                    <?php echo nl2br($view_description)?>
                </p>
			<?php endif; ?>
		</td>
		<td>
			<?php echo wpv_create_content_summary_for_listing($post->ID); ?>
		</td>
		<td>
			<select class="js-views-actions" name="list_views_action_<?php echo $post->ID; ?>" id="list_views_action_<?php echo $post->ID; ?>" data-view-id="<?php echo $post->ID; ?>">
				<option value="0"><?php _e('Choose','wpv-views') ?>&hellip;</option>
				<option value="delete"><?php _e('Delete','wpv-views') ?></option>
				<option value="duplicate"><?php _e('Duplicate','wpv-views') ?></option>
			</select>
		</td>
		<td>
			<button class="button js-scan-button" data-view-id="<?php echo $post->ID; ?>"><?php _e('Scan','wp-views') ?></button>
            <span class="js-nothing-message hidden"><?php _e('Nothing found','wpv-views');?></span>
		</td>
		<td>
			<?php echo get_the_time(get_option('date_format'), $post->ID); ?>
		</td>
	</tr>
	<?php
	$row = ob_get_contents();
	ob_end_clean();

	return $row;

}
