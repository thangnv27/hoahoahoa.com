<?php

$affiliates_clickthroughs_table = $wpdb->prefix . "affiliates_clickthroughs_tbl";
$sales_table = $wpdb->prefix . "affiliates_sales_tbl";

function wp_aff_show_stats() {
    echo '<div class="wrap"><h2>WP Affiliate Platform - Stats Overview</h2>';
    echo '<div id="poststuff"><div id="post-body">';

    wpap_show_date_form_fields_new();

    if (isset($_POST['info_update'])) {
        $start_date = (string) $_POST["start_date"];
        $end_date = (string) $_POST["end_date"];
        echo '<div id="message" class="updated fade"><p><strong>';
        echo 'Affiliate Stats Overview Between ' . $start_date . ' And ' . $end_date;
        echo '</strong></p></div>';

        show_stats_between_dates($start_date, $end_date);
    } else {
        $curr_date = (date("Y-m-d"));
        $m = date('m');
        $y = date('Y');
        $start_date = $y . '-' . $m . '-01';
        $end_date = $curr_date;

        echo '<div id="message" class="updated fade"><p><strong>';
        echo 'Affiliate Stats Overview for This Month';
        echo '</strong></p></div>';

        show_stats_between_dates($start_date, $end_date);
    }

    echo '</div></div>';
    echo '</div>'; //End of wrap
}

function show_stats_between_dates($start_date, $end_date) {
    global $wpdb;
    global $affiliates_clickthroughs_table;
    global $sales_table;

    $query = $wpdb->get_row("SELECT count(*) as total_record FROM $affiliates_clickthroughs_table WHERE date BETWEEN '$start_date' AND '$end_date'", OBJECT);
    $total_clicks = $query->total_record;
    if (empty($total_clicks)) {
        $total_clicks = "0";
    }

    $query = $wpdb->get_row("SELECT count(*) as total_record FROM $sales_table WHERE payment > 0 AND date BETWEEN '$start_date' AND '$end_date'", OBJECT);
    $number_of_sales = $query->total_record;
    if (empty($number_of_sales)) {
        $number_of_sales = "0";
    }

    $row = $wpdb->get_row("select SUM(sale_amount) AS total from $sales_table where date BETWEEN '$start_date' AND '$end_date'", OBJECT);
    $total_sales = $row->total;
    if (empty($total_sales)) {
        $total_sales = "0.00";
    }

    $row = $wpdb->get_row("select SUM(payment) AS total from $sales_table where date BETWEEN '$start_date' AND '$end_date'", OBJECT);
    $total_commission = $row->total;
    if (empty($total_commission)) {
        $total_commission = "0.00";
    }

    $currency = get_option('wp_aff_currency');

    $total_sales = number_format($total_sales, 2, '.', '');
    $total_commission = number_format($total_commission, 2, '.', '');

    echo '<div class="postbox">
	<h3><label for="title">Overview</label></h3>
	<div class="inside">';

    echo '
	<table width="300">
	<thead><tr>
	<th scope="col"></th>
	<th scope="col"></th>
	<th scope="col"></th>
	</tr></thead>
	<tbody>';

    echo '<tr>';
    echo '<td><strong>Total Clicks : </strong></td>';
    echo '<td>' . number_format($total_clicks, 0, ',', '.') . '</td>';
    echo '</tr>';

    echo '<tr>';
    echo '<td><strong>Number of Sales : </strong></td>';
    echo '<td>' . number_format($number_of_sales, 0, ',', '.') . '</td>';
    echo '</tr>';

    echo '<tr>';
    echo '<td><strong>Total Sales Amount : </strong></td>';
    echo '<td>' . number_format($total_sales, 2, ',', '.') . '</td>';
    echo '<td>' . $currency . '</td>';
    echo '</tr>';

    echo '<tr>';
    echo '<td><strong>Total Commission : </strong></td>';
    echo '<td>' . number_format($total_commission, 2, ',', '.') . '</td>';
    echo '<td>' . $currency . '</td>';
    echo '</tr>';

    echo '</tbody></table>';
    echo '</div></div>';

    wp_aff_stats_show_top_referrers_by_clicks($start_date, $end_date);
    wp_aff_stats_show_top_referrers_by_commission($start_date, $end_date);
}

function wp_aff_stats_show_top_referrers_by_clicks($start_date, $end_date) {
    echo '<div class="postbox">
	<h3><label for="title">Top 10 referrers by clicks</label></h3>
	<div class="inside">';

    $leaderboard = get_top_referrer_by_clicks_data(10, $start_date, $end_date);
    echo '<table class="widefat" style="max-width:400px;">
	<thead><tr>
	<th>' . AFF_USERNAME . '</th>
	<th>Clicks</th>
	</tr></thead>
	<tbody>';

    if (count($leaderboard) > 1) {
        foreach ($leaderboard as $key => $value) {
            echo '<tr><td>' . $key . '</td><td>' . $leaderboard[$key] . '</td></tr>';
        }
    } else {
        echo '<tr><td colspan="2">No Data Found.</td></tr>';
    }
    echo '</tbody></table>';

    echo '</div></div>';
}

function wp_aff_stats_show_top_referrers_by_commission($start_date, $end_date) {
    $currency_code = get_option('wp_aff_currency');

    echo '<div class="postbox">
	<h3><label for="title">Top 10 referrers by commission</label></h3>
	<div class="inside">';

    $leaderboard = get_top_referrer_by_commission_data(10, $start_date, $end_date);
    echo '<table class="widefat" style="max-width:400px;">
	<thead><tr>
	<th>' . AFF_USERNAME . '</th>
	<th>Commission</th>
	</tr></thead>
	<tbody>';

    if (count($leaderboard) > 1) {
        foreach ($leaderboard as $key => $value) {
            echo '<tr><td>' . $key . '</td><td>' . $leaderboard[$key] . ' ' . $currency_code . '</td></tr>';
        }
    } else {
        echo '<tr><td colspan="2">No Data Found.</td></tr>';
    }
    echo '</tbody></table>';

    echo '</div></div>';
}