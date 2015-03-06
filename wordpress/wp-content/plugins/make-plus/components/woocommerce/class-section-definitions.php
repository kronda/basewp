<?php
/**
 * @package Make Plus
 */

if ( ! class_exists( 'TTFMP_WooCommerce_Section_Definitions' ) ) :
/**
 * Collector for builder sections.
 *
 * @since 1.0.0.
 *
 * Class TTFMP_WooCommerce_Section_Definitions
 */
class TTFMP_WooCommerce_Section_Definitions {
	/**
	 * The one instance of TTFMP_WooCommerce_Section_Definitions.
	 *
	 * @since 1.0.0.
	 *
	 * @var   TTFMP_WooCommerce_Section_Definitions
	 */
	private static $instance;

	/**
	 * Instantiate or return the one TTFMP_WooCommerce_Section_Definitions instance.
	 *
	 * @since  1.0.0.
	 *
	 * @return TTFMP_WooCommerce_Section_Definitions
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
	 * @since  1.0.0.
	 *
	 * @return TTFMP_WooCommerce_Section_Definitions
	 */
	public function __construct() {
		// Register all of the sections via the section API
		$this->register_product_grid_section();

		// Add the section styles and scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		// Add Product Grid section settings
		add_filter( 'ttfmake_section_defaults', array( $this, 'section_defaults' ) );
		add_filter( 'ttfmake_section_choices', array( $this, 'section_choices' ), 10, 3 );
	}

	/**
	 * Register the Product Grid section.
	 *
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	public function register_product_grid_section() {
		ttfmake_add_section(
			'productgrid',
			__( 'Products', 'make-plus' ),
			trailingslashit( ttfmp_get_woocommerce()->url_base ) . 'css/images/woocommerce.png',
			__( 'Display your WooCommerce products in a grid layout.', 'make-plus' ),
			array( $this, 'save_product_grid' ),
			'sections/builder-templates/product-grid',
			'sections/front-end-templates/product-grid',
			500,
			ttfmp_get_woocommerce()->component_root
		);
	}

	/**
	 * Save the data for the Product Grid section.
	 *
	 * @since  1.0.0.
	 *
	 * @param  array    $data    The data from the $_POST array for the section.
	 * @return array             The cleaned data.
	 */
	public function save_product_grid( $data ) {
		// Checkbox fields will not be set if they are unchecked.
		$checkboxes = array( 'thumb', 'rating', 'price', 'addcart' );
		if ( get_option( 'woocommerce_enable_review_rating' ) === 'no' ) {
			unset( $checkboxes['rating'] );
		}
		foreach ( $checkboxes as $key ) {
			if ( ! isset( $data[$key] ) ) {
				$data[$key] = 0;
			}
		}
		// Data to sanitize and save
		$defaults = array(
			'title' => ttfmake_get_section_default( 'title', 'woocommerce-product-grid' ),
			'columns' => ttfmake_get_section_default( 'columns', 'woocommerce-product-grid' ),
			'type' => ttfmake_get_section_default( 'type', 'woocommerce-product-grid' ),
			'taxonomy' => ttfmake_get_section_default( 'taxonomy', 'woocommerce-product-grid' ),
			'sortby' => ttfmake_get_section_default( 'sortby', 'woocommerce-product-grid' ),
			'count' => ttfmake_get_section_default( 'count', 'woocommerce-product-grid' ),
			'thumb' => ttfmake_get_section_default( 'thumb', 'woocommerce-product-grid' ),
			'rating' => ttfmake_get_section_default( 'rating', 'woocommerce-product-grid' ),
			'price' => ttfmake_get_section_default( 'price', 'woocommerce-product-grid' ),
			'addcart' => ttfmake_get_section_default( 'addcart', 'woocommerce-product-grid' ),
		);
		$parsed_data = wp_parse_args( $data, $defaults );

		$clean_data = array();

		// Title
		$clean_data['title'] = $clean_data['label'] = apply_filters( 'title_save_pre', $parsed_data['title'] );

		// Columns
		$clean_data['columns'] = ttfmake_sanitize_section_choice( $parsed_data['columns'], 'columns', 'woocommerce-product-grid' );

		// Type
		$clean_data['type'] = ttfmake_sanitize_section_choice( $parsed_data['type'], 'type', 'woocommerce-product-grid' );

		// Taxonomy
		$clean_data['taxonomy'] = ttfmake_sanitize_section_choice( $parsed_data['taxonomy'], 'taxonomy', 'woocommerce-product-grid' );

		// Sort
		$clean_data['sortby'] = ttfmake_sanitize_section_choice( $parsed_data['sortby'], 'sortby', 'woocommerce-product-grid' );

		// Count
		$clean_count = (int) $parsed_data['count'];
		if ( $clean_count < 1 && -1 !== $clean_count ) {
			$clean_data['count'] = ttfmake_get_section_default( 'count', 'woocommerce-product-grid' );
		} else {
			$clean_data['count'] = $clean_count;
		}

		// Product name
		$clean_data['thumb'] = absint( $parsed_data['thumb'] );

		// Product description
		$clean_data['rating'] = absint( $parsed_data['rating'] );

		// Price
		$clean_data['price'] = absint( $parsed_data['price'] );

		// Add To Cart
		$clean_data['addcart'] = absint( $parsed_data['addcart'] );

		return $clean_data;
	}

