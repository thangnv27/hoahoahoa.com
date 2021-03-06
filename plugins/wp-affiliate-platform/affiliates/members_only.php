<?php

include_once ('misc_func.php');
if (!isset($_SESSION)) {
    @session_start();
}
//include "./lang/$language";
if (!aff_check_security()) {
    aff_redirect('index.php');
    exit;
}

include "header.php";
?>

<img src="images/wp_aff_stats.jpg" alt="Stats Icon" />

<?php

$affiliates_clickthroughs_table = WP_AFF_CLICKTHROUGH_TABLE;
$sales_table = WP_AFF_SALES_TABLE;
$affiliates_table_name = WP_AFF_AFFILIATES_TABLE;

wp_aff_show_stats();

include "footer.php";

function wp_aff_show_stats() {
    global $wpdb, $affiliates_table_name;
    $wp_aff_affiliates_db = $wpdb->get_row("SELECT * FROM $affiliates_table_name WHERE refid = '" . $_SESSION['user_id'] . "'", OBJECT);
    echo '<h3>' . AFF_WELCOME . ' ' . $wp_aff_affiliates_db->firstname . '</h3>';

    $default_landing_page = get_option('wp_aff_default_affiliate_landing_url');
    if (empty($default_landing_page)) {
        $default_affiliate_home_url = home_url();
    } else {
        $default_affiliate_home_url = $default_landing_page;
    }
    $separator = '?';
    $url = $default_affiliate_home_url;
    if (strpos($url, '?') !== false) {
        $separator = '&';
    }
    $aff_url = $url . $separator . 'ap_id=' . $_SESSION['user_id'];
    $affiliate_link = '<a href="' . $aff_url . '" target="_blank">' . $aff_url . '</a>';
    echo '<strong>' . AFF_AFFILIATE_ID . ': ' . $_SESSION['user_id'] . '</strong><br /><br />';
    echo '<strong>' . AFF_YOUR_AFF_LINK . $affiliate_link . '</strong>';
    echo "<br />";

    echo apply_filters('wpap_below_your_affiliate_link', '');

    //Welcome message
    $wp_aff_platform_config = WP_Affiliate_Platform_Config::getInstance();
    $wp_aff_welcome_page_msg = $wp_aff_platform_config->getValue('wp_aff_welcome_page_msg');
    if (!empty($wp_aff_welcome_page_msg)) {
        $wp_aff_welcome_page_msg = html_entity_decode($wp_aff_welcome_page_msg, ENT_COMPAT, "UTF-8");
        $wp_aff_welcome_page_msg = apply_filters('the_content', $wp_aff_welcome_page_msg);
        echo '<div class="wp_aff_welcome_page_msg">' . $wp_aff_welcome_page_msg . '</div>';
    }

    include ("reports.php");

    if (isset($_POST['info_update'])) {
        $start_date = (string) $_POST["start_date"];
        $end_date = (string) $_POST["end_date"];
        echo '<h4>';
        echo AFF_STATS_OVERVIEW_BETWEEN . ' <font style="color:#222">' . $start_date . '</font> ' . AFF_AND . ' <font style="color:#222">' . $end_date;
        echo '</font></h4>';

        show_stats_between_dates($start_date, $end_date);
    } else {
        $curr_date = (date("Y-m-d"));
        $m = date('m');
        $y = date('Y');
        $start_date = $y . '-' . $m . '-01';
        $end_date = $curr_date;

        echo '<h4>';
        echo AFF_STATS_OVERVIEW;
        echo '</h4>';

        show_stats_between_dates($start_date, $end_date);
    }
}

