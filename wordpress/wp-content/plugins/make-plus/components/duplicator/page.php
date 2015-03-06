<?php
/**
 * @package Make Plus
 */

if ( ! class_exists( 'TTFMP_Page_Duplicator' ) ) :
/**
 * Bootstrap the page duplication functionality.
 *
 * @since 1.1.0.
 */
class TTFMP_Page_Duplicator {
	/**
	 * The one instance of TTFMP_Page_Duplicator.
	 *
	 * @since 1.1.0.
	 *
	 * @var   TTFMP_Page_Duplicator
	 */
	private static $instance;

	/**
	 * Instantiate or return the one TTFMP_Page_Duplicator instance.
	 *
	 * @since  1.1.0.
	 *
	 * @return TTFMP_Page_Duplicator
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Create a new section.
	 *
	 * @since  1.1.0.
	 *
	 * @return TTFMP_Page_Duplicator
	 */
	public function __construct() {
		// Add duplicate link to list of page actions
		add_filter( 'page_row_actions', array( $this, 'page_row_actions' ), 10, 2 );

		// Look for URL to create page copy
		add_action( 'admin_init', array( $this, 'create_page_copy_router' ), 11 );

		// Print the error message if necessary
		add_action( 'admin_footer-edit.php', array( $this, 'admin_footer' ) );

		// Print the success message
		add_action( 'edit_form_top', array( $this, 'edit_form_top' ) );

		// Add duplicator button in the page
		add_action( 'post_submitbox_misc_actions', array( $this, 'post_submitbox_misc_actions' ) );
	}

	/**
	 * Add link to initiate duplication of page.
	 *
	 * @since  1.1.0.
	 *
	 * @param  array      $actions    Array of page row actions.
	 * @param  WP_Post    $post       The current post object.
	 * @return array                  Modified page row actions.
	 */
	public function page_row_actions( $actions, $post ) {
		if ( 'template-builder.php' === get_page_template_slug( $post ) ) {
			$url = add_query_arg(
				array(
					'ttfmp-duplicate-nonce' => wp_create_nonce( 'duplicate' ),
					'page-id'               => $post->ID,
				),
				admin_url( 'options.php' )
			);
			$actions['duplicate'] = '<a href="' . esc_url( $url ) . '" title="' . __( 'Duplicate Page', 'make-plus' ) . '">' . __( 'Duplicate', 'make-plus' ) . '</a>';
		}

		return $actions;
	}

	/**
	 * Detect request to create a page copy and route the request.
	 *
	 * @since  1.1.0.
	 *
	 * @return void
	 */
	public function create_page_copy_router() {
		if ( ! isset( $_GET['ttfmp-duplicate-nonce'] ) || ! wp_verify_nonce( $_GET['ttfmp-duplicate-nonce'], 'duplicate' ) ) {
			return;
		}

		if ( ! isset( $_GET['page-id'] ) ) {
			return;
		}

		// Attempt to get the page to copy
		$this_page = get_post( $_GET['page-id'] );

		if ( ! is_null( $this_page ) ) {
			$new_page_id = $this->create_page_copy( $this_page, $_GET['page-id'] );

			if ( 0 !== (int) $new_page_id && ! is_wp_error( $new_page_id ) ) {
				// Add success message
				new TTFMP_Reporting(
					sprintf(
						__( 'Your page was successfully copied.', 'make-plus' ),
						absint( $_GET['page-id'] )
					),
					'updated'
				);

				// Redirect to created page
				$redirect = add_query_arg(
					array(
						'post'   => $new_page_id,
						'action' => 'edit',
					),
					admin_url( 'post.php' )
				);
				wp_safe_redirect( $redirect );
			} else {
				// Set the error and redirect
				new TTFMP_Reporting( __( 'Error occurred while trying to create a page copy. Please try again.', 'make-plus' ), 'error' );
				wp_safe_redirect( wp_get_referer() );
			}
		} else {
			// Set the error and redirect
			new TTFMP_Reporting( __( 'An unexpected error occurred while trying to create a page copy. Please try again.', 'make-plus' ), 'error' );
			wp_safe_redirect( wp_get_referer() );
		}

		exit();
	}

