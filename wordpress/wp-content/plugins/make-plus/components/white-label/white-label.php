<?php
/**
 * @package Make Plus
 */

if ( class_exists( 'TTFMP_Customizer_Definitions' ) ) :
/**
 * Class TTFMP_Customizer_White_Label
 *
 * Add a White Label section with an option to hide the theme credit line.
 *
 * Migrated from an old Make Plus module called 'TTFMP_Customizer'.
 *
 * @since 1.5.0.
 */
class TTFMP_Customizer_White_Label extends TTFMP_Customizer_Definitions {
	/**
	 * @since 1.5.0.
	 */
	protected function hooks() {
		parent::hooks();

		// Enable White Label setting
		add_filter( 'ttfmake_show_footer_credit', array( $this, 'show_credit' ) );

		// Legacy Customizer definitions
		add_action( 'customize_register', array( $this, 'legacy_definitions' ) );
	}

	/**
	 * @since 1.5.0.
	 */
	public function defaults( $defaults ) {
		$new_defaults = array(
			// Hide credit
			'footer-hide-credit' => 0,
		);

		return array_merge( $defaults, $new_defaults );
	}

	/**
	 * @since 1.5.0.
	 */
	public function definitions( $sections ) {
		$panel = 'ttfmake_general';
		$new_sections = array();

		$new_sections['white-label'] = array(
			'panel' => $panel,
			'title' => __( 'White Label', 'make' ),
			'options' => array(
				'footer-hide-credit' => array(
					'setting' => array(
						'sanitize_callback'	=> 'absint',
					),
					'control' => array(
						'label' => __( 'Hide theme credit', 'make-plus' ),
						'type'  => 'checkbox',
					),
				),
			),
		);

		return array_merge( $sections, $new_sections );
	}

	/**
	 * Return the value of the White Label setting.
	 *
	 * @since  1.5.0.
	 *
	 * @param  bool    $bool    The original boolean
	 *
	 * @return bool             The modified boolean
	 */
	public function show_credit( $bool ) {
		return ! (bool) get_theme_mod( 'footer-hide-credit', ttfmake_get_default( 'footer-hide-credit' ) );
	}

	/**
	 * Add White Label settings and controls to the Footer section in the pre-WP 4.0 Customizer.
	 *
	 * @since  1.5.0.
	 */
	public function legacy_definitions() {
		if ( ttfmake_customizer_supports_panels() ) {
			return;
		}

		global $wp_customize;

		$social_icons = $wp_customize->get_control( 'ttfmake_footer-show-social' );

		$priority       = new TTFMAKE_Prioritizer( $social_icons->priority + 1, 1 );
		$section        = 'ttfmake_footer';
		$control_prefix = 'ttfmp_';
		$setting_prefix = 'footer';

		// White Label line
		$setting_id = $setting_prefix . '-whitelabel-line';
		$wp_customize->add_control(
			new TTFMAKE_Customize_Misc_Control(
				$wp_customize,
				$control_prefix . $setting_id,
				array(
					'section'     => $section,
					'type'        => 'line',
					'priority'    => $priority->add()
				)
			)
		);

		// White Label heading
		$setting_id = $setting_prefix . '-whitelabel-heading';
		$wp_customize->add_control(
			new TTFMAKE_Customize_Misc_Control(
				$wp_customize,
				$control_prefix . $setting_id,
				array(
					'section'  => $section,
					'type'     => 'heading',
					'label'    => __( 'White Label', 'make-plus' ),
					'priority' => $priority->add()
				)
			)
		);

		// Hide credit
		$setting_id = $setting_prefix . '-hide-credit';
		$wp_customize->add_setting(
			$setting_id,
			array(
				'default'           => ttfmake_get_default( $setting_id ),
				'type'              => 'theme_mod',
				'sanitize_callback' => 'absint',
			)
		);
		$wp_customize->add_control(
			$control_prefix . $setting_id,
			array(
				'settings' => $setting_id,
				'section'  => $section,
				'label'    => __( 'Hide theme credit', 'make-plus' ),
				'type'     => 'checkbox',
				'priority' => $priority->add()
			)
		);
	}
}

new TTFMP_Customizer_White_Label;

endif;