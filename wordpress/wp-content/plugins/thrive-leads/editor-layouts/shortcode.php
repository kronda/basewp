<?php global $variation; if (empty($is_ajax_render)) : /** if AJAX-rendering the contents, we need to only output the html part, and do not include any of the custom css / fonts etc needed - used in the state manager */ ?>
<?php do_action('get_header') ?><!DOCTYPE html>
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
    <meta charset="<?php bloginfo('charset'); ?>"/>
    <meta name="robots" content="noindex, nofollow"/>
    <title>
        <?php /* Genesis wraps the meta title into another <title> tag using this hook: genesis_doctitle_wrap. the following line makes sure this isn't called */ ?>
        <?php /* What if they change the priority at which this hook is registered ? :D */ ?>
        <?php remove_filter('wp_title', 'genesis_doctitle_wrap', 20) ?>
        <?php wp_title(''); ?>
    </title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <?php
    do_action('tcb_content_custom_css', $variation);

    $state_manager_collapsed = !empty($_COOKIE['tve_leads_state_collapse']);

    wp_head(); ?>
</head>
<body <?php body_class($state_manager_collapsed ? 'tl-state-collapse ' : '') ?>>
<div style="display: none" class="bSe"></div>
<?php endif;

$key = '';
if (!empty($variation[TVE_LEADS_FIELD_TEMPLATE])) {
    list($type, $key) = explode('|', $variation[TVE_LEADS_FIELD_TEMPLATE]);
    $key = preg_replace('#_v(.+)$#', '', $key);
}
?>

<div id="tve-leads-editor-replace">

    <div class="tve-leads-post-footer tve-leads-shortcode">
        <div class="tl-style" id="tve_<?php echo $key ?>" data-state="<?php echo $variation['key'] ?>">
            <?php echo apply_filters('tve_editor_custom_content', '') ?>
        </div>
        <?php echo apply_filters('tve_leads_variation_append_states', '', $variation); ?>
    </div>
    <div class="tve-leads-template-description" style="opacity: .6; margin-top: 240px;text-align: center">
        <h4><?php echo __('Currently displaying the Shortcode called ', 'thrive-leads') ?> <em><?php echo $variation['post_title'] ?></em></h4>
        <h4><?php echo __("Note that this form doesn't have any width settings. It will expand to the full width of the content area of your theme", 'thrive-leads') ?></h4>
    </div>

</div>

<?php if (empty($is_ajax_render)) : ?>
    <div id="tve_page_loader" class="tve_page_loader">
        <div class="tve_loader_inner"><img src="<?php echo tve_editor_css() ?>/images/loader.gif" alt=""/></div>
    </div>

    <?php include dirname(__FILE__) . '/_form_states.php' ?>
    <?php do_action('get_footer') ?>
    <?php wp_footer() ?>
    </body>
    </html>
<?php endif ?>