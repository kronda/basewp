<div id="tve-content">
    <div id="tve-reporting">
        <div class="tve-header">
            <div class="tve-logo">
                <?php echo '<img src="' . plugins_url('thrive-leads/admin/img') . '/logo.png" > '; ?>
            </div>
        </div>
        <div class="panel">
            <div class="panel-heading">
                <h1 class="panel-title"><?php echo __('Reporting', 'thrive-leads'); ?></h1>
            </div>
            <div class="panel-body">
                <form>
                    <div class="tve-report-type-title">

                    </div>

                    <div class="tve-report-type">
                        <label class="tve-custom-select"><?php echo __('Show report', 'thrive-leads'); ?>:
                            <select name="report_type" id="report_type">
                                <option
                                    value="Conversion"><?php echo __('Conversion Report', 'thrive-leads'); ?></option>
                                <option
                                    value="ConversionRate"><?php echo __('Conversion Rate Report', 'thrive-leads'); ?></option>
                                <option
                                    value="CumulativeConversion"><?php echo __('Cumulative Conversions Report', 'thrive-leads'); ?></option>
                                <option
                                    value="ComparisonChart"><?php echo __('Comparison Report', 'thrive-leads'); ?></option>
                                <option
                                    value="LeadReferral"><?php echo __('Lead Referral Report', 'thrive-leads'); ?></option>
                                <option
                                    value="LeadTracking"><?php echo __('Lead Tracking Report', 'thrive-leads'); ?></option>
                            </select>
                        </label>
                    </div>

                    <div class="tve-report-filters">
                        <div>
                            <span><?php echo __('Start Date', 'thrive-leads'); ?>:</span>
                            <input type="text" name="tve-report-start-date" id="tve-report-start-date"/>
                            <span class="tve-icon-calendar start-date-calendar"></span>
                        </div>

                        <div>
                            <span><?php echo __('End Date', 'thrive-leads'); ?>:</span>
                            <input type="text" name="tve-report-end-date" id="tve-report-end-date"/>
                            <span class="tve-icon-calendar end-date-calendar"></span>
                        </div>

                        <div class="tve-chart-source">
                            <label class="tve-custom-select"><?php echo __('Source', 'thrive-leads'); ?>:
                                <select class="tve-chart-source-select" name="tve-chart-source" autocomplete="off">
                                    <option value="-1"><?php echo __('All', 'thrive-leads') ?></option>
                                    <optgroup label="<?php echo __('Lead Groups', 'thrive-leads'); ?>">
                                        <?php if (!empty($reporting_data['lead_groups'])): ?>
                                            <?php foreach ($reporting_data['lead_groups'] as $group) : ?>
                                                <option
                                                    value="<?php echo $group->ID ?>"><?php echo $group->post_title ?></option>
                                            <?php endforeach ?>
                                        <?php else: ?>
                                            <option value="-1" disabled>(empty)</option>
                                        <?php endif; ?>
                                    </optgroup>
                                    <optgroup label="<?php echo __('Shortcodes', 'thrive-leads'); ?>">
                                        <?php if (!empty($reporting_data['shortcodes'])): ?>
                                            <?php foreach ($reporting_data['shortcodes'] as $shortcode) : ?>
                                                <option
                                                    value="<?php echo $shortcode->ID ?>"><?php echo $shortcode->post_title ?></option>
                                            <?php endforeach ?>
                                        <?php else: ?>
                                            <option value="-1" disabled>(empty)</option>
                                        <?php endif; ?>
                                    </optgroup>
                                    <optgroup label="<?php echo __('2 Step Lightboxes', 'thrive-leads'); ?>">
                                        <?php if (!empty($reporting_data['two_step_lightbox'])): ?>
                                            <?php foreach ($reporting_data['two_step_lightbox'] as $tsl) : ?>
                                                <option
                                                    value="<?php echo $tsl->ID ?>"><?php echo $tsl->post_title ?></option>
                                            <?php endforeach ?>
                                        <?php else: ?>
                                            <option value="-1" disabled>(empty)</option>
                                        <?php endif; ?>
                                    </optgroup>
                                </select>
                            </label>
                        </div>

                        <div class="tve-chart-interval">
                            <label class="tve-custom-select"><?php echo __('Graph Interval', 'thrive-leads'); ?>:
                                <select autocomplete="off" class="tve-chart-interval-select" name="tve-chart-interval">
                                    <option selected value="day"><?php echo __('Daily', 'thrive-leads'); ?></option>
                                    <option value="week"><?php echo __('Weekly', 'thrive-leads'); ?></option>
                                    <option value="month"><?php echo __('Monthly', 'thrive-leads'); ?></option>
                                </select>
                            </label>
                        </div>
                    </div>
                    <div style="clear: both"></div>
                </form>
                <div class="relative">
                    <div id="tve-report-chart" style="height: 600px;"></div>
                    <div class="tve-chart-overlay" style="display: none">
                        <div class="tve-overlay-text">
                            <h1><?php echo __('No Report Data (Yet)', 'thrive-leads'); ?></h1>

                            <div>
                                <?php echo __('Here you will see a graph with the report data from all of your forms. Currently there is no data to display yet.', 'thrive-leads'); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="pagination-top" class="tl-pagination">
        </div>
        <div id="tve-report-meta">
        </div>
        <div id="pagination-bottom" class="tl-pagination">
        </div>
    </div>
</div>

<style>
    .tve-report-filters {
        margin-top: 30px;
    }

    .tve-report-filters div {
        float: left;
        margin-right: 20px;
    }

    #tve-report-chart {
        margin: 30px auto;
    }

    td {
        padding: 0 24px;
        text-align: center;
    }
</style>
