<?php
    if (!isset($tcb_landing_page)) {
        $tcb_landing_page = $GLOBALS['tcb_landing_page'];
    }
    if (!isset($lp_template)) {
        $lp_template = $GLOBALS['tcb_lp_template'];
    }
do_action('get_header'); ?><!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
    <?php if(function_exists('tha_head_top')): tha_head_top(); endif; ?>
    <meta charset="<?php bloginfo('charset'); ?>"/>
    <title>
        <?php /* Genesis wraps the meta title into another <title> tag using this hook: genesis_doctitle_wrap. the following line makes sure this isn't called */ ?>
        <?php /* What if they change the priority at which this hook is registered ? :D */ ?>
        <?php remove_filter( 'wp_title', 'genesis_doctitle_wrap', 20 ) ?>
        <?php wp_title(''); ?>
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>

    <?php $tcb_landing_page->head(); ?>

    <style type="text/css">.thrv_page_section .out {max-width: none}</style>

</head>
<?php
$css_data = $tcb_landing_page->getCssData();
remove_all_filters('body_class'); // strip out any custom classes added by the theme for the <body> tag
?>
<body <?php body_class($css_data['class']) ?> style="<?php echo $css_data['css'] ?>"<?php echo $css_data['custom_color'] ?>>
<?php $tcb_landing_page->afterBodyOpen() ?>
<div class="wrp cnt bSe" style="display: none">
    <div class="awr"></div>
</div>
<div class="tve_wrap_all" id="tcb_landing_page" style="padding: 1px 0 0">
    <div class="tve_post_lp tve_lp_<?php echo $lp_template ?> tve_lp_template_wrapper" style="<?php echo $css_data['main_area']['css'] ?>">
        <?php echo apply_filters('tve_landing_page_content', '') ?>
    </div>
</div>
<?php $tcb_landing_page->footer() ?>
<?php do_action('get_footer') ?>
<?php wp_footer() ?>
<?php $tcb_landing_page->beforeBodyEnd() ?>
</body>
</html>