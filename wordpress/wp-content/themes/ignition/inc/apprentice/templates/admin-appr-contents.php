<table class="options_table">
    <tr>
        <td class="thrive_options_branding" colspan="2">
            <img src="<?php echo get_template_directory_uri(); ?>/inc/images/TT-logo-small.png"
                 class="thrive_admin_logo"/>
        </td>
    </tr>
</table>
<div class="tap">
    <div class="twc left">
        <div class="clear"></div>
        <ol class="thrive-sortable-list <?php echo _thrive_appr_get_list_class("course", $lessonsLevel); ?>">
            <?php foreach ($courses as $c): ?>
                <li class="course_item thrive-appr-tree">
                    <div class="app-content">
                        <div class="app-toggle">
                            <span class="app-toggle-dmy"></span>
                            <span class="item_label has-children">
                                <?php echo $c['name']; ?>
                            </span>
                        </div>
                        <span class="cat_delete">x</span>
                        <input type="hidden" class="thrive_hidden_term_id" value="<?php echo $c['term_id']; ?>"/>
                        <?php $modules = _thrive_get_ordered_cat_array_by_parent($c['term_id']); ?>
                    </div>
                    <ol>
                        <li class="tree-empty-el"></li>
                        <?php if ($modules && count($modules) > 0 && $lessonsLevel > 1): ?>
                            <?php foreach ($modules as $m): ?>
                                <li class="module_item thrive-appr-tree">
                                    <div class="app-content">
                                        <div class="app-toggle">
                                            <span class="app-toggle-dmy"></span>
                                            <input type="hidden" class="thrive_hidden_term_id" value="<?php echo $m['term_id']; ?>"/>
                                            <span class="item_label has-children-open">
                                                <?php echo $m['name']; ?>
                                            </span>
                                        </div>
                                        <span class="cat_delete">x</span>
                                    </div>
                                    <?php $lessonCats = _thrive_get_ordered_cat_array_by_parent($m['term_id']); ?>
                                    <ol>
                                        <li class="tree-empty-el"></li>
                                        <?php if ($lessonCats && count($lessonCats) > 0 && $lessonsLevel > 2): ?>
                                            <?php foreach ($lessonCats as $cat): ?>
                                                <li class="thrive-appr-tree">
                                                    <div class="app-content">
                                                        <div class="app-toggle">
                                                            <span class="app-toggle-dmy"></span>
                                                            <span class="cat_toggle"></span>
                                                            <span class="item_label"><?php echo $cat['name']; ?></span>
                                                        </div>
                                                        <span class="cat_delete">x</span>
                                                        <input type="hidden" class="thrive_hidden_term_id" value="<?php echo $cat['term_id']; ?>"/>
                                                    </div>
                                                    <?php
                                                    if ($lessonsLevel == 3):
                                                        $catTermId = $cat['term_id'];
                                                        require 'admin-lessons-list.php';
                                                    endif;
                                                    ?>
                                                </li>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </ol>
                                    <?php
                                    if ($lessonsLevel == 2):
                                        $catTermId = $m['term_id'];
                                        require 'admin-lessons-list.php';
                                    endif;
                                    ?>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ol>

                    <?php
                    if ($lessonsLevel == 1):
                        $catTermId = $c['term_id'];
                        require 'admin-lessons-list.php';
                    endif;
                    ?>
                </li>
            <?php endforeach; ?>
        </ol>

    </div>
    <div class="twc right">
        <div class="apbox">
            <h3><?php _e("Add a new category", 'thrive'); ?></h3>

            <form action="">
                <label for="cat_name"><?php _e("Name", 'thrive'); ?></label>
                <input type="text" id="thrive_appr_cat_name" autocomplete="off"/>

                <div class="clear"><br/></div>
                <label for="cat_slug"><?php _e("Slug", 'thrive'); ?></label>
                <input type="text" id="thrive_appr_cat_slug" autocomplete="off"/>

                <div class="clear"><br/></div>
                <label for="cat_description"><?php _e("Description", 'thrive'); ?></label>
                <textarea id="thrive_appr_txt_cat_description" autocomplete="off" placeholder="<?php _e("Description", 'thrive'); ?>"></textarea>

                <div class="clear"><br/></div>
                <input type="submit" value="<?php _e("Add New Category", 'thrive'); ?>" id="btn_thrive_appr_add_new_cat"/>

                <div class="clear"><br/></div>
            </form>
            <div class="apbox">
                <form action="">
                    <label for="thrive_appr_lessons_level"><?php _e("Lessons level", 'thrive'); ?></label>
                    <select id="thrive_appr_lessons_level">
                        <option value="1" <?php if ($lessonsLevel == 1): ?>selected<?php endif; ?>>1</option>
                        <option value="2" <?php if ($lessonsLevel == 2): ?>selected<?php endif; ?>>2</option>
                        <option value="3" <?php if ($lessonsLevel == 3): ?>selected<?php endif; ?>>3</option>
                    </select>
                    <input type="submit" value="<?php _e("Set", 'thrive'); ?>" id="btn_thrive_appr_set_lessons_level"/>

                    <div class="clear"><br/></div>
                </form>
            </div>
            <div class="cat-list">
                <p><?php _e("Lessons", 'thrive'); ?></p>
                <ul class="thrive_list_lessons">
                    <?php
                    foreach ($lessons as $lesson):
                        if (!in_array($lesson->ID, $excludeIds)):
                            ?>
                            <li class="lesson-no-cat">
                                <span class="lesson_item_label"><?php echo $lesson->post_title; ?></span>
                                <input type="hidden" class="thrive_hidden_term_id" value="<?php echo $lesson->ID; ?>"/>
                            </li>
                        <?php
                        endif;
                    endforeach;
                    ?>
                </ul>
            </div>
        </div>
        <br/><br/><br/>

    </div>
    <div class="clear"></div>
</div>
<div style="display: none">
    <ol id="lesson-leaf-accept-clone">
        <li class="lesson-leaf-empty-el"></li>
        <li class="content-here">
            <span></span>

            <div class="content-text"><?php _e("Drag and drop lesson here", 'thrive'); ?></div>
            <div class="clear"></div>
        </li>
    </ol>
</div>