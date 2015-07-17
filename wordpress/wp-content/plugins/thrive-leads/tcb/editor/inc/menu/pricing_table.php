<span class="tve_options_headline"><span class="tve_icm tve-ic-move"></span>Pricing Table options</span>
<ul class="tve_menu">
    <?php $has_custom_colors = true; include dirname(__FILE__) . '/_custom_colors.php' ?>
    <li class="tve_text">Move highlighted column:</li>
    <li class="btn_alignment tve_alignment_left tve_click" data-ctrl="controls.pricing_table.move" data-dir="prev">Left</li>
    <li class="btn_alignment tve_alignment_right tve_click" data-ctrl="controls.pricing_table.move" data-dir="next">Right</li>
    <li class="tve_add_highlight">
        <div class="tve_btn_highlight tve_ed_btn tve_btn_text tve_left tve_click" data-ctrl="controls.pricing_table.highlight_toggle">Add highlighted column</div>
    </li>
    <li><input type="text" class="element_class tve_text tve_change" data-ctrl="controls.change.cls" placeholder="Custom class"></li>
    <li class="tve_clear"></li>

    <?php include dirname(__FILE__) . '/_margin.php' ?>
</ul>