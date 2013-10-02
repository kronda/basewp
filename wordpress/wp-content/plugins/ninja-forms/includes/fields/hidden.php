<?php
function ninja_forms_register_field_hiddenbox(){
	$args = array(
		'name' => __( 'Hidden Field' , 'ninja-forms' ),
		'sidebar' => 'template_fields',
		'edit_function' => 'ninja_forms_field_hidden_edit',
		'display_function' => 'ninja_forms_field_hidden_display',
		'save_function' => '',
		'group' => 'standard_fields',
		'edit_label' => true,
		'edit_label_pos' => false,
		'edit_req' => false,
		'edit_custom_class' => true,
		'edit_help' => false,
		'edit_meta' => false,
		'edit_conditional' => true,
		'conditional' => array(
			'value' => array(
				'type' => 'text',
			),
			'action' => array(
				'change_value' => array(
					'name'        => __( 'Change Value', 'ninja-forms' ),
					'js_function' => 'change_value',
					'output'      => 'text',
				),
			),
		),
		'display_label' => false,
	);

	ninja_forms_register_field('_hidden', $args);
}

add_action('init', 'ninja_forms_register_field_hiddenbox');

function ninja_forms_field_hidden_edit($field_id, $data){

	$custom = '';
	// Default Value
	if(isset($data['default_value'])){
		$default_value = $data['default_value'];
	}else{
		$default_value = '';
	}

	?>
	<p class="description description-thin">
		<label for="">
			<?php _e( 'Default Value' , 'ninja-forms'); ?><br />
			<select id="default_value_<?php echo $field_id;?>" name="" class="widefat ninja-forms-hidden-default-value" rel="<?php echo $field_id;?>">
				<option value="" <?php if($default_value == 'none' OR $default_value == ''){ echo 'selected'; $custom = 'no';}?>><?php _e('None', 'ninja-forms'); ?></option>
				<option value="_user_id" <?php if($default_value == '_user_id'){ echo 'selected'; $custom = 'no';}?>><?php _e('User ID (If logged in)', 'ninja-forms'); ?></option>
				<option value="user_firstname" <?php if($default_value == 'user_firstname'){ echo 'selected'; $custom = 'no';}?>><?php _e('User Firstname (If logged in)', 'ninja-forms'); ?></option>
				<option value="user_lastname" <?php if($default_value == 'user_lastname'){ echo 'selected'; $custom = 'no';}?>><?php _e('User Lastname (If logged in)', 'ninja-forms'); ?></option>
				<option value="_user_display_name" <?php if($default_value == '_user_display_name'){ echo 'selected'; $custom = 'no';}?>><?php _e('User Display Name (If logged in)', 'ninja-forms'); ?></option>
				<option value="user_email" <?php if($default_value == 'user_email'){ echo 'selected'; $custom = 'no';}?>><?php _e('User Email (If logged in)', 'ninja-forms'); ?></option>
				<option value="custom" <?php if($custom != 'no'){ echo 'selected';}?>><?php _e('Custom', 'ninja-forms'); ?> -></option>
			</select>
		</label>
	</p>
	<p class="description description-thin">
		<label for="" id="default_value_label_<?php echo $field_id;?>" style="<?php if($custom == 'no'){ echo 'display:none;';}?>">
			<?php _e( 'Custom Default Value' , 'ninja-forms'); ?><br />
			<input type="text" class="widefat code" name="ninja_forms_field_<?php echo $field_id;?>[default_value]" id="ninja_forms_field_<?php echo $field_id;?>_default_value" value="<?php echo $default_value;?>" />
		</label>
	</p>

	<?php
	// Email Input Box ?
	if(isset($data['email'])){
		$email = $data['email'];
	}else{
		$email = '';
	}

	if(isset($data['send_email'])){
		$send_email = $data['send_email'];
	}else{
		$send_email = '';
	}
	?>
	<p class="description description-thin">
			<label for="ninja_forms_field_<?php echo $field_id;?>_email">
			<?php _e( 'Is this an email address?' , 'ninja-forms'); ?>
			<input type="hidden" value="0" name="ninja_forms_field_<?php echo $field_id;?>[email]">
			<input type="checkbox" value="1" name="ninja_forms_field_<?php echo $field_id;?>[email]" id="ninja_forms_field_<?php echo $field_id;?>_email" class="ninja-forms-hidden-email" <?php if($email == 1){ echo "checked";}?>>
		</label>
		<a href="#" class="tooltip">
		    <img id="" class='ninja-forms-help-text' src="<?php echo NINJA_FORMS_URL;?>/images/question-ico.gif" title="">
		    <span>
		        <img class="callout" src="<?php echo NINJA_FORMS_URL;?>/images/callout.gif" />
		        <?php _e( 'If this box is checked, Ninja Forms will validate this input as an email address.', 'ninja-forms' );?>
		    </span>
		</a>
	</p>

	<p class="description description-wide">
			<label for="ninja_forms_field_<?php echo $field_id;?>_send_email" id="" style="">
			<?php _e( 'Send a copy of the form to this address?' , 'ninja-forms'); ?>
			<input type="hidden" value="0" name="ninja_forms_field_<?php echo $field_id;?>[send_email]">
			<input type="checkbox" value="1" name="ninja_forms_field_<?php echo $field_id;?>[send_email]" id="ninja_forms_field_<?php echo $field_id;?>_send_email" class="ninja-forms-hidden-send-email" <?php if($send_email == 1){ echo "checked";}?>>
			</label>
			<a href="#" class="tooltip">
			    <img id="" class='ninja-forms-help-text' src="<?php echo NINJA_FORMS_URL;?>/images/question-ico.gif" title="">
			    <span>
			        <img class="callout" src="<?php echo NINJA_FORMS_URL;?>/images/callout.gif" />
			        <?php _e( 'If this box is checked, Ninja Forms will send a copy of this form (and any messages attached) to this address.', 'ninja-forms' ); ?>
			    </span>
			</a>

	</p>
	<?php
}

function ninja_forms_field_hidden_display($field_id, $data){
	global $current_user;

	$field_class = ninja_forms_get_field_class($field_id);
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

	?>
	<input id="ninja_forms_field_<?php echo $field_id;?>" name="ninja_forms_field_<?php echo $field_id;?>" type="hidden" class="<?php echo $field_class;?>" value="<?php echo $default_value;?>" rel="<?php echo $field_id;?>" />
	<?php

}