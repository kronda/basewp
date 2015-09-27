<h2>Constant Contact</h2>

<form action="<?php echo admin_url('admin.php?page=tve_api_connect') ?>" method="post">
    <table class="form-table">
        <tbody>
        <tr>
            <th scope="row"><label><?php echo __("API Key", "thrive-cb") ?>:</label></th>
            <td>
                <input id="cc-api-key" placeholder="<?php echo __("API Key", "thrive-cb") ?>" type="text" class="text"
                       name="connection[api_key]"
                       value="<?php echo $this->param('api_key', @$_POST['connection']['api_key']) ?>"/>
                <a id="btn-get-token" href="<?php echo $this->getTokenUrl() ?>" target="_blank"
                   class="tve-button tve-button-green"><?php echo __("Get Token") ?></a>
                <p><?php echo __("To get an API Key you have to follow these steps:", "thrive-cb") ?></p>
                <ol>
                    <li><?php echo sprintf(__("Register a new account %s"), '<a target="_blank" href="https://constantcontact.mashery.com/member/register">' . __("here", "thrive-cb") . '</a>') ?></li>
                    <li><?php echo __("Log in and create a new Application for which the API key will be automatically be generated.", "thrive-cb") ?></li>
                    <li><?php echo __("Copy+Paste the API Key into the field", "thrive-cb") ?></li>
                </ol>
            </td>
        </tr>
        <tr>
            <th scope="row"><label><?php echo __("API token", "thrive-cb") ?>:</label></th>
            <td>
                <input type="hidden" name="api" value="constantcontact"/>
                <input placeholder="<?php echo __("API token", "thrive-cb") ?>" type="text" class="text"
                       name="connection[api_token]"
                       value="<?php echo $this->param('api_token', @$_POST['connection']['api_token']) ?>"/>
                <p><?php echo __("To get an API Token you have to follow these steps:", "thrive-cb") ?></p>
                <ol>
                    <li><?php echo __("After you have completed the steps for getting an API Key you have to click the Get Token Button", "thrive-cb") ?></li>
                    <li><?php echo __("Follow the steps until you receive the token string", "thrive-cb") ?></li>
                    <li><?php echo __("Copy+Paste the token string into the field and click the Connect to Constant Contact button", "thrive-cb") ?></li>
                </ol>
            </td>
        </tr>
        <tr>
            <th></th>
            <td>
                <button type="submit"
                        class="tve-button tve-button-green"><?php echo __("Connect to Constant Contact", "thrive-cb") ?></button>
            </td>
        </tr>
        </tbody>
    </table>
</form>
<script type="text/javascript">
    (function ($) {
        var _token_url = $("#btn-get-token").attr('href');

        $("#btn-get-token").click(function () {
            var api_key = $("#cc-api-key").val(),
                $this = $(this);
            if (!api_key) {
                alert('<?php echo __("Please enter the API Key in order to get the token !", "thrive-cb") ?>');
                return false;
            }
            $this.attr('href', _token_url + api_key);
        });
    })(jQuery);
</script>
