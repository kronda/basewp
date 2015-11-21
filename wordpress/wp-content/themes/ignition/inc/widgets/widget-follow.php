<?php

class Thrive_Follow_Widget extends WP_Widget
{
    public function __construct()
    {
        $widget_ops = array('classname' => 'widget_thrive_follow', 'description' => __('Invite visitors to follow you on social media.', 'thrive'));
        parent::__construct('widget_thrive_follow', __('Thrive Follow Me Widget', 'thrive'), $widget_ops);

        $this->alt_option_name = 'widget_thrive_follow';

        add_action('save_post', array(&$this, 'flush_widget_cache'));
        add_action('deleted_post', array(&$this, 'flush_widget_cache'));
        add_action('switch_theme', array(&$this, 'flush_widget_cache'));
    }

    function widget($args, $instance)
    {
        if (!isset($args['widget_id'])) {
            $args['widget_id'] = "widget-thrive" . rand(0, 999);
        }
        wp_enqueue_script('thrive-widgets-options');
        $items = $this->_build_items_array($instance);
        ?>

        <section class="widget follow_me" id="<?php echo $args['widget_id']; ?>">
            <div class="scn">
                <div class="awr side_body">
                    <?php if ($instance['title'] && $instance['title'] != ""): ?>
                        <p class="upp ttl"><?php echo $instance['title'] ?></p>
                    <?php endif; ?>
                    <ul>
                        <?php
                        foreach ($items as $key => $item):
                            ?>
                            <li class="sm_icons">
                                <?php if ($item['type'] != "dribble" && $item['type'] != "rss" && $item['type'] != 'xing'): ?>
                                    <div class="bubb">
                                        <div class="bubble">
                                            <?php $this->render_share_bubble($item['type'], $instance); ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <a <?php if ($item['type'] == "dribble" || $item['type'] == "rss" || $item['type'] == 'xing'): ?>href="<?php echo $item['url']; ?>"<?php endif; ?>
                                   class="sm <?php echo $item['a_class'] . " " . $item['col_class'] ?>" rel="<?php echo $item['type']; ?>">
                                    <span></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="clear"></div>
                </div>
            </div>

            <?php if ((isset($instance['gprofile_active']) && $instance['gprofile_active'] == 1) || (isset($instance['gpage_active']) && $instance['gpage_active'] == 1)): ?>
                <script>jQuery(window).load(function () {
                        ThriveApp.load_script("google");
                    });</script>
            <?php endif; ?>

        </section>
        <?php
    }

