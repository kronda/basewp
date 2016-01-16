<h2>Postmark</h2>
<?php $admin_email = get_option('admin_email');
?>
<p class="tve-form-description"><?php echo __('Postmark requires your email to be verified before allowing any emails to be sent. Please be sure that the email you set here matches the email you confirmed on the postmark website.', 'thrive-leads') ?></p>
<form action="<?php echo admin_url('admin.php?page=tve_api_connect') ?>" method="post">
    <table class="form-table">
        <tbody>
        <tr>
            <th scope="row"><label><?php echo __("Postmark-approved email address", "thrive-cb") ?>:</label></th>
            <td>
                <?php $connection_email = $this->param('email', @$_POST['connection']['email']);  ?>
                <input placeholder="<?php echo __("Email", "thrive-cb") ?>" type="text" class="text" name="connection[email]" value="<?php if(isset($connection_email)) { echo $connection_email;  } else { echo $admin_email; }  ?>"/>
                <input type="hidden" name="api" value="postmark"/>
            </td>
        </tr>
        <tr>
            <th scope="row"><label><?php echo __("API key", "thrive-cb") ?>:</label></th>
            <td>
                <input placeholder="<?php echo __("API key", "thrive-cb") ?>" type="text" class="text" name="connection[key]" value="<?php echo $this->param('key', @$_POST['connection']['key']) ?>"/>
                <input type="hidden" name="api" value="postmark"/>
            </td>
        </tr>
        <tr>
            <th></th>
            <td><button type="submit" class="tve-button tve-button-green"><?php echo __("Save Postmark API Key", "thrive-cb") ?></button></td>
        </tr>
        </tbody>
    </table>
</form>