<?php
/**
 * @package Make Plus
 */

ttfmake_load_section_header();

global $ttfmake_section_data, $ttfmake_is_js_template;
$section_name = ttfmake_get_section_name( $ttfmake_section_data, $ttfmake_is_js_template );

$defaults = array(
	'title' => ttfmake_get_section_default( 'title', 'woocommerce-product-grid' ),
	'columns' => ttfmake_get_section_default( 'columns', 'woocommerce-product-grid' ),
	'type' => ttfmake_get_section_default( 'type', 'woocommerce-product-grid' ),
	'taxonomy' => ttfmake_get_section_default( 'taxonomy', 'woocommerce-product-grid' ),
	'sortby' => ttfmake_get_section_default( 'sortby', 'woocommerce-product-grid' ),
	'count' => ttfmake_get_section_default( 'count', 'woocommerce-product-grid' ),
	'thumb' => ttfmake_get_section_default( 'thumb', 'woocommerce-product-grid' ),
	'rating' => ttfmake_get_section_default( 'rating', 'woocommerce-product-grid' ),
	'price' => ttfmake_get_section_default( 'price', 'woocommerce-product-grid' ),
	'addcart' => ttfmake_get_section_default( 'addcart', 'woocommerce-product-grid' ),
);
$data = wp_parse_args( $ttfmake_section_data['data'], $defaults );
?>

	<div class="ttfmake-titlediv">
		<div class="ttfmake-titlewrap">
			<input placeholder="<?php esc_attr_e( 'Enter title here' ); ?>" type="text" name="<?php echo $section_name; ?>[title]" class="ttfmake-title ttfmake-section-header-title-input" value="<?php echo esc_attr( htmlspecialchars( $data['title'] ) ); ?>" autocomplete="off" />
		</div>
	</div>

	<div class="ttfmake-woocommerce-product-grid-options-container">
		<div class="ttfmake-type-select-wrapper">
			<h4><?php _e( 'Show', 'make-plus' ); ?></h4>
			<select id="<?php echo $section_name; ?>[type]" name="<?php echo $section_name; ?>[type]">
				<?php foreach ( ttfmake_get_section_choices( 'type', 'woocommerce-product-grid' ) as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>"<?php selected( $value, $data['type'] ); ?>>
						<?php echo esc_html( $label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>

		<div class="ttfmake-taxonomy-select-wrapper">
			<h4><?php _e( 'From', 'make-plus' ); ?></h4>
			<select id="<?php echo $section_name; ?>[taxonomy]" name="<?php echo $section_name; ?>[taxonomy]">
				<?php foreach ( ttfmake_get_section_choices( 'taxonomy', 'woocommerce-product-grid' ) as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>"<?php selected( $value, $data['taxonomy'] ); ?><?php if ( false !== strpos( $value, 'ttfmp-disabled' ) ) echo ' disabled="disabled"' ?>>
						<?php echo esc_html( $label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>

		<div class="ttfmake-sortby-select-wrapper">
			<h4><?php _e( 'Sort', 'make-plus' ); ?></h4>
			<select id="<?php echo $section_name; ?>[sortby]" name="<?php echo $section_name; ?>[sortby]">
				<?php foreach ( ttfmake_get_section_choices( 'sortby', 'woocommerce-product-grid' ) as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>"<?php selected( $value, $data['sortby'] ); ?>>
						<?php echo esc_html( $label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>
	</div>

	<div class="ttfmake-woocommerce-product-grid-options-container">
		<div class="ttfmake-columns-select-wrapper">
			<h4><?php _e( 'Columns', 'make-plus' ); ?></h4>
			<select id="<?php echo $section_name; ?>[columns]" name="<?php echo $section_name; ?>[columns]">
				<?php foreach ( ttfmake_get_section_choices( 'columns', 'woocommerce-product-grid' ) as $value => $label ) : ?>
					<option value="<?php echo esc_attr( $value ); ?>"<?php selected( $value, $data['columns'] ); ?>>
						<?php echo esc_html( $label ); ?>
					</option>
				<?php endforeach; ?>
			</select>
		</div>

		<h4 class="ttfmake-woocommerce-product-grid-options-title">
			<?php _e( 'Number to show', 'make-plus' ); ?>
		</h4>
		<input id="<?php echo $section_name; ?>[count]" class="code" type="number" name="<?php echo $section_name; ?>[count]" value="<?php echo (int) $data['count']; ?>" />
		<p><?php _e( 'To show all, set to <code>-1</code>.', 'make-plus' ); ?></p>
	</div>

	<div class="ttfmake-woocommerce-product-grid-options-container">
		<p>
			<h4><?php _e( 'Display', 'make-plus' ); ?></h4>
			<input id="<?php echo $section_name; ?>[thumb]" type="checkbox" name="<?php echo $section_name; ?>[thumb]" value="1"<?php checked( absint( $data['thumb'] ) ); ?> />
			<label for="<?php echo $section_name; ?>[thumb]">
				<?php _e( 'Show product image', 'make-plus' ); ?>
			</label>
		</p>

		<?php if ( get_option( 'woocommerce_enable_review_rating' ) !== 'no' ) : ?>
		<p>
			<input id="<?php echo $section_name; ?>[rating]" type="checkbox" name="<?php echo $section_name; ?>[rating]" value="1"<?php checked( absint( $data['rating'] ) ); ?> />
			<label for="<?php echo $section_name; ?>[rating]">
				<?php _e( 'Show rating', 'make-plus' ); ?>
			</label>
		</p>
		<?php endif; ?>

		<p>
			<input id="<?php echo $section_name; ?>[price]" type="checkbox" name="<?php echo $section_name; ?>[price]" value="1"<?php checked( absint( $data['price'] ) ); ?> />
			<label for="<?php echo $section_name; ?>[price]">
				<?php _e( 'Show price', 'make-plus' ); ?>
			</label>
		</p>

		<p>
			<input id="<?php echo $section_name; ?>[addcart]" type="checkbox" name="<?php echo $section_name; ?>[addcart]" value="1"<?php checked( absint( $data['addcart'] ) ); ?> />
			<label for="<?php echo $section_name; ?>[addcart]">
				<?php _e( 'Show <em>Add to cart</em> button', 'make-plus' ); ?>
			</label>
		</p>
	</div>

	<div class="clear"></div>

	<input type="hidden" class="ttfmake-section-state" name="<?php echo $section_name; ?>[state]" value="<?php if ( isset( $data['state'] ) ) echo esc_attr( $data['state'] ); else echo 'open'; ?>" />

<?php ttfmake_load_section_footer(); ?>