    function _build_items_array($instance)
    {
        $items = array();

        if (isset($instance['facebook_active']) && $instance['facebook_active'] == 1 && $instance['facebook_url'] && $instance['facebook_url'] != "") {
            $items[] = array('type' => 'facebook', 'url' => $instance['facebook_url'], 'a_class' => 'fb', 'col_class' => 'sm-1col', 'span_content' => "&#xf09a;");
        }
        if (isset($instance['twitter_active']) && $instance['twitter_active'] == 1 && $instance['twitter_url'] && $instance['twitter_url'] != "") {
            $items[] = array('type' => 'twitter', 'url' => _thrive_get_twitter_link($instance['twitter_url']), 'a_class' => 'twitter', 'col_class' => 'sm-1col', 'span_content' => "&#xf099;");
        }
        if (isset($instance['gprofile_active']) && $instance['gprofile_active'] == 1 && $instance['gprofile_url'] && $instance['gprofile_url'] != "") {
            $items[] = array('type' => 'gprofile', 'url' => $instance['gprofile_url'], 'a_class' => 'g_plus', 'col_class' => 'sm-1col', 'span_content' => "&#xf0d5;");
        }
        if (isset($instance['gpage_active']) && $instance['gpage_active'] == 1 && $instance['gpage_url'] && $instance['gpage_url'] != "") {
            $items[] = array('type' => 'gpage', 'url' => $instance['gpage_url'], 'a_class' => 'g_plus', 'col_class' => 'sm-1col', 'span_content' => "&#xf0d5;");
        }
        if (isset($instance['linkedin_active']) && $instance['linkedin_active'] == 1 && $instance['linkedin_url'] && $instance['linkedin_url'] != "") {
            $items[] = array('type' => 'linkedin', 'url' => $instance['linkedin_url'], 'a_class' => 'linkedin', 'col_class' => 'sm-1col', 'span_content' => "&#xf0e1;");
        }
        if (isset($instance['pinterest_active']) && $instance['pinterest_active'] == 1 && $instance['pinterest_url'] && $instance['pinterest_url'] != "") {
            $items[] = array('type' => 'pinterest', 'url' => $instance['pinterest_url'], 'a_class' => 'prinster', 'col_class' => 'sm-1col', 'span_content' => "&#xf0d2;");
        }
        if (isset($instance['dribble_active']) && $instance['dribble_active'] == 1 && $instance['dribble_url'] && $instance['dribble_url'] != "") {
            $items[] = array('type' => 'dribble', 'url' => $instance['dribble_url'], 'a_class' => 'dribble', 'col_class' => 'sm-1col', 'span_content' => "&#xf17d;");
        }
        if (isset($instance['rss_active']) && $instance['rss_active'] == 1 && $instance['rss_url'] && $instance['rss_url'] != "") {
            $items[] = array('type' => 'rss', 'url' => $instance['rss_url'], 'a_class' => 'rss', 'col_class' => 'sm-1col', 'span_content' => "&#xf09e;");
        }
        if (isset($instance['youtube_active']) && $instance['youtube_active'] == 1 && $instance['youtube_url'] && $instance['youtube_url'] != "") {
            $items[] = array('type' => 'youtube', 'url' => $instance['youtube_url'], 'a_class' => 'youtube', 'col_class' => 'sm-1col', 'span_content' => "&#xf16a;");
        }
        if (isset($instance['instagram_active']) && $instance['instagram_active'] == 1 && $instance['instagram_url'] && $instance['instagram_url'] != "") {
            $items[] = array('type' => 'instagram', 'url' => $instance['instagram_url'], 'a_class' => 'is', 'col_class' => 'sm-1col');
        }
        if (isset($instance['xing_active']) && $instance['xing_active'] == 1 && $instance['xing_url'] && $instance['xing_url'] != "") {
            $items[] = array('type' => 'xing', 'url' => $instance['xing_url'], 'a_class' => 'xi', 'col_class' => 'sm-1col');
        }
        $no_items = count($items);

        if ($no_items % 3 == 1) {
            $items[$no_items - 1]['col_class'] = 'sm-3col';
        }

        if ($no_items % 3 == 2) {
            $items[$no_items - 1]['col_class'] = 'sm-2col';
        }

        return $items;
    }

    function update($new_instance, $old_instance)
    {

        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['twitter_active'] = (int)$new_instance['twitter_active'];
        $instance['twitter_url'] = strip_tags($new_instance['twitter_url']);

        $instance['twitter_active'] = (int)$new_instance['twitter_active'];
        $instance['twitter_url'] = strip_tags($new_instance['twitter_url']);
        $instance['facebook_active'] = (int)$new_instance['facebook_active'];
        $instance['facebook_url'] = strip_tags($new_instance['facebook_url']);
        $instance['gprofile_active'] = (int)$new_instance['gprofile_active'];
        $instance['gprofile_url'] = strip_tags($new_instance['gprofile_url']);
        $instance['gpage_active'] = (int)$new_instance['gpage_active'];
        $instance['gpage_url'] = strip_tags($new_instance['gpage_url']);
        $instance['linkedin_active'] = (int)$new_instance['linkedin_active'];
        $instance['linkedin_url'] = strip_tags($new_instance['linkedin_url']);
        $instance['pinterest_active'] = (int)$new_instance['pinterest_active'];
        $instance['pinterest_url'] = strip_tags($new_instance['pinterest_url']);
        $instance['dribble_active'] = (int)$new_instance['dribble_active'];
        $instance['dribble_url'] = strip_tags($new_instance['dribble_url']);
        $instance['rss_active'] = (int)$new_instance['rss_active'];
        $instance['rss_url'] = strip_tags($new_instance['rss_url']);
        $instance['youtube_active'] = (int)$new_instance['youtube_active'];
        $instance['youtube_url'] = strip_tags($new_instance['youtube_url']);
        $instance['instagram_active'] = (int)$new_instance['instagram_active'];
        $instance['instagram_url'] = strip_tags($new_instance['instagram_url']);
        $instance['xing_active'] = (int)$new_instance['xing_active'];
        $instance['xing_url'] = strip_tags($new_instance['xing_url']);

        update_option('thrive_follow_widget_facebook', $instance['facebook_active']);

        $this->flush_widget_cache();

        $alloptions = wp_cache_get('alloptions', 'options');
        if (isset($alloptions['widget_thrive_follow']))
            delete_option('widget_thrive_follow');

        return $instance;
    }

