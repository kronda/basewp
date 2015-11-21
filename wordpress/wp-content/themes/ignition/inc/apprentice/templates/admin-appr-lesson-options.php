<table class="form-table postEdit">

    <tr>
        <th scope="row">
            <label for="thrive_meta_appr_download_links"> <?php _e("Download Links", 'thrive'); ?></label>
            <input type="hidden" name="thrive_meta_appr_download_links" value="<?php echo $downloadLinksJson; ?>" id="thrive_meta_appr_download_links" />
        </th>
        <td>
            <div id="thrive-dld-links-container">
                <?php foreach ($downloadLinksArray as $link): ?>
                    <div class="thrive-dld-links-item">
                        <?php _e("Icon", 'thrive'); ?>
                        <select class="thrive-sel-icon">
                            <?php foreach ($icon_types as $key => $icon): ?>
                                <option value="<?php echo $key; ?>" <?php if ($link['icon'] == $key): ?>selected<?php endif; ?>><?php echo $icon; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php _e("Link URL", 'thrive'); ?>
                        <input class="thrive-txt-link-url" type="text" value="<?php echo $link['link_url']; ?>" />
                        <?php _e("Link Text", 'thrive'); ?>
                        <input class="thrive-txt-link-text" type="text" value="<?php echo $link['link_text']; ?>" />
                        <?php _e("Open in New Tab", 'thrive'); ?>
                        <input class="thrive-txt-link-new-tab" type="checkbox" value="1" <?php if ($link['new_tab'] == 1): ?>checked<?php endif; ?> />
                        <a href="#" class="thrive-remove-link"><?php _e("Remove", 'thrive'); ?></a>
                    </div>
                <?php endforeach; ?>
            </div>
            <div id="thrive-dld-links-clone" class="thrive-dld-links-item" style="display:none;">
                <?php _e("Icon", 'thrive'); ?>
                <select class="thrive-sel-icon">
                    <?php foreach ($icon_types as $key => $icon): ?>
                        <option value="<?php echo $key; ?>"><?php echo $icon; ?></option>
                    <?php endforeach; ?>
                </select>
                <?php _e("Link URL", 'thrive'); ?>
                <input class="thrive-txt-link-url" type="text" value="http://" />
                <?php _e("Link Text", 'thrive'); ?>
                <input class="thrive-txt-link-text" type="text" value="" />
                <?php _e("Open in New Tab", 'thrive'); ?>
                <input class="thrive-txt-link-new-tab" type="checkbox" value="1" checked />
                <a href="#" class="thrive-remove-link"><?php _e("Remove", 'thrive'); ?></a>
            </div>
            <br/>
            <input type="button" id="thrive-dld-links-btn-add" value="<?php _e("+ Add Link", 'thrive'); ?>" />

        </td>
    </tr>

    <tr>
        <th scope="row">
            <label for="thrive_meta_appr_lesson_type"> <?php _e("Lesson type", 'thrive'); ?></label>
        </th>
        <td>
            <select id='thrive_meta_appr_lesson_type' name='thrive_meta_appr_lesson_type'>
                <?php foreach ($lesson_types as $key => $type): ?>
                    <?php
                    $selected = ($key == $value_appr_lesson_type) ? "selected" : "";
                    echo "<option value='" . $key . "' " . $selected . ">" . $type . "</option>";
                    ?>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>

