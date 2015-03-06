<?php
/**
 * @package Make Plus
 */

if ( ! class_exists( 'TTFMP_PerPage_Metabox' ) ) :
/**
 * Metabox-related functions.
 *
 * @since 1.0.0.
 */
class TTFMP_PerPage_Metabox {
	/**
	 * The one instance of TTFMP_PerPage_Metabox.
	 *
	 * @since 1.0.0.
	 *
	 * @var   TTFMP_PerPage_Metabox
	 */
	private static $instance;

	/**
	 * Instantiate or return the one TTFMP_PerPage_Metabox instance.
	 *
	 * @since  1.0.0.
	 *
	 * @return TTFMP_PerPage_Metabox
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
	 * @return TTFMP_PerPage_Metabox
	 */
	public function __construct() {
		// Enqueue styles and scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Add the metabox
		add_action( 'add_meta_boxes', array( $this, 'add_metabox' ) );

		// Save metabox data
		add_action( 'save_post', array( $this, 'save_metabox' ), 10, 2 );
	}

	/**
	 * Enqueue Per Page scripts and styles if it is an edit screen.
	 *
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		global $typenow;
		if ( isset( $typenow ) ) {
			// Style
			wp_enqueue_style(
				ttfmp_get_perpage()->prefix . 'style',
				ttfmp_get_perpage()->url_base . '/css/per-page.css',
				array(),
				ttfmp_get_app()->version
			);
			// Script
			wp_enqueue_script(
				ttfmp_get_perpage()->prefix . 'script',
				ttfmp_get_perpage()->url_base . '/js/per-page.js',
				array( 'jquery' ),
				ttfmp_get_app()->version,
				true
			);
		}
	}

	/**
	 * Add the metabox to each qualified post type edit screen
	 *
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	public function add_metabox() {
		// Post types
		$post_types = get_post_types(
			array(
				'public' => true,
				'_builtin' => false
			)
		);
		$post_types[] = 'post';
		$post_types[] = 'page';
		$post_types = apply_filters( 'ttfmp_perpage_post_types', $post_types );

		// Add the metabox for each type
		foreach ( $post_types as $type ) {
			add_meta_box(
				ttfmp_get_perpage()->prefix . 'metabox',
				__( 'Layout Settings', 'make-plus' ),
				array( $this, 'metabox_callback' ),
				$type,
				'side',
				'default'
			);
		}
	}

	/**
	 * Wrapper for rendering the metabox.
	 *
	 * @since  1.0.0.
	 *
	 * @param  object    $post    The current post.
	 * @return void
	 */
	public function metabox_callback( $post ) {
		$view = ttfmp_get_perpage()->get_view( $post );

		// Nonce
		wp_nonce_field( basename( __FILE__ ), ttfmp_get_perpage()->prefix . 'nonce' );

		// Help blurb
		echo '<p class="howto">';
		printf(
			__( 'Check the box next to a global setting to override it.', 'make-plus' )
		);
		echo '</p>';

		// Determine appropriate render function
		if ( 'shop' === $view ) {
			$this->render_metabox_product( $post );
		} else if ( 'product' === $view ) {
			$this->render_metabox_product( $post );
		} else if ( 'page' === $view ) {
			$this->render_metabox_page( $post );
		} else {
			$this->render_metabox_post( $post );
		}
	}

