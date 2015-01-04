<?php
/*
* Added extra files to have the old and new editors working together
* Once we are done, those extra files will be merged with the old ones after cleaning no longer needed functions
*/

/* General TODOs
* TODO: Create extra files to make this screen modular. STATUS: 80%
* TODO: Implement the new options added for Views 1.2.2
*/

// Loop selection files
require_once WPV_PATH . '/inc/redesign/wpv-section-loop-selection.php';
// Layout section files
require_once WPV_PATH . '/inc/redesign/wpv-section-layout-template.php';
require_once WPV_PATH . '/inc/redesign/wpv-section-layout-extra.php';
require_once WPV_PATH . '/inc/redesign/wpv-section-layout-extra-js.php';
// Extra section files
require_once WPV_PATH . '/inc/redesign/wpv-section-content.php';
// Edit view page sections descriptions
require_once WPV_PATH . '/inc/redesign/wpv-section-descriptons.php';
// editor addon
require_once WPV_PATH_EMBEDDED . '/common/visual-editor/editor-addon.class.php';

/**
* WordPress Archives edit screen
*/

function views_archive_redesign_html() {
	global $WP_Views, $post, $views_edit_help;
	if ( isset( $_GET['view_id'] ) && is_numeric( $_GET['view_id'] ) ) {
		do_action('views_edit_screen');
		$view_id = (int)$_GET['view_id'];
		$view = get_post( $view_id );
		if ( null == $view ) {
			wp_die( '<div class="wpv-setting-container"><p class="toolset-alert toolset-alert-error">' . __('You attempted to edit a WordPress Archive that doesn&#8217;t exist. Perhaps it was deleted?', 'wpv-views') . '</p></div>' );
		} elseif ( 'view'!= $view->post_type ) {
			wp_die( '<div class="wpv-setting-container"><p class="toolset-alert toolset-alert-error">' . __('You attempted to edit a WordPress Archive that doesn&#8217;t exist. Perhaps it was deleted?', 'wpv-views') . '</p></div>' );
		} else {
			$view_settings = get_post_meta($_GET['view_id'], '_wpv_settings', true);
			$view_layout_settings = get_post_meta($_GET['view_id'], '_wpv_layout_settings', true);
			if (!is_array($view_layout_settings)) $view_layout_settings = array();
			if (isset($view_settings['view-query-mode']) && (
						('archive' ==  $view_settings['view-query-mode']) ||
						('layouts-loop' ==  $view_settings['view-query-mode']) // We'll use archive mode editor for the layouts-loop View
						)
			   ) {
				$post = $view;
				if ( get_post_status( $view_id ) == 'trash' ) {
					wp_die( '<div class="wpv-setting-container"><p class="toolset-alert toolset-alert-error">' . __('You can’t edit this WordPress Archive because it is in the Trash. Please restore it and try again.', 'wpv-views') . '</p></div>' );
				}
			} else {
				wp_die( '<div class="wpv-setting-container"><p class="toolset-alert toolset-alert-error">' . __('You attempted to edit a WordPress Archive that doesn&#8217;t exist. Perhaps it was deleted?', 'wpv-views') . '</p></div>' );
			}
		}
	} else {
		wp_die( '<div class="wpv-setting-container"><p class="toolset-alert toolset-alert-error">' . __('You attempted to edit a WordPress Archive that doesn&#8217;t exist. Perhaps it was deleted?', 'wpv-views') . '</p></div>' );
	}
	?>
	<div id="screen-meta-dup" class="metabox-prefs js-screen-meta-dup hidden">
		<div id="screen-options-wrap" aria-label="<?php echo htmlentities( __('Screen Options Tab'), ENT_QUOTES ); ?>" class="wpv-screen-options js-wpv-show-hide-container" data-pagneedsfilter="<?php echo htmlentities( __('Pagination requires the Filter HTML section to be visible.', 'wpv-views'), ENT_QUOTES ); ?>" data-unclickable="<?php echo htmlentities( __('This section has unsaved changes, so you can not hide it', 'wpv-views'), ENT_QUOTES ); ?>">
			<h5><?php _e('Show on screen', 'wpv-views');?></h5>
			<?php
				$sections = array();
				$sections = apply_filters('wpv_sections_archive_loop_show_hide', $sections);
				if (!empty($sections)) {
			?>
			<div class="wpv-show-hide-section wpv-show-hide-section-query js-wpv-show-hide-section" data-metasection="wpv-query-section">
				<h6><?php _e('Loop section', 'wpv-views'); ?></h6>
				<span class="js-wpv-screen-pref">
				<?php if ( isset( $view_settings['metasections-hep-show-hide'] ) && isset( $view_settings['metasections-hep-show-hide']['wpv-query-help'] ) ) {
					$state = $view_settings['metasections-hep-show-hide']['wpv-query-help'];
				} else {
					$state = 'on';
				} ?>
				<label for="wpv-show-hide-query-help"><input type="checkbox" id="wpv-show-hide-query-help" data-metasection="query" class="js-wpv-show-hide-help js-wpv-show-hide-query-help"<?php if ('on' == $state) echo ' checked="checked"'; ?> /><?php echo __('Display Query section help', 'wpv-views'); ?></label>
				<input name="wpv-query-help" type="hidden" class="js-wpv-show-hide-help-value js-wpv-show-hide-query-help-value" value="<?php echo $state; ?>" />
				</span>
				<?php
					foreach ($sections as $key => $values) {
						if (isset($view_settings['sections-show-hide']) && isset($view_settings['sections-show-hide'][$key]) ) {
							$values['state'] = $view_settings['sections-show-hide'][$key];
						} else {
							$values['state'] = 'on';
						} ?>
						<span class="js-wpv-screen-pref">
						<label for="wpv-show-hide-<?php echo $key; ?>"><input data-section="<?php echo $key; ?>" type="checkbox" id="wpv-show-hide-<?php echo $key; ?>" class="js-wpv-show-hide js-wpv-show-hide-<?php echo $key; ?>"<?php if ('on' == $values['state']) echo ' checked="checked"'; ?> /><?php echo $values['name']; ?></label>
						<input data-section="<?php echo $key; ?>" name="<?php echo $key; ?>" class="js-wpv-show-hide-value" type="hidden" value="<?php echo $values['state']; ?>" />
						</span>
					<?php }
				?>
			</div>
			<?php } ?>
			<?php
				$sections = array();
				$sections = apply_filters('wpv_sections_layout_show_hide', $sections);
				$js = isset($view_layout_settings['additional_js']) ? strval($view_layout_settings['additional_js']) : '';
				if ('' == $js && isset($sections['layout-settings-extra-js'])) {
					unset($sections['layout-settings-extra-js']);
				}
				if (!empty($sections)) {
			?>
			<div class="wpv-show-hide-section wpv-show-hide-section-layout js-wpv-show-hide-section" data-metasection="wpv-layout-section">
				<h6><?php _e('Layout section', 'wpv-views'); ?></h6>
				<span class="js-wpv-screen-pref">
				<?php if ( isset( $view_settings['metasections-hep-show-hide'] ) && isset( $view_settings['metasections-hep-show-hide']['wpv-layout-help'] ) ) {
					$state = $view_settings['metasections-hep-show-hide']['wpv-layout-help'];
				} else {
					$state = 'on';
				} ?>
				<label for="wpv-show-hide-layout-help"><input type="checkbox" id="wpv-show-hide-layout-help" data-metasection="layout" class="js-wpv-show-hide-help js-wpv-show-hide-layout-help"<?php if ('on' == $state) echo ' checked="checked"'; ?> /><?php echo __('Display Layout section help', 'wpv-views'); ?></label>
				<input name="wpv-layout-help" type="hidden" class="js-wpv-show-hide-help-value js-wpv-show-hide-layout-help-value" value="<?php echo $state; ?>" />
				</span>
				<?php
					foreach ($sections as $key => $values) {
						if (isset($view_settings['sections-show-hide']) && isset($view_settings['sections-show-hide'][$key]) ) {
							$values['state'] = $view_settings['sections-show-hide'][$key];
						} else {
							$values['state'] = 'on';
						}
						?>
						<span class="js-wpv-screen-pref">
						<label for="wpv-show-hide-<?php echo $key; ?>"><input data-section="<?php echo $key; ?>" type="checkbox" id="wpv-show-hide-<?php echo $key; ?>" class="js-wpv-show-hide js-wpv-show-hide-<?php echo $key; ?>"<?php if ('on' == $values['state']) echo ' checked="checked"'; ?> /><?php echo $values['name']; ?></label>
						<input data-section="<?php echo $key; ?>" name="<?php echo $key; ?>" class="js-wpv-show-hide-value" type="hidden" value="<?php echo $values['state']; ?>" />
						</span>
					<?php }
				?>
			</div>
			<?php } ?>
			<p>
				<button data-success="<?php echo htmlentities( __('Saved', 'wpv-views'), ENT_QUOTES ); ?>" data-unsaved="<?php echo htmlentities( __('Not saved', 'wpv-views'), ENT_QUOTES ); ?>" data-nonce="<?php echo wp_create_nonce( 'wpv_view_show_hide_nonce' ); ?>" class="js-wpv-show-hide-update button-secondary"><?php echo __('Save', 'wpv-views'); ?></button>
			</p>
		</div>
	</div> <!-- #screen-meta-dup -->
	<div id="screen-meta-links-dup" class="js-screen-meta-links-dup">
		<div id="screen-options-link-wrap" class="hide-if-no-js screen-meta-toggle">
			<a id="show-settings-link" class="show-settings" aria-expanded="false" aria-controls="screen-options-wrap" href="#screen-options-wrap">Screen Options</a>
		</div>
	</div>
	<?php
	/**
	* Actual WordPress Archive edit page
	*
	* NOTE
	* $views_edit_help is localized and escaped in wpv-section-descriptions.php
	*/
	?>
	<div class="wrap toolset-views">
		<input id="post_ID" class="js-post_ID" type="hidden" value="<?php echo $view_id; ?>" />
		<div id="icon-edit" class="icon32 icon32-posts-post"><br></div>
		<h2>
			<?php
			if ('archive' ==  $view_settings['view-query-mode']) {
				echo __('Edit WordPress Archive','wpv-views');
			} else {
				echo __('Edit Layouts Loop View','wpv-views');
			}
			?>
		</h2>
		<div class="wpv-settings-save-all wpv-setting-container">
			<div class="wpv-setting">
				<p class="update-button-wrap">
					<button class="button-secondary button button-large js-wpv-view-save-all" disabled="disabled" data-success="<?php echo htmlentities( __('View saved', 'wpv-views'), ENT_QUOTES ); ?>" data-unsaved="<?php echo htmlentities( __('View not saved', 'wpv-views'), ENT_QUOTES ); ?>"><?php _e('Save all sections at once', 'wpv-views'); ?></button>
				</p>
			</div>
		</div> <!-- .wpv-settings-save-all -->
		<input type="hidden" name="_wpv_settings[view-query-mode]" value="normal" />
		<div class="wpv-title-section">
			<div class="wpv-setting-container wpv-settings-title-and-desc">
				<div class="wpv-settings-header">
					<h3>
						<?php _e( 'Title and description', 'wpv-views' ) ?>
						<i class="icon-question-sign js-display-tooltip" data-header="<?php echo $views_edit_help['title_and_description']['title'] ?>" data-content="<?php echo $views_edit_help['title_and_description']['content'] ?>"></i>
					</h3>
				</div>
				<div class="wpv-setting">

					<div id="titlediv">
						<div id="titlewrap">
							<label class="screen-reader-text js-title-reader" id="title-prompt-text" for="title"><?php _e('Enter title here','wp-views'); ?></label>
							<input id="title" class="js-title" type="text" name="post_title" size="30" value="<?php echo get_the_title($view_id); ?>" id="title" autocomplete="off">
						</div>
					</div>

					<div id="edit-slug-box" class="js-wpv-slug-container">
						<label for="wpv-slug"><?php _e('Slug of this WordPress Archive', 'wpv-views'); ?>
						<input id="wpv-slug" class="js-wpv-slug" type="text" value="<?php echo esc_attr( $view->post_name ); ?>" />
						 &bull; <button class="button-secondary js-wpv-change-view-status icon-trash" data-statusto="trash" data-success="<?php echo htmlentities( __('View moved to trash', 'wpv-views'), ENT_QUOTES ); ?>" data-unsaved="<?php echo htmlentities( __('View not moved to trash', 'wpv-views'), ENT_QUOTES ); ?>" data-redirect="<?php echo admin_url( 'admin.php?page=view-archives'); ?>" data-nonce="<?php echo wp_create_nonce( 'wpv_view_change_status' ); ?>"> <?php _e('Move to trash', 'wpv-views'); ?></button>
					</div>

					<?php $view_description = get_post_meta($_GET['view_id'], '_wpv_description', true); ?>

					<p class="<?php echo ( isset( $view_description ) && !empty( $view_description ) ) ? 'hidden' : ''; ?>">
						<button class="js-wpv-description-toggle button-secondary" ><?php _e('Add description', 'wpv-views'); ?></button>
					</p>

					<div class="js-wpv-description-container wpv-description-container<?php echo ( isset( $view_description ) && !empty( $view_description ) ) ? '' : ' hidden'; ?>">
						<p>
							<label for="wpv-description"><?php _e('Describe this View', 'wpv-views' ) ?></label>
						</p>
						<p>
							<textarea id="wpv-description" class="js-wpv-description" name="_wpv_settings[view_description]" cols="72" rows="4"><?php echo ( isset( $view_description ) ) ? esc_html($view_description) : ''; ?></textarea>
						</p>
					</div>

					<p class="update-button-wrap">
						<button data-success="<?php echo htmlentities( __('Title and description updated', 'wpv-views'), ENT_QUOTES ); ?>" data-unsaved="<?php echo htmlentities( __('Title and description not saved', 'wpv-views'), ENT_QUOTES ); ?>" data-nonce="<?php echo wp_create_nonce( 'wpv_view_title_description_nonce' ); ?>" class="js-wpv-title-description-update button-secondary" disabled="disabled"><?php _e('Update', 'wpv-views'); ?></button>
					</p>

				</div> <!-- .wpv-setting -->
			</div> <!-- .wpv-setting-container -->
		</div> <!-- .wpv-title-section -->

		<?php if ('archive' ==  $view_settings['view-query-mode']): ?>
			<div class="wpv-query-section">
				<h3 class="wpv-section-title"><?php _e('The loop section determines which listing page to customize','wpv-views') ?></h3>
				<?php do_action('view-editor-section-archive-loop', $view_settings, $view_id); ?>
			</div> <!-- .wpv-query-section -->
		<?php endif; ?>

		<?php
		/*
		* Loop selection - Priority 10
		*/
		?>

		<div class="wpv-layout-section">
			<h3 class="wpv-section-title"><?php _e('The layout section styles the WordPress Archive output on the page.','wpv-views') ?></h3>
			<?php
			$data = wpv_get_view_layout_introduction_data();
			wpv_toolset_help_box($data);
			?>
					<?php do_action('view-editor-section-layout', $view_settings, $view_layout_settings, $view_id); ?>
			<?php do_action('view-editor-section-extra', $view_settings, $view_id); ?>

			<div class="wpv-settings-save-all wpv-setting-container">
				<div class="wpv-setting">
					<p class="update-button-wrap">
						<button class="button-secondary button button-large js-wpv-view-save-all" disabled="disabled" data-success="<?php echo htmlentities( __('View saved', 'wpv-views'), ENT_QUOTES ); ?>" data-unsaved="<?php echo htmlentities( __('View not saved', 'wpv-views'), ENT_QUOTES ); ?>"><?php _e('Save all sections at once', 'wpv-views'); ?></button>
					</p>
				</div>
			</div>
		</div> <!-- .wpv-layout-section -->

		<?php
		/*
		* Output (layout) type - TODO review this https://icanlocalize.basecamphq.com/projects/7393061-toolset/todo_items/162512599/comments - Priority 10 - To remove
		* Output fields TODO this has been reviewed and may be used as training - Priority 20 - To remove
		* Layout templates TODO insert here the new Content Templates editor. https://icanlocalize.basecamphq.com/projects/7393061-toolset/todo_items/161787695/comments - Priority 20 - To review
		* Layout Meta HTML/CSS/JS TODO this has been reviewed and needs some changes. https://icanlocalize.basecamphq.com/projects/7393061-toolset/todo_items/161787640/comments - Priority 40
		* Aditional Javascript files TODO move to its own file - Priority 50
		* Extra sections:
		* 1. Complete output (the_content)
		* 2. Module manager TODO needs to be added
		*/
		?>
	</div> <!-- .toolset-views -->
	<script type="text/javascript">
		jQuery(function($){
			jQuery('li.current a').attr('href',jQuery('li.current a').attr('href')+'&view_id=<?php echo $view_id?>');
		});
	</script>
<?php }