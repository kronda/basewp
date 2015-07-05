<?php

/**
* wpv-admin-notices.php
*
* Handle admin notices
*
* Admin notices can belong to one on two types: user admin notices or global admin notices.
*     User admin notices are dismissed in a usermeta field _wpv_dismissed_notices
*     Gobal admin notices are dismissed in an option setting _wpv_global_dismissed_notices
*
* @since 1.6.2
*/

if ( defined( 'WPT_ADMIN_NOTICES' ) ) {
    return; 
}

define( 'WPT_ADMIN_NOTICES', true );

/**
* WPToolset_Admin_Notices
*
* Methods for handling admin notices
*
* @since 1.6.2
*/

class WPToolset_Admin_Notices {
	
	function __construct() {
		add_action( 'init', array( $this, 'register_admin_notices' ) );
		add_action( 'admin_notices', array( $this, 'display_admin_notices' ) );
		add_action( 'admin_init', array( $this, 'ignore_admin_notice' ) );
	}
	
	/**
	* register_admin_notices
	*
	* Here we hook our internal notices
	*
	* @since 1.6.2
	*/
	
	function register_admin_notices() {
		// Commented for Views 1.7 (by Gen) because this admin_notice should already have been see by anyone interested
		// Uses a deprecated method
        // add_filter( 'wptoolset_filter_admin_notices', array( $this, 'wpv_if_changes' ) );
		// Global notice when WooCommerce is active but WooCommerce Views is not
		add_filter( 'wptoolset_filter_admin_notices', array( $this, 'wpv_woocommerce_recommend_woocommerceviews' ) );
	}
	
	function wpv_if_changes( $notices ) {
		if ( isset( $_GET['page'] ) && $_GET['page']=='views-update-help' ) {
			return $notices;
		} else if ( current_user_can( 'activate_plugins' ) ) {
			global $current_user;
			$user_id = $current_user->ID;
			if ( ! get_user_meta( $user_id, 'wpv_wpv_if_changes_ignore_notice' ) ) {
				$notice_text = sprintf(
					__( 'The last Views update fixed one important security issue which may require some extra action | <a href="%1s">More details</a> | <a class="js-wpv-dismiss" href="%2s">Dismiss</a>.', 'wpv-views' ),
					admin_url('admin.php?page=views-update-help&help-subject=wpv-if'),
					add_query_arg( array( 'wpv_wpv_if_changes_ignore' => 'yes' ) )
				);
				$args = array(
					'notice_class' => 'error',
					'notice_text' => $notice_text
				);
				$notices['wpv_if_changes'] = $args;
			}
		}
		return $notices;
	}
	
	/**
	* wpv_woocommerce_recommend_woocommerceviews
	*
	* Notice when WooCommerce is active but WooCommerce Views is not
	*
	* $type global
	* $id wc_active_wcv_missing
	*
	* @since 1.7
	*/
	
	function wpv_woocommerce_recommend_woocommerceviews( $notices ) {
		global $pagenow;
		if ( $pagenow == 'plugin-install.php' && isset( $_GET['tab'] ) && $_GET['tab']=='commercial' ) {
			return $notices;
		} else if ( current_user_can( 'activate_plugins' ) ) {
			$add_notice = false;
			$get_link = '';
			$dismissed_notices = get_option( '_wpv_global_dismissed_notices', array() );
			if ( ! is_array( $dismissed_notices ) || empty( $dismissed_notices ) ) {
				$dismissed_notices = array();
			}
			if ( isset( $dismissed_notices['wc_active_wcv_missing'] ) ) {
				$add_notice = false;
			} else {
				if ( class_exists( 'WooCommerce' ) ) {
					if ( ! class_exists( 'Class_WooCommerce_Views' ) ) {
						$add_notice = true;
						if ( class_exists( 'WP_Installer' ) ) {
							$get_link = admin_url( 'plugin-install.php?tab=commercial' );
						} else {
							$get_link = 'https://wp-types.com/account/';
						}
					}
				}
			}
			if ( $add_notice ) {
				$notice_text = __( 'In order to build templates for <strong>WooCommerce</strong> products, you need to have the <strong>WooCommerce Views</strong> plugin active. ', 'wpv-views' )
							. WPV_MESSAGE_SPACE_CHAR
							. __( '<strong>WooCommerce Views</strong> will add fields that are needed to design <strong>WooCommerce</strong> templates and product listings.', 'wpv-views' )
							. '<br /><br />'
							. sprintf(
								__( '<a class="button button-primary button-primary-toolset" href="%1s">Get WooCommerce Views</a> <a class="button button-secondary js-wpv-dismiss" href="%2s">Dismiss</a>', 'wpv-views' ),
								$get_link,
								add_query_arg( array( 'wpv_dismiss_global_notice' => 'wc_active_wcv_missing' )
							)
				);
				$args = array(
					'notice_class' => 'update-nag',
					'notice_text' => $notice_text
				);
				$notices['wc_active_wcv_missing'] = $args;
			}
		}
		return $notices;
	}
	
