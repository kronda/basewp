<h2>Infusionsoft</h2>
<form action="<?php echo admin_url('admin.php?page=tve_api_connect') ?>" method="post">
    <table class="form-table">
        <tbody>
        <tr>
            <th scope="row"><label><?php echo __("Client ID", "thrive-cb") ?>:</label></th>
            <td>
                <input placeholder="<?php echo __("Client ID", "thrive-cb") ?>" type="text" class="text" name="connection[client_id]"
                       value="<?php echo $this->param('client_id', @$_POST['connection']['client_id']) ?>"/>
            </td>
        </tr>
        <tr>
            <th scope="row"><label><?php echo __("API Key", "thrive-cb") ?>:</label></th>
            <td>
                <input placeholder="<?php echo __("API Key", "thrive-cb") ?>" type="text" class="text" name="connection[api_key]"
                       value="<?php echo $this->param('api_key', @$_POST['connection']['api_key']) ?>"/>
            </td>
        </tr>
        <tr>
            <th></th>
            <input type="hidden" name="api" value="<?php echo $this->getKey() ?>"/>
            <td>
                <button type="submit" class="tve-button tve-button-green"><?php echo __("Connect to Infusionsoft", "thrive-cb") ?></button>
            </td>
        </tr>
        </tbody>
    </table>
</form>