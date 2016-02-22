<?php
/*
  Plugin Name: Toolset Types
  Plugin URI: http://wordpress.org/extend/plugins/types/
  Description: Toolset Types defines custom content in WordPress. Easily create custom post types, fields and taxonomy and connect everything together.
  Author: OnTheGoSystems
  Author URI: http://www.onthegosystems.com
  Version: 1.9
 */
/**
 *
 *
 */
// Added check because of activation hook and theme embedded code
if ( !defined( 'WPCF_VERSION' ) ) {
    /**
     * make sure that WPCF_VERSION in embedded/bootstrap.php is the same!
     */
    define( 'WPCF_VERSION', '1.9' );
}

define( 'WPCF_REPOSITORY', 'http://api.wp-types.com/' );

define( 'WPCF_ABSPATH', dirname( __FILE__ ) );
define( 'WPCF_RELPATH', plugins_url() . '/' . basename( WPCF_ABSPATH ) );
define( 'WPCF_INC_ABSPATH', WPCF_ABSPATH . '/includes' );
define( 'WPCF_INC_RELPATH', WPCF_RELPATH . '/includes' );
define( 'WPCF_RES_ABSPATH', WPCF_ABSPATH . '/resources' );
define( 'WPCF_RES_RELPATH', WPCF_RELPATH . '/resources' );

// Add installer
$installer = dirname( __FILE__ ) . '/plus/installer/loader.php';
if ( file_exists($installer) ) {
    include_once $installer;
    if ( function_exists('WP_Installer_Setup') ) {
        WP_Installer_Setup(
            $wp_installer_instance,
            array(
                'plugins_install_tab' => '1',
                'repositories_include' => array('toolset', 'wpml')
            )
        );
    }
}

require_once WPCF_INC_ABSPATH . '/constants.php';
/*
 * Since Types 1.2 we load all embedded code without conflicts
 */
require_once WPCF_ABSPATH . '/embedded/types.php';

require_once WPCF_ABSPATH . '/embedded/toolset/onthego-resources/loader.php';
onthego_initialize(WPCF_ABSPATH . '/embedded/toolset/onthego-resources/',
                                   WPCF_RELPATH . '/embedded/toolset/onthego-resources/' );

// Plugin mode only hooks
add_action( 'plugins_loaded', 'wpcf_init' );

// init hook for module manager
add_action( 'init', 'wpcf_wp_init' );

register_deactivation_hook( __FILE__, 'wpcf_deactivation_hook' );
register_activation_hook( __FILE__, 'wpcf_activation_hook' );


add_action( 'after_setup_theme', 'wpcf_initialize_autoloader_full', 20 );

/**
 * Configure autoloader also for full Types (it has been loaded by embedded Types by now).
 */
function wpcf_initialize_autoloader_full() {
	WPCF_Autoloader::get_instance()->add_path( WPCF_INC_ABSPATH . '/classes' );
}

/**
 * Deactivation hook.
 *
 * Reset some of data.
 */
function wpcf_deactivation_hook()
{
    // Delete messages
    delete_option( 'wpcf-messages' );
    delete_option( 'WPCF_VERSION' );
    /**
     * check site kind and if do not exist, delete types_show_on_activate
     */
    if ( !get_option('types-site-kind') ) {
        delete_option('types_show_on_activate');
    }
}

/**
 * Activation hook.
 *
 * Reset some of data.
 */
function wpcf_activation_hook()
{
    $version = get_option('WPCF_VERSION');
    if ( empty($version) ) {
        $version = 0;
        add_option('WPCF_VERSION', 0, null, 'no');
    }
    if ( version_compare($version, WPCF_VERSION) < 0 ) {
        update_option('WPCF_VERSION', WPCF_VERSION);
    }
    if( 0 == version_compare(WPCF_VERSION, '1.6.5')) {
        add_option('types_show_on_activate', 'show', null, 'no');
        if ( get_option('types-site-kind') ) {
            update_option('types_show_on_activate', 'hide');
        }
    }
}

