<span class="tve_options_headline"><span class="tve_icm tve-ic-move"></span>Custom Menu options</span>
<ul class="tve_menu">
    <?php $has_custom_colors = true; include dirname(__FILE__) . '/_custom_colors.php' ?>
    <li class="tve_ed_btn tve_btn_text">
        <div class="tve_option_separator">
            <span class="tve_ind tve_left" data-default="Choose Menu">Choose Menu</span><span
                class="tve_caret tve_icm tve_left"></span>

            <div class="tve_clear"></div>
            <div class="tve_sub_btn">
                <div class="tve_sub active_sub_menu tve_menu_selector">
                    <ul>
                        <?php foreach ($menus as $item) : ?>
                            <li class="tve_click" data-ctrl="controls.widget_menu.menu_changed" data-id="<?php echo $item['id'] ?>"><?php echo $item['name'] ?></li>
                        <?php endforeach ?>
                        <?php if (empty($menus)) : ?>
                            <li style="cursor: default" class="tve_no_click tve_no_hover">No Menu found</li>
                        <?php endif ?>
                    </ul>
                </div>
            </div>
        </div>
    </li>
    <li class="tve_ed_btn tve_btn_text">
        <a target="_blank" style="color: #47bb28" class="menu-edit-link" href="<?php echo $_POST['admin_url'] ?>nav-menus.php?action=edit&menu=" target="_blank">Edit menu</a>
    </li>

    <li class="tve_ed_btn tve_btn_icon">
        <span class="tve_icm tve-ic-paragraph-left tve_click" data-ctrl="controls.click.add_class" data-cls="tve_left"></span>
    </li>
    <li class="tve_ed_btn tve_btn_icon tve_hidden_feature_grid">
        <span class="tve_icm tve-ic-paragraph-center tve_click" data-ctrl="controls.click.add_class" data-cls="tve_center"></span>
    </li>
    <li class="tve_ed_btn tve_btn_icon">
        <span class="tve_icm tve-ic-paragraph-right tve_click" data-ctrl="controls.click.add_class" data-cls="tve_right"></span>
    </li>
    <li class="tve_ed_btn tve_btn_text tve_center tve_click" data-ctrl="controls.click.add_class" data-cls="tve_none">None</li>

    <?php include dirname(__FILE__) . '/_custom_font.php' ?>

    <li class="tve_btn_text">
        <label>
            Font Size <input class="tve_text tve_font_size tve_change" type="text" size="3" maxlength="3"/> px
        </label>
    </li>

    <li class="tve_btn_text">
        <label>
            Title <input class="tve_text tve_menu_title tve_change" type="text" data-ctrl="controls.widget_menu.title_change" />
        </label>
    </li>

    <li class="tve_ed_btn tve_btn_text">
        <div class="tve_option_separator">
            <span class="tve_ind tve_left" data-default="Display">Display ... </span><span
                class="tve_caret tve_icm tve_left"></span>

            <div class="tve_clear"></div>
            <div class="tve_sub_btn">
                <div class="tve_sub active_sub_menu">
                    <ul class="tve_menu_dir">
                        <li class="tve_click" data-ctrl="controls.widget_menu.menu_type" data-type="tve_horizontal">Horizontal</li>
                        <li class="tve_click" data-ctrl="controls.widget_menu.menu_type" data-type="tve_vertical">Vertical</li>
                    </ul>
                </div>
            </div>
        </div>
    </li>

    <?php include dirname(__FILE__) . '/_margin.php' ?>
</ul>