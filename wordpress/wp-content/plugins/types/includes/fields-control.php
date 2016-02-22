<?php
/*
 * Custom Fields Control Screen
 *
 *
 */
require_once ABSPATH . '/wp-admin/includes/class-wp-list-table.php';
require_once dirname(__FILE__).'/classes/class.types.admin.custom.fields.control.table.php';

/**
 * Submitted Bulk actions.
 */
function wpcf_admin_custom_fields_control_bulk_actions($action = '')
{
    if (
        !isset($_POST['_wpnonce'])
        || !wp_verify_nonce($_POST['_wpnonce'], 'custom_fields_control_bulk')
    ) {
        return;
    }
    switch( $action) {

    case 'wpcf-change-type-bulk':
        if (
            true
            && isset($_POST['wpcf-id'])
            && isset($_POST['fields'])
        ) {
            wpcf_admin_custom_fields_change_type($_POST['fields'], $_POST['wpcf-id'], TYPES_CUSTOM_FIELD_GROUP_CPT_NAME);
        }
        break;

    case 'wpcf-deactivate-bulk':
        $fields = wpcf_admin_fields_get_fields(false, true);
        foreach ($_POST['fields'] as $field_id) {
            $field_id = sanitize_text_field( $field_id );
            if (isset($fields[$field_id])) {
                $fields[$field_id]['data']['disabled'] = 1;
                wpcf_admin_message_store(sprintf(__('Removed from Types control: %s',
                    'wpcf'), $fields[$field_id]['name']));
            }
        }
        wpcf_admin_fields_save_fields($fields);
        break;

    case 'wpcf-activate-bulk':
        $fields = wpcf_admin_fields_get_fields(false, true);
        $fields_bulk = wpcf_types_cf_under_control('add',
            array('fields' => $_POST['fields']));
        foreach ($fields_bulk as $field_id) {
            if (isset($fields[$field_id])) {
                $fields[$field_id]['data']['disabled'] = 0;
            }
            wpcf_admin_message_store(sprintf(__('Added to Types control: %s',
                'wpcf'), $field_id));
        }
        wpcf_admin_fields_save_fields($fields);
        break;

    case 'wpcf-delete-bulk':
        require_once WPCF_INC_ABSPATH . '/fields.php';
        $failed = array();
        $success = array();
        foreach ($_POST['fields'] as $field_id) {
            $field_id = sanitize_text_field( $field_id );
            $response = wpcf_admin_fields_delete_field($field_id);
            if (!$response) {
                $failed[] = str_replace('_' . md5('wpcf_not_controlled'), '', $field_id);
            } else {
                $success[] = $field_id;
            }
        }
        if (!empty($success)) {
            wpcf_admin_message_store(sprintf(__('Fields %s have been deleted.',
                'wpcf'), implode(', ', $success)));
        }
        if (!empty($failed)) {
            wpcf_admin_message_store(
                sprintf(
                    __('Fields %s are not Types fields. Types wont delete these fields.', 'wpcf'),
                    implode(', ', $failed)
                ),
                'error'
            );
        }
        break;

    case 'wpcf-add-to-group-bulk':
    case 'wpcf-remove-from-group-bulk':
        if (
            true
            && isset($_POST['wpcf-id'])
            && isset($_POST['fields'])
        ) {
            $fields = array_values((array) $_POST['fields']);
            if ( !empty($fields) ) {
                $groups = explode(',', $_POST['wpcf-id']);
                foreach ($groups as $group_id) {
                    switch ($action) {
                    case 'wpcf-add-to-group-bulk':
                        wpcf_admin_fields_save_group_fields($group_id, $fields, true);
                        break;

                    case 'wpcf-remove-from-group-bulk':
                        wpcf_admin_fields_remove_field_from_group_bulk($group_id, $fields);
                        break;
                    }
                }
            }
        }
        break;
    }
    wp_safe_redirect(
        esc_url_raw(
            add_query_arg(
                array(
                    'page' => 'wpcf-custom-fields-control',
                ),
                admin_url('admin.php')
            )
        )
    );
    die();
}

function wpcf_admin_menu_custom_fields_control_hook_helper()
{
    do_action( 'wpcf_admin_page_init' );
    require_once WPCF_INC_ABSPATH . '/fields.php';
    require_once WPCF_EMBEDDED_INC_ABSPATH . '/fields.php';

    if ( isset( $_REQUEST['_wpnonce'] )
            && wp_verify_nonce( $_REQUEST['_wpnonce'],
                    'custom_fields_control_bulk' )
            && (isset( $_POST['action'] ) || isset( $_POST['action2'] )) && !empty( $_POST['fields'] ) ) {
        $action = ( $_POST['action'] == '-1' ) ? sanitize_text_field( $_POST['action2'] ) : sanitize_text_field( $_POST['action'] );
        wpcf_admin_custom_fields_control_bulk_actions( $action );
    }
    /**
     * add common resources
     */
    wpcf_fields_contol_common_resources();

    wpcf_admin_page_add_options('cfc',  __( 'Post Fields', 'wpcf' ));
    global $wpcf_control_table;
    $wpcf_control_table = new Types_Admin_Custom_Fields_Control_Table();
    $wpcf_control_table->prepare_items();
}

