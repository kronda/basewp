<?php
global $post;
$lazy_load_comments = thrive_get_theme_options("comments_lazy");
$enable_fb_comments = thrive_get_theme_options("enable_fb_comments");
$fb_app_id = thrive_get_theme_options("fb_app_id");
?>

<?php if ($lazy_load_comments == 1): ?>
    <script type="text/javascript">
        _thriveCurrentPost = <?php echo json_encode(get_the_ID()); ?>;
    </script>
<?php endif; ?>
<?php tha_comments_before(); ?>
<?php if ($enable_fb_comments != "only_fb"): ?>
    <article id="comments">
        <div class="awr">
            <?php if (comments_open() && !post_password_required() && ($enable_fb_comments == "both_fb_regular" || $enable_fb_comments == "off")) : ?>
                <div class="ctb">
                    <h5><span class="txt_thrive_link_to_comments"><?php _e("Click Here to Leave a Comment Below", 'thrive'); ?></span></h5>
                </div>
            <?php endif; ?>

            <?php if ($lazy_load_comments != 1):
                thrive_theme_comment_nav();
            endif; ?>

            <div id="thrive_container_list_comments">
                <?php if ($lazy_load_comments != 1): ?>
                    <?php wp_list_comments(array('callback' => 'thrive_comments')); ?>
                <?php endif; ?>
            </div>

            <?php if ($lazy_load_comments != 1):
                thrive_theme_comment_nav();
            endif; ?>
            
            <?php if (comments_open() && !post_password_required() && ($enable_fb_comments == "both_fb_regular" || $enable_fb_comments == "off")) : ?>
                <?php if ($lazy_load_comments == 1): ?>
                    <div class="ctb ctr" style="display: none;" id="thrive_container_preload_comments">
                        <img class="preloader" src="<?php echo get_template_directory_uri() ?>/images/loading.gif" alt=""/>
                    </div>
                <?php endif; ?>
                <div class="lrp" id="thrive_container_form_add_comment">
                    <p><?php _e("Leave a Comment:", 'thrive'); ?></p>
                    <form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">
                        <?php if (!is_user_logged_in()): ?>
                            <input type="text" placeholder="<?php _e("Name", 'thrive'); ?>*" id="author" author="author" class="text_field author" name="author" />
                            <input type="text" placeholder="<?php _e("Email", 'thrive'); ?>*" id="email" author="email" class="text_field email" name="email" />
                            <input type="text" placeholder="<?php _e("Website", 'thrive'); ?>" id="website" author="website" class="text_field website lst" name="url" />
                        <?php endif; ?>
                        <textarea id="comment" placeholder="<?php _e("Comment", 'thrive'); ?>" name="comment" class="textarea"></textarea>

                        <div class="">
                            <input type="submit" value="<?php _e("SUBMIT", 'thrive'); ?>" />
                        </div>
                        <?php comment_id_fields(); ?>
                        <?php do_action('comment_form', $post->ID); ?>
                    </form>
                </div>
            <?php elseif ((!comments_open() || post_password_required()) && get_comments_number() > 0): ?>
                <div class="no_comm">
                    <h4 class="ctr">
                        <?php _e("Comments are closed", 'thrive'); ?>
                    </h4>
                </div>
            <?php endif; ?>
        </div>

    </article>
<?php endif; ?>
<?php if (((comments_open() && $enable_fb_comments != "off") || (!comments_open() && $enable_fb_comments == "fb_when_disabled")) && !empty($fb_app_id)) : ?>
    <article id="comments_fb" style="min-height: 100px; border: 1px solid #ccc;">
        <div class="fb-comments" data-href="<?php echo get_permalink(get_the_ID()); ?>" data-numposts="<?php echo thrive_get_theme_options("fb_no_comments") ?>" data-width="100%" data-colorscheme="<?php echo thrive_get_theme_options("fb_color_scheme") ?>"></div>
    </article>
<?php endif; ?>
<?php tha_comments_after(); ?>