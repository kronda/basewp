<?php
/**
 * @package Make Plus
 */

if ( ! class_exists( 'TTFMP_Page' ) ) :
/**
 * Prepares a new section for duplication.
 *
 * @since 1.0.0.
 */
class TTFMP_Page {
	/**
	 * The builder sections associated with the page.
	 *
	 * @since 1.0.0.
	 *
	 * @var   array    Array of builder sections represented as TTFMP_Section objects.
	 */
	var $sections = array();

	/**
	 * The existing page's ID.
	 *
	 * @since 1.0.0.
	 *
	 * @var   int    The page's ID.
	 */
	var $id = 0;

	/**
	 * The existing page's post data.
	 *
	 * @since 1.0.0.
	 *
	 * @var   object    The page's post data.
	 */
	var $post = '';

	/**
	 * Build a new page by passing the sections needed for the page.
	 *
	 * @since  1.0.0.
	 *
	 * @param  int           $id    Post ID associated with the page.
	 * @return TTFMP_Page
	 */
	public function __construct( $id = 0 ) {
		// Save the ID
		if ( 0 !== $id ) {
			$this->post = get_post( $id );
			if ( $this->post ) {
				$this->id = $id;
			}
		}
	}

	/**
	 * Creates a new page based on the object's section data.
	 *
	 * @since  1.0.0.
	 *
	 * @param  array       $override_data    Overrides for the post data array.
	 * @return int|bool                      Post ID if successful; False if not.
	 */
	public function insert( $override_data = array() ) {
		$save_class = ttfmake_get_builder_save();

		// Section data
		$section_data = $this->get_flattened_sections();

		// Generate the post content
		$post_content = $save_class->generate_post_content( $section_data );

		// Either create a new post or update and existing post
		if ( $this->get_id() > 0 ) {
			$post = get_post( $this->get_id() );

			// Append the new content
			$post->post_content .= $post_content;

			// Update the post
			$result = wp_update_post( $post );
		} else {
			// Prep the post data
			$data = wp_parse_args( $override_data, array(
				'post_type'      => 'page',
				'post_content'   => $post_content,
				'comment_status' => 'closed',
				'ping_status'    => 'closed',
			) );

			// Insert the post
			$result = wp_insert_post( $data );
		}

		if ( ! is_wp_error( $result ) ) {
			$this->id = $result;

			// Set the page templates
			update_post_meta( $this->id, '_wp_page_template', 'template-builder.php' );

			// Save the metadata
			$save_class->save_data( $section_data, $this->get_id() );
			return $this->get_id();
		} else {
			return false;
		}
	}

	/**
	 * Sets up the page according to the template definition.
	 *
	 * @since  1.0.0.
	 *
	 * @param  TTTFMP_Template    $template    The template object to define the page.
	 * @return void
	 */
	public function apply_template( $template ) {
		$this->add_sections( $template->get_sections() );
	}

	/**
	 * Add sections to the page.
	 *
	 * @since  1.0.0.
	 *
	 * @param  array    $sections    Array of TTFMP_Section objects that define sections.
	 * @return void
	 */
	public function add_sections( $sections ) {
		foreach ( $sections as $section ) {
			$this->add_section( $section );
		}
	}

	/**
	 * Add a section to the page.
	 *
	 * @since  1.0.0.
	 *
	 * @param  array    $section    A TTFMP_Section object that defines the section.
	 * @return void
	 */
	public function add_section( $section ) {
		$this->sections[] = $section;
	}

	/**
	 * Return all section data in a single array.
	 *
	 * @since  1.0.0.
	 *
	 * @return array    The data for each section in a single array.
	 */
	public function get_flattened_sections() {
		// Collector for data
		$data = array();

		// Iterate over each section object and get its data
		foreach ( $this->get_sections() as $section ) {
			$section_data                = $section->get_data();
			$data[ $section_data['id'] ] = $section->get_data();
		}

		return $data;
	}

	/**
	 * Get the sections for the object.
	 *
	 * @since  1.0.0.
	 *
	 * @return array    The builder sections associated with the object.
	 */
	public function get_sections() {
		return $this->sections;
	}

	/**
	 * Get the ID for the post.
	 *
	 * @since  1.0.0.
	 *
	 * @return array    The ID for the post associated with the object.
	 */
	public function get_id() {
		return $this->id;
	}
}
endif;