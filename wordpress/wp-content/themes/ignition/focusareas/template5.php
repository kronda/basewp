<?php
$focus_area_class = $current_attrs['_thrive_meta_focus_color'][0];
$wrapper_class = ($position == "top") ? "wrp" : "wrp lfa";
$section_position = ($position == "bottom") ? "farb" : "";
$btn_class = (empty($current_attrs['_thrive_meta_focus_button_color'][0])) ? "blue" : strtolower($current_attrs['_thrive_meta_focus_button_color'][0]);
if (!is_array($optinFieldsArray)) {
    $optinFieldsArray = array();
}
if (count($optinFieldsArray) > 2) {
echo "This focus area template supports only 2 input fields. Please check your opt-in configuration in order to use this template.";
return;
}

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

<section class="far f5 <?php echo $focus_area_class; ?> <?php echo $section_position; ?>">
    <div class="<?php echo $wrapper_class;?>">
        <?php if ($current_attrs['_thrive_meta_focus_image'][0] != ""): ?>
            <div class="left">
                <img class="f5l left" src="<?php echo $current_attrs['_thrive_meta_focus_image'][0]; ?>" alt="">
                <div class="f5r left">
                    <h2 class="upp"><?php echo $current_attrs['_thrive_meta_focus_heading_text'][0]; ?></h2>
                    <p>
                        <?php echo nl2br(do_shortcode($current_attrs['_thrive_meta_focus_subheading_text'][0])); ?>
                    </p>
                </div>
                <div class="clear"></div>
            </div>
        <?php else:?>
            <div class="left">
                <h2 class="upp"><?php echo $current_attrs['_thrive_meta_focus_heading_text'][0]; ?></h2>
                <p>
                    <?php echo nl2br(do_shortcode($current_attrs['_thrive_meta_focus_subheading_text'][0])); ?>
                </p>
            </div>
        <?php endif; ?>
        <div class="clear"></div>
        <hr/>
        <?php if (!empty($optin_id)) : ?>
        <div class="frm">
            <form action="<?php echo $optinFormAction; ?>" method="<?php echo $optinFormMethod ?>">
                <?php echo $optinHiddenInputs; ?>

                <?php echo $optinNotVisibleInputs; ?>

                <?php if ($optinFieldsArray && is_array($optinFieldsArray)): ?>

                    <?php $input_class = count($optinFieldsArray) == 2 ? "input_for_2_fields" : "input_for_1_fields"; ?>

                    <?php foreach ($optinFieldsArray as $name_attr => $field_label): ?>
                        <?php echo Thrive_OptIn::getInstance()->getInputHtml($name_attr, $field_label, array($input_class, "left")) ?>
                    <?php endforeach; ?>

                <?php endif; ?>

                <?php $submit_class = count($optinFieldsArray) == 2 ? "submit_with_2_fields" : "submit_with_1_fields right"; ?>

                <div class="btn big <?php echo $btn_class;?> <?php echo $submit_class;?>">
                    <input type="submit" value="<?php echo $current_attrs['_thrive_meta_focus_button_text'][0]; ?>"
                           class="fbt right focus_submit"/>
                </div>
                <div class="clear"></div>
            </form>
        </div>
        <?php endif; ?>
    </div>
</section>