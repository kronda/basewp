<table class="form-table postEdit">
    <tr>
        <th scope="row">
            <label><?php _e("Max width", 'thrive'); ?></label>
        </th>
        <td>
            <input class="adminWidthInput" id="thrive_shortcode_option_max_width" value="500"/>
        </td>                        
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Alignment", 'thrive'); ?></label>
        </th>
        <td>
            <input type="radio" class="thrive_shortcode_option_align" value="left" checked name="thrive_shortcode_option_align"/> <?php _e("Left", 'thrive');?>
            <input type="radio" class="thrive_shortcode_option_align" value="center" name="thrive_shortcode_option_align"/> <?php _e("Center", 'thrive');?>
            <input type="radio" class="thrive_shortcode_option_align" value="right" name="thrive_shortcode_option_align"/> <?php _e("Right", 'thrive');?>
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
                'max_width' : jQuery("#thrive_shortcode_option_max_width").val(),
                'align' : jQuery(".thrive_shortcode_option_align:checked").val()
            };
            tb_remove();

            var sc_text = "[content_container max_width='" + sc_options.max_width + "' align='" + sc_options.align + "']YourContentHere[/content_container]";

            send_to_editor(sc_text);


        });
    });

</script>