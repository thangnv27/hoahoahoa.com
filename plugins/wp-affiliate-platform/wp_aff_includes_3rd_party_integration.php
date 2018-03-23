<?php

function wp_aff_3rd_party_handle_plugins_loaded_hook() {//fired at plugins_loaded hook
    wp_aff_pp_ipn_listener();
    wp_aff_check_clickbank_transaction();
    wp_aff_check_gumroad_ping();
}

function wp_aff_pp_ipn_listener() {
    if (isset($_REQUEST['aff_paypal_ipn']) && $_REQUEST['aff_paypal_ipn'] == '1') {
        include_once(WP_AFF_PLATFORM_PATH . 'api/ipn_handler.php');
        exit;
    }
}

function wp_aff_check_gumroad_ping() {
    if (isset($_REQUEST['ap_aff_gumroad_ping'])) {
        wp_affiliate_log_debug("Gumraod integration - Received gumroad ping. Checking details...", true);

        $wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();
        if ($wp_aff_platform_config->getValue('wp_aff_enable_gumroad') != '1') {
            wp_affiliate_log_debug("Gumraod integration is disabled in the settings!", false);
            return;
        }

        status_header(200);

        //$request_data = $_REQUEST;
        //wp_aff_write_debug_array($request_data, true);
        $url_params = $_REQUEST['url_params'];
        if (!empty($url_params)) {
            $custom_var = strip_tags($url_params['ap_id']);
            wp_affiliate_log_debug("Gumraod integration - Custom var value: " . $custom_var, true);
            if (empty($custom_var)) {
                wp_affiliate_log_debug("Gumraod integration - this transaction was not referred by an affiliate.", true);
                return;
            }
            $referrer = $custom_var;
            $total_amt = strip_tags($_REQUEST['price']); //eg: 200 ($2.00)
            $sale_amt = round($total_amt / 100);
            $txn_id = strip_tags($_REQUEST['order_number']);
            $item_id = strip_tags($_REQUEST['product_permalink']);
            $buyer_email = strip_tags($_REQUEST['email']);
            $order_details = array("referrer" => $referrer, "sale_amt" => $sale_amt, "txn_id" => $txn_id, "buyer_email" => $buyer_email, "item_id" => $item_id);
            wp_affiliate_log_debug("Invoking Gumraod commisison awarding...", true);
            wp_aff_write_debug_array($order_details, true);
            do_action('wp_affiliate_process_cart_commission', $order_details);
        }
    }
}

/* * * s2Member Integration ** */
if (defined("WS_PLUGIN__S2MEMBER_VERSION")) {
    add_action("ws_plugin__s2member_before_sc_paypal_button_after_shortcode_atts", "wp_aff_s2member_integration");
    add_action("ws_plugin__s2member_pro_before_sc_paypal_form_after_shortcode_atts", "wp_aff_s2member_integration");
    add_action("ws_plugin__s2member_pro_before_sc_authnet_form_after_shortcode_atts", "wp_aff_s2member_integration");
    add_action("ws_plugin__s2member_pro_before_sc_stripe_form_after_shortcode_atts", "wp_aff_s2member_integration");
    add_action("plugins_loaded", "wp_aff_s2member_specify_post_payment_notification_url");
}

function wp_aff_s2member_integration($vars = array()) {
    $cookie_value = $_SESSION["ap_id"];
    if (empty($cookie_value)) {
        $cookie_value = esc_html($_COOKIE["ap_id"]);
    }
    if (!empty($cookie_value)) {
        $vars["__refs"]["attr"]["custom"] .= "|" . $cookie_value;
    }
}

function wp_aff_s2member_specify_post_payment_notification_url() {
    $urls = &$GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["payment_notification_urls"];
    $secret_key = get_option('wp_aff_secret_word_for_post');
    $wp_aff_payment_notification_url = WP_AFF_PLATFORM_URL . '/api/post.php?secret=' . $secret_key . '&ap_id=%%cv1%%&sale_amt=%%amount%%&buyer_email=%%payer_email%%&txn_id=%%txn_id%%';
    $pos = strpos($urls, $wp_aff_payment_notification_url);
    if ($pos === false) {
        $urls = trim($urls . "\n" . $wp_aff_payment_notification_url);
    }

    $specific_post_page_urls = &$GLOBALS["WS_PLUGIN__"]["s2member"]["o"]["sp_sale_notification_urls"];
    $pos2 = strpos($specific_post_page_urls, $wp_aff_payment_notification_url);
    if ($pos2 === false) {
        $specific_post_page_urls = trim($specific_post_page_urls . "\n" . $wp_aff_payment_notification_url);
    }
}