	/**
	 * Render the metabox for a post.
	 *
	 * @since  1.0.0.
	 *
	 * @param  object    $post    The current post.
	 * @return void
	 */
	private function render_metabox_post( $post ) {
		$overrides = ttfmp_get_perpage_options()->get_post_overrides( $post, 'post' );
		$settings = ttfmp_get_perpage_options()->get_post_settings( $post, 'post' );

		$shop_sidebar_views = get_theme_support( 'ttfmp-shop-sidebar' );
		?>
		<ul class="ttfmp-perpage-options">
			<li class="ttfmp-perpage-header first"><?php _e( 'Header, Footer, Sidebars', 'make-plus' ); ?></li>
			<li>
				<?php
				$key = 'hide-header';
				$label = __( 'Hide site header', 'make-plus' );
				$this->control_override( $key, $overrides[$key] );
				$this->control_setting_checkbox( $key, $settings[$key], $label, $overrides[$key] );
				?>
			</li>
			<li>
				<?php
				$key = 'hide-footer';
				$label = __( 'Hide site footer', 'make-plus' );
				$this->control_override( $key, $overrides[$key] );
				$this->control_setting_checkbox( $key, $settings[$key], $label, $overrides[$key] );
				?>
			</li>
			<li>
				<?php
				$key = 'sidebar-left';
				$label = __( 'Show left sidebar', 'make-plus' );
				$this->control_override( $key, $overrides[$key] );
				$this->control_setting_checkbox( $key, $settings[$key], $label, $overrides[$key] );
				?>
			</li>
			<li>
				<?php
				$key = 'sidebar-right';
				$label = __( 'Show right sidebar', 'make-plus' );
				$this->control_override( $key, $overrides[$key] );
				$this->control_setting_checkbox( $key, $settings[$key], $label, $overrides[$key] );
				?>
			</li>
			<?php if ( isset( $shop_sidebar_views[0] ) && in_array( 'post', (array) $shop_sidebar_views[0] ) ) : ?>
			<li class="ttfmp-perpage-header"><?php _e( 'Shop Sidebar Location', 'make-plus' ); ?></li>
			<li>
				<?php
				$key = 'shop-sidebar';
				$this->control_override( $key, $overrides[$key] );
				$this->control_setting_select( $key, $settings[$key], 'product', $overrides[$key] );
				?>
			</li>
			<?php endif; ?>
			<li class="ttfmp-perpage-header"><?php _e( 'Featured Images', 'make-plus' ); ?></li>
			<li>
				<?php
				$key = 'featured-images';
				$this->control_override( $key, $overrides[$key] );
				$this->control_setting_select( $key, $settings[$key], 'post', $overrides[$key] );
				?>
			</li>
			<li class="ttfmp-perpage-header featured-images-dependent"><?php _e( 'Featured Images Alignment', 'make-plus' ); ?></li>
			<li class="featured-images-dependent">
				<?php
				$key = 'featured-images-alignment';
				$this->control_override( $key, $overrides[$key] );
				$this->control_setting_select( $key, $settings[$key], 'post', $overrides[$key] );
				?>
			</li>
			<li class="ttfmp-perpage-header"><?php _e( 'Post Date', 'make-plus' ); ?></li>
			<li>
				<?php
				$key = 'post-date';
				$this->control_override( $key, $overrides[$key] );
				$this->control_setting_select( $key, $settings[$key], 'post', $overrides[$key] );
				?>
			</li>
			<li class="ttfmp-perpage-header post-date-dependent"><?php _e( 'Post Date Location', 'make-plus' ); ?></li>
			<li class="post-date-dependent">
				<?php
				$key = 'post-date-location';
				$this->control_override( $key, $overrides[$key] );
				$this->control_setting_select( $key, $settings[$key], 'post', $overrides[$key] );
				?>
			</li>
			<li class="ttfmp-perpage-header"><?php _e( 'Post Author', 'make-plus' ); ?></li>
			<li>
				<?php
				$key = 'post-author';
				$this->control_override( $key, $overrides[$key] );
				$this->control_setting_select( $key, $settings[$key], 'post', $overrides[$key] );
				?>
			</li>
			<li class="ttfmp-perpage-header post-author-dependent"><?php _e( 'Post Author Location', 'make-plus' ); ?></li>
			<li class="post-author-dependent">
				<?php
				$key = 'post-author-location';
				$this->control_override( $key, $overrides[$key] );
				$this->control_setting_select( $key, $settings[$key], 'post', $overrides[$key] );
				?>
			</li>
			<li class="ttfmp-perpage-header"><?php _e( 'Post Meta', 'make-plus' ); ?></li>
			<li>
				<?php
				$key = 'show-categories';
				$label = __( 'Show categories', 'make-plus' );
				$this->control_override( $key, $overrides[$key] );
				$this->control_setting_checkbox( $key, $settings[$key], $label, $overrides[$key] );
				?>
			</li>
			<li>
				<?php
				$key = 'show-tags';
				$label = __( 'Show tags', 'make-plus' );
				$this->control_override( $key, $overrides[$key] );
				$this->control_setting_checkbox( $key, $settings[$key], $label, $overrides[$key] );
				?>
			</li>
			<li class="ttfmp-perpage-header"><?php _e( 'Comment Count', 'make-plus' ); ?></li>
			<li>
				<?php
				$key = 'comment-count';
				$this->control_override( $key, $overrides[$key] );
				$this->control_setting_select( $key, $settings[$key], 'post', $overrides[$key] );
				?>
			</li>
			<li class="ttfmp-perpage-header comment-count-dependent"><?php _e( 'Comment Count Location', 'make-plus' ); ?></li>
			<li class="comment-count-dependent">
				<?php
				$key = 'comment-count-location';
				$this->control_override( $key, $overrides[$key] );
				$this->control_setting_select( $key, $settings[$key], 'post', $overrides[$key] );
				?>
			</li>
		</ul>
	<?php
	}

