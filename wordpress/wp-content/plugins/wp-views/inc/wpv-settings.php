<?php

class WPV_Settings extends WPV_Settings_Embedded {

    public function __construct() {
        parent::__construct();

        add_action( 'init', array( $this, 'init' ) );
    }

    ////////////////////////////////////////////////////////////////////////////
    //
    // Views Settings API - Helpers
    // 
    ////////////////////////////////////////////////////////////////////////////

    /**
     * Removes View's settings (not Views Plugin Settings) from removed posts
     * @global type $wpdb
     */
    public function refresh_view_settings_data() {
        // TODO this clearing function deletes all View options but the ones starting with wpv
        // and runs every single time a WPA is updated
        // it loops through every View setting too: it's too expensive
        // We need a better way of clearing the Views settings for loops about WPA and CT when the related objects have been deleted
        // MAYBE it would be better to check on render time, and if now available then delete the record, and remove all this clearing function altogether

        global $wpdb;

        foreach ( $this->custom as $k => $v ) {
            if ( substr( $k, 0, 3 ) != "wpv" ) {
                $post_exists = $wpdb->get_row( 
					$wpdb->prepare(
						"SELECT * FROM {$wpdb->posts} 
						WHERE ID = %d 
						AND post_type IN ('view','view-template') 
						LIMIT 1",
						$v
					),
					'ARRAY_A' 
				);
                if ( ! $post_exists ) {
                    unset( $this->custom[$k] );
                }
            }
        }
    }

    ////////////////////////////////////////////////////////////////////////////
    //
    // Views Settings Page - Start up
    // 
    ////////////////////////////////////////////////////////////////////////////

    /**
     * Views Settings page set up
     */
    function init() {
        // Plugin Settings (legacy action: wpv_action_views_settings_sections)
        // Plugin Settings (features)
        add_action( 'wpv_action_views_settings_features_section', array( $this, 'wpv_map_plugin_options' ), 30 );
        add_action( 'wpv_action_views_settings_features_section', array( $this, 'wpv_show_hidden_custom_fields_options' ), 40 );
        // Plugin Settings (compatibility)
        add_action( 'wpv_action_views_settings_compatibility_section', array( $this, 'wpv_bootstrap_options' ), 10 );
        add_action( 'wpv_action_views_settings_compatibility_section', array( $this, 'wpv_custom_inner_shortcodes_options' ), 20 );
        add_action( 'wpv_action_views_settings_compatibility_section', array( $this, 'wpv_custom_conditional_functions' ), 25 );
        add_action( 'wpv_action_views_settings_compatibility_section', array( $this, 'add_wpml_settings' ), 50 );
        // Plugin Settings (development)
        add_action( 'wpv_action_views_settings_development_section', array( $this, 'wpv_edit_view_frontend_links_options' ), 15 );
        add_action( 'wpv_action_views_settings_development_section', array( $this, 'admin_settings' ), 60 );
        add_action( 'wpv_action_views_settings_development_section', array( $this, 'wpv_debug_options' ), 100 );

        // AJAX calls for the Settings page
        // Update Features Settings
        add_action( 'wp_ajax_wpv_update_map_plugin_status', array( $this, 'wpv_update_map_plugin_status' ) );
        add_action( 'wp_ajax_wpv_get_show_hidden_custom_fields', array( $this, 'wpv_get_show_hidden_custom_fields' ) );
        // Update Compatibility Settings
        add_action( 'wp_ajax_wpv_update_bootstrap_version_status', array( $this, 'wpv_update_bootstrap_version_status' ) );
        add_action( 'wp_ajax_wpv_update_custom_inner_shortcodes', array( $this, 'wpv_update_custom_inner_shortcodes' ) );
        add_action( 'wp_ajax_wpv_update_custom_conditional_functions', array( $this, 'wpv_update_custom_conditional_functions' ) );
        add_action( 'wp_ajax_wpv_save_wpml_settings', array( $this, 'wpv_save_wpml_settings' ) );
        // Update Development Settings
        add_action( 'wp_ajax_wpv_update_show_edit_view_link_status', array( $this, 'wpv_update_show_edit_view_link_status' ) );
        add_action( 'wp_ajax_wpv_save_theme_debug_settings', array( $this, 'wpv_save_theme_debug_settings' ) );
        add_action( 'wp_ajax_wpv_switch_debug_check', array( $this, 'wpv_switch_debug_check' ) );
        add_action( 'wp_ajax_wpv_update_debug_mode_status', array( $this, 'wpv_update_debug_mode_status' ) );

        // Register Settings CSS
        wp_register_style( 'views-admin-css', WPV_URL_EMBEDDED . '/res/css/views-admin.css', array( 'toolset-font-awesome', 'toolset-colorbox', 'views-notifications-css', 'views-dialogs-css', 'select2' ), WPV_VERSION );
        // Register Settings JS
        wp_register_script( 'views-settings-js', WPV_URL . '/res/js/views_settings.js', array( 'jquery' ), WPV_VERSION, true );

        if ( is_admin() ) {
            add_action( 'admin_enqueue_scripts', array( $this, 'wpv_admin_enqueue_scripts' ) );
        }
    }

    function wpv_admin_enqueue_scripts( $hook ) {
        // Views settings script
        if ( $hook == 'views_page_views-settings' ) {
            wp_enqueue_style( 'views-admin-css' );
            wp_enqueue_script( 'views-settings-js' );
        }
    }

    ////////////////////////////////////////////////////////////////////////////
    //
    // Views Settings Page - GUI Code
    //
    ////////////////////////////////////////////////////////////////////////////

    /*
     * FIXME: These methods should have consistent naming.
     * TODO: These methods need some documentation.
     */

