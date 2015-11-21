/*
 * 
 * This file is used in the focus area edit page
 * Use a new namespace for this section
 */
ThriveFocusOptions.display_fields = {
    template1: ["container-focus-template", "container-focus-color", "container-heading-text", "container-subheading-text", "container-button-text",
        "container-button-link", "container-new-tab", "container-button-color"],
    template2: ["container-focus-template", "container-focus-image", "container-focus-color", "container-heading-text", "container-subheading-text", "container-button-text",
        "container-button-link", "container-new-tab", "container-button-color"],
    template3: ["container-focus-template", "container-focus-color", "container-heading-text", "container-subheading-text", "container-button-text",
        "container-optin", "container-button-color"],
    template4: ["container-focus-template", "container-focus-color", "container-heading-text", "container-button-color",
        "container-spam-disclaimer", "container-focus-image", "container-button-link", "container-new-tab", "postdivrich", "container-optin"],
    template5: ["container-focus-template", "container-focus-color", "container-heading-text", "container-subheading-text", "container-button-text",
        "container-spam-disclaimer", "container-focus-image", "container-button-link", "container-new-tab", "postdivrich", "container-optin", "container-button-color"],
    template6: ["container-focus-template", "container-button-link", "container-new-tab", "container-ribbon-text", "container-ribbon-color"],
    custom: ["container-custom-text", "container-focus-color", "container-focus-template", "container-subheading-text"]
};

ThriveFocusOptions.hide_fields = {
    template1: ["postdivrich", "container-spam-disclaimer", "container-optin", "container-focus-image", "container-custom-text", "container-ribbon-text", "container-ribbon-color"],
    template2: ["postdivrich", "container-spam-disclaimer", "container-optin", "container-custom-text", "container-ribbon-text", "container-ribbon-color"],
    template3: ["postdivrich", "container-button-link", "container-new-tab", "container-focus-image",
                "container-custom-text", "container-spam-disclaimer", "container-ribbon-text", "container-ribbon-color"],
    template4: ["container-button-link", "container-new-tab", "container-focus-image", "container-custom-text", "container-ribbon-text", "container-ribbon-color"],
    template5: ["container-button-link", "container-new-tab", "container-custom-text", "container-ribbon-text", "container-ribbon-color"],
    template6: ["container-button-color", "container-focus-color", "container-heading-text", "postdivrich", "container-button-text",
        "container-spam-disclaimer", "container-focus-image", "container-optin", "container-custom-text"],
    custom: ["container-heading-text", "container-button-text", "container-button-color", "container-ribbon-color",
        "container-spam-disclaimer", "container-focus-image", "container-button-link", "container-new-tab", "container-optin", "container-ribbon-text"]
};
/*
 * This handles the logic for displaying the option fields for each template
 */
ThriveFocusOptions.display_template_fields = function(template) {
    template = template.toLowerCase();
    switch (template) {
        case "template1":
            for (var i = 0; i < ThriveFocusOptions.display_fields.template1.length; i++) {
                jQuery("#" + ThriveFocusOptions.display_fields.template1[i]).show();
            }
            for (var i = 0; i < ThriveFocusOptions.hide_fields.template1.length; i++) {
                jQuery("#" + ThriveFocusOptions.hide_fields.template1[i]).hide();
            }
            break;
        case "template2":
            for (var i = 0; i < ThriveFocusOptions.display_fields.template2.length; i++) {
                jQuery("#" + ThriveFocusOptions.display_fields.template2[i]).show();
            }
            for (var i = 0; i < ThriveFocusOptions.hide_fields.template2.length; i++) {
                jQuery("#" + ThriveFocusOptions.hide_fields.template2[i]).hide();
            }
            break;
        case "template3":
            for (var i = 0; i < ThriveFocusOptions.display_fields.template3.length; i++) {
                jQuery("#" + ThriveFocusOptions.display_fields.template3[i]).show();
            }
            for (var i = 0; i < ThriveFocusOptions.hide_fields.template3.length; i++) {
                jQuery("#" + ThriveFocusOptions.hide_fields.template3[i]).hide();
            }
            break;
        case "template4":
            for (var i = 0; i < ThriveFocusOptions.display_fields.template4.length; i++) {
                jQuery("#" + ThriveFocusOptions.display_fields.template4[i]).show();
            }
            for (var i = 0; i < ThriveFocusOptions.hide_fields.template4.length; i++) {
                jQuery("#" + ThriveFocusOptions.hide_fields.template4[i]).hide();
            }
            break;
        case "template5":
            for (var i = 0; i < ThriveFocusOptions.display_fields.template5.length; i++) {
                jQuery("#" + ThriveFocusOptions.display_fields.template5[i]).show();
            }
            for (var i = 0; i < ThriveFocusOptions.hide_fields.template5.length; i++) {
                jQuery("#" + ThriveFocusOptions.hide_fields.template5[i]).hide();
            }
            break;
        case "template6":
            for (var i = 0; i < ThriveFocusOptions.display_fields.template6.length; i++) {
                jQuery("#" + ThriveFocusOptions.display_fields.template6[i]).show();
            }
            for (var i = 0; i < ThriveFocusOptions.hide_fields.template6.length; i++) {
                jQuery("#" + ThriveFocusOptions.hide_fields.template6[i]).hide();
            }
            break;
        case "template0":
            for (var i = 0; i < ThriveFocusOptions.display_fields.custom.length; i++) {
                jQuery("#" + ThriveFocusOptions.display_fields.custom[i]).show();
            }
            for (var i = 0; i < ThriveFocusOptions.hide_fields.custom.length; i++) {
                jQuery("#" + ThriveFocusOptions.hide_fields.custom[i]).hide();
            }
            break;
    }
        
};

