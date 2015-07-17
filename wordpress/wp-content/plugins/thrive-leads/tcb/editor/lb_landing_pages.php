<?php

$landing_pages = !empty($_POST['landing_pages']) ? $_POST['landing_pages'] : array();
$tags = array();
foreach ($landing_pages as $code => $_item) {
    if (empty($_item['tags'])) {
        continue;
    }
    foreach ($_item['tags'] as $index => $tag) {
        $clean = strtolower(str_replace(' ', '-', $tag));
        $tags[$clean] = $tag;
        $landing_pages[$code]['tags_classes'][$index] = $clean;
    }
}
?>
<div class=" thrv_columns tve_clearfix">
    <div class="tve_colm tve_foc tve_df tve_ofo">
        <?php if (!empty($_POST['landing_page'])) : ?>
            <div class="tve_message tve_warning" id="tve_landing_page_msg">
                <p class="tve_message_title">Warning - your changes will be lost</p>

                <p class="tve_message_content">
                    If you change your landing page template without saving the current revision, you won't be able to
                    revert back to it later.
                </p>

                <div class="tve_center tve_lb_fields" style="text-align: center">
                    <input id="tve_landing_page_name" type="text" value="" placeholder="Template Name">
                    <a id="tve_landing_page_save" class="tve_click tve_editor_btn tve_btn_success"
                       href="javascript:void(0)"><span>Save Landing Page</span></a>
                </div>
            </div>
        <?php endif ?>
        <h5>Filter by Tag</h5>

        <div class="tve_landing_page_filters">
            <?php foreach ($tags as $value => $label) : ?>
                <label><input type="checkbox" class="tve_change tve_landing_page_tag"
                              value="<?php echo $value ?>"/> <?php echo $label ?></label>
            <?php endforeach ?>
        </div>
    </div>
    <div class="tve_colm tve_tfo tve_df tve_lst">
        <div class="tve_grid tve_landing_pages" id="tve_landing_page_selector">
            <div class="tve_right tve_lb_fields" style="padding: 5px 1px 0 0;"><input class="tve_keyup" data-ctrl="controls.filter_lp" type="text" style="width: 170px" placeholder="Quick filter..." value="" id="tve_landing_page_filter"></div>
            <div class="tve_scT tve_green">
                <ul class="tve_clearfix">
                    <li class="tve_tS tve_click"><span class="tve_scTC1">Default Landing Pages</span></li>
                    <li id="tve_saved_landing_pages" class="tve_click"><span
                            class="tve_scTC2">Your saved Landing Pages</span></li>
                </ul>
                <div class="tve_scTC tve_scTC1" style="display: block">

                    <div class="tve_clear" style="height: 5px;"></div>
                    <div class="tve_overflow_y" style="" id="tve_default_landing_pages">
                        <?php foreach ($landing_pages as $code => $data) : ?>
                            <span
                                class="<?php echo empty($data['tags_classes']) ? '' : implode(' ', $data['tags_classes']) ?> tve_grid_cell tve_landing_page_template tve_click<?php echo $_POST['landing_page'] == $code ? ' tve_cell_selected' : '' ?>"
                                title="Choose <?php echo $data['name'] ?>">
                <input type="hidden" class="lp_code" value="<?php echo $code ?>"/>
                <img src="<?php echo $data['thumbnail'] ?>" width="180" height="152"/>
                <span class="tve_cell_caption_holder"><span class="tve_cell_caption"><?php echo $data['name'] ?></span></span>
                <span class="tve_cell_check tve_icm tve-ic-checkmark"></span>
            </span>
                        <?php endforeach ?>
                    </div>
                    <div class="tve_clear" style="height: 5px;"></div>
                </div>
                <div class="tve_scTC tve_scTC2" style="display: none;">
                    <a href="javascript:void(0)" id="tve_landing_page_delete"
                       style="margin: 15px 0 0 0"
                       class="tve_click tve_editor_btn tve_btn_critical tve_right"><span>Delete Template</span></a>
                    <h5>Choose from your saved Landing Pages</h5>

                    <label><input type="checkbox" id="tve_landing_page_user_filter" class="tve_change" value="1"/> Show
                        only saved versions of the current template</label>

                    <div class="tve_clear" style="height: 15px;"></div>
                    <div class="tve_overflow_y" style="max-height: 380px" id="tve_user_landing_pages">
                        No saved Templates found.
                    </div>
                </div>
            </div>
            <div class="tve_clear" style="height: 15px;"></div>
            <div class="tve_landing_pages_actions">
                <div id="tve_landing_page_select" class="tve_click tve_btn_success tve_right">
                    <div class="tve_update">Load Landing Page</div>
                </div>
                <?php if (!empty($_POST['landing_page'])) : ?>
                    <div id="tve_landing_page_disable" class="tve_click tve_btn_default tve_right">
                        <div class="tve_preview">Revert to theme template</div>
                    </div>
                <?php endif ?>
            </div>
            <div class="tve_clear"></div>
        </div>
    </div>
</div>
<script data-cfasync="false" type="text/javascript">
    jQuery(function() {
        setTimeout(function () {
            jQuery('#tve_landing_page_filter').focus();
        }, 200);
    });
</script>