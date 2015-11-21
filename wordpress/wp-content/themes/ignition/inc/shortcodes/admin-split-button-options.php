<table class="form-table postEdit">
    <tr>
        <th scope="row">
            <label><?php _e("Left Text", 'thrive');?></label>
        </th>
        <td>
            <input type="text" id="thrive_shortcode_left_text">
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Left Link", 'thrive');?></label>
        </th>
        <td>
            <input type="text" id="thrive_shortcode_left_link">
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Left Color", 'thrive');?></label>
        </th>
        <td>
            <select id="thrive_shortcode_left_color">
                <?php foreach ($all_colors as $key => $c): ?>
                    <option value="<?php echo $key; ?>"><?php echo $c; ?></option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Right Text", 'thrive');?></label>
        </th>
        <td>
            <input type="text" id="thrive_shortcode_right_text">
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Right Link", 'thrive');?></label>
        </th>
        <td>
            <input type="text" id="thrive_shortcode_right_link">
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Right Color", 'thrive');?></label>
        </th>
        <td>
            <select id="thrive_shortcode_right_color">
                <?php foreach ($all_colors as $key => $c): ?>
                    <option value="<?php echo $key; ?>"><?php echo $c; ?></option>
                <?php endforeach; ?>
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
                'left_color': jQuery("#thrive_shortcode_left_color").val(),
                'right_color': jQuery("#thrive_shortcode_right_color").val(),
                'left_text': jQuery("#thrive_shortcode_left_text").val().replace(/"/g, '\''),
                'right_text': jQuery("#thrive_shortcode_right_text").val().replace(/"/g, '\''),
                'left_link': jQuery("#thrive_shortcode_left_link").val(),
                'right_link': jQuery("#thrive_shortcode_right_link").val() 
            };
            tb_remove();

            var sc_text = '[thrive_split_button left_color="' + sc_options.left_color + '" right_color="' + sc_options.right_color + '" left_text="' + sc_options.left_text + '" right_text="' + sc_options.right_text + '" left_link="' + sc_options.left_link + '" right_link="' + sc_options.right_link + '"]';

            send_to_editor(sc_text);


        });
    });

</script>