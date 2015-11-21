<table class="form-table postEdit">
    <tr>
        <th scope="row">
            <label><?php _e("Call to Action Text", 'thrive'); ?></label>
        </th>
        <td>
            <input type="text" id="thrive_shortcode_option_phone_text" />
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Mobile Call to Action Text", 'thrive'); ?></label>
        </th>
        <td>
            <input type="text" id="thrive_shortcode_option_mobile_phone_text" />
        </td>
    </tr>    
    <tr>
        <th scope="row">
            <label><?php _e("Mobile Button Color", 'thrive'); ?></label>
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
            <label><?php _e("Phone Number", 'thrive'); ?></label>
        </th>
        <td>
            <input type="text" id="thrive_shortcode_option_phone_no" />
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
        jQuery("#thrive_shortcode_btn_insert").click(function() {

            var sc_options = {
                'phone_text': jQuery("#thrive_shortcode_option_phone_text").val().replace(/"/g, '\''),
                'phone_no': jQuery("#thrive_shortcode_option_phone_no").val().replace(/"/g, '\''),
                'mobile_phone_text' : jQuery("#thrive_shortcode_option_mobile_phone_text").val().replace(/"/g, '\''),
                'color' : jQuery("#thrive_shortcode_option_color").val()
            };
            
            tb_remove();

            var sc_text = '[thrive_custom_phone' + ' phone_text="' + sc_options.phone_text + '" mobile_phone_text="' + sc_options.mobile_phone_text + '" phone_no="' + sc_options.phone_no + '" color="' + sc_options.color + '"]';

            send_to_editor(sc_text);
        });
    });

</script>