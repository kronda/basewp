<style>
<?php foreach ($patterns as $pat): ?>
    <?php echo "#" . $pat . "{ background-image: url('" . get_template_directory_uri() . "/images/patterns/" . $pat . ".png');}"; ?>
<?php endforeach; ?>
</style>
<table class="form-table postEdit">
    <tr class="thrive_shortcode_container_video">
        <td colspan="2">
            <table class="form-table postEdit">
                <tr>
                    <td scope="row" colspan="2">
                        <input type="radio" name="thrive_shortcode_borderless_video_type" class="thrive_shortcode_borderless_video_type"
                               value="youtube" checked/> Youtube
                        <input type="radio" name="thrive_shortcode_borderless_video_type" class="thrive_shortcode_borderless_video_type"
                               value="vimeo"/> Vimeo
                        <input type="radio" name="thrive_shortcode_borderless_video_type" class="thrive_shortcode_borderless_video_type"
                               value="custom"/> <?php _e("Custom", 'thrive'); ?>
                    </td>
                </tr>
                <tr class="thrive_shortcode_container_video_youtube">
                    <td>
                        <?php _e("Video Url", 'thrive'); ?>
                    </td>
                    <td>
                        <input class="adminWidthInput" type="text" id="thrive_shortcode_borderless_youtube_url"/>
                    </td>
                </tr>
                <tr class="thrive_shortcode_container_video_youtube noBorder">
                    <td>
                        <?php _e("Options", 'thrive'); ?>
                    </td>
                    <td>
                        <input type="checkbox" name="thrive_shortcode_borderless_youtube_hide"
                               id="thrive_shortcode_borderless_youtube_hide_related" value="1"/> <?php _e("Hide related videos", 'thrive'); ?> <br/>
                        <input type="checkbox" name="thrive_shortcode_borderless_youtube_hide_logo"
                               id="thrive_shortcode_borderless_youtube_hide_logo" value="1"/> <?php _e("Auto-hide Youtube logo", 'thrive'); ?> <br/>
                        <input type="checkbox" name="thrive_shortcode_borderless_youtube_hide_controls"
                               id="thrive_shortcode_borderless_youtube_hide_controls" value="1"/> <?php _e("Auto-hide player controls", 'thrive'); ?> <br/>
                        <input type="checkbox" name="thrive_shortcode_borderless_youtube_hide_title"
                               id="thrive_shortcode_borderless_youtube_hide_title" value="1"/> <?php _e("Hide video title bar", 'thrive'); ?> <br/>
                        <input type="checkbox" name="thrive_shortcode_borderless_youtube_hide_fullscreen"
                               id="thrive_shortcode_borderless_youtube_hide_fullscreen" value="1"/> <?php _e("Hide full-screen button", 'thrive'); ?>
                    </td>
                </tr>
                <tr class="thrive_shortcode_container_video_vimeo" style="display: none;">
                    <td>
                        <?php _e("Video Url", 'thrive'); ?>:
                    </td>
                    <td>
                        <input class="adminWidthInput" type="text" id="thrive_shortcode_borderless_vimeo_url"/>
                    </td>
                </tr>
                <tr class="thrive_shortcode_container_video_custom" style="display: none;">
                    <td>
                        <?php _e("Video Embed Code", 'thrive'); ?>:
                    </td>
                    <td>
                        <textarea class="adminWidthInput" id="thrive_shortcode_borderless_custom_code"></textarea>
                    </td>
                </tr>
                <tr class="thrive_shortcode_container_video_custom" style="display: none;">
                    <td>
                        <?php _e("Video Custom Url", 'thrive'); ?>: <br/>
                    </td>
                    <td>
                        <input class="adminWidthInput" type="text" id="thrive_shortcode_borderless_custom_url"/>
                    </td>
                </tr>                
            </table>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Maximum video width", 'thrive'); ?></label>
        </th>
        <td>
            <input type="text" id="thrive_shortcode_option_video_width" value="1080"/>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Heading text", 'thrive'); ?></label>
        </th>
        <td>
            <input type="text" id="thrive_shortcode_option_heading_text"/>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Subheading text", 'thrive'); ?></label>
        </th>
        <td>
            <input type="text" id="thrive_shortcode_option_subheading_text"/>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Call to action text", 'thrive'); ?></label>
        </th>
        <td>
            <input type="text" id="thrive_shortcode_option_cta_text"/>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Text and play button color", 'thrive'); ?></label>
        </th>
        <td>
            <select id="thrive_shortcode_option_btn_color">
                <option value='light'><?php _e("Light", 'thrive'); ?></option>
                <option value='dark'><?php _e("Dark", 'thrive'); ?></option>
            </select>
        </td>                        
    </tr>    
    <tr>
        <th scope="row">
            <label><?php _e("Background type", 'thrive'); ?></label>
        </th>
        <td>
            <input type="radio" name="thrive_shortcode_bg_type" class="thrive_shortcode_bg_type" value="image" checked/> <?php _e("Image", 'thrive'); ?>
            <input type="radio" name="thrive_shortcode_bg_type" class="thrive_shortcode_bg_type" value="pattern"/> <?php _e("Pattern", 'thrive'); ?>
            <input type="radio" name="thrive_shortcode_bg_type" class="thrive_shortcode_bg_type" value="solid"/> <?php _e("Solid", 'thrive'); ?>
        </td>
    </tr>

    <tr id="thrive_shortcode_container_bg_image">
        <th scope="row">
            <label><?php _e("Image", 'thrive'); ?></label>
        </th>
        <td>
            <input type="text" class="adminHeightInput" id="thrive_shortcode_option_image" name="thrive_shortcode_option_image"/>
            <input type="button" class="thrive_upload pure-button upload" id="thrive_shortcode_option_image_btn" value="<?php _e("Upload", 'thrive'); ?>"/>
            <input type="button" class="pure-button remove" id="thrive_shortcode_option_remove_image_btn" value="<?php _e("Remove", 'thrive'); ?>"/>
        </td>
    </tr>

    <tr id="thrive_shortcode_container_bg_pattern" style="display: none;">
        <th scope="row">
            <label><?php _e("Pattern", 'thrive'); ?></label>
        </th>
        <td>
            <input type="text" class="adminHeightInput" id="thrive_shortcode_option_pattern"/>
            <input type="button" class="thrive_upload pure-button upload" id="thrive_shortcode_option_pattern_btn" value="<?php _e("Upload", 'thrive'); ?>"/>
            <input type="button" class="pure-button remove"id="thrive_shortcode_option_remove_pattern_btn" value="<?php _e("Remove", 'thrive'); ?>"/>
            <br/>

            <div class="patternSelect">
                <p><?php _e("Select pattern", 'thrive'); ?></p>

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
    <tr id="thrive_shortcode_container_bg_solid" style="display: none;">
        <th scope="row">
            <label><?php _e("Color", 'thrive'); ?></label>
        </th>
        <td>
            <input type="text" value="#2c3e50" class="thrive-color-field" data-default-color="#2c3e50"
                   id="thrive_shortcode_option_color"/>
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
    <tr>
        <td></td>
        <td>
            <input class="button button-primary" type="button" id="thrive_shortcode_btn_insert" value="<?php _e("Insert", 'thrive'); ?>" />
        </td>
    </tr>
