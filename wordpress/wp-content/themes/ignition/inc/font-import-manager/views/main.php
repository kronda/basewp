<?php
/** @var $this Thrive_Font_Import_Manager_View */
?>

<?php if ($this->messages) : ?>
    <?php $this->render('messages') ?>
<?php endif; ?>

<table class="options_table">
    <tr>
        <td class="thrive_options_branding" colspan="2">
            <img src="<?php echo $this->logo_url ?>" class="thrive_admin_logo"/>
        </td>
    </tr>
</table>

<div class="thrive-page-settings" style="width: auto;">
    <h3><?php echo __("Font Import Manager", $this->domain) ?></h3>

    <p><?php echo sprintf(__("Thrive Themes integrates with %s so that you can upload your own font files for use in your web site.", $this->domain), '<a target="_blank" href="//www.fontsquirrel.com/">Font Squirrel</a>') ?></p>

    <h4><?php echo __("Follow these steps to import custom fonts into your site:", $this->domain) ?></h4>
    <ol>
        <li><?php echo sprintf(__("Download one or more fonts from one of the many font libraries on the web. These files should be ttf or otf format. One such font library is: %s", $this->domain), '<a target="_blank" href="//dafont.com">www.dafont.com</a>') ?></li>
        <li><?php echo sprintf(__("Once downloaded to your computer, you can then upload each font to the Font Squirrel generator tool here: %s", $this->domain), '<a target="_blank" href="//www.fontsquirrel.com/tools/webfont-generator">www.fontsquirrel.com/tools/webfont-generator</a>') ?></li>
        <li><?php echo __("Once all your font files are uploaded, you can download the .zip file that is produced to your computer", $this->domain) ?></li>
        <li><?php echo __('Upload this file to your site using the "Upload" button below and then click the "Save and Generate Fonts" button', $this->domain) ?></li>
        <li><?php echo __("Once generated, your fonts will immediately become accessible from the font manager", $this->domain) ?></li>
    </ol>

    <h3><?php echo __("Import Fonts", $this->domain) ?></h3>

    <?php $this->render('form'); ?>

    <h3><?php echo __("Your Custom Fonts", $this->domain) ?></h3>

    <?php if ($this->font_pack && $this->font_pack['font_families']) : ?>
        <ul id="thrive-fonts-list">
            <li class="thrive-head">
                <div class="thrive-labels"><?php echo __("Name", $this->domain) ?></div>
                <div class="thrive-content"><?php echo __("Preview", $this->domain) ?></div>
                <div class="clear"></div>
            </li>
            <?php foreach ($this->font_pack['font_families'] as $family) : ?>
                <li>
                    <div class="thrive-labels">
                        <p><?php echo $family ?></p>
                    </div>
                    <div class="thrive-content">
                        <p style="font-family: '<?php echo $family ?>'">
                            Grumpy wizards make toxic brew for the evil Queen and Jack.
                        </p>
                    </div>
                    <div class="clear"></div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else : ?>
        <p><?php echo __("No custom fonts added", $this->domain) ?></p>
    <?php endif; ?>

    <p>
        <a style="float: right;" class="button" href="<?php echo admin_url('admin.php?page=thrive_font_manager'); ?>">
            <?php echo __("Return to Font Manager", $this->domain) ?>
        </a>
        <div class="clear"></div>
    </p>
</div>
