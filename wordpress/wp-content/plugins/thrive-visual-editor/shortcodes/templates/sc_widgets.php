<?php

$attributes = array(
    'widget' => '',
    'options' => array()
);
?>

<div class="thrv_wrapper thrv_widget code_placeholder">
    <div class="tve_config" style="display: none"><?php echo '__CONFIG_widget__' . json_encode($attributes) . '__CONFIG_widget__' ?></div>
    <a class="tve_click tve_green_button clearfix" id="lb_widgets" data-ctrl="controls.lb_open"> <i class="tve_icm tve-ic-code"></i> <span>Insert Widget</span></a>
    <div class="tve_widget_container"></div>
</div>
