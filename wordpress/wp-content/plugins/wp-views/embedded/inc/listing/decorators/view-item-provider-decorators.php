<?php
/**
 * Item provider decorators for Views and WPAs in embedded listings
 *
 * @package Views
 *
 * @since 1.8
 */


/**
 * Item provider decorator for Views and WordPress Archives.
 *
 * This contains shared functionality for the 'view' post type, which means both Views and WPAs. The only
 * real difference is in the 'view-query-mode' setting.
 *
 * @since 1.8
 */
abstract class WPV_Embedded_ViewsPostType_ItemProvider_Decorator implements IWPV_ItemProvider_Decorator {


    /**
     * @var null|array Contains the output of $this->prepare_view_listing_query. Be sure to call
     *     $this->ensure_pre_query_data() before accesing it.
     */
    protected $views_pre_query_data = null;


    /**
     * @var string View query mode. @see wpv_prepare_view_listing_query() for allowed values and their explanation.
     */
    protected $view_query_mode = '';


    /**
     * @var null|WP_Query The query object after items have been queried.
     */
    protected $query = null;


    /**
     * Class constructor.
     *
     * @param string|array $view_query_mode View query mode. @see wpv_prepare_view_listing_query() for allowed
     *     values and their explanation.
     */
    public function __construct( $view_query_mode ) {
        $this->view_query_mode = $view_query_mode;
    }


    /**
     * Prepare data for view listing query if it has not been done already.
     *
     * Initializes $this->views_pre_query_data.
     */
    private function ensure_pre_query_data() {
        if( null == $this->views_pre_query_data ) {
            $this->views_pre_query_data = $this->prepare_view_listing_query( $this->view_query_mode );
        }
    }


    /**
     * Determine whether there are any items at all.
     *
     * @param array $args Ignored.
     *
     * @return bool True if there are any items to be displayed.
     */
    public function has_items( $args )
    {
        $this->ensure_pre_query_data();
        return ( $this->views_pre_query_data['published_count'] > 0 );
    }


    /**
     * Retrieve items to display.
     *
     * Queries all published posts with appropriate view query mode. No pagination or ordering is supported (yet).
     *
     * @param array $args Array of arguments for building the query. Currently following arguments are supported:
     *     items_per_page, paged, orderby, order, search.
     *
     * @return array An array of matching WP_Post objects.
     */
    public function get_posts( $args = array() )
    {
        $this->ensure_pre_query_data();

        $posts_per_page = (int) wpv_getarr( $args, 'items_per_page', 0 );
        $posts_per_page = ( 0 == $posts_per_page ) ? WPV_ITEMS_PER_PAGE : $posts_per_page;

        $paged = (int) wpv_getarr( $args, 'paged', 0 );
        $paged = ( 0 == $paged ) ? 1 : $paged;

        $order = wpv_getarr( $args, 'order', 'ASC', array( 'ASC', 'DESC' ) );

        $orderby = wpv_getarr( $args, 'orderby', 'post_title', array( 'post_title' ) );

        $query_args = array(
            'post_type' => 'view',
            'post__in' => $this->views_pre_query_data[ 'post__in' ],
            'posts_per_page' => $posts_per_page,
            'paged' => $paged,
            'order' => $order,
            'orderby' => $orderby,
            'post_status' => 'publish'
        );

        $search = wpv_getarr( $args, 'search', '' );
        if( !empty( $search ) ) {
            $query_args['s'] = $search;
        }

        $this->query = new WP_Query( $query_args );

        return $this->query->posts;
    }


