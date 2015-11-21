var ThriveApprOptions = {};
jQuery(document).ready(function() {
    jQuery('#thrive_theme_options_appr_logo_btn').on('click', ThriveThemeOptions.handle_file_upload);

    //apprentice features
    jQuery("#theme_options_enable_apprentice").click(function() {
        jQuery("#theme_options_hidden_appr_enable_feature").val(1);
        jQuery("#tt-submit-button").trigger("click");
    });

    jQuery("#theme_options_disable_apprentice").click(function() {
        jQuery("#theme_options_hidden_appr_enable_feature").val(0);
        jQuery("#tt-submit-button").trigger("click");
    });

    jQuery("#appr_logo_type_image").click(ThriveApprOptions.display_appr_logo_fields);
    jQuery("#appr_logo_type_text").click(ThriveApprOptions.display_appr_logo_fields);
    jQuery("#appr_different_logo_on").click(ThriveApprOptions.display_appr_logo_fields);
    jQuery("#appr_different_logo_off").click(ThriveApprOptions.display_appr_logo_fields);
    ThriveApprOptions.display_appr_logo_fields();
});


ThriveApprOptions.display_appr_logo_fields = function() {
    var _use_different_logo = jQuery("#appr_different_logo_on:checked").val();

    if (!_use_different_logo) {
        jQuery("#appr_logo_position_image").parents("tr").hide();
        jQuery("#thrive_theme_options_appr_logo").parents("tr").hide();
        jQuery("#thrive_theme_options_appr_logo_text").parents("tr").hide();
        jQuery("#thrive_theme_options_appr_logo_color").parents("tr").hide();
        jQuery("#appr_logo_type_image").parents("tr").hide();
    } else {
        jQuery("#appr_logo_type_image").parents("tr").show();
        var _selected_appr_logo_val = jQuery("#appr_logo_type_image:checked").val();
        if (!_selected_appr_logo_val) {
            _selected_appr_logo_val = jQuery("#appr_logo_type_text:checked").val();
        }
        if (_selected_appr_logo_val == "text") {
            jQuery("#thrive_theme_options_appr_logo").parents("tr").hide();
            jQuery("#thrive_theme_options_appr_logo_text").parents("tr").show();
            jQuery("#thrive_theme_options_appr_logo_color").parents("tr").show();
        } else {
            jQuery("#thrive_theme_options_appr_logo").parents("tr").show();
            jQuery("#thrive_theme_options_appr_logo_text").parents("tr").hide();
            jQuery("#thrive_theme_options_appr_logo_color").parents("tr").hide();
        }
    }

};
