<table class="form-table postEdit">
    <tr>
        <th>
            <label><?php _e("Divider style", 'thrive');?></label>
        </th>
        <td>
            <select id="thrive_shortcode_option_style">
                <option value="left"><?php _e("Left", 'thrive');?></option>
                <option value="centered"><?php _e("Centered", 'thrive');?></option>
                <option value="right"><?php _e("Right", 'thrive');?></option>
                <option value="split"><?php _e("Split", 'thrive');?></option>
                <option value="full"><?php _e("Full", 'thrive');?></option>
            </select>
        </td>
    </tr>    
    <tr class="thrive_shortcode_container_submit_button">
        <td></td>
        <td>
            <input class="button button-primary" type="button" id="thrive_shortcode_btn_insert" value="<?php _e("Insert", 'thrive'); ?>" />
        </td>           
    </tr>
</table>

<script type="text/javascript">

    jQuery(document).ready(function() {
        jQuery("#thrive_shortcode_btn_insert").click(function() {

            var sc_options = {
                'style': jQuery("#thrive_shortcode_option_style").val()
            };
            tb_remove();

            var sc_text = "[divider style='"+ sc_options.style +"']";

            send_to_editor(sc_text);


        });
    });

</script>