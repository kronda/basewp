<?php
/**
 *
 * Admin Panel Donate
 *
 * The donate panel widget.
 *
 * @package    EPS 301 Redirects
 * @author     Shawn Wernig ( shawn@eggplantstudios.ca )
 */
?>


<div id="donate-box" class="eps-panel">
    <div class="eps-padding">
        <p>Comments, questions, bugs and feature requests can be sent to: <a href="mailto:plugins@eggplantstudios.ca">plugins@eggplantstudios.ca</a>. Please quote the plugin version: <?php echo get_option( 'eps_redirects_version' ); ?></p>
        <hr>
        <h3>Please consider donating</h3>
        <p>Your donations help support future versions EPS 301 Redirects.</p>
        <form action="https://www.paypal.com/cgi-bin/webscr" method="post" target="_top">
            <p>
                <input type="hidden" name="cmd" value="_s-xclick">
                <input type="hidden" name="hosted_button_id" value="2WC9XYFX49CSQ">
                <input class="button button-secondary" type="submit" name="submit" value="Donate">
            </p>
        </form>
    </div>
</div>