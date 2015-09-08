<span class="tve_options_headline"><span class="tve_icm tve-ic-move"></span><?php echo __("Responsive video", "thrive-cb") ?></span>
<ul class="tve_menu">
    <li class="tve_ed_btn_text tve_firstOnRow">
        <label class="tve_text">
            <?php echo __("Video Type", "thrive-cb") ?>
            <select class="tve_change" id="responsive_video_type">
                <option value="youtube">YouTube</option>
                <option value="vimeo">Vimeo</option>
            </select>
        </label>
    </li>
    <?php include dirname(__FILE__) . '/_margin.php' ?>
    <li class="tve_text clearfix responsive_video_option">
        <input class="tve_change tve_left tve_checkbox_bottom" type="checkbox" id="rv_option_rel" />
        <label for="rv_option_rel" class="tve_left"><?php echo __("Hide related videos", "thrive-cb") ?></label>
    </li>
    <li class="tve_text clearfix responsive_video_option">
        <input class="tve_change tve_left tve_checkbox_bottom" type="checkbox" id="rv_option_modestbranding" />
        <label for="rv_option_modestbranding" class="tve_left"><?php echo __("Auto-hide Youtube logo", "thrive-cb") ?></label>
    </li>
    <li class="tve_text clearfix responsive_video_option">
        <input class="tve_change tve_left tve_checkbox_bottom" type="checkbox" id="rv_option_controls" />
        <label for="rv_option_controls" class="tve_left"><?php echo __("Auto-hide player controls", "thrive-cb") ?></label>
    </li>
    <li class="tve_text clearfix responsive_video_option">
        <input class="tve_change tve_left tve_checkbox_bottom" type="checkbox" id="rv_option_showinfo" />
        <label for="rv_option_showinfo" class="tve_left"><?php echo __("Hide video title bar", "thrive-cb") ?></label>
    </li>
    <li class="tve_text clearfix responsive_video_option">
        <input class="tve_change tve_left tve_checkbox_bottom" type="checkbox" id="rv_option_autoplay" />
        <label for="rv_option_autoplay" class="tve_left"><?php echo __("Autoplay", "thrive-cb") ?></label>
    </li>
    <li class="tve_text clearfix responsive_video_option">
        <input class="tve_change tve_left tve_checkbox_bottom" type="checkbox" id="rv_option_fs" />
        <label for="rv_option_fs" class="tve_left"><?php echo __("Hide full-screen button", "thrive-cb") ?></label>
    </li>
    <li class="tve_ed_btn_text">
        <label class="tve_text" for="responsive_video_url">
            <?php echo __("Video URL", "thrive-cb") ?> <input type="text" class="tve_change" id="responsive_video_url" />
        </label>
    </li>
</ul>