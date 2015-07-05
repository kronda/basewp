<?php
/**
 * Page handlers for admin menus
 *
 * @since 1.8
 */


/**
 * Views embedded listing page.
 */
function wpv_admin_menu_embedded_views_listing_page() {
    $listing = new WPV_Views_Listing_Embedded();
    $listing->display();
}


/**
 * Content templates embedded listing page.
 */
function wpv_admin_menu_embedded_views_templates_listing_page() {
    $listing = new WPV_CT_Listing_Embedded();
    $listing->display();
}


/**
 * WordPress Archives embedded listing page.
 */
function wpv_admin_menu_embedded_views_archives_listing_page() {
    $listing = new WPV_WPA_Listing_Embedded();
    $listing->display();
}
