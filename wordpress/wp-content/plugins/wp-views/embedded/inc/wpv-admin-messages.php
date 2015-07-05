<?php

/**
* wpv-admin-messages.php
*
* @package Views
*
* @since unknown
*
* @note be sure htmlentities is applied to data-attributes
*/

/**
* WPV_Admin_Messages
*
* Wrapper for static methods used to display all sort of admin messages in Views
*
* @since 1.7
*/

class WPV_Admin_Messages {
	
	//----------------------------------------
	// Help boxes
	//----------------------------------------
	
	/**
	* toolset_help_box
	*
	* Creates the HTML version for wpvToolsetHelp()
	*
	* @param data array containing the attributes
	*
	* @return echo HTML box
	*
	* @since unknown
	*
	* @todo when wpvToolsetHelp() is moved to a common utils.js file, this can be moved to a common file too
	*/
	
	static function toolset_help_box( $data = array() ) {
		if ( is_array( $data ) && ! empty( $data ) ) {
			$data_attr = '';
			foreach ( $data as $key => $value ) {
				if ( 'text' != $key ) {
					$data_attr .= ' data-' . $key . '="' . esc_attr( $value ) . '"';
				}
			}
			?>
			<div class="js-show-toolset-message"<?php echo $data_attr; ?>>
			<?php if ( isset( $data['text'] ) ) {
				echo $data['text'];
			} ?>
			</div>
		<?php }
	}
	
	//----------------------------------------
	// Toggle boxes
	//----------------------------------------
	
	// @todo maybe move this along with script to common
	
	/**
	* render_toggle_structure
	*
	* Renders a complete toggle structure
	*
	* @param $args (array)
	*     'toggler_classname' => (string) the toggler classname
	*     'toggler_target' => (string) the toggler target classname
	*     'toggler_title' => (string) the toggler title
	*     'toggled_classname' => (string) the toggled classname
	*     'toggled_intro' => (string) the toggled intro
	*     'toggled_sections' => (array) the toggled sections
	*/
	
	static function render_toggle_structure( $args = array() ) {
		$args_default = array(
			'toggler_classname' => '',
			'toggler_target' => '',
			'toggler_title' => '',
			'toggled_classname' => '',
			'toggled_intro' => '',
			'toggled_sections' => array()
		);
		$args = wp_parse_args( $args, $args_default );
		WPV_Admin_Messages::render_toggle_toggler( $args['toggler_classname'], $args['toggler_target'], $args['toggler_title'] );
		WPV_Admin_Messages::render_toggle_toggled( $args['toggled_classname'], $args['toggled_intro'], $args['toggled_sections'] );
	}
	
	/**
	* render_toggle_toggler
	*
	* Display the toggler span
	*
	* @param $classname (string) classname of the toggler
	* @param $target (string) classname of the div to toggle
	* @param $title (string) text to show inside the span
	*
	* @return echo the span HTML tag
	*
	* @since 1.7
	*/
	
	static function render_toggle_toggler( $classname = '', $target = '', $title = '' ) {
		?>
		<span class="<?php echo $classname; ?>" data-target="<?php echo $target; ?>">
			<span class="wpv-toggle-toggler-icon js-wpv-toggle-toggler-icon">
				<i class="icon-caret-down icon-large"></i>
			</span>
			<?php echo $title; ?>
		</span>
		<?php
	}
	
	/**
	* render_toggle_toggled
	*
	* Display the toggled div
	*
	* @param $classname (string) classname of the div to toggle including the $target one in the toggler
	* @param $intro (string) text to introduce the toggler
	* "param $sections (array) list of sections to display
	*
	* @return echo the div HTML tag
	*
	* @since 1.7
	*/
	
	static function render_toggle_toggled( $classname = '', $intro = '', $sections = array() ) {
		?>
		<div class="<?php echo $classname; ?> hidden">
		<?php
		if ( ! empty( $intro ) ) {
		?>
			<?php echo $intro; ?>
		<?php
		}
		if ( ! empty( $sections ) ) {
			foreach ( $sections as $section ) {
				$args = wpv_views_instructions_section_data( $section );
				WPV_Admin_Messages::render_toggle_section( $args );
			}
		}
		?>
		</div>
		<?php
	}
	
	/**
	* render_toggle_section
	*
	* Display a section inside a toggled div
	*
	* @param $args (array)
	*     $args['classname'] (string) the classname of the section
	*     $args['title'] (string) the h4 title of the section
	*     $args['content'] (string) the introduction text of the section
	*     $args['table'] (array) the elements in the table of the section, as an array:
	*         array(
	*             'element' => the title of the element
	*             'description' => the description of the element
	*         )
	*     $args['content_extra'] (string) the text to display after the table
	*
	* @return echo the span HTML tag
	*
	* @since 1.7
	*/
	
	static function render_toggle_section( $args = array() ) {
		if ( $args == false ) {
			return;
		}
		$args_default = array(
			'classname' => '',
			'title' => '',
			'content' => '',
			'table' => array(),
			'content_extra' => ''
		);
		$args = wp_parse_args( $args, $args_default );
		?>
		<div class="<?php echo $args['classname']; ?>">
		<?php 
		if ( ! empty( $args['title'] ) ) {
		?>
			<h4><?php echo $args['title']; ?></h4>
		<?php 
		}
		if ( ! empty( $args['content'] ) ) {
		?>
			<?php echo $args['content']; ?>
		<?php
		}
		if ( ! empty( $args['table'] ) ) {
		?>
			<table class="widefat">
				<thead>
					<tr>
						<th style="width:200px;">
							<?php _e( 'Element', 'wpv-views' ); ?>
						</th>
						<th>
							<?php _e( 'Description', 'wpv-views' ); ?>
						</th>
					</tr>
				</thead>
				<tbody>
					<?php
					foreach ( $args['table'] as $table_row_index => $table_row ) {
					?>
					<tr <?php if ( ! ( $table_row_index % 2 ) ) { echo 'class="alt"'; } ?>>
						<td>
							<?php echo $table_row['element']; ?>
						</td>
						<td>
							<?php echo $table_row['description']; ?>
						</td>
					</tr>
					<?php
					} ?>
				</tbody>
			</table>
		<?php
		}
		if ( ! empty( $args['content_extra'] ) ) {
		?>
			<?php echo $args['content_extra']; ?>
		<?php
		}
		?>
		</div>
		<?php
	}
	
	//----------------------------------------
	// Help hints per edit sections - full plugin mode
	//----------------------------------------

	/**
	* edit_section_help_pointer
	*
	* Help pointer contents for custom edit pages sections
	*
	* @param $section (string) Section
	*
	* @return $return (array) Both title and description for the section help pointer
	*
	* @since 1.8.0
	*/

	static function edit_section_help_pointer( $section = '' ) {
		$return = array(
			'title' => '', 
			'content' => ''
		);
		switch ( $section ) {
			case 'title_and_description':
				$return = array(
					'title' => __( 'Title and Description', 'wpv-views' ), 
					'content' => __("Each View has a title and an optional description. These are used for you, to identify different Views. The title and the description don't appear anywhere on the site's public pages.", 'wpv-views')
				);
				break;
			case 'content_section':
				$return = array(
					'title' => __('Content to load', 'wpv-views'), 
					'content' => __('Choose between posts, taxonomy and users and then select the specific content type to load. For posts, you can select multiple content types.', 'wpv-views')
				);
				break;
			case 'query_options':
				$return = array(
					'title' => __('Query Options', 'wpv-views'), 
					'content' => __('This section includes additional options for what content to load. You will see different options for posts, taxonomy and users.', 'wpv-views')
				);
				break;
			case 'ordering':
				$return = array(
					'title' => __('Ordering', 'wpv-views'),
					'content' => __('Choose how to order the results that the View gets from the database. You can select the sorting key and direction.', 'wpv-views')
				);
				break;
			case 'limit_and_offset':
				$return = array(
					'title' => __('Limit and Offset', 'wpv-views'),
					'content' => __('You can limit the number of results returned by the query and set an offset. Please note that this option is not intended for pagination and sliders, but for static Limit and Offset settings.', 'wpv-views')
				);
				break;
			case 'filter_the_results':
				$return = array(
					'title' => __('Query Filter', 'wpv-views'),
					'content' => __("You can filter the View query by status, custom fields, taxonomy, users fields and even content search depending on the content that you are going to load. Click on 'Add another filter' and then select the filter type. A View may have as many filters as you like.", 'wpv-views')
				);
				break;
			case 'pagination_and_sliders_settings':
				$return = array(
					'title' => __('Pagination and sliders settings', 'wpv-views'),
					'content' => __("You can use a View to display paginated results and sliders. Both are built using 'Pagination'. For paginated listings, choose to update the entire page. For sliders, choose to update only the View.", 'wpv-views')
				);
				break;
			case 'filters_html_css_js':
				$return = array(
					'title' => __('Filter', 'wpv-views'),
					'content' => __("In this section you can add pagination controls, slider controls and parametric searches. If you enabled pagination, you need to insert the pagination controls here. They are used for both paged results and sliders. For parametric searches, insert 'filter' elements. The output of this section is displayed via the [wpv-filter-meta-html] shortcode in the Filter and Loop Output Integration section.", 'wpv-views')
				);
				break;
			case 'parametric_search':
				$return = array(
					'title' => __('Parametric search', 'wpv-views'),
					'content' => __("In this section you can choose when to refresh the Views results and which options to show in form inputs.", 'wpv-views')
				);
				break;
			case 'layout_html_css_js':
				$return = array(
					'title' => __('View HTML output', 'wpv-views'),
					'content' => __('This HTML determines what the View outputs for the query results. Use the Loop Wizard to design the output of this View. Then, edit the design by adding fields, HTML and media, using the toolbar buttons.', 'wpv-views')
				);
				break;
			case 'layout_extra_js':
				$return = array(
					'title' => __('Additional Javascript files', 'wpv-views'),
					'content' => __('Here you can set the URLs of additional scripts that need to be loaded with this View. Use a comma separated list of URLs', 'wpv-views')
				);
				break;
			case 'templates_for_view':
				$return = array(
					'title' => __('Templates for this View', 'wpv-views'),
					'content' => __("A View may include Content Templates. These templates make it easy to output design structures without having to repeat them in the loop.", 'wpv-views')
				);
				break;
			case 'complete_output':
				$return = array(
					'title' => __('Filter and Loop Output Integration', 'wpv-views'),
					'content' => __( 'This editor lets you control how the Filter and Loop Output sections of this View are displayed. The [wpv-filter-meta-html] shortcode displays the output of the Filter section. The [wpv-layout-meta-html] shortcode displays the output of the Loop Output section. You can add your HTML and fields to rearrange and style the output.', 'wpv-views' )
				);
				break;
			case 'loops_selection':
				$return = array(
					'title' => __('Loop selection', 'wpv-views'),
					'content' => __("Choose which listing page to customize. The WordPress archive will display the exact same content as WordPress normally does, but you can design it using the View HTML.", 'wpv-views')
				);
				break;
			case 'loops_selection_layouts':
				$return = array(
					'title' => __('Layouts archive loop', 'wpv-views'),
					'content' => __('This WordPress Archive is used in a Layout, so it will be used in the archive loops that the Layout has been assigned to.', 'wpv-views')
				);
				break;
			case 'module_manager':
				$return = array(
					'title' => __('Module Manager', 'wpv-views'),
					'content' => __("With Modules, you can easily reuse your designs in different websites and create your own library of building blocks.", 'wpv-views')
				);
				break;
		}
		return $return;
	}
	
}

//----------------------------------------
// Help boxes texts - function
//----------------------------------------

/**
* wpv_toolset_help_box
*
* Creates the HTML version for wpvToolsetHelp()
* 
* @param data array() containing the attributes
* @return echo HTML box
*
* @since unknown
*
* @todo when wpvToolsetHelp() is moved to a common utils.js file, this can be moved to a common file too
* @todo use the method above
*/

function wpv_toolset_help_box( $data ) { 
	if ( is_array( $data ) && ! empty( $data ) ) { ?>
	<div class="js-show-toolset-message"<?php foreach ( $data as $key => $value ) {if ( 'text' != $key ) { ?> data-<?php echo $key; ?>="<?php echo esc_attr( $value ); ?>"<?php } } ?>>
	<?php if ( isset( $data['text'] ) ) {
		echo $data['text'];
	} ?>
	</div>
	<?php }
}

//----------------------------------------
// Help boxes texts - full plugin mode
//----------------------------------------

/**
* wpv_get_view_introduction_data
*
* ToolSet Help Box for Views
* Adds a different help box for each View purpose, will be shown/hidden using a script
*
* @return echo the main help boxes for the Query section
*
* @since unknown
*/

