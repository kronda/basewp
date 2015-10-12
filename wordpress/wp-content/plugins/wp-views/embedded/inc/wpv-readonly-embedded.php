<?php

/**
* wpv-readonly-embedded.php
*
* Readonly generators
*
* @since 1.6.2
*/


/**
 * views_embedded_html
 *
 * Renders the readonly page to test the summaries and readonly textareas
 *
 * @since 1.6.2
 */
function views_embedded_html() {
	global $WP_Views, $post;
	if ( 
		isset( $_GET['view_id'] ) 
		&& is_numeric( $_GET['view_id'] ) 
	) {
		$view_id = (int) $_GET['view_id'];
		$view = get_post( $view_id );
		if ( null == $view ) {
			wpv_die_toolset_alert_error( __( 'You attempted to edit a View that doesn&#8217;t exist. Perhaps it was deleted?', 'wpv-views' ) );
		} elseif ( 'view' != $view->post_type ) {
			wpv_die_toolset_alert_error( __('You attempted to edit a View that doesn&#8217;t exist. Perhaps it was deleted?', 'wpv-views') );
		} else {
			$view_settings = $WP_Views->get_view_settings( $_GET['view_id'] );
			$view_layout_settings = $WP_Views->get_view_layout_settings( $_GET['view_id'] );
			if ( 
				isset( $view_settings['view-query-mode'] ) 
				&& ( 'normal' ==  $view_settings['view-query-mode'] ) 
			) {
				$post = $view;
				if ( get_post_status( $view_id ) == 'trash' ) {
					wpv_die_toolset_alert_error( __('You can&#8217;t edit this View because it is in the Trash. Please restore it and try again.', 'wpv-views') );
				}
			} else {
				wpv_die_toolset_alert_error( __( 'You attempted to edit a View that doesn&#8217;t exist. Perhaps it was deleted?', 'wpv-views') );
			}
		}
	} else {
		wpv_die_toolset_alert_error( __('You attempted to edit a View that doesn&#8217;t exist. Perhaps it was deleted?', 'wpv-views') );
	}
	?>
		<div class="wrap toolset-views toolset-views-embedded">
			<div id="icon-edit" class="icon32 icon32-posts-post"><br></div>
			<h2><?php _e( 'Preview View','wpv-views' ); ?></h2>
			<input id="post_ID" class="js-post_ID" type="hidden" value="<?php echo esc_attr( $view_id ); ?>" />
			<div id="js-wpv-general-actions-bar" class="wpv-general-actions-bar">
			<?php
			wpv_get_embedded_promotional_box( 'view' );
			?>
			</div>
			<?php
			if ( ! isset( $view_settings['view_purpose'] ) ) {
				$view_settings['view_purpose'] = 'full';
			}
			?>
			<input type="hidden" class="js-wpv-view-purpose" value="<?php echo esc_attr( $view_settings['view_purpose'] ); ?>" />
			<?php
			if ( isset( $_GET['in-iframe-for-layout'] ) ) {
				$in_iframe = 'yes';
			} else {
				$in_iframe = '';
			}
			?>
			<input type="hidden" class="js-wpv-display-in-iframe" value="<?php echo esc_attr( $in_iframe ); ?>" />

			<div class="wpv-title-section">
				<div class="wpv-setting-container wpv-settings-title-and-desc">
					<div class="wpv-settings-header">
						<h3>
							<?php _e( 'Title and Description', 'wpv-views' ); ?>
						</h3>
				</div>
				<div class="wpv-setting">
					<h3 style="margin-top:8px;font-size:18px;line-height:1.5;">
						<?php echo esc_html( get_the_title( $view_id ) ); ?>
					</h3>
					<p>
						<?php 
						_e( 'Slug of this View: ', 'wpv-views' ); 
						echo '<code>' . esc_attr( $view->post_name ) . '</code>'; 
						?>
					</p>
					<?php
						$view_description = get_post_meta( $_GET['view_id'], '_wpv_description', true );
						if ( 
							isset( $view_description ) 
							&& ! empty( $view_description ) 
						) {
							printf( '<p>%s</p>', esc_html( $view_description ) );
						}
					?>
				</div>
			</div>
		</div> <!-- .wpv-title-section -->
		
		<div class="wpv-query-section">
			<?php
				// Commented out in Views 1.8
				// wpv_get_embedded_view_introduction_data();
			?>
			<h3 class="wpv-section-title">
				<?php _e('The Query section determines what content the View loads from the database','wpv-views') ?>
			</h3>
			<?php do_action( 'view-embedded-section-query', $view_settings, $view_id ); ?>
		</div>
		
		<div class="wpv-filter-section">
			<h3 class="wpv-section-title">
				<?php _e('The Filter section lets you set up pagination and parametric search, which let visitors control the View query','wpv-views') ?>
			</h3>
			<?php
				// Commented out in Views 1.8
				// wpv_get_embedded_view_filter_introduction_data();
			?>
			<?php do_action( 'view-embedded-section-filter', $view_settings, $view_id ); ?>
		</div>
		
		<div class="wpv-layout-section">
			<h3 class="wpv-section-title"><?php _e('The Loop Output section styles the View output on the page.','wpv-views') ?></h3>
			<?php
				// Commented out in Views 1.8
				// wpv_get_embedded_view_layout_introduction_data();
			?>
			<?php do_action( 'view-embedded-section-layout', $view_settings, $view_layout_settings, $view_id ); ?>
			<?php do_action( 'view-embedded-section-extra', $view_settings, $view_id ); ?>
		</div>

		<div class="wpv-help-section">
			<div class="js-show-toolset-message"
					data-close="false"
					data-tutorial-button-text="<?php echo esc_attr( __('Learn how to display Views','wpv-views') ) ?>" 
					data-tutorial-button-url="http://wp-types.com/documentation/user-guides/views/?utm_source=viewsplugin&utm_campaign=views&utm_medium=embedded-view-readonly-info&utm_term=Want to see this in action?#2.5">
				<h2><?php _e( 'Want to see this in action?','wpv-views' ) ?></h2>
			</div>
		</div>
	</div>
	<?php
}


