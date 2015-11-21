<?php
/*
  Template Name: Full Width
 */
?>
<?php
$options = thrive_get_theme_options();
$main_content_class = ($options['sidebar_alignement'] == "right" || $options['sidebar_alignement'] == "left") ? $options['sidebar_alignement'] : "";
if ($options['sidebar_alignement'] == "right") {
    $main_content_class = "left";
} elseif ($options['sidebar_alignement'] == "left") {
    $main_content_class = "right";
} else {
    $main_content_class = "fullWidth";
}
$sidebar_is_active = is_active_sidebar('sidebar-1');

if (!$sidebar_is_active) {
    $main_content_class = "fullWidth";
}
$next_page_link = get_next_posts_link();
$prev_page_link = get_previous_posts_link();
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
</div>

<?php
if (thrive_check_bottom_focus_area()):
    thrive_render_top_focus_area("bottom");
    echo "<div class='spr'></div>";
endif;
?>

<?php get_footer(); ?>