	/**
	 * Add new section defaults.
	 *
	 * @since  1.0.0.
	 *
	 * @param  array $defaults The default section defaults.
	 * @return array                 The augmented section defaults.
	 */
	public function section_defaults( $defaults ) {
		$new_defaults = array(
			'woocommerce-product-grid-title' => '',
			'woocommerce-product-grid-columns' => 3,
			'woocommerce-product-grid-type' => 'all',
			'woocommerce-product-grid-taxonomy' => 'all',
			'woocommerce-product-grid-sortby' => 'menu_order',
			'woocommerce-product-grid-count' => 9,
			'woocommerce-product-grid-thumb' => 1,
			'woocommerce-product-grid-rating' => 1,
			'woocommerce-product-grid-price' => 1,
			'woocommerce-product-grid-addcart' => 1,
		);

		return array_merge( $defaults, $new_defaults );
	}

	/**
	 * Add new section choices.
	 *
	 * @since  1.0.0.
	 *
	 * @param  array $choices The existing choices.
	 * @param  string    $key             The key for the section setting.
	 * @param  string    $section_type    The section type.
	 * @return array                      The choices for the particular section_type / key combo.
	 */
	public function section_choices( $choices, $key, $section_type ) {
		if ( count( $choices ) > 1 || ! in_array( $section_type, array( 'woocommerce-product-grid' ) ) ) {
			return $choices;
		}

		$choice_id = "$section_type-$key";

		switch ( $choice_id ) {
			case 'woocommerce-product-grid-columns' :
				$choices = array(
					1 => __( '1', 'make-plus' ),
					2 => __( '2', 'make-plus' ),
					3 => __( '3', 'make-plus' ),
					4 => __( '4', 'make-plus' ),
				);
				break;
			case 'woocommerce-product-grid-type' :
				$choices = array(
					'all' => __( 'All products', 'make-plus' ),
					'featured' => __( 'Featured products', 'make-plus' ),
					'sale' => __( 'Sale products', 'make-plus' ),
				);
				break;
			case 'woocommerce-product-grid-sortby' :
				$choices = array(
					'menu_order' => __( 'Default sorting', 'make-plus' ),
					'popularity' => __( 'Popularity', 'make-plus' ),
					'rating'     => __( 'Average rating', 'make-plus' ),
					'date'       => __( 'Newness', 'make-plus' ),
					'price'      => __( 'Price: low to high', 'make-plus' ),
					'price-desc' => __( 'Price: high to low', 'make-plus' )
				);
				if ( get_option( 'woocommerce_enable_review_rating' ) === 'no' ) {
					unset( $choices['rating'] );
				}
				break;
			case 'woocommerce-product-grid-taxonomy' :
				// Default
				$choices = array( 'all' => __( 'All product categories/tags', 'make-plus' ) );
				// Categories
				$product_category_terms = get_terms( 'product_cat' );
				if ( ! empty( $product_category_terms ) ) {
					$category_slugs = array_map( array( $this, 'prefix_cat' ), wp_list_pluck( $product_category_terms, 'slug' ) );
					$category_names = wp_list_pluck( $product_category_terms, 'name' );
					$category_list = array_combine( $category_slugs, $category_names );
					$choices = array_merge(
						$choices,
						array( 'ttfmp-disabled1' => '--- ' . __( 'Product categories', 'make-plus' ) . ' ---' ),
						$category_list
					);
				}
				// Tags
				$product_tag_terms = get_terms( 'product_tag' );
				if ( ! empty( $product_tag_terms ) ) {
					$tag_slugs = array_map( array( $this, 'prefix_tag' ), wp_list_pluck( $product_tag_terms, 'slug' ) );
					$tag_names = wp_list_pluck( $product_tag_terms, 'name' );
					$tag_list = array_combine( $tag_slugs, $tag_names );
					$choices = array_merge(
						$choices,
						array( 'ttfmp-disabled2' => '--- ' . __( 'Product tags', 'make-plus' ) . ' ---' ),
						$tag_list
					);
				}
				break;
		}

		return $choices;
	}

	/**
	 * Add a category prefix to a value.
	 *
	 * @since  1.0.0.
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
	 * @since  1.0.0.
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
	 * @since  1.0.0.
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
			'ttfmp-woocommerce-sections',
			ttfmp_get_woocommerce()->url_base . '/css/sections.css',
			array(),
			ttfmp_get_app()->version,
			'all'
		);
	}
}
endif;

/**
 * Instantiate or return the one TTFMP_WooCommerce_Section_Definitions instance.
 *
 * @since  1.0.0.
 *
 * @return TTFMP_WooCommerce_Section_Definitions
 */
function ttfmp_woocommerce_get_section_definitions() {
	return TTFMP_WooCommerce_Section_Definitions::instance();
}

// Kick off the section definitions immediately
if ( is_admin() ) {
	ttfmp_woocommerce_get_section_definitions();
}