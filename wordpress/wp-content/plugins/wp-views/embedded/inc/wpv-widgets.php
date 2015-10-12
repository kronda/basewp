<?php

/**
* wpv-widgets.php
*
* General implementation for Views widgets
*
* @package Views
*
* @since unknown
*/

/**
* WPV_Widget
*
* Widget to display a complete View
*
* @since unknown
*/

class WPV_Widget extends WP_Widget {
	
	public function __construct( $id_base = 'wp_views', $name = 'WP Views', $widget_options = array(), $control_options = array() ) {
		$this->id_base = 'wp_views';
		$this->name = __( 'WP Views', 'wpv-views' );
		$this->option_name = 'widget_' . $this->id_base;
		$this->widget_options = array(
			'classname' => 'widget_wp_views',
			'description' => __( 'Displays a View', 'wpv-views')
		);
		$this->control_options = wp_parse_args( $control_options, array('id_base' => $this->id_base) );
		parent::__construct( $this->id_base, $this->name, $this->widget_options, $this->control_options );
	}
    
    function widget( $args, $instance ) {
        global $WP_Views;
        extract( $args );
        $instance = wp_parse_args( (array) $instance,
            array( 
                'title' => '',
                'view'  => false
            ) 
        );
        $title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		if ( $instance['view'] ) {
			$WP_Views->set_widget_view_id( $instance['view'] );
			echo $before_widget;
			if ( $title ) {
				echo $before_title . $title . $after_title;
			}
			$out = $WP_Views->render_view_ex( $instance['view'], $instance['view'] );
			$out = wpv_do_shortcode( $out );
			$post_type_object = get_post_type_object( 'view' );
			if ( 
				! $WP_Views->is_embedded() 
				&& current_user_can( $post_type_object->cap->edit_post, $instance['view'] ) 
			) {
				$out .= widget_view_link( $instance['view']);
			}
			echo $out;
			echo $after_widget;
			$WP_Views->set_widget_view_id(0);
		}
    }
    
    function form( $instance ) {
        global $WP_Views;
        $views = wpv_check_views_exists( 'normal', array( 'post_status' =>'publish', 'orderby' => 'post_title' ) );
        $instance = wp_parse_args( (array) $instance, 
            array( 
                'title' => '',
                'view'  => false
            ) 
        );
        $title = $instance['title'];
        $view  = $instance['view'];
        if ( $views ) {
		?>
        <p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'wpv-views' ); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'view' ); ?>"><?php _e( 'View:', 'wpv-views' ); ?></label>
			<select id="<?php echo $this->get_field_id( 'view' ); ?>" name="<?php echo $this->get_field_name( 'view' ); ?>" class="widefat">
			<?php foreach ( $views as $v ) : ?>
				<option value="<?php echo $v ?>"<?php if ( $view == $v ) : ?> selected="selected"<?php endif;?>><?php echo esc_html( get_the_title( $v ) ) ?></option>
			<?php endforeach;?>             
			</select>
		</p>
        <?php 
		} else {
			if ( ! $WP_Views->is_embedded() ) {
				?>
				<p>
				<?php
					printf( __( 'There are no Views defined. You can add them <a%s>here</a>.', 'wpv-views' ), ' href="' . admin_url( 'admin.php?page=views' ). '"' );
				?>
				</p>
				<?php
			} else {
				?>
				<p>
				<?php
					_e( 'There are no Views defined.', 'wpv-views' );
				?>
				</p>
				<?php
			}
        }
    }
    
    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $new_instance = wp_parse_args( ( array ) $new_instance, 
            array( 
                'title' => '',
                'view'  => false
            ) 
        );
        $instance['title'] = strip_tags( $new_instance['title']) ;
        $instance['view']  = $new_instance['view'];
        return $instance;
    }
    
}

/**
* WPV_Widget_filter
*
* Displays only the filter section of a View
*
* @since unknown
*/

