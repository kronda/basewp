<?php
$options = thrive_get_theme_options();
$sidebar_is_active = is_active_sidebar('sidebar-1');
$main_content_class = _thrive_get_main_content_class($options);
?>
<?php get_header(); ?>
<div class="wrp cnt">
    <?php if ($options['sidebar_alignement'] == "left" && $sidebar_is_active): ?>
        <?php get_sidebar(); ?>
    <?php endif; ?>
    <?php if (_thrive_is_active_sidebar()): ?>
    <div class="bSeCont">
        <?php endif; ?>
        <section class="bSe <?php echo $main_content_class; ?>">

            <?php if (have_posts()): ?>
                <?php while (have_posts()): ?>
                    <?php the_post(); ?>
                    <?php get_template_part('content-single', get_post_format()); ?>
                    <?php
                    if (thrive_check_bottom_focus_area()):
                        thrive_render_top_focus_area("bottom");
                        echo "<div class='spr'></div>";
                    endif;
                    ?>
                    <?php if (isset($options['bottom_about_author']) && $options['bottom_about_author'] == 1): ?>
                        <?php get_template_part('authorbox'); ?>
                    <?php endif; ?>
                    <?php if (comments_open() && !post_password_required()) : ?>
                        <?php comments_template('', true); ?>
                    <?php elseif ((!comments_open()) && get_comments_number() > 0): ?>
                        <?php comments_template('/comments-disabled.php'); ?>
                    <?php endif; ?>
                    <?php
                    if (isset($options['bottom_previous_next']) && $options['bottom_previous_next'] == 1 && get_permalink(get_adjacent_post(false, '', false)) != "" && get_permalink(get_adjacent_post(false, '', true)) != ""):
                        ?>
                        <ul class="pgn right">
                            <li>
                                <a class="page-numbers nxt" href='<?php echo get_permalink(get_adjacent_post(false, '', true)); ?>'><?php _e("Previous post", 'thrive'); ?> </a>
                            </li>
                            <li>
                                <a class="page-numbers prv" href='<?php echo get_permalink(get_adjacent_post(false, '', false)); ?>'><?php _e("Next post", 'thrive') ?></a>
                            </li>
                        </ul>
                    <?php endif; ?>
                <?php endwhile; ?>
            <?php else: ?>
                <!--No contents-->
            <?php endif ?>
        </section>
        <?php if (_thrive_is_active_sidebar()): ?>
    </div>
<?php endif; ?>
    <?php if ($options['sidebar_alignement'] == "right" && $sidebar_is_active): ?>
        <?php get_sidebar(); ?>
    <?php endif; ?>
    <div class="clear"></div>
</div>
<?php get_footer(); ?>
