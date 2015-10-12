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
    <li class="tve_ed_btn tve_btn_text">
        <div class="tve_option_separator">
            <span class="tve_ind tve_left"><?php echo __("No Style", "thrive-cb") ?></span>
            <span id="sub_02" class="tve_caret tve_icm tve_left"></span>

            <div class="tve_clear"></div>
            <div class="tve_sub_btn" style="width: 482px">
                <div class="tve_sub active_sub_menu" style="width: 100%; box-sizing: border-box;">
                    <ul class="tve_clearfix">
                        <li class="rv_style tve_ed_btn tve_btn_text tve_left clearfix tve_click" id="rv_style_white_frame" data-cls="rv_style_white_frame" data-ctrl="controls.click.add_class">
                            <div class="rv_style_image"></div>
                            <div><?php echo __("White Frame", "thrive-cb") ?></div>
                        </li>
                        <li class="rv_style tve_ed_btn tve_btn_text tve_left clearfix tve_click" id="rv_style_gray_frame" data-cls="rv_style_gray_frame" data-ctrl="controls.click.add_class">
                            <div class="rv_style_image"></div>
                            <div><?php echo __("Gray Frame", "thrive-cb") ?></div>
                        </li>
                        <li class="rv_style tve_ed_btn tve_btn_text tve_left clearfix tve_click" id="rv_style_dark_frame" data-cls="rv_style_dark_frame" data-ctrl="controls.click.add_class">
                            <div class="rv_style_image"></div>
                            <div><?php echo __("Dark Frame", "thrive-cb") ?></div>
                        </li>
                        <li class="rv_style tve_ed_btn tve_btn_text tve_left clearfix tve_click" id="rv_style_light_frame" data-cls="rv_style_light_frame" data-ctrl="controls.click.add_class">
                            <div class="rv_style_image"></div>
                            <div><?php echo __("Light Frame", "thrive-cb") ?></div>
                        </li>
                        <li class="rv_style tve_ed_btn tve_btn_text tve_left clearfix tve_click" id="rv_style_lifted_style1" data-cls="rv_style_lifted_style1" data-ctrl="controls.click.add_class">
                            <div class="rv_style_image"></div>
                            <div><?php echo sprintf(__("Lifted Style %s", 'thrive-cb'), "1") ?></div>
                        </li>
                        <li class="rv_style tve_ed_btn tve_btn_text tve_left clearfix tve_click" id="rv_style_lifted_style2" data-cls="rv_style_lifted_style2" data-ctrl="controls.click.add_class">
                            <div class="rv_style_image"></div>
                            <div><?php echo sprintf(__("Lifted Style %s", 'thrive-cb'), "2") ?></div>
                        </li>
                        <li class="rv_style tve_ed_btn tve_btn_text tve_left clearfix tve_click" id="rv_style_lifted_style3" data-cls="rv_style_lifted_style3" data-ctrl="controls.click.add_class">
                            <div class="rv_style_image"></div>
                            <div><?php echo sprintf(__("Lifted Style %s", 'thrive-cb'), "3") ?></div>
                        </li>
                        <li class="rv_style tve_ed_btn tve_btn_text tve_left clearfix tve_click" id="rv_style_lifted_style4" data-cls="rv_style_lifted_style4" data-ctrl="controls.click.add_class">
                            <div class="rv_style_image"></div>
                            <div><?php echo sprintf(__("Lifted Style %s", 'thrive-cb'), "4") ?></div>
                        </li>
                        <li class="rv_style tve_ed_btn tve_btn_text tve_left clearfix tve_click" id="rv_style_lifted_style5" data-cls="rv_style_lifted_style5" data-ctrl="controls.click.add_class">
                            <div class="rv_style_image"></div>
                            <div><?php echo sprintf(__("Lifted Style %s", 'thrive-cb'), "5") ?></div>
                        </li>
                        <li class="rv_style tve_ed_btn tve_btn_text tve_left clearfix tve_click" id="rv_style_lifted_style6" data-cls="rv_style_lifted_style6" data-ctrl="controls.click.add_class">
                            <div class="rv_style_image"></div>
                            <div><?php echo sprintf(__("Lifted Style %s", 'thrive-cb'), "6") ?></div>
                        </li>
                        <li class="rv_style tve_ed_btn tve_btn_text tve_left clearfix tve_click" data-cls="rv_style_none" data-ctrl="controls.click.add_class">
                            <div class="rv_style_image"></div>
                            <div><?php echo __("No Style", "thrive-cb") ?></div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </li>
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