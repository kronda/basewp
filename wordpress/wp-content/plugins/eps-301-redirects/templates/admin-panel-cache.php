<?php
/**
 *
 * Admin Panel Cache
 *
 * The cache panel widget.
 *
 * @package    EPS 301 Redirects
 * @author     Shawn Wernig ( shawn@eggplantstudios.ca )
 */
?>

<div class="eps-panel eps-margin-top">
    <form method="post" action="">
        <?php wp_nonce_field('eps_redirect_nonce', 'eps_redirect_nonce_submit');   ?>
        <input type="submit" name="eps_redirect_refresh" id="submit" class="button button-secondary" value="Refresh Cache"/>
        <br><small class="eps-grey-text">Refresh the cache if the dropdowns are out of date.</small>
    </form>
</div>