<?php

/*
 *
 * Function that filters default values, replacing defined strings with the approparite values.
 *
 * @since 2.2.49
 * @return $data
 */

function ninja_forms_default_value_filter( $data, $field_id ) {
	global $current_user;

	if(isset($data['default_value'])){
		$default_value = $data['default_value'];
	}else{
		$default_value = '';
	}

	get_currentuserinfo();
	$user_ID = $current_user->ID;
	$user_firstname = $current_user->user_firstname;
    $user_lastname = $current_user->user_lastname;
    $user_display_name = $current_user->display_name;
    $user_email = $current_user->user_email;

    switch( $default_value ){
		case '_user_id':
			$default_value = $user_ID;
			break;
		case 'user_firstname':
			$default_value = $user_firstname;
			break;
		case 'user_lastname':
			$default_value = $user_lastname;
			break;
		case '_user_display_name':
			$default_value = $user_display_name;
			break;
		case 'user_email':
			$default_value = $user_email;
			break;
	}

	$data['default_value'] = $default_value;

	return $data;
}

add_filter( 'ninja_forms_field', 'ninja_forms_default_value_filter', 9, 2 );