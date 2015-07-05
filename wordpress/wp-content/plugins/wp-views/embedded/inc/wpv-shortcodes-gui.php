<?php

/**
* wpv-shortcodes-gui.php
*
* All callback actions to display popups to set options for our Views shortcodes go here
*
* @package Views
* @since unknown
*/

if ( is_admin() ) {
	
	
	// TODO this is needed because we made some changes in the common code
	// Until we release an update for all plugins including it, we need to have a function to display the Translatable string shortcode popup
	add_action('init', 'wpv_shortcodes_gui_init');
}

function wpv_shortcodes_gui_init() {
	add_action('admin_head', 'wpv_shortcodes_gui_js_init');
}

function wpv_shortcodes_gui_js_init() {
?>
	<script type="text/javascript">
		//<![CDATA[
		<?php if ( function_exists( 'wpml_string_shortcode' ) ) { ?>
		function wpv_insert_translatable_string_popup() {
			jQuery.colorbox({
				href: '<?php echo admin_url('admin-ajax.php'); ?>' + '?_wpnonce=' + '<?php echo wp_create_nonce('wpv_editor_callback'); ?>' + '&action=wpv_translatable_string_popup',
				inline : false,
				onComplete: function() {
					jQuery('.js-wpv-insert-translatable-string-shortcode').addClass('button-secondary').removeClass('button-primary').attr('disabled', true);
					jQuery(document).on('change keyup input cut paste', '.js-wpv-translatable-string-value, .js-wpv-translatable-string-context', function(){
						if ( jQuery('.js-wpv-translatable-string-value').val() != '' && jQuery('.js-wpv-translatable-string-context').val() != '' ) {
							jQuery('.js-wpv-insert-translatable-string-shortcode').addClass('button-primary').removeClass('button-secondary').attr('disabled', false);
						} else {
							jQuery('.js-wpv-insert-translatable-string-shortcode').addClass('button-secondary').removeClass('button-primary').attr('disabled', true);
						}
					});
				}
			});
		}
	<?php } ?>
		//]]>
	</script>
<?php
}

/**
* ----------------------------------------------------------------------
## Parametric search ##
* ----------------------------------------------------------------------
*/

/**
* wpv_ajax_wpv_view_form_popup
*
* Popup for inserting a View form, loaded from a ColorBox AJAX call
*
* @param $_GET['_wpnonce']
* @param $_GET['view_id']
*
* @since 1.4
* @uses ColorBox
*/


