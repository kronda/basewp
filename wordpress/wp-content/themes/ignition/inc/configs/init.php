<?php

require(get_template_directory() . "/inc/configs/constants.php");
require(get_template_directory() . "/inc/image-resize.php");

/**
 *
 * Sets up the theme and provides some helper functions, which are used in the
 * theme as custom template tags. Others are attached to action and filter
 * hooks in WordPress to change core functionality.
 *
 */
if (!function_exists('thrive_setup')):
    /*
     * Sets up the current theme's main options and include the additional files
     */

    function thrive_setup()
    {

        $default_background_color = 'e2e2e2';

        add_theme_support('custom-background', array(
            'default-color' => $default_background_color,
        ));

        add_theme_support('post-thumbnails');

        require(get_template_directory() . "/inc/helpers/labels.php");
        require(get_template_directory() . "/inc/page-templates.php");
        require(get_template_directory() . "/inc/theme-options.php");
        require(get_template_directory() . "/inc/meta-options.php");
        require(get_template_directory() . "/inc/widgets/widget-author.php");
        require(get_template_directory() . "/inc/widgets/widget-follow.php");
        require(get_template_directory() . "/inc/widgets/widget-optin.php");
        require(get_template_directory() . "/inc/widgets/widget-call.php");
        require(get_template_directory() . "/inc/widgets/widget-tabs.php");
        require(get_template_directory() . "/inc/widgets/widget-images.php");
        require(get_template_directory() . "/inc/widgets/widget-custom-text.php");
        require(get_template_directory() . "/inc/widgets/widget-custom-phone.php");
        require(get_template_directory() . "/inc/widgets/widget-related.php");
        require(get_template_directory() . "/inc/shortcodes/admin-shortcodes.php");
        require(get_template_directory() . "/inc/extra/theme-options.php");
        require(get_template_directory() . "/inc/font-manager.php");
        require(get_template_directory() . "/inc/font-import-manager/init.php");
        //require the helper for get users
        require(get_template_directory() . "/inc/helpers/users-autocomplete.php");
        //require the category landing pages plugin
        require(get_template_directory() . "/inc/thrive-category-landing-pages.php");

        if (thrive_get_theme_options('related_posts_enabled') == 1) {
            require(get_template_directory() . "/inc/helpers/related-posts.php");
        }
        //include the woocommerce methods only if the plugin is active
        if (class_exists('WooCommerce')) {
            include(get_template_directory() . '/inc/woocommerce.php');
        }

        // This theme uses wp_nav_menu() in one location.
        register_nav_menu('primary', __('Primary Menu', 'thrive'));

        register_nav_menu('footer', __('Footer menu', 'thrive'));
        //set the default options
        //thrive_set_default_customizer_options();

        //load all the apprentice features
        if (thrive_get_theme_options('appr_enable_feature') == 1) {
            require(get_template_directory() . "/inc/apprentice/functions.php");
        }

        require_once get_template_directory() . "/inc/thrive-optin.php";
    }

endif;
add_action('after_setup_theme', 'thrive_setup');


/*
 * Register and queue up the styles and the javascript used in the frontend
 */

function thrive_enqueue_scripts()
{
    // Load our main stylesheet.
    wp_enqueue_style('ignition-style', get_stylesheet_uri());

    $options = thrive_get_theme_options();
    $options['color_scheme'] = in_array($options['color_scheme'], array('blue', 'brown', 'dark', 'liliac', 'navy', 'pink', 'purple', 'red', 'yellow')) ? $options['color_scheme'] : 'dark';
    $main_css_path = get_template_directory_uri() . '/css/main_' . $options['color_scheme'] . '.css';

    if (!is_admin()) {
        wp_enqueue_script('jquery', false, false, "", true);
    }

    wp_register_script('jquerytouchwipe', get_template_directory_uri() . '/js/jquery.touchwipe.js', array('jquery'), "", true);
    wp_register_script('thrive-main-script', get_template_directory_uri() . '/js/script.min.js', array('jquery'), "", true);

    wp_register_style('thrive-main-style', $main_css_path, array("thrive-reset"), '2014123', 'all');
    wp_register_style('thrive-reset', get_template_directory_uri() . '/css/reset.css', array(), '20120208', 'all');

    wp_enqueue_script('thrive-main-script');

    wp_enqueue_style('thrive-reset');
    wp_enqueue_style('thrive-main-style');
    $lazy_load_comments = isset($options['comments_lazy']) ? $options['comments_lazy'] : 0;
    $params_array = array('ajax_url' => admin_url('admin-ajax.php'), 'lazy_load_comments' => $lazy_load_comments, 'comments_loaded' => 0);
    wp_localize_script('thrive-main-script', 'ThriveApp', $params_array);
}

