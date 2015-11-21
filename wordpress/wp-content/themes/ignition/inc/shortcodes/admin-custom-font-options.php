<table class="form-table postEdit">
    <tr>
        <th>
            <label><?php _e("Custom Font", 'thrive'); ?></label>
        </th>
        <td>
            <select id="thrive_font_select">
                <?php foreach ($fonts as $font): ?>
                <option value="<?php echo $font->font_id; ?>"><?php echo $font->font_name . ' ' . $font->font_size ?></option>;
                <?php endforeach; ?>
            </select>
        </td>
    </tr>   
    <tr>
        <th>
            <label>Text</label>
        </th>
        <td>
            <textarea id="thrive_font_text"></textarea>
        </td>
    </tr>   
    <tr class="thrive_shortcode_container_submit_button">
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
                'font': jQuery("#thrive_font_select").val(),
                'text': jQuery("#thrive_font_text").val()
            };
            tb_remove();

            var sc_text = "[thrive_custom_font id='" + sc_options.font + "']" + sc_options.text + "[/thrive_custom_font]";

            send_to_editor(sc_text);


        });
    });

</script>