/* * * End s2Member integration ** */

/* * * WP-eCommerce Integration ** */

function wpsc_submit_checkout_handler($args) {
    wp_affiliate_log_debug("WPSC Integration - wpsc_submit_checkout_handler(). Saving purchase log ID.", true);
    global $wpdb;
    $aff_relations_tbl = WP_AFF_RELATIONS_TBL_NAME;
    $purchase_log_id = $args['purchase_log_id'];
    $referrer = wp_affiliate_get_referrer();
    $clientdate = (date("Y-m-d"));
    $clienttime = (date("H:i:s"));
    $clientip = wp_aff_get_user_ip();
    $updatedb = "INSERT INTO $aff_relations_tbl (unique_ref,refid,reference,date,time,ipaddress,additional_info) VALUES ('$purchase_log_id','$referrer','wp_ecommerce','$clientdate','$clienttime','$clientip','')";
    $results = $wpdb->query($updatedb);
}

add_action('wpsc_submit_checkout', 'wpsc_submit_checkout_handler'); //Alternative hook - wpsc_pre_submit_gateway

function wpsc_transaction_result_cart_item_handler($order_details) {
    wp_affiliate_log_debug("WPSC Integration - wpsc_transaction_result_cart_item_handler()", true);
    //wp_aff_write_debug_array($order_details,true);
    $purchase_log = $order_details['purchase_log'];
    $sale_amount = $purchase_log['totalprice'];
    $txn_id = $purchase_log['id'];
    $cart_item = $order_details['cart_item'];
    $item_id = $cart_item['prodid'];
    $buyer_email = wpsc_get_buyers_email($purchase_log['id']);
    $shipping = $purchase_log['base_shipping'];
    $sale_amount = $sale_amount - $shipping;
    $gateway = $purchase_log['gateway'];
    $order_status = $purchase_log['processed'];
    if ($gateway == "wpsc_merchant_testmode") {//Manual payment
        wp_affiliate_log_debug("WPSC Integration - manual payment notification. Checking order status...", true);
        if ($order_status != "3" && $order_status != "4" && $order_status != "5") {//This is not a complete payment status
            wp_affiliate_log_debug("WPSC Integration - order status is not 'payment received' so commission will not be awarded. Order Status for this order is: " . $order_status, true);
            return;
        }
    }

    $referrer = wp_affiliate_get_referrer();
    if (empty($referrer)) {
        $referrer = wp_aff_retrieve_id_from_relations_tbl($purchase_log['id']);
    }
    wp_affiliate_log_debug("WPSC Integration - debug data: " . $referrer . "|" . $txn_id . "|" . $sale_amount . "|" . $buyer_email . "|" . $gateway . "|" . $order_status, true);

    global $wpdb;
    $aff_sales_table = WP_AFF_SALES_TBL_NAME;
    $resultset = $wpdb->get_results("SELECT * FROM $aff_sales_table WHERE txn_id = '$txn_id'", OBJECT);
    if ($resultset) {
        //Commission for this transaction has already been awarded so no need to do anything.
    } else {
        if (!empty($referrer)) {
            wp_aff_award_commission($referrer, $sale_amount, $txn_id, $item_id, $buyer_email);
        } else {//Not an affiliate conversion		    
            wp_affiliate_log_debug("WPSC Integration - referrer data (Affiliate ID) is empty so this is not an affiliate sale", true);
        }
    }
}

add_action('wpsc_transaction_result_cart_item', 'wpsc_transaction_result_cart_item_handler');
/* * * End WP-eCommerce ** */

/* * * WooCommerce plugin integration ** */
//add_action('woocommerce_thankyou', 'wp_aff_handle_woocommerce_payment');
add_action('woocommerce_order_status_completed', 'wp_aff_handle_woocommerce_payment'); //Executes when a status changes to completed
add_action('woocommerce_order_status_processing', 'wp_aff_handle_woocommerce_payment'); //Executes when a status changes to processing
add_action('woocommerce_checkout_order_processed', 'wp_aff_handle_woocommerce_payment');

