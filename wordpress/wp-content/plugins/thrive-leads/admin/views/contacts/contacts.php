<div id="tve-content">
    <div id="tve-contacts">
        <div class="tve-header">
            <div class="tve-logo">
                <?php echo '<img src="' . plugins_url('thrive-leads/admin/img') . '/logo.png" > '; ?>
            </div>
        </div>
        <h1>Lead Export</h1>

        <form method="get" action="<?php admin_url('admin.php'); ?>">
            <input type="hidden" name="page" value="thrive_leads_contacts"/>

            <div id="tve-contacts-table">
                <?php $contacts_list->display(); ?>
            </div>
        </form>
        <div id="tve-download-manager"><?php require_once(dirname(__FILE__) . '/contacts_download.php') ?></div>
    </div>
</div>

<div id="tve-email-lb" style="display: none;">
    <table>
        <tr>
            <td><?php echo __('Email Address', 'thrive-leads'); ?></td>
            <td><input type="text" style="width: 75%" id="tve-email-address" data-default-value="<?php echo $saved_email; ?>"></td>
        </tr>
        <tr>
            <td></td>
            <td>
                <label>
                    <input type="checkbox" id="tve-save-email" <?php echo empty($saved_email) ? '' : 'checked'; ?>>
                    <?php echo __('Remember this email address for future use.', 'thrive-leads'); ?>
                </label>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
                <a style="float:right;" href="javascript:void(0)" class="tve-btn tve-btn-green tve-btn-medium tve-send-email"><?php echo __('Send', 'thrive-leads'); ?></a>
                <input type="hidden" id="tve-contact-id">

                <div style="clear: both" id="tve-email-response"></div>
            </td>
        </tr>
    </table>
</div>