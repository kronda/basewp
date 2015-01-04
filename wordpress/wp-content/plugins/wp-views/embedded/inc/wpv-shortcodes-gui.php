<?php

/**
* All callback actions to display popups to set options for our Views shiortcodes go here
*
*/

// First, we add javascript onclick callbacks
// TODO maybe move this to a proper file, or at least do not load it on every admin page

if(is_admin()){ // This is being added to all backend, maybe only on post.php, post-new.php and Views edit screens.We need it there to make this colorbox popups appear
	add_action('init', 'wpv_shortcodes_gui_init');
}

function wpv_shortcodes_gui_init() {
	add_action('admin_head', 'wpv_shortcodes_gui_js_init');
}

function wpv_shortcodes_gui_js_init() {
?>
	<script type="text/javascript">
		//<![CDATA[
		function wpv_insert_view_form_popup(view_id) {
			jQuery.colorbox({
				href: '<?php echo admin_url('admin-ajax.php'); ?>' + '?_wpnonce=' + '<?php echo wp_create_nonce('wpv_editor_callback'); ?>' + '&action=wpv_view_form_popup&view_id=' + view_id,
				inline : false,
				onComplete: function() {

				}
			});
		}
		
		function wpv_insert_search_term_popup() {
			jQuery.colorbox({
				href: '<?php echo admin_url('admin-ajax.php'); ?>' + '?_wpnonce=' + '<?php echo wp_create_nonce('wpv_editor_callback'); ?>' + '&action=wpv_search_term_popup',
				inline : false,
				onComplete: function() {
					
				}
			});
		}
		
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
		var wpcfFieldsEditorCallback_redirect = null;
		
		function wpcfFieldsEditorCallback_set_redirect(function_name, params) {
		wpcfFieldsEditorCallback_redirect = {'function' : function_name, 'params' : params};
		}
		
		//]]>
	</script>
<?php
}

/**
*
* Popup for inserting a View form
*
* Since 1.4 uses ColorBox
*
*/


function wpv_ajax_wpv_view_form_popup() {
    
	global $wpdb, $sitepress;
    
	if (wp_verify_nonce($_GET['_wpnonce'], 'wpv_editor_callback')) {

        $view_id = $_GET['view_id'];
        
        $title = $wpdb->get_var("SELECT post_title FROM {$wpdb->posts} WHERE ID={$view_id}");
        
        // Find the posts that use this view
        
        $trans_join = '';
	$trans_where = '';
	
	if ( isset( $sitepress ) ) {
		$current_lang_code = $sitepress->get_current_language();
		$trans_join = " JOIN {$wpdb->prefix}icl_translations t ";
		$trans_where = " AND ID = t.element_id AND t.language_code =  '{$current_lang_code}' ";
	}
        
        $posts = $wpdb->get_results("SELECT ID, post_title, post_content FROM {$wpdb->posts} {$trans_join} WHERE post_content LIKE '%name=\"{$title}\"%' AND post_type NOT IN ('view','view-template','revision','cred-form') AND post_status='publish' {$trans_where}");
        
        ?>
		<div class="wpv-dialog js-insert-view-form-dialog">
			<div class="wpv-dialog-header">
				<h2><?php echo sprintf(__('Insert search form from View - %s', 'wpv-views'), '<strong>' . $title . '</strong>'); ?></h2>
			</div>
			<?php if (count($posts) == 0) { ?>
				<div class="wpv-dialog-content">
					<p><strong><?php echo __('No target posts were found that use this View', 'wpv-views'); ?></strong></p>
					<p><?php echo sprintf( __('You first need to insert the View named %s in a post or page to show its results', 'wpv-views'), $title ); ?></p>
				</div>
				<div class="wpv-dialog-footer">
					<button class="button-primary js-dialog-close"><?php _e('Close','wpv-views') ?></button>
				</div>
			<?php } else { ?>
				<div class="wpv-dialog-content">
					<input type="hidden" value="<?php echo $view_id; ?>" id="wpv_view_id" />
					<?php echo __('When the form is submitted, go to the results page:', 'wpv-views'); ?>
					<select id="wpv_filter_form_target">
					
						<option value="0">None</option>
						<?php
							$first = true;
							foreach($posts as $post) {
							$post_title = $post->post_title;
							if ($post_title == '') {
								$post_title = $post->ID;
							}
							if ($first) {
								echo '<option value="' . $post->ID . '" selected="selected" >' . $post->post_title . '</option>';
							} else {
								echo '<option value="' . $post->ID . '">' . $post->post_title . '</option>';
							}
							$first = false;
							}
						
						?>
					
					</select>
				</div>
				<div class="wpv-dialog-footer">
					<button class="button-secondary js-dialog-close"><?php _e('Cancel','wpv-views') ?></button>
				<!--	<button class="button button-primary js-remove-view-permanent" data-nonce="<?php echo wp_create_nonce( 'wpv_remove_view_permanent_nonce' ); ?>"><?php _e('Delete','wpv-views') ?></button> -->
					<button class="button-primary js-wpv-insert-form-shortcode" onclick="wpv_insert_form_shortcode()"><?php echo __('Insert shortcode', 'wpv-views'); ?></button>
				</div>
			<?php } ?>
			<script type="text/javascript">
				//<![CDATA[
				function wpv_insert_form_shortcode() {
					jQuery('.js-wpv-insert-form-shortcode').removeClass('button-primary').addClass('button-secondary').prop('disabled', true);
					var spinnerContainer = jQuery('<div class="spinner ajax-loader">').insertAfter(jQuery('.js-wpv-insert-form-shortcode')).show(),
					form_name = '<?php echo $title; ?>';
					window.icl_editor.insert('[wpv-form-view name="' + form_name + '" target_id="' + jQuery('#wpv_filter_form_target').val() + '"]');
					jQuery.colorbox.close();
				}
				
				//]]>
			</script>
		</div> <!-- .js-insert-view-form-dialog -->
        <?php
	}        
	die();
}

/**
*
* Popup for inserting a translatable string
*
* Since 1.4
*
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

// Suggest for WPML string shortcode context

add_action('wp_ajax_wpv_suggest_wpml_contexts', 'wpv_suggest_wpml_contexts');
add_action('wp_ajax_nopriv_wpv_suggest_wpml_contexts', 'wpv_suggest_wpml_contexts');

function wpv_suggest_wpml_contexts() {
	global $wpdb;
	$user = esc_sql(like_escape($_REQUEST['q']));
	$results = $wpdb->get_results("
            SELECT DISTINCT context 
            FROM {$wpdb->prefix}icl_strings
            WHERE context LIKE '%$user%'
            ORDER BY context ASC");
	foreach ($results as $row) {
		echo $row->context . "\n";
	}
	die();
}