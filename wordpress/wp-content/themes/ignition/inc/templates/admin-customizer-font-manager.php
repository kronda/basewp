<?php
$all_fonts = _thrive_get_font_family_array();

$link = 'https://www.googleapis.com/webfonts/v1/webfonts?key=';
$key = 'AIzaSyDJhU1bXm2YTz_c4VpWZrAyspOS37Nn-kI';
$request = wp_remote_get($link . $key, array('sslverify' => false));
$response = json_decode(wp_remote_retrieve_body($request), true);
$google_fonts = is_array($response['items']) ? $response['items'] : array();
$safe_fonts = thrive_font_manager_get_safe_fonts();

$imported_fonts = Thrive_Font_Import_Manager::getImportedFonts();
$imported_fonts_link = Thrive_Font_Import_Manager::getCssFile();

$prefered_fonts = array();
foreach ($google_fonts as $key => $font) {
    if (array_key_exists($font['family'], $all_fonts)) {
        $prefered_fonts[] = $font;
    }
}
?>

<div class="ml">
    <span id="close-modal"><?php echo __("Close", "thrive") ?></span>

    <div id="fontPreview" style="font-size: 20px; margin-bottom: 10px;">
        Grumpy wizards make toxic brew for the evil Queen and Jack.
    </div>
    <hr>
    <div>
        <div>
            <input type="radio" name="display_fonts" id="ttfm_google_fonts"
                   value="google_fonts"/> <?php echo __("Show all fonts", "thrive") ?><br/>
        </div>
        <div>
            <input type="radio" name="display_fonts" id="ttfm_prefered_fonts" value="prefered_fonts"
                   checked/> <?php echo __("Recommended Fonts Only", "thrive") ?><br/>
        </div>
        <div>
            <input type="radio" name="display_fonts" id="ttfm_safe_fonts"
                   value="safe_fonts"/> <?php echo __("Web Safe Fonts", "thrive") ?><br/>
        </div>
        <div>
            <input type="radio" name="display_fonts" id="ttfm_imported_fonts"
                   value="imported_fonts"/> <?php _e("Imported Fonts", 'thrive'); ?>
        </div>
        <a style="float: right" target="_blank"
           href="//www.google.com/fonts"><?php echo __("View All Font Previews", 'thrive') ?></a><br/>
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

    <span style="display: table; margin: 10px auto;"
          class="button button-primary save-modal"><?php echo __("Save", 'thrive') ?></span>
