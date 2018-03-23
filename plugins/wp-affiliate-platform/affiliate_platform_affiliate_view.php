<?php

function wp_affiliate_view2_handler() {
    include_once('views/style2/affiliate_platform_affiliate_view2.php');
    return affiliate_platform_affiliate_view_2_main();
}

function wp_affiliate_view_handler() {
    return affiliate_platform_affiliate_view_main();
}

function affiliate_platform_affiliate_view_main() {
    wp_aff_load_affiliate_view_css();
    $output = "";
    if (wp_aff_view_is_logged_in()) {
        $action = isset($_GET['wp_affiliate_view']) ? $_GET['wp_affiliate_view'] : '';
        switch ($action) {
            case 'members_only':
                include_once('views/members_only_view.php');
                $output .= wp_aff_members_only_view();
                break;
            case 'details':
                include_once('views/details_view.php');
                $output .= wp_aff_details_view();
                break;
            case 'clicks':
                include_once('views/referrals_view.php');
                $output .= wp_aff_referrals_view();
                break;
            case 'sub-affiliates':
                include_once('views/sub_affiliates_view.php');
                $output .= wp_aff_sub_affiliates_view();
                break;
            case 'leads':
                include_once('views/leads_view.php');
                $output .= wp_aff_leads_view();
                break;
            case 'sales':
                include_once('views/sales_view.php');
                $output .= wp_aff_sales_view();
                break;
            case 'payments':
                include_once('views/payments_view.php');
                $output .= wp_aff_payment_history_view();
                break;
            case 'ads':
                include_once('views/ads_view.php');
                $output .= wp_aff_ads_view();
                break;
            case 'creatives':
                include_once('views/creatives_view.php');
                $output .= wp_aff_creatives_view();
                break;
            case 'link_generation':
                include_once('views/link_generation_view.php');
                $output .= wp_aff_link_generation_view();
                break;
            case 'contact':
                include_once('views/contact_view.php');
                $output .= wp_aff_contact_view();
                break;
            case 'logout':
                //see the code in "wp_affiliate_platform1.php" file
                break;
            default:
                include_once('views/members_only_view.php');
                $output .= wp_aff_members_only_view();
                break;
        }
    } else {
        $action = isset($_GET['wp_affiliate_view']) ? $_GET['wp_affiliate_view'] : '';
        switch ($action) {
            case 'login':
                include_once('views/login_view.php');
                $output .= wp_aff_login_view();
                break;
            case 'signup':
                include_once('views/register_view.php');
                $output .= wp_aff_register_view();
                break;
            case 'forgot_pass':
                include_once('views/forgot_pass_view.php');
                $output .= wp_aff_forgot_pass_view();
                break;
            case 'thankyou':
                include_once('views/thankyou_view.php');
                $output .= wp_aff_thankyou_view();
                break;
            default:
                $output .= wp_aff_view_main_index();
                break;
        }
    }
    return $output;
}

function wp_aff_load_affiliate_view_css() {
    echo '<link type="text/css" rel="stylesheet" href="' . WP_AFF_PLATFORM_URL . '/views/affiliate_view.css" />' . "\n";
}

function wp_aff_view_main_index() {
    $wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();
    $login_url = wp_aff_view_get_url_with_separator("login");
    $signup_url = wp_aff_view_get_url_with_separator("signup");

    $output = "";
    $output .= wp_aff_view_get_navbar();
    $output .= '<div id="wp_aff_inside">';

    $wp_aff_index_title = $wp_aff_platform_config->getValue('wp_aff_index_title');

    $output .= '<h3 class="wp_aff_title">' . $wp_aff_index_title . '</h3>';

    $output .= '<div id="aff-box-content">';

    $output .= '<div class="wp-aff-box">
    <img src="' . WP_AFF_PLATFORM_URL . '/affiliates/images/aff-signup-icon-96.png" class="center" alt="Affiliate Sign up icon" />
    <a href="' . $signup_url . '">    
    <div id="aff-box-action">
        <div class="aff-signup-text">' . AFF_SIGN_UP . '</div>
    </div>
    </a>
    </div>';

    $output .= '<div class="wp-aff-box">
    <img src="' . WP_AFF_PLATFORM_URL . '/affiliates/images/aff-login-icon-96.png" class="center" alt="Affiliate Login icon" />
    <a href="' . $login_url . '">    
    <div id="aff-box-action">        
        <div class="aff-login-text">' . AFF_LOGIN . '</div>
    </div>
    </a>
    </div>';

    $output .= '<div class="wp_aff_clear"></div>
    </div>'; //end of #aff-box-content

    $wp_aff_index_body_tmp = $wp_aff_platform_config->getValue('wp_aff_index_body');
    $wp_aff_index_body = html_entity_decode($wp_aff_index_body_tmp, ENT_COMPAT, "UTF-8");
    $wp_aff_index_body = apply_filters('the_content', $wp_aff_index_body);
    $output .= '<div id="wp_aff-index-body">' . $wp_aff_index_body . '</div>';
    $output .= '<div class="wp_aff_clear"></div>';

    $output .= '</div>';
    $output .= wp_aff_view_get_footer();
    return $output;
}