    function wpv_settings_admin() {
        // Which tab is selected?
        // First tab by default: features
        $tab = 'features';

        if ( isset( $_GET['tab'] ) && preg_match( '#^(features|compatibility|development)$#', $_GET['tab'], $selected_tab ) ) {
            $tab = $selected_tab[1];
        }
        ?>

        <div class="wrap">

            <div id="icon-views" class="icon32"><br /></div>
            <h2><?php _e( 'Views Settings', 'wpv-views' ) ?></h2>

            <?php
            /*
             * Tab code is done by hand because WordPress tab classes and functions are marked as private
             */
            ?>
            <!-- tabs -->
            <div class="wp-filter wpv-settings-filter">
                <ul class="filter-links wpv-settings-filter-links">
                    <li class="wpv-settings-tab-features">
                        <a href="<?php echo add_query_arg( array( 'tab' => 'features' ) ); ?>" class="     <?php echo $tab == 'features' ? 'current' : '' ?>">  
                            <?php _e( 'Features', 'wpv-views' ); ?>
                        </a> 
                    </li>
                    <li class="wpv-settings-tab-compatibility">
                        <a href="<?php echo add_query_arg( array( 'tab' => 'compatibility' ) ); ?>" class="<?php echo $tab == 'compatibility' ? 'current' : '' ?>">                           
                            <?php _e( 'Compatibility', 'wpv-views' ); ?>
                        </a> 
                    </li>
                    <li class="wpv-settings-tab-development">
                        <a href="<?php echo add_query_arg( array( 'tab' => 'development' ) ); ?>" class="  <?php echo $tab == 'development' ? 'current' : '' ?>">                             
                            <?php _e( 'Development', 'wpv-views' ); ?>
                        </a> 
                    </li>
                </ul>
            </div>
            <!-- /tabs -->

            <div class="wpv-settings-tab-content">
                <?php do_action( "wpv_action_views_settings_{$tab}_section", $this ); ?>
            </div>

            <?php do_action( "wpv_action_views_settings_sections", $this ); ?>

        </div>

        <?php
    }

    function wpv_map_plugin_options( $options ) {
        ?>

        <div class="wpv-setting-container">
            <div class="wpv-settings-header">
                <h3><?php _e( 'Map plugin', 'wpv-views' ); ?></h3>
            </div>
            <div class="wpv-setting">
                <p>
                    <?php _e( "Enabling Views Maps will add the Google Maps API and the Views Maps plugin to your site.", 'wpv-views' ); ?>

                </p>
                <p>
                    <?php _e( 'This will let you create maps on your site and use Views to plot WordPress posts on a Google Map.', 'wpv-views' ); ?>
                </p>
                <p>
                    <?php echo sprintf( __( 'You can get the details in the <a href="%s" title="Documentation on the Views Maps plugin">documentation page</a>.', 'wpv-views' ), 'http://wp-types.com/documentation/user-guides/map-wordpress-posts/?utm_source=viewsplugin&utm_campaign=views&utm_medium=views-settings&utm_term=documentation page' ); ?>
                </p>
                <div class="js-map-plugin-form">
                    <p>
                        <label>
                            <input type="checkbox" name="wpv-map-plugin" class="js-wpv-map-plugin" value="1" <?php checked( $this->wpv_map_plugin ); ?> />
                            <?php _e( "Enable Views Map Plugin", 'wpv-views' ); ?>
                        </label>
                    </p>
                    <?php
                    wp_nonce_field( 'wpv_map_plugin_nonce', 'wpv_map_plugin_nonce' );
                    ?>
                </div>

                <p class="update-button-wrap">
                    <span class="js-wpv-map-plugin-update-message toolset-alert toolset-alert-success hidden">
                        <?php _e( 'Settings saved', 'wpv-views' ); ?>
                    </span>
                    <button class="js-wpv-map-plugin-settings-save button-secondary" disabled="disabled">
                        <?php _e( 'Save', 'wpv-views' ); ?>
                    </button>
                </p>

            </div>
        </div>

        <?php
    }

    function wpv_show_hidden_custom_fields_options( $options ) {
        global $WP_Views;

        $options = array();

        if ( isset( $this->wpv_show_hidden_fields ) && $this->wpv_show_hidden_fields != '' ) {
            $defaults = explode( ',', $this->wpv_show_hidden_fields );
        } else {
            $defaults = array();
        }
        ?>

        <div class="wpv-setting-container wpv-settings-hidden-cf">

            <div class="wpv-settings-header">
                <h3><?php _e( 'Hidden custom fields', 'wpv-views' ); ?></h3>
            </div>

            <div class="wpv-setting">

                <div class="js-cf-summary">
                    <?php
                    $cf_exists = false;
                    if ( sizeof( $defaults ) > 0 ) {
                        $cf_exists = true;
                    }
                    ?>
                    <p class="js-cf-exists-message <?php echo $cf_exists ? '' : 'hidden'; ?>">
                        <?php _e( 'The following private custom fields are showing in the Views GUI:', 'wpv-views' ); ?>
                    </p>
                    <p class="js-no-cf-message <?php echo $cf_exists ? 'hidden' : ''; ?>">
                        <?php _e( 'No private custom fields are showing in the Views GUI.', 'wpv-views' ); ?>
                    </p>
                    <ul class="wpv-selected-cf-list wpv-taglike-list  js-selected-cf-list <?php echo $cf_exists ? '' : 'hidden'; ?>">
                        <?php foreach ( $defaults as $cf ): ?>
                            <li><?php echo $cf ?></li>
                        <?php endforeach; ?>
                    </ul>

                    <p>
                        <button class="button-secondary js-show-cf-list" type="button"><?php _e( 'Edit', 'wpv-views' ); ?></button>
                        <span class="toolset-alert toolset-alert-success hidden js-cf-update-message">
                            <?php _e( 'Settings saved', 'wpv-views' ); ?>
                        </span>
                    </p>

                </div>

                <div class="js-cf-toggle hidden">

                    <?php $meta_keys = $WP_Views->get_meta_keys( true ); ?>
                    <ul class="cf-list wpv-mightlong-list js-all-cf-list">
                        <?php foreach ( $meta_keys as $key => $field ): ?>
                            <?php if ( strpos( $field, '_' ) === 0 ): ?>
                                <?php
                                $options[$field]['#default_value'] = in_array( $field, $defaults );
                                $element = wpv_form_control( array( 'field' => array(
                                        '#type' => 'checkbox',
                                        '#name' => 'wpv_show_hidden_fields[]',
                                        '#attributes' => array( 'style' => '' ),
                                        '#inline' => true,
                                        '#title' => $field,
                                        '#value' => $field,
                                        '#before' => '',
                                        '#after' => '',
                                        '#default_value' => in_array( $field, $defaults )
                                    ) ) );
                                ?>
                                <li><?php echo $element ?></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <?php wp_nonce_field( 'wpv_show_hidden_custom_fields_nonce', 'wpv_show_hidden_custom_fields_nonce' ); ?>
                    </ul>

                    <p class="update-button-wrap">
                        <span class="js-cf-spinner spinner hidden"></span>
                        <button class="button-secondary js-hide-cf-list"><?php _e( 'Cancel', 'wpv-views' ); ?></button>
                        <button class="button-primary js-save-cf-list"><?php _e( 'Save', 'wpv-views' ); ?></button>
                    </p>

                </div>
            </div>
        </div>

        <?php
    }

