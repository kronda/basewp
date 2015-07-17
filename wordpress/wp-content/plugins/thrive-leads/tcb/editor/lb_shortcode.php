
<h2>Insert Sortcode</h2>
<input type="hidden" name="tve_lb_type" value="shortcode">

<?php

wp_editor('', 'tve_wp_shortcode', array(
    'dfw' => true,
    'media_buttons' => false,
    'tinymce' => true
));
do_action('admin_print_footer_scripts');