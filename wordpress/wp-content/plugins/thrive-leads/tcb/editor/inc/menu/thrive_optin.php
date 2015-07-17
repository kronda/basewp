<span class="tve_options_headline"><span class="tve_icm tve-ic-move"></span>Thrive OptIn options</span>
<ul class="tve_menu">
    <li class="tve_ed_btn tve_btn_text tve_firstOnRow">
        <div class="tve_option_separator">
            <span class="tve_ind tve_left" data-default="Choose Thrive Opt-In">Choose Thrive Opt-In</span><span
                class="tve_caret tve_icm tve_left"></span>

            <div class="tve_clear"></div>
            <div class="tve_sub_btn">
                <div class="tve_sub active_sub_menu">
                    <ul>
                        <?php foreach ($thrive_optins as $_id => $_title) : ?>
                            <li data-value="<?php echo $_id ?>"
                                class="tve_click tve-o-optin" data-ctrl="controls.thrive_optin.option" data-fn="fetch"><?php echo $_title ?></li>
                        <?php endforeach ?>
                    </ul>
                </div>
            </div>
        </div>
    </li>
    <li class="tve_ed_btn tve_btn_text">
        <div class="tve_option_separator">
            <span class="tve_ind tve_left" data-default="Layout">Layout</span><span
                class="tve_caret tve_icm tve_left"></span>

            <div class="tve_clear"></div>
            <div class="tve_sub_btn">
                <div class="tve_sub active_sub_menu">
                    <ul>
                        <li data-value="vertical" class="tve_click tve-o-layout" data-ctrl="controls.thrive_optin.option" data-fn="layout">Vertical</li>
                        <li data-value="horizontal" class="tve_click tve-o-layout" data-ctrl="controls.thrive_optin.option" data-fn="layout">Horizontal</li>
                    </ul>
                </div>
            </div>
        </div>
    </li>
    <li class="tve_ed_btn tve_btn_text">
        <div class="tve_option_separator">
            <span class="tve_ind tve_left" data-default="Button color">Button color</span><span
                class="tve_caret tve_icm tve_left"></span>

            <div class="tve_clear"></div>
            <div class="tve_sub_btn">
                <div class="tve_sub active_sub_menu">
                    <ul>
                        <?php foreach ($thrive_optin_colors as $_key => $_color) : ?>
                            <li data-value="<?php echo $_key ?>"
                                class="tve_click tve-o-color" data-ctrl="controls.thrive_optin.option" data-fn="color"><?php echo $_color ?></li>
                        <?php endforeach ?>
                    </ul>
                </div>
            </div>
        </div>
    </li>
    <li class="tve_ed_btn tve_btn_text">
        <div class="tve_option_separator">
            <span class="tve_ind tve_left" data-default="Button size">Button size</span><span
                class="tve_caret tve_icm tve_left"></span>

            <div class="tve_clear"></div>
            <div class="tve_sub_btn">
                <div class="tve_sub active_sub_menu">
                    <ul>
                        <li data-value="small" class="tve_click tve-o-size" data-ctrl="controls.thrive_optin.option" data-fn="size">Small</li>
                        <li data-value="medium" class="tve_click tve-o-size" data-ctrl="controls.thrive_optin.option" data-fn="size">Medium</li>
                        <li data-value="big" class="tve_click tve-o-size" data-ctrl="controls.thrive_optin.option" data-fn="size">Big</li>
                    </ul>
                </div>
            </div>
        </div>
    </li>
    <li><input type="text" id="tve_thrive_optin_text" class="tve_change" placeholder="Button text"></li>
    <li class="tve_clear"></li>
    <?php include dirname(__FILE__) . '/_margin.php' ?>
</ul>