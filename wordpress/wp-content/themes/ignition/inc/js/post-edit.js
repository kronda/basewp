var ThriveScPageOptions = {'current_input': 'thrive_shortcode_option_image'};
/*
 * Handles the logic for adding a shortcode in the editor (visual or text editor)
 */
var ThriveHandleAddShortcote = function (shortcode) {
    var renderOptionsUrl = ThriveAdminAjaxUrl + "?action=shortcode_display_options&sc=" + shortcode;
    switch (shortcode) {
        case 'ResponsiveVideo':
            tb_show('Edit shortcode options', renderOptionsUrl);
            break;
        case 'BlankSpace':
            var sc_text = "[blank_space height='3em']"; //or some default value
            send_to_editor(sc_text);
            break;
        case 'ContentContainer':
            tb_show('Edit shortcode options', renderOptionsUrl);
            break;
        case 'Button':
            tb_show('Edit shortcode options', renderOptionsUrl);
            break;
        case 'ContentBox':
            tb_show('Edit shortcode options', renderOptionsUrl);
            break;
        case 'CustomFont':
            tb_show('Edit shortcode options', renderOptionsUrl);
            break;
        case 'Borderless':
            tb_show('Edit shortcode options', renderOptionsUrl);
            break;
        case 'Code':
            ThriveGenerateCodeShortcode();
            break;
        case 'DividerLine':
            tb_show('Edit shortcode options', renderOptionsUrl);
            break;
        case 'GMaps':
            tb_show('Edit shortcode options', renderOptionsUrl);
            break;
        case 'Tabs':
            tb_show('Edit shortcode options', renderOptionsUrl);
            break;
        case 'Toggle':
            tb_show('Edit shortcode options', renderOptionsUrl);
            break;
        case 'Phone':
            tb_show('Edit shortcode options', renderOptionsUrl);
            break;
        case 'PageSection':
            tb_show('Edit shortcode options', renderOptionsUrl);
            break;
        case 'Pullquote':
            tb_show('Edit shortcode options', renderOptionsUrl);
            break;
        case 'Optin':
            tb_show('Edit shortcode options', renderOptionsUrl);
            break;
        case 'PostsList':
            tb_show('Edit shortcode options', renderOptionsUrl);
            break;
        case 'CustomMenu':
            tb_show('Edit shortcode options', renderOptionsUrl);
            break;
        case 'PostsGallery':
            tb_show('Edit shortcode options', renderOptionsUrl);
            break;
        case 'VideoSection':
            tb_show('Edit shortcode options', renderOptionsUrl);
            break;
        case 'Testimonal':
            tb_show('Edit shortcode options', renderOptionsUrl);
            break;
        case 'FollowMe':
            tb_show('Edit shortcode options', renderOptionsUrl);
            break;
        case 'Grid':
            tb_show('Edit shortcode options', renderOptionsUrl);
            break;
        case 'Price':
            tb_show('Edit shortcode options', renderOptionsUrl);
            break;
        case 'Accordion':
            tb_show('Edit shortcode options', renderOptionsUrl);
            break;
        case 'Countdown':
            tb_show('Edit shortcode options', renderOptionsUrl);
            break;
        case 'CustomBox':
            tb_show('Edit shortcode options', renderOptionsUrl);
            break;
        case 'FillCounter':
            tb_show('Edit shortcode options', renderOptionsUrl);
            break;
        case 'HeadlineFocus':
            tb_show('Edit shortcode options', renderOptionsUrl);
            break;
        case 'DropCaps':
            tb_show('Edit shortcode options', renderOptionsUrl);
            break;
        case 'NumberCounter':
            tb_show('Edit shortcode options', renderOptionsUrl);
            break;
        case 'ProgressBar':
            tb_show('Edit shortcode options', renderOptionsUrl);
            break;
        case 'SplitButton':
            tb_show('Edit shortcode options', renderOptionsUrl);
            break;
        case 'Highlight':
            tb_show('Edit shortcode options', renderOptionsUrl);
            break;
        case 'IconBox':
            tb_show('Edit shortcode options', renderOptionsUrl);
            break;
        case 'MegaButton':
            tb_show('Edit shortcode options', renderOptionsUrl);
            break;
        case 'LessonsList':
            tb_show('Edit shortcode options', renderOptionsUrl);
            break;
        case 'LessonsGallery':
            tb_show('Edit shortcode options', renderOptionsUrl);
            break;
        case 'WelcomeBack':
            tb_show('Edit shortcode options', renderOptionsUrl);
            break;
        default:
            ThriveGenerateColumnShortcode(shortcode);
            break;
    }
};

