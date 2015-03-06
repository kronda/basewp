<?php
/**
 * @package Make Plus
 */

global $ttfmake_section_data, $ttfmake_sections;

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
$data = wp_parse_args( $ttfmake_section_data, $defaults );

// Sanitize all the data
$title = apply_filters( 'the_title', $data['title'] );
$columns = ttfmake_sanitize_section_choice( $data['columns'], 'columns', 'edd-downloads' );
$taxonomy = ttfmake_sanitize_section_choice( $data['taxonomy'], 'taxonomy', 'edd-downloads' );
$sortby = ttfmake_sanitize_section_choice( $data['sortby'], 'sortby', 'edd-downloads' );
$count = (int) $data['count'];
$thumb = ( absint( $data['thumb'] ) ) ? 'true' : 'false';
$price = ( absint( $data['price'] ) ) ? 'yes' : 'no';
$addcart = ( absint( $data['addcart'] ) ) ? 'yes' : 'no';
$details = ttfmake_sanitize_section_choice( $data['details'], 'details', 'edd-downloads' );

// Parse taxonomy option
$taxonomy_att = '';
if ( 'all' !== $taxonomy ) {
	$term = explode( '_', $taxonomy, 2 );
	if ( 'cat' === $term[0] ) {
		$taxonomy_att = " category=\"{$term[1]}\"";
	} else if ( 'tag' === $term[0] ) {
		$taxonomy_att = " tags=\"{$term[1]}\"";
	}
}

// Parse sortby option
$sortby_att = ' order="DESC" orderby="post_date"';
if ( 'post_date-desc' !== $sortby ) {
	$sort = explode( '-', $sortby, 2 );
	if ( ! isset( $sort[1] ) ) {
		$sortby_att = " orderby=\"{$sort[0]}\"";
	} else {
		$sort[1] = strtoupper( $sort[1] );
		$sortby_att = " order=\"{$sort[1]}\" orderby=\"{$sort[0]}\"";
	}
}

// Parse details option
$details_att = ' excerpt="no" full_content="no"';
if ( 'none' !== $details ) {
	if ( 'full' === $details ) {
		$details_att = ' excerpt="no" full_content="yes"';
	} else if ( 'excerpt' === $details ) {
		$details_att = ' excerpt="yes" full_content="no"';
	}
}
?>

<section id="builder-section-<?php echo esc_attr( $ttfmake_section_data['id'] ); ?>" class="builder-section <?php echo esc_attr( ttfmake_get_builder_save()->section_classes( $ttfmake_section_data, $ttfmake_sections ) ); ?>">
	<div class="container">
		<?php if ( '' !== $data['title'] ) : ?>
			<header class="builder-section-header">
				<h3 class="builder-edd-downloads-section-title">
					<?php echo $title; ?>
				</h3>
			</header>
		<?php endif; ?>
		<div class="builder-section-content">
			[downloads columns="<?php echo $columns; ?>" number="<?php echo $count; ?>" thumbnails="<?php echo $thumb; ?>" price="<?php echo $price; ?>" buy_button="<?php echo $addcart; ?>"<?php echo $taxonomy_att; echo $sortby_att; echo $details_att; ?>]
		</div>
	</div>
</section>