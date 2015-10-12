/**
 * Script for Content Template edit page
 *
 * Creates a function object WPViews.CTEditScreen and it's instance WPViews.ct_edit_screen.
 *
 * Requires: jquery, underscore, knockout3, views-utils-script, icl_editor-script, icl_media-manager-js, quicktags
 * and wp-pointer. Also wp_enqueue_media() call is necessary.
 *
 * @since 1.9
 */

// WPV_Toolset, WPV_Toolset.CodeMirror_instance and WPViews are expected global variables.
var WPV_Toolset = WPV_Toolset || {};

if (typeof WPV_Toolset.CodeMirror_instance === "undefined") {
    WPV_Toolset.CodeMirror_instance = [];
}

var WPViews = WPViews || {};

var ajaxurl = ajaxurl || '';


/**
 * CT edit page object.
 *
 * After the document is loaded, it will be instantiated into WPViews.ct_edit_screen. It encapsulates everything
 * that happens on the edit page.
 *
 * Knockout is used heavily. All interaction between knockout and the page is encapsulated inside a ViewModel (self.vm).
 *
 * It contains following sections:
 * - Constants
 * - ViewModel
 * - Helper functions, knockout modifications, etc.
 * - Interaction with the server
 * - Tooltips, formatting instructions and pointers
 * - Codemirror stuff
 * - Action bar
 * - Init
 *
 * Lot of code here is rather generic. If a new Edit page, based on this one, is going to be created in the future,
 * I recommend to extract as much as possible common code into a generic script that would be shared between those two.
 *
 * @param $ The jQuery object.
 *
 * @since 1.9
 */
