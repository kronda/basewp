<?php

/**
 * Settings section for Content Template edit page
 *
 * @since 1.9
 */


/* ************************************************************************* *\
        Request WPV_Content_Template properties for the JS side
\* ************************************************************************* */


add_filter( 'wpv_ct_editor_request_properties', 'wpv_ct_editor_settings_section_request_properties' );


function wpv_ct_editor_settings_section_request_properties( $property_names ) {
	return array_merge( $property_names, array( 'output_mode' ) );
}


/* ************************************************************************* *\
        Localize the section in JS
\* ************************************************************************* */


add_filter( 'wpv_ct_editor_localize_script', 'wpv_ct_editor_settings_section_localize_script' );


function wpv_ct_editor_settings_section_localize_script( $l10n_data ) {
	$l10n_data['settings_section'] = array(
		'saved' => esc_attr( __( 'Settings updated.', 'wpv-views' ) ),
		'unsaved' => esc_attr(__( 'Settings not saved.', 'wpv-views' ) ),
        'ptr_output_mode' => array(
            'title' => esc_attr( __( 'Output mode', 'wpv-views' ) ),
            'paragraphs' => array(
                esc_attr( __( 'Auto-insert paragraphs - convert single line breaks into &lt;br&gt; tags and double line breaks into &lt;p&gt; (paragraph) tags.', 'wpv-views' ) ),
                esc_attr( __( 'Manual paragraphs - don\'t create &lt;br&gt; and &lt;p&gt; tags from line breaks.', 'wpv-views' ) )
            )
        )
	);
	return $l10n_data;
}


/* ************************************************************************* *\
        Render section content
\* ************************************************************************* */


add_action( 'wpv_ct_editor_sections', 'wpv_ct_editor_settings_section', 40 );


function wpv_ct_editor_settings_section(
    /** @noinspection PhpUnusedParameterInspection */ $ct )
{
	ob_start();

	?>
		<!-- output mode -->
		<h3>
            <?php _e( 'Output mode', 'wpv-views' ) ?>
            <i class="icon-question-sign js-wpv-show-pointer" data-section="settings_section" data-pointer-slug="ptr_output_mode"></i>
        </h3>
        <p>
            <label>
                <input type="radio" value="WP_mode" data-bind="checked: outputModeAccepted" />
                <?php _e( 'Auto-insert paragraphs', 'wpv-views' ); ?>
            </label>
        </p>
		<p>
		    <label>
		        <input type="radio" value="raw_mode" data-bind="checked: outputModeAccepted" />
		        <?php _e( 'Manual paragraphs', 'wpv-views' ); ?>
            </label>
        </p>
        <p>
            <span class="update-action-wrap auto-update">
                <span class="js-wpv-message-container"></span>
                <span class="spinner ajax-loader" data-bind="spinnerActive: isSettingsSectionUpdating"></span>
            </span>
		</p>

	<?php

	$content = ob_get_contents();
	ob_end_clean();

	wpv_ct_editor_render_section( __( 'Settings', 'wpv-views' ), 'js-wpv-settings-section', $content );
}