<?php
/**
 * @package Make Plus
 */

ttfmake_load_section_header();

global $ttfmake_section_data, $ttfmake_is_js_template;
$section_name = ttfmake_get_section_name( $ttfmake_section_data, $ttfmake_is_js_template );

$defaults = array(
	'title' => ttfmake_get_section_default( 'title', 'post-list' ),
	'columns' => ttfmake_get_section_default( 'columns', 'post-list' ),
	'type' => ttfmake_get_section_default( 'type', 'post-list' ),
	'sortby' => ttfmake_get_section_default( 'sortby', 'post-list' ),
	'keyword' => ttfmake_get_section_default( 'keyword', 'post-list' ),
	'count' => ttfmake_get_section_default( 'count', 'post-list' ),
	'offset' => ttfmake_get_section_default( 'offset', 'post-list' ),
	'taxonomy' => ttfmake_get_section_default( 'taxonomy', 'post-list' ),
	'show-title' => ttfmake_get_section_default( 'show-title', 'post-list' ),
	'show-date' => ttfmake_get_section_default( 'show-date', 'post-list' ),
	'show-excerpt' => ttfmake_get_section_default( 'show-excerpt', 'post-list' ),
	'show-author' => ttfmake_get_section_default( 'show-author', 'post-list' ),
	'show-categories' => ttfmake_get_section_default( 'show-categories', 'post-list' ),
	'show-tags' => ttfmake_get_section_default( 'show-tags', 'post-list' ),
	'show-comments' => ttfmake_get_section_default( 'show-comments', 'post-list' ),
	'thumbnail' => ttfmake_get_section_default( 'thumbnail', 'post-list' ),
);
$data = wp_parse_args( $ttfmake_section_data['data'], $defaults );
?>

	<div class="ttfmake-titlediv">
		<div class="ttfmake-titlewrap">
			<input placeholder="<?php esc_attr_e( 'Enter title here' ); ?>" type="text" name="<?php echo $section_name; ?>[title]" class="ttfmake-title ttfmake-section-header-title-input" value="<?php echo esc_attr( htmlspecialchars( $data['title'] ) ); ?>" autocomplete="off" />
		</div>
	</div>

	<div class="ttfmake-post-list-options-container">
		<div class="ttfmake-type-select-wrapper">
			<h4><?php _e( 'Type', 'make-plus' ); ?></h4>
			<select id="<?php echo $section_name; ?>[type]" name="<?php echo $section_name; ?>[type]">
				<?php foreach ( ttfmake_get_section_choices( 'type', 'post-list' ) as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>"<?php selected( $value, $data['type'] ); ?>>
						<?php echo esc_html( $label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>

		<div class="ttfmake-taxonomy-select-wrapper">
			<h4><?php _e( 'From', 'make-plus' ); ?></h4>
			<select id="<?php echo $section_name; ?>[taxonomy]" name="<?php echo $section_name; ?>[taxonomy]">
				<?php foreach ( ttfmake_get_section_choices( 'taxonomy', 'post-list' ) as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>"<?php selected( $value, $data['taxonomy'] ); ?><?php if ( false !== strpos( $value, 'ttfmp-disabled' ) ) echo ' disabled="disabled"' ?>>
						<?php echo esc_html( $label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>

		<div class="ttfmake-sortby-select-wrapper">
			<h4><?php _e( 'Sort', 'make-plus' ); ?></h4>
			<select id="<?php echo $section_name; ?>[sortby]" name="<?php echo $section_name; ?>[sortby]">
				<?php foreach ( ttfmake_get_section_choices( 'sortby', 'post-list' ) as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>"<?php selected( $value, $data['sortby'] ); ?>>
						<?php echo esc_html( $label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>

		<h4 class="ttfmake-post-list-options-title">
			<?php _e( 'Keyword', 'make-plus' ); ?>
		</h4>
		<input placeholder="<?php esc_attr_e( 'e.g. coffee', 'make-plus' ); ?>" id="<?php echo $section_name; ?>[keyword]" class="code" type="text" name="<?php echo $section_name; ?>[keyword]" value="<?php echo esc_attr( $data['keyword'] ); ?>" />
	</div>

	<div class="ttfmake-post-list-options-container">
		<div class="ttfmake-columns-select-wrapper">
			<h4><?php _e( 'Columns', 'make-plus' ); ?></h4>
			<select id="<?php echo $section_name; ?>[columns]" name="<?php echo $section_name; ?>[columns]">
				<?php foreach ( ttfmake_get_section_choices( 'columns', 'post-list' ) as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>"<?php selected( $value, $data['columns'] ); ?>>
						<?php echo esc_html( $label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>

		<h4 class="ttfmake-post-list-options-title">
			<?php _e( 'Number to show', 'make-plus' ); ?>
		</h4>
		<input id="<?php echo $section_name; ?>[count]" class="code" type="number" name="<?php echo $section_name; ?>[count]" value="<?php echo (int) $data['count']; ?>" />
		<p><?php _e( 'To show all, set to <code>-1</code>.', 'make-plus' ); ?></p>
		<h4 class="ttfmake-post-list-options-title">
			<?php _e( 'Item offset', 'make-plus' ); ?>
		</h4>
		<input id="<?php echo $section_name; ?>[offset]" class="code" type="number" name="<?php echo $section_name; ?>[offset]" value="<?php echo (int) $data['offset']; ?>" />
		<div class="ttfmake-thumbnail-select-wrapper">
			<h4><?php _e( 'Show thumbnail', 'make-plus' ); ?></h4>
			<select id="<?php echo $section_name; ?>[thumbnail]" name="<?php echo $section_name; ?>[thumbnail]">
				<?php foreach ( ttfmake_get_section_choices( 'thumbnail', 'post-list' ) as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>"<?php selected( $value, $data['thumbnail'] ); ?>>
						<?php echo esc_html( $label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>
	</div>

	<div class="ttfmake-post-list-options-container">
		<h4><?php _e( 'Display', 'make-plus' ); ?></h4>

		<p>
			<input id="<?php echo $section_name; ?>[show-title]" type="checkbox" name="<?php echo $section_name; ?>[show-title]" value="1"<?php checked( absint( $data['show-title'] ) ); ?> />
			<label for="<?php echo $section_name; ?>[show-title]">
				<?php _e( 'Show item title', 'make-plus' ); ?>
			</label>
		</p>
		<p>
			<input id="<?php echo $section_name; ?>[show-date]" type="checkbox" name="<?php echo $section_name; ?>[show-date]" value="1"<?php checked( absint( $data['show-date'] ) ); ?> />
			<label for="<?php echo $section_name; ?>[show-date]">
				<?php _e( 'Show date', 'make-plus' ); ?>
			</label>
		</p>
		<p>
			<input id="<?php echo $section_name; ?>[show-excerpt]" type="checkbox" name="<?php echo $section_name; ?>[show-excerpt]" value="1"<?php checked( absint( $data['show-excerpt'] ) ); ?> />
			<label for="<?php echo $section_name; ?>[show-excerpt]">
				<?php _e( 'Show excerpt', 'make-plus' ); ?>
			</label>
		</p>
		<p>
			<input id="<?php echo $section_name; ?>[show-author]" type="checkbox" name="<?php echo $section_name; ?>[show-author]" value="1"<?php checked( absint( $data['show-author'] ) ); ?> />
			<label for="<?php echo $section_name; ?>[show-author]">
				<?php _e( 'Show author', 'make-plus' ); ?>
			</label>
		</p>
		<p>
			<input id="<?php echo $section_name; ?>[show-categories]" type="checkbox" name="<?php echo $section_name; ?>[show-categories]" value="1"<?php checked( absint( $data['show-categories'] ) ); ?> />
			<label for="<?php echo $section_name; ?>[show-categories]">
				<?php _e( 'Show categories', 'make-plus' ); ?>
			</label>
		</p>
		<p>
			<input id="<?php echo $section_name; ?>[show-tags]" type="checkbox" name="<?php echo $section_name; ?>[show-tags]" value="1"<?php checked( absint( $data['show-tags'] ) ); ?> />
			<label for="<?php echo $section_name; ?>[show-tags]">
				<?php _e( 'Show tags', 'make-plus' ); ?>
			</label>
		</p>
		<p>
			<input id="<?php echo $section_name; ?>[show-comments]" type="checkbox" name="<?php echo $section_name; ?>[show-comments]" value="1"<?php checked( absint( $data['show-comments'] ) ); ?> />
			<label for="<?php echo $section_name; ?>[show-comments]">
				<?php _e( 'Show comment count', 'make-plus' ); ?>
			</label>
		</p>
	</div>

	<div class="clear"></div>

	<input type="hidden" class="ttfmake-section-state" name="<?php echo $section_name; ?>[state]" value="<?php if ( isset( $data['state'] ) ) echo esc_attr( $data['state'] ); else echo 'open'; ?>" />

<?php ttfmake_load_section_footer(); ?>