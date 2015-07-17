<article class="fl-post" id="fl-post-<?php the_ID(); ?>">

	<header class="fl-post-header">
		<h2 class="fl-post-title"><?php _e('Nothing Found', 'fl-automator'); ?></h2>
	</header><!-- .fl-post-header -->

	<div class="fl-post-content clearfix">
		<?php if (is_search()) : ?>

			<p><?php _e('Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'fl-automator'); ?></p>
			<?php get_search_form(); ?>

		<?php else : ?>

			<p><?php _e( "It seems we can't find what you're looking for. Perhaps searching can help.", 'fl-automator' ); ?></p>
			<?php get_search_form(); ?>

		<?php endif; ?>
	</div><!-- .fl-post-content -->

</article>
<!-- .fl-post -->