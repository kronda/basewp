<?php
/**
 * I18n for admin javascript
 */

global $tve_leads_help_videos;
return array(
    /* group display settings */
    'DisplayGroupSettings' => __('Display Settings', 'thrive-leads'),
    'PleaseSelectANameForYourTemplate' =>  __("Please select a name for your template !", 'thrive-leads'),
    //
    'AddNewForm' => __('Add New Form', 'thrive-leads'),
    'AddShortcode' => __('Add New Lead Shortcode', 'thrive-leads'),
    'AddTwoStepLightbox' => __('Add New Lead 2 Step Lightbox', 'thrive-leads'),
    'AddGroup' => __('Add New Lead Group', 'thrive-leads'),
    'ReAddForm' => __('Re-add Form', 'thrive-leads'),
    'ArchiveForm' => __('Archive Form', 'thrive-leads'),
    'DeleteForm' => __('Delete Form', 'thrive-leads'),
    'StartVariationTest' => __('Start New A/B Test', 'thrive-leads'),
    'StartFormtypeTest' => __('Start New Test of Opt-In Form Types', 'thrive-leads'),
    'TestTitleRequired' => __('Test title is required', 'thrive-leads'),
    'AutoWinMinConversionsRequired' => __('Auto win minimum conversions is required', 'thrive-leads'),
    'AutoWinMinDurationRequired' => __('Auto win minimum duration is required', 'thrive-leads'),
    'AutoWinChanceOriginalRequired' => __('Auto win chance to beat original is required', 'thrive-leads'),
    'AutomaticWinnerSettings' => __('Automatic Winner Settings', 'thrive-leads'),
    'PositiveIntegerNumber' => __('This value must be a positive integer number!', 'thrive-leads'),
    'PositivePercentNumber' => __('This value must be a positive value lower than 100!', 'thrive-leads'),
    'Loading' => __('Loading...', 'thrive-leads'),
    'ChooseWinner' => __('Choose Winner', 'thrive-leads'),
    'MinimumTwoFormTypesAreRequired' => __('Minimum two form types are required to be selected to perform a test', 'thrive-leads'),
    'ResetFormStats' => __('Reset Form Statistics', 'thrive-leads'),
    'TriggerSettings' => __('Trigger Settings', 'thrive-leads'),
    'PositionSettings' => __('Position Settings', 'thrive-leads'),
    'DisplaySettings' => __('Display Settings', 'thrive-leads'),
    'AnimationSettings' => __('Animation Settings', 'thrive-leads'),
    'LockDisplaySettings' => __('Lock Display Settings', 'thrive-leads'),
    'DisplaySettingsTemplateLoaded' => __('Display Settings Template Loaded', 'thrive-leads'),
    'DisplaySettingsTemplateSaved' => __('Display Settings Template Saved', 'thrive-leads'),
    'GeneralError' => __('An unexpected error occurred. Please try again and, if this still happens, try reloading the page.', 'thrive-leads'),
    'Copied' => __('Copied', 'thrive-leads'),
    'SetWinnerFormTypeMessage' => __('You have successfully selected a winner. It is now being show as the default form. All non-winning forms have been added to the "Archived Forms" section at the bottom.', 'thrive-leads'),
    'SetWinnerGroupMessage' => __('You have successfully selected a winner. It is now being show as the default form. All non-winning forms have been added to the "Archived Forms" section at the bottom.', 'thrive-leads'),
    'SetWinnerShortcodeMessage' => __('You have successfully selected a winner. It is now being show as the default form. All non-winning forms have been added to the "Archived Forms" section at the bottom.', 'thrive-leads'),
    'SetWinnerTwoStepLightboxMessage' => __('You have successfully selected a winner. It is now being show as the default form. All non-winning forms have been added to the "Archived Forms" section at the bottom.', 'thrive-leads'),
    'GroupNameRequired' => 'Lead Group Name cannot be empty',
    'ShortcodeNameRequired' => 'Lead Shortcode Name cannot be empty',
    'TwoStepLightboxNameRequired' => '2 Step Lightbox Name cannot be empty',
    'MissingFormContent' => 'Some of the forms do not have any content. Please edit all the form designs marked below before starting a test.',
    'NoTestCompleted' => 'No tests have been completed',
    'DbOptimized' => 'Thrive Leads database tables have been optimized',
    'PHPInsertCode' => 'PHP Insert Code',
    'AddNewFormVideo' => sprintf(
        '<a href="%s" title="%s" class="tl-play-link wistia-popover[height=450,playerColor=2bb914,width=800]"><span class="tve-icon-play"> </span></a>',
        $tve_leads_help_videos['Forms'],
        __('Creating Forms', 'thrive-leads')
    ),
    'AddNewGroupVideo' => sprintf(
        '<a href="%s" title="%s" class="tl-play-link wistia-popover[height=450,playerColor=2bb914,width=800]"><span class="tve-icon-play"> </span></a>',
        $tve_leads_help_videos['LeadGroups'],
        __('Lead Groups', 'thrive-leads')
    ),
    'AddNewShortcodeVideo' => sprintf(
        '<a href="%s" title="%s" class="tl-play-link wistia-popover[height=450,playerColor=2bb914,width=800]"><span class="tve-icon-play"> </span></a>',
        $tve_leads_help_videos['LeadShortcodes'],
        __('Lead Shortcodes', 'thrive-leads')
    ),
    'AddNewTwoStepLightboxVideo' => sprintf(
        '<a href="%s" title="%s" class="tl-play-link wistia-popover[height=450,playerColor=2bb914,width=800]"><span class="tve-icon-play"> </span></a>',
        $tve_leads_help_videos['TwoStepLightbox'],
        __('Lead Shortcodes', 'thrive-leads')
    ),
    'TriggerSettingsVideo' => sprintf(
        '<a href="%s" title="%s" class="tl-play-link wistia-popover[height=450,playerColor=2bb914,width=800]"><span class="tve-icon-play"> </span></a>',
        $tve_leads_help_videos['TriggerSettings'],
        __('Trigger settings', 'thrive-leads')
    ),
    'StartVariationTestVideo' => sprintf(
        '<a href="%s" title="%s" class="tl-play-link wistia-popover[height=450,playerColor=2bb914,width=800]"><span class="tve-icon-play"> </span></a>',
        $tve_leads_help_videos['VariationTest'],
        __('Testing Form Variations', 'thrive-leads')
    ),
    'StartGroupTestVideo' => sprintf(
        '<a href="%s" title="%s" class="tl-play-link wistia-popover[height=450,playerColor=2bb914,width=800]"><span class="tve-icon-play"> </span></a>',
        $tve_leads_help_videos['GroupTest'],
        __('Testing Opt-In Form Types', 'thrive-leads')
    ),
    'GroupSettingsVideo' => sprintf(
        '<a href="%s" title="%s" class="tl-play-link wistia-popover[height=450,playerColor=2bb914,width=800]"><span class="tve-icon-play"> </span></a>',
        $tve_leads_help_videos['GroupDisplaySettings'],
        __('Group settings', 'thrive-leads')
    )
);