<div class="fl-number fl-number-<?php echo $settings->layout ?>">
<?php if( $settings->layout == 'circle' ) : ?>
	<div class="fl-number-circle-container">	
		<div class="fl-number-text">
			<?php if( !empty( $settings->before_number_text ) ) : ?>
				<span class="fl-number-before-text">
					<?php echo esc_html( $settings->before_number_text ) ?>
				</span>
			<?php endif; ?>

			<?php $module->render_number(); ?>

			<?php if( !empty( $settings->after_number_text ) ) : ?>
				<span class="fl-number-after-text">
					<?php echo esc_html( $settings->after_number_text ) ?>
				</span>
			<?php endif; ?>		
		</div>
		<?php $module->render_circle_bar(); ?>
	</div>
<?php elseif( $settings->layout == 'bars' ) : ?>
	<div class="fl-number-text">
		<?php if( !empty( $settings->before_number_text ) ) : ?>
			<span class="fl-number-before-text">
				<?php echo esc_html( $settings->before_number_text ) ?>
			</span>
		<?php endif; ?>
	
		<div class="fl-number-bars-container">
			<div class="fl-number-bar">
				<?php $module->render_number(); ?>
			</div>
		</div>

	</div>
<?php else : ?>
	<div class="fl-number-text">
		<?php if( !empty( $settings->before_number_text ) ) : ?>
			<span class="fl-number-before-text">
				<?php echo esc_html( $settings->before_number_text ) ?>
			</span>
		<?php endif; ?>

		<?php $module->render_number(); ?>

		<?php if( !empty( $settings->after_number_text ) ) : ?>
			<span class="fl-number-after-text">
				<?php echo esc_html( $settings->after_number_text ) ?>
			</span>
		<?php endif; ?>		
	</div>
<?php endif; ?>
</div>