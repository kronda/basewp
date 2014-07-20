<?php
/*
Plugin Name: Video User Manuals
Plugin URI: http://www.videousermanuals.com/
Description: A complete video manual for your clients
Version: 2.3.2
Author: Video User Manuals Pty Ltd
Author URI: http://www.videousermanuals.com
*/

class Vum{

    const pluginver     = '2.3.2';
    const ver_key       = 'wpm_o_ver';
    const vum_domain    = 'http://wordpress.videousermanuals.com/';
    const iframe_url    = '//wordpress.videousermanuals.com/json.php?jsoncallback=?';
    const activate_url  = 'http://vum2.videousermanuals.com/activate.php?serial=';
    const profile_url   = 'http://vum2.videousermanuals.com/save-profile.php';
    const prefs_url     = 'http://vum2.videousermanuals.com/prefs.php?';
    const api_url       = 'http://vum2.videousermanuals.com/api.php';

    var $serial, $form, $formPrefs, $WPLang, $pluginURL; // Holds the form data

    function __construct() {

        // Set serial to use throughout
        $this->serial = get_option('wpm_o_user_id');

        // URL of the plugin folder, used from within views etc.
        $this->pluginURL = plugins_url( '', __FILE__ );

        // Get lang of what WP is running
        $lang = explode( '-', get_bloginfo('language') );
        $this->WPLang = $lang[0];

        // Set the WP Domain / site url and parse it
        $domain = parse_url( get_option( 'siteurl' ) );
        $this->domain = $domain[ 'host' ];

        add_action( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_links' ) );

        // Need to ensure the prefs are set as we need them to build the view if not in english!
        if( !get_option('wpm_o_form_prefs') ) {
            $this->update_prefs();
        }

        add_action( 'wp_head', array( $this, 'wp_head' ) );
        add_action( 'admin_head', array( $this, 'admin_head' ) );
        add_action( 'admin_menu', array( $this,'add_pages' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts') );
        add_action( 'admin_init', array( $this, 'admin_init' ) );
        add_action( 'install_plugins_pre_plugin-information' , array( $this, 'update_plugin' ) );

        register_activation_hook( __FILE__, array( $this, 'install' ) );
    }

    function wp_head() {

        // Only show if a child acount
        if( ! get_option( 'wpm_o_host' ) )
            return;

        // Show only on home page
        if( ! is_front_page() )
            return;

        // Output the version
        echo '<meta name="vum" content="' . self::pluginver. '" />' . "\n";
    }

	/**
	 * Enques JS we need in admin.
	 */
	function admin_scripts() {

		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui' );
		wp_enqueue_script( 'jquery-ui-tabs' );
    }

    /**
     *  Adds plugin action links used to reset.
     */
	function plugin_links( $links ) {

		$new_links   = array();
		$new_links[] = '<a href="' . admin_url( 'options-general.php?page=vum-reset' ) . '">' . $this->terms( 'Reset' ) . '</a>';

		return array_merge( $new_links, $links );
	}

	/**
	 * Outputs to admin_head
	 */
	function admin_head() {

		// This code is only required on WordPress 3.8+
		if ( version_compare( get_bloginfo( 'version' ), '3.8-beta', '<' ) ) {
			return;
		}

		// Base CSS dir
		$icon_base = esc_url( plugins_url( 'images/', __FILE__ ) );

		// Echo following CSS anywhere in wp-admin for nav.
		?>
		<style type="text/css" media="screen">
			#toplevel_page_video-user-manuals-plugin .wp-menu-image {
				background: url(<?php echo $icon_base; ?>/vum_flat_grey.png) no-repeat 10px !important;
			}

			#toplevel_page_video-user-manuals-plugin:hover .wp-menu-image {
				background: url(<?php echo $icon_base; ?>/vum_flat_blue.png) no-repeat 10px !important;
			}

			#toplevel_page_video-user-manuals-plugin .wp-menu-open .wp-menu-image {
				background: url(<?php echo $icon_base; ?>/vum_flat_white.png) no-repeat 10px !important;
			}
		</style> <?php
	}

	/**
	 * Helper function to access what version this plugin is.
	 * @return string
	 */
	function get_ver_key() {
        return self::ver_key;
    }

