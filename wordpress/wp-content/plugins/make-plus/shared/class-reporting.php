<?php
/**
 * @package Make Plus
 */

if ( ! class_exists( 'TTFMP_Reporting' ) ) :
/**
 * A notice system for use within the admin.
 *
 * @since 1.1.0.
 */
class TTFMP_Reporting {
	/**
	 * Key used to store the message in a transient.
	 *
	 * @since 1.1.0.
	 *
	 * @var   string
	 */
	public $key;

	/**
	 * The message.
	 *
	 * @since 1.1.0.
	 *
	 * @var   string
	 */
	public $value;

	/**
	 * Construct that will set the message if provided one.
	 *
	 * @since  1.1.0.
	 *
	 * @param  string             $message       The message to display.
	 * @param  string             $type          The message type (e.g., error, success, notice).
	 * @param  string             $code          The message code.
	 * @param  string             $user_login    The user login for the user setting the message.
	 * @return TTFMP_Reporting
	 */
	public function __construct( $message = '', $type = 'success', $code = '', $user_login = '' ) {
		// Get the user login value
		$user = get_user_by( 'login', $user_login );
		if ( false === $user ) {
			$user = wp_get_current_user();
		}

		$clean_user_login = $user->user_login;

		// Construct and cache the key
		$this->key = $this->_build_key( $clean_user_login );

		// Store the message if it is available
		if ( '' !== $message ) {
			$this->record( $message, $type, $code );
		}
	}

	/**
	 * Record the message and associated data to a transient.
	 *
	 * @since  1.1.0.
	 *
	 * @param  string    $message    The message to display.
	 * @param  string    $type       The message type (e.g., error, success, notice).
	 * @param  string    $code       The message code.
	 * @return bool                  True if set; false if not.
	 */
	public function record( $message, $type = 'success', $code = '' ) {
		$value = $this->sanitize_report_data( $message, $type, $code );
		return set_transient( $this->key, $value, 500 );
	}

	/**
	 * Clean the data before saving to the database.
	 *
	 * @since  1.1.0.
	 *
	 * @param  string    $message    The message to display.
	 * @param  string    $type       The message type (e.g., error, success, notice).
	 * @param  string    $code       The message code.
	 * @return array                 The clean data.
	 */
	public function sanitize_report_data( $message, $type = 'success', $code = '' ) {
		// If it is a WP_Error object, deconstruct it
		if ( is_wp_error( $message ) ) {
			$code    = $message->get_error_code();
			$message = $message->get_error_message();
		}

		// Put the pieces of data together after sanitization
		$value = array(
			'code'    => sanitize_key( $code ),
			'message' => sanitize_text_field( $message ),
			'type'    => sanitize_key( $type ),
		);

		// Return the clean data
		return apply_filters( 'ttfmp_reporting_sanitize_report_data', $value, $message, $type, $code );
	}

	/**
	 * Get the stored message and data.
	 *
	 * @since  1.1.0.
	 *
	 * @return array    The array of notice data.
	 */
	public function get() {
		if ( is_null( $this->value ) ) {
			$this->value = get_transient( $this->key );
		}
		return apply_filters( 'ttfmp_reporting_get', $this->value );
	}

	/**
	 * Delete the message.
	 *
	 * @since  1.1.0.
	 *
	 * @return bool    True on success; False on failure.
	 */
	public function delete() {
		return delete_transient( $this->key );
	}

	/**
	 * Create a key to access the stored message data.
	 *
	 * @since  1.1.0.
	 *
	 * @param  string    $user_login    The user login for the user storing the data.
	 * @return string                   The generate key.
	 */
	private function _build_key( $user_login ) {
		$key = 'ttf-rep-' . md5( $user_login );
		return apply_filters( 'ttfmp_reporting_build_key', $key, $user_login );
	}
}
endif;