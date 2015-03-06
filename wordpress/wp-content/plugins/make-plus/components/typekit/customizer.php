<?php
/**
 * @package Make Plus
 */

if ( ! class_exists( 'TTFMP_Typekit_Customizer' ) ) :
/**
 * Setup the customizer to handle Typekit fonts.
 *
 * @since 1.0.0.
 */
class TTFMP_Typekit_Customizer {
	/**
	 * The Typekit ID before save.
	 *
	 * @since 1.0.0.
	 *
	 * @var   string    The Typekit ID before save.
	 */
	var $typekit_id_before_save;

	/**
	 * The one instance of TTFMP_Typekit_Customizer.
	 *
	 * @since 1.0.0.
	 *
	 * @var   TTFMP_Typekit_Customizer
	 */
	private static $instance;

	/**
	 * Instantiate or return the one TTFMP_Typekit_Customizer instance.
	 *
	 * @since  1.0.0.
	 *
	 * @return TTFMP_Typekit_Customizer
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
	 * @return TTFMP_Typekit_Customizer
	 */
	public function __construct() {
		// Add the sections
		if ( ttfmake_customizer_supports_panels() && function_exists( 'ttfmake_customizer_add_panels' ) ) {
			add_filter( 'make_customizer_typography_sections', array( $this, 'customize_register' ), 20 );
		} else {
			add_action( 'customize_register', array( $this, 'legacy_customize_register' ), 20 );
		}

		// Add scripts and styles
		add_action( 'wp_head', array( $this, 'print_typekit' ), 0 );
		add_action( 'customize_controls_enqueue_scripts', array( $this, 'customize_controls_enqueue_scripts' ) );
		add_action( 'customize_controls_print_styles', array( $this, 'customize_controls_print_styles' ) );

		// AJAX handlers
		add_action( 'wp_ajax_ttfmp_get_typekit_fonts', array( $this, 'get_typekit_fonts' ) );
		add_action( 'wp_ajax_ttfmp_reset_preview', array( $this, 'reset_preview' ) );

		// Filter the available font choices
		add_filter( 'ttfmake_all_fonts', array( $this, 'all_fonts' ) );

		// Handle saving extra options based on what the customizer saved
		add_action( 'customize_save', array( $this, 'customize_save' ) );
		add_action( 'customize_save_after', array( $this, 'customize_save_after' ) );
	}

	/**
	 * Filter to add a new Customizer section
	 *
	 * This function takes the main array of Customizer sections and adds a new one
	 * to the Typography panel.
	 *
	 * @since  1.3.3.
	 *
	 * @param  array    $sections    The array of sections to add to the Customizer.
	 * @return array                 The modified array of sections.
	 */
	public function customize_register( $sections ) {
		global $wp_customize;
		$panel = 'ttfmake_typography';
		$theme_prefix = 'ttfmake_';

		// Define the section
		$sections['font-typekit'] = array(
			'panel' => $panel,
			'title' => __( 'Typekit', 'make' ),
			'description' => __( 'Enter your Kit ID into the field below and click the "Load" button to retrieve the fonts from your kit.', 'make-plus' ),
			'options' => array(
				'typekit-id' => array(
					'setting' => array(
						'default'			=> '',
						'sanitize_callback'	=> array( $this, 'sanitize_typekit_id' ),
					),
					'control' => array(
						'label'				=> __( 'Typekit Kit ID', 'make-plus' ),
						'type'				=> 'text',
					),
				),
				'typekit-load-fonts' => array(
					'control' => array(
						'control_type'		=> 'TTFMAKE_Customize_Misc_Control',
						'type'				=> 'text',
						'description'		=> '<a href="#">' . __( 'Reset', 'make-plus' ) . '</a><a href="#">' . __( 'Load Typekit Fonts', 'make-plus' ) . '</a>',
					),
				),
				'typekit-help-text' => array(
					'control' => array(
						'control_type'		=> 'TTFMAKE_Customize_Misc_Control',
						'type'				=> 'text',
						'description'		=> sprintf(
							__( 'For more information about Typekit integration, please see the %s.', 'make-plus' ),
							sprintf(
								'<a href="%1$s">Make Plus %2$s</a>',
								'https://thethemefoundry.com/docs/make-docs/customizer/typography/',
								__( 'documentation', 'make-plus' )
							)
						),
					),
				),
			),
		);

		return $sections;
	}

