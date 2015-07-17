<?php

/**
 * Wrapper for get_post_types. Just to apply some logic if needed
 * @return array
 */
function get_all_post_types()
{
    $types = array();

    $banned_types = array(
        'revision',
        'nav_menu_item',
        'project',
        'et_pb_layout',
        'tcb_lightbox',
        'focus_area',
        'thrive_optin',
        'thrive_ad_group',
        'thrive_ad',
        'thrive_slideshow',
        'thrive_slide_item',
        'tve_lead_shortcode',
        'tve_lead_2s_lightbox',
        'tve_form_type',
        'tve_lead_group',
    );
    foreach (get_post_types() as $type) {
        if (!in_array($type, $banned_types)) {
            $types[] = $type;
        }
    }

    return $types;
}

function display_layouts()
{
    $layouts = array(
        'featured_image' => 'Featured image',
        'title' => 'Title',
        'text' => 'Text',
    );

    if (isset($_POST['layout']) && !empty($_POST['layout'])) {
        foreach ($_POST['layout'] as $id) {
            ?>
        <li data-layout="<?php echo $id ?>" class="ui-state-default"><span
                class="ui-icon ui-icon-arrowthick-2-n-s"></span><?php echo $layouts[$id] ?></li><?php
        }
        return;
    }

    foreach ($layouts as $id => $label) {
        ?>
    <li data-layout="<?php echo $id ?>" class="ui-state-default"><span
            class="ui-icon ui-icon-arrowthick-2-n-s"></span><?php echo $label ?></li><?php
    }

}