WPViews.CTEditScreen = function( $ ) {

    var self = this;


    self.html = $('html');


    /**
     * If set to true, console logging will be activated on CT edit page.
     * @type boolean
     */
    self.debug = false;


    /**
     * Log function that works only in debug mode (controlled by self.debug).
     * @since 1.10
     */
    self.log = function() {
        if(self.debug) {
            console.log.apply(console, arguments);
        }
    };



    // ----------------------------------------------------------------------------
    // Constants
    // ----------------------------------------------------------------------------


    // Selectors for jQuery (mostly message containers)
    self.titleSectionMessageContainer = '.js-wpv-title-section .js-wpv-message-container';

    self.settingsSectionMessageContainer = '.js-wpv-settings-section .js-wpv-message-container';

    self.usageSectionMessageContainer = '.js-wpv-usage-section .js-wpv-message-container';
    self.usageOtherAssignments = '.js-wpv-usage-section .js-wpv-usage-other-assignments';

    self.contentSectionMessageContainer = '.js-wpv-content-section .js-wpv-message-container';


    // ----------------------------------------------------------------------------
    // ViewModel
    // ----------------------------------------------------------------------------


    /**
     * The ViewModel, which will be instantiated into self.vm. It handles loading and displaying
     * data (various properties of the Content Template) via Knockout bindings.
     *
     * There are few things to note about CT properties in the ViewModel. First of all, for each property (propertyName)
     * there have to be two Knockout observables: vm.propertyNameAccepted and vm.propertyNameOriginal. "Accepted" is
     * for values that *can* be stored into database (there can be a Knockout binding to them or to yet another observable
     * or pureComputed that does some kind of input validation, but that's not a requirement), and "Original", that will
     * mirror the current state of CT property as it *is* stored in the database. Difference between those two observables
     * are used to determine if a property needs updating, etc. See the "Model updating" part for more information.
     *
     * @param ct_data The Model = Content Template data as constructed in wpv_ct_editor_page(). It contains required
     *     CT properties and additional data added by individual sections.
     * @param section_data Additional data from different sections, if the standard way cannot be used.
     *
     * @since 1.9
     */
    self.ViewModel = function(ct_data, section_data) {


        /**
         * vm ~ ViewModel
         */
        var vm = this;


        /**
         * Content template ID
         * @type int
         */
        vm.id = ct_data.id;


        /**
         * If true, debug mode will be activated (console logging of things happening inside vm)
         * @type bool
         */
        vm.debug = self.debug;


        /**
         * Print a log message if debug mode is active.
         *
         * In debug mode, it has the same behaviour as console.log(). Otherwise it does nothing.
         */
        vm.log = function() {
            if(vm.debug) {
                console.log.apply(console, arguments);
            }
        };


        // ------------------------------------------------------------------------
        // Mapping between Model's and ViewModel's property names
        // ------------------------------------------------------------------------

        /* To achieve greater flexibility, property names in the ViewModel can differ from those in Model.
         * Model property names are dictated by properties in WPV_Content_Template PHP class and there can be
         * other needs (or just conventions) for naming in ViewModel.
         *
         * Model property names should be mentioned only once, so they can be easily changed in the future.
         *
         * In order to access Model's property, convert property name by vm.getModelPropertyName() or just
         * use vm.getPropertyFromModel().
         *
         * From now on, by 'property' or 'property name' is meant it's ViewModel version, unless specified otherwise.
         */

        /**
         * Mapping between ViewModel and Model property names.
         *
         * ViewModel propery names are properties and Model property names are their values.
         *
         * If a name pair isn't present here, both values are the same. You should never need to access this object
         * directly, allways use vm.getModelPropertyName or vm.getViewModelPropertyName instead.
         *
         * @since 1.9
         */
        vm.propertyNameViewmodelToModelMap = {
            description: 'description_raw',
            outputMode: 'output_mode',
            assignedSinglePostTypes: 'assigned_single_post_types',
            assignedPostArchives: 'assigned_post_archives',
            assignedTaxonomyArchives: 'assigned_taxonomy_archives',
            postContent: 'content',
            templateCss: 'template_extra_css',
            templateJs: 'template_extra_js'
        };


        /**
         * Reverse mapping to vm.propertyNameViewmodelToModelMap.
         *
         * @since 1.9
         */
        vm.propertyNameModelToViewmodelMap = _.invert(vm.propertyNameViewmodelToModelMap);


        /**
         * Get a Model property name from a ViewModel property name.
         *
         * @param viewModelPropertyName string
         * @returns string
         *
         * @since 1.9
         */
        vm.getModelPropertyName = function(viewModelPropertyName) {
            if(vm.propertyNameViewmodelToModelMap.hasOwnProperty(viewModelPropertyName)) {
                return vm.propertyNameViewmodelToModelMap[ viewModelPropertyName ];
            } else {
                return viewModelPropertyName;
            }
        };


        /**
         * Get a ViewModel property name from a Model property name.
         *
         * @param modelPropertyName string
         * @returns string
         *
         * @since 1.9
         */
        vm.getViewModelPropertyName = function(modelPropertyName) {
            if(vm.propertyNameModelToViewmodelMap.hasOwnProperty(modelPropertyName)) {
                return vm.propertyNameModelToViewmodelMap[ modelPropertyName ];
            } else {
                return modelPropertyName;
            }
        };


        /**
         * Get a property value from Model.
         *
         * @param propertyName ViewModel property name.
         * @returns {*} Property value (undefined if property doesn't exist). Refer to WPV_Content_Template for
         *     particular property description.
         *
         * @since 1.9
         */
        vm.getPropertyFromModel = function(propertyName) {
            return ct_data[vm.getModelPropertyName(propertyName)];
        };


        // ------------------------------------------------------------------------
        // Model updating (generic functionality)
        // ------------------------------------------------------------------------

        /* In order to update a property manually (e.g. on button click), just call
         * vm.updateProperties() and pass array of property names.
         *
         * In order to add property to the automatically updated ones, just push the property name
         * to vm.propertiesToUpdate observableArray.
         *
         * After the update process is finished, vm.lastUpdateResults will be updated and for successfully updated
         * properties, their "Original" versions will be equal with "Accepted".
         */


        /**
         * observableArray of properties that are being updated right now.
         *
         * This is used to determine whether a particular section is being updated (spinner visibility etc.).
         *
         * @since 1.9
         */
        vm.updatingProperties = ko.observableArray();


        /**
         * Update specified CT properties.
         *
         * Manual property updating should be done through this method.
         * It updates vm.updatingProperties instantly and initiates the updating, which happens in a debounced method.
         *
         * @param propertyNames Array of property names that should be updated.
         *
         * @since 1.9
         */
        vm.updateProperties = function(propertyNames) {
            vm.log('vm.updateProperties BEGIN:', propertyNames);
            vm.updatingProperties.pushAll(propertyNames);
            vm.updatePropertiesDebounced();
            vm.log('vm.updateProperties END:', propertyNames);
        };


        /**
         * An observable object with the results of last update.
         *
         * It will allways have following properties:
         * - propertiesUpdated: Array of names of all properties that have been updated (disregarding update result).
         * - succeeded: Array of successful update results.
         * - failed: Array of unsuccessful update results.
         *
         * An update result is an object with those properties:
         * - name: Property name.
         * - success: boolean indicating successful update.
         * - message: Optional. If present, this is an error message that should be displayed instead of a generic one
         *     (it most probably comes from a WPV_RuntimeExceptionWithMessage thrown by WPV_Content_Template).
         *
         * @since 1.9
         */
        vm.lastUpdateResults = ko.observable({
            propertiesUpdated: [],
            succeeded: [],
            failed: []
        });


        /**
         * The actual, debounced, property updating. Prepare data for the AJAX call, execute it and process the result.
         * See comment at the top of this section for more information.
         *
         * @param propertyNames array
         *
         * @since 1.9
         */
        vm.updatePropertiesDebounced = _.debounce(function() {
            var propertyNames = _.toArray(vm.updatingProperties());
            vm.log('vm.updatePropertiesDebounced BEGIN:', propertyNames);

            // Prepare data (property model names and values) for the AJAX call.
            var ct_data = _.map(propertyNames, function(propertyName) {
                return {
                    name: vm.getModelPropertyName(propertyName),
                    value: vm[ propertyName + 'Accepted' ]()
                };
            });


            // Callback after asynchronous AJAX action.
            var callback = function(ajaxResult) {

                var isCallSuccessful = ( ajaxResult !== false );
                var failedUpdates = [];
                var successfulUpdates = [];

                // If call is successful at all, populate failedUpdates and successfulUpdates with update results.
                // Otherwise failedUpdates will be populated with all property names.
                if (isCallSuccessful) {

                    // Transform model property names into viewmodel property names.
                    var updateResults = _.map(ajaxResult, function (updateResult) {
                        updateResult.name = vm.getViewModelPropertyName(updateResult.name);
                        return updateResult;
                    });

                    // Sort update results into successful and failed ones.
                    successfulUpdates = _.filter(updateResults, function (propertyUpdateResult) {
                        return (true == propertyUpdateResult.success);
                    });

                    failedUpdates = _.difference(updateResults, successfulUpdates);

                } else {
                    // Unsuccessful call, all has failed.
                    vm.log('vm.updateProperties: AJAX call unsuccessful, failed updates:', propertyNames);
                    failedUpdates = _.map(propertyNames, function (propertyName) {
                        return {
                            name: propertyName,
                            success: false
                        }
                    });
                }

                // For successfully updated properties, update their "Original" versions.
                _.each(successfulUpdates, function (updateResult) {
                    var originalValue = vm[updateResult.name + 'Original'];

                    // We will update with the value that was actually saved to database. So even
                    // if the 'Accepted' version of the VM property has changed in the meantime,
                    // we're on the safe side (the 'Original' version will keep the right data
                    // and changed property will be further indicated correctly)
                    var acceptedValue = _.findWhere(ct_data, {name: vm.getModelPropertyName(updateResult.name)}).value;
                    vm.log('vm.updatePropertiesDebounced.callback: updating property ' + updateResult.name + ' original (', originalValue(), ') to accepted (', acceptedValue, ')' );

                    // Arrays need to be handled differently - we have to clone them instead passing them by reference.
                    if (_.isArray(acceptedValue)) {
                        originalValue(_.toArray(acceptedValue));
                    } else {
                        originalValue(acceptedValue);
                    }
                });

                // Tell that those properties are no longer being updated.
                vm.updatingProperties.removeAll(propertyNames);

                // Update the last update results.
                vm.lastUpdateResults({
                    propertiesUpdated: propertyNames,
                    succeeded: successfulUpdates,
                    failed: failedUpdates
                });

                vm.log('vm.updatePropertiesDebounced END:', propertyNames);

            };

            // Execute the AJAX call asynchronously
            self.updateProperties(ct_data, callback);

        }, 500);


        /**
         * observableArray of names of properties that should be automatically updated.
         *
         * @since 1.9
         */
        vm.propertiesToUpdate = ko.observableArray();


        /**
         * When propertiesToUpdate array is modified, trigger the update and empty this array.
         *
         * @since 1.9
         */
        vm.propertiesToUpdate.subscribe(_.debounce(function(newPropertiesToUpdate) {
            if(typeof(newPropertiesToUpdate) != 'undefined' && newPropertiesToUpdate.length > 0) {
                var propertiesToUpdate = _.toArray(newPropertiesToUpdate);
                vm.updateProperties(propertiesToUpdate);
                vm.propertiesToUpdate.removeAll(propertiesToUpdate);
            }
        }, 100));


        // ------------------------------------------------------------------------
        // Handling property changes and update results
        // ------------------------------------------------------------------------


        /**
         * Array of names of properties that need updating.
         *
         * This is being used to compute Update button visibility or to trigger automatic update.
         *
         * @since 1.9
         */
        vm.changedProperties = ko.observableArray();


        /**
         * Generic handler for detecting property change (and the need to update) with custom comparator.
         *
         * Compares "Accepted" and "Original" versions of given property via provided comparator. Based on that, it
         * either adds or removes the property name from vm.changedProperties.
         *
         * @param propertyName Name of the property to check.
         * @param isEqual Comparator function accepting two parameters and returning a boolean.
         *
         * @since 1.9
         */
        vm.propertyChangeByComparator = function(propertyName, isEqual) {

            // Something has happened with the property - an user input! So we will remove any error messages.
            vm.log('vm.propertyChange: deleting error messages for changed property:', propertyName);
            self.deletePreviousMessages(undefined, [ propertyName ]);

            var acceptedValue = vm[ propertyName + 'Accepted' ]();
            var originalValue = vm[ propertyName + 'Original' ]();

            if( !isEqual(acceptedValue, originalValue) ) {
                // Values are different, add property name to vm.changedProperties() if it's not already there.
                if (!_.contains(vm.changedProperties(), propertyName)) {
                    vm.log("vm.propertyChange: property " + propertyName + " has changed (for the first time)", acceptedValue);
                    vm.changedProperties.push(propertyName);
                } else {
                    vm.log("vm.propertyChange: property " + propertyName + " is still changed", acceptedValue);
                }
            } else {
                vm.log("vm.propertyChange: property " + propertyName + " is now unchanged", acceptedValue);
                vm.changedProperties.remove(propertyName);
            }

        };


        /**
         * Generic handler for detecting property change (and the need to update).
         *
         * It uses the equality operator to compare "Accepted" and "Original" values.
         * See vm.propertyChangeByComparator() for more information.
         *
         * @param propertyName Name of the property to check.
         *
         * @since 1.9
         */
        vm.propertyChange = _.partial(vm.propertyChangeByComparator, _, function(acceptedValue, originalValue) {
            return (acceptedValue == originalValue);
        });


        /**
         * Process update results after vm.lastUpdateResults is updated.
         *
         * This method is meant to process update results for a particular section: Determine if anything relevant
         * to that section has happened, and if so, show an appropriate message in the right message container.
         *
         * If there are some failed relevant updates, an error message is displayed, and if any of those updates has
         * a custom message, this message overrides the default one.
         *
         * @param propertiesToCheck Array of relevant property names.
         * @param unsavedMessage Generic message to show when any of the relevant updates have failed.
         * @param savedMessage Generic message to show when all of the relevant updates have succeeded.
         * @param messageContainer jQuery selector for the message container.
         * @param updateResults The lastUpdateResults object.
         *
         * @since 1.9
         */
        vm.processUpdateResults = function(propertiesToCheck, unsavedMessage, savedMessage, messageContainer, updateResults) {

            var relevantUpdates = _.intersection(updateResults.propertiesUpdated, propertiesToCheck);
            var haveRelevantUpdates = (relevantUpdates.length > 0);

            if(haveRelevantUpdates) {

                // Filter out relevant failed updates
                var failedUpdates = _.filter(updateResults.failed, function(updateResult) {
                    return _.contains(propertiesToCheck, updateResult.name);
                });
                var haveFailedUpdates = (failedUpdates.length > 0);

                if(haveFailedUpdates) {

                    // Try to find a custom error message.
                    var updateWithErrorMessage = _.find(failedUpdates, function(updateResult) {
                        return updateResult.hasOwnProperty('message');
                    });

                    // Use a custom error message if there is any, or a generic one.
                    var errorMessage = (updateWithErrorMessage != undefined) ? updateWithErrorMessage.message : unsavedMessage;

                    self.showErrorMessage(messageContainer, propertiesToCheck, errorMessage);

                } else {
                    // All relevant updates have succeeded.
                    self.showSuccessMessage(messageContainer, savedMessage);
                }
            }

        };


        /**
         * Determine whether a section (meaning any property in that section) is being updated right now.
         *
         * @param propertiesToCheck Array of property names in the section.
         * @returns {boolean}
         *
         * @since 1.9
         */
        vm.isSectionUpdating = function(propertiesToCheck) {
            return (_.intersection(vm.updatingProperties(), propertiesToCheck).length > 0);
        };


        /**
         * Determine whether a section (meaning any property in that section) has unsaved changes and needs to update.
         *
         * Needed update will be indicated if there are any unsaved changes and the section is not updating at the moment.
         *
         * @param propertiesToCheck Array of property names in the section
         * @param isSectionUpdatingCheck Method to check whether the section is being updated right now.
         * @returns {boolean}
         *
         * @since 1.9
         */
        vm.isSectionUpdateNeeded = function(propertiesToCheck, isSectionUpdatingCheck) {
            var changedSectionProperties = _.intersection(vm.changedProperties(), propertiesToCheck);
            return ( !isSectionUpdatingCheck() && changedSectionProperties.length > 0 );
        };



        // ------------------------------------------------------------------------
        // Properties for Title and Description section
        // ------------------------------------------------------------------------

        // Properties belonging to the Title and Description section.
        vm.titleSectionProperties = ['title', 'slug', 'description'];

        // title
        vm.titleOriginal = ko.observable(ct_data.title);
        vm.titleOriginal.subscribe(_.partial(vm.propertyChange, 'title' ));

        vm.titleAccepted = ko.observable(ct_data.title);
        vm.titleAccepted.subscribe(_.partial(vm.propertyChange, 'title' ));

        vm.titleWasLastInputEscaped = ko.observable(true);
        vm.titleLastInput = ko.observable(null);

        vm.title = ko.pureComputed({
            read: function() {
                if(null === vm.titleLastInput()) {
                    return vm.titleAccepted();
                } else {
                    return vm.titleLastInput();
                }
            },
            write: function(newTitle) {
                vm.titleLastInput(newTitle);
                var titleEscaped = WPV_Toolset.Utils._strip_tags_and_preserve_text(newTitle);
                vm.titleWasLastInputEscaped(titleEscaped != newTitle);
                vm.titleAccepted(titleEscaped);
            }
        });


        // slug
        vm.slugOriginal = ko.observable(ct_data.slug);
        vm.slugOriginal.subscribe(_.partial(vm.propertyChange,'slug'));

        vm.slugAccepted = ko.observable(ct_data.slug);
        vm.slugAccepted.subscribe(_.partial(vm.propertyChange,'slug'));


        //noinspection JSUnresolvedVariable
        /**
         * Description property.
         *
         * @since 1.9
         */
        vm.descriptionOriginal = ko.observable(ct_data.description_raw);
        vm.descriptionOriginal.subscribe(_.partial(vm.propertyChange,'description'));

        //noinspection JSUnresolvedVariable
        vm.descriptionAccepted = ko.observable(ct_data.description_raw);
        vm.descriptionAccepted.subscribe(_.partial(vm.propertyChange,'description'));


        /**
         * Determines the visibility of description field.
         *
         * It is displayed either when there is a description or manually.
         *
         * @since 1.9
         */
        vm.isDescriptionVisible = ko.observable(vm.descriptionAccepted().length > 0);


        /**
         * Determines the visibility of "Add description" button.
         *
         * @since 1.9
         */
        vm.showAddDescriptionButton = ko.pureComputed(function() {
            return (0 == vm.descriptionAccepted().length) && !vm.isDescriptionVisible();
        });


        /**
         * Show the description field (and hide the "Add description" button as a consequence).
         *
         * @since 1.9
         */
        vm.showDescriptionField = function() {
            vm.isDescriptionVisible(true);
        };


        /**
         * This will be true when any of the properties from Title section are being updated right now.
         *
         * @since 1.9
         */
        vm.isTitleSectionUpdating = ko.pureComputed(_.partial(vm.isSectionUpdating, vm.titleSectionProperties));


        /**
         * This will be true when any of the properties from the Title section have unsaved changes
         * or when unsecaped title value differs from the accepted one (even if they will be equal after escaping).
         *
         * @since 1.9
         */
        vm.isTitleSectionUpdateNeeded = ko.pureComputed(function() {
            var isUnescapedTitleChanged = ((vm.titleLastInput() != null) && (vm.titleLastInput() != vm.titleAccepted()));
            var isSectionUpdateNeeded = vm.isSectionUpdateNeeded(vm.titleSectionProperties, vm.isTitleSectionUpdating);
            return (isUnescapedTitleChanged || isSectionUpdateNeeded);
        });


        /**
         * Manually update all properties from the Title section.
         *
         * @since 1.9
         */
        vm.titleSectionUpdate = function() {
            vm.log('vm.titleSectionUpdate');
            vm.updateProperties(vm.titleSectionProperties);
        };


        /**
         * Custom Title section method for processing update results after vm.lastUpdateResults is updated.
         *
         * See vm.ProcessUpdateResults. The difference here is that one particular case is handled differently:
         * When both 'title' and 'slug' properties have failed with a specific error code indicating their
         * value is used elsewhere, we will show a different message for both properties at once, so the user
         * can fix both problems and saves a mouse click.
         *
         * @param updateResults The lastUpdateResults object.
         *
         * @since 1.10
         */
        vm.processTitleSectionUpdateResults = function(updateResults) {

            var relevantUpdates = _.intersection(updateResults.propertiesUpdated, vm.titleSectionProperties);
            var haveRelevantUpdates = (relevantUpdates.length > 0);

            if(haveRelevantUpdates) {

                // Filter out relevant failed updates
                var failedUpdates = _.filter(updateResults.failed, function(updateResult) {
                    return _.contains(vm.titleSectionProperties, updateResult.name);
                });
                var haveFailedUpdates = (failedUpdates.length > 0);

                if(haveFailedUpdates) {

                    // Use a custom error message if there is any, or a generic one.
                    //noinspection JSUnresolvedVariable
                    var errorMessage = self.l10n.title_section.unsaved;

                    // Determine whether both title and slug have failed because they're used elsewhere
                    var titleAndSlugUsedUpdates = _.filter(failedUpdates, function(updateResult) {
                        //noinspection JSUnresolvedVariable
                        return (_.contains(['title', 'slug'], updateResult.name)
                            && (updateResult.code == self.l10n.title_section.value_already_used_exception_code));
                    });
                    var areTitleAndSlugUsedElsewhere = (titleAndSlugUsedUpdates.length == 2);

                    if(areTitleAndSlugUsedElsewhere) {
                        // Use custom error message for title+slug already used.
                        //noinspection JSUnresolvedVariable
                        errorMessage = self.l10n.title_section.title_and_slug_used;
                    } else {
                        // Try to find a custom error message.
                        var updateWithErrorMessage = _.find(failedUpdates, function (updateResult) {
                            return updateResult.hasOwnProperty('message');
                        });
                        if (updateWithErrorMessage != undefined) {
                            errorMessage = updateWithErrorMessage.message;
                        }
                    }

                    self.showErrorMessage(self.titleSectionMessageContainer, vm.titleSectionProperties, errorMessage);

                } else {

                    // All relevant updates have succeeded.

                    //noinspection JSUnresolvedVariable
                    var successMessage = self.l10n.title_section.saved;

                    // If we escaped the title, show a different message and display the accepted value.
                    if(vm.titleWasLastInputEscaped()) {
                        successMessage = self.l10n.title_section.title_was_escaped;
                        vm.titleLastInput(vm.titleAccepted());
                    }

                    self.showSuccessMessage(self.titleSectionMessageContainer, successMessage);
                }
            }

        };


        /**
         * Process update results for the Title section.
         *
         * See vm.processUpdateResults() for more information.
         *
         * @since 1.9
         */
        vm.lastUpdateResults.subscribe(vm.processTitleSectionUpdateResults);


        // ------------------------------------------------------------------------
        // Properties for Content Template Settings section
        // ------------------------------------------------------------------------

        // Properties belonging to the Content Template Settings section.
        vm.settingsSectionProperties = ['outputMode'];

        // outputMode
        vm.outputModeOriginal = ko.observable(vm.getPropertyFromModel('outputMode'));
        vm.outputModeOriginal.subscribe(_.partial(vm.propertyChange, 'outputMode' ));

        vm.outputModeAccepted = ko.observable(vm.outputModeOriginal());
        vm.outputModeAccepted.subscribe(_.partial(vm.propertyChange, 'outputMode' ));

        vm.outputModeAccepted.subscribe(function() {
            vm.propertiesToUpdate.push('outputMode');
        });


        vm.isSettingsSectionUpdating = ko.pureComputed(_.partial(vm.isSectionUpdating, vm.settingsSectionProperties));


        //noinspection JSUnresolvedVariable
        vm.lastUpdateResults.subscribe(_.partial(
            vm.processUpdateResults,
            vm.settingsSectionProperties,
            self.l10n.settings_section.unsaved,
            self.l10n.settings_section.saved,
            self.settingsSectionMessageContainer));


        // ------------------------------------------------------------------------
        // Properties for Usage section
        // ------------------------------------------------------------------------


        vm.usageSectionProperties = ['assignedSinglePostTypes', 'assignedPostArchives', 'assignedTaxonomyArchives'];

        // These properies are arrays with post type or taxonomy archive names. Since we're working
        // with arrays here, notice the usage _.toArray() - it is neccessary to clone the array instead
        // of just passing a reference.

        // assignedSinglePostTypes
        vm.assignedSinglePostTypesOriginal = ko.observableArray(_.toArray(vm.getPropertyFromModel('assignedSinglePostTypes')));
        vm.assignedSinglePostTypesOriginal.subscribe(_.partial(vm.propertyChangeByComparator, 'assignedSinglePostTypes', _.isEqual));

        vm.assignedSinglePostTypesAccepted = ko.observableArray(_.toArray(vm.getPropertyFromModel('assignedSinglePostTypes')));
        vm.assignedSinglePostTypesAccepted.subscribe(_.partial(vm.propertyChangeByComparator, 'assignedSinglePostTypes', _.isEqual));


        // assignedPostArchives
        vm.assignedPostArchivesOriginal = ko.observableArray(_.toArray(vm.getPropertyFromModel('assignedPostArchives')));
        vm.assignedPostArchivesOriginal.subscribe(_.partial(vm.propertyChangeByComparator, 'assignedPostArchives', _.isEqual));

        vm.assignedPostArchivesAccepted = ko.observableArray(_.toArray(vm.getPropertyFromModel('assignedPostArchives')));
        vm.assignedPostArchivesAccepted.subscribe(_.partial(vm.propertyChangeByComparator, 'assignedPostArchives', _.isEqual));


        // assignedTaxonomyArchives
        vm.assignedTaxonomyArchivesOriginal = ko.observableArray(_.toArray(vm.getPropertyFromModel('assignedTaxonomyArchives')));
        vm.assignedTaxonomyArchivesOriginal.subscribe(_.partial(vm.propertyChangeByComparator, 'assignedTaxonomyArchives', _.isEqual));

        vm.assignedTaxonomyArchivesAccepted = ko.observableArray(_.toArray(vm.getPropertyFromModel('assignedTaxonomyArchives')));
        vm.assignedTaxonomyArchivesAccepted.subscribe(_.partial(vm.propertyChangeByComparator, 'assignedTaxonomyArchives', _.isEqual));




        /**
         * Information about post types that have different CT assigned.
         *
         * For each assignment type, this contains a property with an observable array of
         * post type or taxonomy names that have a different CT assigned. These observable
         * arrays are used to calculate the visibility of asterisks (see below) and they get
         * updated when user assigns this CT instead.
         *
         * @type {{single_posts: observableArray, cpt_archives: observableArray, taxonomy_archives: observableArray}}
         * @since 1.9
         */
        vm.usageData = {
            single_posts: ko.observableArray(),
            cpt_archives: ko.observableArray(),
            taxonomy_archives: ko.observableArray()
        };


        // Fill vm.usageData if there is the required information.
        if(_.has(section_data, 'usage') && (section_data.usage != undefined)) {
            if(_.has(section_data.usage, 'single_posts' ) ) {
                //noinspection JSCheckFunctionSignatures
                vm.usageData.single_posts.pushAll(section_data.usage.single_posts);
            }
            if(_.has(section_data.usage, 'cpt_archives' ) ) {
                //noinspection JSCheckFunctionSignatures
                vm.usageData.cpt_archives.pushAll(section_data.usage.cpt_archives);
            }
            if(_.has(section_data.usage, 'taxonomy_archives' ) ) {
                //noinspection JSCheckFunctionSignatures
                vm.usageData.taxonomy_archives.pushAll(section_data.usage.taxonomy_archives);
            }
        }



        // dissident post binding

        //noinspection JSUnresolvedVariable
        /**
         * Array with objects that describe dissident posts for each post type.
         *
         * Dissident posts are those, who have different (or none) Content Template assigned, than is
         * default for their post type.
         *
         * This array contains objects with following attributes:
         * - postType: Post type slug.
         * - posts: An array of post IDs that are dissident..
         * - labelSingular: Singular display name (e.g. Page) of the post type.
         * - labelPlural: Plural display name (e.g. Pages) of the post type.
         *
         * CT edit page offers to bind dissident posts (to assign this CT to them), when this CT is assigned
         * to that type (this option becomes available after saving changes to database).
         *
         * @since 1.9
         */
        vm.postTypesWithDissidentPosts = ko.observableArray(_.map(ct_data.dissident_posts, function(value, key) {
            //noinspection JSUnresolvedVariable
            return {
                postType: key,
                posts: value,
                labelSingular: ct_data.usage_post_type_labels[key].singular,
                labelPlural: ct_data.usage_post_type_labels[key].plural
            };
        }));


        /**
         * Array of post type names whose dissident posts are being bound (from the displaying of confirmation
         * dialog until the action is finished).
         *
         * This is being used to determine spinner visibility, button availability, etc.
         *
         * @since 1.9
         */
        vm.postTypesWithDissidentPostsBeingBound = ko.observableArray();


        /**
         * Action when user click on the "Bind posts" button.
         *
         * It updates the "post types being bound" (see above) and displays a confirmation dialog.
         * If user confirms the action, it continues with vm.finishBindingDissidentPosts,
         * otherwise with vm.cancelBindingDissidentPosts.
         *
         * @param postType Post type slug whose dissident posts should be bound.
         *
         * @since 1.9
         */
        vm.bindDissidentPosts = function(postType) {
            var postTypeInfo = _.findWhere(vm.postTypesWithDissidentPosts(), {postType: postType});
            vm.log('vm.bindDissidentPosts', postTypeInfo);
            vm.postTypesWithDissidentPostsBeingBound.push(postType);
            //noinspection JSUnresolvedVariable
            self.showBindDissidentPostsDialog(postTypeInfo, ct_data.usage_bind_dialog_template);
        };


        /**
         * After confirmation by user, do the actual post binding.
         *
         * If the binding is successful, also update the vm.postTypesWithDissidentPosts array.
         *
         * @param postTypeInfo {{postType: string, posts: Array}}
         * @since 1.9
         */
        vm.finishBindingDissidentPosts = function(postTypeInfo) {
            self.bindPosts(postTypeInfo.posts, function(success, result) {
                if(success) {
                    vm.postTypesWithDissidentPosts.remove(postTypeInfo);
                }
                vm.postTypesWithDissidentPostsBeingBound.remove(postTypeInfo.postType);
                vm.log('vm.finishBindingDissidentPosts', success, result);
            });
        };


        /**
         * Action after user cancelled dissident post binding.
         *
         * Only remove the post type from "post types being bound" array.
         *
         * @param postTypeInfo {{postType: string, posts: Array}}
         * @since 1.9
         */
        vm.cancelBindingDissidentPosts = function(postTypeInfo) {
            vm.log('vm.cancelBindingDissidentPosts', postTypeInfo.postType);
            vm.postTypesWithDissidentPostsBeingBound.remove(postTypeInfo.postType);
        };


        /**
         * Determine if a "Bind posts" button should be visible for given post type.
         *
         * 1. the post type must have dissident posts
         * 2. CT must be curently assigned to this post type (stored in database)
         * 3. CT must be assigned to this post type also on the page (current unsaved value)
         *
         * @param postType Post type name.
         * @returns {boolean}
         * @since 1.9
         */
        vm.isBindButtonVisible = function(postType) {
            var dissidentPostsExist = (typeof(_.findWhere(vm.postTypesWithDissidentPosts(), {postType: postType})) != 'undefined');
            var postTypeAssigned = _.contains(vm.assignedSinglePostTypesOriginal(), postType);
            var postTypeSelected = _.contains(vm.assignedSinglePostTypesAccepted(), postType);
            var isVisible =  dissidentPostsExist && postTypeSelected && postTypeAssigned;
            vm.log('vm.isBindButtonVisible(' + postType + ')', isVisible);
            return isVisible;
        };


        /**
         * Determine if a "Bind posts" button should be enabled for given post type.
         *
         * 1. it must be visible
         * 2. given post type must not be in a process of binding
         *
         * @param postType Post type name.
         * @returns {boolean}
         * @since 1.9
         */
        vm.isBindButtonEnabled = function(postType) {
            var isEnabled = vm.isBindButtonVisible(postType) && !_.contains(vm.postTypesWithDissidentPostsBeingBound(), postType);
            vm.log('vm.isBindButtonEnabled(' + postType + ')', isEnabled);
            return isEnabled;
        };


        vm.isUsageSectionUpdating = ko.pureComputed(_.partial(vm.isSectionUpdating, vm.usageSectionProperties));

        vm.isUsageSectionUpdateNeeded = ko.pureComputed(_.partial(vm.isSectionUpdateNeeded, vm.usageSectionProperties, vm.isUsageSectionUpdating));

        vm.usageSectionUpdate = function() {
            vm.log('vm.usageSectionUpdate');
            vm.updateProperties(vm.usageSectionProperties);
        };

        //noinspection JSUnresolvedVariable
        vm.lastUpdateResults.subscribe(_.partial(
            vm.processUpdateResults,
            vm.usageSectionProperties,
            self.l10n.usage_section.unsaved,
            self.l10n.usage_section.saved,
            self.usageSectionMessageContainer));


        /**
         * Determines if an asterisk, indicating another CT is already assigned, should be displayed.
         *
         * @param assignmentType Index into custom section data with information about other CTs being assigned.
         * @param vmArrayToCompare {Array} ViewModel property name with currently selected items
         *     for given assignmentType.
         * @param value Actual value for which this asterisk would be displayed.
         * @returns {boolean} True if the asterisk should be visible.
         * @since 1.9
         */
        vm.isAsteriskVisible = function(assignmentType, vmArrayToCompare, value) {
            var otherAssignmentExists = _.contains(vm.usageData[ assignmentType ](), value);
            var valueSelected = _.contains(vm[ vmArrayToCompare ](), value);
            return (otherAssignmentExists && valueSelected);
        };


        /**
         * Determines if an explanation for an asterisk should be displayed for given assignment type.
         *
         * True if at least one asterisk was displayed.
         *
         * @param vmArrayToCompare {observableArray} ViewModel property with currently selected items
         *     for given assignmentType.
         * @param assignmentType Index into custom section data with information about other CTs being assigned.
         * @returns {boolean} True if the asterisk explanation should be visible.
         * @since 1.9
         */
        vm.isAsteriskExplanationVisible = function(vmArrayToCompare, assignmentType) {
            return _.intersection(vm[ vmArrayToCompare ](), vm.usageData[ assignmentType ]()).length > 0;
        };


        /**
         * Update information about other CTs being assigned to post types or taxonomies.
         *
         * Updates the appropriate observableArray in vm.usageData.
         *
         * @param {string} assignmentType One of the three assignment types.
         * @param {Array} vmArrayToCompare Array of post type or taxonomy names that where this
         *     CT has been assigned.
         * @since 1.9
         */
        vm.updateOtherAssignments = function(assignmentType, vmArrayToCompare) {
            vm.usageData[assignmentType].removeAll(vmArrayToCompare);
        };


        // Subscribe to update other assignments after changes have been saved to database.
        vm.assignedSinglePostTypesOriginal.subscribe(_.partial(vm.updateOtherAssignments, 'single_posts'));
        vm.assignedPostArchivesOriginal.subscribe(_.partial(vm.updateOtherAssignments, 'cpt_archives'));
        vm.assignedTaxonomyArchivesOriginal.subscribe(_.partial(vm.updateOtherAssignments, 'taxonomy_archives'));


        // ------------------------------------------------------------------------
        // Properties for Content section
        // ------------------------------------------------------------------------


        vm.contentSectionProperties = ['postContent', 'templateCss', 'templateJs'];


        // postContent
        vm.postContentOriginal = ko.observable(vm.getPropertyFromModel('postContent'));
        vm.postContentOriginal.subscribe(_.partial(vm.propertyChange,'postContent'));

        vm.postContentAccepted = ko.observable(vm.getPropertyFromModel('postContent'));
        vm.postContentAccepted.subscribe(_.partial(vm.propertyChange,'postContent'));


        // templateCss
        vm.templateCssOriginal = ko.observable(vm.getPropertyFromModel('templateCss'));
        vm.templateCssOriginal.subscribe(_.partial(vm.propertyChange,'templateCss'));

        vm.templateCssAccepted = ko.observable(vm.getPropertyFromModel('templateCss'));
        vm.templateCssAccepted.subscribe(_.partial(vm.propertyChange,'templateCss'));


        /**
         * Determines whether CSS editor is expanded (visible).
         *
         * Used for determining other elements' visibility.
         *
         * @since 1.9
         */
        vm.isCssEditorExpanded = ko.observable(false);


        /**
         * Determines whether the "Pin" icon should be visible for a CSS editor.
         *
         * It is displayed when editor is not displaying but there is some CSS code.
         *
         * @since 1.9
         */
        vm.isCssPinVisible = ko.pureComputed(function() {
            return ( !vm.isCssEditorExpanded() && vm.templateCssAccepted().trim() != '' );
        });


        /**
         * Action to show or hide CSS editor.
         *
         * @since 1.9
         */
        vm.toggleCssEditor = function() {
            vm.isCssEditorExpanded(!vm.isCssEditorExpanded());
        };


        // templateJs
        vm.templateJsOriginal = ko.observable(vm.getPropertyFromModel('templateJs'));
        vm.templateJsOriginal.subscribe(_.partial(vm.propertyChange,'templateJs'));

        vm.templateJsAccepted = ko.observable(vm.getPropertyFromModel('templateJs'));
        vm.templateJsAccepted.subscribe(_.partial(vm.propertyChange,'templateJs'));


        vm.isJsEditorExpanded = ko.observable(false);


        vm.isJsPinVisible = ko.pureComputed(function() {
            return ( !vm.isJsEditorExpanded() && vm.templateJsAccepted().trim() != '' );
        });


        vm.toggleJsEditor = function() {
            vm.isJsEditorExpanded(!vm.isJsEditorExpanded());
        };


        vm.isContentSectionUpdating = ko.pureComputed(_.partial(vm.isSectionUpdating, vm.contentSectionProperties));

        vm.isContentSectionUpdateNeeded = ko.pureComputed(_.partial(vm.isSectionUpdateNeeded, vm.contentSectionProperties, vm.isContentSectionUpdating));

        vm.contentSectionUpdate = function() {
            vm.log('vm.contentSectionUpdate');
            vm.updateProperties(vm.contentSectionProperties);
        };

        //noinspection JSUnresolvedVariable
        vm.lastUpdateResults.subscribe(_.partial(
            vm.processUpdateResults,
            vm.contentSectionProperties,
            self.l10n.content_section.unsaved,
            self.l10n.content_section.saved,
            self.contentSectionMessageContainer));


        // ------------------------------------------------------------------------
        // "Save all sections at once"
        // ------------------------------------------------------------------------


        /**
         * "Summary" of existing sections.
         *
         * @type {{title: {properties: Array, messageContainer: string, isUpdateNeeded: *}, settings: {properties: Array, messageContainer: string, isUpdateNeeded: Function}, usage: {properties: Array, messageContainer: Array, isUpdateNeeded: *}, content: {properties: Array, messageContainer: string, isUpdateNeeded: *}}}
         * @since 1.10
         */
        vm.sections = {
            title: {
                properties: vm.titleSectionProperties,
                messageContainer: vm.titleSectionMessageContainer,
                isUpdateNeeded: vm.isTitleSectionUpdateNeeded
            },
            settings: {
                properties: vm.settingsSectionProperties,
                messageContainer: vm.settingsSectionMessageContainer,
                isUpdateNeeded: function() { return false }
            },
            usage: {
                properties: vm.usageSectionProperties,
                messageContainer: vm.usageSectionProperties,
                isUpdateNeeded: vm.isUsageSectionUpdateNeeded
            },
            content: {
                properties: vm.contentSectionProperties,
                messageContainer: vm.contentSectionMessageContainer,
                isUpdateNeeded: vm.isContentSectionUpdateNeeded
            }
        };


        /**
         * Determine if there are any unsaved properties.
         *
         * Depends on the per-section checking functions.
         *
         * @since 1.10
         */
        vm.isAnyUpdateNeeded = ko.pureComputed(function() {
            return _.any(vm.sections, function(section) { return section.isUpdateNeeded(); });
        });


        /**
         * Determine "Save all changes at once" button visibility.
         *
         * It will be visible if any property has been changed and isn't being updated yet.
         *
         * @since 1.9
         */
        vm.isSaveAllButtonEnabled = ko.pureComputed(function() {
            return vm.isAnyUpdateNeeded();
        });


        /**
         * Manually save all changed properties at once.
         *
         * @since 1.9
         */
        vm.saveAllProperties = function() {
            vm.updateProperties(_.toArray(vm.changedProperties()));
        };


        /**
         * Process update results.
         *
         * Show a generic error/success message on the action bar.
         *
         * @since 1.9
         */
        vm.lastUpdateResults.subscribe(function(updateResults) {
            var haveFailedUpdates = (updateResults.failed.length > 0);
            var messageContainerSelector = self.action_bar_message_container.selector;
            if(haveFailedUpdates) {
                // This message is about all the properties that have failed (and it will be removed if any of those
                // gets an input from user)
                var failedPropertyNames = _.pluck(updateResults.failed, 'name');
                //noinspection JSUnresolvedVariable
                self.showErrorMessage(messageContainerSelector, failedPropertyNames, self.l10n.editor.unsaved);
                self.highlight_action_bar('failure');
            } else {
                self.showSuccessMessage(messageContainerSelector, self.l10n.editor.saved);
                self.highlight_action_bar('success');
            }
        });


        // ------------------------------------------------------------------------
        // Trash button bindings
        // ------------------------------------------------------------------------


        /**
         * Initiate trashing of this CT.
         * @since 1.10
         */
        vm.trashAction = function() {
            self.trashAction();
        };


        /**
         * Indicates that the CT is in the process of trashing.
         * @since 1.10
         */
        vm.isTrashing = ko.observable(false);


        // ------------------------------------------------------------------------
        // Initialize the ViewModel
        // ------------------------------------------------------------------------


        // Now the magic happens)
        ko.applyBindings(vm);

    };


    // ----------------------------------------------------------------------------
    // Helper functions, knockout modifications, etc.
    // ----------------------------------------------------------------------------


    /**
     * Collection of previous messages that keep being displayed.
     *
     * Each message should be removed when
     * - a new one is being displayed in the same place.
     * - related property gets user input (note that it's not the same as if it was changed in the usual sense)
     *
     * Contains object with three properties:
     * - selector: CSS selector of the message container.
     * - propertyNames: Array of property names this message is about.
     * - messageObject: the wpvToolsetMessage object used to hide the message.
     *
     * @type {Array<{selector: {string}, propertyNames: [], messageObject: {}}>}
     * @since 1.9
     */
    self.previousMessages = [];


    /**
     * Delete previously displayed messages based on their selector and/or related property names.
     *
     * @param {string|undefined} selector All messages with this selector will be removed. Can be undefined, in which case
     *     this check will be skipped.
     * @param {[]|undefined} propertyNames Messages who are related to at least one of these properties will be removed.
     *     Can be undefined, in which case this check will be skipped.
     *
     * @since 1.10
     */
    self.deletePreviousMessages = function(selector, propertyNames) {

        var messagesToDelete = [];

        // Collect messages that will be deleted.
        if(typeof(selector) != 'undefined') {
            messagesToDelete = _.where(self.previousMessages, {selector: selector});
        }

        if(typeof(propertyNames) != 'undefined') {
            var messagesByPropertyNames = _.filter(self.previousMessages, function(msg) {
                return (_.intersection(msg.propertyNames, propertyNames).length > 0);
            });
            messagesToDelete = messagesToDelete.concat(messagesByPropertyNames);
        }

        // Delete collected messages.
        _.each(messagesToDelete, function(messageToDelete) {
            if(_.has(messageToDelete.messageObject, 'wpvMessageRemove')) {
                messageToDelete.messageObject.wpvMessageRemove();
                self.previousMessages = _.without(self.previousMessages, messageToDelete);
            }
        });

    };


    /**
     * Display a standard Toolset message.
     *
     * Also deletes previously displayed messages with the same selector.
     *
     * @param type {string} 'success' or 'error'
     * @param stay {bool} Determines whether the message will fade out or stays displayed.
     * @param fadeOut {int} How long should the fadeOut message last when removing the message.
     * @param selector {string} CSS selector for the message container.
     * @param text {string} Text of the message.
     *
     * @return {*} The message object.
     *
     * @since 1.9
     */
    self.showMessage = function(type, stay, fadeOut, selector, text) {

        self.deletePreviousMessages(selector, undefined);

        return $(selector).wpvToolsetMessage({
            text: text,
            type: type,
            inline: true,
            stay: stay,
            fadeOut: fadeOut
        });
    };


    /**
     * Display a success message.
     *
     * @param selector {string} CSS selector for the message container.
     * @param text {string} Text of the message.
     *
     * @since 1.9
     */
    self.showSuccessMessage = _.partial(self.showMessage, 'success', false, 2000);


    /**
     * Display an error message.
     *
     * Store the message object in self.previousMessages so that it will be removed at the right time.
     *
     * @param selector {string} CSS selector for the message container.
     * @param propertyNames {[]} Names of properties that this message relates to.
     * @param text {string} Text of the message.
     *
     * @since 1.9
     */
    self.showErrorMessage = function(selector, propertyNames, text) {
        var message = {
            selector: selector,
            propertyNames: propertyNames,
            messageObject: self.showMessage('error', true, 0, selector, text)
        };
        // Note that we first have to create the message and then we can push to
        // self.previousMessages, because this array is being overwritten in
        // self.showMessage().
        self.previousMessages.push(message);
    };


    /**
     * Extension of the ko.observableArray() for pushing of array of items at once.
     *
     * See https://github.com/knockout/knockout/pull/845.
     *
     * @param valuesToPush {Array} Array of values to push into the observableArray
     * @returns {ko.observableArray.fn}
     *
     * @since 1.9
     */
    ko.observableArray.fn.pushAll = function(valuesToPush) {
        var items = this;
        for (var i = 0, j = valuesToPush.length; i < j; i++) {
            items.push(valuesToPush[i]);
        }
        return items;
    };


    /**
     * Creates a custom Knockout binding that makes elements shown/hidden via custom method applyEfect
     *
     * @param applyEffect {Function} Function that takes the element and value and applies it in some custom way.
     * @returns {{init: Function, update: Function}} Knockout binding handler.
     *
     * @since 1.9
     */
    self.knockoutEffectBinding = function(applyEffect) {
        return {
            init: function(element, valueAccessor) {
                // Initially set the element to be instantly visible/hidden depending on the value
                var value = valueAccessor();
                // Use "unwrapObservable" so we can handle values that may or may not be observable
                $(element).toggle(ko.unwrap(value));
            },
            update: function(element, valueAccessor) {
                // Whenever the value subsequently changes, slowly fade the element in or out
                var value = valueAccessor();
                applyEffect(element, ko.unwrap(value));
            }
        }
    };


    /**
     * Custom knockout binding for hiding and displaying CodeMirror editors.
     *
     * The target element needs to contain a data-target-editor with an editor slug that's present in self.editors.
     * It slides down/up and it refreshes the CodeMirror instance after the first slideDown.
     *
     * @type {{init: Function, update: Function}}
     *
     * @since 1.9
     */
    ko.bindingHandlers.editorVisible = self.knockoutEffectBinding(function(element, show) {
        var target = $(element);
        if(true == show) {
            target.slideDown(200);

            var editor_slug = target.data('target-editor');
            var editor_info = _.findWhere(self.editors, {slug: editor_slug});
            if( !editor_info.hasOwnProperty('was_refreshed') || !editor_info.was_refreshed ) {
                self.vm.log('ko.bindingHandlers.editorVisible: refreshing', editor_slug);
                self['codemirror_' + editor_slug].refresh();
                editor_info.was_refreshed = true;
            }

        } else {
            target.slideUp(200);
        }
    });


    /**
     * Custom knockout binding for changing element visibility with animating it's width.
     *
     * @type {{init: Function, update: Function}}
     *
     * @since 1.9
     */
    ko.bindingHandlers.widthToggleVisible = self.knockoutEffectBinding(function(element, show) {
        var target = $(element);
        if(true == show) {
            target.animate({width:'show'}, 200);
        } else {
            target.animate({width:'hide'}, 200);
        }
    });


    /**
     * Custom knockout binding for changing element visibility with sliding up/down.
     *
     * @type {{init: Function, update: Function}}
     *
     * @since 1.9
     */
    ko.bindingHandlers.slideToggleVisible = self.knockoutEffectBinding(function(element, show) {
        var target = $(element);
        if(true == show) {
            target.slideDown(200);
        } else {
            target.slideUp(200);
        }
    });


    /**
     * Custom knockout binding for changing element visibility with fading.
     *
     * @type {{init: Function, update: Function}}
     *
     * @since 1.9
     */
    ko.bindingHandlers.fadeVisibility = {
        init: function(element, valueAccessor) {
            // Initially set the element to be instantly visible/hidden depending on the value
            var show = ko.unwrap(valueAccessor());
            var visibility = show ? 'visible' : 'hidden';
            $(element).css('visibility', visibility);
        },
        update: function(element, valueAccessor) {
            // Whenever the value subsequently changes, slowly fade the element in or out
            var show = ko.unwrap(valueAccessor());
            var isDisplayed = ($(element).css('display') != 'none');

            if(show && !isDisplayed) {
                $(element).css('visibility', 'visible').hide().fadeIn('slow');
            } else if(!show && isDisplayed) {
                $(element).css('visibility', 'hide').show().fadeOut('slow');
            }
        }
    };


    /**
     * Custom knockout binding for changing spinner visibility by adding or removing the "is-active" class.
     *
     * @type {{init: Function, update: Function}}
     *
     * @since 1.9
     */
    ko.bindingHandlers.spinnerActive = {
        update: function(element, valueAccessor) {
            var value = ko.utils.unwrapObservable(valueAccessor());
            if(true == value) {
                $(element).addClass('is-active');
            } else {
                $(element).removeClass('is-active');
            }
        }
    };


    // ----------------------------------------------------------------------------
    // Interaction with the server
    // ----------------------------------------------------------------------------

    /* All AJAX calls are defined here. Currently we have only two:
     * - wpv_ct_update_properties for updating a set of properties
     * - wpv_ct_bind_posts for binding posts to this Content Template
     */

    /**
     * Nonce for updating CT properties.
     * @type string
     */
    self.update_nonce = null;


    /**
     * Nonce for bulk trashing CTs.
     * @type {string}
     */
    self.trash_nonce = null;



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
     * Update Content Template properties on the server.
     *
     * Execute a wpv_ct_update_properties AJAX call.
     *
     * @param ct_data Array of objects holding subset of CT data ("name" with property Model name and "value").
     * @param {function} callback Function that will be called after AJAX action finished. It should
     *     accept one argument: Array of update results or false if the AJAX call has failed entirely.
     *
     * @since 1.9
     */
    self.updateProperties = function(ct_data, callback) {

        var data = {
            action: 'wpv_ct_update_properties',
            id: self.ct_data.id,
            wpnonce: self.update_nonce,
            properties: ct_data
        };

        var ret = false;

        self.vm.log('self.updateProperties', data);

        $.ajax({
            async: true,
            type: 'POST',
            url: ajaxurl,
            data: data,
            success: function( originalResponse ) {
                var response = self.parseResponse(originalResponse);
                if(response.success) {

                    // We will be returning update results.
                    ret = response.data.results;
                    self.vm.log('wpv_ct_update_properties response:', JSON.stringify(originalResponse));

                    callback(ret);

                } else {
                    console.log('Error:', originalResponse);
                    ret = false;

                    callback(ret);
                }
            },
            error: function( ajaxContext ) {
                console.log('Error:', ajaxContext.responseText);
                ret = false;

                callback(ret);
            }
        });
    };


    /**
     * Bind posts to the current Content Template.
     *
     * @param postsToBind Array of post IDs to bind.
     * @param callback Function taking two arguments, success and response data.
     *
     * @since 1.9
     */
    self.bindPosts = function(postsToBind, callback) {

        //noinspection JSUnresolvedVariable
        var data = {
            action: 'wpv_ct_bind_posts',
            id: self.ct_data.id,
            wpnonce: self.ct_data.usage_bind_nonce,
            posts_to_bind: postsToBind
        };

        $.ajax({
            type: 'POST',
            url: ajaxurl,
            data: data,
            success: function( originalResponse ) {
                var response = self.parseResponse(originalResponse);
                if(!response.success) {
                    console.log('Error:', originalResponse);
                }
                self.vm.log('wpv_ct_bind_posts response:', JSON.stringify(originalResponse));

                callback(response.success, response.data);
            },
            error: function( ajaxContext ) {
                console.log('Error:', ajaxContext.responseText);

                callback(false, ajaxContext.responseText);
            }
        });
    };


    /**
     * Instance of the Trash dialog object.
     * @type {null|WPViews.CTDialogs.TrashContentTemplatesDialog}
     * @since 1.10
     */
    self.trashDialog = null;


    /**
     * Initialize and setup the Trash dialog object and execute the trashing action.
     *
     * Note that no dialog might actually display, depending on what assignment this CT has.
     * After successful trashing redirects to CT listing page.
     *
     * @since 1.10
     */
    self.trashAction = function() {

        self.vm.isTrashing(true);

        // Callbacks
        var afterTrashing = function() {
            self.log('self.trashAction succeeded');

            // CTs have been trashed. Redirect to CT listing page and show a message.
            var uri = new URI(self.ct_data.listing_page_url);
            window.location.href = uri.addQuery('trashed', '1').addQuery('affected', self.vm.id).toString();
        };

        var onCancel = function() {
            self.log('self.trashAction cancelled');
            self.vm.isTrashing(false);
        };

        // Initialize the dialog object if it wasn't done already
        if(null == self.trashDialog) {
            self.trashDialog = new WPViews.ct_dialogs.TrashContentTemplatesDialog(self.trash_nonce, afterTrashing, onCancel, onCancel);
        }

        self.trashDialog.trashContentTemplates([ self.vm.id ]);
    };


    // ----------------------------------------------------------------------------
    // Tooltips, formatting instructions and pointers
    // ----------------------------------------------------------------------------


    /**
     * Show or hide "Formatting help" section.
     *
     * Expects the "toggle" element to contain a data-target attribute with class name of the element that
     * should actually be toggled. Also changes the caret icon (up/down).
     *
     * @param toggle jQuery wrapper of the toggle element.
     *
     * @since 1.9
     */
    self.show_hide_formatting_help = function( toggle ) {
        $( '.' + toggle.data( 'target' ) ).slideToggle( 400, function() {
            toggle.find( '.js-wpv-toggle-toggler-icon i' ).toggleClass( 'icon-caret-down icon-caret-up' );
        });
    };


    /**
     * Show or hide formatting instructions for an editor.
     *
     * @since 1.9
     */
    $( document ).on( 'click', '.js-wpv-editor-instructions-toggle', function() {
        var toggle = $( this );
        self.show_hide_formatting_help( toggle );
    });


    /**
     * Show a pointer when js-wpv-show-pointer is clicked.
     *
     * The target (clicked element) must contain following data attributes:
     * - section: Slug of the section
     * - pointer-slug: Slug of the pointer
     *
     * Those two values will be used to access pointer data in the l10n object.
     *
     * self.l10n.section.pointer_slug must contain two attributes:
     * - title: A string with pointer title
     * - paragraphs: An array of strings for pointer body, each of them will be wrapped in a p tag.
     *
     * @since 1.9
     */
    $( document ).on( 'click', '.js-wpv-show-pointer', function() {
        var target = $(this);

        $('.wp-pointer').fadeOut(100);

        var pointer_data = self.l10n[ target.data('section') ][ target.data('pointer-slug') ];

        var title_html = '<h3>' + pointer_data.title + '</h3>';

        //noinspection JSUnresolvedVariable
        var content_html = _.reduce(pointer_data.paragraphs, function(memo, paragraph) {
            return memo + '<p>' + paragraph + '</p>';
        }, '');

        //noinspection JSUnresolvedVariable
        var close_button_html = '<button class="button button-primary-toolset alignright js-wpv-close-this">' + self.l10n.editor.pointer_close + '</button>';

        target.pointer({
            pointerClass: 'wp-toolset-pointer wp-toolset-views-pointer',
            content: title_html + content_html,
            position: {
                // Pass this through pointer_data if different values are required in the future.
                edge: 'left',
                align: 'right',
                offset: '-5 0'
            },
            buttons: function( event, t ) {
                var button_close = $(close_button_html);
                button_close.bind( 'click.pointer', function( e ) {
                    e.preventDefault();
                    t.element.pointer('close');
                });
                return button_close;
            }
        }).pointer( 'open' );
    });


    // ----------------------------------------------------------------------------
    // Codemirror stuff
    // ----------------------------------------------------------------------------

    /* Code for initializing and maintaining CodeMirror editors on the page. Each editor
     * should be described in the self.editors array.
     *
     * self.init_editors() should be called at the beginning of the initialization and
     * self.fill_editors() after knockout bindings are applied.
     */

    /**
     * Array with description of CM editors on the page.
     *
     * Each editor description should have these attributes:
     * - slug: CM editor slug. It will be used to store the CM instance in self.codemirror_{$slug} and
     *   if quicktags are allowed, it's instance in self.codemirror_{$slug}_quicktags.
     * - selector: id attribute of the underlying textarea.
     * - allow_quicktags: boolean, if true, quicktags will be added to the editor (basic HTML ones, hardcoded below).
     * - propertyToUpdate: Full name of the VM property (incl. the 'Accepted' suffix) that should be kept in sync
     *   with editor content.
     * - mode: Editor mode (for syntax highlighting). Allowed values depend on CM.
     *
     * @since 1.9
     */
    self.editors = [
        {
            slug: 'content',
            selector: 'wpv_content',
            allow_quicktags: true,
            propertyToUpdate: 'postContentAccepted',
            mode: undefined
        },
        {
            slug: 'css',
            selector: 'wpv_template_extra_css',
            allow_quicktags: false,
            propertyToUpdate: 'templateCssAccepted',
            mode: 'css'
        },
        {
            slug: 'js',
            selector: 'wpv_template_extra_js',
            allow_quicktags: false,
            propertyToUpdate: 'templateJsAccepted',
            mode: 'javascript'
        }
    ];


    /**
     * Initialize CodeMirror editors.
     *
     * Initialize CM editors based on their definition in self.editors. Optionally add quicktags. Store the CM
     * instance in self.codemirror_{$slug} and also in WPV_Toolset.CodeMirror_instance[$selector].
     *
     * This should be called at the beginning of the initialization.
     *
     * @since 1.9
     */
    self.init_editors = function() {
        _.each(self.editors, function(editor_info) {
            var editor_slug = 'codemirror_' + editor_info.slug;

            // create the CodeMirror instance
            self[editor_slug] = icl_editor.codemirror(editor_info.selector, true, editor_info.mode);

            // save it also in WPV_Toolset.CodeMirror_instance
            WPV_Toolset.CodeMirror_instance[editor_info.selector] = self[editor_slug];

            // Optionally add quicktags
            if(editor_info.allow_quicktags) {
                var quicktags_slug = editor_slug + '_quicktags';
                self[quicktags_slug] = quicktags( { id: editor_info.selector, buttons: 'strong,em,link,block,del,ins,img,ul,ol,li,code,close' } );
                WPV_Toolset.add_qt_editor_buttons( self[quicktags_slug],self[editor_slug] );
            }
        });
    };


    /**
     * Populate CodeMirror editors with content and bind it to ViewModel properties.
     *
     * More precisely, we will initialize the editor with the current property value and then, on editor content change,
     * we will be updating the underlying textarea, which is already bound with the ViewModel property via Knockout.
     *
     * @since 1.9
     */
    self.fill_editors = function() {
        _.each(self.editors, function(editor_info) {
            var editor_slug = 'codemirror_' + editor_info.slug;

            // Set current property's value into the editor and refresh it
            self[editor_slug].setValue(self.vm[editor_info.propertyToUpdate]());
            self[editor_slug].refresh();

            // When editor value changes, update the underlying textarea.
            self[editor_slug].on('change', function(cm) {
                var value = cm.getValue();
                $('#' + editor_info.selector).val(value);
                self.vm.log('codemirror change(' + editor_slug + '): updating vm[' + editor_info.propertyToUpdate + '] from "'
                + self.vm[ editor_info.propertyToUpdate ]() + '" to "' + value + '".');
                self.vm[editor_info.propertyToUpdate](value);
            });
        });
    };


    // ----------------------------------------------------------------------------
    // Action bar
    // ----------------------------------------------------------------------------


    /**
     * Initialize the Action bar with the "Save all changes at once" button.
     *
     * Note: This is taken from Views and WPA edit pages and IMHO should exist only once
     * in some common JS file.
     *
     * @since 1.9
     */
    self.init_action_bar = function() {

        self.action_bar = $( '#js-wpv-general-actions-bar' );

        // Update the bar width based on the first (title) section width.
        self.set_action_bar_width = _.debounce(function() {
            //noinspection JSValidateTypes
            var adminBarWidth = $('.js-wpv-title-section').find('.wpv-setting-container').width();
            //noinspection JSValidateTypes
            self.action_bar.width( adminBarWidth );
        }, 100);


        // Initialize the bar position and size and register event hooks to update those on scrolling and resizing.
        if ( self.action_bar && self.action_bar.offset() ) {
            self.action_bar_message_container = self.action_bar.find('.js-wpv-message-container' );
            var toolbarPos = self.action_bar.offset().top;
            var adminBarHeight = 0;
            var wpadminbar = $('#wpadminbar');
            if ( wpadminbar.length !== 0 ) {
                adminBarHeight = wpadminbar.height();
                self.set_action_bar_width()
            }

            self.set_toolbar_pos = function() {
                //noinspection JSValidateTypes
                if ( toolbarPos <= $(window).scrollTop() + adminBarHeight + 5) {
                    self.html.addClass('wpv-general-actions-bar-fixed');
                }
                else {
                    self.html.removeClass('wpv-general-actions-bar-fixed');
                }
            };

            $( window ).on( 'scroll', function() {
                self.set_toolbar_pos();
            });

            $( window ).on( 'resize', self.set_action_bar_width);

            self.set_toolbar_pos();
        }

    };


    /**
     * Show an effect of geen highlighting the bottom of action bar for one second.
     *
     * This is meant to indicate a successful update.
     *
     * @since 1.9
     */
    self.highlight_action_bar = function(mode) {
        var highlight_class = 'wpv-action-success';
        if('failure' == mode) {
            highlight_class = 'wpv-action-failure';
        }
        self.action_bar.addClass(highlight_class);
        setTimeout(function() { self.action_bar.removeClass(highlight_class); }, 1000);
    };


    // ----------------------------------------------------------------------------
    // Dialogs
    // ----------------------------------------------------------------------------

    /* Code for custom dialogs. Currently there is only one - Colorbox - dialog for confirmation of binding
     * dissident posts.
     *
     * This should be done better and possibly with jQuery dialogs instead. The main problem is retrieving
     * "return value" (what button has the user pressed) and performing an action based on it.
     */

    /**
     * Indicates whether a default action (cancel) should be executed when dialog closes.
     *
     * This will be set to false if user choses a non-default action (clicks on the confirm button).
     *
     * @type {boolean}
     * @since 1.9
     */
    self.bindDissidentPostsDialogExecuteOnClosed = true;


    /**
     * Display a Colorbox dialog asking if user wants to bind dissident posts of a particular type.
     *
     * Default action is to call self.vm.cancelBindingDissidentPosts().
     *
     * @param postTypeInfo {{postType: string, posts: Array}} Post type information with IDs of dissident
     *     posts of that type.
     * @param htmlTemplate {string} Dialog template.
     *
     * @since 1.9
     */
    self.showBindDissidentPostsDialog = function(postTypeInfo, htmlTemplate) {

        self.bindDissidentPostsDialogExecuteOnClosed = true;

        $.colorbox({
            html: htmlTemplate,
            onComplete: function() {
                // Populate the template
                $('.js-wpv-ct-bind-dialog-post-count').text(postTypeInfo.posts.length);

                var labelToUse = (postTypeInfo.posts.length == 1) ? 'labelSingular' : 'labelPlural';
                $('.js-wpv-ct-bind-dialog-post-type').text(postTypeInfo[labelToUse]);

                var updateButton = $('.js-wpv-bind-dissident-posts-dialog .js-wpv-dialog-update-button');
                updateButton.data('post-type-info', postTypeInfo);
            },
            onClosed: function() {
                if(self.bindDissidentPostsDialogExecuteOnClosed) {
                    self.vm.cancelBindingDissidentPosts(postTypeInfo);
                }
            }
        });
    };


    /**
     * On "bind dissident posts" dialog, handle the update button action.
     *
     * Run self.vm.finishBindingDissidentPosts and close the dialog, disabling the default action.
     *
     * @since 1.9
     */
    $(document).on('click', '.js-wpv-bind-dissident-posts-dialog .js-wpv-dialog-update-button', function() {

        self.bindDissidentPostsDialogExecuteOnClosed = false;

        var updateButton = $(this);
        var postTypeInfo = updateButton.data('post-type-info');
        self.vm.finishBindingDissidentPosts(postTypeInfo);

        $.colorbox.close();

    });



    // ----------------------------------------------------------------------------
    // Toolset compatibility
    // ----------------------------------------------------------------------------

    /**
     * Interoperation with other Toolset plugins.
     *
     * Currently used to ensure CRED button will work properly.
     *
     * @since 1.9
     */
    self.toolset_compatibility = function() {
        // CRED plugin
        if ( typeof cred_cred != 'undefined' ) {
            cred_cred.posts();
        }
    };


    // ----------------------------------------------------------------------------
    // Confirm unsaved changes before leaving the page
    // ----------------------------------------------------------------------------


    /**
     * Setup displaying confirmation message before user leaves the page with unsaved changes.
     *
     * When such situation occurs, additionally, all sections with unsaved changes will get an error message
     * informing about pending changes.
     *
     * @since 1.10
     */
    self.set_confirm_unload = function() {

        // skip if the method is not available yet
        // @todo should be safe to remove this in Views 1.10
        if(typeof(WPV_Toolset.Utils.setConfirmUnload) == 'undefined') {
            return;
        }

        var show_error_messages_for_unsaved_sections = function() {
            _.each(self.vm.sections, function(section) {
               if(section.isUpdateNeeded()) {
                   self.showErrorMessage(section.messageContainer, section.properties, self.l10n.editor.pending_changes);
               }
            });
        };

        WPV_Toolset.Utils.setConfirmUnload(self.vm.isAnyUpdateNeeded, show_error_messages_for_unsaved_sections, self.l10n.editor.confirm_unload);
    };


    // ----------------------------------------------------------------------------
    // Init
    // ----------------------------------------------------------------------------


    /**
     * Initialize the edit page script.
     *
     * @since 1.9
     */
    self.init = function() {

        self.init_editors();

        // Get the localization data
        self.l10n = wpv_ct_editor_l10n;

        // Read additional data for specific sections.
        // Note: this is not passed via ct_data, because the data is gathered only when sections
        // are being rendered.
        var section_data = {
            usage: $(self.usageOtherAssignments).data('value')
        };

        // Read the Content Template data passed as a l10n variable and initialize
        // ViewModel with them. This will also apply knockout bindings.
        self.ct_data = wpv_ct_editor_ct_data;
        self.vm = new self.ViewModel(self.ct_data, section_data);

        self.update_nonce = self.ct_data.update_nonce;
        self.trash_nonce = self.ct_data.trash_nonce;

        // Show the sections once Knockout bindings have been applied.
        $('.wpv-settings-section').each(function(){
            $(this).removeClass('hidden');
        });

        // Fill CodeMirror editors with data (has to be performed after Knockout bindings
        // have been applied and the sections displayed)
        self.fill_editors();

        self.init_action_bar();

        // Adjust admin menu link
        var menu_link = $( '.wp-has-current-submenu li.current a' );
        menu_link.attr( 'href', menu_link.attr( 'href' ) + '&ct_id=' + self.ct_data.id );

        self.toolset_compatibility();

        self.set_confirm_unload();

    };

    // Call the init method and return the instance.
    _.delay(self.init, 0);

    return self;

};

// Start doing everything when the page is loaded.
jQuery( document ).ready( function( $ ) {
    WPViews.ct_edit_screen = new WPViews.CTEditScreen( $ );
});
