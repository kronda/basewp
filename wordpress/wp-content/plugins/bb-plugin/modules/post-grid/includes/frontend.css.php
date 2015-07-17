<?php if($settings->layout == 'grid') : // GRID ?>

.fl-node-<?php echo $id; ?> .fl-post-grid-post {    
	margin-bottom: <?php echo $settings->post_spacing; ?>px;
	width: <?php echo $settings->post_width; ?>px;
}
.fl-node-<?php echo $id; ?> .fl-post-grid-sizer {
	width: <?php echo $settings->post_width; ?>px;
}

@media screen and (max-width: <?php echo $settings->post_width + $settings->post_spacing; ?>px) {
	.fl-node-<?php echo $id; ?> .fl-post-grid,
	.fl-node-<?php echo $id; ?> .fl-post-grid-post,
	.fl-node-<?php echo $id; ?> .fl-post-grid-sizer {
		width: 100% !important;
	}
}

<?php endif; ?>