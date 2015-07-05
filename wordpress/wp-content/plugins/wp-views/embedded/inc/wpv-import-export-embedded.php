<?php 

/**
* wpv-import-export-embedded.php
*
* Contains methods for Views importing
*
* @package Views
*
* @since unknown
*/

/**
* WPV_Export_Import_Embedded
*
* Class to manage Views import* At the moment, it manages legacy theme import
*
* @since 1.8.0
*/

class WPV_Export_Import_Embedded {
	
	public function __construct() {
		
		/**
		* Legacy
		*
		* Keep for backwards compatibility
		* There was a time when users did embed Views into their themes
		* And they needed a way to update the items that were embedded
		* We provided a nice way to do so
		* That now is DEPRECATED but we need to keep
		*/
        		
		$this->legacy_import_php = null;
		$this->legacy_import_xml = null;
		$this->legacy_show_admin_notice = false;
		
		$this->legacy_import_timestamp = null;
		$this->legacy_import_auto_import = null;
		$this->legacy_import_affiliate_id = null;
		$this->legacy_import_affiliate_key = null;
		
		add_action( 'init', array( $this, 'legacy_init' ) );
		add_action( 'admin_notices', array( $this, 'legacy_admin_notices' ) );
		
		/**
		* Actual and current import methods
		*/
		
		$this->import_errors = null;
		$this->import_messages = array();
		
		add_action( 'wp_loaded', array( $this, 'import_on_form_submit' ) );
		add_action( 'admin_notices', array( $this, 'import_notices_errors' ) );
		add_action( 'admin_notices', array( $this, 'import_notices_messages' ) );
		
    }
	
	/*
	* ------------------------------------------------------------------------
	* LEGACY METHODS
	* ------------------------------------------------------------------------
	*/
	
	/**
	* legacy_init
	*
	* Init the theme embedded import
	*
	* @since 1.8.0
	*/
	
