<div class="tve_cpanel_sec tve_cpanel_sep">
    <span class="tve_cpanel_head tve_expanded">Thrive Theme Elements</span>
</div>
<div class="tve_cpanel_list">
    <?php if ($is_thrive_theme) : ?>
        <div class="tve_option_separator tve_clearfix" title="Borderless Content">
            <div class="tve_icm tve-ic-uniE63C tve_left"></div>
            <span class="tve_expanded tve_left">Borderless Content</span>
                <span class="tve_caret tve_icm tve_right tve_sub_btn tve_expanded"
                      id="sub_02"></span>

            <div class="tve_clear"></div>
            <div class="tve_sub_btn" title="Borderless Content">
                <div class="tve_sub active_sub_menu">
                    <ul>
                        <li class="cp_draggable sc_borderless_image ui-draggable" title="Borderless Image" data-elem="sc_borderless_image">
                            <div class="tve_icm tve-ic-plus"></div>Borderless Image
                        </li>
                        <li class="cp_draggable sc_borderless_html ui-draggable" title="Borderless Video Embed" data-elem="sc_borderless_html">
                            <div class="tve_icm tve-ic-plus"></div>Borderless Video Embed
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    <?php endif ?>
    <div class="cp_draggable sc_page_section tve_option_separator tve_clearfix" title="Page Section" data-elem="sc_page_section">
        <div class="tve_icm tve-ic-layout tve_left" title="Page Section"></div>
        <span class="tve_expanded tve_left">Page Section</span>

    </div>
    <?php if ($is_thrive_theme) : ?>
        <div class="tve_option_separator tve_clearfix" title="Thrive Opt-In">
            <div class="tve_icm tve-ic-share-square-o tve_left"></div>
            <span class="tve_expanded tve_left">Thrive Opt-In</span>
            <span class="tve_caret tve_icm tve_right tve_sub_btn tve_expanded"></span>

            <div class="tve_clear"></div>
            <div class="tve_sub_btn" title="Thrive Opt-In">
                <div class="tve_sub active_sub_menu">
                    <ul>
                        <?php if (!empty($thrive_optins)) : ?>
                            <?php foreach ($thrive_optins as $_id => $_title) : ?>
                                <li class="cp_draggable sc_thrive_optin ui-draggable" title="<?php echo $_title ?>" data-elem="sc_thrive_optin"
                                    data-wpapi="1"
                                    data-overlay="1">
                                    <div class="tve_icm tve-ic-plus"></div><?php echo $_title ?>
                                    <input type="hidden" name="optin" value="<?php echo $_id ?>"/>
                                    <input type="hidden" name="text" value="Subscribe Now"/>
                                    <input type="hidden" name="color" value="blue"/>
                                </li>
                            <?php endforeach ?>
                        <?php else : ?>
                            <li class="tve-no-entry">No Thrive Opt-In found.</li>
                        <?php endif ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="cp_draggable sc_thrive_posts_list tve_option_separator tve_clearfix" title="Thrive Post List" data-elem="sc_thrive_posts_list" data-wpapi="1" data-overlay="1">
            <div class="tve_icm tve-ic-list tve_left" title="Thrive Post List"></div>
            <span class="tve_expanded tve_left">Thrive Posts List</span>
        </div>
        <div class="cp_draggable sc_thrive_custom_menu tve_option_separator tve_clearfix" title="Thrive Custom Menu" data-elem="sc_thrive_custom_menu" data-wpapi="1" data-overlay="1">
            <div class="tve_icm tve-ic-paragraph-justify tve_left" title="Thrive Custom Menu"></div>
            <span class="tve_expanded tve_left">Thrive Custom Menu</span>
        </div>
	    <?php if(!in_array($current_theme_name, $banned_themes_names)) : ?>
	        <div class="cp_draggable sc_thrive_custom_phone tve_option_separator tve_clearfix" title="Thrive Click To Call" data-elem="sc_thrive_custom_phone" data-wpapi="1" data-overlay="1">
	            <div class="tve_icm tve-ic-phone tve_left" title="Thrive Click To Call"></div>
	            <span class="tve_expanded tve_left">Thrive Click To Call</span>
	        </div>
	    <?php endif; ?>
    <?php endif ?>
</div>