add_action('wp_enqueue_scripts', 'thrive_enqueue_scripts');

/*
 * Register theme's current widgets and the 2 sidebars used in the theme
 */

function thrive_init_widgets()
{
    register_widget('Thrive_Author_Widget');
    register_widget('Thrive_Follow_Widget');
    register_widget('Thrive_Optin_Widget');
    register_widget('Thrive_Call_Widget');
    register_widget('Thrive_Tabs_Widget');
    register_widget('Thrive_Custom_Text');
    register_widget('Thrive_Images_Widget');
    register_widget('Thrive_Custom_Phone');
    register_widget('Thrive_Related_Widget');

    register_sidebar(array(
        'name' => __('Main Sidebar', 'thrive'),
        'id' => 'sidebar-1',
        'before_widget' => '<section id="%1$s"><div class="scn">',
        'after_widget' => '</div></section>',
        'before_title' => '<p class="ttl">',
        'after_title' => '</p>',
    ));

    register_sidebar(array(
        'name' => __('Pages Sidebar', 'thrive'),
        'id' => 'sidebar-2',
        'before_widget' => '<section id="%1$s"><div class="scn">',
        'after_widget' => '</div></section>',
        'before_title' => '<p class="ttl">',
        'after_title' => '</p>',
    ));

    register_sidebar(array(
        'name' => __('Footer Column 1', 'thrive'),
        'id' => 'footer-1',
        'before_widget' => '<section id="%1$s">',
        'after_widget' => '</section>',
        'before_title' => '<p class="ttl">',
        'after_title' => '</p>',
    ));

    register_sidebar(array(
        'name' => __('Footer Column 2', 'thrive'),
        'id' => 'footer-2',
        'before_widget' => '<section id="%1$s">',
        'after_widget' => '</section>',
        'before_title' => '<p class="ttl">',
        'after_title' => '</p>',
    ));

    register_sidebar(array(
        'name' => __('Footer Column 3', 'thrive'),
        'id' => 'footer-3',
        'before_widget' => '<section id="%1$s">',
        'after_widget' => '</section>',
        'before_title' => '<p class="ttl">',
        'after_title' => '</p>',
    ));
}

add_action('widgets_init', 'thrive_init_widgets');


// if no title then add widget content wrapper to before widget
add_filter('dynamic_sidebar_params', 'thrive_check_sidebar_params');
/*
 * Checks the place of a widget in order to generate the right markup
 * @param array $params Widget params
 * @return array The widget params formatted
 */

function thrive_check_sidebar_params($params)
{

    if (!isset($params[0]) || !isset($params[1]) || !isset($params[1]['number'])) {
        return $params;
    }

    global $wp_registered_widgets;
    $settings_getter = $wp_registered_widgets[$params[0]['widget_id']]['callback'][0];

    if (!$settings_getter || !is_object($settings_getter)) {
        return $params;
    }

    $widgets_without_default_titles = array('Search', 'Calendar', 'Text', 'Custom Menu', 'Links', 'Dropdown Menu');

    $settings = $settings_getter->get_settings();

    if (!isset($params[1]['number']) || !isset($settings[$params[1]['number']])) {
        return $params;
    }

    $settings = $settings[$params[1]['number']];

    /*
     * Add the correct markup for the widgets that don't have a default title set 
     * by Wordpress
     */
    if (isset($settings['title']) && empty($settings['title']) && ($params[0]['id'] == "sidebar-1" || $params[0]['id'] == "sidebar-2")) {

        if (in_array($params[0]['widget_name'], $widgets_without_default_titles)) {
            //$params[0]['before_widget'] = '<section><div class="scn">';
            //$params[0]['after_widget'] = '</div></section>';
        }
    }

    return $params;
}

/*
 * Includes various helper functions
 */
require(get_template_directory() . "/inc/helpers/helpers.php");

/*
 *  Register and queue up the necessary js and stylesheets for the admin section
 */

