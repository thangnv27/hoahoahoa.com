<?php

function wp_aff_payment_history_view() {
    $output = "";
    ob_start();
    echo wp_aff_view_2_get_navbar();
    echo '<div id="wp_aff_inside">';
    wp_aff_show_payment_history();
    echo '</div>';
    echo wp_aff_view_2_get_footer();
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
    
    echo '<div class="alert alert-info wp_aff_all_time_total_payout">';
    echo '<strong>';
    echo AFF_P_TOTAL . ": ". $all_time_total_payout. " ". $currency;
    echo '</strong>';
    echo "</div>";

    //All time total commission earned
    $resultset = $wpdb->get_row("select SUM(payment) AS total from $aff_sales_table where refid = '".$_SESSION['user_id']."'", OBJECT);
    $all_time_total_commission = round($resultset->total, 2);

    //Outstanding commission
    $outstanding_payout = $all_time_total_commission - $all_time_total_payout;
    echo '<div class="alert alert-info wp_aff_outstanding_payout">';
    echo '<strong>';
    echo AFF_P_TOTAL_OUTSTANDING . ": ". $outstanding_payout. " ". $currency;
    echo '</strong>';
    echo "</div>";
}

function wp_aff_payments_history() {
    
    echo '<div class="wpap-vertical-buffer-10"></div>';
    include_once("aff_view_reports.php");

    $currency = get_option('wp_aff_currency');
    global $wpdb;

    if (isset($_POST['info_update'])) {
        $start_date = (string) $_POST["start_date"];
        $end_date = (string) $_POST["end_date"];
        echo '<div class="alert alert-info">';
        echo '<strong>'. AFF_P_DISPLAYING_PAYOUTS_HISTORY . ' ' . $start_date . ' ' . AFF_AND . ' ' . $end_date.'</div>';
        echo '</div>';

        $curr_date = (date("Y-m-d"));

        $aff_payouts_table = WP_AFF_PAYOUTS_TBL_NAME;
        $wp_aff_payouts = $wpdb->get_results("select * from $aff_payouts_table where refid = '" . $_SESSION['user_id'] . "' AND date BETWEEN '$start_date' AND '$end_date' ORDER BY date DESC", OBJECT);

        if ($wp_aff_payouts) {
            echo "<table class='table' id='reports'>";
            echo "<TR><TH>" . AFF_G_DATE . "</TH><TH>" . AFF_G_TIME . "</TH>";
            echo "<TH>" . AFF_P_PAYMENT . "</TH></TR>";

            foreach ($wp_aff_payouts as $resultset) {
                echo "<TR>";
                echo "<td class='reportscol col1'>";
                echo $resultset->date;
                echo "</TD>";
                echo "<td class='reportscol col1'>";
                echo $resultset->time;
                echo "</TD>";
                echo "<td class='reportscol'>";
                echo round($resultset->payout_payment, 2);
                echo " ";
                echo $currency;
                echo "</TD>";
                echo "</TR>";
            }
            echo "</table>";
        } else {
            echo '<div class="alert alert-warning">' . AFF_P_NO_PAYMENTS_RECEIVED.'</div>';
        }
    } else {
        $aff_payouts_table = WP_AFF_PAYOUTS_TBL_NAME;
        $resultset = $wpdb->get_results("select * from $aff_payouts_table where refid = '" . $_SESSION['user_id'] . "' ORDER BY date DESC LIMIT 20", OBJECT);

        if ($resultset) {
            echo '<div class="alert alert-info"><strong>';
            echo AFF_P_LAST_20_PAYMENTS;
            echo '</strong></div>';

            echo "<table class='table' id='reports'>";
            echo "<TR><TH>" . AFF_G_DATE . "</TH><TH>" . AFF_G_TIME . "</TH>";
            echo "<TH>" . AFF_P_PAYMENT . "</TH></TR>";

            foreach ($resultset as $resultset) {
                echo "<TR>";
                echo "<td class='reportscol col1'>";
                echo $resultset->date;
                echo "</TD>";
                echo "<td class='reportscol col1'>";
                echo $resultset->time;
                echo "</TD>";
                echo "<td class='reportscol'>";
                echo round($resultset->payout_payment, 2);
                echo " ";
                echo $currency;
                echo "</TD>";
                echo "</TR>";
            }
            echo "</TABLE>";
        } else {
            echo '<div class="alert alert-warning">' . AFF_P_NO_PAYMENTS_RECEIVED.'</div>';
        }
    }
}
