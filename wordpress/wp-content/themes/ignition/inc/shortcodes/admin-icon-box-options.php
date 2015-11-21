<table class="form-table postEdit">
    <tr>
        <th scope="row">
            <label><?php _e("Style", 'thrive'); ?></label>
        </th>
        <td>
            <select id="thrive_shortcode_style">
                <option value="1">With border</option>
                <option value="2">Without border</option>
            </select>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Color", 'thrive'); ?></label>
        </th>
        <td>
            <select id="thrive_shortcode_option_color">
                <?php foreach ($all_colors as $key => $c): ?>
                    <option value="<?php echo $key; ?>"><?php echo $c; ?></option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Image", 'thrive'); ?></label>
        </th>
        <td>
            <input type="text" class="icon_box_image_upload"/>
            <input type="button" class="pure-button upload" id="icon_box_image_upload_btn" value="<?php _e("Upload", 'thrive'); ?>"/>
            <input type="button" class="pure-button remove" id="icon_box_image_upload_remove_btn" value="<?php _e("Remove", 'thrive'); ?>"/>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Box content", 'thrive'); ?></label>
        </th>
        <td>
            <textarea id="thrive_shortcode_content"></textarea>
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
        jQuery("#thrive_shortcode_btn_insert").click(function () {

            var sc_options = {
                'color': jQuery("#thrive_shortcode_option_color").val(),
                'style': jQuery("#thrive_shortcode_style").val(),
                'image': jQuery(this).parents(".postEdit").find(".icon_box_image_upload").val(),
                'content': jQuery("#thrive_shortcode_content").val()
            };
            tb_remove();

            var sc_text = "[thrive_icon_box color='" + sc_options.color + "' style='" + sc_options.style + "' image='" + sc_options.image + "']" + sc_options.content + "[/thrive_icon_box]";

            send_to_editor(sc_text);


        });

        jQuery(document).on('click', '#icon_box_image_upload_btn', function (e) {
            if (!window.Icon_Box_Uploader) {
                e.preventDefault();
                //Extend the wp.media object
                window.Icon_Box_Uploader = wp.media.frames.file_frame = wp.media({
                    title: 'Choose Image',
                    button: {
                        text: 'Choose Image'
                    },
                    multiple: false
                });

                //When a file is selected, grab the URL and set it as the text field's value
                window.Icon_Box_Uploader.on('select', function () {
                    var attachment = window.Icon_Box_Uploader.state().get('selection').first().toJSON();
                    jQuery('.icon_box_image_upload').val(attachment.url);
                    window.Icon_Box_Uploader.close();
                    return;
                });

                //Open the uploader dialog
                window.Icon_Box_Uploader.open();
            } else {
                window.Icon_Box_Uploader.open();
            }
        });

        jQuery('#icon_box_image_upload_remove_btn').click(function () {
            jQuery('.icon_box_image_upload').val('');
        });

    });

</script>