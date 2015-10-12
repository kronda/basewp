<?php
$tve_globals = tve_get_post_meta(get_the_ID(), 'tve_globals', true);
$events_config = array(
    array(
        't' => 'click',
        'a' => 'thrive_lightbox',
        'config' => array(
            'l_id' => empty($tve_globals['lightbox_id']) ? '' : $tve_globals['lightbox_id'],
            'l_anim' => 'slide_top'
        )
    )
);
?>
<div class="tve_lp_header tve_content_width tve_empty_dropzone">
    <div class="thrv_wrapper thrv_columns" style="margin-top: 0;margin-bottom: 0;">
        <div class="tve_colm tve_foc tve_df tve_ofo ">
            <div style="margin-top: 0;margin-bottom: 0;width: 275px;" class="thrv_wrapper tve_image_caption aligncenter">
                        <span class="tve_image_frame">
                            <img class="tve_image"
                                 src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/content-focused-logo.png' ?>"
                                 style="width: 275px"/>
                        </span>
            </div>
        </div>
        <div class="tve_colm tve_tfo tve_df tve_lst">
            <p class="tve_p_right hidden-on-mobile" style="color: #666; font-size: 20px;margin-top: 20px;margin-bottom: 0;">
                <a class="tve_evt_manager_listen" href="" data-tcb-events="__TCB_EVENT_<?php echo htmlentities(json_encode($events_config)) ?>_TNEVE_BCT__">
                    &rsaquo; &nbsp;Free Report
                </a>
                <a href="//imimpact.com" target="_blank">&nbsp;&nbsp;&nbsp;&rsaquo; &nbsp;Blog</a>
            </p>
        </div>
    </div>
    <div class="thrv_wrapper" style="margin-top: 0;margin-bottom: 0;">
        <hr class="tve_sep tve_sep1"/>
    </div>
