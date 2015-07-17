<?php

$show_thumbs = FLTheme::get_setting('fl-archive-show-thumbs');
$show_full   = FLTheme::get_setting('fl-archive-show-full');
$more_text   = FLTheme::get_setting('fl-archive-readmore-text');

?>
<article class="fl-post" id="fl-post-<?php the_ID(); ?>" itemscope="itemscope" itemtype="http://schema.org/BlogPosting">

	<header class="fl-post-header">
		<h2 class="fl-post-title" itemprop="headline">
			<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
			<?php edit_post_link( _x( 'Edit', 'Edit post link text.', 'fl-automator' ) ); ?>
		</h2>
		<?php FLTheme::post_top_meta(); ?>
	</header><!-- .fl-post-header -->

	<?php if(has_post_thumbnail() && !empty($show_thumbs)) : ?>
		<?php if($show_thumbs == 'above') : ?>
		<div class="fl-post-thumb">
			<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">
				<?php the_post_thumbnail('large', array('itemprop' => 'image')); ?>
			</a>
		</div>
		<?php else : ?>
		<div class="row">
			<div class="col-md-3 col-sm-3">
				<div class="fl-post-thumb">
					<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">
						<?php the_post_thumbnail('thumbnail', array('itemprop' => 'image')); ?>
					</a>
				</div>
			</div>
			<div class="col-md-9 col-sm-9">
		<?php endif; ?>
	<?php endif; ?>

	<div class="fl-post-content clearfix" itemprop="text">
		<?php

		if(is_search() || !$show_full) {
			the_excerpt();
			echo '<a class="fl-post-more-link" href="'. get_permalink() .'">'. $more_text .'</a>';
		}
		else {
			the_content('<span class="fl-post-more-link">'. $more_text .'</span>');
		}

		?>
	</div><!-- .fl-post-content -->

	<?php FLTheme::post_bottom_meta(); ?>

	<?php if(has_post_thumbnail() && $show_thumbs == 'beside') : ?>
		</div>
	</div>
	<?php endif; ?>

</article>
<!-- .fl-post -->