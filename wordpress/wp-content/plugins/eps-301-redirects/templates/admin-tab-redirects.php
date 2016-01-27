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
global $EPS_Redirects_Plugin;


?>


<div class="wrap">
    <?php do_action('eps_redirects_admin_head'); ?>

    <table id="eps-redirect-entries" class="eps-table eps-table-striped">
        <tr>
            <th class="redirect-small"> <?php eps_get_ordered_filter('id', 'ID'); ?> </th>
            <th> <?php eps_get_ordered_filter('url_from', 'Redirect From'); ?> </th>
            <th> <?php eps_get_ordered_filter('url_to', 'Redirect To'); ?> </th>
            <th class="redirect-small"> <?php eps_get_ordered_filter('count', 'Hits'); ?> </th>
            <th class="redirect-actions">Actions</th>
        </tr>

        <tr id="eps-redirect-add" style="display:none"><td colspan="5"><a href="#" id="eps-redirect-new"><span>+</span></a></td></tr>

        <?php
        echo EPS_Redirects::get_inline_edit_entry();
        echo EPS_Redirects::list_redirects();
        ?>
    </table>


    <div class="right">
        <?php do_action('eps_redirects_panels_right'); ?>
    </div>
    <div class="left">
        <?php do_action('eps_redirects_panels_left'); ?>
    </div>
</div>
    
    
    
    
