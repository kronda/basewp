<?php
$options = thrive_get_options_for_post(get_the_ID(), array('apprentice' => 1));
$template_name = _thrive_get_item_template(get_the_ID());
?>
<?php if ($options['display_breadcrumbs'] && $options['display_breadcrumbs'] == 1 && !is_home() && !is_front_page() && !is_search() && !is_404()): ?>
    <section class="brd">
        <div class="wrp <?php if ($template_name == "Narrow"):?>bwr<?php endif;?>">
            <ul>
                <?php thrive_appr_breadcrumbs(); ?>                
            </ul>
        </div>
    </section>
<?php else: ?>

<?php endif; ?>

