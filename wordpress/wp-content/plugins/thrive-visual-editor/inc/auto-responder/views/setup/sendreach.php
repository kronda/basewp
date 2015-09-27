<h2>SendReach</h2>
<form action="<?php echo admin_url('admin.php?page=tve_api_connect') ?>" method="post">
    <input type="hidden" name="api" value="<?php echo $this->getKey() ?>"/>
    <table class="form-table">
        <tbody>
        <tr>
            <th scope="row"><label><?php echo __("Key", "thrive-cb") ?>:</label></th>
            <td>
                <input placeholder="<?php echo __("Key", "thrive-cb") ?>" type="text" class="text" name="connection[key]"
                       value="<?php echo $this->param('key', @$_POST['connection']['key']) ?>"/>
            </td>
        </tr>
        <tr>
            <th scope="row"><label><?php echo __("Secret", "thrive-cb") ?>:</label></th>
            <td>
                <input placeholder="<?php echo __("Secret", "thrive-cb") ?>" type="text" class="text" name="connection[secret]"
                       value="<?php echo $this->param('secret', @$_POST['connection']['secret']) ?>"/>
            </td>
        </tr>
        <tr>
            <th></th>
            <td>
                <button type="submit" class="tve-button tve-button-green"><?php echo __("Connect to SendReach", "thrive-cb") ?></button>
            </td>
        </tr>
        </tbody>
    </table>
</form>
