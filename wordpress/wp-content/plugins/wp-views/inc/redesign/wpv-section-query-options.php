<?php

/*
* We can enable this to hide the Query options section
*/

add_filter('wpv_sections_query_show_hide', 'wpv_show_hide_query_options', 1,1);

function wpv_show_hide_query_options($sections) {
	$sections['query-options'] = array(
		'name'		=> __('Query options', 'wpv-views'),
		);
	return $sections;
}

add_action('view-editor-section-query', 'add_view_query_options', 20, 2);

function add_view_query_options($view_settings, $view_id) {
    global $views_edit_help;
	$hide = '';
	if (isset($view_settings['sections-show-hide']) && isset($view_settings['sections-show-hide']['query-options']) && 'off' == $view_settings['sections-show-hide']['query-options']) {
		$hide = ' hidden';
	}?>
	<div class="wpv-setting-container wpv-settings-query-options js-wpv-settings-query-options<?php echo $hide; ?>">
		<div class="wpv-settings-header">
			<h3>
				<?php _e( 'Query options', 'wpv-views' ) ?>
				<i class="icon-question-sign js-display-tooltip" data-header="<?php echo $views_edit_help['query_options']['title']; ?>" data-content="<?php echo $views_edit_help['query_options']['content']; ?>"></i>
			</h3>
		</div>
		<div class="wpv-setting">
			<ul class="wpv-query-options wpv-settings-query-type-posts"<?php echo $view_settings['query_type'][0]!='posts' ? ' style="display: none;"' : ''; ?>>
				<li>
					<?php $checked = ( isset( $view_settings['post_type_dont_include_current_page'] ) && $view_settings['post_type_dont_include_current_page'] ) ? ' checked="checked"' : '';?>
					<input type="checkbox" id="wpv-settings-post-include-current" class="js-wpv-query-options-post-type-dont" name="_wpv_settings[post_type_dont_include_current_page]" value="1"<?php echo $checked; ?> />
					<label for="wpv-settings-post-include-current"><?php _e("Don't include current page in query result", 'wpv-views'); ?></label>
				</li>
			</ul>
			<ul class="wpv-query-options wpv-settings-query-type-taxonomy"<?php echo $view_settings['query_type'][0]!='taxonomy' ? ' style="display: none;"' : ''; ?>>
				<li>
					<?php $checked = ( isset( $view_settings['taxonomy_hide_empty'] ) && $view_settings['taxonomy_hide_empty'] ) ? ' checked="checked"' : '';?>
					<input type="checkbox" id="wpv-settings-taxonomy-hide-empty" class="js-wpv-query-options-taxonomy-hide-empty" name="_wpv_settings[taxonomy_hide_empty]" value="1"<?php echo $checked; ?> />
					<label for="wpv-settings-taxonomy-hide-empty"><?php _e( "Don't show empty terms", 'wpv-views' ) ?></label>
				</li>
				<li>
					<?php $checked = ( isset( $view_settings['taxonomy_include_non_empty_decendants'] ) && $view_settings['taxonomy_include_non_empty_decendants'] ) ? ' checked="checked"' : '';?>
					<input type="checkbox" id="wpv-settings-taxonomy-non-empty-decendants" class="js-wpv-query-options-taxonomy-non-empty-decendants" name="_wpv_settings[taxonomy_include_non_empty_decendants]" value="1"<?php echo $checked; ?> />
					<label for="wpv-settings-taxonomy-non-empty-decendants"><?php _e( 'Include terms that have non-empty descendants', 'wpv-views' ) ?></label>
				</li>
				<li>
					<?php $checked = ( isset( $view_settings['taxonomy_pad_counts'] ) && $view_settings['taxonomy_pad_counts'] ) ? ' checked="checked"' : '';?>
					<input id="wpv-settings-taxonomy-pad-counts" type="checkbox" class="js-wpv-query-options-taxonomy-pad-counts" name="_wpv_settings[taxonomy_pad_counts]" value="1"<?php echo $checked; ?> />
					<label for="wpv-settings-taxonomy-pad-counts"><?php _e( 'Include children in the post count', 'wpv-views' ) ?></label>
				</li>
			</ul>
			<ul class="wpv-query-options wpv-settings-query-type-users"<?php echo $view_settings['query_type'][0]!='users' ? ' style="display: none;"' : ''; ?>>
				<li>
					<?php $checked = ( isset( $view_settings['users-show-current'] ) && $view_settings['users-show-current'] ) ? ' checked="checked"' : '';?>
					<input type="checkbox" id="wpv-settings-users-show-current" class="js-wpv-query-options-users-show-current" 
					name="_wpv_settings[users-show-current]" value="1"<?php echo $checked; ?> />
					<label for="wpv-settings-users-show-current"><?php _e( "Don't show current logged user.", 'wpv-views' ) ?></label>
				</li>
				<?php
				/*
				 * NOTE: in future if users will ask query users from entire network, we can uncomment it.
				 */
				 /*if ( is_multisite() ): ?> 
				<li>
					<?php $checked = ( !isset( $view_settings['users-show-multisite'] ) || 
						( isset( $view_settings['users-show-multisite'] ) && $view_settings['users-show-multisite'] == 'all' ) ) ? ' checked="checked"' : '';?>
					<input type="radio" id="wpv-settings-users-show-multisite" class="js-wpv-query-options-users-show-multisite" 
					name="_wpv_settings[users-show-multisite]" value="all"<?php echo $checked; ?> />
					<label for="wpv-settings-users-show-multisite"><?php _e( "Load all users from the multisite network.", 'wpv-views' ) ?></label>
				</li>
				<li>
					<?php $checked = ( isset( $view_settings['users-show-multisite'] ) && $view_settings['users-show-multisite'] == 'current' ) ? ' checked="checked"' : '';?>
					<input type="radio" id="wpv-settings-users-show-multisite2" class="js-wpv-query-options-users-show-multisite" 
					name="_wpv_settings[users-show-multisite]" value="current"<?php echo $checked; ?> />
					<label for="wpv-settings-users-show-multisite2"><?php _e( "Load only users from the child site in the network.", 'wpv-views' ) ?></label>
				</li>
				
				<?php endif; */?>
			</ul>
			<p class="update-button-wrap">
				<button data-success="<?php echo htmlentities( __('Query options updated', 'wpv-views'), ENT_QUOTES ); ?>" data-unsaved="<?php echo htmlentities( __('Query options not saved', 'wpv-views'), ENT_QUOTES ); ?>" data-nonce="<?php echo wp_create_nonce( 'wpv_view_query_options_nonce' ); ?>" class="js-wpv-query-options-update button-secondary" disabled="disabled"><?php _e('Update', 'wpv-views'); ?></button>
			</p>
		</div>
	</div>
<?php }