<div>    
    <br/>
    <table class="thrive-sc-tab-options-container form-table postEdit">
        <tr>
            <th scope="row">
                <label><?php _e("Layout", 'thrive'); ?></label>
            </th>
            <td>
                <select id="thrive-sel-tabs-sc-layout">
                    <option value="horz"><?php _e("Horizontal", 'thrive'); ?></option>
                    <option value="vert"><?php _e("Vertical", 'thrive'); ?></option>
                </select>
            </td>
        </tr>
        <tr class="thrive-sc-tab-options-headline">
            <th scope="row">
                <label><?php _e("Tab", 'thrive'); ?> 1</label>
            </th>
            <td>
                <input type="text" class="txt-thrive-tab-headline" />
                <a class="link-thrive-sc-remove-tab pure-button remove"><?php _e("Remove", 'thrive'); ?></a>
            </td>
        </tr> 
        <tr class="thrive-sc-tab-options-headline">
            <th scope="row">
                <label><?php _e("Tab", 'thrive'); ?> 2</label>
            </th>
            <td>
                <input type="text" class="txt-thrive-tab-headline" />
                <a class="link-thrive-sc-remove-tab pure-button remove"><?php _e("Remove", 'thrive'); ?></a>
            </td>
        </tr>
    </table>
    <table>
        <tr class="thrive-sc-tab-options-headline-clone" style="display: none;">
            <th scope="row">
                <label><?php _e("Tab", 'thrive'); ?> 1</label>
            </th>
            <td>
                <input type="text" class="txt-thrive-tab-headline" />
                <a class="link-thrive-sc-remove-tab pure-button remove"><?php _e("Remove", 'thrive'); ?></a>
            </td>
        </tr> 
    </table> 
    <table class="form-table">
        <tr>
            <th scope="row">
                <input class="button button-primary" type="button" id="thrive_shortcode_btn_add_new_tab" value="<?php _e("Add new tab", 'thrive'); ?>" />
            </th>
            <td>
                <input class="pure-button upload" type="button" id="thrive_shortcode_btn_insert" value="<?php _e("Insert", 'thrive'); ?>" />
            </td>
        </tr>
    </table>
</div>



<script type="text/javascript">

    jQuery(document).ready(function() {

        jQuery("#thrive_shortcode_btn_add_new_tab").click(function() {
            var clone_item = jQuery(".thrive-sc-tab-options-headline-clone").clone();
            clone_item.attr('class', 'thrive-sc-tab-options-headline');

            clone_item.find(".link-thrive-sc-remove-tab").click(function() {
                jQuery(this).parent().parent().remove();
                ThriveScOptionUpdateTabsLabels();
            });

            clone_item.fadeIn();
            jQuery(".thrive-sc-tab-options-container").append(clone_item);

            ThriveScOptionUpdateTabsLabels();
        });

        jQuery(".link-thrive-sc-remove-tab").click(function() {
            jQuery(this).parent().parent().remove();
            ThriveScOptionUpdateTabsLabels();
        });

        var ThriveScOptionUpdateTabsLabels = function() {
            jQuery('.thrive-sc-tab-options-headline label').each(function(index) {
                jQuery(this).html("Tab " + (index + 1));
            });
        };

        ThriveScOptionUpdateTabsLabels();

        jQuery('#thrive_shortcode_btn_insert').click(function() {
            var sc_text = '';
            var no_tabs = jQuery(".txt-thrive-tab-headline").length - 1;
            jQuery(".txt-thrive-tab-headline").each(function(index) {
                var headline_txt_val = jQuery(this).val().replace(/"/g, '\'');
                if (headline_txt_val !== '') {
                    if (index === 0) {
                        sc_text += '[thrive_tab headline="' + headline_txt_val + '" no="' + (index + 1) + '/' + (no_tabs) + '"]Enter your content here[/thrive_tab]';
                    } else if (index === jQuery('.thrive-sc-tab-options-headline').length - 1) {
                        sc_text += '[thrive_tab headline="' + headline_txt_val + '" no="' + (index + 1) + '/' + (no_tabs) + '"]Enter your content here[/thrive_tab]';
                    } else {
                        sc_text += '[thrive_tab headline="' + headline_txt_val + '" no="' + (index + 1) + '/' + (no_tabs) + '"]Enter your content here[/thrive_tab]';
                    }
                }
            });
            var sc_full_text = '[thrive_tabs layout="' + jQuery('#thrive-sel-tabs-sc-layout').val() + '"]' + sc_text + '[/thrive_tabs]';
            send_to_editor(sc_full_text);


        });
    });

</script>