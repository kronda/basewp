<?php

// Check if anything was passed through.
if( ! isset( $_GET['wpmvid'] ) ) {
    die( 'No Video Selected.' );
}

?>

<html>
    <head>
        <title>Video Player</title>
    </head>
    <body style="padding:0;margin:0;">
        <div style="padding:25px">
            <?php echo stripcslashes( get_option( 'wpm_o_localvideos_' . $_GET['wpmvid'] . '_5' ) ); ?>
        </div>
        </body>
</html>