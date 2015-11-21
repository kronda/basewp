<?php

class Thrive_Appr_Favorites extends WP_Widget
{

    public function __construct()
    {
        $widget_ops = array('classname' => 'widget_thrive_appr_favorites', 'description' => __('Apprentice Favorite Lessons', 'thrive'));
        parent::__construct('widget_thrive_appr_favorites', __('Apprentice Favorite Lessons', 'thrive'), $widget_ops);

        add_action('save_post', array(&$this, 'flush_widget_cache'));
        add_action('deleted_post', array(&$this, 'flush_widget_cache'));
        add_action('switch_theme', array(&$this, 'flush_widget_cache'));
    }

    function widget($args, $instance)
    {

        if (!is_user_logged_in()) {
            echo __("You need to be logged in to use this feature", 'thrive');
            return;
        }
        global $current_user;

        $no_posts = (int)$instance['no_posts'];
        $posts = array();
        $thrive_favorites = get_user_meta($current_user->ID, THRIVE_APPR_FAV_META_KEY, true);
        if (is_array($thrive_favorites)) {
            foreach ($thrive_favorites as $key => $pid) {
                $tempPost = get_post($pid);
                if ($tempPost->post_status == "publish" && $tempPost->post_type == TT_APPR_POST_TYPE_LESSON) {
                    array_push($posts, get_post($pid));
                }
            }
        }
        $posts = array_slice($posts, 0, $no_posts);
        $default_text = isset($instance['default_text']) && !empty($instance['default_text'])
            ? $instance['default_text'] : 'Lessons you mark as your favorites will be listed here.';
        ?>

        <section class="wat">
            <div class="scn">
                <?php if ($instance['title']): ?>
                    <p class="ttl"><?php echo $instance['title'] ?></p>
                <?php endif; ?>
                <?php if (count($posts) > 0): ?>
                    <ul>
                        <?php foreach ($posts as $p): ?>
                            <?php if (!empty($p->post_title)): ?>
                                <li><a href="<?php echo get_permalink($p->ID); ?>"><?php echo $p->post_title; ?></a></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <span><?php echo $default_text; ?></span>
                <?php endif; ?>

                <div class="clear"></div>
            </div>
        </section>
        <?php
    }

    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['default_text'] = strip_tags($new_instance['default_text']);
        $instance['no_posts'] = ($new_instance['no_posts']);
        $this->flush_widget_cache();

        $alloptions = wp_cache_get('alloptions', 'options');
        if (isset($alloptions['widget_thrive_appr_favorites']))
            delete_option('widget_thrive_appr_favorites');

        return $instance;
    }

    function flush_widget_cache()
    {
        wp_cache_delete('widget_thrive_appr_favorites', 'widget');
    }

    function form($instance)
    {
        $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
        $default_text = isset($instance['default_text']) && !empty($instance['default_text']) ? esc_attr($instance['default_text']) : 'Lessons you mark as your favorites will be listed here.';
        $no_posts = isset($instance['no_posts']) ? (int)($instance['no_posts']) : 5;
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

        <p><label
                for="<?php echo esc_attr($this->get_field_id('default_text')); ?>"><?php _e('Default Text:', 'thrive'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('default_text')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('default_text')); ?>" type="text"
                   value="<?php echo esc_attr($default_text); ?>"/></p>

        <?php
    }

}