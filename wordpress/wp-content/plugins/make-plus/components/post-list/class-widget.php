<?php
/**
 * @package Make Plus
 */

if ( ! class_exists( 'TTFMP_Post_List_Widget' ) ) :
/**
 * Class TTFMP_Post_List_Widget
 */
class TTFMP_Post_List_Widget extends WP_Widget {
	/**
	 * Set up the widget properties.
	 *
	 * @since 1.2.0.
	 *
	 * @return TTFMP_Post_List_Widget
	 */
	public function __construct() {
		$widget_ops = array(
			'classname' => 'ttfmp-widget-post-list',
			'description' => sprintf( __( 'List posts or pages based on specific criteria. (%s)', 'make-plus' ), 'Make Plus' )
		);
		parent::__construct( 'ttfmp-post-list', __( 'Posts List', 'make-plus' ), $widget_ops );
	}

	/**
	 * Render the widget on the front end.
	 *
	 * @since 1.2.0.
	 *
	 * @param  array    $args        The configuration for this type of widget.
	 * @param  array    $instance    The options for the widget instance.
	 * @return void
	 */
	public function widget( $args, $instance ) {
		// Add widget marker to data that is passed to the template
		$instance['is-widget'] = true;

		// Only proceed if there is something to output
		$title = apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base );
		$instance['columns'] = 1;
		$query = ttfmp_get_post_list()->build_query( $instance );
		$content = ttfmp_get_post_list()->render( $query, $instance );
		if ( ! $title && ! $content ) {
			return;
		}

		// Before widget
		echo $args['before_widget'];

		// Widget title
		if ( $title ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		// Widget content
		echo $content;

		// After widget
		echo $args['after_widget'];
	}

