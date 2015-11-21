var _thrive_sc_menu_items2 = [];
var _thrive_shortcodes2_titles = ['Halves', 'Thirds', 'Fourths'];
for (var i = 0; i < thrive_shortcodes2.length; i++) {
    var temp_item = {
        value: thrive_shortcodes2[i],
        onclick: function () {
            ThriveHandleAddShortcote(this.value());
        },
        icon: "icon-" + thrive_shortcodes2[i].toLowerCase(),
        text: _thrive_shortcodes2_titles.indexOf(thrive_shortcodes2[i]) === -1 ? '' : thrive_shortcodes2[i]
    };
    _thrive_sc_menu_items2[i] = temp_item;
}
(function () {
    tinymce.PluginManager.add('thrivev2_tc_button2', function (editor, url) {
        editor.addButton('thrivev2_tc_button2', {
            title: 'ThriveColumns',
            type: 'menubutton',
            icon: 'icon thrivev2-columns-icon',
            menu: _thrive_sc_menu_items2
        });
    });

})();

thrive_add_tinymce_sc_button2_style = function () {
    var img_icon_path = ThriveThemeUrl + "/inc/images/thrive-shortcode-2.png";
    jQuery(".thrivev2-columns-icon").css({
        "background-image": "url('" + img_icon_path + "')"
    });
};

jQuery(document).ready(function () {
    thrive_add_tinymce_sc_button2_style();
    jQuery(document).on('click', '.mce-menubtn', function () {
        if (jQuery(this).find('thrivev2-columns-icon')) {
            jQuery('.mce-i-icon-halves, .mce-i-icon-thirds, .mce-i-icon-fourths').parent().unbind('click').on('click', function () {
                return false;
            });
        }
    });
});

