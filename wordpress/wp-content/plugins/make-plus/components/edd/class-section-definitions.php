<?php
/**
 * @package Make Plus
 */

if ( ! class_exists( 'TTFMP_EDD_Section_Definitions' ) ) :
/**
 * Collector for builder sections.
 *
 * @since 1.1.0.
 *
 * Class TTFMP_EDD_Section_Definitions
 */
class TTFMP_EDD_Section_Definitions {
	/**
	 * The one instance of TTFMP_EDD_Section_Definitions.
	 *
	 * @since 1.1.0.
	 *
	 * @var   TTFMP_EDD_Section_Definitions
	 */
	private static $instance;

	/**
	 * Instantiate or return the one TTFMP_EDD_Section_Definitions instance.
	 *
	 * @since  1.1.0.
	 *
	 * @return TTFMP_EDD_Section_Definitions
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Register the sections.
	 *
	 * @since  1.1.0.
	 *
	 * @return TTFMP_EDD_Section_Definitions
	 */
	public function __construct() {
		// Register all of the sections via the section API
		$this->register_downloads_section();

		// Add the section styles and scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		// Add Product Grid section settings
		add_filter( 'ttfmake_section_defaults', array( $this, 'section_defaults' ) );
		add_filter( 'ttfmake_section_choices', array( $this, 'section_choices' ), 10, 3 );

		// Add JS fix for "Insert download" button
		add_action( 'admin_head-post.php', array( $this, 'admin_head_script' ) );
		add_action( 'admin_head-post-new.php', array( $this, 'admin_head_script' ) );
	}

	/**
	 * Register the Product Grid section.
	 *
	 * @since  1.1.0.
	 *
	 * @return void
	 */
	public function register_downloads_section() {
		ttfmake_add_section(
			'downloads',
			__( 'Downloads', 'make-plus' ),
			trailingslashit( ttfmp_get_edd()->url_base ) . 'css/images/edd.png',
			__( 'Display your Easy Digital Downloads products in a grid layout.', 'make-plus' ),
			array( $this, 'save_downloads' ),
			'sections/builder-templates/downloads',
			'sections/front-end-templates/downloads',
			500,
			ttfmp_get_edd()->component_root
		);
	}

	/**
	 * Save the data for the Product Grid section.
	 *
	 * @since  1.1.0.
	 *
	 * @param  array    $data    The data from the $_POST array for the section.
	 * @return array             The cleaned data.
	 */
	public function save_downloads( $data ) {
		// Checkbox fields will not be set if they are unchecked.
		$checkboxes = array( 'thumb', 'price', 'addcart' );
		foreach ( $checkboxes as $key ) {
			if ( ! isset( $data[$key] ) ) {
				$data[$key] = 0;
			}
		}
		// Data to sanitize and save
		$defaults = array(
			'title' => ttfmake_get_section_default( 'title', 'edd-downloads' ),
			'columns' => ttfmake_get_section_default( 'columns', 'edd-downloads' ),
			'taxonomy' => ttfmake_get_section_default( 'taxonomy', 'edd-downloads' ),
			'sortby' => ttfmake_get_section_default( 'sortby', 'edd-downloads' ),
			'count' => ttfmake_get_section_default( 'count', 'edd-downloads' ),
			'thumb' => ttfmake_get_section_default( 'thumb', 'edd-downloads' ),
			'price' => ttfmake_get_section_default( 'price', 'edd-downloads' ),
			'addcart' => ttfmake_get_section_default( 'addcart', 'edd-downloads' ),
			'details' => ttfmake_get_section_default( 'details', 'edd-downloads' ),
		);
		$parsed_data = wp_parse_args( $data, $defaults );

		$clean_data = array();

		// Title
		$clean_data['title'] = $clean_data['label'] = apply_filters( 'title_save_pre', $parsed_data['title'] );

		// Columns
		$clean_data['columns'] = ttfmake_sanitize_section_choice( $parsed_data['columns'], 'columns', 'edd-downloads' );

		// Taxonomy
		$clean_data['taxonomy'] = ttfmake_sanitize_section_choice( $parsed_data['taxonomy'], 'taxonomy', 'edd-downloads' );

		// Sortby
		$clean_data['sortby'] = ttfmake_sanitize_section_choice( $parsed_data['sortby'], 'sortby', 'edd-downloads' );

		// Count
		$clean_data['count'] = (int) $parsed_data['count'];
		if ( $clean_data['count'] < -1 ) {
			$clean_data['count'] = abs( $clean_data['count'] );
		}

		// Thumb
		$clean_data['thumb'] = absint( $parsed_data['thumb'] );

		// Price
		$clean_data['price'] = absint( $parsed_data['price'] );

		// Add to cart
		$clean_data['addcart'] = absint( $parsed_data['addcart'] );

		// Sortby
		$clean_data['details'] = ttfmake_sanitize_section_choice( $parsed_data['details'], 'details', 'edd-downloads' );

		return $clean_data;
	}