?>
<div class="tve_post_grid_tabs_container">
    <input type="hidden" name="tve_lb_type" value="tve_post_grid">

    <div class="tve_scT tve_green">
        <ul class="tve_clearfix">
            <li class="tve_tS tve_click"><span class="tve_scTC1">Layout</span></li>
            <li class="tve_click"><span class="tve_scTC2">Edit Query</span></li>
            <li class="tve_click"><span class="tve_scTC3">Filter Settings</span></li>
            <li class="tve_click"><span class="tve_scTC4">Display Settings</span></li>
        </ul>

        <div class="tve_scTC tve_scTC1 tve_clearfix" style="display: block">
            <div class="tve_options_wrapper tve_clearfix">
                <div class="tve_option_container tve_clearfix">
                    <label class="lblOption">Teaser Layout:</label>

                    <div class="tve_fields_container">
                        <label><input
                                type="checkbox" <?php echo isset($_POST['teaser_layout']) ? isset($_POST['teaser_layout']['featured_image']) && $_POST['teaser_layout']['featured_image'] === 'true' ? 'checked="checked"' : '' : 'checked="checked"' ?>
                                name="teaser_layout[featured_image]"/>Featured image</label>
                        <label><input
                                type="checkbox" <?php echo isset($_POST['teaser_layout']) ? isset($_POST['teaser_layout']['title']) && $_POST['teaser_layout']['title'] === 'true' ? 'checked="checked"' : '' : 'checked="checked"' ?>
                                name="teaser_layout[title]"/>Title</label>
                        <label><input
                                type="checkbox" <?php echo isset($_POST['teaser_layout']) ? isset($_POST['teaser_layout']['text']) && $_POST['teaser_layout']['text'] === 'true' ? 'checked="checked"' : '' : 'checked="checked"' ?>
                                name="teaser_layout[text]"/>Text</label>
                        <label><input
                                type="checkbox" <?php echo isset($_POST['teaser_layout']) ? isset($_POST['teaser_layout']['read_more']) && $_POST['teaser_layout']['read_more'] === 'true' ? 'checked="checked"' : '' : 'checked="checked"' ?>
                                name="teaser_layout[read_more]"/>Read more link</label>
                    </div>
                </div>

                <div class="tve_option_container tve_clearfix">
                    <label class="lblOption">Text type:</label>

                    <div class="tve_fields_container">
                        <select name="text_type">
                            <option
                                value="summary" <?php echo isset($_POST['text_type']) && $_POST['text_type'] === 'summary' ? 'selected="selected"' : '' ?>>
                                Summary
                            </option>
                            <option
                                value="excerpt" <?php echo isset($_POST['text_type']) && $_POST['text_type'] === 'excerpt' ? 'selected="selected"' : '' ?>>
                                Excerpt
                            </option>
                            <option
                                value="fulltext" <?php echo isset($_POST['text_type']) && $_POST['text_type'] === 'fulltext' ? 'selected="selected"' : '' ?>>
                                Full text
                            </option>
                        </select>
                    </div>
                </div>

                <div class="tve_option_container tve_clearfix">
                    <label class="lblOption">Layout:</label>

                    <div class="tve_fields_container">
                        <p style="margin-bottom: 25px"><?php echo __("Drag the items into the correct order for display:")?></p>

                        <ul class="tve_sortable_layout">
                            <?php display_layouts() ?>
                        </ul>
                    </div>
                </div>

                <div class="tve_option_container tve_clearfix">
                    <label class="lblOption"><?php echo __("Grid layout:")?></label>

                    <div class="tve_fields_container">
                        <select name="grid_layout">
                            <option value="horizontal" <?php echo empty($_POST['grid_layout']) || $_POST['grid_layout'] === 'horizontal' ? 'selected="selected"' : '' ?>><?php echo __('Horizontal')?></option>
                            <option value="vertical" <?php echo !empty($_POST['grid_layout']) && $_POST['grid_layout'] === 'vertical' ? 'selected="selected"' : '' ?>><?php echo __('Vertical')?></option>
                        </select>
                        <p><?php echo __("For vertical grids the images will always be displayed on the left part of posts."); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="tve_scTC tve_scTC2 tve_clearfix">

            <div class="tve_options_wrapper tve_clearfix">

                <div class="tve_option_container tve_clearfix">
                    <label class="lblOption">Content to include</label>

                    <div class="tve_fields_container">
                        <?php foreach (get_all_post_types() as $type) : ?>
                            <label><input
                                    type="checkbox" <?php echo isset($_POST['post_types'][$type]) ? $_POST['post_types'][$type] === 'true' ? 'checked="checked"' : '' : $type === 'post' ? 'checked="checked"' : '' ?>
                                    name="post_types[<?php echo $type ?>]"/><?php echo ucfirst($type) ?></label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="tve_option_container tve_clearfix">
                    <label class="lblOption">Number of posts</label>

                    <div class="tve_fields_container">
                        <select name="posts_per_page">
                            <option value="0">All</option>
                            <?php for ($i = 1; $i <= 20; $i++) : ?>
                                <option <?php echo isset($_POST['posts_per_page']) ? $_POST['posts_per_page'] == $i ? 'selected="selected"' : '' :
                                    $i === 6 ? 'selected="selected"' : '' ?>><?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <label class="lblOption">Start</label>

                    <div class="tve_fields_container">
                        <select name="posts_start">
                            <?php for ($i = 0; $i <= 20; $i++) : ?>
                                <option <?php echo isset($_POST['posts_start']) ? $_POST['posts_start'] == $i ? 'selected="selected"' : '' :
                                    $i === 0 ? 'selected="selected"' : '' ?>><?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <label class="lblOption">Order by</label>

                    <div class="tve_fields_container">
                        <select name="orderby">
                            <option
                                value="date" <?php echo isset($_POST['orderby']) && $_POST['orderby'] === 'date' ? 'selected="selected"' : '' ?>>
                                Date
                            </option>
                            <option
                                value="title" <?php echo isset($_POST['orderby']) && $_POST['orderby'] === 'title' ? 'selected="selected"' : '' ?>>
                                Title
                            </option>
                            <option
                                value="author" <?php echo isset($_POST['orderby']) && $_POST['orderby'] === 'author' ? 'selected="selected"' : '' ?>>
                                Author
                            </option>
                            <option
                                value="comment_count" <?php echo isset($_POST['orderby']) && $_POST['orderby'] === 'comment_count' ? 'selected="selected"' : '' ?>>
                                Number of Comments
                            </option>
                            <option
                                value="rand" <?php echo isset($_POST['orderby']) && $_POST['orderby'] === 'rand' ? 'selected="selected"' : '' ?>>
                                Random
                            </option>
                        </select>
                    </div>

                    <label class="lblOption">Order</label>

                    <div class="tve_fields_container">
                        <select name="order">
                            <option
                                value="DESC" <?php echo isset($_POST['order']) && $_POST['order'] === 'DESC' ? 'selected="selected"' : '' ?>>
                                Descending
                            </option>
                            <option
                                value="ASC" <?php echo isset($_POST['order']) && $_POST['order'] === 'ASC' ? 'selected="selected"' : '' ?>>
                                Ascending
                            </option>
                        </select>
                    </div>

                    <label class="lblOption"><?php echo __('Show Items More Recent Than') ?></label>

                    <div class="tve_fields_container">
                        <input maxlength="3" value="<?php echo !empty($_POST['recent_days']) ? intval($_POST['recent_days']) : 0; ?>" name="recent_days" type="text" style="margin-bottom: 2px;" /> <?php echo __('Days') ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="tve_scTC tve_scTC3 tve_clearfix">
            <div class="tve_options_wrapper tve_clearfix">
                <div class="tve_option_container tve_clearfix">
                    <p>Choose which content to include</p>
                </div>

                <div class="tve_option_container tve_clearfix">
                    <label class="lblOption">Categories</label>
                    <div class="tve_fields_container ui-front">
                        <input name="filters[category]" value="<?php echo @$_POST['filters']['category'] ?>" type="text" class="tve_post_grid_autocomplete" data-action="tve_categories_list"/>
                    </div>

                    <label class="lblOption">Tags</label>
                    <div class="tve_fields_container ui-front">
                        <input name="filters[tag]" value="<?php echo @$_POST['filters']['tag'] ?>" type="text" class="tve_post_grid_autocomplete" data-action="tve_tags_list"/>
                    </div>

                    <label class="lblOption">Custom Taxonomies</label>
                    <div class="tve_fields_container ui-front">
                        <input name="filters[tax]" value="<?php echo @$_POST['filters']['tax'] ?>" type="text" class="tve_post_grid_autocomplete" data-action="tve_custom_taxonomies_list"/>
                    </div>

                    <label class="lblOption">Author</label>
                    <div class="tve_fields_container ui-front">
                        <input name="filters[author]" value="<?php echo @$_POST['filters']['author'] ?>" type="text" class="tve_post_grid_autocomplete" data-action="tve_authors_list"/>
                    </div>

                    <label class="lblOption">Individual Posts/Pages</label>
                    <div class="tve_fields_container ui-front">
                        <input name="filters[posts]" value="<?php echo @$_POST['filters']['posts'] ?>" type="text" class="tve_post_grid_autocomplete" data-action="tve_posts_list"/>
                    </div>
                </div>
            </div>
        </div>

        <div class="tve_scTC tve_scTC4 tve_clearfix">
            <div class="tve_options_wrapper tve_clearfix">
                <div class="tve_option_container tve_clearfix">
                    <label class="lblOption">Number of Columns</label>
                    <div class="tve_fields_container">
                        <select name="columns">
                            <?php for ($i = 1; $i <= 6; $i++) : ?>
                                <option <?php echo isset($_POST['columns']) ? $_POST['columns'] == $i ? 'selected="selected"' : '' :
                                    $i === 3 ? 'selected="selected"' : '' ?> ><?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <label class="lblOption">Order</label>
                    <div class="tve_fields_container">
                        <select name="display">
                            <option
                                value="grid" <?php echo isset($_POST['display']) && $_POST['display'] === 'grid' ? 'selected="selected"' : '' ?>>
                                Grid
                            </option>
                            <option
                                value="masonry" <?php echo isset($_POST['display']) && $_POST['display'] === 'masonry' ? 'selected="selected"' : '' ?>>
                                Masonry
                            </option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
