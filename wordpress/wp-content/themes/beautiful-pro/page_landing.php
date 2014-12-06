<?php
/**
 * This file adds the Landing template to the Beautiful Pro Theme.
 *
 * @author StudioPress
 * @package Beautiful Pro
 * @subpackage Customizations
*/

/*
Template Name: Landing
*/

//* Add landing body class to the head
add_filter( 'body_class', 'beautiful_add_body_class' );
function beautiful_add_body_class( $classes ) {

	$classes[] = 'beautiful-landing';
	return $classes;

}

//* Force full width content layout
add_filter( 'genesis_site_layout', '__genesis_return_full_width_content' );

//* Remove before header widget area above header
remove_action( 'genesis_before_header', 'beautiful_before_header' );

//* Remove site header elements
remove_action( 'genesis_header', 'genesis_header_markup_open', 5 );
remove_action( 'genesis_header', 'genesis_do_header' );
remove_action( 'genesis_header', 'genesis_header_markup_close', 15 );

//* Remove navigation
remove_action( 'genesis_after_header', 'genesis_do_nav' );
remove_action( 'genesis_after_header', 'genesis_do_subnav', 15 );

//* Remove site header banner
remove_action( 'genesis_after_header', 'beautiful_site_header_banner' );

//* Remove breadcrumbs
remove_action( 'genesis_before_loop', 'genesis_do_breadcrumbs' );

//* Remove site footer widgets
remove_action( 'genesis_before_footer', 'genesis_footer_widget_areas' );

//* Remove site footer elements
remove_action( 'genesis_footer', 'genesis_footer_markup_open', 5 );
remove_action( 'genesis_footer', 'genesis_do_footer' );
remove_action( 'genesis_footer', 'genesis_footer_markup_close', 15 );

//* Run the Genesis loop
genesis();
