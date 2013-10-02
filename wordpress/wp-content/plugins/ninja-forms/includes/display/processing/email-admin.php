<?php
add_action('init', 'ninja_forms_register_email_admin');
function ninja_forms_register_email_admin(){
	add_action('ninja_forms_post_process', 'ninja_forms_email_admin', 999);
}

function ninja_forms_email_admin(){
	global $ninja_forms_processing;

	do_action( 'ninja_forms_email_admin' );

	$form_ID = $ninja_forms_processing->get_form_ID();
	$form_title = $ninja_forms_processing->get_form_setting('form_title');
	$admin_mailto = $ninja_forms_processing->get_form_setting('admin_mailto');
	$email_from = $ninja_forms_processing->get_form_setting('email_from');
	$email_type = $ninja_forms_processing->get_form_setting('email_type');
	$subject = $ninja_forms_processing->get_form_setting('admin_subject');
	$message = $ninja_forms_processing->get_form_setting('admin_email_msg');
	$default_email = get_option( 'admin_email' );

	if(!$subject){
		$subject = $form_title;
	}
	if(!$message){
		$message = '';
	}
	if(!$email_from){
		$email_from = $default_email;
	}
	if(!$email_type){
		$email_type = '';
	}

	if( $email_type !== 'plain' ){
		$message = wpautop( $message );
	}

	$email_from = htmlspecialchars_decode($email_from);
	$email_from = htmlspecialchars_decode($email_from);

	if( $ninja_forms_processing->get_form_setting( 'admin_email_from' ) ){
		$email_from = $ninja_forms_processing->get_form_setting( 'admin_email_from' );
	}

	if( $ninja_forms_processing->get_form_setting( 'admin_email_name' ) ){
		$email_name = $ninja_forms_processing->get_form_setting( 'admin_email_name' );
	}else{
		$email_name = '';
	}

	if( $email_name != '' ){
		$email_from = $email_name." <".$email_from.">";
	}else{
		$email_from = htmlspecialchars_decode($email_from);
	}

	$headers = "\nMIME-Version: 1.0\n";
	$headers .= "From: $email_from \r\n";
	$headers .= "Content-Type: text/".$email_type."; charset=utf-8\r\n";

	if($ninja_forms_processing->get_form_setting('admin_attachments')){
		$attachments = $ninja_forms_processing->get_form_setting('admin_attachments');
	}else{
		$attachments = '';
	}

	if(is_array($admin_mailto) AND !empty($admin_mailto)){
		foreach( $admin_mailto as $to ){
			$sent = wp_mail($to, $subject, $message, $headers, $attachments);
		}
	}
}