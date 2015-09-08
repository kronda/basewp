<h2>Mail Poet</h2>
<?php if ($this->pluginInstalled()) : ?>
    <?php if ($this->isConnected()) : ?>
        <p><?php echo __("MailPoet integration is already setup.", "thrive-cb") ?></p>
    <?php else : ?>
        <p><?php echo __("Click the button below to enable MailPoet integration.", "thrive-cb") ?></p>
        <form action="<?php echo admin_url('admin.php?page=tve_api_connect') ?>" method="post">
            <input type="hidden" name="api" value="mailpoet"/>
            <button type="submit" class="tve-button tve-button-green"><?php echo __("Enable integration with MailPoet", "thrive-cb") ?></button>
        </form>
    <?php endif ?>
<?php else : ?>
    <p><?php echo __("You currently do not have the MailPoet WP plugin installed or activated.", "thrive-cb") ?></p>
<?php endif ?>

