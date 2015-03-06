<?php
/**
 * @package Make Plus
 */

/**
 * Class TTFMP_WooCommerce_Legacy_Color
 *
 * Support color settings for versions of WooCommerce before 2.3.
 *
 * @since 1.5.0.
 */
class TTFMP_WooCommerce_Legacy_Color {
	/**
	 * The one instance of TTFMP_WooCommerce_Legacy_Color.
	 *
	 * @since 1.0.0.
	 *
	 * @var   TTFMP_WooCommerce_Legacy_Color
	 */
	private static $instance;

	/**
	 * Instantiate or return the one TTFMP_WooCommerce_Legacy_Color instance.
	 *
	 * @since  1.0.0.
	 *
	 * @return TTFMP_WooCommerce_Legacy_Color
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 *
	 */
	public function __construct() {}


	/**
	 *
	 */
	public function init() {
		// Modify the WooCommerce General Settings page
		add_action( 'woocommerce_settings_general', array( $this, 'modify_wc_settings' ) );

		// Filter the frontend color settings
		add_filter( 'pre_option_woocommerce_frontend_css_colors', array( $this, 'frontend_css_colors' ) );

		// Use a preview version of the WooCommerce stylesheet while in the Theme Customizer
		add_action( 'wp', array( $this, 'compile_preview_styles' ) );

		// Re-compile the WooCommerce CSS file when settings are saved
		add_action( 'customize_save_after', array( $this, 'save_frontend_styles' ) );

		// Add description for Highlight Color control
		add_filter( 'ttfmp_color_highlight_description', array( $this, 'color_highlight_description' ) );
	}

	/**
	 * Replace the color pickers in the Frontend styles section of the UI with a note
	 * directing users to the Customizer.
	 *
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	public function modify_wc_settings() {
		// Determine the callback to remove
		$callback = $this->has_method_filter( 'woocommerce_admin_field_frontend_styles', 'WC_Settings_General', 'frontend_styles_setting' );

		if ( false !== $callback ) {
			// Replace the Frontend styles options in WooCommerce settings with
			// blurb about settings in the Customizer
			remove_action( 'woocommerce_admin_field_frontend_styles', $callback );
			add_action( 'woocommerce_admin_field_frontend_styles', array( $this, 'frontend_styles_setting' ) );
		}
	}

	/**
	 * Add Frontend styles message.
	 *
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	public function frontend_styles_setting() {
		?>
		<tr valign="top" class="woocommerce_frontend_css_colors">
			<th scope="row" class="titledesc">
				<?php _e( 'Frontend Styles', 'make-plus' ); ?>
			</th>
			<td class="forminp">
				<span class="description">
			<?php // File writability check
			$base_file = WC()->plugin_path() . '/assets/css/woocommerce-base.less';
			$css_file  = WC()->plugin_path() . '/assets/css/woocommerce.css';
			if ( is_writable( $base_file ) && is_writable( $css_file ) ) {
				// Get the URL
				$url = add_query_arg( 'return', urlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ), admin_url( 'customize.php' ) );
				$shop = get_option( 'woocommerce_shop_page_id' );
				if ( $shop ) {
					$url = add_query_arg( 'url', urlencode( get_permalink( $shop ) ), $url );
				}
				// Add the message
				printf(
					__( 'These styles can be customized in the Color Scheme &rarr; General section of the %s.', 'make-plus' ),
					sprintf(
						'<a href="%1$s">%2$s</a>',
						esc_url( $url ),
						__( 'Customizer', 'make-plus' )
					)
				);
			} else {
				echo __( 'To edit colours <code>woocommerce/assets/css/woocommerce-base.less</code> and <code>woocommerce.css</code> need to be writable. See <a href="http://codex.wordpress.org/Changing_File_Permissions">the Codex</a> for more information.', 'make-plus' );
			}
			?>
				</span>
			</td>
		</tr>
	<?php
	}

	/**
	 * Override the WooCommerce frontend color options with the Make color settings
	 *
	 * @since  1.0.0.
	 *
	 * @param  bool     $colors    Unused
	 * @return array               The Make color settings array
	 */
	public function frontend_css_colors( $colors ) {
		$colors = array(
			'primary' => get_theme_mod( 'color-primary', ttfmake_get_default( 'color-primary' ) ),
			'secondary' => get_theme_mod( 'color-secondary', ttfmake_get_default( 'color-secondary' ) ),
			'highlight' => get_theme_mod( 'color-highlight', ttfmake_get_default( 'color-highlight' ) ),
			'content_bg' => get_theme_mod( 'main-background-color', ttfmake_get_default( 'main-background-color' ) ),
			'subtext' => get_theme_mod( 'color-detail', ttfmake_get_default( 'color-detail' ) ),
		);

		return $colors;
	}

