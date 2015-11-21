<?php

class Thrive_Custom_Phone extends WP_Widget
{

    public function __construct()
    {
        $widget_ops = array('classname' => 'widget_thrive_phone', 'description' => __('Click-to-Call phone number with a call to action text line.', 'thrive'));
        parent::__construct('widget_thrive_phone', __('Thrive Custom Phone', 'thrive'), $widget_ops);

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
        <section class="widget phone-number" id="<?php echo $args['widget_id']; ?>">
            <div class="scn">
                <div class="awr">
                    <div class="phone <?php echo $instance['color'] ?>">
                        <a href="tel:<?php echo $instance['custom_phone']; ?>">
                            <div class="phr">
                                <span class="fphr"><?php echo $instance['phone_text']; ?></span>
                                <span class="mphr"><?php echo $instance['mobile_phone_text']; ?></span>
                                <span class="apnr"><?php echo $instance['custom_phone']; ?></span>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </section>
        <?php
    }

    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['phone_text'] = strip_tags($new_instance['phone_text']);
        $instance['mobile_phone_text'] = strip_tags($new_instance['mobile_phone_text']);
        $instance['color'] = strip_tags($new_instance['color']);
        $instance['custom_phone'] = ($new_instance['custom_phone']);
        $this->flush_widget_cache();

        $alloptions = wp_cache_get('alloptions', 'options');
        if (isset($alloptions['widget_thrive_phone']))
            delete_option('widget_thrive_phone');

        return $instance;
    }

    function flush_widget_cache()
    {
        wp_cache_delete('widget_thrive_phone', 'widget');
    }

    function form($instance)
    {
        $phone_text = isset($instance['phone_text']) ? esc_attr($instance['phone_text']) : '';
        $mobile_phone_text = isset($instance['phone_text']) ? esc_attr($instance['mobile_phone_text']) : '';
        $custom_phone = isset($instance['custom_phone']) ? ($instance['custom_phone']) : '';
        $color = isset($instance['color']) ? esc_attr($instance['color']) : '';

        $all_colors = _thrive_get_color_scheme_options();
        $all_colors = array_merge(array('default' => __("Default", 'thrive')), $all_colors);
        ?>
        <p><label
                for="<?php echo esc_attr($this->get_field_id('phone_text')); ?>"><?php _e('Call to action text:', 'thrive'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('phone_text')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('phone_text')); ?>" type="text"
                   value="<?php echo esc_attr($phone_text); ?>"/></p>
        <p><label
                for="<?php echo esc_attr($this->get_field_id('mobile_phone_text')); ?>"><?php _e('Mobile Call to action text:', 'thrive'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('mobile_phone_text')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('mobile_phone_text')); ?>" type="text"
                   value="<?php echo esc_attr($mobile_phone_text); ?>"/></p>

        <p><label
                for="<?php echo esc_attr($this->get_field_id('custom_phone')); ?>"><?php _e('Custom phone:', 'thrive'); ?></label>
            <input class="widefat" type="text" id="<?php echo esc_attr($this->get_field_id('custom_phone')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('custom_phone')); ?>" value="<?php echo esc_attr($custom_phone); ?>"/>
        </p>

        <p><label for="<?php echo esc_attr($this->get_field_id('color')); ?>"><?php _e('Mobile button color', 'thrive'); ?></label>
            <select id="<?php echo esc_attr($this->get_field_id('color')); ?>" name="<?php echo esc_attr($this->get_field_name('color')); ?>">
                <?php foreach ($all_colors as $key => $c): ?>
                    <option value="<?php echo $key; ?>" <?php if ($color == $key): ?>selected<?php endif ?>><?php echo $c; ?></option>
                <?php endforeach; ?>
            </select>
        </p>

        <?php
    }

}
