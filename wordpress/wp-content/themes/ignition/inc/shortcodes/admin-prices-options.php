<table class="form-table postEdit" id="thrive_shortcode_price_options_table">

    <tr class="thrive_shortcode_price_options" id="thrive_shortcode_price_options1">
        <th scope="row" class="thrive_shortcode_price_options_label_col">
            <label class="thrive_shortcode_price_options_label"><?php _e("Table 1 options", 'thrive'); ?></label>
    <div class="thrive_shortcode_price_remove_table" style="display: none;">
        <a class="pure-button remove thrive_shortcode_price_remove_table_btn" href=""><?php _e("Remove", 'thrive'); ?></a>
    </div>
</th>
<td>
    <div class="thrive_shortcode_price_options_container">
        <?php _e("Header", 'thrive'); ?><br/>
        <input type="text" class="thrive_shortcode_price_header adminWidthInput" /> <br/><br/>
        <?php _e("Items", 'thrive'); ?><br/>
        <textarea class="thrive_shortcode_price_items adminWidthInput"></textarea> <br/><br/>
        <?php _e("Price", 'thrive'); ?>
        <input type="text" class="thrive_shortcode_price_value" style="width:50px" /> /
        <input type="text" class="thrive_shortcode_price_time" value="month" style="width: 70px;" /> <br/><br/>
        <?php _e("Button text", 'thrive'); ?><br/>
        <input type="text" class="thrive_shortcode_price_btn_txt adminWidthInput" /> <br/><br/>
        <?php _e("Button link", 'thrive'); ?><br/>
        <input type="text" class="thrive_shortcode_price_btn_link adminWidthInput" /> <br/><br/>
        <?php _e("Button color", 'thrive'); ?><br/> 
        <select class="thrive_shortcode_price_btn_color">
            <?php foreach ($all_colors as $key => $c): ?>
                <option value="<?php echo $key; ?>"><?php echo $c; ?></option>
            <?php endforeach; ?>
        </select> <br/><br/>
        <?php _e("Button size", 'thrive'); ?><br/> 
        <select class="thrive_shortcode_price_btn_size">
            <option value="small"><?php _e("Small", 'thrive'); ?></option>
            <option value="medium" selected><?php _e("Medium", 'thrive'); ?></option>
            <option value="big"><?php _e("Large", 'thrive'); ?></option>
        </select><br/><br/>
        <?php _e("Highlight this table", 'thrive'); ?> 
        <input type="checkbox" class="thrive_shortcode_price_hightlight" />
    </div>
</td>                        
</tr>

</table>

<table class="form-table postEdit">
    <tr>
        <td colspan="2" class="thrive_shortcode_submit_container"><input class="pure-button upload" type="button" id="thrive_shortcode_price_add_table" value="<?php _e("Add new table", 'thrive'); ?>" /></td>
    </tr>
    <tr class="thrive_shortcode_submit_container">
        <td colspan="2">
            <input class="button button-primary" type="button" id="thrive_shortcode_btn_insert" value="<?php _e("Insert", 'thrive'); ?>" />
        </td>           
    </tr>
</table>

