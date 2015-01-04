<?php

// Hook into the template redirect and see if it's an archive loop
// Use the select page (that contains a View) to display the loop items.

add_action('template_redirect', 'wpv_archive_redirect');

function wpv_archive_redirect() {

    global $WPV_view_archive_loop, $WP_Views, $wp_query;
    $options = $WP_Views->get_options();
    $wpa_to_apply = 0;
    $wpa_slug = '';

    // See if we have a WPA for the home page
	if ( is_home() && isset( $options['view_home-blog-page'] ) && $options['view_home-blog-page'] > 0 ) {
		$wpa_to_apply = $options['view_home-blog-page'];
		$wpa_slug = 'view_home-blog-page';
    }
    // Check if it's a post type archive and if we have a WPA for it
    if ( is_post_type_archive() ) {
        $post_type_object = $wp_query->get_queried_object();
        if ( $post_type_object ) {
            if ( isset( $post_type_object->public ) && $post_type_object->public ) {
				// we need to check the public property because some plugin could change it on an earlier hook, leading to PHP notices
				$post_type = $post_type_object->name;
				if ( isset( $options['view_cpt_' . $post_type] ) && $options['view_cpt_' . $post_type] > 0 ) {
					$wpa_to_apply = $options['view_cpt_' . $post_type];
					$wpa_slug = 'view_cpt_' . $post_type;
				}
            }
        }
    }
    // Check taxonomy loops
    if ( is_archive() ) {
        if ( is_tax() || is_category() || is_tag() ) {
			$term = $wp_query->get_queried_object();
            if ( $term && isset( $options['view_taxonomy_loop_' . $term->taxonomy] ) && $options['view_taxonomy_loop_' . $term->taxonomy] > 0 ) {
                $wpa_to_apply = $options['view_taxonomy_loop_' . $term->taxonomy];
                $wpa_slug = 'view_taxonomy_loop_' . $term->taxonomy;
            }
        }
    }
	// Check other archives
    if ( is_search()  && isset( $options['view_search-page'] ) && $options['view_search-page'] > 0 ) {
        $wpa_to_apply = $options['view_search-page'];
        $wpa_slug = 'view_search-page';
    }
    if ( is_author() && isset( $options['view_author-page'] ) && $options['view_author-page'] > 0 ) {
        $wpa_to_apply = $options['view_author-page'];
        $wpa_slug = 'view_author-page';
    }
	if ( is_year() && isset( $options['view_year-page'] ) && $options['view_year-page'] > 0 ) {
		$wpa_to_apply = $options['view_year-page'];
		$wpa_slug = 'view_year-page';
    }
	if ( is_month() && isset( $options['view_month-page'] ) && $options['view_month-page'] > 0 ) {
		$wpa_to_apply = $options['view_month-page'];
		$wpa_slug = 'view_month-page';
    }
	if ( is_day() && isset( $options['view_day-page'] ) && $options['view_day-page'] > 0 ) {
		$wpa_to_apply = $options['view_day-page'];
		$wpa_slug = 'view_day-page';
    }
    
    $wpa_to_apply = wpv_force_wordpress_archive( $wpa_to_apply, $wpa_slug );
    
    // If there is a WPA to apply, apply it
    if ( $wpa_to_apply > 0 ) {
		$WPV_view_archive_loop->initialize_archive_loop( $wpa_to_apply );
    }

}

/**
* wpv_force_wordpress_archive
*
* Applies the wpv_filter_force_wordpress_archive filter to the WPA ID to be displayed
*
* @param $wpa_to_apply (integer) the ID of the WPA we want to overwrite
* @param $wpa_slug (string) [view_cpt_{post_slug}|view_taxonomy_loop_{taxonomy_slug}|view_home-blog-page|view_search-page|view_author-page|view_year-page|view_month-page|view_day-page] the kind of WPA being processed
*
* @return (int) the ID of the WPA to apply_filters
*
* @since 1.6.0
*/

function wpv_force_wordpress_archive( $wpa_to_apply, $wpa_slug ) {
	
	/**
	* Filter wpv_filter_force_wordpress_archive
	*
	* @param $wpa_to_apply (integer) the ID of the WPA we want to overwrite
	* @param $wpa_slug (string) [view_cpt_{post_slug}|view_taxonomy_loop_{taxonomy_slug}|view_home-blog-page|view_search-page|view_author-page|view_year-page|view_month-page|view_day-page] the kind of WPA being processed
	*
	* @return (int) the ID of the WPA to apply
	*
	* @since 1.6.0
	*/

	$wpa_to_apply = apply_filters( 'wpv_filter_force_wordpress_archive', $wpa_to_apply, $wpa_slug );
	return $wpa_to_apply;
}