	/**
	 * Add the Typekit ID input.
	 *
	 * @since  1.0.0.
	 *
	 * @param  WP_Customize_Manager    $wp_customize    Theme Customizer object.
	 * @return void
	 */
	public function legacy_customize_register( $wp_customize ) {
		// Site title font size
		$wp_customize->add_setting(
			'typekit-id',
			array(
				'default'           => '',
				'type'              => 'theme_mod',
				'sanitize_callback' => array( $this, 'sanitize_typekit_id' ),
			)
		);

		$wp_customize->add_control(
			'ttfmp-typekit-id',
			array(
				'settings' => 'typekit-id',
				'section'  => 'ttfmake_font',
				'label'    => __( 'Typekit Kit ID', 'make-plus' ),
				'type'     => 'text',
				'priority' => 450
			)
		);

		$wp_customize->add_control(
			new TTFMAKE_Customize_Misc_Control(
				$wp_customize,
				'ttfmp-typekit-load-fonts',
				array(
					'section'     => 'ttfmake_font',
					'type'        => 'text',
					'description' => '<a href="#">' . __( 'Reset', 'make-plus' ) . '</a><a href="#">' . __( 'Load Typekit Fonts', 'make-plus' ) . '</a>',
					'priority'    => 460
				)
			)
		);

		$wp_customize->add_control(
			new TTFMAKE_Customize_Misc_Control(
				$wp_customize,
				'ttfmp-typekit-documentation',
				array(
					'section'     => 'ttfmake_font',
					'type'        => 'text',
					'description' => sprintf( __( 'For more information about Typekit integration, please see <a href="%s">Make Plus\' documentation</a>.', 'make-plus' ), 'https://thethemefoundry.com/docs/make-docs/customizer/typography/' ),
					'priority'    => 470
				)
			)
		);
	}

