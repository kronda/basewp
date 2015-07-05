<?php

/**
 * Very simple interface for listing page handlers.
 *
 * @since 1.8
 */
interface IWPV_Listing {

    /**
     * Renders the listing page.
     */
    function display();

}


/**
 * Abstract base for embedded listing pages.
 *
 * Contains all basic logic for embedded listing pages by using decorators. Child classes are expected to
 * properly initialize all of those, providing actual functionality. See the display() method for more details.
 *
 * @since 1.8
 */
abstract class WPV_Listing_Embedded implements IWPV_Listing {

    /**
     * @var IWPV_Page_Decorator
     */
    protected $page_decorator;

    /**
     * @var IWPV_Title_Decorator
     */
    protected $title_decorator;

    /**
     * @var IWPV_Noitems_Decorator
     */
    protected $noitems_decorator;

    /**
     * @var IWPV_Pagination_Decorator
     */
    protected $pagination_decorator;


    /**
     * @var IWPV_SearchForm_Decorator
     */
    protected $search_form_decorator;


    /**
     * @var IWPV_Table_Decorator
     */
    protected $table_decorator;

    /**
     * @var IWPV_ItemProvider_Decorator
     */
    protected $item_provider_decorator;


    /**
     * @var string Value of the "page" URL parameter for the admin page where this listing is rendered.
     */
    protected $page_name;


    /**
     * Class constructor.
     */
    public function __construct() {

        $this->page_name = esc_attr( wpv_getget( 'page' ) );

        // Default decorator
        $this->page_decorator = new WPV_Embedded_Page_Decorator();
    }


    /**
     * Render the listing page.
     *
     * Use decorators (which MUST be all provided by child classes) to render the page. The logic is the same for
     * all embedded listings.
     */
    function display() {

        $args = $this->get_args();

        // Open page, show the title
        $this->page_decorator->render_page_start();
        $this->title_decorator->render_title( wpv_getarr( $args, 'search', '' ) );

        // If there are some items, render a table. Otherwise render a "no items" message.
        if( $this->item_provider_decorator->has_items( $args ) ) {
            $this->search_form_decorator->render_search_form( $args );
            $items = $this->item_provider_decorator->get_items( $args );
            $this->table_decorator->render_table( $items, $args );
        } else {
            $this->noitems_decorator->render_no_items_content( $args );
        }

        // Pagination
        $this->pagination_decorator->render_pagination( $this->item_provider_decorator->get_total_item_count(), $args );

        // Close page
        $this->page_decorator->render_page_end();
    }


    /**
     * Retrieve arguments for listing and querying.
     *
     * Read from $_GET or provide default values for ordering and pagination.
     * Pagination arguments are obtained from the pagination decorator.
     *
     * @return array(
     *     Array of arguments for listing and querying.
     *
     *     @type int $items_per_page Number of items per page. 0 means "not set, use default value" and -1 means "show
     *         all items". The value depends on the pagination decorator.
     *     @type int $paged Number of the current page. 0 means "not set, use default". Obtained from pagination decorator.
     *     @type string $orderby Name of field by which should items be ordered. Arbitrary, not validated here.
     *     @type string $order ASC or DESC. Defaults to ASC.
     * )
     */
    function get_args() {
        $args = array(
            'page' => $this->page_name,
            'items_per_page' => $this->pagination_decorator->get_items_per_page(),
            'paged' => $this->pagination_decorator->get_paged(),
            'orderby' => sanitize_text_field( wpv_getget( 'orderby' ) ),
            'order' => wpv_getget( 'order', 'ASC', array( 'ASC', 'DESC' ) ),
            'search' => urldecode( sanitize_text_field( wpv_getget( 'search', '' ) ) ) );

        return $args;
    }

}