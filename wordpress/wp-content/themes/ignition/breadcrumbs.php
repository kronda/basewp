<?php
$options = thrive_get_options_for_post();
?>


<?php if ($options['display_breadcrumbs'] && $options['display_breadcrumbs'] == 1 && !is_front_page() && !is_search() && !is_404()): ?>

    <section class="brd">
        <div class="wrp bwr">

            <?php if (_thrive_check_is_woocommerce_page()): ?>
                <?php woocommerce_breadcrumb(); ?>
            <?php else: ?>
                <ul xmlns:v="http://rdf.data-vocabulary.org/#">
                    <?php
                    /** if on a BBpress page then show the bbpress breadcrumbs */
                    if ((get_post_type() == "forum" || get_post_type() == "topic" || get_post_type() == "reply") && function_exists("bbp_breadcrumb")) {
                        bbp_breadcrumb();
                    } else {
                        thrive_breadcrumbs();
                    }
                    ?>
                </ul>
            <?php endif; ?>

        </div>
    </section>
<?php else: ?>

<?php endif; ?>