	function legacy_init() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		if ( defined( 'WPV_PATH_EMBEDDED' ) ) {
			if (
				file_exists( WPV_PATH_EMBEDDED . '/settings.php' )
				&& file_exists( WPV_PATH_EMBEDDED . '/settings.xml' )
			) {
				$this->legacy_import_php = WPV_PATH_EMBEDDED . '/settings.php';
				$this->legacy_import_xml = WPV_PATH_EMBEDDED . '/settings.xml';
				
				include WPV_PATH_EMBEDDED . '/settings.php';
				// This should provide two important variables:
				// $timestamp contains a timestamp for export file creation
				// $auto_import containt a boolean: ask or auto import?
				$this->legacy_import_timestamp = ( isset( $timestamp ) && is_numeric( $timestamp ) ) ? $timestamp : null;
				$this->legacy_import_auto_import = isset( $auto_import ) ? $auto_import : null;
				if (
					is_null( $this->legacy_import_timestamp )
					|| is_null( $this->legacy_import_auto_import )
				) {
					return;
				}
				// Also, might contain two optional variables:
				// $affiliate_id
				// $affiliate_key
				if (
					isset( $affiliate_id ) 
					&& isset( $affiliate_key ) 
				) {
					$this->legacy_import_affiliate_id = sanitize_text_field( $affiliate_id );
					$this->legacy_import_affiliate_key = sanitize_text_field( $affiliate_key );
				}
				
				$dismissed = get_option( 'wpv-dismissed-messages', array() );
				if ( empty( $dismissed ) ) {
					// Legacy: old option key
					// @todo we should review all this keys and stored data...
					$dismissed = get_option( 'views_dismissed_messages', array() );
				}
				if ( 
					! in_array( $timestamp, $dismissed ) 
					&& $timestamp > get_option( 'views-embedded-import', 0 )
				) {
					// something new to import, not dismissed previously
					if ( 
						$this->legacy_import_auto_import 
						&& ! isset( $_POST['import'] )
					) {
						// setup an automatic import
						$_POST['import'] = 'wpv-import';
						$_POST['wpv-import-nonce'] = wp_create_nonce( 'wpv-import-nonce' );
						$_POST['views-overwrite'] = 'on';
						$_POST['view-templates-overwrite'] = 'on';
						$_POST['import-file'] = $this->legacy_import_xml;
					} else {
						global $pagenow;
						if ( 
							$pagenow != 'options-general.php' 
							|| ! isset( $_GET['page'] ) 
							|| $_GET['page'] != 'wpv-import-theme' 
						) {
							$this->legacy_show_admin_notice = true;
						}
						add_action( 'admin_menu', array( $this, 'legacy_import_menu' ) );
					}
				}
			}
		}
	}
	
	/**
	* legacy_admin_notices
	*
	* Legacy display an admin notice when there is theme data to be imported
	*
	* @since 1.8.0
	*/
	
	function legacy_admin_notices() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		if ( $this->legacy_show_admin_notice ) {
		?>
		<div class="message-updated updated">
			<p>
			<?php
			_e( '<strong>Views</strong> has some bundled data waiting to be imported.', 'wpv-views' );
			?>
			</p>
			<p>
			<?php
			echo sprintf( __( '%sClick here to import%s %sDismiss this message%s', 'wpv-views' ),
				'<a href="' . admin_url( 'options-general.php' ) . '?page=wpv-import-theme" class="button button-primary">',
				'</a>',
				'<a class="js-wpv-embedded-import-dismiss button button-secondary" onclick="var data = {action: \'wpv_dismiss_message\', message_id: \'embedded-import-' . $this->legacy_import_timestamp . '\', timestamp: ' . $this->legacy_import_timestamp . ', _wpnonce: \'' . wp_create_nonce( 'dismiss_message' ) . '\'};jQuery.get(ajaxurl, data, function(response) {jQuery(\'.js-wpv-embedded-import-dismiss\').parent().parent().fadeOut();});return false;" href="#">',
				'</a>' 
			);
			?>
			</p>
		</div>
		<?php
		}
	}
	
	/**
	* legacy_import_menu
	*
	* Add an Options page for legacy theme data import
	*
	* @since 1.8.0
	*/
	
	function legacy_import_menu() {
		add_options_page(
			__( 'Import Views for theme', 'wpv-views' ),
			'Import Views',
			'manage_options',
			'wpv-import-theme',
			array( $this, 'legacy_import_views_from_theme' ) 
		);
	}
	
	/**
	* legacy_import_views_from_theme
	*
	* Content of the Options page for legacy theme data import
	*
	* @since 1.8.0
	*/
	
	function legacy_import_views_from_theme() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		if ( 
			isset( $_POST['import'] )
			&& (
				$_POST['import'] == __( 'Import', 'wpv-views' ) 
				|| $_POST['import'] == 'wpv-import'
			)
			&& wp_verify_nonce( $_POST['wpv-import-nonce'], 'wpv-import-nonce' )
			&& ! $this->import_errors 
		) {
			?>
			<div class="wrap">
				<div id="icon-views" class="icon32"><br /></div>
				<h2><?php _e( 'Views Import' , 'wpv-views' ); ?></h2>
				<br />
				<h3><?php _e( 'Views import complete', 'wpv-views' ) ?></h3>
			</div>
			<?php
		} else {
			?>
			<div class="wrap">
				<div id="icon-views" class="icon32"><br /></div>
				<h2><?php _e( 'Views Import', 'wpv-views' ); ?></h2>
				<br />
				<?php wpv_admin_import_form( $this->legacy_import_xml ); ?>
			</div>
			<?php
		}
	}
	
	/**
	* legacy_get_affiliate_url
	*
	* Legacy get affiliate URL from data on the theme import files
	*
	* @since 1.8.0
	*/
	
	function legacy_get_affiliate_url() {
		$affiliate_url = '?utm_source=viewsplugin&utm_campaign=views&utm_medium=affiliate-link&utm_term=http://www.wp-types.com';
		if (
			! is_null( $this->legacy_import_affiliate_id ) 
			&& ! is_null( $this->legacy_import_affiliate_key )
		) {
			$affiliate_url = '&aid=' . $this->legacy_import_affiliate_id . '&affiliate_key=' . $this->legacy_import_affiliate_key;
		}
		return $affiliate_url;
	}
	
	/**
	* legacy_set_updated_timestamp
	*
	* Legacy update timestamp for last data theme imported
	*
	* @since 1.8.0
	*/
	
	function legacy_set_updated_timestamp() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		global $pagenow;
		if (
			! is_null( $this->legacy_import_timestamp ) 
			&& $pagenow == 'options-general.php' 
			&& isset( $_GET['page'] ) 
			&& $_GET['page'] == 'wpv-import-theme'
		) {
			update_option( 'views-embedded-import', $this->legacy_import_timestamp );
		}
	}
	
	/*
	* ------------------------------------------------------------------------
	* LEGACY METHODS - END
	* ------------------------------------------------------------------------
	*/
	
	/**
	*check_import_status_from_file
	*
	* Given an import XML, check whether items to import already exist and, if needed their edit status
	*
	* @param $path (string) Path to the file to import
	*
	* @return (array|WP_Error)
	*
	* @since 1.8.0
	*/
	
	static function check_import_status_from_file( $path = null ) {
		if ( 
			is_null( $path ) 
			|| ! file_exists( $path )
		) {
			return new WP_Error( 'could_not_open_file', __( 'Could not read the Views import file.', 'wpv-views' ) );
		} else {
			$data = array();
			$return = array(
				'view' => array(),
				'wordpress_archive' => array(),
				'content_template' => array()
			);
			$info = pathinfo( $path );
			$is_xml = $info['extension'] == 'xml' ? true : false;
			if ( $is_xml ) {
				$file_size = filesize( $path );
				$fh = fopen( $path, 'r' );
				if ( $fh ) {
					$data = fread( $fh, $file_size );
					fclose( $fh );
				}
			}
			if ( ! empty( $data ) ) {
				if ( ! function_exists( 'simplexml_load_string' ) ) {
					return new WP_Error( 'xml_missing', __( 'The Simple XML library is missing.', 'wpv-views' ) );
				}
				$xml = simplexml_load_string( $data );
				if ( ! $xml ) {
					return new WP_Error( 'not_xml_file', sprintf( __( 'The XML file (%s) could not be read.', 'wpv-views' ), $path ) );
				}
				global $wpdb;
				$import_data = wpv_admin_import_export_simplexml2array( $xml );
				// Check Content Templates
				if ( isset( $import_data['view-templates']['view-template'] ) ) {
					$view_templates = $import_data['view-templates']['view-template'];
					// check for a single Content Template
					if ( ! isset( $view_templates[0] ) ) {
						$view_templates = array( $view_templates );
					}
					$view_templates_to_import = array();
					foreach ( $view_templates as $view_template ) {
						$view_templates_to_import[$view_template['ID']] = array(
							'post_name' => $view_template['post_name'],
							'post_title' => $view_template['post_title'],
							'post_edited' => false
						);
					}
					if ( count( $view_templates_to_import ) > 0 ) {
						$view_templates_post_name_to_check = wp_list_pluck( $view_templates_to_import, 'post_name' );
						$view_templates_edited = $wpdb->get_col( 
							$wpdb->prepare( 
								"SELECT p.post_name FROM {$wpdb->posts} p LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
								WHERE p.post_name IN ('" . implode( "','", $view_templates_post_name_to_check ) . "') 
								AND p.post_type = %s 
								AND pm.meta_key = %s
								AND pm.meta_value IS NOT NULL
								LIMIT %d", 
								'view-template',
								'_toolset_edit_last',
								count( $view_templates_to_import )
							) 
						);
						if ( count( $view_templates_edited ) > 0 ) {
							foreach ( $view_templates_to_import as $template_key => $template_data ) {
								if ( in_array( $template_data['post_name'], $view_templates_edited ) ) {
									$view_templates_to_import[$template_key]['post_edited'] = true;
								}
							}
						}
					}
					$return['content_template'] = $view_templates_to_import;
				}
				// Check Views and WPAs
				if ( isset( $import_data['views']['view'] ) ) {
					$views = $import_data['views']['view'];
					// check for a single view
					if ( ! isset( $views[0] ) ) {
						$views = array( $views );
					}
					$views_to_import = array();
					$wpa_to_import = array();
					$items_post_name_to_import = array();
					foreach ( $views as $view ) {
						if (
							! isset( $view['meta']['_wpv_settings']['view-query-mode'] )
							|| $view['meta']['_wpv_settings']['view-query-mode'] == 'normal'
						) {
							$views_to_import[$view['ID']] = array(
								'post_name' => $view['post_name'],
								'post_title' => $view['post_title'],
								'post_edited' => false
							);
							$items_post_name_to_import[] = $view['post_name'];
						} else {
							$wpa_to_import[$view['ID']] = array(
								'post_name' => $view['post_name'],
								'post_title' => $view['post_title'],
								'post_edited' => false
							);
							$items_post_name_to_import[] = $view['post_name'];
						}
					}
					if ( count( $items_post_name_to_import ) > 0 ) {
						$items_edited = $wpdb->get_col( 
							$wpdb->prepare( 
								"SELECT p.post_name FROM {$wpdb->posts} p LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id
								WHERE p.post_name IN ('" . implode( "','", $items_post_name_to_import ) . "') 
								AND p.post_type = %s 
								AND pm.meta_key = %s
								AND pm.meta_value IS NOT NULL
								LIMIT %d", 
								'view',
								'_toolset_edit_last',
								count( $items_post_name_to_import )
							) 
						);
						if ( count( $items_edited ) > 0 ) {
							foreach ( $views_to_import as $view_key => $view_data ) {
								if ( in_array( $view_data['post_name'], $items_edited ) ) {
									$views_to_import[$view_key]['post_edited'] = true;
								}
							}
							foreach ( $wpa_to_import as $wpa_key => $wpa_data ) {
								if ( in_array( $wpa_data['post_name'], $items_edited ) ) {
									$wpa_to_import[$wpa_key]['post_edited'] = true;
								}
							}
						}
					}
					$return['view'] = $views_to_import;
					$return['wordpress_archive'] = $wpa_to_import;
				}
				return $return;
			} else {
				return new WP_Error( 'could_not_open_file', __( 'Could not read the Views import file.', 'wpv-views' ) );
			}
		}
	}
	
	/**
	* import_on_form_submit
	*
	* Executes an import workflow based on $_POSTed parameters and a nonce, taking care of other $_POSTed data
	*
	* @since 1.8.0
	*/
	
	function import_on_form_submit() {
		if ( 
			current_user_can( 'manage_options' )
			&& isset( $_POST['import'] ) 
			&& (
				$_POST['import'] == __( 'Import', 'wpv-views' ) 
				|| $_POST['import'] == 'wpv-import'
			)
			&& isset( $_POST['wpv-import-nonce'] ) 
			&& wp_verify_nonce( $_POST['wpv-import-nonce'], 'wpv-import-nonce' )
		) {
			$args = array();
			$posted_args = array(
				'views-overwrite', 'views-delete',
				'view-templates-overwrite', 'view-templates-delete',
				'view-settings-overwrite'
			);
			foreach ( $posted_args as $mod_arg ) {
				if ( 
					isset( $_POST[$mod_arg] ) 
					&& $_POST[$mod_arg] != 'off' // Legacy
				) {
					$args[$mod_arg] = 'on';
				}
			}
			$this->import_errors = wpv_admin_import_data( $args );
			if ( sizeof( $this->import_messages ) ) {
				$this->legacy_set_updated_timestamp();
			}
		}
	}
	
	/**
	* import_notices_errors
	*
	* Display admin notices related to import errors
	*
	* @since 1.8.0
	*/
	
	function import_notices_errors() {
		if ( 
			! is_null( $this->import_errors ) 
			&& is_wp_error( $this->import_errors )
		) {
		?>
		<div class="message error"><p><?php echo $this->import_errors->get_error_message() ?></p></div>
		<?php
		}
	}
	
	/**
	* import_notices_messages
	*
	* Display admin notices related to import messages
	*
	* @since 1.8.0
	*/
	
	function import_notices_messages() {
		if ( sizeof( $this->import_messages ) ) {
			foreach ( $this->import_messages as $message ) {
				?>
				<div class="message updated"><p><?php echo $message ?></p></div>
				<?php
			}
		}
	}
	
	/**
	* import_data
	*
	* Main import method for Views
	*
	* @param $args (array) Import modifiers
	*	'import-file' 				path to the file to import, can be overriden by $_FILES['import-file'] and $_POST['import-file']
	*	'views-overwrite' 			will force overwriting existing Views and WPA
	* 	'views-delete' 				will delete any existing Views and WPA that are not on the import file
	* 	'view-templates-overwrite'	will force overwriting existing CT
	* 	'view-templates-delete' 	will delete any existing CT that is not on the import file
	* 	'view-settings-overwrite' 	will overwrite existing settings
	*	'force_import_id,			will only import items with those XML IDs
	*	'force_import_post_name',	will only import items with those XML post_name
	*	'force_skip_id',			will not import items with those XML IDs
	*	'force_skip_post_name',		will not import items with those XML post_name
	*	'force_duplicate_id',		will duplicate, if they already exist, items with those XML IDs
	*	'force_duplicate_post_name'	will duplicate, if they already exist, items with those XML post_name
	*
	* @return (mixed) true on success, WP_Error otherwise
	*
	* @since unknown
	*/

	function import_data( $args = array() ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return new WP_Error( 'wrong_capability', __( 'Your user can not perform that action.', 'wpv-views' ) );
		}
		global $WP_Views;
		$file = false;
		if ( isset( $_FILES['import-file'] ) ) {
			// If import is happening from the import form, there should be a $_FILES['import-file'] entry
			$file = $_FILES['import-file'];
		} else {
			$candidate_file = false;
			if ( isset( $_POST['import-file'] ) ) {
				// Check for import file from settings.xml in theme
				$candidate_file = $_POST['import-file'];
			} else if ( isset( $args['import-file'] ) ) {
				// Check for import file from $args
				$candidate_file = $args['import-file'];
			}
			if ( 
				$candidate_file
				&& file_exists( $candidate_file )
			) {
				$file = array();
				$file['name'] = $candidate_file;
				$file['tmp_name'] = $candidate_file;
				$file['size'] = filesize( $candidate_file );
			}
		}

		if ( 
			! $file 
			|| ! isset( $file['name'] )
			|| empty( $file['name'] )
		) {
			return new WP_Error(' could_not_open_file', __( 'Could not read the Views import file.', 'wpv-views' ) );
		}

		$data = array();
		$info = pathinfo( $file['name'] );
		$is_zip = $info['extension'] == 'zip' ? true : false;
		if ( $is_zip ) {
			$zip = zip_open( urldecode( $file['tmp_name'] ) );
			if ( is_resource( $zip ) ) {
				while ( ( $zip_entry = zip_read( $zip ) ) !== false ) {
					if ( zip_entry_name( $zip_entry ) == 'settings.xml' ) {
						$data = @zip_entry_read( $zip_entry, zip_entry_filesize( $zip_entry ) );
					}
				}
			} else {
				return new WP_Error( 'could_not_open_file', __( 'Unable to open zip file', 'wpv-views' ) );
			}
		} else {
			$fh = fopen( $file['tmp_name'], 'r' );
			if ( $fh ) {
				$data = fread( $fh, $file['size'] );
				fclose( $fh );
			}
		}

		if ( ! empty( $data ) ) {
			if ( ! function_exists( 'simplexml_load_string' ) ) {
				return new WP_Error( 'xml_missing', __( 'The Simple XML library is missing.', 'wpv-views' ) );
			}
			$xml = simplexml_load_string( $data );
			if ( ! $xml ) {
				return new WP_Error( 'not_xml_file', sprintf( __( 'The XML file (%s) could not be read.', 'wpv-views' ), $file['name'] ) );
			}
			$import_data = wpv_admin_import_export_simplexml2array( $xml );
			
			// Import Content Templates
			$result_content_templates = $this->import_content_templates( $import_data, $args );
			if ( is_wp_error( $result_content_templates ) ) {
				return $result_content_templates;
			}
			// Import Views
			$result_views = $this->import_views( $import_data, $args );
			if ( is_wp_error( $result_views ) ) {
				return $result_views;
			}
			// Import Settings
			$result_view_settings = $this->import_settings( $import_data, $args );
			if ( is_wp_error( $result_view_settings ) ) {
				return $result_view_settings;
			}
			
			return true;
			
		} else {
			return new WP_Error( 'could_not_open_file', __( 'Could not read the Views import file.', 'wpv-views' ) );
		}
		
		return true;
	}
	
	/**
	* import_content_templates
	*
	* Main import method for Views - import Content Templates
	*
	* @param $import_data (array) Array of items to import
	* @param $args (array) Import modifiers
	* 	'view-templates-overwrite'	will force overwriting existing CT
	* 	'view-templates-delete' 	will delete any existing CT that is not on the import file
	*	'force_import_id,			will only import items with those XML IDs
	*	'force_import_post_name',	will only import items with those XML post_name
	*	'force_skip_id',			will not import items with those XML IDs
	*	'force_skip_post_name',		will not import items with those XML post_name
	*	'force_duplicate_id',		will duplicate, if they already exist, items with those XML IDs
	*	'force_duplicate_post_name'	will duplicate, if they already exist, items with those XML post_name
	* 	'return_to'					will handle the special case of Module Manager imports
	*
	* @return (mixed) true on success, WP_Error otherwise | array with result data for Module Manager import
	*
	* @since 1.8.0
	*/
	
	function import_content_templates( $import_data, $args = array() ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return new WP_Error( 'wrong_capability', __( 'Your user can not perform that action.', 'wpv-views' ) );
		}
		global $wpdb, $_wp_additional_image_sizes;
		$return_to = 'views';
		$force_overwrite = false;
		$force_delete = false;
		$force_import_id = array();
		$force_import_post_name = array();
		$force_skip_id = array();
		$force_skip_post_name = array();
		$force_duplicate_id = array();
		$force_duplicate_post_name = array();
		if ( isset( $args['return_to'] ) ) {
			$return_to = $args['return_to'] ;
		}
		if ( isset( $args['view-templates-overwrite'] ) ) {
			$force_overwrite = true;
		}
		if ( isset( $args['view-templates-delete'] ) ) {
			$force_delete = true;
		}
		if ( 
			isset( $args['force_import_id'] ) 
			&& is_array( $args['force_import_id'] )
		) {
			$force_import_id = $args['force_import_id'];
		}
		if ( 
			isset( $args['force_import_post_name'] ) 
			&& is_array( $args['force_import_post_name'] )
		) {
			$force_import_post_name = $args['force_import_post_name'];
		}
		if ( 
			isset( $args['force_skip_id'] ) 
			&& is_array( $args['force_skip_id'] )
		) {
			$force_skip_id = $args['force_skip_id'];
		}
		if ( 
			isset( $args['force_skip_post_name'] ) 
			&& is_array( $args['force_skip_post_name'] )
		) {
			$force_skip_post_name = $args['force_skip_post_name'];
		}
		if ( 
			isset( $args['force_duplicate_id'] ) 
			&& is_array( $args['force_duplicate_id'] )
		) {
			$force_duplicate_id = $args['force_duplicate_id'];
		}
		if ( 
			isset( $args['force_duplicate_post_name'] ) 
			&& is_array( $args['force_duplicate_post_name'] )
		) {
			$force_duplicate_post_name = $args['force_duplicate_post_name'];
		}

		$imported_view_templates = array();
		$overwrite_count = 0;
		$new_count = 0;
		$results = array(
			'updated' => 0,
			'new' => 0,
			'failed' => 0,
			'errors' => array()
		);
		$newitems = array();

		if (
			! isset( $_wp_additional_image_sizes ) 
			|| ! is_array( $_wp_additional_image_sizes ) 
		) {
			$_wp_additional_image_sizes = array();
		}
		$attached_images_sizes = array_merge(
			// additional thumbnail sizes
			array_keys( $_wp_additional_image_sizes ),
			// wp default thumbnail sizes
			array( 'thumbnail', 'medium', 'large' )
		);

		if ( isset( $import_data['view-templates']['view-template'] ) ) {
			$view_templates = $import_data['view-templates']['view-template'];

			// check for a single Content Template
			if ( ! isset( $view_templates[0] ) ) {
				$view_templates = array( $view_templates );
			}

			foreach ( $view_templates as $view_template ) {

				if (
					in_array( $view_template['ID'], $force_skip_id )
					|| in_array( $view_template['post_name'], $force_skip_post_name )
				) {
					continue;
				}
				
				$output_mode = '';
				if ( isset( $view_template['template_mode'] ) ) {
					$output_mode = $view_template['template_mode'];
					unset( $view_template['template_mode'] );
				}
				$template_extra_css = '';
				if ( isset( $view_template['template_extra_css'] ) ) {
					$template_extra_css = $view_template['template_extra_css'];
					unset( $view_template['template_extra_css'] );
				}
				$template_extra_js = '';
				if ( isset( $view_template['template_extra_js'] ) ) {
					$template_extra_js = $view_template['template_extra_js'];
					unset( $view_template['template_extra_js'] );
				}
				$template_description = '';
				if ( isset( $view_template['template_description'] ) ) {
					$template_description = $view_template['template_description'];
					unset( $view_template['template_description'] );
				}
				$template_images = array();
				if ( isset( $view_template['attachments'] ) ) {
					$template_images = array($view_template['attachments']);
					unset( $view_template['attachments'] );
				}

				$post_to_update = $wpdb->get_var( 
					$wpdb->prepare( 
						"SELECT ID FROM {$wpdb->posts} 
						WHERE post_name = %s 
						AND post_type = %s 
						LIMIT 1", 
						$view_template['post_name'],
						'view-template'
					)
				);

				$idflag = 0;
				$id_to_import = $view_template['ID'];

				if ( $post_to_update ) {
					$imported_view_templates[] = $post_to_update;
					if (
						in_array( $view_template['ID'], $force_duplicate_id )
						|| in_array( $view_template['post_name'], $force_duplicate_post_name )
					) {
						// Duplicate post
						$duplicated_template = $view_template;
						unset( $duplicated_template['ID'] );
						unset( $duplicated_template['post_name'] );
						$counter = 0;
						$real_suffix = current_time( 'timestamp' );
						while ( $counter < 20 ) {
							$add = ' ' . $counter;
							if ( $counter == 0 ) {
								$add = '';
							}
							$template_title = $duplicated_template['post_title'] . $real_suffix . $add;
							$existing = $wpdb->get_var(
								$wpdb->prepare(
									"SELECT count(ID) FROM {$wpdb->posts} 
									WHERE ( post_title = %s OR post_name = %s ) 
									AND post_type = 'view-template' 
									LIMIT 1",
									$template_title,
									$template_title
								)
							);
							if ( $existing <= 0 ) {
								break;
							} else {
								$counter++;
							}
						}
						$duplicated_template['post_title'] = $template_title;
						$id = wp_insert_post( $duplicated_template, true );
						if ( is_object( $id ) ) {
							// it's an WP_Error object.
							if ( $return_to == 'module_manager' ) { // if using Module Manager
								$results['failed'] += 1;
								$results['errors'][] = sprintf( __( 'Failed to duplicate Content Template - %s.', 'wpv-views' ), $view_template['post_name'] );
							} else { // normal import
								return new WP_Error( 'could_not_duplicate_post', sprintf( __( 'Failed to duplicate Content Template - %s.', 'wpv-views' ), $view_template['post_name'] ) );
							}
						} else {
							$imported_view_templates[] = $id;
							$new_count++;
							$idflag = $id;
						}
					} else if ( 
						$force_overwrite
						|| in_array( $view_template['ID'], $force_import_id )
						|| in_array( $view_template['post_name'], $force_import_post_name )
					) {
						$view_template['ID'] = $post_to_update;
						$id = wp_update_post( $view_template );
						if ( ! $id ) {
							if ( $return_to == 'module_manager' ) { // if using Module Manager
								$results['failed'] += 1;
								$results['errors'][] = sprintf( __( 'Failed to update view-template - %s.', 'wpv-views' ), $view_template['post_name'] );
							} else { // normal import
								return new WP_Error( 'could_not_update_post', sprintf( __( 'Failed to update Content Template - %s.', 'wpv-views' ), $view_template['post_name'] ) );
							}
						} else {
							$overwrite_count++;
							$idflag = $id;
						}
					}
				} else {
					if (
						! empty( $force_import_id )
						&& ! in_array( $view_template['ID'], $force_import_id )
					) {
						continue;
					} else if (
						! empty( $force_import_post_name )
						&& ! in_array( $view_template['post_name'], $force_import_post_name )
					) {
						continue;
					}
					// it's a new Content template: create it
					unset( $view_template['ID'] );
					$id = wp_insert_post( $view_template, true );
					if ( is_object( $id ) ) {
						// it's an WP_Error object.
						if ( $return_to == 'module_manager' ) { // if using Module Manager
							$results['failed'] += 1;
							$results['errors'][] = sprintf( __( 'Failed to create Content Template - %s.', 'wpv-views' ), $view_template['post_name'] );
						} else { // normal import
							return new WP_Error( 'could_not_create_post', sprintf( __( 'Failed to create Content Template - %s.', 'wpv-views' ), $view_template['post_name'] ) );
						}
					} else {
						$imported_view_templates[] = $id;
						$new_count++;
						$idflag = $id;
					}
				}

				// $idflag is the overwritten or newly created item ID, false otherwise
				if ( $idflag ) {
					if ( $output_mode != '' ) {
						update_post_meta( $idflag, '_wpv_view_template_mode', $output_mode );
					}
					if ( $template_extra_css != '' ) {
						update_post_meta( $idflag, '_wpv_view_template_extra_css', $template_extra_css );
					}
					if ( $template_extra_js != '' ) {
						update_post_meta( $idflag, '_wpv_view_template_extra_js', $template_extra_js );
					}
					if ( $template_description != '' ) {
						update_post_meta( $idflag, '_wpv-content-template-decription', $template_description );
					}
					// Register wpml-string shortcodes for translation
					if ( isset( $view_template['post_content'] ) ) {
						wpv_register_wpml_strings( $view_template['post_content'] );
					}
					$newitems[_VIEW_TEMPLATES_MODULE_MANAGER_KEY_ . $id_to_import] = _VIEW_TEMPLATES_MODULE_MANAGER_KEY_ . $idflag;
					
					// Remove the _toolset_edit_last postmeta flag if it exists
					do_action( 'wpv_action_wpv_import_item', $idflag );
				}

				// Juan - add images importing
				// https://icanlocalize.basecamphq.com/projects/7393061-wp-views/todo_items/150919286/comments

				if ( 
					$idflag 
					&& ! empty( $template_images ) 
				) {
					$upload_dir = wp_upload_dir();
					$upload_path = $upload_dir['basedir'];
					$upload_directory = $upload_dir['baseurl'];
					$path_flag = true;
					if ( ! is_dir( $upload_path . DIRECTORY_SEPARATOR . 'views-import-temp' ) ) {
						mkdir( $upload_path . DIRECTORY_SEPARATOR . 'views-import-temp' );
					} else {
						$path_flag = false;  // if folder already existed
					}
					include_once( ABSPATH . 'wp-admin/includes/file.php' );
					include_once( ABSPATH . 'wp-admin/includes/media.php' );
					include_once( ABSPATH . 'wp-admin/includes/image.php');
					foreach ( $template_images as $attach_array ) {
						$attach_array = array_reverse( $attach_array ); // reverse the array so images are added in the same order they had on the exported site; needed for Module Manager
						foreach ( $attach_array as $attach ) {
							if ( 
								isset( $attach['data'] ) 
								&& isset( $attach['filename'] ) 
							) {
								//  decode attachment data and create the file
								$imgdata = base64_decode( $attach['data'] );
								file_put_contents( $upload_path . DIRECTORY_SEPARATOR . 'views-import-temp' . DIRECTORY_SEPARATOR . $attach['filename'], $imgdata );
								// upload the file using WordPress API and add it to the post as attachment
								// preserving all fields but alt
								$tmp = download_url( $upload_directory . '/views-import-temp/' . $attach['filename'] );
								$file_array['name'] = $attach['filename'];
								$file_array['tmp_name'] = $tmp;
								if ( is_wp_error( $tmp ) ) {
									@unlink( $file_array['tmp_name'] );
									$file_array['tmp_name'] = '';
								}
								$att_data = array();
								if ( isset( $attach['title'] ) ) {
									$att_data['post_title'] = $attach['title'];
								}
								if ( isset( $attach['content'] ) ) {
									$att_data['post_content'] = $attach['content'];
								}
								if ( isset( $attach['excerpt'] ) ) {
									$att_data['post_excerpt'] = $attach['excerpt'];
								}
								if ( isset( $attach['status'] ) ) {
									$att_data['post_status'] = $attach['status'];
								}
								$att_id = media_handle_sideload( $file_array, $idflag, null, $att_data );
								if ( is_wp_error( $att_id ) ) {
									@unlink( $file_array['tmp_name'] );
									return new WP_Error( 'could_not_import_attachment', sprintf( __( 'Failed to import Content Template attachment - %s, %s.', 'wpv-views' ), $view_template['post_name'], $file_array['name'] ) );
								}
								// update alt field
								if ( isset( $attach['alt'] ) ) {
									update_post_meta( $att_id, '_wp_attachment_image_alt', $attach['alt'] );
								}
								@unlink( $upload_path . DIRECTORY_SEPARATOR . 'views-import-temp' . DIRECTORY_SEPARATOR . $attach['filename'] );
								$att_attributes = wp_get_attachment_image_src( $att_id, 'full' );
								foreach ( $attached_images_sizes as $ts ) {
									$imthumbs[$ts] = wp_get_attachment_image_src( $att_id, $ts );
								}
								if ( isset( $attach['on_meta_html_css'] ) ) {
									$template_extra_css = str_replace( $attach['on_meta_html_css'], $att_attributes[0], $template_extra_css );
									update_post_meta( $idflag, '_wpv_view_template_extra_css', $template_extra_css );
								}
								if ( 
									isset( $attach['on_meta_html_css_sizes'] ) 
									&& is_array( $attach['on_meta_html_css_sizes'] ) 
								) {
									foreach ( $attach['on_meta_html_css_sizes'] as $atsize => $aturl ) {
										if ( in_array( $atsize, $attached_images_sizes ) ) {
											$template_extra_css = str_replace( $aturl, $imthumbs[$atsize][0], $template_extra_css );
										} else {
											$template_extra_css = str_replace( $aturl, $imthumbs['thumbnail'][0], $template_extra_css );
										}
									}
									update_post_meta( $idflag, '_wpv_view_template_extra_css', $template_extra_css );
								}
								if ( isset( $attach['on_meta_html_js'] ) ) {
									$template_extra_js = str_replace( $attach['on_meta_html_js'], $att_attributes[0], $template_extra_js );
									update_post_meta( $idflag, '_wpv_view_template_extra_js', $template_extra_js );
								}
								if ( 
									isset( $attach['on_meta_html_js_sizes'] ) 
									&& is_array( $attach['on_meta_html_js_sizes'] ) 
								) {
									foreach ( $attach['on_meta_html_js_sizes'] as $atsize => $aturl ) {
										if ( in_array( $atsize, $attached_images_sizes ) ) {
											$template_extra_js = str_replace( $aturl, $imthumbs[$atsize][0], $template_extra_js );
										} else {
											$template_extra_js = str_replace( $aturl, $imthumbs['thumbnail'][0], $template_extra_js );
										}
									}
									update_post_meta( $idflag, '_wpv_view_template_extra_js', $template_extra_js );
								}
								if ( isset( $attach['on_post_content'] ) ) {
									$up['ID'] = $idflag;
									$up['post_content'] = get_post_field('post_content', $idflag);
									$up['post_content'] = str_replace( $attach['on_post_content'], $att_attributes[0], $up['post_content'] );
									wp_update_post( $up );
								}
								if ( 
									isset( $attach['on_post_content_sizes'] ) 
									&& is_array( $attach['on_post_content_sizes'] ) 
								) {
									$up['ID'] = $idflag;
									$up['post_content'] = get_post_field('post_content', $idflag);
									foreach ( $attach['on_post_content_sizes'] as $atsize => $aturl ) {
										if ( in_array( $atsize, $attached_images_sizes ) ) {
											$up['post_content'] = str_replace( $aturl, $imthumbs[$atsize][0], $up['post_content'] );
										} else {
											$up['post_content'] = str_replace( $aturl, $imthumbs['thumbnail'][0], $up['post_content'] );
										}
									}
									wp_update_post( $up );
								}
							}
						}
					}
					if ( $path_flag ) {
						rmdir( $upload_path . DIRECTORY_SEPARATOR . 'views-import-temp' );
					}
				}
			}
		}
		$deleted_count = 0;
		if ( $force_delete ) {
			if ( ! is_array( $imported_view_templates ) ) {
				$imported_view_templates = array();
			}
			$imported_view_templates = array_map( 'esc_attr', $imported_view_templates );
			$imported_view_templates = array_map( 'trim', $imported_view_templates );
			// is_numeric + intval does sanitization
			$imported_view_templates = array_filter( $imported_view_templates, 'is_numeric' );
			$imported_view_templates = array_map( 'intval', $imported_view_templates );
			$templates_delete_exclude_for_query = implode( ',', $imported_view_templates );
			if ( ! empty( $templates_delete_exclude_for_query ) ) {
				$templates_delete_exclude_for_query = " AND ID NOT IN ( " . $templates_delete_exclude_for_query . " )";
			}
			$templates_to_delete = $wpdb->get_col( 
				$wpdb->prepare(
					"SELECT ID FROM {$wpdb->posts} 
					WHERE post_type = %s 
					{$templates_delete_exclude_for_query}",
					'view-template'
				)
			);
			if ( ! empty( $templates_to_delete ) ) {
				foreach ( $templates_to_delete as $templates_to_delete_id ) {
					wp_delete_post( $templates_to_delete_id, true );
					$deleted_count++;
				}
			}
		}

		$this->import_messages[] = sprintf( __( '%d Content Templates found in the file. %d have been created and %d have been over written.', 'wpv-views' ), sizeof( $imported_view_templates ), $new_count, $overwrite_count );
		if ( $deleted_count ) {
			$this->import_messages[] = sprintf( __( '%d existing Content Templates were deleted.', 'wpv-views' ), $deleted_count );
		}
		$results['updated'] = $overwrite_count;
		$results['new'] = $new_count;
		if ( $return_to == 'module_manager' ) {
			$results['items'] = $newitems;
			return $results;
		} else {
			return true;
		}
	}
	
	/**
	* import_views
	*
	* Main import method for Views - import Views and WordPress Archives
	*
	* @param $import_data (array) Array of items to import
	* @param $args (array) Import modifiers
	*	'views-overwrite' 			will force overwriting existing Views and WPA
	* 	'views-delete' 				will delete any existing Views and WPA that are not on the import file
	*	'force_import_id,			will only import items with those XML IDs
	*	'force_import_post_name',	will only import items with those XML post_name
	*	'force_skip_id',			will not import items with those XML IDs
	*	'force_skip_post_name',		will not import items with those XML post_name
	*	'force_duplicate_id',		will duplicate, if they already exist, items with those XML IDs
	*	'force_duplicate_post_name'	will duplicate, if they already exist, items with those XML post_name
	* 	'return_to'					will handle the special case of Module Manager imports
	*
	* @return (mixed) true on success, WP_Error otherwise | array with result data for Module Manager import
	*
	* @since 1.8.0
	*/
	
	function import_views( $import_data, $args = array() ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return new WP_Error( 'wrong_capability', __( 'Your user can not perform that action.', 'wpv-views' ) );
		}
		global $wpdb, $WP_Views, $_wp_additional_image_sizes;
		$return_to = 'views';
		$force_overwrite = false;
		$force_delete = false;
		$force_import_id = array();
		$force_import_post_name = array();
		$force_skip_id = array();
		$force_skip_post_name = array();
		$force_duplicate_id = array();
		$force_duplicate_post_name = array();
		if ( isset( $args['return_to'] ) ) {
			$return_to = $args['return_to'] ;
		}
		if ( isset( $args['views-overwrite'] ) ) {
			$force_overwrite = true;
		}
		if ( isset( $args['views-delete'] ) ) {
			$force_delete = true;
		}
		if ( 
			isset( $args['force_import_id'] ) 
			&& is_array( $args['force_import_id'] )
		) {
			$force_import_id = $args['force_import_id'];
		}
		if ( 
			isset( $args['force_import_post_name'] ) 
			&& is_array( $args['force_import_post_name'] )
		) {
			$force_import_post_name = $args['force_import_post_name'];
		}
		if ( 
			isset( $args['force_skip_id'] ) 
			&& is_array( $args['force_skip_id'] )
		) {
			$force_skip_id = $args['force_skip_id'];
		}
		if ( 
			isset( $args['force_skip_post_name'] ) 
			&& is_array( $args['force_skip_post_name'] )
		) {
			$force_skip_post_name = $args['force_skip_post_name'];
		}
		if ( 
			isset( $args['force_duplicate_id'] ) 
			&& is_array( $args['force_duplicate_id'] )
		) {
			$force_duplicate_id = $args['force_duplicate_id'];
		}
		if ( 
			isset( $args['force_duplicate_post_name'] ) 
			&& is_array( $args['force_duplicate_post_name'] )
		) {
			$force_duplicate_post_name = $args['force_duplicate_post_name'];
		}

		$imported_views = array();
		$overwrite_count = 0;
		$new_count = 0;
		$results = array(
			'updated' => 0,
			'new' => 0,
			'failed' => 0,
			'errors' => array()
		);
		$newitems = array();

		if ( 
			! isset( $_wp_additional_image_sizes ) 
			|| !is_array( $_wp_additional_image_sizes ) 
		) {
			$_wp_additional_image_sizes = array();
		}
		$attached_images_sizes = array_merge(
			// additional thumbnail sizes
			array_keys( $_wp_additional_image_sizes ),
			// wp default thumbnail sizes
			array( 'thumbnail', 'medium', 'large' )
		);
		
		/**
		* wpv_filter_view_extra_fields_for_import_export
		*
		* Extra postdata from the View that needs to be imported, beyond the settings, layout settings and description
		*
		* @param (array) 
		*
		* @since 1.7
		*/
		
		$extra_metas = apply_filters( 'wpv_filter_view_extra_fields_for_import_export', array() );
	
		$this_settings_metaboxes = array(
			'filter_meta_html',
			'filter_meta_html_css',
			'filter_meta_html_js',
			'layout_meta_html_css',
			'layout_meta_html_js'
		);
		$this_layout_settings_metaboxes = array(
			'layout_meta_html'
		);

		if ( isset( $import_data['views']['view'] ) ) {
			$views = $import_data['views']['view'];
			// check for a single view
			if ( ! isset( $views[0] ) ) {
				$views = array( $views );
			}
			foreach ( $views as $view ) {
				
				if (
					in_array( $view['ID'], $force_skip_id )
					|| in_array( $view['post_name'], $force_skip_post_name )
				) {
					continue;
				}
				
				$meta = $view['meta'];
				unset( $view['meta'] );
				$view_images = array();
				if ( isset( $view['attachments'] ) ) {
					$view_images = array( $view['attachments'] );
					unset( $view['attachments'] );
				}
				// SRDJAN - https://icanlocalize.basecamphq.com/projects/7393061-wp-views/todo_items/142389966/comments
				// Fix URLs
				if (
					! empty( $import_data['site_url'] ) 
					&& ! empty( $import_data['fileupload_url'] ) 
				) {
					if ( 
						isset( $meta['_wpv_settings']['pagination']['spinner_image'] ) 
						&& ! empty( $meta['_wpv_settings']['pagination']['spinner_image'] ) 
					) {
						$meta['_wpv_settings']['pagination']['spinner_image'] = WPV_URL_EMBEDDED . '/res/img/' . basename($meta['_wpv_settings']['pagination']['spinner_image']);
					}
					if (
						isset( $meta['_wpv_settings']['dps']['spinner_image'] ) 
						&& ! empty( $meta['_wpv_settings']['dps']['spinner_image'] ) 
					) {
						$meta['_wpv_settings']['dps']['spinner_image'] = WPV_URL_EMBEDDED . '/res/img/' . basename($meta['_wpv_settings']['dps']['spinner_image']);
					}
					if ( 
						isset( $meta['_wpv_settings']['pagination']['spinner_image_uploaded'] ) 
						&& ! empty( $meta['_wpv_settings']['pagination']['spinner_image_uploaded'] ) 
					) {
						$old_custom_spinner = $meta['_wpv_settings']['pagination']['spinner_image_uploaded']; // keep it for comparing in the new images importing flow
						$meta['_wpv_settings']['pagination']['spinner_image_uploaded'] = wpv_convert_url(
							$meta['_wpv_settings']['pagination']['spinner_image_uploaded'],
							$import_data['site_url'],
							$import_data['fileupload_url']
						);
					}
					if ( 
						isset( $meta['_wpv_settings']['dps']['spinner_image_uploaded'] ) 
						&& ! empty( $meta['_wpv_settings']['dps']['spinner_image_uploaded'] ) 
					) {
						$old_dps_custom_spinner = $meta['_wpv_settings']['dps']['spinner_image_uploaded']; // keep it for comparing in the new images importing flow
						$meta['_wpv_settings']['dps']['spinner_image_uploaded'] = wpv_convert_url(
							$meta['_wpv_settings']['dps']['spinner_image_uploaded'],
							$import_data['site_url'],
							$import_data['fileupload_url']
						);
					}
				}
				// TODO Move all of this to a proper adjustment method
				// SRDJAN - fix term_ids
				// https://icanlocalize.basecamphq.com/projects/7393061-wp-views/todo_items/142382866/comments
				if ( 
					! empty( $meta['_wpv_settings']['taxonomy_terms']['taxonomy_term'] ) 
					&& is_array( $meta['_wpv_settings']['taxonomy_terms']['taxonomy_term'] ) 
				) {
					foreach ( $meta['_wpv_settings']['taxonomy_terms']['taxonomy_term'] as $term_key => $old_term_id ) {
						if ( isset( $import_data['terms_map']['term_' . $old_term_id] ) ) {
							$new_term = get_term_by( 'slug', $import_data['terms_map']['term_' . $old_term_id]['slug'], $import_data['terms_map']['term_' . $old_term_id]['taxonomy'] );
							if ( !empty( $new_term ) ) {
								$meta['_wpv_settings']['taxonomy_terms']['taxonomy_term'][$term_key] = $new_term->term_id;
							}
						}
					}
				}
				
				$post_to_update = $wpdb->get_var( 
					$wpdb->prepare( 
						"SELECT ID FROM {$wpdb->posts} 
						WHERE post_name = %s 
						AND post_type = %s 
						LIMIT 1", 
						$view['post_name'], 
						'view' 
					) 
				);

				$idflag = 0;
				$id_to_import = $view['ID'];
				
				if ( $post_to_update ) {
					$imported_views[] = $post_to_update;
					if (
						in_array( $view['ID'], $force_duplicate_id )
						|| in_array( $view['post_name'], $force_duplicate_post_name )
					) {
						$duplicated_view = $view;
						unset( $duplicated_view['ID'] );
						unset( $duplicated_view['post_name'] );
						$counter = 0;
						$real_suffix = current_time( 'timestamp' );
						while ( $counter < 20 ) {
							$add = ' ' . $counter;
							if ( $counter == 0 ) {
								$add = '';
							}
							$view_title = $duplicated_view['post_title'] . $real_suffix . $add;
							$existing = $wpdb->get_var(
								$wpdb->prepare(
									"SELECT count(ID) FROM {$wpdb->posts} 
									WHERE ( post_title = %s OR post_name = %s ) 
									AND post_type = 'view' 
									LIMIT 1",
									$view_title,
									$view_title
								)
							);
							if ( $existing <= 0 ) {
								break;
							} else {
								$counter++;
							}
						}
						$duplicated_view['post_title'] = $view_title;
						$id = wp_insert_post( $duplicated_view, true );
						if ( is_object( $id ) ) {
							// it's an WP_Error object.
							if ( $return_to == 'module_manager' ) { // if using Module Manager
								$results['failed'] += 1;
								$results['errors'][] = sprintf( __( 'Failed to duplicate view - %s.', 'wpv-views' ), $view['post_name'] );
							} else { // normal import
								return new WP_Error( 'could_not_duplicate_post', sprintf( __( 'Failed to duplicate view - %s.', 'wpv-views' ), $view['post_name'] ) );
							}
						} else {
							$idflag = $id;
							$new_count++;
							$imported_views[] = $id;
						}
					} else if ( 
						$force_overwrite
						|| in_array( $view['ID'], $force_import_id ) 
						|| in_array( $view['post_name'], $force_import_post_name )
					) {
						$view['ID'] = $post_to_update;
						$id = wp_update_post( $view );
						if ( ! $id ) {
							if ( $return_to == 'module_manager' ) { // if using Module Manager
								$results['failed'] += 1;
								$results['errors'][] = sprintf( __( 'Failed to update view - %s.', 'wpv-views' ), $view['post_name'] );
							} else { // normal import
								return new WP_Error( 'could_not_update_post', sprintf( __( 'Failed to update view - %s.', 'wpv-views' ), $view['post_name'] ) );
							}
						} else {
							$idflag = $id;
							$overwrite_count++;
						}
					}
				} else {
					if (
						! empty( $force_import_id )
						&& ! in_array( $view['ID'] , $force_import_id )
					) {
						continue;
					} else if (
						! empty( $force_import_post_name )
						&& ! in_array( $view['post_name'] , $force_import_post_name )
					) {
						continue;
					}
					// it's a new view: create it
					unset( $view['ID'] );
					$id = wp_insert_post( $view, true );
					if ( is_object( $id ) ) {
						// it's an WP_Error object.
						if ( $return_to == 'module_manager' ) { // if using Module Manager
							$results['failed'] += 1;
							$results['errors'][] = sprintf( __( 'Failed to create view - %s.', 'wpv-views' ), $view['post_name'] );
						} else { // normal import
							return new WP_Error( 'could_not_create_post', sprintf( __( 'Failed to create view - %s.', 'wpv-views' ), $view['post_name'] ) );
						}
					} else {
						$idflag = $id;
						$new_count++;
						$imported_views[] = $id;
					}
				}
				// Register wpml-string shortcodes for translation
				if ( $idflag ) {
					// The View was updated (if overwrite) or created, based on settings
					// Now, update postmeta
					if ( isset( $meta['_wpv_settings'] ) ) {
						
						/**
						* wpv_filter_adjust_view_settings_for_import
						*
						* Filter to adjust Views settings on import
						*
						* Some View settings are stored as indexed arrays, producing errors on index 0
						* We transformed those indexed arrays into associative arrays before export, and we restore them on import
						* Also, some settings contain IDs pointing to other Views or Content Templates
						* We transformed them into names, and we restore them on import
						*
						* @param (array) $meta['_wpv_settings'] The View settings being imported
						* @param (array) $view The View post data as an array - no meta, no attachments
						* @param (int) $idflag The resulting View ID
						*
						* @since 1.7
						*/
						
						$meta['_wpv_settings'] = apply_filters( 'wpv_filter_adjust_view_settings_for_import', $meta['_wpv_settings'], $view, $idflag );
						// Check whether this View has a filter by post Ids that might need some manual changes, and warn about it
						if ( isset( $meta['_wpv_settings']['post_id_ids_list_lost'] ) ) {
							if (
								isset( $meta['_wpv_settings']['id_mode'][0] )
								&& $meta['_wpv_settings']['id_mode'][0] == 'by_ids'
							) {
								$not_found_names[$view['post_title']] = $meta['_wpv_settings']['post_id_ids_list_lost'];
							}
							unset( $meta['_wpv_settings']['post_id_ids_list_lost'] );
						}
						if ( 
							isset( $meta['_wpv_settings']['id_mode'] ) 
							&& isset( $meta['_wpv_settings']['id_mode'][0] ) 
							&& 'shortcode' == $meta['_wpv_settings']['id_mode'][0] 
						) {
							$views_with_id_shortcodes[$view['post_title']] = $view['post_title'];
						}
						update_post_meta( $idflag, '_wpv_settings', $meta['_wpv_settings'] );
					}
					if ( isset( $meta['_wpv_layout_settings'] ) ) {
						
						/**
						* wpv_filter_adjust_view_layout_settings_for_import
						*
						* Filter to adjust Views layout settings on import
						*
						* @param (array) $meta['_wpv_settings'] The View settings being imported
						* @param (array) $view The View post data as an array - no meta, no attachments
						* @param (int) $idflag The resulting View ID
						*
						* @since 1.7
						*/
						
						$meta['_wpv_layout_settings'] = apply_filters( 'wpv_filter_adjust_view_layout_settings_for_import', $meta['_wpv_layout_settings'], $view, $idflag );
						update_post_meta( $idflag, '_wpv_layout_settings', $meta['_wpv_layout_settings'] );
					}
					foreach ( $extra_metas as $extra_meta_key ) {
						if ( isset( $meta[$extra_meta_key] ) ) {
							
							/**
							* wpv_filter_adjust_view_extra_fields_for_import
							*
							* Filter to adjust Views registered postmeta that is to be imported
							*
							* @param (array) $meta['_wpv_settings'] The View settings being imported
							* @param (array) $view The View post data as an array - no meta, no attachments
							* @param (string) $extra_meta_key The postmeta key
							* @param (int) $idflag The resulting View ID
							*
							* @since 1.7
							*/
							
							$meta[$extra_meta_key] = apply_filters( 'wpv_filter_adjust_view_extra_fields_for_import', $meta[$extra_meta_key], $view, $extra_meta_key, $idflag );
							update_post_meta( $idflag, $extra_meta_key, $meta[$extra_meta_key] );
						}
					}
					// @todo review why or when this action is being used
					// @todo we might need the same kind of action for Content Templates import
					do_action( 'wpv_view_imported', $id_to_import, $idflag );
					// And now, translate strings if needed
					if ( isset( $view['post_content'] ) ) {
						wpv_register_wpml_strings( $view['post_content'] );
					}
					if ( isset( $meta['_wpv_settings'] ) && isset( $meta['_wpv_settings']['filter_meta_html'] ) ) {
						wpv_add_controls_labels_to_translation( $meta['_wpv_settings']['filter_meta_html'], $idflag );
						wpv_register_wpml_strings( $meta['_wpv_settings']['filter_meta_html'] );
					}
					if ( isset( $meta['_wpv_layout_settings'] ) && isset( $meta['_wpv_layout_settings']['layout_meta_html'] ) ) {
						wpv_register_wpml_strings( $meta['_wpv_layout_settings']['layout_meta_html'] );
					}

					$newitems[_VIEWS_MODULE_MANAGER_KEY_. $id_to_import] = _VIEWS_MODULE_MANAGER_KEY_.$idflag;
					
					// Remove the _toolset_edit_last postmeta flag if it exists
					do_action( 'wpv_action_wpv_import_item', $idflag );
				}
				// Juan - add images importing
				// https://icanlocalize.basecamphq.com/projects/7393061-wp-views/todo_items/150919286/comments

				if ( 
					$idflag 
					&& ! empty( $view_images ) 
				) {
					$upload_dir = wp_upload_dir();
					$upload_path = $upload_dir['basedir'];
					$upload_directory = $upload_dir['baseurl'];
					$path_flag = true;
					if ( ! is_dir( $upload_path . DIRECTORY_SEPARATOR . 'views-import-temp' ) ) {
						mkdir( $upload_path . DIRECTORY_SEPARATOR . 'views-import-temp' );
					} else {
						$path_flag = false;  // if folder already existed
					}
					include_once( ABSPATH . 'wp-admin/includes/file.php' );
					include_once( ABSPATH . 'wp-admin/includes/media.php' );
					include_once( ABSPATH . 'wp-admin/includes/image.php');
					foreach ( $view_images as $attach_array ) {
						$attach_array = array_reverse( $attach_array );
						foreach ( $attach_array as $attach ) {
							if ( 
								isset( $attach['data'] ) 
								&& isset( $attach['filename'] ) 
							) {
								//  decode attachment data and create the file
								$imgdata = base64_decode($attach['data']);
								file_put_contents( $upload_path . DIRECTORY_SEPARATOR . 'views-import-temp' . DIRECTORY_SEPARATOR . $attach['filename'], $imgdata );
								// upload the file using WordPress API and add it to the post as attachment
								// preserving all fields but alt
								$tmp = download_url( $upload_directory . '/views-import-temp/' . $attach['filename'] );
								$file_array['name'] = $attach['filename'];
								$file_array['tmp_name'] = $tmp;
								if ( is_wp_error( $tmp ) ) {
									@unlink( $file_array['tmp_name'] );
									$file_array['tmp_name'] = '';
								}
								$att_data = array();
								if ( isset( $attach['title'] ) ) {
									$att_data['post_title'] = $attach['title'];
								}
								if ( isset($attach['content'] ) ) {
									$att_data['post_content'] = $attach['content'];
								}
								if ( isset($attach['excerpt'] ) ) {
									$att_data['post_excerpt'] = $attach['excerpt'];
								}
								if ( isset($attach['status'] ) ) {
									$att_data['post_status'] = $attach['status'];
								}
								$att_id = media_handle_sideload( $file_array, $idflag, null, $att_data );
								if ( is_wp_error($att_id) ) {
									@unlink( $file_array['tmp_name'] );
									return new WP_Error( 'could_not_import_attachment', sprintf( __( 'Failed to import View attachment - %s, %s.', 'wpv-views' ), $view['post_name'], $file_array['name'] ) );
								}
								// update alt field
								if ( isset( $attach['alt'] ) ) {
									update_post_meta( $att_id, '_wp_attachment_image_alt', $attach['alt'] );
								}
								@unlink( $upload_path . DIRECTORY_SEPARATOR . 'views-import-temp' . DIRECTORY_SEPARATOR . $attach['filename'] );
								// set spinner image and attached images added to MetaHTML boxes
								$att_attributes = wp_get_attachment_image_src( $att_id, 'full');
								foreach ($attached_images_sizes as $ts) {
									$imthumbs[$ts] = wp_get_attachment_image_src( $att_id, $ts );
									if ( isset( $attach['custom_spinner'] ) && ( 'this_' . $ts == $attach['custom_spinner'] ) ) {
										$meta['_wpv_settings']['pagination']['spinner_image_uploaded'] = $imthumbs[$ts][0];
									}
									if ( isset( $attach['dps_custom_spinner'] ) && ( 'this_' . $ts == $attach['dps_custom_spinner'] ) ) {
										$meta['_wpv_settings']['dps']['spinner_image_uploaded'] = $imthumbs[$ts][0];
									}
								}
								if ( isset( $attach['custom_spinner'] ) && 'this' == $attach['custom_spinner'] ) {
									$meta['_wpv_settings']['pagination']['spinner_image_uploaded'] = $att_attributes[0];
								}
								if ( isset( $attach['dps_custom_spinner'] ) && 'this' == $attach['dps_custom_spinner'] ) {
									$meta['_wpv_settings']['dps']['spinner_image_uploaded'] = $att_attributes[0];
								}
								
								foreach ( $this_settings_metaboxes as $metabox_id ) {
									if ( isset( $attach['on_' . $metabox_id] ) ) {
										$meta['_wpv_settings'][$metabox_id] = str_replace( $attach['on_' . $metabox_id], $att_attributes[0], $meta['_wpv_settings'][$metabox_id] );
									}
									if ( 
										isset( $attach['on_' . $metabox_id . '_sizes'] ) 
										&& is_array( $attach['on_' . $metabox_id . '_sizes'] ) 
									) {
										foreach ( $attach['on_' . $metabox_id . '_sizes'] as $atsize => $aturl ) {
											if ( in_array( $atsize, $attached_images_sizes ) ) {
												$meta['_wpv_settings'][$metabox_id] = str_replace( $aturl, $imthumbs[$atsize][0], $meta['_wpv_settings'][$metabox_id] );
											} else {
												$meta['_wpv_settings'][$metabox_id] = str_replace( $aturl, $imthumbs['thumbnail'][0], $meta['_wpv_settings'][$metabox_id] );
											}
										}
									}
								}
								foreach ( $this_layout_settings_metaboxes as $metabox_id ) {
									if ( isset( $attach['on_' . $metabox_id] ) ) {
										$meta['_wpv_layout_settings'][$metabox_id] = str_replace( $attach['on_' . $metabox_id], $att_attributes[0], $meta['_wpv_layout_settings'][$metabox_id] );
									}
									if ( 
										isset( $attach['on_' . $metabox_id . '_sizes'] ) 
										&& is_array( $attach['on_' . $metabox_id . '_sizes'] ) 
									) {
										foreach ( $attach['on_' . $metabox_id . '_sizes'] as $atsize => $aturl ) {
											if ( in_array( $atsize, $attached_images_sizes ) ) {
												$meta['_wpv_layout_settings'][$metabox_id] = str_replace( $aturl, $imthumbs[$atsize][0], $meta['_wpv_layout_settings'][$metabox_id] );
											} else {
												$meta['_wpv_layout_settings'][$metabox_id] = str_replace( $aturl, $imthumbs['thumbnail'][0], $meta['_wpv_layout_settings'][$metabox_id] );
											}
										}
									}
								}
								if ( isset( $attach['on_post_content'] ) ) {
									$up['ID'] = $idflag;
									$up['post_content'] = get_post_field('post_content', $idflag);
									$up['post_content'] = str_replace( $attach['on_post_content'], $att_attributes[0], $up['post_content'] );
									wp_update_post( $up );
								}
								if ( 
									isset( $attach['on_post_content_sizes'] ) 
									&& is_array( $attach['on_post_content_sizes'] ) 
								) {
									$up['ID'] = $idflag;
									$up['post_content'] = get_post_field( 'post_content', $idflag );
									foreach ( $attach['on_post_content_sizes'] as $atsize => $aturl ) {
										if ( in_array( $atsize, $attached_images_sizes ) ) {
											$up['post_content'] = str_replace( $aturl, $imthumbs[$atsize][0], $up['post_content'] );
										} else {
											$up['post_content'] = str_replace( $aturl, $imthumbs['thumbnail'][0], $up['post_content'] );
										}
									}
									wp_update_post( $up );
								}
							}
						}
					}
					update_post_meta( $idflag, '_wpv_settings', $meta['_wpv_settings'] );
					update_post_meta( $idflag, '_wpv_layout_settings', $meta['_wpv_layout_settings'] );
					if ( $path_flag ) {
						rmdir($upload_path . DIRECTORY_SEPARATOR . 'views-import-temp');
					}
				}
			}
		}

		$deleted_count = 0;
		if ( $force_delete ) {
			if ( ! is_array( $imported_views ) ) {
				$imported_views = array();
			}
			$imported_views = array_map( 'esc_attr', $imported_views );
			$imported_views = array_map( 'trim', $imported_views );
			// is_numeric + intval does sanitization
			$imported_views = array_filter( $imported_views, 'is_numeric' );
			$imported_views = array_map( 'intval', $imported_views );
			$views_delete_exclude_for_query = implode( ',', $imported_views );
			if ( ! empty( $views_delete_exclude_for_query ) ) {
				$views_delete_exclude_for_query = " AND ID NOT IN ( " . $views_delete_exclude_for_query . " )";
			}
			$views_to_delete = $wpdb->get_col( 
				$wpdb->prepare(
					"SELECT ID FROM {$wpdb->posts} 
					WHERE post_type = %s 
					{$views_delete_exclude_for_query}",
					'view'
				)
			);
			if ( ! empty( $views_to_delete ) ) {
				foreach ( $views_to_delete as $views_to_delete_id ) {
					wp_delete_post( $views_to_delete_id, true );
					$deleted_count++;
				}
			}
		}

		$this->import_messages[] = sprintf( __( '%d Views found in the file. %d have been created and %d have been over written.', 'wpv-views' ), sizeof( $imported_views ), $new_count, $overwrite_count );

		if ( $deleted_count ) {
			$this->import_messages[] = sprintf( __( '%d existing Views were deleted.', 'wpv-views' ), $deleted_count );
		}

		if ( 
			isset( $not_found_names ) 
			&& ! empty( $not_found_names ) 
		) {
			$view_names = implode( ', ', array_keys( $not_found_names ) );
			$this->import_messages[] = __('Those Views have filters by IDs that were not correctly imported because they filter by posts that do not exist. Please review them: ', 'wpv-views') . '<strong>' . $view_names . '</strong>';
		}
		if ( 
			isset( $views_with_id_shortcodes ) 
			&& ! empty( $views_with_id_shortcodes ) 
		) {
			$view_names = implode( ', ', array_keys( $views_with_id_shortcodes ) );
			$this->import_messages[] = __( 'Those Views filter by post IDs using a shortcode attribute. You may need to modify the Views shortcodes if post IDs have changed during import: ', 'wpv-views' ) . '<strong>' . $view_names . '</strong>';
		}

		$results['updated'] = $overwrite_count;
		$results['new'] = $new_count;
		
		if ( $return_to == 'module_manager' ) {
			$results['items'] = $newitems;
			return $results;
		} else {
			return true; // no errors
		}
	}
	
	/**
	* import_settings
	*
	* Main import method for Views - import settings
	*
	* @param $data (array) Array of data to import
	* @param $args (array) Import modifiers
	*	'views-overwrite' 			will force overwriting existing Views and WPA
	* 	'view-templates-overwrite'	will force overwriting existing CT
	* 	'view-settings-overwrite' 	will overwrite existing settings
	*
	* @return (mixed) true on success, WP_Error otherwise
	*
	* @since 1.8.0
	*/

	function import_settings( $data, $args = array() ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return new WP_Error( 'wrong_capability', __( 'Your user can not perform that action.', 'wpv-views' ) );
		}
		global $WPV_settings, $wpdb;
		$force_settings_overwrite = false;
		$force_settings_overwrite_views = false;
		$force_settings_overwrite_view_templates = false;
		if ( isset( $args['view-settings-overwrite'] ) ) {
			$force_settings_overwrite = true;
		}
		if ( isset( $args['views-overwrite'] ) ) {
			$force_settings_overwrite_views = true;
		}
		if ( isset( $args['view-templates-overwrite'] ) ) {
			$force_settings_overwrite_view_templates = true;
		}
		
		if ( ! isset( $data['settings'] ) ) {
			$data['settings'] = array();
		}
		if ( $force_settings_overwrite ) {
			// Reset options
			foreach ( $WPV_settings as $option_name => $option_value ) {
				if ( 
					strpos( $option_name, 'view_' ) !== 0 
					&& strpos( $option_name, 'views_template_' ) !== 0 
				) {	
					if ( is_numeric( $option_value ) ) {
						$WPV_settings[$option_name] = 0;
					} else {
						$WPV_settings[$option_name] = '';
					}
				}
			}
			// Set exported options
			if ( ! empty( $data['settings'] ) ) {
				foreach ( $data['settings'] as $option_name => $option_value ) {
					if ( 
						strpos( $option_name, 'view_' ) === 0 
						|| strpos( $option_name, 'views_template_' ) === 0 
					) {
						$post_type = strpos( $option_name, 'view_' ) === 0 ? 'view' : 'view-template';
						if ( $option_value ) {
							$post_id = $wpdb->get_var( 
								$wpdb->prepare( 
									"SELECT ID FROM {$wpdb->posts} 
									WHERE post_name = %s 
									AND post_type = %s 
									LIMIT 1", 
									$option_value, 
									$post_type 
								) 
							);
						} else {
							$post_id = 0;
						}
						if ( $post_id ) {
							$WPV_settings[$option_name] = $post_id;
						} else {
							$WPV_settings[$option_name] = 0;
							if ( $option_value ) {
								$this->import_messages[] = sprintf( __( '%s could not be found', 'wpv-views' ), $post_type . ' ' . $option_value );
							}
						}
					} else if ( 
						$option_name == 'wpv_framework_keys' 
						&& is_array( $option_value ) 
					) {
						foreach ( $option_value as $framework_id => $framework_data ) {
							if ( 
								is_array( $framework_data )
								&& ! empty( $framework_data )
							) {
								$framework_data_clean = array();
								foreach ( $framework_data as $key => $framework_key ) {
									$framework_data_clean[] = $framework_key;
								}
								$option_value[$framework_id] = $framework_data_clean;
							} else {
								unset( $option_value[$framework_id] );
							}
						}
						if ( ! empty( $option_value ) ) {
							$WPV_settings[$option_name] = $option_value;
						}
					} else if ( 
						$option_name == 'wpv_custom_inner_shortcodes' 
						&& is_array( $option_value ) 
					) {
						// Custom inner shortcodes are exported in an associative array, we need to make it indexed
						$cis_option_value = array();
						foreach ( $option_value as $key => $inner_shortcode ) {
							$cis_option_value[] = $inner_shortcode;
						}
						$WPV_settings[$option_name] = $cis_option_value;
					} else if ( 
						$option_name == 'wpv_custom_conditional_functions' 
						&& is_array( $option_value ) 
					) {
						// Custom conditional functions are exported in an associative array, we need to make it indexed
						$ccf_option_value = array();
						foreach ( $option_value as $key => $cond_func ) {
							$ccf_option_value[] = $cond_func;
						}
						$WPV_settings[$option_name] = $ccf_option_value;
					} else {
						$WPV_settings[$option_name] = $option_value;
					}
				}
			}
			$WPV_settings->save();
			$this->import_messages[] = __( 'Settings updated', 'wpv-views' );
		}
		// @todo check if this should be an else-if instead
		if ( ! empty( $data['settings'] ) ) {
			foreach ( $WPV_settings as $option_name => $option_value ) {
				if ( 
					$force_settings_overwrite_views
					&& strpos( $option_name, 'view_' ) === 0 
				) {	
					if ( is_numeric( $option_value ) ) {
						$WPV_settings[$option_name] = 0;
					} else {
						$WPV_settings[$option_name] = '';
					}
				}
				if ( 
					$force_settings_overwrite_view_templates 
					&& strpos( $option_name, 'views_template_' ) === 0 
				) {	
					if ( is_numeric( $option_value ) ) {
						$WPV_settings[$option_name] = 0;
					} else {
						$WPV_settings[$option_name] = '';
					}
				}
			}
			foreach ( $data['settings'] as $option_name => $option_value ) {
				if ( 
					strpos( $option_name, 'view_' ) === 0 
					|| strpos( $option_name, 'views_template_' ) === 0 
				) {
					$post_type = strpos( $option_name, 'view_' ) === 0 ? 'view' : 'view-template';
					if ( $option_value ) {
						$post_id = $wpdb->get_var( 
							$wpdb->prepare( 
								"SELECT ID FROM {$wpdb->posts} 
								WHERE post_name = %s 
								AND post_type = %s 
								LIMIT 1", 
								$option_value, 
								$post_type 
							) 
						);
					} else {
						$post_id = 0;
					}
					
					if ( $post_id ) {
						if ( 
							$force_settings_overwrite_view_templates
							&& strpos( $option_name, 'views_template_' ) === 0 
						) {
							$WPV_settings[$option_name] = $post_id;	
						} else if ( 
							$force_settings_overwrite_views
							&& strpos( $option_name, 'view_' ) === 0 
						) {
							$WPV_settings[$option_name] = $post_id;	
						} else {
							if ( 
								! isset( $WPV_settings[$option_name] ) 
								|| ( 
									isset( $WPV_settings[$option_name] ) 
									&& $WPV_settings[$option_name] == 0
								) 
							) {
								$WPV_settings[$option_name] = $post_id;
							}
						}
					} else {
						$WPV_settings[$option_name] = 0;
						if ( $option_value ) {
							$this->import_messages[] = sprintf( __( '%s could not be found', 'wpv-views' ), $post_type . ' ' . $option_value );
						}
					}
				}
			}
			$WPV_settings->save();
			$this->import_messages[] = __( 'WordPress Archives and Content Templates settings updated', 'wpv-views' );		
		}	
		
		return true; // no errors
	}
	
}

