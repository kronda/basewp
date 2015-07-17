<span class="tve_options_headline"><span class="tve_icm tve-ic-move"></span>Thrive Lightbox options</span>
<ul class="tve_menu">
    <li class="tve_firstOnRow tve_ed_btn tve_btn_text">
        <div class="tve_option_separator">
            <i class="tve_icm tve-ic-color-lens tve_left"></i><span
                class="tve_caret tve_icm tve_left" id="sub_01"></span>

            <div class="tve_clear"></div>
            <div class="tve_sub_btn">
                <div class="tve_sub active_sub_menu color_selector" id="tve_sub_01_s">
                    <div class="tve_color_picker tve_left">
                        <span class="tve_options_headline tve_color_title">Custom Colors</span>
                    </div>
                    <div class="tve_clear"></div>
                    <div class="tve_text tve_slider_config" data-value="80" data-min-value="0"
                         data-max-value="100"
                         data-input-selector="#lightbox_opacity_input"
                         data-property="opacity"
                         data-handler="css_opacity"
                         data-no-child="1"
                         data-selector=".tve_p_lb_overlay">
                        <label for="lightbox_opacity_input" class="tve_left">&nbsp;Overlay opacity</label>

                        <div class="tve_slider tve_left" style="width: 150px">
                            <div class="tve_slider_element"></div>
                        </div>
                        <input class="tve_left width50" type="text" id="lightbox_opacity_input" value="80"><span class="tve_left" style="padding-top: 3px;">&nbsp;&nbsp;%</span>

                        <div class="clear"></div>
                    </div>
                    <div class="tve_clear"></div>
                </div>
            </div>
        </div>
    </li>
    <?php if (!empty($page_section_patterns)) : ?>
        <li class="tve_firstOnRow tve_ed_btn tve_btn_text">
            <div class="tve_option_separator">
                <span class="tve_ind tve_left">Background pattern</span>
                <span class="tve_caret tve_icm tve_left" id="sub_02"></span>

                <div class="tve_clear"></div>
                <div class="tve_sub_btn" style="width: 715px;">
                    <div class="tve_sub active_sub_menu" style="width: 100%">
                        <ul class="tve_clearfix">
                            <?php foreach ($page_section_patterns as $i => $_image) : ?>
                                <?php $_uri = $template_uri . '/images/patterns/' . $_image . '.png' ?>
                                <li class="tve_ed_btn tve_btn_text tve_left tve_section_color_change clearfix tve_click" data-ctrl="controls.click.change_pattern" data-plugin="tve_lightbox" data-pattern="1">
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
    <li class="tve_firstOnRow tve_ed_btn tve_btn_text tve_click" id="tve_lightbox_bg_image">Background
        image...
    </li>
    <li class="tve_firstOnRow tve_ed_btn tve_btn_text tve_click"
        id="tve_lightbox_clear_bg_color">Clear background color
    </li>
    <?php if (!empty($page_section_patterns)) : ?>
        <li class="tve_firstOnRow tve_ed_btn tve_btn_text tve_click"
            id="tve_lightbox_clear_bg_pattern">Clear background pattern
        </li>
    <?php endif ?>
    <li class="tve_firstOnRow tve_ed_btn tve_btn_text tve_click"
        id="tve_lightbox_clear_bg_image">Clear background image
    </li>
    <li class="tve_firstOnRow tve_ed_btn tve_btn_text">
        <div class="tve_option_separator">
            <span class="tve_ind tve_left" data-default="Border Type">Border Type</span><span
                class="tve_caret tve_icm tve_left"></span>

            <div class="tve_clear"></div>
            <div class="tve_sub_btn">
                <div class="tve_sub active_sub_menu">
                    <ul>
                        <li id="tve_brdr_none" class="tve_click" data-ctrl="controls.click.add_class" data-border="1">none</li>
                        <li id="tve_brdr_dotted" class="tve_click" data-ctrl="controls.click.add_class" data-border="1">dotted</li>
                        <li id="tve_brdr_dashed" class="tve_click" data-ctrl="controls.click.add_class" data-border="1">dashed</li>
                        <li id="tve_brdr_solid" class="tve_click" data-ctrl="controls.click.add_class" data-border="1">solid</li>
                        <li id="tve_brdr_double" class="tve_click" data-ctrl="controls.click.add_class" data-border="1">double</li>
                        <li id="tve_brdr_groove" class="tve_click" data-ctrl="controls.click.add_class" data-border="1">groove</li>
                        <li id="tve_brdr_ridge" class="tve_click" data-ctrl="controls.click.add_class" data-border="1">ridge</li>
                        <li id="tve_brdr_inset" class="tve_click" data-ctrl="controls.click.add_class" data-border="1">inset</li>
                        <li id="tve_brdr_outset" class="tve_click" data-ctrl="controls.click.add_class" data-border="1">outset</li>
                    </ul>
                </div>
            </div>
        </div>
    </li>
    <li class="tve_firstOnRow tve_ed_btn_text clearfix">
        <label class="tve_left" style="color: #878787">
            <input id="lightbox_border_width" class="tve_change" value="0" type="text" size="3" data-css-property="border-width" data-suffix="px"
                   data-size="1"> px&nbsp;&nbsp;
        </label>
    </li>
    <li class="tve_firstOnRow tve_ed_btn tve_btn_text">
        <div class="tve_option_separator">
            <span class="tve_ind tve_left" data-default="Lightbox style">Lightbox style</span><span
                class="tve_caret tve_icm tve_left"></span>

            <div class="tve_clear"></div>
            <div class="tve_sub_btn">
                <div class="tve_sub active_sub_menu">
                    <ul>
                        <li data-style="style1" class="tve_lightbox_style tve_click">Style 1</li>
                    </ul>
                </div>
            </div>
        </div>
    </li>
    <li class="tve_firstOnRow tve_ed_btn_text clearfix">
        <label class="tve_left" style="color: #878787">
            Max width:
            <input id="lightbox_max_width" class="tve_change" value="650" type="text" size="3" data-css-property="max-width" data-suffix="px"
                   data-size="1"> px&nbsp;&nbsp;
        </label>
    </li>
</ul>