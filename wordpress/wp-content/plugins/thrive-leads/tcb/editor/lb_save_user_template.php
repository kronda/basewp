<h2>Save User Template</h2>
<?php if (empty($_POST['element'])) : ?>
    <p>You can save your work as a template for use on another post/page on your site.</p>
<?php else : ?>
    <p>You can save the current element as a template for use on another post / page on your site</p>
    <input type="hidden" name="element" value="1" />
<?php endif ?>
<br/>
<input type="hidden" name="tve_lb_type" value="user_template">
<input class="tve_lightbox_input" name="template_name" id="template_name" placeholder="Enter Content Template name" />
<br/><br/>
<script type="text/javascript">
    jQuery('#template_name').focus();
</script>