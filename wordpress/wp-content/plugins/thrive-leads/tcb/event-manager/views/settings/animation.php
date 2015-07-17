<?php $animation_classes = ''; /* render specific settings for Thrive Lightbox actions */ ?>
<h5>Animation Settings</h5>

<table class="tve_no_brdr">
    <tr>
        <td width="35%">Which animation should be performed?</td>
        <td width="65%">
            <select name="anim" id="tve-animation-preview">
                <?php foreach ($this->_animations as $value => $label) : $animation_classes .= ($animation_classes ? ' ' : '') . 'tve_anim_' . $value ?>
                    <option value="<?php echo $value ?>"<?php
                    echo !empty($this->config['anim']) && $this->config['anim'] == $value ? ' selected="selected"' : '' ?>><?php echo $label ?></option>
                <?php endforeach ?>
            </select>
        </td>
    </tr>
    <tr><td colspan="2">&nbsp;</td></tr>
    <tr><td colspan="2">&nbsp;</td></tr>
    <tr>
        <td style="vertical-align: top">Animation preview</td>
        <td>
            <img src="<?php echo tve_editor_url() ?>/editor/css/images/tcb-logo-large.png" alt="" class="tve_ea_thrive_animation" id="tve-animation-target" />
        </td>
    </tr>
</table>

<script type="text/javascript">
    jQuery(function () {
        var $select = jQuery('#tve-animation-preview').change(function () {
            setTimeout(function () {
                do_animation();
            }, 100);
        }), $target = jQuery('#tve-animation-target'), t_id = null;

        function do_animation()
        {
            clearTimeout(t_id);
            var animation = $select.val();
            $target.removeClass("<?php echo $animation_classes ?> tve_anim_start").addClass('tve_anim_' + animation);
            setTimeout(function () {
                $target.addClass('tve_anim_start');
            }, 500);
        }
        setTimeout(function () {
            do_animation();
        }, 100);
    });
</script>