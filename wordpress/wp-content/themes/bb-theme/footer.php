		<?php do_action('fl_content_close'); ?>
	
	</div><!-- .fl-page-content -->
	<?php 
		
	do_action('fl_after_content'); 
	
	if ( FLTheme::has_footer() ) :
	
	?>
	<footer class="fl-page-footer-wrap" itemscope="itemscope" itemtype="http://schema.org/WPFooter">
		<?php 
		
		FLTheme::footer_widgets();
		
		do_action('fl_after_footer_widgets');
		
		FLTheme::footer();
		
		do_action('fl_after_footer');
		
		?>
	</footer>
	<?php endif; ?>
</div><!-- .fl-page -->
<?php 
	
wp_footer(); 

do_action('fl_body_close');

FLTheme::footer_code();

?>
</body>
</html>