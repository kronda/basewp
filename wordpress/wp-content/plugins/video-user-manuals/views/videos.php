<?php
// Plugin Heading
echo '<div id="manual-page" class="wrap"> <h2 style="margin-bottom:8px">';
if( get_option('wpm_o_plugin_custom_logo') )
    echo '<img src="'.get_option('wpm_o_plugin_custom_logo').'" alt="logo" style="vertical-align: -7px">&nbsp; ';
echo get_option('wpm_o_plugin_heading_video') . '</h2>';

// Intro Text
if(get_option('wpm_o_intro_text') != '') 
    echo stripslashes(get_option('wpm_o_intro_text'));

if( $show_local && $local_videos ):

    echo stripslashes( "<h2>$local_title</h2>" ); 

    foreach($local_videos as $video_id => $video ):
        
        echo $this->display_vid($video_id, $video);

    endforeach;

    echo '<br style="clear:both" />';

endif;


  
?>

<style type="text/css">

.video-container {margin-right: 20px;float: left;text-align: center;margin-bottom: 20px;}
.video-container a {color: #21759B;text-decoration: underline;font-size: 12px;}
.video-container img {margin-bottom: 6px;}
.vum-highlight{border: #FFFFBF solid 10px; background:#FFFFBF; font-weight: bold;border-top:0;border-bottom: 0; -webkit-border-bottom-right-radius: 4px;-webkit-border-bottom-left-radius: 4px;-moz-border-radius-bottomright: 4px;-moz-border-radius-bottomleft: 4px;border-bottom-right-radius: 4px;border-bottom-left-radius: 4px;}
</style>

    <div id="ajax_msg"></div>
    <div id="ajax_content"></div>
    <script type="text/javascript">
        
        jQuery(document).ready(function() 
        {
            jQuery.getJSON('<?php echo apply_filters( 'vum_iframe', self::iframe_url ); ?>', {<?php  echo $url_params;?>},
                function(data, textStatus)
                {
                    jQuery('#ajax_content').append(data);

<?php

if( $show_local && isset($custom_local_videos) ):

    foreach($custom_local_videos as $video_id => $video ): ?>
        jQuery('#section-<?php echo $video->loc;?>').append('<?php echo addslashes($this->display_vid($video_id, $video));?>');

<?php endforeach;

endif;

?>
                    // Get URL data now, only needs to be worked out once.
                    var entireUrl = jQuery(location).attr('href');
                    var urlBits = entireUrl.split('#');
                    
                    // Want to use JS to populate the URL used for sharing vids direct.
                    // Loops through divs, and chop the URL 
                    jQuery(".video-container").each(function()
                    {
                        var link = jQuery(this).find("a");
                        var divId = jQuery(this).attr('id');
                        var newLink = urlBits[0] + '#' + divId ;
                        link.attr("href", newLink );
                    });

                    // If there is a anchor with the same as a div, highlight it.
                    if( window.location.hash )
                    {
                        // Need to use jQuery to scroll, as the browser jump is already done before the json is loaded.
                        jQuery('html,body').animate({scrollTop:jQuery(window.location.hash).offset().top}, 500);
                        
                        // Apply a border so people notice it
                        jQuery( window.location.hash ).addClass('vum-highlight'); 
                    }
            });
        });

    </script>
 