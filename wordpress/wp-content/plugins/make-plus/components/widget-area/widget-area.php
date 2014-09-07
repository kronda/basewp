<?php

if ( ! class_exists( 'TTFMP_Widget_Area' ) ) :
/**
 * Bootstrap the layout template functionality.
 *
 * @since 1.0.0.
 */
class TTFMP_Widget_Area {
	/**
	 * Name of the component.
	 *
	 * @since 1.0.0.
	 *
	 * @var   string    The name of the component.
	 */
	var $component_slug = 'widget-area';

	/**
	 * Path to the component directory (e.g., /var/www/mysite/wp-content/plugins/make-plus/components/my-component).
	 *
	 * @since 1.0.0.
	 *
	 * @var   string    Path to the component directory
	 */
	var $component_root = '';

	/**
	 * File path to the plugin main file (e.g., /var/www/mysite/wp-content/plugins/make-plus/components/my-component/my-component.php).
	 *
	 * @since 1.0.0.
	 *
	 * @var   string    Path to the plugin's main file.
	 */
	var $file_path = '';

	/**
	 * The URI base for the plugin (e.g., http://domain.com/wp-content/plugins/make-plus/my-component).
	 *
	 * @since 1.0.0.
	 *
	 * @var   string    The URI base for the plugin.
	 */
	var $url_base = '';

	/**
	 * The one instance of TTFMP_Widget_Area.
	 *
	 * @since 1.0.0.
	 *
	 * @var   TTFMP_Widget_Area
	 */
	private static $instance;

	/**
	 * Instantiate or return the one TTFMP_Widget_Area instance.
	 *
	 * @since  1.0.0.
	 *
	 * @return TTFMP_Widget_Area
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
	 * @return TTFMP_Widget_Area
	 */
	public function __construct() {
		// Set the main paths for the component
		$this->component_root = ttfmp_get_app()->component_base . '/' . $this->component_slug;
		$this->file_path      = $this->component_root . '/' . basename( __FILE__ );
		$this->url_base       = untrailingslashit( plugins_url( '/', __FILE__ ) );

		// Add additional files
		require_once $this->component_root . '/sidebar-management.php';

		// Add the JS/CSS
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

		// Add inputs before the text section
		add_action( 'ttfmake_section_text_before_column', array( $this, 'section_text_before_column' ), 10, 2 );

		// Add content after the column
		add_action( 'ttfmake_section_text_after_column', array( $this, 'section_text_after_column' ), 10, 2 );

		// Add more data to the save data routine
		add_filter( 'ttfmake_prepare_data_section', array( $this, 'prepare_data_section' ), 10, 3 );

		// Replace content with shortcode when saving the post content
		add_filter( 'ttfmake_insert_post_data_sections', array( $this, 'insert_post_data_sections' ) );

		// Set up the shortcode
		add_shortcode( 'ttfmp_widget_area', array( $this, 'widget_area' ) );
	}

	/**
	 * Add JS/CSS on page edit screen.
	 *
	 * @since  1.0.0.
	 *
	 * @param  string    $hook_suffix    The current page slug.
	 * @return void
	 */
	public function admin_enqueue_scripts( $hook_suffix ) {
		// Have to be careful with this test because this function was introduced in Make 1.2.0.
		$post_type_supports_builder = ( function_exists( 'ttfmake_post_type_supports_builder' ) ) ? ttfmake_post_type_supports_builder( get_post_type() ) : false;
		$post_type_is_page          = ( 'page' === get_post_type() );

		if ( ( 'post.php' !== $hook_suffix && 'post-new.php' !== $hook_suffix ) || ( ! $post_type_supports_builder && ! $post_type_is_page ) ) {
			return;
		}

		wp_enqueue_script(
			'ttfmp-widget-area',
			$this->url_base . '/js/widget-area.js',
			array( 'jquery' ),
			ttfmp_get_app()->version,
			true
		);

		wp_localize_script(
			'ttfmp-widget-area',
			'ttfmpWidgetArea',
			array(
				'widgetAreaString' => __( 'Convert to widget area', 'make-plus' ),
				'textColumnString' => __( 'Revert to regular column', 'make-plus' ),
			)
		);

		wp_enqueue_style(
			'ttfmp-widget-area',
			$this->url_base . '/css/widget-area.css',
			array(),
			ttfmp_get_app()->version
		);
	}

