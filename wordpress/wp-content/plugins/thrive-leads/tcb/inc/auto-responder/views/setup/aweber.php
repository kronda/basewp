
<h2>AWeber</h2>

<?php if ($this->isConnected()) : ?>
    <p>You are already connected to AWeber.</p>
<?php else : ?>
    <?php
    try {
        $url = $this->getAuthorizeUrl();
        ?>
        <p>Click the button below to login to your AWeber account and authorize the API Connection.</p>
        <a class="tve-button tve-button-green" href="<?php echo $url ?>">Connect to AWeber</a><?php
    } catch (Thrive_Api_AWeber_Exception $e) {
        $url = false;
        $message = $e->getMessage();
        $api_url = isset($e->url) ? $e->url : false;
        ?><p style="color: red">There has been an error while communicating with AWeber API. Below are the error details: </p>
        <?php echo $message;
        if ($api_url) {
            echo ' (API URL: ' . $api_url . ')';
        }
    }
    ?>
<?php endif ?>