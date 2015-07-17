<li data-ctrl="controls.lb_open" class="tve_ed_btn tve_btn_text tve_click <?php echo isset($li_custom_class) ? $li_custom_class : '' ?>" id="tve_event_manager" <?php echo isset($li_custom_style) ? $li_custom_style : '' ?>>
    Event Manager
    <?php
    if (!empty($_POST['disabled_controls']['event_manager'])) {
        foreach ($_POST['disabled_controls']['event_manager'] as $item) {
            echo sprintf('<input type="hidden" name="disabled_controls[]" value="%s">', $item);
        }
    }
    ?>
</li>
<?php unset($li_custom_class, $li_custom_style); ?>