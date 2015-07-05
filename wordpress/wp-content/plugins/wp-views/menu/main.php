<div class="wrap">

    <div id="icon-views" class="icon32"><br /></div>
    <h2><?php _e('Views subscription', 'wpv-views') ?></h2>

    <?php
    
        global $views_plugins, $WPV_settings;

        if (isset($_POST['wpv_subscription_form_nonce'])
                && $_POST['wpv_subscription_form_nonce']
                == wp_create_nonce('wpv_subscription_form')) {

            $WPV_settings->subscription_email = trim($_POST['sub']['subscription_email'], ' ');
            $WPV_settings->subscription_key = trim($_POST['sub']['subscription_key'], ' ');
            $WPV_settings->save();
            
        }
        
        
        
        $args = new stdClass;
        $args->slug = 'views_all';

        $installed = get_plugins();
        // Filter Views plugins
        foreach ($installed as $key => $plugin) {
            if (!in_array($plugin['Name'], $views_plugins)) {
                unset($installed[$key]);
            }
        }
        // TODO Why use json decode?
        //$args->installed = json_encode($installed);
        $args->installed = $installed;

        $plugin_info = get_views_plugin_page(0, 'support_information', $args);
        if (isset($plugin_info->subscription['after'])
                && function_exists('is_multisite')
                && is_multisite()) {
            if (strpos($plugin_info->subscription['after'], 'href="plugins.php') !== false) {
                $plugin_info->subscription['after'] = str_replace('href="plugins.php', 'href="' . network_admin_url('plugins.php'), $plugin_info->subscription['after']);
            }
        }
        
        ?>

            <form id="wpv_subscription_form" method="post" action="">
            <?php wp_nonce_field('wpv_subscription_form', 'wpv_subscription_form_nonce'); ?>    
            <input type="hidden" name="wpv_support_account" value="create" />

        <p style="line-height:1.5"><?php @printf($plugin_info->subscription['before']); ?></p>


        <table class="form-table icl-account-setup">
            <tbody>
                <tr class="form-field">
                    <th scope="row"><?php _e('wp-types.com subscription email', 'wpv-views'); ?></th>
                    <td><input name="sub[subscription_email]" type="text" value="<?php echo isset($_POST['sub']['subscription_email']) ? $_POST['sub']['subscription_email'] : 
                        isset($WPV_settings->subscription_email) ? $WPV_settings->subscription_email : ''; ?>" /></td>
                </tr>
                <tr class="form-field">
                    <th scope="row"><?php _e('wp-types.com subscription key', 'wpv-views'); ?></th>
                    <td><input name="sub[subscription_key]" type="password" value="<?php echo isset($_POST['sub']['subscription_key']) ? $_POST['sub']['subscription_key'] :
                        isset($WPV_settings->subscription_key) ? $WPV_settings->subscription_key : ''; ?>" /></td>
                </tr>
                
            </tbody>
        </table>
        <p class="submit">
            <input type="hidden" name="save_sub" value="1" />
            <input class="button" name="save sub" value="<?php _e('Save subscription details', 'wpv-views'); ?>" type="submit" />
        </p>
        <div class="wpv_progress" style="display:none;"><?php _e('Saving. Please wait...', 'wpv-views'); ?></div>

        <?php @printf($plugin_info->subscription['after']); ?>

    </form>
    
</div>
