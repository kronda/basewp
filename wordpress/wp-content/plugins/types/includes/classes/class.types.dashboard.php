<?php

require_once dirname(__FILE__).'/class.types.admin.php';

class Types_Dashboard extends Types_Admin
{


    public function __construct()
    {
        $this->init_admin();
    }

    public function init_admin()
    {
        $this->init_hooks();

        $this->post_type = 'dashboard';
        $this->boxes = array(
            'types_admin_dashboard_tools' => array(
                'callback' => array($this, 'box_tools'),
                'title' => __('Types Tools', 'wpcf'),
                'default' => 'normal',
            ),
        );
    }

    /**
     * Add/edit form
     */
    public function form()
    {
        $form = $this->prepare_screen();

        /**
         * postbox-controll
         */

        $form['box-1-open'] = array(
            '#type' => 'markup',
            '#markup' => '<div id="post-body-content">',
            '_builtin' => true,
        );

        $html = '<ul class="types">';

        $subpages = wpcf_admin_menu_get_subpages();
        foreach( $subpages as $menu_slug => $menu ) {
            if ( isset($menu['context']) && 'menu_only' == $menu['context'] ) {
                continue;
            }
            $html .= sprintf(
                '<li><a href="%s"><span class="%s %s"></span>%s</a></li>',
                esc_url(add_query_arg(array( 'page' => $menu_slug), admin_url('admin.php'))),
                esc_attr(isset($menu['toolset_icon'])? $menu['toolset_icon']:'icon-toolset-logo'),
                esc_attr($menu_slug),
                $menu['menu_title']
            );
        }
        $html .= '</ul>';

        $form['box-content'] = array(
            '#type' => 'markup',
            '#markup' => $html,
            '_builtin' => true,
        );

        $form['box-1-close'] = array(
            '#type' => 'markup',
            '#markup' => '</div>',
            '_builtin' => true,
        );

        return $form;
    }


    public function box_tools()
    {
	    // fixme add term fields control
        $pages = array(
            'wpcf-custom-fields-control' => array(
                'page' => 'wpcf-custom-fields-control',
                'name' => __('Post Field Control', 'wpcf'),
                'description' => __('Allow to control post fields.', 'wpcf'),
            ),
            'wpcf-user-fields-control' => array(
                'page' => 'wpcf-user-fields-control',
                'name' => __('User Field Control', 'wpcf'),
                'description' => __('Allow to control user fields.', 'wpcf'),
            ),
            'wpcf-term-fields-control' => array(
                'page' => 'wpcf-termmeta-control',
                'name' => __('Term Field Control', 'wpcf'),
                'description' => __('Allow to control term fields.', 'wpcf'),
            ),
            'wpcf-import-export' => array(
                'page' => 'wpcf-import-export',
                'name' => __('Import/Export', 'wpcf'),
                'description' => __('For import or export data from Types.', 'wpcf'),
            ),
            'installer' => array(
                'page' => 'installer',
                'name' => __('Installer', 'wpcf'),
                'description' => __('This page lets you install plugins and update existing plugins.', 'wpcf'),
            ),
            'wpcf-debug-information' => array(
                'page' => 'wpcf-debug-information',
                'name' => __('Debug Information', 'wpcf'),
                'description' => __( 'For retrieving debug information if asked by a support person.', 'wpcf'),
            ),
        );

        /**
         * remove Access page if is a full version of Access 
         * installer and running
         */
        if ( defined( 'WPCF_ACCESS_VERSION' ) ) {
            unset($pages['wpcf-access']);
        }

        echo '<ul>';
        foreach( $pages as $data ) {
            echo '<li>';
            printf(
                '<strong><a href="%s">%s</a></strong>',
                esc_url( admin_url(sprintf('admin.php?page=%s', $data['page']))),
                $data['name']
            );
            if ( isset($data['description']) && !empty($data['description'])) {
                echo ' - ';
                echo $data['description'];
            }
            echo '<li>';
        }
        echo '</ul>';
    }
}

