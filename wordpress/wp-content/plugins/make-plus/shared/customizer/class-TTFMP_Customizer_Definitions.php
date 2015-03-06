<?php
/**
 * @package Make Plus
 */

/**
 * Class TTFMP_Customizer_Definitions
 *
 * Parent class for adding additional Customizer definitions to Make's standard theme options.
 *
 * @since 1.5.0.
 */
class TTFMP_Customizer_Definitions {
	/**
	 * The hook priority for the defaults filter.
	 *
	 * @since 1.5.0.
	 *
	 * @var int
	 */
	protected $defaults_priority = 20;

	/**
	 * The hook priority for the choices filter.
	 *
	 * @since 1.5.0.
	 *
	 * @var int
	 */
	protected $choices_priority = 20;

	/**
	 * The hook priority for the definitions filter.
	 *
	 * @since 1.5.0.
	 *
	 * @var int
	 */
	protected $definitions_priority = 20;

	/**
	 * Toggled when the init() function is run.
	 *
	 * @since 1.5.0.
	 *
	 * @var bool
	 */
	protected $initialized = false;

	/**
	 * Run the init function upon creation.
	 *
	 * Child classes with non-hook public methods may want to override this.
	 *
	 * @since 1.5.0.
	 */
	public function __construct() {
		if ( false === $this->initialized ) {
			$this->init();
			$this->initialized = true;
		}
	}

	/**
	 * Run functions to initialize the class.
	 *
	 * @since 1.5.0.
	 */
	protected function init() {
		$this->hooks();
	}

	/**
	 * Add functions to filter and action hooks.
	 *
	 * @since 1.5.0.
	 */
	protected function hooks() {
		// Defaults
		add_filter( 'make_setting_defaults', array( $this, 'defaults' ), $this->defaults_priority );

		// Choices
		add_filter( 'make_setting_choices', array( $this, 'choices' ), $this->choices_priority, 2 );

		// Definitions
		add_filter( 'make_customizer_sections', array( $this, 'definitions' ), $this->definitions_priority );
	}

	/**
	 * Placeholder filter for adding new theme options defaults to Make's master defaults array.
	 *
	 * @since 1.5.0.
	 *
	 * @param  array    $defaults    The master defaults array to modify.
	 *
	 * @return array
	 */
	public function defaults( $defaults ) {
		return $defaults;
	}

	/**
	 * Placeholder filter for specifying an array of choices for a given theme option.
	 *
	 * @since 1.5.0.
	 *
	 * @param  array     $choices    The current array of choices for the given theme option, if one exists.
	 * @param  string    $setting    The ID of the theme option.
	 *
	 * @return array
	 */
	public function choices( $choices, $setting ) {
		return $choices;
	}

	/**
	 * Placeholder filter for adding new Customizer section definitions to Make's master sections array.
	 *
	 * @since 1.5.0.
	 *
	 * @param  array    $sections    The master sections array to modify.
	 *
	 * @return array
	 */
	public function definitions( $sections ) {
		return $sections;
	}
}