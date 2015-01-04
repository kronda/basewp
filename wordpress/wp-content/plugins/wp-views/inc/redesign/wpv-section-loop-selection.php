<?php

/*
* We can enable this to hide the Loop selection section
* TODO hide it, refresh the page and show it: the list of loops is still hidden
*/

// add_filter('wpv_sections_archive_loop_show_hide', 'wpv_show_hide_archive_loop', 1,1);

function wpv_show_hide_archive_loop($sections) {
	$sections['archive-loop'] = array(
		'name'		=> __('Loops selection', 'wpv-views'),
		);
	return $sections;
}

add_action('view-editor-section-archive-loop', 'add_view_loop_selection', 10, 2);

function add_view_loop_selection($view_settings, $view_id) {
	global $views_edit_help;

	$hide = '';
	if (isset($view_settings['sections-show-hide']) && isset($view_settings['sections-show-hide']['archive-loop']) && 'off' == $view_settings['sections-show-hide']['archive-loop']) {
		$hide = ' hidden';
	}?>
	<div class="wpv-setting-container wpv-settings-archive-loops js-wpv-settings-archive-loop<?php echo $hide; ?>">
		<div class="wpv-settings-header">
			<h3>
				<?php _e('Loops selection', 'wpv-views' ) ?>
				<i class="icon-question-sign js-display-tooltip" data-header="<?php echo $views_edit_help['loops_selection']['title'] ?>" data-content="<?php echo $views_edit_help['loops_selection']['content'] ?>"></i>
			</h3>
		</div>
		<div class="wpv-setting">
			<form class="js-loop-selection-form">
				<?php render_view_loop_selection_form( $view_id ); ?>
			</form>
			<p class="update-button-wrap">
				<button data-success="<?php echo htmlentities( __('Loop selection updated', 'wpv-views'), ENT_QUOTES ); ?>" data-unsaved="<?php echo htmlentities( __('Loop selection not saved', 'wpv-views'), ENT_QUOTES ); ?>" data-nonce="<?php echo wp_create_nonce( 'wpv_view_loop_selection_nonce' ); ?>" class="js-wpv-loop-selection-update button-secondary" disabled="disabled"><?php _e('Update', 'wpv-views'); ?></button>
			</p>
		</div>
	</div>
<?php }

