<form action="" method="post">
    <table class="form-table">
        <tr>
            <th scope="row">Upload Icon Pack</th>
            <td>
                <input type="hidden" name="tve_save_icon_pack" value="1">
                <input type="text" value="<?php echo $this->icon_pack_name ?>" id="tve_icon_pack_file" name="tve_icon_pack[url]" class="thrive_options" readonly="readonly">
                <input type="hidden" value="<?php echo $this->icon_pack_id ?>" id="tve_icon_pack_file_id" name="attachment[id]">
                <br>
                <input type="button" value=" Upload " id="tve_icon_pack_upload" class="thrive_options pure-button upload">
                <input type="button" value=" Remove " id="tve_icon_pack_remove" class="thrive_options pure-button clear-field remove">
            </td>
        </tr>
        <tr>
            <th scope="row">Save options</th>
            <td>
                <input type="submit" value="Save and Generate Icons" class="button button-primary" id="tve_icon_pack_save">
            </td>
        </tr>
    </table>
</form>