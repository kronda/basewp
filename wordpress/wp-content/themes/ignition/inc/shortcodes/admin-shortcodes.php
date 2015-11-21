<?php
require_once plugin_dir_path(__FILE__) . 'shortcodes.php';

add_action('admin_head', 'thrive_print_shortcodes');

function thrive_print_shortcodes()
{
    ?>
    <script type="text/javascript">
        var ThriveThemeUrl = "<?php echo get_template_directory_uri(); ?>";
        var ThriveAdminAjaxUrl = "<?php echo admin_url('admin-ajax.php'); ?>";
        var thrive_shortcodes = [
            'TextAndLayout',
            'HeadlineFocus', 'CustomFont', 'Highlight', 'DropCaps', 'DividerLine', 'ContentContainer', 'BlankSpace', 'Code', 'Pullquote',
            'Conversion',
            'Button', 'SplitButton', 'MegaButton', 'Countdown', 'Price', 'Testimonal', 'Grid', 'Optin',
            'NumbersAndData',
            'NumberCounter', 'ProgressBar', 'FillCounter',
            'Lists',
            'PostsList', 'PostsGallery', 'CustomMenu',
            'ContentReveal',
            'Tabs', 'Toggle', 'Accordion',
            'Other',
            'ContentBox', 'CustomBox', 'IconBox', 'PageSection', 'VideoSection', 'ResponsiveVideo', 'FollowMe', 'Phone', 'GMaps', 'Borderless'
        ];
        /*var thrive_shortcodes = ['Button', 'BlankSpace', 'ContentContainer', 'ContentBox', 'CustomMenu', 'Borderless', 'Code', 'CustomMenu', 'CustomFont', 'DividerLine', 'FollowMe', 'Phone',
         'GMaps', 'Grid', 'Tabs', 'Toggle', 'PageSection', 'Pullquote', 'PostsList', 'PostsGallery', 'Price', 'Optin', 'Testimonal', 'VideoSection', 'ResponsiveVideo'];*/
        var thrive_shortcodes2 = ['Halves', 'Columns-1-2', 'Columns-1-3', 'Thirds', 'Columns-2-3-1', 'Columns-3-2-1', 'Fourths', 'Columns-1-4', 'Columns-3-4-1',
            'Columns-4-3-1', 'Columns-4-2-1', 'Columns-2-4-1', 'Columns-4-1-2'];
        <?php if (thrive_get_theme_options('appr_enable_feature') == 1):?>
        thrive_shortcodes.push("Apprentice", "LessonsList", "LessonsGallery", "WelcomeBack");
        <?php endif;?>
    </script>
    <?php
}

add_action("wp_ajax_nopriv_shortcode_display_options", "thrive_shortcode_display_options");
add_action("wp_ajax_shortcode_display_options", "thrive_shortcode_display_options");

