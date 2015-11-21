<table class="form-table postEdit">
    <tr>
        <th scope="row">
            <label><?php _e("Headline title", 'thrive');?></label>
        </th>
        <td>
            <input type="text" id="thrive_shortcode_headline_title" />
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Alignment", 'thrive');?></label>
        </th>
        <td>
            <select id="thrive_shortcode_option_orientation">
                <option value="left"><?php _e("Left", 'thrive');?></option>
                <option value="right"><?php _e("Right", 'thrive');?></option>
                <option value="center"><?php _e("Center", 'thrive');?></option>
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
                'title': jQuery("#thrive_shortcode_headline_title").val().replace(/"/g, '\''),
                'orientation': jQuery("#thrive_shortcode_option_orientation").val(),
            };
            tb_remove();

            var sc_text = '[thrive_headline_focus title="'+sc_options.title+'" orientation="' + sc_options.orientation + '"]';

            send_to_editor(sc_text);


        });
    });

</script>