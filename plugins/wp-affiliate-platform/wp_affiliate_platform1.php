<?php

/* * ******************************************
 * **       THIS IS NOT A FREE PLUGIN       ***
 * ******************************************* */
if (!isset($_SESSION)) {
    session_start();
}

define('WP_AFF_DATE_CSS_URL', WP_AFF_PLATFORM_URL . '/lib/date/dhtmlxcalendar.css');
define('WP_AFF_COM_JS_URL', WP_AFF_PLATFORM_URL . '/lib/date/dhtmlxcommon.js');
define('WP_AFF_CAL_JS_URL', WP_AFF_PLATFORM_URL . '/lib/date/dhtmlxcalendar.js');
define('WP_AFF_DATE_IMG_URL', WP_AFF_PLATFORM_URL . '/lib/date/codebase/imgs/');

define('WP_AFF_CLICKS_TBL_NAME', $wpdb->prefix . "affiliates_clickthroughs_tbl");
define('WP_AFF_AFFILIATES_TBL_NAME', $wpdb->prefix . "affiliates_tbl");
define('WP_AFF_SALES_TBL_NAME', $wpdb->prefix . "affiliates_sales_tbl");
define('WP_AFF_PAYOUTS_TBL_NAME', $wpdb->prefix . "affiliates_payouts_tbl");
define('WP_AFF_BANNERS_TBL_NAME', $wpdb->prefix . "affiliates_banners_tbl");
define('WP_AFF_LEAD_CAPTURE_TBL_NAME', $wpdb->prefix . "affiliates_leads_tbl");
define('WP_AFF_RELATIONS_TBL_NAME', $wpdb->prefix . "affiliates_relations_tbl");

//Includes
include_once('wp_aff_advanced_configs.php');
include_once('wp_aff_db_access_class.php');
include_once('wp_aff_debug_handler.php');
include_once('wp_aff_utility_functions.php');
include_once('wp_aff_utility_functions2.php');
include_once('wp_aff_plugins_loaded_tasks.php');
include_once('wp_aff_includes_3rd_party_integration.php');
include_once('wp_aff_includes.php');
include_once('wp_aff_includes2.php');
include_once('wp_affiliate_login_widget.php');
include_once('affiliate_platform_affiliate_view.php');
include_once('wp_aff_includes_shortcodes.php');

/* * * PDT Stuff for PayPal transaction commission awarding ** */
if (isset($_GET['tx']) && isset($_GET['amt'])) {
    wp_affiliate_log_debug("PayPal PDT detected - checking if commission need to be tracked...", true);
    $auth_token = get_option('wp_aff_pdt_identity_token');
    if (get_option('wp_aff_enable_3rd_party') != '' && !empty($auth_token)) {
        wp_affiliate_log_debug("Need to process commission for this sale...", true);
        //Process PDT to award commission
        $_SESSION['aff_tx_result_error_msg'] = "";
        $req = 'cmd=_notify-synch';
        $tx_token = strip_tags($_GET['tx']);
        $req .= "&tx=$tx_token&at=$auth_token";

        $sandbox_enabled = get_option('wp_aff_sandbox_mode');
        if ($sandbox_enabled != '') {
            wp_affiliate_log_debug("Sandbox mode is enabled", true);
            $host_url = 'www.sandbox.paypal.com';
            $uri = 'ssl://' . $host_url;
            $port = '443';
            $fp = fsockopen($uri, $port, $errno, $errstr, 30);
        } else {
            $host_url = 'www.paypal.com';
            $fp = fsockopen($host_url, 80, $errno, $errstr, 30);
        }

        if (!$fp) {
            wp_affiliate_log_debug("Error! HTTP ERROR... could not establish a connection to PayPal for verification.", false);
            echo "<br />Error! HTTP ERROR... could not establish a connection to PayPal for verification!";
            return;
        } else {
            // post back to PayPal system to validate
            $header = "";
            $header .= "POST /cgi-bin/webscr HTTP/1.1\r\n";
            $header .= "Host: " . $host_url . "\r\n";
            $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
            $header .= "Content-Length: " . strlen($req) . "\r\n";
            $header .= "Connection: close\r\n\r\n";
            fputs($fp, $header . $req);
            // read the body data
            $res = '';
            $headerdone = false;
            while (!feof($fp)) {
                $line = fgets($fp, 1024);
                if (strcmp($line, "\r\n") == 0) {// read the header
                    $headerdone = true;
                } else if ($headerdone) {// header has been read. now read the contents
                    $res .= $line;
                }
            }
            // parse the data
            $lines = explode("\n", $res);
            //wp_aff_write_debug_array($lines,true);
            $keyarray = array();
            if (eregi("VERIFIED", $res)) {
                wp_affiliate_log_debug("PDT verified with PayPal.", true);
                for ($i = 1; $i < count($lines); $i++) {
                    list($key, $val) = explode("=", $lines[$i]);
                    $keyarray[urldecode($key)] = urldecode($val);
                }
            } else {
                $error_msg = "Error! PDT verification failed! Could not verify the authenticity of the payment with PayPal! " . $errno;
                wp_affiliate_log_debug($error_msg, false);
                wp_aff_write_debug_array($lines, true);
                echo $error_msg;
                return;
            }
        }
        fclose($fp);
        global $wpdb;
        $aff_sales_table = WP_AFF_SALES_TBL_NAME;
        $txn_id = $keyarray['txn_id'];
        $resultset = $wpdb->get_results("SELECT * FROM $aff_sales_table WHERE txn_id = '$txn_id'", OBJECT);
        if ($resultset) {
            //Commission for this transaction has already been awarded so no need to do anything.
            wp_affiliate_log_debug("Commission for this transaction has already been awarded so no need to do anything. Transaction ID:" . $txn_id, true);
        } else {
            wp_affiliate_log_debug("Calling process PDT function.", true);
            wp_aff_process_PDT_payment_data($keyarray);
        }
    } else {
        wp_affiliate_log_debug("Nothing to do... 3rd party integration is disabled or the auth token is missing.", true);
    }
}

