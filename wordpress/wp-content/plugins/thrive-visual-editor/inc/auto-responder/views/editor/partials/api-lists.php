<div id="thrive-api-list">
    <div class="tve-sp"></div>
    <?php if (!empty($selected_api)) : ?>
        <?php $list_subtitle = $selected_api->getListSubtitle() ?>
        <h6><?php echo empty($list_subtitle) ? 'Choose your mailing list:' : $list_subtitle ?></h6>
        <?php if (false === $lists) : /** this means there's been an error while connecting / communicating to the API */ ?>
            <p class="error-message" style="color: red">
                <?php echo __('Error while communicating with the service:', 'thrive-cb')?> <?php echo $selected_api->getApiError() ?>
            </p>
        <?php else : ?>
            <div class="tve_lightbox_select_holder tve_lightbox_input_inline tve_lightbox_select_inline">
                <select id="thrive-api-list-select"<?php echo (empty($lists)) ? ' disabled' : '' ?> >
                    <?php if (empty($lists)) : ?>
                        <option value=""><?php echo __('No list available', 'thrive-cb') ?></option>
                    <?php endif ?>
                    <?php foreach ($lists as $list) : ?>
                        <option value="<?php echo $list['id'] ?>"<?php echo !empty($selected_list) && $selected_list == $list['id'] ? ' selected="selected"' : '' ?>><?php echo $list['name'] ?></option>
                    <?php endforeach ?>
                </select>
            </div>
            &nbsp;&nbsp;&nbsp;
            <a href="javascript:void(0)" class="tve_click tve_lightbox_link tve_lightbox_link_refresh" data-ctrl="function:auto_responder.api.reload_lists" data-force-fetch="1"" data-api="<?php echo $selected_api->getKey() ?>"><?php echo __('Reload', 'thrive-cb')?></a>
            <?php if (!empty($lists)) : ?>
                <?php echo $selected_api->renderExtraEditorSettings(empty($extra_settings[$selected_api->getKey()]) ? array() : $extra_settings[$selected_api->getKey()]) ?>
            <?php endif ?>
        <?php endif ?>
    <?php endif ?>
</div>
