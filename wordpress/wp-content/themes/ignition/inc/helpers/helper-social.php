<?php

function _thrive_enqueue_social_fb($attrs) {
    ?>
    <iframe style="height:70px !important;" src="//www.facebook.com/plugins/likebox.php?href=<?php echo $fbUrl; ?>&width=292&height=32&colorscheme=light&show_faces=false&header=false&stream=false&show_border=false" id="follow_me_content_fb"></iframe>
    <?php
}

function render_share_bubble($type, $attr) {
    switch ($type) {
        case 'facebook':
            $fbUrl = urlencode($attr['facebook_url']);
            ?>
            <div id="container-follow-facebook">
                <iframe style="height:70px !important;" src="//www.facebook.com/plugins/likebox.php?href=<?php echo $fbUrl; ?>&width=292&height=32&colorscheme=light&show_faces=false&header=false&stream=false&show_border=false" id="follow_me_content_fb"></iframe>
            </div>
            <?php
            break;
        case 'gprofile':
            $gProfileUrl = $attr['gprofile_url'];
            ?>
            <div id="container-follow-gprofile">
                <div class="g-person" data-width="273" data-href="<?php echo $gProfileUrl; ?>" data-layout="landscape" data-rel="author" id="follow_me_content_gprofile"></div>
            </div>
            <?php
            break;
        case 'gpage':
            $gPageUrl = $attr['gpage_url'];
            ?>
            <div id="container-follow-gpage">
                <div class="g-person" data-width="273" data-href="<?php echo $gPageUrl; ?>" data-layout="landscape" data-rel="author" id="follow_me_content_gprofile"></div>
            </div>

            <?php
            break;
        case 'twitter':
            $twitterUsername = $attr['twitter_url'];
            ?>
            <div id="container-follow-twitter">
                <a href="https://twitter.com/<?php echo $twitterUsername; ?>" class="twitter-follow-button" data-show-count="false">Follow @<?php echo $twitterUsername; ?></a>
                <script>!function(d, s, id) {
                        var js, fjs = d.getElementsByTagName(s)[0], p = /^http:/.test(d.location) ? 'http' : 'https';
                        if (!d.getElementById(id)) {
                            js = d.createElement(s);
                            js.id = id;
                            js.src = p + '://platform.twitter.com/widgets.js';
                            fjs.parentNode.insertBefore(js, fjs);
                        }
                    }(document, 'script', 'twitter-wjs');</script>                    
            </div>                
            <?php
            break;
        case 'linkedin':
            $linkedinId = $attr['linkedin_url'];
            ?>
            <div id="container-follow-linkedin">
                <script src="//platform.linkedin.com/in.js" type="text/javascript">lang: en_US</script>
                <script type="IN/Share" data-counter="top"></script>
            </div>
            <?php
            break;
        case 'pinterest':
            ?>
            <div id="container-follow-pinterest">
                <a data-pin-do="buttonFollow" href="//www.pinterest.com/<?php echo $attr['pinterest_url'] ?>/"><?php echo ucfirst($attr['pinterest_url']); ?></a>
                <script type="text/javascript" src="//assets.pinterest.com/js/pinit.js"></script>
            </div>
            <?php
            break;
        case 'dribble':
            ?>
            <div id="container-follow-dribble">
                <a href="<?php echo $attr['dribble_url']; ?>" target="_blank">Dribble</a>
            </div>
            <?php
            break;
        case 'rss':
            ?>
            <div id="container-follow-rss">
                <a href="<?php echo $attr['rss_url']; ?>" target="_blank">RSS</a>
            </div>
            <?php
            break;
        case 'youtube':
            ?>
            <div id="container-follow-youtube">
                <script src="https://apis.google.com/js/plusone.js"></script>
                <div class="g-ytsubscribe" data-channel="<?php echo $attr['youtube_url']; ?>" data-layout="full"></div>
            </div>
            <?php
            break;
    }
}
?>
