<?php
/**
 * @package Make Plus
 */

if ( ! class_exists( 'TTFMP_Section' ) ) :
/**
 * Prepares a new section for duplication.
 *
 * @since 1.0.0.
 */
class TTFMP_Section {
	/**
	 * The section type.
	 *
	 * @since 1.0.0.
	 *
	 * @var   string    The unique identifier for the section type.
	 */
	var $type = '';

	/**
	 * The data associated with the section.
	 *
	 * @since 1.0.0.
	 *
	 * @var   array    The array of data used to generate the section.
	 */
	var $data = array();

	/**
	 * The registered section information.
	 *
	 * @since 1.0.0.
	 *
	 * @var   array    The registered section information for the section type.
	 */
	var $info = array();

	/**
	 * Build a new section by passing the data and type.
	 *
	 * @since  1.0.0.
	 *
	 * @param  string           $type    The type of section being created.
	 * @param  array            $data    The data to build the section.
	 * @return TTFMP_Section
	 */
	public function __construct( $type, $data = array() ) {
		$sections = ttfmake_get_sections();

		// Save the section data
		$this->data = $data;

		// Verify the section type is valid
		if ( isset( $sections[ $type ] ) ) {
			$this->type = $type;
			$this->info = $sections[ $type ];
		}
	}

	/**
	 * Get the data for the section.
	 *
	 * @since  1.0.0.
	 *
	 * @return array    The data associated with the section.
	 */
	public function get_data() {
		return $this->data;
	}
}
endif;