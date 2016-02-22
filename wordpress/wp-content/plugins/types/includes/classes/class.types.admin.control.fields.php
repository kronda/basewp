<?php

require_once WPCF_INC_ABSPATH.'/classes/class.types.admin.edit.fields.php';

/**
 * Summary.
 *
 * Description.
 *
 * @since x.x.x
 * @access (for functions: only use if private)
 *
 * @see Function/method/class relied on
 * @link URL
 * @global type $varname Description.
 * @global type $varname Description.
 *
 * @param type $var Description.
 * @param type $var Optional. Description.
 * @return type Description.
 */
class Types_Admin_Control_Fields extends Types_Admin_Edit_Fields
{
    /**
     * Summary.
     *
     * Description.
     *
     * @since x.x.x
     * @access (for functions: only use if private)
     *
     * @see Function/method/class relied on
     * @link URL
     * @global type $varname Description.
     * @global type $varname Description.
     *
     * @param type $var Description.
     * @param type $var Optional. Description.
     * @return type Description.
     */
    public function __construct()
    {
        parent::__construct();
        $this->init_admin();
    }

    /**
     * Summary.
     *
     * Description.
     *
     * @since x.x.x
     * @access (for functions: only use if private)
     *
     * @see Function/method/class relied on
     * @link URL
     * @global type $varname Description.
     * @global type $varname Description.
     *
     * @param type $var Description.
     * @param type $var Optional. Description.
     * @return type Description.
     */
    public function init_admin()
    {
        add_action('wp_ajax_wpcf_custom_fields_control_change_type', array($this, 'types_list'));
        add_action('wp_ajax_wpcf_custom_fields_control_get_groups', array($this, 'add_fields_to_groups'));
        add_action('wp_ajax_wpcf_usermeta_control_get_groups', array($this, 'add_usermeta_to_groups'));
    }

    /**
     * Summary.
     *
     * Description.
     *
     * @since x.x.x
     * @access (for functions: only use if private)
     *
     * @see Function/method/class relied on
     * @link URL
     * @global type $varname Description.
     * @global type $varname Description.
     *
     * @param type $var Description.
     * @param type $var Optional. Description.
     * @return type Description.
     */
    public function types_list()
    {
        /**
         * check nonce
         */
        if (
            0
            || !isset($_REQUEST['_wpnonce'])
            || !wp_verify_nonce($_REQUEST['_wpnonce'], 'custom_fields_control')
        ) {
            $this->verification_failed_and_die();
        }
        $form = array();
        $form += $this->add_description(
            __('You can choose from the available fields:', 'wpcf')
        );
        $form = $this->get_fields_list($form);
        $form['nonce'] = array(
            '#type' => 'hidden',
            '#value' => wp_create_nonce('types_admin_control_fields_change_type'),
            '#name' => 'wpcf-fields-change-type',
            '#id' => 'wpcf-fields-change-type',
        );
        $form = wpcf_form(__FUNCTION__, $form);
        echo $form->renderForm();
        die;
    }

    /**
     * Summary.
     *
     * Description.
     *
     * @since x.x.x
     * @access (for functions: only use if private)
     *
     * @see Function/method/class relied on
     * @link URL
     * @global type $varname Description.
     * @global type $varname Description.
     *
     * @param type $var Description.
     * @param type $var Optional. Description.
     * @return type Description.
     */
    public function add_fields_to_groups()
    {
        /**
         * check nonce
         */
        if (
            0
            || !isset($_REQUEST['_wpnonce'])
            || !wp_verify_nonce($_REQUEST['_wpnonce'], 'custom_fields_control')
        ) {
            $this->verification_failed_and_die();
        }
        $form = $this->get_group_list();
        $form = wpcf_form(__FUNCTION__, $form);
        echo $form->renderForm();
        die;
    }

    /**
     * Summary.
     *
     * Description.
     *
     * @since x.x.x
     * @access (for functions: only use if private)
     *
     * @see Function/method/class relied on
     * @link URL
     * @global type $varname Description.
     * @global type $varname Description.
     *
     * @param type $var Description.
     * @param type $var Optional. Description.
     * @return type Description.
     */
    public function add_usermeta_to_groups()
    {
        /**
         * check nonce
         */
        if (
            0
            || !isset($_REQUEST['_wpnonce'])
            || !wp_verify_nonce($_REQUEST['_wpnonce'], 'custom_fields_control')
        ) {
            $this->verification_failed_and_die();
        }
        $form = $this->get_group_list(array(), 'wpcf-usermeta');
        $form = wpcf_form(__FUNCTION__, $form);
        echo $form->renderForm();
        die;
    }
}