/**
 * content_templates_embedded_html
 *
 * Renders the readonly Content Template summary
 *
 * @since 1.6.2
 */
function content_templates_embedded_html() {
	global $post;
	if ( 
		isset( $_GET['view_id'] ) 
		&& is_numeric( $_GET['view_id'] ) 
	) {
		$view_id = (int) $_GET['view_id'];
		$view = get_post( $view_id );
		if ( 
			( null == $view ) 
			|| ( 'view-template' != $view->post_type ) 
		) {
			wpv_die_toolset_alert_error( __( 'You attempted to edit a Content Template that doesn&#8217;t exist. Perhaps it was deleted?', 'wpv-views') );
		} else {
			$post = $view;
			if ( get_post_status( $view_id ) == 'trash' ) {
				wpv_die_toolset_alert_error( __( 'You can&#8217;t edit this Content Template because it is in the Trash. Please restore it and try again.', 'wpv-views') );
			}
		}
	} else {
		wpv_die_toolset_alert_error( __('You attempted to edit a Content Template that doesn&#8217;t exist. Perhaps it was deleted?', 'wpv-views') );
	}
	?>
	<div class="wrap toolset-views toolset-views-embedded">
		<div id="icon-edit" class="icon32 icon32-posts-post"><br></div>
		<h2><?php _e( 'Preview Content Template','wpv-views' ); ?></h2>
		<input id="post_ID" class="js-post_ID" type="hidden" value="<?php echo esc_attr( $view_id ); ?>" />
		<div id="js-wpv-general-actions-bar" class="wpv-general-actions-bar">
		<?php
		wpv_get_embedded_promotional_box( 'ct' );
		?>
		</div>
		<?php
		if ( isset( $_GET['in-iframe-for-layout'] ) ) {
			$in_iframe = 'yes';
		} else {
			$in_iframe = '';
		}
		?>
		<input type="hidden" class="js-wpv-display-in-iframe" value="<?php echo esc_attr( $in_iframe ); ?>" />
		<div class="wpv-title-section">
			<div class="wpv-setting-container">
				<h3 style="margin-top:8px;font-size:18px;line-height:1.5;">
					<?php echo esc_html( get_the_title( $view_id ) ); ?>
				</h3>
				<p>
					<?php 
					_e( 'Slug of this Content Template: ', 'wpv-views' ); 
					echo '<code>' . esc_attr( $view->post_name ) . '</code>'; 
					?>
				</p>
				<?php
					$view_description = get_post_meta( $view_id, '_wpv-content-template-decription', true );
					if ( 
						isset( $view_description ) 
						&& ! empty( $view_description ) 
					) {
						printf( '<p>%s</p>', esc_html( $view_description ) );
					}
				?>
			</div>
				<?php
					wpv_get_embedded_content_template_introduction_data();
				?>
			<div class="wpv-setting-container">
				<div class="wpv-ct-editors">
					<?php
						$full_view = get_post( $view_id );
						$content = $full_view->post_content;
					?>
					<textarea cols="30" rows="10" id="wpv_content" name="wpv_content"><?php echo esc_textarea( $content ); ?></textarea>
				</div>
			</div>
		</div>
	</div>
	<?php
}


