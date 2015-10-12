<?php

// todo @Jan: determine if we need screens, ajax, hidden columns, filters; check if all works after making the embedded version thinner
abstract class WPV_List_Table extends WPV_List_Table_Embedded {

    /**
     * Cached bulk actions
     *
     * @since 1.8
     * @var array
     * @access private
     */
    private $_actions;


    /**
     * Various information about the current table
     *
     * @since 1.8
     * @var array
     * @access protected
     */
    protected $_args;

    /**
     * The view switcher modes.
     *
     * @since 1.8
     * @var array
     * @access protected
     */
    protected $modes = array();


    /**
     * The current screen
     *
     * @since 1.8
     * @var object
     * @access protected
     */
    protected $screen;


    /**
     * Constructor.
     *
     * The child class should call this constructor from its own constructor to override
     * the default $args.
     *
     * @since 1.8
     * @access public
     *
     * @param array|string $args {
     *     Array or string of arguments.
     *
     *     @type string $plural   Plural value used for labels and the objects being listed.
     *                            This affects things such as CSS class-names and nonces used
     *                            in the list table, e.g. 'posts'. Default empty.
     *     @type string $singular Singular label for an object being listed, e.g. 'post'.
     *                            Default empty
     *     @type bool   $ajax     Whether the list table supports AJAX. This includes loading
     *                            and sorting data, for example. If true, the class will call
     *                            the {@see _js_vars()} method in the footer to provide variables
     *                            to any scripts handling AJAX events. Default false.
     *     @type string $screen   String containing the hook name used to determine the current
     *                            screen. If left null, the current screen will be automatically set.
     *                            Default null.
     * }
     */
    public function __construct( $args = array() ) {
        $args = wp_parse_args( $args, array(
            'plural' => '',
            'singular' => '',
            'ajax' => false,
            'screen' => null,
        ) );

        $this->screen = convert_to_screen( $args['screen'] );

        add_filter( "manage_{$this->screen->id}_columns", array( $this, 'get_columns' ), 0 );

        if ( !$args['plural'] )
            $args['plural'] = $this->screen->base;

        $args['plural'] = sanitize_key( $args['plural'] );
        $args['singular'] = sanitize_key( $args['singular'] );

        $this->_args = $args;

        if ( $args['ajax'] ) {
            // wp_enqueue_script( 'list-table' );
            add_action( 'admin_footer', array( $this, '_js_vars' ) );
        }

        if ( empty( $this->modes ) ) {
            $this->modes = array(
                'list' => __( 'List View' ),
                'excerpt' => __( 'Excerpt View' )
            );
        }
    }

    /**
     * Checks the current user's permissions
     *
     * @since 1.8
     * @access public
     * @abstract
     */
    public function ajax_user_can() {
        die( 'function WPV_List_Table_Embedded::ajax_user_can() must be over-ridden in a sub-class.' );
    }

    /**
     * Display the search box.
     *
     * @since 1.8
     * @access public
     *
     * @param string $text The search button text
     * @param string $input_id The search input id
     */
    public function search_box( $text, $input_id ) {
        if ( empty( $_REQUEST['s'] ) && !$this->has_items() )
            return;

        $input_id = $input_id . '-search-input';

        if ( !empty( $_REQUEST['orderby'] ) )
            echo '<input type="hidden" name="orderby" value="' . esc_attr( $_REQUEST['orderby'] ) . '" />';
        if ( !empty( $_REQUEST['order'] ) )
            echo '<input type="hidden" name="order" value="' . esc_attr( $_REQUEST['order'] ) . '" />';
        if ( !empty( $_REQUEST['post_mime_type'] ) )
            echo '<input type="hidden" name="post_mime_type" value="' . esc_attr( $_REQUEST['post_mime_type'] ) . '" />';
        if ( !empty( $_REQUEST['detached'] ) )
            echo '<input type="hidden" name="detached" value="' . esc_attr( $_REQUEST['detached'] ) . '" />';
        ?>
        <p class="search-box">
            <label class="screen-reader-text" for="<?php echo $input_id ?>"><?php echo $text; ?>:</label>
            <input type="search" id="<?php echo $input_id ?>" name="s" value="<?php _admin_search_query(); ?>" />
            <?php submit_button( $text, 'button', '', false, array( 'id' => 'search-submit' ) ); ?>
        </p>
        <?php
    }

    /**
     * Get an associative array ( option_name => option_title ) with the list
     * of bulk actions available on this table.
     *
     * @since 1.8
     * @access protected
     *
     * @return array
     */
    protected function get_bulk_actions() {
        return array();
    }

