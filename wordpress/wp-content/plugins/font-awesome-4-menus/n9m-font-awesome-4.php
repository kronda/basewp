<?php
/*
Plugin Name: Font Awesome 4 Menus
Plugin URI: http://www.newnine.com/plugins/font-awesome-4-menus
Description: Join the retina/responsive revolution by easily adding Font Awesome 4.3 icons to your WordPress menus and anywhere else on your site! No programming necessary.
Version: 4.3.0.3
Author: New Nine Media
Author URI: http://www.newnine.com
License: GPLv2 or later
*/

/*
    Copyright 2013  NEW NINE MEDIA, L.P.  (tel : +1-800-288-9699)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class FontAwesomeFour {

    var $defaults;

    function admin_enqueue_scripts( $hook ){
        if( 'settings_page_n9m-font-awesome-4-menus' == $hook ){
            wp_enqueue_script( 'n9m-admin-font-awesome-4', plugins_url( 'js/admin.js', __FILE__ ), array( 'jquery' ), $this->defaults[ 'version' ] );
            wp_enqueue_style( 'n9m-admin-font-awesome-4', plugins_url( 'css/font-awesome.min.css', __FILE__ ), false, $this->defaults[ 'version' ] );
        }
    }

    function admin_menu(){
        add_submenu_page( 'options-general.php', 'Font Awesome 4 Menus', 'Font Awesome', 'edit_theme_options', 'n9m-font-awesome-4-menus', array( $this, 'admin_menu_cb' ) );
    }

    function admin_menu_cb(){
        if( $_POST && check_admin_referer( 'n9m-fa' ) ){
            $settings = array();
            switch( $_POST[ 'n9m_location' ] ){
                case 'local':
                case 'maxcdn':
                case 'none':
                    $settings[ 'stylesheet' ] = $_POST[ 'n9m_location' ];
                    break;
                case 'other':
                    $settings[ 'stylesheet' ] = 'other';
                    $settings[ 'stylesheet_location' ] = sanitize_text_field( $_POST[ 'n9m_location-other-location' ] );
                    break;
            }
            if( isset( $_POST[ 'n9m_text_spacing' ] ) ){
                $settings[ 'spacing' ] = 1;
            } else {
                $settings[ 'spacing' ] = 0;
            }
            update_option( 'n9m-font-awesome-4-menus', $settings );
            print '<div class="updated"><p>Your settings have been saved!</p></div>';
        }
        $settings = get_option( 'n9m-font-awesome-4-menus', $this->defaults );
        print ' <div class="wrap">
                    <h2><i class="fa fa-thumbs-o-up"></i> '.get_admin_page_title().'</h2>
                    <p>Thank you for using Font Awesome 4 Menus by <a href="http://www.newnine.com" target="_blank">New Nine</a>! To view available icons, <a href="http://fortawesome.github.io/Font-Awesome/icons/" target="_blank">click here to visit the Font Awesome website</a>.</p>
                    <form action="'.admin_url( 'options-general.php?page=n9m-font-awesome-4-menus' ).'" method="post">
                        <h3>Font Awesome Stylesheet</h3>
                        <p>Select how you want Font Awesome 4&#8217;s stylesheet loaded on your site (if at all):</p>
                        <table class="form-table">
                            <tbody>
                                <tr>
                                    <th scope="row">Load Font Awesome 4 From:</th>
                                    <td>
                                        <fieldset>
                                            <legend class="screen-reader-text"><span>Load Font Awesome 4 From</span></legend>
                                            <label for="n9m_location-local"><input type="radio" name="n9m_location" id="n9m_location-local" value="local"'.( 'local' == $settings[ 'stylesheet' ] ? ' checked' : false ).'> Local plugin folder (default)</label>
                                            <br />
                                            <label for="n9m_location-maxcdn"><input type="radio" name="n9m_location" id="n9m_location-maxcdn" value="maxcdn"'.( 'maxcdn' == $settings[ 'stylesheet' ] ? ' checked' : false ).'> Official Font Awesome CDN <span class="description">(<a href="http://www.bootstrapcdn.com/#fontawesome_tab" target="_blank">Bootstrap CDN powered by MaxCDN</a>)</span></label>
                                            <br />
                                            <label for="n9m_location-other"><input type="radio" name="n9m_location" id="n9m_location-other" value="other"'.( 'other' == $settings[ 'stylesheet' ] ? ' checked' : false ).'> A custom location:</label> <input type="text" name="n9m_location-other-location" id="n9m_location-other-location" placeholder="Enter full url here" class="regular-text" value="'.( isset( $settings[ 'stylesheet_location' ] ) ? $settings[ 'stylesheet_location' ] : '' ).'">
                                            <br />
                                            <label for="n9m_location-none"><input type="radio" name="n9m_location" id="n9m_location-none" value="none"'.( 'none' == $settings[ 'stylesheet' ] ? ' checked' : false ).'>Don&#8217;t load Font Awesome 4&#8217;s stylesheet <span class="description">(use this if you load Font Awesome 4 elsewhere on your site)</span></label>
                                        </fieldset>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <h3>Icon Spacing</h3>
                        <p>By default, Font Awesome 4 Menus adds a space before or after the icon in your menu. Uncheck the box below to remove this space and give you finer control over your custom styling.</p>
                        <p><label for="n9m_text_spacing"><input type="checkbox" name="n9m_text_spacing" id="n9m_text_spacing" value="1"'.( 1 == $settings[ 'spacing' ] ? ' checked' : false ).'> Keep the space between my text and my icons <span class="description">(default is checked)</span></label>
                        <p>'.wp_nonce_field( 'n9m-fa' ).'<button type="submit" class="button button-primary">Save Settings</button></p>
                    </form>
                </div>';
    }

    function admin_notice(){
        global $current_user, $pagenow;
        if( isset( $_REQUEST[ 'action' ] ) && 'kill-n9m-font-awesome-4-notice' == $_REQUEST[ 'action' ] ){
            update_user_meta( $current_user->data->ID, 'n9m-font-awesome-4-notice-hide', 1 );
        }
        $shownotice = get_user_meta( $current_user->data->ID, 'n9m-font-awesome-4-notice-hide', true );
        if( 'plugins.php' == $pagenow && !$shownotice ){
            print ' <div class="updated">
                        <div style="float: right;"><a href="?action=kill-n9m-font-awesome-4-notice" style="color: #7ad03a; display: block; padding: 8px;">&#10008;</a></div>
                        <p>Thank you for installing Font Awesome Menus 4 by <a href="http://www.newnine.com">New Nine</a>! Want to see what else we&#8217;re up to? Subscribe below to our infrequent updates. You can unsubscribe at any time.</p>
                        <form action="http://newnine.us2.list-manage.com/subscribe/post?u=067bab5a6984981f003cf003d&amp;id=1b25a2aee6" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" target="_blank">
                            <p><input type="text" name="FNAME" placeholder="First Name" value="'.( !empty( $current_user->first_name ) ? $current_user->first_name : '' ).'"> <input type="text" name="LNAME" placeholder="Last Name" value="'.( !empty( $current_user->last_name ) ? $current_user->last_name : '' ).'"> <input type="text" name="EMAIL" placeholder="Email address" required value="'.$current_user->user_email.'"> <input type="hidden" id="group_1" name="group[14489][1]" value="1"> <input type="submit" name="subscribe" value="Join" class="button action"></p>
                        </form>
                    </div>';
        }
    }

    function menu( $nav ){
        $menu_item = preg_replace_callback(
            '/(<li[^>]+class=")([^"]+)("?[^>]+>[^>]+>)([^<]+)<\/a>/',
            array( $this, 'replace' ),
            $nav
        );
        return $menu_item;
    }
    
    function replace( $a ){
        $start = $a[ 1 ];
        $classes = $a[ 2 ];
        $rest = $a[ 3 ];
        $text = $a[ 4 ];
        $before = true;
        
        $class_array = explode( ' ', $classes );
        $fontawesome_classes = array();
        foreach( $class_array as $key => $val ){
            if( 'fa' == substr( $val, 0, 2 ) ){
                if( 'fa' == $val ){
                    unset( $class_array[ $key ] );
                } elseif( 'fa-after' == $val ){
                    $before = false;
                    unset( $class_array[ $key ] );
                } else {
                    $fontawesome_classes[] = $val;
                    unset( $class_array[ $key ] );
                }
            }
        }
        
        if( !empty( $fontawesome_classes ) ){
            $fontawesome_classes[] = 'fa';
            $settings = get_option( 'n9m-font-awesome-4-menus', $this->defaults );
            if( $before ){
                if( 1 == $settings[ 'spacing' ] ){
                    $text = ' '.$text;
                }
                $newtext = '<i class="'.implode( ' ', $fontawesome_classes ).'"></i><span class="fontawesome-text">'.$text.'</span>';
            } else {
                if( 1 == $settings[ 'spacing' ] ){
                    $text = $text.' ';
                }
                $newtext = '<span class="fontawesome-text">'.$text.'</span><i class="'.implode( ' ', $fontawesome_classes ).'"></i>';
            }
        } else {
            $newtext = $text;
        }
        
        $item = $start.implode( ' ', $class_array ).$rest.$newtext.'</a>';
        return $item;
    }
    
    function shortcode_icon( $atts ){
        extract( shortcode_atts( array(
            'class' => '',
        ), $atts ) );
        if( !empty( $class ) ){
            $fa_exists = false;
            $class_array = explode( ' ', $class );
            foreach( $class_array as $c ){
                if( 'fa' == $c ){
                    $fa_exists = true;
                }
            }
            if( !$fa_exists ){
                array_unshift( $class_array, 'fa' );
            }
            return '<i class="'.implode( ' ', $class_array ).'"></i>';
        }
    }
    
    function shortcode_stack( $atts, $content = null ){
        extract( shortcode_atts( array(
            'class' => '',
        ), $atts ) );
        if( empty( $class ) ){
            $class_array = array( 'fa-stack' );
        } else {
            $fa_stack_exists = false;
            $class_array = explode( ' ', $class );
            foreach( $class_array as $c ){
                if( 'fa-stack' == $c ){
                    $fa_stack_exists = true;
                }
            }
            if( !$fa_stack_exists ){
                array_unshift( $class_array, 'fa-stack' );
            }
        }
        return '<span class="'.implode( ' ', $class_array ).'">'.do_shortcode( $content ).'</span>';
    }

    function wp_enqueue_scripts(){
        $settings = get_option( 'n9m-font-awesome-4-menus', $this->defaults );
        switch( $settings[ 'stylesheet' ] ){
            case 'local':
                wp_register_style( 'font-awesome-four', plugins_url( 'css/font-awesome.min.css', __FILE__ ), array(), $this->defaults[ 'version' ], 'all' );
                wp_enqueue_style( 'font-awesome-four' );
                break;
            case 'maxcdn':
                wp_register_style( 'font-awesome-four', $this->defaults[ 'maxcdn_location' ], array(), $this->defaults[ 'version' ], 'all' );
                wp_enqueue_style( 'font-awesome-four' );
                break;
            case 'none':
                break;
            case 'other':
                wp_register_style( 'font-awesome-four', $settings[ 'stylesheet_location' ], array(), $this->defaults[ 'version' ], 'all' );
                wp_enqueue_style( 'font-awesome-four' );
                break;
        }
    }
    
    
    function __construct(){
        $this->defaults = array(
            'maxcdn_location' => '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css',
            'spacing' => 1,
            'stylesheet' => 'local',
            'version' => '4.3.0'
        );
        add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        add_action( 'admin_notices', array( $this, 'admin_notice' ) );
        add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts' ) );
        add_filter( 'wp_nav_menu' , array( $this, 'menu' ), 10, 2 );
        add_shortcode( 'fa', array( $this, 'shortcode_icon' ) );
        add_shortcode( 'fa-stack', array( $this, 'shortcode_stack' ) );
    }
}
$n9m_font_awesome_four = new FontAwesomeFour();