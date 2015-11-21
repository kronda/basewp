<?php
$options = thrive_get_theme_options();

$comment_nb_class = ($options['sidebar_alignement'] == "right") ? "comment_nb" : "right_comment_nb";
$featured_image_data = thrive_get_post_featured_image(get_the_ID(), $options['featured_image_style']);
$featured_image = $featured_image_data['image_src'];
$featured_image_alt = $featured_image_data['image_alt'];
$featured_image_title = $featured_image_data['image_title'];

$fname = get_the_author_meta('first_name');
$lname = get_the_author_meta('last_name');
$author_name = get_the_author_meta('display_name');
$display_name = empty($author_name) ? $fname . " " . $lname : $author_name;
?>
<?php tha_entry_before(); ?>

<article <?php if (is_sticky()): ?>class="sticky"<?php endif; ?>>
    <div class="awr">

        <h2 class="entry-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

        <?php if (($options['featured_image_style'] == "wide" || $options['featured_image_style'] == "thumbnail") && $featured_image): ?>
            <a class="psb" href="<?php the_permalink(); ?>">
                <img src="<?php echo $featured_image; ?>" alt="<?php echo $featured_image_alt; ?>" title="<?php echo $featured_image_title; ?>"
                     class="<?php if ($options['featured_image_style'] == "wide"): ?>fwI<?php else: ?>alignleft afim<?php endif; ?>"/>
            </a>
        <?php endif; ?>


        <?php if ($options['other_show_excerpt'] != 1): ?>
            <?php the_content(); ?>
        <?php else: ?>
            <p>
                <?php the_excerpt(); ?>
            </p>
            <?php $read_more_text = ($options['other_read_more_text'] != "") ? $options['other_read_more_text'] : "Read more"; ?>
            <?php if ($options['other_read_more_type'] == "button"): ?>
                <div class='mre'><a href='<?php the_permalink(); ?>'><span><?php echo $read_more_text ?></span><span class='awe'>&#xf18e;</span>

                        <div class='clear'></div>
                    </a></div>
            <?php else: ?>
                <a href='<?php the_permalink(); ?>' class='rmt'><?php echo $read_more_text ?></a>
            <?php endif; ?>
        <?php endif; ?>

        <div class="clear"></div>

        <?php
        if (isset($options['display_meta']) && $options['display_meta'] == 1):
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
                            <?php _e('by', 'thrive'); ?> <a
                                href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>"><?php echo get_the_author(); ?></a>
                        <?php endif; ?>
                    </li>
                    <?php if (isset($options['meta_post_date']) && $options['meta_post_date'] == 1): ?>
                        <?php if ($has_sep) : ?>
                            <li class="sep">|</li>
                        <?php endif ?>
                        <li>
                            <?php if ($options['relative_time'] == 1): ?>
                                <?php echo thrive_human_time(get_the_time('U')); ?>
                            <?php else: ?>
                                <?php echo get_the_date(); ?>
                            <?php endif; $has_sep = true; ?>
                        </li>
                    <?php endif; ?>
                    <?php if (!empty($options['meta_comment_count']) && get_comments_number()): ?>
                        <?php if ($has_sep) : ?>
                            <li class="sep">|</li><?php $has_sep = false; endif ?>
                        <li>
                            <a href="<?php the_permalink(); ?>#comments"
                               class="thrive_link_post_no_comments"><?php echo get_comments_number(); ?> <?php _e('comments', 'thrive'); ?></a>
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
                                <span><?php _e('Tags', 'thrive'); ?>&nbsp;&darr;&nbsp;</span>

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