<?php

class WPV_Widget extends WP_Widget{
    
    function WPV_Widget(){
        $widget_ops = array('classname' => 'widget_wp_views', 'description' => __( 'Displays a View', 'wpv-views') );
        $this->WP_Widget('wp_views', __('WP Views', 'wpv-views'), $widget_ops);
    }
    
    function widget( $args, $instance ) {
        global $WP_Views;
        extract($args);
        $instance = wp_parse_args( (array) $instance,
            array( 
                'title' => '',
                'view'  => false
            ) 
        );
        $title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);
		
		if ( $instance['view'] ) {
			$WP_Views->set_widget_view_id($instance['view']);
			
			echo $before_widget;
			if ( $title )
				echo $before_title . $title . $after_title;

			$out = $WP_Views->render_view_ex($instance['view'], $instance['view']);
			$out = wpv_do_shortcode($out);
			
			$post_type_object = get_post_type_object( 'view' );
			if ( current_user_can( $post_type_object->cap->edit_post, $instance['view'] ) ) {
				$out .= widget_view_link( $instance['view']);
			}
			
			echo $out;

			echo $after_widget;

			$WP_Views->set_widget_view_id(0);
		}
    }
    
    function form( $instance ) {
        global $WP_Views;
        $views = wpv_check_views_exists('normal');     
        $instance = wp_parse_args( (array) $instance, 
            array( 
                'title' => '',
                'view'  => false
            ) 
        );
        $title = $instance['title'];
        $view  = $instance['view'];
         ?>
        
        <?php if($views): ?>
         <p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_name('view'); ?>"><?php _e('View:', 'wpv-views'); ?></label>
			<select id="<?php echo $this->get_field_name('view'); ?>" name="<?php echo $this->get_field_name('view'); ?>" class="widefat">
			<?php foreach($views as $v): ?>
				<option value="<?php echo $v ?>"<?php if($view == $v): ?> selected="selected"<?php endif;?>><?php echo esc_html( get_the_title( $v ) ) ?></option>
			<?php endforeach;?>             
			</select>
		</p>
        <?php else: ?>
            <?php
                if (!$WP_Views->is_embedded()) {
                    printf(__('No Views defined. You can add them <a%s>here</a>.'), ' href="' . admin_url('admin.php?page=views'). '"');
                }
            ?>
        <?php endif;?>
        <?php
    }
    
    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $new_instance = wp_parse_args((array) $new_instance, 
            array( 
                'title' => '',
                'view'  => false
            ) 
        );
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['view']  = $new_instance['view'];
        
        return $instance;
    }
    
}

/**
 * class WPV_Widget_filter
 *
 * Displays only the filter section of a View
 * Can be used for a search
 *
 */

class WPV_Widget_filter extends WP_Widget{
    
    function WPV_Widget_filter(){
        $widget_ops = array('classname' => 'widget_wp_views_filter',
							'description' => __( 'Displays the filter section of a View.', 'wpv-views') 
							);
        $this->WP_Widget('wp_views_filter', __('WP Views Filter', 'wpv-views'), $widget_ops);
    }
    
