<table class="form-table postEdit">
    <tr>
        <th scope="row">
            <label><?php _e("Letter color", 'thrive');?></label>
        </th>
        <td>
            <select id="thrive_shortcode_option_highlight">
                <?php foreach ($all_colors as $key => $c): ?>
                    <option value="<?php echo $key; ?>"><?php echo $c; ?></option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Drop caps style", 'thrive');?></label>
        </th>
        <td>
            <select id="thrive_shortcode_option_style">
                <option value="1"><?php _e("Letter", 'thrive');?></option>
                <option value="2"><?php _e("Box", 'thrive');?></option>
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
                'color': jQuery("#thrive_shortcode_option_highlight").val(),
                'style': jQuery("#thrive_shortcode_option_style").val(),
            };
            tb_remove();

            var sc_text = "[thrive_drop_caps color='"+sc_options.color+"' style='" + sc_options.style + "'][/thrive_drop_caps]";

            send_to_editor(sc_text);


        });
    });

</script>