/**
* wpv_has_wordpress_archive
*
* Checks if a given archive page has a WPA assigned to it
*
* @param $kind (string) [post|taxonomy|other] the kind of archive to be checked
* @param $slug (string) the slug of the archive to be checked:
*    - if $kind is "post" then the slug of the post type
*    - if $kind is "taxonomy" then the slug of the taxonomy
*    - if $kind is "other" it can be [home-blog|search|author|year|month|day]
*
* @return (int) the ID of the assigned WPA or 0 if there is no one
*
* @since 1.6.0
*/

function wpv_has_wordpress_archive( $kind, $slug ) {
	global $WP_Views;
	$return = 0;
	$identifier = '';
	$options = $WP_Views->get_options();
	switch ( $kind ) {
		case 'post':
			$identifier = 'view_cpt_' . $slug;
			break;
		case 'taxonomy':
			$identifier = 'view_taxonomy_loop_' . $slug;
			break;
		case 'other':
			$identifier = 'view_' . $slug . '-page';
			break;
	}
	if ( !empty( $identifier ) && isset( $options[$identifier] ) ) {
		$return = $options[$identifier];
	}
	return $return;
}



class WP_Views_archive_loops{

    function __construct(){
        add_action('init', array($this, 'init'));

		$this->header_started = false;
		$this->in_head = false;

		$this->in_the_loop = false;
        $this->loop_found = false;

		$this->loop_has_no_posts = false;
    }


    function __destruct(){

    }

    function init(){
        if(is_admin()){
            add_action('admin_print_scripts', array($this,'add_js'));

            add_action('wp_ajax_wpv_get_archive_post_type_summary', array($this, '_ajax_get_post_type_loop_summary'));
            add_action('wp_ajax_wpv_get_archive_taxonomy_summary', array($this, '_ajax_get_taxonomy_loop_summary'));
            add_action('wp_ajax_wpv_get_archive_view_edit_summary', array($this, '_ajax_get_view_edit_summary'));

        }

    }

    /** function: initialize_archive_loop
     *
     * This will redirect to display the given post_id
     * The post will be displayed using the theme template selected for it
     * When a View is rendered it will use the posts from the current query
     *
     */

    function initialize_archive_loop($post_id) {
        global $post, $wp_query;

        if (!have_posts()) {

			// We need to handle empty loops and force the loop processing
			// Create a dummy entry and set the post count to 1 so that
			// we recieve the loop_start and loop_end hooks

			$wp_query->post_count = 1;
			$wp_query->posts = array(0);
			$this->loop_has_no_posts = true;
		}


        if (have_posts()) {

            $output_post = get_post($post_id);

            if ($output_post) {

                // Save the original query.

                $this->query = clone $wp_query;
                $this->view_id = $post_id;
                add_action('loop_start', array($this, 'loop_start'), 1, 1);
                add_action('loop_end', array($this, 'loop_end'), 999, 1);
				add_action('get_header', array($this, 'get_header'));

				// Stop the view being displayed in the head.
				// JetPack can cause this.
				add_action( 'wp_head', array( $this, 'html_head_start' ), -100 ); // try to load first
				add_action( 'wp_head', array( $this, 'html_head_end' ), 999 ); // try to load last

            }
        }
    }

    function get_archive_loop_query() {
        if ($this->in_the_loop) {
            return $this->query;
        } else {
            return null;
        }
    }

	function get_header($name) {
		$this->header_started = true;
	}

	function html_head_start() {
		$this->in_head = true;
	}

	function html_head_end() {
		$this->in_head = false;
	}

    function loop_start($query) {
        if (!$this->in_head && $this->header_started && ($query->query_vars_hash == $this->query->query_vars_hash || $query->request == $this->query->request)) {
            ob_start();
            $this->post_count = $query->post_count;
            $query->post_count = 1;
            $this->loop_found = true;
        }
    }

    function loop_end($query) {
        if ($this->loop_found) {
            ob_end_clean();

			if ($this->loop_has_no_posts) {
				// Reset everything if the loop has no posts.
				// Then the View will render with no posts.

				global $post, $wp_query;

				$this->post_count = 0;
				$this->query->post_count = 0;
				$wp_query->post_count = 0;

				$wp_query->posts = array();
				$this->query->posts = array();

				$post = null;
			}

            $query->post_count = $this->post_count;

            $this->in_the_loop = true;
            echo render_view(array('id' => $this->view_id));
            $this->in_the_loop = false;

            $this->loop_found = false;
        }

    }

    // Add setting to the Views Settings page to select which page to use
    // to display our archive loop.

    function admin_settings($options) {

        global $WP_Views;

        // Display controls for Post Type archives.

        $loops = $this->_get_post_type_loops();

		$WP_Views->admin_section_start(__('Views for Post Type archive loops', 'wpv-views'),
									   'http://wp-types.com/documentation/user-guides/normal-vs-archive-views/',
									   __('All about archive Views', 'wpv-views'));

		$this->_display_post_type_loop_summary($loops, $options);
		$this->_display_post_type_loop_admin($loops, $options);

		$WP_Views->admin_section_end();


		// Display controls for the Taxonomy archive loops

		$WP_Views->admin_section_start(__('Views for Taxonomy archive loops', 'wpv-views'));

		$this->_display_taxonomy_loop_summary($options);
		$this->_display_taxonomy_loop_admin($options);

		$WP_Views->admin_section_end();
    }