    function widget( $args, $instance ) {
        global $WP_Views;
        extract($args);
        $instance = wp_parse_args( (array) $instance,
            array( 
                'title' => '',
                'view'  => false
            ) 
        );
        $title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);
		if ( $instance['view'] ) {
			$WP_Views->set_widget_view_id($instance['view']);
			
			echo $before_widget;
			if ( $title )
				echo $before_title . $title . $after_title;

			$atts = array();
			$atts['id'] = $instance['view'];
			$atts['target_id'] = $instance['target_id'];
			$out = $WP_Views->short_tag_wpv_view_form($atts);
			$out = wpv_do_shortcode($out);
			
			$post_type_object = get_post_type_object( 'view' );
			if ( current_user_can( $post_type_object->cap->edit_post, $instance['view'] ) ) {
				$out .= widget_view_link($instance['view']);
			}
			
			echo $out;

			echo $after_widget;

			$WP_Views->set_widget_view_id(0);
		}
    }
    
    function form( $instance ) {
        global $WP_Views, $sitepress, $wpdb;
        $views = wpv_check_views_exists('normal');
        $view_forms = array();
        
		if ( $views ) {
			foreach ( $views as $vi ) {
				 if ( $WP_Views->does_view_have_form_controls( $vi ) ) {
					$view_forms[] = $vi;
				 }
			}
		}
        
        $instance = wp_parse_args( (array) $instance, 
            array( 
                'title' => '',
                'view'  => false,
				'target_id' => '0'
            ) 
        );
        $title = $instance['title'];
        $view  = $instance['view'];
	$target_id = $instance['target_id'];
	
	$trans_join = '';
	$trans_where = '';
	
	if (function_exists('icl_object_id')) {
		// Adjust for WPML support
		if ( $target_id != '0' ) {
			$target_post_type = $wpdb->get_var("SELECT post_type FROM {$wpdb->posts} WHERE ID='{$target_id}'");
			if ( $target_post_type ) {
				$target_id = icl_object_id($target_id, $target_post_type, true);
				$translatable_post_types = array_keys($sitepress->get_translatable_documents());
				if(in_array($target_post_type, $translatable_post_types)){
					$current_lang_code = $sitepress->get_current_language();
					$trans_join = " JOIN {$wpdb->prefix}icl_translations t ";
					$trans_where = " AND ID = t.element_id AND t.language_code =  '{$current_lang_code}' ";
				}
			}
		} else { // if there is no target set, for example when adding the widget for the first time
			$current_lang_code = $sitepress->get_current_language();
			$trans_join = " JOIN {$wpdb->prefix}icl_translations t ";
			$trans_where = " AND ID = t.element_id AND t.language_code =  '{$current_lang_code}' ";
		}
	}
        
        $posts = $wpdb->get_results("SELECT ID, post_title, post_content FROM {$wpdb->posts} {$trans_join} WHERE post_content LIKE '%[wpv-view%' AND post_type NOT IN ('view','view-template','revision','cred-form') AND post_status='publish' {$trans_where}");

        
         ?>
        
        <?php if( count( $view_forms ) > 0 ): ?>
            <p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
			</p>
            <p>
				<label for="<?php echo $this->get_field_name('view'); ?>"><?php _e('View:', 'wpv-views'); ?></label>
				<select id="<?php echo $this->get_field_name('view'); ?>" name="<?php echo $this->get_field_name('view'); ?>" class="widefat">
				<?php foreach($view_forms as $v): ?>
					<option value="<?php echo $v ?>"<?php if($view == $v): ?> selected="selected"<?php endif;?>><?php echo esc_html( get_the_title( $v ) ) ?></option>
				<?php endforeach;?>             
				</select>
			</p>
			<p>
				<label for="<?php echo $this->get_field_name('target_id'); ?>"><?php _e('Target page:', 'wpv-views'); ?></label>
				<select id="<?php echo $this->get_field_name('target_id'); ?>" name="<?php echo $this->get_field_name('target_id'); ?>" class="widefat">
				<?php foreach($posts as $post): ?>
					<option value="<?php echo $post->ID ?>"<?php if($target_id == $post->ID): ?> selected="selected"<?php endif;?>><?php echo esc_html($post->post_title) ?></option>
				<?php endforeach;?>             
				</select>
			</p>

        <?php else: ?>
            <?php
                if (!$WP_Views->is_embedded()) {
                    printf(__('No Views with frontend forms defined. You can add them <a%s>here</a>.'), ' href="' . admin_url('admin.php?page=views'). '"');
                }
            ?>
        <?php endif;?>
        <?php
    }
    
    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $new_instance = wp_parse_args((array) $new_instance, 
            array( 
                'title' => '',
                'view'  => false,
				'target_id' => '0'
            ) 
        );
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['view']  = $new_instance['view'];
        $instance['target_id']  = $new_instance['target_id'];
        
        return $instance;
    }
    
}
  

function widget_view_link($view_id) {
	$options = get_option('wpv_options');
	if ( !isset($options['wpv_show_edit_view_link']) ){
		$options['wpv_show_edit_view_link'] = 1;	
	}
	if ( $options['wpv_show_edit_view_link'] == 1){
		$link =  '<a href="'. admin_url() .'admin.php?page=views-editor&view_id='. $view_id .'" title="'.__('Edit view', 'wpv-views').'">'.__('Edit view', 'wpv-views').' "'.get_the_title($view_id).'"</a>';
			
		$link = apply_filters( 'wpv_edit_view_link', $link );
	}
	else{
		$link = '';	
	}
	return $link;
}
  
?>
