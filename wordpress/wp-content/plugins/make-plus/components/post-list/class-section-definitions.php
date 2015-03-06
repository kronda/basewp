<?php
/**
 * @package Make Plus
 */

if ( ! class_exists( 'TTFMP_Post_List_Section_Definitions' ) ) :
/**
 * Collector for builder sections.
 *
 * @since 1.2.0.
 *
 * Class TTFMP_Post_List_Section_Definitions
 */
class TTFMP_Post_List_Section_Definitions {
	/**
	 * The one instance of TTFMP_Post_List_Section_Definitions.
	 *
	 * @since 1.2.0.
	 *
	 * @var   TTFMP_Post_List_Section_Definitions
	 */
	private static $instance;

	/**
	 * Instantiate or return the one TTFMP_Post_List_Section_Definitions instance.
	 *
	 * @since  1.2.0.
	 *
	 * @return TTFMP_Post_List_Section_Definitions
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
	 * @since  1.2.0.
	 *
	 * @return TTFMP_Post_List_Section_Definitions
	 */
	public function __construct() {
		// Register all of the sections via the section API
		$this->register_post_list_section();

		// Add the section styles and scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		// Add section settings
		add_filter( 'ttfmake_section_defaults', array( $this, 'section_defaults' ) );
		add_filter( 'ttfmake_section_choices', array( $this, 'section_choices' ), 10, 3 );
	}

	/**
	 * Register the section.
	 *
	 * @since  1.2.0.
	 *
	 * @return void
	 */
	public function register_post_list_section() {
		ttfmake_add_section(
			'postlist',
			__( 'Posts List', 'make-plus' ),
			trailingslashit( ttfmp_get_post_list()->url_base ) . 'css/images/post-list.png',
			__( 'Display your posts or pages in a list or grid layout.', 'make-plus' ),
			array( $this, 'save_post_list' ),
			'sections/builder-templates/post-list',
			'sections/front-end-templates/post-list',
			500,
			ttfmp_get_post_list()->component_root
		);
	}