function thrive_enqueue_admin()
{

    // list of focus blog admin pages
    // only load scripts on our own admin pages
    $focus_blog_pages = array('focus_area',
        'thrive_optin',
        'toplevel_page_thrive_admin_options',
        'post',
        'page',
        'widgets',
        'tcb_lightbox',
        'thrive-options_page_thrive_admin_page_templates',
        TT_APPR_POST_TYPE_LESSON,
        TT_APPR_POST_TYPE_PAGE);

    $screen = get_current_screen();
    if (in_array($screen->id, $focus_blog_pages) || $screen->base == "post") {

        if (!get_option("thrive_license_status")) {
            thrive_license_notice();
        } else {

            wp_register_style('thrive-admin-focus', get_template_directory_uri() . '/inc/css/admin-focusareas.css');
            wp_register_style('thrive-admin-focustemplates', get_template_directory_uri() . '/css/focus_areas.css');
            wp_register_style('thrive-admin-responsivefocus', get_template_directory_uri() . '/inc/css/focus_areas_responsive.css');
            wp_register_style('thrive-select2-style', get_template_directory_uri() . '/inc/libs/select2.css');

            wp_register_script('thrive-nouislider', get_template_directory_uri() . '/inc/libs/jquery.nouislider.min.js', array('jquery'));
            wp_register_script('thrive-select2', get_template_directory_uri() . '/inc/libs/select2.js', array('jquery'));
            wp_register_script('thrive-focus-options', get_template_directory_uri() . '/inc/js/focus-areas.js', array('jquery'));
            wp_register_script('thrive-optin-options', get_template_directory_uri() . '/inc/js/optin-options.js', array('jquery'));
            wp_register_script('thrive-admin-shortcodes', get_template_directory_uri() . '/inc/js/shortcodes.js', array('jquery'));
            wp_register_script('thrive-admin-postedit', get_template_directory_uri() . '/inc/js/post-edit.js', array('jquery'));
            wp_register_script('thrive-admin-tooltips', get_template_directory_uri() . '/inc/js/tooltip/jquery.powertip.min.js', array('jquery'));
            wp_register_script('thrive-admin-tooltips-setup', get_template_directory_uri() . '/inc/js/admin-tooltips.js', array('jquery', 'thrive-admin-tooltips'));
            wp_register_script('thrive-theme-options', get_template_directory_uri() . '/inc/js/theme-options.js', array('jquery', 'media-upload', 'thickbox'));

            wp_register_style('thrive-nouislider-css', get_template_directory_uri() . '/inc/css/jquery.nouislider.min.css');
            wp_register_style('thrive-base-css', get_template_directory_uri() . '/inc/css/pure-base-min.css');
            wp_register_style('thrive-pure-css', get_template_directory_uri() . '/inc/css/pure-min.css');
            wp_register_style('thrive-admin-colors', get_template_directory_uri() . '/inc/css/thrive_admin_colours.css');
            wp_register_style('thrive-admin-tooltips', get_template_directory_uri() . '/inc/js/tooltip/css/jquery.powertip-green.css');

            wp_enqueue_style('thickbox');

            wp_enqueue_script('jquery');
            wp_enqueue_script('thickbox');
            wp_enqueue_script('media-upload');

            wp_enqueue_style('thrive-theme-options', get_template_directory_uri() . '/inc/css/theme-options.css', false, '2013-07-03');

            wp_enqueue_style('thrive-admin-colors');
            wp_enqueue_style('thrive-base-css');
            wp_enqueue_style('thrive-pure-css');
            wp_enqueue_style('thrive-admin-tooltips');

            wp_enqueue_script('thrive-admin-tooltips');
            wp_enqueue_script('thrive-admin-tooltips-setup');

            wp_enqueue_script('wp-color-picker');
            wp_enqueue_style('wp-color-picker');
        }
    }

    if ($screen->base == "post" || $screen->id == 'tcb_lightbox') {
        wp_enqueue_script('thrive-admin-postedit');
        wp_enqueue_script('thrive-theme-options');
        wp_enqueue_script('thrive-select2');
        wp_enqueue_script('thrive-nouislider');
        wp_enqueue_style('thrive-nouislider-css');
        //datetime picker
        wp_enqueue_script('thrive-admin-datetime-picker', get_template_directory_uri() . '/inc/js/jquery-ui-timepicker.js', array('jquery-ui-datepicker', 'jquery-ui-slider'));
        //colorpicker
        wp_enqueue_style('wp-color-picker');
    }

    if ($screen->id == 'nav-menus') {
        wp_enqueue_media();
        wp_enqueue_script('admin-menu', get_template_directory_uri() . '/inc/js/admin-menu.js', array('jquery', 'media-upload', 'thickbox'));
    }

    if ($screen->id == "toplevel_page_thrive_admin_options") {
        wp_enqueue_media();
        wp_enqueue_script('thrive-admin-postedit');
        wp_enqueue_script('thrive-select2');
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('thrive-nouislider');
        wp_enqueue_style('thrive-nouislider-css');
        wp_enqueue_style('thrive-theme-options', get_template_directory_uri() . '/inc/css/theme-options.css', false, '2013-07-03');
    }

    if ($screen->id == "thrive-themes_page_thrive_font_manager") {
        wp_enqueue_style('thickbox');
        wp_enqueue_script('thickbox');

        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');

        $imported_fonts_css = Thrive_Font_Import_Manager::getCssFile();
        if(!empty($imported_fonts_css)) {
            wp_enqueue_style('thrive-imported-fonts', $imported_fonts_css);
        }
    }

    if ($screen->id == "thrive-themes_page_thrive_admin_page_templates" || $screen->id == "thrive-themes_page_thrive_font_manager") {
        wp_enqueue_media();
        wp_enqueue_style('thrive-pure-css');
        wp_enqueue_style('thrive-theme-options', get_template_directory_uri() . '/inc/css/theme-options.css', false, '2013-07-03');
        wp_enqueue_style('thrive-admin-colors', get_template_directory_uri() . '/inc/css/thrive_admin_colours.css');
    }

    if ($screen->id == "focus_area") {
        add_editor_style(get_template_directory_uri() . '/inc/css/custom-editor-style.css');
    }

    if ($screen && ($screen->base == "widgets" || $screen->id == "widgets")) {
        wp_enqueue_media();
        wp_enqueue_script("jquery-ui-autocomplete");
        wp_enqueue_style("jquery-ui-autocomplete");
        wp_enqueue_script('thrive-widgets-options', get_template_directory_uri() . '/inc/js/widgets-options.js', array('jquery', 'media-upload', 'thickbox', 'jquery-ui-autocomplete'));

        //prepare the javascript params
        $getUsersWpnonce = wp_create_nonce("thrive_helper_get_users");
        $getUsersUrl = admin_url('admin-ajax.php?action=thrive_helper_get_users&nonce=' . $getUsersWpnonce);

        $js_params_array = array('getUsersUrl' => $getUsersUrl,
            'noonce' => $getUsersWpnonce);
        wp_localize_script('thrive-widgets-options', 'ThriveWidgetsOptions', $js_params_array);
    }

    if ($screen->id == "toplevel_page_thrive_admin_options" || $screen->id == "focus_area") {
        wp_enqueue_style('thrive-select2-style');
        wp_enqueue_script('thrive-select2');
    }

    if ($screen->id == "thrive_optin") {
        wp_enqueue_script('thrive-optin-params');
    }
}

