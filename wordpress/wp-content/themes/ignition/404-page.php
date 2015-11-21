<?php
/*
  Template Name: 404Page
 */
?>
<?php
$options = thrive_get_theme_options();
?>
<?php get_header(); ?>

<section class="bSe">
    <div class="awr">
        <div class="err">
            <span class="left">404</span>
            <p class="left">
                <?php _e("Ooops!", 'thrive');?><br/>
                <b><?php _e("The page you are looking for seems to be missing. Perhaps searching can help.", 'thrive');?></b>
            </p>
            <div class="clear"></div>
            <div class="spr"></div>
            <form action="" method="get">
                <input id="search-field" type="text" placeholder="Search">
                <button id="search-big-button" class="sBn" type="submit"><b><?php _e("SEARCH", 'thrive')?></b></button>
            </form>
        </div>
    </div>
</section>
<?php get_footer(); ?>
