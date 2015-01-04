<?php

/**
* wpv_admin_archive_listing_page
*
* Creates the main structure of the WPA admin listing page: wrapper and header
*
*/

function wpv_admin_archive_listing_page() {
	global $WPV_view_archive_loop;
	?>
	<div class="wrap toolset-views">

        <div class="wpv-views-listing-page wpv-views-listing-archive-page" data-none-message="<?php _e("This WordPress Archive isn't being used for any loops.",'wpv-views') ?>">
		<?php
		$has_items = wpv_check_views_exists('archive');
		$search_term = isset( $_GET["search"] ) ? urldecode( sanitize_text_field($_GET["search"]) ) : '';
		wp_nonce_field( 'work_views_listing', 'work_views_listing' );
		wp_nonce_field( 'wpv_remove_view_permanent_nonce', 'wpv_remove_view_permanent_nonce' );
		?>
		<div id="icon-views" class="icon32"></div>
        	<h2><!-- classname wpv-page-title removed -->
        		<?php _e('WordPress Archives', 'wpv-views') ?>
        		<?php if ( $WPV_view_archive_loop->check_archive_loops_exists() && $has_items ) { ?>
        		<a href="#" data-target="<?php echo admin_url('admin-ajax.php');?>?action=wpv_create_wp_archive_button" class="add-new-h2 js-wpv-views-archive-add-new wpv-views-archive-add-new">
				<?php _e('Add new WordPress Archive','wpv-views') ?>
			</a>
			<?php } ?>
			<?php if ( !empty( $search_term ) ) {
				$search_message = __('Search results for "%s"','wpv-views');
				if ( isset( $_GET["status"] ) && 'trash' == sanitize_text_field( $_GET["status"] ) ) { 
					$search_message = __('Search results for "%s" in trashed WordPress Archives', 'wpv-views');
				} ?>
				<span class="subtitle">
					<?php echo sprintf( $search_message, $search_term ); ?>
				</span>
			<?php } ?>
        	</h2>
        	
       <?php // Messages: trashed, untrashed, deleted
		if ( isset( $_GET['trashed'] ) && is_numeric( $_GET['trashed'] ) ) { ?>
			<div id="message" class="updated below-h2">
				<p>
					<?php _e('WordPress Archive moved to the Trash', 'wpv-views'); ?>. <a href="<?php echo admin_url(); ?>admin.php?page=view-archives&amp;untrashed=1" class="js-wpv-untrash" data-id="<?php echo $_GET['trashed']; ?>" data-nonce="<?php echo wp_create_nonce( 'wpv_view_listing_actions_nonce' ); ?>"><?php _e('Undo', 'wpv-views'); ?></a>
				</p>
			</div>
		<?php }
		if ( isset( $_GET['untrashed'] ) && is_numeric( $_GET['untrashed'] ) && (int)$_GET['untrashed'] == 1 ) { ?>
			<div id="message" class="updated below-h2">
				<p>
					<?php _e('WordPress Archive restored from the Trash', 'wpv-views'); ?>
				</p>
			</div>
		<?php }
		if ( isset( $_GET['deleted'] ) && is_numeric( $_GET['deleted'] ) && (int)$_GET['deleted'] == 1 ) { ?>
			<div id="message" class="updated below-h2">
				<p>
					<?php _e('WordPress Archive permanently deleted', 'wpv-views'); ?>
				</p>
			</div>
		<?php } ?>

            <?php            
		if ( isset( $_GET["arrangeby"] ) && sanitize_text_field( $_GET["arrangeby"] ) == 'usage' ) {
			wp_nonce_field( 'wpv_wp_archive_arrange_usage', 'wpv_wp_archive_arrange_usage' );
			?>
			<?php if ( !$WPV_view_archive_loop->check_archive_loops_exists() ) {?>
				<p id="js-wpv-no-archive" class="toolset-alert toolset-alert-info update below-h2">
					<?php _e('All loops have a WordPress Archive assigned','wpv-views'); ?>
				</p>
			<?php } ?>
			<div class="wpv-views-listing-arrange js-wpv-views-listing-arrange">
				<p><?php _e('Arrange by','wpv-views'); ?>: </p>
				<ul>
				<li data-sortby="name"><a href="<?php echo admin_url('admin.php'); ?>?page=view-archives"><?php _e('Name','wpv-views') ?></a></li>
				<li data-sortby="usage" class="active"><?php _e('Usage','wpv-views') ?></li>
				</ul>
			</div>
			<?php
			wpv_admin_wordpress_archives_listing_table_by_usage();
			if ( $WPV_view_archive_loop->check_archive_loops_exists() ) { ?>
				<p class="add-new-view js-add-new-view">
					<a class="button js-wpv-views-archive-add-new wpv-views-archive-add-new" data-target="<?php echo admin_url('admin-ajax.php');?>?action=wpv_create_wp_archive_button" href="admin.php?page=view-archives-new">
						<i class="icon-plus"></i><?php _e('Add new WordPress Archive','wpv-views') ?>
					</a>
				</p>
			<?php }
		} else {
			if ( $has_items ) {
				?>
				<?php
				wpv_admin_wordpress_archives_listing_table_by_name($has_items);
			} else { ?>
				<?php if ( !$WPV_view_archive_loop->check_archive_loops_exists() ) {?>
					<p id="js-wpv-no-archive" class="toolset-alert toolset-alert-info">
						<?php _e('All loops have a WordPress Archive assigned','wpv-views'); ?>
					</p>
				<?php } ?>
				<div class="wpv-view-not-exist js-wpv-view-not-exist">
					<p><?php _e('WordPress Archives let you customize the output of standard Archive pages.');?></p>
					<p>
					<a class="button js-wpv-views-archive-create-new" data-target="<?php echo admin_url('admin-ajax.php');?>?action=wpv_create_wp_archive_button" href="<?php get_admin_url(); ?>admin.php?page=view-archives-new">
						<i class="icon-plus"></i>
						<?php _e('Create your first WordPress Archive');?>
					</a>
					</p>
				</div>
			<?php }
		}
           
		?>

            

        </div> <!-- .wpv-settings-container" -->


	</div>
<?php

}

