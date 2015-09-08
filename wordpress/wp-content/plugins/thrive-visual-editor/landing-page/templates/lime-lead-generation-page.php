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
                    <div class="thrv_wrapper thrv_content_container_shortcode">
                        <div class="tve_clear"></div>
                        <div class="tve_center tve_content_inner" style="width: 850px;min-width:50px; min-height: 2em;">
                            <div class="thrv_wrapper thrv_columns tve_clearfix" style="margin-bottom: 0;">
                                <div class="tve_colm tve_twc">
                                    <div style="width: 384px;margin-top: 4px;margin-bottom: -282px" class="thrv_wrapper tve_image_caption">
                                        <span class="tve_image_frame">
                                            <a href="">
                                                <img class="tve_image"
                                                     src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/lime-leadgen-big-book.png' ?>"
                                                     style=""/>
                                            </a>
                                        </span>
                                    </div>
                                </div>
                                <div class="tve_colm tve_twc tve_lst">
                                    <h2 style="color: #fff; font-size: 36px;line-height: 45px;margin-top: 10px;margin-bottom: 25px;">
                                        Web Ui/Ux Design patterns-2014
                                    </h2>
                                    <p style="color: #fff; font-size: 15px;line-height: 30px;margin-top: 0;margin-bottom: 0;;">
                                        Amazon, Kickstarter, AirBnB, Quora, LinkedIn, Eventbrite,
                                        Asana, Mailchimp - what web UI design patterns do they use
                                        and why? It’s all explained in this ebook.
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
    <div class="thrv_wrapper thrv_content_container_shortcode">
        <div class="tve_clear"></div>
        <div class="tve_center tve_content_inner" style="width: 850px;min-width:50px; min-height: 2em;">
            <div class="thrv_wrapper thrv_columns">
                <div class="tve_colm tve_twc"><p>&nbsp;</p></div>
                <div class="tve_colm tve_twc tve_lst">
                    <p style="color: #000; font-size: 24px;margin-top: 20px;margin-bottom: 20px;">
                        Sign up to download this <font color="#6dbb67">FREE</font> Ebook
                    </p>
                    <p style="color: #898989; font-size: 14px;margin-top: 0;margin-bottom: 0;">You can download 5 magazines for free.</p>
                    <div data-tve-style="1" class="thrv_wrapper thrv_button_shortcode tve_fullwidthBtn">
                        <div class="tve_btn tve_green tve_bigBtn tve_btn8 tve_nb">
                            <a class="tve_btnLink tve_evt_manager_listen" href="" data-tcb-events="__TCB_EVENT_<?php echo htmlentities(json_encode($events_config)) ?>_TNEVE_BCT__">
                                <span class="tve_left tve_btn_im">
                                    <i></i>
                                </span>
                                <span class="tve_btn_txt">Sign Up & download Ebook</span>
                            </a>
                        </div>
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