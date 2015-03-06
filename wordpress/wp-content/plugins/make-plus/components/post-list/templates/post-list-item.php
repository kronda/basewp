<?php
/**
 * @package Make Plus
 */

global $post, $ttfmp_data;

// Are we rendering a widget?
$is_widget = ( isset( $ttfmp_data['is-widget'] ) && true === $ttfmp_data['is-widget'] );

// Thumbnail
$thumbnail = trim( $ttfmp_data['thumbnail'] );
$thumbnail_size = apply_filters( 'ttfmp_posts_list_thumbnail_size', 'large', $ttfmp_data );

// True/false options
$display_keys = array(
	'show-title', 'show-date',
	'show-excerpt', 'show-author',
	'show-categories', 'show-tags',
	'show-comments',
);
$d = array();
foreach ( $display_keys as $key ) {
	$d[$key] = ( isset( $ttfmp_data[$key] ) ) ? absint( $ttfmp_data[$key] ) : 0;
}

// Title element
$t_wrap = 'h3';
if ( $is_widget || in_array( $thumbnail, array( 'left', 'right' ) ) ) {
	$t_wrap = 'strong';
}
?>

<?php if ( 'none' !== $thumbnail || $d['show-title'] || $d['show-date'] || $d['show-author'] ) : ?>
<header class="ttfmp-post-list-item-header">
	<?php if ( 'none' !== $thumbnail ) : ?>
	<figure class="ttfmp-post-list-item-thumb position-<?php echo esc_attr( $thumbnail ); ?>">
		<a href="<?php the_permalink(); ?>">
			<?php the_post_thumbnail( $thumbnail_size ); ?>
		</a>
	</figure>
	<?php endif; ?>
	<?php if ( $d['show-title'] ) : ?>
	<<?php echo $t_wrap; ?> class="ttfmp-post-list-item-title">
		<a href="<?php the_permalink(); ?>">
			<?php the_title(); ?>
		</a>
	</<?php echo $t_wrap; ?>>
	<?php endif; ?>
	<?php if ( $d['show-date'] ) : ?>
	<span class="ttfmp-post-list-item-date">
		<a href="<?php the_permalink(); ?>" rel="bookmark">
			<?php echo get_the_date(); ?>
		</a>
	</span>
	<?php endif; ?>
</header>
<?php endif; ?>
<?php if ( $d['show-excerpt'] ) : ?>
<div class="ttfmp-post-list-item-content">
	<?php the_excerpt(); ?>
</div>
<?php endif; ?>
<?php if ( $d['show-author'] ) : ?>
<span class="ttfmp-post-list-item-author">
	<?php echo esc_html( get_the_author_meta( 'display_name' ) ); ?>
</span>
<?php endif; ?>
<?php if ( $d['show-categories'] || $d['show-tags'] || $d['show-comments'] ) : ?>
<footer class="ttfmp-post-list-item-footer">
	<?php
	$categorized_blog = true;
	if ( function_exists( 'ttfmake_categorized_blog' ) ) :
		$categorized_blog = ttfmake_categorized_blog();
	endif;
	if ( ( $d['show-categories'] && has_category() && $categorized_blog ) || ( $d['show-tags'] || has_tag() ) ) : ?>
		<?php
		$category_list   = get_the_category_list();
		$tag_list        = get_the_tag_list( '<ul class="post-tags"><li>', "</li>\n<li>", '</li></ul>' ); // Replicates category output
		$taxonomy_output = '';

		// Categories
		if ( $d['show-categories'] && $category_list ) :
			$taxonomy_output .= __( '<i class="fa fa-file"></i> ', 'make-plus' ) . '%1$s';
		endif;

		// Tags
		if ( $d['show-tags'] && $tag_list ) :
			$taxonomy_output .= __( '<i class="fa fa-tag"></i> ', 'make-plus' ) . '%2$s';
		endif;

		// Output
		printf(
			$taxonomy_output,
			$category_list,
			$tag_list
		);
		?>
	<?php endif; ?>
	<?php if ( $d['show-comments'] && ( comments_open() || get_comments_number() > 0 ) ) : ?>
	<a class="ttfmp-post-list-item-comment-link" href="<?php the_permalink(); ?>">
		<?php
		printf(
			_n( '%d comment', '%d comments', get_comments_number(), 'make-plus' ),
			number_format_i18n( get_comments_number() )
		);
		?>
	</a>
	<?php endif; ?>
</footer>
<?php endif; ?>