    function wpv_bootstrap_options( $options ) {
        $disabled = '';
        $disabled_message = '';
        if ( class_exists( 'WPDD_Layouts_CSSFrameworkOptions' ) ) {
            $disabled = ' disabled="disabled" readonly="readonly" ';
            $framework = WPDD_Layouts_CSSFrameworkOptions::getInstance()->get_current_framework();
            $disabled_message = '<p><strong>' . sprintf( __( "Bootstrap version overriden by the Layouts plugin settings to use Bootstrap v.%s", 'wpv-views' ), str_replace( 'bootstrap-', '', $framework ) ) . '</strong></p>';
        }
        ?>

        <div class="wpv-setting-container">
            <div class="wpv-settings-header">
                <h3><?php _e( 'Bootstrap Layouts', 'wpv-views' ); ?></h3>
            </div>
            <div class="wpv-setting">
                <p>
                    <?php _e( "You can set here the Bootstrap version that you want to use, based on the one <strong>provided by your theme</strong>. We will then generate the right markup in the Views Layouts Wizard when using the Bootstrap Grid option.", 'wpv-views' ); ?>
                </p>
                <?php echo $disabled_message; ?>
                <p>
                    <?php echo sprintf( __( 'You can get the details in the <a href="%s" title="Documentation on the Bootstrap Layouts">documentation page</a>.', 'wpv-views' ), 'http://wp-types.com/documentation/user-guides/view-layouts-101/?utm_source=viewsplugin&utm_campaign=views&utm_medium=views-settings&utm_term=documentation page#bootstrap' ); ?>
                </p>
                <ul class="js-bootstrap-version-form">
                    <li>
                        <label>
                            <input type="radio" name="wpv-bootstrap-version" class="js-wpv-bootstrap-version" value="1" <?php
                            checked( $this->wpv_bootstrap_version == 1 );
                            disabled( $disabled );
                            ?> />
                                   <?php _e( "Bootstrap version not set", 'wpv-views' ); ?>
                        </label>
                    </li>
                    <li>
                        <label>
                            <input type="radio" name="wpv-bootstrap-version" class="js-wpv-bootstrap-version" value="2" <?php
                            checked( $this->wpv_bootstrap_version == 2 );
                            disabled( $disabled );
                            ?> />
                                   <?php _e( "Bootstrap v. 2.0", 'wpv-views' ); ?>
                        </label>
                    </li>
                    <li>
                        <label>
                            <input type="radio" name="wpv-bootstrap-version" class="js-wpv-bootstrap-version" value="3" <?php
                            checked( $this->wpv_bootstrap_version == 3 );
                            disabled( $disabled );
                            ?> />
                                   <?php _e( "Bootstrap v. 3.0", 'wpv-views' ); ?>
                        </label>
                    </li>
                </ul>
                <?php
                wp_nonce_field( 'wpv_bootstrap_version_nonce', 'wpv_bootstrap_version_nonce' );
                ?>
                <p class="update-button-wrap">
                    <span class="js-wpv-bootstrap-version-update-message toolset-alert toolset-alert-success hidden">
                        <?php _e( 'Settings saved', 'wpv-views' ); ?>
                    </span>
                    <button class="js-wpv-bootstrap-version-settings-save button-secondary" disabled="disabled">
                        <?php _e( 'Save', 'wpv-views' ); ?>
                    </button>
                </p>
            </div>
        </div>

        <?php
    }

    function wpv_custom_inner_shortcodes_options( $options ) {
        if ( isset( $this->wpv_custom_inner_shortcodes ) && $this->wpv_custom_inner_shortcodes != '' ) {
            $custom_shrt = $this->wpv_custom_inner_shortcodes;
        } else {
            $custom_shrt = array();
        }
        if ( !is_array( $custom_shrt ) ) {
            $custom_shrt = array();
        }
        ?>

        <div class="wpv-setting-container wpv-settings-shortcodes wpv-add-item-settings">

            <div class="wpv-settings-header">
                <h3><?php _e( 'Third-party shortcode arguments', 'wpv-views' ); ?></h3>
            </div>

            <div class="wpv-setting">

                <div class="js-custom-inner-shortcodes-summary">

                    <p>
                        <?php _e( 'List of custom and third-party shortcodes you want to be able to use as Views shortcode arguments.', 'wpv-views' ); ?>
                    </p>
                    <p>
                        <?php _e( 'For example, to support [wpv-post-title id="[my-custom-shortcode]"] add <strong>my-custom-shortcode</strong> as a third-party shortcode argument below.', 'wpv-views' ); ?>
                    </p>
                    <p>
                        <?php echo sprintf( __( 'You can get the details in the <a href="%s" title="Documentation on the shortcodes within shortcodes">documentation page</a>.', 'wpv-views' ), 'http://wp-types.com/documentation/user-guides/shortcodes-within-shortcodes/?utm_source=viewsplugin&utm_campaign=views&utm_medium=views-settings&utm_term=documentation page' ); ?>
                    </p>
                    <div class="js-wpv-add-item-settings-wrapper">
                        <ul class="wpv-taglike-list js-wpv-add-item-settings-list js-custom-shortcode-list">
                            <?php
                            if ( count( $custom_shrt ) > 0 ) {
                                sort( $custom_shrt );
                                foreach ( $custom_shrt as $custom_shrtcode ) {
                                    ?>
                                    <li class="js-<?php echo $custom_shrtcode; ?>-item">
                                        <span class="">[<?php echo $custom_shrtcode; ?>]</span>
                                        <i class="icon-remove-sign js-custom-shortcode-delete" data-target="<?php echo $custom_shrtcode; ?>"></i>
                                    </li>
                                    <?php
                                }
                            }
                            ?>
                        </ul>
                        <form class="js-wpv-add-item-settings-form js-custom-inner-shortcodes-form-add">
                            <input type="text" placeholder="<?php _e( 'Shortcode name', 'wpv-views' ); ?>" class="js-wpv-add-item-settings-form-newname js-custom-inner-shortcode-newname" />
                            <button class="button button-secondary js-wpv-add-item-settings-form-button js-custom-inner-shortcodes-add" type="button" disabled><i class="icon-plus"></i> <?php _e( 'Add', 'wpv-views' ); ?></button>
                            <span class="toolset-alert toolset-alert-error hidden js-wpv-cs-error"><?php _e( 'Only letters, numbers, underscores and dashes', 'wpv-views' ); ?></span>
                            <span class="toolset-alert toolset-alert-info hidden js-wpv-cs-dup"><?php _e( 'That shortcode already exists', 'wpv-views' ); ?></span>
                            <span class="toolset-alert toolset-alert-info hidden js-wpv-cs-ajaxfail"><?php _e( 'An error ocurred', 'wpv-views' ); ?></span>
                        </form>
                    </div>
                    <?php wp_nonce_field( 'wpv_custom_inner_shortcodes_nonce', 'wpv_custom_inner_shortcodes_nonce' ); ?>

                </div>

            </div>
        </div>

        <?php
    }

