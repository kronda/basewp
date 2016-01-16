<?php

require_once ABSPATH . 'wp-admin/includes/plugin.php';
$is_thrive_leads_active = is_plugin_active('thrive-leads/thrive-leads.php');

$menu_path = dirname(__FILE__) . '/inc/menu/';
$side_menu_path = dirname(__FILE__) . '/inc/side-menu/';
$is_thrive_theme = !empty($_POST['is_thrive_theme']);
$template_uri = rtrim(get_template_directory_uri(), '/');
$editor_dir = $_POST['editor_dir'];
$landing_page_dir = dirname(dirname($editor_dir)) . '/landing-page/templates';
$_cPanelPosition = ($_POST['display_options']['position'] == 'left') ? "tve_cpanelFlip" : "";
$_dColor = ($_POST['display_options']['color'] == 'dark') ? "tve_is_dark" : "";

$thrive_optins = $thrive_optin_colors = $posts_categories = $custom_menus = array();
$current_theme_name = $_POST['current_theme_name'];
$banned_themes_names[] = 'Performag';

if ($is_thrive_theme) {
    $thrive_optins = !empty($_POST['thrive_optins']) ? $_POST['thrive_optins'] : array();
    $thrive_optin_colors = !empty($_POST['thrive_optin_colors']) ? $_POST['thrive_optin_colors'] : array();
    $posts_categories = !empty($_POST['posts_categories']) ? $_POST['posts_categories'] : array();
    $custom_menus = !empty($_POST['custom_menus']) ? $_POST['custom_menus'] : array();
}
if ($is_thrive_leads_active) {
    $thrive_leads_shortcodes = tve_leads_get_shortcodes();
}
$landing_page_template = empty($_POST['landing_page']) ? false : $_POST['landing_page'];
$fonts = tve_get_all_custom_fonts(true);
$extra_custom_fonts = apply_filters('tcb_extra_custom_fonts', array(), (int)$_POST['post_id']);
$post_type = $_POST['post_type'];
$menus = empty($_POST['menus']) ? array() : $_POST['menus'];

$last_revision_id = tve_get_last_revision_id($_POST['post_id']);

$page_section_patterns = array();
if (function_exists('_thrive_get_patterns_from_directory')) {
    $page_section_patterns = _thrive_get_patterns_from_directory();
    array_shift($page_section_patterns);
}

$tve_display_save_notification = get_option('tve_display_save_notification', 1);

$tve_disqus_shortname = get_option('tve_comments_disqus_shortname');
$tve_facebook_admins = get_option('tve_comments_facebook_admins');

$web_safe_fonts = tve_font_manager_get_safe_fonts();

?>
<div class="tve_wrapper <?php echo $_cPanelPosition . ' ' . $_dColor ?>" id="tve_cpanel">
    <div class="tve_editor">
        <div class="tve_cpanel_sec tve_control_btns">
            <div class="tve_btn_success tve_left" title="Save">
                <div class="tve_update" title="<?php echo __("Save Changes", "thrive-cb") ?>" id="tve_update_content">
                    <span class="tve_expanded"><?php echo __("Save Changes", 'thrive-cb') ?></span>
                    <span class="tve_icm tve-ic-disk tve_collapsed"></span>
                </div>
            </div>
            <div class="tve_btn_default tve_expanded tve_left" title="<?php echo __("Publish", "thrive-cb") ?>">
                <a class="tve_preview" title="Publish" id="tve_preview_content" target="_blank"
                   href="<?php echo $_POST['preview_url']; ?>">
                    <span class=""><?php echo __("Preview", "thrive-cb") ?></span>
                </a>
            </div>
            <div class="tve_clear"></div>
        </div>

        <?php if ($post_type != 'tcb_lightbox' && $post_type == 'page') : ?>
            <?php include $side_menu_path . 'landing_pages.php' ?>
        <?php endif ?>

        <?php include $side_menu_path . 'page_actions.php' ?>

        <div class="tve_cpanel_options">

            <?php include $side_menu_path . 'simple_content_elements.php' ?>

            <?php include $side_menu_path . 'multi_style_elements.php' ?>

            <?php include $side_menu_path . 'advanced_elements.php' ?>

            <?php if ($is_thrive_theme || $landing_page_template): ?>
                <?php include $side_menu_path . 'thrive_theme_elements.php' ?>
            <?php endif; ?>

        </div>
        <?php include $side_menu_path . 'editor_settings.php' ?>
    </div>
</div>

<div class="tve_cpanel_onpage <?php echo $_dColor ?>" style="display: none" id="tve_cpanel_onpage">
    <div class="tve_secondLayer">
        <div id="typefocus_menu">
            <?php include $menu_path . 'typefocus.php'; ?>
        </div>
        <div id="text_menu">
            <?php include $menu_path . 'text.php' ?>
        </div>

        <div id="text_inline_only_menu">
            <?php include $menu_path . 'text_inline_only.php' ?>
        </div>

        <div id="img_menu">
            <?php include $menu_path . 'img.php' ?>
        </div>

        <div id="button_menu">
            <?php include $menu_path . 'button.php' ?>
        </div>

        <div id="contentbox_menu">
            <?php include $menu_path . 'contentbox.php' ?>
        </div>

        <div id="guarantee_menu">
            <?php include $menu_path . 'guarantee.php' ?>
        </div>

        <div id="calltoaction_menu">
            <?php include $menu_path . 'calltoaction.php' ?>
        </div>

        <div id="testimonial_menu">
            <?php include $menu_path . 'testimonial.php' ?>
        </div>

        <div id="bullets_menu">
            <?php include $menu_path . 'bullets.php' ?>
        </div>

        <div id="tabs_menu">
            <?php include $menu_path . 'tabs.php' ?>
        </div>

        <div id="toggle_menu">
            <?php include $menu_path . 'toggle.php' ?>
        </div>

        <div id="custom_html_menu">
            <?php include $menu_path . 'custom_html.php' ?>
        </div>

        <div id="feature_grid_menu">
            <?php include $menu_path . 'feature_grid.php' ?>
        </div>

        <div id="cc_icons_menu">
            <?php include $menu_path . 'cc_icons.php' ?>
        </div>

        <div id="pricing_table_menu">
            <?php include $menu_path . 'pricing_table.php' ?>
        </div>

        <div id="content_container_menu">
            <?php include $menu_path . 'content_container.php' ?>
        </div>

        <?php if ($is_thrive_theme || $landing_page_template): ?>
            <div id="page_section_menu">
                <?php include $menu_path . 'page_section.php' ?>
            </div>
        <?php endif; ?>

        <div id="table_menu">
            <?php include $menu_path . 'table.php' ?>
        </div>

        <div id="table_cell_menu">
            <?php include $menu_path . 'table_cell.php' ?>
        </div>

        <div id="thrive_optin_menu">
            <?php include $menu_path . 'thrive_optin.php' ?>
        </div>

        <div id="thrive_leads_shortcode_menu">
            <?php include $menu_path . 'thrive_leads_shortcode.php' ?>
        </div>

        <div id="content_reveal_menu">
            <?php include $menu_path . 'content_reveal.php' ?>
        </div>

        <div id="tw_qs_menu">
            <?php include $menu_path . 'tw_qs.php' ?>
        </div>

        <div id="lead_generation_menu">
            <?php include $menu_path . 'lead_generation.php' ?>
        </div>

        <div id="lead_generation_input_menu">
            <?php include $menu_path . 'lead_generation_input.php' ?>
        </div>

        <div id="lead_generation_submit_menu">
            <?php include $menu_path . 'lead_generation_submit.php' ?>
        </div>

        <div id="lead_generation_image_submit_menu">
            <?php include $menu_path . 'lead_generation_image_submit.php' ?>
        </div>

        <div id="lead_generation_checkbox_menu">
            <?php include $menu_path . 'lead_generation_checkbox.php' ?>
        </div>

        <div id="lead_generation_dropdown_menu">
            <?php include $menu_path . 'lead_generation_dropdown.php' ?>
        </div>

        <div id="lead_generation_radio_menu">
            <?php include $menu_path . 'lead_generation_radio.php' ?>
        </div>

        <div id="lead_generation_textarea_menu">
            <?php include $menu_path . 'lead_generation_textarea.php' ?>
        </div>

        <div id="post_grid_menu">
            <?php include $menu_path . 'post_grid.php' ?>
        </div>

        <div id="contents_table_menu">
            <?php include $menu_path . 'contents_table.php' ?>
        </div>

        <div id="responsive_video_menu">
            <?php include $menu_path . 'responsive_video.php' ?>
        </div>

        <div id="countdown_timer_evergreen_menu">
            <?php include $menu_path . 'countdown_timer_evergreen.php' ?>
        </div>

        <div id="countdown_timer_menu">
            <?php include $menu_path . 'countdown_timer.php' ?>
        </div>

        <div id="thrive_posts_list_menu">
            <?php include $menu_path . 'thrive_posts_list.php' ?>
        </div>

        <div id="thrive_custom_phone_menu">
            <?php include $menu_path . 'thrive_custom_phone.php' ?>
        </div>

        <div id="thrive_custommenu_menu">
            <?php include $menu_path . 'thrive_custommenu.php' ?>
        </div>

        <div id="rating_menu">
            <?php include $menu_path . 'rating.php' ?>
        </div>

        <div id="shortcode_menu">
            <?php include $menu_path . 'shortcode.php' ?>
        </div>

        <div id="lists_menu">
            <?php include $menu_path . 'lists.php' ?>
        </div>

        <div id="default_element_menu">
            <?php /* this will be shown as a default menu for everything that does not have a menu, and should contain general options */ ?>
            <?php include $menu_path . 'default_element.php' ?>
        </div>

        <?php if ($post_type == 'tcb_lightbox') : ?>
            <div id="lightbox_menu">
                <?php include $menu_path . 'lightbox.php' ?>
            </div>
        <?php endif ?>

        <?php if ($landing_page_template) : ?>
            <div id="landing_page_menu">
                <?php include $menu_path . 'landing_page.php' ?>
            </div>
            <div id="landing_page_content_menu">
                <?php include $menu_path . 'landing_page_content.php' ?>
            </div>
            <div id="landing_fonts_menu">
                <?php include $menu_path . 'landing_page_fonts.php' ?>
            </div>
        <?php endif ?>

        <div id="icon_menu">
            <?php include $menu_path . 'icon.php' ?>
        </div>

        <div id="cb_text_menu">
            <?php $is_cb_text = true;
            include $menu_path . 'icon.php' ?>
        </div>

        <div id="widget_menu_menu">
            <?php include $menu_path . 'widget_menu.php' ?>
        </div>

        <div id="social_default_menu">
            <?php include $menu_path . 'social_default.php' ?>
        </div>

        <div id="social_custom_menu">
            <?php include $menu_path . 'social_custom.php' ?>
        </div>

        <div id="progress_bar_menu">
            <?php include $menu_path . 'progress_bar.php' ?>
        </div>

        <div id="fill_counter_menu">
            <?php include $menu_path . 'fill_counter.php' ?>
        </div>

        <div id="number_counter_menu">
            <?php include $menu_path . 'number_counter.php' ?>
        </div>

        <div id="facebook_comments_menu">
            <?php include $menu_path . 'facebook_comments.php' ?>
        </div>

        <div id="disqus_comments_menu">
            <?php include $menu_path . 'disqus_comments.php' ?>
        </div>
        <?php echo do_action('tcb_custom_menus_html', $menu_path); ?>

        <div class="tve_clear"></div>
    </div>
    <a href="javascript:void(0)" id="tve_submenu_close" title="<?php echo __("Close", "thrive-cb") ?>"></a>

    <div class="tve_menu" data-multiple-hide>
        <a href="javascript:void(0)" id="tve_submenu_save"
           class="tve_click tve_icm tve-ic-toggle-down tve_lb_small tve_btn tve_no_hide"
           data-ctrl="controls.lb_open"
           data-load="1"
           data-lb="lb_save_user_template"
           title="<?php echo __("Save this element as a Content Template", "thrive-cb") ?>">
            <input type="hidden" name="element" value="1"/>
        </a>
    </div>
    <div id="tve_iris_holder" style="display: none">
        <span class="tve_cp_text tve_cp_title" id="tve_cp_title"><?php echo __("Text Color", "thrive-cb") ?></span>

        <div class="tve_cp_row"></div>

        <div class="tve_cp_row tve_clearfix">
            <span class="tve_cp_text"><?php echo __("Color", "thrive-cb") ?></span>
            <input type="text" size="10" id="tve_cp_color" class="tve_right" style="width: 120px"/>
        </div>
        <div class="tve_cp_row tve_clearfix wp-picker-opacity" id="tve_opacity_ctrl">
            <span class="tve_cp_text tve_left" style=""><?php echo __("Opacity", "thrive-cb") ?></span>
            <input type="text" size="2" id="tve_cp_opacity" class="tve_right" style="width: 36px"/>

            <div class="ui-slider-bg tve_right" style="width: 150px;">
                <div class="wp-opacity-slider" id="tve_cp_opacity_slider"></div>
            </div>
        </div>
        <div class="tve_cp_row tve_cp_actions">
            <div id="tve_cp_save_fav" class="tve_btn_default tve_left">
                <div class="tve_preview"><?php echo __("Save as Favourite Color", "thrive-cb") ?></div>
            </div>
            <div class="tve_btn_success tve_right" id="tve_cp_ok">
                <div class="tve_update"><?php echo __("OK", "thrive-cb") ?></div>
            </div>
        </div>
    </div>
