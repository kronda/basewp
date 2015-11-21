
jQuery(document).ready(function() {
    ThriveOptinOptions.renderFieldLabelsHandler();
    jQuery("#thrive_meta_optin_generate_fields").click(ThriveOptinOptions.renderFieldLabelsHandler);

});

ThriveOptinOptions.renderFieldLabelsHandler = function() {
    var postData = {
        noonce: ThriveOptinOptions.noonce,
        id_post: ThriveOptinOptions.id_post,
        autoresponder_code: jQuery("#thrive_meta_optin_autoresponder_code").val()
    };

    jQuery.post(ThriveOptinOptions.renderFieldsUrl, postData, function(response) {
        if (response === 0 || response === "0") {
            jQuery('#thrive_container_generated_fields').html("We couldn't parse the autoresponder code.");
            return;
        }
        jQuery('#thrive_meta_txt_message').html("");
        jQuery("#thrive_container_generated_fields").html(response);

        jQuery("#thrive_btn_save_autoresponder_fields").unbind().click(ThriveOptinOptions.saveFieldLabelsHandler);
    });

};

ThriveOptinOptions.saveFieldLabelsHandler = function() {
    var tempFieldsArray = {};
    jQuery('.thrive_option_field').each(function(i, obj) {
        var $this = jQuery(this),
            fieldName = jQuery(this).attr('data-encoded-name'),
            field = {
                name: fieldName,
                label: jQuery('#' + fieldName).val(),
                type: $this.attr('data-type')
            };

        tempFieldsArray[fieldName] = field;
    });

    var postData = {
        noonce: ThriveOptinOptions.noonce,
        id_post: ThriveOptinOptions.id_post,
        fieldsArray: tempFieldsArray
    };

    jQuery.post(ThriveOptinOptions.saveFieldsUrl, postData, function(response) {
        if (response === 0 || response === "0") {
            jQuery('#thrive_meta_txt_message').html('Something went wrong. Please refresh and try again.');
            return;
        }

        jQuery('#thrive_meta_txt_message').html('Settings saved successfully.');
        return;
    });

};