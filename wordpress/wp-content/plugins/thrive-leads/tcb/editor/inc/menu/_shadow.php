<?php
/**
 * Created by PhpStorm.
 * User: radu
 * Date: 06.10.2014
 * Time: 14:38
 *
 * display Box Shadow controls for an element
 *
 */
?>
<li class="tve_ed_btn tve_btn_text tve_shadow_control<?php if (isset($btn_class)) echo ' ' . $btn_class;
unset($btn_class) ?>">
    <div class="tve_option_separator">
        <span class="tve_ind tve_left">Shadow</span><span class="tve_caret tve_icm tve_left" id="sub_02"></span>

        <?php if (!empty($css_selector)) : ?>
            <input type="hidden" class="css-selector" value="<?php echo $css_selector ?>"/>
            <?php unset($css_selector); endif ?>

        <div class="tve_clear"></div>
        <div class="tve_sub_btn">
            <div class="tve_sub active_sub_menu tve_dark tve_clearfix" style="min-width: 510px">
                <ul style="width: 250px" class="tve_left">
                    <li style="text-align: center"><strong>Internal Shadow</strong></li>
                    <li class="tve_no_hover tve_no_click tve_clearfix" style="text-align: center">
                        <div class="tve_ed_btn tve_btn_text tve_click" data-ctrl="controls.shadow.size" data-size="small" data-inset="1" style="display: inline-block;margin-right: 15px;">
                            Small
                        </div>
                        <div class="tve_ed_btn tve_btn_text tve_click" data-ctrl="controls.shadow.size" data-size="medium" data-inset="1" style="display: inline-block;margin-right: 15px;">
                            Medium
                        </div>
                        <div class="tve_ed_btn tve_btn_text tve_click" data-ctrl="controls.shadow.size" data-size="large" data-inset="1" style="display: inline-block;">Large</div>
                    </li>
                    <li class="tve_no_hover tve_no_click tve_clearfix" style="text-align: center">
                        <div class="tve_text tve_slider_config" data-value="0" data-min-value="0"
                             data-handler="void_handler"
                             data-max-value="200"
                             data-step="2"
                             data-callback="function:controls.shadow.inset_slider"
                             data-property="">

                            <div class="tve_slider" style="max-width: 200px; margin: 0 auto">
                                <div class="tve_slider_element tve_slider_shadow tve_inset"></div>
                            </div>

                            <div class="clear"></div>
                        </div>
                    </li>
                    <li class="tve_no_hover tve_no_click tve_arrow_group" style="height: auto; line-height: 1;padding: 10px 0;text-align: center">
                        <div class="tve_icm tve-ic-arrow-up-left tve_click" data-ctrl="controls.shadow.position" data-coords="inset_top-left" title="Top Left"></div>
                        <div class="tve_icm tve-ic-arrow-up tve_click" data-ctrl="controls.shadow.position" data-coords="inset_top" title="Top"></div>
                        <div class="tve_icm tve-ic-arrow-up-right tve_click" data-ctrl="controls.shadow.position" data-coords="inset_top-right" title="Top Right"></div>
                        <div class="tve_clear"></div>
                        <div class="tve_icm tve-ic-arrow-left tve_click" data-ctrl="controls.shadow.position" data-coords="inset_left" title="Left"></div>
                        <div class="tve_icm tve-ic-square-o tve_click" data-ctrl="controls.shadow.position" data-coords="inset_center" title="Middle / Center"></div>
                        <div class="tve_icm tve-ic-arrow-right2 tve_click" data-ctrl="controls.shadow.position" data-coords="inset_right" title="Right"></div>
                        <div class="tve_clear"></div>
                        <div class="tve_icm tve-ic-arrow-down-left tve_click" data-ctrl="controls.shadow.position" data-coords="inset_bottom-left" title="Bottom Left"></div>
                        <div class="tve_icm tve-ic-arrow-down tve_click" data-ctrl="controls.shadow.position" data-coords="inset_bottom" title="Bottom"></div>
                        <div class="tve_icm tve-ic-arrow-down-right tve_click" data-ctrl="controls.shadow.position" data-coords="inset_bottom-right" title="Bottom Right"></div>
                    </li>
                    <li class="tve_no_hover tve_no_click" style="text-align: center;">
                        <div style="display: inline-block" class="tve_ed_btn tve_btn_text tve_click tve_center" data-inset="1" data-ctrl="controls.shadow.clear">Clear Internal Shadow</div>
                    </li>
                    <li class="tve_no_hover tve_no_click">&nbsp;</li>
                </ul>
                <ul style="width: 250px" class="tve_left">
                    <li style="text-align: center"><strong>External Shadow</strong></li>
                    <li class="tve_no_hover tve_no_click tve_clearfix" style="text-align: center">
                        <div class="tve_ed_btn tve_btn_text tve_click" data-ctrl="controls.shadow.size" data-size="small" style="display: inline-block;margin-right: 15px;">Small</div>
                        <div class="tve_ed_btn tve_btn_text tve_click" data-ctrl="controls.shadow.size" data-size="medium" style="display: inline-block;margin-right: 15px;">Medium</div>
                        <div class="tve_ed_btn tve_btn_text tve_click" data-ctrl="controls.shadow.size" data-size="large" style="display: inline-block;">Large</div>
                    </li>
                    <li class="tve_no_hover tve_no_click tve_clearfix" style="text-align: center">
                        <div class="tve_text tve_slider_config" data-value="0" data-min-value="0"
                             data-handler="void_handler"
                             data-max-value="100"
                             data-step="2"
                             data-callback="function:controls.shadow.outset_slider"
                             data-property="">

                            <div class="tve_slider" style="max-width: 200px; margin: 0 auto">
                                <div class="tve_slider_element tve_slider_shadow tve_outer"></div>
                            </div>

                            <div class="clear"></div>
                        </div>
                    </li>
                    <li class="tve_no_hover tve_no_click tve_arrow_group" style="height: auto; line-height: 1;padding: 10px 0;text-align: center">
                        <div class="tve_icm tve-ic-arrow-up-left tve_click" data-ctrl="controls.shadow.position" data-coords="top-left" title="Top Left"></div>
                        <div class="tve_icm tve-ic-arrow-up tve_click" data-ctrl="controls.shadow.position" data-coords="top" title="Top"></div>
                        <div class="tve_icm tve-ic-arrow-up-right tve_click" data-ctrl="controls.shadow.position" data-coords="top-right" title="Top Right"></div>
                        <div class="tve_clear"></div>
                        <div class="tve_icm tve-ic-arrow-left tve_click" data-ctrl="controls.shadow.position" data-coords="left" title="Left"></div>
                        <div class="tve_icm tve-ic-square-o tve_click" data-ctrl="controls.shadow.position" data-coords="center" title="Middle / Center"></div>
                        <div class="tve_icm tve-ic-arrow-right2 tve_click" data-ctrl="controls.shadow.position" data-coords="right" title="Right"></div>
                        <div class="tve_clear"></div>
                        <div class="tve_icm tve-ic-arrow-down-left tve_click" data-ctrl="controls.shadow.position" data-coords="bottom-left" title="Bottom Left"></div>
                        <div class="tve_icm tve-ic-arrow-down tve_click" data-ctrl="controls.shadow.position" data-coords="bottom" title="Bottom"></div>
                        <div class="tve_icm tve-ic-arrow-down-right tve_click" data-ctrl="controls.shadow.position" data-coords="bottom-right" title="Bottom Right"></div>
                    </li>
                    <li class="tve_no_hover tve_no_click" style="text-align: center;">
                        <div style="display: inline-block" class="tve_ed_btn tve_btn_text tve_click tve_center" data-ctrl="controls.shadow.clear">Clear External Shadow</div>
                    </li>
                    <li class="tve_no_hover tve_no_click">&nbsp;</li>
                </ul>
                <p>Please note - You can use the color picker tool to set the color of the internal and external shadow</p>
            </div>
        </div>
    </div>
</li>