<span class="tve_options_headline"><span class="tve_icm tve-ic-move"></span>Star Rating options</span>
<ul class="tve_menu">
    <?php include dirname(__FILE__) . '/_custom_colors.php' ?>
    <?php include dirname(__FILE__) . '/_margin.php' ?>
    <li class="tve_btn_text">
        <label class="tve_text">Rating:&nbsp; <input type="text" class="tve_text tve_change" size="3" data-size="1" id="rating_value" value="3" /></label>
        &nbsp;
        <label>Max:&nbsp; <input type="text" class="tve_text tve_change" size="3" data-size="1" id="rating_max" value="5" /></label>
    </li>
    <li class="tve_btn_text">
        <label class="tve_text">&nbsp;Align:&nbsp; </label>
    </li>
    <li id="tve_leftBtn" class="btn_alignment tve_alignment_left">
        Left
    </li>
    <li id="tve_centerBtn" class="btn_alignment tve_alignment_center">
        Center
    </li>
    <li id="tve_rightBtn" class="btn_alignment tve_alignment_right">
        Right
    </li>
</ul>