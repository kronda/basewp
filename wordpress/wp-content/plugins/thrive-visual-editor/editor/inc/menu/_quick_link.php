
<li class="tve_btn_text<?php if (isset($btn_class)) echo ' ' . $btn_class;unset($btn_class) ?> tve_quick_link_input_holder" data-multiple-hide>
    <div class="tve_option_separator">
        <label>
            <?php echo __("Quick Link", "thrive-cb"); ?>
        </label>
        <input class="tve_text tve_keyup tve_mousedown tve_quick_link_input" data-ctrl="controls.quick_link.updateContentList" type="text" data-ctrl-mousedown="controls.save_selection" data-key="linkSel"/>
        <div class="tve_sub_btn">
            <div class="tve_sub active_sub_menu tve_large tve_clearfix" style="min-width:400px">
                <ul>
                    <li class="tve_no_hover tve_no_click">
                        <label class="tve_text">
                            <input type="checkbox" class="tve_change lb_quick_link_target" data-ctrl-change="controls.quick_link.updateQuickLink"/>
                            <span class="tve_label_spacer tve_small"><?php echo __("Open in New Window", "thrive-cb"); ?></span>
                        </label>
                    </li>
                </ul>
                <ul>
                    <li class="tve_no_hover tve_no_click">
                        <label class="tve_text">
                            <input type="checkbox" class="tve_change tve_nofollow lb_quick_link_no_follow" data-ctrl-change="controls.quick_link.updateQuickLink"/>
                            <span class="tve_label_spacer tve_small"><?php echo __("No Follow Link", "thrive-cb"); ?></span>
                        </label>
                    </li>
                </ul>
                <div class="tve_clear"></div>
                <div class="quick_link_content_table"></div>
            </div>
        </div>
    </div>
</li>

