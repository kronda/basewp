<span class="tve_options_headline"><span class="tve_icm tve-ic-move"></span>Content Container options</span>
<ul class="tve_menu">
    <li class="tve_text tve_firstOnRow">Align:</li>
    <li id="tve_leftCC" class="btn_alignment tve_alignment_left">Left</li>
    <li id="tve_centerCC" class="btn_alignment tve_alignment_center">Center</li>
    <li id="tve_rightCC" class="btn_alignment tve_alignment_right">Right</li>
    <li class="tve_text tve_slider_config" data-value="300" data-min-value="50"
        data-max-value="available"
        data-input-selector="#content_container_width_input"
        data-property="width"
        data-selector="> .tve_content_inner">
        <label for="content_container_width_input" class="tve_left">&nbsp;Max-width</label>

        <div class="tve_slider tve_left">
            <div class="tve_slider_element"></div>
        </div>
        <input class="tve_left width50" type="text" id="content_container_width_input" value="300px">

        <div class="clear"></div>
    </li>
    <li class="tve_clear"></li>
    <?php include dirname(__FILE__) . '/_margin.php' ?>
</ul>