class WPV_Widget_filter extends WP_Widget {
	
	public function __construct( $id_base = 'wp_views_filter', $name = 'WP Views Filter', $widget_options = array(), $control_options = array() ) {
		$this->id_base = 'wp_views_filter';
		$this->name = __( 'WP Views Filter', 'wpv-views' );
		$this->option_name = 'widget_' . $this->id_base;
		$this->widget_options = array(
			'classname' => 'widget_wp_views_filter',
			'description' => __( 'Displays the filter section of a View.', 'wpv-views' ) 
		);
		$this->control_options = wp_parse_args( $control_options, array('id_base' => $this->id_base) );
		parent::__construct( $this->id_base, $this->name, $this->widget_options, $this->control_options );
	}
    
    function widget( $args, $instance ) {
        global $WP_Views;
        extract( $args );
        $instance = wp_parse_args( (array) $instance,
            array( 
                'title' => '',
                'view'  => false,
				'target_id' => false
            ) 
        );
		if ( $instance['view'] && $instance['target_id'] ) {
			$WP_Views->set_widget_view_id( $instance['view'] );
			$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
			echo $before_widget;
			if ( $title ) {
				echo $before_title . $title . $after_title;
			}
			$atts = array();
			$atts['id'] = $instance['view'];
			$atts['target_id'] = $instance['target_id'];
			$out = $WP_Views->short_tag_wpv_view_form( $atts );
			$out = wpv_do_shortcode( $out );
			$post_type_object = get_post_type_object( 'view' );
			if ( 
				! $WP_Views->is_embedded() 
				&& current_user_can( $post_type_object->cap->edit_post, $instance['view'] ) 
			) {
				$out .= widget_view_link( $instance['view'] );
			}
			echo $out;
			echo $after_widget;
			$WP_Views->set_widget_view_id( 0 );
		}
    }
    
