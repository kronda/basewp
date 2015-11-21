<?php

class Thrive_Appr_Nav extends WP_Widget
{

    public function __construct()
    {
        $widget_ops = array('classname' => 'widget_thrive_appr_nav', 'description' => __('Apprentice Navigation Widget', 'thrive'));
        parent::__construct('widget_thrive_appr_nav', __('Apprentice Navigation Widget', 'thrive'), $widget_ops);

        add_action('save_post', array(&$this, 'flush_widget_cache'));
        add_action('deleted_post', array(&$this, 'flush_widget_cache'));
        add_action('switch_theme', array(&$this, 'flush_widget_cache'));
    }

    function widget($args, $instance)
    {
        $courses_obj = _thrive_appr_get_category_object();
        $lessonsLevel = thrive_appr_get_lessons_level();
        ?>
        <section>
            <?php if ($instance['title']): ?>
                <p class="ttl"><?php echo $instance['title'] ?></p>
            <?php endif; ?>
            <div class="scn">
                <?php
                switch ($lessonsLevel):
                    case 1:
                        $this->_generate_markup_for_level_1($courses_obj['courses']);
                        break;
                    case 2:
                        $this->_generate_markup_for_level_2($courses_obj['courses']);
                        break;
                    case 3:
                        $this->_generate_markup_for_level_3($courses_obj['courses']);
                        break;
                endswitch;
                ?>
            </div>
        </section>
    <?php
    }

    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $this->flush_widget_cache();

        $alloptions = wp_cache_get('alloptions', 'options');
        if (isset($alloptions['widget_thrive_appr_nav']))
            delete_option('widget_thrive_appr_nav');

        return $instance;
    }

    function flush_widget_cache()
    {
        wp_cache_delete('widget_thrive_appr_nav', 'widget');
    }

    function form($instance)
    {
        $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
        ?>
        <p><label
                for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:', 'thrive'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text"
                   value="<?php echo esc_attr($title); ?>"/></p>


    <?php
    }

    function _generate_markup_for_level_3($courses)
    {
        $post_terms = wp_get_post_terms(get_the_ID(), 'apprentice', array("fields" => "ids"));
        ?>

        <?php foreach ($courses as $course): ?>
        <h6 class="amt"><a href='<?php echo get_term_link($course['slug'], "apprentice"); ?>'><?php echo $course['name']; ?></a></h6>
        <div class="apw">
            <?php foreach ($course['modules'] as $module): ?>
                <div class="apwl ap-c">
                    <a href="<?php echo get_term_link($module['slug'], "apprentice"); ?>" class="ali <?php if (count($module['lessonCats']) > 0): ?>opn<?php endif; ?>"><?php echo $module['name']; ?><span class="apw-b"></span></a>
                    <?php
                    foreach ($module['lessonCats'] as $cat):
                        $cat['posts'] = _thrive_get_ordered_lessons($cat['posts'], $cat['term_id']);
                        ?>
                        <div class="apw-i ap-c">
                            <a href="<?php echo get_term_link($cat['slug'], "apprentice"); ?>" class="opn ali <?php if (in_array($cat['term_id'], $post_terms)): ?>act<?php endif; ?>"><?php echo $cat['name']; ?><span class="apw-b"></span></a>

                            <div class="apws apw-i">
                                <?php
                                foreach ($cat['posts'] as $post):
                                    $post_item_class = $this->_get_post_item_class($post->ID);
                                    ?>
                                    <a href="<?php echo get_permalink($post->ID); ?>" class="ali <?php echo $post_item_class; ?>"><?php echo $post->post_title; ?><span class="apw-b"></span></a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>

    <?php
    }

    function _generate_markup_for_level_2($courses)
    {

        ?>

        <div class="apw">
            <?php
            foreach ($courses as $course):
                ?>
                <div class="apwl ap-c">
                    <a href="<?php echo get_term_link($course['slug'], "apprentice"); ?>" class="ali <?php if (count($course['modules']) > 0): ?>opn<?php endif; ?>"><?php echo $course['name']; ?><span class="apw-b"></span></a>
                    <?php
                    foreach ($course['modules'] as $module):
                        $module['posts'] = _thrive_get_ordered_lessons($module['posts'], $module['term_id']);
                        ?>
                        <div class="apw-i ap-c">
                            <a href="<?php echo get_term_link($module['slug'], "apprentice"); ?>" class="opn ali act"><?php echo $module['name']; ?><span class="apw-b"></span></a>

                            <div class="apws apw-i">
                                <?php
                                foreach ($module['posts'] as $post):
                                    $post_item_class = $this->_get_post_item_class($post->ID);
                                    ?>
                                    <a href="<?php echo get_permalink($post->ID); ?>" class="ali <?php echo $post_item_class; ?>"><?php echo $post->post_title; ?><span class="apw-b"></span></a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        </div>

    <?php
    }

    function _generate_markup_for_level_1($courses)
    {
        ?>
        <div class="apw olv">
        <?php
        foreach ($courses as $course):
            $course['posts'] = _thrive_get_ordered_lessons($course['posts'], $course['term_id']);
            ?>
            <div class="apwl ap-c">
                <a href="<?php echo get_term_link($course['slug'], "apprentice"); ?>" class="ali <?php if (count($course['posts']) > 0): ?>opn<?php endif; ?>"><?php echo $course['name']; ?><span class="apw-b"></span></a>

                <div class="apws apw-i">
                    <?php
                    foreach ($course['posts'] as $post):
                        $post_item_class = $this->_get_post_item_class($post->ID);
                        ?>
                        <a href="<?php echo get_permalink($post->ID); ?>" class="ali <?php echo $post_item_class; ?>"><?php echo $post->post_title; ?><span></span></a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php
    }

    function _get_post_item_class($post_id)
    {
        $post_item_progress = _thrive_appr_get_progress($post_id);
        if ($post_item_progress == THRIVE_APPR_PROGRESS_COMPLETED) {
            $post_item_class = "ald";
        } elseif ($post_item_progress == THRIVE_APPR_PROGRESS_STARTED) {
            $post_item_class = "alp";
        } else {
            $post_item_class = "alc";
        }

        if ($post_id == get_the_ID() && is_singular()) {
            $post_item_class = "spn"; //if current lesson
        }

        return $post_item_class;
    }

}