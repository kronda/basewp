<?php

class Thrive_Related_Widget extends WP_Widget
{

    public function __construct()
    {
        $widget_ops = array('classname' => 'widget_thrive_related', 'description' => __('Styled list of recent, popular or related posts.', 'thrive'));
        parent::__construct('widget_thrive_related', __('Thrive Posts', 'thrive'), $widget_ops);
        $this->alt_option_name = 'widget_thrive_related';

        add_action('save_post', array(&$this, 'flush_widget_cache'));
        add_action('deleted_post', array(&$this, 'flush_widget_cache'));
        add_action('switch_theme', array(&$this, 'flush_widget_cache'));
    }

    function widget($args, $instance)
    {
        if (!isset($args['widget_id'])) {
            $args['widget_id'] = "widget-thrive" . rand(0, 999);
        }
        if (isset($instance['show_date'])) {
            $posts = $this->_get_post_list($instance['list_type'], $instance['no_posts'], $instance['display_thumbnails'], $instance['show_date']);
        } else {
            $posts = $this->_get_post_list($instance['list_type'], $instance['no_posts'], $instance['display_thumbnails']);
        }
        $txt_class = ($instance['display_thumbnails'] == 1) ? "" : " noImageTab";
        if (count($posts) == 0) {
            return;
        }
        ?>

        <section class="rw" id="<?php echo $args['widget_id']; ?>">
            <div class="scn">
                <div class="awr">
                    <div class="twr">
                        <?php if ($instance['title']): ?>
                            <p class="upp ttl"><?php echo $instance['title'] ?></p>
                        <?php endif; ?>
                    </div>

                    <?php if (count($posts) == 0 && $instance['list_type'] == "related"): ?>
                        <div class="left txt noImageTab">
                            <?php echo thrive_get_theme_options("related_no_text"); ?>
                        </div>
                    <?php else: ?>
                        <?php foreach ($posts as $post): ?>
                            <div class="pps clearfix">
                                <div class="left tim">
                                    <?php if (isset($instance['display_thumbnails']) && $instance['display_thumbnails'] == 1): ?>
                                        <?php if (isset($post['image']) && $post['image']): ?>
                                            <div class="wti" style="background-image: url('<?php echo $post['image']; ?>')"></div>
                                        <?php else: ?>
                                            <div class="wti"
                                                 style="background-image: url('<?php echo get_template_directory_uri(); ?>/images/default.png')"></div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                <div class="left txt<?php echo $txt_class; ?>">
                                    <a href="<?php echo $post['url']; ?>"><?php echo $post['title']; ?></a>
                                </div>
                                <?php if (isset($post['show_date']) && $post['show_date']) : ?>
                                    <span class="post-date">
                                    <?php echo date(get_option('date_format'), strtotime($post['date'])); ?>
                                </span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <div class="clear"></div>
                </div>
            </div>
        </section>
        <?php
    }

    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['list_type'] = $new_instance['list_type'];
        $instance['no_posts'] = $new_instance['no_posts'];
        $instance['display_thumbnails'] = (int)$new_instance['display_thumbnails'];
        $instance['show_date'] = (int)$new_instance['show_date'];

        $this->flush_widget_cache();


        $alloptions = wp_cache_get('alloptions', 'options');
        if (isset($alloptions['widget_thrive_related']))
            delete_option('widget_thrive_related');

