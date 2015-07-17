<?php /* lists all the events assigned to an element */ ?>
<h3>
    <?php if ($scope == 'page') : ?>
        Page Event Manager
    <?php else : ?>
        Event Manager
    <?php endif ?>
</h3>

<h5>Existing Events</h5>
<?php $error_indexes = array() ?>
<?php if (empty($events)) : ?>
    <?php if ($scope == 'page') : ?>
        <p>There are no events currently set up for this page</p>
    <?php else : ?>
        <p>There are no events currently set up for this element</p>
    <?php endif ?>
<?php else : ?>
    <div class="tve_event_manager">
        <table>
            <tr class="tve_head">
                <th width="30%">Trigger</th>
                <th width="40%">Action</th>
                <th width="30%">&nbsp;</th>
            </tr>
            <?php foreach ($events as $index => $event) : ?>
                <?php $actions[$event['a']]->setConfig(empty($event['config']) ? array() : $event['config']) ?>
                <?php if (!$actions[$event['a']]->validateConfig()) : /* we need to make sure that the current event is not corrupted. Example: user deleted a lightbox */
                    $error_indexes []= $index ?>
                <?php else : ?>
                    <tr>
                        <td><?php echo $triggers[$event['t']]->getName() ?></td>
                        <td>
                            <?php echo $actions[$event['a']]->getName(); if (!empty($event['config'])) echo $actions[$event['a']]->getSummary() ?>
                            <?php if (method_exists($actions[$event['a']], 'getRowActions')) : ?>
                                <?php echo $actions[$event['a']]->getRowActions() ?>
                            <?php endif ?>
                        </td>
                        <td style="text-align: right">
                            <a href="javascript:void(0)" data-action="edit" data-index="<?php echo $index ?>" class="tve_event_onclick">Edit</a> &nbsp; &nbsp;
                            <a href="javascript:void(0)" data-action="remove" data-index="<?php echo $index ?>" class="tve_event_onclick">Remove</a>
                        </td>
                    </tr>
                <?php endif ?>
            <?php endforeach ?>
        </table>
    </div>
<?php endif ?>
<div class="tve_clear" style="height: 20px;"></div>
<div class="tve_landing_pages_actions">
    <div class="tve_btn_default tve_right tve_event_onclick" data-action="close">
        <div class="tve_preview">Close</div>
    </div>
    <div class="tve_event_onclick tve_btn_success tve_right" data-action="add">
        <div class="tve_update">Add Event</div>
    </div>
</div>
<div class="tve_clear"></div>
<?php if (!empty($error_indexes)) : ?>
    <input type="hidden" id="tve_event_list_errors" value="<?php echo implode(',', $error_indexes) ?>" />
<?php endif ?>