    function wpv_custom_conditional_functions( $options ) {
        if ( isset( $this->wpv_custom_conditional_functions ) && $this->wpv_custom_conditional_functions != '' ) {
            $custom_func = $this->wpv_custom_conditional_functions;
        } else {
            $custom_func = array();
        }
        if ( !is_array( $custom_func ) ) {
            $custom_func = array();
        }
        ?>

        <div class="wpv-setting-container wpv-settings-functions wpv-add-item-settings">

            <div class="wpv-settings-header">
                <h3><?php _e( 'Functions inside conditional evaluations', 'wpv-views' ); ?></h3>
            </div>

            <div class="wpv-setting">

                <div class="js-custom-conditional-functions-summary">

                    <p>
                        <?php _e( 'List of functions and class methods that you want to be able to use as Views [wpv-if] evaluate argument.', 'wpv-views' ); ?>
                    </p>
                    <p>
                        <?php _e( 'For example, to support <em>my-function()</em> add <strong>my-function</strong> as a function name below. For class methods, use the syntax <strong>Class::method</strong>.', 'wpv-views' ); ?>
                    </p>
                    <p>
                        <?php echo sprintf( __( 'You can get the details in the <a href="%s" title="Documentation on using functions inside conditional evaluations">documentation page</a>.', 'wpv-views' ), 'http://wp-types.com/documentation/user-guides/conditional-html-output-in-views/?utm_source=viewsplugin&utm_campaign=views&utm_medium=views-settings&utm_term=documentation page#using-custom-functions' ); ?>
                    </p>
                    <div class="js-wpv-add-item-settings-wrapper">
                        <ul class="wpv-taglike-list js-wpv-add-item-settings-list js-custom-functions-list">
                            <?php
                            if ( count( $custom_func ) > 0 ) {
                                sort( $custom_func );
                                foreach ( $custom_func as $custom_function ) {
                                    ?>
                                    <li class="js-<?php echo str_replace( '::', '-_paamayim_-', $custom_function ); ?>-item">
                                        <span class=""><?php echo $custom_function; ?></span>
                                        <i class="icon-remove-sign js-custom-function-delete" data-target="<?php echo str_replace( '::', '-_paamayim_-', $custom_function ); ?>"></i>
                                    </li>
                                    <?php
                                }
                            }
                            ?>
                        </ul>
                        <form class="js-wpv-add-item-settings-form js-custom-conditional-functions-form-add">
                            <input type="text" placeholder="<?php _e( 'Function name', 'wpv-views' ); ?>" class="js-wpv-add-item-settings-form-newname js-custom-conditional-function-newname" />
                            <button class="button button-secondary js-wpv-add-item-settings-form-button js-custom-conditional-function-add" type="button" disabled><i class="icon-plus"></i> <?php _e( 'Add', 'wpv-views' ); ?></button>
                            <span class="toolset-alert toolset-alert-error hidden js-wpv-cs-error"><?php _e( 'Only letters, numbers, underscores and dashes', 'wpv-views' ); ?></span>
                            <span class="toolset-alert toolset-alert-info hidden js-wpv-cs-dup"><?php _e( 'That function already exists', 'wpv-views' ); ?></span>
                            <span class="toolset-alert toolset-alert-info hidden js-wpv-cs-ajaxfail"><?php _e( 'An error ocurred', 'wpv-views' ); ?></span>
                        </form>
                    </div>
                    <?php wp_nonce_field( 'wpv_custom_conditional_functions_nonce', 'wpv_custom_conditional_functions_nonce' ); ?>

                </div>

            </div>
        </div>

        <?php
    }

