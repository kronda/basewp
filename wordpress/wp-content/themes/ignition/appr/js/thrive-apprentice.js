/*
 * 
 * Namespace for all the apprentice scripts
 */
if (ThriveApprentice === undefined) {
    var ThriveApprentice = {};
}

jQuery(document).ready(function() {

    jQuery("#tt-favorite-lesson").click(function() {
        var post_data = {
            action: 'thrive_appr_add_to_favorites',
            post_id: ThriveApprentice.current_post_id
        };
        jQuery.post(ThriveApprentice.ajax_url, post_data, ThriveApprentice.favorite_handle); 
    });
    
    jQuery("#completed-lesson").click(function() {
        
        var post_data = {
            action: 'thrive_appr_set_progress',
            post_id: ThriveApprentice.current_post_id,
            status : jQuery(this).prop('checked') ? ThriveApprentice.progress_status.completed : ThriveApprentice.progress_status.started
        };
        jQuery.post(ThriveApprentice.ajax_url, post_data, ThriveApprentice.progress_handle);
    });

    ThriveApprentice.navigation_widget();
});

ThriveApprentice.progress_handle = function(response) {
    console.log(response);
};

ThriveApprentice.favorite_handle = function(response) {
    /*
     * TODO - handle the response value
     */
    if (jQuery("#tt-favorite-lesson").find(".heart").hasClass("fill")) {
        jQuery("#tt-favorite-lesson").find("span").html(ThriveApprentice.lang.add_to_fav);
        jQuery("#tt-favorite-lesson").find(".heart").removeClass("fill");
    } else {
        jQuery("#tt-favorite-lesson").find("span").html(ThriveApprentice.lang.remove_from_fav);
        jQuery("#tt-favorite-lesson").find(".heart").addClass("fill");
    }

};

ThriveApprentice.navigation_widget = function () {
    jQuery('.opn .apw-b').on('click', function (e) {
        _parent = jQuery(this).parents('.ap-c').first();
        _parent.children('.apw-i').slideToggle('fast');
        e.preventDefault();
    });
}