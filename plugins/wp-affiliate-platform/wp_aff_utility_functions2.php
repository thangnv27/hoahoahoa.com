<?php

function wpap_is_affiliate_admin_page() {
    if (!isset($_GET['page'])) {
        return false;
    }
    $current_page = $_GET['page'];
    $wp_aff_admin_pages = array(
        'wp-affiliate-platform/wp_affiliate_platform1.php',
        'wp_aff_platform_settings',
        'affiliates',
        'affiliates_addedit',
        'manage_banners',
        'edit_banners',
        'manage_leads',
        'clickthroughs',
        'aff_sales',
        'manage_payouts',
        'payouts_history',
    );
    return in_array($current_page, $wp_aff_admin_pages);
}

function wp_aff_send_welcome_email($aff_id, $to_email, $pass) {
    $email_subj = get_option('wp_aff_signup_email_subject');
    $body_sign_up = get_option('wp_aff_signup_email_body');
    $from_email_address = get_option('wp_aff_senders_email_address');
    $headers = 'From: ' . $from_email_address . "\r\n";

    $additional_params = array();
    $additional_params['password'] = $pass;//$_POST['wp_aff_pwd'];
    $aemailbody = wp_aff_dynamically_replace_affiliate_details_in_message($aff_id, $body_sign_up, $additional_params);
    
    wp_mail($to_email, $email_subj, $aemailbody, $headers);
    wp_affiliate_log_debug("Welcome email successfully sent to the affiliate: ".$to_email,true);
    
}

function wp_aff_send_manual_approval_email($aff_id, $to_email)
{
    $wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();
    
    if ($wp_aff_platform_config->getValue('wp_aff_notify_account_approval') != '1'){
        wp_affiliate_log_debug("Manual approval notification is turned off in the settings. No email will be sent to the affiliate.",true);
        return;
    }
    
    $email_subj = $wp_aff_platform_config->getValue('wp_aff_approval_notif_email_subject');
    $email_body = $wp_aff_platform_config->getValue('wp_aff_approval_notif_email_body');
    $from_email_address = get_option('wp_aff_senders_email_address');
    $headers = 'From: ' . $from_email_address . "\r\n";

    $additional_params = array();
    $email_body = wp_aff_dynamically_replace_affiliate_details_in_message($aff_id, $email_body, $additional_params);
    
    wp_mail($to_email, $email_subj, $email_body, $headers);
    wp_affiliate_log_debug("Manual approval email successfully sent to the affiliate: ".$to_email,true);    
}
