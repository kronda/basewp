<?php

class ThriveTheme_Select_Font_Control1 extends WP_Customize_Control
{

    public $type = 'select';

    public function render_content()
    {
        ?>
        <label>
            <span class="customize-control-title">Headline Font</span>
            <span data-font-location="Header" class="button selectFont">Choose font</span>
            <input readonly class="selectedHeaderFont"/>
            <input type="hidden" class="fontSettingHeader" <?php $this->link(); ?>/>
        </label>

        <input type="hidden" id="font-location">
        <?php include 'admin-customizer-font-manager.php'; ?>

        <?php
    }

}

class ThriveTheme_Select_Font_Control2 extends WP_Customize_Control
{

    public $type = 'select';

    public function render_content()
    {
        ?>

        <label>
            <span class="customize-control-title">Body Font</span>
            <span data-font-location="Body" class="button selectFont">Choose font</span>
            <input readonly class="selectedBodyFont"/>
            <input type="hidden" class="fontSettingBody" <?php $this->link(); ?>/>
        </label>

        <?php
    }

}

class ThriveTheme_Select_Fontsize_Control extends WP_Customize_Control
{

    public $type = 'select';
    public $thrive_font_sizes = array();

    public function render_content()
    {
        for ($i = 10; $i < 72; $i++) {
            $this->thrive_font_sizes[$i] = $i;
        }
        ?>
        <label>
            <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
            <select style="width: 100%;" <?php $this->link(); ?>>
                <?php foreach ($this->thrive_font_sizes as $font): ?>
                    <option <?php if ($this->value() == $font): ?>selected<?php endif ?>><?php echo $font; ?></option>
                <?php endforeach ?>
            </select>
        </label>
        <?php
    }

}

class ThriveTheme_Select_Fontlineheight_Control extends WP_Customize_Control
{

    public $type = 'select';
    public $thrive_line_heights = array(0.8, 0.9, 1, 1.1, 1.2, 1.3, 1.4, 1.5, 1.6, 1.7, 1.8, 1.9, 2.0);

    public function render_content()
    {
        ?>
        <label>
            <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
            <select style="width: 100%;" <?php $this->link(); ?>>
                <?php foreach ($this->thrive_line_heights as $font): ?>
                    <option <?php if ($this->value() == $font): ?>selected<?php endif ?>><?php echo $font; ?></option>
                <?php endforeach ?>
            </select>
        </label>
        <?php
    }

}

class ThriveTheme_Select_Case_Control extends WP_Customize_Control
{

    public $type = 'select';
    public $thrive_font_options = array('Uppercase', 'Regular');

    public function render_content()
    {
        ?>
        <label>
            <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
            <select style="width: 100%;" <?php $this->link(); ?>>
                <?php foreach ($this->thrive_font_options as $font): ?>
                    <option <?php if ($this->value() == $font): ?>selected<?php endif ?>><?php echo $font; ?></option>
                <?php endforeach ?>
            </select>
        </label>
        <?php
    }

}

class ThriveTheme_Select_Weight_Control extends WP_Customize_Control
{

    public $type = 'select';
    public $thrive_font_options = array('Bold', 'Normal');

    public function render_content()
    {
        ?>
        <label>
            <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
            <select style="width: 100%;" <?php $this->link(); ?>>
                <?php foreach ($this->thrive_font_options as $font): ?>
                    <option <?php if ($this->value() == $font): ?>selected<?php endif ?>><?php echo $font; ?></option>
                <?php endforeach ?>
            </select>
        </label>
        <?php
    }

}

class ThriveTheme_ResetDefaults_Control extends WP_Customize_Control
{

    public $type = 'select';
    public $thrive_font_options = array('Uppercase', 'Regular');