function thrive_shortcode_display_options()
{

    $all_colors = _thrive_get_color_scheme_options();
    $req_path = get_template_directory() . "/inc/shortcodes/admin-button-options.php";
    switch ($_GET['sc']) {
        case 'ContentContainer':
            $req_path = get_template_directory() . "/inc/shortcodes/admin-container-options.php";
            break;
        case 'Button':
            $all_colors = _thrive_get_color_scheme_options("buttons");
            $req_path = get_template_directory() . "/inc/shortcodes/admin-button-options.php";
            break;
        case 'ContentBox':
            $all_colors = _thrive_get_color_scheme_options("contentbox");
            $req_path = get_template_directory() . "/inc/shortcodes/admin-contentbox-options.php";
            break;
        case 'CustomFont':
            $fonts = (array)json_decode(get_option('thrive_font_manager_options'));
            $req_path = get_template_directory() . "/inc/shortcodes/admin-custom-font-options.php";
            break;
        case 'Borderless':
            $req_path = get_template_directory() . "/inc/shortcodes/admin-borderless-options.php";
            break;
        case 'DividerLine':
            $req_path = get_template_directory() . "/inc/shortcodes/admin-divider-options.php";
            break;
        case 'GMaps':
            $req_path = get_template_directory() . "/inc/shortcodes/admin-gmaps-options.php";
            break;
        case 'Tabs':
            $req_path = get_template_directory() . "/inc/shortcodes/admin-tabs-options.php";
            break;
        case 'Toggle':
            $req_path = get_template_directory() . "/inc/shortcodes/admin-toggle-options.php";
            break;
        case 'PageSection':
            //remove the first element which is the 'no pattern' png
            $patterns = _thrive_get_patterns_from_directory();
            array_shift($patterns);
            $req_path = get_template_directory() . "/inc/shortcodes/admin-page-section-options.php";
            break;
        case 'Pullquote':
            $all_colors = array_merge($all_colors, array('notebox' => 'Note', 'greybox' => 'Grey'));
            $req_path = get_template_directory() . "/inc/shortcodes/admin-pullquote-options.php";
            break;
        case 'Optin':
            $all_colors = _thrive_get_color_scheme_options("buttons");
            $queryOptins = new WP_Query("post_type=thrive_optin&order=ASC&post_status=publish&posts_per_page=-1");
            $req_path = get_template_directory() . "/inc/shortcodes/admin-optin-options.php";
            break;
        case 'PostsList':
            $all_categories = get_categories();
            $categories_array = array(array('id' => 0, 'name' => __("All categories", 'thrive')));
            foreach ($all_categories as $cat) {
                array_push($categories_array, array('id' => $cat->cat_ID, 'name' => $cat->cat_name));
            }
            $req_path = get_template_directory() . "/inc/shortcodes/admin-posts-list-options.php";
            break;
        case 'Phone':
            $all_colors = _thrive_get_color_scheme_options();
            $all_colors = array_merge(array('default' => __("Default", 'thrive')), $all_colors);
            $req_path = get_template_directory() . "/inc/shortcodes/admin-phone-options.php";
            break;
        case 'CustomMenu':
            $menu_items = get_terms('nav_menu', array('hide_empty' => false));

            $all_menus = array(array('id' => 0, 'name' => __("All menus", 'thrive')));
            foreach ($menu_items as $menu) {
                array_push($all_menus, array('id' => $menu->term_id, 'name' => $menu->name));
            }
            $req_path = get_template_directory() . "/inc/shortcodes/admin-custom-menu-options.php";
            break;
        case 'PostsGallery':
            $all_categories = get_categories();
            $categories_array = array(array('id' => 0, 'name' => __("All categories", 'thrive')));
            foreach ($all_categories as $cat) {
                array_push($categories_array, array('id' => $cat->cat_ID, 'name' => $cat->cat_name));
            }
            $req_path = get_template_directory() . "/inc/shortcodes/admin-posts-gallery-options.php";
            break;
        case 'VideoSection':
            //remove the first element which is the 'no pattern' png
            $patterns = _thrive_get_patterns_from_directory();
            array_shift($patterns);
            $req_path = get_template_directory() . "/inc/shortcodes/admin-video-section-options.php";
            break;
        case 'Testimonal':
            $req_path = get_template_directory() . "/inc/shortcodes/admin-testimonal-options.php";
            break;
        case 'FollowMe':
            $req_path = get_template_directory() . "/inc/shortcodes/admin-followme-options.php";
            break;
        case 'Grid':
            $img_sizes = array('small' => __("Small", 'thrive'), 'medium' => __("Medium", 'thrive'), 'large' => __("Large", 'thrive'));
            $req_path = get_template_directory() . "/inc/shortcodes/admin-grid-options.php";
            break;
        case 'Price':
            $all_colors = _thrive_get_color_scheme_options("buttons");
            $req_path = get_template_directory() . "/inc/shortcodes/admin-prices-options.php";
            break;
        case 'ResponsiveVideo':
            $req_path = get_template_directory() . "/inc/shortcodes/admin-responsive-video-options.php";
            break;
        case 'Accordion':
            $req_path = get_template_directory() . "/inc/shortcodes/admin-accordion.php";
            break;
        case 'Countdown':
            $all_colors = _thrive_get_color_scheme_options("buttons");
            $req_path = get_template_directory() . "/inc/shortcodes/admin-countdown.php";
            break;
        case 'CustomBox':
            $req_path = get_template_directory() . "/inc/shortcodes/admin-custom-box.php";
            break;
        case 'DropCaps':
            $all_colors = _thrive_get_color_scheme_options("buttons");
            $req_path = get_template_directory() . "/inc/shortcodes/admin-drop-caps-options.php";
            break;
        case 'FillCounter':
            $all_colors = _thrive_get_color_scheme_options("buttons");
            $req_path = get_template_directory() . "/inc/shortcodes/admin-fill-counter-options.php";
            break;
        case 'HeadlineFocus':
            $req_path = get_template_directory() . "/inc/shortcodes/admin-headline-focus-options.php";
            break;
        case 'NumberCounter':
            $all_colors = _thrive_get_color_scheme_options("buttons");
            $req_path = get_template_directory() . "/inc/shortcodes/admin-number-counter-options.php";
            break;
        case 'ProgressBar':
            $all_colors = _thrive_get_color_scheme_options("buttons");
            $req_path = get_template_directory() . "/inc/shortcodes/admin-progress-bar-options.php";
            break;
        case 'SplitButton':
            $all_colors = _thrive_get_color_scheme_options("buttons");
            $req_path = get_template_directory() . "/inc/shortcodes/admin-split-button-options.php";
            break;
        case 'Highlight':
            $all_colors = _thrive_get_color_scheme_options("buttons");
            $req_path = get_template_directory() . "/inc/shortcodes/admin-highlight-options.php";
            break;
        case 'IconBox':
            $all_colors = _thrive_get_color_scheme_options("theme");
            $req_path = get_template_directory() . "/inc/shortcodes/admin-icon-box-options.php";
            break;
        case 'MegaButton':
            $all_colors = _thrive_get_color_scheme_options("buttons");
            $req_path = get_template_directory() . "/inc/shortcodes/admin-mega-button-options.php";
            break;
        case 'LessonsList': //apprentice shortcode
            $all_categories = get_terms("apprentice", 'hide_empty=0');
            $categories_array = array(array('id' => 0, 'name' => __("All categories", 'thrive')));
            foreach ($all_categories as $cat) {
                array_push($categories_array, array('id' => $cat->term_id, 'name' => $cat->name));
            }
            $all_users = get_users(array('fields' => array('ID', 'user_nicename', 'display_name')));
            $req_path = get_template_directory() . "/inc/apprentice/shortcodes/admin-lessons-list-options.php";
            break;
        case 'LessonsGallery': //apprentice shortcode
            $all_categories = get_terms("apprentice", 'hide_empty=0');
            $categories_array = array(array('id' => 0, 'name' => __("All categories", 'thrive')));
            foreach ($all_categories as $cat) {
                array_push($categories_array, array('id' => $cat->term_id, 'name' => $cat->name));
            }
            $all_users = get_users(array('fields' => array('ID', 'user_nicename', 'display_name')));
            $req_path = get_template_directory() . "/inc/apprentice/shortcodes/admin-lessons-gallery-options.php";
            break;
        case 'WelcomeBack': //apprentice shortcode
            $all_colors = _thrive_get_color_scheme_options("contentbox");
            $req_path = get_template_directory() . "/inc/apprentice/shortcodes/admin-welcome-back-options.php";
            break;
    }
    require($req_path);
    die;
}

