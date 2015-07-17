<p class="normal-margin"><?php echo __('Choose the type of optin you would like for the Mailchimp integration', 'thrive-visual-editor') ?></p>
<select class="tve-api-extra" name="mailchimp_optin" style="width: 250px">
    <option value="s"<?php echo $data['optin'] === 's' ? ' selected="selected"' : '' ?>><?php echo __('Single optin', 'thrive-visual-editor') ?></option>
    <option value="d"<?php echo $data['optin'] === 'd' ? ' selected="selected"' : '' ?>><?php echo __('Double optin', 'thrive-visual-editor') ?></option>
</select>
<span class="tve-smaller" style="font-size: 80%">&nbsp; &nbsp; <?php echo __('(Double optin means your subscribers will need to confirm their email address before being added to your list)', 'thrive-visual-editor') ?></span>