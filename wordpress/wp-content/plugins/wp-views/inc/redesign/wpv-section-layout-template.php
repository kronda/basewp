<?php

add_action('view-editor-section-layout', 'add_view_layout_template', 40, 3);

function add_view_layout_template( $view_settings, $view_layout_settings, $view_id ) {
    global $views_edit_help;
    wp_nonce_field( 'wpv-ct-inline-edit', 'wpv-ct-inline-edit' );
    $templates = array();
    $valid_templates = array();
    $first_time = get_post_meta( $view_id, '_wpv_first_time_load', true );
    if ( isset( $view_layout_settings['included_ct_ids'] ) ) {
        $templates = explode( ',', $view_layout_settings['included_ct_ids'] );
        $valid_templates = $templates;
    }
    $template_list = '';
    if ( count( $templates ) > 0 ) {
		$attached_templates = count( $templates );
        for ( $i=0; $i<$attached_templates; $i++ ) {
			if ( is_numeric( $templates[$i] ) ) {
				$template_post = get_post( $templates[$i] );
				if ( is_object( $template_post ) ) {
					$template_list .= wpv_list_view_ct_item( $template_post, $templates[$i], $view_id );
				} else {
					unset( $valid_templates[$i] ); // remove Templates that might have been deleted or are missing
				}
            } else {
				unset( $valid_templates[$i] ); // remove Templates that might have been deleted or are missing
            }
        }
        if ( count( $templates ) != count( $valid_templates ) ) {
			$view_layout_settings['included_ct_ids'] = implode( ',', $valid_templates );
			update_post_meta( $view_id, '_wpv_layout_settings', $view_layout_settings );
        }
    }

    ?>
        <script type="application/javascript">
            var wpv_view_ct_msg1 = '<?php echo esc_js( __('Content Template was successfully assigned to view.', 'wpv-views') ); ?>';
            var wpv_view_ct_msg2 = '<?php echo esc_js( __('This Content Template already assigned to this view.', 'wpv-views') ); ?>';
            var wpv_view_ct_msg3 = '<?php echo esc_js( __('Content Template was successfully unassigned from view.', 'wpv-views') ); ?>';
            var wpv_view_ct_msg4 = '<?php echo esc_js( __('Content Template was successfully updated.', 'wpv-views') ); ?>';
            var wpv_view_ct_msg5 = '<?php echo esc_js( __('Views', 'wpv-views') ); ?>';
            var wpv_view_ct_msg6 = '<?php echo esc_js( __('No Content Templates assigned to this view', 'wpv-views') ); ?>';
            var wpv_view_ct_msg7 = '<?php echo esc_js( __('There are no Content Templates for this View. You can add a Content Template using the Content Template button in the Layout editor tool bar.', 'wpv-views') ); ?>';
            var wpv_view_ct_msg8 = '<?php echo esc_js( __( 'A Content Template with that name already exists. Please use another name.', 'wpv-views' ) ); ?>';
        </script>
		<div id="attached-content-templates" class="wpv-settings-templates wpv-setting-container wpv-setting-container-horizontal wpv-settings-layout-markup"<?php echo empty($template_list)? ' style="display:none;"':'' ?>>
            <div class="wpv-settings-header">
				<h3><?php _e('Templates for this View', 'wpv-views') ?>
					<i class="icon-question-sign js-display-tooltip" data-header="<?php echo $views_edit_help['templates_for_view']['title']; ?>" data-content="<?php echo $views_edit_help['templates_for_view']['content']; ?>"></i>
				</h3>
			</div>
			<?php
            if ( $first_time == 'on'){
				$purpose = $view_settings['purpose'];
                if ($purpose == 'slider') {
					$data = wpv_get_view_ct_slider_introduction_data();
					wpv_toolset_help_box($data);
				}
                if ($purpose == 'bootstrap-grid') {
					$data = wpv_get_view_ct_bootstrap_grid_introduction_data($view_settings["view-query-mode"]);
					wpv_toolset_help_box($data);
				}
            }
            ?>

			<div class="js-wpv-content-template-view-list wpv-content-template-view-list wpv-setting">
			    <ul>
			        <?php
			         echo $template_list
			        ?>
                </ul>
                <div class="js-wpv-content-template-section-errors"></div>
			</div>
		</div>
<?php }