/* 3rd party cart commission award handler */

function wp_affiliate_process_cart_commission_handler($order_details) {
    $sale_amount = $order_details['sale_amt'];
    $txn_id = $order_details['txn_id'];
    $item_id = $order_details['item_id'];
    $buyer_email = $order_details['buyer_email'];
    $referrer = $order_details['referrer'];
    if (empty($referrer)) {
        $referrer = wp_affiliate_get_referrer();
    }

    $sale_debug_data = $referrer . "|" . $sale_amount . "|" . $txn_id . "|" . $buyer_email . "|" . $item_id;
    wp_affiliate_log_debug("3rd party affiliate commission processing data - " . $sale_debug_data, true);

    global $wpdb;
    $aff_sales_table = WP_AFF_SALES_TBL_NAME;
    $resultset = $wpdb->get_results("SELECT * FROM $aff_sales_table WHERE txn_id = '$txn_id'", OBJECT);
    if ($resultset) {
        //Commission for this transaction has already been awarded so no need to do anything.
        wp_affiliate_log_debug("Commission for this transaction (" . $txn_id . ") has already been awarded.", true);
    } else {
        $db_data = "Commission tracking debug data:" . $referrer . "|" . $sale_amount . "|" . $txn_id . "|" . $buyer_email;
        wp_affiliate_log_debug($db_data, true);

        if (!empty($referrer)) {
            wp_aff_award_commission($referrer, $sale_amount, $txn_id, $item_id, $buyer_email);
        } else {
            //Not an affiliate conversion
        }
    }
}

/* Affiliate View Option 2 POST Data processing */

function wp_aff_affiliate_view2_login_handler() {
    if (isset($_POST['wpAffDoLogin'])) {
        if ($_POST['userid'] != '' && $_POST['password'] != '') {
            // protection against script injection
            $userid = preg_replace('/[^a-zA-Z0-9_]/', '', $_POST['userid']);
            $password = $_POST['password'];
            include_once(ABSPATH . WPINC . '/class-phpass.php');
            $wp_hasher = new PasswordHash(8, TRUE);

            global $wpdb;
            $affiliates_table_name = WP_AFF_AFFILIATES_TBL_NAME;
            $result = $wpdb->get_row("SELECT * FROM $affiliates_table_name where refid='$userid'", OBJECT);

            setcookie("cart_in_use", "true", time() + 21600, "/", COOKIE_DOMAIN); //set the cookie for W3 Total Cache

            if ($wp_hasher->CheckPassword($password, $result->pass)) {
                if ($result->account_status == "pending") {
                    $msg = urlencode("This account is pending approval from the administrator. Please try again later.");
                    $target_url = wp_aff_view_get_url_with_separator("login&msg=" . $msg);
                    header("Location: " . $target_url);
                    exit;
                }
                // this sets variables in the session
                $_SESSION['user_id'] = $userid;
                setcookie("user_id", $userid, time() + 60 * 60 * 24, "/", COOKIE_DOMAIN); //set cookie for 24 hours
                if (function_exists('wp_cache_serve_cache_file')) {//WP Super cache workaround
                    setcookie("comment_author_", "wp_affiliate", time() + 21600, "/", COOKIE_DOMAIN);
                }

                //set a cookie witout expiry until 60 days
                if (isset($_POST['remember'])) {
                    setcookie("user_id", $_SESSION['user_id'], time() + 60 * 60 * 24 * 60, "/", COOKIE_DOMAIN);
                }

                $target_url = wp_aff_view_get_url_with_separator("members_only");
                header("Location: " . $target_url);
                exit;
            } else {
                $msg = urlencode("Invalid Login. Please try again with correct user name and password.");
                $target_url = wp_aff_view_get_url_with_separator("login&msg=" . $msg);
                header("Location: " . $target_url);
                exit;
            }
        }
    }
}

function wp_aff_affiliate_view2_logout_handler() {
    if (isset($_GET['wp_affiliate_view']) && $_GET['wp_affiliate_view'] == 'logout') {
        /*         * * Delete the cookies and unset the session data ** */
        $aff_id = $_SESSION['user_id'];
        unset($_SESSION['user_id']);
        setcookie("user_id", '', time() - 60 * 60 * 24 * 60, "/", COOKIE_DOMAIN);

        $wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();
        if ($wp_aff_platform_config->getValue('wp_aff_auto_logout_aff_account') == '1') {
            wp_clear_auth_cookie();
        }

        //Logout eMember account if using auto affiliate log-in option
        if (function_exists('wp_eMember_install')) {
            global $emember_config;
            $emember_config = Emember_Config::getInstance();
            $eMember_auto_affiliate_account_login = $emember_config->getValue('eMember_auto_affiliate_account_login');
            if ($eMember_auto_affiliate_account_login) {
                wp_emem_logout();
            }
        }

        $curr_page_after_logout = wp_aff_current_page_url();
        $logout_get_val_pos = strpos($curr_page_after_logout, "?wp_affiliate_view");
        if (empty($logout_get_val_pos)) {
            $logout_get_val_pos = strpos($curr_page_after_logout, "&wp_affiliate_view");
        }
        $redirect_page_after_logout = substr($curr_page_after_logout, 0, $logout_get_val_pos);
        echo '<meta http-equiv="refresh" content="0;url=' . $redirect_page_after_logout . '" />';
        exit;
    }
}

/* End of Affiliate View Option 2 POST Data processing */

function wp_aff_award_custom_commission_handler($atts) {
    $sale_amount = "0"; //Commission will be calcualted based off this amount
    $txn_id = "A unique transaction id"; //can be anything
    $item_id = "Id of this item for identification"; //can be anything
    $buyer_email = "email address of the buyer";


    if (!empty($_SESSION['ap_id'])) {
        $referrer = $_SESSION['ap_id'];
    } else if (isset($_COOKIE['ap_id'])) {
        $referrer = $_COOKIE['ap_id'];
    }

    if (!empty($referrer)) {
        wp_aff_award_commission($referrer, $sale_amount, $txn_id, $item_id, $buyer_email);
    } else {
        //Not an affiliate conversion
    }
    return "";
}