	/**
	 * Maybe enqueue Typekit styles.
	 *
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	public function print_typekit() {
		$id = $this->get_typekit_id();

		if ( '' !== $id && true === $this->is_typekit_used() ) : ?>
			<script type="text/javascript" src="//use.typekit.net/<?php echo $this->sanitize_typekit_id( $id ); ?>.js"></script>
			<script type="text/javascript">try{Typekit.load();}catch(e){}</script>
		<?php endif;
	}

	/**
	 * Determine if a single Typekit font is used.
	 *
	 * @since  1.0.0.
	 *
	 * @return bool    True if a Typekit font is used; False if no Typekit fonts are used.
	 */
	public function is_typekit_used() {
		// Grab the font choices
		if ( ttfmake_customizer_supports_panels() && function_exists( 'ttfmake_get_font_property_option_keys' ) ) {
			$font_keys = ttfmake_get_font_property_option_keys( 'family' );
		} else {
			$font_keys = array( 'font-site-title', 'font-header', 'font-body', );
		}
		$fonts = array();
		foreach ( $font_keys as $key ) {
			$fonts[] = get_theme_mod( $key, ttfmake_get_default( $key ) );
		}

		// De-dupe the fonts
		$fonts         = array_unique( $fonts );
		$allowed_fonts = $this->get_typekit_choices();

		foreach ( $fonts as $key => $font ) {
			if ( isset( $allowed_fonts[ $font ] ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Get the list of Typekit fonts in the current Typekit Kit.
	 *
	 * @since  1.0.0.
	 *
	 * @return array    Array of Typekit fonts available to the kit.
	 */
	public function get_typekit_choices() {
		$choices = get_theme_mod( 'typekit-temp-choices', array() );
		if ( empty( $choices ) ) {
			$choices = get_theme_mod( 'typekit-choices', array() );
		}

		if ( is_array( $choices ) && ! empty( $choices ) ) {
			// Sanitize the stored array of choices.
			$keys = array_map( 'esc_attr', array_keys( $choices ) );
			$values = array();
			foreach ( $choices as $data ) {
				if ( isset( $data['label'] ) && isset( $data['stack'] ) ) {
					$values[] = array(
						'label' => wp_strip_all_tags( $data['label'] ),
						'stack' => wp_strip_all_tags( $data['stack'] ),
					);
				}
			}
			return array_combine( $keys, $values );
		}

		return array();
	}

	/**
	 * Get the list of Typekit ID for the current Typekit Kit.
	 *
	 * @since  1.0.0.
	 *
	 * @return string    ID for the current Typekit Kit.
	 */
	public function get_typekit_id() {
		return ( '' !== get_theme_mod( 'typekit-temp-id', '' ) ) ? get_theme_mod( 'typekit-temp-id' ) : get_theme_mod( 'typekit-id', '' );
	}

	/**
	 * Add scripts to the customizer.
	 *
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	public function customize_controls_enqueue_scripts() {
		wp_enqueue_script(
			'ttfmp-typekit-customizer',
			ttfmp_get_typekit()->url_base . '/js/customizer-typekit.js',
			array(
				'jquery',
				'customize-controls',
				'wp-util',
			),
			TTFMAKE_VERSION,
			true
		);

		$typekit_choices = get_theme_mod( 'typekit-choices', array() );
		$option_keys = ( ttfmake_customizer_supports_panels() && function_exists( 'ttfmake_get_font_property_option_keys' ) ) ? ttfmake_get_font_property_option_keys( 'family' ) : array( 'font-site-title', 'font-header', 'font-body', );

		wp_localize_script(
			'ttfmp-typekit-customizer',
			'ttfmpTypekitData',
			array(
				'nonce'          => wp_create_nonce( 'ttfmp-typekit-request' ),
				'headerLabel'    => __( 'Typekit Fonts', 'make-plus' ),
				'noInputError'   => __( 'Please enter your Typekit Kit ID', 'make-plus' ),
				'ajaxError'      => __( 'Typekit fonts could not be found. Please try again', 'make-plus' ),
				'success'        => __( 'Fonts loaded successfully.', 'make-plus' ),
				'resetSuccess'   => __( 'Fonts reset.', 'make-plus' ),
				'typekitChoices' => ( ! empty( $typekit_choices ) ) ? array_keys( $typekit_choices ) : array(),
				'optionKeys'     => $option_keys,
			)
		);
	}

	/**
	 * Add styles for the Typekit customizer controls.
	 *
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	public function customize_controls_print_styles() {
	?>
		<style type="text/css">
			#customize-control-ttfmp-typekit-load-fonts .error,
			#customize-control-ttfmake_typekit-load-fonts .error {
				color: red !important;
				margin-bottom: 10px;
				display: block;
			}
			#customize-control-ttfmp-typekit-load-fonts .success,
			#customize-control-ttfmake_typekit-load-fonts .success {
				color: blue !important;
				margin-bottom: 10px;
				display: block;
			}
			#customize-control-ttfmp-typekit-load-fonts .description,
			#customize-control-ttfmake_typekit-load-fonts .description {
				margin-top: 10px;
			}
			#customize-control-ttfmp-typekit-load-fonts .button,
			#customize-control-ttfmake_typekit-load-fonts .button {
				font-style: normal;
			}
			#customize-control-ttfmp-typekit-load-fonts .load-fonts,
			#customize-control-ttfmake_typekit-load-fonts .load-fonts {
				margin-left: 5px;
			}
			#customize-control-ttfmp-typekit-load-fonts .spinner,
			#customize-control-ttfmake_typekit-load-fonts .spinner {
				display: inline-block;
				margin-top: 4px;
				vertical-align: middle;
			}
		</style>
	<?php
	}

	/**
	 * Append the Typekit fonts to the array of font choices.
	 *
	 * @since  1.0.0.
	 *
	 * @param  array    $choices    The current font choices.
	 * @return array                The updated font choices.
	 */
	public function all_fonts( $choices ) {
		$typekit_fonts = $this->get_typekit_choices();

		if ( ! empty( $typekit_fonts ) ) {
			$choices = array_merge( $typekit_fonts, $choices );
			$choices = array_merge( array(
				0 => array(
					'label' => sprintf( '--- %s ---', __( 'Typekit Fonts', 'make-plus' ) )
				)
			), $choices );
		}

		return $choices;
	}

	/**
	 * Callback to handle the AJAX request.
	 *
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	public function get_typekit_fonts() {
		// Make sure we have got the data we are expecting.
		$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : '';
		$id    = isset( $_POST['id'] ) ? $this->sanitize_typekit_id( $_POST['id'] ) : '';

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX && wp_verify_nonce( $nonce, 'ttfmp-typekit-request' ) && ! empty( $id ) ) {
			$response      = wp_remote_get( 'https://typekit.com/api/v1/json/kits/' . $id . '/published' );
			$response_code = wp_remote_retrieve_response_code( $response );
			$response_body = json_decode( wp_remote_retrieve_body( $response ) );

			if ( 200 === (int) $response_code && is_object( $response_body ) && isset( $response_body->kit ) && isset( $response_body->kit->families ) && is_array( $response_body->kit->families ) ) {
				$php_options = array();
				$js_options  = array();

				// Package the new select options
				foreach ( $response_body->kit->families as $family ) {
					// This format is needed to plug into the existing fonts
					$php_options[ sanitize_title_with_dashes( $family->slug ) ] = array(
						'label' => wp_strip_all_tags( $family->name ),
						'stack' => ( isset( $family->css_stack ) ) ? wp_strip_all_tags( $family->css_stack ) : '',
					);

					// Key/value pair for JS
					$js_options[ sanitize_title_with_dashes( $family->slug ) ] = wp_strip_all_tags( $family->name );
				}

				// Save the current choices to a theme mod
				set_theme_mod( 'typekit-temp-choices', $php_options );

				// Since we have a successful response, save the Typekit Kit ID
				set_theme_mod( 'typekit-temp-id', $id );

				wp_send_json_success( $js_options );
			} else {
				wp_send_json_error( $response_body );
			}
		} else {
			wp_send_json_error( new WP_Error() );
		}
	}

	/**
	 *
	 */
	public function reset_preview() {
		// Make sure we have got the data we are expecting.
		$nonce = isset( $_POST['nonce'] ) ? $_POST['nonce'] : '';

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX && wp_verify_nonce( $nonce, 'ttfmp-typekit-request' ) ) {
			$this->remove_temp_mods();

			$saved_fonts = array();
			$option_keys = ( ttfmake_customizer_supports_panels() && function_exists( 'ttfmake_get_font_property_option_keys' ) ) ? ttfmake_get_font_property_option_keys( 'family' ) : array( 'font-site-title', 'font-header', 'font-body', );
			foreach ( $option_keys as $key ) {
				$saved_fonts[ $key ] = ttfmake_sanitize_font_choice( get_theme_mod( $key, ttfmake_get_default( $key ) ) );
			}

			wp_send_json_success( $saved_fonts );
		} else {
			wp_send_json_error( new WP_Error( 403 ) );
		}
	}

	/**
	 * Denote the value of the Typekit ID before saving a new one.
	 *
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	public function customize_save() {
		$this->typekit_id_before_save = get_theme_mod( 'typekit-id' );
	}

	/**
	 * Potentially update the typekit choices and remove temporary choices.
	 *
	 * When the customizer is saved, the temporary values need to be cleaned. The temp choices that are saved
	 * during the AJAX request to Typekit need to be moved to the real choices and the temp choices need to be removed.
	 * Additionally, the temp ID needs to be removed to indicate that the state is no longer preview.
	 *
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	public function customize_save_after() {
		$typekit_id_has_changed                      = ( $this->typekit_id_before_save !== get_theme_mod( 'typekit-id' ) );
		$typekit_id_has_not_changed_but_choices_have = ( $this->typekit_id_before_save === get_theme_mod( 'typekit-id' ) && get_theme_mod( 'typekit-choices' ) !== get_theme_mod( 'typekit-temp-choices' ) );

		// If the Typekit ID is empty remove it and the Typekit choices
		if ( '' === get_theme_mod( 'typekit-id' ) ) {
			remove_theme_mod( 'typekit-id' );
			remove_theme_mod( 'typekit-choices' );

		// Determine if the Typekit ID has changed
		} else if ( true === $typekit_id_has_changed || ( true === $typekit_id_has_not_changed_but_choices_have && get_theme_mod( 'typekit-temp-choices' ) ) ) {
			set_theme_mod( 'typekit-choices', get_theme_mod( 'typekit-temp-choices' ) );
		}

		// Remove options that are no longer needed
		$this->remove_temp_mods();
	}

	/**
	 * Ensure that Typekit IDs are [a-z0-9] only.
	 *
	 * @since  1.0.0.
	 *
	 * @param  string    $value    The dirty ID.
	 * @return string              The clean ID.
	 */
	public function sanitize_typekit_id( $value ) {
		return preg_replace( '/[^0-9a-z]+/', '', $value );
	}

	/**
	 * Remove temporary id and choice values stored in the theme mods array.
	 *
	 * @since 1.5.0.
	 */
	public function remove_temp_mods() {
		remove_theme_mod( 'typekit-temp-choices' );
		remove_theme_mod( 'typekit-temp-id' );
	}
}
endif;

if ( ! function_exists( 'ttfmp_get_typekit_customizer' ) ) :
/**
 * Instantiate or return the one TTFMP_Typekit_Customizer instance.
 *
 * @since  1.0.0.
 *
 * @return TTFMP_Typekit_Customizer
 */
function ttfmp_get_typekit_customizer() {
	return TTFMP_Typekit_Customizer::instance();
}
endif;

ttfmp_get_typekit_customizer();