/**
 * Main init hook.
 */
function wpcf_init()
{
    if ( !defined( 'EDITOR_ADDON_RELPATH' ) ) {
        define( 'EDITOR_ADDON_RELPATH', WPCF_RELPATH . '/embedded/toolset/toolset-common/visual-editor' );
    }

    if ( is_admin() ) {
        require_once WPCF_ABSPATH . '/admin.php';
    }
    /**
     * remove unused option
     */
    $version_from_db = get_option('wpcf-version', 0);
    if ( version_compare(WPCF_VERSION, $version_from_db) > 0 ) {
        delete_option('wpcf-survey-2014-09');
        update_option('wpcf-version', WPCF_VERSION);
    }
}

//Render Installer packages
function installer_content()
{
    echo '<div class="wrap">';
    $config['repository'] = array(); // required
    WP_Installer_Show_Products($config);
    echo "</div>";
}

/**
 * WP Main init hook.
 */
function wpcf_wp_init()
{
    if ( is_admin() ) {
        require_once WPCF_ABSPATH . '/admin.php';
        add_action('wpcf_menu_plus', 'setup_installer');
        //Add submenu Installer to Types
        function setup_installer()
        {
            if (
                isset( $_GET['page'] ) 
                && 'installer' == $_GET['page']
            ) {
                wpcf_admin_add_submenu_page(
                    array(
                        'menu_title' => __('Installer', 'wpcf'),
                        'menu_slug' => 'installer',
                        'function' => 'installer_content'
                    )
                );
            }
        }
    }
}



function ajax_wpcf_is_reserved_name() {

    // slug
    $name = isset( $_POST['slug'] )
        ? $_POST['slug']
        : '';

    // context
    $context = isset( $_POST['context'] )
        ? $_POST['context']
        : false;

    // check also page slugs
    $check_pages = isset( $_POST['check_pages'] ) && $_POST['check_pages'] == false
        ? false
        : true;

    // slug pre save
    if( isset( $_POST['slugPreSave'] )
        && $_POST['slugPreSave'] !== 0 ) {

        // for taxonomy
        if( $context == 'taxonomy' )
            $_POST['ct']['wpcf-tax'] = $_POST['slugPreSave'];

        // for post_type
        if( $context == 'post_type' )
            $_POST['ct']['wpcf-post-type'] = $_POST['slugPreSave'];
    }

    if( $context == 'post_type' || $context == 'taxonomy' ) {
        $used_reserved = wpcf_is_reserved_name( $name, $context, $check_pages );

        if( $used_reserved ) {
            die( json_encode( array( 'already_in_use' => 1 ) ) );
        }
    }

    // die( json_encode( $_POST ) );
    die( json_encode( array( 'already_in_use' => 0 ) ) );
}

add_action( 'wp_ajax_wpcf_get_forbidden_names', 'ajax_wpcf_is_reserved_name' );

/**
 * Checks if name is reserved.
 *
 * @param type $name
 * @return type
 */
