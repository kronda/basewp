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
<div class="tve_lp_header tve_empty_dropzone">
</div>

<div class="tve_lp_content tve_editor_main_content tve_empty_dropzone tve_content_width">
    <h1 style="color: #2b2727; font-size: 52px; margin-top: 10px;margin-bottom: 10px;" class="rft">
        Build the Best Conversion-Focused Website with <font color="#fa2514">Product Name</font>
    </h1>

    <p style="color: #333; font-size: 20px;margin-top: 0;margin-bottom: 20px;">
        The perfect template for standalone reviews that convert!
    </p>

    <div class="thrv_wrapper" style="margin-top: 0;margin-bottom: 0;">
        <hr class="tve_sep tve_sep1"/>
    </div>
    <p style="color: #999; font-size: 16px;margin-top: 0;margin-bottom: 0;">
        <span class="bold_text">BY JOHN DOE,</span> MAY 27, 2015 11:00 AM
    </p>

    <div class="thrv_wrapper" style="margin-top: 0;margin-bottom: 0;">
        <hr class="tve_sep tve_sep1"/>
    </div>
    <div data-elem="sc_social_custom">
        <div class="thrv_wrapper thrv_social thrv_social_custom">
            <div class="tve_social_items tve_social_custom tve_style_1 tve_social_medium tve_social_ib">
                <div class="tve_s_item tve_s_fb_share" data-s="fb_share" data-href="{tcb_post_url}">
                    <a href="javascript:void(0)" class="tve_s_link"><span class="tve_s_icon"></span><span
                            class="tve_s_text">Share</span><span class="tve_s_count">0</span></a>
                </div>
                <div class="tve_s_item tve_s_t_share" data-s="t_share" data-href="{tcb_post_url}">
                    <a href="javascript:void(0)" class="tve_s_link"><span class="tve_s_icon"></span><span
                            class="tve_s_text">Tweet</span><span class="tve_s_count">0</span></a>
                </div>
                <div class="tve_s_item tve_s_in_share" data-s="in_share" data-href="{tcb_post_url}">
                    <a href="javascript:void(0)" class="tve_s_link"><span class="tve_s_icon"></span><span
                            class="tve_s_text">Share</span><span class="tve_s_count">0</span></a>
                </div>
            </div>
            <div class="tve_social_overlay"></div>
        </div>
    </div>
    <div class="thrv_wrapper tve_image_caption aligncenter" style="width: 964px;margin-top: 20px;margin-bottom: 30px;">
        <span class="tve_image_frame">
            <img class="tve_image" src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/review_1.png' ?>"
                 style="width: 964px;"/>
        </span>
    </div>
    <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="5">
        <div class="tve_cb tve_cb5 tve_white" style="margin-bottom: 30px;">
            <div class="tve_cb_cnt">
                <h6 style="color: #333; font-size: 24px;margin-top: 0;margin-bottom: 0;">Review Summary</h6>

                <div class="thrv_wrapper" style="margin-top: 0;margin-bottom: 0;">
                    <hr class="tve_sep tve_sep1"/>
                </div>
                <div class="thrv_wrapper thrv_columns tve_clearfix" style="margin-top: 15px;margin-bottom: 15px;">
                    <div class="tve_colm tve_foc tve_df tve_ofo ">
                        <p style="color: #535252;font-size: 18px;margin-top: 0;margin-bottom: 0;">
                            <span class="bold_text">
                                Product Name:
                            </span>
                        </p>
                    </div>
                    <div class="tve_colm tve_tfo tve_df tve_lst">
                        <p style="color: #666666;font-size: 18px;margin-top: 0;margin-bottom: 0;">
                            <span class="bold_text">
                                <font color="#fa2514">
                                    Product Name Here
                                </font>
                            </span>
                        </p>
                    </div>
                </div>
                <div class="thrv_wrapper thrv_columns tve_clearfix" style="margin-top: 15px;margin-bottom: 15px;">
                    <div class="tve_colm tve_foc tve_df tve_ofo ">
                        <p style="color: #535252;font-size: 18px;margin-top: 0;margin-bottom: 0;">
                            <span class="bold_text">
                                Product Type:
                            </span>
                        </p>
                    </div>
                    <div class="tve_colm tve_tfo tve_df tve_lst">
                        <p style="color: #666666;font-size: 18px;margin-top: 0;margin-bottom: 0;">
                            WordPress Plugin
                        </p>
                    </div>
                </div>
                <div class="thrv_wrapper thrv_columns tve_clearfix" style="margin-top: 15px;margin-bottom: 15px;">
                    <div class="tve_colm tve_foc tve_df tve_ofo ">
                        <p style="color: #535252;font-size: 18px;margin-top: 0;margin-bottom: 0;">
                            <span class="bold_text">
                                Price:
                            </span>
                        </p>
                    </div>
                    <div class="tve_colm tve_tfo tve_df tve_lst">
                        <p style="color: #666666;font-size: 18px;margin-top: 0;margin-bottom: 0;">
                            $45
                        </p>
                    </div>
                </div>
                <div class="thrv_wrapper thrv_columns tve_clearfix" style="margin-top: 15px;margin-bottom: 15px;">
                    <div class="tve_colm tve_foc tve_df tve_ofo ">
                        <p style="color: #535252;font-size: 18px;margin-top: 0;margin-bottom: 0;">
                            <span class="bold_text">
                                Summary:
                            </span>
                        </p>
                    </div>
                    <div class="tve_colm tve_tfo tve_df tve_lst">
                        <p style="color: #666666;font-size: 18px;margin-top: 0;margin-bottom: 0;">
                            This review summary provides everything at a glance and makes it easier for impulse buyers
                            to get what they want and move right on to the purchase.
                        </p>
                    </div>
                </div>
                <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="6">
                    <div class="tve_cb tve_cb6 tve_white"
                         style="margin-left: -20px;margin-right: -20px;margin-bottom: -20px;">
                        <div class="tve_cb_cnt">
                            <div class="thrv_wrapper thrv_columns tve_clearfix" style="margin-top: 0;margin-bottom: 0;">
                                <div class="tve_colm tve_foc tve_df tve_ofo ">
                                    <p style="color: #535252;font-size: 18px;margin-top: 20px;margin-bottom: 0;">
                                        <span class="bold_text">
                                            Rating:
                                        </span>
                                    </p>
                                </div>
                                <div class="tve_colm tve_tfo tve_df tve_lst">
                                    <div class="thrv_wrapper thrv_columns tve_clearfix"
                                         style="margin-top: 0;margin-bottom: 0;">
                                        <div class="tve_colm tve_foc tve_df tve_ofo ">
                                            <div class="thrv_wrapper thrv_star_rating tve_black">
                                                <span class="tve_rating_stars tve_style_star" data-value="4"
                                                      data-max="5" title="4 / 5"
                                                      style="width:120px;"><span style="width:96px;"></span>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="tve_colm tve_tfo tve_df tve_lst">
                                            <div class="thrv_wrapper thrv_button_shortcode" data-tve-style="1">
                                                <div class="tve_btn tve_btn3 tve_nb tve_red tve_bigBtn">
                                                    <a class="tve_btnLink tve_evt_manager_listen" href=""
                                                       data-tcb-events="__TCB_EVENT_<?php echo htmlentities(json_encode($events_config)) ?>_TNEVE_BCT__">
                                                <span class="tve_left tve_btn_im">
                                                    <i></i>
                                                    <span class="tve_btn_divider"></span>
                                                </span>
                                                        <span class="tve_btn_txt">Get it Now</span>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <p style="color: #333; font-size: 18px;margin-top: 0;margin-bottom: 25px;">
        Welcome to this long-form review page. It's ideal for writing an in-depth review of a product you can promote as
        an affiliate. The review is an informative piece of content, but at the same time, it is geared towards
        promoting the product at the end.
    </p>

    <p style="color: #333; font-size: 18px;margin-top: 0;margin-bottom: 25px;">
        In this way, it let's you promote a product while staying "under the radar" and without being blatantly
        promotional and salesy. ​Below, you'll see several segments of example content that show how you can mix text,
        sub-headings and images to keep the page visually interesting and make it easy to read.
    </p>

    <div class="thrv_wrapper thrv_columns">
        <div class="tve_colm tve_twc">
            <h3 style="color: #333333; font-size: 38px; margin-top: 20px;margin-bottom: 30px;" class="rft">Bibendum
                ipsum aliquet
                velit?</h3>

            <p style="color: #333; font-size: 18px;margin-top: 0;margin-bottom: 25px;">
                Duis sed odio sit amet nibh vulputate cursus a sit amet mauris. <font color="#ff3300">Morbi accumsan
                    ipsum velit. Nam nec
                    tellus a odio tincidunt auctor a ornare odio.</font> Sed non mauris vitae erat consequat auctor eu
                in elit.
                nostra.
            </p>

            <p style="color: #333; font-size: 18px;margin-top: 0;margin-bottom: 25px;">
                Proin gravida nibh vel velit auctor aliquet. Aenean sollicitudin, <font color="#ff3300">lorem quis
                    bibendum auctor</font>, nisi elit
                consequat ipsum, nec sagittis sem nibh id elit.
            </p>
        </div>
        <div class="tve_colm tve_twc tve_lst">
            <div class="thrv_wrapper tve_image_caption aligncenter" style="width: 458px;">
                <span class="tve_image_frame">
                    <img class="tve_image" src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/review_2.png' ?>"
                         style="width: 458px;"/>
                </span>
            </div>
        </div>
    </div>
    <h3 style="color: #333333; font-size: 38px; margin-top: 20px;margin-bottom: 30px;" class="rft">Class aptent taciti
        sociosqu ad
        litora torquent...</h3>

    <p style="color: #333; font-size: 18px;margin-top: 0;margin-bottom: 25px;">
        Lorem ipsum dolor proin gravida nibh vel velit auctor aliquet. Aenean sollicitudin, lorem quis bibendum auctor,
        nisi elit consequat ipsum, nec sagittis sem nibh id elit. Duis sed odio sit amet nibh vulputate cursus a sit
        amet mauris. Morbi accumsan ipsum velit. Nam nec tellus a odio tincidunt auctor a ornare odio. Sed non mauris
        vitae erat consequat auctor eu in elit. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per
        inceptos himenaeos.
    </p>

    <p style="color: #333; font-size: 18px;margin-top: 0;margin-bottom: 25px;">
        Mauris in erat justo. Nullam ac urna eu felis dapibus condimentum sit amet a augue. Sed non neque elit. Sed ut
        imperdiet nisi. Proin condimentum fermentum nunc.
    </p>

    <div class="thrv_wrapper thrv_columns" style="margin-bottom: 50px;">
        <div class="tve_colm tve_twc">
            <div class="thrv_wrapper tve_image_caption aligncenter" style="width: 423px;">
                <span class="tve_image_frame">
                    <img class="tve_image" src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/review_3.png' ?>"
                         style="width: 423px;"/>
                </span>
            </div>
        </div>
        <div class="tve_colm tve_twc tve_lst">
            <p style="color: #333; font-size: 18px;margin-top: 0;margin-bottom: 25px;">
                Duis sed odio sit amet nibh vulputate cursus a sit amet mauris. Morbi accumsan ipsum velit. Nam nec
                tellus a odio tincidunt auctor a ornare odio. Sed non mauris vitae erat consequat auctor eu in elit.
                nostra.
            </p>
            <blockquote>
                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec ligula lacus, vehicula et risus sed,
                ultricies fringilla mauris.
            </blockquote>
        </div>
    </div>
    <h3 style="color: #333333; font-size: 38px; margin-top: 20px;margin-bottom: 50px;" class="tve_p_center rft">
        Bibendum ipsum aliquet sagittis velit ut <font color="#f53a2b">Aeneam ut Elitis</font> auctor erat nam auctor
        consequat?
    </h3>

    <p style="color: #333; font-size: 18px;margin-top: 0;margin-bottom: 25px;">
        Proin gravida nibh vel velit auctor aliquet. Aenean sollicitudin, lorem quis bibendum auctor, nisi elit
        consequat ipsum, nec sagittis sem nibh id elit. Duis sed odio sit amet nibh vulputate cursus a sit amet mauris.
        Morbi accumsan ipsum velit. Nam nec tellus a odio tincidunt auctor a ornare odio. Sed non mauris vitae erat
        consequat auctor eu in elit. (Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos
        himenaeos.)
    </p>

    <p style="color: #333; font-size: 18px;margin-top: 0;margin-bottom: 25px;">
        Mauris in erat justo. Nullam ac urna eu felis dapibus condimentum sit amet a augue. Sed non neque elit. Sed ut
        imperdiet nisi. Proin condimentum fermentum nunc. <span class="bold_text">Etiam pharetra, erat sed fermentum feugiat, velit mauris
        egestas quam, ut aliquam massa nisl quis neque. Suspendisse in orci enim?</span>
    </p>

    <div class="thrv_wrapper tve_image_caption aligncenter" style="width: 967px;margin-top: 40px;margin-bottom: 40px;">
                <span class="tve_image_frame">
                    <img class="tve_image" src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/review_4.png' ?>"
                         style="width: 967px;"/>
                </span>
    </div>

    <p style="color: #333; font-size: 18px;margin-top: 0;margin-bottom: 25px;">
        Aenean sollicitudin, lorem quis bibendum auctor, nisi elit consequat ipsum, nec sagittis sem nibh id elit. Duis
        sed odio sit amet nibh vulputate cursus a sit amet mauris. Morbi accumsan ipsum velit. Nam nec tellus a odio
        tincidunt auctor a ornare odio. Sed non mauris vitae erat consequat auctor eu in elit. Class aptent taciti
        sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Mauris in erat justo. Nullam ac urna eu
        felis dapibus condimentum sit amet a augue. Sed non neque elit. Sed ut imperdiet nisi.
    </p>

    <div class="thrv_wrapper thrv_content_container_shortcode">
        <div class="tve_clear"></div>
        <div class="tve_center tve_content_inner" style="width: 850px;min-width:50px; min-height: 2em;">
            <p class="red_heading tve_p_center"
               style="color: #ff3000; font-size: 20px;margin-top: 0;margin-bottom: 15px;">
        <span class="italic_text">
            “Aenean sollicitudin, lorem quis bibendum auctor, nisi elit consequat ipsum, nec sagittis sem nibh id elit. Duis
            sed odio sit amet nibh vulputate cursus a sit amet mauris. ”
        </span>
            </p>

            <p class="tve_p_center" style="color: #333; font-size: 18px; margin-top: 0;margin-bottom: 25px;">
        <span class="bold_text">
            Aenean sollicitudin, lorem quis bibendum auctor, nisi elit consequat ipsum, nec sagittis sem nibh id elit. Duis
            sed odio sit amet nibh vulputate cursus a sit amet mauris. Morbi accumsan ipsum velit.
        </span>
            </p>
        </div>
        <div class="tve_clear"></div>
    </div>
    <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="4">
        <div class="tve_cb tve_cb4 tve_white" style="margin-bottom: 40px;">
            <div class="tve_hd">
                <span></span>
            </div>
            <div class="tve_cb_cnt">
                <h4 style="color: #2b2727; font-size: 32px;margin-top: 0;margin-bottom: 20px;" class="rft">
                    PRODUCT NAME CONCLUSION
                </h4>

                <p style="color: #333; font-size: 18px;margin-top: 0;margin-bottom: 0;">
                    Write a brief conclusion and summary of the points in the review here. Keep in mind that many
                    readers are just skimming, so it's good to repeat the most important points in this section. This is
                    also where you can make a strong and clear recommendation of the product.
                </p>

                <div class="thrv_wrapper" style="margin-bottom: -20px;">
                    <hr class="tve_sep tve_sep2"/>
                </div>
                <div class="thrv_wrapper thrv_columns tve_clearfix">
                    <div class="tve_colm tve_foc tve_df tve_ofo ">
                        <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="6">
                            <div class="tve_cb tve_cb6 tve_red" style="margin-top: -4px;margin-left: -1px;">
                                <div class="tve_cb_cnt">
                                    <h3 class="tve_p_center h3_light rft"
                                        style="color: #fff; font-size: 72px;margin-top: 0;margin-bottom: 0;">
                                        4/5
                                    </h3>
                                </div>
                            </div>
                        </div>
                        <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="6">
                            <div class="tve_cb tve_cb6 tve_white" data-tve-custom-colour="23066010"
                                 style="margin-top: -2px;margin-left: -1px;">
                                <div class="tve_cb_cnt">
                                    <div class="thrv_wrapper thrv_star_rating tve_black">
                                        <span class="tve_rating_stars tve_style_star tve_centerBtn" data-value="4"
                                              data-max="5" title="4 / 5"
                                              style="width:120px"><span style="width:96px"></span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tve_colm tve_tfo tve_df tve_lst">
                        <div class="thrv_wrapper thrv_columns" style="margin-bottom: 0;">
                            <div class="tve_colm tve_twc">
                                <h5 style="color: #666; font-size: 28px; margin-top: 0;margin-bottom: 25px;">
                                    The Good Stuff:
                                </h5>
                                <ul>
                                    <li>
                                        Lorem quis bibendum auctor
                                    </li>
                                    <li>
                                        Nisi elit consequat ipsum nec sagittis
                                        sem nibh id elit.
                                    </li>
                                    <li>
                                        Sed non mauris vitae erat consequat
                                        auctor eu in elit
                                    </li>
                                </ul>
                            </div>
                            <div class="tve_colm tve_twc tve_lst">
                                <h5 style="color: #666; font-size: 28px; margin-top: 0;margin-bottom: 25px;">
                                    The Bad Stuff:
                                </h5>
                                <ul>
                                    <li>
                                        Lorem quis bibendum auctor
                                    </li>
                                    <li>
                                        Nisi elit consequat ipsum nec sagittis
                                        sem nibh id elit.
                                    </li>
                                    <li>
                                        Sed non mauris vitae erat consequat
                                        auctor eu in elit
                                    </li>
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="thrv_wrapper thrv_contentbox_shortcode" data-tve-style="5">
        <div class="tve_cb tve_cb5 tve_white" data-tve-custom-colour="37647668">
            <div class="tve_cb_cnt">
                <div class="thrv_wrapper thrv_columns tve_clearfix">
                    <div class="tve_colm tve_oth">
                        <div class="thrv_wrapper tve_image_caption aligncenter" style="width: 331px;">
                            <span class="tve_image_frame">
                                <img class="tve_image"
                                     src="<?php echo TVE_LANDING_PAGE_TEMPLATE . '/css/images/review_5.png' ?>"
                                     style="width: 331px;"/>
                            </span>
                        </div>
                    </div>
                    <div class="tve_colm tve_tth tve_lst">
                        <div class="thrv_wrapper thrv_contentbox_shortcode best_offer_cb" data-tve-style="6">
                            <div class="tve_cb tve_cb6 tve_red">
                                <div class="tve_cb_cnt">
                                    <p class="condensed_font"
                                       style="color: #fff;font-size: 20px;margin-top: 0;margin-bottom: 0;">
                                        <span class="bold_text italic_text">
                                            best offer
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <h4 style="color: #333333; font-size: 36px;margin-top: 50px;margin-bottom: 20px;" class="rft">
                            Start Building Better Pages for Your Site Now
                        </h4>

                        <p style="color: #666666; font-size: 18px;margin-top: 0;margin-bottom: 25px;">
                            Make it easy for your readers to get the best deal on the product you've just reviewed, by advertising it in this section.
                        </p>

                        <div class="thrv_wrapper thrv_button_shortcode" data-tve-style="1">
                            <div class="tve_btn tve_btn5 tve_nb tve_white tve_bigBtn">
                                <a href="" class="tve_btnLink">
                                    <span class="tve_left tve_btn_im">
                                        <i class="tve_sc_icon review-page-icon-arrow"
                                           data-tve-icon="review-page-icon-arrow"
                                           style="background-image: none;color: #fa2514;"></i>
                                        <span class="tve_btn_divider"></span>
                                    </span>
                                    <span class="tve_btn_txt">Get this Product</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="tve_lp_footer tve_empty_dropzone">
    <p class="tve_p_center" style="color: #666666; font-size: 16px; margin: 20px 0; padding: 0;">
        &copy; {tcb_current_year} Thrive Landing Pages. All rights Reserved | <a href="#">Privacy Policy </a> | <a href="#"><span
                class="underline_text">Disclaimer</span></a>
    </p>
</div>