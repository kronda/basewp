<?php

/**
 * Definition of a field.
 *
 * Children of this class must be instantiated exclusively through a factory class inherited from WPCF_Field_Definition_Factory.
 *
 * Note about field definition identification: For historical reasons, there are several, possibly equal, values available:
 * The field key (key in the associative array of field definitions), slug, id and meta_key. While the meta_key's role
 * is clear - it determines how field's data are stored in postmeta -, the first three values seem to be completely
 * identical. Since the slug is used to organize fields into groups, we will use that as the main unique identifier
 * from now on.
 *
 * @since 1.9
 */
abstract class WPCF_Field_Definition extends WPCF_Field_Definition_Abstract {


	/**
	 * For a Types field, this is a default prefix to it's slug that defines the meta_key for storing this field's values.
	 * Note that other custom fields that are brought under Types control additionally don't have to use this prefix
	 * and can have completely arbitrary meta_keys.
	 */
	const FIELD_META_KEY_PREFIX = 'wpcf-';


	const TYPE_CHECKBOXES = 'checkboxes';

	const TYPE_CHECKBOX = 'checkbox';


	/**
	 * @var WPCF_Field_Type_Definition Type definition.
	 */
	private $type;


	/**
	 * @var array The underlying array with complete information about this field.
	 * @todo We need a specification of everything that can be in it.
	 */
	/*

	Example of an field definition:

	array (
		'id' => 'field-1',
		'slug' => 'field-1',
		'type' => 'textfield',
		'name' => 'Field 1',
		'description' => '',
		'data' => array
		(
			'placeholder' => '',
			'user_default_value' => '',
			'repetitive' => '0',
			'is_new' => '1',
			'conditional_display' => array
			(
				'custom_use' => '0',
				'relation' => 'AND',
				'custom' => '',
			),
			'submit-key' => 'textfield-1955088717',
			'disabled_by_type' => 0,
			'set_value' => ... (optional, presence is relevant)
		),
		'meta_key' => 'wpcf-field-1',
		'meta_type' => 'postmeta',
		),
	); */
	private $definition_array;


	/**
	 * @var string Field slug.
	 */
	private $slug;


	/**
	 * @var string Name of the field that can be displayed to the user.
	 */
	private $name;



	/**
	 * WPCF_Field_Definition constructor.
	 *
	 * @param WPCF_Field_Type_Definition $type Field type definition.
	 * @param array $definition_array The underlying array with complete information about this field.
	 */
	public function __construct( $type, $definition_array ) {
		$this->type = $type;
		$this->definition_array = $definition_array;

		$this->slug = wpcf_getarr( $definition_array, 'slug' );
		if( sanitize_title( $this->slug ) != $this->slug ) {
			throw new InvalidArgumentException( 'Invalid slug.' );
		}

		$this->name = sanitize_text_field( wpcf_getarr( $definition_array, 'name', $this->get_slug() ) );
	}



	public function get_slug() { return $this->slug; }


	public function get_name() { return $this->name; }


	/**
	 * @return string Proper display name suitable for direct displaying to the user.
	 *
	 * Handles string translation and adds an asterisk if the field is required.
	 *
	 * @since 1.9
	 */
	public function get_display_name() {

		// Try to translate through standard toolset-forms method
		$string_name = sprintf( 'field %s name', $this->get_slug() );
		$display_name = WPToolset_Types::translate( $string_name, $this->get_name() );

		// Add an asterisk if the field is required
		if( $this->get_is_required() && !empty( $display_name ) ) {
			$display_name .= '&#42;';
		}
		return $display_name;
	}


	/**
	 * @return string Field description as provided by the user. Sanitized.
	 */
	public function get_description() { return sanitize_text_field( wpcf_getarr( $this->definition_array, 'description' ) ); }


	/**
	 * Determine whether the field is currently under Types control.
	 *
	 * If it's not, we are only holding on to this definition in case user decides to return it to Types control in the
	 * future. In all other regards, such field definition should be handled as a generic one.
	 *
	 * @return bool
	 */
	public function is_under_types_control() {
		$is_disabled = (bool) wpcf_getnest( $this->definition_array, array( 'data', 'disabled' ), false );
		return !$is_disabled;
	}


	private $meta_key = null;


	/**
	 * @return string Meta_key use to store this field's values. Defaults to wpcf-$slug.
	 */
	public function get_meta_key() {
		if( null == $this->meta_key ) {
			$this->meta_key = sanitize_title(
				wpcf_getarr( $this->definition_array, 'meta_key', self::FIELD_META_KEY_PREFIX . $this->get_slug() )
			);
		}
		return $this->meta_key;
	}


	private $is_repetitive = null;


	/**
	 * @return bool True if the field is repetitive, false otherwise.
	 */
	public function get_is_repetitive() {
		if( null === $this->is_repetitive ) {
			$this->is_repetitive = ( wpcf_getnest( $this->definition_array, array( 'data', 'repetitive' ), 0 ) != 0 );
		}
		return $this->is_repetitive;
	}


	/**
	 * Get the underlying field definition array.
	 *
	 * Usage of this method is strongly discouraged, consider writing a custom (and safe) getter instead.
	 *
	 * @return array
	 */
	public function get_definition_array() { return $this->definition_array; }


	public function get_type() { return $this->type; }


	/**
	 * For binary fields (like checkbox), it is possible to specify a value that will be saved to the database
	 * if the field is checked/selected/whatever.
	 *
	 * Stored in $cf['data']['set_save'].
	 *
	 * @return mixed|null The value or null if none is defined (make sure to compare with ===).
	 */
	public function get_forced_value() {
		return wpcf_getnest( $this->definition_array, array( 'data', 'set_value' ), null );
	}


	public function has_forced_value() {
		return ( null !== $this->get_forced_value() );
	}


	public function get_should_save_empty_value() {
		return ( 'yes' == wpcf_getnest( $this->definition_array, array( 'data', 'save_empty' ), 'no' ) );
	}


	public function get_is_required() {
		return ( 1 == wpcf_getnest( $this->definition_array, array( 'data', 'validate', 'required', 'active' ), 0 ) );
	}


	/**
	 * @return array An option_id => option_data array.
	 */
	public function get_field_options() {
		return wpcf_ensarr( wpcf_getnest( $this->definition_array, array( 'data', 'options' ) ) );
	}



	/**
	 * Get an accessor for a specific field instance.
	 *
	 * @param WPCF_Field_Instance $field_instance Instance of the field the accessor should access.
	 * @return WPCF_Field_Accessor_Abstract
	 */
	public abstract function get_accessor( $field_instance );




	/**
	 * Get a mapper object that helps translating field data between database and rest of Types.
	 *
	 * Note: This happens here and not in field type definition because the information about field type might not
	 * be enough in the future.
	 *
	 * @todo This should probably be provided by type definition, no switch should be here.
	 *
	 * @return WPCF_Field_DataMapper_Abstract
	 */
	public function get_data_mapper() {
		switch( $this->get_type()->get_slug() ) {
			case self::TYPE_CHECKBOXES:
				return new WPCF_Field_DataMapper_Checkboxes( $this );
			case self::TYPE_CHECKBOX:
				return new WPCF_Field_DataMapper_Checkbox( $this );
			default:
				return new WPCF_Field_DataMapper_Identity( $this );
		}
	}


	/**
	 * Delete all field values!
	 *
	 * @return bool
	 */
	public abstract function delete_all_fields();

}