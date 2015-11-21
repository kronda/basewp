<form action="" method="post">
    <table class="form-table">
        <tr>
            <th scope="row"><?php echo __("Upload Fonts", $this->domain) ?></th>
            <td>
                <input type="hidden" id="thrive_attachment_id" name="attachment_id"/>
                <input type="text" id="thrive_attachment_name" readonly="readonly" value="<?php echo !empty($this->font_pack['filename']) ? $this->font_pack['filename'] : '' ?>">
                <br />
                <input type="button" class="thrive_options pure-button upload" value="<?php echo __("Upload", $this->domain) ?>" id="thrive_upload">
                <input type="submit" class="thrive_options pure-button remove" value="<?php echo __("Remove", $this->domain) ?>" id="thrive_remove">
            </td>
        </tr>
        <tr>
            <th scope="row"><?php echo __("Save options", $this->domain) ?></th>
            <td>
                <input type="submit" value="<?php echo __("Save and Generate Fonts", $this->domain) ?>" class="button button-primary"/>
            </td>
        </tr>
    </table>
</form>
