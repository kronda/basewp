<?php

class Thrive_Images_Widget extends WP_Widget
{

    public function __construct()
    {
        $widget_ops = array('classname' => 'widget_thrive_images', 'description' => __('Thrive Latest Images Widget', 'thrive'));
        parent::__construct('widget_thrive_images', __('Thrive Latest Images Widget', 'thrive'), $widget_ops);
        $this->alt_option_name = 'widget_thrive_images';

        add_action('save_post', array(&$this, 'flush_widget_cache'));
        add_action('deleted_post', array(&$this, 'flush_widget_cache'));
        add_action('switch_theme', array(&$this, 'flush_widget_cache'));
    }

    function widget($args, $instance)
    {
        if (!isset($args['widget_id'])) {
            $args['widget_id'] = "widget-thrive" . rand(0, 999);
        }
        $media_args = array(
            'post_type' => 'attachment',
            'numberposts' => ($instance['no_images'] && $instance['no_images'] > 0) ? $instance['no_images'] : 9,
            'post_status' => null
        );

        $attachments = get_posts($media_args);
        ?>
        <section class="limg" id="<?php echo $args['widget_id']; ?>">
            <div class="scn">
                <?php if (!empty($instance['title'])): ?>
                    <p class="ttl"><?php echo $instance['title']; ?></p>
                <?php endif; ?>
                <?php if ($attachments): ?>
                    <?php foreach ($attachments as $att):
                        //get the featured image size
                        $attachment_meta = wp_get_attachment_metadata($att->ID);
                        if (isset($attachment_meta['sizes']['tt_latest_images'])) {
                            $img_src = wp_get_attachment_image_src($att->ID, "tt_latest_images");
                            if (isset($img_src[0])) {
                                $img_src = $img_src[0];
                            }
                        } else {
                            $img_src = wp_get_attachment_thumb_url($att->ID);
                        }

                        ?>
                        <div class="left"><img src="<?php echo $img_src; ?>" alt=""></div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <?php _e("There are no images in the media library", 'thrive'); ?>
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
        $instance['no_images'] = strip_tags($new_instance['no_images']);

        $this->flush_widget_cache();

        $alloptions = wp_cache_get('alloptions', 'options');
        if (isset($alloptions['widget_thrive_images']))
            delete_option('widget_thrive_images');

        return $instance;
    }

    function flush_widget_cache()
    {
        wp_cache_delete('widget_thrive_images', 'widget');
    }

    function form($instance)
    {
        $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
        $no_images = isset($instance['no_images']) ? esc_attr($instance['no_images']) : 9;
        ?>
        <p><label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:', 'thrive'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>"
                   type="text" value="<?php echo esc_attr($title); ?>"/></p>

        <p><label for="<?php echo esc_attr($this->get_field_id('no_images')); ?>"><?php _e('Number of images:', 'thrive'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('no_images')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('no_images')); ?>" type="text" value="<?php echo esc_attr($no_images); ?>"/></p>

        <script type="text/javascript">
            if (ThriveWidgetsOptions.controls_binded === 0) {
                ThriveWidgetsOptions.bind_handlers();
            }
        </script>

        <?php
    }

}