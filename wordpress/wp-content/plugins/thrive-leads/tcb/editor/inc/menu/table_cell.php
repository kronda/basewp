<span class="tve_options_headline">Table Cell options</span>
<ul class="tve_menu">
    <li class="tve_ed_btn tve_btn_text">
        <div class="tve_option_separator">
            <i class="tve_icm tve-ic-color-lens tve_left"></i>
            <span class="tve_caret tve_icm tve_left" id="sub_01"></span>

            <div class="tve_clear"></div>
            <div class="tve_sub_btn">
                <div class="tve_sub active_sub_menu color_selector" id="tve_sub_01_s">
                    <div class="tve_color_picker tve_left">
                                <span class="tve_options_headline tve_color_title">
                                    Custom Colors
                                </span>
                    </div>
                    <div class="tve_clear"></div>
                </div>
            </div>
        </div>
    </li>
    <li class="tve_ed_btn tve_btn_text">
        <div class="tve_option_separator">
            <span class="tve_ind tve_left" data-default="Border Type">Border Type</span><span
                class="tve_caret tve_icm tve_left"></span>

            <div class="tve_clear"></div>
            <div class="tve_sub_btn">
                <div class="tve_sub active_sub_menu">
                    <ul>
                        <li id="tve_brdr_none" class="tve_click" data-ctrl="controls.click.add_class" data-border="1">none</li>
                        <li id="tve_brdr_dotted" class="tve_click" data-ctrl="controls.click.add_class" data-border="1">dotted</li>
                        <li id="tve_brdr_dashed" class="tve_click" data-ctrl="controls.click.add_class" data-border="1">dashed</li>
                        <li id="tve_brdr_solid" class="tve_click" data-ctrl="controls.click.add_class" data-border="1">solid</li>
                        <li id="tve_brdr_double" class="tve_click" data-ctrl="controls.click.add_class" data-border="1">double</li>
                        <li id="tve_brdr_groove" class="tve_click" data-ctrl="controls.click.add_class" data-border="1">groove</li>
                        <li id="tve_brdr_ridge" class="tve_click" data-ctrl="controls.click.add_class" data-border="1">ridge</li>
                        <li id="tve_brdr_inset" class="tve_click" data-ctrl="controls.click.add_class" data-border="1">inset</li>
                        <li id="tve_brdr_outset" class="tve_click" data-ctrl="controls.click.add_class" data-border="1">outset</li>
                    </ul>
                </div>
            </div>
        </div>
    </li>
    <li class="tve_text clearfix">
        <label class="tve_left">
            <input id="table_cell_border_width" class="tve_change tve_left" value="0" type="text" size="3"
                   data-css-property="border-width"
                   data-suffix="px" data-size="1">&nbsp;px
        </label>
    </li>
    <li class="tve_text clearfix">
        <label class="tve_left" for="table_cell_width">Column Width&nbsp;
            <input id="table_cell_width" class="tve_change" type="text" size="3" value="5" data-size="1"
                   data-suffix="px">&nbsp;px
        </label>
    </li>
    <li class="tve_text">
        <label class="tve_left" for="table_cell_height">Row Height&nbsp;
            <input id="table_cell_height" class="tve_change" type="text" size="3" value="5" data-size="1"
                   data-suffix="px">&nbsp;px
        </label>
    </li>
    <li class="tve_clear"></li>
    <li class="tve_text tve_firstOnRow">Alignment</li>
    <li class="tve_ed_btn tve_btn_icon">
        <div id="tve_align_left" class="tve_icm tve-ic-paragraph-left btn_alignment" title="Text align left"></div>
    </li>
    <li class="tve_ed_btn tve_btn_icon">
        <div id="tve_align_center" class="tve_icm tve-ic-paragraph-center btn_alignment" title="Text align center"></div>
    </li>
    <li class="tve_ed_btn tve_btn_icon">
        <div id="tve_align_right" class="tve_icm tve-ic-paragraph-right btn_alignment" title="Text align right"></div>
    </li>
    <li class="tve_ed_btn tve_btn_icon">
        <div id="tve_align_justify" class="tve_icm tve-ic-paragraph-justify btn_alignment" title="Text align justify"></div>
    </li>
    <li class="tve_ed_btn tve_btn_icon">
        <div id="tve_valign_top" class="tve_icm tve-ic-uniE634 btn_alignment" title="Vertical align top"></div>
    </li>
    <li class="tve_ed_btn tve_btn_icon">
        <div id="tve_valign_middle" class="tve_icm tve-ic-uniE635 btn_alignment" title="Vertical align middle"></div>
    </li>
    <li class="tve_ed_btn tve_btn_icon">
        <div id="tve_valign_bottom" class="tve_icm tve-ic-uniE636 btn_alignment" title="Verical align bottom"></div>
    </li>
    <li class="tve_clear"></li>
    <li class="tve_firstOnRow">
        <div class="tve_ed_btn tve_btn_text tve_center tve_left tve_click"
             id="tve_table_reset_cell_border">Reset cell border
        </div>
        <div class="tve_ed_btn tve_btn_text tve_center tve_left tve_click" id="tve_table_manage_cells">
            Manage cells...
        </div>
    </li>
</ul>