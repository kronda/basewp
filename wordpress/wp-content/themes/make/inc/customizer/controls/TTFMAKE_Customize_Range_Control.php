<?php
/**
 * @package Make
 */

if ( class_exists( 'WP_Customize_Control' ) && ! class_exists( 'TTFMAKE_Customize_Range_Control' ) ) :
/**
 * Class TTFMAKE_Customize_Range_Control
 *
 * Specialized range control to enable a slider with an accompanying number field.
 *
 * Inspired by Kirki.
 * @link https://github.com/aristath/kirki/blob/0.5/includes/controls/class-Kirki_Customize_Sliderui_Control.php
 *
 * @since 1.5.0.
 */
class TTFMAKE_Customize_Range_Control extends WP_Customize_Control {
	public $type = 'range';
	public $mode = 'slider';

	/**
	 * @since 1.5.0.
	 */
	public function enqueue() {
		wp_enqueue_script( 'jquery-ui-slider' );
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
	protected function render_content() { ?>
		<label>
			<?php if ( ! empty( $this->label ) ) : ?>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
			<?php endif;
			if ( ! empty( $this->description ) ) : ?>
				<span class="description customize-control-description"><?php echo $this->description; ?></span>
			<?php endif; ?>
			<div id="slider_<?php echo $this->id; ?>" class="ttfmake-range-slider"></div>
			<input id="input_<?php echo $this->id; ?>" class="ttfmake-control-range" type="number" <?php $this->input_attrs(); ?> value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); ?> />
		</label>
	<?php
	}
}
endif;