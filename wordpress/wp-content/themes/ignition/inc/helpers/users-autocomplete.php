<?php

add_action("wp_ajax_nopriv_thrive_helper_get_users", "_thrive_helper_get_users");
add_action("wp_ajax_thrive_helper_get_users", "_thrive_helper_get_users");

function _thrive_helper_get_users() {
    
    if (!wp_verify_nonce($_REQUEST['nonce'], "thrive_helper_get_users")) {
        echo 0;
        die;
    }
    
    $search_txt = sanitize_text_field($_GET['term']);

    $user_query = new WP_User_Query(array(
        'search' => "*" .  $search_txt . "*",
        'fields' => array('ID', 'user_login'),
        'number' => 20));

    $users = array();

    if (!empty($user_query->results)) {
        foreach ($user_query->results as $res) {
            $users[] = array('id' => $res->ID, 'value' => $res->user_login);
        }
    }
    
    echo json_encode($users);
    die;
}
?>