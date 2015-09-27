<span class="tve_options_headline"><span class="tve_icm tve-ic-move"></span><?php echo __("Responsive video", "thrive-cb") ?></span>
<ul class="tve_menu">
    <li class="tve_ed_btn_text tve_firstOnRow">
        <label class="tve_text">
            <?php echo __("Video Type", "thrive-cb") ?>
            <select class="tve_change" id="responsive_video_type">
                <option value="youtube">YouTube</option>
                <option value="vimeo">Vimeo</option>
                <option value="wistia">Wistia</option>
            </select>
        </label>
        &nbsp;
    </li>
    <?php include dirname(__FILE__) . '/_margin.php'; ?>
    <?php
    $extra_attr = 'data-apply-to="[wistia|vimeo]"';
    $btn_class = "responsive_video_color responsive_video_option";
    $hide_default_colors = true;
    $has_custom_colors = true;
    include dirname(__FILE__) . '/_custom_colors.php';
    ?>
    <li class="tve_ed_btn_text">
        <label class="tve_text" for="responsive_video_url">
            <?php echo __("Video URL", "thrive-cb") ?> <input type="text" class="tve_change" id="responsive_video_url"/>
        </label>
    </li>
    <li class="tve_text clearfix responsive_video_option" data-apply-to="[youtube|wistia]">
        <?php echo __("Video Start Time", "thrive-cb") ?>
        <input type="text" size="2" class="tve_change" data-ctrl="controls.responsive_video.start_time_changed" id="responsive_video_start_min_time"/> <?php echo __("mins", "thrive-cb") ?>
        <input type="text" size="2" class="tve_change" data-ctrl="controls.responsive_video.start_time_changed" id="responsive_video_start_sec_time"/> <?php echo __("secs", "thrive-cb") ?>
    </li>
    <li class="tve_text clearfix responsive_video_option" data-apply-to="[youtube]">
        <input class="tve_change tve_left tve_checkbox_bottom" type="checkbox" id="rv_option_rel"/>
        <label for="rv_option_rel" class="tve_left"><?php echo __("Hide related videos", "thrive-cb") ?></label>
    </li>
    <li class="tve_text clearfix responsive_video_option" data-apply-to="[youtube|vimeo]">
        <input class="tve_change tve_left tve_checkbox_bottom" type="checkbox" id="rv_option_modestbranding"/>
        <label for="rv_option_modestbranding" class="tve_left"><?php echo __("Auto-hide Logo", "thrive-cb") ?></label>
    </li>
    <li class="tve_text clearfix responsive_video_option" data-apply-to="[wistia]">
        <input class="tve_change tve_left tve_checkbox_bottom" data-ctrl="controls.responsive_video.play_bar" type="checkbox" id="rv_option_play_bar"/>
        <label for="rv_option_play_bar" class="tve_left"><?php echo __("Play bar", "thrive-cb") ?></label>
    </li>
    <li class="tve_text clearfix responsive_video_option" data-apply-to="[youtube|wistia]">
        <input class="tve_change tve_left tve_checkbox_bottom" type="checkbox" id="rv_option_controls"/>
        <label for="rv_option_controls" class="tve_left"><?php echo __("Auto-hide player controls", "thrive-cb") ?></label>
    </li>
    <li class="tve_text clearfix responsive_video_option" data-apply-to="[wistia]">
        <input class="tve_change tve_left tve_checkbox_bottom" data-ctrl="controls.responsive_video.onload_controls" type="checkbox" id="rv_option_onload_controls"/>
        <label for="rv_option_controls" class="tve_left"><?php echo __("Controls visible on load", "thrive-cb") ?></label>
    </li>
    <li class="tve_text clearfix responsive_video_option" data-apply-to="[youtube|vimeo]">
        <input class="tve_change tve_left tve_checkbox_bottom" type="checkbox" id="rv_option_showinfo"/>
        <label for="rv_option_showinfo" class="tve_left"><?php echo __("Hide video title bar", "thrive-cb") ?></label>
    </li>
    <li class="tve_text clearfix responsive_video_option" data-apply-to="[youtube|wistia|vimeo]">
        <input class="tve_change tve_left tve_checkbox_bottom" type="checkbox" id="rv_option_autoplay"/>
        <label for="rv_option_autoplay" class="tve_left"><?php echo __("Autoplay", "thrive-cb") ?></label>
    </li>
    <li class="tve_text clearfix responsive_video_option" data-apply-to="[youtube|wistia]">
        <input class="tve_change tve_left tve_checkbox_bottom" type="checkbox" id="rv_option_fs"/>
        <label for="rv_option_fs" class="tve_left"><?php echo __("Hide full-screen button", "thrive-cb") ?></label>
    </li>
</ul>