<div id="thrive-api-list">
    <div class="tve_clear" style="height:20px;"></div>
    <?php if (!empty($selected_api)) : ?>
        <?php $list_subtitle = $selected_api->getListSubtitle() ?>
        <p class="normal-margin"><?php echo empty($list_subtitle) ? 'Choose your mailing list:' : $list_subtitle ?></p>
        <?php if (false === $lists) : /** this means there's been an error while connecting / communicating to the API */ ?>
            <p class="error-message" style="color: red">
                Error while communicating with the service: <?php echo $connection->getApiError() ?>
            </p>
        <?php else : ?>
            <select id="thrive-api-list-select"<?php echo (empty($lists)) ? ' disabled' : '' ?> style="width: 250px;">
                <?php if (empty($lists)) : ?>
                    <option value="">No list available</option>
                <?php endif ?>
                <?php foreach ($lists as $list) : ?>
                    <option value="<?php echo $list['id'] ?>"<?php echo !empty($selected_list) && $selected_list == $list['id'] ? ' selected="selected"' : '' ?>><?php echo $list['name'] ?></option>
                <?php endforeach ?>
            </select>
            &nbsp;&nbsp;&nbsp;
            <a href="javascript:void(0)" class="tve_click" data-ctrl="function:auto_responder.api.reload_lists" data-force-fetch="1"" data-api="<?php echo $selected_api->getKey() ?>">Reload</a>
            <?php if (!empty($lists)) : ?>
                <div class="tve_clear" style="height:20px;"></div>
                <?php echo $selected_api->renderExtraEditorSettings(empty($extra_settings[$selected_api->getKey()]) ? array() : $extra_settings[$selected_api->getKey()]) ?>
            <?php endif ?>
        <?php endif ?>
    <?php endif ?>
</div>