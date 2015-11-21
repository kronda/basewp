<?php tha_content_bottom(); ?>
<?php
$options = thrive_get_options_for_post(get_the_ID(), array('apprentice' => 1));
$active_footers = array();
$num = 0;
while ($num < 4) {
    $num++;
    if (is_active_sidebar('footer-appr-' . $num)) {
        array_push($active_footers, 'footer-appr-' . $num);
    }
}
$num_cols = count($active_footers);
$f_class = _thrive_get_footer_col_class(count($active_footers));
?>
<div class="clear"></div>
<?php tha_content_after(); ?>
<?php tha_footer_before(); ?>
<footer>
    <div class="ftw">
        <?php tha_footer_top(); ?>
        <div class="wrp cnt">
            <?php
            $num = 0;
            foreach ($active_footers as $name):
                $num++;
                ?>
                <div class="<?php echo $f_class; ?> <?php echo ($num == $num_cols) ? 'lst' : ''; ?>">
                    <?php dynamic_sidebar($name); ?>
                </div>
            <?php endforeach; ?>
            <div class="clear"></div>
        </div>
    </div>

    <?php if (has_nav_menu("apprentice")): ?>
        <div class="fmn">
            <div class="wrp cnt">
                <?php wp_nav_menu(array('theme_location' => 'footer', 'depth' => 1, 'menu_class' => 'footer_menu', 'container_class' => 'footer_menu_container')); ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="copy">
        <div class="wrp cnt">
            <p>
                <?php if (isset($options['footer_copyright']) && $options['footer_copyright']): ?>
                    <?php echo $options['footer_copyright']; ?>
                <?php endif; ?>
                <?php if (isset($options['footer_copyright_links']) && $options['footer_copyright_links'] == 1): ?>
                    &nbsp;&nbsp;-&nbsp;&nbsp;Designed by
                    <a href="http://www.thrivethemes.com" target="_blank" style="text-decoration: underline;">Thrive Themes</a>
                    | Powered by <a style="text-decoration: underline;" href="http://www.wordpress.org" target="_blank">WordPress</a>
                <?php endif; ?>
            </p>
            <?php
            if (!empty($options['social_facebook']) || !empty($options['social_twitter']) || !empty($options['social_youtube']) || !empty($options['social_gplus']) || !empty($options['social_pinterest']) || !empty($options['social_linkedin'])):
                ?>
                <ul class="fsc">
                    <?php if (str_replace(" ", "", $options['social_linkedin']) != ""): ?>
                        <li>
                            <a href="<?php echo $options['social_linkedin']; ?>" target="_blank">
                                <span class="awe">&#xf0e1;</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (str_replace(" ", "", $options['social_facebook']) != ""): ?>
                        <li>
                            <a href="<?php echo $options['social_facebook']; ?>" target="_blank">
                                <span class="awe">&#xf09a;</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (str_replace(" ", "", $options['social_twitter']) != ""): ?>
                        <li>
                            <a href="<?php echo _thrive_get_twitter_link($options['social_twitter']); ?>" target="_blank">
                                <span class="awe">&#xf099;</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (str_replace(" ", "", $options['social_gplus']) != ""): ?>
                        <li>
                            <a href="<?php echo $options['social_gplus']; ?>" target="_blank">
                                <span class="awe">&#xf0d5;</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (str_replace(" ", "", $options['social_youtube']) != ""): ?>
                        <li>
                            <a href="<?php echo $options['social_youtube']; ?>" target="_blank">
                                <span class="awe">&#xf167;</span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (str_replace(" ", "", $options['social_pinterest']) != ""): ?>
                        <li>
                            <a href="<?php echo $options['social_pinterest']; ?>" target="_blank">
                                <span class="awe">&#xf0d2;</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
    <?php tha_footer_bottom(); ?>
</footer>
<?php tha_footer_after(); ?>

<?php if (isset($options['analytics_body_script']) && $options['analytics_body_script'] != ""): ?>
    <?php echo $options['analytics_body_script']; ?>
<?php endif; ?>
<?php wp_footer(); ?>
<?php tha_body_bottom(); ?>
</body>
</html>