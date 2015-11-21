<?php
$options = thrive_get_theme_options();
$sidebar_is_active = _thrive_is_active_sidebar();
$main_content_class = _thrive_get_main_content_class($options);
$next_page_link = get_next_posts_link();
$prev_page_link = get_previous_posts_link();
?>
<?php get_header(); ?>
<div class="wrp cnt">
    <?php if ($options['sidebar_alignement'] == "left" && $sidebar_is_active): ?>
        <?php get_sidebar(); ?>
    <?php endif; ?>
    <?php if ($sidebar_is_active): ?>
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
                    <?php if (comments_open() && !post_password_required() && $options['comments_on_pages'] != 0) : ?>
                        <?php comments_template('', true); ?>
                    <?php elseif ((!comments_open()) && get_comments_number() > 0): ?>
                        <?php comments_template('/comments-disabled.php'); ?>
                    <?php endif; ?>
                <?php endwhile; ?>
            <?php else: ?>
                <!--No contents-->
            <?php endif ?>
        </section>
        <?php if ($sidebar_is_active): ?>
    </div>
<?php endif; ?>
    <?php if ($options['sidebar_alignement'] == "right" && $sidebar_is_active): ?>
        <?php get_sidebar(); ?>
    <?php endif; ?>
    <div class="clear"></div>
</div>
<?php get_footer(); ?>