    function _get_post_type_loops() {
        $loops = array('home-blog-page' => __('Home/Blog', 'wpv-views'),
                       'search-page' => __('Search results', 'wpv-views'),
                       'author-page' => __('Author archives', 'wpv-views'),
                       'year-page' => __('Year archives', 'wpv-views'),
                       'month-page' => __('Month archives', 'wpv-views'),
                       'day-page' => __('Day archives', 'wpv-views'));

        // Only offer loops for post types that already have an archive
        $post_types = get_post_types(array('public'=>true, 'has_archive' => true), 'objects');
        foreach($post_types as $post_type) {
			if (!in_array($post_type->name, array('post', 'page', 'attachment'))) {
				$type = 'cpt_' . $post_type->name;
				$name = $post_type->labels->name;
				$loops[$type] = $name;
			}
        }

        return $loops;
    }

    function _ajax_get_post_type_loop_summary() {
        global $WP_Views;

		if (wp_verify_nonce($_POST['wpv_post_type_loop_nonce'], 'wpv_post_type_loop_nonce')) {

            $loops = $this->_get_post_type_loops();

            $options = $WP_Views->get_options();
            $options = $this->submit($options);

            $WP_Views->save_options($options);

            $this->_display_post_type_loop_summary($loops, $options);
        }
        die();
    }

    function _display_post_type_loop_summary($loops, $options) {
        global $WP_Views;
        $views_available = $WP_Views->get_view_titles();

        $selected = '';

        foreach($loops as $loop => $loop_name) {
            if (isset ($options['view_' . $loop]) && $options['view_' . $loop] > 0) {
				$view_id = $options['view_' . $loop];
				if (function_exists('icl_object_id')) {
					$view_id = icl_object_id($view_id, 'view', true);
				}

                $selected .= '<li type=square style="margin:0;">' . sprintf(__('%s using "%s"', 'wpv-views'), $loop_name, $views_available[$view_id]) . '</li>';
            }
        }

        if ($selected == '') {
            $selected = __('There are no Views being used for Post Type archive loops.', 'wpv-views') . '<br />';
        } else {
            $selected = '<ul style="margin-left:20px">' . $selected . '</ul>';
        }

        echo '<div id="wpv-post-type-loop-summary" style="margin-left:20px;">';
        echo $selected;
        ?>
        <input class="button-secondary" type="button" value="<?php echo __('Edit', 'wpv-views'); ?>" name="post_type_loop_edit" onclick="wpv_archive_post_type_loop_edit();"/>
        </div>

        <?php

    }

    function _display_post_type_loop_admin($loops, $options) {

        global $WP_Views;

        $add_new_view_url = admin_url('post-new.php?post_type=view');
        ?>

        <div id="wpv-post-type-loop-edit" style="margin-left:20px;display:none">

    		<?php wp_nonce_field('wpv_post_type_loop_nonce', 'wpv_post_type_loop_nonce'); ?>

            <table class="widefat" style="width:auto;">
                <thead>
                    <tr>
                        <th><?php _e('Loop'); ?></th>
                        <th><?php _e('Use this View', 'wpv-views'); ?></th>
                    </tr>
                </thead>

                <tbody>

                    <?php
                        foreach($loops as $loop => $loop_name) {
                            echo '<tr><td>' . $loop_name . '</td>';

                            $selected_view = 0;
                            if (isset ($options['view_' . $loop])) {
                                $selected_view = $options['view_' . $loop];
                            }

                            $select = $WP_Views->get_view_select_box($loop, $selected_view, true);

                            echo '<td>' . $select . '&nbsp;&nbsp;<a href="' . $add_new_view_url . '&view_archive=' . $loop. '">' . __('Create a new View for this listing page', 'wpv-views'). '</td>';

                            echo '</tr>';
                        }

                    ?>


                </tbody>
            </table>

        <input class="button-primary" type="button" value="<?php echo __('Save', 'wpv-views'); ?>" name="post_type_loop_save" onclick="wpv_archive_post_type_loop_save();"/>
        <img id="wpv_save_post_type_loop_spinner" src="<?php echo WPV_URL; ?>/res/img/ajax-loader.gif" width="16" height="16" style="display:none" alt="loading" />

        <input class="button-secondary" type="button" value="<?php echo __('Cancel', 'wpv-views'); ?>" name="post_type_loop_cancel" onclick="wpv_archive_post_type_loop_cancel();"/>

        </div>

        <?php
    }

    function _ajax_get_taxonomy_loop_summary() {
        global $WP_Views;

		if (wp_verify_nonce($_POST['wpv_taxonomy_loop_nonce'], 'wpv_taxonomy_loop_nonce')) {
            $options = $WP_Views->get_options();
            $options = $this->submit($options);

            $WP_Views->save_options($options);

            $this->_display_taxonomy_loop_summary($options);
        }
        die();


    }