// add more buttons to the html editor
function thrive_add_quicktags()
{
    //enable the buttons only on the required pages
    global $post;

    global $isTvePage;

    if (!$isTvePage) {
        $screen = get_current_screen();
        if ((!$post || ($screen->base != "post")) && ($screen->id != "toplevel_page_thrive_admin_options")) {
            return;
        }
    }

    if (wp_script_is('quicktags') || (isset($screen) && $screen->base == "post")) {
        ?>
        <style>

        </style>
        <script type="text/javascript">

            var ThriveHandleAddQTagBtn = function (shortcodes_list, button) {
                var menu_container_id = "thrive-qtags-sc-submenu1";
                if (jQuery("#thrive-qtags-sc-submenu1").length === 0) {
                    /*
                     * RENDER THE HTML FOR THE SUBMENU
                     */
                    var sc_submenu_html = '<div class="thrive-sc-submenu mce-styled" id="' + menu_container_id + '" style="display: none;"> <ul>',
                        toolbar = jQuery("#ed_toolbar");
                    toolbar = toolbar.length ? toolbar : jQuery('.quicktags-toolbar');
                    for (var i = 0; i < shortcodes_list.length; i++) {
                        sc_submenu_html += '<li data-shortcode="' + shortcodes_list[i] + '" class="thrive-sc-submenu-item ' + 'mce-' + shortcodes_list[i].toLowerCase() + ' ">' + shortcodes_list[i].replace(/([a-z])([A-Z])/g, '$1 $2') + '</li>';
                    }
                    sc_submenu_html += '</ul></div>';
                    toolbar.append(sc_submenu_html).css('position', 'relative');
                    /*
                     * BIND THE ACTIONS FOR THE SUBMENU ITEMS
                     */
                    jQuery('.thrive-sc-submenu-item').click(function () {
                        ThriveHandleAddShortcote(jQuery(this).attr('data-shortcode'));
                        jQuery('.thrive-sc-submenu').hide();
                    });
                }

                /*
                 * unbind the clicks from shortcode titles
                 * */

                var unbind_clicks = function () {
                    jQuery('.mce-textandlayout, .mce-conversion, .mce-numbersanddata, .mce-lists, .mce-contentreveal, .mce-other').unbind('click').on('click', function () {
                        return false;
                    });
                }
                unbind_clicks();

                /*
                 * POSITION THE SUBMENU AND TOGGLE THE DISPLAY
                 */
                var pos = jQuery(button).position();

                if (pos) {
                    jQuery("#thrive-qtags-sc-submenu1").css({
                        position: "absolute",
                        top: (pos.top + 25) + "px",
                        left: pos.left + "px"
                    });
                }
                jQuery("#" + menu_container_id).toggle();
                jQuery("#thrive-qtags-sc-submenu2").hide();

            };

            var ThriveHandleAddQTagScBtn = function (shortcodes_list, button) {
                if (jQuery("#thrive-qtags-sc-submenu2").length == 0) {
                    var sc_col_menu_html = '<div class="thrive_shortcodesAdmin" id="thrive-qtags-sc-submenu2" style="display: none;">' +
                            '<p>Halves</p>' +
                            '<div class="colm twc" id="Columns-1-2">1/2</div>' +
                            '<div class="colm twc lst" id="Columns-1-2">1/2</div>' +
                            '<p>Thirds</p>' +
                            '<div class="colm thc" id="Columns-1-3">1/3</div>' +
                            '<div class="colm thc" id="Columns-1-3">1/3</div>' +
                            '<div class="colm thc lst" id="Columns-1-3">1/3</div>' +
                            '<div class="colm tth" id="Columns-2-3-1">2/3</div>' +
                            '<div class="colm lst oth" id="Columns-2-3-1">1/3</div>' +
                            '<div class="colm oth" id="Columns-2-3-1">1/3</div>' +
                            '<div class="colm tth lst" id="Columns-2-3-1">2/3</div>' +
                            '<p>Fourths</p>' +
                            '<div class="colm foc" id="Columns-1-4">1/4</div>' +
                            '<div class="colm foc" id="Columns-1-4">1/4</div>' +
                            '<div class="colm foc" id="Columns-1-4">1/4</div>' +
                            '<div class="colm foc lst" id="Columns-1-4">1/4</div>' +
                            '<div class="colm ofo" id="Columns-3-4-1">1/4</div>' +
                            '<div class="colm lst tfo" id="Columns-3-4-1">3/4</div>' +
                            '<div class="colm tfo" id="Columns-4-3-1">3/4</div>' +
                            '<div class="colm lst ofo" id="Columns-4-3-1">1/4</div>' +
                            '<div class="colm ofo" id="Columns-4-2-1">1/4</div>' +
                            '<div class="colm ofo" id="Columns-4-2-1">1/4</div>' +
                            '<div class="colm twc lst" id="Columns-4-2-1">1/2</div>' +
                            '<div class="colm ofo" id="Columns-2-4-1">1/4</div>' +
                            '<div class="colm twc" id="Columns-2-4-1">1/2</div>' +
                            '<div class="colm ofo lst" id="Columns-2-4-1">1/4</div>' +
                            '<div class="colm twc" id="Columns-4-1-2">1/2</div>' +
                            '<div class="colm ofo" id="Columns-4-1-2">1/4</div>' +
                            '<div class="colm ofo lst" id="Columns-4-1-2">1/4</div>' +
                            '</div>',
                        toolbar = jQuery("#ed_toolbar");

                    toolbar = toolbar.length ? toolbar : jQuery('.quicktags-toolbar');

                    toolbar.append(sc_col_menu_html).css('position', 'relative');

                    jQuery("#thrive-qtags-sc-submenu2 .colm").click(function () {
                        ThriveHandleAddShortcote(jQuery(this).attr('id'));
                        jQuery('.thrive_shortcodesAdmin').hide();
                    });
                }
                /*
                 * POSITION THE SUBMENU AND TOGGLE THE DISPLAY
                 */
                var pos = jQuery(button).position();
                if (pos) {
                    jQuery("#thrive-qtags-sc-submenu2").css({
                        position: "absolute",
                        top: (pos.top + 25) + "px",
                        left: pos.left + "px"
                    });
                }

                jQuery("#thrive-qtags-sc-submenu2").toggle();

                jQuery("#thrive-qtags-sc-submenu1").hide();

            };
            if (typeof QTags !== 'undefined' && window.QTags && window.QTags.addButton) {
                QTags.addButton('thrive_shortcode', "Shortcodes", function (button) {
                    ThriveHandleAddQTagBtn(thrive_shortcodes, button);
                });

                QTags.addButton('thrive_shortcode2', "Shortcodes2", function (button) {
                    ThriveHandleAddQTagScBtn(thrive_shortcodes2, button);
                });
            }

        </script>
        <?php
    }
}