function wp_aff_login_handler($atts) {
    return aff_login_widget();
}

function wp_aff_login_onpage_version_handler($atts) {
    extract(shortcode_atts(array(
        'url' => '',
                    ), $atts));
    return aff_login_widget_onpage_version($url);
}

function aff_get_cookie_life_time() {
    $cookie_expiry = get_option('wp_aff_cookie_life');
    if (!empty($cookie_expiry)) {
        $cookie_life_time = time() + $cookie_expiry * 86400;
    } else {
        $cookie_life_time = time() + 30 * 86400;
    }
    return $cookie_life_time;
}

if (isset($_GET['ap_id'])) {
    /* Common stripping to avoid any type of hack */
    $referrer_id = trim(strip_tags($_GET['ap_id']));
    if (strlen($referrer_id) > 0) {
        $wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();
        if ($wp_aff_platform_config->getValue('wp_aff_record_verified_aff_clicks') == '1') {
            global $wpdb;
            $affiliates_table_name = WP_AFF_AFFILIATES_TBL_NAME;
            $result = $wpdb->get_row("SELECT * FROM $affiliates_table_name where refid='$referrer_id'", OBJECT);
            if (!$result) {
                wp_affiliate_log_debug("Not tracking this click from referrer ID: " . $referrer_id . ". This site has 'record verified affiliate clicks only' option enabled", true);
                return;
            }
        }

        $campaign_id = isset($_GET['c_id']) ? strip_tags($_GET['c_id']) : '';
        if (WP_AFFILIATE_DO_NOT_OVERRIDE_AFFILIATE_COOKIE == '1') {
            if (isset($_COOKIE['ap_id']) && $_COOKIE['ap_id'] != $referrer_id) {
                //Do not tract this click as the admin doesn't want to override
                wp_affiliate_log_debug("Not tracking this click since the admin has enabled WP_AFFILIATE_DO_NOT_OVERRIDE_AFFILIATE_COOKIE", true);
            } else {
                record_click($referrer_id, $campaign_id);
            }
        } else {
            record_click($referrer_id, $campaign_id);
        }
    }

    if (WP_AFFILIATE_AUTO_REDIRECT_TO_NOT_AFFILIATE_URL == '1') {
        wp_affiliate_log_debug("Redirecting to non affiliate URL since the admin has enabled WP_AFFILIATE_AUTO_REDIRECT_TO_NOT_AFFILIATE_URL", true);
        wp_aff_redirect_to_non_affiliate_url();
    }
}

function record_click($referrer_id, $campaign_id = '') {
    global $wpdb;
    $cookie_life_time = aff_get_cookie_life_time();

    $domain_url = $_SERVER['SERVER_NAME'];
    $cookie_domain = str_replace("www", "", $domain_url);
    setcookie('ap_id', $referrer_id, $cookie_life_time, "/", $cookie_domain);
    if (!empty($campaign_id)) {
        setcookie('c_id', $campaign_id, $cookie_life_time, "/", $cookie_domain);
    }
    if (function_exists('wp_cache_serve_cache_file')) {//WP Super cache workaround
        setcookie("comment_author_", "wp_affiliate", time() + 21600, "/", $cookie_domain);
    }

    $_SESSION['ap_id'] = $referrer_id;
    if (!empty($campaign_id)) {
        $_SESSION['c_id'] = $campaign_id;
    }

    $clientdate = (date("Y-m-d"));
    $clienttime = (date("H:i:s"));
    $clientbrowser = $_SERVER['HTTP_USER_AGENT'];
    $clientip = wp_aff_get_user_ip();
    $clienturl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
    $current_page = wp_aff_current_page_url();

    // Ignore bots and wordpress trackbacks
    if (strpos($clientbrowser, "WordPress") !== false || strpos($clientbrowser, "bot") !== false || strpos($current_page, "?secret=") !== false) {
        return;
    }

    $e_refid = esc_sql($referrer_id);
    $e_date = esc_sql($clientdate);
    $e_time = esc_sql($clienttime);
    $e_browser = esc_sql($clientbrowser);
    $e_ip = esc_sql($clientip);
    $e_url = esc_sql($clienturl);

    $affiliates_clickthroughs_table_name = WP_AFF_CLICKS_TBL_NAME;
    if ($e_url != $current_page) {
        if (empty($e_url)) {
            $e_url = $current_page; //"Data unavailable - May be the URL was entered directly in the browser";
        }
        if (wp_aff_true_click()) {
            $updatedb = "INSERT INTO $affiliates_clickthroughs_table_name (refid,date,time,browser,ipaddress,referralurl,buy,campaign_id) VALUES ('$e_refid', '$e_date', '$e_time', '$e_browser', '$e_ip', '$e_url', '','$campaign_id')";
            $results = $wpdb->query($updatedb);
        }
    }
}

function wp_aff_true_click($clientip = '') {
    global $wpdb;
    $affiliates_clickthroughs_table_name = WP_AFF_CLICKS_TBL_NAME;

    $cooldown_time_in_unix = mktime(date("H"), date("i"), date("s") - 5);
    $cooldown_time = date("H:i:s", $cooldown_time_in_unix);
    $cur_time = date("H:i:s");
    if (empty($clientip)) {
        $clientip = wp_aff_get_user_ip();
    }
    $find = $wpdb->get_results("SELECT * FROM $affiliates_clickthroughs_table_name WHERE time between '$cooldown_time' and '$cur_time' and ipaddress='$clientip'", OBJECT);
    if ($find) {
        return false;
    }
    //Set the following value to true if you want to track one click per IP address
    $track_unique_clicks = false;
    if ($track_unique_clicks) {
        $find = $wpdb->get_results("SELECT * FROM $affiliates_clickthroughs_table_name WHERE ipaddress = '$clientip'", OBJECT);
        if ($find) {
            return false;
        }
    }
    return true;
}