function wpv_ajax_wpv_view_form_popup() {
    
	global $wpdb, $WP_Views;
    
	if ( wp_verify_nonce( $_GET['_wpnonce'], 'wpv_editor_callback' ) ) {

        $view_id = $_GET['view_id'];
		$orig_id = $_GET['orig_id'];
        $title = $wpdb->get_var( 
			$wpdb->prepare( 
				"SELECT post_title FROM {$wpdb->posts} 
				WHERE ID = %d 
				LIMIT 1", 
				$view_id 
			) 
		);
		$has_submit = false;
		$view_settings = $WP_Views->get_view_settings( $view_id );
		if ( isset( $view_settings['filter_meta_html'] ) ) {
			$filter_meta_html = $view_settings['filter_meta_html'] ;
		} else {
			$filter_meta_html = '';
		}
		if ( strpos( $filter_meta_html, '[wpv-filter-submit' ) !== false ) {
			$has_submit = true;
		}
        ?>
		<div class="wpv-dialog js-wpv-insert-view-form-dialog">
			<div class="wpv-dialog-header">
				<h2><?php echo sprintf(__('Insert View %s', 'wpv-views'), '<strong>' . $title . '</strong>'); ?></h2>
			</div>
			<div class="wpv-dialog-content">
				<p class="toolset-alert toolset-alert-info"><i class="icon-filter toolset-rounded-icon"></i><?php _e( 'This View contains a parametric search', 'wpv-views' ); ?></p>
				<div class="js-wpv-insert-view-form-display-container">
					<p><strong><?php _e( 'What do you want to include here?', 'wpv-views' ); ?></strong></p>
					<ul>
						<li>
							<input id="wpv-filter-form-display-both" value="both" type="radio" name="wpv-insert-view-form-display" class="js-wpv-insert-view-form-display" checked="checked" />
							<label for="wpv-filter-form-display-both"><?php _e('Both the search box and results', 'wpv-views'); ?></label>
							<span class="helper-text"><?php _e( 'This will display the full View.', 'wpv-views' ); ?></span>
						</li>
						<li>
							<input id="wpv-filter-form-display-form" value="form" type="radio" name="wpv-insert-view-form-display" class="js-wpv-insert-view-form-display" />
							<label for="wpv-filter-form-display-form"><?php _e('Only the search box', 'wpv-views'); ?></label>
							<span class="helper-text"><?php _e( 'This will display just the form, you can select where to display the results in the next step.', 'wpv-views' ); ?></span>
						</li>
						<li>
							<input id="wpv-filter-form-display-results" value="results" type="radio" name="wpv-insert-view-form-display" class="js-wpv-insert-view-form-display" />
							<label for="wpv-filter-form-display-results"><?php _e('Only the search results', 'wpv-views'); ?></label>
							<span class="helper-text"><?php _e( 'This will display just the results, you need to add the form elsewhere targeting this page.', 'wpv-views' ); ?></span>
						</li>
					</ul>
				</div>
				<div class="js-wpv-insert-view-form-target-container" style="display:none">
					<p><strong><?php _e( 'Where do you want to display the results of this search?', 'wpv-views' ); ?></strong></p>
					<?php if ( ! $has_submit ) { ?>
					<span class="toolset-alert toolset-error">
						<?php _e( 'The form in this View does not have a submit button, so you can only display the results on this same page.', 'wpv-views' ); ?>
					</span>
					<?php } ?>
					<ul>
						<li>
							<input id="wpv-filter-form-target-self" value="self" type="radio" name="wpv-insert-view-form-target" class="js-wpv-insert-view-form-target" checked="checked" />
							<label for="wpv-filter-form-target-self"><?php _e('In other place on this same page', 'wpv-views'); ?></label>
						</li>
						<li>
							<input id="wpv-filter-form-target-other" <?php disabled( $has_submit, false ); ?> value="other" type="radio" name="wpv-insert-view-form-target" class="js-wpv-insert-view-form-target" />
							<label for="wpv-filter-form-target-other" <?php if ( ! $has_submit ) { ?>style="color:#999"<?php } ?>><?php _e('On another page', 'wpv-views'); ?></label>
						</li>
					</ul>
					<div class="js-wpv-insert-view-form-target-set-container" style="display:none;margin-left:20px;">
						<p><?php _e( 'You can display the results on an existing page or create a new one:', 'wpv-views' ); ?></p>
						<ul>
							<li>
								<input id="wpv-insert-view-form-target-set-existing" value="existing" type="radio" name="wpv-insert-view-form-target-set" class="js-wpv-insert-view-form-target-set" checked="checked" />
								<label for="wpv-insert-view-form-target-set-existing"><?php _e( 'Use an existing page', 'wpv-views' ); ?></label>
								<div class="js-wpv-insert-view-form-target-set-existing-extra" style="margin:5px 0 0 20px;">
									<input class="js-wpv-insert-view-form-target-set-existing-title" type="text" name="wpv-insert-view-form-target-set-existing-title" placeholder="<?php echo esc_attr( __( 'Type the title of the page', 'wpv-views' ) ); ?>" value="" />
									<input class="js-wpv-insert-view-form-target-set-existing-id" type="hidden" name="wpv-insert-view-form-target-set-existing-id" value="" />
									<div class="js-wpv-insert-view-form-target-set-actions" style="display:none;background:#ddd;margin-top: 5px;padding: 5px 10px 10px;">
										<?php _e( 'Be sure to complete the setup:', 'wpv-views' ); ?><br />
										<a href="#" target="_blank" class="button-primary js-wpv-insert-view-form-target-set-existing-link" data-origid="<?php echo $orig_id; ?>" data-viewid="<?php echo $view_id; ?>" data-editurl="<?php echo admin_url( 'post.php' ); ?>?post="><?php _e( 'Add the search results to this page', 'wpv-views' ); ?></a>
										<a href="#" class="button-secondary js-wpv-insert-view-form-target-set-discard"><?php _e( 'Not now', 'wpv-views' ); ?></a>
									</div>
								</div>
							</li>
							<li>
								<input id="wpv-insert-view-form-target-set-create" value="create" type="radio" name="wpv-insert-view-form-target-set" class="js-wpv-insert-view-form-target-set" />
								<label for="wpv-insert-view-form-target-set-create"><?php _e( 'Use one new page', 'wpv-views' ); ?></label>
								<div class="js-wpv-insert-view-form-target-set-create-extra" style="display:none;margin:5px 0 0 20px;">
									<input class="js-wpv-insert-view-form-target-set-create-title" type="text" name="wpv-insert-view-form-target-set-extra-title" placeholder="<?php echo esc_attr( __( 'Type a title of the new page', 'wpv-views' ) ); ?>" value="" />
									<button class="button-secondary js-wpv-insert-view-form-target-set-create-action" disabled="disabled" data-viewid="<?php echo $view_id; ?>" data-nonce="<?php echo wp_create_nonce('wpv_create_form_target_page_nonce'); ?>"><?php _e( 'Create page', 'wpv-views' ); ?></button>
								</div>
							</li>
						</ul>
					</div>
				</div>
			</div>
			
			<div class="wpv-dialog-footer">
					<button class="button-secondary js-dialog-close"><?php _e('Cancel','wpv-views') ?></button>
					<button class="button-secondary js-wpv-insert-view-form-prev" style="display:none"><?php _e('Previous', 'wpv-views') ?></button>
					<button class="button-primary js-wpv-insert-view-form-action" onclick="wpv_insert_view_form_action(this)" data-forthlabel="<?php echo esc_attr( __( 'Next', 'wpv-views' ) ); ?>" data-insertlabel="<?php echo esc_attr( __( 'Insert shortcode', 'wpv-views' ) ); ?>"><?php echo __('Insert shortcode', 'wpv-views'); ?></button>
				</div>
			
			<script type="text/javascript">
				//<![CDATA[
				function wpv_insert_view_form_action( element ) {
					var action_button = jQuery( element ),
					thiz_popup = action_button.closest( '.js-wpv-insert-view-form-dialog' ),
					display_container = thiz_popup.find( '.js-wpv-insert-view-form-display-container' ),
					target_container = thiz_popup.find( '.js-wpv-insert-view-form-target-container' ),
					prev_button = thiz_popup.find( '.js-wpv-insert-view-form-prev' ),
					form_name = '<?php echo $title; ?>',
					display = thiz_popup.find( '.js-wpv-insert-view-form-display:checked' ).val(),
					target = thiz_popup.find( '.js-wpv-insert-view-form-target:checked' ).val(),
					set_target = thiz_popup.find( '.js-wpv-insert-view-form-target-set:checked' ).val(),
					set_target_id = thiz_popup.find( '.js-wpv-insert-view-form-target-set-existing-id' ).val(),
					results_helper_container = jQuery( '.js-wpv-insert-form-workflow-help-box' ),
					results_helper_container_after = jQuery( '.js-wpv-insert-form-workflow-help-box-after' );
					
					if ( display == 'both' ) {
						window.icl_editor.insert('[wpv-view name="' + form_name + '"]');
						if ( results_helper_container.length > 0 && results_helper_container.hasClass( 'js-wpv-insert-form-workflow-help-box-for-<?php echo $view_id; ?>' ) ) {
							results_helper_container.fadeOut( 'fast' );
						}
						if ( results_helper_container_after.length > 0 && results_helper_container_after.hasClass( 'js-wpv-insert-form-workflow-help-box-for-after-<?php echo $view_id; ?>' ) ) {
							results_helper_container_after.show();
						}
						jQuery.colorbox.close();
					} else if ( display == 'results' ) {
						window.icl_editor.insert('[wpv-view name="' + form_name + '" view_display="layout"]');
						if ( results_helper_container.length > 0 && results_helper_container.hasClass( 'js-wpv-insert-form-workflow-help-box-for-<?php echo $view_id; ?>' ) ) {
							results_helper_container.fadeOut( 'fast' );
						}
						if ( results_helper_container_after.length > 0 && results_helper_container_after.hasClass( 'js-wpv-insert-form-workflow-help-box-for-after-<?php echo $view_id; ?>' ) ) {
							results_helper_container_after.show();
						}
						jQuery.colorbox.close();
					} else if ( display == 'form' ) {
						if ( action_button.hasClass( 'js-wpv-insert-view-form-dialog-steptwo' ) ) {
							if ( target == 'self' ) {
								window.icl_editor.insert('[wpv-form-view name="' + form_name + '" target_id="self"]');
								if ( results_helper_container.length > 0 ) {
									var results_shortcode = '<code>[wpv-view name="' + form_name + '" view_display=layout"]</code>';
									results_helper_container.find( '.js-wpv-insert-view-form-results-helper-name' ).html( form_name );
									results_helper_container.find( '.js-wpv-insert-view-form-results-helper-shortcode' ).html( results_shortcode );
									results_helper_container.addClass( 'js-wpv-insert-form-workflow-help-box-for-<?php echo $view_id; ?>' ).fadeIn( 'fast' );
								}
							} else {
								window.icl_editor.insert('[wpv-form-view name="' + form_name + '" target_id="' + set_target_id + '"]');
							}
							jQuery.colorbox.close();
						} else {
							action_button.addClass( 'js-wpv-insert-view-form-dialog-steptwo' ).html( action_button.data( 'insertlabel' ) );
							display_container.hide();
							target_container.show();
							prev_button.show();
							if ( target == 'self' ) {
								action_button.addClass( 'button-primary' ).removeClass( 'button-secondary' ).prop( 'disabled', false );
							} else {
								if ( set_target == 'existing' && set_target_id != '' ) {
									thiz_popup
										.find( '.js-wpv-insert-view-form-target-set-actions' )
											.show();
								}
								action_button.removeClass( 'button-primary' ).addClass( 'button-secondary' ).prop( 'disabled', true );
							}
						}
					}
				}
				
				jQuery('.js-wpv-insert-view-form-target-set-existing-title').suggest(ajaxurl + '?action=wpv_suggest_form_targets', {
					onSelect: function() {
						var t_value = this.value,
						t_split_point = t_value.lastIndexOf(' ['),
						t_title = t_value.substr( 0, t_split_point ),
						t_extra = t_value.substr( t_split_point ).split('#'),
						t_id = t_extra[1].replace(']', '');
						jQuery( '.js-wpv-filter-form-help' ).hide();
						jQuery('.js-wpv-insert-view-form-target-set-existing-title').val( t_title );
						t_edit_link = jQuery('.js-wpv-insert-view-form-target-set-existing-link').data( 'editurl' );
						t_view_id = jQuery('.js-wpv-insert-view-form-target-set-existing-link').data( 'viewid' );
						t_orig_id = jQuery('.js-wpv-insert-view-form-target-set-existing-link').data('origid');
						jQuery( '.js-wpv-insert-view-form-target-set-existing-link' ).attr( 'href', t_edit_link + t_id + '&action=edit&completeview=' + t_view_id + '&origid=' + t_orig_id );
						jQuery( '.js-wpv-insert-view-form-target-set-existing-id' ).val( t_id ).trigger( 'change' );
						jQuery( '.js-wpv-insert-view-form-target-set-actions' ).show();
					}
				});
				//]]>
			</script>
		</div> <!-- .js-wpv-insert-view-form-dialog -->
        <?php
	}   
	die();
}