/**
 * view_archives_embedded_html
 *
 * Renders the readonly page to test the summaries and readonly textareas
 *
 * @since 1.6.2
 */
function view_archives_embedded_html() {
	global $WP_Views, $post;
	if ( 
		isset( $_GET['view_id'] ) 
		&& is_numeric( $_GET['view_id'] ) 
	) {
		$view_id = (int) $_GET['view_id'];
		$view = get_post( $view_id );
		if ( 
			( null == $view ) 
			|| ( 'view' != $view->post_type ) 
		) {
			wpv_die_toolset_alert_error( __( 'You attempted to edit a View that doesn&#8217;t exist. Perhaps it was deleted?', 'wpv-views' ) );
		} else {
			$view_settings = $WP_Views->get_view_settings( $_GET['view_id'] );
			$view_layout_settings = $WP_Views->get_view_layout_settings( $_GET['view_id'] );
			if ( isset( $view_settings['view-query-mode'] )
				&& ( 
					( 'archive' ==  $view_settings['view-query-mode'] )
					|| ( 'layouts-loop' ==  $view_settings['view-query-mode'] ) // For elements coming from the Layouts post loop cell
				) 
			) {
				$post = $view;
				if ( get_post_status( $view_id ) == 'trash' ) {
					wpv_die_toolset_alert_error( __( 'You can&#8217;t edit this View because it is in the Trash. Please restore it and try again.', 'wpv-views' ) );
				}
			} else {
				wpv_die_toolset_alert_error( __( 'You attempted to edit a View that doesn&#8217;t exist. Perhaps it was deleted?', 'wpv-views' ) );
			}
		}
	} else {
		wpv_die_toolset_alert_error( __( 'You attempted to edit a View that doesn&#8217;t exist. Perhaps it was deleted?', 'wpv-views' ) );
	}
	?>
	<div class="wrap toolset-views toolset-views-embedded">
		<div id="icon-edit" class="icon32 icon32-posts-post"><br></div>
		<h2>
			<?php
			if ( 'archive' ==  $view_settings['view-query-mode'] ) {
				echo __( 'Preview WordPress Archive', 'wpv-views' );
			} else if ( 'layouts-loop' ==  $view_settings['view-query-mode'] ) {
				echo __( 'Preview Layouts Loop View', 'wpv-views' );
			}
			?>
		</h2>
		<input id="post_ID" class="js-post_ID" type="hidden" value="<?php echo esc_attr( $view_id ); ?>" />
		<div id="js-wpv-general-actions-bar" class="wpv-general-actions-bar">
		<?php
		wpv_get_embedded_promotional_box( 'wpa' );
		?>
		</div>
		<?php
		if ( ! isset( $view_settings['view_purpose'] ) ) {
			$view_settings['view_purpose'] = 'full';
		}
		?>
		<input type="hidden" class="js-wpv-view-purpose" value="<?php echo esc_attr( $view_settings['view_purpose'] ); ?>" />

		<?php
		if ( isset( $_GET['in-iframe-for-layout'] ) ) {
			$in_iframe = 'yes';
		} else {
			$in_iframe = '';
		}
		?>
		<input type="hidden" class="js-wpv-display-in-iframe" value="<?php echo esc_attr( $in_iframe ); ?>" />

		<div class="wpv-title-section">
			<div class="wpv-setting-container wpv-settings-title-and-desc">
				<div class="wpv-settings-header">
					<h3>
						<?php _e( 'Title and Description', 'wpv-views' ); ?>
					</h3>
				</div>
				<div class="wpv-setting">
					<h3 style="margin-top:8px;font-size:18px;line-height:1.5;">
						<?php echo esc_html( get_the_title( $view_id ) ); ?>
					</h3>
					<p>
						<?php 
						_e( 'Slug of this WordPress Archive: ', 'wpv-views' ); 
						echo '<code>' . esc_attr( $view->post_name ) . '</code>'; 
						?>
					</p>
					<?php
						$view_description = get_post_meta( $_GET['view_id'], '_wpv_description', true );
						if ( 
							isset( $view_description ) 
							&& !empty( $view_description ) 
						) {
							printf( '<p>%s</p>', esc_html( $view_description ) );
						}
					?>
				</div>
			</div>
		</div> <!-- .wpv-title-section -->
		
		<?php
			if ( 'archive' ==  $view_settings['view-query-mode'] ) {
				?>
				<div class="wpv-query-section">
					<?php
						// Commented out in Views 1.8
						// wpv_get_embedded_wordpress_archive_introduction_data();
					?>
					<h3 class="wpv-section-title">
						<?php _e( 'The Loops Selection section determines which listing page to customize', 'wpv-views' ); ?>
					</h3>
					<?php do_action( 'view-embedded-section-archive-loop', $view_settings, $view_id ); ?>
				</div>
				<?php
			} else if ( 'layouts-loop' ==  $view_settings['view-query-mode'] ) {
				?>
				<div class="wpv-query-section">
					<?php
						// Commented out in Views 1.8
						// wpv_get_embedded_layouts_loop_introduction_data();
					?>
				</div>
				<?php
			}
		?>
		
		<div class="wpv-layout-section">
			<h3 class="wpv-section-title">
				<?php _e( 'The Loop Output section styles the View output on the page.','wpv-views' ) ?>
			</h3>
			<?php
				// Commented out in Views 1.8
				// wpv_get_embedded_view_layout_introduction_data();
			?>
			<?php do_action( 'view-embedded-section-layout', $view_settings, $view_layout_settings, $view_id ); ?>
			<?php do_action( 'view-embedded-section-extra', $view_settings, $view_id ); ?>
		</div>

		<div class="wpv-help-section"></div>
	</div>
	<?php
}


