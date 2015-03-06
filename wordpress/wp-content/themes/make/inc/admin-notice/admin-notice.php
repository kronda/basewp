<?php
/**
 * @package Make
 */

if ( ! class_exists( 'TTFMAKE_Admin_Notice' ) ) :
/**
 * Class TTFMAKE_Admin_Notice
 *
 * Display notices in the WP Admin
 *
 * @since 1.4.9.
 */
class TTFMAKE_Admin_Notice {
	/**
	 * The array of registered notices.
	 *
	 * @since 1.4.9.
	 *
	 * @var    array    The array of registered notices.
	 */
	var $notices = array();

	/**
	 * The path to the notice template file.
	 *
	 * @since 1.4.9.
	 *
	 * @var    string    The path to the notice template file.
	 */
	var $template = '';

	/**
	 * The single instance of the class.
	 *
	 * @since 1.4.9.
	 *
	 * @var    object    The single instance of the class.
	 */
	private static $instance;

	/**
	 * Instantiate or return the one TTFMAKE_Admin_Notice instance.
	 *
	 * @since  1.4.9.
	 *
	 * @return TTFMAKE_Admin_Notice
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Construct the object.
	 *
	 * @since 1.4.9.
	 *
	 * @return TTFMAKE_Admin_Notice
	 */
	public function __construct() {}

	/**
	 * Initialize the formatting functionality and hook into WordPress.
	 *
	 * @since 1.4.9.
	 *
	 * @return void
	 */
	public function init() {
		// Define template path
		$this->template = trailingslashit( dirname( __FILE__ ) ) . 'template.php';

		// Register Ajax action
		add_action( 'wp_ajax_make_hide_notice', array( $this, 'handle_ajax' ) );

		// Hook up notices
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
	}

	/**
	 * Register an admin notice.
	 *
	 * @since 1.4.9.
	 *
	 * @param string    $id         A unique ID string for the admin notice.
	 * @param string    $message    The content of the admin notice.
	 * @param array     $args       Array of configuration parameters for the admin notice.
	 * @return void
	 */
	public function register_admin_notice( $id, $message, $args = array() ) {
		// Prep id
		$id = sanitize_title_with_dashes( $id );

		// Prep message
		$allowedtags = wp_kses_allowed_html();
		$allowedtags['a']['target'] = true;
		$message = wp_kses( $message, $allowedtags );

		// Prep args
		$defaults = array(
			'cap'     => 'switch_themes',      // User capability to see the notice
			'dismiss' => true,                 // Whether notice is dismissable
			'screen'  => array( 'index.php' ), // Which screens to show the notice on
			'type'    => 'info',               // success, warning, error, info
		);
		$args = wp_parse_args( $args, $defaults );

		// Register the notice
		if ( $message ) {
			$this->notices[ $id ] = array_merge( array( 'message' => $message ), $args );
		}
	}

	/**
	 * Get the visible notices for a specified screen.
	 *
	 * @since 1.4.9.
	 *
	 * @param  string    $screen    The screen to display the notices on.
	 * @return array                Array of notices to display on the specified screen.
	 */
	private function get_notices( $screen = '' ) {
		if ( ! $screen ) {
			return $this->notices;
		}

		// Get the array of notices that the current user has already dismissed
		$user_id = get_current_user_id();
		$dismissed = get_user_meta( $user_id, 'ttfmake-dismissed-notices', true );

		// Remove notices that don't meet requirements
		$notices = $this->notices;
		foreach( $notices as $id => $args ) {
			if (
				! in_array( $screen, (array) $args['screen'] ) ||
				! current_user_can( $args['cap'] ) ||
				in_array( $id, (array) $dismissed )
			) {
				unset( $notices[ $id ] );
			}
		}

		return $notices;
	}

	/**
	 * Wrapper function for admin_notices hook that sets everything up.
	 *
	 * @since 1.4.9.
	 *
	 * @return void
	 */
	public function admin_notices() {
		global $pagenow;
		$current_notices = $this->get_notices( $pagenow );

		if ( ! empty( $current_notices ) && file_exists( $this->template ) ) {
			add_action( 'admin_print_footer_scripts', array( $this, 'print_admin_notices_js' ) );
			$this->render_notices( $current_notices );
		}
	}

