<?php
/**
 * Pagination decorator for embedded listings.
 *
 * Retrieves arguments for pagination from the $_GET variable. Currently it doesn't preserve any other arguments.
 * This needs to have the embedded/res/js/listing_pages.js script included on the page, otherwise changing of the
 * items per page won't work.
 *
 * @since 1.8
 */
class WPV_Embedded_Pagination_Decorator implements IWPV_Pagination_Decorator {

    /**
     * @var int Number of the current page.
     */
    protected $paged;


    /**
     * @var int Number of items to show on one page. The value -1 means to show all items.
     */
    protected $items_per_page;


    /**
     * @var string Name of the page on which the pagination controls are rendered. Needed for building links.
     */
    protected $page_name;


    /**
     * Class constructor.
     *
     * @param $page_name string Name of the page (value of the 'page' parameter) on which the pagination controls are rendered.
     */
    function __construct( $page_name ) {
        $this->paged = (int) wpv_getget( 'paged', 1 );
        $this->items_per_page = (int) wpv_getget( 'items_per_page', WPV_ITEMS_PER_PAGE );
        $this->page_name = $page_name;
    }


    /**
     * @return int Current page.
     */
    function get_paged()
    {
        return $this->paged;
    }


    /**
     * @return int Number of items to show on one page. The value -1 means to show all items.
     */
    function get_items_per_page()
    {
        return $this->items_per_page;
    }


    /**
     * Render pagination controls.
     *
     * @param int $total_item_count Total count of items disregarding pagination.
     * @param array $args Arguments for displaying the page. Currently only 'order' and 'orderby' are supported.
     */
    function render_pagination( $total_item_count, $args )
    {
        $pages_count = ( $this->items_per_page > 0 ) ? ceil( (int) $total_item_count / $this->items_per_page ) : -1;

        if ( $pages_count > 1 ) {

            // Calculate offsets of first and last items displayed
            $items_start = ( ( ( $this->paged - 1 ) * $this->items_per_page ) + 1 );
            $items_end = ( ( ( $this->paged - 1 ) * $this->items_per_page ) + $this->items_per_page );

            if ( $this->paged == $pages_count ) {
                $items_end = $total_item_count;
            }

            // Array of URL parameters to preserve
            $mod_url = array(
                'page' => $this->page_name,
                'orderby' => wpv_getarr( $args, 'orderby', '' ),
                'order' => wpv_getarr( $args, 'order', '' ),
                'search' => wpv_getarr( $args, 'search', '' ),
                'items_per_page' => $this->items_per_page );

            ?>
            <div class="wpv-listing-pagination tablenav">

                <div class="tablenav-pages">

                    <span class="displaying-num">
                        <?php echo __( 'Displaying ', 'wpv-views' ) . "$items_start - $items_end" . __( ' of ', 'wpv-views' ) . $total_item_count; ?>
                    </span>

                    <?php

                    // "Previous page" link
                    if ( $this->paged > 1 ) {

                        $previous_page_args = array_merge( $mod_url, array( 'paged' => $this->paged - 1 ) );

                        printf(
                            '<a href="%s" class="wpv-filter-navigation-link">&laquo; %s</a>',
                            wpv_maybe_add_query_arg( $previous_page_args, admin_url( 'admin.php' ) ),
                            __( 'Previous page', 'wpv-views' ) );
                    }

                    // Page number links
                    for ( $i = 1; $i <= $pages_count; $i++ ) {
                        $classes = ( $this->paged == $i ) ? 'active current' : '';

                        // If this is a last page, we'll add an argument indicating that.
                        $is_last_page = ( $i == $pages_count );

                        $page_number_args = array_merge( $mod_url, array(
                            'paged' => $i,
                            'last_page' => $is_last_page ? '1' : '' ) );

                        printf(
                            '<a href="%s" class="%s">%s</a>',
                            wpv_maybe_add_query_arg( $page_number_args, admin_url( 'admin.php' ) ),
                            $classes,
                            $i );
                    }

                    // "Next page" link
                    if ( $this->paged < $pages_count ) {

                        $is_last_page = ( ( $this->paged + 1 )  == $pages_count );

                        $next_page_args = array_merge( $mod_url, array(
                            'paged' => $this->paged + 1,
                            'last_page' => $is_last_page ? '1' : '' ) );

                        printf(
                            '<a href="%s" class="wpv-filter-navigation-link">%s &raquo;</a>',
                            wpv_maybe_add_query_arg( $next_page_args, admin_url( 'admin.php' ) ),
                            __( 'Next page','wpv-views' ) );
                    }

                    // Items per page switcher
                    _e( 'Items per page', 'wpv-views' );

                    ?>
                    <select class="js-items-per-page">
                        <option value="10" <?php selected( $this->items_per_page == '10' ); ?> >10</option>
                        <option value="20" <?php selected( $this->items_per_page == '20' ); ?> >20</option>
                        <option value="50" <?php selected( $this->items_per_page == '50' ); ?> >50</option>
                    </select>
                    <a href="#" class="js-wpv-display-all-items"><?php _e( 'Display all items', 'wpv-views' ); ?></a>

                </div>
            </div>

        <?php

            } else if ( ( WPV_ITEMS_PER_PAGE != $this->items_per_page ) && ( $total_item_count > WPV_ITEMS_PER_PAGE ) ) {
                // We have only one page, non-default items_per_page setting and more items than we can show in a default setting.
                // Only show a link to reset to defaults.
                ?>
                <div class="wpv-listing-pagination tablenav">
                    <div class="tablenav-pages">
                        <a href="#" class="js-wpv-display-default-items"><?php _e( 'Display 20 items per page', 'wpv-views' ); ?></a>
                    </div>
                </div>
                <?php
            }

        ?>
        <?php
    }

}