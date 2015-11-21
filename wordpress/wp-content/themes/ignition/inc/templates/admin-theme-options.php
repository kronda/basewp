<script charset="ISO-8859-1" src="//fast.wistia.com/assets/external/popover-v1.js"></script>
<table class="options_table" cellpadding="0" cellspacing="0">
    <tr>
        <td class="thrive_options_branding" colspan="2">
            <?php require "partial-share-links.php"; ?>
        </td>
    </tr>
</table>
<div class="option_tabs left">
    <div class="thrive-admin-submenu">
        <a id="thrive-link-general-options" rel="general-options"><?php _e("General Settings", 'thrive'); ?></a>
        <a id="thrive-link-style-options"
           rel="style-options"><?php _e("Style & Layout Settings", 'thrive'); ?></a>
        <a id="thrive-link-blog-options" rel="blog-options"><?php _e("Blog Settings", 'thrive'); ?></a>
        <a id="thrive-link-analytics-options"
           rel="analytics-options"><?php _e("Analytics / Scripts", 'thrive'); ?></a>
        <a id="thrive-link-performance-options" rel="performance-options"><?php _e("Performance", 'thrive'); ?></a>
        <a id="thrive-link-client-options" rel="client-options"><?php _e("Client logos", 'thrive'); ?></a>
        <a id="thrive-link-comments-options" rel="comments-options"><?php _e("Comments", 'thrive'); ?></a>
        <a id="thrive-link-social-options" rel="social-options"><?php _e("Social Media", 'thrive'); ?></a>
        <a id="thrive-link-404" rel="404-options"><?php _e("404 Page", 'thrive'); ?></a>
        <a id="thrive-link-related" rel="related-options"><?php _e("Related Posts", 'thrive'); ?></a>
        <a id="thrive-link-apprentice-options" rel="apprentice-options"><?php _e("Apprentice", 'thrive'); ?></a>

        <div class="clear"></div>
    </div>
