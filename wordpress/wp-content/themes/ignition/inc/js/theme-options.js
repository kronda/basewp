//use this as a namespace for the theme's options javascript code
if (ThriveThemeOptions === undefined) {
    var ThriveThemeOptions = {};
}
ThriveThemeOptions.img_textfield_id = "";
ThriveThemeOptions.currentMenuItem = "general-options";
var file_frame;
jQuery(document).ready(function() {

    var _selectedMenuItem = ThriveThemeOptions.get_selectedItem();
    jQuery("#thrive-admin-subcontainer-" + _selectedMenuItem).show();
    ThriveThemeOptions.set_selectedItem("general-options");
    jQuery('body').find('a[rel='+ _selectedMenuItem + ']').addClass('selected');
    ThriveThemeOptions.currentMenuItem = _selectedMenuItem;

    jQuery(".thrive-admin-submenu a").click(function() {
        jQuery(".thrive-admin-subcontainer").hide();
        jQuery("#thrive-admin-subcontainer-" + jQuery(this).attr('rel')).fadeIn();
        ThriveThemeOptions.currentMenuItem = jQuery(this).attr('rel');
    });
    jQuery(".thrive-admin-submenu a").click(function(){
        jQuery(this).addClass('selected');
        jQuery(this).siblings('a').removeClass('selected');
    });

    jQuery(".thrive-admin-subcontainer h3").click(function() {
        jQuery(this).next().slideToggle(100);
    });

    jQuery("#thrive_theme_hide_cats_from_blog_sel").select2();

    jQuery("#thrive_theme_hide_cats_from_blog_sel").on("change", function(e) {
        var temp_cat_values = JSON.stringify(jQuery("#thrive_theme_hide_cats_from_blog_sel").select2("val"));
        jQuery("#thrive_theme_hide_cats_from_blog_hidden").val(temp_cat_values);
    });
    if (jQuery("#thrive_theme_hide_cats_from_blog_sel").length > 0) {
        var temp_cat_values = JSON.stringify(jQuery("#thrive_theme_hide_cats_from_blog_sel").select2("val"));
    }
    jQuery("#thrive_theme_hide_cats_from_blog_hidden").val(temp_cat_values);

    jQuery(".clear-field").click(function() {
        jQuery(this).siblings("input[type='text']").val("");
    });

    jQuery('.thrive_option_header_nav_slider').noUiSlider({range: [0, 400],
        start: jQuery("#thrive_theme_option_hidden_nav_header").val(),
        handles: 1,
        step: 1,
        slide: function() {
            var temp_val = Math.floor(jQuery(".thrive_option_header_nav_slider").val());
            jQuery("#thrive_theme_option_hidden_nav_header").val(temp_val);
            jQuery(".thrive_label_header_nav_value").html(temp_val);
        }
    });

    
    jQuery('#thrieve_theme_btn_client_logos').on('click', function(event) {        
        event.preventDefault();
        if (file_frame) {
            file_frame.open();
            return;
        }
        file_frame = wp.media.frames.file_frame = wp.media({
            title: jQuery(this).data('uploader_title'),
            button: {
                text: jQuery(this).data('uploader_button_text')
            },
            multiple: true  // Set to true to allow multiple files to be selected
        });

        // When an image is selected, run a callback.
        file_frame.on('select', function() {            
            var _pics_to_append = "";
            file_frame.state().get('selection').each(function(element) {                              
               _pics_to_append += element.attributes.url + "\n";
            });
            if (jQuery("#thrive_theme_options_client_logos").val() == "") {
                 jQuery("#thrive_theme_options_client_logos").val(_pics_to_append);
            } else {
                jQuery("#thrive_theme_options_client_logos").val(jQuery("#thrive_theme_options_client_logos").val() + "\n" + _pics_to_append);
            }
        });
        file_frame.open();
    });
    
    jQuery('#thrive_theme_options_logo_btn').on('click', ThriveThemeOptions.handle_file_upload);
    jQuery('#thrive_theme_options_favicon_btn').on('click', ThriveThemeOptions.handle_file_upload);
    
    jQuery("#logo_type_image").click(ThriveThemeOptions.display_logo_fields);
    jQuery("#logo_type_text").click(ThriveThemeOptions.display_logo_fields);
    ThriveThemeOptions.display_logo_fields();
    
    jQuery("#thrive_theme_options_social_display_posts").click(ThriveThemeOptions.set_social_display_location_value);
    jQuery("#thrive_theme_options_social_display_pages").click(ThriveThemeOptions.set_social_display_location_value);
    
    jQuery(".thrive_chk_social_custom_posts").click(ThriveThemeOptions.set_social_custom_type_value);
    
    jQuery(".radio_sel_social_attention_grabber").click(ThriveThemeOptions.display_social_cta_fields);
    ThriveThemeOptions.display_social_cta_fields();

    jQuery(".thrive_header_phone_chk").click(ThriveThemeOptions.display_header_phone_options);
    ThriveThemeOptions.display_header_phone_options();
    
    jQuery("#thrive_btn_preview_header_phone").click(ThriveThemeOptions.preview_optin_handler);
    
    //related posts category and tags select
    jQuery("#sel_thrive_theme_options_related_ignore_cats").select2();
    jQuery("#sel_thrive_theme_options_related_ignore_cats").on("change", function(e) {
        var temp_cat_values = JSON.stringify(jQuery("#sel_thrive_theme_options_related_ignore_cats").select2("val"));
        jQuery("#thrive_theme_options_related_ignore_cats").val(temp_cat_values);
    });
    if (jQuery("#sel_thrive_theme_options_related_ignore_cats").length > 0) {
        var temp_cat_values = JSON.stringify(jQuery("#sel_thrive_theme_options_related_ignore_cats").select2("val"));
        jQuery("#thrive_theme_options_related_ignore_cats").val(temp_cat_values);
    }
    
    jQuery("#sel_thrive_theme_options_related_ignore_tags").select2();
    jQuery("#sel_thrive_theme_options_related_ignore_tags").on("change", function(e) {
        var temp_tag_values = JSON.stringify(jQuery("#sel_thrive_theme_options_related_ignore_tags").select2("val"));
        jQuery("#thrive_theme_options_related_ignore_tags").val(temp_tag_values);
    });
    if (jQuery("#sel_thrive_theme_options_related_ignore_tags").length > 0) {
        var temp_tag_values = JSON.stringify(jQuery("#sel_thrive_theme_options_related_ignore_tags").select2("val"));
        jQuery("#thrive_theme_options_related_ignore_tags").val(temp_tag_values);
    }    
    //enable or disable the related posts feature
    jQuery("#theme_options_enable_related_posts").click(function() {
        jQuery("#theme_options_hidden_related_posts_enabled").val(1);
        jQuery("#tt-submit-button").trigger("click");
    });    
    jQuery("#theme_options_disable_related_posts").click(function() {
        jQuery("#theme_options_hidden_related_posts_enabled").val(0);
        jQuery("#tt-submit-button").trigger("click");
    });
    
    jQuery("#thrive_btn_generate_related_posts").click(ThriveThemeOptions.generate_related_handler);
    
    jQuery("#tt-submit-button").click(function(event) {
        event.preventDefault();
        //fix for external elements that could mess up the submit
        jQuery("#thrive-options-form").find("input[name=submit]").remove();
        var _selected_element_rel = jQuery(".thrive-admin-submenu a.selected").attr("rel");
        ThriveThemeOptions.set_selectedItem(_selected_element_rel);
        jQuery("#thrive-options-form").submit();
        return true;
    });

    jQuery('#related_posts_box_on').click(ThriveThemeOptions.show_related_posts_settings);
    jQuery('#related_posts_box_off').click(ThriveThemeOptions.hide_related_posts_settings);
    if (jQuery('#related_posts_box_on:checked').length > 0) {
        ThriveThemeOptions.show_related_posts_settings();
    } else if (jQuery('#related_posts_box_off:checked').length > 0) {
        ThriveThemeOptions.hide_related_posts_settings();
    }

    //apprentice features
    jQuery("#theme_options_enable_apprentice").click(function() {
        jQuery("#theme_options_hidden_appr_enable_feature").val(1);
        jQuery("#tt-submit-button").trigger("click");
    });

    ThriveThemeOptions.bind_image_optimization_controls();

});

