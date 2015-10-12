/**
 * Reusable dialogs related to Content Templates.
 *
 * This file has the perspective to be renamed and be used in a more generic way if there's the need.
 *
 * @since 1.10
 */

// WPV_Toolset and WPViews are expected global variables.
var WPV_Toolset = WPV_Toolset || {};
var WPViews = WPViews || {};

/**
 * Object encapsulating code for dialog management.
 *
 * Instantiated as WPViews.ct_dialogs.
 *
 * The idea is that when you need, you can create an instance of a dialog, and then (repeatedly) open it, e.g.:
 *
 *     var dialog = new WPViews.ct_dialogs.TrashContentTemplatesDialog(...);
 *     dialog.trashContentTemplates(contentTemplateIDs);
 *
 * All you need to do besides this is to provide right callback functions.
 *
 * @param $ jQuery
 * @since 1.10
 */
WPViews.CTDialogs = function( $ ) {

    var self = this;

    self.l10n = wpv_ct_dialogs_l10n;


    self.showSpinnerBefore = function(what) {
        $('<div class="wpv-spinner ajax-loader">').insertBefore(what).show();
    };


    self.disablePrimaryButton = function(btn) {
        btn.prop('disabled', true).addClass('button-secondary').removeClass('button-primary');
    };


    self.enablePrimaryButton = function(btn) {
        btn.prop('disabled', false).addClass('button-primary').removeClass('button-secondary');
    };


    /**
     * Ensure that response is always an object with the success property.
     *
     * If it's not, return a dummy object indicating a failure.
     *
     * @param response {*} Response from the AJAX call.
     * @returns {{success: boolean}} Sanitized response.
     *
     * @since 1.9
     */
    self.parseResponse = function(response) {
        if( typeof(response.success) === 'undefined' ) {
            self.log("self.parseResponse: no success", response);
            return { success: false };
        } else {
            return response;
        }
    };


    /**
     * Object that handles trashing one or more Content Templates and the interaction with user during this process.
     *
     * If no CTs are assigned to anything, they will be trashed immediately. Otherwise, a dialog is displayed that
     * allows user to replace these assignment by different CTs that are not being deleted.
     *
     * @param {string} bulkTrashNonce Nonce for bulk trashing CTs (see wpv_bulk_content_templates_move_to_trash AJAX action).
     * @param {function} afterSuccessCallback Callback after successful trashing. Gets an object as a first argument:
     *     action: 'trashed' or 'trashed_with_replace'
     *     response: response from the last AJAX callback
     * @param {function} afterErrorCallback Callback if an (AJAX) error happens.
     * @param {function} onCancelCallback Callback if user cancels the operation (closes the confirmation dialog).
     * @since 1.10
     */
    self.TrashContentTemplatesDialog = function(bulkTrashNonce, afterSuccessCallback, afterErrorCallback, onCancelCallback) {

        var dg = this;


        /**
         * Start the trashing action.
         *
         * @param {[]} ctIDs Array of Content Template IDs that should be trashed.
         * @since 1.10
         */
        dg.trashContentTemplates = function(ctIDs) {

            // Function to show a popup with given content
            var showConfirmation = function(dialogContent) {

                $( 'body' ).append( '<div id="js-wpv-dialog-bulk-trash-warning-ct" class="toolset-shortcode-gui-dialog-container wpv-shortcode-gui-dialog-container js-wpv-shortcode-gui-dialog-container"></div>' );

                var dialog = $( '#js-wpv-dialog-bulk-trash-warning-ct' ).dialog({
                    autoOpen: false,
                    modal: true,
                    title: self.l10n.dialog_trash_warning_dialog_title,
                    minWidth: 600,
                    show: {
                        effect: "blind",
                        duration: 800
                    },
                    open: function( event, ui ) {
                        $( 'body' ).addClass( 'modal-open' );

                        /**
                         * Check that all inputs are set. That means:
                         * - user has to choose action for each template that is being used and
                         * - if "replace with existing template" is selected, a template also must be chosen.
                         *
                         * If these conditions are met, submit button is enabled, otherwise it is disabled.
                         */
                        var checkDialogInput = function() {

                            var submitButton = $('.js-ct-bulk-replace-usage');
                            self.disablePrimaryButton( submitButton );

                            var data_element = $( '.js-ct-bulk-replace-usage-data' );

                            // Content templates that are used and should be replaced.
                            var replaceIDs = decodeURIComponent( data_element.data( 'replace-ids' ) ).split( ',' );

                            var isAllSet = true;

                            // Check that all required inputs are set
                            for( var i = 0; i < replaceIDs.length && isAllSet; ++i ) {
                                var templateId = replaceIDs[ i ];
                                var action = $('input[name=wpv-content-template-replace-' + templateId + '-to]:checked').val();
                                if( 'different_template' == action ) {
                                    // do we have a template selected?
                                    var replacementTemplateId = $('#wpv-ct-list-for-replace-' + templateId).val();
                                    if( '' == replacementTemplateId ) {
                                        // template not selected
                                        isAllSet = false;
                                    }
                                } else if( 'no_template' == action ) {
                                    // ok
                                } else {
                                    // value not set
                                    isAllSet = false;
                                }
                            }

                            if( isAllSet ) {
                                self.enablePrimaryButton(submitButton);
                            }

                        };

                        // Check input when user focuses the select field with replacement content templates
                        $(document).on( 'focus', '.js-wpv-bulk-ct-list-for-replace', function() {
                            var templateId = $(this).data( 'template-id' );
                            $('.js-wpv-bulk-existing-posts-ct-replace-to-selected-ct[name=wpv-content-template-replace-' + templateId + '-to]').prop( 'checked', true );
                            checkDialogInput();
                        });

                        // Check input when user changes a selected replacement template.
                        $(document).on( 'change', '.js-wpv-bulk-ct-list-for-replace', function() {
                            checkDialogInput();
                        });

                        // Check input when user changes the desired action for a content template
                        // (replace with existing template / set none).
                        $(document).on('change','.js-wpv-bulk-existing-posts-ct-replace-to', function() {
                            checkDialogInput();
                        });

                        checkDialogInput();
                    },
                    close: function( event, ui ) {
                        $( 'body' ).removeClass( 'modal-open' );
                        onCancelCallback();
                    },
                    buttons:[
                        {
                            class: 'button-secondary',
                            text: self.l10n.dialog_cancel,
                            click: function() {
                                $( this ).dialog( "close" );
                            }
                        },
                        {
                            class: 'button-primary js-ct-bulk-replace-usage',
                            text: self.l10n.dialog_trash_warning_action,
                            click: function() {
                                dg.confirmTrashWithReplace();
                            }
                        }
                    ]
                });

                var dialogHeight = $( window ).height() - 100;

                dialog.dialog( 'open' ).dialog({
                    width: 770,
                    maxHeight: dialogHeight,
                    draggable: false,
                    resizable: false,
                    position: { my: "center top+50", at: "center top", of: window }
                });

                dialog.html(dialogContent);
            };


            var data = {
                action: 'wpv_bulk_content_templates_move_to_trash',
                ids: ctIDs,
                wpnonce : bulkTrashNonce
            };

            // Bulk trash Content Templates. This results to trashing them immediately or
            // getting a HTML content of the dialog which will be displayed.
            $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: data,
                success: function(originalResponse) {
                    var response = self.parseResponse(originalResponse);
                    if(response.success) {
                        if( 'reload' == response.data.action ) {
                            afterSuccessCallback({action: 'trashed', response: response});
                            return; // no error
                        } else if( 'dialog' == response.data.action ) {
                            showConfirmation(response.data.dialog_content);
                            return; // no error
                        }
                    }
                    console.log( "Error: AJAX returned ", response );
                    afterErrorCallback();
                },
                error: function( ajaxContext ) {
                    console.log( "Error: ", ajaxContext.responseText );
                    afterErrorCallback();
                }
            });
        };


        /**
         * This is called after user clicks the confirm button.
         *
         * Parse input from the dialog and trash CTs with replacing their assignments.
         *
         * @since 1.10
         */
        dg.confirmTrashWithReplace = function() {

            var submitButton = $('.js-ct-bulk-replace-usage');
            self.showSpinnerBefore(submitButton);

            var data_element = $( '.js-ct-bulk-replace-usage-data' );

            // All content templates that are going to be trashed
            var ctIDs = decodeURIComponent(data_element.data('ct-ids')).split(',');

            // Content templates that are used and should be replaced.
            var replaceIDs = decodeURIComponent(data_element.data('replace-ids')).split(',');


            /* This will hold IDs of template replacements (in the same order as in replaceIDs). Value 0 indicates
             * 'don't use any content template'. */
            var replacements = [];

            for (var i = 0; i < replaceIDs.length; ++i) {
                var templateId = replaceIDs[i];
                var action = $('input[name=wpv-content-template-replace-' + templateId + '-to]:checked').val();
                if ('different_template' == action) {
                    // user has selected to replace this template with another one
                    replacements[i] = $('#wpv-ct-list-for-replace-' + templateId).val();
                } else {
                    // user has selected not to replace this template with anything
                    replacements[i] = 0;
                }
            }

            dg.trashWithReplace(ctIDs, self.l10n.view_listing_actions_nonce, replaceIDs, replacements);
        };


        /**
         * Trash Content Templates with replacing their assignments.
         *
         * @param {[]} ctIDs
         * @param {string} trashWithReplaceNonce Nonce for the wpv_ct_bulk_trash_with_replace AJAX action.
         * @param replaceIDs
         * @param replacements
         */
        dg.trashWithReplace = function(ctIDs, trashWithReplaceNonce, replaceIDs, replacements) {
            var data = {
                action: 'wpv_ct_bulk_trash_with_replace',
                ids: ctIDs,
                wpnonce: trashWithReplaceNonce,
                toreplace: replaceIDs,
                replacements: replacements
            };

            $.ajax({
                type: "POST",
                url: ajaxurl,
                data: data,
                success: function (originalResponse) {
                    var response = self.parseResponse(originalResponse);
                    if (response.success) {
                        afterSuccessCallback({action: 'trashed_with_replace', response: response});
                    } else {
                        console.log("Error: AJAX returned ", response);
                        afterErrorCallback(response);
                    }
                },
                error: function (ajaxContext) {
                    console.log("Error: ", ajaxContext.responseText);
                    afterErrorCallback(null);
                }
            });
        };


    }; // self.TrashContentTemplatesDialog

};


// Start doing everything when the page is loaded.
jQuery( document ).ready( function( $ ) {
    WPViews.ct_dialogs = new WPViews.CTDialogs( $ );
});