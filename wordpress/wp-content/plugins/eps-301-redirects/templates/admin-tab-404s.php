<?php
/**
 *
 * The 404 Tab.
 *
 * The main admin area for the 404 tab.
 *
 * @package    EPS 301 Redirects
 * @author     Shawn Wernig ( shawn@eggplantstudios.ca )
 */
?>

<div class="wrap">
    <?php do_action('eps_redirects_admin_head'); ?>

    <div class="eps-panel eps-margin-top group">
        <div class="eps-redirects-50 group">
            <h1>Take your redirects to the next level!</h1>
            <p class="eps-redirects-lead">The <a href="http://www.eggplantstudios.ca/cart/?add_to_cart=2974" target="_blank">Pro Version of EPS 301 Redirects</a> will add a new 404 tracking feature. Every 404 error will be logged, and you will have the power and flexibility to redirect them wherever you want them to go.</p>

            <ul id="eps-redirects-checklist">
                <li><span>See which Request URLs are causing 404 errors on your site.</span></li>
                <li><span>Discover which 404 errors are receiving the most traffic.</span></li>
                <li><span>Improve SEO by lowering your total number of 404 errors.</span></li>
                <li><span>Easily fix the 404 errors by turning them into redirects.</span></li>
            </ul>
        </div>
        <div class="eps-redirects-50 group">
            <div class="padding-lots">
                <a href="http://www.eggplantstudios.ca/cart/?add_to_cart=2974" target="_blank">
                    <img class="eps-redirects-fit" src="<?php echo EPS_REDIRECT_URL; ?>/images/icon-eps-redirects.jpg" title="Upgrade EPS 301 Redirects">
                </a>
                <a class="eps-redirects-big-button" href="http://www.eggplantstudios.ca/cart/?add_to_cart=2974" target="_blank">BUY NOW &bull; ONLY $15.00</a>

            </div>
        </div>
    </div>


    <div class="right">
        <?php do_action('eps_redirects_panels_right'); ?>
    </div>
    <div class="left">
        <?php do_action('eps_redirects_panels_left'); ?>
    </div>
</div>




