<?php
/**
 * @package Make Plus
 */

global $ttfmake_section_data, $ttfmake_sections;

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
$data = wp_parse_args( $ttfmake_section_data, $defaults );

// Sanitize all the data
$title = apply_filters( 'the_title', $data['title'] );
$columns = ttfmake_sanitize_section_choice( $data['columns'], 'columns', 'woocommerce-product-grid' );
$type = ttfmake_sanitize_section_choice( $data['type'], 'type', 'woocommerce-product-grid' );
$taxonomy = ttfmake_sanitize_section_choice( $data['taxonomy'], 'taxonomy', 'woocommerce-product-grid' );
$sortby = ttfmake_sanitize_section_choice( $data['sortby'], 'sortby', 'woocommerce-product-grid' );
$count = absint( $data['count'] );
$thumb = absint( $data['thumb'] );
$rating = absint( $data['rating'] );
$price = absint( $data['price'] );
$addcart = absint( $data['addcart'] );
?>

<section id="builder-section-<?php echo esc_attr( $ttfmake_section_data['id'] ); ?>" class="builder-section <?php echo esc_attr( ttfmake_get_builder_save()->section_classes( $ttfmake_section_data, $ttfmake_sections ) ); ?>">
	<?php if ( '' !== $data['title'] ) : ?>
	<header class="builder-section-header">
		<h3 class="builder-woocommerce-product-grid-section-title">
			<?php echo $title; ?>
		</h3>
	</header>
	<?php endif; ?>
	<div class="builder-section-content">
		[ttfmp_woocomerce_product_grid columns="<?php echo $columns; ?>" type="<?php echo $type; ?>" taxonomy="<?php echo $taxonomy; ?>" sortby="<?php echo $sortby; ?>" count="<?php echo $count; ?>" thumb="<?php echo $thumb; ?>" rating="<?php echo $rating; ?>" price="<?php echo $price; ?>" addcart="<?php echo $addcart; ?>"]
	</div>
</section>