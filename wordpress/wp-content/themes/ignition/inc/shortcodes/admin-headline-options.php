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
            <label><?php _e("Button size", 'thrive');?></label>
        </th>
        <td>
            <select id="thrive_shortcode_option_size">
                <option value="small"><?php _e("Small", 'thrive');?></option>
                <option value="medium" selected><?php _e("Medium", 'thrive');?></option>
                <option value="big"><?php _e("Large", 'thrive');?></option>
            </select>
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <input class="button button-primary" type="button" id="thrive_shortcode_btn_insert" value="<?php _e("Insert", 'thrive');?>" />
        </td>
    </tr>
</table>
<script type="text/javascript">

    jQuery(document).ready(function() {
        jQuery("#thrive_shortcode_btn_insert").click(function() {

            var sc_options = {
                'color': jQuery("#thrive_shortcode_option_color").val(),
                'size': jQuery("#thrive_shortcode_option_size").val()                
            };
            tb_remove();

            var sc_text = "[thrive_headline color='" + sc_options.color + "' size='" + sc_options.size + "']Your Headline Here[/thrive_headline]";

            send_to_editor(sc_text);


        });
    });

</script>