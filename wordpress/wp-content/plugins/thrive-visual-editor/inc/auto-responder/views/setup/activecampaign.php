<h2><?php echo $this->getTitle() ?></h2>
<form action="<?php echo admin_url('admin.php?page=tve_api_connect') ?>" method="post">
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row"><label><?php echo __("API URL", "thrive-cb") ?>:</label></th>
				<td>
					<input placeholder="<?php echo __("API URL", "thrive-cb") ?>" type="text" class="text" name="connection[api_url]" value="<?php echo $this->param('api_url') ?>"/>
					<input type="hidden" name="api" value="<?php echo $this->getKey() ?>"/>
				</td>
			</tr>
            <tr>
                <th scope="row"><label><?php echo __("API Key", "thrive-cb") ?>:</label></th>
                <td>
                    <input placeholder="<?php echo __("API Key", "thrive-cb") ?>" type="text" class="text" name="connection[api_key]" value="<?php echo $this->param('api_key') ?>"/>
                </td>
            </tr>
			<tr>
				<th></th>
				<td><button type="submit" class="tve-button tve-button-green"><?php echo __("Connect to ActiveCampaign", "thrive-cb") ?></button></td>
			</tr>
		</tbody>
	</table>
</form>