	/**
	 * Save the data for the Product Grid section.
	 *
	 * @since  1.2.0.
	 *
	 * @param  array    $data    The data from the $_POST array for the section.
	 * @return array             The cleaned data.
	 */
	public function save_post_list( $data ) {
		// Checkbox fields will not be set if they are unchecked.
		$checkboxes = array( 'show-title', 'show-date', 'show-excerpt', 'show-author', 'show-categories', 'show-tags', 'show-comments' );
		foreach ( $checkboxes as $key ) {
			if ( ! isset( $data[$key] ) ) {
				$data[$key] = 0;
			}
		}
		// Data to sanitize and save
		$defaults = array(
			'title' => ttfmake_get_section_default( 'title', 'post-list' ),
			'columns' => ttfmake_get_section_default( 'columns', 'post-list' ),
			'type' => ttfmake_get_section_default( 'type', 'post-list' ),
			'sortby' => ttfmake_get_section_default( 'sortby', 'post-list' ),
			'keyword' => ttfmake_get_section_default( 'keyword', 'post-list' ),
			'count' => ttfmake_get_section_default( 'count', 'post-list' ),
			'offset' => ttfmake_get_section_default( 'offset', 'post-list' ),
			'taxonomy' => ttfmake_get_section_default( 'taxonomy', 'post-list' ),
			'show-title' => ttfmake_get_section_default( 'show-title', 'post-list' ),
			'show-date' => ttfmake_get_section_default( 'show-date', 'post-list' ),
			'show-excerpt' => ttfmake_get_section_default( 'show-excerpt', 'post-list' ),
			'show-author' => ttfmake_get_section_default( 'show-author', 'post-list' ),
			'show-categories' => ttfmake_get_section_default( 'show-categories', 'post-list' ),
			'show-tags' => ttfmake_get_section_default( 'show-tags', 'post-list' ),
			'show-comments' => ttfmake_get_section_default( 'show-comments', 'post-list' ),
			'thumbnail' => ttfmake_get_section_default( 'thumbnail', 'post-list' ),
		);
		$parsed_data = wp_parse_args( $data, $defaults );

		$clean_data = array();

		// Title
		$clean_data['title'] = $clean_data['label'] = apply_filters( 'title_save_pre', $parsed_data['title'] );

		// Columns
		$clean_data['columns'] = ttfmake_sanitize_section_choice( $parsed_data['columns'], 'columns', 'post-list' );

		// Type
		$clean_data['type'] = ttfmake_sanitize_section_choice( $parsed_data['type'], 'type', 'post-list' );

		// Sort by
		$clean_data['sortby'] = ttfmake_sanitize_section_choice( $parsed_data['sortby'], 'sortby', 'post-list' );

		// Keyword
		$clean_data['keyword'] = esc_html( $parsed_data['keyword'] );

		// Count
		$clean_count = (int) $parsed_data['count'];
		if ( $clean_count < -1 ) {
			$clean_count = abs( $clean_count );
		}
		$clean_data['count'] = $clean_count;

		// Offset
		$clean_data['offset'] = absint( $parsed_data['offset'] );

		// Taxonomy
		$clean_data['taxonomy'] = ttfmake_sanitize_section_choice( $parsed_data['taxonomy'], 'taxonomy', 'post-list' );

		// Checkboxes
		foreach ( $checkboxes as $key ) {
			$clean_data[$key] = absint( $parsed_data[$key] );
		}

		// Thumbnail
		$clean_data['thumbnail'] = ttfmake_sanitize_section_choice( $parsed_data['thumbnail'], 'thumbnail', 'post-list' );

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
			'post-list-title' => '',
			'post-list-columns' => 2,
			'post-list-type' => 'post',
			'post-list-sortby' => 'date-desc',
			'post-list-keyword' => '',
			'post-list-count' => 6,
			'post-list-offset' => 0,
			'post-list-taxonomy' => 'all',
			'post-list-show-title' => 1,
			'post-list-show-date' => 1,
			'post-list-show-excerpt' => 1,
			'post-list-show-author' => 0,
			'post-list-show-categories' => 0,
			'post-list-show-tags' => 0,
			'post-list-show-comments' => 0,
			'post-list-thumbnail' => 'top',
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
		if ( count( $choices ) > 1 || ! in_array( $section_type, array( 'post-list' ) ) ) {
			return $choices;
		}

		$choice_id = "$section_type-$key";

		switch ( $choice_id ) {
			case 'post-list-columns' :
				$choices = array(
					1 => __( '1', 'make-plus' ),
					2 => __( '2', 'make-plus' ),
					3 => __( '3', 'make-plus' ),
					4 => __( '4', 'make-plus' ),
				);
				break;
			case 'post-list-type' :
				// Post types
				$post_types = get_post_types(
					array(
						'public' => true,
						'_builtin' => false
					)
				);
				$post_types = array_merge( array( 'post', 'page' ), (array) $post_types );
				// Labels
				$labels = array();
				foreach ( $post_types as $type ) {
					$l = get_post_type_object( $type )->labels;
					$labels[] = esc_html( $l->name );
				}
				// Choices
				$choices = array_combine( $post_types, $labels );
				break;
			case 'post-list-sortby' :
				$choices = array(
					'date-desc' => __( 'Date: newest first', 'make-plus' ),
					'date-asc' => __( 'Date: oldest first', 'make-plus' ),
					'title-asc' => __( 'Title: A to Z', 'make-plus' ),
					'title-desc' => __( 'Title: Z to A', 'make-plus' ),
					'comment_count-desc' => __( 'Most comments', 'make-plus' ),
					'menu_order-asc' => __( 'Page order', 'make-plus' ),
					'rand' => __( 'Random', 'make-plus' ),
				);
				break;
			case 'post-list-taxonomy' :
				// Default
				$choices = array( 'all' => __( 'All categories/tags', 'make-plus' ) );
				// Categories
				$category_terms = get_terms( 'category' );
				if ( ! empty( $category_terms ) ) {
					$category_slugs = array_map( array( $this, 'prefix_cat' ), wp_list_pluck( $category_terms, 'slug' ) );
					$category_names = wp_list_pluck( $category_terms, 'name' );
					$category_list = array_combine( $category_slugs, $category_names );
					$choices = array_merge(
						$choices,
						array( 'ttfmp-disabled1' => '--- ' . __( 'Categories', 'make-plus' ) . ' ---' ),
						$category_list
					);
				}
				// Tags
				$tag_terms = get_terms( 'post_tag' );
				if ( ! empty( $tag_terms ) ) {
					$tag_slugs = array_map( array( $this, 'prefix_tag' ), wp_list_pluck( $tag_terms, 'slug' ) );
					$tag_names = wp_list_pluck( $tag_terms, 'name' );
					$tag_list = array_combine( $tag_slugs, $tag_names );
					$choices = array_merge(
						$choices,
						array( 'ttfmp-disabled2' => '--- ' . __( 'Tags', 'make-plus' ) . ' ---' ),
						$tag_list
					);
				}
				break;
			case 'post-list-thumbnail' :
				$choices = array(
					'top' => __( 'Top', 'make-plus' ),
					'left' => __( 'Left', 'make-plus' ),
					'right' => __( 'Right', 'make-plus' ),
					'none' => __( 'None', 'make-plus' ),
				);
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
			'ttfmp-post-list-sections',
			ttfmp_get_post_list()->url_base . '/css/sections.css',
			array(),
			ttfmp_get_app()->version,
			'all'
		);
	}
}
endif;

/**
 * Instantiate or return the one TTFMP_Post_List_Section_Definitions instance.
 *
 * @since  1.0.0.
 *
 * @return TTFMP_Post_List_Section_Definitions
 */
function ttfmp_post_list_get_section_definitions() {
	return TTFMP_Post_List_Section_Definitions::instance();
}

// Kick off the section definitions immediately
if ( is_admin() ) {
	ttfmp_post_list_get_section_definitions();
}