	/**
	 * Helper Function to return the constant.
	 * @return string
	 */
	function get_plugin_ver() {
		return self::pluginver;
	}

	/**
	 * Helper function to access the API URL.
	 * @return string
	 */
	function get_api_url() {
        return self::api_url;
    }

	/**
	 * Our admin_init calls.
	 */
	function admin_init() {
		// Load our DB upgrader class & run it.
		require_once 'db-upgrades.php';
		new VUM_DB_Upgrader( $this );

		// Register the style
		wp_register_style( 'vumcss', plugins_url( 'vum.css', __FILE__ ), array(), self::pluginver );

		// Check if just installed, redirect to activation.
		if ( get_option( 'wpm_o_just_installed' ) ) {
			delete_option( 'wpm_o_just_installed' );
			wp_redirect( admin_url( 'admin.php?page=vum-activation' ) );
			exit;
		}

		// If serial is set, but the lang isn't
		if ( $this->serial && ! get_option( 'wpm_o_lang' ) ) {

			// If the heading is there, then this must be legacy, so set lang to AU english, else, try WP default.
			if ( get_option( 'wpm_o_custom_video_title' ) ) {
				update_option( 'wpm_o_lang', 'en-au' );
			} else {
				$lang = explode( '-', strtolower( get_bloginfo( 'language' ) ) );
				$lang = ( $lang[0] == $lang[1] ? $lang[0] : strtolower( get_bloginfo( 'language' ) ) );
				update_option( 'wpm_o_lang', $lang );
			}
		}

	}

	/**
	 * Add Menu pages to WP-Admin
	 */
	function add_pages() {
		$location = ( get_option( 'wpm_o_move_menu_item' ) ? '2.1' : null );

		$custom_title = get_option( 'wpm_o_custom_menu_name' ) ? get_option( 'wpm_o_custom_menu_name' ) : $this->terms( 'Manual' );

		$access = get_option( 'wpm_o_view_access' ) ? 'edit_posts' : 'read';

		// If equal or less than 3.7, show old icon. For newer WP, show CSS space.
		$icon = version_compare( get_bloginfo( 'version' ), '3.8-beta', '>=' ) ? 'div' : plugins_url( 'images/vum-logo.png', __FILE__ );

		$view_page = add_menu_page(
			$custom_title,
			$custom_title,
			$access,
			__FILE__,
			array( $this, 'display' ),
			$icon,
			$location
		);

		add_action( 'load-' . $view_page, array( $this, 'pre_activation' ) );

		// Dont want child pages to show if no serial
		if ( $this->serial ) {

			add_submenu_page(
				__FILE__,
				$this->terms( 'Videos' ),
				$this->terms( 'Videos' ),
				$access,
				__FILE__,
				array( $this, 'display' )
			);

			if ( ! get_option( 'wpm_o_hide_manual' ) ) {
				add_submenu_page(
					__FILE__,
					$this->terms( 'User Manual' ),
					$this->terms( 'User Manual' ),
					$access,
					'vum-ebook',
					array( $this, 'ebook' )
				);
			}

			// If the setting is false (doesn't exist, or is set to no, OR there is a match.
			if ( ! get_option( 'wpm_o_user_menu_restrict' ) || get_option( 'wpm_o_user_menu_restrict' ) == get_current_user_id() ) {
				$admin_page = add_submenu_page(
					__FILE__,
					$this->terms( 'Manual Options' ),
					$this->terms( 'Manual Options' ),
					'activate_plugins',
					'vum-options',
					array( $this, 'admin' )
				);
				add_action( 'load-' . $admin_page, array( $this, 'admin_preload' ) );
				add_action( 'admin_print_styles-' . $admin_page, array( $this, 'admin_css' ) );
			}
		}

		/* Dont want these pages in the menu, but still register the name & actions */
		$reset_page = add_submenu_page( null, 'Reset', 'Reset VUM', 'activate_plugins', 'vum-reset', array( $this, 'reset' ) );
		add_action( 'load-' . $reset_page, array( $this, 'do_reset' ) );

		$activation_page = add_submenu_page( null, 'Activation', 'Activate VUM', 'activate_plugins', 'vum-activation', array( $this, 'activate' ) );
		add_action( 'load-' . $activation_page, array( $this, 'do_activate' ) );

		$embed_page = add_submenu_page( null, 'Video Player', 'Video Player', 'read', 'vum-embed', array( $this, 'embed' ) );
		add_action( 'load-' . $embed_page, array( $this, 'embed' ) );

	}

