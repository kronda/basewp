<h4><?php echo __("Connect with Service", "thrive-cb") ?></h4>
<hr class="tve_lightbox_line"/>
<?php
$connection_config = $data['connection_config'];
/**
 * at this stage, we have a list of existing connections that are to be displayed in a list
 */

$available = Thrive_List_Manager::getAvailableAPIs(true);

?>
<div class="tve_large_lightbox tve_lead_gen_lightbox_small">
    <p><?php echo __("Your sign up form is connected to service(s) using the following API connections:", "thrive-cb") ?></p>
    <table>
        <thead>
        <tr>
            <th colspan="2">
                <?php echo __("Service Name", "thrive-cb") ?>
            </th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($connection_config as $key => $list_id) : if (!isset($available[$key])) continue; ?>
            <tr>
                <td width="90%">
                    <?php echo $available[$key]->getTitle() ?>
                </td>
                <td width="10%">
                    <a href="javascript:void(0)" class="tve_click" data-ctrl="function:auto_responder.connection_form"
                       data-connection-type="api" data-key="<?php echo $key ?>" title="<?php echo __("Settings", "thrive-cb") ?>">
                        <span class="tve_icm tve-ic-cog tve_ic_small tve_lightbox_icon_small"></span>
                    </a>
                    &nbsp;&nbsp;&nbsp;
                    <a href="javascript:void(0)" class="tve_click" data-ctrl="function:auto_responder.api.remove"
                       data-key="<?php echo $key ?>" title="<?php echo __("Remove", "thrive-cb") ?>">
                        <span class="tve_icm tve-ic-close tve_ic_small tve_lightbox_icon_small"></span>
                    </a>
                </td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
    <div class="tve-sp"></div>
    <?php if (count($available) != count($connection_config)) : ?>
        <div class="clearfix">
            <a href="javascript:void(0)" class="tve_click tve_right tve_editor_button tve_editor_button_success"
               data-ctrl="function:auto_responder.connection_form" data-connection-type="api">
                <?php echo __("Add New Connection", "thrive-cb") ?>
            </a>
        </div>
    <?php endif ?>
    <div class="tve_clear" style="height:30px;"></div>
    <p><?php echo __('Select which fields to display and their properties (you can reorder them by dragging the "move" icon from the left):', 'thrive-cb') ?></p>
    <?php
    $fields_table = isset($data['fields_table']) ? $data['fields_table'] : '';
    $show_thank_you_url = true;
    $show_reCaptcha = true;
    include dirname(__FILE__) . '/autoresponder-code-fields.php';

    ?>
    <div class="tve-sp"></div>
    <?php if (!empty($data['show_submit_options'])) : ?>
        <div class="tve_gray_box">
            <h4><?php echo __("Thank You Page URL", "thrive-cb") ?></h4>
            <?php $submit = !empty($_POST['submit_option']) ? $_POST['submit_option'] : 'reload' ?>
            <label><?php echo __("After the form is submitted:", "thrive-cb") ?>&nbsp;</label>

            <div class="tve_lightbox_select_holder tve_lightbox_input_inline tve_lightbox_select_inline">
                <select class="tve_lg_validation_options tve_change" id="tve-api-submit-option"
                        data-ctrl="function:auto_responder.api.submit_option_changed">
                    <option value="reload"<?php echo $submit == 'reload' ? ' selected="selected"' : '' ?>><?php echo __("Reload current page", "thrive-cb") ?>
                    </option>
                    <option value="redirect"<?php echo $submit == 'redirect' ? ' selected="selected"' : '' ?>><?php echo __("Redirect to URL", "thrive-cb") ?>
                    </option>
                </select>
            </div>
            <input <?php echo $submit == 'reload' ? ' style="display: none"' : '' ?> size="70"
                                                                                     class="tve_change tve_text tve_lightbox_input tve_lightbox_input_inline"
                                                                                     data-ctrl="function:auto_responder.api.thank_you_url"
                                                                                     value="<?php echo !empty($_POST['thank_you_url']) ? $_POST['thank_you_url'] : '' ?>"
                                                                                     placeholder="http://"/>
        </div>
    <?php endif ?>
    <div class="tve-sp"></div>
    <div class="tve_clearfix">
        <a href="javascript:void(0)" class="tve_click tve_editor_button tve_editor_button_default tve_right tve_button_margin"
           data-ctrl="function:controls.lb_close">
            <?php echo __("Cancel", "thrive-cb") ?>
        </a>
        &nbsp;
        <a href="javascript:void(0)" class="tve_click tve_editor_button tve_editor_button_success tve_right"
           data-ctrl="function:auto_responder.save_api_connection" data-edit-custom="1">
            <?php echo __("Save", "thrive-cb") ?>
        </a>
    </div>
</div>
