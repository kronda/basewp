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
            <label><?php _e("Headline", 'thrive');?></label>
        </th>
        <td>
            <input type="text" id="thrive_shortcode_option_box_headline" />
        </td>
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
                'headline': jQuery("#thrive_shortcode_option_box_headline").val().replace(/"/g, '\'')
            };
            tb_remove();

            var sc_text = '[thrive_text_block color="' + sc_options.color + '" headline="' + sc_options.headline + '"] [/thrive_text_block]';

            send_to_editor(sc_text);


        });
    });

</script>