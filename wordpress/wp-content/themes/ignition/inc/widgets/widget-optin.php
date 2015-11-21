<?php

class Thrive_Optin_Widget extends WP_Widget
{

    public function __construct()
    {
        $widget_ops = array('classname' => 'widget_thrive_optin', 'description' => __('Get more subscribers for your newsletter/mailing list.', 'thrive'));
        parent::__construct('widget_thrive_optin', __('Thrive Opt-in Widget', 'thrive'), $widget_ops);
        $this->alt_option_name = 'widget_thrive_optin';

        add_action('save_post', array(&$this, 'flush_widget_cache'));
        add_action('deleted_post', array(&$this, 'flush_widget_cache'));
        add_action('switch_theme', array(&$this, 'flush_widget_cache'));
    }

    function widget($args, $instance)
    {
        if (!isset($args['widget_id'])) {
            $args['widget_id'] = "widget-thrive" . rand(0, 999);
        }
        $widget_container_class = strtolower($instance['color']) . "_cta";

        if (!isset($instance['optin']) || (isset($instance['optin']) && !get_post($instance['optin']))) {
            echo "There are some problems with the configuration of the opt-in widget";
            return;
        }

        //form action
        $optinFormAction = get_post_meta($instance['optin'], '_thrive_meta_optin_form_action', true);

        //form method
        $optinFormMethod = get_post_meta($instance['optin'], '_thrive_meta_optin_form_method', true);
        $optinFormMethod = strtolower($optinFormMethod);
        $optinFormMethod = $optinFormMethod === 'post' || $optinFormMethod === 'get' ? $optinFormMethod : 'post';

        //form hidden inputs
        $optinHiddenInputs = get_post_meta($instance['optin'], '_thrive_meta_optin_hidden_inputs', true);

        //form fields
        $optinFieldsJson = get_post_meta($instance['optin'], '_thrive_meta_optin_fields_array', true);
        $optinFieldsArray = json_decode($optinFieldsJson, true);

        //form not visible inputs
        $optinNotVisibleInputs = get_post_meta($instance['optin'], '_thrive_meta_optin_not_visible_inputs', true);

        if (!is_array($optinFieldsArray)) {
            echo "There are some problems with the configuration of the opt-in widget";
            return;
        }
        ?>
        <section id="<?php echo $args['widget_id']; ?>">
            <?php if ($instance['title'] && $instance['title'] != ""): ?>
                <p class="ttl"><?php echo $instance['title'] ?></p>
            <?php endif; ?>
            <div class="oab <?php echo $widget_container_class; ?>">
                <h3><?php echo $instance['headline_text']; ?></h3>

                <p><?php echo $instance['body_text']; ?></p>
                <?php if ($instance['custom_image'] && $instance['custom_image'] != ""): ?>
                    <img src="<?php echo $instance['custom_image']; ?>" alt="">
                <?php endif; ?>
                <form class="ofr" action="<?php echo $optinFormAction; ?>" method="<?php echo $optinFormMethod ?>">

                    <?php echo $optinHiddenInputs; ?>

                    <?php echo $optinNotVisibleInputs; ?>

                    <?php foreach ($optinFieldsArray as $name_attr => $field_label): ?>
                        <?php echo Thrive_OptIn::getInstance()->getInputHtml($name_attr, $field_label) ?>
                    <?php endforeach; ?>

                    <button type="submit" value="">
                        <?php echo $instance['button_text']; ?>
                    </button>

                </form>
                <div class="clear"></div>
            </div>
        </section>
        <?php
    }

    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['color'] = strip_tags($new_instance['color']);
        $instance['headline_text'] = strip_tags($new_instance['headline_text']);
        $instance['body_text'] = strip_tags($new_instance['body_text']);
        $instance['button_text'] = strip_tags($new_instance['button_text']);
        $instance['custom_image'] = strip_tags($new_instance['custom_image']);
        $instance['optin'] = (int)$new_instance['optin'];

