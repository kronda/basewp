<?php
/**
 * @package Make Plus
 */

ttfmake_load_section_header();

global $ttfmake_section_data, $ttfmake_is_js_template;
$section_name = ttfmake_get_section_name( $ttfmake_section_data, $ttfmake_is_js_template );

$defaults = array(
	'title' => ttfmake_get_section_default( 'title', 'edd-downloads' ),
	'columns' => ttfmake_get_section_default( 'columns', 'edd-downloads' ),
	'taxonomy' => ttfmake_get_section_default( 'taxonomy', 'edd-downloads' ),
	'sortby' => ttfmake_get_section_default( 'sortby', 'edd-downloads' ),
	'count' => ttfmake_get_section_default( 'count', 'edd-downloads' ),
	'thumb' => ttfmake_get_section_default( 'thumb', 'edd-downloads' ),
	'price' => ttfmake_get_section_default( 'price', 'edd-downloads' ),
	'addcart' => ttfmake_get_section_default( 'addcart', 'edd-downloads' ),
	'details' => ttfmake_get_section_default( 'details', 'edd-downloads' ),
);
$data = wp_parse_args( $ttfmake_section_data['data'], $defaults );
?>

	<div class="ttfmake-titlediv">
		<div class="ttfmake-titlewrap">
			<input placeholder="<?php esc_attr_e( 'Enter title here' ); ?>" type="text" name="<?php echo $section_name; ?>[title]" class="ttfmake-title ttfmake-section-header-title-input" value="<?php echo esc_attr( htmlspecialchars( $data['title'] ) ); ?>" autocomplete="off" />
		</div>
	</div>

	<div class="ttfmake-edd-downloads-options-container">
		<div class="ttfmake-taxonomy-select-wrapper">
			<h4><?php _e( 'From', 'make-plus' ); ?></h4>
			<select id="<?php echo $section_name; ?>[taxonomy]" name="<?php echo $section_name; ?>[taxonomy]">
				<?php foreach ( ttfmake_get_section_choices( 'taxonomy', 'edd-downloads' ) as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>"<?php selected( $value, $data['taxonomy'] ); ?><?php if ( false !== strpos( $value, 'ttfmp-disabled' ) ) echo ' disabled="disabled"' ?>>
						<?php echo esc_html( $label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>

		<div class="ttfmake-sortby-select-wrapper">
			<h4><?php _e( 'Sort', 'make-plus' ); ?></h4>
			<select id="<?php echo $section_name; ?>[sortby]" name="<?php echo $section_name; ?>[sortby]">
				<?php foreach ( ttfmake_get_section_choices( 'sortby', 'edd-downloads' ) as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>"<?php selected( $value, $data['sortby'] ); ?>>
						<?php echo esc_html( $label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>
	</div>

	<div class="ttfmake-edd-downloads-options-container">
		<div class="ttfmake-columns-select-wrapper">
			<h4><?php _e( 'Columns', 'make-plus' ); ?></h4>
			<select id="<?php echo $section_name; ?>[columns]" name="<?php echo $section_name; ?>[columns]">
				<?php foreach ( ttfmake_get_section_choices( 'columns', 'edd-downloads' ) as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>"<?php selected( $value, $data['columns'] ); ?>>
						<?php echo esc_html( $label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>

		<h4 class="ttfmake-edd-downloads-options-title">
			<?php _e( 'Number to show', 'make-plus' ); ?>
		</h4>
		<input id="<?php echo $section_name; ?>[count]" class="code" type="number" name="<?php echo $section_name; ?>[count]" value="<?php echo esc_attr( $data['count'] ); ?>" />
		<p><?php _e( 'To show all, set to <code>-1</code>.', 'make-plus' ); ?></p>
	</div>

	<div class="ttfmake-edd-downloads-options-container">
		<h4><?php _e( 'Display', 'make-plus' ); ?></h4>
		<p>
			<input id="<?php echo $section_name; ?>[thumb]" type="checkbox" name="<?php echo $section_name; ?>[thumb]" value="1"<?php checked( absint( $data['thumb'] ) ); ?> />
			<label for="<?php echo $section_name; ?>[thumb]">
				<?php _e( 'Show thumbnail image', 'make-plus' ); ?>
			</label>
		</p>
		<p>
			<input id="<?php echo $section_name; ?>[price]" type="checkbox" name="<?php echo $section_name; ?>[price]" value="1"<?php checked( absint( $data['price'] ) ); ?> />
			<label for="<?php echo $section_name; ?>[price]">
				<?php _e( 'Show price', 'make-plus' ); ?>
			</label>
		</p>
		<p>
			<input id="<?php echo $section_name; ?>[addcart]" type="checkbox" name="<?php echo $section_name; ?>[addcart]" value="1"<?php checked( absint( $data['addcart'] ) ); ?> />
			<label for="<?php echo $section_name; ?>[addcart]">
				<?php _e( 'Show purchase button', 'make-plus' ); ?>
			</label>
		</p>

		<div class="ttfmake-details-select-wrapper">
			<h4><?php _e( 'Details', 'make-plus' ); ?></h4>
			<select id="<?php echo $section_name; ?>[details]" name="<?php echo $section_name; ?>[details]">
				<?php foreach ( ttfmake_get_section_choices( 'details', 'edd-downloads' ) as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>"<?php selected( $value, $data['details'] ); ?>>
						<?php echo esc_html( $label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>
	</div>

	<div class="clear"></div>

	<input type="hidden" class="ttfmake-section-state" name="<?php echo $section_name; ?>[state]" value="<?php if ( isset( $data['state'] ) ) echo esc_attr( $data['state'] ); else echo 'open'; ?>" />

<?php ttfmake_load_section_footer(); ?>