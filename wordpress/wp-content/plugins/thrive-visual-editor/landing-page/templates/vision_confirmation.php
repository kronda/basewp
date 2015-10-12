<?php
$tve_globals   = tve_get_post_meta( get_the_ID(), 'tve_globals', true );
$events_config = array(
	array(
		't'      => 'click',
		'a'      => 'thrive_lightbox',
		'config' => array(
			'l_id'   => empty( $tve_globals['lightbox_id'] ) ? '' : $tve_globals['lightbox_id'],
			'l_anim' => 'slide_top'
		)
	)
);
?>
<div class="tve_lp_header tve_empty_dropzone">
	<div class="thrv_wrapper thrv_page_section" data-tve-style="1">
		<div class="out" style="background-color: #FFFFFF">
			<div class="in darkSec">
				<div class="cck clearfix tve_content_width">
					<div style="width: 132px;" class="thrv_wrapper tve_image_caption aligncenter">
                        <span class="tve_image_frame">
                            <a href="">
	                            <img class="tve_image"
	                                 src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/video-lead-logo.png' ?>"
	                                 style="width: 132px"/>
                            </a>
                        </span>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="tve_lp_content tve_editor_main_content tve_empty_dropzone tve_content_width">
	<h1 class="tve_p_center" style="color: #fff; font-size: 45px; margin-top: 60px;
	margin-bottom: 40px;">Thank You for Signing Up!</h1>

	<p class="tve_p_center" style="font-size: 22px; color: #fff;">Follow these steps to complete the process:</p>

	<div class="tve_vs_cf">
		<div class="tve_vs_st1">
			<div class="tve_vs_st">
				<div class="tve_vs_sti">
					<p style="font-size: 14px; color: #476f80; margin-bottom: 0;margin-top: 0;padding-bottom: 0;" class="tve_no_drag tve_no_icons">
						<span class="bold_text">STEP 1</span>
					</p>
				</div>
			</div>
			<div style="font-size: 25px; color: #fff;" class="thrv_wrapper thrv_icon aligncenter">
				<span style="margin-top: 15px;" class="tve_sc_icon vision-confirmation-mail" data-tve-icon="vision-confirmation-mail"></span>
			</div>
			<p class="tve_p_center" style="color: #fff; font-size: 16px;">
				<span class="bold_text">Check your email</span>
			</p>
		</div>
		<div class="tve_vs_img">
			<div class="tve_vs_st2">
                <div class="tve_vs_fe">
                    <div style="width: 206px;" class="thrv_wrapper tve_image_caption aligncenter tve_no_drag">
                        <span class="tve_image_frame">
                            <img draggable="false" class="tve_image"
                                 src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/find_email.png' ?>"
                                 style="width: 206px;"/>
                        </span>
                    </div>
                </div>
                <div class="tve_vs_st1i">
                    <div style="width: 384px;"
                         class="thrv_wrapper tve_image_caption aligncenter tve_no_drag tve_no_icons">
                        <span class="tve_image_frame">
                            <img draggable="false" class="tve_image"
                                 src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/vision_confirmation_bg.png' ?>"
                                 style="width: 384px;"/>
                        </span>
                    </div>
                </div>
			</div>
		</div>
		<div class="thrv_columns tve_clearfix">
			<div class="tve_colm tve_oth">
				<div class="tve_vs_st3">
					<div class="tve_vs_st">
						<div class="tve_vs_sti">
                            <p style="font-size: 14px; color: #476f80; margin-bottom: 0;margin-top: 0;padding-bottom: 0;" class="tve_no_drag tve_no_icons">
								<span class="bold_text">STEP 2</span>
							</p>
						</div>
					</div>
					<div style="font-size: 25px; color: #fff" class="thrv_wrapper thrv_icon aligncenter">
                        <span style="margin-top: 15px;" class="tve_sc_icon vision-confirmation-mailopen" data-tve-icon="vision-confirmation-mailopen"></span>
					</div>
					<p class="tve_p_center" style="color: #fff; font-size: 16px;">
						<span class="bold_text">Open the email sent by us. It has
							the subject line Lorem Ipsum
							and is sent from Email Sender</span>
					</p>
				</div>
			</div>
			<div class="tve_colm tve_oth">
				<div class="tve_vs_st4">
					<div class="tve_vs_st">
						<div class="tve_vs_sti">
                            <p style="font-size: 14px; color: #476f80; margin-bottom: 0;margin-top: 0;padding-bottom: 0;"  class="tve_no_drag tve_no_icons">
								<span class="bold_text">STEP 3</span>
							</p>
						</div>
					</div>
					<div style="font-size: 25px; color: #fff" class="thrv_wrapper thrv_icon aligncenter">
                        <span style="margin-top: 15px;" class="tve_sc_icon vision-confirmation-link" data-tve-icon="vision-confirmation-link"></span>
					</div>
					<p class="tve_p_center" style="color: #fff; font-size: 16px;">
						<span class="bold_text">Click on the confirmation link</span>
					</p>
				</div>
			</div>
			<div class="tve_colm tve_thc tve_lst">
				<div class="tve_vs_st5">
					<div class="tve_vs_st">
						<div class="tve_vs_sti">
                            <p style="font-size: 14px; color: #476f80; margin-bottom: 0;margin-top: 0;padding-bottom: 0;"  class="tve_no_drag tve_no_icons">
								<span class="bold_text">STEP 4</span>
							</p>
						</div>
					</div>
					<div style="font-size: 25px; color: #fff" class="thrv_wrapper thrv_icon aligncenter">
                        <span style="margin-top: 15px;" class="tve_sc_icon vision-confirmation-download" data-tve-icon="vision-confirmation-download"></span>
					</div>
					<p class="tve_p_center" style="color: #fff; font-size: 16px;">
						<span class="bold_text">Enjoy your free download</span>
					</p>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="tve_lp_footer tve_empty_dropzone">
	<div class="thrv_wrapper thrv_page_section" data-tve-style="1" style="margin-top: 40px;">
		<div class="out">
			<div class="in darkSec">
				<div class="cck clearfix">
					<p class="tve_p_center" style="margin: 0; padding: 0; color: #ffffff; font-size: 16px">&copy; {tcb_current_year}
						by ACME Inc.</p>

					<p class="tve_p_center" style="margin: 0; padding: 0; color: #ffffff; font-size: 16px">
						<a href="#"><span class="underline_text">Disclaimer</span></a>
					</p>
				</div>
			</div>
		</div>
	</div>
</div>