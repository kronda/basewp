<?php if (empty($_POST['edit_custom_html'])) : ?>
    <h3>Connect with Service</h3>
    <div class="tve_clear" style="height:20px;"></div>
    <p>Your sign up form is connected to a service using custom HTML form code.</p>
    <div class="tve_clear" style="height:20px;"></div>
    <div class="tve_text_center">
        <a href="javascript:void(0)" class="tve_click tve_editor_btn tve_btn_success" data-ctrl="function:auto_responder.dashboard" data-edit-custom="1">
            <span>Edit HTML form code</span>
        </a>
        &nbsp;
        <a href="javascript:void(0)" class="tve_click tve_editor_btn tve_btn_critical" data-ctrl="function:auto_responder.remove_custom_html">
            <span>Delete Connection</span>
        </a>
    </div>
<?php else : ?>
    <?php $show_textarea = true; include dirname(__FILE__) . '/autoresponder-code-fields.php' ?>
<?php endif ?>