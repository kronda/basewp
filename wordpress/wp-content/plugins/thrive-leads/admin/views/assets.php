<div id="tve-content">
    <script type="text/javascript">
        var TVE_Page_Data = {
            globalSettings: <?php echo json_encode($dashboard_data['global_settings']) ?>,
        };
        ThriveLeads.objects.AssetsCollection = new ThriveLeads.collections.Assets(<?php echo json_encode($assets_data['assets']) ?>);
        ThriveLeads.objects.AssetsWizard = new ThriveLeads.models.AssetWizard(<?php echo json_encode($assets_data['wizard']) ?>);
        ThriveLeads.objects.AssetConnection = new ThriveLeads.collections.AssetConnection(<?php echo json_encode($assets_data['wizard']['structured_apis']) ?>);
    </script>

    <div id="tve-asset-delivery">
        <div class="tve-header">
            <div class="tve-logo">
                <?php echo '<img src="' . plugins_url('thrive-leads/admin/img') . '/logo.png" > '; ?>
                <div class="tve-global-settings">
                    <a href="<?php echo admin_url( 'admin.php?page=thrive_leads_asset_delivery' ); ?>"  title="<?php echo __('Thrive Leads Asset Delivery', 'thrive-leads') ?>">
                        <span class="tve-icon-cloud"></span>
                    </a>
                    &nbsp;
                    <a href="javascript:void(0)" class="tl-inbound-link-builder" title="<?php echo __('Thrive Leads SmartLinks', 'thrive-leads') ?>">
                        <span class="tve-icon-link"></span>
                    </a>

                    &nbsp;
                    <a href="javascript:void(0)" class="tl-open-settings" title="<?php echo __('Settings', 'thrive-leads') ?>">
                        <span class="tve-icon-cog"></span>
                    </a>

                    <div class="tve-settings postbox">
                        <span class="tl-close-settings tve-icon-arrow-up" title="<?php echo __('Close') ?>"></span>

                        <h3 class="tve-settings-title"><span><?php echo __('General Settings', 'thrive-leads') ?></span></h3>

                        <div class="inside">
                            <div class="tve-input-group tve_leads_clearfix">
                                <label><?php echo __('Lazy load forms') ?></label>

                                <div class="tve-input">
                                    <label class="tve-switch">
                                        <input type="checkbox" name="ajax_load" value="1"
                                        <?php if ($dashboard_data['global_settings']['ajax_load']) { ?> checked="checked"
                                            <?php } ?> class="tve-setting-change tve-setting-ajax_load" autocomplete="off">
                                                <i></i>
                                    </label>
                                </div>
                                <span class="tve-field-desc">
                                    <?php echo __('Using lazy loading can speed up the loading of your page and ensure compatibility with the various WordPress caching plugins such as W3 Total Cache, WP Super Cache and WP Rocket. If set to Off while caching plugins are enabled, tracking and conversions will not be recorded correctly', 'thrive-leads') ?>
                                </span>
                            </div>
                            <div class="tve-input-group tve_leads_clearfix">
                                <h3 class="tve-settings-title" style="padding-left:0"><?php echo __('Reset cached statistics') ?></h3>

                                <span class="tve-field-desc">
                                    <?php echo __('In order to increase overall performance, Thrive Leads caches the number of impressions and conversions for each Lead Group, Shortcode, ThriveBox and Form. Click the following link to purge the cache and re-build it.', 'thrive-leads') ?>
                                    <a class="tve-leads-clear-cache" href="javascript:void(0)"><?php echo __('Purge cache', 'thrive-leads') ?></a>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tve-page-title tve_leads_clearfix">
                <h1 class="panel-title pull-left"><?php echo __('Asset Delivery', 'thrive-leads'); ?></h1>

                <div class="tve-asset-group-status pull-right">
                    <span class="tve-asset-group-status-text tve-asset-graytext"><?php echo __('Status', 'thrive-leads'); ?> :</span>
                    <?php if ($wizard == false && $assets_data['wizard']['proprieties']['connections'] == 0) { ?>
                        <div class="tve-asset-group-connection-status tve-asset-group-connection-event tve-asset-group-status-wrapper">
                            <span class="tl-tooltip tve-asset-icon-message tve-icon-paper-plane" title="Click here to set up the email delivery service."></span>
                    <?php } elseif ($wizard == 1 && $assets_data['wizard']['proprieties']['connections'] == 0) { ?>
                        <div class="tve-asset-group-connection-status-red tve-asset-group-connection-event tve-asset-group-status-wrapper">
                            <span class="tl-tooltip tve-asset-icon-message tve-icon-paper-plane tve-icon-paper-plane" title="Asset Delivery is curerntly NOT active because there is no connection to an email delivery service. <strong>Click the icon to add a downloadable</strong>."></span>
                            <span class="tve-asset-icon-exclamation">!</span>
                    <?php } else { ?>
                        <div class="tve-asset-group-connection-status-green <?php if ($wizard == 1 && $assets_data['wizard']['proprieties']['connections'] == 1) { echo 'tve-asset-group-existing-connection-event'; } else { echo 'tve-asset-group-connection-event'; } ?> tve-asset-group-status-wrapper">
                            <span class="tl-tooltip tve-asset-icon-message tve-icon-paper-plane tve-icon-paper-plane" title="Email delivery status: OK. Click to change email delivery settings."></span>
                    <?php } ?>
                        </div>
                    <?php if ($wizard == false && $assets_data['wizard']['proprieties']['template'] == 0) { ?>
                        <div class="tve-asset-group-template-status tve-asset-group-template-event tve-asset-group-status-wrapper">
                            <span class="tl-tooltip tve-asset-icon-message tve-icon-file-text" title="Click here to create your default email template."></span>
                    <?php } elseif ($wizard == 1 && $assets_data['wizard']['proprieties']['template'] == 0) { ?>
                        <div class="tve-asset-group-template-status-red tve-asset-group-template-event tve-asset-group-status-wrapper">
                            <span class="tl-tooltip  tve-asset-icon-message tve-icon-file-text" title="Asset Delivery is curerntly NOT active because no default email template exists. <strong>Click the icon to add a downloadable</strong>."></span>
                            <span class="tve-asset-icon-exclamation">!</span>
                    <?php } else { ?>
                        <div class="tve-asset-group-template-status-green tve-asset-group-template-event tve-asset-group-status-wrapper">
                            <span class="tl-tooltip tve-asset-icon-message tve-icon-file-text" title="Default template status: OK. Click to edit the default template."></span>
                    <?php } ?>
                    </div>
                    <?php if ($wizard == false && $assets_data['wizard']['proprieties']['files'] == 0) { ?>
                        <div class="tve-asset-group-links-status tve-asset-group-links-event tve-asset-group-status-wrapper">
                            <span class="tl-tooltip tve-asset-icon-message tve-icon-cloud" title="Click here to add a downloadable asset."></span>
                    <?php } elseif ($wizard == 1 && $assets_data['wizard']['proprieties']['files'] == 0) { ?>
                        <div class="tve-asset-group-links-status-red tve-asset-group-links-event tve-asset-group-status-wrapper">
                            <span class="tl-tooltip  tve-asset-icon-message tve-icon-cloud" title="Asset Delivery is currently NOT active because no downloadable assets exist. <strong>Click the icon to add a downloadable</strong>."></span>
                            <span class="tve-asset-icon-exclamation">!</span>
                    <?php } else { ?>
                        <div class="tve-asset-group-links-status-green tve-asset-group-links-event tve-asset-group-status-wrapper">
                            <span class="tl-tooltip tve-asset-icon-message tve-icon-cloud" title="Downloadable asset status: OK."></span>
                    <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <div id="asset-delivery-table-wrapper" <?php if(($assets_data['wizard']['proprieties']['connections'] == 0 || $assets_data['wizard']['proprieties']['files'] == 0 || $assets_data['wizard']['proprieties']['template'] == 0) && $wizard == false) { echo 'style="display: none"'; } ?>>
            <div class="tve-collapse-table asset-delivery-table" id="tve-asset-delivery">
                <h2 class="tve-action-title"><?php echo __('Asset Groups', 'thrive-leads'); ?>
                    <a class="tve-leads-button tve-btn tve-btn-blue tve-asset-group-links-event" href="javascript:void(0)"
                       title="<?php echo __('Add New') ?>"
                       id="tve-asset-group-add"><?php echo __('Add New', 'thrive-leads') ?></a>
                </h2>
                <ul class="collapse-table-list" id="tve-asset-group-list"></ul>

                <div class="show-no-asset-groups" style="display: none;">
                    <p>
                        <?php echo __("You don't have any Asset Groups created yet. Click the 'Add new' button to create one.", 'thrive-leads') ?>
                    </p>
                    <a href="//fast.wistia.net/embed/iframe/09t999mxbt?popover=true"
                       class="wistia-popover[height=450,playerColor=2bb914,width=800]"><img
                            src="<?php echo TVE_LEADS_ADMIN_URL . "img/video-thumb-asset-groups.jpg" ?>" alt=""/></a>
                </div>
            </div>
            <div class="tve-asset-delivery-settings">
                <a class="tve-leads-button tve-btn tve-btn-gray tve-asset-group-dashboard"
                   href="<?php echo admin_url('admin.php?page=thrive_leads_dashboard'); ?>"
                   title="<?php echo __('Back to Dashboard') ?>"
                   id="tve-asset-group-dashboard">
                    <span class="tve-icon-double-angle-quotes"></span>
                    <?php echo __('Back to Dashboard', 'thrive-leads') ?>
                </a>
            </div>
        </div>
        <div id="tve-asset-setup" <?php  if($assets_data['wizard']['proprieties']['connections'] == 1 && $assets_data['wizard']['proprieties']['files'] == 1 && $assets_data['wizard']['proprieties']['template'] == 1  || $wizard == 1) { echo 'style="display: none"'; } ?>>
            <h1 class="tl-text-center"> <?php echo __('Thrive Leads Asset Delivery', 'thrive-leads'); ?></h1>
            <p class="tl-text-center"> <?php echo __('Automatically send download links to new subscribers. Here are the 3 steps to get started.', 'thrive-leads'); ?></p>
            <div class="tve-asset-wizard-container">
                <div class="tve-asset-wizard tve-asset-service <?php if($assets_data['wizard']['proprieties']['connections'] == 1) { echo "tve-asset-service-status-ready"; } ?>">
                    <span class="tve-asset-title tve-asset-graytext"><?php echo __('Step 1', 'thrive-leads'); ?></span>
                    <span class="tve-asset-wizard-icon tve-icon-paper-plane"></span>
                    <span class="tve-asset-wizard-type tve-asset-graytext">Email Delivery Service</span>
                    <span class="tve-asset-service-status">
                        <span class="tve-asset-whitetext">
                            <?php if($assets_data['wizard']['proprieties']['connections'] == 0) { echo "Pending"; } else { echo "<span class='tve-icon-checkmark'></span> Ready!"; }   ?>
                        </span>
                    </span>
                </div>
                <div class="tve-asset-wizard tve-asset-template <?php if($assets_data['wizard']['proprieties']['template'] == 1) { echo "tve-asset-service-status-ready"; } ?>">
                    <span class="tve-asset-title tve-asset-graytext"><?php echo __('Step 2', 'thrive-leads'); ?></span>
                    <span class="tve-asset-wizard-icon tve-icon-file-text"></span>
                    <span class="tve-asset-wizard-type tve-asset-graytext">Default Email Template</span>
                    <span class="tve-asset-service-status ">
                        <span class="tve-asset-whitetext">
                            <?php if($assets_data['wizard']['proprieties']['template'] == 0) { echo "Pending"; } else { echo "<span class='tve-icon-checkmark'></span> Ready!"; } ?>
                        </span>
                    </span>
                </div>
                <div class="tve-asset-wizard tve-asset-download-link <?php if($assets_data['wizard']['proprieties']['files'] == 1) { echo "tve-asset-service-status-ready"; } ?>">
                    <span class="tve-asset-title tve-asset-graytext"><?php echo __('Step 3', 'thrive-leads'); ?></span>
                    <span class="tve-asset-wizard-icon tve-icon-cloud"></span>
                    <span class="tve-asset-wizard-type tve-asset-graytext">First Download Link</span>
                    <span class="tve-asset-service-status">
                        <span class="tve-asset-whitetext">
                            <?php if($assets_data['wizard']['proprieties']['files'] == 0) { echo "Pending"; } else { echo "<span class='tve-icon-checkmark'></span> Ready!"; } ?>
                        </span>
                    </span>
                </div>
            </div>
            <div class="tve-leads-asset-get-details tl-text-center ">
				<p><a href="https://thrivethemes.com/tkb_item/asset-delivery/" target="_blank" class="tve-asset-graytext"><?php echo __('Click here to learn more', 'thrive-leads'); ?></a> <?php echo __('about the Asset Delivery feature', 'thrive-leads'); ?></p>            </div>
            <div class="tve-leads-asset-go-dashboard" style="display: none;">
                <p class="tl-text-center tve-small-margin"><?php echo __('Congratulations! Asset Delivery is now ready to go on this website.', 'thrive-leads'); ?></p>
                <div class="tl-center">
                    <a class="tl-play-link wistia-popover[height=450,playerColor=2bb914,width=800] tve-btn tve-btn-gray tve-btn-large tve-leads-asset-opt-in" href="//fast.wistia.net/embed/iframe/t1r77tjnee?popover=true"><?php echo __("Show Me How to Add Assets to Opt-in Forms", "thrive-cb") ?></a>&nbsp;
                    <button class="tve-btn tve-btn-green tve-btn-large tve-leads-asset-dashboard"><?php echo __("Continue to the Asset Delivery Dashboard", "thrive-cb") ?></button>
                </div>
            </div>
        </div>
    </div>

</div>