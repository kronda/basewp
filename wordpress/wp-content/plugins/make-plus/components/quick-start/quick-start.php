<?php
/**
 * @package Make Plus
 */

if ( ! class_exists( 'TTFMP_Quick_Start' ) ) :
/**
 * Bootstrap the layout template functionality.
 *
 * @since 1.0.0.
 */
class TTFMP_Quick_Start {
	/**
	 * Name of the component.
	 *
	 * @since 1.0.0.
	 *
	 * @var   string    The name of the component.
	 */
	var $component_slug = 'quick-start';

	/**
	 * Path to the component directory (e.g., /var/www/mysite/wp-content/plugins/make-plus/components/my-component).
	 *
	 * @since 1.0.0.
	 *
	 * @var   string    Path to the component directory
	 */
	var $component_root = '';

	/**
	 * File path to the plugin main file (e.g., /var/www/mysite/wp-content/plugins/make-plus/components/my-component/my-component.php).
	 *
	 * @since 1.0.0.
	 *
	 * @var   string    Path to the plugin's main file.
	 */
	var $file_path = '';

	/**
	 * The URI base for the plugin (e.g., http://domain.com/wp-content/plugins/make-plus/my-component).
	 *
	 * @since 1.0.0.
	 *
	 * @var   string    The URI base for the plugin.
	 */
	var $url_base = '';

	/**
	 * The one instance of TTFMP_Quick_Start.
	 *
	 * @since 1.0.0.
	 *
	 * @var   TTFMP_Quick_Start
	 */
	private static $instance;

	/**
	 * Instantiate or return the one TTFMP_Quick_Start instance.
	 *
	 * @since  1.0.0.
	 *
	 * @return TTFMP_Quick_Start
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
	 * @since  1.0.0.
	 *
	 * @return TTFMP_Quick_Start
	 */
	public function __construct() {
		// Set the main paths for the component
		$this->component_root = ttfmp_get_app()->component_base . '/' . $this->component_slug;
		$this->file_path      = $this->component_root . '/' . basename( __FILE__ );
		$this->url_base       = untrailingslashit( plugins_url( '/', __FILE__ ) );

		// Include needed files
		require_once $this->component_root . '/class-page.php';
		require_once $this->component_root . '/class-section.php';
		require_once $this->component_root . '/class-template.php';
		require_once $this->component_root . '/class-template-collector.php';

		// Define the templates
		add_action( 'admin_init', array( $this, 'define_templates' ) );

		// Add the import content input
		add_action( 'edit_form_after_title', array( $this, 'edit_form_after_title' ) );

		// Look for URL to create new page with template
		add_action( 'admin_init', array( $this, 'process_new_page' ), 11 );

		// Add the JS/CSS
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
	}