    public function render_content()
    {
        $wpnonce = wp_create_nonce("thrive_reset_customization");
        $resetToDefaults = admin_url('admin-ajax.php?action=reset_customization&nonce=' . $wpnonce);
        ?>
        <label>
            <input class="pure-button pure-button-error" type="button" id="thrive-reset-customization-btn" value="Reset"/>
        </label>
        <script type="text/javascript">
            var ThriveCustomization = {};
            ThriveCustomization.resetUrl = "<?php echo $resetToDefaults; ?>";
            ThriveCustomization.noonce = "<?php echo $wpnonce; ?>";


            jQuery(document).ready(function () {
                jQuery("#thrive-reset-customization-btn").click(function () {
                    var postData = {
                        nonce: ThriveCustomization.noonce
                    };
                    jQuery.post(ThriveCustomization.resetUrl, postData, function (response) {
                        location.reload();
                    });
                });
            });

        </script>
        <?php
    }

}

class ThriveTheme_Header_Pattern extends WP_Customize_Control
{

    public $type = 'select';

    public function render_content()
    {
        $patterns = _thrive_get_patterns_from_directory();
        ?>
        <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/inc/css/thrive_admin_customize.css"/>
        <style>
            <?php foreach ($patterns as $pat): ?>
            <?php echo "#" . $pat . "{ background-image: url('" . get_template_directory_uri() . "/images/patterns/" . $pat . ".png');}"; ?>
            <?php endforeach; ?>
        </style>
        <label class="header-background-type" id="pattern-header">
            <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
            <select <?php $this->link(); ?> id="thrive_select_customizer_header_pattern" style="display: none;">
                <?php foreach ($patterns as $pat): ?>
                    <option <?php if ($this->value() == $pat): ?>selected<?php endif ?> value="<?php echo $pat; ?>"><?php echo $pat; ?></option>
                <?php endforeach; ?>
            </select>

            <div class="patternSelect">
                <div class="defaultHeaderPattern">
                    <span></span>
                    <a href="" id="showHeaderPattern"></a>

                    <div style="clear: both;"></div>
                </div>
                <ul class="headerPatternList" style="display: none;">
                    <?php foreach ($patterns as $pat): ?>
                        <li>
                            <a href="" id="<?php echo $pat; ?>" <?php if ($this->value() == $pat): ?>class="thrive-selected-header-pattern"<?php endif ?>></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </label>

        <script type="text/javascript">

            jQuery(document).ready(function () {
                var firstHeaderPattern = jQuery('.headerPatternList').find('li a').first().css('background-image');

                if (jQuery('.thrive-selected-header-pattern').length > 0) {
                    firstHeaderPattern = jQuery('.thrive-selected-header-pattern').css('background-image');
                }

                jQuery('.defaultHeaderPattern span').css('background-image', firstHeaderPattern);
                jQuery('.headerPatternList li a').each(function () {
                    jQuery(this).click(function () {
                        var imageHeaderSource = jQuery(this).css('background-image');
                        jQuery('.defaultHeaderPattern span').css('background-image', imageHeaderSource);
                        jQuery('.headerPatternList').hide();
                        jQuery("#thrive_select_customizer_header_pattern").val(jQuery(this).attr('id'));
                        jQuery("#thrive_select_customizer_header_pattern").triggerHandler('change');
                        return false;
                    });
                });
                jQuery('#showHeaderPattern').click(function () {
                    jQuery('.headerPatternList').toggle();
                    return false;
                });

                jQuery('#customize-control-thrivetheme_header_background_image, #customize-control-thrivetheme_header_image_type, #customize-control-thrivetheme_header_pattern, #customize-control-thrivetheme_background_value, #customize-control-thrivetheme_header_image_height').hide();
                var choice = jQuery('#customize-control-thrivetheme_theme_background select').find(':selected').val();
                jQuery(choice).show();

                jQuery('#customize-control-thrivetheme_theme_background select').change(function () {
                    var choice = (jQuery(this).val());
                    jQuery('#customize-control-thrivetheme_header_background_image, #customize-control-thrivetheme_header_image_type, #customize-control-thrivetheme_header_pattern, #customize-control-thrivetheme_background_value, #customize-control-thrivetheme_header_image_height').hide();
                    jQuery(choice).show();
                });
            });

        </script>
        <?php
    }

}

