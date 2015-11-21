<table class="form-table postEdit">
    <tr>
        <th scope="row">
            <label><?php _e("Title", 'thrive'); ?></label>
        </th>
        <td>
            <input type="text" id="thrive_shortcode_option_title"/>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Number of posts", 'thrive'); ?></label>
        </th>
        <td>
            <input type="text" id="thrive_shortcode_option_no_posts" value='5'/>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Show", 'thrive'); ?></label>
        </th>
        <td>
            <select id="thrive_shortcode_option_filter">
                <option value='recent'><?php _e("Recent posts", 'thrive'); ?></option>
                <option value='popular'><?php _e("Popular posts", 'thrive'); ?></option>
            </select>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Category", 'thrive'); ?></label>
        </th>
        <td>
            <select id="thrive_shortcode_option_category">
                <?php foreach ($categories_array as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("User", 'thrive'); ?></label>
        </th>
        <td>
            <select id="thrive_shortcode_option_user">
                <option value="-1"><?php _e("All", 'thrive'); ?></option>
                <?php foreach ($all_users as $user): ?>
                    <option value="<?php echo $user->ID; ?>">
                        <?php echo $user->display_name == '' ? $user->user_nicename : $user->display_name; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Display thumbnails", 'thrive'); ?></label>
        </th>
        <td>
            <input type='checkbox' value='1' id='thrive_shortcode_option_thumbnails'/>
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <input class="button button-primary" type="button" id="thrive_shortcode_btn_insert" value="<?php _e("Insert", 'thrive'); ?>"/>
        </td>
    </tr>
</table>

<script type="text/javascript">

    jQuery(document).ready(function () {
        jQuery("#thrive_shortcode_btn_insert").click(function () {

            var sc_options = {
                'title': jQuery("#thrive_shortcode_option_title").val().replace(/"/g, '\''),
                'no_posts': jQuery("#thrive_shortcode_option_no_posts").val(),
                'filter': jQuery("#thrive_shortcode_option_filter").val(),
                'category': jQuery("#thrive_shortcode_option_category").val(),
                'user': jQuery("#thrive_shortcode_option_user").val()
            };
            if (jQuery('#thrive_shortcode_option_thumbnails').prop('checked')) {
                sc_options.thumbnails = "on";
            } else {
                sc_options.thumbnails = "off";
            }
            var _sc_cat_txt = "";
            if (sc_options.category > 0) {
                _sc_cat_txt = " category='" + sc_options.category + "'";
            }
            tb_remove();

            var sc_text = '[thrive_lessons_list' + _sc_cat_txt + ' user="' + sc_options.user + '" title="' + sc_options.title + '" no_posts="' + sc_options.no_posts + '" filter="' + sc_options.filter + '" thumbnails="' + sc_options.thumbnails + '"]';

            send_to_editor(sc_text);
        });
    });

</script>