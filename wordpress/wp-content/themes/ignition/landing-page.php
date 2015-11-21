<?php
/*
  Template Name: Landing
 */
?>
<?php
$options = thrive_get_theme_options();
?>
<?php get_header(); ?>

<div class="wrp cnt bip fullWidth">
    <section class="bSe">

        <?php if (have_posts()): ?>
            <?php while (have_posts()): ?>
                <?php the_post(); ?>
                <?php get_template_part('content-single', get_post_format()); ?>

                <?php if (isset($options['bottom_about_author']) && $options['bottom_about_author'] == 1 && !is_page()): ?>       
                    <?php get_template_part('authorbox'); ?>
                <?php endif; ?>      
                <?php if (comments_open() && !post_password_required() && (!is_page() || (is_page() && $options['comments_on_pages'] != 0 ))) : ?>
                    <?php comments_template('', true); ?>
                <?php elseif ((!comments_open()) && get_comments_number() > 0): ?>
                    <?php comments_template('/comments-disabled.php'); ?>
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

<?php get_footer("landing"); ?>