class ThriveTheme_Select_PatternBg_Control extends WP_Customize_Control
{

    public $type = 'select';

    public function render_content()
    {
        $patterns = _thrive_get_patterns_from_directory();
        ?>
        <link rel="stylesheet" href="<?php echo get_template_directory_uri(); ?>/inc/css/thrive_admin_customize.css"/>
        <style>
            <?php foreach ($patterns as $pat): ?>
            <?php echo "#" . $pat . "{ background-image: url('" . get_template_directory_uri() . "/images/patterns/" . $pat . ".png');}"; ?>
            <?php endforeach; ?>
        </style>
        <label>
            <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
            <select <?php $this->link(); ?> id="thrive_select_customizer_pattern" style="display: none;">
                <?php foreach ($patterns as $pat): ?>
                    <option <?php if ($this->value() == $pat): ?>selected<?php endif ?> value="<?php echo $pat; ?>"><?php echo $pat; ?></option>
                <?php endforeach; ?>
            </select>

            <div class="patternSelect">
                <div class="defaultPattern">
                    <span></span>
                    <a href="" id="showPattern"></a>

                    <div style="clear: both;"></div>
                </div>
                <ul class="patternList" style="display: none;">
                    <?php foreach ($patterns as $pat): ?>
                        <li>
                            <a href="" id="<?php echo $pat; ?>" <?php if ($this->value() == $pat): ?>class="thrive-selected-pattern"<?php endif ?>></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </label>
        <script type="text/javascript">

            jQuery(document).ready(function () {
                var firstPattern = jQuery('.patternList').find('li a').first().css('background-image');

                if (jQuery('.thrive-selected-pattern').length > 0) {
                    firstPattern = jQuery('.thrive-selected-pattern').css('background-image');
                }


                jQuery('.defaultPattern span').css('background-image', firstPattern);
                jQuery('.patternList li a').each(function () {
                    jQuery(this).click(function () {
                        var imageSource = jQuery(this).css('background-image');
                        jQuery('.defaultPattern span').css('background-image', imageSource);
                        jQuery('.patternList').hide();
                        jQuery("#thrive_select_customizer_pattern").val(jQuery(this).attr('id'));
                        jQuery("#thrive_select_customizer_pattern").triggerHandler('change');
                        return false;
                    });
                });
                jQuery('#showPattern').click(function () {
                    jQuery('.patternList').toggle();
                    return false;
                });
            });

        </script>
        <?php
    }
}

class ThriveTheme_Select_ImageBg_Control extends WP_Customize_Control
{

    public $type = 'text';

    public function enqueue()
    {
        wp_enqueue_media();
        wp_enqueue_script('jquery');
        wp_enqueue_script('thickbox');
        wp_enqueue_script('media-upload');
    }

    public function render_content()
    {
        ?>
        <label id="customize-control-thrivetheme-background-image">
            <span class="customize-control-title">Background Image</span>
            <input type="text" id="thrive-select-background-image" <?php $this->link(); ?>>
            <a id="thrive-clear-backgrond-image" href="#">Clear</a>
        </label>


        <script>
            jQuery(document).ready(function () {

                jQuery('#thrive-clear-backgrond-image').click(function () {
                    jQuery("#thrive-select-background-image").val('');
                    jQuery("#thrive-select-background-image").triggerHandler('change');
                });

                var custom_uploader;

                jQuery(document).on('click', '#thrive-select-background-image', function (e) {

                    e.preventDefault();

                    //Extend the wp.media object
                    custom_uploader = wp.media.frames.file_frame = wp.media({
                        title: 'Choose Image',
                        button: {
                            text: 'Choose Image'
                        },
                        multiple: false
                    });

                    //When a file is selected, grab the URL and set it as the text field's value
                    var image = jQuery(this);
                    custom_uploader.on('select', function () {
                        var attachment = custom_uploader.state().get('selection').first().toJSON();
                        image.val(attachment.url);
                        image.triggerHandler('change');
                    });

                    //Open the uploader dialog
                    custom_uploader.open();

                });


            });
        </script>
        <?php
    }

}

