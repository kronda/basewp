<span class="tve_options_headline"><span class="tve_icm tve-ic-move"></span><?php echo __("Number Counter Element", "thrive-cb") ?></span>
<ul class="tve_menu">
    <?php $has_custom_colors = true;
    include dirname(__FILE__) . '/_custom_colors.php' ?>
    <li class="tve_ed_btn tve_btn_text">
        <div class="tve_option_separator">
            <span class="tve_ind tve_left"><?php echo __("Data Settings", "thrive-cb") ?></span><span
                class="tve_caret tve_icm tve_left" id="sub_02"></span>

            <div class="tve_clear"></div>
            <div class="tve_sub_btn">
                <div class="tve_sub active_sub_menu tve_text_left tve_no_click" style="min-width: 260px">
                    <ul>
                        <li class="tve_no_hover tve_no_click">
                            <label class="tve_text">
                                <span class="tve_label_spacer tve_large"><?php echo __("Value: ", "thrive-cb") ?></span>
                                <input type="text" class="tve_change" id="tve_data_element_value_n" data-ctrl="controls.change.data_element_value" placeholder="<?php echo __("Value", "thrive-cb") ?>" value="1" size="15"/>
                            </label>
                        </li>
                        <li class="tve_no_hover tve_no_click">
                            <label class="tve_text">
                                <span class="tve_label_spacer tve_large"><?php echo __("Label: ", "thrive-cb") ?></span>
                                <input type="text" class="tve_change" id="tve_data_element_label_n" data-ctrl="controls.change.data_element_label" placeholder="<?php echo __("Progress bar label", "thrive-cb") ?>" value="1" size="15"/>
                            </label>
                        </li>
                        <li class="tve_no_hover tve_no_click">
                            <label class="tve_text">
                                <span class="tve_label_spacer tve_large"><?php echo __("Unit Before: ", "thrive-cb") ?></span>
                                <input type="text" class="tve_change" id="tve_data_element_unit_b_n" data-ctrl="controls.change.data_element_unit_b" placeholder="" value="1" size="4"/>
                            </label>
                        </li>
                        <li class="tve_no_hover tve_no_click">
                            <label class="tve_text">
                                <span class="tve_label_spacer tve_large"><?php echo __("Unit After: ", "thrive-cb") ?></span>
                                <input type="text" class="tve_change" id="tve_data_element_unit_a_n" data-ctrl="controls.change.data_element_unit_a" placeholder="" value="1" size="4"/>
                            </label>
                        </li>
                        <li class="tve_no_hover tve_no_click">
                            <label class="tve_text">
                                <span class="tve_label_spacer tve_large"><?php echo __("Unit font size: ", "thrive-cb") ?></span>
                                <input type="text" class="tve_change" id="tve_data_unit_font_size_n" data-ctrl="controls.change.data_unit_font_size" placeholder="" value="1" size="2"/> px
                            </label>
                        </li>
                        <li class="tve_no_hover tve_no_click">
                            <label class="tve_text">
                                <span class="tve_label_spacer tve_large"><?php echo __("Label font size: ", "thrive-cb") ?></span>
                                <input type="text" class="tve_change" id="tve_data_element_font_size_n" data-ctrl="controls.change.data_element_font_size" placeholder="" value="1" size="2"/> px
                            </label>
                        </li>
                        <li class="tve_no_hover tve_no_click">
                            <label class="tve_text">
                                <span class="tve_label_spacer tve_large"><?php echo __("Value font size: ", "thrive-cb") ?></span>
                                <input type="text" class="tve_change" id="tve_data_value_font_size_n" data-ctrl="controls.change.data_value_font_size" placeholder="" value="1" size="2"/> px
                            </label>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </li>
    <li class="tve_text">
        <?php echo __("Align:", "thrive-cb") ?>
    </li>
    <li class="tve_ed_btn tve_btn_icon">
        <span class="tve_icm tve-ic-paragraph-left tve_click tve_fill_counter_align" data-cls="alignleft"></span>
    </li>
    <li class="tve_ed_btn tve_btn_icon tve_firstOnRow">
        <span class="tve_icm tve-ic-paragraph-center tve_click tve_fill_counter_align" data-cls="aligncenter"></span>
    </li>
    <li class="tve_ed_btn tve_btn_icon tve_firstOnRow">
        <span class="tve_icm tve-ic-paragraph-right tve_click tve_fill_counter_align" data-cls="alignright"></span>
    </li>
    <li class="tve_ed_btn tve_btn_text">
        <div class="tve_option_separator">
            <span class="tve_ind tve_left" data-default="Custom Font"><?php echo __("Custom Font", "thrive-cb") ?></span><span class="tve_caret tve_icm tve_left tve_icm" id="sub_02"></span>

            <div class="tve_clear"></div>
            <div class="tve_sub_btn">
                <div class="tve_sub active_sub_menu tve_medium" style="min-width: 220px">
                    <ul>
                        <?php foreach ($fonts as $font): ?>
                            <li style="font-size:15px;line-height:28px" class="tve_click tve_font_selector <?php echo $font['font_class'] ?>"
                                data-cls="<?php echo $font['font_class'] ?>"><?php echo $font['font_name'] . ' ' . $font['font_size'] ?></li>
                        <?php endforeach; ?>
                        <li><a class="tve_link" href="<?php echo $_POST['font_settings_url'] ?>" target="_blank"><?php echo __("Add new Custom Font", "thrive-cb") ?></a></li>
                    </ul>
                    <div class="tve_clear"></div>
                </div>
            </div>
        </div>
    </li>
    <li class="tve_ed_btn tve_btn_text tve_click" id="tve_clear_custom_font"><?php echo __("Clear custom font", "thrive-cb") ?></li>
    <?php include dirname(__FILE__) . '/_margin.php' ?>
    <li>
        <input type="text" class="tve_change tve_text element_id" placeholder="ID" data-ctrl="controls.change.element_id">
    </li>
    <li><input type="text" class="element_class tve_text tve_change" data-ctrl="controls.change.cls" placeholder="<?php echo __("Custom class", "thrive-cb") ?>"></li>
</ul>