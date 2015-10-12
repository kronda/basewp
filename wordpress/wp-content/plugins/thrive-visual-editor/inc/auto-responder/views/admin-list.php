<div class="wrap">

    <?php
    $connected_count = count($connected_apis);
    $available_count = count($available_apis);
    $active_tab = $connected_count == $available_count || (isset($_REQUEST['api']) && array_key_exists($_REQUEST['api'], $connected_apis)) ? 'list' : 'add';
    ?>
    <div class="tve-api-logo">
        <img src="<?php echo tve_editor_url(); ?>/inc/auto-responder/views/images/TT-logo-small.png" alt="">
    </div>
    <?php include dirname(__FILE__) . '/admin-messages.php' ?>

    <div class="tve-container">
        <div class="tve-sidebar">
            <ul class="tve-menu">
                <li<?php echo $active_tab == 'add' ? ' class="tve-menu-active"' : '' ?>><a
                        href="#tve-add-connections-tab"><?php echo __('Add connection', 'thrive-cb') ?></a></li>
                <li<?php echo $active_tab == 'list' ? ' class="tve-menu-active"' : '' ?>><a
                        href="#tve-active-connections-tab"><?php echo __('Active Connections', 'thrive-cb') ?></a></li>
            </ul>
        </div>
        <div class="tve-content">
            <div class="tve-tab<?php echo $active_tab == 'add' ? ' tve-tab-active' : '' ?>" id="tve-add-connections-tab">
                <h1><?php echo __('Add connection', 'thrive-cb') ?></h1>
                <!-- Adding connections -->
                <?php if ($available_count != $connected_count) : ?>
                    <div class="tve-add-connection-form <?php if (!$connected_count) echo "tve-add-connection-form-first"; ?>">
                        <table class="form-table">
                            <tbody>
                            <tr>
                                <th scope="row"><label><?php echo __("Connection type", "thrive-cb") ?></label></th>
                                <td>
                                    <select name="api_connection" id="api-connection-type" autocomplete="off">
                                        <option value=""><?php echo __("Connection...", "thrive-cb") ?></option>
                                        <?php foreach ($api_types as $k => $type): $empty = true; ?>
                                            <optgroup label="<?php echo $type; ?>">
                                                <?php foreach ($available_apis as $key => $api): if (array_key_exists($key, $connected_apis) || $api->getType() != $k) continue; ?>
                                                    <option value="<?php echo $key; ?>"<?php if ($current_key == $key) echo ' selected="selected"'; ?>>
                                                        <?php $empty = false; ?>
                                                        <?php echo $api->getTitle(); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                                <?php if ($empty): ?>
                                                    <option disabled><?php echo __("Empty", "thrive-cb") ?></option>
                                                <?php endif; ?>
                                            </optgroup>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                        <div class="add-connections">
                            <?php foreach ($available_apis as $key => $api) : if (array_key_exists($key, $connected_apis)) continue ?>
                                <div class="connection-type-form"<?php if ($current_key != $key) echo ' style="display: none"' ?>
                                     id="connection-type-<?php echo $key ?>">
                                    <?php $api->outputSetupForm() ?>
                                </div>
                            <?php endforeach ?>
                        </div>
                    </div>
                <?php else : ?>
                    <table class="form-table">
                        <tbody>
                        <tr>
                            <th scope="row">
                                <label><?php echo __('All API services are currently connected. You can view them by clicking on the "Active Connections" tab.', 'thrive-cb') ?></label>
                            </th>
                        </tr>
                        </tbody>
                    </table>
                <?php endif ?>
            </div>
            <div class="tve-tab<?php echo $active_tab == 'list' ? ' tve-tab-active' : '' ?>" id="tve-active-connections-tab">
                <h1><?php echo __('Active connections', 'thrive-cb') ?></h1>
                <?php if ($connected_count) : ?>
                    <table class="thrive-list-connections wp-list-table widefat fixed striped">
                        <thead>
                        <tr>
                            <th><?php echo __("Provider", "thrive-cb") ?></th>
                            <th><?php echo __("Options", "thrive-cb") ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($connected_apis as $key => $connection) : /** @var Thrive_List_Connection_Abstract $connection */ ?>
                            <tr class="thrive-list-connection clearfix<?php echo $connection->isConnected() ? ' list-connected' : '' ?>">
                                <td>
                                    <div class="thrive-list-title"><?php echo $connection->getTitle() ?></div>
                                </td>
                                <td>
                                    <div class="thrive-list-actions">
                                        <?php if ($connection->isConnected()) : ?>
                                            <a href="javascript:void(0)" class="tve-button tve-button-green thrive-list-action-form"
                                               data-connection="<?php echo $key ?>"><?php echo __("Edit", "thrive-cb") ?></a> &nbsp;
                                            <a href="<?php echo admin_url('admin.php?page=tve_api_connect&api=' . $key . '&disconnect=1') ?>"
                                               class="tve-button tve-button-red thrive-list-action-disconnect"><?php echo __("Disconnect (remove)", "thrive-cb") ?></a>
                                        <?php else : ?>
                                            <a href="javascript:void(0)" class="thrive-list-action-form" data-connection="<?php echo $key ?>"><?php echo __("Connect", "thrive-cb") ?></a>
                                        <?php endif ?>
                                    </div>
                                    <div id="tve-list-setup-<?php echo $key ?>" class="thrive-list-setup"<?php echo $current_key != $key ? 'style="display:none"' : '' ?>>
                                        <?php $connection->outputSetupForm() ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
        <div style="clear:both"></div>
        <div class="clearfix" style="width: 80.5%; padding-left: 221px;text-align: right">
            <a href="<?php echo admin_url('admin.php?page=thrive_api_error_log'); ?>"><?php echo __("Error logs", "thrive-cb") ?></a>
        </div>
    </div>
</div>
<script type="text/javascript">
    jQuery(function () {
        jQuery('.thrive-list-connections').on('click', '.thrive-list-action-form', function () {
            var $container = jQuery(this).parents('.thrive-list-connection');
            $container.find('.thrive-list-setup').fadeToggle();
        });
        jQuery('.tve-add-connection').on('click', function () {
            jQuery('.tve-add-connection-form').fadeToggle();
        });
        jQuery('#api-connection-type').on('change', function () {
            jQuery('.connection-type-form').hide();
            jQuery('#connection-type-' + jQuery(this).val()).show();
        });
        jQuery('.thrive-list-connections form, .add-connections form').on('submit', function () {
            jQuery(this).find('button[type="submit"]').attr('disabled', 'disabled');
        });
        //tabs
        jQuery(".tve-menu li a").click(function (e) {
            e.preventDefault();
            jQuery(this).parent().parent().find('.tve-menu-active').removeClass('tve-menu-active');
            jQuery(this).parent().addClass('tve-menu-active');
            var openTab = jQuery(this).attr('href');
            jQuery('.tve-content').find('.tve-tab-active').removeClass('tve-tab-active');
            jQuery('.tve-content').find(openTab).addClass('tve-tab-active');
        });
    });
</script>