</div>
<div class="option_window left">
    <form action="options.php" id="thrive-options-form" method="post">
        <div class="options-container">
            <div id="thrive-admin-container">

                <div class="thrive-admin-subcontainer" id="thrive-admin-subcontainer-general-options">

                    <?php
                    settings_fields('thrive_options');
                    do_settings_sections('theme_options');
                    do_settings_sections('theme_global_options');
                    ?>
                </div>

                <div class="thrive-admin-subcontainer" id="thrive-admin-subcontainer-style-options">
                    <?php
                    settings_fields('thrive_options');
                    do_settings_sections('theme_style_options');
                    ?>

                    <p>
                        <?php _e("To further customise the style settings on your theme (for example, to select font types and colours) you can use the ", 'thrive'); ?>
                        <a href="<?php echo get_admin_url(); ?>customize.php"><?php _e("Theme Customiser", 'thrive'); ?></a>
                    </p>

                </div>

                <div class="thrive-admin-subcontainer" id="thrive-admin-subcontainer-blog-options">
                    <?php
                    settings_fields('thrive_options');
                    do_settings_sections('theme_featured_image_options');
                    do_settings_sections('theme_meta_info_options');
                    do_settings_sections('theme_bottom_posts_options');
                    do_settings_sections('theme_other_blog_options');
                    ?>
                </div>

                <div class="thrive-admin-subcontainer" id="thrive-admin-subcontainer-analytics-options">
                    <?php
                    settings_fields('thrive_options');
                    do_settings_sections('theme_analytics_options');
                    ?>
                </div>

                <div class="thrive-admin-subcontainer" id="thrive-admin-subcontainer-performance-options">
                    <?php
                    settings_fields('thrive_options');
                    do_settings_sections('theme_performance_options');
                    require "partial-image-resize.php";
                    ?>
                </div>

                <div class="thrive-admin-subcontainer" id="thrive-admin-subcontainer-comments-options">
                    <?php
                    settings_fields('thrive_options');
                    do_settings_sections('theme_comments_blog_options');
                    do_settings_sections('theme_comments_options');
                    ?>
                </div>

                <div class="thrive-admin-subcontainer" id="thrive-admin-subcontainer-social-options">
                    <?php
                    settings_fields('thrive_options');
                    do_settings_sections('theme_social_button_options');
                    do_settings_sections('theme_social_display_options');
                    do_settings_sections('theme_social_advanced_options');
                    do_settings_sections('theme_social_options');
                    do_settings_sections('theme_social_sharing_data_options');
                    ?>
                </div>

                <div class="thrive-admin-subcontainer" id="thrive-admin-subcontainer-client-options">
                    <?php
                    settings_fields('thrive_options');
                    do_settings_sections('theme_client_options');
                    ?>
                </div>

                <div class="thrive-admin-subcontainer" id="thrive-admin-subcontainer-404-options">
                    <?php
                    settings_fields('thrive_options');
                    do_settings_sections('theme_404tpl_options');
                    ?>
                </div>

                <?php $related_posts_enabled = thrive_get_theme_options('related_posts_enabled'); ?>
                <div class="thrive-admin-subcontainer" id="thrive-admin-subcontainer-related-options">
                    <?php if ($related_posts_enabled != 1): ?>
                        <h3><?php _e('Activate Advanced Related Posts', 'thrive'); ?></h3>
                        <p><?php _e("By default, a list of related posts is generated by listing the latest posts in the same category as the post the visitor is currently viewing. When you activate the advanced feature, our algorithm will search through and map all the relations between your posts. This relations map is updated every time you save a new post. The advantage of the advanced feature is that you get better related posts results. The drawback is that every time you save a new post, there will be a short delay, depending on how many total posts your site has. There is no speed impact on the front-end of your site.", 'thrive'); ?></p>
                        <input type="button" id="theme_options_enable_related_posts"
                               value="<?php _e("Enable this Feature", 'thrive'); ?>"
                               class="thrive_options pure-button upload"/>
                    <?php else: ?>
                        <?php
                        settings_fields('thrive_options');
                        do_settings_sections('theme_related_posts_options');
                        ?>
                        <table class="form-table">
                            <tr>
                                <th scope="row">
                                    <input type="button" id="thrive_btn_generate_related_posts"
                                           value="<?php _e('Map relations for all existing posts', 'thrive'); ?>"
                                           class="button"/>
                                </th>
                                <td><span id="thrive_label_loading_generate_related_posts"
                                          style="display:none;"><?php _e("Please wait, this may take a while...", 'thrive'); ?></span>
                                </td>
                            </tr>
                        </table>
                        <input type="button" id="theme_options_disable_related_posts"
                               value="<?php _e("Disable this feature", 'thrive'); ?>"
                               class="thrive_options pure-button clear-field remove"/>
                    <?php endif; ?>
                    <?php do_settings_sections('theme_related_box_options'); ?>
                    <input type="hidden" value="<?php echo $related_posts_enabled; ?>"
                           name="thrive_theme_options[related_posts_enabled]"
                           id="theme_options_hidden_related_posts_enabled"/>
                </div>

                <?php $apprentice_enabled = thrive_get_theme_options('appr_enable_feature'); ?>
                <div class="thrive-admin-subcontainer" id="thrive-admin-subcontainer-apprentice-options">
                    <?php if ($apprentice_enabled != 1): ?>
                        <h3><?php _e("Activate feature", 'thrive'); ?></h3>
                        <p><?php _e("Thrive Apprentice will add a new page type and new options that are ideal for delivering online lessons and styling content pages for your members.", 'thrive'); ?>
                            <a target="_blank" href="http://thrivethemes.com/thrive-knowledge-base/?section_id=511&parent=603"><?php _e("Learn more about this feature here.", 'thrive'); ?></a>
                        </p>
                        <input type="button" id="theme_options_enable_apprentice"
                               value="<?php _e("Enable this feature", 'thrive'); ?>"
                               class="thrive_options pure-button upload"/>
                    <?php else: ?>
                        <p><?php _e("Thrive Apprentice will add a new page type and new options that are ideal for delivering online lessons and styling content pages for your members.", 'thrive'); ?>
                            <a target="_blank" href="http://thrivethemes.com/thrive-knowledge-base/?section_id=511&parent=603"><?php _e("Learn more about this feature here.", 'thrive'); ?></a>
                        </p>
                        <input type="button" id="theme_options_disable_apprentice"
                               value="<?php _e("Disable this feature", 'thrive'); ?>"
                               class="thrive_options pure-button clear-field remove"/> <br/><br/>
                        <?php
                        settings_fields('thrive_options');
                        do_settings_sections('theme_appr_layout_options');
                        do_settings_sections('theme_appr_feature_options');
                        do_settings_sections('theme_appr_blog_options');
                        do_settings_sections('theme_appr_url_options');
                        ?>
                    <?php endif; ?>
                    <input type="hidden" value="<?php echo $apprentice_enabled; ?>"
                           name="thrive_theme_options[appr_enable_feature]"
                           id="theme_options_hidden_appr_enable_feature"/>
                </div>
            </div>
        </div>
        <div class="options-container with-button">
            <p class="submit"><input type="button" name="submit_button" id="tt-submit-button"
                                     class="button button-primary" value="<?php _e("Save All Changes", 'thrive'); ?>">
            </p>
        </div>
    </form>
</div>
<div class="clear"></div>
<script src="https://apis.google.com/js/platform.js" async defer></script>
<script>!function (d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0], p = /^http:/.test(d.location) ? 'http' : 'https';
        if (!d.getElementById(id)) {
            js = d.createElement(s);
            js.id = id;
            js.src = p + '://platform.twitter.com/widgets.js';
            fjs.parentNode.insertBefore(js, fjs);
        }
    }(document, 'script', 'twitter-wjs');</script>