/**
* wpv_suggest_form_targets
*
* Suggest for WPML string shortcode context, from a suggest callback
*
* @since 1.4
*/

add_action('wp_ajax_wpv_suggest_form_targets', 'wpv_suggest_form_targets');
add_action('wp_ajax_nopriv_wpv_suggest_form_targets', 'wpv_suggest_form_targets');

function wpv_suggest_form_targets() {
	global $wpdb, $sitepress;
	$trans_join = '';
	$trans_where = '';
	$values_to_prepare = array();
	$title_q = '%' . wpv_esc_like( $_REQUEST['q'] ) . '%';
	$values_to_prepare[] = $title_q;
	$exclude_post_type_slugs_where = '';
	$excluded_post_type_slugs = array();
	$excluded_post_type_slugs = apply_filters( 'wpv_admin_exclude_post_type_slugs', $excluded_post_type_slugs );
	if ( count( $excluded_post_type_slugs ) > 0 ) {
		$excluded_post_type_slugs_count = count( $excluded_post_type_slugs );
		$excluded_post_type_slugs_placeholders = array_fill( 0, $excluded_post_type_slugs_count, '%s' );
		$excluded_post_type_slugs_flat = implode( ",", $excluded_post_type_slugs_placeholders );
		foreach ( $excluded_post_type_slugs as $excluded_post_type_slugs_item ) {
			$values_to_prepare[] = $excluded_post_type_slugs_item;
		}
		$exclude_post_type_slugs_where = "AND post_type NOT IN ({$excluded_post_type_slugs_flat})";
	}
	if ( isset( $sitepress ) && function_exists( 'icl_object_id' ) ) {
		$current_lang_code = $sitepress->get_current_language();
		$trans_join = " JOIN {$wpdb->prefix}icl_translations t ";
		$trans_where = " AND ID = t.element_id AND t.language_code = %s ";
		$values_to_prepare[] = $current_lang_code;
	}
	$results = $wpdb->get_results( 
		$wpdb->prepare( "
            SELECT ID, post_title
            FROM {$wpdb->posts} {$trans_join}
            WHERE post_title LIKE '%s'
			{$exclude_post_type_slugs_where}
			AND post_status='publish' 
			{$trans_where}
            ORDER BY ID ASC
			LIMIT 5",
			$values_to_prepare 
		) 
	);
	foreach ($results as $row) {
		echo $row->post_title . " [#" . $row->ID . "]\n";
	}
	die();
}

