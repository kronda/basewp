<?php

/**
* Toolset_Common_Bootstrap
*
* General class to manage common code loading for all Toolset plugins
*
* This class is used to load Toolset Common into all Toolset plugins that have it as a dependency.
* Note that Assets, Menu, Utils, Settings, Localization, Promotion, Debug, Admin Bar and WPML compatibility are always loaded when first instantiating this class.
* Toolset_Common_Bootstrap::load_sections must be called after_setup_theme:10 with an array of sections to load, named as follows:
* 	toolset_forms						Toolset Forms, the shared component for Types and CRED
* 	toolset_visual_editor				Visual Editor Addon, to display buttons and dialogs over editors
* 	toolset_parser						Toolset Parser, to parse conditionals
*
* New sections can be added here, following the same structure.
*
* Note that you have available the following constants:
* 	TOOLSET_COMMON_VERSION				The Toolset Common version
* 	TOOLSET_COMMON_PATH					The path to the active Toolset Common directory
* 	TOOLSET_COMMON_DIR					The name of the directory of the active Toolset Common
* 	TOOLSET_COMMON_URL					The URL to the root of Toolset Common, to be used in backend - adjusted as per SSL settings
* 	TOOLSET_COMMON_PROTOCOL				The protocol of TOOLSET_COMMON_URL - http | https
* 	TOOLSET_COMMON_FRONTEND_URL			The URL to the root of Toolset Common, to be used in frontend - adjusted as per SSL settings
* 	TOOLSET_COMMON_FRONTEND_PROTOCOL	The protocol of TOOLSET_COMMON_FRONTEND_URL - http | https
*
* @todo create an admin page with Common info: path, bundled libraries versions, etc
*/

class Toolset_Common_Bootstrap {

    private static $instance;
	private static $sections_loaded;
	
	public $assets_manager;
	public $object_relationship;
	public $menu;
	public $export_import_screen;
	public $settings_screen;
	public $localization;
	public $settings;
	public $promotion;
	public $wpml_compatibility;
	
    private function __construct() {
		self::$sections_loaded = array();
		// Register assets, utils, settings, localization, promotion, debug, admin bar and WPML compatibility
		$this->register_utils();
		$this->register_res();
		$this->register_libs();
		$this->register_inc();
		
		add_filter( 'toolset_is_toolset_common_available', '__return_true' );
		
		add_action( 'switch_blog', array( $this, 'clear_settings_instance' ) );
    }
	
	public static function getInstance() {
        if ( ! self::$instance ) {
            self::$instance = new Toolset_Common_Bootstrap();
        }
        return self::$instance;
    }
	
	/**
	* Load sections on demand
	*
	* This needs to be called after after_setup_theme:10 because this file is not loaded before that
	*
	* @since 1.9
	*/
	
	public function load_sections( $load = array() ) {
		
		// Load toolset_debug on demand
		if (
			empty( $load ) 
			|| in_array( 'toolset_debug', $load )
		) {
			$this->register_debug();
		}
		
		// Maybe register forms
		if (
			empty( $load ) 
			|| in_array( 'toolset_forms', $load )
		) {
			$this->register_toolset_forms();
		}
		
		// Maybe register the editor addon
		if (
			empty( $load ) 
			|| in_array( 'toolset_visual_editor', $load )
		) {
			$this->register_visual_editor();
		}
		
		if (
			empty( $load ) 
			|| in_array( 'toolset_parser', $load )
		) {
			$this->register_parser();
		}
	}
	
	public function register_res() {
		$sections_loaded = self::$sections_loaded;
		if ( ! in_array( 'toolset_res', $sections_loaded ) ) {
			$sections_loaded[] = 'toolset_res';
			// Use the class provided by Ric
			require_once( TOOLSET_COMMON_PATH . '/inc/toolset.assets.manager.class.php' );
			$this->assets_manager = Toolset_Assets_Manager::getInstance();
			self::$sections_loaded = apply_filters('toolset_register_assets_section', $sections_loaded);
		}
	}
	
