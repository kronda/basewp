<li class="">
    <input type="text" class="element_class tve_change"
           data-ctrl="controls.change.cls" <?php echo !empty($change_class_target) ? 'data-args="' . $change_class_target . '"' : '' ?>
           placeholder="Custom class">
</li>
<?php
if (!empty($change_class_target)) {
    unset($change_class_target);
}
?>