    function _display_taxonomy_loop_summary($options) {

        global $WP_Views;
        $views_available = $WP_Views->get_view_titles();

        $selected = '';

        $taxonomies = get_taxonomies('', 'objects');
        $exclude_tax_slugs = array();
	$exclude_tax_slugs = apply_filters( 'wpv_admin_exclude_tax_slugs', $exclude_tax_slugs );
        foreach ($taxonomies as $category_slug => $category) {
		if ( in_array( $category_slug, $exclude_tax_slugs ) ) {
			continue;
		}
		if ( !$category->show_ui ) {
			continue; // Only show taxonomies with show_ui set to TRUE
		}
            $name = $category->name;
            if (isset ($options['view_taxonomy_loop_' . $name ]) && $options['view_taxonomy_loop_' . $name ] > 0) {
				$view_id = $options['view_taxonomy_loop_' . $name];
				if (function_exists('icl_object_id')) {
					$view_id = icl_object_id($view_id, 'view', true);
				}

                $selected .= '<li type=square style="margin:0;">' . sprintf(__('%s using "%s"', 'wpv-views'), $category->labels->name, $views_available[$view_id]) . '</li>';
            }
        }

        if ($selected == '') {
            $selected = __('There are no Views being used for Taxonomy archive loops.', 'wpv-views') . '<br />';
        } else {
            $selected = '<ul style="margin-left:20px">' . $selected . '</ul>';
        }

        echo '<div id="wpv-taxonomy-loop-summary" style="margin-left:20px;">';
        echo $selected;
        ?>
        <input class="button-secondary" type="button" value="<?php echo __('Edit', 'wpv-views'); ?>" name="wpv-taxonomy-loop-edit" onclick="wpv_archive_taxonomy_loop_edit();"/>
        </div>

        <?php
    }

    function _display_taxonomy_loop_admin($options) {

        global $WP_Views;

        $add_new_view_url = admin_url('post-new.php?post_type=view');

        ?>
        <div id="wpv-taxonomy-loop-edit" style="margin-left:20px;display:none">

            <?php wp_nonce_field('wpv_taxonomy_loop_nonce', 'wpv_taxonomy_loop_nonce'); ?>

            <table class="widefat" style="width:auto;">
                <thead>
                    <tr>
                        <th><?php _e('Loop'); ?></th>
                        <th><?php _e('Use this View', 'wpv-views'); ?></th>
                    </tr>
                </thead>

                <tbody>

                    <?php

                        $taxonomies = get_taxonomies('', 'objects');
                        $exclude_tax_slugs = array();
			$exclude_tax_slugs = apply_filters( 'wpv_admin_exclude_tax_slugs', $exclude_tax_slugs );
                        foreach ($taxonomies as $category_slug => $category) {
				if ( in_array( $category_slug, $exclude_tax_slugs ) ) {
					continue;
				}
				if ( !$category->show_ui ) {
					continue; // Only show taxonomies with show_ui set to TRUE
				}
                            $name = $category->name;
                            ?>
                            <tr>
                                <td><?php echo $category->labels->name; ?></td>
                                <td>
                                    <?php
                                        if (!isset($options['view_taxonomy_loop_' . $name ])) {
                                            $options['view_taxonomy_loop_' . $name ] = '0';
                                        }
                                        $template = $WP_Views->get_view_select_box('taxonomy_loop_'. $name, $options['view_taxonomy_loop_' . $name ], true);

                                        echo $template . '&nbsp;&nbsp;<a href="' . $add_new_view_url . '&view_archive_taxonomy=' . $name. '">' . __('Create a new View for this listing page', 'wpv-views');

                                    ?>
                                </td>
                            </tr>
                            <?php
                        }


                    ?>
                </tbody>
            </table>


        <input class="button-primary" type="button" value="<?php echo __('Save', 'wpv-views'); ?>" name="post_type_loop_save" onclick="wpv_archive_taxonomy_loop_save();"/>
        <img id="wpv_save_taxonomy_loop_spinner" src="<?php echo WPV_URL; ?>/res/img/ajax-loader.gif" width="16" height="16" style="display:none" alt="loading" />

        <input class="button-secondary" type="button" value="<?php echo __('Cancel', 'wpv-views'); ?>" name="post_type_loop_cancel" onclick="wpv_archive_taxonomy_loop_cancel();"/>
        </div>

        <?php

    }


    // Save the view settings for the archive loops.
    function submit($options) {

        foreach($_POST as $index => $value) {
            if (strpos($index, 'view_') === 0) {
                $options[$index] = $value;
            }

            if (strpos($index, 'view_taxonomy_loop_') === 0) {
                $options[$index] = $value;
            }
        }

        return $options;
    }

