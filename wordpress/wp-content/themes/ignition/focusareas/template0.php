<?php
$focus_area_class = $current_attrs['_thrive_meta_focus_color'][0];
$wrapper_class = ($position == "top") ? "wrp" : "wrp lfa";
$section_position = ($position == "bottom") ? "farb" : "";
?>

<section class="far f0 <?php echo $focus_area_class; ?> <?php echo $section_position; ?>">
    <div class="<?php echo $wrapper_class;?>">
        <?php echo nl2br(do_shortcode($current_attrs['_thrive_meta_focus_subheading_text'][0])); ?>
    </div>
</section>