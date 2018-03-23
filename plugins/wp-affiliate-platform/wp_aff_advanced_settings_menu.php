<?php

function wp_aff_advanced_settings_menu_page() {
    $wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();
    if (isset($_POST['update_advanced_settings'])) {
        $wp_aff_platform_config->setValue('wp_aff_enable_tax_form_submission', isset($_POST['wp_aff_enable_tax_form_submission']) ? '1' : '' );
        $tmpmsg = htmlentities(stripslashes($_POST['wp_aff_tax_form_prompt_msg']), ENT_COMPAT, "UTF-8");
        $wp_aff_platform_config->setValue('wp_aff_tax_form_prompt_msg', $tmpmsg);

        $wp_aff_platform_config->setValue('wp_aff_record_verified_aff_clicks', isset($_POST['wp_aff_record_verified_aff_clicks']) ? '1' : '' );
        $wp_aff_platform_config->setValue('wp_aff_record_zero_amt_commission', isset($_POST['wp_aff_record_zero_amt_commission']) ? '1' : '' );

        $wp_aff_platform_config->saveConfig();
        echo '<div id="message" class="updated fade"><p><strong>';
        echo 'Options Updated!';
        echo '</strong></p></div>';
    }

    if (isset($_POST['wpap_management_permission_update'])) {
        $wp_aff_platform_config->setValue('wpap_management_permission', $_POST['wpap_management_permission']);
        $wp_aff_platform_config->saveConfig();
        echo '<div id="message" class="updated fade"><p><strong>';
        echo 'Management permission setting updated!';
        echo '</strong></p></div>';
    }

    $wp_aff_tax_form_prompt_msg = $wp_aff_platform_config->getValue('wp_aff_tax_form_prompt_msg');
    $wp_aff_tax_form_prompt_msg = html_entity_decode($wp_aff_tax_form_prompt_msg, ENT_COMPAT, "UTF-8");
    if (empty($wp_aff_tax_form_prompt_msg)) {//Default msg
        $wp_aff_tax_form_prompt_msg = 'We require you to submit a tax form. Please email your tax form to <EMAIL ADDRESS>';
    }
    ?>

    <p class="wp_affiliate_grey_box">
        These are optional settings that can be handy for some advanced setup.
    </p>

    <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">

        <a name="tax-form"></a>
        <div class="postbox">
            <h3><label for="title">Tax Form Submission Related</label></h3>
            <div class="inside">

                <table width="100%" border="0" cellspacing="0" cellpadding="6">

                    <tr valign="top"><td width="25%" align="left">
                            Prompt for Tax Form Submission
                        </td><td align="left">
                            <input name="wp_aff_enable_tax_form_submission" type="checkbox"  <?php
                            if ($wp_aff_platform_config->getValue('wp_aff_enable_tax_form_submission') == '1') {
                                echo 'checked="checked"';
                            }
                            ?> value="1"/>    
                            <p class="description">Enable this if you want to prompt your affiliates to send you a tax form. When enabled, you will be able to edit an affiliate's record and specify if a tax form has been received from a particular affiliate or not.</p>
                        </td></tr>

                    <tr valign="top"><td width="25%" align="left">
                            Tax Form Prompt Message
                        </td><td align="left">
                            <textarea name="wp_aff_tax_form_prompt_msg" cols="90" rows="6"><?php echo $wp_aff_tax_form_prompt_msg; ?></textarea>
                            <p class="description">This message will be shown to the affiliates who haven't submitted a tax form to you yet. When you receive a tax form from an affiliate, edit the affiliate's record in question and specify that you have received the tax form.</p>
                        </td></tr>

                </table>
            </div></div>

        <a name="misc-advanced-settings"></a>
        <div class="postbox">
            <h3><label for="title">Miscellaneous Advanced Settings</label></h3>
            <div class="inside">

                <table width="100%" border="0" cellspacing="0" cellpadding="6">

                    <tr valign="top"><td width="25%" align="left">
                        Record Verified Affiliate Clicks Only
                        </td><td align="left">
                            <input name="wp_aff_record_verified_aff_clicks" type="checkbox"  <?php
                            if ($wp_aff_platform_config->getValue('wp_aff_record_verified_aff_clicks') == '1') {
                                echo 'checked="checked"';
                            }
                            ?> value="1"/>    
                            <p class="description">Enable this if you want the plugin to check and verify the affiliate ID before recording a click.</p>
                        </td>
                    </tr>

                    <tr valign="top"><td width="25%" align="left">
                        Record $0 Commission
                        </td><td align="left">
                            <input name="wp_aff_record_zero_amt_commission" type="checkbox"  <?php
                            if ($wp_aff_platform_config->getValue('wp_aff_record_zero_amt_commission') == '1') {
                                echo 'checked="checked"';
                            }
                            ?> value="1"/>    
                            <p class="description">By default the plugin won't record the commission if the total commission amount from a transaction is $0.</p>
                        </td>
                    </tr>
                    
                </table>
            </div></div>

        <div class="submit">
            <input type="submit" class="button-primary" name="update_advanced_settings" value="<?php _e('Update options'); ?> &raquo;" />
        </div>

    </form>

    <div class="postbox">
        <h3><label for="title">Admin Dashboard Access Permission</label></h3>
        <div class="inside">

            <p>
                WP Affiliate Platform's admin dashboard is accessible to admin users only (just like any other plugin).
                You can allow users with other WP role to access the affiliate platform's admin dashboard by selecting a value below.
                <br /><br />
                <strong>If don't know what this is for then leave it as it is.</strong>
            </p>
            <?php
            $selected_permission = $wp_aff_platform_config->getValue('wpap_management_permission');
            ?>
            <form method="post" action="">
                <select name="wpap_management_permission">
                    <option <?php echo ($selected_permission == 'manage_options') ? "selected='selected'" : ""; ?> value="manage_options">Admin</option>
                    <option <?php echo ($selected_permission == 'edit_pages') ? "selected='selected'" : ""; ?> value="edit_pages">Editor and Above</option>
                    <option <?php echo ($selected_permission == 'edit_published_posts') ? "selected='selected'" : ""; ?> value="edit_published_posts">Author and Above</option>
                    <option <?php echo ($selected_permission == 'edit_posts') ? "selected='selected'" : ""; ?> value="edit_posts">Contributor and Above</option>
                </select>
                <input type="submit" name="wpap_management_permission_update" class="button" value="Save Permission &raquo" />
            </form>

        </div></div>

    <?php
}
