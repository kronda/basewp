<?php

class TTF_Updater_API_Key {
	/**
	 * The one instance of TTF_Updater_API_Key.
	 *
	 * @since  1.0.0.
	 *
	 * @var TTF_Updater_API_Key
	 */
	private static $instance;

	/**
	 * Instantiate or return the one TTF_Updater_API_Key instance.
	 *
	 * @since  1.0.0.
	 *
	 * @return TTF_Updater_API_Key
	 */
	public static function instance() {
		if ( is_null( self::$instance ) )
			self::$instance = new self();

		return self::$instance;
	}

	/**
	 * Initiate the actions.
	 *
	 * @since  1.0.0.
	 *
	 * @return TTF_Updater_API_Key
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_tool_page' ) );
		add_action( 'admin_init', array( $this, 'add_settings' ) );
		add_filter( 'wp_redirect', array( $this, 'handle_redirect_after_save' ), 99, 2 );

		add_action( 'admin_footer', array( $this, 'display_message' ) );
	}

	/**
	 * Add a new admin page under the Appearance tab.
	 *
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	public function add_tool_page() {
		add_submenu_page(
			'tools.php',
			__( 'Updater API Key', 'make-plus' ),
			__( 'Updater API Key', 'make-plus' ),
			'manage_options',
			'updater_auth_page',
			array( $this, 'render_updater_auth_page' )
		);
	}

	/**
	 * Render the theme page wrapper.
	 *
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	public function render_updater_auth_page() {
		$is_redirect = ( isset( $_GET['action'] ) && ( isset( $_GET['plugin'] ) || isset( $_GET['theme'] ) ) );
	?>
		<div class="wrap">
			<h2><?php _e( 'Updater Authorization', 'make-plus' ); ?></h2>

			<?php if ( $msg = get_transient( 'updater-auth-' . get_current_user_id() ) ) : ?>
			<?php $status = ( isset( $msg['status'] ) ) ? $msg['status'] : ''; ?>
			<div id="setting-error-settings_updated" class="<?php echo esc_attr( $status ); ?> settings-error">
				<?php if ( isset( $msg['message'] ) ) : ?>
				<p>
					<?php echo esc_html( $msg['message'] ); ?>
				</p>
				<?php endif; ?>
				<?php if ( isset( $msg['details'] ) ) : ?>
					<ul>
						<?php foreach ( $msg['details'] as $detail ) : ?>
						<li>
							&nbsp;- <?php echo esc_html( $detail->msg ); ?> (<?php echo esc_html( $detail->code ); ?>)
						</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
			<?php endif; ?>

			<form action="options.php" method="post">
					<?php settings_fields( 'ttf-api-key' ); ?>
					<?php do_settings_sections( 'updater_auth_page' ); ?>
					<p class="submit" class="updater-toggle">
						<?php $label = ( true === $is_redirect ) ? __( 'Authorize Site and Complete Update', 'make-plus' ) : __( 'Authorize Site', 'make-plus' ); ?>
						<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo $label; ?>">
					</p>
					<?php if ( true === $is_redirect ) : ?>
						<?php if ( isset( $_GET['plugin'] ) ) : ?>
						<input type="hidden" name="redirect-plugin" value="<?php echo esc_attr( $_GET['plugin'] ); ?>">
						<?php elseif ( isset( $_GET['theme'] ) ) : ?>
						<input type="hidden" name="redirect-theme" value="<?php echo esc_attr( $_GET['theme'] ); ?>">
						<?php endif; ?>
					<input type="hidden" name="redirect-action" value="<?php echo esc_attr( $_GET['action'] ); ?>">
					<?php wp_nonce_field( 'redirect', 'redirect-nonce' ); ?>
					<?php endif; ?>
				</div>
			</form>
		</div>
		<script type="text/javascript">
			(function ($) {
				var $reAuth = $('.updater-re-auth'),
					$formWrapper = $('.updater-form-wrapper');

				$reAuth.on('click', function(evt) {
					evt.preventDefault();
					$formWrapper.show();
				})
			})(jQuery);
		</script>
		<?php delete_transient( 'updater-auth-' . get_current_user_id() ); ?>
	<?php
	}

	/**
	 * Register the settings, sections, and field
	 *
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	public function add_settings() {
		// Register an option to flag whether or not the site is authorized
		register_setting(
			'ttf-api-key',
			'ttf-api-key',
			array( $this, 'authorize_theme_and_domain' )
		);

		// Add the settings section to hold the interface
		add_settings_section(
			'updater_settings',
			'',
			array( $this, 'render_input_section' ),
			'updater_auth_page'
		);

		// Add the username field
		add_settings_field(
			'updater_render_email',
			__( 'E-mail Address', 'make-plus' ),
			array( $this, 'render_email' ),
			'updater_auth_page',
			'updater_settings'
		);

		// Add the password field
		add_settings_field(
			'updater_render_password',
			__( 'Password', 'make-plus' ),
			array( $this, 'render_password' ),
			'updater_auth_page',
			'updater_settings'
		);
	}

	/**
	 * Render a heading for the section.
	 *
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	public function render_input_section() {
		$api_key = get_option( 'ttf-api-key', false );

		if ( ! empty( $api_key ) ) {
			printf(
				'<h3 class="title updater-authed">%1$s <strong>%2$s</strong></h3>',
				__( 'API Key:', 'make-plus' ),
				$api_key
			);
			printf(
				'<p>%s</p>',
				__( 'Problems? <a href="#" class="updater-re-auth">Try authorizing again</a>.', 'make-plus' )
			);
		} else {
			printf(
				'<p>%s</p>',
				__( 'Please authorize your theme for automatic updates with the same credentials you use to sign into The Theme Foundry website. If you have trouble logging in, please <a href="https://thethemefoundry.com/support/">contact us in the Help Center</a>. The authorization process will send your current domain and product information to authorize this site.', 'make-plus' )
			);
		}
	?>
		<div class="updater-form-wrapper"<?php if ( ! empty( $api_key) ) : ?> style="display:none;"<?php endif; ?>>
	<?php
	}

	/**
	 * Render the email input.
	 *
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	public function render_email() {
		echo '<input type="text" name="updater-email" class="regular-text" value="" autofocus />';
	}

	/**
	 * Render the password input.
	 *
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	public function render_password() {
		echo '<input type="password" name="updater-password" class="regular-text" value="" />';
	}

	/**
	 * Authorize the theme and save the auth status on submission.
	 *
	 * @since  1.0.0.
	 *
	 * @param  mixed    $value    The input value.
	 * @return bool               The auth status.
	 */
	public function authorize_theme_and_domain( $value ) {
		if ( ! isset( $_POST['updater-email'] ) || ! isset( $_POST['updater-password'] ) ) {
			return 0;
		}

		// Grab the auth details
		$email    = $_POST['updater-email'];
		$password = $_POST['updater-password'];

		// Set the endpoint
		$auth_endpoint = 'https://thethemefoundry.com/key/auth';

		// Sometimes HTTP requests need a little extra time locally
		$timeout = ( defined( 'WP_DEBUG' ) && true === WP_DEBUG ) ? 20 : 5;

		// Make the request
		$response = wp_remote_post(
			$auth_endpoint,
			array(
				'headers' => array(
					'Content-Type' => 'application/x-www-form-urlencoded',
				),
				'body'    => array(
					'email'    => $email,
					'password' => $password,
					'domain'   => str_replace( array( 'http://', 'https://' ), array( '', '' ), get_option( 'siteurl' ) ),
					'item'     => sanitize_title_with_dashes( ttf_get_updater()->config['slug'] ),
				),
				'timeout' => $timeout,
			)
		);

		$response_code = (int) wp_remote_retrieve_response_code( $response );
		$response_body = json_decode( wp_remote_retrieve_body( $response ) );

		if ( 200 === $response_code && isset( $response_body->key ) ) {
			$msg = array(
				'status'  => 'updated',
				'message' => __( 'Your site is authorized for automatic updates from The Theme Foundry.', 'make-plus' ),
			);

			// Record the key
			$key = $response_body->key;

			if ( $key === preg_replace( '/[^a-z0-9]/i', '', $key ) ) {
				$this->add_msg( $msg );
				return $key;
			} else {
				$new_error       = new stdClass();
				$new_error->code = '513';
				$new_error->msg  = 'invalid key';
				$errors[]        = $new_error;

				$msg = array(
					'status'  => 'error',
					'message' => __( 'There was an error authorizing your site for automatic updates from The Theme Foundry.', 'make-plus' ),
					'details' => $errors,
				);

				$this->add_msg( $msg );
				return false;
			}
		} else {
			if ( is_wp_error( $response ) ) {
				$errors = array();
				foreach ( $response as $error ) {
					foreach ( $error as $code => $data ) {
						$new_error       = new stdClass();
						$new_error->code = $code;
						$new_error->msg  = $data[0];
						$errors[]        = $new_error;
					}
				}
			} else {
				$errors = (array) $response_body->errors;
			}

			$msg = array(
				'status'  => 'error',
				'message' => __( 'There was an error authorizing your site for automatic updates from The Theme Foundry.', 'make-plus' ),
				'details' => $errors,
			);

			$this->add_msg( $msg );
			return false;
		}
	}

