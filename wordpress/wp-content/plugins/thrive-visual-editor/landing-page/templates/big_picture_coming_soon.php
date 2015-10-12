<?php
$timezone_offset = get_option('gmt_offset');
$sign = ($timezone_offset < 0 ? '-' : '+');
$min = abs($timezone_offset) * 60;
$hour = floor($min / 60);
$tzd = $sign . str_pad($hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($min % 60, 2, '0', STR_PAD_LEFT);
?>

<div class="tve_lp_header tve_empty_dropzone">
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1" style="">
        <div class="out" style="background-color: #ffcc00">
            <div class="in darkSec">
                <div class="cck clearfix">
                    <h1 class="tve_p_center bold_text" style="color: #2b2b2b; font-size: 55px;margin-top: 60px;margin-bottom: 40px;">Something Big is Coming...</h1>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="tve_lp_content tve_editor_main_content tve_empty_dropzone tve_content_width">
    <h2 style="margin: 30px 0 0 0; color: #2b2b2b; font-size: 27px" class="tve_p_center">We are currently working on an <span class="bold_text">awesome new product.</span> Stay Tuned!</h2>

    <div class="thrv_wrapper" style="margin: 0 0 30px 0">
        <hr class="tve_sep tve_sep1"/>
    </div>

    <div class="thrv_wrapper thrv_content_container_shortcode">
        <div class="tve_clear"></div>
        <div class="tve_center tve_content_inner" style="width: 815px;min-width:50px; min-height: 2em;">
            <div class="thrv_wrapper thrv_columns tve_clearfix" style="margin-bottom: 50px;">
                <div class="tve_colm tve_tfo tve_df ">
                    <div class="thrv_wrapper thrv_countdown_timer tve_cd_timer_plain tve_clearfix init_done"
                         data-date="<?php echo date('Y-m-d', strtotime('+9 days')) ?>"
                         data-hour="<?php echo date('h') ?>"
                         data-min="<?php echo date('m') ?>"
                         data-timezone="<?php echo $tzd ?>">
                        <div class="sc_timer_content tve_clearfix tve_block_center" style="">
                            <div class="tve_t_day tve_t_part">
                                <div class="t-digits"></div>
                                <div class="t-caption">Days</div>
                            </div>
                            <div class="tve_t_hour tve_t_part">
                                <div class="t-digits"></div>
                                <div class="t-caption">Hours</div>
                            </div>
                            <div class="tve_t_min tve_t_part">
                                <div class="t-digits"></div>
                                <div class="t-caption">Minutes</div>
                            </div>
                            <div class="tve_t_sec tve_t_part">
                                <div class="t-digits"></div>
                                <div class="t-caption">Seconds</div>
                            </div>
                            <div class="tve_t_text"></div>
                        </div>
                    </div>
                </div>
                <div class="tve_colm  tve_foc tve_ofo tve_df tve_lst">
                    <div class="thrv_wrapper tve_image_caption aligncenter" style="width: 192px;">
                        <span class="tve_image_frame">
                            <img class="tve_image" src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/time_to_launch.png' ?>" style="width: 192px"/>
                        </span>
                    </div>
                </div>
            </div>
            <div class="thrv_wrapper thrv_lead_generation tve_clearfix thrv_lead_generation_horizontal tve_orange  " data-tve-style="1">
                <div class="thrv_lead_generation_code" style="display: none;"></div>
                <div class="thrv_lead_generation_container tve_clearfix">
                    <div class="tve_lead_generated_inputs_container tve_clearfix">
                        <div class="tve_lead_fields_overlay"></div>
                        <div class=" tve_lg_input_container tve_lg_2">
                            <input type="text" data-placeholder="" value="" name="name" placeholder="Enter your email  here to get update from us!"/>
                        </div>
                        <div class="tve_lg_input_container tve_submit_container tve_lg_2">
                            <button type="Submit">Submit</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tve_clear"></div>
    </div>
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1">
        <div class="out" style="background-color: #ededed">
            <div class="in darkSec">
                <div class="cck tve_clearfix">
                    <div class="thrv_wrapper thrv_columns" style="margin: 0;">
                        <div class="tve_colm tve_twc"><p class="tve_p_right" style="color: #999999; font-size: 18px;">For More Awesome News You Can Follow us on:</p></div>
                        <div class="tve_colm tve_twc tve_lst">
                            <div class="thrv_wrapper tve_image_caption alignleft" style="width: 31px; margin: 0 7px 0 0;">
                                <span class="tve_image_frame">
                                    <img class="tve_image" src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/bp_twitter.png' ?>" style="margin: 0 7px 0 0;width: 31px;"/>
                                </span>
                            </div>
                            <div class="thrv_wrapper tve_image_caption alignleft" style="width: 31px; margin: 0 7px 0 0;">
                                <span class="tve_image_frame">
                                    <img class="tve_image" src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/bp_facebook.png' ?>" style="margin: 0 7px 0 0;width: 31px;"/>
                                </span>
                            </div>
                            <div class="thrv_wrapper tve_image_caption alignleft" style="width: 31px; margin: 0 7px 0 0;">
                                <span class="tve_image_frame">
                                    <img class="tve_image" src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/bp_linkedin.png' ?>" style="margin: 0 7px 0 0;width: 31px;"/>
                                </span>
                            </div>
                            <div class="thrv_wrapper tve_image_caption alignleft" style="width: 31px; margin: 0 7px 0 0;">
                                <span class="tve_image_frame">
                                    <img class="tve_image" src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/bp_youtube.png' ?>" style="margin: 0 7px 0 0;width: 31px;"/>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="tve_lp_footer tve_empty_dropzone">
    <div class="thrv_wrapper thrv_page_section tve_no_drag" data-tve-style="1">
        <div class="out">
            <div class="in darkSec">
                <div class="cck tve_clearfix">
                    <p class="float-left tve_p_left" style="margin: 0; padding: 0; color: #555555;">Copyright {tcb_current_year} by ACME Inc.</p>

                    <p class="float-right tve_p_right" style="margin: 0; padding: 0; color: #555555;">
                        <a href="#">Disclaimer</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>