	public function register_libs() {
		$sections_loaded = self::$sections_loaded;
		if ( ! in_array( 'toolset_lib', $sections_loaded ) ) {
			$sections_loaded[] = 'toolset_lib';
			if ( ! class_exists( 'ICL_Array2XML' ) ) {
				require_once( TOOLSET_COMMON_PATH . '/lib/array2xml.php' );
			}
			if ( ! class_exists( 'Zip' ) ) {
				require_once( TOOLSET_COMMON_PATH . '/lib/Zip.php' );
			}
			if ( ! function_exists( 'adodb_date' ) ) {
				require_once( TOOLSET_COMMON_PATH . '/lib/adodb-time.inc.php' );
			}
			if ( ! class_exists( 'Toolset_CakePHP_Validation' ) ) {
				require_once( TOOLSET_COMMON_PATH . '/lib/cakephp.validation.class.php' );
			}
			if ( ! class_exists( 'Toolset_Validate' ) ) {
				require_once( TOOLSET_COMMON_PATH . '/lib/validate.class.php' );
			}
			if ( ! class_exists( 'Toolset_Enlimbo_Forms' ) ) {
				require_once( TOOLSET_COMMON_PATH . '/lib/enlimbo.forms.class.php' );
			}
			self::$sections_loaded = apply_filters('toolset_register_library_section', $sections_loaded);
		}
	}
	
	public function register_inc() {
		$sections_loaded = self::$sections_loaded;
		if ( ! in_array( 'toolset_inc', $sections_loaded ) ) {
			$sections_loaded[] = 'toolset_inc';
			if ( ! class_exists( 'Toolset_Settings' ) ) {
				require_once( TOOLSET_COMMON_PATH . '/inc/toolset.settings.class.php' );
				$this->settings = Toolset_Settings::get_instance();
			}
			if ( ! class_exists( 'Toolset_Localization' ) ) {
				require_once( TOOLSET_COMMON_PATH . '/inc/toolset.localization.class.php' );
				$this->localization = new Toolset_Localization();
			}
			if ( ! class_exists( 'Toolset_WPLogger' ) ) {
				require_once( TOOLSET_COMMON_PATH . '/inc/toolset.wplogger.class.php' );
			}
			if ( ! class_exists( 'Toolset_Object_Relationship' ) ) {
				require_once( TOOLSET_COMMON_PATH . '/inc/toolset.object.relationship.class.php' );
				$this->object_relationship = Toolset_Object_Relationship::get_instance();
			}
			if ( ! class_exists( 'Toolset_Settings_Screen' ) ) {
				require_once( TOOLSET_COMMON_PATH . '/inc/toolset.settings.screen.class.php' );
				$this->settings_screen = new Toolset_Settings_Screen();
			}
			if ( ! class_exists( 'Toolset_Export_Import_Screen' ) ) {
				require_once( TOOLSET_COMMON_PATH . '/inc/toolset.export.import.screen.class.php' );
				$this->export_import_screen = new Toolset_Export_Import_Screen();
			}
			if ( ! class_exists( 'Toolset_Menu' ) ) {
				require_once( TOOLSET_COMMON_PATH . '/inc/toolset.menu.class.php' );
				$this->menu = new Toolset_Menu();
			}
			if ( ! class_exists( 'Toolset_Promotion' ) ) {
				require_once( TOOLSET_COMMON_PATH . '/inc/toolset.promotion.class.php' );
				$this->promotion = new Toolset_Promotion();
			}
			if ( ! class_exists( 'Toolset_Admin_Bar_Menu' ) ) {
				require_once( TOOLSET_COMMON_PATH . '/inc/toolset.admin.bar.menu.class.php' );
				global $toolset_admin_bar_menu;
				$toolset_admin_bar_menu = Toolset_Admin_Bar_Menu::get_instance();
			}
			if ( ! class_exists( 'Toolset_Internal_Compatibility' ) ) {
				require_once( TOOLSET_COMMON_PATH . '/inc/toolset.internal.compatibility.class.php' );
				$this->internal_compatibility = new Toolset_Internal_Compatibility();
			}
			if ( ! class_exists( 'Toolset_WPML_Compatibility' ) ) {
				require_once( TOOLSET_COMMON_PATH . '/inc/toolset.wpml.compatibility.class.php' );
				$this->wpml_compatibility = new Toolset_WPML_Compatibility();
			}
			if ( ! class_exists( 'Toolset_Relevanssi_Compatibility' ) ) {
				require_once( TOOLSET_COMMON_PATH . '/inc/toolset.relevanssi.compatibility.class.php' );
				$this->relevanssi_compatibility = new Toolset_Relevanssi_Compatibility();
			}
            
            if ( ! class_exists( 'Toolset_CssComponent' ) ) {
				require_once( TOOLSET_COMMON_PATH . '/inc/toolset.css.component.class.php' );
				$toolset_bs_component = Toolset_CssComponent::getInstance();
			}
			
			require_once( TOOLSET_COMMON_PATH . '/inc/toolset.compatibility.php' );
			require_once( TOOLSET_COMMON_PATH . '/inc/toolset.function.helpers.php' );
			require_once( TOOLSET_COMMON_PATH . '/deprecated.php' );

			self::$sections_loaded = apply_filters('toolset_register_include_section', $sections_loaded);
		}
	}
	
