<?php

function wp_aff_email_settings()
{
    $wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();
    if (isset($_POST['info_update']))
    {
        update_option('wp_aff_notify_affiliate_for_commission', ($_POST['wp_aff_notify_affiliate_for_commission']!='') ? 'checked="checked"':'' );
        $wp_aff_platform_config->setValue('wp_aff_notify_admin_for_commission', ($_POST['wp_aff_notify_admin_for_commission']=='1') ? '1':'');
        $wp_aff_platform_config->setValue('wp_aff_notify_account_approval', ($_POST['wp_aff_notify_account_approval']=='1') ? '1':'');
        
        update_option('wp_aff_senders_email_address', stripslashes((string)$_POST["wp_aff_senders_email_address"]));
        update_option('wp_aff_signup_email_subject', stripslashes((string)$_POST["wp_aff_signup_email_subject"]));
        update_option('wp_aff_signup_email_body', stripslashes((string)$_POST["wp_aff_signup_email_body"]));

        $wp_aff_platform_config->setValue('wp_aff_comm_notif_senders_address', stripslashes((string)$_POST["wp_aff_comm_notif_senders_address"]));
        $wp_aff_platform_config->setValue('wp_aff_comm_notif_email_subject', stripslashes((string)$_POST["wp_aff_comm_notif_email_subject"]));
        $wp_aff_platform_config->setValue('wp_aff_comm_notif_email_body', stripslashes((string)$_POST["wp_aff_comm_notif_email_body"]));        
        
        $wp_aff_platform_config->setValue('wp_aff_admin_comm_notif_email_subject', stripslashes((string)$_POST["wp_aff_admin_comm_notif_email_subject"]));
        $wp_aff_platform_config->setValue('wp_aff_admin_comm_notif_email_body', stripslashes((string)$_POST["wp_aff_admin_comm_notif_email_body"]));   

        $wp_aff_platform_config->setValue('wp_aff_approval_notif_email_subject', stripslashes((string)$_POST["wp_aff_approval_notif_email_subject"]));
        $wp_aff_platform_config->setValue('wp_aff_approval_notif_email_body', stripslashes((string)$_POST["wp_aff_approval_notif_email_body"])); 
        
        $wp_aff_platform_config->saveConfig();
        echo '<div id="message" class="updated fade"><p><strong>';
        echo 'Options Updated!';
        echo '</strong></p></div>';
    }
    
    if (get_option('wp_aff_notify_affiliate_for_commission'))
        $wp_aff_notify_affiliate_for_commission = 'checked="checked"';
    else
        $wp_aff_notify_affiliate_for_commission = '';
    
    $wp_aff_senders_address = get_option('wp_aff_senders_email_address');
    if (empty($wp_aff_senders_address))
    {
    	$wp_aff_senders_address = get_bloginfo('name')." <".get_option('admin_email').">";
    	update_option('wp_aff_senders_email_address',$wp_aff_senders_address);
    }
    $wp_aff_signup_email_subject = get_option('wp_aff_signup_email_subject');
    if (empty($wp_aff_signup_email_subject))
    {
    	$wp_aff_signup_email_subject = "Affiliate Login Details";
    	update_option('wp_aff_signup_email_subject',$wp_aff_signup_email_subject);
    }

    $wp_aff_signup_email_body = get_option('wp_aff_signup_email_body');
    if (empty($wp_aff_signup_email_body))
    {
		$wp_aff_signup_email_body = "Thank you for registering with us. Here are your login details...\n".        
        "\nAffiliate ID: {user_name}".
        "\nEmail: {email} \n".
        "\nPasswd: {password} \n".
        "\nYou can Log into the system at the following URL:\n{login_url}\n".           
        "\nPlease log into your account to get banners and view your real-time statistics.\n".        
        "\nThank You".
        "\nAdministrator".
        "\n______________________________________________________".
        "\nTHIS IS AN AUTOMATED RESPONSE. ".
        "\n***DO NOT RESPOND TO THIS EMAIL****";

		update_option('wp_aff_signup_email_body',$wp_aff_signup_email_body);
    }  
    
    $notif_email_from_address = $wp_aff_platform_config->getValue('wp_aff_comm_notif_senders_address');
    if(empty($notif_email_from_address)){
            $wp_aff_platform_config->setValue('wp_aff_comm_notif_senders_address',$wp_aff_senders_address);
            $wp_aff_platform_config->saveConfig();
    }
    
    $wp_aff_admin_comm_notif_email_subject = $wp_aff_platform_config->getValue('wp_aff_admin_comm_notif_email_subject');
    if (empty($wp_aff_admin_comm_notif_email_subject)){
    	$wp_aff_admin_comm_notif_email_subject = "Affiliate commission notification";
    }
    $wp_aff_admin_comm_notif_email_body = $wp_aff_platform_config->getValue('wp_aff_admin_comm_notif_email_body');
    if (empty($wp_aff_admin_comm_notif_email_body)){
    	$wp_aff_admin_comm_notif_email_body = "This is an auto-generated email letting you know that one of your affiliates has earned a commission. You can log into your WordPress admin dashboard and get more details about this transaction.";
    }
    
    $wp_aff_approval_notif_email_subject = $wp_aff_platform_config->getValue('wp_aff_approval_notif_email_subject');
    if (empty($wp_aff_approval_notif_email_subject)){
    	$wp_aff_approval_notif_email_subject = "Affiliate Account Approved";
    }
    $wp_aff_approval_notif_email_body = $wp_aff_platform_config->getValue('wp_aff_approval_notif_email_body');
    if (empty($wp_aff_approval_notif_email_body)){
    	$wp_aff_approval_notif_email_body = "Hi, Your affiliate account has been approved. You can now log into the affiliate portal and start promoting.";
    }
    
    ?>
    <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
    <input type="hidden" name="info_update" id="info_update" value="true" />

    <div class="postbox">
    <h3><label for="title">Notification Preferences</label></h3>
    <div class="inside">

    <table width="100%" border="0" cellspacing="0" cellpadding="6">

    <tr valign="top"><td width="25%" align="left">
    <strong>Send Commission Notification:</strong>
    </td><td align="left">
    <input type="checkbox" name="wp_aff_notify_affiliate_for_commission" value="1" <?php echo $wp_aff_notify_affiliate_for_commission; ?> />
    Send Notification to Affiliates
    <br /><i>Check this box if you want your affiliates to get notified via email when they receive a commission.</i>
    <br /><br />
    <input type="checkbox" name="wp_aff_notify_admin_for_commission" value="1" <?php if($wp_aff_platform_config->getValue('wp_aff_notify_admin_for_commission')=='1'){echo 'checked="checked"';} ?> />
    Send Notification to Admin
    <br /><i>Check this box if you want the admin of this site to get notified via email when an affiliate receives a commission.</i><br />    
    </td></tr>  
        
    <tr valign="top"><td width="25%" align="left">
    <strong>Send Account Approval Notification:</strong>
    </td><td align="left">
    <input type="checkbox" name="wp_aff_notify_account_approval" value="1" <?php if($wp_aff_platform_config->getValue('wp_aff_notify_account_approval')=='1'){echo 'checked="checked"';} ?> />
    <br /><i>Check this box if you want to notify your affiliates when you approve their account. This is only applicable if you are using the manual approval option.</i>
    </td></tr>
    
    </table>
    </div></div>
    
    <div class="postbox">
    <h3><label for="title">Affiliate Signup Email</label></h3>
    <div class="inside">

    <table width="100%" border="0" cellspacing="0" cellpadding="6">

    <tr valign="top"><td width="25%" align="left">
    From Email Address
    </td><td align="left">
    <input name="wp_aff_senders_email_address" type="text" size="60" value="<?php echo get_option('wp_aff_senders_email_address'); ?>"/>
    <br /><i>Sender's address (eg. Your Name &lt;admin@your-domain.com&gt;)</i><br /><br />
    </td></tr>

    <tr valign="top"><td width="25%" align="left">
    Email Subject
    </td><td align="left">
    <input name="wp_aff_signup_email_subject" type="text" size="60" value="<?php echo $wp_aff_signup_email_subject; ?>"/>
    <br /><i>The Email Subject</i><br /><br />
    </td></tr>

    <tr valign="top"><td width="25%" align="left">
    The Email Body
    </td><td align="left">
    <textarea name="wp_aff_signup_email_body" cols="60" rows="6"><?php echo $wp_aff_signup_email_body; ?></textarea>
    <br /><i>This is the body of the email that will be sent to the affiliate after they sign up. Do not change the text within the braces {}</i><br /><br />
    </td></tr>
    
    </table>
    </div></div>
    
    <div class="postbox">
    <h3><label for="title">Affiliate Commission Notification Email</label></h3>
    <div class="inside">

<?php 
if (get_option('wp_aff_notify_affiliate_for_commission')){
?>	
    <table width="100%" border="0" cellspacing="0" cellpadding="6">

    <tr valign="top"><td width="25%" align="left">
    From Email Address
    </td><td align="left">
    <input name="wp_aff_comm_notif_senders_address" type="text" size="60" value="<?php echo $wp_aff_platform_config->getValue('wp_aff_comm_notif_senders_address'); ?>"/>
    <br /><i>Sender's email address that will be used in the commission notification email (example, Your Name &lt;admin@your-domain.com&gt;)</i><br /><br />
    </td></tr>

    <tr valign="top"><td width="25%" align="left">
    Commission Notification Email Subject
    </td><td align="left">
    <input name="wp_aff_comm_notif_email_subject" type="text" size="60" value="<?php echo $wp_aff_platform_config->getValue('wp_aff_comm_notif_email_subject'); ?>"/>
    <br /><i>The email subject of the commission notification email</i><br /><br />
    </td></tr>

    <tr valign="top"><td width="25%" align="left">
    Commission Notification Email Body
    </td><td align="left">
    <textarea name="wp_aff_comm_notif_email_body" cols="60" rows="6"><?php echo $wp_aff_platform_config->getValue('wp_aff_comm_notif_email_body'); ?></textarea>
    <br /><i>This is the body of the email that will be sent to the affiliates when they receive a commission</i><br /><br />
    </td></tr>
    
    </table>
<?php 
}
else{
	echo '<p>Enable the Send Commission Notification to Affiliates feature from the notification preferences to use this option.</p>';
}
?>
    </div></div>

    <div class="postbox">
    <h3><label for="title">Commission Notification Email for Admin</label></h3>
    <div class="inside">

<?php 
if ($wp_aff_platform_config->getValue('wp_aff_notify_admin_for_commission') == '1'){
?>
    <table width="100%" border="0" cellspacing="0" cellpadding="6">

    <tr valign="top"><td width="25%" align="left">
    Notification Email Subject
    </td><td align="left">
    <input name="wp_aff_admin_comm_notif_email_subject" type="text" size="60" value="<?php echo $wp_aff_admin_comm_notif_email_subject; ?>"/>
    <br /><i>The email subject of the commission notification email that will be sent to the admin.</i><br /><br />
    </td></tr>

    <tr valign="top"><td width="25%" align="left">
    Notification Email Body
    </td><td align="left">
    <textarea name="wp_aff_admin_comm_notif_email_body" cols="60" rows="6"><?php echo $wp_aff_admin_comm_notif_email_body; ?></textarea>
    <br /><i>This is the body of the email that will be sent to the admin when an affiliate receives a commission</i><br /><br />
    </td></tr>
    
    </table>
<?php 
}
else{
    echo '<p>Enable the Send Commission Notification to Admin feature from the notification preferences to use this option.</p>';
}
?>
    </div></div>
    
    <div class="postbox">
    <h3><label for="title">Manual Account Approval Notification Email</label></h3>
    <div class="inside">

<?php 
if ($wp_aff_platform_config->getValue('wp_aff_notify_account_approval') == '1'){
?>
    <table width="100%" border="0" cellspacing="0" cellpadding="6">

    <tr valign="top"><td width="25%" align="left">
    Notification Email Subject
    </td><td align="left">
    <input name="wp_aff_approval_notif_email_subject" type="text" size="60" value="<?php echo $wp_aff_approval_notif_email_subject; ?>"/>
    <br /><i>The email subject of the manual account approval email.</i><br /><br />
    </td></tr>

    <tr valign="top"><td width="25%" align="left">
    Notification Email Body
    </td><td align="left">
    <textarea name="wp_aff_approval_notif_email_body" cols="60" rows="6"><?php echo $wp_aff_approval_notif_email_body; ?></textarea>
    <br /><i>This is the body of the email that will be sent to the affiliate when you approve an affiliate's account.</i><br /><br />
    </td></tr>
    
    </table>
<?php 
}
else{
    echo '<p>Enable the Send Account Approval Notification feature from the notification preferences to use this option.</p>';
}
?>
    </div></div>
    
    <div class="submit">
        <input type="submit" class="button-primary" name="info_update" value="<?php _e('Update options'); ?> &raquo;" />
    </div>

    </form>    
    <?php
    
}
