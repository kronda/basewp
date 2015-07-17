<?php
$config = $this->config;
?>
<h5>Timer (duration after page load) Settings</h5>


<table class="tve_no_brdr">
    <tr>
        <td width="35%" style="vertical-align: middle">How many seconds after page load should the event be triggered ?</td>
        <td width="65%" style="vertical-align: middle">
            <div class="tve_slider" style="width: 300px; display: inline-block;">
                <div class="tve_slider_element" id="tve_t_timer_slider"></div>
            </div>
            &nbsp;&nbsp;
            <input class="" type="text" name="t_delay" id="tve_t_timer" value="<?php echo !empty($config['t_delay']) ? (int)$config['t_delay'] : '30' ?>" size="3">
            &nbsp; Seconds

            <div class="clear"></div>
        </td>
    </tr>
</table>
<script type="text/javascript">
    (function ($) {
        $(function() {
            var $slider_input = $('#tve_t_timer').change(function () {
                $t_slider.slider('value', parseInt(this.value));
            }),
            $t_slider = $('#tve_t_timer_slider').slider({
                min: 1,
                max: 300,
                step: 1,
                slide: function (event, ui) {
                    $slider_input.val(ui.value);
                }
            });
            $slider_input.change();
        });
    })(jQuery);
</script>