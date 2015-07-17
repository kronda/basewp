<?php

if(!comments_open() && '0' == get_comments_number()) {
	return;
}
if(post_password_required()) {
	return;
}

?>
<div class="fl-comments">

	<?php if(have_comments()) : ?>
	<div class="fl-comments-list">

		<h3 class="fl-comments-list-title"><?php

			if ( $num_comments = get_comments_number() ) {

				printf(
					_nx( '1 Comment', '%d Comments', get_comments_number(), 'Comments list title.', 'fl-automator' ),
					number_format_i18n( $num_comments )
				);

			} else {

				_e( 'No Comments', 'fl-automator' );

			}

		?></h3>

		<ol id="comments">
		<?php wp_list_comments(array('callback' => 'FLTheme::display_comment')); ?>
		</ol>

		<?php if(get_comment_pages_count() > 1) : ?>
		<nav class="fl-comments-list-nav clearfix">
			<div class="fl-comments-list-prev"><?php previous_comments_link() ?></div>
			<div class="fl-comments-list-next"><?php next_comments_link() ?></div>
		</nav>
		<?php endif; ?>

	</div>
	<?php endif; ?>

	<?php if ($post->comment_status == 'open') : ?>
	<div id="respond">

		<h3 class="fl-comments-respond-title"><?php _ex( 'Leave a Comment', 'Respond form title.', 'fl-automator' ); ?></h3>

		<?php if(get_option('comment_registration') && !$user_ID ) : ?>

			<p><?php

				printf(
					_x( 'You must be <a%s>logged in</a> to post a comment.', 'Please, keep the HTML tags.', 'fl-automator' ),
					' href="' . esc_url( home_url( '/wp-login.php' ) ) . '?redirect_to=' . urlencode( get_permalink() ) . '"'
				);

			?></p>

		<?php else : ?>

			<form class="fl-comment-form" action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post">

			<?php if ($user_ID) : ?>

				<p><?php printf( __( 'Logged in as %s.', 'fl-automator' ), '<a href="' . esc_url( home_url( '/wp-admin/profile.php' ) ) . '">' . $user_identity . '</a>' ); ?> <a href="<?php echo wp_logout_url(get_permalink() ); ?>" title="<?php _e( 'Log out of this account', 'fl-automator' ); ?>"><?php _e( 'Log out &raquo;', 'fl-automator' ); ?></a></p>

			<?php else : ?>

				<label for="author"><?php _ex( 'Name', 'Comment form label: comment author name.', 'fl-automator' ); ?><?php if ( $req ) _e( ' (required)', 'fl-automator' ); ?></label>
				<input type="text" name="author" class="form-control" value="<?php echo $comment_author; ?>" tabindex="1" <?php if ($req) echo "aria-required='true'"; ?> />
				<br />

				<label for="email"><?php _ex( 'Email (will not be published)', 'Comment form label: comment author email.', 'fl-automator' ); ?><?php if ( $req ) _e( ' (required)', 'fl-automator' ); ?></label>
				<input type="text" name="email" class="form-control" value="<?php echo $comment_author_email; ?>" tabindex="2" <?php if ($req) echo "aria-required='true'"; ?> />
				<br />

				<label for="url"><?php _ex( 'Website', 'Comment form label: comment author website.', 'fl-automator' ); ?></label>
				<input type="text" name="url" class="form-control" value="<?php echo $comment_author_url; ?>" tabindex="3" />
				<br />

			<?php endif; ?>

				<label for="comment"><?php _ex( 'Comment', 'Comment form label: comment content.', 'fl-automator' ); ?></label>
				<textarea name="comment" class="form-control" cols="60" rows="8" tabindex="4"></textarea>
				<br />

					<input name="submit" type="submit" class="btn btn-primary" tabindex="5" value="<?php _e( 'Submit Comment', 'fl-automator' ); ?>" />
				<?php comment_id_fields(); ?>
				<?php do_action('comment_form', $post->ID); ?>

				<div class="fl-comment-form-cancel">
					<?php cancel_comment_reply_link(); ?>
				</div>

			</form>
		<?php endif;?>
	</div>
	<?php endif; ?>
</div>