<?php
/**
 *
 * The Import/Export Tab.
 *
 * The main admin area for the import/export tab.
 *
 * @package    EPS 301 Redirects
 * @author     Shawn Wernig ( shawn@eggplantstudios.ca )
 */
?>

<div class="wrap">

    <?php do_action('eps_redirects_admin_head'); ?>

    <div class="eps-panel eps-margin-top">
        <h3>Import:</h3>
        <form method="post" action="" class="eps-padding" enctype="multipart/form-data">
            <?php wp_nonce_field('eps_redirect_nonce', 'eps_redirect_nonce_submit'); ?>
            <input accept="csv" type="file" name="eps_redirect_upload_file" value="">
            <input type="submit" name="eps_redirect_upload" id="submit" class="button button-secondary" value="Upload CSV"/>
            <p>
                <input type="radio" name="eps_redirect_upload_method" value="skip" checked="checked"> Skip Duplicates
                &nbsp;&nbsp;&nbsp;<input type="radio" name="eps_redirect_upload_method" value="update"> Update Duplicates
            </p>

            <br><small class="eps-grey-text">Supply Columns: <strong>Status</strong> (301,302,inactive), <strong>Request URL</strong>, <strong>Redirect To</strong> (ID or URL). <a href="<?php echo EPS_REDIRECT_URL . 'example.csv'?>" target="_blank">Download Example CSV</a></small>
        </form>
    </div>

    <div class="eps-panel eps-margin-top">
        <h3>Export:</h3>
        <form method="post" action="">
            <?php wp_nonce_field('eps_redirect_nonce', 'eps_redirect_nonce_submit');   ?>
            <input type="submit" name="eps_redirect_export" id="submit" class="button button-secondary" value="Export Redirects"/>
            <br><small class="eps-grey-text">Export a backup copy of your redirects.</small>
        </form>
    </div>

    <div class="right">
        <?php do_action('eps_redirects_panels_right'); ?>
    </div>
    <div class="left">
        <?php do_action('eps_redirects_panels_left'); ?>
    </div>
</div>