	/**
	 * Check if the currently loading instance is in the Preview pane
	 *
	 * @since  1.0.0.
	 *
	 * @return bool    True if it's in the Preview pane
	 */
	public function is_preview() {
		global $wp_customize;
		return ( isset( $wp_customize ) && $wp_customize->is_preview() );
	}

	/**
	 * Swap the normal woocommerce CSS file with the preview file in the style queue
	 *
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	public function preview_frontend_styles() {
		if ( ! $this->is_preview() ) {
			return;
		}

		$uploads = wp_upload_dir();
		$preview_file = trailingslashit( $uploads['basedir'] ) . 'ttfmp-woocommerce-preview.css';

		if ( file_exists( $preview_file ) ) {
			wp_dequeue_style( 'woocommerce-general' );
			wp_deregister_style( 'woocommerce-general' );
			wp_enqueue_style(
				'woocommerce-general',
				trailingslashit( $uploads['baseurl'] ) . 'ttfmp-woocommerce-preview.css',
				array(),
				time()
			);
		}
	}

	/**
	 * Build a preview version of the woocommerce CSS file
	 *
	 * Based on woocommerce_compile_less_styles() in version 2.1.9 of WooCommerce
	 *
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	public function compile_preview_styles() {
		if ( ! $this->is_preview() ) {
			return;
		}

		$uploads = wp_upload_dir();

		$colors    = array_map( 'esc_attr', (array) get_option( 'woocommerce_frontend_css_colors' ) );
		$base_file = WC()->plugin_path() . '/assets/css/woocommerce-base.less';
		$less_file = WC()->plugin_path() . '/assets/css/woocommerce.less';
		$css_file  = trailingslashit( $uploads['basedir'] ) . 'ttfmp-woocommerce-preview.css';

		if ( ! file_exists( $css_file ) ) {
			$new_file = file_put_contents( $css_file, '' );
			if ( false === $new_file ) {
				return;
			}
		}

		if ( is_writable( $base_file ) && is_writable( $css_file ) ) {
			if ( ! class_exists( 'lessc' ) ) {
				include_once( WC()->plugin_path() . '/includes/libraries/class-lessc.php' );
			}
			if ( ! class_exists( 'cssmin' ) ) {
				include_once( WC()->plugin_path() . '/includes/libraries/class-cssmin.php' );
			}

			try {
				// Write new color to base file
				$color_rules = "
@primary:       " . $colors['primary'] . ";
@primarytext:   " . wc_light_or_dark( $colors['primary'], 'desaturate(darken(@primary,50%),18%)', 'desaturate(lighten(@primary,50%),18%)' ) . ";

@secondary:     " . $colors['secondary'] . ";
@secondarytext: " . wc_light_or_dark( $colors['secondary'], 'desaturate(darken(@secondary,60%),18%)', 'desaturate(lighten(@secondary,60%),18%)' ) . ";

@highlight:     " . $colors['highlight'] . ";
@highlightext:  " . wc_light_or_dark( $colors['highlight'], 'desaturate(darken(@highlight,60%),18%)', 'desaturate(lighten(@highlight,60%),18%)' ) . ";

@contentbg:     " . $colors['content_bg'] . ";

@subtext:       " . $colors['subtext'] . ";
            ";

				// Save the original base for later
				$original_base = file_get_contents( $base_file, null, null, null, 1024 );

				if ( trim( $color_rules ) != trim( $original_base ) ) {
					file_put_contents( $base_file, $color_rules );

					$less         = new lessc;
					$compiled_css = $less->compileFile( $less_file );
					$compiled_css = CssMin::minify( $compiled_css );

					if ( $compiled_css ) {
						file_put_contents( $css_file, $compiled_css );
					}
				}

				// Swap the woocommerce.css file with the new preview file in the style queue
				add_action( 'wp_enqueue_scripts', array( $this, 'preview_frontend_styles' ), 20 );

				// Reset the base
				file_put_contents( $base_file, $original_base );
			} catch ( exception $ex ) {
				wp_die( __( 'Could not compile woocommerce.less:', 'make-plus' ) . ' ' . $ex->getMessage() );
			}
		}
	}

	/**
	 * Re-compile the WooCommerce stylesheet when color changes are saved
	 *
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	public function save_frontend_styles() {
		// Load the LESS compile function
		if ( class_exists( 'WC' ) && ! function_exists( 'woocommerce_compile_less_styles' ) ) {
			// Include the file with the compile function
			$file = WC()->plugin_path() . '/includes/admin/wc-admin-functions.php';
			if ( file_exists( $file ) ) {
				include_once( $file );
			}
		}

		// If the function was successfully loaded, run it
		if ( function_exists( 'woocommerce_compile_less_styles' ) ) {
			woocommerce_compile_less_styles();
		}
	}

	/**
	 * Utility function to determine if an action/filter hook has a particular class method added to it.
	 *
	 * @since  1.0.0.
	 *
	 * @param  string         $tag       The action/filter hook tag.
	 * @param  string         $class     The class.
	 * @param  string         $method    The class method.
	 * @return bool|string               The encoded class/method id attached to the hook.
	 */
	public function has_method_filter( $tag, $class, $method ) {
		global $wp_filter;
		$callback = false;

		if ( isset( $wp_filter[$tag] ) ) {
			foreach ( $wp_filter[$tag] as $priority ) {
				foreach ( $priority as $cb => $action ) {
					if ( is_array( $action['function'] ) && $class === get_class( $action['function'][0] ) && $method === $action['function'][1] ) {
						$callback = $cb;
						break;
					}
				}
				if ( false !== $callback ) {
					break;
				}
			}
		}

		return $callback;
	}

