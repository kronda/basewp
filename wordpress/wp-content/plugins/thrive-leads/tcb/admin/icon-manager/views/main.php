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
        <h3><?php _e("Thrive Icon Manager"); ?></h3>
        <p>Thrive Themes / Content Builder integrate with IcoMoon. Here's how it works: </p>
        <ol>
            <li><a target="_blank" href="//icomoon.io/app/#/select">Click here</a> to go to the IcoMoon web app and select the icons you want to use in your site</li>
            <li>Download the font file from IcoMoon to your computer</li>
            <li>Upload the font file through the upload button below</li>
            <li>Your icons will be available for use!</li>
        </ol>
        <div class="clear"></div>
        <p>&nbsp;</p>
        <h3>Import Icons</h3>

        <?php if (!$this->icons) : ?>
            <p>You don't have any icons yet, use the Upload button to import a custom icon pack.</p>
        <?php else : ?>
            <p>Your custom icon pack has been loaded. To modify your icon pack, simply upload a new file.</p>
        <?php endif ?>

        <?php $this->render('form') ?>

        <div class="clear"></div>
        <p>&nbsp;</p>

        <?php if ($this->icons) : ?>
            <?php $this->render('icons') ?>
        <?php endif ?>

    </div>
<?php endif ?>