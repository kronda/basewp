<?php
$options = thrive_appr_get_theme_options();
?>
<?php get_template_part("appr/header"); ?>

    <div class="bspr"></div>
    <div class="wrp cnt bip">
        <section class="bSe bpd">

            <?php if (have_posts()): ?>
                <?php while (have_posts()): ?>
                    <?php the_post(); ?>
                    <?php get_template_part('appr/content-single'); ?>

                    <?php if (isset($options['bottom_about_author']) && $options['bottom_about_author'] == 1 && !is_page()): ?>
                        <?php get_template_part('authorbox'); ?>
                    <?php endif; ?>
                    <?php if (comments_open() && !post_password_required() && $options['comments_on_pages'] != 0) : ?>
                        <?php comments_template('', true); ?>
                    <?php elseif ((!comments_open()) && get_comments_number() > 0): ?>
                        <?php comments_template('/comments-disabled.php'); ?>
                    <?php endif; ?>

                    <?php
                    $next_lesson_link = _thrive_get_next_prev_lesson_link(get_the_ID(), true);
                    $prev_lesson_link = _thrive_get_next_prev_lesson_link(get_the_ID(), false);
                    if (isset($options['bottom_previous_next']) && $options['bottom_previous_next'] == 1 && ($next_lesson_link != false || $prev_lesson_link != false)):
                        ?>
                        <div class="spr"></div>
                        <div class="awr ctr pgn">
                            <?php if ($prev_lesson_link): ?>
                                <a class="page-numbers nxt"
                                   href='<?php echo $prev_lesson_link; ?>'><?php _e("Previous lesson", 'thrive'); ?> </a>
                            <?php endif; ?>
                            <?php if ($next_lesson_link): ?>
                                <a class="page-numbers prv"
                                   href='<?php echo $next_lesson_link; ?>'><?php _e("Next lesson", 'thrive') ?></a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                <?php endwhile; ?>
            <?php else: ?>
                <!--No contents-->
            <?php endif ?>
        </section>
        <div class="bspr"></div>
    </div>

<?php
if (thrive_check_bottom_focus_area()):
    thrive_render_top_focus_area("bottom");
    echo "<div class='spr'></div>";
endif;
?>

<?php get_template_part("appr/footer"); ?>