global $WPV_Export_Import;
$WPV_Export_Import = new WPV_Export_Import_Embedded();

/**
* wpv_get_affiliate_url
*
* This legacy function was used to get the affiliate link for the import file
*
* @deprecated not used anywhere
*
* @since unknown
*/

function wpv_get_affiliate_url() {
    global $WPV_Export_Import;
    $affiliate_url = $WPV_Export_Import->legacy_get_affiliate_url();
    return $affiliate_url;
}

/**
* wpv_api_import_from_file
*
* API call to import Views data from a file
*
* @param $path (string) Path to the file to import
* @param $args (array) List of extra parameters when importing, to pass to wpv_admin_import_data()
* 	'import-file'				path to the XML file to import, or ZIP file that contains the settings.xml file to import
*	'views-overwrite' 			will force overwriting existing Views and WPA
* 	'views-delete' 				will delete any existing Views and WPA that are not on the import file
* 	'view-templates-overwrite'	will force overwriting existing CT
* 	'view-templates-delete' 	will delete any existing CT that is not on the import file
* 	'view-settings-overwrite' 	will overwrite existing settings
* 	'force_import_id,			will only import items with those XML IDs
*	'force_import_post_name',	will only import items with those XML post_name
*	'force_skip_id',			will not import items with those XML IDs
*	'force_skip_post_name',		will not import items with those XML post_name
*	'force_duplicate_id',		will duplicate, if they already exist, items with those XML IDs
*	'force_duplicate_post_name'	will duplicate, if they already exist, items with those XML post_name
*
* @return (mixed) true on success, WP_Error on failure
*
* @since 1.8.0
*/

