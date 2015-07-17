<span class="tve_options_headline"><span class="tve_icm tve-ic-move"></span>Thrive Leads Shortcode options</span>
<ul class="tve_menu">

    <?php include dirname(__FILE__) . '/_margin.php' ?>

    <li class="tve_ed_btn tve_btn_text tve_firstOnRow">
        <div class="tve_option_separator">
            <span class="tve_ind tve_left" data-default="Chose Leads Shortcode">Choose Leads Shortcode</span><span
                class="tve_caret tve_icm tve_left"></span>

            <div class="tve_clear"></div>
            <div class="tve_sub_btn">
                <div class="tve_sub active_sub_menu">
                    <ul>
                        <?php if (!empty($thrive_leads_shortcodes) && is_array($thrive_leads_shortcodes)) : ?>
                            <?php foreach ($thrive_leads_shortcodes as $thrive_leads_shortcode) : ?>
                                <li data-value="<?php echo $thrive_leads_shortcode->ID ?>" class="tve_click tve-o-leads-shortcode" data-ctrl="controls.thrive_leads_shortcode.option" data-fn="fetch"><?php echo $thrive_leads_shortcode->post_title ?></li>
                            <?php endforeach ?>
                        <?php endif ?>
                    </ul>
                </div>
            </div>
        </div>
    </li>
</ul>