function show_stats_between_dates($start_date, $end_date) {
    global $wpdb;
    global $affiliates_clickthroughs_table;
    global $sales_table;
    global $affiliates_table_name;
    $aff_payouts_table = WP_AFF_PAYOUTS_TBL_NAME;

    $query = $wpdb->get_row("SELECT count(*) as total_record FROM $affiliates_clickthroughs_table WHERE refid = '" . $_SESSION['user_id'] . "' AND date BETWEEN '$start_date' AND '$end_date'", OBJECT);
    $total_clicks = $query->total_record;
    if (empty($total_clicks)) {
        $total_clicks = "0";
    }

    $query = $wpdb->get_row("SELECT count(*) as total_record FROM $sales_table WHERE payment > 0 AND refid = '" . $_SESSION['user_id'] . "' AND date BETWEEN '$start_date' AND '$end_date'", OBJECT);
    $number_of_sales = $query->total_record;
    if (empty($number_of_sales)) {
        $number_of_sales = "0";
    }

    $row = $wpdb->get_row("select SUM(sale_amount) AS total from $sales_table where refid = '" . $_SESSION['user_id'] . "' AND date BETWEEN '$start_date' AND '$end_date'", OBJECT);
    $total_sales = round($row->total, 2);
    if (empty($total_sales)) {
        $total_sales = "0.00";
    }

    $row = $wpdb->get_row("select SUM(payment) AS total from $sales_table where refid = '" . $_SESSION['user_id'] . "' AND date BETWEEN '$start_date' AND '$end_date'", OBJECT);
    $total_commission = round($row->total, 2);
    if (empty($total_commission)) {
        $total_commission = "0.00";
    }

    $payout_resultset = $wpdb->get_row("select SUM(payout_payment) AS total from $aff_payouts_table where refid = '" . $_SESSION['user_id'] . "' AND date BETWEEN '$start_date' AND '$end_date'", OBJECT);
    $total_payout = round($payout_resultset->total, 2);
    if (empty($total_payout)) {
        $total_payout = '0.00';
    }

    $wp_aff_affiliates_db = $wpdb->get_row("SELECT * FROM $affiliates_table_name WHERE refid = '" . $_SESSION['user_id'] . "'", OBJECT);
    $commission_level = $wp_aff_affiliates_db->commissionlevel;

    $currency = get_option('wp_aff_currency');
    echo '
	<table id="reports" width="400">
	<tbody>';

    echo '<tr class="wpap_mo_total_clicks_row">';
    echo '<td><strong>' . AFF_TOTAL_CLICKS . ' : </strong></td>';
    echo '<td>' . number_format($total_clicks, 0, ',', '.') . '</td>';
    echo '</tr>';

    echo '<tr class="wpap_mo_sales_number_row">';
    echo '<td><strong>' . AFF_NUMBER_OF_SALES . ' : </strong></td>';
    echo '<td>' . number_format($number_of_sales, 0, ',', '.') . '</td>';
    echo '</tr>';

    echo '<tr class="wpap_mo_sales_amt_row">';
    echo '<td><strong>' . AFF_TOTAL_SALES_AMOUNT . ' : </strong></td>';
    echo '<td>' . number_format($total_sales, 2, ',', '.') . '</td>';
    echo '<td>' . $currency . '</td>';
    echo '</tr>';

    echo '<tr class="wpap_mo_commission_amt_row">';
    echo '<td><strong>' . AFF_TOTAL_COMMISSION . ' : </strong></td>';
    echo '<td>' . number_format($total_commission, 2, ',', '.') . '</td>';
    echo '<td>' . $currency . '</td>';
    echo '</tr>';

    echo '<tr class="wpap_mo_payout_amt_row">';
    echo '<td><strong>' . AFF_PAYOUT_AMOUNT . ' : </strong></td>';
    echo '<td>' . number_format($total_payout, 2, ',', '.') . '</td>';
    echo '<td>' . $currency . '</td>';
    echo '</tr>';

    echo '<tr class="wpap_mo_comm_level_row">';
    echo '<td><strong>' . AFF_COMMISSION_LEVEL . ' : </strong></td>';
    echo '<td>' . $commission_level . '</td>';

    if (get_option('wp_aff_use_fixed_commission')) {
        echo '<td>' . $currency . '</td>';
    } else {
        echo '<td>%</td>';
    }
    echo '</tr>';

    if (get_option('wp_aff_use_2tier')) {
        $second_tier_commission_level = $wp_aff_affiliates_db->sec_tier_commissionlevel;
        if (empty($second_tier_commission_level)) {
            $second_tier_commission_level = get_option('wp_aff_2nd_tier_commission_level');
        }
        echo '<tr class="wpap_mo_tier_comm_level_row">';
        echo '<td><strong>' . AFF_2ND_TIER_COMMISSION_LEVEL . ' : </strong></td>';
        echo '<td>' . $second_tier_commission_level . '</td>';

        if (get_option('wp_aff_use_fixed_commission')) {
            echo '<td>' . $currency . '</td>';
        } else {
            echo '<td>%</td>';
        }
        echo '</tr>';
    }

    echo '</tbody></table>';
}