	/**
	 * Render the metabox for a page.
	 *
	 * @since  1.0.0.
	 *
	 * @param  object    $post    The current post.
	 * @return void
	 */
	private function render_metabox_page( $post ) {
		$overrides = ttfmp_get_perpage_options()->get_post_overrides( $post, 'page' );
		$settings = ttfmp_get_perpage_options()->get_post_settings( $post, 'page' );

		$shop_sidebar_views = get_theme_support( 'ttfmp-shop-sidebar' );
		?>
		<ul class="ttfmp-perpage-options">
			<li class="ttfmp-perpage-header default-only first"><?php _e( 'Header, Footer, Sidebars', 'make-plus' ); ?></li>
			<li class="ttfmp-perpage-header builder-only first"><?php _e( 'Header, Footer', 'make-plus' ); ?></li>
			<li>
				<?php
				$key = 'hide-header';
				$label = __( 'Hide site header', 'make-plus' );
				$this->control_override( $key, $overrides[$key] );
				$this->control_setting_checkbox( $key, $settings[$key], $label, $overrides[$key] );
				?>
			</li>
			<li>
				<?php
				$key = 'hide-footer';
				$label = __( 'Hide site footer', 'make-plus' );
				$this->control_override( $key, $overrides[$key] );
				$this->control_setting_checkbox( $key, $settings[$key], $label, $overrides[$key] );
				?>
			</li>
			<li class="default-only">
				<?php
				$key = 'sidebar-left';
				$label = __( 'Show left sidebar', 'make-plus' );
				$this->control_override( $key, $overrides[$key] );
				$this->control_setting_checkbox( $key, $settings[$key], $label, $overrides[$key] );
				?>
			</li>
			<li class="default-only">
				<?php
				$key = 'sidebar-right';
				$label = __( 'Show right sidebar', 'make-plus' );
				$this->control_override( $key, $overrides[$key] );
				$this->control_setting_checkbox( $key, $settings[$key], $label, $overrides[$key] );
				?>
			</li>
			<?php if ( isset( $shop_sidebar_views[0] ) && in_array( 'page', (array) $shop_sidebar_views[0] ) ) : ?>
			<li class="ttfmp-perpage-header default-only"><?php _e( 'Shop Sidebar Location', 'make-plus' ); ?></li>
			<li class="default-only">
				<?php
				$key = 'shop-sidebar';
				$this->control_override( $key, $overrides[$key] );
				$this->control_setting_select( $key, $settings[$key], 'product', $overrides[$key] );
				?>
			</li>
			<?php endif; ?>
			<li class="ttfmp-perpage-header"><?php _e( 'Page Title', 'make-plus' ); ?></li>
			<li>
				<?php
				$key = 'hide-title';
				$label = __( 'Hide title', 'make-plus' );
				$this->control_override( $key, $overrides[$key] );
				$this->control_setting_checkbox( $key, $settings[$key], $label, $overrides[$key] );
				?>
			</li>
			<li class="ttfmp-perpage-header default-only"><?php _e( 'Featured Images', 'make-plus' ); ?></li>
			<li class="default-only">
				<?php
				$key = 'featured-images';
				$this->control_override( $key, $overrides[$key] );
				$this->control_setting_select( $key, $settings[$key], 'page', $overrides[$key] );
				?>
			</li>
			<li class="ttfmp-perpage-header featured-images-dependent default-only"><?php _e( 'Featured Images Alignment', 'make-plus' ); ?></li>
			<li class="featured-images-dependent default-only">
				<?php
				$key = 'featured-images-alignment';
				$this->control_override( $key, $overrides[$key] );
				$this->control_setting_select( $key, $settings[$key], 'page', $overrides[$key] );
				?>
			</li>
			<li class="ttfmp-perpage-header"><?php _e( 'Post Date', 'make-plus' ); ?></li>
			<li>
				<?php
				$key = 'post-date';
				$this->control_override( $key, $overrides[$key] );
				$this->control_setting_select( $key, $settings[$key], 'page', $overrides[$key] );
				?>
			</li>
			<li class="ttfmp-perpage-header post-date-dependent"><?php _e( 'Post Date Location', 'make-plus' ); ?></li>
			<li class="post-date-dependent">
				<?php
				$key = 'post-date-location';
				$this->control_override( $key, $overrides[$key] );
				$this->control_setting_select( $key, $settings[$key], 'page', $overrides[$key] );
				?>
			</li>
			<li class="ttfmp-perpage-header"><?php _e( 'Post Author', 'make-plus' ); ?></li>
			<li>
				<?php
				$key = 'post-author';
				$this->control_override( $key, $overrides[$key] );
				$this->control_setting_select( $key, $settings[$key], 'page', $overrides[$key] );
				?>
			</li>
			<li class="ttfmp-perpage-header post-author-dependent"><?php _e( 'Post Author Location', 'make-plus' ); ?></li>
			<li class="post-author-dependent">
				<?php
				$key = 'post-author-location';
				$this->control_override( $key, $overrides[$key] );
				$this->control_setting_select( $key, $settings[$key], 'page', $overrides[$key] );
				?>
			</li>
			<li class="ttfmp-perpage-header"><?php _e( 'Comment Count', 'make-plus' ); ?></li>
			<li>
				<?php
				$key = 'comment-count';
				$this->control_override( $key, $overrides[$key] );
				$this->control_setting_select( $key, $settings[$key], 'page', $overrides[$key] );
				?>
			</li>
			<li class="ttfmp-perpage-header comment-count-dependent"><?php _e( 'Comment Count Location', 'make-plus' ); ?></li>
			<li class="comment-count-dependent">
				<?php
				$key = 'comment-count-location';
				$this->control_override( $key, $overrides[$key] );
				$this->control_setting_select( $key, $settings[$key], 'page', $overrides[$key] );
				?>
			</li>
		</ul>
	<?php
	}

