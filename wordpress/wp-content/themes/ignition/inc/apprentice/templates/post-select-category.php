<div>
    <?php _e("Assign a category from the list bellow or manage your contents ", 'thrive'); ?>
    <a href="admin.php?page=thrive_admin_page_appr_contents"><?php _e("here"); ?></a>
    <br/>
    <select name="thrive_appr_lesson_cat" id="tt-sel-appr-lesson-cat">
        <option value="0"></option>
        <?php foreach ($lessonsLevelCats as $cat): ?>
            <option
                value="<?php echo $cat['term_id']; ?>" <?php if ($currentCat && $currentCat->term_id == $cat['term_id']): ?> selected<?php endif; ?>>
                <?php echo $cat['name']; ?>
            </option>
        <?php endforeach; ?>
    </select>


    <br/>

    <input type="hidden" id="tt-hidden-lessons-level" value="<?php echo $lessonsLevel; ?>"/> <br/>
    <input type="text" id="tt-txt-add-lesson-cat"/> <br/>

    <?php if ($lessonsLevel > 1 && count($firstParentCats) > 0): ?>

        <select id="tt-sel-appr-parent-lesson-cat" style="margin-top: 3px;">
            <option value="0"><?php _e("Select a parent category"); ?></option>
            <?php foreach ($firstParentCats as $cat): ?>
                <option value="<?php echo $cat['term_id']; ?>">
                    <?php echo $cat['name']; ?>
                </option>
            <?php endforeach; ?>
        </select> <br/>
    <?php endif; ?>

    <a id="tt-link-add-lesson-cat" href="#"><?php _e("+ Add a new category", 'thrive'); ?></a>
    <label style="display: none;" id="tt-label-cat-added">&nbsp;&nbsp;*<?php _e("Category added", 'thrive'); ?></label>

</div>