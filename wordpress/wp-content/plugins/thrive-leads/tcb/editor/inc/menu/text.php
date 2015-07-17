<span class="tve_options_headline"><span class="tve_icm tve-ic-move"></span>Text element menu</span>
<ul class="tve_menu">
    <li class="tve_ed_btn tve_btn_text">
        <div class="tve_option_separator tve_mousedown" data-ctrl="controls.prevent_default">
            <i class="tve_icm tve-ic-color-lens tve_left"></i><span
                class="tve_caret tve_icm tve_left" id="sub_01"></span>

            <div class="tve_clear"></div>
            <div class="tve_sub_btn">
                <div class="tve_sub active_sub_menu color_selector" id="tve_sub_01_s">
                    <ul class="tve_default_colors tve_left">
                        <li class="tve_color_title"><span class="tve_options_headline">Default Colors</span></li>
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
                        <span class="tve_options_headline tve_color_title">Custom Colors</span>

                        <div class="tve_colour_pickers">
                            <input type="text" class="text_colour_picker" data-default-color="#000000">
                        </div>
                        <div class="tve_clear"></div>
                        <div class="tve_remove_color_formatting tve_left">
                            <span class="tve_left tve_options_headline tve_click" data-ctrl="controls.text.remove_color" data-mode="foreground"><i class="tve_left tve_icm tve-ic-eraser"></i> Clear text color</span>
                        </div>
                        <div class="tve_clear"></div>
                    </div>
                    <div class="tve_clear"></div>
                </div>
            </div>
        </div>
    </li>
    <?php /* colour picker for background color (highlight of current selection */ ?>
    <li class="tve_ed_btn tve_btn_text">
        <div class="tve_option_separator tve_mousedown" data-ctrl="controls.prevent_default">
            <i class="tve_icm tve-ic-brush tve_left"></i><span
                class="tve_caret tve_icm tve_left" id="sub_01"></span>

            <div class="tve_clear"></div>
            <div class="tve_sub_btn tve_highlight_control">
                <div class="tve_sub active_sub_menu color_selector tve_sub_generic" id="tve_sub_01_s">
                    <ul class="tve_default_colors tve_left">
                        <li class="tve_color_title"><span class="tve_options_headline">Default Colors</span></li>
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
                        <span class="tve_options_headline tve_color_title">Custom Colors</span>

                        <div class="tve_colour_pickers">
                            <input type="text" class="text_colour_picker" data-highlight="1" data-default-color="#000000">
                        </div>
                        <div class="tve_clear"></div>

                        <div class="tve_remove_color_formatting tve_left">
                            <span class="tve_left tve_options_headline tve_click" data-ctrl="controls.text.remove_color" data-mode="background"><i class="tve_left tve_icm tve-ic-eraser"></i> Clear highlight</span>
                        </div>
                        <div class="tve_clear"></div>
                    </div>
                    <div class="tve_clear"></div>
                </div>
            </div>
        </div>
    </li>
    <li class="tve_ed_btn tve_btn_icon">
        <div class="tve_icm tve-ic-bold tve_mousedown" data-ctrl="controls.rangy_cls" data-command="bold" title="Bold"></div>
    </li>
    <li class="tve_ed_btn tve_btn_icon">
        <div class="tve_icm tve-ic-italic tve_mousedown" data-ctrl="controls.rangy_cls" data-command="italic" title="Italic"></div>
    </li>
    <li class="tve_ed_btn tve_btn_icon">
        <div class="tve_icm tve-ic-underline tve_mousedown" data-ctrl="controls.rangy_cls" data-command="underline" title="Underline"></div>
    </li>
    <li class="tve_ed_btn tve_btn_icon">
        <div class="tve_icm tve-ic-strikethrough tve_mousedown" data-ctrl="controls.rangy_cls" data-command="strikethrough" title="Strike-through"></div>
    </li>
    <li class="tve_ed_btn tve_btn_icon">
        <div class="tve_icm tve-ic-list2 tve_click" id="text_bullet" title="Unordered List"></div>
    </li>
    <li class="tve_ed_btn tve_btn_icon">
        <div class="tve_icm tve-ic-numbered-list tve_click" id="text_numbered_bullet" title="Numbered List"></div>
    </li>
    <li class="tve_ed_btn tve_btn_icon">
        <div class="tve_icm tve-ic-paragraph-left tve_click" title="Text align left" data-ctrl="controls.click.text_align" data-cls="tve_p_left"></div>
    </li>
    <li class="tve_ed_btn tve_btn_icon">
        <div class="tve_icm tve-ic-paragraph-center tve_click" title="Text align center" data-ctrl="controls.click.text_align" data-cls="tve_p_center"></div>
    </li>
    <li class="tve_ed_btn tve_btn_icon">
        <div class="tve_icm tve-ic-paragraph-right tve_click" title="Text align right" data-ctrl="controls.click.text_align" data-cls="tve_p_right"></div>
    </li>
    <li class="tve_ed_btn tve_btn_icon">
        <div class="tve_icm tve-ic-paragraph-justify tve_click" title="Text align justify" data-ctrl="controls.click.text_align" data-cls="tvealignjustify"></div>
    </li>
    <li class="tve_ed_btn tve_btn_icon tve_nolink_btns">
        <span class="tve_click tve_lb_small tve_icm tve-ic-chain tve_mousedown" data-ctrl-click="controls.lb_open" data-ctrl-mousedown="controls.save_selection" data-key="linkSel" id="lb_text_link" title="Create link"></span>
    </li>
    <li class="tve_ed_btn tve_link_btns">
        <span class="tve_icm tve-ic-unlink tve_click tve_mousedown" data-key="linkSel" data-ctrl-mousedown="controls.save_selection" data-ctrl="controls.text_unlink"></span>
    </li>
    <?php if (empty($_POST['disabled_controls']['more_link'])) : ?>
        <li class="tve_ed_btn tve_btn_icon">
            <span class="tve_icm tve-ic-more-horiz tve_click" title="Insert more link" data-ctrl="controls.click.more_link"></span>
        </li>
    <?php endif ?>
    <li class="tve_ed_btn tve_btn_text">
        <div class="tve_option_separator">
            <span class="tve_ind tve_left" data-default="Formatting">Formatting</span>
            <span class="tve_caret tve_icm tve_left" id="sub_02"></span>

            <div class="tve_clear"></div>
            <div class="tve_sub_btn">
                <div class="tve_sub active_sub_menu">
                    <ul>
                        <li class="tve_click tve_block_change" data-ctrl="controls.click.block_change" data-tag="p">Paragraph</li>
                        <li class="tve_click tve_block_change" data-ctrl="controls.click.block_change" data-tag="address">Address</li>
                        <li class="tve_click tve_block_change" data-ctrl="controls.click.block_change" data-tag="pre">Preformatted</li>
                        <li class="tve_click tve_block_change" data-ctrl="controls.click.block_change" data-tag="blockquote">Blockquote</li>
                        <li class="tve_click tve_block_change" data-ctrl="controls.click.block_change" data-tag="h1">Heading 1</li>
                        <li class="tve_click tve_block_change" data-ctrl="controls.click.block_change" data-tag="h2">Heading 2</li>
                        <li class="tve_click tve_block_change" data-ctrl="controls.click.block_change" data-tag="h3">Heading 3</li>
                        <li class="tve_click tve_block_change" data-ctrl="controls.click.block_change" data-tag="h4">Heading 4</li>
                        <li class="tve_click tve_block_change" data-ctrl="controls.click.block_change" data-tag="h5">Heading 5</li>
                        <li class="tve_click tve_block_change" data-ctrl="controls.click.block_change" data-tag="h6">Heading 6</li>
                    </ul>
                </div>
            </div>
        </div>
    </li>
    <li class="tve_btn_text">
        <label>
            Font Size <input class="tve_text tve_font_size tve_change tve_mousedown" data-ctrl-mousedown="controls.save_selection" data-key="textSel" type="text" size="3" maxlength="3"/> px
        </label>
    </li>
    <li class="tve_ed_btn tve_btn_text tve_click" id="tve_clear_font_size">Clear font size</li>
    <li class="tve_ed_btn tve_btn_text">
        <div class="tve_option_separator">
            <span class="tve_ind tve_left" data-default="Custom Font">Custom Font</span><span class="tve_caret tve_icm tve_left tve_icm" id="sub_02"></span>

            <div class="tve_clear"></div>
            <div class="tve_sub_btn">
                <div class="tve_sub active_sub_menu tve_medium" style="min-width: 220px">
                    <ul>
                        <?php foreach ($fonts as $font): ?>
                            <li style="font-size:15px;line-height:28px" class="tve_click tve_font_selector <?php echo $font['font_class'] ?>"
                                data-cls="<?php echo $font['font_class'] ?>"><?php echo $font['font_name'] . ' ' . $font['font_size'] ?></li>
                        <?php endforeach; ?>
                        <li><a class="tve_link" href="<?php echo $_POST['font_settings_url'] ?>" target="_blank">Add new Custom Font</a></li>
                    </ul>
                    <div class="tve_clear"></div>
                </div>
            </div>
        </div>
    </li>
    <li class="tve_ed_btn tve_btn_text tve_click" id="tve_clear_custom_font">Clear custom font</li>
    <li>
        <input type="text" class="tve_change tve_text element_id" placeholder="ID" data-ctrl="controls.change.element_id">
    </li>
    <li><input type="text" class="element_class tve_text tve_change" data-ctrl="controls.change.cls" placeholder="Custom class"></li>
    <li class="menu-sep">&nbsp;</li>
    <?php include dirname(__FILE__) . '/_margin.php' ?>
    <?php include dirname(__FILE__) . '/_line_height.php' ?>

    <!-- this only shows when the user clicks on a hyperlink -->

    <?php $li_custom_class = 'tve_link_btns'; $li_custom_style = 'style="display: none"'; include dirname(__FILE__) . '/_event_manager.php' ?>
    <li class="tve_link_btns tve_firstOnRow"><span class="" id="text_h6">
            <input type="text" id="link_anchor" placeholder="Anchor Text" class="tve_change" data-ctrl="controls.change.link_text"/></span>
    </li>
    <li class="tve_link_btns"><span class="" id="text_h6">
            <input type="text" id="link_url" placeholder="URL" class="tve_change" data-ctrl="controls.change.link_url"/>
        </span></li>
    <li class="tve_link_btns"><span class="" id="text_h6"><input type="text" id="anchor_name"
                                                                 placeholder="Anchor name"/></span></li>
    <li class="tve_text tve_link_btns clearfix">
        <input type="checkbox" id="link_new_window" class="tve_left tve_change" data-ctrl="controls.change.link_target">
        <label for="link_new_window" class="tve_left">Open link in new window?</label>
    </li>
    <li class="tve_text tve_link_btns clearfix">
        <input type="checkbox" id="link_no_follow" class="tve_change" data-ctrl="controls.change.link_rel" data-value="nofollow">
        <label for="link_no_follow" class="tve_left">Make Link no follow?</label>
    </li>
</ul>