function wpv_get_view_introduction_data() {
	$all = array(
		'text'			=> '<p>' . __('A View loads content from the database and displays it anyway you choose.', 'wpv-views') . WPV_MESSAGE_SPACE_CHAR
						. __('The Query section lets you choose the content to load.', 'wpv-views') . WPV_MESSAGE_SPACE_CHAR
						. __('A basic query selects all items of a chosen type.', 'wpv-views') . '</p>'
						. '<ul><li>' . __('You can refine the selection by adding filters.', 'wpv-views') . '</li>'
						. '<li>' . __('At the bottom of this page you will find the Loop Output section, where you control the output.', 'wpv-views') . '</li></ul>',
		'close'			=> 'true',
		'hidden'		=> 'true',
		'classname'		=> 'js-metasection-help-query js-for-view-purpose-all'
	);
	wpv_toolset_help_box($all);
	$pagination = array(
		'text'			=> '<p>' . __('A View loads content from the database and displays it anyway you choose.', 'wpv-views') . WPV_MESSAGE_SPACE_CHAR
						. __('The Query section lets you choose the content to load.', 'wpv-views') . WPV_MESSAGE_SPACE_CHAR
						. __('A basic query selects all items of a chosen type.', 'wpv-views') . '</p>'
						. '<ul><li>' . __('You can refine the selection by adding filters.', 'wpv-views') . '</li>'
						. '<li>' . __('The Front-end Filter section includes the pagination controls, allowing visitors to choose which results page to show.', 'wpv-views') . '</li>'
						. '<li>' . __('At the bottom of this page you will find the Loop Output section, where you control the output.', 'wpv-views') . '</li></ul>',
		'tutorial-button-text'	=> htmlentities( __('Creating paginated listings with Views', 'wpv-views'), ENT_QUOTES ),
		'tutorial-button-url'	=> WPV_LINK_CREATE_PAGINATED_LISTINGS,
		'close'			=> 'true',
		'hidden'		=> 'true',
		'classname'		=> 'js-metasection-help-query js-for-view-purpose-pagination'
	);
	wpv_toolset_help_box($pagination);
	$slider = array(
		'text'			=> '<p>' . __('A View loads content from the database and displays it anyway you choose.','wpv-views') . WPV_MESSAGE_SPACE_CHAR
						. __('The Query section lets you choose the content to load.','wpv-views') . WPV_MESSAGE_SPACE_CHAR
						. __('A basic query selects all items of a chosen type.','wpv-views') . '</p>'
						. '<ul><li>' . __('You can refine the selection by adding filters.','wpv-views') . '</li>'
						. '<li>' . __('The Front-end Filter section includes the slider controls, allowing visitors switch between slides.','wpv-views') . '</li>'
						. '<li>' . __('At the bottom of this page you will find a slide Content Template, where you design slides.', 'wpv-views') . '</li></ul>',
		'tutorial-button-text'	=> htmlentities( __('Creating sliders with Views', 'wpv-views'),ENT_QUOTES ),
		'tutorial-button-url'	=> WPV_LINK_CREATE_SLIDERS,
		'close'			=> 'true',
		'hidden'		=> 'true',
		'classname'		=> 'js-metasection-help-query js-for-view-purpose-slider'
	);
	wpv_toolset_help_box($slider);
	$parametric = array(
		'text'			=> '<h3>' . __('Building a View for a parametric search:', 'wpv-views') . '</h3>'
						. '<ol><li>' . __('Select which content to load in the \'Content Selection\' section.', 'wpv-views') . '</li>'
						. '<li>' . __('Add filter input to the Filter section.', 'wpv-views') . '</li>'
						. '<li>' . __('Select advanced search options in the \'Parametric Search Settings\' section.', 'wpv-views') . '</li>'
						. '<li>' . __('Design the search results in the Loop Output section.', 'wpv-views') . '</li>'
						. '<li class="js-layouts-search-help" style="display:none">' . __('Select if you want the form, the results or both displayed in the Layouts cell.', 'wpv-views') . '</li></ol>'
						. '<p>' . __('Remember to click on Update after you complete each section and before you continue to the next section.', 'wpv-views') . '</p>',
		'tutorial-button-text'	=> htmlentities( __('Creating parametric searches with Views', 'wpv-views'), ENT_QUOTES ),
		'tutorial-button-url'	=> WPV_LINK_CREATE_PARAMETRIC_SEARCH,
		'close'			=> 'true',
		'hidden'		=> 'true',
		'classname'		=> 'js-metasection-help-query js-for-view-purpose-parametric'
	);
	wpv_toolset_help_box($parametric);
	$full = array(
		'text'			=> '<p>' . __('A View loads content from the database and displays it anyway you choose.', 'wpv-views') . WPV_MESSAGE_SPACE_CHAR
						. __('The Query section lets you choose the content to load.', 'wpv-views') . WPV_MESSAGE_SPACE_CHAR
						. __('A basic query selects all items of a chosen type.', 'wpv-views') . '</p>'
						. '<ul><li>' . __('You can refine the selection by adding filters.','wpv-views') . '</li>'
						. '<li>' . __('The Front-end filter section lets you add pagination, slider controls and parametric search to the View.', 'wpv-views') . '</li>'
						. '<li>' .  __('At the bottom of this page you will find the Loop Output section, where you control the output.', 'wpv-views') . '</li></ul>',
		'close'			=> 'true',
		'hidden'		=> 'true',
		'classname'		=> 'js-metasection-help-query js-for-view-purpose-full'
	);
	wpv_toolset_help_box($full);
}

/**
* wpv_get_view_filter_introduction_data
*
* ToolSet Help Box for Views
* Pagination introduction
*
* @return echo the main help boxes for the Filter section
*
* @since unknown
*/

function wpv_get_view_filter_introduction_data() {
	$pagination = array(
		'text'			=> '<p>' . __('Pagination lets you break the results into separate pages.', 'wpv-views') . WPV_MESSAGE_SPACE_CHAR
						. __('This way, you can display a large number of results, in shorter pages.', 'wpv-views') . WPV_MESSAGE_SPACE_CHAR
						. __('You can insert next/previous links and page selectors, for navigating directly to a specific page.', 'wpv-views') . '</p>'
						. '<ul><li>' . __('The first part of this section lets you choose how pagination works.', 'wpv-views') . WPV_MESSAGE_SPACE_CHAR
						. __('Select how many results to show in each page and how pages transition.', 'wpv-views') . '</li>'
						. '<li>' . __('The second part of this section lets you design the pagination controls that would appear on the page for visitors.', 'wpv-views') . WPV_MESSAGE_SPACE_CHAR
						. __('The toolbar above the HTML editor includes buttons for inserting various controls.', 'wpv-views') . '</li>'
						. '<li>' . __('Besides pagination, you can also insert parametric search filters and content search controls.', 'wpv-views') . '</li></ul>',
		'close'			=> 'true',
		'hidden'		=> 'true',
		'classname'		=> 'js-metasection-help-filter js-for-view-purpose-pagination'
	);
	wpv_toolset_help_box($pagination);
	$slider = array(
		'text'			=> '<p>' . __('The pagination section lets you build sliders with Views.', 'wpv-views') . WPV_MESSAGE_SPACE_CHAR
						. __('The View will display each slide at a time and allow visitors to switch between slides using next/previous links and slide selectors.', 'wpv-views') . '</p>'
						. '<ul><li>' . __('The first part of this section lets you choose how the slider pagination works.', 'wpv-views') . WPV_MESSAGE_SPACE_CHAR
						. __('Select how many results to show in each slide and how slides transition.', 'wpv-views') . '</li>'
						. '<li>' . __('The second part of this section lets you design the transition controls for the slider.', 'wpv-views') . WPV_MESSAGE_SPACE_CHAR
						. __('The toolbar above the HTML editor includes buttons for inserting the slide transition controls.', 'wpv-views') . '</li></ul>',
		'tutorial-button-text'	=> htmlentities( __('Creating sliders with Views', 'wpv-views'), ENT_QUOTES ),
		'tutorial-button-url'	=> WPV_LINK_DESIGN_SLIDER_TRANSITIONS,
		'close'			=> 'true',
		'hidden'		=> 'true',
		'classname'		=> 'js-metasection-help-filter js-for-view-purpose-slider'
	);
	wpv_toolset_help_box($slider);
	$full = array(
		'text'			=> '<p>' . __('Pagination lets you break the results into separate pages.', 'wpv-views') . WPV_MESSAGE_SPACE_CHAR
						. __('This way, you can display a large number of results, in shorter pages.', 'wpv-views') . WPV_MESSAGE_SPACE_CHAR
						. __('You can insert next/previous links and page selectors, for navigating directly to a specific page.','wpv-views') . '</p><p>'
						. __('Using pagination you can also implement sliders.', 'wpv-views') . WPV_MESSAGE_SPACE_CHAR
						. '<a href="' . WPV_LINK_CREATE_SLIDERS . '" target="_blank">' . __('Learn how to create sliders with Views.', 'wpv-views') . '</a></p>'
						. '<ul><li>' . __('The first part of this section lets you choose how pagination works.', 'wpv-views') . WPV_MESSAGE_SPACE_CHAR
						. __('Select how many results to show in each page and how pages transition.', 'wpv-views') . '</li>'
						. '<li>' . __('The second part of this section lets you design the pagination controls that would appear on the page for visitors.', 'wpv-views') . WPV_MESSAGE_SPACE_CHAR
						. __('The toolbar above the HTML editor includes buttons for inserting various controls.', 'wpv-views') . '</li>'
						. '<li>' . __('Besides pagination and slider transition controls, you can also insert parametric search filters and content search controls.', 'wpv-views') . '</li></ul>'
						. '<p><a href="' . WPV_LINK_CREATE_PARAMETRIC_SEARCH . '" target="_blank">' . __('Learn how to create parametric searches with Views.', 'wpv-views') . '</a></p>',
		'close'			=> 'true',
		'hidden'		=> 'true',
		'classname'		=> 'js-metasection-help-filter js-for-view-purpose-full'
	);
	wpv_toolset_help_box($full);
	$parametric = array(
		'text'			=> '<h3>' . __(' Parametric Search Instructions', 'wpv-views' ) . '</h3>'
						. '<p>' . __('This section controls how the search box for your parametric search will behave and appear.','wpv-views') . '</p>'
						. '<ol><li>' . __('First, choose how to update the results. Depending on your selections, the Filter HTML editor will include different controls.','wpv-views') . '</li>'
						. '<li>' . __('Then, add input filters and search-form controls to the Filter section.','wpv-views') . '</li></ul>'
						. '<p>' . __('Use the "formatting and editing instructions" bar at the bottom of the editor to see full editing instructions.','wpv-views') . '</p>'
						. '<p><a href="' . WPV_LINK_CREATE_PARAMETRIC_SEARCH . '" target="_blank" class="btn">' . __('Complete Views Parametric Search tutorial', 'wpv-views') . '</a></p>'
						. ' <input id="wpv-parametric-hint-dismiss" type="hidden" class="js-wpv-parametric-hint-dismiss" data-nonce="' . wp_create_nonce( 'wpv_view_parametric_hint_dismiss_nonce')  . '" /> ',
		'close'			=> 'true',
		'hidden'		=> 'true',
		'classname'		=> 'js-metasection-help-filter js-for-view-purpose-parametric',
	);
	wpv_toolset_help_box($parametric);
}

/**
* wpv_get_view_layout_introduction_data
*
* ToolSet Help Box for Views
* Layout introduction
*
* @return echo the main help box for the Layout section
*
* @since unknown
*/

function wpv_get_view_layout_introduction_data() {
	$result = array(
		'text'			=> '<h3>' . __('How to Design the Output','wpv-views') . '</h3>'
					. '<p>' . __('The Loop Output HTML editor lets you style the output of the View.', 'wpv-views') . '</p>'
					. '<p>' . __('The content between the <strong>&lt;wpv-loop&gt;</strong> and <strong>&lt;/wpv-loop&gt;</strong> tags will repeat for every item in the View loop.') . '</p>'
					. '<p>' . __('To get started easily, click on the <strong>Loop Wizard</strong>. You will select the loop output style and the fields to display. Then, edit by adding you own HTML markup, media and additional fields.', 'wpv-views') . WPV_MESSAGE_SPACE_CHAR
						. sprintf( __('Learn more by reading the %sViews Loop documentation%s.', 'wpv-views'), '<a href="' . WPV_LINK_LOOP_DOCUMENTATION . '" target="_blank">', '</a>' ) . '</p>'
						. '<p>' . __('The <strong>Filter and Loop Output Integration</strong> section lets you control the order of the <strong>Filter</strong> and <strong>Loop Output</strong> sections and to add your HTML between these two sections.', 'wpv-views') . '</p>'
						. ' <input id="wpv-layout-hint-dismiss" type="hidden" class="js-wpv-layout-hint-dismiss" data-nonce="' . wp_create_nonce( 'wpv_view_layout_hint_dismiss_nonce')  . '" /> ',
		'close'			=> 'true',
		'hidden'		=> 'true',
		'classname'		=> 'js-metasection-help-layout js-for-view-purpose-all js-for-view-purpose-pagination js-for-view-purpose-slider js-for-view-purpose-parametric js-for-view-purpose-full',
	);
	return $result;
}

/**
* wpv_get_view_ct_slider_introduction_data
*
* ToolSet Help Box for Views
* Content Template: View slider mode
*
* @return echo the main help boxes for the Content template section section
*
* @since unknown
*/
function wpv_get_view_ct_slider_introduction_data() {
    $data = array(
        'text'          => '<p class="js-wpv-ct-was-inserted">'
						. __('This Content Template lets you design slides in this slider. Add any field you need to display and design them using HTML and CSS. To style the slide transition controls, scroll up to the filter section.', 'wpv-views')
						. '</p>',                      
        'close'         => 'false',
        'hidden'        => 'false',
        'classname'     => 'js-wpv-content-template-slider-hint',
        'footer'        => 'false'
    );
	wpv_toolset_help_box( $data );
}

/**
* wpv_display_view_howto_help_box
*
* View display help box
*
* @return echo the help box about displaying this View
*
* @since unknown
*/

