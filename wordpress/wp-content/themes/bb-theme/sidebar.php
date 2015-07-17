<?php

$position   = isset( $position ) ? $position : 'right';
$section    = isset( $section ) ? $section : 'blog';
$size       = isset( $size ) ? $size : '4';
$display    = isset( $display ) ? $display : 'desktop';

?>
<div class="fl-sidebar fl-sidebar-<?php echo $position; ?> fl-sidebar-display-<?php echo $display; ?> col-md-<?php echo $size; ?>" itemscope="itemscope" itemtype="http://schema.org/WPSideBar">
	<?php dynamic_sidebar( $section . '-sidebar' ); ?>
</div>