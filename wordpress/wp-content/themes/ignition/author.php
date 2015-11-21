<?php
$current_auth_id = (isset($_GET['author']) && $_GET['author']) ? (intval($_GET['author'])) : 0;

$options = thrive_get_theme_options();

$sidebar_is_active = is_active_sidebar('sidebar-1');
$main_content_class = _thrive_get_main_content_class($options);

$fname = get_the_author_meta('first_name', $current_auth_id);
$lname = get_the_author_meta('last_name', $current_auth_id);
$desc = get_the_author_meta('description', $current_auth_id);

$user_gplus = get_the_author_meta('gplus', $current_auth_id);
$user_twitter = get_the_author_meta('twitter', $current_auth_id);
$user_facebook = get_the_author_meta('facebook', $current_auth_id);

$author_name = get_the_author_meta('display_name', $current_auth_id);
$display_name = empty($author_name) ? $fname . " " . $lname : $author_name;

if ($display_name == "") {
    $display_name = get_the_author_meta('user_login', $current_auth_id);
}

$next_page_link = get_next_posts_link();
$prev_page_link = get_previous_posts_link();
?>
<?php get_header(); ?>
<div class="wrp cnt">
    <?php if ($options['sidebar_alignement'] == "left" && $sidebar_is_active): ?>
        <?php get_sidebar(); ?>
    <?php endif; ?>
    <div class="bSeCont">
    <section class="bSe <?php echo $main_content_class; ?>">
        <?php
        /* Queue the first post, that way we know
         * what author we're dealing with (if that is the case).
         *
         * We reset this later so we can run the loop
         * properly with a call to rewind_posts().
         */
        the_post();
        ?>
        <article>
            <div class="scn awr aut">
                <div class="left">
                    <?php echo get_avatar(get_the_author_meta('user_email'), 60); ?>
                    <?php if (!empty($user_facebook) || !empty($user_gplus) || !empty($user_twitter)): ?>                
                        <ul class="left">
                            <?php if (!empty($user_facebook)): ?>
                                <li>
                                    <a href="<?php echo $user_facebook; ?>" target="_blank" class="fbk"></a>
                                </li>
                            <?php endif; ?>
                            <?php if (!empty($user_twitter)): ?>
                                <li>
                                    <a href="<?php echo $user_twitter; ?>" target="_blank" class="twt"></a>
                                </li>
                            <?php endif; ?>
                            <?php if (!empty($user_gplus)): ?>
                                <li>
                                    <a href="<?php echo $user_gplus; ?>" target="_blank" class="ggl"></a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    <?php endif; ?>
                    <div class="clear"></div>
                    <span><?php echo $display_name; ?></span>
                </div>
                <div class="right">
                    <h5>
                        <?php printf(__('Author Archives: %s', 'thrive'), $display_name); ?>
                    </h5>
                </div>
                <div class="clear"></div>
            </div>
        </article>
        <div class="bspr"></div>
        <?php
        /* Since we called the_post() above, we need to
         * rewind the loop back to the beginning that way
         * we can run the loop properly, in full.
         */
        rewind_posts();
        ?>
        <?php if (have_posts()): ?>
            <?php while (have_posts()): ?>
                <?php the_post(); ?>
                <?php get_template_part('content', get_post_format()); ?>
            <?php endwhile; ?>
            <?php if (_thrive_check_focus_area_for_pages("archive", "bottom")): ?>
                <?php if (strpos($options['blog_layout'], 'masonry') === FALSE && strpos($options['blog_layout'], 'grid') === FALSE): ?>
                    <?php thrive_render_top_focus_area("bottom", "archive"); ?>
                    <div class="spr"></div>
                <?php endif; ?>
            <?php endif; ?>
            <?php if ($next_page_link || $prev_page_link && ($next_page_link != "" || $prev_page_link != "")): ?>
                <div class="awr ctr pgn">
                    <?php thrive_pagination(); ?>
                </div>
                <div class="bspr"></div>
            <?php endif; ?>
        <?php else: ?>
            <!--No contents-->
        <?php endif ?>
    </section>
    </div>
    <?php if ($options['sidebar_alignement'] == "right" && $sidebar_is_active): ?>
        <?php get_sidebar(); ?>
    <?php endif; ?>

</div>

<?php get_footer(); ?>
