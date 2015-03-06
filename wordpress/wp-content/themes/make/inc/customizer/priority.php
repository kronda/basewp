<?php
/**
 * @package Make
 */

if ( ! class_exists( 'TTFMAKE_Prioritizer' ) ) :
/**
 * Class TTFMAKE_Prioritizer
 *
 * Increment upward from a starting number with each call to add().
 *
 * @since 1.0.0.
 */
class TTFMAKE_Prioritizer {
	/**
	 * The starting priority.
	 *
	 * @since 1.0.0.
	 *
	 * @var   int    The priority used to start the incrementor.
	 */
	var $initial_priority = 0;

	/**
	 * The amount to increment for each step.
	 *
	 * @since 1.0.0.
	 *
	 * @var   int    The amount to increment for each step.
	 */
	var $increment = 0;

	/**
	 * Holds the reference to the current priority value.
	 *
	 * @since 1.0.0.
	 *
	 * @var   int    Holds the reference to the current priority value.
	 */
	var $current_priority = 0;

	/**
	 * Set the initial properties on init.
	 *
	 * @param  int                    $initial_priority    Value to being the counter.
	 * @param  int                    $increment           Value to increment the counter by.
	 * @return TTFMAKE_Prioritizer
	 */
	function __construct( $initial_priority = 100, $increment = 100 ) {
		$this->initial_priority = absint( $initial_priority );
		$this->increment        = absint( $increment );
		$this->current_priority = $this->initial_priority;
	}

	/**
	 * Get the current value.
	 *
	 * @since  1.0.0.
	 *
	 * @return int    The current priority value.
	 */
	public function get() {
		return $this->current_priority;
	}

	/**
	 * Increment the priority.
	 *
	 * @since  1.0.0.
	 *
	 * @param  int    $increment    The value to increment by.
	 * @return void
	 */
	public function inc( $increment = 0 ) {
		if ( 0 === $increment ) {
			$increment = $this->increment;
		}
		$this->current_priority += absint( $increment );
	}

	/**
	 * Increment by the $this->increment value.
	 *
	 * @since  1.0.0.
	 *
	 * @return int    The priority value.
	 */
	public function add() {
		$priority = $this->get();
		$this->inc();
		return $priority;
	}

	/**
	 * Change the current priority and/or increment value.
	 *
	 * @since  1.3.0.
	 *
	 * @param  null|int    $new_priority     The new current priority.
	 * @param  null|int    $new_increment    The new increment value.
	 * @return void
	 */
	public function set( $new_priority = null, $new_increment = null ) {
		if ( ! is_null( $new_priority ) ) {
			$this->current_priority = absint( $new_priority );
		}
		if ( ! is_null( $new_increment ) ) {
			$this->increment = absint( $new_increment );
		}
	}

	/**
	 * Reset the counter.
	 *
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	public function reboot() {
		$this->current_priority = $this->initial_priority;
	}
}
endif;