<?php
/**
 * Function to allow users to duplicate posts / pages and other content types at the click of a button.
 */

function thrive_duplicate_post($status = '')
{
    if (!(isset($_GET['post']) || isset($_POST['post']) || (isset($_REQUEST['action']) && 'thrive_duplicate_post' == $_REQUEST['action']))) {
        wp_die(__('No post to duplicate has been supplied!', 'thrive'));
    }

    // Get the original post
    $id = (isset($_GET['post']) ? $_GET['post'] : $_POST['post']);
    $post = get_post($id);

    // Copy the post and insert it
    if (isset($post) && $post != null) {
        $new_id = thrive_create_duplicate($post, $status);

        if ($status == '') {
            // Redirect to the post list screen
            wp_redirect(admin_url('edit.php?post_type=' . $post->post_type));
        } else {
            // Redirect to the edit screen for the new draft post
            wp_redirect(admin_url('post.php?action=edit&post=' . $new_id));
        }
        exit;

    } else {
        $post_type_obj = get_post_type_object($post->post_type);
        wp_die(esc_attr(__('Copy creation failed, could not find original:', 'thrive')) . ' ' . $id);
    }
}

function thrive_get_current_user() {
    if (function_exists('wp_get_current_user')) {
        return wp_get_current_user();
    } else if (function_exists('get_currentuserinfo')) {
        global $userdata;
        get_currentuserinfo();
        return $userdata;
    } else {
        $user_login = $_COOKIE[USER_COOKIE];
        $current_user = $wpdb->get_results("SELECT * FROM $wpdb->users WHERE user_login='$user_login'");
        return $current_user;
    }
}


function thrive_create_duplicate($post, $status = '', $parent_id = '') {

    // We don't want to clone revisions
    if ($post->post_type == 'revision') return;

    if ($post->post_type != 'attachment'){
        $prefix = __("Clone of", 'thrive');
        if (!empty($prefix)) $prefix.= " ";
        if (get_option('duplicate_post_copystatus') == 0) $status = 'draft';
    }
    $new_post_author = thrive_get_current_user();

    $new_post = array(
        'menu_order' => $post->menu_order,
        'comment_status' => $post->comment_status,
        'ping_status' => $post->ping_status,
        'post_author' => $new_post_author->ID,
        'post_content' => $post->post_content,
        'post_excerpt' => (get_option('duplicate_post_copyexcerpt') == '1') ? $post->post_excerpt : "",
        'post_mime_type' => $post->post_mime_type,
        'post_parent' => $new_post_parent = empty($parent_id)? $post->post_parent : $parent_id,
        'post_password' => $post->post_password,
        'post_status' => $new_post_status = (empty($status))? $post->post_status: $status,
        'post_title' => $prefix.$post->post_title,
        'post_type' => $post->post_type,
    );

    $new_post_id = wp_insert_post($new_post);

    // If you have written a plugin which uses non-WP database tables to save
    // information about a post you can hook this action to dupe that data.
    if ($post->post_type == 'page' || (function_exists('is_post_type_hierarchical') && is_post_type_hierarchical( $post->post_type )))
        do_action( 'thrive_duplicate_page', $new_post_id, $post );
    else
        do_action( 'thrive_duplicate_post', $new_post_id, $post );

    delete_post_meta($new_post_id, '_dp_original');
    add_post_meta($new_post_id, '_dp_original', $post->ID);

    // If the copy is published or scheduled, we have to set a proper slug.
    if ($new_post_status == 'publish' || $new_post_status == 'future'){
        $post_name = wp_unique_post_slug($post->post_name, $new_post_id, $new_post_status, $post->post_type, $new_post_parent);

        $new_post = array();
        $new_post['ID'] = $new_post_id;
        $new_post['post_name'] = $post_name;

        // Update the post into the database
        wp_update_post( $new_post );
    }

    return $new_post_id;
}


add_action('thrive_duplicate_post', 'thrive_copy_post_taxonomies', 10, 2);
add_action('thrive_duplicate_page', 'thrive_copy_post_taxonomies', 10, 2);

