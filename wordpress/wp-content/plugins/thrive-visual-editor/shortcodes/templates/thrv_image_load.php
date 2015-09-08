<?php $width = isset($_POST['width']) ? $_POST['width'] : '' ?>
<div style="<?php echo $width ? "width: {$width}px" : '' ?>" class="<?php echo empty($_POST['caption']) ? '' : 'wp-caption ' ?>thrv_wrapper tve_image_caption">

    <span class="tve_image_frame">
        <img class="tve_image" alt="<?php echo $_POST['alt_text']; ?>" src="<?php echo $_POST['url']; ?>"
             style="<?php echo $width ? "width: {$width}px" : '' ?>"/>
    </span>

    <?php if (!empty($_POST['caption'])): ?>
        <p class="wp-caption-text"><?php echo $_POST['caption']; ?></p>
    <?php endif; ?>
</div>