<?php if (!empty($data['forms'])): ?>
    <div class="tve-sp"></div>
    <h6><?php echo __('Choose the form you want to use:', 'thrive-cb') ?></h6>
    <div class="tve_lightbox_select_holder tve_lightbox_input_inline tve_lightbox_select_inline tve_activecampaign_select">
        <?php foreach ($data['forms'] as $list_id => $forms): ?>
            <select data-list-id="<?php echo $list_id; ?>" style="display: none;" class="tve-api-extra tve_disabled" name="activecampaign_form">
                <?php foreach ($forms as $id => $form): ?>
                    <option value="<?php echo $form['id']; ?>" <?php echo !empty($data['form']) && $data['form'] == $form['id'] ? 'selected' : ''; ?>><?php echo $form['name']; ?></option>
                <?php endforeach; ?>
            </select>
        <?php endforeach; ?>
    </div>
    <div class="tve_activecampaign_no_forms">
        <?php echo __('No forms available for this list!', 'thrive-cb'); ?>
    </div>
<?php elseif (!empty($this->_error)): ?>
    <?php echo $this->_error ?>
<?php endif; ?>
<div class="tve-sp"></div>
<h6><?php echo __('Tags', 'thrive-cb') ?></h6>
<input type="text" class="tve-api-extra tve_lightbox_input tve_lightbox_input_inline" name="activecampaign_tags" value="<?php echo !empty($data['tags']) ? $data['tags'] : '' ?>" size="40" />
<p><?php echo __("Comma-separated lists of tags to assign to a new contact in ActiveCampaign", "thrive-tb") ?></p>
<script type="text/javascript">
    (function ($) {
        $(document).on('change', '#thrive-api-list-select', function () {
            var list_id = $('#thrive-api-list-select').find(':selected').val(),
                select = $('.tve_activecampaign_select'),
                no_forms = $('.tve_activecampaign_no_forms'),
                $forms = $('select.tve-api-extra[data-list-id="' + list_id + '"]');
            select.show();
            no_forms.hide();
            $('select.tve-api-extra[name="activecampaign_form"]').addClass('tve_disabled').hide();
            if ($forms.length > 0) {
                $forms.removeClass('tve_disabled').show();
            } else {
                select.hide();
                no_forms.show();
            }
        });

        $('#thrive-api-list-select').trigger('change');
    })(jQuery);
</script>