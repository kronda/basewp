<?php

/*
* We can enable this to hide the Loop selection section
* TODO hide it, refresh the page and show it: the list of loops is still hidden
*/

// add_filter('wpv_sections_archive_loop_show_hide', 'wpv_show_hide_archive_loop', 1,1);

function wpv_show_hide_archive_loop( $sections ) {
	$sections['archive-loop'] = array(
		'name' => __( 'Loops Selection', 'wpv-views' ),
	);
	return $sections;
}

add_action( 'view-editor-section-archive-loop', 'add_view_loop_selection', 10, 2 );

function add_view_loop_selection( $view_settings, $view_id ) {
	$hide = '';
	if ( isset( $view_settings['sections-show-hide'] )
		&& isset( $view_settings['sections-show-hide']['archive-loop'] )
		&& 'off' == $view_settings['sections-show-hide']['archive-loop'] )
	{
		$hide = ' hidden';
	}
	if ( 'layouts-loop' ==  $view_settings['view-query-mode'] ) {
	$section_help_pointer = WPV_Admin_Messages::edit_section_help_pointer( 'loops_selection_layouts' );
	?>
	<div class="wpv-setting-container wpv-settings-archive-loops js-wpv-settings-archive-loop<?php echo $hide; ?>">
		<div class="wpv-settings-header">
			<h3>
				<?php _e('Loops Selection', 'wpv-views' ) ?>
			</h3>
			<i class="icon-question-sign js-display-tooltip" 
				data-header="<?php echo esc_attr( $section_help_pointer['title'] ); ?>" 
				data-content="<?php echo esc_attr( $section_help_pointer['content'] ); ?>">
			</i>
		</div>
		<div class="wpv-setting js-wpv-setting">
			<p>
				<?php _e( 'This WordPress Archive is part of a Layout, so it will display the archive(s) to which the Layout is assigned.', 'wpv-views' ); ?>
			</p>
		</div>
	</div>
	<?php
	} else {
	$section_help_pointer = WPV_Admin_Messages::edit_section_help_pointer( 'loops_selection' );
	?>
	<div class="wpv-setting-container wpv-settings-archive-loops js-wpv-settings-archive-loop<?php echo $hide; ?>">
		<div class="wpv-settings-header">
			<h3>
				<?php _e('Loops Selection', 'wpv-views' ) ?>
				<i class="icon-question-sign js-display-tooltip" 
					data-header="<?php echo esc_attr( $section_help_pointer['title'] ); ?>" 
					data-content="<?php echo esc_attr( $section_help_pointer['content'] ); ?>">
				</i>
			</h3>
		</div>
		<div class="wpv-setting js-wpv-setting">
			<form class="js-loop-selection-form">
				<?php render_view_loop_selection_form( $view_id ); ?>
			</form>
		</div>
		<span class="update-action-wrap auto-update js-wpv-update-action-wrap">
			<span class="js-wpv-message-container"></span>
			<span type="hidden" data-success="<?php echo esc_attr( __( 'Updated', 'wpv-views') ); ?>" data-unsaved="<?php echo esc_attr( __('Not saved', 'wpv-views') ); ?>" data-nonce="<?php echo wp_create_nonce( 'wpv_view_loop_selection_nonce' ); ?>" class="js-wpv-loop-selection-update" />
		</span>
	</div>
	<?php
	}
}


