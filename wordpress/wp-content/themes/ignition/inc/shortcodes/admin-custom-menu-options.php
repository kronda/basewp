<table class="form-table postEdit">
    <tr>
        <th scope="row">
            <label><?php _e("Title", 'thrive'); ?></label>
        </th>
        <td>
            <input type="text" id="thrive_shortcode_option_title" />
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Menu", 'thrive'); ?></label>
        </th>
        <td>
            <select id="thrive_shortcode_option_menu">
                <?php foreach ($all_menus as $menu): ?>
                    <option value="<?php echo $menu['id']; ?>"><?php echo $menu['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </td>                        
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Display thumbnails", 'thrive'); ?></label>
        </th>
        <td>
            <input type='checkbox' value='1' id='thrive_shortcode_option_thumbnails' />
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
                'title': jQuery("#thrive_shortcode_option_title").val(),
                'menu': jQuery("#thrive_shortcode_option_menu").val()
            };
            if (jQuery('#thrive_shortcode_option_thumbnails').prop('checked')) {
                sc_options.thumbnails = "on";
            } else {
                sc_options.thumbnails = "off";
            }
            var _sc_menu_txt = '';
            if (sc_options.menu > 0) {
                _sc_menu_txt = ' menu="' + sc_options.menu + '"';
            }
            tb_remove();

            var sc_text = '[thrive_custom_menu' + _sc_menu_txt + ' title="' + sc_options.title + '" thumbnails="' + sc_options.thumbnails + '"]';

            send_to_editor(sc_text);
        });
    });

</script>