/**
 * Read-only sections
 */


/**
 * wpv_embedded_archive_loop
 *
 * Loop selection read-only section
 *
 * @param $view_settings
 * @param $view_id
 *
 * @since 1.6.2
 */
add_action( 'view-embedded-section-archive-loop', 'wpv_embedded_archive_loop', 10, 2 );

function wpv_embedded_archive_loop( $view_settings, $view_id ) {
	$section_help_pointer = WPV_Admin_Messages::edit_section_help_pointer( 'loops_selection' );
	?>
	<div class="wpv-setting-container">
		<div class="wpv-settings-header">
			<h3>
				<?php _e( 'Loop Selection', 'wpv-views' ) ?>
				<i class="icon-question-sign js-display-tooltip"
						data-header="<?php echo esc_attr( $section_help_pointer['title'] ); ?>"
						data-content="<?php echo esc_attr( $section_help_pointer['content'] ); ?>">
				</i>
			</h3>
		</div>
		<div class="wpv-setting">
			<?php
			global $WPV_settings, $WPV_view_archive_loop;
			$loops = $WPV_view_archive_loop->_get_post_type_loops();
			$builtin_loops = array(
					'home-blog-page' => __( 'Home/Blog', 'wpv-views' ),
					'search-page' => __( 'Search results', 'wpv-views' ),
					'author-page' => __( 'Author archives', 'wpv-views' ),
					'year-page' => __( 'Year archives', 'wpv-views' ),
					'month-page' => __( 'Month archives', 'wpv-views' ),
					'day-page' => __( 'Day archives', 'wpv-views' )	);
			$taxonomies = get_taxonomies( '', 'objects' );
			$exclude_tax_slugs = array();
			$exclude_tax_slugs = apply_filters( 'wpv_admin_exclude_tax_slugs', $exclude_tax_slugs );
			
			$selected = array();
			foreach ( $loops as $loop => $loop_name ) {
				if ( 
					isset( $WPV_settings[ 'view_' . $loop ] ) 
					&& ( $WPV_settings[ 'view_' . $loop ] == $view_id ) 
				) {
					$not_built_in = '';
					if ( ! isset( $builtin_loops[ $loop ] ) ) {
						$not_built_in = __( ' (post type archive)', 'wpv-views' );
					}
					$selected[] = '<li>' . esc_html( $loop_name ) . $not_built_in . '</li>';
				}
			}
			
			foreach ( $taxonomies as $category_slug => $category ) {
				if ( in_array( $category_slug, $exclude_tax_slugs ) ) {
					continue;
				}
				if ( ! $category->show_ui ) {
					continue; // Only show taxonomies with show_ui set to TRUE
				}
				$name = $category->name;
				if ( 
					isset( $WPV_settings[ 'view_taxonomy_loop_' . $name ] ) 
					&& ( $WPV_settings[ 'view_taxonomy_loop_' . $name ] == $view_id ) 
				) {
					$selected[] = '<li>' . esc_html( $category->labels->name ) . __( ' (taxonomy archive)', 'wpv-views' ) . '</li>';
				}
			}
			
			if ( empty( $selected ) ) {
				printf( '<p>%s</p>', __( 'This WordPress Archive is not used on any archive loops', 'wpv-views' ) );
			} else {
				printf( '<p>%s</p>', __( 'This WordPress Archive is used in the following archive loops: ', 'wpv-views' ) );
				?>
					<ul class="wpv-taglike-list js-list-views-loops">
						<?php echo implode( $selected ); ?>
					</ul>
				<?php
			}
			?>
		</div>
	</div>
	<?php
}