function wp_aff_view_get_navbar() {
    $output = "";
    if (wp_aff_view_is_logged_in()) {
        $separator = '?';
        $url = get_permalink();
        if (strpos($url, '?wp_affiliate_view=')) {
            $separator = '?';
        } else if (strpos($url, '?') !== false) {
            $separator = '&';
        }
        $output .= '<div id="wp_aff_nav"><ul>';
        $output .= '<li><a href="' . $url . $separator . 'wp_affiliate_view=members_only">' . AFF_NAV_HOME . '</a></li>';
        $output .= '<li><a href="' . $url . $separator . 'wp_affiliate_view=details">' . AFF_NAV_EDIT_PROFILE . '</a></li>';
        $output .= '<li><a href="' . $url . $separator . 'wp_affiliate_view=clicks">' . AFF_NAV_REFERRALS . '</a></li>';
        $output .= '<li><a href="' . $url . $separator . 'wp_affiliate_view=sales">' . AFF_NAV_SALES . '</a></li>';
        $output .= '<li><a href="' . $url . $separator . 'wp_affiliate_view=payments">' . AFF_NAV_PAYMENT_HISTORY . '</a></li>';
        $output .= '<li><a href="' . $url . $separator . 'wp_affiliate_view=ads">' . AFF_NAV_ADS . '</a></li>';
        $output .= '<li><a href="' . $url . $separator . 'wp_affiliate_view=contact">' . AFF_NAV_CONTACT . '</a></li>';
        $output .= '<li><a href="' . $url . $separator . 'wp_affiliate_view=logout">' . AFF_NAV_LOGOUT . '</a></li>';
        $output .= '</ul></div>';
        $output .= '<div class="wp_aff_clear"></div>';
        $output .= wp_aff_area_user_notice();
        return $output;
    }
    return $output;
}

function wp_aff_area_user_notice() {
    global $wpdb;
    $output = '';
    $notice = '';
    $wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();
    $affiliates_table_name = WP_AFF_AFFILIATES_TBL_NAME;

    if ($wp_aff_platform_config->getValue('wp_aff_enable_tax_form_submission') == '1') {
        $current_aff = $wpdb->get_row("SELECT * FROM $affiliates_table_name WHERE refid = '" . $_SESSION['user_id'] . "'", OBJECT);
        if ($current_aff->tax_form_submitted != '1') {//This affiliate hasn't submitted tax form yet
            $wp_aff_tax_form_prompt_msg = $wp_aff_platform_config->getValue('wp_aff_tax_form_prompt_msg');
            $wp_aff_tax_form_prompt_msg = html_entity_decode($wp_aff_tax_form_prompt_msg, ENT_COMPAT, "UTF-8");
            $notice .= '<div class="wp_aff_user_notice_tax_form_required">';
            $notice .= $wp_aff_tax_form_prompt_msg;
            $notice .= '</div>';
        }
    }

    if (!empty($notice)) {//There is some notice to be displayed
        $output .= '<div class="wp_aff_user_notice_section">';
        $output .= $notice;
        $output .= '</div>';
    }
    return $output;
}

function wp_aff_view_get_url_with_separator($name_value_data, $url = '') {
    $separator = '?';
    if (empty($url)) {
        //$url=wp_aff_current_page_url();
        $url = get_permalink();
        if (empty($url)) {
            $current_url = wp_aff_current_page_url();
            $position = strpos($current_url, 'wp_affiliate_view=');
            $url = substr_replace($current_url, '', $position - 1);
        }
    }
    if (strpos($url, '?wp_affiliate_view=')) {
        $separator = '?';
    } else if (strpos($url, '?') !== false) {
        $separator = '&';
    }
    $full_url = $url . $separator . 'wp_affiliate_view=' . $name_value_data;
    return $full_url;
}

