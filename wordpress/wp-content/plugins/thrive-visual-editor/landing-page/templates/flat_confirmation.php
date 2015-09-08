<?php $notification_events = array(
    array(
        't' => 'tve-viewport',
        'a' => 'thrive_animation',
        'config' => array(
            'anim' => 'appear'
        )
    )
) ?>
<div class="tve_lp_header tve_empty_dropzone">
    <div class="thrv_wrapper thrv_page_section" data-tve-style="1">
        <div class="out" style="background-color: #e9e9e9">
            <div class="in darkSec">
                <div class="cck clearfix tve_content_width">
                    <div style="width: 132px; margin: 0;" class="thrv_wrapper tve_image_caption alignleft">
                        <span class="tve_image_frame">
                            <a href="">
                                <img class="tve_image" src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/lead_generation_flat_logo.png' ?>" style="width: 132px"/>
                            </a>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="tve_lp_content tve_editor_main_content tve_empty_dropzone tve_content_width">

    <div data-tve-style="6" class="thrv_wrapper thrv_contentbox_shortcode tve_ea_thrive_animation tve_evt_manager_listen tve_et_tve-viewport" data-tcb-events="__TCB_EVENT_<?php echo htmlspecialchars(json_encode($notification_events)) ?>_TNEVE_BCT__">
        <div class="tve_cb tve_cb6 tve_green" style="border-radius: 10px; margin-top: 20px;">
            <div class="tve_cb_cnt">
                <div style="font-size: 30px; color: #fff;" class="thrv_wrapper thrv_icon alignleft">
                    <span style="margin: 0" class="tve_sc_icon flat-confirmation-icon-checkmark-circle" data-tve-icon="flat-confirmation-icon-checkmark-circle"></span>
                </div>
                <p style="color: #fff; margin: 0; padding: 0 0 0 40px; font-size: 24px">You have successfully signed up! Please follow the steps below, to complete the process:</p>
            </div>
        </div>
    </div>

    <div class="thrv_wrapper thrv_columns tve_clearfix">

        <div class="tve_colm tve_oth">
            <div data-tve-style="6" class="thrv_wrapper thrv_contentbox_shortcode">
                <div class="tve_cb tve_cb6 tve_white" style="border-radius: 10px; padding-bottom: 20px;">
                    <div class="tve_cb_cnt">
                        <div style="font-size: 110px; color: #305584" class="thrv_wrapper thrv_icon aligncenter">
                            <span style="margin: 30px 0 0 0" class="tve_sc_icon flat-confirmation-icon-envelop" data-tve-icon="flat-confirmation-icon-envelop"></span>
                        </div>
                        <p class="tve_p_center" style="margin: 0; padding: 0; font-size: 26px; color: #666;">
                            <span class="underline_text">Step 1:</span></p>

                        <p class="tve_p_center" style="margin: 0 0 20px 0; padding: 0; font-size: 26px; color: #666;">
                            <span class="bold_text">Go to<br />Your Email Inbox</span></p>

                        <p class="tve_p_center" style="margin: 0; padding: 0; font-size: 20px; color: #666;">Go to the inbox<br/>of the email address<br/>you just used to sign up.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="tve_colm tve_oth">
            <div data-tve-style="6" class="thrv_wrapper thrv_contentbox_shortcode">
                <div class="tve_cb tve_cb6 tve_white" style="border-radius: 10px; padding-bottom: 20px;">
                    <div class="tve_cb_cnt">
                        <div style="font-size: 110px; color: #305584" class="thrv_wrapper thrv_icon aligncenter">
                            <span style="margin: 30px 0 0 0" class="tve_sc_icon flat-confirmation-icon-envelop-opened" data-tve-icon="flat-confirmation-icon-envelop-opened"></span>
                        </div>
                        <p class="tve_p_center" style="margin: 0; padding: 0; font-size: 26px; color: #666;">
                            <span class="underline_text">Step 2:</span></p>

                        <p class="tve_p_center" style="margin: 0 0 20px 0; padding: 0; font-size: 26px; color: #666;">
                            <span class="bold_text">Open<br/>the Confirmation Email</span></p>

                        <p class="tve_p_center" style="margin: 0; padding: 0; font-size: 20px; color: #666;">Find the email sent by us. It has the subject line
                            <span class="bold_text">"Subject Line"</span> and is sent from
                            <span class="bold_text">"From Name Here"</span>.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="tve_colm tve_thc tve_lst">
            <div data-tve-style="6" class="thrv_wrapper thrv_contentbox_shortcode">
                <div class="tve_cb tve_cb6 tve_white" style="border-radius: 10px; padding-bottom: 20px;">
                    <div class="tve_cb_cnt">
                        <div style="font-size: 110px; color: #305584" class="thrv_wrapper thrv_icon aligncenter">
                            <span style="margin: 27px 0 0 0" class="tve_sc_icon flat-confirmation-icon-pointer" data-tve-icon="flat-confirmation-icon-pointer"></span>
                        </div>
                        <p class="tve_p_center" style="margin: 0; padding: 0; font-size: 26px; color: #666;">
                            <span class="underline_text">Step 3:</span></p>

                        <p class="tve_p_center" style="margin: 0 0 20px 0; padding: 0; font-size: 27px; color: #666;">
                            <span class="bold_text">Click<br/>the Confirmation Link</span></p>

                        <p class="tve_p_center" style="margin: 0; padding: 0; font-size: 20px; color: #666;">Click on the link inside the email and you will
                            <span class="bold_text">instantly receive access</span> to your free report!</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="tve_lp_footer tve_empty_dropzone">
    <p class="tve_p_center" style="margin: 0; padding: 0; color: #6c6c6c; font-size: 16px">
        &copy; 2014 by ACME Inc. - <a href="#"><span class="underline_text">Disclaimer</span></a>
    </p>
</div>