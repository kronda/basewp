<table class="form-table postEdit">
    <tr>
        <td scope="row">
            <label><?php _e("Number of columns", 'thrive'); ?></label>
        </td>
        <td>
            <select id="thrive_shortcode_grid_no_cols">
                <option>2</option>
                <option>3</option>
                <option>4</option>
            </select>
        </td>                        
    </tr>
    <tr>
        <td scope="row">
            <label><?php _e("Image size", 'thrive'); ?></label>
        </td>
        <td>
            <select id="thrive_shortcode_grid_img_size">
                <?php foreach ($img_sizes as $key => $val): ?>
                    <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                <?php endforeach; ?>
            </select>
        </td>                        
    </tr>
    <tr>
        <td scope="row"><?php _e("Upload images", 'thrive') ?></th>
        <td>
            <div id="thrive_shortcode_grid_img1_container">
                <?php _e("Column 1", 'thrive'); ?> <input type="text" class="adminHeightInput" id="thrive_shortcode_grid_img1" />
                <input class="pure-button upload thrive_upload" type="button" id="thrive_shortcode_grid_img1_btn" value="<?php _e("Select", 'thrive'); ?>"/>
            </div>
            <br/>
            <div id="thrive_shortcode_grid_img2_container">
                <?php _e("Column 2", 'thrive'); ?> <input type="text" class="adminHeightInput" id="thrive_shortcode_grid_img2" />
                <input class="pure-button upload thrive_upload" type="button" id="thrive_shortcode_grid_img2_btn" value="<?php _e("Select", 'thrive'); ?>"/>
            </div>
            <br/>
            <div id="thrive_shortcode_grid_img3_container">
                <?php _e("Column 3", 'thrive'); ?> <input type="text" class="adminHeightInput" id="thrive_shortcode_grid_img3" />
                <input class="pure-button upload thrive_upload" type="button" id="thrive_shortcode_grid_img3_btn" value="<?php _e("Select", 'thrive'); ?>"/>
            </div>
            <br/>
            <div id="thrive_shortcode_grid_img4_container">
                <?php _e("Column 4", 'thrive'); ?> <input type="text" class="adminHeightInput" id="thrive_shortcode_grid_img4" />
                <input class="pure-button upload thrive_upload" type="button" id="thrive_shortcode_grid_img4_btn" value="<?php _e("Select", 'thrive'); ?>"/>
            </div>
            <br/>
        </td>
    </tr>    
    <tr class="thrive_shortcode_submit_container">
        <td colspan="2">
            <input class="button button-primary" type="button" id="thrive_shortcode_btn_insert" value="<?php _e("Insert", 'thrive'); ?>" />
        </td>           
    </tr>
</table>

<script type="text/javascript">

    jQuery(document).ready(function() {
        jQuery("#thrive_shortcode_grid_img3_container").hide();
        jQuery("#thrive_shortcode_grid_img4_container").hide();

        jQuery("#thrive_shortcode_grid_no_cols").change(function() {
            var _no_cols = jQuery(this).val();
            
            if (_no_cols == 2) {
                jQuery("#thrive_shortcode_grid_img3_container").hide();
                jQuery("#thrive_shortcode_grid_img4_container").hide();
            } else if (_no_cols == 3) {                
                jQuery("#thrive_shortcode_grid_img3_container").show();
                jQuery("#thrive_shortcode_grid_img4_container").hide();
            } else {
                jQuery("#thrive_shortcode_grid_img3_container").show();
                jQuery("#thrive_shortcode_grid_img4_container").show();
            }
        });
        var _current_pic = "";
        var file_frame;
        jQuery('.thrive_upload').on('click', function(event) {
            _current_pic = jQuery(this).attr('id');
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
                multiple: false  // Set to true to allow multiple files to be selected
            });

            // When an image is selected, run a callback.
            file_frame.on('select', function() {

                // We set multiple to false so only get one image from the uploader
                attachment = file_frame.state().get('selection').first().toJSON();
                jQuery("#" + _current_pic).prev("input[type='text']").val(attachment.url);
                // Do something with attachment.id and/or attachment.url here
            });
            file_frame.open();
        });

        jQuery("#thrive_shortcode_btn_insert").click(function() {

            var _no_cols = jQuery('#thrive_shortcode_grid_no_cols').val();
            var _img_size = jQuery('#thrive_shortcode_grid_img_size').val();
            var sc_text = '';
            if (_no_cols == 2) {
                sc_text = '[grid_one_half title="Your title here" img="' + jQuery('#thrive_shortcode_grid_img1').val() + '" size="' + _img_size + '"]Your Content Here[/grid_one_half]';
                sc_text += '[grid_one_half_last title="Your title here" img="' + jQuery('#thrive_shortcode_grid_img2').val() + '" size="' + _img_size + '"]Your Content Here[/grid_one_half_last]';
            } else if (_no_cols == 3) {
                sc_text = '[grid_one_third_first title="Your title here" img="' + jQuery('#thrive_shortcode_grid_img1').val() + '" size="' + _img_size + '"]Your Content Here[/grid_one_third_first]';
                sc_text += '[grid_one_third title="Your title here" img="' + jQuery('#thrive_shortcode_grid_img2').val() + '" size="' + _img_size + '"]Your Content Here[/grid_one_third]';
                sc_text += '[grid_one_third_last title="Your title here" img="' + jQuery('#thrive_shortcode_grid_img3').val() + '" size="' + _img_size + '"]Your Content Here[/grid_one_third_last]';
            } else {
                sc_text = '[grid_one_fourth_first title="Your title here" img="' + jQuery('#thrive_shortcode_grid_img1').val() + '" size="' + _img_size + '"]Your Content Here[/grid_one_fourth_first]';
                sc_text += '[grid_one_fourth title="Your title here" img="' + jQuery('#thrive_shortcode_grid_img2').val() + '" size="' + _img_size + '"]Your Content Here[/grid_one_fourth]';
                sc_text += '[grid_one_fourth title="Your title here" img="' + jQuery('#thrive_shortcode_grid_img3').val() + '" size="' + _img_size + '"]Your Content Here[/grid_one_fourth]';
                sc_text += '[grid_one_fourth_last title="Your title here" img="' + jQuery('#thrive_shortcode_grid_img4').val() + '" size="' + _img_size + '"]Your Content Here[/grid_one_fourth_last]';
            }

            send_to_editor(sc_text);

        });
    });

</script>