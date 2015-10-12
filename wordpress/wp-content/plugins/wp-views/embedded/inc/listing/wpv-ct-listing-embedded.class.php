<?php
/**
 * Content Template embedded listing
 *
 * @package Views
 *
 * @since 1.8
 */

/**
 * Content Templates embedded listing handler.
 *
 * Sets up the decorators accordingly.
 *
 * @since 1.8
 */
class WPV_CT_Listing_Embedded extends WPV_Listing_Embedded {

    function __construct() {
        parent::__construct();

        $this->title_decorator = new WPV_Embedded_Title_Decorator(
            __( 'Content Templates', 'wpv-views' ),
            __( 'Add new Content Template', 'wpv-views' ) );

        $noitems_message = sprintf(
                '<p>%s</p><p>%s</p>',
                __( 'Content Templates let you design single pages.', 'wpv-views' ),
                __( 'Currently there are no items to display.', 'wpv-views' ) );
        $this->noitems_decorator = new WPV_Embedded_Noitems_Decorator( $noitems_message );

        $this->search_form_decorator = new WPV_SearchForm_Decorator( __( 'Search Content Templates', 'wpv-views' ) );
        $this->table_decorator = new WPV_CT_List_Table_Embedded();
        $this->item_provider_decorator = new WPV_Embedded_CT_Item_Provider_Decorator();
        $this->pagination_decorator = new WPV_Embedded_Pagination_Decorator( $this->page_name );
    }

}


/**
 * Table decorator for the Content Template embedded listing.
 *
 * See WPV_List_Table_Embedded and WP_List_Table to understand how this works.
 *
 * @since 1.8
 */
class WPV_CT_List_Table_Embedded extends WPV_List_Table_Embedded {


    public function get_columns() {
        return array(
            'title' => array(
                'title' => __( 'Title', 'wpv-views' ),
                'is_sortable' => true,
                'orderby' => 'post_title',
                'default_order' => 'ASC',
                'title_asc' => ' <i class="icon-sort-by-alphabet"></i>',
                'title_desc' => ' <i class="icon-sort-by-alphabet-alt"></i>' ),
            'used_on' => array( 'title' => __( 'Used on', 'wpv-views' ) ) );
    }


    protected function get_table_classes() {
        return array_merge( parent::get_table_classes(), array( 'wpv-embedded-listing-table' ) );
    }


    /**
     * Title column.
     *
     * Show title as a link to view the item and description (if there is any).
     *
     * @param $item WPV_Content_Template_Embedded Content template.
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
                array( 'page' => 'view-templates-embedded', 'view_id' => $item->id ),
                admin_url( 'admin.php' ) ) ),
            $item->title );

        return $title . $description;
    }


    /**
     * Show information about how a CT is being used.
     *
     * @param $item WPV_Content_Template_Embedded Content template.
     *
     * @return string Content of the table cell.
     */
    public function column_used_on( $item ) {

        if( $item->is_owned_by_view ) {
            // This CT is used as a template for Loop Output in a View or WPA.

            // Get a View or WPA object. We'll be using only methods from their base, so it doesn't matter which one is it.
            $owner_view = WPV_View_Base::get_instance( $item->loop_output_id );

            if( $owner_view == null ) {
                // Something is wrong - most probably the owner doesn't exist.
                return '';
            }

            // Display the appropriate message.
            if( $owner_view->is_published ) {
				$edit_page = 'views-embedded';
				if ( WPV_View_Base::is_archive_view( $owner_view->id ) ) {
					$edit_page = 'view-archives-embedded';
				}
                return sprintf(
                    '<span>%s</span>',
                    sprintf( __( 'This Content Template is used as the loop block for the %s <a href="%s" target="_blank">%s</a>', 'wpv-views' ),
                        $owner_view->query_mode_display_name,
                        esc_url( add_query_arg(
                            array( 
								'page' => $edit_page, 
								'view_id' => $owner_view->id 
							),
                            admin_url( 'admin.php' ) ) ),
                        $owner_view->title ) );
            } else {
                return sprintf(
                    '<span>%s</span>',
                    sprintf(
                        __( 'This Content Template is used as the loop block for the trashed %s <strong>%s</strong>', 'wpv-views' ),
                        $owner_view->query_mode_display_name,
                        $owner_view->title ) );
            }

        } else {
            // This is a normal CT. Obtain information about assignments and display them in a tag-like list.
            $list = array();

            // "single posts"
            $assigned_single_pts = $item->get_assigned_single_post_types();
            foreach( $assigned_single_pts as $loop ) {
                $list[] = sprintf( '<li>%s%s</li>', $loop['display_name'], __(' (single)', 'wpv-views') );
            }

            // post type archives
            $assigned_pt_loops = $item->get_assigned_loops( 'post_type' );
            foreach( $assigned_pt_loops as $loop ) {
                $list[] = sprintf( '<li>%s%s</li>', $loop['display_name'], __(' (post type archive)', 'wpv-views') );
            }

            // taxonomy archives
            $assigned_ta_loops = $item->get_assigned_loops( 'taxonomy' );
            foreach( $assigned_ta_loops as $loop ) {
                $list[] = sprintf( '<li>%s%s</li>', $loop['display_name'], __(' (taxonomy archive)', 'wpv-views') );
            }

            if( !empty( $list ) ) {
                return sprintf( '<ul class="wpv-taglike-list">%s</ul>', implode( $list ) );
            } else {
                return sprintf( '<span>%s</span>', __( 'No Post types/Taxonomies assigned', 'wpv-views' ) );
            }
        }


    }

}