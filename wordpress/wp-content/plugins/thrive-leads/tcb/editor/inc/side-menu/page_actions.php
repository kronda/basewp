<div class="tve_cpanel_sec" id="tve_page_actions">
    <div class="tve_ed_btn tve_btn_icon tve_left">
        <div class="tve_icm tve-ic-undo tve-disabled" id="tve_undo_manager" title="Undo last action"></div>
    </div>
    <div class="tve_ed_btn tve_btn_icon tve_left tve_expanded">
        <div class="tve_icm tve-ic-redo tve-disabled" id="tve_redo_manager" title="Redo last action"></div>
    </div>
    <div class="tve_ed_btn tve_btn_icon tve_left" title="HTML">
        <div class="tve_icm tve-ic-code tve_click" data-ctrl="controls.lb_open" id="lb_full_html" title="HTML"></div>
    </div>
    <div class="tve_ed_btn tve_btn_icon tve_left" title="Save page content">
        <div class="tve_icm tve-ic-toggle-down tve_click tve_lb_small" data-ctrl="controls.lb_open" id="lb_save_user_template" title="Save page content as template"></div>
    </div>
    <div class="tve_ed_btn tve_btn_icon tve_left tve_option_separator tve_dropdown_submenu">
        <div class="tve_icm tve-ic-plus">
            <div class="tve_sub_btn">
                <div class="tve_sub active_sub_menu" id="tve_global_page_settings">
                    <ul>
                        <?php if ($post_type != 'tcb_lightbox' && empty($_POST['disabled_controls']['page_events'])) : ?>
                            <li data-ctrl="controls.lb_open" id="tve_event_manager" class="tve_click" title="Page Event Manager">
                                Page Event Manager
                                <input type="hidden" name="scope" value="page">
                            </li>
                        <?php endif ?>
                        <li id="tve_flipEditor" class="tve_click" data-skip-undo="1" data-ctrl="controls.click.flip_editor">Switch Editor Side</li>
                        <li id="tve_flipColor" class="tve_click" data-skip-undo="1" data-ctrl="controls.click.flip_editor_color">Change Editor Color</li>
                        <li title="Turn On/Off Save Reminders on entire site" class="tve_click" data-skip-undo="1" data-ctrl="controls.click.save_reminders" data-args="<?php echo $tve_display_save_notification ? 0 : 1 ?>">Turn <span><?php echo $tve_display_save_notification ? "Off" : "On" ?></span> Save Reminders</li>
                    </ul>
                    <div class="tve_clear"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="tve_ed_btn tve_btn_icon tve_left" title="<?php echo __('Revision Manager')?>" style="<?php echo !$last_revision_id ? "display: none" : ""; ?>">
        <div class="tve_icm tve-ic-back-in-time tve_click" data-wpapi="lb_revision_manager" data-skip-undo="1" data-ctrl="controls.lb_open" data-load="1" data-btntext="<?php echo __('Close') ?>" id="lb_revision_manager"></div>
    </div>
    <div class="tve_clear"></div>
</div>
<?php
/**
 * action that allows outputting custom page buttons to the top of the control panel
 */
do_action('tcb_custom_top_buttons'); ?>