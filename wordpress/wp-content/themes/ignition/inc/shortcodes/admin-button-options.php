<table class="form-table postEdit">
    <tr>
        <th scope="row">
            <label><?php _e("Button color", 'thrive');?></label>
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
            <label><?php _e("Button text", 'thrive');?></label>
        </th>
        <td>
            <input type="text" id="thrive_shortcode_option_button_text" />
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Button url", 'thrive');?></label>
        </th>
        <td>
            <input type="text" id="thrive_shortcode_option_button_url" />
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Open link in", 'thrive');?></label>
        </th>
        <td>
            <input type="radio" value="_self" class="thrive_shortcode_option_button_tab" name="thrive_shortcode_option_button_tab" checked /> <?php _e("Same tab", 'thrive');?>
            <input type="radio" value="_blank" class="thrive_shortcode_option_button_tab" name="thrive_shortcode_option_button_tab" /> <?php _e("New tab", 'thrive');?>
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
        <th scope="row">
            <label><?php _e("Button alignment", 'thrive');?></label>
        </th>
        <td>
            <input type="radio" value="" class="thrive_shortcode_option_button_align" name="thrive_shortcode_option_button_align" checked value="normal" /> <?php _e("In place", 'thrive');?>
            <input type="radio" value="left" class="thrive_shortcode_option_button_align" name="thrive_shortcode_option_button_align" value="left" /> <?php _e("Left", 'thrive');?>
            <input type="radio" value="aligncenter" class="thrive_shortcode_option_button_align" name="thrive_shortcode_option_button_align" value="center" /> <?php _e("Center", 'thrive');?>
            <input type="radio" value="right" class="thrive_shortcode_option_button_align" name="thrive_shortcode_option_button_align" value="right" /> <?php _e("Right", 'thrive');?>
            <input type="radio" value="full" class="thrive_shortcode_option_button_align" name="thrive_shortcode_option_button_align" value="full" /> <?php _e("Full width", 'thrive');?>
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
                'text': jQuery("#thrive_shortcode_option_button_text").val(),
                'link': jQuery("#thrive_shortcode_option_button_url").val(),
                'size': jQuery("#thrive_shortcode_option_size").val(),
                'target': jQuery(".thrive_shortcode_option_button_tab:checked").val(),
                'align': jQuery(".thrive_shortcode_option_button_align:checked").val()
            };
            tb_remove();

            var sc_text = "[thrive_link color='" + sc_options.color + "'  link='" + sc_options.link + "' target='" + sc_options.target + "' size='" + sc_options.size + "' align='" + sc_options.align + "']" + sc_options.text + "[/thrive_link]";

            send_to_editor(sc_text);


        });
    });

</script>