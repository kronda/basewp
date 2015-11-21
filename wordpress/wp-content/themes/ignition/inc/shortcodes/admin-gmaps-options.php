<table class="form-table postEdit">
    <tr>
        <th scope="row">
            <label><?php _e("Embed Code", 'thrive'); ?></label>
        </th>
        <td>
            <textarea class="adminWidthInput" id="thrive_shortcode_option_embed_code"></textarea>
        </td>                        
    </tr>
    <tr class="thrive_shortcode_container_video_youtube">
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
                'embed_code': jQuery("#thrive_shortcode_option_embed_code").val()
            };
            tb_remove();

            var sc_text = "[thrive_gmaps]" + sc_options.embed_code + "[/thrive_gmaps]";

            send_to_editor(sc_text);


        });
    });

</script>