function wpcf_is_reserved_name($name, $context, $check_pages = true)
{
    $name = strval( $name );
    /*
     *
     * If name is empty string skip page cause there might be some pages without name
     */
    if ( $check_pages && !empty( $name ) ) {
        global $wpdb;
        $page = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_type='page'",
                sanitize_title( $name )
            )
        );
        if ( !empty( $page ) ) {
            return new WP_Error( 'wpcf_reserved_name', __( 'You cannot use this slug because there is already a page by that name. Please choose a different slug.',
                                    'wpcf' ) );
        }
    }

    // Add custom types
    $custom_types = get_option(WPCF_OPTION_NAME_CUSTOM_TYPES, array() );
    $post_types = get_post_types();
    if ( !empty( $custom_types ) ) {
        $custom_types = array_keys( $custom_types );
        $post_types = array_merge( array_combine( $custom_types, $custom_types ),
                $post_types );
    }
    // Unset to avoid checking itself
    /* Note: This will unset any post type with the same slug, so it's possible to overwrite it
    if ( $context == 'post_type' && isset( $post_types[$name] ) ) {
        unset( $post_types[$name] );
    }
    */
    // abort test...
    if( $context == 'post_type' // ... for post type ...
        && isset( $_POST['ct']['wpcf-post-type'] ) // ... if it's an already saved taxonomy ...
        && $_POST['ct']['wpcf-post-type'] == $name // ... and the slug didn't changed.
    ) {
        return false;
    }

    // Add taxonomies
    $custom_taxonomies = (array) get_option( WPCF_OPTION_NAME_CUSTOM_TAXONOMIES, array() );
    $taxonomies = get_taxonomies();
    if ( !empty( $custom_taxonomies ) ) {
        $custom_taxonomies = array_keys( $custom_taxonomies );
        $taxonomies = array_merge( array_combine( $custom_taxonomies,
                        $custom_taxonomies ), $taxonomies );
    }

    // Unset to avoid checking itself
    /* Note: This will unset any taxonomy with the same slug, so it's possible to overwrite it
    if ( $context == 'taxonomy' && isset( $taxonomies[$name] ) ) {
        unset( $taxonomies[$name] );
    }
    */

    // abort test...
    if( $context == 'taxonomy' // ... for taxonomy ...
        && isset( $_POST['ct']['wpcf-tax'] ) // ... if it's an already saved taxonomy ...
        && $_POST['ct']['wpcf-tax'] == $name // ... and the slug didn't changed.
    ) {
        return false;
    }

    $reserved_names = wpcf_reserved_names();
    $reserved = array_merge( array_combine( $reserved_names, $reserved_names ),
            array_merge( $post_types, $taxonomies ) );

    return in_array( $name, $reserved ) ? new WP_Error( 'wpcf_reserved_name', __( 'You cannot use this slug because it is a reserved word, used by WordPress. Please choose a different slug.',
                            'wpcf' ) ) : false;
}

/**
 * Reserved names.
 *
 * @return type
 */
function wpcf_reserved_names()
{
    $reserved = array(
        'action',
        'attachment',
        'attachment_id',
        'author',
        'author_name',
        'calendar',
        'cat',
        'category',
        'category__and',
        'category__in',
        'category_name',
        'category__not_in',
        'comments_per_page',
        'comments_popup',
        'cpage',
        'day',
        'debug',
        'error',
        'exact',
        'feed',
        'field',
        'fields',
        'format',
        'hour',
        'lang',
        'link_category',
        'm',
        'minute',
        'mode',
        'monthnum',
        'more',
        'name',
        'nav_menu',
        'nopaging',
        'offset',
        'order',
        'orderby',
        'p',
        'page',
        'paged',
        'page_id',
        'pagename',
        'parent',
        'pb',
        'perm',
        'post',
        'post_format',
        'post__in',
        'post_mime_type',
        'post__not_in',
        'posts',
        'posts_per_archive_page',
        'posts_per_page',
        'post_status',
        'post_tag',
        'post_type',
        'preview',
        'robots',
        's',
        'search',
        'second',
        'sentence',
        'showposts',
        'static',
        'subpost',
        'subpost_id',
        'tag',
        'tag__and',
        'tag_id',
        'tag__in',
        'tag__not_in',
        'tag_slug__and',
        'tag_slug__in',
        'taxonomy',
        'tb',
        'term',
        'type',
        'w',
        'withcomments',
        'withoutcomments',
        'year',
    );

    return apply_filters( 'wpcf_reserved_names', $reserved );
}

add_action( 'icl_pro_translation_saved', 'wpcf_fix_translated_post_relationships' );