        $this->flush_widget_cache();

        $alloptions = wp_cache_get('alloptions', 'options');
        if (isset($alloptions['widget_thrive_optin']))
            delete_option('widget_thrive_optin');

        return $instance;
    }

    function flush_widget_cache()
    {
        wp_cache_delete('widget_thrive_optin', 'widget');
    }

    function form($instance)
    {
        $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
        $color = isset($instance['color']) ? esc_attr($instance['color']) : '';
        $headline_text = isset($instance['headline_text']) ? esc_attr($instance['headline_text']) : '';
        $body_text = isset($instance['body_text']) ? esc_attr($instance['body_text']) : '';
        $button_text = isset($instance['button_text']) ? esc_attr($instance['button_text']) : '';
        $custom_image = isset($instance['custom_image']) ? esc_attr($instance['custom_image']) : '';
        $optin = isset($instance['optin']) ? absint($instance['optin']) : 0;

        $all_optins = get_posts(array('post_type' => "thrive_optin", 'posts_per_page' => -1));

        $all_colors = _thrive_get_color_scheme_options("optin");
        ?>
        <p><label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:', 'thrive'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>"
                   type="text" value="<?php echo esc_attr($title); ?>"/></p>

        <p><label for="<?php echo esc_attr($this->get_field_id('color')); ?>"><?php _e('Color', 'thrive'); ?></label>
            <select id="<?php echo esc_attr($this->get_field_id('color')); ?>" name="<?php echo esc_attr($this->get_field_name('color')); ?>">
                <?php foreach ($all_colors as $key => $c): ?>
                    <option value="<?php echo $key; ?>" <?php if ($color == $key): ?>selected<?php endif ?>><?php echo $c; ?></option>
                <?php endforeach; ?>
            </select>
        </p>

        <p><label for="<?php echo esc_attr($this->get_field_id('headline_text')); ?>"><?php _e('Headline text:', 'thrive'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('headline_text')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('headline_text')); ?>" type="text" value="<?php echo esc_attr($headline_text); ?>"/></p>

        <p><label for="<?php echo esc_attr($this->get_field_id('body_text')); ?>"><?php _e('Text:', 'thrive'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('body_text')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('body_text')); ?>" type="text" value="<?php echo esc_attr($body_text); ?>"/></p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('custom_image')); ?>"><?php _e('Custom image:', 'thrive'); ?></label>
            <input class="widefat thrive_optin_widget_txt_image" id="<?php echo esc_attr($this->get_field_id('custom_image')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('custom_image')); ?>" type="text" value="<?php echo esc_attr($custom_image); ?>"/>
            <input type='button' class="thrive_optin_widget_btn_upload" id='<?php echo esc_attr($this->get_field_id('custom_image')); ?>_btn_upload'
                   value='<?php _e('Upload', 'thrive'); ?>'/>
        </p>

        <p><label for="<?php echo esc_attr($this->get_field_id('button_text')); ?>"><?php _e('Button text:', 'thrive'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('button_text')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('button_text')); ?>" type="text" value="<?php echo esc_attr($button_text); ?>"/></p>

        <p><label for="<?php echo esc_attr($this->get_field_id('optin')); ?>"><?php _e('Opt-In Integration', 'thrive'); ?></label>
            <select name='<?php echo esc_attr($this->get_field_name('optin')); ?>'>
                <option value='0'></option>
                <?php foreach ($all_optins as $p): ?>
                    <option value='<?php echo $p->ID ?>' <?php if ($optin == $p->ID): ?>selected<?php endif; ?>><?php echo $p->post_title; ?></option>
                <?php endforeach; ?>
            </select></p>

        <script type="text/javascript">
            if (ThriveWidgetsOptions.controls_binded === 0) {
                ThriveWidgetsOptions.bind_handlers();
            }
        </script>

        <?php
    }

}