	/**
	 * Add new section defaults.
	 *
	 * @since  1.1.0.
	 *
	 * @param  array $defaults The default section defaults.
	 * @return array                 The augmented section defaults.
	 */
	public function section_defaults( $defaults ) {
		$new_defaults = array(
			'edd-downloads-title' => '',
			'edd-downloads-columns' => 3,
			'edd-downloads-taxonomy' => 'all',
			'edd-downloads-sortby' => 'post_date-desc',
			'edd-downloads-count' => 9,
			'edd-downloads-thumb' => 1,
			'edd-downloads-price' => 1,
			'edd-downloads-addcart' => 1,
			'edd-downloads-details' => 'excerpt',
		);

		return array_merge( $defaults, $new_defaults );
	}

	/**
	 * Add new section choices.
	 *
	 * @since  1.1.0.
	 *
	 * @param  array $choices The existing choices.
	 * @param  string    $key             The key for the section setting.
	 * @param  string    $section_type    The section type.
	 * @return array                      The choices for the particular section_type / key combo.
	 */
	public function section_choices( $choices, $key, $section_type ) {
		if ( count( $choices ) > 1 || ! in_array( $section_type, array( 'edd-downloads' ) ) ) {
			return $choices;
		}

		$choice_id = "$section_type-$key";

		switch ( $choice_id ) {
			case 'edd-downloads-columns' :
				$choices = array(
					1 => __( '1', 'make-plus' ),
					2 => __( '2', 'make-plus' ),
					3 => __( '3', 'make-plus' ),
					4 => __( '4', 'make-plus' ),
				);
				break;
			case 'edd-downloads-taxonomy' :
				// Default
				$choices = array( 'all' => __( 'All download categories/tags', 'make-plus' ) );
				// Categories
				$product_category_terms = get_terms( 'download_category' );
				if ( ! empty( $product_category_terms ) ) {
					$category_slugs = array_map( array( $this, 'prefix_cat' ), wp_list_pluck( $product_category_terms, 'slug' ) );
					$category_names = wp_list_pluck( $product_category_terms, 'name' );
					$category_list = array_combine( $category_slugs, $category_names );
					$choices = array_merge(
						$choices,
						array( 'ttfmp-disabled1' => '--- ' . __( 'Download categories', 'make-plus' ) . ' ---' ),
						$category_list
					);
				}
				// Tags
				$product_tag_terms = get_terms( 'download_tag' );
				if ( ! empty( $product_tag_terms ) ) {
					$tag_slugs = array_map( array( $this, 'prefix_tag' ), wp_list_pluck( $product_tag_terms, 'slug' ) );
					$tag_names = wp_list_pluck( $product_tag_terms, 'name' );
					$tag_list = array_combine( $tag_slugs, $tag_names );
					$choices = array_merge(
						$choices,
						array( 'ttfmp-disabled2' => '--- ' . __( 'Download tags', 'make-plus' ) . ' ---' ),
						$tag_list
					);
				}
				break;
			case 'edd-downloads-sortby' :
				$choices = array(
					'post_date-desc' => __( 'Date: newest first', 'make-plus' ),
					'post_date-asc' => __( 'Date: oldest first', 'make-plus' ),
					'title-asc' => __( 'Name: A to Z', 'make-plus' ),
					'title-desc' => __( 'Name: Z to A', 'make-plus' ),
					'price-asc' => __( 'Price: low to high', 'make-plus' ),
					'price-desc' => __( 'Price: high to low', 'make-plus' ),
					'random' => __( 'Random', 'make-plus' ),
				);
				break;
			case 'edd-downloads-details' :
				$choices = array(
					'full' => __( 'Full content', 'make-plus' ),
					'excerpt' => __( 'Excerpt', 'make-plus' ),
					'none' => __( 'None', 'make-plus' ),
				);
				break;
		}

		return $choices;
	}

