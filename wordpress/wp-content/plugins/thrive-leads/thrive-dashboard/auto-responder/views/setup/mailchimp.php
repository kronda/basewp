<h2>MailChimp</h2>
<form action="<?php echo admin_url('admin.php?page=thrive_integrations_section&integration=thrive_auto_responder') ?>" method="post">
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row"><label><?php _e('API key:', 'thrive-visual-editor')?></label></th>
				<td>
					<input placeholder="API key" type="text" class="text" name="connection[key]" value="<?php echo $this->param('key', @$_POST['connection']['key']) ?>"/>
					<input type="hidden" name="api" value="mailchimp"/>
				</td>
			</tr>
			<tr>
				<th></th>
				<td><button type="submit" class="tve-button tve-button-green"><?php _e('Save Mailchimp API Key', 'thrive-visual-editor')?></button></td>
			</tr>
		</tbody>
	</table>
</form>