	/**
	 * Add button to turn column into widget area.
	 *
	 * @since  1.0.0.
	 *
	 * @param  array     $data             The section's data.
	 * @param  string    $column_number    The column number.
	 * @return void
	 */
	public function section_text_before_column( $data, $column_number ) {
		global $ttfmake_is_js_template;
		$section_name  = ttfmake_get_section_name( $data, $ttfmake_is_js_template );
		$section_name .= '[columns][' . $column_number . ']';
		$widget_area   = ( isset( $data['data']['columns'][ $column_number ]['widget-area'] ) ) ? $data['data']['columns'][ $column_number ]['widget-area'] : 0;
		$class         = ( 1 === (int) $widget_area ) ? 'active' : 'inactive';
	?>
		<a href="#" class="ttfmp-create-widget-area button button-small widefat">
			<?php if ( 1 === (int) $widget_area ) : ?>
				<?php _e( 'Revert to regular column', 'make-plus' ); ?>
			<?php else : ?>
				<?php _e( 'Convert to widget area', 'make-plus' ); ?>
			<?php endif; ?>
		</a>
		<div class="ttfmp-widget-area-overlay-region ttfmp-widget-area-overlay-region-<?php echo esc_attr( $class ); ?>">
		<input type="hidden" class="ttfmp-text-widget-area" name="<?php echo esc_attr( $section_name ); ?>[widget-area]" value="<?php echo absint( $widget_area ); ?>" />
	<?php
	}

	/**
	 * Add content below text columns.
	 *
	 * @since  1.0.0.
	 *
	 * @param  array     $data             The section data.
	 * @param  string    $column_number    The column number.
	 * @return void
	 */
	public function section_text_after_column( $data, $column_number ) {
		global $ttfmake_is_js_template;
		$section_name  = ttfmake_get_section_name( $data, $ttfmake_is_js_template );
		$section_name .= '[columns][' . $column_number . ']';
		$sidebar_label = ( isset( $data['data']['columns'][ $column_number ]['sidebar-label'] ) ) ? $data['data']['columns'][ $column_number ]['sidebar-label'] : '';

		// Get the sidebar widgets
		if ( true !== $ttfmake_is_js_template ) {
			// Get the sidebar ID
			$page_id    = ( get_post() ) ? get_the_ID() : 0;
			$section_id = $data['data']['id'];
			$sidebar_id = 'ttfmp-' . $page_id . '-' . $section_id . '-' . $column_number;

			// Get the data needed for display
			$widget_data = $this->get_widget_data_for_display( $sidebar_id );
		}
	?>
			<div class="ttfmp-widget-area-overlay">
				<div class="ttfmp-widget-area-display">
					<div class="ttfmake-titlediv">
						<input placeholder="<?php esc_attr_e( 'Enter name here', 'make-plus' ); ?>" type="text" name="<?php echo $section_name; ?>[sidebar-label]" class="ttfmake-title" value="<?php echo sanitize_text_field( $sidebar_label ); ?>" autocomplete="off" />
						<?php if ( true === $ttfmake_is_js_template || ! isset( $widget_data ) || empty( $widget_data ) ) : ?>
							<p><?php _e( 'There are no widgets in this area. After saving this page, go to the Theme Customizer to manage your widgets.', 'make-plus' ); ?></p>
						<?php elseif ( isset( $widget_data ) && ! empty( $widget_data ) ): ?>
							<p><?php _e( 'To manage your widgets, please go to the Theme Customizer.', 'make-plus' ); ?></p>
							<br />
							<p><strong><?php _e( 'Widgets in this area:', 'make-plus' ); ?></strong></p>
							<ul class="ttfmp-widget-list">
							<?php foreach ( $widget_data as $widget ) : ?>
								<li>
									<span class="ttfmp-widget-list-type"><?php echo wp_strip_all_tags( $widget['type'] ); ?></span><?php if ( '' !== $widget['title'] ) : ?>: <span class="ttfmp-widget-list-title"><?php echo wp_strip_all_tags( $widget['title'] ); ?></span><?php endif; ?>
								</li>
							<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	<?php
	}

	/**
	 * Get the data needed to display the list of widgets.
	 *
	 * @since  1.0.0.
	 *
	 * @param  string    $sidebar_id    The ID of the sidebar.
	 * @return array                    Array of widgets in the sidebar.
	 */
	public function get_widget_data_for_display( $sidebar_id ) {
		$widgets = $this->get_widgets_in_sidebar_instance( $sidebar_id );

		// Collector for the widget data
		$widgets_data = array();

		foreach ( $widgets as $widget ) {
			$widgets_data[] = array(
				'type'  => $widget['name'],
				'title' => $this->get_widget_title( $widget['callback'][0]->number, $widget['callback'][0]->option_name ),
			);
		}

		return $widgets_data;
	}