	/**
	 * Detect when authorizing for updates and handle redirects properly.
	 *
	 * @since  1.0.0.
	 *
	 * @param  string    $location    The path to redirect to.
	 * @param  int       $status      Status code to use.
	 * @return string                 The new URL to redirect to.
	 */
	public function handle_redirect_after_save( $location, $status ) {
		// Is this screen accessed as part of the update process
		if ( ! ( isset( $_POST['redirect-plugin'] ) || isset( $_POST['redirect-theme'] ) ) || ! isset( $_POST['redirect-action'] ) || ! wp_verify_nonce( $_POST['redirect-nonce'], 'redirect' ) ) {
			return $location;
		}

		// Set up the GET args for the redirection
		$args = array(
			'action' => $_POST['redirect-action'],
		);

		if ( isset( $_POST['redirect-plugin'] ) ) {
			$args['plugin']   = $_POST['redirect-plugin'];
			$args['_wpnonce'] = wp_create_nonce( 'upgrade-plugin_' . $_POST['redirect-plugin'] );
		} else if ( isset( $_POST['redirect-theme'] ) ) {
			$args['theme']    = $_POST['redirect-theme'];
			$args['_wpnonce'] = wp_create_nonce( 'upgrade-theme_' . $_POST['redirect-theme'] );
		}

		if ( 'upgrade-plugin' === $_POST['redirect-action'] || 'upgrade-theme' === $_POST['redirect-action'] ) {
			return add_query_arg(
				$args,
				admin_url( 'update.php' )
			);
		} else {
			$this->add_msg( 'You are now authorized for updates from The Theme Foundry. Please try your update again.' );
			return admin_url( 'update-core.php' );
		}
	}

	/**
	 * Output a message if necessary.
	 *
	 * @since  1.0.1.
	 *
	 * @return void
	 */
	public function display_message() {
		global $pagenow;

		if ( 'update-core.php' === $pagenow && $msg = get_transient( 'updater-auth-' . get_current_user_id() ) ) : ?>
		<div id="api-auth-message" class="updated settings-error">
			<p>
				<?php echo esc_html( $msg ); ?>
			</p>
		</div>
		<?php delete_transient( 'updater-auth-' . get_current_user_id() );
		endif;
	}

	/**
	 * Save a message to display later.
	 *
	 * @since  1.0.0.
	 *
	 * @param  array    $message    Notice details.
	 * @return void
	 */
	public function add_msg( $message ) {
		set_transient( 'updater-auth-' . get_current_user_id(), $message, 30 );
	}
}

TTF_Updater_API_Key::instance();