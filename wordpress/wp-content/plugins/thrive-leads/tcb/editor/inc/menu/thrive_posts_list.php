<span class="tve_options_headline"><span class="tve_icm tve-ic-move"></span>Thrive Posts List options</span>
<ul class="tve_menu">
    <li class="tve_ed_btn_text tve_firstOnRow">
        <label class="tve_text">
            Number of posts <input type="text" class="tve_change" id="posts_list_no_posts" size="2" />
        </label>
        &nbsp;
        <label class="tve_text">
            Show
            <select class="tve_change" id="posts_list_filter">
                <option value="recent">Recent posts</option>
                <option value="popular">Popular posts</option>
            </select>
        </label>
        &nbsp;
        <label class="tve_text">
            Category
            <select class="tve_change" id="posts_list_category">
                <?php foreach ($posts_categories as $id => $name) : ?>
                    <option value="<?php echo $id ?>"><?php echo $name ?></option>
                <?php endforeach ?>
            </select>
        </label>
    </li>
    <li class="tve_ed_btn_text">
        <label>
            Display thumbnails <input type="checkbox" class="tve_change" id="posts_list_thumbnails" />
        </label>
    </li>
    <?php include dirname(__FILE__) . '/_margin.php' ?>
</ul>