    /**
     * Display the bulk actions dropdown.
     *
     * @since 1.8
     * @access protected
     *
     * @param string $which The location of the bulk actions: 'top' or 'bottom'.
     *                      This is designated as optional for backwards-compatibility.
     */
    protected function bulk_actions( $which = '' ) {
        if ( is_null( $this->_actions ) ) {
            $no_new_actions = $this->_actions = $this->get_bulk_actions();
            /**
             * Filter the list table Bulk Actions drop-down.
             *
             * The dynamic portion of the hook name, `$this->screen->id`, refers
             * to the ID of the current screen, usually a string.
             *
             * This filter can currently only be used to remove bulk actions.
             *
             * @since 3.5.0
             *
             * @param array $actions An array of the available bulk actions.
             */
            $this->_actions = apply_filters( "bulk_actions-{$this->screen->id}", $this->_actions );
            $this->_actions = array_intersect_assoc( $this->_actions, $no_new_actions );
            $two = '';
        } else {
            $two = '2';
        }

        if ( empty( $this->_actions ) )
            return;

        echo "<label for='bulk-action-selector-" . esc_attr( $which ) . "' class='screen-reader-text'>" . __( 'Select bulk action' ) . "</label>";
        echo "<select name='action$two' id='bulk-action-selector-" . esc_attr( $which ) . "'>\n";
        echo "<option value='-1' selected='selected'>" . __( 'Bulk Actions' ) . "</option>\n";

        foreach ( $this->_actions as $name => $title ) {
            $class = 'edit' == $name ? ' class="hide-if-no-js"' : '';

            echo "\t<option value='$name'$class>$title</option>\n";
        }

        echo "</select>\n";

        submit_button( __( 'Apply' ), 'action', '', false, array( 'id' => "doaction$two" ) );
        echo "\n";
    }

    /**
     * Get the current action selected from the bulk actions dropdown.
     *
     * @since 1.8
     * @access public
     *
     * @return string|bool The action name or False if no action was selected
     */
    public function current_action() {
        if ( isset( $_REQUEST['filter_action'] ) && !empty( $_REQUEST['filter_action'] ) )
            return false;

        if ( isset( $_REQUEST['action'] ) && -1 != $_REQUEST['action'] )
            return $_REQUEST['action'];

        if ( isset( $_REQUEST['action2'] ) && -1 != $_REQUEST['action2'] )
            return $_REQUEST['action2'];

        return false;
    }

    /**
     * Generate row actions div
     *
     * @since 1.8
     * @access protected
     *
     * @param array $actions The list of actions
     * @param bool $always_visible Whether the actions should be always visible
     * @return string
     */
    protected function row_actions( $actions, $always_visible = false ) {
        $action_count = count( $actions );
        $i = 0;

        if ( !$action_count )
            return '';

        $out = '<div class="' . ( $always_visible ? 'row-actions visible' : 'row-actions' ) . '">';
        foreach ( $actions as $action => $link ) {
            ++$i;
            ( $i == $action_count ) ? $sep = '' : $sep = ' | ';
            $out .= "<span class='$action'>$link$sep</span>";
        }
        $out .= '</div>';

        return $out;
    }

    /**
     * Get a list of sortable columns. The format is:
     * 'internal-name' => 'orderby'
     * or
     * 'internal-name' => array( 'orderby', true )
     *
     * The second format will make the initial sorting order be descending
     *
     * @since 1.8
     * @access protected
     *
     * @return array
     */
    protected function get_sortable_columns() {
        return array();
    }

    /**
     * Get a list of all, hidden and sortable columns, with filter applied
     *
     * @since 1.8
     * @access protected
     *
     * @return array
     */
    protected function get_column_info() {
        if ( isset( $this->_column_headers ) )
            return $this->_column_headers;

        $columns = get_column_headers( $this->screen );
        $hidden = get_hidden_columns( $this->screen );

        $sortable_columns = $this->get_sortable_columns();
        /**
         * Filter the list table sortable columns for a specific screen.
         *
         * The dynamic portion of the hook name, `$this->screen->id`, refers
         * to the ID of the current screen, usually a string.
         *
         * @since 3.5.0
         *
         * @param array $sortable_columns An array of sortable columns.
         */
        $_sortable = apply_filters( "manage_{$this->screen->id}_sortable_columns", $sortable_columns );

        $sortable = array();
        foreach ( $_sortable as $id => $data ) {
            if ( empty( $data ) )
                continue;

            $data = ( array ) $data;
            if ( !isset( $data[1] ) )
                $data[1] = false;

            $sortable[$id] = $data;
        }

        $this->_column_headers = array( $columns, $hidden, $sortable );

        return $this->_column_headers;
    }

    /**
     * Print column headers, accounting for hidden and sortable columns.
     *
     * @since 1.8
     * @access public
     *
     * @param bool $with_id Whether to set the id attribute or not
     */
    public function print_column_headers( $with_id = true ) {
        list( $columns, $hidden, $sortable ) = $this->get_column_info();

        $current_url = set_url_scheme( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] );
        $current_url = esc_url( remove_query_arg( 'paged', $current_url ) );

        if ( isset( $_GET['orderby'] ) )
            $current_orderby = $_GET['orderby'];
        else
            $current_orderby = '';

        if ( isset( $_GET['order'] ) && 'desc' == $_GET['order'] )
            $current_order = 'desc';
        else
            $current_order = 'asc';

