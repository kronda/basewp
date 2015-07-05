<?php

/**
 * Cleans up data created by this plugin
 * @package Code_Snippets
 * @since 2.0
 */

/* Ensure this plugin is actually being uninstalled */
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

/**
 * Clean up data created by this plugin for a single site
 * @since 2.0
 */
function code_snippets_uninstall_site() {
	global $wpdb;

	/* Remove snippets database table */
	$wpdb->query( "DROP TABLE IF EXISTS $wpdb->snippets" );

	/* Remove saved options */
	delete_option( 'code_snippets_version' );
	delete_option( 'recently_activated_snippets' );
	delete_option( 'code_snippets_settings' );

	/* Deregister capabilities */
	$role = get_role( apply_filters( 'code_snippets_role', 'administrator' ) );
	$role->remove_cap( apply_filters( 'code_snippets_cap', 'manage_snippets' ) );
}


global $wpdb;

$wpdb->snippets = $wpdb->prefix . 'snippets';
$wpdb->ms_snippets = $wpdb->prefix . 'ms_snippets';

/* Multisite uninstall */

if ( is_multisite() ) {

	/* Loop through sites */
	$blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );

	if ( $blog_ids ) {

		foreach ( $blog_ids as $blog_id ) {
			switch_to_blog( $blog_id );
			code_snippets_uninstall_site();
		}

		restore_current_blog();
	}

	/* Remove multisite snippets database table */
	$wpdb->query( "DROP TABLE IF EXISTS $wpdb->ms_snippets" );

	/* Remove saved options */
	delete_site_option( 'code_snippets_version' );
	delete_site_option( 'recently_activated_snippets' );

	/* Remove multisite capabilities */
	$network_cap = apply_filters( 'code_snippets_network_cap', 'manage_network_snippets' );
	$supers = get_super_admins();

	foreach ( $supers as $admin ) {
		$user = new WP_User( 0, $admin );
		$user->remove_cap( $network_cap );
	}
} else {
	code_snippets_uninstall_site();
}