function wpv_display_view_howto_help_box() {
	$general = array(
		'text'			=> '<h3>' . __( 'How to display this View', 'wpfv-views' ) . '</h3>' 
						. '<p>' . __('You can display this View inside content or as a widget.', 'wpv-views') . '</p><p>'
						. __('To display inside content (post, page, custom type), edit that content.', 'wpv-views') . WPV_MESSAGE_SPACE_CHAR
						. __( 'You will find the <strong>Views</strong> button.', 'wpv-views' ) . WPV_MESSAGE_SPACE_CHAR
						. __( 'Click on it and locate this View to insert it anywhere you want inside the content.', 'wpv-views' ) . '</p><p>'
						. sprintf( __('To display as a widget, go to <a href="%s">Appearance -> Widgets</a> and select the <strong>WP Views</strong> widget.', 'wpv-views'), admin_url( 'widgets.php' ) ) . '</p><p>'
						. '</p>',
		'classname'		=> 'js-display-view-howto js-for-view-purpose-all js-for-view-purpose-pagination js-for-view-purpose-slider js-for-view-purpose-full'
	);
	wpv_toolset_help_box( $general );
	$parametric = array(
		'text'			=> '<h3>' . __( 'How to display this Parametric Search View', 'wpfv-views' ) . '</h3>' 
						. '<p>' . __( 'This View contains a search box and results list.', 'wpv-views' ) . WPV_MESSAGE_SPACE_CHAR
						. __( 'You can display them together, on one page, or have the search in one page and the results on another page.', 'wpv-views' ) . '</p><p>'
						. __('Start with the location where you want the search box to appear.', 'wpv-views') . WPV_MESSAGE_SPACE_CHAR
						. __( 'You can display the search inside content or as a widget.', 'wpv-views' ) . '</p><p>'
						. __('To display inside content (post, post, custom type), edit that content.', 'wpv-views') . WPV_MESSAGE_SPACE_CHAR
						. __( 'You will find the <strong>Views</strong> button.', 'wpv-views' ) . WPV_MESSAGE_SPACE_CHAR
						. __( 'Click on it and locate this View to insert it anywhere you want inside the content.', 'wpv-views' ) . '</p><p>'
						. sprintf( __('To display as a widget, go to <a href="%s">Appearance -> Widgets</a> and select the <strong>WP Views Filter</strong> widget.', 'wpv-views'), admin_url( 'widgets.php' ) ) . '</p><p>'
						. __( 'When you insert the search box, Views will offer you where to display the results.', 'wpv-views' )
						. '</p>',
		'classname'		=> 'js-display-view-howto js-for-view-purpose-parametric'
	);
	wpv_toolset_help_box( $parametric );
}

//----------------------------------------
// Help boxes texts - embedded plugin mode
//----------------------------------------

function wpv_get_embedded_promotional_box( $type = 'view' ) {
	$target = __( 'View', 'wpv-views' );
	switch ( $type ) {
		case 'view':
			$target = __( 'View', 'wpv-views' );
			break;
		case 'ct':
			$target = __( 'Content Template', 'wpv-views' );
			break;
		case 'wpa':
			$target = __( 'WordPress Archive', 'wpv-views' );
			break;
	}
	$data = array(
		'text'			=> '<p>' . sprintf( __('You are viewing the read-only version of this %s.', 'wpv-views'), $target )
						. WPV_MESSAGE_SPACE_CHAR
						. __('To edit it, you need to get the Views plugin.', 'wpv-views')
						. '</p>'
						. '<p><a href="http://wp-types.com/home/views-create-elegant-displays-for-your-content/?utm_source=viewsplugin&utm_campaign=views&utm_medium=embedded-view-promotional-link&utm_term=Get Views" title="" class="button button-primary-toolset" target="_blank">'
						. __( 'Get Views', 'wpv-views' )
						. '</a></p>',
		'close'			=> 'false',
		'hidden'		=> 'false',
		'classname'		=> ''
	);
	wpv_toolset_help_box( $data );
}

function wpv_get_embedded_view_introduction_data() {
	$promotional = '<p class="toolset-promotional">' . __( 'You are viewing the read-only version of this View. To edit it, you need to get Views plugin.', 'wpv-views' )
				. '&nbsp;&nbsp;&nbsp;<a href="http://wp-types.com/home/views-create-elegant-displays-for-your-content/?utm_source=viewsplugin&utm_campaign=views&utm_medium=embedded-view-promotional-link&utm_term=Get Views" title="" class="button button-primary-toolset" target="_blank">' . __( 'Get Views', 'wpv-views' ) . '</a>'
				. '</p>';
	$all = array(
		'text'			=> $promotional . '<p>' . __('A View loads content from the database and displays it anyway you choose.', 'wpv-views') . WPV_MESSAGE_SPACE_CHAR
						. __('The Query section lets you choose the content to load.', 'wpv-views') . WPV_MESSAGE_SPACE_CHAR
						. __('A basic query selects all items of a chosen type.', 'wpv-views') . '</p>'
						. '<ul><li>' . __('You can refine the selection by adding filters.', 'wpv-views') . '</li>'
						. '<li>' . __('At the bottom of this page you will find the Loop Output section, where you control the output.', 'wpv-views') . '</li></ul>',
		'close'			=> 'false',
		'hidden'		=> 'true',
		'classname'		=> 'js-metasection-help-query js-for-view-purpose-all'
	);
	wpv_toolset_help_box($all);
	$pagination = array(
		'text'			=> $promotional . '<p>' . __('A View loads content from the database and displays it anyway you choose.', 'wpv-views') . WPV_MESSAGE_SPACE_CHAR
						. __('The Query section lets you choose the content to load.', 'wpv-views') . WPV_MESSAGE_SPACE_CHAR
						. __('A basic query selects all items of a chosen type.', 'wpv-views') . '</p>'
						. '<ul><li>' . __('You can refine the selection by adding filters.', 'wpv-views') . '</li>'
						. '<li>' . __('The Front-end Filter section includes the pagination controls, allowing visitors to choose which results page to show.', 'wpv-views') . '</li>'
						. '<li>' . __('At the bottom of this page you will find the Loop Output section, where you control the output.', 'wpv-views') . '</li></ul>',
		'tutorial-button-text'	=> htmlentities( __('Creating paginated listings with Views', 'wpv-views'), ENT_QUOTES ),
		'tutorial-button-url'	=> WPV_LINK_CREATE_PAGINATED_LISTINGS,
		'close'			=> 'false',
		'hidden'		=> 'true',
		'classname'		=> 'js-metasection-help-query js-for-view-purpose-pagination'
	);
	wpv_toolset_help_box($pagination);
	$slider = array(
		'text'			=> $promotional . '<p>' . __('A View loads content from the database and displays it anyway you choose.','wpv-views') . WPV_MESSAGE_SPACE_CHAR
						. __('The Query section lets you choose the content to load.','wpv-views') . WPV_MESSAGE_SPACE_CHAR
						. __('A basic query selects all items of a chosen type.','wpv-views') . '</p>'
						. '<ul><li>' . __('You can refine the selection by adding filters.','wpv-views') . '</li>'
						. '<li>' . __('The Front-end Filter section includes the slider controls, allowing visitors switch between slides.','wpv-views') . '</li>'
						. '<li>' . __('At the bottom of this page you will find a slide Content Template, where you design slides.', 'wpv-views') . '</li></ul>',
		'tutorial-button-text'	=> htmlentities( __('Creating sliders with Views', 'wpv-views'),ENT_QUOTES ),
		'tutorial-button-url'	=> WPV_LINK_CREATE_SLIDERS,
		'close'			=> 'false',
		'hidden'		=> 'true',
		'classname'		=> 'js-metasection-help-query js-for-view-purpose-slider'
	);
	wpv_toolset_help_box($slider);
	$parametric = array(
		'text'			=> $promotional . '<p>' . __('Building a View for a parametric search:', 'wpv-views') . '</p>'
						. '<ol><li>' . __('Select which content to load in the \'Content Selection\' section.', 'wpv-views') . '</li>'
						. '<li>' . __('Add filter input to the Filter section.', 'wpv-views') . '</li>'
						. '<li>' . __('Select advanced search options in the \'Parametric Search Settings\' section.', 'wpv-views') . '</li>'
						. '<li>' . __('Design the search results in the Loop Output section.', 'wpv-views') . '</li></ol>'
						. '<p>' . __('Remember to click on Update after you complete each section and before you continue to the next section.', 'wpv-views') . '</p>',
		'tutorial-button-text'	=> htmlentities( __('Creating parametric searches with Views', 'wpv-views'), ENT_QUOTES ),
		'tutorial-button-url'	=> WPV_LINK_CREATE_PARAMETRIC_SEARCH,
		'close'			=> 'false',
		'hidden'		=> 'true',
		'classname'		=> 'js-metasection-help-query js-for-view-purpose-parametric'
	);
	wpv_toolset_help_box($parametric);
	$full = array(
		'text'			=> $promotional . '<p>' . __('A View loads content from the database and displays it anyway you choose.', 'wpv-views') . WPV_MESSAGE_SPACE_CHAR
						. __('The Query section lets you choose the content to load.', 'wpv-views') . WPV_MESSAGE_SPACE_CHAR
						. __('A basic query selects all items of a chosen type.', 'wpv-views') . '</p>'
						. '<ul><li>' . __('You can refine the selection by adding filters.','wpv-views') . '</li>'
						. '<li>' . __('The Front-end filter section lets you add pagination, slider controls and parametric search to the View.', 'wpv-views') . '</li>'
						. '<li>' .  __('At the bottom of this page you will find the Loop Output section, where you control the output.', 'wpv-views') . '</li></ul>',
		'close'			=> 'false',
		'hidden'		=> 'true',
		'classname'		=> 'js-metasection-help-query js-for-view-purpose-full'
	);
	wpv_toolset_help_box($full);
}

function wpv_get_embedded_content_template_introduction_data() {
	$all = array(
		'text'			=> '<p>' . __('This Content Template replaces the content area of the posts that you assign it to.', 'wpv-views')
						. WPV_MESSAGE_SPACE_CHAR .  __('It can be used to tweak the content of a post when it is displayed alone or in an archive page.', 'wpv-views') . '</p>'
						. '<p>' . __( 'You can also call this Template using a shortcode [wpv-post-body view_template="XX"] to render specific information about the current post.', 'wpv-views' ) . '</p>'
						. '<p>' . __('You can add shortcodes to post fields, and also your own HTML and CSS to style the fields and design the page template.', 'wpv-views') . '</p>',
		'tutorial-button-text'	=> htmlentities( __('Content Template documentation', 'wpv-views'), ENT_QUOTES ),
		'tutorial-button-url'	=> WPV_LINK_CONTENT_TEMPLATE_DOCUMENTATION,
		'close'			=> 'false',
		'hidden'		=> 'false'
	);
	wpv_toolset_help_box($all);
}

function wpv_get_embedded_wordpress_archive_introduction_data() {
	$promotional = '<p class="toolset-promotional">' . __( 'You are viewing the read-only version of this WordPress Archive. To edit it, you need to get Views plugin.', 'wpv-views' )
				. '&nbsp;&nbsp;&nbsp;<a href="http://wp-types.com/home/views-create-elegant-displays-for-your-content/?utm_source=viewsplugin&utm_campaign=views&utm_medium=embedded-archive-view-promitional-link&utm_term=Get Views" title="" class="button-primary button-primary-toolset">' . __( 'Get Views', 'wpv-views' ) . '</a>'
				. '</p>';
	$all = array(
		'text'			=> $promotional . '<p>' . __('This WordPress Archive replaces the natural archive loops created by WordPress.', 'wpv-views') . '</p>',
		'tutorial-button-text'	=> htmlentities( __('WordPress Archives documentation', 'wpv-views'), ENT_QUOTES ),
		'tutorial-button-url'	=> WPV_LINK_WORDPRESS_ARCHIVE_DOCUMENTATION,
		'close'			=> 'false',
		'hidden'		=> 'false'
	);
	wpv_toolset_help_box($all);
}

function wpv_get_embedded_layouts_loop_introduction_data() {
	$promotional = '<p class="toolset-promotional">' . __( 'You are viewing the read-only version of this WordPress Archive. To edit it, you need to get Views plugin.', 'wpv-views' )
				. '&nbsp;&nbsp;&nbsp;<a href="http://wp-types.com/home/views-create-elegant-displays-for-your-content/?utm_source=viewsplugin&utm_campaign=views&utm_medium=embedded-arhive-loop-promitional-link&utm_term=Get Views" title="" class="button-primary button-primary-toolset">' . __( 'Get Views', 'wpv-views' ) . '</a>'
				. '</p>';
	$all = array(
		'text'			=> $promotional . '<p>' . __('This WordPress Archive replaces the natural archive loops created by WordPress.', 'wpv-views') . '</p>',
		'tutorial-button-text'	=> htmlentities( __('WordPress Archives documentation', 'wpv-views'), ENT_QUOTES ),
		'tutorial-button-url'	=> WPV_LINK_WORDPRESS_ARCHIVE_DOCUMENTATION,
		'close'			=> 'false',
		'hidden'		=> 'false'
	);
	wpv_toolset_help_box($all);
}

