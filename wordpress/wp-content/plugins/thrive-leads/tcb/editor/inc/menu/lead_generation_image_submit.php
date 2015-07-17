<span class="tve_options_headline">Submit Image Options</span>
<ul class="tve_menu">
    <?php
    $css_selector = $css_padding_selector = "input[type='image']";
    $margin_right_hide = $margin_left_hide = true;
    include dirname(__FILE__) . '/_margin.php'
    ?>

    <?php
    $change_class_target = ".edit_mode input[type='image']";
    include dirname(__FILE__) . '/_custom_class.php'
    ?>

    <li class="tve_ed_btn tve_btn_icon">
        <div class="tve_icm tve-ic-paragraph-left tve_click" title="Align left" data-ctrl="controls.click.text_align" data-cls="tve_p_left"></div>
    </li>
    <li class="tve_ed_btn tve_btn_icon">
        <div class="tve_icm tve-ic-paragraph-center tve_click" title="Align center" data-ctrl="controls.click.text_align" data-cls="tve_p_center"></div>
    </li>
    <li class="tve_ed_btn tve_btn_icon">
        <div class="tve_icm tve-ic-paragraph-right tve_click" title="Align right" data-ctrl="controls.click.text_align" data-cls="tve_p_right"></div>
    </li>
    <li class="tve_text tve_slider_config" data-value="300" data-min-value="10" data-property="max-width" data-max-value="available"
        data-selector="function:controls.lead_generation.image_submit_selector"
        data-input-selector="#tve_lg_image_submit_width_input" data-selector="input">
        <label for="tve_lg_image_submit_width" class="tve_left">&nbsp;Max Width</label>

        <div class="tve_slider tve_left">
            <div class="tve_slider_element" id="tve_lg_image_submit_width"></div>
        </div>
        <input class="tve_left" type="text" id="tve_lg_image_submit_width_input" value="">

        <div class="clear"></div>
    </li>
    <li id="tve_lg_change_image" class="tve_ed_btn tve_center tve_btn_text btn_alignment tve_click">Change Image</li>
    <li>
        <div id="tve_convert_image_submit" class="tve_ed_btn tve_btn_text tve_center tve_left tve_click">Convert image to button</div>
    </li>
</ul>