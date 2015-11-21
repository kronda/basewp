<?php
$fname = get_the_author_meta('first_name');
$lname = get_the_author_meta('last_name');
$desc = get_the_author_meta('description');
$thrive_social = array_filter(array("twt" => get_the_author_meta('twitter'),
    "fbk" => get_the_author_meta('facebook'),
    "ggl" => get_the_author_meta('gplus')));

$author_name = get_the_author_meta('display_name');
$display_name = empty($author_name) ? $fname . " " . $lname : $author_name;
$has_social_links = false;
?>
<article>
    <div class="scn awr aut">
        <div class="left">
            <?php echo get_avatar(get_the_author_meta('user_email'), 80); ?>
            <ul class="left">
                <?php foreach ($thrive_social as $service => $url): ?>
                    <?php if (!empty($url)): 
                        $has_social_links = true;
                        if ($service == "twt") {
                            $url = _thrive_get_twitter_link($url);
                        }
                        ?>
                        <li>
                            <a href="<?php echo $url; ?>" class="<?php echo $service; ?>" target="_blank">
                                <?php if ($service == "twt"): ?>
                                    <span class="awe"></span>
                                <?php elseif ($service == "fbk"): ?>
                                    <span class="awe"></span>
                                <?php else: ?>
                                    <span class="awe"></span>
                                <?php endif; ?>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php endforeach; ?>
            </ul>
            <div class="clear"></div>
        </div>
        <div class="right <?php if (!$has_social_links):?>noSocial<?php endif;?>">
            <h4><?php echo $display_name; ?></h4>
            <p>
                <?php echo $desc; ?>
            </p>
        </div>
        <div class="clear"></div>
    </div>
</article>