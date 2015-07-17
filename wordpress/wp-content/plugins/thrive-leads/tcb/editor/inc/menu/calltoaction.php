<span class="tve_options_headline"><span class="tve_icm tve-ic-move"></span>Call to Action options</span>
<ul class="tve_menu">
    <?php $has_custom_colors = true; include dirname(__FILE__) . '/_custom_colors.php' ?>
    <li class="tve_btn_text clearfix">
        <label for="call_to_action_url" class="tve_left">URL:</label>
        <input type="text" id="call_to_action_url" class="tve_left tve_change" data-ctrl="controls.change.link_url"/>
    </li>
    <li class="tve_ed_btn tve_btn_text tve_center">
        <a href="#" class="cta_test_link" target="_blank">Test Link</a>
    </li>
    <li class="tve_text clearfix">
        <input type="checkbox" id="cta_link_new_window" class="tve_change" data-ctrl="controls.change.link_target">
        <label for="cta_link_new_window" class="tve_left">Open link in new window?</label>
    </li>
    <li class="tve_text clearfix">
        <input type="checkbox" id="cta_nofollow" class="tve_change" data-ctrl="controls.change.link_rel" data-value="nofollow">
        <label for="cta_nofollow" class="tve_left">Nofollow</label>
    </li>
    <li class="tve_clear"></li>
    <?php include dirname(__FILE__) . '/_margin.php' ?>
    <?php include dirname(__FILE__) . '/_event_manager.php' ?>
</ul>