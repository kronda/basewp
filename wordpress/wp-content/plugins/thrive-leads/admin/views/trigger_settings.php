<?php if (empty($variation) || empty($form_type)) : ?>
    <h2 class="error"><?php echo __('No form could be found. Please try again', 'thrive-leads') ?></h2>
    <?php exit() ?>
<?php endif ?>

<?php
$triggers = tve_leads_get_available_triggers($form_type);
$config = empty($variation['trigger_config']) ? array() : $variation['trigger_config'];
?>

<h2><?php echo $variation['post_title'] . ' ' . __('Trigger Settings', 'thrive-leads') ?></h2>

<div class="tve-form" id="trigger-settings-base">
    <label class="form-label tve-control-label"><?php echo __('Trigger', 'thrive-leads') ?></label>
    <div class="form-field">
        <select name="trigger" id="trigger-type">
            <?php foreach ($triggers as $key => $trigger) : /** @var TVE_Leads_Trigger_Abstract $trigger */ ?>
                <option value="<?php echo $key ?>"<?php echo $variation['trigger'] == $key ? ' selected="selected"' : '' ?>><?php echo $trigger->get_title() ?></option>
            <?php endforeach ?>
        </select>
    </div>
    <?php foreach ($triggers as $key => $trigger) : ?>
        <div id="trigger-settings-<?php echo $key ?>" class="trigger-settings"<?php echo $variation['trigger'] != $key ? ' style="display:none"' : '' ?>>
            <?php if ($key == $variation['trigger']) $trigger->set_config($config); $trigger->output_settings() ?>
        </div>
    <?php endforeach ?>
    <div class="clearfix"></div>
    <div class="form-buttons">
        <a href="javascript:void(0)" class="tve-btn tve-btn-green tve-btn-large tve-save-trigger"><?php echo __('Save', 'thrive-leads') ?></a>
    </div>
</div>

<script type="text/javascript">
    jQuery(function() {
        var triggerView = new ThriveLeads.views.TriggerSettings({
            el: jQuery('#trigger-settings-base'),
            model: new ThriveLeads.models.FormVariation(<?php echo json_encode($variation) ?>)
        });
        ThriveLeads.objects.triggerView = triggerView;
    });
</script>