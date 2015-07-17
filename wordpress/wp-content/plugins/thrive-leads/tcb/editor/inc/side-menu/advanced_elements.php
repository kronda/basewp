<div class="tve_cpanel_sec tve_cpanel_sep">
    <span class="tve_cpanel_head tve_expanded">Advanced Elements</span>
</div>
<div class="tve_cpanel_list">
    <div class="sc_table tve_option_separator tve_clearfix" title="Widgets">
        <div class="tve_icm tve-ic-gears tve_left"></div>
        <span class="tve_expanded tve_left">Widgets</span>
        <span class="tve_caret tve_icm tve_sub_btn tve_right tve_expanded"></span>

        <div class="tve_clear"></div>
        <div class="tve_sub_btn" title="Table">
            <div class="tve_sub">
                <ul>
                    <li class="cp_draggable" title="Custom Menu" data-elem="sc_widget_menu" data-overlay="1" data-wpapi="1">
                        <div class="tve_icm tve-ic-plus"></div>Custom Menu
                        <?php foreach ($menus as $item) : /* by default, use the first available menu. If nothing is found, show the user a message that no custom menu is defined */ ?>
                            <input type="hidden" name="menu_id" value="<?php echo $item['id'] ?>">
                        <?php break; endforeach; ?>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="tve_grid tve_option_separator tve_clearfix" title="Pricing Table">
        <div class="tve_icm tve-ic-dollar tve_left"></div>
        <span class="tve_expanded tve_left">Pricing Table</span>
        <span class="tve_caret tve_icm tve_sub_btn tve_right tve_expanded" id="sub_02"></span>

        <div class="tve_clear"></div>
        <div class="tve_sub_btn" title="Pricing Table">
            <div class="tve_sub">
                <ul>
                    <li class="cp_draggable sc_pricing_table_1col" title="1 Columns" data-elem="sc_pricing_table_1col">
                        <div class="tve_icm tve-ic-plus"></div>1 Column
                    </li>
                    <li class="cp_draggable sc_pricing_table_2col" title="2 Columns" data-elem="sc_pricing_table_2col">
                        <div class="tve_icm tve-ic-plus"></div>2 Columns
                    </li>
                    <li class="cp_draggable sc_pricing_table_3col" title="3 Columns" data-elem="sc_pricing_table_3col">
                        <div class="tve_icm tve-ic-plus"></div>3 Columns
                    </li>
                    <li class="cp_draggable sc_pricing_table_4col" title="4 Columns" data-elem="sc_pricing_table_4col">
                        <div class="tve_icm tve-ic-plus"></div>4 Columns
                    </li>
                    <li class="cp_draggable sc_pricing_table_5col" title="5 Columns" data-elem="sc_pricing_table_5col">
                        <div class="tve_icm tve-ic-plus"></div>5 Columns
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="sc_table tve_option_separator tve_clearfix" title="Tabs">
        <div class="tve_icm tve-ic-folder tve_left"></div>
        <span class="tve_expanded tve_left">Tabbed Content</span>
        <span class="tve_caret tve_icm tve_sub_btn tve_right tve_expanded"></span>

        <div class="tve_clear"></div>
        <div class="tve_sub_btn" title="Table">
            <div class="tve_sub">
                <ul>
                    <li class="cp_draggable sc_tabs" title="Horizontal Tabs" data-elem="sc_tabs">
                        <div class="tve_icm tve-ic-plus"></div>Horizontal Tabs
                    </li>
                    <li class="cp_draggable sc_tabs sc_vtabs" title="Vertical Tabs" data-elem="sc_vTabs">
                        <div class="tve_icm tve-ic-plus"></div>Vertical Tabs
                        <input type="hidden" name="vtabs" value="1"/>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="tve_grid tve_option_separator tve_clearfix" title="Feature Grids">
        <div class="tve_icm tve-ic-th tve_left"></div>
        <span class="tve_expanded tve_left">Feature Grid</span>
        <span class="tve_caret tve_icm tve_sub_btn tve_right tve_expanded" id="sub_02"></span>

        <div class="tve_clear"></div>
        <div class="tve_sub_btn" title="Feature Grids">
            <div class="tve_sub">
                <ul>
                    <li class="tve_sub_title"><strong>Feature grid with Images</strong></li>
                    <li class="cp_draggable sc_feature_grid_2_column"
                        title="2 Column Feature Grid" data-elem="sc_feature_grid_2_column">
                        <div class="tve_icm tve-ic-plus"></div>2 Column Feature Grid
                    </li>
                    <li class="cp_draggable sc_feature_grid_3_column"
                        title="3 Column Feature Grid" data-elem="sc_feature_grid_3_column">
                        <div class="tve_icm tve-ic-plus"></div>3 Column Feature Grid
                    </li>
                    <li class="cp_draggable sc_feature_grid_4_column"
                        title="4 Column Feature Grid" data-elem="sc_feature_grid_4_column">
                        <div class="tve_icm tve-ic-plus"></div>4 Column Feature Grid
                    </li>
                    <li class="tve_sub_title"><strong>Feature grid with Icons</strong></li>
                    <li class="cp_draggable sc_feature_grid_2_column"
                        title="2 Column Feature Grid" data-elem="sc_feature_grid_2_column_icons">
                        <div class="tve_icm tve-ic-plus"></div>2 Column Feature Grid<input type="hidden" name="use_icons" value="1"/>
                    </li>
                    <li class="cp_draggable sc_feature_grid_3_column"
                        title="3 Column Feature Grid" data-elem="sc_feature_grid_3_column_icons">
                        <div class="tve_icm tve-ic-plus"></div>3 Column Feature Grid<input type="hidden" name="use_icons" value="1"/>
                    </li>
                    <li class="cp_draggable sc_feature_grid_4_column"
                        title="4 Column Feature Grid" data-elem="sc_feature_grid_4_column_icons">
                        <div class="tve_icm tve-ic-plus"></div>4 Column Feature Grid<input type="hidden" name="use_icons" value="1"/>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="cp_draggable sc_toggle tve_option_separator tve_clearfix" title="Toggle" data-elem="sc_toggle">
        <div class="tve_icm tve-ic-eye-blocked tve_left" title="Toggle"></div>
        <span class="tve_expanded tve_left">Content Toggle</span>
    </div>
    <div class="sc_table tve_option_separator tve_clearfix" title="Table">
        <div class="tve_icm tve-ic-table tve_left"></div>
        <span class="tve_expanded tve_left">Table</span>
        <span class="tve_caret tve_icm tve_sub_btn tve_right tve_expanded"></span>

        <div class="tve_clear"></div>
        <div class="tve_sub_btn" title="Table">
            <div class="tve_sub">
                <ul>
                    <li class="cp_draggable sc_table_plain" title="Plain" data-elem="sc_table_plain">
                        <div class="tve_icm tve-ic-plus"></div>Plain
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="cp_draggable sc_gmap tve_option_separator tve_clearfix" title="Google Map" data-elem="sc_gmap">
        <div class="tve_icm tve-ic-location tve_left" title="Google Map"></div>
        <span class="tve_expanded tve_left">Google Map Embed</span>
    </div>
    <div class="sc_countdown_timer tve_option_separator tve_clearfix" title="Countdown Timer">
        <div class="tve_icm tve-ic-clock tve_left"></div>
        <span class="tve_expanded tve_left">Countdown Timer</span>
        <span class="tve_caret tve_icm tve_sub_btn tve_right tve_expanded"></span>

        <div class="tve_clear"></div>
        <div class="tve_sub_btn" title="Table">
            <div class="tve_sub">
                <ul>
                    <li class="cp_draggable sc_countdown_timer sc_countdown_timer_plain" title="Countdown" data-elem="sc_countdown_timer_plain">
                        <div class="tve_icm tve-ic-plus"></div>Countdown
                        <input type="hidden" name="wp_timezone" value="<?php echo $_POST['wp_timezone'] ?>"/>
                        <input type="hidden" name="wp_timezone_offset" value="<?php echo $_POST['wp_timezone_offset'] ?>"/>
                    </li>
                    <li class="cp_draggable sc_countdown_timer sc_countdown_timer_evergreen" title="Countdown Evergreen" data-elem="sc_countdown_timer_evergreen">
                        <div class="tve_icm tve-ic-plus"></div>Countdown Evergreen
                        <input type="hidden" name="wp_timezone" value="<?php echo $_POST['wp_timezone'] ?>"/>
                        <input type="hidden" name="wp_timezone_offset" value="<?php echo $_POST['wp_timezone_offset'] ?>"/>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="cp_draggable sc_responsive_video tve_option_separator tve_clearfix" title="Responsive Video" data-elem="sc_responsive_video">
        <div class="tve_icm tve-ic-play tve_left" title="Responsive Video"></div>
        <span class="tve_expanded tve_left">Responsive Video</span>
    </div>
    <div class="cp_draggable sc_contents_table tve_option_separator tve_clearfix" title="Table Of Contents" data-elem="sc_contents_table">
        <div class="tve_icm tve-ic-list-alt tve_left" title="Table Of Contents"></div>
        <span class="tve_expanded tve_left">Table of Contents</span>
    </div>
    <div class="cp_draggable sc_lead_generation tve_option_separator tve_clearfix" title="Lead Generation" data-elem="sc_lead_generation">
        <div class="tve_icm tve-ic-envelope tve_left" title="Lead Generation"></div>
        <span class="tve_expanded tve_left">Lead Generation</span>
    </div>
    <?php /*
    <div class="cp_draggable tve_option_separator tve_clearfix" title="Widgets" data-elem="sc_widgets">
        <div class="tve_icm tve-ic-square-o tve_left" title="Widgets"></div>
        <span class="tve_expanded tve_left">Widgets</span>
    </div>*/?>
    <div class="cp_draggable tve_option_separator tve_clearfix" title="Post Grid" data-elem="sc_post_grid" data-overlay="1">
        <div class="tve_icm tve-ic-th-large tve_left" title="Post Grid"></div>
        <span class="tve_expanded tve_left">Post Grid</span>
        <input type="hidden" name="placeholder" value="1" />
    </div>
    <?php if($is_thrive_leads_active && empty($_POST['disabled_controls']['leads_shortcodes'])) : ?>
        <div class="cp_draggable tve_option_separator tve_clearfix" title="Thrive Leads Shortcodes">
            <div class="tve_icm tve-ic-my-library-books tve_left"></div>
            <span class="tve_expanded tve_left">Thrive Leads Forms</span>
            <span class="tve_caret tve_icm tve_sub_btn tve_right tve_expanded"></span>

            <div class="tve_clear"></div>
            <div class="tve_sub_btn" title="Table">
                <div class="tve_sub">
                    <ul>
                        <?php foreach($thrive_leads_shortcodes as $thrive_leads_shortcode): ?>
                            <li class="cp_draggable" title="<?php echo $thrive_leads_shortcode->post_title ?>" data-elem="sc_thrive_leads_shortcode" data-overlay="1" data-wpapi="1">
                                <input type="hidden" name="thrive_leads_shortcode_id" value="<?php echo $thrive_leads_shortcode->ID ?>"/>
                                <div class="tve_icm tve-ic-plus"></div><?php echo $thrive_leads_shortcode->post_title ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>