	/**
	 * Render the metabox for a product or shop page.
	 *
	 * @since  1.0.0.
	 *
	 * @param  object    $post    The current post.
	 * @return void
	 */
	private function render_metabox_product( $post ) {
		$overrides = ttfmp_get_perpage_options()->get_post_overrides( $post, 'product' );
		$settings = ttfmp_get_perpage_options()->get_post_settings( $post, 'product' );

		$view = ttfmp_get_perpage()->get_view( $post );
		$shop_sidebar_views = get_theme_support( 'ttfmp-shop-sidebar' );
		?>
		<ul class="ttfmp-perpage-options">
			<li class="ttfmp-perpage-header first"><?php _e( 'Header, Footer, Sidebars', 'make-plus' ); ?></li>
			<li>
				<?php
				$key = 'hide-header';
				$label = __( 'Hide site header', 'make-plus' );
				$this->control_override( $key, $overrides[$key] );
				$this->control_setting_checkbox( $key, $settings[$key], $label, $overrides[$key] );
				?>
			</li>
			<li>
				<?php
				$key = 'hide-footer';
				$label = __( 'Hide site footer', 'make-plus' );
				$this->control_override( $key, $overrides[$key] );
				$this->control_setting_checkbox( $key, $settings[$key], $label, $overrides[$key] );
				?>
			</li>
			<li>
				<?php
				$key = 'sidebar-left';
				$label = __( 'Show left sidebar', 'make-plus' );
				$this->control_override( $key, $overrides[$key] );
				$this->control_setting_checkbox( $key, $settings[$key], $label, $overrides[$key] );
				?>
			</li>
			<li>
				<?php
				$key = 'sidebar-right';
				$label = __( 'Show right sidebar', 'make-plus' );
				$this->control_override( $key, $overrides[$key] );
				$this->control_setting_checkbox( $key, $settings[$key], $label, $overrides[$key] );
				?>
			</li>
			<?php if ( isset( $shop_sidebar_views[0] ) && in_array( $view, (array) $shop_sidebar_views[0] ) ) : ?>
			<li class="ttfmp-perpage-header"><?php _e( 'Shop Sidebar Location', 'make-plus' ); ?></li>
			<li>
				<?php
				$key = 'shop-sidebar';
				$this->control_override( $key, $overrides[$key] );
				$this->control_setting_select( $key, $settings[$key], 'product', $overrides[$key] );
				?>
			</li>
			<?php endif; ?>
		</ul>
	<?php
	}