function wpv_admin_wordpress_archives_listing_table_by_name($has_items) { ?>
	<div id="js-wpv-archive-tables-containter" class="wpv-archive-tables-containter">
		<?php wpv_admin_archive_listing_name($has_items); ?>
	</div>
<?php }

function wpv_admin_wordpress_archives_listing_table_by_usage() {?>
	<div id="js-wpv-archive-tables-containter" class="wpv-archive-tables-containter">
		<?php wpv_admin_archive_listing_usage(); ?>
	</div>
<?php }

function wpv_admin_archive_listing_name($views_ids = array()) {

	global $WP_Views, $WPV_view_archive_loop, $wpdb;
	
	$mod_url = array( // array of URL modifiers
		'orderby' => '',
		'order' => '',
		'search' => '',
		'items_per_page' => '',
		'paged' => '',
		'status' => ''
	);
	
	$wpv_args = array( // array of WP_Query parameters
		'post_type' => 'view',
		'post__in' => $views_ids,
		'posts_per_page' => WPV_ITEMS_PER_PAGE,
		'order' => 'ASC',
		'orderby' => 'title',
		'post_status' => 'publish'
	);
	
	if ( isset( $_GET["status"] ) && '' != $_GET["status"] ) { // apply post_status coming from the URL parameters
		$wpv_args['post_status'] = sanitize_text_field( $_GET["status"] );
		$mod_url['status'] = '&amp;status=' . sanitize_text_field( $_GET["status"] );
	}
	
	if ( isset( $_GET["search"] ) && '' != $_GET["search"] ) {
		$s_param = urldecode(sanitize_text_field($_GET["search"]));
		$new_args = $wpv_args;
		$unique_ids = array();
		
		$new_args['posts_per_page'] = '-1';
		$new_args['s'] = $s_param;
		$query_1 = new WP_Query( $new_args );
		
		while ($query_1->have_posts()) :
			$query_1->the_post();
			$unique_ids[] = get_the_id();
		endwhile;
		
		unset($new_args['s']);
		
		$new_args['meta_query'] =array(
			array(
				'key' => '_wpv_description',
				'value' => $s_param,
				'compare' => 'LIKE'
			)
		);
		$query_2 = new WP_Query( $new_args );
		
		while ($query_2->have_posts()) :
			$query_2->the_post();
			$unique_ids[] = get_the_id();
		endwhile;
		
		$unique = array_unique($unique_ids);
		
		if ( count($unique) == 0 ){
			$wpv_args['post__in'] = array('-1');
		}else{
			$wpv_args['post__in'] = $unique;
		}
	
		$mod_url['search'] = '&amp;search=' . sanitize_text_field($_GET["search"]);
	}
	
	if ( isset( $_GET["items_per_page"] ) && '' != $_GET["items_per_page"] ) {
		$wpv_args['posts_per_page'] = (int) $_GET["items_per_page"];
		$mod_url['items_per_page'] = '&amp;items_per_page=' . (int) $_GET["items_per_page"];
	}
	
	if ( isset( $_GET["orderby"] ) && '' != $_GET["orderby"] ) {
		$wpv_args['orderby'] = sanitize_text_field($_GET["orderby"]);
		$mod_url['orderby'] = '&amp;orderby=' . sanitize_text_field($_GET["orderby"]);
		if ( isset( $_GET["order"] ) && '' != $_GET["order"] ) {
			$wpv_args['order'] = sanitize_text_field($_GET["order"]);
			$mod_url['order'] = '&amp;order=' . sanitize_text_field($_GET["order"]);
		}
	}
	
	if ( isset( $_GET["paged"] ) && '' != $_GET["paged"]) {
		$wpv_args['paged'] = (int) $_GET["paged"];
		$mod_url['paged'] = '&amp;paged=' . (int) $_GET["paged"];
	}
	
	$wpv_query = new WP_Query( $wpv_args );
	
	// $wpv_query = new WP_Query( $wpv_args );
	$wpv_count_posts = $wpv_query->post_count;
	$wpv_found_posts = $wpv_query->found_posts;
	$wpv_total_views_list = implode("','", $views_ids);
	$wpv_views_status = array(); // to hold the number of Views in each status
	$wpv_views_status['publish'] = $wpdb->get_var( "SELECT COUNT(ID) from $wpdb->posts WHERE post_status = 'publish' AND ID IN ('$wpv_total_views_list')" );
	$wpv_views_status['trash'] = ( sizeof( $views_ids ) - $wpv_views_status['publish'] );
	?>
	<div class="wpv-views-listing-arrange js-wpv-views-listing-arrange" style="clear:none;float:left">
		<p style="margin-bottom:0"><?php _e('Arrange by','wpv-views'); ?>: </p>
		<ul>
		<li data-sortby="name" class="active"><?php _e('Name','wpv-views') ?></li>
		<li data-sortby="usage"><a href="<?php echo admin_url('admin.php'); ?>?page=view-archives&amp;arrangeby=usage"><?php _e('Usage','wpv-views') ?></a></li>
		</ul>
	</div>
	<ul class="subsubsub" style="clear:left"><!-- links to lists WPA in different statuses -->
		<li><a href="<?php echo admin_url('admin.php'); ?>?page=view-archives&amp;status=publish"<?php if ( $wpv_args['post_status'] == 'publish' && !isset( $_GET["search"] ) ) echo ' class="current"'; ?>><?php _e('Published', 'wpv-views'); ?></a> (<?php echo $wpv_views_status['publish']; ?>) | </li>
		<li><a href="<?php echo admin_url('admin.php'); ?>?page=view-archives&amp;status=trash"<?php if ( $wpv_args['post_status'] == 'trash' && !isset( $_GET["search"] ) ) echo ' class="current"'; ?>><?php _e('Trash', 'wpv-views'); ?></a> (<?php echo $wpv_views_status['trash']; ?>)</li>
	</ul>
	<?php if ( $wpv_found_posts > 0 ) { ?>
	<form id="posts-filter" action="" method="get" class="<?php // if ( !$WPV_view_archive_loop->check_archive_loops_exists() ) echo 'hidden'; WHY hide the search when all loops have been asigned? ?>">
		<p class="search-box">
			<label class="screen-reader-text" for="post-search-input"><?php _e('Search WordPress Archives','wpv-views'); ?>:</label>
			<?php $search_term = isset( $_GET["search"] ) ? urldecode( sanitize_text_field($_GET["search"]) ) : ''; ?>
			<input type="search" id="post-search-input" name="search" value="<?php echo $search_term; ?>" />
			<input type="submit" name="" id="search-submit" class="button" value="<?php echo htmlentities( __('Search WordPress Archives','wpv-views'), ENT_QUOTES ); ?>" />
			<input type="hidden" name="paged" value="1" />
		</p>
	</form>
	<?php } ?>
	<?php if ( !$WPV_view_archive_loop->check_archive_loops_exists() ) {?>
		<p id="js-wpv-no-archive" class="toolset-alert toolset-alert-info">
			<?php _e('All loops have a WordPress Archive assigned','wpv-views'); ?>
		</p>
	<?php } ?>
	<?php
	if ( $wpv_count_posts > 0 ) {
	?>
	
        <table id="wpv_view_list" class="js-wpv-views-listing wpv-views-listing wpv-views-listing-by-name widefat">
            <thead>
                <tr>
                    <?php 
			$column_active = '';
			$column_sort_to = 'ASC';
			$column_sort_now = 'ASC';
			if ( $wpv_args['orderby'] === 'title' ) {
				$column_active = ' views-list-sort-active';
				$column_sort_to = ( $wpv_args['order'] === 'ASC' ) ? 'DESC' : 'ASC';
				$column_sort_now = $wpv_args['order'];
			}
			?>
			<th class="wpv-admin-listing-col-title"><a href="<?php echo admin_url('admin.php'); ?>?page=view-archives&amp;orderby=title&amp;order=<?php echo $column_sort_to . $mod_url['search'] . $mod_url['items_per_page'] . $mod_url['paged']; ?>" class="js-views-list-sort views-list-sort<?php echo $column_active; ?>" data-orderby="title"><?php _e('Title','wpv-views') ?> <i class="icon-sort-by-alphabet<?php if ( $column_sort_now === 'DESC') echo '-alt'; ?>"></i></a></th>
			<th class="wpv-admin-listing-col-usage"><?php _e('Archive usage','wpv-views') ?></th>
			<th class="wpv-admin-listing-col-action"><?php _e('Action','wpv-views') ?></th>
                    <?php 
			$column_active = '';
			$column_sort_to = 'DESC';
			$column_sort_now = 'DESC';
			if ( $wpv_args['orderby'] === 'date' ) {
				$column_active = ' views-list-sort-active';
				$column_sort_to = ( $wpv_args['order'] === 'ASC' ) ? 'DESC' : 'ASC';
				$column_sort_now = $wpv_args['order'];
			}
			?>
			<th class="wpv-admin-listing-col-date"><a href="<?php echo admin_url('admin.php'); ?>?page=view-archives&amp;orderby=date&amp;order=<?php echo $column_sort_to . $mod_url['search'] . $mod_url['items_per_page'] . $mod_url['paged']; ?>" class="js-views-list-sort views-list-sort<?php echo $column_active; ?>" data-orderby="date"><?php _e('Date','wpv-views') ?> <i class="icon-sort-by-attributes<?php if ( $column_sort_now === 'DESC') echo '-alt'; ?>"></i></a></th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <?php 
			$column_active = '';
			$column_sort_to = 'ASC';
			$column_sort_now = 'ASC';
			if ( $wpv_args['orderby'] === 'title' ) {
				$column_active = ' views-list-sort-active';
				$column_sort_to = ( $wpv_args['order'] === 'ASC' ) ? 'DESC' : 'ASC';
				$column_sort_now = $wpv_args['order'];
			}
			?>
			<th class="wpv-admin-listing-col-title"><a href="<?php echo admin_url('admin.php'); ?>?page=view-archives&amp;orderby=title&amp;order=<?php echo $column_sort_to . $mod_url['search'] . $mod_url['items_per_page'] . $mod_url['paged']; ?>" class="js-views-list-sort views-list-sort<?php echo $column_active; ?>" data-orderby="title"><?php _e('Title','wpv-views') ?> <i class="icon-sort-by-alphabet<?php if ( $column_sort_now === 'DESC') echo '-alt'; ?>"></i></a></th>
			<th class="wpv-admin-listing-col-usage"><?php _e('Archive usage','wpv-views') ?></th>
			<th class="wpv-admin-listing-col-action"><?php _e('Action','wpv-views') ?></th>
                    <?php 
			$column_active = '';
			$column_sort_to = 'DESC';
			$column_sort_now = 'DESC';
			if ( $wpv_args['orderby'] === 'date' ) {
				$column_active = ' views-list-sort-active';
				$column_sort_to = ( $wpv_args['order'] === 'ASC' ) ? 'DESC' : 'ASC';
				$column_sort_now = $wpv_args['order'];
			}
			?>
			<th class="wpv-admin-listing-col-date"><a href="<?php echo admin_url('admin.php'); ?>?page=view-archives&amp;orderby=date&amp;order=<?php echo $column_sort_to . $mod_url['search'] . $mod_url['items_per_page'] . $mod_url['paged']; ?>" class="js-views-list-sort views-list-sort<?php echo $column_active; ?>" data-orderby="date"><?php _e('Date','wpv-views') ?> <i class="icon-sort-by-attributes<?php if ( $column_sort_now === 'DESC') echo '-alt'; ?>"></i></a></th>
                </tr>
            </tfoot>

            <tbody class="js-wpv-views-listing-body">
                <?php
                $options = $WP_Views->get_options();
		$loops = $WPV_view_archive_loop->_get_post_type_loops();
		$builtin_loops = array('home-blog-page' => __('Home/Blog', 'wpv-views'),
				'search-page' => __('Search results', 'wpv-views'),
				'author-page' => __('Author archives', 'wpv-views'),
				'year-page' => __('Year archives', 'wpv-views'),
				'month-page' => __('Month archives', 'wpv-views'),
				'day-page' => __('Day archives', 'wpv-views')
		);
		$taxonomies = get_taxonomies('', 'objects');
		$exclude_tax_slugs = array();
		$exclude_tax_slugs = apply_filters( 'wpv_admin_exclude_tax_slugs', $exclude_tax_slugs );
		$alternate = '';
		while ($wpv_query->have_posts()) :
			$wpv_query->the_post();
			$post_id = get_the_id();
			$post = get_post($post_id);
			$meta = get_post_meta($post_id, '_wpv_settings');
			$view_description = get_post_meta($post_id, '_wpv_description', true);
			$alternate = ' alternate' == $alternate ? '' : ' alternate';
			?>
			<tr id="wpv_view_list_row_<?php echo $post->ID; ?>" class="js-wpv-view-list-row<?php echo $alternate; ?>" >
				<td  class="wpv-admin-listing-col-title">
					<span class="row-title">
					<?php if ( $wpv_args['post_status'] == 'trash' ) { ?>
						<?php echo $post->post_title; ?>
					<?php } else { ?>
						<a href="admin.php?page=view-archives-editor&amp;view_id=<?php echo $post->ID; ?>"><?php echo trim($post->post_title); ?></a>
					<?php } ?>
					</span>
					<?php if (isset($view_description) && '' != $view_description): ?>
					<p class="desc">
					<?php echo nl2br($view_description)?>
					</p>
				<?php endif; ?>
				</td>
				<td  class="wpv-admin-listing-col-usage">
					<?php
					$selected = array();
					foreach ($loops as $loop => $loop_name) {
						if (isset($options['view_' . $loop]) && $options['view_' . $loop] == $post->ID) {
							$not_built_in = '';
							if ( !isset( $builtin_loops[$loop] ) ) {
								$not_built_in = __(' (post type archive)', 'wpv-views');
							}
							$selected[] = '<li>' . $loop_name . $not_built_in . '</li>';
						}
					}
					foreach ($taxonomies as $category_slug => $category) {
						if ( in_array( $category_slug, $exclude_tax_slugs ) ) {
							continue;
						}
						if ( !$category->show_ui ) {
							continue; // Only show taxonomies with show_ui set to TRUE
						}
						$name = $category->name;
						if (isset ($options['view_taxonomy_loop_' . $name ]) && $options['view_taxonomy_loop_' . $name ] == $post->ID) {
							$selected[] = '<li>' . $category->labels->name . __(' (taxonomy archive)', 'wpv-views') . '</li>';
						}
					}
					if ( !empty( $selected ) ) { ?>
					<ul class="wpv-taglike-list js-list-views-loops">
					<?php
						echo implode( $selected );
					?>
					</ul>
					<?php
					} else {
						echo __("This WordPress Archive isn't being used for any loops.",'wpv-views');
					}
					?>
					</ul>
				</td>
				<td class="wpv-admin-listing-col-action">
					<select class="js-list-views-action" name="list_views_action_<?php echo $post->ID; ?>" id="list_views_action_<?php echo $post->ID; ?>" data-view-id="<?php echo $post->ID; ?>" data-viewactionnonce="<?php echo wp_create_nonce( 'wpv_view_listing_actions_nonce' ); ?>">
						<option value="0"><?php _e('Choose','wpv-views') ?>&hellip;</option>
						<?php if ( $wpv_args['post_status'] == 'publish' ) { ?>
							<option value="change"><?php _e('Change archive usage','wpv-views') ?></option>
							<option value="trash"><?php _e('Move to trash','wpv-views') ?></option>
						<?php } else if ( $wpv_args['post_status'] == 'trash' ) { ?>
							<option value="restore-from-trash"><?php _e('Restore from trash','wpv-views') ?></option>
							<option value="delete"><?php _e('Delete','wpv-views') ?></option>
						<?php } ?>
					</select>
				</td>
				<td class="wpv-admin-listing-col-date">
					<?php echo get_the_time(get_option('date_format'), $post->ID); ?>
				</td>
			</tr>
		<?php endwhile; ?>
            </tbody>
        </table>
	<?php
	if ( $WPV_view_archive_loop->check_archive_loops_exists() ) { ?>
		<p class="add-new-view js-add-new-view">
			<a class="button js-wpv-views-archive-add-new wpv-views-archive-add-new" data-target="<?php echo admin_url('admin-ajax.php');?>?action=wpv_create_wp_archive_button" href="">
				<i class="icon-plus"></i><?php _e('Add new WordPress Archive','wpv-views') ?>
			</a>
		</p>
	<?php } ?>
	
	<?php
		wpv_admin_listing_pagination( 'view-archives', $wpv_found_posts, $wpv_args["posts_per_page"], $mod_url );
	?>
	
	<?php } else { // No WordPress Archives matches the criteria ?>
		<div class="wpv-views-listing views-empty-list">
		<?php if ( isset( $_GET["status"] ) && $_GET["status"] == 'trash' && isset( $_GET["search"] ) && $_GET["search"] != '' ) { ?>
			<p><?php echo __('No WordPress Archives in trash matched your criteria.','wpv-views'); ?> <a class="button-secondary" href="<?php echo admin_url('admin.php'); ?>?page=view-archives<?php echo $mod_url['orderby'] . $mod_url['order'] . $mod_url['items_per_page']; ?>&amp;paged=1&amp;status=trash"><?php _e('Return', 'wpv-views'); ?></a></p>
		<?php } else if ( isset( $_GET["status"] ) && $_GET["status"] == 'trash' ) { ?>
			<p><?php echo __('No WordPress Archives in trash.','wpv-views'); ?> <a class="button-secondary" href="<?php echo admin_url('admin.php'); ?>?page=view-archives<?php echo $mod_url['orderby'] . $mod_url['order'] . $mod_url['items_per_page']; ?>&amp;paged=1"><?php _e('Return', 'wpv-views'); ?></a></p>
		<?php } else if ( isset( $_GET["search"] ) && $_GET["search"] != '' ) { ?>
			<p><?php echo __('No WordPress Archives matched your criteria.','wpv-views'); ?> <a class="button-secondary" href="<?php echo admin_url('admin.php'); ?>?page=view-archives<?php echo $mod_url['orderby'] . $mod_url['order'] . $mod_url['items_per_page']; ?>&amp;paged=1"><?php _e('Return', 'wpv-views'); ?></a></p>
		<?php } else { ?>
			<p><?php _e('WordPress Archives let you customize the output of standard Archive pages.');?></p>
			<p>
			<a class="button js-wpv-views-archive-create-new" data-target="<?php echo admin_url('admin-ajax.php');?>?action=wpv_create_wp_archive_button" href="<?php get_admin_url(); ?>admin.php?page=view-archives-new">
				<i class="icon-plus"></i>
				<?php _e('Create your first WordPress Archive');?>
			</a>
			</p>
		<?php } ?>
		</div>
	<?php }
}