function wp_aff_handle_woocommerce_payment($order_id) {
    wp_affiliate_log_debug("WooCommerce Affiliate integration - Order processed... checking if affiliate commission need to be awarded.", true);
    //$order_total = get_post_meta( $order_id, '_order_total', true );
    //$order_tax = get_post_meta( $order_id, '_order_tax', true );
    //$order_shipping = get_post_meta( $order_id, '_order_shipping', true );
    //$order_shipping_tax = get_post_meta( $order_id, '_order_shipping_tax', true );
    //$sale_amount = $order_total - $order_tax - $order_shipping - $order_shipping_tax;
    $order = new WC_Order($order_id);

    /* Subscription Payment Check */
    $recurring_payment_method = get_post_meta($order_id, '_recurring_payment_method', true);
    if (!empty($recurring_payment_method)) {
        wp_affiliate_log_debug("WooCommerce Affiliate integration - This is a recurring payment order. Subscription payment method: " . $recurring_payment_method, true);
        wp_affiliate_log_debug("The commission will be calculated via the recurring payemnt api call.", true);
        return;
    }
    $post = get_post ($order_id);
    $post_name = $post->post_name;
    if(stripos($post_name, 'subscription') !== false){
        //This is an order for subscription. The subscription hook will handle it.
        wp_affiliate_log_debug("WooCommerce Affiliate integration - This is a recurring payment order. The commission will be calculated via the recurring payemnt api call.", true);
        return;
    }    
    /* End Subscription Payment Check */

    $total = $order->order_total;
    $shipping = $order->get_total_shipping();
    $tax = $order->get_total_tax();
    $fees = wpap_get_total_woocommerce_order_fees($order);
    wp_affiliate_log_debug("WooCommerce Affiliate integration Data - Total amount: " . $total . ". Total shipping: " . $shipping . ". Total tax: " . $tax . " Fees: ".$fees, true);
    $sale_amount = $total - $shipping - $tax - $fees;
    $txn_id = $order_id;
    $item_id = "";
    $buyer_email = $order->billing_email;
    $buyer_name = $order->billing_first_name . " " . $order->billing_last_name;
    
    $referrer = get_post_meta($order_id, '_wp_aff_ap_id', true);
    if(empty($referrer)){
        $ip_address = get_post_meta($order_id, '_customer_ip_address', true);
        wp_affiliate_log_debug("WooCommerce Affiliate integration - couldn't get referrer ID from cookie. Checking IP address: ".$ip_address, true);
        if (empty($ip_address)) {
            wp_affiliate_log_debug("WooCommerce Affiliate integration - customer IP address is missing in WooCommerce order.", false);
            return;
        }
        $referrer = wp_aff_get_referrer_id_from_ip_address($ip_address);
        wp_affiliate_log_debug("IP address referral check result... Referrer: " . $referrer, false);
    }

    $order_status = $order->status;
    wp_affiliate_log_debug("WooCommerce Affiliate integration - Order status: " . $order_status, true);
    if (strtolower($order_status) != "completed" && strtolower($order_status) != "processing") {
        wp_affiliate_log_debug("WooCommerce Affiliate integration - Order status for this transaction is not in a 'completed' or 'processing' state. Commission will not be awarded at this stage.", true);
        wp_affiliate_log_debug("Commission for this transaciton will be awarded when you set the order status to completed or processing.", true);
        return;
    }

    //apply filter for coupon check
    $referrer = apply_filters('aff_woo_before_awarding_commission_filter', $referrer, $order);

    if (!empty($referrer)) {
        $debug_data = "Commission tracking debug data from the WooCommerce plugin:" . $referrer . "|" . $sale_amount . "|" . $buyer_email . "|" . $txn_id . "|" . $ip_address . "|" . $buyer_name;
        wp_affiliate_log_debug($debug_data, true);
        wp_aff_award_commission_unique($referrer, $sale_amount, $txn_id, $item_id, $buyer_email, '', '', $buyer_name);
    } else {
        wp_affiliate_log_debug("WooCommerce Affiliate integration - This is not an affiliate referred sale!", true);
    }
}

add_action('woocommerce_order_status_refunded', 'wp_aff_handle_woocommerce_order_refund'); //Executes when a status changes to refunded

function wp_aff_handle_woocommerce_order_refund($order_id) {
    wp_affiliate_log_debug("WooCommerce Affiliate integration - order refunded. Order ID: " . $order_id, true);
    //$order = new WC_Order($order_id);
    $txn_id = $order_id;
    wp_aff_handle_refund($txn_id);
}

