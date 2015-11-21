<?php
$focus_area_class = $current_attrs['_thrive_meta_focus_color'][0];
$action_link_target = ($current_attrs['_thrive_meta_focus_new_tab'][0] == 1) ? "_blank" : "_self";
$wrapper_class = ($position == "top") ? "wrp" : "wrp lfa";
$section_position = ($position == "bottom") ? "farb" : "";
$btn_class = (empty($current_attrs['_thrive_meta_focus_button_color'][0])) ? "blue" : strtolower($current_attrs['_thrive_meta_focus_button_color'][0]);

//overwrite the class of the bottom focus area for the page templates
if ($position == "bottom") {
    $template_name = get_post_custom_values('_thrive_meta_post_template', get_the_ID());
    $template_name = isset($template_name[0]) ? $template_name[0] : "";
    if ($template_name == "Full Width" || $template_name == "Landing" || $template_name == "Narrow") {
        $wrapper_class = "wrp";
        $section_position = "";
    }
}
?>

<section class="far f1 <?php echo $focus_area_class; ?> <?php echo $section_position; ?>">
    <div class="<?php echo $wrapper_class;?>">			
        <h2 class="upp"><?php echo $current_attrs['_thrive_meta_focus_heading_text'][0]; ?></h2>
        <p><?php echo nl2br(do_shortcode($current_attrs['_thrive_meta_focus_subheading_text'][0])); ?></p>
        <br/>
        <a href="<?php echo $current_attrs['_thrive_meta_focus_button_link'][0]; ?>" class="btn big <?php echo $btn_class;?>" target="<?php echo $action_link_target; ?>">
            <span class="fbt"><?php echo $current_attrs['_thrive_meta_focus_button_text'][0]; ?></span>
        </a>
    </div>
</section>
