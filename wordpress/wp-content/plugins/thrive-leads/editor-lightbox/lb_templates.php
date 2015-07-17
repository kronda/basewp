<?php
/**
 * show a list of available templates to use for this Form Type Design
 *
 * post_id and variation key will come from $_POST
 *
 */
$post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
$variation_key = isset($_POST['_key']) ? intval($_POST['_key']) : 0;
if (empty($post_id) || empty($variation_key)) {
    exit(__('Invalid data', 'thrive-leads'));
}
$variation = tve_leads_get_form_variation($post_id, $variation_key);
if (empty($variation)) {
    exit(__('Invalid data', 'thrive-leads'));
}
$form_type = tve_leads_get_form_type_from_variation($variation);
$current_template = !empty($variation[TVE_LEADS_FIELD_TEMPLATE]) ? $variation[TVE_LEADS_FIELD_TEMPLATE] : '';
$templates = Thrive_Leads_Template_Manager::for_form_type($form_type, false);
$parent_form_type = tve_leads_get_form_type_from_variation($variation, true);
$multi_step = Thrive_Leads_Template_Manager::for_multi_step($parent_form_type);

$form_type_name = tve_leads_get_form_type_name($variation['post_parent']);
?>
<p style="margin: -15px 0 10px; text-align: center"><?php echo sprintf(__('Choose the %s template you would like to use for this form', 'thrive-leads'), $form_type_name) ?></p>
<div class="tve_tl_tpl <?php if ($current_template) echo 'thrv_columns ' ?>tve_clearfix" id="tve-leads-tpl">
    <?php if ($current_template) : /* display the "Save" button just if there is some content in the form */ ?>
        <div class="tve_colm tve_foc tve_df tve_ofo">
            <div class="tve_message tve_warning" id="tve_landing_page_msg">
                <p class="tve_message_title"><?php echo __('Warning - your changes will be lost', 'thrive-leads') ?></p>

                <p class="tve_message_content">
                    <?php echo __("If you change your the template without saving the current revision, you won't be able to revert back to it later.", 'thrive-leads') ?>
                </p>

                <div class="tve_center tve_lb_fields" style="text-align: center">
                    <input id="tve_landing_page_name" type="text" value="" placeholder="<?php echo __('Template Name', 'thrive-leads') ?>">
                    <a data-ctrl="function:ext.tve_leads.template.save" class="tve_click tve_editor_btn tve_btn_success"
                       href="javascript:void(0)"><span><?php echo __('Save As Template', 'thrive-leads') ?></span></a>
                </div>
            </div>
        </div>
    <?php endif ?>
    <div class="<?php if ($current_template) : ?>tve_colm tve_tfo tve_df tve_lst<?php endif ?>">
        <div class="tve_grid tve_landing_pages" id="tve_landing_page_selector">
            <div class="tve_right tve_lb_fields" style="padding: 5px 1px 0 0;">
                <input class="tve_keyup" data-ctrl="controls.filter_lp"
                       type="text" style="width: 170px" placeholder="<?php echo __('Quick filter...', 'thrive-leads') ?>" value="" id="tve_landing_page_filter"></div>
            <div class="tve_scT tve_green">
                <ul class="tve_clearfix">
                    <li class="tve_tS tve_click"><span class="tve_scTC1"><?php echo __('Opt In Templates', 'thrive-leads') ?></span></li>
                    <li data-ctrl-mousedown="function:ext.tve_leads.template.user_tab_clicked" class="tve_click tve_mousedown"><span class="tve_scTC2"><?php echo sprintf(__('Your Saved %s Templates', 'thrive-leads'), $form_type_name) ?></span></li>
                    <?php if (!empty($multi_step)) : ?>
                        <li class="tve_click"><span class="tve_scTC3"><?php echo __('Multi-step Templates', 'thrive-leads') ?></span></li>
                    <?php endif ?>
                </ul>
                <div class="tve_scTC tve_scTC1" style="display: block">
                    <div class="tve_clear" style="height: 5px;"></div>
                    <div class="tve_overflow_y " style="">
                        <?php foreach ($templates as $data) : ?>
                            <span
                                class="tve-tpl-<?php echo $form_type ?> tve_grid_cell tve_landing_page_template tve_click<?php echo $current_template == $data['key'] ? ' tve_cell_selected' : '' ?>">
                                <input type="hidden" class="lp_code" value="<?php echo $data['key'] ?>"/>
                                <img src="<?php echo $data['thumbnail'] ?>" width="180" height="152"/>
                                <span class="tve_cell_caption_holder"><span class="tve_cell_caption"><?php echo $data['name'] ?></span></span>
                                <span class="tve_cell_check tve_icm tve-ic-checkmark"></span>
                            </span>
                        <?php endforeach ?>
                    </div>
                    <div class="tve_clear" style="height: 5px;"></div>
                </div>
                <div class="tve_scTC tve_scTC2" style="display: none;">
                    <a href="javascript:void(0)" data-ctrl="function:ext.tve_leads.template.delete_saved"
                       style="margin: 15px 0 0 0"
                       class="tve_click tve_editor_btn tve_btn_critical tve_right"><span><?php echo __('Delete template', 'thrive-leads') ?></span></a>
                    <h5><?php echo __('Choose from your saved templates', 'thrive-leads') ?></h5>

                    <?php if ($current_template) : ?>
                        <label>
                            <input type="checkbox" id="tl-user-current-templates" data-ctrl="function:ext.tve_leads.template.get_saved" class="tve_change" value="1"/>
                            <?php echo __('Show only saved versions of the current template', 'thrive-leads') ?>
                        </label>
                    <?php endif ?>

                    <div class="tve_clear" style="height: 15px;"></div>
                    <div class="tve_overflow_y" style="max-height: 380px" id="tl-saved-templates">

                    </div>
                </div>
                <?php if (!empty($multi_step)) : ?>
                    <div class="tve_scTC tve_scTC3" style="display: none">
                        <div class="tve_clear" style="height: 5px;"></div>
                        <div class="tve_overflow_y " style="">
                            <?php foreach ($multi_step as $data) : ?>
                                <span
                                    class="tve-tpl-<?php echo $form_type ?> tve_grid_cell tve_landing_page_template tve_click">
                                    <input type="hidden" class="lp_code" value="<?php echo $data['key'] ?>"/>
                                    <input type="hidden" class="multi_step" value="1"/>
                                    <img src="<?php echo $data['thumbnail'] ?>" width="180" height="152"/>
                                    <span class="tve_cell_caption_holder"><span class="tve_cell_caption"><?php echo $data['name'] ?></span></span>
                                    <span class="tve_cell_check tve_icm tve-ic-checkmark"></span>
                                </span>
                            <?php endforeach ?>
                        </div>
                        <div class="tve_clear" style="height: 5px;"></div>
                    </div>
                <?php endif ?>
            </div>
            <div class="tve_clear" style="height: 15px;"></div>
            <div class="tve_landing_pages_actions">
                <div id="tve-leads-choose-template" class="tve_btn_success tve_right tve_click" data-ctrl="function:ext.tve_leads.template.choose">
                    <div class="tve_update"><?php echo __('Choose template', 'thrive-leads') ?></div>
                </div>
                <?php if (!empty($current_template)) : ?>
                    <div style="margin-right: 20px;" id="tve-leads-reset-template" class="tve_btn_default tve_right tve_click" data-ctrl="function:ext.tve_leads.template.reset">
                        <div class="tve_preview"><?php echo __('Reset contents', 'thrive-leads') ?></div>
                    </div>
                <?php endif ?>
            </div>
            <div class="tve_clear"></div>
        </div>
    </div>
</div>
<script type="text/javascript">
    jQuery(function () {
        setTimeout(function () {
            jQuery('#tve_landing_page_filter').focus();
        }, 200);
    });
</script>