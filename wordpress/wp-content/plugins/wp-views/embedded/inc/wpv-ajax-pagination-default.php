<?php

// Handle the ajax pagination when using the default permalink structure
// ie. www.site.com/?page=XXX

$dir = dirname(__FILE__);
$base_dir = substr($dir, 0, strpos($dir, DIRECTORY_SEPARATOR. 'wp-content'));

require( $base_dir . '/wp-blog-header.php' );

if ( isset( $_GET['wpv-ajax-pagination'] ) ) {

    $post_data = pack( 'H*', $_GET['wpv-ajax-pagination'] );
    
    $post_data = json_decode( $post_data, true );
	
	global $wp_query;
	if ( $wp_query->is_404 ) {
		$wp_query->is_404 = false;
	}
    
    header( 'HTTP/1.1 200 OK' );
    header( 'Content-Type: text/css' );
    echo '<html><body>';

    wpv_ajax_get_page( $post_data );
    
    echo '</body></html>';
    
    exit;
    
}