	/**
	 * Install action
	 */
	function install() {
        // Flag to know we need to finish setting  up VUM
        add_option( 'wpm_o_just_installed', true );

        // Add the plugin version so an upgrade doesn't occur.
        add_option( $this->get_ver_key(), self::pluginver );
    }

	/**
	 * Reset VUM Settings
	 */
	function do_reset() {
		$this->reset();

		wp_redirect( admin_url( 'admin.php?page=vum-activation' ) );
		exit;
	}

	/**
	 * Do the reset query.
	 */
	function reset() {
		global $wpdb;
		// Delete everything from wp_options where it's with our prefix.
		$wpdb->query( "delete from $wpdb->options where option_name like 'wpm_o_%'" );
	}

	/**
	 * Load our own CSS for admin.
	 */
	function admin_css() {
		wp_enqueue_style( 'vumcss' );
	}

	/**
	 * Used to display embeded videos.
	 */
	function embed() {

		require_once( 'views/embeded.php' );
		die;

	}

	/**
	 * Display the VUM videos.
	 */
	function display() {
		global $wpdb;

		$url                 = new stdClass;
		$url->user_id        = $this->serial;
		$url->plugin_version = self::pluginver;
		$url->wp_version     = get_bloginfo( 'version' );
		$url->lang           = get_option( 'wpm_o_lang' );

		$url->branding_img = get_option( 'wpm_o_branding_img' );
		$url->video_image  = get_option( 'wpm_o_custom_vid_placeholder' );

		$sections       = $wpdb->get_results( "select option_name, option_value from $wpdb->options where option_name not like 'wpm_o_show_video_%' and option_name like 'wpm_o_show_%'" );
		$sectionsToShow = '';

		// Get all sections we ARE showing.
		foreach ( $sections as $section ) {
			if ( $section->option_value == '1' ) {
				$sectionsToShow .= "\'" . str_replace( 'wpm_o_show_', '', $section->option_name ) . "\',";
			}
		}

		// Trim off last comma and put in URL array to pass to VUM.
		$url->sectionsToShow = substr_replace( $sectionsToShow, '', - 1 );

		// Get all videos we are NOT showing
		$videos     = $wpdb->get_results( "select option_name, option_value from $wpdb->options where option_name like 'wpm_o_show_video_%' and option_value = 0" );
		$vidsToHide = '';
		foreach ( $videos as $video ) {
			$vidsToHide .= str_replace( 'wpm_o_show_video_', '', $video->option_name ) . ',';
		}

		// Trim off last comma and put in URL array to pass to VUM.
		$url->vidsToHide = substr_replace( $vidsToHide, '', - 1 );

		$url_params = '';
		foreach ( $url as $k => $v ) {
			if ( $v === FALSE ) {
				$v = '0';
			}
			$url_params .= $k . ':\'' . $v . '\',';
		}

		/* Trim last character (a comma) from string */
		$url_params = substr_replace( $url_params, '', - 1 );

		/* Local Videos */

		$show_local = get_option( 'wpm_o_num_local' );

		if ( $show_local ) {

			$local_videos = array();
			$num_local    = get_option( 'wpm_o_num_local' );
			$local_title  = get_option( 'wpm_o_local_title' );
			$count        = 1;

			while ( $count <= $num_local ) {

				if ( get_option( 'wpm_o_localvideos_' . $count . '_loc' ) == 0 ) {

					$local_videos[$count]          = new stdClass();
					$local_videos[$count]->name    = get_option( 'wpm_o_localvideos_' . $count . '_0' );
					$local_videos[$count]->thumb   = get_option( 'wpm_o_localvideos_' . $count . '_1' );
					$local_videos[$count]->vid     = get_option( 'wpm_o_localvideos_' . $count . '_2' );
					$local_videos[$count]->desc    = get_option( 'wpm_o_localvideos_' . $count . '_3' );
					$local_videos[$count]->image   = get_option( 'wpm_o_localvideos_' . $count . '_4' );
					$local_videos[$count]->embed   = get_option( 'wpm_o_localvideos_' . $count . '_5' );
					$local_videos[$count]->doembed = get_option( 'wpm_o_localvideos_' . $count . '_6' );

				} else {

					$custom_local_videos[$count]          = new stdClass();
					$custom_local_videos[$count]->name    = get_option( 'wpm_o_localvideos_' . $count . '_0' );
					$custom_local_videos[$count]->thumb   = get_option( 'wpm_o_localvideos_' . $count . '_1' );
					$custom_local_videos[$count]->vid     = get_option( 'wpm_o_localvideos_' . $count . '_2' );
					$custom_local_videos[$count]->desc    = get_option( 'wpm_o_localvideos_' . $count . '_3' );
					$custom_local_videos[$count]->image   = get_option( 'wpm_o_localvideos_' . $count . '_4' );
					$custom_local_videos[$count]->embed   = get_option( 'wpm_o_localvideos_' . $count . '_5' );
					$custom_local_videos[$count]->doembed = get_option( 'wpm_o_localvideos_' . $count . '_6' );
					$custom_local_videos[$count]->loc     = get_option( 'wpm_o_localvideos_' . $count . '_loc' );

				}

				$count ++;
			}
		}

		require_once( 'views/videos.php' );
	}

