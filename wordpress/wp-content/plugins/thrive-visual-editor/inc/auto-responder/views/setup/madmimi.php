<h2>MadMimi</h2>
<form action="<?php echo admin_url('admin.php?page=tve_api_connect') ?>" method="post">
    <table class="form-table">
        <tbody>
        <tr>
            <th scope="row"><label><?php echo __("API key", "thrive-cb") ?>:</label></th>
            <td>
                <input placeholder="<?php echo __("API key", "thrive-cb") ?>" type="text" class="text" name="connection[key]" value="<?php echo $this->param('key', @$_POST['connection']['key']) ?>"/>
                <input type="hidden" name="api" value="madmimi"/>
            </td>
        </tr>
        <tr>
            <th scope="row"><label><?php echo __("Email", "thrive-cb") ?>:</label></th>
            <td>
                <input placeholder="<?php echo __("Email", "thrive-cb") ?>" type="text" class="text" name="connection[username]" value="<?php echo $this->param('username', @$_POST['connection']['username']) ?>"/>
                <input type="hidden" name="api" value="madmimi"/>
            </td>
        </tr>
        <tr>
            <th></th>
            <td><button type="submit" class="tve-button tve-button-green"><?php echo __("Save MadMimi API Key", "thrive-cb") ?></button></td>
        </tr>
        </tbody>
    </table>
</form>
