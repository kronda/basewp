<?php $animation_classes = ''; /* render specific settings for Thrive Lightbox actions */

if ($this->success_message) : ?>
    <br>
    <div class="tve_message tve_success" id="tve_landing_page_msg"><?php echo $this->success_message ?></div>
<?php endif ?>
<h5>Thrive Lightbox Settings</h5>

<table class="tve_no_brdr">
    <tr>
        <td width="35%">Which Thrive Lightbox should be displayed ?</td>
        <td width="65%">
            <select name="l_id" class="tve_ctrl_validate" data-validators="required">
                <option value="">Select Lighbox</option>
                <?php foreach ($this->lightboxes as $lightbox) : ?>
                    <option value="<?php echo $lightbox->ID ?>"<?php
                    echo !empty($this->config['l_id']) && $this->config['l_id'] == $lightbox->ID ? ' selected="selected"' : '' ?>><?php echo $lightbox->post_title ?></option>
                <?php endforeach ?>
            </select>
            <?php $add_new_lightbox = $this->for_landing_page ? 'Generate new Landing Page Lightbox' : 'Create new Lightbox' ?>
            &nbsp; <a href="javascript:void(0)" class="tve_custom_event_action" data-action="add_lightbox"><?php echo $add_new_lightbox ?></a>
        </td>
    </tr>
    <tr>
        <td>Lightbox animation</td>
        <td>
            <select name="l_anim" id="tve-animation-preview">
                <?php foreach ($this->_animations as $value => $label) : $animation_classes .= ($animation_classes ? ' ' : '') . 'tve_lb_anim_' . $value; ?>
                    <option value="<?php echo $value ?>"<?php
                    echo !empty($this->config['l_anim']) && $this->config['l_anim'] == $value ? ' selected="selected"' : '' ?>><?php echo $label ?></option>
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
    <tr>
        <td style="vertical-align: top">Animation preview</td>
        <td>
            <div id="tve-animation-target" class="tve_p_lb_background" style="position: static !important">
                <div class="tve_p_lb_content" style="float: left">
                    <img src="<?php echo tve_editor_url() ?>/editor/css/images/tcb-logo-large.png" alt=""/>
                </div>
            </div>
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

        function do_animation() {
            clearTimeout(t_id);
            var animation = $select.val();
            $target.removeClass("<?php echo $animation_classes ?> tve_lb_opening").addClass('tve_lb_anim_' + animation);
            setTimeout(function () {
                $target.addClass('tve_lb_opening');
            }, 30);

        }

        setTimeout(function () {
            do_animation();
        }, 100);
    });
</script>