    /**
     * Get total item count.
     *
     * This works only after get_posts() has been called.
     *
     * @return int Total item count disregarding pagination.
     *
     * @since 1.8
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
     * This is a simplified version of wpv_prepare_view_listing_query().
     *
     * Please look up the original function description in order to understand what it does and why.
     *
     * It is equivalent to wpv_prepare_view_listing_query( $view_query_mode, 'publish', array(), false, array() ),
     * with few minor differences:
     *     1. Returned array contains only published_count and post__in keys.
     *     2. Trashed posts are omitted entirely (which makes the query a little bit lighter).
     *     3. For Views settings we don't call $WP_Views->get_views_settings(), which is available only in full version.
     *         Instead we rely on _wpv_settings being present and already containing the 'view-query-mode' key.
     *         According to how this is being accessed in Views embedded now, it shouldn't be a problem.
     *
     * @param $view_query_mode string View query mode. @see wpv_prepare_view_listing_query().
     *
     * @return array @see wpv_prepare_view_listing_query().
     *
     * @since 1.8
     */
    private function prepare_view_listing_query( $view_query_mode ) {
        global $wpdb;

        /* Queries rows with post id and value of _wpv_settings meta (or null if
         * it doesn't exist, notice the LEFT JOIN). */
        $query = "SELECT ID AS id, postmeta.meta_value AS view_settings
			FROM {$wpdb->posts} AS posts
				LEFT JOIN {$wpdb->postmeta} AS postmeta
				ON ( posts.ID = postmeta.post_id AND postmeta.meta_key = '_wpv_settings' )
			WHERE ( posts.post_type = 'view' AND post_status = 'publish' )";
        $views = $wpdb->get_results( $query );

        $post_in = array();

        // Ensure $view_query_mode is an array
        if( !is_array( $view_query_mode ) ) {
            $view_query_mode = array( $view_query_mode );
        }

        /* For each result we need to determine if it's a View or a WPA. If it's what we want, decide by
         * it's post_status which counter to increment and whether to include into post__in (that means possible result
         * in the final listing query). */
        foreach( $views as $view ) {

            // Prepare the value of _wpv_settings postmeta in the same way get_post_meta( ..., ..., true ) would.
            $view_settings = ( null == $view->view_settings ) ? null: maybe_unserialize( $view->view_settings );

            $this_view_query_mode = wpv_getarr( $view_settings, 'view-query-mode', '' );

            // It is the right kind of View?
            if ( in_array( $this_view_query_mode, $view_query_mode ) ) {

                // This is a possible result of the final listing query
                $post_in[] = $view->id;
            }
        }

        $total_count = count( $post_in );

        // If there are no results, we don't want any post to match anything in post__in.
        if( count( $post_in ) == 0 ) {
            $post_in[] = 0;
        }

        $ret = array(
            'published_count' => $total_count,
            'post__in' => $post_in );

        return $ret;
    }

}


/**
 * Item provider decorator for WordPress Archives.
 *
 * It just sets the correct view query mode in the parent class.
 *
 * @since 1.8
 */
class WPV_Embedded_WPA_Item_Provider_Decorator extends WPV_Embedded_ViewsPostType_ItemProvider_Decorator
{

    /**
     * Class constructor.
     *
     * Call parent constructor with view query mode of WPA.
     */
    public function __construct() {
        parent::__construct( array( 'archive', 'layouts-loop' ) );
    }


    /**
     * Obtain items to be displayed in the listing.
     *
     * @param array $args Arguments relevant for querying. See parent's get_posts() description.
     *
     * @return array Items that should be displayed on the page. An array of WPV_WordPress_Archive_Embedded objects.
     */
    public function get_items( $args )
    {
        $posts = $this->get_posts( $args );
        $wpas = array();
        foreach( $posts as $post ) {
            $wpas[] = new WPV_WordPress_Archive_Embedded( $post );
        }
        return $wpas;
    }
}


/**
 * Item provider decorator for Views.
 *
 * It just sets the correct view query mode in the parent class.
 *
 * @since 1.8
 */
class WPV_Embedded_View_Item_Provider_Decorator extends WPV_Embedded_ViewsPostType_ItemProvider_Decorator {


    /**
     * Class constructor.
     *
     * Call parent constructor with view query mode of View.
     */
    public function __construct() {
        parent::__construct( 'normal' );
    }


    /**
     * Obtain items to be displayed in the listing.
     *
     * @param array $args Arguments relevant for querying. See parent's get_posts() description.
     *
     * @return array Items that should be displayed on the page. An array of WPV_View_Embedded objects.
     */
    public function get_items( $args )
    {
        $posts = $this->get_posts( $args );

        // Create WPV_View_Embedded objects from WP_Post objects.
        $items = array();
        foreach( $posts as $post ) {
            $items[] = new WPV_View_Embedded( $post );
        }
        return $items;
    }
}