    // FIXME: Use wpv_ prefix
    function add_wpml_settings() {
        global $sitepress;
        ?>

        <?php if ( $sitepress ): ?>

            <div class="wpv-setting-container">

                <div class="wpv-settings-header">
                    <h3><?php _e( 'Translating with WPML', 'wpv-views' ); ?></h3>
                </div>

                <div class="wpv-setting">

                    <?php if ( defined( 'WPML_ST_VERSION' ) ): ?>

                        <p><?php _e( 'Congratulations! You are running Views and WPML with the String Translation module, so you can easily translate everything.', 'wpv-views' ); ?></p>
                        <p><?php _e( 'To translate static texts, wrap them in <strong>[wpml-string][/wpml-string]</strong> shortcodes.', 'wpv-views' ); ?></p>

                    <?php else: ?>

                        <p>
                            <?php _e( 'You are running Views and WPML, but missing the String Translation module.', 'wpv-views' ); ?>
                            <a href="http://wpml.org/download/wpml-string-translation/"><?php _e( 'The String Translation', 'wpv-views' ); ?></a>
                            <?php _e( 'allows translating static texts in your Views and Content Templates.', 'wpv-views' ); ?>
                        </p>

                    <?php endif; ?>

                    <?php $translatable_docs = array_keys( $sitepress->get_translatable_documents() ); ?>

                    <p><?php _e( 'How would you like to translate Content Templates?', 'wpv-views' ); ?></p>
                    <ul class="js-wpml-settings-form">
                        <li>
                            <label>
                                <input type="radio" name="wpv-content-template-translation" value="0" <?php
                                checked( !in_array( 'view-template', $translatable_docs ) );
                                ?>/> <?php _e( 'Use the same Content Templates for all languages', 'wpv-views' ); ?>
                            </label>
                        </li>
                        <li>
                            <label>
                                <input type="radio" name="wpv-content-template-translation" value="1" <?php
                                checked( in_array( 'view-template', $translatable_docs ) );
                                ?>/> <?php _e( 'Create different Content Templates for each language', 'wpv-views' ); ?>
                            </label>
                        </li>
                        <?php wp_nonce_field( 'wpv_wpml_settings_nonce', 'wpv_wpml_settings_nonce' ); ?>
                    </ul>

                    <p>
                        <?php _e( 'Need help?', 'wpv-views' ); ?> <a href="http://wp-types.com/documentation/multilingual-sites-with-types-and-views/?utm_source=viewsplugin&utm_campaign=views&utm_medium=edit-content-template-wpml-and-views-help&utm_term=Translating Views and Content Templates with WPML#3" target="_blank"> <?php _e( 'Translating Views and Content Templates with WPML', 'wpv-views' ); ?> &raquo; </a>
                    </p>

                    <p class="update-button-wrap">
                        <span class="js-wpml-update-message hidden toolset-alert toolset-alert-success">
                            <?php _e( 'Settings saved', 'wpv-views' ); ?>
                        </span>
                        <span class="js-wpml-spinner spinner hidden"></span>
                        <button class="js-save-wpml-settings button-secondary" disabled="disabled">
                            <?php _e( 'Save', 'wpv-views' ); ?>
                        </button>
                    </p>

                </div>

            </div>
        <?php endif; ?>
        <?php
    }

    function wpv_edit_view_frontend_links_options( $options ) {
        ?>

        <div class="wpv-setting-container">
            <div class="wpv-settings-header">
                <h3><?php _e( 'Frontend Edit Links', 'wpv-views' ); ?></h3>
            </div>
            <div class="wpv-setting">
                <p>
                    <?php _e( "You can enable/disable the edit links on the frontend for Views, Content Templates and WordPress Archives. Remember that the frontend edit links are only visible to administrators.", 'wpv-views' ); ?>
                </p>
                <ul class="js-bootstrap-version-form">
                    <li>
                        <label>
                            <input type="checkbox" name="wpv-show-edit-view-link" class="js-wpv-show-edit-view-link" value="1" <?php checked( $this->wpv_show_edit_view_link == 1 ); ?> />
                            <?php _e( "Enable edit links on the frontend", 'wpv-views' ); ?>
                        </label>
                    </li>

                </ul>
                <?php
                wp_nonce_field( 'wpv_show_edit_view_link_nonce', 'wpv_show_edit_view_link_nonce' );
                ?>
                <p class="update-button-wrap">
                    <span class="js-wpv-show-edit-view-link-update-message toolset-alert toolset-alert-success hidden">
                        <?php _e( 'Settings saved', 'wpv-views' ); ?>
                    </span>
                    <button class="js-wpv-show-edit-view-link-settings-save button-secondary" disabled="disabled">
                        <?php _e( 'Save', 'wpv-views' ); ?>
                    </button>
                </p>
            </div>
        </div>

        <?php
    }

    // FIXME: Use wpv_ prefix
    function admin_settings( $options ) {
        global $WPV_templates;

        $items_found = array();

        $options = $WPV_templates->legacy_view_settings( $options );

        if ( ! isset( $this->wpv_theme_function ) ) {
            $this->wpv_theme_function = '';
        }
        if ( ! isset( $this->wpv_theme_function_debug ) ) {
            $this->wpv_theme_function_debug = false;
        }

        $this->set( $options );
        ?>

        <div class="wpv-setting-container">
            <div class="wpv-settings-header">
                <h3><?php _e( 'Theme support for Content Templates', 'wpv-views' ); ?></h3>
            </div>
            <div class="wpv-setting">
                <p>
                    <?php _e( "Content Templates modify the content when called from", 'wpv-views' ); ?> <a href="http://codex.wordpress.org/Function_Reference/the_content">the_content</a>
                    <?php _e( "function. Some themes don't use", 'wpv-views' ); ?>  <a href="http://codex.wordpress.org/Function_Reference/the_content">the_content</a>
                    <?php _e( "function but define their own function.", 'wpv-views' ); ?>
                </p>
                <div class="js-debug-settings-form">
                    <p>
                        <?php _e( "If Content Templates don't work with your theme then you can enter the name of the function your theme uses here:", 'wpv-views' ); ?>
                    </p>
                    <input type="text" name="wpv_theme_function" value="<?php echo $this->wpv_theme_function; ?>" />
                    <p>
                        <?php _e( "Don't know the name of your theme function?", 'wpv-views' ); ?>
                    </p>
                    <p>
                        <label>
                            <input type="checkbox" name="wpv_theme_function_debug" value="1" <?php checked( $this->wpv_theme_function_debug ); ?> />
                            <?php _e( "Enable debugging and go to a page that should display a Content Template and Views will display the call function name.", 'wpv-views' ); ?>
                        </label>
                    </p>
                    <?php
                    wp_nonce_field( 'wpv_view_templates_theme_support', 'wpv_view_templates_theme_support' );
                    ?>
                </div>

                <p class="update-button-wrap">
                    <span class="js-debug-update-message toolset-alert toolset-alert-success hidden">
                        <?php _e( 'Settings saved', 'wpv-views' ); ?>
                    </span>
                    <span class="js-debug-spinner spinner hidden"></span>
                    <button class="js-save-debug-settings button-secondary" disabled="disabled">
                        <?php _e( 'Save', 'wpv-views' ); ?>
                    </button>
                </p>

            </div>
        </div>

        <?php
    }

