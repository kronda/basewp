<?php

class Thrive_Author_Widget extends WP_Widget
{
    public function __construct()
    {
        $widget_ops = array('classname' => 'widget_thrive_author', 'description' => __('Short introduction of the blog author/owner.', 'thrive'));
        parent::__construct('widget_thrive_author', __('Thrive Author Widget', 'thrive'), $widget_ops);
        $this->alt_option_name = 'widget_thrive_author';

        add_action('save_post', array(&$this, 'flush_widget_cache'));
        add_action('deleted_post', array(&$this, 'flush_widget_cache'));
        add_action('switch_theme', array(&$this, 'flush_widget_cache'));
    }

    function widget($args, $instance)
    {
        if (!isset($args['widget_id'])) {
            $args['widget_id'] = "widget-thrive" . rand(0, 999);
        }
        $author_email = null;
        if ($instance['user_profile'] && $instance['user_profile'] > 0) {
            $author_email = get_the_author_meta('user_email', $instance['user_profile']);
        }
        ?>
        <section class="authorBox" id="<?php echo $args['widget_id']; ?>">
            <div class="scn">
                <div class="awr">
                    <?php if ($instance['title']): ?>
                        <p class="ttl"><?php echo $instance['title'] ?></p>
                    <?php endif; ?>
                    <?php if ($instance['custom_image'] && $instance['custom_image'] != ""): ?>
                        <img src="<?php echo $instance['custom_image'] ?>" alt="" title="" class="avatar"/>
                    <?php elseif ($author_email): ?>
                        <?php echo get_avatar($author_email, 280); ?>
                    <?php endif; ?>
                    <p>
                        <?php echo wpautop($instance['custom_text']); ?>
                    </p>

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
        $instance['custom_image'] = strip_tags($new_instance['custom_image']);
        $instance['custom_text'] = ($new_instance['custom_text']);
        $instance['user_profile'] = (int)$new_instance['user_profile'];

        $this->flush_widget_cache();


        $alloptions = wp_cache_get('alloptions', 'options');
        if (isset($alloptions['widget_thrive_author']))
            delete_option('widget_thrive_author');

        return $instance;
    }

    function flush_widget_cache()
    {
        wp_cache_delete('widget_thrive_author', 'widget');
    }

    function form($instance)
    {
        $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
        $profile = isset($instance['user_profile']) ? absint($instance['user_profile']) : 0;
        $custom_image = isset($instance['custom_image']) ? esc_attr($instance['custom_image']) : '';
        $custom_text = isset($instance['custom_text']) ? ($instance['custom_text']) : '';
        //$custom_url = isset($instance['custom_url']) ? esc_attr($instance['custom_url']) : '';
        $all_users = get_users(array(
            'fields' => array('ID', 'user_login'),
        ));
        $user_info = get_userdata($profile);
        ?>
        <p><label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:', 'thrive'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>"
                   type="text" value="<?php echo esc_attr($title); ?>"/></p>

        <!--
        <p><label for="<?php echo esc_attr($this->get_field_id('user_profile')); ?>"><?php _e('User profile', 'thrive'); ?></label>
            <select id="<?php echo esc_attr($this->get_field_id('user_profile')); ?>" name="<?php echo esc_attr($this->get_field_name('user_profile')); ?>">
                <option value="0"><?php _e('Select user', 'thrive'); ?></option>
                <?php foreach ($all_users as $u): ?>
                    <option value="<?php echo $u->ID; ?>" <?php if ($u->ID == $profile): ?>selected<?php endif ?>><?php echo $u->user_login; ?></option>
                <?php endforeach; ?>
            </select> 
        </p>
        -->
        <input type="hidden" value="<?php echo $profile; ?>" class="thrive_author_widget_hidden_profile"
               name="<?php echo esc_attr($this->get_field_name('user_profile')); ?>"/>

        <p><label for="<?php echo esc_attr($this->get_field_id('user_profile')); ?>"><?php _e('User profile:', 'thrive'); ?></label>
            <input class="widefat thrive_author_widget_txt_profile" id="<?php echo esc_attr($this->get_field_id('user_profile')); ?>" type="text"
                   value="<?php if ($user_info): ?><?php echo $user_info->user_login; ?><?php endif; ?>"/></p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('custom_image')); ?>"><?php _e('Custom image:', 'thrive'); ?></label>
            <input class="widefat thrive_author_widget_txt_image" id="<?php echo esc_attr($this->get_field_id('custom_image')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('custom_image')); ?>" type="text" value="<?php echo esc_attr($custom_image); ?>"/>
            <input type='button' class="thrive_author_widget_btn_upload" id='<?php echo esc_attr($this->get_field_id('custom_image')); ?>_btn_upload'
                   value='<?php _e('Upload', 'thrive'); ?>'/>
        </p>

        <p><label for="<?php echo esc_attr($this->get_field_id('custom_text')); ?>"><?php _e('Custom text:', 'thrive'); ?></label>
            <textarea style="width: 100%; height: 200px;" id="<?php echo esc_attr($this->get_field_id('custom_text')); ?>"
                      name="<?php echo esc_attr($this->get_field_name('custom_text')); ?>"><?php echo esc_attr($custom_text); ?></textarea></p>

        <script type="text/javascript">
            if (ThriveWidgetsOptions.controls_binded === 0) {
                ThriveWidgetsOptions.bind_handlers();
            }
        </script>
        <?php
    }

}