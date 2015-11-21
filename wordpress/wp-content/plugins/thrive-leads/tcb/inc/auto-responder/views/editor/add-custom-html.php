<h4><?php echo __("Step 2: Insert HTML code", "thrive-cb") ?></h4>
<hr class="tve_lightbox_line"/>
<p><?php echo __("Step 2: Now insert your full HTML autoresponder code. You can find more information about what code is required", "thrive-cb") ?>
    <a class="tve_lightbox_link tve_lightbox_link_info" target="_blank" href="http://thrivethemes.com/tkb_item/add-autoresponder-code-form/"><?php echo __("in our knowledge base", "thrive-cb") ?></a>
</p>

<?php $show_textarea = true; $show_reCaptcha = false; include dirname(__FILE__) . '/autoresponder-code-fields.php' ?>

