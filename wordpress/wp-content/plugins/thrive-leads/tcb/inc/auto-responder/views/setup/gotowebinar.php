<h2>GoToWebinar</h2>
<?php if ($this->isConnected() && $this->expiresIn() > 30) : ?>
    <p>GoToWebinar is connected. The access token expires on <strong><?php echo $this->getExpiryDate() ?></strong></p>
<?php else : ?>
    <form action="<?php echo admin_url('admin.php?page=tve_api_connect') ?>" method="post">
	<table class="form-table">
        <?php if ($this->isExpired()) : ?>
            <p>The GoToWebinar access token has expired on <strong><?php echo $this->getExpiryDate() ?></strong>. You need to renew the token by providing your GoToWebinar credentials below</p>
        <?php elseif ($this->expiresIn() <= 30) : ?>
            <p>The GoToWebinar access token will expire in <strong><?php echo $this->expiresIn() ?> days</strong>. Renew the token by providing your GoToWebinar credentials below</p>
        <?php else : ?>
            <p>Fill in your GoToWebinar username (email) and password below to connect</p>
        <?php endif ?>
        <tr>
        	<th><label>Email:</label></th>
        	<td><input type="text" class="text" autocomplete="off" name="gtw_email" value="<?php echo $this->param('gtw_email', @$_POST['gtw_email']) ?>"/></td>
        </tr>
        <tr>
	        <th><label>Password:</label></th>
	        <td>
				<input type="password" autocomplete="off" class="text" name="gtw_password" value=""/>
		        <input type="hidden" name="api" value="<?php echo $this->getKey()?>"/>
	        </td>
        </tr>
        <tr>
        	<th></th>
        	<td><button type="submit" class="tve-button tve-button-green">Connect to GoToWebinar</button></td>
        </tr>
    </table>
    </form>
<?php endif ?>