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

<div class="tve_lp_header tve_empty_dropzone tve_drop_constraint" data-forbid=".thrv_page_section,.sc_page_section">
    <div style="width: 120px;" class="thrv_wrapper tve_image_caption aligncenter">
        <span class="tve_image_frame">
            <img class="tve_image" src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/foundation_logo.png' ?>"
                 style="width: 120px"/>
        </span>
    </div>
</div>

<div class="tve_lp_content tve_editor_main_content tve_empty_dropzone thrv_wrapper tve_no_drag">
    <div class="thrv_wrapper thrv_content_container_shortcode">
        <div class="tve_clear"></div>
        <div class="tve_center tve_content_inner" style="width: 920px;min-width:50px; min-height: 2em;">
            <div class="thrv_wrapper thrv_columns tve_clearfix">
                <div class="tve_colm tve_tth">
                    <h1 style="color: #333; font-size: 56px;"><span class="bold_text">Free</span> Mini-Coaching</h1>
                    <h2 style="color: #333; font-size: 42px; margin-bottom: 30px;">I Will Make Your Website More Profitable</h2>
                    <p style="color: #333; font-size: 20px;">
                        Hi, I'm Barton E. Clark and I've helped dozens of clients improve their websites
                        and get more customers with just a few simple tweaks. And I can do the
                        same for you. Click the button below to start a completely free
                        mini-coaching session with me.
                    </p>
                    <ul class="thrv_wrapper">
                        <li class="" style="font-size: 18px;">Lorem ipsum dolor feature one</li>
                        <li class="" style="font-size: 18px;">At percipit mandamus salutandi cum feature two</li>
                    </ul>
                    <div class="thrv_wrapper thrv_button_shortcode tve_arrow_button" data-tve-style="1">
                        <div class="tve_btn tve_btn3 tve_nb tve_teal tve_bigBtn">
                            <a class="tve_btnLink tve_evt_manager_listen" href="" data-tcb-events="__TCB_EVENT_<?php echo htmlentities(json_encode($events_config)) ?>_TNEVE_BCT__">
                                <span class="tve_left tve_btn_im">
                                    <i></i>
                                </span>
                                <span class="tve_btn_txt">Start the Mini-Coaching Now!</span>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="tve_colm tve_oth tve_lst">
                    <div style="width: 371px;" class="thrv_wrapper tve_image_caption aligncenter">
                        <span class="tve_image_frame">
                            <img class="tve_image"
                                 src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/foundation_guy.jpg' ?>"
                                 style="width: 371px"/>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="tve_clear"></div>
    </div>
</div>

<div class="tve_lp_footer tve_empty_dropzone tve_drop_constraint" data-forbid=".thrv_page_section,.sc_page_section">
    <p class="tve_p_center" style="color: #333333;"><a href="">&copy; 2014 ACME Inc</a> | <a href="">Disclaimer</a></p>
</div>