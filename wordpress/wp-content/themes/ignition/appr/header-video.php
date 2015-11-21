<?php
$options = thrive_get_options_for_post(get_the_ID(), array('apprentice' => 1));

$thrive_bg_color = $options['appr_media_bg_color'] != "default" ? strtolower($options['appr_media_bg_color']) : $options['color_scheme'];

$featured_image = null;
if (has_post_thumbnail(get_the_ID())) {
    $featured_image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), "full");
}

$thrive_meta_appr_video_type = get_post_meta(get_the_ID(), '_thrive_meta_appr_video_type', true);
$thrive_meta_appr_video_youtube_url = get_post_meta(get_the_ID(), '_thrive_meta_appr_video_youtube_url', true);
$thrive_meta_appr_video_vimeo_url = get_post_meta(get_the_ID(), '_thrive_meta_appr_video_vimeo_url', true);
$thrive_meta_appr_video_custom_url = get_post_meta(get_the_ID(), '_thrive_meta_appr_video_custom_url', true);
$thrive_meta_appr_video_custom_code = get_post_meta(get_the_ID(), '_thrive_meta_appr_video_custom_embed', true);

$youtube_attrs = array(
    'hide_logo' => get_post_meta(get_the_ID(), '_thrive_meta_appr_video_youtube_hide_logo', true),
    'hide_controls' => get_post_meta(get_the_ID(), '_thrive_meta_appr_video_youtube_hide_controls', true),
    'hide_related' => get_post_meta(get_the_ID(), '_thrive_meta_appr_video_youtube_hide_related', true),
    'hide_title' => get_post_meta(get_the_ID(), '_thrive_meta_appr_video_youtube_hide_title', true),
    'autoplay' => get_post_meta(get_the_ID(), '_thrive_meta_appr_video_youtube_autoplay', true),
    'hide_fullscreen' => get_post_meta(get_the_ID(), '_thrive_meta_appr_video_youtube_hide_fullscreen', true),
    'video_width' => 1080
);

if ($thrive_meta_appr_video_type == "youtube") {
    $video_code = _thrive_get_youtube_embed_code($thrive_meta_appr_video_youtube_url, $youtube_attrs);
} elseif ($thrive_meta_appr_video_type == "vimeo") {
    $video_code = _thrive_get_vimeo_embed_code($thrive_meta_appr_video_vimeo_url);
} elseif ($thrive_meta_appr_video_type == "custom_embed") {
    $video_code = do_shortcode($thrive_meta_appr_video_custom_code);
} else {
    $video_code = do_shortcode("[video src='" . $thrive_meta_appr_video_custom_url . "']");
}

?>
<div class="apps <?php echo $thrive_bg_color; ?>">
    <div class="wrp">
        <div class="apv">
            <?php if ($featured_image && isset($featured_image[0])): ?>
                <div class="scvps" style="background-image: url('<?php echo $featured_image[0]; ?>')">
                    <div class="vdc lv">
                        <div class="ltx">
                            <div class="pvb">
                                <a></a>
                            </div>
                        </div>
                    </div>
                    <div class="vdc lv video-container" style="display: none">
                        <div class="vwr">
                            <?php echo $video_code; ?>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <?php if (!empty($video_code)): ?>
                    <div class="rve">
                        <?php echo $video_code; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>

        </div>
    </div>
</div>