function wp_aff_view_get_footer() {
    $wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();
    $output = "";
    if ($wp_aff_platform_config->getValue('wp_aff_do_not_show_powered_by_section') != '1') {
        $output .= '<div id="wp_aff_footer">';
        $aff_id = get_option('wp_aff_user_affilate_id');
        if (!empty($aff_id)) {
            $output .= '<div style="float:right;">Powered by&nbsp;&nbsp;<a target="_blank" href="https://www.tipsandtricks-hq.com/?p=1474&ap_id=' . $aff_id . '">WP Affiliate Platform</a></div>';
        } else {
            $output .= '<div style="float:right;">Powered by&nbsp;&nbsp;<a target="_blank" href="https://ppo.vn/affiliate">WP Affiliate Platform</a></div>';
        }
        $output .= '<div class="wp_aff_clear"></div>';
        $output .= '</div>';
    }
    return $output;
}

function wp_aff_view_is_logged_in() {
    return wp_aff_is_logged_in();
}

function wp_aff_platform_redirect($url, $time = 0) {
    echo "<META HTTP-EQUIV=\"Refresh\" CONTENT=\"$time;URL=$url\">";
    echo "If you are not redirected within a few seconds then please click <a class=leftLink href=$url>" . here . '</a>';
}

/* * * One page affiliate form processor ** */

