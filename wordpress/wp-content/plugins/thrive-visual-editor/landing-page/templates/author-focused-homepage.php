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
            <div style="margin-top: 0;margin-bottom: 0;width: 273px;" class="thrv_wrapper tve_image_caption aligncenter">
                        <span class="tve_image_frame">
                            <img class="tve_image"
                                 src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/author-focused-logo.png' ?>"
                                 style="width: 273px"/>
                        </span>
            </div>
        </div>
        <div class="tve_colm tve_tfo tve_df tve_lst">
            <p class="tve_p_right hidden-on-mobile"
               style="color: #666; font-size: 20px;margin-top: 13px;margin-bottom: 0;">
                <a class="tve_evt_manager_listen" href=""
                   data-tcb-events="__TCB_EVENT_<?php echo htmlentities(json_encode($events_config)) ?>_TNEVE_BCT__">&rsaquo; &nbsp;Free Report</a>
                <a href="//imimpact.com" target="_blank">&nbsp;&nbsp;&nbsp;&rsaquo; &nbsp;Blog</a>
            </p>
        </div>
    </div>
</div>
<div class="tve_lp_content tve_editor_main_content tve_empty_dropzone tve_content_width">
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1" id="authorfocused-ps">
        <div class="out" style="background-color: #d21b08;">
            <div class="in lightSec" style="padding-bottom: 0;">
                <div class="cck clearfix">
                    <div class="thrv_wrapper thrv_columns tve_clearfix" style="margin-bottom: 0;">
                        <div class="tve_colm tve_oth">
                            <div style="width: 277px;margin-bottom: -133px;margin-top: -20px;"
                                 class="thrv_wrapper tve_image_caption aligncenter">
                                <span class="tve_image_frame">
                                    <img class="tve_image"
                                         src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/author-focused-guy.png' ?>"
                                         style="width: 277px"/>
                                </span>
                            </div>
                        </div>
                        <div class="tve_colm tve_tth tve_lst">
                            <h4 style="color: #fff; font-size: 24px;margin-top: 0;margin-bottom: 20px;">
                                Get my new tutorial
                            </h4>

                            <h1 style="color: #fff; font-size: 60px;margin-top: 0;margin-bottom: 30px;" class="rft">
                                How to Get More Leads
                                From Your Home Page
                            </h1>

                            <h4 style="color: #fff; font-size: 24px;margin-top: 0;margin-bottom: 0;">
                                Join over 15,000 subscribers to get my step by step guide
                                for improving conversions on your homepage.
                            </h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1">
        <div class="out" style="background-color: #4a4a4a;">
            <div class="in lightSec" style="padding-top: 30px;padding-bottom: 30px;">
                <div class="cck clearfix">
                    <div class="thrv_wrapper thrv_columns tve_clearfix" style="margin-top: 0;margin-bottom: 0;">
                        <div class="tve_colm tve_oth">
                            <div style="width: 56px;margin-right: -10px;margin-top: 20px;margin-bottom: 0;"
                                 class="thrv_wrapper tve_image_caption alignright">
                                <span class="tve_image_frame">
                                    <img class="tve_image authorfocusedarrow"
                                         src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/author-focused-arrow.png' ?>"
                                         style="width: 56px"/>
                                </span>
                            </div>
                        </div>
                        <div class="tve_colm tve_tth tve_lst">
                            <div class="thrv_wrapper thrv_button_shortcode tve_fullwidthBtn"
                                 data-tve-style="1">
                                <div class="tve_btn tve_red tve_normalBtn tve_btn7 tve_nb">
                                    <a class="tve_btnLink tve_evt_manager_listen" href=""
                                       data-tcb-events="__TCB_EVENT_<?php echo htmlentities(json_encode($events_config)) ?>_TNEVE_BCT__">
                                            <span class="tve_left tve_btn_im">
                                                <i></i>
                                            </span>
                                        <span class="tve_btn_txt">Get the 'Ultimate Homepage Recipe' for Free!</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="thrv_wrapper thrv_icon aligncenter" style="margin-top: -19px;">
        <span style="font-size: 45px;"
              class="tve_black tve_sc_icon authorfocused-icon-arrow"
              data-tve-icon="authorfocused-icon-arrow"></span>
    </div>
    <div class="thrv_wrapper thrv_content_container_shortcode">
        <div class="tve_clear"></div>
        <div class="tve_center tve_content_inner" style="width: 905px;min-width:50px; min-height: 2em;">
            <div class="thrv_wrapper thrv_testimonial_shortcode" data-tve-style="4">
                <div class="tve_ts tve_ts4 tve_red">
                    <div class="tve_ts_o">
                        <div class="tve_ts_imc">
                            <img src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/shanemelaugh3.jpg' ?>"
                                 alt=""/>
                        </div>
                <span>
                    <b>Shane Melaugh</b>
                </span>
                    </div>
                    <div class="tve_ts_t">
                        <span class="tve_ts_c tve_left"></span>

                        <div class="tve_ts_cn tve_left">
                            <span class="tve_ts_ql"></span>
                            <h4>
                                <span class="bold_text">
                                    Add some social proof here!
                                </span>
                            </h4>

                            <p>”Testimonials are a great conversion element and on this homepage template, you can
                                display one or several of them in this section. The testimonials can be about you, your
                                site or the free product you’re offering on the page.”</p>
                        </div>
                        <div class="tve_clear"></div>
                    </div>
                    <div class="tve_clear"></div>
                </div>
            </div>
            <div class="thrv_wrapper thrv_testimonial_shortcode" data-tve-style="5">
                <div class="tve_ts tve_ts9 tve_red">
                    <div class="tve_ts_t">
                        <span class="tve_ts_c tve_right"></span>

                        <div class="tve_ts_cn tve_right">
                            <span class="tve_ts_ql"></span>
                            <h4>
                                <span class="bold_text">
                                    Use the Testimonial Heading Wisely
                                </span>
                            </h4>

                            <p>
                                “Each testimonial has a heading, which you can use to highlight the best part of the
                                testimonial. This is great to get the attention of those readers who are simply skimming
                                through your page.”
                            </p>
                        </div>
                        <div class="tve_clear"></div>
                    </div>
                    <div class="tve_ts_o">
                        <div class="tve_ts_imc">
                            <img src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/girl-reversed.jpg' ?>"
                                 alt=""/>
                        </div>
                <span>
                    <b>Jane Doe</b>
                </span>
                    </div>
                    <div class="tve_clear"></div>
                </div>
            </div>
            <div class="thrv_wrapper thrv_testimonial_shortcode" data-tve-style="4" style="margin-bottom: 80px;">
                <div class="tve_ts tve_ts4 tve_red">
                    <div class="tve_ts_o">
                        <div class="tve_ts_imc">
                            <img src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/shanemelaugh3.jpg' ?>"
                                 alt=""/>
                        </div>
                <span>
                    <b>Shane Melaugh</b>
                </span>
                    </div>
                    <div class="tve_ts_t">
                        <span class="tve_ts_c tve_left"></span>

                        <div class="tve_ts_cn tve_left">
                            <span class="tve_ts_ql"></span>
                            <h4>
                                <span class="bold_text">
                                    The Best Free Report I’ve Ever Downloaded...
                                </span>
                            </h4>

                            <p>
                                “Three testimonials is a good number for this layout, but remember that you can easily
                                delete or duplicate the testimonials to display as many or as few of them as you like.”
                            </p>
                        </div>
                        <div class="tve_clear"></div>
                    </div>
                    <div class="tve_clear"></div>
                </div>
            </div>
            <h3 style="color: #333; font-size: 30px;margin-top: 0;margin-bottom: 20px;" class="rft">
                About the Author
            </h3>
        </div>
        <div class="tve_clear"></div>
    </div>
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1">
        <div class="out" style="background-color: #4a4a4a;">
            <div class="in lightSec">
                <div class="cck clearfix">
                    <div class="thrv_wrapper thrv_content_container_shortcode">
                        <div class="tve_clear"></div>
                        <div class="tve_center tve_content_inner" style="width: 905px;min-width:50px; min-height: 2em;">
                            <div class="thrv_wrapper thrv_columns tve_clearfix"
                                 style="margin-top: 10px;margin-bottom: 10px;">
                                <div class="tve_colm tve_oth">
                                    <div style="margin-top: 0;margin-bottom: 0;width: 213px;"
                                         class="thrv_wrapper tve_image_caption img_style_rounded_corners">
                                        <span class="tve_image_frame">
                                            <img class="tve_image"
                                                 src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/author-focus.jpg' ?>"
                                                 style="width: 213px"/>
                                        </span>
                                    </div>
                                </div>
                                <div class="tve_colm tve_tth tve_lst">
                                    <p style="color: #fff; font-size: 19px;line-height: 33px;margin-top: 0;margin-bottom: 0;">
                                        In this section, display a more personal or candid photo of yourself and add
                                        some text about who you are. This homepage template is “author-focused” and it’s
                                        all about building trust and rapport. This is when you aren’t just selling a
                                        product, you’re also doing personal branding and you want to connect with your
                                        audience on a more personal level. You should keep this section short, but you
                                        can link to a more extensive bio at the end.
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="tve_clear"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="thrv_wrapper thrv_columns tve_clearfix" style="margin-top: 30px;">
        <div class="tve_colm tve_tth">
            <div class="thrv_wrapper thrv_button_shortcode tve_fullwidthBtn"
                 data-tve-style="1">
                <div class="tve_btn tve_red tve_normalBtn tve_btn7 tve_nb">
                    <a class="tve_btnLink tve_evt_manager_listen" href=""
                       data-tcb-events="__TCB_EVENT_<?php echo htmlentities(json_encode($events_config)) ?>_TNEVE_BCT__">
                        <span class="tve_left tve_btn_im">
                            <i></i>
                        </span>
                        <span class="tve_btn_txt">Get the 'Ultimate Homepage Recipe' for Free!</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="tve_colm tve_oth tve_lst">
            <div class="thrv_wrapper thrv_button_shortcode tve_fullwidthBtn"
                 data-tve-style="1">
                <div class="tve_btn tve_black tve_normalBtn tve_btn7 tve_nb">
                    <a class="tve_btnLink" href="">
                        <span class="tve_left tve_btn_im">
                            <i></i>
                        </span>
                        <span class="tve_btn_txt">Go to the Blog</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="tve_lp_footer tve_empty_dropzone tve_content_width">
    <div class="thrv_wrapper thrv_columns">
        <div class="tve_colm tve_twc">
            <p style="color: #3d3a3a; font-size: 20px;margin-top: 0;margin-bottom: 0;">
                Copyright {tcb_current_year} by Company Name
            </p>
        </div>
        <div class="tve_colm tve_twc tve_lst">
            <p style="font-size: 20px;margin-top: 0;margin-bottom: 0;" class="tve_p_right">
                <a href="#">Disclaimer</a> - <a href="#">Contact</a>
            </p>
        </div>
    </div>
</div>