function wpv_api_import_from_file( $args = array() ) {
	if ( ! current_user_can( 'manage_options' ) ) {
		return new WP_Error( 'wrong_capability', __( 'Your user can not perform that action.', 'wpv-views' ) );
	}
	global $WPV_Export_Import;
	$return = $WPV_Export_Import->import_data( $args );
	return $return;
}

/**
* wpv_admin_import_form
*
* Render the form for importing Views, CT and WPA
*
* @param $file_name (string) OPtional URL to the file to import, will render a file input otherwise
*
* @since unknown
*/

function wpv_admin_import_form( $file_name = '' ) {
    ?>
    <div class="wpv-setting-container">

        <div class="wpv-settings-header">
            <?php if ( $file_name != '' ): ?>
                <h3><?php _e('Import Views, WordPress Archives and Content Templates for your Theme','wpv-views'); ?></h3>
            <?php else: ?>
                <h3><?php _e('Import Views, WordPress Archives and Content Templates','wpv-views'); ?></h3>
            <?php endif; ?>
        </div>

        <form name="View_import" enctype="multipart/form-data" action="" method="post">

            <div class="wpv-setting">
                <h4><?php _e( 'Import options', 'wpv-views' ); ?></h4>
				<div class="wpv-advanced-setting">
                <ul>
                    <li>
                        <input id="checkbox-1" type="checkbox" name="views-overwrite" />
                        <label for="checkbox-1"><?php _e( 'Bulk overwrite if View or WordPress Archive exists', 'wpv-views' ); ?></label>
                    </li>
                    <li>
                        <input id="checkbox-2" type="checkbox" name="views-delete" />
                        <label for="checkbox-2"><?php _e( 'Delete any existing Views or WordPress Archives that are not in the import', 'wpv-views' ); ?></label>
                    </li>
                    <li>
                        <input id="checkbox-3" type="checkbox" name="view-templates-overwrite" />
                        <label for="checkbox-3"><?php _e( 'Bulk overwrite if Content Template exists', 'wpv-views' ); ?></label>
                    </li>
                    <li>
                        <input id="checkbox-4" type="checkbox" name="view-templates-delete" />
                        <label for="checkbox-4"><?php _e( 'Delete any existing Content Templates that are not in the import', 'wpv-views' ); ?></label>
                    </li>
                    <li>
                        <input id="checkbox-5" type="checkbox" name="view-settings-overwrite" />
                        <label for="checkbox-5"><?php _e( 'Overwrite Views settings', 'wpv-views' ); ?></label>
                    </li>
                </ul>
				</div>

				<?php if ( $file_name != '' ) { ?>
					
                <h4><?php _e( 'Import the Views XML file placed in the Views Embedded folder', 'wpv-views' ); ?></h4>
				<div class="wpv-advanced-setting">
                <p>
                    <input type="hidden" id="upload-views-file" name="import-file" value="<?php echo $file_name; ?>" />
                    <input type="hidden" name="page" value="views-import-export" />
                </p>
				</div>
				<?php } else { ?>
                <h4><?php _e( 'Select the Views XML file to upload from your computer', 'wpv-views' ); ?></h4>
				<div class="wpv-advanced-setting">
                <p>
                    <label for="upload-views-file"><?php _e('Upload file','wpv-views'); ?>:</label>
                    <input type="file" id="upload-views-file" name="import-file" />
                    <input type="hidden" name="page" value="views-import-export" />
                </p>
				</div>
				<?php } ?>

                <p class="update-button-wrap">
					<input id="wpv-import" type="hidden" value="wpv-import" name="import" />
					<button id="wpv-import-button" class="button-primary"><?php _e( 'Import', 'wpv-views' ); ?></button>
                </p>

                <?php wp_nonce_field( 'wpv-import-nonce', 'wpv-import-nonce' ); ?>

                </form>
            </div>
        </div>

    <?php
}