</div>

<!--lightbox stuff-->
<div class="tve_lightbox_overlay" id="tve_lightbox_overlay"></div>
<div class="tve_lightbox_frame" id="tve_lightbox_frame">
    <a class="tve-lightbox-close" href="javascript:void(0)" title="<?php echo __("Close", "thrive-cb") ?>"><span
            class="tve_lightbox_close tve_click"
            data-ctrl="controls.lb_close"></span></a>

    <div class="tve_lightbox_content" id="tve_lightbox_content"></div>
    <div class="tve-sp"></div>
    <div class="tve_lightbox_buttons tve_clearfix" id="tve_lightbox_buttons">
        <input type="button"
               class="tve_save_lightbox tve_mousedown tve_editor_button tve_editor_button_success tve_right"
               value="<?php echo __("Save", "thrive-cb") ?>" data-ctrl="controls.lb_save">
    </div>
</div>

<?php /* static lightboxes */ ?>
<div class="tve-static-lb" style="display: none">
    <div style="display: none" id="lb_static_lp_export">
        <?php include dirname(__FILE__) . '/lb_static_lp_export.php' ?>
    </div>
    <div style="display: none;" id="lb_text_link">
        <?php include dirname(__FILE__) . '/lb_text_link.php' ?>
    </div>
    <div style="display: none;" id="lb_custom_html">
        <?php include dirname(__FILE__) . '/lb_custom_html.php' ?>
    </div>
    <div style="display: none;" id="lb_custom_css">
        <?php include dirname(__FILE__) . '/lb_custom_css.php' ?>
    </div>
    <div style="display: none;" id="lb_table">
        <?php include dirname(__FILE__) . '/lb_table.php' ?>
    </div>
    <div style="display: none;" id="lb_google_map">
        <?php include dirname(__FILE__) . '/lb_google_map.php' ?>
    </div>
</div>

<div style="display: none" id="tve_table_merge_actions" class="tve_table_merge_cells_actions">
    <?php include $menu_path . 'table_cell_manager.php' ?>
</div>

<div style="display: none" id="tve_social_sort" class="tve_social_sort">
    <?php include $menu_path . 'social_sort.php'; ?>
</div>

<div style="display: none" id="tve_toggle_reorder_menu" class="tve_focused_menu tve_event_root">
    <?php include $menu_path . 'toggle_reorder.php'; ?>
</div>

