<?php

/**
 * Module Manager section
 *
 * Simple MM integration.
 *
 * @since 1.9
 */

add_action( 'init', 'wpv_ct_editor_module_manager_section_init' );

/**
 * If Module Manager is active, add this section on CT edit page.
 */
function wpv_ct_editor_module_manager_section_init() {
    if ( defined( 'MODMAN_PLUGIN_NAME' ) ) {
        add_action( 'wpv_ct_editor_sections', 'wpv_ct_editor_module_manager_section', 50 );
    }
}


/**
 * Render the Module Manager section.
 *
 * Active Module Manager plugin is assumed.
 *
 * @param WPV_Content_Template $ct Content Template
 */
function wpv_ct_editor_module_manager_section( $ct ) {
    ob_start();

    // Copied from the old CT page (almost) without change
    $element = array(
        'id'=> _VIEW_TEMPLATES_MODULE_MANAGER_KEY_ . $ct->id,
        'title'=> $ct->title,
        'section'=>_VIEW_TEMPLATES_MODULE_MANAGER_KEY_
    );

    do_action( 'wpmodules_inline_element_gui', $element );

    $content = ob_get_contents();
    ob_end_clean();

    wpv_ct_editor_render_section(
        __( 'Module Manager', 'wpv-views' ),
        '',
        $content,
        false,
        'wpv-setting-container-module-manager',
        'wpv-setting-module-manager'
    );
}