<h3>Step 2: Choose your API Connection</h3>

<?php
include dirname(__FILE__) . '/partials/api-select.php';

include dirname(__FILE__) . '/partials/api-lists.php'; ?>

<div class="tve_clear" style="height:20px;"></div>

<?php if (!empty($connected_apis)) : ?>
    <div class="tve_text_center" id="tve-save-api">
        <a href="javascript:void(0)" class="tve_click tve_editor_btn tve_btn_success" data-ctrl="function:auto_responder.api.save"
           data-edit="<?php echo $edit_api_key ?>">
            <span>Save</span>
        </a>
        <a href="javascript:void(0)" class="tve_click tve_editor_btn tve_btn_default" data-ctrl="function:auto_responder.dashboard"
           data-edit="<?php echo $edit_api_key ?>">
            <span>Cancel</span>
        </a>
    </div>
<?php endif ?>