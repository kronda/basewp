<span class="tve_options_headline"><span class="tve_icm tve-ic-move"></span><?php echo __("Page Section options", "thrive-cb") ?></span>
<ul class="tve_menu">
    <li class="tve_ed_btn tve_btn_text">
        <div class="tve_option_separator">
            <i class="tve_icm tve-ic-color-lens tve_left"></i><span
                class="tve_caret tve_icm tve_left" id="sub_01"></span>

            <div class="tve_clear"></div>
            <div class="tve_sub_btn">
                <div class="tve_sub active_sub_menu color_selector" id="tve_sub_01_s">
                    <div class="tve_color_picker tve_left">
                        <span class="tve_options_headline tve_color_title"><?php echo __("Custom Colors", "thrive-cb") ?></span>
                    </div>
                    <div class="tve_clear"></div>
                </div>
            </div>
        </div>
    </li>
    <?php if (!empty($page_section_patterns)) : ?>
        <li class="tve_ed_btn tve_btn_text">
            <div class="tve_option_separator">
                <span class="tve_ind tve_left"><?php echo __("Background pattern", "thrive-cb") ?></span>
                <span class="tve_caret tve_icm tve_left" id="sub_02"></span>

                <div class="tve_clear"></div>
                <div class="tve_sub_btn" style="width: 715px;">
                    <div class="tve_sub active_sub_menu" style="width: 100%">
                        <ul class="tve_clearfix">
                            <?php foreach ($page_section_patterns as $i => $_image) : ?>
                                <?php $_uri = $template_uri . '/images/patterns/' . $_image . '.png' ?>
                                <li class="tve_ed_btn tve_btn_text tve_left tve_section_color_change clearfix tve_click" data-ctrl="controls.click.change_pattern" data-pattern="1" data-plugin="tve_page_section">
                                    <span class="tve_section_colour tve_left" style="background:url('<?php echo $_uri ?>')"></span>
                                    <span class="tve_left"><?php echo 'pattern' . ($i + 1); ?></span>
                                    <input type="hidden" data-image="<?php echo $_uri; ?>"/>
                                </li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                </div>
            </div>
        </li>
    <?php endif ?>
    <li class="tve_ed_btn tve_btn_text tve_center tve_click" id="tve_page_section_bg_image"><?php echo __("Background image...", "thrive-cb") ?></li>
    <?php /* removing this from landing pages, as it does not make sense anymore, you can setup custom colors for stuff inside. I wasn't working on any landing page */ ?>
    <?php if (!$landing_page_template) : ?>
        <li class="tve_ed_btn tve_btn_text">
            <div class="tve_option_separator">
                <span class="tve_ind tve_left"><?php echo __("Text style", "thrive-cb") ?></span>
                <span class="tve_caret tve_icm tve_left" id="sub_02"></span>

                <div class="tve_clear"></div>
                <div class="tve_sub_btn">
                    <div class="tve_sub active_sub_menu">
                        <ul>
                            <li class="tve_btn_text tve_click clearfix" id="tve_page_section_s_light"><?php echo __("Light", "thrive-cb") ?></li>
                            <li class="tve_btn_text tve_click clearfix" id="tve_page_section_s_dark"><?php echo __("Dark", "thrive-cb") ?></li>
                        </ul>
                    </div>
                </div>
            </div>
        </li>
    <?php endif ?>
    <?php $css_padding_selector = '.in'; include dirname(__FILE__) . '/_margin.php' ?>
    <li><input type="text" class="element_id tve_change tve_text" data-ctrl="controls.change.element_id" placeholder="<?php echo __("Custom ID", "thrive-cb") ?>"></li>
    <li class="tve_clear"></li>
    <li class="tve_text clearfix tve_firstOnRow">
        <input class="tve_change tve_left tve_checkbox_bottom" type="checkbox" id="tve_page_section_bg_fixed"
               value="1"><label
            for="tve_page_section_bg_fixed" class="tve_left"> <?php echo __("Static image", "thrive-cb") ?></label> &nbsp;
    </li>
    <li class="tve_text clearfix">
        <input class="tve_change tve_left tve_checkbox_bottom" type="checkbox" id="tve_page_section_auto_height" value="1"><label
            for="tve_page_section_auto_height" class="tve_left"> <?php echo __("Full height image", "thrive-cb") ?></label>
    </li>
    <li class="tve_clear"></li>
    <li class="tve_firstOnRow tve_ed_btn tve_btn_text tve_center tve_click"
        id="tve_page_section_clear_shadow"><?php echo __("Clear shadow", "thrive-cb") ?>
    </li>
    <li class="tve_firstOnRow tve_ed_btn tve_btn_text tve_center tve_click"
        id="tve_page_section_clear_bg_color"><?php echo __("Clear background color", "thrive-cb") ?>
    </li>
    <?php if (!empty($page_section_patterns)) : ?>
        <li class="tve_firstOnRow tve_ed_btn tve_btn_text tve_center tve_click"
            id="tve_page_section_clear_bg_pattern"><?php echo __("Clear background pattern", "thrive-cb") ?>
        </li>
    <?php endif ?>
    <li class="tve_firstOnRow tve_ed_btn tve_btn_text tve_center tve_click"
        id="tve_page_section_clear_bg_image"><?php echo __("Clear background image", "thrive-cb") ?>
    </li>
</ul>