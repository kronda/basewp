<?php /* displays the controls required for adding / updating an event*/ ?>

<h5><?php echo empty($is_edit) ? 'Add New Event' : 'Edit Event' ?></h5>

<div class="tve_event_manager" id="tve_current_event_settings">
    <table class="tve_event_manager_list">
        <tr class="tve_head">
            <th width="30%">Trigger</th>
            <th width="70%">Action</th>
        </tr>
        <tr>
            <td>
                <select name="t" id="tve_event_trigger" class="tve_ctrl_validate tve_event_onchange" data-validators="required" data-action="action_settings">
                    <option value=""<?php echo empty($selected_trigger_code) ? ' selected="selected"' : '' ?>>Select Trigger</option>
                    <?php foreach ($triggers as $code => $trigger) : ?>
                        <option value="<?php echo $code ?>"<?php echo $selected_trigger_code == $code ? ' selected="selected"' : '' ?>><?php echo $trigger->getName() ?></option>
                    <?php endforeach ?>
                </select>
            </td>
            <td>
                <select class="tve_event_onchange tve_ctrl_validate" name="a" id="tve_event_action" data-action="action_settings" data-validators="required">
                    <option value=""<?php echo empty($selected_action_code) ? ' selected="selected"' : '' ?>>Select Action</option>
                    <?php foreach ($actions as $code => $action) : ?>
                        <option value="<?php echo $code ?>"<?php echo $selected_action_code == $code ? ' selected="selected"' : '' ?>><?php echo $action->getName() ?></option>
                    <?php endforeach ?>
                </select>
            </td>
        </tr>
    </table>

    <div id="tve_event_settings">
        <?php echo $item_settings; ?>
    </div>

</div>
<div class="tve_clear" style="height: 20px;"></div>
<div class="tve_landing_pages_actions">
    <div class="tve_btn_default tve_right tve_event_onclick" data-action="list">
        <div class="tve_preview">Cancel</div>
    </div>
    <div class="tve_event_onclick tve_btn_success tve_right" style="margin-right: 5px;" data-action="save" id="tve_event_save">
        <div class="tve_update">Save Event</div>
    </div>
</div>
<div class="tve_clear"></div>