function render_view_loop_selection_form( $view_id = 0 ) {
	global $WPV_view_archive_loop, $WPV_settings;
	$WPV_view_archive_loop->_view_edit_options( $view_id, $WPV_settings ); // TODO check if we just need the $WPV_settings above

	$asterisk = ' <span style="color:red">*</span>';
	$asterisk_explanation = __( '<span style="color:red">*</span> A different WordPress Archive is already assigned to this item', 'wpv-views' );
	$show_asterisk_explanation = false;

	// Label and template for "View archive" link.
	$view_archive_template = '<span style="margin-left: 3px;"></span><a style="text-decoration: none;" target="_blank" href="%s"><i class="icon-external-link icon-small"></i></a>';

	// Prepare archive URL for different loops.
	$recent_posts = get_posts( array( "posts_per_page" => 1 ) );
	$default_search_term = __( 'something', 'wpv-views' );
	if( !empty( $recent_posts ) ) {
		$recent_post = reset( $recent_posts );

		// Try to get first word of the post and use it as a search term for search-page loop.
		$recent_post_content = explode( " ", strip_tags( $recent_post->post_content ), 1 );
		$first_word_in_post = reset( $recent_post_content );
		if( false != $first_word_in_post ) {
			$search_page_archive_url = get_search_link( $first_word_in_post );
		} else {
			// No first word, the post is empty (wordless after striping html tags, to be precise).
			$search_page_archive_url = get_search_link( $default_search_term );
		}

		$post_date = new DateTime( $recent_post->post_date );

	} else {
		// No recent post exists, use default values.
		$search_page_archive_url = get_search_link( $default_search_term );
		$post_date = new DateTime(); // now
	}
	$post_year = $post_date->format( "Y" );
	$post_month = $post_date->format( "n" );
	$post_day = $post_date->format( "j" );

	/* $loops: Definition of standard WP loops, each array element contains array of "display_name" and "archive_url"
	 * (url to display the archive in frontend). */
	$loops = array(
			'home-blog-page' => array(
					"display_name" => __( 'Home/Blog', 'wpv-views' ),
					"archive_url" => home_url() ),
			'search-page' => array(
					"display_name" => __( 'Search results', 'wpv-views' ),
					"archive_url" => $search_page_archive_url ),
			'author-page' => array(
					"display_name" => __( 'Author archives', 'wpv-views' ),
					"archive_url" => get_author_posts_url( get_current_user_id() ) ),
			'year-page' => array(
					"display_name" => __( 'Year archives', 'wpv-views' ),
					"archive_url" => get_year_link( $post_year ) ),
			'month-page' => array(
					"display_name" => __( 'Month archives', 'wpv-views' ),
					"archive_url" => get_month_link( $post_year, $post_month ) ),
			'day-page' => array(
					"display_name" => __( 'Day archives', 'wpv-views' ),
					"archive_url" => get_day_link( $post_year, $post_month, $post_day ) )
	);

	// === Selection for Native WordPress Archive Loops === //
	?>
	<h3><?php _e( 'Native WordPress Archive Loops', 'wpv-views' ); ?></h3>
	<div class="wpv-advanced-setting">
		<ul class="enable-scrollbar wpv-mightlong-list">
			<?php
				foreach ( $loops as $loop => $loop_definition ) {
					$show_asterisk = false;
					$is_checked = ( isset( $WPV_settings['view_' . $loop] ) && $WPV_settings['view_' . $loop] == $view_id );
					if ( isset( $WPV_settings['view_' . $loop] )
						&& $WPV_settings['view_' . $loop] != $view_id
						&& $WPV_settings['view_' . $loop] != 0 )
					{
						$show_asterisk = true;
						$show_asterisk_explanation = true;
					}
					?>
						<li>
							<input type="checkbox" <?php checked( $is_checked ); ?> id="wpv-view-loop-<?php echo esc_attr( $loop ); ?>" name="wpv-view-loop-<?php echo esc_attr( $loop ); ?>" autocomplete="off" />
							<label for="wpv-view-loop-<?php echo esc_attr( $loop ); ?>"><?php
									echo $loop_definition[ "display_name" ];
									echo $show_asterisk ? $asterisk : '';
							?></label>
							<?php
								if( $is_checked ) {
									printf( $view_archive_template, $loop_definition[ "archive_url" ] );
								}
							?>
						</li>
					<?php
				}
			?>
		</ul>
		<?php
			if ( $show_asterisk_explanation ) {
				?>
					<span class="wpv-options-box-info">
						<?php echo $asterisk_explanation; ?>
					</span>
				<?php
			}
		?>
	</div>
	<?php

	// === Selection for Post Type Archive Loops === //

	/* Definition of post type archive loops. Keys are post type slugs and each array element contains array of
	 * "display_name" and "archive_url" (url to display the archive in frontend) and "loop".*/
	$pt_loops = array();

	$show_asterisk_explanation = false;
	// Only offer loops for post types that already have an archive
	$post_types = get_post_types( array( 'public' => true, 'has_archive' => true), 'objects' );
	foreach ( $post_types as $post_type ) {
		if ( ! in_array( $post_type->name, array( 'post', 'page', 'attachment' ) ) ) {
			$pt_loops[ $post_type->name ] = array(
					'loop' => 'cpt_' . $post_type->name,
					'display_name' => $post_type->labels->name,
					'archive_url' => get_post_type_archive_link( $post_type->name ) );
		}
	}

	if ( count( $pt_loops ) > 0 ) {
		?>
		<h3><?php _e( 'Post Type Archive Loops', 'wpv-views' ); ?></h3>
		<div class="wpv-advanced-setting">
			<ul class="enable-scrollbar wpv-mightlong-list">
				<?php
					foreach ( $pt_loops as $loop_definition ) {
						$loop = $loop_definition[ 'loop' ];
						$show_asterisk = false;
						$is_checked = ( isset( $WPV_settings['view_' . $loop] ) && $WPV_settings['view_' . $loop] == $view_id );
						if ( isset( $WPV_settings['view_' . $loop] ) && $WPV_settings['view_' . $loop] != $view_id && $WPV_settings['view_' . $loop] != 0 ) {
							$show_asterisk = true;
							$show_asterisk_explanation = true;
						}
						?>
							<li >
								<input type="checkbox" <?php checked( $is_checked ); ?> id="wpv-view-loop-<?php echo esc_attr( $loop ); ?>" name="wpv-view-loop-<?php echo esc_attr( $loop ); ?>" autocomplete="off" />
								<label for="wpv-view-loop-<?php echo esc_attr( $loop ); ?>">
									<?php
										echo $loop_definition[ 'display_name' ];
										echo $show_asterisk ? $asterisk : '';
									?>
								</label>
								<?php
									if( $is_checked ) {
										printf( $view_archive_template, $loop_definition[ 'archive_url' ] );
									}
								?>
							</li>
						<?php
					}
				?>
			</ul>
			<?php
				if ( $show_asterisk_explanation ) {
					?>
						<span class="wpv-options-box-info">
							<?php echo $asterisk_explanation; ?>
						</span>
					<?php
				}
			?>
		</div>
		<?php
	}

	// === Selection for Taxonomy Archive Loops === //
	$taxonomies = get_taxonomies( '', 'objects' );
	$exclude_tax_slugs = apply_filters( 'wpv_admin_exclude_tax_slugs', array() );

	// TODO get_terms( $taxonomies, array( "fields" => "id", hide_empty => 1 ) )
	// and then get_term_link( $term_id, $taxonomy_slug )
	// get_terms( $taxonomy_slug, array( "fields" => "id", "hide_empty" => 1, "number" => 1 ) )

	?>
	<h3><?php _e( 'Taxonomy Archive Loops', 'wpv-views' ); ?></h3>
	<?php $show_asterisk_explanation = false; ?>
	<div class="wpv-advanced-setting">
		<ul class="enable-scrollbar wpv-mightlong-list">
			<?php
				foreach ( $taxonomies as $category_slug => $category ) {
					if ( in_array( $category_slug, $exclude_tax_slugs ) ) {
						continue;
					}

					// Only show taxonomies with show_ui set to TRUE
					if ( !$category->show_ui ) {
						continue;
					}

					$name = $category->name;
					$show_asterisk = false;
					$is_checked = ( isset( $WPV_settings['view_taxonomy_loop_' . $name ] ) && $WPV_settings['view_taxonomy_loop_' . $name ] == $view_id );
					if ( isset( $WPV_settings['view_taxonomy_loop_' . $name ] )
						&& $WPV_settings['view_taxonomy_loop_' . $name ] != $view_id
						&& $WPV_settings['view_taxonomy_loop_' . $name ] != 0 )
					{
						$show_asterisk = true;
						$show_asterisk_explanation = true;
					}
					?>
						<li>
							<input type="checkbox" <?php checked( $is_checked ); ?> id="wpv-view-taxonomy-loop-<?php echo esc_attr( $name ); ?>" name="wpv-view-taxonomy-loop-<?php echo esc_attr( $name ); ?>" autocomplete="off" />
							<label for="wpv-view-taxonomy-loop-<?php echo esc_attr( $name ); ?>">
								<?php
									echo $category->labels->name;
									echo $show_asterisk ? $asterisk : '';
								?>
							</label>
							<?php
								if( $is_checked ) {
									// Get ID of a term that has some posts, if such term exists.
									$terms_with_posts = get_terms( $category_slug, array( "hide_empty" => 1, "number" => 1 ) );
									if( ( $terms_with_posts instanceof WP_Error ) or empty( $terms_with_posts ) ) {
										printf(
											'<span style="margin-left: 3px;"></span><span style="color: grey"><i class="icon-external-link icon-small" title="%s"></i></span>',
											sprintf(
													__( 'The %s page cannot be viewed because no post has any %s.', 'wpv-views' ),
													$category->labels->name,
													$category->labels->singular_name ) );
									} else {
										$term = $terms_with_posts[0];
										printf( $view_archive_template, get_term_link( $term, $category_slug ) );
									}
								}
							?>
						</li>
					<?php
				}
			?>
		</ul>
		<?php
			if ( $show_asterisk_explanation ) {
				?>
					<span class="wpv-options-box-info">
						<?php echo $asterisk_explanation; ?>
					</span>
				<?php
			}
		?>
	</div>
	<?php
}