add_action('processed_subscription_payments_for_order', 'wp_aff_handle_woocommerce_subscription_payment'); //Triggered when a subscription payment is made

function wp_aff_handle_woocommerce_subscription_payment($order) {
    if (!is_object($order)) {
        $order = new WC_Order($order);
    }

    $order_id = $order->id;
    $total = $order->order_total;
    $shipping = $order->get_total_shipping();//get_shipping();
    $sale_amount = $total - $shipping;
    $txn_id = $order_id . "_" . date("Y-m-d"); //Add the subscription charge date to make this unique
    $item_id = "";
    $buyer_email = $order->billing_email;
    $buyer_name = $order->billing_first_name . " " . $order->billing_last_name;
    
    $referrer = get_post_meta($order_id, '_wp_aff_ap_id', true);
    if(empty($referrer)){
        wp_affiliate_log_debug("WooCommerce Affiliate integration - couldn't get referrer ID from cookie. Checking IP address...", true);
        $ip_address = get_post_meta($order_id, '_customer_ip_address', true);
        if (empty($ip_address)) {
            wp_affiliate_log_debug("WooCommerce Subscription Affiliate integration - customer IP address is missing in WooCommerce order.", false);
            return;
        }
        $referrer = wp_aff_get_referrer_id_from_ip_address($ip_address);
    }
    
    $order_status = $order->status;

    //apply filter for coupon check
    $referrer = apply_filters('aff_woo_before_awarding_commission_filter', $referrer, $order);

    $debug_data = "WooCommerce subscripiton payment - Commission tracking debug data: " . $referrer . "|" . $sale_amount . "|" . $buyer_email . "|" . $txn_id . "|" . $ip_address . "|" . $buyer_name;
    wp_affiliate_log_debug($debug_data, true);

    if (!empty($referrer)) {
        wp_aff_award_commission_unique($referrer, $sale_amount, $txn_id, $item_id, $buyer_email, '', '', $buyer_name);
    } else {
        wp_affiliate_log_debug("WooCommerce Affiliate integration - This is not an affiliate referred sale!", true);
    }
}

/**
* Update the order meta with field value
**/
add_action('woocommerce_checkout_update_order_meta', 'wp_aff_woo_checkout_update_order_meta', 10, 2);
function wp_aff_woo_checkout_update_order_meta( $order_id, $posted ) {
    $aff_id = wp_affiliate_get_referrer();
    if(!empty($aff_id)){//Save the referrer ID in the order meta
        update_post_meta( $order_id, '_wp_aff_ap_id', $aff_id);
        $ap_id = get_post_meta($order_id, '_wp_aff_ap_id', true);
        wp_affiliate_log_debug("WooCommerce Affiliate integration - Saving referrer id (".$aff_id.") with order.", true);
    }
}

function wpap_get_total_woocommerce_order_fees($order)
{
    //Calculate total fee (if any) for this order
    $total_fee = 0;
    $order_fee_items = $order->get_fees();
    if(!is_array($order_fee_items)){
        return $total_fee;
    }
    
    foreach ( $order_fee_items as $fee_item ) {
        $total_fee += $fee_item['line_total'];
    }
    return $total_fee;
}
/* * * End WooCommerce integration ** */

/* * * WPMU DEV Pro site/supporter plugin integration ** */
add_action('supporter_payment_processed', 'wp_aff_handle_pro_sites_payment', 10, 4);

function wp_aff_handle_pro_sites_payment($arg1, $arg2, $arg3, $arg4) {
    $referrer = $_COOKIE['ap_id'];
    $sale_amt = $arg2;
    $debug_data = "Commission tracking debug data from the pro-sites/supporter plugin:" . $referrer . "|" . $arg1 . "|" . $arg2 . "|" . $arg3 . "|" . $arg4;
    wp_affiliate_log_debug($debug_data, true);
    wp_aff_award_commission($referrer, $sale_amt, "", "", "");
}

/* * * Clickbank commission award ** */

