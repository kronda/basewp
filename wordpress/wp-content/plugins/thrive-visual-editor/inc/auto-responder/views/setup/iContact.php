<h2>iContact</h2>
<form action="<?php echo admin_url('admin.php?page=tve_api_connect') ?>" method="post">
	<table class="form-table">
		<tbody>
			<tr>
			<th scope="row"><label><?php echo __("Application ID", "thrive-cb") ?>:</label></th>
				<td>
					<input placeholder="<?php echo __("Application ID", "thrive-cb") ?>" type="text" class="text" name="connection[appId]" value="<?php echo $this->param('appId', @$_POST['connection']['appId']) ?>"/>
				</td>
			</tr>
			<th scope="row"><label><?php echo __("iContact Username", "thrive-cb") ?>:</label></th>
				<td>
					 <input placeholder="<?php echo __("iContact Username", "thrive-cb") ?>" type="text" class="text" name="connection[apiUsername]" value="<?php echo $this->param('apiUsername', @$_POST['connection']['apiUsername']) ?>"/>
				</td>
			</tr>
			<th scope="row"><label><?php echo __("Application Password", "thrive-cb") ?>:</label></th>
				<td>
					<input placeholder="<?php echo __("Application Password", "thrive-cb") ?>" type="text" class="text" name="connection[apiPassword]" value="<?php echo $this->param('apiPassword', @$_POST['connection']['apiPassword']) ?>"/>
    				<input type="hidden" name="api" value="<?php echo $this->getKey()?>"/>
				</td>
			</tr>
			<tr>
				<th></th>
				<td><button type="submit" class="tve-button tve-button-green"><?php echo __("Connect to iContact", "thrive-cb") ?></button></td>
			</tr>
		</tbody>
	</table>
</form>