function thrive_copy_post_taxonomies($new_id, $post) {
    global $wpdb;
    if (isset($wpdb->terms)) {
        // Clear default category (added by wp_insert_post)
        wp_set_object_terms( $new_id, NULL, 'category' );

        $post_taxonomies = get_object_taxonomies($post->post_type);
        $taxonomies_blacklist = get_option('duplicate_post_taxonomies_blacklist');
        if ($taxonomies_blacklist == "") $taxonomies_blacklist = array();
        $taxonomies = array_diff($post_taxonomies, $taxonomies_blacklist);
        foreach ($taxonomies as $taxonomy) {
            $post_terms = wp_get_object_terms($post->ID, $taxonomy, array( 'orderby' => 'term_order' ));
            $terms = array();
            for ($i=0; $i<count($post_terms); $i++) {
                $terms[] = $post_terms[$i]->slug;
            }
            wp_set_object_terms($new_id, $terms, $taxonomy);
        }
    }
}

function thrive_copy_post_meta_info($new_id, $post) {
    $post_meta_keys = get_post_custom_keys($post->ID);
    if (empty($post_meta_keys)) return;
    $meta_blacklist = explode(",",get_option('duplicate_post_blacklist'));
    if ($meta_blacklist == "") $meta_blacklist = array();
    $meta_keys = array_diff($post_meta_keys, $meta_blacklist);

    foreach ($meta_keys as $meta_key) {
        $meta_values = get_post_custom_values($meta_key, $post->ID);
        foreach ($meta_values as $meta_value) {
            $meta_value = maybe_unserialize($meta_value);
            add_post_meta($new_id, $meta_key, $meta_value);
        }
    }
}

add_action('thrive_duplicate_post', 'thrive_copy_post_meta_info', 10, 2);
add_action('thrive_duplicate_page', 'thrive_copy_post_meta_info', 10, 2);

function thrive_make_duplicate_link_row($actions, $post) {
    if (current_user_can('edit_posts')) {
        $actions['edit_as_new_draft'] = '<a href="'.thrive_get_clone_post_link( $post->ID ).'" title="'
            . esc_attr(__("Clone this item", 'thrive'))
            . '">' .  __('Clone', 'thrive') . '</a>';
    }
    return $actions;
}

add_filter('post_row_actions', 'thrive_make_duplicate_link_row',10,2);
add_filter('page_row_actions', 'thrive_make_duplicate_link_row',10,2);

function thrive_get_clone_post_link( $id = 0, $context = 'display', $draft = true ) {
    if ( !current_user_can('edit_posts') )
        return;

    if ( !$post = get_post( $id ) )
        return;

    if ($draft)
        $action_name = "thrive_save_as_new_post_draft";
    else
        $action_name = "thrive_save_as_new_post";

    if ( 'display' == $context )
        $action = '?action='.$action_name.'&amp;post='.$post->ID;
    else
        $action = '?action='.$action_name.'&post='.$post->ID;

    $post_type_object = get_post_type_object( $post->post_type );
    if ( !$post_type_object )
        return;

    return apply_filters( 'duplicate_post_get_clone_post_link', admin_url( "admin.php". $action ), $post->ID, $context );
}


function thrive_save_as_new_post($status = ''){
    if (! ( isset( $_GET['post']) || isset( $_POST['post'])  || ( isset($_REQUEST['action']) && 'duplicate_post_save_as_new_post' == $_REQUEST['action'] ) ) ) {
        wp_die(__('No post to duplicate has been supplied!', 'thrive'));
    }

    // Get the original post
    $id = (isset($_GET['post']) ? $_GET['post'] : $_POST['post']);
    $post = get_post($id);

    // Copy the post and insert it
    if (isset($post) && $post!=null) {
        $new_id = thrive_create_duplicate($post, $status);

        if ($status == ''){
            // Redirect to the post list screen
            wp_redirect( admin_url( 'edit.php?post_type='.$post->post_type) );
        } else {
            // Redirect to the edit screen for the new draft post
            wp_redirect( admin_url( 'post.php?action=edit&post=' . $new_id ) );
        }
        exit;

    } else {
        $post_type_obj = get_post_type_object( $post->post_type );
        wp_die(esc_attr(__('Copy creation failed, could not find original:', 'thrive')) . ' ' . $id);
    }
}

add_action('admin_action_thrive_save_as_new_post', 'thrive_save_as_new_post');
add_action('admin_action_thrive_save_as_new_post_draft', 'thrive_save_as_new_post_draft');

/*
 * This function calls the creation of a new copy of the selected post (as a draft)
 * then redirects to the edit post screen
 */
function thrive_save_as_new_post_draft(){
    thrive_save_as_new_post('draft');
}