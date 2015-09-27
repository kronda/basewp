
<h2>AWeber</h2>

<?php if ($this->isConnected()) : ?>
    <p><?php echo __("You are already connected to AWeber.", "thrive-cb") ?></p>
<?php else : ?>
    <?php
    try {
        $url = $this->getAuthorizeUrl();
        ?>
        <p><?php echo __("Click the button below to login to your AWeber account and authorize the API Connection.", "thrive-cb") ?></p>
        <a class="tve-button tve-button-green" href="<?php echo $url ?>"><?php echo __("Connect to AWeber", "thrive-cb") ?></a><?php
    } catch (Thrive_Api_AWeber_Exception $e) {
        $url = false;
        $message = $e->getMessage();
        $api_url = isset($e->url) ? $e->url : false;
        ?><p style="color: red"><?php echo __("There has been an error while communicating with AWeber API. Below are the error details:", "thrive-cb") ?></p>
        <?php echo $message;
        if ($api_url) {
            echo ' (API URL: ' . $api_url . ')';
        }
    }
    ?>
<?php endif ?>
