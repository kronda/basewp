<?php if (isset($show_textarea)) : ?>
    <textarea name="tve_lead_generation_code" style="width: 100%;" placeholder="Insert your code here"></textarea>
    <div class="tve_clearfix">
        <a href="javascript:void(0)" class="tve_click tve_editor_btn tve_btn_default tve_right" data-ctrl="function:auto_responder.dashboard"><span>Cancel</span></a>
        <a href="javascript:void(0)" class="tve_editor_btn tve_btn_success tve_right tve_r tve_lead_generate_fields tve_click" data-ctrl="function:auto_responder.generate_fields"><span>Generate Fields</span></a>
    </div>
<?php endif ?>
<?php unset($show_textarea) ?>
<div id="generated_inputs_container" class="tve_clearfix"><?php echo isset($fields_table) ? $fields_table : '' ?></div>
<div id="tve_lg_icon_list" style="display: none">
    <?php $icon_click = 'function:auto_responder.choose_icon';
    include_once plugin_dir_path(dirname(dirname(dirname(dirname(__FILE__))))) . 'editor/lb_icon.php' ?>
</div>
