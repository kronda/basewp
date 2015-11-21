<?php if( !empty( $settings->number_size ) || !empty( $settings->number_color ) ) : ?>
	.fl-node-<?php echo $id ?> .fl-number-string{
	<?php 
		if( !empty( $settings->number_size ) ) {
			echo 'font-size: '. $settings->number_size .'px;';
		}
		if( !empty( $settings->number_color ) ) {
			echo 'color: #'. $settings->number_color .';';
		}
	?>
	}
<?php endif; ?>

<?php if( !empty( $settings->text_color ) ) : ?>
	.fl-node-<?php echo $id ?> .fl-number-before-text,
	.fl-node-<?php echo $id ?> .fl-number-after-text{
		color: #<?php echo $settings->text_color ?>;
	}
<?php endif; ?>


<?php if( isset( $settings->layout ) && $settings->layout == 'circle' ) : ?>
	.fl-node-<?php echo $id ?> .fl-number-text{
		position: absolute;
		top: 50%;
		left: 50%;
		-webkit-transform: translate(-50%,-50%);
		   -moz-transform: translate(-50%,-50%);
		    -ms-transform: translate(-50%,-50%);
		        transform: translate(-50%,-50%);
	}
	.fl-node-<?php echo $id ?> .fl-number-circle-container{
		<?php 
			if( !empty( $settings->circle_width ) ) {
				echo 'max-width: '. $settings->circle_width .'px;';
				echo 'max-height: '. $settings->circle_width .'px;';
			} else {
				echo 'max-width: 100px;';
				echo 'max-height: 100px;';				
			}
		?>
	}

	.fl-node-<?php echo $id ?> .svg circle{
	<?php 
		if( !empty( $settings->circle_dash_width ) ) {
			echo 'stroke-width: '. $settings->circle_dash_width .'px;';
		}
	?>
	}

	.fl-node-<?php echo $id ?> .svg .fl-bar-bg{
	<?php 
		if( !empty( $settings->circle_bg_color ) ) {
			echo 'stroke: #'. $settings->circle_bg_color .';';
		} else {
			echo 'stroke: transparent;';
		}
	?>
	}

	.fl-node-<?php echo $id ?> .svg .fl-bar{
	<?php 
		if( !empty( $settings->circle_color ) ) {
			echo 'stroke: #'. $settings->circle_color .';';
		} else {
			echo 'stroke: transparent;';
		}
	?>
	}
<?php elseif( isset( $settings->layout ) && $settings->layout == 'bars' ) : ?>
	.fl-node-<?php echo $id ?> .fl-number-bars-container{
		width: 100%;
		background-color: #<?php echo $settings->bar_bg_color ?>;
	}
	.fl-node-<?php echo $id ?> .fl-number-bar{
		width: 0;
		background-color: #<?php echo $settings->bar_color ?>;
	}
<?php endif; ?>