/**
* wpv_embedded_content_selection
*
* Content Selection read-only section
*
* @param $view_settings
*
* @since 1.6.2
*/
add_action( 'view-embedded-section-query', 'wpv_embedded_content_selection', 10 );

function wpv_embedded_content_selection( $view_settings ) {
	$section_help_pointer = WPV_Admin_Messages::edit_section_help_pointer( 'content_section' );
	?>
	<div class="wpv-setting-container">
		<div class="wpv-settings-header">
			<h3>
				<?php _e( 'Content Selection', 'wpv-views' ) ?>
				<i class="icon-question-sign js-display-tooltip"
						data-header="<?php echo esc_attr( $section_help_pointer['title'] ); ?>"
						data-content="<?php echo esc_attr( $section_help_pointer['content'] ); ?>">
				</i>
			</h3>
		</div>
		<div class="wpv-setting">
			<p>
			<?php echo sprintf( __( 'This View loads <strong>%s</strong>', 'wpv-views' ), wpv_get_query_type_summary( $view_settings, 'embedded-info' ) ); ?>
			</p>
		</div>
	</div>
	<?php
}

/**
* wpv_embedded_ordering
*
* Sorting read-only section
*
* @param $view_settings
*
* @since 1.6.2
*/

add_action( 'view-embedded-section-query', 'wpv_embedded_ordering', 20 );

function wpv_embedded_ordering( $view_settings ) {
	$section_help_pointer = WPV_Admin_Messages::edit_section_help_pointer( 'ordering' );
	?>
	<div class="wpv-setting-container">
		<div class="wpv-settings-header">
			<h3>
				<?php _e( 'Ordering', 'wpv-views' ) ?>
				<i class="icon-question-sign js-display-tooltip"
						data-header="<?php echo esc_attr( $section_help_pointer['title'] ); ?>"
						data-content="<?php echo esc_attr( $section_help_pointer['content'] ); ?>">
				</i>
			</h3>
		</div>
		<div class="wpv-setting">
			<p>
			<?php echo __( 'Results are ', 'wpv-views' ) . wpv_get_ordering_summary( $view_settings, 'embedded-info' ); ?>
			</p>
		</div>
	</div>
	<?php
}

add_action( 'view-embedded-section-query', 'wpv_embedded_limit_offset', 30 );

function wpv_embedded_limit_offset( $view_settings ) {
	$section_help_pointer = WPV_Admin_Messages::edit_section_help_pointer( 'limit_and_offset' );
	?>
	<div class="wpv-setting-container">
		<div class="wpv-settings-header">
			<h3>
				<?php _e( 'Limit and Offset', 'wpv-views' ) ?>
				<i class="icon-question-sign js-display-tooltip"
						data-header="<?php echo esc_attr( $section_help_pointer['title'] ); ?>"
						data-content="<?php echo esc_attr( $section_help_pointer['content'] ); ?>">
				</i>
			</h3>
		</div>
		<div class="wpv-setting">
			<p>
			<?php echo wpv_get_limit_offset_summary( $view_settings, 'embedded-info' ); ?>
			</p>
		</div>
	</div>
	<?php
}