	/**
	 *
	 * Helper function to display a VUM video.
	 *
	 * @param $video_id
	 * @param $video
	 *
	 * @return string/HTML
	 */
	function display_vid( $video_id, $video ) {
		$src = stripslashes( get_option( 'wpm_o_localvideos_' . $video_id . '_5' ) );
		preg_match( '/width="(\d+)(px)?" height="(\d+)(px)?"/', $src, $matches );

		$width  = ( isset( $matches[1] ) ? intval( $matches[1] ) : get_option( 'wpm_o_local_video_width' ) );
		$height = ( isset( $matches[3] ) ? intval( $matches[3] ) : get_option( 'wpm_o_local_video_height' ) );

		if ( $video->doembed == '1' ) {
			$onclick = 'window.open(\'' . admin_url( 'admin.php?page=vum-embed' ) . '&wpmvid=' . $video_id . '&amp;video_thumb=' . $video->image . '&amp;width=' . ( $width + 25 ) . '&amp;height=' . $height . '\',\'welcome\',\'width=' . ( $width + 50 ) . ', height=' . ( $height + 50 ) . ',menubar=0,status=0,location=0,toolbar=0,scrollbars=0\')';
		} else {
			$onclick = 'window.open(\'' . self::vum_domain . '/video-player.php?video_url=' . $video->vid . '&amp;video_thumb=' . $video->image . '&amp;width=' . ( $width + 25 ) . '&amp;height=' . $height . '\',\'welcome\',\'width=' . ( $width + 50 ) . ', height=' . ( $height + 50 ) . ',menubar=0,status=0,location=0,toolbar=0,scrollbars=0\')';
		}

		$var = '<div class="video-container">';
		$var .= '<a href="javascript:void(0)" onclick="' . $onclick . '">';
		$var .= '<img src="' . $video->thumb . '" alt="' . stripslashes( $video->name ) . '" width="240" height="150" /><br />';
		$var .= stripslashes( $video->name );
		$var .= '</a>';
		$var .= '</div>';

		return $var;
	}

	/**
	 * VUM EBook Admin Page
	 */
	function ebook() {
		$wpm_urlvars = "lang:'" . get_option( 'wpm_o_lang' ) . "',wp_version:'" . get_bloginfo( 'version' ) . "', user_id:'" . get_option( 'wpm_o_user_id' ) . "',custom_ebook_img:'" . get_option( 'wpm_o_custom_ebook_img' ) . "'";

		?>
		<div id="manual-page" class="wrap">


			<h2 style="margin-bottom:8px">
				<?php
				if ( get_option( 'wpm_o_plugin_custom_logo' ) ) {
					echo '<img src="' . get_option( 'wpm_o_plugin_custom_logo' ) . '" alt="logo" style="vertical-align: -7px">&nbsp; ';
				}

				echo get_option( 'wpm_o_plugin_heading_user' ); ?>
			</h2>

			<div id="ajax_msg"></div>
			<div id="ajax_content"></div>
			<script type="text/javascript">
				jQuery.getJSON('<?php echo is_ssl() ? str_replace( 'http://', 'https://', self::vum_domain ) : self::vum_domain; ?>/online-manual.php?jsoncallback=?',
					{<?php echo $wpm_urlvars;?>},
					function (data, textStatus) {
						jQuery('#ajax_content').append(data);
					});

			</script>
		</div>
	<?php
	}

