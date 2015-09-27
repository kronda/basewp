<h2>Drip</h2>
<form action="<?php echo admin_url('admin.php?page=tve_api_connect') ?>" method="post">
    <table class="form-table">
        <tbody>
        <tr>
            <th scope="row"><label><?php echo __("Client ID", "thrive-cb") ?>:</label></th>
            <td>
                <input placeholder="<?php echo __("Client ID", "thrive-cb") ?>" type="text" class="text"
                       name="connection[client_id]"
                       value="<?php echo $this->param('client_id', @$_POST['connection']['client_id']) ?>"/>
            </td>
        </tr>
        <tr>
            <th scope="row"><label><?php echo __("API token", "thrive-cb") ?>:</label></th>
            <td>
                <input type="hidden" name="api" value="drip"/>
                <input placeholder="<?php echo __("API token", "thrive-cb") ?>" type="text" class="text"
                       name="connection[token]"
                       value="<?php echo $this->param('token', @$_POST['connection']['token']) ?>"/>
            </td>
        </tr>
        <tr>
            <th></th>
            <td>
                <button type="submit"
                        class="tve-button tve-button-green"><?php echo __("Connect to Drip", "thrive-cb") ?></button>
            </td>
        </tr>
        </tbody>
    </table>
</form>