        if ( !empty( $columns['cb'] ) ) {
            static $cb_counter = 1;
            $columns['cb'] = '<label class="screen-reader-text" for="cb-select-all-' . $cb_counter . '">' . __( 'Select All' ) . '</label>'
                    . '<input id="cb-select-all-' . $cb_counter . '" type="checkbox" />';
            $cb_counter++;
        }

        foreach ( $columns as $column_key => $column_display_name ) {
            $class = array( 'manage-column', "column-$column_key" );

            $style = '';
            if ( in_array( $column_key, $hidden ) )
                $style = 'display:none;';

            $style = ' style="' . $style . '"';

            if ( 'cb' == $column_key )
                $class[] = 'check-column';
            elseif ( in_array( $column_key, array( 'posts', 'comments', 'links' ) ) )
                $class[] = 'num';

            if ( isset( $sortable[$column_key] ) ) {
                list( $orderby, $desc_first ) = $sortable[$column_key];

                if ( $current_orderby == $orderby ) {
                    $order = 'asc' == $current_order ? 'desc' : 'asc';
                    $class[] = 'sorted';
                    $class[] = $current_order;
                } else {
                    $order = $desc_first ? 'desc' : 'asc';
                    $class[] = 'sortable';
                    $class[] = $desc_first ? 'asc' : 'desc';
                }

                $column_display_name = '<a href="' . esc_url( add_query_arg( compact( 'orderby', 'order' ), $current_url ) ) . '"><span>' . $column_display_name . '</span><span class="sorting-indicator"></span></a>';
            }

            $id = $with_id ? "id='$column_key'" : '';

            if ( !empty( $class ) )
                $class = "class='" . join( ' ', $class ) . "'";

            echo "<th scope='col' $id $class $style>$column_display_name</th>";
        }
    }

    /**
     * Generate the table navigation above or below the table
     *
     * @since 1.8
     * @access protected
     * @param string $which
     */
    protected function display_tablenav( $which ) {
        if ( 'top' == $which )
            wp_nonce_field( 'bulk-' . $this->_args['plural'] );
        ?>
        <div class="tablenav <?php echo esc_attr( $which ); ?>">

            <div class="alignleft actions bulkactions">
                <?php $this->bulk_actions( $which ); ?>
            </div>
            <?php
            $this->extra_tablenav( $which );
            $this->pagination( $which );
            ?>

            <br class="clear" />
        </div>
        <?php
    }

    protected function column_cb( $item ) {
        
    }

    /**
     * Generates the columns for a single row of the table
     *
     * @since 1.8
     * @access protected
     *
     * @param object $item The current item
     */
    protected function single_row_columns( $item ) {
        list( $columns, $hidden ) = $this->get_column_info();

        foreach ( $columns as $column_name => $column_display_name ) {
            $class = "class='$column_name column-$column_name'";

            $style = '';
            if ( in_array( $column_name, $hidden ) )
                $style = ' style="display:none;"';

            $attributes = "$class$style";

            if ( 'cb' == $column_name ) {
                echo '<th scope="row" class="check-column">';
                echo $this->column_cb( $item );
                echo '</th>';
            } elseif ( method_exists( $this, 'column_' . $column_name ) ) {
                echo "<td $attributes>";
                echo call_user_func( array( $this, 'column_' . $column_name ), $item );
                echo "</td>";
            } else {
                echo "<td $attributes>";
                echo $this->column_default( $item, $column_name );
                echo "</td>";
            }
        }
    }


    /**
     * Get an associative array ( id => link ) with the list
     * of views available on this table.
     *
     * @since 1.8
     * @access protected
     *
     * @return array
     */
    protected function get_views() {
        return array();
    }

    /**
     * Display the list of views available on this table.
     *
     * @since 1.8
     * @access public
     */
    public function views() {
        $views = $this->get_views();
        /**
         * Filter the list of available list table views.
         *
         * The dynamic portion of the hook name, `$this->screen->id`, refers
         * to the ID of the current screen, usually a string.
         *
         * @since 3.5.0
         *
         * @param array $views An array of available list table views.
         */
        $views = apply_filters( "views_{$this->screen->id}", $views );

        if ( empty( $views ) )
            return;

        echo "<ul class='subsubsub'>\n";
        foreach ( $views as $class => $view ) {
            $views[$class] = "\t<li class='$class'>$view";
        }
        echo implode( " |</li>\n", $views ) . "</li>\n";
        echo "</ul>";
    }

    /**
     * Display a view switcher
     *
     * @since 1.8
     * @access protected
     *
     * @param string $current_mode
     */
    protected function view_switcher( $current_mode ) {
        ?>
        <input type="hidden" name="mode" value="<?php echo esc_attr( $current_mode ); ?>" />
        <div class="view-switch">
            <?php
            foreach ( $this->modes as $mode => $title ) {
                $classes = array( 'view-' . $mode );
                if ( $current_mode == $mode )
                    $classes[] = 'current';
                printf(
                    "<a href='%s' class='%s' id='view-switch-$mode'><span class='screen-reader-text'>%s</span></a>\n", esc_url( add_query_arg( 'mode', $mode ) ), implode( ' ', $classes ), $title
                );
            }
            ?>
        </div>
    <?php
    }

}
