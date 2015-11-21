<?php
$options = thrive_appr_get_theme_options();

$sidebar_is_active = is_active_sidebar('sidebar-appr');

$main_content_class = $options['sidebar_alignement'] == "left" ? "right" : ($options['sidebar_alignement'] == "right" ? "left" : "");
if (!$sidebar_is_active) {
    $main_content_class = "fullWidth";
}

$term = get_term_by('slug', get_query_var('term'), get_query_var('taxonomy'));

$courses_obj = _thrive_appr_get_category_object($term->term_id);
$catLevel = _thrive_appr_get_cat_level($term->term_id);  

$lessonsLevel = thrive_appr_get_lessons_level() - $catLevel;
?>

<?php get_template_part("appr/header"); ?>
<div class="bspr"></div>
<div class="wrp cnt">

    <?php if ($options['sidebar_alignement'] == "left" && $sidebar_is_active): ?>
        <?php get_template_part("appr/sidebar"); ?>
    <?php endif; ?>

        <div class="bSeCont">

        <section class="bSe <?php echo $main_content_class; ?>">

            <article>
                <div class="scn awr aut">
                    <h2><?php _e('Lessons for ', 'thrive'); ?><?php echo $term->name; ?></h2>
                </div>
            </article>
            <article>
                <div class="awr">
                    <p><?php echo $term->description; ?></p>
                    <?php if ($lessonsLevel == 3): ?>
                        <?php foreach ($courses_obj['courses'] as $course): ?>
                            <h3><?php echo $course['name']; ?></h3>
                            <p><?php echo $course['description']; ?></p>
                            <?php foreach ($course['modules'] as $module): ?>
                                <div class="lvl-2">
                                    <h3><?php echo $module['name']; ?></h3>
                                    <p><?php echo $module['description']; ?></p>
                                </div>
                                <div class="lvl-3">
                                    <div class="apc">
                                        <?php foreach ($module['posts'] as $post): ?>
                                            <div class="apl clearfix">
                                                <div class="api">
                                                    <span class="awe">
                                                        <?php echo _thrive_app_get_lesson_icon(get_post_meta($post->ID, '_thrive_meta_appr_lesson_type', true)); ?>
                                                    </span>
                                                </div>
                                                <p>
                                                    <a href="<?php echo get_permalink($post->ID); ?>"><?php echo $post->post_title; ?></a>
                                                </p>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    <?php elseif ($lessonsLevel == 2): ?>
                        <?php foreach ($courses_obj['courses'] as $course): ?>
                            <h3><?php echo $course['name']; ?></h3>
                            <p><?php echo $course['description']; ?></p>
                            <div class="lvl-2">
                            <div class="apc">
                                <?php foreach ($course['posts'] as $post): ?>
                                    <div class="apl clearfix">
                                        <div class="api">
                                            <span class="awe">
                                                <?php echo _thrive_app_get_lesson_icon(get_post_meta($post->ID, '_thrive_meta_appr_lesson_type', true)); ?>
                                            </span>
                                        </div>
                                        <p>
                                            <a href="<?php echo get_permalink($post->ID); ?>"><?php echo $post->post_title; ?></a>
                                        </p>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            </div>
                        <?php endforeach; ?>
                        <?php
                    else:
                        $posts = _thrive_appr_get_lessons($term->term_id);
                        ?>
                    <div class="apc">
                        <?php foreach ($posts as $post):
                            ?>
                            <div class="apl clearfix">
                                <div class="api">
                                    <span class="awe">
                                        <?php echo _thrive_app_get_lesson_icon(get_post_meta($post->ID, '_thrive_meta_appr_lesson_type', true)); ?>
                                    </span>
                                </div>
                                <p>
                                    <a href="<?php echo get_permalink($post->ID); ?>"><?php echo $post->post_title; ?></a>
                                </p>
                            </div>
                        <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                </div>
            </article>
            <div class="spr"></div>
            <div class="clear"></div>
        </section>

        </div>


    <?php if ($options['sidebar_alignement'] == "right" && $sidebar_is_active): ?>
        <?php get_template_part("appr/sidebar"); ?>
    <?php endif; ?>

    <div class="clear"></div>
</div>

<?php get_template_part("appr/footer"); ?>