/*
 * 
 * Theme customization file for updating the changes live in the customization section
 */
(function($) {

    wp.customize('thrivetheme_bodytext_color', function(value) {
        value.bind(function(newval) {            
            $('.main .articles p').css('color', newval);
            $('.main').css('color', newval);
        });
    });
    
    wp.customize('thrivetheme_bg_pattern', function(value) {
        value.bind(function(newval) {                        
        });
    });
    
    
    
    


})(jQuery);