	/**
	* display_admin_notices
	*
	* Displays admin notices hooked into the wptoolset_filter_admin_notices filter
	*
	* @since 1.6.2
	*/
	
	function display_admin_notices() {
		
		$notices = array();
		/*
		* wptoolset_filter_admin_notices
		*
		* Filter to pass admin notices
		*
		* $notices is an array with the format:
		*	'notice_id' => $notice_data = array()
		*
		* $notice_data is an array with the format:
		*	'notice_class' => 'update'|'error'|custom (string) (defaults to 'update')
		*	'notice_text' => (string) (mandatory) (localized on origin)
		*/
		$notices = apply_filters( 'wptoolset_filter_admin_notices', $notices );
		
		if ( is_array( $notices ) ) {
			foreach ( $notices as $notice_id => $notice_data ) {
				if ( is_array( $notice_data ) ) {
					$notice_data_defaults = array(
						'notice_class' => 'updated',
						'notice_text' => '',
					);
					$notice_data = wp_parse_args( $notice_data, $notice_data_defaults );
					
					echo '<div id="' . $notice_id . '" class="message ' . esc_attr( $notice_data['notice_class'] ) . '"><p>';
					echo $notice_data['notice_text'];
					echo "</p></div>";
				}
			}
		}
	}
	
	/**
	* ignore_admin_notice
	*
	* Ignores admin notices based on URL parameters
	*
	* @since 1.6.2
	*/
	
	function ignore_admin_notice() {
		$notice_type = '';
		$dismissed_notices = '';
		$notice_id = '';
		
		if ( isset( $_GET['wpv_wpv_if_changes_ignore'] ) && 'yes' == $_GET['wpv_wpv_if_changes_ignore'] ) {
			global $current_user;
			$user_id = $current_user->ID;
			add_user_meta( $user_id, 'wpv_wpv_if_changes_ignore_notice', 'true', true );
		}
		
		/**
		* General case
		*
		* Dismisses user and global admin notices based on a URL parameter
		*
		* @since 1.7
		*/
		
		if ( isset( $_GET['wpv_dismiss_user_notice'] ) ) {
			global $current_user;
			$user_id = $current_user->ID;
			$notice_type = 'user';
			$notice_id = $_GET['wpv_dismiss_user_notice'];
			$dismissed_notices = get_user_meta( $user_id, '_wpv_dismissed_notices', true );
		} else if ( isset( $_GET['wpv_dismiss_global_notice'] ) ) {
			$notice_type = 'global';
			$notice_id = $_GET['wpv_dismiss_global_notice'];
			$dismissed_notices = get_option( '_wpv_global_dismissed_notices', array() );
		}
		$notice_id = sanitize_key( $notice_id );
		if ( empty( $notice_id ) ) {
			return;
		}
		if ( ! is_array( $dismissed_notices ) || empty( $dismissed_notices ) ) {
			$dismissed_notices = array();
		}
		$dismissed_notices[ $notice_id ] = 'yes';
		if ( $notice_type == 'user' ) {
			update_user_meta( $user_id, '_wpv_dismissed_notices', $dismissed_notices );
			// @todo remove this on Views 1.8 or 1.9, once we can be almost sure this entry has been deleted or when performing an upgrade routine
			delete_user_meta( $user_id, 'wpv_1304_types_notice' );
			delete_user_meta( $user_id, 'wpv_1304_cred_notice' );
		} else if ( $notice_type == 'global' ) {
			update_option( '_wpv_global_dismissed_notices', $dismissed_notices );
		}
	}

}

new WPToolset_Admin_Notices();