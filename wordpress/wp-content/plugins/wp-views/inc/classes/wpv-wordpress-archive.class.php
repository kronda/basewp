<?php

final class WPV_WordPress_Archive extends WPV_WordPress_Archive_Embedded {


    /**
     * See parent class constructor description.
     *
     * @param int|WP_Post $wpa WPA post object or ID.
     */
    public function __construct( $wpa ) {
        parent::__construct( $wpa );
    }


    /**
     * Create a new WordPress Archive.
     *
     * If the query mode is set to "layouts-loop", also automatically create new Loop template.
     *
     * @param string $title New WPA title. Must be unique and valid (see validate_title()).
     * @param array $args (
     *          @type array $view_settings View settings that should override the default ones. Optional.
     *          @type array $loop_settings Loop settings that should override the default ones. Optional.
     *          @type bool $forbid_loop_template Never create a Loop template for this View. Optional, default is false.
     *     )
     *
     * @return WPV_WordPress_Archive New WPA object.
     *
     * @throws InvalidArgumentException
     * @throws RuntimeException
     * @throws WPV_RuntimeExceptionWithMessage
     *
     * @note overriding default Views settings and layout settings must provide complete data when the element is an
     * array, because it overrides them all. For example, $args['settings']['pagination'] can not override just the
     * "postsper page" options: it must provide a complete pagination implementation. This might change and be corrected
     * in the future, keeping backwards compatibility.
     *
     * @since 1.10
     */
    public static function create( $title, $args ) {

        $wpa_id = WPV_View_Base::create_post( $title );

        $wpa = new WPV_WordPress_Archive( $wpa_id );

        $wpa->defer_after_update_actions();

        // Construct default View settings and Loop settings
        $view_settings = wpv_getarr( $args, 'view_settings', array() );

        $query_mode = wpv_getarr( $view_settings, WPV_View_Base::VIEW_SETTINGS_QUERY_MODE, 'archive', array( 'archive', 'layouts-loop' ) );
        $view_settings[ WPV_View_Base::VIEW_SETTINGS_QUERY_MODE ] = $query_mode;
        $is_layouts_loop = ( 'layouts-loop' == $query_mode );

        $view_settings_default = wpv_wordpress_archives_defaults( 'view_settings' );
        $view_settings = wp_parse_args( $view_settings, $view_settings_default );

        $wpa->update_postmeta( WPV_View_Base::POSTMETA_VIEW_SETTINGS, $view_settings );

        $loop_settings_default = wpv_wordpress_archives_defaults( 'view_layout_settings' );

        // Modify default loop output for Layouts loop
        if ( $is_layouts_loop ) {
            $loop_settings_default[ WPV_View_Base::LOOP_SETTINGS_META_HTML ] = str_replace(
                "[/wpv-items-found]",
                "[wpv-archive-pager-prev-page]\n"
                . "\t\t[wpml-string context=\"wpv-views\"]Older posts[/wpml-string]\n"
                . "\t[/wpv-archive-pager-prev-page]\n"
                . "\t[wpv-archive-pager-next-page]\n"
                . "\t\t[wpml-string context=\"wpv-views\"]Newer posts[/wpml-string]\n"
                . "\t[/wpv-archive-pager-next-page]\n"
                . "\t[/wpv-items-found]",
                $loop_settings_default[ WPV_View_Base::LOOP_SETTINGS_META_HTML ]
            );
        }

        $loop_settings = wpv_getarr( $args, 'loop_settings', array() );
        $loop_settings = wp_parse_args( $loop_settings, $loop_settings_default );

        $wpa->update_postmeta( WPV_View_Base::POSTMETA_LOOP_SETTINGS, $loop_settings );

        // Create Loop template for Layouts loop
        $forbid_loop_template = wpv_getarr( $args, 'forbid_loop_template', false );
        if( ! $forbid_loop_template && $is_layouts_loop ) {

            $ct_title = sprintf( '%s - %s', $title, __( 'loop item', 'wpv-views' ) );
            $ct_content = sprintf(
                "<h1>[wpv-post-title]</h1>\n[wpv-post-body view_template=\"None\"]\n[wpv-post-featured-image]\n%s",
                sprintf(__('Posted by %s on %s', 'wpv-views'), '[wpv-post-author]', '[wpv-post-date]' )
            );
            $wpa->create_loop_template( $ct_title, $ct_content );
        }


        $wpa->resume_after_update_actions();

        return $wpa;
    }


}