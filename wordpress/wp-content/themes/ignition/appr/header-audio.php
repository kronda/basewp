<?php

$options = thrive_get_options_for_post(get_the_ID(), array('apprentice' => 1));
$thrive_meta_appr_audio_type = get_post_meta(get_the_ID(), '_thrive_meta_appr_audio_type', true);
$thrive_meta_appr_audio_file = get_post_meta(get_the_ID(), '_thrive_meta_appr_audio_file', true);
$thrive_meta_appr_audio_soundcloud_embed_code = get_post_meta(get_the_ID(), '_thrive_meta_appr_audio_soundcloud_embed_code', true);
$thrive_bg_color = $options['appr_media_bg_color'] != "default" ? strtolower($options['appr_media_bg_color']) : $options['color_scheme'];

$featured_image = null;
if (has_post_thumbnail(get_the_ID())) {
    $featured_image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), "full");
    $thrive_bg_color = "nop";
}
?>
<div class="apps  <?php echo $thrive_bg_color;?>">
    <div class="wrp ap-v">
        <?php if ($featured_image && isset($featured_image[0])): ?>
            <div class="apv" style="background-image: url('<?php echo $featured_image[0];?>');">
            <img class="dmy" src="<?php echo $featured_image[0];?>"/>
            <?php endif; ?>
            <?php if ($thrive_meta_appr_audio_type != "soundcloud"): ?>                    
                <?php echo do_shortcode("[audio src='" . $thrive_meta_appr_audio_file . "'][/audio]"); ?>
            <?php else: ?>
                <?php echo $thrive_meta_appr_audio_soundcloud_embed_code; ?>
            <?php endif; ?>
            <?php if ($featured_image): ?>
            </div>
        <?php endif; ?>
    </div>
</div>