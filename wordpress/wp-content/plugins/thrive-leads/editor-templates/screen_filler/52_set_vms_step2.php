<?php
$config = array(
    'email' => 'Please enter a valid email address',
    'phone' => 'Please enter a valid phone number',
    'required' => 'Name and Email fields are required'
)
/**
 * STATE EVENTS examples (important bits: data-tcb-events and the "tve_evt_manager_listen tve_et_click" classes
 *
 * -close lb:
 * <a href="#" data-tcb-events="|close_lightbox|" class="tve_evt_manager_listen tve_et_click">CLOSE THIS LIGHTBOX</a>
 *
 * -close screen filler:
 * <a href="#" data-tcb-events="|close_screen_filler|" class="tve_evt_manager_listen tve_et_click">CLOSE THIS SCREEN FILLER</a>
 *
 * -state switch example ( open_state_x, where x is the index in the _config / multi_step / states array:
 * <a href="#" data-tcb-events="|open_state_2|" class="tve_evt_manager_listen tve_et_click">open state 2</a>
 */
?>

<div
    class="thrv-leads-screen-filler tve_no_drag tve_no_icons tve_element_hover thrv_wrapper tve_52_set_vms_step2 tve_white">
    <div class="tve-screen-filler-content tve_editor_main_content">
        <div data-tve-style="1" class="thrv_wrapper thrv_page_section">
            <div style="background-color: #f5f5f7" class="out">
                <div class="in darkSec">
                    <div class="cck tve_clearfix">
                        <h2 style="color: #333333; font-size: 40px;margin-top: 5px;margin-bottom: 0;" class="rft">
                            Sign up Below <br/>
                            to Get Access to Your Free Product
                        </h2>
                    </div>
                </div>
            </div>
        </div>
	    <div class="thrv_wrapper" style="margin: 0;">
		    <hr class="tve_sep tve_sep1"/>
	    </div>
	    <div class="thrv_wrapper thrv_columns tve_clearfix">
		    <div class="tve_colm tve_oth">
			    <div style="width: 185px;margin-top: 0;margin-bottom: 0;"
			         class="thrv_wrapper tve_image_caption aligncenter">
                                 <span class="tve_image_frame">
                                    <img class="tve_image"
                                         src="<?php echo TVE_LEADS_URL . 'editor-templates/_form_css/images/51_set_image.png' ?>"
                                         style="width: 185px;"/>
                                </span>
			    </div>
		    </div>
		    <div class="tve_colm tve_tth tve_lst">
			    <div
				    class="thrv_wrapper thrv_lead_generation tve_clearfix thrv_lead_generation_vertical tve_red tve_3"
				    data-inputs-count="3" data-tve-style="1" style="margin-top: 10px; margin-bottom: 0;">
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
							    <button type="Submit">Send me the FREE DOWNLOAD</button>
						    </div>
					    </div>
				    </div>
			    </div>
		    </div>
	    </div>
    </div>
    <a href="javascript:void(0)" class="tve-screen-filler-close" title="Close">x</a>
</div>
