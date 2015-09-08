<h2>Ontraport</h2>
<form action="<?php echo admin_url('admin.php?page=tve_api_connect') ?>" method="post">
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row"><label><?php echo __("Application ID", "thrive-cb") ?>:</label></th>
				<td>
					<input placeholder="<?php echo __("Application ID", "thrive-cb") ?>" type="text" class="text" name="connection[app_id]" value="<?php echo $this->param('app_id', @$_POST['connection']['app_id']) ?>"/>
				</td>
			</tr>
			<tr>
				<th scope="row"><label><?php echo __("API key", 'thrive-cb') ?>:</label></th>
			    <td>
			    	<input placeholder="<?php echo __("API key", 'thrive-cb') ?>" type="text" class="text" name="connection[key]" value="<?php echo $this->param('key', @$_POST['connection']['key']) ?>"/>
			    	<input type="hidden" name="api" value="<?php echo $this->getKey()?>"/>
			    </td>
			</tr>
			<tr>
				<th></th>
				<td><button type="submit" class="tve-button tve-button-green"><?php echo __("Connect to Ontraport", "thrive-cb") ?></button></td>
			</tr>
		</tbody>
	</table>
</form>
