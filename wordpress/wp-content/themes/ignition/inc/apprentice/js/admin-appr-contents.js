jQuery(document).ready(function () {

    ThriveApprOptions.bindHandlers();

});

ThriveApprOptions.bindHandlers = function () {
    jQuery("#btn_thrive_appr_add_new_cat").click(ThriveApprOptions.addNewCatHandler);
    jQuery("#btn_thrive_appr_set_lessons_level").click(ThriveApprOptions.setLessonsLevel);
    jQuery(".cat_delete").click(function () {
        if (confirm("Are you sure that you want to remove this category?")) {
            ThriveApprOptions.deleteCatHandler(jQuery(this).parents("li").find(".thrive_hidden_term_id").val());
            return false;
        }
        return false;
    });

    var lessons_level = parseInt(ThriveApprOptions.lessonsLevel),
        ensure_dropzones = function ($li, level) {
            var $dropzone = $li.find('> ol.lesson-leaf-accept');
            if (level == lessons_level && !$dropzone.length) {
                $li.append(jQuery('#lesson-leaf-accept-clone').clone().removeAttr('id').addClass('lesson-leaf-accept'));
                jQuery("ol.thrive-sortable-list").sortable('refresh');
            }
            level != lessons_level && $dropzone.length && $dropzone.remove();
        }

    jQuery("ol.thrive-sortable-list").sortable({
        isValidTarget: function ($item, container) {
            container.el.parents('li').removeClass('appr-collapsed');
            if ($item.hasClass('lesson-leaf')) {
                return container.el.hasClass('lesson-leaf-accept');
            } else if (container.el.hasClass('lesson-leaf-accept')) {
                return false;
            }
            var hl = $item.apprHasLessons();
            if (hl && $item.apprItemSize() + container.el.apprLevel() != lessons_level + 1) {
                return false;
            }
            if (!hl && $item.apprItemSize() + container.el.apprLevel() > lessons_level + 1) {
                return false;
            }
            return true;
        },
        onDrop: function ($item, container) {
            $item.removeClass("dragged").removeAttr("style");
            jQuery("body").removeClass("dragging");
            ThriveApprOptions.sendCategoryTree();
            /* make sure that every leaf node has lesson dropzone and every non-leaf node does not have any lesson dropzone */
            var level = $item.apprLevel(), _selector = '';
            ensure_dropzones($item, level);

            for (var i = 1, len = $item.apprItemSize(); i < len; i++) {
                _selector += '> ol > li.thrive-appr-tree ';
                level++;
                $item.find(_selector).each(function () {
                    ensure_dropzones(jQuery(this), level);
                });
            }
            ThriveApprOptions.notAssignedDrag();
        },
        itemSelector: 'li:not(li.content-here)'
    });

    ThriveApprOptions.notAssignedDrag();
    ThriveApprOptions.bindRemoveHandlers();
    ThriveApprOptions.toggleLessons();
};

ThriveApprOptions.toggleLessons = function () {
    jQuery('.app-toggle').on('click', function () {
        jQuery(this).parents('.thrive-appr-tree').first().toggleClass('appr-collapsed');
    });
};

ThriveApprOptions.notAssignedDrag = function () {
    jQuery(".lesson-no-cat").draggable({
        revert: "invalid",
        start: function () {
            jQuery('body').addClass('dragging');
            jQuery('.content-here').addClass('placeholder');
        },
        stop: function () {
            jQuery('body').removeClass('dragging');
            jQuery('.content-here').removeClass('placeholder');
        }
    });
    jQuery(".content-here").droppable({
        accept: ".lesson-no-cat",
        drop: function (event, ui) {
            var $itemToAdd = ui.draggable.clone();
            $itemToAdd.find('.lesson_item_label').append("<a href='javascript:void(0)' class='lesson-remove'>X</a>");
            $itemToAdd.attr("class", "lesson-leaf thrive-appr-tree");
            jQuery(this).before($itemToAdd);
            $itemToAdd.attr("style", "");
            ui.draggable.remove();
            ThriveApprOptions.sendCategoryTree();
            jQuery('body').removeClass('dragging');
            jQuery('.content-here').removeClass('placeholder');
        }
    });
}

ThriveApprOptions.bindRemoveHandlers = function () {
    jQuery('.thrive-sortable-list').on('click', ".lesson-remove", function () {
        var $itemToRemove = jQuery(this).parents(".lesson-leaf").clone();
        $itemToRemove.find(".lesson-remove").remove();
        $itemToRemove.attr("class", "lesson-no-cat");
        jQuery(".thrive_list_lessons").append($itemToRemove);
        jQuery(this).parents(".lesson-leaf").remove();
        ThriveApprOptions.sendCategoryTree();
        ThriveApprOptions.notAssignedDrag();
        return false;
    });
};