/**
* wpv_admin_import_data
*
* Main import method for Views
*
* @param $args (array) Import modifiers
*	'import-file' 				path to the file to import, can be overriden by $_FILES['import-file'] and $_POST['import-file']
*	'views-overwrite' 			will force overwriting existing Views and WPA
* 	'views-delete' 				will delete any existing Views and WPA that are not on the import file
* 	'view-templates-overwrite'	will force overwriting existing CT
* 	'view-templates-delete' 	will delete any existing CT that is not on the import file
* 	'view-settings-overwrite' 	will overwrite existing settings
*	'force_import_id,			will only import items with those XML IDs
*	'force_import_post_name',	will only import items with those XML post_name
*	'force_skip_id',			will not import items with those XML IDs
*	'force_skip_post_name',		will not import items with those XML post_name
*	'force_duplicate_id',		will duplicate, if they already exist, items with those XML IDs
*	'force_duplicate_post_name'	will duplicate, if they already exist, items with those XML post_name
*
* @return (mixed) false on success, WP_Error otherwise
*
* @since unknown
*
* @deprecated please use wpv_api_import_from_file instead, and note that it returns TRUE on success while this one returns FALSE on success
*/

function wpv_admin_import_data( $args = array() ) {
    if ( ! current_user_can( 'manage_options' ) ) {
		return new WP_Error( 'wrong_capability', __( 'Your user can not perform that action.', 'wpv-views' ) );
	}
	global $WP_Views;
	$file = false;
    if ( isset( $_FILES['import-file'] ) ) {
		// If import is happening from the import form, there should be a $_FILES['import-file'] entry
        $file = $_FILES['import-file'];
    } else {
		$candidate_file = false;
        if ( isset( $_POST['import-file'] ) ) {
			// Check for import file from settings.xml in theme
            $candidate_file = $_POST['import-file'];
        } else if ( isset( $args['import-file'] ) ) {
			// Check for import file from $args
			$candidate_file = $args['import-file'];
		}
		if ( 
			$candidate_file
			&& file_exists( $candidate_file )
		) {
			$file = array();
            $file['name'] = $candidate_file;
            $file['tmp_name'] = $candidate_file;
            $file['size'] = filesize( $candidate_file );
		}
    }

    if ( 
		! $file 
		|| ! isset( $file['name'] )
		|| empty( $file['name'] )
	) {
		return new WP_Error(' could_not_open_file', __( 'Could not read the Views import file.', 'wpv-views' ) );
    }

    $data = array();
    $info = pathinfo( $file['name'] );
    $is_zip = $info['extension'] == 'zip' ? true : false;
    if ( $is_zip ) {
        $zip = zip_open( urldecode( $file['tmp_name'] ) );
        if ( is_resource( $zip ) ) {
            while ( ( $zip_entry = zip_read( $zip ) ) !== false ) {
                if ( zip_entry_name( $zip_entry ) == 'settings.xml' ) {
                    $data = @zip_entry_read( $zip_entry, zip_entry_filesize( $zip_entry ) );
                }
            }
        } else {
            return new WP_Error( 'could_not_open_file', __( 'Unable to open zip file', 'wpv-views' ) );
        }
    } else {
        $fh = fopen( $file['tmp_name'], 'r' );
        if ( $fh ) {
            $data = fread( $fh, $file['size'] );
            fclose( $fh );
        }
    }

    if ( ! empty( $data ) ) {
        if ( ! function_exists( 'simplexml_load_string' ) ) {
            return new WP_Error( 'xml_missing', __( 'The Simple XML library is missing.', 'wpv-views' ) );
        }
        $xml = simplexml_load_string( $data );
        if ( ! $xml ) {
            return new WP_Error( 'not_xml_file', sprintf( __( 'The XML file (%s) could not be read.', 'wpv-views' ), $file['name'] ) );
        }
        $import_data = wpv_admin_import_export_simplexml2array( $xml );
		
		global $WPV_Export_Import;

        // Import Content Templates
        $result_content_templates = $WPV_Export_Import->import_content_templates( $import_data, $args );
        if ( is_wp_error( $result_content_templates ) ) {
            return $result_content_templates;
        }
        // Import Views
        $result_views = $WPV_Export_Import->import_views( $import_data, $args );
        if ( is_wp_error( $result_views ) ) {
            return $result_views;
        }
        // Import Settings
        $result_view_settings = $WPV_Export_Import->import_settings( $import_data, $args );
        if ( is_wp_error( $result_view_settings ) ) {
            return $result_view_settings;
        }
		
    } else {
        return new WP_Error( 'could_not_open_file', __( 'Could not read the Views import file.', 'wpv-views' ) );
    }
}