ThriveThemeOptions.set_social_display_location_value = function() {
    var _selected_posts_val = jQuery("#thrive_theme_options_social_display_posts:checked").prop('checked');
    var _selected_pages_val = jQuery("#thrive_theme_options_social_display_pages:checked").prop('checked');
    var _hidden_input_val = "";
    if (_selected_posts_val) {
        _hidden_input_val += "posts,";
    }
    if (_selected_pages_val) {
        _hidden_input_val += "pages,";
    }
    jQuery("#thrive_theme_options_social_display_hidden").val(_hidden_input_val);
};

ThriveThemeOptions.set_social_custom_type_value = function() {
    var _hidden_input_val = "";
    jQuery(".thrive_chk_social_custom_posts").each(function(index) {
        if (jQuery(this).prop("checked")) {
            _hidden_input_val += jQuery(this).val() + ",";
        }
    });
    jQuery("#thrive_hidden_social_custom_posts").val(_hidden_input_val);
};

ThriveThemeOptions.display_logo_fields = function() {
    var _selected_logo_val = jQuery("#logo_type_image:checked").val();
    if (!_selected_logo_val) {
        _selected_logo_val = jQuery("#logo_type_text:checked").val();
    }
    if (_selected_logo_val == "text") {
        jQuery("#thrive_theme_options_logo").parents("tr").hide();
        jQuery("#thrive_theme_options_logo_text").parents("tr").show();
        jQuery("#thrive_theme_options_logo_color").parents("tr").show();
    } else {
        jQuery("#thrive_theme_options_logo").parents("tr").show();
        jQuery("#thrive_theme_options_logo_text").parents("tr").hide();
        jQuery("#thrive_theme_options_logo_color").parents("tr").hide();
    }
};