add_action('admin_enqueue_scripts', 'thrive_enqueue_admin');
add_action('admin_head', 'thrive_admin_head');

/*
 * Ads specific customization options for the theme
 */
require(get_template_directory() . "/inc/theme-customize.php");

add_action('init', 'thrive_create_post_types');

/*
 * Register the new content types used by this theme 
 * (focus_area and thrive_optin)
 */

function thrive_create_post_types()
{
    register_post_type('focus_area', array(
        'labels' => array(
            'name' => __('Focus Areas', 'thrive'),
            'singular_name' => __('FocusArea', 'thrive')
        ),
        'public' => true,
        'exclude_from_search' => true,
        'publicly_queryable' => false,
        'has_archive' => false,
        'supports' => array('title', 'editor')
    ));

    remove_post_type_support('focus_area', 'editor');

    register_post_type('thrive_optin', array(
        'labels' => array(
            'name' => __('Thrive Opt-In', 'thrive'),
            'singular_name' => __('OptIn', 'thrive')
        ),
        'public' => true,
        'exclude_from_search' => true,
        'publicly_queryable' => false,
        'has_archive' => false,
        'supports' => array('title')
    ));
}

/*
 * Overwrites the rendering of the default template file for a specific post that
 * has a particular template assigned to it
 */

function thrive_template_redirect()
{
    // don't apply template redirects unless single post / page is being displayed.
    if (!is_singular() || _thrive_check_is_woocommerce_page()): return false;
    endif;

    $post_type = get_post_type();
    if ($post_type == TT_APPR_POST_TYPE_LESSON || $post_type == TT_APPR_POST_TYPE_PAGE) {
        return false;
    }

    $template_name = get_post_custom_values('_thrive_meta_post_template', get_the_ID());
    $template_name = isset($template_name[0]) ? $template_name[0] : "";
    $template_page_name = NULL;
    switch ($template_name) {
        case "Full Width":
            $template_page_name = 'fullwidth-page.php';
            break;
        case "Landing Page":
            $template_page_name = 'landing-page.php';
            break;
        case "Narrow":
            $template_page_name = 'narrow-page.php';
            break;
    }

    if ($template_page_name !== NULL) {
        include TEMPLATEPATH . '/' . $template_page_name;
        exit;
    } elseif (is_single()) {
        $default_blog_post_layout = thrive_get_theme_options("blog_post_layout");
        if ($default_blog_post_layout == "full_width") {
            include TEMPLATEPATH . "/fullwidth-page.php";
            exit;
        } elseif ($default_blog_post_layout == "narrow") {
            include TEMPLATEPATH . "/narrow-page.php";
            exit;
        }
    }
}

