<span class="tve_options_headline"><span class="tve_icm tve-ic-move"></span><?php echo __("Text element menu", "thrive-cb") ?></span>
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
                        <div class="tve_clear"></div>
                        <div class="tve_remove_color_formatting tve_left">
                            <span class="tve_left tve_options_headline tve_click" data-ctrl="controls.text.remove_color" data-mode="foreground"><i class="tve_left tve_icm tve-ic-eraser"></i> <?php echo __("Clear text color", "thrive-cb") ?></span>
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
                            <input type="text" class="text_colour_picker" data-highlight="1" data-default-color="#000000">
                        </div>
                        <div class="tve_clear"></div>

                        <div class="tve_remove_color_formatting tve_left">
                            <span class="tve_left tve_options_headline tve_click" data-ctrl="controls.text.remove_color" data-mode="background"><i class="tve_left tve_icm tve-ic-eraser"></i> <?php echo __("Clear highlight", "thrive-cb") ?></span>
                        </div>
                        <div class="tve_clear"></div>
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
        <div class="tve_icm tve-ic-list2 tve_click" id="text_bullet" title="<?php echo __("Unordered List", "thrive-cb") ?>"></div>
    </li>
    <li class="tve_ed_btn tve_btn_icon">
        <div class="tve_icm tve-ic-numbered-list tve_click" id="text_numbered_bullet" title="<?php echo __("Numbered List", "thrive-cb") ?>"></div>
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
    <li class="tve_ed_btn tve_btn_icon tve_nolink_btns">
        <span class="tve_click tve_lb_small tve_icm tve-ic-chain tve_mousedown" data-ctrl-click="controls.lb_open" data-ctrl-mousedown="controls.save_selection" data-key="linkSel" id="lb_text_link" title="<?php echo __("Create link", "thrive-cb") ?>"></span>
    </li>
    <li class="tve_ed_btn tve_link_btns">
        <span class="tve_icm tve-ic-unlink tve_click tve_mousedown" data-key="linkSel" data-ctrl-mousedown="controls.save_selection" data-ctrl="controls.text_unlink"></span>
    </li>
    <?php if (empty($_POST['disabled_controls']['more_link'])) : ?>
        <li class="tve_ed_btn tve_btn_icon">
            <span class="tve_icm tve-ic-more-horiz tve_click" title="<?php echo __("Insert more link", "thrive-cb") ?>" data-ctrl="controls.click.more_link"></span>
        </li>
    <?php endif ?>
    <li class="tve_ed_btn tve_btn_text">
        <div class="tve_option_separator">
            <span class="tve_ind tve_left" data-default="Formatting"><?php echo __("Formatting", "thrive-cb") ?></span>
            <span class="tve_caret tve_icm tve_left" id="sub_02"></span>

            <div class="tve_clear"></div>
            <div class="tve_sub_btn">
                <div class="tve_sub active_sub_menu">
                    <ul>
                        <li class="tve_click tve_block_change" data-ctrl="controls.click.block_change" data-tag="p"><?php echo __("Paragraph", "thrive-cb") ?></li>
                        <li class="tve_click tve_block_change" data-ctrl="controls.click.block_change" data-tag="address"><?php echo __("Address", "thrive-cb") ?></li>
                        <li class="tve_click tve_block_change" data-ctrl="controls.click.block_change" data-tag="pre"><?php echo __("Preformatted", "thrive-cb") ?></li>
                        <li class="tve_click tve_block_change" data-ctrl="controls.click.block_change" data-tag="blockquote"><?php echo __("Blockquote", "thrive-cb") ?></li>
                        <li class="tve_click tve_block_change" data-ctrl="controls.click.block_change" data-tag="h1"><?php echo sprintf(__("Heading %s", "thrive-cb"), "1") ?></li>
                        <li class="tve_click tve_block_change" data-ctrl="controls.click.block_change" data-tag="h2"><?php echo sprintf(__("Heading %s", "thrive-cb"), "2") ?></li>
                        <li class="tve_click tve_block_change" data-ctrl="controls.click.block_change" data-tag="h3"><?php echo sprintf(__("Heading %s", "thrive-cb"), "3") ?></li>
                        <li class="tve_click tve_block_change" data-ctrl="controls.click.block_change" data-tag="h4"><?php echo sprintf(__("Heading %s", "thrive-cb"), "4") ?></li>
                        <li class="tve_click tve_block_change" data-ctrl="controls.click.block_change" data-tag="h5"><?php echo sprintf(__("Heading %s", "thrive-cb"), "5") ?></li>
                        <li class="tve_click tve_block_change" data-ctrl="controls.click.block_change" data-tag="h6"><?php echo sprintf(__("Heading %s", "thrive-cb"), "6") ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </li>
    <li class="tve_btn_text">
        <label>
            <?php echo __("Font Size", "thrive-cb") ?> <input class="tve_text tve_font_size tve_change tve_mousedown" data-ctrl-mousedown="controls.save_selection" data-key="textSel" type="text" size="3" maxlength="3"/> px
        </label>
    </li>
    <li class="tve_ed_btn tve_btn_text tve_click" id="tve_clear_font_size"><?php echo __("Clear font size", "thrive-cb") ?></li>
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
    <li>
        <input type="text" class="tve_change tve_text element_id" placeholder="<?php echo __("ID", "thrive-cb") ?>" data-ctrl="controls.change.element_id">
    </li>
    <li><input type="text" class="element_class tve_text tve_change" data-ctrl="controls.change.cls" placeholder="<?php echo __("Custom class", "thrive-cb") ?>"></li>
    <li class="menu-sep">&nbsp;</li>
    <?php include dirname(__FILE__) . '/_margin.php' ?>
    <?php include dirname(__FILE__) . '/_line_height.php' ?>

    <!-- this only shows when the user clicks on a hyperlink -->

    <?php $li_custom_class = 'tve_link_btns'; $li_custom_style = 'style="display: none"'; include dirname(__FILE__) . '/_event_manager.php' ?>
    <li class="tve_link_btns tve_firstOnRow"><span class="" id="text_h6">
            <input type="text" id="link_anchor" placeholder="<?php echo __("Anchor Text", "thrive-cb") ?>" class="tve_change" data-ctrl="controls.change.link_text"/></span>
    </li>
    <li class="tve_link_btns"><span class="" id="text_h6">
            <input type="text" id="link_url" placeholder="<?php echo __("URL", "thrive-cb") ?>" class="tve_change" data-ctrl="controls.change.link_url"/>
        </span></li>
    <li class="tve_link_btns"><span class="" id="text_h6"><input type="text" id="anchor_name" class="tve_change" data-ctrl="controls.change.link_name"
                                                                 placeholder="<?php echo __("Anchor name", "thrive-cb") ?>"/></span></li>
    <li class="tve_text tve_link_btns clearfix">
        <input type="checkbox" id="link_new_window" class="tve_left tve_change" data-ctrl="controls.change.link_target">
        <label for="link_new_window" class="tve_left"><?php echo __("Open link in new window?", "thrive-cb") ?></label>
    </li>
    <li class="tve_text tve_link_btns clearfix">
        <input type="checkbox" id="link_no_follow" class="tve_change" data-ctrl="controls.change.link_rel" data-value="nofollow">
        <label for="link_no_follow" class="tve_left"><?php echo __("Make Link no follow?", "thrive-cb") ?></label>
    </li>
</ul>