function wpv_get_embedded_view_filter_introduction_data() {
	$pagination = array(
		'text'			=> '<p>' . __('Pagination lets you break the results into separate pages.', 'wpv-views') . WPV_MESSAGE_SPACE_CHAR
						. __('This way, you can display a large number of results, in shorter pages.', 'wpv-views') . WPV_MESSAGE_SPACE_CHAR
						. __('You can insert next/previous links and page selectors, for navigating directly to a specific page.', 'wpv-views') . '</p>'
						. '<ul><li>' . __('The first part of this section lets you choose how pagination works.', 'wpv-views') . WPV_MESSAGE_SPACE_CHAR
						. __('Select how many results to show in each page and how pages transition.', 'wpv-views') . '</li>'
						. '<li>' . __('The second part of this section lets you design the pagination controls that would appear on the page for visitors.', 'wpv-views') . WPV_MESSAGE_SPACE_CHAR
						. __('The toolbar above the HTML editor includes buttons for inserting various controls.', 'wpv-views') . '</li>'
						. '<li>' . __('Besides pagination, you can also insert parametric search filters and content search controls.', 'wpv-views') . '</li></ul>',
		'close'			=> 'false',
		'hidden'		=> 'true',
		'classname'		=> 'js-metasection-help-filter js-for-view-purpose-pagination'
	);
	wpv_toolset_help_box($pagination);
	$slider = array(
		'text'			=> '<p>' . __('The pagination section lets you build sliders with Views.', 'wpv-views') . WPV_MESSAGE_SPACE_CHAR
						. __('The View will display each slide at a time and allow visitors to switch between slides using next/previous links and slide selectors.', 'wpv-views') . '</p>'
						. '<ul><li>' . __('The first part of this section lets you choose how the slider pagination works.', 'wpv-views') . WPV_MESSAGE_SPACE_CHAR
						. __('Select how many results to show in each slide and how slides transition.', 'wpv-views') . '</li>'
						. '<li>' . __('The second part of this section lets you design the transition controls for the slider.', 'wpv-views') . WPV_MESSAGE_SPACE_CHAR
						. __('The toolbar above the HTML editor includes buttons for inserting the slide transition controls.', 'wpv-views') . '</li></ul>',
		'tutorial-button-text'	=> htmlentities( __('Creating sliders with Views', 'wpv-views'), ENT_QUOTES ),
		'tutorial-button-url'	=> WPV_LINK_DESIGN_SLIDER_TRANSITIONS,
		'close'			=> 'false',
		'hidden'		=> 'true',
		'classname'		=> 'js-metasection-help-filter js-for-view-purpose-slider'
	);
	wpv_toolset_help_box($slider);
	$full = array(
		'text'			=> '<p>' . __('Pagination lets you break the results into separate pages.', 'wpv-views') . WPV_MESSAGE_SPACE_CHAR
						. __('This way, you can display a large number of results, in shorter pages.', 'wpv-views') . WPV_MESSAGE_SPACE_CHAR
						. __('You can insert next/previous links and page selectors, for navigating directly to a specific page.','wpv-views') . '</p><p>'
						. __('Using pagination you can also implement sliders.', 'wpv-views') . WPV_MESSAGE_SPACE_CHAR
						. '<a href="' . WPV_LINK_CREATE_SLIDERS . '" target="_blank">' . __('Learn how to create sliders with Views.', 'wpv-views') . '</a></p>'
						. '<ul><li>' . __('The first part of this section lets you choose how pagination works.', 'wpv-views') . WPV_MESSAGE_SPACE_CHAR
						. __('Select how many results to show in each page and how pages transition.', 'wpv-views') . '</li>'
						. '<li>' . __('The second part of this section lets you design the pagination controls that would appear on the page for visitors.', 'wpv-views') . WPV_MESSAGE_SPACE_CHAR
						. __('The toolbar above the HTML editor includes buttons for inserting various controls.', 'wpv-views') . '</li>'
						. '<li>' . __('Besides pagination and slider transition controls, you can also insert parametric search filters and content search controls.', 'wpv-views') . '</li></ul>'
						. '<p><a href="' . WPV_LINK_CREATE_PARAMETRIC_SEARCH . '" target="_blank">' . __('Learn how to create parametric searches with Views.', 'wpv-views') . '</a></p>',
		'close'			=> 'false',
		'hidden'		=> 'true',
		'classname'		=> 'js-metasection-help-filter js-for-view-purpose-full'
	);
	wpv_toolset_help_box($full);
	$parametric = array(
		'text'			=> '<p>' . sprintf(__('To create a parametric search, position the cursor between the %s and %s shortcodes and click on the <strong>New filter</strong> button to insert filter elements.', 'wpv-views'),'<strong>[wpv-filter-controls]</strong>','<strong>[/wpv-filter-controls]</strong>') . WPV_MESSAGE_SPACE_CHAR
						. __('Your parametric search can contain any custom field or taxonomy that this View queries.', 'wpv-views') . '</p>'
						. '<p>' . __('You can also click on the <strong>Content search</strong> button to add a search box for visitors', 'wpv-views') . '</p>'
						. '<p>'. __('Use HTML and CSS to style the filter.', 'wpv-views') . WPV_MESSAGE_SPACE_CHAR
						. __('Remember to include the <strong>Submit</strong> button for the form.', 'wpv-views'). '</p>'
						. '<p><a href="' . WPV_LINK_CREATE_PARAMETRIC_SEARCH . '" target="_blank">' . __('Learn how to create parametric searches with Views.', 'wpv-views') . '</a></p>'
						. ' <input id="wpv-parametric-hint-dismiss" type="hidden" class="js-wpv-parametric-hint-dismiss" data-nonce="' . wp_create_nonce( 'wpv_view_parametric_hint_dismiss_nonce')  . '" /> ',
		'close'			=> 'false',
		'hidden'		=> 'true',
		'classname'		=> 'js-metasection-help-filter js-for-view-purpose-parametric',
	);
	wpv_toolset_help_box($parametric);
}

function wpv_get_embedded_view_layout_introduction_data() {
	$data = array(
		'text'			=> '<p>' . __('The layout HTML box lets you output your View results and style them on your page.', 'wpv-views') . WPV_MESSAGE_SPACE_CHAR
						.  __('Views will provide a wizard that lets you design how the results display.', 'wpv-views') . WPV_MESSAGE_SPACE_CHAR
						.  __('You can also insert fields manually.', 'wpv-views') . '</p>'
						. '<p>' . __('The full Views plugin will let you display Content Templates as part of the loop.', 'wpv-views') . '</p>'
						. '<p>' . __('Besides these helpers, you can edit the HTML content yourself by writing your own HTML and CSS.', 'wpv-views') . WPV_MESSAGE_SPACE_CHAR
						. __('The View will iterate through the the results and display them one by one.', 'wpv-views') . WPV_MESSAGE_SPACE_CHAR
						. sprintf( __('Learn more by reading the %sViews Loop documentation%s.', 'wpv-views'), '<a href="' . WPV_LINK_LOOP_DOCUMENTATION . '" target="_blank">', '</a>' ) . '</p>',
		'close'			=> 'false',
		'hidden'		=> 'true',
		'classname'		=> 'js-metasection-help-layout js-for-view-purpose-all js-for-view-purpose-pagination js-for-view-purpose-slider js-for-view-purpose-parametric js-for-view-purpose-full',
	);
	wpv_toolset_help_box( $data );
}

//----------------------------------------
// Help boxes texts - parametric search workflow
//----------------------------------------
	
/**
* wpv_insert_form_workflow_help_boxes
*
* Display a help box in the post create/edit screen above the post editor, with instructions to complete the workflow for parametric search
*
* @param $post (object)
*
* @since 1.7.0
*/

add_action( 'edit_form_after_title', 'wpv_insert_form_workflow_help_boxes' );