function wp_aff_true_sale($clientip = '') {
    global $wpdb;
    $affiliates_sales_table_name = WP_AFF_SALES_TBL_NAME;

    $cooldown_time_in_unix = mktime(date("H"), date("i"), date("s") - 10);
    $cooldown_time = date("H:i:s", $cooldown_time_in_unix);
    $cur_time = date("H:i:s");
    if (empty($clientip)) {
        $clientip = wp_aff_get_user_ip();
    }
    $find = $wpdb->get_results("SELECT * FROM $affiliates_sales_table_name WHERE time between '$cooldown_time' and '$cur_time' and ipaddress='$clientip'", OBJECT);
    if ($find) {
        return false;
    }
    return true;
}

function wp_aff_record_remote_click($referrer_id, $clientbrowser, $clientip, $clienturl, $campaign_id = '') {
    global $wpdb;
    $affiliates_clickthroughs_table_name = WP_AFF_CLICKS_TBL_NAME;

    $clientdate = (date("Y-m-d"));
    $clienttime = (date("H:i:s"));

    // Ignore bots and wordpress trackbacks
    if (strpos($clientbrowser, "WordPress") !== false || strpos($clientbrowser, "bot") !== false) {
        return;
    }

    $e_refid = esc_sql($referrer_id);
    $e_date = esc_sql($clientdate);
    $e_time = esc_sql($clienttime);
    $e_browser = esc_sql($clientbrowser);
    $e_ip = esc_sql($clientip);
    $e_url = esc_sql($clienturl);

    if (wp_aff_true_click()) {
        $updatedb = "INSERT INTO $affiliates_clickthroughs_table_name (refid,date,time,browser,ipaddress,referralurl,buy,campaign_id) VALUES ('$e_refid', '$e_date', '$e_time', '$e_browser', '$e_ip', '$e_url', '','$campaign_id')";
        $results = $wpdb->query($updatedb);
    }
}

function record_click_for_eStore_cart($referrer_id) {
    global $wpdb;
    $cookie_life_time = aff_get_cookie_life_time();

    $domain_url = $_SERVER['SERVER_NAME'];
    $cookie_domain = str_replace("www", "", $domain_url);
    setcookie('ap_id', $referrer_id, $cookie_life_time, "/", $cookie_domain);

    $_SESSION['ap_id'] = $referrer_id;

    $campaign_id = '';
    $clientdate = (date("Y-m-d"));
    $clienttime = (date("H:i:s"));
    $clientbrowser = $_SERVER['HTTP_USER_AGENT'];
    $clientip = wp_aff_get_user_ip();
    $clienturl = $_SERVER['HTTP_REFERER'];

    $e_refid = esc_sql($referrer_id);
    $e_date = esc_sql($clientdate);
    $e_time = esc_sql($clienttime);
    $e_browser = esc_sql($clientbrowser);
    $e_ip = esc_sql($clientip);
    $e_url = esc_sql($clienturl);
    $current_page = wp_aff_current_page_url();

    $affiliates_clickthroughs_table_name = WP_AFF_CLICKS_TBL_NAME;
    $updatedb = "INSERT INTO $affiliates_clickthroughs_table_name (refid,date,time,browser,ipaddress,referralurl,buy,campaign_id) VALUES ('$e_refid', '$e_date', '$e_time', '$e_browser', '$e_ip', '$e_url', '','$campaign_id')";
    $results = $wpdb->query($updatedb);
}

function wp_aff_record_remote_lead($referrer_id, $buyer_email, $reference, $clientip, $clientbrowser = '', $buyer_name = '') {
    global $wpdb;
    $affiliates_leads_table_name = WP_AFF_LEAD_CAPTURE_TBL_NAME;
    $clientdate = (date("Y-m-d"));
    $clienttime = (date("H:i:s"));

    // Ignore bots and wordpress trackbacks
    if (strpos($clientbrowser, "WordPress") !== false || strpos($clientbrowser, "bot") !== false) {
        return;
    }
    $referrer = esc_sql($referrer_id);
    $buyer_email = esc_sql($buyer_email);
    $reference = esc_sql($reference);
    $clientdate = esc_sql($clientdate);
    $clienttime = esc_sql($clienttime);
    $ipaddress = esc_sql($clientip);

    $updatedb = "INSERT INTO $affiliates_leads_table_name (buyer_email,refid,reference,date,time,ipaddress,buyer_name) VALUES ('$buyer_email','$referrer','$reference','$clientdate','$clienttime','$ipaddress','$buyer_name')";
    $results = $wpdb->query($updatedb);
}

if (isset($_POST['wp_aff_affiliate_id_submit'])) {
    $referrer_id = strip_tags($_POST['wp_aff_affiliate_id']);
    record_click($referrer_id);
}

function wp_aff_set_affiliate_id_form() {
    $output = "";
    if (!isset($_POST['wp_aff_affiliate_id'])) {
        $_POST['wp_aff_affiliate_id'] = "";
    }
    $output .= '<a name="aff_id_entry_anchor"></a>';
    $output .= '<form method="post" action="#aff_id_entry_anchor">';
    $output .= AFF_USERNAME . ': ';
    $output .= '<input name="wp_aff_affiliate_id" type="text" size="20" value="' . $_POST['wp_aff_affiliate_id'] . '"/>';
    $output .= '<div class="submit">';
    $output .= '<input type="submit" name="wp_aff_affiliate_id_submit" value="Submit" />';
    $output .= '</div>';
    $output .= '</form>';
    return $output;
}

