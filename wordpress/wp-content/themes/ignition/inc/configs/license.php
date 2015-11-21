<?php

function thrive_license_check($licensed_email, $license_key = '')
{
    $api_url = "https://thrivethemes.com/wp-content/plugins/license_check/api/request.php";
    $api_url .= "?license=" . $license_key;
    $api_url .= "&email=" . $licensed_email;
    $api_url .= "&product_id=4,5,6,7,13,14,37,38,39,40,41,42,43";
    $licenseValid = wp_remote_get($api_url, array('sslverify' => false));

    if (is_wp_error($licenseValid)) {
        /** @var WP_Error $licenseValid */
        /** Couldn't connect to the API URL - possible because wp_remote_get failed for whatever reason.  Maybe CURL not activated on server, for instance */
        $response = new stdClass();
        $response->success = 0;
        $response->reason = sprintf(__("An error occurred while connecting to the license server. Error: %s. Please login to thrivethemes.com, report this error message on the forums and we'll get this sorted for you", 'thrive'), $licenseValid->get_error_message());

        return $response;
    }

    $response = @json_decode($licenseValid['body']);

    if (empty($response)) {
        $response = new stdClass();
        $response->success = 0;
        $response->reason = sprintf(__("An error occurred while receiving the license status. The response was: %s. Please login to thrivethemes.com, report this error message on the forums and we'll get this sorted for you.", 'thrive'), $licenseValid['body']);

        return $response;
    }

    return $response;
}

/** retrospectively modify license status to remove liceense details from db once activated **/
if (get_option("thrive_license_status") == "ACTIVE") {
    update_option('thrive_license_email', "License Activated");
    update_option('thrive_license_key', "License Activated");
}

if (isset($_POST['thrive_license_email']) && isset($_POST['thrive_license_key'])) {
    update_option('thrive_license_email', trim($_POST['thrive_license_email']));
    update_option('thrive_license_key', trim($_POST['thrive_license_key']));

    $validate = thrive_license_check(get_option('thrive_license_email'), get_option('thrive_license_key'));

    // license validated
    if (isset($validate->success) && $validate->success == 1) {
        update_option('thrive_license_status', "ACTIVE");
        update_option('thrive_license_email', "License Activated");
        update_option('thrive_license_key', "License Activated");

        ?>
        <div id="message" class="updated" style="margin: 0px; margin-top: 10px; font-weight: bold;">
            <p>Thank you - You have successfully validated your license!</p>
        </div>

        <?php
        // some kind of error
    } elseif ($validate->success == 0) {
        ?>
        <div id="message" class="error" style="margin: 0px; margin-top: 10px; font-weight: bold;">
            <p><?php echo $validate->reason; ?></p>
        </div>
    <?php
    } // big time error - fatal error
    else {
        ?>

        <div id="message" class="error" style="margin: 0px; margin-top: 10px; font-weight: bold;">
            <p>License activation error - please contact support copying this message and we'll get this sorted for
                you.</p>
        </div>

    <?php
    }
}
?>

<div class="wpbootstrap">
    <div>
        <center>
            <img src="<?php echo get_template_directory_uri(); ?>/inc/images/TT-logo-small.png"
                 class="thrive_admin_logo"/>
        </center>
        <form method="post" class="form-horizontal">
            <div id="facebook" class="panel like-panel">
                <h2 style="margin-bottom: 10px;">Validate your License:</h2>

                <fieldset style="padding-top: 10px;">
                    <div class="control-group">
                        <label class="control-label" for="thrive_license_email">Email Address:</label>

                        <div class="controls">
                            <input type="text" class="short" name="thrive_license_email" id="thrive_license_email"
                                   value="<?php if (get_option('thrive_license_email', 0)): echo get_option('thrive_license_email'); endif; ?>"
                                   style="width: 270px;"/>
                            <br/><br/>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="thrive_license_key">License Key</label>

                        <div class="controls">
                            <input type="text" class="short" name="thrive_license_key" id="thrive_license_key"
                                   value="<?php if (get_option('thrive_license_key', 0)): echo get_option('thrive_license_key'); endif; ?>"
                                   style="width: 270px;"/>
                        </div>
                    </div>
                </fieldset>

                <div class="form-actions">
                    <br/><br/>
                    <input name="save-action" class="button-primary" type="submit" value="Activate License"/>
                </div>
            </div>
            <div style="clear: both;"></div>
    </div>
    </form>
</div>

<style type="text/css">.wpbootstrap {
        text-align: center;
        margin: 0 auto;
        width: 400px;
        padding: 40px;
        margin-top: 50px;
        -moz-border-radius-bottomleft: 10px;
        -webkit-border-bottom-left-radius: 10px;
        border-bottom-left-radius: 10px;
        -moz-border-radius-bottomright: 10px;
        -webkit-border-bottom-right-radius: 10px;
        border-bottom-right-radius: 10px;
        border-bottom: 1px solid #bdbdbd;
        background-size: 100%;
        background-image: -webkit-gradient(linear, 50% 0%, 50% 100%, color-stop(20%, #ffffff), color-stop(100%, #e6e6e6));
        background-image: -webkit-linear-gradient(top, #fdfdfd 20%, #e6e6e6 100%);
        background-image: -moz-linear-gradient(top, #fdfdfd 20%, #e6e6e6 100%);
        background-image: -o-linear-gradient(top, #fdfdfd 20%, #e6e6e6 100%);
        background-image: linear-gradient(top, #fdfdfd 20%, #e6e6e6 100%);
        -moz-border-radius: 10px;
        -webkit-border-radius: 10px;
        border-radius: 10px;
        -webkit-box-shadow: 2px 5px 3px #efefef;
        -moz-box-shadow: 2px 5px 3px #efefef;
        box-shadow: 2px 5px 3px #efefef;
    }
</style>