</div>
<script type="text/javascript">

    function getFontName(fontLink) {
        var font = fontLink.split('=');

        if (font.length === 1) {
            return font[0];
        }

        if (font[1] === undefined) {
            return '';
        }

        font = font[1].split(':');
        font = font[0].split('&');
        return font[0].replace('+', ' ');
    }

    jQuery(document).ready(function () {
        window.setTimeout(function () {
            if (jQuery('.fontSettingHeader').val() !== '' && jQuery('.fontSettingBody').val() !== '') {
                jQuery('.selectedHeaderFont').val(getFontName(jQuery('.fontSettingHeader').val()));
                jQuery('.selectedBodyFont').val(getFontName(jQuery('.fontSettingBody').val()));
            }
        }, 1000);

        jQuery('.selectFont').click(function () {
            jQuery('.ml').fadeIn();
            jQuery('#font-location').val(jQuery(this).attr('data-font-location'));
        });
        jQuery('#close-modal').on('click', function () {
            jQuery('.ml').fadeOut();
        });

        window.prefered_fonts = <?php echo json_encode($prefered_fonts); ?>;
        window.google_fonts = <?php echo json_encode($google_fonts); ?>;
        window.safe_fonts = <?php echo json_encode($safe_fonts); ?>;
        window.selected_fonts_set = jQuery('input[name="display_fonts"]:checked').val();
        window.imported_fonts = <?php echo json_encode($imported_fonts); ?>;
        window.imported_fonts_link = '<?php echo $imported_fonts_link ?>';
        var font_variants;

        jQuery('#ttfm_fonts').change(function () {
            jQuery('.save-modal').css({display: 'table'});

            jQuery('#ttfm-font-regular').html('<b><?php echo __("Regular Style", "thrive") ?></b> <br />');
            jQuery('#ttfm-font-bold').html('<b><?php echo __("Bold Style", 'thrive') ?></b> <br />');
            jQuery('#ttfm-font-subsets').html('<b><?php echo __("Character Set", 'thrive') ?></b><br/>');
            for (var i in window[selected_fonts_set]) {
                if (window[selected_fonts_set][i].family == jQuery(this).val()) {
                    font_variants = window[selected_fonts_set][i].variants;
                    for (var j in window[selected_fonts_set][i].variants) {
                        jQuery('<input/>').attr({
                            type: 'radio',
                            name: 'ttfm-font-style',
                            value: font_variants[j]
                        }).appendTo('#ttfm-font-regular');
                        jQuery('#ttfm-font-regular').append(font_variants[j] + '<br />');
                        if (window[selected_fonts_set][i].variants[j] > 400) {
                            jQuery('<input/>').attr({
                                type: 'radio',
                                name: 'ttfm-font-bold',
                                value: font_variants[j]
                            }).appendTo('#ttfm-font-bold');
                            jQuery('#ttfm-font-bold').append(font_variants[j] + '<br />');
                        }
                    }

                    for (var j in window[selected_fonts_set][i].subsets) {
                        jQuery('<input/>').attr({
                            type: 'radio',
                            name: 'ttfm-font-character-sets',
                            value: window[selected_fonts_set][i].subsets[j]
                        }).appendTo('#ttfm-font-subsets');
                        jQuery('#ttfm-font-subsets').append(window[selected_fonts_set][i].subsets[j] + '<br />');
                    }
                    jQuery('input[name="ttfm-font-style"]').filter(function () {
                        return this.value == 'regular'
                    }).prop('checked', true);
                    jQuery('input[name="ttfm-font-character-sets"]').filter(function () {
                        return this.value == 'latin'
                    }).prop('checked', true);
                }
            }

            importFont();
        });
        jQuery('input#ttfm_google_fonts').change(function () {
            add_fonts(google_fonts);
            selected_fonts_set = this.value;
            jQuery('#ttfm_fonts').trigger('change');
        });
        jQuery('input#ttfm_prefered_fonts').change(function () {
            add_fonts(prefered_fonts);
            selected_fonts_set = this.value;
            jQuery('#ttfm_fonts').trigger('change');
        });
        jQuery(document).on('change', 'input#ttfm_safe_fonts', function () {
            add_fonts(safe_fonts);
            selected_fonts_set = this.value;
            jQuery('#ttfm_fonts').trigger('change');
        });
        jQuery(document).on('change', 'input#ttfm_imported_fonts', function () {
            add_fonts(imported_fonts);
            selected_fonts_set = this.value;
            jQuery('#ttfm_fonts').trigger('change');
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
        jQuery('.save-modal').click(function () {

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
            var font_name = jQuery('#ttfm_fonts').val();
            var font_style = jQuery('input[name="ttfm-font-style"]:checked').val();
            var font_bold = jQuery('input[name="ttfm-font-bold"]:checked').val();
            var font_set = jQuery('input[name="ttfm-font-character-sets"]:checked').val();
            var font_location = jQuery('#font-location').val();

            var font_italic = '';
            if (font_style === 'regular') {
                font_style = 400;
                if (jQuery.inArray('italic', font_variants) > -1) {
                    font_italic = ',400italic';
                }
            } else if (font_style === 'italic') {
                font_style = '400italic';
            }
            if (font_style === font_bold) {
                font_bold = undefined;
            }
            if (jQuery.inArray(font_bold + 'italic', font_variants) > -1) {
                font_italic += ',' + font_bold + 'italic';
            }
            if (jQuery.inArray(font_style + 'italic', font_variants) > -1) {
                font_italic += ',' + font_style + 'italic';
            }
            if (font_bold === undefined) {
                jQuery.each(font_variants, function (key, value) {
                    var _value = parseInt(value);
                    if (font_bold === undefined && !isNaN(_value) && _value > 400 && (isNaN(font_style) || (!isNaN(font_style) && _value > font_style))) {
                        font_bold = value;
                    }
                });
            }
            if (font_italic === '') {
                font_italic = undefined;
            }
            if (font_set === 'latin') {
                font_set = undefined;
            }

            var font = "//fonts.googleapis.com/css?family=" + font_name.replace(' ', '+') + (font_style !== undefined ? ":" + font_style : "") + (font_italic !== undefined ? font_italic : "") + (font_bold !== undefined ? "," + font_bold : "") + (font_set !== undefined ? "&subset=" + font_set : "");

            if (selected_fonts_set === 'imported_fonts') {
                font = window.imported_fonts_link;
            }

            jQuery('.fontSetting' + font_location).val(selected_fonts_set == 'google_fonts' || selected_fonts_set == 'prefered_fonts' ? font : font_name);
            jQuery('.fontSetting' + font_location).triggerHandler('change');
            jQuery('.selected' + font_location + 'Font').val(font_name);
            jQuery('.selected' + font_location + 'Font').triggerHandler('change');

            jQuery('.ml').fadeOut();
        });
    });

    function prepareFontFamily(font_family) {
        var chunks = font_family.split(","),
            length = chunks.length,
            font = '';

        jQuery(chunks).each(function (i, value) {
            font += "'" + value.trim() + "'";
            font += i + 1 != length ? ", " : '';
        });

        return font;
    }

    function importFont() {
        var font = jQuery('#ttfm_fonts').val();
        var style = jQuery('input[name="ttfm-font-style"]:checked').val();
        if (style == 'regular') {
            style = undefined;
        } else if (style == 'italic') {
            style = '400italic';
        }

        if (selected_fonts_set === 'google_fonts' || selected_fonts_set === 'prefered_fonts') {
            var font_link = "//fonts.googleapis.com/css?family=" + font.replace(" ", "+") + (style !== undefined ? ":" + style : "");
            jQuery('.imported-font').remove();
            jQuery("head").append("<link class='imported-font' href='" + font_link + "' rel='stylesheet' type='text/css'>");
        } else if (selected_fonts_set === 'imported_fonts') {
            jQuery('.imported-font').remove();
            jQuery("head").append("<link class='imported-font' href='" + window.imported_fonts_link + "' rel='stylesheet' type='text/css'>");
        }

        jQuery('#fontPreview').css({'font-family': prepareFontFamily(font)});
    }
</script>