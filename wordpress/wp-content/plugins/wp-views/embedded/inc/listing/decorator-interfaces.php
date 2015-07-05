<?php

/**
 * This file contains all interfaces for decorators used (only) in embedded listings.
 *
 * @since 1.8
 */


/**
 * Page decorator. Handles rendering the beginning and end of listing page.
 *
 * @since 1.8
 */
interface IWPV_Page_Decorator {

    public function render_page_start();

    public function render_page_end();

}


/**
 * Title decorator. Renders page title.
 *
 * @since 1.8
 */
interface IWPV_Title_Decorator {

    public function render_title( $search_term );

}


/**
 * "No items" message decorator. This will be used instead of the table decorator when there are no items to display.
 *
 * @since 1.8
 */
interface IWPV_Noitems_Decorator {

    /**
     * Render the "No items" message.
     *
     * @param array $args An array with arguments used to show the page. Further details depend on particular implementation.
     */
    public function render_no_items_content( $args );

}


/**
 * Pagination decorator. Handles obtaining information about pagination and rendering pagination controls.
 *
 * @since 1.8
 */
interface IWPV_Pagination_Decorator {


    /**
     * Get current page number
     *
     * @return int The "paged" argument representing current page number. Value 0 means "not set, use default".
     */
    public function get_paged();


    /**
     * Get items per page.
     *
     * @return int The "items_per_page" argument. Value 0 means "not set, use default" and -1 means "show all items".
     */
    public function get_items_per_page();


    /**
     * Render pagination controls.
     *
     * @param int $total_item_count Total count of items disregarding pagination.
     * @param array $args Arguments relevant for displaying the page.
     */
    public function render_pagination( $total_item_count, $args );

}


/**
 * Table decorator. Handles rendering the table with given items.
 *
 * Items are usually supposed to be WP_Post objects, but that depends on particular implementation.
 * It should render all items it recieves, without additional logic (that is handled in the listing class, item provider
 * and pagination decorators).
 *
 * @since 1.8
 */
interface IWPV_Table_Decorator {

    /**
     * Render the table.
     *
     * @param array $items Items to be rendered.
     */
    public function render_table( $items );

}


/**
 * Item provider decorator. Handles querying and obtaining items from the database in a form that is accepted by
 * the appropriate table decorator.
 *
 * @since 1.8
 */
interface IWPV_ItemProvider_Decorator {


    /**
     * Query for existence of items to show.
     *
     * @param array $args Arguments relevant for querying. Depends on particular implementation.
     *
     * @return bool True when there are some items to show with provided arguments, false otherwise.
     */
    public function has_items( $args );


    /**
     * Obtain items to be displayed in the listing.
     *
     * @param array $args Arguments relevant for querying. Depends on particular implementation.
     *
     * @return array Items that should be displayed on the page.
     */
    public function get_items( $args );


    /**
     * Get total number of items for used query.
     *
     * After calling get_items(), this method should provide total item count disregarding pagination arguments
     * like items_per_page and paged.
     *
     * @return int Total number of items.
     */
    public function get_total_item_count();
}


interface IWPV_SearchForm_Decorator {

    /**
     * Render the search form.
     *
     * @param array $args
     */
    public function render_search_form( $args );

}