function wpv_insert_form_workflow_help_boxes( $post ) {
	$excluded_post_type_slugs = array();
	$excluded_post_type_slugs = apply_filters( 'wpv_admin_exclude_post_type_slugs', $excluded_post_type_slugs );
	if ( ! in_array( $post->post_type, $excluded_post_type_slugs ) ) {
		$has_view = false;
		$has_orig = false;
		$has_orig_completed = false;
		$has_completed = false;
		$has_orig_type = '';
		$orig_title = '';
		$orig_content = '';
		$view_name = '<strong class="js-wpv-insert-view-form-results-helper-name"></strong>';
		$view_shortcode = '<code class="js-wpv-insert-view-form-results-helper-shortcode"></code>';
		$view_classname = '';
		
		//-----
		// Preparation
		//-----
		
		if ( isset( $_GET['completeview'] ) && ! empty( $_GET['completeview'] ) ) {
			
			global $wpdb;
			$view_id = $_GET['completeview'];
			$title = $wpdb->get_var( 
				$wpdb->prepare( 
					"SELECT post_title FROM {$wpdb->posts} 
					WHERE ID = %d 
					LIMIT 1", 
					$view_id 
				) 
			);
			if ( $title !== NULL ) {
				$has_view = true;
				$view_name = '<strong class="js-wpv-insert-view-form-results-helper-name">' . $title . '</strong>';
				$view_shortcode = '<code class="js-wpv-insert-view-form-results-helper-shortcode">[wpv-view name="' . $title . '"]</code>';
				$view_classname = ' js-wpv-insert-form-workflow-help-box-for-' . $view_id;
				$view_classname_after = ' js-wpv-insert-form-workflow-help-box-for-after-' . $view_id;
				if ( strpos( $post->post_content, '[wpv-view name="' . $title ) !== false ) {
					$has_completed = true;
				}
				if ( isset( $_GET['origid'] ) && !empty( $_GET['origid'] ) &&  $_GET['origid'] != '0' ) {
					$orig_id = $_GET['origid'];
					if ( $orig_id == 'widget' || $orig_id == 'layout' ) {
						$has_orig = true;
						$has_orig_type = $orig_id;
					} else {
						$orig_data_array = $wpdb->get_results( 
							$wpdb->prepare( 
								"SELECT post_title, post_content FROM {$wpdb->posts} 
								WHERE ID = %d 
								LIMIT 1", 
								$orig_id 
							) 
						);
						if ( !empty( $orig_data_array ) ) {
							$has_orig = true;
							$has_orig_type = 'post';
							$orig_data = $orig_data_array[0];
							$orig_title = $orig_data->post_title;
							$orig_content = $orig_data->post_content;
							if ( strpos( $orig_content, '[wpv-form-view name="' . $title ) !== false ) {
								$has_orig_completed = true;
							}
						}
					}
				}
			}
		}
		
		//-----
		// Execution
		//-----
		
		if ( ! $has_view ) {
			// Add the basic help box for the SELF case, hidden
			$data_def = array(
				'text'			=> '<h2>' . __( 'Complete the parametric search setup by inserting the results', 'wpv-views' ) . '</h2>'
								. '<p>' . sprintf( __('This page should display the results of the parametric search provided by the View <strong>%s</strong>.', 'wpv-views'), $view_name ) . WPV_MESSAGE_SPACE_CHAR
								. sprintf( __( 'You can copy and paste this shortcode wherever you want to display the results: %s', 'wpv-views' ), $view_shortcode ) . '</p>'
								. '<p>' . sprintf( __( 'Also, you can click in the <strong>Views</strong> button and select <strong>%s</strong> in the <em>View</em> section.', 'wpv-views' ), $view_name ) . WPV_MESSAGE_SPACE_CHAR
								. __( 'Then, select the option to display just the results for the parametric search.', 'wpv-views' ) . '</p>',
				'close'			=> 'true',
				'hidden'		=> 'true',
				'classname'		=> 'js-wpv-insert-form-workflow-help-box'
			);
			wpv_toolset_help_box( $data_def );
		} else {
			// There is a $_GET['completeview'] attribute, and it matches an existing View
			if ( $has_orig ) {
				// There is also a $_GET['origid'] attribute
				switch ( $has_orig_type ) {
					case 'post':
						// Has View data and orig data, so show everything
						if ( $has_completed ) {
							if ( $has_orig_completed ) {
								// Target has shortcode, and orig has shortcode
								$data = array(
									'text'			=> '<h2>' . __( 'Parametric search setup completed!', 'wpv-views' ) . '</h2>'
													. '<p>' . sprintf( __('You have finished the setup of the parametric search provided by the View <strong>%s</strong>.', 'wpv-views'), $view_name ) . WPV_MESSAGE_SPACE_CHAR
													. sprintf( __( 'The form will appear on the page <strong>%s</strong> and the results will be shown in this page.', 'wpv-views' ), $orig_title ) . '</p>'
													. '<p><a href="#" class="button button-small button-primary-toolset js-wpv-insert-form-workflow-help-box-close">' . __( 'Close', 'wpv-views' ) . '</a></p>',
									'close'			=> 'true',
									'hidden'		=> 'false',
									'classname'		=> 'js-wpv-insert-form-workflow-help-box' . $view_classname
								);
								wpv_toolset_help_box( $data );
							} else {
								// Target has shortcode, but orig lacks shortcode
								$data = array(
									'text'			=> '<h2>' . sprintf( __( 'Don\'t forget to insert the parametric search box into %s', 'wpv-views' ), $orig_title ) . '</h2>'
													. '<p>' . sprintf( __('You are almost done with this parametric search provided by the View <strong>%s</strong>.', 'wpv-views'), $view_name ) . WPV_MESSAGE_SPACE_CHAR
													. __( 'This page already has all it needs to display the results.', 'wpv-views' ) . WPV_MESSAGE_SPACE_CHAR
													. sprintf( __( 'Remember to get back to the other tab in your browser and insert the search View into <strong>%s</strong>.', 'wpv-views' ), $orig_title ) . '</p>'
													. '<p><a href="#" class="button button-small button-primary-toolset js-wpv-insert-form-workflow-help-box-close">' . __( 'Close', 'wpv-views' ) . '</a></p>',
									'close'			=> 'true',
									'hidden'		=> 'false',
									'classname'		=> 'js-wpv-insert-form-workflow-help-box' . $view_classname
								);
								wpv_toolset_help_box( $data );
							}
						} else {
							$data = array(
								'text'			=> '<h2>' . __( 'Complete the parametric search setup by inserting the results', 'wpv-views' ) . '</h2>'
												. '<p>' . sprintf( __('This page should display the results of the parametric search provided by the View <strong>%s</strong>.', 'wpv-views'), $view_name ) . WPV_MESSAGE_SPACE_CHAR
												. sprintf( __( 'You can copy and paste this shortcode wherever you want to display the results: %s', 'wpv-views' ), $view_shortcode ) . '</p>'
												. '<p>' . sprintf( __( 'Also, you can click in the <strong>Views</strong> button and select <strong>%s</strong> in the <em>View</em> section.', 'wpv-views' ), $view_name ) . WPV_MESSAGE_SPACE_CHAR
												. __( 'Then, select the option to display just the results for the parametric search.', 'wpv-views' ) . '</p>',
								'close'			=> 'true',
								'hidden'		=> 'false',
								'classname'		=> 'js-wpv-insert-form-workflow-help-box' . $view_classname
							);
							wpv_toolset_help_box( $data );
							if ( $has_orig_completed ) {
								// After inserting the shortcode, we can call it complete!
								$data_after = array(
									'text'			=> '<h2>' . __( 'Parametric search setup completed!', 'wpv-views' ) . '</h2>'
													. '<p>' . sprintf( __('You have finished the setup of the parametric search provided by the View <strong>%s</strong>.', 'wpv-views'), $view_name ) . WPV_MESSAGE_SPACE_CHAR
													. sprintf( __( 'The form will appear on the page <strong>%s</strong> and the results will be shown in this page.', 'wpv-views' ), $orig_title ) . '</p>'
													. '<p><a href="#" class="button button-small button-primary-toolset js-wpv-insert-form-workflow-help-box-close">' . __( 'Close', 'wpv-views' ) . '</a></p>',
									'close'			=> 'true',
									'hidden'		=> 'true',
									'classname'		=> 'js-wpv-insert-form-workflow-help-box-after' . $view_classname_after
								);
								wpv_toolset_help_box( $data_after );
							} else {
								// After inserting, origin is lacking shortcode
								$data_after = array(
									'text'			=> '<h2>' . sprintf( __( 'Don\'t forget to insert the parametric search box into %s', 'wpv-views' ), $orig_title ) . '</h2>'
													. '<p>' . sprintf( __('You are almost done with this parametric search provided by the View <strong>%s</strong>.', 'wpv-views'), $view_name ) . WPV_MESSAGE_SPACE_CHAR
													. __( 'This page already has all it needs to display the results.', 'wpv-views' ) . WPV_MESSAGE_SPACE_CHAR
													. sprintf( __( 'Remember to get back to the other tab in your browser and insert the search View into <strong>%s</strong>.', 'wpv-views' ), $orig_title ) . '</p>'
													. '<p><a href="#" class="button button-small button-primary-toolset js-wpv-insert-form-workflow-help-box-close">' . __( 'Close', 'wpv-views' ) . '</a></p>',
									'close'			=> 'true',
									'hidden'		=> 'true',
									'classname'		=> 'js-wpv-insert-form-workflow-help-box-after' . $view_classname_after
								);
								wpv_toolset_help_box( $data_after );
							}
						}
						break;
					case 'widget':
					case 'layout':
						if ( $has_orig_type == 'widget' ) {
							$orig_label = __( 'widget', 'wpv-views' );
						} else if ( $has_orig_type == 'layout' ) {
							$orig_label = __( 'layout', 'wpv-views' );
						}
						// Has View data and orig data from widget, so show everything
						if ( $has_completed ) {
							$data = array(
								'text'			=> '<h2>' . __( 'Parametric search setup completed!', 'wpv-views' ) . '</h2>'
													. '<p>' . sprintf( __('This page will display the results of the parametric search provided by the View <strong>%s</strong> used in a %s.', 'wpv-views'), $view_name, $orig_label ) . '</p>'
													. '<p>' . sprintf( __( 'Remember to get back to the other tab in your browser and save the %s settings.', 'wpv-views' ), $orig_label ) . '</p>'
													. '<p><a href="#" class="button button-small button-primary-toolset js-wpv-insert-form-workflow-help-box-close">' . __( 'Close', 'wpv-views' ) . '</a></p>',
								'close'			=> 'true',
								'hidden'		=> 'false',
								'classname'		=> 'js-wpv-insert-form-workflow-help-box' . $view_classname
							);
						} else {
							$data = array(
								'text'			=> '<h2>' . __( 'Complete the parametric search setup by inserting the results', 'wpv-views' ) . '</h2>'
												. '<p>' . sprintf( __('This page should display the results of the parametric search provided by the View <strong>%s</strong> used in a %s.', 'wpv-views'), $view_name, $orig_label ) . WPV_MESSAGE_SPACE_CHAR
												. sprintf( __( 'You can copy and paste this shortcode wherever you want to display the results: %s', 'wpv-views' ), $view_shortcode ) . '</p>'
												. '<p>' . sprintf( __( 'Also, you can click in the <strong>Views</strong> button and select <strong>%s</strong> in the <em>View</em> section.', 'wpv-views' ), $view_name ) . WPV_MESSAGE_SPACE_CHAR
												. __( 'Then, select the option to display just the results for the parametric search.', 'wpv-views' ) . '</p>',
								'close'			=> 'true',
								'hidden'		=> 'false',
								'classname'		=> 'js-wpv-insert-form-workflow-help-box' . $view_classname
							);
							$data_after = array(
								'text'			=> '<h2>' . __( 'Parametric search setup completed!', 'wpv-views' ) . '</h2>'
													. '<p>' . sprintf( __('This page will display the results of the parametric search provided by the View <strong>%s</strong> used in a %s.', 'wpv-views'), $view_name, $orig_label ) . '</p>'
													. '<p>' . sprintf( __( 'Remember to get back to the other tab in your browser and save the %s settings.', 'wpv-views' ), $orig_label ) . '</p>'
													. '<p><a href="#" class="button button-small button-primary-toolset js-wpv-insert-form-workflow-help-box-close">' . __( 'Close', 'wpv-views' ) . '</a></p>',
								'close'			=> 'true',
								'hidden'		=> 'true',
								'classname'		=> 'js-wpv-insert-form-workflow-help-box-after' . $view_classname_after
							);
							wpv_toolset_help_box( $data_after );
						}
						wpv_toolset_help_box( $data );
						break;
					default:
						break;
				}
				// We also need to add basic help box, for maybe future SELF cases, hidden
				$data_def = array(
					'text'			=> '<h2>' . __( 'Complete the parametric search setup by inserting the results', 'wpv-views' ) . '</h2>'
									. '<p>' . sprintf( __('This page should display the results of the parametric search provided by the View <strong>%s</strong>.', 'wpv-views'), $view_name ) . WPV_MESSAGE_SPACE_CHAR
									. sprintf( __( 'You can copy and paste this shortcode wherever you want to display the results: %s', 'wpv-views' ), $view_shortcode ) . '</p>'
									. '<p>' . sprintf( __( 'Also, you can click in the <strong>Views</strong> button and select <strong>%s</strong> in the <em>View</em> section.', 'wpv-views' ), $view_name ) . WPV_MESSAGE_SPACE_CHAR
									. __( 'Then, select the option to display just the results for the parametric search.', 'wpv-views' ) . '</p>',
					'close'			=> 'true',
					'hidden'		=> 'true',
					'classname'		=> 'js-wpv-insert-form-workflow-help-box'
				);
				wpv_toolset_help_box( $data_def );
			} else {
				// There is no valid $_GET['origid'] attribute
				// We check whether the current page has the shortcode already inserted or not
				if ( $has_completed ) {
					// It has View data, no orig data and is completed
					$data_already_inserted = array(
						'text'			=> '<h2>' . __( 'Parametric search setup completed!', 'wpv-views' ) . '</h2>'
										. '<p>' . sprintf( __('You have finished the setup of the parametric search provided by the View <strong>%s</strong>.', 'wpv-views'), $view_name ) . '</p>'
										. '<p><a href="#" class="button button-small button-primary-toolset js-wpv-insert-form-workflow-help-box-close">' . __( 'Close', 'wpv-views' ) . '</a></p>',
						'close'			=> 'true',
						'hidden'		=> 'false',
						'classname'		=> 'js-wpv-insert-form-workflow-help-box-completed'
					);
					wpv_toolset_help_box( $data_already_inserted );
					// We also add the basic help box for SELF, hidden
					$data_def = array(
						'text'			=> '<h2>' . __( 'Complete the parametric search setup by inserting the results', 'wpv-views' ) . '</h2>'
										. '<p>' . sprintf( __('This page should display the results of the parametric search provided by the View <strong>%s</strong>.', 'wpv-views'), $view_name ) . WPV_MESSAGE_SPACE_CHAR
										. sprintf( __( 'You can copy and paste this shortcode wherever you want to display the results: %s', 'wpv-views' ), $view_shortcode ) . '</p>'
										. '<p>' . sprintf( __( 'Also, you can click in the <strong>Views</strong> button and select <strong>%s</strong> in the <em>View</em> section.', 'wpv-views' ), $view_name ) . WPV_MESSAGE_SPACE_CHAR
										. __( 'Then, select the option to display just the results for the parametric search.', 'wpv-views' ) . '</p>',
						'close'			=> 'true',
						'hidden'		=> 'true',
						'classname'		=> 'js-wpv-insert-form-workflow-help-box'
					);
					wpv_toolset_help_box( $data_def );
				} else {
					// It has View data, no orig data and is not completed
					// So we display the basic help box with View data
					$data = array(
						'text'			=> '<h2>' . __( 'Complete the parametric search setup by inserting the results', 'wpv-views' ) . '</h2>'
										. '<p>' . sprintf( __('This page should display the results of the parametric search provided by the View <strong>%s</strong>.', 'wpv-views'), $view_name ) . WPV_MESSAGE_SPACE_CHAR
										. sprintf( __( 'You can copy and paste this shortcode wherever you want to display the results: %s', 'wpv-views' ), $view_shortcode ) . '</p>'
										. '<p>' . sprintf( __( 'Also, you can click in the <strong>Views</strong> button and select <strong>%s</strong> in the <em>View</em> section.', 'wpv-views' ), $view_name ) . WPV_MESSAGE_SPACE_CHAR
										. __( 'Then, select the option to display just the results for the parametric search.', 'wpv-views' ) . '</p>',
						'close'			=> 'true',
						'hidden'		=> 'false',
						'classname'		=> 'js-wpv-insert-form-workflow-help-box' . $view_classname
					);
					wpv_toolset_help_box( $data );
				}
			}
		}
	}
}

//----------------------------------------
// Formatting instruction boxes
//----------------------------------------

