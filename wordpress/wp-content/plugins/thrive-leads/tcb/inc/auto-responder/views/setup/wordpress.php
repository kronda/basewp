<h2><?php echo $this->getTitle() ?></h2>
<?php if ($this->isConnected()) : ?>
    <p>Wordpress integration is already setup.</p>
<?php else : ?>
    <p>Click the button below to enable Wordpress user accounts integration.</p>
    <form action="<?php echo admin_url('admin.php?page=tve_api_connect') ?>" method="post">
        <input type="hidden" name="api" value="wordpress"/>
        <button type="submit" class="tve-button tve-button-green">Enable integration with Wordpress</button>
    </form>
<?php endif ?>