    function defineForm()
    {
        // Updadate prefs
        $this->formPrefs = $this->update_prefs();

        // Only need this in admin.
        if(!is_admin())
            return;

        require_once('form.php');

        $form = new Vum_form( 'Video User Manuals Settings' );

        $form->setPluginURL($this->pluginURL);

        $form->openTab( 'Branding &amp; Customization' );

        $form->addDropdown(
                        'lang',
                        'Language',
                        'Which language / accent would you like?',
                        $this->formPrefs->langs,
                        strtolower( get_bloginfo('language') ) // Use WP Lang as default
                );

        $form->addRadioGroup(
                        'user_menu_restrict',
                        'Restrict VUM Settings',
                        'The settings will only appear for the user logged into this account. This allows you to give clients different admin accounts, and they will not see the settings for this plugin',
                        array( get_current_user_id() => 'Yes', 0 => 'No' ),
                        0
                );

        $form->addYesNo(
                        'hide_manual',
                        'Hide the User Manual',
                        'You can remove the written User Manual option from the admin sidebar if you want. ',
                        0
                );

        $form->addYesNo(
                        'move_menu_item',
                        'Menu up top?',
                        'The Menu item can go up under the dashboard rather than down the bottom',
                        0
                );

        $form->addYesNo(
                        'view_access',
                        'Hide from subscribers',
                        'Hide Video User Manuals from anyone who doesnt have edit_posts access.',
                        1
                );

        $form->addTextbox(
                        'custom_menu_name',
                        'Change Menu Name',
                        'Change the menu name in the sidebar from Manual to what ever you want.',
                        $this->terms( 'Manual' )
                );

        $form->addTextbox(
                        'plugin_heading_video',
                        'Heading on the videos page',
                        'Change the heading of the plugin from Manual to what you want.',
                        $this->terms( 'Manual' )
                );

        $form->addTextbox(
                        'plugin_heading_user',
                        'Custom User Manual Heading',
                        'Change the heading of the plugin from User Manual to what you want.',
                        $this->terms( 'User Manual' )
                );

        $form->addTextarea(
                        'intro_text',
                        'Custom Introduction Text',
                        'Change the introduction text for your clients if you want.',
                        $this->terms( 'intro', false )
                );

        $form->addTextbox(
                        'plugin_custom_logo',
                        'Custom Logo For Plugin Pages',
                        'Appears top left of pages next to heading. 32px high will look the best',
                        'http://vum.s3.amazonaws.com/wp/assets/vum-logo-32.png'
                );

        $form->addTextbox(
                        'custom_ebook_img',
                        'Custom Ebook Image',
                        'Change the ebook image. Please put in the full url including http://'
                );

        $form->addTextbox(
                        'branding_img',
                        'Custom Logo above video player',
                        'Absolute url to your logo. Max size 960 x 30px.',
                        'http://vum.s3.amazonaws.com/wp/assets/vum-logo.gif'
                );

        $form->addTextbox(
                        'custom_vid_placeholder',
                        'Custom Video Placeholder',
                        'This is the image that will appear before the video plays. Should be 960px x 600px. Must have http://'
                );

        $form->closeTab();

        $form->openTab( 'Videos' );

        foreach( $this->formPrefs->sections as $section ) {

            $key = strtolower(str_replace(' ','_',$section->title) );

            $form->addYesNo(
                            'show_' . $key,
                            'Show ' . $section->title . ' Videos',
                            '',
                            $section->showDefault
                    );

            if( $section->videos ) {

                $id = $form->openSection( '',  'show_' . $key );
                $form->addClass( $id, 'manual' );
                foreach( $section->videos as $video ) {
                        $form->addYesNo(
                                    'show_video_' . $video->video_id,
                                    'Show <em>' . $video->vidTitle . '</em>',
                                    ''
                            );
                }
                $form->closeSection();

            }

        }

        $form->closeTab();

        $form->openTab( 'Custom Videos' );

        $form->html( '<p>If you want the videos thumbnails to look consistent, then we recommend you use our <a href="http://vum.s3.amazonaws.com/wp/assets/thumbs-template.psd">Photoshop thumbnail template</a>.</p><br /> ');

        $form->addDropdown(
                'num_local',
                'Number of custom videos',
                'How many custom videos will you be storing',
                array(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20)
        );

        // Only show followiung three options is over 0 vids selected
        if( get_option( 'wpm_o_num_local' ) > 0 ) {

        $form->addTextbox(
                        'local_title',
                        'Title',
                        'Title to appear above all your videos',
                        'Introduction Videos'
                );

        $form->addTextbox(
                        'local_video_height',
                        'Popup Window Height',
                        'Height of your videos. Only have a number, do not include px.',
                        660
                );

        $form->addTextbox(
                        'local_video_width',
                        'Popup Window Width',
                        'Width of your videos. Only have a number, do not include px.',
                        900
                );

        }

        $count = 1;

        $locations = array('0' => 'At the Top' );

        foreach( $this->formPrefs->sections as $section ) {
            $locations[$section->id] = 'Inside ' . $section->title;
        }

        while($count <= get_option( 'wpm_o_num_local' ) )
        {
             $form->addHeading( 'Video ' . $count, 'h3' );

             $form->addTextbox(
                        'localvideos_' . $count . '_0',
                        'Your Video Name',
                        'The Name of Your Video'
                );

             $form->addDropdown(
                        'localvideos_' . $count . '_loc',
                        'Location of the video',
                        'This video can go in one of our sections, or in its own at the top',
                        $locations,
                        0
                );

             $id = $form->addYesNo(
                        'localvideos_' . $count . '_6',
                        'Use Embed Code',
                        'If you select yes, you can paste in your own HTML embed code. Eg YouTube etc.',
                        0
                );

             $form->addClass( $id, 'embed_selector' );

             $form->openSection( '', 'localvideos_' . $count . '_6' );

             $form->addTextarea(
                        'localvideos_' . $count . '_5',
                        'Custom Embed Code',
                        'You are able to embed your own code here. Such as YouTube, Vimeo, etc.'
                );

             $form->closeSection();

             $form->addTextbox(
                        'localvideos_' . $count . '_1',
                        'Small Video Thumbnail',
                        'The full URL of the small video thumbnail. 240px x 150px. Please include http://'
                );

             $id = $form->addTextbox(
                        'localvideos_' . $count . '_2',
                        'Video URL',
                        'The full URL of the video. Please include http://'
                );
             if( $form->getVal( 'localvideos_' . $count . '_6' ) )
                $form->addClass( $id, 'wpm-hidden' );

             $id = $form->addTextbox(
                        'localvideos_' . $count . '_3',
                        'Description Of Video',
                        'The description will appear as alt text for the thumbnail'
                );
             if( $form->getVal( 'localvideos_' . $count . '_6' ) )
                $form->addClass( $id, 'wpm-hidden' );

             $id = $form->addTextbox(
                        'localvideos_' . $count . '_4',
                        'Large Thumbnail Image',
                        'Large Thumbnail image for video. Should same size as video. Please include http://'
                );
             if( $form->getVal( 'localvideos_' . $count . '_6' ) )
                $form->addClass( $id, 'wpm-hidden' );

            $count++;
        }

        $form->closeTab();

        $form->openTab( 'Set Master Profile' );

        $form->html( '<p>If you would like to save theses settings as your "Master Profile" so you can reuse them on other sites, tick the box and click save. </p> ');
        $form->html( '<p> NB: This will overwrite your existing Master Profile, however it will not affect any sites you have previously setup with your Master Profile.</p>' );
        $form->html( '<input type="checkbox" name="set_master_profile" value="1" /> Set as master profile? <br />' );

        $form->closeTab();

        return $form;
    }