function wpv_views_instructions_section_data( $section = '' ) {
	$return = array(
		'classname' => '',
		'title' => '',
		'content' => '',
		'table' => array(),
		'content_extra' => ''
	);
	switch ( $section ) {
		case 'styling':
			$return = array(
				'classname' => 'js-wpv-editor-instructions-for-styling',
				'title' => __( 'Styling', 'wpv-views' ),
				'content' => '',
				'table' => array(
					array(
						'element' => '<span class="wpv-code wpv-code-html">&lt;p&gt;</span>' . __( 'Content', 'wpv-views' ) . '<span class="wpv-code wpv-code-html">&lt;/p&gt;</span>',
						'description' => __( 'Paragraph.', 'wpv-views' )
					),
					array(
						'element' => '<span class="wpv-code wpv-code-html">&lt;br /&gt;</span>',
						'description' => __( 'Line break.', 'wpv-views' )
					),
					array(
						'element' => '<span class="wpv-code wpv-code-html">&lt;div&gt;</span>' . __( 'Content', 'wpv-views' ) . '<span class="wpv-code wpv-code-html">&lt;/div&gt;</span>',
						'description' => __( 'Blocking div.', 'wpv-views' )
					),
					array(
						'element' => '<span class="wpv-code wpv-code-html">&lt;span&gt;</span>' . __( 'Content', 'wpv-views' ) . '<span class="wpv-code wpv-code-html">&lt;/span&gt;</span>',
						'description' => __( 'Inline formatting.', 'wpv-views' )
					),
					array(
						'element' => '<span class="wpv-code wpv-code-html">&lt;a ...&gt;</span>' . __( 'Text', 'wpv-views' ) . '<span class="wpv-code wpv-code-html">&lt;/a&gt;</span>',
						'description' => __( 'Link.', 'wpv-views' )
					),
					array(
						'element' => '<span class="wpv-code wpv-code-html">&lt;img ... /&gt;</span>',
						'description' => __( 'Image.', 'wpv-views' )
					),
					array(
						'element' => '<span class="wpv-code wpv-code-html">&lt;ul&gt;<br />&lt;li&gt;</span>' . __( 'Item1', 'wpv-views' ) . '<span class="wpv-code wpv-code-html">&lt;/li&gt;<br />&lt;li&gt;</span>' . __( 'Item2' , 'wpv-views' ) . '<span class="wpv-code wpv-code-html">&lt;/li&gt;<br />&lt;/ul&gt;</span>',
						'description' => __( 'An unordered list.', 'wpv-views' )
					),
					array(
						'element' => '<span class="wpv-code wpv-code-html">&lt;ol&gt;<br />&lt;li&gt;</span>' . __( 'Item1', 'wpv-views' ) . '<span class="wpv-code wpv-code-html">&lt;/li&gt;<br />&lt;li&gt;</span>' . __( 'Item2' , 'wpv-views' ) . '<span class="wpv-code wpv-code-html">&lt;/li&gt;<br />&lt;/ol&gt;</span>',
						'description' => __( 'An ordered list.', 'wpv-views' )
					),
					array(
						'element' => '<span class="wpv-code wpv-code-html">&lt;strong&gt;</span>' . __( 'Content', 'wpv-views' ) . '<span class="wpv-code wpv-code-html">&lt;/strong&gt;</span>',
						'description' => __( 'Strong (bold) text.', 'wpv-views' )
					)
				),
				'content_extra' => ''
			);
			break;
		case 'fields-and-views':
			$return = array(
				'classname' => 'js-wpv-editor-instructions-for-fields-and-views',
				'title' => __( 'Fields and Views', 'wpv-views' ),
				'content' => '<p>'
						. __( 'Click on the <strong>Fields and Views</strong> button to insert fields that belong to the content, as well as Views and other Content Templates.', 'wpv-views' )
						. '</p>',
				'table' => array(),
				'content_extra' => ''
			);
			break;
		case 'media':
			$return = array(
				'classname' => 'js-wpv-editor-instructions-for-media',
				'title' => __( 'Media', 'wpv-views' ),
				'content' => '<p>'
						. __( 'The <strong>Media</strong> button opens the WordPress media window, where you can upload media and insert media that you’ve already uploaded to the site.', 'wpv-views' )
						. '</p>',
				'table' => array(),
				'content_extra' => ''
			);
			break;
		case 'pagination':
			if ( isset( $_GET['page'] ) && $_GET['page'] == 'view-archives-editor' ) {
				$return = false;
				break;
			}
			$return = array(
				'classname' => 'js-wpv-editor-instructions-for-pagination',
				'title' => __( 'Pagination', 'wpv-views' ),
				'content' => '',
				'table' => array(
					array(
						'element' => '<span class="wpv-code wpv-code-shortcode">[wpv-pager-current-page]</span>',
						'description' => __( 'The current page number.', 'wpv-views' )
					),
					array(
						'element' => '<span class="wpv-code wpv-code-shortcode">[wpv-pager-num-page]</span>',
						'description' => __( 'The number of pages.', 'wpv-views' )
					),
					array(
						'element' => '<span class="wpv-code wpv-code-shortcode">[wpv-pager-current-page ...]</span>',
						'description' => __( 'Displays a pager with the current page selected.', 'wpv-views' )
							. WPV_MESSAGE_SPACE_CHAR
							. __( 'Depending on the value of the style parameter it displays a list of links to the other pages or a drop-down list to select another page.', 'wpv-views' )
					),
					array(
						'element' => '<span class="wpv-code wpv-code-shortcode">[wpv-pager-prev-page]</span><br />' . __( 'Previous', 'wpv-views' ) . '<br /><span class="wpv-code wpv-code-shortcode">[/wpv-pager-prev-page]</span>',
						'description' => __( 'A link to the previous page.', 'wpv-views' )
							. WPV_MESSAGE_SPACE_CHAR
							. __( 'You can edit the text between the shortcodes and use any HTML.', 'wpv-views' )
							. WPV_MESSAGE_SPACE_CHAR
							. __( 'The link will appear only if there is a previous page.', 'wpv-views' )
					),
					array(
						'element' => '<span class="wpv-code wpv-code-shortcode">[wpv-pager-next-page]</span><br />' . __( 'Next', 'wpv-views' ) . '<br /><span class="wpv-code wpv-code-shortcode">[/wpv-pager-next-page]</span>',
						'description' => __( 'A link to the next page.', 'wpv-views' )
							. WPV_MESSAGE_SPACE_CHAR
							. __( 'You can edit the text between the shortcodes and use any HTML.', 'wpv-views' )
							. WPV_MESSAGE_SPACE_CHAR
							. __( 'The link will appear only if there is a next page.', 'wpv-views' )
					)
				),
				'content_extra' => ''
			);
			break;
		case 'filter_section':
			$return = array(
				'classname' => 'js-wpv-editor-instructions-for-filter-section',
				'title' => __( 'The filter section', 'wpv-views' ),
				'content' => '',
				'table' => array(
					array(
						'element' => '<span class="wpv-code wpv-code-shortcode">[wpv-filter-start]</span><br />' . __( 'Content', 'wpv-views' ) . '<br /><span class="wpv-code wpv-code-shortcode">[wpv-filter-end]</span>',
						'description' => __( 'Wrapper for the entire filter section, including the pagination controls and parametric search.', 'wpv-views' )
							. WPV_MESSAGE_SPACE_CHAR
							. __( 'The filter section includes these shortcodes by default and you should not remove them.', 'wpv-views' )
					)
				),
				'content_extra' => ''
			);
			break;
		case 'parametric':
			$return = array(
				'classname' => 'js-wpv-editor-instructions-for-purpose js-wpv-display-for-purpose js-wpv-display-for-purpose-parametric js-wpv-display-for-purpose-full',
				'title' => __( 'Parametric search shortcodes', 'wpv-views' ),
				'content' => '',
				'table' => array(
					array(
						'element' => '<span class="wpv-code wpv-code-shortcode">[wpv-filter-controls]</span><br />' . __( 'Content', 'wpv-views' ) . '<br /><span class="wpv-code wpv-code-shortcode">[/wpv-filter-controls]</span>',
						'description' => __( 'Wrapper for the parametric search controls.', 'wpv-views' )
							. WPV_MESSAGE_SPACE_CHAR
							. __( 'All fields must appear between these shortcodes.', 'wpv-views' )
					),
					array(
						'element' => '<span class="wpv-code wpv-code-shortcode">[wpv-control]</span><br /><span class="wpv-code wpv-code-shortcode">[wpv-control-set]</span>',
						'description' => __( 'A filter element.', 'wpv-views' )
							. WPV_MESSAGE_SPACE_CHAR
							. __( 'You can insert filter elements by clicking on the <strong>New filter</strong> button.', 'wpv-views' )
							. WPV_MESSAGE_SPACE_CHAR
							. __( 'To edit an existing filter, place the cursor inside it and click on <strong>Edit filter</strong>.', 'wpv-views' )
					),
					array(
						'element' => '<span class="wpv-code wpv-code-shortcode">[wpv-filter-search-box ...]</span>',
						'description' => __( 'A content search input, which can search the title or title and content.', 'wpv-views' )
							. WPV_MESSAGE_SPACE_CHAR
							. __( 'To insert this shortcode and set its option, click on the <strong>Content search</strong> button.', 'wpv-views' )
					),
					array(
						'element' => '<span class="wpv-code wpv-code-shortcode">[wpv-filter-spinner ...]</span>',
						'description' => __( 'A spinner, which is displayed when the filter needs update and hidden when update is complete.', 'wpv-views' )
							. WPV_MESSAGE_SPACE_CHAR
							. __( 'To insert, click on the <strong>Spinner graphics</strong> button.', 'wpv-views' )
					),
					array(
						'element' => '<span class="wpv-code wpv-code-shortcode">[wpv-filter-reset ...]</span>',
						'description' => __( 'A button that resets the filter and allows visitors to start a fresh search.', 'wpv-views' )
							. WPV_MESSAGE_SPACE_CHAR
							. __( 'To insert click on the <strong>Clear form</strong> button.', 'wpv-views' )
					),
					array(
						'element' => '<span class="wpv-code wpv-code-shortcode">[wpv-filter-submit ...]</span>',
						'description' => __( 'A submit button for the search filter.', 'wpv-views' )
							. WPV_MESSAGE_SPACE_CHAR
							. __( 'To insert click on the <strong>Submit button</strong> button.', 'wpv-views' )
							. WPV_MESSAGE_SPACE_CHAR
							. __( 'A submit button is not available for forms that have automatic updates on field changes.', 'wpv-views' )
					)
				),
				'content_extra' => ''
			);
			break;
		case 'layout_section':
			$return = array(
				'classname' => 'js-wpv-editor-instructions-for-layout-section',
				'title' => __( 'Output loop', 'wpv-views' ),
				'content' => '',
				'table' => array(
					array(
						'element' => '<span class="wpv-code wpv-code-shortcode">[wpv-layout-start]<br />[wpv-layout-end]</span>',
						'description' => __( 'Wrapper for the Loop Output section.', 'wpv-views' )
							. WPV_MESSAGE_SPACE_CHAR
							. __( 'These shortcodes are added by default and you should keep them.', 'wpv-views' )
							. WPV_MESSAGE_SPACE_CHAR
							. __( 'Add all your elements between these shortcodes.', 'wpv-views' )
					),
					array(
						'element' => '<span class="wpv-code wpv-code-shortcode">[wpv-items-found]</span><br />' . __( 'Content', 'wpv-views' ) . '<br /><span class="wpv-code wpv-code-shortcode">[/wpv-items-found]</span>',
						'description' => __( 'Wrapper for what to display when the View query finds results.', 'wpv-views' )
					),
					array(
						'element' => '<span class="wpv-code wpv-code-shortcode">[wpv-no-items-found]</span><br />' . __( 'Content', 'wpv-views' ) . '<br /><span class="wpv-code wpv-code-shortcode">[/wpv-no-items-found]</span>',
						'description' => __( 'Wrapper for what to display when the View query doesn\'t find results.', 'wpv-views' )
					),
					array(
						'element' => '<span class="wpv-code wpv-code-html">&lt;wpv-loop&gt;</span><br />' . __( 'Content', 'wpv-views' ) . '<br /><span class="wpv-code wpv-code-html">&lt;/wpv-loop&gt;</span>',
						'description' => __( 'The View will loop over the content for every item loaded from the database.', 'wpv-views' )
					),
					array(
						'element' => '<span class="wpv-code wpv-code-shortcode">[wpv-item ...]</span>',
						'description' => __( 'Optional shortcode that separates between items in the loop.', 'wpv-views' )
						. WPV_MESSAGE_SPACE_CHAR
						. __( 'Only needed if you want to display different items in the results with different HTML.', 'wpv-views' )
					),
					array(
						'element' => '<span class="wpv-code wpv-code-shortcode">[wpv-if]</span><br />' . __( 'Content', 'wpv-views' ) . '<br /><span class="wpv-code wpv-code-shortcode">[/wpv-if]</span>',
						'description' => __( 'Conditional display for parts of the loop.', 'wpv-views' )
						. WPV_MESSAGE_SPACE_CHAR
						. __( 'You need to type-in the [wpv-if] shortcodes around the content that you want to display conditionally.', 'wpv-views' )
						. WPV_MESSAGE_SPACE_CHAR
						. sprintf(
							__( 'For documentation, see: %sConditional HTML output in Views%s.', 'wpv-views' ),
							'<a href="http://wp-types.com/documentation/user-guides/conditional-html-output-in-views/" title="' . __( 'Conditional output in Views', 'wpv-views' ) . '">',
							'</a>'
						)
					),
				),
				'content_extra' => ''
			);
			break;
		case 'combined_output_section':
			$return = array(
				'classname' => 'js-wpv-editor-instructions-for-combined-output-section',
				'title' => __( 'View sections', 'wpv-views' ),
				'content' => '',
				'table' => array(
					array(
						'element' => '<span class="wpv-code wpv-code-shortcode">[wpv-filter-meta-html]</span>',
						'description' => __( 'The entire Filter section.', 'wpv-views' )
					),
					array(
						'element' => '<span class="wpv-code wpv-code-shortcode">[wpv-layout-meta-html]</span>',
						'description' => __( 'The entire Loop Output section.', 'wpv-views' )
					)
				),
				'content_extra' => ''
			);
			break;
		case 'extra_css':
			$return = array(
				'classname' => 'js-wpv-editor-instructions-for-extra-css',
				'title' => __( 'Adding custom CSS to Views', 'wpv-views' ),
				'content' => '<p>'
						. __( 'You can add custom CSS styling to this box and it will be loaded when you display this View.', 'wpv-views' )
						. '</p><p>'
						. __( 'For example, if you would want to style the h3 tags featured in your view, you would add this CSS styling here:', 'wpv-views' )
						. '<code>'
						. 'h3 a { color: #2a6496; font-weight: normal }'
						. '</code>'
						. '</p><!--<p>'
						. sprintf(
							__( 'To learn more about adding custom CSS to your views, please review our %sonline tutorial%s', 'wpv-views' ),
							'<a href="#" title="' . __( ' Views tutorial on custom CSS', 'wpv-views' ) . '">',
							'</a>'
						)
						. '</p>-->',
				'table' => array(),
				'content_extra' => ''
			);
			break;
		case 'extra_js':
			$return = array(
				'classname' => 'js-wpv-editor-instructions-for-extra-js',
				'title' => __( 'Adding custom JavaScript to Views', 'wpv-views' ),
				'content' => '<p>'
						. __( 'You can add custom JavaScript to this box and it will be loaded when you display this View.', 'wpv-views' )
						. '</p><p>'
						. __( 'For example, if you would want to present an important message to your users through an alert pop-up, you would add this JavaScript here:', 'wpv-views' )
						. '<code>'
						. 'alert("Hello! I am an alert box!");'
						. '</code>'
						. '</p><!--<p>'
						. sprintf(
							__( 'To learn more about adding custom JavaScript to your views, please review our %sonline tutorial%s', 'wpv-views' ),
							'<a href="#" title="' . __( ' Views tutorial on custom CSS', 'wpv-views' ) . '">',
							'</a>'
						)
						. '</p>-->',
				'table' => array(),
				'content_extra' => ''
			);
			break;
		default:
			$return = false;
			$return = apply_filters( 'wpv_filter_formatting_instructions_section', $return, $section );
			break;
	}
	return $return;
}

