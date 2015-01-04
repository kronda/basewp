<?php

/*
* We can enable this to hide the Limit and offset section
*/

add_filter('wpv_sections_query_show_hide', 'wpv_show_hide_limit_offset', 1,1);

function wpv_show_hide_limit_offset($sections) {
	$sections['limit-offset'] = array(
		'name'		=> __('Limit and offset', 'wpv-views'),
		);
	return $sections;
}

add_action('view-editor-section-query', 'add_view_limit_offset', 40, 2);

function add_view_limit_offset($view_settings, $view_id) {
    global $views_edit_help;
	$view_settings = wpv_limit_default_settings($view_settings); // TODO we need this in the default array, not here
	$hide = '';
	if (isset($view_settings['sections-show-hide']) && isset($view_settings['sections-show-hide']['limit-offset']) && 'off' == $view_settings['sections-show-hide']['limit-offset']) {
		$hide = ' hidden';
	}?>
	<div class="wpv-setting-container wpv-settings-limit js-wpv-settings-limit-offset<?php echo $hide; ?>">
		<div class="wpv-settings-header">
			<h3>
				<?php _e( 'Limit and offset', 'wpv-views' ) ?>
				<i class="icon-question-sign js-display-tooltip" data-header="<?php echo $views_edit_help['limit_and_offset']['title']; ?>" data-content="<?php echo $views_edit_help['limit_and_offset']['content']; ?>"></i>
			</h3>
		</div>
		<div class="wpv-setting">

			<div class="wpv-settings-query-type-posts"<?php echo $view_settings['query_type'][0]!='posts' ? ' style="display: none;"' : ''; ?>>

				<p>
					<label for="wpv-settings-limit"><?php _e( 'Display ', 'wpv-views' ) ?></label>
					<select name="_wpv_settings[limit]" id="wpv-settings-limit" class="js-wpv-limit">
						<option value="-1"><?php _e('No limit', 'wpv-views'); ?></option>
						<?php
						for ($index = 1; $index < 51; $index++) {
							echo '<option value="' . $index . '"';
							if ($view_settings['limit'] == $index) {
							echo ' selected="selected"';
							}
							echo '>' . $index . '</option>';
						}
						?>
					</select>
					<span><?php _e( 'items ', 'wpv-views' ) ?></span>
				</p>

				<p>
					<label for="wpv-settings-offset"><?php _e( 'Skip first', 'wpv-views' ) ?></label>
					<select name="_wpv_settings[offset]" id="wpv-settings-offset" class="js-wpv-offset">
						<option value="0"><?php _e('None', 'wpv-views'); ?></option>
						<?php
						for ($index = 1; $index < 51; $index++) {
							echo '<option value="' . $index . '"';
							if ($view_settings['offset'] == $index) {
							echo ' selected="selected"';
							}
							echo '>' . $index . '</option>';
						}
						?>
					</select>
					<span><?php _e( 'items', 'wpv-views' ) ?></span>
				</p>
			</div>

			<div class="wpv-settings-query-type-taxonomy"<?php echo $view_settings['query_type'][0]!='taxonomy' ? ' style="display: none;"' : ''; ?>>

				<p>
					<label for="wpv-settings-taxonomy-limit"><?php _e( 'Display ', 'wpv-views' ) ?></label>
					<select name="_wpv_settings[taxonomy_limit]" id="wpv-settings-taxonomy-limit" class="js-wpv-taxonomy-limit">
						<option value="-1"><?php _e('No limit', 'wpv-views'); ?></option>
						<?php
						for ($index = 1; $index < 51; $index++) {
							echo '<option value="' . $index . '"';
							if ($view_settings['taxonomy_limit'] == $index) {
							echo ' selected="selected"';
							}
							echo '>' . $index . '</option>';
						}
						?>
					</select>
					<span><?php _e( 'items ', 'wpv-views' ) ?></span>
				</p>

				<p>
					<label for="wpv-settings-taxonomy-offset"><?php _e( 'Skip first', 'wpv-views' ) ?></label>
					<select name="_wpv_settings[taxonomy_offset]" id="wpv-settings-taxonomy-offset" class="js-wpv-taxonomy-offset">
						<option value="0"><?php _e('None', 'wpv-views'); ?></option>
						<?php
						for ($index = 1; $index < 51; $index++) {
							echo '<option value="' . $index . '"';
							if ($view_settings['taxonomy_offset'] == $index) {
							echo ' selected="selected"';
							}
							echo '>' . $index . '</option>';
						}
						?>
					</select>
					<span><?php _e( 'items', 'wpv-views' ) ?></span>
				</p>
			</div>
			<div class="wpv-settings-query-type-users"<?php echo $view_settings['query_type'][0]!='users' ? ' style="display: none;"' : ''; ?>>
			     <p>
                    <label for="wpv-settings-users-limit"><?php _e( 'Display ', 'wpv-views' ) ?></label>
                    <select name="_wpv_settings[users_limit]" id="wpv-settings-users-limit" class="js-wpv-users-limit">
                        <option value="-1"><?php _e('No limit', 'wpv-views'); ?></option>
                        <?php
                        for ($index = 1; $index < 51; $index++) {
                            echo '<option value="' . $index . '"';
                            if ($view_settings['users_limit'] == $index) {
                            echo ' selected="selected"';
                            }
                            echo '>' . $index . '</option>';
                        }
                        ?>
                    </select>
                    <span><?php _e( 'items ', 'wpv-views' ) ?></span>
                </p>

                <p>
                    <label for="wpv-settings-users-offset"><?php _e( 'Skip first', 'wpv-views' ) ?></label>
                    <select name="_wpv_settings[users_offset]" id="wpv-settings-users-offset" class="js-wpv-users-offset">
                        <option value="0"><?php _e('None', 'wpv-views'); ?></option>
                        <?php
                        for ($index = 1; $index < 51; $index++) {
                            echo '<option value="' . $index . '"';
                            if ($view_settings['users_offset'] == $index) {
                            echo ' selected="selected"';
                            }
                            echo '>' . $index . '</option>';
                        }
                        ?>
                    </select>
                    <span><?php _e( 'items', 'wpv-views' ) ?></span>
                </p>
			</div>
			<p class="update-button-wrap">
				<button data-success="<?php echo htmlentities( __('Limit and offset options updated', 'wpv-views'), ENT_QUOTES ); ?>" data-unsaved="<?php echo htmlentities( __('Limit and offset options not saved', 'wpv-views'), ENT_QUOTES ); ?>" data-nonce="<?php echo wp_create_nonce( 'wpv_view_limit_offset_nonce' ); ?>" class="js-wpv-limit-offset-update button-secondary" disabled="disabled"><?php _e('Update', 'wpv-views'); ?></button>
			</p>
		</div>
	</div>
<?php }

