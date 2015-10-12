<?php 

/**
* constants.php
*
*´Set some constants used along the whole plugin
*
* @package Views
*
* @since unknown
*/


//define('WPV_EDIT_BACKGROUND', '#C9F0F5');// DEPRECATED Commented out in 1.7

/*
* Edit links to documentation
*/

define('WPV_SUPPORT_LINK', 'http://wp-types.com/forums/?utm_source=viewsplugin&utm_campaign=views&utm_medium=edit-view&utm_term=support forum');

define('WPV_FILTER_BY_TAXONOMY_LINK', 'http://wp-types.com/documentation/user-guides/filtering-views-by-taxonomy/?utm_source=viewsplugin&utm_campaign=views&utm_medium=edit-view-category-filter&utm_term=Learn about filtering by taxonomy');

define('WPV_FILTER_BY_CUSTOM_FIELD_LINK', 'http://wp-types.com/documentation/user-guides/filtering-views-by-custom-fields/?utm_source=viewsplugin&utm_campaign=views&utm_medium=edit-view-custom-fields-filter&utm_term=Learn about filtering by custom fields');

define('WPV_ADD_FILTER_CONTROLS_LINK', 'http://wp-types.com/documentation/user-guides/front-page-filters/?utm_source=viewsplugin&utm_campaign=views&utm_medium=edit-view-filter-controls&utm_term=filter controls');

define('WPV_FILTER_BY_AUTHOR_LINK', 'http://wp-types.com/documentation/user-guides/filtering-views-query-by-author/?utm_source=viewsplugin&utm_campaign=views&utm_medium=edit-view-author-filter&utm_term=Learn about filtering by Post Author');

define('WPV_FILTER_BY_POST_PARENT_LINK', 'http://wp-types.com/documentation/user-guides/filtering-views-query-by-post-parent/?utm_source=viewsplugin&utm_campaign=views&utm_medium=edit-view-post-parent-filter&utm_term=Learn about filtering by Post Parent');

define('WPV_FILTER_BY_SPECIFIC_TEXT_LINK', 'http://wp-types.com/documentation/user-guides/filtering-views-for-a-specific-text-string-search/?utm_source=viewsplugin&utm_campaign=views&utm_medium=edit-view-search-text-filter&utm_term=Learn about filtering for a specific text string');

define('WPV_FILTER_BY_POST_ID_LINK', 'http://wp-types.com/documentation/user-guides/filtering-views-query-by-post-id/?utm_source=viewsplugin&utm_campaign=views&utm_medium=edit-view-post-ids-filter&utm_term=Learn about filtering by Post ID');

define('WPV_FILTER_BY_USERS_LINK', 'http://wp-types.com/documentation/user-guides/filtering-views-query-by-author/?utm_source=viewsplugin&utm_campaign=views&utm_medium=undefined&utm_term=undefined');

define('WPV_FILTER_BY_USER_FIELDS_LINK', 'http://wp-types.com/documentation/user-guides/filtering-views-by-custom-fields/?utm_source=viewsplugin&utm_campaign=views&utm_medium=edit-view-users-fields-filter&utm_term=Learn about filtering by user fields');

define('WPV_FILTER_BY_POST_DATE_LINK', 'http://wp-types.com/documentation/user-guides/filtering-views-query-by-date/?utm_source=viewsplugin&utm_campaign=views&utm_medium=edit-view-post-date-filter&utm_term=Learn about filtering by Post Date');

// Views layout constants

define('WPV_TAXONOMY_VIEW', 'wpv-view'); // A view used inside another taxonomy view
define('WPV_POST_VIEW', 'wpv-post-view'); // A view used inside another post view

$view_fields = array(WPV_TAXONOMY_VIEW => 'taxonomy_view_',
               WPV_POST_VIEW => 'post_view_');
