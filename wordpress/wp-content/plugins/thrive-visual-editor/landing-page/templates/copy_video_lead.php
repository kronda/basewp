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
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1" style="margin-bottom: 20%">
        <div class="out" style="background-color: #d4343e;">
            <div class="in lightSec">
                <div class="cck tve_clearfix">
                    <h1 class="tve_p_center" style="color: #fff; font-size: 56px; font-weight: 100; margin-top: 50px;">
                        Discover the <span class="bold_text">Fastest Way</span> to Create <span class="bold_text">Landing Pages</span>
                        in WordPress
                    </h1>

                    <div class="thrv_wrapper thrv_content_container_shortcode">
                        <div class="tve_clear"></div>
                        <div class="tve_center tve_content_inner" style="width: 800px;min-width:50px; min-height: 2em;margin-bottom: -25%;">
                            <div class="thrv_responsive_video thrv_wrapper" data-type="youtube" data-rel="0" data-controls="0" data-showinfo="0"
                                 data-url="https://www.youtube.com/watch?v=sYMqHaWErZY&amp;list=UUuL6ZEN9ZF-8C5s8HlcdoyQ" data-embeded-url="//www.youtube.com/embed/sYMqHaWErZY"
                                 data-modestbranding="1">
                                <div style="display: block;" class="tve_responsive_video_container">
                                    <div class="video_overlay"></div>
                                    <iframe frameborder="0" allowfullscreen=""
                                            src="//www.youtube.com/embed/sYMqHaWErZY?rel=0&amp;modestbranding=1&amp;controls=0&amp;showinfo=0&amp;autoplay=0&amp;fs=1&amp;wmode=transparent"></iframe>
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
        <div class="tve_center tve_content_inner"
             style="width: 800px;min-width:50px; min-height: 2em;">
            <div class="thrv_wrapper thrv_columns">
                <div class="tve_colm tve_twc">
                    <div class="thrv_wrapper thrv_columns tve_clearfix">
                        <div class="tve_colm tve_tfo tve_df ">
                            <h2 style="color: #000; font-size: 30px; margin-top: 75px;">What You’ll Get When You <span class="bold_text">Sign Up Today:</span></h2>
                        </div>
                        <div class="tve_colm  tve_foc tve_ofo tve_df tve_lst">
                            <div style="width: 45px;" class="thrv_wrapper tve_image_caption aligncenter">
                                <span class="tve_image_frame">
                                    <img class="tve_image copy_video_arrow" src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/copy_arrow_right.png' ?>" style="width: 45px"/>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="tve_colm tve_twc tve_lst">
                    <ol class="thrv_wrapper" style="margin-top: 50px;">
                        <li style="font-size: 18px; margin-bottom: 30px;">Gorgeous, pre-built landing page templates, optimized for conversions.</li>
                        <li style="font-size: 18px; margin-bottom: 30px;">A fast, easy-to-use and intuitive front-end editor, to customize the pages.</li>
                        <li style="font-size: 18px; margin-bottom: 30px;">100% flexibilty, custom layouts, custom colors and complete design freedom.</li>
                    </ol>
                </div>
            </div>
            <div class="thrv_wrapper thrv_button_shortcode tve_fullwidthBtn" data-tve-style="1">
                <div class="tve_btn tve_btn7 tve_green tve_bigBtn tve_nb" style="margin-bottom: 30px;">
                    <a class="tve_btnLink tve_evt_manager_listen" href="" data-tcb-events="__TCB_EVENT_<?php echo htmlentities(json_encode($events_config)) ?>_TNEVE_BCT__">
                        <span class="tve_left tve_btn_im">
                            <i></i>
                        </span>
                        <span class="tve_btn_txt">Sign Up Today!</span>
                    </a>
                </div>
            </div>
        </div>
        <div class="tve_clear"></div>
    </div>

</div>


<div class="tve_lp_footer tve_empty_dropzone">
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1">
        <div class="out" style="background-color: #333333">
            <div class="in">
                <div class="cck tve_clearfix">
                    <p class="tve_p_center" style="margin: 2px 0; padding: 0; font-size: 16px">
                        &copy; 2014 by ACME Inc. - <a href="#"><span class="underline_text">Disclaimer</span></a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>