	/**
	 * Add a description to the Highlight Color control.
	 *
	 * @since 1.5.0.
	 *
	 * @param $text
	 *
	 * @return string
	 */
	public function color_highlight_description( $text ) {
		$description = __( 'For WooCommerce, used for prices, in stock labels, and sales flash.', 'make-plus' );

		if ( '' !== $text ) {
			$text .= ' ';
		}

		return $text . $description;
	}
}

/**
 * Instantiate or return the one TTFMP_WooCommerce_Legacy_Color instance.
 *
 * @since 1.5.0.
 *
 * @return TTFMP_WooCommerce_Legacy_Color
 */
function ttfmp_woocommerce_legacy_color() {
	return TTFMP_WooCommerce_Legacy_Color::instance();
}

// Initialize the legacy color functions right away.
ttfmp_woocommerce_legacy_color()->init();

if ( ! function_exists( 'ttfmp_woocommerce_add_color_css' ) ) :
/**
 * Use Make's color options to override some of WooCommerce's CSS styles
 *
 * @since 1.0.0.
 *
 * @return void
 */
function ttfmp_woocommerce_add_color_css() {
	// Get and escape options
	$color_primary         = maybe_hash_hex_color( get_theme_mod( 'color-primary', ttfmake_get_default( 'color-primary' ) ) );
	$color_secondary       = maybe_hash_hex_color( get_theme_mod( 'color-secondary', ttfmake_get_default( 'color-secondary' ) ) );
	$color_text            = maybe_hash_hex_color( get_theme_mod( 'color-text', ttfmake_get_default( 'color-text' ) ) );
	$color_detail          = maybe_hash_hex_color( get_theme_mod( 'color-detail', ttfmake_get_default( 'color-detail' ) ) );
	$color_highlight       = maybe_hash_hex_color( get_theme_mod( 'color-highlight', ttfmake_get_default( 'color-highlight' ) ) );
	$main_background_color = maybe_hash_hex_color( get_theme_mod( 'main-background-color', ttfmake_get_default( 'main-background-color' ) ) );

	// Output the rules
	ttfmake_get_css()->add( array(
		'selectors'    => array(
			'.woocommerce #content div.product .woocommerce-tabs ul.tabs li',
			'.woocommerce div.product .woocommerce-tabs ul.tabs li',
			'.woocommerce-page #content div.product .woocommerce-tabs ul.tabs li',
			'.woocommerce-page div.product .woocommerce-tabs ul.tabs li'
		),
		'declarations' => array(
			'background-color' => $color_secondary
		)
	) );
	ttfmake_get_css()->add( array(
		'selectors'    => array(
			'.product_meta',
			'.product_meta a'
		),
		'declarations' => array(
			'color' => $color_detail
		)
	) );
	ttfmake_get_css()->add( array(
		'selectors'    => array(
			'product_meta a:hover',
		),
		'declarations' => array(
			'color' => $color_primary
		)
	) );
	ttfmake_get_css()->add( array(
		'selectors'    => array(
			'.woocommerce #content .quantity .minus',
			'.woocommerce #content .quantity .plus',
			'.woocommerce .quantity .minus',
			'.woocommerce .quantity .plus',
			'.woocommerce-page #content .quantity .minus',
			'.woocommerce-page #content .quantity .plus',
			'.woocommerce-page .quantity .minus',
			'.woocommerce-page .quantity .plus',
			'.woocommerce #content .quantity input.qty',
			'.woocommerce .quantity input.qty',
			'.woocommerce-page #content .quantity input.qty',
			'.woocommerce-page .quantity input.qty',
		),
		'declarations' => array(
			'background-color' => $color_secondary
		)
	) );
	ttfmake_get_css()->add( array(
		'selectors'    => array(
			'.woocommerce #content .quantity .minus',
			'.woocommerce #content .quantity .plus',
			'.woocommerce .quantity .minus',
			'.woocommerce .quantity .plus',
			'.woocommerce-page #content .quantity .minus',
			'.woocommerce-page #content .quantity .plus',
			'.woocommerce-page .quantity .minus',
			'.woocommerce-page .quantity .plus',
			'.woocommerce #content .quantity input.qty',
			'.woocommerce .quantity input.qty',
			'.woocommerce-page #content .quantity input.qty',
			'.woocommerce-page .quantity input.qty',
		),
		'declarations' => array(
			'background-color' => $color_secondary
		)
	) );
	ttfmake_get_css()->add( array(
		'selectors'    => array(
			'.woocommerce #content .quantity .minus:hover',
			'.woocommerce #content .quantity .plus:hover',
			'.woocommerce .quantity .minus:hover',
			'.woocommerce .quantity .plus:hover',
			'.woocommerce-page #content .quantity .minus:hover',
			'.woocommerce-page #content .quantity .plus:hover',
			'.woocommerce-page .quantity .minus:hover',
			'.woocommerce-page .quantity .plus:hover',
		),
		'declarations' => array(
			'background-color' => $main_background_color
		)
	) );
	ttfmake_get_css()->add( array(
		'selectors'    => array(
			'.woocommerce #content input.button.alt',
			'.woocommerce #respond input#submit.alt',
			'.woocommerce a.button.alt',
			'.woocommerce button.button.alt',
			'.woocommerce input.button.alt',
			'.woocommerce-page #content input.button.alt',
			'.woocommerce-page #respond input#submit.alt',
			'.woocommerce-page a.button.alt',
			'.woocommerce-page button.button.alt',
			'.woocommerce-page input.button.alt',
			'.woocommerce #content input.button.alt:hover',
			'.woocommerce #respond input#submit.alt:hover',
			'.woocommerce a.button.alt:hover',
			'.woocommerce button.button.alt:hover',
			'.woocommerce input.button.alt:hover',
			'.woocommerce-page #content input.button.alt:hover',
			'.woocommerce-page #respond input#submit.alt:hover',
			'.woocommerce-page a.button.alt:hover',
			'.woocommerce-page button.button.alt:hover',
			'.woocommerce-page input.button.alt:hover',
		),
		'declarations' => array(
			'background-color' => $color_primary
		)
	) );
	ttfmake_get_css()->add( array(
		'selectors'    => array(
			'.woocommerce #content input.button',
			'.woocommerce #respond input#submit',
			'.woocommerce a.button',
			'.woocommerce button.button',
			'.woocommerce input.button',
			'.woocommerce-page #content input.button',
			'.woocommerce-page #respond input#submit',
			'.woocommerce-page a.button',
			'.woocommerce-page button.button',
			'.woocommerce-page input.button',
			'.woocommerce #content input.button:hover',
			'.woocommerce #respond input#submit:hover',
			'.woocommerce a.button:hover',
			'.woocommerce button.button:hover',
			'.woocommerce input.button:hover',
			'.woocommerce-page #content input.button:hover',
			'.woocommerce-page #respond input#submit:hover',
			'.woocommerce-page a.button:hover',
			'.woocommerce-page button.button:hover',
			'.woocommerce-page input.button:hover',
		),
		'declarations' => array(
			'background-color' => $color_secondary
		)
	) );
	ttfmake_get_css()->add( array(
		'selectors'    => array(
			'.woocommerce span.onsale',
			'.woocommerce-page span.onsale',
		),
		'declarations' => array(
			'background-color' => $color_highlight
		)
	) );
	ttfmake_get_css()->add( array(
		'selectors'    => array(
			'.woocommerce .woocommerce-error',
			'.woocommerce .woocommerce-info',
			'.woocommerce .woocommerce-message',
			'.woocommerce-page .woocommerce-error',
			'.woocommerce-page .woocommerce-info',
			'.woocommerce-page .woocommerce-message',
		),
		'declarations' => array(
			'background-color' => $color_secondary
		)
	) );
	ttfmake_get_css()->add( array(
		'selectors'    => array(
			'.shipping-calculator-button',
		),
		'declarations' => array(
			'color' => $color_highlight
		)
	) );
	ttfmake_get_css()->add( array(
		'selectors'    => array(
			'.woocommerce #payment',
			'.woocommerce-page #payment',
		),
		'declarations' => array(
			'background-color' => $color_secondary
		)
	) );
	ttfmake_get_css()->add( array(
		'selectors'    => array(
			'.woocommerce #payment div.payment_box',
			'.woocommerce-page #payment div.payment_box',
		),
		'declarations' => array(
			'border-color' => $color_detail . ' !important',
			'background-color' => $color_detail
		)
	) );
	ttfmake_get_css()->add( array(
		'selectors'    => array(
			'.builder-section-content .products h3',
		),
		'declarations' => array(
			'color' => $color_text
		)
	) );
}
endif;

add_action( 'ttfmake_css', 'ttfmp_woocommerce_add_color_css' );