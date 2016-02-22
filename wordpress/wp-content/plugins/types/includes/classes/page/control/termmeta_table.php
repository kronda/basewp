<?php

/**
 * Listing table for the Term Field Control page.
 */
final class WPCF_Page_Control_Termmeta_Table extends WPCF_Page_Listing_Table {


	const INPUT_SLUGS = 'field_definition_slugs';

	// Note that these strings are not arbitrary, they're used by custom-fields-control-form.js which is used
	// commonly for post, user and term fields control pages.
	const BULK_ACTION_ADD_TO_GROUP = 'wpcf-add-to-group-bulk';

	const BULK_ACTION_REMOVE_FROM_TO_GROUP = 'wpcf-remove-from-group-bulk';

	const BULK_ACTION_CHANGE_TYPE = 'wpcf-change-type-bulk';

	const BULK_ACTION_ACTIVATE = 'wpcf-activate-bulk';

	const BULK_ACTION_DEACTIVATE = 'wpcf-deactivate-bulk';

	const BULK_ACTION_DELETE = 'wpcf-delete-bulk';


	function __construct() {
		parent::__construct(
			array(
				'singular' => __( 'term field group', 'wpcf' ),
				'plural' => __( 'term field groups', 'wpcf' ),
				'ajax' => true
			)
		);
	}


	function get_columns() {
		$columns = array();

		if ( WPCF_Roles::user_can_create( 'term-field' ) ) {
			$columns['cb'] = '<input type="checkbox" />';
		}

		return array_merge(
			$columns,
			array(
				'name' => __( 'Term Field Name', 'wpcf' ),
				'groups' => __( 'Field Groups', 'wpcf' ),
				'slug' => __( 'Slug', 'wpcf' ),
				'field_type' => __( 'Type', 'wpcf' )
			)
		);

	}


	function get_sortable_columns() {
		return array(
			'name' => array( 'name', true ),
			'slug' => array( 'slug', false ),
			'field_type' => array( 'field_type', false )
		);
	}


	function prepare_items() {

		$per_page = $this->get_items_per_page( WPCF_Page_Control_Termmeta::SCREEN_OPTION_PER_PAGE_NAME, 10 );

		$columns = $this->get_columns();
		$hidden = array();
		$sortable = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );

		$this->process_bulk_action();

		$search_string = mb_strtolower( trim( wpcf_getpost( 's' ) ) );

		$query_args = array(
			'filter' => 'all',
			'orderby' => sanitize_text_field( wpcf_getget( 'orderby', 'name' ) ),
			'order' => sanitize_text_field( wpcf_getget( 'order', 'asc' ) ),
			'search' => $search_string
		);

		$definitions = WPCF_Field_Term_Definition_Factory::get_instance()->query_definitions( $query_args );

		$current_page = $this->get_pagenum();
		$total_items = count( $definitions );
		$definitions = array_slice( $definitions, ( ( $current_page - 1 ) * $per_page ), $per_page );