        function terms( $term = '' , $useTerm = true )
    {
        // Always passed in as eng - don't keep doing.
        if($this->WPLang=='en' && $useTerm ) {
            return $term;
        }

        $prefs = get_option( 'wpm_o_form_prefs' );

        // If we find a term list in this lang, look further
        if( isset( $prefs->terms->{$this->WPLang} ) )
        {
            if( $useTerm ) {
                $term_code = 'term_' . strtolower( str_replace( ' ', '_', $term ) );
            } else {
                $term_code = 'message_' . $term;
            }

            // IF that term exists, return that, else use the original (English)
            if( $prefs->terms->{$this->WPLang}->$term_code ) {
                return $prefs->terms->{$this->WPLang}->$term_code;
            }
        }

        return $term;
    }

    function admin()
    {
        // Define the form array
        $this->form = $this->defineForm();

        // If the custom menu name isn't set, they haven't saved anything yet.
        if( ! get_option('wpm_o_custom_menu_name') )
            $this->notice( 'You have to save your changes in order for the videos to appear' );

        if( isset( $_GET[ 'saved' ] ) )
            $this->notice( 'Settings saved.' );

        if( isset( $_GET[ 'master_profile' ] ) )
            $this->notice( 'Settings saved and your master profile has been updated' );

        // Display the form
        $this->form->display();

        self::load('admin');
    }

