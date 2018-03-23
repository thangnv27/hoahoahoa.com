<?php

include_once ('misc_func.php');
if (!isset($_SESSION)) {
    @session_start();
}

if (!aff_check_security()) {
    aff_redirect('index.php');
    exit;
}

include "header.php";
?>

<img src="images/payments_icon.jpg" alt="Payment History Icon" />

<?php

$currency = get_option('wp_aff_currency');
$aff_payouts_table = WP_AFF_PAYOUTS_TABLE;
$aff_sales_table = WP_AFF_SALES_TBL_NAME;

payments_history();

//All time total payout
$row = $wpdb->get_row("select SUM(payout_payment) AS total from $aff_payouts_table where refid = '" . $_SESSION['user_id'] . "'", OBJECT);
if ($row->total != '') {
    $all_time_total_payout = round($row->total, 2);
} else {
    $all_time_total_payout = 0;
}

echo "<div class='wp_aff_all_time_total_payout'>";
echo '<strong>';
echo AFF_P_TOTAL . ": " . $all_time_total_payout . " " . $currency;
echo '</strong>';
echo "</div>";

//All time total commission earned
$resultset = $wpdb->get_row("select SUM(payment) AS total from $aff_sales_table where refid = '" . $_SESSION['user_id'] . "'", OBJECT);
$all_time_total_commission = round($resultset->total, 2);

//Outstanding commission
$outstanding_payout = $all_time_total_commission - $all_time_total_payout;
echo "<div class='wp_aff_outstanding_payout'>";
echo '<strong>';
echo AFF_P_TOTAL_OUTSTANDING . ": " . $outstanding_payout . " " . $currency;
echo '</strong>';
echo "</div>";

include "footer.php";

function payments_history() {

    include ("reports.php");

    $currency = get_option('wp_aff_currency');
    global $wpdb;

    if (isset($_POST['info_update'])) {
        $start_date = (string) $_POST["start_date"];
        $end_date = (string) $_POST["end_date"];
        echo '<h4>';
        echo AFF_P_DISPLAYING_PAYOUTS_HISTORY . ' <font style="color:#222">' . $start_date . '</font> ' . AFF_AND . ' <font style="color:#222">' . $end_date;
        echo '</font></h4>';

        $curr_date = (date("Y-m-d"));

        $aff_payouts_table = WP_AFF_PAYOUTS_TABLE;
        $wp_aff_payouts = $wpdb->get_results("select * from $aff_payouts_table where refid = '" . $_SESSION['user_id'] . "' AND date BETWEEN '$start_date' AND '$end_date' ORDER BY date DESC", OBJECT);

        if ($wp_aff_payouts) {
            print "<table id='reports'>";
            echo "<TR><TH>" . AFF_G_DATE . "</TH><TH>" . AFF_G_TIME . "</TH>";
            echo "<TH>" . AFF_P_PAYMENT . "</TH></TR>";

            foreach ($wp_aff_payouts as $resultset) {
                print "<TR>";
                print "<td class='reportscol col1'>";
                print $resultset->date;
                print "</TD>";
                print "<td class='reportscol col1'>";
                print $resultset->time;
                print "</TD>";
                print "<td class='reportscol'>";
                print round($resultset->payout_payment, 2);
                print " ";
                print $currency;
                print "</TD>";
                print "</TR>";
            }
            print "</TABLE>";
        } else {
            echo "<br><br><font face=arial>No Payments Found";
        }
    } else {
        $aff_payouts_table = WP_AFF_PAYOUTS_TABLE;
        $resultset = $wpdb->get_results("select * from $aff_payouts_table where refid = '" . $_SESSION['user_id'] . "' ORDER BY date DESC LIMIT 20", OBJECT);

        if ($resultset) {
            echo '<strong>';
            echo "<br><br>" . AFF_P_LAST_20_PAYMENTS;
            echo '</strong>';
            print "<br><br>";

            print "<table id='reports'>";
            echo "<TR><TH>" . AFF_G_DATE . "</TH><TH>" . AFF_G_TIME . "</TH>";
            echo "<TH>" . AFF_P_PAYMENT . "</TH></TR>";

            foreach ($resultset as $resultset) {
                print "<TR>";
                print "<td class='reportscol col1'>";
                print $resultset->date;
                print "</TD>";
                print "<td class='reportscol col1'>";
                print $resultset->time;
                print "</TD>";
                print "<td class='reportscol'>";
                print round($resultset->payout_payment, 2);
                print " ";
                print $currency;
                print "</TD>";
                print "</TR>";
            }
            print "</TABLE>";
        } else {
            echo "<br><br><font face=arial>" . AFF_P_NO_PAYMENTS_RECEIVED;
        }
    }
}

