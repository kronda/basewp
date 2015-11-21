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
            <label><?php _e("Welcome Back Message", 'thrive');?></label>
        </th>
        <td>
            <textarea id="thrive_shortcode_option_box_headline"><?php _e("Welcome back, {NamePlaceholder}! Click here to continue where you left off!", 'thrive');?></textarea>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Get Started Message", 'thrive');?></label>
        </th>
        <td>
            <textarea id="thrive_shortcode_message_start"><?php _e("Hello, {NamePlaceholder}! Click here to get started!", 'thrive');?></textarea>
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
                'headline': jQuery("#thrive_shortcode_option_box_headline").val().replace(/"/g, '\''),
                'get_started': jQuery("#thrive_shortcode_message_start").val().replace(/"/g, '\'')
            };
            tb_remove();

            var sc_text = '[thrive_welcome_back color="' + sc_options.color + '" welcome_message="' + sc_options.headline + '" start_message="' + sc_options.get_started + '"]';

            send_to_editor(sc_text);


        });
    });

</script>