<?php


/**
 * Records the WP filter state.
 *
 * @since 1.9.1
 */

class WPV_WP_filter_state {

    private $current_index;
    private $tag;
    
    public function __construct( $tag ) {
        global $wp_filter;

        $this->tag = $tag;
        
        if ( isset( $wp_filter[$tag] ) ) {
            $this->current_index = current($wp_filter[$tag]);
        }
    }
    
    public function restore( ) {
        global $wp_filter;

        if ( isset( $wp_filter[$this->tag] ) && $this->current_index ) {
            reset($wp_filter[$this->tag]);
            while ( $this->current_index && current($wp_filter[$this->tag]) && $this->current_index != current($wp_filter[$this->tag]) ) {
                next( $wp_filter[$this->tag] );
            }
        }
        
    }

}