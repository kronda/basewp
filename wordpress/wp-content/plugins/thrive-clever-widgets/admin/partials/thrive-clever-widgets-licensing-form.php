<?php

/* @var $this Thrive_Clever_Widgets_Manager_Admin */

/**
 * retrospectively modify license status to remove license details from db once activated
 */
if (get_option("tcw_license_status") == "ACTIVE") {
    update_option('tcw_license_email', "License Activated");
    update_option('tcw_license_key', "License Activated");
}

if (isset($_POST['tcw_license_email']) && isset($_POST['tcw_license_key'])) {

    update_option('tcw_license_email', trim($_POST['tcw_license_email']));
    update_option('tcw_license_key', trim($_POST['tcw_license_key']));

    $response = $this->license_check(get_option('tcw_license_email'), get_option('tcw_license_key'));

    if (isset($response->success) && $response->success == 1) { //valid license
        update_option('tcw_license_status', "ACTIVE");
        update_option('tcw_license_email', "License Activated");
        update_option('tcw_license_key', "License Activated");
        ?>
        <div id="tcw_message" class="updated"><p><?php echo __("Thank you - You have successfully validated your license!", "thrive-cw")?></p></div>
    <?php
    } else if ($response->success == 0) { //invalid license
        ?>
        <div id="tcw_message" class="error"><p><?php echo $response->reason; ?></p></div>
    <?php
    } else { //something wrong
        ?>
        <div id="tcw_message" class="error"><p><?php echo __("License activation error - please contact support copying this message and we'll get this sorted for you.", "thrive-cw")?></p></div>
    <?php
    }
}

?>

<div class="wpbootstrap">
    <div>
        <div class="tcw_logo">
            <img src="<?php echo plugin_dir_url(dirname(__FILE__)) . 'css/images/thrive-clever-widgets-large-logo.png'; ?>" alt="Thrive Clever Widgets License">
        </div>
        <form method="post" class="form-horizontal"
              action="<?php echo admin_url('options-general.php?page=tcw_license_validation') ?>">
            <div id="facebook" class="panel like-panel">
                <h2 style="margin-bottom: 10px;"><?php echo __("Validate your License:", "thrive-cw") ?></h2>
                <fieldset style="padding-top: 10px;border-width: 0">
                    <div class="control-group">
                        <label class="control-label" for="tcw_license_email"><?php echo __("Email Address:", "thrive-cw") ?></label>

                        <div class="controls">
                            <input type="text" class="short" name="tcw_license_email" id="tcw_license_email"
                                   value="<?php if (get_option('tcw_license_email', 0)): echo get_option('tcw_license_email'); endif; ?>"
                                   style="width: 270px;"/> <br/><br/>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="tcw_license_key">License Key:</label>

                        <div class="controls">
                            <input type="text" class="short" name="tcw_license_key" id="tcw_license_key"
                                   value="<?php if (get_option('tcw_license_key', 0)): echo get_option('tcw_license_key'); endif; ?>"
                                   style="width: 270px;"/>
                        </div>
                    </div>
                </fieldset>
                <div class="form-actions">
                    <br/><br/> <input name="save-action" class="button-primary" type="submit" value="Activate License"/>
                </div>
            </div>
            <div style="clear: both;"></div>
        </form>
    </div>
</div>

<style type="text/css">
    .wpbootstrap {
        text-align: center;
        margin: 50px auto 0 auto;
        width: 400px;
        padding: 40px;
        border-bottom: 1px solid #bdbdbd;
        background-image: url('data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgi…3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSJ1cmwoI2dyYWQpIiAvPjwvc3ZnPiA=');
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

    .wpbootstrap .tcw_logo {
        text-align: center;
    }

    #tcw_message {
        margin: 10px 0 0 0;
        font-weight: bold;
    }
</style>