<div style="display: none" id="tve_static_elements">
    <div data-elem="sc_borderless_html">
        <div class="fwi thrv_wrapper tve_custom_html_placeholder code_placeholder">
            <a class="tve_click tve_green_button clearfix" id="lb_custom_html" data-ctrl="controls.lb_open">
                <i class="tve_icm tve-ic-code"></i>
                <span>Insert Custom HTML</span>
            </a>
        </div>
    </div>
    <div data-elem="sc_borderless_image">
        <div class="fwi thrv_wrapper image_placeholder code_placeholder">
            <a class="upload_image tve_green_button clearfix" href="#" target="_self">
                <i class="tve_icm tve-ic-upload"></i>
                <span>Add Media</span>
            </a>
        </div>
    </div>
    <div data-elem="paragraph">
        <p data-default="<?php echo __("Enter your text here...", "thrive-cb") ?>"><?php echo __("Enter your text here...", "thrive-cb") ?></p>
    </div>
    <div data-elem="thrv_image">
        <div class="image_placeholder thrv_wrapper">
            <a class="upload_image tve_green_button clearfix" href="#" target="_self">
                <i class="tve_icm tve-ic-upload"></i>
                <span><?php echo __("Add Media", "thrive-cb") ?></span>
            </a>
        </div>
    </div>
    <div data-elem="sc_icon">
        <div class="image_placeholder thrv_wrapper">
            <a class="tve_click tve_green_button clearfix" href="javascript:void(0)"
               data-ctrl="controls.lb_open" id="lb_icon"
               data-wpapi="lb_icon"
               data-btn-text="Insert Icon" data-load="1">
                <i class="tve_icm tve-ic-upload"></i>
                <span><?php echo __("Add Icon", "thrive-cb") ?></span>
                <input type="hidden" name="cb_icon" value=""/>
            </a>
        </div>
    </div>
    <div data-elem="sc_cc_icons">
        <div class="thrv_wrapper thrv_cc_icons">
            <div class="thrv_cc_wrapper">
                <span class="tve_cc_amex tve_cc_logo tve_no_edit"></span>
                <span class="tve_cc_discover tve_cc_logo tve_no_edit"></span>
                <span class="tve_cc_mc tve_cc_logo tve_no_edit"></span>
                <span class="tve_cc_visa tve_cc_logo tve_no_edit"></span>
                <span class="tve_cc_paypal tve_cc_logo tve_no_edit"></span>
            </div>
        </div>
    </div>
    <div data-elem="sc_content_container">
        <div class="thrv_wrapper thrv_content_container_shortcode">
            <div class="tve_clear"></div>
            <div class="tve_center tve_content_inner" style="min-width: 50px; min-height: 2em;">
                <p><?php echo __("Your content here...", "thrive-cb") ?></p>
            </div>
            <div class="tve_clear"></div>
        </div>
    </div>
    <div data-elem="sc_content_reveal">
        <div class="thrv_wrapper thrv_content_reveal tve_clearfix" data-after="5" data-redirect-url="">
            <p><?php echo __("Your content here...", "thrive-cb") ?></p>
        </div>
    </div>
    <div data-elem="sc_star_rating">
        <div class="thrv_wrapper thrv_star_rating">
            <span class="tve_rating_stars tve_style_star" data-value="3" data-max="5" title="3 / 5" style="width:120px"><span
                    style="width:72px"></span></span>
        </div>
    </div>
    <div data-elem="standard_halfs">
        <div class="thrv_wrapper thrv_columns">
            <div class="tve_colm tve_twc"><p><?php echo sprintf(__("Column %s", "thrive-cb"), "1") ?></p></div>
            <div class="tve_colm tve_twc tve_lst"><p><?php echo sprintf(__("Column %s", "thrive-cb"), "2") ?></p></div>
        </div>
    </div>
    <div data-elem="standard_thirds">
        <div class="thrv_wrapper thrv_columns tve_clearfix">
            <div class="tve_colm tve_oth"><p><?php echo sprintf(__("Column %s", "thrive-cb"), "1") ?></p></div>
            <div class="tve_colm tve_oth"><p><?php echo sprintf(__("Column %s", "thrive-cb"), "2") ?></p></div>
            <div class="tve_colm tve_thc tve_lst"><p><?php echo sprintf(__("Column %s", "thrive-cb"), "3") ?></p></div>
        </div>
    </div>
    <div data-elem="standard_fourths">
        <div class="thrv_wrapper thrv_columns tve_clearfix">
            <div class="tve_colm tve_foc"><p><?php echo sprintf(__("Column %s", "thrive-cb"), "1") ?></p></div>
            <div class="tve_colm tve_foc"><p><?php echo sprintf(__("Column %s", "thrive-cb"), "2") ?></p></div>
            <div class="tve_colm tve_foc"><p><?php echo sprintf(__("Column %s", "thrive-cb"), "3") ?></p></div>
            <div class="tve_colm tve_foc tve_lst"><p><?php echo sprintf(__("Column %s", "thrive-cb"), "4") ?></p></div>
        </div>
    </div>
    <div data-elem="standard_fifths">
        <div class="thrv_wrapper thrv_columns tve_clearfix">
            <div class="tve_colm tve_fic"><p><?php echo sprintf(__("Column %s", "thrive-cb"), "1") ?></p></div>
            <div class="tve_colm tve_fic"><p><?php echo sprintf(__("Column %s", "thrive-cb"), "2") ?></p></div>
            <div class="tve_colm tve_fic"><p><?php echo sprintf(__("Column %s", "thrive-cb"), "3") ?></p></div>
            <div class="tve_colm tve_fic"><p><?php echo sprintf(__("Column %s", "thrive-cb"), "4") ?></p></div>
            <div class="tve_colm tve_fic tve_lst"><p><?php echo sprintf(__("Column %s", "thrive-cb"), "5") ?></p></div>
        </div>
    </div>
    <div data-elem="standard_thirds_one_two">
        <div class="thrv_wrapper thrv_columns tve_clearfix">
            <div class="tve_colm tve_oth"><p><?php echo sprintf(__("Column %s", "thrive-cb"), "1") ?></p></div>
            <div class="tve_colm tve_tth tve_lst"><p><?php echo sprintf(__("Column %s", "thrive-cb"), "2") ?></p></div>
        </div>
    </div>
    <div data-elem="standard_thirds_two_one">
        <div class="thrv_wrapper thrv_columns tve_clearfix">
            <div class="tve_colm tve_tth"><p><?php echo sprintf(__("Column %s", "thrive-cb"), "1") ?></p></div>
            <div class="tve_colm tve_oth tve_lst"><p><?php echo sprintf(__("Column %s", "thrive-cb"), "2") ?></p></div>
        </div>
    </div>
    <div data-elem="standard_fourths_one_three">
        <div class="thrv_wrapper thrv_columns tve_clearfix">
            <div class="tve_colm tve_foc tve_df tve_ofo ">
                <p><?php echo sprintf(__("Column %s", "thrive-cb"), "1") ?></p></div>
            <div class="tve_colm tve_tfo tve_df tve_lst"><p><?php echo sprintf(__("Column %s", "thrive-cb"), "2") ?></p>
            </div>
        </div>
    </div>
    <div data-elem="standard_fourths_three_one">
        <div class="thrv_wrapper thrv_columns tve_clearfix">
            <div class="tve_colm tve_tfo tve_df "><p><?php echo sprintf(__("Column %s", "thrive-cb"), "1") ?></p></div>
            <div class="tve_colm  tve_foc tve_ofo tve_df tve_lst">
                <p><?php echo sprintf(__("Column %s", "thrive-cb"), "2") ?></p></div>
        </div>
    </div>
    <div data-elem="standard_two_fourths_half">
        <div class="thrv_wrapper thrv_columns">
            <div class="tve_colm tve_foc tve_df tve_fft"><p><?php echo sprintf(__("Column %s", "thrive-cb"), "1") ?></p>
            </div>
            <div class="tve_colm tve_foc tve_df tve_fft"><p><?php echo sprintf(__("Column %s", "thrive-cb"), "2") ?></p>
            </div>
            <div class="tve_colm tve_twc tve_lst"><p><?php echo sprintf(__("Column %s", "thrive-cb"), "3") ?></p></div>
        </div>
    </div>
    <div data-elem="standard_fourth_half_fourth">
        <div class="thrv_wrapper thrv_columns">
            <div class="tve_colm tve_foc tve_df tve_fft"><p><?php echo sprintf(__("Column %s", "thrive-cb"), "1") ?></p>
            </div>
            <div class="tve_colm tve_twc"><p><?php echo sprintf(__("Column %s", "thrive-cb"), "2") ?></p></div>
            <div class="tve_colm tve_foc tve_df tve_fft tve_lst">
                <p><?php echo sprintf(__("Column %s", "thrive-cb"), "3") ?></p></div>
        </div>
    </div>
    <div data-elem="standard_half_fourth_fourth">
        <div class="thrv_wrapper thrv_columns">
            <div class="tve_colm tve_twc"><p><?php echo sprintf(__("Column %s", "thrive-cb"), "1") ?></p></div>
            <div class="tve_colm tve_foc tve_df tve_fft"><p><?php echo sprintf(__("Column %s", "thrive-cb"), "2") ?></p>
            </div>
            <div class="tve_colm tve_foc tve_df tve_fft tve_lst">
                <p><?php echo sprintf(__("Column %s", "thrive-cb"), "3") ?></p></div>
        </div>
    </div>
    <div data-elem="sc_tw_quote_share1">
        <div class="thrv_wrapper thrv_tw_qs tve_clearfix" data-tve-style="1" data-url="https://twitter.com/intent/tweet"
             data-via="">
            <div class="thrv_tw_qs_container">
                <div class="thrv_tw_quote">
                    <p><?php echo __("Insert your tweetable quote/phrase here", "thrive-cb") ?></p>
                </div>
                <div class="thrv_tw_qs_button tve_p_right">
            <span>
                <i></i>
                <span class="thrv_tw_qs_button_text"><?php echo __("Click to Tweet", "thrive-cb") ?></span>
            </span>
                </div>
            </div>
        </div>
    </div>
    <div data-elem="sc_divider1">
        <div class="thrv_wrapper">
            <hr class="tve_sep tve_sep1"/>
        </div>
    </div>
    <div data-elem="sc_divider2">
        <div class="thrv_wrapper">
            <hr class="tve_sep tve_sep2"/>
        </div>
    </div>
    <div data-elem="sc_divider3">
        <div class="thrv_wrapper">
            <hr class="tve_sep tve_sep3"/>
        </div>
    </div>
    <div data-elem="sc_divider4">
        <div class="thrv_wrapper">
            <hr class="tve_sep tve_sep4"/>
        </div>
    </div>
    <div data-elem="sc_buttons1_classy">
        <div class="thrv_wrapper thrv_button_shortcode" data-tve-style="1">
            <div class="tve_btn tve_btn5 tve_nb tve_red tve_bigBtn">
                <a href="" class="tve_btnLink">
            <span class="tve_left tve_btn_im">
                <i></i>
                <span class="tve_btn_divider"></span>
            </span>
                    <span class="tve_btn_txt"><?php echo __("ADD TO CART", "thrive-cb") ?></span>
                </a>
            </div>
        </div>
    </div>
    <div data-elem="sc_lead_generation">
        <?php
        $errors = array(
            'email' => __('Please enter a valid email address', "thrive-cb"),
            'phone' => __('Please enter a valid phone number', "thrive-cb"),
            'required' => __('Highlighted fields are required', "thrive-cb"),
        );
        ?>
        <div class="thrv_wrapper thrv_lead_generation tve_clearfix tve_red" data-tve-style="1" data-tve-version="1">
            <input type="hidden" class="tve-lg-err-msg" value="<?php echo htmlspecialchars(json_encode($errors)) ?>"/>

            <div class="thrv_lead_generation_code" style="display: none;"></div>
            <div class="thrv_lead_generation_container tve_clearfix">
                <div class="tve_lead_generated_inputs_container tve_clearfix">
                    <div class="tve_lead_fields_overlay"></div>
                    <div class=" tve_lg_input_container tve_lg_input">
                        <input type="text" data-placeholder="" value="" name="name"/>
                    </div>
                    <div class="tve_lg_input_container tve_lg_input">
                        <input type="text" data-placeholder="" value="" name="email"/>
                    </div>
                    <div class="tve_lg_input_container tve_submit_container tve_lg_submit" tve-data-style="1">
                        <button type="Submit"><?php echo __("Sign Up", "thrive-cb") ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div data-elem="sc_contentbox1">
        <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="1">
            <div class="tve_cb tve_cb1 tve_red">
                <div class="tve_hd tve_cb_cnt">
                    <h3>
                        LOREM IPSUM DOLOR SIT AMET CONSETCTEUR
                    </h3>
                    <span></span>
                </div>
                <div class="tve_cb_cnt">
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut
                        labore
                        et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi
                        ut
                        aliquip ex ea commodo consequat.
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div data-elem="sc_contentbox2">
        <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="2">
            <div class="tve_cb tve_cb2 tve_red">
                <div class="tve_cb_cnt">
                    <h3>
                        LOREM IPSUM DOLOR SIT AMET CONSETCTEUR
                    </h3>
                </div>
                <hr/>
                <div class="tve_cb_cnt">
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut
                        labore
                        et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi
                        ut
                        aliquip ex ea commodo consequat.
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div data-elem="sc_contentbox3">
        <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="3">
            <div class="tve_cb tve_cb3 tve_red">
                <div class="tve_hd tve_cb_cnt">
                    <h3>
                        LOREM IPSUM DOLOR SIT AMET CONSETCTEUR
                    </h3>
                </div>
                <hr/>
                <div class="tve_cb_cnt">
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut
                        labore
                        et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi
                        ut
                        aliquip ex ea commodo consequat.
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div data-elem="sc_contentbox4">
        <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="4">
            <div class="tve_cb tve_cb4 tve_red">
                <div class="tve_hd">
                    <span></span>
                </div>
                <div class="tve_cb_cnt">
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut
                        labore
                        et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi
                        ut
                        aliquip ex ea commodo consequat.
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div data-elem="sc_contentbox5">
        <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="5">
            <div class="tve_cb tve_cb5 tve_red">
                <div class="tve_cb_cnt">
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut
                        labore
                        et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi
                        ut
                        aliquip ex ea commodo consequat.
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div data-elem="sc_contentbox6">
        <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="6">
            <div class="tve_cb tve_cb6 tve_red">
                <div class="tve_cb_cnt">
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut
                        labore
                        et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi
                        ut
                        aliquip ex ea commodo consequat.
                    </p>
                </div>
            </div>
        </div>
    </div>
    <div data-elem="sc_contentbox_text">
        <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="symbol">
            <div class="tve_cb tve_cb_symbol tve_red">
                <div class="thrv_wrapper thrv_icon thrv_cb_text aligncenter tve_no_drag tve_no_icons"
                     style="font-size: 40px;">
                    <span class="tve_sc_text tve_sc_icon">1</span>
                </div>
                <div class="tve_cb_cnt">
                    <p><span class="bold_text">MAIN LABEL</span></p>

                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Molestiae, officia? </p>
                </div>
            </div>
        </div>
    </div>
    <div data-elem="sc_bullets1">
        <div class="thrv_wrapper thrv_bullets_shortcode">
            <ul class="tve_ul tve_ul1 tve_red">
                <li>Bullet Point 1</li>
                <li>Bullet Point 2</li>
            </ul>
        </div>
    </div>
    <div data-elem="sc_bullets2">
        <div class="thrv_wrapper thrv_bullets_shortcode">
            <ul class="tve_ul tve_ul2 tve_red">
                <li>Bullet Point 1</li>
                <li>Bullet Point 2</li>
            </ul>
        </div>
    </div>
    <div data-elem="sc_bullets3">
        <div class="thrv_wrapper thrv_bullets_shortcode">
            <ul class="tve_ul tve_ul3 tve_red">
                <li>Bullet Point 1</li>
                <li>Bullet Point 2</li>
            </ul>
        </div>
    </div>
    <div data-elem="sc_bullets4">
        <div class="thrv_wrapper thrv_bullets_shortcode">
            <ul class="tve_ul tve_ul4 tve_red">
                <li>Bullet Point 1</li>
                <li>Bullet Point 2</li>
            </ul>
        </div>
    </div>
    <div data-elem="sc_bullets5">
        <div class="thrv_wrapper thrv_bullets_shortcode">
            <ul class="tve_ul tve_ul5 tve_red">
                <li>Bullet Point 1</li>
                <li>Bullet Point 2</li>
            </ul>
        </div>
    </div>
    <div data-elem="sc_bullets6">
        <div class="thrv_wrapper thrv_bullets_shortcode">
            <ul class="tve_ul tve_ul6 tve_red">
                <li>Bullet Point 1</li>
                <li>Bullet Point 2</li>
            </ul>
        </div>
    </div>
    <div data-elem="sc_testimonial1">
        <div class="thrv_wrapper thrv_testimonial_shortcode" data-tve-style="1">
            <div class="tve_ts tve_ts1 tve_red">
                <div class="tve_ts_t">
                    <span class="tve_ts_ql"></span>

                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut
                        labore et
                        dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut
                        aliquip
                        ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum
                        dolore eu
                        fugiat nulla pariatur.</p>
                </div>
                <div class="tve_ts_o">
                    <img src="<?php echo tve_editor_css() ?>/images/photo1.jpg" alt=""/>
                <span>
                    <b>John Doe</b>
                    <br/>
                    UI/UX Designer
                </span>
                </div>
            </div>
        </div>
    </div>
    <div data-elem="sc_testimonial2">
        <div class="thrv_wrapper thrv_testimonial_shortcode" data-tve-style="2">
            <div class="tve_ts tve_ts2 tve_red">
                <div class="tve_ts_o">
                    <div class="tve_ts_imc">
                        <img class="tve_image" src="<?php echo tve_editor_css(); ?>/images/photo1.jpg" alt=""/>
                    </div>
                <span>
                    <b>John Doe</b>
                    UI/UX Designer
                </span>
                </div>
                <div class="tve_ts_t">
                    <div class="tve_ts_cn">
                        <span class="tve_ts_ql"></span>

                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut
                            labore et
                            dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi
                            ut aliquip
                            ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
                            cillum dolore eu
                            fugiat nulla pariatur.</p>
                        <span class="tve_ts_qr"></span>
                    </div>
                </div>
                <div class="tve_clear"></div>
            </div>
        </div>
    </div>
    <div data-elem="sc_testimonial3">
        <div class="thrv_wrapper thrv_testimonial_shortcode" data-tve-style="3">
            <div class="tve_ts tve_ts3 tve_red">
                <div class="tve_ts_o">
                    <img class="tve_image" src="<?php echo tve_editor_css(); ?>/images/photo1.jpg" alt=""/>
                <span>
                    <b>John Doe</b>
                    UI/UX Designer
                </span>
                </div>
                <div class="tve_ts_cn">
                    <span class="tve_ts_ql"></span>

                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut
                        labore et
                        dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut
                        aliquip
                        ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum
                        dolore eu
                        fugiat nulla pariatur.</p>
                    <span class="tve_ts_qr"></span>
                </div>
            </div>
        </div>
    </div>
    <div data-elem="sc_testimonial4">
        <div class="thrv_wrapper thrv_testimonial_shortcode" data-tve-style="4">
            <div class="tve_ts tve_ts4 tve_red">
                <div class="tve_ts_o">
                    <div class="tve_ts_imc">
                        <img src="<?php echo tve_editor_css(); ?>/images/photo1.jpg" alt=""/>
                    </div>
                <span>
                    <b>John Doe</b>
                    UI/UX Designer
                </span>
                </div>
                <div class="tve_ts_t">
                    <span class="tve_ts_c tve_left"></span>

                    <div class="tve_ts_cn tve_left">
                        <span class="tve_ts_ql"></span>

                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut
                            labore et
                            dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi
                            ut aliquip
                            ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
                            cillum dolore eu
                            fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui
                            officia
                            deserunt mollit anim id est laborum.</p>
                    </div>
                    <div class="tve_clear"></div>
                </div>
                <div class="tve_clear"></div>
            </div>
        </div>
    </div>
    <div data-elem="sc_testimonial5">
        <div class="thrv_wrapper thrv_testimonial_shortcode" data-tve-style="6">
            <div class="tve_ts tve_ts1 tve_red tve_np">
                <div class="tve_ts_t">
                    <span class="tve_ts_ql"></span>

                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut
                        labore et
                        dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut
                        aliquip
                        ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum
                        dolore eu
                        fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia
                        deserunt mollit anim id est laborum.</p>
                </div>
                <div class="tve_ts_o">
                <span>
                    <b>John Doe</b>
                    <br/>
                    UI/UX Designer
                </span>
                </div>
            </div>
        </div>
    </div>
    <div data-elem="sc_testimonial6">
        <div class="thrv_wrapper thrv_testimonial_shortcode" data-tve-style="7">
            <div class="tve_ts tve_ts2 tve_red tve_np">
                <div class="tve_ts_o">
                <span>
                    <b>John Doe</b>
                    UI/UX Designer
                </span>
                </div>
                <div class="tve_ts_t">
                    <div class="tve_ts_cn">
                        <span class="tve_ts_ql"></span>

                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut
                            labore
                            et
                            dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi
                            ut
                            aliquip
                            ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
                            cillum
                            dolore eu
                            fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui
                            officia
                            deserunt mollit anim id est laborum.</p>
                        <span class="tve_ts_qr"></span>
                    </div>
                </div>
                <div class="tve_clear"></div>
            </div>
        </div>
    </div>
    <div data-elem="sc_testimonial7">
        <div class="thrv_wrapper thrv_testimonial_shortcode" data-tve-style="8">
            <div class="tve_ts tve_ts3 tve_red tve_np">
                <div class="tve_ts_o">
                <span>
                    <b>John Doe</b>
                    UI/UX Designer
                </span>
                </div>
                <div class="tve_ts_cn">
                    <span class="tve_ts_ql"></span>

                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut
                        labore
                        et
                        dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut
                        aliquip
                        ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum
                        dolore eu
                        fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia
                        deserunt mollit anim id est laborum.</p>
                    <span class="tve_ts_qr"></span>
                </div>
            </div>
        </div>
    </div>
    <div data-elem="sc_testimonial8">
        <div class="thrv_wrapper thrv_testimonial_shortcode" data-tve-style="9">
            <div class="tve_ts tve_ts4 tve_red tve_np">
                <div class="tve_ts_o">
                <span>
                    <b>John Doe</b>
                    UI/UX Designer
                </span>
                </div>
                <div class="tve_ts_t">
                    <div class="tve_ts_cn">
                        <span class="tve_ts_ql"></span>

                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut
                            labore
                            et
                            dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi
                            ut
                            aliquip
                            ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
                            cillum
                            dolore eu
                            fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui
                            officia
                            deserunt mollit anim id est laborum.</p>
                    </div>
                </div>
                <div class="tve_clear"></div>
            </div>
        </div>
    </div>
    <div data-elem="sc_testimonial9">
        <div class="thrv_wrapper thrv_testimonial_shortcode" data-tve-style="5">
            <div class="tve_ts tve_ts9 tve_red">
                <div class="tve_ts_t">
                    <span class="tve_ts_c tve_right"></span>

                    <div class="tve_ts_cn tve_right">
                        <span class="tve_ts_ql"></span>

                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut
                            labore et
                            dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi
                            ut aliquip
                            ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse
                            cillum dolore eu
                            fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui
                            officia
                            deserunt mollit anim id est laborum.</p>
                    </div>
                    <div class="tve_clear"></div>
                </div>
                <div class="tve_ts_o">
                    <div class="tve_ts_imc">
                        <img src="<?php echo tve_editor_css(); ?>/images/photo1.jpg" alt=""/>
                    </div>
                <span>
                    <b>John Doe</b>
                    UI/UX Designer
                </span>
                </div>
                <div class="tve_clear"></div>
            </div>
        </div>
    </div>
    <div data-elem="sc_calltoaction1">
        <div class="thrv_wrapper thrv_calltoaction_shortcode" data-tve-style="1">
            <div class="tve_ca tve_ca1 tve_red">
                <div class="tve_line">
                    <h1>LOREM IPSUM DOLOR SIT AMET ELIT</h1>
                </div>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore
                    et
                    dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut
                    aliquip
                    ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum
                    dolore eu
                    fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia
                    deserunt mollit anim id est laborum.</p>

                <div class="tve_btn_cnt">
                    <div class="tve_arrow_left"></div>
                    <div class="tve_btn tve_btn3 tve_red tve_normalBtn">
                        <a class="tve_btnLink" href="">
                            <span>Buy it Now!</span>
                        </a>
                    </div>
                    <div class="tve_arrow_right"></div>
                </div>
            </div>
        </div>
    </div>
    <div data-elem="sc_calltoaction2">
        <div class="thrv_wrapper thrv_calltoaction_shortcode" data-tve-style="2">
            <div class="tve_ca tve_ca2 tve_red">
                <div class="tve_ca_o">
                    <h3>LOREM IPSUM DOLOR SIT AMET ELIT</h3>

                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do</p>
                </div>
                <div class="tve_ca_t">
                    <a class="tve_btnLink" href="">
                        <span>LOREM IPSUM DOLOR!</span>
                        <span class="tve_ca_sp"></span>
                    </a>
                </div>
                <div class="tve_clear"></div>
            </div>
        </div>
    </div>
    <div data-elem="sc_calltoaction3">
        <div class="thrv_wrapper thrv_calltoaction_shortcode" data-tve-style="3">
            <div class="tve_ca tve_ca3 tve_red">
                <div class="tve_ca_o">
                    <h1>LOREM IPSUM DOLOR SIT AMET ELIT</h1>

                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut
                        labore et.</p>
                </div>
                <div class="tve_ca_t">
                    <div class="tve_btn tve_btn2 tve_red tve_normalBtn">
                        <a class="tve_btnLink" href="">
                            <span>Buy it Now!</span>
                            <span class="tve_ca_sp">lorem ipsum dolor</span>
                        </a>
                    </div>
                </div>
                <div class="tve_corner"></div>
                <div class="tve_clear"></div>
            </div>
        </div>
    </div>
    <div data-elem="sc_calltoaction4">
        <div class="thrv_wrapper thrv_calltoaction_shortcode" data-tve-style="4">
            <div class="tve_ca tve_ca4 tve_red">
                <h1>LOREM IPSUM DOLOR SIT AMET ELIT</h1>

                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore
                    et
                    dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut
                    aliquip
                    ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum
                    dolore eu
                    fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia
                    deserunt mollit anim id est laborum.</p>

                <div class="tve_btn_cnt">
                    <div class="tve_btn tve_btn3 tve_red tve_normalBtn">
                        <a class="tve_btnLink" href="">
                            <span>Buy it Now!</span>
                            <span class="tve_ca_sp">lorem ipsum dolor</span>
                        </a>
                    </div>
                    <b></b>
                </div>
            </div>
        </div>
    </div>
    <div data-elem="sc_guarantee1">
        <div class="thrv_wrapper thrv_guarantee_shortcode" data-tve-style="1">
            <div class="tve_fg tve_fg1 tve_red">
                <span class="tve_badge"></span>

                <h2>100% MONEY BACK GUARANTEE</h2>
                <hr/>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore
                    et
                    dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut
                    aliquip
                    ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum
                    dolore eu
                    fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia
                    deserunt mollit anim id est laborum.</p>
            </div>
        </div>
    </div>
    <div data-elem="sc_guarantee2">
        <div class="thrv_wrapper thrv_guarantee_shortcode" data-tve-style="2">
            <div class="tve_fg tve_fg2 tve_red">
                <h2>100% MONEY BACK GUARANTEE</h2>
                <hr/>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore
                    et
                    dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut
                    aliquip
                    ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum
                    dolore eu
                    fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia
                    deserunt mollit anim id est laborum.</p>
                <span class="tve_badge"></span>
            </div>
        </div>
    </div>
    <div data-elem="sc_guarantee3">
        <div class="thrv_wrapper thrv_guarantee_shortcode" data-tve-style="3">
            <div class="tve_fg tve_fg3 tve_red">
                <span class="tve_badge"></span>

                <div class="tve_line">
                    <h3>100% MONEY BACK GUARANTEE</h3>
                </div>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore
                    et
                    dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut
                    aliquip
                    ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum
                    dolore eu
                    fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia
                    deserunt mollit anim id est laborum.</p>
            </div>
        </div>
    </div>
    <div data-elem="sc_guarantee4">
        <div class="thrv_wrapper thrv_guarantee_shortcode" data-tve-style="4">
            <div class="tve_fg tve_fg4 tve_red">
                <div class="tve_rbn">
                    <span class="tve_badge"></span>

                    <div class="tve_line tve_left">
                        <h3>100% MONEY BACK GUARANTEE</h3>
                    </div>
                    <span class="tve_left"></span>

                    <div class="tve_clear"></div>
                </div>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore
                    et
                    dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut
                    aliquip
                    ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum
                    dolore eu
                    fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia
                    deserunt mollit anim id est laborum.</p>
            </div>
        </div>
    </div>
    <div data-elem="sc_pricing_table_1col">
        <div class="thrv_wrapper tve_prt tve_red" data-tve-style="1">
            <div class="tve_one tve_prt_col">
                <div class="tve_prt_in">
                    <div class="tve_ctr">
                        <h2>SILVER</h2>

                        <p class="tve_cond">Byline about this plan.</p>
                    </div>
                    <div class="tve_ftr">
                        <p><b>3</b> Users</p>

                        <p><b>5GB</b> of Storage</p>

                        <div class="thrv_wrapper thrv_bullets_shortcode">
                            <ul class="tve_ul tve_ul1 tve_white">
                                <li>Bullet Point 1</li>
                                <li>Bullet Point 2</li>
                            </ul>
                        </div>
                        <div class="thrv_wrapper thrv_bullets_shortcode" style="margin-top:0px;">
                            <ul class="tve_ul tve_ul7 tve_red">
                                <li>Bullet Point 1</li>
                                <li>Bullet Point 2</li>
                            </ul>
                        </div>
                    </div>
                    <div class="tve_ctr">
                        <h3>$12<span> / month</span></h3>
                    </div>
                    <div class="thrv_wrapper thrv_button_shortcode" data-tve-style="1">
                        <div class="tve_btn tve_btn7 tve_white tve_normalBtn">
                            <a href="" class="tve_btnLink">
                                <div class="tve_left">
                                    <i></i>
                                </div>
                                ADD TO CART
                            </a>
                        </div>
                    </div>
                    <div class="tve_ctr">
                        <p class="tve_cond">No credit card required.</p>
                    </div>
                </div>
            </div>
            <div class="tve_clear"></div>
        </div>
    </div>
    <div data-elem="sc_pricing_table_2col">
        <div class="thrv_wrapper tve_prt tve_red" data-tve-style="1">
            <div class="tve_two tve_prt_col">
                <div class="tve_prt_in">
                    <div class="tve_ctr">
                        <h2>SILVER</h2>

                        <p class="tve_cond">Byline about this plan.</p>
                    </div>
                    <div class="tve_ftr">
                        <p><b>3</b> Users</p>

                        <p><b>5GB</b> of Storage</p>

                        <div class="thrv_wrapper thrv_bullets_shortcode">
                            <ul class="tve_ul tve_ul1 tve_white">
                                <li>Bullet Point 1</li>
                                <li>Bullet Point 2</li>
                            </ul>
                        </div>
                        <div class="thrv_wrapper thrv_bullets_shortcode" style="margin-top:0px;">
                            <ul class="tve_ul tve_ul7 tve_red">
                                <li>Bullet Point 1</li>
                                <li>Bullet Point 2</li>
                            </ul>
                        </div>
                    </div>
                    <div class="tve_ctr">
                        <h3>$12<span> / month</span></h3>
                    </div>
                    <div class="thrv_wrapper thrv_button_shortcode" data-tve-style="1">
                        <div class="tve_btn tve_btn7 tve_white tve_normalBtn">
                            <a href="" class="tve_btnLink">
                                <div class="tve_left">
                                    <i></i>
                                </div>
                                ADD TO CART
                            </a>
                        </div>
                    </div>
                    <div class="tve_ctr">
                        <p class="tve_cond">No credit card required.</p>
                    </div>
                </div>
            </div>
            <div class="tve_two tve_prt_col tve_hgh">
                <div class="tve_prt_in">
                    <div class="tve_ctr">
                        <h2>SILVER</h2>

                        <p class="tve_cond">Byline about this plan.</p>
                    </div>

                    <div class="tve_ftr">
                        <p><b>3</b> Users</p>

                        <p><b>5GB</b> of Storage</p>

                        <div class="thrv_wrapper thrv_bullets_shortcode">
                            <ul class="tve_ul tve_ul1 tve_white">
                                <li>Bullet Point 1</li>
                                <li>Bullet Point 2</li>
                            </ul>
                        </div>
                        <div class="thrv_wrapper thrv_bullets_shortcode" style="margin-top:0px;">
                            <ul class="tve_ul tve_ul7 tve_red">
                                <li>Bullet Point 1</li>
                                <li>Bullet Point 2</li>
                            </ul>
                        </div>
                    </div>
                    <div class="tve_ctr">
                        <h3>$12<span> / month</span></h3>
                    </div>
                    <div class="thrv_wrapper thrv_button_shortcode" data-tve-style="1">
                        <div class="tve_btn tve_btn7 tve_white tve_normalBtn">
                            <a href="" class="tve_btnLink">
                                <div class="tve_left">
                                    <i></i>
                                </div>
                                ADD TO CART
                            </a>
                        </div>
                    </div>
                    <div class="tve_ctr">
                        <p class="tve_cond">No credit card required.</p>
                    </div>
                </div>
            </div>
            <div class="tve_clear"></div>
        </div>
    </div>
    <div data-elem="sc_pricing_table_3col">
        <div class="thrv_wrapper tve_prt tve_red" data-tve-style="1">
            <div class="tve_three tve_prt_col">
                <div class="tve_prt_in">
                    <div class="tve_ctr">
                        <h2>SILVER</h2>

                        <p class="tve_cond">Byline about this plan.</p>
                    </div>

                    <div class="tve_ftr">
                        <p><b>3</b> Users</p>

                        <p><b>5GB</b> of Storage</p>

                        <div class="thrv_wrapper thrv_bullets_shortcode">
                            <ul class="tve_ul tve_ul1 tve_white">
                                <li>Bullet Point 1</li>
                                <li>Bullet Point 2</li>
                            </ul>
                        </div>
                        <div class="thrv_wrapper thrv_bullets_shortcode" style="margin-top:0px;">
                            <ul class="tve_ul tve_ul7 tve_red">
                                <li>Bullet Point 1</li>
                                <li>Bullet Point 2</li>
                            </ul>
                        </div>
                    </div>
                    <div class="tve_ctr">
                        <h3>$12<span> / month</span></h3>
                    </div>
                    <div class="thrv_wrapper thrv_button_shortcode" data-tve-style="1">
                        <div class="tve_btn tve_btn7 tve_red tve_normalBtn">
                            <a href="" class="tve_btnLink">
                                <div class="tve_left">
                                    <i></i>
                                </div>
                                ADD TO CART
                            </a>
                        </div>
                    </div>
                    <div class="tve_ctr">
                        <p class="tve_cond">No credit card required.</p>
                    </div>
                </div>
            </div>
            <div class="tve_three tve_prt_col tve_hgh">
                <div class="tve_prt_in">
                    <div class="tve_ctr">
                        <h2>SILVER</h2>

                        <p class="tve_cond">Byline about this plan.</p>
                    </div>

                    <div class="tve_ftr">
                        <p><b>3</b> Users</p>

                        <p><b>5GB</b> of Storage</p>

                        <div class="thrv_wrapper thrv_bullets_shortcode">
                            <ul class="tve_ul tve_ul1 tve_white">
                                <li>Bullet Point 1</li>
                                <li>Bullet Point 2</li>
                            </ul>
                        </div>
                        <div class="thrv_wrapper thrv_bullets_shortcode" style="margin-top:0px;">
                            <ul class="tve_ul tve_ul7 tve_red">
                                <li>Bullet Point 1</li>
                                <li>Bullet Point 2</li>
                            </ul>
                        </div>
                    </div>
                    <div class="tve_ctr">
                        <h3>$12<span> / month</span></h3>
                    </div>
                    <div class="thrv_wrapper thrv_button_shortcode" data-tve-style="1">
                        <div class="tve_btn tve_btn7 tve_red tve_normalBtn">
                            <a href="" class="tve_btnLink">
                                <div class="tve_left">
                                    <i></i>
                                </div>
                                ADD TO CART
                            </a>
                        </div>
                    </div>
                    <div class="tve_ctr">
                        <p class="tve_cond">No credit card required.</p>
                    </div>
                </div>
            </div>
            <div class="tve_three tve_prt_col">
                <div class="tve_prt_in">
                    <div class="tve_ctr">
                        <h2>SILVER</h2>

                        <p class="tve_cond">Byline about this plan.</p>
                    </div>

                    <div class="tve_ftr">
                        <p><b>3</b> Users</p>

                        <p><b>5GB</b> of Storage</p>

                        <div class="thrv_wrapper thrv_bullets_shortcode">
                            <ul class="tve_ul tve_ul1 tve_white">
                                <li>Bullet Point 1</li>
                                <li>Bullet Point 2</li>
                            </ul>
                        </div>
                        <div class="thrv_wrapper thrv_bullets_shortcode" style="margin-top:0px;">
                            <ul class="tve_ul tve_ul7 tve_red">
                                <li>Bullet Point 1</li>
                                <li>Bullet Point 2</li>
                            </ul>
                        </div>
                    </div>
                    <div class="tve_ctr">
                        <h3>$12<span> / month</span></h3>
                    </div>
                    <div class="thrv_wrapper thrv_button_shortcode" data-tve-style="1">
                        <div class="tve_btn tve_btn7 tve_red tve_normalBtn">
                            <a href="" class="tve_btnLink">
                                <div class="tve_left">
                                    <i></i>
                                </div>
                                ADD TO CART
                            </a>
                        </div>
                    </div>
                    <div class="tve_ctr">
                        <p class="tve_cond">No credit card required.</p>
                    </div>
                </div>
            </div>
            <div class="tve_clear"></div>
        </div>
    </div>
    <div data-elem="sc_pricing_table_4col">
        <div class="thrv_wrapper tve_prt tve_red" data-tve-style="1">
            <div class="tve_four tve_prt_col">
                <div class="tve_prt_in">
                    <div class="tve_ctr">
                        <h2>SILVER</h2>

                        <p class="tve_cond">Byline about this plan.</p>
                    </div>

                    <div class="tve_ftr">
                        <p><b>3</b> Users</p>

                        <p><b>5GB</b> of Storage</p>

                        <div class="thrv_wrapper thrv_bullets_shortcode">
                            <ul class="tve_ul tve_ul1 tve_white">
                                <li>Bullet Point 1</li>
                                <li>Bullet Point 2</li>
                            </ul>
                        </div>
                        <div class="thrv_wrapper thrv_bullets_shortcode" style="margin-top:0px;">
                            <ul class="tve_ul tve_ul7 tve_red">
                                <li>Bullet Point 1</li>
                                <li>Bullet Point 2</li>
                            </ul>
                        </div>
                    </div>
                    <div class="tve_ctr">
                        <h3>$12<span> / month</span></h3>
                    </div>
                    <div class="thrv_wrapper thrv_button_shortcode" data-tve-style="1">
                        <div class="tve_btn tve_btn7 tve_red tve_normalBtn">
                            <a href="" class="tve_btnLink">
                                <div class="tve_left">
                                    <i></i>
                                </div>
                                ADD TO CART
                            </a>
                        </div>
                    </div>
                    <div class="tve_ctr">
                        <p class="tve_cond">No credit card required.</p>
                    </div>
                </div>
            </div>
            <div class="tve_four tve_prt_col">
                <div class="tve_prt_in">
                    <div class="tve_ctr">
                        <h2>SILVER</h2>

                        <p class="tve_cond">Byline about this plan.</p>
                    </div>

                    <div class="tve_ftr">
                        <p><b>3</b> Users</p>

                        <p><b>5GB</b> of Storage</p>

                        <div class="thrv_wrapper thrv_bullets_shortcode">
                            <ul class="tve_ul tve_ul1 tve_white">
                                <li>Bullet Point 1</li>
                                <li>Bullet Point 2</li>
                            </ul>
                        </div>
                        <div class="thrv_wrapper thrv_bullets_shortcode" style="margin-top:0px;">
                            <ul class="tve_ul tve_ul7 tve_red">
                                <li>Bullet Point 1</li>
                                <li>Bullet Point 2</li>
                            </ul>
                        </div>
                    </div>
                    <div class="tve_ctr">
                        <h3>$12<span> / month</span></h3>
                    </div>
                    <div class="thrv_wrapper thrv_button_shortcode" data-tve-style="1">
                        <div class="tve_btn tve_btn7 tve_red tve_normalBtn">
                            <a href="" class="tve_btnLink">
                                <div class="tve_left">
                                    <i></i>
                                </div>
                                ADD TO CART
                            </a>
                        </div>
                    </div>
                    <div class="tve_ctr">
                        <p class="tve_cond">No credit card required.</p>
                    </div>
                </div>
            </div>
            <div class="tve_four tve_prt_col tve_hgh">
                <div class="tve_prt_in">
                    <div class="tve_ctr">
                        <h2>SILVER</h2>

                        <p class="tve_cond">Byline about this plan.</p>
                    </div>

                    <div class="tve_ftr">
                        <p><b>3</b> Users</p>

                        <p><b>5GB</b> of Storage</p>

                        <div class="thrv_wrapper thrv_bullets_shortcode">
                            <ul class="tve_ul tve_ul1 tve_white">
                                <li>Bullet Point 1</li>
                                <li>Bullet Point 2</li>
                            </ul>
                        </div>
                        <div class="thrv_wrapper thrv_bullets_shortcode" style="margin-top:0px;">
                            <ul class="tve_ul tve_ul7 tve_red">
                                <li>Bullet Point 1</li>
                                <li>Bullet Point 2</li>
                            </ul>
                        </div>
                    </div>
                    <div class="tve_ctr">
                        <h3>$12<span> / month</span></h3>
                    </div>
                    <div class="thrv_wrapper thrv_button_shortcode" data-tve-style="1">
                        <div class="tve_btn tve_btn7 tve_red tve_normalBtn">
                            <a href="" class="tve_btnLink">
                                <div class="tve_left">
                                    <i></i>
                                </div>
                                ADD TO CART
                            </a>
                        </div>
                    </div>
                    <div class="tve_ctr">
                        <p class="tve_cond">No credit card required.</p>
                    </div>
                </div>
            </div>
            <div class="tve_four tve_prt_col">
                <div class="tve_prt_in">
                    <div class="tve_ctr">
                        <h2>SILVER</h2>

                        <p class="tve_cond">Byline about this plan.</p>
                    </div>

                    <div class="tve_ftr">
                        <p><b>3</b> Users</p>

                        <p><b>5GB</b> of Storage</p>

                        <div class="thrv_wrapper thrv_bullets_shortcode">
                            <ul class="tve_ul tve_ul1 tve_white">
                                <li>Bullet Point 1</li>
                                <li>Bullet Point 2</li>
                            </ul>
                        </div>
                        <div class="thrv_wrapper thrv_bullets_shortcode" style="margin-top:0px;">
                            <ul class="tve_ul tve_ul7 tve_red">
                                <li>Bullet Point 1</li>
                                <li>Bullet Point 2</li>
                            </ul>
                        </div>
                    </div>
                    <div class="tve_ctr">
                        <h3>$12<span> / month</span></h3>
                    </div>
                    <div class="thrv_wrapper thrv_button_shortcode" data-tve-style="1">
                        <div class="tve_btn tve_btn7 tve_red tve_normalBtn">
                            <a href="" class="tve_btnLink">
                                <div class="tve_left">
                                    <i></i>
                                </div>
                                ADD TO CART
                            </a>
                        </div>
                    </div>
                    <div class="tve_ctr">
                        <p class="tve_cond">No credit card required.</p>
                    </div>
                </div>
            </div>
            <div class="tve_clear"></div>
        </div>
    </div>
    <div data-elem="sc_pricing_table_5col">
        <div class="thrv_wrapper tve_prt tve_red" data-tve-style="1">
            <div class="tve_five tve_prt_col">
                <div class="tve_prt_in">
                    <div class="tve_ctr">
                        <h2>SILVER</h2>

                        <p class="tve_cond">Byline about this plan.</p>
                    </div>

                    <div class="tve_ftr">
                        <p><b>3</b> Users</p>

                        <p><b>5GB</b> of Storage</p>

                        <div class="thrv_wrapper thrv_bullets_shortcode">
                            <ul class="tve_ul tve_ul1 tve_blue">
                                <li>Bullet Point 1</li>
                                <li>Bullet Point 2</li>
                            </ul>
                        </div>
                        <div class="thrv_wrapper thrv_bullets_shortcode" style="margin-top:0px;">
                            <ul class="tve_ul tve_ul7 tve_red">
                                <li>Bullet Point 1</li>
                                <li>Bullet Point 2</li>
                            </ul>
                        </div>
                    </div>
                    <div class="tve_ctr">
                        <h3>$12<span> / month</span></h3>
                    </div>
                    <div class="thrv_wrapper thrv_button_shortcode" data-tve-style="1">
                        <div class="tve_btn tve_btn7 tve_red tve_normalBtn">
                            <a href="" class="tve_btnLink">
                                <div class="tve_left">
                                    <i></i>
                                </div>
                                ADD TO CART
                            </a>
                        </div>
                    </div>
                    <div class="tve_ctr">
                        <p class="tve_cond">No credit card required.</p>
                    </div>
                </div>
            </div>
            <div class="tve_five tve_prt_col tve_hgh">
                <div class="tve_prt_in">
                    <div class="tve_ctr">
                        <h2>SILVER</h2>

                        <p class="tve_cond">Byline about this plan.</p>
                    </div>

                    <div class="tve_ftr">
                        <p><b>3</b> Users</p>

                        <p><b>5GB</b> of Storage</p>

                        <div class="thrv_wrapper thrv_bullets_shortcode">
                            <ul class="tve_ul tve_ul1 tve_blue">
                                <li>Bullet Point 1</li>
                                <li>Bullet Point 2</li>
                            </ul>
                        </div>
                        <div class="thrv_wrapper thrv_bullets_shortcode" style="margin-top:0px;">
                            <ul class="tve_ul tve_ul7 tve_red">
                                <li>Bullet Point 1</li>
                                <li>Bullet Point 2</li>
                            </ul>
                        </div>
                    </div>
                    <div class="tve_ctr">
                        <h3>$12<span> / month</span></h3>
                    </div>
                    <div class="thrv_wrapper thrv_button_shortcode" data-tve-style="1">
                        <div class="tve_btn tve_btn7 tve_red tve_normalBtn">
                            <a href="" class="tve_btnLink">
                                <div class="tve_left">
                                    <i></i>
                                </div>
                                ADD TO CART
                            </a>
                        </div>
                    </div>
                    <div class="tve_ctr">
                        <p class="tve_cond">No credit card required.</p>
                    </div>
                </div>
            </div>
            <div class="tve_five tve_prt_col">
                <div class="tve_prt_in">
                    <div class="tve_ctr">
                        <h2>SILVER</h2>

                        <p class="tve_cond">Byline about this plan.</p>
                    </div>

                    <div class="tve_ftr">
                        <p><b>3</b> Users</p>

                        <p><b>5GB</b> of Storage</p>

                        <div class="thrv_wrapper thrv_bullets_shortcode">
                            <ul class="tve_ul tve_ul1 tve_blue">
                                <li>Bullet Point 1</li>
                                <li>Bullet Point 2</li>
                            </ul>
                        </div>
                        <div class="thrv_wrapper thrv_bullets_shortcode" style="margin-top:0px;">
                            <ul class="tve_ul tve_ul7 tve_red">
                                <li>Bullet Point 1</li>
                                <li>Bullet Point 2</li>
                            </ul>
                        </div>
                    </div>
                    <div class="tve_ctr">
                        <h3>$12<span> / month</span></h3>
                    </div>
                    <div class="thrv_wrapper thrv_button_shortcode" data-tve-style="1">
                        <div class="tve_btn tve_btn7 tve_red tve_normalBtn">
                            <a href="" class="tve_btnLink">
                                <div class="tve_left">
                                    <i></i>
                                </div>
                                ADD TO CART
                            </a>
                        </div>
                    </div>
                    <div class="tve_ctr">
                        <p class="tve_cond">No credit card required.</p>
                    </div>
                </div>
            </div>
            <div class="tve_five tve_prt_col">
                <div class="tve_prt_in">
                    <div class="tve_ctr">
                        <h2>SILVER</h2>

                        <p class="tve_cond">Byline about this plan.</p>
                    </div>

                    <div class="tve_ftr">
                        <p><b>3</b> Users</p>

                        <p><b>5GB</b> of Storage</p>

                        <div class="thrv_wrapper thrv_bullets_shortcode">
                            <ul class="tve_ul tve_ul1 tve_blue">
                                <li>Bullet Point 1</li>
                                <li>Bullet Point 2</li>
                            </ul>
                        </div>
                        <div class="thrv_wrapper thrv_bullets_shortcode" style="margin-top:0px;">
                            <ul class="tve_ul tve_ul7 tve_red">
                                <li>Bullet Point 1</li>
                                <li>Bullet Point 2</li>
                            </ul>
                        </div>
                    </div>
                    <div class="tve_ctr">
                        <h3>$12<span> / month</span></h3>
                    </div>
                    <div class="thrv_wrapper thrv_button_shortcode" data-tve-style="1">
                        <div class="tve_btn tve_btn7 tve_red tve_normalBtn">
                            <a href="" class="tve_btnLink">
                                <div class="tve_left">
                                    <i></i>
                                </div>
                                ADD TO CART
                            </a>
                        </div>
                    </div>
                    <div class="tve_ctr">
                        <p class="tve_cond">No credit card required.</p>
                    </div>
                </div>
            </div>
            <div class="tve_five tve_prt_col">
                <div class="tve_prt_in">
                    <div class="tve_ctr">
                        <h2>SILVER</h2>

                        <p class="tve_cond">Byline about this plan.</p>
                    </div>

                    <div class="tve_ftr">
                        <p><b>3</b> Users</p>

                        <p><b>5GB</b> of Storage</p>

                        <div class="thrv_wrapper thrv_bullets_shortcode">
                            <ul class="tve_ul tve_ul1 tve_blue">
                                <li>Bullet Point 1</li>
                                <li>Bullet Point 2</li>
                            </ul>
                        </div>
                        <div class="thrv_wrapper thrv_bullets_shortcode" style="margin-top:0px;">
                            <ul class="tve_ul tve_ul7 tve_red">
                                <li>Bullet Point 1</li>
                                <li>Bullet Point 2</li>
                            </ul>
                        </div>
                    </div>
                    <div class="tve_ctr">
                        <h3>$12<span> / month</span></h3>
                    </div>
                    <div class="thrv_wrapper thrv_button_shortcode" data-tve-style="1">
                        <div class="tve_btn tve_btn7 tve_red tve_normalBtn">
                            <a href="" class="tve_btnLink">
                                <div class="tve_left">
                                    <i></i>
                                </div>
                                ADD TO CART
                            </a>
                        </div>
                    </div>
                    <div class="tve_ctr">
                        <p class="tve_cond">No credit card required.</p>
                    </div>
                </div>
            </div>
            <div class="tve_clear"></div>
        </div>
    </div>
    <div data-elem="sc_tabs">
        <div class="thrv_wrapper thrv_tabs_shortcode">
            <div class="tve_scT tve_red">
                <ul class="tve_clearfix">
                    <li class="tve_tS"><span class="tve_scTC1">First tab</span></li>
                    <li><span class="tve_scTC2">Second tab</span></li>
                    <li><span class="tve_scTC3">Third tab</span></li>
                </ul>
                <div class="tve_scTC tve_scTC1" style="display: block">
                    <p>Tab 1</p>
                </div>
                <div class="tve_scTC tve_scTC2">
                    <p>Tab 2</p>
                </div>
                <div class="tve_scTC tve_scTC3">
                    <p>Tab 3</p>
                </div>
            </div>
        </div>
    </div>
    <div data-elem="sc_vTabs">
        <div class="thrv_wrapper thrv_tabs_shortcode">
            <div class="tve_scT tve_vtabs tve_red">
                <ul class="tve_clearfix">
                    <li class="tve_tS"><span class="tve_scTC1">First tab</span></li>
                    <li><span class="tve_scTC2">Second tab</span></li>
                    <li><span class="tve_scTC3">Third tab</span></li>
                </ul>
                <div class="tve_scTC tve_scTC1" style="display: block">
                    <p>Tab 1</p>
                </div>
                <div class="tve_scTC tve_scTC2">
                    <p>Tab 2</p>
                </div>
                <div class="tve_scTC tve_scTC3">
                    <p>Tab 3</p>
                </div>
            </div>
        </div>
    </div>
    <div data-elem="sc_feature_grid_2_column">
        <div class="thrv_wrapper thrv_feature_grid tve_gr tve_gr3" data-tve-style="1">
            <div class="tve_colm tve_twc">
                <div class="tve_left tve_gri">
                    <div class="image_placeholder thrv_wrapper">
                        <a class="upload_image tve_green_button clearfix" href="#" target="_self">
                            <i class="tve_icm tve-ic-upload"></i>
                            <span>Add Media</span>
                        </a>
                    </div>
                </div>
                <div class="tve_left tve_grt">
                    <h3>Heading 1</h3>

                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Libero, vero?</p>
                </div>
                <div class="tve_clear"></div>
            </div>
            <div class="tve_colm tve_twc tve_lst">
                <div class="tve_left tve_gri">
                    <div class="image_placeholder thrv_wrapper">
                        <a class="upload_image tve_green_button clearfix" href="#" target="_self">
                            <i class="tve_icm tve-ic-upload"></i>
                            <span>Add Media</span>
                        </a>
                    </div>
                </div>
                <div class="tve_left tve_grt">
                    <h3>Heading 2</h3>

                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Libero, vero?</p>
                </div>
                <div class="tve_clear"></div>
            </div>
            <div class="tve_clear"></div>
        </div>
    </div>
    <div data-elem="sc_feature_grid_2_column_icons">
        <div class="thrv_wrapper thrv_feature_grid tve_gr tve_gr3" data-tve-style="1">
            <div class="tve_colm tve_twc">
                <div class="tve_left tve_gri">
                    <?php include TVE_TEMPLATES_PATH . '/sc_icon.php' ?>
                </div>
                <div class="tve_left tve_grt">
                    <h3>Heading 1</h3>

                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Libero, vero?</p>
                </div>
                <div class="tve_clear"></div>
            </div>
            <div class="tve_colm tve_twc tve_lst">
                <div class="tve_left tve_gri">
                    <?php include TVE_TEMPLATES_PATH . '/sc_icon.php' ?>
                </div>
                <div class="tve_left tve_grt">
                    <h3>Heading 2</h3>

                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Libero, vero?</p>
                </div>
                <div class="tve_clear"></div>
            </div>
            <div class="tve_clear"></div>
        </div>
    </div>
    <div data-elem="sc_feature_grid_3_column">
        <div class="thrv_wrapper thrv_feature_grid tve_gr tve_gr3" data-tve-style="1">
            <div class="tve_colm tve_oth">
                <div class="tve_left tve_gri">
                    <div class="image_placeholder thrv_wrapper">
                        <a class="upload_image tve_green_button clearfix" href="#" target="_self">
                            <i class="tve_icm tve-ic-upload"></i>
                            <span>Add Media</span>
                        </a>
                    </div>
                </div>
                <div class="tve_left tve_grt">
                    <h3>Heading 1</h3>

                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Libero, vero?</p>
                </div>
                <div class="tve_clear"></div>
            </div>
            <div class="tve_colm tve_oth">
                <div class="tve_left tve_gri">
                    <div class="image_placeholder thrv_wrapper">
                        <a class="upload_image tve_green_button clearfix" href="#" target="_self">
                            <i class="tve_icm tve-ic-upload"></i>
                            <span>Add Media</span>
                        </a>
                    </div>
                </div>
                <div class="tve_left tve_grt">
                    <h3>Heading 2</h3>

                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Libero, vero?</p>
                </div>
                <div class="tve_clear"></div>
            </div>
            <div class="tve_colm tve_thc tve_lst">
                <div class="tve_left tve_gri">
                    <div class="image_placeholder thrv_wrapper">
                        <a class="upload_image tve_green_button clearfix" href="#" target="_self">
                            <i class="tve_icm tve-ic-upload"></i>
                            <span>Add Media</span>
                        </a>
                    </div>
                </div>
                <div class="tve_left tve_grt">
                    <h3>Heading 3</h3>

                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Libero, vero?</p>
                </div>
                <div class="tve_clear"></div>
            </div>
            <div class="tve_clear"></div>
        </div>
    </div>
    <div data-elem="sc_feature_grid_3_column_icons">
        <div class="thrv_wrapper thrv_feature_grid tve_gr tve_gr3" data-tve-style="1">
            <div class="tve_colm tve_oth">
                <div class="tve_left tve_gri">
                    <?php include TVE_TEMPLATES_PATH . '/sc_icon.php' ?>
                </div>
                <div class="tve_left tve_grt">
                    <h3>Heading 1</h3>

                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Libero, vero?</p>
                </div>
                <div class="tve_clear"></div>
            </div>
            <div class="tve_colm tve_oth">
                <div class="tve_left tve_gri">
                    <?php include TVE_TEMPLATES_PATH . '/sc_icon.php' ?>
                </div>
                <div class="tve_left tve_grt">
                    <h3>Heading 2</h3>

                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Libero, vero?</p>
                </div>
                <div class="tve_clear"></div>
            </div>
            <div class="tve_colm tve_thc tve_lst">
                <div class="tve_left tve_gri">
                    <?php include TVE_TEMPLATES_PATH . '/sc_icon.php' ?>
                </div>
                <div class="tve_left tve_grt">
                    <h3>Heading 3</h3>

                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Libero, vero?</p>
                </div>
                <div class="tve_clear"></div>
            </div>
            <div class="tve_clear"></div>
        </div>
    </div>
    <div data-elem="sc_feature_grid_4_column">
        <div class="thrv_wrapper thrv_feature_grid tve_gr tve_gr3" data-tve-style="1">
            <div class="tve_colm tve_foc">
                <div class="tve_left tve_gri">
                    <div class="image_placeholder thrv_wrapper">
                        <a class="upload_image tve_green_button clearfix" href="#" target="_self">
                            <i class="tve_icm tve-ic-upload"></i>
                            <span>Add Media</span>
                        </a>
                    </div>
                </div>
                <div class="tve_left tve_grt">
                    <h3>Heading 1</h3>

                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Libero, vero?</p>
                </div>
                <div class="tve_clear"></div>
            </div>
            <div class="tve_colm tve_foc">
                <div class="tve_left tve_gri">
                    <div class="image_placeholder thrv_wrapper">
                        <a class="upload_image tve_green_button clearfix" href="#" target="_self">
                            <i class="tve_icm tve-ic-upload"></i>
                            <span>Add Media</span>
                        </a>
                    </div>
                </div>
                <div class="tve_left tve_grt">
                    <h3>Heading 2</h3>

                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Libero, vero?</p>
                </div>
                <div class="tve_clear"></div>
            </div>
            <div class="tve_colm tve_foc">
                <div class="tve_left tve_gri">
                    <div class="image_placeholder thrv_wrapper">
                        <a class="upload_image tve_green_button clearfix" href="#" target="_self">
                            <i class="tve_icm tve-ic-upload"></i>
                            <span>Add Media</span>
                        </a>
                    </div>
                </div>
                <div class="tve_left tve_grt">
                    <h3>Heading 3</h3>

                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Libero, vero?</p>
                </div>
                <div class="tve_clear"></div>
            </div>
            <div class="tve_colm tve_foc tve_lst">
                <div class="tve_left tve_gri">
                    <div class="image_placeholder thrv_wrapper">
                        <a class="upload_image tve_green_button clearfix" href="#" target="_self">
                            <i class="tve_icm tve-ic-upload"></i>
                            <span>Add Media</span>
                        </a>
                    </div>
                </div>
                <div class="tve_left tve_grt">
                    <h3>Heading 4</h3>

                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Libero, vero?</p>
                </div>
                <div class="tve_clear"></div>
            </div>
            <div class="tve_clear"></div>
        </div>
    </div>
    <div data-elem="sc_feature_grid_4_column_icons">
        <div class="thrv_wrapper thrv_feature_grid tve_gr tve_gr3" data-tve-style="1">
            <div class="tve_colm tve_foc">
                <div class="tve_left tve_gri">
                    <?php include TVE_TEMPLATES_PATH . '/sc_icon.php' ?>
                </div>
                <div class="tve_left tve_grt">
                    <h3>Heading 1</h3>

                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Libero, vero?</p>
                </div>
                <div class="tve_clear"></div>
            </div>
            <div class="tve_colm tve_foc">
                <div class="tve_left tve_gri">
                    <?php include TVE_TEMPLATES_PATH . '/sc_icon.php' ?>
                </div>
                <div class="tve_left tve_grt">
                    <h3>Heading 2</h3>

                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Libero, vero?</p>
                </div>
                <div class="tve_clear"></div>
            </div>
            <div class="tve_colm tve_foc">
                <div class="tve_left tve_gri">
                    <?php include TVE_TEMPLATES_PATH . '/sc_icon.php' ?>
                </div>
                <div class="tve_left tve_grt">
                    <h3>Heading 3</h3>

                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Libero, vero?</p>
                </div>
                <div class="tve_clear"></div>
            </div>
            <div class="tve_colm tve_foc tve_lst">
                <div class="tve_left tve_gri">
                    <?php include TVE_TEMPLATES_PATH . '/sc_icon.php' ?>
                </div>
                <div class="tve_left tve_grt">
                    <h3>Heading 4</h3>

                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Libero, vero?</p>
                </div>
                <div class="tve_clear"></div>
            </div>
            <div class="tve_clear"></div>
        </div>
    </div>
    <div data-elem="sc_toggle">
        <div class="thrv_wrapper thrv_toggle_shortcode tve_red">
            <div class="tve_faq">
                <div class="tve_faqI">
                    <div class="tve_faqB"><span class="tve_not_editable tve_toggle"></span>
                        <h4><?php echo __("Content Toggle Headline", "thrive-cb") ?></h4>
                    </div>
                    <div class="tve_faqC" style="display: none;">
                        <p><?php echo __("Add your content here...", 'thrive-cb') ?></p></div>
                </div>
            </div>
        </div>
    </div>
    <div data-elem="sc_table_plain">
        <div class="table_placeholder thrv_wrapper thrv_table">
            <a class="tve_click tve_lb_small tve_green_button clearfix" id="lb_table" data-ctrl="controls.lb_open">
                <input type="hidden" name="table_style" value="plain">
                <i class="tve_icm tve-ic-table"></i>
                <span><?php echo __("Add Table", "thrive-cb") ?></span>
            </a>
        </div>
    </div>
    <div data-elem="sc_gmap">
        <div class="image_placeholder thrv_wrapper">
            <a class="tve_click tve_green_button clearfix" id="lb_google_map" data-ctrl="controls.lb_open">
                <i class="tve_icm tve-ic-upload"></i>
                <span><?php echo __("Embed Google Map", "thrive-cb") ?></span>
            </a>
        </div>
    </div>
    <div data-elem="sc_responsive_video">
        <div class="responsive_video_placeholder thrv_responsive_video thrv_wrapper">
            <a class="tve_green_button clearfix" href="#" target="_self">
                <i class="tve_icm tve-ic-upload"></i>
                <span><?php echo __("Add Video", "thrive-cb") ?></span>
            </a>

            <div class="tve_responsive_video_container" style="display: none">
                <div class="video_overlay"></div>
                <iframe src="" frameborder="0" allowfullscreen></iframe>
            </div>
        </div>
    </div>
    <div data-elem="sc_contents_table">
        <div class="thrv_wrapper thrv_contents_table tve_ct tve_blue tve_clearfix" data-tve-style="1" data-columns="2">
            <div class="tve_contents_table tve_clearfix">
                <span class="tve_ct_title"><?php echo __("Quick Navigation", "thrive-cb") ?></span>

                <div class="tve_ct_content tve_clearfix"></div>
            </div>
        </div>
    </div>
    <div data-elem="sc_contentbox_icon">
        <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="symbol">
            <div class="tve_cb tve_cb_symbol tve_red">
                <?php $cb_icon = true;
                include TVE_TEMPLATES_PATH . '/sc_icon.php' ?>
                <div class="tve_cb_cnt">
                    <p><span class="bold_text">MAIN LABEL</span></p>

                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Molestiae, officia? </p>
                </div>
            </div>
        </div>
    </div>
    <div data-elem="sc_custom_html">
        <div class="tve_custom_html_placeholder code_placeholder thrv_wrapper">
            <a class="tve_click tve_green_button clearfix" id="lb_custom_html" data-ctrl="controls.lb_open">
                <i class="tve_icm tve-ic-code"></i>
                <span><?php echo __("Insert Custom HTML", "thrive-cb") ?></span>
            </a>
        </div>
    </div>
    <div data-elem="sc_post_grid">
        <div class="image_placeholder thrv_wrapper">
            <a id="lb_post_grid" class="tve_click tve_green_button tve_clearfix" href="javascript:void(0)"
               data-ctrl="controls.lb_open"
               data-wpapi="lb_post_grid"
               data-btn-text="Update" data-load="1">
                <i class="tve_icm tve-ic-upload"></i>
                <span><?php echo __("Add Post Grid", "thrive-cb") ?></span>
            </a>
        </div>
    </div>
    <?php /** these are the default elements that will be inserted into the page */ ?>
    <div data-elem="sc_social_custom">
        <div class="thrv_wrapper thrv_social thrv_social_custom">
            <div class="tve_social_items tve_social_custom tve_style_1 tve_social_medium tve_social_itb">
                <div class="tve_s_item tve_s_fb_share" data-s="fb_share" data-href="{tcb_post_url}">
                    <a href="javascript:void(0)" class="tve_s_link"><span class="tve_s_icon"></span><span
                            class="tve_s_text">Share</span><span
                            class="tve_s_count">0</span></a>
                </div>
                <div class="tve_s_item tve_s_g_share" data-s="g_share" data-href="{tcb_post_url}">
                    <a href="javascript:void(0)" class="tve_s_link"><span class="tve_s_icon"></span><span
                            class="tve_s_text">Share +1</span><span
                            class="tve_s_count">0</span></a>
                </div>
                <div class="tve_s_item tve_s_t_share" data-s="t_share" data-href="{tcb_post_url}">
                    <a href="javascript:void(0)" class="tve_s_link"><span class="tve_s_icon"></span><span
                            class="tve_s_text">Tweet</span><span
                            class="tve_s_count">0</span></a>
                </div>
            </div>
            <div class="tve_social_overlay"></div>
        </div>
    </div>
    <?php /** These are all available element templates */ ?>
    <div id="tve-elem-social-custom" style="display: none">
        <div class="tve_s_share_count"><span class="tve_s_cnt">0</span> shares</div>
        <div class="tve_s_item tve_s_fb_share" data-s="fb_share" data-href="{tcb_post_url}">
            <a href="javascript:void(0)" class="tve_s_link"><span class="tve_s_icon"></span><span class="tve_s_text">Share</span><span
                    class="tve_s_count">0</span></a>
        </div>
        <div class="tve_s_item tve_s_g_share" data-s="g_share" data-href="{tcb_post_url}">
            <a href="javascript:void(0)" class="tve_s_link"><span class="tve_s_icon"></span><span class="tve_s_text">Share +1</span><span
                    class="tve_s_count">0</span></a>
        </div>
        <div class="tve_s_item tve_s_t_share" data-s="t_share" data-href="{tcb_post_url}">
            <a href="javascript:void(0)" class="tve_s_link"><span class="tve_s_icon"></span><span class="tve_s_text">Tweet</span><span
                    class="tve_s_count">0</span></a>
        </div>
        <div class="tve_s_item tve_s_in_share" data-s="in_share" data-href="{tcb_post_url}">
            <a href="javascript:void(0)" class="tve_s_link"><span class="tve_s_icon"></span><span class="tve_s_text">Share</span><span
                    class="tve_s_count">0</span></a>
        </div>
        <div class="tve_s_item tve_s_pin_share" data-s="pin_share" data-href="{tcb_post_url}">
            <a href="javascript:void(0)" class="tve_s_link"><span class="tve_s_icon"></span><span
                    class="tve_s_text">Pin</span><span
                    class="tve_s_count">0</span></a>
        </div>
        <div class="tve_s_item tve_s_xing_share" data-s="xing_share" data-href="{tcb_post_url}">
            <a href="javascript:void(0)" class="tve_s_link"><span class="tve_s_icon"></span><span class="tve_s_text">Share</span><span
                    class="tve_s_count">0</span></a>
        </div>
    </div>
    <div data-elem="sc_social_default">
        <div class="thrv_wrapper thrv_social thrv_social_default" data-tve-style="default">
            <?php $defaults = array(
                'selected' => array(
                    'fb_share', 'g_share', 't_share'
                ),
                'href' => '',
                'type' => 'default',
                'btn_type' => 'btn'
            ) ?>
            <div class="thrive-shortcode-config"
                 style="display: none !important"><?php echo '__CONFIG_social_default__' . json_encode($defaults) . '__CONFIG_social_default__' ?></div>
            <?php echo tve_social_render_default($defaults) ?>
            <div class="tve_social_overlay"></div>
        </div>
    </div>
    <div data-elem="sc_progress_bar">
        <div class="thrv_wrapper thrv_progress_bar tve_red thrv_data_element" data-tve-style="1">
            <div class="tve_progress_bar">
                <div class="tve_progress_bar_fill_wrapper" style="width: 20%;" data-fill="20">
                    <div class="tve_progress_bar_fill"></div>
                    <div class="tve_data_element_label">
                        Progress Bar
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div data-elem="sc_fill_counter">
        <div class="thrv_wrapper thrv_fill_counter tve_red tve_normalfc thrv_data_element" data-tve-style="1">
            <div class="tve_fill_counter_n" style="stroke-dasharray: 476.25 635;" data-fill="75">
                <svg viewBox="0 0 202 202" class="tve_fill_counter_circle" shape-rendering="optimizeSpeed">
                    <circle r="101" cx="101" cy="101"></circle>
                </svg>
                <div class="tve_fill_text_in">
                    <div class="tve_fill_text_value">
                        <span class="tve_fill_text_before"></span>
                        <span class="tve_fill_text">75</span>
                        <span class="tve_fill_text_after">%</span>
                    </div>
                    <span class="tve_data_element_label">
                        Fill Counter
                    </span>
                </div>
            </div>
        </div>
    </div>
    <div data-elem="sc_number_counter">
        <div class="thrv_wrapper thrv_number_counter tve_red thrv_data_element" data-tve-style="1">
            <div class="tve_number_counter">
                <span class="tve_numberc_before"></span>
                <span class="tve_numberc_text" data-counter="123">123</span>
                <span class="tve_numberc_after">km</span>
                <span class="tve_data_element_label">
                    Number Counter
                </span>
            </div>
        </div>
    </div>
    <div data-elem="sc_facebook_comments">
        <div class="thrv_wrapper thrv_facebook_comments">
            <div class="tve-fb-comments" data-colorscheme="light" data-numposts="20" data-order-by="social" data-width="100%" data-href="" data-fb-moderator-ids="<?php echo $tve_facebook_admins; ?>"></div>
        </div>
    </div>
    <div data-elem="sc_disqus_comments">
        <div class="thrv_wrapper thrv_disqus_comments">
            <div id="disqus_thread" data-disqus_identifier="<?php echo $_POST['post_id']; ?>" data-disqus_shortname="<?php echo $tve_disqus_shortname; ?>" data-disqus_url=""></div>
        </div>
    </div>
</div>