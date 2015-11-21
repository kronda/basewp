<table class="form-table postEdit">
    <tr>
        <th scope="row">
            <label><?php _e("Quote", 'thrive');?></label>
        </th>
        <td>
            <textarea class="adminWidthInput" id="thrive_shortcode_option_quote"></textarea>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Alignment", 'thrive');?></label>
        </th>
        <td>
            <input type="radio" name="thrive_shortcode_option_alignement" class="thrive_shortcode_option_alignement"
                   value="left" /> <?php _e("Left", 'thrive');?>
            <input type="radio" name="thrive_shortcode_option_alignement" class="thrive_shortcode_option_alignement"
                   value="normal" checked/> <?php _e("In place", 'thrive');?>
            <input type="radio" name="thrive_shortcode_option_alignement" class="thrive_shortcode_option_alignement"
                   value="right"/> <?php _e("Right", 'thrive');?>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Cite (optional)", 'thrive');?></label>
        </th>
        <td>
            <input class="adminWidthInput" type="text" id="thrive_shortcode_option_cite" />
        </td>
    </tr>
    <tr>
        <td>
            <input class="button button-primary" type="button" id="thrive_shortcode_btn_insert" value="<?php _e("Insert", 'thrive');?>" />
        </td>    
    </tr>
</table>

<script type="text/javascript">

    jQuery(document).ready(function() {
        jQuery("#thrive_shortcode_btn_insert").click(function() {

            var sc_options = {
                'quote': jQuery("#thrive_shortcode_option_quote").val(),
                'align': jQuery(".thrive_shortcode_option_alignement:checked").val(),
                'cite': jQuery("#thrive_shortcode_option_cite").val().replace(/"/g, '\'')
            };
            tb_remove();

            var sc_text = '[pullquote align="' + sc_options.align + '"]' + sc_options.quote + ' [/pullquote]';

            if (sc_options.cite !== '') {
                sc_text = '[pullquote align="' + sc_options.align + '" cite="' + sc_options.cite + '"]' + sc_options.quote + '[/pullquote]';
            }

            send_to_editor(sc_text);


        });
    });

</script>