ThriveThemeOptions.display_social_cta_fields = function() {
    
    var _selected_cta_val = jQuery(".radio_sel_social_attention_grabber:checked").val();
    if (_selected_cta_val == "cta") {
        jQuery("#thrive_theme_options_social_cta_text").parents("tr").show();
    } else {
        jQuery("#thrive_theme_options_social_cta_text").parents("tr").hide();
    }
    
};

ThriveThemeOptions.handle_file_upload = function(event) {
    ThriveThemeOptions.img_textfield_id = jQuery(this).attr('id').replace("_btn", "");
    event.preventDefault();
    if (file_frame) {
        file_frame.open();
        return;
    }
    file_frame = wp.media.frames.file_frame = wp.media({
        title: jQuery(this).data('uploader_title'),
        button: {
            text: jQuery(this).data('uploader_button_text')
        },
        multiple: false
    });
    file_frame.on('select', function() {
        attachment = file_frame.state().get('selection').first().toJSON();        
        jQuery("#" + ThriveThemeOptions.img_textfield_id).val(attachment.url);
    });
    file_frame.open();
};

ThriveThemeOptions.display_header_phone_options = function() {
    var _selected_cta_val = jQuery(".thrive_header_phone_chk:checked").val();
    if (_selected_cta_val == 1) {
        jQuery("#thrive_theme_options_header_phone_no").parents("tr").show();
        jQuery("#thrive_theme_options_header_phone_text").parents("tr").show();
        jQuery("#thrive_theme_options_header_phone_text_mobile").parents("tr").show();
        jQuery("#thrive_theme_options_header_phone_btn_color").parents("tr").show();
    } else {
        jQuery("#thrive_theme_options_header_phone_no").parents("tr").hide();
        jQuery("#thrive_theme_options_header_phone_text").parents("tr").hide();
        jQuery("#thrive_theme_options_header_phone_text_mobile").parents("tr").hide();
        jQuery("#thrive_theme_options_header_phone_btn_color").parents("tr").hide();
    }
};