	/**
	 * Add the template definitions.
	 *
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	public function define_templates() {
		require_once $this->component_root . '/define-templates.php';
	}

	/**
	 * Display the input for importing content.
	 *
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	public function edit_form_after_title() {
		if ( 'page' !== get_post_type() ) {
			return;
		}

		$section_ids        = get_post_meta( get_the_ID(), '_ttfmake-section-ids', true );
		$additional_classes = ( ! empty( $section_ids ) ) ? ' ttfmp-import-message-hide' : '';
	?>
		<div id="message" class="updated below-h2 ttfmp-import-message<?php echo esc_attr( $additional_classes ); ?>">
			<p class="ttfmp-import-message-left">
				<strong><?php _e( 'Quick Start', 'make-plus' ); ?></strong><br />
				<?php _e( 'Import content into this page. To begin, select a category&hellip;', 'make-plus' ); ?>
			</p>
			<p class="ttfmp-import-message-right">
				<select name="ttfmp-import-content" id="ttfmp-import-content">
					<?php $i = 0; ?>
					<?php foreach ( ttfmp_get_template_collector()->get_templates() as $id => $template ) : ?>
					<?php if ( 0 === $i ) $first_template_name = $id; ?>
					<option value="<?php echo esc_attr( $id ); ?>">
						<?php echo esc_html( $template->get_label() ); ?>
					</option>
					<?php $i++; ?>
					<?php endforeach; ?>
				</select>
				<a href="#" id="ttfmp-import-link" class="button">
					<?php _e( 'Import content', 'make-plus' ); ?>
				</a>
			</p>
			<div class="clear"></div>
		</div>
	<?php
	}

	/**
	 * Responds to the ?ttfmp_template=text&ttfmp_template_nonce=sf8ash229s URL to create a builder template page.
	 *
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	public function process_new_page() {
		if ( ! isset( $_GET['ttfmp_template'] ) ) {
			return;
		}

		if ( ! isset( $_GET['ttfmp_template_nonce'] ) || ! wp_verify_nonce( $_GET['ttfmp_template_nonce'], 'create' ) ) {
			return;
		}

		// Verify that the template is registered
		$template_names = ttfmp_get_template_collector()->get_template_names();
		$template       = ( in_array( $_GET['ttfmp_template'], $template_names ) ) ? $_GET['ttfmp_template'] : '';

		if ( empty( $template ) ) {
			return;
		}

		// Build a new page based on the template used
		$template_object = ttfmp_get_template_collector()->get_template( $template );
		$page            = new TTFMP_Page();
		$page->apply_template( $template_object );

		// Add override data
		$override = array(
			'post_title' => ( isset( $_GET['ttfmp_title'] ) ) ? $_GET['ttfmp_title'] : '',
		);

		// Create the new page
		$id = $page->insert( $override );

		// Redirect to the new post on success and back to the page listing on failure
		if ( false === $id ) {
			wp_redirect( admin_url( 'edit.php?post_type=page' ) );
			exit();
		} else {
			wp_redirect( admin_url( 'post.php?post=' . absint( $id ) . '&action=edit' ) );
			exit();
		}
	}

	/**
	 * Add JS/CSS on page edit screen.
	 *
	 * @since  1.0.0.
	 *
	 * @param  string    $hook_suffix    The current page slug.
	 * @return void
	 */
	public function admin_enqueue_scripts( $hook_suffix ) {
		if ( ( 'post.php' !== $hook_suffix && 'post-new.php' !== $hook_suffix ) || 'page' !== get_post_type() ) {
			return;
		}

		wp_enqueue_script(
			'ttfmp-quick-start',
			$this->url_base . '/js/quick-start.js',
			array( 'jquery' ),
			ttfmp_get_app()->version,
			true
		);

		wp_localize_script(
			'ttfmp-quick-start',
			'ttfmpLayoutTemplates',
			array(
				'base'   => admin_url( 'options.php' ),
				'nonce'  => wp_create_nonce( 'create' ),
				'postID' => (string) get_the_ID()
			)
		);

		wp_enqueue_style(
			'ttfmp-quick-start',
			$this->url_base . '/css/quick-start.css',
			array(),
			ttfmp_get_app()->version
		);
	}
}
endif;

if ( ! function_exists( 'ttfmp_get_quick_start' ) ) :
/**
 * Instantiate or return the one TTFMP_Quick_Start instance.
 *
 * @since  1.0.0.
 *
 * @return TTFMP_Quick_Start
 */
function ttfmp_get_quick_start() {
	return TTFMP_Quick_Start::instance();
}
endif;

ttfmp_get_quick_start();

if ( ! function_exists( 'ttfmp_get_template_url' ) ) :
/**
 * Generate a URL for adding a builder page template.
 *
 * @since  1.0.0.
 *
 * @param  string    $template_name    The ID for a registered template.
 * @param  int       $post_id          The ID for a post if replacing content.
 * @return string                      The composed link.
 */
function ttfmp_get_template_url( $template_name, $post_id = 0 ) {
	return add_query_arg(
		array(
			'ttfmp_template'       => $template_name,
			'ttfmp_template_nonce' => wp_create_nonce( 'create' ),
			'ttfmp_post_id'        => $post_id,
		),
		admin_url( 'options.php' )
	);
}
endif;

if ( ! function_exists( 'ttfmp_sideload_image' ) ) :
/**
 * Add an image as a WordPress attachment.
 *
 * @since  1.0.0.
 *
 * @param  string        $file_path    The path to the image.
 * @param  string        $desc         An optional image description.
 * @return int|object                  WP_Error on failure; Post ID on success.
 */
function ttfmp_sideload_image( $file_path, $desc = '' ) {
	preg_match( '/[^\?]+\.(jpe?g|jpe|gif|png)\b/i', $file_path, $matches );

	$file_array = array(
		'tmp_name' => $file_path,
		'name'     => basename( $matches[0] ),
	);

	return media_handle_sideload( $file_array, 0, $desc );
}
endif;