function wp_aff_check_clickbank_transaction() {
    if (WP_AFFILIATE_ENABLE_CLICKBANK_INTEGRATION == '1') {
        if (isset($_REQUEST['cname']) && isset($_REQUEST['cprice'])) {
            $aff_id = wp_affiliate_get_referrer();
            if (!empty($aff_id)) {
                $sale_amt = strip_tags($_REQUEST['cprice']);
                $txn_id = strip_tags($_REQUEST['cbreceipt']);
                $item_id = strip_tags($_REQUEST['item']);
                $buyer_email = strip_tags($_REQUEST['cemail']);
                $debug_data = "Commission tracking debug data from ClickBank transaction:" . $aff_id . "|" . $sale_amt . "|" . $buyer_email . "|" . $txn_id . "|" . $item_id;
                wp_affiliate_log_debug($debug_data, true);
                wp_aff_award_commission_unique($aff_id, $sale_amt, $txn_id, $item_id, $buyer_email);
            }
        }
    }
}

/* * * Contact Form 7 Lead Capture ** */
add_action('wpcf7_before_send_mail', 'wp_aff_wpcf7_lead_capture');

function wp_aff_wpcf7_lead_capture($cf7) {
    //Changes in CF7 API http://contactform7.com/2014/07/02/contact-form-7-39-beta/
    
    $wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();
    if ($wp_aff_platform_config->getValue('wp_aff_enable_wpcf7_lead_capture') === '1') {
        wp_affiliate_log_debug("Contact Form 7 lead capture feature is enabled. Checking details...", true);
        $reference = $cf7->id;
        //Check form exclusion list
        $form_exclusion = $wp_aff_platform_config->getValue('wp_aff_wp_cf7_form_exclusion_list');
        if (!empty($form_exclusion)) {
            $form_exclusion_list = explode(",", $form_exclusion);
            foreach ($form_exclusion_list as $form_id) {
                if ($reference == trim($form_id)) {
                    wp_affiliate_log_debug("You have excluded this contact form (ID: " . $reference . " ) from the lead capture pool. So no lead will be captured for this submission.", true);
                    return;
                }
            }
        }
        
        if(empty($cf7->posted_data)){
            //Retrieve info using thier new structure (new version of contact form 7)
            $submission = WPCF7_Submission::get_instance();
            $buyer_email = $submission->get_posted_data( 'your-email' );//$cf7->posted_data["your-email"];
            $buyer_name = $submission->get_posted_data( 'your-name' );//$cf7->posted_data["your-name"];            
        }else{
            //Retrieve info using old contact form 7 structure (old version of contact form 7)
            $buyer_email = $cf7->posted_data["your-email"];
            $buyer_name = $cf7->posted_data["your-name"];
        }
        
        $aff_id = wp_affiliate_get_referrer();
        $clientdate = (date("Y-m-d"));
        $clienttime = (date("H:i:s"));
        $ipaddress = wp_aff_get_user_ip();
        $debug_data = "Contact Form 7 lead capture data. Name: " . $buyer_name . " | Email: " . $buyer_email . " | Affiliate ID: " . $aff_id . " | Contact Form Reference ID: " . $reference;
        wp_affiliate_log_debug($debug_data, true);
        if (!empty($aff_id)) {
            //Capture the lead in the database
            wp_aff_capture_lead_data_in_leads_table($buyer_email, $buyer_name, $aff_id, $reference, $clientdate, $clienttime, $ipaddress);
            
            //Add the referrer ID in the email body
            $mail = $cf7->prop( 'mail' );
            $mail['body'] = $mail['body'] . "\n\nReferrer ID: " . $aff_id;
            $mail['body'] = apply_filters('wp_aff_cf7_lead_capture_email_body_filter', $mail['body'], $aff_id);   
            $cf7->set_properties( array( 'mail' => $mail ) );//Save the updated mail object data.
            //wp_affiliate_log_debug('Body: '. $mail['body'], true);//Lets check the body field value (for debugging purpose).
            
        } else {
            wp_affiliate_log_debug("Contact Form 7 lead capture result: This is not an affiliate referral.", true);
        }
    }
}

//function wp_affiliate_shopperpress_track_commission_handler($sp_order_details)
//{
//	$total_amt = $sp_order_details['order_total'];
//	$shipping_amt = $sp_order_details['order_shipping'];
//	$sale_amt = $total_amt - $shipping_amt;
//	$txn_id = $sp_order_details['order_id'];
//	//$item_id = $order_details['item_id'];
//	$buyer_email = $sp_order_details['order_email'];    	
//	$order_details = array("sale_amt" =>$sale_amt, "txn_id"=>$txn_id, "buyer_email"=>$buyer_email,"item_id"=>"");
//	wp_affiliate_log_debug("Invoking shopperpress checkout commisison tracking...",true);
//    wp_aff_write_debug_array($order_details,true);
//	do_action('wp_affiliate_process_cart_commission',$order_details);
//}