	/**
	 * Add a category prefix to a value.
	 *
	 * @since  1.1.0.
	 *
	 * @param  string    $value    The original value.
	 * @return string              The modified value.
	 */
	function prefix_cat( $value ) {
		return 'cat_' . $value;
	}

	/**
	 * Add a tag prefix to a value.
	 *
	 * @since  1.1.0.
	 *
	 * @param  string    $value    The original value.
	 * @return string              The modified value.
	 */
	function prefix_tag( $value ) {
		return 'tag_' . $value;
	}

	/**
	 * Enqueue the JS and CSS for the admin.
	 *
	 * @since  1.1.0.
	 *
	 * @param  string    $hook_suffix    The suffix for the screen.
	 * @return void
	 */
	public function admin_enqueue_scripts( $hook_suffix ) {
		// Have to be careful with this test because this function was introduced in Make 1.2.0.
		$post_type_supports_builder = ( function_exists( 'ttfmake_post_type_supports_builder' ) ) ? ttfmake_post_type_supports_builder( get_post_type() ) : false;
		$post_type_is_page          = ( 'page' === get_post_type() );

		// Only load resources if they are needed on the current page
		if ( ! in_array( $hook_suffix, array( 'post.php', 'post-new.php' ) ) || ( ! $post_type_supports_builder && ! $post_type_is_page ) ) {
			return;
		}

		// Add the section CSS
		wp_enqueue_style(
			'ttfmp-edd-sections',
			ttfmp_get_edd()->url_base . '/css/sections.css',
			array(),
			ttfmp_get_app()->version,
			'all'
		);
	}

	/**
	 * This script fixes the "Chosen" download select used by EDD's "Insert download" button for
	 * custom TinyMCE instances. A pull request has been accepted to fix this in the plugin, so this
	 * shouldn't be needed after version 2.0.
	 *
	 * @since 1.2.0.
	 *
	 * @return void
	 */
	public function admin_head_script() {
		// Have to be careful with this test because this function was introduced in Make 1.2.0.
		$post_type_supports_builder = ( function_exists( 'ttfmake_post_type_supports_builder' ) ) ? ttfmake_post_type_supports_builder( get_post_type() ) : false;
		$post_type_is_page          = ( 'page' === get_post_type() );

		if ( ( ! $post_type_supports_builder && ! $post_type_is_page ) || ( defined( 'EDD_VERSION' ) && true === version_compare( EDD_VERSION, '2.0', '>' ) ) ) {
			return;
		}
		?>
		<script type="application/javascript">
			(function($){
				// This fixes the Chosen box being 0px wide when the thickbox is opened
				$('#post').on('click', '.edd-thickbox', function() {
					$('.edd-select-chosen', '#choose-download').css('width', '100%');
				});
			}(jQuery));
		</script>
	<?php
	}
}
endif;

/**
 * Instantiate or return the one TTFMP_EDD_Section_Definitions instance.
 *
 * @since  1.1.0.
 *
 * @return TTFMP_EDD_Section_Definitions
 */
function ttfmp_edd_get_section_definitions() {
	return TTFMP_EDD_Section_Definitions::instance();
}

// Kick off the section definitions immediately
if ( is_admin() ) {
	ttfmp_edd_get_section_definitions();
}