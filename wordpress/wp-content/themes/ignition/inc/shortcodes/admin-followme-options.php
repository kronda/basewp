<table class="form-table postEdit">
    <tr>
        <th scope="row">
            <label><?php _e("Facebook", 'thrive'); ?></label>
        </th>
        <td>
            <input type="text" id="thrive_shortcode_option_facebook" />
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Twitter", 'thrive'); ?></label>
        </th>
        <td>
            <input type="text" id="thrive_shortcode_option_twitter" />
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Google profile url", 'thrive'); ?></label>
        </th>
        <td>
            <input type="text" id="thrive_shortcode_option_gprofile" />
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Google page url", 'thrive'); ?></label>
        </th>
        <td>
            <input type="text" id="thrive_shortcode_option_gpage" />
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Linkedin", 'thrive'); ?></label>
        </th>
        <td>
            <input type="text" id="thrive_shortcode_option_linkedin" />
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Pinterest", 'thrive'); ?></label>
        </th>
        <td>
            <input type="text" id="thrive_shortcode_option_pinterest" />
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Dribble", 'thrive'); ?></label>
        </th>
        <td>
            <input type="text" id="thrive_shortcode_option_dribble" />
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Rss", 'thrive'); ?></label>
        </th>
        <td>
            <input type="text" id="thrive_shortcode_option_rss" />
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Youtube", 'thrive'); ?></label>
        </th>
        <td>
            <input type="text" id="thrive_shortcode_option_youtube" />
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Instagram", 'thrive'); ?></label>
        </th>
        <td>
            <input type="text" id="thrive_shortcode_option_instagram" />
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Xing", 'thrive'); ?></label>
        </th>
        <td>
            <input type="text" id="thrive_shortcode_option_xing" />
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

            var sc_options_txt = "";
            if (jQuery("#thrive_shortcode_option_facebook").val() != "") {
                sc_options_txt += " facebook='" + jQuery("#thrive_shortcode_option_facebook").val() + "'";
            }
            if (jQuery("#thrive_shortcode_option_twitter").val() != "") {
                sc_options_txt += " twitter='" + jQuery("#thrive_shortcode_option_twitter").val() + "'";
            }
            if (jQuery("#thrive_shortcode_option_gprofile").val() != "") {
                sc_options_txt += " gprofile='" + jQuery("#thrive_shortcode_option_gprofile").val() + "'";
            }
            if (jQuery("#thrive_shortcode_option_gpage").val() != "") {
                sc_options_txt += " gpage='" + jQuery("#thrive_shortcode_option_gpage").val() + "'";
            }
            if (jQuery("#thrive_shortcode_option_linkedin").val() != "") {
                sc_options_txt += " linkedin='" + jQuery("#thrive_shortcode_option_linkedin").val() + "'";
            }
            if (jQuery("#thrive_shortcode_option_pinterest").val() != "") {
                sc_options_txt += " pinterest='" + jQuery("#thrive_shortcode_option_pinterest").val() + "'";
            }
            if (jQuery("#thrive_shortcode_option_dribble").val() != "") {
                sc_options_txt += " dribble='" + jQuery("#thrive_shortcode_option_dribble").val() + "'";
            }
            if (jQuery("#thrive_shortcode_option_rss").val() != "") {
                sc_options_txt += " rss='" + jQuery("#thrive_shortcode_option_rss").val() + "'";
            }
            if (jQuery("#thrive_shortcode_option_youtube").val() != "") {
                sc_options_txt += " youtube='" + jQuery("#thrive_shortcode_option_youtube").val() + "'";
            }
            if (jQuery("#thrive_shortcode_option_instagram").val() != "") {
                sc_options_txt += " instagram='" + jQuery("#thrive_shortcode_option_instagram").val() + "'";
            }
            if (jQuery("#thrive_shortcode_option_xing").val() != "") {
                sc_options_txt += " xing='" + jQuery("#thrive_shortcode_option_xing").val() + "'";
            }

            if (sc_options_txt === "") {
                alert("Please add at least one option!");
                return false;
            }

            tb_remove();

            var sc_text = "[thrive_follow_me" + sc_options_txt + "]";

            send_to_editor(sc_text);
        });
    });

</script>