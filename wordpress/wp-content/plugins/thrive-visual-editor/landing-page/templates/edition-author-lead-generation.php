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
<div class="tve_lp_header tve_empty_dropzone">
</div>
<div class="tve_lp_content_bg thrv_wrapper tve_no_drag tve_lp_content" style="margin: 0">
    <div class="tve_editor_main_content tve_empty_dropzone tve_content_width">
        <div class="thrv_wrapper thrv_page_section" data-tve-style="1" style="" id="edition-page-section">
            <div class="out">
                <div class="in darkSec pddbg"
                     style="background-image: url('<?php echo TVE_LANDING_PAGE_TEMPLATE . "/css/images/edition-page-section-bg.png" ?>');
                         box-shadow: none; box-sizing: border-box;max-width: 100vw;" data-width="1600"
                     data-height="596">
                    <div class="cck clearfix">
                        <div class="thrv_wrapper thrv_columns tve_clearfix">
                            <div class="tve_colm tve_foc tve_df tve_ofo "><p>&nbsp;</p></div>
                            <div class="tve_colm tve_tfo tve_df tve_lst">
                                <h1 class="rft"
                                    style="color: #333745; font-size: 59px;margin-top: 20px;margin-bottom: 40px;">
                                    <font color="#58ae7c"><span class="bold_text">Jane Doe:</span></font> The Ultimate
                                    Guide to <span class="bold_text">Landing Page Optimization</span>
                                </h1>

                                <div class="thrv_wrapper thrv_content_container_shortcode">
                                    <div class="tve_clear"></div>
                                    <div class="tve_center tve_content_inner"
                                         style="width: 590px;min-width:50px; min-height: 2em;">
                                        <blockquote>
                                            “ Using the right kind of landing pages in the right way can be transformative for any business. ”
                                        </blockquote>
                                        <div style="width: 100px;" class="thrv_wrapper tve_image_caption aligncenter">
                                            <span class="tve_image_frame">
                                                <img class="tve_image"
                                                     src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/edition-signature.png' ?>"
                                                     style="width: 100px;"/>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="tve_clear"></div>
                                </div>
                                <div class="thrv_wrapper thrv_columns tve_clearfix"
                                     style="margin-bottom: 0;margin-top: 0;">
                                    <div class="tve_colm tve_oth">
                                        <div style="width: 285px;margin-bottom: -105px;margin-top: 0;"
                                             class="thrv_wrapper tve_image_caption aligncenter">
                                            <span class="tve_image_frame">
                                                <img class="tve_image"
                                                     src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/edition-product.png' ?>"
                                                     style="width: 285px;"/>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="tve_colm tve_tth tve_lst">
                                        <ul class="thrv_wrapper">
                                            <li><span class="bold_text">Build what matters:</span>  learn a simple system for creating effective pages that captivate and sell.
                                            </li>
                                            <li><span class="bold_text">Test your way to the top:</span>  how to make use of free testing tools to find the optimal design and content for each page.
                                            </li>
                                        </ul>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="thrv_wrapper thrv_content_container_shortcode">
            <div class="tve_clear"></div>
            <div class="tve_center tve_content_inner"
                 style="width: 840px;min-width:50px; min-height: 2em;margin-top: 50px;">
                <div class="thrv_wrapper thrv_columns">
                    <div class="tve_colm tve_twc">
                        <div class="thrv_wrapper thrv_button_shortcode tve_centerBtn" data-tve-style="1">
                            <div class="tve_btn tve_btn1 tve_nb tve_blue tve_normalBtn">
                                <a class="tve_btnLink tve_evt_manager_listen" href="" data-tcb-events="__TCB_EVENT_<?php echo htmlentities(json_encode($events_config)) ?>_TNEVE_BCT__">
                                    <span class="tve_left tve_btn_im">
                                        <i></i>
                                        <span class="tve_btn_divider"></span>
                                    </span>
                                    <span class="tve_btn_txt">DOWNLOAD FREE SAMPLE</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="tve_colm tve_twc tve_lst">
                        <div class="thrv_wrapper thrv_button_shortcode tve_centerBtn" data-tve-style="1">
                            <div class="tve_btn tve_btn1 tve_nb tve_orange tve_normalBtn">
                                <a href="" class="tve_btnLink">
                                    <span class="tve_left tve_btn_im">
                                        <i></i>
                                        <span class="tve_btn_divider"></span>
                                    </span>
                                    <span class="tve_btn_txt">BUY FROM AMAZON.COM</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tve_clear"></div>
        </div>
        <div class="thrv_wrapper thrv_page_section" data-tve-style="1" style="" id="edition-page-section-shadow">
            <div class="out" style="background: #fff;">
                <div class="in darkSec"
                     style="">
                    <div class="cck clearfix">
                        <h3 style="color: #333; font-size: 36px;margin-top: 50px;margin-bottom: 60px;"
                            class="tve_p_center rft"><span class="bold_text">In this book</span> you will discover:</h3>

                        <div class="thrv_wrapper thrv_columns">
                            <div class="tve_colm tve_twc">
                                <div class="thrv_wrapper thrv_columns tve_clearfix" style="margin-bottom: 40px;">
                                    <div class="tve_colm tve_foc tve_df tve_ofo">
                                        <div style="" class="thrv_wrapper thrv_icon aligncenter">
                                            <span style="color: #58ae7c; font-size: 60px;"
                                                  class="tve_sc_icon edition-icon-gear tve_green"
                                                  data-tve-icon="edition-icon-gear"></span>
                                        </div>
                                    </div>
                                    <div class="tve_colm tve_tfo tve_df tve_lst">
                                        <h4 style="color: #363a46; font-size: 22px;margin-top: 0;margin-bottom: 10px;">
                                            The Ultimate Toolkit
                                        </h4>

                                        <p style="color: #666; font-size: 17px;margin-top: 0;margin-bottom: 20px;">
                                            Aenean sollicitudin, lorem quis bibendum auctor, nisi elit consequat ipsum,
                                            nec sagittis sem
                                            nibh id elit.
                                        </p>
                                    </div>
                                </div>
                                <div class="thrv_wrapper thrv_columns tve_clearfix">
                                    <div class="tve_colm tve_foc tve_df tve_ofo">
                                        <div style="" class="thrv_wrapper thrv_icon aligncenter">
                                            <span style="color: #58ae7c; font-size: 60px;"
                                                  class="tve_sc_icon edition-icon-monitor tve_green"
                                                  data-tve-icon="edition-icon-monitor"></span>
                                        </div>
                                    </div>
                                    <div class="tve_colm tve_tfo tve_df tve_lst">
                                        <h4 style="color: #363a46; font-size: 22px;margin-top: 0;margin-bottom: 10px;">
                                            Make Your Pages Work on Any Device
                                        </h4>

                                        <p style="color: #666; font-size: 17px;margin-top: 0;margin-bottom: 20px;">
                                            Aenean sollicitudin, lorem quis bibendum auctor, nisi elit consequat ipsum,
                                            nec sagittis sem
                                            nibh id elit.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="tve_colm tve_twc tve_lst">
                                <div class="thrv_wrapper thrv_columns tve_clearfix" style="margin-bottom: 40px;">
                                    <div class="tve_colm tve_foc tve_df tve_ofo">
                                        <div style="" class="thrv_wrapper thrv_icon aligncenter">
                                            <span style="color: #58ae7c; font-size: 60px;"
                                                  class="tve_sc_icon edition-icon-globe tve_green"
                                                  data-tve-icon="edition-icon-globe"></span>
                                        </div>
                                    </div>
                                    <div class="tve_colm tve_tfo tve_df tve_lst">
                                        <h4 style="color: #363a46; font-size: 22px;margin-top: 0;margin-bottom: 10px;">
                                            Practical, Step-by-Step Recipes
                                        </h4>

                                        <p style="color: #666; font-size: 17px;margin-top: 0;margin-bottom: 20px;">
                                            Aenean sollicitudin, lorem quis bibendum auctor, nisi elit consequat ipsum,
                                            nec sagittis sem
                                            nibh id elit.
                                        </p>
                                    </div>
                                </div>
                                <div class="thrv_wrapper thrv_columns tve_clearfix">
                                    <div class="tve_colm tve_foc tve_df tve_ofo">
                                        <div style="" class="thrv_wrapper thrv_icon aligncenter">
                                            <span style="color: #58ae7c; font-size: 60px;"
                                                  class="tve_sc_icon edition-icon-lock tve_green"
                                                  data-tve-icon="edition-icon-lock"></span>
                                        </div>
                                    </div>
                                    <div class="tve_colm tve_tfo tve_df tve_lst">
                                        <h4 style="color: #363a46; font-size: 22px;margin-top: 0;margin-bottom: 10px;">
                                            Unlock Your Market's Secrets
                                        </h4>

                                        <p style="color: #666; font-size: 17px;margin-top: 0;margin-bottom: 20px;">
                                            Aenean sollicitudin, lorem quis bibendum auctor, nisi elit consequat ipsum,
                                            nec sagittis sem
                                            nibh id elit.
                                        </p>
                                    </div>
                                </div>
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
        <div class="out" style="background: #2c2d32;">
            <div class="in darkSec">
                <div class="cck tve_clearfix">
                    <div class="thrv_wrapper thrv_columns">
                        <div class="tve_colm tve_twc">
                            <p class="tve_p_left"
                               style="margin: 0; padding: 0; color: #6e757c; font-size: 16px;">
                                &copy; {tcb_current_year} Thrive  Landing Pages. All rights Reserved
                            </p>
                        </div>
                        <div class="tve_colm tve_twc tve_lst">
                            <p class="tve_p_right"
                               style="margin: 0; padding: 0; color: #6e757c; font-size: 16px;">
                                <a href="#">Disclaimer</a></p>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>