jQuery(document).ready(function() {

    jQuery(".btn").click(function() {
        var id_input = jQuery(this).attr('for');
        jQuery("#" + id_input).triggerHandler('click');
        jQuery("#" + id_input).trigger('click');
    });

    jQuery("#thrive_meta_focus_display_sel_categories").select2();

    jQuery("#thrive_meta_focus_display_sel_categories").on("change", function(e) {
        var temp_cat_values = JSON.stringify(jQuery("#thrive_meta_focus_display_sel_categories").select2("val"));
        jQuery("#thrive_meta_focus_hidplay_hidden_cats").val(temp_cat_values);
    });
    if (jQuery("#thrive_meta_focus_display_sel_categories").length > 0) {
        var temp_cat_values = JSON.stringify(jQuery("#thrive_meta_focus_display_sel_categories").select2("val"));
        jQuery("#thrive_meta_focus_hidplay_hidden_cats").val(temp_cat_values);
    }
    jQuery(".thrive_meta_focus_display_is_default").click(function() {
        if (jQuery(this).val() === "0") {
            jQuery("#container_display_show_in_cats").show();
        } else {
            jQuery("#container_display_show_in_cats").hide();
        }
    });

    ThriveFocusOptions.display_template_fields(jQuery("#thrive_focus_template").val());
    //Toggle the display options from the right sidebar for a post
    jQuery(".thrive_meta_focus_display_post_type").click(function() {
        if (jQuery(this).val() === "post") {
            jQuery("#container_display_post_options").show();
        } else {
            jQuery("#container_display_post_options").hide();
        }
        if (jQuery(this).val() === "page") {
            jQuery("#container_display_page_options").show();
        } else {
            jQuery("#container_display_page_options").hide();
        }
    });

    jQuery("#thrive_focus_template").change(function() {
        ThriveFocusOptions.display_template_fields(jQuery(this).val());        
    });

    //deal with the file upload
    var file_frame;
    jQuery('#thrieve_btn_focus_image').click(function(event) {
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
            jQuery("#thrive_meta_focus_image_hidden").val(attachment.url);
            jQuery("#thrive_meta_focus_image").val(attachment.url);
        });
        file_frame.open();
    });

    jQuery("#thrive_focus_options").find(".inside").show();

    jQuery("#thrive-link-focus-preview, #thrive_focus_color").click(ThriveFocusOptions.preview_focus_area_handler);
    jQuery("#thrive_focus_template").change(ThriveFocusOptions.preview_focus_area_handler);

    ThriveFocusOptions.preview_focus_area_handler();

    jQuery(".opt_in_template").click(function() {
        jQuery(".opt_in_template").removeClass("selected");
        jQuery(".opt_in_template").find(".tick").remove();
        var this_template_number = jQuery(this).find(".this_template").val();
        jQuery("#thrive_focus_template").val("Template" + this_template_number);
        jQuery(this).addClass("selected");
        jQuery(this).prepend("<div class='tick'></div>");
        ThriveFocusOptions.preview_focus_area_handler();
        ThriveFocusOptions.display_template_fields("Template" + this_template_number);
        if (this_template_number == 6) { //ribbon template
            jQuery("#thrive_meta_focus_display_location").val("top");
        }
    });

    jQuery("#thrieve_btn_focus_clear_image").click(function() {
        jQuery("#thrive_meta_focus_image_hidden").val("");
        jQuery("#thrive_meta_focus_image").val("");
    });

    jQuery("#thrive_meta_focus_display_location").change(function() {
        if (jQuery("#thrive_focus_template").val() == "Template6") {
            if (jQuery(this).val() == "bottom") {
                alert("This template can be assigned only at the top");
                jQuery(this).val("top");
            }
        }
    });

});

ThriveFocusOptions.preview_focus_area_handler = function() {

    var subheading_content;
    var temp_editor = tinyMCE.get("thrive_meta_focus_subheading_text");
    if (temp_editor) {
        subheading_content = temp_editor.getContent();
    } else {
        subheading_content = jQuery('#thrive_meta_focus_subheading_text').val();
    }

    var postData = {
        noonce: ThriveFocusOptions.noonce,
        id_post: ThriveFocusOptions.id_post,
        'thrive_meta_focus_template': jQuery("#thrive_focus_template").val(),
        'thrive_meta_focus_color': jQuery("#thrive_focus_color").val(),
        'thrive_meta_focus_heading_text': jQuery("#thrive_meta_focus_heading_text").val(),
        'thrive_meta_focus_subheading_text': subheading_content,
        'thrive_meta_focus_button_text': jQuery("#thrive_meta_focus_button_text").val(),
        'thrive_meta_focus_button_color': jQuery("#thrive_meta_focus_button_color").val(),
        'thrive_meta_focus_spam_text': jQuery("#thrive_meta_focus_spam_text").val(),
        'thrive_meta_focus_image': jQuery("#thrive_meta_focus_image").val(),
        'thrive_meta_focus_button_link': jQuery("#thrive_meta_focus_button_link").val(),
        'thrive_meta_focus_new_tab': jQuery("#thrive_meta_focus_new_tab").val(),
        'thrive_meta_focus_optin': jQuery("#thrive_meta_focus_optin").val(),
        'thrive_meta_focus_custom_text': jQuery("#thrive_meta_focus_custom_text").val() || '',
        'thrive_meta_focus_ribbon_text': jQuery("#thrive_meta_focus_ribbon_text").val(),
        'thrive_meta_ribbon_color' : jQuery("#thrive_ribbon_color").val()
    };

    jQuery.post(ThriveFocusOptions.focusPreviewUrl, postData, function(response) {
        if (response === 0 || response === "0") {
            alert('Something went wrong. Please refresh and try again.');
            return;
        }
        jQuery("#container-focus-preview").html(response);
    });
};