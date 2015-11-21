<table class="form-table postEdit">
    <tr class="thrive_shortcode_container_video">
        <td>
            <table>
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
                        <input type="checkbox" name="thrive_shortcode_borderless_youtube_autoplay"
                               id="thrive_shortcode_borderless_youtube_autoplay" value="1"/> <?php _e("Autoplay", 'thrive'); ?> <br/>
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
        <td colspan="2">
            <input class="button button-primary" type="button" id="thrive_shortcode_btn_insert" value="<?php _e("Insert", 'thrive'); ?>"/>
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


        jQuery("#thrive_shortcode_btn_insert").click(function() {

            var sc_options = {
                'video_type': jQuery(".thrive_shortcode_borderless_video_type:checked").val(),
                'youtube_url': jQuery("#thrive_shortcode_borderless_youtube_url").val(),
                'vimeo_url': jQuery("#thrive_shortcode_borderless_vimeo_url").val(),
                'custom_url': jQuery("#thrive_shortcode_borderless_custom_url").val(),
                'hide_related': jQuery("#thrive_shortcode_borderless_youtube_hide_related:checked").val(),
                'hide_logo': jQuery("#thrive_shortcode_borderless_youtube_hide_logo:checked").val(),
                'hide_controls': jQuery("#thrive_shortcode_borderless_youtube_hide_controls:checked").val(),
                'hide_title': jQuery("#thrive_shortcode_borderless_youtube_hide_title:checked").val(),
                'autoplay': jQuery("#thrive_shortcode_borderless_youtube_autoplay:checked").val(),
                'hide_fullscreen': jQuery("#thrive_shortcode_borderless_youtube_hide_fullscreen:checked").val()
            };

            if (sc_options.hide_controls === undefined) {
                sc_options.hide_controls = 0;
            }
            if (sc_options.hide_related === undefined) {
                sc_options.hide_related = 0;
            }
            if (sc_options.hide_logo === undefined) {
                sc_options.hide_logo = 0;
            }
            if (sc_options.hide_title === undefined) {
                sc_options.hide_title = 0;
            }
            if (sc_options.autoplay === undefined) {
                sc_options.autoplay = 0;
            }
            if (sc_options.hide_fullscreen === undefined) {
                sc_options.hide_fullscreen = 0;
            }

            var sc_text = "";

            if (sc_options.video_type === "youtube") {
                sc_text = "[responsive_video type='youtube' hide_related='" + sc_options.hide_related + "' hide_logo='" + sc_options.hide_logo + "' hide_controls='" + sc_options.hide_controls + "' hide_title='" + sc_options.hide_title + "' hide_fullscreen='" + sc_options.hide_fullscreen + "' autoplay='" + sc_options.autoplay + "']" + sc_options.youtube_url + "[/responsive_video]";
                tb_remove();
                send_to_editor(sc_text);
                return;
            }

            if (sc_options.video_type === "vimeo") {
                sc_text = "[responsive_video type='vimeo']" + sc_options.vimeo_url + "[/responsive_video]";
                tb_remove();
                send_to_editor(sc_text);
                return;
            }

            if (sc_options.video_type === "custom") {
                sc_text = "[responsive_video type='custom_url'][video src='" + sc_options.custom_url + "'][/responsive_video]";
                tb_remove();
                send_to_editor(sc_text);
                return;
            }

            tb_remove();
            send_to_editor(sc_text);

        });

    });

</script>