<?php
$timezone_offset = get_option('gmt_offset');
$sign = ($timezone_offset < 0 ? '-' : '+');
$min = abs($timezone_offset) * 60;
$hour = floor($min / 60);
$tzd = $sign . str_pad($hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($min % 60, 2, '0', STR_PAD_LEFT);
?>

<div class="tve_lp_header tve_empty_dropzone"></div>
<div class="tve_lp_content_bg tve_no_drag tve_lp_content" style="margin: 0">
    <div class="tve_editor_main_content tve_empty_dropzone tve_content_width">
        <div class="thrv_wrapper thrv_contentbox_shortcode header_cb" data-tve-style="4">
            <div class="tve_cb tve_cb4 tve_white">
                <div class="tve_hd">
                    <span></span>
                </div>
                <div class="tve_cb_cnt">
                    <div class="thrv_wrapper thrv_columns tve_clearfix" style="">
                        <div class="tve_colm tve_foc tve_df tve_ofo ">
                            <div style="width: 287px; margin: 0;" class="thrv_wrapper tve_image_caption alignleft">
                                <span class="tve_image_frame">
                                    <img class="tve_image"
                                         src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/fame_second_logo.png' ?>"
                                         style="width: 287px"/>
                                </span>
                            </div>
                        </div>
                        <div class="tve_colm tve_tfo tve_df tve_lst">
                            <h2 style="font-size: 48px;margin-top: 40px;margin-bottom: 0;">We’re launching soon...</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <h1 style="font-size: 50px;margin-bottom: 5px;margin-top: 45px;" class="tve_p_center">Yes, you’ve come to the right place.</h1>

        <h1 style="font-size: 50px;margin-bottom: 40px;" class="tve_p_center">Our new site is launching soon...</h1>

        <div style="width: 41px;" class="thrv_wrapper tve_image_caption aligncenter">
                <span class="tve_image_frame">
                    <img class="tve_image"
                         src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/fame_small_divider.png' ?>"
                         style="width: 41px"/>
                </span>
        </div>
        <h3 style="font-size: 30px;" class="tve_p_center">Only a few days left:</h3>

        <div class="thrv_wrapper thrv_countdown_timer tve_cd_timer_plain tve_clearfix init_done"
             data-date="<?php echo date('Y-m-d', strtotime('+9 days')) ?>"
             data-hour="<?php echo date('h') ?>"
             data-min="<?php echo date('m') ?>"
             data-timezone="<?php echo $tzd ?>">
            <div class="sc_timer_content tve_clearfix tve_block_center" style="margin-bottom: 25px;">
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
        <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="6">
            <div class="tve_cb tve_cb6 tve_orange" style="background: #f0bd0d">
                <div class="tve_cb_cnt">
                    <h4 class="tve_p_center" style="margin-top: 35px;">Sign Up Below to be the First to Know When We’re Back:</h4>

                    <div class="thrv_wrapper thrv_lead_generation tve_clearfix tve_purple thrv_lead_generation_horizontal" data-tve-style="1" style="">
                        <div class="thrv_lead_generation_code" style="display: none;"></div>
                        <div class="thrv_lead_generation_container tve_clearfix">
                            <div class="tve_lead_generated_inputs_container tve_clearfix">
                                <div class="tve_lead_fields_overlay" style="width: 100%; height: 100%;"></div>
                                <div class=" tve_lg_input_container tve_lg_2 ">
                                    <input type="text" data-placeholder="Your Email Address..." placeholder="Your Email Address..." value="" name="first_name"/>
                                </div>
                                <div class="tve_lg_input_container tve_submit_container tve_lg_2">
                                    <button type="Submit">SUBMIT</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="tve_lp_footer tve_empty_dropzone tve_drop_constraint">
    <p class="tve_p_center" style="color: #fff;">&copy; 2014 Landing Page. All rights Reserved. <a href="http://thrivethemes.com" target="_blank">
            <font color="#fff">
                <span class="underline_text">Disclaimer</span>
            </font>
        </a></p>

    <div class="thrv_wrapper thrv_content_container_shortcode">
        <div class="tve_clear"></div>
        <div class="tve_center tve_content_inner" style="width: 165px;min-width:50px; min-height: 2em;">
            <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="4">
                <div class="tve_cb tve_cb4 tve_orange" style="background: transparent; border: 1px solid #774a63; border-radius: 200px;">
                    <div class="tve_cb_cnt">
                        <div style="margin-right: 7px;" class="thrv_wrapper thrv_icon alignleft rounded_fame_icons">
                        <span style="background: rgba(70, 41, 62, 0.50); color: #f0bd0d; font-size: 20px;"
                              class="tve_sc_icon fame-icon-facebook" data-tve-icon="fame-icon-facebook"></span>
                        </div>
                        <div style="margin-right: 7px;" class="thrv_wrapper thrv_icon alignleft rounded_fame_icons">
                        <span style="background: rgba(70, 41, 62, 0.50); color: #f0bd0d; font-size: 20px;"
                              class="tve_sc_icon fame-icon-twitter" data-tve-icon="fame-icon-twitter"></span>
                        </div>
                        <div style="" class="thrv_wrapper thrv_icon alignleft rounded_fame_icons">
                        <span style="background: rgba(70, 41, 62, 0.50); color: #f0bd0d; font-size: 20px;"
                              class="tve_sc_icon fame-icon-google" data-tve-icon="fame-icon-google"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="tve_clear"></div>
    </div>
</div>