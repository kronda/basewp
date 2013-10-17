<?php
/*
Plugin Name: Ultimate Coming Soon Page
Plugin URI: http://www.seedprod.com
Description: Creates a Coming Soon or Launch page for your website.
Version: 1.10.0
Author: SeedProd
Author URI: http://www.seedprod.com
License: GPLv2
Copyright 2011  John Turner (email : john@seedprod.com, twitter : @johnturner)
*/

/**
 * Init
 *
 * @package WordPress
 * @subpackage Ultimate_Coming_Soon_Page
 * @since 0.1
 */

/**
 * Require config to get our initial values
 */
 
load_plugin_textdomain('ultimate-coming-soon-page',false, dirname( plugin_basename( __FILE__ ) ) . '/languages/');

require_once('framework/framework.php');
require_once('inc/config.php');

/**
 * Upon activation of the plugin, see if we are running the required version and deploy theme in defined.
 *
 * @since 0.1
 */
function seedprod_ucsp_activation() {
    if ( version_compare( get_bloginfo( 'version' ), '3.0', '<' ) ) {
        deactivate_plugins( __FILE__  );
        wp_die( __('WordPress 3.0 and higher required. The plugin has now disabled itself. On a side note why are you running an old version :( Upgrade!','ultimate-coming-soon-page') );
    }
}
