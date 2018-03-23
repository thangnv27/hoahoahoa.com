<?php

function wp_aff_payment_history_view() {
    $output = "";
    ob_start();
    echo wp_aff_view_get_navbar();
    echo '<div id="wp_aff_inside">';
    echo '<div class="wp_aff_payments_page_graphic wp_aff_portal_page_graphic"><img src="' . WP_AFF_PLATFORM_URL . '/affiliates/images/money_bag.png" alt="payment icon" /></div>';
    wp_aff_show_payment_history();
    echo '</div>';
    echo wp_aff_view_get_footer();
    $output .= ob_get_contents();
    ob_end_clean();
    return $output;
}

function wp_aff_show_payment_history() {
    $currency = get_option('wp_aff_currency');
    global $wpdb;
    $aff_payouts_table = WP_AFF_PAYOUTS_TBL_NAME;
    $aff_sales_table = WP_AFF_SALES_TBL_NAME;

    wp_aff_payments_history();

    $row = $wpdb->get_row("select SUM(payout_payment) AS total from $aff_payouts_table where refid = '" . $_SESSION['user_id'] . "'", OBJECT);
    if($row->total != ''){
        $all_time_total_payout = round($row->total, 2);
    }else{
        $all_time_total_payout = 0;
    }
    
    echo '<div class="wp_aff_all_time_total_payout" style="margin-top: 10px;">';
    echo '<strong>';
    echo AFF_P_TOTAL . ": ". $all_time_total_payout. " ". $currency;
    echo '</strong>';
    echo "</div>";

    //All time total commission earned
    $resultset = $wpdb->get_row("select SUM(payment) AS total from $aff_sales_table where refid = '".$_SESSION['user_id']."'", OBJECT);
    $all_time_total_commission = round($resultset->total, 2);

    //Outstanding commission
    $outstanding_payout = $all_time_total_commission - $all_time_total_payout;
    echo '<div class="wp_aff_outstanding_payout" style="margin-top: 10px;">';
    echo '<strong>';
    echo AFF_P_TOTAL_OUTSTANDING . ": ". $outstanding_payout. " ". $currency;
    echo '</strong>';
    echo "</div>";

}

function wp_aff_payments_history() {
    include_once("aff_view_reports.php");

    $currency = get_option('wp_aff_currency');
    global $wpdb;

    if (isset($_POST['info_update'])) {
        $start_date = (string) $_POST["start_date"];
        $end_date = (string) $_POST["end_date"];
        echo '<h4>';
        echo AFF_P_DISPLAYING_PAYOUTS_HISTORY . ' <font style="color:#222">' . $start_date . '</font> ' . AFF_AND . ' <font style="color:#222">' . $end_date;
        echo '</font></h4>';

        $curr_date = (date("Y-m-d"));

        $aff_payouts_table = WP_AFF_PAYOUTS_TBL_NAME;
        $wp_aff_payouts = $wpdb->get_results("select * from $aff_payouts_table where refid = '" . $_SESSION['user_id'] . "' AND date BETWEEN '$start_date' AND '$end_date' ORDER by date DESC", OBJECT);

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
            echo "<br>No Payments Found";
        }
    } else {
        $aff_payouts_table = WP_AFF_PAYOUTS_TBL_NAME;
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
            echo "<br>" . AFF_P_NO_PAYMENTS_RECEIVED;
        }
    }
}