class ThriveTheme_Header_Image extends WP_Customize_Control
{

    public $type = 'text';

    public function enqueue()
    {
        wp_enqueue_media();
        wp_enqueue_script('jquery');
        wp_enqueue_script('thickbox');
        wp_enqueue_script('media-upload');
    }

    public function render_content()
    {
        ?>
        <label id="customize-control-thrivetheme_header_background_image">
            <span class="customize-control-title">Select image</span>
            <input type="text" id="thrive-select-image" <?php $this->link(); ?>>
            <a id="thrive-clear-backgrond-image-input" href="#">Clear</a>
        </label>


        <script>
            jQuery(document).ready(function () {

                jQuery('#thrive-clear-backgrond-image-input').click(function () {
                    jQuery("input[type=text]#thrive-select-image").val('');
                    jQuery("input[type=text]#thrive-select-image").triggerHandler('change');
                });

                var custom_uploader;

                jQuery(document).on('click', 'input[type=text]#thrive-select-image', function (e) {

                    e.preventDefault();

                    //Extend the wp.media object
                    custom_uploader = wp.media.frames.file_frame = wp.media({
                        title: 'Choose Image',
                        button: {
                            text: 'Choose Image'
                        },
                        multiple: false
                    });

                    //When a file is selected, grab the URL and set it as the text field's value
                    var image = jQuery(this);
                    custom_uploader.on('select', function () {
                        attachment = custom_uploader.state().get('selection').first().toJSON();
                        image.val(attachment.url);
                        jQuery("input[type=text]#thrive-select-image").triggerHandler('change');
                    });

                    //Open the uploader dialog
                    custom_uploader.open();

                });


            });
        </script>
        <?php
    }

}

class ThriveTheme_HighlightColor_Control extends WP_Customize_Control
{

    public $type = 'select';
    public $colors = array(
        '#0f3f8d #5772c9' => 'Blue',
        '#5b5b5b #7c7c7c' => 'Dark',
        '#855ea2 #ad85cb' => 'Purple',
        '#ff4351 #fd737d' => 'Red',
        '#7a0048 #ad3f75' => 'Liliac',
        '#fbb128 #ffd953' => 'Yellow',
        '#005b7f #1485b2' => 'Navy',
        '#fc71d1 #fcb4e6' => 'Pink',
        '#736357 #908073' => 'Brown',
    );

    public function render_content()
    {
        ?>
        <label>
            <span class="customize-control-title"><?php echo esc_html($this->label); ?></span>
            <select style="width: 100%;" <?php $this->link(); ?>>
                <?php foreach ($this->colors as $code => $color): ?>
                    <option value="<?php echo $code; ?>" <?php if ($this->value() == $code): ?>selected<?php endif ?>><?php echo $color; ?></option>
                <?php endforeach ?>
            </select>
        </label>

        <script>
            jQuery(document).ready(function () {
                jQuery(document).on('change', '#customize-control-thrivetheme_default_highlight select', function () {
                    var color = jQuery(this).val().split(" ");
                    jQuery('#customize-control-thrivetheme_highlight_background_color .wp-picker-input-wrap > input.color-picker-hex').val(color[0]).trigger('change');
                    jQuery('#customize-control-thrivetheme_highlight_link_color .wp-picker-input-wrap > input.color-picker-hex').val('#FFFFFF').trigger('change');
                    jQuery('#customize-control-thrivetheme_highlight_hover_background_color .wp-picker-input-wrap > input.color-picker-hex').val(color[1]).trigger('change');
                    jQuery('#customize-control-thrivetheme_highlight_hover_link_color .wp-picker-input-wrap > input.color-picker-hex').val('#FFFFFF').trigger('change');
                });
            });
        </script>
        <?php
    }

}