    function wpv_debug_options( $options ) {
        ?>

        <div class="wpv-setting-container">
            <div class="wpv-settings-header">
                <h3 id="debug_mode"><?php _e( 'Debug mode', 'wpv-views' ); ?></h3>
            </div>
            <div class="wpv-setting">
                <p>
                    <?php _e( "Enabling Views debug will open a popup on every page showing a Views element.", 'wpv-views' ); ?>

                </p>
                <p>
                    <?php _e( 'This popup will show usefull information about the elements being displayed: time needed to render, memory used, shortcodes details...', 'wpv-views' ); ?>
                </p>
                <p>
                    <?php _e( 'There are two modes: compact and full. Compact mode will give you an overview of the elements rendered. The full mode will display a complete report with all the object involved on the page.', 'wpv-views' ); ?>
                </p>
                <p>
                    <?php echo sprintf( __( 'You can get the details in the <a href="%s" title="Documentation on the Views debug tool">documentation page</a>.', 'wpv-views' ), 'http://wp-types.com/documentation/user-guides/debugging-types-and-views/?utm_source=viewsplugin&utm_campaign=views&utm_medium=views-settings&utm_term=documentation page' ); ?>
                </p>
                <div class="js-debug-mode-form">
                    <p>
                        <label>
                            <input type="checkbox" name="wpv-debug-mode" class="js-wpv-debug-mode" value="1" <?php checked( $this->wpv_debug_mode ); ?> />
                            <?php _e( "Enable Views debug mode", 'wpv-views' ); ?>
                        </label>
                    <div class="js-wpv-debug-additional-options<?php echo empty( $this->wpv_debug_mode ) ? ' hidden' : ''; ?>">
                        <ul style="margin-left:30px;">
                            <li><label>
                                    <input type="radio" name="wpv_debug_mode_type" class="js-wpv-debug-mode-type" value="compact" <?php checked( $this->wpv_debug_mode_type == 'compact' ); ?> />
                                    <?php _e( "Compact debug mode", 'wpv-views' ); ?>
                                </label>
                            </li>
                            <li>
                                <label>
                                    <input type="radio" name="wpv_debug_mode_type" class="js-wpv-debug-mode-type" value="full" <?php checked( $this->wpv_debug_mode_type == 'full' ); ?> />
                                    <?php _e( "Full debug mode", 'wpv-views' ); ?>
                                </label>
                            </li>
                        </ul>
                        <?php
                        $debug_enabled = ( isset( $this->wpv_debug_mode ) && $this->wpv_debug_mode );
                        $debug_dismissed = ( isset( $this->dismiss_debug_check ) && $this->dismiss_debug_check == 'true' );
                        $debug_tested = ( isset( $_GET['action'] ) && esc_html( $_GET['action'] ) == 'test-debug' );
                        ?>
                        <div class="js-wpv-debug-checker<?php echo $debug_enabled ? '' : ' hidden'; ?>">
                            <?php if ( $debug_tested ) { ?>
                                <div class="js-wpv-debug-checker-results">
                                    <p><?php _e( 'Did a new window just open?', 'wpv-views' ); ?></p>
                                    <p><a class="js-wpv-debug-checker-success button-secondary"><?php _e( 'Yes, it worked', 'wpv-views' ); ?></a> <a href="#" class="js-wpv-debug-checker-failure button-secondary"><?php _e( 'No, nothing opened', 'wpv-views' ); ?></a></p>
                                    <a href="#" class="js-open-test-popup-trigger hidden"></a>
                                    <script type="text/javascript">
                                        function wpv_open_test_popup() {
                                            var popupWidth = (screen.width / 2),
                                                    popupHeight = (screen.height / 2),
                                                    popupPosLeft = (screen.width / 2) - (popupWidth / 2),
                                                    popupPosTop = (screen.height / 2) - (popupHeight / 2);
                                            var winobj = window.open(
                                                    '',
                                                    'blank',
                                                    'resizable=yes,scrollbars=yes,status=no,location=no,width=' + popupWidth + ',height=' + popupHeight + ',left=' + popupPosLeft + ',top=' + popupPosTop
                                                    );
                                            var debug_info = jQuery('.js-debuger-test-output').clone().html();//alert(debug_info);
                                            winobj.document.write(debug_info);
                                        }
                                        jQuery(document).on('click', '.js-open-test-popup-trigger', function (e) {
                                            e.preventDefault();
                                            wpv_open_test_popup();
                                        });
                                        jQuery(document).ready(function () {
                                            jQuery('.js-open-test-popup-trigger').click();
                                        });
                                    </script>
                                </div>
                                <p class="js-wpv-debug-checker-message-success js-wpv-debug-checker-after toolset-alert toolset-alert-success hidden">
                                    <?php _e( 'Views debugger is ready for use. Thanks for checking it', 'wpv-views' ); ?>
                                </p>
                                <div class="js-wpv-debug-checker-message-failure js-wpv-debug-checker-after toolset-alert toolset-alert-error hidden">
                                    <p><?php _e( 'Seems like your browser is blocking popups. You should allow all popup windows from this site to use the Views debugger.', 'wpv-views' ); ?></p>
                                    <p><?php _e( 'Please refer to the following links for documentation related to the most used browsers:' ); ?></p>
                                    <p>
                                        <a href="http://mzl.la/MyNqBe">Mozilla Firefox</a> &bull; 
                                        <a href="http://windows.microsoft.com/en-us/internet-explorer/ie-security-privacy-settings">Internet Explorer</a> &bull; 
                                        <a href="https://support.google.com/chrome/answer/95472">Google Chrome</a> &bull; 
                                        <a href="http://www.opera.com/help/tutorials/personalize/content/#siteprefs">Opera</a>
                                    </p>
                                </div>
                            <?php } ?>
                            <p class="js-wpv-debug-checker-before<?php echo ( $debug_dismissed || $debug_tested ) ? ' hidden' : ''; ?>"><?php _e( 'Views debugger will need to open a popup window. Your browser may block it, so let\'s check that it\'s working for you.', 'wpv-views' ); ?></p>

                            <p class="js-wpv-debug-checker-actions<?php echo ( $debug_dismissed || $debug_tested ) ? ' hidden' : ''; ?>"><button data-target="<?php echo admin_url(); ?>admin.php?page=views-settings&amp;action=test-debug&amp;timestamp=<?php echo current_time( 'timestamp' ); ?>#debug_mode" class="js-wpv-debug-checker-action button-primary"><?php _e( 'Test the debugger window', 'wpv-views' ); ?></button> <button href="<?php echo admin_url(); ?>admin.php?page=views-settings" class="js-wpv-debug-checker-dismiss button-secondary"><?php _e( 'It\'s OK, skip this test', 'wpv-views' ); ?></button></p>

                            <p class="js-wpv-debug-checker-enabler<?php echo ( $debug_dismissed && !$debug_tested ) ? '' : ' hidden'; ?>"><a href="<?php echo admin_url(); ?>admin.php?page=views-settings" class="js-wpv-debug-checker-recover" title="<?php _e( 'Test debugger window', 'wpv-views' ); ?>"><?php _e( 'Test debugger window', 'wpv-views' ); ?></a></p>
                        </div><!-- js-wpv-debug-checker -->
                    </div><!-- close .js-wpv-debug-additional-options -->

                    </p>
                    <?php
                    wp_nonce_field( 'wpv_debug_mode_option', 'wpv_debug_mode_option' );
                    ?>
                </div><!--  close .js-debug-mode-form -->

                <p class="update-button-wrap">
                    <span class="js-debug-mode-update-message toolset-alert toolset-alert-success hidden">
                        <?php _e( 'Settings saved', 'wpv-views' ); ?>
                    </span>
                    <button class="js-save-debug-mode-settings button-secondary" disabled="disabled">
                        <?php _e( 'Save', 'wpv-views' ); ?>
                    </button>
                </p>

                <div class="wpv-hidden" style="display:none">
                    <div class="js-debuger-test-output">
                        <div>
                            <h1><?php _e( 'It works!', 'wpv-views' ); ?></h1>
                            <p><?php _e( 'You can close this window and click \'Yes, it worked\' in the Views settings page.', 'wpv-views' ); ?></p>
                        </div>
                    </div> <!-- .js-debuger-output -->
                </div>

            </div>
        </div>

        <?php
    }

