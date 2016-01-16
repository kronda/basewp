<?php

$per_page_options = array(20, 50, 100);

$contacts_data = array(
    'lead_groups' => tve_leads_get_groups(
        array(
            'full_data' => false,
            'tracking_data' => false,
            'completed_tests' => false,
            'active_tests' => false,
        )
    ),
    'shortcodes' => tve_leads_get_shortcodes(
        array('active_test' => false)
    ),
    'two_step_lightbox' => tve_leads_get_two_step_lightboxes(
        array('active_test' => false)
    )
);

?>

<?php if ($which == "top"): ?>
    <input type="hidden" name="tve_template_redirect_contacts" value="true"/>
<?php endif; ?>

<span style="position: relative;top: 3px;"><?php echo __('From', "thrive-leads"); ?>:</span>
<input type="text" <?php if ($which == "top"): ?>name="tve-start-date"<?php endif; ?> class="tve-contacts-start-date" value="<?php echo $start_date; ?>"/>
<span style="position: relative;top: 3px;"><?php echo __('To', "thrive-leads"); ?>:</span>
<input type="text" <?php if ($which == "top"): ?>name="tve-end-date"<?php endif; ?> class="tve-contacts-end-date" value="<?php echo $end_date; ?>" style="position: relative;top: 3px;"/>

<label class="tve-custom-select">
    <span style="position: relative;top: 3px;"><?php echo __('Source', "thrive-leads"); ?>:</span>
    <select class="tve-contacts-source" <?php if ($which == "top"): ?>name="tve-source"<?php endif; ?> autocomplete="off">
        <option value="-1"><?php echo __('All', 'thrive-leads') ?></option>
        <optgroup label="<?php echo __('Lead Groups', 'thrive-leads'); ?>">
            <?php if (!empty($contacts_data['lead_groups'])): ?>
                <?php foreach ($contacts_data['lead_groups'] as $group) : ?>
                    <option value="<?php echo $group->ID ?>" <?php echo $source == $group->ID ? 'selected' : ''; ?>><?php echo $group->post_title ?></option>
                <?php endforeach ?>
            <?php else: ?>
                <option value="-1" disabled>(<?php echo __('empty', 'thrive-leads') ?>)</option>
            <?php endif; ?>
        </optgroup>
        <optgroup label="<?php echo __('Shortcodes', 'thrive-leads'); ?>">
            <?php if (!empty($contacts_data['shortcodes'])): ?>
                <?php foreach ($contacts_data['shortcodes'] as $shortcode) : ?>
                    <option value="<?php echo $shortcode->ID ?>" <?php echo $source == $shortcode->ID ? 'selected' : ''; ?>><?php echo $shortcode->post_title ?></option>
                <?php endforeach ?>
            <?php else: ?>
                <option value="-1" disabled>(<?php echo __('empty', 'thrive-leads') ?>)</option>
            <?php endif; ?>
        </optgroup>
        <optgroup label="<?php echo __('ThriveBoxes', 'thrive-leads'); ?>">
            <?php if (!empty($contacts_data['two_step_lightbox'])): ?>
                <?php foreach ($contacts_data['two_step_lightbox'] as $tsl) : ?>
                    <option value="<?php echo $tsl->ID ?>" <?php echo $source == $tsl->ID ? 'selected' : ''; ?>><?php echo $tsl->post_title ?></option>
                <?php endforeach ?>
            <?php else: ?>
                <option value="-1" disabled>(<?php echo __('empty', 'thrive-leads') ?>)</option>
            <?php endif; ?>
        </optgroup>
    </select>
</label>

<label class="tve-custom-select">
    <span style="position: relative;top: 3px;"><?php echo __('Show', "thrive-leads"); ?>:</span>
    <select class="tve-contacts-per-page" <?php if ($which == "top"): ?>name="tve-per-page"<?php endif; ?> autocomplete="off">
        <?php foreach ($per_page_options as $value): ?>
            <option <?php echo ($value == $per_page) ? "selected" : ""; ?> value="<?php echo $value; ?>"><?php echo $value. ' '; ?><?php echo __('per page', "thrive-leads"); ?></option>
        <?php endforeach; ?>
    </select>
</label>

<input type="submit" class="button action" value="<?php echo __('Filter', "thrive-leads"); ?>" style="position: relative;top: 3px;">