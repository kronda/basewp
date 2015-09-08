<?php
$config = array(
    'email' => 'Please enter a valid email address',
    'phone' => 'Please enter a valid phone number',
    'required' => 'Name and Email fields are required'
)
/* always include all elements inside a thrv-leads-widget element */
?>
<div class="thrv-leads-widget thrv_wrapper tve_editor_main_content tve_34_set tve_brdr_dashed">
    <div style="width: 167px;" class="thrv_wrapper tve_image_caption aligncenter">
                 <span class="tve_image_frame">
                    <img class="tve_image"
                         src="<?php echo TVE_LEADS_URL . 'editor-templates/_form_css/images/set_34_icon.png' ?>"
                         style="width: 167px;"/>
                </span>
    </div>
    <h2 style="color: #333333; font-size: 32px; margin-top: 25px;margin-bottom: 15px;" class="rft tve_p_center">
        Download Our <span class="bold_text">Free</span> Report
    </h2>

    <p style="color: #666666; font-size: 20px; margin-top: 0;" class="tve_p_center">
        Aenean sollicitudin, lorem quis bibendum auctor, nisi elit
        ipsum, nec sagittis sem nibh id elit.
    </p>

    <div data-tve-style="1"
         class="thrv_wrapper thrv_lead_generation tve_clearfix tve_orange thrv_lead_generation_vertical tve_2"
         data-inputs-count="2" style="margin-top: 0;margin-bottom: 0;">
        <div style="display: none;" class="thrv_lead_generation_code"></div>
        <input type="hidden" class="tve-lg-err-msg" value="<?php echo htmlspecialchars(json_encode($config)) ?>"/>

        <div class="thrv_lead_generation_container tve_clearfix">
            <div class="tve_lead_generated_inputs_container tve_clearfix">
                <div class="tve_lead_fields_overlay"></div>
                <div class="tve_lg_input_container  tve_lg_2 tve_lg_input">
                    <input type="text" name="name" value="" data-placeholder="email" placeholder="Enter Email">
                </div>
                <div class="tve_lg_input_container tve_submit_container tve_lg_2 tve_lg_submit">
                    <button type="Submit">GET ACCESS</button>
                </div>
            </div>
        </div>
    </div>
</div>
