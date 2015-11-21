<div class="rltp<?php echo $options['related_posts_images'] == 1 ? 'i' : ''; ?> clearfix">
    <div class="awr">
        <h5><?php echo $options['related_posts_title'] ?></h5>
        <?php foreach ($relatedPosts as $p): ?>
            <a href="<?php echo get_permalink($p->ID); ?>" class="rlt left">
                <div class="rlti" <?php
                     if ($options['related_posts_images'] == 1) {
                         if (has_post_thumbnail($p->ID)) {
                             $featured_img_data =  thrive_get_post_featured_image($p->ID, "tt_related_posts");
                             $featured_img = $featured_img_data['image_src'];
                             echo ' style="background-image: url(\'' . $featured_img . '\')"';
                         } else {
                             echo ' style="background-image: url(\'' . get_template_directory_uri() . "/images/default_featured.jpg" . '\')"';
                         }

                     }
                     ?>></div>
                <p><?php echo $p->post_title ?></p>
            </a>
        <?php endforeach; ?>
        <?php if (empty($relatedPosts)): ?>
        <span><?php echo $options['related_no_text'] ?></span>
        <?php endif; ?>
    </div>
</div>