function wpcf_fix_translated_post_relationships($post_id)
{
    require_once WPCF_EMBEDDED_ABSPATH . '/includes/post-relationship.php';
    wpcf_post_relationship_set_translated_parent( $post_id );
    wpcf_post_relationship_set_translated_children( $post_id );
}

// this is for testing promotional message
// set WPCF_PAYED true in your wp-config
if ( !defined( 'WPCF_PAYED' ) )
    define( 'WPCF_PAYED', true );

if( ! function_exists( 'wpcf_is_client' ) ) {
    /**
     * Check if user is a client, who bought Toolset
     * @return bool
     */
    function wpcf_is_client() {

        // for testing
        if( ! WPCF_PAYED )
            return false;

        // check db stored value
        if( get_option( 'wpcf-is-client' ) ) {
            $settings = wpcf_get_settings( 'help_box' );

            // prioritise settings if available
            if( $settings ) {
                switch( $settings ) {
                    case 'by_types':
                    case 'all':
                        return false;
                    case 'no':
                        return true;
                }
            }

            $is_client = get_option( 'wpcf-is-client' );

            // client
            if( $is_client === 'yes' )
                return true;

            // user
            return false;
        }

        // no db stored value
        // make sure get_plugins() is available
        if ( ! function_exists( 'get_plugins' ) )
            require_once ABSPATH . 'wp-admin/includes/plugin.php';

        // all plugins
        $plugins = get_plugins();

        // check each plugin
        foreach( $plugins as $plugin ) {
            // skip plugin that is not created by us
            if( $plugin['Author'] != 'OnTheGoSystems' )
                continue;

            // check for toolset plugin and not embedded = user bought toolset
            if( preg_match( "#(access|cred|layouts|module manager|views)#i", $plugin['Name'] )
                && ! preg_match( '#embedded#i', $plugin['Name'] ) ) {
                add_option( 'wpcf-is-client', 'yes' );

                // set settings "help box" ounce to none
                $settings = get_option( 'wpcf_settings', array() );
                $settings['help_box'] = 'no';
                update_option( 'wpcf_settings', $settings );

                return true;
            }
        }

        // if script comes to this point we have no option "wpcf-is-client" set
        // and also no bought toolset plugin
        add_option( 'wpcf-is-client', 'no' );
        return false;
    }
}

/**
 * On plugin activation clear option "wpcf-is-client"
 */
if( ! function_exists( 'wpcf_clear_option_is_client' ) ) {
    function wpcf_clear_option_is_client() {
        $option_is_client = get_option( 'wpcf-is-client' );
        if( $option_is_client == 'no' ) {
            delete_option( 'wpcf-is-client' );
        }

    }
}

add_action( 'activated_plugin', 'wpcf_clear_option_is_client' );


/**
 * Make sure in built taxonomies are stored
 */
$stored_taxonomies = get_option( WPCF_OPTION_NAME_CUSTOM_TAXONOMIES, array() );

if( empty( $stored_taxonomies ) || !isset( $stored_taxonomies['category'] ) || !isset( $stored_taxonomies['post_tag'] ) ) {
    require_once WPCF_ABSPATH . '/embedded/classes/utils.php';
    $taxonomies = WPCF_Utils::object_to_array_deep( get_taxonomies( array( 'public' => true, '_builtin' => true ), 'objects' ) );

    if( isset( $taxonomies['post_format'] ) )
        unset( $taxonomies['post_format'] );

    foreach( $taxonomies as $slug => $settings ) {
        if( isset( $stored_taxonomies[$slug] ) )
            continue;

        $taxonomies[$slug]['slug'] = $slug;
        foreach( $settings['object_type'] as $support ) {
            $taxonomies[$slug]['supports'][$support] = 1;
        }

        $stored_taxonomies[$slug] = $taxonomies[$slug];
    }

    update_option( WPCF_OPTION_NAME_CUSTOM_TAXONOMIES, $stored_taxonomies );
}