/* * * Gravity Forms Lead Capture ** */
function wp_aff_gf_post_submission_handler($entry, $form) {
    wp_affiliate_log_debug("GF integration (Lead capture) - form submitted. Checking if affiliate lead needs to be captured...", true);
    $aff_id = $_COOKIE['ap_id'];
    if (empty($aff_id)) {
        wp_affiliate_log_debug("GF integration (Lead capture) - affiliate ID is not present. This user was not sent by an affiliate.", true);
        return;
    }
    $lead_capture_enabled = false;
    $pay_per_lead_enabled = false;
    foreach ($form['fields'] as $field) {
        if (trim($field['inputName']) == "wpap-lead-email") {
            $email_field_id = $field['id'];
            $lead_capture_enabled = true;
        }
        if (trim($field['inputName']) == "wpap-gf-commission") {
            $comm_hidden_field_id = $field['id'];
            $pay_per_lead_enabled = true;
            wp_affiliate_log_debug("GF integration - Pay Per Lead is enabled on this form. Field id: " . $comm_hidden_field_id, true);
        }
    }
    $reference = $form['id'];
    $lead_email = $entry[$email_field_id];
    wp_affiliate_log_debug("GF integration (Lead capture) - Debug data: " . $lead_email . "|" . $aff_id . "|" . $reference, true);

    $clientdate = (date("Y-m-d"));
    $clienttime = (date("H:i:s"));
    $ipaddress = wp_aff_get_user_ip();
    global $wpdb;
    $affiliates_leads_table_name = WP_AFF_LEAD_CAPTURE_TBL_NAME;

    if ($pay_per_lead_enabled) {//Award appropriate commission for this lead		
        $commission_amt = $entry[$comm_hidden_field_id];
        wp_affiliate_log_debug("GF integration (Pay Per Lead) - Pay per lead option is enabled on this form. Commisison amount to award: " . $commission_amt, true);
        $fields = array();
        $fields['refid'] = $aff_id;
        $fields['payment'] = $commission_amt;
        $fields['sale_amount'] = "00.00";
        $fields['txn_id'] = uniqid();
        $fields['item_id'] = $reference;
        $fields['buyer_email'] = $lead_email;
        wp_aff_add_commission_amt_directly($fields);
        wp_affiliate_log_debug("GF integration (Pay Per Lead) - Commission awarded! Commission amount: " . $commission_amt, true);
    }

    if (!$lead_capture_enabled) {
        wp_affiliate_log_debug("GF integration (Lead capture) - lead capture is not enabled for this Gravity Form as it is missing the 'wpap-lead-email' parameter name in the email field!", true);
        return;
    }

    $updatedb = "INSERT INTO $affiliates_leads_table_name (buyer_email,refid,reference,date,time,ipaddress) VALUES ('$lead_email','$aff_id','$reference','$clientdate','$clienttime','$ipaddress')";
    $results = $wpdb->query($updatedb);
    wp_affiliate_log_debug("GF integration (Lead capture) - lead successfully captured in the leads table.", true);
}

add_action("gform_post_submission", "wp_aff_gf_post_submission_handler", 10, 2);

/* * * Gravity Forms PayPal addon ** */
//add_filter('gform_paypal_query', 'wp_aff_gf_update_paypal_query', 10, 3);//gform_paypalpro_query

add_action('gform_after_submission', 'wp_aff_gf_save_ap_id', 10, 3);//Save the ap_id after a form entry is submitted.

function wp_aff_gf_save_ap_id($entry, $form) {    
    $aff_id = wp_affiliate_get_referrer();
    wp_affiliate_log_debug("GF integration... adding ap_id meta data to entry (".$entry['id'].") with value: " . $aff_id, true);
    gform_update_meta($entry['id'], 'ap_id', $aff_id);
}

add_action('gform_paypal_post_ipn', 'wp_aff_gf_track_affiliate_commission', 10, 4);
add_action('gform_paypalpro_post_ipn', 'wp_aff_gf_track_affiliate_commission', 10, 4);

