<table class="options_table">
    <tr>
        <td class="thrive_options_branding" colspan="2">
            <img src="<?php echo tve_editor_url() . '/editor/css/images/tcb-logo-large.png'; ?>" class="thrive_admin_logo"/>
        </td>
    </tr>
</table>
<div class="thrive-page-settings">
    <h3><?php echo __("Custom Font Manager", "thrive-cb"); ?></h3>
    <br/>
    <table class="form-table fm" style="width: 100%; text-align: left;">
        <thead>
            <tr>
                <th style="width: 16%;">
                    <?php echo __("Font name", "thrive-cb") ?>
                </th>
                <th style="width: 10%;">
                    <?php echo __("Size", "thrive-cb") ?>
                </th>
                <th style="width: 13%;">
                    <?php echo __("Color", 'thrive-cb') ?>
                </th>
                <th style="width: 25%;">
                    <?php echo __("CSS Class Name", "thrive-cb") ?>
                </th>
                <th style="width: 36%;">
                    <?php echo __("Actions", 'thrive-cb') ?>
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
                        <a class='fm-b edit-font'><?php echo __("Edit", "thrive-cb") ?></a>
                        <a class='fm-b duplicate-font'><?php echo __("Duplicate", 'thrive-cb') ?></a>
                        <a class="fm-b delete-font"><?php echo __("Delete", "thrive-cb") ?></a>
                        <input type="hidden" class="font-id" value="<?php echo $font['font_id']; ?>" >
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5">
                    <input class="font-button thrive_options pure-button upload" id="thrive-add-font" type="button" value="<?php echo __("+ Add Custom Font", "thrive-cb") ?>" >
                    <input style="float: right;" class="font-button thrive_options pure-button upload" id="thrive-update-posts" type="button" value="<?php echo __("Update Posts", "thrive-cb") ?>" >
                    <input type="hidden" value="<?php echo $new_font_id; ?>" id='new-font-id' >
                </td>
            </tr>
        </tfoot>
    </table>
</div>
<script type="text/javascript" >
    jQuery(document).ready(function() {
        jQuery('#thrive-add-font').click(function() {
            var font_id = jQuery('#new-font-id').val();
            tb_show('Edit shortcode options', 'admin-ajax.php?action=display_font_manager&font_id='+font_id);
        });
        
        jQuery('#thrive-update-posts').click(function() {
            jQuery.post('admin-ajax.php?action=thrive_font_manager_update_posts_fonts', function(response) {
            });
        });

        jQuery('a.edit-font').click(function() {
            var font_id = jQuery(this).siblings('.font-id').val();
            tb_show('Edit shortcode options', 'admin-ajax.php?action=display_font_manager&font_action=update&font_id=' + font_id);
        });

        jQuery('a.delete-font').click(function() {
            var font_id = jQuery(this).siblings('.font-id').val();
            var postData = {
                font_id: font_id
            };
            jQuery.post('admin-ajax.php?action=thrive_font_manager_delete', postData, function(response) {
                location.reload();
            });
        });
        jQuery('a.duplicate-font').click(function() {
            var font_id = jQuery(this).siblings('.font-id').val();
            var postData = {
                font_action: 'duplicate',
                font_id: font_id
            };
            jQuery.post('admin-ajax.php?action=thrive_font_manager_duplicate', postData, function(response) {
                location.reload();
            });
        });
    });
</script>