<?php
/**
 * @package Make Plus
 */

global $ttfmake_section_data, $ttfmake_sections;

// The data in $ttfmake_section_data is already sanitized
$d = $ttfmake_section_data;

// Filter the title for front-end output
$title = apply_filters( 'the_title', $d['title'] );

// Build shortcode parameters
$parameters = '';
$keys = array(
	'columns', 'type',
	'sortby', 'keyword', 'count', 'offset',
	'taxonomy', 'show-title', 'show-date',
	'show-excerpt', 'show-author', 'show-categories',
	'show-tags', 'show-comments', 'thumbnail',
);
foreach ( $keys as $key ) {
	if ( isset( $d[$key] ) ) {
		$param = str_replace( '-', '_', $key );
		$value = esc_attr( $d[$key] );
		$parameters .= " $param=\"$value\"";
	}
}
?>

<section id="builder-section-<?php echo esc_attr( $ttfmake_section_data['id'] ); ?>" class="builder-section <?php echo esc_attr( ttfmake_get_builder_save()->section_classes( $ttfmake_section_data, $ttfmake_sections ) ); ?>">
	<?php if ( '' !== $title ) : ?>
	<header class="builder-section-header">
		<h3 class="builder-post-list-section-title">
			<?php echo $title; ?>
		</h3>
	</header>
	<?php endif; ?>
	<div class="builder-section-content container">
		[ttfmp_post_list<?php echo $parameters; ?>]
	</div>
</section>