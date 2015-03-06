<?php
/**
 * @package Make
 */

if ( class_exists( 'WP_Customize_Control' ) && ! class_exists( 'TTFMAKE_Customize_Background_Position_Control' ) ) :
/**
 * Class TTFMAKE_Customize_Background_Position_Control
 *
 * Specialized radio control for choosing background image positioning.
 *
 * @since 1.5.0.
 */
class TTFMAKE_Customize_Background_Position_Control extends WP_Customize_Control {
	public $type = 'radio';
	public $mode = 'background-position';

	/**
	 * @since 1.5.0.
	 */
	public function enqueue() {
		wp_enqueue_script( 'jquery-ui-button' );
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
		if ( empty( $this->choices ) || count( $this->choices ) !== 9 ) {
			return;
		}

		$name = '_customize-radio-' . $this->id;
		?>

		<?php if ( ! empty( $this->label ) ) : ?>
			<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
		<?php endif;
		if ( ! empty( $this->description ) ) : ?>
			<span class="description customize-control-description"><?php echo $this->description; ?></span>
		<?php endif; ?>

			<div id="input_<?php echo $this->id; ?>" class="ttfmake-control-<?php echo $this->mode; ?>">
			<?php
			$i = 1;
			foreach ( $this->choices as $value => $label ) : ?>
				<input type="radio" value="<?php echo esc_attr( $value ); ?>" name="<?php echo esc_attr( $name ); ?>" id="<?php echo esc_attr( $this->id . $value ); ?>" <?php $this->link(); checked( $this->value(), $value ); ?> />
				<label class="choice-<?php echo esc_attr( $i ); ?>" data-label="<?php echo esc_attr( $label ); ?>" for="<?php echo $this->id . $value; ?>"></label>
			<?php
				$i++;
			endforeach; ?>
			</div>
			<div class="background-position-caption">
				<?php if ( isset( $this->choices[ $this->value() ] ) ) : ?>
				<?php echo esc_html( $this->choices[ $this->value() ] ); ?>
				<?php endif; ?>
			</div>
	<?php
	}
}
endif;