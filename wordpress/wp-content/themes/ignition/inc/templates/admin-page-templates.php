<?php
$privacy_options = array(
    'website' => thrive_get_theme_options('privacy_tpl_website'),
    'company' => thrive_get_theme_options('privacy_tpl_company'),
    'contact' => thrive_get_theme_options('privacy_tpl_contact'),
    'address' => thrive_get_theme_options('privacy_tpl_address'),
);
$privacy_container_style = " style='display:block;'";
$checked_privacy_str = " checked='checked' disabled";
if (empty($privacy_options['website']) && empty($privacy_options['company']) && empty($privacy_options['contact']) && empty($privacy_options['address'])) {
    $privacy_container_style = " style='display:none;'";
    $checked_privacy_str = "";
}
$optins = get_posts(array('post_type' => "thrive_optin", 'posts_per_page' => -1));
?>
<table class="options_table">
    <tr>
        <td class="thrive_options_branding" colspan="2">
            <?php require "partial-share-links.php";?>
        </td>
    </tr>
</table>
<div class="thrive-page-settings ">
    <h3><?php _e("Page Settings", 'thrive'); ?></h3>
    <br/>
    <input type="checkbox" id="thrive_chk_enable_privacy" name="" <?php echo $checked_privacy_str; ?> />
    <span class='ptf'><?php _e("Enable Privacy Policy & Disclamer templates", 'thrive'); ?></span>
    <br/><br/>
    <div id="thrive_conainter_page_tpl_privacy">
        <i>
            <?php _e("By activating this option you agree that you understand that privacy policy and disclaimer templates act as guidelines and do not
        constitute legal pages. You agree that is your responsibility to compile the content for the pages and ensure that they are compliant with
        legal requirements in your jurisdiction.", 'thrive'); ?> 

        </i>    
        <br/><br/>
        <form method="post" action="options.php" class="page-section-options">
            <div>
                <?php
                settings_fields('thrive_options');
                do_settings_sections('theme_page_templates_options');
                submit_button('Update Information');
                ?>
            </div>
        </form>    
        <br/><br/>
    </div>
    <br/>
    <form action="<?php echo admin_url("admin.php?page=thrive_admin_page_templates&noheader=true"); ?>" id="" method="POST">
        <h3>Page templates</h3>
        <br/>
        <table class="page-template-table">
            <tr id="row_page_tpl_privacy" <?php echo $privacy_container_style; ?>>
                <td colspan="2">
                    <input type="checkbox" id="chk_page_tpl_privacy" name="page_tpl_privacy"/>
                    <label class='ptf' for="chk_page_tpl_privacy">Privacy Policy</label>
                </td>
            </tr>
            <tr id="row_page_tpl_disclaimer" <?php echo $privacy_container_style; ?>>
                <td colspan="2">
                    <input type="checkbox" id="chk_page_tpl_disclaimer" name="page_tpl_disclaimer"/>
                    <label class='ptf' for="chk_page_tpl_disclaimer">Disclaimer</label>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <input type="checkbox" id="chk_page_tpl_lead_generation" name="page_tpl_lead_generation"/>
                    <label class="ptf" for="chk_page_tpl_lead_generation">Lead Generation Page</label>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <input type="checkbox" id="chk_page_tpl_lead_generation" name="page_tpl_video_lead_generation"/>
                    <label class='ptf' for="chk_page_tpl_video_lead_generation">Video Lead Generation Page</label>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <input type="checkbox" id="chk_page_tpl_email_confirmation" name="page_tpl_email_confirmation"/>
                    <label class='ptf' for="chk_page_tpl_email_confirmation">Email Confirmation Page</label>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <input type="checkbox" id="chk_page_tpl_thank_you" name="page_tpl_thank_you"/>
                    <label class='ptf' for="chk_page_tpl_thank_you">Thank You Page</label>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <input type="checkbox" id="chk_page_tpl_sales_1" name="page_tpl_sales_1"/>
                    <label class='ptf' for="chk_page_tpl_sales_1">Sales Page Template 1</label>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <input type="checkbox" id="chk_page_tpl_homepage1" name="page_tpl_homepage1"/>
                    <label class='ptf' for="chk_page_tpl_homepage1">Homepage 1</label>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <input type="checkbox" id="chk_page_tpl_homepage2" name="page_tpl_homepage2"/>
                    <label class='ptf' for="chk_page_tpl_homepage2">Homepage 2</label>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div id="container_sel_optin">
                        <?php if (count($optins) == 0): ?>
                            <?php _e("Please add a new Thrive Optin in order to use with this templates: Lead Generation Page, Video Lead Generation Page, Homepage", 'thrive'); ?>
                        <?php else: ?>
                            <?php _e("Please select the optin to use with this templates: Lead Generation Page, Video Lead Generation Page, Homepage", 'thrive'); ?>
                            <select name="thrive_optin">
                                <?php foreach ($optins as $p): ?>
                                    <option value='<?php echo $p->ID ?>'><?php echo $p->post_title; ?></option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <br/>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <input type="checkbox" id="chk_all_tpls"/>
                    <label class='ptf' for="chk_all_tpls"><b>Check all</b></label>
                </td>
            </tr>
            
            <tr <?php if (!defined("TVE_VERSION")):?>style="display:none;"<?php endif;?>>
                <td colspan="2">
                    <input type="checkbox" id="chk-editable-with-tcb" name="editable_with_tcb" value="1" />
                    <label class='ptf' for="editable_with_tcb"><b><?php _e("Make these pages editable with Thrive Content Builder", 'thrive');?></b></label>
                </td>
            </tr>
        </table>
        <br/><br/>
        <hr/>
        <br/>
        <input class="button button-primary" type="submit" value="Generate Selected Pages" />
    </form>
</div>

<script type="text/javascript">
    jQuery(document).ready(function() {
        jQuery("#chk_all_tpls").click(function() {
            var is_checked = jQuery(this).is(':checked');
            jQuery(".page-template-table input:checkbox").each(function(index) {
                if (jQuery(this).attr("id") !== "chk_all_tpls" && jQuery(this).attr("id") !== "chk-editable-with-tcb") {
                    jQuery(this).prop("checked", is_checked);
                }
            });
        });

        jQuery("#thrive_chk_enable_privacy").click(function() {
            jQuery("#thrive_conainter_page_tpl_privacy").toggle();
            jQuery("#row_page_tpl_disclaimer").toggle();
            jQuery("#row_page_tpl_privacy").toggle();
        });
    });
</script>
<script src="https://apis.google.com/js/platform.js" async defer></script>
<script>!function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0], p = /^http:/.test(d.location) ? 'http' : 'https';
        if (!d.getElementById(id)) {
            js = d.createElement(s);
            js.id = id;
            js.src = p + '://platform.twitter.com/widgets.js';
            fjs.parentNode.insertBefore(js, fjs);
        }
    }(document, 'script', 'twitter-wjs');</script>