add_action('admin_print_footer_scripts', 'thrive_add_quicktags');

function _thrive_get_vimeo_embed_code($url, $width = "100%")
{

    if (empty($url)) {
        return "";
    }

    $video_id = (int)substr(parse_url($url, PHP_URL_PATH), 1);
    $rand_id = "player" . rand(1, 1000);

    $src_url = "//player.vimeo.com/video/" . $video_id;

    if ($width != "100%") {
        if ($width > 1060) {
            $width = 1060;
        }
        $video_height = ($width * 9) / 16;
    } else {
        $video_height = "400";
    }

    $embed_code = "<iframe id='" . $rand_id . "' src='" . $src_url . "' height='" . $video_height . "' width='" . $width . "' frameborder='0' webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>";

    return $embed_code;
}

function _thrive_get_youtube_embed_code($url, $attr = array())
{

    if (empty($url)) {
        return "";
    }

    $url_params = array();
    $rand_id = "player" . rand(1, 1000);
    parse_str(parse_url($url, PHP_URL_QUERY), $url_params);

    $video_id = (isset($url_params['v'])) ? $url_params['v'] : 0;

    $src_url = "//www.youtube.com/embed/" . $video_id . "?not_used=1";

    if (isset($attr['hide_related']) && ($attr['hide_related'] == 1 || $attr['hide_related'] === "true")) {
        $src_url .= "&rel=0";
    }
    if (isset($attr['hide_logo']) && ($attr['hide_logo'] == 1 || $attr['hide_logo'] === "true")) {
        $src_url .= "&modestbranding=1";
    }
    if (isset($attr['hide_controls']) && ($attr['hide_controls'] == 1 || $attr['hide_controls'] === "true")) {
        $src_url .= "&controls=0";
    }
    if (isset($attr['hide_title']) && ($attr['hide_title'] == 1 || $attr['hide_title'] === "true")) {
        $src_url .= "&showinfo=0";
    }
    $hide_fullscreen = "allowfullscreen";
    if (isset($attr['hide_fullscreen']) && ($attr['hide_fullscreen'] == 1 || $attr['hide_fullscreen'] === "true")) {
        $src_url .= "&fs=0";
    }
    if (isset($attr['autoplay']) && ($attr['autoplay'] == 1 || $attr['autoplay'] === "true")) {
        $src_url .= "&autoplay=1";
    }
    if (!isset($attr['video_width'])) {
        $attr['video_width'] = "100%";
        $attr['video_height'] = 400;
    } else {
        if ($attr['video_width'] > 1080) {
            $attr['video_width'] = 1080;
        }
        $attr['video_height'] = ($attr['video_width'] * 9) / 16;
    }

    $embed_code = "<iframe id='" . $rand_id . "' src='" . $src_url . "' height='" . $attr['video_height'] . "' width='" . $attr['video_width'] . "' frameborder='0' " . $hide_fullscreen . " ></iframe>";

    return $embed_code;
}

