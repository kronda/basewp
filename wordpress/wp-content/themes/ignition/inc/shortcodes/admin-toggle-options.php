<table id="new_toggle" style="display: none;">
    <tr>
        <th scope="row">
            <label><?php _e("Title", 'thrive'); ?></label>
        </th>
        <td>
            <input type="text" class="thrive_shortcode_toggle_title" />
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Content", 'thrive'); ?></label>
        </th>
        <td>
            <textarea class="thrive_shortcode_toggle_content" > </textarea>
        </td>
    </tr>
</table>

<table class="form-table postEdit">
    <tr>
        <th scope="row">
            <label><?php _e("Title", 'thrive'); ?></label>
        </th>
        <td>
            <input type="text" class="thrive_shortcode_toggle_title" />
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Content", 'thrive'); ?></label>
        </th>
        <td>
            <textarea class="thrive_shortcode_toggle_content" > </textarea>
        </td>
    </tr>
    <tr>
        <td><input class="button button-primary" type="button" id="thrive_shortcode_btn_add_toggle" value="<?php _e("Add toggle", 'thrive'); ?>" /></td>
        <td>
            <input class="button button-primary" type="button" id="thrive_shortcode_btn_insert" value="<?php _e("Insert", 'thrive'); ?>" />
        </td>
    </tr>
</table>
<script type="text/javascript">

    jQuery(document).ready(function() {
        jQuery("#thrive_shortcode_btn_add_toggle").click(function() {
            jQuery('#new_toggle tr').each(function() {
                jQuery('.form-table.postEdit tr:last').before(jQuery(this).clone());
            });
        });

        jQuery("#thrive_shortcode_btn_insert").click(function() {
            var title = jQuery('#thrive_shortcode_main_title').val();
            var titles = new Array();
            var contents = new Array();
            jQuery('.thrive_shortcode_toggle_title').each(function() {
                titles.push(jQuery(this).val().replace(/"/g, '\''));
            });
            titles.shift();
            jQuery('.thrive_shortcode_toggle_content').each(function() {
                contents.push(jQuery(this).val());
            });
            contents.shift();

            tb_remove();
            
            var sc_text = '[thrive_toggles_group"]';
            for (var i in titles) {
                sc_text += '[thrive_toggles title="' + titles[i] + '" no="' + (parseInt(i) + 1) + '/' + contents.length + '"]' + contents[i] + '[/thrive_toggles]';
            }
            sc_text += '[/thrive_toggles_group]';

            send_to_editor(sc_text);
        });
    });

</script>