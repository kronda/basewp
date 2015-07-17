<?php

$defaultMenuId = 0;
$menus = get_terms('nav_menu');

if (!empty($menus)) {
    $defaultMenuId = $menus[0]->term_id;
}

$attributes = array(
    'title' => isset($_POST['title']) ? $_POST['title'] : "",
    'thumbnails' => isset($_POST['thumbnails']) ? $_POST['thumbnails'] : 'off',
    'menu' => isset($_POST['menu']) ? $_POST['menu'] : $defaultMenuId,
);
?>

<?php if (empty($_POST['nowrap'])) : ?>
    <div class="thrv_wrapper thrv_custom_menu" data-tve-style="1">
<?php endif ?>
    <div class="thrive-shortcode-config" style="display: none !important"><?php echo '__CONFIG_custom_menu__' . json_encode($attributes) . '__CONFIG_custom_menu__' ?></div>
    <div class="thrive-shortcode-html">
        <?php echo thrive_shortcode_custom_menu($attributes, '') ?>
    </div>
<?php if (empty($_POST['nowrap'])) : ?></div><?php endif ?>