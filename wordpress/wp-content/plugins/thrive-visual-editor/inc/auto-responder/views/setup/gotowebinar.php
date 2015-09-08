<h2>GoToWebinar</h2>
<?php if ($this->isConnected() && $this->expiresIn() > 30) : ?>
    <p><?php echo __("GoToWebinar is connected. The access token expires on:", "thrive-cb") ?> <strong><?php echo $this->getExpiryDate() ?></strong></p>
<?php else : ?>
    <form action="<?php echo admin_url('admin.php?page=tve_api_connect') ?>" method="post">
	<table class="form-table">
        <?php if ($this->isExpired()) : ?>
            <p><?php echo __("The GoToWebinar access token has expired on:", "thrive-cb") ?> <strong><?php echo $this->getExpiryDate() ?></strong>. <?php echo __("You need to renew the token by providing your GoToWebinar credentials below", "thrive-cb") ?></p>
        <?php elseif ($this->isConnected() && $this->expiresIn() <= 30) : ?>
            <p><?php echo sprintf(__("The GoToWebinar access token will expire in <strong>%s days</strong>. Renew the token by providing your GoToWebinar credentials below", "thrive-cb"), $this->expiresIn()) ?></p>
        <?php else : ?>
            <p><?php echo __("Fill in your GoToWebinar username (email) and password below to connect", "thrive-cb") ?></p>
        <?php endif ?>
        <tr>
        	<th><label><?php echo __("Email:", "thrive-cb") ?></label></th>
        	<td><input type="text" class="text" autocomplete="off" name="gtw_email" value="<?php echo $this->param('gtw_email', @$_POST['gtw_email']) ?>"/></td>
        </tr>
        <tr>
	        <th><label><?php echo __("Password:", "thrive-cb") ?></label></th>
	        <td>
				<input type="password" autocomplete="off" class="text" name="gtw_password" value=""/>
		        <input type="hidden" name="api" value="<?php echo $this->getKey()?>"/>
	        </td>
        </tr>
        <tr>
        	<th></th>
        	<td><button type="submit" class="tve-button tve-button-green"><?php echo __("Connect to GoToWebinar", "thrive-cb") ?></button></td>
        </tr>
    </table>
    </form>
<?php endif ?>

