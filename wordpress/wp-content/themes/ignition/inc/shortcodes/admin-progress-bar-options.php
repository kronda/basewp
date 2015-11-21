<table id="new_progress" style="display: none;">
    <tr>
        <th scope="row">
            <label><?php _e("Label text", 'thrive'); ?></label>
        </th>
        <td>
            <input type="text" class="thrive_shortcode_label" />
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Fill percentage", 'thrive'); ?></label>
        </th>
        <td>
            <input type="number" min="0" max="100" class="thrive_shortcode_percentage" />
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Color", 'thrive');?></label>
        </th>
        <td>
            <select class="thrive_shortcode_option_color">
                <?php foreach ($all_colors as $key => $c): ?>
                    <option value="<?php echo $key; ?>"><?php echo $c; ?></option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
</table>

<table class="form-table postEdit">
    <tr>
        <th scope="row">
            <label><?php _e("Label text", 'thrive'); ?></label>
        </th>
        <td>
            <input type="text" class="thrive_shortcode_label" />
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Fill percentage", 'thrive'); ?></label>
        </th>
        <td>
            <input type="number" min="0" max="100" class="thrive_shortcode_percentage" />
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Color", 'thrive');?></label>
        </th>
        <td>
            <select class="thrive_shortcode_option_color">
                <?php foreach ($all_colors as $key => $c): ?>
                    <option value="<?php echo $key; ?>"><?php echo $c; ?></option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <tr>
        <td><input class="button button-primary" type="button" id="thrive_shortcode_btn_add_progress" value="<?php _e("Add progress bar", 'thrive'); ?>" /></td>
        <td>
            <input class="button button-primary" type="button" id="thrive_shortcode_btn_insert" value="<?php _e("Insert", 'thrive'); ?>" />
        </td>
    </tr>
</table>
<script type="text/javascript">

    jQuery(document).ready(function() {
        jQuery("#thrive_shortcode_btn_add_progress").click(function() {
            jQuery('#new_progress tr').each(function() {
                jQuery('.form-table.postEdit tr:last').before(jQuery(this).clone());
            });
        });

        jQuery("#thrive_shortcode_btn_insert").click(function() {
            var sc_options = {
                'labels': new Array(),
                'percentages': new Array(),
                'colors': new Array()
            };
            jQuery('.thrive_shortcode_label').each(function() {
                sc_options.labels.push(jQuery(this).val().replace(/"/g, '\''));
            });
            sc_options.labels.shift();
            jQuery('.thrive_shortcode_percentage').each(function() {
                sc_options.percentages.push(jQuery(this).val());
            });
            sc_options.percentages.shift();
            jQuery('.thrive_shortcode_option_color').each(function() {
                sc_options.colors.push(jQuery(this).val());
            });
            sc_options.colors.shift();
            

            tb_remove();

            var sc_text = '[thrive_progress_bar count="'+sc_options.labels.length+'"';
            for (var i in sc_options.labels) {
                sc_text += ' label' + i + '="' + sc_options.labels[i] + '" percentage' + i + '="' + sc_options.percentages[i] + '" color' + i + '="' + sc_options.colors[i] + '"';
            }
            sc_text += ']';

            send_to_editor(sc_text);


        });
    });

</script>