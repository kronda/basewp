<?php
if ($this->success_message) : ?>
    <br>
    <div class="tve_message tve_success" id="tve_landing_page_msg"><?php echo $this->success_message ?></div>
<?php endif ?>
<h5><?php echo isset($title) ? $title : __('Switch State Settings', 'thrive-leads') ?></h5>

<table class="tve_no_brdr">
    <tr>
        <td width="35%"><?php echo __('Which state should be displayed ?', 'thrive-leads') ?></td>
        <td width="65%">
            <select name="s" class="tve_ctrl_validate" data-validators="required">
                <option value=""><?php echo __('Select state', 'thrive-leads') ?></option>
                <?php foreach ($this->states as $state) : ?>
                    <option value="<?php echo $state['key'] ?>"<?php
                    echo !empty($this->config['s']) && $this->config['s'] == $state['key'] ? ' selected="selected"' : '' ?>><?php echo $state['state_name'] ?></option>
                <?php endforeach ?>
            </select>
        </td>
    </tr>
    <tr>
        <td colspan="2">&nbsp;</td>
    </tr>
    <tr>
        <td colspan="2">&nbsp;</td>
    </tr>
</table>
