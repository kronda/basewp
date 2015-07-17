<?php
$all_fonts = _thrive_get_font_family_array();

$link = 'https://www.googleapis.com/webfonts/v1/webfonts?key=';
$key = 'AIzaSyDJhU1bXm2YTz_c4VpWZrAyspOS37Nn-kI';
$request = wp_remote_get($link . $key, array('sslverify' => false));
$response = json_decode(wp_remote_retrieve_body($request), true);
$google_fonts = $response['items'];

$prefered_fonts = array();
foreach ($google_fonts as $key => $font) {
    if (array_key_exists($font['family'], $all_fonts)) {
        $prefered_fonts[] = $font;
    }
}
$new_font_id = $_GET['font_id'];

$admin_font_manager_link = "admin-ajax.php?action=thrive_font_manager_add";
if (isset($_GET['font_action']) && $_GET['font_action'] == 'update') {
    $font_id = $_GET['font_id'];
    $options = json_decode(get_option('thrive_font_manager_options'), true);
    foreach ($options as $option) {
        if ($option['font_id'] == $font_id) {
            $font = $option;
        }
    }
    $admin_font_manager_link = "admin-ajax.php?action=thrive_font_manager_edit";
}
?>

<div class="thrive-page-settings" style="width: auto; margin-right:20px;">
    <div id="fontPreview" style="font-size: 20px; margin-bottom: 10px;">
        Grumpy wizards make toxic brew for the evil Queen and Jack.
    </div>
    <hr>
    <div>
        <input type="radio" name="display_fonts" id="ttfm_google_fonts"/> Show all fonts
        <input type="radio" name="display_fonts" id="ttfm_prefered_fonts" checked/> Recommended Fonts Only
        <a style="float: right" target="_blank" href="//www.google.com/fonts">View All Font Previews</a>
    </div>

    <div>
        <br/>
        <select id="ttfm_fonts">
            <option value="none"></option>
            <?php foreach ($prefered_fonts as $name => $f): ?>
                <option
                    data-url='<?php echo json_encode($f['files']); ?>' <?php if (isset($f['font_name']) && $f['family'] == $f['font_name']) echo 'selected'; ?>
                    value="<?php echo $f['family']; ?>">
                    <?php echo $f['family']; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br/><br/>
    </div>

    <div>
        <div id="ttfm-font-regular" style="display: inline-block; vertical-align: top; margin-right: 30px"></div>
        <div id="ttfm-font-bold" style="display: inline-block; vertical-align: top; margin-right: 30px"></div>
        <div id="ttfm-font-subsets" style="display: inline-block; vertical-align: top"></div>
    </div>
    <div style="clear: both"></div>
    <hr>
    <div>
        <table>
            <tr>
                <td>Class</td>
                <td>
                    <input id="ttfm-font-class" type="text" readonly value="ttfm<?php echo $new_font_id; ?>">
                </td>
            </tr>
            <tr>
                <td>Size</td>
                <td id="ttfm-font-size">
                    <input type="number" value="<?php
                    if (isset($font['font_size']))
                        echo intval($font['font_size']);
                    else
                        echo '1.6';
                    ?>">
                    <select>
                        <option <?php if (isset($font['font_size']) && strpos($font['font_size'], 'em') > 0) echo 'selected'; ?>>
                            em
                        </option>
                        <option <?php if (isset($font['font_size']) && strpos($font['font_size'], 'px') > 0) echo 'selected'; ?>>
                            px
                        </option>
                        <option <?php if (isset($font['font_size']) && strpos($font['font_size'], '%') > 0) echo 'selected'; ?>>
                            %
                        </option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Line Height</td>
                <td id="ttfm-font-height">
                    <input type="number" value="<?php
                    if (isset($font['font_height']))
                        echo intval($font['font_height']);
                    else
                        echo '1.6';
                    ?>">
                    <select>
                        <option <?php if (isset($font['font_height']) && strpos($font['font_height'], 'em') > 0) echo 'selected'; ?>>
                            em
                        </option>
                        <option <?php if (isset($font['font_height']) && strpos($font['font_height'], 'px') > 0) echo 'selected'; ?>>
                            px
                        </option>
                        <option <?php if (isset($font['font_height']) && strpos($font['font_height'], '%') > 0) echo 'selected'; ?>>
                            %
                        </option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Color</td>
                <td>
                    <input type="text" value="<?php if (isset($font['font_color'])) echo $font['font_color']; ?>"
                           class="wp-color-picker-field" data-default-color="#ffffff"/>
                </td>
            </tr>
            <tr>
                <td>Custom CSS</td>
                <td>
                    <textarea
                        id="ttfm-custom-css"><?php if (isset($font['custom_css'])) echo $font['custom_css']; ?></textarea>
                </td>
            </tr>
        </table>
    </div>
    <hr>
    <input id="ttfm_save_font_options" type="button" value="Save" class="button button-primary"
           style="float: right;margin: 20px;">

    <div class="clear"></div>
