<?php
/**
 * @package Make Plus
 */

if ( ! class_exists( 'TTTFMP_Template' ) ) :
/**
 * Defines a template for use with the templating system.
 *
 * @since 1.0.0.
 */
class TTTFMP_Template {
	/**
	 * Name of the template.
	 *
	 * @since 1.0.0.
	 *
	 * @var   string    The name of the template. Only [a-z0-9\-] are acceptable.
	 */
	var $name = '';

	/**
	 * Label for the template.
	 *
	 * @since 1.0.0.
	 *
	 * @var   string    The label for the template.
	 */
	var $label = '';

	/**
	 * Array of TTFMP_Section objects.
	 *
	 * @since 1.0.0.
	 *
	 * @var   array    Array of TTFMP_Section objects in the order to appear in the template.
	 */
	var $sections = array();

	/**
	 * Construct the object.
	 *
	 * @since  1.0.0.
	 *
	 * @param  string             $name        The name of the template.
	 * @param  string             $label       The label for the template.
	 * @param  array              $sections    Array of TTFMP_Section objects.
	 * @return TTTFMP_Template
	 */
	public function __construct( $name, $label, $sections ) {
		$this->name  = sanitize_title( $name );
		$this->label = $label;
		$this->add_sections( $sections );
	}

	/**
	 * Add a section to the sections instance variable.
	 *
	 * @since  1.0.0.
	 *
	 * @param  TTFMP_Section    $section    A section as a TTFMP_Section object.
	 * @return void
	 */
	public function add_section( $section ) {
		$this->sections[] = $section;
	}

	/**
	 * Add an array of sections to the sections instance variable.
	 *
	 * @since  1.0.0.
	 *
	 * @param  array    $sections    Array of TTFMP_Section objects.
	 * @return void
	 */
	public function add_sections( $sections ) {
		foreach ( $sections as $section ) {
			$this->add_section( $section );
		}
	}

	/**
	 * Get the name for the template.
	 *
	 * @since  1.0.0.
	 *
	 * @return string    The name for the template.
	 */
	public function get_name() {
		return $this->name;
	}

	/**
	 * Get the label for the template.
	 *
	 * @since  1.0.0.
	 *
	 * @return string    The label for the template.
	 */
	public function get_label() {
		return $this->label;
	}

	/**
	 * Get the object's sections.
	 *
	 * @since  1.0.0.
	 *
	 * @return array    The object's sections.
	 */
	public function get_sections() {
		return $this->sections;
	}
}
endif;