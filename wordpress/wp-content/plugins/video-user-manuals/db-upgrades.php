<?php

class VUM_DB_Upgrader{

    // Storage of the parent object.
    private $vum;
    
    function __construct( $vum ) {
        
        // Store the VUM object
        $this->vum = $vum;
         
        // What version are we currently rolling with in the DB
        $stored_version = get_option( $vum->get_ver_key() );
        
        // Get the current version of this plugin
        $current_version = $vum->get_plugin_ver();

        // If they don't match, need to do something & update the DB.
        if ( $stored_version !== $current_version ) {
            
            $this->upgrade( $stored_version, $current_version );
            update_option( $vum->get_ver_key(), $current_version );
        }
    }

    public function upgrade( $from, $to ) {
        
        // Upgrading from pre 2.3 "from" wouldn't exist. 
        if ( $from === FALSE ) {
            $this->two_three_upgrade();
        }
    }
    
    /**
     * Upgrades for new 2.3 plugin.
     * ============================
     * 
     * Plugin needs to "phone home" and check the license type and update the DB.
     * This is for compliance with new hosting provider serial numbers. 
     * 
     */
    public function two_three_upgrade() {

        // Post this serial back to VUM to check what kind of license this is.
        $json = json_encode( array( 'command' => 'check_license_type', 'license' => $this->vum->serial, 'domain' => $this->vum->domain ) );
        
        $response = wp_remote_post( apply_filters( 'vum_api', $this->vum->get_api_url() ), array(
            'method' => 'POST',
            'timeout' => 3,
            'redirection' => 0,
            'httpversion' => '1.0',
            'blocking' => true,
            'body' => array( 'json' => $json ),
            )
        );

        $api_reply = json_decode( wp_remote_retrieve_body( $response ) );
        
        if( is_object( $api_reply ) && $api_reply->license_type == 'host' ) {
            
            // If this license is a host, add the option so we know.
            add_option( 'wpm_o_host', true );
            
            // Send back to the API to make sure this domain is noted.
            $json = json_encode( array( 'command' => 'notify_host_domain', 'license' => $this->vum->serial, 'domain' => $this->vum->domain, 'wpurl' => get_bloginfo( 'wpurl' ) ) );

			$response = wp_remote_post( apply_filters( 'vum_api', $this->vum->get_api_url() ), array(
                'method' => 'POST',
                'timeout' => 3,
                'redirection' => 0,
                'httpversion' => '1.0',
                'blocking' => true,
                'body' => array( 'json' => $json ),
                )
            );
        }
    }

}