        return $instance;
    }

    function flush_widget_cache()
    {
        wp_cache_delete('widget_thrive_related', 'widget');
    }

    function form($instance)
    {
        $related_posts_enabled = thrive_get_theme_options('related_posts_enabled');
        $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
        $list_type = isset($instance['list_type']) ? $instance['list_type'] : 'recent';
        $no_posts = isset($instance['no_posts']) ? esc_attr($instance['no_posts']) : '';
        $display_thumbnails = isset($instance['display_thumbnails']) ? absint($instance['display_thumbnails']) : 0;
        $show_date = isset($instance['show_date']) ? absint($instance['show_date']) : 0;
        ?>
        <p><label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:', 'thrive'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>"
                   type="text" value="<?php echo esc_attr($title); ?>"/></p>

        <p><label for="<?php echo esc_attr($this->get_field_id('list_type')); ?>"><?php _e('List type', 'thrive'); ?></label>
            <select id="<?php echo esc_attr($this->get_field_id('list_type')); ?>" name="<?php echo esc_attr($this->get_field_name('list_type')); ?>">
                <option value="recent" <?php if ($list_type == "recent"): ?>selected<?php endif; ?>><?php _e("Recent posts", 'thrive'); ?></option>
                <option value="popular" <?php if ($list_type == "popular"): ?>selected<?php endif; ?>><?php _e("Popular posts", 'thrive'); ?></option>
                <option value="related" <?php if ($list_type == "related"): ?>selected<?php endif; ?>><?php _e("Related Posts", 'thrive'); ?></option>
            </select>
        </p>

        <p><label for="<?php echo esc_attr($this->get_field_id('no_posts')); ?>"><?php _e('Number of posts:', 'thrive'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('no_posts')); ?>" type="text"
                   value="<?php echo $no_posts; ?>" name="<?php echo esc_attr($this->get_field_name('no_posts')); ?>"/></p>

        <p>
            <input type="checkbox" name="<?php echo esc_attr($this->get_field_name('display_thumbnails')); ?>"
                   <?php if ($display_thumbnails == "1"): ?>checked="checked"<?php endif; ?>
                   class="thrive_chk_show_thumbnail" value="1"/> <?php _e('Show thumbnails', 'thrive'); ?>
        </p>
        <p>
            <input type="checkbox" name="<?php echo esc_attr($this->get_field_name('show_date')); ?>"
                   <?php if ($show_date == "1"): ?>checked="checked"<?php endif; ?>
                   class="thrive_chk_show_date" value="1"/> <?php _e('Show date', 'thrive'); ?>
        </p>

        <script type="text/javascript">
            if (ThriveWidgetsOptions.controls_binded === 0) {
                ThriveWidgetsOptions.bind_handlers();
            }
        </script>
        <?php
    }

    function _get_post_list($type = 'recent', $no_posts = 5, $display_thumb = false, $display_date = true)
    {
        $items_array = array();

        if ($type == "recent") {
            $r = new WP_Query(array(
                'orderby' => 'date',
                'posts_per_page' => $no_posts,
                'ignore_sticky_posts' => 1
            ));
            $recent_posts = $r->get_posts();
            foreach ($recent_posts as $p) {
                $temp_item = array('title' => $p->post_title, 'url' => get_permalink($p->ID), 'date' => get_the_time('Y-m-d', $p->ID));
                if ($display_thumb == 1) {
                    $temp_item['image'] = $this->_get_featured_image($p->ID);
                }
                if ($display_date) {
                    $temp_item['show_date'] = 1;
                }
                array_push($items_array, $temp_item);
            }
            $items_array = array_slice($items_array, 0, $no_posts);
            return $items_array;
        }

        if ($type == "popular") {
            $r = new WP_Query(array(
                'order' => 'DESC',
                'orderby' => 'comment_count',
                'posts_per_page' => $no_posts,
                'ignore_sticky_posts' => 1
            ));
            $popular_posts = $r->get_posts();
            foreach ($popular_posts as $p) {
                $temp_item = array('title' => $p->post_title, 'url' => get_permalink($p->ID), 'date' => get_the_time('Y-m-d', $p->ID));
                if ($display_thumb) {
                    $temp_item['image'] = $this->_get_featured_image($p->ID);
                }
                if ($display_date) {
                    $temp_item['show_date'] = 1;
                }
                array_push($items_array, $temp_item);
            }
            $items_array = array_slice($items_array, 0, $no_posts);
            return $items_array;
        }

        if ($type == "related") {

            $related_posts_enabled = thrive_get_theme_options('related_posts_enabled');

            if ($related_posts_enabled == 1) {
                $relatedPostsStr = get_post_meta(get_the_ID(), "_thrive_meta_related_posts_list", true);

                if ($relatedPostsStr && $relatedPostsIds = json_decode($relatedPostsStr)) {
                    foreach ($relatedPostsIds as $pid) {
                        $p = get_post($pid);
                        if ($p && $p->status == "publish") {
                            $temp_item = array('title' => $p->post_title, 'url' => get_permalink($p->ID), 'date' => get_the_time('Y-m-d', $p->ID));
                            if ($display_thumb) {
                                $temp_item['image'] = $this->_get_featured_image($p->ID);
                            }
                            if ($display_date) {
                                $temp_item['show_date'] = 1;
                            }
                            array_push($items_array, $temp_item);
                        }
                    }
                }
            }
            /*
             * If the related feature is not enabled or if no items found in the related 
             * posts option get the posts from the same categories as the current post
             */
            if ($related_posts_enabled != 1 || ($related_posts_enabled == 1 && count($items_array) == 0)) {
                $related = get_posts(array('category__in' => wp_get_post_categories(get_the_ID()), 'numberposts' => $no_posts, 'post__not_in' => array(get_the_ID())));
                if ($related) {
                    foreach ($related as $p) {
                        $temp_item = array('title' => $p->post_title, 'url' => get_permalink($p->ID), 'date' => get_the_time('Y-m-d', $p->ID));
                        if ($display_thumb) {
                            $temp_item['image'] = $this->_get_featured_image($p->ID);
                        }
                        if ($display_date) {
                            $temp_item['show_date'] = 1;
                        }
                        array_push($items_array, $temp_item);
                    }
                }
            }

            $items_array = array_slice($items_array, 0, $no_posts);
            return $items_array;
        }

        return $items_array;
    }

    function _get_featured_image($postId)
    {

        $featured_image_data = thrive_get_post_featured_image($postId, "tt_post_icon");
        $featured_image = $featured_image_data['image_src'];
        return $featured_image;
    }

}