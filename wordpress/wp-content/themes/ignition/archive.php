<?php
$options = thrive_get_theme_options();
$sidebar_is_active = is_active_sidebar('sidebar-1');
$main_content_class = _thrive_get_main_content_class($options);
$next_page_link = get_next_posts_link();
$prev_page_link = get_previous_posts_link();
?>
<?php get_header(); ?>
    <div class="wrp cnt">
    <?php if ($options['sidebar_alignement'] == "left" && $sidebar_is_active): ?>
        <?php get_sidebar(); ?>
    <?php endif; ?>
    <section class="bSe <?php echo $main_content_class; ?>">
        <article>
            <div class="scn awr aut">
                <h5><?php _e("Archive", 'thrive'); ?></h5>
                <h6>
                    <?php if (is_day()) : ?>
                        <?php printf(__('Daily Archives: %s', 'thrive'), '' . get_the_date() . ''); ?>
                    <?php elseif (is_month()) : ?>
                        <?php printf(__('Monthly Archives: %s', 'thrive'), '' . get_the_date(_x('F Y', 'monthly archives date format', 'thrive')) . ''); ?>
                    <?php elseif (is_year()) : ?>
                        <?php printf(__('Yearly Archives: %s', 'thrive'), '' . get_the_date(_x('Y', 'yearly archives date format', 'thrive')) . ''); ?>
                    <?php else : ?>
                        <?php _e('Blog Archives', 'thrive'); ?>
                    <?php endif; ?>
                </h6>
            </div>
        </article>
        <div class="bspr"></div>
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
<?php if ($options['sidebar_alignement'] == "right" && $sidebar_is_active): ?>
    <?php get_sidebar(); ?>
<?php endif; ?>
        
    </div>

<?php get_footer(); ?>


