<style>
<?php foreach ($patterns as $pat): ?>
    <?php echo "#" . $pat . "{ background-image: url('" . get_template_directory_uri() . "/images/patterns/" . $pat . ".png');}"; ?>
<?php endforeach; ?>
</style>
<br/>
<div id="thrive_shortcode_sel_template_container">
    <div class="sc_select_page_section_tpl cpt selPattern" rel="custom">
    </div>
    <div class="sc_select_page_section_tpl pattern1" rel="1"></div>
    <div class="sc_select_page_section_tpl pattern2" rel="2"></div>
    <div class="sc_select_page_section_tpl pattern3" rel="3"></div>
    <div style="clear: both;"></div>
    <input type="hidden" value="custom" id="thrive_shortcode_hidden_template_option"
           name="thrive_shortcode_hidden_template_option"/>
</div>
<br/><br/>
<table id="thrive_shortcode_options_container" class="form-table postEdit">
    <tr>
        <th scope="row">
            <label><?php _e("Background type", 'thrive');?></label>
        </th>
        <td>
            <input type="radio" name="thrive_shortcode_bg_type" class="thrive_shortcode_bg_type" value="solid" checked/>
            <?php _e("Solid", 'thrive');?>
            <input type="radio" name="thrive_shortcode_bg_type" class="thrive_shortcode_bg_type" value="image"/> <?php _e("Image", 'thrive');?>
            <input type="radio" name="thrive_shortcode_bg_type" class="thrive_shortcode_bg_type" value="pattern"/> <?php _e("Pattern", 'thrive');?>
        </td>
    </tr>
    <tr id="thrive_shortcode_container_bg_solid">
        <th scope="row">
            <label><?php _e("Color", 'thrive');?></label>
        </th>
        <td>
            <input type="text" value="#2c3e50" class="thrive-color-field" data-default-color="#2c3e50"
                   id="thrive_shortcode_option_color"/>
        </td>
    </tr>
    
    <tr id="thrive_shortcode_container_bg_image" style="display: none;">        
        <td colspan="2">
            <input type="text" class="adminHeightInput" id="thrive_shortcode_option_image" name="thrive_shortcode_option_image"/>
            <input type="button" class="thrive_upload pure-button upload" id="thrive_shortcode_option_image_btn" value="<?php _e("Upload image", 'thrive');?>"/>
            <input type="button" class="pure-button remove" id="thrive_shortcode_option_remove_image_btn" value="<?php _e("Remove", 'thrive');?>"/> <br/><br/>

            <input type="radio" class="radio_img_static" value="default" name="radio_img_static" checked /> <?php _e("Default", 'thrive');?>
            <input type="radio" class="radio_img_static" value="static" name="radio_img_static"  /> <?php _e("Static image", 'thrive');?>
            <br/><br/>
            <?php _e("Show full image height", 'thrive');?>
            <input type="radio" class="radio_img_fullheight" value="off" name="radio_img_fullheight" checked /> <?php _e("Off", 'thrive');?>
            <input type="radio" class="radio_img_fullheight" value="on" name="radio_img_fullheight"  /> <?php _e("On", 'thrive');?>
        </td>
    </tr>

    <tr id="thrive_shortcode_container_bg_pattern" style="display: none;">
        <th scope="row">
            <label><?php _e("Pattern", 'thrive');?></label>
        </th>
        <td>
            <input type="text" class="adminHeightInput" id="thrive_shortcode_option_pattern"/>
            <input type="button" class="thrive_upload pure-button upload" id="thrive_shortcode_option_pattern_btn" value="<?php _e("Upload", 'thrive');?>"/>
            <input type="button" class="pure-button remove"id="thrive_shortcode_option_remove_pattern_btn" value="<?php _e("Remove", 'thrive');?>"/>
            <br/>

            <div class="patternSelect">
                <p><?php _e("Select pattern", 'thrive');?></p>

                <div class="defaultPattern">
                    <span></span>
                    <a href="" id="showPattern"></a>

                    <div style="clear: both;"></div>
                </div>
                <ul class="patternList" style="display: none;">
                    <?php foreach ($patterns as $pat): ?>
                        <li>
                            <a href="" id="<?php echo $pat; ?>"></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </td>
    </tr>        
    <tr>
        <th>
            <label><?php _e("Text style", 'thrive');?></label>
        </th>
        <td>
            <select id="thrive_shortcode_option_text_style">
                <option value="light"><?php _e("Light", 'thrive');?></option>
                <option value="dark"><?php _e("Dark", 'thrive');?></option>
            </select>
        </td>
    </tr>    
