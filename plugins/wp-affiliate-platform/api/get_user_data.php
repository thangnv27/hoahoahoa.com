<?php

include_once('../../../../wp-load.php');
include_once('../wp_aff_includes.php');
include_once('../wp_aff_debug_handler.php');

$allow_remote_post = get_option('wp_aff_enable_remote_post');
if (!$allow_remote_post) {
    echo "Remote POST is disabled";
    wp_aff_api_debug('Remote POST is disabled in the settings.', false);
    exit;
}

wp_aff_api_debug('Start processing remote commission tracking request...', true);

if (isset($_REQUEST['secret'])) {
    $secret = $_REQUEST['secret'];
    $ap_id = $_REQUEST['ap_id'];
    wp_aff_api_debug('POST data: ' . $secret . "|" . $ap_id, true);
} else {
    wp_aff_api_debug('Request does not have any GET or POST data... cannot process request', false);
    exit;
}


wp_aff_api_debug('Validating Request Data', true);
$true_secret = get_option('wp_aff_secret_word_for_post');
$valid = true;
if (empty($secret)) {
    wp_aff_api_debug('Secret word is missing... cannot process request', false);
    $valid = false;
    exit;
} else if ($secret != $true_secret) {
    wp_aff_api_debug('Secret word do not match... cannot process request', false);
    $valid = false;
    exit;
}
if (empty($ap_id)) {
    wp_aff_api_debug('Referrer ID is missing... cannot process request', false);
    $valid = false;
    exit;
}

if ($valid) {
    global $wpdb;
    $affiliates_table_name = WP_AFF_AFFILIATES_TBL_NAME;
    $result = $wpdb->get_row("SELECT * FROM $affiliates_table_name where refid='$ap_id'", OBJECT);
    if (!$result) {
        wp_affiliate_log_debug("Affiliate ID for this request is: " . $ap_id . ". This affiliate ID does not exist in the affiliates record.", true);
        echo "Error! The requested Affiliate ID does not exist in the database.";
        exit;
    }
    else{
        $info = strip_tags($_REQUEST['info']);
        $data = wp_aff_get_affiliate_details($ap_id, $info);
        echo $data;
        exit;
    }
}
