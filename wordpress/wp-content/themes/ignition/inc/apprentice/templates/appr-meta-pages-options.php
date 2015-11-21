<table class="form-table postEdit">
    <tr>
        <th scope="row">
            <label for="thrive_post_template"> <?php _e("Post Template", 'thrive'); ?></label>
        </th>
        <td>
            <select id='thrive_post_template' name='thrive_meta_post_template'>
                <?php foreach ($post_templates as $tpl_name): ?>
                <?php $selected = ($tpl_name == $value_post_template) ? "selected" : ""; echo "<option value='" . $tpl_name . "' " . $selected . ">" . $tpl_name . "</option>";?>
                <?php endforeach ?>
            </select>
        </td>
    </tr>
</table>