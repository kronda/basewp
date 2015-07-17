<?php

$show_thumbs = FLTheme::get_setting('fl-posts-show-thumbs');

?>
<article class="fl-post" id="fl-post-<?php the_ID(); ?>" itemscope="itemscope" itemtype="http://schema.org/BlogPosting">

	<header class="fl-post-header">
		<h1 class="fl-post-title" itemprop="headline">
			<?php the_title(); ?>
			<?php edit_post_link( _x( 'Edit', 'Edit post link text.', 'fl-automator' ) ); ?>
		</h1>
		<?php FLTheme::post_top_meta(); ?>
	</header><!-- .fl-post-header -->

	<?php if(has_post_thumbnail() && !empty($show_thumbs)) : ?>
		<?php if($show_thumbs == 'above') : ?>
		<div class="fl-post-thumb">
			<?php the_post_thumbnail('large', array('itemprop' => 'image')); ?>
		</div>
		<?php else : ?>
		<div class="row">
			<div class="col-md-3 col-sm-3">
				<div class="fl-post-thumb">
					<?php the_post_thumbnail('thumbnail', array('itemprop' => 'image')); ?>
				</div>
			</div>
			<div class="col-md-9 col-sm-9">
		<?php endif; ?>
	<?php endif; ?>

	<div class="fl-post-content clearfix" itemprop="text">
		<?php

		the_content();

		wp_link_pages( array(
			'before'         => '<div class="fl-post-page-nav">' . _x( 'Pages:', 'Text before page links on paginated post.', 'fl-automator' ),
			'after'          => '</div>',
			'next_or_number' => 'number'
		) );

		?>
	</div><!-- .fl-post-content -->

	<?php if(has_post_thumbnail() && $show_thumbs == 'beside') : ?>
		</div>
	</div>
	<?php endif; ?>

	<?php FLTheme::post_bottom_meta(); ?>
	<?php FLTheme::post_navigation(); ?>
	<?php comments_template(); ?>

</article>
<!-- .fl-post -->