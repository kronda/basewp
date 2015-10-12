<?php
/**
 * WordPress Archive embedded listing
 *
 * @package Views
 *
 * @since 1.8
 */

/**
 * WordPress Archive embedded listing handler.
 *
 * Sets up the decorators accordingly.
 *
 * @since 1.8
 */
class WPV_WPA_Listing_Embedded extends WPV_Listing_Embedded {

    function __construct() {
        parent::__construct();
        $this->title_decorator = new WPV_Embedded_Title_Decorator(
            __( 'WordPress Archives', 'wpv-views' ),
            __( 'Add new WordPress Archive','wpv-views' ) );

        $noitems_message = sprintf(
                '<p>%s</p><p>%s</p>',
                __( 'WordPress Archives let you customize the output of standard Archive pages.', 'wpv-views' ),
                __( 'Currently there are no items to display.', 'wpv-views' ) );
        $this->noitems_decorator = new WPV_Embedded_Noitems_Decorator( $noitems_message );

        $this->search_form_decorator = new WPV_SearchForm_Decorator( __( 'Search WordPress Archives', 'wpv-views' ) );
        $this->table_decorator = new WPV_WPA_List_Table_Embedded();
        $this->item_provider_decorator = new WPV_Embedded_WPA_Item_Provider_Decorator();
        $this->pagination_decorator = new WPV_Embedded_Pagination_Decorator( $this->page_name );
    }

}


/**
 * Table decorator for the WordPress Archive embedded listing.
 *
 * See WPV_List_Table_Embedded and WP_List_Table to understand how this works.
 *
 * @since 1.8
 */
class WPV_WPA_List_Table_Embedded extends WPV_List_Table_Embedded {


    public function get_columns() {
        return array(
            'title' => array(
                'title' => __( 'Title', 'wpv-views' ),
                'is_sortable' => true,
                'orderby' => 'post_title',
                'default_order' => 'ASC',
                'title_asc' => ' <i class="icon-sort-by-alphabet"></i>',
                'title_desc' => ' <i class="icon-sort-by-alphabet-alt"></i>' ),
            'archive_usage' => array( 'title' => __( 'Archive usage', 'wpv-views' ) ) );
    }


    protected function get_table_classes() {
        return array_merge( parent::get_table_classes(), array( 'wpv-embedded-listing-table' ) );
    }


    /**
     * Title column.
     *
     * Show title as a link to view the item and description (if there is any).
     *
     * @param $item WPV_WordPress_Archive_Embedded WordPress Archive.
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
                array( 'page' => 'view-archives-embedded', 'view_id' => $item->id ),
                admin_url( 'admin.php' ) ) ),
            $item->title );

        return $title . $description;
    }


    /**
     * Show information about how a WPA is being used.
     *
     * @param $item WPV_WordPress_Archive_Embedded WordPress Archive.
     *
     * @return string Content of the table cell.
     */
    public function column_archive_usage( $item ) {

        switch( $item->query_mode ) {

            case 'archive':
                // This is a normal WPA

                // Get loops which have this WPA assigned.
                $assigned_loops = $item->get_assigned_loops();

                if( !empty( $assigned_loops ) ) {

                    // Show loop names in a tag-like list.
                    $suffixes = array(
                        'native' => '',
                        'post_type' => __( ' (post type archive)', 'wpv-views' ),
                        'taxonomy' => __( ' (taxonomy archive)', 'wpv-views' )
                    );

                    $assigned_loops_flat = array();
                    foreach( $assigned_loops as $assigned_loop ) {
                        $assigned_loops_flat[] = "<li>{$assigned_loop['display_name']}{$suffixes[ $assigned_loop['loop_type'] ]}</li>";
                    }

                    return sprintf( '<ul class="wpv-taglike-list">%s</ul>', implode( $assigned_loops_flat ) );

                } else {
                    // Nothing to list.
                    return __( 'This WordPress Archive isn\'t being used for any loops.', 'wpv-views' );
                }

            case 'layouts-loop':
                // This is a WPA coming from the Layouts plugin.
                return __( 'This WordPress Archive is part of a Layout, so it will display the archive(s) to which the Layout is assigned.', 'wpv-views' );

            default:
                // We should never get here.
                return '';
        }
    }

}