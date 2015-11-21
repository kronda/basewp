<?php if (empty($_POST['edit_custom_html'])) : ?>
    <h4><?php echo __("Connect with Service", "thrive-cb") ?></h4>
    <hr class="tve_lightbox_line"/>
    <p><?php echo __("Your sign up form is connected to a service using custom HTML form code.", "thrive-cb") ?></p>
    <div class="tve-sp"></div>
    <div class="tve_clearfix">
        <a href="javascript:void(0)" class="tve_click tve_editor_button tve_editor_button_cancel tve_right tve_button_margin"
           data-ctrl="function:auto_responder.remove_custom_html">
            <span><?php echo __("Delete Connection", "thrive-cb") ?></span>
        </a>
        &nbsp;
        <a href="javascript:void(0)" class="tve_click tve_editor_button tve_editor_button_success tve_right"
           data-ctrl="function:auto_responder.dashboard" data-edit-custom="1">
            <span><?php echo __("Edit HTML form code", "thrive-cb") ?></span>
        </a>
    </div>
<?php else : ?>
    <?php
    $show_textarea = true;
    $show_reCaptcha = false;
    include dirname(__FILE__) . '/autoresponder-code-fields.php'; ?>
<?php endif; ?>