function _thrive_generate_price_shortcode_output($attr, $content, $type, $position)
{
    $output = "";
    if ($position == "first") {
        $output .= "<div class='row'>";
    }

    switch ($type) {
        case "price_one":
            $output .= "<div class='pt_one pt'>";
            break;
        case "price_one_half":
            $output .= "<div class='pt_two pt'>";
            break;
        case "price_one_third":
            $output .= "<div class='pt_three pt'>";
            break;
        case "price_one_fourth":
            $output .= "<div class='pt_four pt'>";
            break;
        case "price_one_fifth":
            $output .= "<div class='pt_five pt'>";
            break;
        default:
            $output .= "<div class='pt_one pt'>";
    }
    $in_div_class = (!empty($attr['highlight'])) ? "pt_in pt_hgh" : "pt_in";

    $output .= "<div class='" . $in_div_class . "'><h3 class='ctr'>" . $attr['title'] . "</h3><ul class='check'>";

    $items = explode("[*]", $content);

    foreach ($items as $item) {
        $output .= "<li>" . $item . "</li>";
    }

    $output .= "<p class='prc ctr'>" . $attr['price'];
    if (!empty($attr['time'])) {
        $output .= "<span>/" . $attr['time'] . "</span>";
    }
    $output .= "</p>";

    $output .= "<div class='prb'><a href='" . $attr['btn_link'] . "' class='btn " . $attr['btn_size'] . " " . $attr['btn_color'] . "'><span class='upp'>" . $attr['btn_text'] . "</span></a></div>";
    $output .= "</div></div>";

    if ($position == "last" || $type == "price_one") {
        $output .= "</div>";
    }

    return $output;
}