function wp_aff_gf_track_affiliate_commission($ipn_data, $entry, $config, $cancel) {
    wp_affiliate_log_debug("GF integration - received IPN notification. Checking details...", true);
    $wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();
    if ($wp_aff_platform_config->getValue('wp_aff_enable_gf_paypal') != '1') {
        wp_affiliate_log_debug("GF PayPal AddOn tracking feature is disabled! No commission will be awarded for this sale.", true);
        return;
    }
    wp_affiliate_log_debug("GF integration - IPN processing. Retrieving ap_id data for entry ID: ".$entry['id'], true);
    $referrer = gform_get_meta($entry['id'], 'ap_id');
    if (!empty($referrer)) {
        $sale_amount = $ipn_data['mc_gross'];
        $txn_id = $ipn_data['txn_id'];
        $item_id = $entry['id'];
        $buyer_email = $ipn_data['payer_email'];
        $clientip = $entry['ip'];
        $buyer_name = $ipn_data['first_name'] . " " . $ipn_data['last_name'];
        $payment_status = $ipn_data['payment_status'];

        $debug_data = "GF integration - Commission tracking debug data from PayPal transaction: " . $referrer . "|" . $sale_amount . "|" . $buyer_email . "|" . $txn_id . "|" . $item_id . "|" . $buyer_name . "|" . $payment_status;
        wp_affiliate_log_debug($debug_data, true);

        if ($sale_amount < 0) {// This is a refund or reversal
            wp_affiliate_log_debug('This is a refund. Refund amount: ' . $sale_amount, true);
            $parent_txn_id = $ipn_data['parent_txn_id'];
            wp_aff_handle_refund($parent_txn_id);
            wp_affiliate_log_debug('Calling Automatic Commission Reversal API. Parent transaction ID: ' . $parent_txn_id, true);
            return true;
        }

        wp_aff_award_commission_unique($referrer, $sale_amount, $txn_id, $item_id, $buyer_email, $clientip, '', $buyer_name);
    }else{
        wp_affiliate_log_debug("GF integration - Affiliate ID is not present. This user was not sent by an affiliate.", true);
    }
}

/* * * WishList Member Related ** */
add_action('wlmem_paypal_ipn_response', 'wp_aff_wlmem_paypal_ipn_response_handler');

function wp_aff_wlmem_paypal_ipn_response_handler() {
    //TODO - handle refund txns
    wp_affiliate_log_debug("WishList Member - paypal_ipn_response. Custom var value: " . $_POST['custom'], true);
    if (isset($_POST['custom'])) {//Check if affilite ID exists
        $custom_array = wp_parse_args($_POST['custom']);
        if (isset($custom_array['ap_id'])) {
            wp_affiliate_log_debug("WishList Member - paypal_ipn_response. Affiliate commission need to be tracked. Affiliate ID: " . $custom_array['ap_id'], true);
            $referrer = $custom_array['ap_id'];
            $clientip = $custom_array['ip'];
            $item_id = $_POST['item_number'];
            $txn_id = $_POST['txn_id'];
            $sale_amt = $_POST['mc_gross'];
            $buyer_email = $_POST['payer_email'];
            $buyer_name = $_POST['first_name'] . " " . $_POST['last_name'];
            $aff_details_debug = "Referrer: " . $referrer . " Sale Amt: " . $sale_amt . " Buyer Email: " . $buyer_email . " Txn ID: " . $txn_id;
            wp_affiliate_log_debug("WishList Member - paypal_ipn_response. Extra debug data: " . $aff_details_debug, true);
            wp_aff_award_commission_unique($referrer, $sale_amt, $txn_id, $item_id, $buyer_email, $clientip, '', $buyer_name);
        } else {
            wp_affiliate_log_debug("WishList Member - paypal_ipn_response. Not an affiliate referral. Commission tracking not needed.", true);
        }
    }
}

/* * ****************************************** */
/* * * 3rd Party Integration Helper function ** */
/* * ****************************************** */

function get_wp_aff_custom_args($args) {
    $aff_id = wp_affiliate_get_referrer();
    $ip_addr = wp_aff_get_user_ip();
    $custom_var = 'ap_id=' . $aff_id . '&ip=' . $ip_addr;
    return $custom_var;
}

function get_wp_aff_custom_input(){
    $custom_var = get_wp_aff_custom_args('');
    $custom_input = '<input type="hidden" name="custom" value="'.$custom_var.'" />';
    return $custom_input;    
}

function get_wp_aff_paypal_fields(){
    $custom_input = get_wp_aff_custom_input();
    $ipn_input = '<input type="hidden" name="notify_url" value="'.WP_AFF_PLATFORM_SITE_HOME_URL.'/?aff_paypal_ipn=1">';
    $fields = $custom_input . $ipn_input;
    return $fields;
}