    ////////////////////////////////////////////////////////////////////////////
    //
    // Views Settings Page - AJAX Update Methods
    //
    ////////////////////////////////////////////////////////////////////////////

    /*
     * FIXME: Use consistent naming in update methods.
     * FIXME: Document these methods.
     */
    
    function wpv_update_map_plugin_status() {

        if ( current_user_can('manage_options') && isset( $_POST['wpv_map_plugin_nonce'] ) && wp_verify_nonce( $_POST['wpv_map_plugin_nonce'], 'wpv_map_plugin_nonce' ) ) {

            if ( !isset( $_POST['wpv_map_plugin_status'] ) || preg_match( '#^(|0|1)$#', $_POST['wpv_map_plugin_status'] ) ) {

                $this->wpv_map_plugin = isset( $_POST['wpv_map_plugin_status'] ) ? (int)$_POST['wpv_map_plugin_status'] : 0;
                $this->save();
                die( 'ok' );
                
            } else {
                die( 'error' );
            }
        } else {
            die( "Security check" );
        }
    }

    function wpv_get_show_hidden_custom_fields() {

        if ( current_user_can('manage_options') && isset( $_POST['wpv_show_hidden_custom_fields_nonce'] ) && wp_verify_nonce( $_POST['wpv_show_hidden_custom_fields_nonce'], 'wpv_show_hidden_custom_fields_nonce' ) ) {

            if ( !isset( $_POST['wpv_show_hidden_fields'] ) || is_array( $_POST['wpv_show_hidden_fields'] ) ) {

                // FIXME: Should we check every field name?
                // FIXME: Should we escape commas?
                
                $this->wpv_show_hidden_fields = isset( $_POST['wpv_show_hidden_fields'] ) ? implode( ',', $_POST['wpv_show_hidden_fields'] ) : '';
                $this->save();
                $this->wpv_show_hidden_custom_fields_options( $this );
                die( /* ok */ );
                
            } else {
                die( 'error' );
            }
        } else {
            die( "Security check" );
        }
    }

    function wpv_update_bootstrap_version_status() {
        
        if( current_user_can('manage_options') && isset($_POST['wpv_bootstrap_version_nonce'] ) && wp_verify_nonce( $_POST['wpv_bootstrap_version_nonce'], 'wpv_bootstrap_version_nonce' )) {
            
            /* 1 <= wpv_bootstrap_version_status <= 3 | 1 == 'no bootstrap' */
            if( !isset($_POST['wpv_bootstrap_version_status']) || preg_match('#^[123]$#', $_POST['wpv_bootstrap_version_status']) ) {
                
                $this->wpv_bootstrap_version = isset($_POST['wpv_bootstrap_version_status']) ? $_POST['wpv_bootstrap_version_status'] : 1;
                $this->save();
                die('ok');
                
            } else {
                die('error');
            }
        } else {
            die( "Security check" );
        }
    }

