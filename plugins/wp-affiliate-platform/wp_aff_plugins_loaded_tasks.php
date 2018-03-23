<?php

function wpap_handle_pass_thru_links() {
    if (isset($_REQUEST['wpap_pass_thru']) && isset($_REQUEST['ap_id'])) {
        $referrer_id = trim(strip_tags($_REQUEST['ap_id']));
        $url = trim(strip_tags($_REQUEST['wpap_pass_thru']));

        if (strlen($referrer_id) > 0) {
            $campaign_id = strip_tags($_REQUEST['c_id']);
            record_click($referrer_id, $campaign_id);
            wp_affiliate_log_debug("Redirecting to pass thru URL: ".$url, true);
            header('Location: ' . $url);
            exit;
        }
    }
}