function _thrive_get_grid_shortcode_size_class($size)
{
    $size_class = "gr3";
    if ($size == "small") {
        $size_class = "gr1";
    } else if ($size == "medium") {
        $size_class = "gr2";
    }
    return $size_class;
}

function _thrive_get_content_for_columns_sc($content)
{
    $out_content = $content;
    if (strpos($content, "[") === 0) {
        $out_content = do_shortcode($content);
    } else {
        $out_content = "<p>" . do_shortcode($content) . "</p>";
    }
    return $out_content;
}

global $wp_version;

if (version_compare($wp_version, "3.9") >= 0) {
    add_action('admin_enqueue_scripts', 'thrivev2_add_my_tc_button');
} else {
    add_action('admin_enqueue_scripts', 'thrive_add_tinymce');
}
global $isTvePage;
$isTvePage = false;
if (isset($_REQUEST['tve']) && $_REQUEST['tve'] == "true") {
    $isTvePage = true;
    add_action('wp_head', 'thrive_print_shortcodes');
    add_action('wp_print_footer_scripts', "thrive_add_quicktags");
    add_action('wp_enqueue_scripts', 'thrive_enqueue_scripts_for_tve');
    if (version_compare($wp_version, "3.9") >= 0) {
        add_action('wp_enqueue_scripts', 'thrivev2_add_my_tc_button');
    } else {
        add_action('wp_enqueue_scripts', 'thrive_add_tinymce');
    }
}

