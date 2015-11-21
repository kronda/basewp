<?php

class Thrive_Appr_Recent extends WP_Widget
{

    public function __construct()
    {
        $widget_ops = array('classname' => 'widget_thrive_appr_recent', 'description' => __('Apprentice Recent Lessons', 'thrive'));
        parent::__construct('widget_thrive_appr_recent', __('Apprentice Recent Lessons', 'thrive'), $widget_ops);

        add_action('save_post', array(&$this, 'flush_widget_cache'));
        add_action('deleted_post', array(&$this, 'flush_widget_cache'));
        add_action('switch_theme', array(&$this, 'flush_widget_cache'));
    }

    function widget($args, $instance)
    {
        $no_posts = (int)$instance['no_posts'];
        $query = new WP_Query(array('post_type' => TT_APPR_POST_TYPE_LESSON, 'posts_per_page' => $no_posts));
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
        $this->flush_widget_cache();

        $alloptions = wp_cache_get('alloptions', 'options');
        if (isset($alloptions['widget_thrive_appr_recent']))
            delete_option('widget_thrive_appr_recent');

        return $instance;
    }

    function flush_widget_cache()
    {
        wp_cache_delete('widget_thrive_appr_recent', 'widget');
    }

    function form($instance)
    {
        $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
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


        <?php
    }

}