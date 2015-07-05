<?php

/**
 * Base class for displaying a list of items in a HTML table.
 *
 * Similar to WP_List_Table. Contains only functionality necessary for embedded listings.
 *
 * @since 1.8
 */
abstract class WPV_List_Table_Embedded implements IWPV_Table_Decorator {


    /**
     * @var array The current list of items.
     */
    protected $items;


    /**
     * @var array Stores the value returned by ->get_column_info().
     */
    protected $columns = null;


    /**
     * Whether the table has items to display or not.
     *
     * @return bool
     */
    protected function has_items() {
        return !empty( $this->items );
    }


    /**
     * Message to be displayed when there are no items.
     */
    protected function no_items() {
        _e( 'No items found.' );
    }


    /**
     * Get a list of columns.
     *
     * This should return an array with column slugs as keys and values in following form:
     *
     * array {
     *     @type string $title Column title to be displayed.
     *     @type bool $is_sortable Optional. Determine whether this column is sortable. Default is false.
     *     @type string $default_order Optional. If items provided are already sorted by this column, this determines
     *         their order. Allowed values are "ASC" and "DESC". Only first column with this argument will be taken into
     *         consideration.
     *     @type string $orderby Value of the "orderby" URL parameter for this column. Mandatory if $is_sortable is true.
     *     @type string $title_asc String that will be appended to column title if the table is ordered by it (ascending). Optional.
     *     @type string $title_desc String that will be appended to column title if the table is ordered by it (descending). Optional.
     * }
     *
     * @return array
     */
    abstract protected function get_columns();


    /**
     * Get a list of all, hidden and sortable columns, with filter applied.
     *
     * @return array
     */
    protected function get_column_info() {
        if ( null == $this->columns ) {
            $this->columns = $this->get_columns();
        }

        return $this->columns;
    }


    /**
     * Return number of columns.
     *
     * @return int
     */
    protected function get_column_count() {
        $columns = $this->get_column_info();
        return count( $columns );
    }


    /**
     * Get column by which the items are supposed to be pre-sorted.
     *
     * @return string Column slug or empty string if no such column exists.
     */
    private function get_presorted_column() {
        $columns = $this->get_column_info();
        foreach( $columns as $column_slug => $column ) {
            $is_sortable = wpv_getarr( $column, 'is_sortable', false );
            $default_order = wpv_getarr( $column, 'default_order', '', array( 'ASC', 'DESC' ) );
            if( $is_sortable && ( '' != $default_order ) ) {
                return $column_slug;
            }
        }
        return '';
    }


    /**
     * Get column with matching 'orderby' argument.
     *
     * @param string $orderby The 'orderby' value to search for.
     *
     * @return string Column slug or empty string if no such column exists.
     */
    private function get_column_slug_by_orderby( $orderby ) {
        $columns = $this->get_column_info();
        foreach( $columns as $column_slug => $column ) {
            $column_orderby = wpv_getarr( $column, 'orderby', '' );
            if( $column_orderby == $orderby ) {
                return $column_slug;
            }
        }
        return '';
    }


