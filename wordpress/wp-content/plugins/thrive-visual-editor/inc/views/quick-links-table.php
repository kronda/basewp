<table style="width: 100%;text-align: left;">
    <thead>
        <tr>
            <th><?php echo __("Title", "thrive-cb"); ?></th>
            <th><?php echo __("Post Type", "thrive-cb"); ?></th>
        </tr>
    </thead>
    <tbody>
    <?php if ($postList) { ?>
        <?php foreach ($postList as $item) : ?>
            <tr class="tve_text tve_select_quick_link_post tve_click" data-ctrl-click="controls.quick_link.updateQuickLink" rel="<?php echo $item['url'] ?>">
                <td><?php echo $item['label'] ?></td>
                <td><?php echo $item['type'] ?></td>
            </tr>
        <?php endforeach ?>
    <?php } else { ?>
        <tr>
            <td colspan="2">
                <p><?php echo __("No results found!", "thrive-cb") ?></p>
            </td>
        </tr>
    <?php } ?>
    </tbody>
</table>