    function add_js() {
        global $pagenow, $post;

        if(($pagenow == 'post.php' && $post->post_type == 'view') ||
                ($pagenow == 'post-new.php' && isset($_GET['post_type']) && $_GET['post_type'] == 'view') ||
                ($pagenow == 'edit.php' && isset($_GET['page']) && $_GET['page'] == 'views-settings')){

            wp_enqueue_script( 'views-archive-loop-script' , WPV_URL . '/res/js/views_archive_loop.js', array('jquery'), WPV_VERSION);
        }
    }

    function view_edit_admin($view_id, $view_settings) {
        global $WP_Views;
        $options = $WP_Views->get_options();
        ?>

        <div id="wpv-archive-view-mode"<?php if($view_settings['view-query-mode'] != 'archive') {echo ' style="display:none;"';} ?>>
            <?php $this->_display_view_edit_summary($view_id, $options); ?>
            <?php $this->_display_view_edit_edit($view_id); ?>
        </div>

        <?php
    }

    function _ajax_get_view_edit_summary() {

		if (wp_verify_nonce($_POST['wpv_view_edit_nonce'], 'wpv_view_edit_nonce')) {
            $options = array();
            $view_id = $_POST['wpv-archive-view-id'];

            foreach($_POST as $key => $value) {
                if (strpos($key, 'wpv-view-loop-') === 0) {
                    $options['view_' . substr($key, 14)] = $view_id;
                }

                if (strpos($key, 'wpv-view-taxonomy-loop-') === 0) {
                    $options['view_taxonomy_loop_' . substr($key, 23)] = $view_id;
                }
            }

            $this->_display_view_edit_summary($view_id, $options);
        }
        die();
    }

    function _display_view_edit_summary($view_id, $options) {
        global $WP_Views;
        $loops = $this->_get_post_type_loops();

        $options = $this->_view_edit_options($view_id, $options);

        ?>
        <div id="wpv-archive-view-mode-summary" style="margin-left:20px;">
            <?php

                $selected = '';
                foreach($loops as $loop => $loop_name) {
                    if (isset ($options['view_' . $loop]) && $options['view_' . $loop] == $view_id) {
                        if ($selected != '') {
                            $selected .= ', ';
                        }
                        $selected .= sprintf(__('post type <strong>%s</strong>', 'wpv-views'), $loop_name);
                    }
                }
                $taxonomies = get_taxonomies('', 'objects');
                $exclude_tax_slugs = array();
		$exclude_tax_slugs = apply_filters( 'wpv_admin_exclude_tax_slugs', $exclude_tax_slugs );
                foreach ($taxonomies as $category_slug => $category) {
			if ( in_array( $category_slug, $exclude_tax_slugs ) ) {
				continue;
			}
			if ( !$category->show_ui ) {
				continue; // Only show taxonomies with show_ui set to TRUE
			}
                    $name = $category->name;
                    if (isset ($options['view_taxonomy_loop_' . $name ]) && $options['view_taxonomy_loop_' . $name ] == $view_id) {
                        if ($selected != '') {
                            $selected .= ', ';
                        }
                        $selected .= sprintf(__('taxonomy <strong>%s</strong>', 'wpv-views'), $category->labels->name);
                    }
                }

                if ($selected == '') {
                    $selected = __("This View isn't being used for any archive loops.", 'wpv-views');
                } else {
                    $selected = sprintf(__('This View is being used for these archive loops: %s', 'wpv-views'), $selected);
                }
                echo $selected;

            ?>
            <br />
            <input class="button-secondary" type="button" value="<?php echo __('Edit', 'wpv-views'); ?>" name="wpv-archive-view-edit" onclick="wpv_archive_view_edit();"/>
        </div>
        <?php
    }

    function _view_edit_options($view_id, $options) {
        static $js_added = false;

        $title = '';
        if (isset($_GET['view_archive'])) {
            $options['view_' . $_GET['view_archive']] = $view_id;
            $loops = $this->_get_post_type_loops();
            $title = sprintf('%s-archive', $loops[$_GET['view_archive']]);
        }

        if (isset($_GET['view_archive_taxonomy'])) {
            $options['view_taxonomy_loop_' . $_GET['view_archive_taxonomy']] = $view_id;
            $taxonomies = get_taxonomies('', 'objects');
            $title = sprintf('%s-taxonomy-archive', $taxonomies[$_GET['view_archive_taxonomy']]->labels->name);
        }

        if ($title != '' && !$js_added) {
            // add some js to set the post title.

            ?>
            <script type="text/javascript">
                jQuery(document).ready(function($){
                    jQuery('#title').val('<?php echo esc_js($title); ?>');
                });
            </script>
            <?php
            $js_added = true;
        }

        return $options;
    }

