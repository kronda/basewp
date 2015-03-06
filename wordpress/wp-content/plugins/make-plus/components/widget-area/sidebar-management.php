<?php
/**
 * @package Make Plus
 */

if ( ! class_exists( 'TTFMP_Sidebar_Management' ) ) :
/**
 * Manage sidebars added and removed by the builder.
 *
 * @since 1.0.0.
 */
class TTFMP_Sidebar_Management {
	/**
	 * The one instance of TTFMP_Sidebar_Management.
	 *
	 * @since 1.0.0.
	 *
	 * @var   TTFMP_Sidebar_Management
	 */
	private static $instance;

	/**
	 * Instantiate or return the one TTFMP_Sidebar_Management instance.
	 *
	 * @since  1.0.0.
	 *
	 * @return TTFMP_Sidebar_Management
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
	 * @return TTFMP_Sidebar_Management
	 */
	public function __construct() {
		// For each stored sidebar, register a WordPress sidebar
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );

		// Delete sidebars when a widget area is removed
		add_action( 'deleted_post_meta', array( $this, 'deleted_post_meta' ), 10, 4 );

		// Delete sidebars when a text column is updated
		add_action( 'updated_post_meta', array( $this, 'updated_post_meta' ), 10, 4 );

		// Delete sidebars when a post is deleted
		add_action( 'after_delete_post', array( $this, 'after_delete_post' ) );
	}

	/**
	 * For each stored sidebar, register a WordPress sidebar.
	 *
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	public function widgets_init() {
		$sidebars = $this->get_registered_sidebars();

		if ( is_array( $sidebars ) ) {
			foreach ( $sidebars as $id => $sidebar ) {
				$pieces = $this->parse_sidebar_id( $sidebar['id'] );

				// Do not register the sidebar if the corresponding page is in the trash
				if ( isset( $pieces['page_id'] ) && 'trash' !== get_post_status( $pieces['page_id'] ) ) {
					$id = 'ttfmp-' . $sidebar['id'];

					register_sidebar( array(
						'id'            => $id,
						'name'          => stripslashes( $this->get_sidebar_title( $sidebar['id'] ) ),
						'description'   => stripslashes( $this->get_sidebar_description( $sidebar['id'] ) ),
						'before_widget' => '<aside id="%1$s" class="widget %2$s">',
						'after_widget'  => '</aside>',
						'before_title'  => '<h4 class="widget-title">',
						'after_title'   => '</h4>'
					) );
				}
			}
		}
	}

	/**
	 * Generate a title for the sidebar.
	 *
	 * @since  1.0.0.
	 *
	 * @param  string    $id    The sidebar ID.
	 * @return string           The generated description.
	 */
	public function get_sidebar_title( $id ) {
		// Attempt to get the label from the array of registered sidebars
		$sidebar = $this->get_registered_sidebar( $id );

		if ( ! empty( $sidebar ) && isset( $sidebar['label'] ) && '' !== $sidebar['label'] ) {
			$label = esc_html( $sidebar['label'] );
		} else {
			$sidebar_information = $this->parse_sidebar_id( $id );
			$label               = __( 'Sidebar', 'make-plus' ) . ' ' . $sidebar_information['page_id'] . '-' . $sidebar_information['section_id'] . '-' . $sidebar_information['column_id'];
		}

		return $label;
	}

	/**
	 * Generate a description for the sidebar.
	 *
	 * @since  1.0.0.
	 *
	 * @param  string    $id    The sidebar ID.
	 * @return string           The generated description.
	 */
	public function get_sidebar_description( $id ) {
		// Attempt to get the label from the array of registered sidebars
		$sidebar = $this->get_registered_sidebar( $id );

		if ( ! empty( $sidebar ) && isset( $sidebar['label'] ) && '' !== $sidebar['label'] ) {
			$label = esc_html( $sidebar['label'] );
		} else {
			$sidebar_information = $this->parse_sidebar_id( $id );
			$label               = $sidebar_information['page_id'] . '-' . $sidebar_information['section_id'] . '-' . $sidebar_information['column_id'];
		}

		return __(
			sprintf(
				'Add widgets to the "%s" widget area.',
				$label
			),
			'make-plus'
		);
	}

	/**
	 * Adds a sidebar via the component pieces.
	 *
	 * @since  1.0.0.
	 *
	 * @param  int       $page_id       The ID of the page.
	 * @param  string    $section_id    The section ID. Value is numeric, but will be greater than the max int value.
	 * @param  int       $column_id     The column number.
	 * @param  string    $label         The label for the sidebar.
	 * @return void
	 */
	public function register_sidebar( $page_id, $section_id, $column_id, $label ) {
		$id = absint( $page_id ) . '-' . $this->clean_section_id( $section_id ) . '-' . absint( $column_id );
		$this->add_sidebar_to_array( $id, $label );
	}

	/**
	 * Removes a sidebar via the component pieces.
	 *
	 * @since  1.0.0.
	 *
	 * @param  int       $page_id       The ID of the page.
	 * @param  string    $section_id    The section ID. Value is numeric, but will be greater than the max int value.
	 * @param  int       $column_id     The column number.
	 * @return void
	 */
	public function deregister_sidebar( $page_id, $section_id, $column_id ) {
		$id = absint( $page_id ) . '-' . $this->clean_section_id( $section_id ) . '-' . absint( $column_id );
		$this->remove_sidebar_from_array( $id );
	}

	/**
	 * Get all of the sidebars that are registered for the builder.
	 *
	 * @since  1.0.0.
	 *
	 * @return array    The list of sidebars registered for the builder.
	 */
	public function get_registered_sidebars() {
		$sidebars = get_option( 'ttfmp-builder-sidebars', false );
		if ( false === $sidebars ) {
			// Check for old theme mod
			$sidebars = get_theme_mod( 'builder-sidebars', array() );
			update_option( 'ttfmp-builder-sidebars', $sidebars );
		}

		return $sidebars;
	}

	/**
	 * Get a single sidebar from the list of registered sidebars.
	 *
	 * @since  1.0.0.
	 *
	 * @param  string    $id    The sidebar ID.
	 * @return array            The sidebar.
	 */
	public function get_registered_sidebar( $id ) {
		$the_sidebar = array();
		$sidebars    = $this->get_registered_sidebars();

		if ( isset( $sidebars[ $id ] ) ) {
			$the_sidebar = $sidebars[ $id ];
		}

		return $the_sidebar;
	}

	/**
	 * Add another sidebar to the array of sidebars.
	 *
	 * @since  1.0.0.
	 *
	 * @param  string    $id       The ID of the sidebar to add.
	 * @param  string    $label    The label for the sidebar.
	 * @return void
	 */
	public function add_sidebar_to_array( $id, $label ) {
		$sidebars = $this->get_registered_sidebars();
		$sidebars = ( is_array( $sidebars ) ) ? $sidebars : array();

		// Replace existing sidebar with new sidebar
		$sidebars[ $id ] = array(
			'id'    => $id,
			'label' => $label
		);

		$this->save_sidebars( $sidebars );
	}

	/**
	 * Remove a single sidebar by ID from the array of sidebars.
	 *
	 * @since  1.0.0.
	 *
	 * @param  string    $id    The ID of the sidebar to remove.
	 * @return void
	 */
	public function remove_sidebar_from_array( $id ) {
		$sidebars = $this->get_registered_sidebars();

		if ( isset( $sidebars[ $id ] ) ) {
			unset( $sidebars[ $id ] );
			$this->save_sidebars( $sidebars );
		}
	}

	/**
	 * Save an array of sidebars to the theme mod.
	 *
	 * @since  1.0.0.
	 *
	 * @param  array    $sidebars    Array of sidebars to save.
	 * @return void
	 */
	public function save_sidebars( $sidebars ) {
		update_option( 'ttfmp-builder-sidebars', $sidebars );
	}

	/**
	 * Break a sidebar ID into component pieces.
	 *
	 * @since  1.0.0.
	 *
	 * @param  string    $id    The sidebar ID (e.g., 4928-1251344524124-1)
	 * @return array            An array containing the page ID, section ID and column number.
	 */
	public function parse_sidebar_id( $id ) {
		$pieces = explode( '-', $id );

		if ( isset( $pieces[0] ) && isset( $pieces[1] ) && isset( $pieces[2] ) ) {
			return array(
				'page_id'    => absint( $pieces[0] ),
				'section_id' => $this->clean_section_id( $pieces[1] ),
				'column_id'  => absint( $pieces[2] ),
			);
		} else {
			return array();
		}
	}

	/**
	 * Sanitizes a string to only return numbers.
	 *
	 * @since  1.0.0.
	 *
	 * @param  string    $id    The section ID.
	 * @return string           The sanitized ID.
	 */
	public static function clean_section_id( $id ) {
		return preg_replace( '/[^0-9]/', '', $id );
	}

	/**
	 * When a widget area post meta value is deleted, delete the corresponding sidebar.
	 *
	 * @since  1.0.0.
	 *
	 * @param  array     $meta_ids       An array of deleted metadata entry IDs.
	 * @param  int       $object_id      Object ID.
	 * @param  string    $meta_key       Meta key.
	 * @param  mixed     $_meta_value    Meta value.
	 * @return void
	 */
	public function deleted_post_meta( $meta_ids, $object_id, $meta_key, $_meta_value ) {
		if ( $this->meta_key_is_widget_area( $meta_key ) ) {
			// Get the page ID, the section ID, and the column number, which will allow for deleting the sidebar
			$pieces     = explode( ':', $meta_key );
			$page_id    = $object_id;
			$section_id = ( isset( $pieces[1] ) ) ? $pieces[1] : 0;
			$column_id  = ( isset( $pieces[3] ) ) ? $pieces[3] : 0;

			// Remove the sidebar
			if ( $page_id > 0 && $section_id > 0 && $column_id > 0 ) {
				$this->deregister_sidebar( $page_id, $section_id, $column_id );
			}
		}
	}

	/**
	 * When a text column is updated, potentially remove a sidebar.
	 *
	 * @since  1.0.0.
	 *
	 * @param  int       $meta_id        ID of updated metadata entry.
	 * @param  int       $object_id      Object ID.
	 * @param  string    $meta_key       Meta key.
	 * @param  mixed     $_meta_value    Meta value.
	 * @return void
	 */
	public function updated_post_meta( $meta_id, $object_id, $meta_key, $_meta_value ) {
		if ( $this->meta_key_is_widget_area( $meta_key ) ) {
			if ( 0 === (int) $_meta_value ) {
				// Get the page ID, the section ID, and the column number, which will allow for deleting the sidebar
				$pieces     = explode( ':', $meta_key );
				$page_id    = $object_id;
				$section_id = ( isset( $pieces[1] ) ) ? $pieces[1] : 0;
				$column_id  = ( isset( $pieces[3] ) ) ? $pieces[3] : 0;

				// Remove the sidebar
				if ( $page_id > 0 && $section_id > 0 && $column_id > 0 ) {
					$this->deregister_sidebar( $page_id, $section_id, $column_id );
				}
			}
		}
	}

	/**
	 * Removes sidebars used in a page when the page is deleted.
	 *
	 * @since  1.0.0.
	 *
	 * @param  int    $post_id    The post ID of the post being deleted.
	 * @return void
	 */
	public function after_delete_post( $post_id ) {
		$sidebars = $this->get_registered_sidebars();

		// Iterate through sidebars, removing any with a page ID matching the deleted page's ID
		foreach ( $sidebars as $id => $sidebar ) {
			$pieces = $this->parse_sidebar_id( $sidebar['id'] );

			// Remove the sidebar if a match is found
			if ( (int) $post_id === (int) $pieces['page_id'] ) {
				$this->remove_sidebar_from_array( $id );
			}
		}
	}

	/**
	 * Determine if a meta key represents a widget area post meta value.
	 *
	 * @since  1.0.0.
	 *
	 * @param  string    $key    The key to test.
	 * @return bool              True if the key is a widget area value. False if it is not.
	 */
	public function meta_key_is_widget_area( $key ) {
		$return = false;

		if ( 0 === strpos( $key, '_ttfmake:' ) ) {
			if ( false !== strpos( $key, ':widget-area' ) ) {
				$return = true;
			}
		}

		return $return;
	}
}
endif;

if ( ! function_exists( 'ttfmp_get_sidebar_management' ) ) :
/**
 * Instantiate or return the one TTFMP_Sidebar_Management instance.
 *
 * @since  1.0.0.
 *
 * @return TTFMP_Sidebar_Management
 */
function ttfmp_get_sidebar_management() {
	return TTFMP_Sidebar_Management::instance();
}
endif;

ttfmp_get_sidebar_management();

if ( ! function_exists( 'ttfmp_register_sidebar' ) ) :
/**
 * Adds a sidebar via the component pieces.
 *
 * @since  1.0.0.
 *
 * @param  int       $page_id       The ID of the page.
 * @param  string    $section_id    The section ID. Value is numeric, but will be greater than the max int value.
 * @param  int       $column_id     The column number.
 * @param  string    $label         The label for the sidebar.
 * @return void
 */
function ttfmp_register_sidebar( $page_id, $section_id, $column_id, $label ) {
	ttfmp_get_sidebar_management()->register_sidebar( $page_id, $section_id, $column_id, $label );
}
endif;