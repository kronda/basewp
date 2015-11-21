<?php
$focus_area_class = $current_attrs['_thrive_meta_focus_color'][0];
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

<section class="far f3 <?php echo $focus_area_class; ?> <?php echo $section_position; ?>">
    <div class="<?php echo $wrapper_class;?>">
        <div class="left">
            <h4 class="upp"><?php echo $current_attrs['_thrive_meta_focus_heading_text'][0]; ?></h4>

            <p><?php echo nl2br(do_shortcode($current_attrs['_thrive_meta_focus_subheading_text'][0])); ?></p>
        </div>
        <div class="right">
            <div class="frm">
                <form action="<?php echo $optinFormAction; ?>" method="post">
                    <?php
                    if ($optinFieldsArray):
                        foreach ($optinFieldsArray as $name_attr => $field_label):
                            ?>
                            <input type="text" class="focus3_email" placeholder="<?php echo $field_label; ?>" name='<?php echo _thrive_get_optin_name_attr_fixed($name_attr); ?>' /><br/>
                            <?php
                        endforeach;
                    endif;
                    ?>
                    <input type="submit" class="focus3_submit <?php echo $btn_class;?>" value="<?php echo $current_attrs['_thrive_meta_focus_button_text'][0]; ?>" />
                    <?php echo $optinHiddenInputs; ?>
                </form>
            </div>
        </div>
        <div class="clear"></div>
    </div>
</section>
