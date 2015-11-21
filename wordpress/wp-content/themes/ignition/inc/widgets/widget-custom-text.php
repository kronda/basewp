<?php

class Thrive_Custom_Text extends WP_Widget
{
    public function __construct()
    {
        $widget_ops = array('classname' => 'widget_thrive_text', 'description' => __('Custom text with styled widget border', 'thrive'));
        parent::__construct('widget_thrive_text', __('Thrive Custom Text', 'thrive'), $widget_ops);

        add_action('save_post', array(&$this, 'flush_widget_cache'));
        add_action('deleted_post', array(&$this, 'flush_widget_cache'));
        add_action('switch_theme', array(&$this, 'flush_widget_cache'));
    }

    function widget($args, $instance)
    {
        if (!isset($args['widget_id'])) {
            $args['widget_id'] = "widget-thrive" . rand(0, 999);
        }
        ?>

        <section id="<?php echo $args['widget_id']; ?>">
            <div class="scn">
                <div class="awr">
                    <?php if ($instance['title']): ?>
                        <p class="ttl"><?php echo $instance['title'] ?></p>
                    <?php endif; ?>
                    <?php echo wpautop(do_shortcode($instance['custom_text'])); ?>
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
        $instance['custom_text'] = ($new_instance['custom_text']);
        $this->flush_widget_cache();

        $alloptions = wp_cache_get('alloptions', 'options');
        if (isset($alloptions['widget_thrive_text']))
            delete_option('widget_thrive_text');

        return $instance;
    }

    function flush_widget_cache()
    {
        wp_cache_delete('widget_thrive_text', 'widget');
    }

    function form($instance)
    {
        $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
        $custom_text = isset($instance['custom_text']) ? ($instance['custom_text']) : '';
        ?>
        <p><label
                for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:', 'thrive'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text"
                   value="<?php echo esc_attr($title); ?>"/></p>

        <p><label
                for="<?php echo esc_attr($this->get_field_id('custom_text')); ?>"><?php _e('Custom text:', 'thrive'); ?></label>
            <textarea style="width: 100%; height: 200px;"
                      id="<?php echo esc_attr($this->get_field_id('custom_text')); ?>"
                      name="<?php echo esc_attr($this->get_field_name('custom_text')); ?>"><?php echo esc_attr($custom_text); ?></textarea>
        </p>

        <?php
    }

}