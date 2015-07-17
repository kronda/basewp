<h2>Ontraport</h2>
<form action="<?php echo admin_url('admin.php?page=tve_api_connect') ?>" method="post">
	<table class="form-table">
		<tbody>
			<tr>
				<th scope="row"><label>Application ID:</label></th>
				<td>
					<input placeholder="Application ID" type="text" class="text" name="connection[app_id]" value="<?php echo $this->param('app_id', @$_POST['connection']['app_id']) ?>"/>
				</td>
			</tr>
			<tr>
				<th scope="row"><label>API key:</label></th>
			    <td>
			    	<input placeholder="API key" type="text" class="text" name="connection[key]" value="<?php echo $this->param('key', @$_POST['connection']['key']) ?>"/>
			    	<input type="hidden" name="api" value="<?php echo $this->getKey()?>"/>
			    </td>
			</tr>
			<tr>
				<th></th>
				<td><button type="submit" class="tve-button tve-button-green">Connect to Ontraport</button></td>
			</tr>
		</tbody>
	</table>
</form>