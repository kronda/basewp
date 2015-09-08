<div id="thrive-api-connections">
    <div class="tve_clear" style="height:20px;"></div>
    <?php if (empty($connected_apis)) : ?>
        <p class="normal-margin">You currently don't have any API integrations set up.</p>
        <div class="tve_clear" style="height:20px;"></div>
        <p class="normal-margin"><a href="<?php echo admin_url('admin.php?page=tve_api_connect') ?>" target="_blank">Click here to set up a new API connection</a></p>
    <?php else : ?>
        <p class="normal-margin">Choose from your list of existing API connections or <a href="<?php echo admin_url('admin.php?page=tve_api_connect') ?>" target="_blank">add a new integration</a></p>
        <select id="thrive-api-connections-select" class="tve_change" data-ctrl="function:auto_responder.api.api_get_lists" autocomplete="off" style="width: 250px;">
            <?php foreach ($connected_apis as $key => $api) : ?>
                <option value="<?php echo $key ?>"<?php echo $edit_api_key == $key ? ' selected="selected"' : '' ?>><?php echo $api->getTitle() ?></option>
            <?php endforeach ?>
        </select>
        &nbsp;&nbsp;&nbsp;
        <a href="javascript:void(0)" data-ctrl="function:auto_responder.api.reload_apis" class="tve_click">Reload</a>
    <?php endif ?>
</div>