function render_view_loop_selection_form( $view_id = 0 ) {
	global $WPV_view_archive_loop, $WP_Views;
	$options = $WP_Views->get_options();
	$options = $WPV_view_archive_loop->_view_edit_options($view_id, $options); // TODO check if we just need the $options above
	$asterisk = ' <span style="color:red">*</span>';
	$asterisk_explanation = __( '<span style="color:red">*</span> A different WordPress Archive is already assigned to this item', 'wpv-views' );
	$show_asterisk_explanation = false;
	$loops = array('home-blog-page' => __('Home/Blog', 'wpv-views'),
			'search-page' => __('Search results', 'wpv-views'),
			'author-page' => __('Author archives', 'wpv-views'),
			'year-page' => __('Year archives', 'wpv-views'),
			'month-page' => __('Month archives', 'wpv-views'),
			'day-page' => __('Day archives', 'wpv-views')
	);
	?>
	<h3><?php _e('WordPress Native Archives', 'wpv-views'); ?></h3>
	<div class="wpv-setting-options-box">
		<ul class="enable-scrollbar wpv-mightlong-list">
		<?php foreach ( $loops as $loop => $loop_name ): ?>
			<?php
			$show_asterisk = false;
			$checked = ( isset( $options['view_' . $loop] ) && $options['view_' . $loop] == $view_id ) ? ' checked="checked"' : '';
			if ( isset( $options['view_' . $loop] ) && $options['view_' . $loop] != $view_id ) {
				$show_asterisk = true;
				$show_asterisk_explanation = true;
			}
			?>
			<li>
				<input type="checkbox" <?php echo $checked; ?> id="wpv-view-loop-<?php echo $loop; ?>" name="wpv-view-loop-<?php echo $loop; ?>" />
				<label for="wpv-view-loop-<?php echo $loop; ?>"><?php echo $loop_name; echo $show_asterisk ? $asterisk : ''; ?></label>
			</li>
		<?php endforeach; ?>
		</ul>
	<?php if ( $show_asterisk_explanation ) { ?>
		<span class="wpv-options-box-info">
			<?php echo $asterisk_explanation; ?>
		</span>
	<?php } ?>
	</div>
	<?php
	$pt_loops = array();
	$show_asterisk_explanation = false;
	// Only offer loops for post types that already have an archive
	$post_types = get_post_types( array( 'public' => true, 'has_archive' => true), 'objects' );
	foreach ( $post_types as $post_type ) {
		if ( !in_array( $post_type->name, array( 'post', 'page', 'attachment' ) ) ) {
			$type = 'cpt_' . $post_type->name;
			$name = $post_type->labels->name;
			$pt_loops[$type] = $name;
		}
	}
	if ( count( $pt_loops ) > 0 ) {
	?>
	<h3><?php _e('Post Type Archives', 'wpv-views'); ?></h3>
	<div class="wpv-setting-options-box">
		<ul class="enable-scrollbar wpv-mightlong-list">
		<?php foreach ( $pt_loops as $loop => $loop_name ): ?>
			<?php
			$show_asterisk = false;
			$checked = ( isset( $options['view_' . $loop] ) && $options['view_' . $loop] == $view_id ) ? ' checked="checked"' : '';
			if ( isset( $options['view_' . $loop] ) && $options['view_' . $loop] != $view_id ) {
				$show_asterisk = true;
				$show_asterisk_explanation = true;
			}
			?>
			<li>
				<input type="checkbox" <?php echo $checked; ?> id="wpv-view-loop-<?php echo $loop; ?>" name="wpv-view-loop-<?php echo $loop; ?>" />
				<label for="wpv-view-loop-<?php echo $loop; ?>"><?php echo $loop_name; echo $show_asterisk ? $asterisk : ''; ?></label>
			</li>
		<?php endforeach; ?>
		</ul>
	<?php if ( $show_asterisk_explanation ) { ?>
		<span class="wpv-options-box-info">
			<?php echo $asterisk_explanation; ?>
		</span>
	<?php } ?>
	</div>
	<?php } ?>

	<h3><?php _e('Taxonomy Archives', 'wpv-views'); ?></h3>
	<?php
	$show_asterisk_explanation = false;
	?>
	<div class="wpv-setting-options-box">
		<ul class="enable-scrollbar wpv-mightlong-list">
		<?php
		$taxonomies = get_taxonomies( '', 'objects' );
		$exclude_tax_slugs = array();
		$exclude_tax_slugs = apply_filters( 'wpv_admin_exclude_tax_slugs', $exclude_tax_slugs );
		foreach ( $taxonomies as $category_slug => $category ):
			if ( in_array( $category_slug, $exclude_tax_slugs ) ) {
				continue;
			}
			if ( !$category->show_ui ) {
				continue; // Only show taxonomies with show_ui set to TRUE
			}
			$name = $category->name;
			$show_asterisk = false;
			$checked = ( isset( $options['view_taxonomy_loop_' . $name ] ) && $options['view_taxonomy_loop_' . $name ] == $view_id ) ? ' checked="checked"' : '';
			if ( isset( $options['view_taxonomy_loop_' . $name ] ) && $options['view_taxonomy_loop_' . $name ] != $view_id ) {
				$show_asterisk = true;
				$show_asterisk_explanation = true;
			}
		?>
			<li>
				<input type="checkbox" <?php echo $checked; ?> id="wpv-view-taxonomy-loop-<?php echo $name; ?>" name="wpv-view-taxonomy-loop-<?php echo $name; ?>" />
				<label for="wpv-view-taxonomy-loop-<?php echo $name; ?>"><?php echo $category->labels->name; echo $show_asterisk ? $asterisk : ''; ?></label>
			</li>
		<?php endforeach; ?>
		</ul>
	<?php if ( $show_asterisk_explanation ) { ?>
		<span class="wpv-options-box-info">
			<?php echo $asterisk_explanation; ?>
		</span>
	<?php } ?>
	</div>
	<?php
}