add_action( 'wp_ajax_wpv_create_form_target_page', 'wpv_create_form_target_page' );

function wpv_create_form_target_page() {
	if ( 
		current_user_can( 'publish_pages' )
		&& wp_verify_nonce( $_GET['_wpnonce'], 'wpv_create_form_target_page_nonce' ) 
	) {
		$target_page = array(
		  'post_title' => wp_strip_all_tags( $_GET['post_title'] ),
		  'post_status' => 'publish',
		  'post_type' => 'page'
		);
		$target_page_id = wp_insert_post( $target_page );
		$target_page_title = get_the_title( $target_page_id );
		$response = array(
			'result' => 'success',
			'page_title' => $target_page_title,
			'page_id' => $target_page_id
		);
		echo json_encode( $response );
	} else {
		$response = array(
			'result' => 'error',
			'error' => __( 'Security error', 'wpv-views' )
		);
		echo json_encode( $response );
	}
	die();
}

/**
* ----------------------------------------------------------------------
## Translatable string ##
* ----------------------------------------------------------------------
*/

/**
* wpv_ajax_wpv_translatable_string_popup
*
* Popup for inserting a translatable string, loaded from a ColorBox AJAX call
*
* @param $_GET['_wpnonce']
*
* @since 1.4
* @uses ColorBox
*/

