<span class="tve_options_headline"><span class="tve_icm tve-ic-move"></span><?php echo __("General text element menu", "thrive-cb") ?></span>
<ul class="tve_menu">
    <li class="tve_ed_btn tve_btn_text">
        <div class="tve_option_separator tve_mousedown" data-ctrl="controls.prevent_default">
            <i class="tve_icm tve-ic-color-lens tve_left"></i><span
                class="tve_caret tve_icm tve_left" id="sub_01"></span>

            <div class="tve_clear"></div>
            <div class="tve_sub_btn">
                <div class="tve_sub active_sub_menu color_selector" id="tve_sub_01_s">
                    <ul class="tve_default_colors tve_left">
                        <li class="tve_color_title"><span class="tve_options_headline"><?php echo __("Default Colors", "thrive-cb") ?></span></li>
                        <li class="tve_clear"></li>
                        <li class="tve_black"><a href="#"></a></li>
                        <li class="tve_blue"><a href="#"></a></li>
                        <li class="tve_green"><a href="#"></a></li>
                        <li class="tve_orange"><a href="#"></a></li>
                        <li class="tve_clear"></li>
                        <li class="tve_purple"><a href="#"></a></li>
                        <li class="tve_red"><a href="#"></a></li>
                        <li class="tve_teal"><a href="#"></a></li>
                        <li class="tve_white"><a href="#"></a></li>
                    </ul>
                    <div class="tve_color_picker tve_left">
                        <span class="tve_options_headline tve_color_title"><?php echo __("Custom Colors", "thrive-cb") ?></span>

                        <div class="tve_colour_pickers">
                            <input type="text" class="text_colour_picker" data-default-color="#000000">
                        </div>
                    </div>
                    <div class="tve_clear"></div>
                </div>
            </div>
        </div>
    </li>
    <li class="tve_ed_btn tve_btn_icon">
        <div class="tve_icm tve-ic-bold tve_mousedown" data-ctrl="controls.rangy_cls" data-command="bold" title="<?php echo __("Bold", "thrive-cb") ?>"></div>
    </li>
    <li class="tve_ed_btn tve_btn_icon">
        <div class="tve_icm tve-ic-italic tve_mousedown" data-ctrl="controls.rangy_cls" data-command="italic" title="<?php echo __("Italic", "thrive-cb") ?>"></div>
    </li>
    <li class="tve_ed_btn tve_btn_icon">
        <div class="tve_icm tve-ic-underline tve_mousedown" data-ctrl="controls.rangy_cls" data-command="underline" title="<?php echo __("Underline", "thrive-cb") ?>"></div>
    </li>
    <li class="tve_ed_btn tve_btn_icon">
        <div class="tve_icm tve-ic-strikethrough tve_mousedown" data-ctrl="controls.rangy_cls" data-command="strikethrough" title="<?php echo __("Strike-through", "thrive-cb") ?>"></div>
    </li>
    <li class="tve_ed_btn tve_btn_icon">
        <div class="tve_icm tve-ic-paragraph-left tve_click" title="<?php echo __("Text align left", "thrive-cb") ?>" data-ctrl="controls.click.text_align" data-cls="tve_p_left"></div>
    </li>
    <li class="tve_ed_btn tve_btn_icon">
        <div class="tve_icm tve-ic-paragraph-center tve_click" title="<?php echo __("Text align center", "thrive-cb") ?>" data-ctrl="controls.click.text_align" data-cls="tve_p_center"></div>
    </li>
    <li class="tve_ed_btn tve_btn_icon">
        <div class="tve_icm tve-ic-paragraph-right tve_click" title="<?php echo __("Text align right", "thrive-cb") ?>" data-ctrl="controls.click.text_align" data-cls="tve_p_right"></div>
    </li>
    <li class="tve_ed_btn tve_btn_icon">
        <div class="tve_icm tve-ic-paragraph-justify tve_click" title="<?php echo __("Text align justify", "thrive-cb") ?>" data-ctrl="controls.click.text_align" data-cls="tvealignjustify"></div>
    </li>
    <li>
        <label>
            <?php echo __("Font Size", "thrive-cb") ?> <input class="tve_text tve_font_size tve_change tve_mousedown" data-ctrl-mousedown="controls.save_selection" data-key="textSel" type="text" size="3" maxlength="3"/> px
        </label>
    </li>
    <li class="tve_ed_btn tve_btn_text tve_click" id="tve_clear_font_size"><?php echo __("Clear font size", "thrive-cb") ?></li>
    <li class="tve_ed_btn tve_btn_text">
        <div class="tve_option_separator">
            <span class="tve_ind tve_left" data-default="Custom Font"><?php echo __("Custom Font", "thrive-cb") ?></span><span class="tve_caret tve_icm tve_left" id="sub_02"></span>

            <div class="tve_clear"></div>
            <div class="tve_sub_btn">
                <div class="tve_sub active_sub_menu tve_medium" style="min-width: 220px">
                    <ul>
                        <?php foreach ($fonts as $font): ?>
                            <li style="font-size:15px;line-height:28px" class="tve_click tve_font_selector <?php echo $font['font_class'] ?>"
                                data-cls="<?php echo $font['font_class'] ?>"><?php echo $font['font_name'] . ' ' . $font['font_size'] ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="tve_clear"></div>
                </div>
            </div>
        </div>
    </li>
    <li class="tve_ed_btn tve_btn_text tve_click" id="tve_clear_custom_font"><?php echo __("Clear custom font", "thrive-cb") ?></li>
    <li>
        <input type="text" class="tve_text tve_change element_id" data-ctrl="controls.change.element_id" placeholder="<?php echo __("ID", "thrive-cb") ?>">
    </li>
    <li><input type="text" class="element_class tve_text tve_change" data-ctrl="controls.change.cls" placeholder="<?php echo __("Custom class", "thrive-cb") ?>"></li>

    <!-- this only shows when the user clicks on a hyperlink -->
    <li class="tve_link_btns tve_firstOnRow" style="clear:both"><span class="" id="text_h6">
            <input type="text" id="link_anchor" placeholder="<?php echo __("Anchor Text", "thrive-cb") ?>" class="tve_change" data-ctrl="controls.change.link_text"/></span>
    </li>
    <li class="tve_link_btns"><span class="" id="text_h6">
            <input type="text" id="link_url" placeholder="<?php echo __("URL", "thrive-cb") ?>" class="tve_change" data-ctrl="controls.change.link_url"/>
        </span></li>
    <li class="tve_link_btns"><span class="" id="text_h6"><input type="text" id="anchor_name" class="tve_change" data-ctrl="controls.change.link_name"
                                                                 placeholder="<?php echo __("Anchor name", "thrive-cb") ?>"/></span></li>
    <li class="tve_text tve_link_btns">
        <input type="checkbox" id="link_new_window" data-ctrl="controls.change.link_target">
        <label for="link_new_window"><?php echo __("Open link in new window?", "thrive-cb") ?></label>
    </li>
    <li class="tve_ed_btn tve_link_btns">
        <span class="tve_icm tve-ic-unlink tve_click tve_mousedown" data-ctrl-mousedown="controls.save_selection" data-key="linkSel" data-ctrl="controls.text_unlink"></span>
    </li>
    <?php include dirname(__FILE__) . '/_line_height.php' ?>
</ul>