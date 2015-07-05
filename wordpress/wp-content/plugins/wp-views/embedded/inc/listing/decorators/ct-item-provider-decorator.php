<?php
/**
 * Item provider decorator for Content Templates in embedded listings
 *
 * @package Views
 *
 * @since 1.8
 */


/**
 * Item provider decorator for Content Templates.
 *
 * Retrieves all Content Templates. Please note that the $args should not change between calling has_items() and
 * get_items() - it will be applied only the first time. Currently supported arguments are 'items_per_page', 'paged',
 * 'order' and 'orderby'.
 *
 * @since 1.8
 */
class WPV_Embedded_CT_Item_Provider_Decorator implements IWPV_ItemProvider_Decorator {


    /**
     * The query object, after ensure_query() has been called.
     *
     * @var null|WP_Query
     */
    private $query = null;


    /**
     * The items that the provider should return, after ensure_query() has been called.
     *
     * @var null|WPV_Content_Template_Embedded
     */
    private $items = null;


    /**
     * Query for existence of items to show.
     *
     * @param array $args Query arguments. See class description.
     *
     * @return bool True when there are some items to show, false otherwise.
     */
    public function has_items( $args )
    {
        $this->ensure_query( $args );
        $has_items = ( $this->query->post_count > 0 );
        return $has_items;
    }


    /**
     * Obtain items to be displayed in the listing.
     *
     * @param array $args Query arguments. See class description.
     *
     * @return array Items that should be displayed on the page. An array of WPV_Content_Template_Embedded objects.
     */
    public function get_items( $args )
    {
        $this->ensure_query( $args );
        return $this->items;
    }


    /**
     * Get total number of items for used query.
     *
     * After calling get_items() or has_items(), this method should provide total item count disregarding pagination
     * arguments like items_per_page and paged.
     *
     * @return int Total number of items.
     */
    public function get_total_item_count()
    {
        if( $this->query instanceof WP_Query ) {
            return $this->query->found_posts;
        } else {
            return 0;
        }
    }


    /**
     * First time it is called, create the query, save it to $this->query and fill $this->items.
     *
     * @param array $args Query arguments. See class description.
     */
    private function ensure_query( $args ) {

        if( $this->query instanceof WP_Query ) {
            // We've already done this.
            return;
        }

        // Build arguments for WP_Query
        $posts_per_page = (int) wpv_getarr( $args, 'items_per_page', 0 );
        $posts_per_page = ( 0 == $posts_per_page ) ? WPV_ITEMS_PER_PAGE : $posts_per_page;

        $paged = (int) wpv_getarr( $args, 'paged', 0 );
        $paged = ( 0 == $paged ) ? 1 : $paged;

        $query_args = array(
            'post_type' => 'view-template',
            'posts_per_page' => $posts_per_page,
            'paged' => $paged,
            'order' => wpv_getarr( $args, 'order', 'ASC', array( 'ASC', 'DESC' ) ),
            'orderby' => wpv_getarr( $args, 'orderby', 'post_title' ),
            'post_status' => 'publish' );

        $search = wpv_getarr( $args, 'search', '' );
        if( !empty( $search ) ) {
            $query_args['s'] = $search;
        }

        $this->query = new WP_Query( $query_args );

        // Create WPV_Content_Template_Embedded objects from WP_Post objects.
        $this->items = array();
        $posts = $this->query->posts;
        foreach( $posts as $post ) {
            $this->items[] = new WPV_Content_Template_Embedded( $post );
        }

    }
}