function wp_aff_check_if_account_exists($email) {
    global $wpdb;
    $affiliates_table_name = WP_AFF_AFFILIATES_TBL_NAME;
    $resultset = $wpdb->get_row("SELECT * FROM $affiliates_table_name WHERE email = '$email'", OBJECT);
    if ($resultset) {
        return true;
    } else {
        return false;
    }
}

function wp_aff_check_if_account_exists_by_affiliate_id($affiliate_id) {
    global $wpdb;
    $affiliates_table_name = WP_AFF_AFFILIATES_TBL_NAME;
    $resultset = $wpdb->get_row("SELECT * FROM $affiliates_table_name WHERE refid = '$affiliate_id'", OBJECT);
    if ($resultset) {
        return true;
    } else {
        return false;
    }
}

function wp_aff_create_affilate($user_name, $pwd, $acompany, $atitle, $afirstname, $alastname, $awebsite, $aemail, $apayable, $astreet, $atown, $astate, $apostcode, $acountry, $aphone, $afax, $date, $paypal_email, $commission_level, $referrer) {
    global $wpdb;
    include_once(ABSPATH . WPINC . '/class-phpass.php');
    $wp_hasher = new PasswordHash(8, TRUE);
    $pwd = $wp_hasher->HashPassword($pwd);

    $affiliates_table_name = WP_AFF_AFFILIATES_TBL_NAME;
    $updatedb = "INSERT INTO $affiliates_table_name (refid,pass,company,title,firstname,lastname,website,email,payableto,street,town,state,postcode,country,phone,fax,date,paypalemail,commissionlevel,referrer) VALUES ('" . $user_name . "', '" . $pwd . "', '" . $acompany . "', '" . $atitle . "', '" . $afirstname . "', '" . $alastname . "', '" . $awebsite . "', '" . $aemail . "', '" . $apayable . "', '" . $astreet . "', '" . $atown . "', '" . $astate . "', '" . $apostcode . "', '" . $acountry . "', '" . $aphone . "', '" . $afax . "', '$date','" . $paypal_email . "','" . $commission_level . "','" . $referrer . "')";
    $results = $wpdb->query($updatedb);
}

function wp_aff_create_affilate_using_array_data($fields) {
    global $wpdb;
    $inTable = WP_AFF_AFFILIATES_TBL_NAME;
    $fieldss = '';
    $valuess = '';
    $first = true;
    foreach ($fields as $field => $value) {
        if ($first)
            $first = false;
        else {
            $fieldss .= ' , ';
            $valuess .= ' , ';
        }
        $fieldss .= " $field ";
        $valuess .= " '" . esc_sql($value) . "' ";
    }

    $query .= " INSERT INTO $inTable ($fieldss) VALUES ($valuess)";
    $results = $wpdb->query($query);
    return $results;
}

function wp_aff_send_sign_up_email($user_name, $pwd, $affiliate_email) {
    $affiliate_login_url = get_option('wp_aff_login_url');

    $email_subj = get_option('wp_aff_signup_email_subject');
    $body_sign_up = get_option('wp_aff_signup_email_body');
    $from_email_address = get_option('wp_aff_senders_email_address');
    $headers = 'From: ' . $from_email_address . "\r\n";

    $tags1 = array("{user_name}", "{email}", "{password}", "{login_url}");
    $vals1 = array($user_name, $affiliate_email, $pwd, $affiliate_login_url);
    $aemailbody = str_replace($tags1, $vals1, $body_sign_up);

    if (get_option('wp_aff_admin_notification')) {
        $admin_email_subj = "New affiliate sign up notification";
        wp_mail($from_email_address, $admin_email_subj, $aemailbody);
    }
    wp_mail($affiliate_email, $email_subj, $aemailbody, $headers);
}

function wp_aff_send_commission_notification($affiliate_email, $txn_id = '', $aff_id = '') {
    $wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();
    if (!empty($txn_id)) {
        global $wpdb;
        $aff_sales_table = WP_AFF_SALES_TBL_NAME;
        $resultset = $wpdb->get_row("SELECT * FROM $aff_sales_table WHERE txn_id = '$txn_id'", OBJECT);
        $aff_id = $resultset->refid;
        if (empty($aff_id)) {
            wp_affiliate_log_debug("Failed to retrieve the affiliate ID for this transaction. TXN ID: " . $txn_id, false);
        }
    }
    if (get_option('wp_aff_notify_affiliate_for_commission')) {
        $from_email_address = $wp_aff_platform_config->getValue('wp_aff_comm_notif_senders_address');
        $headers = 'From: ' . $from_email_address . "\r\n";
        $notify_subj = $wp_aff_platform_config->getValue('wp_aff_comm_notif_email_subject'); //AFF_COMMISSION_RECEIVED_NOTIFICATION_SUBJECT  	
        $notify_body = $wp_aff_platform_config->getValue('wp_aff_comm_notif_email_body'); //AFF_COMMISSION_RECEIVED_NOTIFICATION_BODY	             
        $notify_body = wp_aff_dynamically_replace_affiliate_details_in_message($aff_id, $notify_body);
        $notify_body = wp_aff_dynamically_replace_commission_details_in_msg($txn_id, $notify_body);
        wp_aff_platform_send_email($affiliate_email, $notify_subj, $notify_body, $headers);
        wp_affiliate_log_debug("Commission notification email sent to affiliate. Email address: " . $affiliate_email, true);
        wp_affiliate_log_debug("Commission email subject: " . $notify_subj, true);
    }
    if ($wp_aff_platform_config->getValue('wp_aff_notify_admin_for_commission') == '1') {
        $admin_email = get_option('wp_aff_contact_email');
        $subj = $wp_aff_platform_config->getValue('wp_aff_admin_comm_notif_email_subject');
        $body = $wp_aff_platform_config->getValue('wp_aff_admin_comm_notif_email_body');
        if (empty($subj)) {
            $subj = "Affiliate commission notification";
        }
        if (empty($body)) {
            $body = "This is an auto-generated email letting you know that one of your affiliates has earned a commission. You can log into your WordPress admin dashboard and get more details about this transaction.";
        }
        $from_email_address = $wp_aff_platform_config->getValue('wp_aff_comm_notif_senders_address');
        $headers = 'From: ' . $from_email_address . "\r\n";

        $body = wp_aff_dynamically_replace_affiliate_details_in_message($aff_id, $body);
        $body = wp_aff_dynamically_replace_commission_details_in_msg($txn_id, $body);
        wp_aff_platform_send_email($admin_email, $subj, $body, $headers);
        wp_affiliate_log_debug("Commission notification email sent to admin. Email address: " . $admin_email, true);
    }
}