    function _display_view_edit_edit($view_id) {
        global $WP_Views;
        $options = $WP_Views->get_options();
        $loops = $this->_get_post_type_loops();

        $options = $this->_view_edit_options($view_id, $options);

        ?>
        <div id="wpv-archive-view-mode-edit" style="background:<?php echo WPV_EDIT_BACKGROUND;?>;margin-left:20px;display:none;">

            <?php wp_nonce_field('wpv_view_edit_nonce', 'wpv_view_edit_nonce'); ?>

            <div>
                <input type="hidden" value="<?php echo $view_id; ?>" name="wpv-archive-view-id" />
                <p><?php _e('Use this View for these archive loops:', 'wpv-views');?></p>
                <table class="widefat" style="width:auto;margin-top:10px;">
                    <thead>
                        <tr>
                            <th><?php _e('Post type loopssss', 'wpv-views'); echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'; ?></th>
                            <th><?php _e('Taxonomy loops', 'wpv-views'); ?></th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <td>
                                <ul>
                                    <?php
                                        foreach($loops as $loop => $loop_name) {
                                            $checked = (isset ($options['view_' . $loop]) && $options['view_' . $loop] == $view_id) ? ' checked="checked"' : '';
                                            echo '<li>';
                                            echo '<label><input type="checkbox"' . $checked . ' name="wpv-view-loop-' . $loop . '" />' . $loop_name . '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</label>';
                                            echo '</li>';
                                        }

                                    ?>
                                </ul>
                            </td>
                            <td>
                                <ul>
                                    <?php
                                        $taxonomies = get_taxonomies('', 'objects');
                                        $exclude_tax_slugs = array();
					$exclude_tax_slugs = apply_filters( 'wpv_admin_exclude_tax_slugs', $exclude_tax_slugs );
                                        foreach ($taxonomies as $category_slug => $category) {
						if ( in_array($category_slug, $exclude_tax_slugs ) ) {
							continue;
						}
						if ( !$category->show_ui ) {
							continue; // Only show taxonomies with show_ui set to TRUE
						}
                                            $name = $category->name;
                                            $checked = (isset ($options['view_taxonomy_loop_' . $name ]) && $options['view_taxonomy_loop_' . $name ] == $view_id) ? ' checked="checked"' : '';
                                            echo '<li>';
                                            echo '<label><input type="checkbox"' . $checked . ' name="wpv-view-taxonomy-loop-' . $name . '" />' . $category->labels->name . '</label>';
                                            echo '</li>';
                                        }
                                    ?>
                                </ul>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <br />
                <input class="button-primary" type="button" value="<?php echo __('OK', 'wpv-views'); ?>" name="wpv-archive-view-ok" onclick="wpv_archive_view_ok();"/>
                <img id="wpv_archive_view_loop_spinner" src="<?php echo WPV_URL; ?>/res/img/ajax-loader.gif" width="16" height="16" style="display:none" alt="loading" />

                <input class="button-secondary" type="button" value="<?php echo __('Cancel', 'wpv-views'); ?>" name="wpv-archive-view-cancel" onclick="wpv_archive_view_cancel();"/>
                <br />
            </div>
        </div>
        <?php
    }

