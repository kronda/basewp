<?php
/*
  Template Name: Blank Page
 */
$options = thrive_get_options_for_post(get_the_ID());
?>
<!doctype html>
<html>
<head>
    <title>
        <?php wp_title(''); ?>
    </title>

    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <!--[if lt IE 10]>
    <link rel="stylesheet" type="text/css" href="css/ie.css"/>
    <![endif]-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <?php if (isset($options['analytics_header_script']) && $options['analytics_header_script'] != ""): ?>
        <?php echo $options['analytics_header_script']; ?>
    <?php endif; ?>

    <?php thrive_enqueue_head_fonts(); ?>
    <?php wp_head(); ?>
    <?php if (isset($options['custom_css']) && $options['custom_css'] != ""): ?>
        <style type="text/css"><?php echo $options['custom_css']; ?></style>
    <?php endif; ?>
    <?php tha_head_bottom(); ?>

</head>
<body class="bp-t">
<?php if (isset($options['analytics_body_script_top']) && !empty($options['analytics_body_script_top'])): ?>
    <?php echo $options['analytics_body_script_top']; ?>
<?php endif; ?>
<div class="wrp">
    <div class="bSe fullWidth">
        <?php if (have_posts()): ?>
            <?php while (have_posts()): ?>
                <?php
                the_post();
                $content = apply_filters('the_content', get_the_content());
                ?>
                <?php echo $content; ?>
                <?php if ($options['enable_social_buttons'] == 1): ?>
                    <?php get_template_part('share-buttons'); ?>
                <?php endif; ?>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
</div>

<?php tha_footer_bottom(); ?>
<?php tha_footer_after(); ?>

<?php if (isset($options['analytics_body_script']) && $options['analytics_body_script'] != ""): ?>
    <?php echo $options['analytics_body_script']; ?>
<?php endif; ?>
<?php wp_footer(); ?>
<?php tha_body_bottom(); ?>

</body>
</html>