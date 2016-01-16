<?php
$config = array(
    'email' => 'Please enter a valid email address',
    'phone' => 'Please enter a valid phone number',
    'required' => 'Name and Email fields are required'
)
/* always include all elements inside a thrv-leads-slide-in element */
?>
<div class="thrv-leads-slide-in tve_no_drag tve_no_icons tve_element_hover thrv_wrapper tve_editor_main_content tve_four_set_v2 tve_brdr_solid" style="background-image: url(<?php echo TVE_LEADS_URL . 'editor-templates/_form_css/images/set4_slidein_bg.jpg' ?>);">
    <div class="thrv_wrapper thrv_content_container_shortcode">
        <div class="tve_clear"></div>
        <div class="tve_center tve_content_inner" style="width: 300px;min-width:50px; min-height: 2em;">
            <h2 style="color: #492c3b; font-size: 30px;margin-top: 0;margin-bottom: 340px;" class="tve_p_center tve_set4_slidein_h2">Sign up to Win a royal London experience worth up to $ 12,000</h2>
        </div>
        <div class="tve_clear"></div>
    </div>

    <div data-tve-style="1" class="thrv_wrapper thrv_lead_generation tve_clearfix tve_red tve_draggable thrv_lead_generation_vertical tve_2" data-inputs-count="2" draggable="true" style="margin-top: 10px;margin-bottom: 10px;">
        <div style="display: none;" class="thrv_lead_generation_code"></div>
        <input type="hidden" class="tve-lg-err-msg" value="<?php echo htmlspecialchars(json_encode($config)) ?>" />
        <div class="thrv_lead_generation_container tve_clearfix">
            <div class="tve_lead_generated_inputs_container tve_clearfix">
                <div class="tve_lead_fields_overlay"></div>
                <div class="tve_lg_input_container  tve_lg_2 tve_lg_input">
                    <input type="text" name="email" value="" data-placeholder="Email Address" placeholder="Email Address">
                </div>
                <div class="tve_lg_input_container tve_submit_container tve_lg_2 tve_lg_submit">
                    <button type="Submit">SIGN UP</button>
                </div>
            </div>
        </div>
    </div>

    <a href="javascript:void(0)" class="tve-leads-close" title="<?php echo __('Close', 'thrive-leads') ?>">x</a>
</div>