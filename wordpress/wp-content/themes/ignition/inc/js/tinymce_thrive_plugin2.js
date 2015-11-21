(function() {
    tinymce.create('tinymce.plugins.thriveShortcodes2', {
        init: function(ed, url) {
            ed.onNodeChange.add(
                function(ed, cm, n)
                {                   
                    var img_icon_path = ThriveThemeUrl + "/inc/images/thrive-shortcode-2.png";
                    jQuery("#content_thriveShortcodes2_text").html("");
                    jQuery("#content_thriveShortcodes2_text").css({
                        "background-image": "url('" + img_icon_path + "')",
                        "background-repeat": "no-repeat",
                        "background-position": "center"
                    });
                    jQuery("#content_thriveShortcodes2_text").attr('class', "mceButton mceButtonEnabled");                    
                    jQuery("#content_thriveShortcodes2_open").remove();
                    
                    jQuery("#content_thriveShortcodes2_text").click(function() {
                        setTimeout(function() {
                            jQuery("#content_thriveShortcodes2_text").html("");
                            jQuery('.thrive-shortcode-list-option.thrive_shortcodesAdmin').each(function() {
                                this.parentNode.title = '';
                            });
                            jQuery('#menu_content_content_thriveShortcodes2_menu_co').css('height', 'auto');
                        }, 20);
                    });
                    
                });
        },
        createControl: function(n, cm) {

            if (n == 'thriveShortcodes2') {
            	
                var mlb = cm.createListBox('thriveShortcodes2', {
                    title: 'Shortcodes',
                    onselect: function(v) {                                                
                        ThriveHandleAddShortcote(v);               
                        setTimeout(function() {
                            jQuery("#content_thriveShortcodes2_text").html("");
                            jQuery('.thrive-shortcode-list-option.thrive_shortcodesAdmin').each(function() {
                                this.parentNode.title = '';
                            });
                            jQuery('#menu_content_content_thriveShortcodes2_menu_co').css('height', 'auto');                            
                        }, 20);                      
                        return;
                    }
                });
                                
                var get_html = function( value ) {
                    switch (value) {
                        case 'Columns-1-2':
                            return '<div class="thrive-shortcode-list-option thrive_shortcodesAdmin"><p>Halves</p><div id="Columns-1-2" class="colm twc">1/2</div><div id="Columns-1-2" class="colm twc lst">1/2</div><div class="clear"></div></div>';
                        case 'Columns-1-3':
                            return '<div class="thrive-shortcode-list-option thrive_shortcodesAdmin"><p>Thirds</p><div class="colm thc" id="Columns-1-3">1/3</div><div class="colm thc" id="Columns-1-3">1/3</div><div class="colm thc lst" id="Columns-1-3">1/3</div><div class="clear"></div></div>';
                        case 'Columns-2-3-1':
                            return '<div class="thrive-shortcode-list-option thrive_shortcodesAdmin"><div class="colm tth" id="Columns-2-3-1">2/3</div><div class="colm lst oth" id="Columns-2-3-1">1/3</div><div class="clear"></div></div>';
                        case 'Columns-3-2-1':
                            return '<div class="thrive-shortcode-list-option thrive_shortcodesAdmin"><div class="colm oth" id="Columns-2-3-1">1/3</div><div class="colm tth lst" id="Columns-2-3-1">2/3</div><div class="clear"></div></div>';
                        case 'Columns-1-4':
                            return '<div class="thrive-shortcode-list-option thrive_shortcodesAdmin"><p>Fourths</p><div class="colm foc" id="Columns-1-4">1/4</div><div class="colm foc" id="Columns-1-4">1/4</div><div class="colm foc" id="Columns-1-4">1/4</div><div class="colm foc lst" id="Columns-1-4">1/4</div><div class="clear"></div></div>';
                        case 'Columns-3-4-1':
                            return '<div class="thrive-shortcode-list-option thrive_shortcodesAdmin"><div class="colm ofo" id="Columns-3-4-1">1/4</div><div class="colm lst tfo" id="Columns-3-4-1">3/4</div><div class="clear"></div></div>';
                        case 'Columns-4-3-1':
                            return '<div class="thrive-shortcode-list-option thrive_shortcodesAdmin"><div class="colm tfo" id="Columns-3-4-1">3/4</div><div class="colm lst ofo" id="Columns-3-4-1">1/4</div><div class="clear"></div></div>';
                        case 'Columns-4-2-1':
                            return '<div class="thrive-shortcode-list-option thrive_shortcodesAdmin"><div class="colm ofo" id="Columns 4-2-1">1/4</div><div class="colm ofo" id="Columns 4-2-1">1/4</div><div class="colm twc lst" id="Columns 4-2-1">1/2</div><div class="clear"></div></div>';
                        case 'Columns-2-4-1':
                            return '<div class="thrive-shortcode-list-option thrive_shortcodesAdmin"><div class="colm ofo" id="Columns 2-4-1">1/4</div><div class="colm twc" id="Columns 2-4-1">1/2</div><div class="colm ofo lst" id="Columns 2-4-1">1/4</div><div class="clear"></div></div>';
                        case 'Columns-4-1-2':
                            return '<div class="thrive-shortcode-list-option thrive_shortcodesAdmin"><div class="colm twc" id="Columns 4-1-2">1/2</div><div class="colm ofo" id="Columns 4-1-2">1/4</div><div class="colm ofo lst" id="Columns 4-1-2">1/4</div><div class="clear"></div></div>';
                    }
                    return value;
                };
                               
                mlb.onRenderMenu.add(function (c, m) {
                    setTimeout(function() {
                        jQuery("#content_thriveShortcodes2_text").html("");
                        jQuery('.thrive-shortcode-list-option.thrive_shortcodesAdmin').each(function() {
                            this.parentNode.title = '';
                        });
                        jQuery('#menu_content_content_thriveShortcodes2_menu_co').css('height', 'auto');
                    }, 20);
                });
				
                for (var i in thrive_shortcodes2) 
                    mlb.add(get_html(thrive_shortcodes2[i]), thrive_shortcodes2[i]);

                    
                    
                return mlb;
            }
            return null;
        }

    });
    tinymce.PluginManager.add('thriveShortcodes2', tinymce.plugins.thriveShortcodes2);
})();
