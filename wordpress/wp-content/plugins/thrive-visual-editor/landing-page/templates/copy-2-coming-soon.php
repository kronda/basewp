<?php
$timezone_offset = get_option('gmt_offset');
$sign = ($timezone_offset < 0 ? '-' : '+');
$min = abs($timezone_offset) * 60;
$hour = floor($min / 60);
$tzd = $sign . str_pad($hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($min % 60, 2, '0', STR_PAD_LEFT);
?>
<div class="tve_lp_header tve_empty_dropzone tve_content_width">
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1" style="">
        <div class="out" style="background-color: #0c81bf;">
            <div class="in lightSec">
                <div class="cck clearfix">
                    <div style="width: 265px;margin-top: 0;margin-bottom: 0;"
                         class="thrv_wrapper tve_image_caption aligncenter">
                        <span class="tve_image_frame">
                            <a href="">
                                <img class="tve_image"
                                     src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/copy2_logo.png' ?>"
                                     style="width: 265px;"/>
                            </a>
                        </span>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<div class="tve_lp_content tve_editor_main_content tve_empty_dropzone tve_content_width">
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1" style="margin-bottom: -20px;">
        <div class="out" style="background-color: #0c81bf;">
            <div class="in lightSec">
                <div class="cck clearfix">
                    <h1 class="tve_p_center rft" style="color: #fff; font-size: 60px;margin-top: 0;margin-bottom: 30px;">
                        We’re Launching Soon
                    </h1>
                    <h3 class="tve_p_center rft" style="color: #fff; font-size: 30px;margin-top: 0;margin-bottom: 60px;">
                        We are currently working on an <span class="bold_text">Awesome new product.</span> Stay tuned
                    </h3>
                    <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="1">
                        <div class="tve_cb tve_cb1 tve_black">
                            <div class="tve_hd tve_cb_cnt">
                                <h5 class="tve_p_center" style="color: #fff; font-size: 25px;margin-top: 0;margin-bottom: 0;">
                                    Time left until launch
                                </h5>
                                <div class="thrv_wrapper thrv_countdown_timer tve_cd_timer_plain tve_clearfix init_done tve_countdown_3 tve_white"
                                     data-date="<?php echo date('Y-m-d', strtotime('+9 days')) ?>"
                                     data-hour="<?php echo date('h') ?>"
                                     data-min="<?php echo date('m') ?>"
                                     data-timezone="<?php echo $tzd ?>">
                                    <div class="sc_timer_content tve_clearfix tve_block_center" style="margin-bottom: -115px;">
                                        <div class="tve_t_day tve_t_part">
                                            <div class="t-digits"></div>
                                            <div class="t-caption">DAYS</div>
                                        </div>
                                        <div class="tve_t_hour tve_t_part">
                                            <div class="t-digits"></div>
                                            <div class="t-caption">HOURS</div>
                                        </div>
                                        <div class="tve_t_min tve_t_part">
                                            <div class="t-digits"></div>
                                            <div class="t-caption">MIN</div>
                                        </div>
                                        <div class="tve_t_sec tve_t_part">
                                            <div class="t-digits"></div>
                                            <div class="t-caption">SEC</div>
                                        </div>
                                        <div class="tve_t_text"></div>
                                    </div>
                                </div>
                                <span></span>
                            </div>
                            <div class="tve_cb_cnt">
                                <div class="thrv_wrapper thrv_content_container_shortcode">
                                    <div class="tve_clear"></div>
                                    <div class="tve_center tve_content_inner" style="width: 760px;min-width:50px; min-height: 2em;">
                                        <h4 style="color: #333333; font-size: 30px;margin-top: 85px;margin-bottom: 20px;" class="tve_p_center rft">
                                            <span class="bold_text">Sign Up Below</span> to get on the early bird notification list:
                                        </h4>
                                        <div
                                            class="thrv_wrapper thrv_lead_generation tve_clearfix tve_orange tve_2 thrv_lead_generation_horizontal"
                                            data-inputs-count="2" data-tve-style="1">
                                            <div class="thrv_lead_generation_code" style="display: none;"></div>
                                            <div class="thrv_lead_generation_container tve_clearfix">
                                                <div class="tve_lead_generated_inputs_container tve_clearfix">
                                                    <div class="tve_lead_fields_overlay"></div>
                                                    <div class=" tve_lg_input_container tve_lg_2 tve_lg_input">
                                                        <input type="text" data-placeholder="Your Email Address"
                                                               placeholder="Your Email Address"
                                                               value=""
                                                               name="email_address"/>
                                                    </div>
                                                    <div class="tve_lg_input_container tve_submit_container tve_lg_2 tve_lg_submit">
                                                        <button type="Submit">SUBMIT</button>
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
            </div>
        </div>
    </div>
    <div class="thrv_wrapper thrv_social thrv_social_custom tve_centerBtn" style="margin-top: 60px;">
        <div class="tve_social_items tve_social_custom tve_style_5 tve_social_medium tve_social_ib">
            <div class="tve_s_item tve_s_fb_share" data-s="fb_share" data-href="{tcb_post_url}">
                <a href="javascript:void(0)" class="tve_s_link"><span class="tve_s_icon"></span><span
                        class="tve_s_text">Share</span><span class="tve_s_count">0</span></a>
            </div>
            <div class="tve_s_item tve_s_t_share" data-s="t_share" data-href="{tcb_post_url}">
                <a href="javascript:void(0)" class="tve_s_link"><span class="tve_s_icon"></span><span
                        class="tve_s_text">Tweet</span><span class="tve_s_count">0</span></a>
            </div>
            <div class="tve_s_item tve_s_in_share" data-s="in_share" data-href="{tcb_post_url}">
                <a href="javascript:void(0)" class="tve_s_link"><span class="tve_s_icon"></span><span
                        class="tve_s_text">Share</span><span class="tve_s_count">0</span></a>
            </div>
        </div>
        <div class="tve_social_overlay"></div>
    </div>
</div>

<div class="tve_lp_footer tve_empty_dropzone tve_content_width">
    <div class="thrv_wrapper thrv_columns">
        <div class="tve_colm tve_twc">
            <p class="tve_p_left" style="margin: 0; padding: 0; color: #666666; font-size: 22px">Copyright - Company Name
                Inc.</p>
        </div>
        <div class="tve_colm tve_twc tve_lst">
            <p class="tve_p_right" style="margin: 0; padding: 0; color: #666; font-size:22px">
                <a href="#">Disclaimer</a>
                <a href="#">Contact</a>
            </p>
        </div>
    </div>
</div>