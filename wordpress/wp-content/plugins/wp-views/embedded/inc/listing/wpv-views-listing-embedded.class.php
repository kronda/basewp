<?php
/**
 * Views embedded listing
 *
 * @package Views
 *
 * @since 1.8
 */

/**
 * Views embedded listing handler.
 *
 * Sets up the decorators accordingly.
 *
 * @since 1.8
 */
class WPV_Views_Listing_Embedded extends WPV_Listing_Embedded {

    function __construct() {
        parent::__construct();
        $this->title_decorator = new WPV_Embedded_Title_Decorator(
            __( 'Views', 'wpv-views' ),
            __( 'Add new View', 'wpv-views' ) );

        $noitems_message = sprintf(
                '<p>%s</p><p>%s</p>',
                __( 'Views load content from the database and display on the site.', 'wpv-views' ),
                __( 'Currently there are no items to display.', 'wpv-views' ) );
        $this->noitems_decorator = new WPV_Embedded_Noitems_Decorator( $noitems_message );

        $this->search_form_decorator = new WPV_SearchForm_Decorator( __( 'Search Views', 'wpv-views' ) );
        $this->table_decorator = new WPV_Views_List_Table_Embedded();
        $this->item_provider_decorator = new WPV_Embedded_View_Item_Provider_Decorator();
        $this->pagination_decorator = new WPV_Embedded_Pagination_Decorator( $this->page_name );
    }

}


/**
 * Table decorator for the Views embedded listing.
 *
 * See WPV_List_Table_Embedded and WP_List_Table to understand how this works.
 *
 * @since 1.8
 */
class WPV_Views_List_Table_Embedded extends WPV_List_Table_Embedded {


    public function get_columns() {
        return array(
            'title' => array(
                'title' => __( 'Title', 'wpv-views' ),
                'is_sortable' => true,
                'orderby' => 'post_title',
                'default_order' => 'ASC',
                'title_asc' => ' <i class="icon-sort-by-alphabet"></i>',
                'title_desc' => ' <i class="icon-sort-by-alphabet-alt"></i>' ),
            'content_to_load' => array( 'title' => __( 'Content to load', 'wpv-views' ) ) );
    }


    protected function get_table_classes() {
        return array_merge( parent::get_table_classes(), array( 'wpv-embedded-listing-table' ) );
    }


    /**
     * Title column.
     *
     * Show title as a link to view the item and description (if there is any).
     *
     * @param $item WPV_View_Embedded View.
     *
     * @return string Content of the table cell.
     */
    public function column_title( $item ) {
        if( !empty( $item->description ) ) {
            $description = sprintf(
                '<p class="desc">%s</p>',
                nl2br( $item->description ) );
        } else {
            $description = '';
        }

        $title = sprintf(
            '<span class="row-title"><a href="%s">%s</a></span>',
            esc_url( add_query_arg(
                array( 'page' => 'views-embedded', 'view_id' => $item->id ),
                admin_url( 'admin.php' ) ) ),
            $item->title );

        return $title . $description;
    }


    /**
     * Show information about what content the View loads.
     *
     * @param $item WPV_View_Embedded View.
     *
     * @return string Content of the table cell.
     */
    public function column_content_to_load( $item ) {
        return $item->content_summary;
    }


}