function wpv_ajax_wpv_translatable_string_popup() {
    
	if (wp_verify_nonce($_GET['_wpnonce'], 'wpv_editor_callback')) {
        ?>
		<div class="wpv-dialog js-insert-translatable-string-dialog">
			<div class="wpv-dialog-header">
				<h2><?php echo __('Insert translatable string', 'wpv-views'); ?></h2>
			</div>
			<div class="wpv-dialog-content">
				<p>
					<label for="wpv-translatable-string-value"><?php _e('String to translate', 'wpv-views'); ?></label>
					<input id="wpv-translatable-string-value" type="text" placeholder="" class="js-wpv-translatable-string-value" />
				</p>
				<p>
				<?php
				$icl_contexts = icl_st_get_contexts(false);
				if ( count( $icl_contexts ) == 1 ) {
					foreach ( $icl_contexts as $v ) {
						$context_value = $v->context;
					}
				} else {
					$context_value = '';
				}
				?>
					<label for="wpv-translatable-string-context"><?php _e('WPML context', 'wpv-views'); ?></label>
					<input id="wpv-translatable-string-context" type="text" placeholder="" value="<?php echo $context_value; ?>" class="js-wpv-translatable-string-context" />
				</p>
			</div>
			<div class="wpv-dialog-footer">
				<button class="button-secondary js-dialog-close"><?php _e('Cancel','wpv-views') ?></button>
				<button class="button-primary js-wpv-insert-translatable-string-shortcode" onclick="wpv_insert_translatable_string_shortcode()"><?php echo __('Insert shortcode', 'wpv-views'); ?></button>
			</div>
			<script type="text/javascript">
				//<![CDATA[
				function wpv_insert_translatable_string_shortcode() {
					jQuery('.js-wpv-insert-translatable-string-shortcode').removeClass('button-primary').addClass('button-secondary').prop('disabled', true);
					var spinnerContainer = jQuery('<div class="spinner ajax-loader">').insertAfter(jQuery('.js-wpv-insert-translatable-string-shortcode')).show(),
					tsValue = jQuery('.js-wpv-translatable-string-value').val(),
					tsContext = jQuery('.js-wpv-translatable-string-context').val();
					window.icl_editor.insert('[wpml-string context="' + tsContext + '"]' + tsValue + '[/wpml-string]');
					jQuery.colorbox.close();
				}
				
				jQuery('.js-wpv-translatable-string-context').suggest(ajaxurl + '?action=wpv_suggest_wpml_contexts', {
					onSelect: function() {
						
					}
				});
				
				//]]>
			</script>
		</div> <!-- .js-insert-translatable-string-dialog -->
        <?php
	}
	die();
}