	/**
	 * Sanitize and save the widget options.
	 *
	 * @since 1.2.0.
	 *
	 * @param  array    $new_instance    The current widget options.
	 * @param  array    $old_instance    The previous widget options (unused).
	 * @return array                     The sanitized current widget options.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = ttfmp_post_list_get_section_definitions()->save_post_list( $new_instance );
		if ( isset( $instance['columns'] ) ) {
			unset( $instance['columns'] );
		}
		return $instance;
	}

	/**
	 * Render the form for configuring the widget options.
	 *
	 * @since 1.2.0.
	 *
	 * @param  array    $instance    The current widget options.
	 * @return void
	 */
	public function form( $instance ) {
		$defaults = array(
			'title' => '',
			'type' => ttfmake_get_section_default( 'type', 'post-list' ),
			'sortby' => ttfmake_get_section_default( 'sortby', 'post-list' ),
			'keyword' => ttfmake_get_section_default( 'keyword', 'post-list' ),
			'count' => 3,
			'offset' => ttfmake_get_section_default( 'offset', 'post-list' ),
			'taxonomy' => ttfmake_get_section_default( 'taxonomy', 'post-list' ),
			'show-title' => ttfmake_get_section_default( 'show-title', 'post-list' ),
			'show-date' => ttfmake_get_section_default( 'show-date', 'post-list' ),
			'show-excerpt' => 0,
			'show-author' => ttfmake_get_section_default( 'show-author', 'post-list' ),
			'show-categories' => 0,
			'show-tags' => 0,
			'show-comments' => ttfmake_get_section_default( 'show-comments', 'post-list' ),
			'thumbnail' => 'left',
		);
		$instance = wp_parse_args( (array) $instance, $defaults );

		$instance['title'] = esc_attr( $instance['title'] );
		$instance['keyword'] = esc_attr( $instance['keyword'] );
		$instance['count'] = (int) $instance['count'];
		if ( $instance['count'] < -1 ) {
			$instance['count'] = abs( $instance['count'] );
		}
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $instance['title']; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'type' ); ?>"><?php _e( 'Type:', 'make-plus' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'type' ); ?>" id="<?php echo $this->get_field_id( 'type' ); ?>" class="widefat">
				<?php foreach ( ttfmake_get_section_choices( 'type', 'post-list' ) as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>"<?php selected( $value, $instance['type'] ); ?>>
						<?php echo esc_html( $label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'sortby' ); ?>"><?php _e( 'Sort by:', 'make-plus' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'sortby' ); ?>" id="<?php echo $this->get_field_id( 'sortby' ); ?>" class="widefat">
				<?php foreach ( ttfmake_get_section_choices( 'sortby', 'post-list' ) as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>"<?php selected( $value, $instance['sortby'] ); ?>>
						<?php echo esc_html( $label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'keyword' ); ?>"><?php _e( 'Keyword:' ); ?></label> <input class="widefat" id="<?php echo $this->get_field_id( 'keyword' ); ?>" name="<?php echo $this->get_field_name( 'keyword' ); ?>" type="text" value="<?php echo $instance['keyword']; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'count' ); ?>"><?php _e( 'Number of items to show:' ); ?></label> <input class="widefat" id="<?php echo $this->get_field_id( 'count' ); ?>" name="<?php echo $this->get_field_name( 'count' ); ?>" type="number" value="<?php echo $instance['count']; ?>" />
			<small style="display: block; padding-top: 5px;"><?php _e( 'To show all items, set to <code>-1</code>.', 'make-plus' ); ?></small>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'offset' ); ?>"><?php _e( 'Item offset:' ); ?></label> <input class="widefat" id="<?php echo $this->get_field_id( 'offset' ); ?>" name="<?php echo $this->get_field_name( 'offset' ); ?>" type="number" value="<?php echo $instance['offset']; ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'taxonomy' ); ?>"><?php _e( 'From:', 'make-plus' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'taxonomy' ); ?>" id="<?php echo $this->get_field_id( 'taxonomy' ); ?>" class="widefat">
				<?php foreach ( ttfmake_get_section_choices( 'taxonomy', 'post-list' ) as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>"<?php selected( $value, $instance['taxonomy'] ); ?>>
						<?php echo esc_html( $label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</p>

		<p style="margin-bottom: 0px;"><label><?php _e( 'Item display:', 'make-plus' ); ?></label></p>

		<p style="margin-top: 1em;">
			<input class="checkbox" type="checkbox" <?php checked( $instance['show-title'], true ) ?> id="<?php echo $this->get_field_id( 'show-title' ); ?>" name="<?php echo $this->get_field_name( 'show-title' ); ?>" value="1" />
			<label for="<?php echo $this->get_field_id( 'show-title' ); ?>"><?php _e('Show item title'); ?></label>
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['show-date'], true ) ?> id="<?php echo $this->get_field_id( 'show-date' ); ?>" name="<?php echo $this->get_field_name( 'show-date' ); ?>" value="1" />
			<label for="<?php echo $this->get_field_id( 'show-date' ); ?>"><?php _e('Show date'); ?></label>
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['show-excerpt'], true ) ?> id="<?php echo $this->get_field_id( 'show-excerpt' ); ?>" name="<?php echo $this->get_field_name( 'show-excerpt' ); ?>" value="1" />
			<label for="<?php echo $this->get_field_id( 'show-excerpt' ); ?>"><?php _e('Show excerpt'); ?></label>
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['show-author'], true ) ?> id="<?php echo $this->get_field_id( 'show-author' ); ?>" name="<?php echo $this->get_field_name( 'show-author' ); ?>" value="1" />
			<label for="<?php echo $this->get_field_id( 'show-author' ); ?>"><?php _e('Show author'); ?></label>
		</p>
		<p>
			<input class="checkbox" type="checkbox" <?php checked( $instance['show-comments'], true ) ?> id="<?php echo $this->get_field_id( 'show-comments' ); ?>" name="<?php echo $this->get_field_name( 'show-comments' ); ?>" value="1" />
			<label for="<?php echo $this->get_field_id( 'show-comments' ); ?>"><?php _e('Show comment count'); ?></label>
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'thumbnail' ); ?>"><?php _e( 'Show thumbnail:', 'make-plus' ); ?></label>
			<select name="<?php echo $this->get_field_name( 'thumbnail' ); ?>" id="<?php echo $this->get_field_id( 'thumbnail' ); ?>" class="widefat">
				<?php foreach ( ttfmake_get_section_choices( 'thumbnail', 'post-list' ) as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>"<?php selected( $value, $instance['thumbnail'] ); ?>>
						<?php echo esc_html( $label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</p>
	<?php
	}
}
endif;