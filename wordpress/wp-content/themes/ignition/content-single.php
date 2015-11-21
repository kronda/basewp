<?php
$options = thrive_get_options_for_post(get_the_ID());

$comment_nb_class = ($options['sidebar_alignement'] == "right") ? "comment_nb" : "right_comment_nb";
$featured_image_data = thrive_get_post_featured_image(get_the_ID(), $options['featured_image_style']);
$featured_image = $featured_image_data['image_src'];
$featured_image_alt = $featured_image_data['image_alt'];
$featured_image_title = $featured_image_data['image_title'];

$fname = get_the_author_meta('first_name');
$lname = get_the_author_meta('last_name');
$author_name = get_the_author_meta('display_name');
$display_name = empty($author_name) ? $fname . " " . $lname : $author_name;
$template_name = _thrive_get_item_template(get_the_ID());
if ($template_name == "Landing Page") {
    $options['display_meta'] = 0;
}

$current_content = get_the_content();
?>
<?php tha_entry_before(); ?>

<article>
    <div class="awr">
        <?php if ($options['show_post_title'] != 0): ?>
            <h1 class="entry-title"><?php the_title(); ?></h1>
        <?php endif; ?>
        <?php if (($options['featured_image_style'] == "wide" || $options['featured_image_style'] == "thumbnail") && $featured_image): ?>
            <img src="<?php echo $featured_image; ?>" alt="<?php echo $featured_image_alt; ?>" title="<?php echo $featured_image_title; ?>"
                 class="<?php if ($options['featured_image_style'] == "wide"): ?>fwI<?php else: ?>alignleft afim<?php endif; ?>"/>
        <?php endif; ?>

        <?php the_content(); ?>

        <div class="clear"></div>
        <?php if ($options['enable_social_buttons'] == 1): ?>
            <?php get_template_part('share-buttons'); ?>
        <?php endif; ?>
        <div class="clear"></div>
        <?php
        wp_link_pages(array(
            'before' => '<br><p class="ctr pgn">',
            'after' => '</p>',
            'next_or_number' => 'next_and_number',
            'echo' => 1
        ));
        ?>
        <?php
        if (isset($options['display_meta']) && $options['display_meta'] == 1 && get_post_type() == "post"):
            $li_width_style = "width:" . (100 / $options['meta_no_columns']) . "%;";
            ?>
            <footer>
                <ul class="meta left">
                    <li>
                        <?php $has_sep = false ?>
                        <?php if (isset($options['meta_post_category']) && $options['meta_post_category'] == 1): ?>
                            <?php
                            $has_sep = true;
                            $categories = get_the_category();
                            if ($categories && count($categories) > 0):
                                ?>
                                in
                                <?php foreach ($categories as $key => $cat): ?>
                                <span>
                                        <a href="<?php echo get_category_link($cat->term_id); ?>">
                                            <?php echo $cat->cat_name; ?>
                                        </a>
                                    <?php if ($key != count($categories) - 1 && isset($categories[$key + 1])): ?><span>,</span><?php endif; ?>
                                    </span>
                            <?php endforeach; ?>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if (isset($options['meta_author_name']) && $options['meta_author_name'] == 1): ?>
                            <?php $has_sep = true ?>
                            by <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>"><?php echo get_the_author(); ?></a>
                        <?php endif; ?>
                    </li>
                    <?php if (isset($options['meta_post_date']) && $options['meta_post_date'] == 1): ?>
                        <?php if ($has_sep) : ?>
                            <li class="sep">|</li>
                        <?php endif; ?>
                        <li>
                            <?php if ($options['relative_time'] == 1): ?>
                                <?php echo thrive_human_time(get_the_time('U')); ?>
                            <?php else: ?>
                                <?php echo get_the_date(); ?>
                            <?php endif; $has_sep = true ?>
                        </li>
                    <?php endif; ?>
                    <?php if (!empty($options['meta_comment_count']) && get_comments_number()): ?>
                        <?php if ($has_sep) : ?>
                            <li class="sep">|</li><?php $has_sep = false; endif; ?>
                        <li>
                            <a href="<?php the_permalink(); ?>#comments" class="thrive_link_post_no_comments"><?php echo get_comments_number(); ?> comments</a>
                        </li>
                    <?php endif ?>
                </ul>
                <?php if (isset($options['meta_post_tags']) && $options['meta_post_tags'] == 1): ?>
                    <?php
                    $posttags = get_the_tags();
                    if ($posttags):
                        ?>
                        <div class="tgs right">
                            <span class="icn icn-1"></span>

                            <div class="showTags">
                                <span>Tags&nbsp;&darr;&nbsp;</span>

                                <p class="tagsList" style="display: none">
                                    <?php foreach ($posttags as $key => $tag): ?>
                                        <a href="<?php echo get_tag_link($tag->term_id); ?>"><?php echo $tag->name; ?></a>
                                    <?php endforeach; ?>
                                </p>
                            </div>
                            <div class="clear"></div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
                <div class="clear"></div>
            </footer>
        <?php endif; ?>
        <?php tha_entry_bottom(); ?>
    </div>

</article>
<?php _thrive_render_bottom_related_posts(get_the_ID(), $options); ?>
<?php tha_entry_after(); ?>
<div class="spr"></div>