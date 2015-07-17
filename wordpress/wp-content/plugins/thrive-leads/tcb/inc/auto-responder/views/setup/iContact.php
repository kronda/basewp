<h2>iContact</h2>
<form action="<?php echo admin_url('admin.php?page=tve_api_connect') ?>" method="post">
	<table class="form-table">
		<tbody>
			<tr>
			<th scope="row"><label>Application ID:</label></th>
				<td>
					<input placeholder="Application ID" type="text" class="text" name="connection[appId]" value="<?php echo $this->param('appId', @$_POST['connection']['appId']) ?>"/>
				</td>
			</tr>
			<th scope="row"><label>iContact Username:</label></th>
				<td>
					 <input placeholder="Username" type="text" class="text" name="connection[apiUsername]" value="<?php echo $this->param('apiUsername', @$_POST['connection']['apiUsername']) ?>"/>
				</td>
			</tr>
			<th scope="row"><label>Application Password:</label></th>
				<td>
					<input placeholder="Password" type="text" class="text" name="connection[apiPassword]" value="<?php echo $this->param('apiPassword', @$_POST['connection']['apiPassword']) ?>"/>
    				<input type="hidden" name="api" value="<?php echo $this->getKey()?>"/>
				</td>
			</tr>
			<tr>
				<th></th>
				<td><button type="submit" class="tve-button tve-button-green">Connect to iContact</button></td>
			</tr>
		</tbody>
	</table>
</form>