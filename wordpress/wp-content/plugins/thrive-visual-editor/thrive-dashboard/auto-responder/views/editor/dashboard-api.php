<h3>Connect with Service</h3>
<?php
$connection_config = $data['connection_config'];

/**
 * at this stage, we have a list of existing connections that are to be displayed in a list
 */

$available = Thrive_List_Manager::getAvailableAPIs(true);

?>
<div class="tve_clear" style="height:10px;"></div>
<p class="normal-margin">You sign up form is connected to service(s) using the following API connections:</p>
<table class="tcb-editor-table">
    <thead>
    <tr>
        <td colspan="2">
            Service Name
        </td>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($connection_config as $key => $list_id) : if (!isset($available[$key])) continue; ?>
        <tr class="tcb-row-hover">
            <td width="80%">
                <?php echo $available[$key]->getTitle() ?>
            </td>
            <td width="20%" class="tcb-text-right">
                <a href="javascript:void(0)" class="tve_click" data-ctrl="function:auto_responder.connection_form" data-connection-type="api" data-key="<?php echo $key ?>" title="Settings">
                    <span class="tve_icm tve-ic-cog tve_ic_small"></span>
                </a>
                &nbsp;&nbsp;&nbsp;
                <a href="javascript:void(0)" class="tve_click" data-ctrl="function:auto_responder.api.remove" data-key="<?php echo $key ?>" title="Remove">
                    <span class="tve_icm tve-ic-close tve_ic_small" style="color: #CE271B"></span>
                </a>
            </td>
        </tr>
    <?php endforeach ?>
    </tbody>
</table>
<div class="tve_clear" style="height:10px;"></div>
<?php if (count($available) != count($connection_config)) : ?>
    <div class="clearfix">
        <a href="javascript:void(0)" class="tve_click tve_right tve_editor_btn tve_btn_success" data-ctrl="function:auto_responder.connection_form" data-connection-type="api">
            <span>Add New Connection</span>
        </a>
    </div>
<?php endif ?>
<div class="tve_clear" style="height:30px;"></div>
<p class="normal-margin">Select which fields to display and their properties (you can reorder them by dragging the "move" icon from the left):</p>
<?php
$fields_table = isset($data['fields_table']) ? $data['fields_table'] : '';
$show_thank_you_url = true;
include dirname(__FILE__) . '/autoresponder-code-fields.php';
?>
<div class="tve_clear" style="height:20px;"></div>
<?php if (!empty($data['show_submit_options'])) : ?>
    
        <?php $submit = !empty($_POST['submit_option']) ? $_POST['submit_option'] : 'reload' ?>
        <p class="normal-margin">After the form is submitted:</p>
        <select class="tve_lg_validation_options tve_change" id="tve-api-submit-option" data-ctrl="function:auto_responder.api.submit_option_changed">
            <option value="reload"<?php echo $submit == 'reload' ? ' selected="selected"' : '' ?>>Reload current page</option>
            <option value="redirect"<?php echo $submit == 'redirect' ? ' selected="selected"' : '' ?>>Redirect to URL</option>
        </select>
        <input <?php echo $submit == 'reload' ? ' style="display: none"' : '' ?> size="70" class="tve_change tve_text thrive_txt_field_label" data-ctrl="function:auto_responder.api.thank_you_url" value="<?php echo !empty($_POST['thank_you_url']) ? $_POST['thank_you_url'] : '' ?>" placeholder="http://" />
    
    <div class="tve_clear" style="height:20px;"></div>
<?php endif ?>

<div class="tve_text_center">
    <a href="javascript:void(0)" class="tve_click tve_editor_btn tve_btn_success" data-ctrl="function:auto_responder.save_api_connection" data-edit-custom="1">
        <span>Save</span>
    </a>
    &nbsp;
    <a href="javascript:void(0)" class="tve_click tve_editor_btn tve_btn_default" data-ctrl="function:controls.lb_close">
        <span>Cancel</span>
    </a>
</div>