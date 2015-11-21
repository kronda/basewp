<table class="form-table postEdit countdown-modal">
    <tr>
        <th scope="row">
            <label><?php _e("Countdown color", 'thrive');?></label>
        </th>
        <td>
            <select id="thrive_shortcode_option_color">
                <?php foreach ($all_colors as $key => $c): ?>
                    <option value="<?php echo $key; ?>"><?php echo $c; ?></option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Time", 'thrive'); ?></label>
        </th>
        <td class="date-picker">
            <input readonly type="text" id='thrive_shortcode_countdown_datetime' />
            <span class="dateTimePicker"></span>
        </td>                        
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Timezone", 'thrive'); ?></label>
        </th>
        <td>
            <?php
            $timezone_offset = get_option('gmt_offset');
            $sign = ($timezone_offset < 0 ? '-' : '+');
            $min = abs($timezone_offset) * 60;
            $hour = floor($min / 60);
            $tzd = $sign . str_pad($hour, 2, '0', STR_PAD_LEFT) . ':' . str_pad($min % 60, 2, '0', STR_PAD_LEFT);
            ?>
            <span>UTC <?php echo $tzd ?></span>
        </td>
    </tr>
    <tr>
        <th scope="row">
            <label><?php _e("Fade out", 'thrive'); ?></label>
        </th>
        <td>
            <input type='checkbox' value='1' id='thrive_shortcode_countdown_fade' />
        </td>                        
    </tr> 
    <tr>
        <th scope="row">
            <label><?php _e("Countdown text", 'thrive'); ?></label>
        </th>
        <td>
            <input type='checkbox' value='1' id='thrive_shortcode_countdown_text_chk' />
            <input style="display: none;" type="text" placeholder="countdown text" id="thrive_shortcode_countdown_text">
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <input class="button button-primary" type="button" id="thrive_shortcode_btn_insert" value="<?php _e("Insert", 'thrive'); ?>" />
        </td>
    </tr>
</table>

<style>
    .ui-timepicker-div .ui-widget-header { margin-bottom: 8px; }
    .ui-timepicker-div dl { text-align: left; }
    .ui-timepicker-div dl dt { float: left; clear:left; padding: 0 0 0 5px; }
    .ui-timepicker-div dl dd { margin: 0 10px 10px 45%; }
    .ui-timepicker-div td { font-size: 90%; }
    .ui-tpicker-grid-label { background: none; border: none; margin: 0; padding: 0; }

    .ui-timepicker-rtl{ direction: rtl; }
    .ui-timepicker-rtl dl { text-align: right; padding: 0 5px 0 0; }
    .ui-timepicker-rtl dl dt{ float: right; clear: right; }
    .ui-timepicker-rtl dl dd { margin: 0 45% 10px 10px; }

</style>

<script type="text/javascript">

    jQuery(document).ready(function($) {

        jQuery('#thrive_shortcode_countdown_datetime').click(function() {
            $('.dateTimePicker').datetimepicker({
                altField: '#thrive_shortcode_countdown_datetime',
                altFieldTimeOnly: false,
                controlType: 'select',
                timeFormat: 'HH:mm'
            });
            
            if(!jQuery('.dateTimePicker').is(":visible")) {
                jQuery('.dateTimePicker').show();
            }
        });
        
        jQuery(document).on('click', '.ui-datepicker-close', function (e) {
            e.preventDefault();
            jQuery('.dateTimePicker').hide();
        });

        jQuery('#thrive_shortcode_countdown_text_chk').change(function() {
            jQuery('#thrive_shortcode_countdown_text').toggle();
        });

        jQuery("#thrive_shortcode_btn_insert").click(function() {
            var sc_options = {
                'color': jQuery("#thrive_shortcode_option_color").val(),
                'year': 0,
                'month': 0,
                'day': 0,
                'hour': 0,
                'min': 0,
                'fade': 0,
                'text': ''
            };
            if (jQuery('#thrive_shortcode_countdown_text_chk').prop('checked')) {
                sc_options.text = jQuery('#thrive_shortcode_countdown_text').val().replace(/"/g, '\'');
            } else {
                sc_options.text = "";
            }
            if (jQuery('#thrive_shortcode_countdown_fade').prop('checked')) {
                sc_options.fade = 1;
            } else {
                sc_options.fade = 0;
            }
            var datetime = jQuery('#thrive_shortcode_countdown_datetime').val();
            if (datetime == '') {
                return;
            } else {
                var parts = datetime.split(' ');
                var date = parts[0].split('/');
                var time = parts[1].split(':');

                sc_options.year = date[2];
                sc_options.month = date[0];
                sc_options.day = date[1];
                sc_options.hour = time[0];
                sc_options.min = time[1];
            }
            tb_remove();

            var sc_text = '[thrive_countdown color="' + sc_options.color + '" year="' + sc_options.year + '" month="' + sc_options.month + '" day="' + sc_options.day + '" hour="' + sc_options.hour + '" min="' + sc_options.min + '" fade="' + sc_options.fade + '" text="' + sc_options.text + '"]';

            send_to_editor(sc_text);
        });
    });

</script>