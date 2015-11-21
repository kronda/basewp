jQuery(document).ready(function () {

    jQuery('p.depth-2-extended input.menu-item-extended-text-chk:checked').each(function () {
        jQuery(this).parent().parent().siblings('.extended-free-text').show();
    });

    jQuery(document).on('change', 'p.depth-2-extended input.menu-item-extended-text-chk', function () {
        jQuery(this).parent().parent().siblings('.extended-free-text').toggle();
    });

    jQuery('p.menu-item-extended-activate input').each(function () {
        if (jQuery(this).is(':checked')) {
            jQuery(this).parent().parent().siblings('.menu-item-extended-heading').show();
            jQuery(this).parent().parent().siblings('.menu-item-extended-columns').show();
        } else {
            jQuery(this).parent().parent().siblings('.menu-item-extended-heading').hide();
            jQuery(this).parent().parent().siblings('.menu-item-extended-columns').hide();
        }
    });

    jQuery(document).on('change', 'p.menu-item-extended-activate input', function () {
        jQuery(this).parent().parent().siblings('.menu-item-extended-heading').toggle();
        jQuery(this).parent().parent().siblings('.menu-item-extended-columns').toggle();
    });

    jQuery(document).on('click', '.highlight-menu-item a', function () {
        jQuery(this).parents('.highlight-menu-item').siblings('.highlight-menu-item-info').toggle();
    });

    jQuery(document).on('DOMNodeInserted', function(e) {
        if (jQuery(e.target).is('.menu-item')) {
            jQuery(e.target).find('.menu-item-extended-heading, .menu-item-extended-columns').hide();
        }
    });

});