</table>
<table class="form-table postEdit">
    <tr id="thrive_shortcode_container_bg_shadow">
        <th scope="row">
            <label><?php _e("Shadow", 'thrive');?></label>
        </th>
        <td>
            <input type="text" value="" class="thrive-color-field" data-default-color=""
                   id="thrive_shortcode_option_shadow"/>
        </td>
    </tr>
    <tr>
        <th>
            <label><?php _e("Add padding", 'thrive');?></label>
        </th>
        <td>
            <input type="checkbox" id="chk_add_padding_top" value="top" /> <?php _e("Top", 'thrive'); ?>
            <input type="checkbox" id="chk_add_padding_bottom" value="bottom" /> <?php _e("Bottom", 'thrive'); ?>
        </td>
    </tr>
    <tr>
        <td colspan="2">
            <input type="radio" name="thrive_shortcode_position" class="thrive_shortcode_position"
                   value="default" checked/> <?php _e("Default", 'thrive'); ?>
            <input type="radio" name="thrive_shortcode_position" class="thrive_shortcode_position"
                   value="top"/> <?php _e("Top of the page", 'thrive'); ?>
            <input type="radio" name="thrive_shortcode_position" class="thrive_shortcode_position"
                   value="bottom"/> <?php _e("Bottom of the page", 'thrive'); ?>
        </td>
    </tr>
</table>
<br/>
<input class="button button-primary" type="button" id="thrive_shortcode_btn_insert" value="<?php _e("Insert", 'thrive');?>"/>

