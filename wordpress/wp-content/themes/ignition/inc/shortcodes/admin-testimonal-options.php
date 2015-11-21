<table class="form-table postEdit">
    <tr>
        <th scope="row">
            <label><?php _e("Testimonal content", 'thrive'); ?></label>
        </th>
        <td>
            <textarea id="thrive_shortcode_option_content"></textarea>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Name", 'thrive'); ?></label>
        </th>
        <td>
            <input type="text" id="thrive_shortcode_option_name"/>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Company", 'thrive'); ?></label>
        </th>
        <td>
            <input type="text" id="thrive_shortcode_option_company"/>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Image", 'thrive'); ?></label>
        </th>
        <td>
            <input type="text" class="adminHeightInput" id="thrive_shortcode_option_image" name="thrive_shortcode_option_image"/>
            <input type="button" class="thrive_upload pure-button upload" id="thrive_shortcode_option_image_btn" value="<?php _e("Upload", 'thrive'); ?>"/>
            <input type="button" class="pure-button remove" id="thrive_shortcode_option_remove_image_btn" value="<?php _e("Remove", 'thrive'); ?>"/>
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <input class="button button-primary" type="button" id="thrive_shortcode_btn_insert" value="<?php _e("Insert", 'thrive'); ?>"/>
        </td>
    </tr>
</table>

<script type="text/javascript">

    jQuery(document).ready(function () {

        jQuery(document).on('click', '.thrive_upload', function (e) {
            if (!window.Testimonial_Uploader) {
                e.preventDefault();
                //Extend the wp.media object
                window.Testimonial_Uploader = wp.media.frames.file_frame = wp.media({
                    title: jQuery(this).data('uploader_title'),
                    button: {
                        text: jQuery(this).data('uploader_button_text')
                    },
                    multiple: false  // Set to true to allow multiple files to be selected
                });

                //When a file is selected, grab the URL and set it as the text field's value
                window.Testimonial_Uploader.on('select', function () {
                    var attachment = window.Testimonial_Uploader.state().get('selection').first().toJSON();
                    jQuery(".thrive_upload:visible").prev("input[type='text']").val(attachment.url);
                    window.Testimonial_Uploader.close();
                    return;
                });

                //Open the uploader dialog
                window.Testimonial_Uploader.open();
            } else {
                window.Testimonial_Uploader.open();
            }
        });

        jQuery('#thrive_shortcode_option_remove_image_btn').click(function () {
            jQuery('#thrive_shortcode_option_image').val('');
        });

        jQuery("#thrive_shortcode_btn_insert").click(function () {

            var sc_options = {
                "content": jQuery('#thrive_shortcode_option_content').val(),
                "name": jQuery('#thrive_shortcode_option_name').val().replace(/"/g, '\''),
                "company": jQuery('#thrive_shortcode_option_company').val().replace(/"/g, '\''),
                "image": jQuery('#thrive_shortcode_option_image').val()
            };
            tb_remove();

            var sc_text = '[thrive_testimonial name="' + sc_options.name + '" company="' + sc_options.company + '" image="' + sc_options.image + '"]' + sc_options.content + '[/thrive_testimonial]';

            send_to_editor(sc_text);
        });
    });

</script>