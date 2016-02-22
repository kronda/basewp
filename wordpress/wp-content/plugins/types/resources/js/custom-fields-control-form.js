/**
 * Custom Fields Control screen
 */
jQuery(document).ready(function($){
    $('#wpcf-custom-fields-control-form .actions').on('click', '.button.action', function() {
        var $thiz = $(this);
        var $select = $('select', $(this).parent())
            var action = '';

        if ( 1 > $('#the-list input[type=checkbox]:checked').length) {
            alert(wpcf_custom_fields_control_form.message_select_some_fields);
            return false;
        }

        switch($select.val()) {
            /**
             * activate/deactivate bulk
             */
            case 'wpcf-activate-bulk':
            case 'wpcf-deactivate-bulk':
                return true;

            /**
             * delete
             */
            case 'wpcf-delete-bulk':
                switch( pagenow ) {
                    // term fields control
                    case 'types_page_wpcf-termmeta-control':
                        return confirm(wpcf_custom_fields_control_form.message_delete_confirm_term_fields);

                    // user fields control
                    case 'types_page_wpcf-user-fields-control':
                        return confirm(wpcf_custom_fields_control_form.message_delete_confirm_user_fields);
                }

                // post fields control
                return confirm(wpcf_custom_fields_control_form.message_delete_confirm);


            /**
             * group manipultion
             */
            case 'wpcf-add-to-group-bulk':
            case 'wpcf-remove-from-group-bulk':
                // show a spinner or something via css
                var dialog = $('<div style="display:none;height:450px;"><span class="spinner"></span>'+wpcf_custom_fields_control_form.message_loading+'</div>').appendTo('body');
                // open the dialog
                dialog.dialog({
                    // add a close listener to prevent adding multiple divs to the document
                    close: function(event, ui) {
                        // remove div with all data and events
                        dialog.remove();
                    },
                    dialogClass: 'wpcf-choose-field wpcf-ui-dialog',
                    modal: true,
                    minWidth: 800,
                    maxHeight: .9*$(window).height(),
                    title: $(':checked', $select).text(),
                    position: { my: "center top+50", at: "center top", of: window },
                    buttons: [{
                        text: wpcf_custom_fields_control_form.button_apply,
                        click: function() {
                            var groups = '';
                            $('.js-wpcf-list-of-items input:checked', dialog).each(function(){
                                if ( groups) {
                                    groups +=',';
                                }
                                groups += $(this).val();
                            });
                            $('#wpcf_admin_custom_fields_control_type').val(groups);
                            if ( '' == groups ) {
                                alert(wpcf_custom_fields_control_form.message_select_some_groups);
                            } else {
                                $thiz.closest('form').submit();
                            }
                        },
                        class: 'button-primary'
                    }, {
                        text: wpcf_custom_fields_control_form.button_cancel,
                        click: function() {
                            $( this ).dialog( "close" );
                        },
                        class: 'wpcf-ui-dialog-cancel'
                    }]
                });

                var group_kind = $('#wpcf_admin_field_type').val();
                //noinspection JSDuplicatedDeclaration
                var action;
                switch( group_kind ) {
                    case 'wpcf-usermeta':
                        action = 'wpcf_usermeta_control_get_groups';
                        break;
                    case 'wpcf-termmeta':
                        action = 'wpcf_termmeta_control_get_groups';
                        break;
                    default:
                        action = 'wpcf_custom_fields_control_get_groups';
                        break;
                }


                // load remote content
                dialog.load(
                        ajaxurl,
                        {
                            action: action,
                            _wpnonce: wpcf_custom_fields_control_form.nonce,
                        },
                        function (responseText, textStatus, XMLHttpRequest) {
                            $(dialog).on('click', 'button', function() {
                                $('#wpcf_admin_custom_fields_control_type').val($(this).data('wpcf-field-type'));
                                $thiz.closest('form').submit();
                            });
                        }
                );
                break;

            /**
             * change field type
             * add fields to group
             */
            case 'wpcf-change-type-bulk':
            case 'wpcf-add-to-group-bulk':
                // show a spinner or something via css
                var dialog = $('<div style="display:none;height:450px;" class="wpcf-dashicons"><span class="spinner"></span>'+wpcf_custom_fields_control_form.message_loading+'</div>').appendTo('body');
                // open the dialog
                dialog.dialog({
                    // add a close listener to prevent adding multiple divs to the document
                    close: function(event, ui) {
                        // remove div with all data and events
                        dialog.remove();
                    },
                    dialogClass: 'wpcf-choose-field wpcf-ui-dialog',
                    modal: true,
                    minWidth: 800,
                    maxHeight: .9*$(window).height(),
                    title: $(':checked', $select).text(),
                    position: { my: "center top+50", at: "center top", of: window }
                });
                /**
                 * choose action
                 */
                switch($select.val()) {
                    case 'wpcf-change-type-bulk':
                        action = 'wpcf_custom_fields_control_change_type';
                        break;
                    case 'wpcf-add-to-group-bulk':
                        action = 'wpcf_custom_fields_control_change_type';
                        break;
                }

                // load remote content
                dialog.load(
                        ajaxurl,
                        {
                            action: action,
                            _wpnonce: wpcf_custom_fields_control_form.nonce,
                        },
                        function (responseText, textStatus, XMLHttpRequest) {
                            $(dialog).on('click', 'button', function() {
                                $('#wpcf_admin_custom_fields_control_type').val($(this).data('wpcf-field-type'));
                                $thiz.closest('form').submit();
                            });
                        }
                );
                break;

            default:
                console.log($('select', $(this).parent()).val());
                alert(wpcf_custom_fields_control_form.message_select_some_action);
                break;
        }
        return false;
    });
    return false;



    jQuery('#wpcf-custom-fields-control-form .actions select option').each(function(){
        switch(jQuery(this).val()) {
            case 'wpcf-remove-from-group-bulk':
            case 'wpcf-add-to-group-bulk':
                jQuery(jQuery(this)).attr('disabled','disabled');
        }
    });
    jQuery('#wpcf-custom-fields-control-form #doaction, #wpcf-custom-fields-control-form #doaction2').click(function(){
 //       return wpcfAdminCustomFieldsControlSubmit(jQuery(this).prev());
    });



});

