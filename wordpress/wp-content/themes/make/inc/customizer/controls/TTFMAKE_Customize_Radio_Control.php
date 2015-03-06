<?php
/**
 * @package Make
 */

if ( class_exists( 'WP_Customize_Control' ) && ! class_exists( 'TTFMAKE_Customize_Radio_Control' ) ) :
/**
 * Class TTFMAKE_Customize_Radio_Control
 *
 * Specialized radio control to enable buttonset-style choices.
 *
 * Inspired by Kirki.
 * @link https://github.com/aristath/kirki/blob/0.5/includes/controls/class-Kirki_Customize_Radio_Control.php
 *
 * @since 1.5.0.
 */
class TTFMAKE_Customize_Radio_Control extends WP_Customize_Control {
	public $type = 'radio';
	public $mode = 'radio';

	/**
	 * @since 1.5.0.
	 */
	public function enqueue() {
		if ( 'buttonset' === $this->mode || 'image' === $this->mode ) {
			wp_enqueue_script( 'jquery-ui-button' );
		}
	}

	/**
	 * @since 1.5.0.
	 */
	protected function render() {
		$id    = 'customize-control-' . str_replace( '[', '-', str_replace( ']', '', $this->id ) );
		$class = 'customize-control customize-control-' . $this->type . ' customize-control-' . $this->type . '-' . $this->mode;

		?><li id="<?php echo esc_attr( $id ); ?>" class="<?php echo esc_attr( $class ); ?>">
			<?php $this->render_content(); ?>
		</li><?php
	}

	/**
	 * @since 1.5.0.
	 */
	protected function render_content() {
		if ( empty( $this->choices ) ) {
			return;
		}

		$name = '_customize-radio-' . $this->id;
		?>
		<div id="input_<?php echo $this->id; ?>" class="ttfmake-control-<?php echo $this->mode; ?>">

		<?php if ( ! empty( $this->label ) ) : ?>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
		<?php endif;
		if ( ! empty( $this->description ) ) : ?>
			<span class="description customize-control-description"><?php echo $this->description; ?></span>
		<?php endif; ?>

			<?php
			// Buttonset radios
			if ( 'buttonset' == $this->mode ) {
				foreach ( $this->choices as $value => $label ) : ?>
					<input type="radio" value="<?php echo esc_attr( $value ); ?>" name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $this->id . $value ); ?>" <?php $this->link(); checked( $this->value(), $value ); ?> />
					<label for="<?php echo $this->id . $value; ?>">
						<?php echo esc_html( $label ); ?>
					</label>
					<?php
				endforeach;
			}
			// Image radios
			elseif ( 'image' == $this->mode ) {
				foreach ( $this->choices as $value => $label ) : ?>
					<input class="image-select" type="radio" value="<?php echo esc_attr( $value ); ?>" name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $this->id . $value ); ?>" <?php $this->link(); checked( $this->value(), $value ); ?> />
					<label for="<?php echo $this->id . $value; ?>">
						<img src="<?php echo esc_html( $label ); ?>" alt="<?php echo esc_attr( $value ); ?>">
					</label>
					<?php
				endforeach;
			}
			// Normal radios
			else {
				foreach ( $this->choices as $value => $label ) : ?>
					<label class="customizer-radio">
						<input type="radio" value="<?php echo esc_attr( $value ); ?>" name="<?php echo esc_attr( $name ); ?>" <?php $this->link(); checked( $this->value(), $value ); ?> />
						<?php echo esc_html( $label ); ?><br/>
					</label>
				<?php
				endforeach;
			}
			?>
		</div>
	<?php
	}
}
endif;