/**
* wpv_admin_import_view_templates
*
* @param $import_data (array) Array of items to import
* @param $args (array) Import modifiers
* 	'view-templates-overwrite'	will force overwriting existing CT
* 	'view-templates-delete' 	will delete any existing CT that is not on the import file
* 	'view-settings-overwrite' 	will overwrite existing settings
*	'force_import_id,			will only import items with those XML IDs
*	'force_import_post_name',	will only import items with those XML post_name
*	'force_skip_id',			will not import items with those XML IDs
*	'force_skip_post_name',		will not import items with those XML post_name
*	'force_duplicate_id',		will duplicate, if they already exist, items with those XML IDs
*	'force_duplicate_post_name'	will duplicate, if they already exist, items with those XML post_name
* 	'return_to'					will handle the special case of Module Manager imports
*
* @return (mixed) true on success, WP_Error otherwise
*
* @since unknown
*
* @deprecated Use $WPV_Export_Import->import_content_templates instead
*/

function wpv_admin_import_view_templates( $import_data, $args = array() ) {
	global $WPV_Export_Import;
	$result_content_templates = $WPV_Export_Import->import_content_templates( $import_data, $args );
	return $result_content_templates;
}

/**
* wpv_admin_import_views
*
* @param $import_data (array) Array of items to import
* @param $args (array) Import modifiers
*	'views-overwrite' 			will force overwriting existing Views and WPA
* 	'views-delete' 				will delete any existing Views and WPA that are not on the import file
*	'force_import_id,			will only import items with those XML IDs
*	'force_import_post_name',	will only import items with those XML post_name
*	'force_skip_id',			will not import items with those XML IDs
*	'force_skip_post_name',		will not import items with those XML post_name
*	'force_duplicate_id',		will duplicate, if they already exist, items with those XML IDs
*	'force_duplicate_post_name'	will duplicate, if they already exist, items with those XML post_name
* 	'return_to'					will handle the special case of Module Manager imports
*
* @return (mixed) true on success, WP_Error otherwise
*
* @since unknown
*
* @deprecated Use $WPV_Export_Import->import_views instead
*/

function wpv_admin_import_views( $import_data, $args = array() ) {
	global $WPV_Export_Import;
	$result_views = $WPV_Export_Import->import_views( $import_data, $args );
	return $result_views;
}

/**
* wpv_admin_import_settings
*
* @param $import_data (array) Array of data to import
* @param $args (array) Import modifiers
*	'views-overwrite' 			will force overwriting existing Views and WPA
* 	'view-templates-overwrite'	will force overwriting existing CT
* 	'view-settings-overwrite' 	will overwrite existing settings
*
* @return (mixed) true on success, WP_Error otherwise
*
* @since unknown
*
* @deprecated Use $WPV_Export_Import->import_settings instead
*/

function wpv_admin_import_settings( $import_data, $args = array() ) {
	global $WPV_Export_Import;
	$result_settings = $WPV_Export_Import->import_settings( $import_data, $args );
	return $result_settings;
}

/**
* wpv_admin_import_export_simplexml2array
*
* Loops over elements and convert to array or empty string.
*
* @param $element (array) XML data as an array
*
* @return (array|string)
*
* @since unknown
*/
function wpv_admin_import_export_simplexml2array( $element ) {
    $element = is_string( $element ) ? trim( $element ) : $element;
    if ( !empty( $element ) && is_object( $element ) ) {
        $element = (array) $element;
    }
    // SRDJAN - slider settings that have 0 values are imported as empty string https://icanlocalize.basecamphq.com/projects/7393061-wp-views/todo_items/142382765/comments
    if ( !is_array( $element ) && strval( $element ) == '0' ) {
        $element = 0;
    } else if ( empty( $element ) ) {
        $element = '';
    } else if ( is_array( $element ) ) {
        foreach ( $element as $k => $v ) {
            $k = str_replace( '___032___', ' ', $k ); // unencode spaces
            $v = is_string( $v ) ? trim( $v ) : $v;
            if ( !is_array( $v ) && strval( $v ) == '0' ) {
                $element[$k] = 0;
            } else if ( empty( $v ) ) {
                $element[$k] = '';
                continue;
            }
            $add = wpv_admin_import_export_simplexml2array( $v );
            if ( !is_array( $add ) && strval( $add ) == '0' ) {
                $element[$k] = 0;
            } else if ( !empty( $add ) ) {
                $element[$k] = $add;
            } else {
                $element[$k] = '';
            }
        }
    }

    if ( !is_array( $element ) && strval( $element ) == '0' ) {
        $element = 0;
    } else if ( empty( $element ) ) {
        $element = '';
    }

    return $element;
}

/**
 * Converts URLs.
 *
 * @param type $url
 * @param type $site_url
 * @param type $upload_url
 * @return type
 */
function wpv_convert_url( $url, $site_url, $upload_url ) {
    // Check if uploaded files URL or other URL
    if ( strpos( $url, (string) $upload_url ) !== false ) {
        $upload_dir = wp_upload_dir();
        $url = str_replace( (string) $upload_url, $upload_dir['baseurl'], $url );
    } else if ( strpos( $url, (string) $site_url ) !== false ) {
        $url = str_replace( (string) $site_url, get_site_url(), $url );
    }
    return $url;
}

/**
* _wpv_adjust_view_extra_postmeta_keys_for_export_import
* 
* Set the basic postmeta keys that need to be exported and imported on a View, beyond the settings and layout settings
*
* @param (array) $meta_keys
*
* @return (array)
*
* @since 1.7
*/

add_filter( 'wpv_filter_view_extra_fields_for_import_export', '_wpv_adjust_view_extra_postmeta_keys_for_export_import', 10, 1 );

function _wpv_adjust_view_extra_postmeta_keys_for_export_import( $meta_keys = array() ) {
	$meta_keys = array(
		'_wpv_description',
		'_view_loop_template'
	);
	return $meta_keys;
}

/**
*
* Export adjustments
*
*/

/**
* _wpv_adjust_view_arrays_for_export
*
* Adjust values for several View settings that are stored as indexed arrays that break the XML export file
* We do not include here filters that need ID-to-name adjustments
*
* @param (array) $view_settings
* @param (array) $view_post_array
*
* @return
*
* @since 1.7
*/


add_filter( 'wpv_filter_adjust_view_settings_for_export', '_wpv_adjust_view_arrays_for_export', 10, 2 );

function _wpv_adjust_view_arrays_for_export( $view_settings = array(), $view_post_array = array() ) {
	// Content Selection section
	if ( isset( $view_settings['post_type'] ) ) {
		if ( is_array( $view_settings['post_type'] ) ) {
			$view_settings['post_types'] = $view_settings['post_type'];
			foreach ( $view_settings['post_types'] as $key => $value ) {
				$view_settings['post_types'][ 'post_type-' . $key ] = $value;
				unset( $view_settings['post_types'][ $key ] );
			}
			$view_settings['post_types']['__key'] = 'post_type';
		}
		unset( $view_settings['post_type'] );
	}
	if ( isset( $view_settings['taxonomy_type'] ) ) {
		if ( is_array( $view_settings['taxonomy_type'] ) ) {
			$view_settings['taxonomy_types'] = $view_settings['taxonomy_type'];
			foreach ( $view_settings['taxonomy_types'] as $key => $value ) {
				$view_settings['taxonomy_types'][ 'taxonomy_type-' . $key ] = $value;
				unset( $view_settings['taxonomy_types'][ $key ] );
			}
			$view_settings['taxonomy_types']['__key'] = 'taxonomy_type';
		}
		unset( $view_settings['taxonomy_type'] );
	}
	if ( isset( $view_settings['roles_type'] ) ) {
		if ( is_array( $view_settings['roles_type'] ) ) {
			$view_settings['roles_types'] = $view_settings['roles_type'];
			foreach ( $view_settings['roles_types'] as $key => $value ) {
				$view_settings['roles_types'][ 'roles_type-' . $key ] = $value;
				// Backwards compatibility
				unset( $view_settings['roles_types'][ $key ] );
			}
			$view_settings['roles_types']['__key'] = 'roles_type';
		}
		unset( $view_settings['roles_type'] );
	}
	// Status filter settings are stored in an indexed array
	if ( isset( $view_settings['post_status'] ) ) {
		if ( is_array( $view_settings['post_status'] ) ) {
			$view_settings['post_statuses'] = $view_settings['post_status'];
			foreach ( $view_settings['post_statuses'] as $key => $value ) {
				$view_settings['post_statuses'][ 'post_status-' . $key ] = $value;
				unset( $view_settings['post_statuses'][ $key ] );
			}
			$view_settings['post_statuses']['__key'] = 'post_status';
		}
		unset( $view_settings['post_status'] );
	}
	// Taxonomy terms filter
	if ( isset( $view_settings['taxonomy_terms'] ) ) {
		if ( is_array( $view_settings['taxonomy_terms'] ) ) {
			foreach( $view_settings['taxonomy_terms'] as $key => $value ) {
				$view_settings['taxonomy_terms'][ 'taxonomy_term-' . $key ] = $value;
				unset( $view_settings['taxonomy_terms'][ $key ] );
			}
			$view_settings['taxonomy_terms']['__key'] = 'taxonomy_term';
		} else {
			unset( $view_settings['taxonomy_terms'] );
		}
	}
	// Views settings stored as an array with just one entry
	$settings_to_set_as_themselves = array(
		'post_relationship_mode', 'parent_mode'
	);
	foreach ( $settings_to_set_as_themselves as $set_itself ) {
		if ( isset( $view_settings[$set_itself][0] ) ) {
			$view_settings[$set_itself] = $view_settings[$set_itself][0];
		}
	}
	// For backwards compatibility, there are some settings being stored as 'type' and other as 'state'
	$settings_to_set_as_type = array(
		'author_mode', 'users_mode'
	);
	foreach ( $settings_to_set_as_type as $set_type ) {
		if ( isset( $view_settings[$set_type][0] ) ) {
			$view_settings[$set_type]['type'] = $view_settings[$set_type][0];
			unset( $view_settings[$set_type][0] );
		}
	}
	$settings_to_set_as_state = array(
		'query_type', 
		'taxonomy_parent_mode', 'taxonomy_search_mode', 'search_mode', 'id_mode', 
		'pagination', 'ajax_pagination', 
	);
	foreach ( $settings_to_set_as_state as $set_state ) {
		if ( isset( $view_settings[$set_state][0] ) ) {
			$view_settings[$set_state]['state'] = $view_settings[$set_state][0];
			unset( $view_settings[$set_state][0] );
		}
	}
	return $view_settings;
}

/**
* _wpv_adjust_view_ids_to_names_for_export
*
* Adjust values for things stored as IDs that need to be exported as names
*
* @param
* @param
*
* @return
*
* @since 1.7
*/


add_filter( 'wpv_filter_adjust_view_settings_for_export', '_wpv_adjust_view_ids_to_names_for_export', 20, 2 );

