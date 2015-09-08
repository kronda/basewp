<div id="tcw-options-container">
    <div id="tcw-message-container" style="display: none"></div>
    <div class="tcw_clearfix">
        <button class="tcw_load_saved_options tcw_right"><?php echo __("Load Saved Options", 'thrive-cw');?></button>
        <select class="tcw_saved_options tcw_right" style="margin-right: 10px;">
            <option value="0"><?php echo __("Current selection", 'thrive-cw');?></option>
        </select>
    </div>

    <h1>
        <span><?php echo __("Display Logic", 'thrive-cw'); ?></span>
        <span id="inclusions-count">(0)</span>
    </h1>

    <p><?php echo __("Use this form to set when your widgets will be displayed.", 'thrive-cw') ?></p>

    <div id="show_widget_options"></div>

    <h1 data-target="#exclusions_wrapper" style="display:inline-block;margin-top: 30px;cursor:pointer;"
        class="tcw-clickable tcw-toggle-display collapsed">
        <span class="tcw-icon tcw-icon-keyboard-arrow-right"> </span>
        <span><?php echo __("Exclusions", 'thrive-cw'); ?></span>
        <span id="exclusions-count">(0)</span>
    </h1>

    <div id="exclusions_wrapper" style="display: none;">
        <p style="margin: 0; padding: 0 0 19px 0"><?php echo __("Use this form to set when your widgets will not be displayed. This overrides the display logic above.", 'thrive-cw') ?></p>

        <div id="hide_widget_options"></div>
    </div>

    <div class="tcw_tabs_wrapper" style="margin: 20px 0 0 0">
        <div class="tcw_tabs_content_wrapper">
            <div class="tcw_tabs_content tcw_clearfix" style="display: block">
                <h4><?php echo __("Save as Template", 'thrive-cw'); ?></h4>

                <p><?php echo __("You can save the display configuration that you've created if you'd like a template to re-use with other widgets.", 'thrive-clever-widget'); ?></p>
                <input class="tcw_left" type="text" name="tcw_new_template_name"
                       placeholder="<?php echo __("Template name", 'thrive-cw'); ?>"/>
                <button
                    class="tcw_add_new_template tcw_left"><?php echo __("Save Display Template", 'thrive-clever-widget'); ?></button>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>

    <div class="tcw-widget-settings-footer">
        <div class="footer-buttons">
            <div class="tcw_right">
                <button class="tcw-close-thickbox tcw-btn tcw-btn-red"><?php echo __("Cancel", 'thrive-cw'); ?></button>
                <button class="tcw_save_widget_options tcw-btn tcw-btn-green"><?php echo __("Save & Close", 'thrive-cw'); ?></button>
            </div>
            <div class="tcw_clearfix"></div>
        </div>
    </div>

</div>

<script type="application/javascript">
    var tcw_app = tcw_app || {};

    var jsonHangers = <?php echo json_encode($hangers) ?>;
    var jsonTemplates = <?php echo json_encode($savedTemplates)?>;

    (function () {
        'use strict';

        /**
         * Instantiate a collection of Thrive_Clever_Widgets_Hanger for the thickbox content
         * @type {tcw_app.Hangers}
         */
        tcw_app.hangers = new tcw_app.Hangers(jsonHangers);
        tcw_app.savedTemplates = new tcw_app.Templates(jsonTemplates);

        /**
         * Render the thickbox content when the thickbox opens
         */
        var thickboxView = new tcw_app.ThickboxView({
            collection: tcw_app.hangers
        });

        thickboxView.urlSaveOptions = '<?php echo 'admin-ajax.php?action=tcw_widget_save_options' ?>';
        thickboxView.urlSaveTemplate = '<?php echo 'admin-ajax.php?action=tcw_widget_save_template' ?>';
        thickboxView.widget = '<?php echo $_GET['widget'] ?>';

    })(jQuery);
</script>

<script type="text/template" id="hanger-template">
    <div class="tcw_tabs_wrapper tcw_clearfix">
        <ul class="tcw_clearfix tcw_tabs"></ul>
        <div class="tcw_tabs_content_wrapper"></div>
    </div>
</script>

<script type="text/template" id="tab-label-template">
    <span>
        <# if (tab.countCheckedOptions()) { #><strong> <# }#>
            <#= tab.get('label') #> (<#= tab.countCheckedOptions() #>)
        <# if (tab.countCheckedOptions()) { #></strong> <# }#>
    </span>
</script>

<script type="text/template" id="option-template">
    <label data-type="<#= type #>">
        <input value="<#= id  #>" type="checkbox" class="tcw_toggle_option" <# if (isChecked) { #> checked="checked"<# }
        #> />
        <span><#= label #> </span>
    </label>
</script>

<script type="text/template" id="direct-url-template">
    <a href="<#= label #>" target="_blank"><#= label #></a>
    <button class="tcw_removeDirectLink tcw-btn-red"><?php echo __("Remove URL", 'thrive-cw') ?></button>
</script>

<script type="text/template" id="filter-template">
    <a id="<#= identifier #>" class="tcw_tabFilter <#= cssClass #>" href="javascript:void(0)"><#= label #></a>
</script>

<script type="text/template" id="selected-filter-template">
    <div class="tcw_selectedFilter">
        <h4><?php echo __('Taxonomy:', 'thrive-cw') ?> <#= filter #></h4>
    </div>
</script>

<script type="text/template" id="add-direct-url-form-template">
    <div class="tcw_addDirectLinkWrapper tcw_clearfix">
        <input type="text" class="tcw_directUrl" placeholder="<?php echo __("Add new URL", 'thrive-cw'); ?>"/>
        <button class="tcw_addDirectLink"><?php echo __('Add URL', 'thrive-cw'); ?></button>
    </div>
</script>