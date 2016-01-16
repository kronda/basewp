<?php
$most_recent = 0;
foreach ($download_list as $download) {
    $date = strtotime($download->date);
    if ($date > $most_recent) {
        $most_recent = $date;
    }
}
?>

<div class="manager-collapse-table-list-item">
    <h2><?php echo __('Download Manager', 'thrive-leads'); ?></h2>
    <a class="tve-prevent-click tve-btn tve-btn-icon tve-manager-details" href="javascript:void(0)">
        <span class="tve-icon-plus-circle"></span>
    </a>
</div>


<div class="tve-manager-content" style="display: none;">
    <?php if (!empty($upload['error'])): ?>
        <div class="tve-error">
            <?php echo __('You are unable to save files in the upload folder. Please check the folder <a href="https://codex.wordpress.org/Changing_File_Permissions">permissions</a>!', 'thrive-leads'); ?>
        </div>
    <?php else: ?>
        <h3><?php echo __('I want to download', 'thrive-leads'); ?>:</h3>

        <div class="tve-manager-download">
            <label class="tve-custom-select">
                <select class="tve-manager-source" name="tve-manager-source" autocomplete="off">
                    <option value="all"><?php echo __('All Contacts in Database', 'thrive-leads'); ?></option>
                    <?php if (!empty($most_recent)): ?>
                        <option value="last_download"><?php echo __('All Contacts in Database since last Download ', 'thrive-leads'); ?><?php echo date("jS F, Y H:i", $most_recent); ?></option>
                    <?php endif; ?>
                    <?php if (!empty($contacts_list->items)): ?>
                        <option value="current_report"><?php echo __('All Contacts in Current Report', 'thrive-leads'); ?></option>
                    <?php endif; ?>
                </select>
            </label>

            <div class="tve-manager-file-type">
                <span><strong><?php echo __('As file', 'thrive-leads'); ?>: </strong></span>
                <label class="tve-custom-select">
                    <select class="tve-manager-type" name="tve-manager-type">
                        <option value="excel">Excel (.xls)</option>
                        <option value="csv">Comma-Separated Values (.csv)</option>
                    </select>
                </label>
            </div>
            <a class="tve-btn tve-btn-green tve-btn-medium tve-manager-download-button"><?php echo __('Start Download', 'thrive-leads'); ?></a>
        </div>
        <h3><?php echo __('Download Archive', 'thrive-leads'); ?></h3>
        <table class="tve-downloads-table">
            <thead>
            <tr>
                <th><?php echo __('Report Type', 'thrive-leads'); ?></th>
                <th><?php echo __('Date', 'thrive-leads'); ?></th>
                <th><?php echo __('Status', 'thrive-leads'); ?></th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($download_list as $item): ?>
                <tr>
                    <td><?php echo $item->type ?></td>
                    <td><?php echo date("jS F, Y H:i", strtotime($item->date)); ?></td>
                    <td>
                        <span><?php echo $item->status_title; ?></span>
                    </td>
                    <td style="text-align: right;">
                        <?php if ($item->status == 'complete'): ?>
                            <a href="<?php echo $item->download_link; ?>" class="tve-btn tve-btn-green tve-btn-medium"><?php echo __('Download', 'thrive-leads'); ?></a>
                        <?php endif; ?>
                        <?php if ($item->status == 'complete' || $item->status == 'error'): ?>
                            <a data-id="<?php echo $item->id; ?>" class="tve-btn tve-btn-red tve-btn-medium tve-delete-download"><?php echo __('Delete', 'thrive-leads'); ?></a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div style="display: none">
            <img style="width: 200px;" class="tve-pending-spinner" src="<?php echo includes_url(); ?>js/thickbox/loadingAnimation.gif">
        </div>
    <?php endif; ?>
</div>