    function wpv_update_custom_inner_shortcodes() {

        if ( current_user_can('manage_options') && isset( $_POST['wpv_custom_inner_shortcodes_nonce'] ) && wp_verify_nonce( $_POST['wpv_custom_inner_shortcodes_nonce'], 'wpv_custom_inner_shortcodes_nonce' ) ) {

            if ( isset( $this->wpv_custom_inner_shortcodes ) && is_array( $this->wpv_custom_inner_shortcodes ) ) {
                $shortcodes = $this->wpv_custom_inner_shortcodes;
            } else {
                $shortcodes = array();
            }

            if ( isset( $_POST['csaction'] ) && isset( $_POST['cstarget'] ) ) {
                switch ( $_POST['csaction'] ) {
                    case 'add':
                        // Shortcode names: http://codex.wordpress.org/Shortcode_API#Names
                        if ( !in_array( $_POST['cstarget'], $shortcodes ) && preg_match( '#^[^ \t\r\n\x00\x20<>&\'"\[\]/]+$#', $_POST['cstarget'] ) ) {
                            $shortcodes[] = $_POST['cstarget'];
                        }
                        break;
                    case 'delete':
                        $key = array_search( $_POST['cstarget'], $shortcodes );
                        if ( $key !== false ) {
                            unset( $shortcodes[$key] );
                        }
                        break;
                }
                
                $this->wpv_custom_inner_shortcodes = $shortcodes;
                $this->save();
                die( 'ok' );
                
            } else {
                die( 'error' );
            }
        } else {
            die( "Security check" );
        }
    }

    function wpv_update_custom_conditional_functions() {
        if ( current_user_can('manage_options') && isset( $_POST['wpv_custom_conditional_functions_nonce'] ) && wp_verify_nonce( $_POST['wpv_custom_conditional_functions_nonce'], 'wpv_custom_conditional_functions_nonce' ) ) {
            
            if ( isset( $this->wpv_custom_conditional_functions ) && is_array( $this->wpv_custom_conditional_functions ) ) {
                $functions = $this->wpv_custom_conditional_functions;
            } else {
                $functions = array();
            }

            if ( isset( $_POST['csaction'] ) && isset( $_POST['cstarget'] ) ) {
                switch ( $_POST['csaction'] ) {
                    case 'add':
                        if ( !in_array( $_POST['cstarget'], $functions ) ) {
                            $functions[] = $_POST['cstarget'];
                        }
                        break;
                    case 'delete':
                        $target = str_replace( '-_paamayim_-', '::', $_POST['cstarget'] );
                        $key = array_search( $target, $functions );
                        if ( $key !== false ) {
                            unset( $functions[$key] );
                        }
                        break;
                }
                
                $this->wpv_custom_conditional_functions = $functions;
                $this->save();
                die( 'ok' );
                
            } else {
                die( 'error' );
            }
        } else {
            die( 'Security check' );
        }
    }

    function wpv_save_wpml_settings() {

        if ( current_user_can('manage_options') && isset( $_POST['wpv_wpml_settings_nonce'] ) && wp_verify_nonce( $_POST['wpv_wpml_settings_nonce'], 'wpv_wpml_settings_nonce' ) ) {

            if ( !isset( $_POST['wpv-content-template-translation'] ) || is_integer( $_POST['wpv-content-template-translation'] ) ) {

                global $sitepress;

                $iclsettings['custom_posts_sync_option']['view-template'] = intval( $_POST['wpv-content-template-translation'] );
                if ( intval( $_POST['wpv-content-template-translation'] ) ) {
                    $sitepress->verify_post_translations( 'view-template' );
                }

                if ( !empty( $iclsettings ) ) {
                    $sitepress->save_settings( $iclsettings );
                }

                die( 'ok' );
                
            } else {
                die( 'error' );
            }
        } else {
            die( "Security check" );
        }
    }

    function wpv_update_show_edit_view_link_status() {

        if ( current_user_can('manage_options') && isset( $_POST['wpv_show_edit_view_link_nonce'] ) && wp_verify_nonce( $_POST['wpv_show_edit_view_link_nonce'], 'wpv_show_edit_view_link_nonce' ) ) {

            if ( !isset( $_POST['wpv_show_edit_view_link_status'] ) || preg_match( '#^(|0|1)$#', $_POST['wpv_show_edit_view_link_status'] ) ) {

                $this->wpv_show_edit_view_link = isset( $_POST['wpv_show_edit_view_link_status'] ) ? (int)$_POST['wpv_show_edit_view_link_status'] : 0;
                $this->save();
                die( 'ok' );
                
            } else {
                die( 'error' );
            }
        } else {
            die( "Security check" );
        }
    }

    function wpv_save_theme_debug_settings() {
        
        if ( current_user_can('manage_options') && isset( $_POST['wpv_view_templates_theme_support'] ) && wp_verify_nonce( $_POST['wpv_view_templates_theme_support'], 'wpv_view_templates_theme_support' ) ) {
            
            global $WPV_templates;
			// @todo review this submit, we want to deprecate it
            $WPV_templates->submit( $this );
            
            $this->save();
            die( 'ok' );
            
        } else {
            die( "Security check" );
        }
    }

    function wpv_update_debug_mode_status() {
        
        if ( current_user_can('manage_options') && isset( $_POST['wpv_debug_mode_option'] ) && wp_verify_nonce( $_POST['wpv_debug_mode_option'], 'wpv_debug_mode_option' ) ) {

            if ( !isset( $_POST['debug_status'] ) || preg_match( '#^(|0|1)$#', $_POST['debug_status'] ) ) {

                $this->wpv_debug_mode = isset( $_POST['debug_status'] ) ? (int) $_POST['debug_status'] : 0;

                if ( !isset( $_POST['wpv_debug_mode_type'] ) || preg_match( '#^(compact|full)$#', $_POST['wpv_debug_mode_type'] ) ) {
                    
                    $this->wpv_debug_mode_type = isset( $_POST['wpv_debug_mode_type'] ) ? $_POST['wpv_debug_mode_type'] : '';
                }

                $this->save();
                die( 'ok' );
                
            } else {
                die( 'error' );
            }
        } else {
            die( "Security check" );
        }
    }

    function wpv_switch_debug_check() {
        
        if ( current_user_can('manage_options') && isset( $_POST['wpnonce'] ) && wp_verify_nonce( $_POST['wpnonce'], 'wpv_debug_mode_option' ) ) {

            if ( !isset( $_POST['result'] ) || preg_match( '#^(dismiss|recover|)$#', $_POST['result'] ) ) {
                
                $this->dismiss_debug_check = isset( $_POST['result'] ) && $_POST['result'] == 'recover' ? /* recover */ 'false' : /* dismiss */ 'true';
                $this->save();
                die( 'ok' );
                
            } else {
                die( 'error' );
            }
        } else {
            die( "Security check" );
        }
    }

}