function thrive_enqueue_scripts_for_tve()
{
    wp_register_script('thrive-admin-postedit', get_template_directory_uri() . '/inc/js/post-edit.js', array('jquery'));
    wp_enqueue_script("thrive-admin-postedit");
    wp_enqueue_script("thickbox");
    wp_enqueue_style("thickbox");
    wp_register_style('thrive-admin-colors', get_template_directory_uri() . '/inc/css/thrive_admin_colours.css');
    wp_enqueue_style("thrive-admin-colors");

    //datetime picker
    wp_enqueue_script('thrive-admin-datetime-picker', get_template_directory_uri() . '/inc/js/jquery-ui-timepicker.js', array('jquery-ui-datepicker', 'jquery-ui-slider'));
    /* TODO: fix widgets-options.js - do not overwrite the send_to_editor function - it will cause conflicts
    wp_enqueue_script("jquery-ui-autocomplete");
    wp_enqueue_style("jquery-ui-autocomplete");
    wp_enqueue_script('thrive-widgets-options', get_template_directory_uri() . '/inc/js/widgets-options.js', array('jquery', 'media-upload', 'thickbox', 'jquery-ui-autocomplete'));

    //prepare the javascript params
    $getUsersWpnonce = wp_create_nonce("thrive_helper_get_users");
    $getUsersUrl = admin_url('admin-ajax.php?action=thrive_helper_get_users&nonce=' . $getUsersWpnonce);

    $js_params_array = array('getUsersUrl' => $getUsersUrl,
        'noonce' => $getUsersWpnonce);
    wp_localize_script('thrive-widgets-options', 'ThriveWidgetsOptions', $js_params_array); */
}

function thrivev2_add_my_tc_button($hook)
{
    global $isTvePage;
    if (!$isTvePage) {
        $screen = get_current_screen();
        if (('post.php' != $hook && 'post-new.php' != $hook) && $screen->id != "toplevel_page_thrive_admin_options")
            return;
        // check user permissions
        if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) {
            return;
        }
    }
    // check if WYSIWYG is enabled
    if (get_user_option('rich_editing') == 'true') {
        add_filter("mce_external_plugins", "thrivev2_add_tinymce_plugin");
        add_filter('mce_buttons', 'thrivev2_register_my_tc_button');
    }
}

function thrivev2_add_tinymce_plugin($plugin_array)
{
    $plugin_array['thrivev2_tc_button'] = get_template_directory_uri() . "/inc/js/tinymce_thrive_pluginv2.js";
    $plugin_array['thrivev2_tc_button2'] = get_template_directory_uri() . "/inc/js/tinymce_thrive_plugin2v2.js";
    return $plugin_array;
}

function thrivev2_register_my_tc_button($buttons)
{
    array_push($buttons, "thrivev2_tc_button");
    array_push($buttons, "thrivev2_tc_button2");
    return $buttons;
}

function thrive_add_tinymce($hook)
{
    $screen = get_current_screen();
    global $isTvePage;
    if (('post.php' != $hook && 'post-new.php' != $hook) && $screen->id != "toplevel_page_thrive_admin_options" && !$isTvePage)
        return;

    add_filter('mce_external_plugins', 'thrive_add_tinymce_plugin');
    add_filter('mce_buttons', 'thrive_add_tinymce_button');
}

function thrive_add_tinymce_plugin($plugin_array)
{
    $plugin_array['thriveShortcodes'] = get_template_directory_uri() . "/inc/js/tinymce_thrive_plugin.js";
    $plugin_array['thriveShortcodes2'] = get_template_directory_uri() . "/inc/js/tinymce_thrive_plugin2.js";
    return $plugin_array;
}

function thrive_add_tinymce_button($buttons)
{
    array_push($buttons, 'thriveShortcodes');
    array_push($buttons, 'thriveShortcodes2');
    return $buttons;
}