function wpv_admin_archive_listing_usage() {
    ?>
        <table id="wpv_view_list_usage" class="js-wpv-views-listing wpv-views-listing wpv-views-listing-by-usage widefat">

            <thead>
                <tr>
                    <th class="wpv-admin-listing-col-usage js-wpv-col-one"><?php _e('Archive loop','wpv-views') ?></th>
                    <th class="wpv-admin-listing-col-title js-wpv-col-two"><?php _e('WordPress Archive used','wpv-views') ?></th>
                    <th class="wpv-admin-listing-col-action"><?php _e('Action','wpv-views') ?></th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th class="wpv-admin-listing-col-usage js-wpv-col-one"><?php _e('Used for','wpv-views') ?></th>
                    <th class="wpv-admin-listing-col-title js-wpv-col-two"><?php _e('Title','wpv-views') ?></th>
                    <th class="wpv-admin-listing-col-action"><?php _e('Action','wpv-views') ?></th>
                </tr>
            </tfoot>

            <tbody class="js-wpv-views-listing-body">

                <?php
                    global $WP_Views;

                    $options = $WP_Views->get_options();
                    $alternate = '';
                    
                    $loops = array('home-blog-page' => __('Home/Blog', 'wpv-views'),
							'search-page' => __('Search results', 'wpv-views'),
							'author-page' => __('Author archives', 'wpv-views'),
							'year-page' => __('Year archives', 'wpv-views'),
							'month-page' => __('Month archives', 'wpv-views'),
							'day-page' => __('Day archives', 'wpv-views')
					);
                    
                    foreach ( $loops as $slug => $name ) {
						$alternate = ' alternate' == $alternate ? '' : ' alternate';
						$post = null;
						if ( isset( $options['view_' . $slug] ) ) {
							$post = get_post( $options['view_' . $slug] );
						}
                    ?>
                    <tr class="js-wpv-view-list-row<?php echo $alternate; ?>">
                    <td class="wpv-admin-listing-col-usage">
                        <span class="row-title"><?php echo $name ?></span>
                    </td>
                    <?php if ( is_null( $post ) ): ?>
                        <td colspan="2">
                            <a class="button button-small js-create-view-for-archive" data-forwhom="<?php echo esc_attr( $name ); ?>" href="#"><i class="icon-plus"></i><?php _e('Create a WordPres Archive for this loop');?></a>
                        </td>
                    <?php else: ?>
                    <td class="wpv-admin-listing-col-title">
                        <a class="" href="admin.php?page=view-archives-editor&amp;view_id=<?php echo $post->ID?>"><?php echo $post->post_title; ?></a>
                    </td>

                    <td class="wpv-admin-listing-col-action">
                        <select class="js-list-views-usage-action" name="list_views_usage_action_<?php echo $post->ID; ?>" id="list_views_usage_action_<?php echo $post->ID; ?>" data-view-id="<?php echo 'view_' . $slug; ?>">
                            <option value="0"><?php _e('Choose','wpv-views') ?>&hellip;</option>
                            <option value="change_usage"><?php _e('Change','wpv-views') ?></option>
                        </select>
                    </td>
                    <?php endif; ?>
                    </tr>
                    <?php
                    }
                    $pt_loops = array();
					// Only offer loops for post types that already have an archive
					$post_types = get_post_types( array( 'public' => true, 'has_archive' => true), 'objects' );
					foreach ( $post_types as $post_type ) {
						if ( !in_array( $post_type->name, array( 'post', 'page', 'attachment' ) ) ) {
							$type = 'cpt_' . $post_type->name;
							$name = $post_type->labels->name;
							$pt_loops[$type] = $name;
						}
					}
					if ( count( $pt_loops ) > 0 ) {
						foreach ( $pt_loops as $slug => $name ) {
							$alternate = ' alternate' == $alternate ? '' : ' alternate';
							$post = null;
							if ( isset( $options['view_' . $slug] ) ) {
								$post = get_post( $options['view_' . $slug] );
							}
						?>
						<tr class="js-wpv-view-list-row<?php echo $alternate; ?>">
						<td class="wpv-admin-listing-col-usage">
							<span class="row-title"><?php echo $name . __(' (post type archive)', 'wpv-views'); ?></span>
						</td>
						<?php if ( is_null( $post ) ): ?>
							<td colspan="2">
								<a class="button button-small js-create-view-for-archive" data-forwhom="<?php echo esc_attr( $name ); ?>" href="#"><i class="icon-plus"></i><?php _e('Create a WordPres Archive for this loop');?></a>
							</td>
						<?php else: ?>
						<td class="wpv-admin-listing-col-title">
							<a class="" href="admin.php?page=view-archives-editor&amp;view_id=<?php echo $post->ID?>"><?php echo $post->post_title; ?></a>
						</td>

						<td class="wpv-admin-listing-col-action">
							<select class="js-list-views-usage-action" name="list_views_usage_action_<?php echo $post->ID; ?>" id="list_views_usage_action_<?php echo $post->ID; ?>" data-view-id="<?php echo 'view_' . $slug; ?>">
								<option value="0"><?php _e('Choose','wpv-views') ?>&hellip;</option>
								<option value="change_usage"><?php _e('Change','wpv-views') ?></option>
							</select>
						</td>
						<?php endif; ?>
						</tr>
						<?php
						}
                    }
                    
                    $taxonomies = get_taxonomies('', 'objects');
                    $exclude_tax_slugs = array();
					$exclude_tax_slugs = apply_filters( 'wpv_admin_exclude_tax_slugs', $exclude_tax_slugs );
					foreach ( $taxonomies as $category_slug => $category ) {
						if ( in_array( $category_slug, $exclude_tax_slugs ) ) {
							continue;
						}
						if ( !$category->show_ui ) {
							continue; // Only show taxonomies with show_ui set to TRUE
						}
						$name = $category->name;
						$alternate = ' alternate' == $alternate ? '' : ' alternate';
						$name = $category->name;
						$label = $category->labels->singular_name;
						$post = null;
						if ( isset( $options['view_taxonomy_loop_'.$name] ) ) {
							$post = get_post( $options['view_taxonomy_loop_' . $name] );
						}
						?>
						<tr class="js-wpv-view-list-row<?php echo $alternate; ?>">
						<td class="wpv-admin-listing-col-usage">
							<span class="row-title"><?php echo $label . __(' (taxonomy archive)', 'wpv-views'); ?></span>
						</td>
						<?php if ( is_null( $post ) ): ?>
							<td colspan="2">
								<a class="button button-small js-create-view-for-archive" data-forwhom="<?php echo esc_attr( $label ); ?>" href="#"><i class="icon-plus"></i><?php _e('Create a WordPres Archive for this loop');?></a>
							</td>
						<?php else: ?>
						<td class="wpv-admin-listing-col-title">
							<a class="" href="admin.php?page=view-archives-editor&amp;view_id=<?php echo $post->ID?>"><?php echo $post->post_title; ?></a>
						</td>

						<td class="wpv-admin-listing-col-action">
							<select class="js-list-views-usage-action" name="list_views_usage_action_<?php echo $post->ID; ?>" id="list_views_usage_action_<?php echo $post->ID; ?>" data-view-id="<?php echo 'view_taxonomy_loop_' . $name; ?>">
								<option value="0"><?php _e('Choose','wpv-views') ?>&hellip;</option>
								<option value="change_usage"><?php _e('Change','wpv-views') ?></option>
							</select>
						</td>
						<?php endif; ?>
						</tr>
						<?php
					}
                ?>
            </tbody>
        </table>
        <?php
}