<table class="form-table postEdit">
    <tr>
        <th scope="row">
            <label><?php _e("Heading", 'thrive'); ?></label>
        </th>
        <td>
            <input type="text" id="custom_box_heading" placeholder="title">
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Text Style", 'thrive'); ?></label>
        </th>
        <td>
            <label>
                <input checked="" value="light" type="radio" name="custom_box_text_style"/>
                Light
            </label>
            <label>
                <input value="dark" type="radio" name="custom_box_text_style"/>
                Dark
            </label>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Background", 'thrive'); ?></label>
        </th>
        <td>
            <label>
                <input checked="" value="image" type="radio" name="custom_box_box_background"/>
                Image
            </label>
            <label>
                <input value="color" type="radio" name="custom_box_box_background"/>
                Color
            </label>
        </td>
    </tr>
    <tr class="color-group">
        <th scope="row">
            <label><?php _e("Background Color", 'thrive'); ?></label>
        </th>
        <td>
            <input type="text" id="custom_box_background_color">
        </td>
    <tr class="color-group">
        <th scope="row">
            <label><?php _e("Border Color", 'thrive'); ?></label>
        </th>
        <td>
            <input type="text" id="custom_box_border_color">
        </td>
    </tr>
    <tr class="image-group">
        <th scope="row">
            <label><?php _e("Upload Image", 'thrive'); ?></label>
        </th>
        <td>
            <input type="text" class="custom_box_image_upload">
            <input type="button" class="pure-button upload" id="custom_box_image_upload_btn" value="<?php _e("Upload", 'thrive'); ?>"/>
            <input type="button" class="pure-button remove" id="custom_box_image_upload_remove_btn" value="<?php _e("Remove", 'thrive'); ?>"/>
        </td>
    </tr>
    <tr class="image-group">
        <th scope="row">
            <label><?php _e("Full Image Height", 'thrive'); ?></label>
        </th>
        <td>
            <input type="checkbox" id="custom_box_full_image_height">
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <input class="button button-primary" type="button" id="thrive_shortcode_btn_insert"
                   value="<?php _e("Insert", 'thrive'); ?>"/>
        </td>
    </tr>
</table>
<script type="text/javascript">
    jQuery(document).ready(function () {

        jQuery('#custom_box_background_color').wpColorPicker();
        jQuery('#custom_box_border_color').wpColorPicker();
        jQuery('.color-group').hide();

        jQuery('input[name=custom_box_box_background]').change(function () {
            if (jQuery(this).val() == 'image') {
                jQuery('.color-group').hide();
                jQuery('.image-group').show();
            }
            if (jQuery(this).val() == 'color') {
                jQuery('.color-group').show();
                jQuery('.image-group').hide();
            }
        });

        jQuery(document).on('click', '#custom_box_image_upload_btn', function (e) {
            if (!window.Custom_Box_Uploader) {
                e.preventDefault();
                //Extend the wp.media object
                window.Custom_Box_Uploader = wp.media.frames.file_frame = wp.media({
                    title: 'Choose Image',
                    button: {
                        text: 'Choose Image'
                    },
                    multiple: false
                });

                //When a file is selected, grab the URL and set it as the text field's value
                window.Custom_Box_Uploader.on('select', function () {
                    var attachment = window.Custom_Box_Uploader.state().get('selection').first().toJSON();
                    jQuery('.custom_box_image_upload').val(attachment.url);
                    window.Custom_Box_Uploader.close();
                    return;
                });

                //Open the uploader dialog
                window.Custom_Box_Uploader.open();
            } else {
                window.Custom_Box_Uploader.open();
            }
        });

        jQuery('#custom_box_image_upload_remove_btn').click(function () {
            jQuery('.custom_box_image_upload').val('');
        });

        jQuery("#thrive_shortcode_btn_insert").click(function () {

            var sc_options = {
                'title': jQuery("#custom_box_heading").val().replace(/"/g, '\''),
                'style': jQuery('input[name=custom_box_text_style]:checked').val(),
                'type': jQuery("input[name=custom_box_box_background]:checked").val(),
                'image': jQuery(".custom_box_image_upload").val(),
                'image_full': jQuery("#custom_box_full_image_height").is(':checked') ? '1' : '0',
                'color': jQuery("#custom_box_background_color").val(),
                'border': jQuery("#custom_box_border_color").val()
            };
            tb_remove();

            var sc_text = '[thrive_custom_box title="' + sc_options.title + '" style="' + sc_options.style + '" type="' + sc_options.type + '" ';
            if (sc_options.type == "image") {
                sc_text += 'image="' + sc_options.image + '" full_height=' + sc_options.image_full + ']';
            } else if (sc_options.type == "color") {
                sc_text += 'color="' + sc_options.color + '" border="' + sc_options.border + '"]';
            }
            sc_text += '[/thrive_custom_box]';

            send_to_editor(sc_text);

        });
    })
    ;

</script>