    function flush_widget_cache()
    {
        wp_cache_delete('widget_thrive_follow', 'widget');
    }

    function form($instance)
    {
        $title = isset($instance['title']) ? esc_attr($instance['title']) : '';
        $twitter_active = isset($instance['twitter_active']) ? absint($instance['twitter_active']) : 0;
        $twitter_url = isset($instance['twitter_url']) ? esc_attr($instance['twitter_url']) : '';
        $facebook_active = isset($instance['facebook_active']) ? absint($instance['facebook_active']) : 0;
        $facebook_url = isset($instance['facebook_url']) ? esc_attr($instance['facebook_url']) : '';
        $gprofile_active = isset($instance['gprofile_active']) ? absint($instance['gprofile_active']) : 0;
        $gprofile_url = isset($instance['gprofile_url']) ? esc_attr($instance['gprofile_url']) : '';
        $gpage_active = isset($instance['gpage_active']) ? absint($instance['gpage_active']) : 0;
        $gpage_url = isset($instance['gpage_url']) ? esc_attr($instance['gpage_url']) : '';
        $linkedin_active = isset($instance['linkedin_active']) ? absint($instance['linkedin_active']) : 0;
        $linkedin_url = isset($instance['linkedin_url']) ? esc_attr($instance['linkedin_url']) : '';
        $dribble_active = isset($instance['dribble_active']) ? absint($instance['dribble_active']) : 0;
        $dribble_url = isset($instance['dribble_url']) ? esc_attr($instance['dribble_url']) : '';
        $pinterest_active = isset($instance['pinterest_active']) ? absint($instance['pinterest_active']) : 0;
        $pinterest_url = isset($instance['pinterest_url']) ? esc_attr($instance['pinterest_url']) : '';
        $rss_active = isset($instance['rss_active']) ? absint($instance['rss_active']) : 0;
        $rss_url = isset($instance['rss_url']) ? esc_attr($instance['rss_url']) : '';
        $youtube_active = isset($instance['youtube_active']) ? absint($instance['youtube_active']) : 0;
        $youtube_url = isset($instance['youtube_url']) ? esc_attr($instance['youtube_url']) : '';
        $instagram_active = isset($instance['instagram_active']) ? absint($instance['instagram_active']) : 0;
        $instagram_url = isset($instance['instagram_url']) ? esc_attr($instance['instagram_url']) : '';
        $xing_active = isset($instance['xing_active']) ? absint($instance['xing_active']) : 0;
        $xing_url = isset($instance['xing_url']) ? esc_attr($instance['xing_url']) : '';
        ?>
        <p><label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php _e('Title:', 'thrive'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>"
                   type="text" value="<?php echo esc_attr($title); ?>"/></p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('twitter_active')); ?>"><b><?php _e('Twitter', 'thrive'); ?></b></label> <br/>
            <?php _e('Inactive', 'thrive'); ?>
            <input type="radio" name="<?php echo esc_attr($this->get_field_name('twitter_active')); ?>"
                   id="<?php echo esc_attr($this->get_field_id('twitter_active')); ?>" <?php if ($twitter_active == 0): ?>checked<?php endif ?> value="0"/>
            <?php _e('Active', 'thrive'); ?>
            <input type="radio" name="<?php echo esc_attr($this->get_field_name('twitter_active')); ?>"
                   id="<?php echo esc_attr($this->get_field_id('twitter_active')); ?>" <?php if ($twitter_active == 1): ?>checked<?php endif ?> value="1"/>

        </p>
        <p><label for="<?php echo esc_attr($this->get_field_id('twitter_url')); ?>"><?php _e('Twitter username:', 'thrive'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('twitter_url')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('twitter_url')); ?>" type="text" value="<?php echo esc_attr($twitter_url); ?>"/></p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('facebook_active')); ?>"><b><?php _e('Facebook page', 'thrive'); ?></b></label> <br/>
            <?php _e('Inactive', 'thrive'); ?>
            <input type="radio" name="<?php echo esc_attr($this->get_field_name('facebook_active')); ?>"
                   id="<?php echo esc_attr($this->get_field_id('facebook_active')); ?>" <?php if ($facebook_active == 0): ?>checked<?php endif ?> value="0"/>
            <?php _e('Active', 'thrive'); ?>
            <input type="radio" name="<?php echo esc_attr($this->get_field_name('facebook_active')); ?>"
                   id="<?php echo esc_attr($this->get_field_id('facebook_active')); ?>" <?php if ($facebook_active == 1): ?>checked<?php endif ?> value="1"/>

        </p>
        <p><label for="<?php echo esc_attr($this->get_field_id('facebook_url')); ?>"><?php _e('Page url:', 'thrive'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('facebook_url')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('facebook_url')); ?>" type="text" value="<?php echo esc_attr($facebook_url); ?>"/></p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('gprofile_active')); ?>"><b><?php _e('Google+ profile', 'thrive'); ?></b></label> <br/>
            <?php _e('Inactive', 'thrive'); ?>
            <input type="radio" name="<?php echo esc_attr($this->get_field_name('gprofile_active')); ?>"
                   id="<?php echo esc_attr($this->get_field_id('gprofile_active')); ?>" <?php if ($gprofile_active == 0): ?>checked<?php endif ?> value="0"/>
            <?php _e('Active', 'thrive'); ?>
            <input type="radio" name="<?php echo esc_attr($this->get_field_name('gprofile_active')); ?>"
                   id="<?php echo esc_attr($this->get_field_id('gprofile_active')); ?>" <?php if ($gprofile_active == 1): ?>checked<?php endif ?> value="1"/>

        </p>
        <p><label for="<?php echo esc_attr($this->get_field_id('gprofile_url')); ?>"><?php _e('Profile url', 'thrive'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('gprofile_url')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('gprofile_url')); ?>" type="text" value="<?php echo esc_attr($gprofile_url); ?>"/></p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('gpage_active')); ?>"><b><?php _e('Google+ Page', 'thrive'); ?></b></label> <br/>
            <?php _e('Inactive', 'thrive'); ?>
            <input type="radio" name="<?php echo esc_attr($this->get_field_name('gpage_active')); ?>"
                   id="<?php echo esc_attr($this->get_field_id('gpage_active')); ?>" <?php if ($gpage_active == 0): ?>checked<?php endif ?> value="0"/>
            <?php _e('Active', 'thrive'); ?>
            <input type="radio" name="<?php echo esc_attr($this->get_field_name('gpage_active')); ?>"
                   id="<?php echo esc_attr($this->get_field_id('gpage_active')); ?>" <?php if ($gpage_active == 1): ?>checked<?php endif ?> value="1"/>

        </p>
        <p><label for="<?php echo esc_attr($this->get_field_id('gpage_url')); ?>"><?php _e('Page url:', 'thrive'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('gpage_url')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('gpage_url')); ?>" type="text" value="<?php echo esc_attr($gpage_url); ?>"/></p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('linkedin_active')); ?>"><b><?php _e('LinkedIn', 'thrive'); ?></b></label> <br/>
            <?php _e('Inactive', 'thrive'); ?>
            <input type="radio" name="<?php echo esc_attr($this->get_field_name('linkedin_active')); ?>"
                   id="<?php echo esc_attr($this->get_field_id('linkedin_active')); ?>" <?php if ($linkedin_active == 0): ?>checked<?php endif ?> value="0"/>
            <?php _e('Active', 'thrive'); ?>
            <input type="radio" name="<?php echo esc_attr($this->get_field_name('linkedin_active')); ?>"
                   id="<?php echo esc_attr($this->get_field_id('linkedin_active')); ?>" <?php if ($linkedin_active == 1): ?>checked<?php endif ?> value="1"/>

        </p>
        <p><label for="<?php echo esc_attr($this->get_field_id('linkedin_url')); ?>"><?php _e('Company or profile URL', 'thrive'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('linkedin_url')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('linkedin_url')); ?>" type="text" value="<?php echo esc_attr($linkedin_url); ?>"/></p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('dribble_active')); ?>"><b><?php _e('Dribble', 'thrive'); ?></b></label> <br/>
            <?php _e('Inactive', 'thrive'); ?>
            <input type="radio" name="<?php echo esc_attr($this->get_field_name('dribble_active')); ?>"
                   id="<?php echo esc_attr($this->get_field_id('dribble_active')); ?>" <?php if ($dribble_active == 0): ?>checked<?php endif ?> value="0"/>
            <?php _e('Active', 'thrive'); ?>
            <input type="radio" name="<?php echo esc_attr($this->get_field_name('dribble_active')); ?>"
                   id="<?php echo esc_attr($this->get_field_id('dribble_active')); ?>" <?php if ($dribble_active == 1): ?>checked<?php endif ?> value="1"/>

        </p>
        <p><label for="<?php echo esc_attr($this->get_field_id('dribble_url')); ?>"><?php _e('Dribble username:', 'thrive'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('dribble_url')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('dribble_url')); ?>" type="text" value="<?php echo esc_attr($dribble_url); ?>"/></p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('pinterest_active')); ?>"><b><?php _e('Pinterest', 'thrive'); ?></b></label> <br/>
            <?php _e('Inactive', 'thrive'); ?>
            <input type="radio" name="<?php echo esc_attr($this->get_field_name('pinterest_active')); ?>"
                   id="<?php echo esc_attr($this->get_field_id('pinterest_active')); ?>" <?php if ($pinterest_active == 0): ?>checked<?php endif ?> value="0"/>
            <?php _e('Active', 'thrive'); ?>
            <input type="radio" name="<?php echo esc_attr($this->get_field_name('pinterest_active')); ?>"
                   id="<?php echo esc_attr($this->get_field_id('pinterest_active')); ?>" <?php if ($pinterest_active == 1): ?>checked<?php endif ?> value="1"/>

        </p>
        <p><label for="<?php echo esc_attr($this->get_field_id('pinterest_url')); ?>"><?php _e('Pinterest url:', 'thrive'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('pinterest_url')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('pinterest_url')); ?>" type="text" value="<?php echo esc_attr($pinterest_url); ?>"/></p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('rss_active')); ?>"><b><?php _e('RSS', 'thrive'); ?></b></label> <br/>
            <?php _e('Inactive', 'thrive'); ?>
            <input type="radio" name="<?php echo esc_attr($this->get_field_name('rss_active')); ?>"
                   id="<?php echo esc_attr($this->get_field_id('rss_active')); ?>" <?php if ($rss_active == 0): ?>checked<?php endif ?> value="0"/>
            <?php _e('Active', 'thrive'); ?>
            <input type="radio" name="<?php echo esc_attr($this->get_field_name('rss_active')); ?>"
                   id="<?php echo esc_attr($this->get_field_id('rss_active')); ?>" <?php if ($rss_active == 1): ?>checked<?php endif ?> value="1"/>

        </p>
        <p><label for="<?php echo esc_attr($this->get_field_id('rss_url')); ?>"><?php _e('RSS Feed URL', 'thrive'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('rss_url')); ?>" name="<?php echo esc_attr($this->get_field_name('rss_url')); ?>"
                   type="text" value="<?php echo esc_attr($rss_url); ?>"/></p>

        <p>
            <label for="<?php echo esc_attr($this->get_field_id('youtube_active')); ?>"><b><?php _e('Youtube', 'thrive'); ?></b></label> <br/>
            <?php _e('Inactive', 'thrive'); ?>
            <input type="radio" name="<?php echo esc_attr($this->get_field_name('youtube_active')); ?>"
                   id="<?php echo esc_attr($this->get_field_id('youtube_active')); ?>" <?php if ($youtube_active == 0): ?>checked<?php endif ?> value="0"/>
            <?php _e('Active', 'thrive'); ?>
            <input type="radio" name="<?php echo esc_attr($this->get_field_name('youtube_active')); ?>"
                   id="<?php echo esc_attr($this->get_field_id('youtube_active')); ?>" <?php if ($youtube_active == 1): ?>checked<?php endif ?> value="1"/>

        </p>
        <p><label for="<?php echo esc_attr($this->get_field_id('youtube_url')); ?>"><?php _e('Youtube username:', 'thrive'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('youtube_url')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('youtube_url')); ?>" type="text" value="<?php echo esc_attr($youtube_url); ?>"/></p>


        <p>
            <label
                for="<?php echo esc_attr($this->get_field_id('instagram_active')); ?>"><b><?php _e('Instagram', 'thrive'); ?></b></label>
            <br/>
            <?php _e('Inactive', 'thrive'); ?>
            <input type="radio" name="<?php echo esc_attr($this->get_field_name('instagram_active')); ?>"
                   id="<?php echo esc_attr($this->get_field_id('instagram_active')); ?>"
                   <?php if ($instagram_active == 0): ?>checked<?php endif ?> value="0"/>
            <?php _e('Active', 'thrive'); ?>
            <input type="radio" name="<?php echo esc_attr($this->get_field_name('instagram_active')); ?>"
                   id="<?php echo esc_attr($this->get_field_id('instagram_active')); ?>"
                   <?php if ($instagram_active == 1): ?>checked<?php endif ?> value="1"/>

        </p>
        <p><label
                for="<?php echo esc_attr($this->get_field_id('instagram_url')); ?>"><?php _e('Instagram profile:', 'thrive'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('instagram_url')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('instagram_url')); ?>" type="text"
                   value="<?php echo esc_attr($instagram_url); ?>"/></p>

        <p>
            <label
                for="<?php echo esc_attr($this->get_field_id('xing_active')); ?>"><b><?php _e('Xing', 'thrive'); ?></b></label>
            <br/>
            <?php _e('Inactive', 'thrive'); ?>
            <input type="radio" name="<?php echo esc_attr($this->get_field_name('xing_active')); ?>"
                   id="<?php echo esc_attr($this->get_field_id('xing_active')); ?>"
                   <?php if ($xing_active == 0): ?>checked<?php endif ?> value="0"/>
            <?php _e('Active', 'thrive'); ?>
            <input type="radio" name="<?php echo esc_attr($this->get_field_name('xing_active')); ?>"
                   id="<?php echo esc_attr($this->get_field_id('xing_active')); ?>"
                   <?php if ($xing_active == 1): ?>checked<?php endif ?> value="1"/>

        </p>
        <p><label
                for="<?php echo esc_attr($this->get_field_id('xing_url')); ?>"><?php _e('Xing Url:', 'thrive'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('xing_url')); ?>"
                   name="<?php echo esc_attr($this->get_field_name('xing_url')); ?>" type="text"
                   value="<?php echo esc_attr($xing_url); ?>"/></p>
        <?php
    }