function wpv_formatting_help_filter() {
	$sections = array( 'filter_section', 'pagination', 'parametric', 'fields-and-views', 'media', 'styling' );
	$sections = apply_filters( 'wpv_filter_formatting_help_filter', $sections );
	$args = array(
		'toggler_classname' => 'wpv-editor-instructions-toggle js-wpv-editor-instructions-toggle',
		'toggler_target' => 'js-wpv-editor-instructions-filter-html',
		'toggler_title' => __( 'Formatting and editing instructions', 'wpv-views' ),
		'toggled_classname' => 'wpv-editor-instructions js-wpv-editor-instructions js-wpv-editor-instructions-filter-html',
		'toggled_intro' => '<p>'
						. __( 'The filter section may include pagination for the results and a parametric search (or both of them together).', 'wpv-views' )
						. '</p><p>'
						. sprintf(__('To create a parametric search, position the cursor between the %s and %s shortcodes and click on the <strong>New filter</strong> button to insert filter elements.', 'wpv-views'),'<strong>[wpv-filter-controls]</strong>','<strong>[/wpv-filter-controls]</strong>')
						. WPV_MESSAGE_SPACE_CHAR
						. __('Your parametric search can contain any custom field or taxonomy that this View queries.', 'wpv-views')
						. '</p><p>'
						. __('Your parametric search can also include a <strong>content search input</strong>, <strong>spinner</strong> to indicate updates in progress, a button to <strong>clear the form input</strong> and a <strong>submit button</strong>.', 'wpv-views')
						. '</p><p>'
						. __('Use HTML and CSS to style the filter.', 'wpv-views')
						. '</p><p>'
						. __( 'If you are not using pagination or a parametric search, this section outputs nothing.', 'wpv-views' )
						. '</p>',
		'toggled_sections' => $sections
	);
	WPV_Admin_Messages::render_toggle_structure( $args );
}

function wpv_formatting_help_layout() {
	$sections = array( 'layout_section', 'pagination', 'fields-and-views', 'media', 'styling' );
	$sections = apply_filters( 'wpv_filter_formatting_help_layout', $sections );
	$cred_intro = '';
	if ( in_array( 'cred', $sections ) ) {
		$cred_intro = '<dt>' . __('CRED forms', 'wpv-views') . '</dt><dd>' . __('Add complete blocks into the View loop using Content Templates.', 'wpv-views') . WPV_MESSAGE_SPACE_CHAR
			. __('Allows to insert CRED forms. In Views, you will usually use this controls to insert edit and delete CRED links.', 'wpv-views') . '</dd>';
	}
	$args = array(
		'toggler_classname' => 'wpv-editor-instructions-toggle js-wpv-editor-instructions-toggle',
		'toggler_target' => 'js-wpv-editor-instructions-layout-html',
		'toggler_title' => __( 'Formatting and editing instructions', 'wpv-views' ),
		'toggled_classname' => 'wpv-editor-instructions js-wpv-editor-instructions js-wpv-editor-instructions-layout-html',
		'toggled_intro' =>		'<h4>' . __('Toolbar buttons','wpv-views') . '</h4>'
						. '<dl><dt>' . __( 'Loop wizard', 'wpv-views' ) . '</dt><dd>' . __( 'A wizard that lets you design the loop.', 'wpv-views' ) . WPV_MESSAGE_SPACE_CHAR
							. __('Recommended if you are new to Views.', 'wpv-views') . '</dd>'
						. '<dt>' . __('Fields and Views', 'wpv-views') . '</dt><dd>' . __('Add fields and nested Views to the output.', 'wpv-views') . WPV_MESSAGE_SPACE_CHAR
							. __('Good for building your own unique loops.', 'wpv-views') . '</dd>'
						. '<dt>' . __('Content Template', 'wpv-views') . '</dt><dd>' . __('Add complete blocks into the View loop using Content Templates.', 'wpv-views') . WPV_MESSAGE_SPACE_CHAR
							. __('This method makes it easy to achieve complex loop designs.', 'wpv-views') . '</dd>'
						. '<dt>' . __('Pagination controls', 'wpv-views') . '</dt><dd>' . __('Add complete blocks into the View loop using Content Templates.', 'wpv-views') . WPV_MESSAGE_SPACE_CHAR
							. __('Add controls to move between result-pages. This control will only appear when the View uses pagination.', 'wpv-views') . '</dd>'
						. $cred_intro
						. '<dt>' . __('Media', 'wpv-views') . '</dt><dd>' . __('Add images and other media items to the View.', 'wpv-views') . '</dd></dl>'
						. '<p>' . __('Besides these buttons, you can edit the HTML content yourself by writing your own HTML and CSS.', 'wpv-views')

						. '</p><p>'
						. sprintf(
								__( 'Full documentation is found in the %sViews shortcodes%s page.', 'wpv-views' ),
								'<a href="http://wp-types.com/documentation/views-shortcodes/" title="' . __( 'Views shortcodes documentation', 'wpv-views' ) . '">',
								'</a>'
							)
						. '</p>',
		'toggled_sections' => $sections
	);
	WPV_Admin_Messages::render_toggle_structure( $args );
}

function wpv_formatting_help_inline_content_template( $template = null ) {
	if (
		! is_object( $template ) 
		|| ! isset( $template->post_type ) 
		|| $template->post_type != 'view-template'
	) {
		return;
	}
	$sections = array( 'fields-and-views', 'media', 'styling' );
	$sections = apply_filters( 'wpv_filter_formatting_help_inline_content_template', $sections );
	$args = array(
		'toggler_classname' => 'wpv-editor-instructions-toggle js-wpv-editor-instructions-toggle',
		'toggler_target' => 'js-wpv-editor-instructions-inline-content-template-' . $template->ID,
		'toggler_title' => __( 'Formatting and editing instructions', 'wpv-views' ),
		'toggled_classname' => 'wpv-editor-instructions js-wpv-editor-instructions js-wpv-editor-instructions-inline-content-template-' . $template->ID,
		'toggled_intro' => '<p>'
						. __( 'A Content Template in a View works like a subroutine.', 'wpv-views' )
						. WPV_MESSAGE_SPACE_CHAR
						. __( 'It displays the fields of the post in the loop, by using a simple shortcode:', 'wpv-views' )
						. '<code>'
						. '[wpv-post-body view_template="' . $template->post_title . '"]'
						. '</code>'
						. '</p>',
		'toggled_sections' => $sections
	);
	WPV_Admin_Messages::render_toggle_structure( $args );
}

function wpv_formatting_help_layouts_content_template_cell( $template = null ) {
	if (
		! is_object( $template ) 
		|| ! isset( $template->post_type ) 
		|| $template->post_type != 'view-template'
	) {
		return;
	}
	$sections = array( 'fields-and-views', 'media', 'styling' );
	$sections = apply_filters( 'wpv_filter_formatting_help_layouts_content_template_cell', $sections );
	$args = array(
		'toggler_classname' => 'wpv-editor-instructions-toggle js-wpv-editor-instructions-toggle',
		'toggler_target' => 'js-wpv-editor-instructions-inline-content-template-' . $template->ID,
		'toggler_title' => __( 'Formatting and editing instructions', 'wpv-views' ),
		'toggled_classname' => 'wpv-editor-instructions js-wpv-editor-instructions js-wpv-editor-instructions-inline-content-template-' . $template->ID,
		'toggled_intro' => '<p>'
						. __( 'A Content Template is like a chunk of content.', 'wpv-views' )
						. WPV_MESSAGE_SPACE_CHAR
						. __( 'It displays fields from the current post using simple shortcodes.', 'wpv-views' )
						. WPV_MESSAGE_SPACE_CHAR
						. __( 'For example:', 'wpv-views' )
						. '<ul>'
						. '<li>'
						. '<code>[wpv-post-title]</code>'
						. WPV_MESSAGE_SPACE_CHAR
						. __( 'will display the post title', 'wpv-views' )
						. '</li>'
						. '<li>'
						. '<code>[wpv-post-body view_template="None"]</code>'
						. WPV_MESSAGE_SPACE_CHAR
						. __( 'will display the post content', 'wpv-views' )
						. '</li>'
						. '</ul>'
						. '</p>',
		'toggled_sections' => $sections
	);
	WPV_Admin_Messages::render_toggle_structure( $args );
}

function wpv_formatting_help_combined_output() {
	$sections = array( 'combined_output_section', 'fields-and-views', 'media', 'styling' );
	$sections = apply_filters( 'wpv_filter_formatting_help_combined_output', $sections );
	$args = array(
		'toggler_classname' => 'wpv-editor-instructions-toggle js-wpv-editor-instructions-toggle',
		'toggler_target' => 'js-wpv-editor-instructions-content',
		'toggler_title' => __( 'Formatting and editing instructions', 'wpv-views' ),
		'toggled_classname' => 'wpv-editor-instructions js-wpv-editor-instructions js-wpv-editor-instructions-content',
		'toggled_intro' => '<p>'
						. __( 'When you display the View, the content of this box gets displayed.', 'wpv-views' )
						. WPV_MESSAGE_SPACE_CHAR
						. __( 'Normally, it includes a shortcode for the <strong>Filter</strong> section and another shortcode for the <strong>Loop Output</strong> section, so they will display one after the other.', 'wpv-views' )
						. WPV_MESSAGE_SPACE_CHAR
						. __( 'You can add HTML for styling and switch between the View section.', 'wpv-views' )
						. '</p>',
		'toggled_sections' => $sections
	);
	WPV_Admin_Messages::render_toggle_structure( $args );
}

// @todo when moving the CT to their own editor we will need to review/delete this hook

add_action ( 'edit_form_after_editor', 'wpv_formatting_help_content_template', 5 );

function wpv_formatting_help_content_template( $template ) {
	if ( $template->post_type == 'view-template' ) {
		$sections = array( 'fields-and-views', 'media', 'styling' );
		$sections = apply_filters( 'wpv_filter_formatting_help_content_template', $sections );
		$args = array(
			'toggler_classname' => 'wpv-editor-instructions-toggle js-wpv-editor-instructions-toggle',
			'toggler_target' => 'js-wpv-editor-instructions-content-template',
			'toggler_title' => __( 'Formatting and editing instructions', 'wpv-views' ),
			'toggled_classname' => 'wpv-editor-instructions js-wpv-editor-instructions js-wpv-editor-instructions-content-template',
			'toggled_intro' => '<p>'
							. __( 'A Content Template is used to style single-item pages.', 'wpv-views' )
							. WPV_MESSAGE_SPACE_CHAR
							. __( 'It will replace the ‘main content’ section of the page.', 'wpv-views' )
							. WPV_MESSAGE_SPACE_CHAR
							. __( 'The Content Template can include fields that belong to the content, Views, CRED forms, HTML and media', 'wpv-views' )
							. '</p>',
			'toggled_sections' => $sections
		);
		WPV_Admin_Messages::render_toggle_structure( $args );
	}
}

function wpv_formatting_help_extra_css( $section = 'filter' ) {
	$sections = array( 'extra_css' );
	$sections = apply_filters( 'wpv_filter_formatting_help_extra_css', $sections );
	$args = array(
		'toggler_classname' => 'wpv-editor-instructions-toggle js-wpv-editor-instructions-toggle',
		'toggler_target' => 'js-wpv-editor-instructions-' . $section . '-css',
		'toggler_title' => __( 'Formatting and editing instructions', 'wpv-views' ),
		'toggled_classname' => 'wpv-editor-instructions js-wpv-editor-instructions js-wpv-editor-instructions-' . $section . '-css',
		'toggled_intro' => '<p>'
						. __( 'This editor allows you to add CSS code, which will determine the styling of the page.', 'wpv-views' )
						. '</p>',
		'toggled_sections' => $sections
	);
	WPV_Admin_Messages::render_toggle_structure( $args );
}

function wpv_formatting_help_extra_js( $section = 'filter' ) {
	$sections = array( 'extra_js' );
	$sections = apply_filters( 'wpv_filter_formatting_help_extra_js', $sections );
	$args = array(
		'toggler_classname' => 'wpv-editor-instructions-toggle js-wpv-editor-instructions-toggle',
		'toggler_target' => 'js-wpv-editor-instructions-' . $section . '-js',
		'toggler_title' => __( 'Formatting and editing instructions', 'wpv-views' ),
		'toggled_classname' => 'wpv-editor-instructions js-wpv-editor-instructions js-wpv-editor-instructions-' . $section . '-js',
		'toggled_intro' => '<p>'
						. __( 'This editor allows you to add javascript code, which will run in the browser, when this content is displayed.', 'wpv-views' )
						. '</p>',
		'toggled_sections' => $sections
	);
	WPV_Admin_Messages::render_toggle_structure( $args );
}