ThriveThemeOptions.preview_optin_handler = function () {

    var postData = {
        noonce: ThriveThemeOptions.noonce,
        header_phone_no : jQuery("#thrive_theme_options_header_phone_no").val(),
        header_phone_text: jQuery("#thrive_theme_options_header_phone_text").val(),
        header_phone_text_mobile: jQuery("#thrive_theme_options_header_phone_text_mobile").val(),
        header_phone_btn_color: jQuery("#thrive_theme_options_header_phone_btn_color").val()
    };

    jQuery.post(ThriveThemeOptions.headerPhonePreviewUrl, postData, function (response) {
        if (response === 0 || response === "0") {
            alert('Something went wrong. Please refresh and try again.');
            return;
        }
        jQuery("#thrive_preview_header_phone_container").html(response);
    });
};

ThriveThemeOptions.generate_related_handler = function() {
    var postData = {
        ignore_cats : jQuery("#thrive_theme_options_related_ignore_cats").val(),
        ignore_tags : jQuery("#thrive_theme_options_related_ignore_tags").val(),
        number_posts : jQuery("#thrive_theme_options_related_number_posts").val()                
    };
    jQuery("#thrive_label_loading_generate_related_posts").show();
    jQuery.post(ThriveThemeOptions.generateRelatedUrl, postData, function(response) {
        jQuery("#thrive_label_loading_generate_related_posts").hide();
    });
};

ThriveThemeOptions.check_localStorage = function() {
    var _testLocalStorage = 'testLocalStorage';
    try {
        localStorage.setItem(_testLocalStorage, _testLocalStorage);
        localStorage.removeItem(_testLocalStorage);
        return true;
    } catch(e) {
        return false;
    }
};

ThriveThemeOptions.set_selectedItem = function(item) {
    if (!ThriveThemeOptions.check_localStorage()) {
        return;
    }
    localStorage.setItem("ThriveOptionsSelectedItem", item);
};

ThriveThemeOptions.get_selectedItem = function() {
    //check for hashtag option
    var _hash_param = window.location.hash.substr(1);
    if (_hash_param && _hash_param != "") {
        return _hash_param;
    }

    if (!ThriveThemeOptions.check_localStorage()) {
        return "general-options";
    }
    var _selectedItem = localStorage.getItem("ThriveOptionsSelectedItem");
    if (_selectedItem == "" || !_selectedItem) {
        return "general-options";
    }
    return _selectedItem;
};

ThriveThemeOptions.show_related_posts_settings = function() {
    jQuery('#thrive_theme_options_related_posts_title').parents('tr').show();
    jQuery('#thrive_settings_field_related_posts_number').parents('tr').show();
    jQuery('#related_posts_images_on').parents('tr').show();
};

ThriveThemeOptions.hide_related_posts_settings = function() {
    jQuery('#thrive_theme_options_related_posts_title').parents('tr').hide();
    jQuery('#thrive_settings_field_related_posts_number').parents('tr').hide();
    jQuery('#related_posts_images_on').parents('tr').hide();
};


ThriveThemeOptions.bind_image_optimization_controls = function() {

    jQuery("#tt-btn-resize-images").click(function(event) {
        event.preventDefault();
        if (ThriveThemeOptions.image_optimization_status == "started") {
            ThriveThemeOptions.stop_image_optimization();
        } else {
            ThriveThemeOptions.start_image_optimization();
        }
        return false;
    });

    jQuery("#tt-btn-resize-images-cancel").click(function(event) {
        event.preventDefault();
        ThriveThemeOptions.cancel_process_images();
        return false;
    });

    jQuery(".tt-img-resize-type").change(function() {
        if (jQuery(this).val() == ThriveThemeOptions.optimizeImagesTypes.default) {
            jQuery("#tt-optimization-status-msg").html(ThriveThemeOptions.optimizeImagesLabels.set_wp_default);
        } else {
            jQuery("#tt-optimization-status-msg").html(ThriveThemeOptions.optimizeImagesLabels.not_started);
        }
    });

};

ThriveThemeOptions.start_image_optimization = function() {

    ThriveThemeOptions.image_optimization_status = "started";
    ThriveThemeOptions.process_images();
    jQuery("#tt-btn-resize-images").removeClass("btn-play").addClass("btn-pause");
    jQuery(".tt-img-resize-type").prop("disabled", true);
    jQuery("#tt-btn-resize-images-cancel").show();

};

