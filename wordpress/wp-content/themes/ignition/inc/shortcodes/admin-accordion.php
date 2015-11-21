<table id="new_accordion" style="display: none;">
    <tr>
        <th scope="row">
            <label><?php _e("Title", 'thrive'); ?></label>
        </th>
        <td>
            <input type="text" class="thrive_shortcode_accordion_title" />
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Content", 'thrive'); ?></label>
        </th>
        <td>
            <textarea class="thrive_shortcode_accordion_content" > </textarea>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Open by default", 'thrive'); ?></label>
        </th>
        <td>
            <input type="radio" name="default-accordion" />
        </td>
    </tr>
</table>

<table class="form-table postEdit">
    <tr>
        <th scope="row">
            <label><?php _e("Accordion title", 'thrive'); ?></label>
        </th>
        <td>
            <input type="text" id="thrive_shortcode_main_title" />
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Title", 'thrive'); ?></label>
        </th>
        <td>
            <input type="text" class="thrive_shortcode_accordion_title" />
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Content", 'thrive'); ?></label>
        </th>
        <td>
            <textarea class="thrive_shortcode_accordion_content" > </textarea>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Open by default", 'thrive'); ?></label>
        </th>
        <td>
            <input checked type="radio" name="default-accordion" />
        </td>
    </tr>
    <tr>
        <td><input class="button button-primary" type="button" id="thrive_shortcode_btn_add_accordion" value="<?php _e("Add accordion", 'thrive'); ?>" /></td>
        <td>
            <input class="button button-primary" type="button" id="thrive_shortcode_btn_insert" value="<?php _e("Insert", 'thrive'); ?>" />
        </td>
    </tr>
</table>
<script type="text/javascript">

    jQuery(document).ready(function() {
        jQuery("#thrive_shortcode_btn_add_accordion").click(function() {
            jQuery('#new_accordion tr').each(function() {
                jQuery('.form-table.postEdit tr:last').before(jQuery(this).clone());
            });
        });

        jQuery("#thrive_shortcode_btn_insert").click(function() {
            var default_accordion = (jQuery('input:radio[name=default-accordion]:checked').parent().parent().index()) / 3 - 1;
            var main_title = jQuery('#thrive_shortcode_main_title').val().replace(/"/g, '\'');
            var titles = new Array();
            var contents = new Array();
            jQuery('.thrive_shortcode_accordion_title').each(function() {
                titles.push(jQuery(this).val().replace(/"/g, '\''));
            });
            titles.shift();
            jQuery('.thrive_shortcode_accordion_content').each(function() {
                contents.push(jQuery(this).val());
            });
            contents.shift();

            tb_remove();

            var sc_text = '[thrive_accordion_group title="' + main_title + '"]';
            for (var i in titles) {
                sc_text += '[thrive_accordion title="' + titles[i] + '" no="' + (parseInt(i) + 1) + '/' + contents.length + '" default="' + (default_accordion == i ? 'yes' : 'no') + '"]' + contents[i] + '[/thrive_accordion]';
            }
            sc_text += '[/thrive_accordion_group]';

            send_to_editor(sc_text);


        });
    });

</script>