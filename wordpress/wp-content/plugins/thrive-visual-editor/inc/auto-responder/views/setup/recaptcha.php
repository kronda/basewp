<h2>ReCaptcha</h2>
<form action="<?php echo admin_url('admin.php?page=tve_api_connect') ?>" method="post">
    <table class="form-table">
        <tr>
            <th><label><?php echo __("Site Key:", 'thrive-cb') ?></label></th>
            <td><input type="text" class="text" autocomplete="off" name="site_key" value="<?php echo $this->param('site_key', @$_POST['site_key']); ?>"/></td>
        </tr>
        <tr>
            <th><label><?php echo __("Secret Key:", "thrive-cb") ?></label></th>
            <td>
                <input type="text" autocomplete="off" class="text" name="secret_key" value="<?php echo $this->param('secret_key', @$_POST['secret_key']); ?>"/>
                <input type="hidden" name="api" value="<?php echo $this->getKey()?>"/>
            </td>
        </tr>
        <tr>
            <th></th>
            <td><button type="submit" class="tve-button tve-button-green"><?php echo __("Connect ReCaptcha", "thrive-cb") ?></button></td>
        </tr>
    </table>
</form>