var ThriveGenerateColumnShortcode = function (shortcode) {
    var type = shortcode.toLowerCase();
    var sc_text = "[one_half]YourContentHere[/one_half]";
    sc_text += "[one_half_last]YourContentHere[/one_half_last]";

    switch (type) {
        case 'columns-1-2':
            sc_text = "[one_half_first]YourContentHere[/one_half_first]";
            sc_text += "[one_half_last]YourContentHere[/one_half_last]";
            break;
        case 'columns-1-3':
            sc_text = "[one_third_first]YourContentHere[/one_third_first]";
            sc_text += "[one_third]YourContentHere[/one_third]";
            sc_text += "[one_third_last]YourContentHere[/one_third_last]";
            break;
        case 'columns-1-4':
            sc_text = "[one_fourth_first]YourContentHere[/one_fourth_first]";
            sc_text += "[one_fourth]YourContentHere[/one_fourth]";
            sc_text += "[one_fourth]YourContentHere[/one_fourth]";
            sc_text += "[one_fourth_last]YourContentHere[/one_fourth_last]";
            break;
        case 'columns-2-3-1':
            sc_text = "[two_third_first]YourContentHere[/two_third_first]";
            sc_text += "[one_third_last]YourContentHere[/one_third_last]";
            break;
        case 'columns-3-2-1':
            sc_text = "[one_third_first]YourContentHere[/one_third_first]";
            sc_text += "[two_third_last]YourContentHere[/two_third_last]";
            break;
        case 'columns-3-4-1':
            sc_text = "[one_fourth_3_first]YourContentHere[/one_fourth_3_first]";
            sc_text += "[three_fourth_last]YourContentHere[/three_fourth_last]";
            break;
        case 'columns-4-3-1':
            sc_text = "[three_fourth_first]YourContentHere[/three_fourth_first]";
            sc_text += "[one_fourth_3_last]YourContentHere[/one_fourth_3_last]";
            break;
        case 'columns-4-2-1':
            sc_text = "[one_fourth_2_first]YourContentHere[/one_fourth_2_first]";
            sc_text += "[one_fourth_2]YourContentHere[/one_fourth_2]";
            sc_text += "[one_half_last]YourContentHere[/one_half_last]";
            break;
        case 'columns-2-4-1':
            sc_text = "[one_fourth_2_first]YourContentHere[/one_fourth_2_first]";
            sc_text += "[one_half]YourContentHere[/one_half]";
            sc_text += "[one_fourth_2_last]YourContentHere[/one_fourth_2_last]";
            break;
        case 'columns-4-1-2':
            sc_text = "[one_half_first]YourContentHere[/one_half_first]";
            sc_text += "[one_fourth_2]YourContentHere[/one_fourth_2]";
            sc_text += "[one_fourth_2_last]YourContentHere[/one_fourth_2_last]";
            break;
    }

    send_to_editor(sc_text);

};

var ThriveGenerateCodeShortcode = function (shortcode) {
    var sc_text = "[code]Your content here[/code]";
    send_to_editor(sc_text);
}

jQuery(document).ready(function () {
    if (!window.Thrive_Social_Image_Uploader) {
        jQuery(document).on('click', '#thrive_meta_social_button_upload', function (e) {

            e.preventDefault();

            //Extend the wp.media object
            window.Thrive_Social_Image_Uploader = wp.media.frames.file_frame = wp.media({
                title: 'Choose Image',
                button: {
                    text: 'Choose Image'
                },
                multiple: false
            });

            var _upload_input = jQuery('#thrive_meta_social_image');

            //When a file is selected, grab the URL and set it as the text field's value
            window.Thrive_Social_Image_Uploader.on('select', function () {
                var attachment = window.Thrive_Social_Image_Uploader.state().get('selection').first().toJSON();
                _upload_input.val(attachment.url);
                window.Thrive_Social_Image_Uploader.close();
                return;
            });

            //Open the uploader dialog
            window.Thrive_Social_Image_Uploader.open();

        });
    } else {
        window.Thrive_Social_Image_Uploader.open();
    }

    jQuery('#thrive_meta_social_button_remove').click(function () {
        jQuery('#thrive_meta_social_image').val('');
    });

});