    function render_share_bubble($type, $instance)
    {
        switch ($type) {
            case 'facebook':
                if (isset($instance['facebook_active']) && $instance['facebook_active'] == 1) {
                    $fbUrl = urlencode(_thrive_get_social_link($instance['facebook_url'], 'facebook'));
                    $randId = rand(9, 9999);
                    ?>
                    <div id="container-follow-facebook<?php echo $randId; ?>">
                        <script type="text/javascript">
                            jQuery(document).ready(function () {
                                jQuery("#container-follow-facebook<?php echo $randId; ?>").append("<iframe style='height:70px !important;' src='//www.facebook.com/plugins/likebox.php?href=<?php echo $fbUrl; ?>&width=292&height=32&colorscheme=light&show_faces=false&header=false&stream=false&show_border=false' id='follow_me_content_fb'></iframe>");
                            });
                        </script>
                    </div>
                    <?php
                }
                break;
            case 'gprofile':
                if (isset($instance['gprofile_active']) && $instance['gprofile_active'] == 1) {
                    $gProfileUrl = _thrive_get_social_link($instance['gprofile_url'], 'google');
                    ?>
                    <div id="container-follow-gprofile">
                        <div class="g-person" data-width="273" data-href="<?php echo $gProfileUrl; ?>" data-layout="landscape" data-rel="author"
                             id="follow_me_content_gprofile"></div>
                    </div>
                    <?php
                }
                break;
            case 'gpage':
                if (isset($instance['gpage_active']) && $instance['gpage_active'] == 1) {
                    $gPageUrl = _thrive_get_social_link($instance['gpage_url'], 'google');
                    ?>
                    <div id="container-follow-gpage">
                        <div class='g-follow' data-width="273" data-href="<?php echo $gPageUrl; ?>" data-layout="landscape" data-rel="author"
                             id="follow_me_content_gprofile"></div>
                    </div>

                    <?php
                }
                break;
            case 'twitter':
                if (isset($instance['twitter_active']) && $instance['twitter_active'] == 1) {
                    $twitterUsername = $instance['twitter_url'];
                    ?>
                    <div id="container-follow-twitter">
                        <a href="<?php echo _thrive_get_social_link($instance['twitter_url'], 'twitter'); ?>" class="twitter-follow-button"
                           data-show-count="false">Follow @<?php echo $twitterUsername; ?></a>
                    </div>
                    <script>jQuery(window).load(function () {
                            ThriveApp.load_script("twitter");
                        });</script>
                    <?php
                }
                break;
            case 'linkedin':
                if (isset($instance['linkedin_active']) && $instance['linkedin_active'] == 1) {
                    $linkedinId = _thrive_get_social_link($instance['linkedin_url'], 'linkedin');
                    ?>
                    <div id="container-follow-linkedin">
                        <script>
                            jQuery(window).load(function () {
                                ThriveApp.load_script("linkedin");
                            });
                        </script>
                        <?php echo _thrive_get_linkedin_follow_script($linkedinId); ?>
                    </div>
                    <?php
                }
                break;
            case 'pinterest':
                if (isset($instance['pinterest_active']) && $instance['pinterest_active'] == 1) {
                    $pinterest_user = _thrive_get_social_link($instance['pinterest_url'], 'pinterest');
                    ?>
                    <div id="container-follow-pinterest">
                        <a data-pin-do="buttonFollow"
                           href="//www.pinterest.com/<?php echo $instance['pinterest_url'] ?>/"><?php echo $pinterest_user; ?></a>
                        <script>
                            jQuery(window).load(function () {
                                ThriveApp.load_script("pinterest");
                            });
                        </script>
                    </div>
                    <?php
                }
                break;
            case 'dribble':
                if (isset($instance['dribble_active']) && $instance['dribble_active'] == 1) {
                    ?>
                    <div id="container-follow-dribble">
                        <a href="<?php echo $instance['dribble_url']; ?>" target="_blank">Dribble</a>
                    </div>
                    <?php
                }
                break;
            case 'rss':
                if (isset($instance['rss_active']) && $instance['rss_active'] == 1) {
                    ?>
                    <div id="container-follow-rss">
                        <a href="<?php echo $instance['rss_url']; ?>" target="_blank">RSS</a>
                    </div>
                    <?php
                }
                break;
            case 'youtube':
                if (isset($instance['youtube_active']) && $instance['youtube_active'] == 1) {
                    ?>
                    <div id="container-follow-youtube">
                        <div class="g-ytsubscribe" data-channelid="<?php echo _thrive_get_social_link($instance['youtube_url'], 'youtube'); ?>"
                             data-channel="<?php echo _thrive_get_social_link($instance['youtube_url'], 'youtube'); ?>" data-layout="full"></div>
                        <script>
                            jQuery(window).load(function () {
                                ThriveApp.load_script("youtube");
                            });
                        </script>
                    </div>
                    <?php
                }
                break;
            case 'instagram':
                if (isset($instance['instagram_active']) && $instance['instagram_active'] == 1) {
                    ?>
                    <div id="container-follow-instagram">
                        <style>.ig-b- {
                                display: inline-block;
                            }

                            .ig-b- img {
                                visibility: hidden;
                            }

                            .ig-b-:hover {
                                background-position: 0 -60px;
                            }

                            .ig-b-:active {
                                background-position: 0 -120px;
                            }

                            .ig-b-v-24 {
                                width: 137px;
                                height: 24px;
                                background: url(//badges.instagram.com/static/images/ig-badge-view-sprite-24.png) no-repeat 0 0;
                            }

                            @media only screen and (-webkit-min-device-pixel-ratio: 2), only screen and (min--moz-device-pixel-ratio: 2), only screen and (-o-min-device-pixel-ratio: 2 / 1), only screen and (min-device-pixel-ratio: 2), only screen and (min-resolution: 192dpi), only screen and (min-resolution: 2dppx) {
                                .ig-b-v-24 {
                                    background-image: url(//badges.instagram.com/static/images/ig-badge-view-sprite-24@2x.png);
                                    background-size: 160px 178px;
                                }
                            }</style>
                        <a href="//instagram.com/<?php echo $instance['instagram_url']; ?>?ref=badge" class="ig-b- ig-b-v-24"><img
                                src="//badges.instagram.com/static/images/ig-badge-view-24.png" alt="Instagram"/></a>
                    </div>
                    <?php
                }
                break;
            case 'xing':
                if (isset($instance['xing_active']) && $instance['xing_active'] == 1) {
                    ?>
                    <div id="container-follow-xing">
                        <a href="<?php echo $instance['xing_url']; ?>" target="_blank"></a>
                    </div>
                    <?php
                }
                break;
        }
    }

}
