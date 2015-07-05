<?php
/**
 * Include additional listing-related files.
 *
 * All files within embedded/inc/listing should be included here.
 *
 * Note that file order is not arbitrary.
 * - decorator-interfaces.php must precede decorators/*
 * - wpv-listing-embedded.class.php must precede other *.class.php files.
 */

$wpv_listing_embedded_root = array(
    'decorator-interfaces.php',
    'decorators/ct-item-provider-decorator.php',
    'decorators/pagination-decorator.php',
    'decorators/simple-decorators.php',
    'decorators/view-item-provider-decorators.php',
    'wpv-list-table-embedded.php',
    'wpv-listing-embedded.class.php',
    'wpv-ct-listing-embedded.class.php',
    'wpv-views-listing-embedded.class.php',
    'wpv-wpa-listing-embedded.class.php',
    'page-handlers.php'
);

foreach( $wpv_listing_embedded_root as $required_file ) {
    require_once plugin_dir_path( __FILE__ ) . $required_file;
}
