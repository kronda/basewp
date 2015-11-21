<?php
$after_posts_array = array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10);
?>
<label for="thrive_meta_focus_display_location"><?php echo __("Where should the focus area be displayed?", 'thrive'); ?></label>
<select name="thrive_meta_focus_display_location" id="thrive_meta_focus_display_location">
    <option value="top" <?php if ($value_focus_display_location == "top"): ?>selected<?php endif; ?>><?php echo __("Top Area", 'thrive'); ?></option>
    <option value="bottom" <?php if ($value_focus_display_location == "bottom"): ?>selected<?php endif; ?>><?php echo __("Below post", 'thrive'); ?></option>
    <!--<option value="between_posts" <?php if ($value_focus_display_location == "between_posts"): ?>selected<?php endif; ?>><?php echo __("On blog page between posts", 'thrive'); ?></option>-->
</select>
<div class="clear"></div>
<br/>
<div id="container_focus_display_general_options">
    <label for="thrive_meta_focus_display_post_type"><?php echo __("Which content should it be displayed on?", 'thrive'); ?></label><br/>
    <input class="thrive_meta_focus_display_post_type" type="radio" name="thrive_meta_focus_display_post_type" value="none" <?php if ($value_focus_display_post_type == "none" || $value_focus_display_post_type == ""): ?>checked<?php endif; ?> /> <?php _e("None", 'thrive'); ?>
    <input class="thrive_meta_focus_display_post_type" type="radio" name="thrive_meta_focus_display_post_type" value="post" <?php if ($value_focus_display_post_type == "post"): ?>checked<?php endif; ?> /> <?php _e("Posts", 'thrive'); ?>
    <input class="thrive_meta_focus_display_post_type" type="radio" name="thrive_meta_focus_display_post_type" value="page" <?php if ($value_focus_display_post_type == "page"): ?>checked<?php endif; ?> /> <?php _e("Pages", 'thrive'); ?>

    <div id="container_display_post_options" <?php if ($value_focus_display_post_type != "post"): ?>style="display:none;"<?php endif; ?>>
        <div class="clear"></div>
        <br/>
        <label for="thrive_meta_focus_display_is_default"> <?php echo __("Make this your default focus area?", 'thrive'); ?></label><br/>
        <input class="thrive_meta_focus_display_is_default" type="radio" name="thrive_meta_focus_display_is_default" value="0" <?php if ($value_focus_display_is_default == 0): ?>checked<?php endif; ?> /> <?php _e("No", 'thrive'); ?>
        <input class="thrive_meta_focus_display_is_default" type="radio" name="thrive_meta_focus_display_is_default" value="1" <?php if ($value_focus_display_is_default == 1): ?>checked<?php endif; ?> /> <?php _e("Yes", 'thrive'); ?>
        <div id="container_display_show_in_cats" <?php if ($value_focus_display_is_default == 1): ?>style="display:none;"<?php endif; ?>>
            <br/> <br/>
            <?php _e("Show in categories", 'thrive'); ?> <br/>
            <select id="thrive_meta_focus_display_sel_categories" name="thrive_meta_focus_display_cats" style="width: 220px;" multiple>
                <?php foreach ($categories_array as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php if (in_array($cat['id'], $value_focus_display_categories)): ?>selected<?php endif; ?>><?php echo $cat['name']; ?></option>
                <?php endforeach; ?>            
            </select>
            <input type="hidden" name="thrive_meta_focus_display_categories" id="thrive_meta_focus_hidplay_hidden_cats" value="" />
        </div>
    </div>

    <div id="container_display_page_options" <?php if ($value_focus_display_post_type != "page"): ?>style="display:none;"<?php endif; ?>>
        <div class="clear"></div>
        <br/>
        <input type="checkbox" name="thrive_meta_focus_page_blog" value="blog" <?php if ($value_focus_page_blog == "blog"):?>checked="checked"<?php endif;?> />
        <label><?php _e("Show on Blog Page", 'thrive');?></label> <br/>
        <input type="checkbox" name="thrive_meta_focus_page_archive" value="archive" <?php if ($value_focus_page_archive == "archive"):?>checked="checked"<?php endif;?> />
        <label><?php _e("Show on Archive Pages", 'thrive');?></label>

    </div>
</div>

<div id="container_display_show_between_posts" style="display: none;">
    <?php _e("Show after post number", 'thrive'); ?> <br/>
    <select id="thrive_meta_focus_display_between_posts" name="thrive_meta_focus_display_between_posts">
        <?php foreach ($after_posts_array as $no): ?>
            <option value="<?php echo $no; ?>" <?php if ($no == $value_focus_display_between_posts): ?>selected<?php endif; ?>><?php echo $no; ?></option>
        <?php endforeach; ?>
    </select>
</div>