var _thrive_sc_menu_items = [];
for (var i = 0; i < thrive_shortcodes.length; i++) {
    var temp_item = {
        text : thrive_shortcodes[i].replace(/([a-z])([A-Z])/g, '$1 $2'),
        value : thrive_shortcodes[i],
        classes: thrive_shortcodes[i].toLowerCase(),
        onclick : function() {
            ThriveHandleAddShortcote(this.value());
        }
    };
    _thrive_sc_menu_items[i] = temp_item;
}
(function() {
    tinymce.PluginManager.add('thrivev2_tc_button', function(editor, url) {
        editor.addButton('thrivev2_tc_button', {
            onclick: function () {
                jQuery('.mce-accordion').parents('.mce-container').first().addClass('mce-styled');
                unbind_clicks();
            },
            title: 'ThriveShortcodes',
            type: 'menubutton',
            icon: 'icon thrivev2-shortcodes-icon',
            menu: _thrive_sc_menu_items
        });
    });
    var unbind_clicks = function() {
        jQuery('.mce-textandlayout, .mce-conversion, .mce-numbersanddata, .mce-lists, .mce-contentreveal, .mce-other').unbind('click').on('click', function(){
            return false;
        });
    }
})();

var thrive_add_tinymce_sc_button1_style = function() {
    var img_icon_path = ThriveThemeUrl + "/inc/images/thrive-shortcode-1.png";
    jQuery(".thrivev2-shortcodes-icon").css({
        "background-image": "url('" + img_icon_path + "')"
    });
};

jQuery(document).ready(function() {
    thrive_add_tinymce_sc_button1_style();
});