/**
* wpv_update_loop_selection_callback
*
* Save WPA loop selection section
*
* @since unknown
*/

add_action( 'wp_ajax_wpv_update_loop_selection', 'wpv_update_loop_selection_callback' );

function wpv_update_loop_selection_callback() {
	if ( ! current_user_can( 'manage_options' ) ) {
		$data = array(
			'type' => 'capability',
			'message' => __( 'You do not have permissions for that.', 'wpv-views' )
		);
		wp_send_json_error( $data );
	}
	if ( 
		! isset( $_POST["wpnonce"] )
		|| ! wp_verify_nonce( $_POST["wpnonce"], 'wpv_view_loop_selection_nonce' ) 
	) {
		$data = array(
			'type' => 'nonce',
			'message' => __( 'Your security credentials have expired. Please reload the page to get new ones.', 'wpv-views' )
		);
		wp_send_json_error( $data );
	}
	if (
		! isset( $_POST["id"] )
		|| ! is_numeric( $_POST["id"] )
		|| intval( $_POST['id'] ) < 1 
	) {
		$data = array(
			'type' => 'id',
			'message' => __( 'Wrong or missing ID.', 'wpv-views' )
		);
		wp_send_json_error( $data );
	}
	global $WPV_view_archive_loop;
	parse_str( $_POST['form'], $form_data );
	$WPV_view_archive_loop->update_view_archive_settings( $_POST["id"], $form_data );
	$loop_form = '';
	ob_start();
	render_view_loop_selection_form( $_POST['id'] );
	$loop_form = ob_get_contents();
	ob_end_clean();
	do_action( 'wpv_action_wpv_save_item', $_POST["id"] );
	$data = array(
		'id' => $_POST["id"],
		'updated_archive_loops' => $loop_form,
		'message' => __( 'Loop Selection saved', 'wpv-views' )
	);
	wp_send_json_success( $data );
}