<?php

/**
 * Save handler for types settings
 * Settings are defined in Controller/Page/Extension/Settings
 *
 * @since 2.1
 */
final class Types_Ajax_Handler_Settings_Action extends Types_Ajax_Handler_Abstract {


	/**
	 * @inheritdoc
	 *
	 * @param array $arguments
	 */
	public function process_call( $arguments ) {

		$am = $this->get_am();

		$am->ajax_begin( array( 'nonce' => $am->get_action_js_name( Types_Ajax::CALLBACK_SETTINGS_ACTION ) ) );

		$setting = wpcf_getpost( 'setting' );
		$value = wpcf_getpost( 'setting_value' );

		if( !is_array( $value ) ) {
			parse_str( $value, $value );
			$value = array_pop( $value );
		}

		// use toolset settings if available
		if( class_exists( 'Toolset_Settings' )
		    && method_exists( 'Toolset_Settings', 'get_instance' ) ) {
			$toolset_settings = Toolset_Settings::get_instance();

			if( method_exists( $toolset_settings, 'save' ) ) {
				$toolset_settings[$setting] = $value;
				$toolset_settings->save();
				$am->ajax_finish( 'success', true );
			}
		} else {
			update_option( $setting, $value );
			$am->ajax_finish( 'success', true );
		}

		// default toolset setting error will be used
		// todo throw specific error
		$am->ajax_finish( array('error'), false );
	}
}