function thrive_template_include($template)
{
    // don't apply template redirects unless single post / page is being displayed.
    if (!is_singular() || _thrive_check_is_woocommerce_page()) {
        return $template;
    }

    $post_type = get_post_type();
    if ($post_type == TT_APPR_POST_TYPE_LESSON || $post_type == TT_APPR_POST_TYPE_PAGE) {
        return $template;
    }

    $template_name = get_post_custom_values('_thrive_meta_post_template', get_the_ID());
    $template_name = isset($template_name[0]) ? $template_name[0] : "";
    $template_page_name = NULL;
    switch ($template_name) {
        case "Full Width":
            $template_page_name = 'fullwidth-page.php';
            break;
        case "Landing Page":
            $template_page_name = 'landing-page.php';
            break;
        case "Narrow":
            $template_page_name = 'narrow-page.php';
            break;
    }

    if ($template_page_name !== NULL) {
        return TEMPLATEPATH . '/' . $template_page_name;
    } elseif (is_single()) {
        $default_blog_post_layout = thrive_get_theme_options("blog_post_layout");
        if ($default_blog_post_layout == "full_width") {
            return TEMPLATEPATH . "/fullwidth-page.php";
        } elseif ($default_blog_post_layout == "narrow") {
            return TEMPLATEPATH . "/narrow-page.php";
        }
    }

    return $template;
}

//add_action('template_redirect', 'thrive_template_redirect');

function thrive_template_include_wrong($template)
{
    if (!is_singular()) {
        return $template;
    }

    $post_type = get_post_type();
    if ($post_type == TT_APPR_POST_TYPE_LESSON || $post_type == TT_APPR_POST_TYPE_PAGE) {
        return $template;
    }

    $template_files = array(
        'Full Width' => 'fullwidth-page.php',
        'Landing Page' => 'landing-page.php',
        'Narrow' => 'narrow-page.php',
        'full_width' => 'fullwidth-page.php',
        'narrow' => 'narrow-page.php',
    );

    $post_template_name = 'Default';
    $is_single_post = is_single();

    //read this meta cos only posts can have post_templates
    if($is_single_post) {
        $post_template_name = get_post_custom_values('_thrive_meta_post_template', get_the_ID());
        $post_template_name = is_array($post_template_name) && !empty($post_template_name[0]) ? $post_template_name[0] : $post_template_name;
    }

    //check if we have file defined for post_template_name
    if (array_key_exists($post_template_name, $template_files) && !empty($template_files[$post_template_name])) {
        $template = get_stylesheet_directory() . "/" . $template_files[$post_template_name];
    }

    //if it's post and the post has default template
    //try to include theme blog post layout
    if ($is_single_post && $post_template_name === 'Default') {
        $blog_layout = thrive_get_theme_options("blog_post_layout");
        if (array_key_exists($blog_layout, $template_files) && !empty($template_files[$blog_layout])) {
            $template = get_stylesheet_directory() . "/" . $template_files[$blog_layout];
        }
    }

    return $template;
}
add_filter('template_include', 'thrive_template_include');

