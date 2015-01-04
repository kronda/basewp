<?php
//class
if ( file_exists( WPV_PATH_EMBEDDED . '/common/visual-editor/editor-addon-generic.class.php') && !class_exists( 'Editor_addon_parametric' )  ) {

    require_once( WPV_PATH_EMBEDDED . '/common/visual-editor/editor-addon-generic.class.php' );

    class Editor_addon_parametric extends Editor_addon_generic
    {

    	private static $is_localized = false;
    	private $view_id = null;
		public $media_button_class = null;
		public static $prm_db_fields = array('filter_controls_field_name', 'filter_controls_mode', 'filter_controls_label', 'filter_controls_type', 'filter_controls_values', 'filter_controls_enable', 'filter_controls_param');
		//FIXME: part of the fix for retrocompatibility 2 url_params
		private static $tmp_settings = null;

    	public function __construct($name, $button_text, $plugin_js_url, $media_button_image = '', $print_button = false, $media_button_class = '' )
    	{
	    	parent::__construct($name, $button_text, $plugin_js_url,$media_button_image, $print_button);

            $this->view_id = isset( $_GET['view_id'] ) ? $_GET['view_id'] : get_query_var('view_id');
            $this->media_button_class = $media_button_class;

            /*Data Store Call*/
            add_action('wp_ajax_set_' . $this->name, array( $this, 'send_data_to_parametric_form'));

            add_action('wp_ajax_get_' . $this->name, array( $this, 'get_data_from_parametric_form'));

			add_action('wp_ajax_create_parametric_dialog', array($this, 'create_parametric_dialog') );
			
			add_action('wp_ajax_validate_post_relationship_tree', array($this, 'validate_post_relationship_tree') );

	    	add_action('admin_head', array($this, 'init') );
			
			add_action('wp_ajax_wpv_suggest_auto_fill_default', array( $this, 'wpv_suggest_auto_fill_default' ) );
			add_action('wp_ajax_nopriv_wpv_suggest_auto_fill_default', array( $this, 'wpv_suggest_auto_fill_default' ) );

    	}
		
		function wpv_suggest_auto_fill_default() {
			$user = esc_sql(like_escape($_REQUEST['q']));
			$field = isset( $_REQUEST['field'] ) ? esc_sql( $_REQUEST['field'] ) : '';
			if ( !empty( $field ) ) {
				$needs_db_query = true;
				$input_type = isset( $_REQUEST['type'] ) ? esc_sql( $_REQUEST['type'] ) : '';
				$nice_name = explode('wpcf-', $field);
				$id = ( isset($nice_name[1] ) ) ? $nice_name[1] : $field;
				$types_options = get_option( 'wpcf-fields', array() );
				if( $types_options && !empty( $types_options ) && isset( $types_options[$id] ) && is_array( $types_options[$id] ) ) {
					$field_options = $types_options[$id];
					$field_real_type = isset( $field_options['type'] ) ? $field_options['type'] : '';
					if ( isset( $field_options['data']['options'] ) ) {
						if ( $input_type == 'select' ) {
							$field_lowercase = isset( $field_options['name'] ) ? strtolower( $field_options['name'] ) : $id;
							echo sprintf( __( 'Select one %s', 'wpv-views' ), $field_lowercase ) . "\n";
							echo sprintf( __( 'Any %s', 'wpv-views' ), $field_lowercase ) . "\n";
						}
						switch ( $field_real_type ) {
							case 'checkboxes':
								foreach ( $field_options['data']['options'] as $key => $option ) {
									if ( isset( $option['display'] ) && $option['display'] == 'value' ) {
										$title = isset( $option['display_value_selected'] ) ? $option['display_value_selected'] : $option['title'];
										$title = $option['title'];
									} else {
										$title = $option['title'];
									}
									echo $title . "\n";
									$needs_db_query = false;
								}
								break;
							case 'select':
								if ( isset( $field_options['data']['options']['default'] ) ) {
									unset($field_options['data']['options']['default']);
								}
								foreach ( $field_options['data']['options'] as $key => $option ) {
									$title = isset( $option['title'] ) ? $option['title'] : $option['value'];
									echo $title . "\n";
									$needs_db_query = false;
								}
								break;
							default:
								if ( isset( $field_options['data']['options']['default'] ) ) {
									unset($field_options['data']['options']['default']);
								}
								$display_option = isset( $field_options['data']['display'] ) ? $field_options['data']['display'] : 'db';
								foreach ( $field_options['data']['options'] as $key => $option ) {
									if ( $display_option == 'value' ) {
										$title = isset( $option['display_value'] ) ? $option['display_value'] : $option['title'];
									} else {
										$title = $option['title'];
									}
									echo $title . "\n";
									$needs_db_query = false;
								}
								break;
						}
					}
				}
				if ( $needs_db_query ) {
					if ( $input_type == 'select' ) {
						echo sprintf( __( 'Select one %s', 'wpv-views' ), $field ) . "\n";
						echo sprintf( __( 'Any %s', 'wpv-views' ), $field ) . "\n";
					}
					global $wpdb;
					$sql = "SELECT DISTINCT meta_value FROM {$wpdb->postmeta} WHERE meta_key = '$field' ORDER BY meta_value LIMIT 0, 20";
					$results = $wpdb->get_results( $sql );
					foreach ( $results as $row ) {
						echo $row->meta_value . "\n";
					}
				}
			}
			die();
		}
    	
    	public function validate_post_relationship_tree() {
			if (! wp_verify_nonce($_POST['wpnonce'], 'wpv_parametric_validate_post_relationship_tree') ) die("Security check");
			
			$return = '';
			if ( $_POST['local_tree'] == 'tree' ) {
				$return = 'OK';
			} else {
				$view_settings = get_post_meta( $_POST['id'], '_wpv_settings', true );
				$returned_post_types = $view_settings['post_type'];
				$multi_post_relations = wpv_recursive_post_hierarchy( $returned_post_types );
				$flatten_post_relations = wpv_recursive_flatten_post_relationships( $multi_post_relations );
				if ( strlen( $flatten_post_relations ) > 0 ) {
					$current_used_tree = explode( ',', $_POST['local_tree'] );
					$relations_tree = wpv_get_all_post_relationship_options( $flatten_post_relations );
					$trees_are_valid = true;
					foreach ( $current_used_tree as $current_tree ) {
						if ( !in_array( $current_tree, $relations_tree ) ) {
							$trees_are_valid = false;
						}
					}
					if ( $trees_are_valid ) {
						$return = 'OK';
					} else {
						$return = __('Types ancestors tree not valid.', 'wpv-views');
						$return .= ' ' . __('Please follow the tip hint below. ', 'wpv-views');
					//	$return .= '<ul><li><code>' . implode( '</code></li><li><code>', $relations_tree ) .'</code></li></ul>';
					}
				} else {
					$return = __('The post types selected in this View do not have Types ancestors', 'wpv-views');
				}
			}
			echo $return;
			die();
    	}

		public function create_parametric_dialog()
		{
			if( $_POST && wp_verify_nonce( $_POST['wpv_parametric_create_dialog_nonce'], 'wpv_parametric_create_dialog_nonce' ) )
			{
				include WPV_PATH . '/inc/redesign/templates/wpv-parametric-form.tpl.php';
				die();
			}
			else
			{
				echo __( sprintf('There are nonce problems. Template could not be loaded %s', __METHOD__), 'wpv-views' );
			}
		}

    	/**
    	 * init function.
    	 *
    	 * @access public
    	 * @return void
    	 */
    	public function init()
    	{
    		global $pagenow;
    		//do your stuff only in the views edit page
    		if( $pagenow == 'admin.php' && isset( $_GET['page'] ) && $_GET['page'] == 'views-editor'  )
    		{
	    		$this->initStatics(  );
	    		//append the button
	    		add_filter('wpv_meta_html_add_form_button_new', array($this, 'add_form_button') );
	    		//this is useless for me I added it only to try to debug Cred bug
	    		add_short_codes_to_js( array('post'), $this );
    		}
	    }


    	/**
    	 * getTaxonomiesFromPostTypes function.
    	 *
    	 * @access private
    	 * @param array $object_type
    	 * @param string $out
    	 * @return array
    	 */
    	private function getTaxonomiesFromPostTypes( $object_types, $out = 'objects' )
    	{
    		return get_object_taxonomies( $object_types, $out );
    	}


    	/**
    	 * getPostTypesTaxonomies function.
    	 *
    	 * @access public
    	 * @param mixed $object_types
    	 * @param string $out
    	 * @return array
    	 */
    	public function getPostTypesTaxonomies( $object_types, $out = 'objects' )
    	{
	    	if( is_array( $object_types ) )
	    	{
	    		$taxonomies = $this->getTaxonomiesFromPostTypes( $object_types  );
	
	    		//Clean up the array from values we do not want
	    		$exclude = array();
			$exclude = apply_filters( 'wpv_admin_exclude_tax_slugs', $exclude );
			foreach ($taxonomies as $category_slug => $category) {
				if ( in_array($category_slug, $exclude) || !$category->show_ui ) {
					unset( $taxonomies[$category_slug] );
				}
			}
	    		
	    	//	$excluded = self::wpv_filter_excludes( $this->view_id, $taxonomies );
	    		
	    	//	$exclude = array_merge($exclude, $excluded);
	    		
	    	//	$newArr = array_diff($taxonomies, $exclude);
				
		    	return  $taxonomies;

	    	}
	    	else
	    	{

		    	return array( 'error' => __( sprintf("Parameter should be an array %s",  __METHOD__ ), 'wpv-views') );
	    	}
    	}
    	
    	/**
    	 * wpv_filter_excludes function.
    	 * 
    	 * @access public
    	 * @static
    	 * @param integer $view_id 
    	 * @param array $fields
    	 * @param string $type (default: 'taxonomy', 'custom-field') 
    	 * @return array $exclude
    	 */
    	public static function wpv_filter_excludes( $view_id, $fields, $type = 'taxonomy' )
    	{
    		$exclude = array();
    		
    		if( is_array($fields) && $view_id )
    		{
	    		$settings = get_post_meta( $view_id, '_wpv_settings', true );
	    	
		    	$settings = $settings ? $settings : array();
		    	
		    	$exclude = array();
		    	
		    	$postfix = $type == 'taxonomy' ? "-attribute-url" : "_value";
		    	
		    	foreach( $fields as $field )
		    	{
			    	if( array_key_exists( "{$type}-{$field}{$postfix}", $settings ) ) 
			    	{
				    	array_push($exclude, $field);
			    	}
		    	}
    		}
	    	
	    	return $exclude;
    	}
    	
    	/**
    	 * get_postmetakeys_by_post_type function.
    	 *
    	 * @access private
    	 * @param mixed $type
    	 * @return array
    	 */
    	private function get_postmetakeys_by_post_type( $type )
    	{ // TODO maybe we need to set a limit here, carefull!!
    		if( !$type ) return array('error' => __( sprintf('No types to query...make sure you made a selection %s',  __METHOD__), 'wpv-views') );

	    	global $wpdb;

	    	$arr = array();

	    	//You loop through wp_postmeta by post_id of a given post type, so give keys only once
		    $results = $wpdb->get_results($wpdb->prepare(
		        "SELECT DISTINCT meta_key FROM {$wpdb->postmeta} WHERE post_id IN
		        (SELECT ID FROM {$wpdb->posts} WHERE post_type = %s)", $type
		    ) );

			
		    foreach( $results as $res )
		    {
		    	// push only if it is not there, we want to be safe
		    	if( !in_array( $res->meta_key, $arr ) ) array_push( $arr, $res->meta_key );
		    }

		    return $arr;
    	}

    	/**
    	 * get_meta_keys_by_post_types function.
    	 *
    	 * @access public
    	 * @param array $types
    	 * @return array
    	 */
    	public function get_meta_keys_by_post_types( $types )
    	{
    		$metas = array();

    		if( is_array( $types ) )
    		{
	    		foreach( $types as $type )
	    		{
	    			array_push( $metas, $this->get_postmetakeys_by_post_type( $type ) );
	    		}
	    		
	    		$ret = $this->flattenArray($metas);
    		}
    		else
    		{
	    		$ret = array( 'error' => __( sprintf("Argument should be an array. %s",  __METHOD__), 'wpv-views' ) );
    		}
			
    		return $ret;

    	}

    	/**
    	 * flattenArray function.
    	 *
    	 * @access private
    	 * @param array $array
    	 * @return array $ret_array
    	 */
    	private function flattenArray( $array ){
			  $ret_array = array();

			  if( is_array($array) )
			  {
				  foreach(new RecursiveIteratorIterator(new RecursiveArrayIterator($array)) as $value)
				  {
			     	$ret_array[] = $value;
			      }
			  }
			  else
			  {
				  $ret_array = array('error' => __( sprintf('Argument should be an array %s', __METHOD__), 'wpv-views') );
			  }

			  return $ret_array;
		}

		/**
		 * getPostTypesMetas function.
		 *
		 * @access public
		 * @param mixed $object_types
		 * @param bool $include_hidden (default: false)
		 * @return array
		 */
		public function getPostTypesMetas( $object_types, $include_hidden = false )
		{
			global $WP_Views;

			if( !is_array($object_types) ||  empty( $object_types ) ) return array('error' => __( sprintf('Parameter should be a not empty array %s', __METHOD__), 'wpv-views' )  );


			$metaKeysAll = $this->get_meta_keys_by_post_types( $object_types );

			if( isset( $metaKeysAll['error'] ) ) return array('error' => $metaKeysAll['error'] );

			$ret = $metaKeysAll;
			
		//	$ret = array_diff( $ret, self::wpv_filter_excludes( $this->view_id, $ret, $type = 'custom-field' ) );
			
			if (!$include_hidden) {

				$options = $WP_Views->get_options();
				if (isset($options['wpv_show_hidden_fields'])) {

					$include_these_hidden = explode(',', $options['wpv_show_hidden_fields']);
				} else {

					$include_these_hidden = array();
				}
				
				// exclude hidden fields (starting with an underscore)
				foreach ($ret as $index => $field) {
					if (strpos($field, '_') === 0) {
						if (!in_array($field, $include_these_hidden)) {
							unset($ret[$index]);
						}
					}
				}



				if ( $ret )
				{
                	natcasesort($ret);
                }
			}

			//throw away keys if any and make sure we do not have duplicates: we do no trust you.
			return array_values( array_unique( $ret) );

		}

    	/**
    	 * getCustomFieldProperties function.
    	 *
    	 * @access private
    	 * @param mixed $field
    	 * @return array
    	 */
    	private function getCustomFieldProperties( $field_slug = '' )
    	{
    		$type = 'CHAR'; $input = 'textfield'; $name = ''; $relation = 'AND'; $custom = ''; $id = ''; $is_types = false;
			$group = 'Custom fields';

    	//	if( !function_exists('types_get_field_type') || !$field_slug ) return $type;
    	// Note: this was making the select not being populated when Types was not active. Of course. Do not know for how long...

    		$nice_name = explode('wpcf-', $field_slug);

    		$search = ( isset($nice_name[1] ) ) ? $nice_name[1] : $field_slug;
			$can_force_zero = false;

    		$options = get_option( 'wpcf-fields', array() );
		
	    		foreach( $options as $key => $value )
	    		{
					if( $key == $search ){
						if( function_exists('types_get_field_type') )
						{
							$type = types_get_field_type( $value['type'] );
						}
	    				$input = $value['type'];
						if ( $input == 'checkbox' && $value['data']['save_empty'] == 'yes' ) {
							$can_force_zero = true;
						}
	    				$name = $value['name'];
	    				$relation = isset( $value['data']['conditional_display']['relation'] ) ? $value['data']['conditional_display']['relation'] : $relation;
	    				$custom = isset( $value['data']['conditional_display']['custom'] ) ? $value['data']['conditional_display']['custom'] : $custom;
	    				$id = $value['id'];
					$g = '';
					if( function_exists('wpcf_admin_fields_get_groups_by_field') )
					{
						foreach( wpcf_admin_fields_get_groups_by_field( $key ) as $gs )
						{
							$g = $gs['name'];
							
						}
					}
					$group = $g ? $g : "Custom fields";
					$name = $g ? $name : $field_slug;
					$is_types = $g ? true : false;
					$name = $g ? $name : $field_slug;
					$id = $g ? $id : $field_slug;
    				}
    			}
			
			
    		return array(
    					'data_type' => $type,
    					'type' => $input,
    					'name' => $name ? $name : $search,
    					'relation' => $relation,
    					'custom' => $custom,
    					'id' => $id ? $id : $search,
						'group' => $group,
    					'is_types' => $is_types, ///is a types field?
						'can_force_zero' => $can_force_zero
    		);

    	}

    	/**
    	 * getCustomFieldsDataTypesArray function.
    	 *
    	 * @access private
    	 * @param array $fields
    	 * @return array
    	 */
    	private function getCustomFieldsPropertiesArray($fields)
    	{
	    	if( is_array( $fields ) )
	    	{
	    		$ret = array();

		    	foreach( $fields as $field )
		    	{
		    		if( !array_key_exists( $field, $ret ) )
		    		 $ret[$field] = $this->getCustomFieldProperties($field);
		    	}
	    	}
	    	else
	    	{
		    	$ret = array('error' => __( sprintf('Argument should be an array %s', __METHOD__), 'wpv-views') );
	    	}

	    	return  $ret;
    	}
    	
    	private function wpv_get_other_parametric_filters( $settings ) {
			$ret = array();
			$returned_post_types = $settings['post_type'];
			$existing_real_parents = array_keys( wpv_recursive_post_hierarchy( $returned_post_types, 4 ) );
			if ( !empty( $existing_real_parents ) ) {
				$ret['relationship'] = 'relationship';
			}
			return $ret;
    	}

    	/**
    	 * formGenericDataStore function.
    	 *
    	 * @access public
    	 * @param array $data (default: array( ))
    	 * @return mixed json
    	 */
    	private function formGenericDataStore( $data = array( ) )
    	{
    		if( !is_array($data) ) return json_encode( array( 'error' => __( sprintf('Parameter should be an array %s', __METHOD__), 'wpv-views' ) ) );
	    	return json_encode( array( 'Data' => $data ) );
    	}
		public static function get_param_index_cmp( $settings, $field, $toCheck = 'filter_controls_param', $val = false )
		{
			$len = isset($settings[$toCheck]) ? count( $settings[$toCheck] ) : 0;
			$index = -1; 
			
			$keys = array_keys( $field );
			
			$key = $keys[0] == 'type' ? $keys[1] : $keys[0];
						
			$value = $val ? $val : self::get_field_url_parm( $settings, $field[$key] );
			
			if( empty($value) ) return -1;
					 	
			for( $i = 0; $i < $len; $i++ ) 
			{
				if( strcmp( $settings[$toCheck][$i], $value ) === 0 ){
					$index = $i;
				} 
			}
			
			//FIXME: this is a shit patch for retro compatibility. In case we have BETWEEN and 2 fields in DB instead of one as: url_param_one, url_param_two
			$value = explode( ',', $value );
			
			if( isset( $value[1] ) )
			{
				for( $i = 0; $i < $len; $i++ ) 
				{
					if( strcmp( $settings[$toCheck][$i], $value[0] ) === 0 ){
						$index = $i;
						$settings[$toCheck][$i] = $value[0] . ',' . $settings[$toCheck][$i+1];
					} 
				}
			}
			//TODO:close the patch above!!!!
			
			self::$tmp_settings = $settings;
			
			return $index;
		}
		
		public static function get_param_index( $settings, $field, $toCheck = 'filter_controls_param', $param = 'url_param' )
		{
			$len = count( $settings['filter_controls_param'] );
			$index = -1;
			
			for( $i = 0; $i < $len; $i++ ) 
			{
			
				if( strpos( $settings['filter_controls_param'][$i], $field[$param] ) !== false ){
					$index = $i;
				} 
			}
			
			self::$tmp_settings = $settings;
			
			return $index;
		}
		
		public static function get_field_url_parm( $settings, $param  )
		{
			$index = false;
			if ( isset( $settings['taxonomy-'.$param.'-attribute-url'] ) ) {
				$index = $settings['taxonomy-'.$param.'-attribute-url'];
			}
			elseif ( isset( $settings['custom-field-'.$param.'_value'] ) ) {
				$pattern = '/URL_PARAM\(([^(]*?)\)/siU';
				preg_match_all($pattern, $settings['custom-field-'.$param.'_value'] , $matches, PREG_SET_ORDER);
				if( isset($matches[0][1]) ) {
					$index = $matches[0][1];
				}
				if( isset($matches[1][1]) ) {
					$index .= ', '.$matches[1][1];
				}
			} else if ( isset( $settings['post_relationship_url_parameter'] ) && $param == 'relationship' ) {
				$index = $settings['post_relationship_url_parameter'];
			}
			return $index;
		}
		
		 /**
		  * buildRawFieldToEdit function
		  * @param array $field 
		  * @param array $settings
		  * @return array
		  * @author Riccardo Strobbia
		 **/
		private function buildRawFieldToEdit( $field, $settings = array() )
		{
			$opts = $settings ? $settings : get_post_meta( $this->view_id, '_wpv_settings', true );
			
			$ret = array();		
			
			$index = self::get_param_index_cmp($settings, $field);
			//print "index first method " . $index ."\n";
			//FIXME: fall back for retrocompatibility	
			if( $index === -1 ) {
			//	$index = self::get_param_index($settings, $field);
			}
			
			$index = self::get_param_index($settings, $field);
			
			if( $index > -1 )
			{	
				$field['url_param'] = self::$tmp_settings['filter_controls_param'][$index];
				$field['can_force_zero'] = false;
				self::$tmp_settings = null;
				
				if( isset( $field['taxonomy'] ) )
				{
					$name = $settings['filter_controls_field_name'][$index];
					$id = $name;
					$id = $settings['filter_controls_label'][$index];
					$ret['is_types'] = false;
					$ret['group'] = 'taxonomy';
					///do processing for taxes and return
				}
				else if( isset( $field['field'] ) )
				{
					$g = '';
					$name = $field['field'];
					$nice_name = explode('wpcf-', $name);
					$id = ( isset($nice_name[1] ) ) ? $nice_name[1] : $name;
					$field_options = wpcf_admin_fields_get_field( $id );
					if( function_exists('wpcf_admin_fields_get_groups_by_field') )
					{
						foreach( wpcf_admin_fields_get_groups_by_field( $id ) as $gs )
						{
							$g = $gs['name'];
						}
					}
					$ret['group'] = $g ? $g : "Custom fields";
					$name = $g ? $name : $field['field'];
					$ret['is_types'] = $g ? true : false;
					if ( !empty( $field_options ) && $field_options['type'] == 'checkbox' && $field_options['data']['save_empty'] == 'yes' ) {
						$ret['can_force_zero'] = true;
					}
					$id = $g ? $id : $field['field'];
				}
				else if( isset( $field['relationship'] ) )
				{
					$name = 'relationship';
					$id = __('Post relationship', 'wpv-views');
					$id = $settings['filter_controls_label'][$index];
					$ret['is_types'] = false;
					$ret['group'] = 'basic_filters';
					$ret['kind'] = 'relationship';
					$ret['basic_filter_type'] = 'relationship';
				}
				else
				{								
					$name = $settings['filter_controls_field_name'][$index];
					$id = $name;
					$ret['is_types'] = false;
					$ret['group'] = 'Custom fields';		
				}
			//	print "\n'custom-field-'.$name.'_value'\n";
			//	print_r( $settings['custom-field-'.$name.'_value'] );

				$ret['field'] = $name;
				$ret['id'] = $id;
				
				if( isset( $field['taxonomy'] ) )
				{
					$ret['kind'] = 'taxonomy';
					$ret['group'] = $ret['kind'];
					$ret['compare'] = isset( $settings['taxonomy-' . $name . '-attribute-operator'] ) ? $settings['taxonomy-' . $name . '-attribute-operator'] : 'IN';
				//	$ret['hide_empty'] = isset( $field['hide_empty'] ) ? $field['hide_empty'] : 'false';
				}
				else if( isset( $field['relationship'] ) )
				{
					$ret['group'] = 'basic_filters';
				}
				else
				{
					$ret['compare'] = isset( $settings['custom-field-'.$name.'_compare'] ) ? $settings['custom-field-'.$name.'_compare'] : '=';
					$ret['data_type'] = isset( $settings['custom-field-'.$name.'_type'] ) ? $settings['custom-field-'.$name.'_type'] : 'CHAR';
					$ret['relation'] = isset( $settings['custom-field-'.$name.'_relationship'] ) ? $settings['custom-field-'.$name.'_relationship'] : 'AND';
					$ret['kind'] = 'field';
				}
				$ret['name'] = $ret['is_types'] ? $settings['filter_controls_label'][$index] : $id;
				$ret['type'] = $settings['filter_controls_type'][$index];
				$ret['values'] = $settings['filter_controls_values'][$index];
				$ret['enabled'] = $settings['filter_controls_enable'][$index];
				$ret['index'] = $index;
			/*	$ret_aux = $settings['filter_controls_values'][$index];
				$ret['taxonomy_order'] = isset( $ret_aux['taxonomy_order'] ) ? $ret_aux['taxonomy_order'] : 'ASC';
				$ret['taxonomy_orderby'] = isset( $ret_aux['taxonomy_orderby'] ) ? $ret_aux['taxonomy_orderby'] : 'name';
				$ret['hide_empty'] = isset( $ret_aux['hide_empty'] ) ? $ret_aux['hide_empty'] : 'false';*/
				//implement for tax
				
				return array_merge( $field, $ret ) ;
			}
			else
			{
				return array( 'error', __( sprintf('There is something wrong url parameter is missing %s',  __METHOD__), 'wpv-views' ) );
			}	
		}

    	/**
    	 * send_data_to_parametric_form function.
    	 *
    	 * @access public
    	 * @return void
    	 */
    	public function send_data_to_parametric_form( )
    	{
    		if( $_POST && wp_verify_nonce( $_POST['wpv_parametric_create_nonce'], 'wpv_parametric_create_nonce' ) )
    		{
				global $WP_Views;
    			$this->view_id = $_POST['view_id'];
    			$send = array();
				$edit = false;

				$settings = get_post_meta( $this->view_id, '_wpv_settings', true );
				
				if( isset( $_POST['edit_field'] ) )
				{
					if( isset($_POST['edit_field']['url_param'] ) )	
					{
						$edit = $this->buildRawFieldToEdit( $_POST['edit_field'], $settings );
					}
					else
					{
						$edit = array();
						$edit['error'] = __( sprintf('There is something wrong url parameter is missing %s', __METHOD__), 'wpv-views' );
 					}
 					if ( isset( $edit['error'] ) ) {
						$send['error'] = $edit['error'];
 					} else {
						$send['edit_field'] = $edit;
 					}
					
				} else {
				
					$post_types = $_POST['post_types'] ? explode(',', $_POST['post_types'] ) : array();
					$metas = $WP_Views->get_meta_keys();
					$taxes = $this->getPostTypesTaxonomies( $post_types );
					$types = $this->getCustomFieldsPropertiesArray( $metas );
					$other_filters = $this->wpv_get_other_parametric_filters( $settings );

					if( isset( $metas['error'] ) )
					{
						$send['error'] = $metas['error'];
					}
					elseif( isset( $taxes['error'] ) )
					{
						$send['error'] = $taxes['error'];
					}
					elseif( isset( $types['error'] ) )
					{
						$send['error'] = $types['error'];
					}
					else
					{
						$sendm = $this->buildJsonArray( array($metas, $types ), $settings );
						$sendt = $this->buildJsonArray( array($taxes, $types ), $settings );
						if ( empty( $other_filters ) ) {
							$sendof = false;
						} else {
							$sendof = $this->buildJsonArray( array($other_filters, $types ), $settings );
						}

						if( isset( $sendm['error'] )  )
						{
							$send['error'] = $sendm['error'];
						}
						else if( isset( $sendt['error'] ) )
						{
							$send['error'] = $sendt['error'];
						}
						else
						{
							
							if( $sendof )
							{
								$send['basic_filters'] = $sendof;
							}
							if( $sendt )
							{
								$send['taxonomy'] = $sendt;
							}
							if( $sendm )
							{
								foreach( $sendm as $key => $s)
								{
									$send[ $s['group'] ][ $key ] = $s;
								}
							}
						}

					}
    			
    			}
    			
    			if ( !isset( $send['error'] ) ) {
					$send['view_id'] = $this->view_id;
					$send['settings'] = $settings;
    			}

    		}
    		else
    		{
	    		$send = array( 'error' =>  __( sprintf('Nonce problem: apparently we do not know from where the request comes from. %s', __METHOD__ ), 'wpv-views') );
    		}


    		echo $this->formGenericDataStore( $send );

    		die();

    	}
		
		
		
    	private function buildJsonArray( $args = array(), $settings )
    	{
    		if( !is_array( $args ) ) return array( "error" => __( sprintf("Not able to build data %s", __METHOD__), 'wpv-views') );

	    	$field = $args[0]; $types = $args[1]; $ret = array();
	
	    	foreach( $field as $f )
	    	{
				if( gettype($f) == 'object' )
				{
					$ret[$f->name] = array( 'data_type' => 'CHAR', 'type' => 'textfield', 'name' => $f->labels->name, 'id' => $f->name, 'custom' => '', 'relation' => 'AND' );
				}
		    	elseif( isset( $args[1][$f] ) )
		    	{
			    	$ret[$f] = $args[1][$f];
		    	}
		    	elseif( $f == 'relationship' )
		    	{
					$ret[$f] = array( 'basic_filter_type' => $f, 'data_type' => 'CHAR', 'type' => 'select', 'name' => __('Post relationship', 'wpv-views'), 'id' => 'wpv-pr-child-of', 'custom' => '', 'relation' => 'AND' );
		    	}
		    	else
		    	{
			    	$ret[$f] = array( 'data_type' => 'CHAR', 'type' => 'textfield', 'name' => $f, 'id' => $f, 'custom' => '', 'relation' => 'AND' );
		    	}
		    	
		    	$key = $f;
		    	if ( gettype($f) == 'object' ) {
					$key = $f->name;
		    	}
			
				
				$param = self::get_field_url_parm( $settings, $key );
				
			//	print "PARAM " . $param;
				
				if( $param )
				{
				//	print "ENTERS PARAM " . $param;
					$ret[ $key ]['control'] = $param;
					
					if( gettype($f) != 'object' && isset( $settings['custom-field-'.$f.'_compare'] ) )
					{
						$ret[$f]['compare'] = $settings['custom-field-'.$f.'_compare'];
					}
					
					if( gettype($f) != 'object' && isset( $settings['custom-field-'.$f.'_type'] ) )
					{
						$ret[$f]['data_type'] = $settings['custom-field-'.$f.'_type'];
					}
					
					if( isset( $settings['filter_controls_param'] ) && sizeof( $settings['filter_controls_param'] ) > 0 )
					{
						$ret[ $key ]['index'] = array_search($param, $settings['filter_controls_param'] );
					}
					
				}
	    	}
	    	return $ret;
    	}
    	
    	public static function wpv_filter_custom_field_delete( $view_id, $field_name, $index, $settings ) {
				$view_array = $settings ? $settings : get_post_meta($view_id, '_wpv_settings', true);
				$field = $field_name;
				
				
				$to_delete = array(
					'custom-field-' . $field . '_compare',
					'custom-field-' . $field . '_type',
					'custom-field-' . $field . '_value',
					'custom-field-' . $field . '_relationship'
				);
				
				foreach ($to_delete as $slug) {
                    if ( isset( $view_array[$slug] ) ) {
                        unset( $view_array[$slug] );
                    }
                }
				
				foreach( self::$prm_db_fields as $dbf )
				{
					array_splice($view_array[$dbf], $index, 1);
				}
				
				return $view_array;
		}
		
		public static function wpv_filter_tax_delete( $view_id, $tax_name, $index, $settings ) {
				$view_array = $settings ? $settings : get_post_meta($view_id, '_wpv_settings', true);
				
				$to_delete = array(
					'tax_'.$tax_name.'_relationship' ,
					'taxonomy-'.$tax_name.'-attribute-url',
				//	'taxonomy-'.$tax_name.'-attribute-url-format',
				);
				
				foreach ($to_delete as $slug) {
                    if ( isset( $view_array[$slug] ) ) {
                        unset( $view_array[$slug] );
                    }
                }
				
				foreach( self::$prm_db_fields as $dbf )
				{
					array_splice($view_array[$dbf], $index, 1);
				}
				
				return $view_array;
		}
		
		public static function wpv_filter_rel_delete( $view_id, $index, $settings ) {
				$view_array = $settings ? $settings : get_post_meta($view_id, '_wpv_settings', true);
				
				$to_delete = array(
					'post_relationship_mode',
					'post_relationship_shortcode_attribute',
					'post_relationship_url_parameter',
					'post_relationship_id',
					'post_relationship_url_tree',
				);
				
				foreach ($to_delete as $slug) {
                    if ( isset( $view_array[$slug] ) ) {
                        unset( $view_array[$slug] );
                    }
                }
				
				foreach( self::$prm_db_fields as $dbf )
				{
					array_splice($view_array[$dbf], $index, 1);
				}
				
				return $view_array;
		}
		
		private function get_view_type()
		{
			$settings = get_post_meta($this->view_id, '_wpv_settings', true);
			
			return isset( $settings['view_purpose'] ) ? $settings['view_purpose'] : 'full';
		}
		
    	/**
    	 * get_data_from_parametric_form function.
    	 *
    	 * @access public
    	 * @return void
    	 */
	public function get_data_from_parametric_form()
	{
		if( $_POST && wp_verify_nonce( $_POST['wpv_parametric_submit_create_nonce'], 'wpv_parametric_submit_create_nonce' ) )
		{
			$this->view_id = $_POST['view_id'];


			if( !$this->view_id )
			{
				die( array( 'error' =>  __( sprintf('The view_id variable is missing we do not know what to associate the data with. %s', __METHOD__), 'wpv-views') ) );
			}
			
			$settings = get_post_meta( $this->view_id, '_wpv_settings', true );
			
			$control = $settings;
			//TODO: add delete for insert field when has index
			if( isset( $_POST['edit_field'] ) && isset( $_POST['edit_field']['field'] ) )
			{	
				$settings = self::wpv_filter_custom_field_delete( $this->view_id, $_POST['edit_field']['field'], (int) $_POST['edit_field']['index'], $settings );
			}
			else if( isset( $_POST['edit_field'] ) && isset( $_POST['edit_field']['taxonomy'] ) )
			{
				$settings = self::wpv_filter_tax_delete( $this->view_id, $_POST['edit_field']['taxonomy'], (int) $_POST['edit_field']['index'], $settings );
			}
			else if( isset( $_POST['edit_field'] ) && isset( $_POST['edit_field']['relationship'] ) )
			{
				$settings = self::wpv_filter_rel_delete( $this->view_id, (int) $_POST['edit_field']['index'], $settings );
			}
			else if( isset( $_POST['fields'] ) && isset( $_POST['fields']['index'] ) && $_POST['fields']['index'] != -1 && $_POST['fields']['kind'] == 'field' )
			{
				$settings = self::wpv_filter_custom_field_delete( $this->view_id, $_POST['fields']['field'], (int) $_POST['fields']['index'], $settings );
			}
			else if( isset( $_POST['fields'] ) && isset( $_POST['fields']['index'] ) && $_POST['fields']['index'] != -1 && $_POST['fields']['kind'] == 'taxonomy' )
			{
				$settings = self::wpv_filter_tax_delete( $this->view_id, $_POST['fields']['field'], (int) $_POST['fields']['index'], $settings );
			}
			else if( isset( $_POST['fields'] ) && isset( $_POST['fields']['index'] ) && $_POST['fields']['index'] != -1 && $_POST['fields']['kind'] == 'relationship' )
			{
				$settings = self::wpv_filter_rel_delete( $this->view_id, (int) $_POST['fields']['index'], $settings );
			}
			
			if( $_POST['fields'] && sizeof($_POST['fields']['url_param']) > 0 )
			{
				$base_string = $_POST['fields']['fieldDbName'];
				
				if(  $_POST['fields']['kind'] == 'taxonomy'  )
				{

					$mode = 'slug';
					$settings[str_replace('taxonomy-', 'tax_', $base_string).'_relationship'] = isset( $settings[$base_string.'_relationship'] ) ? $settings[$base_string.'_relationship'] : "FROM URL";
					$settings[$base_string.'-attribute-url'] = $_POST['fields']['url_param'][0]['value'];
					$settings[$base_string.'-attribute-operator'] = $_POST['fields']['compare'];
					$settings[$base_string.'-attribute-url-format'] = isset( $settings[$base_string.'-attribute-url-format'] ) ? $settings[$base_string.'-attribute-url-format'] : array('slug');
					$settings['taxonomy_relationship'] =  isset( $settings['taxonomy_relationship'] ) ? $settings['taxonomy_relationship'] : "AND";
				//	$settings['taxonomy_order'] = isset( $_POST['fields']['order'] ) ? $_POST['fields']['order'] : 'ASC';
				//	$settings['taxonomy_orderby'] = isset( $_POST['fields']['order_by'] ) ? $_POST['fields']['order_by'] : 'name';
				}
				elseif( $_POST['fields']['kind'] == 'field' )
				{
					$mode = 'cf';
					$settings[$base_string.'_compare'] = $_POST['fields']['compare'];
			
					$settings[$base_string.'_type'] = $_POST['fields']['data_type'];
					$settings[$base_string.'_value'] = sizeof( $_POST['fields']['url_param'] ) == 1 ? 'URL_PARAM('.$_POST['fields']['url_param'][0]['value'].')' : 'URL_PARAM('.$_POST['fields']['url_param'][0]['value'].'), URL_PARAM('.$_POST['fields']['url_param'][1]['value'].')';
				//	$settings[$base_string.'_relationship'] = isset( $settings[$base_string.'_custom_fields_relationship'] ) ? $settings[$base_string.'_custom_fields_relationship'] : 'OR';
					$settings['custom_fields_relationship'] = isset( $settings['custom_fields_relationship'] ) ? $settings['custom_fields_relationship'] : 'AND';

				}
				elseif( $_POST['fields']['kind'] == 'relationship' )
				{
					$mode = 'rel';
					$settings['post_relationship_mode'] = isset( $settings['post_relationship_mode'] ) ? $settings['post_relationship_mode'] : array( 'url_parameter' );
					$settings['post_relationship_id'] = 0;
					$settings['post_relationship_url_parameter'] = $_POST['fields']['url_param'][0]['value'];
					$settings['post_relationship_url_tree'] = $_POST['fields']['ancestors'];
					$settings['post_relationship_shortcode_attribute'] = isset( $settings['post_relationship_shortcode_attribute'] ) ? $settings['post_relationship_shortcode_attribute'] : 'wpvprchildof';
				}

			//	$settings['filter_controls_key'][ isset( $settings['filter_controls_key'] ) ? sizeof( $settings['filter_controls_key'] ) : 0 ] = $_POST['fields']['field'];
				$settings['filter_controls_field_name'][ isset($settings['filter_controls_field_name']) ? sizeof($settings['filter_controls_field_name'])  : 0 ] = $_POST['fields']['field'];
				$settings['filter_controls_mode'][ isset($settings['filter_controls_mode']) ? sizeof($settings['filter_controls_mode']) : 0 ] = $mode;
				$settings['filter_controls_field_name'][ isset($settings['filter_controls_field_name']) ? sizeof($settings['filter_controls_field_name']) - 1 : 0 ] = $_POST['fields']['field'];
				$settings['filter_controls_label'][ isset($settings['filter_controls_label']) ? sizeof($settings['filter_controls_label']) : 0 ] = ucfirst( str_replace('_', ' ', $_POST['fields']['name']) );
				$settings['filter_controls_type'][ isset($settings['filter_controls_type']) ? sizeof($settings['filter_controls_type']) : 0 ] = $_POST['fields']['type'];
				$values = isset( $_POST['fields']['values'] ) ? $_POST['fields']['values'] : array();
				$settings['filter_controls_values'][ isset($settings['filter_controls_values']) ? sizeof($settings['filter_controls_values']) : 0 ] = json_encode( 
					array( 
						'values' => $values, 
						'auto_fill' => $_POST['fields']['auto_fill'], 
						'auto_fill_default' =>  $_POST['fields']['auto_fill_default'], 
						'auto_fill_sort' => $_POST['fields']['auto_fill_sort'], 
						'hide_empty' => isset( $_POST['fields']['hide_empty'] ) ? $_POST['fields']['hide_empty'] : 'false', 
						'taxonomy_order' => isset( $_POST['fields']['taxonomy_order'] ) ? $_POST['fields']['taxonomy_order'] : 'ASC', 
						'taxonomy_orderby' => isset( $_POST['fields']['taxonomy_orderby'] ) ? $_POST['fields']['taxonomy_orderby'] : 'name'
					) 
				);
				$settings['filter_controls_enable'][ isset( $settings['filter_controls_enable'] ) ? sizeof( $settings['filter_controls_enable'] ) : 0 ] = true;

				$param = isset($_POST['fields']['url_param'][0]) ? $_POST['fields']['url_param'][0]['value'] : '';
				$param .= isset( $_POST['fields']['url_param'][1] ) ? ', ' . $_POST['fields']['url_param'][1]['value'] : '';

				$settings['filter_controls_param'][ isset( $settings['filter_controls_param'] ) ? sizeof( $settings['filter_controls_param'] ) : 0 ] = $param;

				$check = update_post_meta( $this->view_id, '_wpv_settings', $settings );
			//	print "\n\n" .$check . "\n\n";
			//	print_r( array_diff( $control, $settings ) );
				if( $check || sizeof( @array_diff( $control, $settings ) ) == 0 )
				{
					$send = array( 'insert' => __( 'The view has been succesfully updated', 'wpv-views') );
				}
				else
				{
					$send = array( 'error' => __( sprintf('There are problems updating the view. %s', __METHOD__), 'wpv-views') );
				}


			}
			else
			{
				$send = array( 'error' =>  __( sprintf('There are problems in the data sent by the form %s', __METHOD__), 'wpv-views') );
			}

		}
		else
		{
			$send = array( 'error' =>  __( sprintf('Nonce problem: apparently we do not know from where the request comes from. %s', __METHOD__), 'wpv-views') );
		}

		echo $this->formGenericDataStore($send);
		die();
	}


    	/**
    	 * initScripts function.
    	 *
    	 * @access public
    	 * @return void
    	 */
    	public function initStatics()
    	{
    		$this->parametric_enqueue_scripts();
    	}
		
		public static function is_wpml_active()
		{
			return function_exists("icl_object_id");
		}

    	/**
    	 * parametric_enqueue_scripts function.
    	 *
    	 * @access public
    	 * @return void
    	 */
    	private function parametric_enqueue_scripts()
    	{
		wp_register_script('knockout', WPV_URL . '/res/js/redesign/lib/knockout-2.2.1.debug.js', array(), '2.2.1');
		wp_register_script('wpv-parametric-admin-script' , WPV_URL . '/res/js/redesign/views_parametric.js', array('jquery', 'icl_editor-script', 'views-codemirror-script'), WPV_VERSION);
		
    		wp_enqueue_script('knockout');
    		wp_enqueue_script( 'wpv-parametric-admin-script');
    		
    		if( self::$is_localized )
    		wp_localize_script( 'wpv-parametric-admin-script',
    							'WPV_Parametric',
    							array(
    								'WPV_URL' =>  WPV_URL,
    								'wpv_parametric_create_nonce' => wp_create_nonce( 'wpv_parametric_create_nonce' ),
    								'wpv_parametric_submit_create_nonce' => wp_create_nonce( 'wpv_parametric_submit_create_nonce' ),
									'wpv_parametric_create_dialog_nonce' => wp_create_nonce( 'wpv_parametric_create_dialog_nonce' ),
									'wpv_view_filter_search_nonce' => wp_create_nonce( 'wpv_view_filter_search_nonce' ),
									'wpv_view_filter_search_delete_nonce' => wp_create_nonce( "wpv_view_filter_search_delete_nonce" ),
									'wpv_view_filters_add_filter_nonce' => wp_create_nonce('wpv_view_filters_add_filter_nonce'),
									'wpv_parametric_validate_post_relationship_tree' => wp_create_nonce('wpv_parametric_validate_post_relationship_tree'),
    								'view_id' => $this->view_id,
									'is_wpml_active' => self::is_wpml_active(),
									'view_purpose' => $this->get_view_type(),
    								'debug' => true,
									'make_valid_selection' => __('Please make a valid selection.', 'wpv-views'),
									'something_bad' => __("Something bad happend with shortcode building, check the console", 'wpv-views'),
									'field_mandatory' => __('The value for "Refer to this field as" is mandatory, please provide one.', 'wpv-views'),
									'relationship_tree_mandatory' => __('Please make a valid tree selection.'),
									'basic_field_mandatory' => __('This field can not be left empty', 'wpv-views'),
									'reserved_word' => __('" is a reserved word for ', 'wpv-views'),
									'avoid_conflicts' => __('Change this value to avoid conflicts', 'wpv-views'),
									'ajax_error' => __("Error: AJAX returned ", 'wpv-views'),
									'error_generic' => __("Error: ", 'wpv-views'),
									'db_insert_problem' => __("There are problems inserting your data. Check the console. ", 'wpv-views'),
									'select_post_types' => __('Please select at least one post type to fiter by.', 'wpv-views'),
									'data_loading_problem' => __('Something went wrong loading data ', 'wpv-views'),
									'model_build_problem' => __('Something went wrong while bulding model.', 'wpv-views'),
									
									'select_taxonomy_alert' => __('Select posts with taxonomy:', 'wpv-views'),
									'select_taxonomy_alert_2' => __('the same as set by the URL parameter', 'wpv-views'),
									'error_building_filter' => __("Something went wrong in building the filter ", 'wpv-views'),
									
									'taxonomy' => __('Taxonomy', 'wpv-views'),
									'basic_filters' => __('Basic filters', 'wpv-views'),
									'relationship_select_tree' => __('Select one tree', 'wpv-views'),
									
									'add_submit_shortcode_button' => __('Submit button', 'wpv-views'),
									'add_submit_button_to_shortcode_label' => __('Insert input', 'wpv-views'),
									'add_submit_button_to_shortcode_input_default' => __('Search', 'wpv-views'),
									
									'add_submit_button_to_shortcode_header' => __( 'Create a submit button for this parametric search.', 'wpv-views' ),
									
									'add_reset_shortcode_button' => __('Clear form', 'wpv-views'),
									'add_reset_shortcode_button_label' => __('Clear', 'wpv-views'),
									
									'consider_adding_label_to_button_shortcode' => __( 'Consider adding a label before inserting the button.', 'wpv-views' ),
									
									'add_spinner_shortcode_button' => __('Spinner', 'wpv-views'),
									
									'place_cursor_inside_wpv_controls' => __( 'Place cursor within the [wpv-filter-controls][/wpv-filter-controls] tags.', 'wpv-views' ),
									
									'place_cursor_inside_wpv_filter' => __( 'Place cursor within the [wpv-filter-start][wpv-filter-end] tags.', 'wpv-views' ),
									
									'place_cursor_inside_wpv_control' => __( 'Place your cursor inside [wpv-control] tags.', 'wpv-views' ),
									'place_in_wpv_control_not_wrong' => __("You should select a [wpv-control] short tag instead of ", 'wpv-views'),
									'place_cursor_inside_valid_control_shortcodes' => __('Place your cursor over a [wpv-control] or a [wpv-control-set] tag to edit it.', 'wpv-views'),
									'place_cursor_inside_wpv_control_set' => __( 'To edit this filter, place your cursor over the [wpv-control-set] tag.', 'wpv-views'),
									
									'add_search_shortcode_button' => __('Search', 'wpv-views'),
								//	'no_submit_button' => __('There is no submit button in the form just created. Use "Submit button" button to create one.', 'wpv-views'),
									'cursorInside' => __('Warning: the cursor is inside another short code, this may cause problems.', 'wpv-views'),
									'cancel' => __('Cancel', 'wpv-views'),
									'edit_filter_field' => __('Edit filter field', 'wpv-views'),
									'update_input' => __('Update input', 'wpv-views'),
									'problems_inserting_new_shortcode' => __('There are problems inserting the shortcode.', 'wpv-views'),
									'add_submit_input_label' => __('Button label:', 'wpv-views'),
									'add_submit_classname_input_label' => __('Button classname:', 'wpv-views'),
									'expand_button_expand' => __('Expand', 'wpv-views'),
									'expand_button_hide' => __('Hide', 'wpv-views'),
									'check_values_and_values_labels' => __("Please provide at least one non-empty value, you are free to live some of them empty.", 'wpv-views'),
									'ajax_callback_undefined' => __('You should define a callback for your ajax call to async load data', 'wpv-views'),
									'view_has_already_a_search' => __("This View already has a content search filter. If you insert a search control to the HTML, the existing search filter will be removed.", 'wpv-views')
     							)
    						);

    		self::$is_localized = true;
       	}


        /**
         * Adding a "V" button to the menu
         * @param string $context
         * @param string $text_area
         * @param boolean $standard_v is this a standard V button
         */
        public function add_form_button( $context, $text_area = 'textarea#content',
                $standard_v = true, $add_views = false ) {
            global $wp_version, $wplogger;

            if( defined('WPV_LOGGING_STATUS') && WPV_LOGGING_STATUS == 'debug' )
            {
	            list(, $caller) = debug_backtrace(false);
	            $this->logger->log( sprintf('Adding form buttons %s %s %s %s %s', $context, $text_area, $standard_v ? 'yes' : 'no', $add_views ? 'yes' : 'no', $caller['function'] ) );
            }



            // WP 3.3 changes ($context arg is actually a editor ID now) // TODO Not sure this is needed at all
            if ( version_compare( $wp_version, '3.1.4', '>' ) && !empty( $context ) ) {
                $text_area = $context;
            }

            // Apply filters, this should be needed to populate the parametric search popup values
            $this->items = apply_filters( 'editor_addon_items_' . $this->name,
                    $this->items );


            $addon_button = '<button class="button-secondary parametric-button-open js-code-editor-toolbar-button js-button_' . $this->name .  ' js-parametric-open-window"><i class="' . $this->media_button_class . '"></i><span class="button-label">' . $this->button_text . '</span></button>';


            // generate output content
            $out = '<li class="editor-parametric-button-wrapper js-editor-addon-button-wrapper">' . $addon_button . '</li>';

            // WP 3.3 changes // TODO Not sure this is needed at all
            if ( version_compare( $wp_version, '3.1.4', '>' ) ) {
                echo apply_filters( 'wpv_add_media_buttons', $out );
            } else {
                return apply_filters( 'wpv_add_media_buttons', $context . $out );
            }
        }

          /*

          Add the wpv_views button to the toolbar.

         */

        function wpv_mce_add_button( $buttons )
        {
            array_push( $buttons, "separator",
                    str_replace( '-', '_', $this->name ) );
            return $buttons;
        }

        /*

          Register this plugin as a mce 'addon'
          Tell the mce editor the url of the javascript file.
         */

        function wpv_mce_register( $plugin_array )
        {
            $plugin_array[str_replace( '-', '_', $this->name )] = $this->plugin_js_url;
            return $plugin_array;
        }


    }
}