</table>

<script type="text/javascript">

    jQuery(document).ready(function() {
        
        jQuery(".thrive_shortcode_borderless_video_type").click(function() {
            if (jQuery(this).val() == "youtube") {
                jQuery(".thrive_shortcode_container_video_youtube").show();
                jQuery(".thrive_shortcode_container_video_vimeo").hide();
                jQuery(".thrive_shortcode_container_video_custom").hide();
            } else if (jQuery(this).val() == "vimeo") {
                jQuery(".thrive_shortcode_container_video_vimeo").show();
                jQuery(".thrive_shortcode_container_video_youtube").hide();
                jQuery(".thrive_shortcode_container_video_custom").hide();
            } else {
                jQuery(".thrive_shortcode_container_video_custom").show();
                jQuery(".thrive_shortcode_container_video_vimeo").hide();
                jQuery(".thrive_shortcode_container_video_youtube").hide();
            }
        });

        // Uploading files
        var file_frame;
        jQuery('.thrive_upload').on('click', function(event) {
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
                jQuery(".thrive_upload:visible").prev("input[type='text']").val(attachment.url);
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


        var firstPattern = jQuery('.patternList').find('li a').first().css('background-image');
        jQuery('.defaultPattern span').css('background-image', firstPattern);
                
        var temp_pic_url = ThriveThemeUrl + "/images/patterns/batthern.png";
        jQuery('#thrive_shortcode_option_pattern').val(temp_pic_url);
        
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

        jQuery("#thrive_shortcode_btn_insert").click(function() {

            var sc_options = {
                'type' : jQuery(this).parents(".postEdit").find(".thrive_shortcode_borderless_video_type:checked").val(),
                'btn': jQuery("#thrive_shortcode_option_btn_color").val(),
                'color': jQuery("#thrive_shortcode_option_color").val(),
                'image': jQuery("#thrive_shortcode_option_image").val(),
                'pattern': jQuery("#thrive_shortcode_option_pattern").val(),
                'heading': jQuery("#thrive_shortcode_option_heading_text").val().replace(/"/g, '\''),
                'subheading': jQuery("#thrive_shortcode_option_subheading_text").val().replace(/"/g, '\''),
                'cta': jQuery("#thrive_shortcode_option_cta_text").val().replace(/"/g, '\''),
                'hide_related': jQuery("#thrive_shortcode_borderless_youtube_hide_related").prop("checked"),
                'hide_logo': jQuery("#thrive_shortcode_borderless_youtube_hide_logo").prop("checked"),
                'hide_controls': jQuery("#thrive_shortcode_borderless_youtube_hide_controls").prop("checked"),
                'hide_title': jQuery("#thrive_shortcode_borderless_youtube_hide_title").prop("checked"),
                'autoplay': jQuery("#thrive_shortcode_borderless_youtube_autoplay").prop("checked"),
                'hide_fullscreen': jQuery("#thrive_shortcode_borderless_youtube_hide_fullscreen").prop("checked"),
                'position': jQuery(this).parents(".postEdit").find(".thrive_shortcode_position:checked").val(),
                'video_width' : jQuery("#thrive_shortcode_option_video_width").val()
            };
            var sc_video_txt = '';
            var sc_video_options_txt = '';
            if (sc_options.type == 'youtube') {
                sc_video_options_txt = 'hide_related="' + sc_options.hide_related + '" hide_logo="' + sc_options.hide_logo + '" hide_controls="' + sc_options.hide_controls + '" hide_title="' + sc_options.hide_title + '" hide_fullscreen="' + sc_options.hide_fullscreen + '"';
                sc_video_txt = jQuery('#thrive_shortcode_borderless_youtube_url').val();
            }
            if (sc_options.type == 'vimeo') {
                sc_video_txt = jQuery('#thrive_shortcode_borderless_vimeo_url').val();
            }
            if (sc_options.type == 'custom') {
                if (jQuery('#thrive_shortcode_borderless_custom_code').val() != '') {
                    sc_video_txt = jQuery('#thrive_shortcode_borderless_custom_code').val();
                } else {
                    sc_video_txt = '[video src="' + jQuery('#thrive_shortcode_borderless_custom_url').val() + '" width="' + sc_options.video_width + '"]';
                }
            }
            
            var bg_type = jQuery(this).parents(".postEdit").find(".thrive_shortcode_bg_type:checked").val();
            var sc_text = '';
            switch (bg_type) {
                case "solid":
                    sc_text = '[video_page_section type="' + sc_options.type + '" position="' + sc_options.position + '" color="' + sc_options.color + '" btn="' + sc_options.btn + '" heading="' + sc_options.heading + '" subheading="' + sc_options.subheading + '" cta="' + sc_options.cta + '" video_width="' + sc_options.video_width + '" ' + sc_video_options_txt + ']' + sc_video_txt + '[/video_page_section]';
                    break;
                case "image":
                    sc_text = '[video_page_section type="' + sc_options.type + '" position="' + sc_options.position + '" image="' + sc_options.image + '" btn="' + sc_options.btn + '" heading="' + sc_options.heading + '" subheading="' + sc_options.subheading + '" cta="' + sc_options.cta + '" video_width="' + sc_options.video_width + '" ' + sc_video_options_txt + ']' + sc_video_txt + '[/video_page_section]';
                    break;
                case "pattern":
                    sc_text = '[video_page_section type="' + sc_options.type + '" position="' + sc_options.position + '" pattern="' + sc_options.pattern + '" btn="' + sc_options.btn + '" heading="' + sc_options.heading + '" subheading="' + sc_options.subheading + '" cta="' + sc_options.cta + '" video_width="' + sc_options.video_width + '"" ' + sc_video_options_txt + ']' + sc_video_txt + '[/video_page_section]';
                    break;
            }

            tb_remove();

            send_to_editor(sc_text);

        });
    });

</script>