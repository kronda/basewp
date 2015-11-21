jQuery(document).ready(function() {

    jQuery("#publish, #save-post").click(function(event) {
        ThriveApprLesson.build_dld_links_json();
       // jQuery(this).parents("form").submit();
    });

    jQuery(".thrive-remove-link").click(function() {
        jQuery(this).parents(".thrive-dld-links-item").remove();
    });

    jQuery("#thrive-dld-links-btn-add").click(function() {
        var _thrive_dld_link_div = jQuery("#thrive-dld-links-clone").clone();
        _thrive_dld_link_div.attr("id", "");
        jQuery("#thrive-dld-links-container").append(_thrive_dld_link_div);
        _thrive_dld_link_div.show();
        jQuery(".thrive-remove-link").click(function() {
            jQuery(this).parents(".thrive-dld-links-item").remove();
        });
    });

    ThriveApprLesson.display_lesson_type_options();
    jQuery("#thrive_meta_appr_lesson_type").change(ThriveApprLesson.display_lesson_type_options);
    jQuery(".thrive_meta_appr_video_type").change(ThriveApprLesson.display_lesson_type_options);
    jQuery(".thrive_meta_appr_audio_type").change(ThriveApprLesson.display_lesson_type_options);
    
    jQuery('#btn_thrive_appr_select_gallery').on('click', ThriveApprLesson.handle_file_upload);

    jQuery("#thrive_btn_appr_select_audio_file").on('click', ThriveApprLesson.handle_audio_file_upload);

    jQuery("#tt-link-add-lesson-cat").click(ThriveApprLesson.addNewCatHandler);

    //hide the default category block
    jQuery("#apprenticediv").hide();

});

ThriveApprLesson.build_dld_links_json = function() {
    var _dld_links_obj = [];
    jQuery("#thrive-dld-links-container").find(".thrive-dld-links-item").each(function(index) {
        var _temp_item = {
            icon: jQuery(this).find(".thrive-sel-icon").val(),
            link_url: jQuery(this).find(".thrive-txt-link-url").val(),
            link_text: jQuery(this).find(".thrive-txt-link-text").val(),
            new_tab: jQuery(this).find(".thrive-txt-link-new-tab").prop('checked') ? 1 : 0
        };
        _dld_links_obj.push(_temp_item);
    });
    jQuery("#thrive_meta_appr_download_links").val(JSON.stringify(_dld_links_obj));
};

ThriveApprLesson.display_lesson_type_options = function() {
    var _selected_format = jQuery("#thrive_meta_appr_lesson_type").val();

    if (_selected_format == "audio") {
        var _selected_audio_type = jQuery(".thrive_meta_appr_audio_type:checked").val();
        if (_selected_audio_type == "file") {
            jQuery("#tr_thrive_appr_audio_file").show();
            jQuery("#tr_thrive_appr_audio_soundcould").hide();
        } else {
            jQuery("#tr_thrive_appr_audio_file").hide();
            jQuery("#tr_thrive_appr_audio_soundcould").show();
        }

        jQuery("#thrive_appr_audio_options").show();
        jQuery("#thrive_appr_video_options").hide();
        jQuery("#thrive_appr_quote_options").hide();
        jQuery("#thrive_appr_gallery_options").hide();
        jQuery("#thrive_appr_options").show();
    } else if (_selected_format == "video") {
        jQuery("#thrive_appr_audio_options").hide();
        jQuery("#thrive_appr_video_options").show();
        jQuery("#thrive_appr_quote_options").hide();
        jQuery("#thrive_appr_gallery_options").hide();
        jQuery("#thrive_appr_options").show();

        var _selected_video_type = jQuery(".thrive_meta_appr_video_type:checked").val();
        if (_selected_video_type == "vimeo") {
            jQuery(".thrive_shortcode_container_video_vimeo").show();
            jQuery(".thrive_shortcode_container_video_youtube").hide();
            jQuery(".thrive_shortcode_container_video_custom_embed").hide();
            jQuery(".thrive_shortcode_container_video_custom").hide();
        } else if (_selected_video_type == "custom") {
            jQuery(".thrive_shortcode_container_video_youtube").hide();
            jQuery(".thrive_shortcode_container_video_vimeo").hide();
            jQuery(".thrive_shortcode_container_video_custom").show();
            jQuery(".thrive_shortcode_container_video_custom_embed").hide();
        } else if (_selected_video_type == "custom_embed") {
            jQuery(".thrive_shortcode_container_video_youtube").hide();
            jQuery(".thrive_shortcode_container_video_vimeo").hide();
            jQuery(".thrive_shortcode_container_video_custom").hide();
            jQuery(".thrive_shortcode_container_video_custom_embed").show();
        }  else {
            jQuery(".thrive_shortcode_container_video_vimeo").hide();
            jQuery(".thrive_shortcode_container_video_custom").hide();
            jQuery(".thrive_shortcode_container_video_custom_embed").hide();
            jQuery(".thrive_shortcode_container_video_youtube").show();
        }

    } else if (_selected_format == "gallery") {
        jQuery("#thrive_appr_audio_options").hide();
        jQuery("#thrive_appr_video_options").hide();
        jQuery("#thrive_appr_quote_options").hide();
        jQuery("#thrive_appr_gallery_options").show();
        jQuery("#thrive_appr_options").show();
    } else {
        jQuery("#thrive_appr_options").hide();
    }


};