/**
* wpv_embedded_query_filter
*
* Filters read-only section
*
* @param $view_settings
*
* @since 1.6.2
*
* @todo misses the date filter!
*/

add_action( 'view-embedded-section-query', 'wpv_embedded_query_filter', 40 );

function wpv_embedded_query_filter( $view_settings ) {
	$section_help_pointer = WPV_Admin_Messages::edit_section_help_pointer( 'filter_the_results' );
	?>
	<div class="wpv-setting-container">
		<div class="wpv-settings-header">
			<h3>
				<?php _e( 'Query Filter', 'wpv-views' ) ?>
				<i class="icon-question-sign js-display-tooltip"
						data-header="<?php echo esc_attr( $section_help_pointer['title'] ); ?>"
						data-content="<?php echo esc_attr( $section_help_pointer['content'] ); ?>">
				</i>
			</h3>
		</div>
		<div class="wpv-setting wpv-settings-content-filter">
			<?php
			$filters_summary = '';
			$status_filter = wpv_get_filter_status_summary_txt( $view_settings );
			if ( !empty( $status_filter ) ) {
				$filters_summary .= '<li>' . $status_filter . '</li>';
			}
			$author_filter = wpv_get_filter_post_author_summary_txt( $view_settings );
			if ( !empty( $author_filter ) ) {
				$filters_summary .= '<li>' . $author_filter . '</li>';
			}
			$id_filter = wpv_get_filter_post_id_summary_txt( $view_settings );
			if ( !empty( $id_filter ) ) {
				$filters_summary .= '<li>' . $id_filter . '</li>';
			}
			$search_filter = wpv_get_filter_post_search_summary_txt( $view_settings );
			if ( !empty( $search_filter ) ) {
				$filters_summary .= '<li>' . $search_filter . '</li>';
			}
			$taxonomy_search_filter = wpv_get_filter_taxonomy_search_summary_txt( $view_settings );
			if ( !empty( $taxonomy_search_filter ) ) {
				$filters_summary .= '<li>' . $taxonomy_search_filter . '</li>';
			}
			$custom_field_filter = wpv_get_filter_custom_field_summary_txt( $view_settings );
			if ( ! empty( $custom_field_filter ) ) {
				$filters_summary .= '<li class="filter-row-multiple">' . __( 'Select posts with custom field:', 'wpv-views' ) . $custom_field_filter . '</li>';
			}
			$taxonomy_filter = wpv_get_filter_taxonomy_summary_txt( $view_settings );
			if ( ! empty( $taxonomy_filter ) ) {
				$filters_summary .= '<li class="filter-row-multiple">' . __( 'Select posts with taxonomy:', 'wpv-views' ) . $taxonomy_filter . '</li>';
			}
			$post_relationship_filter = wpv_get_filter_post_relationship_summary_txt( $view_settings );
			if ( !empty( $post_relationship_filter ) ) {
				$filters_summary .= '<li>' . $post_relationship_filter . '</li>';
			}
			$parent_filter = wpv_get_filter_post_parent_summary_txt( $view_settings );
			if ( !empty( $parent_filter ) ) {
				$filters_summary .= '<li>' . $parent_filter . '</li>';
			}
			$taxonomy_parent_filter = wpv_get_filter_taxonomy_parent_summary_txt( $view_settings );
			if ( !empty( $taxonomy_parent_filter ) ) {
				$filters_summary .= '<li>' . $taxonomy_parent_filter . '</li>';
			}
			$taxonomy_terms_filter = wpv_get_filter_taxonomy_term_summary_txt( $view_settings );
			if ( !empty( $taxonomy_terms_filter ) ) {
				$filters_summary .= '<li>' . $taxonomy_terms_filter . '</li>';
			}
			$users_filter = wpv_get_filter_users_summary_txt( $view_settings );
			if ( !empty( $users_filter ) ) {
				$filters_summary .= '<li>' . $users_filter . '</li>';
			}
			$usermeta_field_filter = wpv_get_filter_usermeta_field_summary_txt( $view_settings );
			if ( !empty( $usermeta_field_filter ) ) {
				$filters_summary .= '<li class="filter-row-multiple">' . __( 'Select users with usermeta field:', 'wpv-views' ) . $usermeta_field_filter . '</li>';
			}
			if ( '' != $filters_summary ) {
			?>
			<ul class="filter-list filter-list-readonly">
			<?php echo $filters_summary; ?>
			</ul>
			<?php } else { ?>
			<p>
			<?php _e( 'No filters set', 'wpv-views'); ?>
			</p>
			<?php } ?>
		</div>
	</div>
	<?php
}

