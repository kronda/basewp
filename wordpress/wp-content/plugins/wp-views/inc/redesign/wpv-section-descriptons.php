<?php

/*
* Array with help messages for edit view page
*/

$views_edit_help = 
    array(
        'title_and_description' => 
            array(
                'title' => htmlentities( __('Title and description', 'wpv-views'), ENT_QUOTES ), 
                'content' => htmlentities( __("Each View has a title and an optional description. These are used for you, to identify different Views. The title and the description don't appear anywhere on the site's public pages.", 'wpv-views'), ENT_QUOTES )
            ),
        'content_section' => 
            array(
                'title' => htmlentities( __('Content to load', 'wpv-views'), ENT_QUOTES ), 
                'content' => htmlentities( __('Choose between posts, taxonomy and users and then select the specific content type to load. For posts, you can select multiple content types.', 'wpv-views'), ENT_QUOTES )
            ),
        'query_options' => 
            array(
                'title' => htmlentities( __('Query options', 'wpv-views'), ENT_QUOTES ), 
                'content' => htmlentities( __('This section includes additional options for what content to load. You will see different options for posts, taxonomy and users.', 'wpv-views'), ENT_QUOTES )
            ),
        'ordering' => 
            array(
                'title' => htmlentities( __('Ordering', 'wpv-views'), ENT_QUOTES ), 
                'content' => htmlentities( __('Choose how to order the results that the View gets from the database. You can select the sorting key and direction.', 'wpv-views'), ENT_QUOTES )
            ),
        'limit_and_offset' => 
            array(
                'title' => htmlentities( __('Limit and offset', 'wpv-views'), ENT_QUOTES ), 
                'content' => htmlentities( __('You can limit the number of results returned by the query and set an offset. Please note that this option is not intended for pagination and sliders, but for static limit and offset settings.', 'wpv-views'), ENT_QUOTES )
            ),
        'filter_the_results' => 
            array(
                'title' => htmlentities( __('Query filter', 'wpv-views'), ENT_QUOTES ), 
                'content' => htmlentities( __("You can filter the View query by status, custom fields, taxonomy, users fields and even content search depending on the content that you are going to load. Click on 'Add another filter' and then select the filter type. A View may have as many filters as you like.", 'wpv-views'), ENT_QUOTES )
            ),
        'pagination_and_sliders_settings' => 
            array(
                'title' => htmlentities( __('Pagination and sliders settings', 'wpv-views'), ENT_QUOTES ), 
                'content' => htmlentities( __("You can use a View to display paginated results and sliders. Both are built using 'Pagination'. For paginated listings, choose to update the entire page. For sliders, choose to update only the View.", 'wpv-views'), ENT_QUOTES )
            ),
        'filters_html_css_js' => 
            array(
                'title' => htmlentities( __('Filter HTML/CSS/JS', 'wpv-views'), ENT_QUOTES ), 
                'content' => htmlentities( __("In this section you can add pagination controls, slider controls and parametric searches. If you enabled pagination, you need to insert the pagination controls here. They are used for both paged results and sliders. For parametric searches, insert 'filter' elements. The output of this section is displayed via the [wpv-filter-meta-html] shortcode in the Combined Output section.", 'wpv-views'), ENT_QUOTES )
            ),
		'parametric_search' => 
            array(
                'title' => htmlentities( __('Parametric search', 'wpv-views'), ENT_QUOTES ), 
                'content' => htmlentities( __("In this section you can choose when to refresh the Views results and which options to show in form inputs.", 'wpv-views'), ENT_QUOTES )
            ),
        'layout_html_css_js' => 
            array(
                'title' => htmlentities( __('View HTML output', 'wpv-views'), ENT_QUOTES ), 
                'content' => htmlentities( __('This HTML determines what the View outputs for the query results. Use the Layout wizard to create a new layout. Then, edit it by adding fields, HTML, media and anything else in the toolbar. The output of this section is displayed via the [wpv-layout-meta-html] in the Combined Output section.', 'wpv-views'), ENT_QUOTES )
            ),
        'templates_for_view' => 
            array(
                'title' => htmlentities( __('Templates for this View', 'wpv-views'), ENT_QUOTES ), 
                'content' => htmlentities( __("A View may include templates. These templates make it easy to output complex structures without having to repeat them in the View HTML. Click on the 'Content Template' button in the Layout HTML section to add Content Templates here.", 'wpv-views'), ENT_QUOTES ),          
            ),
        'complete_output' => 
            array(
                'title' => htmlentities( __('Combined Output', 'wpv-views'), ENT_QUOTES ), 
                'content' => htmlentities( __('This HTML box lets you control how the Filter and Layout sections of this Views are displayed. The [wpv-filter-meta-html] shortcode displays the output of the Filter section. The [wpv-layout-meta-html] shortcode displays the output of the Layout section. You can add your HTML and fields to rearrange and style the output.', 'wpv-views'), ENT_QUOTES )
            ),         
        'loops_selection' => 
            array(
                'title' => htmlentities( __('Loop selection', 'wpv-views'), ENT_QUOTES ), 
                'content' => htmlentities( __("Choose which listing page to customize. The WordPress archive will display the exact same content as WordPress normally does, but you can design it using the View HTML.", 'wpv-views'), ENT_QUOTES )
            ),
        'module_manager' => 
            array(
                'title' => htmlentities( __('Module Manager', 'wpv-views'), ENT_QUOTES ), 
                'content' => htmlentities( __("With Modules, you can easily reuse your designs in different websites and create your own library of building blocks.", 'wpv-views'), ENT_QUOTES )
            ),
    );