function _wpv_adjust_view_ids_to_names_for_export( $view_settings = array(), $view_array = array() ) {
	global $wpdb;
	// Query filter by a specific post parent
	if ( 
		isset( $view_settings['parent_id'] ) 
		&& $view_settings['parent_id'] != '' 
	) {
		$parent_name = $wpdb->get_var( 
			$wpdb->prepare(
				"SELECT post_name FROM {$wpdb->posts} WHERE ID = %d LIMIT 1",
				$view_settings['parent_id'] 
			)
		);
		if ( $parent_name ) {
			$view_settings['parent_id'] = $parent_name;
		} else {
			unset( $view_settings['parent_id'] );
		}
	}
	// Query filter by a specific post relationship
	if ( 
		isset( $view_settings['post_relationship_id'] ) 
		&& $view_settings['post_relationship_id'] != '' 
	) {
		$post_relationship_name = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT post_name FROM {$wpdb->posts} WHERE ID = %d LIMIT 1",
				$view_settings['post_relationship_id'] 
			) 
		);
		if ( $post_relationship_name ) {
			$view_settings['post_relationship_id'] = $post_relationship_name;
		} else {
			unset( $view_settings['post_relationship_id'] );
		}
	}
	// Query filter by specific IDs
	if ( isset( $view_settings['post_id_ids_list'] ) ) {
		if ( ! empty( $view_settings['post_id_ids_list'] ) ) {
			// Explode and implode just in case - trust nothing!
			$id_array = explode( ',', $view_settings['post_id_ids_list'] );
			$id_array = array_map( 'esc_attr', $id_array );
			$id_array = array_map( 'trim', $id_array );
			// is_numeric + intval does sanitization
			$id_array = array_filter( $id_array, 'is_numeric' );
			$id_array = array_map( 'intval', $id_array );
			$query_limit = count( $id_array );
			$id_array_for_query = implode( ',', $id_array );
			$id_array_names = array();
			if ( $query_limit > 0 ) {
				$id_array_names = $wpdb->get_col( 
					$wpdb->prepare(
						"SELECT post_name FROM {$wpdb->posts} 
						WHERE ID IN ( " . $id_array_for_query . " ) 
						LIMIT %d",
						$query_limit
					)
				);
			}
			if ( ! empty( $id_array_names ) ) {
				$id_post_names = array();
				foreach ( $id_array_names as $id_key => $id_array_name_item ) {
					$id_post_names[ 'post-' . $id_key ] = $id_array_name_item;
				}
				$view_settings['post_id_ids_list'] = $id_post_names;
			} else {
				unset( $view_settings['post_id_ids_list'] );
			}			
		} else {
			unset( $view_settings['post_id_ids_list'] );
		}
	}
	return $view_settings;
}

/**
* _wpv_adjust_view_filter_by_tax_for_export
*
* Adjust values for several View settings related to query filters by taxes
*
* @param (array) $view_settings
* @param (array) $view_post_array
*
* @return
*
* @since 1.7
*/


add_filter( 'wpv_filter_adjust_view_settings_for_export', '_wpv_adjust_view_filter_by_tax_for_export', 30, 2 );

function _wpv_adjust_view_filter_by_tax_for_export( $view_settings = array(), $view_post_array = array() ) {
	$taxonomies = get_taxonomies( '', 'names' );
	foreach ( $view_settings as $set_key => $set_value ) {
		if ( $set_key == 'post_category' ) {
			// Special case: category taxonomy
			// Check if category is a valid taxonomy and if we have an array of terms
			if ( in_array( 'category', $taxonomies ) ) {
				if ( is_array( $view_settings['post_category'] ) ) {
					foreach ( $view_settings['post_category'] as $key => $id ) {
						$term = get_term_by( 'id', $id, 'category' );
						if ( $term ) {
							$view_settings['post_category'][ 'cat-' . $key ] = $term->name;
						}
						unset( $view_settings['post_category'][ $key ] );
					}
					$view_settings['post_category']['__key'] = 'cat';
				} else {
					unset( $view_settings['post_category'] );
				}
			} else {
				$to_delete = array(
					'post_category', 'tax_category_relationship', 'taxonomy-category-attribute-url', 'taxonomy-category-attribute-url-format', 'taxonomy-category-attribute-operator',
				);
				foreach ( $to_delete as $cat_index_to_delete ) {
					if ( isset( $view_settings[ $cat_index_to_delete ] ) ) {
						unset( $view_settings[ $cat_index_to_delete ] );
					}
				}
			}

		} elseif ( strpos( $set_key, 'tax_input_' ) === 0 ) {
			// Any other taxonomy
			// Check if this is a valid taxonomy and if we have an array of terms
			$tax_key = substr( $set_key, strlen( 'tax_input_' ) );
			if ( in_array( $tax_key, $taxonomies ) ) {
				if ( is_array( $view_settings[ $set_key ] ) ) {
					foreach ( $view_settings[ $set_key ] as $key => $id ) {
						$term = get_term_by( 'id', $id, $tax_key );
						if ( $term ) {
							$view_settings[ $set_key ][ 'cat-' . $key ] = $term->name;
						}
						unset( $view_settings[ $set_key ][ $key ] );
					}
					$view_settings[ $set_key ]['__key'] = 'cat';
				} else {
					unset( $view_settings[ $set_key ] );
				}
			} else {
				$to_delete = array(
					$set_key, 'tax_' . $tax_key . '_relationship', 'taxonomy-' . $tax_key . '-attribute-url',
					'taxonomy-' . $tax_key . '-attribute-url-format', 'taxonomy-' . $tax_key . '-attribute-operator',
				);
				foreach ( $to_delete as $tax_index_to_delete ) {
					if ( isset( $view_settings[ $tax_index_to_delete ] ) ) {
						unset( $view_settings[ $tax_index_to_delete ] );
					}
				}
			}
		} elseif ( 
			strpos( $set_key, 'taxonomy-' ) === 0
			&& strpos( $set_key, '-attribute-url-format' ) === ( strlen( $set_key ) - strlen( '-attribute-url-format' ) ) 
		) {
			$tax_key = substr( $set_key, 0, strlen( $set_key ) - strlen( '-attribute-url-format' ) );
			$tax_key = substr( $tax_key, strlen( 'taxonomy-' ) );
			if ( in_array( $tax_key, $taxonomies ) ) {
				if ( isset( $view_settings[ $set_key ][0] ) ) {
					$view_settings[ $set_key ] = $view_settings[ $set_key ][0];
				}
			} else {
				$to_delete = array(
					'tax_' . $tax_key . '_relationship', 'taxonomy-' . $tax_key . '-attribute-url',
					'taxonomy-' . $tax_key . '-attribute-url-format', 'taxonomy-' . $tax_key . '-attribute-operator',
				);
				foreach ( $to_delete as $tax_index_to_delete ) {
					if ( isset( $view_settings[ $tax_index_to_delete ] ) ) {
						unset( $view_settings[ $tax_index_to_delete ] );
					}
				}
			}
		}
	}
	return $view_settings;
}

/**
* _wpv_adjust_view_parametric_for_export
*
* Adjust values for several View settings related to parametric search
*
* @param (array) $view_settings
* @param (array) $view_post_array
*
* @return
*
* @since 1.7
*/


add_filter( 'wpv_filter_adjust_view_settings_for_export', '_wpv_adjust_view_parametric_for_export', 40, 2 );

function _wpv_adjust_view_parametric_for_export( $view_settings = array(), $view_post_array = array() ) {
	$filter_control_settings = array(
		'filter_controls_enable', 'filter_controls_param', 'filter_controls_mode', 'filter_controls_field_name',
		'filter_controls_label', 'filter_controls_type', 'filter_controls_values' 
	);
	foreach( $filter_control_settings as $filter_control ) {
		if ( isset( $view_settings[ $filter_control ] ) ) {
			if ( is_array( $view_settings[ $filter_control ] ) ) {
				foreach ( $view_settings[ $filter_control ] as $key => $value ) {
					$view_settings[ $filter_control ][ $filter_control . '-' . $key ] = $value;
					unset( $view_settings[ $filter_control ][ $key ] );
				}
				$view_settings[ $filter_control ]['__key'] = $filter_control;
			} else {
				unset( $view_settings[ $filter_control ] );
			}
		}
	}
	return $view_settings;
}

/**
* _wpv_adjust_view_layout_settings_for_export
*
* Adjust values for several View layout settings
*
* @param (array) $view_layout_settings
* @param (array) $view_post_array
*
* @return
*
* @since 1.7
*/


add_filter( 'wpv_filter_adjust_view_layout_settings_for_export', '_wpv_adjust_view_layout_settings_for_export', 10, 2 );

function _wpv_adjust_view_layout_settings_for_export( $view_layout_settings = array(), $view_post_array = array() ) {
	// This are old deprecated layout settings we can delete
	if ( isset( $view_layout_settings['fields'][0] ) ) {
		unset( $view_layout_settings['fields'] );
	}
	// Adjust real fields, stored in an indexed array
	if ( isset( $view_layout_settings['real_fields'] ) ) {
		// Fix an issue with imported Views not correctly setting the layout_settings[real_fields] array and breaking the XML file when exporting
		if ( 
			is_array( $view_layout_settings['real_fields'] ) 
			&& isset( $view_layout_settings['real_fields']['real_fields'] ) 
		) {
			$trans = $view_layout_settings['real_fields']['real_fields'];
			$view_layout_settings['real_fields'] = $trans;
		}
		if ( is_array( $view_layout_settings['real_fields'] ) ) {
			// Only add the layout_settings[real_fields] items if this is an array
			// On 1.3? there might be a problem when there is only one item, and it is stored as a string instead
			// Some eggs need to be broken: no layout edit using the Wizard in this case will be available
			foreach ( $view_layout_settings['real_fields'] as $key => $value ) {
				$view_layout_settings['real_fields'][ 'real_field-'.$key ] = $value;
				unset( $view_layout_settings['real_fields'][$key] );
			}
		} else {
			// If there is only one item and it's not wrapped in an array, say goodbye to the Wizard edit advantage
			// Those eggs are already broken
			unset( $view_layout_settings['real_fields'] );
		}
	}
	return $view_layout_settings;
}

/**
* _wpv_adjust_view_layout_templates_for_export
*
* Adjust values for several View layout settings related to CT
*
* @param (array) $view_layout_settings
* @param (array) $view_post_array
*
* @return
*
* @since 1.7
*/


add_filter( 'wpv_filter_adjust_view_layout_settings_for_export', '_wpv_adjust_view_layout_templates_for_export', 20, 2 );

function _wpv_adjust_view_layout_templates_for_export( $view_layout_settings = array(), $view_post_array = array() ) {
	global $wpdb;
	// Inline Templates
	if ( isset( $view_layout_settings['included_ct_ids'] ) ) {
		if ( ! empty( $view_layout_settings['included_ct_ids'] ) ) {
			// Explode and implode just in case - trust nothing!
			$templates_array = explode( ',', $view_layout_settings['included_ct_ids'] );			
			$templates_array = array_map( 'esc_attr', $templates_array );
			$templates_array = array_map( 'trim', $templates_array );
			// is_numeric + intval does sanitization
			$templates_array = array_filter( $templates_array, 'is_numeric' );
			$templates_array = array_map( 'intval', $templates_array );
			$query_limit = count( $templates_array );
			$templates_array_names = array();
			if ( $query_limit > 0 ) {
				$templates_array_for_query = implode( ',', $templates_array );
				$templates_array_names = $wpdb->get_col(
					$wpdb->prepare(
						"SELECT post_title FROM {$wpdb->posts} 
						WHERE ID IN ( " . $templates_array_for_query . " ) 
						AND post_type = 'view-template' 
						LIMIT %d",
						$query_limit
					)
				);
			}
			if ( ! empty( $templates_array_names ) ) {
				$template_post_names = array();
				foreach ( $templates_array_names as $template_key => $template_array_name_item ) {
					$template_post_names[] = $template_array_name_item;
				}
				if ( count( $template_post_names ) > 0 ) {
					$view_layout_settings['included_ct_ids'] = implode( '#SEPARATOR#', $template_post_names );
				} else {
					unset( $view_layout_settings['included_ct_ids'] );
				}
			} else {
				unset( $view_layout_settings['included_ct_ids'] );
			}
		} else {
			unset( $view_layout_settings['included_ct_ids'] );
		}
	}
	return $view_layout_settings;
}

/**
* _wpv_adjust_view_loop_template_for_export
* 
* Replaces the loop Template ID with its post_title during export
*
* @param (int) $value The loop Template ID, if any
* @param (array) $view_post_array The View post as an array
* @param (string) $meta_key The postmeta key to check against _view_loop_template
*
* @return (string)
*
* @since 1.7
*/

add_filter( 'wpv_filter_adjust_view_extra_fields_for_export', '_wpv_adjust_view_loop_template_for_export', 10, 3 );

function _wpv_adjust_view_loop_template_for_export( $value, $view_post_array, $meta_key ) {
	if (
		$meta_key == '_view_loop_template'
		&& $value != ''
	) {
		global $wpdb;
		$loop_template_title = $wpdb->get_var( 
			$wpdb->prepare(
				"SELECT post_title FROM {$wpdb->posts} WHERE ID = %d AND post_type = 'view-template' LIMIT 1",
				$value
			)
		);
		if ( $loop_template_title ) {
			$value = $loop_template_title;
		} else {
			$value = '';
		}
	}
	return $value;
}

/*#################################
*
* Import adjustments
*
#################################*/

/**
* _wpv_adjust_view_arrays_for_import
*
* Adjust values for several View settings that are stored as indexed arrays that break the XML export file
* We do not include here filters that need ID-to-name adjustments
*
* @param (array) $view_settings
* @param (array) $view_post_array
*
* @return
*
* @since 1.7
*/


add_filter( 'wpv_filter_adjust_view_settings_for_import', '_wpv_adjust_view_arrays_for_import', 10, 2 );