/**
* wpv_embedded_pagination
*
* Pagination read-only section
*
* @param $view_settings
*
* @since 1.6.2
*/

add_action( 'view-embedded-section-filter', 'wpv_embedded_pagination', 10 );

function wpv_embedded_pagination( $view_settings ) {
	$section_help_pointer = WPV_Admin_Messages::edit_section_help_pointer( 'pagination_and_sliders_settings' );
	?>
	<div class="wpv-setting-container">
		<div class="wpv-settings-header">
			<h3>
				<?php _e( 'Pagination and Slider settings', 'wpv-views' ) ?>
				<i class="icon-question-sign js-display-tooltip"
						data-header="<?php echo esc_attr( $section_help_pointer['title'] ); ?>"
						data-content="<?php echo esc_attr( $section_help_pointer['content'] ); ?>">
				</i>
			</h3>
		</div>
		<div class="wpv-setting">
			<p>
			<?php echo wpv_get_pagination_summary( $view_settings, 'embedded-info' ); ?>
			</p>
		</div>
	</div>
	<?php
}

/**
* wpv_embedded_filter_extra
*
* Filter HTML read-only section
*
* @param $view_settings
*
* @since 1.6.2
*/

add_action( 'view-embedded-section-filter', 'wpv_embedded_filter_extra', 20 );

function wpv_embedded_filter_extra( $view_settings ) {
	$section_help_pointer = WPV_Admin_Messages::edit_section_help_pointer( 'filters_html_css_js' );
	?>
	<div class="wpv-setting-container wpv-setting-container-horizontal">
		<div class="wpv-settings-header">
			<h3>
				<?php _e( 'Filter', 'wpv-views' ) ?>
				<i class="icon-question-sign js-display-tooltip"
						data-header="<?php echo esc_attr( $section_help_pointer['title'] ); ?>"
						data-content="<?php echo esc_attr( $section_help_pointer['content'] ); ?>">
				</i>
			</h3>
		</div>
		<div class="wpv-setting">
			<textarea cols="30" rows="10" id="wpv_filter_meta_html_content" name="wpv_filter_meta_html"><?php echo ( isset( $view_settings['filter_meta_html'] ) ) ? esc_textarea( $view_settings['filter_meta_html'] ) : ''; ?></textarea>
		</div>
	</div>
	<?php
}

/**
* wpv_embedded_layout_extra
*
* Layout HTML read-only section
*
* @param $view_settings
* @param $view_layout_settings
*
* @since 1.6.2
*/

add_action( 'view-embedded-section-layout', 'wpv_embedded_layout_extra', 10, 2 );

function wpv_embedded_layout_extra(  $view_settings, $view_layout_settings ) {
	$section_help_pointer = WPV_Admin_Messages::edit_section_help_pointer( 'layout_html_css_js' );
	?>
	<div class="wpv-setting-container wpv-setting-container-horizontal">
		<div class="wpv-settings-header">
			<h3>
				<?php _e( 'Loop Output', 'wpv-views' ) ?>
				<i class="icon-question-sign js-display-tooltip"
						data-header="<?php echo esc_attr( $section_help_pointer['title'] ); ?>"
						data-content="<?php echo esc_attr( $section_help_pointer['content'] ); ?>">
				</i>
			</h3>
		</div>
		<div class="wpv-setting">
			<textarea cols="30" rows="10" id="wpv_layout_meta_html_content" name="wpv_layout_layout_meta_html"><?php echo ( isset( $view_layout_settings['layout_meta_html'] ) ) ? esc_textarea( $view_layout_settings['layout_meta_html'] ) : ''; ?></textarea>
			<?php
			$templates = array();
			if ( isset( $view_layout_settings['included_ct_ids'] ) ) {
				$templates = explode( ',', $view_layout_settings['included_ct_ids'] );
				$templates = array_map( 'esc_attr', $templates );
				$templates = array_map( 'trim', $templates );
				$templates = array_filter( $templates, 'is_numeric' );
				$templates = array_map( 'intval', $templates );
				if ( count( $templates ) > 0 ) {
					?>
					<h4><?php _e( 'Templates for this View', 'wpv-views' ); ?></h4>
					<div class="wpv-advanced-setting">
						<ul>
						<?php
						foreach ( $templates as $tpl ) {
							echo '<li>';
							echo sprintf(
								'<a href="%s" title="%s" target="_blank">%s</a>',
								esc_url( admin_url( 'admin.php?page=view-templates-embedded&view_id=' . $tpl ) ),
								esc_attr( get_the_title( $tpl ) ),
								esc_attr( get_the_title( $tpl ) )
							);
							echo '</li>';
						}
						?>
						</ul>
					</div>
					<?php
				}
			}
			?>
		</div>
	</div>
	<?php
}

