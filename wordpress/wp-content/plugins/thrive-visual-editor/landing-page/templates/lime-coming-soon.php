<?php
$timezone_offset = get_option('gmt_offset');
$sign = ($timezone_offset < 0 ? '-' : '+');
$min = abs($timezone_offset) * 60;
$hour = floor($min / 60);
$tzd = $sign . str_pad($hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($min % 60, 2, '0', STR_PAD_LEFT);
?>
<div class="tve_lp_header tve_empty_dropzone"></div>
<div class="tve_lp_content tve_editor_main_content tve_empty_dropzone tve_content_width">
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1" style="margin-top: -40px;">
        <div class="out" style="background-color: #68bf61">
            <div class="in">
                <div class="cck tve_clearfix">
                    <div style="width: 189px;margin-top: 50px;margin-bottom: 50px;" class="thrv_wrapper tve_image_caption">
                        <span class="tve_image_frame">
                            <a href="">
                                <img class="tve_image"
                                     src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/lime-thrive-logo-light.png' ?>"
                                     style="width: 189px"/>
                            </a>
                        </span>
                    </div>
                    <h2 style="color: #fff; font-size: 36px;margin-top: 0;margin-bottom: 35px;" class="tve_p_center">The App that Will Transform the Way You Run Your Business..</h2>
                    <p style="color: #fff; font-size: 18px; margin-top: 0;margin-bottom: 25px;" class="tve_p_center">We are gearing up to a launch that you won't want to miss. </p>
                    <div class="thrv_wrapper thrv_content_container_shortcode">
                        <div class="tve_clear"></div>
                        <div class="tve_center tve_content_inner" style="width: 620px;min-width:50px; min-height: 2em;">
                            <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="6">
                                <div class="tve_cb tve_cb6 tve_white" style="margin-bottom: -62px;">
                                    <div class="tve_cb_cnt">
                                        <p style="color: #000;font-size: 15px;margin-bottom: 0;" class="tve_p_center">
                                            <span class="bold_text">
                                                TIME LEFT UNTIL LAUNCH DAY:
                                            </span>
                                        </p>
                                        <div class="thrv_wrapper thrv_countdown_timer tve_cd_timer_plain tve_clearfix init_done"
                                             data-date="<?php echo date('Y-m-d', strtotime('+9 days')) ?>"
                                             data-hour="<?php echo date('h') ?>"
                                             data-min="<?php echo date('m') ?>"
                                             data-timezone="<?php echo $tzd ?>">
                                            <div class="sc_timer_content tve_clearfix tve_block_center" style="margin-bottom: 0;margin-top: 25px;">
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
                                </div>
                            </div>
                        </div>
                        <div class="tve_clear"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="thrv_wrapper thrv_content_container_shortcode">
        <div class="tve_clear"></div>
        <div class="tve_center tve_content_inner" style="width: 620px;min-width:50px; min-height: 2em;">
            <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="5">
                <div class="tve_cb tve_cb5 tve_white" style="margin-top: -20px;">
                    <div class="tve_cb_cnt">
                        <p class="tve_p_center" style="color: #000;font-size: 15px;margin-top: 0;margin-bottom: 25px;">
                            <span class="bold_text">Sign up</span> below to get an early-bird notification as soon as our app is ready!
                        </p>
                        <div class="thrv_wrapper thrv_lead_generation tve_clearfix tve_green tve_fullwidthBtn tve_2 thrv_lead_generation_horizontal"
                             data-inputs-count="2" data-tve-style="1" style="margin-bottom: 0;">
                            <div class="thrv_lead_generation_code" style="display: none;"></div>
                            <div class="thrv_lead_generation_container tve_clearfix">
                                <div class="tve_lead_generated_inputs_container tve_clearfix">
                                    <div class="tve_lead_fields_overlay"></div>
                                    <div class=" tve_lg_input_container tve_lg_2 tve_lg_input">
                                        <input type="text" data-placeholder="Full Name" placeholder="Full Name" value=""
                                               name="first_name"/>
                                    </div>
                                    <div class="tve_lg_input_container tve_submit_container tve_lg_2 tve_lg_submit">
                                        <button type="Submit">SIGN UP</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="thrv_wrapper thrv_columns tve_clearfix">
                <div class="tve_colm tve_tfo tve_df ">
                    <p class="tve_p_right" style="color: #7d7d7d; font-size: 15px;margin-top: 0;margin-bottom: 0;">Keep up with the latest news about our app here:</p>
                </div>
                <div class="tve_colm  tve_foc tve_ofo tve_df tve_lst">
                    <div style="width: 20px; margin-top: 0;margin-bottom: 0;" class="thrv_wrapper tve_image_caption alignleft">
                        <span class="tve_image_frame">
                            <a href="">
                                <img class="tve_image"
                                     src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/lime-facebook.png' ?>"
                                     style="width: 20px"/>
                            </a>
                        </span>
                    </div>
                    <div style="width: 20px; margin-top: 0;margin-bottom: 0;" class="thrv_wrapper tve_image_caption alignleft">
                        <span class="tve_image_frame">
                            <a href="">
                                <img class="tve_image"
                                     src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/lime-twitter.png' ?>"
                                     style="width: 20px"/>
                            </a>
                        </span>
                    </div>
                    <div style="width: 20px; margin-top: 0;margin-bottom: 0;" class="thrv_wrapper tve_image_caption alignleft">
                        <span class="tve_image_frame">
                            <a href="">
                                <img class="tve_image"
                                     src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/lime-google.png' ?>"
                                     style="width: 20px"/>
                            </a>
                        </span>
                    </div>
                </div>
            </div>

        </div>
        <div class="tve_clear"></div>
    </div>
</div>

<div class="tve_lp_footer tve_empty_dropzone tve_content_width">
    <div class="thrv_wrapper thrv_columns">
        <div class="tve_colm tve_twc">
            <p class="tve_p_left" style="margin: 0; padding: 0; color: #898989; font-size: 14px">Copyright &copy; 2014</p>
        </div>
        <div class="tve_colm tve_twc tve_lst">
            <p class="tve_p_right" style="margin: 0; padding: 0; color: #898989; font-size: 14px">
                <a href="#"><span class="underline_text">Disclaimer</span></a>
            </p>
        </div>
    </div>
</div>