<h2>Klick Tipp</h2>
<form action="<?php echo admin_url('admin.php?page=tve_api_connect') ?>" method="post">
    <table class="form-table">
        <tr>
            <th><label><?php echo __("Username:", 'thrive-cb') ?></label></th>
            <td><input type="text" class="text" autocomplete="off" name="kt_user" value="<?php echo $this->param('user', @$_POST['connection']['kt_user']) ?>"/></td>
        </tr>
        <tr>
            <th><label><?php echo __("Password:", "thrive-cb") ?></label></th>
            <td>
                <input type="password" autocomplete="off" class="text" name="kt_password" value="<?php echo $this->param('password', @$_POST['connection']['kt_password']) ?>"/>
                <input type="hidden" name="api" value="<?php echo $this->getKey()?>"/>
            </td>
        </tr>
        <tr>
            <th></th>
            <td><button type="submit" class="tve-button tve-button-green"><?php echo __("Connect to Klick Tipp", "thrive-cb") ?></button></td>
        </tr>
    </table>
</form>