</div>
<div class="tve_lp_content tve_editor_main_content tve_empty_dropzone tve_content_width">
    <div class="thrv_wrapper thrv_content_container_shortcode">
        <div class="tve_clear"></div>
        <div class="tve_center tve_content_inner" style="width: 960px;min-width:50px; min-height: 2em;">
            <h1 class="tve_p_center rft" style="color: #56a390; font-size: 45px;margin-top: 45px;margin-bottom: 30px;">
                Join the Web’s Best CRO Newsletter & Get
                <font color="#000000">FREE ACCESS</font> to the “Landing Page Boost” PDF!
            </h1>
        </div>
        <div class="tve_clear"></div>
    </div>
    <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="5">
        <div class="tve_cb tve_cb5 tve_teal" style="margin-bottom: -55px;">
            <div class="tve_cb_cnt">
                <div class="thrv_wrapper thrv_content_container_shortcode">
                    <div class="tve_clear"></div>
                    <div class="tve_center tve_content_inner" style="width: 890px;min-width:50px; min-height: 2em;">
                        <p style="color: #000; font-size: 22px;margin-top: 0;margin-bottom: 25px;">
                            Are your site’s most important landing pages converting as well as they should? Have you
                            employed all the tweaks & tricks that seem small but make a huge difference to your bottom
                            line?
                        </p>

                        <p style="color: #000; font-size: 22px;margin-top: 0;margin-bottom: 0;">
                            Sign up below to get instant access to our very best landing page optimization resource -
                            including the checklist we use every day in our client work, optimizing conversions for the
                            Internet’s most valuable companies:
                        </p>
                    </div>
                    <div class="tve_clear"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1">
        <div class="out" style="background-color: #56a390;">
            <div class="in lightSec">
                <div class="cck clearfix">
                    <div class="thrv_wrapper thrv_content_container_shortcode">
                        <div class="tve_clear"></div>
                        <div class="tve_center tve_content_inner" style="width: 950px;min-width:50px; min-height: 2em;">
                            <div class="thrv_wrapper thrv_columns tve_clearfix"
                                 style="margin-top: 30px;margin-bottom: 30px;">
                                <div class="tve_colm tve_oth">
                                    <div style="width: 226px;"
                                         class="thrv_wrapper tve_image_caption aligncenter">
                                <span class="tve_image_frame">
                                    <img class="tve_image"
                                         src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/content-focused-product.png' ?>"
                                         style="width: 226px"/>
                                </span>
                                    </div>
                                </div>
                                <div class="tve_colm tve_tth tve_lst">
                                    <h2 style="color: #fffd37; font-size: 35px;margin-top: 0;margin-bottom: 0;" class="rft">
                                        Sign Up Below to <br>
                                        Get Instant Access to the Report:
                                    </h2>

                                    <div
                                        class="thrv_wrapper thrv_lead_generation tve_clearfix tve_orange tve_3 thrv_lead_generation_vertical"
                                        data-inputs-count="3" data-tve-style="1" style="margin-bottom: 0;">
                                        <div class="thrv_lead_generation_code" style="display: none;"></div>
                                        <div class="thrv_lead_generation_container tve_clearfix">
                                            <div class="tve_lead_generated_inputs_container tve_clearfix">
                                                <div class="tve_lead_fields_overlay"></div>
                                                <div class=" tve_lg_input_container tve_lg_3 tve_lg_input">
                                                    <input type="text" data-placeholder="Your name..."
                                                           placeholder="Your name..."
                                                           value=""
                                                           name="first_name"/>
                                                </div>
                                                <div class=" tve_lg_input_container tve_lg_3 tve_lg_input">
                                                    <input type="text" data-placeholder="Your email"
                                                           placeholder="Your email"
                                                           value=""
                                                           name="email"/>
                                                </div>
                                                <div
                                                    class="tve_lg_input_container tve_submit_container tve_lg_3 tve_lg_submit">
                                                    <button type="Submit">SEND ME THE FREE REPORT NOW</button>
                                                </div>
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
    <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="5">
        <div class="tve_cb tve_cb5 tve_teal" style="margin-top: -55px;">
            <div class="tve_cb_cnt">
                <div class="thrv_wrapper thrv_content_container_shortcode">
                    <div class="tve_clear"></div>
                    <div class="tve_center tve_content_inner" style="width: 945px;min-width:50px; min-height: 2em;">
                        <div class="thrv_wrapper thrv_columns tve_clearfix" style="margin-top: 0;margin-bottom: 0;">
                            <div class="tve_colm tve_oth">
                                <div style="width: 178px;margin-top: 0;"
                                     class="thrv_wrapper tve_image_caption aligncenter img_style_circle">
                                        <span class="tve_image_frame">
                                            <img class="tve_image"
                                                 src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/shane_melaugh2.jpg' ?>"
                                                 style="width: 178px"/>
                                        </span>
                                </div>
                                <h2 class="tve_p_center rft"
                                    style="color: #56a390; font-size: 35px;margin-top: 0;margin-bottom: 0;">
                                    Shane Melaugh
                                </h2>
                            </div>
                            <div class="tve_colm tve_tth tve_lst">
                                <p style="color: #000; font-size: 22px;margin-top: 0;margin-bottom: 40px;">
                                    Use this space to briefly introduce the author/owner of the website. This homepage
                                    template is mainly focused on the offer, but a brief “about me” section helps give a
                                    bit of a personal touch.
                                </p>

                                <p style="color: #000; font-size: 22px;margin-top: 0;margin-bottom: 30px;">
                                    Primarily, you should write about things that make the author worth listening to and
                                    reinforce the idea that the free offer on this page is worth signing up for.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="tve_clear"></div>
                </div>
            </div>
        </div>
    </div>
    <div class="thrv_wrapper thrv_button_shortcode tve_centerBtn"
         data-tve-style="1">
        <div class="tve_btn tve_teal tve_normalBtn tve_btn5 tve_nb">
            <a class="tve_btnLink" href="">
                <span class="tve_left tve_btn_im">
                    <i class="tve_sc_icon offerfocused-icon-arrow" data-tve-icon="offerfocused-icon-arrow"
                       style="background-image: none;"></i>
                </span>
                <span class="tve_btn_txt">Read the Blog</span>
            </a>
        </div>
    </div>
</div>

<div class="tve_lp_footer tve_empty_dropzone tve_content_width">
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1">
        <div class="out" style="background-color: #d2d2d2;">
            <div class="in lightSec">
                <div class="cck clearfix">
                    <div class="thrv_wrapper thrv_columns" style="margin-top: 0;margin-bottom: 0;">
                        <div class="tve_colm tve_twc">
                            <p style="color: #3d3a3a; font-size: 20px;margin-top: 0;margin-bottom: 0;">
                                Copyright {tcb_current_year} by Company Name
                            </p>
                        </div>
                        <div class="tve_colm tve_twc tve_lst">
                            <p style="color: #56a390; font-size: 20px;margin-top: 0;margin-bottom: 0;" class="tve_p_right">
                                <a href="#">Disclaimer</a> - <a href="#">Contact</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>