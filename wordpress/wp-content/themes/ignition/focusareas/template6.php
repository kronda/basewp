<?php
$focus_area_class = $current_attrs['_thrive_meta_ribbon_color'][0];
$action_link_target = ($current_attrs['_thrive_meta_focus_new_tab'][0] == 1) ? "_blank" : "_self";

?>

<div class="rbn <?php echo $focus_area_class; ?>">
    <div class="rbin clearfix">
        <!--<span class="left"></span>-->
        <a href="<?php echo $current_attrs['_thrive_meta_focus_button_link'][0]; ?>" target="<?php echo $action_link_target; ?>">
             <?php echo $current_attrs['_thrive_meta_focus_ribbon_text'][0];?>
            <span class="awe"></span>
        </a>
    </div>
</div>