	/**
	 * Render the Override checkbox control.
	 *
	 * @since  1.0.0.
	 *
	 * @param  string    $key      The setting to be overridden
	 * @param  bool      $value    1 = checked
	 * @return void
	 */
	private function control_override( $key, $value ) {
		$id = ttfmp_get_perpage()->prefix . 'overrides[' . $key . ']';
		?>
		<label for="<?php echo esc_attr( $id ); ?>">
			<input class="<?php echo esc_attr( ttfmp_get_perpage()->prefix . 'override' ); ?>" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $id ); ?>" type="checkbox" value="1" <?php checked( $value ); ?> />
		</label>
	<?php
	}

	/**
	 * Render a checkbox control for a setting.
	 *
	 * @since  1.0.0.
	 *
	 * @param  string    $key         The setting
	 * @param  bool      $value       1 = checked
	 * @param  string    $label       The label for the checkbox
	 * @param  bool      $override    1 = true
	 * @return void
	 */
	private function control_setting_checkbox( $key, $value, $label, $override ) {
		$id = ttfmp_get_perpage()->prefix . 'settings[' . $key . ']';
		?>
		<label class="selectit" for="<?php echo esc_attr( $id ); ?>">
			<input class="<?php echo esc_attr( ttfmp_get_perpage()->prefix . 'setting' ); ?>" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $id ); ?>" type="checkbox" value="1" <?php checked( $value ); ?><?php if ( ! $override ) echo ' disabled="disabled"'; ?> />
			<?php echo esc_html( $label ); ?>
		</label>
	<?php
	}

	/**
	 * Render a select control for a setting.
	 *
	 * @since  1.0.0.
	 *
	 * @param  string    $key         The setting
	 * @param  bool      $value       The value of the selected option
	 * @param  string    $view        The view type
	 * @param  bool      $override    1 = true
	 * @return void
	 */
	private function control_setting_select( $key, $value, $view, $override ) {
		$id = ttfmp_get_perpage()->prefix . 'settings[' . $key . ']';
		$choices = ttfmake_get_choices( 'layout-' . $view . '-' . $key );
		?>
		<select class="<?php echo esc_attr( ttfmp_get_perpage()->prefix . 'setting' ); ?>" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $id ); ?>" <?php if ( ! $override ) echo ' disabled="disabled"'; ?>>
			<?php foreach ( $choices as $opt_value => $opt_label ) : ?>
			<option value="<?php echo esc_attr( $opt_value ); ?>" <?php selected( $opt_value, $value ); ?>><?php echo esc_html( $opt_label ); ?></option>
			<?php endforeach; ?>
		</select>
	<?php
	}

	/**
	 * Sanitize and save the submitted Per Page post meta data
	 *
	 * @since 1.0.0
	 *
	 * @param int       $post_id    The post ID
	 * @param object    $post       The post object
	 * @return void
	 */
	public function save_metabox( $post_id, $post ) {
		// Checks save status
		$is_autosave = wp_is_post_autosave( $post_id );
		$is_revision = wp_is_post_revision( $post_id );
		$nonce_key = ttfmp_get_perpage()->prefix . 'nonce';
		$is_valid_nonce = ( isset( $_POST[ $nonce_key ] ) && wp_verify_nonce( $_POST[ $nonce_key ], basename( __FILE__ ) ) ) ? 'true' : 'false';

		// Exits script depending on save status
		if ( $is_autosave || $is_revision || ! $is_valid_nonce ) {
			return;
		}

		// Get $_POST arrays
		$new_overrides = ( isset( $_POST[ ttfmp_get_perpage()->prefix . 'overrides' ] ) ) ? $_POST[ ttfmp_get_perpage()->prefix . 'overrides' ] : false;
		$new_settings = ( isset( $_POST[ ttfmp_get_perpage()->prefix . 'settings' ] ) ) ? $_POST[ ttfmp_get_perpage()->prefix . 'settings' ] : false;

		if ( false === $new_overrides ) {
			// Nothing is overridden, so reset both post meta arrays
			delete_post_meta( $post_id, ttfmp_get_perpage()->prefix . 'overrides' );
			delete_post_meta( $post_id, ttfmp_get_perpage()->prefix . 'settings' );
		} else {
			$view = ttfmp_get_perpage()->get_view( $post );

			// Save overrides
			$clean_overrides = array_fill_keys( array_keys( array_map( 'absint', $new_overrides ), 1, true ), 1 );
			update_post_meta( $post_id, ttfmp_get_perpage()->prefix . 'overrides', $clean_overrides );

			// Save only settings with a corresponding active override
			$clean_settings = array();
			foreach ( $clean_overrides as $key => $value ) {
				if ( ! isset( $new_settings[$key] ) ) {
					$clean_settings[$key] = 0;
				} else {
					$clean_settings[$key] = ttfmp_get_perpage_options()->sanitize_post_meta( $key, $new_settings[$key], $view );
				}
			}
			update_post_meta( $post_id, ttfmp_get_perpage()->prefix . 'settings', $clean_settings );
		}
	}
}
endif;

if ( ! function_exists( 'ttfmp_get_perpage_metabox' ) ) :
/**
 * Instantiate or return the one TTFMP_PerPage_Metabox instance.
 *
 * @since  1.0.0.
 *
 * @return TTFMP_PerPage_Metabox
 */
function ttfmp_get_perpage_metabox() {
	return TTFMP_PerPage_Metabox::instance();
}
endif;

ttfmp_get_perpage_metabox();