    function form( $instance ) {
        global $WP_Views;
        $views = wpv_check_views_exists( 'normal', array( 'post_status' =>'publish', 'orderby' => 'post_title' ) );
        $view_forms = array();
		if ( $views ) {
			foreach ( $views as $vi ) {
				 if ( $WP_Views->does_view_have_form_control_with_submit( $vi ) ) {
					$view_forms[] = $vi;
				 }
			}
		}
        $instance = wp_parse_args( (array) $instance, 
            array( 
                'title' => '',
                'view'  => false,
				'target_id' => ''
            ) 
        );
        $title = $instance['title'];
        $view  = $instance['view'];
		$target_id = $instance['target_id'];
		$target_title = '';
		if ( $target_id != '' ) {
			$target_title = esc_attr( get_the_title( $target_id ) );
		}
        if ( count( $view_forms ) > 0 ) {
		?>
            <p>
				<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'wpv-views' ); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
			</p>
            <p>
				<label for="<?php echo $this->get_field_id( 'view' ); ?>"><?php _e( 'View containing the form:', 'wpv-views' ); ?></label>
				<select id="<?php echo $this->get_field_id( 'view' ); ?>" name="<?php echo $this->get_field_name( 'view' ); ?>" class="widefat js-wpv-view-form-id">
				<?php foreach ( $view_forms as $v ) : ?>
					<option value="<?php echo $v ?>"<?php if ( $view == $v ) : ?> selected="selected"<?php endif;?>><?php echo esc_html( get_the_title( $v ) ) ?></option>
				<?php endforeach;?>             
				</select>
				<span class="desc wpv-helper-text"><?php _e( 'Remember that only Views forms containing a <em>Submit</em> button can be used on a widget', 'wpv-views' ); ?></span>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('target_title'); ?>"><?php _e('Target page to show the results:', 'wpv-views'); ?></label>				
				<input type="text" id="<?php echo $this->get_field_id('target_title'); ?>" name="<?php echo $this->get_field_name('target_title'); ?>" value="<?php echo $target_title; ?>" class="widefat js-wpv-widget-form-target-suggest-title" placeholder="<?php echo esc_attr( __( 'Please type', 'wpv-views' ) ); ?>" />
				<input type="hidden" value="<?php echo $target_id; ?>" id="<?php echo $this->get_field_id( 'target_id' ); ?>" name="<?php echo $this->get_field_name( 'target_id' ); ?>" class="widefat js-wpv-widget-form-target-id" />
				<input type="hidden" name="wpv-target-customizer-helper" class="js-wpv-target-customizer-helper" />
			</p>
			<p class="toolset-alert toolset-error js-wpv-incomplete-setup-box" style="display:none">
				<?php _e( 'Setup incomplete. Please select the page where you would like to show the search results.', 'wpv-views' ); ?>
			</p>
			<div class="js-wpv-check-target-setup-box" style="display:none;background:#ddd;margin: 5px 0;padding: 5px 10px 10px;">
				<?php _e( 'Be sure to complete the setup:', 'wpv-views' ); ?><br />
				<a href="#" target="_blank" class="button-primary js-wpv-check-target-setup-link" data-editurl="<?php echo admin_url( 'post.php' ); ?>?post="><?php _e( 'Check the results page', 'wpv-views' ); ?></a>
				<a href="#" class="button-secondary js-wpv-discard-target-setup-link"><?php _e( 'Not now', 'wpv-views' ); ?></a>
			</div>
        <?php 
		} else {
			if ( ! $WP_Views->is_embedded() ) {
				?>
				<p>
				<?php
					printf( __( 'There are no Views with a parametric search. You can add them <a%s>here</a>.', 'wpv-views' ), ' href="' . admin_url( 'admin.php?page=views' ). '"' );
				?>
				</p>
				<p>
				<?php
					_e( 'Remember that only Views forms containing a <em>Submit</em> button can be used on a widget', 'wpv-views' );
				?>
				</p>
				<?php
			} else {
				?>
				<p>
				<?php
					_e( 'There are no Views with a parametric search.', 'wpv-views' );
				?>
				</p>
				<?php
			}
        }
    }
    
    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $new_instance = wp_parse_args( ( array ) $new_instance, 
            array( 
                'title' => '',
                'view'  => false,
				'target_id' => '0',
				'target_title' => ''
            ) 
        );
        $instance['title'] = strip_tags( $new_instance['title'] );
        $instance['view']  = $new_instance['view'];
        $instance['target_id']  = $new_instance['target_id'];
		$instance['target_title'] = strip_tags( $new_instance['target_title'] );
        return $instance;
    }
    
}
  

function widget_view_link( $view_id ) {

    $link = '';	
    
    global $WPV_settings;
	if ( $WPV_settings->wpv_show_edit_view_link == 1 ) {
		$link = '<a href="'. admin_url() .'admin.php?page=views-editor&view_id=' . $view_id . '" title="' . __( 'Edit view', 'wpv-views' ) . '">' . __( 'Edit view', 'wpv-views' ) . ' "' . get_the_title( $view_id ) . '"</a>';
		$link = apply_filters( 'wpv_edit_view_link', $link );
	}
    
	return $link;
    
}

/**
* COMPATIBILITY
*/

/**
* wpv_wpddl_register_layouts_widget_cell_scripts
*
* Registers the needed scripts for compatibility between the Views Filter widget and the Layouts widget cell
*
* @param $scripts array
*
* @return $scripts array
*
* @since 1.7
*/

add_filter( 'wpdll_cell_widget_scripts', 'wpv_wpddl_register_layouts_widget_cell_scripts' );

function wpv_wpddl_register_layouts_widget_cell_scripts( $scripts ) {
	$scripts[] = array( 'views-widgets-gui-script' , WPV_URL_EMBEDDED . '/res/js/views_widgets_gui.js', array( 'jquery','suggest' ), WPV_VERSION, true );
	return $scripts;
}
  
?>