function wp_aff_view2_signup_form_processing_code() {
    unset($_SESSION['wp_aff_signup_success_msg']);
    if (isset($_POST['wpAffDoRegister'])) {
        global $wpdb;
        $wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();
        $affiliates_table_name = WP_AFF_AFFILIATES_TBL_NAME;

        //Sanitize the POST data
        $post_data = $_POST;
        $_POST = array_map('strip_tags', $post_data); //$_POST = filter_var_array($_POST, FILTER_SANITIZE_STRING);

        $login_url = wp_aff_view_get_url_with_separator("login");
        if (get_option('wp_aff_use_recaptcha')) {
            //Recaptcha was shown to the user to lets check the response
            include_once(WP_AFF_PLATFORM_PATH . 'lib/recaptchalib.php');
            $secret = get_option('wp_aff_captcha_private_key'); //Secret

            $reCaptcha = new WPAP_ReCaptcha($secret);
            $resp = $reCaptcha->verifyResponse($_SERVER["REMOTE_ADDR"], $_REQUEST["g-recaptcha-response"]);
            if ($resp != null && $resp->success) {
                //Valid reCAPTCHA response. Go ahead with the registration
            } else {
                //Invalid response. Stop going forward. Set the error msg so the form shows it to the user.
                $recaptcha_error = AFF_IMAGE_VERIFICATION_FAILED;
                $_GET['aff_signup_error_msg'] = AFF_IMAGE_VERIFICATION_FAILED;
                return;
            }
        }

        //=================
        include_once(ABSPATH . WPINC . '/class-phpass.php');
        $wp_hasher = new PasswordHash(8, TRUE);
        $password = $wp_hasher->HashPassword($_POST['wp_aff_pwd']);

        $user_ip = wp_aff_get_user_ip();
        $host = $_SERVER['HTTP_HOST'];
        $host_upper = strtoupper($host);
        $path = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
        $activ_code = rand(1000, 9999);
        $aemail = esc_sql($_POST['aemail']);
        $user_name = esc_sql($_POST['user_name']);
        //============

        $userid = esc_sql($_POST['user_name']);

        $result = $wpdb->get_results("SELECT refid FROM $affiliates_table_name where refid='$userid'", OBJECT);
        if ($result) {
            $_GET['aff_signup_error_msg'] = AFF_SI_USEREXISTS;
            return;
        }
        // check if referred by another affiliate
        $referrer = "";
        if (!empty($_SESSION['ap_id'])) {
            $referrer = $_SESSION['ap_id'];
        } else if (isset($_COOKIE['ap_id'])) {
            $referrer = $_COOKIE['ap_id'];
        }

        $commission_level = get_option('wp_aff_commission_level');
        $date = (date("Y-m-d"));
        $account_details = "";
        $sec_tier_commissionlevel = "";
        if (!isset($_POST['apayable']))
            $_POST['apayable'] = "";

        $account_status = 'approved';
        if ($wp_aff_platform_config->getValue('wp_aff_enable_manual_signup_approval') == '1') {
            wp_affiliate_log_debug("Manual affiliate registration option is enabled. So the account status will be set to pending.", true);
            $account_status = 'pending';
        }

        $updatedb = "INSERT INTO $affiliates_table_name (refid,pass,company,firstname,lastname,website,email,payableto,street,town,state,postcode,country,phone,date,paypalemail,commissionlevel,referrer,tax_id,account_details,sec_tier_commissionlevel,account_status) VALUES ('" . $_POST['user_name'] . "', '" . $password . "', '" . $_POST['acompany'] . "', '" . $_POST['afirstname'] . "', '" . $_POST['alastname'] . "', '" . $_POST['awebsite'] . "', '" . $_POST['aemail'] . "', '" . $_POST['apayable'] . "', '" . $_POST['astreet'] . "', '" . $_POST['atown'] . "', '" . $_POST['astate'] . "', '" . $_POST['apostcode'] . "', '" . $_POST['acountry'] . "', '" . $_POST['aphone'] . "', '$date','" . $_POST['paypal_email'] . "','" . $commission_level . "','" . $referrer . "', '" . $_POST['tax_id'] . "','$account_details','$sec_tier_commissionlevel','$account_status')";
        $results = $wpdb->query($updatedb);

        $affiliate_login_url = get_option('wp_aff_login_url');

        $email_subj = get_option('wp_aff_signup_email_subject');
        $body_sign_up = get_option('wp_aff_signup_email_body');
        $from_email_address = get_option('wp_aff_senders_email_address');
        $headers = 'From: ' . $from_email_address . "\r\n";

        $additional_params = array();
        $additional_params['password'] = $_POST['wp_aff_pwd'];
        $aemailbody = wp_aff_dynamically_replace_affiliate_details_in_message($user_name, $body_sign_up, $additional_params);
        $additional_params['password'] = "********";
        $admin_email_body = wp_aff_dynamically_replace_affiliate_details_in_message($user_name, $body_sign_up, $additional_params);
        $admin_email_body = "The following email was sent to the affiliate: \n" .
                "-----------------------------------------\n" . $admin_email_body;

        if (get_option('wp_aff_admin_notification')) {
            $admin_email_subj = "New affiliate sign up notification";
            $admin_contact_email = get_option('wp_aff_contact_email');
            if (empty($admin_contact_email)) {
                $admin_contact_email = $from_email_address;
            }
            wp_mail($admin_contact_email, $admin_email_subj, $admin_email_body, $headers);
            wp_affiliate_log_debug("Affiliate signup notification email successfully sent to the admin: " . $admin_contact_email, true);
        }
        wp_mail($_POST['aemail'], $email_subj, $aemailbody, $headers);
        wp_affiliate_log_debug("Welcome email successfully sent to the affiliate: " . $_POST['aemail'], true);

        //Check and give registration bonus
        wp_aff_check_and_give_registration_bonus($userid);

        //Check and do autoresponder signup
        include_once('wp_aff_auto_responder_handler.php');
        wp_aff_global_autoresponder_signup($_POST['afirstname'], $_POST['alastname'], $_POST['aemail']);

        //$redirect_page = wp_aff_view_get_url_with_separator("thankyou");
        //echo '<meta http-equiv="refresh" content="0;url='.$redirect_page.'" />';
        //exit();
        $_SESSION['wp_aff_signup_success_msg'] = "";
        $_SESSION['wp_aff_signup_success_msg'] .= "<h2 class='wp_aff_title'>" . AFF_THANK_YOU . "</h2><p class='message'>" . AFF_REGO_COMPLETE . "</p>";
        $_SESSION['wp_aff_signup_success_msg'] .= '<a href="' . $login_url . '">' . AFF_LOGIN_HERE . '</a>';
        $additional_success_msg = apply_filters('wpap_below_registration_success_message', '');
        if (!empty($additional_success_msg)) {
            $_SESSION['wp_aff_signup_success_msg'] .= $additional_success_msg;
        }
        
        do_action('wp_aff_registration_complete');//registration complete hook
    }
}