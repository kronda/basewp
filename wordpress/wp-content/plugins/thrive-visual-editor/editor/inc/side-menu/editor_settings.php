<?php $is_landing_page_related = empty($_POST['landing_page_lightbox']) && empty($_POST['landing_page']) ?>
<div class="tve_cpanel_sec">
    <?php if (count($_POST['style_families']) > 1) : ?>
        <div class="tve_option_separator tve_dropdown_submenu tve_drop_style tve_left">
            <div class="tve_ed_btn tve_btn_text" style="">
                <div class="tve_icm tve-ic-asterisk tve_left tve_sub_btn" id="sub_02"></div>
                <span class="tve_left tve_expanded"><?php echo empty($_POST['loaded_style']) ? 'Style Family' : $_POST['loaded_style'] ?></span>

                <div class="tve_clear"></div>
            </div>
            <div class="tve_sub_btn">
                <div class="tve_sub active_sub_menu">
                    <ul>
                        <?php foreach ($_POST['style_families'] as $style_family_name => $style_family_location): ?>
                            <li id="tve_style_family_<?php echo strtolower($style_family_name); ?>"
                                class="tve_btn_style_family tve_click" data-skip-undo="1" data-ctrl="controls.click.style_family"><?php echo $style_family_name; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    <?php endif ?>
    <div class="tve_clear"></div>
</div>
<div class="tve_cpanel_sec tve_btn_collapse">
    <div class="tve_cpanel_head tve_clearfix tve_click" id="tve_collapse_editor_btn" data-skip-undo="1" data-ctrl="controls.click.collapse_editor">
        <div class="tve_icm ed-side-right tve-ic-arrow-right2 tve_left"></div>
        <div class="tve_icm ed-side-left tve-ic-arrow-left tve_left"></div>
        <span class="tve_left tve_expanded"><?php echo __("Collapse Editor", "thrive-cb") ?></span>
    </div>
    <a href="" class="tve_logo">
        <span class="tve_cpanel_logo"></span>
    </a>

    <div class="tve_clear"></div>
</div>