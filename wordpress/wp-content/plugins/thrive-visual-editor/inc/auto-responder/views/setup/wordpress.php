<h2><?php echo $this->getTitle() ?></h2>
<?php if ($this->isConnected()) : ?>
    <p<?php echo __("Wordpress integration is already setup.", "thrive-cb") ?></p>
<?php else : ?>
    <p><?php echo __("Click the button below to enable Wordpress user accounts integration.", "thrive-cb") ?></p>
    <form action="<?php echo admin_url('admin.php?page=tve_api_connect') ?>" method="post">
        <input type="hidden" name="api" value="wordpress"/>
        <button type="submit" class="tve-button tve-button-green"><?php echo __("Enable integration with Wordpress", "thrive-cb") ?></button>
    </form>
<?php endif ?>

