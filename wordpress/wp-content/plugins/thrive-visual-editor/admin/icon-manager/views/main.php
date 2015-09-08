<?php if ($this->messages) : ?>
    <?php $this->render('messages'); ?>
<?php endif ?>
<?php if (empty($this->messages['redirect'])) : ?>
    <table class="options_table">
        <tr>
            <td class="thrive_options_branding" colspan="2">
                <img src="<?php echo tve_editor_url() . '/editor/css/images/tcb-logo-large.png'; ?>" class="thrive_admin_logo"/>
            </td>
        </tr>
    </table>

    <div class="thrive-page-settings" style="width: auto;">
        <h3><?php echo __("Thrive Icon Manager", "thrive-cb"); ?></h3>
        <p><?php echo __("Thrive Themes / Content Builder integrate with IcoMoon. Here's how it works:", "thrive-cb") ?></p>
        <ol>
            <li><?php echo sprintf(__("%s to go to the IcoMoon web app and select the icons you want to use in your site", "thrive-cb"), '<a target="_blank" href="//icomoon.io/app/#/select">' . __("Click here", 'thrive-cb') . '</a>') ?></li>
            <li><?php echo __("Download the font file from IcoMoon to your computer", 'thrive-cb') ?></li>
            <li><?php echo __("Upload the font file through the upload button below", "thrive-cb") ?></li>
            <li><?php echo __("Your icons will be available for use!", 'thrive-cb') ?></li>
        </ol>
        <div class="clear"></div>
        <p>&nbsp;</p>
        <h3><?php echo __("Import Icons", "thrive-cb") ?></h3>

        <?php if (!$this->icons) : ?>
            <p><?php echo __("You don't have any icons yet, use the Upload button to import a custom icon pack.", "thrive-cb") ?></p>
        <?php else: ?>
            <p><?php echo __("Your custom icon pack has been loaded. To modify your icon pack, simply upload a new file.", 'thrive-cb') ?></p>
        <?php endif ?>

        <?php $this->render('form') ?>

        <div class="clear"></div>
        <p>&nbsp;</p>

        <?php if ($this->icons) : ?>
            <?php $this->render('icons') ?>
        <?php endif ?>

    </div>
<?php endif ?>