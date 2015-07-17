<?php
$config = array(
    'email' => 'Please enter a valid email address',
    'phone' => 'Please enter a valid phone number',
    'required' => 'Name and Email fields are required'
)
/* always include all elements inside a thrv-leads-slide-in element */
?>
<div class="thrv-leads-slide-in tve_no_drag tve_no_icons tve_element_hover thrv_wrapper tve_editor_main_content tve_three_set tve_brdr_solid">
    <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="5">
        <div class="tve_cb tve_cb5 tve_teal">
            <div class="tve_cb_cnt">
                <h2 style="color: #333; font-size: 44px;line-height: 44px;margin-top: 0;margin-bottom: 30px;">
                    Market Research in a Week: Teach Yourself
                </h2>
                <div class="thrv_wrapper thrv_columns" style="margin-bottom: 0;">
                    <div class="tve_colm tve_twc">
                        <div style="width: 290px; margin-top: 20px;margin-bottom: 0px;" class="thrv_wrapper tve_image_caption aligncenter img_style_lifted_style2">
                             <span class="tve_image_frame">
                                <img class="tve_image"
                                     src="<?php echo TVE_LEADS_URL . 'editor-templates/_form_css/images/set3_play_image.jpg' ?>"
                                     style="width: 290px"/>
                            </span>
                        </div>
                    </div>
                    <div class="tve_colm tve_twc tve_lst">
                        <h6 style="color: #333; font-size: 18px;margin-top: 14px;margin-bottom: 7px;">FREE 3 Part Video Course Includes:</h6>
                        <div class="thrv_wrapper thrv_bullets_shortcode">
                            <ul class="tve_ul tve_ul1 tve_red">
                                <li style="color: #333333; font-size: 18px;margin-top: 0;margin-bottom: 20px;">5 Steps To Create A Powerful, Profitable Presentation</li>
                                <li style="color: #333333; font-size: 18px;margin-top: 0;margin-bottom: 20px;">Social Media Marketing Tips</li>
                                <li style="color: #333333; font-size: 18px;margin-top: 0;margin-bottom: 20px;">12 Things Every Business Needs to Know About Digital Marketing</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="6">
        <div class="tve_cb tve_cb6 tve_red" style="margin-top: 20px;padding: 10px 5px;">
            <div class="tve_cb_cnt">
                <div data-tve-style="1"
                     class="thrv_wrapper thrv_lead_generation tve_clearfix tve_teal tve_draggable thrv_lead_generation_vertical tve_2" data-inputs-count="2"
                     draggable="true" style="margin-top: 0;margin-bottom: 0;">
                    <div style="display: none;" class="thrv_lead_generation_code"></div>
                    <input type="hidden" class="tve-lg-err-msg" value="<?php echo htmlspecialchars(json_encode($config)) ?>" />
                    <div class="thrv_lead_generation_container tve_clearfix">
                        <div class="tve_lead_generated_inputs_container tve_clearfix">
                            <div class="tve_lead_fields_overlay"></div>
                            <div class="tve_lg_input_container tve_lg_2 tve_lg_input">
                                <input type="text" name="email" value="" data-placeholder="Enter your email..."
                                       placeholder="Enter your email...">
                            </div>
                            <div class="tve_lg_input_container tve_submit_container tve_lg_2 tve_lg_submit">
                                <button type="Submit">SUBSCRIBE</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <a href="javascript:void(0)" class="tve-leads-close" title="<?php echo __('Close', 'thrive-leads') ?>">x</a>
</div>