<?php $landing_pages = tve_get_landing_page_templates() ?>
<div class="tve_cpanel_sec tve_lp_sub" style="padding-right: 14px;">
    <div class="tve_option_separator tve_dropdown_submenu tve_drop_style ">
        <div class="tve_ed_btn tve_btn_text" style="display: block;">
            <span id="sub_02" class="tve_caret tve_icm tve_right tve_sub_btn tve_expanded" style="margin-top: -3px; margin-left: 4px;"></span>
            <span class="tve_expanded">Thrive Landing Pages</span>

            <div class="tve_clear"></div>
        </div>
        <div class="tve_sub_btn">
            <div class="tve_sub" style="bottom: auto;top: 30px;width: 159px;">
                <ul>
                    <?php if ($landing_page_template) : ?>
                        <li class="tve_click" id="tve_lp_settings">
                            Landing Page Settings
                        </li>
                    <?php endif ?>
                    <li class="tve_click" data-ctrl="controls.lb_open" id="lb_landing_pages">
                        Choose landing page ...
                        <input type="hidden" name="landing_page" value="<?php echo $_POST['landing_page'] ?>"/>
                        <?php foreach ($landing_pages as $code => $data) : ?>
                            <input type="hidden" name="landing_pages[<?php echo $code ?>][name]" value="<?php echo $data['name'] ?>"/>
                            <input type="hidden" name="landing_pages[<?php echo $code ?>][thumbnail]"
                                   value="<?php echo $landing_page_dir . '/thumbnails/' . $code . '.png' ?>"/>
                            <?php if (!empty($data['tags'])) : ?>
                                <?php foreach ($data['tags'] as $tag) : ?>
                                    <input type="hidden" name="landing_pages[<?php echo $code ?>][tags][]" value="<?php echo $tag ?>"/>
                                <?php endforeach ?>
                            <?php endif ?>
                        <?php endforeach ?>
                    </li>
                    <?php if ($landing_page_template) : ?>
                        <li class="tve_click" id="tve_landing_page_disable">Revert to theme</li>
                        <li class="tve_click" id="tve_landing_page_reset" style="color: red;">Reset Landing Page</li>
                    <?php endif ?>
                </ul>
            </div>
        </div>
    </div>
    <div class="tve_clear"></div>
</div>