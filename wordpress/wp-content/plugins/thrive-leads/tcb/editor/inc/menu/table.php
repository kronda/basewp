<span class="tve_options_headline"><span class="tve_icm tve-ic-move"></span>Table options</span>
<ul class="tve_menu">
    <?php $has_custom_colors = true; include dirname(__FILE__) . '/_custom_colors.php' ?>
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
    <li class="tve_ed_btn_text clearfix">
        <label class="tve_left" style="color: #878787">
            <input id="table_border_width" class="tve_change" value="0" type="text" size="3"
                   data-css-property="border-width" data-suffix="px"
                   data-size="1"> px
        </label>
    </li>
    <li class="tve_text clearfix">
        <input class="tve_change tve_left tve_checkbox_bottom" type="checkbox" id="table_outer_border"
               value="1"><label for="table_outer_border"
                                class="tve_left"> Outer
            Border</label>
    </li>
    <li class="tve_text clearfix">
        <input class="tve_change tve_left tve_checkbox_bottom" type="checkbox" id="table_inner_border"
               value="1"><label for="table_inner_border"
                                class="tve_left"> Inner
            Border</label>
    </li>
    <li class="tve_clear"></li>
    <li class="tve_text tve_firstOnRow">Alignment</li>
    <li class="tve_ed_btn tve_btn_icon">
        <div id="tve_table_align_left" class="tve_icm tve-ic-paragraph-left btn_alignment" title="Text align left"></div>
    </li>
    <li class="tve_ed_btn tve_btn_icon">
        <div id="tve_table_align_center" class="tve_icm tve-ic-paragraph-center btn_alignment" title="Text align center"></div>
    </li>
    <li class="tve_ed_btn tve_btn_icon">
        <div id="tve_table_align_right" class="tve_icm tve-ic-paragraph-right btn_alignment" title="Text align right"></div>
    </li>
    <li class="tve_ed_btn tve_btn_icon">
        <div id="tve_table_align_justify" class="tve_icm tve-ic-paragraph-justify btn_alignment"
             title="Text align justify"></div>
    </li>
    <li class="tve_ed_btn tve_btn_icon">
        <div id="tve_table_valign_top" class="tve_icm tve-ic-uniE634 btn_alignment" title="Vertical align top"></div>
    </li>
    <li class="tve_ed_btn tve_btn_icon">
        <div id="tve_table_valign_middle" class="tve_icm tve-ic-uniE635 btn_alignment"
             title="Vertical align middle"></div>
    </li>
    <li class="tve_ed_btn tve_btn_icon">
        <div id="tve_table_valign_bottom" class="tve_icm tve-ic-uniE636 btn_alignment"
             title="Verical align bottom"></div>
    </li>
    <li class="tve_text">Cell Padding&nbsp;<input id="table_cell_padding" class="tve_change" type="text"
                                                  size="3" value="5" data-size="1"
                                                  data-suffix="px"> px
    </li>
    <li class="tve_clear"></li>
    <li class="tve_firstOnRow">
        <div class="tve_ed_btn tve_btn_text tve_left tve_click" data-ctrl="controls.click.css" data-elem="> tbody > tr > td,> thead > tr > th"
             data-prop="width" data-val=""
             title="Reset all column widths to their initial values">Reset widths
        </div>
        <div class="tve_ed_btn tve_btn_text tve_left tve_click" data-ctrl="controls.click.css" data-elem="> tbody > tr > td,> thead > tr > th"
             data-prop="height" data-val=""
             title="Reset all row heights to their initial values">Reset heights
        </div>
        <div class="tve_ed_btn tve_btn_text tve_center tve_left tve_click" id="tve_table_manage_cells">
            Manage cells...
        </div>
    </li>
    <?php include dirname(__FILE__) . '/_margin.php' ?>
</ul>