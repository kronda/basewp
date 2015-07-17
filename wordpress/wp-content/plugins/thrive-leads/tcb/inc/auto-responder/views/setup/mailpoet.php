<h2>Mail Poet</h2>
<?php if ($this->pluginInstalled()) : ?>
    <?php if ($this->isConnected()) : ?>
        <p>MailPoet integration is already setup.</p>
    <?php else : ?>
        <p>Click the button below to enable MailPoet integration.</p>
        <form action="<?php echo admin_url('admin.php?page=tve_api_connect') ?>" method="post">
            <input type="hidden" name="api" value="mailpoet"/>
            <button type="submit" class="tve-button tve-button-green">Enable integration with MailPoet</button>
        </form>
    <?php endif ?>
<?php else : ?>
    <p>You currently do not have the MailPoet WP plugin installed or activated.</p>
<?php endif ?>