</div>


<script type="text/javascript">
    jQuery('#TB_window').addClass('fmp');
    jQuery(document).ready(function ($) {
        $('.wp-color-picker-field').wpColorPicker();
        var prefered_fonts = <?php echo json_encode($prefered_fonts); ?>;
        var google_fonts = <?php echo json_encode($google_fonts); ?>;
        var update_font = '<?php
                           if (isset($font['font_name']))
                               echo $font['font_name'];
                           else
                               echo 0;
                    ?>';
        var font_variants;
        var current_font = <?php echo json_encode(empty($font) ? array() : $font) ?>;

        jQuery('#ttfm_fonts').change(function () {

            jQuery('#ttfm-font-regular').html('<b>Regular Style</b> <br />');
            jQuery('#ttfm-font-bold').html('<b>Bold Style</b> <br />');
            jQuery('#ttfm-font-subsets').html('<b>Character Set</b><br/>');
            for (var i in google_fonts) {
                if (google_fonts[i].family == jQuery(this).val()) {
                    font_variants = google_fonts[i].variants;
                    for (var j in google_fonts[i].variants) {
                        jQuery('<input/>').attr({
                            type: 'radio',
                            name: 'ttfm-font-style',
                            value: font_variants[j]
                        }).appendTo('#ttfm-font-regular');
                        jQuery('#ttfm-font-regular').append(font_variants[j] + '<br />');
                        if (google_fonts[i].variants[j] > 500) {
                            jQuery('<input/>').attr({
                                type: 'radio',
                                name: 'ttfm-font-bold',
                                value: font_variants[j]
                            }).appendTo('#ttfm-font-bold');
                            jQuery('#ttfm-font-bold').append(font_variants[j] + '<br />');
                        }
                    }
                    for (var j in google_fonts[i].subsets) {
                        jQuery('<input/>').attr({
                            type: 'radio',
                            name: 'ttfm-font-character-sets',
                            value: google_fonts[i].subsets[j]
                        }).appendTo('#ttfm-font-subsets');
                        jQuery('#ttfm-font-subsets').append(google_fonts[i].subsets[j] + '<br />');
                    }
                    jQuery('input[name="ttfm-font-style"]').filter(function () {
                        return this.value == 'regular'
                    }).prop('checked', true);
                    jQuery('input[name="ttfm-font-character-sets"]').filter(function () {
                        return this.value == 'latin'
                    }).prop('checked', true);
                }
            }
            //import font
            importFont();
        });
        jQuery(document).on('change', 'input#ttfm_google_fonts', function () {
            add_fonts(google_fonts);
        });
        jQuery(document).on('change', 'input#ttfm_prefered_fonts', function () {
            add_fonts(prefered_fonts);
        });
        function add_fonts(fonts) {
            jQuery('#ttfm_fonts option').remove();
            var select = jQuery('#ttfm_fonts');
            for (var i in fonts) {
                select.append(new Option(fonts[i].family, fonts[i].family));
            }
        }

        jQuery(document).on('change', 'input[name="ttfm-font-style"]', function () {
            if (jQuery('#ttfm_fonts').val() != 'none') {
                importFont();
            }
        });
        jQuery('#ttfm_save_font_options').click(function () {

            if (jQuery('#ttfm_fonts').val() == 'none') {
                alert('Please select a font!');
                return;
            }

            if (!jQuery('input[name="ttfm-font-style"]').is(':checked')) {
                alert('Plese select a font style!');
                return;
            }

            if (!jQuery('input[name="ttfm-font-character-sets"]').is(':checked')) {
                alert('Plese select a font character set!');
                return;
            }
            var style = jQuery('input[name="ttfm-font-style"]:checked').val();
            var bold = jQuery('input[name="ttfm-font-bold"]:checked').val();
            var subset = jQuery('input[name="ttfm-font-character-sets"]:checked').val();
            var italic = '';
            if (style == 'regular') {
                style = 400;
                if (jQuery.inArray('italic', font_variants) !== -1) {
                    italic = ',400italic';
                }
            } else if (style == 'italic') {
                style = '400italic';
            }
            if (style == bold) {
                bold = 0;
            }
            if (jQuery.inArray(bold + 'italic', font_variants) > -1) {
                italic += ',' + bold + 'italic';
            }
            if (jQuery.inArray(style + 'italic', font_variants) > -1) {
                italic += ',' + style + 'italic';
            }
            if (bold == undefined) {
                jQuery.each(font_variants, function (key, value) {
                    var _value = parseInt(value);
                    if (bold == undefined && !isNaN(_value) && _value > 400 && (isNaN(style) || (!isNaN(style) && _value > style))) {
                        bold = value;
                    }
                });
            }
            if (typeof bold === 'undefined') {
                bold = 0;
            }
            if (italic == '') {
                italic = 0;
            }
            if (subset == 'latin') {
                subset = 0;
            }
            var font_manager_link = '<?php echo $admin_font_manager_link; ?>';
            var postData = {
                <?php if (isset($font_id)) echo 'font_id:' . $font_id . ','; ?>
                font_name: jQuery('#ttfm_fonts').val() + '',
                font_style: style + '',
                font_bold: bold + '',
                font_italic: italic + '',
                font_character_set: subset + '',
                font_class: jQuery('#ttfm-font-class').val() + '',
                font_size: jQuery('#ttfm-font-size input').val() + jQuery('#ttfm-font-size select').val(),
                font_height: jQuery('#ttfm-font-height input').val() + jQuery('#ttfm-font-height select').val(),
                font_color: jQuery('.wp-color-picker-field').val(),
                custom_css: jQuery('#ttfm-custom-css').val()
            };
            jQuery.post(font_manager_link, postData, function (response) {
                location.reload();
            });
        });

        if (update_font != 0) {
            add_fonts(google_fonts);
            jQuery('input#ttfm_google_fonts').click();
            jQuery('#ttfm_fonts').val(update_font);
            jQuery('#ttfm_fonts').trigger('change');
            jQuery('input[name="ttfm-font-style"]').trigger('change');
            jQuery('input[name="ttfm-font-style"]').filter(function () {
                return this.value == '<?php echo isset($font["font_style"]) ? $font["font_style"] : ""; ?>' || (this.value === 'italic' && current_font.font_style === '400italic');
            }).prop('checked', true);
            jQuery('input[name="ttfm-font-bold"]').filter(function () {
                return this.value == '<?php echo isset($font["font_bold"]) ? $font["font_bold"] : ""; ?>';
            }).prop('checked', true);
            jQuery('input[name="ttfm-font-character-sets"]').filter(function () {
                return this.value == '<?php echo isset($font["font_character_set"]) ? $font["font_character_set"] : ""; ?>';
            }).prop('checked', true);
        }
    });
    function importFont() {

        var font = jQuery('#ttfm_fonts').val();
        var style = jQuery('input[name="ttfm-font-style"]:checked').val();
        var subset = jQuery('input[name="ttfm-font-character-sets"]:checked').val();
        if (style == 'regular') {
            style = undefined;
        } else if (style == 'italic') {
            style = '400italic';
        }
        if (subset == 'latin') {
            subset = undefined;
        }

        var font_link = "//fonts.googleapis.com/css?family=" + font.replace(" ", "+") + (style !== undefined ? ":" + style : "") + (subset !== undefined ? "&subset=" + subset : "");
        jQuery('.imported-font').remove();
        jQuery("head").prepend("<link class='imported-font' href='" + font_link + "' rel='stylesheet' type='text/css'>");
        jQuery('#fontPreview').css({'font-family': font});
    }
</script>