<script type="text/javascript">
    jQuery(document).ready(function() {
        // Uploading files
        var file_frame;
        var tt_img_textfield_id = "";
        jQuery('.thrive_upload').on('click', function(event) {
            tt_img_textfield_id = jQuery(this).attr('id').replace("_btn", "");
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
                multiple: false  // Set to true to allow multiple files to be selected
            });

            // When an image is selected, run a callback.
            file_frame.on('select', function() {

                // We set multiple to false so only get one image from the uploader
                attachment = file_frame.state().get('selection').first().toJSON();
                //jQuery(".thrive_upload:visible").prev("input[type='text']").val(attachment.url);
                jQuery("#" + tt_img_textfield_id).val(attachment.url);
                // Do something with attachment.id and/or attachment.url here
            });
            file_frame.open();
        });

        jQuery("#thrive_shortcode_option_color").wpColorPicker();
        jQuery("#thrive_shortcode_option_shadow").wpColorPicker();

        jQuery(".sc_select_page_section_tpl").click(function() {
            var tpl_name = jQuery(this).attr('rel');
            if (tpl_name === "custom") {
                jQuery("#thrive_shortcode_options_container").show();
            } else {
                jQuery("#thrive_shortcode_options_container").hide();
            }
            jQuery("#thrive_shortcode_hidden_template_option").val(tpl_name);
            jQuery(this).addClass('selPattern');
            jQuery(this).siblings('.sc_select_page_section_tpl').removeClass('selPattern');
        });

        jQuery(".thrive_shortcode_bg_type").click(function() {
            if (jQuery(this).val() === "solid") {
                jQuery("#thrive_shortcode_container_bg_solid").show();
                jQuery("#thrive_shortcode_container_bg_image").hide();
                jQuery("#thrive_shortcode_container_bg_pattern").hide();
            } else if (jQuery(this).val() === "image") {
                jQuery("#thrive_shortcode_container_bg_image").show();
                jQuery("#thrive_shortcode_container_bg_solid").hide();
                jQuery("#thrive_shortcode_container_bg_pattern").hide();
            } else {
                jQuery("#thrive_shortcode_container_bg_pattern").show();
                jQuery("#thrive_shortcode_container_bg_solid").hide();
                jQuery("#thrive_shortcode_container_bg_image").hide();
            }
        });

        jQuery("#thrive_shortcode_btn_insert").click(function() {

            var sc_options = {
                'color': jQuery("#thrive_shortcode_option_color").val(),
                'shadow': jQuery("#thrive_shortcode_option_shadow").val(),
                'image': jQuery("#thrive_shortcode_option_image").val(),
                'pattern': jQuery("#thrive_shortcode_option_pattern").val(),
                'textstyle': jQuery("#thrive_shortcode_option_text_style").val(),
                'template': jQuery("#thrive_shortcode_hidden_template_option").val(),
                'position': jQuery(".thrive_shortcode_position:checked").val(),
                'padding_top' : jQuery("#chk_add_padding_top").prop('checked'),
                'padding_bottom' : jQuery("#chk_add_padding_bottom").prop('checked'),
                'img_static': jQuery(".radio_img_static:checked").val(),
                'img_fullheight': jQuery(".radio_img_fullheight:checked").val()
            };

            var bg_type = jQuery(".thrive_shortcode_bg_type:checked").val();
            var sc_text = "";
            var sc_shadow_txt = "";
            var sc_padding_top_txt = "";
            var sc_padding_bottom_txt = "";
            var sc_img_static_txt = "";
            var sc_img_fullheight_txt = "";
            if (sc_options.padding_top) {
                sc_padding_top_txt = " padding_top='on'";
            }
            if (sc_options.padding_bottom) {
                sc_padding_bottom_txt = " padding_bottom='on'";
            }
            if (sc_options.shadow != "") {
                sc_shadow_txt = " shadow='" + sc_options.shadow + "'";
            }
            if (sc_options.img_static != "default") {
                sc_img_static_txt = " img_static='on'";
            }
            if (sc_options.img_fullheight != "off") {
                sc_img_fullheight_txt = " fullheight='on'";
            }
            switch (bg_type) {
                case 'solid':
                    sc_text = "[page_section color='" + sc_options.color + "' textstyle='" + sc_options.textstyle + "' position='" + sc_options.position + "'"+ sc_shadow_txt + sc_padding_bottom_txt + sc_padding_top_txt + "] [/page_section]";
                    break;
                case 'image':
                    var sc_image_attrs = " image='" + sc_options.image + "' ";
                    sc_text = "[page_section" + sc_image_attrs + "textstyle='" + sc_options.textstyle + "' position='" + sc_options.position + "'"+ sc_shadow_txt + sc_padding_bottom_txt + sc_padding_top_txt + sc_img_static_txt + sc_img_fullheight_txt + "] [/page_section]";
                    break;
                case 'pattern':
                    sc_text = "[page_section pattern='" + sc_options.pattern + "' textstyle='" + sc_options.textstyle + "' position='" + sc_options.position + "'"+ sc_shadow_txt + sc_padding_bottom_txt + sc_padding_top_txt + "] [/page_section]";
                    break;
            }
            ;

            if (sc_options.template !== "custom") {
                sc_text = "[page_section template='" + sc_options.template + "' position='" + sc_options.position + "'"+ sc_shadow_txt + sc_padding_bottom_txt + sc_padding_top_txt + "] [/page_section]";
            }

            tb_remove();

            send_to_editor(sc_text);


        });

        var firstPattern = jQuery('.patternList').find('li a').first().css('background-image');
        jQuery('.defaultPattern span').css('background-image', firstPattern);
        jQuery('.patternList li a').each(function() {
            jQuery(this).click(function() {
                var imageSource = jQuery(this).css('background-image');
                jQuery('.defaultPattern span').css('background-image', imageSource);
                jQuery('.patternList').hide();
                var temp_pic_url = ThriveThemeUrl + "/images/patterns/" + jQuery(this).attr('id') + ".png";
                jQuery('#thrive_shortcode_option_pattern').val(temp_pic_url);
                return false;
            });
        });
        jQuery('#showPattern').click(function() {
            jQuery('.patternList').toggle();
            return false;
        });

        jQuery("#thrive_shortcode_option_remove_pattern_btn").click(function() {
            jQuery("#thrive_shortcode_option_pattern").val("");
        });
        jQuery("#thrive_shortcode_option_remove_image_btn").click(function() {
            jQuery("#thrive_shortcode_option_image").val("");
        });

    });

</script>