<?php
// Plugin uninstaller - remove ALL our stuff from the DB!
if( WP_UNINSTALL_PLUGIN )
{
    global $wpdb;

    // If MU - need to delete stuff from ALL the wp_options
    if( is_multisite () )
    {
        $blogs = $wpdb->get_results("select * from $wpdb->blogs");

        if( $blogs )
        {
            foreach( $blogs as $blog)
            {
                // Switch to blog to grab correct wp_options value.
                switch_to_blog( $blog->blog_id );

                //Delete stuff
                $wpdb->query("delete from $wpdb->options where option_name like 'wpm_o_%' ");

                // Go back to current blog.
                restore_current_blog();
            }
        }
    }
    else
    {
        // Delete non-MU way.
        $wpdb->query("delete from $wpdb->options where option_name like 'wpm_o_%' ");
    }
}