    function update_prefs()
    {
    	if(!$this->serial)
    		return;

        $url                    = new stdClass;
        $url->user_id           = $this->serial;
        $url->wp_lang           = $this->WPLang;
        $url->plugin_version    = self::pluginver;
        $url->wp_version        = get_bloginfo('version');
        $url->lang              = get_option('wpm_o_lang');
        $url->url               = $this->domain;

        $url_params = '';

        foreach($url as $k=>$v) {

           if($v===FALSE)
               $v='0';

            $url_params .= $k . '=' . $v . '&';

        }

        /* Trim last character (a comma) from string */
        $url_params = substr_replace($url_params ,'',-1);

        $response = wp_remote_get( apply_filters( 'vum_prefs', self::prefs_url . $url_params ) );

        if (is_wp_error($response))
        {
            update_option( 'wpm_o_form_prefs', 'error' );
        }
        else
        {
            $wpm_prefs = json_decode( wp_remote_retrieve_body( $response ) );
            update_option( 'wpm_o_form_prefs', $wpm_prefs );
        }

        return $wpm_prefs;
    }

    function pre_activation()
    {
        // Check there is a serial set.
        if(!$this->serial) {
            wp_redirect ( admin_url( 'admin.php?page=vum-activation' ) );
            die;
        }
    }

    function admin_preload()
    {
        // Check serial is set!
        $this->pre_activation();

        /* If not posting - go away */
        if( !isset($_POST) || empty($_POST) )
            return;

        if( ! wp_verify_nonce($_POST['vum_save'],'vum_nonce') )
            wp_die('VUM Nonce Failed');

        /* Define the form array */
        $this->form = $this->defineForm();

        /*
         * Loop through all fields that should be there and
         * update them in wp_options. If they don't exist at
         * this point, they'll be added.
         */

        global $wpdb;

        foreach( $this->form->fields() as $key => $val ) {
            if( isset( $_POST[ $key ] ) ) {
                update_option( $val['dbName'], $_POST[ $key ] );
            }
        }

        if( isset( $_POST['set_master_profile'] ) ) {

            // Get all settings stored in the options table prefixed with VUM's prefix.
            $settings = $wpdb->get_results("select option_name, option_value from $wpdb->options where option_name like 'wpm_o_%'");

            // Store in serialized array to pass back to VUM server.
            $settings = serialize($settings);

            // Post the settings to Video User Manuals server for storage and future use on your sites.
            $response = wp_remote_post( apply_filters( 'vum_profile', self::profile_url ), array(
                                                    'method' => 'POST',
                                                    'timeout' => 3,
                                                    'redirection' => 0,
                                                    'httpversion' => '1.0',
                                                    'blocking' => true,
                                                    'body' => array( 'settings' => $settings, 'serial' => $this->serial ),
                                                )
                                            );

            if( is_wp_error( $response ) )
            {
               // echo 'Something went wrong!';
            }

        }

        // If we saved master profile - set a URL flag.
        $master_profile_tag = ( isset( $_POST['set_master_profile'] ) ? '&master_profile=saved' : '' );

        if( isset( $_POST['return'] ) && $_POST['return'] )
            wp_redirect( $_POST['return'] . $master_profile_tag );
        else
            wp_redirect( remove_query_arg('saved') . '&saved=true' . $master_profile_tag );

        exit;

    }

