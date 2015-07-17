<?php
$config = $this->config;
?>
<h5>Exit Intent Settings</h5>

<table class="tve_no_brdr">
    <tr>
        <td width="35%" style="vertical-align: middle">
            Perform Action also on Mobile Devices
        </td>
        <td width="65%" style="vertical-align: middle">
            <input id="ei_show_mobile" type="checkbox" name="e_mobile" value="1"<?php if (!empty($config['e_mobile'])) echo ' checked="checked"' ?>>
        </td>
    </tr>
    <tr<?php if (empty($config['e_mobile'])) echo ' class="tve_hidden"' ?> id="e_use_mobile">
        <td width="35%" style="vertical-align: middle">
            It is not possible to detect exit intent on a mobile device, so instead the selected action will be performed after a time delay. How long after page load
            before the action is performed on mobile devices ?
        </td>
        <td width="65%" style="vertical-align: middle">
            <div class="tve_slider" style="width: 300px; display: inline-block;">
                <div class="tve_slider_element" id="tve_e_timer_slider"></div>
            </div>
            &nbsp;&nbsp;
            <input class="" type="text" name="e_delay" id="tve_e_timer" value="<?php echo !empty($config['e_delay']) ? (int)$config['e_delay'] : '30' ?>" size="3">
            &nbsp; Seconds

            <div class="clear"></div>
        </td>
    </tr>
</table>
<script type="text/javascript">
    (function ($) {
        var $e_slider_input = $('#tve_e_timer').change(function () {
                $e_slider.slider('value', parseInt(this.value));
            }),
            $e_slider = $('#tve_e_timer_slider').slider({
                min: 1,
                max: 300,
                step: 1,
                slide: function (event, ui) {
                    $e_slider_input.val(ui.value);
                }
            });
        $e_slider_input.change();
        $('#ei_show_mobile').change(function () {
            var $this = $(this), $target = $('#e_use_mobile');
            $this.is(':checked') && $target.removeClass('tve_hidden');
            !$this.is(':checked') && $target.addClass('tve_hidden');
        });
    })(jQuery);
</script>