//use this as a namespace for the theme's widget options javascript code
if (ThriveWidgetsOptions === undefined) {
    var ThriveWidgetsOptions = {};
}
ThriveWidgetsOptions.img_textfield_id = "";
ThriveWidgetsOptions.controls_binded = 0;

ThriveWidgetsOptions.check_show_tabs_handler = function() {
    var checkedSelections = jQuery(this).parent().find(":checkbox:checked").length;
    if (checkedSelections > 3) {
        jQuery(this).attr('checked', false);
        alert('Maximum number of tabs is 3!');

        //return false;
    }
    ThriveWidgetsOptions.display_tabs_options();
    //return false;
};

ThriveWidgetsOptions.bind_handlers = function() {
    ThriveWidgetsOptions.controls_binded = 1;
    jQuery('.thrive_author_widget_btn_upload').click(ThriveWidgetsOptions.handle_file_upload);
    jQuery('.thrive_call_widget_btn_upload').click(ThriveWidgetsOptions.handle_file_upload);
    jQuery('.thrive_optin_widget_btn_upload').click(ThriveWidgetsOptions.handle_file_upload);
    jQuery('.thrive_chk_show_tabs').click(ThriveWidgetsOptions.check_show_tabs_handler);
    ThriveWidgetsOptions.display_tabs_options();
    
    jQuery(".thrive_author_widget_txt_profile").autocomplete({
        source: ThriveWidgetsOptions.getUsersUrl,
        minLength: 1,
        select: function(event, ui) {
            jQuery(this).parents(".widget-inside").find(".thrive_author_widget_hidden_profile").val(ui.item.id);
        }
    });
};

ThriveWidgetsOptions.display_tabs_options = function() {
    //first hide all the options
    jQuery('.thrive_tabs_widget_container_options_trending').hide();
    jQuery('.thrive_tabs_widget_container_options_alltime').hide();
    jQuery('.thrive_tabs_widget_container_options_recent').hide();
    jQuery('.thrive_tabs_widget_container_options_category').hide();
    jQuery('.thrive_tabs_widget_container_options_custom').hide();
    jQuery(".thrive_chk_show_trending").each(function(index) {
        if (jQuery(this).prop('checked')) {
            jQuery(this).parents(".widget-content").find('.thrive_tabs_widget_container_options_trending').show();
        }
    });
    jQuery(".thrive_chk_show_popular").each(function(index) {
        if (jQuery(this).prop('checked')) {
            jQuery(this).parents(".widget-content").find('.thrive_tabs_widget_container_options_alltime').show();
        }
    });
    jQuery(".thrive_chk_show_recent").each(function(index) {
        if (jQuery(this).prop('checked')) {
            jQuery(this).parents(".widget-content").find('.thrive_tabs_widget_container_options_recent').show();
        }
    });
    jQuery(".thrive_chk_show_category").each(function(index) {
        if (jQuery(this).prop('checked')) {
            jQuery(this).parents(".widget-content").find('.thrive_tabs_widget_container_options_category').show();
        }
    });
    jQuery(".thrive_chk_show_menu").each(function(index) {
        if (jQuery(this).prop('checked')) {
            jQuery(this).parents(".widget-content").find('.thrive_tabs_widget_container_options_custom').show();
        }
    });

};

jQuery(document).ready(function() {

    jQuery(".widget-control-save").click(function() {
        ThriveWidgetsOptions.controls_binded = 0;
    });

    ThriveWidgetsOptions.bind_handlers();

    window.send_to_editor = function(html) {
        var image_url = jQuery('img', html).attr('src');
        jQuery("#" + ThriveWidgetsOptions.img_textfield_id).val(image_url);
        tb_remove();
    };

    jQuery(document).ajaxSuccess(function(e, xhr, settings) {
        ThriveWidgetsOptions.bind_handlers();
    });

    jQuery('div.widgets-sortables').bind('sortstop', function(event, ui) {
        ThriveWidgetsOptions.bind_handlers();
    });
});

//deal with the file upload
var file_frame;
ThriveWidgetsOptions.handle_file_upload = function(event) {
    var _current_item_class = jQuery(this).attr('class');
    if (_current_item_class === "thrive_author_widget_btn_upload") {
        ThriveWidgetsOptions.img_textfield_id = jQuery(this).parent().find('.thrive_author_widget_txt_image').attr('id');
    } else if (_current_item_class === "thrive_optin_widget_btn_upload") {
        ThriveWidgetsOptions.img_textfield_id = jQuery(this).parent().find('.thrive_optin_widget_txt_image').attr('id');
    } else if (_current_item_class === "thrive_call_widget_btn_upload") {
        ThriveWidgetsOptions.img_textfield_id = jQuery(this).parent().find('.thrive_call_widget_txt_image').attr('id');
    }
    event.preventDefault();
    if (file_frame) {
        file_frame.open();
        return;
    }
    file_frame = wp.media.frames.file_frame = wp.media({
        title: jQuery(this).data('uploader_title'),
        button: {
            text: jQuery(this).data('uploader_button_text'),
        },
        multiple: false
    });
    file_frame.on('select', function() {
        attachment = file_frame.state().get('selection').first().toJSON();
        jQuery("#" + ThriveWidgetsOptions.img_textfield_id).val(attachment.url);
    });
    file_frame.open();
};