    function do_activate()
    {
        /* If Submit - lets check the serial */
        if( isset($_POST['serial']) ) {

			$json = json_encode( array(
					'command'		 => 'activate',
					'license' 		 => trim( $_POST['serial'] ),
					'domain'		 => $this->domain,
					'plugin_version' => self::pluginver,
					'wpurl'		 	 => get_bloginfo( 'wpurl' )
				)
			);

			/* Push the data to the activation to the server */
			$response = wp_remote_post( apply_filters( 'vum_activate', self::api_url ), array(
					'method' => 'POST',
					'body' => array( 'json' => $json ),
				)
			);

			if ( is_wp_error( $response ) ) {

				$this->activation_error = 'Sorry, we were unable to contact our activation server. Please try again soon.';

			} else {

				// Decode our JSON response from the API.
				$api_reply = json_decode( wp_remote_retrieve_body( $response ) );

				// If this serial belongs to a hosting company, set that in the DB, then carry on.
				if ( isset( $api_reply->is_host ) && $api_reply->is_host == 'true' ) {

					// Note in the DB this is a host serial number.
					update_option( 'wpm_o_host', true );
				}

				if ( isset( $api_reply->has_prefs ) && $api_reply->has_prefs == 'true' ) {

					//  Add Serial
					update_option( 'wpm_o_user_id', trim( $_POST['serial'] ) );

					// We are going to apply the profile?
					if ( isset( $_POST['applyProfile'] ) ) {

						$response = wp_remote_get( apply_filters( 'vum_activate', self::activate_url . trim( $_POST['serial'] ) . '&apply=true' . '&url=' . $this->domain ) );

						$body = wp_remote_retrieve_body( $response );

						$settings = unserialize( stripslashes( $body ) );

						foreach ( $settings as $s ) {

							if ( ( $s->option_name == 'wpm_o_user_menu_restrict' ) && ( $s->option_value != '0' ) ) {
								$s->option_value = get_current_user_id();
							}

							update_option( $s->option_name, $s->option_value );
						}
					}

					wp_redirect( admin_url( 'admin.php?page=vum-options' ) );
					exit;

				} elseif ( $api_reply->result == 'active' ) {

					update_option( 'wpm_o_user_id', trim( $_POST['serial'] ) );
					wp_redirect( admin_url( 'admin.php?page=vum-options' ) );
					exit;

				} elseif ( $api_reply->result == 'over-quota' ) {
					$this->activation_error = 'Sorry, we were unable match this serial number as you have exeeded your license quota. <a target="_blank" href="http://www.videousermanuals.com/singlehelp/">Please contact us if you need assistance.</a>';

				} elseif ( $api_reply->result == 'expired' ) {
					$this->activation_error = 'Sorry, It appears your license has expired. Please <a href="https://videousermanuals.desk.com/">contact us</a> if you quire assistance. ';

				} else {
					$this->activation_error = 'Sorry, we were unable match this serial number. Did you enter it correctly?';

				}
			}
        }
    }

	function activate() {
		if ( isset( $this->activation_error ) ) {
			$this->notice( $this->activation_error );
		}

		if ( is_multisite() ) {
			$this->notice( 'As this is a Multi-site installation, you will have to activate VUM for each site.' );
		}

		self::load( 'activate' );
	}

	function notice( $message = '' ) {
		echo '<div class="updated"><p>' . $message . '</p> </div>';
	}

	function load( $viewName = '' ) {
		require_once( 'views/' . $viewName . '.php' );
	}

	function update_plugin() {
		if ( isset( $_GET['plugin'] ) && $_GET['plugin'] == 'video-user-manuals' ) {
			add_action( 'admin_head', array( $this, 'hide_stuff' ) );
		}
	}

	function hide_stuff() {
		echo '<style> .fyi ul, .fyi h2, .star-holder, small{display:none} </style>';
	}
}

require_once( 'updater.php' );

$updateVUM = new VUM_PluginUpdateChecker(
	'http://wordpress.videousermanuals.com/video-user-manuals/info.json',
	__FILE__,
	'video-user-manuals',
	12,
	'wpm_external_updater'
);

$vum = new Vum();