/*
 * Function to remove the preview and view post buttons for custom post types where it doesn't apply
 */

function thrive_admin_head()
{
    global $post_type;
    if ($post_type == 'thrive_optin' || $post_type == 'focus_area') {
        echo '<style type="text/css">#preview-action,#edit-slug-box,#view-post-btn,#post-preview,.updated p a{display: none;}</style>';
    }
    echo '<script type="text/javascript">var ThriveThemeUrl = "' . get_template_directory_uri() . '";</script>';
    //Workaround for the bug that causes the wp enqueue function not to work in the admin widgets section
    $screen = get_current_screen();
    if ($screen && ($screen->base == "widgets" || $screen->id == "widgets")) {
        //echo "<script type='text/javascript' src='" . get_template_directory_uri() . "/inc/js/widgets-options.js'></script>";
    }
}

// Setup the language file
add_action('after_setup_theme', 'thrive_language');

/**
 * Make theme available for translation
 */
function thrive_language()
{
    $locale = get_locale();

    $domain = 'thrive';

    $theme = wp_get_theme();

    if ($theme->get('Template')) {
        //if we're in a child theme, we get the parent template
        $theme_name = strtolower($theme->get('Template'));
    } else {
        $theme_name = strtolower($theme);
    }

    // wp-content/languages/thrive/{$theme}-{$locale}.mo
    load_textdomain($domain, trailingslashit(WP_LANG_DIR) . 'thrive/' . $theme_name . '-' . $locale . '.mo');
    // wp-content/themes/thrive/languages/{$locale}.mo
    load_theme_textdomain($domain, get_stylesheet_directory() . '/languages');
    // wp-content/themes/thrive/languages/{$locale}.mo
    load_theme_textdomain($domain, get_template_directory() . '/languages');

}

// notice to be displayed if license not validated - going to load the styles inline because there are so few lines and not worth an extra server hit.
function thrive_license_notice()
{
    ?>
    <div id="tve_license_notice">
        <img src="<?php echo get_template_directory_uri(); ?>/inc/images/TT-logo-small.png"
             class="thrive_admin_logo"/>

        <p>You need to <a href="<?php echo admin_url(); ?>admin.php?page=thrive_license_validation">activate
                your
                license</a> before you can use the theme!</p></div>
    <style type="text/css">
        #tve_license_notice {
            width: 500px;
            margin: 0 auto;
            text-align: center;
            top: 50%;
            left: 50%;
            margin-top: -100px;
            margin-left: -250px;
            padding: 50px;
            z-index: 3000;
            position: fixed;
            -moz-border-radius-bottomleft: 10px;
            -webkit-border-bottom-left-radius: 10px;
            border-bottom-left-radius: 10px;
            -moz-border-radius-bottomright: 10px;
            -webkit-border-bottom-right-radius: 10px;
            border-bottom-right-radius: 10px;
            border-bottom: 1px solid #bdbdbd;
            background-size: 100%;
            background-image: -webkit-gradient(linear, 50% 0%, 50% 100%, color-stop(20%, #ffffff), color-stop(100%, #e6e6e6));
            background-image: -webkit-linear-gradient(top, #ffffff 20%, #e6e6e6 100%);
            background-image: -moz-linear-gradient(top, #ffffff 20%, #e6e6e6 100%);
            background-image: -o-linear-gradient(top, #ffffff 20%, #e6e6e6 100%);
            background-image: linear-gradient(top, #ffffff 20%, #e6e6e6 100%);
            -moz-border-radius: 10px;
            -webkit-border-radius: 10px;
            border-radius: 10px;
            -webkit-box-shadow: 2px 5px 3px #efefef;
            -moz-box-shadow: 2px 5px 3px #efefef;
            box-shadow: 2px 5px 3px #efefef;
        }
    </style>
    <?php
}

function thrive_license_validation()
{
    include('license.php');
}

/*
 * Display top warning if the theme has not activated.
 */
if (!get_option("thrive_license_status")) {
    add_action('admin_notices', 'thrive_admin_notice');
}

function thrive_admin_notice()
{
    ?>
    <div class="update-nag">
        <p>
            <?php _e('Your theme has successfully been activated! Next step: please activate your license by entering your email and license key here: ', 'thrive'); ?>
            <a href="<?php echo admin_url(); ?>admin.php?page=thrive_license_validation">License Activation</a>
        </p>
    </div>
    <?php
}

?>