/**
* wpv_limit_offset_summary_filter
*
* Returns the limit and offset part when building the summary for a View
*
* @param $summary
* @param $post_id
* @param $view_settings
*
* @returns (string) $summary
*
* @since 1.6.0
*/

add_filter('wpv-view-get-content-summary', 'wpv_limit_offset_summary_filter', 5, 3);

function wpv_limit_offset_summary_filter($summary, $post_id, $view_settings) {
	$summary .= wpv_get_limit_offset_summary( $view_settings );
    return $summary;
}

/**
* wpv_get_limit_offset_summary
*
* Returns the limit and offset summary for a View
*
* @param $view_settings
*
* @returns (string) $summary
*
* @since 1.6.0
*/

function wpv_get_limit_offset_summary( $view_settings ) {
	
	$view_settings = wpv_limit_default_settings( $view_settings );
	$output = '';
	if ( !isset( $view_settings['query_type'] ) || ( isset($view_settings['query_type'] ) && $view_settings['query_type'][0] == 'posts' ) ) {
		if ( intval( $view_settings['limit'] ) != -1 ) {
			if ( intval( $view_settings['limit'] ) == 1 ) {
				$output .= __( ', limit to 1 item', 'wpv-views' );
			} else {
				$output .= sprintf( __( ', limit to %d items', 'wpv-views' ), intval( $view_settings['limit'] ) );
			}
		}
		if ( intval( $view_settings['offset'] ) != 0 ) {
			if ( intval( $view_settings['limit'] ) == 1 ) {
				$output .= __( ', skip first item', 'wpv-views' );
			} else {
				$output .= sprintf( __( ', skip %d items', 'wpv-views' ), intval( $view_settings['offset'] ) );
			}
		}
	}
	if ( isset( $view_settings['query_type'] ) && $view_settings['query_type'][0] == 'taxonomy' ) {
		if ( intval( $view_settings['taxonomy_limit'] ) != -1 ) {
			if ( intval( $view_settings['taxonomy_limit'] ) == 1 ) {
				$output .= __( ', limit to 1 item', 'wpv-views' );
			} else {
				$output .= sprintf( __( ', limit to %d items', 'wpv-views' ), intval( $view_settings['taxonomy_limit'] ) );
			}
		}
		if ( intval( $view_settings['taxonomy_offset'] ) != 0 ) {
			if ( intval($view_settings['taxonomy_limit'] ) == 1 ) {
				$output .= __( ', skip first item', 'wpv-views' );
			} else {
				$output .= sprintf( __( ', skip %d items', 'wpv-views' ), intval( $view_settings['taxonomy_offset'] ) );
			}
		}
	}
	if ( isset( $view_settings['query_type'] ) && $view_settings['query_type'][0] == 'users' ) {
		if ( intval( $view_settings['users_limit'] ) != -1 ) {
			if ( intval( $view_settings['users_limit'] ) == 1 ) {
				$output .= __( ', limit to 1 item', 'wpv-views' );
			} else {
				$output .= sprintf( __( ', limit to %d items', 'wpv-views' ), intval( $view_settings['users_limit'] ) );
			}
		}
		if ( intval( $view_settings['users_offset'] ) != 0 ) {
			if ( intval($view_settings['users_limit'] ) == 1 ) {
				$output .= __( ', skip first item', 'wpv-views' );
			} else {
				$output .= sprintf( __( ', skip %d items', 'wpv-views' ), intval( $view_settings['users_offset'] ) );
			}
		}
	}
	return $output;
}