function wp_aff_dynamically_replace_commission_details_in_msg($txn_id, $message_body) {
    if (!empty($txn_id)) {
        global $wpdb;
        $aff_sales_table = WP_AFF_SALES_TBL_NAME;
        $resultset = $wpdb->get_row("SELECT * FROM $aff_sales_table WHERE txn_id = '$txn_id'", OBJECT);
        $aff_id = $resultset->refid;
        if (empty($aff_id)) {
            wp_affiliate_log_debug("Failed to retrieve the commission details for this transaction. TXN ID: " . $txn_id, false);
            return $message_body;
        }

        $tags = array("{commission_amount}", "{sale_amount}", "{sale_date}", "{item_reference}", "{txn_id}");
        $vals = array($resultset->payment, $resultset->sale_amount, $resultset->date, $resultset->item_id, $txn_id);
        $message_body = str_replace($tags, $vals, $message_body);
        return $message_body;
    }
}

function wp_aff_dynamically_replace_affiliate_details_in_message($aff_id, $message_body, $additional_params = '') {
    global $wpdb;
    $affiliates_table_name = WP_AFF_AFFILIATES_TBL_NAME;
    $resultset = $wpdb->get_row("SELECT * FROM $affiliates_table_name where refid='$aff_id'", OBJECT);

    $password = "";
    $login_link = "";
    if (!empty($additional_params)) {
        $password = $additional_params['password'];
    }
    if (empty($password)) {
        $password = "********";
    }
    $login_url = get_option('wp_aff_login_url');

    $tags = array("{user_name}", "{affiliate_id}", "{first_name}", "{last_name}", "{email}",
        "{password}", "{commission_level}", "{login_url}"
    );

    $vals = array($aff_id, $aff_id, $resultset->firstname, $resultset->lastname, $resultset->email,
        $password, $resultset->commissionlevel, $login_url
    );

    $message_body = str_replace($tags, $vals, $message_body);
    return $message_body;
}

function wp_aff_platform_send_email($email, $subject, $body, $headers) {
    if (function_exists('wp_mail')) {
        wp_mail($email, $subject, $body, $headers);
    } else {
        include_once('lib/email.php');
        wp_affiliate_send_mail($email, $body, $subject, $headers);
    }
}

function wp_aff_redirect_to_non_affiliate_url() {
    $curr_page = wp_aff_current_page_url();
    $ap_id_pos = strpos($curr_page, "?ap_id");
    if (empty($ap_id_pos)) {
        $ap_id_pos = strpos($curr_page, "&ap_id");
    }
    $target_url = substr($curr_page, 0, $ap_id_pos);
    header('Location: ' . $target_url);
    exit;
}

function wp_affiliate_referrer_handler($atts) {
    $referrer = wp_affiliate_get_referrer();
    if (empty($referrer)) {
        $referrer = AFF_NONE;
    }
    return $referrer;
}

function wp_affiliate_referrer_details_handler($atts) {
    extract(shortcode_atts(array(
        'ap_id' => '',
        'info' => '',
                    ), $atts));
    if (empty($ap_id)) {//Set it to current referrer
        $ap_id = wp_affiliate_get_referrer();
    }
    if (empty($ap_id)) {
        return AFF_NONE;
    }
    return wp_aff_get_affiliate_details($ap_id, $info);
}

function wp_affiliate_get_referrer() {
    $referrer = '';
    if (!empty($_SESSION['ap_id'])) {
        $referrer = $_SESSION['ap_id'];
    } else if (isset($_COOKIE['ap_id'])) {
        $referrer = $_COOKIE['ap_id'];
    } else if (isset($_REQUEST['ap_id'])) {
        $referrer = strip_tags($_REQUEST['ap_id']);
    }
    return $referrer;
}

function wp_aff_current_page_url() {
    $pageURL = 'http';
    if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
        $pageURL .= "s";
    }
    $pageURL .= "://";
    if ($_SERVER["SERVER_PORT"] != "80") {
        $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
    } else {
        $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    }
    return $pageURL;
}

function wp_aff_login_widget_init() {
    $widget_options = array('classname' => 'wp_affiliate_widget', 'description' => __("Display WP Affiliate Login Widget"));
    wp_register_sidebar_widget('wp_affiliate_widget', __('WP Affiliate Login'), 'show_wp_aff_login_widget', $widget_options);
}

function show_wp_aff_login_widget($args) {
    extract($args);
    //$widget_title = get_option('wp_aff_login_widget_title');
    $widget_title = AFF_WIDGET_TITLE;
    if (empty($widget_title))
        $widget_title = "Affiliate Login";
    echo $before_widget;
    echo $before_title . $widget_title . $after_title;
    echo aff_login_widget();
    echo $after_widget;
}

function wp_aff_front_head_content() {
    $debug_marker = "<!-- WP Affiliate plugin v" . WP_AFFILIATE_PLATFORM_VERSION . " - https://www.tipsandtricks-hq.com/wordpress-affiliate-platform-plugin-simple-affiliate-program-for-wordpress-blogsite-1474 -->";
    echo "\n${debug_marker}\n";
    echo '<link type="text/css" rel="stylesheet" href="' . WP_AFF_PLATFORM_URL . '/affiliate_platform_style.css" />' . "\n";
}

