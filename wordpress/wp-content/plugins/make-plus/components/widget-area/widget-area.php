<?php
/**
 * @package Make Plus
 */

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
	 * Bootstrap the module
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
	}

	/**
	 * Initialize the components of the module
	 *
	 * @since  1.4.3.
	 *
	 * @return void
	 */
	public function init() {
		// Passive mode
		if ( true === ttfmp_get_app()->passive ) {
			// Add additional files
			require_once $this->component_root . '/sidebar-management.php';

			// Set up the shortcode
			add_shortcode( 'ttfmp_widget_area', array( $this, 'widget_area' ) );
		}
		// Active mode
		else {
			// Add additional files
			require_once $this->component_root . '/sidebar-management.php';

			// Add the JS/CSS
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );

			// Add inputs before the text section
			add_action( 'ttfmake_section_text_before_column', array( $this, 'section_text_before_column' ), 10, 2 );

			// Add button for widget area in Make 1.4.0+
			add_action( 'make_column_buttons', array( $this, 'make_column_buttons' ), 10, 2 );

			// Add content after the column
			add_action( 'ttfmake_section_text_after_column', array( $this, 'section_text_after_column' ), 10, 2 );

			// Add more data to the save data routine
			add_filter( 'ttfmake_prepare_data_section', array( $this, 'prepare_data_section' ), 10, 3 );

			// Replace content with shortcode when saving the post content
			add_filter( 'ttfmake_insert_post_data_sections', array( $this, 'insert_post_data_sections' ) );

			// Save widgets
			add_action( 'save_post', array( $this, 'save_widget_data' ), 10, 2 );

			// Set up the shortcode
			add_shortcode( 'ttfmp_widget_area', array( $this, 'widget_area' ) );
		}
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
				'textColumnString' => __( 'Revert to column', 'make-plus' ),
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

		// Only show the button for Make versions less than 1.4.0.
		if ( defined( 'TTFMAKE_VERSION' ) && false === version_compare( TTFMAKE_VERSION, '1.3.99', '>=' ) ) : ?>
		<a href="#" class="ttfmp-create-widget-area button button-small widefat">
			<?php if ( 1 === (int) $widget_area ) : ?>
				<?php _e( 'Revert to regular column', 'make-plus' ); ?>
			<?php else : ?>
				<?php _e( 'Convert to widget area', 'make-plus' ); ?>
			<?php endif; ?>
		</a>
		<?php endif; ?>
		<div class="ttfmp-widget-area-overlay-region ttfmp-widget-area-overlay-region-<?php echo esc_attr( $class ); ?>">
		<input type="hidden" class="ttfmp-text-widget-area" name="<?php echo esc_attr( $section_name ); ?>[widget-area]" value="<?php echo absint( $widget_area ); ?>" />
	<?php
	}

	/**
	 * Add the convert to widget area button to the builder.
	 *
	 * @since  1.4.0.
	 *
	 * @param  array    $column_buttons          The list of buttons for the column.
	 * @param  array    $ttfmake_section_data    All of the section data.
	 * @return array                             The modified list of buttons.
	 */
	public function make_column_buttons( $column_buttons, $ttfmake_section_data ) {
		$column_buttons[300] = array(
			'label' => __( 'Convert text column to widget area', 'make' ),
			'href'  => '#',
			'class' => 'convert-widget-area-link ttfmp-create-widget-area',
			'title' => __( 'Convert to widget area', 'make' ),
		);

		return $column_buttons;
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
		$order         = array();

		// Get the sidebar widgets
		if ( true !== $ttfmake_is_js_template ) {
			// Get the sidebar ID
			$page_id    = ( get_post() ) ? get_the_ID() : 0;
			$section_id = $data['data']['id'];
			$sidebar_id = 'ttfmp-' . $page_id . '-' . $section_id . '-' . $column_number;

			// Get the data needed for display
			$widget_data = $this->get_widget_data_for_display( $sidebar_id );

			// Parse out the ordering data
			foreach ( $widget_data as $widget ) {
				$order[] = $widget['id'];
			}
		}

		// Get the Customizer url
		$customize_url = add_query_arg( 'return', urlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ), admin_url( 'customize.php' ) );
		if ( false !== $permalink = get_permalink() ) {
			$customize_url = add_query_arg( 'url', urlencode( wp_unslash( $permalink ) ), $customize_url );
		}
	?>
			<div class="ttfmp-widget-area-overlay">
				<div class="ttfmp-widget-area-display">
					<div class="ttfmake-titlediv">
						<input placeholder="<?php esc_attr_e( 'Enter name here', 'make-plus' ); ?>" type="text" name="<?php echo $section_name; ?>[sidebar-label]" class="ttfmake-title" value="<?php echo sanitize_text_field( $sidebar_label ); ?>" autocomplete="off" />
						<a href="#" class="ttfmp-revert-widget-area ttfmp-create-widget-area" title="<?php esc_attr_e( 'Revert to column', 'make-plus' ); ?>">
							<span>
								<?php _e( 'Revert to column', 'make-plus' ); ?>
							</span>
						</a>
					</div>

					<div class="ttfmp-widget-area-text">
						<?php if ( true === $ttfmake_is_js_template || ! isset( $widget_data ) || empty( $widget_data ) ) : ?>
							<p>
								<?php
								printf(
									__( 'No widgets added yet. To add widgets, save this page, then go to the <a href="%s">Customizer</a>.', 'make-plus' ),
									esc_url( $customize_url )
								);
								?>
							</p>
						<?php elseif ( isset( $widget_data ) && ! empty( $widget_data ) ): ?>
							<p>
								<?php
								printf(
									__( 'To add new widgets, please go to the <a href="%s">Customizer</a>.', 'make-plus' ),
									esc_url( $customize_url )
								);
								?>
							</p>
							<ul class="ttfmp-widget-list">
							<?php foreach ( $widget_data as $widget ) : ?>
								<li data-id="<?php echo esc_attr( $widget['id'] ); ?>">
									<div title="<?php esc_attr_e( 'Drag-and-drop this widget into place', 'make' ); ?>" class="ttfmake-sortable-handle">
										<div class="sortable-background"></div>
									</div>
									<div class="ttfmp-widget-list-container">
										<span class="ttfmp-widget-list-type"><?php echo wp_strip_all_tags( $widget['type'] ); ?></span><?php if ( '' !== $widget['title'] ) : ?>: <span class="ttfmp-widget-list-title"><?php echo wp_strip_all_tags( $widget['title'] ); ?></span><?php endif; ?>
										<a href="#" class="edit-widget-link ttfmake-overlay-open" data-overlay="#ttfmake-overlay-<?php echo esc_attr( $widget['id'] ); ?>" title="<?php esc_attr_e( 'Configure widget', 'make' ); ?>">
											<span>
												<?php _e( 'Configure widget', 'make' ); ?>
											</span>
										</a>
										<a href="#" class="remove-widget-link ttfmake-widget-remove" title="<?php esc_attr_e( 'Delete widget', 'make' ); ?>">
											<span>
												<?php _e( 'Delete widget', 'make' ); ?>
											</span>
										</a>
									</div>
								</li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</div>
					<input class="widgets" type="hidden" name="<?php echo $section_name; ?>[widgets]" value="<?php echo implode( ',', $order ); ?>" />
				</div>
			</div>
			<?php if ( isset( $widget_data ) && ! empty( $widget_data ) ) : ?>
				<?php foreach ( $widget_data as $widget ) : ?>
					<?php echo $this->overlay_template( $widget['form'], $widget['id_base'], $widget['id'], $widget['type'] ); ?>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	<?php
	}

	/**
	 * Get the template for displaying a widget form.
	 *
	 * @since  1.4.1
	 *
	 * @param  string    $form       The HTML for the widget form.
	 * @param  string    $id_base    The base ID for the widget.
	 * @return string                The overlay template for the widget form.
	 */
	public function overlay_template( $form, $id_base, $id, $title ) {
		ob_start();

		global $ttfmake_overlay_class, $ttfmake_section_data, $ttfmake_overlay_title, $ttfmake_overlay_id;
		$ttfmake_overlay_class = 'ttfmake-configuration-overlay ttfmake-widget-configuration-overlay';
		$ttfmake_overlay_title = __( 'Configure ', 'make-plus' ) . $title;
		$ttfmake_overlay_id    = 'ttfmake-overlay-' . $id;

		// Include the header
		get_template_part( '/inc/builder/core/templates/overlay', 'header' );

		// Sort the config in case 3rd party code added another input
		ksort( $ttfmake_section_data['section']['config'], SORT_NUMERIC );
		?>
		<div class="widget-form">
			<?php echo $form; ?>
			<input type="hidden" name="ttfmp-widgets[]" value="widget-<?php echo esc_attr( $id_base ); ?>" />
		</div>
		<?php
		get_template_part( '/inc/builder/core/templates/overlay', 'footer' );

		return ob_get_clean();
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

		foreach ( $widgets as $id => $widget ) {
			$number = $widget['params'][0]['number'];

			ob_start();
			$widget['callback'][0]->form_callback( $number );
			$widget_form = ob_get_clean();

			$widgets_data[] = array(
				'type'    => $widget['name'],
				'title'   => $this->get_widget_title( $number, $widget['callback'][0]->option_name ),
				'id'      => $id,
				'number'  => $number,
				'id_base' => $widget['callback'][0]->id_base,
				'form'    => $widget_form,
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
					$widgets[ $widget_id ] = $wp_registered_widgets[ $widget_id ];
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

					/**
					 * Note that this value is being set merely as a method of convenience so that
					 * `insert_post_data_sections()` can access the widget data and set the proper widgets and order.
					 * DO NOT RELY ON THIS DATABASE VALUE FOR ANY OTHER PURPOSE. Since widgets can be administered in
					 * a number of places, this data will only be correct during a save routine and should otherwise be
					 * considered to be outdated. Use `wp_get_sidebars_widgets()` to get the correct values.
					 */
					if ( ! empty( $item['widgets'] ) ) {
						$clean_widgets = array();
						$widgets       = explode( ',', $item['widgets'] );

						foreach ( $widgets as $widget ) {
							$clean_widgets[] = sanitize_title( $widget );
						}

						$clean_data['columns'][ $id ]['widgets'] = $widgets;
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

							// Save the current widgets in the correct order.
							$widget_area_id  = 'ttfmp-' . absint( get_the_ID() ) . '-' . ttfmake_get_builder_save()->clean_section_id( $section_id ) . '-' . absint( $column_id );
							$current_widgets = wp_get_sidebars_widgets();

							if ( isset( $column['widgets'] ) ) {
								// Set the current widgets
								$new_widgets = $column['widgets'];
							} else {
								// If there are no widgets in the column, make sure that the array is set as empty
								$new_widgets = array();
							}

							// Update the widgets array with the new widgets
							$current_widgets[ $widget_area_id ] = $new_widgets;

							// Update the widgets array
							wp_set_sidebars_widgets( $current_widgets );
						}
					}
				}
			}
		}

		return $sections;
	}

	/**
	 * Save the individual widget data
	 *
	 * @since  1.4.1.
	 *
	 * @param  int        $post_id    The ID of the saved post.
	 * @param  WP_Post    $post       The post being saved.
	 * @return void
	 */
	public function save_widget_data( $post_id, $post ) {
		global $wp_registered_widgets;

		if ( ! isset( $_POST[ 'ttfmake-builder-nonce' ] ) || ! wp_verify_nonce( $_POST[ 'ttfmake-builder-nonce' ], 'save' ) ) {
			return;
		}

		// Don't do anything during autosave
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Only check permissions for pages since it can only run on pages
		if ( ! current_user_can( 'edit_page', $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Don't save data if we're not using the Builder template
		if ( ! ttfmake_will_be_builder_page() ) {
			return;
		}

		// Don't save widgets if there are none to save
		if ( ! isset( $_POST['ttfmp-widgets'] ) ) {
			return;
		}

		$widgets = array_unique( $_POST['ttfmp-widgets'] );

		foreach ( $widgets as $widget_name ) {
			if ( isset( $_POST[ $widget_name ] ) ) {
				$settings = $_POST[ $widget_name ];

				$all_instances = array();

				foreach ( $settings as $number => $new_instance ) {
					$new_instance = stripslashes_deep( $new_instance );

					if ( isset( $widget_class_instance ) ) {
						unset( $widget_class_instance );
					}

					// Get the widget instance
					$widget_id = str_replace( 'widget-', '', $widget_name . '-' . $number );

					if ( isset( $wp_registered_widgets[ $widget_id ] ) ) {
						$widget_class_instance = $wp_registered_widgets[ $widget_id ];
						$widget_class_instance = $widget_class_instance['callback'][0];

						if ( empty( $all_instances ) ) {
							$all_instances = $widget_class_instance->get_settings();
						}

						if ( $widget_class_instance->updated ) {
							break;
						}

						$widget_class_instance->_set( $number );

						$old_instance = isset( $all_instances[ $number ] ) ? $all_instances[ $number ] : array();

						$was_cache_addition_suspended = wp_suspend_cache_addition();
						if ( $widget_class_instance->is_preview() && ! $was_cache_addition_suspended ) {
							wp_suspend_cache_addition( true );
						}

						$instance = $widget_class_instance->update( $new_instance, $old_instance );

						if ( $widget_class_instance->is_preview() ) {
							wp_suspend_cache_addition( $was_cache_addition_suspended );
						}

						/**
						 * Filter a widget's settings before saving.
						 *
						 * Returning false will effectively short-circuit the widget's ability
						 * to update settings.
						 *
						 * @since 2.8.0
						 *
						 * @param array     $instance     The current widget instance's settings.
						 * @param array     $new_instance Array of new widget settings.
						 * @param array     $old_instance Array of old widget settings.
						 * @param WP_Widget $this         The current widget instance.
						 */
						$instance = apply_filters( 'widget_update_callback', $instance, $new_instance, $old_instance, $this );

						if ( false !== $instance ) {
							$all_instances[ $number ] = $instance;
						}
					}
				}
			}

			if ( isset( $widget_class_instance ) && isset( $all_instances ) ) {
				$widget_class_instance->save_settings( $all_instances );
				$widget_class_instance->updated = true;
			}
		}
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

ttfmp_get_widget_area()->init();