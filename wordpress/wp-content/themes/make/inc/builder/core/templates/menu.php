<?php
/**
 * @package Make
 */

$class = ( 'c' === get_user_setting( 'ttfmakemt' . get_the_ID() ) ) ? 'closed' : 'opened';
?>

<div class="ttfmake-menu ttfmake-menu-<?php echo esc_attr( $class ); ?>" id="ttfmake-menu">
	<div class="ttfmake-menu-pane">
		<ul class="ttfmake-menu-list">
			<?php
			/**
			 * Execute code before the builder menu items are displayed.
			 *
			 * @since 1.2.3.
			 */
			do_action( 'make_before_builder_menu' );
			?>
			<?php foreach ( ttfmake_get_sections_by_order() as $key => $item ) : ?>
			<a href="#" title="<?php echo esc_html( $item['description'] ); ?>" class="ttfmake-menu-list-item-link" id="ttfmake-menu-list-item-link-<?php echo esc_attr( $item['id'] ); ?>" data-section="<?php echo esc_attr( $item['id'] ); ?>">

				<li class="ttfmake-menu-list-item">
						<div class="ttfmake-menu-list-item-link-icon-wrapper clear">
							<span class="ttfmake-menu-list-item-link-icon"></span>
							<div class="section-type-description">
								<h4>
									<?php echo esc_html( $item['label'] ); ?>
								</h4>
							</div>
						</div>

				</li>
				</a>
			<?php endforeach; ?>
			<?php if ( ! ttfmake_is_plus() ) : ?>
				<li id="ttfmake-menu-list-item-link-plus" class="ttfmake-menu-list-item">
					<div>
						<h4><?php _e( 'Get more.', 'make' ); ?></h4>
						<p class="howto">
							<?php
							printf(
								__( 'Looking for more sections and options? %s.', 'make' ),
								sprintf(
									'<a href="%1$s" target="_blank">%2$s</a>',
									ttfmake_get_plus_link(),
									__( 'Upgrade to Make Plus', 'make' )
								)
							);
							?>
						</p>
					</div>
				</li>
			<?php endif; ?>
			<?php
			/**
			 * Execute code after the builder menu items are displayed.
			 *
			 * @since 1.2.3.
			 */
			do_action( 'make_after_builder_menu' );
			?>
		</ul>
	</div>
</div>
