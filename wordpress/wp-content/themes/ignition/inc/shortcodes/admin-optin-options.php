<?php
$all_optins = get_posts(array('post_type' => "thrive_optin", 'posts_per_page' => -1));
?>
<table class="form-table postEdit">
    <tr>
        <th scope="row">
            <label><?php _e("Thrive Optin", 'thrive'); ?></label>
        </th>
        <td>
            <select id="thrive_shortcode_option_optin">
                <option value='0'></option>
                <?php foreach ($all_optins as $p): ?>
                    <option value='<?php echo $p->ID ?>'><?php echo $p->post_title; ?></option>
                <?php endforeach; ?>
            </select>
        </td>                        
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Button color", 'thrive'); ?></label>
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
            <label><?php _e("Button size", 'thrive'); ?></label>
        </th>
        <td>
            <select id="thrive_shortcode_option_size">
                <option value="small"><?php _e("Small", 'thrive'); ?></option>
                <option value="medium" selected><?php _e("Medium", 'thrive'); ?></option>
                <option value="big"><?php _e("Large", 'thrive'); ?></option>
            </select>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Button text", 'thrive'); ?></label>
        </th>
        <td>
            <input type="text" id="thrive_shortcode_option_button_text" />
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Layout", 'thrive'); ?></label>
        </th>
        <td>
            <select id="thrive_shortcode_option_layout">
                <option value="vertical"><?php _e("Vertical", 'thrive'); ?></option>
                <option value="horizontal" selected><?php _e("Horizontal", 'thrive'); ?></option>
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
                'text': jQuery("#thrive_shortcode_option_button_text").val().replace(/"/g, '\''),
                'size': jQuery("#thrive_shortcode_option_size").val(),
                'optin': jQuery("#thrive_shortcode_option_optin").val(),
                'layout': jQuery("#thrive_shortcode_option_layout").val(),
            };
            
            tb_remove();
            var sc_text = '[thrive_optin color="' + sc_options.color + '" text="' + sc_options.text + '" optin="' + sc_options.optin + '" size="' + sc_options.size + '" layout="' + sc_options.layout + '"]';
            send_to_editor(sc_text);

        });
    });

</script>