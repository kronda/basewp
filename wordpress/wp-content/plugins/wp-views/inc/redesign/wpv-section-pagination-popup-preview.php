<h3>
	<?php _e('This is a preview', 'wpv-views'); ?>
</h3>
<p class="js-choose-pagination-type toolset-alert toolset-alert-info">
	<?php _e('Choose pagination control type', 'wpv-views'); ?>
</p>
<div class="js-pagination-preview pagination-preview hidden">
	<p class="js-pagination-preview-element" data-name="current-page-number">
		<?php _e('Showing page','wpv-views'); ?>:
		2
	</p>
	<p>
		<label class="js-pagination-preview-element" data-name="page-selector" data-type="page-selector-link page-selector-select"><?php _e('Choose page','wpv-views'); ?></label>
		<select class="js-disable-events js-pagination-preview-element" disabled data-name="page-selector" data-type="page-selector-select">
			<option>2</option>
		</select>
		<span data-name="page-selector" data-type="page-selector-link" class="js-pagination-preview-element hidden">
			<img src="<?php echo (WPV_URL . '/res/img/dots.png'); ?>" alt="dots" style="vertical-align: middle" />
		</span>
		<!-- <a href="#" data-name="page-selector" data-type="page-selector-link" class="js-pagination-preview-element hidden">1</a>
		<a href="#" data-name="page-selector" data-type="page-selector-link" class="js-pagination-preview-element hidden">2</a>
		<a href="#" data-name="page-selector" data-type="page-selector-link" class="js-pagination-preview-element hidden">3</a>
		<a href="#" data-name="page-selector" data-type="page-selector-link" class="js-pagination-preview-element hidden">4</a> -->
		<span class="js-pagination-preview-element" data-name="total-pages"><?php _e('of','wpv-views'); ?> 3</span>
	</p>
	<p class="js-pagination-preview-element next-previous-controls" data-name="next-previous-controls">
		<a href="#" class="js-disable-events">&laquo; <?php _e('Previous','wpv-views') ?></a>
		<a href="#" class="js-disable-events"><?php _e('Next','wpv-views') ?> &raquo;</a>
	</p>
</div>