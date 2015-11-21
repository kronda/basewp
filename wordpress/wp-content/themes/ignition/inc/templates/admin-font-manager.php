<table class="options_table">
    <tr>
        <td class="thrive_options_branding" colspan="2">
            <?php require "partial-share-links.php"; ?>
        </td>
    </tr>
</table>
<div class="thrive-page-settings">
    <h3><?php _e("Custom Font Manager", 'thrive'); ?></h3>

    <p>
        <?php echo __("By default, Thrive Themes integrates with Google Fonts. This allows you to choose from 600+ fonts for use in your content. However, you can also use the blue import font button below to import your own fonts files using a service called Font Squirrel"); ?>
        <a href="javascript:void"><?php echo __("Learn more about how to import your own fonts", 'thrive') ?></a>
    </p>

    <a class="button button-primary"
       href="<?php echo admin_url("admin.php?page=thrive_font_import_manager") ?>"><?php echo __("Import custom font manager", 'thrive') ?></a>

    <table class="form-table fm" style="width: 100%; text-align: left;">
        <thead>
        <tr>
            <th style="width: 16%;">
                <?php _e("Font name", 'thrive'); ?>
            </th>
            <th style="width: 10%;">
                <?php _e("Size", 'thrive'); ?>
            </th>
            <th style="width: 13%;">
                <?php _e("Color", 'thrive'); ?>
            </th>
            <th style="width: 25%;">
                <?php _e("CSS Class Name", 'thrive'); ?>
            </th>
            <th style="width: 36%;">
                <?php _e("Actions", 'thrive'); ?>
            </th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <?php foreach ($font_options as $font): ?>
            <td><?php echo $font['font_name']; ?></td>
            <td><?php echo $font['font_size']; ?></td>
            <td>
                <div class="cdi">
                    <span class="cd" style="background-color: <?php echo $font['font_color']; ?>;"></span>
                    <?php echo $font['font_color']; ?>
                </div>
            </td>
            <td><input type="text" readonly value="<?php echo $font['font_class']; ?>"></td>
            <td>
                <a class='fm-b edit-font'><?php _e("Edi", 'thrive'); ?>t</a>
                <a class='fm-b duplicate-font'><?php _e("Duplicate", 'thrive'); ?></a>
                <a class="fm-b delete-font"><?php _e("Delete", 'thrive'); ?></a>
                <input type="hidden" class="font-id" value="<?php echo $font['font_id']; ?>">
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
        <tr>
            <td colspan="4"></td>
            <td>
                <a style="float: right;" id="thrive-add-font" href="javascript:void(0)"><?php _e("+ Add Custom Font", 'thrive'); ?></a>
            </td>
        </tr>
        <tr>
            <td colspan="5">
                <a style="float: right" id="thrive-update-posts" class="font-button thrive_options pure-button upload" href="javascript:void(0)"><?php _e("Update Posts", 'thrive'); ?></a>
                <input type="hidden" value="<?php echo $new_font_id; ?>" id='new-font-id'>
            </td>
        </tr>
        </tfoot>
    </table>
</div>
<script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery('#thrive-add-font').click(function () {
            var font_id = jQuery('#new-font-id').val();
            tb_show('Edit shortcode options', 'admin-ajax.php?action=display_font_manager&font_id=' + font_id);
        });

        jQuery('#thrive-update-posts').click(function () {
            jQuery.post('admin-ajax.php?action=thrive_font_manager_update_posts_fonts', function (response) {
            });
        });

        jQuery('a.edit-font').click(function () {
            var font_id = jQuery(this).siblings('.font-id').val();
            tb_show('Edit shortcode options', 'admin-ajax.php?action=display_font_manager&font_action=update&font_id=' + font_id);
        });

        jQuery('a.delete-font').click(function () {
            var font_id = jQuery(this).siblings('.font-id').val();
            var postData = {
                font_id: font_id
            };
            jQuery.post('admin-ajax.php?action=thrive_font_manager_delete', postData, function (response) {
                location.reload();
            });
        });
        jQuery('a.duplicate-font').click(function () {
            var font_id = jQuery(this).siblings('.font-id').val();
            var postData = {
                font_action: 'duplicate',
                font_id: font_id
            };
            jQuery.post('admin-ajax.php?action=thrive_font_manager_duplicate', postData, function (response) {
                location.reload();
            });
        });
    });
</script>
<script src="https://apis.google.com/js/platform.js" async defer></script>
<script>!function (d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0], p = /^http:/.test(d.location) ? 'http' : 'https';
        if (!d.getElementById(id)) {
            js = d.createElement(s);
            js.id = id;
            js.src = p + '://platform.twitter.com/widgets.js';
            fjs.parentNode.insertBefore(js, fjs);
        }
    }(document, 'script', 'twitter-wjs');</script>