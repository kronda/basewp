<table class="form-table postEdit">
    <tr>
        <th scope="row">
            <label><?php _e("Highligh color", 'thrive'); ?></label>
        </th>
        <td>
            <select id="thrive_shortcode_option_highlight">
                <option value="default">default</option>
                <option value="custom">custom</option>
            </select>
            <input type="text" id="thrive_shortcode_custom_color"/>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Font color", 'thrive'); ?></label>
        </th>
        <td>
            <select id="thrive_shortcode_option_color">
                <option value="light"><?php _e("Light", 'thrive'); ?></option>
                <option value="dark"><?php _e("Dark", 'thrive'); ?></option>
            </select>    
        </td>
    </tr>

    <tr>
        <td></td>
        <td>
            <input class="button button-primary" type="button" id="thrive_shortcode_btn_insert" value="<?php _e("Insert", 'thrive'); ?>" />
        </td>
    </tr>
</table>
<script type="text/javascript">

    jQuery(document).ready(function() {

        jQuery("#thrive_shortcode_custom_color").wpColorPicker();
        jQuery(".wp-picker-container").hide();
        jQuery(".wp-picker-container").css({'top': '10px', 'position': 'relative'});


        jQuery('#thrive_shortcode_option_highlight').change(function() {
            if (jQuery(this).val() == 'custom') {
                jQuery(".wp-picker-container").show();
            } else {
                jQuery(".wp-picker-container").hide();
            }
        });

        jQuery("#thrive_shortcode_btn_insert").click(function() {

            var sc_options = {
                'highlight': jQuery("#thrive_shortcode_option_highlight").val() == 'default' ? 'default' : jQuery("#thrive_shortcode_custom_color").val(),
                'color': jQuery("#thrive_shortcode_option_color").val(),
            };
            tb_remove();

            var sc_text = "[thrive_highlight highlight='" + sc_options.highlight + "' text='" + sc_options.color + "'][/thrive_highlight]";

            send_to_editor(sc_text);


        });
    });

</script>