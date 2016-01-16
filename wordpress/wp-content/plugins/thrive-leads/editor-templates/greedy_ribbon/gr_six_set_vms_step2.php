<?php
$config = array(
    'email' => 'Please enter a valid email address',
    'phone' => 'Please enter a valid phone number',
    'required' => 'Name and Email fields are required'
)
/* always include all elements inside a thrv-leads-slide-in element */
?>
<div class="thrv-greedy-ribbon tve_no_drag tve_no_icons tve_element_hover thrv_wrapper tve_gr_six_set tve_white">
    <div class="tve-greedy-ribbon-content tve_editor_main_content">
        <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="5">
            <div class="tve_cb tve_cb5 tve_purple">
                <div class="tve_cb_cnt">
                    <div style="margin-top: 80px;margin-bottom: -105px;width: 210px;" class="thrv_wrapper tve_image_caption aligncenter img_style_circle gr_six_set_image">
                         <span class="tve_image_frame">
                            <img class="tve_image"
                                 src="<?php echo TVE_LEADS_URL . 'editor-templates/_form_css/images/gr_set_six_image.jpg' ?>"
                                 style="width: 210px"/>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="thrv_wrapper thrv_content_container_shortcode">
            <div class="tve_clear"></div>
            <div class="tve_center tve_content_inner" style="width: 850px;min-width:50px; min-height: 2em;margin-top: 120px;">
                <h2 class="tve_p_center rft" style="color: #999999; font-size: 65px;margin-top: 0;margin-bottom: 40px;">
                    <font color="#ff5980">Sign up below</font> to get access
                    to the <font color="#ff5980">FREE Product</font>
                </h2>
            </div>
            <div class="tve_clear"></div>
        </div>
        <div class="thrv_wrapper thrv_content_container_shortcode">
            <div class="tve_clear"></div>
            <div class="tve_center tve_content_inner" style="width: 560px;min-width:50px; min-height: 2em;">
                <div
                    class="thrv_wrapper thrv_lead_generation tve_clearfix thrv_lead_generation_vertical tve_purple tve_3"
                    data-inputs-count="3" data-tve-style="1" style="margin-top: 0; margin-bottom: 20px;">
                    <div class="thrv_lead_generation_code" style="display: none;"></div>
                    <input type="hidden" class="tve-lg-err-msg"
                           value="<?php echo htmlspecialchars(json_encode($config)) ?>"/>

                    <div class="thrv_lead_generation_container tve_clearfix">
                        <div class="tve_lead_generated_inputs_container tve_clearfix">
                            <div class="tve_lead_fields_overlay"></div>
                            <div class=" tve_lg_input_container tve_lg_3 tve_lg_input">
                                <input type="text" data-placeholder="" value="" name="name"
                                       placeholder="Your name"/>
                            </div>
                            <div class=" tve_lg_input_container tve_lg_3 tve_lg_input">
                                <input type="text" data-placeholder="" value="" name="email"
                                       placeholder="Your Email Address"/>
                            </div>
                            <div class="tve_lg_input_container tve_submit_container tve tve_lg_3 tve_lg_submit">
                                <button type="Submit">Show me the Product</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tve_clear"></div>
        </div>

        <div class="thrv_wrapper thrv_icon aligncenter gr-close-button tve_no_drag">
            <span data-tve-icon="gr-six-set-close"
                  class="tve_sc_icon gr-six-set-close tve_white tve_evt_manager_listen tve_et_click"
                  style="font-size: 60px;" data-tcb-events="|close_form|"></span>
        </div>
    </div>
</div>

