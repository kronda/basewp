<span class="tve_options_headline"><span class="tve_icm tve-ic-move"></span>Image options</span>
<ul class="tve_menu">
    <?php $has_custom_colors = true; $btn_class = ''; include dirname(__FILE__) . '/_custom_colors.php' ?>
    <li class="tve_ed_btn tve_btn_text">
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
    <li class="tve_ed_btn_text clearfix">
        <label class="tve_left" style="color: #878787">
            <input id="image_border_width" class="tve_change" value="0" type="text" size="3" data-css-property="border-width" data-suffix="px"
                   data-size="1"> px
        </label>
    </li>
    <li class="tve_ed_btn tve_btn_icon">
        <span class="tve_icm tve-ic-paragraph-left tve_click" id="img_left_align"></span>
    </li>
    <li class="tve_ed_btn tve_btn_icon tve_hidden_feature_grid">
        <span class="tve_icm tve-ic-paragraph-center tve_click" id="img_center_align"></span>
    </li>
    <li class="tve_ed_btn tve_btn_icon">
        <span class="tve_icm tve-ic-paragraph-right tve_click" id="img_right_align"></span>
    </li>
    <li id="img_no_align" class="tve_ed_btn tve_btn_text tve_center tve_click tve_hidden_feature_grid">None</li>

    <?php $css_selector = '_parent::.tve_image_caption'; $btn_class = 'tve_hidden_feature_grid';
    include dirname(__FILE__) . '/_margin.php' ?>

    <li class="tve_ed_btn">
        <span class="tve_click tve_lb_small tve_icm tve-ic-chain" data-ctrl="controls.lb_open" id="lb_image_link"></span>
    </li>
    <!-- this only shows when the user clicks on a hyperlink -->
    <li class="tve_ed_btn tve_link_btns">
        <span class="tve_icm tve-ic-unlink tve_click" data-ctrl="controls.click.image_unlink"></span>
    </li>
    <li class="tve_text tve_slider_config tve_hidden_feature_grid" data-value="300" data-min-value="0" data-max-value="available"
        data-input-selector="#image_width_input">
        <label for="image_width_input" class="tve_left">&nbsp;Image size</label>

        <div class="tve_slider tve_left">
            <div class="tve_slider_element" id="tve_img_size_slider"></div>
        </div>
        <input class="tve_left" type="text" id="image_width_input" value="20px">

        <div class="clear"></div>
    </li>
    <li id="change_image" class="tve_ed_btn tve_center tve_btn_text btn_alignment upload_image_cpanel">Change Image</li>
    <li class="tve_text clearfix">
        <label for="img_alt_att" class="tve_left">Alt text&nbsp;</label>
        <input type="text" id="img_alt_att" class="tve_left tve_change">
    </li>
    <li class="tve_text clearfix tve_btn_text">
        <label for="img_title_att" class="tve_left">Title text&nbsp;</label>
        <input type="text" id="img_title_att" class="tve_left tve_change">
    </li>
    <li class=""><input type="text" class="element_class tve_change" data-ctrl="controls.change.cls" placeholder="Custom class"></li>
    <li class="tve_ed_btn tve_btn_text tve_hidden_feature_grid">
        <div class="tve_option_separator">
            <span class="tve_ind tve_left">No Style</span>
            <span id="sub_02" class="tve_caret tve_icm tve_left"></span>

            <div class="tve_clear"></div>
            <div class="tve_sub_btn" style="width: 482px">
                <div class="tve_sub active_sub_menu" style="width: 100%; box-sizing: border-box;">
                    <ul class="tve_clearfix">
                        <li class="img_style tve_ed_btn tve_btn_text tve_left clearfix tve_click" id="img_style_dark_frame">
                            <div class="img_style_image"></div>
                            <div>Dark Frame</div>
                        </li>
                        <li class="img_style tve_ed_btn tve_btn_text tve_left clearfix tve_click" id="img_style_framed">
                            <div class="img_style_image"></div>
                            <div>Framed</div>
                        </li>
                        <li class="img_style tve_ed_btn tve_btn_text tve_left clearfix tve_click" id="img_style_lifted_style1">
                            <div class="img_style_image"></div>
                            <div>Lifted Style 1</div>
                        </li>
                        <li class="img_style tve_ed_btn tve_btn_text tve_left clearfix tve_click" id="img_style_lifted_style2">
                            <div class="img_style_image"></div>
                            <div>Lifted Style 2</div>
                        </li>
                        <li class="img_style tve_ed_btn tve_btn_text tve_left clearfix tve_click" id="img_style_polaroid">
                            <div class="img_style_image"></div>
                            <div>Polaroid</div>
                        </li>
                        <li class="img_style tve_ed_btn tve_btn_text tve_left clearfix tve_click" id="img_style_rounded_corners">
                            <div class="img_style_image"></div>
                            <div>Rounded Corners</div>
                        </li>
                        <li class="img_style tve_ed_btn tve_btn_text tve_left clearfix tve_click" id="img_style_circle">
                            <div class="img_style_image"></div>
                            <div>Circle</div>
                        </li>
                        <li class="img_style tve_ed_btn tve_btn_text tve_left clearfix tve_click" id="img_style_caption_overlay">
                            <div class="img_style_image"></div>
                            <div>Caption Overlay</div>
                        </li>
                        <li class="img_style tve_ed_btn tve_btn_text tve_left clearfix tve_click">
                            <div class="img_style_image"></div>
                            <div>No Style</div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </li>
    <?php $li_custom_class = ''; include dirname(__FILE__) . '/_event_manager.php'; unset($li_custom_class) ?>
    <li class="tve_clear"></li>
</ul>