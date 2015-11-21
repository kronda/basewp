<?php

class Thrive_Tabs_Widget extends WP_Widget
{
    public function __construct()
    {
        $widget_ops = array('classname' => 'widget_thrive_tabs', 'description' => __('Up to 3 tabs with lists of posts or pages.', 'thrive'));
        parent::__construct('widget_thrive_tabs', __('Thrive Tabs Widget', 'thrive'), $widget_ops);
        $this->alt_option_name = 'widget_thrive_tabs';

        add_action('save_post', array(&$this, 'flush_widget_cache'));
        add_action('deleted_post', array(&$this, 'flush_widget_cache'));
        add_action('switch_theme', array(&$this, 'flush_widget_cache'));
        add_action('wp_footer', array(&$this, '_js'));
    }

    function widget($args, $instance)
    {
        if (!isset($args['widget_id'])) {
            $args['widget_id'] = "widget-thrive" .  rand(0, 999);
        }
        //by default the display of the post date should be on
        $instance['display_post_date'] = (isset($instance['display_post_date'])) ? $instance['display_post_date'] : "on";
        $instance['display_no_comments'] = (isset($instance['display_no_comments'])) ? $instance['display_no_comments'] : "on";

        $tabs_contents = $this->_build_tab_widget_contents($instance);
        $txt_class = ($instance['thumbnail_size'] == "on") ? "" : " noImageTab";
        ?>
        <section class="widget tabs_widget" id="<?php echo $args['widget_id'];?>">

            <div class="scn">
                <div class="awr">
                    <?php if ($instance['title']): ?>
                        <p class="ttl"><?php echo $instance['title'] ?></p>
                    <?php endif; ?>
                    <div class="scT">
                        <ul class="tabs clearfix">
                            <?php $tab_count = count($tabs_contents['labels']); ?>
                            <?php foreach ($tabs_contents['labels'] as $key => $label): ?>
                                <li <?php if ($key == 0): ?>class="tS"<?php endif; ?>
                                    style="width: <?php echo 100 / $tab_count; ?>%">
                                    <a href=""><?php echo $label; ?></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <?php
                        $index = 0;
                        foreach ($tabs_contents['contents'] as $key => $contents):
                            ?>
                            <div class="scTC scTC<?php echo $index; ?>"
                                 <?php if ($index == 0): ?>style="display: block"<?php endif; ?>>
                                <?php foreach ($contents as $c): ?>
                                    <?php if ($instance['thumbnail_size'] && $instance['thumbnail_size'] == "on"): ?>
                                        <?php if ($c['image']): ?>
                                            <div class="tim left">
                                                <a href="<?php echo $c['url']; ?>">
                                                    <div class="wti"
                                                         style="background-image: url('<?php echo $c['image']; ?>')"></div>
                                                </a>
                                            </div>
                                        <?php else: ?>
                                            <div class="tim left default_tab_image">
                                                <a href="<?php echo $c['url']; ?>">
                                                    <div class="wti"
                                                         style="background-image: url('<?php echo get_template_directory_uri(); ?>/images/tabs_default_image.jpg')"></div>
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <div class="txt left <?php echo $txt_class; ?>">
                                        <p><a href="<?php echo $c['url']; ?>"><?php echo $c['title']; ?></a></p>
                                        <i>
                                            <?php if (($key == "popular" || $key == "trending") && isset($c['no_comments']) && $instance['display_no_comments'] != "off"): ?>
                                                <?php echo $c['no_comments'] . " " . __("comments", 'thrive'); ?>
                                            <?php elseif ($instance['display_post_date'] != "off"): ?>
                                                <?php echo date(get_option('date_format'), strtotime($c['date'])); ?>
                                            <?php endif; ?>
                                        </i>
                                    </div>
                                    <div class="clear">&nbsp;</div>
                                <?php endforeach; ?>
                            </div>
                            <?php
                            $index++;
                        endforeach;
                        ?>
                    </div>
                </div>
            </div>
        </section>
    <?php
    }

    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);

        $instance['no_items_show'] = (int)$new_instance['no_items_show'];
        $instance['thumbnail_size'] = isset($new_instance['thumbnail_size']) ? $new_instance['thumbnail_size'] : "";
        $instance['display_post_date'] = isset($new_instance['display_post_date']) ? $new_instance['display_post_date'] : "off";
        $instance['display_no_comments'] = isset($new_instance['display_no_comments']) ? $new_instance['display_no_comments'] : "off";
        //$instance['first_tab'] = $new_instance['first_tab'];
        $instance['trending_time'] = (int)$new_instance['trending_time'];
        $instance['alltime_label'] = strip_tags($new_instance['alltime_label']);
        $instance['trending_label'] = strip_tags($new_instance['trending_label']);
        $instance['recent_label'] = strip_tags($new_instance['recent_label']);
        $instance['category_label'] = strip_tags($new_instance['category_label']);
        $instance['custom_label'] = strip_tags($new_instance['custom_label']);
        $instance['category'] = (int)$new_instance['category'];
        $instance['menu'] = (int)$new_instance['menu'];
        $instance['show_tabs_trending'] = isset($new_instance['show_tabs_trending']) ? $new_instance['show_tabs_trending'] : "";
        $instance['show_tabs_popular'] = isset($new_instance['show_tabs_popular']) ? $new_instance['show_tabs_popular'] : "";
        $instance['show_tabs_recent'] = isset($new_instance['show_tabs_recent']) ? $new_instance['show_tabs_recent'] : "";
        $instance['show_tabs_category'] = isset($new_instance['show_tabs_category']) ? $new_instance['show_tabs_category'] : "";
        $instance['show_tabs_menu'] = isset($new_instance['show_tabs_menu']) ? $new_instance['show_tabs_menu'] : "";

        $this->flush_widget_cache();

        $alloptions = wp_cache_get('alloptions', 'options');
        if (isset($alloptions['widget_thrive_tabs']))
            delete_option('widget_thrive_tabs');

        return $instance;
    }

    function flush_widget_cache()
    {
        wp_cache_delete('widget_thrive_tabs', 'widget');
    }

    function form($instance)
    {
        $instance = wp_parse_args((array)$instance, array('display_post_date' => 'on',
            'display_no_comments' => 'on'
        ));
        $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
        $trending_label = isset($instance['trending_label']) ? esc_attr($instance['trending_label']) : '';
        $alltime_label = isset($instance['alltime_label']) ? esc_attr($instance['alltime_label']) : '';
        $recent_label = isset($instance['recent_label']) ? esc_attr($instance['recent_label']) : '';
        $category_label = isset($instance['category_label']) ? esc_attr($instance['category_label']) : '';
        $custom_label = isset($instance['custom_label']) ? esc_attr($instance['custom_label']) : '';
        $no_items_show = isset($instance['no_items_show']) ? absint($instance['no_items_show']) : 0;
        $thumbnail_size = isset($instance['thumbnail_size']) ? ($instance['thumbnail_size']) : "";
        $trending_time = isset($instance['trending_time']) ? absint($instance['trending_time']) : 0;
        $display_post_date = isset($instance['display_post_date']) ? ($instance['display_post_date']) : "off";
        $display_no_comments = isset($instance['display_no_comments']) ? ($instance['display_no_comments']) : "off";

        $show_tabs_trending = isset($instance['show_tabs_trending']) ? ($instance['show_tabs_trending']) : "";
        $show_tabs_popular = isset($instance['show_tabs_popular']) ? ($instance['show_tabs_popular']) : "";
        $show_tabs_recent = isset($instance['show_tabs_recent']) ? ($instance['show_tabs_recent']) : "";
        $show_tabs_category = isset($instance['show_tabs_category']) ? ($instance['show_tabs_category']) : "";
        $show_tabs_menu = isset($instance['show_tabs_menu']) ? ($instance['show_tabs_menu']) : "";

        $category = isset($instance['category']) ? absint($instance['category']) : 0;

        $mymenu = isset($instance['menu']) ? absint($instance['menu']) : 0;

        $first_tab = isset($instance['first_tab']) ? $instance['first_tab'] : "";

        $all_menus = get_terms('nav_menu', array('hide_empty' => true));
        $menus_array = array();
        foreach ($all_menus as $menu) {
            array_push($menus_array, array('id' => $menu->term_id, 'name' => $menu->name));
        }

        $all_categories = get_categories();
        $categories_array = array();
        foreach ($all_categories as $cat) {
            array_push($categories_array, array('id' => $cat->cat_ID, 'name' => $cat->cat_name));
        }
        ?>
        <p><label
                for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:', 'thrive'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text"
                   value="<?php echo esc_attr($title); ?>"/></p>

        <p><label
                for="<?php echo esc_attr($this->get_field_id('no_items_show')); ?>"><?php _e('Number of items to show:', 'thrive'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('no_items_show')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('no_items_show')); ?>" type="text"
                   value="<?php echo esc_attr($no_items_show); ?>"/></p>

        <p>
            <input type="checkbox" name="<?php echo esc_attr($this->get_field_name('thumbnail_size')); ?>"
                   <?php if ($thumbnail_size == "on"): ?>checked="checked"<?php endif; ?>
                   class="thrive_chk_show_thumbnail"/> <?php _e('Show thumbnails', 'thrive'); ?>
        </p>

        <p>
            <input type="checkbox" name="<?php echo esc_attr($this->get_field_name('display_post_date')); ?>"
                   <?php if ($display_post_date != "off"): ?>checked="checked"<?php endif; ?>
                   class="thrive_chk_display_post_date" value="on"/> <?php _e('Display Post Date', 'thrive'); ?>
        </p>

        <p>
            <input type="checkbox" name="<?php echo esc_attr($this->get_field_name('display_no_comments')); ?>"
                   <?php if ($display_no_comments != "off"): ?>checked="checked"<?php endif; ?>
                   class="thrive_chk_display_no_comments"
                   value="on"/> <?php _e('Display Number of Comments', 'thrive'); ?>
        </p>

        <p>
            <label for=""><?php _e('Show tabs', 'thrive'); ?></label><br/>
            <input type="checkbox" name="<?php echo esc_attr($this->get_field_name('show_tabs_trending')); ?>"
                   <?php if ($show_tabs_trending == "on"): ?>checked="checked"<?php endif; ?>
                   class="thrive_chk_show_tabs thrive_chk_show_trending"/> <?php _e('Trending Posts (Recently Popular)', 'thrive'); ?>
            <br/>
            <input type="checkbox" name="<?php echo esc_attr($this->get_field_name('show_tabs_popular')); ?>"
                   <?php if ($show_tabs_popular == "on"): ?>checked<?php endif; ?>
                   class="thrive_chk_show_tabs thrive_chk_show_popular"/> <?php _e('All-Time Popular Posts', 'thrive'); ?>
            <br/>
            <input type="checkbox" name="<?php echo esc_attr($this->get_field_name('show_tabs_recent')); ?>"
                   <?php if ($show_tabs_recent == "on"): ?>checked<?php endif; ?>
                   class="thrive_chk_show_tabs thrive_chk_show_recent"/> <?php _e('Recent Posts', 'thrive'); ?>
            <br/>
            <input type="checkbox" name="<?php echo esc_attr($this->get_field_name('show_tabs_category')); ?>"
                   <?php if ($show_tabs_category == "on"): ?>checked<?php endif; ?>
                   class="thrive_chk_show_tabs thrive_chk_show_category"/> <?php _e('Custom Category', 'thrive'); ?>
            <br/>
            <input type="checkbox" name="<?php echo esc_attr($this->get_field_name('show_tabs_menu')); ?>"
                   <?php if ($show_tabs_menu == "on"): ?>checked<?php endif; ?>
                   class="thrive_chk_show_tabs thrive_chk_show_menu"/> <?php _e('Custom Menu', 'thrive'); ?>

        </p>
        <div class="thrive_tabs_widget_container_options_trending" style="display: none;">
            <p><label
                    for="<?php echo esc_attr($this->get_field_id('trending_label')); ?>"><?php _e('Trending label:', 'thrive'); ?></label>
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('trending_label')); ?>"
                       name="<?php echo esc_attr($this->get_field_name('trending_label')); ?>" type="text"
                       value="<?php echo esc_attr($trending_label); ?>"/></p>

            <p><label
                    for="<?php echo esc_attr($this->get_field_id('trending_time')); ?>"><?php _e('Trending Time-Frame (in days):', 'thrive'); ?></label>
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('trending_time')); ?>"
                       name="<?php echo esc_attr($this->get_field_name('trending_time')); ?>" type="text"
                       value="<?php echo esc_attr($trending_time); ?>"/></p>
        </div>
        <div class="thrive_tabs_widget_container_options_alltime" style="display: none;">
            <p><label
                    for="<?php echo esc_attr($this->get_field_id('alltime_label')); ?>"><?php _e('All-time Popular label:', 'thrive'); ?></label>
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('alltime_label')); ?>"
                       name="<?php echo esc_attr($this->get_field_name('alltime_label')); ?>" type="text"
                       value="<?php echo esc_attr($alltime_label); ?>"/></p>
        </div>
        <div class="thrive_tabs_widget_container_options_recent" style="display: none;">
            <p><label
                    for="<?php echo esc_attr($this->get_field_id('recent_label')); ?>"><?php _e('Recent Posts label:', 'thrive'); ?></label>
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('recent_label')); ?>"
                       name="<?php echo esc_attr($this->get_field_name('recent_label')); ?>" type="text"
                       value="<?php echo esc_attr($recent_label); ?>"/></p>
        </div>
        <div class="thrive_tabs_widget_container_options_category" style="display: none;">
            <p><label
                    for="<?php echo esc_attr($this->get_field_id('category_label')); ?>"><?php _e('Custom category label:', 'thrive'); ?></label>
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('category_label')); ?>"
                       name="<?php echo esc_attr($this->get_field_name('category_label')); ?>" type="text"
                       value="<?php echo esc_attr($category_label); ?>"/></p>

            <p>
                <label
                    for="<?php echo esc_attr($this->get_field_id('category')); ?>"><?php _e('Category to show', 'thrive'); ?></label>
                <select name="<?php echo esc_attr($this->get_field_name('category')); ?>">
                    <option value="0"></option>
                    <?php foreach ($categories_array as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"
                                <?php if ($category == $cat['id']): ?>selected<?php endif; ?>><?php echo $cat['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </p>
        </div>
        <div class="thrive_tabs_widget_container_options_custom" style="display: none;">
            <p><label
                    for="<?php echo esc_attr($this->get_field_id('custom_label')); ?>"><?php _e('Custom menu label:', 'thrive'); ?></label>
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id('custom_label')); ?>"
                       name="<?php echo esc_attr($this->get_field_name('custom_label')); ?>" type="text"
                       value="<?php echo esc_attr($custom_label); ?>"/></p>

            <p>
                <label
                    for="<?php echo esc_attr($this->get_field_id('menu')); ?>"><?php _e('Menu to show', 'thrive'); ?></label>
                <select name="<?php echo esc_attr($this->get_field_name('menu')); ?>">
                    <option value="0"></option>
                    <?php foreach ($menus_array as $m): ?>
                        <option value="<?php echo $m['id']; ?>"
                                <?php if ($mymenu == $m['id']): ?>selected='selected'<?php endif; ?>><?php echo $m['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </p>
        </div>
        <script type="text/javascript">
            if (ThriveWidgetsOptions.controls_binded === 0) {
                ThriveWidgetsOptions.bind_handlers();
            }
        </script>
    <?php
    }

    function _build_tab_widget_contents($instance)
    {

        $show_tabs_trending = isset($instance['show_tabs_trending']) ? ($instance['show_tabs_trending']) : "";
        $show_tabs_popular = isset($instance['show_tabs_popular']) ? ($instance['show_tabs_popular']) : "";
        $show_tabs_recent = isset($instance['show_tabs_recent']) ? ($instance['show_tabs_recent']) : "";
        $show_tabs_category = isset($instance['show_tabs_category']) ? ($instance['show_tabs_category']) : "";
        $show_tabs_menu = isset($instance['show_tabs_menu']) ? ($instance['show_tabs_menu']) : "";

        $labels = array();
        $contents = array();
        $limit_content = isset($instance['no_items_show']) ? absint($instance['no_items_show']) : 5;

        if ($show_tabs_trending == "on") {
            $labels[] = isset($instance['trending_label']) ? ($instance['trending_label']) : __('Trending', 'thrive');

            $date_params = $this->_get_trending_date($instance['trending_time']);

            $r = new WP_Query(array(
                'date_query' => array(
                    array(
                        'after' => $date_params,
                    ),
                ),
                'order' => 'DESC',
                'orderby' => 'comment_count',
                'posts_per_page' => $limit_content,
                'ignore_sticky_posts' => 1
            ));

            $trending_posts = $r->get_posts();
            $items_array = array();
            foreach ($trending_posts as $p) {
                array_push($items_array, array('title' => $p->post_title, 'url' => get_permalink($p->ID), 'date' => get_the_time('Y-m-d', $p->ID), 'image' => $this->_get_featured_image($p->ID), 'no_comments' => get_comments_number($p->ID)));
            }
            $items_array = array_slice($items_array, 0, $limit_content);
            $contents['trending'] = $items_array;
        }

        if ($show_tabs_popular == "on") {
            $labels[] = isset($instance['alltime_label']) ? ($instance['alltime_label']) : __('Popular', 'thrive');

            $r = new WP_Query(array(
                'order' => 'DESC',
                'orderby' => 'comment_count',
                'posts_per_page' => $limit_content,
                'ignore_sticky_posts' => 1
            ));

            $popular_posts = $r->get_posts();
            $items_array = array();
            foreach ($popular_posts as $p) {
                array_push($items_array, array('title' => $p->post_title, 'url' => get_permalink($p->ID), 'date' => get_the_time('Y-m-d', $p->ID), 'image' => $this->_get_featured_image($p->ID), 'no_comments' => get_comments_number($p->ID)));
            }
            $items_array = array_slice($items_array, 0, $limit_content);
            $contents['popular'] = $items_array;
        }

        if ($show_tabs_recent == "on") {
            $labels[] = isset($instance['recent_label']) ? ($instance['recent_label']) : __('Recent', 'thrive');
            $r = new WP_Query(array(
                'orderby' => 'date',
                'posts_per_page' => $limit_content,
                'ignore_sticky_posts' => 1
            ));

            $recent_posts = $r->get_posts();
            $items_array = array();
            foreach ($recent_posts as $p) {
                array_push($items_array, array('title' => $p->post_title, 'url' => get_permalink($p->ID), 'date' => get_the_time('Y-m-d', $p->ID), 'image' => $this->_get_featured_image($p->ID)));
            }
            $items_array = array_slice($items_array, 0, $limit_content);
            $contents['recent'] = $items_array;
        }

        if ($show_tabs_category == "on" && $instance['category'] > 0) {
            $labels[] = isset($instance['category_label']) ? ($instance['category_label']) : __('Category', 'thrive');
            $cat_posts = get_posts(array('category' => $instance['category']));
            $items_array = array();
            foreach ($cat_posts as $p) {
                array_push($items_array, array('title' => $p->post_title, 'url' => get_permalink($p->ID), 'date' => get_the_time('Y-m-d', $p->ID), 'image' => $this->_get_featured_image($p->ID)));
            }
            $items_array = array_slice($items_array, 0, $limit_content);
            $contents['category'] = $items_array;
        }

        if ($show_tabs_menu == "on" && $instance['menu'] > 0) {
            $labels[] = isset($instance['custom_label']) ? ($instance['custom_label']) : __('Menu', 'thrive');
            $menu_items = wp_get_nav_menu_items($instance['menu']);
            $items_array = array();
            foreach ((array)$menu_items as $key => $menu_item) {
                array_push($items_array, array('title' => $menu_item->title . "-" . $menu_item->db_id, 'url' => $menu_item->url, 'date' => get_the_time('Y-m-d', $menu_item->object_id), 'image' => $this->_get_featured_image($menu_item->object_id)));
            }
            $items_array = array_slice($items_array, 0, $limit_content);
            $contents['menu'] = $items_array;
        }

        return array('labels' => $labels, 'contents' => $contents);
    }

    function _get_featured_image($postId) {

        $featured_image_data = thrive_get_post_featured_image($postId, "tt_post_icon");
        $featured_image = $featured_image_data['image_src'];
        return $featured_image;
    }

    // only load javascript when tabs widget is loaded.
    function _js()
    {
        if (is_active_widget(false, false, $this->id_base, true)) {
            if (wp_script_is('jquery', 'done')) {
                ?>
                <script type="text/javascript">
                    jQuery(".tabs_widget .scT ul li").on('click', function (e) {
                        var $li = jQuery(this),
                            tabs_wrapper = $li.parents(".tabs_widget").first(),
                            target_tab = tabs_wrapper.find(".scTC").eq($li.index());
                        tabs_wrapper.find(".tS").removeClass("tS");
                        $li.addClass('tS');
                        tabs_wrapper.find(".scTC").hide();
                        target_tab.show();
                        e.preventDefault();
                    });
                </script>
            <?php
            }
        }
    }

    function _get_trending_date($no_days)
    {
        $format = 'Y-m-d';

        $current_date = date($format);

        $new_date = date($format, strtotime('-' . $no_days . ' day' . $current_date));

        return $new_date;
        //return array
        $myDate = DateTime::createFromFormat("Y-m-d", $new_date);

        return array('Y' => $myDate->format("Y"),
            'm' => $myDate->format("m"),
            'd' => $myDate->format("d"));
    }

}

