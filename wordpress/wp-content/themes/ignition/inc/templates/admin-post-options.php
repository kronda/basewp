<div class="option_tabs left">
    <div class="thrive-admin-submenu">
        <a id="thrive-link-general-options" rel="general-options"><?php _e("Display Options", 'thrive'); ?></a>
        <a id="thrive-link-focus-areas" rel="focus-areas"><?php _e("Focus Areas", 'thrive'); ?></a>
        <a id="thrive-link-custom-code" rel="custom-code"><?php _e("Custom Code", 'thrive'); ?></a>
        <a id="thrive-link-social-media" rel="social-media"><?php _e("Social Media", 'thrive'); ?></a>
        <div class="clear"></div>
    </div>
</div>
<div class="option_window left">
    <div class="options-container">
        <div id="thrive-admin-container">
            <div class="thrive-admin-subcontainer" id="thrive-admin-subcontainer-general-options">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="thrive_post_template"> <?php _e("Post Template", 'thrive'); ?></label>
                        </th>
                        <td>
                            <select id='thrive_post_template' name='thrive_meta_post_template'>
                                <?php foreach ($post_templates as $tpl_name): ?>
                                    <?php
                                    $selected = ($tpl_name == $value_post_template) ? "selected" : "";
                                    echo "<option value='" . $tpl_name . "' " . $selected . ">" . $tpl_name . "</option>";
                                    ?>
                                <?php endforeach ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for=""><?php _e("Post Title", 'thrive') ?></label>
                        </th>
                        <td>
                            <input type="radio" value="1"
                                   name="thrive_meta_show_post_title" <?php if ($value_show_post_title == 1): ?>checked<?php endif ?> /> Show
                            <input type="radio" value="0"
                                   name="thrive_meta_show_post_title" <?php if ($value_show_post_title == 0): ?>checked<?php endif ?> /> Hide
                        </td>
                    </tr>
                    <tr <?php if (get_post_type() == TT_APPR_POST_TYPE_PAGE):?>style="display:none;"<?php endif;?>>
                        <th scope="row">
                            <label for=""><?php _e("Post Meta Information", 'thrive') ?></label>
                        </th>
                        <td>
                            <input type="radio" value="on"
                                   name="thrive_meta_post_meta_info" <?php if ($value_post_meta_info == "on"): ?>checked<?php endif ?> /> On
                            <input type="radio" value="default"
                                   name="thrive_meta_post_meta_info" <?php if ($value_post_meta_info == "default"): ?>checked<?php endif ?> /> Default
                            <input type="radio" value="off"
                                   name="thrive_meta_post_meta_info" <?php if ($value_post_meta_info == "off"): ?>checked<?php endif ?> /> Off
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for=""><?php _e("Breadcrumbs", 'thrive') ?></label>
                        </th>
                        <td>
                            <input type="radio" value="on"
                                   name="thrive_meta_post_breadcrumbs" <?php if ($value_post_bradcrumbs == "on"): ?>checked<?php endif ?> /> On
                            <input type="radio" value="default"
                                   name="thrive_meta_post_breadcrumbs" <?php if ($value_post_bradcrumbs == "default"): ?>checked<?php endif ?> /> Default
                            <input type="radio" value="off"
                                   name="thrive_meta_post_breadcrumbs" <?php if ($value_post_bradcrumbs == "off"): ?>checked<?php endif ?> /> Off
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for=""><?php _e("Featured Image", 'thrive') ?></label>
                        </th>
                        <td>
                            <input type="radio" value="default"
                                   name="thrive_meta_post_featured_image" <?php if ($value_post_featured_image == "default"): ?>checked<?php endif ?> /> Default
                            <input type="radio" value="thumbnail"
                                   name="thrive_meta_post_featured_image" <?php if ($value_post_featured_image == "thumbnail"): ?>checked<?php endif ?> /> Thumbnail
                            <input type="radio" value="wide"
                                   name="thrive_meta_post_featured_image" <?php if ($value_post_featured_image == "wide"): ?>checked<?php endif ?> /> Wide
                            <input type="radio" value="off"
                                   name="thrive_meta_post_featured_image" <?php if ($value_post_featured_image == "off"): ?>checked<?php endif ?> /> No featured image
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for=""><?php _e("Display Share Buttons", 'thrive') ?></label>
                        </th>
                        <td>
                            <input type="radio" value="default"
                                   name="thrive_meta_post_share_buttons" <?php if ($value_post_share_buttons != "off"): ?>checked<?php endif ?> /> <?php _e("Default", 'thrive'); ?>
                            <input type="radio" value="off"
                                   name="thrive_meta_post_share_buttons" <?php if ($value_post_share_buttons == "off"): ?>checked<?php endif ?> /> <?php _e("Off", 'thrive'); ?>
                        </td>
                    </tr>
	                <tr>
		                <th scope="row">
			                <label for=""><?php _e("Show Floating Icons by Default", 'thrive') ?></label>
		                </th>
		                <td>
			                <input type="radio" value="default"
			                       name="thrive_meta_post_floating_icons" <?php if ($value_post_floating_icons == "default"): ?>checked<?php endif ?> /> <?php _e("Default", 'thrive'); ?>
			                <input type="radio" value="on"
			                       name="thrive_meta_post_floating_icons" <?php if ($value_post_floating_icons == "on"): ?>checked<?php endif ?> /> <?php _e("On", 'thrive'); ?>
			                <input type="radio" value="off"
			                       name="thrive_meta_post_floating_icons" <?php if ($value_post_floating_icons == "off"): ?>checked<?php endif ?> /> <?php _e("Off", 'thrive'); ?>
		                </td>
	                </tr>
                    <?php if (thrive_get_theme_options("related_posts_box") == 1): ?>
                        <tr>
                            <th scope="row">
                                <label for=""><?php _e("Show Related Posts", 'thrive') ?></label>
                            </th>
                            <td>
                                <input type="radio" value="on"
                                       name="thrive_meta_post_related_box" <?php if ($value_post_related_box != "of"): ?>checked<?php endif ?> /> On
                                <input type="radio" value="off"
                                       name="thrive_meta_post_related_box" <?php if ($value_post_related_box == "off"): ?>checked<?php endif ?> /> Off
                            </td>
                        </tr>
                    <?php endif; ?>
                </table>
            </div>

            <div class="thrive-admin-subcontainer" id="thrive-admin-subcontainer-focus-areas">
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <?php _e("Top Focus Area", 'thrive'); ?>
                        </th>
                        <td>
                            <input type='radio' value='default'
                                   name='thrive_meta_post_focus_area_top' <?php if ($value_post_focus_area_top == "default" || $value_post_focus_area_top == ""): ?>checked<?php endif; ?> /> Default
                            <input type='radio' value='hide'
                                   name='thrive_meta_post_focus_area_top' <?php if ($value_post_focus_area_top == "hide"): ?>checked<?php endif; ?> /> Hide
                            <input type='radio' value='custom'
                                   name='thrive_meta_post_focus_area_top' <?php if ($value_post_focus_area_top != "default" && $value_post_focus_area_top != "hide" && $value_post_focus_area_top != ""): ?>checked<?php endif; ?> /> Custom

                            <select name='thrive_meta_post_focus_area_top_select'>
                                <?php foreach ($queryFocusAreas->get_posts() as $p): ?>
                                    <option value='<?php echo $p->ID ?>'
                                            <?php if ($value_post_focus_area_top == $p->ID): ?>selected<?php endif; ?>><?php echo $p->post_title; ?></option>
                                        <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <?php _e("Bottom Focus Area", 'thrive'); ?>
                        </th>
                        <td>
                            <input type='radio' value='default'
                                   name='thrive_meta_post_focus_area_bottom' <?php if ($value_post_focus_area_bottom == "default" || $value_post_focus_area_bottom == ""): ?>checked<?php endif; ?> /> Default
                            <input type='radio' value='hide'
                                   name='thrive_meta_post_focus_area_bottom' <?php if ($value_post_focus_area_bottom == "hide"): ?>checked<?php endif; ?> /> Hide
                            <input type='radio' value='custom'
                                   name='thrive_meta_post_focus_area_bottom' <?php if ($value_post_focus_area_bottom != "default" && $value_post_focus_area_bottom != "hide" && $value_post_focus_area_bottom != ""): ?>checked<?php endif; ?> /> Custom

                            <select name='thrive_meta_post_focus_area_bottom_select'>
                                <?php
                                foreach ($queryFocusAreas->get_posts() as $p):
                                    $focus_area_template = get_post_custom_values("_thrive_meta_focus_template", $p->ID);
                                    ?>
                                    <?php if (isset($focus_area_template[0]) && $focus_area_template[0] != "Template6"): ?>
                                        <option value='<?php echo $p->ID ?>'
                                                <?php if ($value_post_focus_area_bottom == $p->ID): ?>selected<?php endif; ?>><?php echo $p->post_title; ?></option>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="thrive-admin-subcontainer" id="thrive-admin-subcontainer-custom-code">
                <table  class="form-table">
                    <tr>
                        <th scope="row">
                            <label for=""><?php _e("Header scripts", 'thrive') ?></label><br/>
                        </th>
                        <td>
                            <textarea name="thrive_meta_post_header_scripts"><?php echo $value_post_header_scripts; ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for=""><?php _e("Opening Body Scripts", 'thrive') ?></label><br/>
                            <span class="tooltips" title="<?php echo __('Enter the scripts you want to load right after the opening body tag on each page. Typically used for Google Tag Manager..  Example: ' . htmlentities('&lt;script src=\'/path/to/file/script.js\'>&lt;/script>') . '. <a href=\'http://thrivethemes.com/tkb_item/load-scriptscustom-css-individual-postspages-thrive-themes/\'> Read more about custom scripts here</a>.'); ?>"></span>
                        </th>
                        <td>
                            <textarea name="thrive_meta_post_body_scripts_top"><?php echo $value_post_body_scripts_top; ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for=""><?php _e("Body Scripts", 'thrive') ?></label><br/>
                        </th>
                        <td>
                            <textarea name="thrive_meta_post_body_scripts"><?php echo $value_post_body_scripts; ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for=""><?php _e("Custom CSS", 'thrive') ?></label><br/>
                        </th>
                        <td>
                            <textarea name="thrive_meta_post_custom_css"><?php echo $value_post_custom_css; ?></textarea>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="thrive-admin-subcontainer" id="thrive-admin-subcontainer-social-media">
                <table  class="form-table">
                    <tr>
                        <th scope="row" colspan="2">
                            <?php if (thrive_get_theme_options("social_site_meta_enable") != 1):?>
                                <b><?php _e("The social media meta option is disabled. If you want to use it please enable it from Thrive Options -> Social Media -> Social Sharing Data", 'thrive');?></b> <br/><br/>
                            <?php endif;?>
                            <?php echo _e('Social media meta data allows you to control the title, image and description of what is shared on the various social media networks for a higher click through rate. When creating this meta data, think about what will elicit the highest click through rate for your content in order to maximise your social media results. <br><br>You must be sure to fill in all fields marked with a * for the meta data to display.', 'thrive'); ?>
                            <?php if (is_plugin_active('wordpress-seo/wp-seo.php')): ?>
                                <?php echo '<br><br>' . __('We see you have WP SEO enabled in your account.  By adding social media markup in this section here, your WP SEO social media markup settings will be overridden.', 'thrive'); ?>
                            <?php endif; ?>
                        </th>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for=""><?php _e("Title *", 'thrive') ?></label>
                        </th>
                        <td>
                            <input value="<?php echo $thrive_meta_social_data_title; ?>" type="text" class="thrive_post_input_large" name="thrive_meta_social_data_title"/>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for=""><?php _e("Description *", 'thrive') ?></label><br/>
                        </th>
                        <td>
                            <textarea name="thrive_meta_social_data_description"><?php echo $thrive_meta_social_data_description; ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for=""><?php _e("Image", 'thrive') ?></label>
                        </th>
                        <td>
                            <input value="<?php echo $thrive_meta_social_image; ?>" type="text" class="thrive_post_input_large" name="thrive_meta_social_image" id="thrive_meta_social_image"/>
                            <input type="button" class="thrive_options pure-button upload" id="thrive_meta_social_button_upload" value="Upload"/>
                            <input type="button" class="thrive_options pure-button clear-field remove" id="thrive_meta_social_button_remove" value="Remove"/>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">
                            <label for=""><?php _e("Twitter Author Username", 'thrive') ?></label>
                        </th>
                        <td>
                            <input value="<?php echo $thrive_meta_social_twitter_username; ?>" type="text" class="thrive_post_input_large" name="thrive_meta_social_twitter_username"/>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="clear"></div>