		$this->items = $definitions;

		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page' => $per_page,
				'total_pages' => ceil( $total_items / $per_page )
			)
		);
	}


	/**
	 * @param WPCF_Field_Definition_Abstract $item
	 * @return string
	 */
	function column_name( $item ) {
		return $item->get_name();
	}


	/**
	 * @param WPCF_Field_Definition_Abstract $item
	 *
	 * @return string
	 */
	function column_groups( $item ) {
		$groups = array();
		if ( $item instanceof WPCF_Field_Definition ) {
			$groups = $item->get_associated_groups();
		}

		if ( empty( $groups ) ) {
			return __( 'None', 'wpcf' );
		} else {
			$group_titles = array();
			foreach ( $groups as $group ) {
				$group_titles[] = $group->get_name();
			}

			return implode( ', ', $group_titles );
		}
	}


	/**
	 * @param $item WPCF_Field_Definition_Abstract
	 * @return string
	 */
	function column_slug( $item ) {
		return $item->get_slug();
	}


	/**
	 * @param $item WPCF_Field_Definition_Abstract
	 *
	 * @return string
	 */
	function column_field_type( $item ) {
		$type = null;
		if ( $item instanceof WPCF_Field_Definition ) {
			if ( $item->is_under_types_control() ) {
				$type = $item->get_type()->get_display_name();
			}
		}

		return ( null == $type ? __( 'Not under Types control', 'wpcf' ) : $type );
	}


	/**
	 * @param WPCF_Field_Definition_Abstract $item
	 *
	 * @return string
	 */
	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="field_definition_slugs[]" value="%s" />', $item->get_slug()
		);
	}


	public function get_bulk_actions() {
		return array(
			self::BULK_ACTION_ADD_TO_GROUP => __( 'Add to Field Groups', 'wpcf' ),
			self::BULK_ACTION_REMOVE_FROM_TO_GROUP => __( 'Remove from Field Groups', 'wpcf' ),
			self::BULK_ACTION_CHANGE_TYPE => __( 'Change type', 'wpcf' ),
			self::BULK_ACTION_ACTIVATE => __( 'Start controlling with Types', 'wpcf' ),
			self::BULK_ACTION_DEACTIVATE => __( 'Stop controlling with Types', 'wpcf' ),
			self::BULK_ACTION_DELETE => __( 'Delete', 'wpcf' ),
		);
	}


	private function process_bulk_action() {

		$action = $this->current_action();
		if ( false == $action ) {
			return;
		}

		if ( ! wp_verify_nonce( wpcf_getpost( '_wpnonce' ), WPCF_Page_Control_Termmeta::BULK_ACTION_NONCE ) ) {
			wp_die( __( 'Invalid nonce.', 'wpcf' ) );
		}

		$selected_field_definitions = wpcf_getpost( self::INPUT_SLUGS, array() );
		if ( is_string( $selected_field_definitions ) ) {
			$selected_field_definitions = array( $selected_field_definitions );
		}

		if ( ! is_array( $selected_field_definitions ) || empty( $selected_field_definitions ) ) {
			// Nothing to do here
			return;
		}

		$factory = WPCF_Field_Term_Definition_Factory::get_instance();

		switch ( $action ) {
			case self::BULK_ACTION_ADD_TO_GROUP:

				$group_ids = $this->read_group_ids();
				foreach ( $group_ids as $group_id ) {
					wpcf_admin_fields_save_group_fields(
						$group_id,
						$selected_field_definitions,
						true,
						WPCF_Field_Group_Term::POST_TYPE,
						WPCF_Field_Term_Definition_Factory::FIELD_DEFINITIONS_OPTION
					);
				}

				break;

			case self::BULK_ACTION_REMOVE_FROM_TO_GROUP:

				$group_ids = $this->read_group_ids();
				foreach ( $group_ids as $group_id ) {
					wpcf_admin_fields_remove_field_from_group_bulk( $group_id, $selected_field_definitions );
				}

				break;

			case self::BULK_ACTION_CHANGE_TYPE:

				$field_type_slug = wpcf_getpost( 'wpcf-id' );
				if ( ! empty( $field_type_slug ) ) {
					wpcf_admin_custom_fields_change_type(
						$selected_field_definitions,
						$field_type_slug,
						WPCF_Field_Group_Term::POST_TYPE,
						WPCF_Field_Term_Definition_Factory::FIELD_DEFINITIONS_OPTION
					);
				}

				break;

			case self::BULK_ACTION_ACTIVATE:

				$fields = wpcf_admin_fields_get_fields( false, true, false, WPCF_Field_Term_Definition_Factory::FIELD_DEFINITIONS_OPTION );

				$fields_bulk = wpcf_types_cf_under_control(
					'add',
					array( 'fields' => $selected_field_definitions ),
					WPCF_Field_Group_Term::POST_TYPE,
					WPCF_Field_Term_Definition_Factory::FIELD_DEFINITIONS_OPTION
				);

				foreach ( $fields_bulk as $field_id ) {
					if ( isset( $fields[ $field_id ] ) ) {
						$fields[ $field_id ]['data']['disabled'] = 0;
					}
					wpcf_admin_message( sprintf( __( 'Added to Types control: %s', 'wpcf' ), esc_html( $field_id ) ), 'updated', 'echo' );
				}
				wpcf_admin_fields_save_fields( $fields, false, WPCF_Field_Term_Definition_Factory::FIELD_DEFINITIONS_OPTION );
				break;

			case self::BULK_ACTION_DEACTIVATE:

				$fields = wpcf_admin_fields_get_fields( false, true, false, WPCF_Field_Term_Definition_Factory::FIELD_DEFINITIONS_OPTION );
				foreach ( $selected_field_definitions as $field_id ) {
					$field_id = sanitize_text_field( $field_id );
					if ( isset( $fields[ $field_id ] ) ) {
						$fields[ $field_id ]['data']['disabled'] = 1;
						wpcf_admin_message( sprintf( __( 'Removed from Types control: %s', 'wpcf' ), $fields[ $field_id ]['name'] ), 'updated', 'echo' );
					}
				}
				wpcf_admin_fields_save_fields( $fields, false, WPCF_Field_Term_Definition_Factory::FIELD_DEFINITIONS_OPTION );

				break;

			case self::BULK_ACTION_DELETE:

				$failed = array();
				$success = array();
				foreach ( $selected_field_definitions as $field_id ) {
					$field_id = sanitize_text_field( $field_id );

					// Permanently single field definition and field data.
					$field_definition = $factory->load_field_definition( $field_id );
					if( null == $field_definition ) {
						$response = false;
					} else {
						$response = $factory->delete_definition( $field_definition );
					}

					if ( ! $response ) {
						$failed[] = str_replace( '_' . md5( 'wpcf_not_controlled' ), '', $field_id );
					} else {
						$success[] = $field_id;
					}
				}
				if ( ! empty( $success ) ) {
					wpcf_admin_message(
						sprintf(
							__( 'Fields %s have been deleted.', 'wpcf' ),
							esc_html( implode( ', ', $success ) )
						),
						'updated',
						'echo'
					);
				}
				if ( ! empty( $failed ) ) {
					wpcf_admin_message(
						sprintf(
							__( 'Fields %s are not Types fields. Types wont delete these fields.', 'wpcf' ),
							esc_html( implode( ', ', $failed ) )
						),
						'error',
						'echo'
					);
				}
				break;

		}

		// We made changes to field definitions and now the listing table is going to be rendered.
		$factory->clear_definition_storage();

	}


	private function read_group_ids() {
		$group_ids = wpcf_getpost( 'wpcf-id' );
		if ( ! is_string( $group_ids ) || empty( $group_ids ) ) {
			return array();
		}

		return explode( ',', $group_ids );
	}
}