function wp_aff_plugin_conflict_check() {
    $msg = "";

    $wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();
    // WP Super cache check
    if (function_exists('wp_cache_serve_cache_file') && $wp_aff_platform_config->getValue('wp_aff_do_not_show_sc_warning') != '1') {
        $sc_integration_incomplete = false;
        global $wp_super_cache_late_init;
        if (false == isset($wp_super_cache_late_init) || ( isset($wp_super_cache_late_init) && $wp_super_cache_late_init == 0 )) {
            $sc_integration_incomplete = true;
        }
        if (defined('TIPS_AND_TRICKS_SUPER_CACHE_OVERRIDE')) {
            $sc_integration_incomplete = false;
        }
        if ($sc_integration_incomplete) {
            $msg .= '<p>You have the WP Super Cache plugin active. Please make sure to follow <a href="http://www.tipsandtricks-hq.com/forum/topic/using-the-plugins-together-with-wp-super-cache-plugin" target="_blank">this instruction</a> to make it work with the WP Affiliate Platform plugin. You can ignore this message if you have already applied the recommended changes.';
            $msg .= '<input class="button " type="button" onclick="document.location.href=\'admin.php?page=wp_aff_platform_settings&wpap_hide_sc_msg=1\';" value="Hide this Message">';
            $msg .= '</p>';
        }
    }
    if (function_exists('w3tc_pgcache_flush') && class_exists('W3_PgCache')) {
        // W3 Total Cache is active	
        $integration_in_place = false;
        //$w3_pgcache = & W3_PgCache::instance();
        $w3_pgcache = w3_instance('W3_PgCache');
        foreach ($w3_pgcache->_config->get_array('pgcache.reject.cookie') as $reject_cookie) {
            if (strstr($reject_cookie, "cart_in_use") !== false) {
                $integration_in_place = true;
            }
        }
        if (!$integration_in_place) {
            $msg .= '<p>You have the W3 Total Cache plugin active. Please make sure to follow <a href="http://www.tipsandtricks-hq.com/forum/topic/using-the-plugins-with-w3-total-cache-plugin" target="_blank">these instructions</a> to make it work with the WP Affiliate plugin.</p>';
        }
    }
    //Check schema version
    $installed_schema_version = get_option("wp_affiliates_version");
    if ($installed_schema_version != WP_AFFILIATE_PLATFORM_DB_VERSION) {
        $msg .= '<p>It looks like you did not follow the <a href="http://www.tipsandtricks-hq.com/wordpress-affiliate/wordpress-affiliate-platform-installation-guide-6" target="_blank">WP Affiliate upgrade instruction</a> to update the plugin. The database schema is out of sync and need to be updated. Please deactivate the plugin and follow the <a href="http://www.tipsandtricks-hq.com/wordpress-affiliate/wordpress-affiliate-platform-installation-guide-6" target="_blank">upgrade instruction from here</a> to upgrade the plugin and correct this.</p>';
    }

    if (!empty($msg)) {
        echo '<div class="updated fade">' . $msg . '</div>';
    }
}

function wpap_load_language_file() {
    
    $language_override = apply_filters('wp_aff_language_loading_override', '');
    if(!empty($language_override)){
        wp_affiliate_log_debug('Notice - another plugin or addon has overriden the language loading.', true);
        return;
    }
    
    $aff_language = get_option('wp_aff_language');
    if (!empty($aff_language)) {
        $language_file = "affiliates/lang/" . $aff_language;
    } else {
        $language_file = "affiliates/lang/eng.php";
    }
    $language_file = apply_filters('wp_aff_get_language_path', $language_file, $aff_language);
    include_once($language_file);
}

//Add the Admin Menus
if (is_admin()) {
    //Define the admin dashboard management permission
    $wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();
    $selected_permission = $wp_aff_platform_config->getValue('wpap_management_permission');
    if (empty($selected_permission)) {
        define("AFFILIATE_MANAGEMENT_PERMISSION", "add_users");
    } else {
        define("AFFILIATE_MANAGEMENT_PERMISSION", $selected_permission);
    }

    function wp_aff_platform_add_admin_menu() {
        add_menu_page(__("Affiliate Platform", 'wp_affiliate'), __("WP Affiliate", 'wp_affiliate'), AFFILIATE_MANAGEMENT_PERMISSION, __FILE__, "wp_aff_show_stats");
        add_submenu_page(__FILE__, __("WP Affiliate Settings", 'wp_affiliate'), __("Settings", 'wp_affiliate'), AFFILIATE_MANAGEMENT_PERMISSION, 'wp_aff_platform_settings', "show_aff_platform_settings_page");
        add_submenu_page(__FILE__, __("WP Affiliates", 'wp_affiliate'), __("Manage Affiliates", 'wp_affiliate'), AFFILIATE_MANAGEMENT_PERMISSION, 'affiliates', "aff_top_affiliates_menu");
        add_submenu_page(__FILE__, __("WP Affiliates Edit", 'wp_affiliate'), __("Add/Edit Affiliates", 'wp_affiliate'), AFFILIATE_MANAGEMENT_PERMISSION, 'affiliates_addedit', "edit_affiliates_menu");
        add_submenu_page(__FILE__, __("WP Affiliate Banners", 'wp_affiliate'), __("Manage Ads", 'wp_affiliate'), AFFILIATE_MANAGEMENT_PERMISSION, 'manage_banners', "manage_banners_menu");
        add_submenu_page(__FILE__, __("WP Aff Banner Edit", 'wp_affiliate'), __("Add/Edit Ads", 'wp_affiliate'), AFFILIATE_MANAGEMENT_PERMISSION, 'edit_banners', "wp_aff_edit_ads_menu");
        add_submenu_page(__FILE__, __("WP Affiliate Leads", 'wp_affiliate'), __("Manage Leads", 'wp_affiliate'), AFFILIATE_MANAGEMENT_PERMISSION, 'manage_leads', "aff_top_leads_menu");
        add_submenu_page(__FILE__, __("WP Affiliate Clicks", 'wp_affiliate'), __("Click Throughs", 'wp_affiliate'), AFFILIATE_MANAGEMENT_PERMISSION, 'clickthroughs', "clickthroughs_menu");
        add_submenu_page(__FILE__, __("WP Affiliate Sales", 'wp_affiliate'), __("Sales/Comm Data", 'wp_affiliate'), AFFILIATE_MANAGEMENT_PERMISSION, 'aff_sales', "aff_top_sales_menu");
        add_submenu_page(__FILE__, __("WP Payouts", 'wp_affiliate'), __("Manage Payouts", 'wp_affiliate'), AFFILIATE_MANAGEMENT_PERMISSION, 'manage_payouts', "manage_payouts_menu");
        add_submenu_page(__FILE__, __("WP Payouts History", 'wp_affiliate'), __("Payouts History", 'wp_affiliate'), AFFILIATE_MANAGEMENT_PERMISSION, 'payouts_history', "payouts_history_menu");
    }

    //Include menus
    require_once(dirname(__FILE__) . '/aff_stats_menu.php');
    require_once(dirname(__FILE__) . '/wp_affiliate_platform_menu.php');
    require_once(dirname(__FILE__) . '/affiliates_menu.php');
    require_once(dirname(__FILE__) . '/leads_menu.php');
    require_once(dirname(__FILE__) . '/clickthroughs_menu.php');
    require_once(dirname(__FILE__) . '/banners_menu.php');
    require_once(dirname(__FILE__) . '/payouts_menu.php');
    require_once(dirname(__FILE__) . '/payouts_history_menu.php');
    require_once(dirname(__FILE__) . '/sales_menu.php');
}

