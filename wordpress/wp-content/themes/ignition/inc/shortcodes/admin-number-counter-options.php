<table class="form-table postEdit">
    <tr>
        <th scope="row">
            <label><?php _e("Color", 'thrive');?></label>
        </th>
        <td>
            <select id="thrive_shortcode_option_color">
                <?php foreach ($all_colors as $key => $c): ?>
                    <option value="<?php echo $key; ?>"><?php echo $c; ?></option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Value", 'thrive');?></label>
        </th>
        <td>
            <input type="number" id="thrive_shortcode_option_value" >
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Unit before", 'thrive');?></label>
        </th>
        <td>
            <input type="text" id="thrive_shortcode_option_before" >
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Unit after", 'thrive');?></label>
        </th>
        <td>
            <input type="text" id="thrive_shortcode_option_after" >
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Label", 'thrive');?></label>
        </th>
        <td>
            <input type="text" id="thrive_shortcode_option_label" >
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
                'color': jQuery("#thrive_shortcode_option_color").val(),
                'value': jQuery("#thrive_shortcode_option_value").val(),
                'before': jQuery("#thrive_shortcode_option_before").val(),
                'after': jQuery("#thrive_shortcode_option_after").val(),
                'label': jQuery("#thrive_shortcode_option_label").val().replace(/"/g, '\'')
            };
            tb_remove();

            var sc_text = '[thrive_number_counter color="'+ sc_options.color +'" value="'+ sc_options.value +'" before="'+ sc_options.before +'" after="'+ sc_options.after +'" label="'+ sc_options.label +'"]';

            send_to_editor(sc_text);


        });
    });

</script>