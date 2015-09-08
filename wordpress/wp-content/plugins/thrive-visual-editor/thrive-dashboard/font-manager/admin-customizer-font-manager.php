<?php
$all_fonts = _thrive_get_font_family_array();

$link = 'https://www.googleapis.com/webfonts/v1/webfonts?key=';
$key = 'AIzaSyDJhU1bXm2YTz_c4VpWZrAyspOS37Nn-kI';
$request = wp_remote_get($link . $key, array('sslverify' => false));
$response = json_decode(wp_remote_retrieve_body($request), true);
$google_fonts = is_array($response['items']) ? $response['items'] : array();

$prefered_fonts = array();
foreach ($google_fonts as $key => $font) {
    if (array_key_exists($font['family'], $all_fonts)) {
        $prefered_fonts[] = $font;
    }
}
?>

<div class="ml">
    <span id="close-modal">Close</span>

    <div id="fontPreview" style="font-size: 20px; margin-bottom: 10px;">
        Grumpy wizards make toxic brew for the evil Queen and Jack.
    </div>
    <hr>
    <div>
        <input type="radio" name="display_fonts" id="ttfm_google_fonts" /> Show all fonts
        <input type="radio" name="display_fonts" id="ttfm_prefered_fonts" checked /> Recommended Fonts Only
        <a style="float: right" target="_blank" href="//www.google.com/fonts">View All Font Previews</a>
    </div>

    <div>
        <br />
        <select id="ttfm_fonts">
            <option value="none"></option>
            <?php foreach ($prefered_fonts as $name => $f): ?>
                <option data-url='<?php echo json_encode($f['files']); ?>' <?php if (isset($f['font_name']) && $f['family'] == $f['font_name']) echo 'selected'; ?> value="<?php echo $f['family']; ?>">
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

    <span style="display: table; margin: 10px auto;" class="button button-primary save-modal">Save</span>
</div>
<script type="text/javascript">

    function getFontName(fontLink) {
        var font = fontLink.split('=');
        if (font[1] === undefined) {
            return '';
        }
        font = font[1].split(':');
        font = font[0].split('&');
        return font[0].replace('+', ' ');
    }

    jQuery(document).ready(function() {
        window.setTimeout(function() {
            if (jQuery('.fontSettingHeader').val() !== '' && jQuery('.fontSettingBody' !== '')) {
                jQuery('.selectedHeaderFont').val(getFontName(jQuery('.fontSettingHeader').val()));
                jQuery('.selectedBodyFont').val(getFontName(jQuery('.fontSettingBody').val()));
            }
        }, 1000);

        jQuery('.selectFont').click(function() {
            jQuery('.ml').fadeIn();
            jQuery('#font-location').val(jQuery(this).attr('data-font-location'));
        });
        jQuery('#close-modal').on('click', function() {
            jQuery('.ml').fadeOut();
        });

        var prefered_fonts = <?php echo json_encode($prefered_fonts); ?>;
        var google_fonts = <?php echo json_encode($google_fonts); ?>;
        var font_variants;

        jQuery('#ttfm_fonts').change(function() {
            jQuery('.save-modal').css({display: 'table'});

            jQuery('#ttfm-font-regular').html('<b>Regular Style</b> <br />');
            jQuery('#ttfm-font-bold').html('<b>Bold Style</b> <br />');
            jQuery('#ttfm-font-subsets').html('<b>Character Set</b><br/>');
            for (var i in google_fonts) {
                if (google_fonts[i].family == jQuery(this).val()) {
                    font_variants = google_fonts[i].variants;
                    for (var j in google_fonts[i].variants) {
                        jQuery('<input/>').attr({type: 'radio', name: 'ttfm-font-style', value: font_variants[j]}).appendTo('#ttfm-font-regular');
                        jQuery('#ttfm-font-regular').append(font_variants[j] + '<br />');
                        if (google_fonts[i].variants[j] > 400) {
                            jQuery('<input/>').attr({type: 'radio', name: 'ttfm-font-bold', value: font_variants[j]}).appendTo('#ttfm-font-bold');
                            jQuery('#ttfm-font-bold').append(font_variants[j] + '<br />');
                        }
                    }

                    for (var j in google_fonts[i].subsets) {
                        jQuery('<input/>').attr({type: 'radio', name: 'ttfm-font-character-sets', value: google_fonts[i].subsets[j]}).appendTo('#ttfm-font-subsets');
                        jQuery('#ttfm-font-subsets').append(google_fonts[i].subsets[j] + '<br />');
                    }
                    jQuery('input[name="ttfm-font-style"]').filter(function() {
                        return this.value == 'regular'
                    }).prop('checked', true);
                    jQuery('input[name="ttfm-font-character-sets"]').filter(function() {
                        return this.value == 'latin'
                    }).prop('checked', true);
                }
            }

            importFont();
        });
        jQuery('input#ttfm_google_fonts').change(function() {
            add_fonts(google_fonts);
        });
        jQuery('input#ttfm_prefered_fonts').change(function() {
            add_fonts(prefered_fonts);
        });
        function add_fonts(fonts) {
            jQuery('#ttfm_fonts option').remove();
            var select = jQuery('#ttfm_fonts');
            for (var i in fonts) {
                select.append(new Option(fonts[i].family, fonts[i].family));
            }
        }

        jQuery(document).on('change', 'input[name="ttfm-font-style"]', function() {
            if (jQuery('#ttfm_fonts').val() != 'none') {
                importFont();
            }
        });
        jQuery('.save-modal').click(function() {

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
                jQuery.each(font_variants, function(key, value) {
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

            var font = "//fonts.googleapis.com/css?family=" + font_name.replace(" ", "+") + (font_style !== undefined ? ":" + font_style : "") + (font_italic !== undefined ? font_italic : "") + (font_bold !== undefined ? "," + font_bold : "") + (font_set !== undefined ? "&subset=" + font_set : "");
            jQuery('.fontSetting' + font_location).val(font);
            jQuery('.fontSetting' + font_location).triggerHandler('change');
            jQuery('.selected' + font_location + 'Font').val(font_name);
            jQuery('.selected' + font_location + 'Font').triggerHandler('change');
            jQuery('.ml').fadeOut();
        });
    });
    function importFont() {

        var font = jQuery('#ttfm_fonts').val();
        var style = jQuery('input[name="ttfm-font-style"]:checked').val();
        if (style == 'regular') {
            style = undefined;
        } else if (style == 'italic') {
            style = '400italic';
        }

        var font_link = "//fonts.googleapis.com/css?family=" + font.replace(" ", "+") + (style !== undefined ? ":" + style : "");
        jQuery('.imported-font').remove();
        jQuery("head").append("<link class='imported-font' href='" + font_link + "' rel='stylesheet' type='text/css'>");
        jQuery('#fontPreview').css({'font-family': font});
    }
</script>