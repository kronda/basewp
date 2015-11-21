<?php

class Thrive_Appr_Popular extends WP_Widget
{

    public function __construct()
    {
        $widget_ops = array('classname' => 'widget_thrive_appr_popular', 'description' => __('Apprentice Popular Lessons', 'thrive'));
        parent::__construct('widget_thrive_appr_popular', __('Apprentice Popular Lessons', 'thrive'), $widget_ops);

        add_action('save_post', array(&$this, 'flush_widget_cache'));
        add_action('deleted_post', array(&$this, 'flush_widget_cache'));
        add_action('switch_theme', array(&$this, 'flush_widget_cache'));
    }

    function widget($args, $instance)
    {

        $no_posts = (int)$instance['no_posts'];
        $order_by = (int)$instance['order_by']; //1 is by favorites, 0 by comments

        $appr_favorites_enabled = thrive_get_theme_options("appr_favorites");
        if ($appr_favorites_enabled != 1) {
            $order_by = 0;
        }

        if ($order_by != 1) {
            $query = new WP_Query(array(
                'order' => 'DESC',
                'orderby' => 'comment_count',
                'posts_per_page' => $no_posts,
                'post_type' => TT_APPR_POST_TYPE_LESSON
            ));
        } else {
            $query = new WP_Query(array(
                'order' => 'ASC',
                'orderby' => THRIVE_APPR_FAV_POST_META_KEY,
                'posts_per_page' => $no_posts,
                'post_type' => TT_APPR_POST_TYPE_LESSON
            ));
        }
        $posts = $query->get_posts();
        ?>

        <section class="wat">
            <div class="scn">
                <?php if ($instance['title']): ?>
                    <p class="ttl"><?php echo $instance['title'] ?></p>
                <?php endif; ?>
                <ul>
                    <?php foreach ($posts as $p): ?>
                        <?php if (!empty($p->post_title)): ?>
                            <li><a href="<?php echo get_permalink($p->ID); ?>"><?php echo $p->post_title; ?></a></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>

                <div class="clear"></div>
            </div>
        </section>
        <?php
    }

    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;

        $instance['title'] = strip_tags($new_instance['title']);
        $instance['no_posts'] = ($new_instance['no_posts']);
        $instance['order_by'] = (int)$new_instance['order_by'];

        $this->flush_widget_cache();

        $alloptions = wp_cache_get('alloptions', 'options');
        if (isset($alloptions['widget_thrive_appr_popular']))
            delete_option('widget_thrive_appr_popular');

        return $instance;
    }

    function flush_widget_cache()
    {
        wp_cache_delete('widget_thrive_appr_popular', 'widget');
    }

    function form($instance)
    {
        $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
        $no_posts = isset($instance['no_posts']) ? (int)($instance['no_posts']) : 5;
        $order_by = isset($instance['order_by']) ? absint($instance['order_by']) : 0;
        $appr_favorites_enabled = thrive_get_theme_options("appr_favorites");
        $disabled_fav_str = ($appr_favorites_enabled == 1) ? "" : " disabled";
        if ($appr_favorites_enabled != 1) {
            $order_by = 0;
        }
        ?>
        <p><label
                for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:', 'thrive'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text"
                   value="<?php echo esc_attr($title); ?>"/></p>

        <p><label
                for="<?php echo esc_attr($this->get_field_id('no_posts')); ?>"><?php _e('Number of lessons:', 'thrive'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('no_posts')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('no_posts')); ?>" type="text"
                   value="<?php echo esc_attr($no_posts); ?>"/></p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('order_by')); ?>"><b><?php _e('Order by', 'thrive'); ?></b></label> <br/>
            <?php _e('Comments', 'thrive'); ?>
            <input type="radio" name="<?php echo esc_attr($this->get_field_name('order_by')); ?>" id="<?php echo esc_attr($this->get_field_id('order_by')); ?>"
                   <?php if ($order_by == 0): ?>checked<?php endif ?> value="0"/>
            <?php _e('Favorites', 'thrive'); ?>
            <input type="radio" name="<?php echo esc_attr($this->get_field_name('order_by')); ?>" id="<?php echo esc_attr($this->get_field_id('order_by')); ?>"
                   <?php if ($order_by == 1): ?>checked<?php endif ?> value="1" <?php echo $disabled_fav_str; ?> />

        </p>

        <?php
    }

}