	/**
	 * Output the markup and styles for admin notices.
	 *
	 * @since 1.4.9.
	 *
	 * @param  array    $notices    The array of notices to render.
	 * @return void
	 */
	private function render_notices( $notices ) {
		global $wp_version;
		?>
		<style type="text/css">
			.ttfmake-dismiss {
				display: block;
				float: right;
				margin: 0.5em 0;
				padding: 2px;
			}
			.rtl .ttfmake-dismiss {
				float: left;
			}
		</style>
	<?php
		foreach ( $notices as $id => $args ) {
			$message = $args['message'];
			$dismiss = $args['dismiss'];
			$type    = $args['type'];
			$nonce   = wp_create_nonce( 'ttfmake_dismiss_' . $id );

			// CSS and JS in older version of WP rely on the error and updated classes.
			$legacy_class = '';
			if ( version_compare( $wp_version, '4.1', '<=' ) ) {
				if ( in_array( $type, array( 'warning', 'error' ) ) ) {
					$legacy_class = 'error';
				} else if ( in_array( $type, array( 'success', 'info' ) ) ) {
					$legacy_class = 'updated';
				}
			}

			// Load the template
			require( $this->template );
		}
	}

	/**
	 * Output the JS to hide admin notices.
	 *
	 * @since 1.4.9.
	 *
	 * @return void
	 */
	public function print_admin_notices_js() {
		?>
		<script type="application/javascript">
			/* Make admin notices */
			/* <![CDATA[ */
			( function( $ ) {
				$('.notice').on('click', '.ttfmake-dismiss', function(evt) {
					evt.preventDefault();

					var $target = $(evt.target),
						$parent = $target.parents('.notice').first(),
						nonce   = $target.data('nonce'),
						id      = $parent.attr('id').replace('ttfmake-notice-', '');

					$.post(
						ajaxurl,
						{
							action : 'make_hide_notice',
							nid    : id,
							nonce  : nonce
						}
					).done(function(data) {
						if (1 === parseInt(data, 10)) {
							$parent.fadeOut('slow', function() {
								$(this).remove();
							});
						}
					});
				});
			} )( jQuery );
			/* ]]> */
		</script>
	<?php
	}

	/**
	 * Process the Ajax request to hide an admin notice.
	 *
	 * @since 1.4.9.
	 *
	 * @return void
	 */
	public function handle_ajax() {
		// Check requirements
		if (
			! defined( 'DOING_AJAX' ) ||
			true !== DOING_AJAX ||
			! isset( $_POST['nid'] ) ||
			! isset( $_POST['nonce'] ) ||
			! wp_verify_nonce( $_POST['nonce'], 'ttfmake_dismiss_' . $_POST['nid'] )
		) {
			wp_die();
		}

		// Get array of dismissed notices
		$user_id = get_current_user_id();
		$dismissed = get_user_meta( $user_id, 'ttfmake-dismissed-notices', true );
		if ( ! $dismissed ) {
			$dismissed = array();
		}

		// Add a new notice to the array
		$id = $_POST['nid'];
		$dismissed[] = $id;
		$success = update_user_meta( $user_id, 'ttfmake-dismissed-notices', $dismissed );

		// Return a success response.
		if ( $success ) {
			echo 1;
		}
		wp_die();
	}
}

/**
 * Instantiate or return the one TTFMAKE_Admin_Notice instance.
 *
 * @since  1.4.9.
 *
 * @return TTFMAKE_Admin_Notice
 */
function ttfmake_admin_notice() {
	return TTFMAKE_Admin_Notice::instance();
}

/**
 * Wrapper function to register an admin notice.
 *
 * @since 1.4.9.
 *
 * @param string    $id         A unique ID string for the admin notice.
 * @param string    $message    The content of the admin notice.
 * @param array     $args       Array of configuration parameters for the admin notice.
 * @return void
 */
function ttfmake_register_admin_notice( $id, $message, $args ) {
	ttfmake_admin_notice()->register_admin_notice( $id, $message, $args );
}

/**
 * Fire the init function immediately.
 */
ttfmake_admin_notice()->init();
endif;