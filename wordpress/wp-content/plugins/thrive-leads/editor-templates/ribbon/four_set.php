<?php
$config = array(
    'email' => 'Please enter a valid email address',
    'phone' => 'Please enter a valid phone number',
    'required' => 'Name and Email fields are required'
)
/* always include all elements inside a thrv-leads-slide-in element */
?>
<style type="text/css">
    #wpadminbar {
        z-index: 999992!important;
    }
</style>
<div class="thrv-ribbon tve_no_drag tve_no_icons tve_element_hover thrv_wrapper tve_four_set tve_purple" >
    <div class="tve-ribbon-content tve_editor_main_content">
        <div class="thrv_wrapper thrv_columns tve_clearfix" style="margin-top: 0;margin-bottom: 0;">
            <div class="tve_colm tve_twc">
                <h6 style="color: #fff; font-size: 14px;margin-top: 15px;margin-bottom: 0;">
                    Sign up to Win a royal London experience worth up to $ 12,000
                </h6>
            </div>
            <div class="tve_colm tve_twc tve_lst">
                <div class="thrv_wrapper thrv_lead_generation tve_clearfix thrv_lead_generation_horizontal tve_green tve_2" data-inputs-count="2" data-tve-style="1" style="margin-top: 0px; margin-bottom: 0px;">
                    <div class="thrv_lead_generation_code" style="display: none;"></div>
                    <input type="hidden" class="tve-lg-err-msg" value="<?php echo htmlspecialchars(json_encode($config)) ?>" />
                    <div class="thrv_lead_generation_container tve_clearfix">
                        <div class="tve_lead_generated_inputs_container tve_clearfix">
                            <div class="tve_lead_fields_overlay"></div>
                            <div class=" tve_lg_input_container tve_lg_2 tve_lg_input">
                                <input type="text" data-placeholder="" value="" name="name" placeholder="Full Name"/>
                            </div>
                            <div class="tve_lg_input_container tve_submit_container tve_lg_2 tve_lg_submit">
                                <button type="Submit">DOWNLOAD</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <a href="javascript:void(0)" class="tve-ribbon-close" title="Close">x</a>

</div>
