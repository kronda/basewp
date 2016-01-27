<?php
/**
 *
 * The Redirects Tab.
 *
 * The main admin area for the redirects tab.
 *
 * @package    EPS 301 Redirects
 * @author     Shawn Wernig ( shawn@eggplantstudios.ca )
 */
?>


<div class="wrap">
    <?php do_action('eps_redirects_admin_head'); ?>

    <div class="eps-notice eps-warning">
        <?php echo $options['description']; ?>
    </div>


    <div class="right">
        <?php do_action('eps_redirects_panels_right'); ?>
    </div>
    <div class="left">
        <?php do_action('eps_redirects_panels_left'); ?>
    </div>
</div>
    
    
    
    
