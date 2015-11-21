<?php
$options = thrive_get_theme_options();
?>
<?php get_header(); ?>

<div class="wrp cnt">
<section class="bSe fullWidth">
    <article>
        <div class="awr err">
            <h1 class="ctr">404</h1>
            <h4 class="ctr">
                <?php _e("Ooops!", 'thrive'); ?><br/>
                <b><?php _e("The page you are looking for seems to be missing. Perhaps searching can help.", 'thrive'); ?></b>
            </h4>
            <form method="get" action="<?php echo home_url('/'); ?>" class="srh lost">
                <div>
                    <input type="text" placeholder="<?php _e("What Are You looking For?", 'thrive'); ?>" class="search-field" name="s"/>
                    <button type="submit" class="search-button sBn">&#xf002;</button>
                    <div class="clear"></div>
                </div>
            </form>
            <div class="spr"></div>
            <?php if (!empty($options['404_custom_text'])): ?>
                <p><?php echo do_shortcode($options['404_custom_text']); ?></p>
            <?php endif; ?>
            <?php
            if (isset($options['404_display_sitemap']) && $options['404_display_sitemap'] == "on"):
                $categories = get_categories(array('parent' => 0));
                $pages = get_pages();
                ?>
                <div class="csc">
                    <div class="colm thc">
                        <h3><?php _e("Categories", 'thrive'); ?></h3>
                        <ul class="tt_sitemap_list">
                            <?php foreach ($categories as $cat): ?>
                                <li><a href='<?php echo get_category_link($cat->term_id); ?>'><?php echo $cat->name; ?></a>
                                        <?php
                                        $subcats = get_categories(array('child_of' => $cat->term_id));
                                        if (count($subcats) > 0):
                                            ?>
                                            <ul>
                                                <?php foreach ($subcats as $subcat): ?>
                                                    <li><a href='<?php echo get_category_link($subcat->term_id); ?>'><?php echo $subcat->name; ?></a>
                                                    <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="colm thc">
                        <h3><?php _e("Archives", 'thrive'); ?></h3>
                        <ul>
                            <?php wp_get_archives(); ?>
                        </ul>
                    </div>
                    <div class="colm thc lst">
                        <h3><?php _e("Pages", 'thrive'); ?></h3>
                        <ul class="tt_sitemap_list">
                            <?php foreach ($pages as $page): ?>
                                <li><a href='<?php echo get_page_link($page->ID); ?>'><?php echo $page->post_title; ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="clear"></div>
                </div>
            <?php endif; ?>
        </div>
    </article>
</section>
</div>
<div class="clear"></div>
<div class="bspr"></div>
<?php get_footer(); ?>
