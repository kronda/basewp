<ol class="lesson-leaf-accept">
    <?php
    $queryPostsArgs['posts_per_page'] = -1;
    $queryPostsArgs['tax_query'][0]['terms'] = $catTermId;
    $queryLessonCatsPosts = new WP_Query($queryPostsArgs);
    $posts = _thrive_get_ordered_lessons($queryLessonCatsPosts->get_posts(), $catTermId); ?>
    <li class="lesson-leaf-empty-el"></li>
    <?php foreach ($posts as $p): ?>
        <li class="lesson-leaf thrive-appr-tree">
            <div class="app-content">
                <span class="lesson_item_label"><?php echo $p->post_title; ?><a href="javascript:void(0)" class="lesson-remove">X</a></span>
                <input type="hidden" class="thrive_hidden_term_id" value="<?php echo $p->ID; ?>" />
            </div>
        </li>
        <?php
        $excludeIds[] = $p->ID;
    endforeach;
    ?>
    <li class="content-here">
        <span></span>
        <div class="content-text"><?php _e("Drag and drop lesson here", 'thrive'); ?></div>
        <div class="clear"></div>
    </li>
</ol>