	/**
	 * Create a new page.
	 *
	 * @since  1.1.0.
	 *
	 * @param  WP_Post    $page       The Post object for the page to be duplicated.
	 * @param  int        $page_id    The ID for the page to be duplicated.
	 * @return int                    The ID of the newly created page.
	 */
	public function create_page_copy( $page, $page_id ) {
		// Generate the new title
		$copy_text = '(' . _x( 'Copy', 'label for a duplicated page', 'make-plus' ) . ')';
		$title     = ( ! empty( $page->post_title ) ) ? $page->post_title . ' ' . $copy_text : $copy_text;

		// Replace the page's title
		$page->post_title = $title;

		// Reset the ID so it does not update the existing post
		$page->ID = 0;

		// Save the post
		$new_page_id = wp_insert_post( $page );

		// Process metadata if post was added successfully
		if ( 0 !== (int) $new_page_id || ! is_wp_error( $new_page_id ) ) {
			// Get the target's post metadata
			$meta = get_post_custom( $page_id );

			// Save each metadata value to the new post
			foreach ( $meta as $key => $value_as_array ) {
				if ( isset( $value_as_array[0] ) ) {
					add_post_meta( $new_page_id, $key, maybe_unserialize( $value_as_array[0] ) );
				}
			}
		}

		return $new_page_id;
	}

	/**
	 * If there is an error to report, display it.
	 *
	 * @since  1.1.0.
	 *
	 * @return void
	 */
	public function admin_footer() {
		if ( ! isset( $_GET['post_type'] ) || 'page' !== $_GET['post_type'] ) {
			return;
		}

		$reporter = new TTFMP_Reporting();
		$error    = $reporter->get();

		// Now that we have messages, delete them from cache so they only appear once
		$reporter->delete();

		if ( false !== $error && isset( $error['type'] ) && isset( $error['message'] ) ) : ?>
			<div id="message" class="ttfmp-message <?php echo esc_attr( $error['type'] ); ?> below-h2" style="display:none;"><p><?php echo esc_html( $error['message'] ); ?></p></div>
			<script type="text/javascript">
				(function($){
					var $subsubsub = $('.subsubsub'),
						$message = $('.ttfmp-message');

					$subsubsub.before($message.show());
				})(jQuery)
			</script>
		<?php endif;
	}

	/**
	 * If there is a success message to report, display it.
	 *
	 * @since  1.1.0.
	 *
	 * @return void
	 */
	public function edit_form_top() {
		$reporter = new TTFMP_Reporting();
		$error    = $reporter->get();

		// Now that we have messages, delete them from cache so they only appear once
		$reporter->delete();

		if ( false !== $error && isset( $error['type'] ) && isset( $error['message'] ) ) : ?>
			<div id="message" class="ttfmp-message <?php echo esc_attr( $error['type'] ); ?> below-h2"><p><?php echo esc_html( $error['message'] ); ?></p></div>
		<?php endif;
	}

	/**
	 * Display button for duplicating posts.
	 *
	 * @since  1.1.0.
	 *
	 * @return void
	 */
	public function post_submitbox_misc_actions() {
		global $pagenow;

		if ( ( 'post.php' !== $pagenow && 'post-new.php' !== $pagenow ) || 'page' !== get_post_type() ) {
			return;
		}

		$url = add_query_arg(
			array(
				'ttfmp-duplicate-nonce' => wp_create_nonce( 'duplicate' ),
				'page-id'               => get_the_ID(),
			),
			admin_url( 'options.php' )
		);
	?>
		<div class="misc-pub-section ttfmake-duplicator">
			<a style="float:right;" class="ttfmp-duplicator-button button" href="<?php echo esc_url( $url ); ?>"><?php _e( 'Duplicate Page', 'make-plus' ); ?></a>
			<div class="clear"></div>
		</div>
	<?php
	}
}
endif;

if ( ! function_exists( 'ttfmp_get_page_duplicator' ) ) :
/**
 * Instantiate or return the one TTFMP_Page_Duplicator instance.
 *
 * @since  1.1.0.
 *
 * @return TTFMP_Page_Duplicator
 */
function ttfmp_get_page_duplicator() {
	return TTFMP_Page_Duplicator::instance();
}
endif;

ttfmp_get_page_duplicator();