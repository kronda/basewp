<?php

/**
*
* This file is responsible for loading the latest version of the Toolset Common lbraries.
*
* To use it in a plugin or theme you should include this file early in the
* plugin loader and then call the toolset_common_initialize function.
* The toolset_common_initialize should be passed the file path to the directory
* where this file is located and also the url to this directory.
*
* 
*
* -----------------------------------------------------------------------
*
* This version number should always be incremented by 1 whenever a change
* is made to the toolset-common code.
* The version number will then be used to work out which plugin has the latest
* version of the code.
*
* The version number will have a format of XXYYYY
* where XX is the future target Toolset Common version number, built upon the stable released one stated in changelog.txt plus 1
* and YYYY is incremented by 1 on each change to the Toolset Common repo
* so we allow up to 10000 changes per dev cycle.
* 
*/
/**
 * Now that we have a unique version for all plugins
 * we define the version here
 */
$toolset_common_version = 222001;


// ----------------------------------------------------------------------//
// WARNING * WARNING * WARNING
// ----------------------------------------------------------------------//

// Don't modify or add to this code.
// This is only responsible for making sure the latest version of common is loaded.

global $toolset_common_paths;

if ( ! isset( $toolset_common_paths ) ) {
    $toolset_common_paths = array();
}

if ( ! isset( $toolset_common_paths[ $toolset_common_version ] ) ) {
    // Save the path to this version.
    $toolset_common_paths[ $toolset_common_version ]['path'] = str_replace( '\\', '/', dirname( __FILE__ ) );
}

if ( ! function_exists( 'toolset_common_plugins_loaded' ) ) {
    function toolset_common_plugins_loaded() {
        global $toolset_common_paths;

        // find the latest version
        $latest = 0;
        foreach ( $toolset_common_paths as $key => $data ) {
            if ( $key > $latest ) {
                $latest = $key;
            }
        }
        if ( $latest > 0 ) {
            require_once $toolset_common_paths[ $latest ]['path'] . '/toolset-common-loader.php';
            toolset_common_set_constants_and_start( $toolset_common_paths[ $latest ]['url'] );
        }
    }
    add_action( 'plugins_loaded', 'toolset_common_plugins_loaded', -1 );
}

if ( ! function_exists( 'toolset_common_initialize' ) ) {

    function toolset_common_initialize( $path, $url ) {
        global $toolset_common_paths;

        $path = str_replace( '\\', '/', $path );
        
        if ( substr( $path, strlen( $path ) - 1 ) == '/' ) {
            $path = substr( $path, 0, strlen( $path ) - 1 );
        }
        
        // Save the url in the matching path
        foreach ( $toolset_common_paths as $key => $data ) {
            if ( $toolset_common_paths[ $key ]['path'] == $path ) {
                $toolset_common_paths[ $key ]['url'] = $url;
                break;
            }
        }
    }
}

