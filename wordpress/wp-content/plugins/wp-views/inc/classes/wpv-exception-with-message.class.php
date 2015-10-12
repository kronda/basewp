<?php

/**
 * Runtime exception with message for the user.
 *
 * (Currently it is used only in WPV_Content_Template, as a way of passing error message from property setter,
 * through AJAX call handler into JS on a CT edit page.)
 *
 * The only difference from RuntimeException is the additional $user_message argument.
 *
 * @since 1.9
 */
class WPV_RuntimeExceptionWithMessage extends RuntimeException {

    /* Values that can be used as exception codes. */
    const EXCEPTION_UNDEFINED_CODE = 0;
    const EXCEPTION_VALUE_ALREADY_USED = 1;

	protected $user_message;

	public function __construct( $message = '' , $user_message = '', $code = 0, $previous = NULL ) {
		parent::__construct( $message, $code, $previous );
		$this->user_message = $user_message;
	}

	final public function getUserMessage() {
		return $this->user_message;
	}
}