	/**
	 * Get information about all widgets in a sidebar.
	 *
	 * @since  1.0.0.
	 *
	 * @param  string    $sidebar_id    Unique ID for the sidebar.
	 * @return array                    Collection of widget IDs.
	 */
	public function get_widgets_in_sidebar_instance( $sidebar_id ) {
		global $wp_registered_widgets;

		// Collector for the widgets
		$widgets = array();

		// Attempt to find the associated widgets
		$all_sidebars = wp_get_sidebars_widgets();

		// Collect data for each registered widget
		if ( isset( $all_sidebars[ $sidebar_id ] ) ) {
			foreach ( $all_sidebars[ $sidebar_id ] as $widget_id ) {
				if ( isset( $wp_registered_widgets[ $widget_id ] ) ) {
					$widgets[] = $wp_registered_widgets[ $widget_id ];
				}
			}
		}

		return $widgets;
	}

	/**
	 * Get the custom title for an individual widget instance.
	 *
	 * @param  int       $number         The widget number for a multi number widget.
	 * @param  string    $option_name    The option that the widget data is saved under.
	 * @return string                    The title of the widget.
	 */
	public function get_widget_title( $number, $option_name ) {
		$widget_instance_data = get_option( $option_name );
		return ( isset( $widget_instance_data[ $number ]['title'] ) ) ? $widget_instance_data[ $number ]['title'] : '';
	}

	/**
	 * Append the widget area value to the data.
	 *
	 * @since  1.0.0.
	 *
	 * @param  array     $clean_data       The cleaned up data.
	 * @param  array     $original_data    The $_POST data for the section.
	 * @param  string    $section_type     The ID for the section.
	 * @return array                       The additional data.
	 */
	public function prepare_data_section( $clean_data, $original_data, $section_type ) {
		if ( 'text' === $section_type ) {
			if ( isset( $original_data['columns'] ) && is_array( $original_data['columns'] ) ) {
				foreach ( $original_data['columns'] as $id => $item ) {
					if ( isset( $item['widget-area'] ) ) {
						$clean_data['columns'][ $id ]['widget-area'] = absint( $item['widget-area'] );
					}

					if ( isset( $item['sidebar-label'] ) ) {
						$clean_data['columns'][ $id ]['sidebar-label'] = ( 0 === $clean_data['columns'][ $id ]['widget-area'] ) ? '' : esc_html( $item['sidebar-label'] );
					}
				}
			}
		}

		return $clean_data;
	}

	/**
	 * Replace the text column content if the widget area value is set.
	 *
	 * Note that this only resets the content saved to the "post_content" field in the database. It does not touch the
	 * meta data in an effort to ensure that the old values that may be reverted to are kept in tact.
	 *
	 * @since  1.0.0.
	 *
	 * @param  array    $sections    The array of section data for the page.
	 * @return array                 The modified section data.
	 */
	public function insert_post_data_sections( $sections ) {
		foreach ( $sections as $section_id => $section ) {
			if ( isset( $section['section-type'] ) && 'text' === $section['section-type'] ) {
				if ( isset( $section['columns'] ) ) {
					foreach ( $section['columns'] as $column_id => $column ) {
						if ( isset( $column['widget-area'] ) && 1 === (int) $column['widget-area'] ) {
							$new_contents = array(
								'title'       => '',
								'image-link'  => '',
								'image-id'    => '',
								'content'     => '[ttfmp_widget_area page_id="' . absint( get_the_ID() ) . '" section_id="' . ttfmake_get_builder_save()->clean_section_id( $section_id ) . '" column_id="' . absint( $column_id ) . '"]',
								'widget-area' => 1
							);

							$sections[ $section_id ]['columns'][ $column_id ] = array_merge( $column, $new_contents );

							// Grab the sidebar label if available
							$sidebar_label = ( isset( $sections[ $section_id ]['columns'][ $column_id ]['sidebar-label'] ) ) ? $sections[ $section_id ]['columns'][ $column_id ]['sidebar-label'] : '';

							// Register the sidebar
							ttfmp_register_sidebar( get_the_ID(), $section_id, $column_id, $sidebar_label );
						}
					}
				}
			}
		}

		return $sections;
	}

	/**
	 * Print the content for the widget area.
	 *
	 * @since  1.0.0.
	 *
	 * @param  array     $attrs    The list of attributes for the widget.
	 * @return string              The generated content for the shortcode.
	 */
	public function widget_area( $attrs ) {
		$id = 'ttfmp-' . $attrs['page_id'] . '-' . $attrs['section_id'] . '-' . $attrs['column_id'];

		// Run output buffers so that the content is captured and returned
		ob_start();
		dynamic_sidebar( $id );

		return ob_get_clean();
	}
}
endif;

if ( ! function_exists( 'ttfmp_get_widget_area' ) ) :
/**
 * Instantiate or return the one TTFMP_Widget_Area instance.
 *
 * @since  1.0.0.
 *
 * @return TTFMP_Widget_Area
 */
function ttfmp_get_widget_area() {
	return TTFMP_Widget_Area::instance();
}
endif;

ttfmp_get_widget_area();