/**
* wpv_embedded_combined_output
*
* Content read-only section
*
* @param $view_settings
* @param $view_layout_settings
* @param $view_id
*
* @since 1.6.2
*/

add_action( 'view-embedded-section-layout', 'wpv_embedded_combined_output', 20, 3 );

function wpv_embedded_combined_output(  $view_settings, $view_layout_settings, $view_id ) {
	$section_help_pointer = WPV_Admin_Messages::edit_section_help_pointer( 'complete_output' );
	?>
	<div class="wpv-setting-container wpv-setting-container-horizontal">
		<div class="wpv-settings-header">
			<h3>
				<?php _e( 'Filter and Loop Output Integration', 'wpv-views' ) ?>
				<i class="icon-question-sign js-display-tooltip"
						data-header="<?php echo esc_attr( $section_help_pointer['title'] ); ?>"
						data-content="<?php echo esc_attr( $section_help_pointer['content'] ); ?>">
				</i>
			</h3>
		</div>
		<div class="wpv-setting">
			<?php
				$full_view = get_post( $view_id );
				$content = $full_view->post_content;
			?>
			<textarea cols="30" rows="10" id="wpv_content" name="wpv_content"><?php echo esc_textarea( $content ); ?></textarea>
		</div>
	</div>
	<?php
}


/**
 * Retrieve link to a post.
 *
 * @param array|mixed $link Link data. The array can (but doesnẗ have to) contain following elements:
 *     array(
 *         @type bool $is_disabed If true, the link should be disabled or not displayed at all. The URL will not work.
 *             If this value is missing, handle as true.
 *         @type string $url Link URL.
 *     )
 *     If the parameter is not an array, it's not valid and should be considered equal to array().
 * @param string $post_type Type of the post.
 * @param int $post_id Post ID.
 * @param string $link_purpose For finer distinguishing of the link purpose. Common values are "view" and "edit".
 *
 * @since 1.10
 */
add_filter( 'icl_post_link', 'wpv_embedded_post_link', 10, 4 );


/**
 * Adjust post links for embedded Views.
 *
 * Currently only CT edit links are supported. See icl_post_link filter for parameter description.
 *
 * @param $link
 * @param $post_type
 * @param $post_id
 * @param $link_purpose
 * @return array
 *
 * @since 1.10
 */
function wpv_embedded_post_link( $link, $post_type, $post_id, $link_purpose ) {
	global $WP_Views;
	// Skip if we're in full Views
	if( $WP_Views->is_embedded() ) {

		if( !is_array( $link ) ) {
			$link = array();
		}

		switch( $post_type ) {
			// Content Templates
			case WPV_Content_Template_Embedded::POST_TYPE:

				if( 'edit' == $link_purpose ) {
					// Content Template edit page

					// Check that CT exists and is not trashed.
					$ct = WPV_Content_Template_Embedded::get_instance( $post_id );
					if( ( null == $ct ) || $ct->is_trashed ) {
						$link['is_disabled'] = true;
					} else {
						// Generate the URL to the read-only page.
						$link['is_disabled'] = false;
						$link['url'] = esc_url(
							add_query_arg(
								array( 'page' => 'view-templates-embedded', 'view_id' => $post_id ),
								admin_url('admin.php')
							)
						);
					}
				}
				break;
		}
	}
	return $link;
}