/**
* wpv_suggest_wpml_contexts
*
* Suggest for WPML string shortcode context, from a suggest callback
*
* @since 1.4
*/

add_action('wp_ajax_wpv_suggest_wpml_contexts', 'wpv_suggest_wpml_contexts');
add_action('wp_ajax_nopriv_wpv_suggest_wpml_contexts', 'wpv_suggest_wpml_contexts');

function wpv_suggest_wpml_contexts() {
	global $wpdb;
	$context_q = '%' . wpv_esc_like( $_REQUEST['q'] ) . '%';
	$results = $wpdb->get_results( 
		$wpdb->prepare( 
            "SELECT DISTINCT context 
            FROM {$wpdb->prefix}icl_strings
            WHERE context LIKE %s
            ORDER BY context ASC", 
			$context_q 
		) 
	);
	foreach ( $results as $row ) {
		echo $row->context . "\n";
	}
	die();
}

/**
* ----------------------------------------------------------------------
## Search term ##
* ----------------------------------------------------------------------
*/

/**
* wpv_ajax_wpv_search_term_popup
*
* Popup to insert the search term shortcode, loaded from a ColorBox AJAX call
*
* @param $_GET['_wpnonce']
*
* @since unknown
* @uses ColorBox
*/

function wpv_ajax_wpv_search_term_popup() {
	if (wp_verify_nonce($_GET['_wpnonce'], 'wpv_editor_callback')) {
        ?>
		<div class="wpv-dialog js-insert-search-term-dialog">
			<div class="wpv-dialog-header">
				<h2><?php echo __('Insert search term shortcode', 'wpv-views'); ?></h2>
			</div>
			<div class="wpv-dialog-content">
				<p>
					<label for="wpv-search-term-param"><?php _e('URL parameter to watch (optional)', 'wpv-views'); ?></label>
					<input id="wpv-search-term-param" type="text" placeholder="" class="js-wpv-search-term-param" />
				</p>
			</div>
			<div class="wpv-dialog-footer">
				<button class="button-secondary js-dialog-close"><?php _e('Cancel','wpv-views') ?></button>
				<button class="button-primary js-wpv-insert-search-term-shortcode" onclick="wpv_insert_search_term_shortcode()"><?php echo __('Insert shortcode', 'wpv-views'); ?></button>
			</div>
			<script type="text/javascript">
				//<![CDATA[
				function wpv_insert_search_term_shortcode() {
					jQuery('.js-wpv-insert-search-term-shortcode').removeClass('button-primary').addClass('button-secondary').prop('disabled', true);
					var spinnerContainer = jQuery('<div class="spinner ajax-loader">').insertAfter(jQuery('.js-wpv-insert-search-term-shortcode')).show(),
					stParam = jQuery('.js-wpv-search-term-param').val(),
					stAttr = '';
					if ( stParam != '' ) {
						stAttr = ' param="' + stParam + '"';
					}
					window.icl_editor.insert('[wpv-search-term' + stAttr + ']');
					jQuery.colorbox.close();
				}
				//]]>
			</script>
		</div> <!-- .js-insert-translatable-string-dialog -->
        <?php
	}
	die();
}