	public function register_utils() {
		// Although this is full of DDL prefixes, we need to actually port before using it.
		$sections_loaded = self::$sections_loaded;
		if ( ! in_array( 'toolset_utils', $sections_loaded ) ) {
			$sections_loaded[] = 'toolset_utils';
			require_once( TOOLSET_COMMON_PATH . '/utility/utils.php' );
		}
		if ( ! in_array( 'toolset_dialogs', $sections_loaded ) ) {
			$sections_loaded[] = 'toolset_dialogs';
			require_once( TOOLSET_COMMON_PATH . '/utility/dialogs/toolset.dialog-boxes.class.php' );
		}
        if( ! in_array('toolset_help_videos', $sections_loaded) ){
            $sections_loaded[] = 'toolset_help_videos';
            require_once( TOOLSET_COMMON_PATH . '/utility/help-videos/toolset-help-videos.php' );
        }
		self::$sections_loaded = apply_filters('toolset_register_utility_section', $sections_loaded);
	}
	
	public function register_debug() {
		$sections_loaded = self::$sections_loaded;
		if ( ! in_array( 'toolset_debug', $sections_loaded ) ) {
			$sections_loaded[] = 'toolset_debug';
			require_once( TOOLSET_COMMON_PATH . '/debug/debug-information.php' );
			self::$sections_loaded = apply_filters('toolset_register_debug_section', $sections_loaded);
		}
	}
	
	public function register_toolset_forms() {
		$sections_loaded = self::$sections_loaded;
		if ( ! in_array( 'toolset_forms', $sections_loaded ) ) {
			$sections_loaded[] = 'toolset_forms';
			if ( ! class_exists( 'WPToolset_Forms_Bootstrap' ) ) {
				require_once TOOLSET_COMMON_PATH . '/toolset-forms/bootstrap.php';
			}
			self::$sections_loaded = apply_filters('toolset_register_forms_section', $sections_loaded);
		}
	}
	
	public function register_visual_editor() {
		$sections_loaded = self::$sections_loaded;
		if ( ! in_array( 'toolset_visual_editor', $sections_loaded ) ) {
			$sections_loaded[] = 'toolset_visual_editor';
			require_once( TOOLSET_COMMON_PATH . '/visual-editor/editor-addon-generic.class.php' );
			require_once( TOOLSET_COMMON_PATH . '/visual-editor/editor-addon.class.php' );
			require_once( TOOLSET_COMMON_PATH . '/visual-editor/views-editor-addon.class.php' );
			self::$sections_loaded = apply_filters('toolset_register_visual_editor_section', $sections_loaded);
		}
	}
	
	public function register_parser() {
		$sections_loaded = self::$sections_loaded;
		if ( ! in_array( 'toolset_parser', $sections_loaded ) ) {
			$sections_loaded[] = 'toolset_parser';
			if ( ! class_exists( 'Toolset_Regex' ) ) {
				require_once( TOOLSET_COMMON_PATH . '/expression-parser/parser.php' );
			}
			self::$sections_loaded = apply_filters('toolset_register_parsers_section', $sections_loaded);
		}
	}
	
	public function clear_settings_instance() {
		Toolset_Settings::clear_instance();
	}
	
};