function wp_aff_get_referrer_id_from_ip_address($ip_address) {
    if (empty($ip_address)) {
        wp_affiliate_log_debug("IP address value is missing in the query!", true);
        return "";
    }
    global $wpdb;
    $affiliates_clickthroughs_table_name = WP_AFF_CLICKS_TBL_NAME;
    $resultset = $wpdb->get_row("SELECT * FROM $affiliates_clickthroughs_table_name WHERE ipaddress = '$ip_address' ORDER BY date DESC", OBJECT);
    if ($resultset) {
        return $resultset->refid;
    } else {
        return "";
    }
}

function wp_aff_get_c_id_from_ip_address($ip_address) {
    if (empty($ip_address)) {
        wp_affiliate_log_debug("IP address value is missing in the query!", true);
        return "";
    }
    global $wpdb;
    $affiliates_clickthroughs_table_name = WP_AFF_CLICKS_TBL_NAME;
    $resultset = $wpdb->get_row("SELECT * FROM $affiliates_clickthroughs_table_name WHERE ipaddress = '$ip_address' ORDER BY date DESC", OBJECT);
    if ($resultset) {
        $c_id = $resultset->campaign_id;
        return $c_id;
    } else {
        return "";
    }
}

function wp_aff_retrieve_id_from_relations_tbl($unique_ref) {
    wp_affiliate_log_debug("Trying to retrieve Affiliate ID from relations table for Unique Ref: " . $unique_ref, true);
    global $wpdb;
    $aff_relations_tbl = WP_AFF_RELATIONS_TBL_NAME;
    $resultset = $wpdb->get_row("SELECT * FROM $aff_relations_tbl WHERE unique_ref = '$unique_ref'", OBJECT);
    if ($resultset) {
        return $resultset->refid;
    }
    return "";
}

function wp_aff_check_commission_awarded_for_txn_id($txn_id) {
    global $wpdb;
    $aff_sales_table = WP_AFF_SALES_TBL_NAME;
    $resultset = $wpdb->get_results("SELECT * FROM $aff_sales_table WHERE txn_id = '$txn_id'", OBJECT);
    if ($resultset) {
        return true;
    } else {
        return false;
    }
}

function wp_aff_check_if_buyer_is_referrer($referrer_id, $buyer_email) {
    global $wpdb;
    $affiliates_table_name = WP_AFF_AFFILIATES_TBL_NAME;
    $result = $wpdb->get_row("SELECT * FROM $affiliates_table_name where refid='$referrer_id'", OBJECT);
    if ($result) {
        if ($result->email == $buyer_email || $result->paypalemail == $buyer_email) {
            return true;
        }
    }
    return false;
}

function wp_aff_get_referrer_from_leads_table_for_buyer($buyer_email) {
    global $wpdb;
    $affiliates_leads_table_name = WP_AFF_LEAD_CAPTURE_TBL_NAME;
    $result = $wpdb->get_row("SELECT * FROM $affiliates_leads_table_name where buyer_email='$buyer_email'", OBJECT);
    if ($result) {
        $ref_id = $result->refid;
        return $ref_id;
    }
    return "";
}

function wp_aff_capture_lead_data_in_leads_table($buyer_email, $buyer_name, $aff_id, $reference, $clientdate, $clienttime, $ipaddress) {
    global $wpdb;
    $affiliates_leads_table_name = WP_AFF_LEAD_CAPTURE_TBL_NAME;
    if (version_compare(WP_AFFILIATE_PLATFORM_DB_VERSION, '4.2', '>')) {//if current DB version is greater than 4.2
        wp_affiliate_log_debug("Capturing lead with the name. Name: " . $buyer_name, true);
        $updatedb = "INSERT INTO $affiliates_leads_table_name (buyer_email,refid,reference,date,time,ipaddress,buyer_name) VALUES ('$buyer_email','$aff_id','$reference','$clientdate','$clienttime','$ipaddress','$buyer_name')";
    } else {
        $updatedb = "INSERT INTO $affiliates_leads_table_name (buyer_email,refid,reference,date,time,ipaddress) VALUES ('$buyer_email','$aff_id','$reference','$clientdate','$clienttime','$ipaddress')";
    }
    $results = $wpdb->query($updatedb);
    wp_affiliate_log_debug("Lead captured in the leads database table. Lead email: " . $buyer_email, true);
}
