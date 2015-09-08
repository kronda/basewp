<?php /* displays the controls required for adding / updating an event*/ ?>

<h4><?php echo empty($is_edit) ? 'Add New Event' : 'Edit Event' ?></h4>
<hr class="tve_lightbox_line"/>
<div class="tve_event_manager" id="tve_current_event_settings">
    <table class="tve_event_manager_list">
        <thead>
        <tr>
            <th width="35%"><?php echo __("Trigger", "thrive-cb") ?></th>
            <th width="65%"><?php echo __("Action", "thrive-cb") ?></th>
        </tr>
        </thead>
        <tfoot>
        <tr>
            <td>
                <div class="tve_lightbox_select_holder">
                    <select name="t" id="tve_event_trigger" class="tve_ctrl_validate tve_event_onchange"
                            data-validators="required" data-action="action_settings">
                        <option value=""<?php echo empty($selected_trigger_code) ? ' selected="selected"' : '' ?>><?php echo __("Select Trigger", "thrive-cb") ?>
                        </option>
                        <?php foreach ($triggers as $code => $trigger) : ?>
                            <option
                                value="<?php echo $code ?>"<?php echo $selected_trigger_code == $code ? ' selected="selected"' : '' ?>><?php echo $trigger->getName() ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            </td>
            <td>
                <div class="tve_lightbox_select_holder">
                    <select class="tve_event_onchange tve_ctrl_validate" name="a" id="tve_event_action"
                            data-action="action_settings" data-validators="required">
                        <option value=""<?php echo empty($selected_action_code) ? ' selected="selected"' : '' ?>><?php echo __("Select Action", "thrive-cb") ?>
                        </option>
                        <?php foreach ($actions as $code => $action) : ?>
                            <option
                                value="<?php echo $code ?>"<?php echo $selected_action_code == $code ? ' selected="selected"' : '' ?>><?php echo $action->getName() ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
            </td>
        </tr>
        </tfoot>
    </table>
    <div id="tve_event_settings">
        <?php echo $item_settings; ?>
    </div>

</div>
<div class="tve_clear" style="height: 20px;"></div>
<div class="tve_landing_pages_actions">
    <div class="tve_editor_button tve_editor_button_cancel tve_right tve_event_onclick tve_button_margin"
         data-action="list">
        <?php echo __("Cancel", "thrive-cb") ?>
    </div>
    <div class="tve_editor_button tve_editor_button_success tve_right tve_event_onclick" data-action="save"
         id="tve_event_save">
        <?php echo __("Save Event", "thrive-cb") ?>
    </div>
</div>
<div class="tve_clear"></div>