// Insert the options page to the admin menu
if (is_admin()) {
    add_action('admin_menu', 'wp_aff_platform_add_admin_menu');
}

function wp_aff_load_libraries() {
    wp_enqueue_script('jquery');
    if (is_admin()) {//Admin side
        wp_enqueue_script('wpap-admin-js', WP_AFF_PLATFORM_URL . '/lib/wpap-admin.js'); //Admin js code
        if (wpap_is_affiliate_admin_page()) {
            wp_enqueue_script('jquery-ui-datepicker');
            wp_enqueue_style('jquery-style', '//ajax.googleapis.com/ajax/libs/jqueryui/1.8.2 /themes/smoothness/jquery-ui.css');
        }
    }
}

function aff_load_shortcode_specific_scripts() {
    //Use this function to load JS and CSS file that should only be loaded if the shortcode is present in the page
    global $post;
    if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'wp_affiliate_view2')) {
        wp_enqueue_style('wpapview2bscss', WP_AFF_PLATFORM_URL . '/views/style2/assets/css/bootstrap.css');
        wp_enqueue_style('wpapview2customcss', WP_AFF_PLATFORM_URL . '/views/style2/assets/css/wpap.style2.css');
        wp_enqueue_script('wpapview2bsjs', WP_AFF_PLATFORM_URL . '/views/style2/assets/js/bootstrap.min.js', array('jquery'));
    }
}

function wp_aff_init_action_handler() {
    //Check the pluigns loaded tasks file and function also
    wpap_load_language_file();
    wp_aff_load_libraries();
    wp_aff_login_widget_init();
    wp_aff_affiliate_view2_login_handler();
    wp_aff_affiliate_view2_logout_handler();
    wp_aff_view2_signup_form_processing_code();
}

function wp_aff_handle_plugins_loaded_hook() {
    //Normal plugins loaded tasks    
    //...
    //Admin side only plugins loaded task
    if (is_admin()) {//Check if DB needs to be updated
        if (get_option('wp_affiliates_version') != WP_AFFILIATE_PLATFORM_DB_VERSION) {
            wp_affiliate_platform_run_activation();
        }
    } else {
        //Front-end only plugins loaded tasks
        wpap_handle_pass_thru_links();
    }
}

//Register for wp login and register hooks (if applicable)
wp_aff_wp_user_integration_hooks_handler();

add_action('wp_enqueue_scripts', 'aff_load_shortcode_specific_scripts');
add_action('init', 'wp_aff_init_action_handler');
add_action('plugins_loaded', 'wp_aff_3rd_party_handle_plugins_loaded_hook');
add_action('plugins_loaded', 'wp_aff_handle_plugins_loaded_hook');
add_action('wp_head', 'wp_aff_front_head_content');
add_action('wp_affiliate_process_cart_commission', 'wp_affiliate_process_cart_commission_handler');
add_action('wp_affiliate_shopperpress_track_commission', 'wp_affiliate_shopperpress_track_commission_handler');

add_shortcode('wp_aff_award_custom_commission', 'wp_aff_award_custom_commission_handler');
add_shortcode('wp_aff_login', 'wp_aff_login_handler');
add_shortcode('wp_aff_login_onpage_version', 'wp_aff_login_onpage_version_handler');
add_shortcode('wp_affiliate_view', 'wp_affiliate_view_handler');
add_shortcode('wp_affiliate_view2', 'wp_affiliate_view2_handler');
add_shortcode('wp_affiliate_referrer', 'wp_affiliate_referrer_handler');
add_shortcode('wp_aff_set_affiliate_id', 'wp_aff_set_affiliate_id_form');
add_shortcode('wp_affiliate_referrer_details', 'wp_affiliate_referrer_details_handler');
add_shortcode('wp_aff_custom_args', 'get_wp_aff_custom_args'); //Use this for getting ap_id value for custom integration
add_shortcode('wp_aff_custom_input', 'get_wp_aff_custom_input'); 
add_shortcode('wp_aff_paypal_fields', 'get_wp_aff_paypal_fields');
add_shortcode('wp_affiliate_leaderboard', 'wp_affiliate_leaderboard_handler');

if (!is_admin()) {
    add_filter('widget_text', 'do_shortcode');
}

if (is_admin()) {
    add_action('admin_notices', 'wp_aff_plugin_conflict_check');
}