    function _create_view_archive_popup( $view_id = 0 ) {
        global $WP_Views;
        $options = $WP_Views->get_options();
        $loops = $this->_get_post_type_loops();
        $options = $this->_view_edit_options($view_id, $options);// TODO check if we just need the $options above
        $asterisk = ' <span style="color:red">*</span>';
		$asterisk_explanation = __( '<span style="color:red">*</span> A different WordPress Archive is already assigned to this item', 'wpv-views' );
        ?>
        <div class="wpv-dialog wpv-dialog-change js-wpv-dialog-change">
                <div class="wpv-dialog-header">
                    <h2><?php if ( $view_id == 0 ) _e( 'Add new WordPress Archive', 'wpv-views' ); else _e( 'What loop will this Archive be used for?','wpv-views' ); ?></h2>
                    <i class="icon-remove js-dialog-close"></i>
                </div>
                <div class="wpv-dialog-content">
		<form id="wpv-create-archive-view-form">
                    <?php wp_nonce_field('wpv_view_edit_nonce', 'wpv_view_edit_nonce'); ?>
                    <input type="hidden" value="<?php echo $view_id; ?>" name="wpv-archive-view-id" />
                    <?php if ( $view_id == 0 ): ?>
                    <p>
                        <strong><?php _e('What loop will this Archive be used for?','wpv-views') ?></strong>
                    </p>
                    <?php endif; ?>
                    <?php
                        $show_asterisk_explanation = false;
                        $loops = array('home-blog-page' => __('Home/Blog', 'wpv-views'),
						'search-page' => __('Search results', 'wpv-views'),
						'author-page' => __('Author archives', 'wpv-views'),
						'year-page' => __('Year archives', 'wpv-views'),
						'month-page' => __('Month archives', 'wpv-views'),
						'day-page' => __('Day archives', 'wpv-views'));
					?>
					
					<h3><?php _e('WordPress Native Archives', 'wpv-views'); ?></h3>
                    <ul>
                        <?php foreach($loops as $loop => $loop_name): ?>
                            <?php
                            $show_asterisk = false;
                            $checked = ( isset($options['view_' . $loop] ) && $options['view_' . $loop] == $view_id ) ? ' checked="checked"' : '';
                            if ( isset( $options['view_' . $loop] ) && $options['view_' . $loop] != $view_id ) {
								$show_asterisk = true;
								$show_asterisk_explanation = true;
                            }
                            ?>
                            <li>
								<input type="checkbox" <?php echo $checked; ?> id="wpv-view-loop-<?php echo $loop; ?>" name="wpv-view-loop-<?php echo $loop; ?>" />
								<label for="wpv-view-loop-<?php echo $loop; ?>"><?php echo $loop_name; echo $show_asterisk ? $asterisk : '';  ?></label>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php if ( $show_asterisk_explanation ) { ?>
                    <span class="wpv-asterisk-explanation">
						<?php echo $asterisk_explanation; ?>
                    </span>
                    <?php } ?>
                    <?php
						$pt_loops = array();
						$show_asterisk_explanation = false;
						// Only offer loops for post types that already have an archive
						$post_types = get_post_types( array( 'public' => true, 'has_archive' => true), 'objects' );
						foreach ( $post_types as $post_type ) {
							if ( !in_array( $post_type->name, array( 'post', 'page', 'attachment' ) ) ) {
								$type = 'cpt_' . $post_type->name;
								$name = $post_type->labels->name;
								$pt_loops[$type] = $name;
							}
						}
                    ?>

                    <?php if ( !empty( $pt_loops ) ) : ?>
                    <h3><?php _e( 'Post Type Archives', 'wpv-views' ); ?></h3>
                    <ul>
                        <?php foreach ( $pt_loops as $loop => $loop_name ): ?>
                            <?php
                            $show_asterisk = false;
                            $checked = ( isset($options['view_' . $loop] ) && $options['view_' . $loop] == $view_id ) ? ' checked="checked"' : '';
                            if ( isset( $options['view_' . $loop] ) && $options['view_' . $loop] != $view_id ) {
								$show_asterisk = true;
								$show_asterisk_explanation = true;
                            }
                            ?>
                            <li>
								<input type="checkbox" <?php echo $checked; ?> id="wpv-view-loop-<?php echo $loop; ?>" name="wpv-view-loop-<?php echo $loop; ?>" />
								<label for="wpv-view-loop-<?php echo $loop; ?>"><?php echo $loop_name; echo $show_asterisk ? $asterisk : ''; ?></label>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php if ( $show_asterisk_explanation ) { ?>
                    <span class="wpv-asterisk-explanation">
						<?php echo $asterisk_explanation; ?>
                    </span>
                    <?php } ?>
                    <?php endif; ?>

                    <?php
                    $show_asterisk_explanation = false;
                    $taxonomies = get_taxonomies('', 'objects');
                    $exclude_tax_slugs = array();
					$exclude_tax_slugs = apply_filters( 'wpv_admin_exclude_tax_slugs', $exclude_tax_slugs );
                        foreach ($taxonomies as $category_slug => $category):
							if ( in_array($category_slug, $exclude_tax_slugs ) ) {
								unset($taxonomies[$category_slug]);
								continue;
							}
						if ( !$category->show_ui ) {
							unset($taxonomies[$category_slug]);
							continue; // Only show taxonomies with show_ui set to TRUE
						}
                        endforeach;
                    ?>

                    <?php if ( !empty( $taxonomies ) ): ?>
                    <h3><?php _e('Taxonomy Archives', 'wpv-views'); ?></h3>
                    <ul>
                        <?php foreach ( $taxonomies as $category_slug => $category ): ?>
                            <?php
                                $name = $category->name;
                                $show_asterisk = false;
								$checked = ( isset( $options['view_taxonomy_loop_' . $name ] ) && $options['view_taxonomy_loop_' . $name ] == $view_id ) ? ' checked="checked"' : '';
								if ( isset( $options['view_taxonomy_loop_' . $name ] ) && $options['view_taxonomy_loop_' . $name ] != $view_id ) {
									$show_asterisk = true;
									$show_asterisk_explanation = true;
								}
							?>
                            <li>
								<input type="checkbox" <?php echo $checked; ?> id="wpv-view-taxonomy-loop-<?php echo $name; ?>" name="wpv-view-taxonomy-loop-<?php echo $name; ?>" />
                                <label for="wpv-view-taxonomy-loop-<?php echo $name; ?>"><?php echo $category->labels->name; echo $show_asterisk ? $asterisk : ''; ?></label>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    <?php if ( $show_asterisk_explanation ) { ?>
                    <span class="wpv-asterisk-explanation">
						<?php echo $asterisk_explanation; ?>
                    </span>
                    <?php } ?>
                    <?php endif; ?>

                    <?php if ( $view_id == 0 ): ?>
                    <p>
                        <strong><label for="wpv-new-archive-name"><?php _e('Name this WordPress Archive','wpv-views'); ?></label></strong>
                    </p>
                    <p>
                        <input type="text" value="" class="js-wpv-new-archive-name wpv-new-archive-name" placeholder="<?php _e('WordPress Archive name','wpv-views') ?>" name="wpv-new-archive-name">
                    </p>
                    <?php endif; ?>
                    <div class="js-error-container"></div>
                    </form>
                </div>
                <div class="wpv-dialog-footer">
                    <button class="button-secondary js-dialog-close" type="button" name="wpv-archive-view-cancel"><?php _e('Cancel', 'wpv-views'); ?></button>
                    <?php if ( $view_id == 0 ) { ?>
                    <button class="button-primary js-wpv-add-archive" type="button" name="wpv-archive-view-ok" data-error="<?php echo htmlentities( __('A WordPress Archive with that name already exists. Please use another name.', 'wpv-views'), ENT_QUOTES ); ?>" data-url="<?php echo admin_url( 'admin.php?page=view-archives-editor&amp;view_id='); ?>" disabled="disabled">
                        <?php _e('Add new Archive', 'wpv-views'); ?>
                    </button>
                    <?php } else { ?>
					<button class="button-primary js-wpv-update-archive" type="button" name="wpv-archive-view-ok">
						<?php  _e('Accept', 'wpv-views'); ?>
					</button>
                    <?php } ?>
                </div>
        </div>

        <?php
    }

