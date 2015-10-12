<?php
/**
 * Simple decorators for embedded listing pages
 *
 * @package Views
 *
 * @since 1.8
 */

/**
 * Page decorator for embedded listing.
 *
 * Outputs just wrapper div tags and Views icon.
 *
 * @since 1.8
 */
class WPV_Embedded_Page_Decorator implements IWPV_Page_Decorator {


    public function render_page_start() {
        ?>
            <div class="wrap toolset-views">
                <div class="wpv-views-listing-page">
                    <div id="icon-views" class="icon32"></div>
        <?php
    }


    public function render_page_end() {
        ?>
                </div>
            </div>
        <?php
    }
}


/**
 * Title decorator for embedded listing.
 *
 * Outputs only a title in h2 tag. No additional information like in full Views.
 *
 * @since 1.8
 */
class WPV_Embedded_Title_Decorator implements IWPV_Title_Decorator {


    /**
     * @var string Title to be rendered.
     */
    protected $title;


    /**
     * @var string Label of the "Add new" button.
     */
    protected $add_new_label;

    /**
     * Class constructor.
     *
     * @param string $title Title to be rendered, without any surrounding tags.
     * @param string $add_new_label Label for the "Add new" button.
     */
    public function __construct( $title, $add_new_label ) {
        $this->title = sanitize_text_field( $title );
        $this->add_new_label = sanitize_text_field( $add_new_label );
    }


    /**
     * Render the title.
     *
     * @param string $search_term Search term if search results are showing. Empty string otherwise.
     */
    public function render_title( $search_term )
    {
        if( !empty( $search_term ) ) {
            $search_term = sprintf(
                '<span class="subtitle">%s</span>',
                sprintf( __( 'Search results for "%s"', 'wpv-views' ), $search_term ) );
        } else {
            $search_term = '';
        }

        printf(
            '<h2>%s <a href="#" class="add-new-h2 page-title-action js-open-promotional-message">%s</a>%s</h2><br />',
            $this->title,
            $this->add_new_label,
            $search_term );
    }
}


/**
 * "No items" message decorator for embedded listing.
 *
 * Outputs given message without any additional logic inside the appropriate div tag.
 *
 * @since 1.8
 */
class WPV_Embedded_Noitems_Decorator implements IWPV_Noitems_Decorator {


    /**
     * @var string Message string.
     */
    protected $message;


    /**
     * Class constructor.
     *
     * @param $message string Message string. Can containt HTML (will not be sanitized).
     */
    public function __construct( $message ) {
        $this->message = $message;
    }


    /**
     * Render the message.
     *
     * @param array $args Ignored.
     */
    public function render_no_items_content( $args ) {
        printf( '<div class="wpv-view-not-exist">%s</div>', $this->message );
    }
}



/**
 * Stub table decorator that only lists post titles. For testing and debugging purposes.
 *
 * @todo remove this after listing pages have been completely refactored.
 *
 * @since 1.8
 */
class WPV_Stub_Table_Decorator implements IWPV_Table_Decorator {


    public function render_table( $items ) {
        echo "<table>";

        foreach( $items as $item ) {
            printf( '<tr><td>%s</td></tr>', esc_html( $item->post_title ) );
        }

        echo "</table>";
    }
}


class WPV_SearchForm_Decorator implements IWPV_SearchForm_Decorator {


    protected $label;


    public function __construct( $label ) {
        $this->label = $label;
    }


    /**
     * Render the search form.
     *
     * @param array $args
     */
    public function render_search_form( $args )
    {
        $search_term = wpv_getarr( $args, 'search', '' );

        $hidden_fields = array(
            'page' => wpv_getarr( $args, 'page', '' ),
            'items_per_page' => (int) wpv_getarr( $args, 'items_per_page', '' ),
            'paged' => 1 );

        $hidden_fields_flat = array();
        foreach( $hidden_fields as $field_name => $field_value ) {
            $hidden_fields_flat[] = sprintf( '<input type="hidden" name="%s" value="%s" />', $field_name, $field_value );
        }
        $hidden_fields = implode( $hidden_fields_flat );

        ?>
        <div class="alignright">
            <form id="posts-filter" method="get">
                <?php echo $hidden_fields; ?>
                <p class="search-box">
                    <label class="screen-reader-text" for="post-search-input"><?php echo $this->label; ?></label>
                    <input type="search" id="post-search-input" name="search" value="<?php echo $search_term; ?>" />
                    <input type="submit" name="" id="search-submit" class="button" value="<?php echo htmlentities( $this->label, ENT_QUOTES ); ?>" />
                </p>
            </form>
        </div>
        <?php
    }
}


?>