ThriveApprOptions.lessonsOrder = [];
ThriveApprOptions.catOrder = 0;
ThriveApprOptions.sendCategoryTree = function () {
    var tree = {},
        read_tree = function ($li) {
            var _item = {
                type: $li.hasClass('lesson-leaf') ? 'lesson' : 'cat'
            };

            if (_item.type == 'cat') {
                _item.children = {};
                $li.find('> ol > li.thrive-appr-tree').each(function () {
                    var $this = jQuery(this);
                    _item.children[$this.find('.thrive_hidden_term_id').first().val()] = read_tree($this);
                    _item.children[$this.find('.thrive_hidden_term_id').first().val()].order = $this.index();
                });
            }

            return _item;
        };

    jQuery("ol.thrive-sortable-list > li").each(function () {
        tree[jQuery(this).find(".thrive_hidden_term_id").first().val()] = read_tree(jQuery(this));
        tree[jQuery(this).find(".thrive_hidden_term_id").first().val()].order = jQuery(this).index();
    });
    //console.log(tree);

    var postData = {
        noonce: ThriveApprOptions.noonce,
        cat_tree: tree,
        unassigned_lessons: []
    };

    jQuery('li.lesson-no-cat > .thrive_hidden_term_id').each(function () {
        postData.unassigned_lessons.push(this.value);
    });

    jQuery.post(ThriveApprOptions.saveCatTreeUrl, postData, function (response) {
    });

    return false;
};

ThriveApprOptions.addNewCatHandler = function () {
    var _cat_name = jQuery("#thrive_appr_cat_name").val();
    var _cat_slug = jQuery("#thrive_appr_cat_slug").val();

    if (_cat_name === "") {
        alert("Please insert a title for the category");
        return false;
    }

    var postData = {
        noonce: ThriveApprOptions.noonce,
        cat_name: _cat_name,
        cat_slug: _cat_slug,
        cat_description: jQuery("#thrive_appr_txt_cat_description").val(),
        parent_id: 0
    };

    jQuery.post(ThriveApprOptions.addNewCatUrl, postData, function (response) {

        if (response === 0 || response === "0") {
            alert('Something went wrong. Please refresh and try again.');
            return false;
        }

        location.reload();

    });

    return false;
};

ThriveApprOptions.deleteCatHandler = function (_cat_id) {
    if (!_cat_id) {
        return false;
    }

    var postData = {
        noonce: ThriveApprOptions.noonce,
        cat_id: _cat_id
    };

    jQuery.post(ThriveApprOptions.deleteCatUrl, postData, function (response) {

        if (response === 0 || response === "0") {
            alert('Something went wrong. Please refresh and try again.');
            return false;
        }

        location.reload();

    });

};

ThriveApprOptions.setLessonsLevel = function () {
    var _lessonsLevel = jQuery("#thrive_appr_lessons_level").val();
    if (isNaN(_lessonsLevel)) {
        alert("Please insert a number!");
        return false;
    }
    var postData = {
        noonce: ThriveApprOptions.noonce,
        lessons_level: _lessonsLevel
    };

    jQuery.post(ThriveApprOptions.saveLessonsLevelUrl, postData, function (response) {
        if (response === 0 || response === "0") {
            alert('Something went wrong. Please refresh and try again.');
            return false;
        }
        location.reload();
    });
    return false;
};

jQuery.fn.apprHasLessons = function () {
    return (this.find('.lesson-leaf').length);
}
jQuery.fn.apprLevel = function (until) {
    if (typeof until === 'undefined') {
        until = '.thrive-sortable-list';
    }
    return this.parentsUntil(until, 'li').length + 1;
}
jQuery.fn.apprDeepestLevel = function (until) {
    if (typeof until === 'undefined') {
        until = '.thrive-sortable-list';
    }
    var level = this.apprLevel(until);
    this.find('li.thrive-appr-tree:not(.lesson-leaf)').each(function () {
        var cl = jQuery(this).apprLevel(until);
        if (cl > level) {
            level = cl;
        }
    });
    return level;
}
/**
 * how deep is a dragged item (how many levels of subcategories it has)
 */
jQuery.fn.apprItemSize = function () {
    return this.apprDeepestLevel(this.parent());
}