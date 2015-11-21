<table class="widefat">
    <thead>
        <tr>
            <td>
                <?php _e("Field Number", 'thrive'); ?>
            </td>
            <td>
                <?php _e("Field Properties", 'thrive'); ?>
            </td>
            <td>
                <?php _e("Field Label / Description", 'thrive'); ?>
            </td>
        </tr>
    </thead>
    <tbody>
    <?php $i = 1; ?>
    <?php foreach ($parsed_responder_code['elements'] as $key => $field): ?>
        <tr>
            <td><?php echo $i++; ?></td>
            <td>
                <input type="text" readonly value="<?php echo $field['name']; ?>" class="thrive_option_field"
                       data-type="<?php echo $field['type'] ?>"
                       data-encoded-name="<?php echo $field['encoded_name'] ?>"/>
            </td>
            <td>
                <?php
                $field_label_value = "";
                if ($optinFieldsArray && isset($optinFieldsArray[$field['encoded_name']]) && $optinFieldsArray[$field['encoded_name']]) {
                    @$field_label_value = is_array($optinFieldsArray[$field['encoded_name']]) ? $optinFieldsArray[$field['encoded_name']]['label'] : $optinFieldsArray[$field['encoded_name']];
                }
                ?>
                <input type="text" value="<?php echo $field_label_value; ?>" id="<?php echo $field['encoded_name'] ?>"/>
            </td>
        </tr>
    <?php endforeach; ?>
    <tr>
        <td></td>
        <td></td>
        <td>
            <input class="pure-button pure-button-success" type="button" id="thrive_btn_save_autoresponder_fields"
                   value="<?php _e("Save Labels", 'thrive'); ?>"/>
        </td>
    </tr>
    </tbody>
</table>
