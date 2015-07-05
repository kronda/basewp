<?php

function wpv_admin_menu_content_templates_listing_page() {
	?>
	<div class="wrap toolset-views">
		<div class="wpv-views-listing-page">
			<?php
				wp_nonce_field( 'work_view_template', 'work_view_template' );
				$search_term = isset( $_GET["s"] ) ? urldecode( sanitize_text_field($_GET["s"]) ) : '';
			?>
			<div id="icon-views" class="icon32"></div>
			<h2><!-- classname wpv-page-title removed -->
				<?php
					_e( 'Content Templates', 'wpv-views' );

					printf(
							' <a href="#" class="add-new-h2 js-add-new-content-template" data-target="%s">%s</a>',
							add_query_arg( array( 'action' => 'wpv_ct_create_new' ), admin_url( 'admin-ajax.php' ) ),
							__( 'Add new Content Template', 'wpv-views' ) );

					if ( !empty( $search_term ) ) {
						$search_message = __('Search results for "%s"','wpv-views');
						if ( isset( $_GET["status"] ) && 'trash' == sanitize_text_field( $_GET["status"] ) ) {
							$search_message = __('Search results for "%s" in trashed Content Templates', 'wpv-views');
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
				add_filter( 'wpv_maybe_show_listing_message_undo', 'wpv_admin_ct_listing_message_undo', 10, 3 );

				wpv_maybe_show_listing_message(
						'trashed', __( 'Content Template moved to the Trash.', 'wpv-views' ),
						__( '%d Content Templates moved to the Trash.', 'wpv-views' ), true );
				wpv_maybe_show_listing_message(
						'untrashed', __( 'Content Template restored from the Trash.', 'wpv-views' ),
						__( '%d Content Templates restored from the Trash.', 'wpv-views' ) );
				wpv_maybe_show_listing_message(
						'deleted', __( 'Content Template permanently deleted.', 'wpv-views' ),
						__( '%d Content Templates permanently deleted.', 'wpv-views' ) );

				// Determine how should we arrange the items (what should be displayed)
				if ( isset( $_GET["arrangeby"] ) && sanitize_text_field( $_GET["arrangeby"] ) == 'usage' ) {
					$arrange_by = 'single';
					if ( isset( $_GET["usage"] ) ) {
						$arrange_by = sanitize_text_field($_GET["usage"]);
					}
				} else {
					$arrange_by = 'name';
				}
					
				// "Arrange by" tabs
				?>
					<div class="wpv-admin-tabs">
						<ul class="wpv-admin-tabs-links">
							<?php
								$tabs = array(
										'name' => __( 'Name','wpv-views' ),
										'single' => __( 'Usage for single page', 'wpv-views' ),
										'post-archives' => __( 'Usage for custom post archives', 'wpv-views' ),
										'taxonomy-archives' => __( 'Usage for taxonomy archives', 'wpv-views' ) );
										
								foreach( $tabs as $tab_slug => $tab_label ) {
									printf(
										'<li><a href="%s" %s>%s</a></li>',
										add_query_arg(
											array(
													'page' => 'view-templates',
													'arrangeby' => ( 'name' == $tab_slug ) ? 'name' : 'usage',
													'usage' => $tab_slug ),
											admin_url( 'admin.php' ) ),
										wpv_current_class( $arrange_by, $tab_slug, false ),
										$tab_label );
								}
							?> 
						</ul>
					</div>				
				<?php

				// Render the actual listing
				if ( 'name' == $arrange_by ) {
					wpv_admin_content_template_listing_name();
				} else {
					wpv_admin_content_template_listing_usage( $arrange_by );
				}
				
			?>
		</div> <!-- .wpv-views-listing-page -->
	</div> <!-- .toolset-views -->
	<?php
}


/**
 * Generates an Undo link for the 'trashed' message on Content Templates listing.
 *
 * @since 1.7
 * 
 * @see wpv_maybe_show_listing_message_undo filter.
 */ 
function wpv_admin_ct_listing_message_undo( $undo_link, $message_name, $affected_ids ) {
	if( ( 'trashed' == $message_name ) && !empty( $affected_ids ) ) {
		$undo_link = sprintf( '<a href="%s"	class="js-wpv-untrash" data-ids="%s" data-nonce="%s">%s</a>',
				add_query_arg( array( 'page' => 'view-templates', 'untrashed' => count( $affected_ids ) ), admin_url( 'admin.php' ) ),
				urlencode( implode( ',', $affected_ids ) ),
				wp_create_nonce( 'wpv_view_listing_actions_nonce' ),
				__( 'Undo', 'wpv-views' ) );
	}
	return $undo_link;
}


function wpv_admin_content_template_listing_name() {

	$mod_url = array( // array of URL modifiers
		'orderby' => '',
		'order' => '',
		's' => '',
		'items_per_page' => '',
		'paged' => '',
		'status' => ''
	);

	$wpv_args = array(
		'post_type' => 'view-template',
		'posts_per_page' => WPV_ITEMS_PER_PAGE,
		'order' => 'ASC',
		'orderby' => 'title',
		'post_status' => 'publish'
	);

	// Apply post_status coming from the URL parameters.
	if ( isset( $_GET["status"] ) && '' != $_GET["status"] ) { 
		$wpv_args['post_status'] = sanitize_text_field( $_GET["status"] );
		$mod_url['status'] = sanitize_text_field( $_GET["status"] );
	}

	if ( isset( $_GET["s"] ) && '' != $_GET["s"] ) {
		$wpv_args = wpv_modify_wpquery_for_search( $_GET["s"], $wpv_args );
		$mod_url['s'] = sanitize_text_field( $_GET["s"] );
	}

	if ( isset( $_GET["items_per_page"] ) && '' != $_GET["items_per_page"] ) {
		$wpv_args['posts_per_page'] = (int) $_GET["items_per_page"];
		$mod_url['items_per_page'] = (int) $_GET["items_per_page"];
	}

	if ( isset( $_GET["orderby"] ) && '' != $_GET["orderby"] ) {
		$wpv_args['orderby'] = sanitize_text_field($_GET["orderby"]);
		$mod_url['orderby'] = sanitize_text_field($_GET["orderby"]);
		if ( isset( $_GET["order"] ) && '' != $_GET["order"] ) {
			$wpv_args['order'] = sanitize_text_field($_GET["order"]);
			$mod_url['order'] = sanitize_text_field($_GET["order"]);
		}
	}

	if ( isset( $_GET["paged"] ) && '' != $_GET["paged"]) {
		$wpv_args['paged'] = (int) $_GET["paged"];
		$mod_url['paged'] = (int) $_GET["paged"];
	}

	$query = new WP_Query( $wpv_args );

	// Number of posts that are being displayed.
	$wpv_count_posts = $query->post_count;

	// Total number of posts matching the query.
	$wpv_found_posts = $query->found_posts;
	
	$all_posts = wp_count_posts('view-template');
	$wpv_views_status = array(); // to hold the number of Views in each status
	$wpv_views_status['publish'] = $all_posts->publish;
	$wpv_views_status['trash'] = $all_posts->trash;

	// True if some content templates (even those not matching current query) exist.
	$some_posts_exist = ( $wpv_views_status['publish'] > 0 || $wpv_views_status['trash'] > 0 );
	
	?>

	<?php
		if ( $some_posts_exist ) {
			?>
			<ul class="subsubsub" style="clear:both"><!-- links to lists WPA in different statuses -->
				<li>
					<?php
						$is_plain_publish_current_status = ( $wpv_args['post_status'] == 'publish' && !isset( $_GET["s"] ) );
						printf(
								'<a href="%s" %s>%s</a> (%s) | ',
								add_query_arg(
										array( 'page' => 'view-templates', 'status' => 'publish' ),
										admin_url( 'admin.php' ) ),
								$is_plain_publish_current_status ?  ' class="current" ' : '',
								__( 'Published', 'wpv-views' ),
								$wpv_views_status['publish'] );

					?>
				</li>
				<li>
					<?php
						$is_plain_trash_current_status = ( $wpv_args['post_status'] == 'trash' && !isset( $_GET["s"] ) );
						printf(
								'<a href="%s" %s>%s</a> (%s)',
								add_query_arg(
										array( 'page' => 'view-templates', 'status' => 'trash' ),
										admin_url( 'admin.php' ) ),
								$is_plain_trash_current_status ?  ' class="current" ' : '',
								__( 'Trash', 'wpv-views' ),
								$wpv_views_status['trash'] );
					?>
				</li>
			</ul>

			<?php
		} else {
			// No post exist at all
			?>
			<p class="wpv-view-not-exist">
			<?php _e('Content Templates let you design single pages.','wpv-views'); ?>
			</p>
			<p class="add-new-view">
				<button class="button js-add-new-content-template"
				data-target="<?php echo add_query_arg( array( 'action' => 'wpv_ct_create_new' ), admin_url( 'admin-ajax.php' ) ); ?>">
					<i class="icon-plus"></i><?php _e('Add new Content Template','wpv-views') ?>
				</button>
			</p><?php
		}

		if( $some_posts_exist ) {

			// A nonce for CT action - used for individual as well as for bulk actions
			$ct_action_nonce = wp_create_nonce( 'wpv_view_listing_actions_nonce' );
			
			// === Render "tablenav" section (Bulk actions and Search box) ===
			echo '<div class="tablenav top">';

			if( $wpv_count_posts > 0 ) {
		
				// Prepare to render bulk actions dropdown.
				if( 'publish' == $wpv_args['post_status'] ) {
					$bulk_actions = array( 'trash' => __( 'Move to trash', 'wpv-views' ) );
				} else {
					$bulk_actions = array(
							'restore-from-trash' => __( 'Restore from trash', 'wpv-views' ),
							'delete' => __( 'Delete permanently', 'wpv-views' ) );
				}

				$bulk_actions_args = array( 'data-viewactionnonce' => $ct_action_nonce );
				$bulk_actions_class = 'js-wpv-ct-listing-bulk-action';

				echo wpv_admin_table_bulk_actions( $bulk_actions, $bulk_actions_class, $bulk_actions_args, 'top' );
			}

			// Show search box
			if ( $wpv_found_posts > 0 ) {
				?>
				<div class="alignright">
					<form id="posts-filter" action="" method="get">
						<p class="search-box">
							<label class="screen-reader-text" for="post-search-input"><?php _e('Search Views:', 'wpv-views') ?></label>
							<?php $search_term = isset( $_GET["s"] ) ? urldecode( sanitize_text_field($_GET["s"]) ) : ''; ?>
							<input type="search" id="ct-post-search-input" name="s" value="<?php echo $search_term; ?>">
							<input type="submit" name="" id="ct-search-submit" class="button" value="<?php echo htmlentities( __('Search Content Templates', 'wpv-views'), ENT_QUOTES ); ?>">
							<input type="hidden" name="paged" value="1" />
						</p>
					</form>
				</div>
				<?php
			}
			
			echo '</div>'; // End of tablenav section
			
		}

		if ( $wpv_count_posts == 0 && $some_posts_exist ) {
			// No posts are displayed, but some exist
			if ( isset( $_GET["s"] ) && '' != $_GET["s"] ) {
				if ( $wpv_args['post_status'] == 'trash' ) {
					// Searching in trash
					?>
					<div class="wpv-views-listing views-empty-list">
						<p>
							<?php
								printf(
										'<p>%s</p><p><a class="button-secondary" href="%s">%s</a></p>',
										__( 'No Content Templates in trash matched your criteria.', 'wpv-views' ),
										wpv_maybe_add_query_arg(
												array(
														'page' => 'view-templates',
														'orderby' => $mod_url['orderby'],
														'order' => $mod_url['order'],
														'items_per_page' => $mod_url['items_per_page'],
														'paged' => '1',
														'status' => 'trash' ),
												admin_url( 'admin.php' ) ),
										__( 'Return', 'wpv-views' ) );
							?>
						</p>
					</div>
					<?php
				} else {
					// Normal search
					?>
					<div class="wpv-views-listing views-empty-list">
						<p>
							<?php
								printf(
										'<p>%s</p><p><a class="button-secondary" href="%s">%s</a></p>',
										__( 'No Content Templates matched your criteria.', 'wpv-views' ),
										wpv_maybe_add_query_arg(
												array(
														'page' => 'view-templates',
														'orderby' => $mod_url['orderby'],
														'order' => $mod_url['order'],
														'items_per_page' => $mod_url['items_per_page'],
														'paged' => '1' ),
												admin_url( 'admin.php' ) ),
										__( 'Return', 'wpv-views' ) );
							?>
						</p>
					</div>
					<?php
				}
			} else {
				if ( $wpv_args['post_status'] == 'trash' ) {
					// No items in trash
					?>
					<div class="wpv-views-listing views-empty-list">
						<p>
							<?php
								printf(
										'<p>%s</p><p><a class="button-secondary" href="%s">%s</a></p>',
										__( 'No Content Templates in trash.', 'wpv-views' ),
										wpv_maybe_add_query_arg(
												array(
														'page' => 'view-templates',
														'orderby' => $mod_url['orderby'],
														'order' => $mod_url['order'],
														'items_per_page' => $mod_url['items_per_page'],
														'paged' => '1' ),
												admin_url( 'admin.php' ) ),
										__( 'Return', 'wpv-views' ) );
							?>
						</p>
					</div>
					<?php
				} else {
					?>
					<p class="wpv-view-not-exist">
						<?php _e('Content Templates let you design single pages.','wpv-views'); ?>
					</p>
					<p class="add-new-view">
						<button class="button js-add-new-content-template"
								data-target="<?php echo add_query_arg( array( 'action' => 'wpv_ct_create_new' ), admin_url( 'admin-ajax.php' ) ); ?>">
							<i class="icon-plus"></i><?php _e( 'Add new Content Template', 'wpv-views') ?>
						</button>
					</p>
					<?php
				}
			}
			
		} else if ( $wpv_count_posts != 0 ) {
			// We have some results to display.
			
			global $wpdb;

			?>
			
			<table class="wpv-views-listing widefat">

			<!-- section for: sort by name -->
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
							$status = '';
							if ( $wpv_args['orderby'] === 'title' ) {
								$column_active = ' views-list-sort-active';
								$column_sort_to = ( $wpv_args['order'] === 'ASC' ) ? 'DESC' : 'ASC';
								$column_sort_now = $wpv_args['order'];
							}
							if ( isset($_GET['status']) && $_GET['status'] == 'trash' ){
								$status = 'trash';
							}
						?>
						<th class="wpv-admin-listing-col-title">
							<?php
								printf(
										'<a href="%s" class="%s" data-orderby="title">%s <i class="%s"></i></a>',
										wpv_maybe_add_query_arg(
												array(
														'page' => 'view-templates',
														'status' => $status,
														'orderby' => 'title',
														'order' => $column_sort_to,
														's' => $mod_url['s'],
														'items_per_page' => $mod_url['items_per_page'],
														'paged' => $mod_url['paged'] ),
												admin_url( 'admin.php' ) ),
										'js-views-list-sort views-list-sort ' . $column_active,
										__( 'Title', 'wpv-views' ),
										( 'DESC'  === $column_sort_now ) ? 'icon-sort-by-alphabet-alt' : 'icon-sort-by-alphabet' );
							?>
						</th>
						<th class="wpv-admin-listing-col-usage js-wpv-col-two"><?php _e('Used on','wpv-views') ?></th>
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
								printf(
										'<a href="%s" class="%s" data-orderby="date">%s <i class="%s"></i></a>',
										wpv_maybe_add_query_arg(
												array(
														'page' => 'view-templates',
														'status' => $status,
														'orderby' => 'date',
														'order' => $column_sort_to,
														's' => $mod_url['s'],
														'items_per_page' => $mod_url['items_per_page'],
														'paged' => $mod_url['paged'] ),
												admin_url( 'admin.php' ) ),
										'js-views-list-sort views-list-sort ' . $column_active,
										__( 'Date', 'wpv-views' ),
										( 'DESC'  === $column_sort_now ) ? 'icon-sort-by-attributes-alt' : 'icon-sort-by-attributes' );
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
					while ( $query->have_posts() ) :
						$query->the_post();
						$post = get_post( get_the_id() );
						$template_id = $post->ID;
						$wpv_content_template_decription  = get_post_meta( $template_id, '_wpv-content-template-decription', true );
						$layout_loop_template_for_view_id = get_post_meta( $template_id, '_view_loop_id', true );
						$alternate = ( ' alternate' == $alternate ) ? '' : ' alternate';
						?>
						<tr id="wpv_ct_list_row_<?php echo $template_id; ?>" class="js-wpv-ct-list-row<?php echo $alternate; ?>">
							<th class="wpv-admin-listing-col-bulkactions check-column">
								<?php
									if ( empty( $layout_loop_template_for_view_id ) ) {
										printf( '<input type="checkbox" value="%s" name="view[]" />', $template_id );
									}
								?>
							</th>
							<td class="wpv-admin-listing-col-title post-title page-title column-title">
								<span class="row-title">
									<?php
										if ( $wpv_args['post_status'] == 'trash' ) {
											echo $post->post_title;
										} else {
											printf(
													'<a href="%s">%s</a>',
													add_query_arg(
															array( 'action' => 'edit', 'post' => $template_id ),
															admin_url( 'post.php' ) ),
													$post->post_title );
										}
									?>
								</span>
								<?php
									if ( ! empty( $wpv_content_template_decription ) ) {
										?>
										<p class="desc">
											<?php echo nl2br( $wpv_content_template_decription )?>
										</p>
										<?php
									}
									/* Generate and show row actions.
									 * Note that we want to add also 'simple' action names to the action list because
									 * they get echoed as a class of the span tag and get styled from WordPress core css
									 * accordingly (e.g. trash in different colour than the rest) */
									$row_actions = array();
									$asigned_count = $wpdb->get_var(
										$wpdb->prepare(
											"SELECT COUNT(post_id) FROM {$wpdb->postmeta} JOIN {$wpdb->posts} p 
											WHERE meta_key = '_views_template' 
											AND meta_value = %s
											AND post_id = p.ID 
											AND p.post_status NOT IN ('auto-draft') 
											AND p.post_type != 'revision'",
											$template_id
										)
									);
									if ( 'publish' == $wpv_args['post_status'] ) {
										$row_actions['edit'] = sprintf(
												'<a href="%s">%s</a>',
												add_query_arg(
														array( 'action' => 'edit', 'post' => $template_id ),
														admin_url( 'post.php' ) ),
												__( 'Edit', 'wpv-views' ) );
										/* Note that hash in <a href="#"> is present so the link behaves like a link.
										 * <a href=""> causes problems with colorbox and with mere <a> the mouse cursor
										 * doesn't change when hovering over the link. */
										if ( empty( $layout_loop_template_for_view_id ) ) {
											$row_actions['change js-list-ct-action-change'] = sprintf( '<a href="#">%s</a>', __( 'Change template usage', 'wpv-views' ) );
										}
										$row_actions['duplicate js-list-ct-action-duplicate'] = sprintf( '<a href="#">%s</a>', __( 'Duplicate', 'wpv-views' ) );
										if ( empty( $layout_loop_template_for_view_id ) ) {
											$row_actions['trash js-list-ct-action-trash'] = sprintf( '<a href="#">%s</a>', __( 'Move to trash', 'wpv-views' ) );
										}
									} else if ( 'trash' == $wpv_args['post_status'] ) {
										$row_actions['restore-from-trash js-list-ct-action-restore-from-trash'] = sprintf( '<a href="#">%s</a>', __( 'Restore from trash', 'wpv-views' ) );
										$row_actions['delete js-list-ct-action-delete'] = sprintf( '<a href="#">%s</a>', __( 'Delete', 'wpv-views' ) );
									}

									echo wpv_admin_table_row_actions( $row_actions,	array(
												"data-ct-id" => $template_id,
												"data-postcount" => $asigned_count,
												"data-ct-name" => htmlentities( $post->post_title, ENT_QUOTES ),
												"data-viewactionnonce" => $ct_action_nonce,
												// Used by the "duplicate" action
												"data-msg" => htmlentities( __( 'Enter new title','wpv-views'), ENT_QUOTES ) 
											)
										);
								?>
							</td>
							<td class="wpv-admin-listing-col-usage">
								<?php echo wpv_content_template_used_for_list( $template_id ); ?>
							</td>
							<td class="wpv-admin-listing-col-date">
								<?php echo get_the_time( get_option( 'date_format' ), $template_id ); ?>
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


			<p class="add-new-view">
				<button class="button js-add-new-content-template"
						data-target="<?php echo add_query_arg( array( 'action' => 'wpv_ct_create_new' ), admin_url( 'admin-ajax.php' ) ); ?>">
					<i class="icon-plus"></i><?php _e( 'Add new Content Template','wpv-views' ) ?>
				</button>
			</p>

			<?php
		}

		wpv_admin_listing_pagination( 'view-templates', $wpv_found_posts, $wpv_args["posts_per_page"], $mod_url );

		// Render dialog templates.
		wpv_render_ct_listing_dialog_templates_arrangeby_name();
}

function wpv_admin_content_template_listing_usage( $usage = 'single' ) {
	?>
	<table class="wpv-views-listing widefat">

		<thead>
			<tr>
				<th class="wpv-admin-listing-col-usage"><?php _e('Used on','wpv-views') ?></th>
				<th class="wpv-admin-listing-col-used-title"><?php _e('Template used','wpv-views') ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th class="wpv-admin-listing-col-usage"><?php _e('Used on','wpv-views') ?></th>
				<th class="wpv-admin-listing-col-used-title"><?php _e('Template used','wpv-views') ?></th>
			</tr>
		</tfoot>
		<!-- / section for: sort by name -->

		<tbody class="js-wpv-views-listing-body">
			<?php
				echo wpv_admin_menu_content_template_listing_by_type_row( 'usage-' . $usage );
			?>
		</tbody>
	</table>

	<?php
	
	// Render dialog templates
	wpv_render_ct_listing_dialog_templates_arrangeby_usage();
}

// @todo this use of IN in the query can lead to long queries - problems

function wpv_content_template_used_for_list( $ct_id ) {
	global $WP_Views, $WPV_settings;
	$list = '';
	$layout_loop_template_for_view_id = get_post_meta( $ct_id, '_view_loop_id', true );
	if ( empty( $layout_loop_template_for_view_id ) ) {
		global $wpdb;
		$post_types_array = wpv_get_pt_tax_array();
		$count_single_post = count( $post_types_array['single_post'] );
		$count_archive_post = count( $post_types_array['archive_post'] );
		$count_taxonomy_post = count( $post_types_array['taxonomy_post'] );

		for ( $i=0; $i<$count_single_post; $i++ ) {
			$type = $post_types_array['single_post'][$i][0];
			$label = $post_types_array['single_post'][$i][1];
			if ( isset( $WPV_settings['views_template_for_' . $type] ) && $WPV_settings['views_template_for_' . $type] == $ct_id ) {
                $list .= '<li>' . $label . __(' (single)', 'wpv-views');
					$posts = $wpdb->get_col( 
						$wpdb->prepare(
							"SELECT {$wpdb->posts}.ID FROM {$wpdb->posts} 
							WHERE post_type = %s 
							AND post_status != 'auto-draft'",
							$type
						)
					);
					$count = sizeof( $posts );
					if ( $count > 0 ) {
						$posts = "'" . implode( "','", $posts ) . "'";
						$set_count = $wpdb->get_var( 
							$wpdb->prepare(
								"SELECT COUNT(post_id) FROM {$wpdb->postmeta} 
								WHERE meta_key = '_views_template' 
								AND meta_value = %s
								AND post_id IN ({$posts}) 
								LIMIT %d",
								$WPV_settings['views_template_for_' . $type],
								$count
							)
						);
						if ( ( $count - $set_count ) > 0 ) {
							$list .= sprintf(
									'<span class="%s"><a class="%s" data-target="%s"> %s</a></span>',
									'js-alret-icon-hide-' . $type,
									'button button-small button-leveled icon-warning-sign js-apply-for-all-posts js-alret-icon-hide-' . $type,
									add_query_arg(
											array(
													'action' => 'wpv_ct_update_posts',
													'type' => $type,
													'tid' => $ct_id,
													'wpnonce' => wp_create_nonce( 'work_view_template' ) ),
											admin_url( 'admin-ajax.php' ) ),
									sprintf( __( 'Bind %u %s ', 'wpv-views' ), $count - $set_count, $label ) );
						}
					}
				$list .= '</li>';
			}
		}

		for ( $i=0; $i < $count_archive_post; $i++ ) {
			$type = $post_types_array['archive_post'][$i][0];
			$label = $post_types_array['archive_post'][$i][1];
			if ( isset( $WPV_settings['views_template_archive_for_' . $type] ) && $WPV_settings['views_template_archive_for_' . $type] == $ct_id ) {
                $list .= '<li>' . $label . __(' (post type archive)','wpv-views') . '</li>';
			 }
		}

		for ( $i=0; $i < $count_taxonomy_post; $i++ ) {
			$type = $post_types_array['taxonomy_post'][$i][0];
			$label = $post_types_array['taxonomy_post'][$i][1];
			if ( isset( $WPV_settings['views_template_loop_' . $type] ) && $WPV_settings['views_template_loop_' . $type] == $ct_id ) {
                $list .= '<li>' . $label . __(' (taxonomy archive)','wpv-views') . '</li>';
			 }
		}
		
		if ( ! empty( $list ) ) {
			$list = '<ul class="wpv-taglike-list">' . $list . '</ul>';
		} else {
		   $list = '<span>' . __( 'No Post types/Taxonomies assigned', 'wpv-views' ) . '</span>';
		}
	} else {
		$view_loop_title = get_the_title( $layout_loop_template_for_view_id );
		$view_loop_status = get_post_status( $layout_loop_template_for_view_id );
		$view_loop_type_text = __( 'View', 'wpv-views' );
		if ( $WP_Views->is_archive_view( $layout_loop_template_for_view_id ) ) {
			$view_loop_type_text = __( 'WordPress Archive', 'wpv-views' );
		}
		if ( $view_loop_status == 'publish' ) {
			$view_loop_link = get_admin_url()."admin.php?page=views-editor&view_id=" . $layout_loop_template_for_view_id;
			$list = '<span>' . sprintf( __( 'This Content Template is used as the loop block for the %s <a href="%s" target="_blank">%s</a>', 'wpv-views' ), $view_loop_type_text, $view_loop_link, $view_loop_title ) . '</span>';
		} else {
			$list = '<span>' . sprintf( __( 'This Content Template is used as the loop block for the trashed %s <strong>%s</strong>', 'wpv-views' ), $view_loop_type_text, $view_loop_title ) . '</span>';
		}
	}
	return $list;
}

// TODO consider using WP_Views_archive_loops::get_archive_loops instead
function wpv_get_pt_tax_array(){
   static $post_types_array;
   static $taxonomies_array;
   static $wpv_posts_array;
   if ( !is_array($post_types_array) ){
	   $post_types = get_post_types( array('public' => true), 'objects' );
   }
   if ( !is_array($taxonomies_array) ){
	   $taxonomies = get_taxonomies( '', 'objects' );
   }
   $exclude_tax_slugs = array();
	$exclude_tax_slugs = apply_filters( 'wpv_admin_exclude_tax_slugs', $exclude_tax_slugs );

   if ( is_array($wpv_posts_array) ){
	   return $wpv_posts_array;
   }
	$wpv_posts_array['single_post'] = array();
	$wpv_posts_array['archive_post'] = array();
   foreach ( $post_types as $post_type ) {
		$wpv_posts_array['single_post'][] = array( $post_type->name, $post_type->label );
		if (!in_array($post_type->name, array('post', 'page', 'attachment')) && $post_type->has_archive ) {
			// take out Posts, Pages and Attachments for post types archive loops; take out posts without archives too
			$wpv_posts_array['archive_post'][] = array( $post_type->name, $post_type->label );
		}
   }
	$wpv_posts_array['taxonomy_post'] = array();
   foreach ( $taxonomies as $category_slug => $category ) {
	   if ( in_array($category_slug, $exclude_tax_slugs ) ) {
				continue;
	   }
	   if ( !$category->show_ui ) {
			continue; // Only show taxonomies with show_ui set to TRUE
		}
		$wpv_posts_array['taxonomy_post'][] = array( $category->name, $category->labels->name );
   }

   return $wpv_posts_array;
}

/**
 * @todo comment
 */ 
// TODO check if the action URL parameter is needed when creating a CT
function wpv_admin_menu_content_template_listing_by_type_row( $sort, $page = 0 ) {
	global $WPV_settings, $post, $wpdb;
	// $post_types = get_post_types( array('public' => true), 'objects' );
	$post_types_array = wpv_get_pt_tax_array();

	ob_start();
	if ( $sort == 'usage-single' ){

		$counter = count( $post_types_array['single_post'] );
		$alternate = '';
		for ( $i = 0; $i < $counter; ++$i ) {
			$type = $post_types_array['single_post'][ $i ][0];
			$label = $post_types_array['single_post'][ $i ][1];
			$alternate = ' alternate' == $alternate ? '' : ' alternate';

			?>
			<tr id="wpv_ct_list_row_<?php echo $type; ?>" class="js-wpv-ct-list-row<?php echo $alternate; ?>">

				<td class="wpv-admin-listing-col-usage post-title page-title column-title">
					<span class="row-title">
						<?php echo $label;?>
					</span>
					<?php
						$row_actions = array(
								"change_pt js-list-ct-action-change-pt" => sprintf( '<a href="#">%s</a>', __('Change Content Template','wpv-views') ) );

						echo wpv_admin_table_row_actions( $row_actions,	array(
								"data-msg" => 1,
								"data-sort" => $sort,
								"data-pt" => 'views_template_for_' . $type ) );
					?>
				</td>

				<td class="wpv-admin-listing-col-used-title">
					<ul>
						<?php
							$add_button = sprintf(
									'<a class="button button-small" data-disabled="1"
											href="%s">
										<i class="icon-plus"></i>
										%s
									</a>',
									add_query_arg(
											array(
													'post_type' => 'view-template',
													'action' => 'wpv_ct_create_new',
													'post_title' => urlencode( __( 'Content template for ','wpv-views' ) . $label ),
													'ct_selected' => 'views_template_for_' . $type,
													'toggle' => '1,0,0' ),
											admin_url( 'post-new.php' ) ),
									sprintf( __( 'Create a Content Template for single %s', 'wpv-views' ), $label ) );

							// TODO get_posts or explanation why is it done this way (optimalization?)
							$posts = $wpdb->get_col( 
								$wpdb->prepare(
									"SELECT {$wpdb->posts}.ID FROM {$wpdb->posts} 
									WHERE post_type = %s 
									AND post_status != 'auto-draft'",
									$type
								)
							);
							$count = sizeof( $posts );
							$posts_ids = "'" . implode( "','", $posts ) . "'";

							if ( isset( $WPV_settings[ 'views_template_for_' . $type ] ) ) {
								if ( $WPV_settings[ 'views_template_for_' . $type ] != 0 ) {
									$template = get_post( $WPV_settings[ 'views_template_for_' . $type ] );
									if ( is_object( $template ) ) {
										printf(
												'<a href="%s">%s</a>',
												add_query_arg( array( 'post' => $template->ID, 'action' => 'edit' ), admin_url( 'post.php' ) ),
												$template->post_title );
										if ( $count > 0 ) {
											$set_count = $wpdb->get_var(
												$wpdb->prepare(
													"SELECT COUNT(post_id) FROM {$wpdb->postmeta}
													WHERE meta_key = '_views_template'
													AND meta_value = %s
													AND post_id IN ({$posts_ids}) 
													LIMIT %d",
													$WPV_settings['views_template_for_' . $type],
													$count
												)
											);
											if ( ( $count - $set_count ) > 0 ) {
												?>
												<span class="js-alret-icon-hide-<?php echo $type; ?>">
													<?php
														printf(
																'<a class="%s" data-target="%s"> %s</a>',
																'button button-small button-leveled icon-warning-sign js-apply-for-all-posts',
																add_query_arg(
																		array(
																				'action' => 'wpv_ct_update_posts',
																				'type' => $type,
																				'tid' => $template->ID,
																				'wpnonce' => wp_create_nonce( 'work_view_template' ) ),
																		admin_url( 'admin-ajax.php' ) ),
																sprintf( __( 'Bind %u %s ', 'wpv-views' ), $count - $set_count, $label ) );
													?>
												</span>
												<?php
											}
										}
									} else {
										echo $add_button;
									}
								} else {
									echo $add_button;

									if ( $count > 0 ) {
										$set_count = $wpdb->get_var(
											$wpdb->prepare(
												"SELECT COUNT(post_id) FROM {$wpdb->postmeta}
												WHERE meta_key = '_views_template'
												AND meta_value != %s
												AND post_id IN ({$posts_ids}) 
												LIMIT %d",
												'0',
												$count
											)
										);
										if ( $set_count > 0) {
											?>
											<a class="button button-small js-single-unlink-template-open-dialog" href="#"
													data-unclear="<?php echo $set_count; ?>"
													data-slug="<?php echo $type; ?>"
													data-label="<?php echo htmlentities( $label, ENT_QUOTES ); ?>">
												<i class="icon-unlink"></i>
												<?php echo sprintf( __('Clear %d %s', 'wpv-views'), $set_count, $label ); ?>
											</a>
											<?php
										}
									}
								}
							} else {
								echo $add_button;
								if ( $count > 0 ) {
									$set_count = $wpdb->get_var(
										$wpdb->prepare(
											"SELECT COUNT(post_id) FROM {$wpdb->postmeta}
											WHERE meta_key = '_views_template'
											AND meta_value != %s
											AND post_id IN ({$posts_ids}) 
											LIMIT %d",
											'0',
											$count
										)
									);
									if ( $set_count > 0 ) {
										?>
										<a class="button button-small js-single-unlink-template-open-dialog" href="#"
												data-unclear="<?php echo $set_count; ?>"
												data-slug="<?php echo $type; ?>"
												data-label="<?php echo htmlentities( $label, ENT_QUOTES ); ?>">
											<i class="icon-unlink"></i>
											<?php echo sprintf( __('Clear %d %s', 'wpv-views'), $set_count, $label ); ?>
										</a>
										<?php
									}
								}
							}
						?>
					</ul>
				</td>
			</tr>
			<?php
		}

	} else if ( $sort == 'usage-post-archives' ){

		$alternate = '';
		$counter = count( $post_types_array['archive_post'] );
		for ( $i = 0; $i < $counter; ++$i ) {

			$type = $post_types_array['archive_post'][ $i ][0];
			$label = $post_types_array['archive_post'][ $i ][1];
			$add_button = sprintf(
					'<a class="button button-small" data-disabled="1" href="%s"><i class="icon-plus"></i> %s</a>',
					add_query_arg(
							array(
									'post_type' => 'view-template',
									'action' => 'wpv_ct_create_new',
									'post_title' => urlencode( __( 'Content template for ', 'wpv-views' ) . $label ),
									'ct_selected' => 'views_template_archive_for_' . $type,
									'toggle' => '0,1,0' ),
							admin_url( 'post-new.php' ) ),
					__( 'Add a new Content Template for this post type', 'wpv-views' ) );

			$alternate = ' alternate' == $alternate ? '' : ' alternate';
			?>
			<tr id="wpv_ct_list_row_<?php echo $type; ?>" class="js-wpv-ct-list-row<?php echo $alternate; ?>">
				<td class="post-title page-title column-title">
					<span class="row-title">
						<?php echo $label; ?>
					</span>
					<?php
						$row_actions = array(
								"change_pt js-list-ct-action-change-pt" => sprintf( '<a href="#">%s</a>', __( 'Change Content Template', 'wpv-views' ) ) );

						echo wpv_admin_table_row_actions( $row_actions,	array(
								"data-msg" => 1,
								"data-sort" => $sort,
								"data-pt" => 'views_template_archive_for_' . $type ) );
					?>
				</td>
				<td>
					<ul>
						<?php
							if ( isset( $WPV_settings[ 'views_template_archive_for_' . $type ] )
									&& $WPV_settings[ 'views_template_archive_for_' . $type ] != 0) {
								$post = get_post( $WPV_settings[ 'views_template_archive_for_' . $type ] );
								if ( is_object( $post ) ) {
									printf(
											'<a href="%s">%s</a>',
											add_query_arg(
													array( 'post' => $post->ID, 'action' => 'edit' ),
													admin_url( 'post.php' ) ),
											$post->post_title );
								} else {
									echo $add_button;
								}
							} else {
								echo $add_button;
							}
						?>
					</ul>
				</td>
			</tr>
			<?php
		}

	} else if ( $sort == 'usage-taxonomy-archives' ){

		$counter = count( $post_types_array['taxonomy_post'] );
		$alternate = '';

		for ( $i = 0; $i < $counter; ++$i ) {
			$type = $post_types_array['taxonomy_post'][ $i ][0];
			$label = $post_types_array['taxonomy_post'][ $i ][1];

			$add_button = sprintf(
					'<a class="button button-small js-wpv-ct-create-new-for-usage" data-disabled="1"
							data-title="%s" data-usage="%s" href="%s">
						<i class="icon-plus"></i>
						%s
					</a>',
					urlencode( __( 'Content template for ', 'wpv-views' ) . $label ),
					'views_template_loop_' . $type,
					add_query_arg(
							array(
									'post_type' => 'view-template',
									'action' => 'wpv_ct_create_new',
									'post_title' => urlencode( __( 'Content template for ', 'wpv-views' ) . $label ),
									'ct_selected' => 'views_template_loop_' . $type,
									'toggle' => '0,0,1' ),
							admin_url( 'post-new.php' ) ),
					__( 'Add a new Content Template for this taxonomy', 'wpv-views' ) );

			$alternate = ' alternate' == $alternate ? '' : ' alternate';

			?>
			<tr id="wpv_ct_list_row_<?php echo $type; ?>" class="js-wpv-ct-list-row<?php echo $alternate; ?>">
				<td class="post-title page-title column-title">
					<span class="row-title">
						<?php echo $label;?>
					</span>
					<?php
						$row_actions = array(
								"change_pt js-list-ct-action-change-pt" => sprintf( '<a href="#">%s</a>', __( 'Change Content Template', 'wpv-views' ) ) );

						echo wpv_admin_table_row_actions( $row_actions,	array(
								"data-msg" => 2,
								"data-sort" => $sort,
								"data-pt" => 'views_template_loop_' . $type ) );
					?>
				</td>
				<td>
					<ul>
						<?php
							if ( isset( $WPV_settings[ 'views_template_loop_' . $type ] )
									&& $WPV_settings[ 'views_template_loop_' . $type ] != 0 ) {
								$post = get_post( $WPV_settings['views_template_loop_' . $type] );
								if ( is_object( $post ) ) {
									printf(
											'<a href="%s">%s</a>',
											add_query_arg(
													array( 'post' => $post->ID, 'action' => 'edit' ),
													admin_url( 'post.php' ) ),
											$post->post_title );
								} else {
									echo $add_button;
								}
							} else {
								echo $add_button;
							}
						?>
					</ul>
				</td>
			</tr>
			<?php
		}
	}

	$row = ob_get_contents();
	ob_end_clean();

	return $row;
}