    /**
     * Print column headers.
     */
    protected function print_column_headers() {

        $columns = $this->get_column_info();

        /* Get the current 'orderby' URL parameter. Obtain it either from $_GET or from column definitions (if there is
         * a pre-sorted column). If items aren't ordered, it will be an empty string. */
        $current_orderby = wpv_getget( 'orderby', '' );

        if( '' == $current_orderby ) {
            // Try to get information from column definitions.
            $current_orderby_column = $this->get_presorted_column();
            $current_orderby = $columns[ $current_orderby_column ]['orderby'];
            $is_presorted = ( '' != $current_orderby );
        } else {
            $is_presorted = false;
            $current_orderby_column = $this->get_column_slug_by_orderby( $current_orderby );
        }

        // Get the current 'order' URL parameter from $_GET or from column definitions. Defaults to 'ASC' if both methods fail.
        $current_order = wpv_getget( 'order', '', array( 'ASC', 'DESC' ) );
        if( '' == $current_order ) {
            if( $is_presorted ) {
                $current_order = wpv_getarr( $columns[ $current_orderby_column ], 'default_order', 'ASC', array( 'ASC', 'DESC' ) );
            } else {
                $current_order = 'ASC';
            }
        }

        $reverse_order = ( 'ASC' == $current_order ) ? 'DESC' : 'ASC';

        // URL that will be used to build sorting links. All URL parameters except 'paged' and 'last_page' will be preserved.
        $current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
        $current_url = remove_query_arg( array( 'paged', 'last_page' ), $current_url );

        // Print table header cell for each column
        foreach ( $columns as $column_slug => $column ) {

            $class = array( "column-$column_slug" );

            $is_sortable = wpv_getarr( $column, 'is_sortable', false );

            if( $is_sortable ) {
                // Sortable column

                // URL 'orderby' parameter for this column
                $column_orderby = wpv_getarr( $column, 'orderby', '' );

                if( $column_orderby == $current_orderby ) {
                    // We're sorting by this column. Build link to reverse order.
                    $new_order = $reverse_order;
                    $new_orderby = $current_orderby;

                    // Append to the title according to current order.
                    $title_order = wpv_getarr( $column, ( 'ASC' == $current_order ) ? 'title_asc' : 'title_desc', '' );
                } else {
                    // Not sorting by this column. Build link to sort by this column, ascending.
                    $new_order = 'ASC';
                    $new_orderby = $column_orderby;
                    $title_order = '';
                }

                $title = sprintf(
                    '<a href="%s">%s%s</a>',
                    add_query_arg(
                        array( 'order' => $new_order, 'orderby' => $new_orderby ),
                        $current_url ),
                    wpv_getarr( $column, 'title', $column_slug ),
                    $title_order );

            } else {
                // Non-sortable column, just print the title text.
                $title = wpv_getarr( $column, 'title', $column_slug );
            }

            printf(
                '<th scope="col" class="%s">%s</th>', join( ' ', $class ), $title );
        }
    }


    /**
     * Display the table.
     */
    protected function display() {
        ?>
        <table class="wp-list-table <?php echo implode( ' ', $this->get_table_classes() ); ?>">
            <thead>
                <tr>
                    <?php $this->print_column_headers(); ?>
                </tr>
            </thead>

            <tbody id="the-list">
               <?php $this->display_rows_or_placeholder(); ?>
            </tbody>

            <tfoot>
                <tr>
                    <?php $this->print_column_headers(); ?>
                </tr>
            </tfoot>
        </table>
        <?php

    }


    /**
     * Get a list of CSS classes for the list table table tag.
     *
     * @return array List of CSS classes for the table tag.
     */
    protected function get_table_classes() {
        return array( 'widefat', 'fixed', 'striped' );
    }


    /**
     * Generate content of the tbody element for the list table.
     */
    protected function display_rows_or_placeholder() {
        if ( $this->has_items() ) {
            $this->display_rows();
        } else {
            echo '<tr class="no-items"><td class="colspanchange" colspan="' . $this->get_column_count() . '">';
            $this->no_items();
            echo '</td></tr>';
        }
    }


    /**
     * Generate the table rows.
     */
    protected function display_rows() {
        foreach ( $this->items as $item ) {
            $this->single_row( $item );
        }
    }


    /**
     * Generates content for a single row of the table.
     *
     * @param object $item The current item.
     */
    public function single_row( $item ) {
        static $alternate;
        $alternate = 'alternate' == $alternate ? '' : 'alternate';

        printf( '<tr class="%s">', $alternate );
        $this->single_row_columns( $item );
        printf( '</tr>' );
    }


    /**
     * Try to show something when there is no column handler.
     *
     * Use column name as a key if the item is an array, or as a property if it is an object. Return the whole item
     * otherwise.
     *
     * @param mixed $item Item to display.
     * @param string $column_name Name of the column.
     * @return mixed Hopefully some relevant information.
     */
    protected function column_default( $item, $column_name ) {
        if ( is_array( $item ) && isset( $item[ $column_name ] ) ) {
            return $item[ $column_name ];
        } elseif ( is_object( $item ) && isset( $item->$column_name ) ) {
            return $item->$column_name;
        } else {
            return $item;
        }
    }


    /**
     * Generates the columns for a single row of the table.
     *
     * @param object $item The current item
     */
    protected function single_row_columns( $item ) {
        $columns = $this->get_column_info();

        foreach ( $columns as $column_name => $column ) {
            echo "<td>";

            if ( method_exists( $this, 'column_' . $column_name ) ) {
                echo call_user_func( array( $this, 'column_' . $column_name ), $item );
            } else {
                echo $this->column_default( $item, $column_name );
            }

            echo "</td>";
        }
    }


    /**
     * Render the table.
     *
     * Implementation of IWPV_Table_Decorator.
     *
     * @param array $items Items to display.
     */
    public function render_table( $items )
    {
        $this->items = $items;
        $this->display();
    }

}