ThriveThemeOptions.stop_image_optimization = function() {

    ThriveThemeOptions.image_optimization_status = "stopped";
    jQuery("#tt-btn-resize-images").removeClass("btn-pause").addClass("btn-play");

};

ThriveThemeOptions.process_images = function () {

    if (ThriveThemeOptions.image_optimization_status != "started" || ThriveThemeOptions.img_ajax_runnning) {
        return false;
    }

    var _post_params = {
        resize_type: jQuery(".tt-img-resize-type:checked").val()
    };
    ThriveThemeOptions.img_ajax_runnning = true;

    ThriveThemeOptions.current_ajax = jQuery.post(ThriveThemeOptions.optimizeImagesUrl, _post_params, function (response) {
        ThriveThemeOptions.img_ajax_runnning = false;
        if (response.resize_type == ThriveThemeOptions.optimizeImagesTypes.default) {
            ThriveThemeOptions.stop_image_optimization();
            jQuery("#tt-optimization-status-msg").html(response.message);
            jQuery("#tt-btn-resize-images-cancel").hide();
            jQuery(".tt-img-resize-type").prop("disabled", false);
        } else {
            if (response.status == ThriveThemeOptions.optimizeImagesStatuses.finished) {
                ThriveThemeOptions.stop_image_optimization();
                jQuery("#tt-optimization-status-msg").html(ThriveThemeOptions.optimizeImagesLabels.finished);
                jQuery("#tt-btn-resize-images-cancel").hide();
                jQuery(".tt-img-resize-type").prop("disabled", false);
            } else if (response.status == ThriveThemeOptions.optimizeImagesStatuses.started) {

                if (ThriveThemeOptions.image_optimization_status == "cancelled") {
                    return false; //stop here if the process has been cancelled
                }

                jQuery("#tt-optimization-status-msg").html(ThriveThemeOptions.optimizeImagesLabels.in_progress);

                var _resize_msg = ThriveThemeOptions.optimizeImagesLabels.resized_msg + " <i>" + response.filename + "</i>";
                var _clone_list_item = jQuery("#tt-clone-li-filename").clone();
                _clone_list_item.find(".img-name").html(_resize_msg);
                _clone_list_item.show();
                jQuery("#tt-list-optimized-images").prepend(_clone_list_item);

                if (ThriveThemeOptions.image_optimization_status == "started") {
                    ThriveThemeOptions.process_images();
                }
            } else if (response.status == ThriveThemeOptions.optimizeImagesStatuses.error) {
                ThriveThemeOptions.stop_image_optimization();
                jQuery("#tt-optimization-status-msg").html(response.message);
                jQuery("#tt-btn-resize-images-cancel").hide();
            }
        }
    }).always(function (response) {
        ThriveThemeOptions.img_ajax_runnning = false;

        if (!response || response.length === 0) {
            var _clone_list_item = jQuery("#tt-clone-li-filename").clone();
            _clone_list_item.find(".img-name").html(" <i>Error resizing image: Memory limit!</i>");
            _clone_list_item.show();
            jQuery("#tt-list-optimized-images").prepend(_clone_list_item);

            ThriveThemeOptions.process_images();
        }
    });

};

ThriveThemeOptions.cancel_process_images = function () {
    var _post_params = {
        cancel_process: 1
    }

    ThriveThemeOptions.current_ajax && ThriveThemeOptions.current_ajax.abort();

    jQuery.post(ThriveThemeOptions.optimizeImagesUrl, _post_params, function (response) {
        ThriveThemeOptions.stop_image_optimization();
        jQuery("#tt-btn-resize-images-cancel").hide();
        jQuery("#tt-optimization-status-msg").html(ThriveThemeOptions.optimizeImagesLabels.not_started);
        jQuery("#tt-list-optimized-images").html("");
        ThriveThemeOptions.image_optimization_status = "cancelled";
        jQuery(".tt-img-resize-type").prop("disabled", false);
    });

};