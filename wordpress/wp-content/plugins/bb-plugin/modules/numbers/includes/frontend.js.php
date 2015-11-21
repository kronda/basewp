<?php 
	
	// set defaults
	$layout = isset( $settings->layout ) ? $settings->layout : 'default';
	$type   = isset( $settings->number_type ) ? $settings->number_type : 'percent';
	$speed  = !empty( $settings->animation_speed ) ? $settings->animation_speed * 1000 : 1000;
	$number = !empty( $settings->number ) ? $settings->number : 0;
	$max    = !empty( $settings->max_number ) ? $settings->max_number : $number;
	$delay  = !empty( $settings->delay ) ? $settings->delay : 0;

 ?>

(function($) {

	$(function() {

	    new FLBuilderNumber({
	    	id: '<?php echo $id ?>',
	    	layout: '<?php echo $layout ?>',
	    	type: '<?php echo $type ?>',
	    	number: <?php echo $number ?>,
	    	max: <?php echo $max ?>,
	    	speed: <?php echo $speed ?>,
	    	delay: <?php echo $delay ?>,
	    });

	});

})(jQuery);