/**
 * wpv_views_editor_hidden_messages_boxes_pointers
 *
 * Outputs hidden containers with HTML structures to be used in Views and WPA edit screens, in dialogs, belp boxes or pointers, without AJAX need
 *
 * @param $view_settings
 * @param $view_layout_settings
 * @param $view_id
 * @param $user_id
 *
 * @since 1.7
 */
add_action( 'view-editor-section-hidden', 'wpv_views_editor_hidden_messages_boxes_pointers', 10, 4 );

function wpv_views_editor_hidden_messages_boxes_pointers( $view_settings, $view_layout_settings, $view_id, $user_id ) {
	$dismissed_pointers = get_user_meta( $user_id, '_wpv_dismissed_pointers', true );
	if ( ! is_array( $dismissed_pointers ) || empty( $dismissed_pointers ) ) {
		$dismissed_pointers = array();
	}

	$dismissed_dialogs = get_user_meta( $user_id, '_wpv_dismissed_dialogs', true );
	if ( ! is_array( $dismissed_dialogs ) || empty( $dismissed_dialogs ) ) {
		$dismissed_dialogs = array();
	}

	wp_nonce_field( 'wpv-editor-dismiss-message-box-pointer', 'wpv-editor-dismiss-message-box-pointer' );
	
	?>
	<div id="js-hidden-messages-boxes-pointers-container" class="popup-window-container">
		<?php
		// ------------------------------------------
		// Elements exclusively for Views
		// ------------------------------------------
		if ( 
			isset( $view_settings['view-query-mode'] ) 
			&& 'normal' ==  $view_settings['view-query-mode']
		) {
			// =======
			// Pointers
			// =======
			
			// Enabled pagination pointer
			$dismissed_classname = '';
			if ( isset( $dismissed_pointers['enabled-view-pagination'] ) ) {
				$dismissed_classname = ' js-wpv-pointer-dismissed';
			}
			?>
			<div class="js-wpv-enabled-view-pagination-pointer<?php echo $dismissed_classname; ?>">
				<h3><?php _e( 'Pagination enabled', 'wpv-views' ); ?></h3>
				<p>
					<?php
					_e( 'This View uses pagination, but pagination controls are still not inserted into the filter HTML section.', 'wpv-views' );
					?>
				</p>
				<p>
					<?php
					_e( 'To add them, just place your cursor between the [wpv-filter-start] and the [wpv-filter-end] shortcodes and click on the <strong>Pagination controls</strong> button.', 'wpv-views' );
					?>
				</p>
				<p>
					<label>
						<input type="checkbox" class="js-wpv-dismiss-pointer" data-pointer="enabled-view-pagination" id="wpv-dismiss-enabled-view-pagination-pointer" />
						<?php _e( 'Don\'t show this again', 'wpv-views' ); ?>
					</label>
				</p>
			</div>
			
			<?php
			// =======
			// Dialogs
			// =======
			
			// Insert pagination dialog
			?>
			<div class="wpv-dialog wpv-dialog-pagination-wizard js-wpv-pagination-form-dialog">
				<div class="wpv-dialog-header">
					<h2><?php _e('Would you like to insert transition controls for the pagination?','wpv-views') ?></h2>
					<i class="icon-remove js-dialog-close"></i>
				</div>
				<div class="wpv-dialog-sidebar filter-preview">
					<h3>
						<?php _e('Preview', 'wpv-views'); ?>
					</h3>
					<div class="js-pagination-preview pagination-preview">
						<p class="js-pagination-preview-element" data-name="current-page-number">
							<?php _e('Showing page','wpv-views'); ?>: 2
						</p>
						<p>
							<span class="js-pagination-preview-element" data-name="page-selector" data-type="page-selector-link page-selector-select"><?php _e('Choose page','wpv-views'); ?></span>
							<select class="js-disable-events js-pagination-preview-element" disabled data-name="page-selector" data-type="page-selector-select">
								<option>2</option>
							</select>
							<span data-name="page-selector" data-type="page-selector-link" class="js-pagination-preview-element hidden">
								<img src="<?php echo (WPV_URL . '/res/img/dots.png'); ?>" alt="dots" style="vertical-align: middle" />
							</span>
							<span class="js-pagination-preview-element" data-name="total-pages"><?php _e('of','wpv-views'); ?> 3</span>
						</p>
						<p class="js-pagination-preview-element next-previous-controls" data-name="next-previous-controls">
							<a href="#" class="js-disable-events">&laquo; <?php _e('Previous','wpv-views') ?></a>
							<a href="#" class="js-disable-events"><?php _e('Next','wpv-views') ?> &raquo;</a>
						</p>
					</div>
				</div>
				<div class="wpv-dialog-content">
					<h3><?php _e('Pagination controls', 'wpv-views'); ?></h3>
					<p>
						<input type="radio" name="pagination_control" class="js-wpv-pagination-dialog-control" id="pagination-include-page-num" value="page_num" data-target="current-page-number" />
						<label for="pagination-include-page-num"><?php _e('Current page number','wpv-views'); ?></label>
					</p>
					<p>
						<input type="radio" name="pagination_control" class="js-wpv-pagination-dialog-control" id="pagination-include-page-total" value="page_total" data-target="total-pages" />
						<label for="pagination-include-page-total"><?php _e('Number of pages','wpv-views'); ?></label>
					</p>
					<p>
						<input type="radio" name="pagination_control" class="js-wpv-pagination-dialog-control" id="pagination-include-page-selector" value="page_selector" data-target="page-selector"/>
						<label for="pagination-include-page-selector"><?php _e('Page selector using','wpv-views'); ?></label>
						<select name="pagination_controls_type" id="pagination-controls-type" class="js-pagination-control-type">
							<option value="drop_down"><?php _e('dropdown','wpv-views') ?></option>
							<option value="link"><?php _e('links','wpv-views') ?></option>
						</select>
					</p>
					<p>
						<input type="radio" name="pagination_control" class="js-wpv-pagination-dialog-control" id="pagination-include-controls" value="page_controls" data-target="next-previous-controls" />
						<label for="pagination-include-controls"><?php _e('Next and previous page controls','wpv-views'); ?></label>
					</p>

					<h3><?php _e('Pagination display', 'wpv-views'); ?></h3>
					<p>
						<input type="checkbox" name="pagination_display" class="js-wpv-pagination-dialog-display" id="pagination-include-wrapper" />
						<label for="pagination-include-wrapper"><?php _e('Don\'t show pagination controls if there is only one page','wpv-views'); ?></label>
					</p>
				</div>
				<div class="wpv-dialog-footer">
					<button class="button js-dialog-close"><?php _e('Cancel','wpv-views') ?></button>
					<button class="button button-primary js-wpv-insert-pagination"><?php _e('Insert pagination','wpv-views') ?></button>
				</div>
			</div>
			<?php
		}

		// ------------------------------------------
		// Elements exclusively for Archives
		// ------------------------------------------
		if ( 
			isset( $view_settings['view-query-mode'] )
			&& (
				'archive' ==  $view_settings['view-query-mode']
				|| 'layouts-loop' ==  $view_settings['view-query-mode']
			) )
		{
			// =======
			// Pointers
			// =======
			
			// =======
			// Dialogs
			// =======

			// Archive pagination controls dialog
			?>
			<div class="wpv-dialog js-wpv-archive-pagination-dialog">
				<div class="wpv-dialog-header">
					<h2><?php _e( 'Archive pagination controls', 'wpv-views' ) ?></h2>
					<i class="icon-remove js-dialog-close"></i>
				</div>
				<div class="wpv-dialog-content">

					<p><?php _e( 'Which pagination controls do you want to insert?', 'wpv-views' ); ?></p>
					<p>
						<input type="checkbox" checked="checked"
								name="archive_pagination_insert_prev" id="archive-pagination-insert-prev"
								class="js-wpv-archive-pagination-option" />
						<label for="archive-pagination-insert-prev"><?php _e( 'Link to previous page','wpv-views' ); ?></label>
					</p>
					<p>
						<input type="checkbox" checked="checked"
								name="archive_pagination_insert_next" id="archive-pagination-insert-next"
								class="js-wpv-archive-pagination-option" />
						<label for="archive-pagination-insert-next"><?php _e( 'Link to next page','wpv-views' ); ?></label>
					</p>
				</div>
				<div class="wpv-dialog-footer">
					<button class="button js-dialog-close"><?php _e( 'Cancel','wpv-views' ); ?></button>
					<button class="button button-primary js-wpv-insert-archive-pagination"><?php _e( 'Insert pagination','wpv-views' ); ?></button>
				</div>
			</div>
			<?php
		}
		
		// ------------------------------------------
		// Elements for both Views and Archives
		// ------------------------------------------

		// =======
		// Pointers
		// =======
		$dismissed_classname = '';
		if ( isset( $dismissed_pointers['inserted-layout-loop-content-template'] ) ) {
			$dismissed_classname = ' js-wpv-pointer-dismissed';
		}
		
		?>
			<div class="js-wpv-inserted-layout-loop-content-template-pointer<?php echo $dismissed_classname; ?>">
				<h3><?php _e( 'Content Template used as a View loop', 'wpv-views' ); ?></h3>
				<p>
					<?php
						_e( 'The Loop Wizard just updated the editor and created a Content Template.', 'wpv-views' );
						echo WPV_MESSAGE_SPACE_CHAR;
						_e( 'The HTML box includes the View loop and the Content Template contains the fields that you added.', 'wpv-views' );
						echo WPV_MESSAGE_SPACE_CHAR;
						_e( 'You can change the appearance by adding HTML and CSS.', 'wpv-views' );
					?>
				</p>
				<p>
					<label>
						<input type="checkbox" class="js-wpv-dismiss-pointer" data-pointer="inserted-layout-loop-content-template" id="wpv-dismiss-inserted-layout-loop-content-template-pointer" />
						<?php _e( 'Don\'t show this again', 'wpv-views' ); ?>
					</label>
				</p>
			</div>
		<?php
		$dismissed_classname = '';
		if ( isset( $dismissed_pointers['inserted-layout-loop'] ) ) {
			$dismissed_classname = ' js-wpv-pointer-dismissed';
		}
		
		?>
			<div class="js-wpv-inserted-layout-loop-pointer<?php echo $dismissed_classname; ?>">
				<h3><?php _e( 'Content Template used as a View loop', 'wpv-views' ); ?></h3>
				<p>
					<?php
						_e( 'The Loop Wizard just updated the editor with the fields that you added.', 'wpv-views' );
						echo WPV_MESSAGE_SPACE_CHAR;
						_e( 'You can change the appearance by adding HTML and CSS.', 'wpv-views' );
					?>
				</p>
				<p>
					<label>
						<input type="checkbox" class="js-wpv-dismiss-pointer" data-pointer="inserted-layout-loop" id="wpv-dismiss-inserted-layout-loop-pointer" />
						<?php _e( 'Don\'t show this again', 'wpv-views' ); ?>
					</label>
				</p>
			</div>
		<?php
		// =======
		// Dialogs
		// =======
		
		?>
	</div>
	<?php
}


// ========================================
// This should be moved to CRED
// @todo add this in CRED with a higher priority for the filters
// @todo remove this when CRED has been released with this included
// ========================================

/**
* wpv_add_cred_to_formatting_instructions
* 
* Registers the hooks to add the CRED information to the formatting instructions under CodeMirror textareas
*
* @since 1.7
*/

add_action( 'init', 'wpv_add_cred_to_formatting_instructions' );

function wpv_add_cred_to_formatting_instructions() {
	if ( class_exists( 'CRED_CRED' ) ) {
		// Register the section
		add_filter( 'wpv_filter_formatting_help_layout', 'wpv_register_cred_section' );// -2
		add_filter( 'wpv_filter_formatting_help_inline_content_template', 'wpv_register_cred_section' );// -2
		add_filter( 'wpv_filter_formatting_help_layouts_content_template_cell', 'wpv_register_cred_section' );// -2
		add_filter( 'wpv_filter_formatting_help_combined_output', 'wpv_register_cred_section' );// -2
		add_filter( 'wpv_filter_formatting_help_content_template', 'wpv_register_cred_section' );// -2
		// Register the section content
		add_filter( 'wpv_filter_formatting_instructions_section', 'wpv_cred_shortcodes_instructions', 10, 2 );
	}
}

function wpv_register_cred_section( $sections ) {
	if ( ! in_array( 'cred', $sections ) ) {
		array_splice( $sections, -2, 0, array( 'cred' ) );
	}
	return $sections;
}

function wpv_cred_shortcodes_instructions( $return, $section ) {
	if ( $section == 'cred' ) {
		$return = array(
			'classname' => 'js-wpv-editor-instructions-for-cred',
			'title' => __( 'CRED Forms', 'wpv-views' ),
			'content' => '<p>'
					. __( 'Click on the <strong>CRED Forms</strong> button to add forms.', 'wpv-views' )
					. WPV_MESSAGE_SPACE_CHAR
					. __( 'Normally, you will use \'edit forms\' and \'delete forms\' in Content Templates used in Views loops.', 'wpv-views' )
					. '</p>',
			'table' => array(),
			'content_extra' => ''
		);
	}
	return $return;
}
