<?php
$config = array(
    'email' => 'Please enter a valid email address',
    'phone' => 'Please enter a valid phone number',
    'required' => 'Name and Email fields are required'
)
?>
<div class="thrv_wrapper thrv_columns tve_clearfix" style="margin-top: 0;">
    <div class="tve_colm tve_foc tve_df tve_ofo ">
        <div style="width: 162px;" class="thrv_wrapper tve_image_caption aligncenter">
                 <span class="tve_image_frame">
                    <img class="tve_image"
                         src="<?php echo TVE_LEADS_URL . 'editor-templates/_form_css/images/set_20_icon.png' ?>"
                         style="width: 162px"/>
                </span>
        </div>
    </div>
    <div class="tve_colm tve_tfo tve_df tve_lst">
        <h2 style="color: #101010; font-size: 34px; margin-top: 25px;margin-bottom: 0;" class="rft">
            Subscribe to our Newsletter!
        </h2>

        <p style="color: #000; font-size: 18px; margin-top: 0;">
            Aenean sollicitudin, lorem quis bibendum auctor, nisi elit
            ipsum, nec sagittis sem nibh id elit.
        </p>
    </div>
</div>
<div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="5">
    <div class="tve_cb tve_cb5 tve_teal">
        <div class="tve_cb_cnt">
            <div data-tve-style="1" class="thrv_wrapper thrv_lead_generation tve_clearfix tve_red thrv_lead_generation_horizontal tve_2" data-inputs-count="3" style="margin-top: 0;margin-bottom: 0;">
                <div style="display: none;" class="thrv_lead_generation_code"></div>
                <input type="hidden" class="tve-lg-err-msg" value="<?php echo htmlspecialchars(json_encode($config)) ?>" />
                <div class="thrv_lead_generation_container tve_clearfix">
                    <div class="tve_lead_generated_inputs_container tve_clearfix">
                        <div class="tve_lead_fields_overlay"></div>
                        <div class="tve_lg_input_container  tve_lg_2 tve_lg_input">
                            <input type="text" name="email" value="" data-placeholder="email" placeholder="Email Address...">
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