    public function check_archive_loops_exists() {
        global $WP_Views;

        $options = $WP_Views->get_options();
        $loops = $this->_get_post_type_loops();
        
        foreach($loops as $loop => $loop_name) {
            foreach ($options as $opt_id=> $opt_name) {
                            
                if ('view_'.$loop == $opt_id && $opt_name !== 0) {

                    unset($loops[$loop]);
                    break;
                }
            }
		}

        $taxonomies = get_taxonomies('', 'objects');
        $exclude_tax_slugs = array();
	$exclude_tax_slugs = apply_filters( 'wpv_admin_exclude_tax_slugs', $exclude_tax_slugs );
       
        foreach ($taxonomies as $category_slug => $category) {
            
			if ( in_array( $category_slug, $exclude_tax_slugs ) ) {
				unset($taxonomies[$category_slug]);
				continue;
			}
			if ( !$category->show_ui ) {
				unset($taxonomies[$category_slug]);
				continue; // Only show taxonomies with show_ui set to TRUE
			}
	
			foreach ($options as $opt_id=> $opt_name) {
				
				if ('view_taxonomy_loop_' . $category_slug == $opt_id && $opt_name !== 0) {
					
					unset($taxonomies[$category_slug]);
					break;
				}
			}
        }
       

        return !(empty($loops) && empty($taxonomies));
    }

    function update_view_archive_settings($post_id, $data) {
        global $wpdb, $WP_Views;

        $found = false;
        $options = $WP_Views->get_options();

        // clear existing ones
        $loops = $this->_get_post_type_loops();
        foreach ($loops as $type => $name) {
            if (isset($options['view_' . $type]) && $options['view_' . $type] == $post_id) {
                unset($options['view_' . $type]);
                $found = true;
            }
        }
        $taxonomies = get_taxonomies('', 'objects');
        foreach ($taxonomies as $category_slug => $category) {
            if (isset($options['view_taxonomy_loop_' . $category_slug]) && $options['view_taxonomy_loop_' . $category_slug] == $post_id) {
                unset($options['view_taxonomy_loop_' . $category_slug]);
                $found = true;
            }
        }

        foreach ($data as $key => $value) {
            if (strpos($key, 'wpv-view-loop-') === 0) {
                preg_match('/wpv-view-loop-(.*)/', $key, $out);
                $options['view_' . $out[1]] = $post_id;
                $found = true;
            }

            if (strpos($key, 'wpv-view-taxonomy-loop-') === 0) {
                $options['view_taxonomy_loop_' . substr($key, 23)] = $post_id;
                $found = true;
            }
        }
        self::clear_options_data($options);
        if ($found) {
            $WP_Views->save_options($options);
        }
    }

    // TODO this clearing function deletes all View options but the ones starting with wpv
    // and runs every single time a WPA is updated
    // it loops through every View setting too: it's too expensive
    // We need a better way of clearing the Views settings for loops about WPA and CT when the related objects have been deleted
    // MAYBE it would be better to check on render time, and if now available then delete the record, and remove all this clearing function altogether
    static function clear_options_data(&$options) {
        global $wpdb;

        foreach ($options as $k=>$v) {
			if ( substr( $k, 0, 3 ) != "wpv" ) {
				$post_exists = $wpdb->get_row("SELECT * FROM {$wpdb->posts} WHERE id = '" . $v . "' AND post_type IN ('view','view-template')", 'ARRAY_A');
				if(!$post_exists){
					unset($options[$k]);
				}
            }
        }
    }

}

global $WPV_view_archive_loop;
$WPV_view_archive_loop = new WP_Views_archive_loops;

