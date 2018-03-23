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

<img src="images/sales_icon.jpg" alt="Sales Icon" />

<?php

$currency = get_option('wp_aff_currency');
$aff_sales_table = WP_AFF_SALES_TABLE;

sales_history($aff_sales_table);

echo '<strong>';
print "<br><br>" . AFF_S_TOTAL . ": ";

$row = $wpdb->get_row("select SUM(payment) AS total from $aff_sales_table where refid = '" . $_SESSION['user_id'] . "'", OBJECT);
$total = round($row->total, 2);
print ($total != '' ? $total : '0');
print " ";
print $currency;
print "<br><br>";
echo '</strong>';

include "footer.php";

function sales_history($aff_sales_table) {

    include ("reports.php");

    $currency = get_option('wp_aff_currency');
    global $wpdb, $wp_aff_platform_config;

    if (isset($_POST['info_update'])) {
        $start_date = (string) $_POST["start_date"];
        $end_date = (string) $_POST["end_date"];
        $curr_date = (date("Y-m-d"));
        echo '<h4>';
        echo AFF_S_DISPLAYING_SALES_HISTORY . ' <font style="color:#222;">' . $start_date . '</font> ' . AFF_AND . ' <font style="color:#222;">' . $end_date;
        echo '</font></h4>';

        if (WP_AFFILIATE_SHOW_EPC_DATA_TO_AFFILIATES === '1') {//show EPC data
            //TODO - make a settings option for this
            //http://support.google.com/affiliatenetwork/publisher/bin/answer.py?hl=en&answer=107746
            $affiliates_clickthroughs_table = WP_AFF_CLICKS_TBL_NAME;
            $sales_table = WP_AFF_SALES_TBL_NAME;
            $query = $wpdb->get_row("SELECT count(*) as total_record FROM $affiliates_clickthroughs_table WHERE refid = '" . $_SESSION['user_id'] . "' AND date BETWEEN '$start_date' AND '$end_date'", OBJECT);
            $total_clicks = $query->total_record;
            if (empty($total_clicks)) {
                $total_clicks = "0";
            }

            $row = $wpdb->get_row("select SUM(payment) AS total from $sales_table where refid = '" . $_SESSION['user_id'] . "' AND date BETWEEN '$start_date' AND '$end_date'", OBJECT);
            $total_commission = $row->total;
            if (empty($total_commission)) {
                $total_commission = "0.00";
            }
            $aff_epc_data1 = (float) $total_commission;
            $aff_epc_data2 = (int) $total_clicks;
            if ($aff_epc_data2 < 1) {
                $aff_epc_data2 = 1;
            }
            $aff_epc_data = ($aff_epc_data1 / $aff_epc_data2) * 100;
            $aff_epc = number_format($aff_epc_data, 2);

            echo "<strong>" . AFF_SALES_COMMISSION_EARNED . "</strong>" . $total_commission . ' ' . $currency . '<br />';
            echo '<strong>' . AFF_SALES_EARNING_PER_CLICK . '</strong>' . $aff_epc . ' ' . $currency . '<br /><br />';
        }

        $wp_aff_sales = $wpdb->get_results("select * from $aff_sales_table where refid = '" . $_SESSION['user_id'] . "' AND date BETWEEN '$start_date' AND '$end_date' ORDER BY date DESC", OBJECT);
        if ($wp_aff_sales) {
            print "<table id='reports'>";
            echo "<TR><TH>" . AFF_G_DATE . "</TH><TH>" . AFF_G_TIME . "</TH>";
            echo "<TH>" . AFF_S_EARNED . "</TH>";

            if (get_option('wp_aff_show_buyer_details_to_affiliates') ||
                    $wp_aff_platform_config->getValue('wp_aff_show_buyer_details_name_to_affiliates') == '1' ||
                    $wp_aff_platform_config->getValue('wp_aff_show_txn_id_to_affiliates') == '1') {
                echo "<th>" . AFF_BUYER_DETAILS . "</th>";
            }
            echo "</TR>";

            foreach ($wp_aff_sales as $resultset) {
                print "<TR>";
                print "<td class='reportscol col1'>";
                print $resultset->date;
                print "</TD>";
                print "<td class='reportscol col1'>";
                print $resultset->time;
                print "</TD>";
                print "<td class='reportscol'>";
                print round($resultset->payment, 2);
                print " ";
                print $currency;
                if (!empty($resultset->campaign_id)) {
                    echo " (" . AFF_CUSTOM_VALUE . ': ' . $resultset->campaign_id . ")";
                }
                print "</TD>";
                $buyer_txn_details = "";
                if ($wp_aff_platform_config->getValue('wp_aff_show_buyer_details_name_to_affiliates') == '1') {
                    $buyer_txn_details .= $resultset->buyer_name . '<br />';
                }
                if (get_option('wp_aff_show_buyer_details_to_affiliates')) {
                    $buyer_txn_details .= $resultset->buyer_email . '<br />';
                }
                if ($wp_aff_platform_config->getValue('wp_aff_show_txn_id_to_affiliates') == '1') {
                    $buyer_txn_details .= $resultset->txn_id . '<br />';
                }
                if (!empty($buyer_txn_details)) {
                    echo "<td class='reportscol'>";
                    echo $buyer_txn_details;
                    echo "</td>";
                }
                print "</TR>";
            }
            print "</TABLE>";
        } else {
            echo "<br /><br /><p>" . AFF_S_NO_SALES_IN_THIS_PERIOD . "</p>";
        }
    } else {
        $resultset = $wpdb->get_results("select * from $aff_sales_table where refid = '" . $_SESSION['user_id'] . "' ORDER BY date DESC LIMIT 20", OBJECT);

        if ($resultset) {
            echo '<strong>';
            echo "<br><br><font face=arial>" . AFF_S_SALES;
            echo '</strong>';
            echo "<br><br>" . AFF_S_SHOWING_20;
            print "<br><br>";

            print "<table id='reports'>";
            echo "<TR><TH>" . AFF_G_DATE . "</TH><TH>" . AFF_G_TIME . "</TH>";
            echo "<TH>" . AFF_S_EARNED . "</TH>";

            if (get_option('wp_aff_show_buyer_details_to_affiliates') ||
                    $wp_aff_platform_config->getValue('wp_aff_show_buyer_details_name_to_affiliates') == '1' ||
                    $wp_aff_platform_config->getValue('wp_aff_show_txn_id_to_affiliates') == '1') {
                echo "<th>" . AFF_BUYER_DETAILS . "</th>";
            }
            echo "</TR>";

            foreach ($resultset as $resultset) {
                print "<TR>";
                print "<td class='reportscol col1'>";
                print $resultset->date;
                print "</TD>";
                print "<td class='reportscol col1'>";
                print $resultset->time;
                print "</TD>";
                print "<td class='reportscol'>";
                print $resultset->payment;
                print " ";
                print $currency;
                if (!empty($resultset->campaign_id)) {
                    echo " (" . AFF_CUSTOM_VALUE . ': ' . $resultset->campaign_id . ")";
                }
                print "</TD>";
                $buyer_txn_details = "";
                if ($wp_aff_platform_config->getValue('wp_aff_show_buyer_details_name_to_affiliates') == '1') {
                    $buyer_txn_details .= $resultset->buyer_name . '<br />';
                }
                if (get_option('wp_aff_show_buyer_details_to_affiliates')) {
                    $buyer_txn_details .= $resultset->buyer_email . '<br />';
                }
                if ($wp_aff_platform_config->getValue('wp_aff_show_txn_id_to_affiliates') == '1') {
                    $buyer_txn_details .= $resultset->txn_id . '<br />';
                }
                if (!empty($buyer_txn_details)) {
                    echo "<td class='reportscol'>";
                    echo $buyer_txn_details;
                    echo "</td>";
                }
                print "</TR>";
            }
            print "</TABLE>";
        } else {
            echo "<br /><p>" . AFF_S_NO_SALES . "</p>";
        }
    }
}
