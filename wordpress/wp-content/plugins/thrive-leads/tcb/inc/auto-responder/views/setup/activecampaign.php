<h2><?php echo $this->getTitle() ?></h2>
<form action="<?php echo admin_url('admin.php?page=tve_api_connect') ?>" method="post">
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row"><label>API URL:</label></th>
				<td>
					<input placeholder="API URL" type="text" class="text" name="connection[api_url]" value="<?php echo $this->param('api_url') ?>"/>
					<input type="hidden" name="api" value="<?php echo $this->getKey() ?>"/>
				</td>
			</tr>
            <tr>
                <th scope="row"><label>API Key:</label></th>
                <td>
                    <input placeholder="API Key" type="text" class="text" name="connection[api_key]" value="<?php echo $this->param('api_key') ?>"/>
                </td>
            </tr>
			<tr>
				<th></th>
				<td><button type="submit" class="tve-button tve-button-green">Connect to ActiveCampaign</button></td>
			</tr>
		</tbody>
	</table>
</form>