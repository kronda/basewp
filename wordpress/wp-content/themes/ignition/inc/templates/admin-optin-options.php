<div>
    <label><?php _e("Autoresponder Code", 'thrive'); ?></label>
    <textarea style="width: 100%; height: 200px;" name="thrive_meta_optin_autoresponder_code" id="thrive_meta_optin_autoresponder_code"><?php echo $value_optin_autoresponder_code;?></textarea>
    <br/><br/>
    <input class="button upload" type="button" value="<?php _e('Generate Fields', 'thrive'); ?>" id="thrive_meta_optin_generate_fields" />
    <br/><br/>
    <span id="thrive_meta_txt_message"></span>
    <div id="thrive_container_generated_fields"></div>
</div>