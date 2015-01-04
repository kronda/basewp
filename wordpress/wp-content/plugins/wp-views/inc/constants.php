<?php 

define('WPV_DEFAULT_LIST_ITEMS', 10);
define('WPV_EDIT_BACKGROUND', '#C9F0F5');

define('WPV_SUPPORT_LINK', 'http://wp-types.com/forums/');

define('WPV_FILTER_BY_TAXONOMY_LINK', 'http://wp-types.com/documentation/user-guides/filtering-views-by-taxonomy/');

define('WPV_FILTER_BY_CUSTOM_FIELD_LINK', 'http://wp-types.com/documentation/user-guides/filtering-views-by-custom-fields/');

define('WPV_ADD_FILTER_CONTROLS_LINK', 'http://wp-types.com/documentation/user-guides/front-page-filters/');

define('WPV_FILTER_BY_AUTHOR_LINK', 'http://wp-types.com/documentation/user-guides/filtering-views-query-by-author/');

define('WPV_FILTER_BY_POST_PARENT_LINK', 'http://wp-types.com/documentation/user-guides/filtering-views-query-by-post-parent/');

define('WPV_FILTER_BY_SPECIFIC_TEXT_LINK', 'http://wp-types.com/documentation/user-guides/filtering-views-for-a-specific-text-string-search/');

define('WPV_FILTER_BY_POST_ID_LINK', 'http://wp-types.com/documentation/user-guides/filtering-views-query-by-post-id/');
//Filter by users
define('WPV_FILTER_BY_USERS_LINK', 'http://wp-types.com/documentation/user-guides/filtering-views-query-by-author/');
//Filter by usermeta
define('WPV_FILTER_BY_USER_FIELDS_LINK', 'http://wp-types.com/documentation/user-guides/filtering-views-by-custom-fields/');


// Views layout constants.

define('WPV_TAXONOMY_VIEW', 'wpv-view'); // A view used inside another taxonomy view
define('WPV_POST_VIEW', 'wpv-post-view'); // A view used inside another post view

$view_fields = array(WPV_TAXONOMY_VIEW => 'taxonomy_view_',
               WPV_POST_VIEW => 'post_view_');
