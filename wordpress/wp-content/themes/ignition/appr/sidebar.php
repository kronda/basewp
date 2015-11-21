<?php
$options = thrive_appr_get_theme_options();
$sidebar_class = ($options['sidebar_alignement'] == "right" || $options['sidebar_alignement'] == "left") ? $options['sidebar_alignement'] : "";
?>
<?php tha_sidebars_before(); ?>

<?php tha_sidebar_top(); ?>
<div class="sAsCont">
    <aside class="sAs <?php echo $sidebar_class; ?>">

        <?php if (!dynamic_sidebar('sidebar-appr')) : ?>
            <!--IF THE APPRENTICE SIDEBAR IS NOT REGISTERED-->
        <?php endif; // end post sidebar widget area  ?>

    </aside>
</div>
<?php tha_sidebar_bottom(); ?>

<?php tha_sidebars_after(); ?>