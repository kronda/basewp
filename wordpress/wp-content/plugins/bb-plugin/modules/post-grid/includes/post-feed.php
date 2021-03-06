<div class="fl-post-feed-post<?php if($settings->image_position == 'beside') echo ' fl-post-feed-image-beside'; if(has_post_thumbnail() && $settings->show_image) echo ' fl-post-feed-has-image'; ?>" itemscope="itemscope" itemtype="http://schema.org/BlogPosting">

	<div class="fl-post-feed-header">
		<h2 class="fl-post-feed-title" itemprop="headline">
			<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>
		</h2>
		<?php if($settings->show_author || $settings->show_date || $settings->show_comments) : ?>
		<div class="fl-post-feed-meta">
			<?php if($settings->show_author) : ?>
				<span class="fl-post-feed-author" itemprop="author" itemscope="itemscope" itemtype="http://schema.org/Person">
					<?php

					printf(
						_x( 'By %s', '%s stands for author name.', 'fl-builder' ),
						'<a href="' . get_author_posts_url( get_the_author_meta( 'ID' ) ) . '" itemprop="url"><span itemprop="name">' . get_the_author_meta( 'display_name', get_the_author_meta( 'ID' ) ) . '</span></a>'
					);

					?>
				</span>
			<?php endif; ?>
			<?php if($settings->show_date) : ?>
				<?php if($settings->show_author) : ?>
					<span class="fl-sep"> | </span>
				<?php endif; ?>
				<span class="fl-post-feed-date" itemprop="datePublished" datetime="<?php echo the_time('Y-m-d'); ?>">
					<?php FLBuilderLoop::post_date($settings->date_format); ?>
				</span>
			<?php endif; ?>
			<?php if($settings->show_comments) : ?>
				<?php if($settings->show_author || $settings->show_date) : ?>
					<span class="fl-sep"> | </span>
				<?php endif; ?>
				<span class="fl-post-feed-comments">
					<?php comments_popup_link('0 Comments', '1 Comment', '% Comments'); ?>
				</span>
			<?php endif; ?>
		</div>
		<?php endif; ?>
	</div>

	<?php if(has_post_thumbnail() && $settings->show_image) : ?>
	<div class="fl-post-feed-image">
		<a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">
			<?php the_post_thumbnail($settings->image_size, array('itemprop' => 'image')); ?>
		</a>
	</div>
	<?php endif; ?>

	<?php if($settings->show_content) : ?>
	<div class="fl-post-feed-content" itemprop="text">
		<?php 
		
		if ( 'full' == $settings->content_type ) {
			the_content();
		}
		else {
			the_excerpt(); 
		}
		
		?>
		<?php if($settings->show_more_link) : ?>
		<a class="fl-post-feed-more" href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php echo $settings->more_link_text; ?></a>
		<?php endif; ?>
	</div>
	<?php endif; ?>

	<div class="fl-clear"></div>
</div>