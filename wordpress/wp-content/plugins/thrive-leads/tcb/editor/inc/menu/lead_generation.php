<span class="tve_options_headline"><span class="tve_icm tve-ic-move"></span>Lead Generation options</span>
<ul class="tve_menu tve_clearfix">
    <?php $has_custom_colors = true;
    include dirname(__FILE__) . '/_custom_colors.php' ?>

    <?php include dirname(__FILE__) . '/_margin.php' ?>

    <?php
    $css_selector = $css_padding_selector = "input[type='text'], input[type='image'], select, textarea, button, .tve_lg_radio, .tve_lg_checkbox";
    $margin_prefix = 'Fields';
    include dirname(__FILE__) . '/_margin.php'
    ?>

    <li>
        <div id="lb_lead_generation_code" class="tve_ed_btn tve_btn tve_btn_text tve_click" data-wpapi="lb_lead_generation_code"
             data-ctrl="controls.lb_open">Connect with Service
        </div>
    </li>
    <li class="tve_ed_btn tve_btn_text">
        <div class="tve_option_separator">
            <span class="tve_ind tve_left">Vertical</span><span id="sub_02" class="tve_caret tve_icm tve_left"></span>

            <div class="tve_clear"></div>
            <div class="tve_sub_btn">
                <div class="tve_sub active_sub_menu" style="display: block;">
                    <ul>
                        <li class="lead_generation_style tve_click" id="thrv_lead_generation_vertical"
                            data-ctrl="controls.lead_generation.style">
                            <div class="lead_generation_image" id="lead_generation_vertical_image"></div>
                            <div>Vertical</div>
                        </li>
                        <li class="lead_generation_style tve_click" id="thrv_lead_generation_horizontal"
                            data-ctrl="controls.lead_generation.style">
                            <div class="lead_generation_image" id="lead_generation_horizontal_image"></div>
                            <div>Horizontal</div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </li>
    <li class="tve_ed_btn tve_btn_text">
        <div class="tve_option_separator">
            <span class="tve_ind tve_left">Dimensions</span><span id="sub_02" class="tve_caret tve_icm tve_left"></span>

            <div class="tve_clear"></div>
            <div class="tve_sub_btn">
                <div class="tve_sub active_sub_menu tve_lg_dimensions_dropdown tve_clearfix" style="display: block;">
                    <ul>
                        <li class="tve_text tve_slider_config tve_clearfix" data-value="300" data-min-value="200"
                            data-property="max-width"
                            data-max-value="available"
                            data-input-selector="#lead_generation_width_input" data-callback="lead_generation">
                            <label for="lead_generation_width_input" class="tve_left">&nbsp;Form size</label>

                            <div class="tve_slider tve_left">
                                <div class="tve_slider_element" id="tve_lead_generation_size_slider"></div>
                            </div>
                            <input class="tve_left" type="text" id="lead_generation_width_input" value="50px">

                            <div id="tve_fullwidthBtn"
                                 class="tve_ed_btn tve_btn_text tve_center btn_alignment tve_left">Full Width
                            </div>
                            <div class="clear"></div>
                        </li>
                        <?php /* removed input heights, as it seems it caused issues for a lot of users
                        <li class="tve_text tve_slider_config tve_clearfix" data-min-value="30" data-property="height"
                            data-max-value="100"
                            data-selector="function:controls.lead_generation.heights_selector"
                            data-input-selector="#lead_generation_height_input" data-callback="lead_generation">
                            <label for="lead_generation_height_input" class="tve_left">&nbsp;Inputs height</label>

                            <div class="tve_slider tve_left">
                                <div class="tve_slider_element" id="tve_lead_generation_height_slider"></div>
                            </div>
                            <input class="tve_left" type="text" id="lead_generation_height_input" value="">

                            <div data-ctrl="function:controls.lead_generation.clear_inputs_heights"
                                 data-args="function:controls.lead_generation.heights_selector,.edit_mode"
                                 class="tve_ed_btn tve_btn_text tve_center tve_left tve_click tve_no_click">Clear Inputs Height
                            </div>
                            <div class="clear"></div>
                        </li> */ ?>
                        <li class="tve_text tve_slider_config tve_clearfix" data-min-value="100" data-property="max-width"
                            data-max-value="available"
                            data-selector="function:controls.lead_generation.widths_selector"
                            data-input-selector="#lead_generation_widths_input" data-callback="lead_generation">
                            <label for="lead_generation_height_input" class="tve_left">&nbsp;Inputs width</label>

                            <div class="tve_slider tve_left">
                                <div class="tve_slider_element" id="tve_lead_generation_widths_slider"></div>
                            </div>
                            <input class="tve_left" type="text" id="lead_generation_widths_input" value="">

                            <div data-ctrl="function:controls.lead_generation.clear_inputs_widths"
                                 data-args="function:controls.lead_generation.widths_selector,.edit_mode"
                                 class="tve_ed_btn tve_btn_text tve_center tve_left tve_click tve_no_click">Clear Inputs Width
                            </div>
                            <div class="clear"></div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </li>
    <li class="tve_btn_text">
        <label class="tve_text">&nbsp;Align:&nbsp; </label>
    </li>
    <li id="tve_leftBtn" class="btn_alignment tve_alignment_left">
        Left
    </li>
    <li id="tve_centerBtn" class="btn_alignment tve_alignment_center">
        Center
    </li>
    <li id="tve_rightBtn" class="btn_alignment tve_alignment_right">
        Right
    </li>
    <?php include dirname(__FILE__) . '/_font_size.php' ?>
    <li>
        <label>
            <input id="lead_generation_form_target" class="tve_text tve_change" type="checkbox" value="_blank"/> Open in
            new window
        </label>
    </li>
    <li class="tve_ed_btn tve_btn_text">
        <div class="tve_option_separator">
            <span class="tve_ind tve_left">Set Error Messages</span>
            <span class="tve_caret tve_icm tve_left"></span>

            <div class="tve_clear"></div>
            <div class="tve_sub_btn">
                <div class="tve_sub active_sub_menu tve_lg_errors tve_clearfix">
                    <ul>
                        <li class="tve_clearfix">
                            <label for="tve_lg_email_error">Email address invalid:</label>
                            <textarea id="tve_lg_email_error" class="tve_change"></textarea>
                        </li>
                        <li class="tve_clearfix">
                            <label for="tve_lg_phone_error">Phone number invalid</label>
                            <textarea id="tve_lg_phone_error" class="tve_change"></textarea>
                        </li>
                        <li class="tve_clearfix">
                            <label for="tve_lg_required_error">Required field missing</label>
                            <textarea id="tve_lg_required_error" class="tve_change"></textarea>
                        </li>
                        <li><div data-ctrl="function:controls.lead_generation.reset_errors" class="tve_ed_btn tve_btn_text tve_center tve_right tve_click tve_no_click">Reset errors to default</div></li>
                    </ul>
                </div>
            </div>
        </div>
    </li>
    <li class="tve_ed_btn tve_btn_text">
        <div class="tve_option_separator">
            <span class="tve_ind tve_left">Edit Components</span>
            <span class="tve_caret tve_icm tve_left"></span>

            <div class="tve_clear"></div>
            <div class="tve_sub_btn" style="min-width: 137px">
                <div class="tve_sub active_sub_menu">
                    <ul>
                        <li class="tve_clearfix tve_click" data-ctrl="controls.click.toggle_menu"
                            data-args="lead_generation_input,input[type='text']">Text Inputs
                        </li>
                        <li class="tve_clearfix tve_click" data-ctrl="controls.click.toggle_menu"
                            data-args="lead_generation_submit,button">Submit Button
                        </li>
                        <li class="tve_clearfix tve_click" data-ctrl="controls.click.toggle_menu"
                            data-args="lead_generation_textarea,textarea">Textareas
                        </li>
                        <li class="tve_clearfix tve_click" data-ctrl="controls.click.toggle_menu"
                            data-args="lead_generation_dropdown,select">Dropdown Lists
                        </li>
                        <li class="tve_clearfix tve_click" data-ctrl="controls.click.toggle_menu"
                            data-args="lead_generation_checkbox,.tve_lg_checkbox">Checkbox Inputs
                        </li>
                        <li class="tve_clearfix tve_click" data-ctrl="controls.click.toggle_menu"
                            data-args="lead_generation_radio,.tve_lg_radio">Radio Inputs
                        </li>
                        <li class="tve_clearfix tve_click" data-ctrl="controls.click.toggle_menu"
                            data-args="lead_generation_image_submit,input[type='image']">Submit Image
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </li>
</ul>
<div id="lead_generation_checkbox_menu" class="tve_clearfix tve_lg_custom_menu" style="display: none">
    <?php include_once dirname(__FILE__) . "/lead_generation_checkbox.php"; ?>