function wpv_list_view_ct_item( $post, $ct_id, $view_id ){
    $meta = get_post_meta( $view_id, '_wpv_first_time_load', true);

    ob_start();

    ?>
    <li id="wpv-ct-listing-<?php echo $ct_id?>" class="js-wpv-ct-listing js-wpv-ct-listing-show layout-html-editor" data-id="<?php echo $ct_id?>" data-viewid="<?php echo $view_id?>">
        <p>
            <i class="icon-remove-sign js-wpv-ct-remove-from-view" title="<?php _e('Remove this Content Template','wpv-views'); ?>"></i>
            <button class="js-wpv-content-template-open wpv-content-template-open" data-target="<?php echo $ct_id?>" data-viewid="<?php echo $view_id?>">
            <?php echo $post->post_title;?>  <i class="icon-caret-down"></i>
            </button>
        </p>
        <div class="js-wpv-ct-inline-edit wpv-ct-inline-edit wpv-ct-inline-edit hidden" data-template-id="<?php echo $ct_id?>"></div>
    </li>
    <?php if ($meta == 'on' ){

        delete_post_meta( $view_id, '_wpv_first_time_load' );
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function($) {


                var $this = $('.js-wpv-content-template-open');
                var id = $this.data('target');
                var $inlineEditor = $('.js-wpv-ct-inline-edit ').filter('[data-template-id='+ id +']');
                var $arrowIcon = $this.find('[class^="icon-"]');
                $inlineEditor.toggle( 0 ,function() {
                    $arrowIcon
                            .removeClass('icon-caret-down')
                            .addClass('icon-caret-up');
                        if (!window["wpv_ct_inline_editor_" + id]){
                            var $spinnerContainer = $('<div class="spinner ajax-loader">').insertAfter($(this)).show();
                            data = {
                                action : 'wpv_ct_loader_inline',
                                id : id,
                                wpnonce : $('#wpv-ct-inline-edit').attr('value')
                            };

                            $.post(ajaxurl, data, function(response) {

                                $('#wpv-ct-listing-'+id).find('.js-wpv-ct-inline-edit').html(response);
                                if( typeof cred_cred != 'undefined'){
                                    cred_cred.posts();
                                }
                                window["wpv_ct_inline_editor_" + id] = icl_editor.codemirror('wpv-ct-inline-editor-'+id, true);
                                window["wpv_ct_inline_editor_val_" + id] = window["wpv_ct_inline_editor_" + id].getValue();
                                window["wpv_ct_inline_editor_" + id].on('change', function(){
                                    if( window["wpv_ct_inline_editor_val_" + id] !=  window["wpv_ct_inline_editor_" + id].getValue()){
                                        $('.js-wpv-ct-update-inline-'+ id).addClass('js-wpv-section-unsaved');
                                        setConfirmUnload(true);

                                        $('.js-wpv-ct-update-inline-' + id).removeClass('button-secondary').addClass('button-primary');
                                    }
                                    else{
                                        $('.js-wpv-ct-update-inline-'+ id).removeClass('js-wpv-section-unsaved');
                                        $('.js-wpv-ct-update-inline-'+ id).parent().find('.toolset-alert-error').remove();
                                        setConfirmUnload(true);
                                        $('.js-wpv-ct-update-inline-' + id).removeClass('button-primary').addClass('button-secondary');
                                    }
                                });

                                $spinnerContainer.remove();
                            });




                    }
                });
            });
        </script>
    <?php }?>
    <?php
    $row = ob_get_contents();
    ob_end_clean();

    return $row;
}