<script type="text/javascript">

    jQuery(document).ready(function() {

        jQuery("#thrive_shortcode_price_add_table").click(function() {
            if (jQuery(".thrive_shortcode_price_options").length >= 5) {
                alert("You've reached the maximum number of tables that you can add per shortcode.");
                return false;
            }
            var _new_table_row = jQuery("#thrive_shortcode_price_options1").clone();
            _new_table_row.find(".thrive_shortcode_price_options_label").html("Table " + (jQuery(".thrive_shortcode_price_options").length + 1) + " options");

            jQuery(".thrive_shortcode_price_options_container").hide();
            _new_table_row.find(".thrive_shortcode_price_options_container").show();
            _new_table_row.find(".thrive_shortcode_price_remove_table").show();
            _new_table_row.find(".thrive_shortcode_price_remove_table_btn").click(function(event) {
                event.preventDefault();
                jQuery(this).parents(".thrive_shortcode_price_options").remove();
                jQuery(".thrive_shortcode_price_options").each(function(index) {
                    jQuery(this).find(".thrive_shortcode_price_options_label").html("Table " + (index + 1) + " options");
                });
                return false;
            });
            _new_table_row.attr('id', "");
            _new_table_row.find(".thrive_shortcode_price_options_label").click(function() {
                jQuery(".thrive_shortcode_price_options_container").hide();
                jQuery(this).parents(".thrive_shortcode_price_options").find(".thrive_shortcode_price_options_container").show();
            });

            jQuery("#thrive_shortcode_price_options_table").append(_new_table_row);
        });

        jQuery(".thrive_shortcode_price_options_label").click(function() {
            jQuery(".thrive_shortcode_price_options_container").hide();
            jQuery(this).parents(".thrive_shortcode_price_options").find(".thrive_shortcode_price_options_container").show();
        });

        jQuery("#thrive_shortcode_btn_insert").click(function() {
            var _no_cols = jQuery(".thrive_shortcode_price_options").length;
            var sc_text = "";
            jQuery(".thrive_shortcode_price_options").each(function(index) {
                switch (_no_cols) {
                    case 2:
                        if (index == 0) {
                            sc_text += "[price_one_half ";
                        } else {
                            sc_text += "[price_one_half_last ";
                        }
                        break;
                    case 3:
                        if (index == 0) {
                            sc_text += "[price_one_third_first ";
                        } else if (index == jQuery(".thrive_shortcode_price_options").length - 1) {
                            sc_text += "[price_one_third_last ";
                        } else {
                            sc_text += "[price_one_third ";
                        }
                        break;
                    case 4:
                        if (index == 0) {
                            sc_text += "[price_one_fourth_first ";
                        } else if (index == jQuery(".thrive_shortcode_price_options").length - 1) {
                            sc_text += "[price_one_fourth_last ";
                        } else {
                            sc_text += "[price_one_fourth ";
                        }
                        break;

                    case 5:
                        if (index == 0) {
                            sc_text += "[price_one_fifth_first ";
                        } else if (index == jQuery(".thrive_shortcode_price_options").length - 1) {
                            sc_text += "[price_one_fifth_last ";
                        } else {
                            sc_text += "[price_one_fifth ";
                        }
                        break;
                    default:
                        sc_text += "[price_one ";
                }

                if (jQuery(this).find(".thrive_shortcode_price_hightlight").prop('checked') ) {
                    sc_text += "highlight='1' ";
                }

                sc_text += 'title="' + jQuery(this).find('.thrive_shortcode_price_header').val().replace(/"/g, '\'') + '" price="' + jQuery(this).find('.thrive_shortcode_price_value').val() + '" time="' + jQuery(this).find('.thrive_shortcode_price_time').val() + '" btn_text="' + jQuery(this).find('.thrive_shortcode_price_btn_txt').val().replace(/"/g, '\'') + '" btn_link="' + jQuery(this).find('.thrive_shortcode_price_btn_link').val() + '" btn_color="' + jQuery(this).find('.thrive_shortcode_price_btn_color').val() + '" btn_size="' + jQuery(this).find('.thrive_shortcode_price_btn_size').val() + '"]';

                var _items = jQuery(this).find('.thrive_shortcode_price_items').val().split('\n');
                for (var i = 0; i < _items.length; i++) {
                    sc_text += _items[i];
                    if (i != _items.length - 1) {
                        sc_text += "[*]";
                    }
                }

                switch (_no_cols) {
                    case 2:
                        if (index == 0) {
                            sc_text += "[/price_one_half]";
                        } else {
                            sc_text += "[/price_one_half_last]";
                        }
                        break;
                    case 3:
                        if (index == 0) {
                            sc_text += "[/price_one_third_first]";
                        } else if (index == jQuery(".thrive_shortcode_price_options").length - 1) {
                            sc_text += "[/price_one_third_last]";
                        } else {
                            sc_text += "[/price_one_third]";
                        }
                        break;
                    case 4:
                        if (index == 0) {
                            sc_text += "[/price_one_fourth_first]";
                        } else if (index == jQuery(".thrive_shortcode_price_options").length - 1) {
                            sc_text += "[/price_one_fourth_last]";
                        } else {
                            sc_text += "[/price_one_fourth]";
                        }
                        break;

                    case 5:
                        if (index == 0) {
                            sc_text += "[/price_one_fifth_first]";
                        } else if (index == jQuery(".thrive_shortcode_price_options").length - 1) {
                            sc_text += "[/price_one_fifth_last]";
                        } else {
                            sc_text += "[/price_one_fifth]";
                        }
                        break;
                    default:
                        sc_text += "[/price_one]";
                }
            });

            send_to_editor(sc_text);

        });

    });
</script>