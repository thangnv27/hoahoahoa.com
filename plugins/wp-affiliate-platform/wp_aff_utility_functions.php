<?php

function wp_aff_default_index_body() {
    $output =
            "Join our affiliate program and start earning money for every sale you send our way! Simply create your account, place your linking code into your website and watch your account balance grow as your visitors become our customers.<br />
<h3>How Does it Work?</h3>
The process is very simple:<br /><br />
1. Visitor clicks on an affiliate link on your site or in an email.<br /><br />
2. The visitors IP is logged and a cookie is placed in their browser for tracking purposes.<br /><br />
3. The visitor browses our site, and may decide to order.<br /><br />
4. If the visitor orders (the order does not need to be placed during the same browser session - cookies and IPs are stored up to a configurable amount of time), the order will be registered as a sale for you and you will receive commission for this sale.<br /><br />
<h3>Already an Affiliate?</h3>
Please visit the Affiliate Login page and enter your username and password to gain access to your account statistics, banners, linking code.
";
    return $output;
}

function wp_aff_is_valid_url_if_not_empty($url) {
    if (empty($url)) {
        return true;
    } else {
        return wp_aff_is_valid_url($url);
    }
}

function wp_aff_is_valid_url($url) {
    $orig_url = $url;
    $url = @parse_url($url);
    if (!$url) {
        return false;
    }
    $url = array_map('trim', $url);
    $scheme = $url['scheme'];
    if ($scheme == "https") {
        $url['port'] = 443;
    }
    $url['port'] = (!isset($url['port'])) ? 80 : (int) $url['port'];
    $path = (isset($url['path'])) ? $url['path'] : '';
    if ($path == '') {
        $path = '/';
    }
    $path .= ( isset($url['query']) ) ? "?$url[query]" : '';
    if (isset($url['host']) AND $url['host'] != gethostbyname($url['host'])) {
        if (PHP_VERSION >= 5) { //Primary checking method
            if (ini_get('allow_url_fopen') != '1') {
                //do nothing... it will fall back to the 2nd second checking method
            } else {
                $headers = get_headers("$url[scheme]://$url[host]:$url[port]$path");
                $headers = ( is_array($headers) ) ? implode("\n", $headers) : $headers;
                return (bool) preg_match('#^HTTP/.*\s+[(200|301|302)]+\s#i', $headers);
            }
        }

        if (function_exists('fsockopen')) { //Alternate checking method using fsockopen
            $fp = fsockopen($url['host'], $url['port'], $errno, $errstr, 30);
            if (!$fp) {
                return false;
            }
            fputs($fp, "HEAD $path HTTP/1.1\r\nHost: $url[host]\r\n\r\n");
            $headers = fread($fp, 128);
            fclose($fp);
            $headers = ( is_array($headers) ) ? implode("\n", $headers) : $headers;
            return (bool) preg_match('#^HTTP/.*\s+[(200|301|302)]+\s#i', $headers);
        }


        if (function_exists('curl_init')) {//Alternate checking method using CURL
            $ch = curl_init($orig_url);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $data = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($httpcode >= 200 && $httpcode < 300) {
                return true;
            } else {
                return false;
            }
        } else {
            return true; //Could not validate... just return true anyway.
        }
    }
    return false;
}

function wp_aff_url_validation_error_message($field_name, $field_value) {
    $validation_error_msg .= "<br /><strong>The URL specified in the \"" . $field_name . "\" field does not seem to be a valid URL! Please check this value again:</strong>";
    $validation_error_msg .= "<br />" . $field_value . "<br />";
    return $validation_error_msg;
}

function wp_aff_escape_csv_value($value) {
    $value = str_replace('"', '""', $value); // First off escape all " and make them ""
    $value = trim($value, ",");
    $value = preg_replace("/[\n\r]/", " ", $value); //replace newlines with space
    $value = preg_replace('/,/', " ", $value); //replace comma with space
    if (preg_match('/"/', $value)) { // Check if I have any " character
        return '"' . $value . '"'; // If I have new lines or commas escape them
    } else {
        return $value; // If no new lines or commas just return the value
    }
}

function wp_aff_is_logged_in() {
    if (isset($_COOKIE['user_id'])) {
        $_SESSION['user_id'] = $_COOKIE['user_id'];
    }
    if (!isset($_SESSION['user_id'])) {
        return false;
    } else {
        return true;
    }
}

function wp_aff_get_affiliate_details($aff_id, $info) {
    if (empty($aff_id)) {
        return '<div class="aff_error_msg">You did not specify an Affiliate ID. This function needs an affiliate ID to work.</div>';
    }
    if (empty($info)) {
        return '<div class="aff_error_msg">You did not specify which information of the affiliate you want to show (the "info" parameter is empty).</div>';
    }
    global $wpdb;
    $affiliates_table_name = WP_AFF_AFFILIATES_TBL_NAME;
    $aff_details = $wpdb->get_row("SELECT * FROM $affiliates_table_name WHERE refid = '$aff_id'", OBJECT);
    //var_dump($aff_details);
    if ($info == 'all') {
        var_dump($aff_details);
        return "";
    }
    if (isset($aff_details->$info)) {
        return $aff_details->$info;
    } else {
        return '<div class="aff_error_msg">The value you specified for the "info" parameter does not exist in the affiliate database. Please check the documentation to learn the usage.</div>';
    }
}

function wp_aff_get_affiliate_record_from_db($referrer) {
    global $wpdb;
    $affiliates_table_name = WP_AFF_AFFILIATES_TBL_NAME;
    $wp_aff_referrer_record = $wpdb->get_row("SELECT * FROM $affiliates_table_name WHERE refid = '$referrer'", OBJECT);
    return $wp_aff_referrer_record;
}

function wp_aff_check_and_give_registration_bonus($aff_id) {
    global $wpdb;
    $wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();
    if ($wp_aff_platform_config->getValue('wp_aff_enable_registration_bonus') == '1') {
        $bonus_amt = $wp_aff_platform_config->getValue('wp_aff_registration_bonus_amt');
        wp_affiliate_log_debug("Applying registration bonus to affiliate's account. Bonus amount: " . $bonus_amt, true);
        $fields = array();
        $fields['refid'] = $aff_id;
        $fields['payment'] = $bonus_amt;
        $fields['sale_amount'] = "00.00";
        $fields['txn_id'] = "Bonus-" . uniqid();
        $fields['item_id'] = "Registration Bonus";
        wp_aff_add_commission_amt_directly($fields);
    }
}

function wp_aff_get_user_ip() {
    $user_ip = '';
    if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && !empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $user_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $user_ip = $_SERVER['REMOTE_ADDR'];
    }

    if (strstr($user_ip, ',')) {
        $ip_values = explode(',', $user_ip);
        $user_ip = $ip_values['0'];
    }

    return apply_filters('wp_aff_get_user_ip', $user_ip);
}

function wp_aff_redirect_to_url($url, $delay = '0', $exit = '1') {
    if (empty($url)) {
        echo "<br /><strong>Error! The URL value is empty. Please specify a correct URL value to redirect to!</strong>";
        exit;
    }

    $url = apply_filters('wp_aff_redirect_to_url', $url);

    if (!headers_sent()) {
        header('Location: ' . $url);
    } else {
        echo '<meta http-equiv="refresh" content="' . $delay . ';url=' . $url . '" />';
    }
    if ($exit == '1') {
        exit;
    }
}