</table>
<div id="thrive_appr_options">
    <table class="form-table postEdit" id="thrive_appr_video_options" style="display:none;">

        <tr>
            <td scope="row" colspan="2">
                <input type="radio" name="thrive_meta_appr_video_type" class="thrive_meta_appr_video_type"
                       value="youtube" <?php if ($thrive_meta_appr_video_type != "vimeo" && $thrive_meta_appr_video_type != "custom"): ?>checked<?php endif; ?>/> Youtube
                <input type="radio" name="thrive_meta_appr_video_type" class="thrive_meta_appr_video_type"
                       value="vimeo" <?php if ($thrive_meta_appr_video_type == "vimeo"): ?>checked<?php endif; ?>/> Vimeo
                <input type="radio" name="thrive_meta_appr_video_type" class="thrive_meta_appr_video_type"
                       value="custom" <?php if ($thrive_meta_appr_video_type == "custom"): ?>checked<?php endif; ?>/> <?php _e("Custom", 'thrive'); ?> (link)
                <input type="radio" name="thrive_meta_appr_video_type" class="thrive_meta_appr_video_type"
                       value="custom_embed" <?php if ($thrive_meta_appr_video_type == "custom_embed"): ?>checked<?php endif; ?>/> <?php _e("Custom", 'thrive'); ?> (embed)
            </td>
        </tr>
        <tr class="thrive_shortcode_container_video_youtube">
            <td>
                <?php _e("Video Url", 'thrive'); ?>
            </td>
            <td>
                <input class="adminWidthInput" type="text" id="thrive_meta_appr_video_youtube_url" 
                       name="thrive_meta_appr_video_youtube_url" value="<?php echo $thrive_meta_appr_video_youtube_url; ?>"/>
            </td>
        </tr>
        <tr class="thrive_shortcode_container_video_youtube noBorder">
            <td>
                <?php _e("Options", 'thrive'); ?>
            </td>
            <td>
                <input type="checkbox" name="thrive_meta_appr_video_youtube_hide_related" <?php if ($thrive_meta_appr_video_youtube_hide_related == 1): ?>checked<?php endif; ?>
                       id="thrive_meta_appr_video_youtube_hide_related" value="1"/> <?php _e("Hide related videos", 'thrive'); ?> <br/>
                <input type="checkbox" name="thrive_meta_appr_video_youtube_hide_logo" <?php if ($thrive_meta_appr_video_youtube_hide_logo == 1): ?>checked<?php endif; ?>
                       id="thrive_meta_appr_video_youtube_hide_logo" value="1"/> <?php _e("Auto-hide Youtube logo", 'thrive'); ?> <br/>
                <input type="checkbox" name="thrive_meta_appr_video_youtube_hide_controls" <?php if ($thrive_meta_appr_video_youtube_hide_controls == 1): ?>checked<?php endif; ?>
                       id="thrive_meta_appr_video_youtube_hide_controls" value="1"/> <?php _e("Auto-hide player controls", 'thrive'); ?> <br/>
                <input type="checkbox" name="thrive_meta_appr_video_youtube_hide_title" <?php if ($thrive_meta_appr_video_youtube_hide_title == 1): ?>checked<?php endif; ?>
                       id="thrive_meta_appr_video_youtube_hide_title" value="1"/> <?php _e("Hide video title bar", 'thrive'); ?> <br/>
                <input type="checkbox" name="thrive_meta_appr_video_youtube_autoplay" <?php if ($thrive_meta_appr_video_youtube_autoplay == 1): ?>checked<?php endif; ?>
                       id="thrive_meta_appr_video_youtube_autoplay" value="1"/> <?php _e("Autoplay", 'thrive'); ?> <br/>
                <input type="checkbox" name="thrive_meta_appr_video_youtube_hide_fullscreen" <?php if ($thrive_meta_appr_video_youtube_hide_fullscreen == 1): ?>checked<?php endif; ?>
                       id="thrive_meta_appr_video_youtube_hide_fullscreen" value="1"/> <?php _e("Hide full-screen button", 'thrive'); ?>
            </td>
        </tr>
        <tr class="thrive_shortcode_container_video_vimeo" style="display: none;">
            <td>
                <?php _e("Video Url", 'thrive'); ?>:
            </td>
            <td>
                <input class="adminWidthInput" type="text" id="thrive_meta_appr_video_vimeo_url" 
                       name="thrive_meta_appr_video_vimeo_url" value="<?php echo $thrive_meta_appr_video_vimeo_url; ?>"/>
            </td>
        </tr>
        <tr class="thrive_shortcode_container_video_custom" style="display: none;">
            <td>
                <?php _e("Video Url", 'thrive'); ?>: <br/>
            </td>
            <td>
                <input class="adminWidthInput" type="text" id="thrive_meta_appr_video_custom_url" 
                       name="thrive_meta_appr_video_custom_url" value="<?php echo $thrive_meta_appr_video_custom_url; ?>"/>
            </td>
        </tr>       
        <tr class="thrive_shortcode_container_video_custom_embed" style="display: none;">
            <td>
                <?php _e("Video Embed Code", 'thrive'); ?>: <br/>
            </td>
            <td>
                <textarea class="adminWidthInput" type="text" id="thrive_meta_appr_video_custom_embed" 
                       name="thrive_meta_appr_video_custom_embed"><?php echo $thrive_meta_appr_video_custom_embed; ?></textarea>
            </td>
        </tr>       

    </table>

    <table class="form-table postEdit" id="thrive_appr_quote_options" style="display:none;">
        <tr>
            <th scope="row">
                <label for=""><?php _e("Quote") ?></label><br/>
            </th>
            <td>
                <textarea name="thrive_meta_appr_quote_text" id="thrive_meta_appr_quote_text"><?php echo $thrive_meta_appr_quote_text; ?></textarea>
            </td>
        </tr>
        <tr>
            <th scope="row">
                <label for=""><?php _e("Author") ?></label><br/>
            </th>
            <td>
                <input type="text" name="thrive_meta_appr_quote_author" id="thrive_meta_appr_quote_author" value="<?php echo $thrive_meta_appr_quote_author; ?>" />
            </td>
        </tr>
    </table>

    <table class="form-table postEdit" id="thrive_appr_audio_options" style="display:none;">
        <tr>
            <td>
                <input class="thrive_meta_appr_audio_type" type="radio" name="thrive_meta_appr_audio_type" value="file" <?php if ($thrive_meta_appr_audio_type != "soundcloud"): ?>checked<?php endif; ?> /> <?php _e("Custom", 'thrive'); ?>
                <input class="thrive_meta_appr_audio_type" type="radio" name="thrive_meta_appr_audio_type" value="soundcloud" <?php if ($thrive_meta_appr_audio_type == "soundcloud"): ?>checked<?php endif; ?> /> <?php _e("Soundcloud", 'thrive'); ?>
            </td>
        </tr>
        <tr id="tr_thrive_appr_audio_file">
            <th scope="row">
                <label for=""><?php _e("File url") ?></label><br/>
            </th>
            <td>
                <input type="text" id="thrive_meta_appr_audio_file" name="thrive_meta_appr_audio_file" value="<?php echo $thrive_meta_appr_audio_file; ?>" />
                <input type="button" id="thrive_btn_appr_select_audio_file" value="<?php _e("Select"); ?>" />
            </td>
        </tr>
        <tr id="tr_thrive_appr_audio_soundcould">
            <th scope="row">
                <label for=""><?php _e("Url", 'thrive') ?></label><br/>
            </th>
            <td>
                <input type="text" name="thrive_meta_appr_audio_soundcloud_url" value="<?php echo $thrive_meta_appr_audio_soundcloud_url; ?>" />
                <label for=""><?php _e("Autoplay") ?></label>
                <input type="checkbox" name="thrive_meta_appr_audio_soundcloud_autoplay" value="1" <?php if ($thrive_meta_appr_audio_soundcloud_autoplay == 1): ?>checked<?php endif; ?> />
            </td>
        </tr>
    </table>

    <table class="form-table postEdit" id="thrive_appr_gallery_options" style="display:none;">
        <tr>
            <td>            
                <div id="thrive_container_appr_gallery_list">
                    <?php
                    $thrive_gallery_ids = explode(",", $thrive_meta_appr_gallery_images);
                    foreach ($thrive_gallery_ids as $key => $id):
                        $img_url = wp_get_attachment_url($id);
                        if ($img_url):
                            ?>
                            <img class="thrive-gallery-thumb" src="<?php echo $img_url; ?>" width="50" height="50" />
                        <?php
                        endif;
                    endforeach;
                    ?>
                </div>
                <input type="hidden" name="thrive_meta_appr_gallery_images" value="<?php echo $thrive_meta_appr_gallery_images; ?>" id="thrive_meta_appr_gallery_images" />
                <input type="button" id="btn_thrive_appr_select_gallery" value="<?php _e("Select images", 'thrive') ?>" />
            </td>
        </tr>

    </table>
</div>