</div>
<div id="lead_generation_dropdown_menu" class="tve_clearfix tve_lg_custom_menu" style="display: none">
    <?php include_once dirname(__FILE__) . "/lead_generation_dropdown.php"; ?>
</div>
<div id="lead_generation_image_submit_menu" class="tve_clearfix tve_lg_custom_menu" style="display: none">
    <?php include_once dirname(__FILE__) . "/lead_generation_image_submit.php"; ?>
</div>
<div id="lead_generation_input_menu" class="tve_clearfix tve_lg_custom_menu" style="display: none">
    <?php include_once dirname(__FILE__) . "/lead_generation_input.php"; ?>
</div>
<div id="lead_generation_radio_menu" class="tve_clearfix tve_lg_custom_menu" style="display: none">
    <?php include_once dirname(__FILE__) . "/lead_generation_radio.php"; ?>
</div>
<div id="lead_generation_submit_menu" class="tve_clearfix tve_lg_custom_menu" style="display: none">
    <?php include_once dirname(__FILE__) . "/lead_generation_submit.php"; ?>
</div>
<div id="lead_generation_textarea_menu" class="tve_clearfix tve_lg_custom_menu" style="display: none">
    <?php include_once dirname(__FILE__) . "/lead_generation_textarea.php"; ?>
</div>