//deal with the file upload
var file_frame;
ThriveApprLesson.handle_file_upload = function(event) {
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
        multiple: true  // Set to true to allow multiple files to be selected
    });

    file_frame.on('open', function() {
        var selection = file_frame.state().get('selection');
        ids = jQuery('#thrive_meta_appr_gallery_images').val().split(',');
        ids.forEach(function(id) {
            attachment = wp.media.attachment(id);
            attachment.fetch();
            selection.add(attachment ? [attachment] : []);
        });
    });

    // When an image is selected, run a callback.
    file_frame.on('select', function() {

        var _thrive_gallery_ids = "";
        jQuery("#thrive_container_appr_gallery_list").html("");
        file_frame.state().get('selection').each(function(element) {
            if (element.attributes.id != "") {
                _thrive_gallery_ids += element.attributes.id + ",";
                jQuery("#thrive_container_appr_gallery_list").append("<img class='thrive-gallery-thumb' src='" + element.attributes.url + "' width='50' height='50' />");
            }
        });
        jQuery("#thrive_meta_appr_gallery_images").val(_thrive_gallery_ids);
    });
    file_frame.open();
};

var file_frame_audio;
ThriveApprLesson.handle_audio_file_upload = function(event) {
    event.preventDefault();
    if (file_frame_audio) {
        file_frame_audio.open();
        return;
    }
    file_frame_audio = wp.media.frames.file_frame_audio = wp.media({
        title: jQuery(this).data('uploader_title'),
        button: {
            text: jQuery(this).data('uploader_button_text'),
        },
        library: {
            type: 'audio'
        },
        multiple: false
    });
    file_frame_audio.on('select', function() {
        attachment = file_frame_audio.state().get('selection').first().toJSON();
        jQuery("#thrive_meta_appr_audio_file").val(attachment.url);
    });
    file_frame_audio.open();
};

ThriveApprLesson.addNewCatHandler = function () {
    var _cat_name = jQuery("#tt-txt-add-lesson-cat").val();

    if (_cat_name === "") {
        alert("Please insert a title for the category");
        return false;
    }

    var _lessonsLevel = jQuery("#tt-hidden-lessons-level").val();
    var _parent_id = 0;
    if (_lessonsLevel > 1 && jQuery("#tt-sel-appr-parent-lesson-cat").length > 0) {
        _parent_id = jQuery("#tt-sel-appr-parent-lesson-cat").val();

        if (_parent_id == 0) {
            alert("Please select a parent category or manage your content from the 'Manage Contents' section!");
            return false;
        }
    }

    var postData = {
        noonce: ThriveApprLesson.noonce,
        cat_name: _cat_name,
        cat_slug: _cat_name,
        cat_description: "",
        parent_id: _parent_id
    };

    jQuery.post(ThriveApprLesson.addNewCatUrl, postData, function (response) {
        if (response === 0 || response === "0") {
            alert('Something went wrong. Please refresh and try again.');
            return false;
        } else {
            var _new_cat_option = "<option value='"+ response + "'>"+ _cat_name + "</option>";
            jQuery("#tt-sel-appr-lesson-cat").append(_new_cat_option);
            jQuery("#tt-sel-appr-lesson-cat").val(response);
            jQuery("#tt-txt-add-lesson-cat").val("");

            jQuery("#tt-label-cat-added").show().delay(3000).fadeOut();
        }

    });

    return false;
};