function _wpv_adjust_view_arrays_for_import( $view_settings = array(), $view_post_array = array() ) {
	// Content Selection section
	if ( isset( $view_settings['post_types'] ) ) {
		$view_settings['post_type'] = $view_settings['post_types'];
		unset( $view_settings['post_types'] );
		if ( is_array( $view_settings['post_type']['post_type'] ) ) {
			$view_settings['post_type'] = $view_settings['post_type']['post_type'];
		} else if ( isset( $view_settings['post_type']['post_type'] ) ) {
			$view_settings['post_type'][0] = $view_settings['post_type']['post_type'];
			unset( $view_settings['post_type']['post_type'] );
		}
	}
	if ( isset( $view_settings['taxonomy_types'] ) ) {
		$view_settings['taxonomy_type'] = $view_settings['taxonomy_types'];
		unset( $view_settings['taxonomy_types'] );
		if ( is_array( $view_settings['taxonomy_type']['taxonomy_type'] ) ) {
			$view_settings['taxonomy_type'] = $view_settings['taxonomy_type']['taxonomy_type'];
		} elseif ( isset( $view_settings['taxonomy_type']['taxonomy_type'] ) ) {
			$view_settings['taxonomy_type'][0] = $view_settings['taxonomy_type']['taxonomy_type'];
			unset( $view_settings['taxonomy_type']['taxonomy_type'] );
		}
	}
	if ( isset( $view_settings['roles_types'] ) ) {
		$view_settings['roles_type'] = $view_settings['roles_types'];
		unset( $view_settings['roles_types'] );
		if ( is_array( $view_settings['roles_type']['roles_type'] ) ) {
			$view_settings['roles_type'] = $view_settings['roles_type']['roles_type'];
		} elseif ( isset( $view_settings['roles_type']['roles_type'] ) ) {
			$view_settings['roles_type'][0] = $view_settings['roles_type']['roles_type'];
			unset( $view_settings['roles_type']['roles_type'] );
		}
	}
	// Post status filter
	if ( isset( $view_settings['post_statuses'] ) ) {
		$view_settings['post_status'] = $view_settings['post_statuses'];
		unset( $view_settings['post_statuses'] );
		if ( is_array( $view_settings['post_status']['post_status'] ) ) {
			$view_settings['post_status'] = $view_settings['post_status']['post_status'];
		} else if ( isset( $view_settings['post_status']['post_status'] ) ) {
			$view_settings['post_status'][0] = $view_settings['post_status']['post_status'];
			unset( $view_settings['post_status']['post_status'] );
		}
	}
	// Taxonomy terms filter
	if ( isset( $view_settings['taxonomy_terms'] ) ) {
		if ( is_array( $view_settings['taxonomy_terms']['taxonomy_term'] ) ) {
			$view_settings['taxonomy_terms'] = $view_settings['taxonomy_terms']['taxonomy_term'];
		} else if ( isset( $view_settings['taxonomy_terms']['taxonomy_term'] ) ) {
			$view_settings['taxonomy_terms'][0] = $view_settings['taxonomy_terms']['taxonomy_term'];
			unset( $view_settings['taxonomy_terms']['taxonomy_term'] );
		}
	}
	// Other things stored in arrays
	if ( isset( $view_settings['query_type']['state'] ) ) {
		$view_settings['query_type'][0] = $view_settings['query_type']['state'];
		unset( $view_settings['query_type']['state'] );
	} else {
		$view_settings['query_type'][0] = 'posts';
	}
	
	if ( isset( $view_settings['taxonomy_parent_mode']['state'] ) ) {
		$view_settings['taxonomy_parent_mode'] = array( $view_settings['taxonomy_parent_mode']['state'] );
	}
	if ( isset( $view_settings['taxonomy_search_mode']['state'] ) ) {
		$view_settings['taxonomy_search_mode'] = array( $view_settings['taxonomy_search_mode']['state'] );
	}
	if ( isset( $view_settings['search_mode']['state'] ) ) {
		$view_settings['search_mode'] = array( $view_settings['search_mode']['state'] );
	}
	if ( isset( $view_settings['id_mode']['state'] ) ) {
		$view_settings['id_mode'] = array( $view_settings['id_mode']['state'] );
	}
	
	if ( isset( $view_settings['author_mode']['type'] ) ) {
		$view_settings['author_mode'] = array( $view_settings['author_mode']['type'] );
	}
	if ( isset( $view_settings['users_mode']['type'] ) ) {
		$view_settings['users_mode'] = array( $view_settings['users_mode']['type'] );
	}
	
	if ( isset( $view_settings['parent_mode'] ) ) {
		$view_settings['parent_mode'] = array( $view_settings['parent_mode'] );
	}
	if ( isset( $view_settings['post_relationship_mode'] ) ) {
		$view_settings['post_relationship_mode'] = array( $view_settings['post_relationship_mode'] );
	}
	
	
	if ( isset( $view_settings['pagination']['state'] ) ) {
		$view_settings['pagination'][0] = $view_settings['pagination']['state'];
		unset( $view_settings['pagination']['state'] );
	}
	if ( isset( $view_settings['ajax_pagination']['state'] ) ) {
		$view_settings['ajax_pagination'][0] = $view_settings['ajax_pagination']['state'];
		unset( $view_settings['ajax_pagination']['state'] );
	}
	return $view_settings;
}

/**
* _wpv_adjust_view_names_to_ids_for_import
*
* Adjust values for things stored as IDs that need to be exported as names
*
* @param
* @param
*
* @return
*
* @since 1.7
*/


add_filter( 'wpv_filter_adjust_view_settings_for_import', '_wpv_adjust_view_names_to_ids_for_import', 20, 2 );

function _wpv_adjust_view_names_to_ids_for_import( $view_settings = array(), $view_post_array = array() ) {
	global $wpdb;
	if (
		isset( $view_settings['parent_id'] ) 
		&& $view_settings['parent_id'] != '' 
	) {
		$parent_id = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts} WHERE post_name = %s LIMIT 1",
				$view_settings['parent_id'] 
			) 
		);
		if ( $parent_id ) {
			$view_settings['parent_id'] = $parent_id;
		} else {
			unset( $view_settings['parent_id'] );
		}
	}
	if (
		isset( $view_settings['post_relationship_id'] ) 
		&& $view_settings['post_relationship_id'] != '' 
	) {
		$post_relationship_id = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts} WHERE post_name = %s LIMIT 1",
				$view_settings['post_relationship_id'] 
			)
		);
		if ( $post_relationship_id ) {
			$view_settings['post_relationship_id'] = $post_relationship_id;
		} else {
			unset( $view_settings['post_relationship_id'] );
		}
	}
	
	if ( 
		isset( $view_settings['author_name'] ) 
		&& ! empty( $view_settings['author_name'] ) 
	) {
		$new_author = '';
		$new_author = $wpdb->get_var( 
			$wpdb->prepare(
				"SELECT ID FROM {$wpdb->users} WHERE display_name = %s LIMIT 1",
				$view_settings['author_name'] 
			)
		);
		if ( 
			isset( $new_author ) 
			&& ! empty( $new_author ) 
		) {
			$view_settings['author_id'] = $new_author;
		} else {
			unset( $view_settings['author_name'] );
			unset( $view_settings['author_id'] );
		}
	}
	
	if ( isset( $view_settings['users_name'] ) ) {
		if ( ! empty( $view_settings['users_name'] ) ) {
			$user_list = array_map( 'trim', explode( ',', $view_settings['users_name'] ) );
			$user_list = array_map( 'sanitize_text_field', $user_list );
			$new_users_ids = array();
			$new_users_names = array();
			if ( ! empty( $user_list ) ) {
				$values_to_prepare = array();
				$query_limit = count( $user_list );
				$user_list_placeholders = array_fill( 0, $query_limit, '%s' );
				$user_list_for_query = implode( ",", $user_list_placeholders );
				foreach ( $user_list as $user_list_item ) {
					$values_to_prepare[] = $user_list_item;
				}
				$values_to_prepare[] = $query_limit;
				$new_users = $wpdb->get_results( 
					$wpdb->prepare(
						"SELECT ID, display_name FROM {$wpdb->users} 
						WHERE display_name IN ( " . $user_list_for_query . " ) 
						LIMIT %d",
						$values_to_prepare
					)
				);
				if ( ! empty( $new_users ) ) {
					foreach ( $new_users as $new_user_data ) {
						$new_users_ids[] = $new_user_data->ID;
						$new_users_names[] = $new_user_data->display_name;
					}
					$view_settings['users_name'] = implode( ",", $new_users_names );
					$view_settings['users_id'] = implode( ",", $new_users_ids );
				} else {
					unset( $view_settings['users_name'] );
					if ( isset( $view_settings['users_id'] ) ) {
						unset( $view_settings['users_id'] );
					}
				}
			}
		} else {
			unset( $view_settings['users_name'] );
			if ( isset( $view_settings['users_id'] ) ) {
				unset( $view_settings['users_id'] );
			}
		}
	}
	
	if ( isset( $view_settings['post_id_ids_list'] ) ) {
		if ( 
			empty( $view_settings['post_id_ids_list'] )
			|| ! is_array( $view_settings['post_id_ids_list'] ) 
		) {
			unset( $view_settings['post_id_ids_list'] );
		} else {
			$ids_list = array();
			$ids_lost = array();
			$values_to_prepare = array();
			$id_list_names = array_map( 'sanitize_text_field', $view_settings['post_id_ids_list'] );
			$query_limit = count( $id_list_names);
			$id_list_placeholders = array_fill( 0, $query_limit, '%s' );
			$id_list_names_for_query = implode( ",", $id_list_placeholders );
			foreach ( $id_list_names as $id_list_names_item ) {
				$values_to_prepare[] = $id_list_names_item;
			}
			$values_to_prepare[] = $query_limit;
			$new_post_ids = $wpdb->get_col( 
				$wpdb->prepare(
					"SELECT ID FROM {$wpdb->posts} 
					WHERE post_name IN ( " . $id_list_names_for_query . " ) 
					LIMIT %d",
					$values_to_prepare
				)
			);
			if ( ! empty( $new_post_ids ) ) {
				$view_settings['post_id_ids_list'] = implode( ',', $new_post_ids );
				if ( count( $view_settings['post_id_ids_list'] ) != $query_limit ) {
					$view_settings['post_id_ids_list_lost'] = 1;
				}
			} else {
				$view_settings['post_id_ids_list_lost'] = $query_limit;
				unset( $view_settings['post_id_ids_list'] );
			}
		}
	}
	
	return $view_settings;
}

/**
* _wpv_adjust_view_filter_by_tax_for_import
*
* Adjust values for several View settings related to query filters by taxes
*
* @param (array) $view_settings
* @param (array) $view_post_array
*
* @return
*
* @since 1.7
*/


add_filter( 'wpv_filter_adjust_view_settings_for_import', '_wpv_adjust_view_filter_by_tax_for_import', 30, 2 );

function _wpv_adjust_view_filter_by_tax_for_import( $view_settings = array(), $view_post_array = array() ) {
	$taxonomies = get_taxonomies( '', 'objects' );
	foreach ( $taxonomies as $category_slug => $category ) {
		$save_name = ( $category->name == 'category' ) ? 'post_category' : 'tax_input_' . $category->name;
		if ( isset( $view_settings[ $save_name ] ) ) {
			if ( is_array( $view_settings[ $save_name ]['cat'] ) ) {
				$view_settings[ $save_name ] = $view_settings[ $save_name ]['cat'];
			} else if ( isset( $view_settings[ $save_name ]['cat'] ) ) {
				$view_settings[ $save_name ][0] = $view_settings[ $save_name ]['cat'];
				unset( $view_settings[ $save_name ]['cat'] );
			} else {
				$view_settings[ $save_name ] = array();
			}
			foreach( $view_settings[ $save_name ] as $key => $name ) {
				$term = get_term_by( 'name', $name, $category->name );
				if ( $term ) {
					$view_settings[ $save_name ][ $key ] = $term->term_id;
				}
			}
		}
		// Use this to check attribute-url-format
		$attribute_url_format = 'taxonomy-' . $category->name . '-attribute-url-format';
		if ( isset( $view_settings[ $attribute_url_format ] ) ) {
			$view_settings[ $attribute_url_format ] = array( $view_settings[ $attribute_url_format ] );
		}
	}
	return $view_settings;
}

/**
* _wpv_adjust_view_parametric_for_import
*
* Adjust values for several View settings related to parametric search
*
* @param (array) $view_settings
* @param (array) $view_post_array
*
* @return
*
* @since 1.7
*/


add_filter( 'wpv_filter_adjust_view_settings_for_import', '_wpv_adjust_view_parametric_for_import', 40, 2 );

function _wpv_adjust_view_parametric_for_import( $view_settings = array(), $view_post_array = array() ) {
	$filter_control_settings = array(
		'filter_controls_enable',
		'filter_controls_param',
		'filter_controls_mode',
		'filter_controls_field_name',
		'filter_controls_label',
		'filter_controls_type',
		'filter_controls_values' 
	);
	foreach ( $filter_control_settings as $filter_control ) {
		if ( isset( $view_settings[ $filter_control ][ $filter_control ] ) ) {
			if ( is_array( $view_settings[ $filter_control ][ $filter_control ] ) ) {
				$view_settings[ $filter_control ] = $view_settings[ $filter_control ][ $filter_control ];
			}
			unset( $view_settings[ $filter_control ][ $filter_control ] );
		}
	}
	return $view_settings;
}

/**
* _wpv_adjust_view_layout_settings_for_import
*
* Adjust values for several View layout settings
*
* @param (array) $view_layout_settings
* @param (array) $view_post_array
*
* @return
*
* @since 1.7
*/


add_filter( 'wpv_filter_adjust_view_layout_settings_for_import', '_wpv_adjust_view_layout_settings_for_import', 10, 2 );

function _wpv_adjust_view_layout_settings_for_import( $view_layout_settings = array(), $view_post_array = array() ) {
	if ( isset( $view_layout_settings['fields'] ) ) {
		foreach( $view_layout_settings['fields'] as $key => $value ) {
			if ( substr( $key, 0, 5 ) == 'name_' ) {
				if ( ! isset( $view_layout_settings['fields'][ 'suffix_' + substr( $key, 5 ) ] ) ) {
					$view_layout_settings['fields'][ 'suffix_' . substr( $key, 5 ) ] = '';
				}
				if ( ! isset( $view_layout_settings['fields'][ 'prefix_' + substr( $key, 5 ) ] ) ) {
					$view_layout_settings['fields'][ 'prefix_' . substr( $key, 5 ) ] = '';
				}
			}
		}
	}
	// Fix the not-well-formatted layout_settings[real_fields] array after exporting and XML-to-array conversion
	// It pushes the data in a one-level-deep array when it should be just an indexed array
	if ( 
		isset( $view_layout_settings['real_fields'] ) 
		&& isset( $view_layout_settings['real_fields']['real_fields'] ) 
	) {
		$trans = $view_layout_settings['real_fields']['real_fields'];
		$view_layout_settings['real_fields'] = $trans;
	}
	return $view_layout_settings;
}

/**
* _wpv_adjust_view_layout_templates_for_import
*
* Adjust values for several View layout settings related to CT
*
* @param (array) $view_layout_settings
* @param (array) $view_post_array
*
* @return
*
* @since 1.7
*/


add_filter( 'wpv_filter_adjust_view_layout_settings_for_import', '_wpv_adjust_view_layout_templates_for_import', 20, 2 );

function _wpv_adjust_view_layout_templates_for_import( $view_layout_settings = array(), $view_post_array = array() ) {
	global $wpdb;
	// Fix the export/import flow for the Templates attached to a View
	if ( isset( $view_layout_settings['included_ct_ids'] ) ) {
		$templates_names = explode( '#SEPARATOR#', $view_layout_settings['included_ct_ids'] );
		$templates_names = array_map( 'sanitize_text_field', $templates_names );
		$template_import = array();
		$values_to_prepare = array();
		$query_limit = count( $templates_names );
		if ( $query_limit > 0 ) {
			$templates_names_placeholders = array_fill( 0, $query_limit, '%s' );
			$templates_names_for_query = implode( ",", $templates_names_placeholders );
			foreach ( $templates_names as $templates_names_item ) {
				$values_to_prepare[] = $templates_names_item;
			}			
			$values_to_prepare[] = $query_limit;
			$new_templates_ids = $wpdb->get_col( 
				$wpdb->prepare(
					"SELECT ID FROM {$wpdb->posts} 
					WHERE post_title IN ( " . $templates_names_for_query . " ) 
					AND post_type = 'view-template' 
					LIMIT %d",
					$values_to_prepare
				)
			);
			if ( ! empty( $new_templates_ids ) ) {
				$view_layout_settings['included_ct_ids'] = implode( ',', $new_templates_ids );
			} else {
				unset( $view_layout_settings['included_ct_ids'] );
			}
		} else {
			unset( $view_layout_settings['included_ct_ids'] );
		}
	}
	return $view_layout_settings;
}

/**
* _wpv_adjust_view_loop_template_for_import
* 
* Replaces the loop Template post_title with its ID during import
*
* @param (int) $value The loop Template post_title, if any
* @param (array) $view_post_array The View post as an array
* @param (string) $meta_key The postmeta key to check against _view_loop_template
* @param (int) $new_view_id The imported View ID
*
* @return (string)
*
* @since 1.7
*/

add_filter( 'wpv_filter_adjust_view_extra_fields_for_import', '_wpv_adjust_view_loop_template_for_import', 10, 4 );

function _wpv_adjust_view_loop_template_for_import( $value, $view_post_array, $meta_key, $new_view_id ) {
	if (
		$meta_key == '_view_loop_template'
		&& $value != ''
	) {
		global $wpdb;
		$loop_template_id = $wpdb->get_var( 
			$wpdb->prepare(
				"SELECT ID FROM {$wpdb->posts} WHERE post_title = %s AND post_type = 'view-template' LIMIT 1",
				$value
			)
		);
		if ( $loop_template_id ) {
			$value = $loop_template_id;
			update_post_meta( $loop_template_id, '_view_loop_id', $new_view_id );
		} else {
			$value = '';
		}
	}
	return $value;
}