function wpcfAdminCustomFieldsControlSubmit(action_field)
{
    
    var action = action_field.val();
    var open_popup = false;
    if (action == 'wpcf-add-to-group-bulk') {
        open_popup = true;
    } else if (action == 'wpcf-remove-from-group-bulk') {
        open_popup = true;
    } else if (action == 'wpcf-change-type-bulk') {
        open_popup = true;
    }


    if (open_popup == true) {
        var data = jQuery('#wpcf-custom-fields-control-form').serialize();
//        var url = "<?php echo admin_url('admin-ajax.php'); ?>?"+data+"&action=wpcf_ajax&wpcf_action=custom_fields_control_bulk&wpcf_bulk_action="+action+"&keepThis=true&TB_iframe=true&width=400&height=400";
        var title = jQuery('select[name="'+action_field.attr('name')+'"] option:checked').text();
        tb_show(title, url);
        return false;
    }
    if (action == 'wpcf-delete-bulk') {
//        var answer = confirm('<?php _e('Deleting fields will remove fields from groups and delete post meta. Continue?', 'wpcf') ?>');
        if (answer){
            jQuery('#wpcf-custom-fields-control-form').submit();
        } else{
            return false;
        }
    }
    return true;
}

/**
 * fixes for dialogs
 */
( function( $ ) {

    // on dialogopen
    $( document ).on( 'dialogopen', '.ui-dialog', function( e, ui ) {

        // normalize primary buttons
        $( 'button.button-primary' )
            .blur()
            .addClass( 'button' )
            .removeClass( 'ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only' );
    } );

    // resize
    var resizeTimeout;
    $( window ).on( 'resize scroll', function() {
        clearTimeout( resizeTimeout );
        resizeTimeout = setTimeout( dialogResize, 200 );
    } );

    function dialogResize() {
        $( '.ui-dialog' ).each( function() {
            $( this ).css( {
                'maxWidth': '100%',
                'top': $( window ).scrollTop() + 50 + 'px',
                'left': ( $( 'body' ).innerWidth() - $( this ).outerWidth() ) / 2 + 'px'
            } );
        } );
    }
} )( jQuery );