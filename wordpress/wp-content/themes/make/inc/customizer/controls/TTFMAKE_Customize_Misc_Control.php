<?php
/**
 * @package Make
 */

if ( class_exists( 'WP_Customize_Control' ) && ! class_exists( 'TTFMAKE_Customize_Misc_Control' ) ) :
/**
 * Class TTFMAKE_Customize_Misc_Control
 *
 * Control for adding arbitrary HTML to a Customizer section.
 *
 * @since 1.0.0.
 */
class TTFMAKE_Customize_Misc_Control extends WP_Customize_Control {
	/**
	 * The current setting name.
	 *
	 * @since 1.0.0.
	 *
	 * @var   string    The current setting name.
	 */
	public $settings = 'blogname';

	/**
	 * The current setting description.
	 *
	 * @since 1.0.0.
	 *
	 * @var   string    The current setting description.
	 */
	public $description = '';

	/**
	 * Render the description and title for the section.
	 *
	 * Prints arbitrary HTML to a customizer section. This provides useful hints for how to properly set some custom
	 * options for optimal performance for the option.
	 *
	 * @since  1.0.0.
	 *
	 * @return void
	 */
	public function render_content() {
		switch ( $this->type ) {
			case 'group-title' :
				echo '<h4 class="ttfmake-control-group-title">' . $this->label . '</h4>';
				if ( '' !== $this->description ) {
					echo '<span class="description customize-control-description">' . $this->description . '</span>';
				}
				break;
			case 'heading' :
				echo '<span class="customize-control-title">' . $this->label . '</span>';
				break;
			default:
			case 'text' :
				echo '<p class="description customize-control-description">' . $this->description . '</p>';
				break;
			case 'line' :
				echo '<hr />';
				break;
		}
	}
}
endif;