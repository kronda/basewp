<h2><?php echo $variation['post_title'] . ' ' . __('Position Settings', 'thrive-leads') ?></h2>

<p class="tve-form-description">
    <?php echo __('This setting determines the position of the "' . $variation['tve_form_type_name'] . '" form".', 'thrive-leads') ?>

    <?php if ($variation['tve_form_type'] == 'in_content'): ?>
        <?php echo __(' This type of form will be displayed only on singular pages!', 'thrive-leads') ?>
    <?php endif; ?>
</p>

<div id="position-settings-base" class="tve-form">
    <label class="form-label tve-control-label"><?php echo $form_type_position['label'] ?></label>

    <div class="form-field">
        <select class="form-position" name="position" tabindex="1">
            <?php foreach ($form_type_position['position'] as $key => $position): ?>
                <option value="<?php echo $key; ?>" <?php echo $variation['position'] == $key ? ' selected="selected"' : '' ?>><?php echo $position; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="clear" style="height: 10px;"></div>
    <div class="form-buttons">
        <a href="javascript:void(0)" class="tve-btn tve-btn-gray tve-btn-large tve-close-thickbox" tabindex="3"><?php echo __('Cancel', 'thrive-leads') ?></a>
        &nbsp;
        <a href="javascript:void(0)" class="tve-btn tve-btn-green tve-btn-large tve-save-position tl-enter-action" tabindex="2"><?php echo __('Save', 'thrive-leads') ?></a>
    </div>
</div>

<script type="text/javascript">
    jQuery(function () {
        var positionView = new ThriveLeads.views.PositionSettings({
            el: jQuery('#position-settings-base'),
            model: new ThriveLeads.models.FormVariation(<?php echo json_encode($variation) ?>)
        });
        ThriveLeads.objects.positionView = positionView;
    });
</script>