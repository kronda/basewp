/**
* wiews_conditional_button_plugin.js
*
* Contains helper functions for Views conditional output button in TinyMCE toolbar
*
* @since 1.10
* @package Views
*/

(function() {
    tinymce.create("tinymce.plugins.wpv_add_views_conditional_button", {

        //url argument holds the absolute url of our plugin directory
        init : function(ed, url) {

            //add new button    
            ed.addButton("wpv_conditional_output", {
                title : wpv_shortcodes_gui_texts.wpv_conditional_button,
                cmd : "views_conditional_output",
                icon : 'icon wpv-conditional-output-icon'
            });

            //button functionality.
            ed.addCommand("views_conditional_output", function() {
                current_editor_object = {'e' : '', 'c' : '', 'ed' : ed, 't' : '', 'post_id' : '', 'close_tag' : true, 'codemirror' : ''};
                window.wpcfActiveEditor = ed.id;
                WPViews.shortcodes_gui.wpv_insert_popup_conditional('wpv-conditional', icl_editor_localization_texts.wpv_insert_conditional_shortcode, {}, icl_editor_localization_texts.wpv_editor_callback_nonce, current_editor_object );
            });

        }
    });

    tinymce.PluginManager.add("wpv_add_views_conditional_button", tinymce.plugins.wpv_add_views_conditional_button);
})();